/* Homepage text entrances and scroll-linked card and hero motion. */
(() => {
    'use strict';
    const preference = matchMedia('(prefers-reduced-motion: reduce)');
    const clamp = value => Math.min(1, Math.max(0, value));
    let frame = 0;
    let items = [];
    let hero;
    let photo;
    let originalPhotoScale = '';
    let heroLayers = [];
    let textItems;
    let textObserver;
    const textEntrances = new Map();
    function animateText(element, delay = 0) {
        if (preference.matches || !element.animate) return;
        const animation = element.animate([
            {opacity: 0, transform: 'translateY(24px) scale(.97)'},
            {opacity: 1, transform: 'translateY(-3px) scale(1)', offset: .72},
            {opacity: 1, transform: 'translateY(0)'}
        ], {duration: 620, delay, fill: 'backwards', easing: 'cubic-bezier(.22,.8,.36,1)'});
        textEntrances.set(element, animation);
        const clear = () => {
            if (textEntrances.get(element) === animation) textEntrances.delete(element);
        };
        animation.finished.then(clear, clear);
    }
    function observeText() {
        textObserver?.disconnect();
        if (preference.matches || !('IntersectionObserver' in window)) return;
        textObserver = new IntersectionObserver(entries => {
            let stagger = 0;
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    textEntrances.get(entry.target)?.cancel();
                    return;
                }
                if (!entry.target.contains(document.activeElement)) {
                    animateText(entry.target, Math.min(stagger++ * 55, 165));
                }
            });
        });
        textItems.forEach(element => textObserver.observe(element));
    }
    function reset() {
        textEntrances.forEach(animation => animation.cancel());
        textEntrances.clear();
        items.forEach(item => {
            item.element.style.translate = item.translate;
            item.element.style.opacity = item.opacity;
            item.y = 0;
        });
        if (photo) photo.style.scale = originalPhotoScale;
        heroLayers.forEach(layer => { layer.element.style.translate = layer.translate; });
    }
    function render() {
        frame = 0;
        if (preference.matches) { reset(); return; }
        const height = window.innerHeight;
        // Read geometry before writing styles; subtract our own translation to avoid feedback.
        const positions = items.map(item => item.element.getBoundingClientRect().top - item.y);
        const heroBounds = hero?.getBoundingClientRect();
        const heroProgress = heroBounds ? clamp(window.scrollY / Math.max(1, heroBounds.top + window.scrollY + heroBounds.height)) : 0;
        const motionScale = window.innerWidth < 700 ? .4 : 1;
        items.forEach((item, index) => {
            const focused = item.element.contains(document.activeElement);
            const progress = focused ? 1 : clamp((height * .96 - positions[index]) / (height * .38));
            item.y = Number(((1 - progress) * 48).toFixed(2));
            item.element.style.translate = '0 ' + item.y.toFixed(2) + 'px';
            item.element.style.opacity = String(.3 + progress * .7);
        });
        if (photo) photo.style.scale = String(1 + heroProgress * .09);
        heroLayers.forEach(layer => {
            // Individual translate preserves the sticker's existing rotation and card styling.
            const focused = layer.element.contains(document.activeElement);
            const progress = focused ? 0 : heroProgress;
            layer.element.style.translate = (progress * layer.x * motionScale).toFixed(2) + 'px ' + (progress * layer.y * motionScale).toFixed(2) + 'px';
        });
    }
    function schedule() {
        if (!frame) frame = requestAnimationFrame(render);
    }
    function init() {
        hero = document.querySelector('#top_part');
        photo = document.querySelector('#rightImage');
        originalPhotoScale = photo?.style.scale || '';
        heroLayers = [
            ['#about .hero-tag', 0, -28],
            ['#about h1', 0, -90],
            ['#about .hero-description', 0, -46],
            ['#about .hero-btn-group', 0, -22],
            ['.studio-photo-sticker', -24, -72],
            ['.hero-overlay-card', 0, -110]
        ].flatMap(([selector, x, y]) => {
            const element = document.querySelector(selector);
            return element ? [{element, x, y, translate: element.style.translate}] : [];
        });
        items = Array.from(document.querySelectorAll('.home-event-card, .care-locations-intro, .malaysia-map-card, .section-title-wrap, .program-card, .gallery-item, #bio > :not(.section-title-wrap), #contact > :not(.section-title-wrap)')).map(element => ({
            element, y: 0, translate: element.style.translate, opacity: element.style.opacity
        }));
        textItems = document.querySelectorAll(`
            #top_part :is(h1, h3, p, .hero-tag),
            .home-event-copy > *,
            #rightMap :is(h2, h4, p, .hero-tag, .map-live-tag, .nearest-bar-content),
            .studio-values > span,
            :is(#programs, #work, #bio, #contact) :is(.section-header, p),
            .program-card h3, .gallery-badge, .stat-box > *,
            #inner_table td:not(:has(.social)), #footer > div:first-child
        `);
        observeText();
        window.addEventListener('scroll', schedule, {passive: true});
        window.addEventListener('resize', schedule, {passive: true});
        window.addEventListener('pageshow', schedule);
        document.addEventListener('focusin', schedule);
        document.addEventListener('focusout', schedule);
        preference.addEventListener('change', () => {
            reset();
            observeText();
            schedule();
        });
        schedule();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, {once: true});
    else init();
})();
