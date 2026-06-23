<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ServiceManagementController extends Controller
{
    private array $serviceLabels = [
        'book-borrowing'       => 'Book Borrowing',
        'book-reservation'     => 'Book Reservation',
        'facility-reservation' => 'Facility Reservation',
        'book-renewal'         => 'Book Renewal',
        'book-return'          => 'Book Return',
        'referral-letter'      => 'Referral Letter',
    ];

    public function index(Request $request)
    {
        $serviceFilter = $request->query('service');
        $statusFilter  = $request->query('status');
        $search        = strtolower(trim($request->query('search', '')));

        $requests = $this->collectRequests()
            ->when($serviceFilter, function ($collection) use ($serviceFilter) {
                return $collection->where('service_key', $serviceFilter);
            })
            ->when($statusFilter, function ($collection) use ($statusFilter) {
                return $collection->where('status_key', $statusFilter);
            })
            ->when($search !== '', function ($collection) use ($search) {
                return $collection->filter(function ($item) use ($search) {
                    return str_contains(strtolower($item->requester_name ?? ''), $search)
                        || str_contains(strtolower($item->requester_email ?? ''), $search)
                        || str_contains(strtolower($item->title ?? ''), $search)
                        || str_contains(strtolower($item->subtitle ?? ''), $search)
                        || str_contains(strtolower($item->service_label ?? ''), $search);
                });
            })
            ->sortByDesc('requested_at')
            ->values();

        $panelRequests = $requests
            ->map(function ($item) {
                return $this->buildPanelItem($item);
            })
            ->values();

        return view('admin.services.index', [
            'requests'      => $requests,
            'panelRequests' => $panelRequests,
            'serviceLabels' => $this->serviceLabels,
            'serviceFilter' => $serviceFilter,
            'statusFilter'  => $statusFilter,
            'search'        => $request->query('search', ''),
            'penaltyTypes'  => Schema::hasTable('penalty_types')
                ? DB::table('penalty_types')->orderBy('penalty_type_name')->get()
                : collect(),
        ]);
    }

    public function review(Request $request, string $serviceKey, int $requestId)
    {
        $record = $this->findServiceRequest($serviceKey, $requestId);

        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $remarks = $validated['remarks'] ?? 'Your request is now under review by library staff.';

        $this->logAction($record, 'review', $remarks);

        UserNotification::send(
            $record->user_id,
            $record->service_label . ' Under Review',
            $remarks,
            'service-request',
            null,
            [
                'service_key' => $serviceKey,
                'request_id'  => $requestId,
                'action'      => 'review',
            ]
        );

        return $this->respond($request, 'Request marked for review.', $serviceKey, $requestId);
    }

    public function approve(Request $request, string $serviceKey, int $requestId)
    {
        if ($serviceKey === 'book-return') {
            return $this->confirmReturn($request, $serviceKey, $requestId);
        }

        $record = $this->findServiceRequest($serviceKey, $requestId);

        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $remarks = $validated['remarks'] ?? 'Your request has been approved.';

        match ($serviceKey) {
            'book-borrowing'       => $this->approveBookBorrowing($record, $remarks),
            'book-reservation'     => $this->approveBookReservation($record, $remarks),
            'facility-reservation' => $this->approveFacilityReservation($record, $remarks),
            'book-renewal'         => $this->approveBookRenewal($record, $remarks),
            'referral-letter'      => $this->approveReferralLetter($record, $remarks),
            default                => abort(404),
        };

        $freshRecord = $this->findServiceRequest($serviceKey, $requestId);

        $this->logAction($freshRecord, 'approve', $remarks);

        UserNotification::send(
            $freshRecord->user_id,
            $freshRecord->service_label . ' Approved',
            $remarks,
            'service-request',
            null,
            [
                'service_key' => $serviceKey,
                'request_id'  => $requestId,
                'action'      => 'approve',
            ]
        );

        return $this->respond($request, 'Request approved successfully.', $serviceKey, $requestId);
    }

    public function reject(Request $request, string $serviceKey, int $requestId)
    {
        $record = $this->findServiceRequest($serviceKey, $requestId);

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:2000'],
        ]);

        $remarks = $validated['remarks'];

        if ($serviceKey === 'book-borrowing') {
            DB::table('borrow_transactions')
                ->where('id', $requestId)
                ->update([
                    'status_id'   => $this->borrowStatusId('rejected'),
                    'processed_by'=> Auth::id(),
                    'remarks'     => $this->appendRemarks($record->raw_remarks, $remarks),
                    'updated_at'  => now(),
                ]);
        } else {
            $this->updateRequestStatus($serviceKey, $requestId, 'rejected', $remarks);
        }

        $freshRecord = $this->findServiceRequest($serviceKey, $requestId);

        $this->logAction($freshRecord, 'reject', $remarks);

        UserNotification::send(
            $freshRecord->user_id,
            $freshRecord->service_label . ' Rejected',
            $remarks,
            'service-request',
            null,
            [
                'service_key' => $serviceKey,
                'request_id'  => $requestId,
                'action'      => 'reject',
            ]
        );

        return $this->respond($request, 'Request rejected successfully.', $serviceKey, $requestId);
    }

    public function confirmReturn(Request $request, string $serviceKey, int $requestId)
    {
        abort_if($serviceKey !== 'book-return', 404);

        $record = $this->findServiceRequest($serviceKey, $requestId);

        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $remarks = $validated['remarks'] ?? 'Your book return has been confirmed by library staff.';

        DB::transaction(function () use ($record, $requestId, $remarks) {
            DB::table('book_return_requests')
                ->where('id', $requestId)
                ->update([
                    'status_id'    => $this->requestStatusId('approved'),
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'remarks'      => $this->appendRemarks($record->raw_remarks, $remarks),
                    'updated_at'   => now(),
                ]);

            DB::table('borrow_transactions')
                ->where('id', $record->borrow_transaction_id)
                ->update([
                    'returned_at' => $record->returned_at ?? now(),
                    'status_id'   => $this->borrowStatusId('returned'),
                    'updated_at'  => now(),
                ]);

            $availableCopyStatusId = $this->copyStatusId('available');

            if ($availableCopyStatusId && $record->copy_id) {
                DB::table('resource_copies')
                    ->where('id', $record->copy_id)
                    ->update([
                        'copy_status_id' => $availableCopyStatusId,
                        'updated_at'     => now(),
                    ]);
            }
        });

        $freshRecord = $this->findServiceRequest($serviceKey, $requestId);

        $this->logAction($freshRecord, 'confirm-return', $remarks);

        UserNotification::send(
            $freshRecord->user_id,
            'Book Return Confirmed',
            $remarks,
            'service-request',
            null,
            [
                'service_key' => $serviceKey,
                'request_id'  => $requestId,
                'action'      => 'confirm-return',
            ]
        );

        return $this->respond($request, 'Book return confirmed successfully.', $serviceKey, $requestId);
    }

    public function followUp(Request $request, string $serviceKey, int $requestId)
    {
        abort_if($serviceKey !== 'book-return', 404);

        $record = $this->findServiceRequest($serviceKey, $requestId);

        $validated = $request->validate([
            'remarks' => ['required', 'string', 'max:2000'],
        ]);

        $remarks = $validated['remarks'];

        $this->logAction($record, 'follow-up', $remarks);

        UserNotification::send(
            $record->user_id,
            'Book Return Follow-up',
            $remarks,
            'service-request',
            null,
            [
                'service_key' => $serviceKey,
                'request_id'  => $requestId,
                'action'      => 'follow-up',
            ]
        );

        return $this->respond($request, 'Follow-up notification sent.', $serviceKey, $requestId);
    }

    public function addPenalty(Request $request, string $serviceKey, int $requestId)
    {
        $record = $this->findServiceRequest($serviceKey, $requestId);

        $validated = $request->validate([
            'penalty_type_id' => ['required', 'exists:penalty_types,id'],
            'amount'          => ['required', 'numeric', 'min:0'],
            'description'     => ['required', 'string', 'max:2000'],
            'credit_points'   => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $penaltyId = DB::table('penalties')->insertGetId([
            'user_id'           => $record->user_id,
            'penalty_type_id'   => $validated['penalty_type_id'],
            'penalty_status_id' => $this->penaltyStatusId('unpaid'),
            'amount'            => $validated['amount'],
            'description'       => $validated['description'],
            'issued_at'         => now(),
            'paid_at'           => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $deductedPoints = (int) ($validated['credit_points'] ?? 10);

        $this->deductCreditScore($record->user_id, $deductedPoints);

        $message = 'A penalty has been added to your account: ₱'
            . number_format((float) $validated['amount'], 2)
            . '. Reason: ' . $validated['description'];

        $this->logAction($record, 'add-penalty', $message, [
            'penalty_id'    => $penaltyId,
            'amount'        => $validated['amount'],
            'credit_points' => $deductedPoints,
        ]);

        UserNotification::send(
            $record->user_id,
            'Library Penalty Added',
            $message,
            'penalty',
            null,
            [
                'service_key' => $serviceKey,
                'request_id'  => $requestId,
                'penalty_id'  => $penaltyId,
            ]
        );

        return $this->respond($request, 'Penalty added and user notified.', $serviceKey, $requestId);
    }

    public function sendReferralLetter(Request $request, string $serviceKey, int $requestId)
    {
        abort_if($serviceKey !== 'referral-letter', 404);

        $record = $this->findServiceRequest($serviceKey, $requestId);

        $validated = $request->validate([
            'approved_letter' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:4096'],
            'remarks'         => ['required', 'string', 'max:2000'],
        ]);

        $letterPath = null;

        if ($request->hasFile('approved_letter')) {
            $letterPath = $request->file('approved_letter')
                ->store('approved-referral-letters', 'public');
        }

        DB::table('referral_requests')
            ->where('id', $requestId)
            ->update([
                'status_id'            => $this->requestStatusId('approved'),
                'processed_by'         => Auth::id(),
                'processed_at'         => now(),
                'approved_letter_path' => $letterPath,
                'remarks'              => $this->appendRemarks($record->raw_remarks, $validated['remarks']),
                'updated_at'           => now(),
            ]);

        $freshRecord = $this->findServiceRequest($serviceKey, $requestId);

        $this->logAction($freshRecord, 'send-letter', $validated['remarks'], [
            'approved_letter_path' => $letterPath,
        ]);

        UserNotification::send(
            $freshRecord->user_id,
            'Referral Letter Request Updated',
            $validated['remarks'],
            'service-request',
            null,
            [
                'service_key'          => $serviceKey,
                'request_id'           => $requestId,
                'approved_letter_path' => $letterPath,
            ]
        );

        return $this->respond($request, 'Referral letter update sent to user notification inbox.', $serviceKey, $requestId);
    }

    private function collectRequests()
    {
        return collect()
            ->concat($this->bookBorrowingRequests())
            ->concat($this->bookReservationRequests())
            ->concat($this->facilityReservationRequests())
            ->concat($this->bookRenewalRequests())
            ->concat($this->bookReturnRequests())
            ->concat($this->referralLetterRequests())
            ->values();
    }

    private function bookBorrowingRequests()
    {
        if (!Schema::hasTable('borrow_transactions')) {
            return collect();
        }

        return DB::table('borrow_transactions as bt')
            ->join('users as u', 'bt.user_id', '=', 'u.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->join('resources as r', 'rc.resource_id', '=', 'r.id')
            ->join('borrow_statuses as bs', 'bt.status_id', '=', 'bs.id')
            ->select(
                'bt.id as request_id',
                'bt.user_id',
                'u.email as requester_email',
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT(up.first_name, ' ', up.last_name)), ''), u.email) as requester_name"),
                DB::raw("'book-borrowing' as service_key"),
                DB::raw("'Book Borrowing' as service_label"),
                'r.title',
                'r.isbn',
                'rc.id as copy_id',
                'rc.accession_number',
                'bt.borrowed_at as requested_at',
                'bt.due_at',
                'bs.status_key',
                'bs.status_name',
                'bt.remarks as raw_remarks',
                DB::raw("'borrow_transactions' as request_table")
            )
            ->orderByDesc('bt.created_at')
            ->get()
            ->map(function ($item) {
                $item->subtitle = 'Accession: ' . ($item->accession_number ?? 'N/A');
                return $item;
            });
    }

    private function bookReservationRequests()
    {
        if (!Schema::hasTable('book_reservations')) {
            return collect();
        }

        return DB::table('book_reservations as br')
            ->join('users as u', 'br.user_id', '=', 'u.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->join('resources as r', 'br.resource_id', '=', 'r.id')
            ->join('request_statuses as rs', 'br.status_id', '=', 'rs.id')
            ->leftJoin('resource_copies as rc', 'br.copy_id', '=', 'rc.id')
            ->select(
                'br.id as request_id',
                'br.user_id',
                'u.email as requester_email',
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT(up.first_name, ' ', up.last_name)), ''), u.email) as requester_name"),
                DB::raw("'book-reservation' as service_key"),
                DB::raw("'Book Reservation' as service_label"),
                'r.id as resource_id',
                'r.title',
                'r.isbn',
                'br.copy_id',
                'rc.accession_number',
                'br.reserved_at as requested_at',
                'br.expires_at',
                'rs.status_key',
                'rs.status_name',
                'br.remarks as raw_remarks',
                DB::raw("'book_reservations' as request_table")
            )
            ->orderByDesc('br.created_at')
            ->get()
            ->map(function ($item) {
                $item->subtitle = 'Claim until: ' . ($item->expires_at ?? 'N/A');
                return $item;
            });
    }

    private function facilityReservationRequests()
    {
        if (!Schema::hasTable('facility_reservations')) {
            return collect();
        }

        return DB::table('facility_reservations as fr')
            ->join('users as u', 'fr.user_id', '=', 'u.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->join('facilities as f', 'fr.facility_id', '=', 'f.id')
            ->join('request_statuses as rs', 'fr.status_id', '=', 'rs.id')
            ->select(
                'fr.id as request_id',
                'fr.user_id',
                'u.email as requester_email',
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT(up.first_name, ' ', up.last_name)), ''), u.email) as requester_name"),
                DB::raw("'facility-reservation' as service_key"),
                DB::raw("'Facility Reservation' as service_label"),
                'f.id as facility_id',
                'f.facility_name as title',
                'f.capacity',
                'fr.reservation_date',
                'fr.start_time',
                'fr.end_time',
                'fr.participants_count',
                'fr.created_at as requested_at',
                'fr.purpose',
                'rs.status_key',
                'rs.status_name',
                'fr.remarks as raw_remarks',
                DB::raw("'facility_reservations' as request_table")
            )
            ->orderByDesc('fr.created_at')
            ->get()
            ->map(function ($item) {
                $item->subtitle = $item->reservation_date . ' • ' . $item->start_time . ' - ' . $item->end_time;
                return $item;
            });
    }

    private function bookRenewalRequests()
    {
        if (!Schema::hasTable('renewal_requests')) {
            return collect();
        }

        return DB::table('renewal_requests as rr')
            ->join('users as u', 'rr.user_id', '=', 'u.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->join('borrow_transactions as bt', 'rr.borrow_transaction_id', '=', 'bt.id')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->join('resources as r', 'rc.resource_id', '=', 'r.id')
            ->join('request_statuses as rs', 'rr.status_id', '=', 'rs.id')
            ->select(
                'rr.id as request_id',
                'rr.user_id',
                'u.email as requester_email',
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT(up.first_name, ' ', up.last_name)), ''), u.email) as requester_name"),
                DB::raw("'book-renewal' as service_key"),
                DB::raw("'Book Renewal' as service_label"),
                'rr.borrow_transaction_id',
                'bt.copy_id',
                'rc.resource_id',
                'r.title',
                'r.isbn',
                'rc.accession_number',
                'bt.due_at',
                'rr.new_due_at',
                'rr.requested_at',
                'rs.status_key',
                'rs.status_name',
                'rr.remarks as raw_remarks',
                DB::raw("'renewal_requests' as request_table")
            )
            ->orderByDesc('rr.created_at')
            ->get()
            ->map(function ($item) {
                $item->subtitle = 'Requested new due date: ' . ($item->new_due_at ?? 'N/A');
                return $item;
            });
    }

    private function bookReturnRequests()
    {
        if (!Schema::hasTable('book_return_requests')) {
            return collect();
        }

        return DB::table('book_return_requests as brr')
            ->join('users as u', 'brr.user_id', '=', 'u.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->join('borrow_transactions as bt', 'brr.borrow_transaction_id', '=', 'bt.id')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->join('resources as r', 'rc.resource_id', '=', 'r.id')
            ->join('request_statuses as rs', 'brr.status_id', '=', 'rs.id')
            ->select(
                'brr.id as request_id',
                'brr.user_id',
                'u.email as requester_email',
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT(up.first_name, ' ', up.last_name)), ''), u.email) as requester_name"),
                DB::raw("'book-return' as service_key"),
                DB::raw("'Book Return' as service_label"),
                'brr.borrow_transaction_id',
                'bt.copy_id',
                'r.title',
                'r.isbn',
                'rc.accession_number',
                'bt.due_at',
                'brr.returned_at',
                'brr.material_condition',
                'brr.proof_path',
                'brr.created_at as requested_at',
                'rs.status_key',
                'rs.status_name',
                'brr.remarks as raw_remarks',
                DB::raw("'book_return_requests' as request_table")
            )
            ->orderByDesc('brr.created_at')
            ->get()
            ->map(function ($item) {
                $item->subtitle = 'Condition: ' . ucfirst(str_replace('_', ' ', $item->material_condition ?? 'N/A'));
                return $item;
            });
    }

    private function referralLetterRequests()
    {
        if (!Schema::hasTable('referral_requests')) {
            return collect();
        }

        return DB::table('referral_requests as rr')
            ->join('users as u', 'rr.user_id', '=', 'u.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->join('request_statuses as rs', 'rr.status_id', '=', 'rs.id')
            ->select(
                'rr.id as request_id',
                'rr.user_id',
                'u.email as requester_email',
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT(up.first_name, ' ', up.last_name)), ''), u.email) as requester_name"),
                DB::raw("'referral-letter' as service_key"),
                DB::raw("'Referral Letter' as service_label"),
                'rr.destination_library as title',
                'rr.material_needed',
                'rr.purpose',
                'rr.approved_letter_path',
                'rr.requested_at',
                'rs.status_key',
                'rs.status_name',
                'rr.remarks as raw_remarks',
                DB::raw("'referral_requests' as request_table")
            )
            ->orderByDesc('rr.created_at')
            ->get()
            ->map(function ($item) {
                $item->subtitle = 'Material needed: ' . ($item->material_needed ?? 'N/A');
                return $item;
            });
    }

    private function buildPanelItem(object $item): array
    {
        return [
            'id'             => $item->request_id,
            'service_key'    => $item->service_key,
            'service_label'  => $item->service_label,
            'title'          => $item->title,
            'subtitle'       => $item->subtitle ?? '',
            'status_key'     => $item->status_key,
            'status_name'    => $item->status_name,
            'requester_name' => $item->requester_name,
            'requester_email'=> $item->requester_email,
            'requested_at'   => $item->requested_at,
            'raw_remarks'    => $item->raw_remarks,
            'details'        => (array) $item,
            'user_context'   => $this->userContext((int) $item->user_id),
            'availability'   => $this->availabilityContext($item),
        ];
    }

    private function userContext(int $userId): array
    {
        return [
            'credit_score'         => $this->creditScore($userId),
            'active_borrowed_books'=> $this->activeBorrowedBooks($userId),
            'past_borrowed_books'  => $this->pastBorrowedBooks($userId),
            'active_reservations'  => $this->activeReservations($userId),
            'outstanding_penalties'=> $this->outstandingPenalties($userId),
        ];
    }

    private function availabilityContext(object $item): array
    {
        if ($item->service_key === 'facility-reservation') {
            $conflicts = DB::table('facility_reservations as fr')
                ->join('request_statuses as rs', 'fr.status_id', '=', 'rs.id')
                ->where('fr.facility_id', $item->facility_id)
                ->whereDate('fr.reservation_date', $item->reservation_date)
                ->where('fr.id', '!=', $item->request_id)
                ->whereIn('rs.status_key', ['pending', 'approved'])
                ->where('fr.start_time', '<', $item->end_time)
                ->where('fr.end_time', '>', $item->start_time)
                ->count();

            return [
                'type'      => 'facility',
                'conflicts' => $conflicts,
                'message'   => $conflicts > 0
                    ? 'There are conflicting facility reservations.'
                    : 'No facility conflict found.',
            ];
        }

        if (in_array($item->service_key, ['book-reservation', 'book-renewal'], true)) {
            $copyId     = $item->copy_id ?? null;
            $resourceId = $item->resource_id ?? null;

            $activeReservations = DB::table('book_reservations as br')
                ->join('request_statuses as rs', 'br.status_id', '=', 'rs.id')
                ->when($copyId, fn ($query) => $query->where('br.copy_id', $copyId))
                ->when(!$copyId && $resourceId, fn ($query) => $query->where('br.resource_id', $resourceId))
                ->where('br.user_id', '!=', $item->user_id)
                ->whereIn('rs.status_key', ['pending', 'approved'])
                ->count();

            return [
                'type'               => 'book',
                'active_reservations'=> $activeReservations,
                'message'            => $activeReservations > 0
                    ? 'This book has active reservations from other users.'
                    : 'No active reservation conflict found.',
            ];
        }

        return [
            'type'    => 'general',
            'message' => 'No special availability warning.',
        ];
    }

    private function findServiceRequest(string $serviceKey, int $requestId): object
    {
        $record = $this->collectRequests()
            ->first(function ($item) use ($serviceKey, $requestId) {
                return $item->service_key === $serviceKey
                    && (int) $item->request_id === (int) $requestId;
            });

        abort_if(!$record, 404);

        return $record;
    }

    private function approveBookBorrowing(object $record, string $remarks): void
    {
        DB::transaction(function () use ($record, $remarks) {
            DB::table('borrow_transactions')
                ->where('id', $record->request_id)
                ->update([
                    'status_id'    => $this->borrowStatusId('borrowed'),
                    'processed_by' => Auth::id(),
                    'remarks'      => $this->appendRemarks($record->raw_remarks, $remarks . "\nReleased for clearance."),
                    'updated_at'   => now(),
                ]);

            $borrowedCopyStatusId = $this->copyStatusId('borrowed');

            if ($borrowedCopyStatusId && $record->copy_id) {
                DB::table('resource_copies')
                    ->where('id', $record->copy_id)
                    ->update([
                        'copy_status_id' => $borrowedCopyStatusId,
                        'updated_at'     => now(),
                    ]);
            }
        });
    }

    private function approveBookReservation(object $record, string $remarks): void
    {
        DB::table('book_reservations')
            ->where('id', $record->request_id)
            ->update([
                'status_id'    => $this->requestStatusId('approved'),
                'approved_by'  => Auth::id(),
                'processed_at' => now(),
                'remarks'      => $this->appendRemarks($record->raw_remarks, $remarks),
                'updated_at'   => now(),
            ]);
    }

    private function approveFacilityReservation(object $record, string $remarks): void
    {
        $availability = $this->availabilityContext($record);

        if (($availability['conflicts'] ?? 0) > 0) {
            throw ValidationException::withMessages([
                'availability' => 'Cannot approve because another pending or approved facility reservation conflicts with this schedule.',
            ]);
        }

        DB::table('facility_reservations')
            ->where('id', $record->request_id)
            ->update([
                'status_id'    => $this->requestStatusId('approved'),
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'remarks'      => $this->appendRemarks($record->raw_remarks, $remarks),
                'updated_at'   => now(),
            ]);
    }

    private function approveBookRenewal(object $record, string $remarks): void
    {
        $availability = $this->availabilityContext($record);

        if (($availability['active_reservations'] ?? 0) > 0) {
            throw ValidationException::withMessages([
                'availability' => 'Cannot approve renewal because this book has an active reservation from another user.',
            ]);
        }

        DB::transaction(function () use ($record, $remarks) {
            DB::table('renewal_requests')
                ->where('id', $record->request_id)
                ->update([
                    'status_id'    => $this->requestStatusId('approved'),
                    'approved_by'  => Auth::id(),
                    'processed_at' => now(),
                    'remarks'      => $this->appendRemarks($record->raw_remarks, $remarks),
                    'updated_at'   => now(),
                ]);

            DB::table('borrow_transactions')
                ->where('id', $record->borrow_transaction_id)
                ->update([
                    'due_at'     => $record->new_due_at,
                    'updated_at' => now(),
                ]);
        });
    }

    private function approveReferralLetter(object $record, string $remarks): void
    {
        DB::table('referral_requests')
            ->where('id', $record->request_id)
            ->update([
                'status_id'    => $this->requestStatusId('approved'),
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'remarks'      => $this->appendRemarks($record->raw_remarks, $remarks),
                'updated_at'   => now(),
            ]);
    }

    private function updateRequestStatus(string $serviceKey, int $requestId, string $statusKey, string $remarks): void
    {
        $statusId = $this->requestStatusId($statusKey);

        $table = match ($serviceKey) {
            'book-reservation'     => 'book_reservations',
            'facility-reservation' => 'facility_reservations',
            'book-renewal'         => 'renewal_requests',
            'book-return'          => 'book_return_requests',
            'referral-letter'      => 'referral_requests',
            default                => abort(404),
        };

        $processedColumn = match ($serviceKey) {
            'book-reservation', 'book-renewal' => 'approved_by',
            default                             => 'processed_by',
        };

        $existing = DB::table($table)->where('id', $requestId)->first();

        DB::table($table)
            ->where('id', $requestId)
            ->update([
                'status_id'       => $statusId,
                $processedColumn  => Auth::id(),
                'processed_at'    => now(),
                'remarks'         => $this->appendRemarks($existing->remarks ?? null, $remarks),
                'updated_at'      => now(),
            ]);
    }

    private function logAction(object $record, string $action, ?string $remarks = null, array $metadata = []): void
    {
        if (!Schema::hasTable('service_request_logs')) {
            return;
        }

        DB::table('service_request_logs')->insert([
            'service_key'   => $record->service_key,
            'request_table' => $record->request_table,
            'request_id'    => $record->request_id,
            'user_id'       => $record->user_id,
            'admin_id'      => Auth::id(),
            'action'        => $action,
            'remarks'       => $remarks,
            'metadata'      => empty($metadata) ? null : json_encode($metadata),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    private function respond(Request $request, string $message, string $serviceKey, int $requestId)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'item'    => $this->buildPanelItem($this->findServiceRequest($serviceKey, $requestId)),
            ]);
        }

        return back()->with('success', $message);
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

        return (int) $id;
    }

    private function borrowStatusId(string $statusKey): int
    {
        $id = DB::table('borrow_statuses')
            ->where('status_key', $statusKey)
            ->value('id');

        if (!$id && $statusKey === 'borrowed') {
            $id = DB::table('borrow_statuses')
                ->whereIn('status_key', ['active', 'approved'])
                ->value('id');
        }

        if (!$id) {
            throw ValidationException::withMessages([
                'status' => "Missing borrow status: {$statusKey}. Please seed borrow_statuses first.",
            ]);
        }

        return (int) $id;
    }

    private function copyStatusId(string $statusKey): ?int
    {
        return DB::table('copy_statuses')
            ->where('status_key', $statusKey)
            ->value('id');
    }

    private function penaltyStatusId(string $statusKey): int
    {
        $id = DB::table('penalty_statuses')
            ->where('status_key', $statusKey)
            ->value('id');

        if (!$id) {
            $id = DB::table('penalty_statuses')
                ->whereIn('status_key', ['pending', 'open'])
                ->value('id');
        }

        if (!$id) {
            throw ValidationException::withMessages([
                'penalty_status' => 'Missing unpaid/pending penalty status. Please seed penalty_statuses first.',
            ]);
        }

        return (int) $id;
    }

    private function appendRemarks(?string $oldRemarks, string $newRemarks): string
    {
        return trim(($oldRemarks ? $oldRemarks . "\n\n" : '') . '[Admin] ' . $newRemarks);
    }

    private function creditScore(int $userId): object
    {
        if (!Schema::hasTable('credit_scores')) {
            return (object) [
                'current_score' => 100,
                'level_name'    => 'Starter Reader',
                'description'   => 'Default library credit standing.',
            ];
        }

        $score = DB::table('credit_scores as cs')
            ->join('credit_score_levels as csl', 'cs.credit_score_level_id', '=', 'csl.id')
            ->where('cs.user_id', $userId)
            ->select(
                'cs.current_score',
                'csl.level_name',
                'csl.description'
            )
            ->first();

        return $score ?: (object) [
            'current_score' => 100,
            'level_name'    => 'Starter Reader',
            'description'   => 'Default library credit standing.',
        ];
    }

    private function deductCreditScore(int $userId, int $points): void
    {
        if (!Schema::hasTable('credit_scores')) {
            return;
        }

        $score = DB::table('credit_scores')
            ->where('user_id', $userId)
            ->first();

        if (!$score) {
            return;
        }

        $newScore = max(0, (int) $score->current_score - $points);

        $levelId = DB::table('credit_score_levels')
            ->where('min_score', '<=', $newScore)
            ->where('max_score', '>=', $newScore)
            ->value('id');

        DB::table('credit_scores')
            ->where('user_id', $userId)
            ->update([
                'current_score'        => $newScore,
                'credit_score_level_id'=> $levelId ?: $score->credit_score_level_id,
                'updated_at'           => now(),
            ]);
    }

    private function activeBorrowedBooks(int $userId)
    {
        return DB::table('borrow_transactions as bt')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->join('resources as r', 'rc.resource_id', '=', 'r.id')
            ->join('borrow_statuses as bs', 'bt.status_id', '=', 'bs.id')
            ->where('bt.user_id', $userId)
            ->whereNull('bt.returned_at')
            ->select(
                'bt.id',
                'bt.borrowed_at',
                'bt.due_at',
                'bs.status_name',
                'r.title',
                'r.isbn',
                'rc.accession_number'
            )
            ->orderBy('bt.due_at')
            ->limit(8)
            ->get();
    }

    private function pastBorrowedBooks(int $userId)
    {
        return DB::table('borrow_transactions as bt')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->join('resources as r', 'rc.resource_id', '=', 'r.id')
            ->join('borrow_statuses as bs', 'bt.status_id', '=', 'bs.id')
            ->where('bt.user_id', $userId)
            ->whereNotNull('bt.returned_at')
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
            ->orderByDesc('bt.returned_at')
            ->limit(8)
            ->get();
    }

    private function activeReservations(int $userId)
    {
        $bookReservations = DB::table('book_reservations as br')
            ->join('resources as r', 'br.resource_id', '=', 'r.id')
            ->join('request_statuses as rs', 'br.status_id', '=', 'rs.id')
            ->where('br.user_id', $userId)
            ->whereIn('rs.status_key', ['pending', 'approved'])
            ->select(
                DB::raw("'Book Reservation' as reservation_type"),
                'r.title',
                'br.reserved_at',
                'br.expires_at',
                'rs.status_name'
            )
            ->get();

        $facilityReservations = DB::table('facility_reservations as fr')
            ->join('facilities as f', 'fr.facility_id', '=', 'f.id')
            ->join('request_statuses as rs', 'fr.status_id', '=', 'rs.id')
            ->where('fr.user_id', $userId)
            ->whereIn('rs.status_key', ['pending', 'approved'])
            ->select(
                DB::raw("'Facility Reservation' as reservation_type"),
                'f.facility_name as title',
                'fr.created_at as reserved_at',
                DB::raw('NULL as expires_at'),
                'rs.status_name'
            )
            ->get();

        return $bookReservations
            ->concat($facilityReservations)
            ->values();
    }

    private function outstandingPenalties(int $userId)
    {
        return DB::table('penalties as p')
            ->join('penalty_types as pt', 'p.penalty_type_id', '=', 'pt.id')
            ->join('penalty_statuses as ps', 'p.penalty_status_id', '=', 'ps.id')
            ->where('p.user_id', $userId)
            ->whereNull('p.paid_at')
            ->whereNotIn('ps.status_key', ['paid', 'settled', 'waived'])
            ->select(
                'p.id',
                'p.amount',
                'p.description',
                'p.issued_at',
                'pt.penalty_type_name',
                'ps.status_name'
            )
            ->orderByDesc('p.issued_at')
            ->get();
    }
}