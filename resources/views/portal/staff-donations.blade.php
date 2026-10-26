@extends('layouts.portal')

@section('title', 'Staff Donations & Sanctuary Funds')
@section('breadcrumb', 'Workspace | Staff Donations')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/donations.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}">
    <style>
        .staff-donations-hero {
            background: linear-gradient(135deg, #065f46 0%, #047857 55%, #059669 100%);
            color: #ffffff;
            border-radius: var(--radius-md);
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 18px rgba(6, 95, 70, 0.18);
            position: relative;
            overflow: hidden;
        }

        .staff-donations-hero::after {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .fund-allocation-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .fund-allocation-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .fund-progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
        }

        .fund-progress-fill {
            height: 100%;
            border-radius: 4px;
        }
    </style>
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
                <i class="fas fa-plus-circle"></i> Record Received Donation
            </button>
            <a href="{{ route('donations') }}" class="btn-outline" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border-color: rgba(255, 255, 255, 0.35); font-weight: 700;" title="Switch to Admin Multi-Orphanage Cash Flow Comparison">
                <i class="fas fa-chart-line"></i> Admin Cash Flow Benchmark
            </a>
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
        Audit Status: <strong>All Receipts Serialized & Reconciled with Sanctuary Vault</strong>
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
        <div class="metric-sub">98.4% earmarked to child welfare</div>
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
<div class="filter-bar" style="margin-bottom: 20px;">
    <div class="search-box-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="staff_donation_search" class="search-input" placeholder="Search by donor name, receipt #, or allocated need..." oninput="renderStaffDonations()">
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_donation_type_filter" class="filter-select" onchange="renderStaffDonations()">
            <option value="all">All Contribution Types</option>
            <option value="Monetary">Monetary ($ Cash / Bank Wire)</option>
            <option value="In-Kind">In-Kind (Food, Apparel, Goods)</option>
        </select>
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_donation_fund_filter" class="filter-select" onchange="renderStaffDonations()">
            <option value="all">All Earmarked Funds</option>
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
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Verified Donations Received Ledger</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">
                    Complete record of charitable contributions received at HopeHaven Sanctuary.
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
                        <th>Donor / Benefactor</th>
                        <th>Type & Channel</th>
                        <th>Amount / Valuation</th>
                        <th>Allocated Sanctuary Purpose</th>
                        <th>Staff Receiver</th>
                        <th class="action-column" style="text-align: right;">Action</th>
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
<div id="modal_staff_record_donation" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 540px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Record Received Donation</h3>
                <p class="modal-sub">Log cash, bank transfer, or in-kind supplies received by staff.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffDonationModal('modal_staff_record_donation')">&times;</button>
        </div>

        <form onsubmit="handleStaffDonationSubmit(event)">
            <div class="form-group">
                <label class="form-label">Donor / Contributor Name *</label>
                <input type="text" id="staff_don_name" class="form-control" placeholder="e.g. Mr. Robert Chen / Al-Barakah Community Trust" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contribution Type *</label>
                    <select id="staff_don_type" class="form-control" onchange="toggleDonationTypeFields()" required>
                        <option value="Monetary">Monetary ($ Cash / Transfer)</option>
                        <option value="In-Kind">In-Kind (Material Goods)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount ($ USD) / Fair Valuation *</label>
                    <input type="number" id="staff_don_amount" class="form-control" value="250.00" min="1" step="0.5" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Payment Channel / Receipt Method *</label>
                    <select id="staff_don_channel" class="form-control" required>
                        <option value="Cash at Front Desk">Cash at Front Desk</option>
                        <option value="Direct Bank Wire">Direct Bank Wire</option>
                        <option value="Cheque Deposit">Cheque Deposit</option>
                        <option value="Online Payment Gateway">Online Payment Gateway</option>
                        <option value="Hand-Delivered Goods">Hand-Delivered Goods</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Earmarked Sanctuary Fund *</label>
                    <select id="staff_don_fund" class="form-control" required>
                        <option value="Child Medical & Dental Care">Child Medical & Dental Care</option>
                        <option value="Higher Education & Tuition">Higher Education & Tuition</option>
                        <option value="Nutritional Food & Milk Nursery">Nutritional Food & Milk Nursery</option>
                        <option value="Dorm Clothing & Bedding">Dorm Clothing & Bedding</option>
                        <option value="General Sanctuary Operations">General Sanctuary Operations</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Donor Contact (Email or Phone)</label>
                <input type="text" id="staff_don_contact" class="form-control" placeholder="e.g. robert.chen@mail.com / +60 12-388 9911">
            </div>

            <div class="form-group">
                <label class="form-label">Staff Duty Notes & Acknowledgement Remarks</label>
                <textarea id="staff_don_notes" class="form-control" rows="2" placeholder="e.g. Received at morning shift gate. Official paper receipt # handed to donor..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffDonationModal('modal_staff_record_donation')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #10b981; border-color: #10b981;">
                    <i class="fas fa-check"></i> Record & Issue Official E-Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================================
     MODAL 2: VIEW / PRINT OFFICIAL RECEIPT
     ========================================================================== -->
<div id="modal_staff_view_receipt" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 540px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Official Donation Receipt</h3>
                <p class="modal-sub" id="receipt_modal_ref">Receipt No: RCP-2026-0881</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffDonationModal('modal_staff_view_receipt')">&times;</button>
        </div>

        <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px; background: #fafbfc;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 800; color: #065f46; margin: 0 0 2px 0;">HopeHaven Sanctuary</h4>
                    <span style="font-size: 11px; color: #64748b;">Registered Non-Profit Sanctuary &bull; Tax Exemption Reg #MY-99482</span>
                </div>
                <div style="text-align: right; font-size: 12px;">
                    <div id="receipt_modal_date">Date: Oct 20, 2026</div>
                    <span class="badge badge-success">Official Receipt</span>
                </div>
            </div>

            <div style="font-size: 12.5px; display: flex; flex-direction: column; gap: 8px;">
                <div>Received from: <strong id="receipt_modal_donor">The Meridian Heritage Foundation</strong></div>
                <div>Amount Recognized: <strong style="font-size: 18px; color: #065f46;" id="receipt_modal_amount">$5,000.00 USD</strong></div>
                <div>Payment Method: <strong id="receipt_modal_method">Direct Bank Wire</strong></div>
                <div>Allocated Fund: <strong id="receipt_modal_fund">Child Medical & Dental Care</strong></div>
                <div>Receiver: <strong id="receipt_modal_staff">Nurse Mary (Duty Staff Caregiver)</strong></div>
            </div>

            <div style="margin-top: 18px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b; font-style: italic;">
                "HopeHaven certifies that this contribution has been officially banked into the dedicated sanctuary escrow account. Thank you for empowering our residential children."
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-outline" onclick="closeStaffDonationModal('modal_staff_view_receipt')">Close</button>
            <button type="button" class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Official Receipt</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const STAFF_DONATIONS_STORAGE_KEY = 'hopehaven_staff_donations_v1';

    // Seed Data
    const DEFAULT_STAFF_DONATIONS = [
        {
            receiptNo: 'RCP-2026-0881',
            date: '2026-10-20',
            donor: 'The Meridian Heritage Foundation',
            type: 'Monetary',
            amount: 5000.00,
            channel: 'Direct Bank Wire',
            fund: 'Child Medical & Dental Care',
            staff: 'Nurse Mary',
            notes: 'Quarterly pediatric healthcare endowment contribution.'
        },
        {
            receiptNo: 'RCP-2026-0882',
            date: '2026-10-18',
            donor: 'Dr. Gregory & Eleanor Vance',
            type: 'Monetary',
            amount: 2500.00,
            channel: 'Cheque Deposit',
            fund: 'Higher Education & Tuition',
            staff: 'Sister Grace',
            notes: 'Secondary school scholarship grant for Elijah and Maya.'
        },
        {
            receiptNo: 'RCP-2026-0883',
            date: '2026-10-15',
            donor: 'Evergreen Food Staples Ltd',
            type: 'In-Kind',
            amount: 1400.00,
            channel: 'Hand-Delivered Goods',
            fund: 'Nutritional Food & Milk Nursery',
            staff: 'Nurse Mary',
            notes: '15 sacks jasmine rice, 20 cans formula milk delivered to kitchen.'
        },
        {
            receiptNo: 'RCP-2026-0884',
            date: '2026-10-14',
            donor: 'Rotary Club Titiwangsa',
            type: 'Monetary',
            amount: 1800.00,
            channel: 'Cash at Front Desk',
            fund: 'Child Medical & Dental Care',
            staff: 'Nurse Mary',
            notes: 'Special donation handed over during Saturday charity visit.'
        },
        {
            receiptNo: 'RCP-2026-0885',
            date: '2026-10-10',
            donor: 'Puan Sri Halimah & Family',
            type: 'Monetary',
            amount: 850.00,
            channel: 'Online Payment Gateway',
            fund: 'Dorm Clothing & Bedding',
            staff: 'Brother Thomas',
            notes: 'Winter fleece blankets and footwear allowance for boys hall.'
        },
        {
            receiptNo: 'RCP-2026-0886',
            date: '2026-10-08',
            donor: 'Beacon Medical Logistics',
            type: 'In-Kind',
            amount: 1200.00,
            channel: 'Hand-Delivered Goods',
            fund: 'Child Medical & Dental Care',
            staff: 'Nurse Mary',
            notes: 'Pediatric first aid supplies, gauze packs, and thermometer units.'
        },
        {
            receiptNo: 'RCP-2026-0887',
            date: '2026-10-05',
            donor: 'St. Jude Alumni Association',
            type: 'Monetary',
            amount: 1100.00,
            channel: 'Direct Bank Wire',
            fund: 'Higher Education & Tuition',
            staff: 'Sister Grace',
            notes: 'Annual school stationery and computer lab maintenance fund.'
        },
        {
            receiptNo: 'RCP-2026-0888',
            date: '2026-10-02',
            donor: 'Local Community Care Circle',
            type: 'Monetary',
            amount: 450.00,
            channel: 'Cash at Front Desk',
            fund: 'Nutritional Food & Milk Nursery',
            staff: 'Nurse Mary',
            notes: 'Collected from weekend community farmers market bake sale.'
        }
    ];

    const DEFAULT_DONORS = [
        { name: 'The Meridian Heritage Foundation', tier: 'Platinum Patron', total: '$45,000.00', contact: 'giving@meridian.org', category: 'Foundation' },
        { name: 'Dr. Gregory & Eleanor Vance', tier: 'Gold Benefactor', total: '$18,500.00', contact: 'vance.family@net.com', category: 'Individual' },
        { name: 'Evergreen Food Staples Ltd', tier: 'Corporate Sponsor', total: '$12,400.00', contact: 'csr@evergreen.com', category: 'Corporate' },
        { name: 'Rotary Club Titiwangsa', tier: 'Community Circle', total: '$8,200.00', contact: 'secretary@rotarykl.org', category: 'Civic Club' },
        { name: 'Puan Sri Halimah & Family', tier: 'Silver Friend', total: '$4,800.00', contact: 'halimah.charity@my.org', category: 'Individual' },
        { name: 'Beacon Medical Logistics', tier: 'Healthcare Partner', total: '$6,500.00', contact: 'supplies@beaconmed.com', category: 'Corporate' }
    ];

    const StaffDonationsStore = {
        getAll: function() {
            try {
                const data = localStorage.getItem(STAFF_DONATIONS_STORAGE_KEY);
                return data ? JSON.parse(data) : DEFAULT_STAFF_DONATIONS;
            } catch (e) {
                return DEFAULT_STAFF_DONATIONS;
            }
        },
        saveAll: function(list) {
            localStorage.setItem(STAFF_DONATIONS_STORAGE_KEY, JSON.stringify(list));
        }
    };

    function openStaffDonationModal(id) {
        $('#' + id).css('display', 'flex').hide().fadeIn(200);
    }

    function closeStaffDonationModal(id) {
        $('#' + id).fadeOut(150);
    }

    function renderStaffDonations() {
        const list = StaffDonationsStore.getAll();
        const search = ($('#staff_donation_search').val() || '').toLowerCase().trim();
        const typeFilter = $('#staff_donation_type_filter').val() || 'all';
        const fundFilter = $('#staff_donation_fund_filter').val() || 'all';

        // 1. Calculate Metrics
        let totalMoney = 34350.00; // Base treasury
        const donorsSet = new Set();
        DEFAULT_DONORS.forEach(d => donorsSet.add(d.name));

        list.forEach(item => {
            if (item.type === 'Monetary') {
                totalMoney += parseFloat(item.amount) || 0;
            }
            if (item.donor) {
                donorsSet.add(item.donor);
            }
        });

        $('#kpi_total_money_owned').text('$' + totalMoney.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD');
        $('#kpi_total_donors_count').text(donorsSet.size + ' Patrons');
        $('#kpi_total_donations_count').text(list.length + ' Gifts');
        $('#tab_donations_count').text(list.length);
        $('#tab_donors_count').text(donorsSet.size);

        // 2. Filter Table Items
        const filtered = list.filter(item => {
            const matchesSearch = !search || 
                item.donor.toLowerCase().includes(search) || 
                item.receiptNo.toLowerCase().includes(search) || 
                item.fund.toLowerCase().includes(search);
            const matchesType = typeFilter === 'all' || item.type === typeFilter;
            const matchesFund = fundFilter === 'all' || item.fund.toLowerCase().includes(fundFilter.toLowerCase());
            return matchesSearch && matchesType && matchesFund;
        });

        let tableHtml = '';
        if (filtered.length === 0) {
            tableHtml = '<tr><td colspan="7" style="text-align: center; padding: 30px; color: #94a3b8;">No donation records matching your filter criteria.</td></tr>';
        } else {
            filtered.forEach((d, idx) => {
                tableHtml += `
                    <tr>
                        <td>
                            <strong style="color: #0f172a; font-family: monospace;">${d.receiptNo}</strong>
                            <div style="font-size: 11px; color: #64748b;">${d.date}</div>
                        </td>
                        <td>
                            <strong style="color: #0f172a; font-size: 13.5px;">${escapeHtml(d.donor)}</strong>
                            <div style="font-size: 11px; color: #64748b;">${escapeHtml(d.notes || 'Verified contribution')}</div>
                        </td>
                        <td>
                            <span class="badge ${d.type === 'Monetary' ? 'badge-success' : 'badge-warning'}">${d.type}</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">${escapeHtml(d.channel)}</div>
                        </td>
                        <td>
                            <strong style="font-size: 14.5px; color: #065f46;">$${parseFloat(d.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight: 700; color: #1e40af;">${escapeHtml(d.fund)}</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary" style="font-size: 11px;">
                                <i class="fas fa-user-check"></i> ${escapeHtml(d.staff)}
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button class="table-action-btn" onclick="viewDonationReceipt('${d.receiptNo}')" title="View Official Receipt" style="color: #10b981;">
                                <i class="fas fa-file-invoice-dollar"></i> E-Receipt
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#staff_donations_table_tbody').html(tableHtml);

        // 3. Render Donors Roster Cards
        renderDonorsRoster();

        // 4. Render Cash Log
        renderCashLog(list);
    }

    function renderDonorsRoster() {
        let gridHtml = '';
        DEFAULT_DONORS.forEach(d => {
            gridHtml += `
                <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <span class="badge badge-info">${d.category}</span>
                        <span class="badge badge-success">${d.tier}</span>
                    </div>
                    <h4 style="font-size: 14.5px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">${escapeHtml(d.name)}</h4>
                    <div style="font-size: 11.5px; color: #64748b; margin-bottom: 12px;">Contact: ${escapeHtml(d.contact)}</div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                        <span style="font-size: 11px; color: #64748b; font-weight: 600;">Lifetime Giving:</span>
                        <strong style="color: #065f46; font-size: 14px;">${d.total}</strong>
                    </div>
                </div>
            `;
        });
        $('#staff_donors_grid').html(gridHtml);
    }

    function renderCashLog(list) {
        const cashItems = list.filter(d => d.channel === 'Cash at Front Desk');
        let html = '';
        if (cashItems.length === 0) {
            html = '<div style="padding: 20px; text-align: center; color: #94a3b8;">No front desk cash donations logged yet.</div>';
        } else {
            cashItems.forEach(c => {
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div>
                            <strong style="color: #0f172a; font-size: 13.5px;">${escapeHtml(c.donor)} &bull; $${parseFloat(c.amount).toFixed(2)} USD</strong>
                            <div style="font-size: 11.5px; color: #64748b;">${c.date} &bull; Received by ${escapeHtml(c.staff)} &bull; Purpose: ${escapeHtml(c.fund)}</div>
                        </div>
                        <span class="badge badge-success"><i class="fas fa-lock"></i> Banked in Vault</span>
                    </div>
                `;
            });
        }
        $('#staff_cash_log_list').html(html);
    }

    function viewDonationReceipt(receiptNo) {
        const list = StaffDonationsStore.getAll();
        const item = list.find(d => d.receiptNo === receiptNo);
        if (!item) return;

        $('#receipt_modal_ref').text(`Receipt No: ${item.receiptNo}`);
        $('#receipt_modal_date').text(`Date: ${item.date}`);
        $('#receipt_modal_donor').text(item.donor);
        $('#receipt_modal_amount').text(`$${parseFloat(item.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} USD`);
        $('#receipt_modal_method').text(`${item.channel} (${item.type})`);
        $('#receipt_modal_fund').text(item.fund);
        $('#receipt_modal_staff').text(`${item.staff} (Sanctuary Caregiver)`);

        openStaffDonationModal('modal_staff_view_receipt');
    }

    function handleStaffDonationSubmit(e) {
        e.preventDefault();
        const donor = $('#staff_don_name').val().trim();
        const type = $('#staff_don_type').val();
        const amount = parseFloat($('#staff_don_amount').val()) || 0;
        const channel = $('#staff_don_channel').val();
        const fund = $('#staff_don_fund').val();
        const notes = $('#staff_don_notes').val().trim();

        const list = StaffDonationsStore.getAll();
        const rcpNo = 'RCP-2026-' + Math.floor(1000 + Math.random() * 9000);
        const today = new Date().toISOString().split('T')[0];

        const newDonation = {
            receiptNo: rcpNo,
            date: today,
            donor: donor,
            type: type,
            amount: amount,
            channel: channel,
            fund: fund,
            staff: 'Nurse Mary',
            notes: notes || 'Logged at staff duty desk.'
        };

        list.unshift(newDonation);
        StaffDonationsStore.saveAll(list);

        closeStaffDonationModal('modal_staff_record_donation');
        e.target.reset();
        renderStaffDonations();

        showPortalToast(
            `Contribution of $${amount.toFixed(2)} from ${donor} logged! E-Receipt #${rcpNo} generated.`,
            true,
            'Donation Recorded Successfully',
            'Staff Treasury Desk'
        );
    }

    function toggleDonationTypeFields() {
        const type = $('#staff_don_type').val();
        if (type === 'In-Kind') {
            $('#staff_don_channel').val('Hand-Delivered Goods');
        } else {
            $('#staff_don_channel').val('Cash at Front Desk');
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    $(document).ready(function() {
        $('.tab-btn').on('click', function() {
            const target = $(this).data('tab');
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.tab-content-panel').hide();
            $('#' + target).fadeIn(150);
        });

        renderStaffDonations();
    });
</script>
@endpush
