/* Local development only: refresh styles and source changes without Vite. */
(function () {
    'use strict';
    const endpoint = document.querySelector('meta[name="development-revision"]')?.content;
    if (!endpoint) return;
    let revision;
    let pendingReload = false;
    const dirty = new Set();
    document.addEventListener('input', event => {
        const editor = event.target.closest('form,[role="dialog"]');
        if (editor) dirty.add(editor);
    });
    document.addEventListener('reset', event => { dirty.delete(event.target); });

    function safeReload() {
        if (!pendingReload) return;
        const editing = [...dirty].some(form => form.isConnected && form.getBoundingClientRect().height > 0);
        if (!editing) location.reload();
        else {
            let notice = document.getElementById('development-refresh-notice');
            if (!notice) {
                notice = document.createElement('button');
                notice.id = 'development-refresh-notice';
                notice.type = 'button';
                notice.textContent = 'Code updated — finish your form, or click to refresh';
                notice.style.cssText = 'position:fixed;bottom:18px;right:18px;z-index:99999;padding:13px 18px;border:1px solid #9cb983;border-radius:12px;background:#214936;color:white;font:12px sans-serif;cursor:pointer;max-width:calc(100vw - 36px)';
                notice.addEventListener('click', () => location.reload());
                document.body.appendChild(notice);
            }
        }
    }

    async function check() {
        try {
            if (!document.hidden) {
                const response = await fetch(endpoint, {cache:'no-store', headers:{Accept:'application/json'}});
                if (response.ok) {
                    const next = await response.json();
                    if (revision && next.code !== revision.code) pendingReload = true;
                    if (revision && next.styles !== revision.styles) {
                        document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                            const url = new URL(link.href);
                            if (url.origin !== location.origin) return;
                            url.searchParams.set('live', next.styles);
                            link.href = url.href;
                        });
                    }
                    revision = next;
                    safeReload();
                }
            }
        } catch (_) { /* Keep the page usable while the local server restarts. */ }
        setTimeout(check, 2000);
    }
    check();
})();
