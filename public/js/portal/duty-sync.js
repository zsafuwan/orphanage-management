/* Daily duty state survives local source refreshes and follows other open tabs. */
(function () {
    'use strict';
    const date=new Date();
    const day=`${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    const key='hopehaven_duty_'+day;
    let cards, schedule;
    function state() {
        try { const saved=JSON.parse(localStorage.getItem(key)); if(saved && Array.isArray(saved.dismissed)&&Array.isArray(saved.completed)&&Array.isArray(saved.tasks))return saved; } catch(_) {}
        return {dismissed:[],completed:[],tasks:[]};
    }
    function save(change) { const value=state();change(value);window.HopeHavenSync.write(key,JSON.stringify(value)); }
    window.HopeHavenDuty={
        dismiss:id=>save(value=>{if(!value.dismissed.includes(id))value.dismissed.push(id);}),
        complete:id=>save(value=>{if(!value.completed.includes(id))value.completed.push(id);}),
        add:task=>save(value=>value.tasks.push(task))
    };
    function render() {
        const cardBox=document.getElementById('duty_cards_container'), list=document.getElementById('routine_schedule_list');
        if(!cardBox||!list)return;
        const value=state(), escape=window.HopeHavenSync.escape;
        cardBox.innerHTML=cards;
        value.dismissed.forEach(id=>document.getElementById(id)?.remove());
        list.innerHTML=schedule+value.tasks.map(task=>`<div class="staff-schedule-item upcoming" id="${escape(task.id)}"><div class="sched-time">${escape(task.time)}</div><div class="sched-content"><div class="sched-title">${escape(task.title)}</div><div class="sched-detail">Added by Nurse Mary · Morning Shift</div></div><button class="sched-status-badge pending" data-complete-duty="${escape(task.id)}">Pending</button></div>`).join('');
        value.completed.forEach(id=>{
            const task=document.getElementById(id);if(!task)return;
            task.classList.remove('in-progress','upcoming');task.classList.add('completed');
            const button=task.querySelector('.sched-status-badge');
            if(button){button.classList.remove('progress','pending');button.classList.add('done');button.textContent='Completed';button.removeAttribute('onclick');button.disabled=true;}
        });
        const categories={all:'All',clinical:'Health',medication:'Meds',supplies:'Supplies'};
        document.querySelectorAll('[data-alert-filter]').forEach(button=>{const category=button.dataset.alertFilter;const count=cardBox.querySelectorAll(category==='all'?'.duty-alert-card':`[data-category="${category}"]`).length;button.textContent=`${categories[category]} (${count})`;});
        const active=document.querySelector('[data-alert-filter].active');
        cardBox.querySelectorAll('.duty-alert-card').forEach(card=>{card.style.display=!active||active.dataset.alertFilter==='all'||card.dataset.category===active.dataset.alertFilter?'':'none';});
        window.updateDutyActiveCount?.();
    }
    document.addEventListener('click',event=>{
        const button=event.target.closest('[data-complete-duty]');if(button)window.markTaskDone(button.dataset.completeDuty);
    });
    document.addEventListener('DOMContentLoaded',()=>{
        const cardBox=document.getElementById('duty_cards_container'),list=document.getElementById('routine_schedule_list');
        if(!cardBox||!list)return;
        cards=cardBox.innerHTML;schedule=list.innerHTML;
        render();window.HopeHavenSync.subscribe(key,render);
    });
})();
