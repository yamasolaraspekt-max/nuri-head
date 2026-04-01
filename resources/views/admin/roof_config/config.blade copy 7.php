<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Calendar MVP – Trello-style Editorial Calendar</title>

  <!-- Tailwind (CDN build for MVP) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    if (window.tailwind) {
      window.tailwind.config = {
        theme: {
          extend: {
            colors: {
              ink: '#0f172a',
              slateish: '#334155',
              ringy: '#93c5fd'
            }
          }
        }
      };
    }
  </script>

  <!-- FullCalendar v6 (core bundle + locales + rrule plugin) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/rrule@2.7.2/dist/es5/rrule.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@6.1.10/index.global.min.js"></script>

  <style>
    /* Trello-style event card */
    .fc-event { border-radius: .5rem !important; border: 1px solid rgba(0,0,0,.06) !important; }
    .fc .fc-daygrid-event { padding: 0 !important; }
    .fc .fc-daygrid-event .fc-event-main { padding: .375rem .5rem .45rem .5rem; }
    .fc .fc-timegrid-event .fc-event-main { padding: .25rem .4rem; }

    .ev-card {
      display: grid;
      grid-template-columns: 4px 1fr;
      gap: .5rem;
      align-items: start;
    }
    .ev-leftbar { width: 4px; border-radius: 8px; }
    .ev-title { font-size: .85rem; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ev-tags { display:flex; gap:.25rem; margin-top:.25rem; flex-wrap: wrap; }
    .ev-pill {
      font-size: .65rem; padding:.15rem .4rem; border-radius:9999px; border:1px solid rgba(0,0,0,.07);
      background: #f8fafc;
    }
    .ev-people {
      font-size:.68rem; margin-top:.15rem; opacity:.8;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    /* Focus ring helpers for a11y */
    .focus-ring:focus { outline: 2px solid #93c5fd; outline-offset: 2px; }
    /* Compact header on small screens */
    @media (max-width: 640px){
      .toolbar-grid { grid-template-columns: 1fr; gap:.5rem; }
      .filters-row { grid-template-columns: 1fr; }
      .views-wrap { justify-content: space-between; }
    }
  </style>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="max-w-7xl mx-auto px-3 sm:px-6 py-5">
    <!-- Top Toolbar -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 p-3 sm:p-4">
      <div class="toolbar-grid grid grid-cols-3 items-center gap-3">
        <!-- Left: Nav -->
        <div class="flex items-center gap-2">
          <button id="btnPrev" class="focus-ring rounded-lg px-2.5 py-1.5 border hover:bg-slate-50">&larr;</button>
          <button id="btnToday" class="focus-ring rounded-lg px-3 py-1.5 border hover:bg-slate-50">Today</button>
          <button id="btnNext" class="focus-ring rounded-lg px-2.5 py-1.5 border hover:bg-slate-50">&rarr;</button>
        </div>
        <!-- Center: Title -->
        <div class="text-center">
          <div id="calTitle" class="font-semibold text-slate-900"></div>
          <div class="text-xs text-slate-500" id="activeCalName">Main Calendar</div>
        </div>
        <!-- Right: View buttons -->
        <div class="views-wrap flex items-center justify-end gap-1.5">
          <button data-view="dayGridMonth" class="view-btn focus-ring rounded-lg px-3 py-1.5 border bg-slate-900 text-white">Month</button>
          <button data-view="timeGridWeek" class="view-btn focus-ring rounded-lg px-3 py-1.5 border hover:bg-slate-50">Week</button>
          <button data-view="timeGridDay"  class="view-btn focus-ring rounded-lg px-3 py-1.5 border hover:bg-slate-50">Day</button>
          <button data-view="listWeek"    class="view-btn focus-ring rounded-lg px-3 py-1.5 border hover:bg-slate-50">List</button>
        </div>
      </div>

      <!-- Filters / Actions -->
      <div class="mt-3 grid filters-row grid-cols-1 lg:grid-cols-12 gap-3">
        <div class="lg:col-span-4">
          <label class="sr-only" for="searchTitle">Search</label>
          <input id="searchTitle" class="focus-ring w-full rounded-xl border px-3 py-2"
                 placeholder="Search title…" />
        </div>

        <div class="lg:col-span-5">
          <div class="flex flex-wrap items-center gap-2" id="tagFilters" aria-label="Tag filters">
            <!-- Tag pills injected here -->
          </div>
        </div>

        <div class="lg:col-span-3 flex items-center justify-end gap-2">
          <label class="inline-flex items-center gap-2 text-sm bg-slate-100 border rounded-xl px-3 py-2">
            <input id="toggleWeekends" type="checkbox" class="h-4 w-4" checked />
            <span>Show weekends</span>
          </label>

          <select id="localeSelect" class="focus-ring rounded-xl border px-2.5 py-2 text-sm">
            <option value="en">EN</option>
            <option value="de">DE</option>
          </select>

          <button id="btnAdd" class="focus-ring rounded-xl bg-blue-600 text-white px-3 py-2">Add Event</button>

          <button id="btnExport" class="focus-ring rounded-xl border px-3 py-2">Export JSON</button>
          <label class="focus-ring rounded-xl border px-3 py-2 cursor-pointer">
            Import JSON<input id="importFile" type="file" accept="application/json" class="hidden" />
          </label>
        </div>
      </div>
    </div>

    <!-- Calendar -->
    <div class="mt-4 bg-white shadow-sm rounded-2xl border border-slate-200 p-2 sm:p-4">
      <div id="calendar"></div>
    </div>
  </div>

  <!-- Quick View Modal -->
  <div id="modalQuick" class="fixed inset-0 hidden items-center justify-center z-40">
    <div class="absolute inset-0 bg-black/40" data-close></div>
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border p-4">
      <div class="flex items-start justify-between">
        <h3 id="qvTitle" class="text-lg font-semibold"></h3>
        <button class="focus-ring p-2 rounded-xl hover:bg-slate-100" data-close aria-label="Close">&times;</button>
      </div>
      <div class="mt-2 text-sm text-slate-700 space-y-2">
        <div id="qvWhen" class="font-medium"></div>
        <div id="qvTags" class="flex flex-wrap gap-1"></div>
        <div id="qvPeople" class="text-slate-600"></div>
        <div id="qvDesc" class="text-slate-700 whitespace-pre-wrap"></div>
        <div id="qvLoc"  class="text-slate-600"></div>
      </div>
      <div class="mt-4 flex justify-end gap-2">
        <button id="qvEdit"   class="focus-ring rounded-xl border px-3 py-2">Edit</button>
        <button id="qvDelete" class="focus-ring rounded-xl bg-red-600 text-white px-3 py-2">Delete</button>
      </div>
    </div>
  </div>

  <!-- Create/Edit Modal -->
  <div id="modalEdit" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/40" data-close></div>
    <form id="editForm" class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border p-4" novalidate>
      <div class="flex items-start justify-between">
        <h3 id="editTitle" class="text-lg font-semibold">New Event</h3>
        <button type="button" class="focus-ring p-2 rounded-xl hover:bg-slate-100" data-close aria-label="Close">&times;</button>
      </div>

      <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div class="md:col-span-2">
          <label class="block text-slate-600 mb-1">Title</label>
          <input name="title" class="focus-ring w-full rounded-xl border px-3 py-2" required />
        </div>

        <div>
          <label class="block text-slate-600 mb-1">Start</label>
          <input name="start_at" type="datetime-local" class="focus-ring w-full rounded-xl border px-3 py-2" required />
        </div>
        <div>
          <label class="block text-slate-600 mb-1">End</label>
          <input name="end_at" type="datetime-local" class="focus-ring w-full rounded-xl border px-3 py-2" />
        </div>

        <div class="md:col-span-2 flex items-center gap-2">
          <input id="fldAllDay" name="all_day" type="checkbox" class="h-4 w-4" />
          <label for="fldAllDay">All day</label>
        </div>

        <div class="md:col-span-2">
          <label class="block text-slate-600 mb-1">Tags</label>
          <div id="editTags" class="flex flex-wrap gap-2"></div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-slate-600 mb-1">People (comma-separated: “Name &lt;email&gt;” or “Name”)</label>
          <input name="people" class="focus-ring w-full rounded-xl border px-3 py-2" placeholder="Alice <alice@acme.com>, Bob" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-slate-600 mb-1">Location</label>
          <input name="location" class="focus-ring w-full rounded-xl border px-3 py-2" />
        </div>

        <div class="md:col-span-2">
          <label class="block text-slate-600 mb-1">Description</label>
          <textarea name="description" rows="3" class="focus-ring w-full rounded-xl border px-3 py-2"></textarea>
        </div>

        <div class="md:col-span-2">
          <label class="block text-slate-600 mb-1">Recurrence (RRULE, optional)</label>
          <input name="recurrence_rrule" class="focus-ring w-full rounded-xl border px-3 py-2"
                 placeholder="FREQ=WEEKLY;INTERVAL=1;BYDAY=MO,WE;COUNT=8" />
          <p class="text-xs text-slate-500 mt-1">Supports RFC5545 via RRule (FREQ=DAILY|WEEKLY|MONTHLY; INTERVAL; BYDAY; COUNT; UNTIL).</p>
        </div>
      </div>

      <div class="mt-4 flex items-center justify-between">
        <div class="text-xs text-slate-500" id="editMeta"></div>
        <div class="flex gap-2">
          <button type="button" class="focus-ring rounded-xl border px-3 py-2" data-close>Cancel</button>
          <button type="submit" class="focus-ring rounded-xl bg-blue-600 text-white px-3 py-2">Save</button>
        </div>
      </div>

      <input type="hidden" name="id" />
      <input type="hidden" name="calendar_id" />
      <input type="hidden" name="timezone" />
    </form>
  </div>

  <script>
    // -----------------------------
    // Config / Constants
    // -----------------------------
    const LS_KEY = 'calendar_mvp_v1';
    const DEFAULT_CALENDAR_ID = 'cal_main';
    const DEFAULT_TZ = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';

    // Tag palette (primary tag controls left border)
    const TAGS = [
      { key: 'Planning',  color: '#6366f1' },
      { key: 'Design',    color: '#d946ef' },
      { key: 'Dev',       color: '#22c55e' },
      { key: 'Marketing', color: '#f59e0b' },
      { key: 'Release',   color: '#ef4444' }
    ];
    const tagColor = (tag) => (TAGS.find(t => t.key === tag)?.color) || '#94a3b8';

    // UI state persisted
    const UI_LS_KEY = 'calendar_mvp_ui';
    const getUIState = () => JSON.parse(localStorage.getItem(UI_LS_KEY) || '{}');
    const setUIState = (s) => localStorage.setItem(UI_LS_KEY, JSON.stringify(s));

    // -----------------------------
    // Lightweight "API" (localStorage-backed)
    // -----------------------------
    function uuidv4(){
      // RFC4122-ish
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
      });
    }

    function loadStore(){
      let data = localStorage.getItem(LS_KEY);
      if (!data) {
        const now = new Date();
        const seedBase = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 10, 0, 0);
        const days = (n)=> new Date(seedBase.getTime() + n*86400000);

        const seedEvents = [
          { title:'Sprint Planning',    start_at: iso(days(2)), end_at: iso(days(2), 11), tags:['Planning'], attendees:[{name:'Alice'}] },
          { title:'Wireframe Review',   start_at: iso(days(3), 14), end_at: iso(days(3), 15), tags:['Design','Planning'], attendees:[{name:'Bob'}] },
          { title:'Dev Kickoff',        start_at: iso(days(5), 9),  end_at: iso(days(5), 11), tags:['Dev'], attendees:[{name:'Caro'}] },
          { title:'Content Draft',      start_at: iso(days(7), 13), end_at: iso(days(7), 15), tags:['Marketing'] },
          { title:'Release Train',      start_at: iso(days(10), 16),end_at: iso(days(10), 17), tags:['Release'] },
          { title:'1:1 Alice',          start_at: iso(days(1), 10), end_at: iso(days(1), 10,30), tags:['Planning'] },
          { title:'Design Standup',     start_at: iso(days(0), 9,30), end_at: iso(days(0), 10), tags:['Design'], recurrence_rrule:'FREQ=WEEKLY;COUNT=6;BYDAY=MO' },
          { title:'Dev Standup',        start_at: iso(days(0), 10,15), end_at: iso(days(0), 10,45), tags:['Dev'], recurrence_rrule:'FREQ=WEEKLY;COUNT=8;BYDAY=MO,TU,WE,TH,FR' },
          { title:'Marketing Sync',     start_at: iso(days(4), 11), end_at: iso(days(4), 12), tags:['Marketing'], attendees:[{name:'Dan'}, {name:'Eve'}] },
          { title:'QA Regression',      start_at: iso(days(12), 15), end_at: iso(days(12), 17), tags:['Dev','Release'] },
        ].map(e => makeEvent(e));

        const store = {
          calendars: [{
            id: DEFAULT_CALENDAR_ID, name:'Main', color:'#2563eb',
            owner_id:'demo', visibility:'private', created_at: iso(new Date()), updated_at: iso(new Date())
          }],
          events: seedEvents
        };
        localStorage.setItem(LS_KEY, JSON.stringify(store));
        return store;
      }
      return JSON.parse(data);
    }

    function saveStore(store){
      localStorage.setItem(LS_KEY, JSON.stringify(store));
    }

    function apiListCalendars(){
      const s = loadStore();
      return s.calendars;
    }

    // Core list: expand recurrences and filter by range + UI filters
    function apiListEvents({from, to, calendar_id}){
      const s = loadStore();
      const events = s.events.filter(e => !e.deleted_at && (!calendar_id || e.calendar_id === calendar_id));
      const out = [];
      const rangeStart = new Date(from);
      const rangeEnd   = new Date(to);

      // UI filters
      const ui = getUIState();
      const q = (ui.search || '').trim().toLowerCase();
      const selectedTags = ui.tags || [];

      for (const e of events) {
        if (q && !e.title.toLowerCase().includes(q)) continue;
        if (selectedTags.length){
          const hasAny = (e.tags || []).some(t => selectedTags.includes(t));
          if (!hasAny) continue;
        }

        if (e.recurrence_rrule){
          try {
            // Expand via RRule
            const rrule = new RRule({
              ...RRule.parseString(e.recurrence_rrule),
              dtstart: new Date(e.start_at)
            });
            const between = rrule.between(rangeStart, rangeEnd, true);
            for (const dt of between) {
              const dur = e.end_at ? (new Date(e.end_at) - new Date(e.start_at)) : 0;
              const instStart = new Date(dt);
              const instEnd   = dur ? new Date(instStart.getTime() + dur) : null;
              out.push({...e,
                id: `${e.id}__${instStart.toISOString()}`,
                instance_start: instStart.toISOString(),
                series_id: e.id,
                start_at: instStart.toISOString(),
                end_at: instEnd ? instEnd.toISOString() : null
              });
            }
          } catch(err) {
            console.warn('RRULE parse error', err, e.recurrence_rrule);
          }
        } else {
          // Non-recurring: include if intersects
          const sAt = new Date(e.start_at);
          const eAt = e.end_at ? new Date(e.end_at) : null;
          const overlaps = eAt
            ? (sAt < rangeEnd && eAt > rangeStart)
            : (sAt >= rangeStart && sAt <= rangeEnd);
          if (overlaps) out.push(e);
        }
      }
      return out;
    }

    function apiCreateEvent(payload){
      const s = loadStore();
      const ev = makeEvent(payload);
      s.events.push(ev);
      saveStore(s);
      return ev;
    }

    function apiReadEvent(id){
      const s = loadStore();
      return s.events.find(e => e.id === id) || null;
    }

    function apiUpdateEvent(id, patch){
      const s = loadStore();
      const i = s.events.findIndex(e => e.id === id);
      if (i === -1) return null;
      const updated = {...s.events[i], ...patch, updated_at: iso(new Date())};
      s.events[i] = normalizeEvent(updated);
      saveStore(s);
      return s.events[i];
    }

    function apiDeleteEvent(id){
      const s = loadStore();
      const e = s.events.find(ev => ev.id === id);
      if (!e) return null;
      e.deleted_at = iso(new Date());
      e.updated_at = iso(new Date());
      saveStore(s);
      return e;
    }

    function makeEvent(p){
      const base = {
        id: uuidv4(),
        calendar_id: p.calendar_id || DEFAULT_CALENDAR_ID,
        title: p.title || '',
        description: p.description || null,
        location: p.location || null,
        start_at: p.start_at,
        end_at: p.end_at || null,
        all_day: !!p.all_day,
        tags: Array.isArray(p.tags) ? [...p.tags] : [],
        attendees: Array.isArray(p.attendees) ? p.attendees.map(a => ({name:a.name||'', email:a.email||'', status:a.status||'invited'})) : [],
        color: p.color || null,
        recurrence_rrule: p.recurrence_rrule || null,
        timezone: p.timezone || DEFAULT_TZ,
        created_by: p.created_by || 'demo',
        created_at: iso(new Date()),
        updated_at: iso(new Date()),
        deleted_at: null
      };
      return normalizeEvent(base);
    }

    function normalizeEvent(e){
      // ensure ISO with timezone (UTC Z)
      return {...e,
        start_at: toISOZ(e.start_at),
        end_at: e.end_at ? toISOZ(e.end_at) : null
      };
    }

    function iso(date, hh=0, mm=0){
      const d = new Date(date);
      d.setHours(hh, mm, 0, 0);
      return toISOZ(d);
    }
    function toISOZ(d){
      const dt = (d instanceof Date) ? d : new Date(d);
      return new Date(dt.getTime()).toISOString();
    }

    // -----------------------------
    // UI Bootstrap
    // -----------------------------
    const store = loadStore(); // seeds on first run

    // Build tag filter pills
    const tagFiltersEl = document.getElementById('tagFilters');
    function renderTagFilters(){
      tagFiltersEl.innerHTML = '';
      const ui = getUIState();
      const selected = new Set(ui.tags || []);
      TAGS.forEach(t => {
        const id = `tag_${t.key}`;
        const wrap = document.createElement('label');
        wrap.className = `inline-flex items-center gap-2 px-2.5 py-1.5 rounded-full border cursor-pointer select-none ${selected.has(t.key)?'bg-slate-900 text-white border-slate-900':'bg-white hover:bg-slate-50'}`;
        wrap.innerHTML = `
          <input type="checkbox" value="${t.key}" ${selected.has(t.key)?'checked':''} class="hidden">
          <span class="h-2.5 w-2.5 rounded-full" style="background:${t.color}"></span>
          <span class="text-sm">${t.key}</span>
        `;
        wrap.addEventListener('change', (e) => {
          const ui = getUIState();
          const set = new Set(ui.tags || []);
          const v = wrap.querySelector('input').checked;
          if (v) set.add(t.key); else set.delete(t.key);
          setUIState({...ui, tags: Array.from(set)});
          calendar.refetchEvents();
        });
        tagFiltersEl.appendChild(wrap);
      });
    }
    renderTagFilters();

    // Build edit-form tags
    const editTagsEl = document.getElementById('editTags');
    function renderEditTags(selected= []){
      editTagsEl.innerHTML = '';
      TAGS.forEach(t => {
        const id = `edtag_${t.key}`;
        const lab = document.createElement('label');
        lab.className = "inline-flex items-center gap-2 px-2.5 py-1.5 rounded-full border cursor-pointer select-none";
        lab.innerHTML = `
          <input type="checkbox" value="${t.key}" ${selected.includes(t.key)?'checked':''} class="h-4 w-4">
          <span class="h-2.5 w-2.5 rounded-full" style="background:${t.color}"></span>
          <span class="text-sm">${t.key}</span>
        `;
        editTagsEl.appendChild(lab);
      });
    }

    // Persisted UI bits
    const searchTitle = document.getElementById('searchTitle');
    const toggleWeekends = document.getElementById('toggleWeekends');
    const localeSelect    = document.getElementById('localeSelect');
    const uiInit = getUIState();
    searchTitle.value = uiInit.search || '';
    toggleWeekends.checked = uiInit.weekends ?? true;
    localeSelect.value = uiInit.locale || 'en';

    searchTitle.addEventListener('input', () => {
      const ui = getUIState();
      setUIState({...ui, search: searchTitle.value});
      calendar.refetchEvents();
    });
    toggleWeekends.addEventListener('change', () => {
      const ui = getUIState();
      setUIState({...ui, weekends: toggleWeekends.checked});
      calendar.setOption('weekends', toggleWeekends.checked);
    });
    localeSelect.addEventListener('change', () => {
      const ui = getUIState();
      setUIState({...ui, locale: localeSelect.value});
      calendar.setOption('locale', localeSelect.value);
    });

    // -----------------------------
    // FullCalendar
    // -----------------------------
    const calEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calEl, {
      height: 'auto',
      locales: FullCalendar.allLocales,
      locale: localeSelect.value,
      initialView: (getUIState().view) || 'dayGridMonth',
      weekends: toggleWeekends.checked,
      nowIndicator: true,
      navLinks: true,
      editable: true,
      droppable: false,
      selectable: false,
      dayMaxEventRows: 4,
      headerToolbar: false, // we use custom toolbar
      eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
      // Data source (range + filters)
      events: function(info, success, failure) {
        try {
          const list = apiListEvents({from: info.startStr, to: info.endStr, calendar_id: DEFAULT_CALENDAR_ID});
          // Map to FullCalendar event objects
          const mapped = list.map(e => {
            const primaryTag = (e.tags && e.tags[0]) || null;
            const borderColor = e.color || (primaryTag ? tagColor(primaryTag) : '#94a3b8');
            return {
              id: e.id,
              start: e.start_at,
              end: e.end_at,
              allDay: !!e.all_day,
              title: e.title,
              extendedProps: {
                ...e,
                borderColor
              }
            };
          });
          success(mapped);
          document.getElementById('calTitle').textContent = calendar.view.title;
        } catch(err){
          console.error(err);
          failure(err);
        }
      },
      // Trello-style card rendering
      eventContent: function(arg){
        const e = arg.event.extendedProps;
        const el = document.createElement('div');
        el.className = 'ev-card';
        const left = document.createElement('div');
        left.className = 'ev-leftbar';
        left.style.background = e.borderColor || '#94a3b8';

        const right = document.createElement('div');
        const t = document.createElement('div');
        t.className = 'ev-title';
        t.textContent = arg.event.title || '(Untitled)';

        const tagsWrap = document.createElement('div');
        tagsWrap.className = 'ev-tags';
        (e.tags || []).slice(0,2).forEach(tag => {
          const pill = document.createElement('span');
          pill.className = 'ev-pill';
          pill.style.borderColor = 'rgba(0,0,0,.08)';
          pill.textContent = tag;
          tagsWrap.appendChild(pill);
        });

        const people = (e.attendees || []).map(a => a.name).filter(Boolean);
        if (people.length){
          const ppl = document.createElement('div');
          ppl.className = 'ev-people';
          ppl.textContent = '👤 ' + people.slice(0,3).join(', ') + (people.length>3 ? '…' : '');
          right.appendChild(ppl);
        }

        right.prepend(tagsWrap);
        right.prepend(t);
        el.appendChild(left);
        el.appendChild(right);
        return { domNodes: [el] };
      },
      eventDidMount: function(arg){
        // Ensure left border exists for keyboard users too
        arg.el.style.borderLeft = `4px solid ${arg.event.extendedProps.borderColor || '#94a3b8'}`;
      },
      dateClick: function(info){
        openEditModal({ start: info.dateStr, allDay: true });
      },
      eventClick: function(info){
        openQuickView(info.event);
      },
      eventDrop: function(info){
        const e = info.event.extendedProps;
        if (e.recurrence_rrule){
          alert('Updating single instances of recurring events is not implemented in this MVP. Edit the series instead.');
          info.revert();
          return;
        }
        apiUpdateEvent(e.id, {
          start_at: info.event.start?.toISOString(),
          end_at: info.event.end ? info.event.end.toISOString() : null,
          all_day: info.event.allDay
        });
      },
      eventResize: function(info){
        const e = info.event.extendedProps;
        if (e.recurrence_rrule){
          alert('Resizing recurring event instances is not implemented in this MVP. Edit the series instead.');
          info.revert();
          return;
        }
        apiUpdateEvent(e.id, {
          end_at: info.event.end ? info.event.end.toISOString() : null
        });
      }
    });
    calendar.render();

    // Custom toolbar buttons
    document.getElementById('btnPrev').addEventListener('click',()=>{ calendar.prev(); calendar.refetchEvents(); });
    document.getElementById('btnNext').addEventListener('click',()=>{ calendar.next(); calendar.refetchEvents(); });
    document.getElementById('btnToday').addEventListener('click',()=>{ calendar.today(); calendar.refetchEvents(); });
    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('bg-slate-900','text-white'));
        btn.classList.add('bg-slate-900','text-white');
        const v = btn.getAttribute('data-view');
        calendar.changeView(v);
        const ui = getUIState();
        setUIState({...ui, view: v});
        calendar.refetchEvents();
      });
    });

    // Keep calendar title updated on view change
    calendar.on('datesSet', () => {
      document.getElementById('calTitle').textContent = calendar.view.title;
    });

    // -----------------------------
    // Quick View Modal
    // -----------------------------
    const modalQuick = setupModal('modalQuick');
    let qvEvent; // FullCalendar EventApi

    function openQuickView(eventApi){
      qvEvent = eventApi;
      const e = qvEvent.extendedProps;
      document.getElementById('qvTitle').textContent = eventApi.title || '(Untitled)';
      document.getElementById('qvWhen').textContent = humanWhen(eventApi.start, eventApi.end, eventApi.allDay, getUIState().locale || 'en');
      const tagWrap = document.getElementById('qvTags'); tagWrap.innerHTML='';
      (e.tags || []).forEach(tag => {
        const s = document.createElement('span');
        s.className = 'ev-pill';
        s.textContent = tag;
        tagWrap.appendChild(s);
      });
      document.getElementById('qvPeople').textContent = (e.attendees && e.attendees.length)
        ? '👤 ' + e.attendees.map(a=>a.name).filter(Boolean).join(', ')
        : '';
      document.getElementById('qvDesc').textContent = e.description || '';
      document.getElementById('qvLoc').textContent  = e.location ? `📍 ${e.location}` : '';
      modalQuick.open();
    }

    document.getElementById('qvEdit').addEventListener('click', () => {
      modalQuick.close();
      const e = qvEvent.extendedProps;
      openEditModal({
        id: e.id,
        calendar_id: e.calendar_id,
        title: qvEvent.title,
        start: qvEvent.start,
        end: qvEvent.end,
        allDay: qvEvent.allDay,
        tags: e.tags || [],
        attendees: e.attendees || [],
        description: e.description || '',
        location: e.location || '',
        recurrence_rrule: e.recurrence_rrule || '',
        timezone: e.timezone || DEFAULT_TZ
      });
    });

    document.getElementById('qvDelete').addEventListener('click', () => {
      const e = qvEvent.extendedProps;
      if (confirm('Delete this event?')){
        if (e.series_id && e.instance_start){
          alert('Deleting a single occurrence of a recurring series is not implemented in this MVP.');
          return;
        }
        apiDeleteEvent(e.id);
        modalQuick.close();
        calendar.refetchEvents();
      }
    });

    // -----------------------------
    // Create/Edit Modal
    // -----------------------------
    const modalEdit = setupModal('modalEdit');
    const editForm = document.getElementById('editForm');

    document.getElementById('btnAdd').addEventListener('click', () => {
      openEditModal({});
    });

    function openEditModal(data){
      // Populate fields
      editForm.reset();
      const f = formFields();
      f.id.value           = data.id || '';
      f.calendar_id.value  = data.calendar_id || DEFAULT_CALENDAR_ID;
      f.title.value        = data.title || '';
      f.timezone.value     = data.timezone || DEFAULT_TZ;

      const locale = (getUIState().locale || 'en') === 'de' ? 'de-DE' : 'en-GB';
      const dtStart = data.start ? localDTString(data.start) : localDTString(new Date());
      const dtEnd   = data.end   ? localDTString(data.end)   : '';

      f.start_at.value = dtStart;
      f.end_at.value   = dtEnd;
      f.all_day.checked = !!data.allDay;

      renderEditTags(data.tags || []);
      const peopleNames = (data.attendees || []).map(a => a.name + (a.email?` <${a.email}>`:``)).join(', ');
      f.people.value = peopleNames;
      f.location.value = data.location || '';
      f.description.value = data.description || '';
      f.recurrence_rrule.value = data.recurrence_rrule || '';

      document.getElementById('editTitle').textContent = data.id ? 'Edit Event' : 'New Event';
      document.getElementById('editMeta').textContent = data.id ? `ID: ${data.id}` : '';
      modalEdit.open();
    }

    editForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const f = formFields();

      // Collect tags
      const selectedTags = Array.from(editTagsEl.querySelectorAll('input[type="checkbox"]:checked')).map(x => x.value);

      // Validate
      if (!f.title.value.trim()) { alert('Title is required'); f.title.focus(); return; }

      const allDay = f.all_day.checked;
      let startISO = toISOZ(new Date(f.start_at.value));
      let endISO   = f.end_at.value ? toISOZ(new Date(f.end_at.value)) : null;

      if (allDay){
        // Normalize all-day to 00:00 start, omit end or set end same-day 23:59?
        const s = new Date(f.start_at.value);
        s.setHours(0,0,0,0);
        startISO = toISOZ(s);
        if (endISO){
          const end = new Date(f.end_at.value);
          end.setHours(23,59,59,999);
          endISO = toISOZ(end);
        }
      }
      if (endISO && new Date(endISO) < new Date(startISO)) { alert('End must be after start'); return; }

      const attendees = parsePeople(f.people.value);

      const payload = {
        id: f.id.value || undefined,
        calendar_id: f.calendar_id.value || DEFAULT_CALENDAR_ID,
        title: f.title.value.trim(),
        description: f.description.value.trim() || null,
        location: f.location.value.trim() || null,
        start_at: startISO,
        end_at: endISO,
        all_day: allDay,
        tags: selectedTags,
        attendees,
        recurrence_rrule: f.recurrence_rrule.value.trim() || null,
        timezone: f.timezone.value || DEFAULT_TZ
      };

      if (payload.id){
        if (payload.recurrence_rrule && payload.id.includes('__')) {
          alert('Editing a single occurrence of a recurring series is not implemented in this MVP.');
          return;
        }
        apiUpdateEvent(payload.id, payload);
      } else {
        apiCreateEvent(payload);
      }
      modalEdit.close();
      calendar.refetchEvents();
    });

    function formFields(){
      const fd = new FormData(editForm);
      return {
        id:           editForm.elements['id'],
        calendar_id:  editForm.elements['calendar_id'],
        title:        editForm.elements['title'],
        start_at:     editForm.elements['start_at'],
        end_at:       editForm.elements['end_at'],
        all_day:      editForm.elements['all_day'],
        people:       editForm.elements['people'],
        description:  editForm.elements['description'],
        location:     editForm.elements['location'],
        recurrence_rrule: editForm.elements['recurrence_rrule'],
        timezone:     editForm.elements['timezone']
      };
    }

    // -----------------------------
    // Import / Export JSON
    // -----------------------------
    document.getElementById('btnExport').addEventListener('click', () => {
      const data = loadStore();
      const blob = new Blob([JSON.stringify(data, null, 2)], {type:'application/json'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'calendar-export.json';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    });

    document.getElementById('importFile').addEventListener('change', async (e) => {
      const file = e.target.files[0];
      if (!file) return;
      try {
        const text = await file.text();
        const json = JSON.parse(text);

        const cur = loadStore();
        // Merge calendars
        if (Array.isArray(json.calendars)){
          for (const c of json.calendars){
            if (!cur.calendars.find(x => x.id === c.id)) cur.calendars.push(c);
          }
        }
        // Merge events (assign UUID if missing)
        if (Array.isArray(json.events)){
          for (const ev of json.events){
            const id = ev.id || uuidv4();
            if (!cur.events.find(x => x.id === id)){
              cur.events.push(normalizeEvent({...ev, id}));
            }
          }
        }
        saveStore(cur);
        calendar.refetchEvents();
        e.target.value = '';
        alert('Import completed.');
      } catch(err){
        console.error(err);
        alert('Import failed. Is the file valid JSON export?');
      }
    });

    // -----------------------------
    // Helpers
    // -----------------------------
    function parsePeople(str){
      if (!str) return [];
      return str.split(',').map(s => s.trim()).filter(Boolean).map(s => {
        const m = s.match(/^(.*?)(?:<([^>]+)>)?$/);
        return {name: (m?.[1] || '').trim(), email: (m?.[2] || '').trim() || '', status: 'invited'};
      });
    }
    function localDTString(d){
      const dt = (d instanceof Date) ? d : new Date(d);
      const z = new Date(dt.getTime() - dt.getTimezoneOffset()*60000);
      return z.toISOString().slice(0,16); // "YYYY-MM-DDTHH:MM"
    }
    function humanWhen(start, end, allDay, locale='en'){
      const optDate = {weekday:'short', year:'numeric', month:'short', day:'numeric'};
      const optTime = {hour:'2-digit', minute:'2-digit'};
      const fmtD = new Intl.DateTimeFormat(locale, optDate);
      const fmtT = new Intl.DateTimeFormat(locale, optTime);
      if (allDay){
        if (end && start.toDateString() !== end.toDateString()){
          return `${fmtD.format(start)} – ${fmtD.format(end)} (all day)`;
        }
        return `${fmtD.format(start)} (all day)`;
      }
      if (end){
        const sameDay = start.toDateString() === end.toDateString();
        return sameDay
          ? `${fmtD.format(start)} ${fmtT.format(start)}–${fmtT.format(end)}`
          : `${fmtD.format(start)} ${fmtT.format(start)} – ${fmtD.format(end)} ${fmtT.format(end)}`;
      }
      return `${fmtD.format(start)} ${fmtT.format(start)}`;
    }

    // Basic modal util with focus trap + Esc/overlay close
    function setupModal(id){
      const root = document.getElementById(id);
      const overlay = root.querySelector('[data-close]') || root.firstElementChild;
      const closeButtons = root.querySelectorAll('[data-close]');
      function open(){
        root.classList.remove('hidden');
        root.classList.add('flex');
        trapFocus(root);
      }
      function close(){
        root.classList.add('hidden');
        root.classList.remove('flex');
        releaseFocus(root);
      }
      root.addEventListener('click', (e) => {
        if (e.target.hasAttribute('data-close')) close();
      });
      closeButtons.forEach(btn => btn.addEventListener('click', close));
      document.addEventListener('keydown', (e) => {
        if (!root.classList.contains('hidden') && e.key === 'Escape') close();
      });
      return { open, close, el: root };
    }

    // Simple focus trap
    function trapFocus(root){
      const focusable = root.querySelectorAll('a[href],button:not([disabled]),textarea,input[type="text"],input[type="datetime-local"],input[type="checkbox"],input:not([type]),select,[tabindex]:not([tabindex="-1"])');
      const first = focusable[0], last = focusable[focusable.length-1];
      function handler(e){
        if (e.key !== 'Tab') return;
        if (e.shiftKey && document.activeElement === first){ e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last){ e.preventDefault(); first.focus(); }
      }
      root.__trapHandler = handler;
      root.addEventListener('keydown', handler);
      (first || root).focus();
    }
    function releaseFocus(root){
      if (root.__trapHandler) root.removeEventListener('keydown', root.__trapHandler);
      root.__trapHandler = null;
    }
  </script>
</body>
</html>
