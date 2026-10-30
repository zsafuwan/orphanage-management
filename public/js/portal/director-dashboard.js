/**
 * Director Dashboard JavaScript Logic
 * Handles Monthly Spending Graph (Chart.js), Treasury metrics,
 * Donator proof of donation modals, and monthly student sickness/appointments tracking.
 */

let spendingChartInstance = null;
let currentChartMode = 'trend'; // 'trend' or 'category'
let activeProofDonor = null;

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Dashboard UI interactions
    initDashboardInteractions();
    initSpendingChart();
    initSicknessFilterTabs();
});

function initDashboardInteractions() {
    // Quick approve buttons on dashboard
    document.querySelectorAll('.btn-director-quick-approve').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const reqId = this.getAttribute('data-req-id');
            const reqItem = this.getAttribute('data-req-item');
            quickApproveRequisition(reqId, reqItem, this);
        });
    });
}

/**
 * Initialize Chart.js Monthly Spending Visualization
 */
function initSpendingChart() {
    const ctx = document.getElementById('directorSpendingChart');
    if (!ctx) return;

    // Monthly Trend Data (Past 6 Months)
    const monthlyLabels = ['May 2026', 'Jun 2026', 'Jul 2026', 'Aug 2026', 'Sep 2026', 'Oct 2026'];
    const monthlySpendData = [21400, 23800, 22100, 25900, 23150, 24680];
    const monthlyBudgetData = [28000, 28000, 28000, 30000, 30000, 30000];

    // Destroy existing instance if any
    if (spendingChartInstance) {
        spendingChartInstance.destroy();
    }

    if (currentChartMode === 'trend') {
        spendingChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        label: 'Monthly Actual Spending (RM)',
                        data: monthlySpendData,
                        backgroundColor: 'rgba(79, 70, 229, 0.85)',
                        borderColor: '#4f46e5',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barThickness: 26
                    },
                    {
                        label: 'Approved Orphanage Budget (RM)',
                        data: monthlyBudgetData,
                        type: 'line',
                        borderColor: '#f59e0b',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 4,
                        fill: false,
                        tension: 0.2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Poppins', size: 12, weight: '600' },
                            color: '#475569',
                            boxWidth: 14
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Poppins', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                return ` ${context.dataset.label}: RM ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Poppins', size: 11, weight: '500' },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#64748b',
                            callback: function (val) {
                                return 'RM ' + (val / 1000) + 'k';
                            }
                        }
                    }
                }
            }
        });
    } else {
        // Current Month (October 2026) Category Breakdown
        spendingChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Food & Kitchen Nutrition',
                    'Education & Workbooks',
                    'Medical & Clinical Care',
                    'Facilities & Utilities',
                    'Staff Caregivers & Transport'
                ],
                datasets: [
                    {
                        data: [9800, 5200, 3420, 3860, 2400],
                        backgroundColor: [
                            '#4f46e5', // Indigo
                            '#8b5cf6', // Purple
                            '#059669', // Emerald
                            '#f59e0b', // Amber
                            '#06b6d4'  // Cyan
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: 'Poppins', size: 11, weight: '600' },
                            color: '#334155',
                            padding: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Poppins', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return ` RM ${context.raw.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
}

/**
 * Switch Chart Display Mode (Monthly Trend vs Category Breakdown)
 */
function toggleSpendingChart(mode) {
    currentChartMode = mode;

    document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    const activeBtn = document.getElementById(`btn_chart_${mode}`);
    if (activeBtn) activeBtn.classList.add('active');

    initSpendingChart();
}

/**
 * Filter Student Sickness & Appointment Table
 */
function initSicknessFilterTabs() {
    const tabs = document.querySelectorAll('.sickness-tab-btn');
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-sickness-filter');
            filterSicknessRows(filter);
        });
    });
}

