@extends('layouts.portal')

@section('title', 'Staff Inventory Requests & Requisitions')
@section('breadcrumb', 'Inventory Requisitions')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-inventory.css') }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Page Header -->
    <div class="inv-page-header">
        <div class="inv-page-title-wrap">
            <h1><i class="fas fa-boxes-packing" style="color: #4f46e5;"></i> Staff Inventory Requisitions</h1>
            <p>Review, sign, and authorize replenishment requests submitted by caregivers and department heads.</p>
        </div>
        <div class="inv-header-actions">
            <button type="button" class="btn-director-review" onclick="exportRequisitionsAudit()">
                <i class="fas fa-file-excel"></i> Export Requisition Ledger
            </button>
        </div>
    </div>

    <!-- Metric Stats Row -->
    <div class="inv-stats-row">
        <div class="inv-stat-card">
            <div class="inv-stat-icon amber">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="inv-stat-val" id="stat_pending_count">4</div>
                <div class="inv-stat-lbl">Pending Director Sign-off</div>
            </div>
        </div>

        <div class="inv-stat-card">
            <div class="inv-stat-icon rose">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="inv-stat-val">2</div>
                <div class="inv-stat-lbl">Urgent / Clinical Priority</div>
            </div>
        </div>

        <div class="inv-stat-card">
            <div class="inv-stat-icon emerald">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <div class="inv-stat-val">12</div>
                <div class="inv-stat-lbl">Approved & Dispatched (Q4)</div>
            </div>
        </div>

        <div class="inv-stat-card">
            <div class="inv-stat-icon indigo">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="inv-stat-val">RM 14,580</div>
                <div class="inv-stat-lbl">Available Supply Reserve</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="inv-toolbar">
        <div class="inv-filter-tabs">
            <button type="button" class="inv-tab-btn active" data-filter="all">
                All Requisitions <span class="inv-tab-badge">18</span>
            </button>
            <button type="button" class="inv-tab-btn" data-filter="pending">
                Pending Approval <span class="inv-tab-badge" id="tab_badge_pending">4</span>
            </button>
            <button type="button" class="inv-tab-btn" data-filter="critical">
                Urgent Priority <span class="inv-tab-badge">2</span>
            </button>
            <button type="button" class="inv-tab-btn" data-filter="approved">
                Approved <span class="inv-tab-badge">12</span>
            </button>
            <button type="button" class="inv-tab-btn" data-filter="rejected">
                Revisions <span class="inv-tab-badge">2</span>
            </button>
        </div>

        <div class="inv-search-wrap">
            <select class="inv-select-filter" id="inv_category_filter">
                <option value="all">All Departments</option>
                <option value="medical">Medical & Sickbay</option>
                <option value="education">Education & Study Hall</option>
                <option value="nursery">Nursery & Infants</option>
                <option value="kitchen">Kitchen & Nutrition</option>
                <option value="facility">Facility & Maintenance</option>
            </select>
            <input type="text" class="inv-search-input" id="inv_search_input" placeholder="Search staff, item, or ID...">
        </div>
    </div>

    <!-- Requisitions Table Card -->
    <div class="inv-table-card">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Requesting Staff</th>
                    <th>Item Description</th>
                    <th>Priority</th>
                    <th>Estimated Cost</th>
                    <th>Stock On-Hand</th>
                    <th>Status</th>
                    <th class="action-column" style="text-align: right;">Director Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1: Pediatric Nebulizers (Nurse Mary) -->
                <tr data-req-id="REQ-2026-104" data-status="pending" data-priority="critical" data-category="medical">
                    <td><strong>#REQ-2026-104</strong><br><span style="font-size: 11px; color: #94a3b8;">Today, 08:30 AM</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar">NM</div>
                            <div>
                                <div class="user-cell-name">Nurse Mary</div>
                                <div class="user-cell-dept">Ward Alpha Medical</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="item-cell-title">Pediatric Ultrasonic Nebulizers</div>
                        <div class="item-cell-desc"><span class="item-badge-qty">Qty: 2 units</span>Includes 4 pediatric aerosol masks & tubing sets.</div>
                    </td>
                    <td>
                        <span class="priority-pill critical"><i class="fas fa-bolt"></i> Critical</span>
                    </td>
                    <td><strong>RM 680.00</strong></td>
                    <td><span style="color: #e11d48; font-weight: 700;">0 (Defective)</span></td>
                    <td class="status-cell">
                        <span class="status-pill pending"><i class="fas fa-clock"></i> Awaiting Director</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="inv-action-group" style="justify-content: flex-end;">
                            <button type="button" class="btn-sign" onclick="openDirectorSignModal('REQ-2026-104', 'Nurse Mary', 'Ward Alpha Medical', 'RM 680.00', 'Pediatric Ultrasonic Nebulizers (2 units) + Masks', 'Current unit broke down; Ahmad and Danial have active asthma prescriptions requiring daily therapy.')">
                                <i class="fas fa-signature"></i> Review & Sign
                            </button>
                            <button type="button" class="btn-quick-app" onclick="quickApproveRow('REQ-2026-104', 'Pediatric Nebulizers')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn-reject-modal" onclick="openDirectorRejectModal('REQ-2026-104')">
                                <i class="fas fa-reply"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 2: Secondary School Books (Ustaz Hakim) -->
                <tr data-req-id="REQ-2026-102" data-status="pending" data-priority="high" data-category="education">
                    <td><strong>#REQ-2026-102</strong><br><span style="font-size: 11px; color: #94a3b8;">Yesterday</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#e0f2fe; color:#0369a1;">UH</div>
                            <div>
                                <div class="user-cell-name">Ustaz Hakim</div>
                                <div class="user-cell-dept">Study Hall & Tuition</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="item-cell-title">SPM & PT3 Exam Exercise Modules</div>
                        <div class="item-cell-desc"><span class="item-badge-qty">Qty: 30 sets</span>Mathematics, Bahasa Melayu, & General Science.</div>
                    </td>
                    <td>
                        <span class="priority-pill high"><i class="fas fa-circle-exclamation"></i> High</span>
                    </td>
                    <td><strong>RM 1,240.00</strong></td>
                    <td><span style="color: #64748b;">4 sets remaining</span></td>
                    <td class="status-cell">
                        <span class="status-pill pending"><i class="fas fa-clock"></i> Awaiting Director</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="inv-action-group" style="justify-content: flex-end;">
                            <button type="button" class="btn-sign" onclick="openDirectorSignModal('REQ-2026-102', 'Ustaz Hakim', 'Study Hall & Tuition', 'RM 1,240.00', 'SPM & PT3 Exam Exercise Modules (30 sets)', 'Upcoming Mid-Year assessments require new syllabus modules for 18 secondary students.')">
                                <i class="fas fa-signature"></i> Review & Sign
                            </button>
                            <button type="button" class="btn-quick-app" onclick="quickApproveRow('REQ-2026-102', 'SPM Exam Modules')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn-reject-modal" onclick="openDirectorRejectModal('REQ-2026-102')">
                                <i class="fas fa-reply"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 3: Nursery Formula & Diapers (Caregiver Lisa) -->
                <tr data-req-id="REQ-2026-098" data-status="pending" data-priority="critical" data-category="nursery">
                    <td><strong>#REQ-2026-098</strong><br><span style="font-size: 11px; color: #94a3b8;">2 Days ago</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#fef3c7; color:#b45309;">CL</div>
                            <div>
                                <div class="user-cell-name">Caregiver Lisa</div>
                                <div class="user-cell-dept">Nursery Toddler Wing</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="item-cell-title">Hypoallergenic Baby Formula & Drypers L</div>
                        <div class="item-cell-desc"><span class="item-badge-qty">Qty: 8 tins + 12 pks</span>Special lactose-free dietary milk for 2 infants.</div>
                    </td>
                    <td>
                        <span class="priority-pill critical"><i class="fas fa-bolt"></i> Critical</span>
                    </td>
                    <td><strong>RM 820.00</strong></td>
                    <td><span style="color: #e11d48; font-weight: 700;">1 tin left</span></td>
                    <td class="status-cell">
                        <span class="status-pill pending"><i class="fas fa-clock"></i> Awaiting Director</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="inv-action-group" style="justify-content: flex-end;">
                            <button type="button" class="btn-sign" onclick="openDirectorSignModal('REQ-2026-098', 'Caregiver Lisa', 'Nursery Toddler Wing', 'RM 820.00', 'Lactose-Free Formula (8 tins) & Drypers L (12 packs)', 'Infants Maya and Rayyan have sensitive digestive tract; buffer stock depleted.')">
                                <i class="fas fa-signature"></i> Review & Sign
                            </button>
                            <button type="button" class="btn-quick-app" onclick="quickApproveRow('REQ-2026-098', 'Baby Formula & Diapers')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn-reject-modal" onclick="openDirectorRejectModal('REQ-2026-098')">
                                <i class="fas fa-reply"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 4: Kitchen Rice Cooker Element (Chef Razak) -->
                <tr data-req-id="REQ-2026-095" data-status="pending" data-priority="high" data-category="kitchen">
                    <td><strong>#REQ-2026-095</strong><br><span style="font-size: 11px; color: #94a3b8;">3 Days ago</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#fce7f3; color:#be185d;">CR</div>
                            <div>
                                <div class="user-cell-name">Chef Razak</div>
                                <div class="user-cell-dept">Sanctuary Dining & Kitchen</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="item-cell-title">Commercial Rice Cooker 20L Element</div>
                        <div class="item-cell-desc"><span class="item-badge-qty">Qty: 1 spare</span>Replacement heating coil for daily dining hall service.</div>
                    </td>
                    <td>
                        <span class="priority-pill high"><i class="fas fa-circle-exclamation"></i> High</span>
                    </td>
                    <td><strong>RM 680.00</strong></td>
                    <td><span style="color: #64748b;">Primary heating slow</span></td>
                    <td class="status-cell">
                        <span class="status-pill pending"><i class="fas fa-clock"></i> Awaiting Director</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="inv-action-group" style="justify-content: flex-end;">
                            <button type="button" class="btn-sign" onclick="openDirectorSignModal('REQ-2026-095', 'Chef Razak', 'Sanctuary Dining & Kitchen', 'RM 680.00', 'Commercial Rice Cooker 20L Element & Thermostat', 'Prevents kitchen disruption during communal breakfast and dinner serving.')">
                                <i class="fas fa-signature"></i> Review & Sign
                            </button>
                            <button type="button" class="btn-quick-app" onclick="quickApproveRow('REQ-2026-095', 'Rice Cooker Element')">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn-reject-modal" onclick="openDirectorRejectModal('REQ-2026-095')">
                                <i class="fas fa-reply"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 5: Approved Antiseptic & First Aid Consumables -->
                <tr data-req-id="REQ-2026-088" data-status="approved" data-priority="normal" data-category="medical">
                    <td><strong>#REQ-2026-088</strong><br><span style="font-size: 11px; color: #94a3b8;">Last Week</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar">NM</div>
                            <div>
                                <div class="user-cell-name">Nurse Mary</div>
                                <div class="user-cell-dept">Ward Alpha Medical</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="item-cell-title">Antiseptic Solutions & Gauze Packs</div>
                        <div class="item-cell-desc"><span class="item-badge-qty">Qty: 20 boxes</span>Dettol, sterile saline, elastic bandages, plasters.</div>
                    </td>
                    <td>
                        <span class="priority-pill normal">Normal</span>
                    </td>
                    <td><strong>RM 340.00</strong></td>
                    <td><span style="color: #10b981;">Adequate (Restocked)</span></td>
                    <td class="status-cell">
                        <span class="status-pill approved"><i class="fas fa-check-circle"></i> Approved by Sister Sarah</span>
                    </td>
                    <td style="text-align: right;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600;"><i class="fas fa-box-open" style="color: #10b981;"></i> Dispatched</span>
                    </td>
                </tr>

                <!-- Row 6: Approved School Bags & Stationery Bundles -->
                <tr data-req-id="REQ-2026-082" data-status="approved" data-priority="normal" data-category="education">
                    <td><strong>#REQ-2026-082</strong><br><span style="font-size: 11px; color: #94a3b8;">Oct 12</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#e0f2fe; color:#0369a1;">UH</div>
                            <div>
                                <div class="user-cell-name">Ustaz Hakim</div>
                                <div class="user-cell-dept">Study Hall & Tuition</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="item-cell-title">Ergonomic School Bags & Geometry Kits</div>
                        <div class="item-cell-desc"><span class="item-badge-qty">Qty: 15 bags</span>Assigned to new intake primary students.</div>
                    </td>
                    <td>
                        <span class="priority-pill normal">Normal</span>
                    </td>
                    <td><strong>RM 1,450.00</strong></td>
                    <td><span style="color: #10b981;">Supplied</span></td>
                    <td class="status-cell">
                        <span class="status-pill approved"><i class="fas fa-check-circle"></i> Approved by Sister Sarah</span>
                    </td>
                    <td style="text-align: right;">
                        <span style="font-size: 12px; color: #64748b; font-weight: 600;"><i class="fas fa-box-open" style="color: #10b981;"></i> Dispatched</span>
                    </td>
                </tr>

                <!-- Row 7: Rejected / Revision Item (High-End Tablets) -->
                <tr data-req-id="REQ-2026-079" data-status="rejected" data-priority="normal" data-category="education">
                    <td><strong>#REQ-2026-079</strong><br><span style="font-size: 11px; color: #94a3b8;">Oct 08</span></td>
                    <td>
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#f1f5f9; color:#475569;">BT</div>
                            <div>
                                <div class="user-cell-name">Brother Thomas</div>
                                <div class="user-cell-dept">Vocational IT Lab</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="item-cell-title">Tablet Devices with Stylus (4 units)</div>
                        <div class="item-cell-desc"><span class="item-badge-qty">Qty: 4 units</span>High-end graphics tablets.</div>
                    </td>
                    <td>
                        <span class="priority-pill normal">Normal</span>
                    </td>
                    <td><strong>RM 4,800.00</strong></td>
                    <td><span style="color: #64748b;">N/A</span></td>
                    <td class="status-cell">
                        <span class="status-pill rejected"><i class="fas fa-circle-xmark"></i> Revision Requested</span>
                    </td>
                    <td style="text-align: right;">
                        <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Use Donated Laptops</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Include Dedicated Modals Partial -->
@include('portal.director.partials.inventory-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-inventory.js') }}"></script>
@endpush
