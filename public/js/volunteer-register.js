(function () {
    'use strict';
    const form = document.getElementById('volunteer-form');
    const message = document.getElementById('registration-message');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const data = new FormData(form);
        const person = {name: String(data.get('name')).trim(), email: String(data.get('email')).trim().toLowerCase(), phone: String(data.get('phone')).trim()};
        if (!person.name || !person.phone) { message.textContent = 'Please enter your name and phone number.'; return; }
        try {
            const key = 'hopehaven_public_volunteers_v1';
            const saved = JSON.parse(localStorage.getItem(key) || '[]');
            if (!Array.isArray(saved)) throw new Error('Invalid registration storage');
            const existing = saved.findIndex(function (row) { return row.email === person.email; });
            if (existing >= 0) saved[existing] = Object.assign({}, saved[existing], person);
            else saved.push(Object.assign({id: crypto.randomUUID(), registeredAt: new Date().toISOString()}, person));
            localStorage.setItem(key, JSON.stringify(saved));
            message.textContent = 'Your volunteer details are saved on this browser. You can sign up for an event when events become available.';
            form.reset();
        } catch (_) { message.textContent = 'Your details could not be saved. Please check that browser storage is available and try again.'; }
    });
})();
