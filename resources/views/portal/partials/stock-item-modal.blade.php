<div class="portal-modal-backdrop" id="modal_staff_add_stock">
    <div class="portal-modal" style="max-width:620px;">
        <div class="modal-header">
            <div><h3 class="modal-title">Add Stock Item</h3><p class="modal-sub">Record an item and how many are currently in stock.</p></div>
            <button type="button" class="modal-close-btn" aria-label="Close stock item form" onclick="closeStaffInvModal('modal_staff_add_stock')">&times;</button>
        </div>
        <form onsubmit="handleAddStockItem(event)">
            <div class="form-group"><label for="stock_item_name" class="form-label">Item Name *</label><input id="stock_item_name" type="text" class="form-control" maxlength="160" placeholder="e.g. First aid bandages" required></div>
            <div class="form-row">
                <div class="form-group"><label for="stock_item_category" class="form-label">Category *</label><select id="stock_item_category" class="form-control" required><option value="Medical">Medical</option><option value="Hygiene">Hygiene</option><option value="Nutrition">Food & Nutrition</option><option value="Education">Education</option><option value="Linen">Clothing & Bedding</option></select></div>
                <div class="form-group"><label for="stock_item_unit" class="form-label">Unit *</label><input id="stock_item_unit" type="text" class="form-control" maxlength="60" placeholder="e.g. boxes, bottles, packs" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="stock_item_qty" class="form-label">Current Stock Count *</label><input id="stock_item_qty" type="number" min="0" step="1" class="form-control" value="0" required></div>
                <div class="form-group"><label for="stock_item_min" class="form-label">Low Stock Alert At *</label><input id="stock_item_min" type="number" min="0" step="1" class="form-control" value="5" required></div>
            </div>
            <div class="form-group"><label for="stock_item_location" class="form-label">Storage Location *</label><input id="stock_item_location" type="text" class="form-control" maxlength="160" placeholder="e.g. Main storeroom, Shelf A" required></div>
            <div class="form-row">
                <div class="form-group"><label for="stock_item_batch" class="form-label">Batch Number</label><input id="stock_item_batch" type="text" class="form-control" maxlength="80" placeholder="Optional"></div>
                <div class="form-group"><label for="stock_item_expiry" class="form-label">Expiry Date</label><input id="stock_item_expiry" type="date" data-open-picker class="form-control"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn-outline" onclick="closeStaffInvModal('modal_staff_add_stock')">Cancel</button><button type="submit" class="btn-primary"><i class="fas fa-check" aria-hidden="true"></i> Save Stock Item</button></div>
        </form>
    </div>
</div>
