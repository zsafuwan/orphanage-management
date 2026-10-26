/**
 * HopeHaven Orphanage Management System
 * Shared Welfare Requisitions & Multi-Tier Approvals Store
 * File: public/js/portal/welfare-store.js
 */

(function() {
    'use strict';

    const STORAGE_KEY = 'hopehaven_welfare_requests_v2';

    const SEED_REQUESTS = [
        {
            id: 'WR-2026-045',
            title: 'Pediatric Asthma Ultrasonic Spacer & Nebulizer Chamber',
            category: 'Medical / Surgical',
            beneficiaryName: 'Maya Cruz',
            beneficiaryId: 'CH-2024-001',
            dorm: 'St. Clare Cottage',
            urgency: 'Emergency',
            cost: 85.00,
            lineItems: [
                { item: 'AeroChamber Plus Flow-Vu Spacer', cost: 45.00 },
                { item: 'Replacement Silicone Mask (Child Size)', cost: 22.00 },
                { item: 'Sterile Saline Nebule Solution (20x2.5ml)', cost: 18.00 }
            ],
            justification: 'Maya developed recurrent nighttime wheezing during the seasonal temperature drop. Her previous spacer has a micro-crack causing medication loss.',
            requestedBy: 'Nurse Mary (Active Caregiver)',
            requestedDate: 'Today · 08:30 AM',
            status: 'Pending Admin Approval',
            adminDecision: null
        },
        {
            id: 'WR-2026-044',
            title: 'Secondary School Exam Review Books & Scientific Calculators',
            category: 'Education & Tuition',
            beneficiaryName: 'Elijah Liam Ramos',
            beneficiaryId: 'CH-2022-008',
            dorm: 'St. Francis Boys Hall',
            urgency: 'High',
            cost: 117.00,
            lineItems: [
                { item: 'Casio Scientific Calculator fx-570EX', cost: 28.00 },
                { item: '3x Algebra & Trigonometry Workbooks', cost: 54.00 },
                { item: 'Physics SPM Syllabus Comprehensive Guide', cost: 35.00 }
            ],
            justification: 'Elijah is entering mid-year national mock evaluations. Lacks personal scientific calculator for trigonometry and physics practical exams.',
            requestedBy: 'Mr. David Tan (Lead Educator)',
            requestedDate: '2026-10-18',
            status: 'Pending Admin Approval',
            adminDecision: null
        },
        {
            id: 'WR-2026-042',
            title: 'Emergency Pediatric Dental Extraction & Cavity Surgery',
            category: 'Medical / Surgical',
            beneficiaryName: 'Maya Cruz',
            beneficiaryId: 'CH-2024-001',
            dorm: 'St. Clare Cottage',
            urgency: 'Emergency',
            cost: 445.00,
            lineItems: [
                { item: 'Pediatric Oral Surgeon Specialist Consultation', cost: 120.00 },
                { item: '2x Molar Extraction & Local Anesthesia', cost: 280.00 },
                { item: 'Post-op Amoxicillin & Analgesic Syrup', cost: 45.00 }
            ],
            justification: 'Maya presented with acute molar abscess preventing sleep and normal feeding. Immediate surgical extraction required.',
            requestedBy: 'Nurse Jenny Adams',
            requestedDate: '2026-10-16',
            status: 'Approved',
            adminDecision: {
                approvedBy: 'Sister Grace (Executive Director)',
                decisionDate: '2026-10-16 11:45 AM',
                fundingSource: 'Sanctuary Emergency Healthcare Endowment',
                remarks: 'Approved unconditionally. Hospital escort assigned to Volunteer Sarah.',
                disbursementRef: 'DISB-MED-2026-089'
            }
        },
        {
            id: 'WR-2026-039',
            title: 'Winter Thermal Quilts & Flannel Sheet Sets for Boys Hall',
            category: 'Clothing & Bedding',
            beneficiaryName: 'St. Francis Boys Hall (Dorm 2)',
            beneficiaryId: 'FAC-DORM-02',
            dorm: 'St. Francis Boys Hall',
            urgency: 'Standard',
            cost: 576.00,
            lineItems: [
                { item: '12x Heavyweight Fleece Quilts', cost: 360.00 },
                { item: '12x Flannel Bed Sheet Sets', cost: 216.00 }
            ],
            justification: 'Seasonal temperature drop in hillside facility. Ensuring all boys have insulated winter bedding.',
            requestedBy: 'Brother Luke (Facility Caregiver)',
            requestedDate: '2026-10-10',
            status: 'Approved',
            adminDecision: {
                approvedBy: 'Sister Grace (Executive Director)',
                decisionDate: '2026-10-11 14:00 PM',
                fundingSource: 'General Sanctuary Welfare Allocation',
                remarks: 'Approved with central storeroom stock drawing under Voucher VCH-882.',
                disbursementRef: 'DISB-BED-2026-034'
            }
        },
        {
            id: 'WR-2026-031',
            title: 'Custom Orthopedic Supportive Footwear & Arch Insoles',
            category: 'Medical / Surgical',
            beneficiaryName: 'Kenji Sato',
            beneficiaryId: 'CH-2025-019',
            dorm: 'St. Francis Boys Hall',
            urgency: 'Standard',
            cost: 160.00,
            lineItems: [
                { item: 'Orthopedic Support Running Shoes', cost: 110.00 },
                { item: 'Custom Arch Support Silicone Insoles', cost: 50.00 }
            ],
            justification: 'Kenji reported discomfort during physical education classes.',
            requestedBy: 'Nurse Mary (Active Caregiver)',
            requestedDate: '2026-09-28',
            status: 'Rejected',
            adminDecision: {
                approvedBy: 'Sister Grace (Executive Director)',
                decisionDate: '2026-09-29 16:20 PM',
                fundingSource: 'N/A',
                remarks: 'Declined for external purchase. Please draw from the unissued orthopedic shoe inventory donated by Beacon Medical Logistics in Storeroom Bay 3 first.',
                disbursementRef: null
            }
        }
    ];

    window.HopeHavenWelfareStore = {
        getAll: function() {
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                if (stored) {
                    return JSON.parse(stored);
                }
            } catch (e) {
                console.error('Error reading welfare store:', e);
            }
            this.saveAll(SEED_REQUESTS);
            return SEED_REQUESTS;
        },

        saveAll: function(list) {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
            } catch (e) {
                console.error('Error saving welfare store:', e);
            }
        },

        getById: function(id) {
            const list = this.getAll();
            return list.find(r => r.id === id) || null;
        },

        // STAFF-ONLY: Submit Requisition
        createRequest: function(data) {
            const list = this.getAll();
            const year = new Date().getFullYear();
            const seq = String(list.length + 1).padStart(3, '0');
            const newId = `WR-${year}-${seq}`;

            const newTicket = {
                id: newId,
                title: data.title || 'Welfare Assistance Requisition',
                category: data.category || 'General Welfare',
                beneficiaryName: data.beneficiaryName || 'Resident Ward',
                beneficiaryId: data.beneficiaryId || 'CH-General',
                dorm: data.dorm || 'Main Sanctuary Dorm',
                urgency: data.urgency || 'Standard',
                cost: parseFloat(data.cost) || 0,
                lineItems: data.lineItems || [],
                justification: data.justification || 'Logged by duty caregiver.',
                requestedBy: data.requestedBy || 'Nurse Mary (Active Caregiver)',
                requestedDate: 'Today · Just now',
                status: 'Pending Admin Approval',
                adminDecision: null
            };

            list.unshift(newTicket);
            this.saveAll(list);
            return newTicket;
        },

        // ADMIN-ONLY: Authorize Requisition
        approveRequest: function(id, adminName, fundingSource, remarks) {
            const list = this.getAll();
            const ticket = list.find(t => t.id === id);
            if (!ticket) return null;

            ticket.status = 'Approved';
            ticket.adminDecision = {
                approvedBy: adminName || 'Sister Grace (Executive Director)',
                decisionDate: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' · Authorized',
                fundingSource: fundingSource || 'Sanctuary Emergency Healthcare Endowment',
                remarks: remarks || 'Approved for full disbursement and procurement.',
                disbursementRef: `DISB-${Math.floor(1000 + Math.random() * 9000)}`
            };

            this.saveAll(list);
            return ticket;
        },

        // ADMIN-ONLY: Reject / Request Modification
        rejectRequest: function(id, adminName, reason) {
            const list = this.getAll();
            const ticket = list.find(t => t.id === id);
            if (!ticket) return null;

            ticket.status = 'Rejected';
            ticket.adminDecision = {
                approvedBy: adminName || 'Sister Grace (Executive Director)',
                decisionDate: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' · Declined',
                fundingSource: 'N/A',
                remarks: reason || 'Requisition denied after administrative assessment.',
                disbursementRef: null
            };

            this.saveAll(list);
            return ticket;
        }
    };

})();
