/* Connect Donor, Donation, Donation Item, Donation Allocation, and Donation Receipt views & forms to MariaDB (iowrms_db). */
(() => {
    'use strict';

    const SYNC_KEY = 'hopehaven_mariadb_donations_sync_ts';
    const esc = value => window.HopeHavenSync ? window.HopeHavenSync.escape(value) : String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const today = () => new Date().toLocaleDateString('en-CA');
    const initials = name => {
        const words = String(name || 'Donor').trim().split(/\s+/).filter(Boolean);
        if (words.length >= 2) return (words[0][0] + words[1][0]).toUpperCase();
        return (words[0] || 'DN').substring(0, 2).toUpperCase();
    };
    const fmtMoney = val => '$' + (Number(val) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

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
                : (result.message || 'Could not save or load MariaDB donation records.');
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

    const db = window.HopeHavenMariadbDonations = {
        data: {
            donor: [],
            donation: [],
            'donation-item': [],
            'donation-allocation': [],
            'donation-receipt': []
        },
        save: (endpoint, payload) => api('/donation-data/' + endpoint, payload),
        async refresh() {
            const snapshot = await api('/donation-data');
            db.data = Object.assign({}, db.data, snapshot);
            renderAll();
            document.dispatchEvent(new CustomEvent('mariadb-donations-loaded', {detail: db.data}));
            return db.data;
        }
    };

    function getEnrichedDonations() {
        const donors = db.data.donor || [];
        const donations = db.data.donation || [];
        const items = db.data['donation-item'] || [];
        const allocations = db.data['donation-allocation'] || [];
        const receipts = db.data['donation-receipt'] || [];

        return donations.map(don => {
            const donor = donors.find(d => Number(d.Donor_Id) === Number(don.Donor_Id));
            const receipt = receipts.find(r => Number(r.Donation_Id) === Number(don.Donation_Id));
            const donItems = items.filter(i => Number(i.Donation_id) === Number(don.Donation_Id));
            const primaryItem = donItems[0];
            const alloc = primaryItem
                ? allocations.find(a => Number(a.Donation_Item_Id) === Number(primaryItem.Donation_Item_Id))
                : null;
            const rawType = String(don.Donation_Type || 'Monetary');
            const isInKind = /in-kind|goods|material|item/i.test(rawType);
            const normType = isInKind ? 'In-Kind' : 'Monetary';
            const rcpRef = receipt?.Receipt_reference || ('RCP-' + new Date().getFullYear() + '-' + String(don.Donation_Id).padStart(4, '0'));

            return {
                donationId: don.Donation_Id,
                receiptNo: rcpRef,
                receiptDate: receipt?.Receipt_date || don.Donation_Date || today(),
                date: don.Donation_Date || today(),
                donorId: don.Donor_Id,
                donor: donor ? donor.Donor_name : ('Donor #' + don.Donor_Id),
                donorEmail: donor?.Email || '',
                donorPhone: donor?.Contact_number || '',
                type: normType,
                rawType: rawType,
                amount: Number(don.Amount) || 0,
                channel: rawType.includes('·') ? rawType.split('·')[1].trim() : (isInKind ? 'Hand-Delivered Goods' : 'Direct Contribution'),
                fund: primaryItem ? primaryItem.Item_name : 'General Orphanage Welfare',
                quantity: primaryItem ? Number(primaryItem.Quantity) || 1 : 1,
                allocatedQty: alloc ? Number(alloc.Quantity_Allocated) || 1 : (primaryItem ? Number(primaryItem.Quantity) || 1 : 1),
                orphanageId: alloc ? alloc.Orphanage_Id : 1,
                allocationDate: alloc ? alloc.Allocation_date : (don.Donation_Date || today()),
                staff: document.body?.dataset?.portalRole === 'director' ? 'Sister Sarah' : 'Nurse Mary',
                notes: primaryItem
                    ? `${primaryItem.Item_name} (Qty: ${primaryItem.Quantity}${alloc ? ' · Allocated to Orphanage #' + alloc.Orphanage_Id : ''})`
                    : 'Verified MariaDB contribution'
            };
        });
    }

    function getEnrichedDonors() {
        const donors = db.data.donor || [];
        const donations = db.data.donation || [];

        return donors.map(d => {
            const donorDonations = donations.filter(don => Number(don.Donor_Id) === Number(d.Donor_Id));
            const totalAmount = donorDonations.reduce((sum, don) => sum + (Number(don.Amount) || 0), 0);
            const tier = totalAmount >= 10000
                ? 'Platinum Patron'
                : totalAmount >= 3000
                    ? 'Gold Donor'
                    : totalAmount > 0
                        ? 'Silver Friend'
                        : 'Registered Patron';
            return {
                donorId: d.Donor_Id,
                code: 'DNR-' + String(1000 + Number(d.Donor_Id)),
                name: d.Donor_name,
                contact: d.Email || d.Contact_number || 'Not provided',
                email: d.Email || '',
                phone: d.Contact_number || '',
                category: 'Verified Donor',
                tier: tier,
                totalNumeric: totalAmount,
                total: fmtMoney(totalAmount),
                count: donorDonations.length
            };
        });
    }

    // 1. Render Staff Donations (/staff/donations) & Director Donations ledger (/director/donations)
    function renderStaffAndDirectorDonations() {
        const enrichedDonations = getEnrichedDonations();
        const enrichedDonors = getEnrichedDonors();
        if (enrichedDonations.length === 0 && enrichedDonors.length === 0) return;

        const tbody = document.getElementById('staff_donations_table_tbody');
        const donorsGrid = document.getElementById('staff_donors_grid');
        const cashLogList = document.getElementById('staff_cash_log_list');

        const totalMoney = enrichedDonations.reduce((sum, item) => sum + (Number(item.amount) || 0), 0);

        const setElText = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text; };
        if (enrichedDonations.length > 0) {
            setElText('kpi_total_money_owned', fmtMoney(totalMoney) + ' USD');
            setElText('kpi_total_donations_count', `${enrichedDonations.length} ${enrichedDonations.length === 1 ? 'Gift' : 'Gifts'}`);
            setElText('tab_donations_count', String(enrichedDonations.length));
        }
        if (enrichedDonors.length > 0) {
            setElText('kpi_total_donors_count', `${enrichedDonors.length} ${enrichedDonors.length === 1 ? 'Patron' : 'Patrons'}`);
            setElText('tab_donors_count', String(enrichedDonors.length));
            setElText('roster_badge_count', `${enrichedDonors.length} Registered Patrons`);
        }

        // Director Donations KPIs (.dir-don-kpi-val)
        const dirKpis = document.querySelectorAll('.dir-don-kpi-card .dir-don-kpi-val');
        if (dirKpis.length >= 3 && (enrichedDonations.length > 0 || enrichedDonors.length > 0)) {
            if (enrichedDonations.length > 0) {
                dirKpis[0].textContent = 'RM ' + totalMoney.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
            if (enrichedDonors.length > 0) {
                dirKpis[1].textContent = `${enrichedDonors.length} Donors`;
            }
        }

        if (tbody && enrichedDonations.length > 0) {
            const search = (document.getElementById('staff_donation_search')?.value || '').toLowerCase().trim();
            const typeFilter = document.getElementById('staff_donation_type_filter')?.value || 'all';
            const fundFilter = document.getElementById('staff_donation_fund_filter')?.value || 'all';

            const filtered = enrichedDonations.filter(item => {
                const matchesSearch = !search ||
                    item.donor.toLowerCase().includes(search) ||
                    item.receiptNo.toLowerCase().includes(search) ||
                    item.fund.toLowerCase().includes(search);
                const matchesType = typeFilter === 'all' || item.type === typeFilter;
                const matchesFund = fundFilter === 'all' || item.fund.toLowerCase().includes(fundFilter.toLowerCase());
                return matchesSearch && matchesType && matchesFund;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 30px; color: #94a3b8;">No donation records matching your filter criteria.</td></tr>';
            } else {
                tbody.innerHTML = filtered.map(d => `
                    <tr>
                        <td>
                            <strong style="color: #0f172a; font-family: monospace;">${esc(d.receiptNo)}</strong>
                            <div style="font-size: 11px; color: #64748b;">${esc(d.date)}</div>
                        </td>
                        <td>
                            <strong style="color: #0f172a; font-size: 13.5px;">${esc(d.donor)}</strong>
                            <div style="font-size: 11px; color: #64748b;">${esc(d.notes)}</div>
                        </td>
                        <td>
                            <span class="badge ${d.type === 'Monetary' ? 'badge-success' : 'badge-warning'}">${esc(d.type)}</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">${esc(d.channel)}</div>
                        </td>
                        <td>
                            <strong style="font-size: 14.5px; color: #065f46;">${fmtMoney(d.amount)}</strong>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight: 700; color: #1e40af;">${esc(d.fund)} (Qty: ${esc(d.allocatedQty)})</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary" style="font-size: 11px;">
                                <i class="fas fa-user-check"></i> ${esc(d.staff)}
                            </span>
                        </td>
                        <td class="staff-receipt-column" style="white-space: nowrap;">
                            <button type="button" class="table-action-btn staff-receipt-btn" onclick="viewDonationReceipt('${esc(d.receiptNo).replace(/'/g, "\\'")}')" title="View Official Receipt" aria-label="View receipt ${esc(d.receiptNo)}">
                                <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i><span>E-Receipt</span>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
        }

        if (donorsGrid && enrichedDonors.length > 0) {
            donorsGrid.innerHTML = enrichedDonors.map(d => `
                <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <span class="badge badge-info">${esc(d.code)}</span>
                        <span class="badge badge-success">${esc(d.tier)}</span>
                    </div>
                    <h4 style="font-size: 14.5px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">${esc(d.name)}</h4>
                    <div style="font-size: 11.5px; color: #64748b; margin-bottom: 12px;">Contact: ${esc(d.contact)}</div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                        <span style="font-size: 11px; color: #64748b; font-weight: 600;">Lifetime Giving (${d.count}):</span>
                        <strong style="color: #065f46; font-size: 14px;">${esc(d.total)}</strong>
                    </div>
                </div>
            `).join('');
        }

        if (cashLogList && enrichedDonations.length > 0) {
            cashLogList.innerHTML = enrichedDonations.map(c => `
                <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div>
                        <strong style="color: #0f172a; font-size: 13.5px;">${esc(c.donor)} &bull; ${fmtMoney(c.amount)} USD</strong>
                        <div style="font-size: 11.5px; color: #64748b;">${esc(c.date)} &bull; Receipt ${esc(c.receiptNo)} &bull; Purpose: ${esc(c.fund)}</div>
                    </div>
                    <span class="badge badge-success"><i class="fas fa-lock"></i> Saved in MariaDB</span>
                </div>
            `).join('');
        }
    }

    // 2. Render Admin Donations & Cash Flow Page (/donations)
    function renderAdminDonationsPage() {
        const donationsTbody = document.getElementById('donations_tbody');
        const tabDonorsGrid = document.querySelector('#tab_donors > div');
        const tabAllocationsGrid = document.querySelector('#tab_allocations > div');

        const enrichedDonations = getEnrichedDonations();
        const enrichedDonors = getEnrichedDonors();
        const allocations = db.data['donation-allocation'] || [];
        const items = db.data['donation-item'] || [];

        if (donationsTbody && enrichedDonations.length > 0) {
            donationsTbody.innerHTML = enrichedDonations.map(d => `
                <tr class="searchable-donation" data-category="${esc(d.type)}">
                    <td style="font-family: monospace; font-weight: 700;">${esc(d.receiptNo)}</td>
                    <td>
                        <strong>${esc(d.donor)}</strong>
                        <div style="font-size: 11px; color: #64748b;">${esc(d.donorEmail || d.donorPhone || 'Registered Patron')}</div>
                    </td>
                    <td><span class="badge ${d.type === 'Monetary' ? 'badge-success' : 'badge-warning'}">${esc(d.type)}</span></td>
                    <td>
                        <strong style="color: #10b981; font-size: 15px;">${fmtMoney(d.amount)} USD</strong>
                        <div style="font-size: 11px; color: #64748b;">${esc(d.rawType)}</div>
                    </td>
                    <td>
                        <strong>${esc(d.fund)} (Qty: ${esc(d.quantity)})</strong>
                        <div style="margin-top: 2px;"><span class="badge badge-info">Allocated to Orphanage #${esc(d.orphanageId)}</span></div>
                    </td>
                    <td style="font-size: 12px; color: #64748b;">${esc(d.date)}</td>
                    <td style="text-align: right;">
                        <button class="btn-outline btn-sm" onclick="openAdminReceiptModal('${esc(d.receiptNo).replace(/'/g, "\\'")}')">
                            <i class="fas fa-print"></i> E-Receipt
                        </button>
                    </td>
                </tr>
            `).join('');

            const contribTabBtn = document.querySelector('.tab-btn[data-tab="tab_contributions"]');
            if (contribTabBtn) {
                contribTabBtn.innerHTML = `<i class="fas fa-receipt"></i> HopeHaven Ledger (${enrichedDonations.length})`;
            }
        }

        if (tabDonorsGrid && enrichedDonors.length > 0) {
            tabDonorsGrid.innerHTML = enrichedDonors.map(d => `
                <div class="portal-card" style="padding: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <span class="badge badge-purple">${esc(d.tier)}</span>
                        <span style="font-family: monospace; font-size: 11px; color: #64748b;">${esc(d.code)}</span>
                    </div>
                    <h4 style="font-size: 16px; font-weight: 800; margin-top: 10px;">${esc(d.name)}</h4>
                    <span style="font-size: 12px; color: #14b8a6; font-weight: 600;">${esc(d.contact)}</span>
                    <div style="margin-top: 16px; font-size: 12px; display: flex; flex-direction: column; gap: 6px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Lifetime Giving:</span> <strong style="color: #10b981;">${esc(d.total)} USD</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Contribution Count:</span> <strong>${esc(d.count)} Donations</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Phone / Email:</span> <span style="font-family: monospace;">${esc(d.phone || d.email || 'Verified')}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            const donorsTabBtn = document.querySelector('.tab-btn[data-tab="tab_donors"]');
            if (donorsTabBtn) {
                donorsTabBtn.innerHTML = `<i class="fas fa-users"></i> Donor Registry (${enrichedDonors.length})`;
            }
        }

        if (tabAllocationsGrid && (allocations.length > 0 || items.length > 0)) {
            const allocCards = allocations.map(alloc => {
                const item = items.find(i => Number(i.Donation_Item_Id) === Number(alloc.Donation_Item_Id));
                const don = item ? enrichedDonations.find(d => Number(d.donationId) === Number(item.Donation_id)) : null;
                return `
                    <div class="portal-card" style="padding: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span class="badge badge-info">Allocation #${esc(alloc.Allocation_Id)}</span>
                            <span class="badge badge-success">Qty: ${esc(alloc.Quantity_Allocated)} Deployed</span>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 800;">${esc(item ? item.Item_name : 'Donation Item #' + alloc.Donation_Item_Id)}</h4>
                        <p style="font-size: 12.5px; color: #64748b; margin-top: 4px;">
                            ${don ? `Funded via ${fmtMoney(don.amount)} (${don.type}) from ${esc(don.donor)} (Receipt ${esc(don.receiptNo)}).` : 'Allocated from MariaDB donation item inventory.'}
                        </p>
                        <div style="background: #f8fafc; padding: 12px; border-radius: 6px; margin-top: 12px; font-size: 12px;">
                            <div>Target Orphanage ID: <strong>Orphanage #${esc(alloc.Orphanage_Id)}</strong></div>
                            <div>Allocation Date: <strong style="font-family: monospace;">${esc(alloc.Allocation_date)}</strong></div>
                        </div>
                    </div>
                `;
            });
            if (allocCards.length > 0) {
                tabAllocationsGrid.innerHTML = allocCards.join('');
            }
        }
    }

    // 3. Render Director Dashboard Donor Proof Table (/director) & Admin Dashboard (/dashboard)
    function renderDashboardDonations() {
        const enrichedDonations = getEnrichedDonations();
        if (enrichedDonations.length === 0) return;

        const totalMoney = enrichedDonations.reduce((sum, item) => sum + (Number(item.amount) || 0), 0);

        // Admin Dashboard subsystem meta
        const donMeta = document.querySelector('.subsystem-admin-card.s-donations .s-meta');
        if (donMeta) {
            donMeta.innerHTML = `<i class="fas fa-donate"></i> ${fmtMoney(totalMoney)} Inflows (${enrichedDonations.length})`;
        }

        // Director Dashboard Donation Receipts & Review table (.donor-table tbody)
        const donorTableTbody = document.querySelector('.donor-table tbody');
        if (donorTableTbody) {
            donorTableTbody.innerHTML = enrichedDonations.map(d => {
                const av = initials(d.donor);
                const amtStr = 'RM ' + d.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                return `
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="donor-avatar">${esc(av)}</div>
                                <div>
                                    <strong>${esc(d.donor)}</strong>
                                    <div style="font-size: 11px; color: #64748b;">${esc(d.donorEmail || d.donorPhone || 'Registered Patron')}</div>
                                </div>
                            </div>
                        </td>
                        <td><strong style="color: #059669; font-size: 14px;">${esc(amtStr)}</strong></td>
                        <td>${esc(d.rawType)}<br><span style="font-size: 11px; font-family: monospace; color: #475569;">#${esc(d.receiptNo)}</span></td>
                        <td>${esc(d.fund)} (Qty: ${esc(d.allocatedQty)})</td>
                        <td>${esc(d.date)}<br><span style="font-size: 11px; color: #64748b;">MariaDB Ledger</span></td>
                        <td>
                            <button type="button" class="proof-pill-btn" onclick="openDonationProofModal('${esc(d.donor).replace(/'/g, "\\'")}', '${esc(amtStr)}', '${esc(d.channel).replace(/'/g, "\\'")}', '${esc(d.receiptNo).replace(/'/g, "\\'")}', '${esc(d.date)}', '${esc(d.fund).replace(/'/g, "\\'")}', 'Verified')">
                                <i class="fas fa-file-pdf"></i> View Receipt
                            </button>
                        </td>
                        <td class="donor-status-cell">
                            <span class="status-pill" style="background:#ecfdf5; color:#047857; font-size: 11px; font-weight: 700;">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    }

    function renderAll() {
        renderStaffAndDirectorDonations();
        renderAdminDonationsPage();
        renderDashboardDonations();
    }

    // Receipt Modal Viewer on /donations
    window.openAdminReceiptModal = function (receiptNo) {
        const list = getEnrichedDonations();
        const item = list.find(d => d.receiptNo === receiptNo) || list[0];
        const modal = document.getElementById('modal_view_receipt');
        if (modal && item) {
            const sub = modal.querySelector('.modal-sub');
            if (sub) sub.textContent = `Receipt No: ${item.receiptNo}`;
            const strongs = modal.querySelectorAll('strong');
            if (strongs.length >= 5) {
                strongs[0].textContent = item.receiptDate;
                strongs[1].textContent = item.donor;
                strongs[2].textContent = `${fmtMoney(item.amount)} USD`;
                strongs[3].textContent = `${item.channel} (${item.type})`;
                strongs[4].textContent = `${item.fund} (Qty: ${item.allocatedQty})`;
            }
        }
        window.openModal?.('modal_view_receipt');
    };

    // Hook into viewDonationReceipt on /staff/donations & /director/donations
    const origViewDonationReceipt = window.viewDonationReceipt;
    window.viewDonationReceipt = function (receiptNo) {
        const list = getEnrichedDonations();
        const item = list.find(d => d.receiptNo === receiptNo);
        if (item) {
            const setTxt = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            setTxt('receipt_modal_ref', `Receipt No: ${item.receiptNo}`);
            setTxt('receipt_modal_date', `Date: ${item.receiptDate}`);
            setTxt('receipt_modal_donor', item.donor);
            setTxt('receipt_modal_amount', `${fmtMoney(item.amount)} USD`);
            setTxt('receipt_modal_method', `${item.channel} (${item.type})`);
            setTxt('receipt_modal_fund', `${item.fund} (Allocated Qty: ${item.allocatedQty})`);
            setTxt('receipt_modal_staff', `${item.staff} (Orphanage Caregiver)`);
            window.openStaffDonationModal?.('modal_staff_view_receipt');
            return;
        }
        if (typeof origViewDonationReceipt === 'function') origViewDonationReceipt(receiptNo);
    };

    // Intercept form submissions for all Donor & Donation modals
    document.addEventListener('submit', async event => {
        const form = event.target;

        // 1. Staff & Director Record Donation Modal (#modal_staff_record_donation)
        if (form.closest('#modal_staff_record_donation')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const donor = document.getElementById('staff_don_name')?.value?.trim() || '';
            const type = document.getElementById('staff_don_type')?.value || 'Monetary';
            const amount = parseFloat(document.getElementById('staff_don_amount')?.value) || 0;
            const channel = document.getElementById('staff_don_channel')?.value || 'Cash at Front Desk';
            const contact = document.getElementById('staff_don_contact')?.value?.trim() || '';
            const itemNameInput = document.getElementById('staff_don_item_name')?.value?.trim() || '';
            const itemQtyInput = parseInt(document.getElementById('staff_don_item_qty')?.value, 10) || 1;
            const notes = document.getElementById('staff_don_notes')?.value?.trim() || '';

            if (!donor || amount <= 0) {
                notify('Enter a donor name and a positive amount.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const res = await db.save('contribution', {
                    Donor_name: donor,
                    Contact_number: contact || null,
                    Donation_Date: today(),
                    Donation_Type: `${type} · ${channel}`.slice(0, 50),
                    Amount: amount,
                    Item_name: (itemNameInput || notes || 'General Orphanage Operations').slice(0, 100),
                    Quantity: Math.max(1, itemQtyInput),
                    Orphanage_Id: 1
                });

                const rcpNo = res.receipt?.Receipt_reference || 'RCP';
                form.reset();
                window.closeStaffDonationModal?.('modal_staff_record_donation');
                notify(`Contribution of ${fmtMoney(amount)} from ${donor} saved to MariaDB! Receipt #${rcpNo} generated.`, true);
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

        // 2. Admin Record Donation Modal (#modal_record_donation on /donations)
        if (form.closest('#modal_record_donation')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const donor = document.getElementById('don_name_input')?.value?.trim() || '';
            const typeSelect = form.querySelector('select');
            const type = typeSelect ? typeSelect.value : 'Monetary';
            const amount = parseFloat(document.getElementById('don_amount_input')?.value) || 0;
            const need = document.getElementById('don_need_input')?.value?.trim() || 'General Orphanage Welfare';
            const notes = form.querySelector('textarea')?.value?.trim() || '';

            if (!donor || amount <= 0) {
                notify('Enter a donor name and a positive amount.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const res = await db.save('contribution', {
                    Donor_name: donor,
                    Donation_Date: today(),
                    Donation_Type: type,
                    Amount: amount,
                    Item_name: (need || notes || 'General Orphanage Welfare').slice(0, 100),
                    Quantity: 1,
                    Orphanage_Id: 1
                });

                const rcpNo = res.receipt?.Receipt_reference || 'RCP';
                form.reset();
                window.closeModal?.('modal_record_donation');
                notify(`Donation of ${fmtMoney(amount)} saved to MariaDB! E-Receipt #${rcpNo} generated.`, true);
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

        // 3. Register Donor Modal (#modal_register_donor on /staff/donations, /director/donations, /donations)
        if (form.closest('#modal_register_donor')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const inputs = form.querySelectorAll('input');
            const name = (form.querySelector('input[name="name"]')?.value || inputs[0]?.value || '').trim();
            const contact = (form.querySelector('input[name="contact"]')?.value || inputs[1]?.value || '').trim();

            if (!name) {
                notify('Please enter the donor name.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                await db.save('donor', {
                    Donor_name: name,
                    Contact_number: contact || null,
                    Email: contact.includes('@') ? contact : null
                });

                form.reset();
                window.closeStaffDonationModal?.('modal_register_donor');
                window.closeModal?.('modal_register_donor');
                notify(`Donor "${name}" saved to MariaDB!`, true);
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
    }, true);

    document.addEventListener('DOMContentLoaded', () => {
        // Re-render when search/filter inputs change on /staff/donations
        ['staff_donation_search', 'staff_donation_type_filter', 'staff_donation_fund_filter'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', () => renderStaffAndDirectorDonations());
                el.addEventListener('change', () => renderStaffAndDirectorDonations());
            }
        });

        // Director Grant Release button inside #modal_grant_release
        window.confirmGrantRelease = async function () {
            const grantor = window.activeGrantor || document.getElementById('grant_release_grantor')?.innerText?.trim() || 'Institutional Grantor';
            const trancheRaw = document.getElementById('grant_release_tranche')?.innerText || '10000';
            const amount = parseFloat(String(trancheRaw).replace(/[^0-9.]/g, '')) || 10000;
            const directive = document.getElementById('grant_director_notes')?.value?.trim() || 'Approved Institutional Grant Tranche';

            window.closeDirectorModal?.('modal_grant_release');
            try {
                const res = await db.save('contribution', {
                    Donor_name: grantor,
                    Donation_Date: today(),
                    Donation_Type: 'Institutional Grant',
                    Amount: amount,
                    Item_name: directive.slice(0, 100),
                    Quantity: 1,
                    Orphanage_Id: 1
                });
                notify(`Grant payment (${fmtMoney(amount)}) from ${grantor} saved to MariaDB (Receipt #${res.receipt?.Receipt_reference || 'RCP'})!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            }
        };

        // Director Dashboard Verify Donation button
        window.verifyDonation = async function (donorName, amountStr, buttonEl) {
            if (buttonEl) {
                buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                buttonEl.disabled = true;
            }
            const amount = parseFloat(String(amountStr).replace(/[^0-9.]/g, '')) || 1200;
            try {
                await db.save('contribution', {
                    Donor_name: donorName,
                    Donation_Date: today(),
                    Donation_Type: 'Verified Online Transfer',
                    Amount: amount,
                    Item_name: 'Director Verified Contribution',
                    Quantity: 1,
                    Orphanage_Id: 1
                });
                notify(`Donation of ${amountStr} from ${donorName} verified and saved to MariaDB!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                if (buttonEl) {
                    buttonEl.innerHTML = 'Retry';
                    buttonEl.disabled = false;
                }
                notify(err.message, false);
            }
        };

        if (window.HopeHavenSync) {
            window.HopeHavenSync.subscribe(SYNC_KEY, () => db.refresh().catch(() => {}));
        }
    });

    db.ready = db.refresh();
    db.ready.catch(err => console.warn('MariaDB donations sync notice:', err.message));
})();
