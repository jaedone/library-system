<div class="auth-form-container sign-up-container">

    <div class="auth-brand">
        <i class="bi bi-book-half"></i>
        <span>PUP Library</span>
    </div>

    <h1>Create an account</h1>

    <p class="auth-subtitle">
        Join us to access our services!
    </p>

    @if ($errors->any() && old('form_type') === 'register')
    <div class="error-summary">
        <p>We couldn't create your account:</p>

        <ul>
            @foreach ($errors->all() as $message)
            <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="form_type" value="register">

        <div class="eyebrow">Personal information</div>

    
            <div class="field">
                <label for="first_name">
                    First name <span class="required-mark">*</span>
                </label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="{{ old('first_name') }}"
                    required>
            </div>

            <div class="field">
                <label for="middle_name">
                    Middle name
                </label>

                <input
                    type="text"
                    id="middle_name"
                    name="middle_name"
                    value="{{ old('middle_name') }}"
                    placeholder="Optional">
            </div>

            <div class="field">
                <label for="last_name">
                    Last name <span class="required-mark">*</span>
                </label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="{{ old('last_name') }}"
                    required>
            </div>

        <div class="field">
            <label for="reg_email">Email address <span class="required-mark">*</span></label>

            <input
                type="email"
                id="reg_email"
                name="email"
                value="{{ old('email') }}"
                required>

            <small id="emailFeedback" class="live-feedback"></small>

            @error('email')
            <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
    <label for="contact_number">
        Contact number <span class="required-mark">*</span>
    </label>

    <input
        type="tel"
        id="contact_number"
        name="contact_number"
        value="{{ old('contact_number') }}"
        placeholder="e.g. 09171234567 or +639171234567"
        required
    >

    <small id="contactFeedback" class="live-feedback"></small>
</div>

        <div class="field">
    <label for="photo_2x2">
        2×2 photo <span class="required-mark">*</span>
    </label>

    <input
        type="file"
        id="photo_2x2"
        name="photo_2x2"
        accept="image/*"
        required
    >
</div>

        <div class="eyebrow">Account type</div>

        <div class="field">
            <label for="role">I am registering as a <span class="required-mark">*</span></label>

            <select id="role" name="role" required>
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>
                    What's your role?
                </option>

                @foreach ($roles as $role)
                <option
                    value="{{ $role->role_name }}"
                    {{ old('role') === $role->role_name ? 'selected' : '' }}>
                    {{ $role->display_role_name }}
                </option>
                @endforeach
            </select>
        </div>

        @include('auth.partials.register.studentFields')
        @include('auth.partials.register.employeeFields')
        @include('auth.partials.register.alumniFields')
        @include('auth.partials.register.visitorFields')

        <div class="eyebrow">Security</div>
            <div class="field">
                <label for="reg_password">Password <span class="required-mark">*</span></label>

                <div class="password-wrap">
                    <input
                        type="password"
                        id="reg_password"
                        name="password"
                        required>

                    <button
                        type="button"
                        class="toggle-password"
                        data-toggle-password="reg_password"
                        aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <div class="password-checklist" id="passwordChecklist">
                    <div data-password-rule="length">At least 8 characters</div>
                    <div data-password-rule="lowercase">Has lowercase letter</div>
                    <div data-password-rule="uppercase">Has uppercase letter</div>
                    <div data-password-rule="number">Has number</div>
                    <div data-password-rule="special">Has special character</div>
                </div>

                @error('password')
                <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="reg_password_confirmation">Confirm password <span class="required-mark">*</span></label>

                <div class="password-wrap">
                    <input
                        type="password"
                        id="reg_password_confirmation"
                        name="password_confirmation"
                        required>

                    <button
                        type="button"
                        class="toggle-password"
                        data-toggle-password="reg_password_confirmation"
                        aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <small id="confirmPasswordFeedback" class="live-feedback"></small>
            </div>

        <button type="submit" class="auth-btn auth-btn-primary">
            Create Account
        </button>
    </form>

    <p class="form-foot">
        Already have an account?
        <a href="#" data-toggle="signin">Sign in</a>
    </p>
</div>