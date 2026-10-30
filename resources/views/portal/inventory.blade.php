@extends('layouts.portal')

@section('title', 'Inventory & Supply Management')
@section('breadcrumb', 'Inventory & Supplies')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/inventory.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/inventory.css')), 0, 12) }}">
@endpush

@section('content')
<!-- Subsystem Header -->
<div class="page-header-card">
    <div>
        
        <h1 class="page-title">Inventory Balances, Suppliers & Welfare Distribution</h1>
        <p class="page-desc">
            Monitor storeroom inventory levels and minimum reorder thresholds, record delivery receipts & issues, manage certified suppliers, and trace supplies distributed to approved welfare requests.
        </p>
    </div>
    <div class="header-action-group">
        <button class="btn-primary" onclick="openModal('modal_stock_receipt')">
            <i class="fas fa-arrow-down"></i> Record Stock Receipt
        </button>
        <button class="btn-outline" onclick="openModal('modal_stock_issue')">
            <i class="fas fa-arrow-up"></i> Record Stock Issue
        </button>
        <button class="btn-outline" onclick="openModal('modal_add_supplier')">
            <i class="fas fa-truck"></i> Add Supplier
        </button>
    </div>
</div>

<!-- Metric KPI Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Estimated Stock Value</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-warehouse"></i>
            </div>
        </div>
        <div class="metric-val">$2,965</div>
        <div class="metric-sub">6 active SKU lines in storeroom</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Low Stock Alerts</span>
            <div class="metric-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #ef4444;">2 Items</div>
        <div class="metric-sub">Below minimum reorder threshold</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Welfare Request Links</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-hands-helping"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #10b981;">2 Distributions</div>
        <div class="metric-sub">Traceable to approved welfare tickets</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Contracted Suppliers</span>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-truck-loading"></i>
            </div>
        </div>
        <div class="metric-val">4 Vendors</div>
        <div class="metric-sub">Food, Medical, Linen & Stationery</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container">
    <button class="tab-btn active" data-tab="tab_stock">
        <i class="fas fa-boxes"></i> Stock Balances & Reorder Thresholds
    </button>
    <button class="tab-btn" data-tab="tab_movements">
        <i class="fas fa-exchange-alt"></i> Receipts & Issues Ledger
    </button>
    <button class="tab-btn" data-tab="tab_welfare_link">
        <i class="fas fa-clipboard-check"></i> Welfare Request Associations (2)
    </button>
    <button class="tab-btn" data-tab="tab_suppliers">
        <i class="fas fa-truck"></i> Suppliers & Categories
    </button>
</div>

<!-- Search & Filter Bar -->
<div class="filter-bar">
    <div class="search-box-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" class="portal-input search-input" data-search-target=".searchable-item" placeholder="Search by item name, SKU code, or supplier...">
    </div>
    <div style="display: flex; gap: 10px;">
        <select class="portal-select category-filter" data-filter-target=".searchable-item">
            <option value="All">All Categories</option>
            <option value="Food & Nutrition">Food & Nutrition</option>
            <option value="Medical & Hygiene">Medical & Hygiene</option>
            <option value="School Supplies">School Supplies</option>
            <option value="Clothing & Bedding">Clothing & Bedding</option>
        </select>
    </div>
</div>

