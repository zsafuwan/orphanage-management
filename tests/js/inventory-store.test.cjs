const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup() {
    const data = new Map();
    const localStorage = {getItem: key => data.get(key) ?? null, setItem: (key, value) => data.set(key, String(value))};
    const context = vm.createContext({localStorage, console, queueMicrotask, Date,
        document: {addEventListener() {}, querySelectorAll() {return [];}}, addEventListener() {}});
    context.window = context;
    for (const file of ['live-sync.js', 'inventory-store.js']) vm.runInContext(fs.readFileSync('public/js/portal/' + file, 'utf8'), context);
    return {store: context.HopeHavenInventory, sync: context.HopeHavenSync, data, localStorage};
}
function requestData(store, overrides = {}) {
    const item = store.getItems()[0];
    return {item: item.name, qty: 3, destination: item.location, category: item.category, priority: 'Normal', notes: 'Restock for next week', ...overrides};
}

test('staff and director read existing inventory keys without replacing saved records', () => {
    const {store, data} = setup();
    data.set(store.keys.items, JSON.stringify([{id: 'KEEP', name: 'Saved stock', onHand: 9}]));
    assert.equal(store.getItems()[0].id, 'KEEP');
    data.set(store.keys.requests, '[]');
    assert.equal(store.getRequisitions().length, 0);
});
test('staff can add an item with an accurate stock count and unit', () => {
    const {store} = setup();
    const added = store.addItem({name: 'Bandages', category: 'Medical', location: 'New room', unit: 'boxes', onHand: '7', minThreshold: '2'});
    assert.equal(store.findItem('bandages', 'New room').onHand, 7);
    assert.equal(added.unit, 'boxes');
    assert.throws(() => store.addItem({...added}), /already exists/);
    assert.equal(store.updateCount(added.id, 0, 'Physical count').onHand, 0);
    assert.throws(() => store.updateCount(added.id, -1, 'Physical count'), /zero or more/);
    assert.throws(() => store.updateCount(added.id, 1.5, 'Physical count'), /whole stock count/);
});
test('a staff request waits for director approval and approval does not increase stock', () => {
    const {store} = setup();
    const initial = store.getItems()[0].onHand;
    const request = store.createRequest(requestData(store));
    assert.equal(request.status, 'Pending');
    assert.throws(() => store.receiveRequest(request.id), /director must approve/);
    store.decideRequest(request.id, 'Approved', 'Approved for next week');
    assert.equal(store.getItems()[0].onHand, initial);
    assert.throws(() => store.decideRequest(request.id, 'Approved', ''), /already been reviewed/);
    assert.equal(store.getRequisitions().find(r => r.id === request.id).directorRemarks, 'Approved for next week');
});
test('receiving approved supplies adds the quantity exactly once', () => {
    const {store} = setup();
    const original = store.getItems()[0];
    const request = store.createRequest(requestData(store));
    store.decideRequest(request.id, 'Approved', '');
    store.receiveRequest(request.id);
    store.receiveRequest(request.id);
    assert.equal(store.getItems().find(i => i.id === original.id).onHand, original.onHand + 3);
    assert.equal(store.getRequisitions().find(r => r.id === request.id).status, 'Received');
});
test('returned requests require feedback and cannot add stock', () => {
    const {store} = setup();
    const request = store.createRequest(requestData(store));
    assert.throws(() => store.decideRequest(request.id, 'Rejected', '  '), /Reason/);
    store.decideRequest(request.id, 'Rejected', 'Please check quantity');
    assert.equal(store.getRequisitions().find(r => r.id === request.id).directorRemarks, 'Please check quantity');
    assert.throws(() => store.receiveRequest(request.id), /director must approve/);
});
test('a new requested item is created at its delivery location only on receipt', () => {
    const {store} = setup();
    const request = store.createRequest(requestData(store, {item: 'New supplies', destination: 'New cupboard', qty: 5}));
    store.decideRequest(request.id, 'Approved', '');
    assert.equal(store.findItem('New supplies', 'New cupboard'), undefined);
    store.receiveRequest(request.id);
    assert.equal(store.findItem('New supplies', 'New cupboard').onHand, 5);
});
test('retrying after a failed request-status save does not double stock', () => {
    const {store, localStorage} = setup();
    const original = store.getItems()[0];
    const request = store.createRequest(requestData(store));
    store.decideRequest(request.id, 'Approved', '');
    const write = localStorage.setItem;
    localStorage.setItem = (key, value) => {if (key === store.keys.requests) throw Error('Storage full'); write(key, value);};
    assert.throws(() => store.receiveRequest(request.id), /Storage full/);
    localStorage.setItem = write;
    store.receiveRequest(request.id);
    assert.equal(store.getItems().find(i => i.id === original.id).onHand, original.onHand + 3);
});
test('new requests reject fractional, empty and non-positive quantities', () => {
    const {store} = setup();
    for (const qty of ['', 0, -2, 2.5]) assert.throws(() => store.createRequest(requestData(store, {qty})), /positive whole/);
});
test('stock and request changes notify subscribed views', async () => {
    const {store, sync} = setup();
    let changes = 0;
    sync.subscribe([store.keys.items, store.keys.requests], () => changes++);
    const request = store.createRequest(requestData(store));
    await new Promise(resolve => queueMicrotask(resolve));
    assert.equal(changes, 1);
    store.decideRequest(request.id, 'Approved', '');
    await new Promise(resolve => queueMicrotask(resolve));
    assert.equal(changes, 2);
});
