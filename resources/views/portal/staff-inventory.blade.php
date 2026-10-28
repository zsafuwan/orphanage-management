@extends('layouts.portal')

@section('title', 'Inventory & Supplies')
@section('breadcrumb', 'Inventory & Supplies')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/inventory.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/inventory.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/dashboard.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/embedded/portal-staff-inventory-1.css') }}?v={{ substr(hash_file('sha256', public_path('css/embedded/portal-staff-inventory-1.css')), 0, 12) }}">
@endpush

@section('content')

<!-- Staff Hero Header -->
<div class="staff-inv-hero">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div style="max-width: 680px;">
            <div class="staff-inv-badge">
                <i class="fas fa-boxes"></i> Subsystem 2 · Staff Duty Ward Inventory
            </div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                Inventory & Supplies
            </h1>
            <p style="font-size: 13.5px; opacity: 0.92; margin: 0; line-height: 1.5;">
                Add stock items, update current counts, and request more supplies for director approval. Stock and request updates appear automatically in both views.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <button type="button" class="btn-primary" onclick="openStaffInvModal('modal_staff_add_stock')"><i class="fas fa-plus" aria-hidden="true"></i> Add Stock Item</button>
            <button class="btn-primary" style="background: #ffffff; color: #065f46; border-color: #ffffff; font-weight: 700;" onclick="openStaffInvModal('modal_dispense_to_child')">
                <i class="fas fa-hand-holding-heart"></i> Give Supplies to Child
            </button>
            <button class="btn-outline" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border-color: rgba(255, 255, 255, 0.35); font-weight: 700;" onclick="openStaffInvModal('modal_staff_request_supplies')">
                <i class="fas fa-cart-plus"></i> Request Supplies
            </button>
        </div>
    </div>
</div>

<!-- Clearance Level & Identity Ribbon -->
<div class="rbac-banner" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-nurse" style="color: #10b981; font-size: 18px;"></i>
        <div>
            <span style="font-size: 12px; font-weight: 700; color: var(--dark); text-transform: uppercase;">Staff in Charge:</span>
            <span style="font-size: 12px; color: var(--dark-muted); margin-left: 4px;">Caregiver on Duty: <strong style="color: #10b981;">Nurse Mary</strong> · Morning Shift (Ward Alpha & Beta Nursery)</span>
        </div>
    </div>
    <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
        Storeroom Dispatch Window: <strong>11:00 AM & 15:00 Daily</strong>
    </div>
</div>

<!-- Metric KPI Cards -->
<div class="metrics-grid" style="margin-bottom: 24px;">
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Ward Cupboard Items</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-cubes"></i>
            </div>
        </div>
        <div class="metric-val" id="metric_total_items">12 Lines</div>
        <div class="metric-sub">Across Ward Alpha & Beta cabinets</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Low Stock / Urgent</span>
            <div class="metric-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #ef4444;" id="metric_low_stock">2 Items</div>
        <div class="metric-sub">Below minimum ward threshold</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Given Today</span>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #2563eb;" id="metric_dispensed_today">6 Allocations</div>
        <div class="metric-sub">Logged to specific residential children</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Supply Requests</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-dolly"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #d97706;" id="metric_active_reqs">2 En Route</div>
        <div class="metric-sub">Sent to the director for approval</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container" style="margin-bottom: 20px;">
    <button class="tab-btn active" data-tab="tab_ward_balances">
        <i class="fas fa-boxes"></i> Ward Cabinet Balances (<span id="tab_count_balances">12</span>)
    </button>
    <button class="tab-btn" data-tab="tab_child_dispensations">
        <i class="fas fa-history"></i> Supplies Given to Children (<span id="tab_count_dispensations">6</span>)
    </button>
    <button class="tab-btn" data-tab="tab_storeroom_orders">
        <i class="fas fa-truck-loading"></i> Supply Requests & Tracking (<span id="tab_count_orders">3</span>)
    </button>
    <button class="tab-btn" data-tab="tab_first_aid_checklist">
        <i class="fas fa-medkit"></i> Daily First Aid & Safety Checklist
    </button>
</div>

