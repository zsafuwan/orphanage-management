/**
 * Director Donations & Grants Governance JavaScript
 */

let activeGrantor = null;

function openGrantReleaseModal(grantor, total, tranche) {
    activeGrantor = grantor;
    document.getElementById('grant_release_grantor').innerText = grantor;
    document.getElementById('grant_release_total').innerText = total;
    document.getElementById('grant_release_tranche').innerText = tranche;

    const modal = document.getElementById('modal_grant_release');
    if (modal) modal.classList.add('active');
}

function closeDirectorModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}

function confirmGrantRelease() {
    closeDirectorModal('modal_grant_release');
    if (typeof showPortalToast === 'function') {
        showPortalToast(`Director payment approved for ${activeGrantor}. Funds released to orphanage ledger.`, 'success');
    } else {
        alert(`Payment confirmed for ${activeGrantor}.`);
    }
}
