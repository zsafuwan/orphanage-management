@extends('layouts.portal')

@php
    $isStaff = request()->routeIs('dashboard.staff') || request()->routeIs('staff.*') || request()->routeIs('*.staff') || request('view') === 'staff' || request('role') === 'staff';
@endphp

@section('title', $isStaff ? 'Staff Schedules & Volunteers' : 'Staff & Volunteer Management')
@section('breadcrumb', $isStaff ? 'Work Schedules & Volunteers' : 'Staff & Volunteers')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/staff-volunteers.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/staff-volunteers.css')), 0, 12) }}">
@endpush

@section('content')
<!-- Subsystem Header -->
<div class="page-header-card">
    <div>
        
        <h1 class="page-title">{{ $isStaff ? 'Staff Schedules & Volunteers' : 'Staff, Volunteers & Work Schedules' }}</h1>
        <p class="page-desc">
            {{ $isStaff 
                ? 'Review your caregiving shifts, coordinate with on-duty staff across wings, log volunteer activity hours, and check daily shift handovers.' 
                : 'Register employees and volunteers, monitor child safeguarding & pediatric CPR certifications, schedule daily caregiving duty rosters, and track volunteer community hours.' }}
        </p>
    </div>
    <div class="header-action-group">
        @if($isStaff)
            <button class="btn-outline" onclick="openModal('modal_assign_shift')">
                <i class="fas fa-exchange-alt"></i> Shift Handover Note
            </button>
        @else
            <button class="btn-primary" onclick="openModal('modal_register_personnel')">
                <i class="fas fa-user-plus"></i> Add Staff or Volunteer
            </button>
            <button class="btn-outline" onclick="openModal('modal_volunteer_hours')">
                <i class="fas fa-clock"></i> Log Volunteer Hours
            </button>
            <button class="btn-outline" onclick="openModal('modal_assign_shift')">
                <i class="fas fa-calendar-alt"></i> Assign Shift
            </button>
        @endif
    </div>
</div>

<!-- Metric KPI Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Permanent Staff</span>
            <div class="metric-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                <i class="fas fa-id-badge"></i>
            </div>
        </div>
        <div class="metric-val">3 Staff</div>
        <div class="metric-sub">Social workers, house parents, teachers</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Active Volunteers</span>
            <div class="metric-icon" style="background: rgba(255, 44, 90, 0.12); color: var(--primary);">
                <i class="fas fa-heart"></i>
            </div>
        </div>
        <div class="metric-val">2 Active</div>
        <div class="metric-sub">Weekend tutoring & medical clinics</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Total Volunteer Hours</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-stopwatch"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #10b981;">232 hrs</div>
        <div class="metric-sub">Verified community service logged</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Safeguarding Compliance</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #f59e0b;">1 Expiring</div>
        <div class="metric-sub">CPR renewal due in 30 days</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container">
    <button class="tab-btn active" data-tab="tab_directory">
        <i class="fas fa-users"></i> Staff & Volunteers Directory (5)
    </button>
    <button class="tab-btn" data-tab="tab_credentials">
        <i class="fas fa-award"></i> Safeguarding & Training & Qualifications
    </button>
    <button class="tab-btn" data-tab="tab_roster">
        <i class="fas fa-calendar-check"></i> Duty & Shift Rostering
    </button>
    <button class="tab-btn" data-tab="tab_attendance">
        <i class="fas fa-clock"></i> Volunteer Attendance & Hours
    </button>
</div>

<!-- Search & Filter Bar -->
<div class="filter-bar">
    <div class="search-box-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" class="portal-input search-input" data-search-target=".searchable-staff" placeholder="Search by name, role, or department...">
    </div>
    <div style="display: flex; gap: 10px;">
        <select class="portal-select category-filter" data-filter-target=".searchable-staff">
            <option value="All">All Staff Types</option>
            <option value="Staff">Permanent Staff</option>
            <option value="Volunteer">Active Volunteers</option>
        </select>
    </div>
</div>