<!-- Search & Category Filters -->
<div class="filter-bar staff-record-filters" style="margin-bottom: 20px;">
    <div class="search-box-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="staff_inv_search" class="search-input" placeholder="Search ward items by name, SKU, or cupboard location..." oninput="filterStaffInventory()">
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_inv_cat_filter" class="filter-select" onchange="filterStaffInventory()">
            <option value="all">All Categories</option>
            <option value="Medical">Pediatric Medical & First Aid</option>
            <option value="Hygiene">Baby & Nursery Hygiene</option>
            <option value="Nutrition">Nutritional Food & Milk Formula</option>
            <option value="Education">Educational & Stationery</option>
            <option value="Linen">Bedding & Linens</option>
        </select>
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_inv_loc_filter" class="filter-select" onchange="filterStaffInventory()">
            <option value="all">All Ward Locations</option>
            <option value="Ward Alpha Medicine Cabinet">Ward Alpha Medicine Cabinet</option>
            <option value="Ward Alpha Nursery Shelf">Ward Alpha Nursery Shelf</option>
            <option value="Ward Beta Care Cupboard">Ward Beta Care Cupboard</option>
            <option value="Dispensary Refrigerator">Dispensary Refrigerator</option>
            <option value="Common Study Locker">Common Study Locker</option>
        </select>
    </div>

    <!-- Toggle View Mode: Cards vs Table -->
    <div class="view-mode-toggle staff-inventory-view-toggle" role="group" aria-label="Inventory display">
        <button type="button" class="view-mode-btn active" aria-pressed="true" id="btn_view_inv_cards" onclick="toggleStaffInvView('cards')">
            <i class="fas fa-th-large"></i> Cards
        </button>
        <button type="button" class="view-mode-btn" aria-pressed="false" id="btn_view_inv_table" onclick="toggleStaffInvView('table')">
            <i class="fas fa-list-ul"></i> Table
        </button>
    </div>
</div>

<!-- ==========================================================================
     TAB 1: WARD CABINET STOCK BALANCES
     ========================================================================== -->
<div class="tab-content-panel" id="tab_ward_balances">
    <!-- Cards View Grid -->
    <div class="staff-inv-grid" id="staff_inv_cards_container">
        <!-- Rendered dynamically by JavaScript -->
    </div>

    <!-- Table View Wrapper (Initially Hidden) -->
    <div class="portal-card" id="staff_inv_table_container" style="display: none; padding: 0; overflow: hidden; margin-bottom: 24px;">
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Item Details & SKU</th>
                        <th>Category</th>
                        <th>Ward Location</th>
                        <th>On-Hand Qty</th>
                        <th>Min Threshold</th>
                        <th>Stock Health</th>
                        <th class="action-column" style="text-align: right;">Duty Actions</th>
                    </tr>
                </thead>
                <tbody id="staff_inv_table_tbody">
                    <!-- Rendered dynamically by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 2: CHILD DISPENSATION LEDGER
     ========================================================================== -->
<div class="tab-content-panel" id="tab_child_dispensations" style="display: none;">
    <div class="portal-card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Child Supply History</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">
                    Log of medicine, hygiene items, and educational materials allocated directly to resident children by shift staff.
                </p>
            </div>
            <button class="btn-primary btn-sm" onclick="openStaffInvModal('modal_dispense_to_child')">
                <i class="fas fa-plus"></i> New Child Supply Distribution
            </button>
        </div>

        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Child Ward Recipient</th>
                        <th>Supply Item & Quantity</th>
                        <th>Category</th>
                        <th>Care Reason / Dosage Note</th>
                        <th>Given By</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody id="staff_dispensation_table_tbody">
                    <!-- Rendered dynamically by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 3: STOREROOM REQUISITIONS & TRACKING
     ========================================================================== -->
<div class="tab-content-panel" id="tab_storeroom_orders" style="display: none;">
    <div class="portal-card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Staff Caregiver Requests to Storeroom</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">
                    Track supply requests sent to the director. Click "Confirm Received" once approved items arrive to update stock.
                </p>
            </div>
            <button class="btn-primary btn-sm" style="background: #f59e0b; border-color: #f59e0b;" onclick="openStaffInvModal('modal_staff_request_supplies')">
                <i class="fas fa-paper-plane"></i> Submit Request
            </button>
        </div>

        <div id="staff_requisitions_container">
            <!-- Rendered dynamically by JavaScript -->
        </div>
    </div>
</div>

<!-- ==========================================================================
     TAB 4: EMERGENCY FIRST AID & CLINICAL DISPENSARY CHECKLIST
     ========================================================================== -->
