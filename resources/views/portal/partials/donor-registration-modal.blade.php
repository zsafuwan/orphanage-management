<div id="modal_register_donor" class="portal-modal-backdrop">
    <div class="portal-modal" style="max-width: 480px;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Register Donor Profile</h3>
                <p class="modal-sub">Add individual, corporate or foundation patron.</p>
            </div>
            <button class="modal-close-btn" onclick="closeStaffDonationModal('modal_register_donor')">&times;</button>
        </div>

        <form onsubmit="handleRegisterDonor(event)">
            <div class="form-group">
                <label class="form-label">Donor / Entity Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Pacific Heritage Trust" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Entity Classification</label>
                    <select name="category" class="form-control">
                        <option>Foundation</option>
                        <option>Individual</option>
                        <option>Corporate</option>
                        <option>Faith Community</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Recognition Tier</label>
                    <select name="tier" class="form-control">
                        <option>Platinum Patron</option>
                        <option>Gold Donor</option>
                        <option>Silver Friend</option>
                        <option>Community Supporter</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email / Contact</label>
                <input type="email" name="contact" class="form-control" placeholder="giving@trust.org">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeStaffDonationModal('modal_register_donor')">Cancel</button>
                <button type="submit" class="btn-primary">Save Donor Profile</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: VIEW E-RECEIPT -->
