<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    private array $serviceOptions = [
        'book-borrowing'       => 'Book Borrowing',
        'book-reservation'     => 'Book Reservation',
        'book-renewal'         => 'Book Renewal',
        'book-return'          => 'Book Return',
        'referral-letter'      => 'Referral Letter Request',
        'facility-reservation' => 'Facility Reservation',
    ];

    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $roleId = $request->query('role_id');
        $statusId = $request->query('account_status_id');

        $users = DB::table('users as u')
            ->join('roles as r', 'u.role_id', '=', 'r.id')
            ->join('account_statuses as acs', 'u.account_status_id', '=', 'acs.id')
            ->leftJoin('user_profiles as up', 'u.id', '=', 'up.user_id')
            ->leftJoin('student_profiles as sp', 'u.id', '=', 'sp.user_id')
            ->leftJoin('employee_profiles as ep', 'u.id', '=', 'ep.user_id')
            ->leftJoin('colleges as col', 'sp.college_id', '=', 'col.id')
            ->leftJoin('programs as prog', 'sp.program_id', '=', 'prog.id')
            ->leftJoin('departments as dep', 'ep.department_id', '=', 'dep.id')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . strtolower($search) . '%';

                $query->where(function ($q) use ($like) {
                    $q->whereRaw('LOWER(u.email) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(up.first_name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(up.last_name) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(up.library_account_number) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(sp.student_number) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(ep.employee_number) LIKE ?', [$like]);
                });
            })
            ->when($roleId, fn($query) => $query->where('u.role_id', $roleId))
            ->when($statusId, fn($query) => $query->where('u.account_status_id', $statusId))
            ->select(
                'u.id',
                'u.email',
                'u.created_at',
                'u.role_id',
                'u.account_status_id',

                'r.display_role_name',
                'r.role_name',

                'acs.status_name',
                'acs.status_key',

                'up.first_name',
                'up.middle_name',
                'up.last_name',
                'up.contact_number',
                'up.profile_photo_path',
                'up.library_account_number',

                'sp.student_number',
                'sp.college_id',
                'sp.program_id',
                'sp.year_level',

                'col.college_name',
                'prog.program_name',

                'ep.employee_number',
                'ep.employee_id_number',
                'ep.employee_type_id',
                'ep.department_id',

                'dep.department_name'
            )
            ->orderByDesc('u.created_at')
            ->paginate(10)
            ->withQueryString();

        $panelUsers = $users->getCollection()
            ->map(function ($user) {
                return [
                    'id' => $user->id,

                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'account_status_id' => $user->account_status_id,

                    'first_name' => $user->first_name ?? '',
                    'middle_name' => $user->middle_name ?? '',
                    'last_name' => $user->last_name ?? '',

                    'full_name' => trim(
                        ($user->first_name ?? '') . ' ' .
                            ($user->middle_name ?? '') . ' ' .
                            ($user->last_name ?? '')
                    ) ?: 'No profile name',

                    'role' => $user->display_role_name,
                    'status' => $user->status_name,

                    'library_account_number' => $user->library_account_number ?? 'N/A',
                    'contact_number' => $user->contact_number ?? '',

                    'student_number' => $user->student_number ?? '',
                    'college_id' => $user->college_id ?? '',
                    'program_id' => $user->program_id ?? '',
                    'year_level' => $user->year_level ?? '',
                    'college' => $user->college_name ?? null,
                    'program' => $user->program_name ?? null,

                    'employee_number' => $user->employee_number ?? '',
                    'employee_id_number' => $user->employee_id_number ?? '',
                    'employee_type_id' => $user->employee_type_id ?? '',
                    'department_id' => $user->department_id ?? '',
                    'department' => $user->department_name ?? null,

                    'created_at' => $user->created_at
                        ? date('M d, Y', strtotime($user->created_at))
                        : 'N/A',

                    'profile_photo_url' => !empty($user->profile_photo_path)
                        ? asset('storage/' . $user->profile_photo_path)
                        : null,
                ];
            })
            ->values();

        $roles = DB::table('roles')
            ->orderBy('display_role_name')
            ->get();

        $statuses = DB::table('account_statuses')
            ->orderBy('status_name')
            ->get();

        $restrictions = DB::table('user_service_restrictions')
            ->whereIn('user_id', $users->getCollection()->pluck('id'))
            ->where('is_active', true)
            ->orderBy('service_key')
            ->get()
            ->groupBy('user_id');

        return view('admin.members.index', [
            'users' => $users,
            'panelUsers' => $panelUsers,
            'roles' => $roles,
            'statuses' => $statuses,
            'restrictions' => $restrictions,
            'serviceOptions' => $this->serviceOptions,
            'search' => $search,
            'roleId' => $roleId,
            'statusId' => $statusId,

            'colleges' => DB::table('colleges')->orderBy('college_name')->get(),
            'programs' => DB::table('programs')->orderBy('program_name')->get(),
            'employeeTypes' => DB::table('employee_types')->orderBy('employee_type_name')->get(),
            'departments' => DB::table('departments')->orderBy('department_name')->get(),
        ]);
    }
    public function update(Request $request, int $user)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'role_id'            => ['required', 'exists:roles,id'],
            'account_status_id'  => ['required', 'exists:account_statuses,id'],
            'first_name'         => ['required', 'string', 'max:100'],
            'middle_name'        => ['nullable', 'string', 'max:100'],
            'last_name'          => ['required', 'string', 'max:100'],
            'contact_number'     => ['nullable', 'string', 'max:30'],
            'student_number'     => ['nullable', 'string', 'max:50'],
            'college_id'         => ['nullable', 'exists:colleges,id'],
            'program_id'         => ['nullable', 'exists:programs,id'],
            'year_level'         => ['nullable', 'string', 'max:50'],
            'employee_number'    => ['nullable', 'string', 'max:50'],
            'employee_id_number' => ['nullable', 'string', 'max:80'],
            'employee_type_id'   => ['nullable', 'exists:employee_types,id'],
            'department_id'      => ['nullable', 'exists:departments,id'],
        ]);

        DB::transaction(function () use ($validated, $user) {
            DB::table('users')
                ->where('id', $user)
                ->update([
                    'email'             => $validated['email'],
                    'role_id'           => $validated['role_id'],
                    'account_status_id' => $validated['account_status_id'],
                    'updated_at'        => now(),
                ]);

            DB::table('user_profiles')
                ->where('user_id', $user)
                ->update([
                    'first_name'     => $validated['first_name'],
                    'middle_name'    => $validated['middle_name'] ?? null,
                    'last_name'      => $validated['last_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'updated_at'     => now(),
                ]);

            if (DB::table('student_profiles')->where('user_id', $user)->exists()) {
                DB::table('student_profiles')
                    ->where('user_id', $user)
                    ->update([
                        'student_number' => $validated['student_number'],
                        'college_id'     => $validated['college_id'],
                        'program_id'     => $validated['program_id'],
                        'year_level'     => $validated['year_level'],
                        'updated_at'     => now(),
                    ]);
            }

            if (DB::table('employee_profiles')->where('user_id', $user)->exists()) {
                DB::table('employee_profiles')
                    ->where('user_id', $user)
                    ->update([
                        'employee_number'    => $validated['employee_number'],
                        'employee_id_number' => $validated['employee_id_number'],
                        'employee_type_id'   => $validated['employee_type_id'],
                        'department_id'      => $validated['department_id'],
                        'updated_at'         => now(),
                    ]);
            }
        });

        return redirect()
    ->route('admin.members.index')
    ->with('success', 'User profile updated successfully.');
    }

    public function destroy(int $user)
    {
        DB::table('users')
            ->where('id', $user)
            ->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'User deleted successfully.');
    }

    public function banService(Request $request, int $user)
    {
        $validated = $request->validate([
            'service_key'       => ['required', 'string', Rule::in(array_keys($this->serviceOptions))],
            'reason'            => ['nullable', 'string', 'max:1000'],
            'restricted_until'  => ['nullable', 'date', 'after:now'],
        ]);

        DB::table('user_service_restrictions')->updateOrInsert(
            [
                'user_id'     => $user,
                'service_key' => $validated['service_key'],
            ],
            [
                'reason'           => $validated['reason'] ?? null,
                'restricted_until' => $validated['restricted_until'] ?? null,
                'restricted_by'    => Auth::id(),
                'is_active'        => true,
                'updated_at'       => now(),
                'created_at'       => now(),
            ]
        );

        return back()->with('success', 'User service restriction saved successfully.');
    }

    public function unbanService(int $user, int $restriction)
    {
        DB::table('user_service_restrictions')
            ->where('id', $restriction)
            ->where('user_id', $user)
            ->update([
                'is_active'  => false,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Service restriction removed successfully.');
    }
}
