<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLoginRegister()
    {
        $colleges = DB::table('colleges')
            ->orderBy('college_name')
            ->get();

        $programs = DB::table('programs')
            ->orderBy('program_name')
            ->get();

        $departments = DB::table('departments')
            ->leftJoin('colleges', 'departments.college_id', '=', 'colleges.id')
            ->select(
                'departments.*',
                'colleges.college_name'
            )
            ->orderBy('departments.department_name')
            ->get();

        $roles = DB::table('roles')
            ->whereNotIn('role_name', ['library_staff'])
            ->orderBy('id')
            ->get();

        $employeeTypes = DB::table('employee_types')
            ->orderBy('id')
            ->get();

        $academicTitles = DB::table('academic_titles')
            ->orderBy('id')
            ->get();

        $divisions = DB::table('divisions')
            ->orderBy('division_name')
            ->get();

        $jobTitles = DB::table('job_titles')
            ->orderBy('job_title_name')
            ->get();

        return view('auth.loginRegister', compact(
            'colleges',
            'programs',
            'departments',
            'roles',
            'employeeTypes',
            'academicTitles',
            'divisions',
            'jobTitles'
        ));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'library_account_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($request->email));
        $libraryAccountNumber = strtoupper(trim($request->library_account_number));

        $user = DB::table('users')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->select('users.*')
            ->whereRaw('LOWER(users.email) = ?', [$email])
            ->whereRaw('UPPER(user_profiles.library_account_number) = ?', [$libraryAccountNumber])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'login' => 'Invalid email, library account number, or password.',
                ])
                ->withInput([
                    'email' => $request->email,
                    'library_account_number' => $request->library_account_number,
                    'form_type' => 'login',
                ]);
        }

        $accountStatus = DB::table('account_statuses')
            ->where('id', $user->account_status_id)
            ->first();

        if (!$accountStatus || $accountStatus->status_key !== 'approved') {
            return back()
                ->withErrors([
                    'login' => 'Your account is still pending approval.',
                ])
                ->withInput([
                    'email' => $request->email,
                    'library_account_number' => $request->library_account_number,
                    'form_type' => 'login',
                ]);
        }

        Auth::loginUsingId($user->id, $request->boolean('remember'));

        return redirect()->route('home');
    }

    public function register(Request $request)
    {
        $roleInput = $request->role;

        $allowedRoles = [
            'student',
            'employee',
            'alumni',
            'visitor',
        ];

        $selectedEmployeeType = null;

        if ($roleInput === 'employee' && $request->filled('employee_type_id')) {
            $selectedEmployeeType = DB::table('employee_types')
                ->where('id', $request->employee_type_id)
                ->value('employee_type_key');
        }

        $baseRules = [
            'role' => ['required', Rule::in($allowedRoles)],

            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
                function ($attribute, $value, $fail) use ($roleInput) {
                    $email = strtolower($value);

                    $restrictedRoles = [
                        'student',
                        'employee',
                        'alumni',
                    ];

                    $allowedDomains = [
                        '@iskolarngbayan.pup.edu.ph',
                        '@pup.edu.ph',
                    ];

                    if (in_array($roleInput, $restrictedRoles, true)) {
                        $isAllowed = false;

                        foreach ($allowedDomains as $domain) {
                            if (str_ends_with($email, $domain)) {
                                $isAllowed = true;
                                break;
                            }
                        }

                        if (!$isAllowed) {
                            $fail('Students, employees, and alumni must use a PUP email ending in @iskolarngbayan.pup.edu.ph or @pup.edu.ph.');
                        }
                    }
                },
            ],

            'contact_number' => [
                'required',
                'string',
                'regex:/^(09\d{9}|\+639\d{9})$/',
            ],

            'photo_2x2' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ];

        $roleRules = match ($roleInput) {
            'student' => [
                'student_number' => ['required', 'string', 'max:50', 'unique:student_profiles,student_number'],
                'college_id' => ['required', 'exists:colleges,id'],
                'program_id' => ['required', 'exists:programs,id'],
                'year_level' => ['required', 'string', 'max:50'],
                'cor_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            ],

            'employee' => [
                'employee_number' => ['required', 'string', 'max:50', 'unique:employee_profiles,employee_number'],
                'employee_id_number' => ['required', 'string', 'max:80', 'unique:employee_profiles,employee_id_number'],
                'employee_type_id' => ['required', 'exists:employee_types,id'],
                'department_id' => ['required', 'exists:departments,id'],

                'academic_title_id' => [
                    Rule::requiredIf($selectedEmployeeType === 'faculty'),
                    'nullable',
                    'exists:academic_titles,id',
                ],

                'job_title_id' => [
                    Rule::requiredIf($selectedEmployeeType === 'staff'),
                    'nullable',
                    'exists:job_titles,id',
                ],

                'division_id' => [
                    Rule::requiredIf($selectedEmployeeType === 'staff'),
                    'nullable',
                    'exists:divisions,id',
                ],

                'id_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            ],

            'alumni' => [
                'alumni_id_number' => ['required', 'string', 'max:50', 'unique:alumni_profiles,alumni_id_number'],
                'graduated_program' => ['required', 'string', 'max:180'],
                'graduation_year' => ['required', 'integer', 'min:1950', 'max:' . date('Y')],
                'alumni_id_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            ],

            'visitor' => [
                'institution' => ['required', 'string', 'max:180'],
                'research_topic' => ['required', 'string', 'max:255'],
                'intended_visit_date' => ['nullable', 'date'],
                'purpose_of_visit' => ['nullable', 'string'],
                'referral_letter_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
                'valid_id_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            ],

            default => [],
        };

        $validated = $request->validate(
            array_merge($baseRules, $roleRules),
            [
                'contact_number.required' => 'Contact number is required.',
                'contact_number.regex' => 'Please enter a valid Philippine mobile number, such as 09171234567 or +639171234567.',
                'photo_2x2.required' => 'A 2x2 photo is required.',
                'photo_2x2.image' => 'The 2x2 photo must be an image file.',
                'photo_2x2.mimes' => 'The 2x2 photo must be a JPG, JPEG, or PNG file.',
                'photo_2x2.max' => 'The 2x2 photo must not be larger than 2MB.',
            ]
        );

        DB::beginTransaction();

        try {
            $role = DB::table('roles')
                ->where('role_name', $roleInput)
                ->first();

            $approvedStatus = DB::table('account_statuses')
                ->where('status_key', 'approved')
                ->first();

            if (!$role || !$approvedStatus) {
                throw new \Exception('Required role or account status is missing from the database.');
            }

            $userId = DB::table('users')->insertGetId([
                'role_id' => $role->id,
                'account_status_id' => $approvedStatus->id,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('user_profiles')->insert([
    'user_id' => $userId,
    'first_name' => $validated['first_name'],
    'middle_name' => $validated['middle_name'] ?? null,
    'last_name' => $validated['last_name'],
    'contact_number' => $validated['contact_number'] ?? null,
    'library_account_number' => 'LIB-' . str_pad($userId, 6, '0', STR_PAD_LEFT),
    'profile_photo_path' => $this->storeFile($request, 'photo_2x2', 'user-photos'),
    'created_at' => now(),
    'updated_at' => now(),
]);

            if ($roleInput === 'student') {
                DB::table('student_profiles')->insert([
                    'user_id' => $userId,
                    'student_number' => $validated['student_number'],
                    'college_id' => $validated['college_id'],
                    'program_id' => $validated['program_id'],
                    'year_level' => $validated['year_level'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->storeUserDocument($request, $userId, 'cor_file', 'Certificate of Registration');
            }

            if ($roleInput === 'employee') {
                $employeeType = DB::table('employee_types')
                    ->where('id', $validated['employee_type_id'])
                    ->first();

                if (!$employeeType) {
                    throw new \Exception('Selected employee type does not exist.');
                }

                $employeeProfileId = DB::table('employee_profiles')->insertGetId([
                    'user_id' => $userId,
                    'employee_number' => $validated['employee_number'],
                    'employee_id_number' => $validated['employee_id_number'],
                    'employee_type_id' => $validated['employee_type_id'],
                    'department_id' => $validated['department_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($employeeType->employee_type_key === 'faculty') {
                    DB::table('faculty_profiles')->insert([
                        'employee_profile_id' => $employeeProfileId,
                        'academic_title_id' => $validated['academic_title_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if ($employeeType->employee_type_key === 'staff') {
                    DB::table('staff_profiles')->insert([
                        'employee_profile_id' => $employeeProfileId,
                        'job_title_id' => $validated['job_title_id'],
                        'division_id' => $validated['division_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $this->storeUserDocument($request, $userId, 'id_file', 'Employee ID');
            }

            if ($roleInput === 'alumni') {
                DB::table('alumni_profiles')->insert([
                    'user_id' => $userId,
                    'alumni_id_number' => $validated['alumni_id_number'],
                    'graduated_program' => $validated['graduated_program'],
                    'graduation_year' => $validated['graduation_year'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->storeUserDocument($request, $userId, 'alumni_id_file', 'Alumni ID');
            }

            if ($roleInput === 'visitor') {
                DB::table('visitor_profiles')->insert([
                    'user_id' => $userId,
                    'institution' => $validated['institution'],
                    'research_topic' => $validated['research_topic'],
                    'intended_visit_date' => $validated['intended_visit_date'] ?? null,
                    'purpose_of_visit' => $validated['purpose_of_visit'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->storeUserDocument($request, $userId, 'referral_letter_file', 'Referral Letter');
                $this->storeUserDocument($request, $userId, 'valid_id_file', 'Valid Government ID');
            }

            $this->storeUserDocument($request, $userId, 'photo_2x2', '2x2 Photo');

            $excellentLevelId = DB::table('credit_score_levels')
                ->where('level_key', 'excellent')
                ->value('id');

            if ($excellentLevelId) {
                DB::table('credit_scores')->insert([
                    'user_id' => $userId,
                    'credit_score_level_id' => $excellentLevelId,
                    'current_score' => 100,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            Auth::loginUsingId($userId);

            return redirect()
                ->route('home')
                ->with('status', 'Account created successfully. Welcome to PUP Library!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['register' => 'Registration failed: ' . $e->getMessage()])
                ->withInput(['form_type' => 'register']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function storeFile(Request $request, string $field, string $folder): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store($folder, 'public');
    }

    private function storeUserDocument(Request $request, int $userId, string $field, string $documentTypeName): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        $documentType = DB::table('document_types')
            ->where('document_type_name', $documentTypeName)
            ->first();

        if (!$documentType) {
            return;
        }

        $pendingStatusId = DB::table('request_statuses')
            ->where('status_key', 'pending')
            ->value('id');

        if (!$pendingStatusId) {
            return;
        }

        $path = $request->file($field)->store('user-documents', 'public');

        DB::table('user_documents')->insert([
            'user_id' => $userId,
            'document_type_id' => $documentType->id,
            'status_id' => $pendingStatusId,
            'file_path' => $path,
            'uploaded_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
