@extends('admin.layouts.app')
@section('title') PROZESS @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<style>
    /* Appointment Tabs */
   

    /* --- New Appointment Drawer Styles --- */
    .ap-tabs {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 0 1rem;
        flex-shrink: 0; /* Don't shrink */
    }
    .ap-tab-link {
        padding: 1rem 1.2rem;
        cursor: pointer;
        font-weight: 600;
        color: #6c757d;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .ap-tab-link:hover { color: #333; background: #f1f1f1; }
    .ap-tab-link.active {
        color: #93c21c;
        border-bottom-color: #93c21c;
        background: #fff;
    }
    
    /* Content Area */
    .ap-tab-content {
        display: none;
        flex-direction: column;
        height: calc(100vh - 120px); /* Fill remaining height */
        overflow-y: auto;
        position: relative;
    }
    .ap-tab-content.active {
        display: flex; /* Flex to allow calendar to grow */
    }

    /* Calendar Wrapper */
    #ap-calendar-wrap {
        flex: 1; /* Grow to fill space */
        padding: 15px;
        min-height: 500px;
    }
    
    /* FullCalendar Overrides */
    .fc-event { cursor: pointer; font-size: 0.85em; }
    .fc-header-toolbar { margin-bottom: 10px !important; }
    .fc-button { padding: 0.25rem 0.5rem !important; font-size: 0.85rem !important; }

    /* Card List (Grid) */
    #ap-card-view {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 15px;
    }
    @media (max-width: 900px) { #ap-card-view { grid-template-columns: 1fr; } }
</style> 
<style>
/* ======= Layout: main + right action rail ======= */
.pro-layout{ display:grid; grid-template-columns:minmax(0,1fr) 56px; gap:.75rem; align-items:start; }
@media (max-width: 992px){ .pro-layout{ grid-template-columns:1fr 48px; } }
.pro-rail{ 
  position: relative;
    width: 70px;
    height: 44px;
    border: none;
    border-radius: 12px;
    background: #8fc73e;
    color: #ffffff;
    display: grid;
    place-items: center;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .08);
    cursor: pointer;
    transition: transform .12s, background .12s, box-shadow .12s;
    right: 48px;
}
.rail-btn{ position:relative; width:44px; height:44px; border:none; border-radius:12px; background:#8fc73e; color:#333; display:grid; place-items:center; box-shadow:0 1px 2px rgba(0,0,0,.08); cursor:pointer; transition:transform .12s, background .12s, box-shadow .12s; }
.rail-btn:hover{ transform:translateY(-1px); background:#eef1f6; }
.rail-btn .feather{ width:20px; height:20px; }
.rail-btn--active{ background:#e7f3d2; color:#2f5c00; box-shadow:0 0 0 2px rgba(147,194,28,.25) inset; }
.rail-badge{ position:absolute; top:-6px; right:-6px; min-width:18px; height:18px; line-height:18px; padding:0 4px; border-radius:10px; background:#93c21c; color:#fff; font-size:11px; font-weight:700; text-align:center; }
.d-none{ display:none !important; }
a { border-radius:0px !important;}
/* ======= Drawer (single, no internal tabs) ======= */
.drawer{ position:fixed; inset:0 0 0 auto; width:480px; max-width:92vw; transform:translateX(100%); transition:transform .22s ease; background:#fff; box-shadow:-12px 0 30px rgba(0,0,0,.12); z-index:1080; display:flex; flex-direction:column; }
.drawer.open{ transform:translateX(0); }
.drawer-header{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid #e5e7eb; }
.drawer-body{ padding:14px; overflow:auto; }
.drawer-backdrop{ position:fixed; inset:0; background:rgba(0,0,0,.25); opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:1075; }
.drawer-backdrop.show{ opacity:1; pointer-events:auto; }

/* chips */
.chips{ display:flex; gap:.4rem; flex-wrap:wrap; align-items:center; margin:6px 2px 0 2px; }
.chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.15rem .5rem; background:#eef2f7; border-radius:999px; font-size:.85rem; }
.chip .x{ cursor:pointer; opacity:.7; } .chip .x:hover{ opacity:1; }

/* small badge inside title */
.tab-badge-inline{ margin-left:.4rem; padding:.05rem .35rem; border-radius:10px; font-size:.75rem; background:#93c21c; color:#fff; font-weight:700; }

/* ======= Kanban ======= */
.card{ background:#fff; padding:15px; margin:10px 0; border-left:5px solid #74b2d4; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,.2); cursor:grab; user-select:none; display:flex; flex-direction:column; gap:4px; position:relative; }
.card .card-header{ display:flex; align-items:center; flex-wrap:wrap; justify-content:space-between; border-bottom:none; padding:1px; background:transparent; font-size:13px; text-transform:uppercase; }
.card .circle{ width:30px; height:30px; border-radius:50%; background:#b0d5f2; display:flex; justify-content:center; align-items:center; font-weight:700; font-size:11px; position:absolute; top:2px; right:3px; }
.card.selected{ background:#d1ecf1; border-left:5px solid #17a2b8; }
.card-actions{ display:flex; gap:.25rem; justify-content:space-between; align-items:center; padding-top:6px; z-index:5; }
.btn-icon{ border:none; background:none; cursor:pointer; font-size:18px; line-height:1; padding:.35rem .45rem; border-radius:10px; color:#7b93a7; transition:transform .12s, background .15s, color .15s; }
.btn-icon:hover{ transform:translateY(-1px); background:rgba(0,0,0,.04); }
.btn-icon.is-active{ box-shadow:inset 0 0 0 2px rgba(0,0,0,.06); background:rgba(0,0,0,.03); }
.btn-play{ color:#95c11f!important; }
.card.status-playing{ border-left-color:#95c11f!important; }
.card.status-paused{ border-left-color:#f3c12f!important; }
.card.status-stopped{ border-left-color:#c93a3a!important; }
.card .card-status-overlay{ position:absolute; inset:0; backdrop-filter: blur(1.5px); background:rgba(255,255,255,.35); border-radius:8px; display:none; align-items:center; justify-content:center; text-align:center; padding:10px; z-index:3; pointer-events:none; }
.card.card-has-overlay .card-status-overlay{ display:flex; }
.card .card-status-badge{ display:inline-flex; gap:.4rem; align-items:center; font-weight:700; text-transform:uppercase; letter-spacing:.5px; font-size:.85rem; padding:.35rem .6rem; border-radius:14px; background:#eee; color:#555; box-shadow:0 1px 2px rgba(0,0,0,.08); }
.card.status-paused .card-status-badge{ background:#fff4d6; color:#8a6d00; }
.card.status-stopped .card-status-badge{ background:#ffe2e2; color:#8a1f1f; }

/* Kanban columns */
.kanban-container{ display:flex; gap:0; overflow-x:auto; padding-bottom:10px; }
.column{ background:#f1f1f1; width:300px; height:1000px; display:flex; flex-direction:column; border-right:2px dashed #c0baba; position:relative; }
.column h3{ position:sticky; top:0; z-index:1; background:#95c11f; color:#fff; padding:6px; font-size:20px; text-align:center; text-transform:uppercase; font-weight:bold; margin:0; display:flex; align-items:center; justify-content:space-between; }
.column-content{ overflow-y:auto; flex-grow:1; padding:10px; }
.count-badge{ background:#93c21c; color:#fff; font-size:.8rem; padding:2px 8px; border-radius:12px; margin-left:.5rem; font-weight:600; }

/* List: table + sort */
th.sortable{ cursor:pointer; user-select:none; white-space:nowrap; }
th.sortable .sort-icon{ font-size:.8rem; opacity:.4; transition:transform .2s, opacity .2s; }
th.sortable.active .sort-icon{ opacity:1; }
th.sortable.desc .sort-icon{ transform:rotate(180deg); }

/* Tooltips (custom) */
.tooltip-trigger{ cursor:pointer; display:inline-block; position:relative; }
.tooltip-trigger .custom-tooltip{ position:absolute; bottom:130%; left:50%; transform:translateX(-50%); background:#93c21c; color:#fff; padding:4px 8px; border-radius:6px; font-size:12px; white-space:nowrap; opacity:0; pointer-events:none; transition:opacity .15s, transform .15s; z-index:50; }
.tooltip-trigger:hover .custom-tooltip{ opacity:1; transform:translateX(-50%) translateY(-2px); }
.tooltip-trigger .custom-tooltip::after{ content:''; position:absolute; top:100%; left:50%; margin-left:-4px; border-width:4px; border-style:solid; border-color:#93c21c transparent transparent transparent; }

/* Priority dots */
.prio-dot, .new-dot, .late-dot{ font-size:16px; vertical-align:middle; }
.prio-high{ color:#dc3545; } .prio-normal{ color:#93c21c; } .prio-low{ color:#6c757d; }
.new-dot{ color:#ffc107; } .late-dot{ color:#f45b69; }

/* Summary cards */
.summary-card{ cursor:pointer; transition:transform .15s ease; position:relative; }
.summary-card:hover{ transform:translateY(-2px); }
.summary-card.active > div{ border:2px solid #93c21c!important; box-shadow:0 0 6px rgba(147,194,28,.6); }
.summary-card.active::after{ content:"ausgewählt"; position:absolute; bottom:-18px; left:50%; transform:translateX(-50%); font-size:12px; color:#93c21c; }

/* Misc */
.tab-icon{ width:16px; height:16px; margin-right:6px; vertical-align:-2px; }
.table thead th{ vertical-align:bottom; }
.timeline-item {
  list-style:none;
}
/* --- Kanban status block --- */
.kb-status {
  margin-top: .35rem;
  border: 1px dashed rgba(0,0,0,.08);
  background: #fafbfc;
  border-radius: 10px;
  padding: .45rem .55rem;
}
.kb-status .badge { font-weight: 700; letter-spacing:.2px; }
.kb-status .meta {
  display:grid; gap:.25rem; margin-top:.35rem;
  grid-template-columns: 1.1rem 1fr; align-items:start; font-size:.82rem; color:#5b6470;
}
.kb-status .meta i.feather { width:16px; height:16px; opacity:.75; }
.kb-status .rowline { display:contents; } /* keep grid semantics */
.kb-status .value { line-height:1.2; word-break:break-word; }
.kb-status .muted { opacity:.7; }
.kb-status .time { font-variant-numeric: tabular-nums; }
.kb-status{ outline:1px dashed rgba(147,194,28,.35); }
/* put this near your existing .kb-status / overlay styles */
.card .card-status-overlay{ z-index:1; }   /* keep overlay below content */
.kb-status{ position:relative; z-index:2; }/* ensure the block sits on top */

/* ===== Notes Drawer ===== */
.notes-backdrop{ position:fixed; inset:0; background:rgba(0,0,0,.25); opacity:0; pointer-events:none; transition:opacity .2s; z-index:1075; }
.notes-backdrop.show{ opacity:1; pointer-events:auto; }
.notes-drawer{ position:fixed; top:0; right:0; bottom:0; width:1112px; max-width:95vw; background:#fff; transform:translateX(100%); transition:transform .24s ease; z-index:1080; display:flex; flex-direction:column; box-shadow:-12px 0 30px rgba(0,0,0,.12); }
.notes-drawer.open{ transform:translateX(0); }

/* ===== Notes Tabs (inside drawer) ===== */
.notes-tabs{
  display:flex;
  align-items:flex-end;
  gap:.25rem;
  padding:0 .75rem;
  border-bottom:1px solid #e5e7eb;
  background:#f9fafb;
}

.notes-tab{
  border:none;
  background:transparent;
  padding:.45rem .8rem;
  font-size:.9rem;
  cursor:pointer;
  border-bottom:2px solid transparent;
  border-top-left-radius:10px;
  border-top-right-radius:10px;
  color:#6b7280;
  outline:0;
}

.notes-tab--active{
  background:#ffffff;
  border-color:#93c21c;
  color:#111827;
  font-weight:600;
}

/* Customer Report styles inside notes drawer */
#customerReportList {
    padding: 6px 8px 10px;
    max-height: 100%;
    overflow-y: auto;
}

.cr-shell {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.cr-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 4px 4px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.25);
}

.cr-title-row {
    display: flex;
    align-items: center;
    gap: 6px;
}

.cr-new-wrapper {
    border-radius: 10px;
    border: 1px dashed rgba(148, 163, 184, 0.6);
    padding: 8px;
    background: rgba(15, 23, 42, 0.02);
}

/* Cards */
.cr-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.cr-card {
    border-radius: 12px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: #ffffff;
    padding: 8px 9px;
    box-shadow: 0 5px 18px rgba(15, 23, 42, 0.06);
    transition: box-shadow .15s ease, transform .15s ease;
}

.cr-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.10);
}

/* Card header */
.cr-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.cr-author {
    display: flex;
    align-items: center;
    gap: 6px;
}

.cr-avatar {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    object-fit: cover;
    box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.06);
}

.cr-author-name {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
}

.cr-author-meta {
    font-size: 11px;
    color: #6b7280;
}

/* Body */
.cr-card-body {
    font-size: 12px;
    line-height: 1.4;
    color: #111827;
    padding: 4px 0 2px;
    border-top: 1px dashed rgba(148, 163, 184, 0.4);
    border-bottom: 1px dashed rgba(148, 163, 184, 0.3);
    margin-top: 4px;
    margin-bottom: 4px;
    max-height: 140px;
    overflow-y: auto;
}

/* Footer */
.cr-card-foot {
    display: flex;
    justify-content: flex-end;
}

/* Comments */
.cr-comments {
    margin-top: 6px;
    border-top: 1px solid rgba(148, 163, 184, 0.30);
    padding-top: 6px;
}

.cr-comments-list {
    max-height: 120px;
    overflow-y: auto;
    margin-bottom: 4px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

/* Single comment */
.cr-comment-row {
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.cr-comment-row--reply {
    margin-top: 4px;
    margin-left: 22px;
}

.cr-comment-avatar {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    object-fit: cover;
}

.cr-comment-bubble {
    background: #f9fafb;
    border-radius: 10px;
    padding: 4px 6px;
    border: 1px solid rgba(209, 213, 219, 0.8);
    width: 100%;
}

.cr-comment-meta {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #6b7280;
    margin-bottom: 2px;
}

.cr-comment-author {
    font-weight: 600;
}

.cr-comment-text {
    font-size: 11px;
    color: #111827;
}

/* Comment form */
.cr-comment-form textarea {
    font-size: 12px;
}

.cr-comment-form .btn {
    padding: 2px 8px;
}


.notes-head{ display:flex; align-items:center; justify-content:space-between; padding:.75rem .9rem; border-bottom:1px solid #e5e7eb; }
.notes-title{ display:flex; align-items:center; gap:.5rem; font-weight:700; font-size:1rem; }
.notes-body{ flex:1; overflow:auto; padding:12px; background:#f8fafc; }
.notes-foot{ border-top:1px solid #e5e7eb; padding:.6rem .75rem; background:#fff; }

.note-row{ display:flex; align-items:flex-end; gap:.5rem; margin:8px 0; }
.note-row.me{ justify-content:flex-end; }
.note-avatar{ width:34px; height:34px; border-radius:50%; object-fit:cover; background:#eee; flex:0 0 34px; }
.note-bubble{ max-width:500px;width:500px; padding:.5rem .6rem; border-radius:14px; position:relative; word-break:break-word; }
.note-meta{ font-size:.75rem; opacity:.8; margin-top:4px; }

.note-bubble.other{ background:#cfe09b; color:#000; }
.note-bubble.me{ background:#cfe09b6e; color:#10212b; font-weight:600; }
.note-actions {     border: 0px;  background: #e6efd3;} 
/* little tail */
.note-bubble.other::after{ content:''; position:absolute; left:-6px; bottom:6px; border:7px solid transparent; border-right-color:#cfe09b; }
.note-bubble.me::after{ content:''; position:absolute; right:-6px; bottom:6px; border:7px solid transparent; border-left-color:#74b2d4; }

/* composer */
.notes-composer{ display:grid; grid-template-columns:1fr auto; gap:.5rem; }
.notes-composer textarea{ resize:vertical; min-height:42px; max-height:140px; }

/* Notes icon on card */
/* Generic Icon Badges on Kanban Cards */
.btn-icon { 
    position: relative; 
}
.btn-icon .badge-notes { 
    position: absolute; 
    top: -6px; 
    right: -6px; 
    min-width: 18px; 
    height: 18px; 
    line-height: 18px; 
    padding: 0 4px; 
    border-radius: 10px; 
    background: #93c21c; 
    color: #fff; 
    font-size: 11px; 
    font-weight: 700; 
    text-align: center; 
    pointer-events: none; /* Ensures clicking the badge triggers the button */
    z-index: 10;
}
.kb-menu-dropdown{
  position:absolute; top:36px; left:8px; background:#fff; border:1px solid rgba(0,0,0,.08);
  border-radius:10px; box-shadow:0 6px 24px rgba(0,0,0,.12); padding:6px; z-index:4;
  display:flex; flex-direction:column; min-width:140px;
}
.kb-menu-item{
  display:block; text-align:left; width:100%;
  background:transparent; border:0; padding:8px 10px; border-radius:8px; font-size:.95rem;
}
.kb-menu-item:hover{ background:#f3f4f6; }


/* ===== Appointment Reports (inside Notes -> Report tab) ================== */

.ap-report-wrapper {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* Header (optional global overview above list) */
.ap-report-header {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  padding: 8px 10px;
  margin-bottom: 6px;
  border-radius: 12px;
  background: linear-gradient(90deg, #cfe09b, #74b2d4);
  color: #10212b;
}
.ap-report-header-title {
  font-weight: 700;
  font-size: 14px;
}
.ap-report-header-meta {
  font-size: 12px;
  opacity: 0.9;
}

/* Single report card */
.ap-report-card {
  position: relative;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
  background: #ffffff;
  box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06);
  padding: 10px 12px;
  display: grid;
  grid-template-columns: minmax(0, 1fr);
  grid-row-gap: 6px;
}

/* Top row: title, date, stage */
.ap-report-top {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: baseline;
  gap: 4px;
}
.ap-report-title {
  font-weight: 600;
  font-size: 13px;
  color: #111827;
}
.ap-report-sub {
  font-size: 11px;
  color: #6b7280;
}

/* Stage badge */
.ap-report-stage {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 7px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
  background: #eef2ff;
  color: #3730a3;
}

/* Body text (the report content) */
.ap-report-body {
  font-size: 13px;
  color: #111827;
  line-height: 1.4;
  padding: 6px 0 2px 0;
  border-top: 1px dashed #e5e7eb;
}
.ap-report-body p {
  margin-bottom: 4px;
}

/* Footer: employee & reactions */
.ap-report-footer {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
  padding-top: 4px;
  border-top: 1px dashed #e5e7eb;
}

/* Author / employees */
.ap-report-author {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: #4b5563;
}
.ap-report-avatar {
  width: 24px;
  height: 24px;
  border-radius: 999px;
  object-fit: cover;
  background: #e5e7eb;
}

/* Like / dislike buttons */
.ap-report-actions {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
}
.ap-report-like,
.ap-report-dislike {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  padding: 2px 7px;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  background: #f9fafb;
  font-size: 11px;
  cursor: pointer;
}
.ap-report-like .feather,
.ap-report-dislike .feather {
  width: 13px;
  height: 13px;
}
.ap-report-like.is-active {
  border-color: #93c21c;
  background: #e7f5d0;
  color: #2f5c00;
}
.ap-report-dislike.is-active {
  border-color: #f97373;
  background: #ffe2e2;
  color: #991b1b;
}

/* Comments area */
.ap-report-comments-toggle {
  font-size: 11px;
  color: #4b5563;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 3px;
}
.ap-report-comments {
  margin-top: 6px;
  padding-top: 6px;
  border-top: 1px dashed #e5e7eb;
  font-size: 12px;
}
.ap-report-comment-row {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  margin-bottom: 6px;
}
.ap-report-comment-avatar {
  width: 24px;
  height: 24px;
  border-radius: 999px;
  object-fit: cover;
  background: #e5e7eb;
}
.ap-report-comment-bubble {
  padding: 5px 8px;
  border-radius: 10px;
  background: #f3f4f6;
}
.ap-report-comment-meta {
  font-size: 11px;
  color: #6b7280;
  margin-bottom: 2px;
}

/* Comment composer (per report) */
.ap-report-comment-composer {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 4px;
  margin-top: 4px;
}
.ap-report-comment-text {
  resize: vertical;
  min-height: 40px;
  max-height: 90px;
}
.ap-report-comment-submit {
  white-space: nowrap;
}

/* Readonly state: not in employee list */
.ap-report-card.ap-report--readonly {
  background: #f9fafb;
}
.ap-report-card.ap-report--readonly .ap-report-actions,
.ap-report-card.ap-report--readonly .ap-report-comment-composer {
  opacity: 0.6;
  pointer-events: none;
}
.ap-report-lock {
  position: absolute;
  top: 6px;
  right: 8px;
  padding: 2px 7px;
  border-radius: 999px;
  background: #fef9c3;
  color: #92400e;
  font-size: 10px;
  font-weight: 600;
}

</style>


<style>
/* ===== Lead History (lh-) — no conflicts ===== */
:root{
  --lh-bg:#fff; --lh-backdrop:rgba(15,23,42,.45); --lh-border:#e5e7eb; --lh-muted:#6b7280;
  --lh-shadow:0 10px 30px rgba(0,0,0,.18), 0 2px 8px rgba(0,0,0,.08);
}
.lh-root{
  position:fixed; inset:0; z-index:1060;
  pointer-events:none; opacity:0; transition:opacity .18s ease;
}
.lh-root[aria-hidden="false"]{ pointer-events:auto; opacity:1; }

.lh-backdrop{ position:absolute; inset:0; background:var(--lh-backdrop); }

/* Panel is FIXED to the right; slides from right -> to position 0 */
.lh-panel{
  position:fixed; top:0; right:0; height:100%;
  width:min(980px,92vw);
  background:var(--lh-bg); color:#111827;
  border-left:1px solid var(--lh-border);
  border-top-left-radius:16px; border-bottom-left-radius:16px;
  box-shadow:var(--lh-shadow);
  transform:translateX(100%); /* start off-screen to the RIGHT */
  transition:transform .26s cubic-bezier(.22,.9,.22,1);
  display:flex; flex-direction:column; outline:0;
}
.lh-root[aria-hidden="false"] .lh-panel{ transform:translateX(0); }

@media (prefers-reduced-motion:reduce){
  .lh-root,.lh-panel{ transition:none !important; }
}

/* Header */
.lh-header{
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 16px; border-bottom:1px solid var(--lh-border);
  background:#fff; border-top-left-radius:16px;
}
#lh-title{ font-weight:700; letter-spacing:.2px; }
#lh-title-text{ font-weight:600; }

/* Body */
.lh-body{ overflow:auto; height:100%; background:#fff; border-bottom-left-radius:16px; }
.lh-muted{ color:var(--lh-muted); }

/* Timeline spine */
.lh-timeline{ position:relative; padding-left:0; list-style:none; margin:0; }
.lh-timeline::before{
  content:""; position:absolute; left:28px; top:0; bottom:0; width:2px; background:#eef2f7;
}
.lh-item{ display:flex; }
.lh-icowrap{ width:56px; display:flex; align-items:flex-start; justify-content:center; padding-top:6px; }
.lh-ico{
  width:26px; height:26px; border-radius:8px; display:flex; align-items:center; justify-content:center;
  background:#f3f6ff; border:1px solid #e6ecff; box-shadow:inset 0 1px 0 rgba(255,255,255,.8);
}
.lh-content{ flex:1; padding:8px 0 14px; }

/* Badges (local variants; won’t clash with Bootstrap) */
.lh-badge{ display:inline-block; padding:.25rem .55rem; border-radius:10px; border:1px solid var(--lh-border); font-weight:600; background:#f9fafb; }
.lh-badge--success{ background:#e8f7ed; border-color:#d0f0db; }
.lh-badge--danger{ background:#fde8e8; border-color:#f9cfcf; }
.lh-badge--warning{ background:#fff6e5; border-color:#ffe6b0; }
.lh-badge--info{ background:#e7f5ff; border-color:#cfe8ff; }
.lh-badge--secondary{ background:#f1f5f9; border-color:#e2e8f0; }
.lh-badge--primary{ background:#eef2ff; border-color:#dbe3ff; }

/* Cards (right column) */
.lh-list > .lh-card,
.lh-list .list-group-item{
  border:1px solid #eef2f7; background:#fff; border-radius:12px; padding:10px 12px; margin-bottom:10px;
  box-shadow:0 1px 2px rgba(0,0,0,.04);
}

/* Skeletons */
.lh-skel{ background:linear-gradient(90deg,#f1f5f9 25%,#eef2f7 37%,#f1f5f9 63%); background-size:400% 100%;
  animation:lh-sk 1.1s ease-in-out infinite; border-radius:10px; height:12px; margin:8px 0; }
@keyframes lh-sk{ 0%{background-position:100% 0} 100%{background-position:0 0} }
.kb-menu-dropdown { z-index: 2000; }


/* ===== Task Drawer: wider + pro layout (ONLY affects #pt-drawer) ===== */

/* Make the task drawer much wider on desktop, responsive on smaller screens */
  /* ===== Personal Task Kanban (unique classes: ptk-*) ===== */

/* Board */
#pt-drawer .ptk-board { padding: 8px; }
#pt-drawer .ptk-board-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 10px;
}
@media (min-width: 720px){
  #pt-drawer .ptk-board-row { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
@media (min-width: 1200px){
  #pt-drawer .ptk-board-row { grid-template-columns: repeat(5, minmax(0,1fr)); }
}

/* Columns */
#pt-drawer .ptk-col {
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
}
#pt-drawer .ptk-col-head {
  display:flex; align-items:center; justify-content:space-between;
  padding: 8px 10px;
  background:#eef2f7;
  border-bottom:1px solid #e5e7eb;
}
#pt-drawer .ptk-col-title { font-weight:700; font-size:13px; color:#0f172a; }
#pt-drawer .ptk-col-count {
  font-size:12px; font-weight:700; background:#fff; border:1px solid #e5e7eb;
  padding: 2px 8px; border-radius: 999px; color:#374151;
}
#pt-drawer .ptk-col-body { padding: 8px; min-height: 120px; }
#pt-drawer .ptk-col-body.ptk-over { outline: 2px dashed #9ca3af; outline-offset: -4px; }

/* Cards */
#pt-drawer .ptk-card {
  position: relative;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:12px;
  padding:10px 12px 10px 14px;
  box-shadow:0 1px 2px rgba(0,0,0,.04);
  cursor:grab;
  margin-bottom: 8px;
}
#pt-drawer .ptk-card:active { cursor:grabbing; }
#pt-drawer .ptk-card .ptk-card-color {
  position:absolute; left:0; top:0; bottom:0; width:4px; border-top-left-radius:12px; border-bottom-left-radius:12px;
}
#pt-drawer .ptk-card-title { font-weight:700; color:#0f172a; font-size:14px; line-height:1.25; }
#pt-drawer .ptk-card-desc  { color:#374151; font-size:13px; margin-top:4px; }
#pt-drawer .ptk-card-emps  { display:flex; flex-wrap:wrap; gap:6px; margin-top:6px; }
#pt-drawer .ptk-emp {
  display:inline-flex; align-items:center; gap:6px;
  background:#fff; border:1px solid #e5e7eb; border-radius:999px; padding:2px 8px; font-size:12px; color:#374151;
}
#pt-drawer .ptk-emp--xs { padding:1px 6px; font-size:11px; }
#pt-drawer .ptk-ava { border-radius:999px; display:block; }

#pt-drawer .ptk-steps { margin-top:8px; border-top:1px dashed #e5e7eb; padding-top:6px; }
#pt-drawer .ptk-step { display:flex; align-items:center; justify-content:space-between; margin-top:4px; }
#pt-drawer .ptk-step-title { font-size:12px; font-weight:600; color:#111827; }
#pt-drawer .ptk-step-emps { display:flex; gap:4px; flex-wrap:wrap; }

#pt-drawer .ptk-empty {
  font-size:12px; color:#6b7280; border:1px dashed #e5e7eb;
  background:#fff; border-radius:10px; padding:8px; text-align:center;
}

#pt-title.ptk-loading::after {
  content: '…';
  animation: ptk-ell 1s infinite steps(3);
  margin-left: 4px;
}
@keyframes ptk-ell { 0%{content:'·';} 33%{content:'··';} 66%{content:'···';} }

#pt-drawer .ptk-card .btn-icon{
  padding:.2rem .35rem; border-radius:8px; font-size:16px;
}

.swal2-container { z-index: 200000 !important; }
.ptk-col-body { height:100vh;}
/* Task Drawer search + highlight */
#ptk-search-wrap { flex: 1 1 auto; }
#ptk-search::placeholder { color: #9aa3af; }
.ptk-hl { background: #ffec99; padding: 0 .1em; border-radius: 3px; }
/* ===== FOOTER COMPOSER: scrollable, with sticky Save button ===== */

/* Footer shell stays pinned; no scrolling on the shell itself */
#pt-drawer .notes-foot{
  background:#fff;
  border-top:1px solid #e5e7eb;
  z-index:2;

  /* two columns: form (scrolls) + save button */
  display:grid;
  grid-template-columns: 1fr auto;
  gap:.75rem;
  align-items:start;
  padding:.75rem;
}

/* The form column: it gets its own scrollbar when tall */
#pt-drawer .notes-foot .notes-composer{
  min-height:0;               /* allow shrinking in grid */
  max-height:44vh;            /* cap height of the form column */
  overflow:auto;              /* scroll inside the form */
  padding-right:.25rem;       /* keep content clear of scrollbar */
}

/* Ensure the inner wrapper can shrink and not force overflow */
#pt-drawer .notes-foot .notes-composer > .w-100{
  min-width:0;
}

/* Keep the Save button always reachable */
#pt-drawer .notes-foot .btn.btn-primary{
  position: sticky;
  top:.25rem;                 /* sticks within the footer area */
  align-self:start;
  white-space:nowrap;
}

/* Inputs row: tidy gaps on wrap */
#pt-drawer .notes-foot .d-flex.flex-wrap.gap-2{
  gap:.5rem !important;
}

/* Match your inline widths via CSS (you can remove the inline styles if you like) */
#pt-drawer #pt-start_date,
#pt-drawer #pt-due_date{ max-width:180px; }
#pt-drawer #pt-due_time{ max-width:140px; }
#pt-drawer #pt-priority{ max-width:150px; }
#pt-drawer #pt-color{ max-width:70px; padding:0 2px; }

/* Steps block inside the composer: cap height so Save stays visible */
#pt-drawer #pt-steps{
  max-height:24vh;            /* slightly smaller than the form cap */
  overflow:auto;
  padding-right:2px;
  scroll-behavior:smooth;
}

/* Select2 & Swal should float above the drawer */
.select2-container--open{ z-index:2000 !important; }
.swal2-container{ z-index:200000 !important; }
/* ===== Appointment Drawer ===== */
#ap-drawer {
  width: 520px;
  max-width: 95vw;
}
#ap-drawer .notes-body {
  background:#f8fafc;
}

#ap-drawer .ap-card {
  position:relative;
  display:flex;
  padding:10px 10px 10px 12px;
  border-radius:12px;
  background:#fff;
  box-shadow:0 1px 3px rgba(15,23,42,.08);
  margin-bottom:8px;
  border:1px solid #e5e7eb;
}
#ap-drawer .ap-color {
  width:4px;
  border-radius:999px;
  background:#74b2d4;
  margin-right:8px;
}
#ap-drawer .ap-main {
  flex:1;
  min-width:0;
}
#ap-drawer .ap-title {
  font-weight:600;
  font-size:14px;
}
#ap-drawer .ap-note {
  color:#4b5563;
}
#ap-drawer .ap-meta {
  font-size:12px;
  color:#6b7280;
  display:flex;
  flex-wrap:wrap;
  gap:6px;
  align-items:center;
}
#ap-drawer .ap-emps {
  margin-top:4px;
  display:flex;
  flex-wrap:wrap;
  gap:4px;
}
#ap-drawer .ap-emp {
  display:inline-flex;
  align-items:center;
  gap:4px;
  padding:2px 6px;
  border-radius:999px;
  border:1px solid #e5e7eb;
  background:#f9fafb;
  font-size:11px;
}
#ap-drawer .ap-emp-img {
  width:18px;
  height:18px;
  border-radius:999px;
  object-fit:cover;
}
#ap-drawer .ap-actions .btn-icon {
  padding:.15rem .25rem;
  border-radius:6px;
  font-size:15px;
}

/* Footer layout for ap drawer */
#ap-drawer .notes-foot{
  display:block;
  padding:.7rem .8rem;
}
#ap-drawer #ap-form .form-group{
  margin-bottom:.4rem;
}
#ap-drawer #ap-form small.text-muted{
  font-size:11px;
}

#ap-customer_search_group {
  display: none !important;
}
/* Make the appointments drawer wider */
#ap-drawer.notes-drawer {
    width: 960px;          /* nice and wide */
    max-width: 90vw;       /* don't explode on small screens */
}

/* Optional: on very big screens, give it even more room */
@media (min-width: 1600px) {
    #ap-drawer.notes-drawer {
        width: 1100px;
    }
}
/* Flex layout: list + form side by side */
#ap-drawer .ap-layout {
    display: flex;
    flex-direction: row;
    height: calc(100vh - 60px); /* 100vh minus header height */
    padding: 0.75rem 0.75rem 0.75rem 0.75rem;
    box-sizing: border-box;
}

/* Left side: appointments list */
#ap-drawer .ap-list-wrapper {
    flex: 1.2;                      /* a bit wider than form */
    padding-right: 0.75rem;
    border-right: 1px solid #e5e5e5;
    overflow-y: auto;
}

/* Right side: form */
#ap-drawer .ap-form-wrapper {
    flex: 1;
    padding-left: 0.75rem;
    overflow-y: auto;
}
/* Grid: 4 appointments per row */
#ap-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    grid-gap: 0.5rem; /* space between cards */
}

/* Appointment card styling */
#ap-list .ap-item {
    background: #fff;
    border: 1px solid #e2e6ea;
    border-radius: 4px;
    padding: 0.5rem 0.6rem;
    font-size: 12px;
    cursor: pointer;
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}

#ap-list .ap-item:hover {
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transform: translateY(-1px);
}

/* Title + meta inside card (optional) */
#ap-list .ap-item-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#ap-list .ap-item-meta {
    color: #6c757d;
    font-size: 11px;
}
@media (max-width: 1200px) {
    #ap-list {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 992px) {
    #ap-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    #ap-list {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    /* On small screens stack list and form vertically again */
    #ap-drawer .ap-layout {
        flex-direction: column;
        height: auto;
    }
    #ap-drawer .ap-list-wrapper,
    #ap-drawer .ap-form-wrapper {
        border: none;
        padding: 0;
        max-height: 50vh;
    }
}
.kb-menu-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .35rem;
  width: 100%;
}

.kb-menu-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 1.4rem;
  padding: 0 .35rem;
  border-radius: 999px;
  font-size: 11px;
  line-height: 1.3;
  background: #f1f3f5;
  color: #495057;
}

.kb-menu-pill--ap { background: #e7f5ff; }  /* Termine */
.kb-menu-pill--pt { background: #fff3bf; }  /* Aufgaben */
.ap-appointment-group {
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    padding: 10px 12px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
}

.ap-appointment-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    padding-bottom: 6px;
    border-bottom: 1px dashed #e5e7eb;
}

.ap-appointment-title {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.ap-appointment-sub {
    font-size: 12px;
    color: #6b7280;
}

.ap-appointment-type {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 999px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 11px;
}

.ap-appointment-employees {
    margin-top: 4px;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.ap-appointment-employee {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    border-radius: 999px;
    background: #f9fafb;
    font-size: 11px;
    color: #4b5563;
}

.ap-appointment-employee-avatar {
    width: 18px;
    height: 18px;
    border-radius: 999px;
    object-fit: cover;
}

.ap-appointment-actions {
    flex-shrink: 0;
}

</style>
<style>
  /* ---------- PER-CARD LIVE FEED (COMPACT) ---------- */
  .live-feed-bar{
      display:flex;
      align-items:center;
      margin-top:6px;
      border-radius:999px;
      overflow:hidden;
      background:#ffffff;
      color:#e5e7eb;
      font-size:11px;
      padding:4px 8px;
  }

  .live-feed-left{
      flex:0 0 auto;
      display:flex;
      align-items:center;
      justify-content:center;
      width:26px;
      height:26px;
      border-radius:999px;
      background:linear-gradient(135deg, #95c11f, #95c11feb);
      margin-right:8px;
  }

  .live-feed-icon{
      display:flex;
      align-items:center;
      justify-content:center;
  }
  .live-feed-icon i{
      font-size:13px;
  }

  .live-feed-body{
      flex:1 1 auto;
      min-width:0;
      display:flex;
      flex-direction:column;
      justify-content:center;
      gap:2px;
  }

  .live-feed-line{
      display:flex;
      align-items:center;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      gap:.25rem;
  }

  .live-feed-title{
      font-weight:600;
      color:#5b6470;
  }

  .live-feed-dot{
      color:rgba(248,250,252,.6);
  }

  .live-feed-text{
      color:#cbd5f5;
      opacity:.9;
      min-width:0;
  }

  .live-feed-meta{
      display:flex;
      align-items:center;
      gap:.4rem;
      font-size:10px;
      color:#9ca3af;
  }

  .live-feed-pill{
      display:inline-flex;
      align-items:center;
      padding:1px .55rem;
      border-radius:999px;
      border:1px solid rgba(148,163,184,.6);
      background:rgba(15,23,42,.85);
      color:#e5e7eb;
      font-size:9px;
      text-transform:uppercase;
      letter-spacing:.08em;
      white-space:nowrap;
  }

  .live-feed-time{
      display:inline-flex;
      align-items:center;
      gap:.18rem;
  }

  .live-feed-counter{
      margin-left:auto;
      opacity:.7;
  }

  .live-feed-controls{
      flex:0 0 auto;
      display:flex;
      align-items:center;
      gap:.15rem;
      margin-left:6px;
  }

  .live-feed-btn{
      width:24px;
      height:24px;
      border-radius:999px;
      border:1px solid rgba(148,163,184,.35);
      background:rgba(15,23,42,.95);
      display:flex;
      align-items:center;
      justify-content:center;
      cursor:pointer;
      padding:0;
      color:white;
      transition:background .15s ease,transform .15s ease,border-color .15s ease;
  }

  .live-feed-btn:hover{
      background:#77a763;
      border-color:#77a763;
      transform:translateY(-1px);
  }

  .live-feed-btn i{
      font-size:12px;
  }

  .live-feed-bar.live-feed--empty .live-feed-pill,
  .live-feed-bar.live-feed--empty .live-feed-time,
  .live-feed-bar.live-feed--empty .live-feed-counter{
      opacity:.55;
  }

  .live-feed-bar.live-feed--paused{
      opacity:.88;
  }

  @media (max-width:768px){
      .live-feed-bar{
          border-radius:16px;
          padding:4px 8px;
      }
  }

  /* Slight extra slimming for cards */
  .card-live-feed {
    margin-top:6px;
    border-radius:999px;
    font-size:11px;
  }

  .card-live-feed .live-feed-body {
    overflow:hidden;
    white-space:nowrap;
  }

  /* Ticker for long text */
  .live-feed-text.live-feed-animate {
    display:inline-block;
    animation:liveFeedTicker 10s linear infinite;
  }

  @keyframes liveFeedTicker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
  }
</style>

<style>
  /* ---- Live Feed Modal ---- */
.lfm-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  z-index: 10000; /* Increased z-index to be above drawers */
  display: none; /* Default state */
}
.lfm-shell {
  position: fixed;
  inset: 5% 8%;
  max-width: 1200px;
  margin: 0 auto;
  background: #ffffff;
  color: #333;
  border-radius: 18px;
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
  display: none; /* Default state */
  flex-direction: column;
  z-index: 10001; /* Increased z-index */
  overflow: hidden;
}

@media (max-width: 768px) {
  .lfm-shell {
    inset: 4% 2%;
  }
}

.lfm-header {
  padding: 16px 20px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.4);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.lfm-title {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
}

.lfm-subtitle {
  font-size: 13px;
  color: #9ca3af;
  margin-top: 2px;
}

/* Ensure flex display when active */
.lfm-shell[style*="display: flex"],
.lfm-shell[style*="display:flex"] {
  display: flex !important;
}

@media (max-width: 768px) {
  .lfm-shell {
    inset: 2% 2%;
  }
}

.lfm-header-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.lfm-body {
  padding: 12px 20px 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  height: 100%;
}

.lfm-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  font-size: 12px;
}

.lfm-pill {
  padding: 3px 10px;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.45);
  background: rgba(15, 23, 42, 0.9);
  color: #e5e7eb;
}
.lfm-pill.muted {
  color: #9ca3af;
  border-style: dashed;
}

.lfm-filters {
  display: inline-flex;
  gap: 4px;
  padding: 2px;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.85);
  border: 1px solid rgba(148, 163, 184, 0.6);
}

.lfm-filter-btn {
  border: none;
  background: transparent;
  color: #9ca3af;
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 999px;
  cursor: pointer;
  white-space: nowrap;
}
.lfm-filter-btn.is-active {
  background: #85b22f;
  color: #022c22;
}

.lfm-icon-btn {
  border: none;
  background: rgba(15, 23, 42, 0.8);
  border-radius: 999px;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #9ca3af;
}
.lfm-icon-btn:hover {
  background: rgba(30, 64, 175, .75);
  color: #e5e7eb;
}

.lfm-list {
  margin-top: 6px;
  padding: 6px 0 4px;
  border-radius: 12px;
  background: #ffffffff; 
  overflow-y: auto;
  flex: 1;
}

/* Each item row */
.lfm-item {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 10px;
  padding: 9px 12px;
  border-bottom: 1px solid rgba(30, 64, 175, 0.4);
  font-size: 13px;
}
.lfm-item:last-child {
  border-bottom: none;
}

.lfm-item-type {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 70px;
  padding: 4px 8px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 500;
}
.lfm-item-type.task {
  background: rgba(34, 197, 94, 0.1);
  color: #4ade80;
  border: 1px solid rgba(34, 197, 94, 0.6);
}
.lfm-item-type.appointment {
  background: rgba(59, 130, 246, 0.1);
  color: #60a5fa;
  border: 1px solid rgba(59, 130, 246, 0.6);
}
.lfm-item-type.ticket {
  background: rgba(248, 113, 113, 0.08);
  color: #fca5a5;
  border: 1px solid rgba(248, 113, 113, 0.6);
}

.lfm-item-main {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.lfm-item-title {
  font-size: 13px;
  font-weight: 600;
  color: #515152;
}
.lfm-item-sub {
  font-size: 12px;
  color: #aaaaaaff;
}
.lfm-item-meta {
  font-size: 11px;
  color: #515152;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 2px;
}
.lfm-item-badge {
  padding: 2px 7px;
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.95);
  border: 1px solid rgba(148, 163, 184, 0.7);
}

.lfm-item-time {
  font-size: 12px;
  color: #515152;
  text-align: right;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.lfm-item-time span:first-child {
  font-weight: 500;
}
.lfm-item-time span:last-child {
  font-size: 11px;
  color: #9ca3af;
}

/* Date Age Indicators */
.age-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
    vertical-align: middle;
}
.age-green { background-color: #93c21c; box-shadow: 0 0 4px rgba(40,167,69,0.5); }
.age-orange { background-color: #fd7e14; box-shadow: 0 0 4px rgba(253,126,20,0.5); }
.age-red { background-color: #dc3545; box-shadow: 0 0 4px rgba(220,53,69,0.5); }

/* Ensure the calendar icon and date stay aligned */
.kb-meta-item.date-item, .list-date-cell {
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.lfm-item-link {
  font-size: 11px;
  color: #ffffffff;
  text-decoration: none;
}
.lfm-item-link:hover {
  text-decoration: underline;
}

/* empty state */
.lfm-empty {
  padding: 16px;
  text-align: center;
  font-size: 13px;
  color: #9ca3af;
}

.notes-composer {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
}

.notes-quill {
    width: 100%;
}

.notes-quill .ql-container {
    border-radius: .25rem;
}

.notes-quill .ql-editor {
    min-height: 80px;
    max-height: 220px;
    overflow-y: auto;
}

/* Quill inside notes drawer */
#notesDrawer .ql-toolbar {
    border-radius: 8px 8px 0 0;
    border-color: #e5e7eb;
    background: #f9fafb;
    padding: 4px 8px;
}

#notesDrawer .ql-container {
    border-radius: 0 0 8px 8px;
    border-color: #e5e7eb;
    min-height: 80px;
    max-height: 150px;
    font-size: 0.875rem;
}

/* slightly smaller buttons */
#notesDrawer .ql-toolbar .ql-formats button,
#notesDrawer .ql-toolbar .ql-formats .ql-picker {
    height: 22px;
}

/* remove big margin between toolbar and content */
#notesDrawer .ql-editor {
    padding: 6px 8px;
}

/* FORCE HIDE ARCHIVE COLUMN BY DEFAULT */
.kanban-container #archive {
    display: none;
}