<!-- TAB 1: PERSONNEL DIRECTORY -->
<div id="tab_directory" class="tab-pane personnel-directory">
    <div class="cards-grid-3">
        <!-- Person 1 -->
        <div class="searchable-staff" data-category="Staff">
            <div class="child-profile-card">
                <div>
                    <div class="card-top-info">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&auto=format&fit=crop&crop=faces&q=85" class="avatar-large" alt="Sister Teresa">
                        <div style="text-align: right;">
                            <div style="margin-top: 4px;"><span class="badge badge-success">Cleared</span></div>
                        </div>
                    </div>
                    <div class="profile-name">Sister Teresa Maria</div>
                    <div class="profile-meta-line">Senior Case Social Worker</div>

                    <div class="info-rows">
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-building" aria-hidden="true"></i> Organization affiliation:</span>
                            <span class="info-row-val">Not provided</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-envelope"></i> Email:</span>
                            <span class="info-row-val">teresa.m@hopehaven.org</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-phone"></i> Phone:</span>
                            <span class="info-row-val">+1 (555) 789-0123</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-certificate"></i> Credential:</span>
                            <span class="info-row-val" style="color: #10b981;">LCSW License Valid</span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                    <span class="badge badge-secondary">Trauma Counseling</span>
                    <span class="badge badge-secondary">Adoption</span>
                </div>
            </div>
        </div>

        <!-- Person 2 -->
        <div class="searchable-staff" data-category="Staff">
            <div class="child-profile-card">
                <div>
                    <div class="card-top-info">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&auto=format&fit=crop&crop=faces&q=85" class="avatar-large" alt="Brother Luke">
                        <div style="text-align: right;">
                            <div style="margin-top: 4px;"><span class="badge badge-warning">CPR Renewal Due</span></div>
                        </div>
                    </div>
                    <div class="profile-name">Brother Luke Montgomery</div>
                    <div class="profile-meta-line">Boys Dorm Supervisor & House Parent</div>

                    <div class="info-rows">
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-building" aria-hidden="true"></i> Organization affiliation:</span>
                            <span class="info-row-val">Not provided</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-envelope"></i> Email:</span>
                            <span class="info-row-val">luke.m@hopehaven.org</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-phone"></i> Phone:</span>
                            <span class="info-row-val">+1 (555) 890-1234</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-certificate"></i> CPR/AED:</span>
                            <span class="info-row-val" style="color: #f59e0b;">Exp: Nov 15</span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                    <span class="badge badge-secondary">Youth Guidance</span>
                    <span class="badge badge-secondary">Residential Safety</span>
                </div>
            </div>
        </div>

        <!-- Person 3 -->
        <div class="searchable-staff" data-category="Staff">
            <div class="child-profile-card">
                <div>
                    <div class="card-top-info">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&auto=format&fit=crop&crop=faces&q=85" class="avatar-large" alt="David Tan">
                        <div style="text-align: right;">
                            <div style="margin-top: 4px;"><span class="badge badge-success">Cleared</span></div>
                        </div>
                    </div>
                    <div class="profile-name">Mr. David Tan</div>
                    <div class="profile-meta-line">Lead Educator & Academic Head</div>

                    <div class="info-rows">
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-building" aria-hidden="true"></i> Organization affiliation:</span>
                            <span class="info-row-val">Not provided</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-envelope"></i> Email:</span>
                            <span class="info-row-val">david.tan@hopehaven.org</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-phone"></i> Phone:</span>
                            <span class="info-row-val">+1 (555) 901-2345</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-certificate"></i> License:</span>
                            <span class="info-row-val" style="color: #10b981;">B.Ed STEM Certified</span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                    <span class="badge badge-secondary">STEM Curriculum</span>
                    <span class="badge badge-secondary">Remedial Reading</span>
                </div>
            </div>
        </div>

        <!-- Person 4: Volunteer -->
        <div class="searchable-staff" data-category="Volunteer">
            <div class="child-profile-card">
                <div>
                    <div class="card-top-info">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&auto=format&fit=crop&crop=faces&q=85" class="avatar-large" alt="Elena Rostova">
                        <div style="text-align: right;">
                            <span class="badge badge-info" style="background: rgba(255, 44, 90, 0.12); color: var(--primary);">Active Volunteer</span>
                            <div style="margin-top: 4px;"><span class="badge badge-success">148 Hours</span></div>
                        </div>
                    </div>
                    <div class="profile-name">Elena Rostova</div>
                    <div class="profile-meta-line">Weekend Math & Arts Volunteer Tutor</div>

                    <div class="info-rows">
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-building" aria-hidden="true"></i> Organization affiliation:</span>
                            <span class="info-row-val">Not provided</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-clock"></i> Schedule:</span>
                            <span class="info-row-val">Saturday & Sunday</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-shield-alt"></i> Clearance:</span>
                            <span class="info-row-val" style="color: #10b981;">Police & Child Safe</span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                    <span class="badge badge-secondary">High School Algebra</span>
                    <span class="badge badge-secondary">Watercolor Arts</span>
                </div>
            </div>
        </div>

        <!-- Person 5: Volunteer -->
        <div class="searchable-staff" data-category="Volunteer">
            <div class="child-profile-card">
                <div>
                    <div class="card-top-info">
                        <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=400&h=400&auto=format&fit=crop&crop=faces&q=85" class="avatar-large" alt="Dr. Kevin Zhao">
                        <div style="text-align: right;">
                            <span class="badge badge-info" style="background: rgba(255, 44, 90, 0.12); color: var(--primary);">Active Volunteer</span>
                            <div style="margin-top: 4px;"><span class="badge badge-success">84 Hours</span></div>
                        </div>
                    </div>
                    <div class="profile-name">Dr. Kevin Zhao</div>
                    <div class="profile-meta-line">Volunteer Pediatric Dental Consultant</div>

                    <div class="info-rows">
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-building" aria-hidden="true"></i> Organization affiliation:</span>
                            <span class="info-row-val">Not provided</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-clock"></i> Schedule:</span>
                            <span class="info-row-val">Alternate Fridays</span>
                        </div>
                        <div class="info-row">
                            <span class="info-row-label"><i class="fas fa-shield-alt"></i> Dental Board:</span>
                            <span class="info-row-val" style="color: #10b981;">DDS License Active</span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                    <span class="badge badge-secondary">Pediatric Dentistry</span>
                    <span class="badge badge-secondary">Oral Screenings</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB 2: CREDENTIALS & SAFEGUARDING -->
