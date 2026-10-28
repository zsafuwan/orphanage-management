<!-- Staff Onboarding Success & Credential Slip Modal -->
<div class="director-modal-overlay" id="modal_staff_onboarding_success">
    <div class="director-modal" style="max-width: 580px;">
        <div class="director-modal-head" style="background: #ecfdf5; border-bottom: 1px solid #a7f3d0;">
            <h3><i class="fas fa-id-card-clip" style="color: #059669;"></i> Staff Account Created</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_staff_onboarding_success')">&times;</button>
        </div>
        <div class="director-modal-body">
            <div style="text-align: center; margin-bottom: 20px;">
                <div style="width: 54px; height: 54px; background: #ecfdf5; color: #059669; border-radius: 50%; font-size: 24px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                    <i class="fas fa-check"></i>
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: #0f172a;" id="success_staff_name">Caregiver Name</h2>
                <div style="font-size: 13px; color: #64748b;" id="success_staff_role">Senior Caregiver · Ward Alpha</div>
            </div>

            <!-- Credential Box -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 18px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #64748b;">Staff Access ID:</span>
                    <strong id="success_staff_id" style="font-family: monospace; color: #4f46e5; font-size: 14px;">HH-CARE-09</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #64748b;">Orphanage Wing:</span>
                    <strong id="success_staff_wing">Ward Alpha (Nursery)</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span style="color: #64748b;">Authorizing Director:</span>
                    <strong>Sister Sarah (Director)</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px dashed #cbd5e1; font-size: 13px;">
                    <span style="color: #64748b;">Temporary Password:</span>
                    <strong id="success_staff_password" style="font-family: monospace; color: #059669;">Caregiver@2026</strong>
                </div>
            </div>

            <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 12px; font-size: 12px; color: #312e81; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-shield-halved" style="color: #4f46e5; font-size: 16px;"></i>
                <span>PDRM police check logged. Staff member is approved to commence child caregiving duties.</span>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_staff_onboarding_success')">Close</button>
            <button type="button" class="btn-director-review" onclick="printStaffCredentialSlip()">
                <i class="fas fa-print"></i> Print Credential Slip
            </button>
            <button type="button" class="btn-sign" onclick="copyStaffCredentials()">
                <i class="fas fa-copy"></i> Copy Credentials
            </button>
        </div>
    </div>
</div>
