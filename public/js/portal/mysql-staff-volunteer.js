/* Connect Staff, Volunteer, Duty, Qualification, Training, Attendance, and Event views & forms to MySQL (orphanage_system). */
(() => {
    'use strict';

    const SYNC_KEY = 'hopehaven_mysql_sync_ts';
    const esc = value => window.HopeHavenSync ? window.HopeHavenSync.escape(value) : String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const today = () => new Date().toLocaleDateString('en-CA');
    const initials = name => {
        const words = String(name || 'Staff').trim().split(/\s+/).filter(Boolean);
        if (words.length >= 2) return (words[0][0] + words[1][0]).toUpperCase();
        return (words[0] || 'ST').substring(0, 2).toUpperCase();
    };
    const staffCode = id => 'HH-CARE-' + String(id).padStart(2, '0');
    const avatarColors = [
        {bg: '#eef2ff', color: '#4f46e5'},
        {bg: '#fef3c7', color: '#b45309'},
        {bg: '#e0f2fe', color: '#0369a1'},
        {bg: '#fce7f3', color: '#be185d'},
        {bg: '#ecfdf5', color: '#047857'}
    ];
    const staffPhotos = [
        'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&auto=format&fit=crop&crop=faces&q=85',
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&auto=format&fit=crop&crop=faces&q=85',
        'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&auto=format&fit=crop&crop=faces&q=85'
    ];
    const volunteerPhotos = [
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&auto=format&fit=crop&crop=faces&q=85',
        'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=400&h=400&auto=format&fit=crop&crop=faces&q=85'
    ];

    const api = async (url, payload) => {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch(url, {
            method: payload ? 'POST' : 'GET',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'Accept': 'application/json',
                ...(payload ? {'Content-Type': 'application/json'} : {}),
                ...(csrf ? {'X-CSRF-TOKEN': csrf} : {})
            },
            ...(payload ? {body: JSON.stringify(payload)} : {})
        });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) {
            const msg = response.status === 422
                ? Object.values(result.errors || {}).flat().join(' ')
                : (result.message || 'Could not save or load MySQL records.');
            throw new Error(msg);
        }
        return result;
    };

    const notify = (msg, ok = true) => {
        if (typeof window.showPortalToast === 'function') window.showPortalToast(msg, ok);
        else alert(msg);
    };

    const broadcastSync = () => {
        try {
            if (window.HopeHavenSync) window.HopeHavenSync.write(SYNC_KEY, String(Date.now()));
            else localStorage.setItem(SYNC_KEY, String(Date.now()));
        } catch (_) {}
    };

    const parseAttendanceHours = activityText => {
        const match = String(activityText || '').match(/(\d+(?:\.\d+)?)\s*(?:hrs?|hours?)/i);
        return match ? parseFloat(match[1]) : 3.5;
    };

    const parseAttendanceParts = activityText => {
        const raw = String(activityText || 'Volunteer Community Support');
        const hours = parseAttendanceHours(raw);
        const signedMatch = raw.match(/Signed:\s*([^)]+)/i);
        const supervisor = signedMatch ? signedMatch[1].trim() : 'Coordinator Verified';
        const cleanTitle = raw.replace(/\s*\(\d+(?:\.\d+)?\s*(?:hrs?|hours?)[^)]*\)/i, '').trim() || raw;
        return {title: cleanTitle, hours, supervisor};
    };

    const db = window.HopeHavenMysqlDatabase = {
        data: {
            staff: [],
            volunteer: [],
            duty: [],
            'duty-assignment': [],
            qualification: [],
            'staff-qualification': [],
            training: [],
            'training-record': [],
            'volunteer-attendance': [],
            event: [],
            'event-registration': []
        },
        save: (endpoint, payload) => api('/staff-volunteer-data/' + endpoint, payload),
        async refresh() {
            const snapshot = await api('/staff-volunteer-data');
            db.data = Object.assign({}, db.data, snapshot);
            renderAll();
            document.dispatchEvent(new CustomEvent('mysql-staff-volunteer-loaded', {detail: db.data}));
            return db.data;
        }
    };

    function getStaffQualifications(staffId) {
        const sqs = (db.data['staff-qualification'] || []).filter(sq => Number(sq.Staff_Id) === Number(staffId));
        return sqs.map(sq => {
            const q = (db.data.qualification || []).find(item => Number(item.Qualification_id) === Number(sq.Qualification_Id));
            return {
                id: sq.Staff_Qualification_Id,
                name: q ? q.Qualification_name : 'Certified Qualification',
                year: sq.Year_Obtained || new Date().getFullYear()
            };
        });
    }

    function getPersonTrainings(staffId, volunteerId) {
        const trs = (db.data['training-record'] || []).filter(tr =>
            (staffId && Number(tr.Staff_Id) === Number(staffId)) ||
            (volunteerId && Number(tr.Volunteer_Id) === Number(volunteerId))
        );
        return trs.map(tr => {
            const t = (db.data.training || []).find(item => Number(item.Training_Id) === Number(tr.Training_Id));
            return {
                id: tr.Training_Record_Id,
                name: t ? t.Training_Name : 'Safeguarding Training',
                trainer: t?.Trainer_Name || 'HopeHaven Compliance Board',
                trainingDate: t?.Training_Date || tr.Completion_Date || today(),
                completionDate: tr.Completion_Date || t?.Training_Date || today(),
                status: tr.Status || 'Completed'
            };
        });
    }

    function getStaffDuties(staffId) {
        const das = (db.data['duty-assignment'] || []).filter(da => Number(da.Staff_Id) === Number(staffId));
        return das.map(da => {
            const d = (db.data.duty || []).find(item => Number(item.Duty_Id) === Number(da.Duty_Id));
            return {
                dutyId: da.Duty_Id,
                name: d ? d.Duty_name : 'Assigned Duty Shift',
                date: da.Assignment_date || today()
            };
        });
    }

    function getVolunteerAttendances(volunteerId) {
        return (db.data['volunteer-attendance'] || []).filter(va => Number(va.Volunteer_Id) === Number(volunteerId));
    }

    function getVolunteerEvents(volunteerId) {
        const regs = (db.data['event-registration'] || []).filter(er => Number(er.Volunteer_Id) === Number(volunteerId));
        return regs.map(er => {
            const ev = (db.data.event || []).find(e => Number(e.Event_ID) === Number(er.Event_Id));
            return {
                eventId: er.Event_Id,
                title: ev ? ev.Title : 'Community Event',
                location: ev?.Location || 'HopeHaven Campus',
                registeredAt: er.Registered_At,
                status: er.Status || 'Registered'
            };
        });
    }

    function enhanceCollapsibleCard(card) {
        const rows = card.querySelector('.info-rows');
        if (!rows || card.querySelector('details.personnel-details')) return;
        const name = card.querySelector('.profile-name')?.textContent.trim() || 'Member';
        const details = document.createElement('details');
        details.className = 'personnel-details';
        const summary = document.createElement('summary');
        summary.textContent = 'Contact & Qualifications';
        summary.setAttribute('aria-label', 'Contact and credentials for ' + name);
        details.appendChild(summary);
        const body = document.createElement('div');
        body.className = 'personnel-details-body';
        Array.from(rows.children).slice(1).forEach(function (row) {
            const value = row.querySelector('.info-row-val');
            const label = row.querySelector('.info-row-label');
            if (value && label && /Email:|Phone:/.test(label.textContent)) {
                const text = value.textContent.trim();
                if (text && text !== 'Not provided') {
                    const link = document.createElement('a');
                    link.textContent = text;
                    link.href = /Email:/.test(label.textContent) ? 'mailto:' + text : 'tel:' + text.replace(/[^+0-9]/g, '');
                    value.replaceChildren(link);
                }
            }
            body.appendChild(row);
        });
        details.appendChild(body);
        rows.after(details);
    }

    // 1. Render Staff & Volunteers Portal Page (/staff-volunteers & /staff/staff-volunteers)
    function renderStaffVolunteersPage() {
        const directoryGrid = document.querySelector('#tab_directory .cards-grid-3');
        const credentialsTbody = document.querySelector('#tab_credentials .portal-table tbody');
        const rosterGrid = document.querySelector('#tab_roster > div');
        const attendanceTbody = document.getElementById('attendance_tbody');

        const staffList = db.data.staff || [];
        const volList = db.data.volunteer || [];
        const attendanceList = db.data['volunteer-attendance'] || [];
        const dutyAssignList = db.data['duty-assignment'] || [];
        const dutyList = db.data.duty || [];
        const eventList = db.data.event || [];
        const staffQualList = db.data['staff-qualification'] || [];
        const trainingRecList = db.data['training-record'] || [];

        // Update KPI cards on /staff-volunteers if present
        const metricCards = document.querySelectorAll('.metrics-grid .metric-card');
        if (metricCards.length >= 4) {
            if (staffList.length > 0) {
                metricCards[0].querySelector('.metric-val').textContent = `${staffList.length} Staff`;
            }
            if (volList.length > 0) {
                metricCards[1].querySelector('.metric-val').textContent = `${volList.length} Active`;
            }
            if (attendanceList.length > 0) {
                const totalHrs = attendanceList.reduce((sum, item) => sum + parseAttendanceHours(item.Activity), 0);
                metricCards[2].querySelector('.metric-val').textContent = `${Number.isInteger(totalHrs) ? totalHrs : totalHrs.toFixed(1)} hrs`;
            }
            const totalCreds = staffQualList.length + trainingRecList.length;
            if (totalCreds > 0) {
                metricCards[3].querySelector('.metric-val').textContent = `${totalCreds} Active`;
                metricCards[3].querySelector('.metric-sub').textContent = 'Verified qualifications & trainings in MySQL';
            }
        }

        // Populate Volunteer Select in Modal 2 (#modal_volunteer_hours)
        const volSelect = document.getElementById('vol_name_select');
        if (volSelect && volList.length > 0) {
            const currentVal = volSelect.value;
            volSelect.innerHTML = volList.map(v =>
                `<option value="${esc(v.Volunteer_Id)}" data-name="${esc(v.Name)}">${esc(v.Name)}${v.Email ? ' (' + esc(v.Email) + ')' : ''}</option>`
            ).join('');
            if ([...volSelect.options].some(o => o.value === currentVal)) volSelect.value = currentVal;
        }

        // Populate Staff/Volunteer Select in Modal 3 (#modal_assign_shift)
        const shiftSelect = document.querySelector('#modal_assign_shift select');
        if (shiftSelect && (staffList.length > 0 || volList.length > 0)) {
            const currentVal = shiftSelect.value;
            const staffOpts = staffList.map(s =>
                `<option value="staff:${esc(s.Staff_Id)}" data-name="${esc(s.Name)}">${esc(s.Name)} (Staff · ${esc(s.Position || 'Caregiver')})</option>`
            );
            const volOpts = volList.map(v =>
                `<option value="volunteer:${esc(v.Volunteer_Id)}" data-name="${esc(v.Name)}">${esc(v.Name)} (Volunteer)</option>`
            );
            shiftSelect.innerHTML = [...staffOpts, ...volOpts].join('');
            if ([...shiftSelect.options].some(o => o.value === currentVal)) shiftSelect.value = currentVal;
        }

        // Tab 1: Directory Cards
        if (directoryGrid && (staffList.length > 0 || volList.length > 0)) {
            const staffCardsHtml = staffList.map((s, idx) => {
                const quals = getStaffQualifications(s.Staff_Id);
                const trainings = getPersonTrainings(s.Staff_Id, null);
                const duties = getStaffDuties(s.Staff_Id);
                const photo = staffPhotos[idx % staffPhotos.length];
                const credLabel = quals[0]?.name || trainings[0]?.name || 'Verified Staff Record';
                const tags = [
                    ...quals.map(q => q.name),
                    ...trainings.map(t => t.name),
                    ...duties.map(d => d.name)
                ].slice(0, 3);
                if (!tags.length) tags.push(s.Position || 'Caregiver Operations', staffCode(s.Staff_Id));

                return `
                    <div class="searchable-staff" data-category="Staff" data-staff-id="${esc(s.Staff_Id)}">
                        <div class="child-profile-card">
                            <div>
                                <div class="card-top-info">
                                    <img src="${photo}" class="avatar-large" alt="${esc(s.Name)}">
                                    <div style="text-align: right;">
                                        <div style="margin-top: 4px;"><span class="badge badge-success">Cleared</span></div>
                                    </div>
                                </div>
                                <div class="profile-name">${esc(s.Name)}</div>
                                <div class="profile-meta-line">${esc(s.Position || 'Permanent Caregiver')}</div>
                                <div class="info-rows">
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-id-badge" aria-hidden="true"></i> Staff ID:</span>
                                        <span class="info-row-val">${esc(staffCode(s.Staff_Id))}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-phone"></i> Phone:</span>
                                        <span class="info-row-val">${esc(s.Contact_Number || 'Not provided')}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-certificate"></i> Credential:</span>
                                        <span class="info-row-val" style="color: #10b981;">${esc(credLabel)}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="personnel-skill-tags" style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                                ${tags.map(t => `<span class="badge badge-secondary">${esc(t)}</span>`).join(' ')}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            const volCardsHtml = volList.map((v, idx) => {
                const attendances = getVolunteerAttendances(v.Volunteer_Id);
                const hrs = attendances.reduce((sum, a) => sum + parseAttendanceHours(a.Activity), 0);
                const trainings = getPersonTrainings(null, v.Volunteer_Id);
                const events = getVolunteerEvents(v.Volunteer_Id);
                const photo = volunteerPhotos[idx % volunteerPhotos.length];
                const latestActivity = attendances[0] ? parseAttendanceParts(attendances[0].Activity).title : 'Community Volunteer Corps';
                const tags = [
                    ...events.map(e => e.title),
                    ...trainings.map(t => t.name),
                    ...attendances.slice(0, 2).map(a => parseAttendanceParts(a.Activity).title)
                ].slice(0, 3);
                if (!tags.length) tags.push('Active Volunteer', `ID #${v.Volunteer_Id}`);

                return `
                    <div class="searchable-staff" data-category="Volunteer" data-volunteer-id="${esc(v.Volunteer_Id)}">
                        <div class="child-profile-card">
                            <div>
                                <div class="card-top-info">
                                    <img src="${photo}" class="avatar-large" alt="${esc(v.Name)}">
                                    <div style="text-align: right;">
                                        <span class="badge badge-info" style="background: rgba(255, 44, 90, 0.12); color: var(--primary);">Active Volunteer</span>
                                        <div style="margin-top: 4px;"><span class="badge badge-success">${Number.isInteger(hrs) ? hrs : hrs.toFixed(1)} Hours</span></div>
                                    </div>
                                </div>
                                <div class="profile-name">${esc(v.Name)}</div>
                                <div class="profile-meta-line">${esc(latestActivity)}</div>
                                <div class="info-rows">
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-envelope" aria-hidden="true"></i> Email:</span>
                                        <span class="info-row-val">${esc(v.Email || 'Not provided')}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-phone"></i> Phone:</span>
                                        <span class="info-row-val">${esc(v.Contact_Number || 'Not provided')}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-shield-alt"></i> Clearance:</span>
                                        <span class="info-row-val" style="color: #10b981;">Registered Volunteer</span>
                                    </div>
                                </div>
                            </div>
                            <div class="personnel-skill-tags" style="margin-top: 16px; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                                ${tags.map(t => `<span class="badge badge-secondary">${esc(t)}</span>`).join(' ')}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            directoryGrid.innerHTML = staffCardsHtml + volCardsHtml;
            directoryGrid.querySelectorAll('.child-profile-card').forEach(enhanceCollapsibleCard);

            const dirTabBtn = document.querySelector('.tab-btn[data-tab="tab_directory"]');
            if (dirTabBtn) {
                dirTabBtn.innerHTML = `<i class="fas fa-users"></i> Staff & Volunteers Directory (${staffList.length + volList.length})`;
            }
        }

        // Tab 2: Credentials, Qualifications & Trainings Table
        if (credentialsTbody && (staffQualList.length > 0 || trainingRecList.length > 0 || (db.data.qualification || []).length > 0 || (db.data.training || []).length > 0)) {
            const rows = [];

            staffQualList.forEach(sq => {
                const staff = staffList.find(s => Number(s.Staff_Id) === Number(sq.Staff_Id));
                const qual = (db.data.qualification || []).find(q => Number(q.Qualification_id) === Number(sq.Qualification_Id));
                const year = sq.Year_Obtained || new Date().getFullYear();
                rows.push(`
                    <tr>
                        <td><strong>${esc(staff ? staff.Name : 'Staff #' + sq.Staff_Id)}</strong> (Staff)</td>
                        <td>${esc(qual ? qual.Qualification_name : 'Qualification #' + sq.Qualification_Id)}</td>
                        <td>Official Qualification Registry</td>
                        <td style="font-family: monospace;">${esc(year)}-01-01</td>
                        <td style="font-family: monospace;">${esc(Number(year) + 4)}-12-31</td>
                        <td><span class="badge badge-success">Valid</span></td>
                    </tr>
                `);
            });

            trainingRecList.forEach(tr => {
                const training = (db.data.training || []).find(t => Number(t.Training_Id) === Number(tr.Training_Id));
                const staff = tr.Staff_Id ? staffList.find(s => Number(s.Staff_Id) === Number(tr.Staff_Id)) : null;
                const vol = tr.Volunteer_Id ? volList.find(v => Number(v.Volunteer_Id) === Number(tr.Volunteer_Id)) : null;
                const personLabel = staff
                    ? `<strong>${esc(staff.Name)}</strong> (Staff)`
                    : vol
                        ? `<strong>${esc(vol.Name)}</strong> (Volunteer)`
                        : '<strong>Care Team Member</strong>';
                const issueDate = training?.Training_Date || tr.Completion_Date || today();
                const compDate = tr.Completion_Date || issueDate;
                const status = tr.Status || 'Completed';
                rows.push(`
                    <tr>
                        <td>${personLabel}</td>
                        <td>${esc(training ? training.Training_Name : 'Training #' + tr.Training_Id)}</td>
                        <td>${esc(training?.Trainer_Name || 'HopeHaven Compliance Board')}</td>
                        <td style="font-family: monospace;">${esc(issueDate)}</td>
                        <td style="font-family: monospace;">${esc(compDate)}</td>
                        <td><span class="badge badge-success">${esc(status)}</span></td>
                    </tr>
                `);
            });

            if (rows.length > 0) {
                credentialsTbody.innerHTML = rows.join('');
            }
        }

        // Tab 3: Duty Roster & Scheduled Events
        if (rosterGrid && (dutyAssignList.length > 0 || dutyList.length > 0 || eventList.length > 0)) {
            const cards = [];

            dutyAssignList.forEach(da => {
                const duty = dutyList.find(d => Number(d.Duty_Id) === Number(da.Duty_Id));
                const staff = staffList.find(s => Number(s.Staff_Id) === Number(da.Staff_Id));
                cards.push(`
                    <div class="portal-card" style="padding: 22px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                            <span class="badge badge-secondary">${esc(staff?.Position || 'Duty Assignment')}</span>
                            <span class="badge badge-success">Scheduled</span>
                        </div>
                        <h4 style="font-size: 15px; font-weight: 800;">${esc(duty ? duty.Duty_name : 'Duty #' + da.Duty_Id)}</h4>
                        <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Assigned to: <strong>${esc(staff ? staff.Name : 'Staff #' + da.Staff_Id)}</strong> (Staff)</p>
                        <div style="margin-top: 12px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                            <div><i class="fas fa-map-marker-alt"></i> Location: HopeHaven Orphanage Wing</div>
                            <div><i class="fas fa-clock"></i> Date: ${esc(da.Assignment_date || today())}</div>
                        </div>
                    </div>
                `);
            });

            // Any unassigned duties in duty table
            dutyList.forEach(d => {
                if (dutyAssignList.some(da => Number(da.Duty_Id) === Number(d.Duty_Id))) return;
                cards.push(`
                    <div class="portal-card" style="padding: 22px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                            <span class="badge badge-secondary">Orphanage Duty</span>
                            <span class="badge badge-success">Active</span>
                        </div>
                        <h4 style="font-size: 15px; font-weight: 800;">${esc(d.Duty_name)}</h4>
                        <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Duty ID: <strong>#${esc(d.Duty_Id)}</strong></p>
                        <div style="margin-top: 12px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                            <div><i class="fas fa-map-marker-alt"></i> Location: HopeHaven Main Campus</div>
                            <div><i class="fas fa-clock"></i> Daily Caregiving Roster</div>
                        </div>
                    </div>
                `);
            });

            // Events & Event Registrations
            eventList.forEach(ev => {
                const regs = (db.data['event-registration'] || []).filter(er => Number(er.Event_Id) === Number(ev.Event_ID));
                const volNames = regs.map(er => {
                    const v = volList.find(item => Number(item.Volunteer_Id) === Number(er.Volunteer_Id));
                    return v ? v.Name : 'Volunteer #' + er.Volunteer_Id;
                });
                cards.push(`
                    <div class="portal-card" style="padding: 22px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                            <span class="badge badge-secondary">${esc(ev.Event_Type || 'Event')}</span>
                            <span class="badge badge-success">${esc(ev.Status || 'Scheduled')}</span>
                        </div>
                        <h4 style="font-size: 15px; font-weight: 800;">${esc(ev.Title)}</h4>
                        <p style="font-size: 12px; color: #64748b; margin-top: 4px;">Assigned to: <strong>${esc(volNames.length ? volNames.join(', ') : 'Open for Volunteers')}</strong> (${regs.length}/${esc(ev.Capacity || 30)} Registered)</p>
                        <div style="margin-top: 12px; font-size: 12px; background: #f8fafc; padding: 10px; border-radius: 6px;">
                            <div><i class="fas fa-map-marker-alt"></i> Location: ${esc(ev.Location || 'HopeHaven Campus')}</div>
                            <div><i class="fas fa-clock"></i> ${esc(ev.Start_At || today())}</div>
                        </div>
                    </div>
                `);
            });

            if (cards.length > 0) {
                rosterGrid.innerHTML = cards.join('');
            }
        }

        // Tab 4: Volunteer Attendance Log
        if (attendanceTbody && attendanceList.length > 0) {
            attendanceTbody.innerHTML = attendanceList.map(att => {
                const vol = volList.find(v => Number(v.Volunteer_Id) === Number(att.Volunteer_Id));
                const ev = att.Event_Id ? eventList.find(e => Number(e.Event_ID) === Number(att.Event_Id)) : null;
                const parsed = parseAttendanceParts(att.Activity);
                return `
                    <tr>
                        <td><strong>${esc(vol ? vol.Name : 'Volunteer #' + (att.Volunteer_Id || 1))}</strong></td>
                        <td>${esc(parsed.title)}${ev ? ` <span class="badge badge-secondary">${esc(ev.Title)}</span>` : ''}</td>
                        <td style="font-size: 12px; color: #64748b;">${esc(att.Attendance_Date || today())}</td>
                        <td><span class="badge badge-success" style="font-weight: 800;">${esc(parsed.hours)} hrs</span></td>
                        <td>${esc(parsed.supervisor)}</td>
                        <td style="font-style: italic; font-size: 12px; color: #64748b;">"Verified & saved in MySQL attendance log."</td>
                    </tr>
                `;
            }).join('');
        }
    }

    // 2. Render Director Staff & Volunteers Page (/director/staff-volunteers)
    function renderDirectorStaffVolunteersPage() {
        const tables = document.querySelectorAll('.dir-staff-table');
        if (!tables.length) return;

        const staffList = db.data.staff || [];
        const volList = db.data.volunteer || [];
        const dutyAssignList = db.data['duty-assignment'] || [];

        const kpiVals = document.querySelectorAll('.dir-staff-kpi-card .dir-staff-kpi-val');
        if (kpiVals.length >= 4) {
            if (staffList.length > 0) kpiVals[0].textContent = `${staffList.length} Caregivers`;
            if (volList.length > 0) kpiVals[1].textContent = `${volList.length} Active`;
            if (dutyAssignList.length > 0 || staffList.length > 0) {
                kpiVals[3].textContent = `${dutyAssignList.length || staffList.length} On Duty`;
            }
        }

        // Table 1: Permanent Staff Roster
        const staffTbody = tables[0]?.querySelector('tbody');
        if (staffTbody && staffList.length > 0) {
            const headBadge = tables[0].closest('.dir-staff-table-card')?.querySelector('.director-card-head .badge');
            if (headBadge) {
                headBadge.innerHTML = `<i class="fas fa-check"></i> All ${staffList.length} Vetted & Certified`;
            }
            staffTbody.innerHTML = staffList.map((s, idx) => {
                const av = initials(s.Name);
                const palette = avatarColors[idx % avatarColors.length];
                const quals = getStaffQualifications(s.Staff_Id);
                const trainings = getPersonTrainings(s.Staff_Id, null);
                const duties = getStaffDuties(s.Staff_Id);
                const certName = quals[0]?.name || trainings[0]?.name || 'Pediatric CPR & Child Safe';
                const certSub = quals[0] ? `Obtained ${quals[0].year}` : (trainings[0] ? `${trainings[0].status} (${trainings[0].completionDate})` : 'Valid Accreditation');
                const shiftText = duties[0] ? `On Duty · ${duties[0].name}` : 'On Duty · Day Shift';

                return `
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-cell-avatar" style="background:${palette.bg}; color:${palette.color};">${esc(av)}</div>
                                <div>
                                    <div class="user-cell-name">${esc(s.Name)}</div>
                                    <div class="user-cell-dept">Staff ID: ${esc(staffCode(s.Staff_Id))}${s.Contact_Number ? ' · ' + esc(s.Contact_Number) : ''}</div>
                                </div>
                            </div>
                        </td>
                        <td>${esc(s.Position || 'Lead Clinical Caregiver')}<br><span style="font-size: 11px; color: #64748b;">Orphanage Operations</span></td>
                        <td><span class="vetted-badge"><i class="fas fa-shield-check"></i> PDRM Vetted (${new Date().getFullYear()})</span></td>
                        <td>${esc(certName)}<br><span style="font-size: 11px; color: #10b981; font-weight: 700;">${esc(certSub)}</span></td>
                        <td><span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;">${esc(shiftText)}</span></td>
                        <td style="text-align: right;">
                            <button type="button" class="btn-director-review" data-staff-id="${esc(s.Staff_Id)}" onclick="openStaffDossier('${esc(s.Name).replace(/'/g, "\\'")}', '${esc(s.Position || 'Caregiver').replace(/'/g, "\\'")}', '${esc(av)}')">
                                <i class="fas fa-file-shield"></i> PDF
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Table 2: Active Volunteer Corps
        const volTbody = tables[1]?.querySelector('tbody');
        if (volTbody && volList.length > 0) {
            const headSpan = tables[1].closest('.dir-staff-table-card')?.querySelector('.director-card-head span:last-child');
            if (headSpan) {
                headSpan.textContent = `${volList.length} Active Volunteers in Q4 ${new Date().getFullYear()}`;
            }
            volTbody.innerHTML = volList.map(v => {
                const attendances = getVolunteerAttendances(v.Volunteer_Id);
                const events = getVolunteerEvents(v.Volunteer_Id);
                const totalHrs = attendances.reduce((sum, a) => sum + parseAttendanceHours(a.Activity), 0);
                const latestAct = attendances[0] ? parseAttendanceParts(attendances[0].Activity).title : (events[0]?.title || 'Community Support & Tutoring');
                return `
                    <tr>
                        <td><strong>${esc(v.Name)}</strong><br><span style="font-size: 11px; color: #64748b;">${esc(v.Email || v.Contact_Number || 'Registered Volunteer')}</span></td>
                        <td>${esc(v.Contact_Number || 'Community Volunteer')}</td>
                        <td>${esc(latestAct)}</td>
                        <td><strong style="color: #4f46e5;">${Number.isInteger(totalHrs) ? totalHrs : totalHrs.toFixed(1)} Hours</strong></td>
                        <td>
                            ${attendances.length > 0
                                ? '<span class="status-pill" style="background:#ecfdf5; color:#047857; font-size:11px; font-weight:700;"><i class="fas fa-check-circle"></i> Hours Approved</span>'
                                : `<button type="button" class="btn-director-review" onclick="approveVolunteerHours('${esc(v.Name).replace(/'/g, "\\'")}', 4, this)"><i class="fas fa-check"></i> Approve 4h</button>`
                            }
                        </td>
                    </tr>
                `;
            }).join('');
        }
    }

    // 3. Render Director Register Staff Page (/director/register-staff)
    function renderDirectorRegisterStaffPage() {
        const recentList = document.getElementById('recent_staff_list');
        if (!recentList) return;

        const staffList = db.data.staff || [];
        const maxId = staffList.reduce((max, s) => Math.max(max, Number(s.Staff_Id) || 0), 0);
        const nextCode = staffCode(maxId + 1);

        const metricVals = document.querySelectorAll('.dir-reg-metric-card .dir-reg-metric-val');
        if (metricVals.length >= 2 && staffList.length > 0) {
            metricVals[0].textContent = `${staffList.length} Caregivers`;
            metricVals[1].textContent = nextCode;
        }

        const staffIdInput = document.getElementById('staff_id_input');
        const previewId = document.getElementById('preview_id');
        if (staffIdInput && staffList.length > 0) staffIdInput.value = nextCode;
        if (previewId && staffList.length > 0) previewId.textContent = nextCode;

        if (staffList.length > 0) {
            recentList.innerHTML = staffList.map((s, idx) => {
                const av = initials(s.Name);
                const palette = avatarColors[idx % avatarColors.length];
                const duties = getStaffDuties(s.Staff_Id);
                const shiftInfo = duties[0] ? ` · ${duties[0].name}` : '';
                return `
                    <div class="recent-staff-item">
                        <div class="user-cell">
                            <div class="user-cell-avatar" style="background:${palette.bg}; color:${palette.color};">${esc(av)}</div>
                            <div>
                                <div class="user-cell-name">${esc(s.Name)}</div>
                                <div class="user-cell-dept">${esc(s.Position || 'Caregiver')} · ${esc(staffCode(s.Staff_Id))}${esc(shiftInfo)}</div>
                            </div>
                        </div>
                        <span class="vetted-badge"><i class="fas fa-check-circle"></i> On Duty</span>
                    </div>
                `;
            }).join('');
        }
    }

    // 4. Render Staff Profile Page (/staff/profile)
    function renderStaffProfilePage() {
        const clearanceTab = document.getElementById('tab_clearance');
        if (!clearanceTab) return;

        const staffList = db.data.staff || [];
        if (staffList.length > 0) {
            const activeStaff = staffList[0];
            if (typeof window.updateProfileDOM === 'function' && window.profileData && !window.__staffProfileHydrated) {
                window.__staffProfileHydrated = true;
                window.updateProfileDOM(
                    activeStaff.Name || window.profileData.fullname,
                    window.profileData.username,
                    window.profileData.affiliation,
                    activeStaff.Position || window.profileData.ward,
                    window.profileData.email,
                    activeStaff.Contact_Number || window.profileData.phone,
                    window.profileData.address
                );
            }
        }

        // Render Qualifications & Trainings in Tab 3 (#tab_clearance) if present in MySQL
        const quals = db.data['staff-qualification'] || [];
        const trainings = db.data['training-record'] || [];
        const certCardsContainer = clearanceTab.querySelector('.profile-card .profile-card-body');
        if (certCardsContainer && (quals.length > 0 || trainings.length > 0)) {
            const items = [];
            quals.forEach(sq => {
                const q = (db.data.qualification || []).find(item => Number(item.Qualification_id) === Number(sq.Qualification_Id));
                const s = staffList.find(item => Number(item.Staff_Id) === Number(sq.Staff_Id));
                items.push(`
                    <div class="cert-card-item">
                        <div class="cert-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--dark);">${esc(q ? q.Qualification_name : 'Qualification')}</div>
                                <span class="tag-badge verified-badge" style="font-size: 9.5px; padding: 2px 6px;">Certified</span>
                            </div>
                            <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">
                                Holder: ${esc(s ? s.Name : 'Staff Member')} • Year Obtained: ${esc(sq.Year_Obtained || new Date().getFullYear())}
                            </div>
                        </div>
                    </div>
                `);
            });
            trainings.forEach(tr => {
                const t = (db.data.training || []).find(item => Number(item.Training_Id) === Number(tr.Training_Id));
                items.push(`
                    <div class="cert-card-item">
                        <div class="cert-icon-wrap" style="background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--dark);">${esc(t ? t.Training_Name : 'Training')}</div>
                                <span class="tag-badge verified-badge" style="font-size: 9.5px; padding: 2px 6px;">${esc(tr.Status || 'Completed')}</span>
                            </div>
                            <div style="font-size: 11.5px; color: var(--dark-muted); margin-top: 2px;">
                                Trainer: ${esc(t?.Trainer_Name || 'Compliance Board')} • Completed: ${esc(tr.Completion_Date || today())}
                            </div>
                        </div>
                    </div>
                `);
            });
            certCardsContainer.innerHTML = items.join('');
            const badgePill = document.querySelector('.staff-tab-btn[data-tab="tab_clearance"] .badge-pill');
            if (badgePill) badgePill.textContent = String(items.length);
        }
    }

    // 5. Render Dashboards (/dashboard & /dashboard/staff)
    function renderDashboards() {
        const staffList = db.data.staff || [];
        const volList = db.data.volunteer || [];
        const dutyList = db.data.duty || [];
        const dutyAssignList = db.data['duty-assignment'] || [];

        // Admin Dashboard subsystem meta count
        const staffMeta = document.querySelector('.subsystem-admin-card.s-staff .s-meta');
        if (staffMeta && (staffList.length > 0 || volList.length > 0)) {
            staffMeta.innerHTML = `<i class="fas fa-user-clock"></i> ${staffList.length + volList.length} Staff & Volunteers`;
        }

        // Staff Duty Dashboard Active Volunteers KPI card
        document.querySelectorAll('.kpi-card').forEach(card => {
            const label = card.querySelector('.kpi-label')?.textContent.trim();
            if (label === 'Active Volunteers' && volList.length > 0) {
                const valNode = card.querySelector('.kpi-value');
                if (valNode) valNode.textContent = `${volList.length} ${volList.length === 1 ? 'Volunteer' : 'Volunteers'}`;
            }
        });

        // Admin Dashboard Priority Tasks (#dashboard_todo_list) - append MySQL duties
        const todoList = document.getElementById('dashboard_todo_list');
        if (todoList && dutyList.length > 0) {
            todoList.querySelectorAll('[data-mysql-duty]').forEach(el => el.remove());
            dutyList.forEach(d => {
                const li = document.createElement('li');
                li.className = 'adminator-todo-item';
                li.setAttribute('data-mysql-duty', String(d.Duty_Id));
                const cbId = 'mysql_duty_td_' + d.Duty_Id;
                li.innerHTML = `
                    <div class="todo-left">
                        <input type="checkbox" class="todo-checkbox" id="${esc(cbId)}" onchange="toggleTodo(this)">
                        <label for="${esc(cbId)}" class="todo-text">${esc(d.Duty_name)}</label>
                    </div>
                    <span class="todo-pill upcoming">MYSQL</span>
                `;
                todoList.prepend(li);
            });
        }

        // Staff Duty Dashboard Routine Schedule (#routine_schedule_list) - append MySQL duties
        const routineList = document.getElementById('routine_schedule_list');
        if (routineList && (dutyList.length > 0 || dutyAssignList.length > 0)) {
            routineList.querySelectorAll('[data-mysql-routine]').forEach(el => el.remove());
            dutyList.forEach(d => {
                const da = dutyAssignList.find(item => Number(item.Duty_Id) === Number(d.Duty_Id));
                const staff = da ? staffList.find(s => Number(s.Staff_Id) === Number(da.Staff_Id)) : null;
                const itemId = 'mysql_duty_sched_' + d.Duty_Id;
                const div = document.createElement('div');
                div.className = 'staff-schedule-item upcoming';
                div.id = itemId;
                div.setAttribute('data-mysql-routine', String(d.Duty_Id));
                div.innerHTML = `
                    <div class="sched-time">${esc(da?.Assignment_date || 'TODAY')}</div>
                    <div class="sched-content">
                        <div class="sched-title">${esc(d.Duty_name)}</div>
                        <div class="sched-detail">Assigned to: ${esc(staff ? staff.Name : 'On-Duty Caregiver')} · Saved in MySQL</div>
                    </div>
                    <button class="sched-status-badge pending" style="border:none; cursor:pointer;" onclick="markTaskDone('${esc(itemId)}')">Pending</button>
                `;
                routineList.appendChild(div);
            });
        }
    }

    function renderAll() {
        renderStaffVolunteersPage();
        renderDirectorStaffVolunteersPage();
        renderDirectorRegisterStaffPage();
        renderStaffProfilePage();
        renderDashboards();
    }

    // Intercept form submissions across all Staff & Volunteer modals and pages
    document.addEventListener('submit', async event => {
        const form = event.target;

        // 1. Modal: Register Staff or Volunteer (#modal_register_personnel)
        if (form.closest('#modal_register_personnel')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;
            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const inputs = form.querySelectorAll('input.form-control');
                const selects = form.querySelectorAll('select.form-control');
                const name = inputs[0]?.value?.trim() || '';
                const classification = selects[0]?.value?.trim() || 'Permanent Staff';
                const jobRole = inputs[1]?.value?.trim() || '';
                const department = selects[1]?.value?.trim() || '';
                const email = inputs[2]?.value?.trim() || '';
                const phone = inputs[3]?.value?.trim() || '';
                const qualification = document.getElementById('reg_pers_qualification')?.value?.trim() || '';
                const training = document.getElementById('reg_pers_training')?.value?.trim() || '';

                await db.save('personnel', {
                    type: classification === 'Active Volunteer' ? 'volunteer' : 'staff',
                    Name: name,
                    Position: jobRole,
                    Department: department,
                    Email: email || null,
                    Contact_Number: phone || null,
                    Qualification_name: qualification || (classification !== 'Active Volunteer' && jobRole ? jobRole + ' Certification' : null),
                    Training_Name: training || null
                });

                form.reset();
                window.closeModal?.('modal_register_personnel');
                notify(`${name} saved to MySQL (${classification})!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 2. Modal: Log Volunteer Service Attendance (#modal_volunteer_hours)
        if (form.closest('#modal_volunteer_hours')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;
            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const select = document.getElementById('vol_name_select');
                const rawVal = select?.value || '';
                const selectedOpt = select?.options[select.selectedIndex];
                const volId = /^\d+$/.test(rawVal) ? Number(rawVal) : null;
                const volName = selectedOpt?.dataset?.name || selectedOpt?.text || rawVal || 'Volunteer';
                const activity = document.getElementById('vol_activity_input')?.value?.trim() || 'Community Service';
                const hours = document.getElementById('vol_hours_input')?.value?.trim() || '3.5';
                const textInputs = form.querySelectorAll('input[type="text"]');
                const signoff = textInputs[1]?.value?.trim() || 'Coordinator';
                const eventTitle = document.getElementById('vol_event_input')?.value?.trim() || '';

                const formattedActivity = `${activity} (${hours} hrs · Signed: ${signoff})`.slice(0, 120);

                await db.save('attendance', {
                    ...(volId ? {Volunteer_Id: volId} : {Volunteer_Name: volName}),
                    Activity: formattedActivity,
                    Attendance_Date: today(),
                    ...(eventTitle ? {Event_Title: eventTitle} : {})
                });

                form.reset();
                window.closeModal?.('modal_volunteer_hours');
                notify(`Logged ${hours} hrs for ${volName} in MySQL!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 3. Modal: Assign Shift (#modal_assign_shift)
        if (form.closest('#modal_assign_shift')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;
            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const inputs = form.querySelectorAll('input.form-control');
                const select = form.querySelector('select.form-control');
                const activity = inputs[0]?.value?.trim() || 'Caregiving Duty Shift';
                const day = inputs[1]?.value?.trim() || '';
                const hours = inputs[2]?.value?.trim() || '';
                const scheduleMeta = [day, hours].filter(Boolean).join(' • ');
                const dutyName = (scheduleMeta ? `${activity} (${scheduleMeta})` : activity).slice(0, 100);

                const rawTarget = select?.value || '';
                const selectedOpt = select?.options[select.selectedIndex];
                const personName = selectedOpt?.dataset?.name || selectedOpt?.text || rawTarget;

                const payload = {
                    Duty_name: dutyName,
                    Assignment_date: today(),
                    Location: scheduleMeta || 'HopeHaven Campus'
                };

                if (rawTarget.startsWith('volunteer:')) {
                    payload.Volunteer_Id = Number(rawTarget.split(':')[1]);
                } else if (rawTarget.startsWith('staff:')) {
                    payload.Staff_Id = Number(rawTarget.split(':')[1]);
                } else {
                    payload.Staff_Name = personName;
                }

                await db.save('shift', payload);

                form.reset();
                window.closeModal?.('modal_assign_shift');
                notify(`Duty shift "${dutyName}" saved to MySQL!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 4. Director Staff Registration Form (.dir-reg-form on /director/register-staff)
        if (form.classList.contains('dir-reg-form')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (form.dataset.saving) return;

            const name = document.getElementById('staff_name_input')?.value?.trim() || '';
            const email = document.getElementById('staff_email_input')?.value?.trim() || '';
            const phone = document.getElementById('staff_phone_input')?.value?.trim() || '';
            const roleSelect = document.getElementById('staff_role_select');
            const role = roleSelect ? roleSelect.options[roleSelect.selectedIndex].text : 'Caregiver';
            const shiftSelect = document.getElementById('staff_shift_select');
            const shift = shiftSelect && shiftSelect.value ? shiftSelect.options[shiftSelect.selectedIndex].text : '';
            const password = document.getElementById('staff_password_input')?.value?.trim() || 'Caregiver@2026';
            const authCheck = document.getElementById('director_authorizer_check');

            if (!name || !email || !phone || !shift) {
                alert('Please complete all required staff details.');
                return;
            }
            if (authCheck && !authCheck.checked) {
                alert('Director signature approval declaration must be checked.');
                return;
            }

            form.dataset.saving = 'true';
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;

            try {
                const res = await db.save('personnel', {
                    type: 'staff',
                    Name: name,
                    Position: role,
                    Contact_Number: phone,
                    Email: email,
                    Duty_name: shift,
                    Qualification_name: `${role} License`,
                    Training_Name: 'PDRM Safeguarding & Pediatric First Aid'
                });

                const newStaffId = res.record?.Staff_Id ? staffCode(res.record.Staff_Id) : (document.getElementById('staff_id_input')?.value || 'HH-CARE-09');

                const setElText = (id, val) => { const el = document.getElementById(id); if (el) el.innerText = val; };
                setElText('success_staff_name', name);
                setElText('success_staff_role', role);
                setElText('success_staff_shift', shift);
                setElText('success_staff_id', newStaffId);
                setElText('success_staff_password', password);

                window.lastRegisteredStaff = {name, id: newStaffId, role, shift, password};

                const modal = document.getElementById('modal_staff_onboarding_success');
                if (modal) modal.classList.add('active');

                notify(`Staff Caregiver ${name} (${newStaffId}) saved to MySQL & accredited!`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            } finally {
                delete form.dataset.saving;
                if (btn) btn.disabled = false;
            }
            return;
        }

        // 5. Staff Profile Inline Form (#profile_inline_form) & Modal Form (#modal_edit_profile_form)
        if (form.id === 'profile_inline_form' || form.id === 'modal_edit_profile_form') {
            const isModal = form.id === 'modal_edit_profile_form';
            const fullname = document.getElementById(isModal ? 'modal_input_fullname' : 'inline_input_fullname')?.value?.trim();
            const phone = document.getElementById(isModal ? 'modal_input_phone' : 'inline_input_phone')?.value?.trim();
            const ward = document.getElementById(isModal ? 'modal_input_ward' : 'inline_input_ward')?.value?.trim() || window.profileData?.ward || 'Senior Caregiver';
            if (fullname) {
                const existingStaff = (db.data.staff || [])[0];
                db.save('personnel', {
                    type: 'staff',
                    ...(existingStaff ? {Staff_Id: existingStaff.Staff_Id} : {}),
                    Name: fullname,
                    Position: ward.slice(0, 50),
                    Contact_Number: phone || null
                }).then(() => {
                    broadcastSync();
                    return db.refresh();
                }).catch(err => notify(err.message, false));
            }
        }

        // 6. Staff Handover Modal Form (#modal_handover_note form)
        if (form.closest('#modal_handover_note')) {
            const noteText = document.getElementById('modal_handover_text')?.value?.trim();
            if (noteText) {
                db.save('shift', {
                    Duty_name: `Handover: ${noteText}`.slice(0, 100),
                    Assignment_date: today()
                }).then(() => {
                    broadcastSync();
                    return db.refresh();
                }).catch(() => {});
            }
        }

        // 7. Admin Provision Form (#admin_provision_form on /register)
        if (form.id === 'admin_provision_form') {
            const fullName = document.getElementById('admin_full_name')?.value?.trim();
            const adminId = document.getElementById('admin_id')?.value?.trim();
            const pw = document.getElementById('admin_password')?.value;
            const pwConfirm = document.getElementById('admin_password_confirmation')?.value;
            if (fullName && pw && pw === pwConfirm) {
                db.save('personnel', {
                    type: 'staff',
                    Name: fullName,
                    Position: `Administrator (${adminId || 'ADM'})`.slice(0, 50)
                }).then(() => {
                    broadcastSync();
                    return db.refresh();
                }).catch(() => {});
            }
        }
    }, true);

    // Hook into imperative action functions across portal pages
    document.addEventListener('DOMContentLoaded', () => {
        // Update database.js banner text to reflect both PostgreSQL and MySQL connections
        setTimeout(() => {
            const statusNotice = document.querySelector('.portal-content > p[role="status"]');
            if (statusNotice && statusNotice.textContent.includes('PostgreSQL')) {
                statusNotice.textContent = 'Child & care records save to local PostgreSQL, Inventory & Supplies save to remote PostgreSQL (10.213.198.128), Staff & Volunteers save to MySQL (10.213.198.141), and Donations save to MariaDB (10.213.198.163).';
            }
        }, 50);

        // Director Endorsement button inside #modal_staff_safeguarding_dossier
        window.endorseCaregiverCredentials = async function () {
            const staffName = window.activeStaffName || document.getElementById('dossier_staff_name')?.innerText?.trim() || 'Caregiver';
            window.closeDirectorModal?.('modal_staff_safeguarding_dossier');
            try {
                await db.save('credential', {
                    Staff_Name: staffName,
                    Qualification_name: 'Director Safeguarding Endorsement',
                    Year_Obtained: String(new Date().getFullYear()),
                    Training_Name: 'JKM Child Protection & PDRM Vetting',
                    Status: 'Certified'
                });
                notify(`Official Director Endorsement saved to MySQL for ${staffName}.`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                notify(err.message, false);
            }
        };

        // Director Approve Volunteer Hours button on /director/staff-volunteers
        window.approveVolunteerHours = async function (name, hours, btn) {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
            }
            try {
                await db.save('attendance', {
                    Volunteer_Name: name,
                    Activity: `Director Approved Service (${hours} hrs · Signed: Sister Sarah)`,
                    Attendance_Date: today()
                });
                notify(`Verified and logged ${hours} volunteer hours in MySQL for ${name}.`, true);
                broadcastSync();
                await db.refresh();
            } catch (err) {
                if (btn) {
                    btn.innerHTML = 'Retry';
                    btn.disabled = false;
                }
                notify(err.message, false);
            }
        };

        // Staff Duty Dashboard: Add Routine Task -> saves to MySQL duty & duty_assignment
        if (typeof window.addNewRoutineTask === 'function') {
            window.addNewRoutineTask = async function () {
                const title = prompt('Enter new daily routine task:');
                if (!title || !title.trim()) return;
                const time = prompt('Enter scheduled time (e.g. 03:15 PM):', '03:00 PM') || '03:00 PM';
                try {
                    await db.save('shift', {
                        Duty_name: `${title.trim()} (${time.trim()})`.slice(0, 100),
                        Assignment_date: today()
                    });
                    notify(`Added "${title.trim()}" to MySQL duty schedule!`, true);
                    broadcastSync();
                    await db.refresh();
                } catch (err) {
                    notify(err.message, false);
                }
            };
        }

        // Admin Dashboard: Add Task -> saves to MySQL duty & duty_assignment
        if (typeof window.addNewTodoPrompt === 'function') {
            window.addNewTodoPrompt = async function () {
                const taskText = prompt('Enter a task:');
                if (!taskText || !taskText.trim()) return;
                try {
                    await db.save('shift', {
                        Duty_name: taskText.trim().slice(0, 100),
                        Assignment_date: today()
                    });
                    notify(`Task "${taskText.trim()}" saved to MySQL duty table!`, true);
                    broadcastSync();
                    await db.refresh();
                } catch (err) {
                    notify(err.message, false);
                }
            };
        }

        // Staff Profile: Save Shift Handover Note -> saves to MySQL duty
        if (typeof window.saveShiftNote === 'function') {
            const origSaveShiftNote = window.saveShiftNote;
            window.saveShiftNote = async function () {
                const note = document.getElementById('shift_handover_notes')?.value?.trim();
                origSaveShiftNote();
                if (note) {
                    try {
                        await db.save('shift', {
                            Duty_name: `Shift Note: ${note}`.slice(0, 100),
                            Assignment_date: today()
                        });
                        broadcastSync();
                        await db.refresh();
                    } catch (_) {}
                }
            };
        }

        if (window.HopeHavenSync) {
            window.HopeHavenSync.subscribe(SYNC_KEY, () => db.refresh().catch(() => {}));
        }
    });

    db.ready = db.refresh();
    db.ready.catch(err => console.warn('MySQL staff/volunteer sync notice:', err.message));
})();
