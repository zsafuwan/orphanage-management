// Navigation header scroll behavior matching template 2
var width = $(window).width();

function updateHeaderOnScroll() {
    var width = $(window).width();
    if (width >= 1000) {
        if (document.body.scrollTop > 30 || document.documentElement.scrollTop > 30) {
            $("#header").css({
                "background": "rgba(255, 255, 255, 0.98)",
                "color": "#12141d",
                "box-shadow": "0px 4px 20px rgba(0,0,0,0.08)",
                "padding": "12px 4vw"
            });
        } else {
            $("#header").css({
                "background": "rgba(255, 255, 255, 0.95)",
                "color": "#12141d",
                "box-shadow": "0px 2px 14px rgba(0,0,0,0.05)",
                "padding": "14px 4vw"
            });
        }
        $("#header .logo-sub").css("color", "#64748b");
        $("#navigation a:not(.nav-cta-btn):not(.nav-portal-link)").css("color", "#1e293b");
        $(".nav-portal-link").css({
            "background": "rgba(18, 20, 29, 0.05)",
            "color": "#12141d",
            "border-color": "rgba(0, 0, 0, 0.1)"
        });
    } else {
        // Reset desktop inline overrides on phone/tablet widths so CSS takes full control
        $("#header").removeAttr("style");
        $("#header .logo-sub").removeAttr("style");
        $(".nav-portal-link").removeAttr("style");
    }
}

window.onscroll = updateHeaderOnScroll;
$(window).on("resize", updateHeaderOnScroll);

// Lightbox magnify functions
function magnify(imglink, captionText) {
    $("#img_here").css("background", "url('" + imglink + "') center center");
    $("#magnify_caption").text(captionText || "HopeHaven Orphanage Activities");
    $("#magnify").css("display", "flex");
    $("#magnify").addClass("animated fadeIn");
    setTimeout(function() {
        $("#magnify").removeClass("animated fadeIn");
    }, 500);
}

function closemagnify() {
    $("#magnify").addClass("animated fadeOut");
    setTimeout(function() {
        $("#magnify").css("display", "none");
        $("#magnify").removeClass("animated fadeOut");
        $("#img_here").css("background", "url('') center center");
    }, 500);
}

// Toast notification helper
function showToast(message, isSuccess) {
    var toast = $("#toast_msg");
    toast.html((isSuccess ? '<i class="fas fa-check-circle" style="color:#22c55e;"></i> ' : '<i class="fas fa-exclamation-circle" style="color:#ef4444;"></i> ') + message);
    toast.css("display", "flex");
    toast.addClass("animated fadeInUp");
    setTimeout(function() {
        toast.removeClass("fadeInUp").addClass("fadeOutDown");
        setTimeout(function() {
            toast.css("display", "none").removeClass("fadeOutDown");
        }, 500);
    }, 4500);
}

// Preloader fadeout matching template 2
setTimeout(function() {
    $("#loading").addClass("animated fadeOut");
    setTimeout(function() {
        $("#loading").removeClass("animated fadeOut");
        $("#loading").css("display", "none");
    }, 600);
}, 1200);

// Smooth scrolling for anchor hash links
$(document).ready(function() {
    updateHeaderOnScroll();

    // Phone / mobile width navigation toggle
    $("#mobile_nav_toggle").on("click", function(e) {
        e.stopPropagation();
        var header = $("#header");
        var isOpen = header.hasClass("nav-open");
        
        if (isOpen) {
            header.removeClass("nav-open");
            $(this).find("i").removeClass("fa-times").addClass("fa-bars");
            $(this).attr("aria-expanded", "false");
        } else {
            header.addClass("nav-open");
            $(this).find("i").removeClass("fa-bars").addClass("fa-times");
            $(this).attr("aria-expanded", "true");
        }
    });

    // Auto-close mobile navigation when a menu link is tapped
    $("#navigation a").on("click", function() {
        if ($(window).width() <= 1000) {
            $("#header").removeClass("nav-open");
            $("#mobile_nav_toggle").find("i").removeClass("fa-times").addClass("fa-bars");
            $("#mobile_nav_toggle").attr("aria-expanded", "false");
        }
    });

    // Close mobile menu when tapping anywhere outside the header
    $(document).on("click", function(e) {
        if (!$(e.target).closest("#header").length && $("#header").hasClass("nav-open")) {
            $("#header").removeClass("nav-open");
            $("#mobile_nav_toggle").find("i").removeClass("fa-times").addClass("fa-bars");
            $("#mobile_nav_toggle").attr("aria-expanded", "false");
        }
    });

    $("a").on("click", function(event) {
        if (this.hash !== "" && $(this.hash).length) {
            event.preventDefault();
            var hash = this.hash;
            var targetOffset = $(hash).offset().top - 70;
            $("body,html").animate({
                scrollTop: targetOffset
            }, 900, function() {
                window.location.hash = hash;
            });
        }
    });

    // Close modal on escape key
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            closemagnify();
        }
    });

    // Contact / Inquiry Form (Pure UI Simulation - No database table required)
    $("#inquiry_form").on("submit", function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find("button[type='submit']");
        var originalBtnText = submitBtn.html();

        submitBtn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

        setTimeout(function() {
            submitBtn.prop("disabled", false).html(originalBtnText);
            showToast("Thank you for reaching out! Your message has been received.", true);
            form[0].reset();
        }, 700);
    });
});
