@extends('layouts.portal')

@section('title', 'Staff Ward Inventory & Supply Dispensation')
@section('breadcrumb', 'Workspace | Staff Inventory')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/inventory.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}">
    <style>
        /* Staff Inventory Specific Styles */
        .staff-inv-hero {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 60%, #047857 100%);
            color: #ffffff;
            border-radius: var(--radius-md);
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 18px rgba(6, 78, 59, 0.18);
            position: relative;
            overflow: hidden;
        }

        .staff-inv-hero::after {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .staff-inv-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .staff-inv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .ward-inv-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .ward-inv-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .ward-inv-card.low-stock {
            border-left: 4px solid #ef4444;
            background: linear-gradient(180deg, #fff 0%, #fef2f2 100%);
        }

        .ward-inv-card.good-stock {
            border-left: 4px solid #10b981;
        }

        .qty-badge {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .qty-badge small {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .cabinet-loc-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #475569;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        .action-icon-btn {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .action-icon-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .action-icon-btn.primary {
            background: #10b981;
            color: #ffffff;
            border-color: #10b981;
        }

        .action-icon-btn.primary:hover {
            background: #059669;
        }

        .action-icon-btn.req {
            background: #f59e0b;
            color: #ffffff;
            border-color: #f59e0b;
        }

        .action-icon-btn.req:hover {
            background: #d97706;
        }

        .req-status-pill {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .req-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .req-dispatched { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .req-received { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    </style>
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
                Ward Inventory & Child Supply Dispensation
            </h1>
            <p style="font-size: 13.5px; opacity: 0.92; margin: 0; line-height: 1.5;">
                Caregiver workspace for Residential Ward Alpha & Beta. Check cupboard stock balances, dispense care essentials directly to resident children, log storeroom supply orders, and verify safe stock levels.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <button class="btn-primary" style="background: #ffffff; color: #065f46; border-color: #ffffff; font-weight: 700;" onclick="openStaffInvModal('modal_dispense_to_child')">
                <i class="fas fa-hand-holding-heart"></i> Dispense to Child
            </button>
            <button class="btn-outline" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border-color: rgba(255, 255, 255, 0.35); font-weight: 700;" onclick="openStaffInvModal('modal_staff_request_supplies')">
                <i class="fas fa-cart-plus"></i> Request Storeroom Supplies
            </button>
            <a href="{{ route('inventory') }}" class="btn-outline" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; border-color: rgba(255, 255, 255, 0.25);" title="Switch to Admin Central Inventory Catalog">
                <i class="fas fa-warehouse"></i> Admin Catalog
            </a>
        </div>
    </div>
</div>

<!-- Clearance Level & Identity Ribbon -->
<div class="rbac-banner" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-nurse" style="color: #10b981; font-size: 18px;"></i>
        <div>
            <span style="font-size: 12px; font-weight: 700; color: var(--dark); text-transform: uppercase;">Staff Ward Custodian:</span>
            <span style="font-size: 12px; color: var(--dark-muted); margin-left: 4px;">Duty Caregiver: <strong style="color: #10b981;">Nurse Mary</strong> · Morning Shift (Ward Alpha & Beta Nursery)</span>
        </div>
    </div>
    <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
        Storeroom Dispatch Window: <strong>11:00 AM & 15:00 PM Daily</strong>
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
            <span class="metric-label">Dispensed Today</span>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #2563eb;" id="metric_dispensed_today">6 Allocations</div>
        <div class="metric-sub">Logged to specific residential children</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Storeroom Requisitions</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-dolly"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #d97706;" id="metric_active_reqs">2 En Route</div>
        <div class="metric-sub">Submitted to Storeroom Steward</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container" style="margin-bottom: 20px;">
    <button class="tab-btn active" data-tab="tab_ward_balances">
        <i class="fas fa-boxes"></i> Ward Cabinet Balances (<span id="tab_count_balances">12</span>)
    </button>
    <button class="tab-btn" data-tab="tab_child_dispensations">
        <i class="fas fa-history"></i> Child Dispensation Ledger (<span id="tab_count_dispensations">6</span>)
    </button>
    <button class="tab-btn" data-tab="tab_storeroom_orders">
        <i class="fas fa-truck-loading"></i> Storeroom Requisitions & Tracking (<span id="tab_count_orders">3</span>)
    </button>
    <button class="tab-btn" data-tab="tab_first_aid_checklist">
        <i class="fas fa-medkit"></i> Daily First Aid & Safety Checklist
    </button>
</div>

<!-- Search & Category Filters -->
<div class="filter-bar" style="margin-bottom: 20px;">
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
    <div class="view-mode-toggle" style="margin-left: auto;">
        <button type="button" class="view-mode-btn active" id="btn_view_inv_cards" onclick="toggleStaffInvView('cards')">
            <i class="fas fa-th-large"></i> Cards
        </button>
        <button type="button" class="view-mode-btn" id="btn_view_inv_table" onclick="toggleStaffInvView('table')">
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
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Child Supply Dispensation History</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">
                    Log of medicine, hygiene items, and educational materials allocated directly to resident children by shift staff.
                </p>
            </div>
            <button class="btn-primary btn-sm" onclick="openStaffInvModal('modal_dispense_to_child')">
                <i class="fas fa-plus"></i> New Child Dispensation
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
                        <th>Dispensed By</th>
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
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark); margin: 0;">Staff Caregiver Requisitions to Storeroom</h3>
                <p style="font-size: 12.5px; color: #64748b; margin: 2px 0 0 0;">
                    Track batches requested from central inventory steward Sister Grace. Click "Confirm Received" once items arrive in the ward.
                </p>
            </div>
            <button class="btn-primary btn-sm" style="background: #f59e0b; border-color: #f59e0b;" onclick="openStaffInvModal('modal_staff_request_supplies')">
                <i class="fas fa-paper-plane"></i> Submit Requisition
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
                <div><strong>Storeroom Steward (Brother Leo):</strong><br><code>Ext 104 · +60 12-449 8123</code></div>
                <div style="margin-top: 8px;"><strong>Lead Administrator (Sister Grace):</strong><br><code>Ext 101 · +60 19-204 1188</code></div>
                <div style="margin-top: 8px;"><strong>Sanctuary Dispensary Pharmacy:</strong><br><code>03-2615 5555 (Klinik Kesihatan)</code></div>
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
                <h3 class="modal-title">Submit Ward Supply Requisition</h3>
                <p class="modal-sub">Request bulk restocking from central storeroom steward Sister Grace.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffInvModal('modal_staff_request_supplies')">&times;</button>
        </div>

        <form onsubmit="handleStaffRequisitionSubmit(event)">
            <div class="form-group">
                <label class="form-label">Supply Category *</label>
                <select id="req_category_select" class="form-control" onchange="updateRequisitionItemDropdown()" required>
                    <option value="Medical">Pediatric Medical & First Aid</option>
                    <option value="Hygiene">Baby & Nursery Hygiene</option>
                    <option value="Nutrition">Nutritional Food & Milk Formula</option>
                    <option value="Education">Educational Stationery & Books</option>
                    <option value="Linen">Bedding, Towels & Ward Linens</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Item Needed *</label>
                <select id="req_item_select" class="form-control" required>
                    <!-- Populated dynamically -->
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Quantity Needed *</label>
                    <input type="number" id="req_qty_input" class="form-control" value="2" min="1" max="100" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Delivery Destination Ward *</label>
                    <select id="req_destination_select" class="form-control" required>
                        <option value="Ward Alpha Medicine Cabinet">Ward Alpha Medicine Cabinet</option>
                        <option value="Ward Alpha Nursery Shelf">Ward Alpha Nursery Shelf</option>
                        <option value="Ward Beta Care Cupboard">Ward Beta Care Cupboard</option>
                        <option value="Dispensary Refrigerator">Dispensary Refrigerator</option>
                        <option value="Common Study Locker">Common Study Locker</option>
                    </select>
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
                <label class="form-label">Staff Duty Caregiver Remarks</label>
                <textarea id="req_notes_input" class="form-control" rows="2" placeholder="e.g. 2 children with mild cough; nursery diaper stock down to last pack..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffInvModal('modal_staff_request_supplies')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #f59e0b; border-color: #f59e0b;">
                    <i class="fas fa-paper-plane"></i> Dispatch Requisition
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
                <h3 class="modal-title">Dispense Supply to Resident Child</h3>
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
                    <select id="dispense_item_select" class="form-control" onchange="updateDispenseAvailablePreview()" required>
                        <!-- Populated dynamically from Ward Inventory -->
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity Dispensed *</label>
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
                    <i class="fas fa-check"></i> Confirm Dispensation
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
                    <input type="number" id="count_new_qty" class="form-control" min="0" max="500" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Adjustment Reason *</label>
                <select id="count_reason_select" class="form-control" required>
                    <option value="Routine Shift Physical Verification">Routine Shift Physical Verification</option>
                    <option value="Restocked from Earlier Requisition">Restocked from Earlier Requisition</option>
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

@endsection

@push('scripts')
<script src="{{ asset('js/portal/child-welfare.js') }}"></script>
<script>
    // Staff Ward Inventory Engine
    const STAFF_INV_STORAGE_KEY = 'hopehaven_staff_inventory_v1';
    const STAFF_DISPENSE_STORAGE_KEY = 'hopehaven_staff_dispensations_v1';
    const STAFF_REQ_STORAGE_KEY = 'hopehaven_staff_requisitions_v1';

    // Seed Data
    const DEFAULT_WARD_ITEMS = [
        {
            id: 'WINV-001',
            name: 'Pediatric Paracetamol Syrup (120mg/5ml)',
            category: 'Medical',
            location: 'Ward Alpha Medicine Cabinet',
            onHand: 4,
            minThreshold: 3,
            unit: 'bottles (100ml)',
            batchNo: 'BAT-2026-MED9',
            expiry: '2027-04-30'
        },
        {
            id: 'WINV-002',
            name: 'Antiseptic Wet Wipes (Alcohol-Free)',
            category: 'Hygiene',
            location: 'Ward Alpha Nursery Shelf',
            onHand: 2,
            minThreshold: 5,
            unit: 'packs (80 sheets)',
            batchNo: 'WIP-8821',
            expiry: '2027-08-15'
        },
        {
            id: 'WINV-003',
            name: 'Enfamil Stage 2 Baby Formula Powder',
            category: 'Nutrition',
            location: 'Ward Alpha Nursery Shelf',
            onHand: 3,
            minThreshold: 2,
            unit: 'cans (900g)',
            batchNo: 'ENF-9092',
            expiry: '2027-02-10'
        },
        {
            id: 'WINV-004',
            name: 'Sterile Adhesive Gauze & Plasters (Assorted)',
            category: 'Medical',
            location: 'Ward Alpha Medicine Cabinet',
            onHand: 14,
            minThreshold: 10,
            unit: 'strips/patches',
            batchNo: 'GAU-4102',
            expiry: '2028-01-01'
        },
        {
            id: 'WINV-005',
            name: 'Salbutamol Metered Dose Inhaler (100mcg)',
            category: 'Medical',
            location: 'Dispensary Refrigerator',
            onHand: 1,
            minThreshold: 2,
            unit: 'inhaler canisters',
            batchNo: 'SAL-1029',
            expiry: '2026-12-31'
        },
        {
            id: 'WINV-006',
            name: 'Comfort Baby Diapers (Size L · 9-14kg)',
            category: 'Hygiene',
            location: 'Ward Alpha Nursery Shelf',
            onHand: 6,
            minThreshold: 10,
            unit: 'packs (44 pcs)',
            batchNo: 'DIA-7721',
            expiry: '2028-06-01'
        },
        {
            id: 'WINV-007',
            name: 'Pediatric Oral Rehydration Salts (ORS Sachet)',
            category: 'Medical',
            location: 'Ward Alpha Medicine Cabinet',
            onHand: 18,
            minThreshold: 12,
            unit: 'sachets',
            batchNo: 'ORS-6612',
            expiry: '2027-10-20'
        },
        {
            id: 'WINV-008',
            name: 'Single Line Exercise Books (A4 80pg)',
            category: 'Education',
            location: 'Common Study Locker',
            onHand: 24,
            minThreshold: 15,
            unit: 'books',
            batchNo: 'ED-BK-009',
            expiry: 'N/A'
        },
        {
            id: 'WINV-009',
            name: 'Color Pencils & Drawing Pads Set',
            category: 'Education',
            location: 'Common Study Locker',
            onHand: 8,
            minThreshold: 5,
            unit: 'sets',
            batchNo: 'ART-3320',
            expiry: 'N/A'
        },
        {
            id: 'WINV-010',
            name: 'Baby Body Wash & Tear-Free Shampoo (500ml)',
            category: 'Hygiene',
            location: 'Ward Beta Care Cupboard',
            onHand: 5,
            minThreshold: 3,
            unit: 'bottles',
            batchNo: 'HYG-5002',
            expiry: '2027-09-10'
        },
        {
            id: 'WINV-011',
            name: 'Fitted Single Bed Linens & Pillowcases',
            category: 'Linen',
            location: 'Ward Beta Care Cupboard',
            onHand: 12,
            minThreshold: 8,
            unit: 'sets',
            batchNo: 'LIN-2201',
            expiry: 'N/A'
        },
        {
            id: 'WINV-012',
            name: 'Dettol Hospital-Grade Disinfectant (1L)',
            category: 'Hygiene',
            location: 'Ward Beta Care Cupboard',
            onHand: 3,
            minThreshold: 2,
            unit: 'bottles',
            batchNo: 'DET-9921',
            expiry: '2027-11-01'
        }
    ];

    const DEFAULT_DISPENSATIONS = [
        {
            id: 'DSP-2026-001',
            childName: 'Ahmad Razak (CH-2023-014)',
            itemName: 'Pediatric Paracetamol Syrup (120mg/5ml)',
            qty: '1 bottle (5ml dose)',
            category: 'Medical',
            reason: 'Mild evening fever (37.9°C); temperature reduced to 36.8°C within 1 hour.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 08:45 AM'
        },
        {
            id: 'DSP-2026-002',
            childName: 'Maya Cruz (CH-2024-001)',
            itemName: 'Color Pencils & Drawing Pads Set',
            qty: '1 set',
            category: 'Education',
            reason: 'Grade 3 Visual Arts project preparation; mentoring session with Sister Teresa.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 09:15 AM'
        },
        {
            id: 'DSP-2026-003',
            childName: 'Hannah Grace Morales (CH-2025-032)',
            itemName: 'Comfort Baby Diapers (Size L · 9-14kg)',
            qty: '2 pcs',
            category: 'Hygiene',
            reason: 'Routine nursery morning change in St. Clare Cottage.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 10:00 AM'
        },
        {
            id: 'DSP-2026-004',
            childName: 'Kenji Sato (CH-2025-019)',
            itemName: 'Single Line Exercise Books (A4 80pg)',
            qty: '2 books',
            category: 'Education',
            reason: 'School mathematics and Bahasa Melayu class note-taking replenishment.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 10:30 AM'
        },
        {
            id: 'DSP-2026-005',
            childName: 'Maya Cruz (CH-2024-001)',
            itemName: 'Salbutamol Metered Dose Inhaler (100mcg)',
            qty: '1 canister',
            category: 'Medical',
            reason: 'Routine morning pulmonary check; inhaler replaced for school sports bag.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 11:00 AM'
        },
        {
            id: 'DSP-2026-006',
            childName: 'Elijah Liam Ramos (CH-2022-008)',
            itemName: 'Sterile Adhesive Gauze & Plasters (Assorted)',
            qty: '2 patches',
            category: 'Medical',
            reason: 'Minor scrape dressing on left knee from basketball playground playtime.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 11:30 AM'
        }
    ];

    const DEFAULT_REQUISITIONS = [
        {
            id: 'REQ-2026-104',
            item: 'Antiseptic Wet Wipes (Alcohol-Free)',
            qty: 5,
            category: 'Hygiene',
            destination: 'Ward Alpha Nursery Shelf',
            priority: 'Urgent',
            notes: 'Critical nursery stock depleted; required before evening feed.',
            status: 'Dispatched',
            statusLabel: 'En Route with Steward',
            time: 'Today 10:15 AM'
        },
        {
            id: 'REQ-2026-105',
            item: 'Salbutamol Metered Dose Inhaler (100mcg)',
            qty: 2,
            category: 'Medical',
            destination: 'Dispensary Refrigerator',
            priority: 'Urgent',
            notes: 'Down to single backup unit; multiple asthmatic wards.',
            status: 'Approved',
            statusLabel: 'Packed in Storeroom',
            time: 'Today 11:05 AM'
        },
        {
            id: 'REQ-2026-102',
            item: 'Enfamil Stage 2 Baby Formula Powder',
            qty: 4,
            category: 'Nutrition',
            destination: 'Ward Alpha Nursery Shelf',
            priority: 'Normal',
            notes: 'Weekly buffer replenishment for toddlers.',
            status: 'Received',
            statusLabel: 'Received by Nurse Mary',
            time: 'Yesterday 15:30 PM'
        }
    ];

    // Data Access Store
    const StaffInvStore = {
        getItems: function() {
            try {
                const data = localStorage.getItem(STAFF_INV_STORAGE_KEY);
                return data ? JSON.parse(data) : DEFAULT_WARD_ITEMS;
            } catch (e) {
                return DEFAULT_WARD_ITEMS;
            }
        },
        saveItems: function(items) {
            localStorage.setItem(STAFF_INV_STORAGE_KEY, JSON.stringify(items));
        },
        getDispensations: function() {
            try {
                const data = localStorage.getItem(STAFF_DISPENSE_STORAGE_KEY);
                return data ? JSON.parse(data) : DEFAULT_DISPENSATIONS;
            } catch (e) {
                return DEFAULT_DISPENSATIONS;
            }
        },
        saveDispensations: function(list) {
            localStorage.setItem(STAFF_DISPENSE_STORAGE_KEY, JSON.stringify(list));
        },
        getRequisitions: function() {
            try {
                const data = localStorage.getItem(STAFF_REQ_STORAGE_KEY);
                return data ? JSON.parse(data) : DEFAULT_REQUISITIONS;
            } catch (e) {
                return DEFAULT_REQUISITIONS;
            }
        },
        saveRequisitions: function(reqs) {
            localStorage.setItem(STAFF_REQ_STORAGE_KEY, JSON.stringify(reqs));
        }
    };

    let currentViewMode = 'cards';

    function toggleStaffInvView(mode) {
        currentViewMode = mode;
        $('.view-mode-btn').removeClass('active');
        if (mode === 'cards') {
            $('#btn_view_inv_cards').addClass('active');
            $('#staff_inv_cards_container').show();
            $('#staff_inv_table_container').hide();
        } else {
            $('#btn_view_inv_table').addClass('active');
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
                        <button class="action-icon-btn primary" style="flex: 1;" onclick="openDispenseModalForItem('${item.id}')" title="Dispense to Child">
                            <i class="fas fa-hand-holding-heart"></i> Dispense
                        </button>
                        <button class="action-icon-btn req" onclick="openRequisitionModalForItem('${item.id}')" title="Request Reorder from Storeroom">
                            <i class="fas fa-cart-plus"></i> Reorder
                        </button>
                        <button class="action-icon-btn" onclick="openCountModalForItem('${item.id}')" title="Adjust Physical Count">
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
                            <i class="fas fa-hand-holding-heart"></i> Dispense
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
                                <i class="fas ${icon}"></i> ${r.statusLabel || r.status}
                            </span>
                            <span style="font-size: 11.5px; font-family: monospace; font-weight: 700; color: #64748b;">${r.id}</span>
                            <span class="badge ${r.priority === 'Urgent' ? 'badge-danger' : 'badge-secondary'}" style="font-size: 10px;">${r.priority}</span>
                        </div>
                        <h4 style="font-size: 14.5px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">
                            ${escapeHtml(r.item)} &bull; ${r.qty} Units
                        </h4>
                        <div style="font-size: 11.5px; color: #64748b;">
                            Destination: <strong>${escapeHtml(r.destination)}</strong> &bull; Ordered: ${r.time}
                        </div>
                        <div style="font-size: 11.5px; color: #475569; font-style: italic; margin-top: 4px;">
                            "${escapeHtml(r.notes || 'Routine restock')}"
                        </div>
                    </div>

                    <div>
                        ${r.status === 'Dispatched' ? `
                            <button class="btn-primary btn-sm" onclick="markRequisitionReceived(${idx})">
                                <i class="fas fa-box-open"></i> Confirm Stock Received
                            </button>
                        ` : (r.status === 'Received' ? `
                            <span style="font-size: 12px; color: #059669; font-weight: 700;"><i class="fas fa-check"></i> Stock Added to Cupboard</span>
                        ` : `
                            <span style="font-size: 12px; color: #b45309; font-style: italic;"><i class="fas fa-hourglass-half"></i> Awaiting Steward Packing</span>
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
        const reqs = StaffInvStore.getRequisitions().filter(r => r.status !== 'Received').length;

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
        const items = StaffInvStore.getItems();
        let dispenseOpts = '';
        items.forEach(i => {
            dispenseOpts += `<option value="${i.id}" data-unit="${i.unit}" data-onhand="${i.onHand}">${i.name} (${i.onHand} in ${i.location.split(' ')[0]})</option>`;
        });
        $('#dispense_item_select').html(dispenseOpts);
        updateDispenseAvailablePreview();
        updateRequisitionItemDropdown();
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

    function updateDispenseAvailablePreview() {
        const selectedId = $('#dispense_item_select').val();
        const items = StaffInvStore.getItems();
        const item = items.find(i => i.id === selectedId);
        if (item) {
            $('#dispense_avail_qty').text(`${item.onHand} ${item.unit} (${item.location})`);
            $('#dispense_qty_input').attr('max', item.onHand > 0 ? item.onHand : 1);
        }
    }

    function updateRequisitionItemDropdown() {
        const cat = $('#req_category_select').val();
        const items = StaffInvStore.getItems().filter(i => !cat || i.category === cat);
        let html = '';
        items.forEach(i => {
            html += `<option value="${i.name}">${i.name} [Current Ward Bal: ${i.onHand} ${i.unit}]</option>`;
        });
        $('#req_item_select').html(html);
    }

    function openDispenseModalForItem(itemId) {
        $('#dispense_item_select').val(itemId);
        updateDispenseAvailablePreview();
        openStaffInvModal('modal_dispense_to_child');
    }

    function openRequisitionModalForItem(itemId) {
        const items = StaffInvStore.getItems();
        const item = items.find(i => i.id === itemId);
        if (item) {
            $('#req_category_select').val(item.category);
            updateRequisitionItemDropdown();
            $('#req_item_select').val(item.name);
            $('#req_destination_select').val(item.location);
        }
        openStaffInvModal('modal_staff_request_supplies');
    }

    function openCountModalForItem(itemId) {
        const items = StaffInvStore.getItems();
        const item = items.find(i => i.id === itemId);
        if (!item) return;

        $('#count_item_id').val(item.id);
        $('#count_item_name').val(item.name);
        $('#count_current_qty').val(`${item.onHand} ${item.unit}`);
        $('#count_new_qty').val(item.onHand);
        openStaffInvModal('modal_quick_count');
    }

    // Form Handlers
    function handleStaffRequisitionSubmit(e) {
        e.preventDefault();
        const itemName = $('#req_item_select').val();
        const qty = parseInt($('#req_qty_input').val()) || 1;
        const category = $('#req_category_select').val();
        const destination = $('#req_destination_select').val();
        const priority = $('#req_priority_select').val();
        const notes = $('#req_notes_input').val().trim();

        const reqs = StaffInvStore.getRequisitions();
        const newReq = {
            id: `REQ-2026-${Math.floor(110 + Math.random() * 900)}`,
            item: itemName,
            qty: qty,
            category: category,
            destination: destination,
            priority: priority,
            notes: notes || 'Caregiver routine requisition',
            status: 'Pending',
            statusLabel: 'Pending Steward Approval',
            time: 'Just now'
        };

        reqs.unshift(newReq);
        StaffInvStore.saveRequisitions(reqs);

        closeStaffInvModal('modal_staff_request_supplies');
        e.target.reset();
        renderAllStaffInventory();

        showPortalToast(
            `Requisition for ${qty}x ${itemName} dispatched to Storeroom Steward Brother Leo!`,
            true,
            'Storeroom Requisition Dispatched',
            'Ward Alpha & Beta'
        );
    }

    function handleChildDispenseSubmit(e) {
        e.preventDefault();
        const childName = $('#dispense_child_select').val();
        const itemId = $('#dispense_item_select').val();
        const qty = parseInt($('#dispense_qty_input').val()) || 1;
        const reason = $('#dispense_reason_input').val().trim();

        const items = StaffInvStore.getItems();
        const item = items.find(i => i.id === itemId);
        if (!item) return;

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
            id: `DSP-2026-${String(dispensations.length + 1).padStart(3, '0')}`,
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
            `Dispensed ${qty}x ${item.name} directly to ${childName}. Ward stock updated!`,
            true,
            'Supply Dispensed to Child',
            'Duty Log Entry'
        );
    }

    function handleQuickCountSubmit(e) {
        e.preventDefault();
        const itemId = $('#count_item_id').val();
        const newQty = parseInt($('#count_new_qty').val());
        const reason = $('#count_reason_select').val();

        const items = StaffInvStore.getItems();
        const item = items.find(i => i.id === itemId);
        if (!item) return;

        item.onHand = Math.max(0, newQty);
        StaffInvStore.saveItems(items);

        closeStaffInvModal('modal_quick_count');
        renderAllStaffInventory();

        showPortalToast(
            `Stock count for ${item.name} adjusted to ${item.onHand} (${reason}).`,
            true,
            'Physical Count Certified',
            'Ward Cupboard Audit'
        );
    }

    function markRequisitionReceived(index) {
        const reqs = StaffInvStore.getRequisitions();
        const req = reqs[index];
        if (!req) return;

        req.status = 'Received';
        req.statusLabel = 'Received by Nurse Mary';
        StaffInvStore.saveRequisitions(reqs);

        // Replenish item in ward stock
        const items = StaffInvStore.getItems();
        const matchedItem = items.find(i => i.name.toLowerCase() === req.item.toLowerCase());
        if (matchedItem) {
            matchedItem.onHand += req.qty;
            StaffInvStore.saveItems(items);
        }

        renderAllStaffInventory();
        showPortalToast(
            `Received ${req.qty}x ${req.item}! Added to ${req.destination}.`,
            true,
            'Supplies Received & Stocked',
            'Ward Restock Complete'
        );
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
    });
</script>
@endpush
