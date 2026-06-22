@extends('common.main')

@section('title', 'Change Password | PUP Library')

@section('content')

<section class="password-page">

    <a href="{{ route('account.profile') }}" class="password-back-link">
        <i class="bi bi-arrow-left"></i>
        Back to Profile
    </a>

    <div class="password-card">
        <div class="password-illustration">
            <div class="password-mailbox">
                <i class="bi bi-shield-lock"></i>
            </div>
        </div>

        <div class="password-header">
            <span class="password-eyebrow">PUP Library Account</span>
            <h1>Change Password</h1>
            <p>
                Update your account password to keep your library profile secure.
            </p>
        </div>

        @if ($errors->any())
            <div class="password-error-alert">
                <i class="bi bi-exclamation-circle"></i>

                <div>
                    <strong>Please check your password details.</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('account.password.update') }}" class="password-form">
            @csrf
            @method('PUT')

            <div class="password-field">
                <label for="old_password">Old Password</label>

                <div class="password-input-wrap">
                    <input
                        type="password"
                        id="old_password"
                        name="old_password"
                        placeholder="Enter your old password"
                        required
                    >

                    <button type="button" data-password-toggle>
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="password-field">
                <label for="new_password">New Password</label>

                <div class="password-input-wrap">
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter your new password"
                        required
                    >

                    <button type="button" data-password-toggle>
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="password-field">
                <label for="new_password_confirmation">Confirm Password</label>

                <div class="password-input-wrap">
                    <input
                        type="password"
                        id="new_password_confirmation"
                        name="new_password_confirmation"
                        placeholder="Confirm your new password"
                        required
                    >

                    <button type="button" data-password-toggle>
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="password-submit-btn">
                Change Password
            </button>
        </form>
    </div>

</section>

@endsection

@push('scripts')
    @vite(['resources/js/pages/password.js'])
@endpush