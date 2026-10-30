@extends('layouts.portal')

@section('title', 'Children’s Education')
@section('breadcrumb', 'Children Education')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-education.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/director-education.css')), 0, 12) }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Page Header -->
    <div class="edu-page-header">
        <div class="edu-header-title">
            <h1><i class="fas fa-graduation-cap" style="color: #7c3aed;"></i> Children’s Education</h1>
            <p>School enrollment oversight, term examination performance, scholarship sponsorship grants, and volunteer tutoring interventions.</p>
        </div>
        <div>
            <button type="button" class="btn-edu" onclick="showPortalToast('Orphanage School Report (Term 2) exported.', 'info')">
                <i class="fas fa-file-invoice"></i> Export Academic Report
            </button>
        </div>
    </div>

    <!-- Academic KPI Metrics Row -->
    <div class="edu-kpi-row">
        <div class="edu-kpi-card">
            <div class="edu-kpi-icon purple">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <div class="edu-kpi-val">43</div>
                <div class="edu-kpi-lbl">Enrolled School Students</div>
            </div>
        </div>

        <div class="edu-kpi-card">
            <div class="edu-kpi-icon emerald">
                <i class="fas fa-award"></i>
            </div>
            <div>
                <div class="edu-kpi-val">88.4%</div>
                <div class="edu-kpi-lbl">Overall Term Pass Rate</div>
            </div>
        </div>

        <div class="edu-kpi-card">
            <div class="edu-kpi-icon indigo">
                <i class="fas fa-medal"></i>
            </div>
            <div>
                <div class="edu-kpi-val">6</div>
                <div class="edu-kpi-lbl">Honor Roll / Distinction</div>
            </div>
        </div>

        <div class="edu-kpi-card">
            <div class="edu-kpi-icon amber">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <div>
                <div class="edu-kpi-val">6</div>
                <div class="edu-kpi-lbl">Active Remedial Tutoring</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="edu-toolbar">
        <div class="edu-filter-tabs">
            <button type="button" class="edu-tab-btn active" data-level="all">
                All Students <span class="edu-tab-badge">43</span>
            </button>
            <button type="button" class="edu-tab-btn" data-level="primary">
                Primary (Darjah 1-6) <span class="edu-tab-badge">25</span>
            </button>
            <button type="button" class="edu-tab-btn" data-level="lower_secondary">
                Lower Sec (Ting. 1-3) <span class="edu-tab-badge">11</span>
            </button>
            <button type="button" class="edu-tab-btn" data-level="upper_secondary">
                SPM Prep (Ting. 4-5) <span class="edu-tab-badge">7</span>
            </button>
        </div>

        <div class="edu-search-wrap">
            <select class="inv-select-filter" id="edu_standing_filter">
                <option value="all">All Academic Standings</option>
                <option value="distinction">Honor Roll (Grade A)</option>
                <option value="credit">Good Standing (Grade B)</option>
                <option value="remedial">Remedial Focus (Grade C)</option>
            </select>
            <input type="text" class="edu-search-input" id="edu_search_input" placeholder="Search student name, school, or class...">
        </div>
    </div>

    <!-- Children Education Table Card -->
    <div class="edu-table-card">
        <table class="edu-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>School & Class</th>
                    <th>Term GPA</th>
                    <th>Core Subject Grades</th>
                    <th>Attendance</th>
                    <th>Co-Curricular & Talents</th>
                    <th>Scholarship Sponsor</th>
                    <th style="text-align: right;">Academic Record</th>
                </tr>
            </thead>
            <tbody>
                    <tr><td colspan="8">Loading shared child registry…</td></tr>
                </tbody>
        </table>
    </div>
</div>

<!-- Include Dedicated Modals Partial -->
@include('portal.director.partials.education-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-education.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/director-education.js')), 0, 12) }}"></script>
@endpush
