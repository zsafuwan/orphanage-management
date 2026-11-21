<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | HopeHaven</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ substr(hash_file('sha256', public_path('css/login.css')), 0, 12) }}">
    <script src="{{ asset('js/login.js') }}?v={{ substr(hash_file('sha256', public_path('js/login.js')), 0, 12) }}" defer></script>
</head>
<body>
    <header class="login-header"><a class="login-brand" href="{{ route('home') }}"><span class="brand-heart" aria-hidden="true">♥</span>HopeHaven<span class="brand-dot">.</span></a><a class="home-link" href="{{ route('home') }}">Back to home <span aria-hidden="true">↗</span></a></header>
    <main class="login-shell">
        <aside class="login-story" aria-label="Welcome to HopeHaven">
            <span class="eyebrow">TOGETHER, FOR EVERY CHILD</span>
            <div class="care-art" aria-hidden="true"><div class="art-orbit"></div><span class="art-spark">✳</span><div class="art-house"><span>♥</span></div><span class="art-note">A little care.<br>A brighter tomorrow.</span><span class="art-dot"></span></div>
            <div class="story-copy"><h2>Good people.<br>Brighter futures.</h2><p>A shared space for the people who care.</p></div>
            <div class="story-footer"><span class="care-mark" aria-hidden="true">✦</span> Small acts. Lasting change.</div>
        </aside>
        <section class="login-panel" aria-labelledby="login-title">
            <div class="panel-top"><span class="eyebrow">YOUR HOPEHAVEN ACCOUNT</span><span class="panel-flower" aria-hidden="true">✳</span></div>
            <h1 id="login-title">Welcome back<span>.</span></h1>
            <p class="login-intro">A new day to make a difference.</p>
            <ul class="login-roles" aria-label="For all HopeHaven users"><li>Admin</li><li>Director</li><li>Staff</li><li>Volunteer</li></ul>
            @if (! $accountsReady)
                <p class="login-notice" role="status">Account sign-in is not available yet. Please contact your administrator.</p>
            @endif
            @if ($errors->any())
                <div class="login-error" role="alert">{{ $errors->first() }}</div>
            @endif
            @if (session('status'))<p class="login-notice" role="status">{{ session('status') }}</p>@endif
            <form method="POST" action="{{ url('/login') }}" id="login-form">
                @csrf
                <label for="login-email">Email address</label>
                <input id="login-email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" placeholder="you@example.com" maxlength="254" required @error('email') aria-invalid="true" @enderror>
                <label for="login-password">Password</label>
                <div class="password-field"><input id="login-password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required><button class="password-toggle" type="button" aria-label="Show password" aria-controls="login-password" aria-pressed="false" hidden>Show</button></div>
                <div class="login-options"><label class="remember-label"><input name="remember" type="checkbox" value="1" @checked(old('remember'))> Remember me</label><a href="{{ route('home') }}#contact">Need help?</a></div>
                <button class="login-submit" type="submit" @disabled(! $accountsReady)>Log in <span aria-hidden="true">→</span></button>
            </form>
            <p class="login-join">New to volunteering? <a href="{{ route('volunteer.register') }}">Join our community <span aria-hidden="true">↗</span></a></p>
            <p class="login-caption">One login. A shared purpose.</p>
        </section>
    </main>
    <footer class="login-footer">HopeHaven <span aria-hidden="true">·</span> Here for brighter days.</footer>
</body>
</html>
