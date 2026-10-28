/* Full-field native calendar activation. */
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        const input = event.target.closest('input[data-open-picker]');
        if (input && typeof input.showPicker === 'function') {
            try { input.showPicker(); } catch (_) { /* Native calendar icon remains available. */ }
        }
    });
});
