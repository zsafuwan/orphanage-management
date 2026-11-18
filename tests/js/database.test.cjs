const test = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');

test('database registry reloads persisted records without seeds, keeps zero scores and never writes browser storage', async () => {
    const data = {
        child:[{Child_Id:42,Child_Name:'Test child',Child_Date_Of_Birth:'2016-01-01',Child_Gender:'Female',Orphanage_Id:7,Child_Photo_Path:'child-photos/example.png'}],
        orphanage:[{Orphanage_Id:7,Orphanage_Name:'Test home'}], organization:[],
        education:[{Education_Id:3,Child_Id:42,Academic_Year:'2026',Child_Result:'0.00',Child_Attendance:350}],
        medical:[{Medical_Id:4,Child_Id:42,Medical_Diagnosis:'Review',Medical_Treatment:null,Medical_Record_Date:'2026-11-17'}],
        appointment:[],followup:[],subject:[{Subject_Id:5,Subject_Name:'Math'}],
        'subject-result':[{Education_Id:3,Subject_Id:5,Subject_Score:'0.00',Subject_Grade:'D'}]
    };
    const requests = [];
    const window = {HopeHavenChildStore:{calculateAge:()=>10}};
    const document = {readyState:'loading', querySelector:selector=>selector === 'meta[name="csrf-token"]' ? {content:'test-token'} : null, getElementById:()=>null, addEventListener:()=>{}, dispatchEvent:()=>{}};
    vm.runInNewContext(fs.readFileSync('public/js/portal/database.js','utf8'), {
        window,document,Event,Date,console,FormData,alert:()=>{},
        fetch:async (url, options) => {requests.push({url,options});return {ok:true,json:async()=>data};},
        localStorage:{getItem:()=>{throw Error('No local reads');},setItem:()=>{throw Error('No local writes');}}
    });
    await window.HopeHavenDatabase.ready;
    const child = window.HopeHavenChildStore.getById('42');
    assert.equal(child.dorm,'Test home');
    assert.equal(child.avatar,'/care-data/child/42/photo?v=example.png');
    assert.equal(child.academic.overallScore,0);
    assert.equal(child.academic.attendance,350);
    assert.equal(child.academic.paperResults[0].subjects[0].score,0);
    assert.equal(window.HopeHavenChildStore.getAll().length,1);
    assert.throws(()=>window.HopeHavenChildStore.save([]),/connected care record form/);
    await window.HopeHavenDatabase.save('medical',{Child_Id:42});
    assert.equal(requests[1].options.method,'POST');
    assert.equal(requests[1].options.headers['X-CSRF-TOKEN'],'test-token');
    assert.equal(requests[1].url,'/care-data/medical');
    const multipart = new FormData();
    multipart.append('Child_Name','Upload test');
    await window.HopeHavenDatabase.save('intake',multipart);
    assert.equal(requests[2].options.body,multipart);
    assert.equal(requests[2].options.headers['Content-Type'],undefined);
});
