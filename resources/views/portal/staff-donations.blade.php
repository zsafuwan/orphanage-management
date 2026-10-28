@extends('layouts.portal')

@section('title', 'Staff Donations & Orphanage Funds')
@section('breadcrumb', 'Workspace | Staff Donations')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/donations.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/donations.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/dashboard.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/embedded/portal-staff-donations-1.css') }}?v={{ substr(hash_file('sha256', public_path('css/embedded/portal-staff-donations-1.css')), 0, 12) }}">
@endpush

@section('content')

<!-- Staff Hero Header -->
<div class="staff-donations-hero">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div style="max-width: 680px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; margin-bottom: 10px; letter-spacing: 0.5px; text-transform: uppercase;">
                <i class="fas fa-hand-holding-usd"></i> Staff Front Desk & Ward Fund Operations · Subsystem 5
            </div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                Donations Received, Total Funds Owned & Donor Roster
            </h1>
            <p style="font-size: 13.5px; opacity: 0.92; margin: 0; line-height: 1.5;">
                Staff caregiver overview of incoming monetary contributions, current available cash balances owned for ward child care, and active philanthropic supporters.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <button class="btn-primary" style="background: #ffffff; color: #065f46; border-color: #ffffff; font-weight: 700;" onclick="openStaffDonationModal('modal_staff_record_donation')">
                <i class="fas fa-plus-circle"></i> Record Contribution
            </button>
            <button type="button" class="btn-outline" onclick="openStaffDonationModal('modal_register_donor')"><i class="fas fa-user-plus"></i> Register Donor</button>
        </div>
    </div>
</div>

<!-- Clearance Level Ribbon -->
<div class="rbac-banner" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-check" style="color: #10b981; font-size: 18px;"></i>
        <div>
            <span style="font-size: 12px; font-weight: 700; color: var(--dark); text-transform: uppercase;">Staff Treasury Desk:</span>
            <span style="font-size: 12px; color: var(--dark-muted); margin-left: 4px;">Duty Custodian: <strong style="color: #10b981;">Nurse Mary</strong> · Morning Shift Operations</span>
        </div>
    </div>
    <div style="font-size: 12px; color: #64748b;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
        Audit Status: <strong>All Receipts Serialized & Reconciled with Orphanage Vault</strong>
    </div>
</div>

<!-- ==========================================================================
     CORE KPIS REQUESTED BY USER:
     1. Total Money They Own (Available Treasury Balance)
     2. Number of Donors (Active Benefactors)
     3. Total Donations Count
     4. Average Contribution
     ========================================================================== -->
<div class="metrics-grid" style="margin-bottom: 24px;">
    <!-- 1. Total Money They Own -->
    <div class="metric-card" style="border-left: 4px solid #10b981; background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);">
        <div class="metric-header">
            <span class="metric-label" style="font-weight: 800; color: #065f46;">Total Money Owned</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.16); color: #10b981;">
                <i class="fas fa-vault"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #065f46; font-size: 26px; font-weight: 800;" id="kpi_total_money_owned">$48,650.00</div>
        <div class="metric-sub" style="color: #15803d; font-weight: 600;">
            <i class="fas fa-check-circle"></i> On-hand treasury across ward & child funds
        </div>
    </div>

    <!-- 2. Number of Donors -->
    <div class="metric-card" style="border-left: 4px solid #3b82f6;">
        <div class="metric-header">
            <span class="metric-label" style="font-weight: 800; color: #1e40af;">Number of Donors</span>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #1d4ed8; font-size: 26px; font-weight: 800;" id="kpi_total_donors_count">86 Patrons</div>
        <div class="metric-sub">Foundations, families & community supporters</div>
    </div>

    <!-- 3. Total Donations Received -->
    <div class="metric-card" style="border-left: 4px solid #f59e0b;">
        <div class="metric-header">
            <span class="metric-label">Donation Records</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #d97706; font-size: 26px;" id="kpi_total_donations_count">128 Gifts</div>
        <div class="metric-sub">Monetary wires, cash & in-kind lots</div>
    </div>

    <!-- 4. This Month Giving -->
    <div class="metric-card" style="border-left: 4px solid #8b5cf6;">
        <div class="metric-header">
            <span class="metric-label">Recent Month Inflow</span>
            <div class="metric-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #7c3aed; font-size: 26px;">+$12,450.00</div>
        <div class="metric-sub">98.4% allocated to child welfare</div>
    </div>
</div>

<!-- ==========================================================================
     MONEY OWNED BREAKDOWN (WHERE THE MONEY RESIDES)
     ========================================================================== -->
