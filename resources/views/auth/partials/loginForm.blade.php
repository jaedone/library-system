<div class="auth-form-container sign-in-container">

    <div class="auth-brand">
        <i class="bi bi-book-half"></i>
        <span>PUP Library</span>
    </div>

    <h1>Welcome back</h1>

    <p class="auth-subtitle">
        Sign in using your email, library account number, and password.
    </p>

    @if (session('status'))
        <div class="status-banner">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any() && old('form_type', 'login') === 'login')
        <div class="error-summary">
            <p>We couldn't sign you in:</p>

            <ul>
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <input type="hidden" name="form_type" value="login">

        <div class="auth-field">
            <label for="login_email">
                Email address <span class="required-mark">*</span>
            </label>

            <input
                type="email"
                id="login_email"
                name="email"
                value="{{ old('email') }}"
                placeholder="e.g. student@iskolarngbayan.pup.edu.ph"
                required
                autofocus
            >
        </div>

        <div class="auth-field">
            <label for="library_account_number">
                Library Account Number <span class="required-mark">*</span>
            </label>

            <input
                type="text"
                id="library_account_number"
                name="library_account_number"
                value="{{ old('library_account_number') }}"
                placeholder="e.g. LIB-000001"
                required
            >
        </div>

        <div class="auth-field">
            <label for="login_password">
                Password <span class="required-mark">*</span>
            </label>

            <div class="password-wrap">
                <input
                    type="password"
                    id="login_password"
                    name="password"
                    required
                >

                <button
                    type="button"
                    class="toggle-password"
                    data-toggle-password="login_password"
                    aria-label="Show password"
                >
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="remember-row">
            <label>
                <input type="checkbox" name="remember">
                Remember me
            </label>

            <a href="{{ Route::has('password.request') ? route('password.request') : '#' }}">
                Forgot your password?
            </a>
        </div>

        <button type="submit" class="auth-btn auth-btn-primary">
            Sign In
        </button>
    </form>

    <p class="form-foot">
        New to PUP Library?
        <a href="#" data-toggle="signup">Create an account</a>
    </p>

</div>