<!-- TAB 1: STOCK BALANCES & THRESHOLDS -->
<div id="tab_stock" class="tab-pane">
    <div class="portal-card">
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Item Description & SKU</th>
                        <th>Category</th>
                        <th>Current Stock Balance</th>
                        <th>Min Threshold / Reorder</th>
                        <th>Health Status</th>
                        <th>Storage Location</th>
                        <th class="action-column" style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="searchable-item" data-category="Food & Nutrition">
                        <td>
                            <strong>Fortified Jasmine Rice (25kg Bag)</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">NUT-RIC-01 &bull; $28.50/bag</div>
                        </td>
                        <td><span class="badge badge-secondary">Food & Nutrition</span></td>
                        <td>
                            <strong>48 bags</strong>
                            <div style="height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 80px;">
                                <div style="height: 100%; background: #10b981; width: 80%;"></div>
                            </div>
                        </td>
                        <td>Min: 20 &bull; Reorder at: <strong>30</strong></td>
                        <td><span class="badge badge-success">Adequate</span></td>
                        <td style="font-size: 12px; color: #64748b;">Central Pantry - Bay 1</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_stock_issue')">Issue</button>
                        </td>
                    </tr>

                    <tr class="searchable-item" data-category="Medical & Hygiene">
                        <td>
                            <strong>Pediatric Paracetamol 250mg</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">MED-PAR-02 &bull; $6.20/bottle</div>
                        </td>
                        <td><span class="badge badge-secondary">Medical & Hygiene</span></td>
                        <td>
                            <strong style="color: #ef4444;">8 bottles</strong>
                            <div style="height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 80px;">
                                <div style="height: 100%; background: #ef4444; width: 32%;"></div>
                            </div>
                        </td>
                        <td>Min: 15 &bull; Reorder at: <strong>25</strong></td>
                        <td><span class="badge badge-danger">Low Stock Alert</span></td>
                        <td style="font-size: 12px; color: #64748b;">Clinic Cabinet A</td>
                        <td style="text-align: right;">
                            <button class="btn-primary btn-sm" onclick="openModal('modal_stock_receipt')">Reorder</button>
                        </td>
                    </tr>

                    <tr class="searchable-item" data-category="School Supplies">
                        <td>
                            <strong>Ruled Exercise Notebooks (Pack of 10)</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">SCH-NOT-03 &bull; $12.00/pack</div>
                        </td>
                        <td><span class="badge badge-secondary">School Supplies</span></td>
                        <td>
                            <strong>120 packs</strong>
                            <div style="height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 80px;">
                                <div style="height: 100%; background: #10b981; width: 100%;"></div>
                            </div>
                        </td>
                        <td>Min: 50 &bull; Reorder at: <strong>80</strong></td>
                        <td><span class="badge badge-success">Adequate</span></td>
                        <td style="font-size: 12px; color: #64748b;">Study Hall Shelf 3</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_stock_issue')">Issue</button>
                        </td>
                    </tr>

                    <tr class="searchable-item" data-category="Clothing & Bedding">
                        <td>
                            <strong>Thermal Fleece Blankets (Single)</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">CLO-BLA-04 &bull; $18.00/pc</div>
                        </td>
                        <td><span class="badge badge-secondary">Clothing & Bedding</span></td>
                        <td>
                            <strong style="color: #f59e0b;">14 pcs</strong>
                            <div style="height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 80px;">
                                <div style="height: 100%; background: #f59e0b; width: 45%;"></div>
                            </div>
                        </td>
                        <td>Min: 25 &bull; Reorder at: <strong>40</strong></td>
                        <td><span class="badge badge-warning">Low Stock</span></td>
                        <td style="font-size: 12px; color: #64748b;">Linen Annex B</td>
                        <td style="text-align: right;">
                            <button class="btn-primary btn-sm" onclick="openModal('modal_stock_receipt')">Reorder</button>
                        </td>
                    </tr>

                    <tr class="searchable-item" data-category="Food & Nutrition">
                        <td>
                            <strong>High-Calcium UHT Milk (1L)</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">NUT-MIL-05 &bull; $2.10/carton</div>
                        </td>
                        <td><span class="badge badge-secondary">Food & Nutrition</span></td>
                        <td>
                            <strong>85 cartons</strong>
                            <div style="height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 80px;">
                                <div style="height: 100%; background: #10b981; width: 90%;"></div>
                            </div>
                        </td>
                        <td>Min: 40 &bull; Reorder at: <strong>60</strong></td>
                        <td><span class="badge badge-success">Adequate</span></td>
                        <td style="font-size: 12px; color: #64748b;">Pantry Chiller 2</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_stock_issue')">Issue</button>
                        </td>
                    </tr>

                    <tr class="searchable-item" data-category="Medical & Hygiene">
                        <td>
                            <strong>Sterile First Aid Gauze Rolls</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">MED-FAK-06 &bull; $9.50/box</div>
                        </td>
                        <td><span class="badge badge-secondary">Medical & Hygiene</span></td>
                        <td>
                            <strong style="color: #ef4444;">4 boxes</strong>
                            <div style="height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 80px;">
                                <div style="height: 100%; background: #ef4444; width: 25%;"></div>
                            </div>
                        </td>
                        <td>Min: 10 &bull; Reorder at: <strong>20</strong></td>
                        <td><span class="badge badge-danger">Critical</span></td>
                        <td style="font-size: 12px; color: #64748b;">Dispensary Emergency Kit</td>
                        <td style="text-align: right;">
                            <button class="btn-primary btn-sm" onclick="openModal('modal_stock_receipt')">Reorder</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 2: RECEIPTS & ISSUES LEDGER -->
