@extends('layouts.portal')

@section('title', 'Orphanage Donations & Grants Management')
@section('breadcrumb', 'Donations & Grants')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-donations.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/director-donations.css')), 0, 12) }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Header -->
    <div class="dir-don-header">
        <div>
            <h1><i class="fas fa-hand-holding-usd" style="color: #059669;"></i> Orphanage Endowments, Grants & Financial Management</h1>
            <p>Executive management of institutional grants, corporate CSR partnerships, and audited Section 44(6) tax-exempt funds.</p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button type="button" class="btn-primary" onclick="openStaffDonationModal('modal_staff_record_donation')"><i class="fas fa-plus-circle"></i> Record Contribution</button>
            <button type="button" class="btn-outline" onclick="openStaffDonationModal('modal_register_donor')"><i class="fas fa-user-plus"></i> Register Donor</button>
            <button type="button" class="btn-director-review" onclick="showPortalToast('Orphanage Donation Records (LHDN Form) exported.', 'info')">
                <i class="fas fa-file-invoice-dollar"></i> Export Fund Ledger (PDF)
            </button>
        </div>
    </div>

    <!-- Financial Governance KPIs -->
    <div class="dir-don-kpis">
        <div class="dir-don-kpi-card">
            <div class="dir-don-kpi-icon" style="background:#ecfdf5; color:#059669;">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="dir-don-kpi-val">RM 284,500</div>
                <div class="dir-don-kpi-lbl">Total YTD Contributions (2026)</div>
            </div>
        </div>

        <div class="dir-don-kpi-card">
            <div class="dir-don-kpi-icon" style="background:#eef2ff; color:#4f46e5;">
                <i class="fas fa-building-columns"></i>
            </div>
            <div>
                <div class="dir-don-kpi-val">3 Grants</div>
                <div class="dir-don-kpi-lbl">Active Institutional Endowments</div>
            </div>
        </div>

        <div class="dir-don-kpi-card">
            <div class="dir-don-kpi-icon" style="background:#f5f3ff; color:#7c3aed;">
                <i class="fas fa-vault"></i>
            </div>
            <div>
                <div class="dir-don-kpi-val">RM 148,350</div>
                <div class="dir-don-kpi-lbl">Available Capital Reserve</div>
            </div>
        </div>

        <div class="dir-don-kpi-card">
            <div class="dir-don-kpi-icon" style="background:#fef3c7; color:#d97706;">
                <i class="fas fa-certificate"></i>
            </div>
            <div>
                <div class="dir-don-kpi-val">100% Tax Deductible</div>
                <div class="dir-don-kpi-lbl">LHDN Sec 44(6) Audited</div>
            </div>
        </div>
    </div>

    <!-- Institutional Grants & Endowments Table -->
    <div class="dir-don-table-card">
        <div class="director-card-head">
            <div class="director-card-title">
                <i class="fas fa-landmark"></i>
                <span>Institutional Foundation Grants & Designated Endowments</span>
            </div>
            <span class="badge" style="background:#ecfdf5; color:#047857; font-weight:700; padding:4px 10px; border-radius:20px;">
                <i class="fas fa-check-double"></i> All Tranches Fully Reconciled
            </span>
        </div>
        <table class="dir-don-table">
            <thead>
                <tr>
                    <th>Grantor Entity</th>
                    <th>Grant Type & Purpose</th>
                    <th>Committed Grant</th>
                    <th>Disbursed to Date</th>
                    <th>Next Tranche (Q4)</th>
                    <th>Audit Status</th>
                    <th class="action-column" style="text-align: right;">Director Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Grant 1: Yayasan Hasanah -->
                <tr>
                    <td>
                        <strong>Yayasan Hasanah</strong><br>
                        <span style="font-size: 11px; color: #64748b;">Ref: YH-GRANT-2026-KL</span>
                    </td>
                    <td>SPM & Secondary School Scholarships<br><span style="font-size: 11px; color: #7c3aed;">Covers 18 Secondary Students</span></td>
                    <td><strong style="color: #0f172a;">RM 50,000.00</strong></td>
                    <td><span style="color: #059669; font-weight: 700;">RM 37,500.00</span></td>
                    <td><span style="color: #4f46e5; font-weight: 800;">RM 12,500.00</span></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Milestone Audited</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-director-review" onclick="openGrantReleaseModal('Yayasan Hasanah', 'RM 50,000.00', 'RM 12,500.00')">
                            <i class="fas fa-stamp"></i> Release Tranche
                        </button>
                    </td>
                </tr>

                <!-- Grant 2: Maybank Islamic -->
                <tr>
                    <td>
                        <strong>Maybank Islamic Berhad</strong><br>
                        <span style="font-size: 11px; color: #64748b;">Ref: MBB-CSR-ISL-26</span>
                    </td>
                    <td>Pediatric Healthcare & Nutrition Fund<br><span style="font-size: 11px; color: #059669;">Nursery Formula & Clinical Care</span></td>
                    <td><strong style="color: #0f172a;">RM 40,000.00</strong></td>
                    <td><span style="color: #059669; font-weight: 700;">RM 30,000.00</span></td>
                    <td><span style="color: #4f46e5; font-weight: 800;">RM 10,000.00</span></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Verified</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-director-review" onclick="openGrantReleaseModal('Maybank Islamic Berhad', 'RM 40,000.00', 'RM 10,000.00')">
                            <i class="fas fa-stamp"></i> Release Tranche
                        </button>
                    </td>
                </tr>

                <!-- Grant 3: Sunway Care -->
                <tr>
                    <td>
                        <strong>Sunway Group (Jeffrey Cheah Fdn)</strong><br>
                        <span style="font-size: 11px; color: #64748b;">Ref: JCF-SANCT-09</span>
                    </td>
                    <td>Orphanage Infrastructure & IT Lab<br><span style="font-size: 11px; color: #4f46e5;">High-speed broadband & laptops</span></td>
                    <td><strong style="color: #0f172a;">RM 30,000.00</strong></td>
                    <td><span style="color: #059669; font-weight: 700;">RM 20,000.00</span></td>
                    <td><span style="color: #4f46e5; font-weight: 800;">RM 10,000.00</span></td>
                    <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Verified</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-director-review" onclick="openGrantReleaseModal('Sunway Group (Jeffrey Cheah Fdn)', 'RM 30,000.00', 'RM 10,000.00')">
                            <i class="fas fa-stamp"></i> Release Tranche
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@include('portal.partials.donation-entry-ledger')
@include('portal.partials.donation-entry-modals')
@include('portal.partials.donor-registration-modal')
@include('portal.director.partials.donations-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/donation-entry.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/donation-entry.js')), 0, 12) }}"></script>
    <script src="{{ asset('js/portal/director-donations.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/director-donations.js')), 0, 12) }}"></script>
@endpush
