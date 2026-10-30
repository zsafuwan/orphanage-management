<!-- Staff Safeguarding PDF Modal -->
<div class="director-modal-overlay" id="modal_staff_safeguarding_dossier">
    <div class="director-modal" style="max-width: 580px;">
        <div class="director-modal-head" style="background: #eef2ff; border-bottom: 1px solid #c7d2fe;">
            <h3><i class="fas fa-user-shield" style="color: #4f46e5;"></i> Staff Background Checks</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_staff_safeguarding_dossier')">&times;</button>
        </div>
        <div class="director-modal-body">
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #e2e8f0;">
                <div class="req-avatar" style="width: 48px; height: 48px; font-size: 16px;" id="dossier_staff_avatar">NM</div>
                <div>
                    <h2 id="dossier_staff_name" style="font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 2px;">Nurse Mary Anne Joseph</h2>
                    <div style="font-size: 12px; color: #64748b;" id="dossier_staff_role">Senior Nurse & Lead Caregiver · Ward Alpha</div>
                </div>
            </div>

            <div class="dossier-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 16px;">
                <div class="dossier-box">
                    <div class="dossier-box-label">PDRM Police Background Check</div>
                    <div class="dossier-box-value" style="color: #059669;"><i class="fas fa-check-circle"></i> Vetted & Cleared</div>
                </div>
                <div class="dossier-box">
                    <div class="dossier-box-label">Pediatric First Aid & CPR</div>
                    <div class="dossier-box-value" style="color: #059669;">Valid to Dec 2027</div>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 16px; font-size: 12px; color: #334155;">
                <div style="font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase;">Child Protection Accreditation</div>
                <div>JKM Registered Childcare License: <strong>JKM/PAKK/2026-9812</strong></div>
                <div style="margin-top: 4px;">Typhoid Vaccination: <strong>Administered Sep 2026 (Annual)</strong></div>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_staff_safeguarding_dossier')">Close</button>
            <button type="button" class="btn-sign" onclick="endorseCaregiverCredentials()">
                <i class="fas fa-stamp"></i> Renew Director Endorsement
            </button>
        </div>
    </div>
</div>
