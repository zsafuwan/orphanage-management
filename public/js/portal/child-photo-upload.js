/* Admission photos share the existing browser-local child registry. */
(function () {
    'use strict';
    const states = new WeakMap();
    function stateFor(widget) {
        if (!states.has(widget)) states.set(widget, {version: 0, image: '', pending: false});
        return states.get(widget);
    }
    function reset(widget) {
        const state = stateFor(widget);
        state.version++;
        state.image = '';
        state.pending = false;
        const input = widget.querySelector('input');
        input.value = '';
        input.setCustomValidity('');
        const preview = widget.querySelector('img');
        preview.removeAttribute('src');
        preview.hidden = true;
        widget.querySelector('.child-photo-remove').hidden = true;
        widget.querySelector('.child-photo-message').textContent = '';
        widget.classList.remove('has-photo');
    }
    function read(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(new Error('Could not read this photo. Please choose it again.'));
            reader.readAsDataURL(file);
        });
    }
    async function prepare(file) {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) throw new Error('Choose a JPG, PNG or WebP image.');
        if (file.size > 5 * 1024 * 1024) throw new Error('Choose a photo smaller than 5 MB.');
        const source = await read(file);
        const image = new Image();
        await new Promise((resolve, reject) => {
            image.onload = resolve;
            image.onerror = () => reject(new Error('This image could not be opened. Please choose another photo.'));
            image.src = source;
        });
        if (!image.naturalWidth || !image.naturalHeight) throw new Error('Please choose a valid photo.');
        const scale = Math.min(1, 400 / Math.max(image.naturalWidth, image.naturalHeight));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
        canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));
        const context = canvas.getContext('2d');
        if (!context) throw new Error('Photo previews are unavailable in this browser.');
        context.fillStyle = '#ffffff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(image, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.8);
    }
    document.addEventListener('change', async event => {
        if (!event.target.matches('.child-photo-input')) return;
        const input = event.target;
        const widget = input.closest('[data-child-photo]');
        const file = input.files[0];
        if (!file) return;
        reset(widget);
        const state = stateFor(widget);
        const version = state.version;
        state.pending = true;
        input.setCustomValidity('Please wait for the photo to finish loading.');
        const message = widget.querySelector('.child-photo-message');
        message.textContent = 'Preparing photo…';
        // Allow cancelling a slow file read without submitting the admission.
        widget.querySelector('.child-photo-remove').hidden = false;
        try {
            const result = await prepare(file);
            if (state.version !== version) return;
            state.image = result;
            const preview = widget.querySelector('img');
            preview.src = result;
            preview.hidden = false;
            widget.classList.add('has-photo');
            input.setCustomValidity('');
            message.textContent = 'Photo ready. It will be saved with the child’s profile.';
        } catch (error) {
            if (state.version !== version) return;
            message.textContent = error.message;
            input.setCustomValidity(error.message);
        } finally {
            if (state.version === version) state.pending = false;
        }
    });
    document.addEventListener('click', event => {
        const button = event.target.closest('.child-photo-remove');
        if (button) reset(button.closest('[data-child-photo]'));
    });
    document.addEventListener('reset', event => {
        event.target.querySelectorAll('[data-child-photo]').forEach(reset);
    });
    window.HopeHavenChildPhoto = {
        get(form) {
            const widget = form.querySelector('[data-child-photo]');
            return widget ? stateFor(widget).image : '';
        },
        ready(form) {
            const input = form.querySelector('.child-photo-input');
            return !input || input.reportValidity();
        }
    };
})();
