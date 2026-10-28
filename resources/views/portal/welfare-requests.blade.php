@extends('layouts.portal')

@section('title', 'Welfare Requests & Multi-Tier Approvals (Admin Review)')
@section('breadcrumb', 'Welfare Approvals & Review')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/welfare-requests.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/welfare-requests.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/dashboard.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/embedded/portal-welfare-requests-1.css') }}?v={{ substr(hash_file('sha256', public_path('css/embedded/portal-welfare-requests-1.css')), 0, 12) }}">
@endpush

@section('content')

<!-- Admin Header -->
<div class="admin-welfare-hero">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div style="max-width: 680px;">
            <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; margin-bottom: 10px; letter-spacing: 0.5px; text-transform: uppercase;">
                <i class="fas fa-clipboard-check"></i> Subsystem 3 · Request Approvals
            </div>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0 0 8px 0; letter-spacing: -0.5px;">
                Welfare Requests & Request Approvals
            </h1>
            <p style="font-size: 13.5px; opacity: 0.92; margin: 0; line-height: 1.5;">
                Request approval desk. Review, allocate fund funding, or decline welfare assistance requests submitted by duty caregivers and educational staff.
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <button class="btn-primary" style="background: #10b981; border-color: #10b981; font-weight: 700;" onclick="filterAdminRequestsByTab('Pending Admin Approval', $('.tab-btn[data-filter=\'Pending Admin Approval\']'))">
                <i class="fas fa-stamp"></i> Review Pending Queue
            </button>
        </div>
    </div>
</div>

<!-- Separation of Duties Ribbon -->
<div class="rbac-banner" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-shield" style="color: #3b82f6; font-size: 18px;"></i>
        <div>
            <span style="font-size: 12px; font-weight: 700; color: var(--dark); text-transform: uppercase;">Clearance Level:</span>
            <span style="font-size: 12px; color: var(--dark-muted); margin-left: 4px;">Authorizing Official: <strong style="color: #2563eb;">Sister Grace (Executive Director)</strong></span>
        </div>
    </div>
    <div style="font-size: 12px; color: #64748b;">
        <i class="fas fa-info-circle" style="color: #3b82f6;"></i>
        <strong>Who Can Approve:</strong> Staff members submit child welfare requests; only administrators approve financial & inventory payment.
    </div>
</div>

<!-- Metric KPI Cards -->
<div class="metrics-grid" style="margin-bottom: 24px;">
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Awaiting My Decision</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #d97706;" id="kpi_admin_pending">2 Pending</div>
        <div class="metric-sub">Submitted by staff for approval</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Approved Aid Value</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #059669;" id="kpi_admin_approved_value">$1,021.00 USD</div>
        <div class="metric-sub">Allocated from healthcare & tuition funds</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">In Fulfillment Pipeline</span>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-truck-loading"></i>
            </div>
        </div>
        <div class="metric-val" style="color: #2563eb;">2 Tickets</div>
        <div class="metric-sub">Clinic scheduled or goods dispatched</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Decision Audit Trail</span>
            <div class="metric-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                <i class="fas fa-history"></i>
            </div>
        </div>
        <div class="metric-val" id="kpi_admin_total">5 Logged</div>
        <div class="metric-sub">Fully audited institutional records</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container" style="margin-bottom: 20px;">
    <button class="tab-btn active" data-filter="Pending Admin Approval" onclick="filterAdminRequestsByTab('Pending Admin Approval', this)">
        <i class="fas fa-stamp"></i> Review & Approval Queue (<span id="admin_count_pending">2</span>)
    </button>
    <button class="tab-btn" data-filter="all" onclick="filterAdminRequestsByTab('all', this)">
        <i class="fas fa-list-alt"></i> All Requests (<span id="admin_count_all">5</span>)
    </button>
    <button class="tab-btn" data-filter="Approved" onclick="filterAdminRequestsByTab('Approved', this)">
        <i class="fas fa-check-circle"></i> Approved Cases (<span id="admin_count_approved">2</span>)
    </button>
    <button class="tab-btn" data-filter="Rejected" onclick="filterAdminRequestsByTab('Rejected', this)">
        <i class="fas fa-times-circle"></i> Declined / Revision (<span id="admin_count_rejected">1</span>)
    </button>
