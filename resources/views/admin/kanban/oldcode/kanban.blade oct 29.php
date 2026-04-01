@extends('admin.layouts.app')
@section('title') PROZESS @stop

@section('style')

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
 
<style>
.timeline {
    list-style: none;
    padding: 0;
    margin: 0;
    position: relative;
}
.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}
.timeline-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 18px;
    flex-shrink: 0;
    margin-right: 15px;
}
.timeline-content {
    flex-grow: 1;
    border-left: 2px solid #dee2e6;
    padding-left: 15px;
}
</style>


<style>
.card {
    background: white;
    padding: 15px;
    margin: 10px 0;
    border-left: 5px solid #74b2d4;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    cursor: grab;
    user-select: none;
    display: flex;
    flex-direction: column;
    gap: 4px;
}


.card .card-header {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    justify-content: space-between;
    border-bottom: none;
    padding: 1px;
    background-color: transparent;
    font-size: 13px;
    text-transform: uppercase;
}

.card .circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #b0d5f2;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    font-size: 11px;
    position: absolute;
    top: 2px;
    right: 3px;
}

.card.selected {
    background-color: #d1ecf1;
    border-left: 5px solid #17a2b8;
}

.card-actions {
    display: flex;
    justify-content: space-around;
    padding-top: 5px;
}

.card-actions button {
    border: none;
    background: none;
    cursor: pointer;
    font-size: 18px;
}

.card-actions button:hover {
    color: #94c11f;
}

.card-actions button {
    color: #b0d5f2;
}

.kanban-container {
    display: flex;
    gap: 0;
    overflow-x: auto;
    padding-bottom: 10px;
}

.column {
    background: #f1f1f1;
    width: 300px;
    height: 1000px;
    display: flex;
    flex-direction: column;
    border-right: 2px dashed #c0baba;
    position: relative;
}

.column h3 {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #95c11f;
    color: white;
    padding: 6px;
    font-size: 20px;
    text-align: center;
    text-transform: uppercase;
    font-weight: bold;
    margin: 0;
    flex-shrink: 0;
}

.column-content {
    overflow-y: auto;
    flex-grow: 1;
    padding: 10px;
}
</style>
<style>
  th.sortable { cursor: pointer; user-select: none; white-space: nowrap; }
  th.sortable .sort-icon { font-size: 0.8rem; opacity: .4; transition: transform .2s, opacity .2s; }
  th.sortable.active .sort-icon { opacity: 1; }
  th.sortable.desc .sort-icon { transform: rotate(180deg); }
