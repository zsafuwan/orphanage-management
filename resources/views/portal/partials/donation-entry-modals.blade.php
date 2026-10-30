<div id="modal_staff_record_donation" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 540px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Record Donation</h3>
                <p class="modal-sub">Log cash, bank transfer, or in-kind supplies received by staff.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffDonationModal('modal_staff_record_donation')">&times;</button>
        </div>

        <form onsubmit="handleStaffDonationSubmit(event)">
            <div class="form-group">
                <label class="form-label">Donor Name *</label>
                <input type="text" id="staff_don_name" class="form-control" placeholder="e.g. Mr. Robert Chen / Al-Barakah Community Trust" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Contribution Type *</label>
                    <select id="staff_don_type" class="form-control" onchange="toggleDonationTypeFields()" required>
                        <option value="Monetary">Monetary ($ Cash / Transfer)</option>
                        <option value="In-Kind">In-Kind (Material Goods)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount ($ USD) / Fair Value *</label>
                    <input type="number" id="staff_don_amount" class="form-control" value="250.00" min="1" step="0.5" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Payment Method *</label>
                    <select id="staff_don_channel" class="form-control" required>
                        <option value="Cash at Front Desk">Cash at Front Desk</option>
                        <option value="Direct Bank Wire">Direct Bank Wire</option>
                        <option value="Cheque Deposit">Cheque Deposit</option>
                        <option value="Online Payment Gateway">Online Payment Gateway</option>
                        <option value="Hand-Delivered Goods">Hand-Delivered Goods</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Donor Contact (Email or Phone)</label>
                <input type="text" id="staff_don_contact" class="form-control" placeholder="e.g. robert.chen@mail.com / +60 12-388 9911">
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea id="staff_don_notes" class="form-control" rows="2" placeholder="e.g. Received at morning shift gate. Official paper receipt # handed to donor..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffDonationModal('modal_staff_record_donation')">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #10b981; border-color: #10b981;">
                    <i class="fas fa-check"></i> Save & Create Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==========================================================================
     MODAL 2: VIEW / PRINT OFFICIAL RECEIPT
     ========================================================================== -->
<div id="modal_staff_view_receipt" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 540px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Official Donation Receipt</h3>
                <p class="modal-sub" id="receipt_modal_ref">Receipt No: RCP-2026-0881</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffDonationModal('modal_staff_view_receipt')">&times;</button>
        </div>

        <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px; background: #fafbfc;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
                <div>
                    <h4 style="font-size: 16px; font-weight: 800; color: #065f46; margin: 0 0 2px 0;">HopeHaven Orphanage</h4>
                    <span style="font-size: 11px; color: #64748b;">Registered Non-Profit orphanage &bull; Tax Exemption Reg #MY-99482</span>
                </div>
                <div style="text-align: right; font-size: 12px;">
                    <div id="receipt_modal_date">Date: Oct 20, 2026</div>
                    <span class="badge badge-success">Official Receipt</span>
                </div>
            </div>

            <div style="font-size: 12.5px; display: flex; flex-direction: column; gap: 8px;">
                <div>Received from: <strong id="receipt_modal_donor">The Meridian Heritage Foundation</strong></div>
                <div>Amount Recognized: <strong style="font-size: 18px; color: #065f46;" id="receipt_modal_amount">$5,000.00 USD</strong></div>
                <div>Payment Method: <strong id="receipt_modal_method">Direct Bank Wire</strong></div>
                <div>Allocated Fund: <strong id="receipt_modal_fund">Child Medical & Dental Care</strong></div>
                <div>Receiver: <strong id="receipt_modal_staff">Nurse Mary (Duty Staff Caregiver)</strong></div>
            </div>

            <div style="margin-top: 18px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #64748b; font-style: italic;">
                "HopeHaven certifies that this contribution has been officially banked into the dedicated orphanage escrow account. Thank you for empowering our residential children."
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-outline" onclick="closeStaffDonationModal('modal_staff_view_receipt')">Close</button>
            <button type="button" class="btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Official Receipt</button>
        </div>
    </div>
</div>

