<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Orphanage Management System') | HopeHaven</title>
    
    <!-- Google Fonts: Poppins & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Animate.css for entrance transitions -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
    
    <!-- Portal Stylesheet (Base / Shared Framework) -->
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    
    <!-- Page-Specific Stylesheet Stack -->
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/table-alignment.css') }}?v={{ filemtime(public_path('css/portal/table-alignment.css')) }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
@php
    $isDirector = request()->routeIs('director.*') || request()->is('director*') || request('view') === 'director' || request('role') === 'director';
    $isStaff = !$isDirector && (request()->routeIs('dashboard.staff') || request()->routeIs('staff.*') || request()->routeIs('*.staff') || request('view') === 'staff' || request('role') === 'staff');
@endphp
<body>
    <div class="portal-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="portal-sidebar">
            <div>
                <div class="sidebar-brand">
                    <a href="{{ route('home') }}" class="brand-link" title="Return to Homepage">
                        <div class="brand-icon" style="{{ $isDirector ? 'background: linear-gradient(135deg, #6366f1, #4338ca); box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);' : '' }}">
                            <i class="{{ $isDirector ? 'fas fa-crown' : 'fas fa-heart' }}"></i>
                        </div>
                        <div>
                            <div class="brand-name">HopeHaven<span>.</span></div>
                            <span class="brand-sub">{{ $isDirector ? 'Director Governance' : ($isStaff ? 'Caregiver Portal' : 'Management Portal') }}</span>
                        </div>
                    </a>
                </div>

                <div class="sidebar-nav">
                    @if($isDirector)
                        <div class="nav-section-title">Directorate Oversight</div>

                        <a href="{{ route('director.dashboard') }}" class="nav-item {{ request()->routeIs('director.dashboard*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Director Dashboard</span>
                        </a>

                        <a href="{{ route('director.inventory-requests') }}" class="nav-item {{ request()->routeIs('director.inventory-requests*') ? 'active' : '' }}">
                            <i class="fas fa-boxes-packing"></i>
                            <span>Staff Inventory Requests</span>
                        </a>

                        <a href="{{ route('director.medical-records') }}" class="nav-item {{ request()->routeIs('director.medical-records*') ? 'active' : '' }}">
                            <i class="fas fa-heartbeat"></i>
                            <span>Children Medical Records</span>
                        </a>

                        <a href="{{ route('director.children-education') }}" class="nav-item {{ request()->routeIs('director.children-education*') ? 'active' : '' }}">
                            <i class="fas fa-user-graduate"></i>
                            <span>Education of Children</span>
                        </a>

                        <div class="nav-section-title" style="margin-top: 16px;">Sanctuary Governance</div>

                        <a href="{{ route('director.staff-volunteers') }}" class="nav-item {{ request()->routeIs('director.staff-volunteers*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>Staff & Volunteers</span>
                        </a>

                        <a href="{{ route('director.donations') }}" class="nav-item {{ request()->routeIs('director.donations*') ? 'active' : '' }}">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>Donations & Grants</span>
                        </a>

                        <div class="nav-section-title" style="margin-top: 16px;">Access & Security</div>

                        <a href="{{ route('director.profile') }}" class="nav-item {{ request()->routeIs('director.profile*') ? 'active' : '' }}">
                            <i class="fas fa-id-card"></i>
                            <span>My Profile</span>
                        </a>

                        <a href="{{ route('director.register-staff') }}" class="nav-item {{ request()->routeIs('director.register-staff*') ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i>
                            <span>Register Staff</span>
                        </a>
                    @else
                        <div class="nav-section-title">Sanctuary Operations</div>

                        <a href="{{ $isStaff ? route('child-welfare', ['view' => 'staff']) : route('child-welfare') }}" class="nav-item {{ request()->routeIs('child-welfare*') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Child Welfare & Edu</span>
                        </a>

                        <a href="{{ $isStaff ? route('staff.inventory') : route('inventory') }}" class="nav-item {{ request()->routeIs('inventory*') || request()->routeIs('staff.inventory*') ? 'active' : '' }}">
                            <i class="fas fa-boxes"></i>
                            <span>{{ $isStaff ? 'Staff Inventory' : 'Inventory & Supplies' }}</span>
                        </a>

                        <a href="{{ $isStaff ? route('staff.welfare-requests') : route('welfare-requests') }}" class="nav-item {{ request()->routeIs('welfare-requests*') || request()->routeIs('staff.welfare-requests*') ? 'active' : '' }}">
                            <i class="{{ $isStaff ? 'fas fa-hand-holding-heart' : 'fas fa-clipboard-check' }}"></i>
                            <span>{{ $isStaff ? 'Staff Welfare Requests' : 'Welfare Approvals & Review' }}</span>
                        </a>

                        <a href="{{ $isStaff ? route('staff.staff-volunteers') : route('staff-volunteers') }}" class="nav-item {{ request()->routeIs('staff-volunteers*') || request()->routeIs('staff.staff-volunteers*') || request()->routeIs('staff.volunteers*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>{{ $isStaff ? 'Duty Roster & Volunteers' : 'Staff & Volunteers' }}</span>
                        </a>

                        <a href="{{ $isStaff ? route('staff.donations') : route('donations') }}" class="nav-item {{ request()->routeIs('donations*') || request()->routeIs('staff.donations*') ? 'active' : '' }}">
                            <i class="fas fa-hand-holding-usd"></i>
                            <span>{{ $isStaff ? 'Staff Donations' : 'Donations & Contributions' }}</span>
                        </a>

                        <div class="nav-section-title" style="margin-top: 16px;">Access & Security</div>

                        <a href="{{ $isStaff ? route('staff.profile') : route('profile') }}" class="nav-item {{ request()->routeIs('profile*') || request()->routeIs('staff.profile*') ? 'active' : '' }}">
                            <i class="fas fa-id-card"></i>
                            <span>My Profile</span>
                        </a>
                        
                        @if(!$isStaff)
                        <a href="{{ route('register') }}" class="nav-item {{ request()->routeIs('register') ? 'active' : '' }}">
                            <i class="fas fa-user-shield"></i>
                            <span>Register Admin</span>
                        </a>
                        @endif
                    @endif
                </div>
            </div>

            <div class="sidebar-footer">
                @if($isDirector)
                    <a href="{{ route('director.profile') }}" class="user-profile-badge" style="text-decoration: none; color: inherit; display: flex; width: 100%; transition: opacity 0.2s;" title="View Executive Credentials">
                        <div class="user-avatar" style="background: linear-gradient(135deg, #6366f1, #4338ca);">
                            SS
                        </div>
                        <div>
                            <div class="user-meta-name">Sister Sarah</div>
                            <div class="user-meta-role">Orphanage Director</div>
                        </div>
                    </a>
                @elseif($isStaff)
                    <a href="{{ route('staff.profile') }}" class="user-profile-badge" style="text-decoration: none; color: inherit; display: flex; width: 100%; transition: opacity 0.2s;" title="View Profile & Duty Status">
                        <div class="user-avatar" style="background: linear-gradient(135deg, #10b981, #059669);">
                            NM
                        </div>
                        <div>
                            <div class="user-meta-name">Nurse Mary</div>
                            <div class="user-meta-role">Senior Caregiver · On Duty</div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('profile') }}" class="user-profile-badge" style="text-decoration: none; color: inherit; display: flex; width: 100%; transition: opacity 0.2s;" title="View Profile & Credentials">
                        <div class="user-avatar">
                            SG
                        </div>
                        <div>
                            <div class="user-meta-name">Sister Grace</div>
                            <div class="user-meta-role">Sanctuary Administrator</div>
                        </div>
                    </a>
                @endif
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="portal-main">
            <!-- Topbar Header -->
            <header class="portal-topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" id="mobile_sidebar_toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="breadcrumb-tag">
                        <a href="{{ $isDirector ? route('director.dashboard') : ($isStaff ? route('dashboard.staff') : route('dashboard')) }}">
                            <i class="fas fa-home"></i> {{ $isDirector ? 'Director Portal' : ($isStaff ? 'Staff Portal' : 'Admin Portal') }}
                        </a>
                        <span>/</span>
                        <span class="active">@yield('breadcrumb', 'Dashboard')</span>
                    </div>
                </div>

                <div class="topbar-right">
                    <!-- Dashboard Role Switcher Pill -->
                    <div class="dashboard-view-switcher" style="margin-right: 6px;">
                        <a href="{{ route('director.dashboard') }}" class="{{ $isDirector ? 'active director-mode' : '' }}" title="Executive Director Governance">
                            <i class="fas fa-crown"></i> Director View
                        </a>
                        <a href="{{ route('dashboard') }}" class="{{ !$isDirector && !$isStaff ? 'active admin-mode' : '' }}" title="Executive Admin Dashboard">
                            <i class="fas fa-shield-alt"></i> Admin View
                        </a>
                        <a href="{{ route('dashboard.staff') }}" class="{{ $isStaff ? 'active staff-mode' : '' }}" title="Caregiver & Daily Operations Dashboard">
                            <i class="fas fa-user-clock"></i> Staff View
                        </a>
                    </div>

                    <!-- Notification Bell & Dropdown -->
                    <div class="topbar-notifications-wrap">
                        <button class="topbar-bell-btn" id="topbar_bell_toggle" onclick="toggleNotificationsDropdown(event)" title="Duty & Sanctuary Notifications">
                            <i class="far fa-bell"></i>
                            <span class="bell-badge" id="topbar_bell_count">3</span>
                        </button>

                        <div class="notifications-dropdown" id="notifications_dropdown">
                            <div class="notif-dropdown-head">
                                <div class="notif-head-title">
                                    <i class="fas fa-bell" style="color: var(--primary);"></i>
                                    <span>Duty Notifications</span>
                                    <span class="badge" style="background: #e2e8f0; font-size: 11px; padding: 2px 7px;" id="dropdown_unread_badge">3 New</span>
                                </div>
                                <button class="notif-head-btn" onclick="markAllNotificationsRead(event)">Mark Read</button>
                            </div>

                            <div class="notif-dropdown-list" id="dropdown_notif_list">
                                <!-- Item 1: Clinical Dental Check -->
                                <div class="notif-item unread" onclick="handleNotificationClick('Aminah Dental Appointment', '10:30 AM with Volunteer Sarah at Klinik Titiwangsa')">
                                    <div class="notif-item-icon red">
                                        <i class="fas fa-tooth"></i>
                                    </div>
                                    <div class="notif-item-text">
                                        <div class="notif-item-title">Dental Clinic Visit (Aminah)</div>
                                        <div class="notif-item-desc">Scheduled at 10:30 AM with Volunteer Sarah. Transport confirmed.</div>
                                        <div class="notif-item-time">12 mins ago · Health Routine</div>
                                    </div>
                                </div>

                                <!-- Item 2: Medication Alert -->
                                <div class="notif-item unread" onclick="handleNotificationClick('Ahmad Inhaler Review', 'Scheduled at 14:00 PM before sports activity')">
                                    <div class="notif-item-icon amber">
                                        <i class="fas fa-lungs"></i>
                                    </div>
                                    <div class="notif-item-text">
                                        <div class="notif-item-title">Asthma Inhaler Check (Ahmad)</div>
                                        <div class="notif-item-desc">Afternoon peak-flow inspection required at 14:00 PM.</div>
                                        <div class="notif-item-time">35 mins ago · Medication</div>
                                    </div>
                                </div>

                                <!-- Item 3: Handover Note -->
                                <div class="notif-item unread" onclick="handleNotificationClick('Night Shift Handover', 'Brother Thomas logged peaceful night report')">
                                    <div class="notif-item-icon green">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div class="notif-item-text">
                                        <div class="notif-item-title">Night Shift Handover Published</div>
                                        <div class="notif-item-desc">Brother Thomas: "All 4 wards slept well. Oatmeal breakfast prepped."</div>
                                        <div class="notif-item-time">1 hr ago · Duty Log</div>
                                    </div>
                                </div>

                                <!-- Item 4: Storeroom Approval -->
                                <div class="notif-item" onclick="handleNotificationClick('Storeroom Dispatch', 'Ward Alpha supplies ready for collection at Bay 2')">
                                    <div class="notif-item-icon purple">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <div class="notif-item-text">
                                        <div class="notif-item-title">Supply Request Approved</div>
                                        <div class="notif-item-desc">Sister Grace approved Nursery Wipes & Formula milk package.</div>
                                        <div class="notif-item-time">2 hrs ago · Storeroom</div>
                                    </div>
                                </div>
                            </div>

                            <div class="notif-dropdown-footer">
                                <a href="{{ route('dashboard.staff') }}" class="notif-footer-link">
                                    View Staff Duty Schedule &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($isDirector)
                        <a href="{{ route('director.profile') }}" class="topbar-user-link {{ request()->routeIs('director.profile*') ? 'active' : '' }}" title="Director Governance & Executive Profile">
                            <span class="topbar-avatar" style="background: linear-gradient(135deg, #6366f1, #4338ca);">SS</span>
                            <span class="topbar-username">Sister Sarah</span>
                            <span class="topbar-role-badge" style="background: rgba(99, 102, 241, 0.12); color: #4338ca; border-color: rgba(99, 102, 241, 0.25);">Director</span>
                        </a>
                    @elseif($isStaff)
                        <a href="{{ route('staff.profile') }}" class="topbar-user-link {{ request()->routeIs('profile*') || request()->routeIs('staff.profile*') ? 'active' : '' }}" title="My Staff Profile & Duty Status">
                            <span class="topbar-avatar" style="background: linear-gradient(135deg, #10b981, #059669);">NM</span>
                            <span class="topbar-username">Nurse Mary</span>
                            <span class="topbar-role-badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; border-color: rgba(16, 185, 129, 0.25);">Staff</span>
                        </a>
                    @else
                        <a href="{{ route('profile') }}" class="topbar-user-link {{ request()->routeIs('profile*') || request()->routeIs('staff.profile*') ? 'active' : '' }}" title="My Profile & Security">
                            <span class="topbar-avatar">SG</span>
                            <span class="topbar-username">Sister Grace</span>
                            <span class="topbar-role-badge">Admin</span>
                        </a>
                    @endif
                </div>
            </header>

            <!-- Page Content -->
            <div class="portal-content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Global Toast Element -->
    <div id="portal_toast"></div>

    <!-- Portal Scripts -->
    <script src="{{ asset('js/portal.js') }}"></script>
    @stack('scripts')
</body>
</html>
