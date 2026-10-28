/**
 * Director Staff & Volunteers JavaScript
 */

let activeStaffName = null;

function openStaffDossier(name, role, avatar) {
    activeStaffName = name;
    document.getElementById('dossier_staff_name').innerText = name;
    document.getElementById('dossier_staff_role').innerText = role;
    document.getElementById('dossier_staff_avatar').innerText = avatar;

    const modal = document.getElementById('modal_staff_safeguarding_dossier');
    if (modal) modal.classList.add('active');
}

function closeDirectorModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}

function endorseCaregiverCredentials() {
    closeDirectorModal('modal_staff_safeguarding_dossier');
    if (typeof showPortalToast === 'function') {
        showPortalToast(`Official Director Endorsement renewed for ${activeStaffName}.`, 'success');
    } else {
        alert(`Endorsement recorded for ${activeStaffName}.`);
    }
}

function approveVolunteerHours(name, hours, btn) {
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    setTimeout(() => {
        btn.parentElement.innerHTML = '<span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Hours Approved</span>';
        if (typeof showPortalToast === 'function') {
            showPortalToast(`Verified and logged ${hours} volunteer community service hours for ${name}.`, 'success');
        }
    }, 500);
}
