@extends('common.main')

@section('title', 'Member Management | PUP Library')

@section('content')

<section class="admin-page admin-members-page">
    <div class="admin-page-header">
        <div>
            <span class="admin-eyebrow">Admin Dashboard</span>
            <h1>Member Management</h1>
            <p>View users, edit profiles, restrict services, or remove accounts.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="admin-success-alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="admin-error-alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="admin-error-alert">
            <strong>Please check the form.</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.members.index') }}" class="admin-filter-card">
        <div class="admin-form-grid">
            <div class="admin-field">
                <label for="search">Search User</label>
                <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Name, email, student no., employee no."
                >
            </div>

            <div class="admin-field">
                <label for="role_id">Role</label>
                <select id="role_id" name="role_id">
                    <option value="">All roles</option>

                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) $roleId === (string) $role->id)>
                            {{ $role->display_role_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label for="account_status_id">Status</label>
                <select id="account_status_id" name="account_status_id">
                    <option value="">All statuses</option>

                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) $statusId === (string) $status->id)>
                            {{ $status->status_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="admin-filter-actions">
            <button type="submit" class="admin-primary-btn">
                <i class="bi bi-search"></i>
                Apply Filters
            </button>

            <a href="{{ route('admin.members.index') }}" class="admin-secondary-btn">
                Clear
            </a>
        </div>
    </form>

    <div class="admin-table-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Account No.</th>
                    <th>Restrictions</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    @php
                        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->middle_name ?? '') . ' ' . ($user->last_name ?? ''));
                        $userRestrictions = $restrictions[$user->id] ?? collect();
                    @endphp

                    <tr>
                        <td>
                            <div class="admin-table-title">
                                <i class="bi bi-person-circle"></i>

                                <div>
                                    <strong>{{ $fullName ?: 'No profile name' }}</strong>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>

                        <td>{{ $user->display_role_name }}</td>

                        <td>
                            <span class="admin-status {{ $user->status_key === 'active' ? 'active' : 'inactive' }}">
                                {{ $user->status_name }}
                            </span>
                        </td>

                        <td>{{ $user->library_account_number ?? 'N/A' }}</td>

                        <td>
                            @if ($userRestrictions->count() > 0)
                                <span class="admin-status inactive">
                                    {{ $userRestrictions->count() }} service ban(s)
                                </span>
                            @else
                                <span class="admin-status active">None</span>
                            @endif
                        </td>

                        <td>
                            <div class="admin-table-actions">
                                <button type="button" data-member-action="view" data-member-id="{{ $user->id }}">
                                    View
                                </button>

                                <button type="button" data-member-action="edit" data-member-id="{{ $user->id }}">
                                    Edit
                                </button>

                                <button type="button" data-member-action="ban" data-member-id="{{ $user->id }}">
                                    Ban
                                </button>

                                <button type="button" data-member-action="delete" data-member-id="{{ $user->id }}">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</section>

