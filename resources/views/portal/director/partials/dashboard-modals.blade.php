<!-- Donation Proof of Payment Viewer Modal -->
<div class="director-modal-overlay" id="modal_donation_proof">
    <div class="director-modal" style="max-width: 580px;">
        <div class="director-modal-head" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0;">
            <h3><i class="fas fa-file-invoice-dollar" style="color: #059669;"></i> Verified Proof of Donation</h3>
            <button type="button" class="director-modal-close" onclick="closeDirectorModal('modal_donation_proof')">&times;</button>
        </div>
        <div class="director-modal-body">
            <!-- Slip Visual Preview Card -->
            <div class="slip-preview-card">
                <div class="slip-stamp-badge" id="proof_stamp_badge">
                    <i class="fas fa-shield-check"></i> Verified Digital Bank Slip
                </div>
                <div class="slip-bank-header" id="proof_bank_name">Maybank Islamic Berhad</div>
                <div style="font-size: 11px; color: #64748b; margin-bottom: 6px;">Instant DuitNow / Interbank GIRO Advice</div>
                <div class="slip-amount-text" id="proof_amount">RM 10,000.00</div>
                <div style="font-size: 12px; font-weight: 700; color: #475569;" id="proof_purpose">Designated: Emergency Medical & Pediatric Care Fund</div>

                <table class="slip-details-table">
                    <tr>
                        <td>Donor Name:</td>
                        <td id="proof_donor_name">Tan Sri Dr. Jeffrey Cheah</td>
                    </tr>
                    <tr>
                        <td>Beneficiary:</td>
                        <td>Pertubuhan Kebajikan HopeHaven Sanctuary</td>
                    </tr>
                    <tr>
                        <td>Trustee Account:</td>
                        <td>5642-1823-9901 (Maybank Islamic)</td>
                    </tr>
                    <tr>
                        <td>Transaction Reference:</td>
                        <td id="proof_tx_ref" style="font-family: monospace;">MBB-20261019-9821</td>
                    </tr>
                    <tr>
                        <td>Date & Time:</td>
                        <td id="proof_timestamp">19 Oct 2026, 14:22:18 MYT</td>
                    </tr>
                    <tr>
                        <td>Tax Exemption Ref:</td>
                        <td id="proof_tax_ref">LHDN.01/35/42/51/179-6.8821</td>
                    </tr>
                    <tr>
                        <td>Verification Status:</td>
                        <td id="proof_status_text"><span style="color: #059669; font-weight: 800;"><i class="fas fa-check-circle"></i> Authenticated by Director</span></td>
                    </tr>
                </table>
            </div>

            <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 12px 16px; font-size: 12px; color: #312e81; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <i class="fas fa-certificate" style="color: #4f46e5; margin-right: 6px;"></i>
                    <span>Official Tax Exemption Section 44(6) ITA 1967</span>
                </div>
                <button type="button" class="btn-director-review" style="padding: 4px 10px; font-size: 11px;" onclick="downloadOfficialTaxReceipt()">
                    <i class="fas fa-file-pdf"></i> Download Receipt
                </button>
            </div>
        </div>
        <div class="director-modal-foot">
            <button type="button" class="btn-tab" style="background:#f1f5f9; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;" onclick="closeDirectorModal('modal_donation_proof')">Close</button>
            <button type="button" class="btn-verify-ack" id="btn_modal_verify_action" onclick="acknowledgeDonationLetter()">
                <i class="fas fa-envelope-open-text"></i> Dispatch Director Acknowledgment Letter
            </button>
        </div>
    </div>
</div>
