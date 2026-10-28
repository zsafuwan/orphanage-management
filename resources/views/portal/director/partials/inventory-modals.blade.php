<!-- Requisition Review & Authorization Modal -->
<div class="director-modal-overlay" id="modal_director_sign_req">
    <div class="director-modal">
        <div class="director-modal-head">
            <h3><i class="fas fa-signature" style="color: #4f46e5;"></i> Review Supply Request</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_director_sign_req')">&times;</button>
        </div>
        <div class="director-modal-body">
            <div class="modal-info-grid">
                <div class="modal-info-item">
                    <label>Request Ref</label>
                    <span id="sign_req_id">REQ-2026-104</span>
                </div>
                <div class="modal-info-item">
                    <label>Requested By</label>
                    <span id="sign_req_staff">Nurse Mary (Ward Alpha)</span>
                </div>
                <div class="modal-info-item">
                    <label>Delivery Location</label>
                    <span id="sign_req_dept">Medical & Sickbay</span>
                </div>
                <div class="modal-info-item">
                    <label>Current Stock</label>
                    <span id="sign_req_cost" style="color: #4f46e5; font-size: 15px; font-weight: 800;">RM 680.00</span>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">Requested Items Summary</label>
                <div id="sign_req_items" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 13px; color: #334155;">
                    <!-- Dynamically populated -->
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">Why Supplies Are Needed</label>
                <div id="sign_req_justification" style="font-size: 13px; color: #475569; font-style: italic; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 10px 12px;">
                    "Current nebulizer unit malfunctioning; 2 asthmatic children need regular therapy."
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label for="director_approval_remarks" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">Director Notes</label>
                <textarea id="director_approval_remarks" rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; font-family: inherit; outline: none;" placeholder="e.g. Approved. Please arrange delivery and confirm receipt."></textarea>
            </div>

            <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 12px; display: flex; align-items: center; gap: 12px;">
                <input type="checkbox" id="director_auth_check" style="width: 18px; height: 18px; accent-color: #4f46e5; cursor: pointer;">
                <label for="director_auth_check" style="font-size: 12px; color: #312e81; font-weight: 600; cursor: pointer;">
                    I approve this supply request. Stock will be added only after staff confirm that the supplies have arrived.
                </label>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_director_sign_req')">Cancel</button>
            <button type="button" class="btn-sign" id="btn_confirm_director_sign" onclick="submitDirectorSign()">
                <i class="fas fa-check-double"></i> Approve Request
            </button>
        </div>
    </div>
</div>

<!-- Rejection / Request Revision Modal -->
<div class="director-modal-overlay" id="modal_director_reject_req">
    <div class="director-modal">
        <div class="director-modal-head">
            <h3><i class="fas fa-reply" style="color: #e11d48;"></i> Return Request to Staff</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_director_reject_req')">&times;</button>
        </div>
        <div class="director-modal-body">
            <p style="font-size: 13px; color: #64748b; margin-bottom: 14px;">
                Tell staff why the request needs changes. Your feedback will appear with the request.
            </p>

            <div style="margin-bottom: 14px;">
                <label style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Request Reference</label>
                <input type="text" id="reject_req_id_input" readonly style="width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; font-size: 13px; font-weight: 600;">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="director_reject_reason" style="font-size: 12px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Feedback / Rejection Reason</label>
                <textarea id="director_reject_reason" rows="3" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 13px; font-family: inherit; outline: none;" placeholder="e.g. Please check the quantity and submit an updated request."></textarea>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_director_reject_req')">Cancel</button>
            <button type="button" class="btn-reject-modal" onclick="submitDirectorReject()">
                <i class="fas fa-paper-plane"></i> Send Feedback to Staff
            </button>
        </div>
    </div>
</div>