<div class="tab-content-panel" id="tab_first_aid_checklist" style="display: none;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <div class="portal-card" style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Morning Shift Safety & Clinical Readiness Checklist</h3>
                    <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">Mandatory daily inspection completed by Nurse Mary</p>
                </div>
                <span class="badge badge-success" style="font-size: 11px;">Inspected: Today 08:30 AM</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;" id="shift_safety_checklist">
                <label style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer;">
                    <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: #10b981;">
                    <div>
                        <strong style="font-size: 13.5px; color: #0f172a;">Emergency Pediatric Asthma Nebulizer Machine</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Chambers sterilized, power cable intact, 6 Salbutamol nebules ready in Ward Alpha</div>
                    </div>
                </label>

                <label style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer;">
                    <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: #10b981;">
                    <div>
                        <strong style="font-size: 13.5px; color: #0f172a;">Digital Fever Infrared Thermometers (x2)</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Calibrated at 36.6°C reference baseline. Batteries tested above 80% charge.</div>
                    </div>
                </label>

                <label style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer;">
                    <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: #10b981;">
                    <div>
                        <strong style="font-size: 13.5px; color: #0f172a;">Sterile Wound Care & Antiseptic Bandage Kit</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Dettol antiseptic solution, micropore surgical tape, sterile gauze swabs verified.</div>
                    </div>
                </label>

                <label style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer;">
                    <input type="checkbox" checked style="width: 18px; height: 18px; accent-color: #10b981;">
                    <div>
                        <strong style="font-size: 13.5px; color: #0f172a;">Pediatric EpiPen Auto-Injector (Emergency Allergic Anaphylaxis)</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Stored in secure dispensary lockbox. Expiry date valid: November 2026.</div>
                    </div>
                </label>
            </div>

            <button type="button" class="btn-outline btn-sm" style="margin-top: 14px;" onclick="showPortalToast('Shift clinical checklist certified & saved to log.', true, 'Checklist Verified');">
                <i class="fas fa-check-double"></i> Save Checklist Certification
            </button>
        </div>

        <div class="portal-card" style="padding: 24px; background: #f8fafc; border: 1px dashed #cbd5e1;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div>
                    <h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0;">Urgent Supply Contacts</h4>
                    <span style="font-size: 11px; color: #64748b;">Emergency storeroom dispatch</span>
                </div>
            </div>

            <div style="font-size: 12.5px; color: #334155; line-height: 1.8;">
                <div><strong>Storeroom Manager (Brother Leo):</strong><br><code>Ext 104 · +60 12-449 8123</code></div>
                <div style="margin-top: 8px;"><strong>Lead Administrator (Sister Grace):</strong><br><code>Ext 101 · +60 19-204 1188</code></div>
                <div style="margin-top: 8px;"><strong>Orphanage Pharmacy:</strong><br><code>03-2615 5555 (Klinik Kesihatan)</code></div>
            </div>

            <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 11.5px; color: #64748b; font-style: italic;">
                "For life-threatening pediatric medical emergencies, dial 999 immediately before requesting replacement supplies."
            </div>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL 1: REQUEST STOREROOM SUPPLIES (STAFF REQUISITION)
     ========================================================================== -->
<div id="modal_staff_request_supplies" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 580px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Request More Supplies</h3>
                <p class="modal-sub">Send a supply request to the director for approval.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffInvModal('modal_staff_request_supplies')">&times;</button>
        </div>

        <form onsubmit="handleStaffRequisitionSubmit(event)">
            <div class="form-group">
                <label class="form-label">Supply Category *</label>
                <select id="req_category_select" class="form-control" required>
                    <option value="Medical">Pediatric Medical & First Aid</option>
                    <option value="Hygiene">Baby & Nursery Hygiene</option>
                    <option value="Nutrition">Nutritional Food & Milk Formula</option>
                    <option value="Education">Educational Stationery & Books</option>
                    <option value="Linen">Bedding, Towels & Ward Linens</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Item Needed *</label>
                <input type="text" id="req_item_input" class="form-control" placeholder="Enter the supply item needed" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Quantity Needed *</label>
                    <input type="number" id="req_qty_input" class="form-control" value="2" min="1"  required>
                </div>
                <div class="form-group">
                    <label class="form-label">Delivery Destination Ward *</label>
                    <input type="text" id="req_destination_input" class="form-control" placeholder="Enter the delivery ward or storage location" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Priority Level *</label>
                <select id="req_priority_select" class="form-control" required>
                    <option value="Normal">Normal (Deliver during 15:00 dispatch)</option>
                    <option value="Urgent">Urgent (Required within 1 hour)</option>
                    <option value="Critical">Critical Emergency (Immediate Handover)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Staff Caregiver on Duty Remarks</label>
                <textarea id="req_notes_input" class="form-control" rows="2" placeholder="e.g. 2 children with mild cough; nursery diaper stock down to last pack..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffInvModal('modal_staff_request_supplies')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #f59e0b; border-color: #f59e0b;">
                    <i class="fas fa-paper-plane"></i> Send Request for Approval
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================================
     MODAL 2: DISPENSE SUPPLY TO RESIDENT CHILD
     ========================================================================== -->
