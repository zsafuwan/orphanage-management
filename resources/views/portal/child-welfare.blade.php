@extends('layouts.portal')

@section('title', 'Child Care & Education Management')
@section('breadcrumb', 'Child Care & Education')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/child-record-viewer.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/child-record-viewer.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/child-welfare.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/child-welfare.css')), 0, 12) }}">
@endpush

@section('content')
@php
    $isStaff = request()->routeIs('dashboard.staff') || request()->routeIs('staff.child-welfare') || request('view') === 'staff' || request('role') === 'staff';
@endphp
<!-- Subsystem Header -->
<div class="page-header-card">
    <div>
        
        <h1 class="page-title">Child Care & Education</h1>
        <p class="page-desc">
            View child profiles, exam results and medical records.
        </p>
    </div>
</div>

<!-- Role-Based Access Control Simulation Banner -->
<div class="rbac-banner">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-user-shield" style="color: #10b981; font-size: 18px;"></i>
        <div>
            <span style="font-size: 12px; font-weight: 700; color: var(--dark); text-transform: uppercase;">Access Level:</span>
            <span style="font-size: 12px; color: var(--dark-muted); margin-left: 4px;">Viewing As: <strong id="current_role_badge" style="color: #10b981;">{{ $isStaff ? 'Staff Caregiver (Nurse Mary)' : 'Administrator' }}</strong></span>
        </div>
    </div>
    <div class="rbac-roles">
        <button class="role-pill {{ !$isStaff ? 'active' : '' }}" onclick="simulateRole('Administrator', this)">Administrator</button>
        <button class="role-pill {{ $isStaff ? 'active' : '' }}" onclick="simulateRole('Staff Caregiver', this)">Staff Caregiver</button>
        <button class="role-pill" onclick="simulateRole('Social Worker', this)">Social Worker</button>
        <button class="role-pill" onclick="simulateRole('Educator', this)">Educator</button>
        <button class="role-pill" onclick="simulateRole('Clinic Nurse', this)">Clinic Nurse</button>
    </div>
</div>

<!-- Metric KPI Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Enrolled Wards</span>
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="metric-val" id="metric_enrolled_count">4 Children</div>
        <div class="metric-sub">Active residential children in registry</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">School Attendance</span>
            <div class="metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="fas fa-school"></i>
            </div>
        </div>
        <div class="metric-val" id="metric_school_attendance">95.8%</div>
        <div class="metric-sub">Average term attendance rate</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Health Screenings Due</span>
            <div class="metric-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-stethoscope"></i>
            </div>
        </div>
        <div class="metric-val" id="metric_health_screenings" style="color: #ef4444;">4 Visits</div>
        <div class="metric-sub">Dental & pediatric reviews on roster</div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-label">Honor Roll</span>
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="fas fa-award"></i>
            </div>
        </div>
        <div class="metric-val" id="metric_honor_roll" style="color: #f59e0b;">2 Scholars</div>
        <div class="metric-sub">GPA &ge; 3.75 distinction standing</div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-container">
    <button class="tab-btn active" data-tab="tab_profiles">
        <i class="fas fa-address-card"></i> Child Profiles & Registry <span id="tab_profiles_counter">(4)</span>
    </button>
    <button class="tab-btn" data-tab="tab_education">
        <i class="fas fa-book-reader"></i> School Enrollment & Paper Results
    </button>
    <button class="tab-btn" data-tab="tab_health">
        <i class="fas fa-heartbeat"></i> Healthcare & Basic Medical Records
    </button>
    <button class="tab-btn" data-tab="tab_history">
        <i class="fas fa-history"></i> Role-Based Child History & PDF
    </button>
</div>