</style>
<style>
.tooltip-trigger {
  cursor: pointer;
  display: inline-block;
}
.tooltip-trigger .custom-tooltip {
  position: absolute;
  bottom: 130%;
  left: 50%;
  transform: translateX(-50%);
  background-color: #93c21c;
  color: #fff;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s ease, transform 0.2s ease;
  z-index: 50;
}
.tooltip-trigger:hover .custom-tooltip {
  opacity: 1;
  transform: translateX(-50%) translateY(-2px);
}
.tooltip-trigger .custom-tooltip::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -4px;
  border-width: 4px;
  border-style: solid;
  border-color: #93c21c transparent transparent transparent;
}
</style>
<style>
.prio-dot, .new-dot, .late-dot { font-size:16px; vertical-align:middle; }
.prio-high   { color:#dc3545; }   /* rot */
.prio-normal { color:#93c21c; }   /* grün */
.prio-low    { color:#6c757d; }   /* grau */
.new-dot     { color:#ffc107; }   /* gelb */
.late-dot    { color:#f45b69; }   /* rötlich */
</style>

<style>
.summary-card { cursor:pointer; transition:transform .15s ease; }
.summary-card:hover { transform:translateY(-2px); }
.summary-card.active > div {
  border:2px solid #93c21c!important;
  box-shadow:0 0 6px rgba(147,194,28,.6);
  position:relative;
}
.summary-card.active::after {
  content:"ausgewählt";
  position:absolute;
  bottom:-18px;
  left:50%;
  transform:translateX(-50%);
  font-size:12px;
  color:#93c21c;
}
</style>


<style>
/* —— Run-state colors (left border) —— */
.card.status-playing  { border-left-color:#95c11f !important; } /* Play */
.card.status-paused   { border-left-color:#f3c12f !important; } /* Pause */
.card.status-stopped  { border-left-color:#c93a3a !important; } /* Stop  */

/* —— Status overlay (blur + label) —— */
.card .card-status-overlay{
  position: absolute;
  inset: 0;
  backdrop-filter: blur(2px);
  background: rgba(255,255,255,.55);
  border-radius: 8px;
  display: none;
  align-items: center;
  justify-content: center;
  text-align:center;
  padding: 10px;
  z-index: 3;
}
.card.card-has-overlay .card-status-overlay{ display:flex; }
.card .card-status-badge{
  display:inline-flex;
  gap:.4rem;
  align-items:center;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:.5px;
  font-size: .85rem;
  padding:.35rem .6rem;
  border-radius:14px;
  background:#eee;
  color:#555;
  box-shadow:0 1px 2px rgba(0,0,0,.08);
}
.card.status-paused  .card-status-badge{ background:#fff4d6; color:#8a6d00; }
.card.status-stopped .card-status-badge{ background:#ffe2e2; color:#8a1f1f; }

/* —— Action row (new media controls) —— */
.card-actions{
  display:flex;
  gap:.25rem;
  justify-content:space-between;
  align-items:center;
  padding-top:6px;
  position:relative;
  z-index:2;
}
.card-actions .left-actions,
.card-actions .right-actions{ display:flex; gap:.25rem; align-items:center; }

/* Base button */
.btn-icon{
  border:none;
  background:none;
  cursor:pointer;
  font-size:18px;
  line-height:1;
  padding:.35rem .45rem;
  border-radius:10px;
  color:#7b93a7;
  transition:transform .12s ease, background .15s ease, color .15s ease;
}
.btn-icon:hover{ transform:translateY(-1px); background:rgba(0,0,0,.04); }

/* Media buttons */
.btn-play  { color:#95c11f !important; }
.btn-pause {}
.btn-stop  {}

/* Active state highlight */
.btn-icon.is-active{ box-shadow:inset 0 0 0 2px rgba(0,0,0,.06); background:rgba(0,0,0,.03); }

/* Reuse your custom tooltip */
.tooltip-trigger { position: relative; display:inline-flex; }
.tooltip-trigger .custom-tooltip{
  position:absolute; bottom:130%; left:50%; transform:translateX(-50%);
  background-color:#93c21c; color:#fff; padding:4px 8px; border-radius:6px;
  font-size:12px; white-space:nowrap; opacity:0; pointer-events:none;
  transition:opacity .15s ease, transform .15s ease; z-index:50;
}
.tooltip-trigger:hover .custom-tooltip{ opacity:1; transform:translateX(-50%) translateY(-2px); }
.tooltip-trigger .custom-tooltip::after{
  content:''; position:absolute; top:100%; left:50%; margin-left:-4px;
  border-width:4px; border-style:solid; border-color:#93c21c transparent transparent transparent;
}

/* Ensure cards can host overlays */
.card{ position:relative; }

/* Let clicks pass through the overlay so Play works */
.card .card-status-overlay{
  pointer-events: none;             /* <-- crucial */
  z-index: 3;                       /* overlay layer */
}

/* Keep header (customer name) and action buttons readable & clickable */
.card .card-header{ 
  position: relative; 
  z-index: 5;                       /* above overlay */
}

/* Your buttons were z-index:2; raise above overlay */
.card-actions{
  position: relative;
  z-index: 5;                       /* above overlay */
}

.card .card-status-overlay{
  background: rgba(255,255,255,.35); /* lighter */
  backdrop-filter: blur(1.5px);
}
.card .card-status-overlay{ pointer-events:none; }
 
 .count-badge{
  background:#93c21c; color:#fff; font-size:.8rem; padding:2px 8px;
  border-radius:12px; margin-left:.5rem; font-weight:600;
}
.column h3{ display:flex; align-items:center; justify-content:space-between; }

</style>

<style>
/* App layout: main + right action rail */
.pro-layout{
  display:grid;
  grid-template-columns: minmax(0,1fr) 56px; /* main + rail */
  gap: 0.75rem;
  align-items: start;
}
@media (max-width: 992px){
  .pro-layout{ grid-template-columns: 1fr 48px; }
}

/* Right action rail */
.pro-rail{
  display:flex; flex-direction:column; gap:.5rem; align-items:center;
  position: sticky; top: 84px; /* stick under header */
  padding-top:.25rem;
}
.rail-btn{
  width:44px; height:44px; border:none; border-radius:12px;
  background:#f3f4f6; color:#333; display:grid; place-items:center;
  box-shadow:0 1px 2px rgba(0,0,0,.08); cursor:pointer;
  transition: transform .12s ease, background .12s ease;
}
.rail-btn:hover{ transform: translateY(-1px); background:#eef1f6; }
.rail-btn .feather{ width:20px; height:20px; }

/* Generic right drawer (slide-over) */
.drawer{
  position: fixed; inset: 0 0 0 auto; width: 420px; max-width: 90vw;
  transform: translateX(100%); transition: transform .22s ease;
  background:#fff; box-shadow: -12px 0 30px rgba(0,0,0,.12); z-index: 1080;
  display:flex; flex-direction:column;
}
.drawer.open{ transform: translateX(0); }
.drawer-header{
  display:flex; align-items:center; justify-content:space-between;
  padding:12px 14px; border-bottom:1px solid #e5e7eb;
}
.drawer-body{ padding:14px; overflow:auto; }

/* Backdrop */
.drawer-backdrop{
  position: fixed; inset: 0; background: rgba(0,0,0,.25);
  opacity: 0; pointer-events: none; transition: opacity .2s ease; z-index: 1075;
}
.drawer-backdrop.show{ opacity:1; pointer-events: auto; }

/* Make summary cards look good in the drawer */
#summaryStats .summary-card > .border{ padding:.6rem .75rem!important; }
/* Hide legacy summary/filter rows in the page layout (we'll use drawer versions) */
.hide-on-modern{ display:none!important; }
</style>



@endsection
@section('content')

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDEN ÜBERSICHT</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">DASHBOARD</a>
                                </li>
                                
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="content-body">
               <div class="row text-center" id="summaryStats" style="justify-content:center">
                  <div id="cardEmployees" class="col-md-1 summary-card">
                    <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                      <strong class="text-primary">Verantwortliche</strong>
                      <div id="totalEmployees" class="h4">{{ $totalEmployees }}</div>
                    </div>
                  </div>

                  <div id="cardProducts" class="col-md-1 summary-card">
                    <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                      <strong class="text-primary">Produkt</strong>
                      <div id="totalProduct" class="h4">{{ $totalProducts }}</div>
                    </div>
                  </div>

                  <div id="cardCustomers" class="col-md-1 summary-card">
                    <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                      <strong class="text-primary">Kunde</strong>
                      <div id="totalCustomer" class="h4">{{ $totalCustomers }}</div>
                    </div>
                  </div>

                  <div id="cardAnfragen" class="col-md-1 summary-card">
                    <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                      <strong class="text-primary">Nachfrage</strong>
                      <div id="totalAnfrage" class="h4">{{ $tabCounts['kanban'] }}</div>
                    </div>
                  </div>

                  <div id="cardOffen" class="col-md-1 summary-card">
                    <div class="border rounded py-2 bg-orange text-white" style="background:#f49f43;color:white!important;">
                      <strong>Offen</strong>
                      <div id="statusOffen" class="h4 text-white">
                        {{ $statusCounts['offen'] }} <small>({{ $statusPercentages['offen'] }}%)</small>
                      </div>
                    </div>
                  </div>

                  <div id="cardZusage" class="col-md-1 summary-card">
                    <div class="border rounded py-2 bg-primary text-white">
                      <strong>Zusage</strong>
                      <div id="statusZusage" class="h4 text-white">
                        {{ $statusCounts['zusage'] }} <small>({{ $statusPercentages['zusage'] }}%)</small>
                      </div>
                    </div>
                  </div>

                  <div id="cardAbsage" class="col-md-1 summary-card">
                    <div class="border rounded py-2 bg-danger text-white">
                      <strong>Absage</strong>
                      <div id="statusAbsage" class="h4 text-white">
                        {{ $statusCounts['absage'] }} <small>({{ $statusPercentages['absage'] }}%)</small>
                      </div>
                    </div>
                  </div>
                </div> 
              <div class="filter-container mb-2">
                <form id="kanbanFilterForm" class="row align-items-end g-2 mb-3 mt-2">
                  <div class="col-md-2">
                    <label for="customerFilter" class="form-label d-flex align-items-center">
                      Kunde
                      <span class="badge badge-secondary ml-2 d-none" id="countCustomers">{{ $totalCustomers ?? 0 }}</span>
                    </label>
                    <select name="customer" id="customerFilter" class="form-control select2">
                      <option value="">Alle</option>
                      @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-2">
                    <label for="stageFilter" class="form-label">Phase</label>
                    <select name="stage" id="stageFilter" class="form-control select2">
                      <option value="">Alle Phasen</option>
                      @foreach($stageNames as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-2">
                    <label for="employeeFilter" class="form-label d-flex align-items-center">
                      Mitarbeiter
                      <span class="badge badge-secondary ml-2 d-none" id="countEmployees">{{ $totalEmployees ?? 0 }}</span>
                    </label>
                    <select name="employee" id="employeeFilter" class="form-control select2">
                      <option value="">Alle</option>
                      @foreach ($employees as $employee)
                        <option value="{{ $employee->name }}">{{ $employee->name }} {{ $employee->lastname }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-2">
                    <label for="departmentFilter" class="form-label d-flex align-items-center">
                      Abteilung
                      <span class="badge badge-secondary ml-2 d-none" id="countDepartments">{{ $totalDepartments ?? 0 }}</span>
                    </label>
                    <select name="department" id="departmentFilter" class="form-control select2">
                      <option value="">Alle</option>
                      @foreach ($departments as $department)
                        <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-2">
                    <label for="productFilter" class="form-label d-flex align-items-center">
                      Produkt
                      <span class="badge badge-secondary ml-2 d-none" id="countProducts">{{ $totalProducts ?? 0 }}</span>
                    </label>
                    <select name="product" id="productFilter" class="form-control select2">
                      <option value="">Alle</option>
                      @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->article_group }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="col-md-1">
                    <label for="interestFilter" class="form-label">Interesse</label>
                    <select name="interest" id="interestFilter" class="form-control select2">
                      <option value="">Alle Interessen</option>
                      <option value="interest">Kaufinteresse</option>
                      <option value="intent">Kaufabsicht</option>
                      <option value="option">Kaufoption</option>
                    </select>
                  </div>

                  <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filtern</button>
                  </div>
                </form>

            </div>
            <section id="basic-tabs-components">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="cards overflow-hidden">
                            <div class="card-content">
                                  @php
                                    // Safe counts
                                    $countActive  = isset($totalLeads) ? (int)$totalLeads : (method_exists($leads, 'total') ? $leads->total() : 0);
                                    $countArchive = method_exists($archive ?? null, 'total') ? $archive->total() : 0;
                                    $countJunk    = method_exists($junk ?? null, 'total') ? $junk->total() : 0;
                                  @endphp

                                  <style>
                                    .tab-icon{ width:16px; height:16px; margin-right:6px; vertical-align:-2px; }
                                    .tab-badge{ margin-left:6px; }
                                  </style>

                                  <div class="card-body">
                                     <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                          <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-selected="true">
                                            {{-- Kanban icon --}}
                                            <img src="{{ asset('images/icons/kanban.svg') }}" alt="" width="16" class="mr-1">
                                            Kanban
                                            <span class="badge badge-secondary ml-1">{{ $tabCounts['kanban'] }}</span>
                                          </a>
                                        </li>

                                        <li class="nav-item">
                                          <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-selected="false">
                                            {{-- List icon --}}
                                            <img src="{{ asset('images/icons/list.svg') }}" alt="" width="16" class="mr-1">
                                            Liste
                                            <span class="badge badge-secondary ml-1">{{ $tabCounts['list'] }}</span>
                                          </a>
                                        </li>

                                        <li class="nav-item">
                                          <a class="nav-link" id="archive-tab" data-toggle="tab" href="#archive" role="tab" aria-selected="false">
                                            {{-- Archive icon --}}
                                            <img src="{{ asset('images/icons/archive.svg') }}" alt="" width="16" class="mr-1">
                                            Archiv
                                            <span class="badge badge-secondary ml-1">{{ $tabCounts['archive'] }}</span>
                                          </a>
                                        </li>

                                        <li class="nav-item">
                                          <a class="nav-link" id="junk-tab" data-toggle="tab" href="#junk" role="tab" aria-selected="false">
                                            {{-- Junk icon --}}
                                            <img src="{{ asset('images/icons/trash.svg') }}" alt="" width="16" class="mr-1">
                                            Junk
                                            <span class="badge badge-secondary ml-1">{{ $tabCounts['junk'] }}</span>
                                          </a>
                                        </li>
                                      </ul>


                                    <div class="tab-content">
                                      <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                        <div id="kanban" class="kanban-container"></div>
                                      </div>

                                      <div class="tab-pane active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                        @include('admin.kanban.partials.list')
                                      </div>

                                      <div class="tab-pane" id="archive" aria-labelledby="archive-tab" role="tabpanel">
                                        @include('admin.kanban.partials.archive')
                                      </div>

                                      <div class="tab-pane" id="junk" aria-labelledby="junk-tab" role="tabpanel">
                                        @include('admin.kanban.partials.junk')
                                      </div>
                                    </div>
                                  </div>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- END: Content-->
@stop

 @section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
 <script>
/* ============================================================================
   INIT: Toastr, Select2
   ============================================================================ */
$(document).ready(function() {
  @if(Session::has('update_msg')) toastr.success(@json(session('updated_msg'))); @endif
  @if(Session::has('save_msg'))   toastr.success(@json(session('save_msg')));   @endif
  @if(Session::has('delete_msg')) toastr.error(@json(session('delete_msg')));   @endif

  if ($.fn.select2) $('.select2').select2({ placeholder: 'Auswählen...', allowClear: true, width: '100%' });
});

/* ============================================================================
   CONSTANTS & STAGE MAPS
   ============================================================================ */
const EMP_SRC = "{{ asset('images/employee') }}";

// Keep keys the same for logic; display names only change in column headers.
const stageNames = {
  lead: "Lead",
  offer: "Angebot",
  deal: "Auftrag",
  project: "Montage",
  completed: "Abschluss",
};
const stageAlias = {
  Lead: "lead",
  open: "lead",
  complete: "completed",
  abgeschlossen: "completed",
  reject: "junk",
  rejeck: "junk",
};
function canonicalStage(s) {
  const key = String(s || "").toLowerCase();
  return stageNames[key] ? key : (stageAlias[s] || stageAlias[key] || "lead");
}

/* ============================================================================
   DATE/PRIORITY HELPERS (Neu / >48h / Priorität)
   ============================================================================ */
function parseDt(v){ return v ? new Date(v) : null; }
function hoursSince(dt){ if(!dt) return Infinity; return (Date.now() - dt.getTime()) / 36e5; }
function priorityMeta(raw){
  const p = String(raw || 'normal').toLowerCase();
  if (p === 'high' || p === 'urgent') return {label:'Hoch', cls:'prio-high',   icon:'alert-triangle'};
  if (p === 'low')                     return {label:'Niedrig', cls:'prio-low',  icon:'arrow-down-circle'};
  return {label:'Normal', cls:'prio-normal', icon:'circle'};
}

/* ============================================================================
   SAFE DOM HELPERS + SUMMARY SYNC
   ============================================================================ */
function safeSetHTML(sel, html){ const el = document.querySelector(sel); if (el) el.innerHTML = html; }
function safeSetText(sel, text){ const el = document.querySelector(sel); if (el) el.textContent = text; }

function syncSummary(data){
  safeSetText("#totalEmployees", String(data?.totalEmployees ?? ""));
  safeSetText("#totalProduct",   String(data?.totalProducts  ?? ""));
  safeSetText("#totalCustomer",  String(data?.totalCustomers ?? ""));

  const offen  = `${data?.statusCounts?.offen  ?? 0} <small>(${data?.statusPercentages?.offen  ?? 0}%)</small>`;
  const zusage = `${data?.statusCounts?.zusage ?? 0} <small>(${data?.statusPercentages?.zusage ?? 0}%)</small>`;
  const absage = `${data?.statusCounts?.absage ?? 0} <small>(${data?.statusPercentages?.absage ?? 0}%)</small>`;
  safeSetHTML("#statusOffen",  offen);
  safeSetHTML("#statusZusage", zusage);
  safeSetHTML("#statusAbsage", absage);

  safeSetText("#countCustomers",   String(data?.totalCustomers   ?? ""));
  safeSetText("#countProducts",    String(data?.totalProducts    ?? ""));
  safeSetText("#countDepartments", String(data?.totalDepartments ?? ""));
  safeSetText("#countEmployees",   String(data?.totalEmployees   ?? ""));
}

/* ============================================================================
   GLOBAL STATE + QS BUILDERS
   ============================================================================ */
let lastKanbanLeads = []; // unpaginated current Kanban dataset
let lastFilterQS    = ""; // baseline filter (no page)
let currentSort     = { key: 'created_at', dir: 'asc' };

function buildFilterQS() {
  const form = document.getElementById("kanbanFilterForm");
  if (!form) return "";
  const p = new URLSearchParams(new FormData(form));
  p.delete('page');
  return p.toString();
}
function buildQSWithPage(page) {
  const p = new URLSearchParams(lastFilterQS);
  p.set('page', page);
  return p.toString();
}

/* ============================================================================
   FETCHERS
   ============================================================================ */
function fetchKanbanView(qs) {
  const url = `/lead/kanban/search${qs ? `?${qs}` : ""}`;
  fetch(url)
    .then(r => r.json())
    .then(leads => {
      lastKanbanLeads = Array.isArray(leads) ? leads : (leads.leads || []);
      renderKanban(lastKanbanLeads);
    })
    .catch(err => {
      console.error('Kanban fetch failed', err);
      Swal.fire('Fehler', 'Kanban-Daten konnten nicht geladen werden.', 'error');
    });
}

function fetchListView(qs) {
  const url = `/lead/kanban/ajax${qs ? `?${qs}` : ""}`;
  fetch(url)
    .then(r => r.json())
    .then(data => {
      const leads = Array.isArray(data) ? data : (data.leads || []);
      window.lastListData = leads;
      window.lastListMeta = data;
      updateListView(leads, data);
      renderPagination(data.pagination || null);
    })
    .catch(err => {
      console.error('List fetch failed', err);
      Swal.fire('Fehler', 'Listen-Daten konnten nicht geladen werden.', 'error');
    });
}

/* ============================================================================
   KANBAN RENDERING + DnD (with renamed headers + live counters)
   ============================================================================ */
function ensureColumnsExist() {
  const board = document.getElementById('kanban');
  if (!board) return;
  if (board.querySelector('.column')) return;

  // Only headers changed; keys stay identical
  const map = {
    lead: "Lead", // was "Lead"
    offer: "Verkauf",                 // was "Angebot"
    deal: "Auftrag",
    project: "Montage",
    completed: "Abschluss",
  };

  Object.entries(map).forEach(([id, title]) => {
    const col = document.createElement('div');
    col.className = 'column';
    col.id = id;
    col.ondrop = drop;
    col.ondragover = e => e.preventDefault();
    col.innerHTML = `
      <h3>
        <span class="col-title">${title}</span>
        <span class="count-badge" data-count-for="${id}">0</span>
      </h3>
      <div class="column-content"></div>`;
    board.appendChild(col);
  });
}

function updateColumnCounts(){
  document.querySelectorAll('.column').forEach(col => {
    const body = col.querySelector('.column-content');
    const n = body ? body.querySelectorAll('.card').length : 0;
    const badge = col.querySelector('.count-badge');
    if (badge) badge.textContent = n;
  });
}

function getColumnContentByStage(stageKey){
  const col = document.getElementById(stageKey);
  return col ? col.querySelector('.column-content') : null;
}

let selectedCards = new Set();
function selectCard(e, card){
  if (e.ctrlKey || e.metaKey) {
    card.classList.toggle("selected");
    selectedCards.has(card.id) ? selectedCards.delete(card.id) : selectedCards.add(card.id);
  } else {
    document.querySelectorAll(".card.selected").forEach(c => c.classList.remove("selected"));
    selectedCards.clear();
    card.classList.add("selected");
    selectedCards.add(card.id);
  }
}
function drag(e){
  let ids = Array.from(selectedCards);
  if (!selectedCards.has(e.target.id)) ids = [e.target.id];
  e.dataTransfer.setData("text", JSON.stringify(ids));
}

/* ==================== Run-state helpers (Play/Pause/Stop with reason) ==================== */
const RUN_STATES = { playing:'playing', paused:'paused', stopped:'stopped' };
const RUN_CLASS  = { playing:'status-playing', paused:'status-paused', stopped:'status-stopped' };
const RUN_LABEL  = { playing:'Aktiv', paused:'Pausiert', stopped:'Gestoppt' };
const RUN_ICON   = { playing:'icon-play', paused:'icon-pause',  stopped:'icon-square' };
const RUN_BADGE  = { playing:'success',   paused:'warning text-dark', stopped:'danger' };

function applyRunStateUI(cardEl, state) {
  if (!cardEl) return;
  const s = (state && RUN_CLASS[state]) ? state : RUN_STATES.playing;

  const cl = cardEl.classList;
  cl.remove('status-playing', 'status-paused', 'status-stopped', 'card-has-overlay');
  cl.add(RUN_CLASS[s]);

  const overlay = cardEl.querySelector('.card-status-overlay');
  if (overlay) {
    overlay.style.pointerEvents = 'none';
    if (s === RUN_STATES.paused || s === RUN_STATES.stopped) {
      cl.add('card-has-overlay');
      overlay.style.display = 'flex';
      overlay.innerHTML = `
        <span class="card-status-badge" data-run-badge data-id="${cardEl.dataset.leadProductId}" data-state="${s}">
          <i class="feather ${s === RUN_STATES.paused ? 'icon-pause' : 'icon-square'}"></i>
          ${s === RUN_STATES.paused ? 'Pause' : 'Stopp'}
        </span>`;
      overlay.setAttribute('aria-hidden', 'false');
    } else {
      overlay.style.display = 'none';
      overlay.innerHTML = '';
      overlay.setAttribute('aria-hidden', 'true');
    }
  }

  const btnPlay  = cardEl.querySelector('[data-run="playing"]');
  const btnPause = cardEl.querySelector('[data-run="paused"]');
  const btnStop  = cardEl.querySelector('[data-run="stopped"]');
  [btnPlay, btnPause, btnStop].forEach(b => b && b.classList.remove('is-active'));
  if (s === RUN_STATES.playing && btnPlay)  btnPlay.classList.add('is-active');
  if (s === RUN_STATES.paused  && btnPause) btnPause.classList.add('is-active');
  if (s === RUN_STATES.stopped && btnStop)  btnStop.classList.add('is-active');

  cardEl.dataset.runState = s;
}

async function askRunReason(state){
  const titles = { playing:'Grund für Start', paused:'Grund für Pause', stopped:'Grund für Stopp' };
  const { isConfirmed, value } = await Swal.fire({
    title: titles[state] || 'Grund angeben',
    input: 'textarea',
    inputLabel: 'Bitte Grund eingeben',
    inputPlaceholder: '…',
    inputAttributes: { 'aria-label': 'Grund' },
    inputValidator: (v) => (!v || !v.trim() ? 'Bitte Grund eingeben' : undefined),
    showCancelButton: true,
    confirmButtonText: 'Speichern'
  });
  return isConfirmed ? value.trim() : null;
}

async function persistRunState(leadProductId, state, reason){
  try {
    const res = await fetch(`/lead-product/progress/${leadProductId}/${state}`, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ reason })
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json().catch(() => ({}));
    if (data && data.success === false) throw new Error(data.message || 'Save failed');
    return data;
  } catch (e){
    console.warn('Progress persist failed:', e.message);
    return { success:false, message:e.message };
  }
}

async function setCardRunState(cardId, state){
  const card = document.getElementById(cardId);
  if (!card) return;

  const reason = await askRunReason(state);
  if (!reason) return;

  const prev = card.dataset.runState || 'playing';
  applyRunStateUI(card, state);

  const result = await persistRunState(card.dataset.leadProductId, state, reason);
  if (!result || result.success === false){
    applyRunStateUI(card, prev);
    Swal.fire('Fehler', result?.message || 'Konnte Grund nicht speichern.', 'error');
    return;
  }
  card.dataset.runReason = reason;
}

/* ============================================================================
   ADD/RENDER CARDS
   ============================================================================ */
function addCard(stageKey, item) {
  const content = getColumnContentByStage(stageKey);
  if (!content) return;

  const card = document.createElement("div");
  card.className = "card";
  card.id = "card-" + Math.random().toString(36).slice(2, 10);
  card.draggable = true;
  card.ondragstart = drag;
  card.onclick = e => selectCard(e, card);

  const employee = item?.employee && item.employee.employee_id ? item.employee : null;
  const employeeHtml = (employee?.name && employee?.image)
    ? `<ul class="list-unstyled users-list m-0 d-flex align-items-center">
         <li class="avatar pull-up" data-toggle="tooltip" title="${employee.lastname ?? ''} ${employee.name}">
           <img class="media-object rounded-circle" src="${EMP_SRC}/${employee.image}" height="30" width="30">
         </li>
       </ul>`
    : `<small>No employee assigned</small>`;

  card.dataset.customerId    = item?.customer_id ?? "";
  card.dataset.alternativeId = item?.alternative_id ?? "";
  card.dataset.productId     = item?.product_id ?? "";
  card.dataset.service       = item?.service ?? "";
  card.dataset.employeeId    = employee?.employee_id ?? 0;
  card.dataset.leadProductId = item?.lead_product_id ?? "";
  card.dataset.serviceId     = item?.service_id ?? 0;
  card.dataset.departmentId  = item?.department_id ?? 0;

  const fullName  = `${item?.customer_name ?? ""} ${item?.customer_lastname ?? ""}`.trim();
  const address   = [item?.street, item?.postcode, item?.city].filter(Boolean).join(", ");
  const updated   = new Date(item?.updated_at ?? Date.now()).toLocaleDateString("de-DE");

  card.innerHTML = `
    <div class="card-status-overlay"></div>

    <div class="card-header">
      <strong>${fullName || 'Unbekannt'}</strong>
      <div class="circle">${item?.initial ?? ''}</div>
    </div>

    <div>
      <small><i class="feather icon-calendar"></i> ${updated}</small><br>
      <small>${address}</small>
    </div>

    <div class='employeeList'>${employeeHtml}</div>

    <div class='card-actions'>
      <div class="left-actions">
        <span class="tooltip-trigger">
          <button class="btn-icon btn-play" data-run="playing" onclick="setCardRunState('${card.id}','playing')">
            <i class="feather icon-play"></i>
          </button>
          <span class="custom-tooltip">Play</span>
        </span>

        <span class="tooltip-trigger">
          <button class="btn-icon btn-pause" data-run="paused" onclick="setCardRunState('${card.id}','paused')">
            <i class="feather icon-pause"></i>
          </button>
          <span class="custom-tooltip">Pause</span>
        </span>

        <span class="tooltip-trigger">
          <button class="btn-icon btn-stop" data-run="stopped" onclick="setCardRunState('${card.id}','stopped')">
            <i class="feather icon-square"></i>
          </button>
          <span class="custom-tooltip">Stop</span>
        </span>
      </div>

      <div class="right-actions">
        <span class="tooltip-trigger">
          <button class="btn-icon" onclick="visitProfile('${card.dataset.customerId}')"><i class="feather icon-eye"></i></button>
          <span class="custom-tooltip">Profil</span>
        </span>

        <span class="tooltip-trigger">
          <button class="btn-icon" onclick="editCard('${card.id}')"><i class="feather icon-edit"></i></button>
          <span class="custom-tooltip">Bearbeiten</span>
        </span>

        <span class="tooltip-trigger">
          <button class="btn-icon" onclick="deleteCard('${card.id}')"><i class="feather icon-trash"></i></button>
          <span class="custom-tooltip">Löschen</span>
        </span>

        ${stageKey === 'completed'
          ? `<span class="tooltip-trigger">
              <button class="btn-icon" onclick="archiveCard('${card.id}')"><i class="feather icon-archive"></i></button>
              <span class="custom-tooltip">Archivieren</span>
            </span>`
          : ``}
      </div>
    </div>
  `;

  applyRunStateUI(card, item?.work_status || RUN_STATES.playing);
  content.appendChild(card);
}

function renderKanban(leads) {
  const board = document.getElementById("kanban");
  if (!board) return;

  ensureColumnsExist();
  board.querySelectorAll(".column .column-content").forEach(el => el.innerHTML = "");

  for (const item of leads) {
    const rawStage = String(item?.stage || "").toLowerCase();
    if (['archive','archiv','junk'].includes(rawStage)) continue;
    addCard(canonicalStage(rawStage), item);
  }

  board.querySelectorAll(".column").forEach(col => {
    const content = col.querySelector(".column-content");
    if (content && !content.querySelector(".card")) content.innerHTML = `<small>Keine Daten</small>`;
  });

  updateColumnCounts();
}

/* ============================================================================
   DnD DROP (with counters update)
   ============================================================================ */
function drop(e){
  e.preventDefault();
  const ids = JSON.parse(e.dataTransfer.getData("text") || "[]");
  if (!ids.length) return;
  const column = e.target.closest(".column");
  if (!column) return;
  const newStage = canonicalStage(column.id);

  const card = document.getElementById(ids[0]);
  if (!card) return;

  const customer_id    = card.dataset.customerId;
  const alternative_id = card.dataset.alternativeId;
  const product_id     = card.dataset.productId;
  const service        = card.dataset.service;
  const employee_id    = card.dataset.employeeId || 0;
  const service_id     = card.dataset.serviceId || 0;
  const department_id  = card.dataset.departmentId || 0;

  let title = "Phase verschieben?", text = `Möchten Sie in "${newStage}" verschieben?`, icon = "warning";
  if (newStage === 'ticket') { title = "⚠️ Achtung: Problemphase!"; text = "Dies kann ein Ticket erzeugen."; icon = "error"; }

  Swal.fire({ title, text, icon, showCancelButton: true, confirmButtonText: "Ja", cancelButtonText: "Abbrechen" })
    .then(result => {
      if (!result.isConfirmed) return Swal.close();
      Swal.fire({
        title: 'Notiz zur Phase',
        html: '<div id="quillEditorDrag" style="height: 200px;"></div>',
        showCancelButton: true,
        confirmButtonText: 'Speichern',
        didOpen: () => { window.quillDrag = new Quill('#quillEditorDrag', { theme: 'snow' }); },
        preConfirm: () => window.quillDrag.root.innerHTML
      }).then(({ isConfirmed, value: note }) => {
        if (!isConfirmed) return;
        fetch(`/lead/kanban/${seg(customer_id)}/${seg(alternative_id)}/${seg(product_id)}/${seg(employee_id, 0)}/${seg(service,'complete')}/${seg(newStage)}/${seg(service_id,0)}/${seg(department_id,0)}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: JSON.stringify({ description: note })
        })
        .then(r => r.json())
        .then(data => {
          if (!data.success) return Swal.fire("Fehler!", data.message || "Aktualisierung fehlgeschlagen.", "error");
          const dest = getColumnContentByStage(newStage);
          if (dest) dest.appendChild(card);
          card.classList.remove("selected");
          selectedCards.delete(card.id);
          Swal.fire("Erfolg!", "Kunde verschoben.", "success");
          updateColumnCounts();
        })
        .catch(() => Swal.fire("Fehler!", "Serverfehler beim Speichern.", "error"));
      });
    });
}

/* ============================================================================
   LIST VIEW (TABLE) + SORT + PAGINATION + STAGE CHANGE
   ============================================================================ */
function updateListView(leads, data){
  const tbody = document.getElementById("kanbanTableBody");
  if (!tbody) return;
  tbody.innerHTML = "";

  if (!leads.length) {
    tbody.innerHTML = `<tr><td colspan="8" class="text-center">Keine Ergebnisse gefunden</td></tr>`;
    syncSummary(data);
    return;
  }

  const services = { complete:'Komplett', montage:'Montage', product:'Produkt', plan:'Planung',
                     maintenance:'Wartung', repair:'Reparatur', emergency:'Notdienst', others:'Sonstiges' };

  const interestIcons = {
    interest: { icon: "kaufinteresse.svg", label: "Kaufinteresse" },
    intent:   { icon: "kaufabsicht.svg",   label: "Kaufabsicht" },
    option:   { icon: "kaufoption.svg",    label: "Kaufoption" },
  };

  leads.forEach(lead => {
    const s = canonicalStage(lead.stage);

    const employee = lead.employee?.name
      ? `<img src="/images/employee/${lead.employee.image}" width="30" class="rounded-circle me-1">
         <strong>${lead.employee.lastname}</strong> ${lead.employee.name}`
      : `<small>-</small>`;

    const translatedPhase = services[lead.phase_section_title] ?? null;
    const interest = interestIcons[lead.interest] ?? null;

    let statusText = "Absage", statusClass = "danger";
    if (["lead", "offer", "deal"].includes(s)) { statusText = "Offen";  statusClass = "warning text-dark"; }
    else if (["project", "completed"].includes(s)) { statusText = "Zusage"; statusClass = "success"; }

    const stageOptions = Object.entries(stageNames)
      .map(([key,label]) => `<option value="${key}" ${s===key?'selected':''}>${label}</option>`)
      .join('');

    const ref = parseDt(lead.request_date || lead.created_at);
    const isNew  = hoursSince(ref) <= 24;
    const isLate = hoursSince(ref) > 48;
    const pr = priorityMeta(lead.priority);

    const dateCellHtml = `
      ${lead.created_at ? new Date(lead.created_at).toLocaleDateString("de-DE") : "-"}
      <div class="d-flex align-items-center gap-2 mt-1">
        <div class="tooltip-trigger position-relative">
          <i class="feather icon-${pr.icon} prio-dot ${pr.cls}"></i>
          <div class="custom-tooltip">Priorität: ${pr.label}</div>
        </div>
        ${isNew ? `
          <div class="tooltip-trigger position-relative">
            <i class="feather icon-star new-dot"></i>
            <div class="custom-tooltip">Neu (${ref ? ref.toLocaleString('de-DE') : ''})</div>
          </div>` : ''}
        ${isLate ? `
          <div class="tooltip-trigger position-relative">
            <i class="feather icon-clock late-dot"></i>
            <div class="custom-tooltip">> 48 Stunden ohne Bearbeitung</div>
          </div>` : ''}
      </div>
    `;

    const iconRow = `
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center me-2">
          <img src="/images/icons/produkt.svg" style="width:26px" class="me-1">
          <span class="mr-2">${lead.initial ?? ''}</span>
        </div>
        ${lead.department_name ? `
          <div class="tooltip-trigger position-relative">
            <img src="/images/icons/abteilung.svg" style="width:30px">
            <div class="custom-tooltip">${lead.department_name}</div>
          </div>` : ''}
        ${translatedPhase ? `
          <div class="tooltip-trigger position-relative">
            <img src="/images/icons/dienstleistung.svg" style="width:33px">
            <div class="custom-tooltip">${translatedPhase}</div>
          </div>` : ''}
        ${interest ? `
          <div class="tooltip-trigger position-relative">
            <img src="/images/icons/${interest.icon}" style="width:20px">
            <div class="custom-tooltip">${interest.label}</div>
          </div>` : ''}
      </div>
    `;

    // Work-status badge clickable to view reasons
    const work = (lead.work_status && RUN_LABEL[lead.work_status])
      ? `
        <div class="mt-1">
          <span class="badge bg-${RUN_BADGE[lead.work_status]}" data-run-badge data-id="${lead.lead_product_id}" data-state="${lead.work_status}" style="cursor:pointer">
            <i class="feather ${RUN_ICON[lead.work_status]}"></i>
            ${RUN_LABEL[lead.work_status]}
          </span>
        </div>
      `
      : '';

    const statusCell = `
      <div><span class="badge bg-${statusClass}" id="status">${statusText}</span></div>
      ${work}
      ${
        lead.latest_phase || lead.latest_activity || lead.done_date
        ? `<div class="small mt-1">
            <i class='feather icon-box'></i> ${lead.latest_phase ?? '-'}<br>
            <i class='feather icon-check-circle'></i> ${lead.latest_activity ?? '-'}<br>
            <i class='feather icon-clock'></i> ${new Date(lead.done_date ?? lead.updated_at).toLocaleString("de-DE")}
          </div>`
        : ''
      }
    `;

    document.getElementById("kanbanTableBody").insertAdjacentHTML("beforeend", `
      <tr id="row-${lead.lead_product_id}"
          data-customer-id="${lead.customer_id}"
          data-alternative-id="${lead.alternative_id}"
          data-product-id="${lead.product_id}"
          data-employee-id="${lead.employee_id ?? lead.employee?.employee_id ?? 0}"
          data-service="${lead.service}"
          data-service-id="${lead.service_id ?? 0}"
          data-department-id="${lead.department_id ?? 0}">
        <td>${dateCellHtml}</td>
        <td><strong>${lead.customer_lastname ?? ''}</strong> ${lead.customer_name ?? ''}</td>
        <td><i class="feather icon-map-pin"></i> ${lead.city ?? ''}</td>
        <td>${iconRow}</td>
        <td>${employee}</td>
        <td>${statusCell}</td>
        <td>
          <select class="form-control stage-select" data-id="${lead.lead_product_id}">
            ${stageOptions}
          </select>
        </td>
        <td>
          <a href="/new_lead_profile/${lead.customer_id}" class="btn btn-outline-primary">
            <i class="feather icon-eye"></i>
          </a>
          <a href="/lead/process/history/${lead.customer_id}/${lead.alternative_id}/${lead.product_id}"
             class="btn btn-outline-primary" data-show-history>
            <i class="fa fa-tree"></i>
          </a>
        </td>
      </tr>
    `);
  });

  syncSummary(data);
}

function renderPagination(meta) {
  const wrap = document.getElementById('listPagination');
  if (!wrap) return;
  if (!meta || !meta.current_page || !meta.last_page) { wrap.innerHTML = ''; return; }

  const { current_page, last_page } = meta;
  let html = `<nav><ul class="pagination mb-0">`;

  const addBtn = (p, label, disabled=false, active=false) => {
    if (disabled) html += `<li class="page-item disabled"><span class="page-link">${label}</span></li>`;
    else if (active) html += `<li class="page-item active" aria-current="page"><span class="page-link">${label}</span></li>`;
    else html += `<li class="page-item"><a class="page-link" href="#" data-page="${p}">${label}</a></li>`;
  };

  addBtn(current_page - 1, '«', current_page === 1);
  const start = Math.max(1, current_page - 2);
  const end   = Math.min(last_page, current_page + 2);
  for (let p = start; p <= end; p++) addBtn(p, String(p), false, p === current_page);
  addBtn(current_page + 1, '»', current_page === last_page);

  html += `</ul></nav>`;
  wrap.innerHTML = html;
}

// Pagination click (delegate)
document.addEventListener('click', function(e){
  const a = e.target.closest('#listPagination a[data-page]');
  if (!a) return;
  e.preventDefault();
  fetchListView(buildQSWithPage(parseInt(a.getAttribute('data-page'), 10) || 1));
});

// Sorting
document.addEventListener('click', e => {
  const th = e.target.closest('th.sortable');
  if (!th) return;
  const key = th.dataset.sort;
  if (!key) return;

  if (currentSort.key === key) currentSort.dir = currentSort.dir === 'asc' ? 'desc' : 'asc';
  else currentSort = { key, dir: 'asc' };

  document.querySelectorAll('th.sortable').forEach(h => h.classList.remove('active', 'desc'));
  th.classList.add('active');
  if (currentSort.dir === 'desc') th.classList.add('desc');

  sortAndRenderTable();
});
function sortAndRenderTable(){
  const tbody = document.getElementById('kanbanTableBody');
  if (!tbody || !window.lastListData) return;

  const leads = [...window.lastListData];
  const { key, dir } = currentSort;

  const get = (obj, path) => path.split('.').reduce((o,p)=>o?.[p], obj);
  leads.sort((a,b)=>{
    const va = get(a,key), vb = get(b,key);
    if (key.includes('created_at') || key.includes('updated_at')) {
      const da = new Date(va), db = new Date(vb);
      return dir==='asc' ? da-db : db-da;
    }
    return dir==='asc'
      ? String(va ?? '').localeCompare(String(vb ?? ''), 'de', {numeric:true})
      : String(vb ?? '').localeCompare(String(va ?? ''), 'de', {numeric:true});
  });

  updateListView(leads, window.lastListMeta);
}

function seg(v, fallback='') {
  const s = (v == null || v === 'null' || v === '') ? fallback : String(v);
  return encodeURIComponent(s);
}


// Stage change from LIST rows
document.addEventListener('change', function (e) {
  const select = e.target.closest('.stage-select');
  if (!select) return;

  const stage = select.value;
  const row   = select.closest('tr');
  const customer_id    = row.dataset.customerId;
  const alternative_id = row.dataset.alternativeId;
  const product_id     = row.dataset.productId;
  const employee_id    = row.dataset.employeeId || 0;
  const service        = row.dataset.service;
  const service_id     = row.dataset.serviceId || 0;
  const department_id  = row.dataset.departmentId || 0;

  const prevIndex = [...select.options].findIndex(opt => opt.defaultSelected);

  let cfg = {
    title: 'Phase ändern?',
    text: `Möchten Sie wirklich zur Phase "${stage}" wechseln?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ja, ändern',
    cancelButtonText: 'Abbrechen'
  };
  if (stage === 'ticket') cfg = { ...cfg, title: '⚠️ Achtung: Problemphase!', text: 'Dies kann einen Problemprozess auslösen.', icon: 'error' };

  Swal.fire(cfg).then(result => {
    if (!result.isConfirmed) { select.selectedIndex = prevIndex; return; }

    Swal.fire({
      title: 'Kundennotiz hinzufügen',
      html: `<div id="quill-editor" style="height: 200px;"></div>`,
      showCancelButton: true,
      confirmButtonText: 'Speichern & Phase ändern',
      cancelButtonText: 'Abbrechen',
      didOpen: () => { const quill = new Quill('#quill-editor', { theme: 'snow' }); document.getElementById('quill-editor')._quill = quill; },
      preConfirm: () => document.querySelector('#quill-editor')._quill.root.innerHTML.trim()
    }).then(noteResult => {
      if (!noteResult.isConfirmed) { select.selectedIndex = prevIndex; return; }

      fetch(`/lead-product/change-stage/${seg(customer_id)}/${seg(alternative_id)}/${seg(product_id)}/${seg(employee_id,0)}/${seg(service,'complete')}/${seg(stage)}/${seg(service_id,0)}/${seg(department_id,0)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ description: noteResult.value })
      })
      .then(r => r.json())
      .then(stageData => {
        if (!stageData.success) {
          select.selectedIndex = prevIndex;
          return Swal.fire('Fehler!', stageData.message ?? 'Konnte Phase nicht ändern.', 'error');
        }
        Swal.fire('Erfolg!', stageData.message, 'success');
        const statusSpan = row.querySelector('#status');
        if (statusSpan) {
          if (['lead','offer','deal'].includes(stage))      { statusSpan.textContent = 'Offen';  statusSpan.className = 'badge bg-warning text-dark'; }
          else if (['project','completed'].includes(stage)) { statusSpan.textContent = 'Zusage'; statusSpan.className = 'badge bg-success'; }
          else                                              { statusSpan.textContent = 'Absage'; statusSpan.className = 'badge bg-danger'; }
        }
        select.querySelectorAll('option').forEach(opt => opt.defaultSelected = false);
        select.options[select.selectedIndex].defaultSelected = true;
      })
      .catch(() => {
        select.selectedIndex = prevIndex;
        Swal.fire('Fehler!', 'Serverfehler beim Speichern.', 'error');
      });
    });
  });
});

/* ============================================================================
   CARD ACTIONS + HISTORY MODAL
   ============================================================================ */
function visitProfile(customerId){
  if (!customerId) return;
  window.location.href = `/new_lead_profile/${customerId}`;
}
function editCard(cardId){
  const card = document.getElementById(cardId);
  if (!card) return;
  Swal.fire({
    title: "Bist du sicher?",
    text: "Möchten Sie diesen Lead bearbeiten?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ja, bearbeiten",
    cancelButtonText: "Abbrechen"
  }).then(res => {
    if (!res.isConfirmed) return;
    window.location.href = `/new_lead_edit/${card.dataset.customerId}/${card.dataset.alternativeId}`;
  });
}
function deleteCard(cardId){
  const card = document.getElementById(cardId);
  if (!card) return;
  Swal.fire({
    title: "Bist du sicher?",
    text: "Sie sind dabei, diesen Lead zu löschen.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ja, löschen",
    cancelButtonText: "Abbrechen"
  }).then(res => {
    if (!res.isConfirmed) return;
    window.location.href = `/delete_lead_product/${card.dataset.leadProductId}`;
  });
}
async function archiveCard(cardId){
  const card = document.getElementById(cardId);
  if (!card) return;
  const { customerId, alternativeId, productId, service, employeeId, serviceId, departmentId } = {
    customerId: card.dataset.customerId,
    alternativeId: card.dataset.alternativeId,
    productId: card.dataset.productId,
    service: card.dataset.service,
    employeeId: card.dataset.employeeId || 0,
    serviceId: card.dataset.serviceId || 0,
    departmentId: card.dataset.departmentId || 0,
  };

  const { value: confirmed } = await Swal.fire({
    title: 'In Archiv verschieben?',
    text: 'Dieser Lead wird in den Archiv-Tab verschoben.',
    icon: 'question', showCancelButton: true,
    confirmButtonText: 'Ja, archivieren', cancelButtonText: 'Abbrechen'
  });
  if (!confirmed) return;

  const noteModal = await Swal.fire({
    title: 'Notiz hinzufügen (optional)',
    html: '<div id="quillArchive" style="height: 150px;"></div>',
    showCancelButton: true,
    cancelButtonText: 'Ohne Notiz',
    confirmButtonText: 'Speichern & Archivieren',
    didOpen: () => { window.quillArchive = new Quill('#quillArchive', { theme: 'snow' }); },
    preConfirm: () => window.quillArchive.root.innerHTML
  });

  try {
    const res = await fetch(`/lead/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/archive/${serviceId}/${departmentId}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ description: noteModal.isConfirmed ? noteModal.value : '' })
    });
    const data = await res.json();
    if (!data.success) return Swal.fire('Fehler!', data.message || 'Archivieren fehlgeschlagen.', 'error');
    card.remove();
    updateColumnCounts();
    Swal.fire('Archiviert!', 'Lead wurde ins Archiv verschoben.', 'success');
  } catch {
    Swal.fire('Fehler!', 'Serverfehler beim Archivieren.', 'error');
  }
}

// History modal loader
document.addEventListener('click', function (e) {
  const btn = e.target.closest('[data-show-history]');
  if (!btn) return;
  e.preventDefault();
  fetch(btn.getAttribute('href'))
    .then(res => res.text())
    .then(html => {
      const wrap = document.createElement('div');
      wrap.innerHTML = html;
      document.body.appendChild(wrap);
      const modalEl = wrap.querySelector('.modal');
      if (!modalEl) return;
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
      modalEl.addEventListener('hidden.bs.modal', () => wrap.remove());
    })
    .catch(() => Swal.fire('Fehler', 'Konnte Verlauf nicht laden.', 'error'));
});

/* ============================================================================
   RUN HISTORY MODAL (click on badges)
   ============================================================================ */
document.addEventListener('click', async (e) => {
  const badge = e.target.closest('[data-run-badge]');
  if (!badge) return;
  const id    = badge.dataset.id;
  const state = badge.dataset.state;

  try {
    const res = await fetch(`/lead-product/progress-history/${id}?state=${encodeURIComponent(state)}`);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const { success, entries } = await res.json();

    if (!success) throw new Error('Fehlerhafte Antwort');

    const rows = (entries || []).map((h, i) => `
      <tr>
        <td>${i+1}</td>
        <td>${h.work_status}</td>
        <td>${h.reason ? h.reason : '-'}</td>
        <td>${h.changed_at}</td>
        <td>${h.changed_by_name || h.changed_by || '-'}</td>
      </tr>
    `).join('');

    Swal.fire({
      title: `Gründe – ${state === 'paused' ? 'Pause' : state === 'stopped' ? 'Stopp' : 'Start'}`,
      html: `
        <div class="table-responsive" style="text-align:left">
          <table class="table table-sm table-bordered">
            <thead>
              <tr><th>#</th><th>Status</th><th>Grund</th><th>Zeit</th><th>von</th></tr>
            </thead>
            <tbody>${rows || '<tr><td colspan="5" class="text-center">Keine Einträge</td></tr>'}</tbody>
          </table>
        </div>
      `,
      confirmButtonText: 'Schließen'
    });
  } catch (err){
    Swal.fire('Fehler', err.message || 'Konnte Verlauf nicht laden.', 'error');
  }
});

/* ============================================================================
   SUMMARY CARD FILTERS
   ============================================================================ */
document.addEventListener('DOMContentLoaded', () => {
  const cards = {
    cardAbsage : { field: 'stageFilter', value: 'reject' },
    cardZusage : { field: 'stageFilter', value: 'project' },
    cardOffen  : { field: 'stageFilter', value: 'lead' },
  };

  Object.entries(cards).forEach(([id, cfg]) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('click', () => {
      document.querySelectorAll('.summary-card.active').forEach(c => c.classList.remove('active'));
      el.classList.add('active');
      const sel = document.getElementById(cfg.field);
      if (sel) { $(sel).val(cfg.value).trigger('change'); }
      lastFilterQS = buildFilterQS();
      fetchKanbanView(lastFilterQS);
      fetchListView(buildQSWithPage(1));
    });
  });

  ['cardEmployees','cardProducts','cardCustomers','cardAnfragen'].forEach(id=>{
    const el=document.getElementById(id);
    if(!el)return;
    el.addEventListener('click',()=>{
      document.querySelectorAll('.summary-card.active').forEach(c => c.classList.remove('active'));
      $('#stageFilter').val('').trigger('change');
      lastFilterQS = buildFilterQS();
      fetchKanbanView(lastFilterQS);
      fetchListView(buildQSWithPage(1));
    });
  });
});

/* ============================================================================
   FILTER FORM + TABS + INITIAL LOAD
   ============================================================================ */
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("kanbanFilterForm");
  if (form) {
    form.addEventListener("submit", e => {
      e.preventDefault();
      lastFilterQS = buildFilterQS();
      fetchKanbanView(lastFilterQS);
      fetchListView(buildQSWithPage(1));
    });
  }
});

$('a[data-toggle="tab"][href="#home"]').on("shown.bs.tab", function () {
  ensureColumnsExist();
  renderKanban(lastKanbanLeads);
});

document.addEventListener("DOMContentLoaded", function () {
  lastFilterQS = buildFilterQS();
  fetchKanbanView(lastFilterQS);     // unpaginated
  fetchListView(buildQSWithPage(1)); // paginated
});
</script>


@endsection