function filterSicknessRows(filter) {
    const rows = document.querySelectorAll('.sickness-table tbody tr');
    rows.forEach(row => {
        const rowType = row.getAttribute('data-record-type'); // 'sickness', 'scheduled', 'attended'
        if (filter === 'all') {
            row.style.display = '';
        } else if (filter === 'sickness' && row.getAttribute('data-has-sickness') === 'true') {
            row.style.display = '';
        } else if (filter === 'scheduled' && rowType === 'scheduled') {
            row.style.display = '';
        } else if (filter === 'attended' && rowType === 'attended') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Open Donator Proof of Payment Modal
 */
function openDonationProofModal(donorName, amount, bank, txRef, date, purpose, status) {
    activeProofDonor = donorName;

    document.getElementById('proof_donor_name').innerText = donorName;
    document.getElementById('proof_amount').innerText = amount;
    document.getElementById('proof_bank_name').innerText = bank;
    document.getElementById('proof_tx_ref').innerText = txRef;
    document.getElementById('proof_timestamp').innerText = date;
    document.getElementById('proof_purpose').innerText = `Designated: ${purpose}`;

    const stampBadge = document.getElementById('proof_stamp_badge');
    const statusText = document.getElementById('proof_status_text');
    const actionBtn = document.getElementById('btn_modal_verify_action');

    if (status === 'Verified') {
        stampBadge.innerHTML = '<i class="fas fa-shield-check"></i> Verified Digital Bank Slip';
        stampBadge.style.background = '#ecfdf5';
        stampBadge.style.color = '#047857';
        statusText.innerHTML = '<span style="color: #059669; font-weight: 800;"><i class="fas fa-check-circle"></i> Authenticated by Director</span>';
        if (actionBtn) actionBtn.innerText = 'Resend Confirmation';
    } else {
        stampBadge.innerHTML = '<i class="fas fa-clock"></i> Pending Verification Slip';
        stampBadge.style.background = '#fef3c7';
        stampBadge.style.color = '#b45309';
        statusText.innerHTML = '<span style="color: #d97706; font-weight: 800;"><i class="fas fa-hourglass-half"></i> Pending Sister Sarah Review</span>';
        if (actionBtn) actionBtn.innerText = 'Approve & Verify Donation';
    }

    const modal = document.getElementById('modal_donation_proof');
    if (modal) modal.classList.add('active');
}

/**
 * Quick Verify donation from table row
 */
function verifyDonation(donorName, amount, buttonEl) {
    buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    buttonEl.disabled = true;

    setTimeout(() => {
        const row = buttonEl.closest('tr');
        if (row) {
            const statusCell = row.querySelector('.donor-status-cell');
            if (statusCell) {
                statusCell.innerHTML = '<span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Verified</span>';
            }
            buttonEl.parentElement.innerHTML = '<span style="font-size:11px; color:#10b981; font-weight:700;"><i class="fas fa-check"></i> Verified</span>';
        }

        if (typeof showPortalToast === 'function') {
            showPortalToast(`Donation of ${amount} from ${donorName} authenticated and verified!`, 'success');
        } else {
            alert(`Donation of ${amount} from ${donorName} verified.`);
        }
    }, 500);
}

/**
 * Close any Director Modal
 */
function closeDirectorModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}

/**
 * Download Official Tax Receipt
 */
function downloadOfficialTaxReceipt() {
    if (typeof showPortalToast === 'function') {
        showPortalToast(`Generating LHDN Tax Exemption Receipt for ${activeProofDonor}...`, 'info');
    } else {
        alert(`Generating Tax Exemption Receipt for ${activeProofDonor}...`);
    }
}

/**
 * Acknowledge donation letter
 */
function acknowledgeDonationLetter() {
    closeDirectorModal('modal_donation_proof');
    if (typeof showPortalToast === 'function') {
        showPortalToast(`Official Orphanage Acknowledgment letter dispatched to ${activeProofDonor}.`, 'success');
    } else {
        alert(`Acknowledgment dispatched to ${activeProofDonor}.`);
    }
}

/**
 * Handle quick requisition approval from dashboard
 */
function quickApproveRequisition(reqId, itemName, buttonEl) {
    if (!confirm(`Confirm Director Approval for Request #${reqId} (${itemName})?\nFunds will be released from the Q4 Caregiver Supply Reserve.`)) {
        return;
    }

    // Visual loading state
    buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
    buttonEl.disabled = true;

    setTimeout(() => {
        const row = buttonEl.closest('.req-item-card');
        if (row) {
            row.style.background = '#f0fdf4';
            row.style.transition = 'all 0.5s ease';
            
            const actionsDiv = row.querySelector('.req-actions');
            if (actionsDiv) {
                actionsDiv.innerHTML = '<span class="badge" style="background:#ecfdf5; color:#047857; font-weight:700; padding:6px 12px; border-radius:6px;"><i class="fas fa-check-circle"></i> Approved by Sister Sarah</span>';
            }
        }

        const countBadge = document.getElementById('director_pending_req_count');
        if (countBadge) {
            let current = parseInt(countBadge.innerText, 10);
            if (!isNaN(current) && current > 0) {
                countBadge.innerText = (current - 1).toString();
            }
        }

        if (typeof showPortalToast === 'function') {
            showPortalToast(`Request #${reqId} (${itemName}) successfully authorized and ledger updated.`, 'success');
        } else {
            alert(`Request #${reqId} successfully approved.`);
        }
    }, 600);
}

/**
 * Trigger export of quarterly directorate report
 */
function exportDirectorateReport() {
    if (typeof showPortalToast === 'function') {
        showPortalToast('Generating Q4 2026 Executive Orphanage Management Audit (PDF)... Ready in seconds.', 'info');
    } else {
        alert('Generating Q4 2026 Executive Director Report...');
    }
}

/**
 * Trigger emergency notice broadcast modal
 */
function directEmergencyNotice() {
    const notice = prompt('Broadcast Directive to All Caregivers & Staff on Duty:\n(This will pin an executive priority alert across all shift terminals)', 'Urgent Weather Advisory: Ensure Ward Alpha & Beta storm shutters are latched.');
    if (notice && notice.trim() !== '') {
        if (typeof showPortalToast === 'function') {
            showPortalToast(`Director Directive broadcasted to 8 active staff on duty: "${notice}"`, 'success');
        } else {
            alert('Notice broadcasted to all active staff.');
        }
    }
}
