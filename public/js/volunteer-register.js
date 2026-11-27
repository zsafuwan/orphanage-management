(function () {
    'use strict';
    const form = document.getElementById('volunteer-form');
    const message = document.getElementById('registration-message');
    if (!form || !message) return;

    const note = form.querySelector('.storage-note');
    if (note) {
        note.textContent = 'Your volunteer registration is saved directly to the MySQL database.';
    }
    message.textContent = '';

    // Load available events from MySQL so volunteers can optionally register for an event
    fetch('/staff-volunteer-data', {headers: {'Accept': 'application/json'}, cache: 'no-store'})
        .then(res => res.ok ? res.json() : null)
        .then(data => {
            if (!data || !Array.isArray(data.event) || !data.event.length) return;
            if (document.getElementById('volunteer-event')) return;
            const label = document.createElement('label');
            label.setAttribute('for', 'volunteer-event');
            label.innerHTML = 'Event sign-up <span>(optional)</span>';
            const select = document.createElement('select');
            select.id = 'volunteer-event';
            select.name = 'Event_Id';
            select.style.cssText = 'width:100%;padding:12px 14px;border-radius:12px;border:1px solid #d5ddd0;background:#fff;font:inherit;margin-bottom:14px;';
            select.add(new Option('General volunteer (no specific event)', ''));
            data.event.forEach(ev => {
                select.add(new Option(`${ev.Title}${ev.Location ? ' · ' + ev.Location : ''}`, String(ev.Event_ID)));
            });
            if (note) {
                note.before(label, select);
            } else {
                form.prepend(label, select);
            }
        })
        .catch(() => {});

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const data = new FormData(form);
        const name = String(data.get('name') || '').trim();
        const email = String(data.get('email') || '').trim().toLowerCase();
        const phone = String(data.get('phone') || '').trim();
        const eventId = data.get('Event_Id') ? Number(data.get('Event_Id')) : null;

        if (!name || !phone) {
            message.textContent = 'Please enter your name and phone number.';
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        message.textContent = 'Saving your volunteer registration...';

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch('/staff-volunteer-data/personnel', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(csrf ? {'X-CSRF-TOKEN': csrf} : {})
                },
                body: JSON.stringify({
                    type: 'volunteer',
                    Name: name,
                    Email: email || null,
                    Contact_Number: phone,
                    ...(eventId ? {Event_Id: eventId} : {})
                })
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                const errText = response.status === 422
                    ? Object.values(result.errors || {}).flat().join(' ')
                    : (result.message || 'Could not save your registration.');
                throw new Error(errText);
            }
            try { localStorage.setItem('hopehaven_mysql_sync_ts', String(Date.now())); } catch (_) {}
            message.textContent = 'Thank you! Your volunteer registration has been saved to the database.';
            form.reset();
        } catch (err) {
            message.textContent = err.message || 'Your details could not be saved. Please try again.';
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
})();
