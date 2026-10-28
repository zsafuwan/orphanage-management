/* Director inventory reads the same browser-local stock and requests as staff. */
(function () {
    'use strict';
    let activeRequestId = null;
    let filter = 'all';
    const store = () => window.HopeHavenInventory;
    const escape = value => window.HopeHavenSync.escape(value);
    const setText = (id, value) => { const node = document.getElementById(id); if (node) node.textContent = value; };
    const statusLabels = {Pending: 'Awaiting Approval', Approved: 'Approved', Dispatched: 'Ready to Receive', Received: 'Received', Rejected: 'Returned'};
    const note = (message, ok = true) => window.showPortalToast ? showPortalToast(message, ok) : alert(message);
    function stockFor(request) {
        return store().getItems().find(item => item.location.toLowerCase().trim() === String(request.destination || '').toLowerCase().trim()
            && (item.id === request.itemId || item.name.toLowerCase().trim() === String(request.item || '').toLowerCase().trim()));
    }
    function render() {
        const requests = store().getRequisitions();
        const items = store().getItems();
        setText('stat_pending_count', requests.filter(r => r.status === 'Pending').length);
        setText('stat_low_stock_count', items.filter(i => Number(i.onHand) <= Number(i.minThreshold)).length);
        setText('stat_stock_item_count', items.length);
        setText('stat_approved_request_count', requests.filter(r => ['Approved', 'Dispatched', 'Received'].includes(r.status)).length);
        document.querySelectorAll('[data-request-count]').forEach(node => {
            node.textContent = requests.filter(r => node.dataset.requestCount === 'all' || r.status === node.dataset.requestCount).length;
        });
        const search = document.getElementById('inv_search_input').value.toLowerCase().trim();
        const visible = requests.filter(r => (filter === 'all' || r.status === filter)
            && [r.id, r.item, r.destination, r.requestedBy, r.notes].join(' ').toLowerCase().includes(search));
        document.getElementById('director_requests_tbody').innerHTML = visible.map(r => {
            const item = stockFor(r);
            const state = ['Pending', 'Approved', 'Received', 'Rejected', 'Dispatched'].includes(r.status) ? r.status.toLowerCase() : 'pending';
            return `<tr><td><strong>${escape(r.id)}</strong><small class="inventory-cell-sub">${escape(r.requestedBy || 'Staff')}</small><small class="inventory-cell-sub">${escape(r.time || '')}</small></td>
                <td><strong>${escape(r.item)}</strong><small class="inventory-cell-sub">Requested: ${escape(r.qty)} ${escape(r.unit || item?.unit || 'units')}</small></td>
                <td>${escape(r.destination)}</td><td><span class="inventory-priority">${escape(r.priority)}</span></td>
                <td><strong>${escape(item?.onHand ?? 0)}</strong> ${escape(item?.unit || r.unit || 'units')}</td>
                <td><span class="inventory-status ${state}">${escape(statusLabels[r.status] || r.status)}</span>${r.directorRemarks ? `<small class="inventory-cell-sub">${escape(r.directorRemarks)}</small>` : ''}</td>
                <td><div class="inventory-review-actions">${r.status === 'Pending' ? `<button type="button" class="btn-sign" data-inventory-action="approve" data-request-id="${escape(r.id)}">Review</button><button type="button" class="btn-outline btn-sm" data-inventory-action="reject" data-request-id="${escape(r.id)}">Return</button>` : `<span class="inventory-cell-sub">${escape(r.reviewedBy || (r.status === 'Received' ? 'Received by staff' : 'Reviewed'))}</span>`}</div></td></tr>`;
        }).join('') || '<tr><td colspan="7" class="inventory-empty">No requests match these filters.</td></tr>';
        setText('director_requests_summary', visible.length + ' of ' + requests.length + ' requests');
        const stockSearch = document.getElementById('director_stock_search').value.toLowerCase().trim();
        const lowOnly = document.getElementById('director_low_only').checked;
        const stocks = items.filter(i => [i.id, i.name, i.location, i.category].join(' ').toLowerCase().includes(stockSearch)
            && (!lowOnly || Number(i.onHand) <= Number(i.minThreshold)));
        document.getElementById('director_stock_tbody').innerHTML = stocks.map(i => {
            const low = Number(i.onHand) <= Number(i.minThreshold);
            return `<tr><td><strong>${escape(i.name)}</strong><small class="inventory-cell-sub">${escape(i.id)}</small></td><td>${escape(i.category)}</td><td>${escape(i.location)}</td>
                <td><strong class="inventory-stock-number">${escape(i.onHand)}</strong> ${escape(i.unit)}</td><td>${escape(i.minThreshold)} ${escape(i.unit)}</td>
                <td><span class="inventory-status ${low ? 'low' : 'received'}">${low ? 'Low Stock' : 'In Stock'}</span></td></tr>`;
        }).join('') || '<tr><td colspan="6" class="inventory-empty">No stock items match these filters.</td></tr>';
        setText('director_stock_summary', stocks.length + ' of ' + items.length + ' stock items');
        if (activeRequestId) {
            const request = requests.find(r => r.id === activeRequestId);
            const item = request && stockFor(request);
            if (request) setText('sign_req_cost', (item?.onHand || 0) + ' ' + (item?.unit || request.unit || 'units'));
        }
    }
    window.openDirectorSignModal = function (id) {
        const request = store().getRequisitions().find(r => r.id === id);
        if (!request || request.status !== 'Pending') { note('This request has already been reviewed.', false); return; }
        activeRequestId = id;
        const item = stockFor(request);
        setText('sign_req_id', request.id);
        setText('sign_req_staff', request.requestedBy || 'Staff');
        setText('sign_req_dept', request.destination);
        setText('sign_req_cost', (item?.onHand || 0) + ' ' + (item?.unit || request.unit || 'units'));
        setText('sign_req_items', request.qty + ' ' + (request.unit || item?.unit || 'units') + ' × ' + request.item);
        setText('sign_req_justification', request.notes || 'No extra notes.');
        document.getElementById('director_approval_remarks').value = '';
        document.getElementById('director_auth_check').checked = false;
        document.getElementById('modal_director_sign_req').classList.add('active');
    };
    window.openDirectorRejectModal = function (id) {
        const request = store().getRequisitions().find(r => r.id === id);
        if (!request || request.status !== 'Pending') { note('This request has already been reviewed.', false); return; }
        activeRequestId = id;
        document.getElementById('reject_req_id_input').value = id;
        document.getElementById('director_reject_reason').value = '';
        document.getElementById('modal_director_reject_req').classList.add('active');
    };
    window.closeDirectorModal = function (id) {
        document.getElementById(id)?.classList.remove('active');
        activeRequestId = null;
    };
    window.submitDirectorSign = function () {
        if (!document.getElementById('director_auth_check').checked) { note('Please confirm that you approve this request.', false); return; }
        try {
            store().decideRequest(activeRequestId, 'Approved', document.getElementById('director_approval_remarks').value);
            closeDirectorModal('modal_director_sign_req');
            render();
            note('Request approved. Staff can confirm receipt when the supplies arrive.');
        } catch (error) { note(error.message, false); }
    };
    window.submitDirectorReject = function () {
        try {
            store().decideRequest(activeRequestId, 'Rejected', document.getElementById('director_reject_reason').value);
            closeDirectorModal('modal_director_reject_req');
            render();
            note('Request returned. Your feedback is visible to staff.');
        } catch (error) { note(error.message, false); }
    };
    window.exportRequisitionsAudit = function () {
        const cell = value => '"' + String(value ?? '').replace(/^[=+@-]/, "'$&").replaceAll('"', '""') + '"';
        const rows = [['Request ID', 'Staff', 'Item', 'Quantity', 'Unit', 'Destination', 'Status', 'Director Feedback'],
            ...store().getRequisitions().map(r => [r.id, r.requestedBy || 'Staff', r.item, r.qty, r.unit || 'units', r.destination, r.status, r.directorRemarks || ''])];
        const url = URL.createObjectURL(new Blob(['\ufeff' + rows.map(row => row.map(cell).join(',')).join('\r\n')], {type: 'text/csv;charset=utf-8'}));
        const link = document.createElement('a');
        link.href = url; link.download = 'inventory-requests.csv'; link.click();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    };
    document.addEventListener('DOMContentLoaded', function () {
        if (!document.getElementById('director_requests_tbody')) return;
        document.querySelectorAll('.inv-tab-btn').forEach(button => button.addEventListener('click', function () {
            filter = button.dataset.filter;
            document.querySelectorAll('.inv-tab-btn').forEach(other => {
                other.classList.toggle('active', other === button);
                other.setAttribute('aria-pressed', String(other === button));
            });
            render();
        }));
        ['inv_search_input', 'director_stock_search'].forEach(id => document.getElementById(id).addEventListener('input', render));
        document.getElementById('director_low_only').addEventListener('change', render);
        document.addEventListener('click', function (event) {
            const action = event.target.closest('[data-inventory-action]');
            if (!action) return;
            if (action.dataset.inventoryAction === 'approve') openDirectorSignModal(action.dataset.requestId);
            else openDirectorRejectModal(action.dataset.requestId);
        });
        render();
        window.HopeHavenSync.subscribe([store().keys.items, store().keys.requests], () => window.HopeHavenSync.refreshView(render));
        const requested = new URLSearchParams(window.location.search).get('request');
        if (requested && store().getRequisitions().some(r => r.id === requested && r.status === 'Pending')) openDirectorSignModal(requested);
    });
})();
