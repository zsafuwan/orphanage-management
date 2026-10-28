<!-- Full Clinical PDF Modal -->
<div class="director-modal-overlay" id="modal_child_medical_dossier">
    <div class="director-modal" style="max-width: 680px;">
        <div class="director-modal-head" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0;">
            <h3><i class="fas fa-notes-medical" style="color: #059669;"></i> Official Child Clinical PDF</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_child_medical_dossier')">&times;</button>
        </div>
        <div class="director-modal-body">
            <!-- Header Identity Banner -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #e2e8f0;">
                <div>
                    <h2 id="dossier_child_name" style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 3px;">Aminah Binti Abdullah</h2>
                    <div style="font-size: 12px; color: #64748b; display: flex; gap: 8px;">
                        <span id="dossier_child_meta">Age: 8 yrs · Ward Gamma</span>
                        <span>·</span>
                        <span id="dossier_child_mykid">MyKid: 160412-14-5582</span>
                    </div>
                </div>
                <div>
                    <span id="dossier_blood_badge" class="blood-badge" style="font-size: 13px; padding: 4px 10px;">Blood Type: O+</span>
                </div>
            </div>

            <!-- Allergy Alert Banner -->
            <div id="dossier_allergy_banner" style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-triangle-exclamation" style="color: #e11d48; font-size: 20px;"></i>
                <div>
                    <div style="font-size: 12px; font-weight: 800; color: #9f1239; text-transform: uppercase;">Medical Warning: Active Allergies</div>
                    <div id="dossier_allergy_text" style="font-size: 13px; color: #881337; font-weight: 600;">Severe Penicillin & Amoxicillin allergy. Administer Erythromycin alternatives only.</div>
                </div>
            </div>

            <!-- Vitals Grid -->
            <div class="dossier-grid">
                <div class="dossier-box">
                    <div class="dossier-box-label">Height & Weight</div>
                    <div class="dossier-box-value" id="dossier_vitals">124 cm · 24.5 kg</div>
                </div>
                <div class="dossier-box">
                    <div class="dossier-box-label">BMI & Growth Status</div>
                    <div class="dossier-box-value" id="dossier_bmi" style="color: #059669;">16.0 (Healthy Normal)</div>
                </div>
                <div class="dossier-box">
                    <div class="dossier-box-label">Immunization Index</div>
                    <div class="dossier-box-value" id="dossier_immunization" style="color: #059669;">Not recorded</div>
                </div>
            </div>

            <!-- Active Prescriptions -->
            <div style="margin-bottom: 18px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">Active Medication & Caregiver Instructions</label>
                <div id="dossier_medications" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 13px; color: #334155;">
                    <!-- Dynamically populated -->
                </div>
            </div>

            <!-- Clinical Care Timeline -->
            <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 8px;">Recent Clinical Milestones</label>
            <div class="dossier-timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-title">Routine Pediatric Physical Checkup</div>
                    <div class="timeline-desc">Oct 14, 2026 · Dr. Raymond Lee (Klinik Kesihatan Titiwangsa). Normal heart sounds, clear lungs.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: #4f46e5; box-shadow: 0 0 0 2px #4f46e5;"></div>
                    <div class="timeline-title">Annual Dental Assessment</div>
                    <div class="timeline-desc">Aug 20, 2026 · Volunteer Dental Clinic. Fluoride varnish applied, zero cavities.</div>
                </div>
            </div>

            <!-- Attending Doctor & Clinic -->
            <div style="background: #f1f5f9; border-radius: 8px; padding: 12px 16px; font-size: 12px; color: #475569; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>Attending Physician:</strong> <span id="dossier_doctor">Dr. Raymond Lee (Pediatrician, Reg #42981)</span><br>
                    <strong>Emergency Clinic:</strong> Hospital Kuala Lumpur (HKL) Pediatric Wing
                </div>
                <button type="button" class="btn-dossier" style="background: #4f46e5;" onclick="printHealthPassport()">
                    <i class="fas fa-print"></i> Health Passport
                </button>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_child_medical_dossier')">Close</button>
            <button type="button" class="btn-dossier" onclick="openAddClinicalNoteModal()">
                <i class="fas fa-plus"></i> Add Clinical Observation
            </button>
        </div>
    </div>
</div>

<!-- Add Clinical Observation Note Modal -->
<div class="director-modal-overlay" id="modal_add_clinical_note">
    <div class="director-modal">
        <div class="director-modal-head">
            <h3><i class="fas fa-stethoscope" style="color: #059669;"></i> Add Director Health Note</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_add_clinical_note')">&times;</button>
        </div>
        <div class="director-modal-body">
            <div style="margin-bottom: 14px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Child Name / File</label>
                <input type="text" id="note_child_name" readonly style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; font-size: 13px; font-weight: 600;">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="clinical_note_type" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Observation Type</label>
                <select id="clinical_note_type" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; outline: none;">
                    <option value="routine">Routine Dietary / Growth Observation</option>
                    <option value="clinic_followup">Clinic / Hospital Follow-up Directive</option>
                    <option value="prescription_adjustment">Medication Dosage Adjustment</option>
                    <option value="allergy_update">Allergy Watchlist Update</option>
                </select>
            </div>

            <div style="margin-bottom: 14px;">
                <label for="clinical_note_text" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Director Observation / Recommendation</label>
                <textarea id="clinical_note_text" rows="3" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; font-family: inherit; outline: none;" placeholder="e.g. Schedule allergy test repeat at Klinik Kesihatan Titiwangsa before school swimming session."></textarea>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_add_clinical_note')">Cancel</button>
            <button type="button" class="btn-dossier" onclick="saveClinicalNote()">
                <i class="fas fa-save"></i> Save to Health Record
            </button>
        </div>
    </div>
</div>