/* --- Logic for Icons --- */

/* Default (Unchecked): Hide ON icon, Show OFF icon */
.col-toggle-checkbox ~ .custom-control-label .toggle-icon-on {
    display: none;
}
.col-toggle-checkbox ~ .custom-control-label .toggle-icon-off {
    display: inline-block;
}

/* Checked: Show ON icon, Hide OFF icon */
.col-toggle-checkbox:checked ~ .custom-control-label .toggle-icon-on {
    display: inline-block;
}
.col-toggle-checkbox:checked ~ .custom-control-label .toggle-icon-off {
    display: none;
}

/* --- Logic for Text Readability --- */

/* Default (Unchecked): Grey text */
.col-toggle-checkbox ~ .custom-control-label .toggle-label-text {
    color: #999;
    font-weight: 400;
}

/* Checked: Dark, bold text for better readability */
.col-toggle-checkbox:checked ~ .custom-control-label .toggle-label-text {
    color: #333;
    font-weight: 600;
}
.kb-card-meta .kb-meta-row{
  display:flex;
  align-items:center;
  gap:.5rem;
  flex-wrap:wrap;
}
.kb-card-meta .kb-meta-item{
  display:inline-flex;
  align-items:center;
  gap:.35rem;
  font-size:12px;
  opacity:.95;
}
.kb-card-meta .kb-meta-sep{
  opacity:.6;
  font-size:12px;
}
.kb-card-meta .kb-meta-address{
  display:block;
  margin-top:2px;
  opacity:.85;
}
.kb-branch-name{
  max-width:160px;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
  display:inline-block;
}

</style>

