@extends('layouts.portal')

@section('title', 'Admin Accounts & Permissions')
@section('breadcrumb', 'Access & Security / Register Admin')

@push('styles')
    <!-- Registration / Provisioning CSS -->
    <link rel="stylesheet" href="{{ asset('css/portal/auth.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/auth.css')), 0, 12) }}">
@endpush

@section('content')

<!-- Restricted Administrative Context Hero -->
<section class="admin-provision-hero">
    <div>
        <div class="hero-main-title">
            <i class="fas fa-user-shield"></i>
            <span>Admin Accounts & Permissions</span>
        </div>
        <p class="hero-desc">
            <strong>Account Registration Rules:</strong> To protect children and their information, public sign-up is disabled. An existing admin must review and approve each new admin account.
        </p>
    </div>
    <div class="hero-auth-badge">
        <div class="hero-auth-avatar">SG</div>
        <div class="hero-auth-meta">
            <div class="meta-label">
                <i class="fas fa-lock"></i> Approving Admin
            </div>
            <div class="meta-name">Sister Grace</div>
            <div class="meta-id">ID: ADM-2026-001 · Super Admin</div>
        </div>
    </div>
</section>

<!-- Quick Administrative Metrics -->
<div class="provision-metrics-row">
    <div class="prov-metric-card">
        <div class="prov-metric-icon coral">
            <i class="fas fa-users-cog"></i>
        </div>
        <div class="prov-metric-info">
            <div class="m-label">Active Administrators</div>
            <div class="m-val" id="stat_total_admins">4 Admins</div>
        </div>
    </div>
    <div class="prov-metric-card">
        <div class="prov-metric-icon blue">
            <i class="fas fa-fingerprint"></i>
        </div>
        <div class="prov-metric-info">
            <div class="m-label">Next Available AdminID</div>
            <div class="m-val" style="font-family: 'JetBrains Mono', monospace; font-size: 17px;" id="stat_next_id">ADM-2026-005</div>
        </div>
    </div>
    <div class="prov-metric-card">
        <div class="prov-metric-icon green">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="prov-metric-info">
            <div class="m-label">Enforced 2FA Status</div>
            <div class="m-val">100% Compliant</div>
        </div>
    </div>
    <div class="prov-metric-card">
        <div class="prov-metric-icon purple">
            <i class="fas fa-network-wired"></i>
        </div>
        <div class="prov-metric-info">
            <div class="m-label">Access Areas</div>
            <div class="m-val">5 Modules Live</div>
        </div>
    </div>
</div>

