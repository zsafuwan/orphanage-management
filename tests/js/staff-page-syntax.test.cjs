const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

test('staff child-welfare inline scripts parse so records and modal buttons can initialize', () => {
    const view = fs.readFileSync('resources/views/portal/staff-child-welfare.blade.php', 'utf8');
    const scripts = [...view.matchAll(/<script(?:\s[^>]*)?>([\s\S]*?)<\/script>/g)];
    assert.ok(scripts.length > 0);
    for (const [index, script] of scripts.entries()) {
        assert.doesNotThrow(() => new vm.Script(script[1], {filename: 'staff-inline-' + index + '.js'}));
    }
});