<style>
  .kb-branch { --branch-color: #93c21c; }

  /* SVG + name follow branch color */
  .kb-meta-item.kb-branch { color: var(--branch-color); }

  /* Product circle must win */
  .circle.product_circle{
    background-color: var(--branch-color, #93c21c) !important;
    color: #fff !important;
  }

   .list-action-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    opacity: 0.8;
}
.list-action-bar:hover { opacity: 1; }
.btn-list-icon {
    background: transparent;
    border: none;
    padding: 0;
    cursor: pointer;
    color: #6c757d;
    transition: transform 0.1s;
}
.btn-list-icon:hover { transform: scale(1.1); color: #333; }
.btn-list-icon.note:hover { color: #74b2d4; }
.customer-link {
    color: #333;
    font-weight: 600;
    text-decoration: none;
}
.customer-link:hover { color: #93c21c; text-decoration: underline; }
/* Badge for List Icons */
.btn-list-icon { 
    position: relative; /* Needed for absolute positioning of badge */
}

.btn-list-icon .badge-notes {
    position: absolute;
    top: -6px;
    right: -8px;
    min-width: 16px;
    height: 16px;
    line-height: 16px;
    padding: 0 4px;
    border-radius: 10px;
    background: #93c21c;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    text-align: center;
    pointer-events: none;
    z-index: 10;
}

/* Hide badge if count is 0 or empty */
.btn-list-icon .badge-notes:empty,
.btn-list-icon .badge-notes[data-count="0"] {
    display: none;
}
</style>
<style>
    /* 1. Allow SweetAlert content to overflow so dropdowns aren't cut off */
    .swal2-html-container {
        overflow: visible !important;
        z-index: 2;
    }
    
    .swal2-popup {
        overflow: visible !important;
    }

    /* 2. Force Select2 dropdown to be on top of SweetAlert (z-index 1060+) */
    .select2-container--default .select2-dropdown {
        z-index: 99999999 !important;
    }
    
    /* 3. Style the employee option in the dropdown */
    .employee-option {
        display: flex;
        align-items: center;
        padding: 4px;
    }
    .employee-option img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }
</style>

<style>
  /* ---------- Team hover popover ---------- */
  .team-popover {
    position: fixed;
    z-index: 99999;
    width: 320px;
    max-width: calc(100vw - 24px);
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: 0 18px 50px rgba(15,23,42,.22);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 12px;
    transform: translateY(6px);
    opacity: 0;
    pointer-events: none;
    transition: opacity .12s ease, transform .12s ease;
  }

  /* Kanban Column Toolbar (Search & Sort) */
.column-toolbar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    background: #eef2f7;
    border-bottom: 1px solid #d1d5db;
}
.col-search-input {
    flex: 1;
    height: 26px;
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    transition: border-color 0.2s;
}
.col-search-input:focus {
    outline: none;
    border-color: #93c21c;
    box-shadow: 0 0 0 2px rgba(147,194,28,.15);
}
.col-sort-btn {
    background: #fff;
    border: 1px solid #cbd5e1;
    color: #64748b;
    border-radius: 4px;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}
.col-sort-btn:hover {
    background: #f1f5f9;
    color: #93c21c;
    border-color: #93c21c;
}
  .team-popover.is-open{
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }

  .team-popover__title{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom: 10px;
  }
  .team-popover__title .t1{
    font-weight: 800;
    font-size: 13px;
    letter-spacing: .2px;
    color: #0f172a;
  }
  .team-popover__title .t2{
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
  }

  .team-popover__list{
    display:flex;
    flex-direction:column;
    gap:8px;
    max-height: 260px;
    overflow:auto;
    padding-right: 4px;
  }

  .team-popover__item{
    display:flex;
    align-items:center;
    gap:10px;
    padding: 8px 10px;
    border-radius: 12px;
    border: 1px solid rgba(15,23,42,.06);
    background: rgba(255,255,255,.75);
  }
  .team-popover__item:hover{
    background: rgba(241,245,249,.85);
    border-color: rgba(15,23,42,.10);
  }

  /* Card Border Colors */
  .card.status-playing { border-left-color: #93c21c !important; }
  .card.status-paused { border-left-color: #f3c12f !important; }
  .card.status-stopped { border-left-color: #dc3545 !important; }

  /* Button Highlighting */
  .kb-menu-item[data-run="playing"]:hover { background: #e7f5d0; }
  .kb-menu-item[data-run="paused"]:hover { background: #fff4d6; }
  .kb-menu-item[data-run="stopped"]:hover { background: #fde8e8; }

  .team-popover__avatar{
    width: 34px;
    height: 34px;
    border-radius: 999px;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 8px 18px rgba(15,23,42,.12);
    flex: 0 0 auto;
  }
  .team-popover__name{
    font-weight: 800;
    font-size: 13px;
    color: #0f172a;
    line-height: 1.2;
  }
  .team-popover__meta{
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    line-height: 1.2;
    margin-top: 2px;
  }

  /* optional: subtle cursor hint on team stacks */
  ul[data-team-hover] { cursor: pointer; }

    /* ---------- Team hover popover (with Assigned by + Date) ---------- */
  .team-popover__meta{
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    line-height: 1.25;
    margin-top: 2px;
  }
  .team-popover__meta .lbl{ color:#94a3b8; font-weight:700; margin-right:6px; }
  .team-popover__meta .val{ color:#0f172a; font-weight:800; }
  .team-popover__meta .sep{ margin: 0 8px; color:#cbd5e1; }
  .team-popover__meta .date{ white-space: nowrap; }
  /* --- List View Specific Overrides --- */

  /* 1. Status Block in Table */
  .table .kb-status {
      margin-top: 6px;
      padding: 6px 8px;
      background: #f8f9fa; /* Lighter background for table */
      border: 1px solid #e9ecef; /* Subtle border */
      font-size: 0.8rem;
      min-width: 180px; /* Ensure it doesn't get too squashed */
      max-width: 240px;
  }
  .table .kb-status .badge {
      margin-bottom: 4px; /* Space between badge and metadata */
  }

  /* 2. Live Feed in Table (Customer Column) */
  .table .live-feed-bar {
      margin-top: 8px;
      background: #fdfdfd;
      border: 1px solid #f0f0f0;
      width: 100%;
      max-width: 450px; /* Prevent it from stretching infinitely */
  }

  /* 3. Adjust Table alignment for top vertical alignment */
  .table tbody tr td {
      vertical-align: top !important;
  }
</style>
<style>
  #ap-tab-calendar { height: calc(100vh - 140px); } /* adjust if needed */
  #ap-calendar-wrap { height: calc(100% - 48px); }  /* subtract header */
  #ap-fullcalendar { height: 100%; }

    /* Date Age Indicators (Traffic Light System) */
  .traffic-light-wrapper {
      display: inline-flex;
      gap: 4px;
      align-items: center;
      background: #f8f9fa;
      padding: 4px 6px;
      border-radius: 12px;
      border: 1px solid #e9ecef;
  }

  /* Kanban positioning: Top right beside the menu */
  .card .traffic-light-wrapper {
      position: absolute;
      top: 8px;
      right: 36px; 
      z-index: 4;
  }

  /* List view adjustments */
  .list-date-cell .traffic-light-wrapper {
      margin-bottom: 6px;
  }

  /* Base dot styling */
  .tl-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: #d1d5db; /* Inactive Gray */
      transition: all 0.3s ease;
  }

  /* Active glowing states */
  .tl-dot.tl-green.is-active { background-color: #93c21c; box-shadow: 0 0 6px rgba(40,167,69,0.8); }
  .tl-dot.tl-orange.is-active { background-color: #fd7e14; box-shadow: 0 0 6px rgba(253,126,20,0.8); }
  .tl-dot.tl-red.is-active { background-color: #dc3545; box-shadow: 0 0 6px rgba(220,53,69,0.8); }

  /* Splash Animations */
  @keyframes splash-green { 0% { box-shadow: 0 0 0 0 rgba(40,167,69,0.7); } 70% { box-shadow: 0 0 0 6px rgba(40,167,69,0); } 100% { box-shadow: 0 0 0 0 rgba(40,167,69,0); } }
  @keyframes splash-orange { 0% { box-shadow: 0 0 0 0 rgba(253,126,20,0.7); } 70% { box-shadow: 0 0 0 6px rgba(253,126,20,0); } 100% { box-shadow: 0 0 0 0 rgba(253,126,20,0); } }
  @keyframes splash-red { 0% { box-shadow: 0 0 0 0 rgba(220,53,69,0.7); } 70% { box-shadow: 0 0 0 6px rgba(220,53,69,0); } 100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); } }

  .splash-green { animation: splash-green 1.5s infinite; }
  .splash-orange { animation: splash-orange 1.5s infinite; }
  .splash-red { animation: splash-red 1.5s infinite; }
</style>

@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-0">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h5 class="content-header-title float-left mb-0">KUNDEN ÜBERSICHT</h5> 
          </div>
        </div>
      </div>
    </div>

    {{-- ======= MAIN SHELL ======= --}}
    <section id="basic-tabs-components">
      <div class="pro-layout">
        <!-- MAIN -->
        <div class="pro-main">
          <div class="row">
            <div class="col-sm-12">
              <div class="cards overflow-hidden">
                <div class="card-content">
                  <div class="card-body">
                    {{-- Tabs header with Feather icons --}}
                   <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-selected="false">
                          <i class="feather icon-columns tab-icon"></i> Kanban
                          <span class="badge badge-secondary ml-1" id="tabCountKanban">{{ $tabCounts['kanban'] ?? 0 }}</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-selected="true">
                          <i class="feather icon-list tab-icon"></i> Liste
                          <span class="badge badge-secondary ml-1" id="tabCountList">{{ $tabCounts['list'] ?? 0 }}</span>
                        </a>
                      </li> 
                      <li class="nav-item">
                        <a class="nav-link" id="junk-tab" data-toggle="tab" href="#junk" role="tab" aria-selected="false">
                          <i class="feather icon-trash tab-icon"></i> Junk
                          <span class="badge badge-secondary ml-1" id="tabCountJunk">{{ $tabCounts['junk'] ?? 0 }}</span>
                        </a>
                      </li>

                      <li class="nav-item">
                        <a class="nav-link" id="ticket-tab" data-toggle="tab" href="#ticket" role="tab" aria-selected="false">
                          <i class="feather icon-list tab-icon"></i> Ticket
                          <span class="badge badge-secondary ml-1" id="tabCountTicket">{{ $tabCounts['ticket'] ?? 0 }}</span>
                        </a>
                      </li>
 

                    </ul>


                    {{-- Tabs content --}}
                    <div class="tab-content">
                      {{-- Kanban --}}
                      <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                        <div id="kanban" class="kanban-container"></div> 

                      </div>

                      {{-- List (AJAX) --}}
                      <div class="tab-pane show active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                          <div class="table-responsive p-3"> 
                                <div class="d-flex justify-content-end align-items-center mb-2 rounded p-1 border">
                                    <label for="listSortSelect" class="small text-muted mb-0 mr-2 font-weight-bold">Sortieren:</label>
                                    
                                    <select id="listSortSelect" class="custom-select custom-select-sm" style="width: auto; max-width: 250px;">
                                        <optgroup label="Datum">
                                            <option value="created_at|desc" selected>Datum (neueste zuerst)</option>
                                            <option value="created_at|asc">Datum (älteste zuerst)</option>
                                        </optgroup>
                                        <optgroup label="Zuletzt aktualisiert">
                                            <option value="updated_at|desc">Aktualisiert (neueste)</option>
                                            <option value="updated_at|asc">Aktualisiert (älteste)</option>
                                        </optgroup>
                                        <optgroup label="Kunde">
                                            <option value="customer_lastname|asc">Kunde (A-Z)</option>
                                            <option value="customer_lastname|desc">Kunde (Z-A)</option>
                                        </optgroup>
                                        <optgroup label="Ort">
                                            <option value="city|asc">Ort (A-Z)</option>
                                            <option value="city|desc">Ort (Z-A)</option>
                                        </optgroup>
                                        <optgroup label="Status">
                                            <option value="status|asc">Status (A-Z)</option>
                                            <option value="status|desc">Status (Z-A)</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <table class="table table-striped table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Datum</th>
                                            <th>Kunde</th>
                                            <th>Ort</th>
                                            <th>Produkt</th>
                                            <th>Mitarbeiter</th>
                                            <th>Status</th>
                                            <th>Phase</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kanbanTableBody">
                                        <tr><td colspan="8" class="text-center text-muted">Lade Daten…</td></tr>
                                    </tbody>
                                </table>
                                
                                <div id="listPagination" class="d-flex justify-content-center py-2"></div>
                            </div>
                      </div>
 

                      {{-- Junk (server-rendered partial) --}}
                      <div class="tab-pane" id="junk" aria-labelledby="junk-tab" role="tabpanel">
                        @include('admin.kanban.partials.junk', ['junk' => $junk])
                      </div>

                     
                      <div class="tab-pane" id="ticket" aria-labelledby="ticket-tab" role="tabpanel">
                       @include('admin.kanban.partials.ticket', [
                          'tickets' => $tickets ?? null, // ← $tickets is never set in kanban()
                          'total'   => $tabCounts['ticket'] ?? 0
                        ])
                      </div>
 

                    </div><!-- /tab-content -->

                  </div><!-- /card-body -->
                </div><!-- /card-content -->
              </div><!-- /cards -->
            </div><!-- /col -->
          </div><!-- /row -->
        </div><!-- /pro-main -->

        <!-- ACTION RAIL -->
        <aside class="pro-rail" aria-label="Aktionen">
          <button class="rail-btn" id="btnOpenDrawer" title="Übersicht & Filter">
            <i class="feather icon-sliders"></i>
            <span id="filterBadge" class="rail-badge d-none">0</span>
          </button>
        </aside>
      </div><!-- /pro-layout -->
    </section>

    {{-- ======= SINGLE DRAWER: Übersicht & Filter ======= --}}
    <div class="drawer-backdrop" id="drawerBackdrop"></div>
    <aside class="drawer" id="sideDrawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
      <div class="drawer-header">
        <div class="d-flex align-items-center">
          <i class="feather icon-sliders mr-2"></i>
          <h5 id="drawerTitle" class="mb-0">Übersicht &amp; Filter</h5>
          <span id="tabFilterCount" class="tab-badge-inline d-none ml-2">0</span>
        </div>
        <div class="d-flex align-items-center">
          <button class="btn btn-sm btn-outline-secondary mr-1" id="btnClearFilters"><i class="feather icon-rotate-ccw"></i> Alles löschen</button>
          <button class="btn btn-sm btn-primary" id="btnApplyFilters"><i class="feather icon-check-circle"></i> Anwenden</button>
          <button class="btn btn-sm btn-outline-secondary ml-1" data-close-drawer><i class="feather icon-x"></i></button>
        </div>
      </div>

      {{-- Chips summary of active filters --}}
      <div class="px-3 pt-2">
        <div id="activeFilterChips" class="chips"></div>
      </div>

      <div class="drawer-body">
        <!-- SUMMARY (top) -->
        <div id="view-summary" class="mb-1">
          <div class="row text-center" id="summaryStats" style="justify-content:center">
            <div id="cardEmployees" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Verantwortliche</strong>
                <div id="totalEmployees" class="h4">{{ $totalEmployees ?? 0 }}</div>
              </div>
            </div>
            <div id="cardProducts" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Produkt</strong>
                <div id="totalProduct" class="h4">{{ $totalProducts ?? 0 }}</div>
              </div>
            </div>
            <div id="cardCustomers" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Kunde</strong>
                <div id="totalCustomer" class="h4">{{ $totalCustomers ?? 0 }}</div>
              </div>
            </div>
            <div id="cardAnfragen" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Nachfrage</strong>
                <div id="totalAnfrage" class="h4">{{ ($tabCounts['kanban'] ?? 0) }}</div>
              </div>
            </div>

            <div id="cardOffen" class="col-12 summary-card mb-2">
              <div class="border rounded py-2 bg-orange text-white" style="background:#f49f43;color:white!important;">
                <strong>Offen</strong>
                <div id="statusOffen" class="h4 text-white">
                  {{ $statusCounts['offen'] ?? 0 }} <small>({{ $statusPercentages['offen'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardZusage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-primary text-white">
                <strong>Zusage</strong>
                <div id="statusZusage" class="h4 text-white">
                  {{ $statusCounts['zusage'] ?? 0 }} <small>({{ $statusPercentages['zusage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardAbsage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-danger text-white">
                <strong>Absage</strong>
                <div id="statusAbsage" class="h4 text-white">
                  {{ $statusCounts['absage'] ?? 0 }} <small>({{ $statusPercentages['absage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <hr class="my-2">

        <!-- FILTER (below summary) -->
        <div id="view-filter">
          <form id="kanbanFilterForm" class="row align-items-end g-2">
            <div class="col-md-6">
              <label for="customerFilter" class="form-label d-flex align-items-center">
                Kunde <span class="badge badge-secondary ml-2 d-none" id="countCustomers">{{ $totalCustomers ?? 0 }}</span>
              </label>
              <select name="customer" id="customerFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($customers as $customer)
                  <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="stageFilter" class="form-label">Phase</label>
              <select name="stage" id="stageFilter" class="form-control select2">
                <option value="">Alle Phasen</option>
                @foreach(($stageNames ?? ['lead'=>'Lead','offer'=>'Verkauf','deal'=>'Auftrag','project'=>'Montage','completed'=>'Abschluss']) as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="leadAgeFilter" class="form-label">Lead-Alter (Farbe)</label>
              <select name="lead_age" id="leadAgeFilter" class="form-control select2">
                <option value="">Alle Zeiten</option>
                <option value="green">🟢 Neu (< 24h)</option>
                <option value="orange">🟠 Letzter Tag (24 - 48h)</option>
                <option value="red">🔴 Überfällig (> 48h)</option>
              </select>
            </div>

            <div class="col-md-6">
            <label for="branchFilter" class="form-label d-flex align-items-center">
              Filiale
              <span class="badge badge-secondary ml-2 d-none" id="countBranches">{{ count($branches ?? []) }}</span>
            </label>

            <select name="branch" id="branchFilter" class="form-control select2">
              <option value="">Alle</option>
              @foreach (($branches ?? []) as $b)
                <option value="{{ $b->id }}" data-color="{{ $b->color ?? '#93c21c' }}">
                  {{ $b->branch }}
                </option>
              @endforeach
            </select>
          </div>


            <div class="col-md-6">
              <label for="employeeFilter" class="form-label d-flex align-items-center">
                Mitarbeiter <span class="badge badge-secondary ml-2 d-none" id="countEmployees">{{ $totalEmployees ?? 0 }}</span>
              </label>
             <select name="employee" id="employeeFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($employees as $employee)
                  <option value="{{ $employee->id }}">
                    {{ $employee->name }} {{ $employee->lastname }}
                  </option>
                @endforeach
              </select>

            </div>

            <div class="col-md-6">
              <label for="departmentFilter" class="form-label d-flex align-items-center">
                Abteilung <span class="badge badge-secondary ml-2 d-none" id="countDepartments">{{ $totalDepartments ?? 0 }}</span>
              </label>
              <select name="department" id="departmentFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($departments as $department)
                  <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="productFilter" class="form-label d-flex align-items-center">
                Produkt <span class="badge badge-secondary ml-2 d-none" id="countProducts">{{ $totalProducts ?? 0 }}</span>
              </label>
              <select name="product" id="productFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($products as $product)
                  <option value="{{ $product->id }}">{{ $product->article_group }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="interestFilter" class="form-label">Interesse</label>
              <select name="interest" id="interestFilter" class="form-control select2">
                <option value="">Alle Interessen</option>
                <option value="interest">Kaufinteresse</option>
                <option value="intent">Kaufabsicht</option>
                <option value="option">Kaufoption</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="dateFrom" class="form-label">Von (Datum)</label>
              <input type="date" name="date_from" id="dateFrom" class="form-control" />
            </div>

            <div class="col-md-6">
              <label for="dateTo" class="form-label">Bis (Datum)</label>
              <input type="date" name="date_to" id="dateTo" class="form-control" />
            </div>


           <div class="col-12">
              <hr class="my-2">
              <label class="form-label mb-3 font-weight-bold text-dark">
                  <i class="feather icon-layout mr-1"></i> Spalten Sichtbarkeit
              </label>

              <div class="d-flex flex-wrap" id="columnTogglesContainer">
                  @foreach(($stageNames ?? ['lead'=>'Lead','offer'=>'Verkauf','deal'=>'Auftrag','project'=>'Montage','completed'=>'Abschluss', 'archive'=>'Archiv']) as $key => $label)
                      <div class="custom-control custom-checkbox mr-3 mb-2">
                          <input type="checkbox" 
                                class="custom-control-input col-toggle-checkbox" 
                                id="toggleCol_{{ $key }}" 
                                value="{{ $key }}"
                                {{ $key !== 'archive' ? 'checked' : '' }}>
                          
                          <label class="custom-control-label d-flex align-items-center" for="toggleCol_{{ $key }}" style="cursor: pointer; user-select: none;">
                              {{-- Icon for ON --}}
                              <span class="toggle-icon-on mr-1">
                                  <i class="feather icon-eye text-success"></i>
                              </span>
                              {{-- Icon for OFF --}}
                              <span class="toggle-icon-off mr-1">
                                  <i class="feather icon-eye-off text-muted"></i>
                              </span>
                              
                              {{-- Text Label --}}
                              <span class="toggle-label-text">{{ $label }}</span>
                          </label>
                      </div>
                  @endforeach
              </div>
          </div>

            <div class="col-12 small text-muted mt-2">
              Tipp: <kbd>Enter</kbd> = Anwenden, <kbd>Esc</kbd> = Schließen.
            </div>
          </form>
        </div>
      </div>
    </aside>


    <!-- Live Feed Modal Backdrop -->
      <div id="liveFeedModalBackdrop" class="lfm-backdrop" style="display:none;"></div>

      <!-- Live Feed Modal -->
      <div id="liveFeedModal"
          class="lfm-shell"
          role="dialog"
          aria-modal="true"
          aria-labelledby="liveFeedModalTitle"
          style="display:none;">

        <div class="lfm-header">
          <div>
            <h3 id="liveFeedModalTitle" class="lfm-title">Aktivitäten</h3>
            <div class="lfm-subtitle" id="liveFeedModalSubtitle">Kunde</div>
          </div>

          <div class="lfm-header-right">
            <div class="lfm-filters" id="liveFeedTypeFilters">
              <button type="button" class="lfm-filter-btn is-active" data-type="all">
                Alle
              </button>
              <button type="button" class="lfm-filter-btn" data-type="task">
                Aufgaben
              </button>
              <button type="button" class="lfm-filter-btn" data-type="appointment">
                Termine
              </button>
              <button type="button" class="lfm-filter-btn" data-type="ticket">
                Tickets
              </button>
            </div>

            <button type="button"
                    class="lfm-icon-btn"
                    id="liveFeedModalClose"
                    aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </div>

        <div class="lfm-body">
          <div class="lfm-meta">
            <span class="lfm-pill" id="liveFeedModalCount">0 Einträge</span>
            <span class="lfm-pill muted">
              <i class="feather icon-clock"></i>
              nach Nähe zu jetzt sortiert
            </span>
          </div>

          <div class="lfm-list" id="liveFeedModalList">
            <!-- Dynamisch gefüllt -->
          </div>
        </div>
      </div>

 

      <!-- Lead History Drawer -->
      <div id="lh-drawer" class="lh-root" aria-hidden="true" role="dialog" aria-labelledby="lh-title">
        <div class="lh-backdrop" data-lh-close></div>

        <aside class="lh-panel" tabindex="-1">
          <header class="lh-header">
            <h5 id="lh-title" class="mb-0">
              <i class="feather icon-activity mr-2"></i>
              <span id="lh-title-text">Verlauf</span>
            </h5>
            <button class="btn btn-sm btn-outline-secondary" data-lh-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </header>

          <section class="lh-body">
            <div class="row no-gutters">
              <div class="col-lg-7 pr-lg-2 border-right">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-trending-up mr-1"></i> Phasenverlauf</h6>
                  <ul id="lh-timeline" class="lh-timeline list-unstyled mb-0"></ul>
                </div>
              </div>
              <div class="col-lg-5 pl-lg-2">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-list mr-1"></i> Aktivitäten & Notizen</h6>
                  <div id="lh-activities" class="lh-list list-group"></div>
                </div>
              </div>
            </div>
          </section>
        </aside>
      </div>
 
      <div id="notesBackdrop" class="notes-backdrop"></div>
      <aside id="notesDrawer" class="notes-drawer" role="dialog" aria-modal="true" aria-labelledby="notesTitle">
          <div class="notes-head">
              <div class="notes-title">
                  <i class="feather icon-message-square"></i>
                  <span id="notesTitle">Kunden-Notizen</span>
                  <span id="notesCountBadge" class="badge badge-secondary" data-count="0">0</span>
              </div>
              <div>
                  <button class="btn btn-sm btn-outline-secondary" data-notes-close>
                      <i class="feather icon-x"></i>
                  </button>
              </div>
          </div>

          <div class="notes-tabs">
              <button type="button"
                      class="notes-tab notes-tab--active"
                      data-notes-tab="notes"
                      aria-selected="true">
                  <i class="feather icon-message-square mr-25"></i> Notizen
                  <span id="tabBadgeNotes" class="badge badge-light ml-25" style="background:#eef2f7; color:#333;">0</span>
              </button>
              
              <button type="button"
                      class="notes-tab"
                      data-notes-tab="customerReport"
                      aria-selected="false">
                  <i class="feather icon-bar-chart-2 mr-25"></i> Kunde Report
                  <span id="tabBadgeCustomerReport" class="badge badge-light ml-25 d-none" style="background:#eef2f7; color:#333;">0</span>
              </button>

              <button type="button"
                      class="notes-tab"
                      data-notes-tab="report"
                      aria-selected="false">
                  <i class="feather icon-bar-chart-2 mr-25"></i> Termin Report
                  <span id="tabBadgeTerminReport" class="badge badge-light ml-25 d-none" style="background:#eef2f7; color:#333;">0</span>
              </button>
          </div>

          <div class="notes-body">
              <div id="notesList" data-notes-panel="notes" aria-live="polite"></div>

              <div id="notesReport" data-notes-panel="report" class="d-none">
                  <div class="text-muted small p-2">
                      Report wird geladen, sobald der Tab „Report“ geöffnet wird.
                  </div>
              </div>

              <div id="customerReportList" data-notes-panel="customerReport" class="d-none">
                  <div class="text-muted small p-2">
                      Report wird geladen, sobald der Tab „Report“ geöffnet wird.
                  </div>
              </div>
          </div>

            <div class="notes-foot">
              <form id="notesForm" class="notes-composer">
                  {{-- Quill editor container --}}
                  <div id="noteEditor" class="notes-quill flex-grow-1"></div>

                  {{-- optional hidden field for fallback / future use --}}
                  <input type="hidden" id="noteText" />

                  <button class="btn btn-primary ml-50" type="submit">
                      <i class="feather icon-send"></i>
                  </button>
              </form>

              <input type="hidden" id="notesCustomerId">
              <input type="hidden" id="notesAlternativeId">
              <input type="hidden" id="notesProductId">
          </div>

      </aside>


          {{-- Appointment Drawer --}}
    <!-- BACKDROP -->
      <div id="ap-backdrop" class="notes-backdrop" data-ap-close></div>

      <!-- DRAWER -->
      <aside id="ap-drawer" class="notes-drawer ap-drawer" role="dialog" aria-modal="true" aria-labelledby="ap-title">
        <!-- Header -->
        <header class="notes-head ap-head">
          <div class="notes-title ap-head-left">
            <span class="ap-head-icon"><i class="feather icon-calendar"></i></span>
            <div class="ap-head-title">
              <div class="ap-head-row">
                <span id="ap-title" class="ap-title">Termine</span>
                <span id="ap-count" class="badge badge-secondary ml-2">0</span>
              </div>
              <div class="ap-head-sub text-muted small">
                Kalender • Liste • Mitarbeiter-Filter
              </div>
            </div>
          </div>

          <div class="ap-head-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-ap-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </header>

        <!-- Tabs -->
        <nav class="ap-tabs" role="tablist" aria-label="Termine Navigation">
          <button type="button" class="ap-tab-link active" data-tab="calendar" role="tab" aria-selected="true">
            <i class="feather icon-layout"></i>
            <span>Übersicht</span>
          </button>
          <button type="button" class="ap-tab-link" data-tab="form" role="tab" aria-selected="false">
            <i class="feather icon-plus-circle"></i>
            <span>Neu / Bearbeiten</span>
          </button>
        </nav>

        <!-- CONTENT -->
        <section class="ap-body">
          <!-- TAB: Calendar/List -->
          <div id="ap-tab-calendar" class="ap-tab-content active" role="tabpanel">
            <!-- Toolbar -->
            <div class="ap-toolbar border-bottom bg-white">
              <div class="ap-toolbar-left">
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary active" data-view="calendar">
                    <i class="feather icon-grid"></i> Kalender
                  </button>
                  <button type="button" class="btn btn-outline-secondary" data-view="cards">
                    <i class="feather icon-list"></i> Liste
                  </button>
                </div>
              </div>

              <div class="ap-toolbar-right">
                <!-- Employee Filter (search appointments by employee) -->
                <div class="ap-filter">
                  <label for="ap-emp-filter" class="ap-filter-label text-muted small d-none d-lg-inline">Mitarbeiter</label>
                  <select id="ap-emp-filter" class="form-control form-control-sm">
                    <option value="">Alle Mitarbeiter</option>
                  </select>
                </div>

                <!-- Jump to appointment -->
                <div class="ap-filter">
                  <label for="ap-jump" class="ap-filter-label text-muted small d-none d-lg-inline">Schnellsuche</label>
                  <select id="ap-jump" class="form-control form-control-sm">
                    <option value="">— Termin auswählen (Springen) —</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Calendar -->
            <div id="ap-calendar-wrap" class="ap-calendar-wrap">
              <div id="ap-fullcalendar" class="ap-fullcalendar"></div>
            </div>

            <!-- Cards/List -->
            <div id="ap-card-view" class="ap-card-view" style="display:none;">
              <div class="text-center text-muted small my-2">Keine Termine geladen.</div>
            </div>
          </div>

          <!-- TAB: Form -->
          <div id="ap-tab-form" class="ap-tab-content" role="tabpanel">
            <div class="p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 id="ap-form-title" class="mb-0 font-weight-bold">Neuer Termin</h5>

                <button type="button" class="btn btn-sm btn-light border" id="ap-btn-back-to-cal">
                  <i class="feather icon-arrow-left"></i> Zurück
                </button>
              </div>

              <form id="ap-form" autocomplete="off">
                <input type="hidden" id="ap-id">
                <input type="hidden" id="ap-customer_id">
                <input type="hidden" id="ap-alternative_id">
                <input type="hidden" id="ap-product_id">

                <div class="form-group mb-2">
                  <label class="small mb-1 font-weight-bold">Titel*</label>
                  <input type="text" class="form-control" id="ap-name" required placeholder="z.B. Beratungsgespräch">
                </div>

                <div class="form-group mb-2">
                  <label class="small mb-1">Notiz / Beschreibung</label>
                  <textarea class="form-control" id="ap-note" rows="2"></textarea>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6 mb-2">
                    <label class="small mb-1">Datum*</label>
                    <input type="date" class="form-control" id="ap-start_date" required>
                  </div>
                  <div class="form-group col-3 mb-2">
                    <label class="small mb-1">Von</label>
                    <input type="time" class="form-control" id="ap-start_time">
                  </div>
                  <div class="form-group col-3 mb-2">
                    <label class="small mb-1">Bis</label>
                    <input type="time" class="form-control" id="ap-end_time">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-4 mb-2">
                    <label class="small mb-1">Art</label>
                    <select class="form-control" id="ap-appointment_type">
                      <option value="">–</option>
                      <option value="Besichtigung">Besichtigung</option>
                      <option value="Beratung">Beratung</option>
                      <option value="Telefonat">Telefonat</option>
                      <option value="Online-Meeting">Online-Meeting</option>
                    </select>
                  </div>
                  <div class="form-group col-md-4 mb-2">
                    <label class="small mb-1">Kontaktweg</label>
                    <select class="form-control" id="ap-contact_mode">
                      <option value="">–</option>
                      <option value="telefon">Telefon</option>
                      <option value="online">Online</option>
                      <option value="vor Ort">Vor Ort</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label class="small mb-1">Prio</label>
                    <select class="form-control" id="ap-priority">
                      <option value="normal">Normal</option>
                      <option value="high">Hoch</option>
                      <option value="low">Niedrig</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label class="small mb-1">Farbe</label>
                    <input type="color" class="form-control" id="ap-color" value="#74b2d4" style="height:36px; padding:2px;">
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label class="small mb-1 font-weight-bold">Mitarbeiter zuweisen</label>
                  <select id="ap-employee_ids" class="form-control select2" multiple style="width:100%">
                    @foreach ($employees as $e)
                      <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="p-2 bg-light border rounded mb-3">
                  <label class="small text-muted text-uppercase font-weight-bold mb-1">Adresse / Ort</label>
                  <input type="text" class="form-control mb-1 form-control-sm" id="ap-full_address" placeholder="Adresse suchen...">
                  <div class="form-row">
                    <div class="col-8 mb-1">
                      <input type="text" id="ap-street" class="form-control form-control-sm" placeholder="Straße">
                    </div>
                    <div class="col-4 mb-1">
                      <input type="text" id="ap-postcode" class="form-control form-control-sm" placeholder="PLZ">
                    </div>
                  </div>
                  <input type="text" class="form-control mb-1 form-control-sm" id="ap-city" placeholder="Ort">
                  <input type="hidden" id="ap-latitude">
                  <input type="hidden" id="ap-longitude">
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                  <button type="button" class="btn btn-outline-danger d-none" id="ap-btn-delete">
                    <i class="feather icon-trash-2"></i> Löschen
                  </button>
                  <button type="submit" class="btn btn-primary ml-auto">
                    <i class="feather icon-save"></i> Speichern
                  </button>
                </div>
              </form>
            </div>
          </div>
        </section>
      </aside>

      <style>
        /* Layout helpers (safe; no theme override) */
        .ap-drawer { display:flex; flex-direction:column; }
        .ap-body { flex: 1 1 auto; min-height: 0; display:flex; flex-direction:column; }
        .ap-tab-content { flex: 1 1 auto; min-height: 0; }
        .ap-toolbar { padding: 10px 12px; display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
        .ap-toolbar-right { display:flex; gap:10px; align-items:center; justify-content:flex-end; flex-wrap:wrap; }
        .ap-filter { display:flex; gap:6px; align-items:center; }
        .ap-filter select { min-width: 260px; }
        .ap-calendar-wrap { height: calc(100vh - 220px); min-height: 420px; padding: 8px; }
        .ap-fullcalendar { height: 100%; }
        .ap-card-view { padding: 12px; display: grid; grid-template-columns: 1fr; gap: 10px; }
      </style>

 
      <div id="pt-backdrop" class="notes-backdrop"></div>
      <aside id="pt-drawer" class="notes-drawer" role="dialog" aria-modal="true" aria-labelledby="pt-title" style="width:1300px !important;">
        <div class="notes-head">
          <div class="notes-title">
            <i class="feather icon-check-square"></i>
            <span id="pt-title">Aufgaben</span>
            <span id="pt-count" class="badge badge-secondary">0</span>
          </div>
          <div>
            <button class="btn btn-sm btn-outline-secondary" data-pt-close><i class="feather icon-x"></i></button>
          </div>
  
        </div>

        <div class="notes-body" id="pt-list" style="background:#f8fafc">
          <div class="text-center text-muted my-2">Lade Aufgaben…</div>
        </div>

        <div class="notes-foot">
          <form id="pt-form" class="notes-composer" autocomplete="off">
            <div class="w-100">
              <input class="form-control mb-1" id="pt-task_title" placeholder="Aufgabentitel*" required>
              <textarea class="form-control mb-1" id="pt-description" placeholder="Beschreibung (optional)"></textarea>
              <div class="d-flex flex-wrap gap-2">
                <input type="date" class="form-control mr-1 mb-1" id="pt-start_date" style="max-width:180px">
                <input type="date" class="form-control mr-1 mb-1" id="pt-due_date" style="max-width:180px">
                <input type="time" class="form-control mr-1 mb-1" id="pt-due_time" style="max-width:140px">
                <select class="form-control mr-1 mb-1" id="pt-priority" style="max-width:150px">
                  <option value="normal">Normal</option>
                  <option value="high">Hoch</option>
                  <option value="low">Niedrig</option>
                </select>
                <input type="color" class="form-control mb-1" id="pt-color" value="#8fc73e" style="max-width:70px; padding:0 2px;">
              </div>

              {{-- Hide this whole block when steps are used --}}
              <div id="pt-employee-wrap" class="mt-1">
                <label class="small text-muted mb-1">Mitarbeiter (für gesamte Aufgabe)</label>
                <select id="pt-employee_ids" class="form-control select2" multiple data-width="100%">
                  @foreach ($employees as $e)
                    <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Steps UI --}}
              <div class="border rounded p-2 mt-2 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Arbeitsschritte</strong>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="pt-add-step"><i class="feather icon-plus"></i> Schritt</button>
                </div>
                <div id="pt-steps" class="mt-2"></div>
                <small class="text-muted d-block mt-1">Wenn mindestens ein Schritt existiert, wird die Mitarbeiterauswahl der Hauptaufgabe ausgeblendet und pro Schritt vergeben.</small>
              </div>
            </div>
            <button class="btn btn-primary ml-2"><i class="feather icon-save"></i></button>
          </form>

          {{-- Hidden context from Kanban card --}}
          <input type="hidden" id="pt-customer_id">
          <input type="hidden" id="pt-alternative_id">
          <input type="hidden" id="pt-product_id">
        </div>
      </aside>


  </div><!-- /content-wrapper -->
</div>
@stop
 @section('script')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/de.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script async
  src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&language=de&region=DE">
 
</script>

 
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    window.ALL_EMPLOYEES = @json($employees); 
</script>

<script>
/* =============================================================================
 * LeadUI – Core (Segment 1/2)
 * - Config, State, Storage, URL Sync
 * - Utilities + Polyfills
 * - Network layer: safeFetchJSON / postJSON
 * - Filters + Drawer
 * - Kanban renderers
 * - Notes drawer
 * - Junk partial loaders
 * - LiveFeed: per-card mini feed + full-screen modal (LiveFeedModal)
 * =============================================================================*/
(function () {
  "use strict";

  /* --- Polyfills --- */
  window.requestIdleCallback ||= (cb) => setTimeout(() => cb({ timeRemaining: () => 10 }), 0);

  if (!window.CSS || !CSS.escape) {
    window.CSS = {
      ...(window.CSS || {}),
      escape: (s) => String(s).replace(/[^a-zA-Z0-9_\-]/g, "\\$&"),
    };
  }

  /* --- Config --- */
  const APP = {
    EMP_SRC: "{{ asset('images/employee') }}",
    endpoints: {
      kanbanSearch: "/lead/kanban/search", 
      listSearch: "/lead/kanban/ajax", 
      changeStage: "/lead-product/change-stage", 
      progress: "/lead-product/progress", 
      purge: "/lead-product/purge", 

      notesIndex: "/customer-notes", 
      notesStore: "/customer-notes", 
      notesInlineUpdate: (id) => `/customer-notes/inline-update/${id}`, 
      notesDestroy: (id) => `/customer-notes/delete/${id}`, 

      junk: "/lead/kanban/junk", 

      personalTasksIndex: "/personal-tasks/index", 
      personalTasksStore: "/personal-tasks/store", 
      personalTasksUpdate: (id) => `/personal-tasks/${id}/update`, 
      personalTasksDestroy: (id) => `/personal-tasks/${id}/destroy`, 
      ptEmployeesSync: (id) => `/personal-tasks/${id}/employees/sync`, 

      ptStepsIndex: (taskId) => `/personal-tasks/${taskId}/steps`, 
      ptStepsStore: (taskId) => `/personal-tasks/${taskId}/steps`, 
      ptStepsUpdate: (stepId) => `/personal-tasks/steps/${stepId}`, 
      ptStepsDestroy: (stepId) => `/personal-tasks/steps/${stepId}`, 
      ptStepsEmpSync: (stepId) => `/personal-tasks/steps/${stepId}/employees/sync`, 

      ticketize: (id) => `/lead-product/ticketize/${id}`,
      tickets: "/lead/kanban/tickets", 

      appointmentsIndex: "appointments/index", 
      appointmentsStore: "appointments/store", 
      appointmentsUpdate: (id) => `appointments/${id}/update`, 
      appointmentsDestroy: (id) => `appointments/${id}/destroy`, 
      appointmentsCustomerSearch: "appointments/customer-search", 

      reportsIndex: "{{ url('kanban/appointments/reports') }}",
      reportsReact: (id) => "{{ url('kanban/appointments/reports') }}/" + id + "/react",
      reportsComment: (id) => "{{ url('kanban/appointments/reports') }}/" + id + "/comment",
      reportsStore: (appointmentId) => "{{ url('kanban/appointments') }}/" + appointmentId + "/reports",

      customerReportsIndex: "{{ url('kanban/customer-reports') }}", 
      customerReportsStore: "{{ url('kanban/customer-reports') }}", 
      customerReportsComment: (id) => "{{ url('kanban/customer-reports') }}/" + id + "/comment",

      liveFeed: "/lead/kanban/feed",
    },
    stageNames: {
      lead: "Lead",
      offer: "Verkauf",
      deal: "Auftrag",
      project: "Montage",
      completed: "Abschluss",
      archive: "Archiv",
    },
    stageAlias: {
      open: "lead",
      angebot: "offer",
      auftrag: "deal",
      montage: "project",
      abschluss: "completed",
      archiv: "archive",
      archive: "archive",
      complete: "completed",
      reject: "junk",
      rejeck: "junk",
    },
    defaults: {
      sort: { key: "created_at", dir: "desc" },
      page: 1,
    },
    authUserId: "{{ auth()->user()->name ?? '' }}",
  };

  const RUN = {
    badgeTone: { playing: "success", paused: "warning", stopped: "danger" },
    icon: { playing: "icon-play", paused: "icon-pause", stopped: "icon-square" },
    label: { playing: "Aktiv", paused: "Pausiert", stopped: "Gestoppt" },
  };

  /* --- Quill for Notes --- */
  let noteQuill = null;
  function ensureNoteQuill() {
      if (typeof window.Quill === "undefined") return null;
      if (noteQuill) return noteQuill;

      let editorHost = document.getElementById("noteEditor");
      const textarea = document.getElementById("noteText");

      if (!editorHost && textarea) {
          editorHost = document.createElement("div");
          editorHost.id = "noteEditor";
          textarea.parentNode.insertBefore(editorHost, textarea);
          textarea.style.display = "none";
      }

      if (!editorHost) return null;

      noteQuill = new Quill("#" + editorHost.id, {
          theme: "snow",
          placeholder: "Neue Notiz schreiben …",
          modules: {
              toolbar: [
                  ['bold', 'italic', 'underline'],
                  [{ list: 'ordered' }, { list: 'bullet' }],
                  ['link']
              ]
          }
      });

      return noteQuill;
  }
  function getNoteEditorHTML() {
    const textarea = document.getElementById("noteText");
    if (noteQuill) {
      return (noteQuill.root.innerHTML || "").trim();
    }
    return (textarea?.value || "").trim();
  }
  function setNoteEditorHTML(html) {
    const textarea = document.getElementById("noteText");
    if (noteQuill) {
      noteQuill.root.innerHTML = html || "";
      try {
        const len = noteQuill.getLength();
        noteQuill.setSelection(len, len);
      } catch {}
    } else if (textarea) {
      textarea.value = html || "";
    }
  }

  /* --- State --- */
  const STORAGE_KEY = "leadOverview.filters.v4";
  const State = {
    sort: { ...APP.defaults.sort },
    page: APP.defaults.page,
    filtersQS: "",
    lastAppliedQS: "",
    lastKanbanData: [],
    loaded: { kanban: false, list: false },
    req: { kanban: null, list: null },
    statusGroup: null,
    selectedIds: new Set(),
  };

  /* --- Utils --- */
  const qs = (s, ctx = document) => ctx.querySelector(s);
  const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
  const CSRF = () => qs('meta[name="csrf-token"]')?.content || "";
  const isLikelyHTML = (t) => /^\s*</.test(t || "");
  const fmtDE = (v) => {
    try {
      return v ? new Date(v).toLocaleString("de-DE") : "";
    } catch {
      return "";
    }
  };

    const getDateAgeIndicator = (dateString, stage) => {
      // We removed the 'if (currentStage !== "lead") return;' check 
      // so this now runs for ALL columns.

      if (!dateString) return '';
      const targetDate = new Date(dateString);
      if (isNaN(targetDate.getTime())) return '';

      const now = new Date();
      const diffMs = now - targetDate;
      const diffHours = diffMs / (1000 * 60 * 60);

      let state = 'green';
      let title = 'Neu (Unter 24 Stunden)';

      if (diffHours > 48) {
          state = 'red';
          title = 'Überfällig (Älter als 48 Stunden)';
      } else if (diffHours > 24) {
          state = 'orange';
          title = 'Letzter Tag (Läuft in unter 24h ab)';
      }

      return `
      <div class="traffic-light-wrapper" title="${title}">
          <span class="tl-dot tl-green ${state === 'green' ? 'is-active splash-green' : ''}"></span>
          <span class="tl-dot tl-orange ${state === 'orange' ? 'is-active splash-orange' : ''}"></span>
          <span class="tl-dot tl-red ${state === 'red' ? 'is-active splash-red' : ''}"></span>
      </div>`;
  };
  const featherRefreshSoon = () => {
    if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
  };
  const shortNum = (n) => {
    n = Number(n || 0);
    if (n < 1e3) return "" + n;
    if (n < 1e6) return (n / 1e3).toFixed(n % 1e3 ? 1 : 0).replace(/\.0$/, "") + "k";
    if (n < 1e9) return (n / 1e6).toFixed(n % 1e6 ? 1 : 0).replace(/\.0$/, "") + "M";
    return (n / 1e9).toFixed(n % 1e9 ? 1 : 0).replace(/\.0$/, "") + "B";
  };
  const canonicalStage = (s) => {
    const k = String(s || "").toLowerCase();
    return APP.stageNames[k] ? k : APP.stageAlias[k] || "lead";
  };
  const escapeHTML = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));
  
  const branchSVG = (size = 14) => `
    <svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" style="vertical-align:-2px;">
      <path d="M3 21h18"/>
      <path d="M5 21V7a2 2 0 0 1 2-2h3v16"/>
      <path d="M10 21V4h7a2 2 0 0 1 2 2v15"/>
      <path d="M8 9h1"/>
      <path d="M8 12h1"/>
      <path d="M8 15h1"/>
      <path d="M13 9h1"/>
      <path d="M13 12h1"/>
      <path d="M13 15h1"/>
    </svg>
  `;

  const STAGE_ORDER = ["lead", "offer", "deal", "project", "completed", "archive"];
  const stageRank = (s) => STAGE_ORDER.indexOf(canonicalStage(s));
  const isBackward = (from, to) => stageRank(to) < stageRank(from);

  function enforceActionVisibility(cardOrStage) {
    const cards = cardOrStage && cardOrStage.nodeType === 1 ? [cardOrStage] : Array.from(document.querySelectorAll(".card"));
    cards.forEach((c) => {
      const stage = canonicalStage(c.dataset.stage || c.closest(".column")?.id || "lead");
      const hideJunk = stageRank(stage) >= stageRank("deal"); 
      const junkBtn = c.querySelector('[data-act="delete"]');
      if (junkBtn) {
        junkBtn.disabled = hideJunk;
        junkBtn.classList.toggle("d-none", hideJunk);
        junkBtn.setAttribute("aria-hidden", hideJunk ? "true" : "false");
      }
    });
  }

  function stageFilterExcludes(newStage) {
    const p = new URLSearchParams(State.filtersQS || "");
    const f = p.get("stage");
    if (!f) return false;
    return canonicalStage(f) !== canonicalStage(newStage);
  }

  function saveToLocal() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify({
        sort: State.sort,
        page: State.page,
        filtersQS: State.filtersQS,
        statusGroup: State.statusGroup,
      }));
    } catch {}
  }

  function restoreFromLocal() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return;
      const { sort, page, filtersQS, statusGroup } = JSON.parse(raw);
      if (sort?.key && sort?.dir) State.sort = sort;
      if (page) State.page = Number(page) || 1;
      if (typeof filtersQS === "string") State.filtersQS = filtersQS;
      if (statusGroup === null || ["offen", "zusage", "absage"].includes(statusGroup)) State.statusGroup = statusGroup;
    } catch {}
  }

  function syncURL() {
    const url = new URL(location.href);
    const p = new URLSearchParams(State.filtersQS || "");
    p.set("sort_by", State.sort.key);
    p.set("sort_dir", State.sort.dir);
    p.set("page", String(State.page));
    const newQS = p.toString();
    if (url.search.slice(1) !== newQS) {
      url.search = newQS;
      history.replaceState(null, "", url.toString());
    }
  }

  function initFromURL() {
    const p = new URLSearchParams(location.search);
    const form = qs("#kanbanFilterForm");
    if (form && p.size) {
      p.forEach((v, k) => {
        const el = form.elements[k];
        if (el) {
          try { el.value = v; } catch {}
        }
      });
      if (window.jQuery) {
        jQuery(form).find(".select2").each(function () {
          const name = this.getAttribute("name");
          if (name && p.has(name)) jQuery(this).val(p.get(name)).trigger("change");
        });
      }
    }
    State.sort.key = p.get("sort_by") || State.sort.key;
    State.sort.dir = (p.get("sort_dir") || State.sort.dir).toLowerCase() === "asc" ? "asc" : "desc";
    State.page = parseInt(p.get("page") || State.page, 10) || 1;
    State.filtersQS = buildFilterQS();
  }

  /* --- Networking --- */
  function cancel(key) {
    try { State.req[key]?.abort(); } catch {}
    State.req[key] = new AbortController();
    return State.req[key].signal;
  }
  async function safeFetchJSON(url, { method = "GET", headers = {}, body, signal, retries = 0, retryDelay = 240 } = {}) {
    const go = async () => {
      const res = await fetch(url, {
        method, credentials: "same-origin",
        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", ...headers },
        body, signal,
      });
      const text = await res.text();
      if (!res.ok || isLikelyHTML(text)) {
        throw new Error(`HTTP ${res.status} ${res.statusText}`);
      }
      try { return JSON.parse(text); } catch { throw new Error("Invalid JSON"); }
    };
    try {
      return await go();
    } catch (err) {
      if (retries > 0 && method === "GET") {
        await new Promise((r) => setTimeout(r, retryDelay));
        return safeFetchJSON(url, { method, headers, body, signal, retries: retries - 1, retryDelay: retryDelay * 1.6 });
      }
      throw err;
    }
  }
  const postJSON = (url, payload = {}) =>
    safeFetchJSON(url, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() },
      body: JSON.stringify(payload),
    });

  /* --- Filters/UI --- */
  function initSelect2() {
    if (!window.jQuery) return;
    const $d = jQuery("#sideDrawer");
    $d.find(".select2").each(function () {
      const $el = jQuery(this);
      if ($el.hasClass("select2-hidden-accessible")) $el.select2("destroy");
      $el.select2({ placeholder: "Auswählen…", allowClear: true, width: "100%", dropdownParent: $d });
    });
  }

  function getFilterValues() {
    const f = qs("#kanbanFilterForm");
    if (!f) return {};
    const fd = new FormData(f), obj = {};
    fd.forEach((v, k) => (obj[k] = v === "" ? null : v));
    return obj;
  }

function updateFilterBadges() {
    const vals = getFilterValues(); 
    const keys = ["customer", "stage", "employee", "department", "product", "interest", "date_from", "date_to", "lead_age"];
    const n = keys.reduce((t, k) => t + (vals[k] && String(vals[k]).trim() ? 1 : 0), 0) + (State.statusGroup ? 1 : 0);
    const rail = qs("#filterBadge");
    const tab = qs("#tabFilterCount");
    const btn = qs("#btnOpenDrawer");
    if (rail) { rail.textContent = n; rail.classList.toggle("d-none", !n); }
    if (tab) { tab.textContent = n; tab.classList.toggle("d-none", !n); }
    if (btn) btn.classList.toggle("rail-btn--active", !!n);
  }

  function buildFilterQS() {
    const form = qs("#kanbanFilterForm") || document.createElement("form");
    const p = new URLSearchParams(new FormData(form));
    if (State.statusGroup) {
      p.set("status_group", State.statusGroup);
      p.delete("stage");
      const stageSel = qs("#stageFilter");
      if (stageSel) stageSel.value = "";
    } else {
      p.delete("status_group");
    }
    p.set("sort_by", State.sort.key);
    p.set("sort_dir", State.sort.dir);
    p.delete("page");
    return p.toString();
  }

  const Drawer = (() => {
    const el = qs("#sideDrawer"), bd = qs("#drawerBackdrop");
    function open() {
      el?.classList.add("open");
      bd?.classList.add("show");
      document.body.style.overflow = "hidden";
      setTimeout(initSelect2, 10);
      updateFilterBadges();
    }
    function close() {
      el?.classList.remove("open");
      bd?.classList.remove("show");
      document.body.style.overflow = "";
    }
    bd?.addEventListener("click", close);
    qsa("[data-close-drawer]").forEach((b) => b.addEventListener("click", close));
    qs("#btnOpenDrawer")?.addEventListener("click", open);
    return { open, close };
  })();

  function closeOverlays() {
    qs("#drawerBackdrop")?.classList.remove("show");
    qs("#sideDrawer")?.classList.remove("open");
    qs("#notesBackdrop")?.classList.remove("show");
    qs("#notesDrawer")?.classList.remove("open");
    document.body.style.overflow = "";
  }
  
  /* --- Kanban DOM --- */
/* --- Kanban DOM --- */
function ensureColumns() {
  const board = qs("#kanban");
  if (!board) return;
  if (board.querySelector(".column")) return;
  
  const frag = document.createDocumentFragment();
  Object.entries(APP.stageNames).forEach(([id, title]) => {
    const col = document.createElement("div");
    col.className = "column";
    col.id = id;
    col.ondragover = (e) => e.preventDefault();
    
    // Add the new toolbar with Search Input and Sort Button
    col.innerHTML = `
      <h3>
          <span>${title}</span>
          <span class="count-badge" data-count-for="${id}" aria-live="polite">0</span>
      </h3>
      <div class="column-toolbar">
          <input type="text" class="col-search-input" data-col="${id}" placeholder="In ${title} suchen...">
          <button type="button" class="col-sort-btn" data-col="${id}" data-sort="desc" title="Nach Datum sortieren">
              <i class="feather icon-arrow-down"></i>
          </button>
      </div>
      <div class="column-content"></div>
    `;
    frag.appendChild(col);
  });
  
  board.appendChild(frag);
  
  // Bind the search and sort logic immediately after creating columns
  bindColumnTools();
}

// 👇 ADD THIS NEW FUNCTION RIGHT BELOW ensureColumns() 👇
function bindColumnTools() {
    // 1. Search Logic (Filters cards by text)
    document.querySelectorAll('.col-search-input').forEach(input => {
        input.addEventListener('input', function() {
            const colId = this.dataset.col;
            const term = this.value.toLowerCase().trim();
            const container = document.querySelector(`#${colId} .column-content`);
            if (!container) return;
            
            const cards = container.querySelectorAll('.card');
            cards.forEach(card => {
                // Check all text inside the card (Name, City, Tags, etc.)
                const cardText = card.innerText.toLowerCase();
                card.style.display = cardText.includes(term) ? '' : 'none';
            });
        });
    });

    // 2. Date Sorting Logic (Sorts cards up/down)
    document.querySelectorAll('.col-sort-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const colId = this.dataset.col;
            const isDesc = this.dataset.sort === 'desc';
            
            // Toggle state
            this.dataset.sort = isDesc ? 'asc' : 'desc';
            this.innerHTML = isDesc ? '<i class="feather icon-arrow-up"></i>' : '<i class="feather icon-arrow-down"></i>';

            const container = document.querySelector(`#${colId} .column-content`);
            if (!container) return;
            
            const cards = Array.from(container.querySelectorAll('.card'));

            // Sort cards based on the data-updated-at attribute you already set
            cards.sort((a, b) => {
                const dateA = new Date(a.dataset.updatedAt || 0).getTime();
                const dateB = new Date(b.dataset.updatedAt || 0).getTime();
                
                return isDesc ? (dateA - dateB) : (dateB - dateA); 
            });

            // Re-append sorted cards to the DOM (this physically moves them)
            cards.forEach(card => container.appendChild(card));
        });
    });
}
  function clearColumns() {
    qsa(".column .column-content").forEach((el) => (el.innerHTML = ""));
    qsa("#kanban > :not(.column)").forEach((n) => n.remove());
  }

  const colContent = (s) => qs(`#${CSS.escape(s)} .column-content`);

  function updateCounts() {
    qsa(".column").forEach((col) => {
      const n = col.querySelectorAll(".column-content .card").length;
      const b = col.querySelector(".count-badge");
      if (b) b.textContent = String(n);
    });
  }

  function statusBadge(stage) {
    if (["lead", "offer"].includes(stage)) return ["Offen", "warning", "text-dark"];
    if (["deal", "project", "completed"].includes(stage)) return ["Zusage", "success", ""];
    if (["archive", "archiv"].includes(stage)) return ["Archiv", "secondary", ""];
    return ["Absage", "danger", ""];
  }

  function buildStatusBlock(lead) {
    const s = canonicalStage(lead.stage);
    const [txt, tone, extra] = statusBadge(s);
    const ws = String(lead.work_status || "").toLowerCase();
    const hasWS = !!RUN.label[ws];
    const latestPhase = lead.latest_phase || "-";
    const latestAct = lead.latest_activity || "-";
    const timeText = fmtDE(lead.done_date || lead.updated_at) || "-";

    return `
      <div class="kb-status">
        <div><span class="badge bg-${tone} badge-${tone} ${extra}">${txt}</span></div>
        ${hasWS ? `<div class="mt-1"><span class="badge bg-${RUN.badgeTone[ws]} ${ws === "paused" ? "text-dark" : ""}"><i class="feather ${RUN.icon[ws]}"></i> ${RUN.label[ws]}</span></div>` : ""}
        <div class="meta">
          <div class="rowline"><i class="feather icon-box"></i></div><div class="rowline value"><strong>${latestPhase}</strong></div>
          <div class="rowline"><i class="feather icon-check-circle"></i></div><div class="rowline value">${latestAct}</div>
          <div class="rowline"><i class="feather icon-clock"></i></div><div class="rowline value time">${timeText}</div>
        </div>
      </div>`;
  }

  function applyRunStateUI(card, state) {
    const cls = { playing: "status-playing", paused: "status-paused", stopped: "status-stopped" };
    card.classList.remove("status-playing", "status-paused", "status-stopped", "card-has-overlay");
    card.classList.add(cls[state] || cls.playing);
    const overlay = card.querySelector(".card-status-overlay");
    if (!overlay) return;
    if (state === "paused" || state === "stopped") {
      card.classList.add("card-has-overlay");
      overlay.style.display = "flex";
      overlay.innerHTML = `<span class="card-status-badge"><i class="feather ${state === "paused" ? "icon-pause" : "icon-square"}"></i> ${state === "paused" ? "Pause" : "Stopp"}</span>`;
    } else {
      overlay.style.display = "none";
      overlay.innerHTML = "";
    }
    card.dataset.runState = state;
  }

  const cardId = (it) => `card-${it.lead_product_id}`;

  function cardHTML(item, stageKey) {
      "use strict";

      // 1. Define helpers at the top to prevent "esc is not defined" errors
      const safeStr = (v) => (v == null ? "" : String(v));
      const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#039;"
      })[m]);
      const isNonEmpty = (v) => safeStr(v).trim().length > 0;

    const fullName = (() => {
          const fn = safeStr(item?.customer_name).trim();
          const ln = safeStr(item?.customer_lastname).trim();
          const firma = safeStr(item?.firma).trim();
          const name = `${fn} ${ln}`.trim();
          return name || firma || "Unbekannt";
      })();

      const address = [item?.street, item?.postcode, item?.city]
          .map((v) => safeStr(v).trim())
          .filter(Boolean)
          .join(", ");

      const updated = (() => {
          const raw = item?.updated_at;
          const d = raw ? new Date(raw) : new Date();
          return Number.isNaN(d.getTime()) ?
              new Date().toLocaleDateString("de-DE") :
              d.toLocaleDateString("de-DE");
      })();

      const branchName = [
              item?.branch_name,
              item?.branch,
              item?.branch_title,
              item?.department_name,
              item?.department,
          ]
          .map((v) => safeStr(v).trim())
          .find((v) => v.length) || "";

      const employee = item?.employee && item.employee.employee_id ? item.employee : null;
      const fieldEmployee =
          item?.field_employee && item.field_employee.employee_id ? item.field_employee : null;

      const mkEmp = (emp, fallbackTitle) => {
          if (!emp) return null;
          const title =
              `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || fallbackTitle;
          return {
              title,
              image: safeStr(emp?.image).trim(),
              id: Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0,
          };
      };

      const empList = [mkEmp(employee, "Innendienst"), mkEmp(fieldEmployee, "Außendienst")].filter(
          Boolean
      );

      const empHTML =
          empList.length > 0 ?
          `
            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
              ${empList
                .map(
                  (e) => `
                  <li class="avatar pull-up" title="${esc(e.title)}">
                    <img class="media-object rounded-circle"
                        src="${APP.EMP_SRC}/${esc(e.image || "noimage.png")}"
                        height="30" width="30" alt=""
                        style="object-fit:cover;">
                  </li>`
                )
                .join("")}
            </ul>` :
          `<small>&ndash;</small>`;

      // ✅ TEAM HTML
      const teamHTML = (() => {
          // Reuse esc/safeStr from parent scope is fine, or redefine if isolated
          const stageLabel = APP.stageNames?.[canonicalStage(stageKey)] || canonicalStage(stageKey);

          const teamA = Array.isArray(item?.team_assignments) ? item.team_assignments : [];
          const teamM = Array.isArray(item?.team_members) ? item.team_members : [];

          const list = teamA.length ?
              teamA.map(a => ({
                  member: a?.member || null,
                  assigned_at: a?.assigned_at || null,
                  assigned_by_user: a?.assigned_by_user || null,
                  assigned_by: a?.assigned_by || null,
                  stage: a?.stage || null,
                  stage_label: a?.stage_label || null,
              })).filter(x => x.member) :
              teamM.map(m => ({
                  member: m,
                  assigned_at: null,
                  assigned_by_user: null,
                  assigned_by: null,
                  stage: null,
                  stage_label: null,
              }));

          if (!list.length) return "";

          const avatars = list.map((x) => {
              const emp = x.member;
              const id = Number(emp?.id ?? emp?.employee_id ?? emp?.emp_id ?? 0) || 0;
              const img = emp?.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
              const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || "Team";

              const ab = (() => {
                  const u = x?.assigned_by_user;
                  if (u && (u.name || u.lastname)) return `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
                  const bid = Number(x?.assigned_by ?? 0);
                  return bid > 0 ? `Mitarbeiter #${bid}` : "";
              })();

              const at = safeStr(x?.assigned_at || "").trim();

              return `
              <li class="avatar pull-up"
                  data-emp-id="${esc(id)}"
                  data-assigned-by="${esc(ab)}"
                  data-assigned-at="${esc(at)}"
                  data-stage-label="${esc(stageLabel)}"
                  title="${esc(name)}"
                  style="margin-left:-8px; z-index:10;">
                <img class="media-object rounded-circle"
                    src="${esc(img)}"
                    height="26" width="26"
                    alt="${esc(name)}"
                    style="border:2px solid #fff; object-fit:cover;">
              </li>
            `;
          }).join("");

          return `
            <ul class="list-unstyled users-list m-0 d-flex align-items-center"
                data-team-hover
                style="margin-left:10px; padding-left:10px; border-left:1px solid #e0e0e0;">
              ${avatars}
            </ul>`;
      })();

      const hideJunk = stageRank(canonicalStage(stageKey)) >= stageRank("deal");

      return `
        <div class="card-status-overlay" aria-hidden="true"></div>

        ${getDateAgeIndicator(item?.created_at, stageKey)}

        <div class="kb-menu kb-menu--card" aria-label="Kartenmenü">
          <button type="button" class="btn-icon kb-menu-toggle" data-act="custom-menu-toggle" title="Menü" aria-haspopup="menu" aria-expanded="false">
            <i class="feather icon-more-vertical" aria-hidden="true"></i>
          </button>

          <div class="kb-menu-dropdown" role="menu" aria-label="Menü" hidden>
            <button type="button" class="kb-menu-item" data-menu="verlauf" role="menuitem">
              <i class="feather icon-clock mr-50"></i> Verlauf
            </button>
            <button type="button" class="kb-menu-item" data-menu="ticket" role="menuitem">
              <i class="feather icon-alert-circle mr-50"></i> Ticket
            </button>
            <button type="button" class="kb-menu-item" data-menu="wartung" role="menuitem">
              <i class="feather icon-tool mr-50"></i> Wartung
            </button>
            
            <hr class="my-50">
            <button type="button" class="kb-menu-item text-success" data-run="playing" role="menuitem">
              <i class="feather icon-play mr-50"></i> Start
            </button>
            <button type="button" class="kb-menu-item text-warning" data-run="paused" role="menuitem">
              <i class="feather icon-pause mr-50"></i> Pause
            </button>
            <button type="button" class="kb-menu-item text-danger" data-run="stopped" role="menuitem">
              <i class="feather icon-square mr-50"></i> Stopp
            </button>
          </div>
        </div>

        <div class="card-header card-header--kb">
          <div class="card-title">
            <strong class="card-name">${esc(fullName)}</strong>
            <div class="circle product_circle" aria-hidden="true">${esc(safeStr(item?.initial))}</div>
          </div>
        </div>

        <div class="kb-card-meta">
          <div class="kb-meta-row">
            <span class="kb-meta-item">
            ${getDateAgeIndicator(item?.created_at, stageKey)}
              <i class="feather icon-calendar"></i> 
            </span>
            ${isNonEmpty(branchName) ? `
              <span class="kb-meta-sep" aria-hidden="true">•</span>
              <span class="kb-meta-item kb-branch" title="${esc(branchName)}">
                ${branchSVG(14)}
                <span class="kb-branch-name">${esc(branchName)}</span>
              </span>` : ""
            }
          </div>
          <small class="kb-meta-address">${esc(address)}</small>
        </div>

        <div class="employeeList d-flex align-items-center mt-2">
          ${empHTML}
          ${teamHTML}
        </div>

        ${buildStatusBlock(item)}

        <div class="live-feed-bar card-live-feed" data-feed-root data-feed-count="0" style="display:none;">
          <div class="live-feed-left">
            <div class="live-feed-icon"><i class="feather icon-zap"></i></div>
          </div>
          <div class="live-feed-body">
            <div class="live-feed-line" data-feed-empty>
              <span class="live-feed-title">Keine Aktivitäten</span>
              <span class="live-feed-dot">•</span>
              <span class="live-feed-text">Noch keine Termine oder Aufgaben.</span>
            </div>
            <div class="live-feed-line" data-feed-line>
              <span class="live-feed-title" data-feed-title>Aktivität</span>
              <span class="live-feed-dot">•</span>
              <span class="live-feed-text" data-feed-text>Details…</span>
            </div>
            <div class="live-feed-meta">
              <span class="live-feed-pill" data-feed-pill>Info</span>
              <span class="live-feed-time">
                <i class="feather icon-clock mr-25"></i>
                <span data-feed-time>–</span>
              </span>
              <span class="live-feed-counter" data-feed-counter></span>
            </div>
          </div>
          <div class="live-feed-controls">
            <button type="button" class="live-feed-btn" title="Zurück" data-feed-prev><i class="feather icon-skip-back"></i></button>
            <button type="button" class="live-feed-btn" title="Pause / Abspielen" data-feed-toggle>
              <i class="feather icon-pause" data-feed-icon-pause></i>
              <i class="feather icon-play d-none" data-feed-icon-play></i>
            </button>
            <button type="button" class="live-feed-btn" title="Weiter" data-feed-next><i class="feather icon-skip-forward"></i></button>
            <button type="button" class="live-feed-btn" title="Alle Aktivitäten anzeigen" data-feed-open-modal><i class="feather icon-maximize-2"></i></button>
          </div>
        </div>

        <div class="card-actions" role="group" aria-label="Aktionen">
          <div class="left-actions">
            <button class="btn-icon" data-menu="termin" title="Termin">
              <i class="feather icon-calendar"></i>
              <span class="badge-notes" data-ap-count style="display:none">0</span>
            </button>
            <button class="btn-icon" data-menu="aufgabe" title="Aufgabe">
              <i class="feather icon-check-square"></i>
              <span class="badge-notes" data-pt-count style="display:none">0</span>
            </button>
          </div>

          <div class="right-actions">
            <button type="button" class="btn-icon btn-notes note" data-open-notes data-customer="${esc(item.customer_id)}" data-alt="${esc(item.alternative_id)}" data-product="${esc(item.product_id)}" title="Notizen">
              <i class="feather icon-message-square"></i>
              <span class="badge-notes" data-count="0" style="display:none">0</span>
            </button>

            <a href="/new_lead_profile/${encodeURIComponent(safeStr(item?.customer_id))}" class="btn-icon" title="Profil">
              <i class="feather icon-eye"></i>
            </a>

            ${!hideJunk ? `<button class="btn-icon" data-act="delete" title="In Junk verschieben"><i class="feather icon-trash-2"></i></button>` : ``}
            ${stageKey === "completed" ? `<button class="btn-icon" data-act="archive" title="Archivieren"><i class="feather icon-archive"></i></button>` : ``}
          </div>
        </div>
      `;
  }
  
    function normalizeTeamIds(item) {
      const toId = (x) => {
        const n = Number(
          x?.id ??
          x?.employee_id ??
          x?.emp_id ??
          x
        );
        return Number.isFinite(n) && n > 0 ? n : null;
      };

      // preferred: backend sends ids directly
      const direct =
        item?.team_ids ??
        item?.teamIds ??
        item?.teams_ids ??
        item?.teamsIds ??
        null;

      if (Array.isArray(direct)) return direct.map(toId).filter(Boolean);

      // fallback: arrays of objects
      const arr =
        Array.isArray(item?.team_members) ? item.team_members :
        Array.isArray(item?.teams) ? item.teams :
        [];

      return arr.map(toId).filter(Boolean);
    }

  function mountOrUpdateCard(stageKey, item, existing) {
    let card = existing;
    if (!card) {
      card = document.createElement("div");
      card.className = "card";
      card.id = cardId(item);
      card.draggable = true;
      card.dataset.customerId = item.customer_id ?? "";
      card.dataset.alternativeId = item.alternative_id ?? "";
      card.dataset.productId = item.product_id ?? "";
      card.dataset.leadProductId = item.lead_product_id ?? "";
    }
    card.dataset.employeeId = item.employee?.employee_id ?? 0;
    card.dataset.fieldEmployeeId = item.field_employee?.employee_id ?? 0;
    card.dataset.service = item.service ?? "complete";
    card.dataset.serviceId = item.service_id ?? 0;
    card.dataset.departmentId = item.department_id ?? 0;
    card.dataset.stage = canonicalStage(item.stage);
    card.dataset.latestPhase = item.latest_phase || "";
    card.dataset.latestActivity = item.latest_activity || "";
    card.dataset.doneDate = item.done_date || "";
    card.dataset.updatedAt = item.updated_at || "";
    card.dataset.fullAddress = item.full_address || "";
    card.dataset.street = item.street || "";
    card.dataset.postcode = item.postcode || "";
    card.dataset.city = item.city || "";
    card.dataset.phone = item.phone || "";
    card.dataset.email = item.email || "";
    card.dataset.latitude = item.latitude || "";
    card.dataset.longitude = item.longitude || "";
    card.dataset.teamIds = JSON.stringify(normalizeTeamIds(item));

    card.innerHTML = cardHTML(item, stageKey);
    enforceActionVisibility(card);
    const ws = (item.work_status || "playing").toString().toLowerCase();
    applyRunStateUI(card, ["playing", "paused", "stopped"].includes(ws) ? ws : "playing");
    return card;
  }

  function renderKanbanDiff(leads) {
    ensureColumns();
    const existing = new Map();
    qsa("#kanban .card").forEach((el) => existing.set(el.id, el));
    const stageBuckets = new Map(Object.keys(APP.stageNames).map((k) => [k, []]));
    
    const filtered = (leads || []).filter((it) => !["junk"].includes(canonicalStage(it.stage)));

    for (const it of filtered) {
      const s = canonicalStage(it.stage);
      if (stageBuckets.has(s)) stageBuckets.get(s).push(it);
    }

    for (const [stage, arr] of stageBuckets) {
      const container = colContent(stage);
      if (!container) continue;
      const frag = document.createDocumentFragment();
      for (const item of arr) {
        const id = cardId(item);
        const prev = existing.get(id) || null;
        const card = mountOrUpdateCard(stage, item, prev);
        frag.appendChild(card);
        existing.delete(id);
      }
      container.innerHTML = "";
      container.appendChild(frag);
    }
    for (const [, el] of existing) el.remove();
    updateCounts();
    featherRefreshSoon();
    updateNoteBadgesForVisibleCards();
    LiveFeed.bootstrapFromFirstCard();
  }

  function autoChunk() {
    const low = (navigator.hardwareConcurrency || 4) < 6;
    const narrow = window.matchMedia?.("(max-width: 768px)").matches;
    return low || narrow ? 24 : 60;
  }

  function renderKanbanIncremental(leads, chunkSize = autoChunk(), done = () => {}) {
    ensureColumns();
    clearColumns();
    const list = (leads || []).filter((it) => !["junk"].includes(String(it?.stage || "").toLowerCase()));
    let i = 0;
    (function pump() {
      const frags = new Map();
      const getFrag = (s) => {
        if (!frags.has(s)) frags.set(s, document.createDocumentFragment());
        return frags.get(s);
      };
      for (let c = 0; c < chunkSize && i < list.length; c++, i++) {
        const item = list[i];
        const stage = canonicalStage(item.stage);
        if (APP.stageNames[stage] || APP.stageAlias[stage]) {
          const card = mountOrUpdateCard(stage, item, null);
          getFrag(stage).appendChild(card);
        }
      }
      for (const [stage, frag] of frags) colContent(stage)?.appendChild(frag);
      if (i < list.length) {
        requestIdleCallback(pump);
      } else {
        updateCounts();
        featherRefreshSoon();
        updateNoteBadgesForVisibleCards();
        enforceActionVisibility();
        LiveFeed.bootstrapFromFirstCard();
        done();
      }
    })();
  }

  /* --- Note Logic (Unified for List & Kanban) --- */
  const visibleCardTuples = () => {
    const cards = qsa("#kanban .card");
    const rows = qsa("#kanbanTableBody tr.list-row-item");
    return [...cards, ...rows].map((el) => ({
      el,
      customer_id: el.dataset.customerId,
      alternative_id: el.dataset.alternativeId,
      product_id: el.dataset.productId || null,
    }));
  };

  async function fetchNoteCountOnce(t) {
    const params = new URLSearchParams({ customer_id: t.customer_id, alternative_id: t.alternative_id, per_page: 1 });
    if (t.product_id) params.set("product_id", t.product_id);
    try {
      const p = await safeFetchJSON(`${APP.endpoints.notesIndex}?${params.toString()}`);
      return Number(p?.total || 0);
    } catch { return 0; }
  }

  function updateBadge(el, n) {
    const bd = el.querySelector(".badge-notes");
    if (!bd) return;
    bd.dataset.count = String(n);
    bd.textContent = shortNum(n);
    bd.style.display = n > 0 ? 'block' : 'none'; 
  }

  function updateNoteBadgesForVisibleCards() {
    const tuples = visibleCardTuples();
    tuples.forEach((t) => updateBadge(t.el, 0));
    let i = 0;
    (function next() {
      const batch = tuples.slice(i, (i += 4));
      if (!batch.length) return;
      Promise.all(batch.map(async (t) => updateBadge(t.el, await fetchNoteCountOnce(t)))).finally(() => setTimeout(next, 30));
    })();
  }

 function setNotesTab(tab) {
      const tabs = document.querySelectorAll("[data-notes-tab]");
      const panels = document.querySelectorAll("[data-notes-panel]");
      tabs.forEach((btn) => {
        const isActive = btn.dataset.notesTab === tab;
        btn.classList.toggle("notes-tab--active", isActive);
        btn.setAttribute("aria-selected", isActive ? "true" : "false");
      });
      panels.forEach((panel) => {
        const isActive = panel.dataset.notesPanel === tab;
        panel.classList.toggle("d-none", !isActive);
      });

      // 🔴 FIX: Hide the Quill Editor footer if we are not on the "notes" tab
      const footer = document.querySelector("#notesDrawer .notes-foot");
      if (footer) {
          footer.style.display = (tab === 'notes') ? 'block' : 'none';
      }
  }

  async function loadNotesReport() {
      const panel = document.getElementById("notesReport");
      if (!panel) return;
      const cId = document.getElementById("notesCustomerId")?.value || "";
      const aId = document.getElementById("notesAlternativeId")?.value || "";
      const pId = document.getElementById("notesProductId")?.value || "";
      
      if (!cId || !aId) {
          panel.innerHTML = `<div class="text-muted small p-2">Kein Kontext (Kunde/Alternative) vorhanden.</div>`;
          return;
      }
      
      // Only show loading if empty (prevents flashing during background load)
      if(panel.innerHTML.trim() === "") {
          panel.innerHTML = `<div class="text-muted small p-2">Report wird geladen…</div>`;
      }
      
      try {
          const params = new URLSearchParams({ customer_id: cId, alternative_id: aId });
          if (pId) params.set("product_id", pId);
          const res = await fetch(`${APP.endpoints.reportsIndex}?${params.toString()}`, { method: "GET", credentials: "same-origin", headers: { Accept: "text/html,application/json", "X-Requested-With": "XMLHttpRequest" } });
          const text = await res.text();
          if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
          let html = text;
          const ct = res.headers.get("content-type") || "";
          if (ct.includes("application/json")) {
              try {
                  const json = JSON.parse(text);
                  html = typeof json.html === "string" ? json.html : `<pre class="small p-2 bg-light border rounded mb-0" style="max-height: 320px; overflow:auto;">${JSON.stringify(json, null, 2)}</pre>`;
              } catch { html = text; }
          }
          panel.innerHTML = html;

          // 🔴 FIX: Count the reports and update the badge
          const count = panel.querySelectorAll('.ap-report-card').length;
          const badge = document.getElementById('tabBadgeTerminReport');
          if (badge) {
              badge.textContent = count;
              badge.classList.remove('d-none');
          }

      } catch (e) {
          panel.innerHTML = `<div class="text-danger small p-2">Report konnte nicht geladen werden.<br>${(e && e.message) ? e.message : ''}</div>`;
      }
  }

async function loadCustomerReport() {
      const panel = document.getElementById("customerReportList");
      if (!panel) return;
      const cId = document.getElementById("notesCustomerId")?.value || "";
      const aId = document.getElementById("notesAlternativeId")?.value || "";
      const pId = document.getElementById("notesProductId")?.value || "";
      
      if (!cId || !aId) {
          panel.innerHTML = `<div class="text-muted small p-2">Kein Kontext (Kunde/Alternative) vorhanden.</div>`;
          return;
      }
      
      // Only show loading if empty (prevents flashing during background load)
      if(panel.innerHTML.trim() === "") {
          panel.innerHTML = `<div class="text-muted small p-2">Kundenreport wird geladen…</div>`;
      }

      try {
          const params = new URLSearchParams({ customer_id: cId, alternative_id: aId });
          if (pId) params.set("product_id", pId);
          const res = await safeFetchJSON(`${APP.endpoints.customerReportsIndex}?${params.toString()}`, { method: "GET" });
          if (!res || typeof res.html !== "string") throw new Error(res?.message || "Unerwartete Serverantwort.");
          panel.innerHTML = res.html;

          // 🔴 FIX: Count the reports and update the badge
          const count = panel.querySelectorAll('.cr-card, .ap-report-card').length;
          const badge = document.getElementById('tabBadgeCustomerReport');
          if (badge) {
              badge.textContent = count;
              badge.classList.remove('d-none');
          }

      } catch (e) {
          panel.innerHTML = `<div class="text-danger small p-2">Kundenreport konnte nicht geladen werden.<br>${(e && e.message) ? e.message : ''}</div>`;
      }
  }
  
  // NOTE HANDLERS
  function noteHTML(n) {
    const me = String(n.created_by ?? "") === String(APP.authUserId);
    const img = n?.author?.image ? `${APP.EMP_SRC}/${n.author.image}` : `${APP.EMP_SRC}/noimage.png`;
    const who = n.author ? `${n.author.lastname ?? ""} ${n.author.name ?? ""}`.trim() : "Unbekannt";
    const when = n.created_at ? new Date(n.created_at).toLocaleString("de-DE") : "";
    const bubble = `<div class="note-bubble ${me ? "me" : "other"}"><div class="note-bubble-body" data-note-body>${n.description || ""}</div><div class="note-meta"><span class="note-meta-author">${who}</span><span class="note-meta-sep">•</span><span class="note-meta-time">${when}</span></div>${me ? `<div class="note-actions"><button type="button" class="note-action note-action-edit" data-note-edit data-note-id="${n.id}"><i class="feather icon-edit-2"></i></button><button type="button" class="note-action note-action-delete" data-note-delete data-note-id="${n.id}"><i class="feather icon-trash-2"></i></button></div>` : ""}</div>`;
    return `<div class="note-row ${me ? "me" : "other"}" data-note-id="${n.id}">${me ? bubble + `<img class="note-avatar" src="${img}" alt="">` : `<img class="note-avatar" src="${img}" alt="">` + bubble}</div>`;
  }
  
  function adjustNotesCounters(delta) {
      const badge = document.getElementById("notesCountBadge");
      if (badge) {
        const next = Math.max(0, Number(badge.dataset.count || 0) + delta);
        badge.dataset.count = String(next);
        badge.textContent = shortNum(next);
      }
      const cId = document.getElementById("notesCustomerId")?.value;
      const aId = document.getElementById("notesAlternativeId")?.value;
      const pId = document.getElementById("notesProductId")?.value;

      if (!cId || !aId) return;

      const selector = `
          .card[data-customer-id="${CSS.escape(cId)}"][data-alternative-id="${CSS.escape(aId)}"][data-product-id="${CSS.escape(pId)}"] .badge-notes,
          tr[data-customer-id="${CSS.escape(cId)}"][data-alternative-id="${CSS.escape(aId)}"][data-product-id="${CSS.escape(pId)}"] .badge-notes
      `;

      document.querySelectorAll(selector).forEach((b) => {
        const next = Math.max(0, Number(b.dataset.count || 0) + delta);
        b.dataset.count = String(next);
        b.textContent = shortNum(next);
        b.style.display = next > 0 ? 'block' : 'none';
      });
  }

async function openNotesDrawerFor(cId, aId, pId, title, lId) { // <--- Added lId parameter
    const drawer = qs("#notesDrawer"), list = qs("#notesList"), titleEl = qs("#notesTitle");
    const fC = qs("#notesCustomerId"), fA = qs("#notesAlternativeId"), fP = qs("#notesProductId");
    const fL = qs("#notesLeadProductListId"); // <--- Select the new hidden input

    titleEl.textContent = title || "Kunden-Notizen";
    drawer.classList.add("open");
    qs("#notesBackdrop").classList.add("show");
    document.body.style.overflow = "hidden";

    ensureNoteQuill();
    setNoteEditorHTML("");
    setNotesTab("notes");

    fC.value = cId; 
    fA.value = aId; 
    fP.value = pId || "";
    if (fL) fL.value = lId || ""; // <--- Set the new hidden input value

    // Clear old report panels so they don't show wrong data briefly
    const rPanel = document.getElementById("notesReport");
    const cPanel = document.getElementById("customerReportList");
    if(rPanel) rPanel.innerHTML = "";
    if(cPanel) cPanel.innerHTML = "";

    try {
        const params = new URLSearchParams({ customer_id: cId, alternative_id: aId, per_page: 50 });
        if (pId) params.set("product_id", pId);
        
        const payload = await safeFetchJSON(`${APP.endpoints.notesIndex}?${params.toString()}`);
        const items = (Array.isArray(payload?.notes) ? payload.notes : payload || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        list.innerHTML = items.map(noteHTML).join("");
        
        const total = payload?.total ?? items.length;
        
        // Update Header Badge
        const headerBadge = document.getElementById("notesCountBadge");
        if (headerBadge) { headerBadge.dataset.count = String(total); headerBadge.textContent = shortNum(total); }
        
        // Update the specific Tab Badge
        const tabBadge = document.getElementById("tabBadgeNotes");
        if (tabBadge) { tabBadge.textContent = total; }

        list.scrollTop = list.scrollHeight;
    } catch (e) { 
        Swal.fire("Fehler", e.message, "error"); 
    }
    
    loadNotesReport();
    loadCustomerReport();
}
  // NOTE DRAWER CLOSE LOGIC
  const closeNotes = () => {
      qs("#notesDrawer")?.classList.remove("open");
      qs("#notesBackdrop")?.classList.remove("show");
      document.body.style.overflow = "";
  };
  qs("#notesBackdrop")?.addEventListener("click", closeNotes);
  qsa("[data-notes-close]").forEach(b => b.addEventListener("click", closeNotes));
  
  // NOTE SUBMIT LOGIC
qs("#notesForm").onsubmit = async (ev) => {
    ev.preventDefault();
    const text = getNoteEditorHTML();
    if (!text) return;
    
    // Grab all hidden inputs
    const fC = qs("#notesCustomerId");
    const fA = qs("#notesAlternativeId");
    const fP = qs("#notesProductId");
    const fL = qs("#notesLeadProductListId"); // <--- Grab the new hidden input
    
    try {
        const res = await safeFetchJSON(APP.endpoints.notesStore, { 
            method: "POST", 
            headers: { 
                "Content-Type": "application/json", 
                "X-CSRF-TOKEN": CSRF(), 
                "X-Requested-With": "XMLHttpRequest" 
            }, 
            body: JSON.stringify({ 
                customer_id: Number(fC.value), 
                alternative_id: Number(fA.value), 
                product_id: fP.value ? Number(fP.value) : null, 
                lead_product_list_id: fL && fL.value ? Number(fL.value) : null, // <--- Add to payload
                description: text 
            }) 
        });
        
        qs("#notesList").insertAdjacentHTML("beforeend", noteHTML(res.note || res));
        qs("#notesList").scrollTop = qs("#notesList").scrollHeight;
        setNoteEditorHTML("");
        adjustNotesCounters(+1);
    } catch (e) { 
        Swal.fire("Fehler", e.message, "error"); 
    }
};
  document.addEventListener("submit", async (e) => {
    const form = e.target.closest(".ap-report-create-form");
    if (!form) return;
    e.preventDefault();
    const title = (form.querySelector('input[name="title"]')?.value || "").trim();
    const content = (form.querySelector('textarea[name="content"]')?.value || "").trim();
    if (!title || !content) { Swal.fire("Hinweis", "Titel und Text sind Pflichtfelder.", "info"); return; }
    const appointmentId = form.dataset.appointmentId || null;
    try {
      const payload = { title, content, stage: (form.querySelector('select[name="stage"]')?.value || "").trim(), report: `${title}\n\n${content}`, report_date: form.querySelector('input[name="report_date"]')?.value || null };
      const res = await safeFetchJSON(APP.endpoints.reportsStore(appointmentId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify(payload) });
      if (!res || res.status !== "ok") throw new Error(res?.message || "Fehler.");
      const group = form.closest(".ap-appointment-group");
      group?.querySelector(".ap-report-list")?.insertAdjacentHTML("afterbegin", res.html);
      form.reset();
      group.querySelector(".ap-report-create-wrapper").style.display = "none";
      Swal.fire("Gespeichert", "Report wurde hinzugefügt.", "success");
    } catch (err) { Swal.fire("Fehler", err.message, "error"); }
  });

  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".ap-report-like, .ap-report-dislike");
    if (!btn) return;
    const card = btn.closest(".ap-report-card");
    const reportId = card.getAttribute("data-report-id");
    if (!reportId) return;
    let reaction = btn.classList.contains("ap-report-like") ? "like" : "dislike";
    if (btn.classList.contains("is-active")) reaction = "none";
    try {
      const res = await safeFetchJSON(APP.endpoints.reportsReact(reportId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify({ reaction }) });
      card.querySelector(".ap-report-like-count").textContent = res.likes ?? 0;
      card.querySelector(".ap-report-dislike-count").textContent = res.dislikes ?? 0;
      card.querySelectorAll(".ap-report-like, .ap-report-dislike").forEach((b) => b.classList.remove("is-active"));
      if (res.my_reaction === "like") card.querySelector(".ap-report-like")?.classList.add("is-active");
      else if (res.my_reaction === "dislike") card.querySelector(".ap-report-dislike")?.classList.add("is-active");
    } catch (err) { Swal.fire("Fehler", err.message, "error"); }
  });

  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".ap-open-report-form");
    if (!btn) return;
    const wrapper = btn.closest(".ap-appointment-group").querySelector(".ap-report-create-wrapper");
    const isVisible = wrapper.style.display !== "none";
    wrapper.style.display = isVisible ? "none" : "block";
    if (!btn.dataset.originalLabel) btn.dataset.originalLabel = btn.innerHTML;
    btn.innerHTML = !isVisible ? `<i class="feather icon-file-text"></i> Report schließen` : btn.dataset.originalLabel;
  });

  document.addEventListener("click", (e) => {
    const toggleBtn = e.target.closest("[data-report-toggle-comments]");
    if (!toggleBtn) return;
    const section = toggleBtn.closest(".ap-report-card").querySelector(".ap-report-comments");
    if (section.hasAttribute("hidden")) section.removeAttribute("hidden"); else section.setAttribute("hidden", "");
  });

  document.addEventListener("click", async (e) => {
    const submitBtn = e.target.closest(".ap-report-comment-submit");
    if (!submitBtn) return;
    const card = submitBtn.closest(".ap-report-card");
    const reportId = card.getAttribute("data-report-id");
    const textarea = card.querySelector(".ap-report-comment-text");
    const text = textarea.value.trim();
    if (!text) return;
    try {
      const res = await safeFetchJSON(APP.endpoints.reportsComment(reportId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify({ comment: text }) });
      if (res && typeof res.html === "string") {
        card.querySelector(".ap-report-comments-list").insertAdjacentHTML("beforeend", res.html);
        const toggleBtn = card.querySelector("[data-report-toggle-comments]");
        const current = parseInt(toggleBtn.textContent.match(/(\d+)/)?.[1] || 0, 10);
        toggleBtn.innerHTML = `<i class="feather icon-message-circle mr-25"></i> Kommentare (${current + 1})`;
      }
      textarea.value = "";
    } catch (err) { Swal.fire("Fehler", err.message, "error"); }
  });

  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-notes-tab]");
    if (!btn) return;
    const tab = btn.dataset.notesTab;
    setNotesTab(tab);
    if (tab === "report") loadNotesReport();
    else if (tab === "customerReport") loadCustomerReport();
  });

  /* --------------------- Custom card menu (kb-menu) ------------------------ */
  (function () {
    const closeAllMenus = () => {
      document.querySelectorAll(".kb-menu-dropdown").forEach((d) => d.setAttribute("hidden", ""));
      document.querySelectorAll('[data-act="custom-menu-toggle"][aria-expanded="true"]').forEach((btn) => btn.setAttribute("aria-expanded", "false"));
    };
    document.addEventListener("click", (e) => {
      const toggleBtn = e.target.closest('[data-act="custom-menu-toggle"]');
      if (toggleBtn) {
        const dd = toggleBtn.parentElement.querySelector(".kb-menu-dropdown");
        const isOpen = dd && !dd.hasAttribute("hidden");
        closeAllMenus();
        if (dd && !isOpen) { dd.removeAttribute("hidden"); toggleBtn.setAttribute("aria-expanded", "true"); }
        e.stopImmediatePropagation();
        return;
      }
      const item = e.target.closest(".kb-menu-item");
      if (item) {
       const card = item.closest(".card");
        const type = item.dataset.menu;
        const runState = item.dataset.run;

        // IMPORTANT: If it's a Play/Pause/Stop button, DO NOT call stopPropagation.
        // Let it bubble up to the global "Run" handler.
        if (runState) {
            closeAllMenus();
            return; // Let the global handler take over
        }

        closeAllMenus();

        if (type === "verlauf" && card) {
            const a = document.createElement("a");
            a.href = `/lead/process/history/${encodeURIComponent(card.dataset.customerId)}/${encodeURIComponent(card.dataset.alternativeId)}/${encodeURIComponent(card.dataset.productId)}`;
            a.setAttribute("data-lh-history", "");
            a.style.display = "none";
            document.body.appendChild(a);
            a.click();
            a.remove();
        }
        if (type === "verlauf" && card) {
          const a = document.createElement("a");
          a.href = `/lead/process/history/${encodeURIComponent(card.dataset.customerId)}/${encodeURIComponent(card.dataset.alternativeId)}/${encodeURIComponent(card.dataset.productId)}`;
          a.setAttribute("data-lh-history", "");
          a.style.display = "none";
          document.body.appendChild(a);
          a.click();
          a.remove();
        }
        if (type === "ticket" && card) { /* Ticket Logic */ }
        if (type === "termin" && card) {
           const name = card.querySelector(".card-header strong")?.textContent?.trim() || "Kunde";
           card.dispatchEvent(new CustomEvent("open-appointments", { bubbles: true, detail: { customerId: card.dataset.customerId, alternativeId: card.dataset.alternativeId, productId: card.dataset.productId, title: `Termine • ${name}`, full_address: card.dataset.fullAddress || "" } }));
        }
        if (type === "aufgabe" && card) {
           const name = card.querySelector(".card-header strong")?.textContent?.trim() || "Kunde";
           card.dispatchEvent(new CustomEvent("open-personal-tasks", { bubbles: true, detail: { customerId: card.dataset.customerId, alternativeId: card.dataset.alternativeId, productId: card.dataset.productId, title: `Aufgaben • ${name}` } }));
        }
        e.stopImmediatePropagation();
      }
    });
    document.addEventListener("click", (e) => { if (!e.target.closest(".kb-menu")) closeAllMenus(); });
  })();

  /* --------------------------- Junk tab ------------------------- */
    async function fetchJunkTab(qsStr) {
      const pane = document.querySelector("#junk");
      if (!pane) return;

      try {
        const res = await fetch(`${APP.endpoints.junk}${qsStr ? `?${qsStr}` : ""}`, {
          headers: { Accept: "text/html", "X-Requested-With": "XMLHttpRequest" },
          credentials: "same-origin",
        });

        const html = await res.text();

        // Replace the whole tab content (safe and avoids nested #junkInner)
        pane.innerHTML = html;
      } catch (e) {}
    }


    document.addEventListener("click", async (e) => {
      const btn = e.target.closest(".btn-restore");
      if (!btn) return;

      const row = btn.closest("tr");
      if (!row) return;

      const target = row.querySelector(".restore-select")?.value;
      if (!target) {
        Swal.fire("Hinweis", "Bitte Zielphase wählen.", "info");
        return;
      }

      const customerId = row.dataset.customerId || "";
      const alternativeId = row.dataset.alternativeId || row.dataset.altId || "";
      const productId = row.dataset.productId || "";

      if (!customerId || !alternativeId || !productId) {
        Swal.fire("Fehler", "Fehlende IDs in der Zeile (customer/alternative/product).", "error");
        return;
      }

      const { value: reason, isConfirmed } = await Swal.fire({
        title: "Grund",
        input: "textarea",
        showCancelButton: true,
        confirmButtonText: "Wiederherstellen",
      });
      if (!isConfirmed) return;

      try {
        const url = `${APP.endpoints.changeStage}/${encodeURIComponent(customerId)}/${encodeURIComponent(alternativeId)}/${encodeURIComponent(productId)}`;

        const res = await postJSON(url, {
          lead_product_id: Number(btn.dataset.id),
          stage: target,
          description: reason || "",
          source: "junk",
        });

        if (!res?.success) throw new Error(res?.message || "Fehler");
        row.remove();
        Swal.fire("OK", "Wiederhergestellt.", "success");

        // keep UI in sync
        window.LeadUI?.silentRefreshBoth?.();
      } catch (err) {
        Swal.fire("Fehler", err?.message || "Serverfehler", "error");
      }
    });

  /* ====================== Live Feed Modal ================== */
