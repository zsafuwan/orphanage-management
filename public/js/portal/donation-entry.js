
    const STAFF_DONATIONS_STORAGE_KEY = 'hopehaven_staff_donations_v1';

    // Seed Data
    const DEFAULT_STAFF_DONATIONS = [
        {
            receiptNo: 'RCP-2026-0881',
            date: '2026-10-20',
            donor: 'The Meridian Heritage Foundation',
            type: 'Monetary',
            amount: 5000.00,
            channel: 'Direct Bank Wire',
            fund: 'Child Medical & Dental Care',
            staff: 'Nurse Mary',
            notes: 'Quarterly pediatric healthcare fund contribution.'
        },
        {
            receiptNo: 'RCP-2026-0882',
            date: '2026-10-18',
            donor: 'Dr. Gregory & Eleanor Vance',
            type: 'Monetary',
            amount: 2500.00,
            channel: 'Cheque Deposit',
            fund: 'Higher Education & Tuition',
            staff: 'Sister Grace',
            notes: 'Secondary school scholarship grant for Elijah and Maya.'
        },
        {
            receiptNo: 'RCP-2026-0883',
            date: '2026-10-15',
            donor: 'Evergreen Food Staples Ltd',
            type: 'In-Kind',
            amount: 1400.00,
            channel: 'Hand-Delivered Goods',
            fund: 'Nutritional Food & Milk Nursery',
            staff: 'Nurse Mary',
            notes: '15 sacks jasmine rice, 20 cans formula milk delivered to kitchen.'
        },
        {
            receiptNo: 'RCP-2026-0884',
            date: '2026-10-14',
            donor: 'Rotary Club Titiwangsa',
            type: 'Monetary',
            amount: 1800.00,
            channel: 'Cash at Front Desk',
            fund: 'Child Medical & Dental Care',
            staff: 'Nurse Mary',
            notes: 'Special donation handed over during Saturday charity visit.'
        },
        {
            receiptNo: 'RCP-2026-0885',
            date: '2026-10-10',
            donor: 'Puan Sri Halimah & Family',
            type: 'Monetary',
            amount: 850.00,
            channel: 'Online Payment Gateway',
            fund: 'Dorm Clothing & Bedding',
            staff: 'Brother Thomas',
            notes: 'Winter fleece blankets and footwear allowance for boys hall.'
        },
        {
            receiptNo: 'RCP-2026-0886',
            date: '2026-10-08',
            donor: 'Beacon Medical Logistics',
            type: 'In-Kind',
            amount: 1200.00,
            channel: 'Hand-Delivered Goods',
            fund: 'Child Medical & Dental Care',
            staff: 'Nurse Mary',
            notes: 'Pediatric first aid supplies, gauze packs, and thermometer units.'
        },
        {
            receiptNo: 'RCP-2026-0887',
            date: '2026-10-05',
            donor: 'St. Jude Alumni Association',
            type: 'Monetary',
            amount: 1100.00,
            channel: 'Direct Bank Wire',
            fund: 'Higher Education & Tuition',
            staff: 'Sister Grace',
            notes: 'Annual school stationery and computer lab maintenance fund.'
        },
        {
            receiptNo: 'RCP-2026-0888',
            date: '2026-10-02',
            donor: 'Local Community Care Circle',
            type: 'Monetary',
            amount: 450.00,
            channel: 'Cash at Front Desk',
            fund: 'Nutritional Food & Milk Nursery',
            staff: 'Nurse Mary',
            notes: 'Collected from weekend community farmers market bake sale.'
        }
    ];

    const DEFAULT_DONORS = [
        { name: 'The Meridian Heritage Foundation', tier: 'Platinum Patron', total: '$45,000.00', contact: 'giving@meridian.org', category: 'Foundation' },
        { name: 'Dr. Gregory & Eleanor Vance', tier: 'Gold Donor', total: '$18,500.00', contact: 'vance.family@net.com', category: 'Individual' },
        { name: 'Evergreen Food Staples Ltd', tier: 'Corporate Sponsor', total: '$12,400.00', contact: 'csr@evergreen.com', category: 'Corporate' },
        { name: 'Rotary Club Titiwangsa', tier: 'Community Circle', total: '$8,200.00', contact: 'secretary@rotarykl.org', category: 'Civic Club' },
        { name: 'Puan Sri Halimah & Family', tier: 'Silver Friend', total: '$4,800.00', contact: 'halimah.charity@my.org', category: 'Individual' },
        { name: 'Beacon Medical Logistics', tier: 'Healthcare Partner', total: '$6,500.00', contact: 'supplies@beaconmed.com', category: 'Corporate' }
    ];

    const StaffDonationsStore = {
        getAll: function() {
            try {
                const data = localStorage.getItem(STAFF_DONATIONS_STORAGE_KEY);
                return data ? JSON.parse(data) : DEFAULT_STAFF_DONATIONS;
            } catch (e) {
                return DEFAULT_STAFF_DONATIONS;
            }
        },
        saveAll: function(list) {
            window.HopeHavenSync.write(STAFF_DONATIONS_STORAGE_KEY, JSON.stringify(list));
        }
    };

    function openStaffDonationModal(id) {
        $('#' + id).css('display', 'flex').hide().fadeIn(200);
    }

    function closeStaffDonationModal(id) {
        $('#' + id).fadeOut(150);
    }

    function renderStaffDonations() {
        const list = StaffDonationsStore.getAll();
        const search = ($('#staff_donation_search').val() || '').toLowerCase().trim();
        const typeFilter = $('#staff_donation_type_filter').val() || 'all';
        const fundFilter = $('#staff_donation_fund_filter').val() || 'all';

        // 1. Calculate Metrics
        let totalMoney = 34350.00; // Base treasury
        const donorsSet = new Set();
        getRegisteredDonors().forEach(d => donorsSet.add(d.name));

        list.forEach(item => {
            if (item.type === 'Monetary') {
                totalMoney += parseFloat(item.amount) || 0;
            }
            if (item.donor) {
                donorsSet.add(item.donor);
            }
        });

        $('#kpi_total_money_owned').text('$' + totalMoney.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD');
        $('#kpi_total_donors_count').text(donorsSet.size + ' Patrons');
        $('#kpi_total_donations_count').text(list.length + ' Gifts');
        $('#tab_donations_count').text(list.length);
        $('#tab_donors_count').text(donorsSet.size);

        // 2. Filter Table Items
        const filtered = list.filter(item => {
            const matchesSearch = !search || 
                item.donor.toLowerCase().includes(search) || 
                item.receiptNo.toLowerCase().includes(search) || 
                item.fund.toLowerCase().includes(search);
            const matchesType = typeFilter === 'all' || item.type === typeFilter;
            const matchesFund = fundFilter === 'all' || item.fund.toLowerCase().includes(fundFilter.toLowerCase());
            return matchesSearch && matchesType && matchesFund;
        });

        let tableHtml = '';
        if (filtered.length === 0) {
            tableHtml = '<tr><td colspan="7" style="text-align: center; padding: 30px; color: #94a3b8;">No donation records matching your filter criteria.</td></tr>';
        } else {
            filtered.forEach((d, idx) => {
                tableHtml += `
                    <tr>
                        <td>
                            <strong style="color: #0f172a; font-family: monospace;">${d.receiptNo}</strong>
                            <div style="font-size: 11px; color: #64748b;">${d.date}</div>
                        </td>
                        <td>
                            <strong style="color: #0f172a; font-size: 13.5px;">${escapeHtml(d.donor)}</strong>
                            <div style="font-size: 11px; color: #64748b;">${escapeHtml(d.notes || 'Verified contribution')}</div>
                        </td>
                        <td>
                            <span class="badge ${d.type === 'Monetary' ? 'badge-success' : 'badge-warning'}">${d.type}</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">${escapeHtml(d.channel)}</div>
                        </td>
                        <td>
                            <strong style="font-size: 14.5px; color: #065f46;">$${parseFloat(d.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight: 700; color: #1e40af;">${escapeHtml(d.fund)}</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary" style="font-size: 11px;">
                                <i class="fas fa-user-check"></i> ${escapeHtml(d.staff)}
                            </span>
                        </td>
                        <td class="staff-receipt-column" style="white-space: nowrap;">
                            <button type="button" class="table-action-btn staff-receipt-btn" onclick="viewDonationReceipt('${d.receiptNo}')" title="View Official Receipt" aria-label="View receipt ${escapeHtml(d.receiptNo)}">
                                <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i><span>E-Receipt</span>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#staff_donations_table_tbody').html(tableHtml);

        // 3. Render Donors Roster Cards
        renderDonorsRoster();

        // 4. Render Cash Log
        renderCashLog(list);
    }

    function renderDonorsRoster() {
        let gridHtml = '';
        getRegisteredDonors().forEach(d => {
            gridHtml += `
                <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <span class="badge badge-info">${escapeHtml(d.category)}</span>
                        <span class="badge badge-success">${escapeHtml(d.tier)}</span>
                    </div>
                    <h4 style="font-size: 14.5px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0;">${escapeHtml(d.name)}</h4>
                    <div style="font-size: 11.5px; color: #64748b; margin-bottom: 12px;">Contact: ${escapeHtml(d.contact)}</div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                        <span style="font-size: 11px; color: #64748b; font-weight: 600;">Lifetime Giving:</span>
                        <strong style="color: #065f46; font-size: 14px;">${escapeHtml(d.total)}</strong>
                    </div>
                </div>
            `;
        });
        $('#staff_donors_grid').html(gridHtml);
    }

    function renderCashLog(list) {
        const cashItems = list.filter(d => d.channel === 'Cash at Front Desk');
        let html = '';
        if (cashItems.length === 0) {
            html = '<div style="padding: 20px; text-align: center; color: #94a3b8;">No front desk cash donations logged yet.</div>';
        } else {
            cashItems.forEach(c => {
                html += `
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div>
                            <strong style="color: #0f172a; font-size: 13.5px;">${escapeHtml(c.donor)} &bull; $${parseFloat(c.amount).toFixed(2)} USD</strong>
                            <div style="font-size: 11.5px; color: #64748b;">${c.date} &bull; Received by ${escapeHtml(c.staff)} &bull; Purpose: ${escapeHtml(c.fund)}</div>
                        </div>
                        <span class="badge badge-success"><i class="fas fa-lock"></i> Banked in Vault</span>
                    </div>
                `;
            });
        }
        $('#staff_cash_log_list').html(html);
    }

    function viewDonationReceipt(receiptNo) {
        const list = StaffDonationsStore.getAll();
        const item = list.find(d => d.receiptNo === receiptNo);
        if (!item) return;

        $('#receipt_modal_ref').text(`Receipt No: ${item.receiptNo}`);
        $('#receipt_modal_date').text(`Date: ${item.date}`);
        $('#receipt_modal_donor').text(item.donor);
        $('#receipt_modal_amount').text(`$${parseFloat(item.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} USD`);
        $('#receipt_modal_method').text(`${item.channel} (${item.type})`);
        $('#receipt_modal_fund').text(item.fund);
        $('#receipt_modal_staff').text(`${item.staff} (Orphanage Caregiver)`);

        openStaffDonationModal('modal_staff_view_receipt');
    }

    function handleStaffDonationSubmit(e) {
        e.preventDefault();
        const donor = $('#staff_don_name').val().trim();
        const type = $('#staff_don_type').val();
        const amount = parseFloat($('#staff_don_amount').val()) || 0;
        const channel = $('#staff_don_channel').val();
        const fund = 'General Orphanage Operations';
        const notes = $('#staff_don_notes').val().trim();

        if (!donor || !Number.isFinite(amount) || amount <= 0) { showPortalToast('Enter a donor name and a positive amount.', false); return; }
        const list = StaffDonationsStore.getAll();
        const rcpNo = window.HopeHavenSync.nextId(list.map(item => ({id: item.receiptNo})), 'RCP');
        const today = new Date().toISOString().split('T')[0];

        const newDonation = {
            receiptNo: rcpNo,
            date: today,
            donor: donor,
            type: type,
            amount: amount,
            channel: channel,
            fund: fund,
            staff: document.body.dataset.portalRole === 'director' ? 'Sister Sarah' : 'Nurse Mary',
            contact: $('#staff_don_contact').val().trim(),
            notes: notes || 'Logged at staff duty desk.'
        };

        list.unshift(newDonation);
        StaffDonationsStore.saveAll(list);

        closeStaffDonationModal('modal_staff_record_donation');
        e.target.reset();
        renderStaffDonations();

        showPortalToast(
            `Contribution of $${amount.toFixed(2)} from ${donor} logged! E-Receipt #${rcpNo} generated.`,
            true,
            'Donation Recorded Successfully',
            'Staff Treasury Desk'
        );
    }

    function toggleDonationTypeFields() {
        const type = $('#staff_don_type').val();
        if (type === 'In-Kind') {
            $('#staff_don_channel').val('Hand-Delivered Goods');
        } else {
            $('#staff_don_channel').val('Cash at Front Desk');
        }
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
        $('.tab-btn').on('click', function() {
            const target = $(this).data('tab');
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.tab-content-panel').hide();
            $('#' + target).fadeIn(150);
        });

        renderStaffDonations();
        window.HopeHavenSync.subscribe([STAFF_DONATIONS_STORAGE_KEY, 'hopehaven_registered_donors_v1'], function() {
            window.HopeHavenSync.refreshView(renderStaffDonations);
        });
    });

function getRegisteredDonors() {
    try { const saved = JSON.parse(localStorage.getItem('hopehaven_registered_donors_v1') || 'null'); if (Array.isArray(saved)) return saved; } catch (_) {}
    return DEFAULT_DONORS.map(d => ({...d}));
}
function handleRegisterDonor(event) {
    event.preventDefault();
    const form = event.target;
    const data = new FormData(form);
    const name = String(data.get('name') || '').trim();
    const donors = getRegisteredDonors();
    if (!name) return;
    if (donors.some(d => d.name.toLowerCase() === name.toLowerCase())) { showPortalToast('This donor is already registered.', false); return; }
    donors.unshift({name, category: data.get('category'), tier: data.get('tier'), contact: String(data.get('contact') || '').trim(), total: '$0.00'});
    try {
        window.HopeHavenSync.write('hopehaven_registered_donors_v1', JSON.stringify(donors));
        closeStaffDonationModal('modal_register_donor'); form.reset(); renderStaffDonations();
        showPortalToast('Donor registered successfully.', true);
    } catch (error) { showPortalToast('Could not save donor. Please try again.', false); }
}