<!-- Search & Dorm Filter Bar -->
<div class="filter-bar">
    <div class="search-box-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" class="portal-input search-input" data-search-target=".searchable-item" placeholder="Search by child name, ID, school, medical condition, or paper result...">
    </div>
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <select class="portal-select category-filter" data-filter-target=".searchable-item">
            <option value="All">All Dormitories</option>
            <option value="St. Clare Cottage">St. Clare Cottage (Girls)</option>
            <option value="St. Francis Boys Hall">St. Francis Boys Hall</option>
        </select>

        <!-- View Mode Switcher -->
        <div class="view-mode-toggle">
            <button class="view-mode-btn active" id="btn_view_cards" title="View as Profile Cards Grid">
                <i class="fas fa-th-large"></i> Cards
            </button>
            <button class="view-mode-btn" id="btn_view_table" title="View as Detailed Children List Table">
                <i class="fas fa-list-ul"></i> Table List
            </button>
        </div>
    </div>
</div>

<!-- TAB 1: CHILD PROFILES & REGISTRY (LIST OF CHILDREN THAT HAS BEEN INSERTED) -->
<div id="tab_profiles" class="tab-pane">
    <!-- View 1: Card Grid -->
    <div class="cards-grid-3" id="profiles_grid">
        <!-- Rendered dynamically by ChildWelfareUI.renderCardsGrid -->
    </div>

    <!-- View 2: Detailed Children Table List (Hidden by default, toggled via switcher) -->
    <div id="profiles_table_wrapper" class="children-registry-table-card" style="display: none;">
        <div class="children-table-header">
            <div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--dark); margin-bottom: 2px;">
                    <i class="fas fa-clipboard-list" style="color: #10b981; margin-right: 6px;"></i> Complete Children Registry List
                </h3>
                <p style="font-size: 12px; color: var(--dark-muted); margin: 0;">
                    Complete record of all inserted children. Staff can key in exam paper results and update basic medical condition records directly.
                </p>
            </div>
            <button class="btn-primary btn-sm" onclick="openModal('modal_register_child')">
                <i class="fas fa-user-plus"></i> Add New Child
            </button>
        </div>
        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Child Ward</th>
                        <th>Dorm Placement</th>
                        <th>School & Class</th>
                        <th>Latest Paper Result</th>
                        <th>Basic Medical Condition</th>
                        <th>Next Clinic Review</th>
                        <th class="action-column" style="text-align: right;">Staff Duty Actions</th>
                    </tr>
                </thead>
                <tbody id="children_table_tbody">
                    <!-- Rendered dynamically by ChildWelfareUI.renderChildrenTable -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 2: SCHOOL ENROLLMENT & PAPER RESULTS -->
<div id="tab_education" class="tab-pane" style="display: none;">
    <div class="portal-card">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark);">Academic Performance & Exam Paper Records</h3>
                <p style="font-size: 12px; color: var(--dark-muted);">
                    View schools, attendance and exam results.
                </p>
            </div>
            <button class="btn-primary btn-sm" onclick="ChildWelfareUI.openKeyPaperModal()">
                <i class="fas fa-plus"></i> Key in Paper Result
            </button>
        </div>

        <div class="table-responsive">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Child / Student</th>
                        <th>School Name & ID</th>
                        <th>Grade Level</th>
                        <th>Attendance %</th>
                        <th>Cumulative GPA</th>
                        <th>Latest Paper & Subject Scores</th>
                        <th>Performance Standing</th>
                        <th class="action-column" style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody id="academics_table_tbody">
                    <!-- Rendered dynamically by ChildWelfareUI.renderAcademicsTab -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 3: HEALTHCARE & BASIC MEDICAL RECORDS -->
<div id="tab_health" class="tab-pane" style="display: none;">
    <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--dark);">Health Records & Follow-ups</h3>
            <p style="font-size: 12px; color: var(--dark-muted);">
                Blood group baselines, chronic conditions, drug/food allergies, daily medications, and scheduled clinic examinations.
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button class="btn-primary btn-sm" onclick="openModal('modal_record_medical')">
                <i class="fas fa-notes-medical"></i> Record Medical Condition
            </button>
            <button class="btn-outline btn-sm" onclick="openModal('modal_health_appointment')">
                <i class="fas fa-calendar-plus"></i> Schedule Clinic Visit
            </button>
        </div>
    </div>

    <div id="healthcare_cards_grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <!-- Rendered dynamically by ChildWelfareUI.renderHealthcareTab -->
    </div>
