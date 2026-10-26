/**
 * HopeHaven Orphanage Management Portal - Staff Profile Logic
 * File: public/js/portal/staff-profile.js
 */

var profileData = {
    fullname: "Nurse Mary Jenkins",
    username: "nurse.mary",
    affiliation: "HopeHaven Sanctuary – Pediatric Ward & Child Welfare Unit",
    ward: "Ward Alpha (Infants) & Ward Beta Nursery",
    email: "mary.jenkins@hopehaven.org",
    phone: "+60 12-345 6789",
    address: "HopeHaven Sanctuary Staff Quarters, Block B Room 204, St. Jude Valley, CA 94103, United States",
    role: "Senior Caregiver & Clinical Custodian",
    shiftStatus: "On Duty"
};

var isInlineEditing = false;
var isPasswordRevealed = false;

// 1. Tab Switching Function
function switchStaffProfileTab(tabId, btnElement) {
    $('.staff-tab-btn').removeClass('active');
    $(btnElement).addClass('active');

    $('.staff-tab-pane').removeClass('active').hide();
    $('#' + tabId).fadeIn(200).addClass('active');
}

// 2. Toggle Inline Edit Mode
function toggleInlineEdit(forceState) {
    if (typeof forceState !== 'undefined') {
        isInlineEditing = forceState;
    } else {
        isInlineEditing = !isInlineEditing;
    }

    if (isInlineEditing) {
        // Automatically switch to tab 1 (identity) if not already there
        if (!$('#tab_identity').hasClass('active')) {
            switchStaffProfileTab('tab_identity', $('.staff-tab-btn[data-tab="tab_identity"]'));
        }

        $('#profile_inline_form').addClass('edit-mode-active');
        $('#inline_edit_btn_text').text('Exit Edit Mode');
        $('#btn_toggle_inline_edit').addClass('active').css({'background': '#059669', 'color': '#ffffff', 'border-color': '#059669'});
        $('#inline_input_fullname').focus();
    } else {
        $('#profile_inline_form').removeClass('edit-mode-active');
        $('#inline_edit_btn_text').text('Inline Edit Mode');
        $('#btn_toggle_inline_edit').removeClass('active').css({'background': '', 'color': '', 'border-color': ''});
        syncInputsFromData();
    }
}

// 3. Synchronize Form Inputs from profileData
function syncInputsFromData() {
    $('#inline_input_fullname').val(profileData.fullname);
    $('#inline_input_username').val(profileData.username);
    $('#inline_input_affiliation').val(profileData.affiliation);
    $('#inline_input_ward').val(profileData.ward);
    $('#inline_input_email').val(profileData.email);
    $('#inline_input_phone').val(profileData.phone);
    $('#inline_input_address').val(profileData.address);

    $('#modal_input_fullname').val(profileData.fullname);
    $('#modal_input_username').val(profileData.username);
    $('#modal_input_affiliation').val(profileData.affiliation);
    $('#modal_input_ward').val(profileData.ward);
    $('#modal_input_email').val(profileData.email);
    $('#modal_input_phone').val(profileData.phone);
    $('#modal_input_address').val(profileData.address);
}

// 4. Open Dedicated Field Modal
function openEditProfileModal(targetField) {
    syncInputsFromData();
    openModal('modal_edit_profile');
    setTimeout(function() {
        if (targetField === 'fullname') $('#modal_input_fullname').focus();
        else if (targetField === 'username') $('#modal_input_username').focus();
        else if (targetField === 'affiliation') $('#modal_input_affiliation').focus();
        else if (targetField === 'ward') $('#modal_input_ward').focus();
        else if (targetField === 'email') $('#modal_input_email').focus();
        else if (targetField === 'phone') $('#modal_input_phone').focus();
        else if (targetField === 'address') $('#modal_input_address').focus();
    }, 220);
}