/* ====================== Live Feed Modal (Robust) ================== */
  const LiveFeedModal = (() => {
    const modalId = "liveFeedModal";
    const backdropId = "liveFeedModalBackdrop";
    
    // Cache DOM elements
    const getEl = (id) => document.getElementById(id);
    const listEl = () => getEl("liveFeedModalList");
    const countEl = () => getEl("liveFeedModalCount");
    
    let allItems = [];
    let typeFilter = "all";

    function render() {
      const list = listEl();
      const count = countEl();
      if (!list) return;

      const items = typeFilter === "all" ? allItems : allItems.filter(i => i.type === typeFilter);
      
      if (count) count.textContent = `${items.length} von ${allItems.length} Einträgen`;
      
      list.innerHTML = items.length ? items.map(i => `
        <div class="lfm-item">
          <div class="lfm-item-type ${i.type === 'task' ? 'task' : i.type === 'appointment' ? 'appointment' : 'ticket'}">
            ${i.type_label || i.type}
          </div>
          <div class="lfm-item-main">
            <div class="lfm-item-title">${i.title}</div>
            <div class="lfm-item-sub">${i.text}</div>
          </div>
          <div class="lfm-item-time">
            <span>${i.when_human}</span>
          </div>
        </div>`).join("") : `<div class="lfm-empty">Keine Aktivitäten gefunden.</div>`;
    }

    function open(items) {
      console.log("Opening Modal with items:", items); // Debug
      allItems = Array.isArray(items) ? items : [];
      typeFilter = "all";
      
      const modal = getEl(modalId);
      const backdrop = getEl(backdropId);
      
      if(modal && backdrop) {
          render();
          modal.style.display = "flex"; // Force flex
          backdrop.style.display = "block";
          document.body.style.overflow = "hidden";
      } else {
          console.error("LiveFeedModal elements not found in DOM.");
      }
    }

    function close() {
      const modal = getEl(modalId);
      const backdrop = getEl(backdropId);
      if (modal) modal.style.display = "none";
      if (backdrop) backdrop.style.display = "none";
      document.body.style.overflow = "";
    }

    // Attach global listeners once
    document.addEventListener("DOMContentLoaded", () => {
        getEl(backdropId)?.addEventListener("click", close);
        getEl("liveFeedModalClose")?.addEventListener("click", close);
        
        getEl("liveFeedTypeFilters")?.addEventListener("click", (e) => {
            const btn = e.target.closest(".lfm-filter-btn");
            if (btn) {
                typeFilter = btn.dataset.type;
                document.querySelectorAll(".lfm-filter-btn").forEach(b => b.classList.toggle("is-active", b === btn));
                render();
            }
        });
    });

    return {
      open,
      close,
      openForCard: (wrapper) => {
         // This wrapper is the .card or .list-row-item
         if(!wrapper) return;
         // Use the shared LiveFeed module to get data
         const items = window.LeadUI.liveFeed.getItemsForCard(wrapper);
         
         if (items && items.length > 0) {
             open(items);
         } else {
             // If data isn't loaded yet, try loading it then opening
             // This uses the wrapper's dataset
             if(window.LeadUI.liveFeed.loadForCard) {
                 window.LeadUI.liveFeed.loadForCard(wrapper).then(() => {
                     // Retry getting items after fetch
                     const freshItems = window.LeadUI.liveFeed.getItemsForCard(wrapper);
                     open(freshItems);
                 });
             }
         }
      }
    };
  })();

  /* ====================== Per-card Live Feed ================== */
  const LiveFeed = (() => {
    const registry = new WeakMap();
    function createInstance(root) {
      let items = [], index = 0, timer = null;
      const textEl = root.querySelector("[data-feed-text]"); 
      const render = () => {
        if (!items.length) { root.style.display = "none"; return; }
        root.style.display = "";
        const item = items[index];
        if(textEl) textEl.textContent = item.text || "";
        root.querySelector("[data-feed-title]").textContent = item.title || "Aktivität";
        root.querySelector("[data-feed-time]").textContent = item.when_human || "";
      };
      const go = (step) => { index = (index + step + items.length) % items.length; render(); };
      return { 
        setItems: (next) => { items = next; index = 0; render(); }, 
        loadForTuple: async (c, a, p, l) => {
            try {
                const res = await safeFetchJSON(`${APP.endpoints.liveFeed}?customer_id=${c}`);
                items = res.items || [];
                render();
            } catch(e) { console.error(e); }
        },
        getItems: () => items 
      };
    }
    function getInstance(root) {
        if (!root) return null;
        if (!registry.has(root)) registry.set(root, createInstance(root));
        return registry.get(root);
    }
    return {
        loadForCard: (card) => getInstance(card.querySelector("[data-feed-root]"))?.loadForTuple(card.dataset.customerId),
        getItemsForCard: (card) => getInstance(card.querySelector("[data-feed-root]"))?.getItems() || [],
        bootstrapFromFirstCard: () => { const c = qs("#kanban .card"); if(c) getInstance(c.querySelector("[data-feed-root]"))?.loadForTuple(c.dataset.customerId); },
        bootstrapAllCards: () => { qsa("#kanban .card").forEach(c => getInstance(c.querySelector("[data-feed-root]"))?.loadForTuple(c.dataset.customerId)); }
    };
  })();

  /* ------------------------------- Expose Core ----------------------------- */
  window.LeadUI = {
    APP, State,
    utils: { qs, qsa, CSRF, fmtDE, getDateAgeIndicator, featherRefreshSoon, shortNum, canonicalStage, stageFilterExcludes, saveToLocal, restoreFromLocal, syncURL, initFromURL, closeOverlays, enforceActionVisibility, isBackward, stageRank },
    net: { safeFetchJSON, postJSON, cancel },
    filters: { initSelect2, getFilterValues, updateFilterBadges, buildFilterQS, Drawer },
    kanban: { ensureColumns, clearColumns, colContent, updateCounts, statusBadge, buildStatusBlock, applyRunStateUI, cardId, cardHTML, mountOrUpdateCard, renderKanbanDiff, renderKanbanIncremental, autoChunk },
    notes: { openNotesDrawerFor, updateNoteBadgesForVisibleCards },
    partials: { fetchJunkTab, fetchTicketsTab: async () => {} },
    liveFeed: LiveFeed,
    liveFeedModal: LiveFeedModal,
  };
})();
</script>
 