</div>

<!-- TAB 4: ROLE-BASED CHILD HISTORY & OFFICIAL PDF -->
<div id="tab_history" class="tab-pane" style="display: none;">
    <div class="portal-card" style="padding: 28px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; flex-wrap: wrap; gap: 12px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                    <span style="font-size: 12px; font-weight: 700; color: #64748b;">Select Resident Child:</span>
                    <select id="history_child_select" class="portal-select" style="min-width: 220px; font-weight: 600;">
                        <!-- Options populated dynamically -->
                    </select>
                </div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--dark);" id="history_child_title">Child History</h3>
                <p style="font-size: 12px; color: var(--dark-muted);">Subject to Role-Based Access Control (RBAC). Try switching roles at top of page.</p>
            </div>
            <span class="badge badge-secondary" style="font-family: monospace; font-size: 13px;" id="history_child_id_badge">CH-2024-001</span>
        </div>

        <!-- Role Access Notice -->
        <div class="confidential-dossier" style="margin-bottom: 20px;">
            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid #86efac; padding: 12px; border-radius: 8px; color: #166534; font-size: 12px;">
                <i class="fas fa-shield-alt"></i> <strong>Full Record Access:</strong> Intake trauma baselines, court guardianship certificates, paper results, and medical condition notes visible.
            </div>
        </div>

        <!-- Timeline container -->
        <div id="history_timeline_container" style="position: relative; padding-left: 30px; display: flex; flex-direction: column; gap: 24px; border-left: 2px solid #e2e8f0; margin-left: 10px;">
            <!-- Populated dynamically by ChildWelfareUI.renderHistoryTimeline -->
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODALS FOR CHILD WELFARE OPERATIONS
     ========================================================================== -->