</div>

<!-- Search & Category Filters -->
<div class="filter-bar admin-welfare-filters" style="margin-bottom: 20px;">
    <div class="search-box-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="admin_req_search" class="search-input" placeholder="Search by ticket #, child recipient, or staff originator..." oninput="renderAdminRequests()">
    </div>

    <div class="filter-select-wrapper">
        <select id="admin_req_urgency_filter" class="filter-select" onchange="renderAdminRequests()">
            <option value="all">All Urgency Tiers</option>
            <option value="Emergency">Emergency (Immediate 24h)</option>
            <option value="High">High Urgency</option>
            <option value="Standard">Standard Routine</option>
        </select>
    </div>

    <div class="filter-select-wrapper">
        <select id="admin_req_cat_filter" class="filter-select" onchange="renderAdminRequests()">
            <option value="all">All Aid Categories</option>
            <option value="Medical / Surgical">Medical / Surgical</option>
            <option value="Education & Tuition">Education & Tuition</option>
            <option value="Clothing & Bedding">Clothing & Bedding</option>
            <option value="Nutrition & Diet">Nutrition & Diet</option>
        </select>
    </div>
</div>

<!-- Admin Requests List Container -->
<div id="admin_requests_container">
    <!-- Populated dynamically by JavaScript -->
</div>

<!-- ==========================================================================
     MODAL 1: AUTHORIZE / APPROVE REQUISITION
     ========================================================================== -->
<div id="modal_admin_approve" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 520px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" style="color: #065f46;">Approve Welfare Request</h3>
                <p class="modal-sub" id="approve_modal_sub">Ticket WR-2026-044 &bull; Elijah Liam Ramos</p>
            </div>
            <button class="modal-close-btn" onclick="closeAdminModal('modal_admin_approve')">&times;</button>
        </div>

        <form onsubmit="handleAdminApproveSubmit(event)">
            <input type="hidden" id="approve_ticket_id">

            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px;">
                <div style="font-size: 11px; color: #166534; font-weight: 700; text-transform: uppercase;">Request Total Amount</div>
                <div style="font-size: 20px; font-weight: 800; color: #15803d;" id="approve_modal_cost">$117.00 USD</div>
                <div style="font-size: 12px; color: #166534; margin-top: 2px;" id="approve_modal_title">Secondary School Exam Review Books</div>
            </div>

            <div class="form-group">
                <label class="form-label">Funding Source Allocation *</label>
                <select id="approve_fund_source" class="form-control" required>
                    <option value="Sanctuary Emergency Healthcare Endowment">Orphanage Emergency Healthcare Fund</option>
                    <option value="Hope Memorial Education Scholarship Fund">Hope Memorial Education Scholarship Fund</option>
                    <option value="General Orphanage Welfare Allocation">General Orphanage Welfare Allocation</option>
                    <option value="Rotary Club Pediatric Medical Grant">Rotary Club Pediatric Medical Grant</option>
                    <option value="Central Storeroom Inventory Allocation">Central Storeroom Inventory Allocation</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Authorizing Official</label>
                <input type="text" id="approve_admin_name" class="form-control" value="Sister Grace (Executive Director)" readonly style="background: #f1f5f9; font-weight: 700;">
            </div>

            <div class="form-group">
                <label class="form-label">Administrative Approval Remarks & Directives *</label>
                <textarea id="approve_remarks" class="form-control" rows="2" placeholder="e.g. Approved for procurement. Receipt voucher issued to storeroom Manager..." required>Approved unconditionally for beneficiary welfare.</textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeAdminModal('modal_admin_approve')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #10b981; border-color: #10b981;">
                    <i class="fas fa-stamp"></i> Confirm Official Approval
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================================
     MODAL 2: REJECT / DECLINE REQUISITION
     ========================================================================== -->
