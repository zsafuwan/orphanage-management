@extends('layouts.portal')

@section('title', 'Children’s Medical Records & Clinical PDFs')
@section('breadcrumb', 'Children’s Medical Records')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-medical.css') }}?v={{ substr(hash_file('sha256', public_path('css/portal/director-medical.css')), 0, 12) }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Page Header -->
    <div class="med-header">
        <div class="med-header-title">
            <h1><i class="fas fa-heartbeat" style="color: #059669;"></i> Children’s Medical Records & Clinical PDFs</h1>
            <p>Comprehensive health oversight, allergy warnings, chronic illness management, and immunization tracking for all 48 children in Care.</p>
        </div>
        <div>
            <button type="button" class="btn-dossier" onclick="showPortalToast('Orphanage Medical Report (KKM Form 12) downloaded.', 'info')">
                <i class="fas fa-file-medical"></i> Export Health Register (PDF)
            </button>
        </div>
    </div>

    <!-- Clinical KPI Counters -->
    <div class="med-kpi-row">
        <div class="med-kpi-card">
            <div class="med-kpi-icon emerald">
                <i class="fas fa-shield-virus"></i>
            </div>
            <div>
                <div class="med-kpi-val">100%</div>
                <div class="med-kpi-lbl">Immunization Compliance</div>
            </div>
        </div>

        <div class="med-kpi-card">
            <div class="med-kpi-icon indigo">
                <i class="fas fa-pills"></i>
            </div>
            <div>
                <div class="med-kpi-val">5</div>
                <div class="med-kpi-lbl">Active Daily Prescriptions</div>
            </div>
        </div>

        <div class="med-kpi-card">
            <div class="med-kpi-icon rose">
                <i class="fas fa-hand-dots"></i>
            </div>
            <div>
                <div class="med-kpi-val">4</div>
                <div class="med-kpi-lbl">Allergy Watchlist Profiles</div>
            </div>
        </div>

        <div class="med-kpi-card">
            <div class="med-kpi-icon amber">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <div class="med-kpi-val">2</div>
                <div class="med-kpi-lbl">Specialist Visits This Week</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="med-toolbar">
        <div class="med-filter-tabs">
            <button type="button" class="med-tab-btn active" data-ward="all">
                All Wards <span class="med-tab-badge">48</span>
            </button>
            <button type="button" class="med-tab-btn" data-ward="alpha">
                Ward Alpha (Nursery) <span class="med-tab-badge">5</span>
            </button>
            <button type="button" class="med-tab-btn" data-ward="beta">
                Ward Beta (Boys) <span class="med-tab-badge">22</span>
            </button>
            <button type="button" class="med-tab-btn" data-ward="gamma">
                Ward Gamma (Girls) <span class="med-tab-badge">21</span>
            </button>
        </div>

        <div class="med-search-wrap">
            <select class="inv-select-filter" id="med_status_filter">
                <option value="all">All Health Conditions</option>
                <option value="meds">Active Medication Only</option>
                <option value="allergy">Allergy Watchlist</option>
                <option value="clear">Fully Clear / Routine</option>
            </select>
            <input type="text" class="med-search-input" id="med_search_input" placeholder="Search child name, registry ID, or condition...">
        </div>
    </div>

    <!-- Medical PDFs Table Card -->
    <div class="med-table-card">
        <table class="med-table">
            <thead>
                <tr>
                    <th>Child PDF</th>
                    <th>Ward / Age</th>
                    <th>Blood Type</th>
                    <th>Allergy Alerts</th>
                    <th>Chronic Watchlist</th>
                    <th>Growth Vitals & BMI</th>
                    <th>Attending Doctor</th>
                    <th style="text-align: right;">Clinical Records</th>
                </tr>
            </thead>
            <tbody>
                    <tr><td colspan="8">Loading shared child registry…</td></tr>
                </tbody>
        </table>
    </div>
</div>

<!-- Include Dedicated Modals Partial -->
@include('portal.director.partials.medical-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-medical.js') }}?v={{ substr(hash_file('sha256', public_path('js/portal/director-medical.js')), 0, 12) }}"></script>
@endpush