// 5. Handle Save Modal Form
function handleSaveModalProfile(e) {
    e.preventDefault();

    var newFullname = $('#modal_input_fullname').val().trim();
    var newUsername = $('#modal_input_username').val().trim();
    var newAffiliation = $('#modal_input_affiliation').val().trim();
    var newWard = $('#modal_input_ward').val().trim();
    var newEmail = $('#modal_input_email').val().trim();
    var newPhone = $('#modal_input_phone').val().trim();
    var newAddress = $('#modal_input_address').val().trim();

    if (!newFullname || !newUsername || !newAffiliation || !newWard || !newEmail || !newPhone || !newAddress) {
        showPortalToast('Please complete all required fields.', false);
        return;
    }

    updateProfileDOM(newFullname, newUsername, newAffiliation, newWard, newEmail, newPhone, newAddress);
    closeModal('modal_edit_profile');
    showPortalToast('Staff profile details updated successfully!', true);
}

// 6. Handle Save Inline Form
function handleSaveInlineProfile(e) {
    e.preventDefault();

    var newFullname = $('#inline_input_fullname').val().trim();
    var newUsername = $('#inline_input_username').val().trim();
    var newAffiliation = $('#inline_input_affiliation').val().trim();
    var newWard = $('#inline_input_ward').val().trim();
    var newEmail = $('#inline_input_email').val().trim();
    var newPhone = $('#inline_input_phone').val().trim();
    var newAddress = $('#inline_input_address').val().trim();

    if (!newFullname || !newUsername || !newAffiliation || !newWard || !newEmail || !newPhone || !newAddress) {
        showPortalToast('Please complete all required fields.', false);
        return;
    }

    updateProfileDOM(newFullname, newUsername, newAffiliation, newWard, newEmail, newPhone, newAddress);
    toggleInlineEdit(false);
    showPortalToast('Staff profile details updated successfully!', true);
}

// 7. Central DOM Updater
function updateProfileDOM(fullname, username, affiliation, ward, email, phone, address) {
    profileData.fullname = fullname;
    profileData.username = username;
    profileData.affiliation = affiliation;
    profileData.ward = ward;
    profileData.email = email;
    profileData.phone = phone;
    profileData.address = address;

    var initials = fullname.split(' ').filter(Boolean).map(function(w) { return w[0]; }).join('').substring(0, 2).toUpperCase() || 'NM';

    // 1. Full Name
    $('#display_fullname').text(fullname);
    $('#hero_fullname_display').html(fullname + ' <span class="tag-badge verified-badge" title="Official Sanctuary Staff Accreditation"><i class="fas fa-check-circle"></i> Verified Staff</span>');
    $('#modal_pass_name').text(fullname);
    $('#hero_avatar_display').text(initials);
    $('.user-avatar, .topbar-avatar').text(initials);
    $('.user-meta-name, .topbar-username').text(fullname);

    // 2. Username
    $('#display_username').text(username);
    $('#hero_username_text').text(username);

    // 3. Affiliation
    $('#display_affiliation').text(affiliation);
    $('#hero_affiliation_text').text(affiliation);

    // 4. Ward
    $('#display_ward').text(ward);
    $('#kpi_ward').html('<i class="fas fa-baby"></i> ' + ward);

    // 5. Email
    $('#display_email').text(email);

    // 6. Phone
    $('#display_phone').text(phone);

    // 7. Address
    $('#display_address').text(address);

    syncInputsFromData();
}

// 8. Shift Status Switcher
function setShiftStatus(status) {
    $('.shift-status-toggle-btn').removeClass('active');
    profileData.shiftStatus = status;

    if (status === 'On Duty') {
        $('#btn_status_duty').addClass('active');
        $('#hero_duty_text').text('Morning Shift · On Duty');
        $('#hero_duty_pill').css({'background': 'rgba(16, 185, 129, 0.15)', 'color': '#047857', 'border-color': 'rgba(16, 185, 129, 0.3)'});
        $('#kpi_shift_status').html('<i class="fas fa-clock"></i> Active (07:00 – 15:30)').css('color', '#059669');
        showPortalToast('Duty status set to: Active On Duty (Ward Alpha)', true);
    } else if (status === 'On Break') {
        $('#btn_status_break').addClass('active');
        $('#hero_duty_text').text('Morning Shift · On Break');
        $('#hero_duty_pill').css({'background': 'rgba(245, 158, 11, 0.15)', 'color': '#b45309', 'border-color': 'rgba(245, 158, 11, 0.3)'});
        $('#kpi_shift_status').html('<i class="fas fa-coffee"></i> On Break (30 min)').css('color', '#d97706');
        showPortalToast('Duty status set to: On Meal/Rest Break', true);
    } else if (status === 'Handover') {
        $('#btn_status_handover').addClass('active');
        $('#hero_duty_text').text('Shift Handover in Progress');
        $('#hero_duty_pill').css({'background': 'rgba(59, 130, 246, 0.15)', 'color': '#1d4ed8', 'border-color': 'rgba(59, 130, 246, 0.3)'});
        $('#kpi_shift_status').html('<i class="fas fa-exchange-alt"></i> Handover Phase').css('color', '#2563eb');
        showPortalToast('Duty status set to: Shift Handover & Clinical Debriefing', true);
    }
}

