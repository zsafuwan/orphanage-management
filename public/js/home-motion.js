/* Scroll-scrubbed motion. No hidden content or intercepted scrolling. */
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
    let textItems = [];
    let textObserver;
    const textEntrances = new Set();
    function animateText(element, delay = 0) {
        if (preference.matches || !element.animate) return;
        const animation = element.animate([
            {opacity: .45, transform: 'translateY(14px)'},
            {opacity: 1, transform: 'translateY(0)'}
        ], {duration: 850, delay, easing: 'cubic-bezier(.16,1,.3,1)'});
        textEntrances.add(animation);
        animation.finished.then(() => textEntrances.delete(animation)).catch(() => textEntrances.delete(animation));
    }
    function observeText() {
        textObserver?.disconnect();
        if (preference.matches) return;
        const elements = [...textItems.map(item => item.element), ...document.querySelectorAll('#inquiry_form input:not([type="hidden"]), #inquiry_form select, #inquiry_form textarea')];
        if (!('IntersectionObserver' in window)) {
            elements.forEach(element => animateText(element));
            return;
        }
        textObserver = new IntersectionObserver(entries => {
            let stagger = 0;
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                animateText(entry.target, Math.min(stagger++ * 45, 270));
                textObserver.unobserve(entry.target);
            });
        }, {threshold: 0, rootMargin: '0px 0px -24px 0px'});
        elements.forEach(element => textObserver.observe(element));
    }
    function prepareText() {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode()) {
            const node = walker.currentNode;
            if (!node.textContent.trim() || node.parentElement.closest('script, style, noscript, textarea, select, svg, #loading, #magnify, #toast_msg, #malaysia_places_map, .home-motion-text')) continue;
            nodes.push(node);
        }
        textItems = nodes.map(node => {
            const span = document.createElement('span');
            span.className = 'home-motion-text';
            node.replaceWith(span);
            span.appendChild(node);
            return {element: span, y: 0};
        });
        observeText();
    }
    function reset() {
        textEntrances.forEach(animation => animation.cancel());
        textEntrances.clear();
        textItems.forEach(item => { item.element.style.translate = ''; item.y = 0; });
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
        const textPositions = textItems.map(item => {
            let offset = item.y;
            items.forEach(parent => { if (parent.element.contains(item.element)) offset += parent.y; });
            return item.element.getBoundingClientRect().top - offset;
        });
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
        textItems.forEach((item, index) => {
            const focused = item.element.closest('a, button, input, select, textarea') === document.activeElement;
            const progress = focused ? 1 : clamp((height * .98 - textPositions[index]) / (height * .32));
            item.y = Number(((1 - progress) * 18).toFixed(2));
            item.element.style.translate = '0 ' + item.y + 'px';
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
        prepareText();
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