<div id="tab_movements" class="tab-pane" style="display: none;">
    <div class="portal-card">
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Movement Type</th>
                        <th>Reference #</th>
                        <th>Item Description</th>
                        <th>Quantity Change</th>
                        <th>Date</th>
                        <th>Source / Destination</th>
                        <th>Welfare Association</th>
                    </tr>
                </thead>
                <tbody id="movements_tbody">
                    <tr>
                        <td><span class="badge badge-success"><i class="fas fa-arrow-down"></i> Receipt</span></td>
                        <td style="font-family: monospace; font-weight: 600;">REC-2026-114</td>
                        <td><strong>Fortified Jasmine Rice (25kg Bag)</strong></td>
                        <td style="font-weight: 700; color: #10b981;">+30 bags</td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-08</td>
                        <td>Evergreen Food Staples Ltd</td>
                        <td><span class="badge badge-secondary">Standard Restock</span></td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Issue</span></td>
                        <td style="font-family: monospace; font-weight: 600;">ISS-2026-089</td>
                        <td><strong>Thermal Fleece Blankets (Single)</strong></td>
                        <td style="font-weight: 700; color: #ef4444;">-8 pcs</td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-14</td>
                        <td>St. Francis Boys Hall (Dorm 2)</td>
                        <td><span class="badge badge-info" style="font-family: monospace;">WR-2026-039</span></td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Issue</span></td>
                        <td style="font-family: monospace; font-weight: 600;">ISS-2026-090</td>
                        <td><strong>Ruled Exercise Notebooks (Pack of 10)</strong></td>
                        <td style="font-weight: 700; color: #ef4444;">-15 packs</td>
                        <td style="font-size: 12px; color: #64748b;">2026-10-15</td>
                        <td>Study Hall Review Group</td>
                        <td><span class="badge badge-info" style="font-family: monospace;">WR-2026-044</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 3: WELFARE REQUEST ASSOCIATIONS -->
