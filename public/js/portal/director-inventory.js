/**
 * Director Inventory Requests Script
 * Handles table filters, search, modal operations, and executive signatures
 */

let activeRequisitionId = null;

document.addEventListener('DOMContentLoaded', function () {
    initFilters();
    initSearch();
});

/**
 * Filter tabs logic
 */
function initFilters() {
    const tabBtns = document.querySelectorAll('.inv-tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');
            filterTableRows(filterValue);
        });
    });

    const categorySelect = document.getElementById('inv_category_filter');
    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            filterTableRows(null, this.value);
        });
    }
}

/**
 * Filter table rows by status or department category
 */
function filterTableRows(statusFilter, categoryFilter) {
    const currentStatus = statusFilter !== undefined && statusFilter !== null 
        ? statusFilter 
        : document.querySelector('.inv-tab-btn.active')?.getAttribute('data-filter') || 'all';

    const currentCategory = categoryFilter !== undefined && categoryFilter !== null
        ? categoryFilter
        : document.getElementById('inv_category_filter')?.value || 'all';

    const rows = document.querySelectorAll('.inv-table tbody tr');

    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const rowPriority = row.getAttribute('data-priority');
        const rowCategory = row.getAttribute('data-category');

        let matchesStatus = (currentStatus === 'all') 
            || (currentStatus === 'critical' && rowPriority === 'critical') 
            || (rowStatus === currentStatus);

        let matchesCategory = (currentCategory === 'all') || (rowCategory === currentCategory);

        if (matchesStatus && matchesCategory) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Live search in table
 */
function initSearch() {
    const searchInput = document.getElementById('inv_search_input');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.inv-table tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (query === '' || text.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

/**
 * Open Review & Authorization Modal
 */
function openDirectorSignModal(reqId, staffName, dept, cost, items, justification) {
    activeRequisitionId = reqId;

    document.getElementById('sign_req_id').innerText = reqId;
    document.getElementById('sign_req_staff').innerText = staffName;
    document.getElementById('sign_req_dept').innerText = dept;
    document.getElementById('sign_req_cost').innerText = cost;
    document.getElementById('sign_req_items').innerHTML = items;
    document.getElementById('sign_req_justification').innerText = justification;

    document.getElementById('director_approval_remarks').value = '';
    document.getElementById('director_auth_check').checked = true;

    const modal = document.getElementById('modal_director_sign_req');
    if (modal) modal.classList.add('active');
}

/**
 * Open Rejection / Revision Modal
 */
function openDirectorRejectModal(reqId) {
    activeRequisitionId = reqId;

    document.getElementById('reject_req_id_input').value = reqId;
    document.getElementById('director_reject_reason').value = '';

    const modal = document.getElementById('modal_director_reject_req');
    if (modal) modal.classList.add('active');
}

/**
 * Close any Director Modal
 */
function closeDirectorModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
    activeRequisitionId = null;
}

/**
 * Confirm Director Signature and Fund Release
 */
function submitDirectorSign() {
    const authCheck = document.getElementById('director_auth_check');
    if (authCheck && !authCheck.checked) {
        alert('Please check the authorization declaration box to sign as Executive Director.');
        return;
    }

    const remarks = document.getElementById('director_approval_remarks').value.trim() || 'Authorized by Sister Sarah (Director).';
    const reqId = activeRequisitionId;

    // Update row in table
    updateRowStatus(reqId, 'approved', 'Approved by Director', '#ecfdf5', '#047857');
    closeDirectorModal('modal_director_sign_req');

    // Update stats
    decrementPendingCounter();

    if (typeof showPortalToast === 'function') {
        showPortalToast(`Requisition #${reqId} signed & authorized! Funds released with remarks: "${remarks}"`, 'success');
    } else {
        alert(`Requisition #${reqId} signed & authorized!`);
    }
}

/**
 * Submit Rejection / Return for revision
 */
function submitDirectorReject() {
    const reason = document.getElementById('director_reject_reason').value.trim();
    if (!reason) {
        alert('Please specify guidance or instructions for the requesting caregiver.');
        return;
    }

    const reqId = activeRequisitionId;

    updateRowStatus(reqId, 'rejected', 'Revision Requested', '#fef2f2', '#991b1b');
    closeDirectorModal('modal_director_reject_req');

    decrementPendingCounter();

    if (typeof showPortalToast === 'function') {
        showPortalToast(`Feedback sent to caregiver on Requisition #${reqId}. Status updated to Revision Requested.`, 'warning');
    } else {
        alert(`Requisition #${reqId} returned for revision.`);
    }
}

/**
 * Quick Approve action directly from table
 */
function quickApproveRow(reqId, itemName) {
    if (!confirm(`Authorize immediate payment & fulfillment for Requisition #${reqId} (${itemName})?`)) {
        return;
    }

    updateRowStatus(reqId, 'approved', 'Approved by Director', '#ecfdf5', '#047857');
    decrementPendingCounter();

    if (typeof showPortalToast === 'function') {
        showPortalToast(`Requisition #${reqId} (${itemName}) approved by Sister Sarah.`, 'success');
    }
}

/**
 * Helper to update row appearance after action
 */
function updateRowStatus(reqId, newStatus, badgeText, bgColor, textColor) {
    const row = document.querySelector(`.inv-table tbody tr[data-req-id="${reqId}"]`);
    if (!row) return;

    row.setAttribute('data-status', newStatus);

    const statusCell = row.querySelector('.status-cell');
    if (statusCell) {
        statusCell.innerHTML = `<span class="status-pill" style="background:${bgColor}; color:${textColor};"><i class="fas fa-check-circle"></i> ${badgeText}</span>`;
    }

    const actionCell = row.querySelector('.inv-action-group');
    if (actionCell) {
        actionCell.innerHTML = `<span style="font-size:12px; color:#64748b; font-weight:600;"><i class="fas fa-check-double" style="color:#10b981;"></i> Processed</span>`;
    }
}

/**
 * Decrement pending counter in stats and tabs
 */
function decrementPendingCounter() {
    const statPending = document.getElementById('stat_pending_count');
    if (statPending) {
        let val = parseInt(statPending.innerText, 10);
        if (!isNaN(val) && val > 0) statPending.innerText = (val - 1).toString();
    }

    const tabBadge = document.getElementById('tab_badge_pending');
    if (tabBadge) {
        let val = parseInt(tabBadge.innerText, 10);
        if (!isNaN(val) && val > 0) tabBadge.innerText = (val - 1).toString();
    }
}

/**
 * Export Requisitions Summary
 */
function exportRequisitionsAudit() {
    if (typeof showPortalToast === 'function') {
        showPortalToast('Exporting Staff Requisitions & Allocation Audit (Excel / CSV)...', 'info');
    } else {
        alert('Exporting Staff Requisitions Ledger...');
    }
}
