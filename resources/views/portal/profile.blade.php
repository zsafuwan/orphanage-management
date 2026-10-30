@extends('layouts.portal')

@section('title', 'My Profile & Account Details')
@section('breadcrumb', 'Staff & Volunteers Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/profile.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/profile.css')), 0, 12) }}">
@endpush

@section('content')
<!-- Subsystem Header -->
<div class="page-header-card">
    <div>
        <div class="subsystem-pill" style="background: #8b5cf6;">
            <i class="fas fa-id-card"></i> Staff & Volunteers Identity & Credentials
        </div>
        <h1 class="page-title">My Profile & Login Details</h1>
        <p class="page-desc">
            View and update your personal details including full name, username, affiliation, email, phone number, organization address, and password.
        </p>
    </div>
    <div class="header-action-group">
        <a href="{{ route('staff.profile') }}" class="btn-outline" style="border-color: #10b981; color: #059669; font-weight: 600;" title="Switch to Staff Caregiver Profile">
            <i class="fas fa-user-nurse"></i> Staff Profile View
        </a>
        <button class="btn-outline" id="btn_toggle_inline_edit" onclick="toggleInlineEdit()">
            <i class="fas fa-edit"></i> <span id="inline_edit_btn_text">Inline Edit Mode</span>
        </button>
        <button class="btn-outline" onclick="openModal('modal_change_password')">
            <i class="fas fa-key"></i> Change Password
        </button>
    </div>
</div>

<!-- Profile Hero Identity Card -->
<div class="profile-hero-card">
    <div class="profile-hero-content">
        <div class="profile-hero-left">
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar-circle" id="hero_avatar_display">
                    SG
                </div>
                <div class="avatar-status-dot" title="Active on-duty staff"></div>
            </div>

            <div class="profile-hero-details">
                <h1 id="hero_fullname_display">
                    Sister Grace Montgomery
                    <span class="tag-badge verified-badge" title="Verified Orphanage Staff">
                        <i class="fas fa-check-circle"></i> Verified
                    </span>
                </h1>

                <div class="profile-tag-group">
                    <span class="tag-badge role-badge" id="hero_role_badge">
                        <i class="fas fa-shield-alt"></i>Administrator
                    </span>
                    <span class="tag-badge username-badge" id="hero_username_badge" onclick="copyText($('#display_username').text(), 'Username copied to clipboard!')" style="cursor: pointer;" title="Click to copy username">
                        <i class="fas fa-at"></i> <span id="hero_username_text">sister.grace</span> <i class="fas fa-copy" style="font-size: 10px; margin-left: 2px;"></i>
                    </span>
                </div>

                <div class="profile-affiliation-sub" id="hero_affiliation_display">
                    <i class="fas fa-building" style="color: var(--primary);"></i>
                    <span id="hero_affiliation_text">HopeHaven Orphanage – Child Welfare & Social Services Foundation</span>
                </div>
            </div>
        </div>

        <div class="profile-hero-right">
            <button class="btn-outline" onclick="copyText($('#display_email').text(), 'Official Email copied!')">
                <i class="fas fa-envelope"></i> Copy Email
            </button>
            <button class="btn-outline" onclick="openModal('modal_qr_id')">
                <i class="fas fa-qrcode"></i> Digital Staff ID
            </button>
        </div>
    </div>

    <!-- Quick Personnel KPIs -->
    <div class="hero-stats-row">
        <div class="hero-stat-item">
            <span class="hero-stat-label">System Role</span>
            <span class="hero-stat-val" id="kpi_role">
                <i class="fas fa-user-shield" style="color: #8b5cf6;"></i> Administrator
            </span>
        </div>
        <div class="hero-stat-item">
            <span class="hero-stat-label">Access Level</span>
            <span class="hero-stat-val" style="color: #10b981;">
                <i class="fas fa-certificate"></i> Tier 1 (Master)
            </span>
        </div>
        <div class="hero-stat-item">
            <span class="hero-stat-label">Safeguarding Badge</span>
            <span class="hero-stat-val" style="color: var(--teal);">
                <i class="fas fa-id-badge"></i> Certified (Active)
            </span>
        </div>

    </div>
</div>

