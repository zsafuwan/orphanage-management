@extends('layouts.portal')

@section('title', 'Director Staff Registration & Account Setup')
@section('breadcrumb', 'Access & Security / Register Staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-register-staff.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/director-register-staff.css')), 0, 12) }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Director Authorizing Hero Banner -->
    <div class="dir-reg-hero">
        <div>
            <div class="dir-reg-hero-badge">
                <i class="fas fa-crown"></i> Director of Staff & Volunteers Management · Statutory Onboarding
            </div>
            <h1 class="dir-reg-hero-title">Register Staff</h1>
            <p class="dir-reg-hero-sub">
                Official employee and caregiver registration under the Child Act 2001 (Act 611). Only the Executive Director possesses the authority to issue permanent orphanage credentials and assign ward custody duties.
            </p>
        </div>
        <div class="dir-authorizer-badge">
            <div class="dir-authorizer-avatar">SS</div>
            <div class="dir-authorizer-meta">
                <label><i class="fas fa-signature"></i> Approving Admin</label>
                <span>Sister Sarah Montgomery</span>
                <div style="font-size: 11px; color: #c7d2fe;">Orphanage Executive Director</div>
            </div>
        </div>
    </div>

    <!-- Director Staff Provisioning Metrics -->
    <div class="dir-reg-metrics">
        <div class="dir-reg-metric-card">
            <div class="dir-reg-metric-icon" style="background:#eef2ff; color:#4f46e5;">
                <i class="fas fa-users-gear"></i>
            </div>
            <div>
                <div class="dir-reg-metric-val">8 Caregivers</div>
                <div class="dir-reg-metric-lbl">Active Orphanage Staff</div>
            </div>
        </div>

        <div class="dir-reg-metric-card">
            <div class="dir-reg-metric-icon" style="background:#e0f2fe; color:#0369a1;">
                <i class="fas fa-fingerprint"></i>
            </div>
            <div>
                <div class="dir-reg-metric-val" style="font-family: monospace;">HH-CARE-09</div>
                <div class="dir-reg-metric-lbl">Next Allocated Staff ID</div>
            </div>
        </div>

        <div class="dir-reg-metric-card">
            <div class="dir-reg-metric-icon" style="background:#ecfdf5; color:#059669;">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div>
                <div class="dir-reg-metric-val">100% Cleared</div>
                <div class="dir-reg-metric-lbl">PDRM Police Background Checks</div>
            </div>
        </div>

        <div class="dir-reg-metric-card">
            <div class="dir-reg-metric-icon" style="background:#f5f3ff; color:#7c3aed;">
                <i class="fas fa-building-user"></i>
            </div>
            <div>
                <div class="dir-reg-metric-val">4 Wings</div>
                <div class="dir-reg-metric-lbl">Orphanage Ward Coverage</div>
            </div>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="dir-reg-grid">
        <!-- Left: Registration Form -->
        <div class="dir-reg-card">
            <div class="dir-reg-card-head">
                <h2><i class="fas fa-user-plus" style="color: #4f46e5;"></i> Staff Registration</h2>
                <span style="font-size: 12px; color: #64748b;">Required fields are marked *</span>
            </div>
            <form class="dir-reg-form" onsubmit="submitStaffRegistration(event)">
                <!-- Staff details -->
                <div class="form-group-grid" style="grid-template-columns: minmax(0, 1fr);">
                    <div class="dir-input-group">
                        <label for="staff_name_input">Full Name *</label>
                        <input type="text" id="staff_name_input" class="dir-input" placeholder="e.g. Nurul Huda Binti Ahmad" required>
                    </div>
                </div>

                <!-- Role and shift -->
                <div class="form-group-grid">
                    <div class="dir-input-group">
                        <label for="staff_role_select">Designation & Responsibilities *</label>
                        <select id="staff_role_select" class="dir-select" required>
                            <option value="lead_caregiver">Lead Clinical Caregiver</option>
                            <option value="infant_nurse">Pediatric Nurse / Infant Care</option>
                            <option value="pastoral_tutor">Education Tutor & Pastoral Mentor</option>
                            <option value="social_worker">Registered Case Social Worker</option>
                            <option value="kitchen_head">Head of Nutrition & Food Safety</option>
                            <option value="custodian">Facilities & Safety Custodian</option>
                        </select>
                    </div>
                    <div class="dir-input-group">
                        <label for="staff_shift_select">Assigned Shift *</label>
                        <select id="staff_shift_select" class="dir-select" required>
                            <option value="" disabled selected>Choose a shift</option>
                            <option value="morning">Morning (08:00–16:00)</option>
                            <option value="evening">Evening (16:00–00:00)</option>
                            <option value="night">Night (00:00–08:00)</option>
                        </select>
                    </div>
                </div>

                <!-- Contact & Staff ID -->
                <div class="form-group-grid">
                    <div class="dir-input-group">
                        <label for="staff_email_input">Official Email Address *</label>
                        <input type="email" id="staff_email_input" class="dir-input" placeholder="e.g. huda.ahmad@hopehaven.org.my" required>
                    </div>
                    <div class="dir-input-group">
                        <label for="staff_phone_input">Contact Mobile Number *</label>
                        <input type="tel" id="staff_phone_input" class="dir-input" placeholder="e.g. +60 17-891 2345" required>
                    </div>
                </div>

                <div class="form-group-grid">
                    <div class="dir-input-group">
                        <label for="staff_id_input">Assigned Staff Access ID</label>
                        <input type="text" id="staff_id_input" class="dir-input" value="HH-CARE-09" style="font-family: monospace; font-weight: 700; background: #f8fafc;" readonly>
                    </div>
                    <div class="dir-input-group">
                        <label for="staff_password_input">Initial Temporary Access Password *</label>
                        <input type="text" id="staff_password_input" class="dir-input" value="Caregiver@2026" style="font-family: monospace;" required>
                    </div>
                </div>


                <!-- Director Authorization Seal Checkbox -->
                <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
                    <input type="checkbox" id="director_authorizer_check" checked style="width: 20px; height: 20px; accent-color: #4f46e5; cursor: pointer;">
                    <label for="director_authorizer_check" style="font-size: 12px; color: #312e81; font-weight: 600; cursor: pointer; line-height: 1.5;">
                        I, <strong>Sister Sarah (Orphanage Director & Sole Signatory)</strong>, authorize this staff member to join orphanage operations.
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="reset" class="btn-tab" style="background:#f1f5f9; border:none; padding:10px 20px; border-radius:6px; cursor:pointer;">Reset</button>
                    <button type="submit" class="btn-sign" style="padding: 10px 22px; font-size: 13px;">
                        <i class="fas fa-stamp"></i> Provision & Issue Staff Credentials
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Staff Badge Preview & Recent List -->
        <div>
            <!-- Live Preview Card -->
            <div class="staff-badge-preview">
                <div class="staff-badge-header">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #fde047; font-weight: 700;">
                            <i class="fas fa-heart"></i> HopeHaven Orphanage Caregiver
                        </div>
                    </div>
                    <div class="staff-badge-id" id="preview_id">HH-CARE-09</div>
                </div>
                <div class="staff-badge-body">
                    <div class="staff-badge-avatar" id="preview_avatar">NC</div>
                    <div>
                        <div class="staff-badge-name" id="preview_name">New Caregiver</div>
                        <div class="staff-badge-role" id="preview_role">Lead Clinical Caregiver</div>
                        <div id="preview_shift" style="font-size: 12px; margin-top: 6px; color: #dbeafe;">Shift not selected</div>

                    </div>
                </div>
                <div class="staff-badge-footer">
                    <span>Authorized by: Sister Sarah</span>
                    <span><i class="fas fa-shield-check"></i> 100% Vetted</span>
                </div>
            </div>

            <!-- Recently Registered Staff by Director -->
            <div class="dir-reg-card">
                <div class="dir-reg-card-head">
                    <h2><i class="fas fa-clock-rotate-left"></i> New Staff & Volunteers</h2>
                    <span style="font-size: 11px; color: #64748b;">Active Payroll</span>
                </div>
                <div id="recent_staff_list">
                    <!-- Staff 1: Nurse Mary -->
                    <div class="recent-staff-item">
                        <div class="user-cell">
                            <div class="user-cell-avatar">NM</div>
                            <div>
                                <div class="user-cell-name">Nurse Mary Anne Joseph</div>
                                <div class="user-cell-dept">Lead Caregiver · HH-CARE-01</div>
                            </div>
                        </div>
                        <span class="vetted-badge"><i class="fas fa-check-circle"></i> On Duty</span>
                    </div>

                    <!-- Staff 2: Caregiver Lisa -->
                    <div class="recent-staff-item">
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#fef3c7; color:#b45309;">CL</div>
                            <div>
                                <div class="user-cell-name">Caregiver Lisa Wong</div>
                                <div class="user-cell-dept">Infant Care · HH-CARE-04</div>
                            </div>
                        </div>
                        <span class="vetted-badge"><i class="fas fa-check-circle"></i> On Duty</span>
                    </div>

                    <!-- Staff 3: Ustaz Hakim -->
                    <div class="recent-staff-item">
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#e0f2fe; color:#0369a1;">UH</div>
                            <div>
                                <div class="user-cell-name">Ustaz Hakim Bin Salleh</div>
                                <div class="user-cell-dept">Study Hall Tutor · HH-EDU-02</div>
                            </div>
                        </div>
                        <span class="vetted-badge"><i class="fas fa-check-circle"></i> On Duty</span>
                    </div>

                    <!-- Staff 4: Chef Razak -->
                    <div class="recent-staff-item">
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:#fce7f3; color:#be185d;">CR</div>
                            <div>
                                <div class="user-cell-name">Chef Razak Bin Kassim</div>
                                <div class="user-cell-dept">Head of Kitchen · HH-KIT-01</div>
                            </div>
                        </div>
                        <span class="vetted-badge"><i class="fas fa-check-circle"></i> On Duty</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modal Partial -->
@include('portal.director.partials.register-staff-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-register-staff.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/director-register-staff.js')), 0, 12) }}"></script>
@endpush
