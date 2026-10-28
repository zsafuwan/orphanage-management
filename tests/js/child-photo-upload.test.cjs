const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup() {
    const handlers = {};
    const input = {value: '', files: [], validity: '', matches: () => true,
        setCustomValidity(value) { this.validity = value; }, reportValidity() { return !this.validity; }};
    const preview = {hidden: true, removeAttribute() { delete this.src; }};
    const remove = {hidden: true};
    const message = {textContent: ''};
    const nodes = {'input': input, 'img': preview, '.child-photo-remove': remove, '.child-photo-message': message};
    const widget = {querySelector: selector => nodes[selector], classList: {add() {}, remove() {}}};
    input.closest = () => widget;
    const form = {querySelector: selector => selector === '[data-child-photo]' ? widget : input,
        querySelectorAll: () => [widget]};
    const canvas = {getContext: () => ({fillRect() {}, drawImage() {}}), toDataURL: () => 'data:image/jpeg;base64,preview'};
    const readers = [];
    const context = {window: {}, document: {addEventListener: (name, handler) => { handlers[name] = handler; },
        createElement: () => canvas},
        FileReader: class {constructor() {readers.push(this);} readAsDataURL() {}},
        Image: class {naturalWidth = 1600; naturalHeight = 800;
            set src(value) { this.onload(); }}
    };
    vm.createContext(context);
    vm.runInContext(fs.readFileSync('public/js/portal/child-photo-upload.js', 'utf8'), context);
    return {input, preview, remove, message, form, canvas, readers, handlers, api: context.window.HopeHavenChildPhoto};
}

test('admission photo is resized, previewed and available to save', async () => {
    const s = setup();
    s.input.files = [{type: 'image/png', size: 2000}];
    const pending = s.handlers.change({target: s.input});
    assert.equal(s.api.ready(s.form), false);
    s.readers[0].result = 'data:image/png;base64,input';
    s.readers[0].onload();
    await pending;
    assert.equal(s.canvas.width, 400);
    assert.equal(s.canvas.height, 200);
    assert.equal(s.preview.hidden, false);
    assert.equal(s.api.get(s.form), 'data:image/jpeg;base64,preview');
    assert.equal(s.api.ready(s.form), true);
});

test('unsupported and oversized files are rejected without reading them', async () => {
    for (const file of [{type: 'image/svg+xml', size: 12}, {type: 'image/jpeg', size: 6 * 1024 * 1024}]) {
        const s = setup();
        s.input.files = [file];
        await s.handlers.change({target: s.input});
        assert.equal(s.readers.length, 0);
        assert.equal(s.api.ready(s.form), false);
        assert.equal(s.api.get(s.form), '');
    }
});

test('reset cancels an in-progress photo so it cannot leak into the next admission', async () => {
    const s = setup();
    s.input.files = [{type: 'image/jpeg', size: 1000}];
    const pending = s.handlers.change({target: s.input});
    s.handlers.reset({target: s.form});
    s.readers[0].result = 'data:image/jpeg;base64,input';
    s.readers[0].onload();
    await pending;
    assert.equal(s.api.get(s.form), '');
    assert.equal(s.preview.hidden, true);
    assert.equal(s.api.ready(s.form), true);
    assert.equal(s.message.textContent, '');
});
