@extends('layouts.portal')

@section('title', 'Education of Children & Academic Governance')
@section('breadcrumb', 'Children Education')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/portal/director-education.css') }}">
@endpush

@section('content')
<div class="portal-container">
    <!-- Page Header -->
    <div class="edu-page-header">
        <div class="edu-header-title">
            <h1><i class="fas fa-graduation-cap" style="color: #7c3aed;"></i> Education of Children & Academic Governance</h1>
            <p>School enrollment oversight, term examination performance, scholarship sponsorship grants, and volunteer tutoring interventions.</p>
        </div>
        <div>
            <button type="button" class="btn-edu" onclick="showPortalToast('Sanctuary Academic Audit Report (Term 2) exported.', 'info')">
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
                <!-- Student 1: Daniel Arif (Tingkatan 2, Distinction) -->
                <tr data-level="lower_secondary" data-standing="distinction">
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar secondary">DA</div>
                            <div>
                                <div class="student-name">Daniel Arif Bin Rosli</div>
                                <div class="student-meta">14 yrs · Ward Beta</div>
                            </div>
                        </div>
                    </td>
                    <td>SMK Seri Titiwangsa<br><span style="font-size: 11px; color: #64748b;">Tingkatan 2 Amanah</span></td>
                    <td><span class="grade-pill distinction"><i class="fas fa-star"></i> 86% · A</span></td>
                    <td>
                        <div class="sub-tags-wrap">
                            <span class="sub-tag">BM: <span>A</span></span>
                            <span class="sub-tag">BI: <span>A+</span></span>
                            <span class="sub-tag">Math: <span>B+</span></span>
                            <span class="sub-tag">Sci: <span>A</span></span>
                        </div>
                    </td>
                    <td><span style="color: #059669; font-weight: 700;">98.8%</span></td>
                    <td>Debate Club, Football Team</td>
                    <td><span style="font-size: 12px; font-weight: 700; color: #7c3aed;">Yayasan Hasanah</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-edu" onclick="openStudentAcademicPortfolio('Daniel Arif Bin Rosli', 'SMK Seri Titiwangsa · Tingkatan 2 Amanah', 'Yayasan Hasanah', 'Grade A (86%)', '98.8% (Exemplary)', '3rd of 36 Students', 'Debate Club & Football Team', '<tr><td>Bahasa Melayu</td><td style=\'text-align:center;\'>88</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Excellent essay composition and grammar.</td></tr><tr><td>English Language</td><td style=\'text-align:center;\'>94</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A+</td><td>Fluent orator, led interschool debate team.</td></tr><tr><td>Mathematics</td><td style=\'text-align:center;\'>78</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B+</td><td>Algebra and geometry mastery improving.</td></tr><tr><td>General Science</td><td style=\'text-align:center;\'>84</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Strong grasp of biological concepts.</td></tr>', 'Enrolled in Saturday Mathematics Clinic with volunteer tutor Brother David. Showing great confidence in algebra.')">
                            <i class="fas fa-id-badge"></i> Portfolio
                        </button>
                    </td>
                </tr>

                <!-- Student 2: Nur Aishah (Darjah 5, Distinction) -->
                <tr data-level="primary" data-standing="distinction">
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar primary">NA</div>
                            <div>
                                <div class="student-name">Nur Aishah Binti Kamaruddin</div>
                                <div class="student-meta">11 yrs · Ward Gamma</div>
                            </div>
                        </div>
                    </td>
                    <td>SK Convent Bukit Nanas<br><span style="font-size: 11px; color: #64748b;">Darjah 5 Mawar</span></td>
                    <td><span class="grade-pill distinction"><i class="fas fa-star"></i> 91% · A+</span></td>
                    <td>
                        <div class="sub-tags-wrap">
                            <span class="sub-tag">BM: <span>A+</span></span>
                            <span class="sub-tag">BI: <span>A+</span></span>
                            <span class="sub-tag">Math: <span>A</span></span>
                            <span class="sub-tag">Sci: <span>A</span></span>
                        </div>
                    </td>
                    <td><span style="color: #059669; font-weight: 700;">100%</span></td>
                    <td>School Choir, Art Club</td>
                    <td><span style="font-size: 12px; font-weight: 700; color: #7c3aed;">Maybank Islamic Scholar</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-edu" onclick="openStudentAcademicPortfolio('Nur Aishah Binti Kamaruddin', 'SK Convent Bukit Nanas · Darjah 5 Mawar', 'Maybank Islamic Scholar', 'Grade A+ (91%)', '100% (Perfect Attendance)', '1st of 32 Students', 'School Choir & Art Club', '<tr><td>Bahasa Melayu</td><td style=\'text-align:center;\'>92</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A+</td><td>Top mark in class for creative writing.</td></tr><tr><td>English Language</td><td style=\'text-align:center;\'>95</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A+</td><td>Outstanding comprehension and vocabulary.</td></tr><tr><td>Mathematics</td><td style=\'text-align:center;\'>88</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Strong problem solving skills.</td></tr><tr><td>General Science</td><td style=\'text-align:center;\'>89</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Enthusiastic experimental project participant.</td></tr>', 'Independent learner. Preparing for national English choral speaking competition.')">
                            <i class="fas fa-id-badge"></i> Portfolio
                        </button>
                    </td>
                </tr>

                <!-- Student 3: Siti Nurhaliza (Tingkatan 5 SPM, Distinction) -->
                <tr data-level="upper_secondary" data-standing="distinction">
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar secondary">SN</div>
                            <div>
                                <div class="student-name">Siti Nurhaliza Binti Yusof</div>
                                <div class="student-meta">17 yrs · Ward Gamma</div>
                            </div>
                        </div>
                    </td>
                    <td>SMK Seri Titiwangsa<br><span style="font-size: 11px; color: #64748b;">Tingkatan 5 Sains (SPM Candidate)</span></td>
                    <td><span class="grade-pill distinction"><i class="fas fa-star"></i> 88% · A</span></td>
                    <td>
                        <div class="sub-tags-wrap">
                            <span class="sub-tag">BM: <span>A</span></span>
                            <span class="sub-tag">AddMath: <span>B+</span></span>
                            <span class="sub-tag">Bio: <span>A</span></span>
                            <span class="sub-tag">Chem: <span>A</span></span>
                        </div>
                    </td>
                    <td><span style="color: #059669; font-weight: 700;">99.1%</span></td>
                    <td>Head Prefect, Red Crescent</td>
                    <td><span style="font-size: 12px; font-weight: 700; color: #7c3aed;">Yayasan Sime Darby</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-edu" onclick="openStudentAcademicPortfolio('Siti Nurhaliza Binti Yusof', 'SMK Seri Titiwangsa · Tingkatan 5 Sains (SPM)', 'Yayasan Sime Darby', 'Grade A (88%)', '99.1% (Exemplary)', '2nd of 28 Students', 'Head Prefect & Red Crescent Secretary', '<tr><td>Bahasa Melayu SPM</td><td style=\'text-align:center;\'>90</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Distinction Karangan SPM.</td></tr><tr><td>Additional Mathematics</td><td style=\'text-align:center;\'>79</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B+</td><td>Calculus coaching scheduled.</td></tr><tr><td>Biology SPM</td><td style=\'text-align:center;\'>91</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A+</td><td>Aspirations to enter Nursing / Medicine.</td></tr><tr><td>Chemistry SPM</td><td style=\'text-align:center;\'>86</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Consistent high performance.</td></tr>', 'Targeting 8A+ in SPM 2026. Weekly intensive prep coaching sponsored by Yayasan Sime Darby.')">
                            <i class="fas fa-id-badge"></i> Portfolio
                        </button>
                    </td>
                </tr>

                <!-- Student 4: Faris Hakimi (Darjah 3, Credit) -->
                <tr data-level="primary" data-standing="credit">
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar primary">FH</div>
                            <div>
                                <div class="student-name">Faris Hakimi Bin Amran</div>
                                <div class="student-meta">9 yrs · Ward Beta</div>
                            </div>
                        </div>
                    </td>
                    <td>SK St. John<br><span style="font-size: 11px; color: #64748b;">Darjah 3 Cemerlang</span></td>
                    <td><span class="grade-pill credit">74% · B</span></td>
                    <td>
                        <div class="sub-tags-wrap">
                            <span class="sub-tag">BM: <span>B+</span></span>
                            <span class="sub-tag">BI: <span>B</span></span>
                            <span class="sub-tag">Math: <span>A</span></span>
                            <span class="sub-tag">Sci: <span>B+</span></span>
                        </div>
                    </td>
                    <td><span style="color: #059669; font-weight: 700;">97.5%</span></td>
                    <td>Robotics Club, Scout</td>
                    <td><span style="font-size: 12px; font-weight: 700; color: #64748b;">Sanctuary Education Fund</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-edu" onclick="openStudentAcademicPortfolio('Faris Hakimi Bin Amran', 'SK St. John · Darjah 3 Cemerlang', 'Sanctuary Education Fund', 'Grade B (74%)', '97.5% (Good)', '8th of 30 Students', 'Robotics Club & Junior Scout', '<tr><td>Bahasa Melayu</td><td style=\'text-align:center;\'>76</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B+</td><td>Reading comprehension improving.</td></tr><tr><td>English Language</td><td style=\'text-align:center;\'>72</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B</td><td>Practicing spoken dialogue with volunteers.</td></tr><tr><td>Mathematics</td><td style=\'text-align:center;\'>85</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Strong mental arithmetic.</td></tr><tr><td>General Science</td><td style=\'text-align:center;\'>78</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B+</td><td>Great enthusiasm in robotics lab.</td></tr>', 'Steady progress across all subjects. Participates eagerly in Study Hall homework hours.')">
                            <i class="fas fa-id-badge"></i> Portfolio
                        </button>
                    </td>
                </tr>

                <!-- Student 5: Adam Harith (Tingkatan 1, Remedial Support) -->
                <tr data-level="lower_secondary" data-standing="remedial">
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar secondary">AH</div>
                            <div>
                                <div class="student-name">Adam Harith Bin Zulkifli</div>
                                <div class="student-meta">13 yrs · Ward Beta</div>
                            </div>
                        </div>
                    </td>
                    <td>SMK Seri Titiwangsa<br><span style="font-size: 11px; color: #64748b;">Tingkatan 1 Bakti</span></td>
                    <td><span class="grade-pill remedial"><i class="fas fa-triangle-exclamation"></i> 64% · C</span></td>
                    <td>
                        <div class="sub-tags-wrap">
                            <span class="sub-tag">BM: <span>B</span></span>
                            <span class="sub-tag">BI: <span>C+</span></span>
                            <span class="sub-tag">Math: <span style="color:#e11d48;">C</span></span>
                            <span class="sub-tag">Sci: <span>B</span></span>
                        </div>
                    </td>
                    <td><span style="color: #d97706; font-weight: 700;">94.2%</span></td>
                    <td>Athletics (100m Sprint)</td>
                    <td><span style="font-size: 12px; font-weight: 700; color: #64748b;">Sanctuary Education Fund</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-edu" onclick="openStudentAcademicPortfolio('Adam Harith Bin Zulkifli', 'SMK Seri Titiwangsa · Tingkatan 1 Bakti', 'Sanctuary Education Fund', 'Grade C (64%)', '94.2% (Satisfactory)', '22nd of 34 Students', 'School Track & Field (100m Sprint Champion)', '<tr><td>Bahasa Melayu</td><td style=\'text-align:center;\'>70</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B</td><td>Satisfactory literature and essay writing.</td></tr><tr><td>English Language</td><td style=\'text-align:center;\'>62</td><td style=\'text-align:center; font-weight:bold; color:#d97706;\'>C+</td><td>Needs focus on vocabulary and grammar rules.</td></tr><tr><td>Mathematics</td><td style=\'text-align:center;\'>55</td><td style=\'text-align:center; font-weight:bold; color:#e11d48;\'>C</td><td>Struggling with linear equations and fractions.</td></tr><tr><td>General Science</td><td style=\'text-align:center;\'>70</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B</td><td>Active in laboratory practical experiments.</td></tr>', 'Assigned to bi-weekly 1-on-1 Mathematics remedial tutoring with volunteer Brother Kevin.')">
                            <i class="fas fa-id-badge"></i> Portfolio
                        </button>
                    </td>
                </tr>

                <!-- Student 6: Aminah (Darjah 2, Distinction) -->
                <tr data-level="primary" data-standing="distinction">
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar primary">AB</div>
                            <div>
                                <div class="student-name">Aminah Binti Abdullah</div>
                                <div class="student-meta">8 yrs · Ward Gamma</div>
                            </div>
                        </div>
                    </td>
                    <td>SK Convent Bukit Nanas<br><span style="font-size: 11px; color: #64748b;">Darjah 2 Melati</span></td>
                    <td><span class="grade-pill distinction"><i class="fas fa-star"></i> 85% · A</span></td>
                    <td>
                        <div class="sub-tags-wrap">
                            <span class="sub-tag">BM: <span>A</span></span>
                            <span class="sub-tag">BI: <span>A</span></span>
                            <span class="sub-tag">Math: <span>B+</span></span>
                            <span class="sub-tag">Sci: <span>A</span></span>
                        </div>
                    </td>
                    <td><span style="color: #059669; font-weight: 700;">99.4%</span></td>
                    <td>Storytelling, Traditional Dance</td>
                    <td><span style="font-size: 12px; font-weight: 700; color: #7c3aed;">PETRONAS Foundation</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-edu" onclick="openStudentAcademicPortfolio('Aminah Binti Abdullah', 'SK Convent Bukit Nanas · Darjah 2 Melati', 'PETRONAS Foundation', 'Grade A (85%)', '99.4% (Exemplary)', '4th of 30 Students', 'Storytelling Competition & Traditional Dance', '<tr><td>Bahasa Melayu</td><td style=\'text-align:center;\'>88</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Fluent reader, expressive recitation.</td></tr><tr><td>English Language</td><td style=\'text-align:center;\'>86</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Strong phonics and reading comprehension.</td></tr><tr><td>Mathematics</td><td style=\'text-align:center;\'>79</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B+</td><td>Mastered multiplication tables 1 through 6.</td></tr><tr><td>General Science</td><td style=\'text-align:center;\'>88</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Keen observation of plants and animals.</td></tr>', 'Loves the sanctuary library. Reading at 2 grades above her age level.')">
                            <i class="fas fa-id-badge"></i> Portfolio
                        </button>
                    </td>
                </tr>

                <!-- Student 7: Ahmad Danial (Darjah 5, Credit) -->
                <tr data-level="primary" data-standing="credit">
                    <td>
                        <div class="student-cell">
                            <div class="student-avatar primary">AD</div>
                            <div>
                                <div class="student-name">Ahmad Danial Bin Razif</div>
                                <div class="student-meta">11 yrs · Ward Beta</div>
                            </div>
                        </div>
                    </td>
                    <td>SK St. John<br><span style="font-size: 11px; color: #64748b;">Darjah 5 Usaha</span></td>
                    <td><span class="grade-pill credit">78% · B+</span></td>
                    <td>
                        <div class="sub-tags-wrap">
                            <span class="sub-tag">BM: <span>B+</span></span>
                            <span class="sub-tag">BI: <span>B+</span></span>
                            <span class="sub-tag">Math: <span>A</span></span>
                            <span class="sub-tag">Sci: <span>B</span></span>
                        </div>
                    </td>
                    <td><span style="color: #059669; font-weight: 700;">96.8%</span></td>
                    <td>School Chess Club</td>
                    <td><span style="font-size: 12px; font-weight: 700; color: #64748b;">Sanctuary Education Fund</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-edu" onclick="openStudentAcademicPortfolio('Ahmad Danial Bin Razif', 'SK St. John · Darjah 5 Usaha', 'Sanctuary Education Fund', 'Grade B+ (78%)', '96.8% (Good)', '7th of 32 Students', 'School Chess Club Captain', '<tr><td>Bahasa Melayu</td><td style=\'text-align:center;\'>79</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B+</td><td>Neat penmanship and solid comprehension.</td></tr><tr><td>English Language</td><td style=\'text-align:center;\'>78</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B+</td><td>Good listening and writing test results.</td></tr><tr><td>Mathematics</td><td style=\'text-align:center;\'>86</td><td style=\'text-align:center; font-weight:bold; color:#059669;\'>A</td><td>Analytical thinker, shines in logic puzzles.</td></tr><tr><td>General Science</td><td style=\'text-align:center;\'>72</td><td style=\'text-align:center; font-weight:bold; color:#4f46e5;\'>B</td><td>Understands scientific inquiry and circuits.</td></tr>', 'Represented district in Junior Chess championship. Very focused and attentive.')">
                            <i class="fas fa-id-badge"></i> Portfolio
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Include Dedicated Modals Partial -->
@include('portal.director.partials.education-modals')
@endsection

@push('scripts')
    <script src="{{ asset('js/portal/director-education.js') }}"></script>
@endpush
