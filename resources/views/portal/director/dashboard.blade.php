@extends('layouts.portal')

@section('title', 'Director Executive Dashboard')
@section('breadcrumb', 'Director Governance')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal/record-actions.css') }}?v={{ filemtime(public_path('css/portal/record-actions.css')) }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Executive Welcome Banner -->
    <div class="director-hero-card">
        <div>
            <div class="director-hero-badge">
                <i class="fas fa-crown"></i> Directorate of Sanctuary Operations · Q4 2026
            </div>
            <h1 class="director-hero-title">Welcome, Sister Sarah</h1>
            <p class="director-hero-subtitle">
                Executive sanctuary governance & financial oversight for HopeHaven. Track real-time treasury reserves, monthly spending patterns, authenticated donor contributions, and clinical student health.
            </p>
        </div>
        <div class="director-hero-actions">
            <button type="button" class="director-btn-light" onclick="directEmergencyNotice()">
                <i class="fas fa-bullhorn"></i> Broadcast Directive
            </button>
            <button type="button" class="director-btn-outline" onclick="exportDirectorateReport()">
                <i class="fas fa-file-download"></i> Directorate Audit (PDF)
            </button>
        </div>
    </div>

    <!-- 1. Money Available & Sanctuary Treasury Reserves -->
    <div class="treasury-overview-card">
        <div class="treasury-main-balance">
            <span class="treasury-label-tag">
                <i class="fas fa-vault"></i> Liquid Treasury Balance
            </span>
            <div class="treasury-balance-amount">RM 148,350.00</div>
            <div class="treasury-sub-desc">
                <i class="fas fa-shield-check" style="color: #059669;"></i>
                <span>Maybank Islamic Trustee A/C #5642-1823-9901 · <strong>6.2 Months</strong> Operating Runway</span>
            </div>
        </div>

        <div class="treasury-fund-pills">
            <!-- Fund 1: General Operations -->
            <div class="fund-pill-box">
                <div class="fund-pill-title">
                    <i class="fas fa-house-chimney-window" style="color: #4f46e5;"></i>
                    <span>Operations & Food</span>
                </div>
                <div class="fund-pill-amount">RM 65,400.00</div>
                <div class="fund-pill-note">44.1% of liquid reserves</div>
            </div>

            <!-- Fund 2: Medical Reserve -->
            <div class="fund-pill-box">
                <div class="fund-pill-title">
                    <i class="fas fa-heart-pulse" style="color: #059669;"></i>
                    <span>Medical & Clinical</span>
                </div>
                <div class="fund-pill-amount" style="color: #059669;">RM 38,200.00</div>
                <div class="fund-pill-note">Emergency hospital pool</div>
            </div>

            <!-- Fund 3: Education Endowment -->
            <div class="fund-pill-box">
                <div class="fund-pill-title">
                    <i class="fas fa-graduation-cap" style="color: #7c3aed;"></i>
                    <span>Education Endowment</span>
                </div>
                <div class="fund-pill-amount" style="color: #7c3aed;">RM 44,750.00</div>
                <div class="fund-pill-note">Tuition & school grants</div>
            </div>
        </div>
    </div>

    <!-- Strategic Directorate KPI Grid -->
    <div class="director-kpi-grid">
        <!-- KPI 1: Students with Sickness This Month -->
        <div class="director-kpi-card">
            <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-rose">
                    <i class="fas fa-head-side-cough"></i>
                </div>
                <span class="kpi-badge kpi-badge-rose">October Watch</span>
            </div>
            <div>
                <div class="kpi-value">5 Students</div>
                <div class="kpi-title">Active Sickness This Month</div>
            </div>
            <div class="kpi-footer-note">
                <i class="fas fa-notes-medical" style="color: #e11d48;"></i> Asthma (2), Fever (1), Skin (2)
            </div>
        </div>

        <!-- KPI 2: Clinic & Hospital Appointments -->
        <div class="director-kpi-card">
            <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-amber">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <span class="kpi-badge kpi-badge-amber">8 Appointments</span>
            </div>
            <div>
                <div class="kpi-value">2 Upcoming</div>
                <div class="kpi-title">Medical Appointments (Month)</div>
            </div>
            <div class="kpi-footer-note">
                <i class="fas fa-check-circle" style="color: #10b981;"></i> 6 Attended & completed in Oct
            </div>
        </div>

        <!-- KPI 3: Month Spending -->
        <div class="director-kpi-card">
            <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-indigo">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <span class="kpi-badge kpi-badge-indigo">82.3% of Budget</span>
            </div>
            <div>
                <div class="kpi-value">RM 24,680</div>
                <div class="kpi-title">Spending This Month (Oct)</div>
            </div>
            <div class="kpi-footer-note">
                <i class="fas fa-arrow-down" style="color: #10b981;"></i> RM 5,320 under RM 30k budget
            </div>
        </div>

        <!-- KPI 4: Verified Donator Inflow -->
        <div class="director-kpi-card">
            <div class="kpi-header">
                <div class="kpi-icon-box kpi-icon-emerald">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <span class="kpi-badge kpi-badge-emerald">100% Tax Deductible</span>
            </div>
            <div>
                <div class="kpi-value">+RM 28,450</div>
                <div class="kpi-title">Verified Donations (Month)</div>
            </div>
            <div class="kpi-footer-note">
                <i class="fas fa-file-invoice" style="color: #059669;"></i> 5 Major bank slips logged
            </div>
        </div>
    </div>

    <!-- 2. Graph of Month Spending & Staff Requisitions -->
    <div class="director-two-col">
        <!-- Left: Graph of Month Spending -->
        <div class="director-card">
            <div class="director-card-head">
                <div class="director-card-title">
                    <i class="fas fa-chart-line"></i>
                    <span>Monthly Expenditure & Sanctuary Budget Graph</span>
                </div>
                <div class="chart-toggle-group">
                    <button type="button" class="chart-toggle-btn active" id="btn_chart_trend" onclick="toggleSpendingChart('trend')">
                        Monthly Trend (6 Mo)
                    </button>
                    <button type="button" class="chart-toggle-btn" id="btn_chart_category" onclick="toggleSpendingChart('category')">
                        Category Breakdown (Oct)
                    </button>
                </div>
            </div>
            <div class="spending-chart-container">
                <div class="chart-summary-strip">
                    <div class="chart-stat-item">
                        <span class="chart-stat-lbl">October Total Expenditure</span>
                        <span class="chart-stat-val" style="color: #4f46e5;">RM 24,680.00</span>
                    </div>
                    <div class="chart-stat-item">
                        <span class="chart-stat-lbl">Approved Directorate Budget</span>
                        <span class="chart-stat-val" style="color: #d97706;">RM 30,000.00</span>
                    </div>
                    <div class="chart-stat-item">
                        <span class="chart-stat-lbl">Monthly Operating Surplus</span>
                        <span class="chart-stat-val" style="color: #059669;">+RM 5,320.00</span>
                    </div>
                </div>

                <div class="canvas-wrapper">
                    <canvas id="directorSpendingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right: Pending Staff Requisitions for Director Sign-off -->
        <div class="director-card requisition-actions-panel">
            <div class="director-card-head">
                <div class="director-card-title">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Pending Staff Requisitions (4)</span>
                </div>
                <a href="{{ route('director.inventory-requests') }}" class="director-link-action">
                    Review All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="director-card-body">
                <!-- Item 1: Pediatric Nebulizers -->
                <div class="req-item-card">
                    <div class="req-left-info">
                        <div class="req-avatar">NM</div>
                        <div>
                            <div class="req-meta-title">Pediatric Nebulizers & Inhalers (2 units)</div>
                            <div class="req-meta-sub">
                                <span>Nurse Mary · Ward Alpha</span>
                                <span>·</span>
                                <span class="req-priority-tag req-priority-critical">Critical</span>
                                <span>·</span>
                                <strong>RM 680</strong>
                            </div>
                        </div>
                    </div>
                    <div class="req-actions">
                        <button type="button" class="btn-director-approve btn-director-quick-approve" data-req-id="REQ-2026-104" data-req-item="Pediatric Nebulizers">
                            <i class="fas fa-signature"></i> Sign
                        </button>
                    </div>
                </div>

                <!-- Item 2: Secondary School Workbooks -->
                <div class="req-item-card">
                    <div class="req-left-info">
                        <div class="req-avatar" style="background:#e0f2fe; color:#0369a1;">UH</div>
                        <div>
                            <div class="req-meta-title">Darjah 4-6 Mathematics & Science (30 sets)</div>
                            <div class="req-meta-sub">
                                <span>Ustaz Hakim · Study Hall</span>
                                <span>·</span>
                                <span class="req-priority-tag req-priority-high">High</span>
                                <span>·</span>
                                <strong>RM 1,240</strong>
                            </div>
                        </div>
                    </div>
                    <div class="req-actions">
                        <button type="button" class="btn-director-approve btn-director-quick-approve" data-req-id="REQ-2026-102" data-req-item="Workbooks">
                            <i class="fas fa-signature"></i> Sign
                        </button>
                    </div>
                </div>

                <!-- Item 3: Hypoallergenic Formula -->
                <div class="req-item-card">
                    <div class="req-left-info">
                        <div class="req-avatar" style="background:#fef3c7; color:#b45309;">CL</div>
                        <div>
                            <div class="req-meta-title">Lactose-Free Formula & Diapers Pack</div>
                            <div class="req-meta-sub">
                                <span>Caregiver Lisa · Nursery</span>
                                <span>·</span>
                                <span class="req-priority-tag req-priority-critical">Critical</span>
                                <span>·</span>
                                <strong>RM 820</strong>
                            </div>
                        </div>
                    </div>
                    <div class="req-actions">
                        <button type="button" class="btn-director-approve btn-director-quick-approve" data-req-id="REQ-2026-098" data-req-item="Baby Formula">
                            <i class="fas fa-signature"></i> Sign
                        </button>
                    </div>
                </div>

                <!-- Item 4: Rice Cooker Element -->
                <div class="req-item-card">
                    <div class="req-left-info">
                        <div class="req-avatar" style="background:#fce7f3; color:#be185d;">CR</div>
                        <div>
                            <div class="req-meta-title">Commercial 20L Rice Cooker Element</div>
                            <div class="req-meta-sub">
                                <span>Chef Razak · Kitchen</span>
                                <span>·</span>
                                <span class="req-priority-tag req-priority-high">High</span>
                                <span>·</span>
                                <strong>RM 680</strong>
                            </div>
                        </div>
                    </div>
                    <div class="req-actions">
                        <button type="button" class="btn-director-approve btn-director-quick-approve" data-req-id="REQ-2026-095" data-req-item="Rice Cooker Element">
                            <i class="fas fa-signature"></i> Sign
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Monthly Students Sickness & Clinic Appointments Log -->
    <div class="director-card" style="margin-bottom: 24px;">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-hospital-user" style="color: #e11d48;"></i>
                <span>Monthly Students Sickness & Clinic Appointments (October 2026)</span>
            </div>
            <div class="chart-toggle-group">
                <button type="button" class="chart-toggle-btn sickness-tab-btn active" data-sickness-filter="all">
                    All Month Records (8)
                </button>
                <button type="button" class="chart-toggle-btn sickness-tab-btn" data-sickness-filter="sickness">
                    Active Sickness (5)
                </button>
                <button type="button" class="chart-toggle-btn sickness-tab-btn" data-sickness-filter="scheduled">
                    Upcoming Scheduled (2)
                </button>
                <button type="button" class="chart-toggle-btn sickness-tab-btn" data-sickness-filter="attended">
                    Attended / Cleared (6)
                </button>
            </div>
        </div>
        <div class="table-responsive" tabindex="0" role="region" aria-label="Monthly sickness and clinic appointments">
            <table class="sickness-table">
                <thead>
                    <tr>
                        <th scope="col">Child / Student</th>
                        
                        
                        <th scope="col">Attending Doctor & Clinic</th>
                        <th scope="col">Appointment Date & Time</th>
                        <th scope="col">Status for Month</th>
                        <th class="action-column" scope="col" style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Student 1: Ahmad Danial (Active Asthma, Upcoming Appointment) -->
                    <tr data-record-type="scheduled" data-has-sickness="true">
                        <td>
                            <div class="child-profile-cell">
                                <div class="child-avatar-box child-avatar-boy">AD</div>
                                <div>
                                    <div class="child-name">Ahmad Danial Bin Razif</div>
                                    <div class="child-meta">MyKid: 150823-10-6123</div>
                                </div>
                            </div>
                        </td>
                        
                        
                        <td>Dr. Raymond Lee<br><span style="font-size: 11px; color: #64748b;">HKL Pediatric Pulmonology</span></td>
                        <td><strong>Oct 25, 2026</strong><br><span style="font-size: 11px; color: #d97706;">02:30 PM (This Friday)</span></td>
                        <td>
                            <span class="appt-status-tag scheduled">
                                <i class="fas fa-clock"></i> Upcoming Appointment
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('director.medical-records') }}" class="btn-director-review" style="padding: 10px 18px; font-size: 14px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-file-medical"></i> PDF
                            </a>
                        </td>
                    </tr>

                    <!-- Student 2: Aminah (Upcoming Dental & Penicillin Review) -->
                    <tr data-record-type="scheduled" data-has-sickness="true">
                        <td>
                            <div class="child-profile-cell">
                                <div class="child-avatar-box child-avatar-girl">AB</div>
                                <div>
                                    <div class="child-name">Aminah Binti Abdullah</div>
                                    <div class="child-meta">MyKid: 160412-14-5582</div>
                                </div>
                            </div>
                        </td>
                        
                        
                        <td>Volunteer Dental Officer<br><span style="font-size: 11px; color: #64748b;">Klinik Kesihatan Titiwangsa</span></td>
                        <td><strong>Oct 24, 2026</strong><br><span style="font-size: 11px; color: #d97706;">10:30 AM (Tomorrow)</span></td>
                        <td>
                            <span class="appt-status-tag scheduled">
                                <i class="fas fa-clock"></i> Upcoming Appointment
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('director.medical-records') }}" class="btn-director-review" style="padding: 10px 18px; font-size: 14px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-file-medical"></i> PDF
                            </a>
                        </td>
                    </tr>

                    <!-- Student 3: Rayyan Iskandar (Lactose Rash, Attended) -->
                    <tr data-record-type="attended" data-has-sickness="true">
                        <td>
                            <div class="child-profile-cell">
                                <div class="child-avatar-box child-avatar-baby">RI</div>
                                <div>
                                    <div class="child-name">Rayyan Iskandar</div>
                                    <div class="child-meta">MyKid: 240219-14-1109</div>
                                </div>
                            </div>
                        </td>
                        
                        
                        <td>Dr. Norazlina<br><span style="font-size: 11px; color: #64748b;">Klinik Kesihatan Setapak</span></td>
                        <td>Oct 18, 2026<br><span style="font-size: 11px; color: #64748b;">11:00 AM</span></td>
                        <td>
                            <span class="appt-status-tag attended">
                                <i class="fas fa-check-circle"></i> Attended · Recovering
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('director.medical-records') }}" class="btn-director-review" style="padding: 10px 18px; font-size: 14px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-file-medical"></i> PDF
                            </a>
                        </td>
                    </tr>

                    <!-- Student 4: Siti Nurhaliza (Eczema, Attended) -->
                    <tr data-record-type="attended" data-has-sickness="true">
                        <td>
                            <div class="child-profile-cell">
                                <div class="child-avatar-box child-avatar-girl">SN</div>
                                <div>
                                    <div class="child-name">Siti Nurhaliza Binti Yusof</div>
                                    <div class="child-meta">MyKid: 110905-14-8840</div>
                                </div>
                            </div>
                        </td>
                        
                        
                        <td>Dr. Wong Siew May<br><span style="font-size: 11px; color: #64748b;">HKL Dermatology Clinic</span></td>
                        <td>Oct 12, 2026<br><span style="font-size: 11px; color: #64748b;">03:00 PM</span></td>
                        <td>
                            <span class="appt-status-tag attended">
                                <i class="fas fa-check-circle"></i> Attended · Controlled
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('director.medical-records') }}" class="btn-director-review" style="padding: 10px 18px; font-size: 14px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-file-medical"></i> PDF
                            </a>
                        </td>
                    </tr>

                    <!-- Student 5: Daniel Arif (Viral Flu, Healed) -->
                    <tr data-record-type="attended" data-has-sickness="true">
                        <td>
                            <div class="child-profile-cell">
                                <div class="child-avatar-box child-avatar-boy">DA</div>
                                <div>
                                    <div class="child-name">Daniel Arif Bin Rosli</div>
                                    <div class="child-meta">MyKid: 120401-14-7221</div>
                                </div>
                            </div>
                        </td>
                        
                        
                        <td>Medical Officer on Call<br><span style="font-size: 11px; color: #64748b;">Klinik Titiwangsa</span></td>
                        <td>Oct 06, 2026<br><span style="font-size: 11px; color: #64748b;">09:00 AM</span></td>
                        <td>
                            <span class="appt-status-tag attended">
                                <i class="fas fa-check-circle"></i> Attended · Cleared
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('director.medical-records') }}" class="btn-director-review" style="padding: 10px 18px; font-size: 14px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-file-medical"></i> PDF
                            </a>
                        </td>
                    </tr>

                    <!-- Student 6: Adam Harith (Sports Sprain & Allergy, Attended) -->
                    <tr data-record-type="attended" data-has-sickness="false">
                        <td>
                            <div class="child-profile-cell">
                                <div class="child-avatar-box child-avatar-boy">AH</div>
                                <div>
                                    <div class="child-name">Adam Harith Bin Zulkifli</div>
                                    <div class="child-meta">MyKid: 130310-10-4491</div>
                                </div>
                            </div>
                        </td>
                        
                        
                        <td>Physiotherapy Dept<br><span style="font-size: 11px; color: #64748b;">Hospital Kuala Lumpur</span></td>
                        <td>Oct 03, 2026<br><span style="font-size: 11px; color: #64748b;">10:00 AM</span></td>
                        <td>
                            <span class="appt-status-tag attended">
                                <i class="fas fa-check-circle"></i> Attended · Healed
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('director.medical-records') }}" class="btn-director-review" style="padding: 10px 18px; font-size: 14px; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-file-medical"></i> PDF
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Donator Proof of Donation & Verification Ledger -->
    <div class="director-card">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-receipt" style="color: #059669;"></i>
                <span>Donator Proof of Donation & Audit Verification (October 2026)</span>
            </div>
            <a href="{{ route('donations') }}" class="director-link-action">
                Full Donations Ledger <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div>
            <table class="donor-table">
                <thead>
                    <tr>
                        <th>Donator Name / Entity</th>
                        <th>Amount Contributed</th>
                        <th>Channel & Reference</th>
                        <th>Designated Purpose</th>
                        <th>Date & Time</th>
                        <th>Proof of Transfer Slip</th>
                        <th>Director Audit</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Donor 1: Tan Sri Dr. Jeffrey Cheah -->
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="donor-avatar">JC</div>
                                <div>
                                    <strong>Tan Sri Dr. Jeffrey Cheah</strong>
                                    <div style="font-size: 11px; color: #64748b;">Sunway Care Philanthropy</div>
                                </div>
                            </div>
                        </td>
                        <td><strong style="color: #059669; font-size: 14px;">RM 10,000.00</strong></td>
                        <td>Maybank DuitNow<br><span style="font-size: 11px; font-family: monospace; color: #475569;">#MBB-20261019-9821</span></td>
                        <td>Emergency Pediatric Medical Care</td>
                        <td>19 Oct 2026<br><span style="font-size: 11px; color: #64748b;">02:22 PM</span></td>
                        <td>
                            <button type="button" class="proof-pill-btn" onclick="openDonationProofModal('Tan Sri Dr. Jeffrey Cheah', 'RM 10,000.00', 'Maybank Islamic Berhad', 'MBB-20261019-9821', '19 Oct 2026, 14:22:18 MYT', 'Emergency Pediatric Medical Care Fund', 'Verified')">
                                <i class="fas fa-file-pdf"></i> View Proof Slip
                            </button>
                        </td>
                        <td class="donor-status-cell">
                            <span class="status-pill" style="background:#ecfdf5; color:#047857; font-size: 11px; font-weight: 700;">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        </td>
                    </tr>

                    <!-- Donor 2: Yayasan Hasanah -->
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="donor-avatar" style="background:#e0e7ff; color:#4338ca;">YH</div>
                                <div>
                                    <strong>Yayasan Hasanah Grants</strong>
                                    <div style="font-size: 11px; color: #64748b;">Khazanah Nasional Trust</div>
                                </div>
                            </div>
                        </td>
                        <td><strong style="color: #059669; font-size: 14px;">RM 8,000.00</strong></td>
                        <td>Direct Interbank Wire<br><span style="font-size: 11px; font-family: monospace; color: #475569;">#HASANAH-GRT-26</span></td>
                        <td>Children Secondary SPM Scholarships</td>
                        <td>15 Oct 2026<br><span style="font-size: 11px; color: #64748b;">11:45 AM</span></td>
                        <td>
                            <button type="button" class="proof-pill-btn" onclick="openDonationProofModal('Yayasan Hasanah Grants', 'RM 8,000.00', 'Bank Islam Malaysia', 'HASANAH-GRT-26', '15 Oct 2026, 11:45:00 MYT', 'Secondary & SPM Exam Grants', 'Verified')">
                                <i class="fas fa-file-pdf"></i> View Proof Slip
                            </button>
                        </td>
                        <td class="donor-status-cell">
                            <span class="status-pill" style="background:#ecfdf5; color:#047857; font-size: 11px; font-weight: 700;">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        </td>
                    </tr>

                    <!-- Donor 3: Hajah Faridah Binti Mansor -->
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="donor-avatar" style="background:#fef3c7; color:#b45309;">FM</div>
                                <div>
                                    <strong>Hajah Faridah Binti Mansor</strong>
                                    <div style="font-size: 11px; color: #64748b;">Private Contributor</div>
                                </div>
                            </div>
                        </td>
                        <td><strong style="color: #059669; font-size: 14px;">RM 3,500.00</strong></td>
                        <td>DuitNow QR Transfer<br><span style="font-size: 11px; font-family: monospace; color: #475569;">#DN-881920-KUL</span></td>
                        <td>Study Hall Exercise Books & Tutoring</td>
                        <td>14 Oct 2026<br><span style="font-size: 11px; color: #64748b;">09:15 AM</span></td>
                        <td>
                            <button type="button" class="proof-pill-btn" onclick="openDonationProofModal('Hajah Faridah Binti Mansor', 'RM 3,500.00', 'CIMB Clicks', 'DN-881920-KUL', '14 Oct 2026, 09:15:30 MYT', 'Study Hall Workbooks & Tutoring', 'Verified')">
                                <i class="fas fa-file-pdf"></i> View Proof Slip
                            </button>
                        </td>
                        <td class="donor-status-cell">
                            <span class="status-pill" style="background:#ecfdf5; color:#047857; font-size: 11px; font-weight: 700;">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        </td>
                    </tr>

                    <!-- Donor 4: Dr. Raymond Lee & Associates -->
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="donor-avatar" style="background:#fce7f3; color:#be185d;">RL</div>
                                <div>
                                    <strong>Dr. Raymond Lee & Clinic</strong>
                                    <div style="font-size: 11px; color: #64748b;">Medical Partner Community Fund</div>
                                </div>
                            </div>
                        </td>
                        <td><strong style="color: #059669; font-size: 14px;">RM 2,500.00</strong></td>
                        <td>Cheque Deposit<br><span style="font-size: 11px; font-family: monospace; color: #475569;">#MBB-CHK-00421</span></td>
                        <td>Infant Formula & Pharmacy Supplies</td>
                        <td>10 Oct 2026<br><span style="font-size: 11px; color: #64748b;">04:10 PM</span></td>
                        <td>
                            <button type="button" class="proof-pill-btn" onclick="openDonationProofModal('Dr. Raymond Lee & Associates', 'RM 2,500.00', 'Maybank Berhad', 'MBB-CHK-00421', '10 Oct 2026, 16:10:00 MYT', 'Infant Formula & Pharmacy Stock', 'Verified')">
                                <i class="fas fa-file-pdf"></i> View Proof Slip
                            </button>
                        </td>
                        <td class="donor-status-cell">
                            <span class="status-pill" style="background:#ecfdf5; color:#047857; font-size: 11px; font-weight: 700;">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        </td>
                    </tr>

                    <!-- Donor 5: Keluarga Haji Azman (Pending Review) -->
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="donor-avatar" style="background:#fff1f2; color:#e11d48;">HA</div>
                                <div>
                                    <strong>Keluarga Haji Azman</strong>
                                    <div style="font-size: 11px; color: #64748b;">Friday Kitchen Sadaqah</div>
                                </div>
                            </div>
                        </td>
                        <td><strong style="color: #059669; font-size: 14px;">RM 1,200.00</strong></td>
                        <td>Instant Online FPX<br><span style="font-size: 11px; font-family: monospace; color: #475569;">#CIMB-991203-KUL</span></td>
                        <td>Communal Friday Lunch & Provisions</td>
                        <td>Yesterday<br><span style="font-size: 11px; color: #64748b;">06:40 PM</span></td>
                        <td>
                            <button type="button" class="proof-pill-btn" onclick="openDonationProofModal('Keluarga Haji Azman', 'RM 1,200.00', 'CIMB Bank', 'CIMB-991203-KUL', '22 Oct 2026, 18:40:12 MYT', 'Communal Friday Lunch & Provisions', 'Pending Review')">
                                <i class="fas fa-file-pdf"></i> View Proof Slip
                            </button>
                        </td>
                        <td class="donor-status-cell">
                            <button type="button" class="btn-verify-ack" onclick="verifyDonation('Keluarga Haji Azman', 'RM 1,200.00', this)">
                                <i class="fas fa-stamp"></i> Verify & Sign
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Dedicated Modals Partial -->
@include('portal.director.partials.dashboard-modals')
@endsection

@push('scripts')
    <!-- Chart.js CDN for Interactive Monthly Spending Graph -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/portal/director-dashboard.js') }}"></script>
@endpush
