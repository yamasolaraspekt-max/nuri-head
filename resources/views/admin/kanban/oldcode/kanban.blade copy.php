@extends('admin.layouts.app')
@section('title') PROZESS @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
/* ======= Layout: main + right action rail ======= */
.pro-layout{ display:grid; grid-template-columns:minmax(0,1fr) 56px; gap:.75rem; align-items:start; }
@media (max-width: 992px){ .pro-layout{ grid-template-columns:1fr 48px; } }
.pro-rail{ display:flex; flex-direction:column; gap:.5rem; align-items:center; position:sticky; top:84px; padding-top:.25rem; }
.rail-btn{ position:relative; width:44px; height:44px; border:none; border-radius:12px; background:#f3f4f6; color:#333; display:grid; place-items:center; box-shadow:0 1px 2px rgba(0,0,0,.08); cursor:pointer; transition:transform .12s, background .12s, box-shadow .12s; }
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
.btn-notes{ position:relative; }
.btn-notes .badge-notes{ position:absolute; top:-6px; right:-6px; min-width:18px; height:18px; line-height:18px; padding:0 4px; border-radius:10px; background:#93c21c; color:#fff; font-size:11px; font-weight:700; text-align:center; }
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
  z-index: 1050;
}

.lfm-shell {
  position: fixed;
  inset: 5% 8%;
  max-width: 1200px;
  margin: 0 auto;
  background: #edededff;
  color: #484848ff;
  border-radius: 18px;
  box-shadow: 0 30px 80px rgba(15, 23, 42, 0.7);
  display: flex;
  flex-direction: column;
  z-index: 1060;
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
                        <a class="nav-link" id="archive-tab" data-toggle="tab" href="#archive" role="tab" aria-selected="false">
                          <i class="feather icon-archive tab-icon"></i> Archiv
                          <span class="badge badge-secondary ml-1" id="tabCountArchive">{{ $tabCounts['archive'] ?? 0 }}</span>
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

                      <li class="nav-item">
                          <a class="nav-link" id="investment-tab" data-toggle="tab" href="#investment" role="tab" aria-selected="false">
                            <i class="feather icon-bar-chart-2 tab-icon"></i> Kundenwert
                            <span class="badge badge-secondary ml-1" id="tabCountInvestment">0</span>
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
                          <table class="table table-striped table-bordered align-middle">
                            <thead>
                              <tr>
                                <th class="sortable" data-sort="created_at">Datum <i class="sort-icon feather icon-chevron-up"></i></th>
                                <th class="sortable" data-sort="customer_lastname">Kunde <i class="sort-icon feather icon-chevron-up"></i></th>
                                <th class="sortable" data-sort="city">Ort <i class="sort-icon feather icon-chevron-up"></i></th>
                                <th>Produkt</th>
                                <th>Mitarbeiter</th>
                                <th>Status</th>
                                <th>Phase</th>
                                <th>Aktionen</th>
                              </tr>
                            </thead>
                            <tbody id="kanbanTableBody">
                              <tr><td colspan="8" class="text-center text-muted">Lade Daten…</td></tr>
                            </tbody>
                          </table>
                          <div id="listPagination" class="d-flex justify-content-center py-2"></div>
                        </div>
                      </div>

                      {{-- Archive (server-rendered partial) --}}
                      <div class="tab-pane" id="archive" aria-labelledby="archive-tab" role="tabpanel">
                        @include('admin.kanban.partials.archive', ['archive' => $archive])
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

                      <div class="tab-pane" id="investment" aria-labelledby="investment-tab" role="tabpanel"> 
                        @include('admin.kanban.partials.investment', ['investments' => [], 'overallMin' => null, 'overallMax' => null])
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
              </button>
              <button type="button"
                      class="notes-tab"
                      data-notes-tab="customerReport"
                      aria-selected="false">
                  <i class="feather icon-bar-chart-2 mr-25"></i> Kunde Report
              </button>

              <button type="button"
                      class="notes-tab"
                      data-notes-tab="report"
                      aria-selected="false">
                  <i class="feather icon-bar-chart-2 mr-25"></i> Termin Report
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
    <div id="ap-backdrop" class="notes-backdrop"></div>
    <aside id="ap-drawer" class="notes-drawer" role="dialog" aria-modal="true" aria-labelledby="ap-title">
      <div class="notes-head">
        <div class="notes-title">
          <i class="feather icon-calendar"></i>
          <span id="ap-title">Termine</span>
          <span id="ap-count" class="badge badge-secondary">0</span>
        </div>
        <div>
          <button class="btn btn-sm btn-outline-secondary" data-ap-close>
            <i class="feather icon-x"></i>
          </button>
        </div>
      </div>

      <!-- NEW WRAPPER FOR SPLIT LAYOUT -->
      <div class="ap-layout">
        <!-- LEFT: LIST -->
        <div class="ap-list-wrapper">
          <div class="notes-body" id="ap-list">
            <div class="text-center text-muted small my-2">Keine Termine geladen.</div>
          </div>
        </div>

        <!-- RIGHT: FORM -->
        <div class="ap-form-wrapper">
          <form id="ap-form" autocomplete="off">
            <input type="hidden" id="ap-id">
            <input type="hidden" id="ap-customer_id">
            <input type="hidden" id="ap-alternative_id">
            <input type="hidden" id="ap-product_id">

            <div class="form-group mb-1">
              <label class="small mb-1">Kalender-Titel*</label>
              <input type="text" class="form-control" id="ap-name" required>
            </div>

            <div class="form-group mb-1">
              <label class="small mb-1">Beschreibung</label>
              <textarea class="form-control" id="ap-note" rows="2"></textarea>
            </div>

            <div class="form-row">
              <div class="form-group col-6 mb-1">
                <label class="small mb-1">Datum*</label>
                <input type="date" class="form-control" id="ap-start_date" required>
              </div>
              <div class="form-group col-3 mb-1">
                <label class="small mb-1">Von</label>
                <input type="time" class="form-control" id="ap-start_time">
              </div>
              <div class="form-group col-3 mb-1">
                <label class="small mb-1">Bis</label>
                <input type="time" class="form-control" id="ap-end_time">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-4 mb-1">
                <label class="small mb-1">Art</label>
                <select class="form-control" id="ap-appointment_type">
                  <option value="">–</option>
                  <option value="Besichtigung">Besichtigung</option>
                  <option value="Beratung">Beratung</option>
                  <option value="Telefonat">Telefonat</option>
                  <option value="Online-Meeting">Online-Meeting</option>
                </select>
              </div>

              <div class="form-group col-4 mb-1">
                <label class="small mb-1">Kontaktweg</label>
                <select class="form-control" id="ap-contact_mode">
                  <option value="">–</option>
                  <option value="telefon">Telefon</option>
                  <option value="online">Online</option>
                  <option value="vor Ort">Vor Ort</option>
                </select>
              </div>

              <div class="form-group col-2 mb-1">
                <label class="small mb-1">Priorität</label>
                <select class="form-control" id="ap-priority">
                  <option value="normal">Normal</option>
                  <option value="high">Hoch</option>
                  <option value="low">Niedrig</option>
                </select>
              </div>

              <div class="form-group col-2 mb-1">
                <label class="small mb-1 d-block">Farbe</label>
                <input type="color" class="form-control" id="ap-color" value="#74b2d4" style="padding:0 2px;">
              </div>
            </div>

            <div class="form-group mb-2">
              <label class="small mb-1">Mitarbeiter</label>
              <select id="ap-employee_ids" class="form-control select2" multiple data-width="100%">
                @foreach ($employees as $e)
                  <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="border-top pt-2 mt-1">
              <div class="form-group mb-1 d-none">
                <label class="small mb-1">Kunde suchen</label>
                <select id="ap-customer_search" class="form-control"></select>
                <small class="text-muted d-block mt-1">
                  Auswahl aktualisiert Adresse, Kontakt und Geo-Daten.
                </small>
              </div>

              <div class="form-group mb-1">
                <label class="small mb-1">Adresse</label>
                <input type="text" class="form-control mb-1" id="ap-full_address" placeholder="Adresse">
                <div class="form-row">
                  <div class="col-7 mb-1">
                    <input type="text" id="ap-street" class="form-control" placeholder="Straße">
                  </div>
                  <div class="col-5 mb-1">
                    <input type="text" id="ap-postcode" class="form-control" placeholder="PLZ">
                  </div>
                </div>
                <input type="text" class="form-control mb-1" id="ap-city" placeholder="Ort">
              </div>

              <div class="form-row">
                <div class="form-group col-6 mb-1">
                  <label class="small mb-1">Telefon</label>
                  <input type="text" class="form-control" id="ap-phone">
                </div>
                <div class="form-group col-6 mb-1">
                  <label class="small mb-1">E-Mail</label>
                  <input type="email" class="form-control" id="ap-email">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-6 mb-1">
                  <label class="small mb-1">Breite (Lat)</label>
                  <input type="text" class="form-control" id="ap-latitude">
                </div>
                <div class="form-group col-6 mb-1">
                  <label class="small mb-1">Länge (Lon)</label>
                  <input type="text" class="form-control" id="ap-longitude">
                </div>
              </div>
            </div>

            <div class="text-right mt-2">
              <button type="submit" class="btn btn-primary">
                <i class="feather icon-save"></i> Speichern
              </button>
            </div>
          </form>
        </div>
      </div>
    </aside>
 
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
 
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script async
  src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&language=de&region=DE">
</script>

 
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

 <script>
/* =============================================================================
 * LeadUI – Core (Segment 1/2)
 * - Config, State, Storage, URL Sync
 * - Utilities + Polyfills
 * - Network layer: safeFetchJSON / postJSON
 * - Filters + Drawer
 * - Kanban renderers (diff + incremental)
 * - Notes drawer (now with Quill editor support)
 * - Archive/Junk partial loaders
 * - LiveFeed: per-card mini feed + full-screen modal (LiveFeedModal)
 * =============================================================================*/
(function () {
  "use strict";

  /* ------------------------------ Polyfills -------------------------------- */
  // requestIdleCallback shim
  // @ts-ignore
  window.requestIdleCallback ||= (cb) =>
    setTimeout(() => cb({ timeRemaining: () => 10 }), 0);

  // CSS.escape shim
  // @ts-ignore
  if (!window.CSS || !CSS.escape) {
    window.CSS = {
      ...(window.CSS || {}),
      escape: (s) => String(s).replace(/[^a-zA-Z0-9_\-]/g, "\\$&"),
    };
  }

  /* -------------------------------- Config -------------------------------- */
  const APP = {
    EMP_SRC: "{{ asset('images/employee') }}",
    endpoints: {
      kanbanSearch: "/lead/kanban/search", // GET
      listSearch: "/lead/kanban/ajax", // GET (paginated)
      changeStage: "/lead-product/change-stage", // POST /:c/:a/:p
      progress: "/lead-product/progress", // POST /:leadProductId/:state
      purge: "/lead-product/purge", // DELETE (via POST+_method)

      notesIndex: "/customer-notes", // GET ?customer_id&alternative_id&product_id
      notesStore: "/customer-notes", // POST
      notesInlineUpdate: (id) => `/customer-notes/inline-update/${id}`, // POST
      notesDestroy: (id) => `/customer-notes/delete/${id}`, // DELETE

      archive: "/lead/kanban/archive", // GET partial HTML
      junk: "/lead/kanban/junk", // GET partial HTML

      personalTasksIndex: "/personal-tasks/index", // GET ?customer_id&alternative_id&product_id
      personalTasksStore: "/personal-tasks/store", // POST
      personalTasksUpdate: (id) => `/personal-tasks/${id}/update`, // PUT
      personalTasksDestroy: (id) => `/personal-tasks/${id}/destroy`, // DELETE
      ptEmployeesSync: (id) => `/personal-tasks/${id}/employees/sync`, // POST

      ptStepsIndex: (taskId) => `/personal-tasks/${taskId}/steps`, // GET
      ptStepsStore: (taskId) => `/personal-tasks/${taskId}/steps`, // POST
      ptStepsUpdate: (stepId) => `/personal-tasks/steps/${stepId}`, // PUT
      ptStepsDestroy: (stepId) => `/personal-tasks/steps/${stepId}`, // DELETE
      ptStepsEmpSync: (stepId) =>
        `/personal-tasks/steps/${stepId}/employees/sync`, // POST

      ticketize: (id) => `/lead-product/ticketize/${id}`,
      tickets: "/lead/kanban/tickets",
      investment: "/lead/kanban/investment",

      appointmentsIndex: "appointments/index", // GET ?customer_id&alternative_id&product_id
      appointmentsStore: "appointments/store", // POST
      appointmentsUpdate: (id) => `appointments/${id}/update`, // PUT
      appointmentsDestroy: (id) => `appointments/${id}/destroy`, // DELETE
      appointmentsCustomerSearch: "appointments/customer-search", // GET ?q=

      reportsIndex: "{{ url('kanban/appointments/reports') }}",
      reportsReact: (id) =>
        "{{ url('kanban/appointments/reports') }}/" + id + "/react",
      reportsComment: (id) =>
        "{{ url('kanban/appointments/reports') }}/" + id + "/comment",
      reportsStore: (appointmentId) =>
        "{{ url('kanban/appointments') }}/" + appointmentId + "/reports",

      customerReportsIndex: "{{ url('kanban/customer-reports') }}", // GET
      customerReportsStore: "{{ url('kanban/customer-reports') }}", // POST
      customerReportsComment: (id) =>
        "{{ url('kanban/customer-reports') }}/" + id + "/comment",

      liveFeed: "/lead/kanban/feed",
    },
    stageNames: {
      lead: "Lead",
      offer: "Verkauf",
      deal: "Auftrag",
      project: "Montage",
      completed: "Abschluss",
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

  /* --------------------------- Quill for notes ----------------------------- */
  // Global Quill instance for the notes drawer (new note input)
  let noteQuill = null;

  function ensureNoteQuill() {
      if (typeof window.Quill === "undefined") return null;
      if (noteQuill) return noteQuill;

      let editorHost = document.getElementById("noteEditor");
      const textarea = document.getElementById("noteText");

      // If there is no explicit #noteEditor, create one in place of the textarea
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

  /* ------------------------------ State store ------------------------------ */
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

  /* --------------------------------- Utils -------------------------------- */
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
  const featherRefreshSoon = () => {
    if (window.feather?.replace)
      requestAnimationFrame(() => feather.replace());
  };
  const shortNum = (n) => {
    n = Number(n || 0);
    if (n < 1e3) return "" + n;
    if (n < 1e6)
      return (n / 1e3)
        .toFixed(n % 1e3 ? 1 : 0)
        .replace(/\.0$/, "") + "k";
    if (n < 1e9)
      return (n / 1e6)
        .toFixed(n % 1e6 ? 1 : 0)
        .replace(/\.0$/, "") + "M";
    return (n / 1e9)
      .toFixed(n % 1e9 ? 1 : 0)
      .replace(/\.0$/, "") + "B";
  };

  const canonicalStage = (s) => {
    const k = String(s || "").toLowerCase();
    return APP.stageNames[k] ? k : APP.stageAlias[k] || "lead";
  };

  // Stage order + helpers
  const STAGE_ORDER = ["lead", "offer", "deal", "project", "completed"];
  const stageRank = (s) => STAGE_ORDER.indexOf(canonicalStage(s));
  const isBackward = (from, to) => stageRank(to) < stageRank(from);

  // Show/Hide action buttons based on stage (hide Junk for deal+)
  function enforceActionVisibility(cardOrStage) {
    const cards =
      cardOrStage && cardOrStage.nodeType === 1
        ? [cardOrStage]
        : Array.from(document.querySelectorAll(".card"));

    cards.forEach((c) => {
      const stage =
        canonicalStage(c.dataset.stage || c.closest(".column")?.id || "lead");
      const hideJunk = stageRank(stage) >= stageRank("deal"); // deal or later
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
      localStorage.setItem(
        STORAGE_KEY,
        JSON.stringify({
          sort: State.sort,
          page: State.page,
          filtersQS: State.filtersQS,
          statusGroup: State.statusGroup,
        })
      );
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
      if (
        statusGroup === null ||
        ["offen", "zusage", "absage"].includes(statusGroup)
      )
        State.statusGroup = statusGroup;
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
          try {
            el.value = v;
          } catch {}
        }
      });
      if (window.jQuery) {
        jQuery(form)
          .find(".select2")
          .each(function () {
            const name = this.getAttribute("name");
            if (name && p.has(name))
              jQuery(this).val(p.get(name)).trigger("change");
          });
      }
    }
    State.sort.key = p.get("sort_by") || State.sort.key;
    State.sort.dir =
      (p.get("sort_dir") || State.sort.dir).toLowerCase() === "asc"
        ? "asc"
        : "desc";
    State.page = parseInt(p.get("page") || State.page, 10) || 1;
    State.filtersQS = buildFilterQS(); // ensure status_group injection
  }

  /* ------------------------------- Networking ----------------------------- */
  function cancel(key) {
    try {
      State.req[key]?.abort();
    } catch {}
    State.req[key] = new AbortController();
    return State.req[key].signal;
  }

  async function safeFetchJSON(
    url,
    { method = "GET", headers = {}, body, signal, retries = 0, retryDelay = 240 } = {}
  ) {
    const go = async () => {
      const res = await fetch(url, {
        method,
        credentials: "same-origin",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
          ...headers,
        },
        body,
        signal,
      });
      const text = await res.text();
      if (!res.ok || isLikelyHTML(text)) {
        const hint =
          res.status === 419
            ? "CSRF (419)"
            : res.status === 401
            ? "Unauthorized (401)"
            : isLikelyHTML(text)
            ? "Returned HTML (login/exception?)"
            : "";
        const snippet = text.replace(/\s+/g, " ").slice(0, 400);
        throw new Error(
          `HTTP ${res.status} ${res.statusText}. ${hint}\n${snippet}`
        );
      }
      try {
        return JSON.parse(text);
      } catch {
        throw new Error("Invalid JSON");
      }
    };

    try {
      return await go();
    } catch (err) {
      if (retries > 0 && method === "GET") {
        await new Promise((r) => setTimeout(r, retryDelay));
        return safeFetchJSON(url, {
          method,
          headers,
          body,
          signal,
          retries: retries - 1,
          retryDelay: retryDelay * 1.6,
        });
      }
      throw err;
    }
  }

  const postJSON = (url, payload = {}) =>
    safeFetchJSON(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CSRF(),
      },
      body: JSON.stringify(payload),
    });

  /* ------------------------------ Filters/UI ------------------------------ */
  function initSelect2() {
    if (!window.jQuery) return;
    const $d = jQuery("#sideDrawer");
    $d.find(".select2").each(function () {
      const $el = jQuery(this);
      if ($el.hasClass("select2-hidden-accessible")) $el.select2("destroy");
      $el.select2({
        placeholder: "Auswählen…",
        allowClear: true,
        width: "100%",
        dropdownParent: $d,
      });
    });
  }

  function getFilterValues() {
    const f = qs("#kanbanFilterForm");
    if (!f) return {};
    const fd = new FormData(f),
      obj = {};
    fd.forEach((v, k) => (obj[k] = v === "" ? null : v));
    return obj;
  }

  function updateFilterBadges() {
    const vals = getFilterValues();
    const keys = [
      "customer",
      "stage",
      "employee",
      "department",
      "product",
      "interest",
      "date_from",
      "date_to",
    ];
    const n =
      keys.reduce(
        (t, k) => t + (vals[k] && String(vals[k]).trim() ? 1 : 0),
        0
      ) + (State.statusGroup ? 1 : 0);

    const rail = qs("#filterBadge");
    const tab = qs("#tabFilterCount");
    const btn = qs("#btnOpenDrawer");
    if (rail) {
      rail.textContent = n;
      rail.classList.toggle("d-none", !n);
    }
    if (tab) {
      tab.textContent = n;
      tab.classList.toggle("d-none", !n);
    }
    if (btn) {
      btn.classList.toggle("rail-btn--active", !!n);
    }
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
    const el = qs("#sideDrawer"),
      bd = qs("#drawerBackdrop");
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
    qsa("[data-close-drawer]").forEach((b) =>
      b.addEventListener("click", close)
    );
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

  /* ------------------------------ Kanban DOM ------------------------------- */
  function ensureColumns() {
    const board = qs("#kanban");
    if (!board) return;
    if (board.querySelector(".column")) return;

    const frag = document.createDocumentFragment();
    Object.entries(APP.stageNames).forEach(([id, title]) => {
      const col = document.createElement("div");
      col.className = "column";
      col.id = id;
      col.ondragover = (e) => e.preventDefault(); // onDrop wired in Segment 2
      col.innerHTML = `
        <h3>
          <span>${title}</span>
          <span class="count-badge" data-count-for="${id}" aria-live="polite">0</span>
        </h3>
        <div class="column-content"></div>
      `;
      frag.appendChild(col);
    });
    board.appendChild(frag);
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
    if (["lead", "offer"].includes(stage))
      return ["Offen", "warning", "text-dark"];

    if (["deal", "project", "completed"].includes(stage))
      return ["Zusage", "success", ""];

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
        <div>
          <span class="badge bg-${tone} badge-${tone} ${extra}">${txt}</span>
        </div>
        ${
          hasWS
            ? `
        <div class="mt-1">
          <span class="badge bg-${RUN.badgeTone[ws]} ${
              ws === "paused" ? "text-dark" : ""
            }">
            <i class="feather ${RUN.icon[ws]}"></i> ${RUN.label[ws]}
          </span>
        </div>`
            : ""
        }

        <div class="meta">
          <div class="rowline"><i class="feather icon-box"></i></div>
          <div class="rowline value"><strong>${latestPhase}</strong></div>

          <div class="rowline"><i class="feather icon-check-circle"></i></div>
          <div class="rowline value">${latestAct}</div>

          <div class="rowline"><i class="feather icon-clock"></i></div>
          <div class="rowline value time">${timeText}</div>
        </div>
      </div>`;
  }

  function applyRunStateUI(card, state) {
    const cls = {
      playing: "status-playing",
      paused: "status-paused",
      stopped: "status-stopped",
    };
    card.classList.remove(
      "status-playing",
      "status-paused",
      "status-stopped",
      "card-has-overlay"
    );
    card.classList.add(cls[state] || cls.playing);

    const overlay = card.querySelector(".card-status-overlay");
    if (!overlay) return;

    if (state === "paused" || state === "stopped") {
      card.classList.add("card-has-overlay");
      overlay.style.display = "flex";
      overlay.innerHTML = `
        <span class="card-status-badge">
          <i class="feather ${
            state === "paused" ? "icon-pause" : "icon-square"
          }"></i>
          ${state === "paused" ? "Pause" : "Stopp"}
        </span>`;
    } else {
      overlay.style.display = "none";
      overlay.innerHTML = "";
    }
    card.dataset.runState = state;
  }

  const cardId = (it) => `card-${it.lead_product_id}`;

  function cardHTML(item, stageKey) {
    const fullName =
      `${item.customer_name ?? ""} ${item.customer_lastname ?? ""}`.trim() ||
      "Unbekannt";
    const address = [item.street, item.postcode, item.city]
      .filter(Boolean)
      .join(", ");
    const updated = new Date(
      item.updated_at || Date.now()
    ).toLocaleDateString("de-DE");

    const employee =
      item.employee && item.employee.employee_id ? item.employee : null;
    const fieldEmployee =
      item.field_employee && item.field_employee.employee_id
        ? item.field_employee
        : null;

    const empList = [];

    if (employee) {
      empList.push({
        title:
          ((employee.lastname ?? "") + " " + (employee.name ?? "")).trim() ||
          "Innendienst",
        image: employee.image || "",
      });
    }

    if (fieldEmployee) {
      empList.push({
        title:
          ((fieldEmployee.lastname ?? "") + " " + (fieldEmployee.name ?? "")).trim() ||
          "Außendienst",
        image: fieldEmployee.image || "",
      });
    }

    const empHTML =
      empList.length > 0
        ? `<ul class="list-unstyled users-list m-0 d-flex align-items-center">
            ${empList
              .map(
                (e) => `
              <li class="avatar pull-up" title="${e.title}">
                <img class="media-object rounded-circle"
                    src="${APP.EMP_SRC}/${e.image}"
                    height="30" width="30" alt="">
              </li>`
              )
              .join("")}
          </ul>`
        : "<small>&ndash;</small>";

    const hideJunk =
      stageRank(canonicalStage(stageKey)) >= stageRank("deal"); // deal+

    return `
      <div class="card-status-overlay" aria-hidden="true"></div>

      <!-- Top-left custom menu -->
      <div class="kb-menu" style="position:absolute; left:8px; top:8px; z-index:3;">
        <button type="button"
                class="btn-icon"
                data-act="custom-menu-toggle"
                title="Menü"
                aria-haspopup="menu"
                aria-expanded="false">
          <i class="feather icon-more-vertical"></i>
        </button>
        <div class="kb-menu-dropdown" hidden>
          <button type="button" class="kb-menu-item" data-menu="verlauf" role="menuitem">Verlauf</button> 
          <button type="button" class="kb-menu-item" data-menu="ticket"  role="menuitem">Ticket</button>
          <button type="button" class="kb-menu-item" data-menu="wartung" role="menuitem">Wartung</button>
          <button type="button" class="kb-menu-item" data-menu="termin">
            Termin
            <span class="kb-menu-pill kb-menu-pill--ap" data-ap-count style="display:none;">0</span>
          </button>
          <button type="button" class="kb-menu-item" data-menu="aufgabe">
            Aufgabe
            <span class="kb-menu-pill kb-menu-pill--pt" data-pt-count style="display:none;">0</span>
          </button>
        </div>
      </div>

      <div class="card-header" style="padding-left:44px;">
        <strong>${fullName}</strong>
        <div class="circle">${item.initial ?? ""}</div>
      </div>

      <div>
        <small><i class="feather icon-calendar"></i> ${updated}</small><br>
        <small>${address}</small>
      </div>

      <div class="employeeList">${empHTML}</div>

      ${buildStatusBlock(item)}

      <!-- Compact per-card live feed; initially hidden, only shown if there are items -->
      <div class="live-feed-bar card-live-feed"
          data-feed-root
          data-feed-count="0"
          style="display:none;">
        <div class="live-feed-left">
          <div class="live-feed-icon">
            <i class="feather icon-zap"></i>
          </div>
        </div>

        <div class="live-feed-body">
          <div class="live-feed-line d-none" data-feed-empty>
            <span class="live-feed-title">Keine Aktivitäten</span>
            <span class="live-feed-dot">•</span>
            <span class="live-feed-text">Noch keine Termine oder Aufgaben.</span>
          </div>

          <div class="live-feed-line d-none" data-feed-line>
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
          <button type="button"
                  class="live-feed-btn"
                  title="Zurück"
                  data-feed-prev>
            <i class="feather icon-skip-back"></i>
          </button>

          <button type="button"
                  class="live-feed-btn"
                  title="Pause / Abspielen"
                  data-feed-toggle>
            <i class="feather icon-pause" data-feed-icon-pause></i>
            <i class="feather icon-play d-none" data-feed-icon-play></i>
          </button>

          <button type="button"
                  class="live-feed-btn"
                  title="Weiter"
                  data-feed-next>
            <i class="feather icon-skip-forward"></i>
          </button>

          <button type="button"
                  class="live-feed-btn"
                  title="Alle Aktivitäten anzeigen"
                  data-feed-open-modal>
            <i class="feather icon-maximize-2"></i>
          </button>
        </div>
      </div>

      <div class="card-actions" role="group" aria-label="Aktionen">
        <div class="left-actions">
          <button class="btn-icon btn-play"  data-run="playing" aria-label="Start">
            <i class="feather icon-play"></i>
          </button>
          <button class="btn-icon"           data-run="paused"  aria-label="Pause">
            <i class="feather icon-pause"></i>
          </button>
          <button class="btn-icon"           data-run="stopped" aria-label="Stopp">
            <i class="feather icon-square"></i>
          </button>
        </div>
        <div class="right-actions">
          <button class="btn-icon btn-notes" data-act="notes" title="Notizen">
            <i class="feather icon-message-square"></i>
            <span class="badge-notes">–</span>
          </button>
          <button class="btn-icon" data-act="profile" title="Profil">
            <i class="feather icon-eye"></i>
          </button>
          ${
            hideJunk
              ? ``
              : `
            <button class="btn-icon" data-act="delete" title="In Junk verschieben">
              <i class="feather icon-trash-2"></i>
            </button>`
          }
          ${
            stageKey === "completed"
              ? `
            <button class="btn-icon" data-act="archive" title="Archivieren">
              <i class="feather icon-archive"></i>
            </button>`
              : ``
          }
        </div>
      </div>`;
  }

  function mountOrUpdateCard(stageKey, item, existing) {
    let card = existing;
    if (!card) {
      card = document.createElement("div");
      card.className = "card";
      card.id = cardId(item);
      card.draggable = true; // drag handlers in Segment 2

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

    // contact data for Termine
    card.dataset.fullAddress = item.full_address || "";
    card.dataset.street = item.street || "";
    card.dataset.postcode = item.postcode || "";
    card.dataset.city = item.city || "";
    card.dataset.phone = item.phone || "";
    card.dataset.email = item.email || "";
    card.dataset.latitude = item.latitude || "";
    card.dataset.longitude = item.longitude || "";

    card.innerHTML = cardHTML(item, stageKey);
    enforceActionVisibility(card);

    const ws = (item.work_status || "playing").toString().toLowerCase();
    applyRunStateUI(
      card,
      ["playing", "paused", "stopped"].includes(ws) ? ws : "playing"
    );

    return card;
  }

  function renderKanbanDiff(leads) {
    ensureColumns();

    const existing = new Map();
    qsa("#kanban .card").forEach((el) => existing.set(el.id, el));

    const stageBuckets = new Map(
      Object.keys(APP.stageNames).map((k) => [k, []])
    );
    const filtered = (leads || []).filter((it) => {
      const s = canonicalStage(it.stage);
      return !["archive", "archiv", "junk"].includes(s);
    });

    for (const it of filtered) {
      const s = canonicalStage(it.stage);
      stageBuckets.get(s).push(it);
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

  function renderKanbanIncremental(
    leads,
    chunkSize = autoChunk(),
    done = () => {}
  ) {
    ensureColumns();
    clearColumns();

    const list = (leads || []).filter((it) => {
      const s = String(it?.stage || "").toLowerCase();
      return !["archive", "archiv", "junk"].includes(s);
    });

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
        const card = mountOrUpdateCard(stage, item, null);
        getFrag(stage).appendChild(card);
      }

      for (const [stage, frag] of frags)
        colContent(stage)?.appendChild(frag);

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

  /* -------------------------------- Notes --------------------------------- */
  const visibleCardTuples = () =>
    qsa("#kanban .card").map((el) => ({
      el,
      customer_id: el.dataset.customerId,
      alternative_id: el.dataset.alternativeId,
      product_id: el.dataset.productId || null,
    }));

  async function fetchNoteCountOnce(t) {
    const params = new URLSearchParams({
      customer_id: t.customer_id,
      alternative_id: t.alternative_id,
      per_page: 1,
    });
    if (t.product_id) params.set("product_id", t.product_id);

    try {
      const p = await safeFetchJSON(
        `${APP.endpoints.notesIndex}?${params.toString()}`
      );
      return Number(p?.total || 0);
    } catch {
      return 0;
    }
  }

  function updateBadge(el, n) {
    const bd = el.querySelector(".badge-notes");
    if (!bd) return;
    bd.dataset.count = String(n);
    bd.textContent = shortNum(n);
  }

  function updateNoteBadgesForVisibleCards() {
    const tuples = visibleCardTuples();
    tuples.forEach((t) => updateBadge(t.el, 0));
    let i = 0;
    const CON = 4;

    (function next() {
      const batch = tuples.slice(i, (i += CON));
      if (!batch.length) return;
      Promise.all(
        batch.map(async (t) => updateBadge(t.el, await fetchNoteCountOnce(t)))
      ).finally(() => setTimeout(next, 30));
    })();
  }

  // Tabs inside the notes drawer (Notizen / Kunde Report / Termin Report)
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
  }

  // Termin-Report loader (appointment reports)
  async function loadNotesReport() {
    const panel = document.getElementById("notesReport");
    if (!panel) return;

    const cId = document.getElementById("notesCustomerId")?.value || "";
    const aId = document.getElementById("notesAlternativeId")?.value || "";
    const pId = document.getElementById("notesProductId")?.value || "";

    if (!cId || !aId) {
      panel.innerHTML = `
        <div class="text-muted small p-2">
          Kein Kontext (Kunde/Alternative) vorhanden.
        </div>
      `;
      return;
    }

    panel.innerHTML = `
      <div class="text-muted small p-2">
        Report wird geladen…
      </div>
    `;

    try {
      const params = new URLSearchParams({
        customer_id: cId,
        alternative_id: aId,
      });
      if (pId) params.set("product_id", pId);

      const url = `${APP.endpoints.reportsIndex}?${params.toString()}`;

      const res = await fetch(url, {
        method: "GET",
        credentials: "same-origin",
        headers: {
          Accept: "text/html,application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      const text = await res.text();
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
      }

      let html = text;
      const ct = res.headers.get("content-type") || "";

      if (ct.includes("application/json")) {
        try {
          const json = JSON.parse(text);
          if (typeof json.html === "string") {
            html = json.html;
          } else {
            html = `
              <pre class="small p-2 bg-light border rounded mb-0" style="max-height: 320px; overflow:auto;">
                ${JSON.stringify(json, null, 2)}
              </pre>
            `;
          }
        } catch {
          html = text;
        }
      }

      panel.innerHTML = html;
    } catch (e) {
      panel.innerHTML = `
        <div class="text-danger small p-2">
          Report konnte nicht geladen werden.<br>
          ${(e && e.message) ? e.message : ''}
        </div>
      `;
    }
  }

  // Kundenreport loader (customer reports tab)
  async function loadCustomerReport() {
    const panel = document.getElementById("customerReportList");
    if (!panel) return;

    const cId = document.getElementById("notesCustomerId")?.value || "";
    const aId = document.getElementById("notesAlternativeId")?.value || "";
    const pId = document.getElementById("notesProductId")?.value || "";

    if (!cId || !aId) {
      panel.innerHTML = `
        <div class="text-muted small p-2">
          Kein Kontext (Kunde/Alternative) vorhanden.
        </div>
      `;
      return;
    }

    panel.innerHTML = `
      <div class="text-muted small p-2">
        Kundenreport wird geladen…
      </div>
    `;

    try {
      const params = new URLSearchParams({
        customer_id: cId,
        alternative_id: aId,
      });
      if (pId) params.set("product_id", pId);

      const res = await safeFetchJSON(
        `${APP.endpoints.customerReportsIndex}?${params.toString()}`,
        { method: "GET" }
      );

      if (!res || typeof res.html !== "string") {
        throw new Error(res?.message || "Unerwartete Serverantwort.");
      }

      panel.innerHTML = res.html;
    } catch (e) {
      panel.innerHTML = `
        <div class="text-danger small p-2">
          Kundenreport konnte nicht geladen werden.<br>
          ${(e && e.message) ? e.message : ''}
        </div>
      `;
    }
  }

  // Termin-Report: create new report (ap-*)
  document.addEventListener("submit", async (e) => {
    const form = e.target.closest(".ap-report-create-form");
    if (!form) return;

    e.preventDefault();

    const titleEl = form.querySelector('input[name="title"]');
    const stageEl = form.querySelector('select[name="stage"]');
    const contentEl = form.querySelector('textarea[name="content"]');

    const title = (titleEl?.value || "").trim();
    const content = (contentEl?.value || "").trim();
    const stage = (stageEl?.value || "").trim();

    if (!title || !content) {
      Swal.fire("Hinweis", "Titel und Text sind Pflichtfelder.", "info");
      return;
    }

    const appointmentId = form.dataset.appointmentId || null;

    try {
      const payload = {
        title,
        content,
        stage,
        report: `${title}\n\n${content}`,
        report_date:
          form.querySelector('input[name="report_date"]')?.value || null,
      };

      const url = APP.endpoints.reportsStore(appointmentId);

      const res = await safeFetchJSON(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF(),
        },
        body: JSON.stringify(payload),
      });

      if (!res || res.status !== "ok") {
        throw new Error(res?.message || "Report konnte nicht gespeichert werden.");
      }

      const group = form.closest(".ap-appointment-group");
      const list = group?.querySelector(".ap-report-list");
      if (list && typeof res.html === "string") {
        list.insertAdjacentHTML("afterbegin", res.html);
      }

      if (titleEl) titleEl.value = "";
      if (contentEl) contentEl.value = "";
      if (stageEl) stageEl.value = "";

      const wrapper = group?.querySelector(".ap-report-create-wrapper");
      if (wrapper) wrapper.style.display = "none";

      Swal.fire("Gespeichert", "Report wurde hinzugefügt.", "success");
    } catch (err) {
      Swal.fire(
        "Fehler",
        err.message || "Report konnte nicht gespeichert werden.",
        "error"
      );
    }
  });

  // Termin-Report: reactions + comments (ap-*)
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".ap-report-like, .ap-report-dislike");
    if (!btn) return;

    const card = btn.closest(".ap-report-card");
    if (!card) return;

    const reportId = card.getAttribute("data-report-id");
    if (!reportId) return;

    const isLike = btn.classList.contains("ap-report-like");
    let reaction = isLike ? "like" : "dislike";

    if (btn.classList.contains("is-active")) {
      reaction = "none";
    }

    try {
      const res = await safeFetchJSON(APP.endpoints.reportsReact(reportId), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF(),
        },
        body: JSON.stringify({ reaction }),
      });

      const likeCountEl = card.querySelector(".ap-report-like-count");
      const dislikeCountEl = card.querySelector(".ap-report-dislike-count");

      if (likeCountEl) likeCountEl.textContent = res.likes ?? 0;
      if (dislikeCountEl) dislikeCountEl.textContent = res.dislikes ?? 0;

      card
        .querySelectorAll(".ap-report-like, .ap-report-dislike")
        .forEach((b) => b.classList.remove("is-active"));

      if (res.my_reaction === "like") {
        card.querySelector(".ap-report-like")?.classList.add("is-active");
      } else if (res.my_reaction === "dislike") {
        card.querySelector(".ap-report-dislike")?.classList.add("is-active");
      }
    } catch (err) {
      Swal.fire(
        "Fehler",
        err.message || "Bewertung konnte nicht gespeichert werden.",
        "error"
      );
    }
  });

  // Termin-Report: toggle inline create form
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".ap-open-report-form");
    if (!btn) return;

    const group = btn.closest(".ap-appointment-group");
    if (!group) return;

    const wrapper = group.querySelector(".ap-report-create-wrapper");
    if (!wrapper) return;

    document.querySelectorAll(".ap-report-create-wrapper").forEach((el) => {
      if (el !== wrapper) el.style.display = "none";
    });

    const isVisible =
      wrapper.style.display !== "none" && wrapper.style.display !== "";
    wrapper.style.display = isVisible ? "none" : "block";

    if (!btn.dataset.originalLabel) {
      btn.dataset.originalLabel = btn.innerHTML;
    }
    if (!isVisible) {
      btn.innerHTML = `<i class="feather icon-file-text"></i> Report schließen`;
    } else {
      btn.innerHTML = btn.dataset.originalLabel;
    }
  });

  // Termin-Report: toggle comments section
  document.addEventListener("click", (e) => {
    const toggleBtn = e.target.closest("[data-report-toggle-comments]");
    if (!toggleBtn) return;

    const card = toggleBtn.closest(".ap-report-card");
    if (!card) return;

    const section = card.querySelector(".ap-report-comments");
    if (!section) return;

    if (section.hasAttribute("hidden")) {
      section.removeAttribute("hidden");
    } else {
      section.setAttribute("hidden", "");
    }
  });

  // Termin-Report: submit comment
  document.addEventListener("click", async (e) => {
    const submitBtn = e.target.closest(".ap-report-comment-submit");
    if (!submitBtn) return;

    const card = submitBtn.closest(".ap-report-card");
    if (!card) return;

    const reportId = card.getAttribute("data-report-id");
    const textarea = card.querySelector(".ap-report-comment-text");
    if (!reportId || !textarea) return;

    const text = textarea.value.trim();
    if (!text) return;

    try {
      const res = await safeFetchJSON(APP.endpoints.reportsComment(reportId), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF(),
        },
        body: JSON.stringify({ comment: text }),
      });

      if (res && typeof res.html === "string") {
        const list = card.querySelector(".ap-report-comments-list");
        if (list) {
          list.insertAdjacentHTML("beforeend", res.html);
        }

        const toggleBtn = card.querySelector("[data-report-toggle-comments]");
        if (toggleBtn) {
          const m = toggleBtn.textContent.match(/(\d+)/);
          const current = m ? parseInt(m[1], 10) : 0;
          toggleBtn.innerHTML = `<i class="feather icon-message-circle mr-25"></i> Kommentare (${
            current + 1
          })`;
        }
      }

      textarea.value = "";
    } catch (err) {
      Swal.fire(
        "Fehler",
        err.message || "Kommentar konnte nicht gespeichert werden.",
        "error"
      );
    }
  });

  // Kundenreport: toggle new report form
  document.addEventListener("click", (e) => {
    const btnNew = e.target.closest(".cr-toggle-new");
    if (btnNew) {
      const panel = document.getElementById("customerReportList");
      const wrapper = panel?.querySelector(".cr-new-wrapper");
      if (!wrapper) return;

      const willShow = wrapper.hidden || wrapper.style.display === "none";
      wrapper.hidden = !willShow;
      if (willShow) {
        btnNew.classList.add("active");
      } else {
        btnNew.classList.remove("active");
      }
      return;
    }

    const btnCancel = e.target.closest(".cr-cancel-new");
    if (btnCancel) {
      const wrapper = btnCancel.closest(".cr-new-wrapper");
      if (wrapper) wrapper.hidden = true;

      const toggle = document.querySelector(".cr-toggle-new");
      if (toggle) toggle.classList.remove("active");
    }
  });

  // Kundenreport: create new customer report
  document.addEventListener("submit", async (e) => {
    const form = e.target.closest(".cr-create-form");
    if (!form) return;

    e.preventDefault();

    const customerId =
      form.querySelector('input[name="customer_id"]')?.value || "";
    const alternativeId =
      form.querySelector('input[name="alternative_id"]')?.value || "";
    const productId =
      form.querySelector('input[name="product_id"]')?.value || "";
    const stageEl = form.querySelector('select[name="stage"]');
    const reportEl = form.querySelector('textarea[name="report"]');

    const report = (reportEl?.value || "").trim();
    const stage = (stageEl?.value || "").trim();

    if (!customerId || !alternativeId) {
      Swal.fire("Fehler", "Kundenkontext fehlt.", "error");
      return;
    }
    if (!report) {
      Swal.fire("Hinweis", "Reporttext ist erforderlich.", "info");
      return;
    }

    try {
      const payload = {
        customer_id: Number(customerId),
        alternative_id: Number(alternativeId),
        product_id: productId ? Number(productId) : null,
        stage,
        report,
      };

      const res = await safeFetchJSON(APP.endpoints.customerReportsStore, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF(),
        },
        body: JSON.stringify(payload),
      });

      if (!res || res.status !== "ok" || typeof res.html !== "string") {
        throw new Error(
          res?.message || "Kundenreport konnte nicht gespeichert werden."
        );
      }

      const panel = document.getElementById("customerReportList");
      const list = panel?.querySelector(".cr-list");
      if (list) {
        list.insertAdjacentHTML("afterbegin", res.html);
      }

      if (reportEl) reportEl.value = "";
      if (stageEl) stageEl.value = "";

      const wrapper = form.closest(".cr-new-wrapper");
      if (wrapper) wrapper.hidden = true;

      const toggle = document.querySelector(".cr-toggle-new");
      if (toggle) toggle.classList.remove("active");

      Swal.fire("Gespeichert", "Kundenreport wurde erstellt.", "success");
    } catch (err) {
      Swal.fire(
        "Fehler",
        err.message || "Kundenreport konnte nicht gespeichert werden.",
        "error"
      );
    }
  });

  // Kundenreport: toggle comments section
  document.addEventListener("click", (e) => {
    const toggle = e.target.closest(".cr-toggle-comments");
    if (!toggle) return;

    const card = toggle.closest(".cr-card");
    if (!card) return;

    const block = card.querySelector(".cr-comments");
    if (!block) return;

    block.hidden = !block.hidden;
  });

  // Kundenreport: submit comment
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".cr-comment-submit");
    if (!btn) return;

    const card = btn.closest(".cr-card");
    if (!card) return;

    const reportId = card.getAttribute("data-report-id");
    const textarea = card.querySelector(".cr-comment-text");
    if (!reportId || !textarea) return;

    const text = textarea.value.trim();
    if (!text) return;

    try {
      const res = await safeFetchJSON(
        APP.endpoints.customerReportsComment(reportId),
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": CSRF(),
          },
          body: JSON.stringify({ comment: text }),
        }
      );

      if (res && typeof res.html === "string") {
        const list = card.querySelector(".cr-comments-list");
        if (list) {
          list.insertAdjacentHTML("beforeend", res.html);
        }

        const toggle = card.querySelector(".cr-toggle-comments");
        if (toggle) {
          const m = toggle.textContent.match(/(\d+)/);
          const current = m ? parseInt(m[1], 10) : 0;
          toggle.innerHTML = `<i class="feather icon-message-circle mr-25"></i> Kommentare (${
            current + 1
          })`;
        }
      }

      textarea.value = "";
    } catch (err) {
      Swal.fire(
        "Fehler",
        err.message || "Kommentar konnte nicht gespeichert werden.",
        "error"
      );
    }
  });

  // Global click listener for the three tabs inside the notes drawer
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-notes-tab]");
    if (!btn) return;

    const tab = btn.dataset.notesTab;
    if (!tab) return;

    setNotesTab(tab);

    if (tab === "report") {
      loadNotesReport();
    } else if (tab === "customerReport") {
      loadCustomerReport();
    }
  });

  /* -------------------------------- Notes UI -------------------------------- */

  // Render single note bubble
  function noteHTML(n) {
    const me = String(n.created_by ?? "") === String(APP.authUserId);
    const img = n?.author?.image
      ? `${APP.EMP_SRC}/${n.author.image}`
      : `${APP.EMP_SRC}/noimage.png`;
    const who = n.author
      ? `${n.author.lastname ?? ""} ${n.author.name ?? ""}`.trim()
      : "Unbekannt";
    const when = n.created_at
      ? new Date(n.created_at).toLocaleString("de-DE")
      : "";

    const description = n.description || "";

    const actions = me
      ? `
        <div class="note-actions">
          <button type="button"
                  class="note-action note-action-edit"
                  data-note-edit
                  data-note-id="${n.id}">
            <i class="feather icon-edit-2"></i>
          </button>
          <button type="button"
                  class="note-action note-action-delete"
                  data-note-delete
                  data-note-id="${n.id}">
            <i class="feather icon-trash-2"></i>
          </button>
        </div>
      `
      : "";

    const bubble = `
      <div class="note-bubble ${me ? "me" : "other"}">
        <div class="note-bubble-body" data-note-body>
          ${description}
        </div>
        <div class="note-meta">
          <span class="note-meta-author">${who}</span>
          <span class="note-meta-sep">•</span>
          <span class="note-meta-time">${when}</span>
        </div>
        ${actions}
      </div>
    `;

    if (me) {
      return `
        <div class="note-row me" data-note-id="${n.id}">
          ${bubble}
          <img class="note-avatar" src="${img}" alt="">
        </div>
      `;
    }

    return `
      <div class="note-row other" data-note-id="${n.id}">
        <img class="note-avatar" src="${img}" alt="">
        ${bubble}
      </div>
    `;
  }

  // Update global notes count badge + per-card badges
  function adjustNotesCounters(delta) {
    const badge = document.getElementById("notesCountBadge");
    if (badge) {
      const current = Number(badge.dataset.count || 0);
      const next = Math.max(0, current + delta);
      badge.dataset.count = String(next);
      badge.textContent = shortNum(next);
    }

    const fC = document.getElementById("notesCustomerId");
    const fA = document.getElementById("notesAlternativeId");
    const fP = document.getElementById("notesProductId");

    if (!fC || !fA) return;

    const cId = fC.value;
    const aId = fA.value;
    const pId = fP.value || "";

    document
      .querySelectorAll(
        `.card[data-customer-id="${CSS.escape(
          cId
        )}"][data-alternative-id="${CSS.escape(
          aId
        )}"][data-product-id="${CSS.escape(pId)}"] .badge-notes`
      )
      .forEach((b) => {
        const current = Number(b.dataset.count || 0);
        const next = Math.max(0, current + delta);
        b.dataset.count = String(next);
        b.textContent = shortNum(next);
      });
  }

  // Notes drawer open helper (now using Quill for new note text when available)
  async function openNotesDrawerFor(cId, aId, pId, title) {
    const drawer = qs("#notesDrawer"),
      bd = qs("#notesBackdrop"),
      list = qs("#notesList"),
      titleEl = qs("#notesTitle");
    const fC = qs("#notesCustomerId"),
      fA = qs("#notesAlternativeId"),
      fP = qs("#notesProductId");
    const form = qs("#notesForm"),
      ta = qs("#noteText");

    function open() {
      titleEl.textContent = title || "Kunden-Notizen";
      drawer.classList.add("open");
      bd.classList.add("show");
      document.body.style.overflow = "hidden";

      // Make sure Quill is initialized (if available) and clear content
      ensureNoteQuill();
      setNoteEditorHTML("");

      setNotesTab("notes");

      // Focus Quill editor or fallback textarea
      if (noteQuill) {
        try {
          noteQuill.focus();
        } catch {}
      } else if (ta) {
        ta.value = "";
        ta.focus();
      }
    }
    function close() {
      drawer.classList.remove("open");
      bd.classList.remove("show");
      document.body.style.overflow = "";
    }
    bd.onclick = close;
    qsa("[data-notes-close]").forEach((b) => (b.onclick = close));

    fC.value = cId;
    fA.value = aId;
    fP.value = pId || "";

    open();

    try {
      const params = new URLSearchParams({
        customer_id: cId,
        alternative_id: aId,
        per_page: 50,
      });
      if (pId) params.set("product_id", pId);

      const payload = await safeFetchJSON(
        `${APP.endpoints.notesIndex}?${params.toString()}`
      );
      const items = Array.isArray(payload?.notes)
        ? payload.notes
        : payload || [];
      items.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

      list.innerHTML = items.map((n) => noteHTML(n)).join("");

      const total = payload?.total ?? items.length;
      const badge = document.getElementById("notesCountBadge");
      if (badge) {
        badge.dataset.count = String(total);
        badge.textContent = shortNum(total);
      }

      list.scrollTop = list.scrollHeight;
    } catch (e) {
      Swal.fire(
        "Fehler",
        e.message || "Notizen konnten nicht geladen werden.",
        "error"
      );
    }

    form.onsubmit = async (ev) => {
      ev.preventDefault();

      const text = getNoteEditorHTML();
      if (!text) return;

      try {
        const res = await safeFetchJSON(APP.endpoints.notesStore, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": CSRF(),
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            customer_id: Number(fC.value),
            alternative_id: Number(fA.value),
            product_id: fP.value ? Number(fP.value) : null,
            description: text,
          }),
        });

        const n = res.note || res;

        // append new note bubble
        document
          .getElementById("notesList")
          .insertAdjacentHTML("beforeend", noteHTML(n));

        // scroll to bottom
        const listEl = document.getElementById("notesList");
        if (listEl) listEl.scrollTop = listEl.scrollHeight;

        // clear editor
        setNoteEditorHTML("");

        // update counters (drawer + card badge)
        adjustNotesCounters(+1);
      } catch (e) {
        Swal.fire("Fehler", e.message || "Notiz nicht gespeichert.", "error");
      }
    };
  }

  // Inline edit / delete for notes (still simple textarea, not Quill)
  document.addEventListener("click", async (e) => {
    // ENTER EDIT MODE
    const editBtn = e.target.closest("[data-note-edit]");
    if (editBtn) {
      const row = editBtn.closest(".note-row");
      if (!row) return;
      const bubble = row.querySelector(".note-bubble");
      const body = bubble?.querySelector("[data-note-body]");
      if (!bubble || !body) return;
      if (bubble.classList.contains("is-editing")) return;

      const originalText = body.textContent || "";
      bubble.classList.add("is-editing");
      bubble.dataset.originalText = originalText;

      const textarea = document.createElement("textarea");
      textarea.className = "note-edit-text form-control";
      textarea.value = originalText.trim();
      body.innerHTML = "";
      body.appendChild(textarea);

      const toolbar = document.createElement("div");
      toolbar.className = "note-edit-toolbar mt-25";
      toolbar.innerHTML = `
        <button type="button" class="btn btn-sm btn-primary" data-note-save>Speichern</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-note-cancel>Abbrechen</button>
      `;
      bubble.appendChild(toolbar);

      textarea.focus();
      textarea.setSelectionRange(textarea.value.length, textarea.value.length);

      return;
    }

    // SAVE EDIT
    const saveBtn = e.target.closest("[data-note-save]");
    if (saveBtn) {
      const row = saveBtn.closest(".note-row");
      if (!row) return;
      const id = row.dataset.noteId;
      if (!id) return;

      const bubble = row.querySelector(".note-bubble");
      const body = bubble?.querySelector("[data-note-body]");
      const textarea = bubble?.querySelector(".note-edit-text");
      const toolbar = bubble?.querySelector(".note-edit-toolbar");
      if (!bubble || !body || !textarea || !toolbar) return;

      const newText = textarea.value.trim();
      if (!newText) {
        Swal.fire("Hinweis", "Text darf nicht leer sein.", "info");
        return;
      }

      try {
        const res = await safeFetchJSON(
          APP.endpoints.notesInlineUpdate(id),
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": CSRF(),
            },
            body: JSON.stringify({ description: newText }),
          }
        );

        const n = res?.note || null;
        const finalText = n?.description || newText;

        body.textContent = finalText;

        toolbar.remove();
        bubble.classList.remove("is-editing");
        delete bubble.dataset.originalText;
      } catch (err) {
        Swal.fire(
          "Fehler",
          err.message || "Notiz konnte nicht aktualisiert werden.",
          "error"
        );
      }

      return;
    }

    // CANCEL EDIT
    const cancelBtn = e.target.closest("[data-note-cancel]");
    if (cancelBtn) {
      const row = cancelBtn.closest(".note-row");
      if (!row) return;
      const bubble = row.querySelector(".note-bubble");
      const body = bubble?.querySelector("[data-note-body]");
      const toolbar = bubble?.querySelector(".note-edit-toolbar");
      if (!bubble || !body || !toolbar) return;

      const originalText = bubble.dataset.originalText || "";
      body.textContent = originalText;

      toolbar.remove();
      bubble.classList.remove("is-editing");
      delete bubble.dataset.originalText;

      return;
    }

    // DELETE NOTE
    const deleteBtn = e.target.closest("[data-note-delete]");
    if (deleteBtn) {
      const row = deleteBtn.closest(".note-row");
      if (!row) return;
      const id = row.dataset.noteId;
      if (!id) return;

      const ok = await Swal.fire({
        title: "Notiz löschen?",
        text: "Diese Notiz wird endgültig gelöscht.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ja, löschen",
        cancelButtonText: "Abbrechen",
      });

      if (!ok.isConfirmed) return;

      try {
        await safeFetchJSON(APP.endpoints.notesDestroy(id), {
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": CSRF(),
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        });

        row.remove();
        adjustNotesCounters(-1);
      } catch (err) {
        Swal.fire(
          "Fehler",
          err.message || "Notiz konnte nicht gelöscht werden.",
          "error"
        );
      }
    }
  });

  // Row button (list) -> open notes
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-open-notes]");
    if (!btn) return;
    const row = btn.closest("tr");
    const name =
      row?.querySelector("td:nth-child(2)")?.textContent?.trim() ||
      "Kunde";
    openNotesDrawerFor(
      btn.dataset.customer,
      btn.dataset.alt,
      btn.dataset.product,
      `Notizen • ${name}`
    );
  });

  /* --------------------- Custom card menu (kb-menu) ------------------------ */
  (function () {
    const closeAllMenus = () => {
      document
        .querySelectorAll(".kb-menu-dropdown")
        .forEach((d) => d.setAttribute("hidden", ""));
      document
        .querySelectorAll(
          '[data-act="custom-menu-toggle"][aria-expanded="true"]'
        )
        .forEach((btn) => btn.setAttribute("aria-expanded", "false"));
    };

    document.addEventListener("click", (e) => {
      const toggleBtn = e.target.closest('[data-act="custom-menu-toggle"]');
      if (toggleBtn) {
        const dd = toggleBtn.parentElement.querySelector(
          ".kb-menu-dropdown"
        );
        const isOpen = dd && dd.hasAttribute("hidden") === false;

        closeAllMenus();

        if (dd && !isOpen) {
          dd.removeAttribute("hidden");
          toggleBtn.setAttribute("aria-expanded", "true");
        }

        e.stopImmediatePropagation();
        return;
      }

      const item = e.target.closest(".kb-menu-item");
      if (item) {
        const card = item.closest(".card");
        const type = item.dataset.menu;
        closeAllMenus();

        // Verlauf -> open history drawer
        if (type === "verlauf" && card) {
          const href = `/lead/process/history/${encodeURIComponent(
            card.dataset.customerId
          )}/${encodeURIComponent(card.dataset.alternativeId)}/${encodeURIComponent(
            card.dataset.productId
          )}`;

          const a = document.createElement("a");
          a.href = href;
          a.setAttribute("data-lh-history", "");
          a.style.display = "none";
          document.body.appendChild(a);
          a.click();
          a.remove();

          e.stopImmediatePropagation();
          return;
        }

        // Ticket -> mark as ticket and hide
        if (type === "ticket" && card) {
          const leadProductId = card.dataset.leadProductId;

          (async () => {
            try {
              const ok = await Swal.fire({
                title: "Als Ticket markieren?",
                text: "Dieser Lead wird als Ticket geführt und aus der Übersicht ausgeblendet.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ja",
              });
              if (!ok.isConfirmed) return;

              const res = await window.LeadUI.net.safeFetchJSON(
                window.LeadUI.APP.endpoints.ticketize(leadProductId),
                {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": window.LeadUI.utils.CSRF(),
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                  },
                  body: JSON.stringify({}),
                }
              );

              if (!res?.success)
                throw new Error(
                  res?.message || "Ticketisierung fehlgeschlagen"
                );

              card.remove();
              window.LeadUI.kanban.updateCounts();

              window.LeadUI?.silentRefreshBoth?.();

              Swal.fire("OK", "Als Ticket markiert.", "success");
            } catch (err) {
              Swal.fire(
                "Fehler",
                err.message || "Serverfehler",
                "error"
              );
            }
          })();

          e.stopImmediatePropagation();
          return;
        }

        // Termin -> open appointment drawer
        if (type === "termin" && card) {
          const name =
            card.querySelector(".card-header strong")?.textContent?.trim() ||
            "Kunde";

          const contact = {
            full_address: card.dataset.fullAddress || "",
            street: card.dataset.street || "",
            postcode: card.dataset.postcode || "",
            city: card.dataset.city || "",
            phone: card.dataset.phone || "",
            email: card.dataset.email || "",
            latitude: card.dataset.latitude || "",
            longitude: card.dataset.longitude || "",
          };

          if (window.AppointmentsUI?.open) {
            window.AppointmentsUI.open(
              card.dataset.customerId,
              card.dataset.alternativeId,
              card.dataset.productId,
              `Termine • ${name}`,
              contact
            );
          } else {
            card.dispatchEvent(
              new CustomEvent("open-appointments", {
                bubbles: true,
                detail: {
                  customerId: card.dataset.customerId,
                  alternativeId: card.dataset.alternativeId,
                  productId: card.dataset.productId,
                  title: `Termine • ${name}`,
                  ...contact,
                },
              })
            );
          }

          e.stopImmediatePropagation();
          return;
        }

        // Aufgabe -> open personal tasks drawer
        if (type === "aufgabe" && card) {
          const name =
            card.querySelector(".card-header strong")?.textContent?.trim() ||
            "Kunde";

          if (window.PersonalTasksUI?.open) {
            window.PersonalTasksUI.open(
              card.dataset.customerId,
              card.dataset.alternativeId,
              card.dataset.productId,
              `Aufgaben • ${name}`
            );
          } else {
            card.dispatchEvent(
              new CustomEvent("open-personal-tasks", {
                bubbles: true,
                detail: {
                  customerId: card.dataset.customerId,
                  alternativeId: card.dataset.alternativeId,
                  productId: card.dataset.productId,
                  title: `Aufgaben • ${name}`,
                },
              })
            );
          }

          e.stopImmediatePropagation();
          return;
        }

        // Fallback
        console.log("CUSTOM-MENU", {
          type,
          customer_id: card?.dataset.customerId,
          alternative_id: card?.dataset.alternativeId,
          product_id: card?.dataset.productId,
          lead_product_id: card?.dataset.leadProductId,
        });
        Swal.fire("Menu", `Selected: ${type}`, "info");

        e.stopImmediatePropagation();
        return;
      }
    });

    // Outside-click closes menus
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".kb-menu")) {
        closeAllMenus();
      }
    });

    // ESC closes menus
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeAllMenus();
    });

    // Prevent drag issues while interacting with menu
    document.addEventListener("mousedown", (e) => {
      if (e.target.closest(".kb-menu")) {
        e.stopPropagation();
        const card = e.target.closest(".card");
        if (card) card.draggable = false;
      }
    });
    document.addEventListener("mouseup", (e) => {
      const card = e.target.closest(".card");
      if (card) card.draggable = true;
    });
  })();

  /* --------------------------- Archive / Junk tabs ------------------------- */
  async function fetchArchiveTab(qsStr) {
    const pane = document.querySelector("#archive");
    if (!pane) return;
    const url = `${APP.endpoints.archive}${qsStr ? `?${qsStr}` : ""}`;

    try {
      const res = await fetch(url, {
        headers: {
          Accept: "text/html",
          "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
      });
      const html = await res.text();
      const wrap = pane.querySelector("#archiveInner") || pane;
      wrap.innerHTML = html;

      window.LeadUI?.partials?.syncTabCountsFromArchivePane?.();
    } catch (e) {}
  }

  async function fetchJunkTab(qsStr) {
    const pane = document.querySelector("#junk");
    if (!pane) return;
    const url = `${APP.endpoints.junk}${qsStr ? `?${qsStr}` : ""}`;

    try {
      const res = await fetch(url, {
        headers: {
          Accept: "text/html",
          "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
      });
      const html = await res.text();
      const wrap = pane.querySelector("#junkInner") || pane;
      wrap.innerHTML = html;

      window.LeadUI?.partials?.syncTabCountsFromArchivePane?.();
    } catch (e) {}
  }

  // Restore from Junk with reason and route /{c}/{a}/{p}
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".btn-restore");
    if (!btn) return;

    const row = btn.closest("tr");
    const select = row?.querySelector(".restore-select");
    const target = select?.value;

    if (!target) {
      Swal.fire(
        "Hinweis",
        "Bitte wählen Sie, wohin der Lead wiederhergestellt werden soll.",
        "info"
      );
      return;
    }

    const id = Number(btn.dataset.id); // lead_product_lists.id
    const source = btn.dataset.source || "junk";

    const cid = row?.dataset.customerId;
    const aid = row?.dataset.altId;
    const pid = row?.dataset.productId;

    if (!cid || !aid || !pid) {
      console.error("Restore: fehlende Tuple-IDs", { cid, aid, pid });
      Swal.fire(
        "Fehler",
        "Technischer Fehler: fehlende Kundendaten.",
        "error"
      );
      return;
    }

    const { value: reason, isConfirmed } = await Swal.fire({
      title: "Grund für Wiederherstellung",
      input: "textarea",
      inputPlaceholder:
        "Bitte Grund für die Wiederherstellung eingeben…",
      inputAttributes: { "aria-label": "Grund für Wiederherstellung" },
      showCancelButton: true,
      confirmButtonText: "Wiederherstellen",
      cancelButtonText: "Abbrechen",
      inputValidator: (value) => {
        if (!value || !value.trim()) {
          return "Bitte einen Grund eingeben.";
        }
        return null;
      },
    });

    if (!isConfirmed) return;

    try {
      btn.disabled = true;
      btn.classList.add("disabled");

      const url = `${APP.endpoints.changeStage}/${encodeURIComponent(
        cid
      )}/${encodeURIComponent(aid)}/${encodeURIComponent(pid)}`;

      const res = await postJSON(url, {
        lead_product_id: id,
        stage: target,
        description: reason,
        source,
      });

      if (!res || !res.success) {
        throw new Error(
          res?.message || "Wiederherstellung ist fehlgeschlagen."
        );
      }

      row?.remove();

      try {
        const qsStr = new URLSearchParams(location.search).toString();
        window.LeadUI?.partials?.fetchJunkTab?.(qsStr);
        window.LeadUI?.silentRefreshBoth?.();
      } catch (_) {}

      Swal.fire("OK", "Lead wurde wiederhergestellt.", "success");
    } catch (err) {
      Swal.fire(
        "Fehler",
        err.message || "Wiederherstellung ist fehlgeschlagen.",
        "error"
      );
    } finally {
      btn.disabled = false;
      btn.classList.remove("disabled");
    }
  });

  /* ====================== Per-card Live Feed (under card) ================== */
  const LiveFeed = (() => {
    const registry = new WeakMap();

    function createInstance(root) {
      const lineEmpty = root.querySelector("[data-feed-empty]");
      const lineLive = root.querySelector("[data-feed-line]");
      const titleEl = root.querySelector("[data-feed-title]");
      const textEl = root.querySelector("[data-feed-text]");
      const pillEl = root.querySelector("[data-feed-pill]");
      const timeEl = root.querySelector("[data-feed-time]");
      const counterEl = root.querySelector("[data-feed-counter]");
      const btnPrev = root.querySelector("[data-feed-prev]");
      const btnNext = root.querySelector("[data-feed-next]");
      const btnToggle = root.querySelector("[data-feed-toggle]");
      const iconPause = root.querySelector("[data-feed-icon-pause]");
      const iconPlay = root.querySelector("[data-feed-icon-play]");

      let items = [];
      let index = 0;
      let timer = null;
      const period = 8000; // ms

      function applyTickerAnimation() {
        if (!textEl) return;
        textEl.classList.remove("live-feed-animate");
        void textEl.offsetWidth; // reflow
        textEl.classList.add("live-feed-animate");
      }

      function render() {
        const hasItems = items.length > 0;

        if (!hasItems) {
          root.style.display = "none";
          root.classList.add("live-feed--empty");
          root.dataset.feedCount = "0";

          if (lineEmpty) lineEmpty.classList.add("d-none");
          if (lineLive) lineLive.classList.add("d-none");
          return;
        }

        root.style.display = ""; // display via CSS
        root.classList.remove("live-feed--empty");
        root.dataset.feedCount = String(items.length);

        if (lineEmpty) lineEmpty.classList.add("d-none");
        if (lineLive) lineLive.classList.remove("d-none");

        const item = items[index] || items[0];

        if (titleEl) titleEl.textContent = item.title || "Aktivität";
        if (textEl) {
          textEl.textContent = item.text || "";
          applyTickerAnimation();
        }
        if (pillEl) pillEl.textContent = item.badge || item.type_label || "";
        if (timeEl) timeEl.textContent = item.when_human || "";
        if (counterEl)
          counterEl.textContent = `${index + 1} / ${items.length}`;
      }

      function go(step) {
        if (!items.length) return;
        index = (index + step + items.length) % items.length;
        render();
      }

      function start() {
        if (timer || !items.length) return;
        root.classList.remove("live-feed--paused");
        if (iconPause) iconPause.classList.remove("d-none");
        if (iconPlay) iconPlay.classList.add("d-none");
        timer = setInterval(() => go(1), period);
      }

      function pause() {
        if (!timer) return;
        clearInterval(timer);
        timer = null;
        root.classList.add("live-feed--paused");
        if (iconPause) iconPause.classList.add("d-none");
        if (iconPlay) iconPlay.classList.remove("d-none");
      }

      function setItems(nextItems) {
        items = Array.isArray(nextItems) ? nextItems : [];
        index = 0;
        if (items.length) start();
        else pause();
        render();
      }

      async function loadForTuple(
        customerId,
        alternativeId,
        productId,
        leadProductId
      ) {
        if (!customerId) {
          setItems([]);
          return;
        }

        const params = new URLSearchParams({ customer_id: customerId });
        if (alternativeId) params.set("alternative_id", alternativeId);
        if (productId) params.set("product_id", productId);
        if (leadProductId) params.set("lead_product_id", leadProductId);

        try {
          const res = await safeFetchJSON(
            `${APP.endpoints.liveFeed}?${params.toString()}`,
            { method: "GET", retries: 1 }
          );
          if (res && Array.isArray(res.items) && res.items.length) {
            setItems(res.items);
          } else {
            setItems([]);
          }
        } catch (e) {
          console.error("LiveFeed error", e);
          setItems([]);
        }
      }

      // Controls (do not bubble up to card click)
      btnPrev?.addEventListener("click", (e) => {
        e.stopPropagation();
        pause();
        go(-1);
      });
      btnNext?.addEventListener("click", (e) => {
        e.stopPropagation();
        pause();
        go(1);
      });
      btnToggle?.addEventListener("click", (e) => {
        e.stopPropagation();
        if (timer) pause();
        else start();
      });

      // initial: hidden
      setItems([]);

      function getItems() {
        return items.slice();
      }

      return { setItems, loadForTuple, getItems };
    }

    function getInstance(root) {
      if (!root) return null;
      let inst = registry.get(root);
      if (!inst) {
        inst = createInstance(root);
        registry.set(root, inst);
      }
      return inst;
    }

    function loadForCard(card) {
      if (!card) return;
      const root = card.querySelector("[data-feed-root]");
      if (!root) return;

      const inst = getInstance(root);
      inst.loadForTuple(
        card.dataset.customerId,
        card.dataset.alternativeId,
        card.dataset.productId,
        card.dataset.leadProductId
      );
    }

    function bootstrapAllCards() {
      const cards = qsa("#kanban .card");
      if (!cards.length) return;

      let i = 0;
      const BATCH = 4;

      (function pump() {
        const slice = cards.slice(i, (i += BATCH));
        slice.forEach(loadForCard);
        if (i < cards.length) {
          requestIdleCallback(pump);
        }
      })();
    }

    function bootstrapFromFirstCard() {
      const first = qs("#kanban .card");
      if (!first) return;
      loadForCard(first);
    }

    function getItemsForCard(card) {
      if (!card) return [];
      const root = card.querySelector("[data-feed-root]");
      if (!root) return [];
      const inst = getInstance(root);
      if (!inst || !inst.getItems) return [];
      return inst.getItems();
    }

    return {
      loadForCard,
      bootstrapAllCards,
      bootstrapFromFirstCard,
      getItemsForCard,
    };
  })();

  /* ================= Live Feed Modal (Full List + Filter) ================== */
  const LiveFeedModal = (() => {
    const modal     = document.getElementById("liveFeedModal");
    const backdrop  = document.getElementById("liveFeedModalBackdrop");
    const listEl    = document.getElementById("liveFeedModalList");
    const titleEl   = document.getElementById("liveFeedModalTitle");
    const subEl     = document.getElementById("liveFeedModalSubtitle");
    const countEl   = document.getElementById("liveFeedModalCount");
    const closeBtn  = document.getElementById("liveFeedModalClose");
    const filtersEl = document.getElementById("liveFeedTypeFilters");

    let allItems   = [];
    let typeFilter = "all";

    if (!modal || !backdrop) {
      return {
        openForCard: () => {},
      };
    }

    function applyFilter(items) {
      if (typeFilter === "all") return items;
      return items.filter((it) => it.type === typeFilter);
    }

    function fmtDate(iso) {
      if (!iso) return "";
      try {
        return new Date(iso).toLocaleString("de-DE");
      } catch {
        return iso;
      }
    }

    function render() {
      const items = applyFilter(allItems);
      const total = allItems.length;
      const visible = items.length;

      if (countEl) {
        countEl.textContent = `${visible} von ${total} Einträgen`;
      }

      if (!items.length) {
        listEl.innerHTML = `
          <div class="lfm-empty">
            Keine Aktivitäten für diesen Filter.
          </div>
        `;
        return;
      }

      const html = items
        .map((i) => {
          const typeCls =
            i.type === "task"
              ? "task"
              : i.type === "appointment"
              ? "appointment"
              : i.type === "ticket"
              ? "ticket"
              : "";

          const typeLabel = i.type_label || i.type || "Aktivität";

          const whenMain = i.when_human || "";
          const whenAbs  = fmtDate(i.when);

          let linkHtml = "";
          if (i.link) {
            linkHtml = `<a href="${i.link}" target="_blank" class="lfm-item-link">
                          <i class="feather icon-external-link mr-25"></i> Details
                        </a>`;
          }

          return `
            <div class="lfm-item">
              <div class="lfm-item-type ${typeCls}">
                ${typeLabel}
              </div>

              <div class="lfm-item-main">
                <div class="lfm-item-title">${i.title || "Aktivität"}</div>
                <div class="lfm-item-sub">${i.text || ""}</div>
                <div class="lfm-item-meta">
                  ${
                    i.badge
                      ? `<span class="lfm-item-badge">${i.badge}</span>`
                      : ""
                  }
                  ${
                    i.owner_name
                      ? `<span><i class="feather icon-user mr-25"></i>${i.owner_name}</span>`
                      : ""
                  }
                  ${
                    i.priority
                      ? `<span><i class="feather icon-flag mr-25"></i>${i.priority}</span>`
                      : ""
                  }
                  ${linkHtml}
                </div>
              </div>

              <div class="lfm-item-time">
                <span>${whenMain || "–"}</span>
                <span>${whenAbs || ""}</span>
              </div>
            </div>
          `;
        })
        .join("");

      listEl.innerHTML = html;
    }

    function open(items, card) {
      allItems   = Array.isArray(items) ? items.slice() : [];
      typeFilter = "all";

      const name =
        card?.querySelector(".card-header strong")?.textContent?.trim() ||
        "Kunde";
      const stage = card?.dataset.stage || "";

      if (titleEl) titleEl.textContent = "Aktivitäten";
      if (subEl)
        subEl.textContent = `${name} • ${stage ? stage.toUpperCase() : ""}`;

      if (filtersEl) {
        filtersEl.querySelectorAll(".lfm-filter-btn").forEach((btn) => {
          btn.classList.toggle("is-active", btn.dataset.type === "all");
        });
      }

      render();

      modal.style.display = "flex";
      backdrop.style.display = "block";
      document.body.style.overflow = "hidden";
    }

    function close() {
      modal.style.display = "none";
      backdrop.style.display = "none";
      document.body.style.overflow = "";
    }

    backdrop.addEventListener("click", close);
    closeBtn?.addEventListener("click", close);

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && modal.style.display !== "none") {
        close();
      }
    });

    if (filtersEl) {
      filtersEl.addEventListener("click", (e) => {
        const btn = e.target.closest(".lfm-filter-btn");
        if (!btn) return;

        const type = btn.dataset.type || "all";
        typeFilter = type;

        filtersEl.querySelectorAll(".lfm-filter-btn").forEach((b) =>
          b.classList.toggle("is-active", b === btn)
        );

        render();
      });
    }

    function openForCard(card) {
      if (!card) return;
      const items = LiveFeed.getItemsForCard(card) || [];

      if (!items.length) {
        LiveFeed.loadForCard(card);
        setTimeout(() => {
          const again = LiveFeed.getItemsForCard(card) || [];
          open(again, card);
        }, 250);
      } else {
        open(items, card);
      }
    }

    return { openForCard };
  })();

  /* -------------------------- LiveFeed Click Hooks ------------------------- */

  // When a Kanban card background is clicked (not actions/menus), load its feed
  document.addEventListener("click", (e) => {
    const card = e.target.closest("#kanban .card");
    if (!card) return;

    if (e.target.closest(".kb-menu, .card-actions, [data-act], [data-run]")) {
      return;
    }

    LiveFeed.loadForCard(card);
  });

  // Open LiveFeedModal when the expand button in a card feed is clicked
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-feed-open-modal]");
    if (!btn) return;

    const card = btn.closest(".card");
    if (!card) return;

    LiveFeedModal.openForCard(card);
  });

  // After initial load, try to bootstrap for existing cards
  requestIdleCallback(() => {
    LiveFeed.bootstrapAllCards();
  });

  /* ------------------------------- Expose Core ----------------------------- */
  window.LeadUI = {
    APP,
    State,

    utils: {
      qs,
      qsa,
      CSRF,
      fmtDE,
      featherRefreshSoon,
      shortNum,
      canonicalStage,
      stageFilterExcludes,
      saveToLocal,
      restoreFromLocal,
      syncURL,
      initFromURL,
      closeOverlays,
      enforceActionVisibility,
      isBackward,
      stageRank,
    },

    net: { safeFetchJSON, postJSON, cancel },

    filters: {
      initSelect2,
      getFilterValues,
      updateFilterBadges,
      buildFilterQS,
      Drawer,
    },

    kanban: {
      ensureColumns,
      clearColumns,
      colContent,
      updateCounts,
      statusBadge,
      buildStatusBlock,
      applyRunStateUI,
      cardId,
      cardHTML,
      mountOrUpdateCard,
      renderKanbanDiff,
      renderKanbanIncremental,
      autoChunk,
    },

    notes: {
      openNotesDrawerFor,
      updateNoteBadgesForVisibleCards,
    },

    partials: {
      fetchArchiveTab,
      fetchJunkTab,
      fetchTicketsTab: async () => {},
      fetchInvestmentTab: async () => {},
    },

    liveFeed: LiveFeed,
    liveFeedModal: LiveFeedModal,
  };
})();
</script>


