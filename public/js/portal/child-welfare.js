// HopeHaven Orphanage Sanctuary - Child Care & Education Management
// Dynamic Child Registry, Paper Result Entry, Medical Condition Tracking, and PDF Logic

(function() {
    'use strict';

    const STORAGE_KEY = 'hopehaven_children_registry_v2';

    // Initial Seed Data with Complete Medical and Academic Records
    const DEFAULT_CHILDREN = [
        {
            id: 'CH-2024-001',
            name: 'Maya Angelika Cruz',
            preferredName: 'Maya',
            gender: 'Female',
            dob: '2015-05-14',
            age: 11,
            dorm: 'St. Clare Cottage',
            room: 'Room 2B',
            school: 'St. Jude Elementary',
            schoolId: 'STJ-5044',
            grade: 'Grade 5',
            status: 'Active Ward',
            avatar: 'https://images.unsplash.com/photo-1544717305-2782549b5136?w=150&auto=format&fit=crop&q=80',
            caseWorker: 'Sister Teresa',
            admissionDate: '2022-03-15',
            basicMedical: {
                bloodType: 'O+',
                condition: 'Mild Pediatric Asthma & Seasonal Allergies',
                allergies: 'Penicillin, Peanuts (Mild)',
                medications: 'Multivitamin syrup once daily',
                immunization: 'Up to Date',
                nextClinicDate: '2026-10-25',
                nextClinicSpecialty: 'Pediatric Dental Screening & Extraction',
                clinicDoctor: 'Dr. Angela Reyes',
                clinicLocation: 'Klinik Kesihatan Titiwangsa',
                medicalNotes: 'Maya is thriving emotionally. Shows high artistic aptitude. Has mild sensitivity to peanuts; inhaler on standby.'
            },
            academic: {
                attendance: 96,
                gpa: 3.85,
                academicStanding: 'Distinction',
                paperResults: [
                    {
                        examName: 'Mid-Year Final Paper Examination',
                        term: 'Term 1 · 2026',
                        date: '2026-06-20',
                        subjects: [
                            { name: 'Mathematics', score: 92, grade: 'A' },
                            { name: 'Science', score: 94, grade: 'A' },
                            { name: 'English Language', score: 96, grade: 'A+' },
                            { name: 'Visual Arts', score: 98, grade: 'A+' }
                        ],
                        overallScore: 95,
                        gpa: 3.85,
                        status: 'Distinction',
                        remarks: 'Outstanding performance across STEM and creative arts papers. Top 3 in regional science exhibition.'
                    }
                ]
            },
            timeline: [
                {
                    date: '2022-03-15',
                    title: 'Orphanage Admission & Guardian Intake',
                    category: 'intake',
                    detail: 'Official admission into St. Clare Cottage under guardianship court order #SW-2022/88.'
                },
                {
                    date: '2023-01-10',
                    title: 'St. Jude Elementary Enrollment - Grade 2',
                    category: 'academic',
                    detail: 'Enrolled under HopeHaven Education Scholarship fund. Assigned mentor Sister Teresa.'
                },
                {
                    date: '2025-05-18',
                    title: 'Optometry Screening & Eyeglass Prescription',
                    category: 'medical',
                    detail: 'Completed routine pediatric vision review. Prescribed reading lenses (-1.25 D).'
                },
                {
                    date: '2026-06-20',
                    title: 'Midterm Distinction Honors (GPA 3.85)',
                    category: 'academic',
                    detail: 'Awarded Grade 5 Distinction standing. Scored 95% overall average across all papers.'
                }
            ]
        },
        {
            id: 'CH-2023-019',
            name: 'Samuel Christian O’Connor',
            preferredName: 'Sammy',
            gender: 'Male',
            dob: '2017-08-22',
            age: 9,
            dorm: 'St. Francis Boys Hall',
            room: 'Room 1A',
            school: 'Hope Junior Academy',
            schoolId: 'HJA-3012',
            grade: 'Grade 3',
            status: 'Active Ward',
            avatar: 'https://images.unsplash.com/photo-1543610892-0b1f7e6d8ac1?w=150&auto=format&fit=crop&q=80',
            caseWorker: 'Brother Luke',
            admissionDate: '2023-02-10',
            basicMedical: {
                bloodType: 'A+',
                condition: 'Exercise-Induced Bronchial Asthma',
                allergies: 'Dust mites, Shellfish',
                medications: 'Salbutamol 100mcg inhaler before physical sports',
                immunization: 'Up to Date',
                nextClinicDate: '2026-11-14',
                nextClinicSpecialty: 'Asthma Pulmonary Function Review',
                clinicDoctor: 'Dr. Angela Reyes',
                clinicLocation: 'Klinik Kesihatan Titiwangsa',
                medicalNotes: 'Energetic and passionate about soccer. Dorm staff briefed to monitor breathing during cold weather sports.'
            },
            academic: {
                attendance: 91,
                gpa: 3.40,
                academicStanding: 'Good',
                paperResults: [
                    {
                        examName: 'First Term Assessment Paper',
                        term: 'Term 1 · 2026',
                        date: '2026-05-12',
                        subjects: [
                            { name: 'Mathematics', score: 86, grade: 'B+' },
                            { name: 'Reading & Phonics', score: 88, grade: 'B+' },
                            { name: 'Physical Education', score: 99, grade: 'A+' }
                        ],
                        overallScore: 91,
                        gpa: 3.40,
                        status: 'Good',
                        remarks: 'Solid grasp of fundamental arithmetic. Reading fluency improving week-on-week.'
                    }
                ]
            },
            timeline: [
                {
                    date: '2023-02-10',
                    title: 'Orphanage Residential Admission',
                    category: 'intake',
                    detail: 'Placed in St. Francis Boys Hall. General pediatric health baselines recorded.'
                },
                {
                    date: '2024-03-05',
                    title: 'Clinical Asthma Care Protocol Established',
                    category: 'medical',
                    detail: 'Assessed at general hospital. Inhaler spacer kit issued and kept in dispensary.'
                },
                {
                    date: '2026-05-12',
                    title: 'Term 1 Assessment Completed (GPA 3.40)',
                    category: 'academic',
                    detail: 'Passed all Grade 3 papers with strong PE performance and consistent reading progress.'
                }
            ]
        },
        {
            id: 'CH-2022-008',
            name: 'Elijah Liam Ramos',
            preferredName: 'Eli',
            gender: 'Male',
            dob: '2011-03-05',
            age: 15,
            dorm: 'St. Francis Boys Hall',
            room: 'Room 3C',
            school: 'Hope Memorial High School',
            schoolId: 'HMH-9082',
            grade: 'Grade 9',
            status: 'Active Ward',
            avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80',
            caseWorker: 'Sister Teresa',
            admissionDate: '2022-01-14',
            basicMedical: {
                bloodType: 'B+',
                condition: 'Mild Myopia (Corrective Glasses)',
                allergies: 'None known',
                medications: 'None currently prescribed',
                immunization: 'Up to Date',
                nextClinicDate: '2026-10-30',
                nextClinicSpecialty: 'Adolescent Wellness & Optometry Review',
                clinicDoctor: 'Dr. Kelvin Tan',
                clinicLocation: 'Orphanage Clinic',
                medicalNotes: 'Fit and active. Requires reminder to keep corrective lenses on during computer classes.'
            },
            academic: {
                attendance: 88,
                gpa: 3.10,
                academicStanding: 'Needs Math Support',
                paperResults: [
                    {
                        examName: 'Mid-Year Comprehensive Paper',
                        term: 'Term 1 · 2026',
                        date: '2026-06-18',
                        subjects: [
                            { name: 'Algebra I', score: 76, grade: 'C+' },
                            { name: 'General Biology', score: 83, grade: 'B' },
                            { name: 'Computer Studies', score: 93, grade: 'A' }
                        ],
                        overallScore: 84,
                        gpa: 3.10,
                        status: 'Needs Math Support',
                        remarks: 'Excels in computer lab work. Assigned 2x weekly math tutoring with volunteer David.'
                    }
                ]
            },
            timeline: [
                {
                    date: '2022-01-14',
                    title: 'Adolescent Care Intake & Dorm Placement',
                    category: 'intake',
                    detail: 'Transferred to HopeHaven. Assigned to St. Francis Senior Hall.'
                },
                {
                    date: '2024-08-20',
                    title: 'Secondary School Placement & Vocational Orientation',
                    category: 'academic',
                    detail: 'Commenced secondary education at Hope Memorial High School with focus on IT.'
                },
                {
                    date: '2026-06-18',
                    title: 'Mid-Year Paper Results Logged',
                    category: 'academic',
                    detail: 'Algebra score logged at 76%. Academic support plan initiated.'
                }
            ]
        },
        {
            id: 'CH-2025-032',
            name: 'Hannah Grace Morales',
            preferredName: 'Hannah',
            gender: 'Female',
            dob: '2019-11-19',
            age: 7,
            dorm: 'St. Clare Cottage',
            room: 'Room 1B',
            school: 'Hope Junior Academy',
            schoolId: 'HJA-1005',
            grade: 'Grade 1',
            status: 'Active Ward',
            avatar: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80',
            caseWorker: 'Sister Beatrice',
            admissionDate: '2025-04-10',
            basicMedical: {
                bloodType: 'O+',
                condition: 'Healthy / Mild Articulation Delay (Speech Therapy)',
                allergies: 'Lactose Intolerance (Soy/Oat Milk Provided)',
                medications: 'Pediatric chewable calcium supplement',
                immunization: 'Up to Date',
                nextClinicDate: '2026-11-05',
                nextClinicSpecialty: 'Speech & Articulation Review',
                clinicDoctor: 'Dr. Siti Nurhaliza',
                clinicLocation: 'Klinik Kanak-Kanak Seri Petaling',
                medicalNotes: 'Speech clarity significantly improved. Needs dairy-free alternatives for breakfast.'
            },
            academic: {
                attendance: 98,
                gpa: 3.90,
                academicStanding: 'Distinction',
                paperResults: [
                    {
                        examName: 'Grade 1 Foundation Paper',
                        term: 'Term 1 · 2026',
                        date: '2026-06-10',
                        subjects: [
                            { name: 'Literacy & Phonics', score: 95, grade: 'A+' },
                            { name: 'Early Numeracy', score: 94, grade: 'A' },
                            { name: 'Creative Arts', score: 99, grade: 'A+' }
                        ],
                        overallScore: 96,
                        gpa: 3.90,
                        status: 'Distinction',
                        remarks: 'Remarkable memory and phonics progress. Warm and cheerful classroom demeanor.'
                    }
                ]
            },
            timeline: [
                {
                    date: '2025-04-10',
                    title: 'Intake Admission to St. Clare Junior Dorm',
                    category: 'intake',
                    detail: 'Young ward admitted with comprehensive initial pediatric screening completed.'
                },
                {
                    date: '2025-09-12',
                    title: 'Speech Articulation Therapy Commenced',
                    category: 'medical',
                    detail: 'Bi-weekly sessions scheduled with speech therapist Dr. Siti.'
                },
                {
                    date: '2026-06-10',
                    title: 'Grade 1 Foundation Paper Distinction',
                    category: 'academic',
                    detail: 'Achieved 96% overall average score. Awarded Star Reader certificate.'
                }
            ]
        }
    ];

    // Data Store API
    window.HopeHavenChildStore = {
        getAll: function() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed)) {
                        return parsed;
                    }
                }
            } catch (err) {
                console.warn('Error reading from localStorage:', err);
            }
            // Fallback to default seeds and save
            const seeds = JSON.parse(JSON.stringify(DEFAULT_CHILDREN));
            this.save(seeds);
            return seeds;
        },

        save: function(children) {
            try {
                window.HopeHavenSync.write(STORAGE_KEY, JSON.stringify(children));
            } catch (err) {
                console.error('Error saving to localStorage:', err);
                throw err;
            }
        },

        getById: function(id) {
            const list = this.getAll();
            return list.find(c => c.id === id) || null;
        },

        add: function(childData) {
            const list = this.getAll();
            if (childData.dob && (Number.isNaN(new Date(childData.dob).getTime()) || new Date(childData.dob) > new Date())) {
                throw new Error('Date of birth must be a valid date in the past.');
            }
            const id = childData.id || window.HopeHavenSync.nextId(list, 'CH');
            if (list.some(child => child.id === id)) throw new Error('This child ID is already in use.');

            const newChild = {
                id: id,
                name: childData.name || 'New Child Ward',
                preferredName: childData.preferredName || (childData.name ? childData.name.split(' ')[0] : 'Ward'),
                gender: childData.gender || 'Female',
                dob: childData.dob || '2017-01-01',
                age: childData.age || this.calculateAge(childData.dob),
                dorm: childData.dorm || 'St. Clare Cottage',
                room: childData.room || 'Room 1A',
                school: childData.school || 'Hope Junior Academy',
                schoolId: childData.schoolId || `SCH-${Math.floor(1000 + Math.random() * 9000)}`,
                grade: childData.grade || 'Grade 3',
                status: 'Active Ward',
                avatar: childData.avatar || this.getDefaultAvatar(childData.gender),
                caseWorker: childData.caseWorker || 'Sister Grace',
                admissionDate: childData.admissionDate || new Date().toISOString().split('T')[0],
                basicMedical: {
                    bloodType: childData.bloodType || 'O+',
                    condition: childData.condition || 'Healthy / No Chronic Condition',
                    allergies: childData.allergies || 'None recorded',
                    medications: childData.medications || 'None currently prescribed',
                    immunization: childData.immunization || 'Up to Date',
                    nextClinicDate: childData.nextClinicDate || '2026-12-01',
                    nextClinicSpecialty: childData.nextClinicSpecialty || 'General Pediatric Wellness Review',
                    clinicDoctor: childData.clinicDoctor || 'Dr. Angela Reyes',
                    clinicLocation: childData.clinicLocation || 'Orphanage Clinic',
                    medicalNotes: childData.medicalNotes || 'Initial medical baseline recorded upon admission.'
                },
                academic: {
                    attendance: childData.attendance || 95,
                    gpa: childData.gpa || 3.50,
                    academicStanding: childData.academicStanding || 'Good',
                    paperResults: childData.initialPaper ? [childData.initialPaper] : []
                },
                timeline: [
                    {
                        date: new Date().toISOString().split('T')[0],
                        title: 'Orphanage Admission & Ward Placement',
                        category: 'intake',
                        detail: `Registered as residential ward in ${childData.dorm || 'Orphanage Dorm'}.`
                    }
                ]
            };

            list.unshift(newChild);
            this.save(list);
            return newChild;
        },

        update: function(id, updatedFields) {
            const list = this.getAll();
            const index = list.findIndex(c => c.id === id);
            if (index !== -1) {
                list[index] = { ...list[index], ...updatedFields };
                this.save(list);
                return list[index];
            }
            return null;
        },

        recordPaperResult: function(childId, paperData) {
            const list = this.getAll();
            const child = list.find(c => c.id === childId);
            if (!child) return null;

            if (!child.academic) {
                child.academic = { attendance: 95, gpa: 3.50, academicStanding: 'Good', paperResults: [] };
            }
            if (!Array.isArray(child.academic.paperResults)) {
                child.academic.paperResults = [];
            }

            child.academic.paperResults.unshift(paperData);

            // Update latest overall GPA & academic standing
            if (paperData.gpa) {
                child.academic.gpa = parseFloat(paperData.gpa).toFixed(2);
            }
            if (paperData.status) {
                child.academic.academicStanding = paperData.status;
            }

            // Push to timeline
            if (!Array.isArray(child.timeline)) child.timeline = [];
            child.timeline.unshift({
                date: paperData.date || new Date().toISOString().split('T')[0],
                title: `${paperData.examName} (${paperData.overallScore}%)`,
                category: 'academic',
                detail: `Keyed in by staff. Overall score: ${paperData.overallScore}% · Standing: ${paperData.status}. ${paperData.remarks || ''}`
            });

            this.save(list);
            return child;
        },

        updateMedicalCondition: function(childId, medData) {
            const list = this.getAll();
            const child = list.find(c => c.id === childId);
            if (!child) return null;
            child.basicMedical = child.basicMedical || {};

            child.basicMedical = {
                ...child.basicMedical,
                bloodType: medData.bloodType || child.basicMedical.bloodType || 'O+',
                condition: medData.condition || child.basicMedical.condition || 'Healthy',
                allergies: medData.allergies ?? child.basicMedical.allergies ?? 'None',
                medications: medData.medications ?? child.basicMedical.medications ?? 'None',
                immunization: medData.immunization || child.basicMedical.immunization || 'Up to Date',
                nextClinicDate: medData.nextClinicDate ?? child.basicMedical.nextClinicDate,
                nextClinicSpecialty: medData.nextClinicSpecialty || child.basicMedical.nextClinicSpecialty || 'General Checkup',
                clinicDoctor: medData.clinicDoctor || child.basicMedical.clinicDoctor || 'Dr. Angela Reyes',
                clinicLocation: medData.clinicLocation || child.basicMedical.clinicLocation || 'Orphanage Clinic',
                medicalNotes: medData.medicalNotes ?? child.basicMedical.medicalNotes ?? ''
            };

            // Push to timeline
            if (!Array.isArray(child.timeline)) child.timeline = [];
            child.timeline.unshift({
                date: new Date().toISOString().split('T')[0],
                title: `Medical Condition Updated: ${medData.condition || 'Review'}`,
                category: 'medical',
                detail: `Allergies: ${medData.allergies || 'None'}. Medications: ${medData.medications || 'None'}. Review Date: ${medData.nextClinicDate || 'Pending'}.`
            });

            this.save(list);
            return child;
        },

        deleteChild: function(id) {
            let list = this.getAll();
            list = list.filter(c => c.id !== id);
            this.save(list);
        },

        calculateAge: function(dobString) {
            if (!dobString) return 9;
            const birthDate = new Date(dobString);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return isNaN(age) || age < 0 ? null : age;
        },

        getDefaultAvatar: function(gender) {
            if (gender === 'Male') {
                return 'https://images.unsplash.com/photo-1543610892-0b1f7e6d8ac1?w=150&auto=format&fit=crop&q=80';
            }
            return 'https://images.unsplash.com/photo-1544717305-2782549b5136?w=150&auto=format&fit=crop&q=80';
        }
    };

    // UI Rendering Logic for Child Welfare Page
    window.ChildWelfareUI = {
        currentViewMode: 'cards', // 'cards' or 'table'
        activeHistoryChildId: null,

        isStaffEditor: function() {
            return document.body?.dataset.portalRole === 'staff';
        },

        init: function() {
            this.renderAll();
            this.bindEvents();
        },

        renderAll: function() {
            const children = window.HopeHavenChildStore.getAll();
            this.renderKPIs(children);
            this.renderCardsGrid(children);
            this.renderChildrenTable(children);
            this.renderAcademicsTab(children);
            this.renderHealthcareTab(children);
            this.renderHistoryTab(children);
            this.populateChildSelectDropdowns(children);
        },

        renderKPIs: function(children) {
            // 1. Enrolled Wards Count
            $('#metric_enrolled_count').text(`${children.length} Children`);
            $('#tab_profiles_counter').text(`(${children.length})`);

            // 2. Average School Attendance
            let totalAtt = 0;
            children.forEach(c => {
                totalAtt += Number(c.academic?.attendance ?? 0);
            });
            const avgAtt = children.length ? (totalAtt / children.length).toFixed(1) : '0.0';
            $('#metric_school_attendance').text(`${avgAtt}%`);

            // 3. Health Screenings Due (within the next 45 calendar days)
            let upcomingCount = 0;
            const start = new Date(); start.setHours(0,0,0,0);
            const end = new Date(start); end.setDate(end.getDate()+45);
            children.forEach(c => {
                if (c.basicMedical && c.basicMedical.nextClinicDate) {
                    const date = new Date(c.basicMedical.nextClinicDate+'T12:00:00');
                    if (date >= start && date <= end) upcomingCount++;
                }
            });
            $('#metric_health_screenings').text(`${upcomingCount} Visits`);

            // 4. Honor Roll Scholars (GPA >= 3.75)
            let scholars = 0;
            children.forEach(c => {
                if (c.academic && Number(c.academic.gpa) >= 3.75) {
                    scholars++;
                }
            });
            $('#metric_honor_roll').text(`${scholars} Scholars`);
        },

        renderCardsGrid: function(children) {
            const $grid = $('#profiles_grid');
            if (!$grid.length) return;

            let html = '';
            children.forEach(c => {
                const med = c.basicMedical || {};
                const acad = c.academic || {};
                const latestPaper = (acad.paperResults && acad.paperResults.length) ? acad.paperResults[0] : null;

                const medConditionBadge = med.condition 
                    ? `<span class="med-condition-pill caution" title="${med.allergies || 'No allergies'}"><i class="fas fa-heartbeat"></i> ${this.escape(med.condition)}</span>`
                    : `<span class="med-condition-pill normal"><i class="fas fa-check-circle"></i> Fit & Healthy</span>`;

                const paperResultBadge = latestPaper
                    ? `<span class="badge badge-info" title="${latestPaper.examName}"><i class="fas fa-file-alt"></i> ${latestPaper.overallScore}% (${latestPaper.status})</span>`
                    : `<span class="badge badge-secondary"><i class="fas fa-pencil-alt"></i> No paper results</span>`;

                html += `
                    <div class="child-card-wrapper searchable-item" data-category="${this.escape(c.dorm)}">
                        <div class="child-profile-card">
                            <div>
                                <div class="card-top-info">
                                    <img src="${c.avatar}" class="avatar-large" alt="${this.escape(c.name)}" onerror="this.src='https://images.unsplash.com/photo-1544717305-2782549b5136?w=150&auto=format&fit=crop&q=80'">
                                    <div style="text-align: right;">
                                        <span class="badge badge-secondary" style="font-family: monospace;">${c.id}</span>
                                        <div style="margin-top: 4px;"><span class="badge badge-success">Active Ward</span></div>
                                    </div>
                                </div>
                                <div class="profile-name">${this.escape(c.name)}</div>
                                <div class="profile-meta-line">"${this.escape(c.preferredName)}" &bull; ${c.age} yrs &bull; ${c.gender} &bull; Blood: <strong>${med.bloodType || 'O+'}</strong></div>

                                <div class="info-rows">
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-home"></i> Dormitory:</span>
                                        <span class="info-row-val">${this.escape(c.dorm)}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-graduation-cap"></i> School:</span>
                                        <span class="info-row-val">${this.escape(c.school)} (${this.escape(c.grade)})</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-file-invoice"></i> Latest Paper:</span>
                                        <span class="info-row-val">${paperResultBadge}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-notes-medical"></i> Medical:</span>
                                        <span class="info-row-val">${medConditionBadge}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-row-label"><i class="fas fa-stethoscope"></i> Next Clinic:</span>
                                        <span class="info-row-val" style="color: #ef4444;">${med.nextClinicDate || 'None scheduled'}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="child-record-actions">
                                <div class="child-record-actions-primary">
                                    <button type="button" class="btn-action-paper" onclick="ChildWelfareUI.openKeyPaperModal('${c.id}')" title="${this.isStaffEditor() ? 'Enter exam results' : 'View exam results'}">
                                        <i class="fas ${this.isStaffEditor() ? 'fa-pen-nib' : 'fa-file-lines'}" aria-hidden="true"></i><span>${this.isStaffEditor() ? 'Key In Paper' : 'View Exam Results'}</span>
                                    </button>
                                    <button type="button" class="btn-action-med" onclick="ChildWelfareUI.openRecordMedicalModal('${c.id}')" title="${this.isStaffEditor() ? 'Update medical record' : 'View medical record'}">
                                        <i class="fas fa-stethoscope" aria-hidden="true"></i><span>${this.isStaffEditor() ? 'Medical Rec' : 'View Medical Record'}</span>
                                    </button>
                                </div>
                                <div class="child-record-actions-secondary">
                                    <button type="button" class="btn-outline btn-sm child-record-full" onclick="ChildWelfareUI.openDossierModal('${c.id}')">
                                        <i class="fas fa-folder-open" aria-hidden="true"></i><span>View Full PDF</span><i class="fas fa-arrow-right" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn-outline btn-sm child-record-remove" onclick="ChildWelfareUI.confirmDelete('${c.id}')" title="Archive / Remove child record" aria-label="Remove ${this.escape(c.name)}">
                                        <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $grid.html(html);
        },

        renderChildrenTable: function(children) {
            const $tbody = $('#children_table_tbody');
            if (!$tbody.length) return;

            let html = '';
            children.forEach(c => {
                const med = c.basicMedical || {};
                const acad = c.academic || {};
                const latestPaper = (acad.paperResults && acad.paperResults.length) ? acad.paperResults[0] : null;

                html += `
                    <tr class="searchable-item" data-category="${this.escape(c.dorm)}">
                        <td>
                            <div class="table-child-cell">
                                <img src="${c.avatar}" class="table-child-avatar" alt="${this.escape(c.name)}" onerror="this.src='https://images.unsplash.com/photo-1544717305-2782549b5136?w=150&auto=format&fit=crop&q=80'">
                                <div>
                                    <div class="table-child-name">${this.escape(c.name)}</div>
                                    <div class="table-child-sub">"${this.escape(c.preferredName)}" &bull; ${c.age} yrs &bull; ${c.gender} &bull; <span style="font-family: monospace; font-weight: 700;">${c.id}</span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>${this.escape(c.dorm)}</strong>
                            <div style="font-size: 11px; color: #64748b;">${this.escape(c.room || 'Main Ward')}</div>
                        </td>
                        <td>
                            <div>${this.escape(c.school)}</div>
                            <span class="badge badge-secondary" style="margin-top: 2px;">${this.escape(c.grade)}</span>
                        </td>
                        <td>
                            ${latestPaper ? `
                                <div><strong style="color: #2563eb;">${latestPaper.overallScore}%</strong> <span class="badge badge-info" style="font-size: 10px;">${latestPaper.status}</span></div>
                                <div style="font-size: 11px; color: #64748b;" title="${this.escape(latestPaper.examName)}">${this.escape(latestPaper.term)}</div>
                            ` : `
                                <span style="font-size: 11.5px; color: #94a3b8; font-style: italic;">No paper results</span>
                            `}
                        </td>
                        <td>
                            <div><span class="badge badge-secondary" style="font-weight: 700;">${med.bloodType || 'O+'}</span> <strong style="font-size: 12px;">${this.escape(med.condition || 'Healthy')}</strong></div>
                            <div style="font-size: 11px; color: #64748b;">Allergies: ${this.escape(med.allergies || 'None')}</div>
                        </td>
                        <td>
                            <div style="font-size: 12px; font-weight: 600; color: #ef4444;">${med.nextClinicDate || 'None'}</div>
                            <div style="font-size: 11px; color: #64748b;">${this.escape(med.nextClinicSpecialty || 'General')}</div>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button class="btn-action-paper" onclick="ChildWelfareUI.openKeyPaperModal('${c.id}')" title="${this.isStaffEditor() ? 'Enter exam results' : 'View exam results'}">
                                <i class="fas ${this.isStaffEditor() ? 'fa-pen-nib' : 'fa-file-lines'}"></i> ${this.isStaffEditor() ? 'Key Paper' : 'View Results'}
                            </button>
                            <button class="btn-action-med" onclick="ChildWelfareUI.openRecordMedicalModal('${c.id}')" title="${this.isStaffEditor() ? 'Update medical record' : 'View medical record'}">
                                <i class="fas fa-stethoscope"></i> ${this.isStaffEditor() ? 'Medical' : 'View Medical'}
                            </button>
                            <button class="btn-outline btn-sm" onclick="ChildWelfareUI.openDossierModal('${c.id}')" title="Official PDF">
                                <i class="fas fa-id-card"></i>
                            </button>
                            <button class="btn-outline btn-sm" style="color: #ef4444;" onclick="ChildWelfareUI.confirmDelete('${c.id}')" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            $tbody.html(html);
        },

        renderAcademicsTab: function(children) {
            const $tbody = $('#academics_table_tbody');
            if (!$tbody.length) return;

            let html = '';
            children.forEach(c => {
                const acad = c.academic || {};
                const papers = acad.paperResults || [];
                const latestPaper = papers.length > 0 ? papers[0] : null;

                let subjectsHtml = '';
                if (latestPaper && latestPaper.subjects && latestPaper.subjects.length > 0) {
                    latestPaper.subjects.forEach(s => {
                        subjectsHtml += `<span class="badge badge-info" style="margin-right: 4px; margin-bottom: 2px;">${this.escape(s.name)} (${s.score}%)</span>`;
                    });
                } else {
                    subjectsHtml = '<span style="color: #94a3b8; font-size: 11px; font-style: italic;">No individual subject papers keyed in</span>';
                }

                const attendanceVal = acad.attendance || 95;
                const gpaVal = acad.gpa || 3.50;
                const standing = acad.academicStanding || 'Good';

                let statusBadgeClass = 'badge-success';
                if (standing.toLowerCase().includes('support') || standing.toLowerCase().includes('need')) {
                    statusBadgeClass = 'badge-warning';
                } else if (standing.toLowerCase().includes('distinction') || standing.toLowerCase().includes('honor')) {
                    statusBadgeClass = 'badge-success';
                }

                html += `
                    <tr class="searchable-item">
                        <td>
                            <strong>${this.escape(c.name)}</strong>
                            <div style="font-size: 11px; color: #64748b; font-family: monospace;">${c.id}</div>
                        </td>
                        <td>
                            ${this.escape(c.school)}
                            <div style="font-size: 11px; color: #64748b;">${c.schoolId || 'SCH-General'}</div>
                        </td>
                        <td><span class="badge badge-secondary">${this.escape(c.grade)}</span></td>
                        <td>
                            <strong style="color: #10b981;">${attendanceVal}%</strong>
                            <div style="height: 4px; background: #e2e8f0; border-radius: 4px; margin-top: 4px; width: 60px;">
                                <div style="height: 100%; background: #10b981; width: ${attendanceVal}%;"></div>
                            </div>
                        </td>
                        <td><strong style="font-size: 14px;">${gpaVal}</strong></td>
                        <td>
                            <div style="font-weight: 700; font-size: 12px; margin-bottom: 4px; color: var(--dark);">
                                ${latestPaper ? `${this.escape(latestPaper.examName)} (${latestPaper.overallScore}%)` : 'Baseline Record'}
                            </div>
                            <div>${subjectsHtml}</div>
                        </td>
                        <td>
                            <span class="badge ${statusBadgeClass}">${this.escape(standing)}</span>
                        </td>
                        <td style="text-align: right;">
                            <button class="btn-action-paper" onclick="ChildWelfareUI.openKeyPaperModal('${c.id}')" title="${this.isStaffEditor() ? 'Enter exam results' : 'View exam results'} for ${this.escape(c.name)}">
                                <i class="fas ${this.isStaffEditor() ? 'fa-plus' : 'fa-file-lines'}"></i> ${this.isStaffEditor() ? 'Key Paper Result' : 'View Exam Results'}
                            </button>
                        </td>
                    </tr>
                `;
            });

            $tbody.html(html);
        },

        renderHealthcareTab: function(children) {
            const $grid = $('#healthcare_cards_grid');
            if (!$grid.length) return;

            let html = '';
            children.forEach(c => {
                const med = c.basicMedical || {};

                html += `
                    <div class="portal-card searchable-item" style="padding: 24px;" data-category="${this.escape(c.dorm)}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="${c.avatar}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;" alt="${this.escape(c.name)}" onerror="this.src='https://images.unsplash.com/photo-1544717305-2782549b5136?w=150&auto=format&fit=crop&q=80'">
                                <div>
                                    <h4 style="font-size: 15px; font-weight: 800; color: var(--dark);">${this.escape(c.name)}</h4>
                                    <span style="font-size: 11px; color: #64748b;">Blood Type: <strong>${med.bloodType || 'O+'}</strong> &bull; Age ${c.age} &bull; ${c.gender}</span>
                                </div>
                            </div>
                            <button class="btn-action-med" onclick="ChildWelfareUI.openRecordMedicalModal('${c.id}')" title="${this.isStaffEditor() ? 'Update medical record' : 'View medical record'}">
                                <i class="fas ${this.isStaffEditor() ? 'fa-pencil-alt' : 'fa-notes-medical'}"></i> ${this.isStaffEditor() ? 'Update Medical' : 'View Medical Record'}
                            </button>
                        </div>

                        <div style="background: #f8fafc; padding: 12px; border-radius: var(--radius-sm); margin: 12px 0; font-size: 12px;">
                            <div><span style="color: #64748b;">Primary Medical Condition:</span> <strong style="color: var(--dark);">${this.escape(med.condition || 'Healthy')}</strong></div>
                            <div style="margin-top: 4px;"><span style="color: #64748b;">Next Clinic Review:</span> <strong style="color: #ef4444;">${med.nextClinicDate || 'None'}</strong> &bull; ${this.escape(med.nextClinicSpecialty || 'General')}</div>
                            <div style="margin-top: 4px; font-size: 11px; color: #64748b;">Doctor / Facility: <strong>${this.escape(med.clinicDoctor || 'Orphanage Clinic')}</strong> (${this.escape(med.clinicLocation || 'Titiwangsa')})</div>
                        </div>

                        <div style="font-size: 12px; display: flex; flex-direction: column; gap: 6px;">
                            <div><strong>Known Allergies:</strong> <span class="badge badge-warning">${this.escape(med.allergies || 'None recorded')}</span></div>
                            <div><strong>Prescriptions & Meds:</strong> ${this.escape(med.medications || 'None currently prescribed')}</div>
                            <div><strong>Immunization Status:</strong> <span class="badge badge-success">${this.escape(med.immunization || 'Up to Date')}</span></div>
                        </div>

                        <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b; font-style: italic;">
                            "${this.escape(med.medicalNotes || 'Child is in good health and adhering to scheduled residential care routines.')}"
                        </div>
                    </div>
                `;
            });

            $grid.html(html);
        },

        renderHistoryTab: function(children) {
            const $select = $('#history_child_select');
            if ($select.length && !this.activeHistoryChildId && children.length > 0) {
                this.activeHistoryChildId = children[0].id;
            }

            if ($select.length) {
                let options = '';
                children.forEach(c => {
                    const sel = c.id === this.activeHistoryChildId ? 'selected' : '';
                    options += `<option value="${c.id}" ${sel}>${this.escape(c.name)} (${c.id})</option>`;
                });
                $select.html(options);
            }

            this.renderHistoryTimeline();
        },

        renderHistoryTimeline: function() {
            const childId = this.activeHistoryChildId;
            const child = window.HopeHavenChildStore.getById(childId);
            if (!child) return;

            $('#history_child_title').text(`${child.name} — Institutional Lifecycle & Historical PDF`);
            $('#history_child_id_badge').text(child.id);

            const $timeline = $('#history_timeline_container');
            if (!$timeline.length) return;

            const events = child.timeline || [];
            let html = '';

            events.forEach(ev => {
                let color = '#3b82f6';
                if (ev.category === 'intake') color = '#8b5cf6';
                if (ev.category === 'medical') color = '#ef4444';
                if (ev.category === 'academic') color = '#10b981';

                html += `
                    <div style="position: relative;">
                        <div style="position: absolute; left: -36px; top: 0; width: 14px; height: 14px; border-radius: 50%; background: ${color}; border: 2px solid #fff;"></div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700;">
                            <span>${this.escape(ev.title)}</span>
                            <span style="color: #64748b; font-family: monospace;">${ev.date}</span>
                        </div>
                        <p style="font-size: 12px; color: #475569; margin-top: 4px;">
                            ${this.escape(ev.detail)}
                        </p>
                    </div>
                `;
            });

            $timeline.html(html);
        },

        populateChildSelectDropdowns: function(children) {
            const selectors = ['#paper_child_select', '#med_child_select', '#health_appointment_child_select'];
            selectors.forEach(selId => {
                const $el = $(selId);
                if ($el.length) {
                    let opts = '';
                    children.forEach(c => {
                        opts += `<option value="${c.id}">${this.escape(c.name)} (${c.id}) - ${this.escape(c.grade)}</option>`;
                    });
                    $el.html(opts);
                }
            });
        },

        bindEvents: function() {
            const self = this;

            // View Mode Toggle (Cards vs Table)
            $('#btn_view_cards').on('click', function() {
                $('.view-mode-btn').removeClass('active');
                $(this).addClass('active');
                $('#profiles_grid').fadeIn(200);
                $('#profiles_table_wrapper').hide();
                self.currentViewMode = 'cards';
            });

            $('#btn_view_table').on('click', function() {
                $('.view-mode-btn').removeClass('active');
                $(this).addClass('active');
                $('#profiles_grid').hide();
                $('#profiles_table_wrapper').fadeIn(200);
                self.currentViewMode = 'table';
            });

            // History child select change
            $('#history_child_select').on('change', function() {
                self.activeHistoryChildId = $(this).val();
                self.renderHistoryTimeline();
            });

            // Auto calculate paper overall marks
            $(document).on('input', '.subject-score-input', function() {
                self.calculatePaperResultAverages();
            });

            // When changing child in medical modal, update input fields
            $(document).on('change', '#med_child_select', function() {
                const cId = $(this).val();
                if (window.HopeHavenChildStore) {
                    const selectedChild = window.HopeHavenChildStore.getById(cId);
                    self.populateMedicalFormFields(selectedChild);
                }
            });
        },

        // Modal triggers
        openKeyPaperModal: function(childId) {
            if (!this.isStaffEditor()) return this.openDossierModal(childId, 'dossier_tab_papers');
            const list = window.HopeHavenChildStore ? window.HopeHavenChildStore.getAll() : [];
            const targetId = childId || (list.length > 0 ? list[0].id : null);
            if (targetId) {
                $('#paper_child_select').val(targetId);
            }
            this.resetSubjectRows();
            openModal('modal_key_paper_result');
        },

        openRecordMedicalModal: function(childId) {
            if (!this.isStaffEditor()) return this.openDossierModal(childId, 'dossier_tab_medical');
            if (!window.HopeHavenChildStore) return;
            const list = window.HopeHavenChildStore.getAll();
            const child = childId ? window.HopeHavenChildStore.getById(childId) : (list.length > 0 ? list[0] : null);
            if (!child) return;

            $('#med_child_select').val(child.id).trigger('change');
            this.populateMedicalFormFields(child);
            openModal('modal_record_medical');
        },

        populateMedicalFormFields: function(child) {
            if (!child) return;
            const med = child.basicMedical || {};
            $('#med_blood_type').val(med.bloodType || 'O+');
            $('#med_condition_input').val(med.condition || 'Healthy');
            $('#med_allergies_input').val(med.allergies || 'None');
            $('#med_prescriptions_input').val(med.medications || 'None');
            $('#med_immunization_select').val(med.immunization || 'Up to Date');
            $('#med_next_clinic_date').val(med.nextClinicDate || '2026-11-15');
            $('#med_doctor_input').val(med.clinicDoctor || 'Dr. Angela Reyes');
            $('#med_notes_input').val(med.medicalNotes || '');
        },

        openDossierModal: function(childId, tabId) {
            const child = window.HopeHavenChildStore.getById(childId);
            if (!child) return;

            const med = child.basicMedical || {};
            const acad = child.academic || {};
            const papers = acad.paperResults || [];

            $('#dossier_modal_name').text(child.name);
            $('#dossier_modal_meta').text(`Code: ${child.id} · Admitted: ${child.admissionDate} · Case Worker: ${child.caseWorker}`);
            $('#dossier_modal_avatar').attr('src', child.avatar);
            $('#dossier_modal_head_name').text(child.name);
            $('#dossier_modal_head_sub').html([`${child.age} years old`, child.gender, `Blood: ${med.bloodType || 'Not recorded'}`].map(value => `<span>${this.escape(value)}</span>`).join(''));
            $('#dossier_modal_head_dorm').text(`${child.dorm} (${child.room || 'Main Ward'})`);

            // Academic Card
            $('#dossier_modal_school').text(child.school);
            $('#dossier_modal_grade').text(child.grade);
            $('#dossier_modal_gpa').text(`${acad.gpa || 3.50} (${acad.academicStanding || 'Good'})`);
            $('#dossier_modal_attendance').text(`${acad.attendance || 95}%`);

            // Health Card
            $('#dossier_modal_condition').text(med.condition || 'Healthy');
            $('#dossier_modal_allergies').text(med.allergies || 'None');
            $('#dossier_modal_meds').text(med.medications || 'None');
            $('#dossier_modal_next_visit').text(med.nextClinicDate || 'None scheduled');

            // Paper Results History List
            let papersHtml = '';
            if (papers.length > 0) {
                papers.forEach(p => {
                    const normalizedScore = value => value !== null && value !== undefined && value !== '' && Number.isFinite(Number(value)) ? Math.min(100, Math.max(0, Number(value))) : null;
                    const overall = normalizedScore(p.overallScore);
                    let subs = '';
                    if (p.subjects && p.subjects.length > 0) {
                        p.subjects.forEach(s => {
                            const score = normalizedScore(s.score);
                            const grade = s.grade || (score === null ? '—' : this.scoreToGrade(score));
                            subs += `<article class="record-subject">
                                <div class="record-subject-top"><span class="record-subject-name">${this.escape(s.name)}</span><span class="record-subject-grade">${this.escape(grade)}</span></div>
                                <div class="record-subject-score">${score === null ? '—' : score}<small>${score === null ? 'Not recorded' : '/ 100 marks'}</small></div>
                                ${score === null ? '' : `<div class="record-score-track" role="progressbar" aria-label="${this.escape(s.name)} score" aria-valuenow="${score}" aria-valuemin="0" aria-valuemax="100"><span style="width:${score}%;"></span></div>`}
                            </article>`;
                        });
                    } else {
                        subs = '<p class="record-empty">No subject scores recorded for this exam.</p>';
                    }
                    papersHtml += `
                        <article class="paper-history-card">
                            <div class="record-exam-head">
                                <div>
                                    <div class="record-exam-type"><i class="fas fa-file-lines" aria-hidden="true"></i> EXAM SUMMARY</div>
                                    <h4 class="record-exam-title">${this.escape(p.examName)}</h4>
                                    <div class="record-exam-meta">${this.escape(p.term || 'Term not recorded')}<br><i class="far fa-calendar" aria-hidden="true"></i> ${this.escape(p.date || 'Date not recorded')}</div>
                                </div>
                                <div class="record-overall-score"><small>OVERALL</small><strong>${overall === null ? '—' : overall + '%'}</strong><span>${this.escape(p.status || 'No standing recorded')}</span></div>
                            </div>
                            <div class="record-subjects">${subs}</div>
                            ${p.remarks ? `<div class="record-exam-note"><i class="fas fa-quote-left" aria-hidden="true"></i><span>${this.escape(p.remarks)}</span></div>` : ''}
                        </article>
                    `;
                });
            } else {
                papersHtml = '<div class="record-empty"><i class="fas fa-book-open" aria-hidden="true"></i><p>No exam results recorded yet.</p><small>Saved results will appear here when they are added.</small></div>';
            }
            $('#dossier_paper_history_list').html(papersHtml);

            // Switch to first tab in PDF
            $('#dossier_record_label').text(tabId === 'dossier_tab_papers' ? 'Exam Results' : tabId === 'dossier_tab_medical' ? 'Medical Record' : 'Child Records');
            $('#dossier_medical_immunization').text(med.immunization || 'Not recorded');
            $('#dossier_medical_blood').text(med.bloodType || 'Not recorded');
            $('#dossier_medical_clinic').text(med.clinicLocation || 'Not recorded');
            $('#dossier_medical_doctor').text(med.clinicDoctor || 'Not assigned');
            $('#dossier_medical_condition').text(med.condition || 'Not recorded');
            $('#dossier_medical_allergies').text(med.allergies || 'None recorded');
            $('#dossier_medical_medications').text(med.medications || 'None recorded');
            $('#dossier_medical_review').text(med.nextClinicDate || 'Not scheduled');
            $('#dossier_medical_notes').text(med.medicalNotes || 'No notes recorded');
            this.switchDossierTab(tabId || 'dossier_tab_overview');

            openModal('modal_child_dossier');
        },

        switchDossierTab: function(tabId) {
            const labels = {dossier_tab_overview: 'Child Records', dossier_tab_papers: 'Exam Results', dossier_tab_medical: 'Medical Record'};
            if (!labels[tabId]) return;
            const viewer = $('#modal_child_dossier');
            viewer.find('.dossier-tab-btn').removeClass('active').attr('aria-pressed', 'false');
            viewer.find(`.dossier-tab-btn[data-dossier-tab="${tabId}"]`).addClass('active').attr('aria-pressed', 'true');
            viewer.find('.dossier-pane').stop(true, true).hide();
            $('#' + tabId).show();
            $('#dossier_record_label').text(labels[tabId]);
        },

        resetSubjectRows: function() {
            const defaultSubjects = [
                { name: 'Mathematics', score: 85 },
                { name: 'Science', score: 88 },
                { name: 'English Language', score: 90 },
                { name: 'Bahasa Melayu', score: 86 }
            ];

            let html = '';
            defaultSubjects.forEach(s => {
                html += `
                    <div class="subject-score-row">
                        <input type="text" class="form-control subject-name-input" value="${s.name}" placeholder="Subject Name">
                        <input type="number" class="form-control subject-score-input" value="${s.score}" min="0" max="100" placeholder="Marks %">
                        <input type="text" class="form-control subject-grade-preview" value="${this.scoreToGrade(s.score)}" readonly style="background: #f1f5f9; text-align: center; font-weight: 700;">
                        <button type="button" class="subject-remove-btn" onclick="$(this).closest('.subject-score-row').remove(); ChildWelfareUI.calculatePaperResultAverages();" title="Remove Subject">&times;</button>
                    </div>
                `;
            });

            $('#subjects_container').html(html);
            this.calculatePaperResultAverages();
        },

        addSubjectRow: function() {
            const html = `
                <div class="subject-score-row">
                    <input type="text" class="form-control subject-name-input" value="Creative Arts" placeholder="Subject Name">
                    <input type="number" class="form-control subject-score-input" value="90" min="0" max="100" placeholder="Marks %">
                    <input type="text" class="form-control subject-grade-preview" value="A+" readonly style="background: #f1f5f9; text-align: center; font-weight: 700;">
                    <button type="button" class="subject-remove-btn" onclick="$(this).closest('.subject-score-row').remove(); ChildWelfareUI.calculatePaperResultAverages();" title="Remove Subject">&times;</button>
                </div>
            `;
            $('#subjects_container').append(html);
            this.calculatePaperResultAverages();
        },

        calculatePaperResultAverages: function() {
            let total = 0;
            let count = 0;

            const self = this;
            $('.subject-score-row').each(function() {
                const score = parseFloat($(this).find('.subject-score-input').val()) || 0;
                $(this).find('.subject-grade-preview').val(self.scoreToGrade(score));
                total += score;
                count++;
            });

            const avg = count > 0 ? Math.round(total / count) : 0;
            $('#paper_avg_score_preview').text(`${avg}%`);
            $('#paper_avg_score_hidden').val(avg);

            // Suggested GPA
            const gpa = ((avg / 100) * 4.0).toFixed(2);
            $('#paper_gpa_input').val(gpa);

            // Suggested standing
            if (avg >= 90) {
                $('#paper_standing_select').val('Distinction');
            } else if (avg >= 75) {
                $('#paper_standing_select').val('Good');
            } else {
                $('#paper_standing_select').val('Needs Math Support');
            }
        },

        scoreToGrade: function(score) {
            if (score >= 95) return 'A+';
            if (score >= 85) return 'A';
            if (score >= 75) return 'B+';
            if (score >= 65) return 'B';
            if (score >= 50) return 'C';
            return 'D';
        },

        confirmDelete: function(childId) {
            const child = window.HopeHavenChildStore.getById(childId);
            if (!child) return;

            if (confirm(`Are you sure you want to archive / delete child profile "${child.name}" (${child.id})?`)) {
                window.HopeHavenChildStore.deleteChild(childId);
                this.renderAll();
                showPortalToast(`Child record for ${child.name} removed from registry.`, false, 'Child Record Archived', 'Child Welfare Registry');
            }
        },

        escape: function(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    };

    // Form Handlers
    window.handleRegisterNewChildForm = function(event) {
        event.preventDefault();
        if (!window.HopeHavenChildPhoto.ready(event.target)) return;

        const name = $('#reg_child_name').val().trim();
        const preferred = $('#reg_child_callname').val().trim() || name.split(' ')[0];
        const gender = $('#reg_child_gender').val();
        const dob = $('#reg_child_dob').val();
        const dorm = $('#reg_child_dorm').val();
        const school = $('#reg_child_school').val().trim() || 'Hope Junior Academy';
        const grade = $('#reg_child_grade').val().trim() || 'Grade 3';
        const bloodType = $('#reg_child_blood').val();
        const condition = $('#reg_child_condition').val().trim() || 'Healthy / No Chronic Condition';
        const allergies = $('#reg_child_allergies').val().trim() || 'None recorded';
        const notes = $('#reg_child_notes').val().trim();

        const newChild = window.HopeHavenChildStore.add({
            name: name,
            preferredName: preferred,
            gender: gender,
            dob: dob,
            dorm: dorm,
            school: school,
            grade: grade,
            bloodType: bloodType,
            condition: condition,
            allergies: allergies,
            avatar: window.HopeHavenChildPhoto.get(event.target),
            medicalNotes: notes
        });

        closeModal('modal_register_child');
        event.target.reset();
        window.ChildWelfareUI.renderAll();

        showPortalToast(
            `Child profile for ${name} (${newChild.id}) successfully added to orphanage registry!`,
            true,
            'Child Ward Registered',
            'Child Welfare Registry'
        );
    };

    window.handleKeyPaperResultForm = function(event) {
        event.preventDefault();
        if (!window.ChildWelfareUI.isStaffEditor()) return;

        const childId = $('#paper_child_select').val();
        const child = window.HopeHavenChildStore.getById(childId);
        if (!child) return;

        const examName = $('#paper_exam_name').val().trim() || 'Term Paper Examination';
        const term = $('#paper_term').val().trim() || 'Term 2 · 2026';
        const examDate = $('#paper_exam_date').val() || new Date().toISOString().split('T')[0];
        const gpa = $('#paper_gpa_input').val() || '3.50';
        const status = $('#paper_standing_select').val();
        const remarks = $('#paper_remarks_input').val().trim();

        const subjects = [];
        let totalScore = 0;
        let count = 0;

        $('.subject-score-row').each(function() {
            const sName = $(this).find('.subject-name-input').val().trim();
            const sScore = parseFloat($(this).find('.subject-score-input').val()) || 0;
            const sGrade = window.ChildWelfareUI.scoreToGrade(sScore);
            if (sName) {
                subjects.push({ name: sName, score: sScore, grade: sGrade });
                totalScore += sScore;
                count++;
            }
        });

        const overall = count > 0 ? Math.round(totalScore / count) : 85;

        const paperData = {
            examName: examName,
            term: term,
            date: examDate,
            subjects: subjects,
            overallScore: overall,
            gpa: parseFloat(gpa).toFixed(2),
            status: status,
            remarks: remarks || `Keyed in by staff. Completed ${count} subject examination papers.`
        };

        window.HopeHavenChildStore.recordPaperResult(childId, paperData);
        closeModal('modal_key_paper_result');
        event.target.reset();
        window.ChildWelfareUI.renderAll();

        showPortalToast(
            `Paper results (${overall}% - ${status}) recorded for ${child.name}!`,
            true,
            'Paper Results Keyed In',
            'Academic Records'
        );
    };

    window.handleRecordMedicalForm = function(event) {
        event.preventDefault();
        if (!window.ChildWelfareUI.isStaffEditor()) return;

        const childId = $('#med_child_select').val();
        const child = window.HopeHavenChildStore.getById(childId);
        if (!child) return;

        const medData = {
            bloodType: $('#med_blood_type').val(),
            condition: $('#med_condition_input').val().trim(),
            allergies: $('#med_allergies_input').val().trim(),
            medications: $('#med_prescriptions_input').val().trim(),
            immunization: $('#med_immunization_select').val(),
            nextClinicDate: $('#med_next_clinic_date').val(),
            clinicDoctor: $('#med_doctor_input').val().trim(),
            medicalNotes: $('#med_notes_input').val().trim()
        };

        window.HopeHavenChildStore.updateMedicalCondition(childId, medData);
        closeModal('modal_record_medical');
        window.ChildWelfareUI.renderAll();

        showPortalToast(
            `Basic medical condition & healthcare PDF updated for ${child.name}!`,
            true,
            'Medical Condition Logged',
            'Orphanage Healthcare'
        );
    };

    // Initialize upon DOM readiness
    $(document).ready(function() {
        if (!document.getElementById('profiles_grid')) return;
        window.ChildWelfareUI.init();
        window.HopeHavenSync.subscribe(STORAGE_KEY, function() {
            window.HopeHavenSync.refreshView(() => window.ChildWelfareUI.renderAll());
            $('.search-input[data-search-target]').trigger('keyup');
        });
    });

})();