<!-- Main 2-Column Provisioning Workspace -->
<div class="provision-layout-grid">
    <!-- Left Column: Admin Registration Form -->
    <div class="provision-card">
        <div class="provision-card-header">
            <div>
                <h2 class="provision-card-title">
                    <i class="fas fa-user-plus" style="color: var(--primary);"></i> Register New Administrator
                </h2>
                <p class="provision-card-subtitle">
                    Enter full legal name, official email address, initial password, and institutional Admin ID.
                </p>
            </div>
            <span class="badge-role super" style="font-size: 11.5px;">
                <i class="fas fa-key"></i> Dual-Admin Approval
            </span>
        </div>

        <div class="provision-card-body">
            <form id="admin_provision_form" onsubmit="handleAdminProvision(event)">
                @csrf

                <!-- Section: Mandatory Core Credentials -->
                <div class="form-section-divider">
                    <i class="fas fa-id-card"></i> 1. Administrator Identity & Credentials
                </div>

                <div class="form-grid-2">
                    <!-- 1. Full Name -->
                    <div class="form-group">
                        <label class="form-label" for="admin_full_name">
                            Full Name <span style="color: var(--primary);">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-user input-icon-left"></i>
                            <input 
                                type="text" 
                                id="admin_full_name" 
                                name="name" 
                                class="form-control" 
                                placeholder="e.g. David Kim / Sarah Jenkins" 
                                required
                            >
                        </div>
                        <div class="field-hint">Official name matching government identification</div>
                    </div>

                    <!-- 2. Admin ID (adminID) -->
                    <div class="form-group">
                        <label class="form-label" for="admin_id">
                            Administrator ID (AdminID) <span style="color: var(--primary);">*</span>
                        </label>
                        <div class="input-group-action">
                            <div class="input-with-icon" style="flex: 1;">
                                <i class="fas fa-id-badge input-icon-left"></i>
                                <input 
                                    type="text" 
                                    id="admin_id" 
                                    name="admin_id" 
                                    class="form-control font-mono" 
                                    value="ADM-2026-005" 
                                    placeholder="ADM-YYYY-XXX" 
                                    pattern="ADM-[0-9]{4}-[0-9]{3,4}"
                                    title="Format must be ADM-YYYY-XXX (e.g. ADM-2026-005)"
                                    required
                                >
                            </div>
                            <button 
                                type="button" 
                                class="btn-inline-action" 
                                onclick="generateNextAdminId()" 
                                title="Auto-increment next sequential ID"
                            >
                                <i class="fas fa-sync-alt"></i> Auto ID
                            </button>
                        </div>
                        <div class="field-hint">
                            <span class="badge-admin-id" style="font-size: 10px; padding: 1px 6px;">ADM-YYYY-XXX</span> 
                            Unique orphanage identifier
                        </div>
                    </div>
                </div>

                <!-- 3. Official Email -->
                <div class="form-group">
                    <label class="form-label" for="admin_email">
                        Work Email <span style="color: var(--primary);">*</span>
                    </label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope input-icon-left"></i>
                        <input 
                            type="email" 
                            id="admin_email" 
                            name="email" 
                            class="form-control" 
                            placeholder="e.g. david.kim@hopehaven.org" 
                            required
                        >
                    </div>
                    <div class="field-hint">Must belong to official domain or verified institutional partner</div>
                </div>

                <!-- 4. Initial Password & Confirmation -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label class="form-label" for="admin_password" style="margin-bottom: 0;">
                                Initial Temporary Password <span style="color: var(--primary);">*</span>
                            </label>
                            <button 
                                type="button" 
                                onclick="generateRandomPassword()" 
                                style="background: none; border: none; color: var(--primary); font-size: 11.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                            >
                                <i class="fas fa-magic"></i> Auto-Generate
                            </button>
                        </div>
                        <div class="input-with-icon">
                            <i class="fas fa-lock input-icon-left"></i>
                            <input 
                                type="password" 
                                id="admin_password" 
                                name="password" 
                                class="form-control" 
                                placeholder="Min. 8 characters" 
                                minlength="8" 
                                oninput="checkPasswordStrength(this.value)"
                                required
                            >
                            <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('admin_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="pw-strength-bar">
                            <div class="pw-strength-fill" id="pw_strength_fill"></div>
                        </div>
                        <div class="field-hint" id="pw_strength_text">Minimum 8 characters with letters & numbers</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin_password_confirmation">
                            Confirm Password <span style="color: var(--primary);">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-check-double input-icon-left"></i>
                            <input 
                                type="password" 
                                id="admin_password_confirmation" 
                                name="password_confirmation" 
                                class="form-control" 
                                placeholder="Re-enter password" 
                                minlength="8" 
                                required
                            >
                            <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility('admin_password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="field-hint">Must match password exactly</div>
                    </div>
                </div>

                <!-- Section: Admin Scope & Role Tier -->
                <div class="form-section-divider">
                    <i class="fas fa-shield-alt"></i> 2. Administrative Role & Scope
                </div>

                <div class="admin-tier-grid">
                    <label class="admin-tier-option active" onclick="selectAdminTier(this, 'System Administrator', 'Systems & IT Infrastructure')">
                        <input type="radio" name="admin_tier" value="system_admin" checked>
                        <div>
                            <div class="tier-label-name">System Administrator</div>
                            <div class="tier-label-desc">Full portal infrastructure, security policies, backup audits & RBAC management.</div>
                        </div>
                    </label>

                    <label class="admin-tier-option" onclick="selectAdminTier(this, 'Welfare Operations Admin', 'Child Welfare & Family Services')">
                        <input type="radio" name="admin_tier" value="welfare_admin">
                        <div>
                            <div class="tier-label-name">Welfare Operations Admin</div>
                            <div class="tier-label-desc">Oversees child admissions, educational records & approval of urgent welfare requests.</div>
                        </div>
                    </label>

                    <label class="admin-tier-option" onclick="selectAdminTier(this, 'Financial Oversight Admin', 'Treasury & Grants Management')">
                        <input type="radio" name="admin_tier" value="finance_admin">
                        <div>
                            <div class="tier-label-name">Financial Oversight Admin</div>
                            <div class="tier-label-desc">Audits donation receipts, tax-deductible contributions, and payment tracking.</div>
                        </div>
                    </label>

                    <label class="admin-tier-option" onclick="selectAdminTier(this, 'Orphanage Co-Director', 'Orphanage Management')">
                        <input type="radio" name="admin_tier" value="co_director">
                        <div>
                            <div class="tier-label-name">Orphanage Co-Director</div>
                            <div class="tier-label-desc">Executive sign-off authority across both operations, staffing, and external regulatory bodies.</div>
                        </div>
                    </label>
                </div>

                <!-- Section: Subsystems Permissions Checklist -->
                <div class="form-section-divider">
                    <i class="fas fa-key"></i> 3. Page Access
                </div>

                <div class="perms-checkbox-list">
                    <label class="perm-checkbox-item">
                        <input type="checkbox" name="permissions[]" value="child_welfare" checked>
                        <span><i class="fas fa-graduation-cap" style="color: #3b82f6; width: 16px;"></i> Child Care & Education</span>
                    </label>
                    <label class="perm-checkbox-item">
                        <input type="checkbox" name="permissions[]" value="welfare_requests" checked>
                        <span><i class="fas fa-clipboard-check" style="color: #f59e0b; width: 16px;"></i> Welfare Requests</span>
                    </label>
                    <label class="perm-checkbox-item">
                        <input type="checkbox" name="permissions[]" value="staff_volunteers" checked>
                        <span><i class="fas fa-users-cog" style="color: #8b5cf6; width: 16px;"></i> Staff & Volunteer Rosters</span>
                    </label>
                    <label class="perm-checkbox-item">
                        <input type="checkbox" name="permissions[]" value="donations" checked>
                        <span><i class="fas fa-hand-holding-usd" style="color: #ec4899; width: 16px;"></i> Donations & Receipts</span>
                    </label>
                    <label class="perm-checkbox-item">
                        <input type="checkbox" name="permissions[]" value="admin_provision" checked>
                        <span><i class="fas fa-user-shield" style="color: var(--primary); width: 16px;"></i> Admin Onboarding</span>
                    </label>
                </div>

                <!-- Section: Security Directives -->
                <div class="security-directives-box">
                    <div class="security-directives-title">
                        <i class="fas fa-exclamation-triangle"></i> Mandatory Security Enforcements
                    </div>
                    <label>
                        <input type="checkbox" id="enforce_pw_reset" checked>
                        <span>Require password reset upon first administrator login</span>
                    </label>
                    <label>
                        <input type="checkbox" id="enforce_2fa" checked>
                        <span>Enforce Hardware Key / Authenticator App (2FA) verification</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn_provision_submit" class="auth-submit-action">
                    <i class="fas fa-user-shield"></i> Create Admin Account
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Protocol Guide & Verification Seal -->
    <div>
        <!-- Security Protocol Card -->
        <div class="protocol-card">
            <h3 class="protocol-title">
                <i class="fas fa-shield-virus" style="color: var(--primary);"></i>
                Admin Management Protocol
            </h3>

            <div class="protocol-step-item">
                <div class="step-num">1</div>
                <div class="step-info">
                    <div class="step-heading">Zero Public Self-Signup</div>
                    <div class="step-text">
                        orphanage databases contain sensitive child medical & legal records. Self-registration is permanently disabled.
                    </div>
                </div>
            </div>

            <div class="protocol-step-item">
                <div class="step-num">2</div>
                <div class="step-info">
                    <div class="step-heading">Unique Institutional AdminID</div>
                    <div class="step-text">
                        Each administrator receives an immutable identification code (e.g. <code>ADM-2026-005</code>) used in system audit trails.
                    </div>
                </div>
            </div>

            <div class="protocol-step-item">
                <div class="step-num">3</div>
                <div class="step-info">
                    <div class="step-heading">Credential Slip Generation</div>
                    <div class="step-text">
                        Upon account Setup, an official credentials voucher is produced. Share this securely with the appointed administrator.
                    </div>
                </div>
            </div>

            <div class="protocol-step-item">
                <div class="step-num">4</div>
                <div class="step-info">
                    <div class="step-heading">Dual Signatory Trail</div>
                    <div class="step-text">
                        Each new admin account records who approved it and when.
                    </div>
                </div>
            </div>
        </div>

        <!-- Authorizer Verification Seal -->
        <div class="seal-card">
            <div class="seal-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="seal-title">Verified Authorizer Clearance</div>
            <div class="seal-sub">
                Current Active Session: <strong>Sister Grace</strong><br>
                Clearance: Level 4 Super Administrator (Charter #HH-SANCT-01)
            </div>
            <div class="seal-timestamp">
                <i class="fas fa-clock"></i> Session Timestamp: {{ now()->format('Y-m-d H:i') }} UTC+8
            </div>
        </div>
    </div>
</div>

<!-- Section: Active Sanctuary Administrators Directory -->
<div class="admin-directory-section">
    <div class="directory-toolbar">
        <div>
            <h3 class="directory-toolbar-title">
                <i class="fas fa-users-cog" style="color: var(--primary);"></i>
                Active Orphanage Admins
                <span class="badge-count" id="admin_count_badge">4 Registered</span>
            </h3>
            <p style="font-size: 12.5px; color: var(--dark-muted); margin-top: 3px;">
                Directory of staff & Volunteers authorized with administrative clearance across the system.
            </p>
        </div>

        <div class="directory-search-box">
            <div class="input-with-icon">
                <i class="fas fa-search input-icon-left"></i>
                <input 
                    type="text" 
                    id="search_admins_input" 
                    class="directory-search-input" 
                    placeholder="Search by Name, Email, or AdminID..." 
                    oninput="filterAdmins(this.value)"
                >
            </div>
        </div>
    </div>

    <div class="admin-table-container">
        <table class="admin-table" id="admins_table">
            <thead>
                <tr>
                    <th>Admin ID</th>
                    <th>Administrator</th>
                    <th>Official Email</th>
                    <th>Role & Scope</th>
                    <th>Authorizing Admin</th>
                    <th>Status</th>
                    <th class="action-column" style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody id="admins_tbody">
                <!-- Row 1: Sister Grace (Super Admin) -->
                <tr data-admin-id="ADM-2026-001" data-name="Sister Grace" data-email="grace@hopehaven.org">
                    <td>
                        <span class="badge-admin-id">
                            <i class="fas fa-id-badge"></i> ADM-2026-001
                        </span>
                    </td>
                    <td>
                        <div class="admin-profile-cell">
                            <div class="admin-avatar" style="background: linear-gradient(135deg, #ff2c5a, #eb1e4b);">SG</div>
                            <div>
                                <div class="admin-cell-name">Sister Grace</div>
                                <div class="admin-cell-email">Founding Matron</div>
                            </div>
                        </div>
                    </td>
                    <td><code>grace@hopehaven.org</code></td>
                    <td>
                        <span class="badge-role super">
                            <i class="fas fa-crown"></i> Super Administrator
                        </span>
                    </td>
                    <td><span style="color: #64748b; font-size: 12px;">Board of Trustees</span></td>
                    <td>
                        <span class="status-indicator active">
                            <span class="status-dot"></span> Active
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button class="table-action-btn" onclick="viewCredentialsModal('ADM-2026-001', 'Sister Grace', 'grace@hopehaven.org', 'Super Administrator', 'Board of Trustees')">
                            <i class="fas fa-file-invoice"></i> Voucher
                        </button>
                    </td>
                </tr>

                <!-- Row 2: David Kim (IT & Security) -->
                <tr data-admin-id="ADM-2026-002" data-name="David Kim" data-email="david.kim@hopehaven.org">
                    <td>
                        <span class="badge-admin-id">
                            <i class="fas fa-id-badge"></i> ADM-2026-002
                        </span>
                    </td>
                    <td>
                        <div class="admin-profile-cell">
                            <div class="admin-avatar" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">DK</div>
                            <div>
                                <div class="admin-cell-name">David Kim</div>
                                <div class="admin-cell-email">Infrastructure Lead</div>
                            </div>
                        </div>
                    </td>
                    <td><code>david.kim@hopehaven.org</code></td>
                    <td>
                        <span class="badge-role it">
                            <i class="fas fa-shield-alt"></i> Systems & Security
                        </span>
                    </td>
                    <td><span style="color: #334155; font-size: 12px; font-weight: 500;">Sister Grace (ADM-001)</span></td>
                    <td>
                        <span class="status-indicator active">
                            <span class="status-dot"></span> Active
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button class="table-action-btn" onclick="viewCredentialsModal('ADM-2026-002', 'David Kim', 'david.kim@hopehaven.org', 'Systems & Security Administrator', 'Sister Grace (ADM-001)')">
                            <i class="fas fa-file-invoice"></i> Voucher
                        </button>
                    </td>
                </tr>

                <!-- Row 3: Sarah Jenkins (Welfare Operations) -->
                <tr data-admin-id="ADM-2026-003" data-name="Sarah Jenkins" data-email="s.jenkins@hopehaven.org">
                    <td>
                        <span class="badge-admin-id">
                            <i class="fas fa-id-badge"></i> ADM-2026-003
                        </span>
                    </td>
                    <td>
                        <div class="admin-profile-cell">
                            <div class="admin-avatar" style="background: linear-gradient(135deg, #10b981, #059669);">SJ</div>
                            <div>
                                <div class="admin-cell-name">Sarah Jenkins</div>
                                <div class="admin-cell-email">Welfare Director</div>
                            </div>
                        </div>
                    </td>
                    <td><code>s.jenkins@hopehaven.org</code></td>
                    <td>
                        <span class="badge-role welfare">
                            <i class="fas fa-child"></i> Welfare Operations
                        </span>
                    </td>
                    <td><span style="color: #334155; font-size: 12px; font-weight: 500;">Sister Grace (ADM-001)</span></td>
                    <td>
                        <span class="status-indicator active">
                            <span class="status-dot"></span> Active
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button class="table-action-btn" onclick="viewCredentialsModal('ADM-2026-003', 'Sarah Jenkins', 's.jenkins@hopehaven.org', 'Welfare Operations Admin', 'Sister Grace (ADM-001)')">
                            <i class="fas fa-file-invoice"></i> Voucher
                        </button>
                    </td>
                </tr>

                <!-- Row 4: Marcus Tan (Treasury & Donations) -->
                <tr data-admin-id="ADM-2026-004" data-name="Marcus Tan" data-email="m.tan@hopehaven.org">
                    <td>
                        <span class="badge-admin-id">
                            <i class="fas fa-id-badge"></i> ADM-2026-004
                        </span>
                    </td>
                    <td>
                        <div class="admin-profile-cell">
                            <div class="admin-avatar" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">MT</div>
                            <div>
                                <div class="admin-cell-name">Marcus Tan</div>
                                <div class="admin-cell-email">Financial Controller</div>
                            </div>
                        </div>
                    </td>
                    <td><code>m.tan@hopehaven.org</code></td>
                    <td>
                        <span class="badge-role finance">
                            <i class="fas fa-coins"></i> Financial Oversight
                        </span>
                    </td>
                    <td><span style="color: #334155; font-size: 12px; font-weight: 500;">Sister Grace (ADM-001)</span></td>
                    <td>
                        <span class="status-indicator active">
                            <span class="status-dot"></span> Active
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button class="table-action-btn" onclick="viewCredentialsModal('ADM-2026-004', 'Marcus Tan', 'm.tan@hopehaven.org', 'Financial Oversight Admin', 'Sister Grace (ADM-001)')">
                            <i class="fas fa-file-invoice"></i> Voucher
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- CREDENTIALS SLIP / VOUCHER MODAL -->
<div id="modal_credentials_voucher" class="modal-voucher-backdrop">
    <div class="modal-voucher-content">
        <div class="voucher-header">
            <button class="voucher-close-btn" onclick="closeVoucherModal()">&times;</button>
            <span class="voucher-badge">
                <i class="fas fa-check-circle"></i> Account Creation Record
            </span>
            <div class="voucher-title">Orphanage Admin Account Details</div>
            <div class="voucher-sub">HopeHaven Orphanage & Child Care Security Council</div>
        </div>

        <div class="voucher-body">
            <div class="voucher-card-box">
                <!-- Admin ID Field -->
                <div class="voucher-field">
                    <div class="voucher-field-label">Administrator Identification Code (AdminID)</div>
                    <div class="voucher-field-value">
                        <span class="voucher-admin-id-highlight" id="voucher_modal_admin_id">ADM-2026-005</span>
                        <button class="voucher-copy-btn" onclick="copyText('voucher_modal_admin_id', 'Admin ID copied!')">
                            <i class="fas fa-copy"></i> Copy ID
                        </button>
                    </div>
                </div>

                <!-- Full Name Field -->
                <div class="voucher-field">
                    <div class="voucher-field-label">Full Name</div>
                    <div class="voucher-field-value" id="voucher_modal_name">David Kim</div>
                </div>

                <!-- Email Field -->
                <div class="voucher-field">
                    <div class="voucher-field-label">Work Email</div>
                    <div class="voucher-field-value">
                        <span id="voucher_modal_email">d.kim@hopehaven.org</span>
                        <button class="voucher-copy-btn" onclick="copyText('voucher_modal_email', 'Email copied!')">
                            <i class="fas fa-copy"></i> Copy Email
                        </button>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="voucher-field" id="voucher_pw_container">
                    <div class="voucher-field-label">Initial Temporary Password</div>
                    <div class="voucher-field-value">
                        <span class="voucher-password-highlight" id="voucher_modal_password">••••••••••••</span>
                        <button class="voucher-copy-btn" onclick="copyText('voucher_modal_password', 'Password copied!')">
                            <i class="fas fa-copy"></i> Copy Password
                        </button>
                    </div>
                </div>

                <!-- Role / Scope Field -->
                <div class="voucher-field">
                    <div class="voucher-field-label">Appointed Administrative Role</div>
                    <div class="voucher-field-value" id="voucher_modal_role">System Administrator</div>
                </div>

                <!-- Authorizer Info -->
                <div class="voucher-field" style="border-top: 1px dashed #cbd5e1; padding-top: 10px; margin-top: 10px;">
                    <div class="voucher-field-label">Authorizing Administrator & Timestamp</div>
                    <div style="font-size: 12px; color: #475569; margin-top: 2px;">
                        Authorized by: <strong id="voucher_modal_authorizer">Sister Grace (ADM-2026-001)</strong> · {{ now()->format('M d, Y H:i') }} UTC+8
                    </div>
                </div>
            </div>

            <div class="voucher-footer">
                <button type="button" class="voucher-btn-primary" onclick="copyFullPackage()">
                    <i class="fas fa-clipboard-check"></i> Copy Full Credential Package
                </button>
                <button type="button" class="voucher-btn-secondary" onclick="closeVoucherModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // State tracking for sequential ID generation
    var nextIdNumber = 5;
    var selectedTierTitle = 'System Administrator';
    var selectedDepartment = 'Systems & IT Infrastructure';

    // Auto-increment Next Admin ID
    function generateNextAdminId() {
        var year = new Date().getFullYear();
        var seqFormatted = String(nextIdNumber).padStart(3, '0');
        var generatedId = "ADM-" + year + "-" + seqFormatted;
        $('#admin_id').val(generatedId);
        showToast("Generated Next Admin ID: " + generatedId, true);
    }

    // Tier selection handler
    function selectAdminTier(element, title, dept) {
        $('.admin-tier-option').removeClass('active');
        $(element).addClass('active');
        selectedTierTitle = title;
        selectedDepartment = dept;
    }

    // Toggle Password Visibility
    function togglePasswordVisibility(fieldId, btn) {
        var input = document.getElementById(fieldId);
        var icon = $(btn).find('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    }

    // Generate Random Secure Password
    function generateRandomPassword() {
        var chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%&*";
        var password = "";
        for (var i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        $('#admin_password').val(password);
        $('#admin_password_confirmation').val(password);
        checkPasswordStrength(password);
        showToast("Generated 12-character high entropy password!", true);
    }

    // Password strength evaluator
    function checkPasswordStrength(val) {
        var fill = $('#pw_strength_fill');
        var text = $('#pw_strength_text');

        if (!val || val.length === 0) {
            fill.removeClass('weak medium strong').css('width', '0%');
            text.text('Minimum 8 characters with letters & numbers').css('color', '#64748b');
            return;
        }

        var score = 0;
        if (val.length >= 8) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        if (score <= 2) {
            fill.removeClass('medium strong').addClass('weak');
            text.text('Weak: add numbers, upper & lowercase letters').css('color', '#ef4444');
        } else if (score <= 4) {
            fill.removeClass('weak strong').addClass('medium');
            text.text('Good password strength').css('color', '#f59e0b');
        } else {
            fill.removeClass('weak medium').addClass('strong');
            text.text('Strong institutional-grade security password').css('color', '#10b981');
        }
    }

    // Interactive Toast Notification
    function showToast(message, isSuccess) {
        var toast = $("#portal_toast");
        toast.html((isSuccess ? '<i class="fas fa-check-circle" style="color:#10b981; font-size:16px;"></i> ' : '<i class="fas fa-exclamation-circle" style="color:#ef4444; font-size:16px;"></i> ') + message);
        toast.css("display", "flex");
        setTimeout(function() {
            toast.fadeOut(400);
        }, 4000);
    }

    // Copy to clipboard helper
    function copyText(elementId, successMsg) {
        var text = $('#' + elementId).text().trim();
        navigator.clipboard.writeText(text).then(function() {
            showToast(successMsg || "Copied to clipboard!", true);
        });
    }

    // Copy Full Credential Package
    function copyFullPackage() {
        var id = $('#voucher_modal_admin_id').text().trim();
        var name = $('#voucher_modal_name').text().trim();
        var email = $('#voucher_modal_email').text().trim();
        var pw = $('#voucher_modal_password').text().trim();
        var role = $('#voucher_modal_role').text().trim();

        var packageText = 
            "=========================================\n" +
            "HOPEHAVEN ORPHANAGE - ADMIN CREDENTIALS\n" +
            "=========================================\n" +
            "AdminID   : " + id + "\n" +
            "Full Name : " + name + "\n" +
            "Email     : " + email + "\n" +
            "Password  : " + pw + "\n" +
            "Scope     : " + role + "\n" +
            "Portal URL: " + window.location.origin + "/dashboard\n" +
            "Authorized: Sister Grace (ADM-2026-001)\n" +
            "=========================================\n" +
            "Note: Enforced 2FA and password change on first login.";

        navigator.clipboard.writeText(packageText).then(function() {
            showToast("Full credentials package copied to clipboard!", true);
        });
    }

    // View Credentials Modal
    function viewCredentialsModal(adminId, name, email, role, authorizer, pw) {
        $('#voucher_modal_admin_id').text(adminId);
        $('#voucher_modal_name').text(name);
        $('#voucher_modal_email').text(email);
        $('#voucher_modal_role').text(role);
        $('#voucher_modal_authorizer').text(authorizer || 'Sister Grace (ADM-2026-001)');

        if (pw) {
            $('#voucher_pw_container').show();
            $('#voucher_modal_password').text(pw);
        } else {
            $('#voucher_pw_container').show();
            $('#voucher_modal_password').text("•••••••••••• [Protected Hash]");
        }

        $('#modal_credentials_voucher').css('display', 'flex');
    }

    function closeVoucherModal() {
        $('#modal_credentials_voucher').fadeOut(200);
    }

    // Form Submission: Admin Provisions Another Admin
    function handleAdminProvision(e) {
        e.preventDefault();

        var fullName = $('#admin_full_name').val().trim();
        var email = $('#admin_email').val().trim();
        var adminId = $('#admin_id').val().trim();
        var password = $('#admin_password').val();
        var passwordConfirm = $('#admin_password_confirmation').val();

        // Validate match
        if (password !== passwordConfirm) {
            showToast("Passwords do not match. Please verify.", false);
            $('#admin_password_confirmation').focus();
            return;
        }

        // Check if AdminID already exists in table
        var duplicate = false;
        $('#admins_tbody tr').each(function() {
            if ($(this).attr('data-admin-id') === adminId) {
                duplicate = true;
            }
        });

        if (duplicate) {
            showToast("Admin ID " + adminId + " is already assigned! Click 'Auto ID' for the next sequence.", false);
            $('#admin_id').focus();
            return;
        }

        var btn = $('#btn_provision_submit');
        var originalBtnHtml = btn.html();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating account ' + adminId + '...');

        setTimeout(function() {
            btn.prop('disabled', false).html(originalBtnHtml);

            // Compute initials
            var initials = fullName.split(' ').map(function(n) { return n[0]; }).join('').substring(0, 2).toUpperCase();

            // Append to table
            var newRow = 
                '<tr class="newly-added" data-admin-id="' + adminId + '" data-name="' + fullName + '" data-email="' + email + '">' +
                    '<td><span class="badge-admin-id"><i class="fas fa-id-badge"></i> ' + adminId + '</span></td>' +
                    '<td>' +
                        '<div class="admin-profile-cell">' +
                            '<div class="admin-avatar" style="background: linear-gradient(135deg, #14b8a6, #0d9488);">' + initials + '</div>' +
                            '<div>' +
                                '<div class="admin-cell-name">' + fullName + '</div>' +
                                '<div class="admin-cell-email">' + selectedDepartment + '</div>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td><code>' + email + '</code></td>' +
                    '<td><span class="badge-role super"><i class="fas fa-user-shield"></i> ' + selectedTierTitle + '</span></td>' +
                    '<td><span style="color: #334155; font-size: 12px; font-weight: 500;">Sister Grace (ADM-001)</span></td>' +
                    '<td><span class="status-indicator active"><span class="status-dot"></span> Active</span></td>' +
                    '<td style="text-align: right;">' +
                        '<button class="table-action-btn" onclick="viewCredentialsModal(\'' + adminId + '\', \'' + fullName + '\', \'' + email + '\', \'' + selectedTierTitle + '\', \'Sister Grace (ADM-001)\', \'' + password + '\')">' +
                            '<i class="fas fa-file-invoice"></i> Voucher' +
                        '</button>' +
                    '</td>' +
                '</tr>';

            $('#admins_tbody').prepend(newRow);

            // Update stats
            nextIdNumber++;
            var totalAdmins = $('#admins_tbody tr').length;
            $('#stat_total_admins').text(totalAdmins + ' Admins');
            $('#admin_count_badge').text(totalAdmins + ' Registered');

            var year = new Date().getFullYear();
            var nextFormatted = "ADM-" + year + "-" + String(nextIdNumber).padStart(3, '0');
            $('#stat_next_id').text(nextFormatted);

            showToast("Success! Administrator " + fullName + " (" + adminId + ") created.", true);

            // Open Voucher Modal with real password
            viewCredentialsModal(adminId, fullName, email, selectedTierTitle, 'Sister Grace (ADM-2026-001)', password);

            // Reset form for next entry & auto-set next ID
            $('#admin_provision_form')[0].reset();
            $('#admin_id').val(nextFormatted);
            $('#pw_strength_fill').removeClass('weak medium strong').css('width', '0%');
            $('#pw_strength_text').text('Minimum 8 characters with letters & numbers').css('color', '#64748b');

        }, 900);
    }

    // Filter directory table
    function filterAdmins(query) {
        var term = (query || '').toLowerCase().trim();
        $('#admins_tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(term) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Close modal on escape key
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            closeVoucherModal();
        }
    });

    // Close modal on background click
    $('#modal_credentials_voucher').click(function(e) {
        if (e.target === this) {
            closeVoucherModal();
        }
    });
</script>
@endpush
