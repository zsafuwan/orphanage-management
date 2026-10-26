@extends('layouts.portal')

@section('title', 'Staff & Volunteers Governance')
@section('breadcrumb', 'Staff & Volunteers')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-staff-volunteers.css') }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Header -->
    <div class="dir-staff-header">
        <div>
            <h1><i class="fas fa-users-cog" style="color: #4f46e5;"></i> Personnel, Safeguarding & Duty Governance</h1>
            <p>Directorate oversight of sanctuary staff, background vetting, safeguarding certifications, and volunteer deployment.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('director.register-staff') }}" class="btn-director-approve" style="text-decoration: none; padding: 8px 16px;">
                <i class="fas fa-user-plus"></i> Register New Staff
            </a>
            <button type="button" class="btn-director-review" onclick="showPortalToast('Sanctuary Staff Vetting Register (JKM Audit) exported.', 'info')">
                <i class="fas fa-file-shield"></i> Export Staff Compliance Ledger
            </button>
        </div>
    </div>

    <!-- Director Staff KPIs -->
    <div class="dir-staff-kpis">
        <div class="dir-staff-kpi-card">
            <div class="dir-staff-kpi-icon" style="background:#eef2ff; color:#4f46e5;">
                <i class="fas fa-user-nurse"></i>
            </div>
            <div>
                <div class="dir-staff-kpi-val">8 Caregivers</div>
                <div class="dir-staff-kpi-lbl">Sanctuary Staff on Payroll</div>
            </div>
        </div>

        <div class="dir-staff-kpi-card">
            <div class="dir-staff-kpi-icon" style="background:#ecfdf5; color:#059669;">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div>
                <div class="dir-staff-kpi-val">14 Active</div>
                <div class="dir-staff-kpi-lbl">Vetted Volunteer Corps</div>
            </div>
        </div>

        <div class="dir-staff-kpi-card">
            <div class="dir-staff-kpi-icon" style="background:#f5f3ff; color:#7c3aed;">
                <i class="fas fa-shield-check"></i>
            </div>
            <div>
                <div class="dir-staff-kpi-val">100% Cleared</div>
                <div class="dir-staff-kpi-lbl">PDRM Police Background Checks</div>
            </div>
        </div>

        <div class="dir-staff-kpi-card">
            <div class="dir-staff-kpi-icon" style="background:#fef3c7; color:#d97706;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="dir-staff-kpi-val">6 On Duty</div>
                <div class="dir-staff-kpi-lbl">Active Shift Operations Today</div>
            </div>
        </div>
    </div>

    <!-- Permanent Staff Roster & Compliance Table -->
    <div class="dir-staff-table-card">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-id-badge"></i>
                <span>Sanctuary Permanent Staff & Caregiver Safeguarding</span>
            </div>
            <span class="badge" style="background:#ecfdf5; color:#047857; font-weight:700; padding:4px 10px; border-radius:20px;">
                <i class="fas fa-check"></i> All 8 Vetted & Certified
            </span>
        </div>
        <table class="dir-staff-table">
            <thead>
                <tr>
                    <th>Staff Caregiver</th>
                    <th>Sanctuary Role & Wing</th>
                    <th>Child Safeguarding & Police Check</th>
                    <th>Medical & CPR Cert</th>
                    <th>Shift Status</th>
                    <th style="text-align: right;">Director Oversight</th>
                </tr>
            </thead>
            <tbody>
                <!-- Staff 1: Nurse Mary -->
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar">NM</div>
                            <div>
                                <div class="user-cell-name">Nurse Mary Anne Joseph</div>
                                <div class="user-cell-dept">Staff ID: HH-CARE-01</div>
                            </div>
                        </div>
                    </td>
                    <td>Lead Clinical Caregiver<br><span style="font-size: 11px; color: #64748b;">Ward Alpha (Nursery & Sickbay)</span></td>
                    <td><span class="vetted-badge"><i class="fas fa-shield-check"></i> PDRM Vetted (2026)</span></td>
                    <td>Pediatric CPR Certified<br><span style="font-size: 11px; color: #10b981; font-weight: 700;">Valid to Dec 2027</span></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;">On Duty · Day Shift</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-director-review" onclick="openStaffDossier('Nurse Mary Anne Joseph', 'Lead Clinical Caregiver · Ward Alpha', 'NM')">
                            <i class="fas fa-file-shield"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Staff 2: Caregiver Lisa -->
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#fef3c7; color:#b45309;">CL</div>
                            <div>
                                <div class="user-cell-name">Caregiver Lisa Wong</div>
                                <div class="user-cell-dept">Staff ID: HH-CARE-04</div>
                            </div>
                        </div>
                    </td>
                    <td>Infant Care Specialist<br><span style="font-size: 11px; color: #64748b;">Ward Alpha (Nursery Wing)</span></td>
                    <td><span class="vetted-badge"><i class="fas fa-shield-check"></i> PDRM Vetted (2026)</span></td>
                    <td>Early Childhood First Aid<br><span style="font-size: 11px; color: #10b981; font-weight: 700;">Valid to Aug 2027</span></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;">On Duty · Day Shift</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-director-review" onclick="openStaffDossier('Caregiver Lisa Wong', 'Infant Care Specialist · Nursery Wing', 'CL')">
                            <i class="fas fa-file-shield"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Staff 3: Ustaz Hakim -->
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#e0f2fe; color:#0369a1;">UH</div>
                            <div>
                                <div class="user-cell-name">Ustaz Hakim Bin Salleh</div>
                                <div class="user-cell-dept">Staff ID: HH-EDU-02</div>
                            </div>
                        </div>
                    </td>
                    <td>Education & Pastoral Head<br><span style="font-size: 11px; color: #64748b;">Study Hall & Ward Beta</span></td>
                    <td><span class="vetted-badge"><i class="fas fa-shield-check"></i> PDRM Vetted (2026)</span></td>
                    <td>Mental Health First Aid<br><span style="font-size: 11px; color: #10b981; font-weight: 700;">Valid to Jan 2028</span></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;">On Duty · Study Hall</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-director-review" onclick="openStaffDossier('Ustaz Hakim Bin Salleh', 'Education & Pastoral Head · Study Hall', 'UH')">
                            <i class="fas fa-file-shield"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Staff 4: Chef Razak -->
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#fce7f3; color:#be185d;">CR</div>
                            <div>
                                <div class="user-cell-name">Chef Razak Bin Kassim</div>
                                <div class="user-cell-dept">Staff ID: HH-KIT-01</div>
                            </div>
                        </div>
                    </td>
                    <td>Head of Nutrition & Kitchen<br><span style="font-size: 11px; color: #64748b;">Sanctuary Dining Hall</span></td>
                    <td><span class="vetted-badge"><i class="fas fa-shield-check"></i> PDRM Vetted (2026)</span></td>
                    <td>Typhoid & Food Hygiene Cert<br><span style="font-size: 11px; color: #10b981; font-weight: 700;">Grade A Certified</span></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;">On Duty · Kitchen</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-director-review" onclick="openStaffDossier('Chef Razak Bin Kassim', 'Head of Nutrition & Kitchen', 'CR')">
                            <i class="fas fa-file-shield"></i> PDF
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Active Volunteer Corps Table -->
    <div class="dir-staff-table-card">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-hand-holding-heart" style="color: #059669;"></i>
                <span>Community Volunteers & Hours Verification</span>
            </div>
            <span style="font-size: 12px; color: #64748b;">14 Active Volunteers in Q4 2026</span>
        </div>
        <table class="dir-staff-table">
            <thead>
                <tr>
                    <th>Volunteer Name</th>
                    <th>Profession / Specialization</th>
                    <th>Sanctuary Deployment</th>
                    <th>Logged Hours (Oct)</th>
                    <th>Director Audit</th>
                    <th class="action-column" style="text-align: right;">Approval Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Volunteer 1: Dr. Raymond Lee -->
                <tr>
                    <td><strong>Dr. Raymond Lee</strong><br><span style="font-size: 11px; color: #64748b;">Pediatric Specialist</span></td>
                    <td>Medical & Health Rounds</td>
                    <td>Weekly Clinical Consultations (Sickbay)</td>
                    <td><strong style="color: #4f46e5;">18 Hours</strong></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Hours Approved</span></td>
                    <td style="text-align: right;"><span style="font-size: 11px; color: #64748b;">Verified</span></td>
                </tr>

                <!-- Volunteer 2: Brother David -->
                <tr>
                    <td><strong>Brother David Tan</strong><br><span style="font-size: 11px; color: #64748b;">Secondary Math Teacher</span></td>
                    <td>Academic Tutoring</td>
                    <td>Saturday Math & Science Clinic (Darjah 4-6)</td>
                    <td><strong style="color: #4f46e5;">12 Hours</strong></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Hours Approved</span></td>
                    <td style="text-align: right;"><span style="font-size: 11px; color: #64748b;">Verified</span></td>
                </tr>

                <!-- Volunteer 3: Sarah Farhana -->
                <tr>
                    <td><strong>Sarah Farhana</strong><br><span style="font-size: 11px; color: #64748b;">Dental Officer</span></td>
                    <td>Health Escort</td>
                    <td>Hospital Transport & Dental Escort</td>
                    <td><strong style="color: #d97706;">6 Hours</strong></td>
                    <td><span class="status-pill" style="background:#fef3c7; color:#b45309; font-size:11px; font-weight:700;"><i class="fas fa-clock"></i> Pending Sign-off</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-sign" style="padding: 4px 10px; font-size: 11px;" onclick="approveVolunteerHours('Sarah Farhana', '6', this)">
                            <i class="fas fa-check"></i> Authorize
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@include('portal.director.partials.staff-volunteers-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-staff-volunteers.js') }}"></script>
@endpush
