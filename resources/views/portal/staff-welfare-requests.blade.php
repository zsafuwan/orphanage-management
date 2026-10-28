@extends('layouts.portal')

@section('title', 'Staff Support Requests & Requests')
@section('breadcrumb', 'Workspace | Staff Support Requests')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/welfare-requests.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/welfare-requests.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/dashboard.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/embedded/portal-staff-welfare-requests-1.css') }}?v={{ substr(hash_file('sha256', public_path('css/embedded/portal-staff-welfare-requests-1.css')), 0, 12) }}">
@endpush

@section('content')

<!-- Staff Hero Header -->
<div class="staff-welfare-hero">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div style="max-width: 680px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; margin-bottom: 10px; letter-spacing: 0.5px; text-transform: uppercase;">
                <i class="fas fa-hand-holding-heart"></i> Staff Caregiver Request Portal · Subsystem 3
            </div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                Child Support Requests
            </h1>
            <p style="font-size: 13.5px; opacity: 0.92; margin: 0; line-height: 1.5;">
                Caregiver workspace to submit specialized medical, dental, schooling, and clothing aid requests for resident children. All submissions are automatically routed to Sister Grace for administrative approval.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <button class="btn-primary" style="background: #ffffff; color: #1e40af; border-color: #ffffff; font-weight: 700;" onclick="openStaffWelfareModal('modal_staff_create_request')">
                <i class="fas fa-plus"></i> Submit Welfare Request
            </button>
        </div>
    </div>
</div>

<!-- Workflow Notice Banner -->
<div class="rbac-workflow-ribbon">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-shield-alt" style="font-size: 18px; color: #2563eb;"></i>
        <div>
            <strong>Approval Rules:</strong>
            Staff duty members originate welfare aid requests. 
            <strong style="color: #059669;">Sister Grace (Orphanage Executive Director)</strong> holds the exclusive authority to approve funding payment or draw central warehouse inventory.
        </div>
    </div>
    <span class="badge badge-info" style="font-size: 11px;">Viewing as Nurse Mary</span>
</div>

<!-- Metric KPI Cards -->
<div class="metrics-grid" style="margin-bottom: 24px;">
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">My Submitted Requests</span>
            <div class="metric-icon" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
        <div class="metric-val" id="kpi_staff_total_requests">5 Requests</div>
        <div class="metric-sub">Logged across residential wards</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Awaiting Admin Approval</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #d97706;" id="kpi_staff_pending_requests">2 Pending</div>
        <div class="metric-sub">Under Sister Grace's executive review</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Authorized & Funded</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #059669;" id="kpi_staff_approved_requests">2 Approved</div>
        <div class="metric-sub">Budgets disbursed or items issued</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Review Turnaround</span>
            <div class="metric-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                <i class="fas fa-history"></i>
            </div>
        </div>
        <div class="metric-val">~24 Hours</div>
        <div class="metric-sub">Emergency cases fast-tracked in 2h</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container" style="margin-bottom: 20px;">
    <button class="tab-btn active" data-filter="all" onclick="filterStaffRequestsByTab('all', this)">
        <i class="fas fa-list-alt"></i> All Requests (<span id="count_tab_all">5</span>)
    </button>
    <button class="tab-btn" data-filter="Pending Admin Approval" onclick="filterStaffRequestsByTab('Pending Admin Approval', this)">
        <i class="fas fa-clock"></i> Awaiting Admin Approval (<span id="count_tab_pending">2</span>)
    </button>
    <button class="tab-btn" data-filter="Approved" onclick="filterStaffRequestsByTab('Approved', this)">
        <i class="fas fa-check-double"></i> Approved by Sister Grace (<span id="count_tab_approved">2</span>)
    </button>
    <button class="tab-btn" data-filter="Rejected" onclick="filterStaffRequestsByTab('Rejected', this)">
        <i class="fas fa-times-circle"></i> Denied / Needs Revision (<span id="count_tab_rejected">1</span>)
    </button>
</div>