<div id="tab_welfare_link" class="tab-pane" style="display: none;">
    <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid #86efac; border-radius: var(--radius-md); padding: 16px; margin-bottom: 20px; font-size: 12.5px; color: #166534;">
        <i class="fas fa-info-circle"></i> <strong>Audit Traceability Standard:</strong> All relief goods, bedding, and specialized medical items issued from central inventory must be associated directly with an approved Welfare Assistance Ticket ID.
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span class="badge badge-info" style="font-family: monospace; font-size: 12px;">Linked: WR-2026-039</span>
                <span style="font-size: 11px; font-family: monospace; color: #64748b;">ISS-2026-089</span>
            </div>
            <h4 style="font-size: 15px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">8x Thermal Fleece Blankets</h4>
            <p style="font-size: 12px; color: var(--dark-muted); margin-bottom: 14px;">Issued for incoming winter seasonal rotation in hillside cottage.</p>

            <div style="background: #f8fafc; padding: 12px; border-radius: 6px; font-size: 12px;">
                <div><span style="color: #64748b;">Beneficiary:</span> <strong>St. Francis Boys Hall (Dorm 2)</strong></div>
                <div><span style="color: #64748b;">Dispatched By:</span> Brother Mark (Pantry Master)</div>
                <div><span style="color: #64748b;">Approval Ref:</span> Sister Sarah (Director Decision)</div>
            </div>
        </div>

        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <span class="badge badge-info" style="font-family: monospace; font-size: 12px;">Linked: WR-2026-044</span>
                <span style="font-size: 11px; font-family: monospace; color: #64748b;">ISS-2026-090</span>
            </div>
            <h4 style="font-size: 15px; font-weight: 800; color: var(--dark); margin-bottom: 4px;">15x Exercise Notebooks & Stationery</h4>
            <p style="font-size: 12px; color: var(--dark-muted); margin-bottom: 14px;">Midterm revision preparation for Grade 5 and Grade 9 scholars.</p>

            <div style="background: #f8fafc; padding: 12px; border-radius: 6px; font-size: 12px;">
                <div><span style="color: #64748b;">Beneficiary:</span> <strong>Elijah Ramos & Study Group</strong></div>
                <div><span style="color: #64748b;">Dispatched By:</span> Mr. David Tan (Lead Educator)</div>
                <div><span style="color: #64748b;">Approval Ref:</span> Academic Support Grant</div>
            </div>
        </div>
    </div>
</div>

<!-- TAB 4: SUPPLIERS & CATEGORIES -->
<div id="tab_suppliers" class="tab-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h4 style="font-size: 15px; font-weight: 800;">Evergreen Food Staples Ltd</h4>
                <span class="badge badge-success">Preferred</span>
            </div>
            <span class="badge badge-secondary" style="margin-top: 6px;">Food & Nutrition</span>
            <div style="font-size: 12px; color: #64748b; margin-top: 14px; display: flex; flex-direction: column; gap: 4px;">
                <div>Contact: <strong>Marcus Vance</strong></div>
                <div>Phone: +1 (555) 234-8901</div>
                <div>Email: orders@evergreenstaples.com</div>
                <div style="color: #10b981; font-weight: 700; margin-top: 4px;">Lead Time: 2 Business Days</div>
            </div>
        </div>

        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h4 style="font-size: 15px; font-weight: 800;">MediCare Community Pharma</h4>
                <span class="badge badge-success">Preferred</span>
            </div>
            <span class="badge badge-secondary" style="margin-top: 6px;">Medical & Hygiene</span>
            <div style="font-size: 12px; color: #64748b; margin-top: 14px; display: flex; flex-direction: column; gap: 4px;">
                <div>Contact: <strong>Dr. Evelyn Tan</strong></div>
                <div>Phone: +1 (555) 345-9012</div>
                <div>Email: wholesale@medicarepharma.org</div>
                <div style="color: #10b981; font-weight: 700; margin-top: 4px;">Lead Time: 3 Business Days</div>
            </div>
        </div>

        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h4 style="font-size: 15px; font-weight: 800;">Apex Educational Goods</h4>
                <span class="badge badge-secondary">Active</span>
            </div>
            <span class="badge badge-secondary" style="margin-top: 6px;">School Supplies</span>
            <div style="font-size: 12px; color: #64748b; margin-top: 14px; display: flex; flex-direction: column; gap: 4px;">
                <div>Contact: <strong>Clara Oswald</strong></div>
                <div>Phone: +1 (555) 456-0123</div>
                <div>Email: sales@apexedu.com</div>
                <div style="color: #10b981; font-weight: 700; margin-top: 4px;">Lead Time: 5 Business Days</div>
            </div>
        </div>

        <div class="portal-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <h4 style="font-size: 15px; font-weight: 800;">Comfort Textiles & Apparel</h4>
                <span class="badge badge-secondary">Active</span>
            </div>
            <span class="badge badge-secondary" style="margin-top: 6px;">Clothing & Bedding</span>
            <div style="font-size: 12px; color: #64748b; margin-top: 14px; display: flex; flex-direction: column; gap: 4px;">
                <div>Contact: <strong>Arthur Dent</strong></div>
                <div>Phone: +1 (555) 567-1234</div>
                <div>Email: support@comforttextiles.net</div>
                <div style="color: #10b981; font-weight: 700; margin-top: 4px;">Lead Time: 4 Business Days</div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 1: RECORD STOCK RECEIPT -->
