<!-- ========================================================================= -->
<!-- MODAL 1: EDIT STAFF PROFILE DETAILS (Dedicated Edit Modal) -->
<!-- ========================================================================= -->
<div id="modal_edit_profile" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 600px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Edit Staff Profile Information</h3>
                <p class="modal-sub">Update your staff & Volunteers identification, assigned wards, and contact coordinates.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modal_edit_profile')">&times;</button>
        </div>

        <form id="modal_edit_profile_form" onsubmit="handleSaveModalProfile(event)">
            <div class="form-row">
                <!-- Full Name -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_fullname">
                        <i class="fas fa-signature"></i> Full Name *
                    </label>
                    <input type="text" id="modal_input_fullname" class="form-control" placeholder="e.g. Nurse Mary Jenkins" required>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_username">
                        <i class="fas fa-at"></i> Username *
                    </label>
                    <div class="input-prefix-group">
                        <span class="input-prefix-addon">@</span>
                        <input type="text" id="modal_input_username" class="form-control" placeholder="nurse.mary" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <!-- Affiliation -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_affiliation">
                        <i class="fas fa-hospital-alt"></i> Organization Affiliation *
                    </label>
                    <input type="text" id="modal_input_affiliation" class="form-control" placeholder="HopeHaven Orphanage – Child Welfare Unit" required>
                </div>

                <!-- Assigned Ward -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_ward">
                        <i class="fas fa-baby"></i> Assigned Wards *
                    </label>
                    <input type="text" id="modal_input_ward" class="form-control" placeholder="Ward Alpha & Beta Nursery" required>
                </div>
            </div>

            <div class="form-row">
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_email">
                        <i class="fas fa-envelope"></i> Official Staff Email *
                    </label>
                    <input type="email" id="modal_input_email" class="form-control" placeholder="mary.jenkins@hopehaven.org" required>
                </div>

                <!-- No. Phone -->
                <div class="form-group">
                    <label class="form-label" for="modal_input_phone">
                        <i class="fas fa-phone"></i> Telephone / Emergency Mobile *
                    </label>
                    <input type="tel" id="modal_input_phone" class="form-control" placeholder="+60 12-345 6789" required>
                </div>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label class="form-label" for="modal_input_address">
                    <i class="fas fa-home"></i> Staff Residential Address & Quarters *
                </label>
                <textarea id="modal_input_address" class="form-control" rows="2" placeholder="HopeHaven Orphanage Staff Quarters, Block B Room 204..." required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_edit_profile')">Cancel</button>
                <button type="submit" class="btn-primary" id="btn_modal_save_profile" style="background: #059669; border-color: #047857;">
                    <i class="fas fa-check-circle"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: CHANGE PASSWORD -->
<!-- ========================================================================= -->
<div id="modal_change_password" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Update Staff Password</h3>
                <p class="modal-sub">Ensure your caregiver account remains safeguarded with a secure password.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('modal_change_password')">&times;</button>
        </div>

        <form id="change_password_form" onsubmit="handleChangePassword(event)">
            <div class="form-group">
                <label class="form-label" for="current_password">Current Password *</label>
                <div style="position: relative;">
                    <input type="password" id="current_password" class="form-control" placeholder="Enter existing password" required>
                    <button type="button" class="field-action-btn" onclick="toggleInputType('current_password', this)" style="position: absolute; right: 12px; top: 10px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="new_password">New Password *</label>
                <div style="position: relative;">
                    <input type="password" id="new_password" class="form-control" placeholder="Minimum 8 characters" minlength="8" required onkeyup="evaluateNewPasswordStrength(this.value)">
                    <button type="button" class="field-action-btn" onclick="toggleInputType('new_password', this)" style="position: absolute; right: 12px; top: 10px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <!-- Dynamic Strength Meter -->
                <div style="margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; color: var(--dark-muted);">
                        <span>Password Strength:</span>
                        <strong id="strength_text" style="color: #ef4444;">Weak</strong>
                    </div>
                    <div style="height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden;">
                        <div id="strength_bar" style="height: 100%; width: 20%; background: #ef4444; transition: all 0.3s ease;"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_new_password">Confirm New Password *</label>
                <div style="position: relative;">
                    <input type="password" id="confirm_new_password" class="form-control" placeholder="Re-type new password" minlength="8" required>
                    <button type="button" class="field-action-btn" onclick="toggleInputType('confirm_new_password', this)" style="position: absolute; right: 12px; top: 10px;">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 12px; margin-bottom: 20px;">
                <div style="font-size: 11.5px; font-weight: 700; color: var(--dark); margin-bottom: 4px;">Security Policy Requirements:</div>
                <ul style="font-size: 11px; color: var(--dark-muted); padding-left: 18px; margin: 0; line-height: 1.6;">
                    <li>Minimum 8 alphanumeric characters</li>
                    <li>At least 1 special symbol (e.g. #, $, @, !)</li>
                    <li>Complies with Child Welfare Digital Safeguarding protocols</li>
                </ul>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal_change_password')">Cancel</button>
                <button type="submit" class="btn-primary" id="btn_save_password" style="background: #059669; border-color: #047857;">
                    <i class="fas fa-check-circle"></i> Save New Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: DIGITAL STAFF ID -->
<!-- ========================================================================= -->
<div id="modal_qr_id" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 400px; text-align: center;">
        <div class="modal-header" style="justify-content: center; position: relative;">
            <h3 class="modal-title">Official Orphanage Staff Pass</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modal_qr_id')" style="position: absolute; right: 0; top: 0;">&times;</button>
        </div>

        <div style="padding: 20px 0;">
            <div style="width: 140px; height: 140px; margin: 0 auto 16px auto; background: #ffffff; border: 2px solid #10b981; border-radius: 12px; padding: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);">
                <i class="fas fa-qrcode" style="font-size: 110px; color: #047857;"></i>
            </div>
            <h4 style="font-size: 16px; font-weight: 800; color: var(--dark); margin-bottom: 4px;" id="modal_pass_name">Nurse Mary Jenkins</h4>
            <div style="font-size: 12px; font-weight: 700; color: #059669; margin-bottom: 8px;" id="modal_pass_role">Senior Caregiver · Ward Operations</div>
            <div style="font-size: 11.5px; font-family: 'JetBrains Mono', monospace; color: var(--dark-muted);">ID: HH-STF-2022-089 • Clearance: Level 2</div>
        </div>

        <div class="modal-footer" style="justify-content: center;">
            <button type="button" class="btn-primary" style="background: #059669; border-color: #047857;" onclick="closeModal('modal_qr_id'); showPortalToast('Staff Digital Pass saved to Apple Wallet / Google Pay format!', true);">
                <i class="fas fa-download"></i> Save to Wallet
            </button>
        </div>
    </div>
</div>