<!-- Search & Urgency Filter Bar -->
<div class="filter-bar staff-record-filters" style="margin-bottom: 20px;">
    <div class="search-box-wrapper">
        <i class="fas fa-search search-icon" aria-hidden="true"></i>
        <input type="text" id="staff_req_search" class="search-input" aria-label="Search welfare requests" placeholder="Search by ticket #, child recipient, or aid title..." oninput="renderStaffRequests()">
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_req_urgency_filter" class="filter-select" aria-label="Filter by urgency" onchange="renderStaffRequests()">
            <option value="all">All Urgency Tiers</option>
            <option value="Emergency">Emergency (Immediate 24h)</option>
            <option value="High">High Urgency</option>
            <option value="Standard">Standard Care</option>
        </select>
    </div>

    <div class="filter-select-wrapper">
        <select id="staff_req_category_filter" class="filter-select" aria-label="Filter by category" onchange="renderStaffRequests()">
            <option value="all">All Categories</option>
            <option value="Medical / Surgical">Medical / Surgical</option>
            <option value="Education & Tuition">Education & Schooling</option>
            <option value="Clothing & Bedding">Clothing & Bedding</option>
            <option value="Nutrition & Diet">Nutrition & Supplements</option>
        </select>
    </div>
</div>

<!-- Staff Requests Cards Container -->
<div id="staff_requests_container">
    <!-- Populated dynamically by JavaScript -->
</div>

<!-- ==========================================================================
     MODAL 1: SUBMIT NEW WELFARE REQUISITION (STAFF ORIGINATION)
     ========================================================================== -->
