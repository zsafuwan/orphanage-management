const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup() {
    const data = new Map();
    const events = {};
    const localStorage = {
        getItem: key => data.get(key) ?? null,
        setItem: (key, value) => data.set(key, String(value)),
    };
    const document = {hidden:false, activeElement:null, addEventListener(){}, querySelectorAll(){return [];}};
    const context = vm.createContext({document, localStorage, console, queueMicrotask, Date, URLSearchParams,
        addEventListener: (name, callback) => {events[name]=callback;},
        $: () => ({ready(){}}),
    });
    context.window = context;
    for(const file of ['live-sync.js','child-welfare.js','welfare-store.js','duty-sync.js']) {
        vm.runInContext(fs.readFileSync('public/js/portal/'+file,'utf8'),context);
    }
    return {context,localStorage,events,document};
}
const flush = () => new Promise(resolve => queueMicrotask(resolve));

test('same-page writes notify subscribed views once per batch',async()=>{
    const {context}=setup(); let count=0;
    context.HopeHavenSync.subscribe(['children','welfare'],()=>count++);
    context.HopeHavenSync.write('children','[]');
    context.HopeHavenSync.write('welfare','[]');
    await flush(); assert.equal(count,1);
});
test('cross-tab writes and returning to a suspended page update the view',async()=>{
    const {context,localStorage,events}=setup(); let count=0;
    context.HopeHavenSync.subscribe('children',()=>count++);
    localStorage.setItem('children','[1]');
    events.storage({storageArea:localStorage,key:'children'});
    await flush(); assert.equal(count,1);
    localStorage.setItem('children','[1,2]'); events.focus();
    await flush(); assert.equal(count,2);
});
test('refresh preserves a selected child, input text and cursor position',()=>{
    const {context,document}=setup();
    const field={id:'notes',type:'text',tagName:'INPUT',value:'unfinished note',checked:false,selectionStart:4,selectionEnd:4,focus(){},setSelectionRange(start,end){this.selectionStart=start;this.selectionEnd=end;}};
    document.activeElement=field; document.querySelectorAll=()=>[field]; document.getElementById=()=>field;
    context.HopeHavenSync.refreshView(()=>{field.value='';field.selectionStart=0;});
    assert.equal(field.value,'unfinished note'); assert.equal(field.selectionStart,4);
});
test('IDs continue the highest sequence instead of colliding after deletion',()=>{
    const {context}=setup(); const year=new Date().getFullYear();
    assert.equal(context.HopeHavenSync.nextId([{id:`WR-${year}-045`},{id:`WR-${year}-003`}],'WR'),`WR-${year}-046`);
});
test('an intentionally empty registry remains empty',()=>{
    const {context,localStorage}=setup();
    localStorage.setItem('hopehaven_children_registry_v2','[]');
    assert.equal(context.HopeHavenChildStore.getAll().length,0);
});
test('infants have age zero and future birth dates are rejected',()=>{
    const {context}=setup();
    assert.equal(context.HopeHavenChildStore.calculateAge(new Date().toISOString().slice(0,10)),0);
    assert.throws(()=>context.HopeHavenChildStore.add({name:'Test',dob:'2999-01-01'}),/Date of birth/);
});
test('medical fields can be cleared rather than silently keeping stale values',()=>{
    const {context}=setup(); const store=context.HopeHavenChildStore;
    const child=store.getAll()[0];
    store.updateMedicalCondition(child.id,{allergies:'',medications:'',nextClinicDate:'',medicalNotes:''});
    const updated=store.getById(child.id);
    assert.equal(updated.basicMedical.allergies,''); assert.equal(updated.basicMedical.nextClinicDate,'');
});
test('failed storage writes do not announce a successful update',async()=>{
    const {context,localStorage}=setup(); let updates=0;
    context.HopeHavenSync.subscribe('children',()=>updates++);
    localStorage.setItem=()=>{throw new Error('quota');};
    assert.throws(()=>context.HopeHavenSync.write('children','[]'),/quota/);
    await flush(); assert.equal(updates,0);
});
test('duty completions and dismissals are idempotent',()=>{
    const {context,localStorage}=setup();
    context.HopeHavenDuty.complete('task_item_4');context.HopeHavenDuty.complete('task_item_4');
    context.HopeHavenDuty.dismiss('clinical');context.HopeHavenDuty.dismiss('clinical');
    const date=new Date(), day=`${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    const saved=JSON.parse(localStorage.getItem('hopehaven_duty_'+day));
    assert.deepEqual(saved.completed,['task_item_4']);assert.deepEqual(saved.dismissed,['clinical']);
});
test('text entered by users is escaped before HTML rendering',()=>{
    const {context}=setup();
    assert.equal(context.HopeHavenSync.escape('<b>Mary & Jane</b>'),'&lt;b&gt;Mary &amp; Jane&lt;/b&gt;');
    const record=context.HopeHavenSync.escapeRecord({name:'<img onerror="bad">',age:0});
    assert.equal(record.age,0);assert.equal(record.name,'&lt;img onerror=&quot;bad&quot;&gt;');
});