<script>
  (function () {
    "use strict";

    // 1. Safe Access to Core Modules
    const LeadUI = window.LeadUI || {};
    const { APP, utils, net, notes, kanban, liveFeed, liveFeedModal } = LeadUI;

    // ---------------------------------------------------------
    // 2. Global Helpers for this Closure
    // ---------------------------------------------------------
    const safeStr = (v) => (v == null ? "" : String(v));
    const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));
    
    const fmtDE = (v) => {
      try {
        return v ? new Date(v).toLocaleDateString("de-DE") : "-";
      } catch {
        return "-";
      }
    };

    // ---------------------------------------------------------
    // 3. HTML Generator: Live Feed Container
    // ---------------------------------------------------------
    function listFeedHTML() {
      return `
        <div class="live-feed-bar list-live-feed card-live-feed"
            data-feed-root
            data-feed-count="0"
            style="display:none; margin-top:0.6rem; width: 100%; max-width: 100%;">
          <div class="live-feed-left">
            <div class="live-feed-icon"><i class="feather icon-zap"></i></div>
          </div>
          <div class="live-feed-body">
            <div class="live-feed-line" data-feed-empty>
              <span class="live-feed-title">Keine Aktivitäten</span>
              <span class="live-feed-dot">•</span>
              <span class="live-feed-text">Noch keine Einträge.</span>
            </div>
            <div class="live-feed-line" data-feed-line>
              <span class="live-feed-title" data-feed-title>Aktivität</span>
              <span class="live-feed-dot">•</span>
              <span class="live-feed-text" data-feed-text>Details…</span>
            </div>
            <div class="live-feed-meta">
              <span class="live-feed-pill" data-feed-pill>Info</span>
              <span class="live-feed-time">
                <i class="feather icon-clock mr-25"></i>
                <span data-feed-time>–</span>
              </span>
              <span class="live-feed-counter" data-feed-counter></span>
            </div>
          </div>
          <div class="live-feed-controls">
            <button type="button" class="live-feed-btn" title="Zurück" data-feed-prev>
              <i class="feather icon-skip-back"></i>
            </button>
            <button type="button" class="live-feed-btn" title="Pause / Abspielen" data-feed-toggle>
              <i class="feather icon-pause" data-feed-icon-pause></i>
              <i class="feather icon-play d-none" data-feed-icon-play></i>
            </button>
            <button type="button" class="live-feed-btn" title="Weiter" data-feed-next>
              <i class="feather icon-skip-forward"></i>
            </button>
            <button type="button" class="live-feed-btn" title="Vergrößern" data-feed-open-modal>
              <i class="feather icon-maximize-2"></i>
            </button>
          </div>
        </div>
      `;
    }

    // ---------------------------------------------------------
    // 4. HTML Generator: Avatar List Item
    // ---------------------------------------------------------
    function avatarLiFromEmp(emp, { withData = false, assignedBy = "", assignedAt = "", stageLabel = "" } = {}) {
      if (!emp) return "";
      const EMP_SRC = APP.EMP_SRC || '/images/employee';
      
      const id = Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0;
      const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;
      const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || `#${id}`;

      return `
        <li class="avatar pull-up"
            ${withData ? `data-emp-id="${esc(id)}"` : ""}
            ${withData ? `data-assigned-by="${esc(assignedBy)}"` : ""}
            ${withData ? `data-assigned-at="${esc(assignedAt)}"` : ""}
            ${withData ? `data-stage-label="${esc(stageLabel)}"` : ""}
            title="${esc(name)}"
            style="margin-left:-8px;">
          <img class="media-object rounded-circle"
              src="${esc(img)}"
              width="26" height="26"
              alt="${esc(name)}"
              style="border:2px solid #fff; object-fit:cover;">
        </li>
      `;
    }

    // ---------------------------------------------------------
    // 5. HTML Generator: Employee & Team Column
    // ---------------------------------------------------------
    function listEmpAndTeamHTML(lead) {
      const stageKey = utils.canonicalStage(lead?.stage);
      const stageLabel = APP.stageNames?.[stageKey] || stageKey;

      const main = [];
      if (lead?.employee && (lead.employee.employee_id || lead.employee.id)) main.push(lead.employee);
      if (lead?.field_employee && (lead.field_employee.employee_id || lead.field_employee.id)) main.push(lead.field_employee);

      const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
      let teamMembers = [];
      if (teamAssignments.length > 0) {
          teamMembers = teamAssignments;
      } else if (Array.isArray(lead?.team_members)) {
          teamMembers = lead.team_members.map(m => ({ member: m }));
      } else if (Array.isArray(lead?.teams)) {
          teamMembers = lead.teams.map(m => ({ member: m }));
      }

      if (!main.length && !teamMembers.length) return `<span class="text-muted small">&ndash;</span>`;

      const mainHtml = main.length
        ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center">
            ${main.map((e) => avatarLiFromEmp(e, { withData: false })).join("")}
          </ul>`
        : "";

      const teamHtml = teamMembers.length
        ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center"
              data-team-hover
              style="margin-left:10px; padding-left:10px; border-left:1px solid #e0e0e0;">
            ${teamMembers.map((a) => {
              const member = a?.member || a;
              const u = a?.assigned_by_user;
              let ab = "";
              if (u && (u.name || u.lastname)) ab = `${safeStr(u.lastname)} ${safeStr(u.name)}`.trim();
              else if (a?.assigned_by) ab = `Mitarbeiter #${a.assigned_by}`;
              const at = safeStr(a?.assigned_at || "").trim();
              return avatarLiFromEmp(member, { withData: true, assignedBy: ab, assignedAt: at, stageLabel });
            }).join("")}
          </ul>`
        : "";

      return `<div class="d-flex align-items-center">${mainHtml}${teamHtml}</div>`;
    }

    // ---------------------------------------------------------
    // 6. MAIN FUNCTION: Build Table Row
    // ---------------------------------------------------------
    function buildRowHTML(lead) {
          // 1. Define helper 'esc' immediately to avoid errors
          const safeStr = (v) => (v == null ? "" : String(v));
          const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
              "&": "&amp;",
              "<": "&lt;",
              ">": "&gt;",
              '"': "&quot;",
              "'": "&#039;"
          })[m]);

          // Helper for Date formatting
          const fmtDE = (v) => {
              try {
                  return v ? new Date(v).toLocaleDateString("de-DE") : "-";
              } catch {
                  return "-";
              }
          };

          // 2. DEFINE DISPLAY NAME HERE (This fixes the "not defined" error!)
          const cName = safeStr(lead?.customer_name).trim();
          const cLastname = safeStr(lead?.customer_lastname).trim();
          const cFirma = safeStr(lead?.firma).trim();
          const displayName = `${cLastname} ${cName}`.trim() || cFirma || "Unbekannt";

          const stageKey = (window.LeadUI && window.LeadUI.utils) ? window.LeadUI.utils.canonicalStage(lead?.stage) : (lead?.stage || "lead");
          const cId = lead?.customer_id ?? "";
          const aId = lead?.alternative_id ?? "";
          const pId = lead?.product_id ?? "";
          const lpId = lead?.lead_product_id ?? "";
          const ws = String(lead?.work_status || "playing").toLowerCase();

          // 3. Get Status Block from Kanban (Core)
          const statusBlockHTML = (window.LeadUI && window.LeadUI.kanban) ? window.LeadUI.kanban.buildStatusBlock(lead) : `<span class="badge badge-secondary">${stageKey}</span>`;

          // 4. Get Live Feed HTML
          const liveFeedRow = typeof listFeedHTML === 'function' ? listFeedHTML() : '';

          // 5. Meta Logic (Assigned By...)
          const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
          let teamsRaw = lead?.teams;
          if (typeof teamsRaw === "string") {
              try { teamsRaw = JSON.parse(teamsRaw); } catch { teamsRaw = []; }
          }
          if (!Array.isArray(teamsRaw)) teamsRaw = [];

          const assignments = teamAssignments.length ?
              teamAssignments :
              teamsRaw.map((t) => ({
                  assigned_at: t?.assigned_at ?? null,
                  assigned_at_iso: t?.assigned_at_iso ?? null,
                  assigned_by: t?.assigned_by ?? null,
                  assigned_by_user: t?.assigned_by_user ?? null,
                  stage_label: t?.stage_label ?? null,
              }));

          const parseAssignedAt = (a) => {
              const raw = (a?.assigned_at_iso || a?.assigned_at || "").trim();
              if (!raw) return 0;
              const isoish = raw.includes("T") ? raw : raw.replace(" ", "T");
              const ts = Date.parse(isoish);
              return Number.isFinite(ts) ? ts : 0;
          };

          const latestA = assignments.reduce((best, a) => {
              const ta = parseAssignedAt(a);
              const tb = parseAssignedAt(best);
              return ta > tb ? a : best;
          }, null);

          const assignedBy = (() => {
              const u = latestA?.assigned_by_user;
              if (u && (u.name || u.lastname)) return `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
              const id = Number(latestA?.assigned_by ?? 0);
              return id > 0 ? `Mitarbeiter #${id}` : "";
          })();

          const assignedAtRaw = (latestA?.assigned_at_iso || latestA?.assigned_at || "").trim();
          const STAGE_DE = {
              lead: "Lead",
              offer: "Verkauf",
              deal: "Auftrag",
              project: "Montage",
              completed: "Abschluss",
              archive: "Archiv",
              junk: "Junk"
          };

          const phaseLabel = (() => {
              const lbl = (latestA?.stage_label || "").trim();
              if (lbl) return lbl;
              const key = String(latestA?.stage || "").trim().toLowerCase();
              return STAGE_DE[key] || "";
          })();

          const assignedMetaHTML =
              assignedBy || assignedAtRaw || phaseLabel ?
              `<div class="small text-muted mt-1">
                    ${phaseLabel ? `<span class="mr-2"><i class="feather icon-layers mr-25"></i><span>Phase: <strong>${esc(phaseLabel)}</strong></span></span><span class="mx-1">•</span>` : ``}
                    <i class="feather icon-user mr-25"></i><span>Zugewiesen von: <strong>${esc(assignedBy || "-")}</strong></span>
                    <span class="mx-1">•</span>
                    <i class="feather icon-calendar mr-25"></i><span>${esc(assignedAtRaw ? fmtDE(assignedAtRaw) : "-")}</span>
                  </div>` :
              "";

          return `
              <tr id="row-${esc(lpId)}"
                  class="list-row-item"
                  data-customer-id="${esc(cId)}"
                  data-alternative-id="${esc(aId)}"
                  data-product-id="${esc(pId)}"
                  data-lead-product-id="${esc(lpId)}"
                  data-stage="${esc(stageKey)}"
                  data-run-state="${esc(ws)}">
                
               <<td style="width: 110px;" class="list-date-cell">
                  ${window.LeadUI.utils.getDateAgeIndicator(lead?.created_at, stageKey)}
                  ${lead?.created_at ? fmtDE(lead.created_at) : "-"}
                </td>

                <td style="min-width: 350px;">
                  <a href="/new_lead_profile/${encodeURIComponent(safeStr(cId))}" class="customer-link" style="font-size:1.05rem;">
                    ${esc(displayName)}
                  </a>

                  ${assignedMetaHTML}

                  <div class="list-action-bar">
                    <button type="button" class="btn-list-icon" data-menu="termin" title="Termin">
                      <i class="feather icon-calendar"></i>
                      <span class="badge-notes" data-ap-count style="display:none">0</span>
                    </button>
                    <button type="button" class="btn-list-icon" data-menu="aufgabe" title="Aufgabe">
                      <i class="feather icon-check-square"></i>
                      <span class="badge-notes" data-pt-count style="display:none">0</span>
                    </button>

                    <span style="border-left:1px solid #ddd; height:14px; margin:0 4px;"></span>

                    <button type="button" class="btn-list-icon note" data-open-notes data-customer="${esc(cId)}" data-alt="${esc(aId)}" data-product="${esc(pId)}">
                      <i class="feather icon-message-square"></i>
                      <span class="badge-notes" data-count="0" style="display:none">0</span>
                    </button>

                    <div class="btn-group">
                      <button type="button" class="btn-list-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="feather icon-more-vertical"></i>
                      </button>
                      <div class="dropdown-menu">
                          <button class="dropdown-item text-success" data-run="playing"><i class="feather icon-play mr-50"></i> Start</button>
                          <button class="dropdown-item text-warning" data-run="paused"><i class="feather icon-pause mr-50"></i> Pause</button>
                          <button class="dropdown-item text-danger" data-run="stopped"><i class="feather icon-square mr-50"></i> Stopp</button>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="/lead/process/history/${encodeURIComponent(safeStr(cId))}/${encodeURIComponent(safeStr(aId))}/${encodeURIComponent(safeStr(pId))}" data-lh-history>
                              <i class="feather icon-activity mr-50"></i> Verlauf
                          </a>
                      </div>
                    </div>
                  </div>

                  ${liveFeedRow}
                </td>

                <td>${esc(lead?.city ?? "")}</td>
                <td>${esc(lead?.initial ?? "")}</td>
                <td>${typeof listEmpAndTeamHTML === 'function' ? listEmpAndTeamHTML(lead) : ''}</td>

                <td>
                  ${statusBlockHTML}
                </td>

                <td>
                  <select class="form-control stage-select" data-id="${esc(lpId)}">
                    ${Object.entries((window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.stageNames) || {})
                      .map(([k, l]) => `<option value="${esc(k)}" ${stageKey === k ? "selected" : ""}>${esc(l)}</option>`)
                      .join("")}
                  </select>
                </td>
              </tr>
            `;
    }
    // ---------------------------------------------------------
    // 7. Bootstrapper: Activates the Feed on List Load
    // ---------------------------------------------------------
    function bootstrapListLiveFeed(container) {
        if (!liveFeed || typeof liveFeed.loadForCard !== "function") return;

        const root = container || document;
        // MATCH the class used in buildRowHTML (list-row-item)
        const rows = root.querySelectorAll("tr.list-row-item");

        if (!rows.length) return;

        let i = 0;
        const BATCH = 4;

        (function pump() {
          const slice = Array.prototype.slice.call(rows, i, i + BATCH);
          i += BATCH;
          slice.forEach((row) => {
              if(row.dataset.customerId) {
                  // This call makes the AJAX request and removes 'display:none' if data is found
                  liveFeed.loadForCard(row);
              }
          });
          if (i < rows.length) {
              if ("requestIdleCallback" in window) requestIdleCallback(pump);
              else setTimeout(pump, 0);
          }
        })();
    }
    
    // ---------------------------------------------------------
    // 8. Update & Fetch Logic
    // ---------------------------------------------------------
    function updateListView(leads, meta) {
      const tbody = utils.qs("#kanbanTableBody");
      if (!tbody) return;
      
      if (!Array.isArray(leads) || !leads.length) { 
          tbody.innerHTML = '<tr><td colspan="7" class="text-center p-3 text-muted">Keine Ergebnisse gefunden.</td></tr>'; 
          return; 
      }
      
      // Inject HTML
      tbody.innerHTML = leads.map(buildRowHTML).join("");
      
      // Activate Features
      notes.updateNoteBadgesForVisibleCards(); 
      bootstrapListLiveFeed(tbody);
      if(utils.featherRefreshSoon) utils.featherRefreshSoon();
    }

    function fetchListView(qsStr) {
        net.safeFetchJSON(`${APP.endpoints.listSearch}?${qsStr}`).then(res => {
            const leads = res.leads || res.data || [];
            updateListView(leads);
        });
    }

    // Initial Load
    document.addEventListener("DOMContentLoaded", () => {
      fetchListView(""); 
    });

    /* ---------------------------------------------------------
      9. EVENT LISTENERS
    --------------------------------------------------------- */
    
    // A. Notes Click 
    document.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-open-notes]");
        if (!btn) return;
        e.stopPropagation();

        let name = "Kunde";
        
        // Check if we are in the List view
        const row = btn.closest("tr");
        if (row) {
            const link = row.querySelector(".customer-link");
            if (link) name = link.textContent.trim();
        } else {
            // Check if we are in the Kanban view
            const card = btn.closest(".card");
            if (card) {
                const nameEl = card.querySelector(".card-name");
                if (nameEl) name = nameEl.textContent.trim();
            }
        }

        // Extract the lead_product_list_id from the row or card wrapper
        const wrapper = row || btn.closest(".card");
        const leadProductId = wrapper ? wrapper.dataset.leadProductId : null;

        // Pass the leadProductId as the 5th parameter
        notes.openNotesDrawerFor(
            btn.dataset.customer,
            btn.dataset.alt,
            btn.dataset.product,
            `Notizen • ${name}`,
            leadProductId
        );
    });
    // B. Live Feed Controls (Maximize, Prev, Next)
    document.addEventListener("click", (e) => {
        // Look for any button inside the feed controls
        const btn = e.target.closest(".live-feed-btn");
        if (!btn) return;

        const feedRoot = btn.closest(".live-feed-bar");
        if (!feedRoot) return;
        
        // The wrapper is the table row
        const wrapper = feedRoot.closest("tr.list-row-item") || feedRoot.closest(".card");
        if (!wrapper) return;

        e.preventDefault();
        e.stopPropagation();

        // 1. Maximize Button
        if (btn.hasAttribute("data-feed-open-modal")) {
            if (liveFeedModal && typeof liveFeedModal.openForCard === 'function') {
                liveFeedModal.openForCard(wrapper);
            } else {
                console.error("LeadUI.liveFeedModal is missing or invalid.");
            }
        }
        
        // Note: Next/Prev/Pause are usually handled via internal state in your 'liveFeed' module. 
        // If those buttons aren't working, it's because 'liveFeed.js' likely attaches listeners 
        // locally or not at all for dynamically added list rows. 
        // Ensure 'liveFeed.js' uses delegation or attaches listeners on 'loadForCard'.
    });

  })();


  document.addEventListener("click", (e) => {
      const btn = e.target.closest('[data-menu]');
      if (!btn) return;
      
      const menuType = btn.dataset.menu;
      const card = btn.closest('.card') || btn.closest('tr'); // Works for both Kanban and List
      
      if (menuType === 'termin') {
          // Trigger your existing Appointment open logic
          const event = new CustomEvent("open-appointments", {
              bubbles: true,
              detail: { 
                  customerId: card.dataset.customerId, 
                  alternativeId: card.dataset.alternativeId, 
                  productId: card.dataset.productId 
              }
          });
          btn.dispatchEvent(event);
      }
      
      if (menuType === 'aufgabe') {
          // Trigger your existing Task open logic
          const event = new CustomEvent("open-personal-tasks", {
              bubbles: true,
              detail: { 
                  customerId: card.dataset.customerId, 
                  alternativeId: card.dataset.alternativeId, 
                  productId: card.dataset.productId 
              }
          });
          btn.dispatchEvent(event);
      }
  });
</script>

