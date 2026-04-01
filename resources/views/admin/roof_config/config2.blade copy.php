<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <title>Daily Task Planner – MVP Demo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Icons -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://unpkg.com/lucide-static@0.344.0/font/lucide.css" rel="stylesheet">
  <!-- dayjs -->
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/isoWeek.min.js"></script>
  <script>dayjs.extend(window.dayjs_plugin_isoWeek);</script>
  <!-- Interact.js (Drag/Resize) -->
  <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
  <style>
    /* dezente Scrollbars */
    ::-webkit-scrollbar { height: 10px; width: 10px; }
    ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 9999px; }
    .hour-grid::before {
      content:""; position:absolute; inset:0;
      background-image: linear-gradient(to right,
        rgba(0,0,0,0.06) 1px, transparent 1px);
      background-size: calc(100% / 8) 100%;
      pointer-events:none;
    }
    .dragging { opacity: .8; filter: drop-shadow(0 6px 14px rgba(0,0,0,.25)); }
    .dep-line path { stroke: #9ca3af; stroke-width: 2; fill: none; }
    .dep-line--invalid path { stroke: #ef4444; }
    .card-shadow { box-shadow: 0 6px 18px rgba(0,0,0,.15); }
    .resizer-h { position:absolute; top:0; bottom:0; width:8px; cursor:ew-resize; }
    .resizer-h.left { left:-4px; } .resizer-h.right { right:-4px; }
    .cell { position: relative; min-height: 70px; }
    .assignment { position:absolute; top:8px; height:54px; border-radius: 12px; }
    .badge { font-size: .7rem; padding: 2px 6px; border-radius: 8px; }
    .menu { display:none; position:absolute; z-index:50; }
    .assignment:focus-within .menu, .assignment:hover .menu { display:block; }
  </style>
</head>
<body class="bg-slate-50 text-slate-900">
  <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
      <h1 class="text-xl font-bold">Tagesplaner (MVP)</h1>
      <div class="ml-auto flex items-center gap-2">
        <button data-mode="deadline" class="mode-btn px-3 py-2 rounded-xl border border-slate-300 hover:bg-slate-100">Nach Frist</button>
        <button data-mode="taskpack" class="mode-btn px-3 py-2 rounded-xl border border-slate-300 hover:bg-slate-100">Aufgabenpaket</button>
        <button data-mode="availability" class="mode-btn px-3 py-2 rounded-xl border border-slate-300 hover:bg-slate-100">Verfügbarkeit</button>
        <div class="hidden md:flex items-center gap-2 ml-4">
          <label class="text-sm text-slate-600">Zeitraum:</label>
          <input id="fromDate" type="date" class="px-2 py-1 border rounded-lg text-sm">
          <span class="text-slate-400">–</span>
          <input id="toDate" type="date" class="px-2 py-1 border rounded-lg text-sm">
          <button id="applyRange" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white">Anwenden</button>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-4 grid grid-cols-12 gap-4">
    <!-- Left: Panels -->
    <aside class="col-span-12 lg:col-span-4 space-y-4">
      <!-- Deadline Panel -->
      <section id="panel-deadline" class="panel hidden bg-white border border-slate-200 rounded-2xl p-4 card-shadow">
        <h2 class="font-semibold mb-3">Planung nach Frist</h2>
        <div class="grid grid-cols-2 gap-2">
          <label class="text-sm">Fällig bis</label>
          <input id="deadlineDate" type="date" class="px-2 py-1 border rounded-lg">
          <label class="text-sm">Tagesstunden/Person</label>
          <input id="deadlineHpd" type="number" value="8" min="1" max="12" class="px-2 py-1 border rounded-lg">
        </div>
        <div class="mt-3">
          <h3 class="text-sm font-medium mb-2">Tasks auswählen</h3>
          <div id="dlTaskList" class="space-y-2 max-h-44 overflow-auto"></div>
        </div>
        <div class="mt-3 text-sm bg-slate-50 border border-slate-200 rounded-xl p-3" id="dlHint">
          <div>Gesamtstunden: <span id="dlTotal" class="font-semibold">0</span>h</div>
          <div>Arbeitstage: <span id="dlDays" class="font-semibold">0</span></div>
          <div>Erforderliche Kopfzahl: <span id="dlHeadcount" class="font-semibold">–</span></div>
        </div>
        <div class="mt-3">
          <h3 class="text-sm font-medium mb-2">Mitarbeitende auswählen</h3>
          <div id="dlEmpList" class="grid grid-cols-2 gap-2 max-h-28 overflow-auto"></div>
        </div>
        <div class="mt-4 flex gap-2">
          <button id="btnDlCalc" class="px-3 py-2 rounded-xl bg-slate-900 text-white">Berechnen</button>
          <button id="btnDlPlan" class="px-3 py-2 rounded-xl bg-emerald-600 text-white">Auto-verteilen</button>
        </div>
      </section>

      <!-- Task Pack Panel -->
      <section id="panel-taskpack" class="panel hidden bg-white border border-slate-200 rounded-2xl p-4 card-shadow">
        <h2 class="font-semibold mb-3">Aufgabenpaket (ein Tag)</h2>
        <div class="grid grid-cols-2 gap-2">
          <label class="text-sm">Datum</label>
          <input id="tpDate" type="date" class="px-2 py-1 border rounded-lg">
        </div>
        <div class="mt-3">
          <h3 class="text-sm font-medium mb-2">Tasks</h3>
          <div id="tpTaskList" class="space-y-2 max-h-44 overflow-auto"></div>
        </div>
        <div class="mt-3">
          <h3 class="text-sm font-medium mb-2">Mitarbeitende</h3>
          <div id="tpEmpList" class="grid grid-cols-2 gap-2 max-h-28 overflow-auto"></div>
        </div>
        <div class="mt-3 text-sm bg-slate-50 border border-slate-200 rounded-xl p-3">
          <div>Gesamtstunden: <span id="tpTotal" class="font-semibold">0</span>h</div>
          <div>Tageskapazität: <span id="tpCapacity" class="font-semibold">0</span>h</div>
          <div id="tpOver" class="text-amber-600 font-medium hidden">⚠️ Überstunden wahrscheinlich</div>
        </div>
        <div class="mt-4 flex gap-2">
          <button id="btnTpPlan" class="px-3 py-2 rounded-xl bg-emerald-600 text-white">Paket einplanen</button>
          <label class="ml-auto inline-flex items-center gap-2 text-sm">
            <input id="tpSpill" type="checkbox" class="rounded"> Überlauf auf Folgetag
          </label>
        </div>
      </section>

      <!-- Availability Panel -->
      <section id="panel-availability" class="panel hidden bg-white border border-slate-200 rounded-2xl p-4 card-shadow">
        <h2 class="font-semibold mb-3">Planung nach Verfügbarkeit</h2>
        <div class="grid grid-cols-2 gap-2">
          <label class="text-sm">Datum</label>
          <input id="avDate" type="date" class="px-2 py-1 border rounded-lg">
        </div>
        <div class="mt-3 text-sm bg-slate-50 border border-slate-200 rounded-xl p-3">
          <div>Verfügbare MA: <span id="avCount" class="font-semibold">0</span></div>
          <div>Gesamtkapazität: <span id="avCap" class="font-semibold">0</span>h</div>
        </div>
        <div class="mt-3">
          <h3 class="text-sm font-medium mb-2">Ungeplante Tasks</h3>
          <div id="avTaskList" class="space-y-2 max-h-44 overflow-auto"></div>
        </div>
        <div class="mt-4">
          <button id="btnAvFit" class="px-3 py-2 rounded-xl bg-emerald-600 text-white">Auto-Fit</button>
        </div>
      </section>

      <!-- Unassigned bucket -->
      <section class="bg-white border border-slate-200 rounded-2xl p-4 card-shadow">
        <h2 class="font-semibold mb-3">Unzugewiesen</h2>
        <div id="bucket" class="min-h-[120px] grid grid-cols-1 gap-2"></div>
      </section>
    </aside>

    <!-- Right: Timeline -->
    <section class="col-span-12 lg:col-span-8">
      <div class="bg-white border border-slate-200 rounded-2xl p-4 card-shadow">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-semibold">Timeline</h2>
          <div class="text-sm text-slate-500" id="rangeLabel"></div>
        </div>
        <div class="overflow-x-auto" id="timelineWrap">
          <div id="timeline" class="min-w-[900px] relative">
            <!-- Header + Rows werden per JS gerendert -->
            <svg id="depSvg" class="absolute pointer-events-none" style="inset:0; width:100%; height:100%;"></svg>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Drawer -->
  <aside id="drawer" class="fixed right-4 bottom-4 w-[320px] bg-white border border-slate-200 rounded-2xl p-4 card-shadow hidden">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold">Task-Segment</h3>
      <button id="drawerClose" class="text-slate-500 hover:text-slate-900">&times;</button>
    </div>
    <div id="drawerBody" class="text-sm mt-2 space-y-2"></div>
  </aside>

  <script>
    // ----------------------------
    // Fake Data
    // ----------------------------
    const employees = [
      { id: 'e1', name: 'Anna', skills: ['PV','Roof'], maxDay: 8, availability: {} },
      { id: 'e2', name: 'Ben', skills: ['PV','DC'],   maxDay: 8, availability: {} },
      { id: 'e3', name: 'Cem', skills: ['AC'],        maxDay: 8, availability: {} },
      { id: 'e4', name: 'Dana',skills: ['Roof'],      maxDay: 8, availability: {} },
      { id: 'e5', name: 'Emil',skills: ['PV','AC'],   maxDay: 8, availability: {} },
    ];

    const tasks = [
      { id:'t1', job:'PV-Montage Müller', customer:'Müller', name:'Panelmontage', estimated:6, crew:2, skills:['PV','Roof'] },
      { id:'t2', job:'PV-Montage Müller', customer:'Müller', name:'Verkabelung',  estimated:4, crew:1, skills:['DC'] },
      { id:'t3', job:'PV-Montage Müller', customer:'Müller', name:'Inbetriebnahme', estimated:3, crew:1, skills:['AC'] },
      { id:'t4', job:'Wartung Schulze',   customer:'Schulze', name:'Inspektion',   estimated:5, crew:1, skills:['PV'] },
      { id:'t5', job:'Dachcheck Klein',   customer:'Klein',   name:'Dachprüfung',  estimated:4, crew:2, skills:['Roof'] },
      { id:'t6', job:'Anschluss Weber',   customer:'Weber',   name:'Wechselrichter', estimated:3, crew:1, skills:['AC'] },
    ];
    // Dependencies: t1 -> t2 -> t3
    const dependencies = [
      { from:'t1', to:'t2', type:'FS' },
      { from:'t2', to:'t3', type:'FS' },
    ];

    // Assignments = gesplittete Segmente pro Tag/MA
    // { id, taskId, empId|null, date(YYYY-MM-DD), startSlot(0..7), hours(1..8) }
    const assignments = [];

    // Availability Beispiel: Cem am übermorgen nur 3h
    function setAvailability(empId, date, hours) {
      const emp = employees.find(e=>e.id===empId);
      if (!emp.availability) emp.availability = {};
      emp.availability[date] = hours;
    }

    // ----------------------------
    // Zeitfenster
    // ----------------------------
    const fromInput = document.getElementById('fromDate');
    const toInput = document.getElementById('toDate');
    const applyRangeBtn = document.getElementById('applyRange');
    const rangeLabel = document.getElementById('rangeLabel');
    const today = dayjs();
    let rangeFrom = today.startOf('day');
    let rangeTo = today.add(6,'day').startOf('day'); // 7 Tage

    fromInput.value = rangeFrom.format('YYYY-MM-DD');
    toInput.value = rangeTo.format('YYYY-MM-DD');

    applyRangeBtn.addEventListener('click', ()=>{
      rangeFrom = dayjs(fromInput.value);
      rangeTo = dayjs(toInput.value);
      renderTimeline();
    });

    // Beispiel-Availability setzen
    setTimeout(()=>{
      setAvailability('e3', dayjs().add(2,'day').format('YYYY-MM-DD'), 3);
    },0);

    // ----------------------------
    // Panels & UI Listen
    // ----------------------------
    const panels = {
      deadline: document.getElementById('panel-deadline'),
      taskpack: document.getElementById('panel-taskpack'),
      availability: document.getElementById('panel-availability'),
    };
    const modeBtns = document.querySelectorAll('.mode-btn');
    function showPanel(name){
      Object.values(panels).forEach(p=>p.classList.add('hidden'));
      panels[name].classList.remove('hidden');
      modeBtns.forEach(b=>b.classList.toggle('bg-slate-900', b.dataset.mode===name));
      modeBtns.forEach(b=>b.classList.toggle('text-white', b.dataset.mode===name));
    }
    // Default öffnen
    showPanel('deadline');

    modeBtns.forEach(b=>b.addEventListener('click', ()=> showPanel(b.dataset.mode)));

    // Listen rendern
    function renderTaskPickers(listElId, withHours=true){
      const el = document.getElementById(listElId);
      el.innerHTML = '';
      tasks.forEach(t=>{
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2';
        row.innerHTML = `
          <label class="flex items-center gap-2 flex-1">
            <input type="checkbox" data-task="${t.id}" class="rounded">
            <span class="text-sm">${t.name} <span class="text-slate-400">(${t.job})</span>
            <span class="ml-1 badge bg-slate-100 border border-slate-200">Crew ${t.crew}</span></span>
          </label>
          ${withHours ? `<input type="number" min="1" max="16" value="${t.estimated}" data-hours="${t.id}" class="w-20 px-2 py-1 border rounded-lg text-sm">` : ''}
        `;
        el.appendChild(row);
      });
    }
    function renderEmpPickers(listElId){
      const el = document.getElementById(listElId);
      el.innerHTML = '';
      employees.forEach(e=>{
        const row = document.createElement('label');
        row.className = 'flex items-center gap-2';
        row.innerHTML = `
          <input type="checkbox" data-emp="${e.id}" class="rounded">
          <span class="text-sm">${e.name} <span class="text-slate-400">(${e.skills.join(', ')})</span></span>
        `;
        el.appendChild(row);
      });
    }

    // Panels initialisieren
    // Deadline
    const dlTaskList = document.getElementById('dlTaskList');
    const dlEmpList = document.getElementById('dlEmpList');
    const dlTotalEl = document.getElementById('dlTotal');
    const dlDaysEl = document.getElementById('dlDays');
    const dlHeadcountEl = document.getElementById('dlHeadcount');
    const deadlineDate = document.getElementById('deadlineDate');
    const deadlineHpd = document.getElementById('deadlineHpd');
    deadlineDate.value = dayjs().add(10,'day').format('YYYY-MM-DD');

    renderTaskPickers('dlTaskList', true);
    renderEmpPickers('dlEmpList');

    function workingDaysBetween(from, to, weekend=false){
      let d = dayjs(from), end = dayjs(to), n=0;
      while (d.isSame(end,'day') || d.isBefore(end,'day')){
        const wd = d.day(); // 0..6
        if (weekend || (wd>=1 && wd<=5)) n++;
        d = d.add(1,'day');
      }
      return n;
    }
    function calcDlHints(){
      const boxes = dlTaskList.querySelectorAll('input[type="checkbox"][data-task]');
      let total = 0;
      boxes.forEach(b=>{
        if (b.checked){
          const h = parseFloat(dlTaskList.querySelector(`input[data-hours="${b.dataset.task}"]`).value) || 0;
          total += h;
        }
      });
      const days = workingDaysBetween(dayjs(), deadlineDate.value);
      const hpd = parseFloat(deadlineHpd.value) || 8;
      const head = days>0 ? Math.ceil(total/(days*hpd)) : '–';
      dlTotalEl.textContent = total;
      dlDaysEl.textContent = days;
      dlHeadcountEl.textContent = head;
      return { total, days, hpd, head };
    }
    dlTaskList.addEventListener('input', calcDlHints);
    deadlineDate.addEventListener('change', calcDlHints);
    deadlineHpd.addEventListener('input', calcDlHints);
    calcDlHints();

    document.getElementById('btnDlCalc').addEventListener('click', calcDlHints);

    document.getElementById('btnDlPlan').addEventListener('click', ()=>{
      const {total, days, hpd} = calcDlHints();
      const chosenTasks = getCheckedTasks(dlTaskList, true);
      const chosenEmps = getCheckedEmps(dlEmpList);
      if (chosenTasks.length===0 || chosenEmps.length===0) {
        alert('Bitte Tasks und Mitarbeitende wählen.');
        return;
      }
      // Greedy: ab heute bis Deadline füllen
      const start = dayjs().startOf('day');
      const end = dayjs(deadlineDate.value);
      autoDistributeAcrossRange(chosenTasks, chosenEmps, start, end, hpd);
      renderTimeline();
      alert('Auto-Verteilung abgeschlossen (Deadline).');
    });

    function getCheckedTasks(container, withHours){
      const out = [];
      container.querySelectorAll('input[type="checkbox"][data-task]').forEach(ch=>{
        if (ch.checked){
          const t = tasks.find(x=>x.id===ch.dataset.task);
          const hours = withHours ? parseFloat(container.querySelector(`input[data-hours="${t.id}"]`).value) : t.estimated;
          out.push({...t, hours});
        }
      });
      return out;
    }
    function getCheckedEmps(container){
      const out = [];
      container.querySelectorAll('input[type="checkbox"][data-emp]').forEach(ch=>{
        if (ch.checked){
          const e = employees.find(x=>x.id===ch.dataset.emp);
          out.push(e);
        }
      });
      return out;
    }

    // Taskpack
    const tpDate = document.getElementById('tpDate');
    tpDate.value = dayjs().format('YYYY-MM-DD');
    const tpTaskList = document.getElementById('tpTaskList');
    const tpEmpList = document.getElementById('tpEmpList');
    renderTaskPickers('tpTaskList', true);
    renderEmpPickers('tpEmpList');

    const tpTotalEl = document.getElementById('tpTotal');
    const tpCapacityEl = document.getElementById('tpCapacity');
    const tpOverEl = document.getElementById('tpOver');

    function calcTpHints(){
      const selTasks = getCheckedTasks(tpTaskList, true);
      const total = selTasks.reduce((s,t)=>s+(parseFloat(t.hours)||0),0);
      tpTotalEl.textContent = total;
      const date = tpDate.value;
      const emps = getCheckedEmps(tpEmpList);
      const cap = emps.reduce((s,e)=> s + (e.availability?.[date] ?? e.maxDay ?? 8), 0);
      tpCapacityEl.textContent = cap;
      tpOverEl.classList.toggle('hidden', total <= cap);
      return { total, cap, emps, date, selTasks };
    }
    tpTaskList.addEventListener('input', calcTpHints);
    tpEmpList.addEventListener('change', calcTpHints);
    tpDate.addEventListener('change', calcTpHints);
    calcTpHints();

    document.getElementById('btnTpPlan').addEventListener('click', ()=>{
      const { total, cap, emps, date, selTasks } = calcTpHints();
      if (selTasks.length===0 || emps.length===0) {
        alert('Bitte Tasks und Mitarbeitende wählen.');
        return;
      }
      autoFitSingleDay(selTasks, emps, dayjs(date), document.getElementById('tpSpill').checked);
      renderTimeline();
      alert('Paket eingeplant.');
    });

    // Availability
    const avDate = document.getElementById('avDate');
    avDate.value = dayjs().add(1,'day').format('YYYY-MM-DD');
    const avTaskList = document.getElementById('avTaskList');
    function renderAvTaskList(){
      avTaskList.innerHTML = '';
      tasks.forEach(t=>{
        // "Ungeplant" simulieren: nimm alle Tasks rein
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2';
        row.innerHTML = `
          <label class="flex items-center gap-2 flex-1">
            <input type="checkbox" data-task="${t.id}" class="rounded">
            <span class="text-sm">${t.name} <span class="text-slate-400">(${t.job})</span></span>
          </label>
          <input type="number" min="1" max="16" value="${t.estimated}" data-hours="${t.id}" class="w-20 px-2 py-1 border rounded-lg text-sm">
        `;
        avTaskList.appendChild(row);
      });
    }
    renderAvTaskList();

    function calcAvHints(){
      const date = avDate.value;
      const availEmps = employees.filter(e => (e.availability?.[date] ?? e.maxDay ?? 8) > 0);
      const cap = availEmps.reduce((s,e)=> s + (e.availability?.[date] ?? e.maxDay ?? 8), 0);
      document.getElementById('avCount').textContent = availEmps.length;
      document.getElementById('avCap').textContent = cap;
      return { date, availEmps, cap };
    }
    avDate.addEventListener('change', calcAvHints);
    calcAvHints();

    document.getElementById('btnAvFit').addEventListener('click', ()=>{
      const { date, availEmps } = calcAvHints();
      const chosenTasks = getCheckedTasks(avTaskList, true);
      if (chosenTasks.length===0 || availEmps.length===0) {
        alert('Bitte Tasks wählen; es müssen MA verfügbar sein.');
        return;
      }
      autoFitSingleDay(chosenTasks, availEmps, dayjs(date), true);
      renderTimeline();
      alert('Auto-Fit durchgeführt (Verfügbarkeit).');
    });

    // Unassigned Bucket
    const bucket = document.getElementById('bucket');
    function renderBucket(){
      bucket.innerHTML = '';
      tasks.forEach(t=>{
        // zeige als Karten, damit man per Drag ggf. später verwenden könnte
        const card = document.createElement('div');
        card.className = 'px-3 py-2 bg-white border border-slate-200 rounded-xl flex items-center justify-between';
        card.innerHTML = `
          <div>
            <div class="font-medium">${t.name}</div>
            <div class="text-xs text-slate-500">${t.job} • ${t.estimated}h • Crew ${t.crew}</div>
          </div>
          <span class="badge bg-slate-100 border border-slate-200">${t.customer}</span>
        `;
        bucket.appendChild(card);
      });
    }
    renderBucket();

    // ----------------------------
    // Auto-Planer (Greedy Heuristiken)
    // ----------------------------
    const HPPD_DEFAULT = 8;

    function empDayCapacity(emp, dateStr){
      return emp.availability?.[dateStr] ?? emp.maxDay ?? HPPD_DEFAULT;
    }

    function dateList(from, to){
      const arr = [];
      let d = dayjs(from);
      while (d.isSame(to,'day') || d.isBefore(to,'day')) {
        arr.push(d);
        d = d.add(1,'day');
      }
      return arr;
    }

    function autoDistributeAcrossRange(taskList, empList, from, to, hppd = HPPD_DEFAULT){
      const days = dateList(from, to);
      // clone mutable remaining hours
      const queue = taskList.map(t=> ({...t, remaining: t.hours, crew: t.crew||1}));
      // track remaining per emp/day per hour slots
      const free = {};
      for (const d of days){
        const ds = d.format('YYYY-MM-DD');
        free[ds] = {};
        for (const e of empList){
          const cap = Math.min(empDayCapacity(e, ds), hppd);
          free[ds][e.id] = cap;
        }
      }
      // place in 1h-Scheiben, mit Crew-Gleichzeitigkeit
      for (const d of days){
        const ds = d.format('YYYY-MM-DD');
        let progress = true;
        while (progress){
          progress = false;
          for (const t of queue){
            if (t.remaining <= 0) continue;
            // crew slots suchen
            const candidates = empList.filter(e=> (free[ds][e.id] ?? 0) >= 1).slice(0, t.crew);
            if (candidates.length < t.crew) continue;
            // Startslot = 0..7 je nach restlicher Belegung -> wir vereinfachen: packe linksbündig
            candidates.forEach(e=>{
              const start = (HPPD_DEFAULT - (free[ds][e.id] ?? 0)); // grob: belegter Anteil
              createAssignment({ taskId: t.id, empId: e.id, date: ds, startSlot: start, hours: 1 });
              free[ds][e.id] -= 1;
            });
            t.remaining -= 1;
            progress = true;
          }
        }
      }
    }

    function autoFitSingleDay(taskList, empList, day, spillNext){
      const ds = day.format('YYYY-MM-DD');
      const free = new Map(empList.map(e=> [e.id, empDayCapacity(e, ds)]));
      // tasks sortiert nach Crew (größer zuerst) dann Reststunden
      const queue = taskList.map(t=> ({...t, remaining: t.hours, crew: t.crew||1}))
                            .sort((a,b)=> b.crew - a.crew || b.hours - a.hours);
      for (const t of queue){
        while (t.remaining > 0){
          const group = empList.filter(e => (free.get(e.id) ?? 0) >= 1).slice(0, t.crew);
          if (group.length < t.crew) break;
          group.forEach(e=>{
            const start = (HPPD_DEFAULT - (free.get(e.id) ?? 0));
            createAssignment({ taskId: t.id, empId: e.id, date: ds, startSlot: start, hours: 1 });
            free.set(e.id, free.get(e.id)-1);
            t.remaining -= 1;
          });
        }
        if (t.remaining > 0 && spillNext){
          // schiebe Rest auf nächsten Tag (einfach, ohne erneute Crewprüfung)
          const next = day.add(1,'day');
          createAssignment({ taskId: t.id, empId: null, date: next.format('YYYY-MM-DD'), startSlot: 0, hours: t.remaining });
          t.remaining = 0;
        }
      }
    }

    function createAssignment({taskId, empId, date, startSlot, hours}){
      const id = 'a'+Math.random().toString(36).slice(2,9);
      assignments.push({ id, taskId, empId, date, startSlot: Math.max(0, Math.min(7, startSlot)), hours: Math.max(1, Math.min(8, hours)) });
      return id;
    }

    // ----------------------------
    // Timeline Rendering
    // ----------------------------
    const timeline = document.getElementById('timeline');
    const depSvg = document.getElementById('depSvg');

    function renderTimeline(){
      const cols = [];
      let d = dayjs(rangeFrom);
      while (d.isSame(rangeTo,'day') || d.isBefore(rangeTo,'day')){
        cols.push(d);
        d = d.add(1,'day');
      }
      rangeLabel.textContent = `${rangeFrom.format('DD.MM.YYYY')} – ${rangeTo.format('DD.MM.YYYY')}`;

      const colCount = cols.length + 1; // +1 für Name-Spalte
      timeline.innerHTML = `
        <div class="grid" style="grid-template-columns: 220px repeat(${cols.length}, 1fr); position:relative;">
          <!-- Header -->
          <div class="sticky left-0 z-[2] bg-white font-medium px-3 py-2 border-b border-slate-200">Mitarbeitende</div>
          ${cols.map(c=>`
            <div class="px-3 py-2 border-b border-slate-200 text-sm text-slate-600">
              <div class="font-medium">${c.format('ddd DD.MM')}</div>
              <div class="text-xs text-slate-400">8h-Raster</div>
            </div>
          `).join('')}

          <!-- Rows -->
          ${employees.map(emp=>{
            return `
              <div class="sticky left-0 z-[1] bg-white px-3 py-4 border-b border-slate-100">
                <div class="font-medium">${emp.name}</div>
                <div class="text-xs text-slate-500">${emp.skills.join(', ')}</div>
              </div>
              ${cols.map(c=>{
                const ds = c.format('YYYY-MM-DD');
                const cap = empDayCapacity(emp, ds);
                return `
                  <div class="cell hour-grid border-b border-slate-100" data-emp="${emp.id}" data-date="${ds}">
                    <div class="absolute top-1 right-2 text-[11px] text-slate-400">${cap}h</div>
                  </div>
                `;
              }).join('')}
            `;
          }).join('')}

          <!-- Unassigned row -->
          <div class="sticky left-0 bg-slate-50 px-3 py-3 border-t border-slate-200">Nicht zugewiesen</div>
          ${cols.map(c=>`
            <div class="cell hour-grid bg-slate-50" data-emp="" data-date="${c.format('YYYY-MM-DD')}"></div>
          `).join('')}
        </div>
      `;

      // Karten rendern
      for (const a of assignments){
        placeCard(a);
      }

      // Dependency-Linien
      drawDependencyLines();
    }

    function placeCard(a){
      const sel = `.cell[data-emp="${a.empId ?? ''}"][data-date="${a.date}"]`;
      const cell = timeline.querySelector(sel);
      if (!cell) return;

      const task = tasks.find(t=>t.id===a.taskId);
      const color = pickColor(task.id);
      const card = document.createElement('div');
      card.className = `assignment select-none ${a.empId? 'bg-white' : 'bg-amber-50'} border border-slate-200`;
      card.dataset.id = a.id;
      card.dataset.taskId = a.taskId;
      card.style.left = `${(a.startSlot/8)*100}%`;
      card.style.width = `${(a.hours/8)*100}%`;
      card.innerHTML = `
        <div class="h-full w-full rounded-2xl card-shadow overflow-hidden">
          <div class="h-2" style="background:${color}"></div>
          <div class="px-3 py-2">
            <div class="text-sm font-medium truncate">${task.name}</div>
            <div class="text-[11px] text-slate-500 truncate">${task.job} • ${a.hours}h ${a.empId?'':'• unzugew.'}</div>
          </div>
        </div>
        <div class="resizer-h left"></div>
        <div class="resizer-h right"></div>
        <div class="menu -top-2 right-2">
          <button class="text-[11px] px-2 py-1 bg-white border border-slate-200 rounded-md" data-action="split">Split</button>
          <button class="text-[11px] px-2 py-1 bg-white border border-slate-200 rounded-md" data-action="delete">Löschen</button>
        </div>
      `;
      // Click → Drawer
      card.addEventListener('click', (e)=>{
        if (e.target.dataset.action) return; // Menü
        openDrawer(a);
      });
      // Menü
      card.querySelector('[data-action="delete"]').addEventListener('click', ()=>{
        const idx = assignments.findIndex(x=>x.id===a.id);
        if (idx>=0) assignments.splice(idx,1);
        renderTimeline();
      });
      card.querySelector('[data-action="split"]').addEventListener('click', ()=>{
        if (a.hours <= 1) return;
        const half = Math.floor(a.hours/2);
        a.hours = half;
        createAssignment({ taskId:a.taskId, empId:a.empId, date:a.date, startSlot:a.startSlot+half, hours: Math.max(1, half) });
        renderTimeline();
      });

      cell.appendChild(card);
      makeDraggable(card);
    }

    function openDrawer(a){
      const t = tasks.find(t=>t.id===a.taskId);
      const drawer = document.getElementById('drawer');
      const body = document.getElementById('drawerBody');
      body.innerHTML = `
        <div><span class="text-slate-500">Task:</span> <span class="font-medium">${t.name}</span></div>
        <div class="text-slate-500 text-xs">${t.job} • Crew ${t.crew} • Skills: ${t.skills.join(', ')}</div>
        <div><span class="text-slate-500">Datum:</span> ${dayjs(a.date).format('ddd, DD.MM.YYYY')}</div>
        <div><span class="text-slate-500">Zuweisung:</span> ${a.empId ? employees.find(e=>e.id===a.empId).name : 'Unzugewiesen'}</div>
        <div class="grid grid-cols-2 gap-2">
          <label class="text-sm">Startslot</label>
          <input id="dStart" type="number" min="0" max="7" value="${a.startSlot}" class="px-2 py-1 border rounded-lg">
          <label class="text-sm">Stunden</label>
          <input id="dHours" type="number" min="1" max="8" value="${a.hours}" class="px-2 py-1 border rounded-lg">
        </div>
        <div class="pt-2">
          <button id="dSave" class="px-3 py-2 rounded-lg bg-slate-900 text-white">Speichern</button>
        </div>
      `;
      document.getElementById('dSave').onclick = ()=>{
        a.startSlot = Math.max(0, Math.min(7, parseInt(document.getElementById('dStart').value)));
        a.hours = Math.max(1, Math.min(8, parseInt(document.getElementById('dHours').value)));
        renderTimeline();
      };
      drawer.classList.remove('hidden');
    }
    document.getElementById('drawerClose').addEventListener('click', ()=> document.getElementById('drawer').classList.add('hidden'));

    // ----------------------------
    // Drag & Resize (Interact.js)
    // ----------------------------
    function makeDraggable(card){
      interact(card).draggable({
        inertia: false,
        listeners: {
          start(ev){ card.classList.add('dragging'); },
          move(ev){
            const x = (parseFloat(card.getAttribute('data-x')) || 0) + ev.dx;
            const y = (parseFloat(card.getAttribute('data-y')) || 0) + ev.dy;
            card.style.transform = `translate(${x}px, ${y}px)`;
            card.setAttribute('data-x', x);
            card.setAttribute('data-y', y);
          },
          end(ev){
            card.classList.remove('dragging');
            // Ziel-Zelle bestimmen
            const rect = card.getBoundingClientRect();
            const centerX = rect.left + rect.width/2 + window.scrollX;
            const centerY = rect.top + rect.height/2 + window.scrollY;
            const cells = Array.from(timeline.querySelectorAll('.cell'));
            let target = null;
            for (const c of cells){
              const r = c.getBoundingClientRect();
              const x0 = r.left + window.scrollX, y0 = r.top + window.scrollY;
              const x1 = x0 + r.width, y1 = y0 + r.height;
              if (centerX>=x0 && centerX<=x1 && centerY>=y0 && centerY<=y1){ target = c; break; }
            }
            const a = assignments.find(x=> x.id === card.dataset.id);
            if (target && a){
              a.empId = target.dataset.emp || null;
              a.date = target.dataset.date;
              // Startslot anhand relativer Position innerhalb der Zelle
              const r = target.getBoundingClientRect();
              const left = rect.left - r.left;
              const pct = Math.max(0, Math.min(1, left / r.width));
              a.startSlot = Math.round(pct * 8);
            }
            card.style.transform = ''; card.removeAttribute('data-x'); card.removeAttribute('data-y');
            renderTimeline();
          }
        }
      }).resizable({
        edges: { left: '.left', right: '.right' },
        listeners: {
          move (event) {
            let { x, y } = event.target.dataset;
            x = (parseFloat(x) || 0) + event.deltaRect.left;
            y = (parseFloat(y) || 0) + event.deltaRect.top;
            Object.assign(event.target.style, {
              width: `${event.rect.width}px`,
              transform: `translate(${x}px, ${y}px)`
            });
            Object.assign(event.target.dataset, { x, y });
          },
          end (event) {
            const a = assignments.find(x=> x.id === event.target.dataset.id);
            if (!a) return;
            // width → Stunden runden auf 1/8
            const cell = event.target.closest('.cell');
            const r = cell.getBoundingClientRect();
            const newLeft = parseFloat(event.target.dataset.x) || 0;
            const newWidth = event.rect.width;
            const leftPct = Math.max(0, Math.min(1, (event.target.offsetLeft + newLeft) / r.width));
            const widthPct = Math.max(0.125, Math.min(1, newWidth / r.width));
            a.startSlot = Math.round(leftPct * 8);
            a.hours = Math.max(1, Math.round(widthPct * 8));
            event.target.style.transform = ''; event.target.dataset.x = 0; event.target.dataset.y = 0;
            renderTimeline();
          }
        }
      });
    }

    // ----------------------------
    // Dependency Lines
    // ----------------------------
    function drawDependencyLines(){
      depSvg.innerHTML = '';
      const svgNS = 'http://www.w3.org/2000/svg';
      for (const dep of dependencies){
        // finde späteste Scheibe des FROM und früheste des TO
        const fromSegs = assignments.filter(a=>a.taskId===dep.from);
        const toSegs = assignments.filter(a=>a.taskId===dep.to);
        if (fromSegs.length===0 || toSegs.length===0) continue;
        const fromMax = fromSegs.sort((a,b)=> (a.date<b.date?-1:1) || (a.startSlot - b.startSlot)).slice(-1)[0];
        const toMin = toSegs.sort((a,b)=> (a.date<b.date?-1:1) || (a.startSlot - b.startSlot))[0];
        const fromEl = timeline.querySelector(`.assignment[data-id="${fromMax.id}"]`);
        const toEl = timeline.querySelector(`.assignment[data-id="${toMin.id}"]`);
        if (!fromEl || !toEl) continue;
        const fr = fromEl.getBoundingClientRect();
        const tr = toEl.getBoundingClientRect();
        const sx = fr.right + window.scrollX;
        const sy = fr.top + fr.height/2 + window.scrollY;
        const tx = tr.left + window.scrollX;
        const ty = tr.top + tr.height/2 + window.scrollY;

        const path = document.createElementNS(svgNS, 'path');
        const mid = (sx + tx) / 2;
        path.setAttribute('d', `M ${sx},${sy} C ${mid},${sy} ${mid},${ty} ${tx},${ty}`);
        const g = document.createElementNS(svgNS, 'g');
        g.classList.add('dep-line');
        // Validierung FS: to darf nicht vor Ende von from liegen (Datum/Slot)
        const fromEndKey = fromMax.date + ':' + (fromMax.startSlot + fromMax.hours);
        const toStartKey = toMin.date + ':' + toMin.startSlot;
        if (fromEndKey > toStartKey) g.classList.add('dep-line--invalid');
        g.appendChild(path);
        depSvg.appendChild(g);
      }
    }

    // ----------------------------
    // Farben
    // ----------------------------
    const palette = ['#93c5fd','#86efac','#fca5a5','#fde047','#f0abfc','#a5b4fc','#67e8f9','#f9a8d4'];
    function pickColor(taskId){
      const idx = Math.abs(hashCode(taskId)) % palette.length;
      return palette[idx];
    }
    function hashCode(s){ let h=0; for(let i=0;i<s.length;i++){ h=((h<<5)-h)+s.charCodeAt(i); h|=0; } return h; }

    // ----------------------------
    // Init: ein paar Startbelegungen
    // ----------------------------
    (function seed(){
      // Ein wenig vorplanen, damit man sofort was sieht
      const d0 = dayjs().format('YYYY-MM-DD');
      createAssignment({ taskId:'t1', empId:'e1', date:d0, startSlot:0, hours:3 });
      createAssignment({ taskId:'t1', empId:'e2', date:d0, startSlot:0, hours:3 });
      createAssignment({ taskId:'t2', empId:'e2', date:dayjs().add(1,'day').format('YYYY-MM-DD'), startSlot:2, hours:4 });
      createAssignment({ taskId:'t3', empId:'e3', date:dayjs().add(2,'day').format('YYYY-MM-DD'), startSlot:1, hours:3 });
      createAssignment({ taskId:'t5', empId:null, date:dayjs().add(1,'day').format('YYYY-MM-DD'), startSlot:0, hours:4 });
    })();

    // Render zum Start
    renderTimeline();
  </script>
</body>
</html>