<div style="margin-bottom: 24px;">
    <h3 style="font-size: 15px; font-weight: 800; color: var(--dark); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-wallet" style="color: #10b981;"></i> Current Balances Owned by Dedicated Care Fund
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
        <div class="fund-allocation-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <strong style="font-size: 13.5px; color: #0f172a;">Child Medical & Dental Care</strong>
                <span class="badge badge-success">$18,450.00</span>
            </div>
            <div style="font-size: 11.5px; color: #64748b;">Emergency surgery, prescription drugs, asthma kits</div>
            <div class="fund-progress-bar">
                <div class="fund-progress-fill" style="width: 38%; background: #10b981;"></div>
            </div>
            <div style="font-size: 11px; color: #10b981; margin-top: 6px; font-weight: 700;">38% of total owned funds</div>
        </div>

        <div class="fund-allocation-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <strong style="font-size: 13.5px; color: #0f172a;">Higher Education & Tuition</strong>
                <span class="badge badge-info">$14,200.00</span>
            </div>
            <div style="font-size: 11.5px; color: #64748b;">School books, exam fees, STEM computer labs</div>
            <div class="fund-progress-bar">
                <div class="fund-progress-fill" style="width: 29%; background: #3b82f6;"></div>
            </div>
            <div style="font-size: 11px; color: #3b82f6; margin-top: 6px; font-weight: 700;">29% of total owned funds</div>
        </div>

        <div class="fund-allocation-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <strong style="font-size: 13.5px; color: #0f172a;">Nutritional Food & Milk Nursery</strong>
                <span class="badge badge-warning">$10,800.00</span>
            </div>
            <div style="font-size: 11.5px; color: #64748b;">Fresh vegetables, milk formula, dietary pantry</div>
            <div class="fund-progress-bar">
                <div class="fund-progress-fill" style="width: 22%; background: #f59e0b;"></div>
            </div>
            <div style="font-size: 11px; color: #f59e0b; margin-top: 6px; font-weight: 700;">22% of total owned funds</div>
        </div>

        <div class="fund-allocation-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <strong style="font-size: 13.5px; color: #0f172a;">Dorm Clothing & Bedding</strong>
                <span class="badge badge-secondary">$5,200.00</span>
            </div>
            <div style="font-size: 11.5px; color: #64748b;">Winter thermal quilts, shoes, school uniforms</div>
            <div class="fund-progress-bar">
                <div class="fund-progress-fill" style="width: 11%; background: #8b5cf6;"></div>
            </div>
            <div style="font-size: 11px; color: #8b5cf6; margin-top: 6px; font-weight: 700;">11% of total owned funds</div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container" style="margin-bottom: 20px;">
    <button class="tab-btn active" data-tab="tab_donations_list">
        <i class="fas fa-list-alt"></i> List of Donations Received (<span id="tab_donations_count">8</span>)
    </button>
    <button class="tab-btn" data-tab="tab_donors_roster">
        <i class="fas fa-users"></i> Registered Donors Roster (<span id="tab_donors_count">6</span>)
    </button>
    <button class="tab-btn" data-tab="tab_cash_log">
        <i class="fas fa-money-bill-wave"></i> Front Desk Cash Inflow Log
    </button>
</div>

<!-- Search & Filter Bar -->
<div class="filter-bar staff-record-filters" style="margin-bottom: 20px;">
    <div class="search-box-wrapper">
        <i class="fas fa-search search-icon" aria-hidden="true"></i>
        <input type="text" id="staff_donation_search" class="search-input" aria-label="Search donations" placeholder="Search by donor name, receipt #, or allocated need..." oninput="renderStaffDonations()">
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_donation_type_filter" class="filter-select" aria-label="Filter contribution type" onchange="renderStaffDonations()">
            <option value="all">All Contribution Types</option>
            <option value="Monetary">Monetary ($ Cash / Bank Wire)</option>
            <option value="In-Kind">In-Kind (Food, Apparel, Goods)</option>
        </select>
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_donation_fund_filter" class="filter-select" aria-label="Filter allocated fund" onchange="renderStaffDonations()">
            <option value="all">All Allocated Funds</option>
            <option value="Child Medical">Child Medical & Dental Care</option>
            <option value="Education">Higher Education & Tuition</option>
            <option value="Nutrition">Nutritional Food & Milk Nursery</option>
            <option value="Clothing">Dorm Clothing & Bedding</option>
        </select>
    </div>
</div>

<!-- ==========================================================================
     TAB 1: LIST OF DONATIONS THEY GET
     ========================================================================== -->
<div class="tab-content-panel" id="tab_donations_list">
    <div class="portal-card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Confirmed Donations</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">
                    Complete record of charitable contributions received at HopeHaven Orphanage.
                </p>
            </div>
            <button class="btn-primary btn-sm" onclick="openStaffDonationModal('modal_staff_record_donation')">
                <i class="fas fa-plus"></i> Record New Donation
            </button>
        </div>

        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Receipt No & Date</th>
                        <th>Donor / Donor</th>
                        <th>Type & Channel</th>
                        <th>Amount / Value</th>
                        <th>Allocated Orphanage Purpose</th>
                        <th>Staff Receiver</th>
                        <th class="action-column staff-receipt-column">Action</th>
                    </tr>
                </thead>
                <tbody id="staff_donations_table_tbody">
                    <!-- Populated dynamically by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 2: DONOR ROSTER (NUMBER OF DONORS DETAILS)
     ========================================================================== -->
<div class="tab-content-panel" id="tab_donors_roster" style="display: none;">
    <div class="portal-card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Registered Benefactors & Supporters</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">
                    Patrons and organizations who regularly contribute to child welfare.
                </p>
            </div>
            <span class="badge badge-success" style="font-size: 12px;" id="roster_badge_count">6 Registered Patrons</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px;" id="staff_donors_grid">
            <!-- Populated dynamically by JavaScript -->
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 3: FRONT DESK CASH INFLOW LOG
     ========================================================================== -->
<div class="tab-content-panel" id="tab_cash_log" style="display: none;">
    <div class="portal-card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Front Gate & Reception Cash Box Log</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">Shift cash donations handovers from visitors & local community members.</p>
            </div>
            <span class="badge badge-info"><i class="fas fa-lock"></i> Petty Cash Vault Reconciled</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;" id="staff_cash_log_list">
            <!-- Populated dynamically -->
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL 1: RECORD RECEIVED DONATION (STAFF INPUT)
     ========================================================================== -->
@include('portal.partials.donation-entry-modals')
@include('portal.partials.donor-registration-modal')
@endsection

@push('scripts')
<script src="{{ asset('js/portal/donation-entry.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/donation-entry.js')), 0, 12) }}"></script>
@endpush
