<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login | {{ $profile->full_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth">
    <main class="auth__card">
        <p class="brand brand--center">{{ $profile->initials }}</p>
        <h1 class="auth__title">Login to Admin</h1>
        <p class="auth__sub">Enter your credentials to continue.</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="form">
            @csrf

            @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
                <x-alert type="error">Unable to sign in.</x-alert>
            @endif

            <x-field name="email" type="email" label="Email address" required autofocus autocomplete="username" />
            <x-field name="password" type="password" label="Password" required autocomplete="current-password" />

            <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>

            <button type="submit" class="btn btn--dark btn--block">Login</button>
        </form>

        <a href="{{ route('home') }}" class="auth__back">Back to Portfolio</a>
    </main>
</body>
</html>
