/* Browser-local change notifications. Database transport can be added later. */
(function () {
    'use strict';
    const listeners = new Map();
    const snapshots = new Map();
    const pending = new Set();
    let scheduled = false;

    function read(key) {
        try { return localStorage.getItem(key); } catch (_) { return null; }
    }

    function notify(key) {
        if (!listeners.has(key)) return;
        snapshots.set(key, read(key));
        pending.add(key);
        if (scheduled) return;
        scheduled = true;
        queueMicrotask(function () {
            scheduled = false;
            const callbacks = new Set();
            pending.forEach(key => listeners.get(key)?.forEach(callback => callbacks.add(callback)));
            pending.clear();
            callbacks.forEach(callback => {
                try { callback(); } catch (error) { console.error('Live update failed:', error); }
            });
        });
    }

    function reconcile() {
        listeners.forEach((_, key) => {
            if (snapshots.get(key) !== read(key)) notify(key);
        });
    }

    window.HopeHavenSync = {
        write: function (key, value) {
            // Do not report success when the browser cannot retain a change.
            try { localStorage.setItem(key, value); }
            catch (error) {
                if (typeof showPortalToast === 'function') showPortalToast('Your browser could not save this change. Check available storage and try again.', false);
                throw error;
            }
            notify(key);
        },
        subscribe: function (keys, callback) {
            [].concat(keys).forEach(key => {
                if (!listeners.has(key)) listeners.set(key, new Set());
                listeners.get(key).add(callback);
                snapshots.set(key, read(key));
            });
            return function () { [].concat(keys).forEach(key => listeners.get(key)?.delete(callback)); };
        },
        escape: function (value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[char]));
        },
        escapeRecord: function (value) {
            if (typeof value === 'string') return this.escape(value);
            if (Array.isArray(value)) return value.map(item=>this.escapeRecord(item));
            if (value && typeof value === 'object') return Object.fromEntries(Object.entries(value).map(([key,item])=>[key,this.escapeRecord(item)]));
            return value;
        },
        nextId: function (records, prefix) {
            const yearPrefix = prefix + '-' + new Date().getFullYear() + '-';
            const highest = records.reduce((max, item) => {
                if (!String(item.id).startsWith(yearPrefix)) return max;
                const sequence = Number(String(item.id).slice(yearPrefix.length));
                return Number.isInteger(sequence) ? Math.max(max, sequence) : max;
            }, 0);
            return yearPrefix + String(highest + 1).padStart(3, '0');
        },
        refreshView: function (callback) {
            // Preserve selections and unfinished form fields during a background render.
            const fields = [...document.querySelectorAll('input,select,textarea')].filter(field => field.id && field.type !== 'file');
            const values = fields.map(field => [field.id, field.value, field.checked, field.selectionStart, field.selectionEnd]);
            const activeId = document.activeElement?.id;
            callback();
            values.forEach(([id, value, checked, start, end]) => {
                const field = document.getElementById(id);
                if (!field) return;
                if (field.tagName !== 'SELECT' || [...field.options].some(option => option.value === value)) field.value = value;
                field.checked = checked;
                if (id === activeId) {
                    field.focus({preventScroll:true});
                    if (start !== null && typeof field.setSelectionRange === 'function') {
                        try { field.setSelectionRange(start, end); } catch (_) { /* Not a text field. */ }
                    }
                }
            });
        }
    };
    addEventListener('storage', event => {
        if (event.storageArea !== localStorage) return;
        if (event.key === null) listeners.forEach((_, key) => notify(key));
        else notify(event.key);
    });
    addEventListener('focus', reconcile);
    addEventListener('pageshow', reconcile);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) reconcile(); });
})();
