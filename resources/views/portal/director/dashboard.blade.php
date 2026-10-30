@extends('layouts.portal')

@section('title', 'Director Dashboard')
@section('breadcrumb', 'Director Portal')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/director-dashboard.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/record-actions.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/record-actions.css')), 0, 12) }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Executive Welcome Banner -->
    <div class="director-hero-card portal-dashboard-hero">
        <div class="portal-hero-copy">
        <div>
            <div class="director-hero-badge">
                <i class="fas fa-crown"></i> Orphanage Management · Q4 2026
            </div>
            <h1 class="director-hero-title">Welcome, Sister Sarah</h1>
        </div>
        <div class="director-hero-actions">
            <button type="button" class="director-btn-light" onclick="directEmergencyNotice()">
                <i class="fas fa-bullhorn"></i> Broadcast Directive
            </button>
            <button type="button" class="director-btn-outline" onclick="exportDirectorateReport()">
                <i class="fas fa-file-download"></i> Download Report (PDF)
            </button>
        </div>
        </div>
        @include('portal.partials.workspace-art')
    </div>

    @include('portal.partials.care-shortcuts')

    <!-- 1. Money Available & Sanctuary Treasury Reserves -->
    <div class="treasury-overview-card">
        <div class="treasury-main-balance">
            <span class="treasury-label-tag">
                <i class="fas fa-vault"></i> Available Funds
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
                    <span>Education Fund</span>
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
                    <span>Monthly Spending & Budget</span>
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
                        <span class="chart-stat-lbl">Approved Director Budget</span>
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

        <div class="director-card requisition-actions-panel">
            <div class="director-card-head"><div><h2 class="director-card-title" id="director_inventory_pending_title">Staff Supply Requests</h2><p id="director_inventory_stock_note" class="inventory-panel-note"></p></div><a href="{{ route('director.inventory-requests') }}" class="director-link-action">View Inventory <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
            <div class="director-card-body" id="director_inventory_pending_list" data-review-url="{{ route('director.inventory-requests') }}"></div>
        </div>
    </div>

    <!-- 3. Monthly Students Sickness & Clinic Appointments Log -->
    <div class="director-card" style="margin-bottom: 24px;">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-hospital-user" style="color: #e11d48;"></i>
                <span class="sickness-table-caption">Health & Appointments</span>
            </div>
            <div class="chart-toggle-group">
                <button type="button" class="chart-toggle-btn sickness-tab-btn active" data-sickness-filter="all">
                    All Month Records (6)
                </button>
                <button type="button" class="chart-toggle-btn sickness-tab-btn" data-sickness-filter="sickness">
                    Active Sickness (5)
                </button>
                <button type="button" class="chart-toggle-btn sickness-tab-btn" data-sickness-filter="scheduled">
                    Upcoming Scheduled (2)
                </button>
                <button type="button" class="chart-toggle-btn sickness-tab-btn" data-sickness-filter="attended">
                    Attended / Cleared (4)
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
                    <tr><td colspan="5">Loading shared child registry…</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Donator Proof of Donation & Verification Ledger -->
    <div class="director-card">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-receipt" style="color: #059669;"></i>
                <span>Donation Receipts & Review (October 2026)</span>
            </div>
        </div>
        <div class="table-responsive" tabindex="0" role="region" aria-label="Donation proofs and director audit">
            <table class="donor-table">
                <thead>
                    <tr>
                        <th>Donor Name / Entity</th>
                        <th>Amount Contributed</th>
                        <th>Channel & Reference</th>
                        <th>Designated Purpose</th>
                        <th>Date & Time</th>
                        <th>Payment Receipt</th>
                        <th>Director Review</th>
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
                                <i class="fas fa-file-pdf"></i> View Receipt
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
                                <i class="fas fa-file-pdf"></i> View Receipt
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
                                <i class="fas fa-file-pdf"></i> View Receipt
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
                                <i class="fas fa-file-pdf"></i> View Receipt
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
                                <i class="fas fa-file-pdf"></i> View Receipt
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
    <script src="{{ asset('js/portal/inventory-dashboard.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/inventory-dashboard.js')), 0, 12) }}"></script>
    <!-- Chart.js CDN for Interactive Monthly Spending Graph -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/portal/director-dashboard.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/director-dashboard.js')), 0, 12) }}"></script>
@endpush
