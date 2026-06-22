<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $userId = Auth::id();

        $profile = DB::table('users as u')
            ->leftJoin('roles as r', 'u.role_id', '=', 'r.id')
            ->leftJoin('account_statuses as ast', 'u.account_status_id', '=', 'ast.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->where('u.id', $userId)
            ->select(
                'u.id',
                'u.email',
                'r.role_name',
                'r.display_role_name',
                'ast.status_key',
                'ast.status_name',
                'up.first_name',
                'up.middle_name',
                'up.last_name',
                'up.contact_number',
                'up.profile_photo_path',
                'up.library_account_number'
            )
            ->first();

        $studentProfile = DB::table('student_profiles as sp')
            ->join('colleges as c', 'sp.college_id', '=', 'c.id')
            ->join('programs as p', 'sp.program_id', '=', 'p.id')
            ->where('sp.user_id', $userId)
            ->select(
                'sp.student_number',
                'sp.year_level',
                'c.college_name',
                'p.program_name',
                'sp.college_id',
'sp.program_id',
            )
            ->first();

        $employeeProfile = DB::table('employee_profiles as ep')
            ->join('employee_types as et', 'ep.employee_type_id', '=', 'et.id')
            ->join('departments as d', 'ep.department_id', '=', 'd.id')
            ->leftJoin('faculty_profiles as fp', 'ep.id', '=', 'fp.employee_profile_id')
            ->leftJoin('academic_titles as at', 'fp.academic_title_id', '=', 'at.id')
            ->leftJoin('staff_profiles as sp', 'ep.id', '=', 'sp.employee_profile_id')
            ->leftJoin('job_titles as jt', 'sp.job_title_id', '=', 'jt.id')
            ->leftJoin('divisions as div', 'sp.division_id', '=', 'div.id')
            ->where('ep.user_id', $userId)
            ->select(
                'ep.employee_number',
                'ep.employee_id_number',
                'et.employee_type_name',
                'd.department_name',
                'at.display_title',
                'jt.job_title_name',
                'div.division_name',
                'ep.department_id',
            )
            ->first();

        $userInfo = $this->buildUserInfo($profile, $studentProfile, $employeeProfile);

        $activeBorrowedBooks = $this->activeBorrowedBooks($userId);
        $pastBorrowedBooks = $this->pastBorrowedBooks($userId);
        $booksDueSoon = $this->booksDueSoon($userId);
        $activeReservations = $this->activeReservations($userId);
        $outstandingPenalties = $this->outstandingPenalties($userId);
        $penaltyHistory = $this->penaltyHistory($userId);

        $creditScore = $this->creditScore($userId);
        $deductionRules = $this->creditRules('deduction');
        $rewardRules = $this->creditRules('reward');
        $statusLevels = DB::table('credit_score_levels')
            ->orderBy('min_score')
            ->get();
        $colleges = DB::table('colleges')
    ->orderBy('college_name')
    ->get();

$programs = DB::table('programs')
    ->orderBy('program_name')
    ->get();

$departments = DB::table('departments')
    ->orderBy('department_name')
    ->get();
        return view('account.profile', compact(
    'profile',
    'userInfo',
    'activeBorrowedBooks',
    'pastBorrowedBooks',
    'booksDueSoon',
    'activeReservations',
    'outstandingPenalties',
    'penaltyHistory',
    'creditScore',
    'deductionRules',
    'rewardRules',
    'statusLevels',
    'studentProfile',
    'employeeProfile',
    'colleges',
    'programs',
    'departments'
));
    }

    private function buildUserInfo($profile, $studentProfile, $employeeProfile): array
    {
        $fullName = trim(collect([
            $profile->first_name ?? '',
            $profile->middle_name ?? '',
            $profile->last_name ?? '',
        ])->filter()->implode(' '));

        $userType = $profile->display_role_name ?? 'Library User';
        $userNumber = 'N/A';
        $collegeDepartment = 'N/A';

        if ($studentProfile) {
            $userType = 'Student';
            $userNumber = $studentProfile->student_number;
            $collegeDepartment = $studentProfile->college_name . ' / ' . $studentProfile->program_name;
        }

        if ($employeeProfile) {
            $userType = $employeeProfile->employee_type_name ?? 'Employee';
            $userNumber = $employeeProfile->employee_number ?? $employeeProfile->employee_id_number;
            $collegeDepartment = $employeeProfile->department_name ?? 'N/A';
        }

        return [
            'full_name' => $fullName !== '' ? $fullName : 'Library User',
            'user_type' => $userType,
            'user_number' => $userNumber,
            'college_department' => $collegeDepartment,
        ];
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
            ->limit(10)
            ->get();
    }

    private function booksDueSoon(int $userId)
    {
        return DB::table('borrow_transactions as bt')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->join('resources as r', 'rc.resource_id', '=', 'r.id')
            ->where('bt.user_id', $userId)
            ->whereNull('bt.returned_at')
            ->whereBetween('bt.due_at', [now(), now()->addDays(3)])
            ->select(
                'bt.id',
                'bt.due_at',
                'r.title',
                'r.isbn',
                'rc.accession_number'
            )
            ->orderBy('bt.due_at')
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
            'br.id',
            DB::raw("'Book Reservation' as reservation_type"),
            'r.title',
            'r.isbn',
            'br.reserved_at',
            'br.expires_at',
            DB::raw('NULL as reservation_date'),
            DB::raw('NULL as start_time'),
            DB::raw('NULL as end_time'),
            'rs.status_name'
        )
        ->get();

    $facilityReservations = DB::table('facility_reservations as fr')
        ->join('facilities as f', 'fr.facility_id', '=', 'f.id')
        ->join('request_statuses as rs', 'fr.status_id', '=', 'rs.id')
        ->where('fr.user_id', $userId)
        ->whereIn('rs.status_key', ['pending', 'approved'])
        ->select(
            'fr.id',
            DB::raw("'Facility Reservation' as reservation_type"),
            'f.facility_name as title',
            DB::raw('NULL as isbn'),
            'fr.created_at as reserved_at',
            DB::raw('NULL as expires_at'),
            'fr.reservation_date',
            'fr.start_time',
            'fr.end_time',
            'rs.status_name'
        )
        ->get();

    return $bookReservations
        ->concat($facilityReservations)
        ->sortByDesc('reserved_at')
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

    private function penaltyHistory(int $userId)
    {
        return DB::table('penalties as p')
            ->join('penalty_types as pt', 'p.penalty_type_id', '=', 'pt.id')
            ->join('penalty_statuses as ps', 'p.penalty_status_id', '=', 'ps.id')
            ->where('p.user_id', $userId)
            ->select(
                'p.id',
                'p.amount',
                'p.description',
                'p.issued_at',
                'p.paid_at',
                'pt.penalty_type_name',
                'ps.status_name'
            )
            ->orderByDesc('p.issued_at')
            ->limit(10)
            ->get();
    }

    private function creditScore(int $userId)
    {
        $score = DB::table('credit_scores as cs')
            ->join('credit_score_levels as csl', 'cs.credit_score_level_id', '=', 'csl.id')
            ->where('cs.user_id', $userId)
            ->select(
                'cs.current_score',
                'csl.level_name',
                'csl.min_score',
                'csl.max_score',
                'csl.description'
            )
            ->first();

        if (!$score) {
            return (object) [
                'current_score' => 100,
                'level_name' => 'Starter Reader',
                'min_score' => 0,
                'max_score' => 100,
                'description' => 'Default library credit standing.',
            ];
        }

        return $score;
    }

    private function creditRules(string $type)
    {
        return DB::table('credit_score_rules')
            ->where(function ($query) use ($type) {
                $query->where('rule_type', $type);

                if ($type === 'deduction') {
                    $query->orWhere('points', '<', 0);
                }

                if ($type === 'reward') {
                    $query->orWhere('points', '>', 0);
                }
            })
            ->orderBy('rule_name')
            ->get();
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
    'full_name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $userId],
    'contact_number' => ['nullable', 'string', 'max:30'],

    'college_id' => ['nullable', 'exists:colleges,id'],
    'program_id' => ['nullable', 'exists:programs,id'],
    'department_id' => ['nullable', 'exists:departments,id'],
]);

        $nameParts = preg_split('/\s+/', trim($validated['full_name']));

        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? array_pop($nameParts) : '';
        $middleName = count($nameParts) > 1
            ? trim(implode(' ', array_slice($nameParts, 1)))
            : null;

        DB::transaction(function () use ($userId, $validated, $firstName, $middleName, $lastName) {
            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'email' => $validated['email'],
                    'updated_at' => now(),
                ]);

            DB::table('user_profiles')
                ->where('user_id', $userId)
                ->update([
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'contact_number' => $validated['contact_number'] ?? null,
                    'updated_at' => now(),
                ]);
            if (!empty($validated['college_id']) && !empty($validated['program_id'])) {
    DB::table('student_profiles')
        ->where('user_id', $userId)
        ->update([
            'college_id' => $validated['college_id'],
            'program_id' => $validated['program_id'],
            'updated_at' => now(),
        ]);
}

