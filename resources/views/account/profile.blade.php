@extends('common.main')

@section('title', 'My Profile | PUP Library')

@section('content')

@php
    $photoUrl = !empty($profile->profile_photo_path)
        ? asset('storage/' . $profile->profile_photo_path)
        : null;

    $initials = collect(explode(' ', $userInfo['full_name']))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');

    $score = (int) ($creditScore->current_score ?? 100);
    $minScore = (int) ($creditScore->min_score ?? 0);
    $maxScore = (int) ($creditScore->max_score ?? 100);
    $scoreRange = max(1, $maxScore - $minScore);
    $scorePercent = max(0, min(100, (($score - $minScore) / $scoreRange) * 100));
@endphp

<section class="profile-top-grid">

    {{-- LEFT: HERO + BASIC INFO COMBINED --}}
    <article class="profile-hero-card profile-hero-card-embedded">
    <div class="profile-hero-header">
        <div>
            @php
    $scoreValue = (int) ($creditScore->current_score ?? 100);

    if ($scoreValue >= 90) {
        $creditMessage = 'Your account is in excellent standing. You can freely borrow books, reserve materials, renew eligible books, and reserve facilities.';
    } elseif ($scoreValue >= 75) {
        $creditMessage = 'Your account is in good standing. You can use most library services, but keep your returns and penalties updated.';
    } elseif ($scoreValue >= 60) {
        $creditMessage = 'Your account has limited standing. Some services may require staff approval before proceeding.';
    } else {
        $creditMessage = 'Your account needs attention. Please settle penalties or contact library staff to restore full service access.';
    }
@endphp

