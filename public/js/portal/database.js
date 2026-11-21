/* PostgreSQL is authoritative. No child records are seeded or cached in browser storage. */
(() => {
    'use strict';
    const store = window.HopeHavenChildStore;
    let children = [];
    const today = () => new Date().toLocaleDateString('en-CA');
    const value = id => document.getElementById(id)?.value?.trim() || '';
    const api = async (url, data) => {
        const multipart = typeof FormData !== 'undefined' && data instanceof FormData;
        const response = await fetch(url, {
            method: data ? 'POST' : 'GET', credentials: 'same-origin', cache: 'no-store',
            headers: {'Accept': 'application/json', ...(!multipart ? {'Content-Type': 'application/json'} : {}), 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
            ...(data ? {body: multipart ? data : JSON.stringify(data)} : {})
        });
        const result = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(response.status === 422 ? Object.values(result.errors || {}).flat().join(' ') : response.status === 403 ? 'Database access is available on this computer only.' : 'Could not save or load database records. Please try again.');
        return result;
    };
    const db = window.HopeHavenDatabase = {
        data: {},
        save: (resource, data) => api('/care-data/' + resource, data),
        async refresh() {
            const data = await api('/care-data');
            db.data = data;
            children = data.child.map(row => {
                const id = String(row.Child_Id);
                const medical = data.medical.filter(item => String(item.Child_Id) === id).reverse();
                const appointment = data.appointment.filter(item => String(item.Child_Id) === id && item.Appointment_Date >= today()).sort((a,b) => a.Appointment_Date.localeCompare(b.Appointment_Date))[0];
                const education = data.education.filter(item => String(item.Child_Id) === id).reverse();
                const papers = education.map(item => ({
                    id: item.Education_Id, examName: 'Academic results', term: item.Academic_Year, date: '',
                    overallScore: item.Child_Result === null ? undefined : Number(item.Child_Result), status: 'Recorded',
                    subjects: data['subject-result'].filter(result => result.Education_Id === item.Education_Id).map(result => ({name: data.subject.find(subject => subject.Subject_Id === result.Subject_Id)?.Subject_Name || '', score: result.Subject_Score === null ? undefined : Number(result.Subject_Score), grade: result.Subject_Grade || ''}))
                }));
                return {
                    id, name: row.Child_Name, gender: row.Child_Gender, dob: row.Child_Date_Of_Birth,
                    age: store.calculateAge(row.Child_Date_Of_Birth), preferredName: row.Child_Name.split(' ')[0],
                    dorm: data.orphanage.find(home => home.Orphanage_Id === row.Orphanage_Id)?.Orphanage_Name || '',
                    orphanageId: row.Orphanage_Id, room: '', school: 'Not recorded', grade: 'Not recorded', status: 'Registered', admissionDate: '', avatar: row.Child_Photo_Path ? '/care-data/child/' + id + '/photo?v=' + encodeURIComponent(row.Child_Photo_Path.split('/').pop()) : '/favicon.svg',
                    basicMedical: {bloodType:'Not recorded', allergies:'Not recorded', immunization:'Not recorded', clinicDoctor:'Not recorded', condition: medical[0]?.Medical_Diagnosis || 'Not recorded', medications: medical[0]?.Medical_Treatment || '', medicalNotes: medical[0]?.Medical_Treatment || '', nextClinicDate: appointment?.Appointment_Date || '', nextClinicSpecialty: appointment?.Appointment_Purpose || ''},
                    academic: {gpa:'Not recorded', academicStanding:'Not recorded', attendance: education[0]?.Child_Attendance ?? undefined, overallScore: papers[0]?.overallScore, paperResults: papers},
                    timeline: [...medical.map(item => ({date:item.Medical_Record_Date,title:item.Medical_Diagnosis,detail:item.Medical_Treatment || '',category:'medical'})), ...data.followup.filter(item => String(item.Child_Id) === id).map(item => ({date:item.Followup_Date,title:'Follow-up',detail:item.Followup_Remarks || '',category:'followup'}))]
                };
            });
            populateOrphanages();
            window.ChildWelfareUI?.renderAll();
            window.renderStaffWardRegistry?.();
            document.dispatchEvent(new Event('care-data-loaded'));
            return data;
        }
    };
    store.getAll = () => children;
    store.getById = id => children.find(child => child.id === String(id)) || null;
    // Old synchronous callers must never report a browser-only write as database success.
    ['save','add','update','recordPaperResult','updateMedicalCondition','deleteChild'].forEach(method => {
        store[method] = () => { throw new Error('Use the connected care record form to save this change.'); };
    });
    function populateOrphanages() {
        ['reg_child_dorm','staff_add_dorm'].forEach(id => {
            const select = document.getElementById(id); if (!select) return;
            const selected = select.value; select.replaceChildren(new Option('Choose an orphanage', ''));
            db.data.orphanage?.forEach(row => select.add(new Option(row.Orphanage_Name, String(row.Orphanage_Id))));
            select.value = selected; select.required = true;
            const label = select.closest('.form-group,.staff-field-group')?.querySelector('label'); if (label) label.textContent = 'Orphanage *';
        });
    }
    function notify(message, success = false) {
        if (window.showPortalToast) window.showPortalToast(message, success);
        else alert(message);
    }
    const handlers = {
        modal_register_child: form => admission(form, 'reg_child_'),
        modal_staff_add_child: form => admission(form, 'staff_add_'),
        modal_key_paper_result: form => paper(form, false),
        modal_staff_key_paper: form => paper(form, true),
        modal_record_medical: form => medical(form, false),
        modal_staff_record_med: form => medical(form, true),
        modal_health_appointment: form => db.save('appointment', {Child_Id:value('health_appointment_child_select'), Appointment_Date:form.querySelector('input[type="date"]').value, Appointment_Purpose:value('health_appointment_specialty')})
    };
    async function admission(form, prefix) {
        if (!window.HopeHavenChildPhoto.ready(form)) throw new Error('Please finish selecting a valid child photo.');
        const data = new FormData();
        Object.entries({Child_Name:value(prefix+'name'), Child_Gender:value(prefix+'gender'), Child_Date_Of_Birth:value(prefix+'dob'), Orphanage_Id:value(prefix+'dorm'), Medical_Diagnosis:value(prefix+'condition')}).forEach(([key,item])=>data.append(key,item));
        const photo = window.HopeHavenChildPhoto.getFile(form);
        if (photo) data.append('photo', photo);
        await db.save('intake', data);
    }
    async function paper(form, staff) {
        const subjects = [...form.querySelectorAll('.subject-score-row')].map(row => {
            const name = row.querySelector(staff ? '.staff-subj-name' : '.subject-name-input').value.trim();
            const raw = row.querySelector(staff ? '.staff-subj-score' : '.subject-score-input').value;
            if (!raw || !Number.isFinite(Number(raw))) throw new Error('Enter a score for every subject.');
            const score = Number(raw);
            return {name, score, grade:window.ChildWelfareUI.scoreToGrade(score)};
        });
        const year = value(staff ? 'staff_paper_term' : 'paper_term');
        const attendance = value(staff ? 'staff_paper_attendance' : 'paper_attendance_input');
        if (attendance !== '' && (!Number.isInteger(Number(attendance)) || Number(attendance) < 0 || Number(attendance) > 366)) {
            throw new Error('School attendance must be a whole number from 0 to 366 days.');
        }
        await db.save('paper', {Child_Id:value(staff ? 'staff_paper_child_select' : 'paper_child_select'), Academic_Year:year, subjects,
            ...(attendance !== '' ? {Child_Attendance:Number(attendance)} : {})});
    }
    async function medical(form, staff) {
        await db.save('medical', {
            Child_Id:value(staff ? 'staff_med_child_select' : 'med_child_select'),
            Medical_Record_Date:today(), Medical_Diagnosis:value(staff ? 'staff_med_condition' : 'med_condition_input'),
            Medical_Treatment:value(staff ? 'staff_med_prescriptions' : 'med_prescriptions_input') || null
        });
    }
    document.addEventListener('submit', async event => {
        const form = event.target;
        const modal = form.closest('[id^="modal_"]');
        const handler = handlers[modal?.id]; if (!handler) return;
        event.preventDefault(); event.stopImmediatePropagation();
        if (form.dataset.saving) return;
        form.dataset.saving = 'true';
        const button = form.querySelector('[type="submit"]'); if (button) button.disabled = true;
        try {
            await db.ready;
            await handler(form);
            // A successful write is reported even if a subsequent refresh fails.
            form.reset(); modal.classList.remove('active');
            window.closeModal?.(modal.id); window.closeStaffModal?.(modal.id);
            notify('Record saved to PostgreSQL.', true);
            await db.refresh();
        } catch (error) { notify(error.message); }
        finally { delete form.dataset.saving; if (button) button.disabled = false; }
    }, true);
    document.addEventListener('change', async event => {
        const input = event.target;
        if (!input.matches('.saved-child-photo-input') || !input.files[0]) return;
        const file = input.files[0];
        if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            notify('Choose a JPG, PNG or WebP image up to 5 MB.'); input.value=''; return;
        }
        input.disabled = true;
        try {
            const data = new FormData(); data.append('photo',file);
            await api('/care-data/child/' + encodeURIComponent(input.dataset.childId) + '/photo',data);
            await db.refresh();
            notify('Child photo saved.',true);
        } catch (error) { notify(error.message); }
        finally { input.disabled=false; input.value=''; }
    });
    document.addEventListener('DOMContentLoaded', () => {
        const notice = document.createElement('p');
        notice.setAttribute('role','status'); notice.style.cssText='padding:12px;background:#eef2e5;border-radius:8px;margin-bottom:16px';
        notice.textContent = 'Child and care records save to PostgreSQL through the existing forms. Volunteer, staff, donation, inventory and welfare-request tools remain browser-only demos. Fields marked unavailable have no matching column.';
        document.querySelector('.portal-content')?.prepend(notice);
        Object.keys(handlers).forEach(id => {
            const modal = document.getElementById(id); if (!modal) return;
            const info = document.createElement('p'); info.textContent='Only enabled fields are saved. Additional details require matching database columns.'; info.style.cssText='padding:12px;color:#52613e';
            modal.querySelector('form')?.prepend(info);
        });
        const unavailable = ['reg_child_callname','reg_child_school','reg_child_grade','reg_child_blood','reg_child_allergies','reg_child_notes','staff_add_callname','staff_add_school','staff_add_grade','staff_add_blood','staff_add_allergies','staff_add_notes','paper_exam_name','paper_exam_date','paper_remarks_input','staff_paper_exam_name','staff_paper_date','staff_paper_remarks','med_blood_type','med_allergies_input','med_immunization_select','med_next_clinic_date','med_doctor_input','med_notes_input','staff_med_blood','staff_med_allergies','staff_med_immunization','staff_med_clinic_date','staff_med_doctor','staff_med_notes','update_grade_standing','director_edu_remark'];
        unavailable.forEach(id => {
            const field = document.getElementById(id); if (!field) return;
            field.disabled = true; field.required = false; field.title = 'Unavailable: no matching column in the existing database.';
            const label = field.closest('.form-group,.staff-field-group,div')?.querySelector('label'); if (label) label.textContent += ' (unavailable)';
        });
        ['paper_term','staff_paper_term'].forEach(id => {
            const field=document.getElementById(id); if (!field) return;
            field.value=String(new Date().getFullYear());field.maxLength=9;
            const label=field.closest('.form-group,.staff-field-group')?.querySelector('label');if(label)label.textContent='Academic year *';
        });
        const noteButton=document.querySelector('#modal_add_clinical_note .btn-dossier');
        if(noteButton)noteButton.innerHTML='<i class="fas fa-save"></i> Save follow-up note';
        // Deletion has no archive column and must not be simulated in browser storage.
        if (window.ChildWelfareUI) window.ChildWelfareUI.confirmDelete = () => notify('Archiving is unavailable because the existing Child table has no archive/status column.');
    });
    db.ready = db.refresh();
    db.ready.catch(error => {
        const show = () => notify(error.message);
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', show, {once:true}); else show();
    });
})();