// 9. Save Shift Handover Note
function saveShiftNote() {
    var note = $('#shift_handover_notes').val().trim();
    if (!note) {
        showPortalToast('Please enter a handover note before saving.', false);
        return;
    }
    showPortalToast('Shift Handover note logged to Ward Alpha Daily Log Book!', true);
}

// 10. Password reveal toggle
function togglePasswordVisibility() {
    isPasswordRevealed = !isPasswordRevealed;
    if (isPasswordRevealed) {
        $('#password_display_text').hide();
        $('#password_clear_text').fadeIn(150);
        $('#toggle_password_icon').removeClass('fa-eye').addClass('fa-eye-slash');
        $('#toggle_password_text').text('Hide');
    } else {
        $('#password_clear_text').hide();
        $('#password_display_text').fadeIn(150);
        $('#toggle_password_icon').removeClass('fa-eye-slash').addClass('fa-eye');
        $('#toggle_password_text').text('Show');
    }
}

// 11. Toggle input type for password modal
function toggleInputType(inputId, btn) {
    var input = $('#' + inputId);
    var icon = $(btn).find('i');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

// 12. Dynamic password strength evaluator
function evaluateNewPasswordStrength(pwd) {
    var strength = 0;
    if (pwd.length >= 8) strength += 25;
    if (pwd.match(/[A-Z]/)) strength += 25;
    if (pwd.match(/[0-9]/)) strength += 25;
    if (pwd.match(/[^A-Za-z0-9]/)) strength += 25;

    var bar = $('#strength_bar');
    var text = $('#strength_text');

    bar.css('width', Math.max(strength, 15) + '%');

    if (strength <= 25) {
        bar.css('background', '#ef4444');
        text.css('color', '#ef4444').text('Weak');
    } else if (strength <= 75) {
        bar.css('background', '#f59e0b');
        text.css('color', '#f59e0b').text('Moderate');
    } else {
        bar.css('background', '#10b981');
        text.css('color', '#10b981').text('Strong (Secure)');
    }
}

// 13. Handle Change Password
function handleChangePassword(e) {
    e.preventDefault();

    var current = $('#current_password').val();
    var newPass = $('#new_password').val();
    var confirmPass = $('#confirm_new_password').val();

    if (newPass !== confirmPass) {
        showPortalToast('New passwords do not match. Please re-enter.', false);
        $('#confirm_new_password').focus();
        return;
    }

    if (newPass.length < 8) {
        showPortalToast('Password must contain at least 8 characters.', false);
        return;
    }

    var btn = $('#btn_save_password');
    var origHtml = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Encrypting & Updating...');

    setTimeout(function() {
        btn.prop('disabled', false).html(origHtml);
        $('#password_clear_text').text(newPass);
        closeModal('modal_change_password');
        $('#change_password_form')[0].reset();
        evaluateNewPasswordStrength('');
        showPortalToast('Staff account password updated and encrypted successfully!', true);
    }, 800);
}

// 14. Copy to Clipboard Helper
function copyText(text, successMsg) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showPortalToast(successMsg, true);
        });
    } else {
        var temp = $('<input>');
        $('body').append(temp);
        temp.val(text).select();
        document.execCommand('copy');
        temp.remove();
        showPortalToast(successMsg, true);
    }
}

$(document).ready(function() {
    syncInputsFromData();
});
