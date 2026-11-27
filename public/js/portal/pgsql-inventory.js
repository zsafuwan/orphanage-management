/* Connect Inventory, Item Category, Orphanage, Stock Issue, Stock Receipt, and Supplier views & forms to remote PostgreSQL (10.213.198.128 / workshop2_izzah). */
(() => {
    'use strict';

    const SYNC_KEY = 'hopehaven_pgsql_inventory_sync_ts';
    const esc = value => window.HopeHavenSync ? window.HopeHavenSync.escape(value) : String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const today = () => new Date().toLocaleDateString('en-CA');
    const itemCode = id => 'WINV-' + String(id).padStart(3, '0');

    const normalizeCategoryForUi = rawCat => {
        const s = String(rawCat || '').toLowerCase();
        if (s.includes('med') || s.includes('first aid') || s.includes('clinic')) return 'Medical';
        if (s.includes('hyg') || s.includes('nurs') || s.includes('diaper') || s.includes('wipe')) return 'Hygiene';
        if (s.includes('nutr') || s.includes('food') || s.includes('milk')) return 'Nutrition';
        if (s.includes('edu') || s.includes('school') || s.includes('book') || s.includes('stationery')) return 'Education';
        if (s.includes('lin') || s.includes('cloth') || s.includes('bed')) return 'Linen';
        return 'Medical';
    };

    const api = async (url, payload) => {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch(url, {
            method: payload ? 'POST' : 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'Accept': 'application/json',
                ...(payload ? {'Content-Type': 'application/json'} : {}),
                ...(csrf ? {'X-CSRF-TOKEN': csrf} : {})
            },
            ...(payload ? {body: JSON.stringify(payload)} : {})
        });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) {
            const msg = response.status === 422
                ? Object.values(result.errors || {}).flat().join(' ')
                : (result.message || 'Could not save or load remote PostgreSQL inventory records.');
            throw new Error(msg);
        }
        return result;
    };

    const notify = (msg, ok = true) => {
        if (typeof window.showPortalToast === 'function') window.showPortalToast(msg, ok);
        else alert(msg);
    };

    const broadcastSync = () => {
        try {
            if (window.HopeHavenSync) window.HopeHavenSync.write(SYNC_KEY, String(Date.now()));
            else localStorage.setItem(SYNC_KEY, String(Date.now()));
        } catch (_) {}
    };

    const db = window.HopeHavenPgsqlInventory = {
        data: {
            inventory: [],
            'item-category': [],
            orphanage: [],
            'stock-issue': [],
            'stock-receipt': [],
            supplier: []
        },
        save: (endpoint, payload) => api('/inventory-data/' + endpoint, payload),
        async refresh() {
            const snapshot = await api('/inventory-data');
            db.data = Object.assign({}, db.data, snapshot);
            syncInventoryStore();
            renderAdminInventoryPage();
            document.dispatchEvent(new CustomEvent('pgsql-inventory-loaded', {detail: db.data}));
            return db.data;
        }
    };

    function parseIssuePurpose(purposeText) {
        const raw = String(purposeText || 'Ward Care Distribution');
        const childMatch = raw.match(/^Child:\s*([^·]+?)\s*·\s*(.+)$/i);
        if (childMatch) {
            return {childName: childMatch[1].trim(), reason: childMatch[2].trim()};
        }
        return {childName: 'Residential Ward Child', reason: raw};
    }

    function getMappedItems() {
        const inv = db.data.inventory || [];
        const cats = db.data['item-category'] || [];
        const homes = db.data.orphanage || [];

        return inv.map(row => {
            const cat = cats.find(c => Number(c.category_id) === Number(row.category_id));
            const home = homes.find(h => Number(h.orphanage_id) === Number(row.orphanage_id));
            const rawCat = cat ? cat.category_name : 'Medical';
            return {
                id: itemCode(row.item_id),
                dbId: Number(row.item_id),
                name: row.name,
                category: normalizeCategoryForUi(rawCat),
                rawCategory: rawCat,
                location: home ? home.name : 'Ward Alpha Cupboard',
                onHand: Number(row.quantity) || 0,
                minThreshold: Number(row.minimum_stock) || 5,
                unit: 'units',
                batchNo: 'PG-' + String(row.item_id).padStart(3, '0'),
                expiry: 'N/A'
            };
        });
    }

    function getMappedDispensations() {
        const issues = db.data['stock-issue'] || [];
        const inv = db.data.inventory || [];
        const cats = db.data['item-category'] || [];

        return issues.map(iss => {
            const item = inv.find(i => Number(i.item_id) === Number(iss.item_id));
            const cat = item ? cats.find(c => Number(c.category_id) === Number(item.category_id)) : null;
            const parsed = parseIssuePurpose(iss.purpose);
            return {
                id: 'ISS-' + new Date().getFullYear() + '-' + String(iss.issue_id).padStart(3, '0'),
                dbId: Number(iss.issue_id),
                childName: parsed.childName,
                itemName: item ? item.name : ('Item #' + iss.item_id),
                qty: `${iss.quantity} units`,
                category: normalizeCategoryForUi(cat ? cat.category_name : 'Medical'),
                reason: parsed.reason,
                dispensedBy: 'Nurse Mary',
                timestamp: iss.issue_date || today()
            };
        });
    }

    function syncInventoryStore() {
        if (!window.HopeHavenInventory) return;
        const mappedItems = getMappedItems();
        const mappedDispensations = getMappedDispensations();

        if (mappedItems.length > 0) {
            window.HopeHavenInventory.saveItems(mappedItems);
        }
        if (mappedDispensations.length > 0) {
            window.HopeHavenInventory.saveDispensations(mappedDispensations);
        }
    }

    // Render Admin Inventory Page (/inventory) if on that page
    function renderAdminInventoryPage() {
        const stockTbody = document.querySelector('#tab_stock .portal-table tbody');
        const movementsTbody = document.getElementById('movements_tbody');
        const suppliersGrid = document.querySelector('#tab_suppliers > div');
        const receiptSelect = document.getElementById('receipt_item_select');
        const issueSelect = document.querySelector('#modal_stock_issue select');

        const mappedItems = getMappedItems();
        const receipts = db.data['stock-receipt'] || [];
        const issues = db.data['stock-issue'] || [];
        const suppliers = db.data.supplier || [];
        const inv = db.data.inventory || [];

        // Update KPI metrics on /inventory
        if (stockTbody && (mappedItems.length > 0 || suppliers.length > 0)) {
            const kpis = document.querySelectorAll('.metrics-grid .metric-card');
            if (kpis.length >= 4) {
                if (mappedItems.length > 0) {
                    const lowCount = mappedItems.filter(i => i.onHand <= i.minThreshold).length;
                    kpis[0].querySelector('.metric-sub').textContent = `${mappedItems.length} active SKU lines in PostgreSQL`;
                    kpis[1].querySelector('.metric-val').textContent = `${lowCount} Items`;
                }
                if (issues.length > 0) {
                    kpis[2].querySelector('.metric-val').textContent = `${issues.length} Distributions`;
                }
                if (suppliers.length > 0) {
                    kpis[3].querySelector('.metric-val').textContent = `${suppliers.length} Vendors`;
                }
            }
        }

        // Populate Item Dropdowns in #modal_stock_receipt and #modal_stock_issue
        if (receiptSelect && mappedItems.length > 0) {
            receiptSelect.innerHTML = mappedItems.map(i =>
                `<option value="${esc(i.dbId)}" data-name="${esc(i.name)}">${esc(i.name)} (${esc(i.onHand)} in stock)</option>`
            ).join('');
        }
        if (issueSelect && mappedItems.length > 0) {
            issueSelect.innerHTML = mappedItems.map(i =>
                `<option value="${esc(i.dbId)}" data-name="${esc(i.name)}">${esc(i.name)} (${esc(i.onHand)} in stock)</option>`
            ).join('');
        }

        // Tab 1: Stock Balances Table on /inventory
        if (stockTbody && mappedItems.length > 0) {
            stockTbody.innerHTML = mappedItems.map(item => {
                const isLow = item.onHand <= item.minThreshold;
                const pct = Math.min(100, Math.max(15, Math.round((item.onHand / Math.max(1, item.minThreshold * 2)) * 100)));
                return `
                    <tr class="searchable-item" data-category="${esc(item.rawCategory)}">
                        <td>
                            <strong>${esc(item.name)}</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">${esc(item.id)}</div>
                        </td>
                        <td><span class="badge badge-secondary">${esc(item.rawCategory)}</span></td>
                        <td>
                            <strong style="color: ${isLow ? '#ef4444' : '#0f172a'};">${esc(item.onHand)} units</strong>
                            <div style="height: 5px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 80px;">
                                <div style="height: 100%; background: ${isLow ? '#ef4444' : '#10b981'}; width: ${pct}%;"></div>
                            </div>
                        </td>
                        <td>Min: <strong>${esc(item.minThreshold)}</strong></td>
                        <td><span class="badge ${isLow ? 'badge-danger' : 'badge-success'}">${isLow ? 'Low Stock Alert' : 'Adequate'}</span></td>
                        <td style="font-size: 12px; color: #64748b;">${esc(item.location)}</td>
                        <td style="text-align: right;">
                            <button class="btn-outline btn-sm" onclick="openModal('modal_stock_issue')">Issue</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Tab 2: Receipts & Issues Ledger on /inventory
        if (movementsTbody && (receipts.length > 0 || issues.length > 0)) {
            const rows = [];
            receipts.forEach(r => {
                const item = inv.find(i => Number(i.item_id) === Number(r.item_id));
                const sup = suppliers.find(s => Number(s.supplier_id) === Number(r.supplier_id));
                rows.push(`
                    <tr>
                        <td><span class="badge badge-success"><i class="fas fa-arrow-down"></i> Receipt</span></td>
                        <td style="font-family: monospace; font-weight: 600;">REC-${new Date().getFullYear()}-${String(r.receipt_id).padStart(3, '0')}</td>
                        <td><strong>${esc(item ? item.name : 'Item #' + r.item_id)}</strong></td>
                        <td style="font-weight: 700; color: #10b981;">+${esc(r.quantity)} units</td>
                        <td style="font-size: 12px; color: #64748b;">${esc(r.receipt_date)}</td>
                        <td>${esc(r.received_from || sup?.supplier_name || 'Central Storeroom')}</td>
                        <td><span class="badge badge-secondary">PostgreSQL Stock Receipt</span></td>
                    </tr>
                `);
            });
            issues.forEach(iss => {
                const item = inv.find(i => Number(i.item_id) === Number(iss.item_id));
                rows.push(`
                    <tr>
                        <td><span class="badge badge-danger"><i class="fas fa-arrow-up"></i> Issue</span></td>
                        <td style="font-family: monospace; font-weight: 600;">ISS-${new Date().getFullYear()}-${String(iss.issue_id).padStart(3, '0')}</td>
                        <td><strong>${esc(item ? item.name : 'Item #' + iss.item_id)}</strong></td>
                        <td style="font-weight: 700; color: #ef4444;">-${esc(iss.quantity)} units</td>
                        <td style="font-size: 12px; color: #64748b;">${esc(iss.issue_date)}</td>
                        <td>${esc(iss.purpose || 'Residential Ward')}</td>
                        <td><span class="badge badge-info" style="font-family: monospace;">${esc(iss.request_item_id ? 'REQ-' + iss.request_item_id : 'Direct Issue')}</span></td>
                    </tr>
                `);
            });
            movementsTbody.innerHTML = rows.join('');
        }

        // Tab 4: Suppliers & Categories on /inventory
        if (suppliersGrid && suppliers.length > 0) {
            suppliersGrid.innerHTML = suppliers.map(s => `
                <div class="portal-card" style="padding: 22px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <h4 style="font-size: 15px; font-weight: 800;">${esc(s.supplier_name)}</h4>
                        <span class="badge badge-success">Verified</span>
                    </div>
                    <span class="badge badge-secondary" style="margin-top: 6px;">Supplier #${esc(s.supplier_id)}</span>
                    <div style="font-size: 12px; color: #64748b; margin-top: 14px; display: flex; flex-direction: column; gap: 4px;">
                        <div>Phone: <strong>${esc(s.contact_number || 'Not recorded')}</strong></div>
                        <div style="color: #10b981; font-weight: 700; margin-top: 4px;">Synced from PostgreSQL (10.213.198.128)</div>
                    </div>
                </div>
            `).join('');
        }
    }

    function extractDbIdFromCode(codeOrName) {
        const m = String(codeOrName || '').match(/^WINV-0*(\d+)$/i);
        if (m) return Number(m[1]);
        if (/^\d+$/.test(String(codeOrName || ''))) return Number(codeOrName);
        const found = (db.data.inventory || []).find(i => i.name.toLowerCase() === String(codeOrName || '').trim().toLowerCase());
        return found ? Number(found.item_id) : null;
    }

    // Intercept form submissions across all Inventory modals
    document.addEventListener('submit', async event => {
        const form = event.target;

        // 1. Add Stock Item Modal (#modal_staff_add_stock on /staff/inventory)
        if (form.closest('#modal_staff_add_stock')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const name = document.getElementById('stock_item_name')?.value?.trim() || '';
            const category = document.getElementById('stock_item_category')?.value || 'Medical';
            const qty = parseInt(document.getElementById('stock_item_qty')?.value, 10);
            const min = parseInt(document.getElementById('stock_item_min')?.value, 10);
            const location = document.getElementById('stock_item_location')?.value?.trim() || 'Central Storeroom';
            const batch = document.getElementById('stock_item_batch')?.value?.trim() || '';

            if (!name || !Number.isInteger(qty) || qty < 0) {
                notify('Please enter a valid item name and whole stock count.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                await db.save('item', {
                    name: name,
                    category: category,
                    location: location,
                    quantity: qty,
                    minimum_stock: Number.isInteger(min) && min >= 0 ? min : 5,
                    supplier_name: batch ? `Batch ${batch}` : 'HopeHaven Central Storeroom'
                });

                form.reset();
                window.closeStaffInvModal?.('modal_staff_add_stock');
                notify(`${name} saved to remote PostgreSQL inventory!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 2. Give Supplies to a Child Modal (#modal_dispense_to_child on /staff/inventory)
        if (form.closest('#modal_dispense_to_child')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const childName = document.getElementById('dispense_child_input')?.value?.trim() || '';
            const itemInput = document.getElementById('dispense_item_input')?.value?.trim() || '';
            const qty = parseInt(document.getElementById('dispense_qty_input')?.value, 10);
            const reason = document.getElementById('dispense_reason_input')?.value?.trim() || '';

            if (!childName || !itemInput || !Number.isInteger(qty) || qty < 1) {
                notify('Please complete child name, supply item, and positive quantity.', false);
                return;
            }

            const storeItem = window.HopeHavenInventory?.findItem(itemInput);
            if (storeItem && storeItem.onHand < qty) {
                alert(`Insufficient stock on hand in ${storeItem.location}. Current quantity is ${storeItem.onHand}.`);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const dbId = extractDbIdFromCode(storeItem?.id || itemInput);
                await db.save('issue', {
                    ...(dbId ? {item_id: dbId} : {}),
                    name: storeItem?.name || itemInput,
                    category: storeItem?.category || 'Medical',
                    location: storeItem?.location || 'Ward Alpha Cupboard',
                    quantity: qty,
                    purpose: `Child: ${childName} · ${reason}`.slice(0, 255)
                });

                form.reset();
                window.closeStaffInvModal?.('modal_dispense_to_child');
                notify(`Given ${qty}x ${storeItem?.name || itemInput} to ${childName} (saved to PostgreSQL stock_issue)!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 3. Adjust Ward Cupboard Count Modal (#modal_quick_count on /staff/inventory)
        if (form.closest('#modal_quick_count')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const itemCodeVal = document.getElementById('count_item_id')?.value || '';
            const itemNameVal = document.getElementById('count_item_name')?.value?.trim() || '';
            const newQty = parseInt(document.getElementById('count_new_qty')?.value, 10);
            const reason = document.getElementById('count_reason_select')?.value || 'Routine Shift Physical Verification';

            if (!Number.isInteger(newQty) || newQty < 0) {
                notify('Please enter a valid physical count of 0 or more.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const dbId = extractDbIdFromCode(itemCodeVal || itemNameVal);
                await db.save('adjust', {
                    ...(dbId ? {item_id: dbId} : {}),
                    name: itemNameVal,
                    quantity: newQty,
                    reason: reason
                });

                window.closeStaffInvModal?.('modal_quick_count');
                notify(`Stock count for ${itemNameVal} updated to ${newQty} in PostgreSQL!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 4. Request More Supplies Modal (#modal_staff_request_supplies on /staff/inventory)
        if (form.closest('#modal_staff_request_supplies')) {
            const cat = document.getElementById('req_category_select')?.value || 'Medical';
            const itemName = document.getElementById('req_item_input')?.value?.trim() || '';
            const dest = document.getElementById('req_destination_input')?.value?.trim() || 'Ward Alpha Cupboard';
            if (itemName) {
                const existing = (db.data.inventory || []).find(i => i.name.toLowerCase() === itemName.toLowerCase());
                if (!existing) {
                    db.save('item', {
                        name: itemName,
                        category: cat,
                        location: dest,
                        quantity: 0,
                        minimum_stock: 5
                    }).then(() => {
                        broadcastSync();
                        return db.refresh();
                    }).catch(() => {});
                }
            }
            return;
        }

        // 5. Admin Record Stock Receipt Modal (#modal_stock_receipt on /inventory)
        if (form.closest('#modal_stock_receipt')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const select = document.getElementById('receipt_item_select');
            const selectedOpt = select?.options[select.selectedIndex];
            const rawVal = select?.value || '';
            const dbId = /^\d+$/.test(rawVal) ? Number(rawVal) : null;
            const itemName = selectedOpt?.dataset?.name || selectedOpt?.text || rawVal || 'Inventory Item';
            const qty = parseInt(document.getElementById('receipt_qty_input')?.value, 10) || 1;
            const supplier = document.getElementById('receipt_supplier_input')?.value?.trim() || 'Verified Merchant';

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                await db.save('receipt', {
                    ...(dbId ? {item_id: dbId} : {}),
                    name: itemName,
                    quantity: qty,
                    supplier_name: supplier,
                    received_from: supplier
                });

                form.reset();
                window.closeModal?.('modal_stock_receipt');
                notify(`Inbound delivery of +${qty} ${itemName} saved to PostgreSQL stock_receipt!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 6. Admin Record Stock Issue Modal (#modal_stock_issue on /inventory)
        if (form.closest('#modal_stock_issue')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const selects = form.querySelectorAll('select.form-control');
            const inputs = form.querySelectorAll('input.form-control');
            const itemSelect = selects[0];
            const selectedOpt = itemSelect?.options[itemSelect.selectedIndex];
            const rawVal = itemSelect?.value || '';
            const dbId = /^\d+$/.test(rawVal) ? Number(rawVal) : null;
            const itemName = selectedOpt?.dataset?.name || selectedOpt?.text || rawVal || 'Supply Item';
            const qty = parseInt(inputs[0]?.value, 10) || 1;
            const dest = selects[1]?.value || 'Residential Ward';
            const ticket = inputs[1]?.value?.trim() || '';

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                await db.save('issue', {
                    ...(dbId ? {item_id: dbId} : {}),
                    name: itemName,
                    quantity: qty,
                    purpose: `${dest}${ticket ? ' · Ticket: ' + ticket : ''}`.slice(0, 255)
                });

                form.reset();
                window.closeModal?.('modal_stock_issue');
                notify(`Outbound issue of -${qty} ${itemName} saved to PostgreSQL stock_issue!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 7. Admin Add Supplier Modal (#modal_add_supplier on /inventory)
        if (form.closest('#modal_add_supplier')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const inputs = form.querySelectorAll('input.form-control');
            const supplierName = inputs[0]?.value?.trim() || '';
            const categoryName = inputs[1]?.value?.trim() || '';
            const phone = inputs[3]?.value?.trim() || '';

            if (!supplierName) {
                notify('Please enter the supplier name.', false);
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                await db.save('supplier', {
                    supplier_name: supplierName,
                    contact_number: phone || null,
                    category_name: categoryName || null
                });

                form.reset();
                window.closeModal?.('modal_add_supplier');
                notify(`Supplier "${supplierName}" saved to remote PostgreSQL!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }
    }, true);

    document.addEventListener('DOMContentLoaded', () => {
        // Hook markRequisitionReceived on /staff/inventory so confirming receipt saves to PostgreSQL stock_receipt & inventory
        if (typeof window.markRequisitionReceived === 'function') {
            window.markRequisitionReceived = async function (index) {
                const reqs = window.HopeHavenInventory?.getRequisitions() || [];
                const request = reqs[index];
                if (!request) return;
                try {
                    window.HopeHavenInventory.receiveRequest(request.id);
                    const dbId = extractDbIdFromCode(request.itemId || request.item);
                    await db.save('receipt', {
                        ...(dbId ? {item_id: dbId} : {}),
                        name: request.item,
                        category: request.category || 'Medical',
                        location: request.destination || 'Ward Alpha Cupboard',
                        quantity: Number(request.qty) || 1,
                        supplier_name: 'Director Approved Requisition',
                        received_from: `Approved Request ${request.id}`
                    });
                    if (window.HopeHavenSqlsrvWelfare && request.dbId) {
                        window.HopeHavenSqlsrvWelfare.save('receive', {
                            request_id: Number(request.dbId),
                            received_by: 'staff01'
                        }).catch(() => {});
                    }
                    window.renderAllStaffInventory?.();
                    notify(`Received ${request.qty} × ${request.item} and updated PostgreSQL inventory!`, true);
                    broadcastSync();
                    await db.refresh();
                } catch (err) {
                    notify(err.message, false);
                }
            };
        }

        if (window.HopeHavenSync) {
            window.HopeHavenSync.subscribe(SYNC_KEY, () => db.refresh().catch(() => {}));
        }
    });

    db.ready = db.refresh();
    db.ready.catch(err => console.warn('Remote PostgreSQL inventory sync notice:', err.message));
})();
