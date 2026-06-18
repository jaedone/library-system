<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Sign In / Register | PUP Library</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/style.css', 'resources/js/app.js',
    'resources/js/pages/auth.js'])
</head>

<body class="auth-body">
<div class="auth-background" id="authBackground" aria-hidden="true"></div>
    <a href="{{ route('home') }}" class="auth-return-home">
        <i class="bi bi-arrow-left"></i>
        <span>Return to Home</span>
    </a>

    <main class="auth-page">
        <div class="auth-card {{ old('form_type') === 'register' ? 'right-panel-active' : '' }}" id="authContainer">

            @include('auth.partials.loginForm')

            @include('auth.partials.registerForm')

            @include('auth.partials.authOverlay')

        </div>
    </main>

</body>
</html>