<!-- MODAL 1: REGISTER / ADD NEW CHILD -->
<div id="modal_register_child" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 650px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Register New Child Admission</h3>
                <p class="modal-sub">Add a child ward to orphanage residential care, dorm placement, and medical registry.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_register_child')">&times;</button>
        </div>

        <form onsubmit="handleRegisterNewChildForm(event)">
            @include('portal.partials.child-photo-upload')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" id="reg_child_name" class="form-control" placeholder="e.g. Gabriel Joseph Santos" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Preferred Name</label>
                    <input type="text" id="reg_child_callname" class="form-control" placeholder="e.g. Gabe">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select id="reg_child_gender" class="form-control">
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" id="reg_child_dob" class="form-control" value="2017-06-15" max="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Dormitory Placement</label>
                    <select id="reg_child_dorm" class="form-control">
                        <option value="St. Clare Cottage">St. Clare Cottage (Girls)</option>
                        <option value="St. Francis Boys Hall">St. Francis Boys Hall</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">School Name</label>
                    <input type="text" id="reg_child_school" class="form-control" placeholder="e.g. Hope Junior Academy" value="Hope Junior Academy">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">School Grade / Class Level</label>
                    <input type="text" id="reg_child_grade" class="form-control" placeholder="e.g. Grade 3" value="Grade 3">
                </div>
                <div class="form-group">
                    <label class="form-label">Blood Type</label>
                    <select id="reg_child_blood" class="form-control">
                        <option value="O+">O+ (Positive)</option>
                        <option value="A+">A+ (Positive)</option>
                        <option value="B+">B+ (Positive)</option>
                        <option value="AB+">AB+ (Positive)</option>
                        <option value="O-">O- (Negative)</option>
                        <option value="A-">A- (Negative)</option>
                        <option value="B-">B- (Negative)</option>
                        <option value="AB-">AB- (Negative)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Primary / Basic Medical Condition</label>
                <input type="text" id="reg_child_condition" class="form-control" placeholder="e.g. Healthy, Mild Asthma, Eczema, None">
            </div>

            <div class="form-group">
                <label class="form-label">Known Allergies</label>
                <input type="text" id="reg_child_allergies" class="form-control" placeholder="e.g. Penicillin, Peanuts, Seafood, None known">
            </div>

            <div class="form-group">
                <label class="form-label">Intake Case Worker & Medical Baseline Notes</label>
                <textarea id="reg_child_notes" class="form-control" rows="2" placeholder="Document background, emotional adjustment notes, special care needs..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_register_child')">Cancel</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check"></i> Register Child Profile
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: KEY IN PAPER RESULTS -->
<div id="modal_key_paper_result" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 640px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Key in Child Examination Paper Results</h3>
                <p class="modal-sub">Record term assessment scores, subject breakdown, and teacher appraisals.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_key_paper_result')">&times;</button>
        </div>

        <form onsubmit="handleKeyPaperResultForm(event)">
            <div class="form-group">
                <label class="form-label">Select Resident Child *</label>
                <select id="paper_child_select" class="form-control" required>
                    <!-- Options populated dynamically -->
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Exam / Paper Name *</label>
                    <input type="text" id="paper_exam_name" class="form-control" placeholder="e.g. Mid-Year Examination Paper" value="Term 2 Assessment Paper" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Academic Term / Semester</label>
                    <input type="text" id="paper_term" class="form-control" placeholder="e.g. Term 2 · 2026" value="Term 2 · 2026" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Examination Date</label>
                    <input type="date" id="paper_exam_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Performance Standing</label>
                    <select id="paper_standing_select" class="form-control">
                        <option value="Distinction">Distinction (Honors)</option>
                        <option value="Good" selected>Good Academic Standing</option>
                        <option value="Needs Math Support">Needs Math Support</option>
                        <option value="Needs Reading Support">Needs Reading Support</option>
                        <option value="Satisfactory">Satisfactory</option>
                    </select>
                </div>
            </div>

            <!-- Subject Scores Breakdown -->
            <div style="margin: 16px 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label class="form-label" style="margin: 0;">
                        <i class="fas fa-list-ol"></i> Subject Scores
                    </label>
                    <button type="button" class="btn-outline btn-sm" onclick="ChildWelfareUI.addSubjectRow()">
                        <i class="fas fa-plus"></i> Add Subject
                    </button>
                </div>

                <div id="subjects_container">
                    <!-- Populated dynamically by ChildWelfareUI.resetSubjectRows -->
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: #e0f2fe; padding: 10px 14px; border-radius: 8px; border: 1px solid #bae6fd; margin-top: 10px;">
                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: #0369a1;">Calculated Average Score:</span>
                        <strong id="paper_avg_score_preview" style="font-size: 16px; color: #0284c7; margin-left: 6px;">88%</strong>
                        <input type="hidden" id="paper_avg_score_hidden" value="88">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 12px; font-weight: 600; color: #0369a1;">Suggested GPA:</span>
                        <input type="number" step="0.01" min="0" max="4.0" id="paper_gpa_input" class="form-control" style="width: 75px; height: 32px; padding: 2px 8px; font-weight: 700;" value="3.52">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Teacher & Staff Appraisal Remarks</label>
                <textarea id="paper_remarks_input" class="form-control" rows="2" placeholder="e.g. Demonstrated great effort in mathematics. Needs continuing guidance in English grammar and vocabulary."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_key_paper_result')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #2563eb; border-color: #2563eb;">
                    <i class="fas fa-save"></i> Save Paper Results
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: RECORD BASIC MEDICAL CONDITION -->
<div id="modal_record_medical" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 620px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Update Medical Record</h3>
                <p class="modal-sub">Update conditions, allergies, medicines and follow-ups.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_record_medical')">&times;</button>
        </div>

        <form onsubmit="handleRecordMedicalForm(event)">
            <div class="form-group">
                <label class="form-label">Select Resident Child *</label>
                <select id="med_child_select" class="form-control" required>
                    <!-- Options populated dynamically -->
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Blood Type</label>
                    <select id="med_blood_type" class="form-control">
                        <option value="O+">O+ (Positive)</option>
                        <option value="A+">A+ (Positive)</option>
                        <option value="B+">B+ (Positive)</option>
                        <option value="AB+">AB+ (Positive)</option>
                        <option value="O-">O- (Negative)</option>
                        <option value="A-">A- (Negative)</option>
                        <option value="B-">B- (Negative)</option>
                        <option value="AB-">AB- (Negative)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Immunization Status</label>
                    <select id="med_immunization_select" class="form-control">
                        <option value="Up to Date">Up to Date (All Scheduled Vaccines)</option>
                        <option value="Pending Booster">Pending Booster Dose</option>
                        <option value="Incomplete">Incomplete / Requires Catch-up</option>
                        <option value="Exempt">Medically Exempt</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Medical Condition</label>
                <input type="text" id="med_condition_input" class="form-control" placeholder="e.g. Bronchial Asthma, Eczema, Type 1 Diabetes, Healthy" required>
            </div>

            <div class="form-group">
                <label class="form-label">Known Allergies</label>
                <input type="text" id="med_allergies_input" class="form-control" placeholder="e.g. Penicillin, Peanuts, Dust mites, Shellfish, None known">
            </div>

            <div class="form-group">
                <label class="form-label">Medicines & Dosage</label>
                <input type="text" id="med_prescriptions_input" class="form-control" placeholder="e.g. Salbutamol inhaler 100mcg as needed, Daily multivitamins 5ml">
            </div>

            <div class="form-group">
                <label class="form-label">Next Clinic Visit</label>
                <input type="date" id="med_next_clinic_date" class="form-control" value="2026-11-20" data-open-picker>
            </div>

            <div class="form-group">
                <label class="form-label">Doctor / Clinic</label>
                <input type="text" id="med_doctor_input" class="form-control" placeholder="e.g. Dr. Angela Reyes (Klinik Kesihatan Titiwangsa)" value="Dr. Angela Reyes">
            </div>

            <div class="form-group">
                <label class="form-label">Care Instructions</label>
                <textarea id="med_notes_input" class="form-control" rows="2" placeholder="Document special dietary instructions, physical sports precautions, emergency protocol..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_record_medical')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #059669; border-color: #059669;">
                    <i class="fas fa-check"></i> Save Medical Record
                </button>
            </div>
        </form>
    </div>
