<nav class="care-shortcuts" aria-label="Workspace shortcuts">
    <div class="care-shortcuts-intro"><strong>Quick links</strong></div>
    @if(request()->routeIs('director.*'))
        <a href="{{ route('director.medical-records') }}"><span class="care-shortcut-icon peach"><i class="fas fa-heart-pulse" aria-hidden="true"></i></span><span><strong>Children's wellbeing</strong><small>Health records & appointments</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        <a href="{{ route('director.inventory-requests') }}"><span class="care-shortcut-icon sage"><i class="fas fa-box-open" aria-hidden="true"></i></span><span><strong>Review requests</strong><small>Keep essential care moving</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        <a href="{{ route('director.donations') }}"><span class="care-shortcut-icon lavender"><i class="fas fa-hand-holding-heart" aria-hidden="true"></i></span><span><strong>Community support</strong><small>Donations, grants & giving</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
    @else
        <a href="{{ request()->routeIs('dashboard.staff') ? route('staff.child-welfare') : route('child-welfare') }}"><span class="care-shortcut-icon peach"><i class="fas fa-heart-pulse" aria-hidden="true"></i></span><span><strong>Care for a child</strong><small>Profiles, learning & wellbeing</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        @if(request()->routeIs('dashboard.staff'))
        <a href="{{ route('staff.inventory') }}"><span class="care-shortcut-icon sage"><i class="fas fa-box-open" aria-hidden="true"></i></span><span><strong>Check supplies</strong><small>The essentials for every day</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        @endif
        <a href="{{ request()->routeIs('dashboard.staff') ? route('staff.welfare-requests') : route('welfare-requests') }}"><span class="care-shortcut-icon lavender"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span><span><strong>Welfare requests</strong><small>Follow up on care and support</small></span><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
    @endif
</nav>