<span class="profile-dashboard-eyebrow">Library Account</span>
<h1>Hi, {{ $profile->first_name ?? 'User' }}!</h1>
<p>{{ $creditMessage }}</p>
        </div>

        <div class="profile-avatar-showcase">
            <form
                method="POST"
                action="{{ route('account.profile.photo.update') }}"
                enctype="multipart/form-data"
                class="profile-photo-form"
                data-profile-photo-form
            >
                @csrf
                @method('PUT')

                <label class="profile-avatar-upload">
                    @if (!empty($profile->profile_photo_path))
                        <img
                            src="{{ asset('storage/' . $profile->profile_photo_path) }}"
                            alt="Profile Photo"
                            class="profile-avatar-image"
                        >
                    @else
                        <div class="profile-avatar-circle">
                            {{ strtoupper(substr($profile->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($profile->last_name ?? '', 0, 1)) }}
                        </div>
                    @endif

                    <span class="profile-avatar-overlay">
                        <i class="bi bi-camera"></i>
                        Update Photo
                    </span>

                    <input
                        type="file"
                        name="profile_photo"
                        accept="image/png,image/jpeg,image/jpg"
                        hidden
                        data-profile-photo-input
                    >
                </label>
            </form>
        </div>
    </div>

    <div class="profile-hero-info-block">
        <div class="profile-card-header">
            <div>
                <span class="profile-dashboard-eyebrow">Basic Information</span>
                <h2>My Profile</h2>
            </div>

            <div class="profile-main-actions">
                <button type="button" class="profile-panel-action" data-profile-edit>
                    <i class="bi bi-pencil-square"></i>
                    Edit Profile
                </button>

                <a href="{{ route('account.password.edit') }}" class="profile-panel-action">
                    <i class="bi bi-key"></i>
                    Change Password
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('account.profile.update') }}" class="profile-edit-form">
            @csrf
            @method('PUT')

            <div class="profile-info-grid editable profile-info-embedded">
                <div>
                    <span>Full Name</span>

                    <input
                        type="text"
                        name="full_name"
                        value="{{ trim(($profile->first_name ?? '') . ' ' . ($profile->middle_name ?? '') . ' ' . ($profile->last_name ?? '')) }}"
                        disabled
                        data-profile-field
                    >
                </div>

                <div>
                    <span>User Type</span>

                    <input
                        type="text"
                        value="{{ $userInfo['user_type'] }}"
                        disabled
                        readonly
                    >
                </div>

                <div>
                    <span>Student / Employee Number</span>

                    <input
                        type="text"
                        value="{{ $userInfo['user_number'] }}"
                        disabled
                        readonly
                    >
                </div>

                @if ($studentProfile)
                    <div>
                        <span>College</span>

                        <select name="college_id" disabled data-profile-field>
                            @foreach ($colleges as $college)
                                <option
                                    value="{{ $college->id }}"
                                    @selected((int) $studentProfile->college_id === (int) $college->id)
                                >
                                    {{ $college->college_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <span>Program</span>

                        <select name="program_id" disabled data-profile-field>
                            @foreach ($programs as $program)
                                <option
                                    value="{{ $program->id }}"
                                    @selected((int) $studentProfile->program_id === (int) $program->id)
                                >
                                    {{ $program->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif ($employeeProfile)
                    <div>
                        <span>Department</span>

                        <select name="department_id" disabled data-profile-field>
                            @foreach ($departments as $department)
                                <option
                                    value="{{ $department->id }}"
                                    @selected((int) $employeeProfile->department_id === (int) $department->id)
                                >
                                    {{ $department->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <span>Email Address</span>

                    <input
                        type="email"
                        name="email"
                        value="{{ $profile->email ?? '' }}"
                        disabled
                        data-profile-field
                    >
                </div>

                <div>
                    <span>Contact Number</span>

                    <input
                        type="text"
                        name="contact_number"
                        value="{{ $profile->contact_number ?? '' }}"
                        disabled
                        data-profile-field
                    >
                </div>

                <div>
                    <span>Library Account Number</span>

                    <input
                        type="text"
                        value="{{ $profile->library_account_number ?? 'N/A' }}"
                        disabled
                        readonly
                    >
                </div>

                <div>
                    <span>Account Status</span>

                    <input
                        type="text"
                        value="{{ $profile->status_name ?? 'N/A' }}"
                        disabled
                        readonly
                    >
                </div>
            </div>

            <div class="profile-edit-actions" data-profile-edit-actions hidden>
                <button type="submit" class="profile-save-btn">
                    <i class="bi bi-check2-circle"></i>
                    Save Changes
                </button>

                <button type="button" class="profile-cancel-btn" data-profile-cancel>
                    <i class="bi bi-x-circle"></i>
                    Cancel
                </button>
            </div>
        </form>

        <p class="profile-main-note">
            Library Account Number and User Type are system-managed and cannot be edited by the user.
        </p>
    </div>
</article>

    {{-- RIGHT: CREDIT SCORE + CREDIT SCORE INFO --}}
<div class="profile-right-stack">
    <article class="profile-score-card profile-score-card-expanded">
        <span class="profile-dashboard-eyebrow">Library Credit Score</span>

        <h2>{{ $creditScore->level_name ?? 'No Level' }}</h2>

        <div class="profile-score-value">
            {{ $creditScore->current_score ?? 0 }}
        </div>

        <p>
            {{ $creditScore->description ?? 'Maintain good standing by returning books on time and keeping penalties cleared.' }}
        </p>

        @php
            $scorePercent = min(100, max(0, (($creditScore->current_score ?? 0) / 100) * 100));
        @endphp

        <div class="game-score-bar">
            <span style="width: {{ number_format($scorePercent, 2, '.', '') }}%;"></span>
        </div>

        <small>
            {{ $creditScore->min_score ?? 0 }}–{{ $creditScore->max_score ?? 100 }} points
        </small>

        <div class="profile-credit-carousel" data-credit-carousel>
            <div class="profile-credit-carousel-header">
                <div>
                    <span class="profile-dashboard-eyebrow">Credit Information</span>
                    <h3 data-credit-title>Status Levels</h3>
                </div>

                <button type="button" data-credit-next>
                    Next
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>

            <div class="profile-credit-slide is-active" data-credit-slide data-title="Status Levels">
                <table class="profile-score-info-table dark">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Range</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($statusLevels as $level)
                            <tr>
                                <td>{{ $level->level_name }}</td>
                                <td>{{ $level->min_score }}–{{ $level->max_score }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No status levels found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="profile-credit-slide" data-credit-slide data-title="Deductions Table">
                <table class="profile-score-info-table dark">
                    <thead>
                        <tr>
                            <th>Rule</th>
                            <th>Points</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($deductionRules as $rule)
                            <tr>
                                <td>{{ $rule->rule_name }}</td>
                                <td class="danger">{{ $rule->points }} pts</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No deduction rules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="profile-credit-slide" data-credit-slide data-title="Rewards Table">
                <table class="profile-score-info-table dark">
                    <thead>
                        <tr>
                            <th>Rule</th>
                            <th>Points</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($rewardRules as $rule)
                            <tr>
                                <td>{{ $rule->rule_name }}</td>
                                <td class="reward">+{{ $rule->points }} pts</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">No reward rules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </article>
</div>

</section>

    {{-- ACCOUNT DASHBOARD PANELS --}}
    <section class="profile-panels-grid">
    @include('common.profile-list-panel', [
        'title' => 'Active Borrowed Books',
        'icon' => 'bi-book',
        'items' => $activeBorrowedBooks,
        'empty' => 'No active borrowed books.',
        'actionLabel' => 'Renew Book',
        'actionUrl' => route('services.show', 'book-renewal'),
    ])

    @include('common.profile-list-panel', [
        'title' => 'Books Due Soon',
        'icon' => 'bi-calendar-alert',
        'items' => $booksDueSoon,
        'empty' => 'No books due soon.',
        'actionLabel' => 'Return Book',
        'actionUrl' => route('services.show', 'book-return'),
    ])

    @include('common.profile-list-panel', [
    'title' => 'Active Reservations',
    'icon' => 'bi-bookmark-check',
    'items' => $activeReservations,
    'empty' => 'No active reservations.',
    'actions' => [
        [
            'label' => 'Reserve Book',
            'url' => route('services.show', 'book-reservation'),
            'icon' => 'bi-bookmark-plus',
        ],
        [
            'label' => 'Reserve Facility',
            'url' => route('services.show', 'facility-reservation'),
            'icon' => 'bi-building-add',
        ],
    ],
])

    @include('common.profile-list-panel', [
        'title' => 'Past Borrowed Books',
        'icon' => 'bi-clock-history',
        'items' => $pastBorrowedBooks,
        'empty' => 'No past borrowed books yet.',
        'actionLabel' => 'Borrow Again',
        'actionUrl' => route('services.show', 'book-borrowing'),
    ])
</section>

    {{-- PENALTIES --}}
    <section class="profile-panels-grid">
        <article class="profile-soft-panel">
            <div class="profile-card-header">
                <div>
                    <span class="profile-dashboard-eyebrow">Penalties</span>
                    <h2>Outstanding Penalties</h2>
                </div>
            </div>

            <div class="profile-record-list">
                @forelse ($outstandingPenalties as $penalty)
                    <div class="profile-record-item danger">
                        <div>
                            <h3>{{ $penalty->penalty_type_name }}</h3>
                            <p>{{ $penalty->description ?? 'No description provided.' }}</p>
                            <small>{{ \Carbon\Carbon::parse($penalty->issued_at)->format('M d, Y') }}</small>
                        </div>

                        <strong>₱{{ number_format($penalty->amount, 2) }}</strong>
                    </div>
                @empty
                    <div class="profile-empty-state">
                        <i class="bi bi-shield-check"></i>
                        <p>No outstanding penalties.</p>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="profile-soft-panel">
            <div class="profile-card-header">
                <div>
                    <span class="profile-dashboard-eyebrow">History</span>
                    <h2>Penalty History</h2>
                </div>
            </div>

            <div class="profile-record-list">
                @forelse ($penaltyHistory as $penalty)
                    <div class="profile-record-item">
                        <div>
                            <h3>{{ $penalty->penalty_type_name }}</h3>
                            <p>{{ $penalty->status_name }}</p>
                            <small>{{ \Carbon\Carbon::parse($penalty->issued_at)->format('M d, Y') }}</small>
                        </div>

                        <strong>₱{{ number_format($penalty->amount, 2) }}</strong>
                    </div>
                @empty
                    <div class="profile-empty-state">
                        <i class="bi bi-clock-history"></i>
                        <p>No penalty history.</p>
                    </div>
                @endforelse
            </div>
        </article>
    </section>

@endsection