<div id="modal_dispense_to_child" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 580px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Give Supplies to a Child</h3>
                <p class="modal-sub">Log supplies, medicine, or hygiene essentials given to a ward.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffInvModal('modal_dispense_to_child')">&times;</button>
        </div>

        <form onsubmit="handleChildDispenseSubmit(event)">
            <div class="form-group">
                <label class="form-label">Select Resident Child Recipient *</label>
                <select id="dispense_child_select" class="form-control" required>
                    <!-- Populated dynamically from HopeHavenChildStore -->
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ward Supply Item *</label>
                    <input type="text" id="dispense_item_input" class="form-control" placeholder="Enter an inventory item name or ID" oninput="updateDispenseAvailablePreview()" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity Given *</label>
                    <input type="number" id="dispense_qty_input" class="form-control" value="1" min="1" max="20" required>
                </div>
            </div>

            <div id="dispense_avail_notice" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 12px; font-size: 12px; color: #1e40af; margin-bottom: 14px;">
                Available in ward cabinet: <strong id="dispense_avail_qty">8 packs</strong>
            </div>

            <div class="form-group">
                <label class="form-label">Care Purpose / Dosage Note *</label>
                <input type="text" id="dispense_reason_input" class="form-control" placeholder="e.g. Prescribed bedtime dose, morning diaper change, school art class" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffInvModal('modal_dispense_to_child')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #10b981; border-color: #10b981;">
                    <i class="fas fa-check"></i> Confirm Supplies Given
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================================
     MODAL 3: QUICK CUPBOARD STOCK COUNT ADJUSTMENT
     ========================================================================== -->
<div id="modal_quick_count" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Adjust Ward Cupboard Count</h3>
                <p class="modal-sub">Update physical count during morning inventory check.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffInvModal('modal_quick_count')">&times;</button>
        </div>

        <form onsubmit="handleQuickCountSubmit(event)">
            <input type="hidden" id="count_item_id">
            
            <div class="form-group">
                <label class="form-label">Item Name</label>
                <input type="text" id="count_item_name" class="form-control" readonly style="background: #f1f5f9; font-weight: 700;">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Current System Count</label>
                    <input type="text" id="count_current_qty" class="form-control" readonly style="background: #f1f5f9;">
                </div>
                <div class="form-group">
                    <label class="form-label">Actual Physical Count *</label>
                    <input type="number" id="count_new_qty" class="form-control" min="0"  required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Adjustment Reason *</label>
                <select id="count_reason_select" class="form-control" required>
                    <option value="Routine Shift Physical Verification">Routine Shift Physical Verification</option>
                    <option value="Restocked from Earlier Requisition">Restocked from Earlier Request</option>
                    <option value="Damaged / Expired Container Discarded">Damaged / Expired Container Discarded</option>
                    <option value="Transferred to Other Ward">Transferred to Other Ward</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffInvModal('modal_quick_count')">Cancel</button>
                <button type="submit" class="btn-primary">Update Count</button>
            </div>
        </form>
    </div>
</div>

@include('portal.partials.stock-item-modal')
@endsection