<div id="modal_stock_receipt" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Record Stock Receipt (Inbound)</h3>
                <p class="modal-sub">Log delivery from supplier or donation receipt into warehouse.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_stock_receipt')">&times;</button>
        </div>

        <form onsubmit="handleStockReceiptSubmit(event)">
            <div class="form-group">
                <label class="form-label">Inventory Item</label>
                <select id="receipt_item_select" class="form-control" required>
                    <option>Fortified Jasmine Rice (25kg Bag)</option>
                    <option>Pediatric Paracetamol 250mg</option>
                    <option>Thermal Fleece Blankets (Single)</option>
                    <option>Ruled Exercise Notebooks (Pack of 10)</option>
                    <option>High-Calcium UHT Milk (1L)</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Quantity Received</label>
                    <input type="number" id="receipt_qty_input" class="form-control" value="25" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Supplier / Merchant</label>
                    <input type="text" id="receipt_supplier_input" class="form-control" value="Evergreen Food Staples Ltd">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Delivery Note / Condition</label>
                <input type="text" class="form-control" placeholder="Invoice #, batch expiry, sealed condition">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_stock_receipt')">Cancel</button>
                <button type="submit" class="btn-primary">Confirm Inbound Stock</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: RECORD STOCK ISSUE -->
<div id="modal_stock_issue" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Record Stock Issue (Outbound)</h3>
                <p class="modal-sub">Distribute supplies to dormitory cottages or approved welfare tickets.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_stock_issue')">&times;</button>
        </div>

        <form onsubmit="event.preventDefault(); closeModal('modal_stock_issue'); showPortalToast('Goods issued from inventory successfully!', true);">
            <div class="form-group">
                <label class="form-label">Item to Disburse</label>
                <select class="form-control">
                    <option>Fortified Jasmine Rice (25kg Bag)</option>
                    <option>Pediatric Paracetamol 250mg</option>
                    <option>Thermal Fleece Blankets (Single)</option>
                    <option>Ruled Exercise Notebooks (Pack of 10)</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Quantity to Issue</label>
                    <input type="number" class="form-control" value="5" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Destination</label>
                    <select class="form-control">
                        <option>St. Clare Cottage (Girls)</option>
                        <option>St. Francis Boys Hall</option>
                        <option>Orphanage Dining Hall / Kitchen</option>
                        <option>Clinic Dispensary</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Associated Welfare Request Ticket ID (Optional)</label>
                <input type="text" class="form-control" placeholder="e.g. WR-2026-042" style="font-family: monospace;">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_stock_issue')">Cancel</button>
                <button type="submit" class="btn-primary">Approve Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: ADD SUPPLIER -->
<div id="modal_add_supplier" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Register Certified Supplier</h3>
                <p class="modal-sub">Add a verified wholesaler or institutional merchant.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_add_supplier')">&times;</button>
        </div>

        <form onsubmit="event.preventDefault(); closeModal('modal_add_supplier'); showPortalToast('Supplier registered in catalog!', true);">
            <div class="form-group">
                <label class="form-label">Supplier / Company Name</label>
                <input type="text" class="form-control" placeholder="e.g. Beacon Medical Logistics" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Supply Category</label>
                    <input type="text" class="form-control" placeholder="Medical / Food / Linen">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input type="text" class="form-control" placeholder="Account Manager">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" placeholder="+1 (555) 000-0000">
                </div>
                <div class="form-group">
                    <label class="form-label">Lead Time (Days)</label>
                    <input type="number" class="form-control" value="3">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_add_supplier')">Cancel</button>
                <button type="submit" class="btn-primary">Save Supplier</button>
            </div>
        </form>
    </div>
</div>
@endsection