<script>
  /* =============================================================================
  * LeadUI – Interactions & Boot (Segment 2/2) — REWRITE
  * - Selection + Drag & Drop (Kanban)
  * - Stage-change flow (SweetAlert + Select2 team + optional reason)
  * - List rendering + pagination (+ LiveFeed row under each list row)
  * - Fetchers (Kanban + List)
  * - All event bindings, keyboard shortcuts
  * - Bootstrap on DOMContentLoaded
  * ============================================================================= */
  (() => {
    "use strict";

    /* -------------------------------------------------------------------------- */
    /* Guard                                                                       */
    /* -------------------------------------------------------------------------- */
    if (!window.LeadUI) {
      console.error("LeadUI missing on window.");
      return;
    }

    const { APP, State, utils, net, filters, kanban, notes, partials, liveFeed } =
      window.LeadUI;

    const {
      qs,
      qsa,
      canonicalStage,
      featherRefreshSoon,
      stageFilterExcludes,
      saveToLocal,
      restoreFromLocal,
      syncURL,
      initFromURL,
      closeOverlays,
      enforceActionVisibility,
      isBackward,
      stageRank,
    } = utils;

    const { safeFetchJSON, postJSON, cancel } = net;

    const {
      ensureColumns,
      colContent,
      updateCounts,
      buildStatusBlock,
      applyRunStateUI,
      renderKanbanDiff,
      renderKanbanIncremental,
      autoChunk,
    } = kanban;


    const safeStr = (v) => (v == null ? "" : String(v));
    
    const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;"
    })[m]);

    const fmtDE = (v) => {
      try {
        return v ? new Date(v).toLocaleDateString("de-DE") : "-";
      } catch {
        return "-";
      }
    };
    /* -------------------------------------------------------------------------- */
    /* Constants                                                                   */
    /* -------------------------------------------------------------------------- */
    const DND_MIME = "application/x-leadui-cards";

    const interestIcons = {
      interest: { icon: "kaufinteresse.svg", label: "Kaufinteresse" },
      intent: { icon: "kaufabsicht.svg", label: "Kaufabsicht" },
      option: { icon: "kaufoption.svg", label: "Kaufoption" },
    };

    const servicesMap = {
      complete: "Komplett",
      montage: "Montage",
      product: "Produkt",
      plan: "Planung",
      maintenance: "Wartung",
      repair: "Reparatur",
      emergency: "Notdienst",
      others: "Sonstiges",
    };

    /* -------------------------------------------------------------------------- */
    /* Small helpers                                                               */
    /* -------------------------------------------------------------------------- */

    function parseDT(raw) {
        const s = String(raw || "").trim();
        if (!s) return null;

        // MySQL "YYYY-MM-DD HH:MM:SS" -> ISO-like "YYYY-MM-DDTHH:MM:SS"
        const isoLike = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(s) ? s.replace(" ", "T") : s;

        const d = new Date(isoLike);
        if (!Number.isFinite(d.getTime())) return null;
        return d;
      }

      function fmtDEDate(raw) {
        const d = parseDT(raw);
        return d ? d.toLocaleDateString("de-DE") : "-";
      }

      function fmtDEDateTime(raw) {
        const d = parseDT(raw);
        return d ? d.toLocaleString("de-DE") : "-";
      }

    const toInt = (v, def = 0) => {
      const n = Number(v);
      return Number.isFinite(n) ? n : def;
    };

    const safeJSON = (raw, fallback) => {
      try {
        return JSON.parse(raw);
      } catch (_) {
        return fallback;
      }
    };

    function runIdle(fn) {
      if ("requestIdleCallback" in window) window.requestIdleCallback(fn);
      else window.setTimeout(fn, 0);
    }

    function addPage(qsStr, page) {
      const p = new URLSearchParams(qsStr || "");
      p.set("page", String(page));
      return p.toString();
    }

    function isKanbanActive() {
      return qs("#home")?.classList.contains("active");
    }

    function setTabCount(selector, n) {
      const el = qs(selector);
      if (el) el.textContent = String(toInt(n, 0));
    }

    function normalizePaginationMeta(input) {
      if (!input) return null;
      const direct = input.meta || input.pagination || input;

      const cp = toInt(direct.current_page ?? direct.currentPage ?? direct.page ?? 1, 1);
      const lp = toInt(
        direct.last_page ??
          direct.lastPage ??
          (direct.total && direct.per_page ? Math.ceil(toInt(direct.total, 0) / toInt(direct.per_page, 1)) : 1),
        1
      );

      return { current_page: Math.max(1, cp), last_page: Math.max(1, lp) };
    }

    /* -------------------------------------------------------------------------- */
    /* Selection (Kanban)                                                         */
    /* -------------------------------------------------------------------------- */
    function selectCard(card, ev) {
      if (!card) return;

      const multi = !!(ev?.ctrlKey || ev?.metaKey);

      if (!multi) {
        qsa("#kanban .card.selected").forEach((c) => c.classList.remove("selected"));
        State.selectedIds?.clear?.();
      }

      if (!State.selectedIds) State.selectedIds = new Set();

      if (multi && State.selectedIds.has(card.id)) {
        card.classList.remove("selected");
        State.selectedIds.delete(card.id);
        return;
      }

      card.classList.add("selected");
      State.selectedIds.add(card.id);
    }

    /* -------------------------------------------------------------------------- */
    /* Drag & Drop (Kanban)                                                       */
    /* -------------------------------------------------------------------------- */
    function getDragIds(card) {
      if (!State.selectedIds) State.selectedIds = new Set();
      let ids = Array.from(State.selectedIds);
      if (!ids.length || !State.selectedIds.has(card.id)) ids = [card.id];
      return ids;
    }

    function onKanbanDragStart(ev, card) {
      if (!ev?.dataTransfer || !card) return;
      const ids = getDragIds(card);

      // Use a custom MIME to avoid browser default "open new tab" behavior elsewhere.
      ev.dataTransfer.setData(DND_MIME, JSON.stringify(ids));
      ev.dataTransfer.effectAllowed = "move";
    }

    function refreshCardStatus(card, overrides = {}) {
      const s = canonicalStage(overrides.stage || card.dataset.stage || card.closest(".column")?.id || "lead");
      const ws = String(overrides.work_status || card.dataset.runState || "playing").toLowerCase();
      const stamp = overrides.updated_at || card.dataset.updatedAt || card.dataset.doneDate || new Date().toISOString();

      card.dataset.stage = s;

      if (overrides.latest_phase != null) card.dataset.latestPhase = overrides.latest_phase;
      if (overrides.latest_activity != null) card.dataset.latestActivity = overrides.latest_activity;
      if (overrides.updated_at != null) card.dataset.updatedAt = overrides.updated_at;

      const old = card.querySelector(".kb-status");
      if (old) {
        old.outerHTML = buildStatusBlock({
          stage: s,
          work_status: ws,
          latest_phase: overrides.latest_phase ?? card.dataset.latestPhase ?? "-",
          latest_activity: overrides.latest_activity ?? card.dataset.latestActivity ?? "-",
          updated_at: stamp,
          done_date: stamp,
        });
      }

      applyRunStateUI(card, ["playing", "paused", "stopped"].includes(ws) ? ws : "playing");
      featherRefreshSoon();
    }

   function moveOrRefreshKanbanCard({ newStage, cardFromDOM }) {
      const card = cardFromDOM;
      if (!card) return;

      if (stageFilterExcludes(newStage)) {
        card.remove();
      } else {
        const targetCol = colContent(newStage);
        if (targetCol && card.parentElement !== targetCol) {
            targetCol.appendChild(card);
            // 👇 ADD THIS LINE: Ensure card is visible when dropped into a new column
            card.style.display = ''; 
        }

        refreshCardStatus(card, { stage: newStage, updated_at: new Date().toISOString() });

        card.classList.remove("selected");
        State.selectedIds?.delete?.(card.id);
      }

      updateCounts();
    }
    /* -------------------------------------------------------------------------- */
    /* Stage-change confirm (SweetAlert + Select2 team + reason)                   */
    /* -------------------------------------------------------------------------- */
    async function confirmStageChange(newStage, currentStage, currentTeamIds = [], opts = {}) {
      const labelNew = APP.stageNames?.[newStage] || newStage;

      const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];
      const teamSet = new Set((currentTeamIds || []).map((x) => toInt(x)));

      const removedIds = (opts.removedTeamIds || []).map((x) => toInt(x)).filter(Boolean);

      const removedListHTML = removedIds.length
        ? `<div class="mb-3 p-2" style="border:1px solid #f1c40f;background:#fff8e1;border-radius:8px;">
            <div class="font-weight-bold mb-1">Achtung: Rückwärtswechsel</div>
            <div class="small text-muted mb-2">Folgende Mitarbeiter werden in der vorherigen Phase nicht übernommen:</div>
            <ul class="mb-0" style="padding-left:18px;">
              ${removedIds
                .map((id) => {
                  const emp = employees.find((e) => toInt(e.id) === id);
                  const name = emp ? `${emp.lastname || ""} ${emp.name || ""}`.trim() : `#${id}`;
                  return `<li>${name}</li>`;
                })
                .join("")}
            </ul>
          </div>`
        : "";

      const options = employees
        .map((emp) => {
          const id = toInt(emp.id);
          const selected = teamSet.has(id) ? "selected" : "";
          const imgUrl = emp.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
          const text = `${emp.lastname || ""} ${emp.name || ""}`.trim();
          return `<option value="${id}" data-image="${imgUrl}" ${selected}>${text}</option>`;
        })
        .join("");

      const htmlContent = `
        <div style="text-align:left; overflow:visible;">
          ${removedListHTML}
          <div class="mb-3">
            <label class="small text-muted font-weight-bold text-uppercase">Team zuweisen</label>
            <select id="swal-team-select" class="form-control" multiple style="width:100%;">${options}</select>
          </div>
          <div class="mb-1">
            <label class="small text-muted font-weight-bold text-uppercase">Grund / Notiz</label>
            <textarea id="swal-reason-text" class="form-control" rows="3" placeholder="Optional: Grund für den Wechsel..."></textarea>
          </div>
        </div>
      `;

      const formatEmployee = (state) => {
        if (!state?.id) return state?.text || "";
        const el = state.element;
        const img = el?.dataset?.image;
        if (!img) return state.text;

        const wrap = document.createElement("span");
        wrap.className = "employee-option";
        wrap.innerHTML = `<img src="${img}" style="width:20px;height:20px;border-radius:999px;object-fit:cover;margin-right:8px;">${state.text}`;
        return wrap;
      };

      const result = await Swal.fire({
        title: `Wechsel zu ${labelNew}`,
        html: htmlContent,
        showCancelButton: true,
        confirmButtonText: "Speichern",
        cancelButtonText: "Abbrechen",
        customClass: { popup: "swal-overflow-visible" },
        didOpen: () => {
          if (window.jQuery && window.jQuery.fn.select2) {
            const $sel = window.jQuery("#swal-team-select");
            $sel.select2({
              placeholder: "Mitarbeiter wählen...",
              dropdownParent: window.jQuery(Swal.getPopup()),
              width: "100%",
              templateResult: formatEmployee,
              templateSelection: formatEmployee,
              closeOnSelect: false,
            });
          }
        },
        preConfirm: () => {
          const reason = qs("#swal-reason-text")?.value || "";

          // fallback to currentTeamIds if select2 not available
          let teams = currentTeamIds.slice();

          if (window.jQuery) {
            const v = window.jQuery("#swal-team-select").val();
            if (Array.isArray(v)) {
              teams = v.map((x) => toInt(x)).filter(Boolean);
            }
          }

          return { reason, teams };
        },

      });

      if (!result.isConfirmed) return { ok: false };

      return {
        ok: true,
        reasonHTML: result.value?.reason || "",
        teams: Array.isArray(result.value?.teams) ? result.value.teams : [],
      };
    }

    async function applyStageChange({
      customerId,
      alternativeId,
      productId,
      leadProductId,
      newStage,
      noteHTML,
      teams = [],
    }) {
      const url = `${APP.endpoints.changeStage}/${encodeURIComponent(customerId)}/${encodeURIComponent(
        alternativeId
      )}/${encodeURIComponent(productId)}`;

      const payload = {
        stage: newStage,
        description: noteHTML || "",
        lead_product_id: toInt(leadProductId) || undefined,
        teams: Array.isArray(teams) ? teams.map((x) => toInt(x)).filter(Boolean) : [],
      };

      const data = await postJSON(url, payload);
      if (!data?.success) throw new Error(data?.message || "Fehler");
      return data;
    }

    /* -------------------------------------------------------------------------- */
    /* Kanban Drop                                                                 */
    /* -------------------------------------------------------------------------- */
    async function onKanbanDrop(ev) {
      ev.preventDefault();
      ev.stopPropagation();

      const col = ev.target.closest(".column");
      if (!col) return;

      const raw = ev.dataTransfer?.getData(DND_MIME) || "";
      const ids = Array.isArray(safeJSON(raw, [])) ? safeJSON(raw, []) : [];
      if (!ids.length) return;

      const card = qs(`#${CSS.escape(ids[0])}`);
      if (!card) return;

      const newStage = canonicalStage(col.id);
      const currentStage = canonicalStage(card.dataset.stage);
      if (currentStage === newStage) return;

      // teams from card (if you store it)
      let currentTeamIds = safeJSON(card.dataset.teamIds || "[]", []);
      if (!Array.isArray(currentTeamIds)) currentTeamIds = [];
      currentTeamIds = currentTeamIds.map((x) => toInt(x)).filter(Boolean);

      const backward = isBackward(currentStage, newStage);
      const removedTeamIds = backward ? currentTeamIds.slice() : [];

      const confirm = await confirmStageChange(newStage, currentStage, currentTeamIds, { removedTeamIds });
      if (!confirm.ok) return;

      try {
        const { customerId, alternativeId, productId, leadProductId } = card.dataset;

        await applyStageChange({
          customerId,
          alternativeId,
          productId,
          leadProductId,
          newStage,
          noteHTML: confirm.reasonHTML,
          teams: confirm.teams,
        });

        card.dataset.teamIds = JSON.stringify(confirm.teams || []);
        moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
        enforceActionVisibility(card);

        window.LeadUI?.silentRefreshBoth?.();

        Swal.fire({
          icon: "success",
          title: "OK",
          text: "Status & Team aktualisiert.",
          timer: 1200,
          showConfirmButton: false,
        });
      } catch (err) {
        Swal.fire("Fehler", err?.message || "Serverfehler.", "error");
      }
    }

    /* -------------------------------------------------------------------------- */
    /* List rendering (+ LiveFeed row)                                             */
    /* -------------------------------------------------------------------------- */
      function priorityMeta(raw) {
        const p = String(raw || "normal").toLowerCase();
        if (p === "high" || p === "urgent") return { label: "Hoch", cls: "prio-high", icon: "alert-triangle" };
        if (p === "low") return { label: "Niedrig", cls: "prio-low", icon: "arrow-down-circle" };
        return { label: "Normal", cls: "prio-normal", icon: "circle" };
      }

      function employeeCellHTML(lead) {
        const esc = (s) =>
          String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

        const office = lead?.employee || null;
        const field = lead?.field_employee || lead?.fieldEmployee || null;

        // team can come as: team_members OR teams (array)
        const teamArr = Array.isArray(lead?.team_members)
          ? lead.team_members
          : Array.isArray(lead?.teams)
            ? lead.teams
            : [];

        const hasOffice = !!(office && (office.name || office.lastname));
        const hasField  = !!(field && (field.name || field.lastname));
        const hasTeam   = teamArr.length > 0;

        if (!hasOffice && !hasField && !hasTeam) return "<small>&ndash;</small>";

        const imgOrNo = (img) => (img ? esc(img) : "noimage.png");

        const chunks = [];

        // wrapper to align employees + team similar to blade
        chunks.push(`<div class="d-flex align-items-start flex-wrap" style="gap:10px;">`);

        // employee stack
        if (hasOffice || hasField) {
          const empChunks = [];

          if (hasOffice) {
            empChunks.push(`
              <div class="d-flex align-items-center">
                <img src="/images/employee/${imgOrNo(office.image)}" width="30" height="30" class="rounded-circle mr-1" alt="" style="object-fit:cover;">
                <div>
                  <div style="line-height:1.1"><strong>${esc(office.lastname || "")}</strong> ${esc(office.name || "")}</div>
                  <small class="text-muted">Innendienst</small>
                </div>
              </div>
            `);
          }

          if (hasField) {
            empChunks.push(`
              <div class="d-flex align-items-center">
                <img src="/images/employee/${imgOrNo(field.image)}" width="26" height="26" class="rounded-circle mr-1" alt="" style="object-fit:cover;">
                <div>
                  <div style="line-height:1.1"><strong>${esc(field.lastname || "")}</strong> ${esc(field.name || "")}</div>
                  <small class="text-muted">Außendienst</small>
                </div>
              </div>
            `);
          }

          chunks.push(`<div class="d-flex flex-column" style="gap:6px;">${empChunks.join("")}</div>`);
        }

        // team avatars
        if (hasTeam) {
          const avatars = teamArr
            .map((t) => {
              const name = `${t?.lastname ?? ""} ${t?.name ?? ""}`.trim() || "Team";
              const img = t?.image ? `/images/employee/${esc(t.image)}` : `/images/employee/noimage.png`;
              return `
                <li class="avatar pull-up" title="${esc(name)}" style="margin-left:-8px;">
                  <img class="media-object rounded-circle"
                      src="${img}"
                      width="26" height="26"
                      alt="${esc(name)}"
                      style="border:2px solid #fff; object-fit:cover;">
                </li>`;
            })
            .join("");

          chunks.push(`
            <div class="d-flex align-items-center" style="margin-top:2px; padding-left:10px; border-left:1px solid #e0e0e0;">
              <ul class="list-unstyled users-list m-0 d-flex align-items-center" style="gap:0; padding:0;">
                ${avatars}
              </ul>
            </div>
          `);
        }

        chunks.push(`</div>`);
        return chunks.join("");
      }

      // ---------------------------------------------------------
      // 1. Helper: Live Feed HTML Structure
      // ---------------------------------------------------------
        function listFeedHTML() {
          return `
          <div class="live-feed-bar list-live-feed card-live-feed"
              data-feed-root
              data-feed-count="0"
              style="display:none; margin-top:0.5rem; width: 100%; max-width: 450px;">
            <div class="live-feed-left">
              <div class="live-feed-icon"><i class="feather icon-zap"></i></div>
            </div>
            <div class="live-feed-body">
              <div class="live-feed-line" data-feed-empty>
                <span class="live-feed-title">Keine Aktivitäten</span>
                <span class="live-feed-dot">•</span>
                <span class="live-feed-text">Noch keine Termine oder Aufgaben.</span>
              </div>
              <div class="live-feed-line" data-feed-line>
                <span class="live-feed-title" data-feed-title>Aktivität</span>
                <span class="live-feed-dot">•</span>
                <span class="live-feed-text" data-feed-text>Details…</span>
              </div>
              <div class="live-feed-meta">
                <span class="live-feed-pill" data-feed-pill>Info</span>
                <span class="live-feed-time">
                  <i class="feather icon-clock mr-25"></i>
                  <span data-feed-time>–</span>
                </span>
                <span class="live-feed-counter" data-feed-counter></span>
              </div>
            </div>
            <div class="live-feed-controls">
              <button type="button" class="live-feed-btn" title="Zurück" data-feed-prev>
                <i class="feather icon-skip-back"></i>
              </button>
              <button type="button" class="live-feed-btn" title="Pause / Abspielen" data-feed-toggle>
                <i class="feather icon-pause" data-feed-icon-pause></i>
                <i class="feather icon-play d-none" data-feed-icon-play></i>
              </button>
              <button type="button" class="live-feed-btn" title="Weiter" data-feed-next>
                <i class="feather icon-skip-forward"></i>
              </button>
              <button type="button" class="live-feed-btn" title="Alle Aktivitäten anzeigen" data-feed-open-modal>
                  <i class="feather icon-maximize-2"></i>
              </button>
            </div>
          </div>
        `;
      }

      // ---------------------------------------------------------
      // 2. Helper: Avatar Generator
      // ---------------------------------------------------------
      function avatarLiFromEmp(emp, { withData = false, assignedBy = "", assignedAt = "", stageLabel = "" } = {}) {
        if (!emp) return "";
        
        // Constants from parent scope or fallback
        const EMP_SRC = (window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.EMP_SRC) ? window.LeadUI.APP.EMP_SRC : '/images/employee';
        const safeStr = (v) => (v == null ? "" : String(v));
        const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

        const id = Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0;
        const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;
        const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || `#${id}`;

        return `
          <li class="avatar pull-up"
              ${withData ? `data-emp-id="${esc(id)}"` : ""}
              ${withData ? `data-assigned-by="${esc(assignedBy)}"` : ""}
              ${withData ? `data-assigned-at="${esc(assignedAt)}"` : ""}
              ${withData ? `data-stage-label="${esc(stageLabel)}"` : ""}
              title="${esc(name)}"
              style="margin-left:-8px;">
            <img class="media-object rounded-circle"
                src="${esc(img)}"
                width="26" height="26"
                alt="${esc(name)}"
                style="border:2px solid #fff; object-fit:cover;">
          </li>
        `;
      }

      // ---------------------------------------------------------
      // 3. Helper: Employee & Team Column Generator
      // ---------------------------------------------------------
      function listEmpAndTeamHTML(lead) {
        const stageKey = window.LeadUI.utils.canonicalStage(lead?.stage);
        const stageLabel = window.LeadUI.APP.stageNames?.[stageKey] || stageKey;
        const safeStr = (v) => (v == null ? "" : String(v));

        // A. Main Employees (Office & Field)
        const main = [];
        if (lead?.employee && (lead.employee.employee_id || lead.employee.id)) main.push(lead.employee);
        if (lead?.field_employee && (lead.field_employee.employee_id || lead.field_employee.id)) main.push(lead.field_employee);

        // B. Team Members
        const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
        let teamMembers = [];
        if (teamAssignments.length > 0) {
            teamMembers = teamAssignments;
        } else if (Array.isArray(lead?.team_members)) {
            teamMembers = lead.team_members.map(m => ({ member: m }));
        } else if (Array.isArray(lead?.teams)) {
            teamMembers = lead.teams.map(m => ({ member: m }));
        }

        if (!main.length && !teamMembers.length) return `<span class="text-muted small">&ndash;</span>`;

        const mainHtml = main.length
          ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center">
              ${main.map((e) => avatarLiFromEmp(e, { withData: false })).join("")}
            </ul>`
          : "";

        const teamHtml = teamMembers.length
          ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center"
                data-team-hover
                style="margin-left:10px; padding-left:10px; border-left:1px solid #e0e0e0;">
              ${teamMembers.map((a) => {
                const member = a?.member || a;
                const u = a?.assigned_by_user;
                let ab = "";
                if (u && (u.name || u.lastname)) ab = `${safeStr(u.lastname)} ${safeStr(u.name)}`.trim();
                else if (a?.assigned_by) ab = `Mitarbeiter #${a.assigned_by}`;
                const at = safeStr(a?.assigned_at || "").trim();
                return avatarLiFromEmp(member, { withData: true, assignedBy: ab, assignedAt: at, stageLabel });
              }).join("")}
            </ul>`
          : "";

        return `<div class="d-flex align-items-center">${mainHtml}${teamHtml}</div>`;
      }

      // ---------------------------------------------------------
      // 4. Main Function: Build Row
      // ---------------------------------------------------------
        function buildRowHTML(lead) {
          // 1. Define formatting helpers and calculate displayName FIRST
          const safeStr = (v) => (v == null ? "" : String(v));
          const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" })[m]);
          const fmtDE = (v) => { try { return v ? new Date(v).toLocaleDateString("de-DE") : "-"; } catch { return "-"; } };

          const cName = safeStr(lead?.customer_name).trim();
          const cLastname = safeStr(lead?.customer_lastname).trim();
          const cFirma = safeStr(lead?.firma).trim();
          const displayName = `${cLastname} ${cName}`.trim() || cFirma || "Unbekannt";

          const stageKey = utils.canonicalStage(lead?.stage);

          const cId = lead?.customer_id ?? "";
          const aId = lead?.alternative_id ?? "";
          const pId = lead?.product_id ?? "";
          const lpId = lead?.lead_product_id ?? "";

          const ws = String(lead?.work_status || "playing").toLowerCase();

          // 1. Get Status Block from Kanban (Core)
          const statusBlockHTML = kanban ? kanban.buildStatusBlock(lead) : `<span class="badge badge-secondary">${stageKey}</span>`;

          // 2. Get Live Feed HTML
          const liveFeedRow = listFeedHTML();

          // 3. Meta Logic (Assigned By...)
          const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
          let teamsRaw = lead?.teams;
          if (typeof teamsRaw === "string") {
              try {
                  teamsRaw = JSON.parse(teamsRaw);
              } catch {
                  teamsRaw = [];
              }
          }
          if (!Array.isArray(teamsRaw)) teamsRaw = [];

          const assignments = teamAssignments.length ?
              teamAssignments :
              teamsRaw.map((t) => ({
                  assigned_at: t?.assigned_at ?? null,
                  assigned_at_iso: t?.assigned_at_iso ?? null,
                  assigned_by: t?.assigned_by ?? null,
                  assigned_by_user: t?.assigned_by_user ?? null,
                  stage_label: t?.stage_label ?? null,
              }));

          const parseAssignedAt = (a) => {
              const raw = (a?.assigned_at_iso || a?.assigned_at || "").trim();
              if (!raw) return 0;
              const isoish = raw.includes("T") ? raw : raw.replace(" ", "T");
              const ts = Date.parse(isoish);
              return Number.isFinite(ts) ? ts : 0;
          };

          const latestA = assignments.reduce((best, a) => {
              const ta = parseAssignedAt(a);
              const tb = parseAssignedAt(best);
              return ta > tb ? a : best;
          }, null);

          const assignedBy = (() => {
              const u = latestA?.assigned_by_user;
              if (u && (u.name || u.lastname)) return `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
              const id = Number(latestA?.assigned_by ?? 0);
              return id > 0 ? `Mitarbeiter #${id}` : "";
          })();

          const assignedAtRaw = (latestA?.assigned_at_iso || latestA?.assigned_at || "").trim();
          const STAGE_DE = {
              lead: "Lead",
              offer: "Verkauf",
              deal: "Auftrag",
              project: "Montage",
              completed: "Abschluss",
              archive: "Archiv",
              junk: "Junk"
          };
          const phaseLabel = (() => {
              const lbl = (latestA?.stage_label || "").trim();
              if (lbl) return lbl;
              const key = String(latestA?.stage || "").trim().toLowerCase();
              return STAGE_DE[key] || "";
          })();

          const assignedMetaHTML =
              assignedBy || assignedAtRaw || phaseLabel ?
              `<div class="small text-muted mt-1">
                    ${phaseLabel ? `<span class="mr-2"><i class="feather icon-layers mr-25"></i><span>Phase: <strong>${esc(phaseLabel)}</strong></span></span><span class="mx-1">•</span>` : ``}
                    <i class="feather icon-user mr-25"></i><span>Zugewiesen von: <strong>${esc(assignedBy || "-")}</strong></span>
                    <span class="mx-1">•</span>
                    <i class="feather icon-calendar mr-25"></i><span>${esc(assignedAtRaw ? fmtDE(assignedAtRaw) : "-")}</span>
                  </div>` :
              "";

          return `
              <tr id="row-${esc(lpId)}"
                  class="list-row-item"
                  data-customer-id="${esc(cId)}"
                  data-alternative-id="${esc(aId)}"
                  data-product-id="${esc(pId)}"
                  data-lead-product-id="${esc(lpId)}"
                  data-stage="${esc(stageKey)}"
                  data-run-state="${esc(ws)}">
                
                <td style="width: 100px;">${lead?.created_at ? fmtDE(lead.created_at) : "-"}</td>

                <td style="min-width: 350px;">
                 <a href="/new_lead_profile/${encodeURIComponent(safeStr(cId))}" class="customer-link" style="font-size:1.05rem;">
                  ${esc(displayName)}
                </a>

                  ${assignedMetaHTML}

                  <div class="list-action-bar">
                    <button type="button" class="btn-list-icon" data-menu="termin" title="Termin">
                      <i class="feather icon-calendar"></i>
                      <span class="badge-notes" data-ap-count style="display:none">0</span>
                    </button>
                    <button type="button" class="btn-list-icon" data-menu="aufgabe" title="Aufgabe">
                      <i class="feather icon-check-square"></i>
                      <span class="badge-notes" data-pt-count style="display:none">0</span>
                    </button>

                    <span style="border-left:1px solid #ddd; height:14px; margin:0 4px;"></span>

                    <button type="button" class="btn-list-icon note" data-open-notes data-customer="${esc(cId)}" data-alt="${esc(aId)}" data-product="${esc(pId)}">
                      <i class="feather icon-message-square"></i>
                      <span class="badge-notes" data-count="0" style="display:none">0</span>
                    </button>

                    <div class="btn-group">
                      <button type="button" class="btn-list-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="feather icon-more-vertical"></i>
                      </button>
                      <div class="dropdown-menu">
                          <button class="dropdown-item text-success" data-run="playing"><i class="feather icon-play mr-50"></i> Start</button>
                          <button class="dropdown-item text-warning" data-run="paused"><i class="feather icon-pause mr-50"></i> Pause</button>
                          <button class="dropdown-item text-danger" data-run="stopped"><i class="feather icon-square mr-50"></i> Stopp</button>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="/lead/process/history/${encodeURIComponent(safeStr(cId))}/${encodeURIComponent(safeStr(aId))}/${encodeURIComponent(safeStr(pId))}" data-lh-history>
                              <i class="feather icon-activity mr-50"></i> Verlauf
                          </a>
                      </div>
                    </div>
                  </div>

                  ${liveFeedRow}
                </td>

                <td>${esc(lead?.city ?? "")}</td>
                <td>${esc(lead?.initial ?? "")}</td>
                <td>${listEmpAndTeamHTML(lead)}</td>

                <td>
                  ${statusBlockHTML}
                </td>

                <td>
                  <select class="form-control stage-select" data-id="${esc(lpId)}">
                    ${Object.entries(APP.stageNames || {})
                      .map(([k, l]) => `<option value="${esc(k)}" ${stageKey === k ? "selected" : ""}>${esc(l)}</option>`)
                      .join("")}
                  </select>
                </td>
              </tr>
            `;
      }
      // ---------------------------------------------------------
      // 5. Updated Bootstrapper
      // ---------------------------------------------------------
      function bootstrapListLiveFeed(container) {
            if (!window.LeadUI.liveFeed || typeof window.LeadUI.liveFeed.loadForCard !== "function") return;

            const root = container || document;
            // CHANGED: .list-feed-row -> .list-row-item
            const rows = root.querySelectorAll("tr.list-row-item"); 

            if (!rows.length) return;

            let i = 0;
            const BATCH = 4; // Process in batches to avoid freezing UI

            (function pump() {
                const slice = Array.prototype.slice.call(rows, i, i + BATCH);
                i += BATCH;
                slice.forEach((row) => {
                    // IMPORTANT: Ensure the row has a customer ID before trying to load
                    if (row.dataset.customerId) {
                        window.LeadUI.liveFeed.loadForCard(row);
                    }
                });
                if (i < rows.length) {
                    if ("requestIdleCallback" in window) requestIdleCallback(pump);
                    else setTimeout(pump, 0);
                }
            })();
        }

        // Expose helpers globally
        window.listFeedHTML = listFeedHTML;
        window.LeadUI.bootstrapListLiveFeed = bootstrapListLiveFeed;
    function syncSummary(data) {
      const setTxt = (sel, v) => {
        const el = qs(sel);
        if (el) el.textContent = String(v ?? "");
      };
      const setHTML = (sel, v) => {
        const el = qs(sel);
        if (el) el.innerHTML = v;
      };

      setTxt("#totalEmployees", data?.totalEmployees);
      setTxt("#totalProduct", data?.totalProducts);
      setTxt("#totalCustomer", data?.totalCustomers);

      setHTML("#statusOffen", `${data?.statusCounts?.offen ?? 0} <small>(${data?.statusPercentages?.offen ?? 0}%)</small>`);
      setHTML("#statusZusage", `${data?.statusCounts?.zusage ?? 0} <small>(${data?.statusPercentages?.zusage ?? 0}%)</small>`);
      setHTML("#statusAbsage", `${data?.statusCounts?.absage ?? 0} <small>(${data?.statusPercentages?.absage ?? 0}%)</small>`);

      setTxt("#countCustomers", data?.totalCustomers);
      setTxt("#countProducts", data?.totalProducts);
      setTxt("#countDepartments", data?.totalDepartments);
      setTxt("#countEmployees", data?.totalEmployees);
    }

    function updateListView(leads, meta) {
      const tbody = qs("#kanbanTableBody");
      if (!tbody) return;

      if (!Array.isArray(leads) || !leads.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Keine Ergebnisse gefunden</td></tr>';
        syncSummary(meta);
        return;
      }

      const tmp = document.createElement("tbody");
      tmp.innerHTML = leads.map(buildRowHTML).join("");

      tbody.innerHTML = "";
      tbody.append(...tmp.childNodes);

      syncSummary(meta);
      featherRefreshSoon();

      // Notes badges (list)
      window.LeadUI?.notes?.updateNoteBadgesForVisibleCards?.();

      bootstrapListLiveFeed(tbody);
    }

    function renderPagination(metaLike) {
      const wrap = qs("#listPagination");
      if (!wrap) return;

      const meta = normalizePaginationMeta(metaLike);
      if (!meta || meta.last_page <= 1) {
        wrap.innerHTML = "";
        return;
      }

      const { current_page, last_page } = meta;

      let html = `<nav aria-label="Seiten"><ul class="pagination mb-0">`;

      const add = (p, label, disabled = false, active = false) => {
        if (disabled) html += `<li class="page-item disabled"><span class="page-link">${label}</span></li>`;
        else if (active) html += `<li class="page-item active"><span class="page-link">${label}</span></li>`;
        else html += `<li class="page-item"><a class="page-link" href="#" data-page="${p}">${label}</a></li>`;
      };

      add(current_page - 1, "«", current_page === 1);

      const win = 2;
      const st = Math.max(1, current_page - win);
      const en = Math.min(last_page, current_page + win);

      if (st > 1) {
        add(1, "1", false, current_page === 1);
        if (st > 2) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
      }

      for (let p = st; p <= en; p++) add(p, String(p), false, p === current_page);

      if (en < last_page) {
        if (en < last_page - 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
        add(last_page, String(last_page), false, current_page === last_page);
      }

      add(current_page + 1, "»", current_page === last_page);

      wrap.innerHTML = html + "</ul></nav>";
    }

    /* -------------------------------------------------------------------------- */
    /* Fetchers                                                                    */
    /* -------------------------------------------------------------------------- */
    function normalizeLead(raw) {
      const pick = (obj, ...keys) => {
        for (const k of keys) {
          const v = obj?.[k];
          if (v !== undefined && v !== null && v !== "") return v;
        }
        return null;
      };
      const latest_phase = pick(raw, "latest_phase", "phase_name", "phase_title", "phase_section_title");
      const latest_activity = pick(raw, "latest_activity", "activity_title");
      const done_date = pick(raw, "done_date", "updated_at", "history_at");
      const updated_at = pick(raw, "updated_at", done_date);
      return { ...raw, latest_phase, latest_activity, done_date, updated_at };
    }

    function ensureLoadedMap() {
      if (!State.loaded || typeof State.loaded !== "object") State.loaded = { kanban: false, list: false };
      if (!("kanban" in State.loaded)) State.loaded.kanban = false;
      if (!("list" in State.loaded)) State.loaded.list = false;
    }

    function syncTabCountsFromListPayload(payload) {
      const total =
        payload?.pagination?.total ||
        payload?.meta?.total ||
        (Array.isArray(payload?.leads) ? payload.leads.length : 0);

      setTabCount("#tabCountList", total);
      setTabCount("#tabCountKanban", total);
    }

    function syncTabCountsFromKanban(leads) {
      if (Array.isArray(leads)) setTabCount("#tabCountKanban", leads.length);
    }

    function fetchKanbanView(qsStr) {
      ensureLoadedMap();

      const signal = cancel("kanban");
      const board = qs("#kanban");
      if (board && !State.loaded.kanban) board.innerHTML = '<div class="p-2 text-muted">Lade Kanban…</div>';

      return safeFetchJSON(`${APP.endpoints.kanbanSearch}${qsStr ? `?${qsStr}` : ""}`, { signal, retries: 0 })
        .then((payload) => {
          const arr = Array.isArray(payload?.leads)
            ? payload.leads
            : Array.isArray(payload?.data)
            ? payload.data
            : Array.isArray(payload)
            ? payload
            : [];

          State.lastKanbanData = arr.map(normalizeLead);

          if (!State.loaded.kanban) {
            renderKanbanIncremental(State.lastKanbanData, autoChunk(), () => {
              ensureLoadedMap();
              State.loaded.kanban = true;
              syncTabCountsFromKanban(State.lastKanbanData);
            });
          } else {
            renderKanbanDiff(State.lastKanbanData);
            syncTabCountsFromKanban(State.lastKanbanData);
          }
        })
        .catch((e) => {
          if (e?.name !== "AbortError") Swal.fire("Fehler", e?.message || "Fehler", "error");
        });
    }

    function fetchListView(qsStr) {
      ensureLoadedMap();

      const signal = cancel("list");
      const tbody = qs("#kanbanTableBody");
      if (tbody && !State.loaded.list) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Lade Liste…</td></tr>';
      }

      return safeFetchJSON(`${APP.endpoints.listSearch}${qsStr ? `?${qsStr}` : ""}`, { signal, retries: 0 })
        .then((payload) => {
          ensureLoadedMap();
          State.loaded.list = true;

          const leads = Array.isArray(payload?.leads) ? payload.leads : Array.isArray(payload?.data) ? payload.data : [];
          updateListView(leads, payload);

          renderPagination(payload.pagination || payload.meta || payload);
          syncTabCountsFromListPayload(payload);
        })
        .catch((e) => {
          if (e?.name === "AbortError") return;
          Swal.fire("Fehler", e?.message || "Serverfehler.", "error");
          updateListView([], {});
          renderPagination(null);
        });
    }

    /* -------------------------------------------------------------------------- */
    /* Partials: Ticket & Investment tabs                                          */
    /* -------------------------------------------------------------------------- */
    partials.fetchTicketsTab = async function (qsStr = "") {
      const pane = qs("#ticket");
      if (!pane) return;

      const url = `${APP.endpoints.tickets}${qsStr ? `?${qsStr}` : ""}`;

      try {
        const res = await fetch(url, {
          headers: { Accept: "text/html", "X-Requested-With": "XMLHttpRequest" },
          credentials: "same-origin",
        });

        const html = await res.text();
        pane.innerHTML = html;

        const totalNode = pane.querySelector("[data-ticket-total]") || pane.querySelector("[data-total]");
        const total = totalNode
          ? toInt(
              totalNode.getAttribute("data-ticket-total") ||
                totalNode.getAttribute("data-total") ||
                totalNode.dataset.ticketTotal ||
                totalNode.dataset.total ||
                0,
              0
            )
          : 0;

        const badge = qs("#tabCountTicket");
        if (badge) badge.textContent = String(total);
      } catch (e) {
        console.error("Ticket partial load failed:", e);
      }
    };

  

    function refreshArchiveAndJunk(qsStr) {
      partials.fetchJunkTab?.(qsStr);
      partials.fetchTicketsTab?.(qsStr);
    }

    /* -------------------------------------------------------------------------- */
    /* Unified run state prompt                                                    */
    /* -------------------------------------------------------------------------- */
    async function promptRunReason(state) {
      const label =
        state === "playing" ? "Start" : state === "paused" ? "Pause" : state === "stopped" ? "Stopp" : state;

      const { value: reason, isConfirmed } = await Swal.fire({
        title: `Grund für ${label}`,
        input: "textarea",
        showCancelButton: true,
        confirmButtonText: "Speichern",
        inputValidator: (v) => (!v?.trim() ? "Bitte Grund eingeben" : undefined),
      });

      if (!isConfirmed) return null;
      return String(reason || "").trim();
    }

    /* -------------------------------------------------------------------------- */
    /* Click handlers (Unified: List + Kanban)                                     */
    /* -------------------------------------------------------------------------- */
  /* -------------------------------------------------------------------------- */
/* Click handlers (Unified: List + Kanban)                                    */
/* -------------------------------------------------------------------------- */
document.addEventListener("click", async (e) => {
  // 1. Find the button (works for both direct clicks and nested icon clicks)
  const actBtn = e.target.closest("[data-act], [data-run]");
  if (!actBtn) return;

  // 2. Identify if we are in Kanban or List
  const card = actBtn.closest(".card"); // Kanban
  const row = actBtn.closest("tr.list-row-item"); // List

  // 3. Handle the 'Run' (Play/Pause/Stop) logic
  if (actBtn.dataset.run) {
      e.preventDefault();
      e.stopPropagation();

      const state = actBtn.dataset.run;
      const target = card || row;
      const lpId = target.dataset.leadProductId;

      if (!lpId) {
          console.error("Lead Product ID missing on target element");
          return;
      }

      // Optional: Ask for a reason (matches your controller logic)
      const { value: reason } = await Swal.fire({
          title: `Grund für ${state}`,
          input: 'textarea',
          showCancelButton: true,
          confirmButtonText: 'Speichern'
      });

      if (reason === undefined) return; // User cancelled

      try {
          const res = await window.LeadUI.net.postJSON(`/lead-product/progress/${lpId}/${state}`, {
              reason: reason
          });

          if (res.success) {
              // Update the UI immediately
              if (card) window.LeadUI.kanban.applyRunStateUI(card, state);
              window.LeadUI.silentRefreshBoth(); // Sync the other view
              Swal.fire("Aktualisiert", "", "success");
          }
      } catch (err) {
          Swal.fire("Fehler", "Status konnte nicht geändert werden", "error");
      }
      return;
  }

  // 👇 --- ADD THIS NEW BLOCK BELOW YOUR RUN LOGIC --- 👇
  
  // 4. Handle 'Archive' and 'Delete' (Junk) logic
  if (actBtn.dataset.act === "archive" || actBtn.dataset.act === "delete") {
      e.preventDefault();
      e.stopPropagation();

      const target = card || row;
      if (!target) return;

      const currentStage = target.dataset.stage;
      const newStage = actBtn.dataset.act === "archive" ? "archive" : "junk";

      // Extract current team IDs to prefill the SweetAlert Select2
      let currentTeamIds = [];
      try {
          currentTeamIds = JSON.parse(target.dataset.teamIds || "[]").map(x => Number(x));
      } catch(err) {}

      // Prompt user with your existing SweetAlert logic
      const confirm = await confirmStageChange(newStage, currentStage, currentTeamIds);
      if (!confirm.ok) return;

      try {
          const { customerId, alternativeId, productId, leadProductId } = target.dataset;

          await applyStageChange({
              customerId,
              alternativeId,
              productId,
              leadProductId,
              newStage,
              noteHTML: confirm.reasonHTML,
              teams: confirm.teams,
          });

          // Update DOM gracefully
          if (card) {
              if (newStage === "junk" || stageFilterExcludes(newStage)) {
                  card.remove();
              } else {
                  moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
                  enforceActionVisibility(card);
              }
          } else if (row) {
              if (newStage === "junk" || stageFilterExcludes(newStage)) {
                  row.remove();
              } else {
                  row.dataset.stage = newStage;
              }
          }

          window.LeadUI?.silentRefreshBoth?.();
          Swal.fire({
              icon: "success",
              title: newStage === "archive" ? "Archiviert" : "In Junk verschoben",
              text: newStage === "archive" ? "Erfolgreich ins Archiv verschoben." : "Eintrag wurde aussortiert.",
              timer: 1500,
              showConfirmButton: false
          });
      } catch (err) {
          Swal.fire("Fehler", err?.message || "Serverfehler beim Verschieben.", "error");
      }
      return;
  }
});

    /* -------------------------------------------------------------------------- */
    /* Kanban: click selection + dragstart delegation                              */
    /* -------------------------------------------------------------------------- */
    document.addEventListener("click", (e) => {
      const card = e.target.closest("#kanban .card");
      if (!card) return;

      // Avoid selecting when clicking action buttons/links/inputs
      if (e.target.closest(".card-actions, button, a, input, select, textarea")) return;

      selectCard(card, e);
    });

    document.addEventListener("dragstart", (e) => {
      const card = e.target.closest("#kanban .card");
      if (!card) return;
      onKanbanDragStart(e, card);
    });

    // Enable drop only on columns (and avoid "open in new tab" elsewhere)
    document.addEventListener("dragover", (e) => {
      if (!e.dataTransfer) return;

      // Only handle our own DND type
      if (!Array.from(e.dataTransfer.types || []).includes(DND_MIME)) return;

      const col = e.target.closest(".column");
      if (col) e.preventDefault();
    });

    document.addEventListener(
      "drop",
      (e) => {
        if (!e.dataTransfer) return;
        if (!Array.from(e.dataTransfer.types || []).includes(DND_MIME)) return;

        const col = e.target.closest(".column");
        if (!col) {
          // Prevent browser from navigating when dropping our internal drag payload
          e.preventDefault();
          return;
        }

        onKanbanDrop(e);
      },
      true
    );

    /* -------------------------------------------------------------------------- */
    /* List: stage select change                                                   */
    /* -------------------------------------------------------------------------- */
    document.addEventListener("change", async (e) => {
      const sel = e.target.closest("select.stage-select");
      if (!sel) return;

      const row = sel.closest("tr.list-row-item");
      if (!row) return;

      const newStage = sel.value;

      // old stage from defaultSelected (Laravel often renders the current one)
      const prevIndex = Array.from(sel.options).findIndex((o) => o.defaultSelected);
      const oldStage = prevIndex >= 0 ? canonicalStage(sel.options[prevIndex].value) : canonicalStage(row.dataset.stage);

      const customerId = row.dataset.customerId;
      const alternativeId = row.dataset.alternativeId;
      const productId = row.dataset.productId;
      const leadProductId = sel.dataset.id || row.dataset.leadProductId || row.id?.split("-")[1];

      // teams from row if you ever store them (optional)
      const currentTeamIds = Array.isArray(safeJSON(row.dataset.teamIds || "[]", []))
        ? safeJSON(row.dataset.teamIds || "[]", [])
        : [];

      try {
        const confirm = await confirmStageChange(newStage, oldStage, currentTeamIds);
        if (!confirm.ok) {
          sel.selectedIndex = Math.max(0, prevIndex);
          return;
        }

        await applyStageChange({
          customerId,
          alternativeId,
          productId,
          leadProductId,
          newStage,
          noteHTML: confirm.reasonHTML,
          teams: confirm.teams,
        });

        // Update defaultSelected to keep oldStage detection correct next time
        sel.querySelectorAll("option").forEach((o) => (o.defaultSelected = false));
        sel.options[sel.selectedIndex].defaultSelected = true;

        // Remove list rows if excluded
        if (stageFilterExcludes(newStage)) {
          // remove main row + feed row
          const feedRow = row.nextElementSibling?.classList?.contains("list-feed-row") ? row.nextElementSibling : null;
          row.remove();
          feedRow?.remove?.();
        } else {
          row.dataset.stage = canonicalStage(newStage);
        }

        // update kanban card if present
        const card =
          qs(`#card-${CSS.escape(String(leadProductId))}`) ||
          qs(`#${CSS.escape(String(leadProductId))}`) ||
          qs(`.card[data-lead-product-id="${CSS.escape(String(leadProductId))}"]`);

        if (card) {
          moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
          enforceActionVisibility(card);
        }

        window.LeadUI?.silentRefreshBoth?.();
        Swal.fire("OK", "Phase aktualisiert.", "success");
      } catch (err) {
        sel.selectedIndex = Math.max(0, prevIndex);
        Swal.fire("Fehler", err?.message || "Serverfehler.", "error");
      }
    });

    /* -------------------------------------------------------------------------- */
    /* Sorting + pagination clicks                                                 */
    /* -------------------------------------------------------------------------- */
    document.addEventListener("click", (e) => {
      const th = e.target.closest("#profile th.sortable");
      if (!th) return;

      const key = th.dataset.sort;
      if (!key) return;

      State.sort = State.sort?.key === key
        ? { key, dir: State.sort.dir === "asc" ? "desc" : "asc" }
        : { key, dir: "asc" };

      qsa("#profile th.sortable").forEach((h) => h.classList.remove("active", "desc"));
      th.classList.add("active");
      if (State.sort.dir === "desc") th.classList.add("desc");

      State.page = 1;
      State.filtersQS = filters.buildFilterQS();
      saveToLocal();
      syncURL();

      fetchListView(addPage(State.filtersQS, State.page));
      if (isKanbanActive()) fetchKanbanView(State.filtersQS);
    });

    document.addEventListener("click", (e) => {
      const a = e.target.closest("#listPagination a.page-link[data-page]");
      if (!a) return;

      e.preventDefault();
      const p = toInt(a.getAttribute("data-page"), 1);
      State.page = p;

      saveToLocal();
      syncURL();

      fetchListView(addPage(State.filtersQS, State.page));
    });

    /* -------------------------------------------------------------------------- */
    /* Tabs                                                                        */
    /* -------------------------------------------------------------------------- */
    if (window.jQuery) {
      jQuery('a[data-toggle="tab"][href="#home"]').on("shown.bs.tab", () => {
        ensureColumns();
        renderKanbanDiff(State.lastKanbanData || []);
        featherRefreshSoon();
        enforceActionVisibility();
      });

      jQuery('a[data-toggle="tab"][href="#junk"]').on("shown.bs.tab", () => {
        partials.fetchJunkTab?.(State.filtersQS);
      });
  
    }

    document.addEventListener("shown.bs.tab", (e) => {
      const trg = e.target?.getAttribute("href") || "";
      if (trg === "#ticket") {
        const qsStr = filters.buildFilterQS();
        partials.fetchTicketsTab?.(qsStr);
      }
    });

    /* -------------------------------------------------------------------------- */
    /* Summary cards + filter buttons                                              */
    /* -------------------------------------------------------------------------- */
    function setSummaryActive(id) {
      qsa(".summary-card").forEach((c) => c.classList.remove("active"));
      if (id) qs("#" + id)?.classList.add("active");
    }

    function applyStatusGroup(g, cardId) {
      State.statusGroup = g;
      State.page = 1;

      State.filtersQS = filters.buildFilterQS();
      saveToLocal();
      syncURL();

      const withPage = addPage(State.filtersQS, State.page);

      fetchListView(withPage);
      fetchKanbanView(State.filtersQS);
      refreshArchiveAndJunk(State.filtersQS);

      setSummaryActive(cardId || null);
      filters.updateFilterBadges?.();
    }

    qs("#cardOffen")?.addEventListener("click", () => applyStatusGroup("offen", "cardOffen"));
    qs("#cardZusage")?.addEventListener("click", () => applyStatusGroup("zusage", "cardZusage"));
    qs("#cardAbsage")?.addEventListener("click", () => applyStatusGroup("absage", "cardAbsage"));

    qs("#btnApplyFilters")?.addEventListener("click", () => {
      State.page = 1;
      State.filtersQS = filters.buildFilterQS();
      State.lastAppliedQS = State.filtersQS;

      saveToLocal();
      syncURL();

      const withPage = addPage(State.filtersQS, State.page);

      fetchListView(withPage);
      fetchKanbanView(State.filtersQS);
      refreshArchiveAndJunk(State.filtersQS);

      partials.fetchTicketsTab?.(State.filtersQS);

      closeOverlays();
    });

    qs("#btnClearFilters")?.addEventListener("click", () => {
      const form = qs("#kanbanFilterForm");
      if (!form) return;

      form.reset();
      if (window.jQuery) window.jQuery(form).find(".select2").val(null).trigger("change");

      State.statusGroup = null;
      setSummaryActive(null);

      State.page = 1;
      State.filtersQS = filters.buildFilterQS();

      saveToLocal();
      syncURL();

      filters.updateFilterBadges?.();

      const withPage = addPage(State.filtersQS, State.page);

      fetchListView(withPage);
      fetchKanbanView(State.filtersQS);
      refreshArchiveAndJunk(State.filtersQS); 
      partials.fetchTicketsTab?.(State.filtersQS);
    });

    /* -------------------------------------------------------------------------- */
    /* LiveFeed row click                                                          */
    /* -------------------------------------------------------------------------- */
    document.addEventListener("click", (e) => {
      const row = e.target.closest("#kanbanTableBody tr.list-row-item");
      if (!row) return;
      if (e.target.closest("button, a, select, input, textarea")) return;

      if (liveFeed && typeof liveFeed.loadForCard === "function") liveFeed.loadForCard(row);
    });

    /* -------------------------------------------------------------------------- */
    /* Keyboard                                                                     */
    /* -------------------------------------------------------------------------- */
    document.addEventListener("keydown", (e) => {
      if (e.ctrlKey && e.key.toLowerCase() === "f") {
        e.preventDefault();
        qs("#btnOpenDrawer")?.click();
      }
      if (e.key === "Escape") closeOverlays();
    });

    /* -------------------------------------------------------------------------- */
    /* Silent refresh (public)                                                     */
    /* -------------------------------------------------------------------------- */
    function silentRefreshBoth() {
      const qsStr = State.filtersQS || "";
      fetchListView(addPage(qsStr, State.page || 1));
      fetchKanbanView(qsStr);
      partials.fetchTicketsTab?.(qsStr);
    }
    window.LeadUI.silentRefreshBoth = silentRefreshBoth;

    /* -------------------------------------------------------------------------- */
    /* Boot                                                                         */
    /* -------------------------------------------------------------------------- */
    document.addEventListener("DOMContentLoaded", () => {
      featherRefreshSoon();
      filters.initSelect2?.();
      filters.updateFilterBadges?.();

      initFromURL();
      if (!location.search) restoreFromLocal();

      State.filtersQS = filters.buildFilterQS();
      saveToLocal();
      syncURL();

      ensureLoadedMap();
      State.loaded.kanban = false;
      State.loaded.list = false;

      // initial loads
      fetchListView(addPage(State.filtersQS, State.page || 1));
      fetchKanbanView(State.filtersQS);

      // side tabs initial refresh
      refreshArchiveAndJunk(State.filtersQS);
    });
  })();
</script>
 
<!-- Kanban Column Script  -->
<script>
  (function() {
      "use strict";

      document.addEventListener("DOMContentLoaded", () => {
          // Function to toggle column visibility
          function toggleColumn(stageId, isVisible) {
              const col = document.getElementById(stageId);
              if (!col) return;

              if (isVisible) {
                  // We use 'flex' because your .column class likely uses display:flex
                  col.style.display = 'flex'; 
                  col.classList.remove('d-none');
              } else {
                  col.style.display = 'none';
                  col.classList.add('d-none');
              }
          }

          // 1. Bind Click Events to Checkboxes
          const toggles = document.querySelectorAll('.col-toggle-checkbox');
          toggles.forEach(chk => {
              // Initial check to sync JS with HTML state
              // (Optional, but good if you have cached values)
              
              chk.addEventListener('change', () => {
                  toggleColumn(chk.value, chk.checked);
              });
          });

          // 2. Patch Kanban Renderer 
          // This ensures that if the board re-renders (e.g. after a search),
          // we re-apply the visibility rules based on the checkboxes.
          if (window.LeadUI && window.LeadUI.kanban) {
              const originalEnsureColumns = window.LeadUI.kanban.ensureColumns;
              
              window.LeadUI.kanban.ensureColumns = function() {
                  originalEnsureColumns(); // Let the core create the columns
                  
                  // Immediately apply visibility based on current checkbox state
                  document.querySelectorAll('.col-toggle-checkbox').forEach(chk => {
                      toggleColumn(chk.value, chk.checked);
                  });
              };
          }
      });
  })();