@push('scripts')
<script>
    // Staff Ward Inventory Engine
    const STAFF_INV_STORAGE_KEY = window.HopeHavenInventory.keys.items;
    const STAFF_DISPENSE_STORAGE_KEY = window.HopeHavenInventory.keys.dispensations;
    const STAFF_REQ_STORAGE_KEY = window.HopeHavenInventory.keys.requests;
    const StaffInvStore = window.HopeHavenInventory;

    let currentViewMode = 'cards';

    function toggleStaffInvView(mode) {
        currentViewMode = mode;
        $('.view-mode-btn').removeClass('active').attr('aria-pressed', 'false');
        if (mode === 'cards') {
            $('#btn_view_inv_cards').addClass('active').attr('aria-pressed', 'true');
            $('#staff_inv_cards_container').show();
            $('#staff_inv_table_container').hide();
        } else {
            $('#btn_view_inv_table').addClass('active').attr('aria-pressed', 'true');
            $('#staff_inv_cards_container').hide();
            $('#staff_inv_table_container').show();
        }
    }

    function openStaffInvModal(id) {
        $('#' + id).css('display', 'flex').hide().fadeIn(200);
    }

    function closeStaffInvModal(id) {
        $('#' + id).fadeOut(150);
    }

    function renderAllStaffInventory() {
        renderWardInventoryItems();
        renderDispensationsTable();
        renderRequisitionsList();
        updateStaffInvMetrics();
        populateItemSelectDropdowns();
        populateChildSelectDropdown();
    }

    function renderWardInventoryItems() {
        const items = StaffInvStore.getItems();
        const previousLocation = $('#staff_inv_loc_filter').val() || 'all';
        const locations = [...new Set(items.map(item => item.location))];
        $('#staff_inv_loc_filter').html('<option value="all">All Storage Locations</option>' + locations.map(location => '<option value="' + escapeHtml(location) + '">' + escapeHtml(location) + '</option>').join('')).val(locations.includes(previousLocation) ? previousLocation : 'all');
        const search = ($('#staff_inv_search').val() || '').toLowerCase().trim();
        const catFilter = $('#staff_inv_cat_filter').val() || 'all';
        const locFilter = $('#staff_inv_loc_filter').val() || 'all';

        const filtered = items.filter(item => {
            const matchesSearch = !search || item.name.toLowerCase().includes(search) || item.id.toLowerCase().includes(search) || item.location.toLowerCase().includes(search);
            const matchesCat = catFilter === 'all' || item.category === catFilter;
            const matchesLoc = locFilter === 'all' || item.location === locFilter;
            return matchesSearch && matchesCat && matchesLoc;
        });

        // 1. Render Cards
        let cardsHtml = '';
        filtered.forEach(item => {
            const isLow = item.onHand <= item.minThreshold;
            const cardClass = isLow ? 'ward-inv-card low-stock' : 'ward-inv-card good-stock';
            
            let catColor = '#10b981';
            let catClass = 'category-hygiene';
            if (item.category === 'Medical') { catColor = '#ef4444'; catClass = 'category-medical'; }
            else if (item.category === 'Nutrition') { catColor = '#f59e0b'; catClass = 'category-food'; }
            else if (item.category === 'Education') { catColor = '#8b5cf6'; catClass = 'category-education'; }
            else if (item.category === 'Linen') { catColor = '#3b82f6'; catClass = 'category-clothing'; }

            cardsHtml += `
                <div class="${cardClass}">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <span class="category-pill ${catClass}">${item.category}</span>
                            <span class="stock-alert-box ${isLow ? 'stock-alert-critical' : 'stock-alert-normal'}">
                                <i class="fas ${isLow ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                                ${isLow ? 'Restock Urgently' : 'Optimal Level'}
                            </span>
                        </div>

                        <h4 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; line-height: 1.3;">
                            ${escapeHtml(item.name)}
                        </h4>
                        
                        <div style="margin-bottom: 12px;">
                            <span class="cabinet-loc-pill">
                                <i class="fas fa-map-marker-alt"></i> ${escapeHtml(item.location)}
                            </span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: flex-end; background: #f8fafc; padding: 12px 14px; border-radius: 8px; margin-bottom: 14px; border: 1px solid #e2e8f0;">
                            <div>
                                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Ward Cupboard On-Hand</div>
                                <div class="qty-badge" style="color: ${isLow ? '#dc2626' : '#0f172a'};">
                                    ${item.onHand} <small>${escapeHtml(item.unit)}</small>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 11px; color: #64748b; font-weight: 600;">Min Safe Buffer</div>
                                <div style="font-size: 13.5px; font-weight: 700; color: #475569;">${item.minThreshold} units</div>
                            </div>
                        </div>

                        <div style="font-size: 11.5px; color: #64748b; margin-bottom: 14px; display: flex; justify-content: space-between;">
                            <span>Code: <code>${item.id}</code></span>
                            <span>Exp: <strong>${item.expiry || 'N/A'}</strong></span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                        <button class="action-icon-btn primary" style="flex: 1;" onclick="openDispenseModalForItem('${item.id}')" title="Give Supplies to Child">
                            <i class="fas fa-hand-holding-heart"></i> Give Supplies
                        </button>
                        <button class="action-icon-btn req" onclick="openRequisitionModalForItem('${item.id}')" title="Request Reorder from Storeroom">
                            <i class="fas fa-cart-plus"></i> Reorder
                        </button>
                        <button class="action-icon-btn" onclick="openCountModalForItem('${item.id}')" title="Update Stock Count" aria-label="Update stock count">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        $('#staff_inv_cards_container').html(cardsHtml || '<div style="grid-column: 1/-1; padding: 36px; text-align: center; color: #94a3b8;">No ward inventory items matched your search filters.</div>');

        // 2. Render Table
        let tableHtml = '';
        filtered.forEach(item => {
            const isLow = item.onHand <= item.minThreshold;
            tableHtml += `
                <tr>
                    <td>
                        <strong style="color: #0f172a; font-size: 13.5px;">${escapeHtml(item.name)}</strong>
                        <div style="font-size: 11px; color: #64748b; font-family: monospace;">${item.id} · Batch: ${item.batchNo || 'N/A'}</div>
                    </td>
                    <td><span class="category-pill">${item.category}</span></td>
                    <td><span class="cabinet-loc-pill">${escapeHtml(item.location)}</span></td>
                    <td><strong style="font-size: 15px; color: ${isLow ? '#dc2626' : '#0f172a'};">${item.onHand}</strong> <span style="font-size: 11px; color: #64748b;">${item.unit}</span></td>
                    <td>${item.minThreshold}</td>
                    <td>
                        <span class="stock-alert-box ${isLow ? 'stock-alert-critical' : 'stock-alert-normal'}">
                            ${isLow ? 'Low Stock' : 'Adequate'}
                        </span>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button class="action-icon-btn primary" onclick="openDispenseModalForItem('${item.id}')" style="padding: 4px 8px; font-size: 11px;">
                            <i class="fas fa-hand-holding-heart"></i> Give Supplies
                        </button>
                        <button class="action-icon-btn req" onclick="openRequisitionModalForItem('${item.id}')" style="padding: 4px 8px; font-size: 11px;">
                            <i class="fas fa-cart-plus"></i> Reorder
                        </button>
                        <button class="action-icon-btn" onclick="openCountModalForItem('${item.id}')" style="padding: 4px 8px; font-size: 11px;">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#staff_inv_table_tbody').html(tableHtml);
    }

    function renderDispensationsTable() {
        const list = StaffInvStore.getDispensations();
        let html = '';
        list.forEach(d => {
            html += `
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">
                                <i class="fas fa-child"></i>
                            </div>
                            <strong style="color: #0f172a; font-size: 13px;">${escapeHtml(d.childName)}</strong>
                        </div>
                    </td>
                    <td>
                        <strong>${escapeHtml(d.itemName)}</strong>
                        <div style="font-size: 11.5px; color: #2563eb; font-weight: 600;">Qty: ${escapeHtml(d.qty)}</div>
                    </td>
                    <td><span class="category-pill">${d.category || 'General'}</span></td>
                    <td style="font-size: 12px; color: #475569; max-width: 260px;">"${escapeHtml(d.reason)}"</td>
                    <td><span class="badge badge-success"><i class="fas fa-user-check"></i> ${escapeHtml(d.dispensedBy)}</span></td>
                    <td style="font-size: 12px; color: #64748b; font-family: monospace;">${escapeHtml(d.timestamp)}</td>
                </tr>
            `;
        });
        $('#staff_dispensation_table_tbody').html(html);
        $('#tab_count_dispensations').text(list.length);
    }

    function renderRequisitionsList() {
        const reqs = StaffInvStore.getRequisitions();
        let html = '';
        reqs.forEach((r, idx) => {
            let statusClass = 'req-pending';
            let icon = 'fa-clock';
            if (r.status === 'Dispatched') { statusClass = 'req-dispatched'; icon = 'fa-shipping-fast'; }
            if (r.status === 'Received') { statusClass = 'req-received'; icon = 'fa-check-circle'; }

            html += `
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span class="req-status-pill ${statusClass}">
                                <i class="fas ${icon}"></i> ${escapeHtml(({Pending: 'Awaiting Director Approval', Approved: 'Approved by Director', Rejected: 'Returned by Director', Dispatched: 'Ready to Receive', Received: 'Stock Received'})[r.status] || r.status)}
                            </span>
                            <span style="font-size: 11.5px; font-family: monospace; font-weight: 700; color: #64748b;">${r.id}</span>
                            <span class="badge ${r.priority === 'Urgent' ? 'badge-danger' : 'badge-secondary'}" style="font-size: 10px;">${r.priority}</span>
                        </div>
                        <h4 style="font-size: 14.5px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">
                            ${escapeHtml(r.item)} &bull; ${r.qty} Units
                        </h4>
                        <div style="font-size: 11.5px; color: #64748b;">
                            Destination: <strong>${escapeHtml(r.destination)}</strong> &bull; Ordered: ${escapeHtml(r.time)}
                        </div>
                        <div style="font-size: 11.5px; color: #475569; font-style: italic; margin-top: 4px;">
                            "${escapeHtml(r.notes || 'Routine restock')}"
                            ${r.directorRemarks ? '<br><strong>Director feedback:</strong> ' + escapeHtml(r.directorRemarks) : ''}
                        </div>
                    </div>

                    <div>
                        ${['Approved', 'Dispatched'].includes(r.status) ? `
                            <button class="btn-primary btn-sm" onclick="markRequisitionReceived(${idx})">
                                <i class="fas fa-box-open"></i> Confirm Stock Received
                            </button>
                        ` : (r.status === 'Received' ? `
                            <span style="font-size: 12px; color: #059669; font-weight: 700;"><i class="fas fa-check"></i> Stock Added to Cupboard</span>
                        ` : `
                            <span style="font-size: 12px; color: #b45309; font-style: italic;"><i class="fas fa-hourglass-half"></i> ${r.status === 'Rejected' ? 'Please review the director’s feedback.' : 'Waiting for the director’s approval.'}</span>
                        `)}
                    </div>
                </div>
            `;
        });
        $('#staff_requisitions_container').html(html);
        $('#tab_count_orders').text(reqs.length);
    }

    function updateStaffInvMetrics() {
        const items = StaffInvStore.getItems();
        const low = items.filter(i => i.onHand <= i.minThreshold).length;
        const dispensations = StaffInvStore.getDispensations().length;
        const reqs = StaffInvStore.getRequisitions().filter(r => ['Pending', 'Approved', 'Dispatched'].includes(r.status)).length;

        $('#metric_total_items').text(items.length + ' Lines');
        $('#metric_low_stock').text(low + ' Items');
        $('#metric_dispensed_today').text(dispensations + ' Allocations');
        $('#metric_active_reqs').text(reqs + ' Active');
        $('#tab_count_balances').text(items.length);
    }

    function filterStaffInventory() {
        renderWardInventoryItems();
    }

    function populateItemSelectDropdowns() {
        updateDispenseAvailablePreview();
    }

    function populateChildSelectDropdown() {
        let childOpts = '';
        if (window.HopeHavenChildStore) {
            const children = window.HopeHavenChildStore.getAll();
            children.forEach(c => {
                childOpts += `<option value="${c.name} (${c.id})">${c.name} (${c.id} · ${c.dorm})</option>`;
            });
        } else {
            childOpts = `
                <option value="Maya Cruz (CH-2024-001)">Maya Cruz (CH-2024-001 · St. Clare Cottage)</option>
                <option value="Ahmad Razak (CH-2023-014)">Ahmad Razak (CH-2023-014 · St. Francis Boys Hall)</option>
                <option value="Elijah Liam Ramos (CH-2022-008)">Elijah Liam Ramos (CH-2022-008 · St. Francis Senior)</option>
                <option value="Hannah Grace Morales (CH-2025-032)">Hannah Grace Morales (CH-2025-032 · St. Clare Nursery)</option>
            `;
        }
        $('#dispense_child_select').html(childOpts);
    }

    function findDispenseItem() {
        const value = $('#dispense_item_input').val().trim().toLowerCase();
        const items = StaffInvStore.getItems();
        return items.find(item => item.id.toLowerCase() === value)
            || items.find(item => item.name.toLowerCase() === value);
    }

    function updateDispenseAvailablePreview() {
        document.getElementById('dispense_item_input').setCustomValidity('');
        const item = findDispenseItem();
        if (item) {
            $('#dispense_avail_qty').text(item.onHand + ' ' + item.unit + ' (' + item.location + ')');
            $('#dispense_qty_input').attr('max', item.onHand > 0 ? item.onHand : 1);
        } else {
            $('#dispense_avail_qty').text('Enter an existing item name or inventory ID to check stock');
            $('#dispense_qty_input').removeAttr('max');
        }
    }

    function openDispenseModalForItem(itemId) {
        const item = StaffInvStore.getItems().find(item => item.id === itemId);
        $('#dispense_item_input').val(item ? item.name : '');
        updateDispenseAvailablePreview();
        openStaffInvModal('modal_dispense_to_child');
    }

    function openRequisitionModalForItem(itemId) {
        const items = StaffInvStore.getItems();
        const item = items.find(i => i.id === itemId);
        if (item) {
            $('#req_category_select').val(item.category);
            $('#req_item_input').val(item.name);
            $('#req_destination_input').val(item.location);
        }
        openStaffInvModal('modal_staff_request_supplies');
    }

    function openCountModalForItem(itemId) {
        const item = StaffInvStore.getItems().find(item => item.id === itemId);
        if (!item) return;

        $('#count_item_id').val(item.id);
        $('#count_item_name').val(item.name);
        $('#count_current_qty').val(`${item.onHand} ${item.unit}`);
        $('#count_new_qty').val(item.onHand);
        openStaffInvModal('modal_quick_count');
    }

    // Form Handlers
    function handleAddStockItem(e) {
        e.preventDefault();
        try {
            const item = StaffInvStore.addItem({name: $('#stock_item_name').val(), category: $('#stock_item_category').val(),
                unit: $('#stock_item_unit').val(), onHand: $('#stock_item_qty').val(), minThreshold: $('#stock_item_min').val(),
                location: $('#stock_item_location').val(), batchNo: $('#stock_item_batch').val(), expiry: $('#stock_item_expiry').val()});
            closeStaffInvModal('modal_staff_add_stock');
            e.target.reset();
            renderAllStaffInventory();
            showPortalToast(item.name + ' added to inventory.', true);
        } catch (error) { showPortalToast(error.message, false); }
    }

    function handleStaffRequisitionSubmit(e) {
        e.preventDefault();
        try {
            const request = StaffInvStore.createRequest({item: $('#req_item_input').val(), qty: $('#req_qty_input').val(),
                category: $('#req_category_select').val(), destination: $('#req_destination_input').val(),
                priority: $('#req_priority_select').val(), notes: $('#req_notes_input').val(), requestedBy: 'Nurse Mary'});
            closeStaffInvModal('modal_staff_request_supplies');
            e.target.reset();
            renderAllStaffInventory();
            showPortalToast('Request ' + request.id + ' sent to the director for approval.', true);
        } catch (error) { showPortalToast(error.message, false); }
    }

    function handleChildDispenseSubmit(e) {
        e.preventDefault();
        const childName = $('#dispense_child_select').val();
        const selectedItem = findDispenseItem();
        const qty = Number($('#dispense_qty_input').val());
        const reason = $('#dispense_reason_input').val().trim();
        if (!Number.isInteger(qty) || qty < 1) {
            showPortalToast('Enter a positive whole quantity.', false);
            return;
        }

        const items = StaffInvStore.getItems();
        const item = selectedItem && items.find(i => i.id === selectedItem.id);
        if (!item) { showPortalToast('Enter an existing inventory item name or ID.', false); return; }

        if (item.onHand < qty) {
            alert(`Insufficient stock on hand in ${item.location}. Current quantity is ${item.onHand}.`);
            return;
        }

        // Deduct stock
        item.onHand -= qty;
        StaffInvStore.saveItems(items);

        // Record dispensation
        const dispensations = StaffInvStore.getDispensations();
        dispensations.unshift({
            id: window.HopeHavenSync.nextId(dispensations, 'DSP'),
            childName: childName,
            itemName: item.name,
            qty: `${qty} ${item.unit}`,
            category: item.category,
            reason: reason,
            dispensedBy: 'Nurse Mary',
            timestamp: 'Just now'
        });
        StaffInvStore.saveDispensations(dispensations);

        closeStaffInvModal('modal_dispense_to_child');
        e.target.reset();
        renderAllStaffInventory();

        showPortalToast(
            `Given ${qty}x ${item.name} directly to ${childName}. Ward stock updated!`,
            true,
            'Supply Given to Child',
            'Duty Log Entry'
        );
    }

    function handleQuickCountSubmit(e) {
        e.preventDefault();
        try {
            const item = StaffInvStore.updateCount($('#count_item_id').val(), $('#count_new_qty').val(), $('#count_reason_select').val());
            closeStaffInvModal('modal_quick_count');
            renderAllStaffInventory();
            showPortalToast('Stock count updated for ' + item.name + '.', true);
        } catch (error) { showPortalToast(error.message, false); }
    }

    function markRequisitionReceived(index) {
        const request = StaffInvStore.getRequisitions()[index];
        if (!request) return;
        try {
            StaffInvStore.receiveRequest(request.id);
            renderAllStaffInventory();
            showPortalToast('Received ' + request.qty + ' × ' + request.item + '. Inventory updated.', true);
        } catch (error) { showPortalToast(error.message, false); }
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

    // Tab switching
    $(document).ready(function() {
        $('.tab-btn').on('click', function() {
            const target = $(this).data('tab');
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.tab-content-panel').hide();
            $('#' + target).fadeIn(150);
        });

        renderAllStaffInventory();
        window.HopeHavenSync.subscribe([STAFF_INV_STORAGE_KEY, STAFF_DISPENSE_STORAGE_KEY, STAFF_REQ_STORAGE_KEY, 'hopehaven_children_registry_v2'], function() {
            window.HopeHavenSync.refreshView(renderAllStaffInventory);
        });
    });
</script>
@endpush
