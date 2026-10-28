// HopeHaven Orphanage Sanctuary - Portal Interactive Logic (HTML/CSS/JS)

$(document).ready(function() {
    // Center control columns and their headings, including newly synced rows.
    function alignTableControls(table) {
        if (!table.tHead || !table.tHead.rows.length) return;
        const headers = [...table.tHead.rows[table.tHead.rows.length - 1].cells];
        const rows = [...table.tBodies].flatMap(body => [...body.rows]);
        headers.forEach(function(header, index) {
            if (header.colSpan !== 1) return;
            const cells = rows.map(row => row.cells[index]).filter(cell => cell && cell.colSpan === 1);
            const controls = header.classList.contains('action-column') || cells.some(cell => cell.querySelector('button,a[class*="btn"],input[type="checkbox"]'));
            header.classList.toggle('table-control-column', controls);
            cells.forEach(function(cell) {
                cell.classList.toggle('table-control-column', controls);
                if (controls) [...cell.children].forEach(function(child) {
                    if (child.tagName === 'DIV' && getComputedStyle(child).display === 'flex') child.classList.add('table-control-group');
                });
            });
        });
    }
    const tableArea = document.querySelector('.portal-content');
    if (tableArea) {
        tableArea.querySelectorAll('table').forEach(alignTableControls);
        const changedTables = new Set();
        let alignmentFrame;
        new MutationObserver(function(changes) {
            changes.forEach(function(change) {
                const table = change.target.closest?.('table');
                if (table) changedTables.add(table);
                change.addedNodes.forEach(function(node) {
                    if (node.nodeType !== 1) return;
                    if (node.matches('table')) changedTables.add(node);
                    node.querySelectorAll('table').forEach(table => changedTables.add(table));
                });
            });
            if (changedTables.size && !alignmentFrame) alignmentFrame = requestAnimationFrame(function() {
                alignmentFrame = null;
                changedTables.forEach(table => { if (table.isConnected) alignTableControls(table); });
                changedTables.clear();
            });
        }).observe(tableArea, {childList:true,subtree:true});
    }
    document.querySelectorAll('.form-group,.staff-field-group').forEach(function(group) {
        const label=group.querySelector('label'), field=group.querySelector('input[id],select[id],textarea[id]');
        if(label && field && !label.htmlFor) label.htmlFor=field.id;
    });
    // Director dialogs share one accessible overlay behavior on every page.
    const dialogTriggers = new WeakMap();
    document.querySelectorAll('.director-modal-overlay').forEach(function(overlay, index) {
        const heading = overlay.querySelector('h3');
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-hidden', overlay.classList.contains('active') ? 'false' : 'true');
        overlay.setAttribute('tabindex', '-1');
        if (heading) { heading.id = heading.id || 'care-dialog-heading-' + index; overlay.setAttribute('aria-labelledby', heading.id); }
        overlay.querySelectorAll('.director-modal-close').forEach(button => button.setAttribute('aria-label', 'Close dialog'));
        new MutationObserver(function() {
            const open = overlay.classList.contains('active');
            overlay.setAttribute('aria-hidden', open ? 'false' : 'true');
            document.body.classList.toggle('has-care-dialog', !!document.querySelector('.director-modal-overlay.active'));
            if (open) {
                dialogTriggers.set(overlay, document.activeElement);
                (overlay.querySelector('button,input,select,textarea,[tabindex="0"]') || overlay).focus();
            } else dialogTriggers.get(overlay)?.focus?.({preventScroll:true});
        }).observe(overlay, {attributes:true, attributeFilter:['class']});
        overlay.addEventListener('click', event => { if (event.target === overlay) overlay.classList.remove('active'); });
    });
    document.addEventListener('keydown', function(event) {
        const open = [...document.querySelectorAll('.director-modal-overlay.active')].at(-1);
        if (!open) return;
        if (event.key === 'Escape') { open.classList.remove('active'); return; }
        if (event.key !== 'Tab') return;
        const fields = [...open.querySelectorAll('button,input,select,textarea,a[href],[tabindex="0"]')].filter(field => !field.disabled && field.getClientRects().length);
        const first = fields[0], last = fields.at(-1);
        if (!first) { event.preventDefault(); open.focus(); }
        else if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
    // 1. Mobile Sidebar Toggle
    $("#mobile_sidebar_toggle").on("click", function() {
        $(".portal-sidebar").toggleClass("open");
        $(this).attr("aria-expanded", $(".portal-sidebar").hasClass("open") ? "true" : "false");
    });

    $(document).on("click", function(e) {
        if (!$(e.target).closest(".portal-sidebar, #mobile_sidebar_toggle").length) {
            $(".portal-sidebar").removeClass("open");
            $("#mobile_sidebar_toggle").attr("aria-expanded", "false");
        }
    });

    $(document).on("keydown", function(e) {
        if (e.key === "Escape" && $(".portal-sidebar").hasClass("open")) {
            $(".portal-sidebar").removeClass("open");
            $("#mobile_sidebar_toggle").attr("aria-expanded", "false").trigger("focus");
        }
    });

    // 2. Generic Tab Switching Logic
    $(".tab-btn[data-tab]").on("click", function() {
        var targetTab = $(this).data("tab");
        var container = $(this).closest(".portal-content");
        
        container.find(".tab-btn").removeClass("active");
        $(this).addClass("active");

        container.find(".tab-pane").hide();
        $("#" + targetTab).fadeIn(200);
    });

    // 3. Table and Card Search Filtering
    $(".search-input[data-search-target]").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var targetSelector = $(this).data("search-target");

        $(targetSelector).filter(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(value) > -1);
        });
    });

    // 4. Dropdown Category Filtering
    $(".category-filter").on("change", function() {
        var selected = $(this).val();
        var targetRows = $(this).data("filter-target");

        if (selected === "All") {
            $(targetRows).show();
        } else {
            $(targetRows).each(function() {
                var category = $(this).data("category");
                if (category === selected) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // 5. Close modal on backdrop click or ESC
    $(".portal-modal-backdrop").on("click", function(e) {
        if ($(e.target).hasClass("portal-modal-backdrop")) {
            $(this).fadeOut(150);
        }
    });

    $(document).on("keydown", function(e) {
        if (e.key === "Escape") {
            $(".portal-modal-backdrop,.staff-duty-modal-backdrop,.modal-voucher-backdrop").fadeOut(150);
        }
    });
});

// Modal Helpers
function openModal(id) {
    $("#" + id).css("display", "flex").hide().fadeIn(200);
}

function closeModal(id) {
    $("#" + id).fadeOut(150);
}

// Modern Toast & Notification System
function showPortalToast(message, isSuccess, title, meta) {
    if (typeof isSuccess === 'string') {
        return showPrettyNotification({
            message: message,
            type: isSuccess === 'error' ? 'danger' : isSuccess,
            title: title,
            meta: meta
        });
    }
    var toast = $("#portal_toast");
    if (!toast.length) {
        $("body").append('<div id="portal_toast"></div>');
        toast = $("#portal_toast");
    }

    var type = isSuccess === false ? 'danger' : 'success';
    var iconHtml = isSuccess === false 
        ? '<i class="fas fa-exclamation-triangle"></i>' 
        : '<i class="fas fa-check-circle"></i>';

    var displayTitle = title || (isSuccess === false ? 'Action Alert' : 'Duty Action Completed');
    var displayMeta = meta || 'Just now · Orphanage Management';

    var borderColor = isSuccess === false ? '#ef4444' : '#10b981';

    toast.css('border-left-color', borderColor);
    toast.html(`
        <div class="toast-icon-wrap ${type}">
            ${iconHtml}
        </div>
        <div class="toast-body">
            <div class="toast-title">
                <span>${displayTitle}</span>
                <button class="toast-close-btn" onclick="$('#portal_toast').fadeOut(200);">&times;</button>
            </div>
            <div class="toast-desc">${window.HopeHavenSync.escape(message)}</div>
            <div class="toast-meta"><i class="fas fa-clock"></i> ${displayMeta}</div>
        </div>
        <div class="toast-progress-bar" style="background: ${borderColor};"></div>
    `);

    toast.stop(true, true).css("display", "flex").hide().fadeIn(250);

    // Clear previous timeout if any
    if (window.portalToastTimer) {
        clearTimeout(window.portalToastTimer);
    }

    window.portalToastTimer = setTimeout(function() {
        toast.fadeOut(350);
    }, 4200);
}

// Dedicated Pretty Notification Helper with custom types
function showPrettyNotification(options) {
    var toast = $("#portal_toast");
    if (!toast.length) {
        $("body").append('<div id="portal_toast"></div>');
        toast = $("#portal_toast");
    }

    var type = options.type || 'success'; // success, clinical, warning, danger, info
    var iconMap = {
        'success': '<i class="fas fa-check-circle"></i>',
        'clinical': '<i class="fas fa-stethoscope"></i>',
        'warning': '<i class="fas fa-bell"></i>',
        'danger': '<i class="fas fa-exclamation-triangle"></i>',
        'info': '<i class="fas fa-info-circle"></i>'
    };

    var borderMap = {
        'success': '#10b981',
        'clinical': '#8b5cf6',
        'warning': '#f59e0b',
        'danger': '#ef4444',
        'info': '#3b82f6'
    };

    var borderColor = borderMap[type] || '#10b981';
    var iconHtml = options.icon ? `<i class="${options.icon}"></i>` : (iconMap[type] || iconMap.success);

    toast.css('border-left-color', borderColor);
    toast.html(`
        <div class="toast-icon-wrap ${type}">
            ${iconHtml}
        </div>
        <div class="toast-body">
            <div class="toast-title">
                <span>${options.title || 'Duty Alert'}</span>
                <button class="toast-close-btn" onclick="$('#portal_toast').fadeOut(200);">&times;</button>
            </div>
            <div class="toast-desc">${window.HopeHavenSync.escape(options.message)}</div>
            <div class="toast-meta"><i class="fas fa-clock"></i> ${options.meta || 'Just now · Duty Staff Log'}</div>
        </div>
        <div class="toast-progress-bar" style="background: ${borderColor};"></div>
    `);

    toast.stop(true, true).css("display", "flex").hide().fadeIn(250);

    if (window.portalToastTimer) {
        clearTimeout(window.portalToastTimer);
    }

    window.portalToastTimer = setTimeout(function() {
        toast.fadeOut(350);
    }, 4200);
}

// Topbar Notifications Dropdown Logic
function toggleNotificationsDropdown(e) {
    if (e) e.stopPropagation();
    $("#notifications_dropdown").fadeToggle(180);
}

function markAllNotificationsRead(e) {
    if (e) e.stopPropagation();
    $(".notif-item").removeClass("unread");
    $("#topbar_bell_count").fadeOut(200);
    $("#dropdown_unread_badge").text("0 New").css("background", "#f1f5f9");
    showPortalToast("All duty notifications marked as read.", true, "Notifications Cleared", "Just now · Topbar");
}

function handleNotificationClick(title, details) {
    $("#notifications_dropdown").fadeOut(150);
    showPrettyNotification({
        title: title,
        message: details,
        type: 'info',
        meta: 'Viewing duty bulletin'
    });
}

// Click outside to close notifications dropdown
$(document).on("click", function(e) {
    if (!$(e.target).closest(".topbar-notifications-wrap").length) {
        $("#notifications_dropdown").fadeOut(150);
    }
});

// Subsystem 1: Role-Based Access Control Simulation (Child Welfare)
function simulateRole(roleName, element) {
    $(".role-pill").removeClass("active");
    $(element).addClass("active");

    $("#current_role_badge").text(roleName);

    if (roleName === "Educator") {
        $(".confidential-dossier").html(`
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px dashed #f59e0b; padding: 12px; border-radius: 8px; color: #b45309; font-size: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-lock"></i>
                <span><strong>Confidential Clinical Record Redacted:</strong> Educational credentials active. Intake trauma & psychiatric evaluations are restricted to Social Workers & Clinic Officers.</span>
            </div>
        `);
        showPortalToast("Switched to Educator clearance. Clinical trauma notes masked.", true);
    } else if (roleName === "Clinic Nurse") {
        $(".confidential-dossier").html(`
            <div style="background: rgba(59, 130, 246, 0.08); border: 1px solid #93c5fd; padding: 12px; border-radius: 8px; color: #1d4ed8; font-size: 12px;">
                <i class="fas fa-notes-medical"></i> <strong>Medical PDF Clear:</strong> Immunization schedule, prescription allergies, and growth records fully unlocked.
            </div>
        `);
        showPortalToast("Switched to Clinic Nurse mode. Medical records unlocked.", true);
    } else if (roleName === "Staff Caregiver") {
        $(".confidential-dossier").html(`
            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid #86efac; padding: 12px; border-radius: 8px; color: #166534; font-size: 12px;">
                <i class="fas fa-user-check"></i> <strong>Staff Duty Clearance Active:</strong> Nurse Mary authorized to insert child records, key in term examination papers, and document daily medical conditions.
            </div>
        `);
        showPortalToast("Active clearance: Staff Caregiver (Nurse Mary). Full entry enabled.", true);
    } else {
        // Social Worker / Administrator
        $(".confidential-dossier").html(`
            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid #86efac; padding: 12px; border-radius: 8px; color: #166534; font-size: 12px;">
                <i class="fas fa-shield-alt"></i> <strong>Full Case Clearance:</strong> Intake trauma baselines, court guardianship certificates, and psychiatric evaluations visible.
            </div>
        `);
        showPortalToast("Role updated to " + roleName + ". Full clearance active.", true);
    }
}

// Subsystem 2: Quick Record Stock Movement
function handleStockReceiptSubmit(event) {
    event.preventDefault();
    var itemName = $("#receipt_item_select option:selected").text();
    var qty = $("#receipt_qty_input").val();
    var supplier = $("#receipt_supplier_input").val() || "Verified Merchant";

    var newRow = `
        <tr>
            <td><span class="badge badge-success"><i class="fas fa-arrow-down"></i> Receipt</span></td>
            <td style="font-family: monospace; font-weight: 600;">REC-2026-${Math.floor(100 + Math.random() * 900)}</td>
            <td><strong>${itemName}</strong></td>
            <td style="font-weight: 700; color: #10b981;">+${qty} units</td>
            <td style="font-size: 12px; color: #64748b;">Today</td>
            <td>${supplier}</td>
            <td><span class="badge badge-secondary">Central Storeroom</span></td>
        </tr>
    `;

    $("#movements_tbody").prepend(newRow);
    closeModal('modal_stock_receipt');
    showPortalToast("Inbound delivery of " + qty + " units recorded into stock!", true);
    event.target.reset();
}

// Subsystem 4: Quick Log Volunteer Hours
function handleVolunteerHoursSubmit(event) {
    event.preventDefault();
    var volunteer = $("#vol_name_select").val();
    var hours = $("#vol_hours_input").val();
    var activity = $("#vol_activity_input").val();

    var newLog = `
        <tr>
            <td><strong>${volunteer}</strong></td>
            <td>${activity}</td>
            <td style="font-size: 12px; color: #64748b;">Today</td>
            <td><span class="badge badge-success" style="font-weight: 800;">${hours} hrs</span></td>
            <td>Sister Teresa Maria</td>
            <td style="font-style: italic; font-size: 12px; color: #64748b;">"Service logged by coordinator"</td>
        </tr>
    `;

    $("#attendance_tbody").prepend(newLog);
    closeModal('modal_volunteer_hours');
    showPortalToast("Logged " + hours + " service hours for " + volunteer + "!", true);
    event.target.reset();
}

// Subsystem 5: Quick Record Donation
function handleDonationSubmit(event) {
    event.preventDefault();
    var donor = $("#don_name_input").val();
    var amount = $("#don_amount_input").val();
    var need = $("#don_need_input").val() || "General Orphanage Welfare";

    var rcpNo = "RCP-2026-" + Math.floor(1000 + Math.random() * 9000);

    var newRow = `
        <tr>
            <td style="font-family: monospace; font-weight: 700;">${rcpNo}</td>
            <td><strong>${donor}</strong></td>
            <td><span class="badge badge-success">Monetary</span></td>
            <td style="font-size: 15px; font-weight: 800; color: #10b981;">$${Number(amount).toLocaleString()} USD</td>
            <td><strong>${need}</strong></td>
            <td style="font-size: 12px; color: #64748b;">Today</td>
            <td><button class="btn-outline btn-sm" onclick="showPortalToast('E-Receipt ${rcpNo} sent to donor', true)"><i class="fas fa-print"></i> E-Receipt</button></td>
        </tr>
    `;

    $("#donations_tbody").prepend(newRow);
    closeModal('modal_record_donation');
    showPortalToast("Donation of $" + amount + " recorded and official E-Receipt generated!", true);
    event.target.reset();
}