</script>


 <script>
    (function(){
      "use strict";
    
    /* Maps */
      const DATE_FMT = { hour:'2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'numeric' };

      /* ✅ German labels for your stages */
      const LABEL = (s) => ({
        // your set
        lead:      'Lead',        // or 'Interessent'
        offer:     'Angebot',
        deal:      'Auftrag',
        project:   'Projekt',     // or 'Montage'
        junk:      'Aussortiert',
        canceled:  'Abgebrochen', // or 'Storniert'
        ticket:    'Ticket',
        pause:     'Pausiert',

        // optional extras (kept for safety; remove if unused)
        completed: 'Abgeschlossen',
        qualify:   'Qualifizierung',
        negotiation:'Verhandlung',
        won:       'Gewonnen',
        lost:      'Verloren',
        maintenance:'Wartung',
        repair:    'Reparatur',
        planning:  'Planung',
        complete:  'Komplett'
      }[String(s||'').toLowerCase()] || (s ? String(s) : 'Unbekannt'));

      /* 🎨 Badge classes per stage (lh- namespaced) */
      const BADGE = (s) => ({
        lead:      'lh-badge lh-badge--secondary',
        offer:     'lh-badge lh-badge--info',
        deal:      'lh-badge lh-badge--primary',
        project:   'lh-badge lh-badge--primary',
        completed: 'lh-badge lh-badge--success',
        junk:      'lh-badge lh-badge--secondary',
        canceled:  'lh-badge lh-badge--danger',
        ticket:    'lh-badge lh-badge--secondary',
        pause:     'lh-badge lh-badge--warning',

        // optional extras
        qualify:    'lh-badge lh-badge--secondary',
        negotiation:'lh-badge lh-badge--warning',
        won:        'lh-badge lh-badge--success',
        lost:       'lh-badge lh-badge--danger',
        maintenance:'lh-badge lh-badge--secondary',
        repair:     'lh-badge lh-badge--secondary',
        planning:   'lh-badge lh-badge--secondary',
        complete:   'lh-badge lh-badge--primary'
      }[String(s||'').toLowerCase()] || 'lh-badge');


      const ICONS = {
        lead:`<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M3 11h18v2H3z"/></svg>`,
        qualify:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M9 16.2l-3.5-3.5 1.4-1.4L9 13.4l7.1-7.1 1.4 1.41z"/></svg>`,
        offer:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 6h16v12H4zM6 8h12v2H6z"/></svg>`,
        negotiation:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 4h10v6H4zM14 10l6 4-6 4z"/></svg>`,
        won:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24 7.46 13.97 5.82 21z"/></svg>`,
        lost:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L10.59 13.4l-6.3 6.3L2.88 18.3l6.3-6.29-6.3-6.3L4.3 4.29l6.29 6.3 6.3-6.3z"/></svg>`
      };

      /* DOM */
      const root = document.getElementById('lh-drawer');
      const panel = root?.querySelector('.lh-panel');
      const title = document.getElementById('lh-title-text');
      const tl    = document.getElementById('lh-timeline');
      const acts  = document.getElementById('lh-activities');
      if (!root || !panel || !title || !tl || !acts) return;

      /* Drawer controls */
      const open = () => { root.setAttribute('aria-hidden','false'); panel.focus({preventScroll:true}); document.body.style.overflow='hidden'; };
      const close = () => { root.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };
      document.addEventListener('click', e => { if (e.target.closest('[data-lh-close]')) close(); });
      document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

      /* Helpers */
      const esc = s => (s==null?'':String(s)).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

      let apAutocomplete = null;

      function initAddressAutocomplete(){
        const input = qs('#ap-full_address');
        if (!input) return;
        if (!window.google || !google.maps || !google.maps.places) return;

        if (apAutocomplete) return; // only once

        apAutocomplete = new google.maps.places.Autocomplete(input, {
          types: ['geocode'],
          componentRestrictions: { country: 'de' }
        });

        apAutocomplete.addListener('place_changed', () => {
          const place = apAutocomplete.getPlace();
          if (!place || !place.geometry) return;

          const lat = place.geometry.location.lat();
          const lng = place.geometry.location.lng();
          qs('#ap-latitude').value  = lat;
          qs('#ap-longitude').value = lng;

          let street = '', streetNo = '', postcode = '', city = '';

          (place.address_components || []).forEach(c => {
            const types = c.types || [];
            if (types.includes('route'))          street   = c.long_name;
            if (types.includes('street_number'))  streetNo = c.long_name;
            if (types.includes('postal_code'))    postcode = c.long_name;
            if (types.includes('locality'))       city     = c.long_name;
            if (!city && types.includes('postal_town')) city = c.long_name;
          });

          const streetField   = qs('#ap-street');
          const postcodeField = qs('#ap-postcode');
          const cityField     = qs('#ap-city');

          if (streetField && !streetField.value)
            streetField.value = [street, streetNo].filter(Boolean).join(' ');
          if (postcodeField && !postcodeField.value)
            postcodeField.value = postcode;
          if (cityField && !cityField.value)
            cityField.value = city;
        });
      }
      window.initAddressAutocomplete = initAddressAutocomplete;

      const fmt = s => s ? new Date(String(s).replace(' ','T')).toLocaleString('de-DE', DATE_FMT) : '';

      function skeleton(){
        title.textContent = 'Verlauf wird geladen …';
        tl.innerHTML = `
          <li class="lh-item">
            <div class="lh-icowrap"><div class="lh-ico"></div></div>
            <div class="lh-content">
              <div class="lh-skel" style="width:55%"></div>
              <div class="lh-skel" style="width:35%"></div>
              <div class="lh-skel" style="width:80%"></div>
            </div>
          </li>`;
        acts.innerHTML = `
          <div class="lh-card">
            <div class="lh-skel" style="width:60%"></div>
            <div class="lh-skel" style="width:40%"></div>
            <div class="lh-skel" style="width:85%"></div>
          </div>`;
      }

      function render(data){
        title.textContent = 'Verlauf – ' + (data.customerName || '');

        // Timeline
        tl.innerHTML = (data.timeline?.length ? data.timeline : []).map(t => {
          const key = String(t.to_stage||'').toLowerCase();
          const to  = esc(LABEL(key));
          const from = t.from_stage ? `<small class="lh-muted ml-2">von ${esc(LABEL(t.from_stage))}</small>` : '';
          const when = t.changed_at ? `<small class="lh-muted ml-2">${fmt(t.changed_at)}</small>` : '';
          const by   = t.changed_by ? `<small class="lh-muted ml-2">· ${esc(t.changed_by)}</small>` : '';
          const desc = t.description ? `<div class="mt-2">${esc(t.description).replace(/\n/g,'<br>')}</div>` : '';
          return `
            <li class="lh-item">
              <div class="lh-icowrap"><div class="lh-ico" title="${to}">${ICONS[key]||''}</div></div>
              <div class="lh-content">
                <div class="d-flex align-items-center flex-wrap">
                  <span class="${BADGE(key)} mr-2">${to}</span>${from}${when}${by}
                </div>
                ${desc}
              </div>
            </li>`;
        }).join('') || `<li class="lh-muted" style="padding:.5rem 0">Kein Phasenverlauf vorhanden.</li>`;

        // Activities
        acts.innerHTML = (data.customerHistory?.length ? data.customerHistory : []).map(h => {
          const when = h.at ? `<span class="lh-muted">${fmt(h.at)}</span>` : '';
          const ch   = h.channel ? ` · <span class="lh-muted">#${esc(h.channel)}</span>` : '';
          const note = h.note ? `<div class="mt-2">${esc(h.note).replace(/\n/g,'<br>')}</div>` : '';
          const meta = (h.meta && typeof h.meta==='object')
            ? `<div class="mt-2">` + Object.entries(h.meta).map(([k,v]) =>
                `<span class="lh-badge" style="margin-right:6px;margin-bottom:6px">
                  <span class="lh-muted">${esc(k)}:</span> ${esc(typeof v==='string'||typeof v==='number'? v : JSON.stringify(v))}
                </span>`
              ).join('') + `</div>` : '';
          return `
            <div class="lh-card">
              <div class="d-flex justify-content-between">
                <div class="font-weight-bold">${esc(h.phase_name||'–')}${h.activity_title?` · ${esc(h.activity_title)}`:''}</div>
                ${when}
              </div>
              <div class="lh-muted mt-1"><i class="feather icon-user" style="font-size:12px"></i> ${esc(h.by||'Unbekannt')}${ch}</div>
              ${note}${meta}
            </div>`;
        }).join('') || `<div class="lh-muted" style="padding:.5rem 0">Keine Aktivitäten gefunden.</div>`;
      }

      async function fetchJSON(href){
        const url = href.includes('?') ? `${href}&format=json` : `${href}?format=json`;
        const res = await fetch(url, {
          headers:{ 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
          credentials:'same-origin', cache:'no-store'
        });
        const ct = res.headers.get('content-type') || '';
        if (!ct.includes('application/json')) throw new Error('Non-JSON response: ' + ct);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      }

      function onClick(e){
        const a = e.target.closest('a[data-lh-history]');
        if (!a) return;
        e.preventDefault();
        open(); skeleton();
        fetchJSON(a.href).then(render).catch(err=>{
          console.error('[lh] fetch failed:', err);
          title.textContent = 'Fehler beim Laden';
          tl.innerHTML = '<li class="lh-muted" style="color:#b91c1c;padding:.5rem 0">Fehler beim Laden des Verlaufs.</li>';
          acts.innerHTML = '';
        });
      }

      document.addEventListener('click', onClick);
      document.addEventListener('turbo:load',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
      document.addEventListener('turbolinks:load',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
      document.addEventListener('livewire:navigated',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
    })();
</script>
 
<script>
  (function () {
    "use strict";

    // ------------------------------------------------
    // Bootstrap from LeadUI (with safe fallbacks)
    // ------------------------------------------------
    const { APP = {}, net = {}, utils = {} } = window.LeadUI || {};

    const {
      safeFetchJSON: leadSafeFetchJSON
    } = net;

    const {
      qs: leadQs,
      qsa: leadQsa,
      CSRF: leadCSRF,
      featherRefreshSoon: leadFeatherRefreshSoon,
    } = utils;

    const qs =
      leadQs ||
      function (selector, ctx = document) {
        return ctx.querySelector(selector);
      };

    const qsa =
      leadQsa ||
      function (selector, ctx = document) {
        return Array.from(ctx.querySelectorAll(selector));
      };

    const CSRF =
      leadCSRF ||
      function () {
        return (
          document.querySelector('meta[name="csrf-token"]')?.content || ""
        );
      };

    const featherRefreshSoon =
      leadFeatherRefreshSoon ||
      function () {
        /* noop */
      };

    const safeFetchJSON =
      leadSafeFetchJSON ||
      async function (url, { method = "GET", headers = {}, body, retries = 0 } = {}) {
        async function go() {
          const res = await fetch(url, {
            method,
            credentials: "same-origin",
            headers: {
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
              ...headers,
            },
            body,
          });

          const text = await res.text();
          if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
          }

          try {
            return JSON.parse(text);
          } catch {
            throw new Error("Invalid JSON response");
          }
        }

        try {
          return await go();
        } catch (err) {
          if (retries > 0 && method === "GET") {
            await new Promise((r) => setTimeout(r, 200));
            return safeFetchJSON(url, { method, headers, body, retries: retries - 1 });
          }
          throw err;
        }
      };

    // ------------------------------------------------
    // Tiny helpers
    // ------------------------------------------------
    const esc = (val) =>
      String(val ?? "").replace(/[&<>]/g, (m) => {
        return { "&": "&amp;", "<": "&lt;", ">": "&gt;" }[m];
      });

    const norm = (val) => String(val || "").toLowerCase().trim();

    const debounce = (fn, ms = 200) => {
      let t;
      return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), ms);
      };
    };

    // safe highlight for simple plain text
    function hl(text, query) {
      const src = esc(text ?? "");
      const q = norm(query);
      if (!q) return src;
      const re = new RegExp(
        `(${q.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`,
        "ig"
      );
      return src.replace(re, '<mark class="ptk-hl">$1</mark>');
    }

    // ------------------------------------------------
    // Config: columns & labels
    // ------------------------------------------------
    const PTK_STATUS = [
      { key: "open",        label: "Offen" },
      { key: "in_progress", label: "In Arbeit" },
      { key: "paused",      label: "Pausiert" },
      { key: "done",        label: "Erledigt" },
      { key: "canceled",    label: "Storniert" },
    ];

    const STATUS_ORDER = PTK_STATUS.map((s) => s.key);

    const statusLabel = (key) =>
      PTK_STATUS.find((s) => s.key === key)?.label || key;

    // ------------------------------------------------
    // Personal Tasks Kanban (PTK)
    // ------------------------------------------------
    const PTK = {
      _tasks: [],
      _query: "",
      _ctx: null,
      _searchEl: null,
      _editingId: null,

      // --------------- public API ----------------

      open(customerId, alternativeId, productId, title) {
        const titleEl = qs("#pt-title");
        if (titleEl) {
          titleEl.textContent = title || "Aufgaben";
        }

        const cField = qs("#pt-customer_id");
        const aField = qs("#pt-alternative_id");
        const pField = qs("#pt-product_id");

        if (cField) cField.value = customerId || "";
        if (aField) aField.value = alternativeId || "";
        if (pField) pField.value = productId || "";

        this._ctx = {
          customerId: customerId || "",
          alternativeId: alternativeId || "",
          productId: productId || "",
        };

        this._editingId = null;

        const form = qs("#pt-form");
        if (form) form.reset();

        if (window.jQuery) {
          jQuery("#pt-employee_ids").val(null).trigger("change");
        }

        this.show();
        this.ensureBoard();
        this.ensureSearchBar();
        this.renderSkeletonContent();
        this.loadTasks();
      },

      show() {
        qs("#pt-backdrop")?.classList.add("show");
        qs("#pt-drawer")?.classList.add("open");
        document.body.style.overflow = "hidden";
      },

      hide() {
        qs("#pt-backdrop")?.classList.remove("show");
        qs("#pt-drawer")?.classList.remove("open");
        document.body.style.overflow = "";
        this._editingId = null;
      },

      setQuery(query) {
        this._query = norm(query || "");
        this.renderFiltered();
      },

      updateCardBadge() {
        const ctx = this._ctx;
        if (!ctx) return;

        const c = ctx.customerId || "";
        const a = ctx.alternativeId || "";
        const p = ctx.productId || "";
        const count = this._tasks.length;

        const selector = `.card[data-customer-id="${c}"][data-alternative-id="${a}"][data-product-id="${p}"]`;

        qsa(selector).forEach((card) => {
          const btn = card.querySelector('.kb-menu-item[data-menu="aufgabe"]');
          if (!btn) return;

          let pill = btn.querySelector("[data-pt-count]");
          if (!pill) {
            pill = document.createElement("span");
            pill.className = "kb-menu-pill kb-menu-pill--pt";
            pill.setAttribute("data-pt-count", "");
            btn.appendChild(pill);
          }

          pill.textContent = String(count);
          pill.style.display = count ? "inline-flex" : "none";
        });
      },

      // --------------- board shell ----------------

      ensureBoard() {
        const wrap = qs("#pt-list");
        if (!wrap) return;

        if (wrap.dataset.ptkBoard === "1") {
          return;
        }

        wrap.classList.add("ptk-board");
        wrap.dataset.ptkBoard = "1";

        wrap.innerHTML = `
          <div class="ptk-board-row">
            ${PTK_STATUS.map(
              (s) => `
                <section class="ptk-col" data-ptk-col="${s.key}">
                  <header class="ptk-col-head">
                    <span class="ptk-col-title">${s.label}</span>
                    <span class="ptk-col-count" data-ptk-count="${s.key}">0</span>
                  </header>
                  <div class="ptk-col-body"
                      data-ptk-dropzone="${s.key}"
                      aria-label="${s.label}"
                      tabindex="0"></div>
                </section>
              `
            ).join("")}
          </div>
        `;

        this.bindDND();
      },

      ensureSearchBar() {
        const drawer = qs("#pt-drawer");
        const body = qs("#pt-list");
        if (!drawer || !body) return;

        let bar = drawer.querySelector("#ptk-search-bar");
        if (!bar) {
          bar = document.createElement("div");
          bar.id = "ptk-search-bar";
          bar.className = "p-2 bg-white border-bottom";
          bar.innerHTML = `
            <div class="input-group" id="ptk-search-wrap" style="max-width:520px">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="feather icon-search"></i>
                </span>
              </div>
              <input id="ptk-search"
                    class="form-control"
                    placeholder="Aufgaben durchsuchen… (Titel, Beschreibung, Mitarbeiter)"
                    autocomplete="off">
              <div class="input-group-append">
                <button id="ptk-search-clear"
                        class="btn btn-outline-secondary"
                        type="button"
                        title="Leeren">&times;</button>
              </div>
            </div>
          `;

          const head = drawer.querySelector(".notes-head");
          if (head) {
            head.insertAdjacentElement("afterend", bar);
          } else {
            drawer.insertBefore(bar, body);
          }
        }

        this._searchEl = qs("#ptk-search");
        const clearBtn = qs("#ptk-search-clear");

        if (this._searchEl && !this._searchEl._wired) {
          const run = debounce((ev) => this.setQuery(ev.target.value), 120);
          this._searchEl.addEventListener("input", run);
          this._searchEl.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
              this._searchEl.value = "";
              this.setQuery("");
              this._searchEl.blur();
            }
          });
          this._searchEl._wired = true;
        }

        if (clearBtn && !clearBtn._wired) {
          clearBtn.addEventListener("click", () => {
            if (!this._searchEl) return;
            this._searchEl.value = "";
            this.setQuery("");
            this._searchEl.focus();
          });
          clearBtn._wired = true;
        }
      },

      renderSkeletonContent() {
        qsa("[data-ptk-dropzone]").forEach((zone) => {
          zone.innerHTML = `<div class="ptk-empty">Lade Aufgaben…</div>`;
        });
        const head = qs("#pt-title");
        if (head) head.classList.add("ptk-loading");
      },

      setLoading(on) {
        const head = qs("#pt-title");
        if (!head) return;
        head.classList.toggle("ptk-loading", !!on);
      },

      // --------------- data IO ----------------

      async loadTasks() {
        const c = qs("#pt-customer_id")?.value;
        const a = qs("#pt-alternative_id")?.value;
        const p = qs("#pt-product_id")?.value || "";

        if (!c || !a) {
          this._tasks = [];
          this.renderFiltered();
          this.updateCardBadge();
          return;
        }

        const url =
          `${APP.endpoints.personalTasksIndex}` +
          `?customer_id=${encodeURIComponent(c)}` +
          `&alternative_id=${encodeURIComponent(a)}` +
          (p ? `&product_id=${encodeURIComponent(p)}` : "");

        try {
          this.setLoading(true);
          const res = await safeFetchJSON(url, { retries: 0 });

          const tasks = Array.isArray(res?.tasks)
            ? res.tasks
            : Array.isArray(res)
            ? res
            : [];

          this._tasks = tasks;
          this.renderFiltered();

          const badge = qs("#pt-count");
          if (badge) badge.textContent = String(tasks.length);

          this.updateCardBadge();
        } catch (err) {
          qsa("[data-ptk-dropzone]").forEach((zone) => {
            zone.innerHTML = `
              <div class="text-danger p-2 small">
                Aufgaben konnten nicht geladen werden.<br>${esc(err.message || "")}
              </div>
            `;
          });
        } finally {
          this.setLoading(false);
        }
      },

      // --------------- filtering + rendering ---------------

      getFilteredTasks() {
        const q = this._query;
        if (!q) return this._tasks.slice();

        const has = (txt) => txt && norm(txt).includes(q);

        return this._tasks.filter((task) => {
          if (has(task.task_title)) return true;
          if (has(task.description)) return true;

          const statusKey = String(task.task_status || "open").toLowerCase();
          if (has(statusLabel(statusKey))) return true;

          // task-level employees
          if (Array.isArray(task.employees)) {
            for (const emp of task.employees) {
              const name = `${emp.lastname || ""} ${emp.name || ""}`;
              if (has(name) || has(emp.lastname) || has(emp.name)) return true;
            }
          }

          // step titles / descriptions / employees
          if (Array.isArray(task.steps)) {
            for (const step of task.steps) {
              if (has(step.title) || has(step.description)) return true;

              if (Array.isArray(step.employees)) {
                for (const emp of step.employees) {
                  const name = `${emp.lastname || ""} ${emp.name || ""}`;
                  if (has(name) || has(emp.lastname) || has(emp.name)) {
                    return true;
                  }
                }
              }
            }
          }

          return false;
        });
      },

      renderFiltered() {
        const filtered = this.getFilteredTasks();

        // group by status
        const buckets = Object.fromEntries(STATUS_ORDER.map((k) => [k, []]));
        for (const t of filtered) {
          const key = String(t.task_status || "open").toLowerCase();
          (buckets[key] || buckets.open).push(t);
        }

        for (const key of STATUS_ORDER) {
          const zone = qs(`[data-ptk-dropzone="${key}"]`);
          const badge = qs(`[data-ptk-count="${key}"]`);
          const arr = buckets[key] || [];

          if (badge) badge.textContent = String(arr.length);

          if (!zone) continue;

          zone.innerHTML =
            arr.map((t) => this.cardHTML(t, this._query)).join("") ||
            `<div class="ptk-empty">Keine Aufgaben</div>`;
        }

        const totalBadge = qs("#pt-count");
        if (totalBadge) totalBadge.textContent = String(filtered.length);

        featherRefreshSoon();
        this.bindCardEvents();
        this.recountColumns();
      },

      // --------------- card rendering ---------------

      cardHTML(task, query) {
        const q = query || "";
        const status = String(task.task_status || "open").toLowerCase();
        const color = task.color || "#8fc73e";

        const title = task.task_title || "Aufgabe";
        const descBlock = task.description
          ? `<div class="ptk-card-desc">${hl(task.description, q)}</div>`
          : "";

        const steps = Array.isArray(task.steps) ? task.steps : [];
        let stepsBlock = "";

        if (steps.length) {
          const rows = steps
            .map((step) => {
              const emps = Array.isArray(step.employees)
                ? step.employees
                : [];
              const empHTML = emps
                .map((emp) => {
                  const name = `${emp.lastname || ""} ${emp.name || ""}`.trim();
                  return `
                    <span class="ptk-emp ptk-emp--xs">
                      <img src="/images/employee/${emp.image || ""}"
                          alt=""
                          width="16"
                          height="16"
                          class="ptk-ava">
                      ${hl(name || emp.lastname || "", q)}
                    </span>
                  `;
                })
                .join("");

              return `
                <div class="ptk-step">
                  <div class="ptk-step-emps">${empHTML}</div>
                </div>
              `;
            })
            .join("");

          stepsBlock = `
            <div class="ptk-steps">
              <div class="small text-muted mb-1">Verantwortliche</div>
              ${rows}
            </div>
          `;
        } else if (Array.isArray(task.employees) && task.employees.length) {
          const emps = task.employees
            .map((emp) => {
              const name = `${emp.lastname || ""} ${emp.name || ""}`.trim();
              return `
                <span class="ptk-emp ptk-emp--xs">
                  <img src="/images/employee/${emp.image || ""}"
                      alt=""
                      width="16"
                      height="16"
                      class="ptk-ava">
                  ${hl(name || emp.lastname || "", q)}
                </span>
              `;
            })
            .join("");

          stepsBlock = `
            <div class="ptk-steps">
              <div class="small text-muted mb-1">Verantwortliche</div>
              <div class="ptk-step">
                <div class="ptk-step-emps">${emps}</div>
              </div>
            </div>
          `;
        }

        return `
          <article class="ptk-card"
                  draggable="true"
                  data-ptk-card
                  data-id="${task.id}"
                  data-status="${status}">
            <i class="ptk-card-color" style="background:${color}"></i>

            <div style="position:absolute; right:8px; top:8px; display:flex; gap:4px; z-index:2">
              <button type="button"
                      class="btn-icon"
                      title="Bearbeiten"
                      data-ptk-edit="${task.id}">
                <i class="feather icon-edit-2"></i>
              </button>
              <button type="button"
                      class="btn-icon"
                      title="Löschen"
                      data-ptk-del="${task.id}">
                <i class="feather icon-trash-2"></i>
              </button>
            </div>

            <div class="ptk-card-main">
              <div class="ptk-card-title">${hl(title, q)}</div>
              ${descBlock}
              ${stepsBlock}
            </div>
          </article>
        `;
      },

      // --------------- DnD ---------------

      bindDND() {
        qsa("[data-ptk-dropzone]").forEach((zone) => {
          if (zone._ptkDnd) return;
          zone._ptkDnd = true;

          zone.addEventListener("dragover", (e) => {
            e.preventDefault();
            zone.classList.add("ptk-over");
          });

          zone.addEventListener("dragleave", () => {
            zone.classList.remove("ptk-over");
          });

          zone.addEventListener("drop", async (e) => {
            e.preventDefault();
            zone.classList.remove("ptk-over");

            const id = e.dataTransfer.getData("text/ptk-id");
            const from = e.dataTransfer.getData("text/ptk-from");
            const to = zone.getAttribute("data-ptk-dropzone");

            if (!id || !to || from === to) return;

            const card = qs(`[data-ptk-card][data-id="${id}"]`);
            if (card) {
              zone.appendChild(card);
              card.dataset.status = to;
            }

            try {
              await this.updateStatus(id, to);
            } catch (err) {
              Swal.fire("Fehler", err.message || "Status konnte nicht gespeichert werden.", "error");
              this.loadTasks();
            }
          });
        });
      },

      bindCardEvents() {
        qsa("[data-ptk-card]").forEach((card) => {
          if (card._ptkBound) return;
          card._ptkBound = true;

          card.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData("text/ptk-id", card.dataset.id);
            e.dataTransfer.setData("text/ptk-from", card.dataset.status || "open");
            e.dataTransfer.effectAllowed = "move";
            setTimeout(() => card.classList.add("ptk-dragging"), 0);
          });

          card.addEventListener("dragend", () => {
            card.classList.remove("ptk-dragging");
          });
        });

        this.recountColumns();
      },

      recountColumns() {
        for (const key of STATUS_ORDER) {
          const n = qsa(
            `[data-ptk-dropzone="${key}"] > [data-ptk-card]`
          ).length;
          const badge = qs(`[data-ptk-count="${key}"]`);
          if (badge) badge.textContent = String(n);
        }
      },

      // --------------- CRUD ---------------

      async updateStatus(id, status) {
        const url = APP.endpoints.personalTasksUpdate(id);
        const resp = await fetch(url, {
          method: "PUT",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": CSRF(),
          },
          body: JSON.stringify({ task_status: status }),
        });

        const json = await resp.json().catch(() => ({}));
        if (!resp.ok || json?.success === false) {
          throw new Error(json?.message || "Status konnte nicht gespeichert werden.");
        }

        const t = this._tasks.find((x) => String(x.id) === String(id));
        if (t) t.task_status = status;

        this.recountColumns();
        this.updateCardBadge();
      },

      async submitForm(ev) {
        ev.preventDefault();

        const title = qs("#pt-task_title")?.value.trim() || "";
        if (!title) {
          Swal.fire("Fehler", "Aufgabentitel ist erforderlich.", "error");
          return;
        }

        const customerId = Number(qs("#pt-customer_id")?.value || 0);
        const alternativeId = Number(qs("#pt-alternative_id")?.value || 0);
        const productIdRaw = qs("#pt-product_id")?.value || "";

        if (!customerId || !alternativeId) {
          Swal.fire("Fehler", "Der Kontext (Kunde/Alternative) fehlt.", "error");
          return;
        }

        const payload = {
          customer_id: customerId,
          alternative_id: alternativeId,
          product_id: productIdRaw ? Number(productIdRaw) : null,

          task_title: title,
          description: qs("#pt-description")?.value.trim() || null,
          start_date: qs("#pt-start_date")?.value || null,
          due_date: qs("#pt-due_date")?.value || null,
          due_time: qs("#pt-due_time")?.value || null,
          priority: qs("#pt-priority")?.value || "normal",
          color: qs("#pt-color")?.value || "#8fc73e",
        };

        if (window.jQuery) {
          const emps = jQuery("#pt-employee_ids").val() || [];
          payload.employee_ids = emps;
        }

        const isEdit = !!this._editingId;
        const url = isEdit
          ? APP.endpoints.personalTasksUpdate(this._editingId)
          : APP.endpoints.personalTasksStore;
        const method = isEdit ? "PUT" : "POST";

        try {
          const resp = await fetch(url, {
            method,
            credentials: "same-origin",
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              "X-CSRF-TOKEN": CSRF(),
            },
            body: JSON.stringify(payload),
          });

          const json = await resp.json().catch(() => ({}));
          if (!resp.ok || json?.success === false) {
            throw new Error(json?.message || "Aufgabe konnte nicht gespeichert werden.");
          }

          const saved =
            json.task ||
            json.data ||
            json;

          if (isEdit) {
            const idx = this._tasks.findIndex(
              (t) => String(t.id) === String(this._editingId)
            );
            if (idx !== -1) this._tasks[idx] = saved;
          } else {
            this._tasks.push(saved);
          }

          this._editingId = null;
          if (qs("#pt-form")) qs("#pt-form").reset();
          if (window.jQuery) {
            jQuery("#pt-employee_ids").val(null).trigger("change");
          }

          this.renderFiltered();
          this.updateCardBadge();

          Swal.fire(
            "Gespeichert",
            isEdit ? "Aufgabe aktualisiert." : "Aufgabe angelegt.",
            "success"
          );
        } catch (err) {
          Swal.fire("Fehler", err.message || "Serverfehler", "error");
        }
      },

      fillForm(id) {
        const task = this._tasks.find((t) => String(t.id) === String(id));
        if (!task) return;

        this._editingId = id;

        const set = (sel, val) => {
          const el = qs(sel);
          if (el) el.value = val ?? "";
        };

        set("#pt-task_title", task.task_title || "");
        set("#pt-description", task.description || "");
        set("#pt-start_date", task.start_date || "");
        set("#pt-due_date", task.due_date || "");
        set("#pt-due_time", task.due_time || "");
        set("#pt-priority", task.priority || "normal");
        set("#pt-color", task.color || "#8fc73e");

        if (window.jQuery) {
          const ids = Array.isArray(task.employees)
            ? task.employees.map((e) => e.id)
            : [];
          jQuery("#pt-employee_ids").val(ids).trigger("change");
        }
      },

      async deleteTask(id) {
        const ok = await Swal.fire({
          title: "Aufgabe löschen?",
          text: "Dieser Vorgang kann nicht rückgängig gemacht werden.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Ja, löschen",
        });
        if (!ok.isConfirmed) return;

        try {
          const resp = await fetch(APP.endpoints.personalTasksDestroy(id), {
            method: "DELETE",
            credentials: "same-origin",
            headers: {
              "X-Requested-With": "XMLHttpRequest",
              "X-CSRF-TOKEN": CSRF(),
            },
          });

          const json = await resp.json().catch(() => ({}));
          if (!resp.ok || json?.success === false) {
            throw new Error(json?.message || "Löschen fehlgeschlagen.");
          }

          this._tasks = this._tasks.filter(
            (t) => String(t.id) !== String(id)
          );
          this.renderFiltered();
          this.updateCardBadge();

          Swal.fire("Gelöscht", "Aufgabe wurde gelöscht.", "success");
        } catch (err) {
          Swal.fire("Fehler", err.message || "Serverfehler", "error");
        }
      },
    };

    // ------------------------------------------------
    // Global bindings
    // ------------------------------------------------

    // Drawer open/close
    qs("#pt-backdrop")?.addEventListener("click", () => PTK.hide());
    qsa("[data-pt-close]").forEach((btn) =>
      btn.addEventListener("click", () => PTK.hide())
    );

    // Form submit
    qs("#pt-form")?.addEventListener("submit", (ev) => PTK.submitForm(ev));

    // Edit / delete buttons (delegated)
    document.addEventListener("click", (e) => {
      const del = e.target.closest("[data-ptk-del]");
      if (del) {
        e.preventDefault();
        const id = del.getAttribute("data-ptk-del");
        if (id) PTK.deleteTask(id);
        return;
      }

      const edit = e.target.closest("[data-ptk-edit]");
      if (edit) {
        e.preventDefault();
        const id = edit.getAttribute("data-ptk-edit");
        if (id) PTK.fillForm(id);
      }
    });

    // Custom event fallback: open-personal-tasks
    document.addEventListener("open-personal-tasks", (e) => {
      const d = e.detail || {};
      PTK.open(d.customerId, d.alternativeId, d.productId, d.title);
    });

    // Export to global
    window.PersonalTasksUI = PTK;
  })();
</script>

 <script>
    (() => {
      "use strict";

      /* --------------------------------------------------------------------------
      * Team Hover Popover (fixed)
      * - Reads assigned-by / assigned-at from:
      *    1) avatar element itself (img/li/span with data-emp-id)
      *    2) closest <li> wrapper (even if LI does NOT have data-emp-id)
      *    3) closest parent element
      * - Shows Stage (German) from nearest .card or tr.list-row-item dataset.stage
      *   using window.LeadUI.APP.stageNames when available
      * ------------------------------------------------------------------------ */

      const EMP_SRC = "/images/employee";
      const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];

      const byId = new Map(
        employees
          .map((e) => {
            const id = Number(e?.id);
            return Number.isFinite(id) ? [id, e] : null;
          })
          .filter(Boolean)
      );

      const fallbackStageNames = {
        lead: "Lead",
        offer: "Verkauf",
        deal: "Auftrag",
        project: "Montage",
        completed: "Abschluss",
        archive: "Archiv",
        junk: "Junk",
      };

      const stageNames =
        (window.LeadUI?.APP?.stageNames && typeof window.LeadUI.APP.stageNames === "object"
          ? window.LeadUI.APP.stageNames
          : fallbackStageNames);

      let pop = null;
      let anchor = null;
      let hideTimer = null;

      const esc = (s) =>
        String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

      const pad2 = (n) => String(n).padStart(2, "0");

      const parseAnyDate = (raw) => {
        const s = String(raw || "").trim();
        if (!s) return null;

        // ISO works directly
        let d = new Date(s);
        if (!Number.isNaN(d.getTime())) return d;

        // "YYYY-MM-DD HH:mm:ss" -> "YYYY-MM-DDTHH:mm:ss"
        if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/.test(s)) {
          d = new Date(s.replace(" ", "T"));
          if (!Number.isNaN(d.getTime())) return d;
        }

        return null;
      };

      const fmtDE = (raw) => {
        const d = parseAnyDate(raw);
        if (!d) return "–";
        try {
          return d.toLocaleString("de-DE");
        } catch {
          return `${pad2(d.getDate())}.${pad2(d.getMonth() + 1)}.${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
        }
      };

      function ensurePop() {
        if (pop) return pop;

        pop = document.createElement("div");
        pop.className = "team-popover";
        pop.setAttribute("role", "dialog");
        pop.setAttribute("aria-label", "Team");

        pop.innerHTML = `
          <div class="team-popover__title">
            <div class="t1">Team</div>
            <div class="t2" data-subline></div>
          </div>
          <div class="team-popover__list" data-list></div>
        `;

        document.body.appendChild(pop);

        pop.addEventListener("mouseenter", () => hideTimer && clearTimeout(hideTimer));
        pop.addEventListener("mouseleave", () => scheduleHide());

        return pop;
      }

      function readAttrChain(node, keyKebab, keyDataset) {
        if (!node) return "";
        const direct = node.getAttribute?.(keyKebab) || node.dataset?.[keyDataset] || "";
        return String(direct || "").trim();
      }

      function getContextStage(ul) {
        const ctx = ul.closest?.(".card, tr.list-row-item") || null;
        const raw = String(ctx?.dataset?.stage || "").trim().toLowerCase();
        if (!raw) return "";
        return stageNames[raw] || raw;
      }

      function collectAvatars(ul) {
        // Keep DOM order: select anything with data-emp-id (img or li etc.)
        const nodes = Array.from(ul.querySelectorAll("[data-emp-id]"));

        const out = [];
        for (const n of nodes) {
          const id = Number(n.getAttribute("data-emp-id"));
          if (!Number.isFinite(id) || id <= 0) continue;

          // IMPORTANT FIX:
          // Your markup usually has data-emp-id on IMG but assigned-by/date on LI.
          // So we read from: n, closest LI, and parent.
          const li = n.closest("li");
          const parent = n.parentElement;

          const assignedBy =
            readAttrChain(n, "data-assigned-by", "assignedBy") ||
            readAttrChain(li, "data-assigned-by", "assignedBy") ||
            readAttrChain(parent, "data-assigned-by", "assignedBy");

          const assignedAt =
            readAttrChain(n, "data-assigned-at", "assignedAt") ||
            readAttrChain(li, "data-assigned-at", "assignedAt") ||
            readAttrChain(parent, "data-assigned-at", "assignedAt");

          const position =
            readAttrChain(n, "data-position", "position") ||
            readAttrChain(li, "data-position", "position") ||
            readAttrChain(parent, "data-position", "position");

          out.push({ id, assignedBy, assignedAt, position });
        }
        return out;
      }

      function uniqueById(list) {
        const seen = new Set();
        const out = [];
        for (const it of list) {
          if (seen.has(it.id)) continue;
          seen.add(it.id);
          out.push(it);
        }
        return out;
      }

      // 1) Your buildRow is OK now (it WILL show phase) ✅
        // The missing part is: you must PASS stage / stageLabel into buildRow
        // from the DOM (data-* attrs) OR from the API payload (team_assignments).

        function buildRow({ id, assignedBy, assignedAt, position, stage, stageLabel }) {
          const emp = byId.get(Number(id)) || null;

          const name = emp ? `${emp.lastname || ""} ${emp.name || ""}`.trim() : `#${id}`;
          const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;

          const role =
            (position && String(position).trim()) ||
            (emp?.position ? String(emp.position) : "") ||
            (emp?.role ? String(emp.role) : "") ||
            "Mitarbeiter";

          const by = (assignedBy && String(assignedBy).trim()) || "–";
          const when = fmtDE(assignedAt);

          const stageText =
            (stageLabel && String(stageLabel).trim()) ||
            (stage && String(stage).trim()) ||
            "–";

          return `
            <div class="team-popover__item">
              <img class="team-popover__avatar" src="${esc(img)}" alt="${esc(name)}">
              <div style="min-width:0;">
                <div class="team-popover__name">${esc(name)}</div>
                <div class="team-popover__meta">${esc(role)}</div>

                <div class="team-popover__meta">
                  <strong>Phase:</strong> ${esc(stageText)}
                </div>

                <div class="team-popover__meta">
                  <strong>Zugewiesen von:</strong> ${esc(by)}
                  <span style="padding:0 6px;">•</span>
                  <strong><i class="feather icon-calendar"></i></strong> ${esc(when)}
                </div>
              </div>
            </div>
          `;
        }

        // 2) Build popover rows from EACH avatar <li> dataset (this is what makes Phase show)
        function rowsFromTeamEl(teamEl) {
          const lis = Array.from(teamEl.querySelectorAll('li[data-emp-id]'));
          return lis.map((li) => ({
            id: li.dataset.empId,
            assignedBy: li.dataset.assignedBy,     // must exist on li
            assignedAt: li.dataset.assignedAt,     // must exist on li
            position: li.dataset.position,
            stage: li.dataset.stage,              // must exist on li
            stageLabel: li.dataset.stageLabel,    // must exist on li (German label)
          }));
        }

        // Example usage inside your hover/open logic:
        function renderTeamPopover(teamEl, popoverEl) {
          const rows = rowsFromTeamEl(teamEl);
          popoverEl.innerHTML = rows.map(buildRow).join("") || `<div class="team-popover__empty">–</div>`;
          if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
        }



      function renderFor(ul) {
        const p = ensurePop();
        const listEl = p.querySelector("[data-list]");
        const subEl = p.querySelector("[data-subline]");

        const stageLabel = getContextStage(ul);
        const avatars = uniqueById(collectAvatars(ul));

        const countText = `${avatars.length} Mitglied${avatars.length === 1 ? "" : "er"}`;
        subEl.textContent = stageLabel ? `${countText} • Phase: ${stageLabel}` : countText;

        if (!avatars.length) {
          listEl.innerHTML = `
            <div class="team-popover__item">
              <div style="min-width:0;">
                <div class="team-popover__name">Kein Team</div>
                <div class="team-popover__meta">—</div>
              </div>
            </div>
          `;
          return;
        }

        listEl.innerHTML = avatars.map(buildRow).join("");
      }

      function placeNear(el) {
        const p = ensurePop();
        const r = el.getBoundingClientRect();

        const pw = p.offsetWidth || 320;
        const ph = p.offsetHeight || 220;

        const pad = 12;
        const vw = window.innerWidth;
        const vh = window.innerHeight;

        let left = r.left + r.width / 2 - pw / 2;
        let top = r.top - ph - 10;

        left = Math.max(pad, Math.min(left, vw - pw - pad));
        if (top < pad) top = r.bottom + 10;
        if (top + ph > vh - pad) top = Math.max(pad, vh - ph - pad);

        p.style.left = `${Math.round(left)}px`;
        p.style.top = `${Math.round(top)}px`;
      }

      function openFor(ul) {
        if (!ul) return;
        hideTimer && clearTimeout(hideTimer);
        anchor = ul;

        renderFor(ul);
        placeNear(ul);

        ensurePop().classList.add("is-open");
      }

      function closeNow() {
        if (!pop) return;
        pop.classList.remove("is-open");
        anchor = null;
      }

      function scheduleHide() {
        hideTimer && clearTimeout(hideTimer);
        hideTimer = setTimeout(closeNow, 120);
      }

      function getTeamTarget(node) {
        return node?.closest ? node.closest("ul[data-team-hover]") : null;
      }

      document.addEventListener(
        "mouseover",
        (e) => {
          const ul = getTeamTarget(e.target);
          if (!ul) return;

          const from = e.relatedTarget;
          if (from && ul.contains(from)) return;

          if (anchor === ul && pop?.classList.contains("is-open")) return;
          openFor(ul);
        },
        true
      );

      document.addEventListener(
        "mouseout",
        (e) => {
          if (!anchor) return;

          const to = e.relatedTarget;
          if (to && (anchor.contains(to) || (pop && pop.contains(to)))) return;

          scheduleHide();
        },
        true
      );

      window.addEventListener(
        "scroll",
        () => {
          if (anchor && pop?.classList.contains("is-open")) placeNear(anchor);
        },
        true
      );

      window.addEventListener("resize", () => {
        if (anchor && pop?.classList.contains("is-open")) placeNear(anchor);
      });

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeNow();
      });
    })();