<!-- Unified Inline Edit Form Wrapper -->
<form id="profile_inline_form" onsubmit="handleSaveInlineProfile(event)">
    <!-- Main Profile Layout Grid -->
    <div class="profile-grid-layout">
        <!-- Left Column: Detailed Identity, Contact & Password Information -->
        <div>
            <!-- Card 1: Core Personal & Organizational Identity -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-user-circle"></i> Core Identity & Organizational Affiliation
                    </div>
                </div>

                <div class="profile-card-body">
                    <div class="detail-fields-grid">
                        <!-- 1. FULL NAME (Editable) -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_fullname">
                                <i class="fas fa-signature"></i> Full Name
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_fullname">Sister Grace Montgomery</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('fullname')" title="Edit Full Name">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_fullname').text(), 'Full Name copied!')" title="Copy Full Name">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="text" id="inline_input_fullname" class="edit-mode-item editable-input" style="display: none;" value="Sister Grace Montgomery" required>
                        </div>

                        <!-- 2. USERNAME (Editable) -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_username">
                                <i class="fas fa-at"></i> Username
                            </label>
                            <div class="view-mode-item field-value-box mono">
                                <span class="field-value-text" id="display_username">sister.grace</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('username')" title="Edit Username">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_username').text(), 'Username copied!')" title="Copy Username">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="edit-mode-item input-prefix-group" style="display: none;">
                                <span class="input-prefix-addon">@</span>
                                <input type="text" id="inline_input_username" class="editable-input" value="sister.grace" required>
                            </div>
                        </div>

                        <!-- 3. ROLE (Appointed / Non-editable here) -->
                        <div class="detail-field-item">
                            <label class="field-label">
                                <i class="fas fa-user-tag"></i> Role
                            </label>
                            <div class="field-value-box">
                                <span class="field-value-text" id="display_role">Administrator</span>
                                <span class="tag-badge role-badge" style="font-size: 10px; padding: 2px 7px;">RBAC Tier 1</span>
                            </div>
                        </div>

                        <!-- 4. PERSONNEL CODE (Employee ID) -->
                        <div class="detail-field-item">
                            <label class="field-label">
                                <i class="fas fa-barcode"></i> Staff ID
                            </label>
                            <div class="field-value-box mono">
                                <span class="field-value-text">HH-ADM-2021-042</span>
                                <span class="tag-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 10px;">Valid</span>
                            </div>
                        </div>

                        <!-- 5. AFFILIATION (Editable) -->
                        <div class="detail-field-item full-width">
                            <label class="field-label" for="inline_input_affiliation">
                                <i class="fas fa-hospital-alt"></i> Organization
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_affiliation">HopeHaven Orphanage – Child Welfare & Social Services Foundation</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('affiliation')" title="Edit Affiliation">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_affiliation').text(), 'Affiliation copied!')" title="Copy Affiliation">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="text" id="inline_input_affiliation" class="edit-mode-item editable-input" style="display: none;" value="HopeHaven Orphanage – Child Welfare & Social Services Foundation" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Contact Information & Residential Address -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-map-marker-alt"></i> Contact Communications & Physical Address
                    </div>
                </div>

                <div class="profile-card-body">
                    <div class="detail-fields-grid">
                        <!-- 6. EMAIL ADDRESS (Editable) -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_email">
                                <i class="fas fa-envelope"></i>Email 
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_email">grace.montgomery@hopehaven.org</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('email')" title="Edit Email">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_email').text(), 'Email copied!')" title="Copy Email">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="email" id="inline_input_email" class="edit-mode-item editable-input" style="display: none;" value="grace.montgomery@hopehaven.org" required>
                        </div>

                        <!-- 7. NO. PHONE (Editable) -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_phone">
                                <i class="fas fa-phone"></i> No. Phone (Telephone)
                            </label>
                            <div class="view-mode-item field-value-box mono">
                                <span class="field-value-text" id="display_phone">+60 11-1111111</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('phone')" title="Edit Phone Number">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_phone').text(), 'Phone copied!')" title="Copy Phone">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="tel" id="inline_input_phone" class="edit-mode-item editable-input" style="display: none;" value="+1 (555) 234-8890" required>
                        </div>

                        <!-- 8. PHYSICAL ADDRESS (Editable) -->
                        <div class="detail-field-item full-width">
                            <label class="field-label" for="inline_input_address">
                                <i class="fas fa-home"></i> Organization Address
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_address">Plot 42 Hope Haven Orphanage Blvd, Staff Residence Suite 3B, St. Jude Valley, CA 94103, United States</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('address')" title="Edit Address">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_address').text(), 'Address copied!')" title="Copy Address">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <textarea id="inline_input_address" class="edit-mode-item editable-input" rows="2" style="display: none;" required>Plot 42 Hope Haven Orphanage Blvd, Staff Residence Suite 3B, St. Jude Valley, CA 94103, United States</textarea>
                        </div>
                    </div>

                    <!-- Inline Actions Bar (Save / Cancel) -->
                    <div class="edit-actions-panel" id="inline_actions_panel">
                        <button type="button" class="btn-outline" onclick="toggleInlineEdit(false)">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                    </div>

                    <!-- Address GPS / Location Card Preview -->
                    <div class="address-map-card">
                        <div class="address-map-icon">
                            <i class="fas fa-compass"></i>
                        </div>
                        <div class="address-map-text">
                            <h4>HopeHaven Central Complex – West Orphanage Quarters</h4>
                            <p>Geographic Zone: District 4-A • Coordinates: 37.7749° N, 122.4194° W • On-site Emergency Response Team Hub</p>
                        </div>
                        <button type="button" class="btn-pill-small" onclick="showPortalToast('Orphanage map location copied to clipboard!', true)">
                            <i class="fas fa-location-arrow"></i> Locate
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 3: Security & Password Management -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-lock"></i> Authentication & Password Credentials
                    </div>
                    <span class="tag-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-shield-alt"></i> 256-bit Secured
                    </span>
                </div>

                <div class="profile-card-body">
                    <!-- PASSWORD SPECIAL DISPLAY CONTAINER -->
                    <label class="field-label">
                        <i class="fas fa-key"></i> Current Account Password
                    </label>

                    <div class="password-display-wrapper">
                        <div class="password-row-main">
                            <div>
                                <span class="password-hidden-dots" id="password_display_text">••••••••••••••••</span>
                                <span id="password_clear_text" style="display: none; font-family: 'JetBrains Mono', monospace; font-size: 14px; font-weight: 700; color: var(--primary);">HopeHaven#Admin2026!</span>
                            </div>

                            <div class="password-actions-group">
                                <button type="button" class="btn-pill-small" id="btn_toggle_password_mask" onclick="togglePasswordVisibility()">
                                    <i class="fas fa-eye" id="toggle_password_icon"></i> <span id="toggle_password_text">Show</span>
                                </button>
                                <button type="button" class="btn-pill-small btn-primary-pill" onclick="openModal('modal_change_password')">
                                    <i class="fas fa-edit"></i> Change Password
                                </button>
                            </div>
                        </div>

                        <div class="password-meta-info">
                            <div class="strength-indicator">
                                <i class="fas fa-shield-virus"></i> Security Rating: Strong
                                <div class="strength-dots-bar">
                                    <div class="strength-dot"></div>
                                    <div class="strength-dot"></div>
                                    <div class="strength-dot"></div>
                                    <div class="strength-dot"></div>
                                </div>
                            </div>
                            <div style="font-size: 11.5px; color: #94a3b8;">
                                <i class="fas fa-history"></i> Last rotated 24 days ago (Auto-expiry: 90 days)
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Security Protections -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px;">
                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 12px 14px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 12.5px; font-weight: 700; color: var(--dark);">Two-Factor Authentication</div>
                                    <div style="font-size: 11px; color: var(--dark-muted);">Hardware YubiKey + OTP Authenticator</div>
                                </div>
                                <span class="tag-badge verified-badge" style="font-size: 10px;">Active</span>
                            </div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 12px 14px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 12.5px; font-weight: 700; color: var(--dark);">Session Approval</div>
                                    <div style="font-size: 11px; color: var(--dark-muted);">Auto-lock after 15 min idle</div>
                                </div>
                                <span class="tag-badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 10px;">Enforced</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Card 5: Recent Audit & Activity Timeline -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-history"></i> Recent Security & Account Audits
                    </div>
                    <button type="button" class="btn-pill-small" onclick="showPortalToast('Security Audit Log refreshed.', true)">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>

                <div class="profile-card-body">
                    <div class="activity-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-title">Authenticated from Orphanage Management Terminal</div>
                            <div class="timeline-time">Today, 20:01 • IP: 192.168.1.14 (Orphanage Admin Desk)</div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color: #10b981;"></div>
                            <div class="timeline-title">Child Safeguarding Level 1 Certificate Re-Verified</div>
                            <div class="timeline-time">Yesterday, 14:30 • Verified by Child Welfare Board</div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color: #8b5cf6;"></div>
                            <div class="timeline-title">Account Security Key Rotation Completed</div>
                            <div class="timeline-time">24 days ago • Hardware YubiKey paired successfully</div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color: #f59e0b;"></div>
                            <div class="timeline-title">Profile Residential Address Updated</div>
                            <div class="timeline-time">October 2026 • Suite 3B Quarters confirmed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- ========================================================================= -->
