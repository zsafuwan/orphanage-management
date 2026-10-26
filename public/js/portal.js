// HopeHaven Orphanage Sanctuary - Portal Interactive Logic (HTML/CSS/JS)

$(document).ready(function() {
    // 1. Mobile Sidebar Toggle
    $("#mobile_sidebar_toggle").on("click", function() {
        $(".portal-sidebar").toggleClass("open");
    });

    $(document).on("click", function(e) {
        if (!$(e.target).closest(".portal-sidebar, #mobile_sidebar_toggle").length) {
            $(".portal-sidebar").removeClass("open");
        }
    });

    // 2. Generic Tab Switching Logic
    $(".tab-btn").on("click", function() {
        var targetTab = $(this).data("tab");
        var container = $(this).closest(".portal-content");
        
        container.find(".tab-btn").removeClass("active");
        $(this).addClass("active");

        container.find(".tab-pane").hide();
        $("#" + targetTab).fadeIn(200);
    });

    // 3. Table and Card Search Filtering
    $(".search-input").on("keyup", function() {
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
            $(".portal-modal-backdrop").fadeOut(150);
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
    var displayMeta = meta || 'Just now · Sanctuary Management';

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
            <div class="toast-desc">${message}</div>
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
            <div class="toast-desc">${options.message}</div>
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

// Subsystem 1: Quick Register Child handler
function handleNewChildSubmit(event) {
    event.preventDefault();
    var name = $("#child_name_input").val();
    var dorm = $("#child_dorm_input").val();
    var grade = $("#child_grade_input").val();

    if (!name) return;

    var newCardHtml = `
        <div class="child-profile-card">
            <div>
                <div class="card-top-info">
                    <img src="https://images.unsplash.com/photo-1543610892-0b1f7e6d8ac1?w=150&auto=format&fit=crop&q=80" class="avatar-large" alt="${name}">
                    <div style="text-align: right;">
                        <span class="badge badge-secondary" style="font-family: monospace;">CH-2026-005</span>
                        <div style="margin-top: 4px;"><span class="badge badge-success">Active Ward</span></div>
                    </div>
                </div>
                <div class="profile-name">${name}</div>
                <div class="profile-meta-line">Newly Registered Resident</div>

                <div class="info-rows">
                    <div class="info-row">
                        <span class="info-row-label"><i class="fas fa-home"></i> Dormitory:</span>
                        <span class="info-row-val">${dorm}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row-label"><i class="fas fa-graduation-cap"></i> School:</span>
                        <span class="info-row-val">${grade}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-row-label"><i class="fas fa-heartbeat"></i> Status:</span>
                        <span class="info-row-val" style="color: #10b981;">Intake Completed</span>
                    </div>
                </div>
            </div>
            <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; gap: 8px;">
                <button class="btn-outline btn-sm" style="flex: 1;" onclick="showPortalToast('Viewing ${name} PDF', true)"><i class="fas fa-id-card"></i> View PDF</button>
            </div>
        </div>
    `;

    $("#profiles_grid").prepend(newCardHtml);
    closeModal('modal_register_child');
    showPortalToast("Child profile for " + name + " created successfully!", true);
    event.target.reset();
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

// Subsystem 3: Quick Approve/Reject Welfare Request
function updateRequestDecision(ticketId, newStatus) {
    var badgeClass = newStatus === 'Approved' ? 'badge-success' : 'badge-danger';
    $("#status_badge_" + ticketId).removeClass('badge-warning badge-success badge-danger badge-info').addClass(badgeClass).text(newStatus);
    showPortalToast("Welfare Request #" + ticketId + " has been marked as " + newStatus + "!", true);
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
