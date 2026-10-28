/**
 * Director Children Education JavaScript
 * Handles academic cohorts filtering, grade card previews, transcript exports, and director reviews
 */

let activePortfolioStudent = null;

document.addEventListener('DOMContentLoaded', function () {
    initEducationFilters();
    initEducationSearch();
});

/**
 * Filter tabs and select dropdown
 */
function initEducationFilters() {
    const tabBtns = document.querySelectorAll('.edu-tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const level = this.getAttribute('data-level');
            filterEducationTable(level, null);
        });
    });

    const standingSelect = document.getElementById('edu_standing_filter');
    if (standingSelect) {
        standingSelect.addEventListener('change', function () {
            filterEducationTable(null, this.value);
        });
    }
}

/**
 * Filter table rows by cohort level and standing
 */
function filterEducationTable(levelFilter, standingFilter) {
    const currentLevel = levelFilter !== null && levelFilter !== undefined
        ? levelFilter
        : document.querySelector('.edu-tab-btn.active')?.getAttribute('data-level') || 'all';

    const currentStanding = standingFilter !== null && standingFilter !== undefined
        ? standingFilter
        : document.getElementById('edu_standing_filter')?.value || 'all';

    const rows = document.querySelectorAll('.edu-table tbody tr');

    rows.forEach(row => {
        const rowLevel = row.getAttribute('data-level');
        const rowStanding = row.getAttribute('data-standing');

        let matchesLevel = (currentLevel === 'all') || (rowLevel === currentLevel);
        let matchesStanding = (currentStanding === 'all') || (rowStanding === currentStanding);

        if (matchesLevel && matchesStanding && row.textContent.toLowerCase().includes((document.getElementById('edu_search_input')?.value || '').toLowerCase().trim())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Live search filter
 */
function initEducationSearch() {
    const searchInput = document.getElementById('edu_search_input');
    if (searchInput) searchInput.addEventListener('input', function () { filterEducationTable(null, null); });
}

/**
 * Open Student Academic Portfolio Modal
 */
function openStudentAcademicPortfolio(name, school, sponsor, grade, attendance, rank, cocurriculum, scores, tutoring) {
    activePortfolioStudent = name;

    document.getElementById('portfolio_student_name').innerText = name;
    document.getElementById('portfolio_school_info').innerText = school;
    document.getElementById('portfolio_sponsor_info').innerText = `Sponsor: ${sponsor}`;
    document.getElementById('portfolio_overall_grade').innerText = grade;
    document.getElementById('portfolio_attendance').innerText = attendance;
    document.getElementById('portfolio_rank').innerText = rank;
    document.getElementById('portfolio_cocurriculum').innerText = cocurriculum;
    document.getElementById('portfolio_scorecard_body').innerHTML = scores;
    document.getElementById('portfolio_tutoring_notes').innerText = tutoring;

    const modal = document.getElementById('modal_student_academic_portfolio');
    if (modal) modal.classList.add('active');
}

/**
 * Open Update Grades & Remarks Modal
 */
function openUpdateGradesModal() {
    document.getElementById('update_grade_student_name').value = activePortfolioStudent || 'Student';
    document.getElementById('director_edu_remark').value = '';

    const modal = document.getElementById('modal_update_student_grades');
    if (modal) modal.classList.add('active');
}

/**
 * Close any Director Modal
 */
function closeDirectorModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
}

/**
 * Save Student Grade Evaluation
 */
function saveStudentGradeUpdate() {
    const student = document.getElementById('update_grade_student_name').value;
    const avg = document.getElementById('update_grade_average').value;
    const standing = document.getElementById('update_grade_standing').options[document.getElementById('update_grade_standing').selectedIndex].text;
    const remark = document.getElementById('director_edu_remark').value.trim() || 'Approved and endorsed by Sister Sarah.';
    if (avg === '' || !Number.isFinite(Number(avg)) || Number(avg) < 0 || Number(avg) > 100) {
        showPortalToast('Enter an average score between 0 and 100.', false);
        return;
    }
    const record = window.HopeHavenChildStore?.getById(window.HopeHavenSelectedChildId);
    if (record) {
        window.HopeHavenChildStore.update(record.id, {
            academic:{...record.academic,overallScore:Number(avg),academicStanding:standing,remarks:remark},
            timeline:[{date:new Date().toISOString().slice(0,10),title:'Director academic review',category:'academic',detail:remark},...(record.timeline||[])]
        });
    }

    closeDirectorModal('modal_update_student_grades');

    if (typeof showPortalToast === 'function') {
        showPortalToast(`Academic record for ${student} updated: ${avg}% (${standing}). Remark logged: "${remark}"`, 'success');
    } else {
        alert(`Academic record for ${student} updated successfully.`);
    }
}

/**
 * Print Student Transcript
 */
function printStudentTranscript() {
    if (typeof showPortalToast === 'function') {
        showPortalToast(`Generating Official Academic Transcript for ${activePortfolioStudent}...`, 'info');
    } else {
        alert(`Generating Official Academic Transcript for ${activePortfolioStudent}...`);
    }
}
