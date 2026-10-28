/* Shared browser-local stock and staff requests. Keep existing storage keys. */
(function () {
    'use strict';
    // Seed Data
    const DEFAULT_WARD_ITEMS = [
        {
            id: 'WINV-001',
            name: 'Pediatric Paracetamol Syrup (120mg/5ml)',
            category: 'Medical',
            location: 'Ward Alpha Medicine Cabinet',
            onHand: 4,
            minThreshold: 3,
            unit: 'bottles (100ml)',
            batchNo: 'BAT-2026-MED9',
            expiry: '2027-04-30'
        },
        {
            id: 'WINV-002',
            name: 'Antiseptic Wet Wipes (Alcohol-Free)',
            category: 'Hygiene',
            location: 'Ward Alpha Nursery Shelf',
            onHand: 2,
            minThreshold: 5,
            unit: 'packs (80 sheets)',
            batchNo: 'WIP-8821',
            expiry: '2027-08-15'
        },
        {
            id: 'WINV-003',
            name: 'Enfamil Stage 2 Baby Formula Powder',
            category: 'Nutrition',
            location: 'Ward Alpha Nursery Shelf',
            onHand: 3,
            minThreshold: 2,
            unit: 'cans (900g)',
            batchNo: 'ENF-9092',
            expiry: '2027-02-10'
        },
        {
            id: 'WINV-004',
            name: 'Sterile Adhesive Gauze & Plasters (Assorted)',
            category: 'Medical',
            location: 'Ward Alpha Medicine Cabinet',
            onHand: 14,
            minThreshold: 10,
            unit: 'strips/patches',
            batchNo: 'GAU-4102',
            expiry: '2028-01-01'
        },
        {
            id: 'WINV-005',
            name: 'Salbutamol Metered Dose Inhaler (100mcg)',
            category: 'Medical',
            location: 'Dispensary Refrigerator',
            onHand: 1,
            minThreshold: 2,
            unit: 'inhaler canisters',
            batchNo: 'SAL-1029',
            expiry: '2026-12-31'
        },
        {
            id: 'WINV-006',
            name: 'Comfort Baby Diapers (Size L · 9-14kg)',
            category: 'Hygiene',
            location: 'Ward Alpha Nursery Shelf',
            onHand: 6,
            minThreshold: 10,
            unit: 'packs (44 pcs)',
            batchNo: 'DIA-7721',
            expiry: '2028-06-01'
        },
        {
            id: 'WINV-007',
            name: 'Pediatric Oral Rehydration Salts (ORS Sachet)',
            category: 'Medical',
            location: 'Ward Alpha Medicine Cabinet',
            onHand: 18,
            minThreshold: 12,
            unit: 'sachets',
            batchNo: 'ORS-6612',
            expiry: '2027-10-20'
        },
        {
            id: 'WINV-008',
            name: 'Single Line Exercise Books (A4 80pg)',
            category: 'Education',
            location: 'Common Study Locker',
            onHand: 24,
            minThreshold: 15,
            unit: 'books',
            batchNo: 'ED-BK-009',
            expiry: 'N/A'
        },
        {
            id: 'WINV-009',
            name: 'Color Pencils & Drawing Pads Set',
            category: 'Education',
            location: 'Common Study Locker',
            onHand: 8,
            minThreshold: 5,
            unit: 'sets',
            batchNo: 'ART-3320',
            expiry: 'N/A'
        },
        {
            id: 'WINV-010',
            name: 'Baby Body Wash & Tear-Free Shampoo (500ml)',
            category: 'Hygiene',
            location: 'Ward Beta Care Cupboard',
            onHand: 5,
            minThreshold: 3,
            unit: 'bottles',
            batchNo: 'HYG-5002',
            expiry: '2027-09-10'
        },
        {
            id: 'WINV-011',
            name: 'Fitted Single Bed Linens & Pillowcases',
            category: 'Linen',
            location: 'Ward Beta Care Cupboard',
            onHand: 12,
            minThreshold: 8,
            unit: 'sets',
            batchNo: 'LIN-2201',
            expiry: 'N/A'
        },
        {
            id: 'WINV-012',
            name: 'Dettol Hospital-Grade Disinfectant (1L)',
            category: 'Hygiene',
            location: 'Ward Beta Care Cupboard',
            onHand: 3,
            minThreshold: 2,
            unit: 'bottles',
            batchNo: 'DET-9921',
            expiry: '2027-11-01'
        }
    ];

    const DEFAULT_DISPENSATIONS = [
        {
            id: 'DSP-2026-001',
            childName: 'Ahmad Razak (CH-2023-014)',
            itemName: 'Pediatric Paracetamol Syrup (120mg/5ml)',
            qty: '1 bottle (5ml dose)',
            category: 'Medical',
            reason: 'Mild evening fever (37.9°C); temperature reduced to 36.8°C within 1 hour.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 08:45 AM'
        },
        {
            id: 'DSP-2026-002',
            childName: 'Maya Cruz (CH-2024-001)',
            itemName: 'Color Pencils & Drawing Pads Set',
            qty: '1 set',
            category: 'Education',
            reason: 'Grade 3 Visual Arts project preparation; mentoring session with Sister Teresa.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 09:15 AM'
        },
        {
            id: 'DSP-2026-003',
            childName: 'Hannah Grace Morales (CH-2025-032)',
            itemName: 'Comfort Baby Diapers (Size L · 9-14kg)',
            qty: '2 pcs',
            category: 'Hygiene',
            reason: 'Routine nursery morning change in St. Clare Cottage.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 10:00 AM'
        },
        {
            id: 'DSP-2026-004',
            childName: 'Kenji Sato (CH-2025-019)',
            itemName: 'Single Line Exercise Books (A4 80pg)',
            qty: '2 books',
            category: 'Education',
            reason: 'School mathematics and Bahasa Melayu class note-taking replenishment.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 10:30 AM'
        },
        {
            id: 'DSP-2026-005',
            childName: 'Maya Cruz (CH-2024-001)',
            itemName: 'Salbutamol Metered Dose Inhaler (100mcg)',
            qty: '1 canister',
            category: 'Medical',
            reason: 'Routine morning pulmonary check; inhaler replaced for school sports bag.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 11:00 AM'
        },
        {
            id: 'DSP-2026-006',
            childName: 'Elijah Liam Ramos (CH-2022-008)',
            itemName: 'Sterile Adhesive Gauze & Plasters (Assorted)',
            qty: '2 patches',
            category: 'Medical',
            reason: 'Minor scrape dressing on left knee from basketball playground playtime.',
            dispensedBy: 'Nurse Mary',
            timestamp: 'Today · 11:30 AM'
        }
    ];

    const DEFAULT_REQUISITIONS = [
        {
            id: 'REQ-2026-104',
            item: 'Antiseptic Wet Wipes (Alcohol-Free)',
            qty: 5,
            category: 'Hygiene',
            destination: 'Ward Alpha Nursery Shelf',
            priority: 'Urgent',
            notes: 'Critical nursery stock depleted; required before evening feed.',
            status: 'Dispatched',
            statusLabel: 'En Route with Steward',
            time: 'Today 10:15 AM'
        },
        {
            id: 'REQ-2026-105',
            item: 'Salbutamol Metered Dose Inhaler (100mcg)',
            qty: 2,
            category: 'Medical',
            destination: 'Dispensary Refrigerator',
            priority: 'Urgent',
            notes: 'Down to single backup unit; multiple asthmatic wards.',
            status: 'Approved',
            statusLabel: 'Packed in Storeroom',
            time: 'Today 11:05 AM'
        },
        {
            id: 'REQ-2026-102',
            item: 'Enfamil Stage 2 Baby Formula Powder',
            qty: 4,
            category: 'Nutrition',
            destination: 'Ward Alpha Nursery Shelf',
            priority: 'Normal',
            notes: 'Weekly buffer replenishment for toddlers.',
            status: 'Received',
            statusLabel: 'Received by Nurse Mary',
            time: 'Yesterday 15:30'
        }
    ];


    const keys = {items: 'hopehaven_staff_inventory_v1', requests: 'hopehaven_staff_requisitions_v1', dispensations: 'hopehaven_staff_dispensations_v1'};
    const categories = ['Medical', 'Hygiene', 'Nutrition', 'Education', 'Linen'];
    const copy = value => JSON.parse(JSON.stringify(value));
    const text = value => String(value ?? '').trim();
    const normalize = value => text(value).toLowerCase();
    function read(key, fallback) {
        try {
            const raw = localStorage.getItem(key);
            if (raw !== null) {
                const value = JSON.parse(raw);
                if (Array.isArray(value)) return value;
            }
        } catch (_) { /* Use the shared examples when storage is unavailable. */ }
        return copy(fallback);
    }
    function write(key, value) { window.HopeHavenSync.write(key, JSON.stringify(value)); }
    function quantity(value, min = 0) {
        if (value === '' || value === null || value === undefined || !Number.isSafeInteger(Number(value)) || Number(value) < min) {
            throw new Error(min ? 'Enter a positive whole quantity.' : 'Enter a whole stock count of zero or more.');
        }
        return Number(value);
    }
    function required(value, label) {
        const result = text(value);
        if (!result) throw new Error(label + ' is required.');
        return result;
    }
    const store = {
        keys, categories,
        getItems() { return read(keys.items, DEFAULT_WARD_ITEMS); },
        saveItems(items) { write(keys.items, items); },
        getRequisitions() { return read(keys.requests, DEFAULT_REQUISITIONS); },
        saveRequisitions(requests) { write(keys.requests, requests); },
        getDispensations() { return read(keys.dispensations, DEFAULT_DISPENSATIONS); },
        saveDispensations(records) { write(keys.dispensations, records); },
        findItem(name, location) {
            return this.getItems().find(item => (normalize(item.name) === normalize(name) || normalize(item.id) === normalize(name))
                && (!location || normalize(item.location) === normalize(location)));
        },
        addItem(data) {
            const name = required(data.name, 'Item name');
            const location = required(data.location, 'Storage location');
            if (!categories.includes(data.category)) throw new Error('Choose a valid supply category.');
            if (this.findItem(name, location)) throw new Error('This item already exists in that location. Use Update Stock Count instead.');
            const items = this.getItems();
            const item = {id: window.HopeHavenSync.nextId(items, 'WINV'), name, location,
                category: data.category, unit: required(data.unit, 'Unit'),
                onHand: quantity(data.onHand), minThreshold: quantity(data.minThreshold),
                expiry: text(data.expiry), batchNo: text(data.batchNo), updatedBy: 'Nurse Mary', updatedAt: new Date().toISOString()};
            items.unshift(item);
            this.saveItems(items);
            return item;
        },
        updateCount(id, value, reason) {
            const items = this.getItems();
            const item = items.find(item => item.id === id);
            if (!item) throw new Error('This stock item no longer exists.');
            item.onHand = quantity(value);
            item.updatedBy = 'Nurse Mary';
            item.updatedAt = new Date().toISOString();
            item.countReason = required(reason, 'Reason');
            this.saveItems(items);
            return item;
        },
        createRequest(data) {
            const requests = this.getRequisitions();
            const name = required(data.item, 'Item needed');
            const destination = required(data.destination, 'Delivery location');
            if (!categories.includes(data.category)) throw new Error('Choose a valid supply category.');
            if (!['Normal', 'Urgent', 'Critical'].includes(data.priority)) throw new Error('Choose a valid priority.');
            const item = this.findItem(name, destination);
            const request = {id: window.HopeHavenSync.nextId(requests, 'REQ'),
                item: item?.name || name, itemId: item?.id || null, unit: item?.unit || 'units',
                qty: quantity(data.qty, 1), category: data.category, destination,
                priority: data.priority, notes: text(data.notes) || 'Routine stock request',
                requestedBy: text(data.requestedBy) || 'Nurse Mary',
                status: 'Pending', statusLabel: 'Awaiting Director Approval',
                time: new Date().toLocaleString('en-MY'), createdAt: new Date().toISOString()};
            requests.unshift(request);
            this.saveRequisitions(requests);
            return request;
        },
        decideRequest(id, decision, remarks) {
            if (!['Approved', 'Rejected'].includes(decision)) throw new Error('Choose approve or return request.');
            const requests = this.getRequisitions();
            const request = requests.find(request => request.id === id);
            if (!request) throw new Error('This request no longer exists.');
            if (request.status !== 'Pending') throw new Error('This request has already been reviewed.');
            if (decision === 'Rejected') required(remarks, 'Reason for returning the request');
            request.status = decision;
            request.statusLabel = decision === 'Approved' ? 'Approved by Director' : 'Returned by Director';
            request.directorRemarks = text(remarks);
            request.reviewedBy = 'Sister Sarah';
            request.reviewedAt = new Date().toISOString();
            this.saveRequisitions(requests);
            return request;
        },
        receiveRequest(id) {
            const requests = this.getRequisitions();
            const request = requests.find(request => request.id === id);
            if (!request) throw new Error('This request no longer exists.');
            if (request.status === 'Received') return request;
            if (!['Approved', 'Dispatched'].includes(request.status)) throw new Error('The director must approve this request before stock can be received.');
            const items = this.getItems();
            let item = items.find(item => normalize(item.location) === normalize(request.destination)
                && (item.id === request.itemId || normalize(item.name) === normalize(request.item)));
            if (!item) {
                item = {id: window.HopeHavenSync.nextId(items, 'WINV'), name: request.item,
                    category: request.category, location: request.destination, unit: request.unit || 'units',
                    onHand: 0, minThreshold: 0, expiry: '', batchNo: ''};
                items.unshift(item);
            }
            item.receivedRequestIds = item.receivedRequestIds || [];
            // Keep receipt idempotent even if the request-status write fails after stock is saved.
            if (!item.receivedRequestIds.includes(id)) {
                const updated = quantity(item.onHand) + quantity(request.qty, 1);
                item.onHand = quantity(updated);
                item.receivedRequestIds.push(id);
                item.updatedAt = new Date().toISOString();
                this.saveItems(items);
            }
            request.status = 'Received';
            request.statusLabel = 'Received by Staff';
            request.receivedAt = new Date().toISOString();
            request.itemId = item.id;
            this.saveRequisitions(requests);
            return request;
        }
    };
    window.HopeHavenInventory = store;
})();
