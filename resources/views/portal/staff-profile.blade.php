@extends('layouts.portal')

@section('title', 'Staff Profile & Qualifications')
@section('breadcrumb', 'Staff Profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/profile.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/profile.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/staff-profile.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/staff-profile.css')), 0, 12) }}">
@endpush

@section('content')
<!-- Subsystem Header -->
<div class="page-header-card">
    <div>
        <div class="staff-subsystem-pill">
            <i class="fas fa-user-nurse"></i> Staff Caregiver PDF & Duty Status
        </div>
        <h1 class="page-title">Staff Profile & Qualifications</h1>
        <p class="page-desc">
            View and manage your staff & Volunteers identification, assigned wards, duty shift status, and child safeguarding clearances.
        </p>
    </div>
    <div class="header-action-group">
        <a href="{{ route('profile') }}" class="btn-outline" style="border-color: #8b5cf6; color: #7c3aed; font-weight: 600;" title="Switch to Orphanage Admin Profile">
            <i class="fas fa-shield-alt"></i> Admin Profile View
        </a>
        <button type="button" class="btn-outline" id="btn_toggle_inline_edit" onclick="toggleInlineEdit()">
            <i class="fas fa-edit"></i> <span id="inline_edit_btn_text">Inline Edit Mode</span>
        </button>
        <button type="button" class="btn-outline" onclick="openModal('modal_change_password')">
            <i class="fas fa-key"></i> Change Password
        </button>
    </div>
</div>

<!-- Staff Profile Hero Identity Card -->
<div class="staff-hero-card">
    <div class="profile-hero-content">
        <div class="profile-hero-left">
            <div class="profile-avatar-wrapper">
                <div class="staff-avatar-circle" id="hero_avatar_display">
                    NM
                </div>
                <div class="avatar-status-dot" style="background: #10b981;" title="Active on-duty staff"></div>
            </div>

            <div class="profile-hero-details">
                <h1 id="hero_fullname_display">
                    Nurse Mary Jenkins
                    <span class="tag-badge verified-badge" title="Verified Orphanage Staff">
                        <i class="fas fa-check-circle"></i> Verified Staff
                    </span>
                </h1>

                <div class="profile-tag-group">
                    <span class="tag-badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25);" id="hero_role_badge">
                        <i class="fas fa-user-nurse"></i> Senior Caregiver
                    </span>
                    <span class="tag-badge username-badge" id="hero_username_badge" onclick="copyText($('#display_username').text(), 'Username copied to clipboard!')" style="cursor: pointer;" title="Click to copy username">
                        <i class="fas fa-at"></i> <span id="hero_username_text">nurse.mary</span> <i class="fas fa-copy" style="font-size: 10px; margin-left: 2px;"></i>
                    </span>
                    <span class="staff-duty-pill" id="hero_duty_pill">
                        <span class="staff-pulse-dot"></span> <span id="hero_duty_text">Morning Shift · On Duty</span>
                    </span>
                </div>

                <div class="profile-affiliation-sub" id="hero_affiliation_display">
                    <i class="fas fa-hospital-alt" style="color: #059669;"></i>
                    <span id="hero_affiliation_text">HopeHaven Orphanage – Pediatric Ward & Child Welfare Unit</span>
                </div>
            </div>
        </div>

        <div class="profile-hero-right">
            <button type="button" class="btn-outline" onclick="copyText($('#display_email').text(), 'Staff email copied to clipboard!')">
                <i class="fas fa-envelope"></i> Copy Email
            </button>
            <button type="button" class="btn-outline" style="border-color: #10b981; color: #059669; font-weight: 600;" onclick="openModal('modal_qr_id')">
                <i class="fas fa-qrcode"></i> Digital Staff ID
            </button>
        </div>
    </div>

    <!-- Quick Staff KPIs Bar -->
    <div class="hero-stats-row">
        <div class="hero-stat-item">
            <span class="hero-stat-label">System Role</span>
            <span class="hero-stat-val" id="kpi_role" style="color: #059669;">
                <i class="fas fa-id-badge"></i> Staff Caregiver (Tier 2)
            </span>
        </div>
        <div class="hero-stat-item">
            <span class="hero-stat-label">Assigned Ward</span>
            <span class="hero-stat-val" id="kpi_ward" style="color: #0284c7;">
                <i class="fas fa-baby"></i> Ward Alpha & Beta
            </span>
        </div>
        <div class="hero-stat-item">
            <span class="hero-stat-label">Safeguarding Level</span>
            <span class="hero-stat-val" style="color: #10b981;">
                <i class="fas fa-shield-alt"></i> Tier 2 (Pediatric Clear)
            </span>
        </div>
        <div class="hero-stat-item">
            <span class="hero-stat-label">Duty Shift Status</span>
            <span class="hero-stat-val" id="kpi_shift_status" style="color: #059669;">
                <i class="fas fa-clock"></i> Active (08:00 – 16:00)
            </span>
        </div>
    </div>
