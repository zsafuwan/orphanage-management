@extends('layouts.portal')

@section('title', 'Children Medical Records & Clinical PDFs')
@section('breadcrumb', 'Children Medical Records')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-medical.css') }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Page Header -->
    <div class="med-header">
        <div class="med-header-title">
            <h1><i class="fas fa-heartbeat" style="color: #059669;"></i> Children Medical Records & Clinical PDFs</h1>
            <p>Comprehensive health oversight, allergy warnings, chronic illness management, and immunization tracking for all 48 sanctuary wards.</p>
        </div>
        <div>
            <button type="button" class="btn-dossier" style="background:#4f46e5;" onclick="showPortalToast('Sanctuary Clinical Audit Export (KKM Form 12) downloaded.', 'info')">
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
                <option value="allergy">Severe Allergies Only</option>
                <option value="clear">Fully Clear / Routine</option>
            </select>
            <input type="text" class="med-search-input" id="med_search_input" placeholder="Search child name, MyKid, or condition...">
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
                <!-- Child 1: Aminah (Ward Gamma, Penicillin Allergy) -->
                <tr data-ward="gamma" data-health-status="allergy">
                    <td>
                        <div class="child-profile-cell">
                            <div class="child-avatar-box child-avatar-girl">AB</div>
                            <div>
                                <div class="child-name">Aminah Binti Abdullah</div>
                                <div class="child-meta">MyKid: 160412-14-5582</div>
                            </div>
                        </div>
                    </td>
                    <td>Ward Gamma<br><span style="font-size: 11px; color: #64748b;">8 years old</span></td>
                    <td><span class="blood-badge">O+</span></td>
                    <td><span class="allergy-tag"><i class="fas fa-ban"></i> Penicillin</span></td>
                    <td><span class="condition-pill normal"><i class="fas fa-check"></i> Good Standing</span></td>
                    <td>124 cm · 24.5 kg<br><span style="font-size: 11px; color: #059669; font-weight: 700;">BMI 16.0 (Normal)</span></td>
                    <td>Dr. Raymond Lee<br><span style="font-size: 11px; color: #64748b;">Klinik Titiwangsa</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-dossier" onclick="openChildMedicalDossier('Aminah Binti Abdullah', 'Age: 8 yrs · Ward Gamma', '160412-14-5582', 'O+', 'Severe Penicillin & Amoxicillin allergy', '124 cm · 24.5 kg', '16.0 (Healthy Normal)', 'Multivitamin syrup (5ml daily with breakfast). Zero antibiotics containing penicillin.', 'Dr. Raymond Lee (Pediatrician)')">
                            <i class="fas fa-file-medical"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Child 2: Ahmad Danial (Ward Beta, Asthma) -->
                <tr data-ward="beta" data-health-status="meds">
                    <td>
                        <div class="child-profile-cell">
                            <div class="child-avatar-box child-avatar-boy">AD</div>
                            <div>
                                <div class="child-name">Ahmad Danial Bin Razif</div>
                                <div class="child-meta">MyKid: 150823-10-6123</div>
                            </div>
                        </div>
                    </td>
                    <td>Ward Beta<br><span style="font-size: 11px; color: #64748b;">11 years old</span></td>
                    <td><span class="blood-badge">A+</span></td>
                    <td><span class="allergy-none">None known</span></td>
                    <td><span class="condition-pill active"><i class="fas fa-lungs"></i> Bronchial Asthma</span></td>
                    <td>142 cm · 34.0 kg<br><span style="font-size: 11px; color: #059669; font-weight: 700;">BMI 16.8 (Normal)</span></td>
                    <td>Dr. Raymond Lee<br><span style="font-size: 11px; color: #64748b;">HKL Pediatric Clinic</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-dossier" onclick="openChildMedicalDossier('Ahmad Danial Bin Razif', 'Age: 11 yrs · Ward Beta', '150823-10-6123', 'A+', 'None known', '142 cm · 34.0 kg', '16.8 (Healthy Normal)', 'Ventolin Inhaler 100mcg (2 puffs PRN before physical activity). Seretide inhaler daily bedtime.', 'Dr. Raymond Lee (Pediatrician)')">
                            <i class="fas fa-file-medical"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Child 3: Rayyan Iskandar (Ward Alpha Nursery) -->
                <tr data-ward="alpha" data-health-status="allergy">
                    <td>
                        <div class="child-profile-cell">
                            <div class="child-avatar-box child-avatar-baby">RI</div>
                            <div>
                                <div class="child-name">Rayyan Iskandar</div>
                                <div class="child-meta">MyKid: 240219-14-1109</div>
                            </div>
                        </div>
                    </td>
                    <td>Ward Alpha<br><span style="font-size: 11px; color: #64748b;">2 years old (Toddler)</span></td>
                    <td><span class="blood-badge">B+</span></td>
                    <td><span class="allergy-tag"><i class="fas fa-ban"></i> Cow Milk / Lactose</span></td>
                    <td><span class="condition-pill active"><i class="fas fa-baby"></i> Dietary Sensitive</span></td>
                    <td>86 cm · 12.2 kg<br><span style="font-size: 11px; color: #059669; font-weight: 700;">BMI 16.5 (Normal)</span></td>
                    <td>Dr. Norazlina<br><span style="font-size: 11px; color: #64748b;">Klinik Kesihatan Setapak</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-dossier" onclick="openChildMedicalDossier('Rayyan Iskandar', 'Age: 2 yrs · Ward Alpha Nursery', '240219-14-1109', 'B+', 'Cow milk protein allergy / Lactose intolerance', '86 cm · 12.2 kg', '16.5 (Normal Infant Curve)', 'Isomil Soy Hypoallergenic Formula exclusively. Probiotic drops 5 drops morning.', 'Dr. Norazlina (Pediatric Nutritionist)')">
                            <i class="fas fa-file-medical"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Child 4: Siti Nurhaliza (Ward Gamma, Eczema) -->
                <tr data-ward="gamma" data-health-status="meds">
                    <td>
                        <div class="child-profile-cell">
                            <div class="child-avatar-box child-avatar-girl">SN</div>
                            <div>
                                <div class="child-name">Siti Nurhaliza Binti Yusof</div>
                                <div class="child-meta">MyKid: 110905-14-8840</div>
                            </div>
                        </div>
                    </td>
                    <td>Ward Gamma<br><span style="font-size: 11px; color: #64748b;">15 years old</span></td>
                    <td><span class="blood-badge">O+</span></td>
                    <td><span class="allergy-none">None known</span></td>
                    <td><span class="condition-pill active"><i class="fas fa-notes-medical"></i> Atopic Eczema</span></td>
                    <td>158 cm · 48.0 kg<br><span style="font-size: 11px; color: #059669; font-weight: 700;">BMI 19.2 (Normal)</span></td>
                    <td>Dr. Wong Siew May<br><span style="font-size: 11px; color: #64748b;">Dermatology HKL</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-dossier" onclick="openChildMedicalDossier('Siti Nurhaliza Binti Yusof', 'Age: 15 yrs · Ward Gamma', '110905-14-8840', 'O+', 'None known', '158 cm · 48.0 kg', '19.2 (Healthy Normal)', 'Aqueous cream moisturizer twice daily. Betamethasone 0.05% topical ointment for flare-ups only.', 'Dr. Wong Siew May (Dermatologist)')">
                            <i class="fas fa-file-medical"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Child 5: Adam Harith (Ward Beta, Peanut Allergy) -->
                <tr data-ward="beta" data-health-status="allergy">
                    <td>
                        <div class="child-profile-cell">
                            <div class="child-avatar-box child-avatar-boy">AH</div>
                            <div>
                                <div class="child-name">Adam Harith Bin Zulkifli</div>
                                <div class="child-meta">MyKid: 130310-10-4491</div>
                            </div>
                        </div>
                    </td>
                    <td>Ward Beta<br><span style="font-size: 11px; color: #64748b;">13 years old</span></td>
                    <td><span class="blood-badge">AB+</span></td>
                    <td><span class="allergy-tag"><i class="fas fa-ban"></i> Peanuts / Tree Nuts</span></td>
                    <td><span class="condition-pill normal"><i class="fas fa-check"></i> Good Standing</span></td>
                    <td>152 cm · 42.5 kg<br><span style="font-size: 11px; color: #059669; font-weight: 700;">BMI 18.4 (Normal)</span></td>
                    <td>Dr. Raymond Lee<br><span style="font-size: 11px; color: #64748b;">Klinik Titiwangsa</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-dossier" onclick="openChildMedicalDossier('Adam Harith Bin Zulkifli', 'Age: 13 yrs · Ward Beta', '130310-10-4491', 'AB+', 'Severe Anaphylactic Peanut Allergy. Epipen stored in Ward Beta Emergency Kit.', '152 cm · 42.5 kg', '18.4 (Healthy Normal)', 'No daily routine medication. Emergency EpiPen Jr 0.3mg stored in First Aid Locker.', 'Dr. Raymond Lee (Pediatrician)')">
                            <i class="fas fa-file-medical"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Child 6: Maya Sofia (Ward Alpha Nursery) -->
                <tr data-ward="alpha" data-health-status="clear">
                    <td>
                        <div class="child-profile-cell">
                            <div class="child-avatar-box child-avatar-baby">MS</div>
                            <div>
                                <div class="child-name">Maya Sofia</div>
                                <div class="child-meta">MyKid: 250508-14-3298</div>
                            </div>
                        </div>
                    </td>
                    <td>Ward Alpha<br><span style="font-size: 11px; color: #64748b;">1 year old (Infant)</span></td>
                    <td><span class="blood-badge">A+</span></td>
                    <td><span class="allergy-none">None known</span></td>
                    <td><span class="condition-pill normal"><i class="fas fa-check"></i> 100% Immunized</span></td>
                    <td>74 cm · 9.4 kg<br><span style="font-size: 11px; color: #059669; font-weight: 700;">BMI 17.1 (Normal)</span></td>
                    <td>Dr. Norazlina<br><span style="font-size: 11px; color: #64748b;">Klinik Kesihatan Setapak</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-dossier" onclick="openChildMedicalDossier('Maya Sofia', 'Age: 1 yr · Ward Alpha Nursery', '250508-14-3298', 'A+', 'None known', '74 cm · 9.4 kg', '17.1 (Normal Infant Curve)', 'Routine infant vitamins & Vitamin D drops (400 IU daily).', 'Dr. Norazlina (Pediatrician)')">
                            <i class="fas fa-file-medical"></i> PDF
                        </button>
                    </td>
                </tr>

                <!-- Child 7: Faris Hakimi (Ward Beta) -->
                <tr data-ward="beta" data-health-status="clear">
                    <td>
                        <div class="child-profile-cell">
                            <div class="child-avatar-box child-avatar-boy">FH</div>
                            <div>
                                <div class="child-name">Faris Hakimi Bin Amran</div>
                                <div class="child-meta">MyKid: 170214-10-8201</div>
                            </div>
                        </div>
                    </td>
                    <td>Ward Beta<br><span style="font-size: 11px; color: #64748b;">9 years old</span></td>
                    <td><span class="blood-badge">O+</span></td>
                    <td><span class="allergy-none">None known</span></td>
                    <td><span class="condition-pill normal"><i class="fas fa-check"></i> Good Standing</span></td>
                    <td>130 cm · 28.0 kg<br><span style="font-size: 11px; color: #059669; font-weight: 700;">BMI 16.5 (Normal)</span></td>
                    <td>Dr. Raymond Lee<br><span style="font-size: 11px; color: #64748b;">Klinik Titiwangsa</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-dossier" onclick="openChildMedicalDossier('Faris Hakimi Bin Amran', 'Age: 9 yrs · Ward Beta', '170214-10-8201', 'O+', 'None known', '130 cm · 28.0 kg', '16.5 (Healthy Normal)', 'Routine calcium & multivitamin daily chewables. No chronic complaints.', 'Dr. Raymond Lee (Pediatrician)')">
                            <i class="fas fa-file-medical"></i> PDF
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Include Dedicated Modals Partial -->
@include('portal.director.partials.medical-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-medical.js') }}"></script>
@endpush
