@php
    $artRole = request()->routeIs('director.*') ? 'director' : (request()->routeIs('dashboard.staff') ? 'staff' : 'admin');
    $artIcon = ['director' => 'fa-seedling', 'admin' => 'fa-layer-group', 'staff' => 'fa-heart'][$artRole];
@endphp
<aside class="portal-hero-art" aria-hidden="true">
    <div class="portal-art-orbit" aria-hidden="true">
        <span class="portal-orbit-ring ring-one"></span><span class="portal-orbit-ring ring-two"></span>
        <span class="portal-orbit-center"><i class="fas {{ $artIcon }}"></i></span>
        <span class="portal-orbit-spark"><i class="fas fa-asterisk"></i></span>
        <span class="portal-orbit-heart"><i class="far fa-heart"></i></span>
    </div>
</aside>