<script>
/* =============================================================================
 * LeadUI – Interactions & Boot (Segment 2/2)
 * - Selection + Drag & Drop
 * - Stage-change flow (confirm + optional Quill note)
 * - List rendering + pagination (+ LiveFeed own row under each list row)
 * - Fetchers (Kanban + List)
 * - All event bindings, keyboard shortcuts
 * - Bootstrap on DOMContentLoaded
 * =============================================================================*/
(function () {
  "use strict";

  const { APP, State, utils, net, filters, kanban, notes, partials, liveFeed } =
    window.LeadUI;

  const {
    qs,
    qsa,
    canonicalStage,
    featherRefreshSoon,
    shortNum,
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
    mountOrUpdateCard,
    renderKanbanDiff,
    renderKanbanIncremental,
    autoChunk,
  } = kanban;

  /* ========================================================================== */
  /* Selection + Drag & Drop (KANBAN)                                          */
  /* ========================================================================== */
  function onCardSelect(e, card) {
    if (e.ctrlKey || e.metaKey) {
      card.classList.toggle("selected");
      if (State.selectedIds.has(card.id)) {
        State.selectedIds.delete(card.id);
      } else {
        State.selectedIds.add(card.id);
      }
    } else {
      qsa(".card.selected").forEach((c) => c.classList.remove("selected"));
      State.selectedIds.clear();
      card.classList.add("selected");
      State.selectedIds.add(card.id);
    }
  }

  function onDragStart(e, card) {
    let ids = Array.from(State.selectedIds);
    if (!State.selectedIds.has(card.id)) ids = [card.id];
    e.dataTransfer.setData("text", JSON.stringify(ids));
  }

  document.addEventListener("mousedown", (e) => {
    const card = e.target.closest("#kanban .card");
    if (!card) return;
    if (!card._selBound) {
      card._selBound = true;
      card.addEventListener("click", (ev) => onCardSelect(ev, card));
      card.addEventListener("dragstart", (ev) => onDragStart(ev, card));
    }
  });

  /* ========================================================================== */
  /* Ticket & Investment tabs partial loaders                                  */
  /* ========================================================================== */

  partials.fetchTicketsTab = async function (qsStr = "") {
    const pane = document.querySelector("#ticket");
    if (!pane) return;

    const url = `${APP.endpoints.tickets}${qsStr ? `?${qsStr}` : ""}`;

    try {
      const res = await fetch(url, {
        headers: {
          Accept: "text/html",
          "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
      });

      const html = await res.text();
      pane.innerHTML = html;

      const totalNode =
        pane.querySelector("[data-ticket-total]") ||
        pane.querySelector("[data-total]");

      const total = totalNode
        ? Number(
            totalNode.getAttribute("data-ticket-total") ||
              totalNode.getAttribute("data-total") ||
              totalNode.dataset.ticketTotal ||
              totalNode.dataset.total ||
              0
          )
        : 0;

      const badge = document.querySelector("#tabCountTicket");
      if (badge) badge.textContent = String(total);
    } catch (e) {
      console.error("Ticket partial load failed:", e);
    }
  };

  partials.fetchInvestmentTab = async function (qsStr = "") {
    const pane = document.querySelector("#investment");
    if (!pane) return;

    const url = `${APP.endpoints.investment}${qsStr ? `?${qsStr}` : ""}`;

    try {
      const res = await fetch(url, {
        headers: {
          Accept: "text/html",
          "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
      });

      const html = await res.text();
      pane.innerHTML = html;

      const inner = pane.querySelector("#investmentInner");
      const total =
        inner?.getAttribute("data-investment-total") ||
        inner?.dataset.investmentTotal ||
        0;

      const badge = document.querySelector("#tabCountInvestment");
      if (badge) badge.textContent = String(total);
    } catch (e) {
      console.error("investment tab load failed", e);
    }
  };

  /* ========================================================================== */
  /* Stage-change flow helpers                                                 */
  /* ========================================================================== */

  async function confirmStageChange(newStage, currentStage) {
    const labelNew = APP.stageNames?.[newStage] || newStage;
    const labelCur = APP.stageNames?.[currentStage] || currentStage || "—";

    const step1 = await Swal.fire({
      title: "Phase ändern?",
      text: `Von „${labelCur}“ zu „${labelNew}“ wechseln?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ja",
    });
    if (!step1.isConfirmed) return { ok: false };

    if (currentStage && isBackward(currentStage, newStage)) {
      const warn = await Swal.fire({
        title: "Achtung – Rückwärtswechsel",
        html: "Bitte stornieren/prüfen Sie Details (Positionen, Termine, Aufträge) vor dem Rücksprung.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Weiter",
      });
      if (!warn.isConfirmed) return { ok: false };

      const reasonPrompt = await Swal.fire({
        title: "Grund erforderlich",
        input: "textarea",
        inputPlaceholder: "Grund für Rückwärtswechsel…",
        inputAttributes: { "aria-label": "Grund" },
        inputValidator: (v) =>
          !v?.trim() ? "Bitte Grund eingeben" : undefined,
        showCancelButton: true,
        confirmButtonText: "Speichern",
      });
      if (!reasonPrompt.isConfirmed) return { ok: false };
      return {
        ok: true,
        reasonHTML: (reasonPrompt.value || "").replace(/\n/g, "<br>"),
        backward: true,
      };
    }

    const maybeNote = await promptOptionalNote("quillStage");
    if (maybeNote === null) return { ok: false };
    return { ok: true, reasonHTML: maybeNote || "", backward: false };
  }

  function setTabCount(selector, n) {
    const el = document.querySelector(selector);
    if (el) el.textContent = String(Number(n || 0));
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
    if (Array.isArray(leads))
      setTabCount("#tabCountKanban", leads.length);
  }

  function refreshArchiveAndJunk(qsStr) {
    partials.fetchArchiveTab(qsStr);
    partials.fetchJunkTab(qsStr);
    window.LeadUI?.partials?.fetchTicketsTab?.(qsStr);
  }

  function quillSwalOpts(id) {
    return {
      title: "Notiz (optional)",
      html: `<div id="${id}" style="height:170px;"></div>`,
      showCancelButton: true,
      confirmButtonText: "Speichern",
      focusConfirm: false,
      allowEnterKey: false,
      zIndex: 200000,
      didOpen: () => {
        if (window.Quill) {
          const q = new Quill(`#${id}`, { theme: "snow" });
          window[id] = q;
          setTimeout(() => q.focus(), 0);
        }
      },
      preConfirm: () => window[id]?.root?.innerHTML || "",
    };
  }

  async function promptOptionalNote(id) {
    const r = await Swal.fire(quillSwalOpts(id));
    return r.isConfirmed ? r.value || "" : null;
  }

  async function applyStageChange({
    customerId,
    alternativeId,
    productId,
    leadProductId,
    newStage,
    noteHTML,
  }) {
    const url = `${APP.endpoints.changeStage}/${encodeURIComponent(
      customerId
    )}/${encodeURIComponent(alternativeId)}/${encodeURIComponent(productId)}`;
    const payload = {
      stage: newStage,
      description: noteHTML || "",
      lead_product_id: Number(leadProductId || 0) || undefined,
    };
    const data = await postJSON(url, payload);
    if (!data?.success) throw new Error(data?.message || "Fehler");
    return data;
  }

  function refreshCardStatus(card, overrides = {}) {
    const s = canonicalStage(
      overrides.stage ||
        card.dataset.stage ||
        card.closest(".column")?.id ||
        "lead"
    );
    const ws = (
      overrides.work_status ||
      card.dataset.runState ||
      "playing"
    ).toLowerCase();
    const latest_phase =
      overrides.latest_phase ?? card.dataset.latestPhase ?? "-";
    const latest_activity =
      overrides.latest_activity ??
      card.dataset.latestActivity ??
      "-";
    const stamp =
      overrides.updated_at ||
      card.dataset.updatedAt ||
      card.dataset.doneDate ||
      new Date().toISOString();

    card.dataset.stage = s;
    if (overrides.latest_phase != null)
      card.dataset.latestPhase = overrides.latest_phase;
    if (overrides.latest_activity != null)
      card.dataset.latestActivity = overrides.latest_activity;
    if (overrides.updated_at != null)
      card.dataset.updatedAt = overrides.updated_at;

    const old = card.querySelector(".kb-status");
    if (old)
      old.outerHTML = buildStatusBlock({
        stage: s,
        work_status: ws,
        latest_phase,
        latest_activity,
        updated_at: stamp,
        done_date: stamp,
      });

    applyRunStateUI(
      card,
      ["playing", "paused", "stopped"].includes(ws) ? ws : "playing"
    );
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
      }
      refreshCardStatus(card, {
        stage: newStage,
        updated_at: new Date().toISOString(),
      });
      card.classList.remove("selected");
      State.selectedIds.delete(card.id);
    }
    updateCounts();
  }

  /* ========================================================================== */
  /* Kanban Drop handler                                                       */
  /* ========================================================================== */

  async function onDrop(e) {
    e.preventDefault();
    const ids = JSON.parse(e.dataTransfer.getData("text") || "[]");
    if (!ids.length) return;

    const col = e.target.closest(".column");
    if (!col) return;
    const newStage = canonicalStage(col.id);

    const card = document.getElementById(ids[0]);
    if (!card) return;

    const customerId = card.dataset.customerId;
    const alternativeId = card.dataset.alternativeId;
    const productId = card.dataset.productId;
    const leadProductId = card.dataset.leadProductId;

    const currentStage = canonicalStage(card.closest(".column")?.id || "");
    if (currentStage && currentStage === newStage) return;

    try {
      const confirm = await confirmStageChange(newStage, currentStage);
      if (!confirm.ok) return;

      await applyStageChange({
        customerId,
        alternativeId,
        productId,
        leadProductId,
        newStage,
        noteHTML: confirm.reasonHTML,
      });

      moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
      enforceActionVisibility(card);
      silentRefreshBoth();
      Swal.fire("OK", "Phase aktualisiert.", "success");
    } catch (e) {
      Swal.fire("Fehler", e.message || "Serverfehler.", "error");
    }
  }

  document.addEventListener("dragover", (e) => {
    const col = e.target.closest(".column");
    if (col) e.preventDefault();
  });
  document.addEventListener("drop", (e) => {
    const col = e.target.closest(".column");
    if (!col) return;
    onDrop(e);
  });

  /* ========================================================================== */
  /* List rendering (WITH Live Feed own row under each row)                    */
  /* ========================================================================== */

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

  function priorityMeta(raw) {
    const p = String(raw || "normal").toLowerCase();
    if (p === "high" || p === "urgent")
      return {
        label: "Hoch",
        cls: "prio-high",
        icon: "alert-triangle",
      };
    if (p === "low")
      return {
        label: "Niedrig",
        cls: "prio-low",
        icon: "arrow-down-circle",
      };
    return { label: "Normal", cls: "prio-normal", icon: "circle" };
  }

  function employeeCellHTML(lead) {
    const office = lead?.employee || null;
    // try both notations, depending on how your API sends it
    const field =
      lead?.field_employee ||
      lead?.fieldEmployee ||
      null;

    if (!office && !field) {
      return "<small>&ndash;</small>";
    }

    const chunks = [];

    if (office && (office.name || office.lastname)) {
      chunks.push(`
        <div class="d-flex align-items-center mb-1">
          <img src="/images/employee/${office.image || ""}"
              width="30" height="30"
              class="rounded-circle mr-1" alt="">
          <div>
            <div><strong>${office.lastname || ""}</strong> ${office.name || ""}</div>
            <small class="text-muted">Innendienst</small>
          </div>
        </div>
      `);
    }

    if (field && (field.name || field.lastname)) {
      chunks.push(`
        <div class="d-flex align-items-center">
          <img src="/images/employee/${field.image || ""}"
              width="26" height="26"
              class="rounded-circle mr-1" alt="">
          <div>
            <div><strong>${field.lastname || ""}</strong> ${field.name || ""}</div>
            <small class="text-muted">Außendienst</small>
          </div>
        </div>
      `);
    }

    return chunks.join("");
  }


  function listFeedHTML() {
    return `
      <div class="live-feed-bar list-live-feed card-live-feed"
           data-feed-root
           data-feed-count="0"
           style="display:none; margin-top:0.4rem;">
        <div class="live-feed-left">
          <div class="live-feed-icon">
            <i class="feather icon-zap"></i>
          </div>
        </div>
        <div class="live-feed-body">
          <div class="live-feed-line d-none" data-feed-empty>
            <span class="live-feed-title">Keine Aktivitäten</span>
            <span class="live-feed-dot">•</span>
            <span class="live-feed-text">Noch keine Termine oder Aufgaben.</span>
          </div>
          <div class="live-feed-line d-none" data-feed-line>
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
          <button type="button"
                  class="live-feed-btn"
                  title="Zurück"
                  data-feed-prev>
            <i class="feather icon-skip-back"></i>
          </button>
          <button type="button"
                  class="live-feed-btn"
                  title="Pause / Abspielen"
                  data-feed-toggle>
            <i class="feather icon-pause" data-feed-icon-pause></i>
            <i class="feather icon-play d-none" data-feed-icon-play></i>
          </button>
          <button type="button"
                  class="live-feed-btn"
                  title="Weiter"
                  data-feed-next>
            <i class="feather icon-skip-forward"></i>
          </button>
        </div>
      </div>
    `;
  }

  function buildRowHTML(lead) {
    const s = canonicalStage(lead.stage);
      const [txt, tone, extra] = (function (stage) {
      if (["lead", "offer"].includes(stage))
        return ["Offen", "warning", "text-dark"];

      if (["deal", "project", "completed"].includes(stage))
        return ["Zusage", "success", ""];

      return ["Absage", "danger", ""];
    })(s);


    const pr = priorityMeta(lead.priority);
    const created = lead.created_at
      ? new Date(lead.created_at).toLocaleDateString("de-DE")
      : "-";
    const interest = interestIcons[lead.interest] || null;
    const translatedPhase =
      servicesMap[lead.phase_section_title] ??
      servicesMap[lead.service] ??
      null;
    const stageOptions = Object.entries(APP.stageNames)
      .map(
        ([k, l]) =>
          `<option value="${k}" ${s === k ? "selected" : ""}>${l}</option>`
      )
      .join("");

    return `
      <!-- MAIN DATA ROW -->
      <tr id="row-${lead.lead_product_id}"
          data-customer-id="${lead.customer_id}"
          data-alternative-id="${lead.alternative_id}"
          data-product-id="${lead.product_id}"
          data-lead-product-id="${lead.lead_product_id}"
          data-employee-id="${lead.employee?.employee_id ?? 0}"
          data-service="${lead.service || "complete"}"
          data-service-id="${lead.service_id ?? 0}"
          data-department-id="${lead.department_id ?? 0}">
        <td>
          ${created}
          <div class="d-flex align-items-center gap-2 mt-1">
            <span class="tooltip-trigger position-relative">
              <i class="feather icon-${pr.icon} prio-dot ${pr.cls}"></i>
              <span class="custom-tooltip">Priorität: ${pr.label}</span>
            </span>
          </div>
        </td>
        <td>
          <strong>${lead.customer_lastname ?? ""}</strong> ${
            lead.customer_name ?? ""
          }
        </td>
        <td><i class="feather icon-map-pin"></i> ${lead.city ?? ""}</td>
        <td>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="d-flex align-items-center">
              <img src="/images/icons/produkt.svg" style="width:26px" class="mr-1" alt="">
              <span>${lead.initial ?? ""}</span>
            </div>
            ${
              lead.department_name
                ? `<span class="tooltip-trigger position-relative">
                     <img src="/images/icons/abteilung.svg" style="width:30px" alt="">
                     <span class="custom-tooltip">${lead.department_name}</span>
                   </span>`
                : ""
            }
            ${
              translatedPhase
                ? `<span class="tooltip-trigger position-relative">
                     <img src="/images/icons/dienstleistung.svg" style="width:33px" alt="">
                     <span class="custom-tooltip">${translatedPhase}</span>
                   </span>`
                : ""
            }
            ${
              interest
                ? `<span class="tooltip-trigger position-relative">
                     <img src="/images/icons/${interest.icon}" style="width:20px" alt="">
                     <span class="custom-tooltip">${interest.label}</span>
                   </span>`
                : ""
            }
          </div>
        </td>
        <td>${employeeCellHTML(lead)}</td>
        <td>
          <div><span class="badge bg-${tone} ${extra}">${txt}</span></div>
          ${
            lead.latest_phase || lead.latest_activity || lead.done_date
              ? `<div class="small mt-1">
                   <i class="feather icon-box"></i> ${
                     lead.latest_phase ?? "-"
                   }<br>
                   <i class="feather icon-check-circle"></i> ${
                     lead.latest_activity ?? "-"
                   }<br>
                   <i class="feather icon-clock"></i> ${new Date(
                     lead.done_date ?? lead.updated_at
                   ).toLocaleString("de-DE")}
                 </div>`
              : ""
          }
        </td>
        <td>
          <select class="form-control stage-select" data-id="${
            lead.lead_product_id
          }">
            ${stageOptions}
          </select>
        </td>
        <td>
          <button class="btn btn-outline-secondary btn-sm"
                  data-open-notes
                  data-customer="${lead.customer_id}"
                  data-alt="${lead.alternative_id}"
                  data-product="${lead.product_id}"
                  title="Notizen">
            Notizen
          </button>
          <a href="/new_lead_profile/${
            lead.customer_id
          }" class="btn btn-outline-primary btn-sm" title="Profil">
            Profil
          </a>
          <a href="/lead/process/history/${lead.customer_id}/${
      lead.alternative_id
    }/${lead.product_id}"
             class="btn btn-outline-primary btn-sm"
             data-lh-history>
            Verlauf
          </a>
        </td>
      </tr>

      <!-- LIVE FEED ROW -->
      <tr class="list-feed-row"
          data-customer-id="${lead.customer_id}"
          data-alternative-id="${lead.alternative_id}"
          data-product-id="${lead.product_id}"
          data-lead-product-id="${lead.lead_product_id}"
          data-employee-id="${lead.employee?.employee_id ?? 0}"
          data-service="${lead.service || "complete"}"
          data-service-id="${lead.service_id ?? 0}"
          data-department-id="${lead.department_id ?? 0}">
        <td colspan="8">
          ${listFeedHTML()}
        </td>
      </tr>
    `;
  }

  function runIdle(fn) {
    if ("requestIdleCallback" in window) {
      window.requestIdleCallback(fn);
    } else {
      window.setTimeout(fn, 0);
    }
  }

  // ⬇ REPLACE old bootstrapListLiveFeed with this:
  function bootstrapListLiveFeed(container) {
    if (!liveFeed || typeof liveFeed.loadForCard !== "function") return;

    const root = container || document;
    const rows = root.querySelectorAll("tr.list-feed-row[data-customer-id]");
    if (!rows.length) return;

    let i = 0;
    const BATCH = 4;

    (function pump() {
      const slice = Array.prototype.slice.call(rows, i, i + BATCH);
      i += BATCH;
      slice.forEach((row) => liveFeed.loadForCard(row));
      if (i < rows.length) runIdle(pump);
    })();
  }

  // Make it available globally for partials (archive/junk)
  window.LeadUI.bootstrapListLiveFeed = bootstrapListLiveFeed;


  function updateListView(leads, meta) {
    const tbody = qs("#kanbanTableBody");
    if (!tbody) return;

    if (!leads.length) {
      tbody.innerHTML =
        '<tr><td colspan="8" class="text-center">Keine Ergebnisse gefunden</td></tr>';
      syncSummary(meta);
      return;
    }

    const tmp = document.createElement("tbody");
    tmp.innerHTML = leads.map(buildRowHTML).join("");
    tbody.innerHTML = "";
    tbody.append(...tmp.childNodes);

    syncSummary(meta);
    featherRefreshSoon();
    bootstrapListLiveFeed(tbody); // ⬅ CHANGED
  }

  function normalizePaginationMeta(input) {
    if (!input) return null;
    const direct = input.meta || input.pagination || input;
    const cp = Number(
      direct.current_page ?? direct.currentPage ?? direct.page ?? 1
    );
    const lp = Number(
      direct.last_page ??
        direct.lastPage ??
        (direct.total && direct.per_page
          ? Math.ceil(direct.total / direct.per_page)
          : 1)
    );
    return { current_page: cp, last_page: Math.max(1, lp) };
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

    const add = (p, l, dis = false, act = false) => {
      if (dis) {
        html += `<li class="page-item disabled"><span class="page-link">${l}</span></li>`;
      } else if (act) {
        html += `<li class="page-item active"><span class="page-link">${l}</span></li>`;
      } else {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${p}">${l}</a></li>`;
      }
    };

    add(current_page - 1, "«", current_page === 1);

    const win = 2;
    const st = Math.max(1, current_page - win);
    const en = Math.min(last_page, current_page + win);

    if (st > 1) {
      add(1, "1", false, current_page === 1);
      if (st > 2)
        html +=
          '<li class="page-item disabled"><span class="page-link">…</span></li>';
    }

    for (let p = st; p <= en; p++) {
      add(p, String(p), false, p === current_page);
    }

    if (en < last_page) {
      if (en < last_page - 1)
        html +=
          '<li class="page-item disabled"><span class="page-link">…</span></li>';
      add(last_page, String(last_page), false, current_page === last_page);
    }

    add(current_page + 1, "»", current_page === last_page);

    wrap.innerHTML = html + "</ul></nav>";
  }

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

    setHTML(
      "#statusOffen",
      `${data?.statusCounts?.offen ?? 0} <small>(${
        data?.statusPercentages?.offen ?? 0
      }%)</small>`
    );
    setHTML(
      "#statusZusage",
      `${data?.statusCounts?.zusage ?? 0} <small>(${
        data?.statusPercentages?.zusage ?? 0
      }%)</small>`
    );
    setHTML(
      "#statusAbsage",
      `${data?.statusCounts?.absage ?? 0} <small>(${
        data?.statusPercentages?.absage ?? 0
      }%)</small>`
    );

    setTxt("#countCustomers", data?.totalCustomers);
    setTxt("#countProducts", data?.totalProducts);
    setTxt("#countDepartments", data?.totalDepartments);
    setTxt("#countEmployees", data?.totalEmployees);
  }

  /* ========================================================================== */
  /* Fetchers (Kanban + List)                                                  */
  /* ========================================================================== */

  function normalizeLead(raw) {
    const pick = (obj, ...keys) => {
      for (const k of keys) {
        const v = obj?.[k];
        if (v !== undefined && v !== null && v !== "") return v;
      }
      return null;
    };
    const latest_phase = pick(
      raw,
      "latest_phase",
      "phase_name",
      "phase_title",
      "phase_section_title"
    );
    const latest_activity = pick(raw, "latest_activity", "activity_title");
    const done_date = pick(raw, "done_date", "updated_at", "history_at");
    const updated_at = pick(raw, "updated_at", done_date);
    return { ...raw, latest_phase, latest_activity, done_date, updated_at };
  }

  function ensureLoadedMap() {
    if (!State.loaded || typeof State.loaded !== "object") {
      State.loaded = { kanban: false, list: false };
    } else {
      if (!("kanban" in State.loaded)) State.loaded.kanban = false;
      if (!("list" in State.loaded)) State.loaded.list = false;
    }
  }

  function fetchKanbanView(qsStr) {
    ensureLoadedMap();
    const signal = cancel("kanban");
    const board = qs("#kanban");
    if (board && !State.loaded.kanban) {
      board.innerHTML =
        '<div class="p-2 text-muted">Lade Kanban…</div>';
    }

    return safeFetchJSON(
      `${APP.endpoints.kanbanSearch}${qsStr ? `?${qsStr}` : ""}`,
      { signal, retries: 0 }
    )
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
          renderKanbanIncremental(
            State.lastKanbanData,
            autoChunk(),
            () => {
              ensureLoadedMap();
              State.loaded.kanban = true;
              syncTabCountsFromKanban(State.lastKanbanData);
            }
          );
        } else {
          renderKanbanDiff(State.lastKanbanData);
          syncTabCountsFromKanban(State.lastKanbanData);
        }
      })
      .catch((e) => {
        if (e.name !== "AbortError") {
          Swal.fire("Fehler", e.message, "error");
        }
      });
  }

  function fetchListView(qsStr) {
    const signal = cancel("list");
    const tbody = qs("#kanbanTableBody");
    if (tbody && !State.loaded.list) {
      tbody.innerHTML =
        '<tr><td colspan="8" class="text-center text-muted">Lade Liste…</td></tr>';
    }

    return safeFetchJSON(
      `${APP.endpoints.listSearch}${qsStr ? `?${qsStr}` : ""}`,
      { signal, retries: 0 }
    )
      .then((payload) => {
        ensureLoadedMap();
        State.loaded.list = true;

        const leads = Array.isArray(payload?.leads)
          ? payload.leads
          : Array.isArray(payload?.data)
          ? payload.data
          : [];

        updateListView(leads, payload);
        renderPagination(payload.pagination || payload.meta || payload);
        syncTabCountsFromListPayload(payload);
      })
      .catch((e) => {
        if (e.name !== "AbortError") {
          Swal.fire("Fehler", e.message, "error");
          updateListView([], {});
          renderPagination(null);
        }
      });
  }

  refreshArchiveAndJunk(State.filtersQS);

  /* ========================================================================== */
  /* List: stage change                                                        */
  /* ========================================================================== */

  document.addEventListener("change", async (e) => {
    const sel = e.target.closest("select.stage-select");
    if (!sel) return;

    const row = sel.closest("tr");
    const newStage = sel.value;

    const prevIndex = [...sel.options].findIndex(
      (o) => o.defaultSelected
    );
    const oldStage =
      prevIndex >= 0
        ? canonicalStage(sel.options[prevIndex].value)
        : null;

    const customerId = row?.dataset.customerId || sel.dataset.customerId;
    const alternativeId =
      row?.dataset.alternativeId || sel.dataset.alternativeId;
    const productId =
      row?.dataset.productId || sel.dataset.productId;
    const leadProductId =
      sel.dataset.id || row?.id?.split("-")[1];

    try {
      const confirm = await confirmStageChange(newStage, oldStage);
      if (!confirm.ok) {
        sel.selectedIndex = prevIndex;
        return;
      }

      await applyStageChange({
        customerId,
        alternativeId,
        productId,
        leadProductId,
        newStage,
        noteHTML: confirm.reasonHTML,
      });

      const card =
        document.getElementById(`card-${leadProductId}`) ||
        document.querySelector(
          `.card[data-lead-product-id="${leadProductId}"]`
        );

      if (card) {
        moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
        enforceActionVisibility(card);
      }

      sel.querySelectorAll("option").forEach((o) => {
        o.defaultSelected = false;
      });
      sel.options[sel.selectedIndex].defaultSelected = true;

      if (stageFilterExcludes(newStage)) row?.remove();

      silentRefreshBoth();
      Swal.fire("OK", "Phase aktualisiert.", "success");
    } catch (err) {
      sel.selectedIndex = prevIndex;
      Swal.fire("Fehler", err.message || "Serverfehler.", "error");
    }
  });

  /* ========================================================================== */
  /* Card actions (KANBAN buttons)                                             */
  /* ========================================================================== */

  document.addEventListener("click", (e) => {
    const actBtn = e.target.closest(
      ".card .card-actions [data-act],[data-run]"
    );
    if (!actBtn) return;

    const card = actBtn.closest(".card");
    if (!card) return;

    if (actBtn.dataset.run)
      return (async () => {
        const state = actBtn.dataset.run;
        const { value: reason, isConfirmed } = await Swal.fire({
          title: `Grund für ${
            state === "playing"
              ? "Start"
              : state === "paused"
              ? "Pause"
              : "Stopp"
          }`,
          input: "textarea",
          showCancelButton: true,
          confirmButtonText: "Speichern",
          inputValidator: (v) =>
            !v?.trim() ? "Bitte Grund eingeben" : undefined,
        });
        if (!isConfirmed) return;

        const prev = card.dataset.runState || "playing";
        applyRunStateUI(card, state);

        try {
          const res = await postJSON(
            `${APP.endpoints.progress}/${card.dataset.leadProductId}/${state}`,
            { reason }
          );
          if (!res || res.success === false)
            throw new Error(res?.message || "Fehler");

          refreshCardStatus(card, {
            work_status: state,
            updated_at: new Date().toISOString(),
          });
          silentRefreshBoth();
        } catch (err) {
          applyRunStateUI(card, prev);
          Swal.fire(
            "Fehler",
            err.message || "Speichern fehlgeschlagen.",
            "error"
          );
        }
      })();

    const act = actBtn.dataset.act;

    if (act === "profile") {
      return window.location.assign(
        `/new_lead_profile/${card.dataset.customerId}`
      );
    }

    if (act === "edit") {
      return Swal.fire({
        title: "Lead bearbeiten?",
        icon: "question",
        showCancelButton: true,
      }).then((r) => {
        if (r.isConfirmed) {
          window.location.assign(
            `/new_lead_edit/${card.dataset.customerId}/${card.dataset.alternativeId}`
          );
        }
      });
    }

    if (act === "notes") {
      return notes.openNotesDrawerFor(
        card.dataset.customerId,
        card.dataset.alternativeId,
        card.dataset.productId,
        card.querySelector(".card-header strong")?.textContent?.trim()
      );
    }

    if (act === "delete")
      return (async () => {
        if (
          stageRank(canonicalStage(card.dataset.stage)) >=
          stageRank("deal")
        ) {
          return Swal.fire(
            "Gesperrt",
            "Leads ab „Auftrag“ können nicht in Junk verschoben werden.",
            "info"
          );
        }

        const ok = await Swal.fire({
          title: "In Junk verschieben?",
          text: "Dieser Lead wird in den Junk-Bereich verschoben (nicht gelöscht).",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Ja, verschieben",
        });
        if (!ok.isConfirmed) return;

        const note = await Swal.fire(quillSwalOpts("quillJunk"));

        try {
          const url = `${APP.endpoints.changeStage}/${encodeURIComponent(
            card.dataset.customerId
          )}/${encodeURIComponent(
            card.dataset.alternativeId
          )}/${encodeURIComponent(card.dataset.productId)}`;

          const res = await postJSON(url, {
            stage: "junk",
            description: note.isConfirmed ? note.value || "" : "",
            lead_product_id:
              Number(card.dataset.leadProductId || 0) || undefined,
          });
          if (!res?.success) throw new Error(res?.message || "Fehler");

          card.remove();
          updateCounts();
          silentRefreshBoth();
          Swal.fire("Verschoben", "Lead liegt jetzt im Junk.", "success");
        } catch (err) {
          Swal.fire(
            "Fehler",
            err.message || "Verschieben fehlgeschlagen.",
            "error"
          );
        }
      })();

    if (act === "archive")
      return (async () => {
        const ok = await Swal.fire({
          title: "Archivieren?",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Ja",
        });
        if (!ok.isConfirmed) return;

        const note = await Swal.fire(quillSwalOpts("quillArchive"));

        try {
          const url = `${APP.endpoints.changeStage}/${encodeURIComponent(
            card.dataset.customerId
          )}/${encodeURIComponent(
            card.dataset.alternativeId
          )}/${encodeURIComponent(card.dataset.productId)}`;

          const data = await postJSON(url, {
            stage: "archive",
            description: note.isConfirmed ? note.value : "",
            lead_product_id:
              Number(card.dataset.leadProductId || 0) || undefined,
          });

          if (!data.success)
            throw new Error(data.message || "Fehler");

          card.remove();
          updateCounts();
          silentRefreshBoth();

          Swal.fire("Archiviert", "Lead verschoben.", "success");
        } catch (err) {
          Swal.fire(
            "Fehler",
            err.message || "Archivieren fehlgeschlagen.",
            "error"
          );
        }
      })();
  });

  /* ========================================================================== */
  /* Sorting / paging (LIST)                                                   */
  /* ========================================================================== */

  function addPage(qsStr, page) {
    const p = new URLSearchParams(qsStr || "");
    p.set("page", String(page));
    return p.toString();
  }

  function isKanbanActive() {
    return qs("#home")?.classList.contains("active");
  }

  document.addEventListener("click", (e) => {
    const th = e.target.closest("#profile th.sortable");
    if (!th) return;

    const key = th.dataset.sort;
    if (!key) return;

    State.sort =
      State.sort.key === key
        ? { key, dir: State.sort.dir === "asc" ? "desc" : "asc" }
        : { key, dir: "asc" };

    qsa("#profile th.sortable").forEach((h) =>
      h.classList.remove("active", "desc")
    );
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
    const a = e.target.closest(
      "#listPagination a.page-link[data-page]"
    );
    if (!a) return;
    e.preventDefault();

    const p = parseInt(a.getAttribute("data-page"), 10) || 1;
    State.page = p;
    saveToLocal();
    syncURL();

    fetchListView(addPage(State.filtersQS, State.page));
  });

  /* ========================================================================== */
  /* Tabs (Bootstrap)                                                          */
  /* ========================================================================== */

  if (window.jQuery) {
    jQuery('a[data-toggle="tab"][href="#home"]').on(
      "shown.bs.tab",
      function () {
        ensureColumns();
        renderKanbanDiff(State.lastKanbanData);
        featherRefreshSoon();
        enforceActionVisibility();
      }
    );

    jQuery('a[data-toggle="tab"][href="#archive"]').on(
      "shown.bs.tab",
      function () {
        partials.fetchArchiveTab(State.filtersQS);
      }
    );

    jQuery('a[data-toggle="tab"][href="#junk"]').on(
      "shown.bs.tab",
      function () {
        partials.fetchJunkTab(State.filtersQS);
      }
    );

    jQuery('a[data-toggle="tab"][href="#investment"]').on(
      "shown.bs.tab",
      function () {
        partials.fetchInvestmentTab(State.filtersQS);
      }
    );
  }

  document.addEventListener("shown.bs.tab", (e) => {
    const trg = e.target?.getAttribute("href") || "";
    if (trg === "#ticket") {
      const qsStr = window.LeadUI.filters.buildFilterQS();
      window.LeadUI.partials.fetchTicketsTab(qsStr);
    }
  });

  /* ========================================================================== */
  /* Apply / Clear Filters                                                     */
  /* ========================================================================== */

  function setSummaryActive(id) {
    qsa(".summary-card").forEach((c) =>
      c.classList.remove("active")
    );
    if (id) qs("#" + id)?.classList.add("active");
  }

  function applyStatusGroup(g, cardId) {
    State.statusGroup = g;
    State.page = 1;
    State.filtersQS = filters.buildFilterQS();
    saveToLocal();
    syncURL();

    const qsStrWithPage = addPage(State.filtersQS, State.page);

    fetchListView(qsStrWithPage);
    fetchKanbanView(State.filtersQS);
    refreshArchiveAndJunk(State.filtersQS);
    setSummaryActive(cardId || null);
    filters.updateFilterBadges();
  }

  qs("#cardOffen")?.addEventListener("click", () =>
    applyStatusGroup("offen", "cardOffen")
  );
  qs("#cardZusage")?.addEventListener("click", () =>
    applyStatusGroup("zusage", "cardZusage")
  );
  qs("#cardAbsage")?.addEventListener("click", () =>
    applyStatusGroup("absage", "cardAbsage")
  );

  qs("#btnApplyFilters")?.addEventListener("click", () => {
    State.page = 1;
    State.filtersQS = filters.buildFilterQS();
    window.LeadUI.partials.fetchTicketsTab(State.filtersQS);
    window.LeadUI.partials.fetchInvestmentTab(State.filtersQS);
    State.lastAppliedQS = State.filtersQS;
    saveToLocal();
    syncURL();

    const withPage = addPage(State.filtersQS, State.page);
    fetchListView(withPage);
    fetchKanbanView(State.filtersQS);

    refreshArchiveAndJunk(State.filtersQS);
    closeOverlays();
  });

  qs("#btnClearFilters")?.addEventListener("click", () => {
    const form = qs("#kanbanFilterForm");
    if (!form) return;

    form.reset();
    if (window.jQuery)
      jQuery(form).find(".select2").val(null).trigger("change");

    State.statusGroup = null;
    setSummaryActive(null);

    State.page = 1;
    State.filtersQS = filters.buildFilterQS();
    saveToLocal();
    syncURL();
    filters.updateFilterBadges();

    const withPage = addPage(State.filtersQS, State.page);

    fetchListView(withPage);
    fetchKanbanView(State.filtersQS);
    refreshArchiveAndJunk(State.filtersQS);
    window.LeadUI.partials.fetchInvestmentTab(State.filtersQS);
    window.LeadUI.partials.fetchTicketsTab(State.filtersQS);
  });

  /* ========================================================================== */
  /* Live Feed trigger for LIST (optional manual reload on feed row click)     */
  /* ========================================================================== */

  document.addEventListener("click", (e) => {
    const row = e.target.closest(
      "#kanbanTableBody tr.list-feed-row"
    );
    if (!row) return;

    if (e.target.closest("button, a, select")) return;

    if (liveFeed && typeof liveFeed.loadForCard === "function") {
      liveFeed.loadForCard(row);
    }
  });

  /* ========================================================================== */
  /* Keyboard shortcuts                                                        */
  /* ========================================================================== */

  document.addEventListener("keydown", (e) => {
    if (e.ctrlKey && e.key.toLowerCase() === "f") {
      e.preventDefault();
      qs("#btnOpenDrawer")?.click();
    }
    if (e.key === "Escape") {
      closeOverlays();
    }
  });

  /* ========================================================================== */
  /* Silent refresh (keep Kanban + List + Tickets in sync)                     */
  /* ========================================================================== */

  function silentRefreshBoth() {
    const qsStr = State.filtersQS;
    fetchListView(addPage(qsStr, State.page));
    fetchKanbanView(qsStr);
    window.LeadUI.partials.fetchTicketsTab(qsStr);
  }

  window.LeadUI.silentRefreshBoth = silentRefreshBoth;

  /* ========================================================================== */
  /* Bootstrapping                                                             */
  /* ========================================================================== */

  document.addEventListener("DOMContentLoaded", () => {
    featherRefreshSoon();
    filters.initSelect2();
    filters.updateFilterBadges();

    initFromURL();
    if (!location.search) restoreFromLocal();

    State.filtersQS = filters.buildFilterQS();
    saveToLocal();
    syncURL();

    ensureLoadedMap();
    State.loaded.kanban = false;
    State.loaded.list = false;

    fetchListView(addPage(State.filtersQS, State.page));
    fetchKanbanView(State.filtersQS);
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

<!-- Invest sort  -->

<script>
(function() {
  "use strict";

  function parseNumberDE(str) {
    if (!str) return 0;
    str = String(str).trim();
    str = str.replace(/[^0-9,.\-]/g, "");
    str = str.replace(/\./g, "");
    str = str.replace(/,/g, ".");
    const n = parseFloat(str);
    return isNaN(n) ? 0 : n;
  }

  function getCellSortValue(cell, type) {
    if (!cell) return "";
    if (cell.dataset && cell.dataset.sortVal != null && cell.dataset.sortVal !== "") {
      return type === "number"
        ? parseNumberDE(cell.dataset.sortVal)
        : String(cell.dataset.sortVal).toLowerCase();
    }

    const txt = cell.innerText || cell.textContent || "";
    if (type === "number") {
      return parseNumberDE(txt);
    }
    return String(txt).trim().toLowerCase();
  }

  function initInvestmentSorting() {
    const table = document.querySelector("#investmentTable[data-investment-table]");
    if (!table) return;

    const thead = table.tHead;
    const tbody = table.tBodies[0];
    if (!thead || !tbody) return;

    const headers = thead.querySelectorAll("th[data-sort]");
    let current = { index: null, dir: 1 };

    headers.forEach(function(th, index) {
      th.addEventListener("click", function() {
        const type = th.getAttribute("data-sort") || "text";

        if (current.index === index) {
          current.dir = -current.dir;
        } else {
          current.index = index;
          current.dir = 1;
        }

        headers.forEach(function(h) {
          h.classList.remove("sorted-asc", "sorted-desc");
        });
        th.classList.add(current.dir === 1 ? "sorted-asc" : "sorted-desc");

        const rows = Array.from(tbody.querySelectorAll("tr"));
        rows.sort(function(a, b) {
          const aVal = getCellSortValue(a.children[index], type);
          const bVal = getCellSortValue(b.children[index], type);

          if (type === "number") {
            return (aVal - bVal) * current.dir;
          }

          if (aVal < bVal) return -1 * current.dir;
          if (aVal > bVal) return 1 * current.dir;
          return 0;
        });

        rows.forEach(function(row) {
          tbody.appendChild(row);
        });
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initInvestmentSorting);
  } else {
    initInvestmentSorting();
  }
})();
</script>


 
<!-- Aufgabe Script  -->

<script>
(function () {
  "use strict";

  /* ------------------------------------------------
   * Bootstrap from LeadUI (with sane fallbacks)
   * ----------------------------------------------*/
  const root = window.LeadUI || {};
  const { APP = {}, net = {}, utils = {} } = root;

  const {
    qs: leadQs,
    qsa: leadQsa,
    CSRF: leadCSRF,
    featherRefreshSoon: leadFeather,
  } = utils;

  const { safeFetchJSON: leadSafeFetchJSON } = net;

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
    leadFeather ||
    function () {
      /* noop fallback */
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

  /* ------------------------------------------------
   * Helpers
   * ----------------------------------------------*/
  const esc = (s) =>
    String(s ?? "").replace(/[&<>]/g, (m) => {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;" }[m];
    });

  // simple mapping for appointment priority
  const prioMeta = (p) => {
    const v = String(p || "normal").toLowerCase();
    if (v === "high" || v === "urgent") {
      return {
        label: "Hoch",
        cls: "text-danger",
        icon: "icon-alert-triangle",
      };
    }
    if (v === "low") {
      return {
        label: "Niedrig",
        cls: "text-muted",
        icon: "icon-arrow-down-circle",
      };
    }
    return {
      label: "Normal",
      cls: "text-success",
      icon: "icon-circle",
    };
  };

  /* ------------------------------------------------
   * Appointments UI
   * ----------------------------------------------*/
  const UI = {
    _list: [],
    _ctx: null,

    open(customerId, alternativeId, productId, title, contact) {
      const titleEl = qs("#ap-title");
      if (titleEl) {
        titleEl.textContent = title || "Termine";
      }

      const idEl = qs("#ap-id");
      const cEl = qs("#ap-customer_id");
      const aEl = qs("#ap-alternative_id");
      const pEl = qs("#ap-product_id");

      if (idEl) idEl.value = "";
      if (cEl) cEl.value = customerId || "";
      if (aEl) aEl.value = alternativeId || "";
      if (pEl) pEl.value = productId || "";

      // remember which card we are working on
      this._ctx = {
        customerId: customerId || "",
        alternativeId: alternativeId || "",
        productId: productId || "",
      };

      this.resetForm();
      this.prefillFromContact(contact || {});
      this.show();
      this.loadList();
    },

    updateCardBadge() {
      const ctx = this._ctx;
      if (!ctx) return;

      const c = ctx.customerId || "";
      const a = ctx.alternativeId || "";
      const p = ctx.productId || "";
      const count = this._list.length;

      const selector =
        `.card[data-customer-id="${c}"][data-alternative-id="${a}"][data-product-id="${p}"]`;

      qsa(selector).forEach((card) => {
        const btn = card.querySelector('.kb-menu-item[data-menu="termin"]');
        if (!btn) return;

        let pill = btn.querySelector("[data-ap-count]");
        if (!pill) {
          pill = document.createElement("span");
          pill.className = "kb-menu-pill kb-menu-pill--ap";
          pill.setAttribute("data-ap-count", "");
          btn.appendChild(pill);
        }

        pill.textContent = String(count);
        pill.style.display = count ? "inline-flex" : "none";
      });
    },

    show() {
      qs("#ap-backdrop")?.classList.add("show");
      qs("#ap-drawer")?.classList.add("open");
      document.body.style.overflow = "hidden";

      // Address autocomplete is wired elsewhere
      setTimeout(() => {
        try {
          // global helper, if present
          typeof initAddressAutocomplete === "function" &&
            initAddressAutocomplete();
        } catch {
          // ignore
        }
      }, 300);
    },

    hide() {
      // use appointment IDs ✅
      qs("#ap-backdrop")?.classList.remove("show");
      qs("#ap-drawer")?.classList.remove("open");
      document.body.style.overflow = "";
    },

    resetForm() {
      const form = qs("#ap-form");
      if (!form) return;

      form.reset();

      const colorEl = qs("#ap-color");
      if (colorEl) colorEl.value = "#74b2d4";

      const prioEl = qs("#ap-priority");
      if (prioEl) prioEl.value = "normal";

      if (window.jQuery) {
        jQuery("#ap-employee_ids").val(null).trigger("change");
        // Kundensuche ist in deinem Markup bereits versteckt
      }
    },

    prefillFromContact(contact) {
      if (!contact) return;

      const {
        full_address,
        street,
        postcode,
        city,
        phone,
        email,
        latitude,
        longitude,
      } = contact;

      if (full_address) qs("#ap-full_address") && (qs("#ap-full_address").value = full_address);
      if (street) qs("#ap-street") && (qs("#ap-street").value = street);
      if (postcode) qs("#ap-postcode") && (qs("#ap-postcode").value = postcode);
      if (city) qs("#ap-city") && (qs("#ap-city").value = city);
      if (phone) qs("#ap-phone") && (qs("#ap-phone").value = phone);
      if (email) qs("#ap-email") && (qs("#ap-email").value = email);
      if (latitude) qs("#ap-latitude") && (qs("#ap-latitude").value = latitude);
      if (longitude) qs("#ap-longitude") && (qs("#ap-longitude").value = longitude);
    },

    async loadList() {
      const c = qs("#ap-customer_id")?.value;
      const a = qs("#ap-alternative_id")?.value;
      const p = qs("#ap-product_id")?.value;

      const wrap = qs("#ap-list");
      const countEl = qs("#ap-count");

      if (!c) {
        if (wrap) {
          wrap.innerHTML =
            '<div class="text-muted small my-2">Kein Kunde gesetzt – Termin wird trotzdem gespeichert, sobald ein Kunde gewählt ist.</div>';
        }
        if (countEl) countEl.textContent = "0";
        this._list = [];
        return;
      }

      const url =
        `${APP?.endpoints?.appointmentsIndex || ""}?customer_id=${encodeURIComponent(
          c
        )}` +
        (a ? `&alternative_id=${encodeURIComponent(a)}` : "") +
        (p ? `&product_id=${encodeURIComponent(p)}` : "");

      if (!APP?.endpoints?.appointmentsIndex) {
        if (wrap) {
          wrap.innerHTML =
            '<div class="text-danger small my-2">Endpoint für Termine ist nicht konfiguriert.</div>';
        }
        if (countEl) countEl.textContent = "0";
        this._list = [];
        return;
      }

      try {
        const res = await safeFetchJSON(url, { retries: 0 });
        const list = Array.isArray(res?.appointments)
          ? res.appointments
          : [];

        this._list = list || [];

        if (countEl) countEl.textContent = String(this._list.length);
        this.renderList();
        this.updateCardBadge();
      } catch (e) {
        if (wrap) {
          wrap.innerHTML =
            `<div class="text-danger small my-2">Termine konnten nicht geladen werden.<br>${esc(
              e.message || ""
            )}</div>`;
        }
        if (countEl) countEl.textContent = "0";
        this._list = [];
      }
    },

    renderList() {
      const wrap = qs("#ap-list");
      if (!wrap) return;

      wrap.classList.add("ap-grid");

      if (!this._list.length) {
        wrap.innerHTML =
          '<div class="text-muted small my-2">Noch keine Termine für diesen Kunden.</div>';
        return;
      }

      wrap.innerHTML = this._list.map((a) => this.cardHTML(a)).join("");
      featherRefreshSoon();
    },

    cardHTML(a) {
      const date = a.start_date || a.end_date || "";
      const fmtDate = date
        ? String(date).split("-").reverse().join(".")
        : "Kein Datum";

      const startTime = a.start_time ? String(a.start_time).slice(0, 5) : "";
      const endTime = a.end_time ? String(a.end_time).slice(0, 5) : "";

      const timePart = startTime
        ? endTime
          ? `${startTime}–${endTime}`
          : startTime
        : endTime || "";

      const when = timePart ? `${fmtDate} • ${timePart}` : fmtDate;

      const emps =
        Array.isArray(a.employees) && a.employees.length
          ? a.employees
              .map(
                (e) => `
            <span class="ap-emp">
              <img src="/images/employee/${esc(e.image || "")}"
                   alt=""
                   class="ap-emp-img">
              ${esc(e.lastname || "")} ${esc(e.name || "")}
            </span>`
              )
              .join("")
          : '<span class="text-muted small">Keine Mitarbeiter</span>';

      const color = a.color || "#74b2d4";
      const prio = prioMeta(a.priority);
      const prioTag = `
        <span class="small text-nowrap">
          <i class="feather ${prio.icon} ${prio.cls}"></i>
          <span class="${prio.cls}">${prio.label}</span>
        </span>
      `;

      const noteText = a.note != null ? String(a.note) : "";
      const noteBlock = noteText
        ? `<div class="ap-note small mb-1">${esc(noteText).slice(0, 160)}${
            noteText.length > 160 ? "…" : ""
          }</div>`
        : "";

      const typeBadge = a.appointment_type
        ? `<span class="badge badge-light badge-pill mr-1">${esc(
            a.appointment_type
          )}</span>`
        : "";

      const modeBadge = a.contact_mode
        ? `<span class="badge badge-light badge-pill mr-1">${esc(
            a.contact_mode
          )}</span>`
        : "";

      const addrPart = a.full_address
        ? `<span><i class="feather icon-map-pin"></i> ${esc(
            a.full_address
          )}</span>`
        : "";

      return `
        <article class="ap-card" data-ap-id="${a.id}">
          <div class="ap-color" style="background:${color};"></div>
          <div class="ap-main">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <div>
                <div class="ap-title">${esc(a.name || "Ohne Titel")}</div>
                ${typeBadge}
                ${modeBadge}
                ${prioTag}
              </div>
              <div class="ap-actions d-flex">
                <button type="button"
                        class="btn-icon"
                        data-ap-edit="${a.id}"
                        title="Bearbeiten">
                  <i class="feather icon-edit-2"></i>
                </button>
                <button type="button"
                        class="btn-icon"
                        data-ap-del="${a.id}"
                        title="Löschen">
                  <i class="feather icon-trash-2"></i>
                </button>
              </div>
            </div>

            ${noteBlock}

            <div class="ap-meta">
              <span><i class="feather icon-calendar"></i> ${when}</span>
              ${addrPart}
            </div>

            <div class="ap-emps mt-1">${emps}</div>
          </div>
        </article>
      `;
    },

    fillForm(id) {
      const appt = this._list.find(
        (x) => String(x.id) === String(id)
      );
      if (!appt) return;

      const set = (sel, val) => {
        const el = qs(sel);
        if (el) el.value = val ?? "";
      };

      set("#ap-id", appt.id);
      set("#ap-name", appt.name || "");
      set("#ap-note", appt.note || "");
      set("#ap-start_date", appt.start_date || "");
      set("#ap-start_time", appt.start_time || "");
      set("#ap-end_time", appt.end_time || "");
      set("#ap-appointment_type", appt.appointment_type || "");
      set("#ap-contact_mode", appt.contact_mode || "");
      set("#ap-color", appt.color || "#74b2d4");

      const prioEl = qs("#ap-priority");
      if (prioEl) prioEl.value = appt.priority || "normal";

      set("#ap-full_address", appt.full_address || "");
      set("#ap-street", appt.street || "");
      set("#ap-postcode", appt.postcode || "");
      set("#ap-city", appt.city || "");
      set("#ap-phone", appt.phone || "");
      set("#ap-email", appt.email || "");
      set("#ap-latitude", appt.latitude || "");
      set("#ap-longitude", appt.longitude || "");

      if (window.jQuery) {
        const ids = Array.isArray(appt.employees)
          ? appt.employees.map((e) => e.id)
          : [];
        jQuery("#ap-employee_ids").val(ids).trigger("change");
      }
    },

    async submitForm(ev) {
      ev.preventDefault();

      const name = (qs("#ap-name")?.value || "").trim();
      const startDate = qs("#ap-start_date")?.value || "";
      const customerId = qs("#ap-customer_id")?.value || "";

      if (!name) {
        Swal.fire("Fehler", "Titel ist erforderlich.", "error");
        return;
      }
      if (!startDate) {
        Swal.fire("Fehler", "Datum ist erforderlich.", "error");
        return;
      }
      if (!customerId) {
        Swal.fire(
          "Fehler",
          "Bitte einen Kunden wählen (Termin immer über eine Karte öffnen).",
          "error"
        );
        return;
      }

      const employeeIds = window.jQuery
        ? jQuery("#ap-employee_ids").val() || []
        : [];
      if (!employeeIds.length) {
        Swal.fire(
          "Fehler",
          "Bitte mindestens einen Mitarbeiter auswählen.",
          "error"
        );
        return;
      }

      const altId = qs("#ap-alternative_id")?.value || "";
      const prodId = qs("#ap-product_id")?.value || "";

      const payload = {
        customer_id: Number(customerId),
        alternative_id: altId ? Number(altId) : null,
        product_id: prodId ? Number(prodId) : null,

        name,
        note: (qs("#ap-note")?.value || "").trim() || null,
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
        phone: qs("#ap-phone")?.value || null,
        email: qs("#ap-email")?.value || null,
        latitude: qs("#ap-latitude")?.value || null,
        longitude: qs("#ap-longitude")?.value || null,

        employee_ids: employeeIds,
      };

      const id = qs("#ap-id")?.value || "";
      const isEdit = !!id;

      const storeEndpoint = APP?.endpoints?.appointmentsStore;
      const updateEndpoint = APP?.endpoints?.appointmentsUpdate;

      if (!storeEndpoint || !updateEndpoint) {
        Swal.fire(
          "Fehler",
          "Endpoint für Termine ist nicht konfiguriert.",
          "error"
        );
        return;
      }

      const url = isEdit ? updateEndpoint(id) : storeEndpoint;
      const method = isEdit ? "PUT" : "POST";

      try {
        const resR = await fetch(url, {
          method,
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": CSRF(),
          },
          body: JSON.stringify(payload),
        });

        const json = await resR.json().catch(() => ({}));
        if (!resR.ok || json?.success === false) {
          throw new Error(
            json?.message || "Termin konnte nicht gespeichert werden."
          );
        }

        Swal.fire(
          "Gespeichert",
          isEdit ? "Termin aktualisiert." : "Termin angelegt.",
          "success"
        );

        const idEl = qs("#ap-id");
        if (idEl) idEl.value = "";
        this.resetForm();
        await this.loadList();
      } catch (err) {
        Swal.fire("Fehler", err.message || "Serverfehler", "error");
      }
    },

    async delete(id) {
      const ok = await Swal.fire({
        title: "Termin löschen?",
        text: "Dieser Vorgang kann nicht rückgängig gemacht werden.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ja, löschen",
      });

      if (!ok.isConfirmed) return;

      const destroyEndpoint = APP?.endpoints?.appointmentsDestroy;
      if (!destroyEndpoint) {
        Swal.fire(
          "Fehler",
          "Endpoint für Termine ist nicht konfiguriert.",
          "error"
        );
        return;
      }

      try {
        const resR = await fetch(destroyEndpoint(id), {
          method: "DELETE",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": CSRF(),
          },
        });

        const json = await resR.json().catch(() => ({}));
        if (!resR.ok || json?.success === false) {
          throw new Error(json?.message || "Löschen fehlgeschlagen.");
        }

        this._list = this._list.filter(
          (a) => String(a.id) !== String(id)
        );
        this.renderList();

        const countEl = qs("#ap-count");
        if (countEl) countEl.textContent = String(this._list.length);

        this.updateCardBadge();

        Swal.fire("Gelöscht", "Termin wurde gelöscht.", "success");
      } catch (err) {
        Swal.fire("Fehler", err.message || "Serverfehler", "error");
      }
    },
  };

  /* ------------------------------------------------
   * Global bindings
   * ----------------------------------------------*/
  qs("#ap-backdrop")?.addEventListener("click", () => UI.hide());
  qsa("[data-ap-close]").forEach((btn) =>
    btn.addEventListener("click", () => UI.hide())
  );
  qs("#ap-form")?.addEventListener("submit", (ev) => UI.submitForm(ev));

  // custom event used from Kanban card menu
  document.addEventListener("open-appointments", (e) => {
    const d = e.detail || {};
    UI.open(d.customerId, d.alternativeId, d.productId, d.title, d);
  });

  // delegated edit/delete on cards
  document.addEventListener("click", (e) => {
    const del = e.target.closest("[data-ap-del]");
    if (del) {
      const id = del.dataset.apDel || del.getAttribute("data-ap-del");
      if (id != null) UI.delete(id);
      return;
    }

    const ed = e.target.closest("[data-ap-edit]");
    if (ed) {
      const id = ed.dataset.apEdit || ed.getAttribute("data-ap-edit");
      if (id != null) UI.fillForm(id);
      return;
    }
  });

  // Select2 in drawer (only employees)
  if (window.jQuery) {
    jQuery(function () {
      const $drawer = jQuery("#ap-drawer");

      jQuery("#ap-employee_ids").select2({
        placeholder: "Mitarbeiter wählen…",
        dropdownParent: $drawer,
      });

      // Kundensuche ist in deinem Markup bereits versteckt; Finger weg
    });
  }

  // export for debugging / external hooks
  window.AppointmentsUI = UI;
})();
</script>

<!-- Termin Script  -->
<script>
(function () {
  "use strict";

  // ARCHIVE pagination (tab: Archiv)
  $(document).on('click', '#archiveInner .pagination a', function (e) {
    e.preventDefault();

    const url = $(this).attr('href'); // URL already has archive_page=2&...

    $.get(url, function (html) {
      // archivePartial returns <div id="archiveInner">...</div>
      $('#archiveInner').replaceWith(html);
    });
  });

  // JUNK pagination (tab: Junk)
  $(document).on('click', '#junkInner .pagination a', function (e) {
    e.preventDefault();

    const url = $(this).attr('href'); // URL already has junk_page=2&...

    $.get(url, function (html) {
      $('#junkInner').replaceWith(html);
    });
  });

  // TICKET pagination (tab: Ticket)
  $(document).on('click', '#ticketInner .pagination a', function (e) {
    e.preventDefault();

    const url = $(this).attr('href'); // URL already has ticket_page=2&...

    $.get(url, function (html) {
      $('#ticketInner').replaceWith(html);
    });
  });

})();
</script>


@endsection