</div>

@include('portal.partials.child-record-viewer')

<!-- MODAL 5: SCHEDULE HEALTH APPOINTMENT -->
<div id="modal_health_appointment" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Schedule Health Screening</h3>
                <p class="modal-sub">Book a health appointment.</p>
            </div>
            <button class="modal-close-btn" onclick="closeModal('modal_health_appointment')">&times;</button>
        </div>

        <form onsubmit="event.preventDefault(); closeModal('modal_health_appointment'); showPortalToast('Clinic screening visit booked successfully!', true, 'Appointment Confirmed', 'Clinic Roster');">
            <div class="form-group">
                <label class="form-label">Child Patient</label>
                <select id="health_appointment_child_select" class="form-control">
                    <!-- Populated dynamically -->
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Examination Specialty</label>
                <select class="form-control">
                    <option>Pediatric Dental Screening</option>
                    <option>General Growth & Pediatric Exam</option>
                    <option>Optometry & Vision Screening</option>
                    <option>Asthma Pulmonary Function Review</option>
                    <option>Speech & Articulation Review</option>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Appointment Date</label>
                    <input type="date" class="form-control" value="2026-11-15">
                </div>
                <div class="form-group">
                    <label class="form-label">Doctor / Clinic</label>
                    <input type="text" class="form-control" value="Dr. Angela Reyes">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_health_appointment')">Cancel</button>
                <button type="submit" class="btn-primary">Confirm Booking</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@endpush
