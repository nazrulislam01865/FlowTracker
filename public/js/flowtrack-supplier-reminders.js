/* FlowTracker Order reminders. Stable workflow action keys, one active template per event. */
(() => {
    'use strict';
    // Livewire navigation can execute a page script again. Reuse the initialized
    // controller instead of overwriting its window handlers with empty state.
    if (window.__flowtrackSupplierReminderController) {
        window.__flowtrackSupplierReminderController.initialize();
        return;
    }
    let root, initializedRoot = null, bootstrap, templates = [], events = [], currentId = null, config = {}, urls;
    const sectionNames = ['greeting','order_summary','confirmation','artwork','action','support','internal'];
    const sample = {recipient_name:'Supplier A',supplier_name:'Supplier A',order_number:'FT-10245',product_name:'Promotional Gift Box',quantity:'250',artwork_confirmed_date:'08 Oct 2026',completed_date:'08 Oct 2026',event_name:'Artwork Confirmed'};
    const $ = sel => root?.querySelector(sel);
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const merge = v => String(v ?? '').replace(/\{\{([a-z_]+)\}\}/g, (_,k) => sample[k] ?? '');
    const record = () => templates.find(t => t.id === currentId) ?? null;
    const artworkKey='order_task:ART_INTERNAL_REVIEW:confirm';
    const eventKey=t => t?.config?.event_key || artworkKey;
    const eventOption=key => events.find(e=>e.key===key) || {key,stage:'Unavailable stage',task:'Workflow task',action:'Completed',module:'Order'};
    const eventText=e => e.stage+' → '+e.task+' → '+e.action;
    const recipientTypes = {
        supplier:'Supplier assigned to order product', owner:'Order owner',
        coordinator:'Order coordinator',assignee:'Completed task assignee',
        client:'Client email',client_contact:'Client primary contact',user:'Specific FlowTracker user'
    };
    const recipientLabel = cfg => cfg?.recipient_type==='user'
        ? (bootstrap?.recipientUsers||[]).find(u=>Number(u.id)===Number(cfg.recipient_user_id))?.name||'Specific FlowTracker user'
        : recipientTypes[cfg?.recipient_type||'supplier']||'Assigned supplier';
    const updateRecipient = () => {
        const type=$('#recipientType')?.value||'supplier';
        const selector=$('#recipientUserRow');if(selector)selector.hidden=type!=='user';
        const hints={
          supplier:'Only the supplier selected for each order product receives the message.',
          owner:'Uses the active order owner assigned in FlowTracker.',
          coordinator:'Uses the active coordinator assigned to this order.',
          assignee:'Uses the active assignee of the completed task.',
          client:'Uses the email stored on the client record linked to this order.',
          client_contact:'Uses the primary contact of the client linked to this order.',
          user:'Send to the selected active FlowTracker user only for orders they are authorized to view.'
        };
        if($('#recipientSourceHint'))$('#recipientSourceHint').textContent=hints[type];
        if($('#recipientEmailSource'))$('#recipientEmailSource').textContent='The current email is resolved securely when the task finishes. Missing or invalid emails are skipped and recorded.';
        if($('#ruleRecipientSummary'))$('#ruleRecipientSummary').textContent=type==='user'
          ? ((bootstrap?.recipientUsers||[]).find(u=>Number(u.id)===Number($('#recipientUserId')?.value))?.name||'Select a user')
          : recipientTypes[type];
        sample.recipient_name=type==='supplier'?'Supplier A':type==='client'?'Client contact':type==='client_contact'?'Primary client contact':type==='user'
            ? (bootstrap?.recipientUsers||[]).find(u=>Number(u.id)===Number($('#recipientUserId')?.value))?.name||'FlowTracker user'
            : type==='owner'?'Order owner':type==='coordinator'?'Order coordinator':'Task assignee';
        renderEmail();
    };
    const stages=()=>[...new Set(events.map(e=>e.stage))];
    const fillStages=()=>{
        const picker=$('#createReminderStage');if(!picker)return;
        picker.innerHTML=stages().map(s=>`<option value="${esc(s)}">${esc(s)}</option>`).join('');
        fillEventChoices();
    };
    const fillEventChoices=()=>{
        const picker=$('#createReminderEvent');if(!picker)return;
        const values=events.filter(e=>e.stage===$('#createReminderStage')?.value);
        picker.innerHTML=values.map(e=>`<option value="${esc(e.key)}">${esc(e.task+' → '+e.action)}</option>`).join('');
    };
    const notice = (message,error=false) => {
        const box=$('#toast'); if (!box) return;
        box.textContent=message;box.style.background=error?'#a23740':'var(--ft-theme-primary)';box.style.display='block';
        clearTimeout(notice.timer);
        notice.timer=setTimeout(()=>{if(box.isConnected)box.style.display='none';},5000);
    };
    const request = async (url,method='GET',payload=null) => {
        const response=await fetch(url,{method,credentials:'same-origin',headers:{Accept:'application/json',...(method!=='GET'?{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content??''}:{})},...(payload!==null?{body:JSON.stringify(payload)}:{})});
        const data=await response.json().catch(()=>({}));
        if(!response.ok)throw Error(Object.values(data.errors??{}).flat().join(' ')||data.message||'The request could not be completed.');
        return data;
    };
    const busy = async action => {
        const buttons=[...root.querySelectorAll('button')].filter(el=>/create draft|save draft|publish|send test email|pause reminder/i.test(el.textContent));
        buttons.forEach(b=>b.disabled=true);
        try{await action();}catch(e){notice(e.message||'Operation failed.',true);}finally{buttons.forEach(b=>b.disabled=false);}
    };
    const stage = () => $('#reminderStageFilter')?.value || 'All stages';
    const statusOf = t => t.active?'Published':t.published?'Paused':'Draft';
    const selectedTiming = () => root.querySelector('input[name="timing"]:checked')?.value === 'delayed';
    const updateTiming = () => {
        const delayed=selectedTiming(), row=$('#reminderDelayRow');
        if(row)row.hidden=!delayed;
        root.querySelectorAll('.radioChoice').forEach(el=>el.classList.toggle('active',!!el.querySelector('input:checked')));
        const summary=$('#ruleTimingSummary');if(summary)summary.textContent=delayed?'After '+($('#delayMinutes')?.value||'60')+' minutes':'Immediately after success';
    };
    const read = () => {
        const data={...config};
        for(const key of ['subject','preheader','heading','cta','message'])data[key]=$('#'+key)?.value??'';
        data.delay_minutes=selectedTiming()?Number($('#delayMinutes')?.value||0):0;
        data.event_key=config.event_key||artworkKey;
        data.missing_email='skip';
        data.recipient_type=$('#recipientType')?.value||'supplier';
        data.recipient_user_id=data.recipient_type==='user'?Number($('#recipientUserId')?.value||0)||null:null;
        data.sections={};for(const key of sectionNames)data.sections[key]=key!=='internal'&&!!$('[data-section="'+key+'"]')?.checked;
        return data;
    };
    const hydrate = () => {
        const t=record();if(!t)return;
        config=structuredClone(t.config);
        config.event_key ||= artworkKey;
        const event=eventOption(config.event_key);
        if($('#ruleSelectedEvent'))$('#ruleSelectedEvent').value=eventText(event);
        if($('#ruleEventSummary'))$('#ruleEventSummary').textContent=event.task+' → '+event.action;
        if($('#ruleStageSummary'))$('#ruleStageSummary').textContent=event.stage;
        if($('#ruleFlowEvent'))$('#ruleFlowEvent').textContent='✓ '+event.task;
        if($('#previewEventValue'))$('#previewEventValue').textContent=eventText(event);
        if($('#testReminderType'))$('#testReminderType').value=t.name;
        sample.event_name=event.key===artworkKey?'Artwork Confirmed':event.task+' Completed';
        root.querySelectorAll('[data-section="artwork"],[data-section="confirmation"]').forEach(el=>{
            if(event.key!==artworkKey)el.checked=false;
            const row=el.closest('.blockrow');if(row)row.hidden=event.key!==artworkKey;
        });
        $('#reminderName').value=t.name;
        $('#recipientType').value=config.recipient_type||'supplier';
        $('#recipientUserId').value=config.recipient_user_id||'';
        updateRecipient();
        for(const key of ['subject','preheader','heading','cta','message']) {const el=$('#'+key);if(el)el.value=config[key]??'';}
        for(const key of sectionNames){const el=$('[data-section="'+key+'"]');if(el)el.checked=key!=='internal'&&!!config.sections?.[key];}
        const delayed=Number(config.delay_minutes)>0;
        root.querySelectorAll('input[name="timing"]').forEach(r=>r.checked=r.value===(delayed?'delayed':'immediate'));
        $('#delayMinutes').value=delayed?String(config.delay_minutes):'60';
        updateTiming();status();renderEmail();
    };
    const status = () => {
        const t=record(), current=t?statusOf(t):'Not configured';
        const active=templates.find(v=>v.active);
        if($('#recipientMetricValue'))$('#recipientMetricValue').textContent=active?recipientLabel(active.config):'Not configured';
        const metric=$('#reminderStateMetric');if(metric)metric.textContent=active?'Published':'Inactive';
        const hint=$('#reminderMetricHelp');if(hint)hint.textContent=active?'Active reminder'+(templates.filter(t=>t.active).length>1?'s':'')+': '+templates.filter(t=>t.active).map(t=>t.name).join(', '):'Publish a reminder to activate it';
        for(const selector of ['#ruleDraftBadge','#editorDraftBadge','#previewStateBadge']){
            const pill=$(selector);if(!pill)continue;
            pill.textContent=t?(t.active?'Published v'+t.version:t.published?'Paused v'+t.version:'Draft'):'Not configured';
            pill.classList.toggle('yellow',!t?.active);
        }
        const badge=$('#previewActiveState');if(badge){badge.textContent=t?.active?'Active':t?.published?'Paused':'Not activated';badge.classList.toggle('grey',!t?.active);}
        const mail=$('#orderEmailState');if(mail){mail.textContent=bootstrap.orderEmailEnabled?'Enabled':'Disabled';mail.classList.toggle('grey',!bootstrap.orderEmailEnabled);}
        const name=t?.name||'No reminder selected';
        if($('#editorReminderName'))$('#editorReminderName').textContent=name;
        if($('#previewReminderName'))$('#previewReminderName').textContent=name;
    };
    const showCreate = (show=true) => {
        const form=$('#createReminderForm');if(!form)return;
        form.hidden=!show;
        if(show){$('#createReminderName').value='Supplier · '+(events[0]?.task||'Workflow reminder');fillStages();$('#createReminderName').focus();}
    };
    const renderList = () => {
        const tbody=$('#reminderRows');if(!tbody)return;
        const term=($('#reminderSearch')?.value??'').trim().toLowerCase();
        const filter=$('#reminderStatusFilter')?.value??'All statuses';
        const selectedStage=stage();
        const filtered=templates.filter(t=>{
            const e=eventOption(eventKey(t));
            return (!term||(t.name+' '+eventText(e)+' '+recipientLabel(t.config)).toLowerCase().includes(term))
                && (filter==='All statuses'||statusOf(t)===filter)
                && (selectedStage==='All stages'||e.stage===selectedStage);
        });
        tbody.innerHTML=filtered.length?filtered.map(t=>{
            const state=statusOf(t),delay=Number(t.config.delay_minutes)||0;
            const e=eventOption(eventKey(t));
            return `<tr${t.id===currentId?' class="rowSelected"':''}><td><div class="rowTitle"><div class="rowIcon">✉</div><div><b>${esc(t.name)}</b><small>${esc(e.task)} notification to ${esc(recipientLabel(t.config))}</small></div></div></td>
                <td>${esc(e.stage+' → '+e.task)}<small>${esc(e.action)}</small></td>
                <td>${esc(recipientLabel(t.config))}<small>Email resolved when task completes</small></td>
                <td>${delay?'After '+esc(delay)+' minutes':'Immediately'}<small>After action completes</small></td>
                <td><span class="pill ${state==='Draft'?'yellow':state==='Paused'?'grey':''}">${state}</span></td>
                <td><div class="actions"><button class="btn" type="button" data-action="edit" data-id="${t.id}">Configure</button><button class="btn" type="button" data-action="preview" data-id="${t.id}">Preview</button>${t.active?`<button class="btn" type="button" data-action="pause" data-id="${t.id}">Pause</button>`:''}</div></td></tr>`;
        }).join(''):'<tr><td colspan="6" class="emptyReminders">No reminders match these filters. Use Create Reminder to add a draft.</td></tr>';
        if($('#reminderListFooter'))$('#reminderListFooter').textContent='Showing '+filtered.length+' of '+templates.length+' configured reminder(s)';
        status();
    };
    const selectReminder=(id,page='rule')=>{
        const t=templates.find(x=>x.id===Number(id));if(!t){notice('Reminder could not be found.',true);return;}
        currentId=t.id;hydrate();renderList();go(page);
    };
    const createReminder = event => {
        event?.preventDefault();
        busy(async()=>{
            const name=$('#createReminderName')?.value?.trim();if(!name){notice('Enter a reminder name.',true);return;}
            const event_key=$('#createReminderEvent')?.value;
            if(!event_key){notice('Select a configured workflow event.',true);return;}
            const result=await request(urls.create,'POST',{name,event_key});
            templates.push(result.reminder);showCreate(false);selectReminder(result.reminder.id,'rule');notice(result.message);
        });
        return false;
    };
    const save = publish => {
        if(!record()){notice('Create or select a reminder first.',true);go('list');return;}
        busy(async()=>{
            const name=$('#reminderName')?.value?.trim();if(!name){notice('Enter a reminder name.',true);go('rule');return;}
            if($('#recipientType')?.value==='user'&&!$('#recipientUserId')?.value){notice('Select a FlowTracker user before saving.',true);go('rule');return;}
            const result=await request(urls.save,'POST',{id:currentId,name,...read(),publish});
            if(publish)templates.forEach(t=>{if(eventKey(t)===config.event_key)t.active=false;});
            const index=templates.findIndex(t=>t.id===currentId);
            templates[index]=result.reminder;
            config=structuredClone(result.reminder.config);
            status();renderList();notice(result.message);
        });
    };
    const pauseReminder = id => busy(async()=>{
        const result=await request(urls.pause,'POST',{id:Number(id)});
        templates.forEach(t=>{if(t.id===Number(id))t.active=false;});
        renderList();notice(result.message);
    });
    const renderEmail = () => {
        if(!root)return;
        const data=record()?read():bootstrap.defaults, visible=data.sections||{};
        const isArtwork=(data.event_key||artworkKey)===artworkKey;
        const company=esc(root.dataset.brandName||'STEP PROMO');
        const logo=root.dataset.brandLogo?'<img src="'+esc(root.dataset.brandLogo)+'" alt="'+company+'" loading="lazy">':company;
        const row=(label,value)=>'<div class="kv"><span>'+label+'</span><b>'+esc(value)+'</b></div>';
        const html=`<div class="email"><div class="emailHead">${logo}<span class="headRight">${esc(sample.event_name)}</span></div><div class="emailBody"><span class="emailKicker">Order reminder</span><h2>${esc(merge(data.heading))}</h2>
          ${visible.greeting?'<p>Hi '+esc(sample.recipient_name)+',</p>':''}
          <p>${esc(merge(data.message)).replace(/\n/g,'<br>')}</p>
          ${visible.order_summary?'<div class="emailCard">'+row('Order number',sample.order_number)+row('Product',sample.product_name)+row('Quantity',sample.quantity)+'</div>':''}
          ${(visible.confirmation&&isArtwork)?'<div class="emailBadge">✓ Artwork Confirmed · '+esc(sample.artwork_confirmed_date)+'</div>':''}
          ${(visible.artwork&&isArtwork)?'<p style="margin-top:12px">Confirmed artwork is available through the authorized order tracking process.</p>':''}
          ${visible.action?'<p style="margin-top:14px"><span class="emailButton">'+esc(data.cta)+'</span></p>':''}
          ${visible.support?'<div class="emailFoot">Need help? Contact your STEP PROMO order team.</div>':''}</div></div>`;
        const editor=$('#editorEmail');if(editor)editor.innerHTML=html;
        const preview=$('#previewEmail');if(preview&&preview.querySelector('iframe')===null)preview.innerHTML=html;
    };
    const sendTest=()=>{
        if(!record()){notice('Create or select a reminder first.',true);return;}
        busy(async()=>{
            const recipient=$('#testRecipient')?.value.trim();if(!recipient){notice('Enter a test recipient email address.',true);return;}
            const data=read();if(!$('#testArtworkLink')?.checked){data.sections.artwork=false;data.sections.action=false;}
            const result=await request(urls.test,'POST',{id:currentId,recipient,published:false,config:data});notice(result.message);
        });
    };
    const loadHistory=async (nextUrl=null) => {
        const body=$('#historyRows');if(!body)return;
        const qs=new URLSearchParams();const q=$('#historySearch')?.value.trim();if(q)qs.set('q',q);
        const event=$('#historyStageFilter')?.value;if(event&&event!=='All events')qs.set('event_key',event);
        const status=$('#historyStatus')?.value;if(['Sent','Skipped','Failed','Queued','Sending'].includes(status))qs.set('status',status.toLowerCase());
        body.innerHTML='<tr><td colspan="6">Loading reminder history…</td></tr>';
        try{
            const result=await request(nextUrl||urls.history+'?'+qs.toString());
            body.innerHTML=result.data?.length?result.data.map(r=>`<tr><td><b>${esc(r.job_number||'Order '+r.id)}</b><small>${esc(r.product)}</small></td><td>${esc(r.supplier_name||'Recipient unavailable')}<small>${esc(r.recipient_email||'Recipient email is unavailable')}</small></td><td>${esc(r.task_title||'Task completed')}</td><td><span class="pill ${r.status==='failed'?'red':r.status==='skipped'?'yellow':r.status==='sent'?'':'grey'}">${esc(r.status)}</span></td><td>${esc(r.sent_at||r.created_at)}</td><td>${esc(r.reason||(r.status==='sent'?'Accepted by email provider':'Awaiting delivery'))}</td></tr>`).join(''):'<tr><td colspan="6">No delivery records match these filters.</td></tr>';
            const foot=$('#historyFooter');if(foot){foot.textContent='Showing '+(result.data?.length||0)+' record(s)';if(result.next_page_url){const more=document.createElement('button');more.className='btn';more.textContent='Next 20 →';more.style.marginLeft='12px';more.addEventListener('click',()=>loadHistory(result.next_page_url));foot.appendChild(more);}}
        }catch(e){body.innerHTML='<tr><td colspan="6">'+esc(e.message)+'</td></tr>';}
    };
    const fetchPreview=async()=>{
        const target=$('#previewEmail');if(!target||!record())return;
        try{const result=await request(urls.preview,'POST',read());const frame=document.createElement('iframe');frame.className='reminder-preview';frame.setAttribute('sandbox','allow-popups');frame.title='Actual reminder email preview';frame.srcdoc=result.html;target.replaceChildren(frame);}
        catch(e){notice(e.message||'Unable to render email preview.',true);}
    };
    const go=id=>{
        if(!root)return;
        if(!['list','rule','editor','preview','history'].includes(id))id='list';
        if(!record()&&!['list','history'].includes(id)){notice('Create a reminder first.',true);id='list';}
        root.querySelectorAll('.view').forEach(el=>el.classList.toggle('active',el.id==='view-'+id));
        root.querySelectorAll('[data-nav]').forEach(el=>el.classList.toggle('active',el.dataset.nav===id));
        const crumb=$('#crumbRest');if(crumb)crumb.textContent=id==='list'?'':' › '+({rule:'Trigger & Recipients',editor:'Email Content',preview:'Preview & Test',history:'Delivery History'}[id]);
        if(location.hash.slice(1)!==id)history.replaceState(null,'','#'+id);
        if(id==='history')loadHistory();if(id==='preview')fetchPreview();
    };
    const insertVar=token=>{const field=$('#message');if(!field)return;field.setRangeText(' '+token,field.selectionStart,field.selectionEnd,'end');field.dispatchEvent(new Event('input'));field.focus();};
    const initialize=()=>{
        root=document.getElementById('flowtrackReminderRoot');
        if(!root||root===initializedRoot)return;
        bootstrap=JSON.parse($('#reminderBootstrap')?.textContent||'{}');
        events=bootstrap.events||[];
        if($('#availableEventMetric'))$('#availableEventMetric').textContent=String(events.length).padStart(2,'0');
        const stagePicker=$('#reminderStageFilter');if(stagePicker)stagePicker.innerHTML='<option>All stages</option>'+stages().map(s=>'<option>'+esc(s)+'</option>').join('');
        fillStages();
        const userPicker=$('#recipientUserId');
        if(userPicker)userPicker.innerHTML='<option value="">Select an active user</option>'+(bootstrap.recipientUsers||[]).map(u=>'<option value="'+Number(u.id)+'">'+esc(u.name)+' ('+esc(u.email)+')</option>').join('');
        const historyStage=$('#historyStageFilter');if(historyStage)historyStage.innerHTML='<option>All events</option>'+events.map(e=>'<option value="'+esc(e.key)+'">'+esc(eventText(e))+'</option>').join('');
        templates=bootstrap.templates||[];currentId=templates.find(t=>t.active)?.id||templates[0]?.id||null;
        config=record()?.config||bootstrap.defaults||{};
        urls={save:root.dataset.saveUrl,create:root.dataset.createUrl,pause:root.dataset.pauseUrl,test:root.dataset.testUrl,preview:root.dataset.previewUrl,history:root.dataset.historyUrl};
        if(record())hydrate();else status();renderList();
        root.querySelectorAll('#view-editor input,#view-editor textarea').forEach(el=>el.addEventListener('input',renderEmail));
        root.querySelectorAll('input[name="timing"]').forEach(el=>el.addEventListener('change',updateTiming));
        $('#createReminderStage')?.addEventListener('change',fillEventChoices);
        $('#createReminderEvent')?.addEventListener('change',()=>{
            const field=$('#createReminderName'),e=eventOption($('#createReminderEvent').value);
            if(field && (field.value==='' || field.value.startsWith('Reminder · ')))field.value='Reminder · '+e.task;
        });
        $('#delayMinutes')?.addEventListener('input',updateTiming);
        $('#recipientType')?.addEventListener('change',updateRecipient);
        $('#recipientUserId')?.addEventListener('change',updateRecipient);
        for(const sel of ['#reminderSearch','#reminderStatusFilter','#reminderStageFilter']) $(sel)?.addEventListener(sel==='#reminderSearch'?'input':'change',renderList);
        root.querySelector('#reminderRows')?.addEventListener('click', e=>{
            const button=e.target.closest('button[data-action]');if(!button)return;
            const id=Number(button.dataset.id);if(button.dataset.action==='pause')pauseReminder(id);else selectReminder(id,button.dataset.action==='preview'?'preview':'rule');
        });
        for(const sel of ['#historyStatus','#historyStageFilter'])$(sel)?.addEventListener('change',()=>loadHistory());
        let timer;$('#historySearch')?.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(()=>loadHistory(),350);});
        window.addEventListener('hashchange',()=>{if(root?.isConnected)go(location.hash.slice(1));});
        go(location.hash.slice(1)||'list');
        initializedRoot=root;
    };
    window.__flowtrackSupplierReminderController={initialize};
    Object.assign(window,{go,notice,renderEmail,insertVar,showCreate,createReminder,saveDraft:()=>save(false),publish:()=>save(true),sendTest});
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initialize,{once:true});else initialize();
    document.addEventListener('livewire:navigated',initialize);
})();
