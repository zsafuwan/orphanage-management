/* Navigation shortcuts use the current role's existing sidebar destinations. */
(function () {
    'use strict';
    const dialog = document.getElementById('studio-jump');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const input = document.getElementById('studio-jump-query');
    const results = document.getElementById('studio-jump-results');
    const empty = dialog.querySelector('.studio-jump-empty');
    const pages = Array.from(document.querySelectorAll('.sidebar-nav a.nav-item')).map(link => ({
        name: link.textContent.trim().replace(/\s+/g, ' '),
        href: link.href,
        icon: link.querySelector('i')?.className || 'fas fa-arrow-right'
    }));
    let opener;
    function render() {
        const query = input.value.trim().toLocaleLowerCase();
        const matches = pages.filter(page => page.name.toLocaleLowerCase().includes(query));
        results.replaceChildren();
        matches.forEach(page => {
            const link = document.createElement('a');
            link.href = page.href;
            const icon = document.createElement('i');
            icon.className = page.icon;
            icon.setAttribute('aria-hidden', 'true');
            const label = document.createElement('span');
            label.textContent = page.name;
            const arrow = document.createElement('i');
            arrow.className = 'fas fa-arrow-right';
            arrow.setAttribute('aria-hidden', 'true');
            link.append(icon, label, arrow);
            results.append(link);
        });
        empty.hidden = matches.length > 0;
    }
    function open() {
        if (dialog.open) return;
        // Never interrupt a form modal or an in-progress text entry.
        opener = document.activeElement;
        input.value = '';
        render();
        dialog.showModal();
        input.focus();
    }
    document.querySelectorAll('.studio-jump-trigger').forEach(button => button.addEventListener('click', open));
    input.addEventListener('input', render);
    input.addEventListener('keydown', event => {
        if (event.key === 'ArrowDown') { event.preventDefault(); results.querySelector('a')?.focus(); }
        if (event.key === 'Enter') { event.preventDefault(); results.querySelector('a')?.click(); }
    });
    dialog.querySelector('[data-close-jump]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            dialog.close();
        }
    });
    dialog.addEventListener('close', () => opener?.focus());
    dialog.addEventListener('click', event => {
        if (event.target !== dialog) return;
        const bounds = dialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
    });
    document.addEventListener('keydown', event => {
        if (!(event.ctrlKey || event.metaKey) || event.key.toLowerCase() !== 'k') return;
        if (document.activeElement?.matches('input,textarea,select,[contenteditable="true"]') && !dialog.open) return;
        if (document.querySelector('dialog[open]:not(#studio-jump),.modal-overlay[style*="flex"],.modal-overlay[style*="block"]')) return;
        event.preventDefault();
        if (dialog.open) dialog.close(); else open();
    });
})();