<div id="tab_credentials" class="tab-pane" style="display: none;">
    <div class="portal-card">
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Certification / Qualification</th>
                        <th>Accreditation Authority</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Sister Teresa Maria</strong> (Staff)</td>
                        <td>Licensed Clinical Social Worker (LCSW)</td>
                        <td>State Board of Behavioral Health</td>
                        <td style="font-family: monospace;">2020-04-10</td>
                        <td style="font-family: monospace;">2028-04-10</td>
                        <td><span class="badge badge-success">Valid</span></td>
                    </tr>
                    <tr>
                        <td><strong>Brother Luke Montgomery</strong> (Staff)</td>
                        <td>Pediatric CPR & AED First Responder</td>
                        <td>Red Cross International</td>
                        <td style="font-family: monospace;">2024-08-10</td>
                        <td style="font-family: monospace; color: #f59e0b; font-weight: 700;">2026-11-15</td>
                        <td><span class="badge badge-warning">Expiring in 30d</span></td>
                    </tr>
                    <tr>
                        <td><strong>Mr. David Tan</strong> (Staff)</td>
                        <td>Bachelor of Education (STEM Focus)</td>
                        <td>National Teachers University</td>
                        <td style="font-family: monospace;">2018-06-20</td>
                        <td style="font-family: monospace;">2030-01-01</td>
                        <td><span class="badge badge-success">Valid</span></td>
                    </tr>
                    <tr>
                        <td><strong>Elena Rostova</strong> (Volunteer)</td>
                        <td>Child Safeguarding & Safety Code</td>
                        <td>HopeHaven Compliance Board</td>
                        <td style="font-family: monospace;">2024-02-10</td>
                        <td style="font-family: monospace;">2027-02-10</td>
                        <td><span class="badge badge-success">Valid</span></td>
                    </tr>
                    <tr>
                        <td><strong>Dr. Kevin Zhao</strong> (Volunteer)</td>
                        <td>Doctor of Dental Surgery (DDS)</td>
                        <td>National Dental Board</td>
                        <td style="font-family: monospace;">2015-05-20</td>
                        <td style="font-family: monospace;">2029-05-20</td>
                        <td><span class="badge badge-success">Valid</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 3: DUTY ROSTER SHIFTS -->
