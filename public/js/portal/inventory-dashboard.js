(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.getElementById('director_inventory_pending_list');
        if (!list) return;
        const store = window.HopeHavenInventory;
        const escape = value => window.HopeHavenSync.escape(value);
        function render() {
            const pending = store.getRequisitions().filter(request => request.status === 'Pending');
            const items = store.getItems();
            const low = items.filter(item => item.onHand <= item.minThreshold).length;
            document.getElementById('director_inventory_pending_title').textContent = 'Staff Supply Requests (' + pending.length + ')';
            document.getElementById('director_inventory_stock_note').textContent = items.length + ' stock items · ' + low + ' running low';
            list.innerHTML = pending.slice(0, 4).map(request => {
                const item = store.findItem(request.item, request.destination);
                return `<div class="req-item-card"><div class="req-left-info"><div class="req-avatar"><i class="fas fa-box-open" aria-hidden="true"></i></div><div><div class="req-meta-title">${escape(request.item)}</div><div class="req-meta-sub">${escape(request.requestedBy || 'Staff')} · Requested: ${escape(request.qty)} ${escape(request.unit || item?.unit || 'units')}<br>In stock: ${escape(item?.onHand ?? 0)} ${escape(item?.unit || request.unit || 'units')}</div></div></div><a class="btn-director-review" href="${escape(list.dataset.reviewUrl)}?request=${encodeURIComponent(request.id)}">Review</a></div>`;
            }).join('') || '<p class="inventory-empty">No supply requests are waiting for approval.</p>';
        }
        render();
        window.HopeHavenSync.subscribe([store.keys.items, store.keys.requests], render);
    });
})();
