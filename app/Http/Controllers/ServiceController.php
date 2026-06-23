<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    public function index()
    {
        return view('services.index');
    }

    public function show(string $service)
    {
        $services = $this->services();

        abort_if(!array_key_exists($service, $services), 404);

        $selectedService = $services[$service];

        if (!$selectedService['is_public'] && !Auth::check()) {
            return redirect()
                ->guest(route('login'))
                ->with('status', 'Please sign in first to access this library service.');
        }
        
        return view('services.show', [
    'service' => $selectedService,
    'serviceKey' => $service,
    'availableCopies' => $this->availableCopies(),
    'reservableResources' => $this->reservableResources(),
    'activeBorrowTransactions' => $this->activeBorrowTransactions(),
    'facilities' => $this->facilities(),
    'libraryBranches' => $this->libraryBranches(),
    'materialTypes' => $this->materialTypes(),
    'borrowingHistory' => $this->borrowingHistory(),
]);
    }

    public function store(Request $request, string $service)
    {
        $services = $this->services();

        abort_if(!array_key_exists($service, $services), 404);

        $selectedService = $services[$service];

        if (!$selectedService['is_public'] && !Auth::check()) {
            return redirect()
                ->guest(route('login'))
                ->with('status', 'Please sign in first to access this library service.');
        }

        return match ($service) {
            'book-borrowing' => $this->storeBookBorrowing($request),
            'book-reservation' => $this->storeBookReservation($request),
            'book-renewal' => $this->storeBookRenewal($request),
            'book-return' => $this->storeBookReturn($request),
            'referral-letter' => $this->storeReferralLetter($request),
            'facility-reservation' => $this->storeFacilityReservation($request),
            default => abort(404),
        };
    }

    private function storeBookBorrowing(Request $request)
{
    $validated = $request->validate([
        'copy_id' => ['required', 'exists:resource_copies,id'],
        'material_group' => ['required', 'string', 'in:fiction,graduate_school,college_of_law,general'],
        'borrow_start_date' => ['required', 'date', 'after:today'],
        'borrow_end_date' => ['required', 'date', 'after_or_equal:borrow_start_date'],
        'purpose' => ['required', 'string', 'max:1000'],
    ]);

    $this->ensureNoBlockingPenalty();

    $copy = DB::table('resource_copies as rc')
        ->join('copy_statuses as cs', 'rc.copy_status_id', '=', 'cs.id')
        ->where('rc.id', $validated['copy_id'])
        ->where('rc.is_borrowable', true)
        ->where('cs.status_key', 'available')
        ->first();

    if (!$copy) {
        throw ValidationException::withMessages([
            'copy_id' => 'The selected copy is not available for borrowing.',
        ]);
    }

    $borrowerType = Auth::user()->role->role_key ?? 'student';

    $allowedDays = $this->getBorrowingDays(
        $validated['material_group'],
        $borrowerType
    );

    $borrowedAt = Carbon::parse($validated['borrow_start_date']);
    $dueAt = Carbon::parse($validated['borrow_end_date']);

    $selectedDays = $borrowedAt->diffInDays($dueAt) + 1;

    if ($selectedDays > $allowedDays) {
        throw ValidationException::withMessages([
            'borrow_end_date' => "Borrowing period cannot exceed {$allowedDays} day(s).",
        ]);
    }

    DB::table('borrow_transactions')->insert([
        'user_id' => Auth::id(),
        'copy_id' => $validated['copy_id'],
        'reservation_id' => null,
        'processed_by' => null,
        'borrowed_at' => $borrowedAt,
        'due_at' => $dueAt,
        'returned_at' => null,
        'status_id' => $this->borrowStatusId('pending'),
        'remarks' => "Purpose: {$validated['purpose']}\nAllowed borrowing days: {$allowedDays}\nRequested days: {$selectedDays}",
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return back()->with(
        'success',
        "Borrowing request submitted. Requested borrowing period: {$selectedDays} day(s)."
    );
}

    private function storeBookReservation(Request $request)
{
    $validated = $request->validate([
        'resource_id' => ['required', 'exists:resources,id'],
        'copy_id' => ['nullable', 'exists:resource_copies,id'],
        'usage_date' => ['required', 'date', 'after:today'],
        'reservation_notes' => ['nullable', 'string', 'max:1000'],
    ]);

    if (!empty($validated['copy_id'])) {
        $copy = DB::table('resource_copies as rc')
            ->join('copy_statuses as cs', 'rc.copy_status_id', '=', 'cs.id')
            ->where('rc.id', $validated['copy_id'])
            ->where('rc.resource_id', $validated['resource_id'])
            ->where('rc.is_borrowable', true)
            ->where('cs.status_key', 'available')
            ->first();

        if (!$copy) {
            throw ValidationException::withMessages([
                'copy_id' => 'The selected copy is not available for reservation.',
            ]);
        }
    }

    $reservationClaimDays = 2;

    DB::table('book_reservations')->insert([
        'user_id' => Auth::id(),
        'resource_id' => $validated['resource_id'],
        'copy_id' => $validated['copy_id'] ?? null,
        'status_id' => $this->requestStatusId('pending'),
        'reserved_at' => now(),
        'usage_date' => Carbon::parse($validated['usage_date']),
        'expires_at' => now()->addDays($reservationClaimDays),
        'approved_by' => null,
        'processed_at' => null,
        'claimed_at' => null,
        'remarks' => trim(
            "Usage date: {$validated['usage_date']}\n" .
            "Approved reservations must be claimed within {$reservationClaimDays} day(s).\n" .
            ($validated['reservation_notes'] ?? '')
        ),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return back()->with(
        'success',
        "Book reservation submitted. Once approved, the book must be claimed within {$reservationClaimDays} day(s)."
    );
}

    private function storeBookRenewal(Request $request)
    {
        $validated = $request->validate([
            'borrow_transaction_id' => ['required', 'exists:borrow_transactions,id'],
            'requested_renewal_date' => ['required', 'date', 'after:today'],
            'renewal_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->ensureNoBlockingPenalty();

        $borrowTransaction = DB::table('borrow_transactions')
            ->where('id', $validated['borrow_transaction_id'])
            ->where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->first();

        if (!$borrowTransaction) {
            throw ValidationException::withMessages([
                'borrow_transaction_id' => 'The selected borrowed book is invalid or already returned.',
            ]);
        }

        DB::table('renewal_requests')->insert([
            'borrow_transaction_id' => $borrowTransaction->id,
            'user_id' => Auth::id(),
            'status_id' => $this->requestStatusId('pending'),
            'requested_at' => now(),
            'approved_by' => null,
            'processed_at' => null,
            'new_due_at' => Carbon::parse($validated['requested_renewal_date']),
            'remarks' => $validated['renewal_reason'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Book renewal request submitted. Please wait for staff approval.');
    }

    private function storeBookReturn(Request $request)
    {
        $validated = $request->validate([
            'borrow_transaction_id' => ['required', 'exists:borrow_transactions,id'],
            'return_date' => ['required', 'date', 'after_or_equal:today'],
            'material_condition' => ['required', 'string', 'in:good,minor_damage,damaged,lost'],
            'proof_of_return' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'return_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $borrowTransaction = DB::table('borrow_transactions')
            ->where('id', $validated['borrow_transaction_id'])
            ->where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->first();

        if (!$borrowTransaction) {
            throw ValidationException::withMessages([
                'borrow_transaction_id' => 'The selected borrowed book is invalid or already returned.',
            ]);
        }

        $proofPath = $request->file('proof_of_return')
            ->store('service-proofs/book-returns', 'public');

        DB::table('book_return_requests')->insert([
            'borrow_transaction_id' => $borrowTransaction->id,
            'user_id' => Auth::id(),
            'status_id' => $this->requestStatusId('pending'),
            'returned_at' => Carbon::parse($validated['return_date']),
            'material_condition' => $validated['material_condition'],
            'proof_path' => $proofPath,
            'remarks' => $validated['return_notes'] ?? null,
            'processed_by' => null,
            'processed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Book return proof submitted. Please wait for library staff verification.');
    }

    private function storeReferralLetter(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'destination_library' => ['required', 'string', 'max:180'],
            'material_needed' => ['required', 'string', 'max:1000'],
            'request_purpose' => ['required', 'string', 'max:1000'],
            'valid_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $supportingDocumentPath = null;

        if ($request->hasFile('valid_id')) {
            $supportingDocumentPath = $request->file('valid_id')
                ->store('service-proofs/referral-letters', 'public');
        }

        DB::table('referral_requests')->insert([
            'user_id' => Auth::id(),
            'destination_library' => $validated['destination_library'],
            'purpose' => $validated['request_purpose'],
            'material_needed' => $validated['material_needed'],
            'status_id' => $this->requestStatusId('pending'),
            'requested_at' => now(),
            'processed_by' => null,
            'processed_at' => null,
            'approved_letter_path' => null,
            'remarks' => trim(
    "Requester Name: " . ($validated['full_name'] ?? 'N/A') . "\n" .
    "Email: " . $validated['email'] . "\n" .
    "Supporting Document: " . ($supportingDocumentPath ?? 'None') . "\n" .
    "Updates will be sent through email."
),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Referral letter request submitted. Updates will be sent through email.');
    }

    private function storeFacilityReservation(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'reservation_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'participants' => ['required', 'integer', 'min:1'],
            'facility_purpose' => ['required', 'string', 'max:1000'],
        ]);

        $reservationDate = Carbon::parse($validated['reservation_date']);
        $earliestAllowedDate = now()->startOfDay()->addDay();
        $latestAllowedDate = now()->startOfDay()->addDays(14);

        if ($reservationDate->lt($earliestAllowedDate)) {
            throw ValidationException::withMessages([
                'reservation_date' => 'Facility reservations must be made at least 1 day before the intended use.',
            ]);
        }

        if ($reservationDate->gt($latestAllowedDate)) {
            throw ValidationException::withMessages([
                'reservation_date' => 'Facility reservations can only be made up to 14 days in advance.',
            ]);
        }

        $start = Carbon::parse($validated['reservation_date'] . ' ' . $validated['start_time']);
        $end = Carbon::parse($validated['reservation_date'] . ' ' . $validated['end_time']);

        if ($start->diffInMinutes($end) > 120) {
            throw ValidationException::withMessages([
                'end_time' => 'Facility reservations are limited to a maximum of 2 hours per request.',
            ]);
        }

        $facility = DB::table('facilities')
            ->where('id', $validated['facility_id'])
            ->where('is_active', true)
            ->first();

        if (!$facility) {
            throw ValidationException::withMessages([
                'facility_id' => 'The selected facility is not available.',
            ]);
        }

        if ($facility->capacity && $validated['participants'] > $facility->capacity) {
            throw ValidationException::withMessages([
                'participants' => 'The number of participants exceeds the selected facility capacity.',
            ]);
        }

        $hasConflict = DB::table('facility_reservations as fr')
            ->join('request_statuses as rs', 'fr.status_id', '=', 'rs.id')
            ->where('fr.facility_id', $validated['facility_id'])
            ->whereDate('fr.reservation_date', $reservationDate)
            ->whereIn('rs.status_key', ['pending', 'approved'])
            ->where('fr.start_time', '<', $validated['end_time'])
            ->where('fr.end_time', '>', $validated['start_time'])
            ->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'start_time' => 'The selected facility is already reserved during that time.',
            ]);
        }

        DB::table('facility_reservations')->insert([
            'user_id' => Auth::id(),
            'facility_id' => $validated['facility_id'],
            'status_id' => $this->requestStatusId('pending'),
            'reservation_date' => $reservationDate,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'purpose' => $validated['facility_purpose'],
            'participants_count' => $validated['participants'],
            'processed_by' => null,
            'processed_at' => null,
            'remarks' => 'Reservations must be made 1 to 14 days in advance. Maximum duration is 2 hours.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Facility reservation submitted. Please wait for staff approval.');
    }

    private function availableCopies()
{
    $authorSubquery = DB::table('resource_authors as ra')
        ->join('authors as a', 'ra.author_id', '=', 'a.id')
        ->select(
            'ra.resource_id',
            DB::raw("STRING_AGG(a.author_name, ', ') as authors")
        )
        ->groupBy('ra.resource_id');

    $rows = DB::table('resources as r')
        ->leftJoin('resource_copies as rc', 'r.id', '=', 'rc.resource_id')
        ->leftJoin('copy_statuses as cs', 'rc.copy_status_id', '=', 'cs.id')
        ->leftJoin('material_types as mt', 'r.material_type_id', '=', 'mt.id')
        ->leftJoin('categories as c', 'r.category_id', '=', 'c.id')
        ->leftJoinSub($authorSubquery, 'au', function ($join) {
            $join->on('r.id', '=', 'au.resource_id');
        })
        ->select(
            'r.id as resource_id',
            'r.title',
            'r.isbn',
            'au.authors',
            'mt.material_type_name',
            'c.category_name',
            'rc.id as copy_id',
            'rc.accession_number',
            'rc.barcode',
            'rc.is_borrowable',
            'cs.status_key',
            'cs.status_name'
        )
        ->orderBy('r.title')
        ->orderBy('rc.accession_number')
        ->get();

    return $rows->groupBy('resource_id')->map(function ($bookRows) {
        $first = $bookRows->first();

        $copies = $bookRows
            ->filter(fn ($row) => !empty($row->copy_id))
            ->map(function ($row) {
                return [
                    'copy_id' => $row->copy_id,
                    'accession_number' => $row->accession_number,
                    'barcode' => $row->barcode,
                    'status_key' => $row->status_key,
                    'status_name' => $row->status_name,
                    'is_available' => $row->is_borrowable && $row->status_key === 'available',
                ];
            })
            ->values();

        $availableCopies = $copies->where('is_available', true)->values();

        return (object) [
            'resource_id' => $first->resource_id,
            'title' => $first->title,
            'isbn' => $first->isbn,
            'authors' => $first->authors ?? 'Unknown Author',
            'material_type_name' => $first->material_type_name,
            'category_name' => $first->category_name,
            'copies' => $copies,
            'has_available_copies' => $availableCopies->isNotEmpty(),
            'default_copy_id' => $availableCopies->first()['copy_id'] ?? null,
        ];
    })->values();
}

    private function reservableResources()
    {
        return DB::table('resources as r')
            ->leftJoin('material_types as mt', 'r.material_type_id', '=', 'mt.id')
            ->leftJoin('categories as c', 'r.category_id', '=', 'c.id')
            ->select(
                'r.id',
                'r.title',
                'r.isbn',
                'mt.material_type_name',
                'c.category_name'
            )
            ->orderBy('r.title')
            ->get();
    }

    private function activeBorrowTransactions()
    {
        if (!Auth::check()) {
            return collect();
        }

        return DB::table('borrow_transactions as bt')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->join('resources as r', 'rc.resource_id', '=', 'r.id')
            ->join('borrow_statuses as bs', 'bt.status_id', '=', 'bs.id')
            ->where('bt.user_id', Auth::id())
            ->whereNull('bt.returned_at')
            ->whereIn('bs.status_key', ['borrowed', 'active', 'overdue'])
            ->select(
                'bt.id',
                'bt.borrowed_at',
                'bt.due_at',
                'bs.status_name',
                'r.title',
                'r.isbn',
                'rc.accession_number'
            )
            ->orderByDesc('bt.borrowed_at')
            ->get();
    }

    private function facilities()
    {
        return DB::table('facilities')
            ->where('is_active', true)
            ->orderBy('facility_name')
            ->get();
    }

    private function requestStatusId(string $statusKey): int
    {
        $id = DB::table('request_statuses')
            ->where('status_key', $statusKey)
            ->value('id');

        if (!$id) {
            throw ValidationException::withMessages([
                'status' => "Missing request status: {$statusKey}. Please seed request_statuses first.",
            ]);
        }

        return $id;
    }

    private function borrowStatusId(string $statusKey): int
    {
        $id = DB::table('borrow_statuses')
            ->where('status_key', $statusKey)
            ->value('id');

        if (!$id) {
            throw ValidationException::withMessages([
                'status' => "Missing borrow status: {$statusKey}. Please seed borrow_statuses first.",
            ]);
        }

        return $id;
    }

    private function getBorrowingDays(string $materialGroup, string $borrowerType): int
    {
        if (in_array($borrowerType, ['faculty', 'employee'], true)) {
            return 7;
        }

        return match ($materialGroup) {
            'fiction' => 7,
            'graduate_school' => 2,
            'college_of_law' => 2,
            default => 7,
        };
    }

    private function ensureNoBlockingPenalty(): void
    {
        if (!Auth::check()) {
            return;
        }

        $hasUnsettledPenalty = DB::table('penalties as p')
            ->join('penalty_statuses as ps', 'p.penalty_status_id', '=', 'ps.id')
            ->where('p.user_id', Auth::id())
            ->whereNull('p.paid_at')
            ->whereNotIn('ps.status_key', ['paid', 'settled', 'waived'])
            ->exists();

        if ($hasUnsettledPenalty) {
            throw ValidationException::withMessages([
                'penalty' => 'You have unsettled penalties. Please settle them before using this service.',
            ]);
        }
    }

    private function services(): array
    {
        return [
            'book-borrowing' => [
                'is_public' => false,
                'section' => 'Book Borrowing Section',
                'title' => 'Book Borrowing',
                'icon' => 'bi-book',
                'description' => 'Borrow eligible library materials for academic, research, and personal learning purposes.',
                'notice' => null,
            ],

            'book-reservation' => [
                'is_public' => false,
                'section' => 'Book Reservation Section',
                'title' => 'Book Reservation',
                'icon' => 'bi-bookmark-check',
                'description' => 'Reserve available books before visiting the library.',
                'notice' => null,
            ],

            'book-renewal' => [
                'is_public' => false,
                'section' => 'Book Renewal Section',
                'title' => 'Book Renewal',
                'icon' => 'bi-arrow-repeat',
                'description' => 'Extend the borrowing period of eligible materials.',
                'notice' => 'Book Renewal is connected to Book Borrowing. You can only renew materials that are currently borrowed under your account.',
            ],

            'book-return' => [
                'is_public' => false,
                'section' => 'Book Return Section',
                'title' => 'Book Return',
                'icon' => 'bi-box-arrow-in-left',
                'description' => 'Manage the return of borrowed library materials.',
                'notice' => 'Book Return is connected to Book Borrowing. Returned materials are checked against your borrowing record, due dates, and penalties.',
            ],

            'referral-letter' => [
                'is_public' => true,
                'section' => 'Referral Letter Request Section',
                'title' => 'Referral Letter Request',
                'icon' => 'bi-file-earmark-text',
                'description' => 'Request a referral letter to access other libraries when the required material is unavailable at PUP Library.',
                'notice' => 'Updates for referral letter requests will be sent through email.',
            ],

            'facility-reservation' => [
                'is_public' => false,
                'section' => 'Facility Reservation Section',
                'title' => 'Facility Reservation',
                'icon' => 'bi-building-check',
                'description' => 'Reserve library facilities for academic, educational, and university-related activities.',
                'notice' => null,
            ],
        ];
    }

    private function borrowingHistory()
{
    if (!Auth::check()) {
        return collect();
    }

    return DB::table('borrow_transactions as bt')
        ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
        ->join('resources as r', 'rc.resource_id', '=', 'r.id')
        ->join('borrow_statuses as bs', 'bt.status_id', '=', 'bs.id')
        ->where('bt.user_id', Auth::id())
        ->select(
            'bt.id',
            'bt.borrowed_at',
            'bt.due_at',
            'bt.returned_at',
            'bs.status_name',
            'r.title',
            'r.isbn',
            'rc.accession_number'
        )
        ->orderByDesc('bt.borrowed_at')
        ->get();
}

public function facilityAvailability(Request $request)
{
    $facilityId = $request->query('facility_id');
    $date = $request->query('date');

    abort_if(!$facilityId, 422);

    $slots = [
        ['start' => '08:00', 'end' => '10:00', 'label' => '8:00am - 10:00am'],
        ['start' => '10:00', 'end' => '12:00', 'label' => '10:00am - 12:00pm'],
        ['start' => '13:00', 'end' => '15:00', 'label' => '1:00pm - 3:00pm'],
        ['start' => '15:00', 'end' => '17:00', 'label' => '3:00pm - 5:00pm'],
    ];

    if ($date) {
        $availableSlots = collect($slots)->reject(function ($slot) use ($facilityId, $date) {
            return DB::table('facility_reservations as fr')
                ->join('request_statuses as rs', 'fr.status_id', '=', 'rs.id')
                ->where('fr.facility_id', $facilityId)
                ->whereDate('fr.reservation_date', $date)
                ->whereIn('rs.status_key', ['pending', 'approved'])
                ->where('fr.start_time', '<', $slot['end'])
                ->where('fr.end_time', '>', $slot['start'])
                ->exists();
        })->values();

        return response()->json([
            'available_slots' => $availableSlots,
        ]);
    }

    $availableDates = collect(range(1, 14))
        ->map(fn ($day) => now()->startOfDay()->addDays($day)->format('Y-m-d'))
        ->filter(function ($date) use ($facilityId, $slots) {
            foreach ($slots as $slot) {
                $taken = DB::table('facility_reservations as fr')
                    ->join('request_statuses as rs', 'fr.status_id', '=', 'rs.id')
                    ->where('fr.facility_id', $facilityId)
                    ->whereDate('fr.reservation_date', $date)
                    ->whereIn('rs.status_key', ['pending', 'approved'])
                    ->where('fr.start_time', '<', $slot['end'])
                    ->where('fr.end_time', '>', $slot['start'])
                    ->exists();

                if (!$taken) {
                    return true;
                }
            }

            return false;
        })
        ->values();

    return response()->json([
        'available_dates' => $availableDates,
    ]);
}

private function libraryBranches()
{
    return DB::table('library_branches')
        ->orderBy('branch_name')
        ->get();
}

private function materialTypes()
{
    return DB::table('material_types')
        ->orderBy('material_type_name')
        ->get();
}
}