<div id="modal_staff_create_request" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 620px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Submit Child Welfare Request</h3>
                <p class="modal-sub">Originates from shift caregiver for administrative financial approval.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffWelfareModal('modal_staff_create_request')">&times;</button>
        </div>

        <form onsubmit="handleStaffSubmitRequestForm(event)">
            <div class="form-group">
                <label class="form-label">Beneficiary Resident Child *</label>
                <select id="req_child_select" class="form-control" required>
                    <!-- Populated dynamically from HopeHavenChildStore -->
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Request Title *</label>
                <input type="text" id="req_title_input" class="form-control" placeholder="e.g. Specialized Vision Eyeglasses & Scratch-Resistant Lenses" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Welfare Category *</label>
                    <select id="req_cat_input" class="form-control" required>
                        <option value="Medical / Surgical">Medical & Pediatric Surgery</option>
                        <option value="Education & Tuition">Education, Books & Tuition</option>
                        <option value="Clothing & Bedding">Clothing, Footwear & Bedding</option>
                        <option value="Nutrition & Diet">Nutritional Therapy & Formula</option>
                        <option value="Assistive Equipment">Specialized Mobility & Assistive Tech</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Urgency Tier *</label>
                    <select id="req_urgency_input" class="form-control" required>
                        <option value="Emergency">Emergency (Immediate 24h Review)</option>
                        <option value="High" selected>High Urgency (Needs Action in 48h)</option>
                        <option value="Standard">Standard Routine Care (Within 7 Days)</option>
                    </select>
                </div>
            </div>

            <!-- Itemized Line Items -->
            <div style="margin: 16px 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label class="form-label" style="margin: 0;">
                        <i class="fas fa-list-ul"></i> Itemized Expense Breakdown ($ USD) *
                    </label>
                    <button type="button" class="btn-outline btn-sm" onclick="addRequisitionItemRow()" style="font-size: 11px;">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div id="requisition_items_container">
                    <div class="req-line-item-row" style="display: grid; grid-template-columns: 3fr 1fr auto; gap: 8px; margin-bottom: 8px;">
                        <input type="text" class="form-control line-item-name" placeholder="Item description (e.g. Optometry prescription frame)" value="Pediatric Anti-Reflective Frame" required>
                        <input type="number" step="0.5" class="form-control line-item-cost" placeholder="Cost $" value="65.00" oninput="calculateRequisitionTotal()" required>
                        <button type="button" style="background: none; border: none; color: #ef4444; font-size: 18px; cursor: pointer;" onclick="$(this).closest('.req-line-item-row').remove(); calculateRequisitionTotal();">&times;</button>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: #eff6ff; padding: 10px 14px; border-radius: 8px; border: 1px solid #bfdbfe; margin-top: 10px;">
                    <span style="font-size: 12px; font-weight: 700; color: #1e40af;">Total Estimated Request Budget:</span>
                    <strong id="req_total_cost_preview" style="font-size: 16px; color: #2563eb;">$65.00 USD</strong>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Caregiver Justification & Clinical Rationale *</label>
                <textarea id="req_justification_input" class="form-control" rows="3" placeholder="Provide background circumstances, caregiver observation, and why this expenditure cannot be deferred..." required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffWelfareModal('modal_staff_create_request')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #2563eb; border-color: #2563eb;">
                    <i class="fas fa-paper-plane"></i> Submit to Sister Grace for Approval
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let activeFilterTab = 'all';

    function openStaffWelfareModal(id) {
        $('#' + id).css('display', 'flex').hide().fadeIn(200);
    }

    function closeStaffWelfareModal(id) {
        $('#' + id).fadeOut(150);
    }

    function filterStaffRequestsByTab(status, btn) {
        activeFilterTab = status;
        $('.tab-btn').removeClass('active');
        $(btn).addClass('active');
        renderStaffRequests();
    }

    function addRequisitionItemRow() {
        const html = `
            <div class="req-line-item-row" style="display: grid; grid-template-columns: 3fr 1fr auto; gap: 8px; margin-bottom: 8px;">
                <input type="text" class="form-control line-item-name" placeholder="Item description" required>
                <input type="number" step="0.5" class="form-control line-item-cost" placeholder="Cost $" value="30.00" oninput="calculateRequisitionTotal()" required>
                <button type="button" style="background: none; border: none; color: #ef4444; font-size: 18px; cursor: pointer;" onclick="$(this).closest('.req-line-item-row').remove(); calculateRequisitionTotal();">&times;</button>
            </div>
        `;
        $('#requisition_items_container').append(html);
        calculateRequisitionTotal();
    }

    function calculateRequisitionTotal() {
        let total = 0;
        $('#requisition_items_container .req-line-item-row').each(function() {
            const cost = parseFloat($(this).find('.line-item-cost').val()) || 0;
            total += cost;
        });
        $('#req_total_cost_preview').text('$' + total.toFixed(2) + ' USD');
    }

    function renderStaffRequests() {
        if (!window.HopeHavenWelfareStore) return;

        const all = window.HopeHavenWelfareStore.getAll();
        const search = ($('#staff_req_search').val() || '').toLowerCase().trim();
        const urgencyFilter = $('#staff_req_urgency_filter').val() || 'all';
        const categoryFilter = $('#staff_req_category_filter').val() || 'all';

        // Update counts
        const pendingCount = all.filter(r => r.status === 'Pending Admin Approval').length;
        const approvedCount = all.filter(r => r.status === 'Approved').length;
        const rejectedCount = all.filter(r => r.status === 'Rejected').length;

        $('#count_tab_all').text(all.length);
        $('#count_tab_pending').text(pendingCount);
        $('#count_tab_approved').text(approvedCount);
        $('#count_tab_rejected').text(rejectedCount);

        $('#kpi_staff_total_requests').text(all.length + ' Requests');
        $('#kpi_staff_pending_requests').text(pendingCount + ' Pending');
        $('#kpi_staff_approved_requests').text(approvedCount + ' Approved');

        const filtered = all.filter(r => {
            const matchesTab = activeFilterTab === 'all' || r.status === activeFilterTab;
            const matchesSearch = !search || 
                r.id.toLowerCase().includes(search) || 
                r.title.toLowerCase().includes(search) || 
                r.beneficiaryName.toLowerCase().includes(search);
            const matchesUrgency = urgencyFilter === 'all' || r.urgency === urgencyFilter;
            const matchesCategory = categoryFilter === 'all' || r.category === categoryFilter;
            return matchesTab && matchesSearch && matchesUrgency && matchesCategory;
        });

        let html = '';
        if (filtered.length === 0) {
            html = `
                <div class="portal-card" style="padding: 40px; text-align: center; color: #94a3b8;">
                    <i class="fas fa-clipboard-list" style="font-size: 32px; margin-bottom: 12px; color: #cbd5e1;"></i>
                    <h4 style="font-size: 15px; color: #475569; margin-bottom: 4px;">No welfare requests found</h4>
                    <p style="font-size: 12.5px; margin: 0;">Try adjusting your search criteria or submit a new welfare request above.</p>
                </div>
            `;
        } else {
            filtered.forEach(r => {
                let statusBadge = '';
                let borderHighlight = '';
                let decisionBanner = '';

                if (r.status === 'Pending Admin Approval') {
                    statusBadge = `<span class="req-status-pill status-pending-admin"><i class="fas fa-hourglass-half"></i> Pending Sister Grace Approval</span>`;
                    borderHighlight = 'border-left: 4px solid #f59e0b;';
                    decisionBanner = `
                        <div style="background: #fffbeb; border: 1px dashed #fcd34d; border-radius: 8px; padding: 10px 14px; margin-top: 14px; font-size: 12px; color: #92400e; display: flex; align-items: center; justify-content: space-between;">
                            <span><i class="fas fa-info-circle"></i> In Admin Review Queue. <strong>Staff cannot approve</strong> &mdash; awaiting Sister Grace's approval.</span>
                            <span style="font-weight: 700;">Est. Decision: Today</span>
                        </div>
                    `;
                } else if (r.status === 'Approved') {
                    statusBadge = `<span class="req-status-pill status-approved-admin"><i class="fas fa-check-circle"></i> Approved by Admin</span>`;
                    borderHighlight = 'border-left: 4px solid #10b981;';
                    const dec = r.adminDecision || {};
                    decisionBanner = `
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 14px; margin-top: 14px; font-size: 12px; color: #166534;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                <strong><i class="fas fa-stamp"></i> Authorized by ${escapeHtml(dec.approvedBy || 'Sister Grace')}</strong>
                                <span style="font-family: monospace; font-size: 11px;">Ref: ${dec.disbursementRef || 'DISB-2026'} &bull; ${dec.decisionDate || 'Approved'}</span>
                            </div>
                            <div>Funding Allocated: <strong>${escapeHtml(dec.fundingSource || 'Orphanage Fund')}</strong></div>
                            <div style="margin-top: 4px; font-style: italic;">"${escapeHtml(dec.remarks || 'Approved for payment.')}"</div>
                        </div>
                    `;
                } else if (r.status === 'Rejected') {
                    statusBadge = `<span class="req-status-pill status-rejected-admin"><i class="fas fa-times-circle"></i> Request Denied</span>`;
                    borderHighlight = 'border-left: 4px solid #ef4444;';
                    const dec = r.adminDecision || {};
                    decisionBanner = `
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; margin-top: 14px; font-size: 12px; color: #991b1b;">
                            <div style="font-weight: 700; margin-bottom: 4px;"><i class="fas fa-comment-slash"></i> Sister Grace's Assessment:</div>
                            <div style="font-style: italic;">"${escapeHtml(dec.remarks || 'Declined based on institutional review.')}"</div>
                        </div>
                    `;
                }

                // Line items pill preview
                let linesPreview = '';
                if (r.lineItems && r.lineItems.length > 0) {
                    linesPreview = '<div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">';
                    r.lineItems.forEach(li => {
                        linesPreview += `<span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 11.5px; color: #475569;">${escapeHtml(li.item)} ($${li.cost})</span>`;
                    });
                    linesPreview += '</div>';
                }

                html += `
                    <div class="ticket-item-row" style="${borderHighlight}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 280px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                                    <span class="badge badge-secondary" style="font-family: monospace; font-size: 11px;">${r.id}</span>
                                    <span class="badge ${r.urgency === 'Emergency' ? 'badge-danger' : (r.urgency === 'High' ? 'badge-warning' : 'badge-secondary')}">
                                        <i class="fas ${r.urgency === 'Emergency' ? 'fa-bolt' : 'fa-clock'}"></i> ${r.urgency}
                                    </span>
                                    ${statusBadge}
                                    <span style="font-size: 11.5px; color: #64748b;">Submitted ${r.requestedDate} by ${escapeHtml(r.requestedBy)}</span>
                                </div>

                                <h3 style="font-size: 16.5px; font-weight: 800; color: #0f172a; margin: 4px 0 6px 0;">
                                    ${escapeHtml(r.title)}
                                </h3>

                                <div style="display: flex; gap: 18px; font-size: 12.5px; margin-bottom: 8px; flex-wrap: wrap;">
                                    <div><span style="color: #64748b;">Beneficiary Child:</span> <strong style="color: #0f172a;">${escapeHtml(r.beneficiaryName)} (${r.beneficiaryId})</strong></div>
                                    <div><span style="color: #64748b;">Category:</span> <strong>${escapeHtml(r.category)}</strong></div>
                                    <div><span style="color: #64748b;">Dorm:</span> <strong>${escapeHtml(r.dorm)}</strong></div>
                                    <div><span style="color: #64748b;">Total Budget:</span> <strong style="color: #2563eb; font-size: 14px;">$${parseFloat(r.cost).toFixed(2)} USD</strong></div>
                                </div>

                                <div style="font-size: 12.5px; color: #475569; background: #f8fafc; padding: 10px 14px; border-radius: 6px; border: 1px solid #e2e8f0; margin-top: 6px;">
                                    <strong>Caregiver Justification:</strong> "${escapeHtml(r.justification)}"
                                </div>

                                ${linesPreview}
                                ${decisionBanner}
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        $('#staff_requests_container').html(html);
    }

    function populateChildSelect() {
        let opts = '';
        if (window.HopeHavenChildStore) {
            const children = window.HopeHavenChildStore.getAll();
            children.forEach(c => {
                opts += `<option value="${c.name}|${c.id}|${c.dorm}">${c.name} (${c.id} · ${c.dorm})</option>`;
            });
        } else {
            opts = `
                <option value="Maya Cruz|CH-2024-001|St. Clare Cottage">Maya Cruz (CH-2024-001 · St. Clare Cottage)</option>
                <option value="Ahmad Razak|CH-2023-014|St. Francis Boys Hall">Ahmad Razak (CH-2023-014 · St. Francis Boys Hall)</option>
                <option value="Elijah Liam Ramos|CH-2022-008|St. Francis Boys Hall">Elijah Liam Ramos (CH-2022-008 · St. Francis Boys Hall)</option>
                <option value="Hannah Grace Morales|CH-2025-032|St. Clare Cottage">Hannah Grace Morales (CH-2025-032 · St. Clare Cottage)</option>
            `;
        }
        $('#req_child_select').html(opts);
    }

    function handleStaffSubmitRequestForm(e) {
        e.preventDefault();

        const childParts = ($('#req_child_select').val() || 'Maya Cruz|CH-2024-001|St. Clare Cottage').split('|');
        const childName = childParts[0];
        const childId = childParts[1] || 'CH-General';
        const dorm = childParts[2] || 'St. Clare Cottage';

        const title = $('#req_title_input').val().trim();
        const category = $('#req_cat_input').val();
        const urgency = $('#req_urgency_input').val();
        const justification = $('#req_justification_input').val().trim();

        const lineItems = [];
        let totalCost = 0;
        $('#requisition_items_container .req-line-item-row').each(function() {
            const name = $(this).find('.line-item-name').val().trim();
            const cost = parseFloat($(this).find('.line-item-cost').val()) || 0;
            if (name) {
                lineItems.push({ item: name, cost: cost });
                totalCost += cost;
            }
        });

        if (lineItems.length === 0) {
            showPortalToast('Add at least one item and its actual cost before submitting.', false);
            return;
        }

        const newRequest = window.HopeHavenWelfareStore.createRequest({
            title: title,
            category: category,
            beneficiaryName: childName,
            beneficiaryId: childId,
            dorm: dorm,
            urgency: urgency,
            cost: totalCost,
            lineItems: lineItems,
            justification: justification,
            requestedBy: 'Nurse Mary (Active Caregiver)'
        });

        closeStaffWelfareModal('modal_staff_create_request');
        e.target.reset();
        renderStaffRequests();

        showPortalToast(
            `Welfare request #${newRequest.id} ($${totalCost.toFixed(2)}) submitted to Sister Grace for review!`,
            true,
            'Welfare Request Originated',
            'Staff Caregiver Desk'
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

    $(document).ready(function() {
        populateChildSelect();
        renderStaffRequests();

        window.HopeHavenSync.subscribe('hopehaven_welfare_requests_v2', function() {
            window.HopeHavenSync.refreshView(renderStaffRequests);
        });
    });
</script>
@endpush