<div id="tab_roster" class="tab-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span class="badge badge-secondary">Academic Tutoring</span>
                <span class="badge badge-success">Scheduled</span>
            </div>
            <h4 style="font-size: 15px; font-weight: 800;">Evening Study Hall & Math Review</h4>
            <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Assigned to: <strong>Elena Rostova</strong> (Volunteer)</p>
            <div style="margin-top: 12px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                <div><i class="fas fa-map-marker-alt"></i> Location: HopeHaven Study Library</div>
                <div><i class="fas fa-clock"></i> Saturday &bull; 09:00 AM - 12:00 PM</div>
            </div>
        </div>

        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span class="badge badge-secondary">Child Care & Dorm</span>
                <span class="badge badge-success">Scheduled</span>
            </div>
            <h4 style="font-size: 15px; font-weight: 800;">Boys Dormitory Night Watch & Devotions</h4>
            <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Assigned to: <strong>Brother Luke Montgomery</strong> (Staff)</p>
            <div style="margin-top: 12px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                <div><i class="fas fa-map-marker-alt"></i> Location: St. Francis Boys Hall</div>
                <div><i class="fas fa-clock"></i> Daily (Mon - Fri) &bull; 07:00 PM - 07:00 AM</div>
            </div>
        </div>

        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span class="badge badge-secondary">Medical Escort</span>
                <span class="badge badge-success">Scheduled</span>
            </div>
            <h4 style="font-size: 15px; font-weight: 800;">Pediatric Oral Health Clinic Checkups</h4>
            <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Assigned to: <strong>Dr. Kevin Zhao</strong> (Volunteer)</p>
            <div style="margin-top: 12px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                <div><i class="fas fa-map-marker-alt"></i> Location: Orphanage Medical Clinic</div>
                <div><i class="fas fa-clock"></i> Alternate Fridays &bull; 02:00 PM - 05:00 PM</div>
            </div>
        </div>
    </div>
</div>

<!-- TAB 4: VOLUNTEER ATTENDANCE LOG -->
<div id="tab_attendance" class="tab-pane" style="display: none;">
    <div class="portal-card">
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Volunteer Name</th>
                        <th>Activity / Duty</th>
                        <th>Date</th>
                        <th>Hours Logged</th>
                        <th>Supervisor Signoff</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody id="attendance_tbody">
                    <tr>
                        <td><strong>Elena Rostova</strong></td>
                        <td>Math Tutoring for Grade 5 & Grade 9</td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-17</td>
                        <td><span class="badge badge-success" style="font-weight: 800;">3.5 hrs</span></td>
                        <td>Mr. David Tan</td>
                        <td style="font-style: italic; font-size: 12px; color: #64748b;">"Excellent rapport with Maya and Elijah on algebra worksheets."</td>
                    </tr>
                    <tr>
                        <td><strong>Dr. Kevin Zhao</strong></td>
                        <td>Routine Pediatric Dental Screening</td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-10</td>
                        <td><span class="badge badge-success" style="font-weight: 800;">4.0 hrs</span></td>
                        <td>Sister Teresa</td>
                        <td style="font-style: italic; font-size: 12px; color: #64748b;">"Screened 12 children, scheduled dental clinic extraction."</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL 1: REGISTER PERSONNEL -->
