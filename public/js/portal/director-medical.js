/**
 * Director Children’s Medical Records JavaScript
 * Handles patient filtering, PDF previews, clinical notes, and health exports
 */

let activeDossierChild = null;

document.addEventListener('DOMContentLoaded', function () {
    initMedicalFilters();
    initMedicalSearch();
});

/**
 * Filter tabs & dropdowns
 */
function initMedicalFilters() {
    const tabBtns = document.querySelectorAll('.med-tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const wardFilter = this.getAttribute('data-ward');
            filterMedicalTable(wardFilter, null);
        });
    });

    const statusSelect = document.getElementById('med_status_filter');
    if (statusSelect) {
        statusSelect.addEventListener('change', function () {
            filterMedicalTable(null, this.value);
        });
    }
}

/**
 * Filter medical rows by ward and health condition status
 */
function filterMedicalTable(wardFilter, statusFilter) {
    const currentWard = wardFilter !== null && wardFilter !== undefined
        ? wardFilter
        : document.querySelector('.med-tab-btn.active')?.getAttribute('data-ward') || 'all';

    const currentStatus = statusFilter !== null && statusFilter !== undefined
        ? statusFilter
        : document.getElementById('med_status_filter')?.value || 'all';

    const rows = document.querySelectorAll('.med-table tbody tr');

    rows.forEach(row => {
        const rowWard = row.getAttribute('data-ward');
        const rowStatus = row.getAttribute('data-health-status');

        let matchesWard = (currentWard === 'all') || (rowWard === currentWard);
        let matchesStatus = (currentStatus === 'all') || (rowStatus === currentStatus)
            || (currentStatus === 'allergy' && row.dataset.hasAllergy === 'true')
            || (currentStatus === 'meds' && row.dataset.hasMeds === 'true');

        if (matchesWard && matchesStatus && row.textContent.toLowerCase().includes((document.getElementById('med_search_input')?.value || '').toLowerCase().trim())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Live search
 */
function initMedicalSearch() {
    const searchInput = document.getElementById('med_search_input');
    if (searchInput) searchInput.addEventListener('input', function () { filterMedicalTable(null, null); });
}

/**
 * Open Child Clinical PDF Modal
 */
function openChildMedicalDossier(name, meta, mykid, blood, allergy, vitals, bmi, meds, doctor) {
    activeDossierChild = name;

    document.getElementById('dossier_child_name').innerText = name;
    document.getElementById('dossier_child_meta').innerText = meta;
    document.getElementById('dossier_child_mykid').innerText = `MyKid: ${mykid}`;
    document.getElementById('dossier_blood_badge').innerText = `Blood Type: ${blood}`;

    const allergyBanner = document.getElementById('dossier_allergy_banner');
    const allergyText = document.getElementById('dossier_allergy_text');
    if (allergy && allergy.toLowerCase() !== 'none' && allergy.toLowerCase() !== 'none known') {
        allergyBanner.style.display = 'flex';
        allergyText.innerText = `Active Allergy Warning: ${allergy}. Strictly avoid allergen triggers.`;
    } else {
        allergyBanner.style.display = 'none';
    }

    document.getElementById('dossier_vitals').innerText = vitals;
    document.getElementById('dossier_bmi').innerText = bmi;
    document.getElementById('dossier_medications').innerHTML = meds;
    document.getElementById('dossier_doctor').innerText = doctor;

    const modal = document.getElementById('modal_child_medical_dossier');
    if (modal) modal.classList.add('active');
}

/**
 * Open Add Clinical Note Modal
 */
function openAddClinicalNoteModal() {
    document.getElementById('note_child_name').value = activeDossierChild || 'Aminah Binti Abdullah';
    document.getElementById('clinical_note_text').value = '';

    const modal = document.getElementById('modal_add_clinical_note');
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
 * Save Clinical Note
 */
function saveClinicalNote() {
    const noteText = document.getElementById('clinical_note_text').value.trim();
    if (!noteText) {
        alert('Please enter clinical observation or doctor directive details.');
        return;
    }

    const noteType = document.getElementById('clinical_note_type').options[document.getElementById('clinical_note_type').selectedIndex].text;
    const child = document.getElementById('note_child_name').value;
    const record = window.HopeHavenChildStore?.getById(window.HopeHavenSelectedChildId);
    if (record) {
        const timeline = record.timeline || [];
        timeline.unshift({date:new Date().toISOString().slice(0,10),title:noteType,category:'medical',detail:noteText});
        window.HopeHavenChildStore.update(record.id, {
            basicMedical:{...record.basicMedical,medicalNotes:noteText},
            timeline:timeline
        });
    }

    closeDirectorModal('modal_add_clinical_note');

    if (typeof showPortalToast === 'function') {
        showPortalToast(`Clinical note recorded for ${child}: [${noteType}] "${noteText}"`, 'success');
    } else {
        alert(`Clinical note recorded for ${child}.`);
    }
}

/**
 * Print Health Passport
 */
function printHealthPassport() {
    if (typeof showPortalToast === 'function') {
        showPortalToast(`Generating Ministry of Health (KKM) Child Health Passport for ${activeDossierChild}...`, 'info');
    } else {
        alert(`Generating Health Passport for ${activeDossierChild}...`);
    }
}