<!-- MODAL 1: EDIT PROFILE DETAILS (Dedicated Edit Modal) -->
<!-- ========================================================================= -->
<div id="modal_edit_profile" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 580px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Edit Profile Information</h3>
                <p class="modal-sub">Update your staff & Volunteers identification, organizational affiliation and contact coordinates.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_edit_profile')">&times;</button>
        </div>

        <form id="modal_edit_profile_form" onsubmit="handleSaveModalProfile(event)">
            <div class="form-row">
                <!-- Full Name -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_fullname">
                        <i class="fas fa-signature"></i> Full Name *
                    </label>
                    <input type="text" id="modal_input_fullname" class="form-control" placeholder="e.g. Sister Grace Montgomery" required>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_username">
                        <i class="fas fa-at"></i> Username *
                    </label>
                    <div class="input-prefix-group">
                        <span class="input-prefix-addon">@</span>
                        <input type="text" id="modal_input_username" class="form-control" placeholder="sister.grace" required>
                    </div>
                </div>
            </div>

            <!-- Affiliation -->
            <div class="form-group">
                <label class="form-label" for="modal_input_affiliation">
                    <i class="fas fa-hospital-alt"></i> Organizational Affiliation *
                </label>
                <input type="text" id="modal_input_affiliation" class="form-control" placeholder="HopeHaven Orphanage – Child Welfare Foundation" required>
            </div>

            <div class="form-row">
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_email">
                        <i class="fas fa-envelope"></i> Official Email *
                    </label>
                    <input type="email" id="modal_input_email" class="form-control" placeholder="name@hopehaven.org" required>
                </div>

                <!-- No. Phone -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_phone">
                        <i class="fas fa-phone"></i> No. Phone (Telephone) *
                    </label>
                    <input type="tel" id="modal_input_phone" class="form-control" placeholder="+1 (555) 234-8890" required>
                </div>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label class="form-label" for="modal_input_address">
                    <i class="fas fa-home"></i> Orphanage Residential Address *
                </label>
                <textarea id="modal_input_address" class="form-control" rows="3" placeholder="Plot 42 Hope Haven Orphanage Blvd, Staff Residence Suite 3B..." required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_edit_profile')">Cancel</button>
                <button type="submit" class="btn-primary" id="btn_modal_save_profile">
                    <i class="fas fa-check-circle"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: CHANGE PASSWORD -->