<div id="modal_admin_reject" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" style="color: #991b1b;">Decline Welfare Request</h3>
                <p class="modal-sub" id="reject_modal_sub">Ticket WR-2026-044 &bull; Elijah Liam Ramos</p>
            </div>
            <button class="modal-close-btn" onclick="closeAdminModal('modal_admin_reject')">&times;</button>
        </div>

        <form onsubmit="handleAdminRejectSubmit(event)">
            <input type="hidden" id="reject_ticket_id">

            <div class="form-group">
                <label class="form-label">Reason for Declining / Modification Request *</label>
                <textarea id="reject_reason" class="form-control" rows="3" placeholder="Explain why the request cannot be approved (e.g. Draw existing inventory stock from Storeroom Bay 3 first, or wait for next budget cycle)..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Declining Authority</label>
                <input type="text" class="form-control" value="Sister Grace (Executive Director)" readonly style="background: #f1f5f9; font-weight: 700;">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeAdminModal('modal_admin_reject')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #ef4444; border-color: #ef4444;">
                    <i class="fas fa-times"></i> Decline Request
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let adminFilterTab = 'Pending Admin Approval';

    function openAdminModal(id) {
        $('#' + id).css('display', 'flex').hide().fadeIn(200);
    }

    function closeAdminModal(id) {
        $('#' + id).fadeOut(150);
    }

    function filterAdminRequestsByTab(status, btn) {
        adminFilterTab = status;
        $('.tab-btn').removeClass('active');
        $(btn).addClass('active');
        renderAdminRequests();
    }

    function renderAdminRequests() {
        if (!window.HopeHavenWelfareStore) return;

        const all = window.HopeHavenWelfareStore.getAll();
        const search = ($('#admin_req_search').val() || '').toLowerCase().trim();
        const urgencyFilter = $('#admin_req_urgency_filter').val() || 'all';
        const categoryFilter = $('#admin_req_cat_filter').val() || 'all';

        const pendingList = all.filter(r => r.status === 'Pending Admin Approval');
        const approvedList = all.filter(r => r.status === 'Approved');
        const rejectedList = all.filter(r => r.status === 'Rejected');

        $('#admin_count_all').text(all.length);
        $('#admin_count_pending').text(pendingList.length);
        $('#admin_count_approved').text(approvedList.length);
        $('#admin_count_rejected').text(rejectedList.length);

        $('#kpi_admin_pending').text(pendingList.length + ' Pending');
        $('#kpi_admin_total').text(all.length + ' Logged');

        // Sum approved aid value
        let approvedValue = 0;
        approvedList.forEach(r => approvedValue += (parseFloat(r.cost) || 0));
        $('#kpi_admin_approved_value').text('$' + approvedValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD');

        const filtered = all.filter(r => {
            const matchesTab = adminFilterTab === 'all' || r.status === adminFilterTab;
            const matchesSearch = !search || 
                r.id.toLowerCase().includes(search) || 
                r.title.toLowerCase().includes(search) || 
                r.beneficiaryName.toLowerCase().includes(search) || 
                r.requestedBy.toLowerCase().includes(search);
            const matchesUrgency = urgencyFilter === 'all' || r.urgency === urgencyFilter;
            const matchesCategory = categoryFilter === 'all' || r.category === categoryFilter;
            return matchesTab && matchesSearch && matchesUrgency && matchesCategory;
        });

        let html = '';
        if (filtered.length === 0) {
            html = `
                <div class="portal-card" style="padding: 40px; text-align: center; color: #94a3b8;">
                    <i class="fas fa-check-circle" style="font-size: 32px; margin-bottom: 12px; color: #10b981;"></i>
                    <h4 style="font-size: 15px; color: #475569; margin-bottom: 4px;">Queue Clear</h4>
                    <p style="font-size: 12.5px; margin: 0;">No welfare requests matching the selected filter criteria.</p>
                </div>
            `;
        } else {
            filtered.forEach(r => {
                let statusBadge = '';
                let borderHighlight = '';
                let actionControls = '';

                if (r.status === 'Pending Admin Approval') {
                    statusBadge = `<span class="req-status-pill status-pending-admin"><i class="fas fa-clock"></i> Awaiting Your Decision</span>`;
                    borderHighlight = 'border-left: 4px solid #f59e0b;';
                    actionControls = `
                        <div style="display: flex; gap: 8px;">
                            <button class="btn-primary btn-sm" style="background: #10b981; border-color: #10b981;" onclick="openApproveModal('${r.id}')">
                                <i class="fas fa-check"></i> Approve & Approve
                            </button>
                            <button class="btn-outline btn-sm" style="color: #ef4444; border-color: #ef4444;" onclick="openRejectModal('${r.id}')">
                                <i class="fas fa-times"></i> Decline
                            </button>
                        </div>
                    `;
                } else if (r.status === 'Approved') {
                    statusBadge = `<span class="req-status-pill status-approved-admin"><i class="fas fa-check-double"></i> Authorized by Sister Grace</span>`;
                    borderHighlight = 'border-left: 4px solid #10b981;';
                    const dec = r.adminDecision || {};
                    actionControls = `
                        <div style="text-align: right;">
                            <span class="badge badge-success"><i class="fas fa-lock"></i> Authorized & Budget Released</span>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 4px;">Ref: ${dec.disbursementRef || 'DISB-2026'}</div>
                        </div>
                    `;
                } else if (r.status === 'Rejected') {
                    statusBadge = `<span class="req-status-pill status-rejected-admin"><i class="fas fa-times-circle"></i> Declined</span>`;
                    borderHighlight = 'border-left: 4px solid #ef4444;';
                    actionControls = `
                        <div style="text-align: right;">
                            <span class="badge badge-danger">Declined by Admin</span>
                        </div>
                    `;
                }

                // Line items
                let linesPreview = '';
                if (r.lineItems && r.lineItems.length > 0) {
                    linesPreview = '<div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">';
                    r.lineItems.forEach(li => {
                        linesPreview += `<span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 11.5px; color: #475569;">&bull; ${escapeHtml(li.item)} ($${li.cost})</span>`;
                    });
                    linesPreview += '</div>';
                }

                // Decision remarks
                let decisionBanner = '';
                if (r.adminDecision) {
                    const dec = r.adminDecision;
                    if (r.status === 'Approved') {
                        decisionBanner = `
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; margin-top: 12px; font-size: 12px; color: #166534;">
                                <div><strong>Approved by:</strong> ${escapeHtml(dec.approvedBy)} &bull; <strong>Funding Source:</strong> ${escapeHtml(dec.fundingSource)}</div>
                                <div style="margin-top: 3px; font-style: italic;">"${escapeHtml(dec.remarks)}"</div>
                            </div>
                        `;
                    } else if (r.status === 'Rejected') {
                        decisionBanner = `
                            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; margin-top: 12px; font-size: 12px; color: #991b1b;">
                                <div><strong>Declined by:</strong> ${escapeHtml(dec.approvedBy)} &bull; <strong>Reason:</strong> ${escapeHtml(dec.remarks)}</div>
                            </div>
                        `;
                    }
                }

                html += `
                    <div class="admin-ticket-card" style="${borderHighlight}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 280px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                                    <span class="badge badge-secondary" style="font-family: monospace; font-size: 11px;">${r.id}</span>
                                    <span class="badge ${r.urgency === 'Emergency' ? 'badge-danger' : (r.urgency === 'High' ? 'badge-warning' : 'badge-secondary')}">
                                        <i class="fas ${r.urgency === 'Emergency' ? 'fa-bolt' : 'fa-clock'}"></i> ${r.urgency}
                                    </span>
                                    ${statusBadge}
                                    <span style="font-size: 11.5px; color: #64748b;">Originated by ${escapeHtml(r.requestedBy)} &bull; ${r.requestedDate}</span>
                                </div>

                                <h3 style="font-size: 16.5px; font-weight: 800; color: #0f172a; margin: 4px 0 6px 0;">
                                    ${escapeHtml(r.title)}
                                </h3>

                                <div style="display: flex; gap: 18px; font-size: 12.5px; margin-bottom: 8px; flex-wrap: wrap;">
                                    <div><span style="color: #64748b;">Beneficiary:</span> <strong style="color: #0f172a;">${escapeHtml(r.beneficiaryName)} (${r.beneficiaryId})</strong></div>
                                    <div><span style="color: #64748b;">Category:</span> <strong>${escapeHtml(r.category)}</strong></div>
                                    <div><span style="color: #64748b;">Dorm:</span> <strong>${escapeHtml(r.dorm)}</strong></div>
                                    <div><span style="color: #64748b;">Requested Budget:</span> <strong style="color: #059669; font-size: 14.5px;">$${parseFloat(r.cost).toFixed(2)} USD</strong></div>
                                </div>

                                <div style="font-size: 12.5px; color: #475569; background: #f8fafc; padding: 10px 14px; border-radius: 6px; border: 1px solid #e2e8f0; margin-top: 6px;">
                                    <strong>Caregiver Justification:</strong> "${escapeHtml(r.justification)}"
                                </div>

                                ${linesPreview}
                                ${decisionBanner}
                            </div>

                            <div style="align-self: center;">
                                ${actionControls}
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        $('#admin_requests_container').html(html);
    }

    function openApproveModal(ticketId) {
        const ticket = window.HopeHavenWelfareStore.getById(ticketId);
        if (!ticket) return;

        $('#approve_ticket_id').val(ticket.id);
        $('#approve_modal_sub').text(`Ticket ${ticket.id} · Beneficiary: ${ticket.beneficiaryName} (${ticket.dorm})`);
        $('#approve_modal_cost').text(`$${parseFloat(ticket.cost).toFixed(2)} USD`);
        $('#approve_modal_title').text(ticket.title);

        openAdminModal('modal_admin_approve');
    }

    function openRejectModal(ticketId) {
        const ticket = window.HopeHavenWelfareStore.getById(ticketId);
        if (!ticket) return;

        $('#reject_ticket_id').val(ticket.id);
        $('#reject_modal_sub').text(`Ticket ${ticket.id} · Beneficiary: ${ticket.beneficiaryName}`);
        openAdminModal('modal_admin_reject');
    }

    function handleAdminApproveSubmit(e) {
        e.preventDefault();
        const ticketId = $('#approve_ticket_id').val();
        const fundSource = $('#approve_fund_source').val();
        const remarks = $('#approve_remarks').val().trim();
        const adminName = $('#approve_admin_name').val();

        window.HopeHavenWelfareStore.approveRequest(ticketId, adminName, fundSource, remarks);
        closeAdminModal('modal_admin_approve');
        renderAdminRequests();

        showPortalToast(
            `Welfare Request #${ticketId} authorized & funded from ${fundSource}!`,
            true,
            'Request Approved',
            'Executive Approval'
        );
    }

    function handleAdminRejectSubmit(e) {
        e.preventDefault();
        const ticketId = $('#reject_ticket_id').val();
        const reason = $('#reject_reason').val().trim();
        const adminName = 'Sister Grace (Executive Director)';

        window.HopeHavenWelfareStore.rejectRequest(ticketId, adminName, reason);
        closeAdminModal('modal_admin_reject');
        renderAdminRequests();

        showPortalToast(
            `Welfare Request #${ticketId} declined. Feedback saved for staff.`,
            false,
            'Request Declined',
            'Executive Review'
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
        renderAdminRequests();

        window.HopeHavenSync.subscribe('hopehaven_welfare_requests_v2', function() {
            window.HopeHavenSync.refreshView(renderAdminRequests);
        });
    });
</script>
@endpush
