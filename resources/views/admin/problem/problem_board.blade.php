 @extends('admin.layouts.app')

@section('title','Tickets')

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  $cBlue      = '#74b2d4';
  $cGreen     = '#93c21c';
  $cGreenSoft = '#cfe09b';
  $cBlueSoft  = '#c0d8ea';
  $cDanger    = '#e2583e';
@endphp

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<style>
  :root{
    --t-blue: {{ $cBlue }};
    --t-green: {{ $cGreen }};
    --t-greenSoft: {{ $cGreenSoft }};
    --t-blueSoft: {{ $cBlueSoft }};
    --t-danger: {{ $cDanger }};
    --t-ink: #0b1220;
    --t-muted: #6b7280;
    --t-border: #e5e7eb;
    --t-bg: #f9fafb;
    --t-surface: #ffffff;
  }

  .content-wrapper { padding:0 !important; }
  .content-body { padding:0 !important; }
  .content-body .tk-layout{ padding: 0px 55px 0px 20px !important; }

  .tk-layout{ display:flex; flex-direction:column; gap:.7rem; }
  .tk-header{ display:flex; flex-wrap:wrap; gap:.6rem; justify-content:space-between; align-items:center; }
  .tk-title{ font-size:16px; font-weight:950; letter-spacing:.02em; color:var(--t-ink); margin:0; }
  .tk-sub{ font-size:12px; color:var(--t-muted); margin:0; }

  .tk-view-toggle{
    display:inline-flex; border-radius:999px; border:1px solid var(--t-border);
    overflow:hidden; background:#fff;
  }
  .tk-view-toggle button{
    font-size:12px; padding:.28rem .85rem; border:none; background:transparent; cursor:pointer;
    font-weight:950;
  }
  .tk-view-toggle button.is-active{ background:#020617; color:#fff; }

  .tk-surface{
    background:var(--t-surface);
    border:1px solid var(--t-border);
    border-radius:14px;
    padding:.85rem;
  }

  /* ✅ ANALYTICS */
  .tk-analytics{
    display:grid;
    grid-template-columns:repeat(5,minmax(0,1fr)); /* ✅ incl. Beendet */
    gap:.65rem;
    margin:.25rem 0 .75rem;
  }
  @media (max-width: 1200px){ .tk-analytics{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
  @media (max-width: 640px){ .tk-analytics{ grid-template-columns:1fr; } }

  .tk-metric{
    background:rgba(255,255,255,.85);
    border:1px solid var(--t-border);
    border-radius:16px;
    padding:.75rem .85rem;
    box-shadow:0 10px 25px rgba(15,23,42,0.06);
    display:flex;
    gap:.75rem;
    align-items:center;
    min-width:0;
  }
  .tk-metric .ico{
    width:38px; height:38px;
    border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    font-size:16px; font-weight:950;
    border:1px solid rgba(0,0,0,.06);
    flex:0 0 auto;
  }
  .tk-metric .txt{ min-width:0; }
  .tk-metric .k{
    font-size:11px;
    font-weight:950;
    letter-spacing:.08em;
    color:#64748b;
    text-transform:uppercase;
  }
  .tk-metric .v{
    font-size:18px;
    font-weight:950;
    color:var(--t-ink);
    margin-top:.1rem;
    line-height:1.1;
  }
  .tk-metric .s{
    font-size:11px;
    font-weight:900;
    color:#6b7280;
    margin-top:.15rem;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .ico.blue{ background:rgba(116,178,212,.16); color:#0b3c55; }
  .ico.green{ background:rgba(147,194,28,.16); color:#36510c; }
  .ico.gray{ background:rgba(148,163,184,.18); color:#0f172a; }
  .ico.red{ background:rgba(226,88,62,.12); color:#5b1b10; }

  .tk-tabs{ display:flex; flex-wrap:wrap; gap:.35rem; }
  .tk-chip{
    border-radius:999px; padding:.22rem .72rem;
    font-size:11px; font-weight:950;
    border:1px solid var(--t-border); background:#fff; cursor:pointer;
    display:inline-flex; align-items:center; gap:.35rem;
  }
  .tk-chip.is-active{ background:#020617; border-color:#020617; color:#fff; }
  .tk-chip small{ opacity:.75; font-weight:900; }

  .tk-filters{
    display:flex; flex-wrap:wrap; gap:.45rem; align-items:center;
    padding:.55rem .75rem; border-radius:9999px;
    background:#f9fafb; border:1px solid var(--t-greenSoft);
    box-shadow:0 10px 25px rgba(15,23,42,0.08);
    font-size:12px;
  }
  .tk-filters input[type="text"], .tk-filters select{
    min-width: 150px;
    border-radius:9999px;
    border:1px solid var(--t-greenSoft);
    padding:.33rem .85rem;
    font-size:12px; color:#374151; background:#fff; outline:none;
  }
  .tk-filters input[type="text"]:focus, .tk-filters select:focus{
    border-color: var(--t-green);
    box-shadow:0 0 0 2px rgba(147,194,28,.18);
  }

  .tk-btn{
    border-radius:9999px;
    border:1px solid var(--t-border);
    background:#fff;
    padding:.32rem .85rem;
    font-size:12px;
    font-weight:950;
    cursor:pointer;
    display:inline-flex; align-items:center; gap:.35rem;
    white-space:nowrap;
    transition:transform .12s ease, box-shadow .12s ease;
  }
  .tk-btn:hover{ transform:translateY(-1px); box-shadow:0 12px 30px rgba(15,23,42,.10); }
  .tk-btn-green{
    background:linear-gradient(135deg, var(--t-green), var(--t-greenSoft));
    color:#0b1220; border:none;
    box-shadow:0 12px 30px rgba(147,194,28,.28);
  }
  .tk-btn-primary{
    background:linear-gradient(135deg, var(--t-blue), var(--t-blueSoft));
    color:#0b1220; border:none;
    box-shadow:0 12px 30px rgba(116,178,212,.22);
  }

  .status-pill{
    padding:.12rem .55rem;
    border-radius:999px;
    font-weight:950;
    font-size:.7rem;
    letter-spacing:.05em;
    border:1px solid var(--t-border);
    display:inline-flex; align-items:center; gap:.25rem;
    white-space:nowrap;
  }
  .s-open{ background: rgba(147,194,28,.16); border-color: rgba(147,194,28,.55); color:#36510c;}
  .s-proc{ background: rgba(116,178,212,.20); border-color: rgba(116,178,212,.55); color:#0b3c55;}
  .s-end { background: rgba(192,216,234,.45); border-color: rgba(116,178,212,.45); color:#12324a;}
  .s-junk{ background: rgba(226,88,62,.14); border-color: rgba(226,88,62,.45); color:#5b1b10;}

  .avatar{
    width:26px;height:26px;border-radius:999px; object-fit:cover;
    border:2px solid #fff; overflow:hidden;
    box-shadow:0 6px 18px rgba(11,18,32,.12);
    background:#e5e7eb;
  }
  .avatar.sm{ width:20px; height:20px; }
  .tk-avatars{ display:flex; align-items:center; }
  .tk-avatars .avatar{ margin-left:-7px; }
  .tk-avatars .avatar:first-child{ margin-left:0; }

  .audit-row{
    display:flex; flex-wrap:wrap; gap:.55rem;
    margin-top:.4rem;
    padding-top:.4rem;
    border-top:1px dashed var(--t-border);
    font-size:11px;
    color:#64748b;
    font-weight:850;
  }
  .audit-item{ display:flex; gap:.4rem; align-items:center; min-width:0; }
  .audit-item .k{ font-weight:950; letter-spacing:.06em; color:#6b7280; }
  .audit-item .v{ color:#0f172a; font-weight:950; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:220px; }
  .audit-item .d{ color:#6b7280; font-weight:900; }

  .shell{
    border:1px solid var(--t-border);
    border-radius:18px;
    background:rgba(255,255,255,.75);
    overflow:hidden;
  }
  .shell-head{
    padding:.75rem .9rem;
    background:rgba(255,255,255,.80);
    border-bottom:1px solid var(--t-border);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:.75rem;
  }

  /* ✅ kanban (now 4 columns incl. end) */
  .tk-board{ display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; }
  @media (max-width: 1200px){ .tk-board{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
  @media (max-width: 700px){ .tk-board{ grid-template-columns:1fr; } }

  .tk-col{
    background:#f1f1f1;
    border-right:2px dashed #c5c5c5;
    padding:.55rem;
    display:flex; flex-direction:column;
    max-height: calc(100vh - 320px);
    border-radius:14px; overflow:hidden;
  }
  .tk-col-header{
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:.45rem;
    font-size:13px; text-transform:uppercase; letter-spacing:.08em;
    color:#1f2937; background: rgba(207,224,155,.65);
    padding:10px 12px; font-weight:950;
    border-radius:12px;
  }
  .tk-col-body{ flex:1; overflow-y:auto; padding-right:.25rem; display:flex; flex-direction:column; gap:.55rem; }

  .tk-card{
    border-radius:14px;
    border:1px solid #e5e7eb;
    background:#fff;
    padding:.7rem .8rem;
    font-size:12px;
    border-left:4px solid var(--t-blue);
    transition:box-shadow .15s ease, transform .15s ease;
  }
  .tk-card:hover{ box-shadow:0 18px 45px rgba(15,23,42,.10); transform:translateY(-1px); }

  .tk-card-head{ display:flex; justify-content:space-between; gap:.6rem; align-items:flex-start; }
  .tk-card-title{ font-weight:950; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .tk-card-meta{ margin-top:.25rem; color:#6b7280; font-weight:900; font-size:11px; }
  .tk-card-sub{ margin-top:.15rem; color:#6b7280; font-weight:850; font-size:11px; }
  .tk-card-foot{ margin-top:.55rem; display:flex; justify-content:space-between; align-items:center; gap:.5rem; flex-wrap:wrap; }

  /* ✅ CARD VIEW GRID: force 3 columns on large screens */
  .tk-cards-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:1rem;
  }
  @media (max-width: 1100px){ .tk-cards-grid{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
  @media (max-width: 700px){ .tk-cards-grid{ grid-template-columns:1fr; } }

  /* modals */
  .modal-backdrop{ position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:3000; display:none; }
  .modal{ position:fixed; inset:0; z-index:3001; display:none; align-items:center; justify-content:center; padding:16px; }
  .modal-card{
    width:100%; max-width:900px;
    border-radius:18px;
    background:#fff;
    box-shadow:0 20px 50px rgba(15,23,42,.25);
    border:1px solid var(--t-border);
    overflow:hidden;
  }
  .modal-head{
    padding:.75rem .9rem;
    border-bottom:1px solid var(--t-border);
    display:flex; align-items:center; justify-content:space-between; gap:.75rem;
  }
  .modal-body{ padding:.9rem; max-height:75vh; overflow:auto; display:flex; flex-direction:column; gap:.75rem; }
  .field{ display:flex; flex-direction:column; gap:.35rem; }
  .label{ font-size:11px; font-weight:950; letter-spacing:.08em; color:#6b7280; text-transform:uppercase; }
  .input{
    border:1px solid var(--t-border);
    border-radius:14px;
    padding:.55rem .7rem;
    font-size:12px;
    font-weight:900;
    outline:none;
    background:#fff;
  }
  .row{ display:grid; grid-template-columns:1fr 1fr; gap:.7rem; }
  @media (max-width: 720px){ .row{ grid-template-columns:1fr; } }

  .meta-box{
    border:1px solid var(--t-border);
    border-radius:16px;
    padding:.75rem;
    background:#fff;
  }
  .meta-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:.7rem;
  }
  @media (max-width: 900px){ .meta-grid{ grid-template-columns:1fr; } }
  .meta-item{ display:flex; gap:.6rem; align-items:center; }
  .meta-item .txt{ min-width:0; }
  .meta-item .name{ font-size:12px; font-weight:950; color:var(--t-ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .meta-item .sub{ font-size:11px; font-weight:900; color:#6b7280; }
  .ql-container{ border-bottom-left-radius:14px; border-bottom-right-radius:14px; }
  .ql-toolbar{ border-top-left-radius:14px; border-top-right-radius:14px; }
</style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="col-12">
        <h2 class="content-header-title float-left mb-0">TICKETS</h2>
        <div class="breadcrumb-wrapper col-12">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
          </ol>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="tk-layout">

        <div class="tk-header">
          <div class="tk-view-toggle">
            <button type="button" id="btnViewList"   data-view="list">Liste</button>
            <button type="button" id="btnViewCards"  data-view="cards">Karten</button>
            <button type="button" id="btnViewKanban" data-view="kanban">Board</button>
          </div>
        </div>

        {{-- ✅ Analytics --}}
        <div class="tk-analytics" id="analyticsRow">
          <div class="tk-metric"><div class="ico green">✓</div><div class="txt"><div class="k">Offen</div><div class="v" id="mOpen">0</div><div class="s">Tickets</div></div></div>
          <div class="tk-metric"><div class="ico blue">↻</div><div class="txt"><div class="k">In Bearbeitung</div><div class="v" id="mProc">0</div><div class="s">Tickets</div></div></div>
          <div class="tk-metric"><div class="ico gray">⎘</div><div class="txt"><div class="k">Beendet</div><div class="v" id="mEnd">0</div><div class="s">Tickets</div></div></div>
          <div class="tk-metric"><div class="ico red">!</div><div class="txt"><div class="k">Junk</div><div class="v" id="mJunk">0</div><div class="s">Tickets</div></div></div>
          <div class="tk-metric"><div class="ico gray">Σ</div><div class="txt"><div class="k">Gesamt</div><div class="v" id="mTotal">0</div><div class="s">Alle Status</div></div></div>
        </div>

        <div class="tk-surface">
          <div class="tk-header" style="gap:.6rem; align-items:flex-start;">
            <div class="tk-tabs" style="flex:1;">
              <button class="tk-chip" data-filter="mine"     id="chipMine">MEINE <small>({{ $counts['mine'] ?? 0 }})</small></button>
              <button class="tk-chip" data-filter="open"     id="chipOpen">OFFEN <small>({{ $counts['open'] ?? 0 }})</small></button>
              <button class="tk-chip" data-filter="progress" id="chipProg">IN BEARBEITUNG <small>({{ $counts['progress'] ?? 0 }})</small></button>
              <button class="tk-chip" data-filter="completed" id="chipEnd">BEENDET <small>({{ $counts['completed'] ?? 0 }})</small></button>
              <button class="tk-chip" data-filter="junk"     id="chipJunk">JUNK <small>({{ $counts['junk'] ?? 0 }})</small></button>
              <button class="tk-chip" data-filter="all"      id="chipAll">ALLE <small>({{ $counts['all'] ?? 0 }})</small></button>
            </div>

            <div class="tk-filters">
              <input id="qSearch" type="text" placeholder="Suche: Ticket#, Kunde, Produkt, Fehlercode..." />
              <select id="sortField">
                <option value="created_at">Erstellt</option>
                <option value="updated_at">Aktualisiert</option>
                <option value="ticket_no">Ticket#</option>
                <option value="status">Status</option>
                <option value="priority">Priorität</option>
                <option value="customer">Kunde</option>
                <option value="product">Produkt</option>
                <option value="start_date">Start</option>
              </select>
              <select id="sortOrder">
                <option value="desc">↓</option>
                <option value="asc">↑</option>
              </select>
              <select id="perPage">
                <option value="12">12</option>
                <option value="24">24</option>
                <option value="36">36</option>
              </select>
              <button id="btnClear" type="button" class="tk-btn tk-btn-green">Zurücksetzen</button>
              <a href="{{url('problem_create')}}" type="button" class="tk-btn tk-btn-green">Erstellen</a>
            </div>
          </div>

          <div style="margin-top:.85rem;">
            <div id="viewListCards">
              <div id="ticketsHost" style="min-height:220px;"></div>
              <div id="paginationHost" style="margin-top:.5rem;"></div>
            </div>

            <div id="viewKanban" style="display:none;">
              @php
                // ✅ Board INCL. completed
                $cols = [
                  ['key'=>'offen','label'=>'OFFEN','pill'=>'s-open'],
                  ['key'=>'process','label'=>'IN BEARBEITUNG','pill'=>'s-proc'],
                  ['key'=>'end','label'=>'BEENDET','pill'=>'s-end'],
                  ['key'=>'junk','label'=>'JUNK','pill'=>'s-junk'],
                ];
              @endphp

              <div class="tk-board">
                @foreach($cols as $col)
                  <div class="tk-col">
                    <div class="tk-col-header">
                      <span>{{ $col['label'] }}</span>
                      <div style="display:flex;align-items:center;gap:.5rem;">
                        <span class="status-pill {{ $col['pill'] }}">{{ $col['label'] }}</span>
                        <span style="font-weight:950;color:#4b5563;" id="count-{{ $col['key'] }}">0</span>
                        <button class="tk-btn" type="button" style="padding:.2rem .55rem;" onclick="Tickets.reloadKanban()">↻</button>
                      </div>
                    </div>
                    <div class="tk-col-body" data-kanban-col="{{ $col['key'] }}" id="col-{{ $col['key'] }}"></div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</div>

{{-- Fehler Modal --}}
<div class="modal-backdrop" id="errBackdrop"></div>
<div class="modal" id="errModal">
  <div class="modal-card">
    <div class="modal-head">
      <div>
        <div style="font-size:13px;font-weight:950;color:var(--t-ink);">Fehlerdetails</div>
        <div style="font-size:12px;font-weight:900;color:#6b7280;" id="errModalSubtitle">Ticket</div>
      </div>
      <button class="tk-btn" type="button" id="btnCloseErr">Schließen</button>
    </div>
    <div class="modal-body" id="errModalBody"></div>
  </div>
</div>

{{-- Ticket beenden Modal --}}
<div class="modal-backdrop" id="endBackdrop"></div>
<div class="modal" id="endModal">
  <div class="modal-card">
    <div class="modal-head">
      <div style="min-width:0;">
        <div style="font-size:13px;font-weight:950;color:var(--t-ink);">Ticket abschließen</div>
        <div style="font-size:12px;font-weight:900;color:#6b7280;" id="endModalSubtitle">Ticket</div>
      </div>
      <button class="tk-btn" type="button" id="btnCloseEnd">Schließen</button>
    </div>

    <div class="modal-body">
      <div class="meta-box">
        <div class="meta-grid" id="endMetaGrid"></div>
      </div>

      <div class="row">
        <div class="field">
          <div class="label">Enddatum</div>
          <input class="input" type="date" id="endDate" />
        </div>
        <div class="field">
          <div class="label">Status</div>
          <input class="input" type="text" value="END" readonly />
        </div>
      </div>

      <div class="field">
        <div class="label">Lösung / Durchführung</div>
        <div id="endSolutionEditor"></div>
        <input type="hidden" id="endSolutionHtml">
      </div>

      <div style="display:flex; justify-content:flex-end; gap:.5rem; flex-wrap:wrap;">
        <button class="tk-btn" type="button" id="btnCancelEnd">Abbrechen</button>
        <button class="tk-btn tk-btn-green" type="button" id="btnSaveEnd">Ticket abschließen</button>
      </div>

      <div id="endModalHint" style="display:none; font-size:12px; font-weight:950; color:#b42318; background:rgba(226,88,62,.10); border:1px solid rgba(226,88,62,.35); padding:.6rem .75rem; border-radius:14px;">
        Bitte Enddatum setzen und eine Lösung eintragen.
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
const Tickets = (() => {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  const state = {
    view: @json($viewMode ?? 'list'),
    filter: 'open',
    search: '',
    sort_field: 'created_at',
    sort_order: 'desc',
    per_page: 12,
    page: 1,
    kanban: { sortables: [] },
    endFlow: { ticket:null, ticketId:null, ticketNo:null, prevStatus:null, selectEl:null }
  };

  let endQuill = null;

  const els = {
    host: () => document.getElementById('ticketsHost'),
    pag: () => document.getElementById('paginationHost'),
    viewListCards: () => document.getElementById('viewListCards'),
    viewKanban: () => document.getElementById('viewKanban'),

    q: () => document.getElementById('qSearch'),
    btnClear: () => document.getElementById('btnClear'),
    sortField: () => document.getElementById('sortField'),
    sortOrder: () => document.getElementById('sortOrder'),
    perPage: () => document.getElementById('perPage'),

    // analytics
    mOpen: () => document.getElementById('mOpen'),
    mProc: () => document.getElementById('mProc'),
    mEnd:  () => document.getElementById('mEnd'),
    mJunk: () => document.getElementById('mJunk'),
    mTotal: () => document.getElementById('mTotal'),

    // error modal
    errBackdrop: () => document.getElementById('errBackdrop'),
    errModal: () => document.getElementById('errModal'),

    // end modal
    endBackdrop: () => document.getElementById('endBackdrop'),
    endModal: () => document.getElementById('endModal'),
    endSubtitle: () => document.getElementById('endModalSubtitle'),
    endMetaGrid: () => document.getElementById('endMetaGrid'),
    endDate: () => document.getElementById('endDate'),
    endSolutionHtml: () => document.getElementById('endSolutionHtml'),
    endHint: () => document.getElementById('endModalHint'),
    btnSaveEnd: () => document.getElementById('btnSaveEnd'),
    btnCancelEnd: () => document.getElementById('btnCancelEnd'),
    btnCloseEnd: () => document.getElementById('btnCloseEnd'),
  };

  function setActiveChip(){
    document.querySelectorAll('.tk-chip').forEach(c => c.classList.remove('is-active'));
    const chip = document.querySelector(`.tk-chip[data-filter="${state.filter}"]`);
    if (chip) chip.classList.add('is-active');
  }

  function setActiveViewButtons(){
    const map = {list:'btnViewList',cards:'btnViewCards',kanban:'btnViewKanban'};
    Object.values(map).forEach(id => document.getElementById(id)?.classList.remove('is-active'));
    document.getElementById(map[state.view])?.classList.add('is-active');
  }

  function showView(){
    setActiveViewButtons();
    if (state.view === 'kanban') {
      els.viewListCards().style.display = 'none';
      els.viewKanban().style.display = 'block';
      reloadKanban();
    } else {
      els.viewKanban().style.display = 'none';
      els.viewListCards().style.display = 'block';
      loadTickets(1);
    }
  }

  function qs(params){
    const u = new URLSearchParams(params);
    return u.toString();
  }

  function setAnalytics(open, proc, end, junk){
    const total = (open||0) + (proc||0) + (end||0) + (junk||0);
    els.mOpen().textContent = open ?? 0;
    els.mProc().textContent = proc ?? 0;
    els.mEnd().textContent  = end ?? 0;
    els.mJunk().textContent = junk ?? 0;
    els.mTotal().textContent = total;
  }

  async function loadTickets(page=1){
    state.page = page;
    setActiveChip();
    els.host().innerHTML = `<div style="padding:1.6rem 0;text-align:center;font-size:12px;font-weight:950;color:#6b7280;">Lade…</div>`;
    els.pag().innerHTML = '';

    const url = `{{ route('tickets.fetch') }}?` + qs({
      mode: state.view,
      filter: state.filter,   // ✅ now includes "completed"
      search: state.search,
      sort_field: state.sort_field,
      sort_order: state.sort_order,
      per_page: state.per_page,
      page: state.page,
    });

    const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }});
    const data = await res.json();

    els.host().innerHTML = data.html || '';
    els.pag().innerHTML = data.pagination || '';

    if (data.analytics){
      setAnalytics(data.analytics.open, data.analytics.process, data.analytics.end, data.analytics.junk);
    } else {
      setAnalytics(
        {{ (int)($counts['open'] ?? 0) }},
        {{ (int)($counts['progress'] ?? 0) }},
        {{ (int)($counts['completed'] ?? 0) }},
        {{ (int)($counts['junk'] ?? 0) }}
      );
    }

    els.pag().querySelectorAll('a').forEach(a=>{
      a.addEventListener('click', (e)=>{
        e.preventDefault();
        const p = new URL(a.href).searchParams.get('page') || 1;
        loadTickets(parseInt(p,10));
      });
    });

    bindTicketActions();
  }

  function statusPillClass(st){
    const s = (st||'offen').toLowerCase();
    if (s === 'process') return 's-proc';
    if (s === 'end') return 's-end';
    if (s === 'junk') return 's-junk';
    return 's-open';
  }

  async function updateStatus(id, payload){
    const res = await fetch(`{{ url('/tickets') }}/${id}/status`, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With':'XMLHttpRequest',
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json().catch(()=>({}));
    if (!res.ok || !data.success) throw new Error(data.message || 'Update fehlgeschlagen');
    return data;
  }

  // ---------------- END MODAL ----------------
  function ensureEndQuill(){
    if (endQuill) return;
    endQuill = new Quill('#endSolutionEditor', {
      theme: 'snow',
      placeholder: 'Lösung / Schritte / Ergebnis…',
      modules: {
        toolbar: [
          [{ header: [1,2,false] }],
          ['bold','italic','underline','strike'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['link','blockquote','code-block'],
          [{ color: [] }, { background: [] }],
          [{ align: [] }],
          ['clean']
        ]
      }
    });

    endQuill.on('text-change', () => {
      els.endSolutionHtml().value = endQuill.root.innerHTML || '';
    });
  }

  function todayYmd(){
    const d = new Date();
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0');
    return `${yyyy}-${mm}-${dd}`;
  }

  function fmtDateTime(str){
    try{
      const d = new Date(str);
      if (!isNaN(d.getTime())) {
        const yy = d.getFullYear();
        const mm = String(d.getMonth()+1).padStart(2,'0');
        const dd = String(d.getDate()).padStart(2,'0');
        const hh = String(d.getHours()).padStart(2,'0');
        const mi = String(d.getMinutes()).padStart(2,'0');
        return `${yy}-${mm}-${dd} ${hh}:${mi}`;
      }
    }catch(e){}
    return String(str || '');
  }

  function escapeHtml(str){
    return String(str ?? '').replace(/[&<>"']/g, s => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[s]));
  }

  function metaItem(label, user, dateStr){
    const u = user || {};
    const img = u.avatar_url || '';
    const name = u.name || '—';
    const when = dateStr ? fmtDateTime(dateStr) : '—';
    return `
      <div class="meta-item">
        ${img ? `<img class="avatar sm" src="${img}" alt="" onerror="this.style.display='none'">` : `<div class="avatar sm"></div>`}
        <div class="txt">
          <div class="sub">${escapeHtml(label)}</div>
          <div class="name">${escapeHtml(name)}</div>
          <div class="sub">${escapeHtml(when)}</div>
        </div>
      </div>
    `;
  }

  function openEndModal(ticketObj, ticketId, ticketNo, prevStatus, selectEl){
    state.endFlow.ticket = ticketObj || null;
    state.endFlow.ticketId = ticketId;
    state.endFlow.ticketNo = ticketNo;
    state.endFlow.prevStatus = prevStatus || 'offen';
    state.endFlow.selectEl = selectEl || null;

    ensureEndQuill();

    document.getElementById('endModalSubtitle').textContent = `Ticket #${ticketNo}`;
    els.endHint().style.display = 'none';
    els.endDate().value = todayYmd();

    endQuill.setContents([]);
    els.endSolutionHtml().value = '';

    const t = state.endFlow.ticket || {};
    els.endMetaGrid().innerHTML = [
      metaItem('Erstellt', t.created_by_user, t.created_at || null),
      metaItem('Bearbeitet',  t.updated_by_user, t.edit_date || t.updated_at || null),
      metaItem('Abschluss',   t.current_user || t.ended_by_user, els.endDate().value)
    ].join('');

    els.endBackdrop().style.display='block';
    els.endModal().style.display='flex';
  }

  function closeEndModal(rollback=false){
    els.endBackdrop().style.display='none';
    els.endModal().style.display='none';
    els.endHint().style.display='none';

    if (rollback && state.endFlow.selectEl){
      state.endFlow.selectEl.value = state.endFlow.prevStatus;
      state.endFlow.selectEl.setAttribute('data-prev-status', state.endFlow.prevStatus);
    }

    state.endFlow = { ticket:null, ticketId:null, ticketNo:null, prevStatus:null, selectEl:null };
  }

  async function saveEnd(){
    const id = state.endFlow.ticketId;
    const end_date = (els.endDate().value || '').trim();
    const solution_html = (els.endSolutionHtml().value || '').trim();
    const hasText = solution_html.replace(/<(.|\n)*?>/g,'').trim().length > 0;

    if (!end_date || !hasText){
      els.endHint().style.display='block';
      return;
    }

    els.endHint().style.display='none';
    els.btnSaveEnd().disabled = true;

    try{
      await updateStatus(id, { status: 'end', end_date, solution: solution_html });
      closeEndModal(false);

      if (state.view === 'kanban') reloadKanban();
      else loadTickets(state.page || 1);
    }catch(err){
      alert(err.message || 'Speichern fehlgeschlagen');
      closeEndModal(true);
    }finally{
      els.btnSaveEnd().disabled = false;
    }
  }

  // ---------------- ERROR MODAL ----------------
  function openErrorModal(ticketNo, errors){
    document.getElementById('errModalSubtitle').textContent = `Ticket #${ticketNo}`;
    const body = document.getElementById('errModalBody');

    if (!errors || !errors.length){
      body.innerHTML = `<div class="meta-box"><div style="font-size:12px;font-weight:950;color:#374151;">Keine Fehler verknüpft.</div></div>`;
    } else {
      body.innerHTML = errors.map(er => `
        <div class="meta-box">
          <div class="label">Fehlercode</div>
          <div style="font-size:16px;font-weight:950;color:var(--t-ink);">${escapeHtml(er.error_code || '-')}</div>
          <div style="font-size:11px;font-weight:900;color:#6b7280;margin-top:.2rem;">${escapeHtml(er.problem_types || '')}</div>
          ${er.reason ? `<div style="margin-top:.55rem;" class="label">Ursache</div><div style="font-size:12px;font-weight:850;color:#374151;white-space:pre-line;">${escapeHtml(er.reason)}</div>` : ``}
          ${er.solution ? `<div style="margin-top:.55rem;" class="label">Lösung</div><div style="font-size:12px;font-weight:850;color:#374151;white-space:pre-line;">${escapeHtml(er.solution)}</div>` : ``}
        </div>
      `).join('');
    }

    els.errBackdrop().style.display='block';
    els.errModal().style.display='flex';
  }

  function closeErrorModal(){
    els.errBackdrop().style.display='none';
    els.errModal().style.display='none';
  }

  // ---------------- BIND ACTIONS ----------------
  function bindTicketActions(){
    document.querySelectorAll('[data-ticket-status]').forEach(sel=>{
      sel.addEventListener('change', async (e)=>{
        const id = sel.getAttribute('data-ticket-id');
        const nextStatus = e.target.value;
        const prevStatus = sel.getAttribute('data-prev-status') || sel.value;

        const rawTicket = sel.getAttribute('data-ticket-json') || null;
        const ticketObj = rawTicket ? JSON.parse(rawTicket) : null;

        if (String(nextStatus).toLowerCase() === 'end'){
          const ticketNo = sel.getAttribute('data-ticket-no') || (ticketObj?.ticket_no ?? '');
          openEndModal(ticketObj, id, ticketNo, prevStatus, sel);
          return;
        }

        sel.disabled = true;
        try{
          await updateStatus(id, { status: nextStatus });
          sel.setAttribute('data-prev-status', nextStatus);

          const pill = document.querySelector(`[data-pill-id="${id}"]`);
          if (pill){
            pill.className = `status-pill ${statusPillClass(nextStatus)}`;
            pill.textContent = nextStatus.toUpperCase();
          }

          if (state.view === 'kanban') reloadKanban();
          else loadTickets(state.page || 1);
        }catch(err){
          alert(err.message);
          sel.value = prevStatus;
        }finally{
          sel.disabled = false;
        }
      });

      if (!sel.getAttribute('data-prev-status')) sel.setAttribute('data-prev-status', sel.value);
    });

    document.querySelectorAll('[data-open-errors]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const ticketNo = btn.getAttribute('data-ticket-no');
        const raw = btn.getAttribute('data-errors-json') || '[]';
        const errors = JSON.parse(raw);
        openErrorModal(ticketNo, errors);
      });
    });
  }

  // ---------------- KANBAN ----------------
  function destroySortables(){
    state.kanban.sortables.forEach(s => { try{ s.destroy(); }catch(e){} });
    state.kanban.sortables = [];
  }

  function kanCard(t){
    const st = (t.status || 'offen').toLowerCase();
    const pill = statusPillClass(st);
    const errCount = (t.errors || []).length;

    const emps = (t.employees || []).slice(0,3).map(e => {
      const src = e.avatar_url || '';
      return `<img class="avatar" src="${src}" alt="" onerror="this.style.display='none'">`;
    }).join('');

    const more = (t.employees || []).length > 3
      ? `<div class="avatar" style="display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:950;color:#374151;background:#fff;">+${(t.employees||[]).length-3}</div>`
      : '';

    const created = t.created_by_user || null;
    const edited  = t.updated_by_user || null;

    // ✅ show "END" tickets too
    return `
      <div class="tk-card kan-card" data-ticket-id="${t.id}">
        <div class="tk-card-head">
          <a href="${t.profile_url}" class="tk-card-title" style="color:var(--t-ink); text-decoration:none;">#${t.ticket_no}</a>
          <span class="status-pill ${pill}" data-pill-id="${t.id}">${(t.status||'offen').toUpperCase()}</span>
        </div>

        <div class="tk-card-meta">${escapeHtml(t.customer || '')}</div>
        <div class="tk-card-sub">${escapeHtml(t.product || '')}</div>

        <div class="audit-row">
          <div class="audit-item">
            ${created?.avatar_url ? `<img class="avatar sm" src="${created.avatar_url}" alt="" onerror="this.style.display='none'">` : `<div class="avatar sm"></div>`}
            <span class="k">ERSTELLT</span>
            <span class="v">${escapeHtml(created?.name || '—')}</span>
            <span class="d">${escapeHtml(t.created_at ? fmtDateTime(t.created_at) : '—')}</span>
          </div>
          <div class="audit-item">
            ${edited?.avatar_url ? `<img class="avatar sm" src="${edited.avatar_url}" alt="" onerror="this.style.display='none'">` : `<div class="avatar sm"></div>`}
            <span class="k">BEARBEITET</span>
            <span class="v">${escapeHtml(edited?.name || '—')}</span>
            <span class="d">${escapeHtml(t.edit_date || t.updated_at ? fmtDateTime(t.edit_date || t.updated_at) : '—')}</span>
          </div>
        </div>

        <div class="tk-card-foot">
          <div class="tk-avatars" title="Mitarbeiter">${emps}${more}</div>

          <button class="tk-btn" type="button" style="padding:.22rem .6rem;"
            data-open-errors="1"
            data-ticket-no="${t.ticket_no}"
            data-errors-json='${escapeHtml(JSON.stringify(t.errors || []))}'>
            Fehler (${errCount})
          </button>

          <select class="input" style="padding:.38rem .6rem; border-radius:999px; font-size:11px;"
            data-ticket-status="1"
            data-ticket-id="${t.id}"
            data-ticket-no="${t.ticket_no}"
            data-ticket-json='${escapeHtml(JSON.stringify(t))}'
            data-prev-status="${st}">
            <option value="offen" ${st==='offen'?'selected':''}>OFFEN</option>
            <option value="process" ${st==='process'?'selected':''}>IN BEARBEITUNG</option>
            <option value="end" ${st==='end'?'selected':''}>BEENDET</option>
            <option value="junk" ${st==='junk'?'selected':''}>JUNK</option>
          </select>
        </div>
      </div>
    `;
  }

  async function reloadKanban(){
    destroySortables();

    const url = `{{ route('tickets.kanban') }}?` + qs({
      filter: state.filter,  // ✅ supports completed/all/mine too
      search: state.search,
    });

    const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }});
    const data = await res.json();

    const cols = { offen:[], process:[], end:[], junk:[] };
    (Array.isArray(data) ? data : []).forEach(t=>{
      const st = (t.status || 'offen').toLowerCase();
      if (cols[st]) cols[st].push(t);
    });

    setAnalytics(cols.offen.length, cols.process.length, cols.end.length, cols.junk.length);

    ['offen','process','end','junk'].forEach(k=>{
      document.getElementById(`col-${k}`).innerHTML = (cols[k]||[]).map(kanCard).join('');
      document.getElementById(`count-${k}`).textContent = (cols[k]||[]).length;
    });

    bindTicketActions();

    ['offen','process','end','junk'].forEach(k=>{
      const el = document.getElementById(`col-${k}`);
      const s = new Sortable(el, {
        group: 'tickets',
        animation: 150,
        ghostClass: 'opacity-50',
        onEnd: async (evt)=>{
          const id = evt.item.getAttribute('data-ticket-id');
          const newStatus = evt.to.getAttribute('data-kanban-col');
          const prevStatus = evt.from.getAttribute('data-kanban-col');

          if (String(newStatus).toLowerCase() === 'end' && String(prevStatus).toLowerCase() !== 'end'){
            // ✅ only open end modal when moving INTO end (optional)
            const raw = evt.item.querySelector('[data-ticket-json]')?.getAttribute('data-ticket-json') || null;
            const t = raw ? JSON.parse(raw) : null;
            openEndModal(t, id, t?.ticket_no || id, prevStatus, null);
            reloadKanban();
            return;
          }

          try{
            await updateStatus(id, { status: newStatus });
            reloadKanban();
          }catch(err){
            alert(err.message);
            reloadKanban();
          }
        }
      });
      state.kanban.sortables.push(s);
    });
  }

  function init(){
    state.filter = 'open';
    setActiveChip();

    setAnalytics(
      {{ (int)($counts['open'] ?? 0) }},
      {{ (int)($counts['progress'] ?? 0) }},
      {{ (int)($counts['completed'] ?? 0) }},
      {{ (int)($counts['junk'] ?? 0) }}
    );

    document.querySelectorAll('[data-view]').forEach(b=>{
      b.addEventListener('click', ()=>{
        state.view = b.getAttribute('data-view');
        showView();
      });
    });

    document.querySelectorAll('.tk-chip[data-filter]').forEach(ch=>{
      ch.addEventListener('click', ()=>{
        state.filter = ch.getAttribute('data-filter');
        setActiveChip();
        if (state.view === 'kanban') reloadKanban();
        else loadTickets(1);
      });
    });

    let t=null;
    els.q().addEventListener('input', ()=>{
      clearTimeout(t);
      t = setTimeout(()=>{
        state.search = els.q().value.trim();
        if (state.view === 'kanban') reloadKanban();
        else loadTickets(1);
      }, 250);
    });

    els.btnClear().addEventListener('click', ()=>{
      els.q().value = '';
      state.search = '';
      if (state.view === 'kanban') reloadKanban();
      else loadTickets(1);
    });

    els.sortField().addEventListener('change', ()=>{ state.sort_field = els.sortField().value; state.page=1; if(state.view==='kanban') reloadKanban(); else loadTickets(1); });
    els.sortOrder().addEventListener('change', ()=>{ state.sort_order = els.sortOrder().value; state.page=1; if(state.view==='kanban') reloadKanban(); else loadTickets(1); });
    els.perPage().addEventListener('change', ()=>{ state.per_page = parseInt(els.perPage().value,10); state.page=1; if(state.view==='kanban') reloadKanban(); else loadTickets(1); });

    document.getElementById('btnCloseErr').addEventListener('click', closeErrorModal);
    els.errBackdrop().addEventListener('click', closeErrorModal);

    els.btnSaveEnd().addEventListener('click', saveEnd);
    els.btnCancelEnd().addEventListener('click', ()=> closeEndModal(true));
    els.btnCloseEnd().addEventListener('click', ()=> closeEndModal(true));
    els.endBackdrop().addEventListener('click', ()=> closeEndModal(true));

    document.addEventListener('keydown', (e)=>{
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase()==='k'){ e.preventDefault(); els.q().focus(); }
      if (e.key === 'Escape') { closeErrorModal(); closeEndModal(true); }
    });

    showView();
  }

  return { init, loadTickets, reloadKanban };
})();

document.addEventListener('DOMContentLoaded', Tickets.init);
</script>
@endsection
