@php
    $artRole = request()->routeIs('director.*') ? 'director' : (request()->routeIs('dashboard.staff') ? 'staff' : 'admin');
    $artCopy = [
        'director' => ['THE BIGGER PICTURE', 'Lead with care.', 'A clearer view. A brighter future.', 'fa-seedling'],
        'admin' => ['GOOD THINGS, WELL ORGANISED', 'Make care happen.', 'Every detail makes a difference.', 'fa-layer-group'],
        'staff' => ['HERE FOR THEIR EVERYDAY', 'Little moments. Big care.', 'Your everyday work means everything.', 'fa-heart'],
    ][$artRole];
@endphp
<aside class="portal-hero-art" aria-label="{{ $artCopy[1] }}">
    <div class="portal-art-caption"><span class="portal-art-dot" aria-hidden="true"></span>{{ $artCopy[0] }}</div>
    <div class="portal-art-orbit" aria-hidden="true">
        <span class="portal-orbit-ring ring-one"></span><span class="portal-orbit-ring ring-two"></span>
        <span class="portal-orbit-center"><i class="fas {{ $artCopy[3] }}"></i></span>
        <span class="portal-orbit-spark"><i class="fas fa-asterisk"></i></span>
        <span class="portal-orbit-heart"><i class="far fa-heart"></i></span>
    </div>
    <div class="portal-art-message"><strong>{{ $artCopy[1] }}</strong><span>{{ $artCopy[2] }}</span></div>
</aside>