<div id="modal_register_personnel" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 520px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Register Staff or Volunteer</h3>
                <p class="modal-sub">Add staff & Volunteers to human resources directory.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_register_personnel')">&times;</button>
        </div>

        <form onsubmit="event.preventDefault(); closeModal('modal_register_personnel'); showPortalToast('Personnel registered successfully!', true);">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-control" placeholder="e.g. Sister Angela Davis" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Classification</label>
                    <select class="form-control">
                        <option>Permanent Staff</option>
                        <option>Active Volunteer</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Job Role</label>
                    <input type="text" class="form-control" placeholder="House Parent, Tutor">
                </div>
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select class="form-control">
                        <option>Child Welfare & Protection</option>
                        <option>Residential Operations</option>
                        <option>Education & Tutoring</option>
                        <option>Health & Clinic</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="name@hopehaven.org">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" placeholder="+1 (555) 000-0000">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_register_personnel')">Cancel</button>
                <button type="submit" class="btn-primary">Save Staff Member</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: LOG VOLUNTEER HOURS -->
<div id="modal_volunteer_hours" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 480px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Log Volunteer Service Attendance</h3>
                <p class="modal-sub">Record hours and supervisor feedback.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_volunteer_hours')">&times;</button>
        </div>

        <form onsubmit="handleVolunteerHoursSubmit(event)">
            <div class="form-group">
                <label class="form-label">Volunteer</label>
                <select id="vol_name_select" class="form-control">
                    <option>Elena Rostova</option>
                    <option>Dr. Kevin Zhao</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Activity Performed</label>
                <input type="text" id="vol_activity_input" class="form-control" value="Weekend Math Tutoring Session" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Hours Served</label>
                    <input type="number" id="vol_hours_input" class="form-control" value="3.5" step="0.5" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Supervisor Signoff</label>
                    <input type="text" class="form-control" value="Mr. David Tan">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_volunteer_hours')">Cancel</button>
                <button type="submit" class="btn-primary">Record Service Hours</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: ASSIGN SHIFT -->
<div id="modal_assign_shift" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 480px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Schedule a Shift</h3>
                <p class="modal-sub">Assign caregiving, tutoring or kitchen shift.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_assign_shift')">&times;</button>
        </div>

        <form onsubmit="event.preventDefault(); closeModal('modal_assign_shift'); showPortalToast('Duty shift assigned!', true);">
            <div class="form-group">
                <label class="form-label">Duty Activity</label>
                <input type="text" class="form-control" placeholder="e.g. Dining Hall Lunch Prep" required>
            </div>

            <div class="form-group">
                <label class="form-label">Staff & Volunteers</label>
                <select class="form-control">
                    <option>Sister Teresa Maria</option>
                    <option>Brother Luke Montgomery</option>
                    <option>Mr. David Tan</option>
                    <option>Elena Rostova</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Day of Week</label>
                    <input type="text" class="form-control" placeholder="Sunday / Mon - Fri">
                </div>
                <div class="form-group">
                    <label class="form-label">Shift Hours</label>
                    <input type="text" class="form-control" placeholder="10:00 AM - 02:00 PM">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_assign_shift')">Cancel</button>
                <button type="submit" class="btn-primary">Schedule Duty</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.personnel-directory .child-profile-card').forEach(function (card) {
        const rows = card.querySelector('.info-rows');
        if (!rows) return;
        const name = card.querySelector('.profile-name').textContent.trim();
        const details = document.createElement('details');
        details.className = 'personnel-details';
        const summary = document.createElement('summary');
        summary.textContent = 'Contact & Qualifications';
        summary.setAttribute('aria-label', 'Contact and credentials for ' + name);
        details.appendChild(summary);
        const body = document.createElement('div');
        body.className = 'personnel-details-body';
        Array.from(rows.children).slice(1).forEach(function (row) {
            const value = row.querySelector('.info-row-val');
            const label = row.querySelector('.info-row-label');
            if (value && label && /Email:|Phone:/.test(label.textContent)) {
                const link = document.createElement('a');
                link.textContent = value.textContent.trim();
                link.href = /Email:/.test(label.textContent) ? 'mailto:' + link.textContent : 'tel:' + link.textContent.replace(/[^+0-9]/g, '');
                value.replaceChildren(link);
            }
            body.appendChild(row);
        });
        details.appendChild(body);
        rows.after(details);
    });
});
</script>
@endpush