if (!empty($validated['department_id'])) {
    DB::table('employee_profiles')
        ->where('user_id', $userId)
        ->update([
            'department_id' => $validated['department_id'],
            'updated_at' => now(),
        ]);
}
        });

        return redirect()
            ->route('account.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function editPassword()
{
    return view('account.password');
}

public function updatePassword(Request $request)
{
    $validated = $request->validate([
        'old_password' => ['required', 'string'],
        'new_password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
        ],
    ]);

    $user = Auth::user();

    if (!Hash::check($validated['old_password'], $user->password)) {
        throw ValidationException::withMessages([
            'old_password' => 'The old password is incorrect.',
        ]);
    }

    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'password' => Hash::make($validated['new_password']),
            'updated_at' => now(),
        ]);

    return redirect()
        ->route('account.profile')
        ->with('success', 'Password changed successfully.');
}

public function updatePhoto(Request $request)
{
    $validated = $request->validate([
        'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ]);

    $userId = Auth::id();

    $profile = DB::table('user_profiles')
        ->where('user_id', $userId)
        ->first();

    if ($profile && $profile->profile_photo_path) {
        Storage::disk('public')->delete($profile->profile_photo_path);
    }

    $path = $request->file('profile_photo')
        ->store('profile-photos', 'public');

    DB::table('user_profiles')
        ->where('user_id', $userId)
        ->update([
            'profile_photo_path' => $path,
            'updated_at' => now(),
        ]);

    return redirect()
        ->route('account.profile')
        ->with('success', 'Profile photo updated successfully.');
}
}