</script>
 
<!-- Aufgabe Script  -->
<script>
(function () {
  "use strict";

  const root  = window.LeadUI || {};
  const APP   = root.APP || {};
  const net   = root.net || {};
  const utils = root.utils || {};

  const qs  = (s, el=document) => el.querySelector(s);
  const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));

  const esc = (s) => String(s ?? "").replace(/[&<>"]/g, (m) => ({ "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;" }[m]));
  const dateOnly = (v) => (v ? String(v).slice(0, 10) : "");
  const timeOnly = (v) => (v ? String(v).slice(0, 8) : "");
  const isZeroTime = (v) => !v || String(v).startsWith("00:00");
  const addDays = (yyyy_mm_dd, days) => {
    if (!yyyy_mm_dd) return "";
    const d = new Date(yyyy_mm_dd + "T00:00:00");
    d.setDate(d.getDate() + (days || 0));
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,"0")}-${String(d.getDate()).padStart(2,"0")}`;
  };

  const EMP_IMG_BASE = (window.__EMP_IMG_BASE || "/images/employee").replace(/\/+$/, "");
  const employeeImageUrl = (img) => {
    if (!img) return "";
    if (/^https?:\/\//i.test(img)) return img;
    return EMP_IMG_BASE + "/" + String(img).replace(/^\/+/, "");
  };

  const EMP_ENDPOINT = APP?.endpoints?.getAllEmployees || "/getAllEmployees";

  function resolveEmployeeCalendarUrl(empId) {
    if (APP?.endpoints?.employeeCalendar && typeof APP.endpoints.employeeCalendar === "function") {
      return APP.endpoints.employeeCalendar(empId);
    }
    const sel = qs("#ap-emp-filter");
    const base = sel?.dataset?.empCalBase || "";
    if (base) return String(base).replace(/\/+$/, "") + "/" + encodeURIComponent(empId);
    return "/get_employee_calendar/" + encodeURIComponent(empId);
  }

  function safeJsonFetch(url) {
    return fetch(url, {
      method: "GET",
      credentials: "same-origin",
      headers: { "Accept": "application/json" }
    }).then(async (r) => {
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    });
  }

  // small styles
  (function injectStyles() {
    if (qs("#ap-unified-style")) return;
    const st = document.createElement("style");
    st.id = "ap-unified-style";
    st.textContent = `
      .ap-loading-mask{position:absolute;inset:0;background:rgba(255,255,255,.72);display:flex;align-items:center;justify-content:center;z-index:50;border-radius:6px;}
      .ap-loading-mask .spin{width:18px;height:18px;border-radius:999px;border:2px solid rgba(0,0,0,.12);border-top-color:rgba(0,0,0,.55);animation:apspin 1s linear infinite;}
      @keyframes apspin{to{transform:rotate(360deg)}}
      .ap-creator-line{font-size:11px;opacity:.85;margin-top:2px;display:flex;gap:6px;align-items:center;}
      .ap-emp-avatars{display:flex;gap:6px;flex-wrap:wrap;align-items:center;margin-top:4px;}
      .ap-emp-pill{display:inline-flex;align-items:center;gap:6px;padding:2px 8px 2px 2px;border-radius:999px;border:1px solid rgba(0,0,0,.10);background:#fff;}
      .ap-emp-pill .av{width:22px;height:22px;border-radius:999px;overflow:hidden;border:1px solid rgba(0,0,0,.12);display:inline-flex;align-items:center;justify-content:center;background:#f8f9fa;}
      .ap-emp-pill .av img{width:100%;height:100%;object-fit:cover;display:block;}
      .ap-emp-pill .fb{font-size:10px;font-weight:900;opacity:.75;}
      .ap-emp-pill .nm{font-size:12px;font-weight:700;}
      .ap-source-badge{font-size:10px;font-weight:900;padding:2px 8px;border-radius:999px;border:1px solid rgba(0,0,0,.12);opacity:.85;}
    `;
    document.head.appendChild(st);
  })();

  const UI = {
    _ctx: {},
    _calendar: null,

    _employees: [],
    _selectedEmpId: "",

    // mode:
    // - "customer" => load customer appointmentsIndex
    // - "employee" => load ONLY employee calendar endpoint
    _mode: "customer",

    _customerAppointments: [],
    _employeeEvents: [],
    _rendered: [],

    open(customerId, alternativeId, productId, title, contact) {
      this._ctx = { customerId: customerId || "", alternativeId: alternativeId || "", productId: productId || "" };

      const titleEl = qs("#ap-title");
      if (titleEl) titleEl.textContent = title || "Termine";

      this.resetForm();
      this.prefillFromContact(contact || {});

      qs("#ap-backdrop")?.classList.add("show");
      qs("#ap-drawer")?.classList.add("open");
      document.body.style.overflow = "hidden";

      this.switchTab("calendar");
      this.switchView("calendar");

      this.ensureEmployeesLoaded()
        .then(() => {
          this.initSelect2ForFormEmployees();
          return this.onEmpFilterChanged(true);
        })
        .catch(() => this.onEmpFilterChanged(true));
    },

    hide() {
      qs("#ap-backdrop")?.classList.remove("show");
      qs("#ap-drawer")?.classList.remove("open");
      document.body.style.overflow = "";
    },

    switchTab(tab) {
      qsa(".ap-tab-link").forEach(b => b.classList.toggle("active", b.dataset.tab === tab));
      qsa(".ap-tab-content").forEach(c => c.classList.remove("active"));
      const target = qs(tab === "calendar" ? "#ap-tab-calendar" : "#ap-tab-form");
      if (target) target.classList.add("active");
      if (tab === "calendar") setTimeout(() => { this.initCalendar(); this.refreshCalendar(); }, 150);
    },

    switchView(viewName) {
      qsa("[data-view]").forEach(btn => {
        const active = btn.dataset.view === viewName;
        btn.classList.toggle("active", active);
        btn.classList.toggle("btn-outline-primary", active);
        btn.classList.toggle("btn-outline-secondary", !active);
      });

      const calWrap = qs("#ap-calendar-wrap");
      const cardWrap = qs("#ap-card-view");
      if (calWrap) calWrap.style.display = viewName === "calendar" ? "block" : "none";
      if (cardWrap) cardWrap.style.display = viewName === "cards" ? "grid" : "none";
      if (viewName === "calendar") setTimeout(() => this.refreshCalendar(), 120);
    },

    showLoading(on) {
      const wrap = qs("#ap-calendar-wrap");
      if (!wrap) return;

      let mask = qs("#ap-loading-mask");
      if (on) {
        if (!mask) {
          mask = document.createElement("div");
          mask.id = "ap-loading-mask";
          mask.className = "ap-loading-mask";
          mask.innerHTML = `<div class="spin"></div>`;
          wrap.style.position = "relative";
          wrap.appendChild(mask);
        }
      } else {
        mask?.remove();
      }
    },

    /* =========================
     * EMPLOYEES (filter + form select2)
     * =======================*/
    async ensureEmployeesLoaded() {
      const sel = qs("#ap-emp-filter");
      if (!sel) return;
      if (this._employees.length) return;

      const res = await net.safeFetchJSON(EMP_ENDPOINT, { retries: 0 });
      const rows = Array.isArray(res) ? res : (Array.isArray(res?.employees) ? res.employees : []);

      this._employees = rows.map(e => ({
        id: String(e.emp_id ?? e.id ?? ""),
        name: e.name || "",
        lastname: e.lastname || "",
        image: e.image || null
      })).filter(e => e.id);

      // IMPORTANT: empty option means "customer calendar"
      sel.innerHTML =
        `<option value="">Aktueller Kunde (Termine)</option>` +
        this._employees
          .slice()
          .sort((a,b) => (a.lastname||"").localeCompare(b.lastname||""))
          .map(e => `<option value="${esc(e.id)}">${esc((e.lastname + " " + e.name).trim())}</option>`)
          .join("");

      // If filter select2 exists, attach hooks
      if (window.jQuery) {
        const $sel = window.jQuery(sel);
        if ($sel.length && !$sel.data("select2")) {
          $sel.select2({
            placeholder: "Aktueller Kunde (Termine)",
            allowClear: true,
            dropdownParent: window.jQuery("#ap-drawer"),
            width: "100%"
          });
          $sel.on("select2:select select2:clear", () => this.onEmpFilterChanged(false));
        }
      }
    },

    initSelect2ForFormEmployees() {
      if (!window.jQuery) return;
      const el = qs("#ap-employee_ids");
      if (!el) return;

      const $el = window.jQuery(el);
      if ($el.data("select2")) return;

      $el.select2({
        placeholder: "Mitarbeiter wählen…",
        dropdownParent: window.jQuery("#ap-drawer"),
        width: "100%"
      });
    },

    getEmpFilterValue() {
      const sel = qs("#ap-emp-filter");
      if (!sel) return "";
      return String(sel.value || "");
    },

    async onEmpFilterChanged(isFirstOpen) {
      const empId = this.getEmpFilterValue();
      this._selectedEmpId = empId;

      // Always return to calendar view
      this.switchTab("calendar");
      this.switchView("calendar");
      this.initCalendar();

      if (empId) {
        // EMPLOYEE MODE => only employee calendar
        this._mode = "employee";
        await this.loadEmployeeCalendar(empId);
      } else {
        // CUSTOMER MODE => customer appointments
        this._mode = "customer";
        await this.loadCustomerAppointments();
      }

      this.renderCalendarEvents();
      this.renderCardList();
      this.populateJumpDropdown();
      this.updateCount();

      setTimeout(() => this.refreshCalendar(), 160);
    },

    /* =========================
     * DATA LOADERS
     * =======================*/
    async loadCustomerAppointments() {
      const { customerId, alternativeId, productId } = this._ctx;
      if (!customerId || !APP?.endpoints?.appointmentsIndex) {
        this._customerAppointments = [];
        this._rendered = [];
        return;
      }

      const url =
        `${APP.endpoints.appointmentsIndex}?customer_id=${encodeURIComponent(customerId)}` +
        (alternativeId ? `&alternative_id=${encodeURIComponent(alternativeId)}` : "") +
        (productId ? `&product_id=${encodeURIComponent(productId)}` : "");

      this.showLoading(true);
      try {
        const res = await net.safeFetchJSON(url, { retries: 0 });
        const list = Array.isArray(res?.appointments) ? res.appointments : (Array.isArray(res) ? res : []);

        this._customerAppointments = list.map(a => ({
          ...a,
          __creator_label:
            (typeof a.created_by === "string" ? a.created_by : null) ||
            a.created_by_name ||
            a.creator_name ||
            (a.created_by ? `User #${a.created_by}` : "")
        }));

        this._rendered = this._customerAppointments.map(a => this.mapLeadAppointment(a));
      } catch (e) {
        this._customerAppointments = [];
        this._rendered = [];
        window.Swal?.fire("Fehler", "Kunden-Termine konnten nicht geladen werden.", "error");
      } finally {
        this.showLoading(false);
      }
    },

    async loadEmployeeCalendar(empId) {
      const url = resolveEmployeeCalendarUrl(empId);

      this.showLoading(true);
      try {
        const json = await safeJsonFetch(url);
        const rows = Array.isArray(json?.data) ? json.data : [];

        this._employeeEvents = rows;
        this._rendered = rows.map(r => this.mapEmployeeEvent(r));
      } catch (e) {
        this._employeeEvents = [];
        this._rendered = [];
        window.Swal?.fire("Fehler", "Mitarbeiter-Kalender konnte nicht geladen werden.", "error");
      } finally {
        this.showLoading(false);
      }
    },

    /* =========================
     * NORMALIZERS
     * =======================*/
    mapLeadAppointment(a) {
      const sd = dateOnly(a.start_date);
      const ed = dateOnly(a.end_date) || sd;
      const st = timeOnly(a.start_time);
      const et = timeOnly(a.end_time);

      const allDay = !st || isZeroTime(st);

      let start, end;
      if (allDay) {
        start = sd;
        end = addDays(ed, 1);
      } else {
        start = `${sd}T${st || "00:00:00"}`;
        end   = `${ed}T${et || st || "23:59:59"}`;
      }

      const emps = Array.isArray(a.employees) ? a.employees : [];
      const employees = emps.map(e => {
        const full = ((e.lastname ? e.lastname + " " : "") + (e.name || "")).trim();
        return { id: String(e.id), full, initials: (e.lastname || e.name || "?").slice(0,2).toUpperCase(), image: e.image ? employeeImageUrl(e.image) : "" };
      });

      return {
        _source: "customer",
        _raw: a,
        id: `lead-${a.id}`,
        title: a.name || "Termin",
        start, end, allDay,
        color: a.color || "#74b2d4",
        creator_label: a.__creator_label || "",
        description: a.note || "",
        type: "appointment",
        employees
      };
    },

    mapEmployeeEvent(r) {
      const sd = dateOnly(r.start_date);
      const ed = dateOnly(r.end_date) || sd;
      const st = timeOnly(r.start_time);
      const et = timeOnly(r.end_time);

      const allDay = isZeroTime(st) && isZeroTime(et);

      let start, end;
      if (allDay) {
        start = sd;
        end = addDays(ed, 1);
      } else {
        start = `${sd}T${st || "00:00:00"}`;
        end   = `${ed}T${et || "23:59:59"}`;
      }

      const emps = Array.isArray(r.employees) ? r.employees : [];
      const employees = emps.map(e => {
        const full = ((e.lastname ? e.lastname + " " : "") + (e.name || "")).trim();
        return { id: String(e.employee_id), full, initials: (e.lastname || e.name || "?").slice(0,2).toUpperCase(), image: e.image ? employeeImageUrl(e.image) : "" };
      });

      return {
        _source: "employee",
        _raw: r,
        id: `${r.type || "event"}-${r.id}`,
        title: r.title || "Eintrag",
        start, end, allDay,
        color: r.taskColor || "#74b2d4",
        creator_label: "",
        description: r.description || "",
        type: r.type || "event",
        employees
      };
    },

    /* =========================
     * CALENDAR
     * =======================*/
    initCalendar() {
      const calEl = qs("#ap-fullcalendar");
      if (!calEl || !window.FullCalendar) return;
      if (this._calendar) return;

      this._calendar = new FullCalendar.Calendar(calEl, {
        locale: "de",
        initialView: "dayGridMonth",
        headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek,listWeek" },
        height: "100%",
        navLinks: true,
        editable: false,
        dayMaxEvents: true,
        events: [],

        eventContent: (arg) => {
          const p = arg.event.extendedProps || {};
          const title = esc(arg.event.title || "");
          const creator = p.creator_label ? `<div class="ap-creator-line"><i class="feather icon-user" style="font-size:12px;"></i><span>${esc(p.creator_label)}</span></div>` : "";

          const emps = Array.isArray(p.employees) ? p.employees : [];
          const avatars = emps.length ? `
            <div class="ap-emp-avatars">
              ${emps.slice(0,4).map(e => {
                const nm = esc(e.full || "");
                const img = e.image ? esc(e.image) : "";
                const fb  = esc((e.initials || "?").slice(0,2));
                return `<span title="${nm}" style="width:18px;height:18px;border-radius:999px;overflow:hidden;border:1px solid rgba(0,0,0,.12);display:inline-flex;align-items:center;justify-content:center;background:#fff;">
                  ${img ? `<img src="${img}" alt="${nm}" style="width:100%;height:100%;object-fit:cover;display:block;">` : `<span style="font-size:9px;font-weight:900;opacity:.75;">${fb}</span>`}
                </span>`;
              }).join("")}
              ${emps.length > 4 ? `<span style="font-size:10px;font-weight:900;opacity:.8;">+${emps.length-4}</span>` : ""}
            </div>` : "";

          return { html: `<div><div style="font-weight:800;font-size:12px;">${title}</div>${creator}${avatars}</div>` };
        },

        dateClick: (info) => {
          // ALWAYS allow creation (even if employee is selected)
          this.resetForm();

          // preset date
          qs("#ap-start_date") && (qs("#ap-start_date").value = info.dateStr);

          // preset selected employee into appointment employees
          this.preselectEmployeeIntoForm();

          qs("#ap-form-title") && (qs("#ap-form-title").textContent =
            "Neuer Termin am " + new Date(info.dateStr + "T00:00:00").toLocaleDateString("de-DE")
          );

          this.switchTab("form");
        },

        eventClick: (info) => {
          const id = String(info.event.id || "");
          // only lead appointments are editable
          if (id.startsWith("lead-")) {
            this.fillForm(id.replace("lead-",""));
            this.switchTab("form");
          } else {
            this.switchView("cards");
          }
        }
      });

      this._calendar.render();
    },

    refreshCalendar() {
      if (!this._calendar) return;
      try { this._calendar.updateSize(); } catch(_) {}
    },

    renderCalendarEvents() {
      if (!this._calendar) return;

      const events = (this._rendered || []).map(ev => ({
        id: ev.id,
        title: ev.title,
        start: ev.start,
        end: ev.end,
        allDay: !!ev.allDay,
        backgroundColor: ev.color,
        borderColor: ev.color,
        extendedProps: {
          creator_label: ev.creator_label || "",
          employees: ev.employees || [],
          raw: ev._raw
        }
      }));

      this._calendar.removeAllEvents();
      this._calendar.addEventSource(events);

      // if employee mode, jump to first event date
      if (this._mode === "employee" && this._employeeEvents?.length) {
        const first = dateOnly(this._employeeEvents[0]?.start_date);
        if (first) this._calendar.gotoDate(first);
      }
    },

    /* =========================
     * LIST VIEW + JUMP + COUNT
     * =======================*/
    updateCount() {
      const el = qs("#ap-count");
      if (el) el.textContent = String((this._rendered || []).length);
    },

    populateJumpDropdown() {
      const sel = qs("#ap-jump");
      if (!sel) return;

      const list = (this._rendered || []).slice().sort((a,b) => String(b.start).localeCompare(String(a.start)));
      sel.innerHTML =
        `<option value="">— Termin auswählen (Springen) —</option>` +
        list.map(ev => {
          const d = String(ev.start || "").slice(0,10);
          const dateLabel = d ? d.split("-").reverse().join(".") : "";
          const timeLabel = ev.allDay ? " Ganztägig" : (" " + String(ev.start).slice(11,16));
          return `<option value="${esc(ev.id)}">${esc(dateLabel + timeLabel)} — ${esc(ev.title)}</option>`;
        }).join("");
    },

    jumpToEvent(eventId) {
      const ev = (this._rendered || []).find(x => String(x.id) === String(eventId));
      if (!ev || !this._calendar) return;

      const d = String(ev.start || "").slice(0,10);
      if (!d) return;

      this.switchView("calendar");
      this.switchTab("calendar");

      setTimeout(() => {
        this._calendar.gotoDate(d);
        if (String(ev.id).startsWith("lead-")) {
          this.fillForm(String(ev.id).replace("lead-",""));
          this.switchTab("form");
        }
      }, 160);
    },

    renderCardList() {
      const wrap = qs("#ap-card-view");
      if (!wrap) return;

      const list = (this._rendered || []);
      if (!list.length) {
        wrap.innerHTML = '<div class="text-center text-muted col-12 small my-3">Keine Einträge gefunden.</div>';
        return;
      }

      wrap.innerHTML = list.map(ev => {
        const d = String(ev.start || "").slice(0,10);
        const date = d ? d.split("-").reverse().join(".") : "";

        const time = ev.allDay
          ? "Ganztägig"
          : `${String(ev.start).slice(11,16)} – ${String(ev.end || "").slice(11,16)}`;

        const creator = ev.creator_label ? `
          <div class="small text-muted" style="margin-top:2px;">
            <i class="feather icon-user" style="font-size:12px;"></i>
            <span class="ml-1">${esc(ev.creator_label)}</span>
          </div>` : "";

        const empPills = (ev.employees || []).map(e => {
          const full = e.full || "Mitarbeiter";
          const img  = e.image || "";
          const fb   = esc((e.initials || "?").slice(0,2));
          return `
            <span class="ap-emp-pill" title="${esc(full)}">
              <span class="av">
                ${img ? `<img src="${esc(img)}" alt="${esc(full)}">` : `<span class="fb">${fb}</span>`}
              </span>
              <span class="nm">${esc(full)}</span>
            </span>`;
        }).join("");

        return `
          <article class="ap-card" style="cursor:pointer" onclick="AppointmentsUI.jumpToEvent('${esc(ev.id)}')">
            <div class="ap-color" style="background:${esc(ev.color || "#74b2d4")};"></div>
            <div class="ap-main">
              <div class="d-flex justify-content-between">
                <div class="ap-title font-weight-bold">${esc(ev.title)}</div>
                <div class="text-muted" style="font-size:10px;"><i class="feather icon-calendar"></i> ${esc(date)}</div>
              </div>
              <div class="text-muted small mb-1">${esc(time)}</div>
              ${creator}
              <div class="ap-note small text-muted mb-2" style="line-height:1.2;">${esc(ev.description || "").slice(0,110)}</div>
              <div class="ap-emp-avatars">${empPills}</div>
            </div>
          </article>
        `;
      }).join("");
    },

    /* =========================
     * FORM (create/edit lead appointments)
     * =======================*/
    preselectEmployeeIntoForm() {
      const empId = String(this._selectedEmpId || "");
      if (!empId) return;

      // ensure select2 exists
      this.initSelect2ForFormEmployees();

      if (!window.jQuery || !qs("#ap-employee_ids")) return;

      const $sel = window.jQuery("#ap-employee_ids");
      const existing = $sel.val() || [];
      if (!existing.includes(empId)) {
        $sel.val([...existing, empId]).trigger("change");
      }
    },

    resetForm() {
      const form = qs("#ap-form");
      if (!form) return;
      form.reset();

      qs("#ap-form-title") && (qs("#ap-form-title").textContent = "Neuer Termin");

      const delBtn = qs("#ap-btn-delete");
      if (delBtn) { delBtn.classList.add("d-none"); delBtn.onclick = null; }

      qs("#ap-customer_id") && (qs("#ap-customer_id").value = this._ctx.customerId);
      qs("#ap-alternative_id") && (qs("#ap-alternative_id").value = this._ctx.alternativeId);
      qs("#ap-product_id") && (qs("#ap-product_id").value = this._ctx.productId);
      qs("#ap-id") && (qs("#ap-id").value = "");

      qs("#ap-color") && (qs("#ap-color").value = "#74b2d4");

      if (window.jQuery) window.jQuery("#ap-employee_ids").val(null).trigger("change");
    },

    fillForm(id) {
      const appt = (this._customerAppointments || []).find(x => String(x.id) === String(id));
      if (!appt) return;

      this.resetForm();
      qs("#ap-form-title") && (qs("#ap-form-title").textContent = "Termin bearbeiten");

      const delBtn = qs("#ap-btn-delete");
      if (delBtn) {
        delBtn.classList.remove("d-none");
        delBtn.onclick = () => this.delete(id);
      }

      const set = (sel, val) => { const el = qs(sel); if (el) el.value = val ?? ""; };

      set("#ap-id", appt.id);
      set("#ap-name", appt.name);
      set("#ap-note", appt.note);

      set("#ap-start_date", dateOnly(appt.start_date));
      set("#ap-start_time", timeOnly(appt.start_time).slice(0,5));
      set("#ap-end_time", timeOnly(appt.end_time).slice(0,5));

      set("#ap-appointment_type", appt.appointment_type);
      set("#ap-contact_mode", appt.contact_mode);
      set("#ap-priority", appt.priority || "normal");
      set("#ap-color", appt.color || "#74b2d4");

      set("#ap-full_address", appt.full_address);
      set("#ap-street", appt.street);
      set("#ap-postcode", appt.postcode);
      set("#ap-city", appt.city);

      this.initSelect2ForFormEmployees();
      if (window.jQuery && Array.isArray(appt.employees)) {
        const ids = appt.employees.map(e => String(e.id));
        window.jQuery("#ap-employee_ids").val(ids).trigger("change");
      }
    },

    prefillFromContact(contact) {
      if (!contact) return;
      const map = {
        full_address:"#ap-full_address",
        street:"#ap-street",
        postcode:"#ap-postcode",
        city:"#ap-city",
        latitude:"#ap-latitude",
        longitude:"#ap-longitude"
      };
      for (const [k, sel] of Object.entries(map)) {
        if (contact[k]) { const el = qs(sel); if (el) el.value = contact[k]; }
      }
    },

    async submitForm(ev) {
      ev.preventDefault();

      const name = qs("#ap-name")?.value.trim();
      const startDate = qs("#ap-start_date")?.value;
      const customerId = qs("#ap-customer_id")?.value;

      if (!name || !startDate || !customerId) {
        window.Swal?.fire("Fehler", "Titel und Datum sind Pflichtfelder.", "error");
        return;
      }

      const payload = {
        customer_id: customerId,
        alternative_id: qs("#ap-alternative_id")?.value || null,
        product_id: qs("#ap-product_id")?.value || null,
        name,
        note: qs("#ap-note")?.value || null,
        start_date: startDate,
        start_time: qs("#ap-start_time")?.value || null,
        end_time: qs("#ap-end_time")?.value || null,
        appointment_type: qs("#ap-appointment_type")?.value || null,
        contact_mode: qs("#ap-contact_mode")?.value || null,
        priority: qs("#ap-priority")?.value || "normal",
        color: qs("#ap-color")?.value || "#74b2d4",
        full_address: qs("#ap-full_address")?.value || null,
        street: qs("#ap-street")?.value || null,
        postcode: qs("#ap-postcode")?.value || null,
        city: qs("#ap-city")?.value || null,
        latitude: qs("#ap-latitude")?.value || null,
        longitude: qs("#ap-longitude")?.value || null,
        employee_ids: window.jQuery ? (window.jQuery("#ap-employee_ids").val() || []) : []
      };

      const id = qs("#ap-id")?.value || "";
      const isEdit = !!id;
      const url = isEdit ? APP.endpoints.appointmentsUpdate(id) : APP.endpoints.appointmentsStore;
      const method = isEdit ? "PUT" : "POST";

      try {
        const res = await net.safeFetchJSON(url, {
          method,
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": (utils.CSRF ? utils.CSRF() : "")
          },
          body: JSON.stringify(payload)
        });

        if (res?.success !== false) {
          window.Swal?.fire("Gespeichert", isEdit ? "Termin aktualisiert." : "Termin angelegt.", "success");

          // after save: reload CUSTOMER calendar (since appointment belongs to customer context)
          await this.loadCustomerAppointments();

          // keep employee filter selection as is, but rendering should match mode:
          // - if employee selected => show employee calendar (not customer)
          // - if empty => show customer calendar
          await this.onEmpFilterChanged(false);

          this.switchTab("calendar");
        } else {
          throw new Error(res?.message || "Fehler beim Speichern.");
        }
      } catch (err) {
        window.Swal?.fire("Fehler", err.message || "Fehler beim Speichern.", "error");
      }
    },

    async delete(id) {
      const ok = window.Swal
        ? await Swal.fire({ title:"Löschen?", text:"Wirklich löschen?", icon:"warning", showCancelButton:true })
        : { isConfirmed: confirm("Wirklich löschen?") };

      if (!ok.isConfirmed) return;

      try {
        await net.safeFetchJSON(APP.endpoints.appointmentsDestroy(id), {
          method: "DELETE",
          headers: { "X-CSRF-TOKEN": (utils.CSRF ? utils.CSRF() : "") }
        });

        window.Swal?.fire("Gelöscht", "Termin entfernt.", "success");

        await this.loadCustomerAppointments();
        await this.onEmpFilterChanged(false);
        this.switchTab("calendar");
      } catch (err) {
        window.Swal?.fire("Fehler", err.message || "Fehler beim Löschen.", "error");
      }
    }
  };

  // delegated events (works with ajax-injected drawer too)
  if (!window.__AP_UNIFIED_BOUND) {
    window.__AP_UNIFIED_BOUND = true;

    document.addEventListener("click", (e) => {
      const t = e.target;
      if (t?.id === "ap-backdrop" || t?.closest?.("[data-ap-close]")) UI.hide();
    });

    document.addEventListener("click", (e) => {
      const btn = e.target?.closest?.(".ap-tab-link");
      if (btn) UI.switchTab(btn.dataset.tab);
    });

    document.addEventListener("click", (e) => {
      const btn = e.target?.closest?.("[data-view]");
      if (btn) UI.switchView(btn.dataset.view);
    });

    document.addEventListener("change", (e) => {
      if (e.target?.id === "ap-jump") {
        const v = e.target.value;
        if (v) UI.jumpToEvent(v);
        e.target.value = "";
      }
      if (e.target?.id === "ap-emp-filter") UI.onEmpFilterChanged(false);
    });

    document.addEventListener("submit", (e) => {
      if (e.target?.id === "ap-form") UI.submitForm(e);
    });

    document.addEventListener("click", (e) => {
      if (e.target?.closest?.("#ap-btn-back-to-cal")) UI.switchTab("calendar");
    });

    document.addEventListener("transitionend", (e) => {
      const drawer = qs("#ap-drawer");
      if (drawer && e.target === drawer && drawer.classList.contains("open")) {
        UI.initCalendar();
        UI.refreshCalendar();
      }
    });
  }

  document.addEventListener("open-appointments", (e) => {
    const d = e.detail || {};
    UI.open(d.customerId, d.alternativeId, d.productId, d.title, d);
  });

  window.AppointmentsUI = UI;
})();
</script>

 <!-- Termin Script -->
 <script>
(function () {
  "use strict";

  const BRANCH_COLOR_MAP = @json(
    collect($branches ?? [])->mapWithKeys(function ($b) {
      $name  = mb_strtolower(trim((string)($b->branch ?? '')));
      $color = (string)($b->color ?? '#93c21c');
      return [$name => $color];
    })->all()
  );

  const DEFAULT_COLOR = "#93c21c";
  const norm = (v) => (v ?? "").toString().trim().toLowerCase();

  function setImportant(el, prop, value) {
    if (!el) return;
    el.style.setProperty(prop, value, "important");
  }

  function pickBranchName(branchEl) {
    if (!branchEl) return "";
    const t = norm(branchEl.getAttribute("title"));
    if (t) return t;

    const nameEl = branchEl.querySelector(".kb-branch-name");
    const txt = norm(nameEl ? nameEl.textContent : branchEl.textContent);
    return txt;
  }

  function resolveColor(branchName) {
    const key = norm(branchName);
    return BRANCH_COLOR_MAP[key] || DEFAULT_COLOR;
  }

  function findCard(el) {
    // Your circle lives inside `.card`, so include that.
    return (
      el.closest(".kb-card") ||
      el.closest(".kanban-card") ||
      el.closest(".kb-item") ||
      el.closest(".card") ||
      el.closest("[data-lead-id]") ||
      el.closest("[data-id]") ||
      el.parentElement
    );
  }

  function paintCardCircle(card, color) {
    if (!card) return;

    // IMPORTANT: target product_circle specifically
    const circle =
      card.querySelector(".circle.product_circle") ||
      card.querySelector(".product_circle") ||
      card.querySelector(".circle");

    if (!circle) return;

    circle.style.setProperty("--branch-color", color);
    setImportant(circle, "background-color", color);
    setImportant(circle, "color", "#fff");
  }

  function paintBranch(branchEl) {
    const card = findCard(branchEl);
    const branchName = pickBranchName(branchEl);
    const color = resolveColor(branchName);

    // color branch label + svg
    branchEl.style.setProperty("--branch-color", color);
    setImportant(branchEl, "color", color);

    // color product circle in the same card
    paintCardCircle(card, color);
  }

  function paintCircle(circleEl) {
    // only force product circle (avoid random circles elsewhere)
    if (!circleEl.classList.contains("product_circle")) return;

    const card = findCard(circleEl);
    if (!card) return;

    const branchEl = card.querySelector(".kb-meta-item.kb-branch");
    const branchName = pickBranchName(branchEl);
    if (!branchName) return;

    const color = resolveColor(branchName);
    paintCardCircle(card, color);
  }

  function paintAll(root = document) {
    root.querySelectorAll(".kb-meta-item.kb-branch").forEach(paintBranch);
    root.querySelectorAll(".circle.product_circle, .product_circle").forEach(paintCircle);
  }

  document.addEventListener("DOMContentLoaded", () => paintAll());

  const container =
    document.querySelector("#kanban") ||
    document.querySelector(".kanban-board") ||
    document.body;

  const obs = new MutationObserver((mutations) => {
    for (const m of mutations) {
      if (!m.addedNodes) continue;
      m.addedNodes.forEach((node) => {
        if (node && node.nodeType === 1) paintAll(node);
      });
    }
  });

  obs.observe(container, { childList: true, subtree: true });

  // optional manual trigger after your own render
  window.paintBranchColors = paintAll;
})();


document.addEventListener("click", function(e) {
    // Target pagination links specifically inside the Junk pane
    const paginationLink = e.target.closest("#junk .pagination a");
    
    if (paginationLink) {
        e.preventDefault();
        
        const url = paginationLink.getAttribute("href");
        const junkPane = document.querySelector("#junk");

        // Show a loading state
        junkPane.style.opacity = '0.5';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update the content
            junkPane.innerHTML = html;
            junkPane.style.opacity = '1';
            
            // Re-initialize any specific UI elements if needed
            // e.g., if you have Tooltips or specific button styles
            if (window.feather) {
                feather.replace();
            }
            
            // Optional: Smooth scroll back to the top of the table
            junkPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(error => {
            console.error('Error loading junk pagination:', error);
            junkPane.style.opacity = '1';
        });
    }
});

</script>



@endsection

