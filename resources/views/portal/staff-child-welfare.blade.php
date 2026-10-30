@extends('layouts.portal')

@section('title', 'Staff Child Welfare & Duty Dashboard')
@section('breadcrumb', 'Workspace | Staff Child Welfare')
@section('hide-workspace-footer', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/dashboard.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/dashboard.css')), 0, 12) }}">
    <link rel="stylesheet" href="{{ asset('css/portal/record-actions.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/record-actions.css')), 0, 12) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')

<!-- Staff Duty Hero Section -->
<section class="adminator-hero portal-dashboard-hero">
    <div class="portal-hero-copy">
    <div class="hero-text">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
            <span class="hero-date" id="heroLiveDate" style="margin-bottom: 0;">
                <i class="far fa-calendar-alt"></i> {{ now()->format('l · F j · Y') }}
            </span>
            <span style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px; border: 1px solid rgba(16, 185, 129, 0.25); display: inline-flex; align-items: center; gap: 5px;">
                <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 0 2px rgba(16,185,129,0.3);"></span>
                Active Duty Staff View
            </span>
        </div>
        <h1 class="hero-title">
            Welcome on duty, <span class="accent-name" style="color: #059669;">Nurse Mary</span> 👋
        </h1>
        <p class="hero-sub">
            <strong>Morning Shift (08:00–16:00)</strong> · <strong>Alpha & Beta Rooms</strong> · <span data-child-count>4</span> children
        </p>
    </div>
    <div class="hero-actions">
        <button class="btn-outline btn-sm" onclick="openStaffModal('modal_handover_note')">
            <i class="fas fa-pen-alt"></i> Handover Log
        </button>
        <button class="btn-primary btn-sm" style="background: #10b981; border-color: #10b981;" onclick="openStaffModal('modal_staff_add_child')">
            <i class="fas fa-user-plus"></i> Add Child Ward
        </button>
    </div>
    </div>
    @include('portal.partials.workspace-art')
</section>
@include('portal.partials.care-shortcuts')


<!-- Prettier Caregiver Shift Notifications & Priority Alerts Banner -->
<section class="staff-duty-notifications" id="staff_notifications_widget">
    <div class="duty-notif-header">
        <div class="duty-notif-title-wrap">
            <div class="duty-notif-icon-bell">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="duty-notif-title">Duty Alerts</span>
                    <span class="duty-notif-badge" id="duty_active_count_badge">
                        <i class="fas fa-circle" style="font-size: 7px;"></i> <span id="duty_badge_num">3</span> Active
                    </span>
                </div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Morning shift · Alpha & Beta
                </div>
            </div>
        </div>

        <div class="duty-notif-filter-pills">
            <button class="duty-filter-btn active" data-alert-filter="all" onclick="filterDutyAlerts('all', this)">All (3)</button>
            <button class="duty-filter-btn" data-alert-filter="clinical" onclick="filterDutyAlerts('clinical', this)">Health (1)</button>
            <button class="duty-filter-btn" data-alert-filter="medication" onclick="filterDutyAlerts('medication', this)">Meds (1)</button>
            <button class="duty-filter-btn" data-alert-filter="supplies" onclick="filterDutyAlerts('supplies', this)">Supplies (1)</button>
        </div>
    </div>

    <div class="duty-cards-grid" id="duty_cards_container">
        <!-- Alert 1: Clinical (Dental Follow-up) -->
        <div class="duty-alert-card alert-clinical" id="duty_card_clinical" data-category="clinical">
            <div>
                <div class="duty-card-top">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(225, 29, 72, 0.12); color: #e11d48; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        <i class="fas fa-tooth"></i>
                    </div>
                    <div>
                        <span class="duty-badge-tag">Escort Needed</span>
                        <div class="duty-alert-title">Aminah: Dental Follow-up</div>
                        <div class="duty-alert-desc">Klinik Kesihatan Titiwangsa · Escort: Sarah · Van: 10:15 AM.</div>
                    </div>
                </div>
            </div>
            <div class="duty-card-footer">
                <span class="duty-alert-time"><i class="fas fa-clock"></i> 10:30 AM Today</span>
                <button class="duty-action-btn-sm" onclick="handleDutyCardAction('duty_card_clinical', 'Aminah dental escort checklist verified!')">
                    <i class="fas fa-check"></i> Check Transport
                </button>
            </div>
        </div>

        <!-- Alert 2: Medication (Asthma Inhaler) -->
        <div class="duty-alert-card alert-medication" id="duty_card_meds" data-category="medication">
            <div>
                <div class="duty-card-top">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(217, 119, 6, 0.12); color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        <i class="fas fa-lungs"></i>
                    </div>
                    <div>
                        <span class="duty-badge-tag">Medicine Check</span>
                        <div class="duty-alert-title">Ahmad: Inhaler Check</div>
                        <div class="duty-alert-desc">Check peak flow before sports. Inhaler: Clinic Cabinet #2. Check spacer is clean.</div>
                    </div>
                </div>
            </div>
            <div class="duty-card-footer">
                <span class="duty-alert-time"><i class="fas fa-clock"></i> 02:00 PM Today</span>
                <button class="duty-action-btn-sm" onclick="handleDutyCardAction('duty_card_meds', 'Inhaler protocol confirmed for Ahmad Daniel!')">
                    <i class="fas fa-check"></i> Confirm
                </button>
            </div>
        </div>

        <!-- Alert 3: Supplies Ready -->
        <div class="duty-alert-card alert-supplies" id="duty_card_supplies" data-category="supplies">
            <div>
                <div class="duty-card-top">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(5, 150, 105, 0.12); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <span class="duty-badge-tag">Stock Ready</span>
                        <div class="duty-alert-title">Alpha Supplies</div>
                        <div class="duty-alert-desc">Wipes, Dettol & formula · Collect at Bay 2.</div>
                    </div>
                </div>
            </div>
            <div class="duty-card-footer">
                <span class="duty-alert-time"><i class="fas fa-clock"></i> Ready 20m ago</span>
                <button class="duty-action-btn-sm" onclick="handleDutyCardAction('duty_card_supplies', 'Supplies marked as received in Ward Alpha!')">
                    <i class="fas fa-check"></i> Collect Stock
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Staff Operational KPI Grid (Caregiving & Daily Shift Metrics) -->
<section class="kpi-grid" aria-label="Key staff duty metrics">
    <!-- Metric 1: Current Shift Timing -->
    <article class="kpi-card c-success">
        <div class="kpi-top">
            <div class="kpi-identity">
                <div class="kpi-icon success">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="kpi-label">Current Shift</div>
            </div>
            <span class="kpi-pill up" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fas fa-circle" style="font-size: 8px;"></i> On Duty
            </span>
        </div>
        <div class="kpi-value">08:00 - 16:00</div>
        <div class="kpi-compare">
            <i class="fas fa-hourglass-half" style="color: #10b981;"></i> 4h 30m elapsed <span class="sep">·</span> Handover at 16:00
        </div>
    </article>

    <!-- Metric 2: Wards in Direct Care -->
    <article class="kpi-card c-primary">
        <div class="kpi-top">
            <div class="kpi-identity">
                <div class="kpi-icon primary">
                    <i class="fas fa-child"></i>
                </div>
                <div class="kpi-label">Children in Care</div>
            </div>
            <span class="kpi-pill info">
                <i class="fas fa-check-circle"></i> 100% Present
            </span>
        </div>
        <div class="kpi-value">4 Children</div>
        <div class="kpi-compare">
            <i class="fas fa-school" style="color: #3b82f6;"></i> <span data-schooling-summary>School and early childhood care</span>
        </div>
    </article>

    <!-- Metric 3: Health & Medication Tasks -->
    <article class="kpi-card c-danger">
        <div class="kpi-top">
            <div class="kpi-identity">
                <div class="kpi-icon danger">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="kpi-label">Medication Tasks</div>
            </div>
            <span class="kpi-pill down" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                <i class="fas fa-bell"></i> 1 Upcoming
            </span>
        </div>
        <div class="kpi-value">2 / 3 Given</div>
        <div class="kpi-compare">
            <i class="fas fa-check" style="color: #10b981;"></i> Morning vitamins done <span class="sep">·</span> 14:00 Inhaler review
        </div>
    </article>

    <!-- Metric 4: Volunteers on Support Roster -->
    <article class="kpi-card c-purple">
        <div class="kpi-top">
            <div class="kpi-identity">
                <div class="kpi-icon purple">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <div class="kpi-label">Active Volunteers</div>
            </div>
            <span class="kpi-pill flat">
                <i class="fas fa-user-check"></i> Checked-In
            </span>
        </div>
        <div class="kpi-value">3 Volunteers</div>
        <div class="kpi-compare">
            <i class="fas fa-book-reader" style="color: #8b5cf6;"></i> Tutoring Math & English <span class="sep">·</span> Room 2
        </div>
    </article>
</section>

<!-- Staff Quick Action Hub -->
<div class="staff-quick-actions-bar">
    <div class="staff-action-card" onclick="openStaffRecordMedModal()">
        <div class="staff-action-icon green">
            <i class="fas fa-stethoscope"></i>
        </div>
        <div>
            <div class="staff-action-title">Record Medical</div>
            <div class="staff-action-sub">Condition, clinic reviews & meds</div>
        </div>
    </div>

    <a href="{{ route('child-welfare', ['view' => 'staff']) }}" class="staff-action-card" style="text-decoration: none; color: inherit;">
        <div class="staff-action-icon blue" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div>
            <div class="staff-action-title">Child Care & Education</div>
            <div class="staff-action-sub">Add children & key in paper / med records</div>
        </div>
    </a>

    <a href="{{ route('staff.inventory') }}" class="staff-action-card" style="text-decoration: none; color: inherit;">
        <div class="staff-action-icon amber">
            <i class="fas fa-boxes"></i>
        </div>
        <div>
            <div class="staff-action-title">Staff Ward Inventory</div>
            <div class="staff-action-sub">Manage cupboard stock & child supplies</div>
        </div>
    </a>

    <div class="staff-action-card" onclick="recordRollCall()">
        <div class="staff-action-icon blue">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div>
            <div class="staff-action-title">Daily Roll Call</div>
            <div class="staff-action-sub">Verify all wards present</div>
        </div>
    </div>

    <div class="staff-action-card" onclick="openStaffModal('modal_handover_note')">
        <div class="staff-action-icon purple">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div>
            <div class="staff-action-title">Shift Handover</div>
            <div class="staff-action-sub">Leave briefing for next shift</div>
        </div>
    </div>
</div>

<!-- Full-width Operational Grid -->
<div class="adminator-grid">
    <!-- Left Column: Today's Routine Schedule + Wards under Direct Care -->
    <div class="col-12">
        <!-- Card 1: Today's Daily Care & Medication Timeline -->
        <section class="admin-card" style="margin-bottom: 24px;">
            <div class="admin-card-head">
                <div class="card-title-wrap">
                    <span class="eyebrow" style="color: #059669;">Today’s Tasks</span>
                    <h2 class="card-title">Today’s Tasks</h2>
                </div>
                <button type="button" class="staff-routine-add-btn" onclick="addNewRoutineTask()">
                    <span class="staff-routine-add-icon" aria-hidden="true"><i class="fas fa-plus"></i></span> Add Routine Task
                </button>
            </div>

            <div class="staff-schedule-list" id="routine_schedule_list">
                <!-- Task 1: Breakfast & Morning Roll Call (Completed) -->
                <div class="staff-schedule-item completed" id="task_item_1">
                    <div class="sched-time">08:00 AM</div>
                    <div class="sched-content">
                        <div class="sched-title">
                            <i class="fas fa-check-circle" style="color: #10b981; margin-right: 4px;"></i>
                            Morning Ward Attendance & Wholesome Breakfast
                        </div>
                        <div class="sched-detail">All 4 children present. Oatmeal, boiled eggs and fresh milk served.</div>
                    </div>
                    <span class="sched-status-badge done">Completed</span>
                </div>

                <!-- Task 2: Pediatric Vitamin Dispensing (Completed) -->
                <div class="staff-schedule-item completed" id="task_item_2">
                    <div class="sched-time">08:45 AM</div>
                    <div class="sched-content">
                        <div class="sched-title">
                            <i class="fas fa-check-circle" style="color: #10b981; margin-right: 4px;"></i>
                            Daily Pediatric Multivitamins & Iron Syrup
                        </div>
                        <div class="sched-detail">Given to Ahmad, Aminah, Kenji, Priya per Dr. Lee prescription.</div>
                    </div>
                    <span class="sched-status-badge done">Completed</span>
                </div>

                <!-- Task 3: School Bus Departure (Completed) -->
                <div class="staff-schedule-item completed" id="task_item_3">
                    <div class="sched-time">09:15 AM</div>
                    <div class="sched-content">
                        <div class="sched-title">
                            <i class="fas fa-check-circle" style="color: #10b981; margin-right: 4px;"></i>
                            School Transport Departure for SK Convent Wards
                        </div>
                        <div class="sched-detail">Kenji Chen escorted safely to orphanage van driver Uncle Lim.</div>
                    </div>
                    <span class="sched-status-badge done">Completed</span>
                </div>

                <!-- Task 4: Dental Clinic Follow-up (In Progress) -->
                <div class="staff-schedule-item in-progress" id="task_item_4">
                    <div class="sched-time">10:30 AM</div>
                    <div class="sched-content">
                        <div class="sched-title">
                            <i class="fas fa-spinner fa-spin" style="color: #d97706; margin-right: 4px;"></i>
                            Pediatric Dental Appointment (Aminah binti Yusuf)
                        </div>
                        <div class="sched-detail">Accompanied by Volunteer Sarah at Klinik Kesihatan Titiwangsa.</div>
                    </div>
                    <button class="sched-status-badge progress" style="border:none; cursor:pointer;" onclick="markTaskDone('task_item_4')">
                        Mark Done
                    </button>
                </div>

                <!-- Task 5: Nutritious Lunch & Rest (Upcoming) -->
                <div class="staff-schedule-item upcoming" id="task_item_5">
                    <div class="sched-time">01:00 PM</div>
                    <div class="sched-content">
                        <div class="sched-title">Balanced Lunch & Afternoon Quiet Rest</div>
                        <div class="sched-detail">Steamed chicken, rice, pumpkin soup and sliced papaya.</div>
                    </div>
                    <button class="sched-status-badge pending" style="border:none; cursor:pointer;" onclick="markTaskDone('task_item_5')">
                        Pending
                    </button>
                </div>

                <!-- Task 6: Afternoon Study & Tuition (Upcoming) -->
                <div class="staff-schedule-item upcoming" id="task_item_6">
                    <div class="sched-time">02:30 PM</div>
                    <div class="sched-content">
                        <div class="sched-title">Afternoon Academic Tutoring & Reading Hour</div>
                        <div class="sched-detail">Volunteer tutors David and Priya conducting English reading circle.</div>
                    </div>
                    <button class="sched-status-badge pending" style="border:none; cursor:pointer;" onclick="markTaskDone('task_item_6')">
                        Pending
                    </button>
                </div>

                <!-- Task 7: Shift Handover & Log Sign-off (Pending) -->
                <div class="staff-schedule-item upcoming" id="task_item_7">
                    <div class="sched-time">04:00 PM</div>
                    <div class="sched-content">
                        <div class="sched-title">Caregiver Shift Handover & Daily PDF Sign-off</div>
                        <div class="sched-detail">Briefing Evening Shift Caregiver Brother Thomas on ward observations.</div>
                    </div>
                    <span class="sched-status-badge pending">Upcoming</span>
                </div>
            </div>
        </section>

        <!-- Card 2: Residential Children Under Direct Care (Ward Status Cards & Staff Registry) -->
        <section class="admin-card residential-actions-panel">
            <div class="admin-card-head" style="flex-wrap: wrap; gap: 12px;">
                <div class="card-title-wrap">
                    <span class="eyebrow" style="color: #059669;">Child Records</span>
                    <h2 class="card-title">Child Records (<span id="staff_wards_count">4</span> Profiles)</h2>
                    <p style="font-size: 12px; color: #64748b; margin-top: 2px;">
                        Insert new children, key in examination paper results, and record basic medical conditions directly on duty.
                    </p>
                </div>
                <div class="residential-toolbar">
                    <button type="button" class="btn-primary btn-sm" onclick="openStaffModal('modal_staff_add_child')">
                        <i class="fas fa-user-plus"></i> Add Child
                    </button>
                    <button type="button" class="btn-outline btn-sm" onclick="openStaffKeyPaperModal()">
                        <i class="fas fa-pen-nib"></i> Key Paper Result
                    </button>
                    <button type="button" class="btn-outline btn-sm" onclick="openStaffRecordMedModal()">
                        <i class="fas fa-stethoscope"></i> Record Medical
                    </button>
                    
                    <!-- View toggle -->
                    <div class="view-mode-toggle" role="group" aria-label="Residential wards display">
                        <button type="button" class="view-mode-btn active" id="btn_staff_view_cards" aria-pressed="true" onclick="toggleStaffWardView('cards')">
                            <i class="fas fa-th-large"></i> Cards
                        </button>
                        <button type="button" class="view-mode-btn" id="btn_staff_view_table" aria-pressed="false" onclick="toggleStaffWardView('table')">
                            <i class="fas fa-list-ul"></i> Table List
                        </button>
                    </div>
                </div>
            </div>

            <!-- View 1: Cards Grid -->
            <div class="staff-ward-grid" id="staff_ward_grid">
                <!-- Rendered dynamically by renderStaffWardRegistry -->
            </div>

            <!-- View 2: Detailed Table List -->
            <div id="staff_ward_table_wrap" style="display: none; margin-top: 14px; overflow-x: auto;">
                <table class="portal-table" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <thead>
                        <tr>
                            <th>Child Ward</th>
                            <th>Dormitory</th>
                            <th>School & Grade</th>
                            <th>Latest Paper Result</th>
                            <th>Basic Medical Condition</th>
                            <th>Next Clinic</th>
                            <th class="action-column" style="text-align: right;">Staff Actions</th>
                        </tr>
                    </thead>
                    <tbody id="staff_ward_table_tbody">
                        <!-- Populated dynamically by renderStaffWardRegistry -->
                    </tbody>
                </table>
            </div>
        </section>
    </div>


</div>

<!-- MODAL 1: LOG VITALS & HEALTH OBSERVATION (PREMIUM REDESIGN) -->
<div id="modal_log_vitals" class="staff-duty-modal-backdrop">
    <div class="staff-duty-modal-card">
        <div class="staff-modal-header clinical">
            <button type="button" class="staff-modal-close-btn" onclick="closeStaffModal('modal_log_vitals')" title="Close">&times;</button>
            <span class="staff-modal-badge">
                <i class="fas fa-stethoscope"></i> Clinical Care Record
            </span>
            <h3 class="staff-modal-title">Record Daily Child Vitals</h3>
            <p class="staff-modal-sub">
                <i class="fas fa-user-nurse"></i> Logged by: <strong>Nurse Mary · Senior Caregiver</strong>
            </p>
        </div>
        <div class="staff-modal-body">
            <form onsubmit="handleLogVitalsSubmit(event)">
                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-child"></i> Select Residential Ward Child *
                    </label>
                    <select id="vitals_child_select" class="staff-input-control" required>
                        <option value="Ahmad Daniel">Ahmad Daniel (Ward Alpha · Room A-102)</option>
                        <option value="Aminah binti Yusuf">Aminah binti Yusuf (Ward Alpha · Room A-104)</option>
                        <option value="Kenji Chen">Kenji Chen (Ward Beta · Room B-201)</option>
                        <option value="Priya Murugan">Priya Murugan (Ward Beta · Room B-203)</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                    <div>
                        <label class="staff-field-label">
                            <i class="fas fa-temperature-high"></i> Body Temp *
                        </label>
                        <div class="staff-input-suffix-wrap">
                            <input type="number" step="0.1" id="vitals_temp" class="staff-input-control" value="36.7" required>
                            <span class="staff-input-suffix">°C</span>
                        </div>
                    </div>
                    <div>
                        <label class="staff-field-label">
                            <i class="fas fa-heartbeat"></i> Heart Rate *
                        </label>
                        <div class="staff-input-suffix-wrap">
                            <input type="number" id="vitals_pulse" class="staff-input-control" value="78" required>
                            <span class="staff-input-suffix">BPM</span>
                        </div>
                    </div>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-smile"></i> General Mood & Nutrition Intake *
                    </label>
                    <select id="vitals_mood" class="staff-input-control" required>
                        <option value="cheerful_normal">🟢 Cheerful & Ate Complete Meal</option>
                        <option value="mild_fatigue">🟡 Mild Fatigue / Needs Afternoon Rest</option>
                        <option value="reduced_appetite">🔴 Reduced Appetite / Active Medical Watch</option>
                    </select>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-notes-medical"></i> Caregiver Clinical Notes
                    </label>
                    <textarea id="vitals_notes" class="staff-textarea-control" placeholder="e.g. Skin clear, hydration normal, active in morning playtime, no wheezing..."></textarea>
                </div>

                <div class="staff-modal-footer-btns">
                    <button type="submit" class="staff-btn-save">
                        <i class="fas fa-check-circle"></i> Save Vitals Record
                    </button>
                    <button type="button" class="staff-btn-cancel" onclick="closeStaffModal('modal_log_vitals')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: HANDOVER NOTE MODAL (PREMIUM REDESIGN) -->
<div id="modal_handover_note" class="staff-duty-modal-backdrop">
    <div class="staff-duty-modal-card">
        <div class="staff-modal-header handover">
            <button type="button" class="staff-modal-close-btn" onclick="closeStaffModal('modal_handover_note')" title="Close">&times;</button>
            <span class="staff-modal-badge">
                <i class="fas fa-exchange-alt"></i> Shift Handover
            </span>
            <h3 class="staff-modal-title">Post Shift Handover Briefing</h3>
            <p class="staff-modal-sub">
                <i class="fas fa-clipboard-list"></i> Visible to oncoming shift caregivers and supervisors
            </p>
        </div>
        <div class="staff-modal-body">
            <form onsubmit="handleHandoverModalSubmit(event)">
                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-clock"></i> Shift Identification
                    </label>
                    <input type="text" class="staff-input-control" value="Morning Shift (08:00 - 16:00) · Lead Caregiver" readonly style="background: #f1f5f9; color: #475569;">
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-pen"></i> Handover Summary & Ward Status *
                    </label>
                    <textarea id="modal_handover_text" class="staff-textarea-control" style="height: 110px;" placeholder="Document child behaviors, medicine given, scheduled clinic visits, and items for next caregiver..." required></textarea>
                </div>

                <div class="staff-modal-footer-btns">
                    <button type="submit" class="staff-btn-save handover">
                        <i class="fas fa-paper-plane"></i> Publish to Shift Board
                    </button>
                    <button type="button" class="staff-btn-cancel" onclick="closeStaffModal('modal_handover_note')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 3: STOREROOM SUPPLY REQUEST (PREMIUM REDESIGN) -->
<div id="modal_storeroom_request" class="staff-duty-modal-backdrop">
    <div class="staff-duty-modal-card">
        <div class="staff-modal-header supplies">
            <button type="button" class="staff-modal-close-btn" onclick="closeStaffModal('modal_storeroom_request')" title="Close">&times;</button>
            <span class="staff-modal-badge">
                <i class="fas fa-boxes"></i> Inventory Dispatch
            </span>
            <h3 class="staff-modal-title">Caregiver Supply Request</h3>
            <p class="staff-modal-sub">
                <i class="fas fa-paper-plane"></i> Request submitted to Sister Grace and Storeroom Manager
            </p>
        </div>
        <div class="staff-modal-body">
            <form onsubmit="event.preventDefault(); closeStaffModal('modal_storeroom_request'); notifyStaff('Request Sent', 'Urgent supplies request sent to Storeroom Manager!', 'warning', 'fas fa-dolly');">
                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-tag"></i> Item Category
                    </label>
                    <select class="staff-input-control">
                        <option>Pediatric Medical & First Aid</option>
                        <option>Baby Hygiene & Nursery Supplies</option>
                        <option>Educational Books & Stationery</option>
                        <option>Nutritional Food & Milk Formula</option>
                    </select>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-box"></i> Item Description & Quantity *
                    </label>
                    <input type="text" class="staff-input-control" placeholder="e.g. 3 packs of baby wipes, 2 bottles Dettol" required>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">
                        <i class="fas fa-exclamation-circle"></i> Priority
                    </label>
                    <select class="staff-input-control">
                        <option>Normal (Deliver during regular 15:00 dispatch)</option>
                        <option>Urgent (Required within 1 hour)</option>
                    </select>
                </div>

                <div class="staff-modal-footer-btns">
                    <button type="submit" class="staff-btn-save supplies">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                    <button type="button" class="staff-btn-cancel" onclick="closeStaffModal('modal_storeroom_request')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================================================
     STAFF-SPECIFIC MODALS FOR CHILD WELFARE & EDUCATION (NO ADMIN REDIRECT)
     ========================================================================== -->

<!-- STAFF MODAL 1: ADD NEW CHILD WARD (CAREGIVER INTAKE) -->
<div class="staff-duty-modal-backdrop" id="modal_staff_add_child">
    <div class="staff-duty-modal-card" style="max-width: 620px;">
        <div class="staff-modal-header intake">
            <button type="button" class="staff-modal-close-btn" aria-label="Close child admission form" onclick="closeStaffModal('modal_staff_add_child')">&times;</button>
            <span class="staff-modal-badge">
                <i class="fas fa-user-plus"></i> Staff Caregiver Intake Entry
            </span>
            <h3 class="staff-modal-title">Add a Child</h3>
            <p class="staff-modal-sub">
                <i class="fas fa-user-nurse"></i> Logged by Nurse Mary · Morning Shift (Ward Alpha & Beta)
            </p>
        </div>
        <div class="staff-modal-body" style="max-height: 80vh; overflow-y: auto;">
            <form onsubmit="handleStaffAddChildSubmit(event)">
                @include('portal.partials.child-photo-upload')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="staff-field-group">
                        <label class="staff-field-label">Full Name *</label>
                        <input type="text" id="staff_add_name" class="staff-input-control" placeholder="e.g. Gabriel Joseph Santos" required>
                    </div>
                    <div class="staff-field-group">
                        <label class="staff-field-label">Preferred Name</label>
                        <input type="text" id="staff_add_callname" class="staff-input-control" placeholder="e.g. Gabe">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="staff-field-group">
                        <label class="staff-field-label">Gender</label>
                        <select id="staff_add_gender" class="staff-input-control">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="staff-field-group">
                        <label class="staff-field-label">Date of Birth</label>
                        <input type="date" id="staff_add_dob" class="staff-input-control" value="2017-06-15" max="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="staff-field-group">
                        <label class="staff-field-label">Dormitory Placement</label>
                        <select id="staff_add_dorm" class="staff-input-control">
                            <option value="Ward Alpha">Ward Alpha (Ground Floor)</option>
                            <option value="Ward Beta">Ward Beta (First Floor)</option>
                            <option value="St. Clare Cottage">St. Clare Cottage (Girls)</option>
                            <option value="St. Francis Boys Hall">St. Francis Boys Hall</option>
                        </select>
                    </div>
                    <div class="staff-field-group">
                        <label class="staff-field-label">School Name</label>
                        <input type="text" id="staff_add_school" class="staff-input-control" value="Hope Junior Academy" placeholder="School Name">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="staff-field-group">
                        <label class="staff-field-label">Grade / Class Level</label>
                        <input type="text" id="staff_add_grade" class="staff-input-control" value="Grade 3" placeholder="e.g. Grade 3">
                    </div>
                    <div class="staff-field-group">
                        <label class="staff-field-label">Blood Type</label>
                        <select id="staff_add_blood" class="staff-input-control">
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

                <div class="staff-field-group">
                    <label class="staff-field-label">Primary Medical Condition / Baseline</label>
                    <input type="text" id="staff_add_condition" class="staff-input-control" placeholder="e.g. Healthy, Mild Asthma, Eczema">
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Known Allergies</label>
                    <input type="text" id="staff_add_allergies" class="staff-input-control" placeholder="e.g. Peanut allergy, Penicillin, None">
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Caregiver Duty Intake Notes</label>
                    <textarea id="staff_add_notes" class="staff-input-control" rows="2" placeholder="Dietary instructions, daily routine notes..."></textarea>
                </div>

                <div class="staff-modal-footer-btns">
                    <button type="submit" class="staff-btn-save clinical" style="background: #0d9488;">
                        <i class="fas fa-check"></i> Register Child As Staff
                    </button>
                    <button type="button" class="staff-btn-cancel" onclick="closeStaffModal('modal_staff_add_child')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- STAFF MODAL 2: KEY IN EXAMINATION PAPER RESULTS -->
<div class="staff-duty-modal-backdrop" id="modal_staff_key_paper">
    <div class="staff-duty-modal-card" style="max-width: 620px;">
        <div class="staff-modal-header academic">
            <button type="button" class="staff-modal-close-btn" onclick="closeStaffModal('modal_staff_key_paper')">&times;</button>
            <span class="staff-modal-badge">
                <i class="fas fa-graduation-cap"></i> Staff Academic Entry
            </span>
            <h3 class="staff-modal-title">Enter Exam Results</h3>
            <p class="staff-modal-sub">
                <i class="fas fa-user-edit"></i> Keyed in by Nurse Mary · Staff Educational Care
            </p>
        </div>
        <div class="staff-modal-body" style="max-height: 80vh; overflow-y: auto;">
            <form onsubmit="handleStaffPaperSubmit(event)">
                <div class="staff-field-group">
                    <label class="staff-field-label">Select Child *</label>
                    <select id="staff_paper_child_select" class="staff-input-control" required>
                        <!-- Populated dynamically -->
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="staff-field-group">
                        <label class="staff-field-label">Exam / Paper Name *</label>
                        <input type="text" id="staff_paper_exam_name" class="staff-input-control" value="Term 2 Assessment Paper" placeholder="Exam Name" required>
                    </div>
                    <div class="staff-field-group">
                        <label class="staff-field-label">Academic Term</label>
                        <input type="text" id="staff_paper_term" class="staff-input-control" value="Term 2 · 2026" placeholder="Term">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="staff-field-group">
                        <label class="staff-field-label">Examination Date</label>
                        <input type="date" id="staff_paper_date" class="staff-input-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="staff-field-group">
                        <label class="staff-field-label">Standing Status</label>
                        <select id="staff_paper_standing" class="staff-input-control">
                            <option value="Distinction">Distinction (Honors)</option>
                            <option value="Good" selected>Good Academic Standing</option>
                            <option value="Needs Math Support">Needs Math Support</option>
                            <option value="Needs Reading Support">Needs Reading Support</option>
                        </select>
                    </div>
                </div>

                <!-- Subject scores -->
                <div style="margin: 16px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <label class="staff-field-label" style="margin: 0;">
                            <i class="fas fa-list-ol"></i> Subject Scores
                        </label>
                        <button type="button" class="staff-add-subject-btn" onclick="addStaffSubjectRow()">
                            <span class="staff-add-subject-icon" aria-hidden="true"><i class="fas fa-plus"></i></span> Add Subject
                        </button>
                    </div>

                    <div id="staff_subjects_container">
                        <!-- Populated dynamically -->
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; background: #eff6ff; padding: 10px 14px; border-radius: 8px; border: 1px solid #bfdbfe; margin-top: 10px;">
                        <div>
                            <span style="font-size: 12px; font-weight: 700; color: #1e40af;">Calculated Average:</span>
                            <strong id="staff_paper_avg_preview" style="font-size: 16px; color: #2563eb; margin-left: 6px;">87%</strong>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 12px; font-weight: 600; color: #1e40af;">GPA:</span>
                            <input type="number" step="0.01" min="0" max="4.0" id="staff_paper_gpa" class="staff-input-control" style="width: 75px; height: 32px; padding: 2px 8px; font-weight: 700;" value="3.48">
                        </div>
                    </div>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Staff Appraisal Remarks</label>
                    <textarea id="staff_paper_remarks" class="staff-input-control" rows="2" placeholder="Keyed in by Nurse Mary. Good progress in coursework..."></textarea>
                </div>

                <div class="staff-modal-footer-btns">
                    <button type="submit" class="staff-btn-save" style="background: #2563eb;">
                        <i class="fas fa-save"></i> Save Paper Results
                    </button>
                    <button type="button" class="staff-btn-cancel" onclick="closeStaffModal('modal_staff_key_paper')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- STAFF MODAL 3: RECORD BASIC MEDICAL CONDITION -->
<div class="staff-duty-modal-backdrop" id="modal_staff_record_med">
    <div class="staff-duty-modal-card" style="max-width: 620px;">
        <div class="staff-modal-header clinical">
            <button type="button" class="staff-modal-close-btn" onclick="closeStaffModal('modal_staff_record_med')">&times;</button>
            <span class="staff-modal-badge">
                <i class="fas fa-stethoscope"></i> Staff Clinical Care Entry
            </span>
            <h3 class="staff-modal-title">Record Child Medical Condition</h3>
            <p class="staff-modal-sub">
                <i class="fas fa-heartbeat"></i> Recorded by Nurse Mary · Clinical Caregiver on Duty
            </p>
        </div>
        <div class="staff-modal-body" style="max-height: 80vh; overflow-y: auto;">
            <form onsubmit="handleStaffMedSubmit(event)">
                <div class="staff-field-group">
                    <label class="staff-field-label">Select Resident Child *</label>
                    <select id="staff_med_child_select" class="staff-input-control" required>
                        <!-- Populated dynamically -->
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="staff-field-group">
                        <label class="staff-field-label">Blood Type</label>
                        <select id="staff_med_blood" class="staff-input-control">
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
                    <div class="staff-field-group">
                        <label class="staff-field-label">Immunization Status</label>
                        <select id="staff_med_immunization" class="staff-input-control">
                            <option value="Up to Date">Up to Date (All Scheduled Vaccines)</option>
                            <option value="Pending Booster">Pending Booster</option>
                            <option value="Incomplete">Incomplete / Needs Follow-up</option>
                        </select>
                    </div>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Medical Condition *</label>
                    <input type="text" id="staff_med_condition" class="staff-input-control" placeholder="e.g. Bronchial Asthma, Eczema, Fit & Healthy" required>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Known Allergies</label>
                    <input type="text" id="staff_med_allergies" class="staff-input-control" placeholder="e.g. Penicillin, Peanuts, Dust mites, None">
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Daily Prescriptions & Dosage</label>
                    <input type="text" id="staff_med_prescriptions" class="staff-input-control" placeholder="e.g. Salbutamol inhaler 100mcg as needed, Daily vitamins">
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Next Scheduled Clinic Review</label>
                    <input type="date" id="staff_med_clinic_date" class="staff-input-control" value="2026-11-20" data-open-picker>
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Attending Doctor / Clinic</label>
                    <input type="text" id="staff_med_doctor" class="staff-input-control" value="Dr. Angela Reyes (Klinik Kesihatan Titiwangsa)">
                </div>

                <div class="staff-field-group">
                    <label class="staff-field-label">Staff Caregiver on Duty Instructions</label>
                    <textarea id="staff_med_notes" class="staff-input-control" rows="2" placeholder="Specific care instructions for shift staff..."></textarea>
                </div>

                <div class="staff-modal-footer-btns">
                    <button type="submit" class="staff-btn-save clinical">
                        <i class="fas fa-check"></i> Save Medical Record As Staff
                    </button>
                    <button type="button" class="staff-btn-cancel" onclick="closeStaffModal('modal_staff_record_med')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- STAFF MODAL 4: RESIDENT CHILD PDF -->
<div class="staff-duty-modal-backdrop" id="modal_staff_child_dossier">
    <div class="staff-duty-modal-card" style="max-width: 660px;">
        <div class="staff-modal-header handover">
            <button type="button" class="staff-modal-close-btn" onclick="closeStaffModal('modal_staff_child_dossier')">&times;</button>
            <span class="staff-modal-badge">
                <i class="fas fa-id-card"></i> Official Ward PDF
            </span>
            <h3 class="staff-modal-title" id="staff_dossier_title">Child Record</h3>
            <p class="staff-modal-sub" id="staff_dossier_sub">
                Inspected by Nurse Mary · Staff Shift Access
            </p>
        </div>
        <div class="staff-modal-body" style="max-height: 80vh; overflow-y: auto;">
            <div style="display: flex; gap: 16px; align-items: center; background: #f8fafc; padding: 14px; border-radius: 12px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
                <img id="staff_dossier_avatar" src="{{ asset('favicon.svg') }}" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover; border: 2px solid #cbd5e1;" alt="Child portrait">
                <div>
                    <h4 style="font-size: 16px; font-weight: 800; color: #0f172a;" id="staff_dossier_name">Name</h4>
                    <div style="font-size: 12px; color: #64748b;" id="staff_dossier_meta">Age · Dorm</div>
                </div>
            </div>

            <!-- Tab Buttons -->
            <div class="staff-dossier-tabs" role="group" aria-label="Child record sections">
                <button type="button" class="dossier-tab-btn active" id="btn_staff_dos_tab_acad" aria-pressed="true" aria-controls="staff_dossier_pane_acad" onclick="switchStaffDossierTab('acad')">
                    <span class="staff-dossier-tab-icon" aria-hidden="true"><i class="fas fa-graduation-cap"></i></span> Paper Results History
                </button>
                <button type="button" class="dossier-tab-btn" id="btn_staff_dos_tab_med" aria-pressed="false" aria-controls="staff_dossier_pane_med" onclick="switchStaffDossierTab('med')">
                    <span class="staff-dossier-tab-icon" aria-hidden="true"><i class="fas fa-heartbeat"></i></span> Medical Condition & Notes
                </button>
            </div>

            <div id="staff_dossier_pane_acad">
                <div id="staff_dossier_paper_history">
                    <!-- Populated dynamically -->
                </div>
            </div>

            <div id="staff_dossier_pane_med" style="display: none;">
                <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 12.5px;">
                    <div style="margin-bottom: 8px;"><strong>Primary Condition:</strong> <span id="staff_dossier_med_cond">Healthy</span></div>
                    <div style="margin-bottom: 8px;"><strong>Allergies:</strong> <span id="staff_dossier_med_allergies">None</span></div>
                    <div style="margin-bottom: 8px;"><strong>Prescriptions:</strong> <span id="staff_dossier_med_presc">None</span></div>
                    <div style="margin-bottom: 8px;"><strong>Next Review:</strong> <span id="staff_dossier_med_review">None</span></div>
                    <div><strong>Caregiver Instructions:</strong> <span id="staff_dossier_med_notes" style="font-style: italic;">Standard care</span></div>
                </div>
            </div>

            <div class="staff-modal-footer-btns" style="margin-top: 18px;">
                <button type="button" class="staff-btn-cancel" onclick="closeStaffModal('modal_staff_child_dossier')">
                    Close PDF
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Pretty Toast & Duty Alert Feedback
    function notifyStaff(title, message, type, icon) {
        showPrettyNotification({
            title: title || 'Staff Duty Notice',
            message: message,
            type: type || 'success',
            icon: icon,
            meta: 'Nurse Mary · Morning Shift'
        });
    }

    // Modal helpers
    function openStaffModal(modalId) {
        $('#' + modalId).css('display', 'flex').hide().fadeIn(200);
    }

    function closeStaffModal(modalId) {
        $('#' + modalId).fadeOut(200);
    }

    // Filter Duty Alert Cards
    function filterDutyAlerts(category, btn) {
        $('.duty-filter-btn').removeClass('active');
        $(btn).addClass('active');

        if (category === 'all') {
            $('.duty-alert-card').fadeIn(200);
        } else {
            $('.duty-alert-card').hide();
            $('.duty-alert-card[data-category="' + category + '"]').fadeIn(200);
        }
    }

    // Handle Action on a Duty Card
    function handleDutyCardAction(cardId, feedbackMsg) {
        window.HopeHavenDuty.dismiss(cardId);
        var card = $('#' + cardId);
        card.css({
            'opacity': '0.5',
            'transform': 'scale(0.97)',
            'pointer-events': 'none'
        });

        setTimeout(function() {
            card.slideUp(250, function() {
                $(this).remove();
                updateDutyActiveCount();
            });
        }, 300);

        notifyStaff('Duty Task Confirmd', feedbackMsg, 'success', 'fas fa-check-double');
    }

    function updateDutyActiveCount() {
        var count = $('#duty_cards_container .duty-alert-card').length;
        $('#duty_badge_num').text(count);
        if (count === 0) {
            $('#duty_active_count_badge').html('<i class="fas fa-check-circle" style="color: #10b981;"></i> All Clear').css({
                'background': '#ecfdf5',
                'color': '#059669',
                'border-color': '#a7f3d0'
            });
            $('#duty_cards_container').html(`
                <div style="grid-column: span 3; text-align: center; padding: 24px; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 12px; color: #64748b;">
                    <i class="fas fa-check-circle" style="color: #10b981; font-size: 28px; margin-bottom: 8px; display: inline-block;"></i>
                    <div style="font-size: 13.5px; font-weight: 700; color: #0f172a;">All Alerts Done</div>
                    <div style="font-size: 12px; margin-top: 2px;">No urgent medical or logistical alerts pending for this shift.</div>
                </div>
            `);
        }
    }

    // Mark task done
    function markTaskDone(taskId) {
        window.HopeHavenDuty.complete(taskId);
        var task = $('#' + taskId);
        task.removeClass('in-progress upcoming').addClass('completed');
        task.find('.sched-status-badge')
            .removeClass('progress pending')
            .addClass('done')
            .text('Completed')
            .prop('onclick', null);
        task.find('.sched-title i').removeClass('fa-spinner fa-spin').addClass('fa-check-circle').css('color', '#10b981');
        
        var taskTitle = task.find('.sched-title').text().trim();
        notifyStaff('Routine Task Completed', taskTitle, 'success', 'fas fa-calendar-check');
    }

    // Add routine task
    function addNewRoutineTask() {
        var title = prompt("Enter new daily routine task:");
        if (title && title.trim()) {
            var time = prompt("Enter scheduled time (e.g. 03:15 PM):", "03:00 PM");
            var newId = 'task_custom_' + Date.now();
            var html = 
                '<div class="staff-schedule-item upcoming" id="' + newId + '">' +
                    '<div class="sched-time">' + window.HopeHavenSync.escape(time || '03:00 PM') + '</div>' +
                    '<div class="sched-content">' +
                        '<div class="sched-title">' + window.HopeHavenSync.escape(title) + '</div>' +
                        '<div class="sched-detail">Added by Nurse Mary · Morning Shift</div>' +
                    '</div>' +
                    '<button class="sched-status-badge pending" style="border:none; cursor:pointer;" onclick="markTaskDone(\'' + newId + '\')">Pending</button>' +
                '</div>';
            $('#routine_schedule_list').append(html);
            window.HopeHavenDuty.add({id:newId,title:title.trim(),time:time||'03:00 PM'});
            notifyStaff('Timetable Updated', 'Added "' + title + '" to today\'s caregiving schedule', 'info', 'fas fa-plus-circle');
        }
    }

    // Roll call confirmation
    function recordRollCall() {
        var count = window.HopeHavenChildStore ? window.HopeHavenChildStore.getAll().length : 4;
        notifyStaff('Roll Call Complete', 'All ' + count + ' resident children verified physically present in children in Care.', 'success', 'fas fa-user-check');
    }

    // Quick log child vitals
    function quickLogChild(childName) {
        $('#vitals_child_select').val(childName);
        openStaffModal('modal_log_vitals');
    }

    // Handle Vitals Submit
    function handleLogVitalsSubmit(e) {
        e.preventDefault();
        var child = $('#vitals_child_select').val();
        var temp = $('#vitals_temp').val();
        var pulse = $('#vitals_pulse').val();
        closeStaffModal('modal_log_vitals');
        
        notifyStaff(
            'Clinical Vitals Recorded', 
            child + ': Temperature ' + temp + '°C · Pulse ' + pulse + ' BPM recorded into medical PDF by Nurse Mary.', 
            'clinical', 
            'fas fa-heartbeat'
        );
    }

    // Handle Quick Supply Form
    function handleQuickSupplySubmit(e) {
        e.preventDefault();
        var item = $('#quick_supply_item option:selected').text();
        var qty = $('#quick_supply_qty').val();
        var dest = $('#quick_supply_ward').val();
        
        notifyStaff(
            'Request Dispatched',
            qty + 'x ' + item + ' dispatched to Storeroom Bay for ' + dest, 
            'warning', 
            'fas fa-dolly'
        );
    }

    // Post Quick Handover Note
    function postQuickHandoverNote() {
        var text = $('#quick_handover_input').val().trim();
        if (!text) return;

        var nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        var html = 
            '<div class="handover-box" style="border-left: 3px solid #10b981; background: #f0fdf4;">' +
                '<div class="handover-author">' +
                    '<span class="handover-author-name">' +
                        '<i class="fas fa-user-circle" style="color: #10b981;"></i> Nurse Mary (Active)' +
                    '</span>' +
                    '<span class="handover-time">' + nowTime + '</span>' +
                '</div>' +
                '<div class="handover-text">"' + text + '"</div>' +
            '</div>';

        $('#handover_notes_container').prepend(html);
        $('#quick_handover_input').val('');
        
        notifyStaff(
            'Handover Log Published', 
            'Duty note recorded on live bulletin board for incoming staff.', 
            'success', 
            'fas fa-pen-alt'
        );
    }

    function handleHandoverModalSubmit(e) {
        e.preventDefault();
        var text = $('#modal_handover_text').val().trim();
        if (!text) return;

        var nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        var html = 
            '<div class="handover-box" style="border-left: 3px solid #6d28d9; background: #faf5ff;">' +
                '<div class="handover-author">' +
                    '<span class="handover-author-name">' +
                        '<i class="fas fa-user-circle" style="color: #6d28d9;"></i> Nurse Mary' +
                    '</span>' +
                    '<span class="handover-time">' + nowTime + '</span>' +
                '</div>' +
                '<div class="handover-text">"' + text + '"</div>' +
            '</div>';

        $('#handover_notes_container').prepend(html);
        closeStaffModal('modal_handover_note');
        $('#modal_handover_text').val('');
        
        notifyStaff(
            'Shift Handover Filed', 
            'Official handover briefing logged and archived in orphanage logs.',
            'info', 
            'fas fa-clipboard-check'
        );
    }

    // ==========================================================================
    // STAFF CHILD WELFARE & EDUCATION OPERATIONS LOGIC (DIRECT ON STAFF PAGE)
    // ==========================================================================

    function toggleStaffWardView(mode) {
        $('#btn_staff_view_cards').attr('aria-pressed', mode === 'cards' ? 'true' : 'false');
        $('#btn_staff_view_table').attr('aria-pressed', mode === 'table' ? 'true' : 'false');
        if (mode === 'cards') {
            $('#btn_staff_view_cards').addClass('active');
            $('#btn_staff_view_table').removeClass('active');
            $('#staff_ward_grid').fadeIn(200);
            $('#staff_ward_table_wrap').hide();
        } else {
            $('#btn_staff_view_cards').removeClass('active');
            $('#btn_staff_view_table').addClass('active');
            $('#staff_ward_grid').hide();
            $('#staff_ward_table_wrap').fadeIn(200);
        }
    }

    function renderStaffWardRegistry() {
        if (!window.HopeHavenChildStore) return;
        var children = window.HopeHavenChildStore.getAll();
        if (!children) return;

        $('#staff_wards_count').text(children.length);
        children = children.map(child => window.HopeHavenSync.escapeRecord(child));

        // 1. Render Cards Grid
        var cardsHtml = '';
        children.forEach(function(c) {
            var med = c.basicMedical || {};
            var acad = c.academic || {};
            var latestPaper = (acad.paperResults && acad.paperResults.length) ? acad.paperResults[0] : null;

            var medConditionText = med.condition || 'Healthy';
            var medTag = medConditionText.toLowerCase().includes('healthy') 
                ? '<span class="ward-tag good-tag"><i class="fas fa-check"></i> ' + medConditionText + '</span>'
                : '<span class="ward-tag alert-tag"><i class="fas fa-heartbeat"></i> ' + (medConditionText.length > 28 ? medConditionText.substring(0, 28) + '...' : medConditionText) + '</span>';

            var paperBadge = latestPaper 
                ? '<span class="badge badge-info" style="font-size: 11px;"><i class="fas fa-file-alt"></i> ' + latestPaper.overallScore + '% (' + latestPaper.status + ')</span>'
                : '<span style="font-size: 11px; color: #94a3b8; font-style: italic;">No exam paper recorded</span>';

            cardsHtml += `
                <div class="staff-ward-card">
                    <div>
                        <div class="ward-card-header">
                            <img src="${c.avatar}" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #cbd5e1;" alt="${c.name}">
                            <div>
                                <div class="ward-name">${c.name} (${c.age} yrs)</div>
                                <div class="ward-room"><i class="fas fa-door-open"></i> ${c.dorm} · ${c.room || 'Main Ward'}</div>
                            </div>
                        </div>
                        <div style="font-size: 11.5px; color: #475569; margin-bottom: 8px;">
                            <div><strong>School:</strong> ${c.school} (${c.grade})</div>
                            <div style="margin-top: 3px;"><strong>Latest Paper:</strong> ${paperBadge}</div>
                        </div>
                    </div>
                    <div>
                        ${medTag}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #e2e8f0; gap: 4px; flex-wrap: wrap;">
                            <span style="font-size: 11px; color: #64748b;">Blood: <strong>${med.bloodType || 'O+'}</strong></span>
                            <div class="care-record-actions">
                                <button class="table-action-btn" onclick="openStaffKeyPaperModal('${c.id}')" title="Key in Paper Result" style="color: #2563eb;">
                                    <i class="fas fa-pen-nib"></i> Key Paper
                                </button>
                                <button class="table-action-btn" onclick="openStaffRecordMedModal('${c.id}')" title="Record Medical Condition" style="color: #059669;">
                                    <i class="fas fa-stethoscope"></i> Medical
                                </button>
                                <button class="table-action-btn" onclick="openStaffDossierModal('${c.id}')" title="View PDF">
                                    <i class="fas fa-id-card"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#staff_ward_grid').html(cardsHtml);

        // 2. Render Table List
        var tableHtml = '';
        children.forEach(function(c) {
            var med = c.basicMedical || {};
            var acad = c.academic || {};
            var latestPaper = (acad.paperResults && acad.paperResults.length) ? acad.paperResults[0] : null;

            tableHtml += `
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="${c.avatar}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;" alt="${c.name}">
                            <div>
                                <strong style="font-size: 13px; color: #0f172a;">${c.name}</strong>
                                <div style="font-size: 11px; color: #64748b;">"${c.preferredName}" · ${c.age} yrs · ${c.gender} · <span style="font-family: monospace;">${c.id}</span></div>
                            </div>
                        </div>
                    </td>
                    <td><strong>${c.dorm}</strong></td>
                    <td>${c.school} <span class="badge badge-secondary">${c.grade}</span></td>
                    <td>
                        ${latestPaper ? '<strong style="color: #2563eb;">' + latestPaper.overallScore + '%</strong> <span class="badge badge-info">' + latestPaper.status + '</span>' : '<span style="color:#94a3b8; font-style:italic;">None</span>'}
                    </td>
                    <td>
                        <span class="badge badge-secondary">${med.bloodType || 'O+'}</span> <strong>${med.condition || 'Healthy'}</strong>
                        <div style="font-size: 11px; color: #64748b;">Allergies: ${med.allergies || 'None'}</div>
                    </td>
                    <td>
                        <span style="color: #ef4444; font-weight: 600;">${med.nextClinicDate || 'None'}</span>
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button class="table-action-btn" onclick="openStaffKeyPaperModal('${c.id}')" style="color: #2563eb;">
                            <i class="fas fa-pen-nib"></i> Key Paper
                        </button>
                        <button class="table-action-btn" onclick="openStaffRecordMedModal('${c.id}')" style="color: #059669;">
                            <i class="fas fa-stethoscope"></i> Medical
                        </button>
                        <button class="table-action-btn" onclick="openStaffDossierModal('${c.id}')" title="Official PDF">
                            <i class="fas fa-id-card"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#staff_ward_table_tbody').html(tableHtml);

        // 3. Update Dropdowns
        var opts = '';
        children.forEach(function(c) {
            opts += '<option value="' + c.id + '">' + c.name + ' (' + c.dorm + ' - ' + c.grade + ')</option>';
        });
        $('#staff_paper_child_select').html(opts);
        $('#staff_med_child_select').html(opts);

        var vitalsOpts = '';
        children.forEach(function(c) {
            vitalsOpts += '<option value="' + c.name + '">' + c.name + ' (' + c.dorm + ')</option>';
        });
        $('#vitals_child_select').html(vitalsOpts);
    }

    // Modal Trigger: Staff Key Paper
    function openStaffKeyPaperModal(childId) {
        if (childId) {
            $('#staff_paper_child_select').val(childId);
        } else if (window.HopeHavenChildStore) {
            var all = window.HopeHavenChildStore.getAll();
            if (all.length > 0) $('#staff_paper_child_select').val(all[0].id);
        }
        resetStaffSubjectRows();
        openStaffModal('modal_staff_key_paper');
    }

    function resetStaffSubjectRows() {
        var defaultSubjects = [
            { name: 'Mathematics', score: 86 },
            { name: 'Science', score: 88 },
            { name: 'English Language', score: 92 },
            { name: 'Bahasa Melayu', score: 85 }
        ];

        var html = '';
        defaultSubjects.forEach(function(s) {
            html += `
                <div class="subject-score-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 8px; align-items: center; margin-bottom: 8px; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="text" class="staff-input-control staff-subj-name" value="${s.name}" placeholder="Subject">
                    <input type="number" min="0" max="100" class="staff-input-control staff-subj-score" value="${s.score}" placeholder="Marks %" oninput="calculateStaffPaperTotals()">
                    <input type="text" class="staff-input-control staff-subj-grade" value="${staffScoreToGrade(s.score)}" readonly style="text-align: center; font-weight: 700; background: #e2e8f0;">
                    <button type="button" style="background: none; border: none; color: #ef4444; font-size: 16px; cursor: pointer;" onclick="$(this).closest('.subject-score-row').remove(); calculateStaffPaperTotals();">&times;</button>
                </div>
            `;
        });
        $('#staff_subjects_container').html(html);
        calculateStaffPaperTotals();
    }

    function addStaffSubjectRow() {
        var html = `
            <div class="subject-score-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 8px; align-items: center; margin-bottom: 8px; background: #f8fafc; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <input type="text" class="staff-input-control staff-subj-name" value="Creative Arts" placeholder="Subject">
                <input type="number" min="0" max="100" class="staff-input-control staff-subj-score" value="90" placeholder="Marks %" oninput="calculateStaffPaperTotals()">
                <input type="text" class="staff-input-control staff-subj-grade" value="A+" readonly style="text-align: center; font-weight: 700; background: #e2e8f0;">
                <button type="button" style="background: none; border: none; color: #ef4444; font-size: 16px; cursor: pointer;" onclick="$(this).closest('.subject-score-row').remove(); calculateStaffPaperTotals();">&times;</button>
            </div>
        `;
        $('#staff_subjects_container').append(html);
        calculateStaffPaperTotals();
    }

    function calculateStaffPaperTotals() {
        var total = 0;
        var count = 0;
        $('#staff_subjects_container .subject-score-row').each(function() {
            var score = parseFloat($(this).find('.staff-subj-score').val()) || 0;
            $(this).find('.staff-subj-grade').val(staffScoreToGrade(score));
            total += score;
            count++;
        });

        var avg = count > 0 ? Math.round(total / count) : 85;
        $('#staff_paper_avg_preview').text(avg + '%');
        var gpa = ((avg / 100) * 4.0).toFixed(2);
        $('#staff_paper_gpa').val(gpa);

        if (avg >= 90) {
            $('#staff_paper_standing').val('Distinction');
        } else if (avg >= 75) {
            $('#staff_paper_standing').val('Good');
        } else {
            $('#staff_paper_standing').val('Needs Math Support');
        }
    }

    function staffScoreToGrade(score) {
        if (score >= 95) return 'A+';
        if (score >= 85) return 'A';
        if (score >= 75) return 'B+';
        if (score >= 65) return 'B';
        if (score >= 50) return 'C';
        return 'D';
    }

    // Handle Staff Paper Form Submit
    function handleStaffPaperSubmit(e) {
        e.preventDefault();
        var childId = $('#staff_paper_child_select').val();
        var child = window.HopeHavenChildStore ? window.HopeHavenChildStore.getById(childId) : null;
        if (!child) return;

        var examName = $('#staff_paper_exam_name').val().trim();
        var term = $('#staff_paper_term').val().trim();
        var examDate = $('#staff_paper_date').val();
        var gpa = $('#staff_paper_gpa').val();
        var status = $('#staff_paper_standing').val();
        var remarks = $('#staff_paper_remarks').val().trim() || 'Keyed in by Nurse Mary during Morning Duty Shift.';

        var subjects = [];
        var totalScore = 0;
        var count = 0;
        $('#staff_subjects_container .subject-score-row').each(function() {
            var sName = $(this).find('.staff-subj-name').val().trim();
            var sScore = parseFloat($(this).find('.staff-subj-score').val()) || 0;
            if (sName) {
                subjects.push({ name: sName, score: sScore, grade: staffScoreToGrade(sScore) });
                totalScore += sScore;
                count++;
            }
        });

        if (count === 0) {
            showPortalToast('Add at least one subject before saving a paper result.', false);
            return;
        }
        var overall = Math.round(totalScore / count);

        var paperData = {
            examName: examName,
            term: term,
            date: examDate,
            subjects: subjects,
            overallScore: overall,
            gpa: parseFloat(gpa).toFixed(2),
            status: status,
            remarks: remarks
        };

        window.HopeHavenChildStore.recordPaperResult(childId, paperData);
        closeStaffModal('modal_staff_key_paper');
        renderStaffWardRegistry();

        notifyStaff(
            'Paper Results Keyed In', 
            examName + ' (' + overall + '% - ' + status + ') recorded for ' + child.name + ' by Nurse Mary.', 
            'success', 
            'fas fa-graduation-cap'
        );
    }

    // Modal Trigger: Staff Medical Condition
    function openStaffRecordMedModal(childId) {
        if (!window.HopeHavenChildStore) return;
        var child = childId ? window.HopeHavenChildStore.getById(childId) : window.HopeHavenChildStore.getAll()[0];
        if (!child) return;

        $('#staff_med_child_select').val(child.id).trigger('change');
        populateStaffMedFormFields(child);
        openStaffModal('modal_staff_record_med');
    }

    function populateStaffMedFormFields(child) {
        if (!child) return;
        var med = child.basicMedical || {};
        $('#staff_med_blood').val(med.bloodType || 'O+');
        $('#staff_med_condition').val(med.condition || 'Healthy');
        $('#staff_med_allergies').val(med.allergies || 'None');
        $('#staff_med_prescriptions').val(med.medications || 'None');
        $('#staff_med_immunization').val(med.immunization || 'Up to Date');
        $('#staff_med_clinic_date').val(med.nextClinicDate || '2026-11-20');
        $('#staff_med_doctor').val(med.clinicDoctor || 'Dr. Angela Reyes');
        $('#staff_med_notes').val(med.medicalNotes || '');
    }

    // Handle Staff Medical Submit
    function handleStaffMedSubmit(e) {
        e.preventDefault();
        var childId = $('#staff_med_child_select').val();
        var child = window.HopeHavenChildStore ? window.HopeHavenChildStore.getById(childId) : null;
        if (!child) return;

        var medData = {
            bloodType: $('#staff_med_blood').val(),
            condition: $('#staff_med_condition').val().trim(),
            allergies: $('#staff_med_allergies').val().trim(),
            medications: $('#staff_med_prescriptions').val().trim(),
            immunization: $('#staff_med_immunization').val(),
            nextClinicDate: $('#staff_med_clinic_date').val(),
            clinicDoctor: $('#staff_med_doctor').val().trim(),
            medicalNotes: $('#staff_med_notes').val().trim()
        };

        window.HopeHavenChildStore.updateMedicalCondition(childId, medData);
        closeStaffModal('modal_staff_record_med');
        renderStaffWardRegistry();

        notifyStaff(
            'Medical Condition Recorded', 
            'Clinical condition (' + medData.condition + ') for ' + child.name + ' documented by Nurse Mary.', 
            'clinical', 
            'fas fa-stethoscope'
        );
    }

    // Handle Staff Add Child Submit
    function handleStaffAddChildSubmit(e) {
        e.preventDefault();
        if (!window.HopeHavenChildPhoto.ready(e.target)) return;
        var name = $('#staff_add_name').val().trim();
        var preferred = $('#staff_add_callname').val().trim() || name.split(' ')[0];
        var gender = $('#staff_add_gender').val();
        var dob = $('#staff_add_dob').val();
        var dorm = $('#staff_add_dorm').val();
        var school = $('#staff_add_school').val().trim() || 'Hope Junior Academy';
        var grade = $('#staff_add_grade').val().trim() || 'Grade 3';
        var bloodType = $('#staff_add_blood').val();
        var condition = $('#staff_add_condition').val().trim() || 'Healthy';
        var allergies = $('#staff_add_allergies').val().trim() || 'None';
        var notes = $('#staff_add_notes').val().trim();

        var newChild = window.HopeHavenChildStore.add({
            name: name,
            preferredName: preferred,
            gender: gender,
            dob: dob,
            dorm: dorm,
            school: school,
            grade: grade,
            bloodType: bloodType,
            condition: condition,
            allergies: allergies,
            medicalNotes: notes,
            avatar: window.HopeHavenChildPhoto.get(e.target),
            caseWorker: 'Nurse Mary (Staff)'
        });

        closeStaffModal('modal_staff_add_child');
        e.target.reset();
        renderStaffWardRegistry();

        notifyStaff(
            'Child Ward Admitted', 
            name + ' (' + newChild.id + ') registered to ' + dorm + ' by Nurse Mary.', 
            'success', 
            'fas fa-child'
        );
    }

    // Modal Trigger: Staff Child PDF
    function openStaffDossierModal(childId) {
        if (!window.HopeHavenChildStore) return;
        var child = window.HopeHavenChildStore.getById(childId);
        if (!child) return;

        var med = child.basicMedical || {};
        var acad = child.academic || {};
        var papers = acad.paperResults || [];

        $('#staff_dossier_title').text('Resident Child PDF: ' + child.name);
        $('#staff_dossier_sub').text('Code: ' + child.id + ' · Admitted: ' + child.admissionDate + ' · Case Worker: Nurse Mary');
        $('#staff_dossier_avatar').attr('src', child.avatar);
        $('#staff_dossier_name').text(child.name);
        $('#staff_dossier_meta').text(child.age + ' yrs · ' + child.gender + ' · Blood: ' + (med.bloodType || 'O+') + ' · ' + child.dorm);

        // Medical Pane
        $('#staff_dossier_med_cond').text(med.condition || 'Healthy');
        $('#staff_dossier_med_allergies').text(med.allergies || 'None');
        $('#staff_dossier_med_presc').text(med.medications || 'None');
        $('#staff_dossier_med_review').text(med.nextClinicDate ? med.nextClinicDate + ' (' + (med.nextClinicSpecialty || 'Review') + ')' : 'None');
        $('#staff_dossier_med_notes').text(med.medicalNotes || 'Standard care routine.');

        // Paper History
        var papersHtml = '';
        if (papers.length > 0) {
            papers.forEach(function(p) {
                var subs = '';
                if (p.subjects && p.subjects.length > 0) {
                    p.subjects.forEach(function(s) {
                        subs += '<span class="badge badge-info" style="margin-right: 4px;">' + s.name + ': ' + s.score + '% (' + (s.grade || 'A') + ')</span>';
                    });
                }
                papersHtml += `
                    <div class="paper-history-card" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;">${p.examName}</strong>
                                <div style="font-size: 11px; color: #64748b;">${p.term} · Date: ${p.date}</div>
                            </div>
                            <span class="badge badge-success">${p.overallScore}% · ${p.status}</span>
                        </div>
                        <div style="margin: 6px 0;">${subs}</div>
                        <div style="font-size: 11px; color: #475569; font-style: italic;">"${p.remarks || 'Standard examination record'}"</div>
                    </div>
                `;
            });
        } else {
            papersHtml = '<div style="padding: 16px; text-align: center; color: #94a3b8; font-style: italic;">No paper examination records logged yet.</div>';
        }
        $('#staff_dossier_paper_history').html(papersHtml);

        switchStaffDossierTab('acad');
        openStaffModal('modal_staff_child_dossier');
    }

    function switchStaffDossierTab(tab) {
        $('#btn_staff_dos_tab_acad').attr('aria-pressed', tab === 'acad' ? 'true' : 'false');
        $('#btn_staff_dos_tab_med').attr('aria-pressed', tab === 'med' ? 'true' : 'false');
        if (tab === 'acad') {
            $('#btn_staff_dos_tab_acad').addClass('active');
            $('#btn_staff_dos_tab_med').removeClass('active');
            $('#staff_dossier_pane_acad').show();
            $('#staff_dossier_pane_med').hide();
        } else {
            $('#btn_staff_dos_tab_acad').removeClass('active');
            $('#btn_staff_dos_tab_med').addClass('active');
            $('#staff_dossier_pane_acad').hide();
            $('#staff_dossier_pane_med').show();
        }
    }

    // Close modals on escape key
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('.staff-duty-modal-backdrop, .modal-voucher-backdrop').fadeOut(200);
        }
    });

    // Close modal on background click
    $('.staff-duty-modal-backdrop, .modal-voucher-backdrop').click(function(e) {
        if (e.target === this) {
            $(this).fadeOut(200);
        }
    });

    // Initial render
    $(document).ready(function() {
        renderStaffWardRegistry();

        // Listen for child switch in staff medical modal
        $('#staff_med_child_select').on('change', function() {
            var cId = $(this).val();
            if (window.HopeHavenChildStore) {
                var selectedChild = window.HopeHavenChildStore.getById(cId);
                populateStaffMedFormFields(selectedChild);
            }
        });

        window.HopeHavenSync.subscribe('hopehaven_children_registry_v2', function() {
            window.HopeHavenSync.refreshView(renderStaffWardRegistry);
        });
    });
</script>
@endpush
