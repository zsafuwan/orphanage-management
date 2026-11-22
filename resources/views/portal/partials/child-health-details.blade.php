<article class="record-summary-panel record-health-panel">
    @if ($showHealthHeading ?? true)
        <h4><i class="fas fa-notes-medical" aria-hidden="true"></i> Health &amp; care details</h4>
    @endif
    <dl class="record-facts record-health-facts">
        <div><dt>Vaccinations</dt><dd id="dossier_medical_immunization"></dd></div>
        <div><dt>Blood type</dt><dd id="dossier_medical_blood"></dd></div>
        <div><dt>Clinic</dt><dd id="dossier_medical_clinic"></dd></div>
        <div><dt>Doctor</dt><dd id="dossier_medical_doctor"></dd></div>
        <div><dt>Medical condition</dt><dd id="dossier_medical_condition"></dd></div>
        <div><dt>Allergies</dt><dd id="dossier_medical_allergies"></dd></div>
        <div><dt>Medicines</dt><dd id="dossier_medical_medications"></dd></div>
        <div><dt>Next clinic visit</dt><dd id="dossier_medical_review"></dd></div>
        <div class="record-care-notes"><dt>Care notes</dt><dd id="dossier_medical_notes"></dd></div>
    </dl>
</article>
