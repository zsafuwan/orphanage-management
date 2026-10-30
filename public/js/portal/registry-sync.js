/* One browser-local child registry powers staff, admin and director views. */
(function () {
    'use strict';
    const key = 'hopehaven_children_registry_v2';
    const escape = window.HopeHavenSync.escape;
    const text = (selector, value) => document.querySelectorAll(selector).forEach(node => { node.textContent = value; });
    const today = () => { const date = new Date(); return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`; };
    const hasValue = value => !!value && !/^(none|no |not recorded|n\/a)/i.test(value);
    const initials = name => String(name || 'Child').trim().split(/\s+/).slice(0,2).map(word => word[0]).join('').toUpperCase();
    const cell = value => `<td>${escape(value ?? 'Not recorded')}</td>`;
    const profile = child => `<td><div class="child-profile-cell"><div class="child-avatar-box child-avatar-boy">${escape(initials(child.name))}</div><div><div class="child-name">${escape(child.name)}</div><div class="child-meta">${escape(child.id)}</div></div></div></td>`;
    const action = (child, kind, label) => `<td><button type="button" class="btn-dossier" data-registry-action="${kind}" data-child-id="${escape(child.id)}">${label}</button></td>`;
    const latest = child => child.academic?.paperResults?.[0];
    const score = child => child.academic?.overallScore ?? latest(child)?.overallScore;
    const standing = child => score(child) === undefined ? 'unrecorded' : score(child) >= 90 ? 'distinction' : score(child) >= 70 ? 'credit' : 'remedial';
    const level = child => { const grade = Number(String(child.grade).match(/\d+/)?.[0]); return grade <= 6 ? 'primary' : grade <= 9 ? 'lower_secondary' : grade ? 'upper_secondary' : 'other'; };
    const dateLabel = date => date ? new Date(date+'T12:00:00').toLocaleDateString('en-MY',{day:'numeric',month:'short',year:'numeric'}) : 'Not scheduled';

    function renderMedical(children) {
        const table = document.querySelector('.med-table tbody');
        if (!table) return;
        const tabs = document.querySelector('.med-filter-tabs');
        const selected = tabs.querySelector('.active')?.dataset.ward || 'all';
        const wards = ['all', ...new Set(children.map(child => child.dorm))];
        tabs.innerHTML = wards.map(ward => `<button type="button" class="med-tab-btn ${ward === selected ? 'active' : ''}" data-ward="${escape(ward)}">${escape(ward === 'all' ? 'All Wards' : ward)} <span class="med-tab-badge">${children.filter(child => ward === 'all' || child.dorm === ward).length}</span></button>`).join('');
        if (!tabs.querySelector('.active')) tabs.querySelector('button')?.classList.add('active');
        table.innerHTML = children.map(child => {
            const med = child.basicMedical || {};
            const healthStatus=hasValue(med.allergies)?'allergy':hasValue(med.medications)?'meds':/^healthy|^no chronic/i.test(med.condition||'')?'clear':'condition';
            return `<tr data-ward="${escape(child.dorm)}" data-has-meds="${hasValue(med.medications)}" data-has-allergy="${hasValue(med.allergies)}" data-health-status="${healthStatus}">${profile(child)}${cell(`${child.dorm} · ${child.age} years`)}${cell(med.bloodType)}${cell(med.allergies || 'None recorded')}${cell(med.condition)}${cell(med.vitals || 'Not recorded')}${cell(med.clinicDoctor)}${action(child,'medical','View record')}</tr>`;
        }).join('') || '<tr><td colspan="8">No children registered yet.</td></tr>';
        text('.med-header-title p', `Health records, care notes and appointments for ${children.length} registered children. Updates sync automatically with the caregiver registry.`);
        const values = document.querySelectorAll('.med-kpi-card .med-kpi-val');
        const immunized = children.filter(child => child.basicMedical?.immunization === 'Up to Date').length;
        [children.length ? Math.round(immunized/children.length*100)+'%' : '—', children.filter(child=>hasValue(child.basicMedical?.medications)).length, children.filter(child=>hasValue(child.basicMedical?.allergies)).length, children.filter(child=>child.basicMedical?.nextClinicDate >= today()).length].forEach((value,index) => { if(values[index]) values[index].textContent=value; });
        text('.med-kpi-card:last-child .med-kpi-lbl','Upcoming clinic visits');
        window.filterMedicalTable?.(null,null);
    }

    function renderEducation(children) {
        const table = document.querySelector('.edu-table tbody');
        if (!table) return;
        table.innerHTML = children.map(child => {
            const academic = child.academic || {};
            const paper = latest(child);
            return `<tr data-level="${level(child)}" data-standing="${standing(child)}">${profile(child)}${cell(`${child.school} · ${child.grade}`)}${cell(academic.gpa ?? 'Not recorded')}${cell(paper?.subjects?.map(subject=>`${subject.name}: ${subject.score}%`).join(' · ') || 'No paper recorded')}${cell(academic.attendance === undefined ? 'Not recorded' : academic.attendance+'%')}${cell(academic.cocurriculum)}${cell(academic.sponsor)}${action(child,'education','View record')}</tr>`;
        }).join('') || '<tr><td colspan="8">No children registered yet.</td></tr>';
        document.querySelectorAll('.edu-tab-btn').forEach(tab => { const badge=tab.querySelector('.edu-tab-badge'); if(badge) badge.textContent=children.filter(child=>tab.dataset.level==='all'||level(child)===tab.dataset.level).length; });
        const graded = children.filter(child=>score(child)!==undefined);
        const values=document.querySelectorAll('.edu-kpi-val');
        [children.length, graded.length ? Math.round(graded.filter(child=>score(child)>=50).length/graded.length*100)+'%' : '—', children.filter(child=>standing(child)==='distinction').length, children.filter(child=>standing(child)==='remedial').length].forEach((value,index)=>{if(values[index])values[index].textContent=value;});
        text('.edu-header-title p','Academic records from the shared child registry. New children and paper results appear here automatically.');
        window.filterEducationTable?.(null,null);
    }

    function renderClinicOverview(children) {
        const table=document.querySelector('.sickness-table tbody');
        if(!table)return;
        let scheduled=0,attended=0,sickness=0;
        table.innerHTML=children.map(child=>{
            const med=child.basicMedical||{};
            const sick=!!med.condition&&!/^healthy|^no chronic/i.test(med.condition);
            const status=med.appointmentStatus==='attended'?'attended':med.nextClinicDate>=today()?'scheduled':med.nextClinicDate?'review':'routine';
            if(sick)sickness++;
            if(status==='scheduled')scheduled++;
            if(status==='attended')attended++;
            return `<tr data-record-type="${status}" data-has-sickness="${sick}">${profile(child)}<td class="clinic-provider"><strong class="clinic-provider-doctor">${escape(med.clinicDoctor||'Not assigned')}</strong><span class="clinic-provider-location">${escape(med.clinicLocation||'Clinic not recorded')}</span></td>${cell(dateLabel(med.nextClinicDate))}${cell(status==='attended'?'Attended':status==='scheduled'?'Upcoming appointment':status==='review'?'Review appointment status':'Routine care')}${action(child,'clinic','View record')}</tr>`;
        }).join('')||'<tr><td colspan="5">No children registered yet.</td></tr>';
        const counts={all:children.length,sickness,scheduled,attended};
        const labels={all:'All Records',sickness:'Active Conditions',scheduled:'Upcoming',attended:'Attended'};
        document.querySelectorAll('[data-sickness-filter]').forEach(button=>{button.textContent=`${labels[button.dataset.sicknessFilter]} (${counts[button.dataset.sicknessFilter]})`;});
        text('.sickness-table-caption','Health & Appointments');
        const metrics=document.querySelectorAll('.director-kpi-card');
        if(metrics[0]) { metrics[0].querySelector('.kpi-value').textContent=sickness+' Children'; metrics[0].querySelector('.kpi-title').textContent='Health Conditions'; metrics[0].querySelector('.kpi-footer-note').textContent='Shared caregiver health registry'; }
        if(metrics[1]) { metrics[1].querySelector('.kpi-value').textContent=scheduled+' Upcoming'; metrics[1].querySelector('.kpi-title').textContent='Upcoming Visits'; metrics[1].querySelector('.kpi-badge').textContent=children.filter(child=>child.basicMedical?.nextClinicDate).length+' Recorded visits'; metrics[1].querySelector('.kpi-footer-note').textContent=attended+' marked attended'; }
        const filter=document.querySelector('.sickness-tab-btn.active')?.dataset.sicknessFilter||'all';
        window.filterSicknessRows?.(filter);
    }

    function refresh() {
        const children=window.HopeHavenChildStore.getAll();
        document.querySelectorAll('[data-child-count]').forEach(node=>{node.textContent=children.length;});
        document.querySelectorAll('.kpi-card').forEach(card=>{const label=card.querySelector('.kpi-label')?.textContent.trim(); if(label==='Children in Care') card.querySelector('.kpi-value').textContent=children.length+' Children';});
        const schooling=children.filter(child=>Number(child.age)>=5).length;
        document.querySelectorAll('[data-schooling-summary]').forEach(node=>{node.textContent=`${schooling} in school-age care · ${children.length-schooling} in early childhood care`;});
        renderMedical(children);
        renderEducation(children);
        renderClinicOverview(children);
        if (!document.querySelector('#modal_add_clinical_note.active,#modal_update_student_grades.active')) {
            const kind=document.querySelector('#modal_child_medical_dossier.active')?'medical':document.querySelector('#modal_student_academic_portfolio.active')?'education':null;
            if(kind) [...document.querySelectorAll('[data-registry-action]')].find(button=>button.dataset.registryAction===kind&&button.dataset.childId===window.HopeHavenSelectedChildId)?.click();
        }
    }

    function refreshWelfareOverview() {
        const cards=[...document.querySelectorAll('.kpi-card')].filter(card=>card.querySelector('.kpi-label')?.textContent.trim()==='Welfare Requests');
        if(!cards.length)return;
        const pending=window.HopeHavenWelfareStore.getAll().filter(request=>request.status==='Pending Admin Approval').length;
        cards.forEach(card=>{
            card.querySelector('.kpi-value').textContent=pending+(pending===1?' Ticket':' Tickets');
            const badge=card.querySelector('.kpi-pill');if(badge)badge.textContent=pending+' Review';
            const detail=card.querySelector('.kpi-compare');if(detail)detail.textContent='Awaiting review in the shared welfare queue';
        });
    }

    document.addEventListener('click',event=>{
        const wardTab=event.target.closest('.med-tab-btn');
        if(wardTab) {document.querySelectorAll('.med-tab-btn').forEach(tab=>tab.classList.toggle('active',tab===wardTab)); window.filterMedicalTable?.(wardTab.dataset.ward,null);}
        const button=event.target.closest('[data-registry-action]');
        if(!button)return;
        const child=window.HopeHavenChildStore.getById(button.dataset.childId);
        if(!child)return;
        window.HopeHavenSelectedChildId=child.id;
        const med=child.basicMedical||{}, academic=child.academic||{};
        if(button.dataset.registryAction==='clinic') { location.href='/director/medical-records?child='+encodeURIComponent(child.id); return; }
        if(button.dataset.registryAction==='medical') {
            window.openChildMedicalDossier(child.name,`${child.dorm} · ${child.age} years`,child.id,med.bloodType||'Not recorded',hasValue(med.allergies)?med.allergies:'None',med.vitals||'Not recorded',med.bmi||'Not recorded',escape(med.medications||'None recorded'),med.clinicDoctor||'Not assigned');
            text('#dossier_child_mykid','Registry ID: '+child.id);
            text('#dossier_immunization',med.immunization||'Not recorded');
            const timeline=document.querySelector('.dossier-timeline');
            if(timeline)timeline.innerHTML=(child.timeline||[]).filter(item=>item.category==='medical').map(item=>`<div class="timeline-item"><div class="timeline-dot"></div><div class="timeline-title">${escape(item.title)}</div><div class="timeline-desc">${escape(item.date)} · ${escape(item.detail)}</div></div>`).join('')||'<p>No clinical milestones recorded.</p>';
        } else {
            const subjects=latest(child)?.subjects||[];
            const scores=subjects.map(subject=>`<tr>${cell(subject.name)}${cell(subject.score+'%')}${cell(subject.grade||'—')}${cell(subject.remarks||'Not recorded')}</tr>`).join('')||'<tr><td colspan="4">No subject results recorded.</td></tr>';
            window.openStudentAcademicPortfolio(child.name,`${child.school} · ${child.grade}`,academic.sponsor||'Not recorded',score(child)===undefined?'No paper recorded':score(child)+'%',academic.attendance===undefined?'Not recorded':academic.attendance+'%',academic.academicStanding||'Not recorded',academic.cocurriculum||'Not recorded',scores,academic.tutoring||'Not recorded');
        }
    });
    document.addEventListener('DOMContentLoaded',()=>{
        refresh();
        refreshWelfareOverview();
        window.HopeHavenSync.subscribe(key,()=>window.HopeHavenSync.refreshView(refresh));
        window.HopeHavenSync.subscribe('hopehaven_welfare_requests_v2',refreshWelfareOverview);
        if(document.querySelector('.med-table')) {
            const selected=new URLSearchParams(location.search).get('child');
            if(selected) [...document.querySelectorAll('[data-registry-action="medical"]')].find(button=>button.dataset.childId===selected)?.click();
        }
    });
})();
