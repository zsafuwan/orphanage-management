<!-- Institutional Grant Release Modal -->
<div class="director-modal-overlay" id="modal_grant_release">
    <div class="director-modal" style="max-width: 580px;">
        <div class="director-modal-head" style="background: #ecfdf5; border-bottom: 1px solid #a7f3d0;">
            <h3><i class="fas fa-hand-holding-usd" style="color: #059669;"></i> Director Institutional Grant Release</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_grant_release')">&times;</button>
        </div>
        <div class="director-modal-body">
            <div style="margin-bottom: 16px;">
                <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Institutional Grantor</label>
                <div id="grant_release_grantor" style="font-size: 16px; font-weight: 800; color: #0f172a;">Yayasan Hasanah</div>
            </div>

            <div class="dossier-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 16px;">
                <div class="dossier-box">
                    <div class="dossier-box-label">Approved Grant Total</div>
                    <div class="dossier-box-value" id="grant_release_total" style="color: #4f46e5;">RM 50,000.00</div>
                </div>
                <div class="dossier-box">
                    <div class="dossier-box-label">Next Tranche Allocation</div>
                    <div class="dossier-box-value" id="grant_release_tranche" style="color: #059669;">RM 12,500.00</div>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label for="grant_director_notes" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">Director Allocation Directive</label>
                <textarea id="grant_director_notes" rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; font-family: inherit; outline: none;" placeholder="e.g. Disburse Q4 allocation directly to SPM Candidate Tuition & Exam Fee Ledger."></textarea>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_grant_release')">Cancel</button>
            <button type="button" class="btn-sign" style="background: #059669;" onclick="confirmGrantRelease()">
                <i class="fas fa-check-double"></i> Authorize Tranche Disbursement
            </button>
        </div>
    </div>
</div>