<aside class="admin-member-panel" data-member-panel aria-hidden="true">
    <button type="button" class="admin-member-panel-close" data-member-panel-close>
        <i class="bi bi-x-lg"></i>
    </button>

    <div class="admin-member-panel-header">
        <div class="admin-member-avatar" data-panel-avatar>
            <span data-panel-initials>U</span>
            <img src="" alt="User photo" data-panel-photo hidden>
        </div>

        <div>
            <span class="admin-eyebrow" data-panel-mode-label>User Profile</span>
            <h2 data-panel-name>Select a user</h2>
            <p data-panel-email></p>
        </div>
    </div>

    {{-- VIEW MODE --}}
    <section class="admin-member-panel-section" data-panel-section="view">
        <div class="admin-member-info-grid">
            <div>
                <span>Role</span>
                <strong data-view-role>N/A</strong>
            </div>

            <div>
                <span>Status</span>
                <strong data-view-status>N/A</strong>
            </div>

            <div>
                <span>Library Account No.</span>
                <strong data-view-library-account>N/A</strong>
            </div>

            <div>
                <span>Contact Number</span>
                <strong data-view-contact>N/A</strong>
            </div>

            <div>
                <span>Student Number</span>
                <strong data-view-student-number>N/A</strong>
            </div>

            <div>
                <span>Employee Number</span>
                <strong data-view-employee-number>N/A</strong>
            </div>

            <div>
                <span>College</span>
                <strong data-view-college>N/A</strong>
            </div>

            <div>
                <span>Program</span>
                <strong data-view-program>N/A</strong>
            </div>

            <div>
                <span>Department</span>
                <strong data-view-department>N/A</strong>
            </div>

            <div>
                <span>Created At</span>
                <strong data-view-created>N/A</strong>
            </div>
        </div>
    </section>

    {{-- EDIT MODE --}}
    <section class="admin-member-panel-section" data-panel-section="edit" hidden>
        <form method="POST" action="#" data-edit-form>
            @csrf
            @method('PUT')

            <div class="admin-field">
                <label>Email</label>
                <input type="email" name="email" data-edit-email required>
            </div>

            <div class="admin-form-grid two">
                <div class="admin-field">
                    <label>Role</label>
                    <select name="role_id" data-edit-role required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">
                                {{ $role->display_role_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-field">
                    <label>Account Status</label>
                    <select name="account_status_id" data-edit-status required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">
                                {{ $status->status_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-form-grid two">
                <div class="admin-field">
                    <label>First Name</label>
                    <input type="text" name="first_name" data-edit-first-name required>
                </div>

                <div class="admin-field">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" data-edit-middle-name>
                </div>
            </div>

            <div class="admin-form-grid two">
                <div class="admin-field">
                    <label>Last Name</label>
                    <input type="text" name="last_name" data-edit-last-name required>
                </div>

                <div class="admin-field">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" data-edit-contact>
                </div>
            </div>

            <div class="admin-panel-divider"></div>

            <h3>Student Profile</h3>

            <div class="admin-field">
                <label>Student Number</label>
                <input type="text" name="student_number" data-edit-student-number>
            </div>

            <div class="admin-form-grid two">
                <div class="admin-field">
                    <label>College</label>
                    <select name="college_id" data-edit-college>
                        <option value="">No college</option>

                        @foreach ($colleges as $college)
                            <option value="{{ $college->id }}">
                                {{ $college->college_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-field">
                    <label>Program</label>
                    <select name="program_id" data-edit-program>
                        <option value="">No program</option>

                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}">
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-field">
                <label>Year Level</label>
                <input type="text" name="year_level" data-edit-year-level>
            </div>

            <div class="admin-panel-divider"></div>

            <h3>Employee Profile</h3>

            <div class="admin-form-grid two">
                <div class="admin-field">
                    <label>Employee Number</label>
                    <input type="text" name="employee_number" data-edit-employee-number>
                </div>

                <div class="admin-field">
                    <label>Employee ID Number</label>
                    <input type="text" name="employee_id_number" data-edit-employee-id-number>
                </div>
            </div>

            <div class="admin-form-grid two">
                <div class="admin-field">
                    <label>Employee Type</label>
                    <select name="employee_type_id" data-edit-employee-type>
                        <option value="">No employee type</option>

                        @foreach ($employeeTypes as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->employee_type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-field">
                    <label>Department</label>
                    <select name="department_id" data-edit-department>
                        <option value="">No department</option>

                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="admin-primary-btn">
                <i class="bi bi-save"></i>
                Save Changes
            </button>
        </form>
    </section>

    {{-- BAN MODE --}}
    <section class="admin-member-panel-section" data-panel-section="ban" hidden>
        <form method="POST" action="#" data-ban-form>
            @csrf

            <div class="admin-field">
                <label>Service to Restrict</label>
                <select name="service_key" required>
                    <option value="">Select service</option>

                    @foreach ($serviceOptions as $serviceKey => $serviceName)
                        <option value="{{ $serviceKey }}">
                            {{ $serviceName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label>Reason</label>
                <textarea name="reason" rows="4" placeholder="Why is this user restricted?"></textarea>
            </div>

            <div class="admin-field">
                <label>Restricted Until</label>
                <input type="datetime-local" name="restricted_until">
            </div>

            <button type="submit" class="admin-primary-btn">
                <i class="bi bi-slash-circle"></i>
                Restrict Service
            </button>
        </form>

        <div class="admin-panel-divider"></div>

        <h3>Active Restrictions</h3>

        <div data-ban-list></div>
    </section>

    {{-- DELETE MODE --}}
    <section class="admin-member-panel-section" data-panel-section="delete" hidden>
        <div class="admin-delete-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <h3>Delete this user?</h3>
            <p>This action will remove the user account. This should only be done for test or invalid accounts.</p>
        </div>

        <form method="POST" action="#" data-delete-form>
            @csrf
            @method('DELETE')

            <button type="submit" class="admin-danger-btn">
                <i class="bi bi-trash"></i>
                Confirm Delete User
            </button>
        </form>
    </section>
</aside>

<script type="application/json" data-member-users-json>
{!! json_encode($panelUsers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
</script>

<script type="application/json" data-member-restrictions-json>
{!! json_encode($restrictions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
</script>

@endsection