<!-- ========================================================================= -->
<div id="modal_change_password" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Update Security Password</h3>
                <p class="modal-sub">Ensure your account remains safeguarded with a strong password.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_change_password')">&times;</button>
        </div>

        <form id="change_password_form" onsubmit="handleChangePassword(event)">
            <div class="form-group">
                <label class="form-label" for="current_password">Current Password *</label>
                <div style="position: relative;">
                    <input type="password" id="current_password" class="form-control" placeholder="Enter existing password" required>
                    <button type="button" class="field-action-btn" onclick="toggleInputType('current_password', this)" style="position: absolute; right: 12px; top: 10px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">New Password *</label>
                <div style="position: relative;">
                    <input type="password" id="new_password" class="form-control" placeholder="Minimum 8 characters" minlength="8" required onkeyup="evaluateNewPasswordStrength(this.value)">
                    <button type="button" class="field-action-btn" onclick="toggleInputType('new_password', this)" style="position: absolute; right: 12px; top: 10px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <!-- Dynamic Strength Meter -->
                <div style="margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; color: var(--dark-muted);">
                        <span>Password Strength:</span>
                        <strong id="strength_text" style="color: #ef4444;">Weak</strong>
                    </div>
                    <div style="height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden;">
                        <div id="strength_bar" style="height: 100%; width: 20%; background: #ef4444; transition: all 0.3s ease;"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_new_password">Confirm New Password *</label>
                <div style="position: relative;">
                    <input type="password" id="confirm_new_password" class="form-control" placeholder="Re-type new password" minlength="8" required>
                    <button type="button" class="field-action-btn" onclick="toggleInputType('confirm_new_password', this)" style="position: absolute; right: 12px; top: 10px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 12px; margin-bottom: 20px;">
                <div style="font-size: 11.5px; font-weight: 700; color: var(--dark); margin-bottom: 4px;">Security Policy Requirements:</div>
                <ul style="font-size: 11px; color: var(--dark-muted); padding-left: 18px; margin: 0; line-height: 1.6;">
                    <li>Minimum 8 alphanumeric characters</li>
                    <li>At least 1 special symbol (e.g. #, $, @, !)</li>
                    <li>Cannot match past 3 previous passwords</li>
                </ul>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_change_password')">Cancel</button>
                <button type="submit" class="btn-primary" id="btn_save_password">
                    <i class="fas fa-check-circle"></i> Save New Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: DIGITAL STAFF ID -->
<!-- ========================================================================= -->
<div id="modal_qr_id" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 400px; text-align: center;">
        <div class="modal-header" style="justify-content: center; position: relative;">
            <h3 class="modal-title">Official Orphanage Digital Pass</h3>
            <button class="modal-close-btn" onclick="closeModal('modal_qr_id')" style="position: absolute; right: 0; top: 0;">&times;</button>
        </div>

        <div style="padding: 20px 0;">
            <div style="width: 140px; height: 140px; margin: 0 auto 16px auto; background: #ffffff; border: 2px solid var(--border-color); border-radius: 12px; padding: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
                <i class="fas fa-qrcode" style="font-size: 110px; color: var(--dark);"></i>
            </div>
            <h4 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;" id="modal_pass_name">Sister Grace Montgomery</h4>
            <div style="font-size: 12px; font-weight: 700; color: #8b5cf6; margin-bottom: 8px;" id="modal_pass_role">Orphanage Admin</div>
            <div style="font-size: 11.5px; font-family: 'JetBrains Mono', monospace; color: var(--dark-muted);">ID: HH-ADM-2021-042 • Access Level 1</div>
        </div>

        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn-primary" onclick="closeModal('modal_qr_id'); showPortalToast('Orphanage Pass saved to Apple Wallet / Google Pay format!', true);">
                <i class="fas fa-download"></i> Save to Wallet
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Master data object for the 6 editable attributes
    var profileData = {
        fullname: "Sister Grace Montgomery",
        username: "sister.grace",
        affiliation: "HopeHaven Orphanage – Child Welfare & Social Services Foundation",
        email: "grace.montgomery@hopehaven.org",
        phone: "+1 (555) 234-8890",
        address: "Plot 42 Hope Haven Orphanage Blvd, Staff Residence Suite 3B, St. Jude Valley, CA 94103, United States"
    };

    // 1. Open the dedicated edit profile modal and populate with latest values
    function openEditProfileModal(focusField) {
        $('#modal_input_fullname').val(profileData.fullname);
        $('#modal_input_username').val(profileData.username);
        $('#modal_input_affiliation').val(profileData.affiliation);
        $('#modal_input_email').val(profileData.email);
        $('#modal_input_phone').val(profileData.phone);
        $('#modal_input_address').val(profileData.address);

        openModal('modal_edit_profile');

        setTimeout(function() {
            if (focusField === 'username') {
                $('#modal_input_username').focus();
            } else if (focusField === 'affiliation') {
                $('#modal_input_affiliation').focus();
            } else if (focusField === 'email') {
                $('#modal_input_email').focus();
            } else if (focusField === 'phone') {
                $('#modal_input_phone').focus();
            } else if (focusField === 'address') {
                $('#modal_input_address').focus();
            } else {
                $('#modal_input_fullname').focus();
            }
        }, 250);
    }

    // 2. Handle saving from the Edit Profile Modal
    function handleSaveModalProfile(e) {
        e.preventDefault();

        var newFullname = $('#modal_input_fullname').val().trim();
        var newUsername = $('#modal_input_username').val().trim().replace(/^@/, '');
        var newAffiliation = $('#modal_input_affiliation').val().trim();
        var newEmail = $('#modal_input_email').val().trim();
        var newPhone = $('#modal_input_phone').val().trim();
        var newAddress = $('#modal_input_address').val().trim();

        if (!newFullname || !newUsername || !newAffiliation || !newEmail || !newPhone || !newAddress) {
            showPortalToast('Please complete all 6 required fields.', false);
            return;
        }

        // Apply changes
        updateProfileDOM(newFullname, newUsername, newAffiliation, newEmail, newPhone, newAddress);

        closeModal('modal_edit_profile');
        showPortalToast('Profile details updated successfully!', true);
    }

    // 3. Toggle inline edit mode
    var isInlineEditActive = false;

    function toggleInlineEdit(forceState) {
        if (typeof forceState !== 'undefined') {
            isInlineEditActive = forceState;
        } else {
            isInlineEditActive = !isInlineEditActive;
        }

        if (isInlineEditActive) {
            // Synchronize inline inputs with current values
            $('#inline_input_fullname').val(profileData.fullname);
            $('#inline_input_username').val(profileData.username);
            $('#inline_input_affiliation').val(profileData.affiliation);
            $('#inline_input_email').val(profileData.email);
            $('#inline_input_phone').val(profileData.phone);
            $('#inline_input_address').val(profileData.address);

            $('.view-mode-item').hide();
            $('.edit-mode-item').fadeIn(150);
            $('#inline_actions_panel').css('display', 'flex');
            $('#inline_edit_btn_text').text('Exit Inline Edit');
            $('#btn_toggle_inline_edit').addClass('active').css('background', 'var(--primary-light)').css('color', 'var(--primary)');
            $('#inline_input_fullname').focus();
        } else {
            $('.edit-mode-item').hide();
            $('.view-mode-item').fadeIn(150);
            $('#inline_actions_panel').hide();
            $('#inline_edit_btn_text').text('Inline Edit Mode');
            $('#btn_toggle_inline_edit').removeClass('active').css('background', '').css('color', '');
        }
    }

    // 4. Handle saving from inline edit mode
    function handleSaveInlineProfile(e) {
        e.preventDefault();

        var newFullname = $('#inline_input_fullname').val().trim();
        var newUsername = $('#inline_input_username').val().trim().replace(/^@/, '');
        var newAffiliation = $('#inline_input_affiliation').val().trim();
        var newEmail = $('#inline_input_email').val().trim();
        var newPhone = $('#inline_input_phone').val().trim();
        var newAddress = $('#inline_input_address').val().trim();

        if (!newFullname || !newUsername || !newAffiliation || !newEmail || !newPhone || !newAddress) {
            showPortalToast('Please complete all 6 required fields.', false);
            return;
        }

        updateProfileDOM(newFullname, newUsername, newAffiliation, newEmail, newPhone, newAddress);
        toggleInlineEdit(false);
        showPortalToast('Profile details updated successfully!', true);
    }

    // 5. Central DOM Updater for all 6 editable attributes
    function updateProfileDOM(fullname, username, affiliation, email, phone, address) {
        profileData.fullname = fullname;
        profileData.username = username;
        profileData.affiliation = affiliation;
        profileData.email = email;
        profileData.phone = phone;
        profileData.address = address;

        // Calculate 2-letter initials
        var initials = fullname.split(' ').filter(Boolean).map(function(w) { return w[0]; }).join('').substring(0, 2).toUpperCase() || 'SG';

        // 1. Full Name
        $('#display_fullname').text(fullname);
        $('#hero_fullname_display').html(fullname + ' <span class="tag-badge verified-badge" title="Verified Orphanage Staff"><i class="fas fa-check-circle"></i> Verified</span>');
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

        // 4. Email
        $('#display_email').text(email);

        // 5. Phone
        $('#display_phone').text(phone);

        // 6. Address
        $('#display_address').text(address);
    }

    // 6. Toggle masked vs clear text password visibility
    var isPasswordRevealed = false;
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

    // 7. Toggle input type for password modals
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

    // 8. Dynamic password strength calculation
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

    // 9. Handle change password submit
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
            showPortalToast('Security Password updated and encrypted successfully!', true);
        }, 800);
    }

    // 10. Clipboard helper
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

    // 11. Export PDF function
    function exportProfileDossier() {
        showPortalToast('Generating encrypted staff & Volunteers PDF (PDF format)...', true);
        setTimeout(function() {
            showPortalToast('Staff & Volunteers PDF PDF downloaded successfully!', true);
        }, 1200);
    }
</script>
@endpush
