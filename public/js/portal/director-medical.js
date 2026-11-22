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
function openChildMedicalDossier(child, hasRecordedAllergies) {
    const med = child.basicMedical || {};
    activeDossierChild = child.name;

    document.getElementById('dossier_child_name').textContent = child.name;
    document.getElementById('dossier_child_meta').textContent = `${child.dorm} · ${child.age} years`;
    document.getElementById('dossier_child_mykid').textContent = `Registry ID: ${child.id}`;

    const allergyBanner = document.getElementById('dossier_allergy_banner');
    const allergyText = document.getElementById('dossier_allergy_text');
    if (hasRecordedAllergies) {
        allergyBanner.style.display = 'flex';
        allergyText.textContent = med.allergies;
    } else {
        allergyBanner.style.display = 'none';
    }

    document.getElementById('dossier_vitals').textContent = med.vitals || 'Not recorded';
    document.getElementById('dossier_bmi').textContent = med.bmi || 'Not recorded';
    const details = {
        immunization: med.immunization,
        blood: med.bloodType,
        clinic: med.clinicLocation,
        doctor: med.clinicDoctor,
        condition: med.condition,
        allergies: med.allergies,
        medications: med.medications,
        review: med.nextClinicDate,
        notes: med.medicalNotes,
    };
    Object.entries(details).forEach(([field, value]) => {
        document.getElementById(`dossier_medical_${field}`).textContent = value || 'Not recorded';
    });

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
async function saveClinicalNote() {
    const noteText = document.getElementById('clinical_note_text').value.trim();
    if (!noteText) {
        alert('Enter the health note.');
        return;
    }

    const noteType = document.getElementById('clinical_note_type').options[document.getElementById('clinical_note_type').selectedIndex].text;
    const child = document.getElementById('note_child_name').value;
    const record = window.HopeHavenChildStore?.getById(window.HopeHavenSelectedChildId);
    if (record) {
        try {
            await window.HopeHavenDatabase.save('followup', {Child_Id:record.id,Followup_Date:new Date().toLocaleDateString('en-CA'),Followup_Remarks:noteType + ': ' + noteText});
            await window.HopeHavenDatabase.refresh();
        } catch (error) { showPortalToast(error.message, false); return; }
    } else {
        showPortalToast('Choose a saved child record first.', false); return;
    }

    closeDirectorModal('modal_add_clinical_note');

    if (typeof showPortalToast === 'function') {
        showPortalToast(`Health note saved for ${child}: [${noteType}] "${noteText}"`, 'success');
    } else {
        alert(`Health note saved for ${child}.`);
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