</div>

<!-- Tab Navigation Header (Divided Sections) -->
<div class="staff-profile-tabs">
    <button type="button" class="staff-tab-btn active" data-tab="tab_identity" onclick="switchStaffProfileTab('tab_identity', this)">
        <i class="fas fa-id-card"></i> Staff & Volunteers Details
    </button>
    <button type="button" class="staff-tab-btn" data-tab="tab_shift" onclick="switchStaffProfileTab('tab_shift', this)">
        <i class="fas fa-clock"></i> Shift & Operations
    </button>
    <button type="button" class="staff-tab-btn" data-tab="tab_clearance" onclick="switchStaffProfileTab('tab_clearance', this)">
        <i class="fas fa-certificate"></i> Accreditations & Logs <span class="badge-pill">3</span>
    </button>
    <button type="button" class="staff-tab-btn" data-tab="tab_security" onclick="switchStaffProfileTab('tab_security', this)">
        <i class="fas fa-shield-alt"></i> Security & Access
    </button>
</div>

<!-- Main Form Wrapper for Inline Editing -->
<form id="profile_inline_form" onsubmit="handleSaveInlineProfile(event)">

    <!-- ========================================================================= -->
    <!-- TAB 1: PERSONNEL & WARD DETAILS -->
    <!-- ========================================================================= -->
    <div class="staff-tab-pane active" id="tab_identity">
        <div class="tab-two-column-grid">
            <!-- Card 1: Core Caregiver Identity & Ward Assignment -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-user-circle" style="color: #059669;"></i> Caregiver Identity & Ward Assignment
                    </div>
                </div>

                <div class="profile-card-body">
                    <div class="detail-fields-grid">
                        <!-- Full Name -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_fullname">
                                <i class="fas fa-signature"></i> Full Name
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_fullname">Nurse Mary Jenkins</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('fullname')" title="Edit Full Name">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_fullname').text(), 'Full Name copied!')" title="Copy Full Name">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="text" id="inline_input_fullname" class="edit-mode-item editable-input" style="display: none;" value="Nurse Mary Jenkins" required>
                        </div>

                        <!-- Username -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_username">
                                <i class="fas fa-at"></i> Username
                            </label>
                            <div class="view-mode-item field-value-box mono">
                                <span class="field-value-text" id="display_username">nurse.mary</span>
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
                                <input type="text" id="inline_input_username" class="editable-input" value="nurse.mary" required>
                            </div>
                        </div>

                        <!-- Role (Read-only) -->
                        <div class="detail-field-item">
                            <label class="field-label">
                                <i class="fas fa-user-tag"></i> Role
                            </label>
                            <div class="field-value-box">
                                <span class="field-value-text" id="display_role">Senior Caregiver & Clinical Custodian</span>
                                <span class="tag-badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 10px; padding: 2px 7px;">RBAC Tier 2</span>
                            </div>
                        </div>

                        <!-- Staff ID (Read-only) -->
                        <div class="detail-field-item">
                            <label class="field-label">
                                <i class="fas fa-barcode"></i> Staff ID
                            </label>
                            <div class="field-value-box mono">
                                <span class="field-value-text">HH-STF-2022-089</span>
                                <span class="tag-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 10px;">Active Staff</span>
                            </div>
                        </div>

                        <!-- Affiliation -->
                        <div class="detail-field-item full-width">
                            <label class="field-label" for="inline_input_affiliation">
                                <i class="fas fa-hospital-alt"></i> Organization Affiliation
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_affiliation">HopeHaven Orphanage – Pediatric Ward & Child Welfare Unit</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('affiliation')" title="Edit Affiliation">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_affiliation').text(), 'Affiliation copied!')" title="Copy Affiliation">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="text" id="inline_input_affiliation" class="edit-mode-item editable-input" style="display: none;" value="HopeHaven Orphanage – Pediatric Ward & Child Welfare Unit" required>
                        </div>

                        <!-- Assigned Wards -->
                        <div class="detail-field-item full-width">
                            <label class="field-label" for="inline_input_ward">
                                <i class="fas fa-baby"></i> Assigned Wards / Dormitories
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_ward">Ward Alpha (Infants) & Ward Beta Nursery</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('ward')" title="Edit Ward Assignment">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_ward').text(), 'Ward copied!')" title="Copy Ward">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="text" id="inline_input_ward" class="edit-mode-item editable-input" style="display: none;" value="Ward Alpha (Infants) & Ward Beta Nursery" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Contact Information & Staff Residence -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-map-marker-alt" style="color: #059669;"></i> Contact Communications & Staff Residence
                    </div>
                </div>

                <div class="profile-card-body">
                    <div class="detail-fields-grid">
                        <!-- Email -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_email">
                                <i class="fas fa-envelope"></i> Official Staff Email
                            </label>
                            <div class="view-mode-item field-value-box">
                                <span class="field-value-text" id="display_email">mary.jenkins@hopehaven.org</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('email')" title="Edit Email">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_email').text(), 'Email copied!')" title="Copy Email">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="email" id="inline_input_email" class="edit-mode-item editable-input" style="display: none;" value="mary.jenkins@hopehaven.org" required>
                        </div>

                        <!-- Phone -->
                        <div class="detail-field-item">
                            <label class="field-label" for="inline_input_phone">
                                <i class="fas fa-phone"></i> Telephone / Mobile
                            </label>
                            <div class="view-mode-item field-value-box mono">
                                <span class="field-value-text" id="display_phone">+60 12-345 6789</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('phone')" title="Edit Phone Number">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_phone').text(), 'Phone copied!')" title="Copy Phone">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="tel" id="inline_input_phone" class="edit-mode-item editable-input" style="display: none;" value="+60 12-345 6789" required>
                        </div>

                        <!-- Residential Address -->
                        <div class="detail-field-item full-width">
                            <label class="field-label" for="inline_input_address">
                                <i class="fas fa-home"></i> Staff Residential Address & Quarters
                            </label>
                            <div class="view-mode-item field-value-box multi-line">
                                <span class="field-value-text" id="display_address">HopeHaven Orphanage Staff Quarters, Block B Room 204, St. Jude Valley, CA 94103, United States</span>
                                <div class="field-actions-cluster">
                                    <button type="button" class="field-action-btn edit-btn" onclick="openEditProfileModal('address')" title="Edit Address">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="field-action-btn" onclick="copyText($('#display_address').text(), 'Address copied!')" title="Copy Address">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <textarea id="inline_input_address" class="edit-mode-item editable-input" rows="2" style="display: none;" required>HopeHaven Orphanage Staff Quarters, Block B Room 204, St. Jude Valley, CA 94103, United States</textarea>
                        </div>
                    </div>

                    <!-- Staff Residence GPS / Location Card Preview -->
                    <div class="address-map-card" style="border-left-color: #10b981; margin-top: 20px;">
                        <div class="address-map-icon" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                            <i class="fas fa-compass"></i>
                        </div>
                        <div class="address-map-text">
                            <h4>HopeHaven Orphanage – Staff Residential Quarters (Block B)</h4>
                            <p>Sector: East Orphanage Wing • Room 204 • Direct Nursery Walkway & Emergency Intercom Line 4</p>
                        </div>
                        <button type="button" class="btn-pill-small" onclick="showPortalToast('Staff Quarters emergency coordinates copied!', true)">
                            <i class="fas fa-location-arrow"></i> Locate Quarters
                        </button>
                    </div>

                    <!-- Inline Actions Bar (Save / Cancel) -->
                    <div class="inline-actions-panel edit-actions-panel" style="display: none; margin-top: 18px;">
                        <button type="button" class="btn-outline" onclick="toggleInlineEdit(false)">Cancel</button>
                        <button type="submit" class="btn-primary" style="background: #059669; border-color: #047857;">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: SHIFT ROSTER & OPERATIONS -->
    <!-- ========================================================================= -->
    <div class="staff-tab-pane" id="tab_shift">
        <div class="tab-two-column-grid">
            <!-- Card 3: Active Shift Schedule & Status -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-clock" style="color: #059669;"></i> Active Shift & Caregiver Status
                    </div>
                    <div class="shift-status-toggle-group">
                        <button type="button" class="shift-status-toggle-btn active" id="btn_status_duty" onclick="setShiftStatus('On Duty')">On Duty</button>
                        <button type="button" class="shift-status-toggle-btn" id="btn_status_break" onclick="setShiftStatus('On Break')">On Break</button>
                        <button type="button" class="shift-status-toggle-btn" id="btn_status_handover" onclick="setShiftStatus('Handover')">Handover</button>
                    </div>
                </div>

                <div class="profile-card-body">
                    <div style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-sm); padding: 14px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <span style="font-size: 12px; font-weight: 700; color: #047857; text-transform: uppercase; letter-spacing: 0.5px;">Current Duty Schedule</span>
                            <span class="tag-badge" style="background: #10b981; color: #ffffff; font-size: 10px;">Primary Roster</span>
                        </div>
                        <div style="font-size: 15px; font-weight: 800; color: var(--dark);">Morning Shift (08:00 – 16:00)</div>
                        <div style="font-size: 12px; color: var(--dark-muted); margin-top: 3px;">
                            Duty Station: Ward Alpha Caregiver Desk • Sector Nursery & Infancy Unit
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 10px 12px;">
                            <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;">Reporting Supervisor</div>
                            <div style="font-size: 12.5px; font-weight: 700; color: var(--dark); margin-top: 2px;">Sister Grace Montgomery</div>
                            <div style="font-size: 11px; color: #8b5cf6;">Orphanage Admin</div>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 10px 12px;">
                            <div style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase;">Emergency Medical Lead</div>
                            <div style="font-size: 12.5px; font-weight: 700; color: var(--dark); margin-top: 2px;">Dr. Robert Vance</div>
                            <div style="font-size: 11px; color: #059669;">Pediatric Consultant</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Daily Shift Handover Log -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-clipboard-list" style="color: #059669;"></i> Shift Handover Remarks & Notes
                    </div>
                </div>

                <div class="profile-card-body">
                    <label class="field-label" for="shift_handover_notes">
                        <i class="fas fa-comment-alt"></i> Notes for Incoming Caregiver
                    </label>
                    <textarea id="shift_handover_notes" class="form-control" rows="4" style="font-size: 12.5px; resize: vertical;" placeholder="Add remarks for evening shift caregiver (e.g., Ward Beta feeding schedule completed, nursery temperature log up to date)..."></textarea>
                    <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
                        <button type="button" class="btn-primary" style="background: #059669; border-color: #047857;" onclick="saveShiftNote()">
                            <i class="fas fa-check"></i> Save Shift Handover Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: ACCREDITATIONS & RECENT ACTIVITY LOGS -->
    <!-- ========================================================================= -->
    <div class="staff-tab-pane" id="tab_clearance">
        <div class="tab-two-column-grid">
            <!-- Card 5: Professional Accreditations & Safeguarding Clearances -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-certificate" style="color: #059669;"></i> Accreditations & Safeguarding Clearances
                    </div>
                    <span class="tag-badge verified-badge">
                        <i class="fas fa-check"></i> 3 Active Clearances
                    </span>
                </div>

                <div class="profile-card-body">
                    <!-- Cert 1 -->
                    <div class="cert-card-item">
                        <div class="cert-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--dark);">Pediatric First Aid & CPR</div>
                                <span class="tag-badge verified-badge" style="font-size: 9.5px; padding: 2px 6px;">Certified</span>
                            </div>
                            <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">
                                Issued by National Red Cross • License: CPR-PED-2024-918
                            </div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                Valid through: <strong>November 2027</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Cert 2 -->
                    <div class="cert-card-item">
                        <div class="cert-icon-wrap" style="background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--dark);">Child Safeguarding Act Clearance</div>
                                <span class="tag-badge verified-badge" style="font-size: 9.5px; padding: 2px 6px;">Tier 2 Clear</span>
                            </div>
                            <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">
                                Background Check & Safeguarding Vetting #HH-SAFE-4881
                            </div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                Verified by: Child Welfare Services Board
                            </div>
                        </div>
                    </div>

                    <!-- Cert 3 -->
                    <div class="cert-card-item">
                        <div class="cert-icon-wrap" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--dark);">Ward Medicine & Supply Distribution Access</div>
                                <span class="tag-badge" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6; font-size: 9.5px; padding: 2px 6px;">Authorized</span>
                            </div>
                            <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">
                                Ward Alpha & Beta Clinical Storeroom Clearance
                            </div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                Level 2 Prescription Administering Rights
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Recent Duty Activity & Clinical Care Timeline -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-history" style="color: #059669;"></i> Recent Caregiver Activity & Logs
                    </div>
                    <button type="button" class="btn-pill-small" onclick="showPortalToast('Staff Duty Log refreshed.', true)">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>

                <div class="profile-card-body">
                    <div class="activity-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color: #10b981;"></div>
                            <div class="timeline-title">Morning Vitals Check Completed for 12 Wards</div>
                            <div class="timeline-time">Today, 08:30 • Ward Alpha Nursery Care Station</div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color: #0284c7;"></div>
                            <div class="timeline-title">Given Pediatric Formula & Nursery Supplies</div>
                            <div class="timeline-time">Today, 09:15 • Storeroom Supply Distribution logged</div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color: #8b5cf6;"></div>
                            <div class="timeline-title">Logged Term 2 Exam Papers for Primary Education</div>
                            <div class="timeline-time">Yesterday, 14:10 • Child Education Subsystem</div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color: #f59e0b;"></div>
                            <div class="timeline-title">Shift Handover Completed with Nurse Sarah</div>
                            <div class="timeline-time">Yesterday, 15:25 • Ward Alpha Sign-off</div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-title">Authenticated from Ward Alpha Care Station Terminal</div>
                            <div class="timeline-time">Today, 06:58 • IP: 192.168.1.28</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: SECURITY & CREDENTIALS -->
    <!-- ========================================================================= -->
    <div class="staff-tab-pane" id="tab_security">
        <div class="tab-two-column-grid">
            <!-- Card 7: Password Management -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-lock" style="color: #059669;"></i> Authentication & Password Credentials
                    </div>
                    <span class="tag-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-shield-alt"></i> 256-bit Encrypted
                    </span>
                </div>

                <div class="profile-card-body">
                    <label class="field-label">
                        <i class="fas fa-key"></i> Current Account Password
                    </label>

                    <div class="password-display-wrapper">
                        <div class="password-row-main">
                            <div>
                                <span class="password-hidden-dots" id="password_display_text">••••••••••••••••</span>
                                <span id="password_clear_text" style="display: none; font-family: 'JetBrains Mono', monospace; font-size: 14px; font-weight: 700; color: #059669;">HopeHaven#Nurse2026!</span>
                            </div>

                            <div class="password-actions-group">
                                <button type="button" class="btn-pill-small" id="btn_toggle_password_mask" onclick="togglePasswordVisibility()">
                                    <i class="fas fa-eye" id="toggle_password_icon"></i> <span id="toggle_password_text">Show</span>
                                </button>
                                <button type="button" class="btn-pill-small btn-primary-pill" style="background: #059669;" onclick="openModal('modal_change_password')">
                                    <i class="fas fa-edit"></i> Change Password
                                </button>
                            </div>
                        </div>

                        <div class="password-meta-info">
                            <div class="strength-indicator">
                                <i class="fas fa-shield-virus" style="color: #10b981;"></i> Security Rating: Strong (Staff Standard)
                                <div class="strength-dots-bar">
                                    <div class="strength-dot" style="background: #10b981;"></div>
                                    <div class="strength-dot" style="background: #10b981;"></div>
                                    <div class="strength-dot" style="background: #10b981;"></div>
                                    <div class="strength-dot" style="background: #10b981;"></div>
                                </div>
                            </div>
                            <div style="font-size: 11.5px; color: #94a3b8;">
                                <i class="fas fa-history"></i> Last rotated 18 days ago (Mandatory cycle: 90 days)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 8: Safeguarding Policies & 2FA Security -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <i class="fas fa-user-shield" style="color: #059669;"></i> Access Policies & Session Guard
                    </div>
                </div>

                <div class="profile-card-body">
                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 14px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 13px; font-weight: 700; color: var(--dark);">Two-Factor Authentication</div>
                                    <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">SMS Token + Staff Authenticator App</div>
                                </div>
                                <span class="tag-badge verified-badge" style="font-size: 10px;">Active</span>
                            </div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 14px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 13px; font-weight: 700; color: var(--dark);">Session Approval & Ward Auto-lock</div>
                                    <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">Auto-lock after 10 min idle at ward caregiver station</div>
                                </div>
                                <span class="tag-badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 10px;">Enforced</span>
                            </div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 14px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 13px; font-weight: 700; color: var(--dark);">Orphanage Digital Pass</div>
                                    <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">Cryptographic Ward Entry NFC / QR Pass</div>
                                </div>
                                <button type="button" class="btn-pill-small" style="background: #059669; color: #ffffff;" onclick="openModal('modal_qr_id')">
                                    <i class="fas fa-qrcode"></i> View Pass
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Include Modals Partial -->
@include('portal.partials.staff-profile-modals')

@endsection

@push('scripts')
    <script src="{{ asset('js/portal/staff-profile.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/staff-profile.js')), 0, 12) }}"></script>
@endpush
