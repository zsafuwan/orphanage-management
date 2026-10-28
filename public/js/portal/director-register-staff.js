/**
 * Director Staff Registration JavaScript
 * Handles live preview, input synchronization, validation, and credential issuing
 */

let lastRegisteredStaff = null;

document.addEventListener('DOMContentLoaded', function () {
    initLivePreview();
});

/**
 * Synchronize live badge preview with form fields
 */
function initLivePreview() {
    const nameInput = document.getElementById('staff_name_input');
    const roleSelect = document.getElementById('staff_role_select');
    const wingSelect = document.getElementById('staff_wing_select');

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            const val = this.value.trim() || 'New Caregiver';
            document.getElementById('preview_name').innerText = val;

            // Generate initials
            const words = val.split(/\s+/);
            let initials = '';
            if (words.length >= 2) {
                initials = (words[0][0] + words[1][0]).toUpperCase();
            } else if (words.length === 1 && words[0].length > 0) {
                initials = words[0].substring(0, 2).toUpperCase();
            } else {
                initials = 'NC';
            }
            document.getElementById('preview_avatar').innerText = initials;
        });
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            const role = this.options[this.selectedIndex].text;
            document.getElementById('preview_role').innerText = role;
        });
    }

    if (wingSelect) {
        wingSelect.addEventListener('change', function () {
            const wing = this.options[this.selectedIndex].text;
            document.getElementById('preview_wing').innerText = wing;
        });
    }
}

/**
 * Handle form submission
 */
function submitStaffRegistration(e) {
    e.preventDefault();

    const name = document.getElementById('staff_name_input').value.trim();
    const nric = document.getElementById('staff_nric_input').value.trim();
    const email = document.getElementById('staff_email_input').value.trim();
    const phone = document.getElementById('staff_phone_input').value.trim();
    const role = document.getElementById('staff_role_select').options[document.getElementById('staff_role_select').selectedIndex].text;
    const wing = document.getElementById('staff_wing_select').options[document.getElementById('staff_wing_select').selectedIndex].text;
    const staffId = document.getElementById('staff_id_input').value.trim() || 'HH-CARE-09';
    const password = document.getElementById('staff_password_input').value.trim() || 'Caregiver@2026';
    const authCheck = document.getElementById('director_authorizer_check');

    if (!name || !nric || !email || !phone) {
        alert('Please complete all mandatory staff contact and identity fields.');
        return;
    }

    if (authCheck && !authCheck.checked) {
        alert('Director signature approval declaration must be checked.');
        return;
    }

    // Prepare success modal
    document.getElementById('success_staff_name').innerText = name;
    document.getElementById('success_staff_role').innerText = `${role} · ${wing}`;
    document.getElementById('success_staff_id').innerText = staffId;
    document.getElementById('success_staff_wing').innerText = wing;
    document.getElementById('success_staff_password').innerText = password;

    lastRegisteredStaff = {
        name: name,
        id: staffId,
        role: role,
        wing: wing,
        password: password
    };

    // Open Success Modal
    const modal = document.getElementById('modal_staff_onboarding_success');
    if (modal) modal.classList.add('active');

    // Add to recent list
    appendRecentStaff(name, role, staffId, wing);

    if (typeof showPortalToast === 'function') {
        showPortalToast(`Staff Caregiver ${name} (${staffId}) officially registered & accredited!`, 'success');
    }
}

/**
 * Append newly registered staff to right column
 */
function appendRecentStaff(name, role, id, wing) {
    const list = document.getElementById('recent_staff_list');
    if (!list) return;

    const words = name.trim().split(/\s+/);
    const initials = words.length >= 2 ? (words[0][0] + words[1][0]).toUpperCase() : name.substring(0, 2).toUpperCase();

    const div = document.createElement('div');
    div.className = 'recent-staff-item';
    div.style.background = '#f0fdf4';
    div.innerHTML = `
        <div class="user-cell">
            <div class="user-cell-avatar" style="background:#10b981; color:#ffffff;">${initials}</div>
            <div>
                <div class="user-cell-name">${window.HopeHavenSync.escape(name)}</div>
                <div class="user-cell-dept">${window.HopeHavenSync.escape(role)} · ${window.HopeHavenSync.escape(id)}</div>
            </div>
        </div>
        <span class="vetted-badge" style="font-size: 11px;"><i class="fas fa-check-circle"></i> Just Onboarded</span>
    `;

    list.insertBefore(div, list.firstChild);
}

/**
 * Copy staff credentials to clipboard
 */
function copyStaffCredentials() {
    if (!lastRegisteredStaff) return;
    const text = `HopeHaven Orphanage Staff Credentials:\nName: ${lastRegisteredStaff.name}\nStaff ID: ${lastRegisteredStaff.id}\nWing: ${lastRegisteredStaff.wing}\nRole: ${lastRegisteredStaff.role}\nTemporary Password: ${lastRegisteredStaff.password}\nPortal URL: ${window.location.origin}/staff/child-welfare`;

    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            if (typeof showPortalToast === 'function') {
                showPortalToast('Staff login credentials copied to clipboard!', 'success');
            } else {
                alert('Credentials copied to clipboard!');
            }
        });
    } else {
        alert(text);
    }
}

/**
 * Print Staff Credential Slip
 */
function printStaffCredentialSlip() {
    if (typeof showPortalToast === 'function') {
        showPortalToast('Printing official Director Staff Accreditation Slip (PDF)...', 'info');
    } else {
        alert('Printing Staff Credential Slip...');
    }
}

/**
 * Close any Director Modal
 */
function closeDirectorModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}
