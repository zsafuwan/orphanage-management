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

<!-- Metric KPI Cards -->
<div class="metrics-grid child-care-summary">
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
            <span class="metric-label">Medical Appointment</span>
            <div class="metric-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                <i class="fas fa-stethoscope"></i>
            </div>
        </div>
        <div class="metric-val" id="metric_health_screenings" style="color: #ef4444;">4 Visits</div>
        <div class="metric-sub">Dental & pediatric reviews on roster</div>
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
                        <th>Orphanage</th>
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
    <div class="academic-section-header">
        <div>
            <h3>Academic Performance & Exam Paper Records</h3>
            <p>View schools, attendance and exam results.</p>
        </div>
        <button class="btn-primary btn-sm" onclick="ChildWelfareUI.openKeyPaperModal()"><i class="fas fa-plus"></i> Key in Paper Result</button>
    </div>
    <div id="academic_cards_grid" class="academic-cards-grid" aria-label="Student academic records"></div>
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
                    <label class="form-label">Orphanage</label>
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
                <label class="form-label">Intake & Care Notes</label>
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
                    <label class="form-label" for="paper_attendance_input">School Attendance (days)</label>
                    <input type="number" id="paper_attendance_input" class="form-control" min="0" max="366" step="1" placeholder="Days attended this academic year">
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
                <label class="form-label" for="health_appointment_specialty">Examination Specialty</label>
                <input type="text" id="health_appointment_specialty" class="form-control" placeholder="Enter examination specialty" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Appointment Date</label>
                    <input type="date" class="form-control" value="2026-11-15">
                </div>
                <div class="form-group">
                    <label class="form-label" for="health_appointment_clinic">Clinic</label>
                    <input type="text" id="health_appointment_clinic" class="form-control" placeholder="Enter clinic name" aria-describedby="appointment-clinic-note">
                    <small id="appointment-clinic-note" style="display: block; margin-top: 6px; color: #64748b;">Clinic name is not saved yet.</small>
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
