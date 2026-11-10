<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Volunteer Registration | HopeHaven</title>
    <link rel="stylesheet" href="{{ asset('css/volunteer-register.css') }}?v={{ substr(hash_file('sha256', public_path('css/volunteer-register.css')), 0, 12) }}">
</head>
<body>
    <header><a class="brand" href="{{ route('home') }}">HopeHaven<span>.</span></a><a class="back" href="{{ route('home') }}">← Back to home</a></header>
    <main>
        <aside><span class="eyebrow">A LITTLE TIME. A LOT OF GOOD.</span><div class="heart" aria-hidden="true">♡</div><h1>Show up.<br>Make a difference.</h1><p>Join our volunteer community and help create brighter days for children.</p><span class="note">Every helping hand counts.</span></aside>
        <section aria-labelledby="registration-title">
            <span class="eyebrow">GET INVOLVED</span><h2 id="registration-title">Register as a volunteer</h2><p class="intro">Start with a few details about you.</p>
            <form id="volunteer-form">
                <label for="full-name">Full name <span>*</span></label><input id="full-name" name="name" autocomplete="name" maxlength="120" required placeholder="Your full name">
                <label for="email">Email <span>*</span></label><input id="email" name="email" type="email" autocomplete="email" maxlength="254" required placeholder="you@example.com">
                <label for="phone">Phone number <span>*</span></label><input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="30" required placeholder="e.g. +60 12 345 6789">
                <p class="storage-note">Your details are saved on this browser only. Event sign-up will be available when events are added.</p>
                <button type="submit">Register as a volunteer <span aria-hidden="true">↗</span></button>
                <p id="registration-message" role="status" aria-live="polite"></p>
            </form>
        </section>
    </main>
    <script src="{{ asset('js/volunteer-register.js') }}" defer></script>
</body>
</html>
