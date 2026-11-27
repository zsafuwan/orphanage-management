/* Connect Welfare_Request, Request_Item, Approval_History, Notification, Inventory, Orphanage, and User views & forms to SQL Server (10.213.198.248 / QUERY_DB). */
(() => {
    'use strict';

    const SYNC_KEY = 'hopehaven_sqlsrv_welfare_sync_ts';
    const esc = value => window.HopeHavenSync ? window.HopeHavenSync.escape(value) : String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const escJs = value => String(value ?? '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");

    const api = async (url, payload) => {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch(url, {
            method: payload ? 'POST' : 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'Accept': 'application/json',
                ...(payload ? {'Content-Type': 'application/json'} : {}),
                ...(csrf ? {'X-CSRF-TOKEN': csrf} : {})
            },
            ...(payload ? {body: JSON.stringify(payload)} : {})
        });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) {
            const msg = response.status === 422
                ? Object.values(result.errors || {}).flat().join(' ')
                : (result.message || 'Could not save or load SQL Server welfare records.');
            throw new Error(msg);
        }
        return result;
    };

    const notify = (msg, ok = true) => {
        if (typeof window.showPortalToast === 'function') window.showPortalToast(msg, ok);
        else alert(msg);
    };

    const broadcastSync = () => {
        try {
            if (window.HopeHavenSync) window.HopeHavenSync.write(SYNC_KEY, String(Date.now()));
            else localStorage.setItem(SYNC_KEY, String(Date.now()));
        } catch (_) {}
    };

    const db = window.HopeHavenSqlsrvWelfare = {
        data: {
            'approval-history': [],
            'inventory': [],
            'notification': [],
            'orphanage': [],
            'request-item': [],
            'user': [],
            'welfare-request': []
        },
        save: (endpoint, payload) => api('/welfare-data/' + endpoint, payload),
        async refresh() {
            const snapshot = await api('/welfare-data');
            db.data = Object.assign({}, db.data, snapshot);
            syncWelfareStore();
            syncInventoryRequisitions();
            renderTopbarNotifications();
            renderRegisterUsers();
            renderDashboardWelfare();
            document.dispatchEvent(new CustomEvent('sqlsrv-welfare-loaded', {detail: db.data}));
            return db.data;
        }
    };

    function inferCategory(itemNames, rawCat) {
        if (rawCat && ['Medical / Surgical', 'Education & Tuition', 'Clothing & Bedding', 'Nutrition & Diet', 'Assistive Equipment'].includes(rawCat)) {
            return rawCat;
        }
        const joined = String(itemNames || '').toLowerCase();
        if (joined.includes('baju') || joined.includes('cloth') || joined.includes('bed') || joined.includes('quilt') || joined.includes('shoe') || joined.includes('linen')) return 'Clothing & Bedding';
        if (joined.includes('book') || joined.includes('calc') || joined.includes('school') || joined.includes('sekolah') || joined.includes('edu') || joined.includes('pen')) return 'Education & Tuition';
        if (joined.includes('beras') || joined.includes('minyak') || joined.includes('susu') || joined.includes('milk') || joined.includes('formula') || joined.includes('food') || joined.includes('nutr')) return 'Nutrition & Diet';
        return 'Medical / Surgical';
    }

    function inferInventoryCategory(itemName) {
        const s = String(itemName || '').toLowerCase();
        if (s.includes('baju') || s.includes('bed') || s.includes('linen') || s.includes('cloth')) return 'Linen';
        if (s.includes('book') || s.includes('sekolah') || s.includes('school') || s.includes('calc') || s.includes('pen')) return 'Education';
        if (s.includes('beras') || s.includes('minyak') || s.includes('susu') || s.includes('milk') || s.includes('formula') || s.includes('food')) return 'Nutrition';
        if (s.includes('wipe') || s.includes('diaper') || s.includes('shampoo') || s.includes('dettol') || s.includes('hyg')) return 'Hygiene';
        return 'Medical';
    }

    function parseNotificationMeta(requestId) {
        const notifs = db.data.notification || [];
        let title = null;
        let child = null;
        let cat = null;
        let urgency = null;
        let note = null;
        let fund = null;
        let remarks = null;

        notifs.forEach(n => {
            const msg = String(n.Message || '');
            if (msg.startsWith(`WR #${requestId}:`)) {
                const body = msg.replace(`WR #${requestId}:`, '').trim();
                const parts = body.split('|').map(p => p.trim());
                if (parts[0]) title = parts[0];
                parts.slice(1).forEach(p => {
                    if (p.startsWith('Child:')) child = p.replace('Child:', '').trim();
                    if (p.startsWith('Cat:')) cat = p.replace('Cat:', '').trim();
                    if (p.startsWith('Urgency:')) urgency = p.replace('Urgency:', '').trim();
                    if (p.startsWith('Note:')) note = p.replace('Note:', '').trim();
                });
            } else if (msg.startsWith(`WR #${requestId} `)) {
                const parts = msg.split('|').map(p => p.trim());
                parts.slice(1).forEach(p => {
                    if (p.startsWith('Fund:')) fund = p.replace('Fund:', '').trim();
                    if (p.startsWith('Remarks:')) remarks = p.replace('Remarks:', '').trim();
                });
            }
        });

        return {title, child, cat, urgency, note, fund, remarks};
    }

    function extractDbRequestId(ticketId) {
        const m = String(ticketId || '').match(/(?:WR|REQ)-(?:\d{4}-)?0*(\d+)$/i);
        if (m) return Number(m[1]);
        if (/^\d+$/.test(String(ticketId || ''))) return Number(ticketId);
        return null;
    }

    function syncWelfareStore() {
        if (!window.HopeHavenWelfareStore) return;
        const requests = db.data['welfare-request'] || [];
        if (requests.length === 0) return;

        const reqItems = db.data['request-item'] || [];
        const inv = db.data.inventory || [];
        const homes = db.data.orphanage || [];
        const users = db.data.user || [];
        const approvals = db.data['approval-history'] || [];

        const mapped = requests.map(req => {
            const reqId = Number(req.Request_Id);
            const home = homes.find(h => Number(h.Orphanage_Id) === Number(req.Orphanage_Id));
            const user = users.find(u => Number(u.User_Id) === Number(req.User_Id));
            const items = reqItems.filter(ri => Number(ri.Request_Id) === reqId);
            const approval = approvals.find(a => Number(a.Request_Id) === reqId);
            const approver = approval ? users.find(u => Number(u.User_Id) === Number(approval.User_Id)) : null;
            const meta = parseNotificationMeta(reqId);

            const lineItems = items.map(ri => {
                const invItem = inv.find(i => Number(i.Item_Id) === Number(ri.Item_Id));
                const itemName = invItem ? invItem.Name : `Supply Item #${ri.Item_Id}`;
                const qty = Number(ri.Quantity_Requested) || 1;
                return {
                    item: `${itemName} (${qty} units)`,
                    rawItem: itemName,
                    qty: qty,
                    cost: Number((qty * 15).toFixed(2))
                };
            });

            if (lineItems.length === 0) {
                const fallbackItem = inv[(reqId - 1) % Math.max(1, inv.length)];
                const itemName = fallbackItem ? fallbackItem.Name : 'Orphanage Welfare Assistance';
                lineItems.push({
                    item: `${itemName} (5 units)`,
                    rawItem: itemName,
                    qty: 5,
                    cost: 75.00
                });
            }

            const totalCost = lineItems.reduce((sum, li) => sum + (Number(li.cost) || 0), 0);
            const rawNames = lineItems.map(li => li.rawItem).join(', ');
            const rawStatus = String(req.Status || 'Pending');
            let uiStatus = 'Pending Admin Approval';
            if (/approv|receiv|dispat/i.test(rawStatus)) uiStatus = 'Approved';
            else if (/reject|declin|return/i.test(rawStatus)) uiStatus = 'Rejected';

            let adminDecision = null;
            if (uiStatus === 'Approved' || uiStatus === 'Rejected') {
                adminDecision = {
                    approvedBy: approver ? `${approver.Username} (${approver.Role})` : 'Sister Grace (admin01)',
                    decisionDate: approval?.Decision_Date || req.Request_Date || '2026-10-19',
                    fundingSource: meta.fund || (uiStatus === 'Approved' ? 'General Orphanage Welfare Allocation' : 'N/A'),
                    remarks: meta.remarks || (uiStatus === 'Approved'
                        ? `Approved in SQL Server QUERY_DB (Request #${reqId}).`
                        : `Declined in SQL Server QUERY_DB (Request #${reqId}).`),
                    disbursementRef: approval ? `APR-SQL-${String(approval.Approval_Id).padStart(3, '0')}` : `DISB-SQL-${String(reqId).padStart(3, '0')}`
                };
            }

            return {
                id: 'WR-2026-' + String(reqId).padStart(3, '0'),
                dbId: reqId,
                title: meta.title || `Welfare Supply Request: ${rawNames}`,
                category: inferCategory(rawNames, meta.cat),
                beneficiaryName: meta.child || (home ? home.Name : 'Rumah Anak Yatim Kasih'),
                beneficiaryId: 'ORM-' + String(req.Orphanage_Id || 1).padStart(3, '0'),
                dorm: home ? `${home.Name} (${home.Address})` : 'Rumah Anak Yatim Kasih',
                urgency: meta.urgency || (reqId % 2 === 1 ? 'High' : 'Standard'),
                cost: totalCost,
                lineItems: lineItems,
                justification: meta.note || `Official welfare & supply requisition logged in SQL Server (QUERY_DB) by ${user ? user.Username : 'staff'} for ${home ? home.Name : 'orphanage ward'}.`,
                requestedBy: user ? `${user.Username} (${user.Role})` : 'staff01 (Staff)',
                requestedDate: String(req.Request_Date || '').slice(0, 16) || '2026-10-19',
                status: uiStatus,
                adminDecision: adminDecision
            };
        });

        window.HopeHavenWelfareStore.saveAll(mapped);
        if (typeof window.renderAdminRequests === 'function') window.renderAdminRequests();
        if (typeof window.renderStaffRequests === 'function') window.renderStaffRequests();
    }

    function syncInventoryRequisitions() {
        if (!window.HopeHavenInventory) return;
        const requests = db.data['welfare-request'] || [];
        if (requests.length === 0) return;

        const reqItems = db.data['request-item'] || [];
        const inv = db.data.inventory || [];
        const homes = db.data.orphanage || [];
        const users = db.data.user || [];
        const approvals = db.data['approval-history'] || [];

        const mappedReqs = requests.map(req => {
            const reqId = Number(req.Request_Id);
            const home = homes.find(h => Number(h.Orphanage_Id) === Number(req.Orphanage_Id));
            const user = users.find(u => Number(u.User_Id) === Number(req.User_Id));
            const ri = reqItems.find(item => Number(item.Request_Id) === reqId);
            const invItem = ri ? inv.find(i => Number(i.Item_Id) === Number(ri.Item_Id)) : inv[(reqId - 1) % Math.max(1, inv.length)];
            const approval = approvals.find(a => Number(a.Request_Id) === reqId);
            const approver = approval ? users.find(u => Number(u.User_Id) === Number(approval.User_Id)) : null;
            const meta = parseNotificationMeta(reqId);

            const itemName = invItem ? invItem.Name : (meta.title || `Supply Item #${reqId}`);
            const qty = ri ? (Number(ri.Quantity_Requested) || 1) : 5;
            const rawStatus = String(req.Status || 'Pending');
            let status = 'Pending';
            if (/receiv/i.test(rawStatus) || (ri && Number(ri.Quantity_Fulfilled) >= qty && /approv/i.test(rawStatus))) {
                status = /receiv/i.test(rawStatus) ? 'Received' : 'Approved';
            } else if (/approv/i.test(rawStatus)) {
                status = 'Approved';
            } else if (/reject|declin|return/i.test(rawStatus)) {
                status = 'Rejected';
            }

            return {
                id: 'REQ-2026-' + String(reqId).padStart(3, '0'),
                dbId: reqId,
                item: itemName,
                itemId: invItem ? ('WINV-' + String(invItem.Item_Id).padStart(3, '0')) : null,
                unit: 'units',
                qty: qty,
                category: inferInventoryCategory(itemName),
                destination: home ? home.Name : 'Rumah Anak Yatim Kasih',
                priority: meta.urgency === 'Emergency' ? 'Critical' : (meta.urgency === 'High' ? 'Urgent' : 'Normal'),
                notes: meta.note || `SQL Server Requisition #${reqId} (${home ? home.Address : 'Campus'})`,
                requestedBy: user ? `${user.Username} (${user.Role})` : 'staff01',
                status: status,
                statusLabel: status === 'Approved' ? 'Approved in SQL Server' : (status === 'Rejected' ? 'Returned' : (status === 'Received' ? 'Received by Staff' : 'Awaiting Approval')),
                directorRemarks: meta.remarks || (approval ? `${approval.Decision} on ${String(approval.Decision_Date).slice(0, 16)}` : ''),
                reviewedBy: approver ? approver.Username : (status === 'Pending' ? '' : 'admin01'),
                time: String(req.Request_Date || '').slice(0, 16)
            };
        });

        window.HopeHavenInventory.saveRequisitions(mappedReqs);
        if (typeof window.renderAllStaffInventory === 'function') window.renderAllStaffInventory();
    }

    function renderTopbarNotifications() {
        const listEl = document.getElementById('dropdown_notif_list');
        const bellCountEl = document.getElementById('topbar_bell_count');
        const unreadBadgeEl = document.getElementById('dropdown_unread_badge');
        const notifs = db.data.notification || [];
        const users = db.data.user || [];

        if (!listEl || notifs.length === 0) return;

        const unreadCount = notifs.filter(n => String(n.Status || '').toLowerCase() !== 'read').length;
        if (bellCountEl) {
            bellCountEl.textContent = String(unreadCount);
            bellCountEl.style.display = unreadCount > 0 ? 'flex' : 'none';
        }
        if (unreadBadgeEl) {
            unreadBadgeEl.textContent = `${unreadCount} New`;
            unreadBadgeEl.style.background = unreadCount > 0 ? '#e2e8f0' : '#f1f5f9';
        }

        const colors = ['red', 'amber', 'green', 'purple'];
        const icons = ['fa-bell', 'fa-clipboard-check', 'fa-hand-holding-heart', 'fa-box-open'];

        listEl.innerHTML = notifs.map((n, idx) => {
            const isUnread = String(n.Status || '').toLowerCase() !== 'read';
            const user = users.find(u => Number(u.User_Id) === Number(n.User_Id));
            const color = colors[idx % colors.length];
            const icon = icons[idx % icons.length];
            const title = `SQL Notification #${n.Notification_Id} (${user ? user.Username : 'User #' + n.User_Id})`;
            const msg = String(n.Message || '');

            return `
                <div class="notif-item ${isUnread ? 'unread' : ''}" data-notif-id="${esc(n.Notification_Id)}" onclick="handleSqlsrvNotificationClick(${Number(n.Notification_Id)}, '${escJs(title)}', '${escJs(msg)}')">
                    <div class="notif-item-icon ${color}">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="notif-item-text">
                        <div class="notif-item-title">${esc(title)}</div>
                        <div class="notif-item-desc">${esc(msg)}</div>
                        <div class="notif-item-time">${esc(n.Status)} · User: ${esc(user ? `${user.Username} (${user.Role})` : '#' + n.User_Id)}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    window.handleSqlsrvNotificationClick = async function (notifId, title, details) {
        if (typeof window.handleNotificationClick === 'function') {
            window.handleNotificationClick(title, details);
        }
        try {
            await db.save('notifications/read', {notification_id: notifId});
            broadcastSync();
            await db.refresh();
        } catch (_) {}
    };

    function renderRegisterUsers() {
        const users = db.data.user || [];
        if (users.length === 0) return;

        // 1. /register (Admin Directory Table)
        const adminsTbody = document.getElementById('admins_tbody');
        if (adminsTbody) {
            adminsTbody.querySelectorAll('tr[data-sqlsrv-user]').forEach(tr => tr.remove());

            const gradients = [
                'linear-gradient(135deg, #ff2c5a, #eb1e4b)',
                'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                'linear-gradient(135deg, #10b981, #059669)',
                'linear-gradient(135deg, #8b5cf6, #6d28d9)'
            ];

            const rowsHtml = users.map((u, idx) => {
                const code = `USR-SQL-${String(u.User_Id).padStart(3, '0')}`;
                const username = String(u.Username || 'user');
                const role = String(u.Role || 'Staff');
                const initials = username.replace(/[^A-Za-z0-9]/g, '').slice(0, 2).toUpperCase() || 'US';
                const email = username.includes('@') ? username : `${username.toLowerCase().replace(/[^a-z0-9.]/g, '')}@hopehaven.org`;
                const grad = gradients[idx % gradients.length];

                return `
                    <tr data-sqlsrv-user="true" data-admin-id="${esc(code)}" data-name="${esc(username)}" data-email="${esc(email)}">
                        <td>
                            <span class="badge-admin-id">
                                <i class="fas fa-database"></i> ${esc(code)}
                            </span>
                        </td>
                        <td>
                            <div class="admin-profile-cell">
                                <div class="admin-avatar" style="background: ${grad};">${esc(initials)}</div>
                                <div>
                                    <div class="admin-cell-name">${esc(username)}</div>
                                    <div class="admin-cell-email">SQL Server User #${esc(u.User_Id)}</div>
                                </div>
                            </div>
                        </td>
                        <td><code>${esc(email)}</code></td>
                        <td>
                            <span class="badge-role ${role.toLowerCase() === 'admin' ? 'super' : 'welfare'}">
                                <i class="fas fa-user-shield"></i> ${esc(role)}
                            </span>
                        </td>
                        <td><span style="color: #334155; font-size: 12px; font-weight: 500;">QUERY_DB (10.213.198.248)</span></td>
                        <td>
                            <span class="status-indicator active">
                                <span class="status-dot"></span> Active
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <button class="table-action-btn" onclick="viewCredentialsModal('${escJs(code)}', '${escJs(username)}', '${escJs(email)}', '${escJs(role)}', 'SQL Server QUERY_DB')">
                                <i class="fas fa-file-invoice"></i> Voucher
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            adminsTbody.insertAdjacentHTML('afterbegin', rowsHtml);
            const totalRows = adminsTbody.querySelectorAll('tr').length;
            const statTotal = document.getElementById('stat_total_admins');
            const badgeCount = document.getElementById('admin_count_badge');
            if (statTotal) statTotal.textContent = `${totalRows} Accounts`;
            if (badgeCount) badgeCount.textContent = `${totalRows} Registered`;
        }

        // 2. /director/register-staff (Recent Staff List)
        const recentStaffList = document.getElementById('recent_staff_list');
        if (recentStaffList) {
            recentStaffList.querySelectorAll('[data-sqlsrv-user]').forEach(el => el.remove());
            const staffHtml = users.map(u => {
                const username = String(u.Username || 'staff');
                const role = String(u.Role || 'Staff');
                const initials = username.replace(/[^A-Za-z0-9]/g, '').slice(0, 2).toUpperCase() || 'ST';
                return `
                    <div class="recent-staff-item" data-sqlsrv-user="true">
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#e0e7ff; color:#4338ca;">${esc(initials)}</div>
                            <div>
                                <div class="user-cell-name">${esc(username)}</div>
                                <div class="user-cell-dept">${esc(role)} · SQL-USR-${String(u.User_Id).padStart(2, '0')}</div>
                            </div>
                        </div>
                        <span class="vetted-badge"><i class="fas fa-check-circle"></i> Active</span>
                    </div>
                `;
            }).join('');
            recentStaffList.insertAdjacentHTML('afterbegin', staffHtml);
        }
    }

    function renderDashboardWelfare() {
        const requests = window.HopeHavenWelfareStore?.getAll?.() || [];
        if (requests.length === 0) return;

        // Update /dashboard Welfare Requests KPI card & Recent Support Provided table
        const pendingCount = requests.filter(r => r.status === 'Pending Admin Approval').length;
        const kpiPurple = document.querySelector('.kpi-card.c-purple');
        if (kpiPurple) {
            const pill = kpiPurple.querySelector('.kpi-pill');
            const val = kpiPurple.querySelector('.kpi-value');
            if (pill) pill.innerHTML = `<i class="fas fa-clock"></i> ${pendingCount} Review`;
            if (val) val.textContent = `${requests.length} Tickets`;
        }
        const quickLinkMeta = document.querySelector('.subsystem-admin-card.s-requests .s-meta');
        if (quickLinkMeta) {
            quickLinkMeta.innerHTML = `<i class="fas fa-clipboard-check"></i> ${pendingCount} Pending Review`;
        }

        const recentSupportTbody = document.querySelector('.adminator-table tbody');
        if (recentSupportTbody) {
            recentSupportTbody.innerHTML = requests.slice(0, 5).map(r => {
                const tagClass = r.status === 'Approved' ? 'tag-new' : (r.status === 'Rejected' ? 'tag-unavail' : 'tag-used');
                const tagLabel = r.status === 'Pending Admin Approval' ? 'Pending' : r.status;
                return `
                    <tr>
                        <td><strong>${esc(r.title)}</strong></td>
                        <td><span class="adminator-tag ${tagClass}">${esc(tagLabel)}</span></td>
                        <td>${esc(r.beneficiaryName)}</td>
                        <td style="text-align: right; font-weight: 700; color: #10b981;">$${Number(r.cost || 0).toFixed(0)}</td>
                    </tr>
                `;
            }).join('');
        }
    }

    // Intercept form submissions across Welfare Requests, Approvals, Director Reviews, and User Registration
    document.addEventListener('submit', async event => {
        const form = event.target;

        // 1. Staff Submit New Welfare Request (#modal_staff_create_request on /staff/welfare-requests)
        if (form.closest('#modal_staff_create_request')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const childParts = (document.getElementById('req_child_select')?.value || 'Maya Cruz|CH-2024-001|Rumah Anak Yatim Kasih').split('|');
            const childName = childParts[0] || 'Maya Cruz';
            const childId = childParts[1] || 'CH-2024-001';
            const dorm = childParts[2] || 'Rumah Anak Yatim Kasih';

            const title = document.getElementById('req_title_input')?.value?.trim() || '';
            const category = document.getElementById('req_cat_input')?.value || 'Medical / Surgical';
            const urgency = document.getElementById('req_urgency_input')?.value || 'High';
            const justification = document.getElementById('req_justification_input')?.value?.trim() || '';

            const lineItems = [];
            let totalCost = 0;
            document.querySelectorAll('#requisition_items_container .req-line-item-row').forEach(row => {
                const name = row.querySelector('.line-item-name')?.value?.trim() || '';
                const cost = parseFloat(row.querySelector('.line-item-cost')?.value) || 0;
                if (name) {
                    lineItems.push({
                        item: name,
                        qty: Math.max(1, Math.round(cost / 15) || 1),
                        cost: cost
                    });
                    totalCost += cost;
                }
            });

            if (!title || lineItems.length === 0) {
                notify('Please enter a request title and at least one item.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const res = await db.save('request', {
                    title: title,
                    category: category,
                    beneficiary_name: childName,
                    beneficiary_id: childId,
                    dorm: dorm,
                    urgency: urgency,
                    cost: totalCost,
                    justification: justification,
                    requested_by: 'staff01',
                    line_items: lineItems
                });

                window.closeStaffWelfareModal?.('modal_staff_create_request');
                form.reset();
                const reqId = res.request?.Request_Id || '';
                notify(`Welfare request #WR-2026-${String(reqId).padStart(3, '0')} ($${totalCost.toFixed(2)}) saved to SQL Server QUERY_DB!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 2. Admin Approve Welfare Request (#modal_admin_approve on /welfare-requests)
        if (form.closest('#modal_admin_approve')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const ticketId = document.getElementById('approve_ticket_id')?.value || '';
            const fundSource = document.getElementById('approve_fund_source')?.value || 'General Orphanage Welfare Allocation';
            const remarks = document.getElementById('approve_remarks')?.value?.trim() || 'Approved for beneficiary welfare.';
            const adminName = document.getElementById('approve_admin_name')?.value || 'admin01';
            const dbReqId = extractDbRequestId(ticketId);

            if (!dbReqId) {
                notify('Invalid Welfare Request ID.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                await db.save('decision', {
                    request_id: dbReqId,
                    decision: 'Approved',
                    approved_by: adminName,
                    funding_source: fundSource,
                    remarks: remarks
                });

                window.closeAdminModal?.('modal_admin_approve');
                notify(`Welfare Request #${ticketId} approved and logged in SQL Server Approval_History!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 3. Admin Reject Welfare Request (#modal_admin_reject on /welfare-requests)
        if (form.closest('#modal_admin_reject')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const ticketId = document.getElementById('reject_ticket_id')?.value || '';
            const reason = document.getElementById('reject_reason')?.value?.trim() || 'Declined after executive review.';
            const dbReqId = extractDbRequestId(ticketId);

            if (!dbReqId) {
                notify('Invalid Welfare Request ID.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                await db.save('decision', {
                    request_id: dbReqId,
                    decision: 'Rejected',
                    approved_by: 'admin01',
                    funding_source: 'N/A',
                    remarks: reason
                });

                window.closeAdminModal?.('modal_admin_reject');
                notify(`Welfare Request #${ticketId} declined and saved to SQL Server Approval_History.`, false);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 4. Staff Supply Request Modal (#modal_staff_request_supplies on /staff/inventory) -> also saves to SQL Server Welfare_Request + Request_Item
        if (form.closest('#modal_staff_request_supplies')) {
            const cat = document.getElementById('req_category_select')?.value || 'Medical';
            const itemName = document.getElementById('req_item_input')?.value?.trim() || '';
            const qty = parseInt(document.getElementById('req_qty_input')?.value, 10) || 1;
            const dest = document.getElementById('req_destination_input')?.value?.trim() || 'Rumah Anak Yatim Kasih';
            const priority = document.getElementById('req_priority_select')?.value || 'Normal';
            const notes = document.getElementById('req_notes_input')?.value?.trim() || 'Routine supply request';

            if (itemName) {
                db.save('request', {
                    title: `${itemName} (${qty} units)`,
                    category: cat,
                    beneficiary_name: dest,
                    dorm: dest,
                    urgency: priority === 'Critical' ? 'Emergency' : (priority === 'Urgent' ? 'High' : 'Standard'),
                    cost: qty * 15,
                    justification: notes,
                    requested_by: 'staff01',
                    line_items: [{item: itemName, qty: qty, cost: qty * 15}]
                }).then(() => {
                    broadcastSync();
                    return db.refresh();
                }).catch(() => {});
            }
            return;
        }

        // 5. Admin Provision Form (#admin_provision_form on /register) -> saves to SQL Server dbo.[User] & dbo.Notification
        if (form.id === 'admin_provision_form') {
            const fullName = document.getElementById('admin_full_name')?.value?.trim() || '';
            const email = document.getElementById('admin_email')?.value?.trim() || '';
            const adminId = document.getElementById('admin_id')?.value?.trim() || '';
            const pw = document.getElementById('admin_password')?.value || '';
            const pwConfirm = document.getElementById('admin_password_confirmation')?.value || '';

            if (fullName && pw && pw === pwConfirm) {
                db.save('user', {
                    username: fullName,
                    role: 'Admin',
                    email: email,
                    admin_id: adminId
                }).then(() => {
                    broadcastSync();
                    return db.refresh();
                }).catch(err => console.warn('SQL Server user save error:', err.message));
            }
            return;
        }

        // 6. Director Register Staff Form (.dir-reg-form on /director/register-staff) -> saves to SQL Server dbo.[User]
        if (form.classList.contains('dir-reg-form')) {
            const name = document.getElementById('staff_name_input')?.value?.trim() || '';
            const email = document.getElementById('staff_email_input')?.value?.trim() || '';
            const staffId = document.getElementById('staff_id_input')?.value?.trim() || '';

            if (name) {
                db.save('user', {
                    username: name,
                    role: 'Staff',
                    email: email,
                    admin_id: staffId
                }).then(() => {
                    broadcastSync();
                    return db.refresh();
                }).catch(err => console.warn('SQL Server staff user save error:', err.message));
            }
            return;
        }
    }, true);

    document.addEventListener('DOMContentLoaded', () => {
        // Hook markAllNotificationsRead in topbar to update SQL Server dbo.Notification
        const origMarkRead = window.markAllNotificationsRead;
        window.markAllNotificationsRead = async function (e) {
            if (typeof origMarkRead === 'function') origMarkRead(e);
            try {
                await db.save('notifications/read', {});
                broadcastSync();
                await db.refresh();
            } catch (_) {}
        };

        // Hook Director Inventory Sign & Reject buttons on /director/inventory-requests to also save to SQL Server Approval_History & Welfare_Request
        const origSubmitDirectorSign = window.submitDirectorSign;
        if (typeof origSubmitDirectorSign === 'function') {
            window.submitDirectorSign = async function () {
                const reqIdText = document.getElementById('sign_req_id')?.textContent || '';
                const remarks = document.getElementById('director_approval_remarks')?.value || 'Approved by Director';
                const checked = document.getElementById('director_auth_check')?.checked;
                origSubmitDirectorSign();
                if (!checked) return;
                const dbReqId = extractDbRequestId(reqIdText);
                if (dbReqId) {
                    try {
                        await db.save('decision', {
                            request_id: dbReqId,
                            decision: 'Approved',
                            approved_by: 'manager01',
                            funding_source: 'Central Storeroom Inventory Allocation',
                            remarks: remarks
                        });
                        broadcastSync();
                        await db.refresh();
                    } catch (_) {}
                }
            };
        }

        const origSubmitDirectorReject = window.submitDirectorReject;
        if (typeof origSubmitDirectorReject === 'function') {
            window.submitDirectorReject = async function () {
                const reqIdText = document.getElementById('reject_req_id_input')?.value || '';
                const reason = document.getElementById('director_reject_reason')?.value?.trim() || '';
                origSubmitDirectorReject();
                if (!reason) return;
                const dbReqId = extractDbRequestId(reqIdText);
                if (dbReqId) {
                    try {
                        await db.save('decision', {
                            request_id: dbReqId,
                            decision: 'Rejected',
                            approved_by: 'manager01',
                            funding_source: 'N/A',
                            remarks: reason
                        });
                        broadcastSync();
                        await db.refresh();
                    } catch (_) {}
                }
            };
        }

        if (window.HopeHavenSync) {
            window.HopeHavenSync.subscribe(SYNC_KEY, () => db.refresh().catch(() => {}));
        }
    });

    db.ready = db.refresh();
    db.ready.catch(err => console.warn('SQL Server welfare sync notice:', err.message));
})();
