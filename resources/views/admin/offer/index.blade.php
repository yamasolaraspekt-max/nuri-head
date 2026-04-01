@extends('admin.layouts.app')

@section('title', 'Angebote')

@once
@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  :root {
    --app-bg:#f3f4f6;
    --card-bg:#ffffff;
    --text-main:#1f2937;
    --text-muted:#6b7280;
    --border:#e5e7eb;

    --primary:#93c21c;
    --primary-hover:#7baa18;
    --primary-light:#f4fae7;

    --blue:#74b2d4;
    --blue-light:#eff6ff;

    --success:#10b981;
    --success-light:#ecfdf5;

    --warning:#f59e0b;
    --warning-light:#fffbeb;

    --danger:#ef4444;
    --danger-hover:#dc2626;
    --danger-light:#fef2f2;

    --gray:#6b7280;
    --gray-light:#f3f4f6;

    --shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
    --shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
    --radius:14px;
    --transition:all .2s ease-in-out;
  }

  .oc-wrap{
    font-family:Inter,system-ui,-apple-system,sans-serif;
    color:var(--text-main);
    max-width:1500px;
    margin:20px auto;
    padding:20px;
  }

  .oc-header{margin-bottom:18px;margin-top:103px;}
  .oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }

  #crud-modal { z-index: 1100; }
  #folder-modal { z-index: 1200; }
  #alert-modal { z-index: 1400; }
  .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}

  .select2-container--open { z-index: 1600 !important; }
  .select2-dropdown { z-index: 1601 !important; }
  .select2-search--dropdown .select2-search__field { width: 100% !important; box-sizing: border-box; }
  
  .oc-btn{
    background:var(--primary);
    color:#fff;
    border:none;
    padding:10px 16px;
    border-radius:10px;
    font-weight:900;
    cursor:pointer;
    transition:var(--transition);
    display:inline-flex;
    align-items:center;
    gap:8px;
  }
  .oc-btn:hover{background:var(--primary-hover)}
  .oc-btn.danger{background:var(--danger)}
  .oc-btn.danger:hover{background:var(--danger-hover)}

  .oc-btn-soft{
    background:#fff;
    color:var(--text-main);
    border:1px solid var(--border);
    padding:10px 14px;
    border-radius:10px;
    font-weight:800;
    cursor:pointer;
    transition:var(--transition);
  }
  .oc-btn-soft:hover{background:#f9fafb}

  .oc-btn-ic{
    width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:#fff;
    display:inline-flex;align-items:center;justify-content:center;color:var(--text-muted);cursor:pointer;transition:var(--transition)
  }
  .oc-btn-ic:hover{background:#f9fafb;color:var(--text-main);border-color:#d1d5db}
  .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .oc-btn-ic.primary:hover{border-color:var(--primary)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}
  .oc-btn-ic.active{background:var(--primary-light); color:var(--primary); border-color:var(--primary);}

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(6, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }

  @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(3, minmax(0,1fr));} }
  @media(max-width:700px){ .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }

  .oc-stat{
    background:var(--card-bg); border:1px solid var(--border); border-radius:16px; padding:16px;
    box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:12px; min-height:92px;
  }
  .oc-stat-icon{ width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex:0 0 auto; }
  .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
  .oc-stat-icon.draft{background:var(--gray-light);color:var(--gray)}
  .oc-stat-icon.sent{background:#eff6ff;color:#74b2d4}
  .oc-stat-icon.negotiation{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.final{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.cancel{background:var(--danger-light);color:var(--danger)}

  .oc-stat-meta{min-width:0}
  .oc-stat-label{ font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; }
  .oc-stat-value{ font-size:24px; font-weight:900; color:#111827; line-height:1.1; margin-top:4px; }
  .oc-stat-sub{ font-size:12px; color:var(--text-muted); margin-top:4px; }

  .oc-toolbar{
    background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:14px 16px;
    display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end; justify-content:space-between;
    margin-bottom:16px; box-shadow:var(--shadow-sm)
  }

  .oc-toolbar-left,.oc-toolbar-right{ display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
  .oc-toolbar-left{flex:1;}

  .oc-filter-block{ display:flex; flex-direction:column; gap:6px; min-width:170px; }
  .oc-filter-block.search{flex:1;min-width:260px;}
  .oc-filter-label{ font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; }

  .oc-input{
    background:#f9fafb;border:1px solid var(--border);border-radius:8px;padding:10px 12px 10px 36px;font-size:14px;outline:none;transition:var(--transition);min-width:240px; width:100%;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:10px center;background-size:16px
  }
  .oc-input:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}

  .oc-select{
    padding:10px 34px 10px 12px;border-radius:8px;border:1px solid var(--border);
    background-color:#fff;font-size:13px;cursor:pointer;outline:none;appearance:none;min-height:42px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 10px center;background-size:14px
  }

  /* -------------------
     LIST (COLLAPSE) VIEW
  ------------------- */
  .oc-list-head{
    display:grid; grid-template-columns:76px minmax(200px,1.3fr) minmax(180px,1fr) minmax(160px,.9fr) 150px 120px 120px;
    gap:14px; align-items:center; padding:0 16px 10px 16px; color:var(--text-muted); font-size:11px;
    font-weight:900; text-transform:uppercase; letter-spacing:.06em;
  }
  @media(max-width:1180px){ .oc-list-head{display:none;} }
  .oc-head-btn{ display:inline-flex; align-items:center; gap:6px; cursor:pointer; user-select:none; background:none; border:none; padding:0; color:inherit; font:inherit; font-weight:900; }
  .oc-sort-mark{ font-size:12px; color:#9ca3af; }
  .oc-sort-mark.active{color:var(--primary)}

  .oc-list{display:flex;flex-direction:column;gap:12px}
  .oc-item{ background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); transition:var(--transition); position:relative; overflow:hidden; }
  .oc-item:hover{border-color:var(--primary);box-shadow:var(--shadow);}
  .oc-item-header{ padding:16px; display:grid; gap:16px; align-items:center; grid-template-columns:76px minmax(200px,1.3fr) minmax(180px,1fr) minmax(160px,.9fr) 150px 120px 120px; cursor:pointer; }
  
  @media(max-width:1180px){
    .oc-item-header{ grid-template-columns:56px 1fr; grid-template-rows:auto auto auto auto; gap:12px; }
    .oc-responsive-col{grid-column:2;}
    .oc-actions-wrap{grid-column:2;justify-self:start;}
    .oc-actions{grid-column:2;justify-self:start;}
  }

  .oc-cell{min-width:0}
  .oc-cell-title{ font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:4px; display:none; }
  @media(max-width:1180px){ .oc-cell-title{display:block;} }

  .oc-ic{ width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:var(--primary-light);color:var(--primary);transition:transform .2s; }
  .oc-ic.is-open{transform:rotate(90deg);}
  .oc-ic svg{width:24px;height:24px}

  .oc-main{display:flex;flex-direction:column;min-width:0}
  .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
  .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .oc-tag{ display:inline-flex; align-items:center; padding:4px 8px; border-radius:8px; font-size:11px; font-weight:900; text-transform:uppercase; background:var(--primary-light); color:#6d8c12; margin-right:6px; }

  .oc-status-pill{ display:inline-flex; align-items:center; justify-content:center; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:900; white-space:nowrap; }
  .oc-status-pill.gray{background:#f3f4f6;color:#4b5563;}
  .oc-status-pill.blue{background:#eff6ff;color:#1d4ed8;}
  .oc-status-pill.orange{background:#fffbeb;color:#b45309;}
  .oc-status-pill.green{background:#ecfdf5;color:#047857;}
  .oc-status-pill.red{background:#fef2f2;color:#b91c1c;}

  .oc-actions-wrap{ display:flex; align-items:center; justify-content:flex-end; gap:12px; }
  .oc-actions{display:flex;gap:8px;}

  .oc-item-body{ padding:0 16px 16px 16px; border-top:1px solid var(--border); background:#fafafa; display:none; }
  .oc-item-body.is-open{display:block;}

  .oc-item-body-top{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:16px; margin-bottom:8px; flex-wrap:wrap; }
  .oc-item-body-title{ margin:0; font-size:14px; font-weight:900; color:#111827; }
  .oc-meta-inline{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
  .oc-mini-badge{ display:inline-flex; align-items:center; padding:5px 10px; border-radius:999px; background:#fff; border:1px solid var(--border); font-size:12px; font-weight:800; color:var(--text-muted); }

  /* -------------------
     SPLIT VIEW (NEW)
  ------------------- */
  .oc-split-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 20px;
    align-items: start;
  }
  @media(max-width:992px){
    .oc-split-layout { grid-template-columns: 1fr; }
  }

  .oc-sidebar {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    height: 75vh;
    min-height: 600px;
    overflow-y: auto;
    box-shadow: var(--shadow-sm);
  }
  .oc-sidebar::-webkit-scrollbar { width: 6px; }
  .oc-sidebar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

  .oc-sidebar-item {
    padding: 16px;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: var(--transition);
  }
  .oc-sidebar-item:last-child { border-bottom: none; }
  .oc-sidebar-item:hover { background: #f9fafb; }
  .oc-sidebar-item.active {
    background: var(--blue-light);
    border-left: 4px solid var(--blue);
  }

  .oc-detail-pane {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    min-height: 75vh;
    box-shadow: var(--shadow-sm);
  }

  .oc-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
  }

  /* Stacked Avatars for Sidebar */
  .oc-avatar-group { display: flex; align-items: center; }
  .oc-avatar-tiny { 
    width: 24px; 
    height: 24px; 
    border-radius: 999px; 
    border: 2px solid #fff; 
    margin-left: -8px; 
    object-fit: cover; 
    background: #f3f4f6; 
    box-shadow: var(--shadow-sm);
  }
  .oc-avatar-tiny:first-child { margin-left: 0; }
  .oc-avatar-more {
    width: 24px; height: 24px; border-radius: 999px; border: 2px solid #fff; margin-left: -8px;
    background: #e5e7eb; color: #4b5563; font-size: 10px; font-weight: 800;
    display: flex; align-items: center; justify-content: center; z-index: 1;
  }

  /* -------------------
     FOLDERS (SHARED)
  ------------------- */
  .folder-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px; margin-top:8px; }
  .folder-card{ background:#fff; border:1px solid var(--border); border-radius:12px; padding:14px; display:flex; align-items:flex-start; gap:12px; cursor:pointer; transition:var(--transition); box-shadow:var(--shadow-sm); }
  .folder-card:hover{ border-color:var(--primary); transform:translateY(-2px); box-shadow:0 6px 12px rgba(0,0,0,0.08); }
  .folder-icon{ width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex:0 0 auto; }
  .folder-meta{min-width:0;flex:1;}
  .folder-title{ font-weight:800; font-size:13px; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .folder-sub{ font-size:11px; color:var(--text-muted); margin-top:3px; }
  .folder-footer{ margin-top:10px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
  .folder-user{ display:flex; align-items:center; gap:8px; min-width:0; }
  .folder-avatar{ width:30px; height:30px; border-radius:999px; object-fit:cover; border:1px solid var(--border); background:#f3f4f6; flex:0 0 auto; }
  .folder-user-name{ font-size:12px; font-weight:700; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; }
  .folder-date{ font-size:11px; color:var(--text-muted); font-weight:700; }

  .oc-empty{ text-align:center; padding:60px; color:var(--text-muted); background:#fff; border:1px dashed var(--border); border-radius:16px; }

  /* Modals & Toasts */
  .oc-modal-backdrop{ position:fixed; inset:0; z-index:1100; background:rgba(17,24,39,.55); backdrop-filter:blur(3px); opacity:0; pointer-events:none; transition:opacity .22s ease; display:flex; align-items:center; justify-content:center; padding:18px; }
  .oc-modal-backdrop.open{opacity:1;pointer-events:auto}
  .oc-modal{ width:100%; max-width:560px; background:#fff; border:1px solid rgba(229,231,235,.9); border-radius:16px; box-shadow:var(--shadow); transform:translateY(12px) scale(.985); transition:transform .22s ease; overflow:visible; }
  .oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1)}
  .oc-modal-h{ display:flex; gap:12px; align-items:center; justify-content:space-between; padding:16px 18px; border-bottom:1px solid var(--border); background:#fafafa; border-radius:16px 16px 0 0; }
  .oc-modal-ttl{font-weight:900;font-size:16px;line-height:1.2;margin:0;color:#111827}
  .oc-modal-b{padding:20px 18px;max-height:70vh;overflow-y:auto;}
  .oc-modal-f{ padding:14px 18px; border-top:1px solid var(--border); background:#fafafa; display:flex; gap:10px; justify-content:flex-end; border-radius:0 0 16px 16px; }
  .oc-form-group{margin-bottom:16px;}
  .oc-label{display:block;font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:6px;}
  .oc-input-form{ width:100%; padding:10px 12px; border-radius:8px; border:1px solid var(--border); background:#fff; font-size:14px; outline:none; transition:var(--transition); }
  .oc-input-form:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}

  .oc-toast-wrap{ position:fixed; right:20px; bottom:20px; z-index:9999; display:flex; flex-direction:column; gap:10px; pointer-events:none; }
  .oc-toast{ pointer-events:auto; min-width:280px; max-width:360px; background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); padding:12px; display:flex; gap:10px; align-items:flex-start; animation:ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards; }
  @keyframes ocToastIn{ from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
  .oc-toast-ic{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 auto}
  .oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
  .oc-toast-ic.warn{background:var(--warning-light);color:var(--warning)}
  .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
  .oc-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
  .oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}
  .oc-toast-x{margin-left:auto;background:transparent;border:none;cursor:pointer;color:var(--text-muted);}

  #crud-modal .oc-modal { z-index: 1101; position: relative; }
  #folder-modal .oc-modal { z-index: 1201; position: relative; }
  #alert-modal .oc-modal { z-index: 1401; position: relative; }

  .oc-btn-ic.clone {
    color: var(--blue);
    border-color: #dbeafe;
    background: #eff6ff;
  }
  .oc-btn-ic.clone:hover {
    border-color: var(--blue);
    background: #dbeafe;
  }

  .folder-card.is-locked {
    border-color: #fecaca;
    background: linear-gradient(180deg, #ffffff 0%, #fef2f2 100%);
  }

  .folder-card.is-locked:hover {
    border-color: #ef4444;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(239, 68, 68, 0.10);
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap" id="offer-app">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">Angebotsverwaltung</div>
        <div class="oc-sub">Analytics, Filter, Sortierung und automatisches Ordner-Management.</div>
      </div>
      <button class="oc-btn" type="button" onclick="openModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Neues Angebot
      </button>
    </div>
  </div>

  <div class="oc-analytics" id="analytics-cards">
    <div class="oc-stat"><div class="oc-stat-icon total"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/></svg></div><div class="oc-stat-meta"><div class="oc-stat-label">Gesamt</div><div class="oc-stat-value" id="stat-total">0</div><div class="oc-stat-sub">Alle Angebote</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon draft"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg></div><div class="oc-stat-meta"><div class="oc-stat-label">Entwurf</div><div class="oc-stat-value" id="stat-draft">0</div><div class="oc-stat-sub">In Bearbeitung</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon sent"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg></div><div class="oc-stat-meta"><div class="oc-stat-label">Gesendet</div><div class="oc-stat-value" id="stat-sent">0</div><div class="oc-stat-sub">An Kunden verschickt</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon negotiation"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 12h8"/><path d="M12 8v8"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg></div><div class="oc-stat-meta"><div class="oc-stat-label">Verhandlung</div><div class="oc-stat-value" id="stat-negotiation">0</div><div class="oc-stat-sub">Offene Kommunikation</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon final"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div><div class="oc-stat-meta"><div class="oc-stat-label">Abgeschlossen</div><div class="oc-stat-value" id="stat-final">0</div><div class="oc-stat-sub">Erfolgreich finalisiert</div></div></div>
    <div class="oc-stat"><div class="oc-stat-icon cancel"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></div><div class="oc-stat-meta"><div class="oc-stat-label">Storniert</div><div class="oc-stat-value" id="stat-cancel">0</div><div class="oc-stat-sub">Abgebrochen oder verloren</div></div></div>
  </div>

  <div class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input class="oc-input" id="search-input" placeholder="Suchen nach Kunde, Produkt, Straße..." autocomplete="off" oninput="handleSearch(this.value)"/>
      </div>
      <div class="oc-filter-block">
        <label class="oc-filter-label">Status</label>
        <select class="oc-select" id="status-filter" onchange="handleFilter('status', this.value)">
          <option value="">Alle Status</option>
          <option value="draft">Entwurf</option>
          <option value="sent">Gesendet</option>
          <option value="negotiation">Verhandlung</option>
          <option value="final">Abgeschlossen</option>
          <option value="cancel">Storniert</option>
        </select>
      </div>
      <div class="oc-filter-block">
        <label class="oc-filter-label">Produkt</label>
        <select class="oc-select" id="product-filter" onchange="handleFilter('product', this.value)"><option value="">Alle Produkte</option></select>
      </div>
      <div class="oc-filter-block">
        <label class="oc-filter-label">Erstellt von</label>
        <select class="oc-select" id="creator-filter" onchange="handleFilter('creator', this.value)"><option value="">Alle Benutzer</option></select>
      </div>
      <div class="oc-filter-block">
        <label class="oc-filter-label">Ordner</label>
        <select class="oc-select" id="folder-filter" onchange="handleFilter('hasFolders', this.value)">
          <option value="">Alle</option>
          <option value="yes">Mit Ordner</option>
          <option value="no">Ohne Ordner</option>
        </select>
      </div>
    </div>

    <div class="oc-toolbar-right">
      <div class="oc-filter-block">
        <label class="oc-filter-label">Ansicht</label>
        <div style="display:flex; background:#f9fafb; border:1px solid var(--border); border-radius:8px; padding:2px;">
            <button class="oc-btn-ic" id="view-split-btn" type="button" onclick="setViewMode('split')" style="border:none; border-radius:6px; box-shadow:none; width:36px; height:32px;" title="Split View">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
            </button>
            <button class="oc-btn-ic" id="view-list-btn" type="button" onclick="setViewMode('list')" style="border:none; border-radius:6px; box-shadow:none; width:36px; height:32px;" title="Listenansicht (Collapse)">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            </button>
        </div>
      </div>
      <div class="oc-filter-block">
        <label class="oc-filter-label">Sortieren</label>
        <select class="oc-select" id="sort-by" onchange="handleSortChange()">
          <option value="id">ID</option>
          <option value="customer">Kunde</option>
          <option value="created_at">Datum</option>
        </select>
      </div>
      <button class="oc-btn-soft" type="button" onclick="resetFilters()">Reset</button>
    </div>
  </div>

  <div id="list-head-container">
      <div class="oc-list-head">
        <div></div>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('customer')">Kunde / Objekt <span class="oc-sort-mark" id="sort-mark-customer">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('product')">Produkt / Service <span class="oc-sort-mark" id="sort-mark-product">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('creator')">Erstellt von <span class="oc-sort-mark" id="sort-mark-creator">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('status')">Status <span class="oc-sort-mark" id="sort-mark-status">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('folders')">Ordner <span class="oc-sort-mark" id="sort-mark-folders">↕</span></button>
        <button type="button" class="oc-head-btn" onclick="toggleColumnSort('created_at')">Datum <span class="oc-sort-mark" id="sort-mark-created_at">↕</span></button>
      </div>
  </div>

  <div id="offer-list" class="oc-list" aria-live="polite">
    <div class="oc-empty">Lade Daten...</div>
  </div>
</div>

{{-- CRUD Modal --}}
<div class="oc-modal-backdrop" id="crud-modal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 id="modal-title" class="oc-modal-ttl">Neues Angebot erstellen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal()" style="width:32px;height:32px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="oc-modal-b">
      <form id="crud-form" onsubmit="submitForm(event)">
        <input type="hidden" id="inp-id">
        <div class="oc-form-group">
          <label class="oc-label">Kunde & Objekt suchen *</label>
          <select id="inp-customer-object" style="width:100%;"></select>
          <input type="hidden" id="inp-customer" required>
          <input type="hidden" id="inp-alternative" required>
        </div>
        <div class="oc-form-group">
          <label class="oc-label">Produkt auswählen *</label>
          <select id="inp-product" class="oc-select" style="width:100%" required disabled><option value="">Bitte zuerst Kunde & Objekt wählen</option></select>
        </div>
        <div class="oc-form-group">
          <label class="oc-label">Service / Projekt (Optional)</label>
          <input type="text" id="inp-service" class="oc-input-form" placeholder="z.B. Installation">
        </div>
        <div id="folder-creation-section">
          <hr style="border:0; border-top:1px solid var(--border); margin: 24px 0;">
          <div style="font-weight:800; font-size:14px; margin-bottom:12px; color:var(--text-main);">Standard-Ordner Einstellungen</div>
          <div class="oc-form-group">
            <label class="oc-label">Ordner Name *</label>
            <input type="text" id="inp-folder-name" class="oc-input-form" placeholder="z.B. V1 Entwurf">
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Ordner Farbe</label>
            <div style="display:flex; align-items:center; gap:12px;">
              <input type="color" id="inp-folder-color" value="#93c21c" style="height:42px; width:60px; border:1px solid var(--border); border-radius:8px; cursor:pointer; padding:2px;">
              <span style="font-size:12px; color:var(--text-muted);">Farbe zur besseren Erkennung</span>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="oc-modal-f">
      <button type="button" class="oc-btn-soft" onclick="closeModal()">Abbrechen</button>
      <button type="submit" form="crud-form" class="oc-btn" id="btn-submit">Speichern</button>
    </div>
  </div>
</div>

{{-- Alert Modal --}}
<div class="oc-modal-backdrop" id="alert-modal">
  <div class="oc-modal" style="max-width:460px;">
    <div class="oc-modal-h">
      <h3 id="alert-modal-title" class="oc-modal-ttl">Hinweis</h3>
      <button class="oc-btn-ic" type="button" onclick="closeAlertModal()" style="width:32px;height:32px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="oc-modal-b"><div id="alert-modal-message" style="font-size:14px; color:var(--text-main); line-height:1.6;"></div></div>
    <div class="oc-modal-f" id="alert-modal-actions"><button type="button" class="oc-btn" onclick="closeAlertModal()">Schließen</button></div>
  </div>
</div>

{{-- Folder Modal --}}
<div class="oc-modal-backdrop" id="folder-modal">
  <div class="oc-modal" style="max-width:520px;">
    <div class="oc-modal-h">
      <h3 id="folder-modal-title" class="oc-modal-ttl">Ordner erstellen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeFolderModal()" style="width:32px;height:32px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="oc-modal-b">
      <form id="folder-form" onsubmit="submitFolderForm(event)">
        <input type="hidden" id="folder-offer-id">
        <input type="hidden" id="folder-id">
        <div class="oc-form-group">
          <label class="oc-label">Ordner Name *</label>
          <input type="text" id="folder-name" class="oc-input-form" placeholder="z.B. V2 Kalkulation" required>
        </div>
        <div class="oc-form-group">
          <label class="oc-label">Ordner Farbe</label>
          <input type="color" id="folder-color" value="#93c21c" style="height:42px; width:70px; border:1px solid var(--border); border-radius:8px; cursor:pointer; padding:2px;">
        </div>
        <div class="oc-form-group">
          <label class="oc-label">Status</label>
          <select id="folder-status" class="oc-select" style="width:100%;">
            <option value="draft">Entwurf</option>
            <option value="sent">Gesendet</option>
            <option value="negotiation">Verhandlung</option>
            <option value="final">Abgeschlossen</option>
            <option value="cancel">Storniert</option>
          </select>
        </div>
      </form>
    </div>
    <div class="oc-modal-f">
      <button type="button" class="oc-btn-soft" onclick="closeFolderModal()">Abbrechen</button>
      <button type="submit" form="folder-form" class="oc-btn" id="folder-submit-btn">Speichern</button>
    </div>
  </div>
</div>

{{-- Duplicate Offer Modal --}}
<div class="oc-modal-backdrop" id="duplicate-offer-modal">
  <div class="oc-modal" style="max-width:620px;">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Angebot bereits vorhanden</h3>
      <button class="oc-btn-ic" type="button" onclick="closeDuplicateOfferModal()" style="width:32px;height:32px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="oc-modal-b">
      <div style="font-size:14px; color:var(--text-main); line-height:1.7; margin-bottom:14px;">Für diese Kombination aus <strong>Kunde</strong>, <strong>Objekt</strong> und <strong>Produkt</strong> wurde bereits ein Angebot erstellt.</div>
      <div id="duplicate-offer-content"></div>
    </div>
    <div class="oc-modal-f">
      <button type="button" class="oc-btn-soft" onclick="closeDuplicateOfferModal()">Schließen</button>
      <button type="button" class="oc-btn" id="duplicate-offer-open-btn">Datensatz öffnen</button>
    </div>
  </div>
</div>

<div class="oc-modal-backdrop" id="clone-folder-modal">
  <div class="oc-modal" style="max-width:520px;">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Ordner klonen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeCloneFolderModal()" style="width:32px;height:32px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="oc-modal-b">
      <form id="clone-folder-form" onsubmit="submitCloneFolderForm(event)">
        <input type="hidden" id="clone-folder-id">

        <div class="oc-form-group">
          <label class="oc-label">Neuer Ordnername *</label>
          <input type="text" id="clone-folder-name" class="oc-input-form" required>
        </div>

        <div class="oc-form-group">
          <label class="oc-label">Farbe</label>
          <input type="color" id="clone-folder-color" value="#93c21c"
                 style="height:42px; width:70px; border:1px solid var(--border); border-radius:8px; cursor:pointer; padding:2px;">
        </div>

        <div class="oc-form-group">
          <label class="oc-label">Status</label>
          <select id="clone-folder-status" class="oc-select" style="width:100%;">
            <option value="draft">Entwurf</option>
            <option value="sent">Gesendet</option>
            <option value="negotiation">Verhandlung</option>
            <option value="final">Abgeschlossen</option>
            <option value="cancel">Storniert</option>
          </select>
        </div>

        <div class="oc-form-group" style="margin-bottom:0;">
          <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer;">
            <input type="checkbox" id="clone-folder-everything" checked style="margin-top:3px;">
            <span style="font-size:13px; color:var(--text-main);">
              Alle Inhalte mitklonen
              <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                Positionen, Details, Anhänge und weitere Ordnerdaten übernehmen.
              </div>
            </span>
          </label>
        </div>
      </form>
    </div>

    <div class="oc-modal-f">
      <button type="button" class="oc-btn-soft" onclick="closeCloneFolderModal()">Abbrechen</button>
      <button type="submit" form="clone-folder-form" class="oc-btn" id="clone-folder-submit-btn">Klonen</button>
    </div>
  </div>
</div>
<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  const ENDPOINTS = {
    data: @json(route('admin.offers.data')),
    store: @json(route('admin.offers.store')),
    updateBase: @json(url('/admin/offers')),
    searchCustomerObjects: @json(route('admin.offers.search-customer-objects')),
    getProducts: @json(route('admin.offers.get-products')),
    folderStoreBase: @json(url('/admin/offers')),
    folderUpdateBase: @json(url('/admin/offers/folders')),
    cloneFolderBase: @json(url('/admin/offers/folders')),
  };

  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const EMPLOYEE_IMAGE_BASE = @json(asset('images/employee'));
  const DEFAULT_AVATAR = @json(asset('images/default-avatar.png'));

  const STATUS_MAP = {
    draft:       { label: 'Entwurf',       color: 'gray'   },
    sent:        { label: 'Gesendet',      color: 'blue'   },
    negotiation: { label: 'Verhandlung',   color: 'orange' },
    final:       { label: 'Abgeschlossen', color: 'green'  },
    cancel:      { label: 'Storniert',     color: 'red'    }
  };

  let state = {
    viewMode: 'split', 
    selectedOfferId: null, 
    offers: [],
    expanded: {},
    search: '',
    filters: { status: '', product: '', creator: '', hasFolders: '' },
    sort: { by: 'id', dir: 'desc' }
  };

  let alertModalResolver = null;

  const esc = (s) => String(s ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');

  function iconSvg(name) {
    if (name === 'chevron') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>`;
    if (name === 'folder') return `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2z"/></svg>`;
    if (name === 'edit') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`;
    if (name === 'trash') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>`;
    if (name === 'clone') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="9" y="9" width="11" height="11" rx="2"/>
        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
      </svg>`;
    if (name === 'lock') return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="5" y="11" width="14" height="10" rx="2"/>
        <path d="M8 11V8a4 4 0 1 1 8 0v3"/>
      </svg>`;
    return '';
  }

  function formatDate(d) { try { const dt = new Date(d); return Number.isNaN(dt.getTime()) ? '-' : dt.toLocaleDateString('de-DE'); } catch { return '-'; } }
  function formatDateTime(d) { return formatDate(d); }
  function getCustomerName(o) { return [o.customer?.firma, o.customer?.name, o.customer?.lastname].filter(Boolean).join(' ').trim() || 'Unbekannt'; }
  function getObjectName(o) { return [o.alternative?.street, o.alternative?.city].filter(Boolean).join(', ') || 'Kein Objekt'; }
  function getProductName(o) { return o.product?.article_group || 'Unbekannt'; }
  function getCreatorName(o) { return [o.creator?.name, o.creator?.lastname].filter(Boolean).join(' ').trim() || o.creator?.name || 'Unbekannt'; }
  function getFolderCount(o) { return Array.isArray(o.folders) ? o.folders.length : 0; }
  function getStatusPill(sk) { const st = STATUS_MAP[sk] || STATUS_MAP.draft; return `<span class="oc-status-pill ${st.color}">${esc(st.label)}</span>`; }
  function getFolderCreatorName(f) { return [f.creator?.name, f.creator?.lastname].filter(Boolean).join(' ').trim() || f.creator?.name || 'Unbekannt'; }
  function getFolderCreatorImage(f) { return f.creator?.image ? `${EMPLOYEE_IMAGE_BASE}/${f.creator.image}` : DEFAULT_AVATAR; }

 function isFolderLocked(folder) {
  const status = String(folder?.status || '').toLowerCase().trim();
  return status === 'cancel' || status === 'cancelled' || status === 'storniert';
}

function isFinalFolderStatus(status) {
  const s = String(status || '').toLowerCase().trim();
  return s === 'final' || s === 'abgeschlossen';
}
  function getFolderStatusLabel(status) {
    const key = String(status || '').toLowerCase().trim();
    if (STATUS_MAP[key]) return STATUS_MAP[key].label;
    if (key === 'abgeschlossen') return 'Abgeschlossen';
    return 'Entwurf';
  }

  
  function offerHasFinalFolder(offerId) {
    const offer = state.offers.find(x => Number(x.id) === Number(offerId));
    if (!offer || !Array.isArray(offer.folders)) return false;
    return offer.folders.some(folder => isFinalFolderStatus(folder.status));
  }

  function showOfferFinalizedMessage() {
    return openAlertModal({
      title: 'Angebot finalisiert',
      message: 'Das Angebot ist finalisiert und kann nicht geändert werden.',
      type: 'alert',
      confirmText: 'OK'
    });
  }

 function tryOpenFolder(folder) {
    openFolderPreview(folder.id);
    return true;
  }
  // Get the most recent date between the offer itself and all its folders
  function getLatestUpdateDate(o) {
    let latest = new Date(o.updated_at || o.created_at || 0);
    if (Array.isArray(o.folders)) {
      o.folders.forEach(f => {
        let d = new Date(f.updated_at || f.created_at || 0);
        if (d > latest) latest = d;
      });
    }
    if (Number.isNaN(latest.getTime()) || latest.getTime() === 0) return '-';
    return latest.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit' }) + ' Uhr';
  }

  // Extract unique creators from folders and generate overlapping tiny pictures
  function getFolderCreatorsAvatars(o) {
    if (!Array.isArray(o.folders) || !o.folders.length) return '';
    const creators = new Map();
    if (o.creator) creators.set(o.creator.id, o.creator);
    o.folders.forEach(f => { if (f.creator) creators.set(f.creator.id, f.creator); });
    if (creators.size === 0) return '';
    let html = '<div class="oc-avatar-group" title="Beteiligte Mitarbeiter">';
    let count = 0;
    for (let [id, c] of creators) {
      if (count >= 4) { html += `<div class="oc-avatar-more">+${creators.size - 4}</div>`; break; }
      const img = c.image ? `${EMPLOYEE_IMAGE_BASE}/${c.image}` : DEFAULT_AVATAR;
      const name = esc([c.name, c.lastname].filter(Boolean).join(' '));
      html += `<img src="${img}" class="oc-avatar-tiny" title="${name}" onerror="this.src='${DEFAULT_AVATAR}'">`;
      count++;
    }
    html += '</div>';
    return html;
  }

  function setViewMode(mode) {
      state.viewMode = mode;
      const splitBtn = document.getElementById('view-split-btn');
      const listBtn = document.getElementById('view-list-btn');
      if(mode === 'split'){
          splitBtn.classList.add('active'); listBtn.classList.remove('active');
          document.getElementById('list-head-container').style.display = 'none';
      } else {
          listBtn.classList.add('active'); splitBtn.classList.remove('active');
          document.getElementById('list-head-container').style.display = 'block';
      }
      renderList();
  }

  function selectOfferInSplitView(id) {
      state.selectedOfferId = id;
      renderList();
  }

  async function loadData() {
    try {
        const res = await fetch(ENDPOINTS.data, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();
        if (json.success) {
            state.offers = Array.isArray(json.data) ? json.data : [];
            populateFilters();
            renderAnalytics();
            setViewMode(state.viewMode);
        } else {
            document.getElementById('offer-list').innerHTML = `<div class="oc-empty">Fehler beim Laden der Daten.</div>`;
        }
    } catch (e) {
        document.getElementById('offer-list').innerHTML = `<div class="oc-empty">Daten konnten nicht geladen werden.</div>`;
    }
  }

  function renderList() {
    const listEl = document.getElementById('offer-list');
    const filtered = getProcessedOffers();

    if (!filtered.length) {
      listEl.innerHTML = `<div class="oc-empty">Keine Angebote gefunden.</div>`;
      return;
    }

    if (state.viewMode === 'split') {
        listEl.innerHTML = buildSplitViewHTML(filtered);
    } else {
        listEl.innerHTML = `<div class="oc-list">${filtered.map(o => buildListItemHTML(o)).join('')}</div>`;
    }
  }

  function buildSplitViewHTML(filtered) {
      if (!state.selectedOfferId || !filtered.find(o => o.id === state.selectedOfferId)) {
          state.selectedOfferId = filtered[0].id;
      }
      const selectedOffer = filtered.find(o => o.id === state.selectedOfferId);

      // Sidebar
      let sidebarHTML = `<div class="oc-sidebar">`;
      sidebarHTML += filtered.map(o => {
          const isActive = o.id === state.selectedOfferId;
          const folderCount = getFolderCount(o);
          const latestUpdate = getLatestUpdateDate(o);
          const avatarsHtml = getFolderCreatorsAvatars(o);

          return `
            <div class="oc-sidebar-item ${isActive ? 'active' : ''}" onclick="selectOfferInSplitView(${o.id})">
                <div class="oc-ttl" style="font-size:14px; display:flex; justify-content:space-between; align-items:flex-start;">
                    <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; padding-right:8px;">${esc(getCustomerName(o))}</span>
                    <span class="oc-tag" style="margin:0; font-size:10px; flex-shrink:0;">#${o.id}</span>
                </div>
                <div class="oc-subt" style="margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${esc(getProductName(o))}</div>
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    ${getStatusPill(o.status)}
                    <div style="font-size:11px; color:var(--text-muted); font-weight:700; display:flex; align-items:center; gap:4px;" title="Letzte Änderung">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        ${esc(latestUpdate)}
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    ${avatarsHtml}
                    <span style="font-size:11px; color:var(--text-muted); font-weight:800;">${folderCount} Ordner</span>
                </div>
            </div>`;
      }).join('');
      sidebarHTML += `</div>`;

      // Right Detail Pane
      let detailHTML = `<div class="oc-detail-pane">`;
      if (selectedOffer) {
          const folders = Array.isArray(selectedOffer.folders) ? selectedOffer.folders : [];
          const hasFinalFolder = Array.isArray(selectedOffer?.folders) && selectedOffer.folders.some(f => isFinalFolderStatus(f.status));
          detailHTML += `
            <div class="oc-detail-header">
                <div>
                    <h2 style="margin:0 0 6px 0; font-size:22px; font-weight:900;">${esc(getCustomerName(selectedOffer))}</h2>
                    <div style="font-size:14px; color:var(--text-muted); margin-bottom:4px;"><strong>Objekt:</strong> ${esc(getObjectName(selectedOffer))}</div>
                    <div style="font-size:14px; color:var(--text-muted);"><strong>Produkt:</strong> ${esc(getProductName(selectedOffer))} | <strong>Erstellt von:</strong> ${esc(getCreatorName(selectedOffer))}</div>
                </div>
                <div class="oc-actions-wrap">
                    <select class="oc-select" onchange="quickStatus(${selectedOffer.id}, this.value)" style="padding:6px 28px 6px 10px; min-height:36px;">
                        ${Object.keys(STATUS_MAP).map(k => `<option value="${k}" ${selectedOffer.status === k ? 'selected' : ''}>${STATUS_MAP[k].label}</option>`).join('')}
                    </select>
                    <button class="oc-btn-ic" type="button" onclick="openModal(${selectedOffer.id})" title="Bearbeiten">${iconSvg('edit')}</button>
                    <button class="oc-btn-ic danger" type="button" onclick="deleteOffer(${selectedOffer.id})" title="Löschen">${iconSvg('trash')}</button>
                </div>
            </div>
            <div class="oc-item-body-top" style="margin-top:0;">
                <h4 class="oc-item-body-title" style="font-size:16px;">Ordnerübersicht (${folders.length})</h4>
                <button
                  type="button"
                  class="oc-btn"
                  ${hasFinalFolder ? 'disabled style="opacity:.6;cursor:not-allowed;"' : ''}
                  onclick="${hasFinalFolder ? 'showOfferFinalizedMessage()' : `openFolderModal(${selectedOffer.id})`}"
                >
                  ${iconSvg('folder')} Neuer Ordner
                </button>
            </div>
            <div class="folder-grid">
                ${folders.length ? folders.map(f => buildFolderCardHTML(selectedOffer.id, f)).join('') : '<div class="oc-empty" style="padding:30px;">Keine Ordner angelegt.</div>'}
            </div>
          `;
      }
      detailHTML += `</div>`;

      return `<div class="oc-split-layout">${sidebarHTML}${detailHTML}</div>`;
  }

  function buildListItemHTML(o) {
      const isOpen = !!state.expanded[o.id];
      const folders = Array.isArray(o.folders) ? o.folders : [];
      const hasFinalFolder = folders.some(f => isFinalFolderStatus(f.status));
      return `
        <div class="oc-item">
            <div class="oc-item-header" onclick="toggleOffer(${o.id})">
            <div class="oc-ic ${isOpen ? 'is-open' : ''}">${iconSvg('chevron')}</div>
            <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Kunde / Objekt</div><div class="oc-main"><div class="oc-ttl"><span class="oc-tag">#${o.id}</span>${esc(getCustomerName(o))}</div><div class="oc-subt">${esc(getObjectName(o))}</div></div></div>
            <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Produkt / Service</div><div class="oc-main"><div class="oc-ttl" style="font-size:14px;">${esc(getProductName(o))}</div><div class="oc-subt">${esc(o.service || 'Standard')}</div></div></div>
            <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Erstellt von</div><div class="oc-main"><div class="oc-ttl" style="font-size:14px;">${esc(getCreatorName(o))}</div><div class="oc-subt">${esc(formatDateTime(o.created_at))}</div></div></div>
            <div class="oc-cell oc-responsive-col" onclick="event.stopPropagation()"><div class="oc-cell-title">Status</div><div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">${getStatusPill(o.status || 'draft')}</div></div>
            <div class="oc-cell oc-responsive-col"><div class="oc-cell-title">Ordner</div><div class="oc-main"><div class="oc-ttl" style="font-size:14px;">${folders.length}</div><div class="oc-subt">Verknüpfte Ordner</div></div></div>
            <div class="oc-actions-wrap" onclick="event.stopPropagation()">
                <select class="oc-select" onchange="quickStatus(${o.id}, this.value)" style="padding:6px 28px 6px 10px; min-height:36px;">
                ${Object.keys(STATUS_MAP).map(k => `<option value="${k}" ${o.status === k ? 'selected' : ''}>${STATUS_MAP[k].label}</option>`).join('')}
                </select>
            </div>
            <div class="oc-actions" onclick="event.stopPropagation()">
                <button class="oc-btn-ic" type="button" onclick="openModal(${o.id})" title="Bearbeiten">${iconSvg('edit')}</button>
                <button class="oc-btn-ic danger" type="button" onclick="deleteOffer(${o.id})" title="Löschen">${iconSvg('trash')}</button>
            </div>
            </div>
            <div class="oc-item-body ${isOpen ? 'is-open' : ''}">
            <div class="oc-item-body-top">
                <h4 class="oc-item-body-title">Ordnerübersicht</h4>
                <div class="oc-meta-inline">
                    <span class="oc-mini-badge">Angebot #${o.id}</span><span class="oc-mini-badge">${folders.length} Ordner</span><span class="oc-mini-badge">${esc(STATUS_MAP[o.status]?.label || 'Entwurf')}</span>
                    <button
                      type="button"
                      class="oc-btn"
                      ${hasFinalFolder ? 'disabled style="opacity:.6;cursor:not-allowed;"' : ''}
                      onclick="event.stopPropagation(); ${hasFinalFolder ? 'showOfferFinalizedMessage()' : `openFolderModal(${o.id})`}"
                    >
                      ${iconSvg('folder')} Ordner hinzufügen
                    </button>
                </div>
            </div>
            <div class="folder-grid">
                ${folders.length ? folders.map(f => buildFolderCardHTML(o.id, f)).join('') : '<div class="oc-empty" style="padding:30px;">Keine Ordner angelegt.</div>'}
            </div>
            </div>
        </div>
      `;
  }

function buildFolderCardHTML(offerId, f) {
    const locked = isFolderLocked(f);

    return `
      <div
          class="folder-card ${locked ? 'is-locked' : ''}"
          onclick='tryOpenFolder(${JSON.stringify({
            id: f.id,
            name: f.name || '',
            color: f.color || '#93c21c',
            status: f.status || 'draft'
          })})'
        >
          <div class="folder-icon" style="background:${esc(f.color || '#93c21c')}20; color:${esc(f.color || '#93c21c')};">
            ${locked ? iconSvg('lock') : iconSvg('folder')}
          </div>

          <div class="folder-meta">
              <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                  <div style="min-width:0; flex:1;">
                      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <div class="folder-title">${esc(f.name || 'Ordner')}</div>
                        ${locked ? `
                          <span style="
                            display:inline-flex;
                            align-items:center;
                            gap:5px;
                            padding:4px 8px;
                            border-radius:999px;
                            background:#fef2f2;
                            color:#b91c1c;
                            font-size:10px;
                            font-weight:900;
                            text-transform:uppercase;
                            border:1px solid #fecaca;
                          ">
                            ${iconSvg('lock')}
                            Gesperrt
                          </span>
                        ` : ''}
                      </div>

                      <div class="folder-sub">${esc(getFolderStatusLabel(f.status))}</div>
                  </div>

                  <div style="display:flex; gap:6px;" onclick="event.stopPropagation()">
                      <button
                          class="oc-btn-ic clone"
                          type="button"
                          title="${locked ? 'Nicht verfügbar – storniert' : 'Klonen'}"
                          onclick='${locked ? `
                            openAlertModal({
                              title: "Ordner gesperrt",
                              message: "Dieser Ordner ist storniert und kann nicht geklont werden.",
                              type: "alert",
                              confirmText: "OK"
                            })
                          ` : `
                            openCloneFolderModal(${JSON.stringify({
                              id: f.id,
                              name: f.name || '',
                              color: f.color || '#93c21c',
                              status: f.status || 'draft'
                            })})
                          `}'
                      >
                          ${iconSvg('clone')}
                      </button>

                      <button
                          class="oc-btn-ic"
                          type="button"
                          title="${locked ? 'Nicht verfügbar – storniert' : 'Bearbeiten'}"
                          onclick='${locked ? `
                            openAlertModal({
                              title: "Ordner gesperrt",
                              message: "Dieser Ordner ist storniert und kann nicht bearbeitet werden.",
                              type: "alert",
                              confirmText: "OK"
                            })
                          ` : `
                            openFolderModal(${offerId}, ${JSON.stringify({
                              id: f.id,
                              name: f.name || '',
                              color: f.color || '#93c21c',
                              status: f.status || 'draft'
                            })})
                          `}'
                      >
                          ${iconSvg('edit')}
                      </button>

                      <button
                          class="oc-btn-ic danger"
                          type="button"
                          title="${locked ? 'Nicht verfügbar – storniert' : 'Löschen'}"
                          onclick="${locked ? `
                            openAlertModal({
                              title: 'Ordner gesperrt',
                              message: 'Dieser Ordner ist storniert und kann nicht gelöscht werden.',
                              type: 'alert',
                              confirmText: 'OK'
                            })
                          ` : `deleteFolder(${offerId}, ${f.id})`}"
                      >
                          ${iconSvg('trash')}
                      </button>
                  </div>
              </div>

              <div class="folder-footer">
                  <div class="folder-user">
                      <img src="${getFolderCreatorImage(f)}" alt="${esc(getFolderCreatorName(f))}" class="folder-avatar" onerror="this.src='${DEFAULT_AVATAR}'">
                      <div class="folder-user-name">${esc(getFolderCreatorName(f))}</div>
                  </div>
                  <div class="folder-date">${esc(formatDate(f.created_at))}</div>
              </div>
          </div>
      </div>`;
}

  function toggleOffer(id) { state.expanded[id] = !state.expanded[id]; renderList(); }
  function handleSearch(val) { state.search = (val || '').toLowerCase().trim(); renderList(); }
  function handleFilter(key, val) { state.filters[key] = val || ''; renderList(); }
  
  function getProcessedOffers() {
    let filtered = [...state.offers];
    filtered = filtered.filter(o => {
      const searchHaystack = [String(o.id || ''), getCustomerName(o), getObjectName(o), getProductName(o), getCreatorName(o), o.service || '', o.status || '', o.alternative?.street || '', o.alternative?.city || ''].join(' ').toLowerCase();
      const matchesSearch = !state.search || searchHaystack.includes(state.search);
      const matchesStatus = !state.filters.status || (o.status || 'draft') === state.filters.status;
      const matchesProduct = !state.filters.product || getProductName(o) === state.filters.product;
      const matchesCreator = !state.filters.creator || getCreatorName(o) === state.filters.creator;
      let matchesFolders = true;
      if (state.filters.hasFolders === 'yes') matchesFolders = getFolderCount(o) > 0;
      if (state.filters.hasFolders === 'no') matchesFolders = getFolderCount(o) === 0;
      return matchesSearch && matchesStatus && matchesProduct && matchesCreator && matchesFolders;
    });

    filtered.sort((a, b) => {
      const dir = state.sort.dir === 'asc' ? 1 : -1;
      let av='', bv='';
      switch (state.sort.by) {
        case 'id': return (Number(a.id||0) - Number(b.id||0)) * dir;
        case 'customer': return getCustomerName(a).toLowerCase().localeCompare(getCustomerName(b).toLowerCase(), 'de') * dir;
        case 'product': return getProductName(a).toLowerCase().localeCompare(getProductName(b).toLowerCase(), 'de') * dir;
        case 'creator': return getCreatorName(a).toLowerCase().localeCompare(getCreatorName(b).toLowerCase(), 'de') * dir;
        case 'status': return (STATUS_MAP[a.status]?.label||'').localeCompare(STATUS_MAP[b.status]?.label||'', 'de') * dir;
        case 'folders': return (getFolderCount(a) - getFolderCount(b)) * dir;
        case 'created_at': return (new Date(a.created_at||0).getTime() - new Date(b.created_at||0).getTime()) * dir;
        default: return 0;
      }
    });
    return filtered;
  }

  function populateFilters() {
    const productFilter = document.getElementById('product-filter');
    const creatorFilter = document.getElementById('creator-filter');
    const products = [...new Set(state.offers.map(o => getProductName(o)).filter(Boolean))].sort((a,b) => a.localeCompare(b, 'de'));
    const creators = [...new Set(state.offers.map(o => getCreatorName(o)).filter(Boolean))].sort((a,b) => a.localeCompare(b, 'de'));
    productFilter.innerHTML = `<option value="">Alle Produkte</option>` + products.map(p => `<option value="${esc(p)}">${esc(p)}</option>`).join('');
    creatorFilter.innerHTML = `<option value="">Alle Benutzer</option>` + creators.map(c => `<option value="${esc(c)}">${esc(c)}</option>`).join('');
    productFilter.value = state.filters.product || ''; creatorFilter.value = state.filters.creator || '';
  }

  function renderAnalytics() {
    const offers = state.offers || [];
    const countBy = (status) => offers.filter(o => (o.status || 'draft') === status).length;
    document.getElementById('stat-total').textContent = offers.length;
    document.getElementById('stat-draft').textContent = countBy('draft');
    document.getElementById('stat-sent').textContent = countBy('sent');
    document.getElementById('stat-negotiation').textContent = countBy('negotiation');
    document.getElementById('stat-final').textContent = countBy('final');
    document.getElementById('stat-cancel').textContent = countBy('cancel');
  }

  function handleSortChange() { state.sort.by = document.getElementById('sort-by').value; state.sort.dir = 'desc'; syncSortMarks(); renderList(); }
  function toggleColumnSort(column) {
    if (state.sort.by === column) state.sort.dir = state.sort.dir === 'asc' ? 'desc' : 'asc';
    else { state.sort.by = column; state.sort.dir = column === 'id' || column === 'created_at' || column === 'folders' ? 'desc' : 'asc'; }
    document.getElementById('sort-by').value = state.sort.by;
    syncSortMarks(); renderList();
  }
  function syncSortMarks() {
    document.querySelectorAll('.oc-sort-mark').forEach(el => { el.textContent = '↕'; el.classList.remove('active'); });
    const active = document.getElementById(`sort-mark-${state.sort.by}`);
    if (active) { active.textContent = state.sort.dir === 'asc' ? '↑' : '↓'; active.classList.add('active'); }
  }
  function resetFilters() {
    state.search = ''; state.filters = { status: '', product: '', creator: '', hasFolders: '' }; state.sort = { by: 'id', dir: 'desc' };
    document.getElementById('search-input').value = ''; document.getElementById('status-filter').value = '';
    document.getElementById('product-filter').value = ''; document.getElementById('creator-filter').value = '';
    document.getElementById('folder-filter').value = ''; document.getElementById('sort-by').value = 'id';
    syncSortMarks(); renderList();
  }

  /* MODALS & ALERTS */
  function toast(kind, title, msg) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;
    const icons = {
      ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
      warn: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>`,
      bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
    };
    const el = document.createElement('div'); el.className = 'oc-toast';
    el.innerHTML = `<div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div><div style="flex:1;"><p class="oc-toast-ttl">${esc(title)}</p><p class="oc-toast-msg">${esc(msg)}</p></div><button class="oc-toast-x" onclick="this.parentElement.remove()"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg></button>`;
    wrap.appendChild(el); setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
  }

  function openCloneFolderModal(folder) {
    if (!folder) return;

    document.getElementById('clone-folder-id').value = folder.id || '';
    document.getElementById('clone-folder-name').value = (folder.name || 'Ordner') + ' - Kopie';
    document.getElementById('clone-folder-color').value = folder.color || '#93c21c';
    document.getElementById('clone-folder-status').value = folder.status || 'draft';
    document.getElementById('clone-folder-everything').checked = true;

    document.getElementById('clone-folder-modal').classList.add('open');
  }

  function closeCloneFolderModal() {
    document.getElementById('clone-folder-modal').classList.remove('open');
  }

  async function submitCloneFolderForm(e) {
    e.preventDefault();

    const folderId = document.getElementById('clone-folder-id').value;
    const payload = {
      name: document.getElementById('clone-folder-name').value.trim(),
      color: document.getElementById('clone-folder-color').value,
      status: document.getElementById('clone-folder-status').value,
      clone_everything: document.getElementById('clone-folder-everything').checked ? 1 : 0,
    };

    if (!payload.name) {
      return openAlertModal({
        title: 'Pflichtfeld fehlt',
        message: 'Bitte einen neuen Ordnernamen eingeben.',
        type: 'alert'
      });
    }

    const submitBtn = document.getElementById('clone-folder-submit-btn');
    submitBtn.disabled = true;

    try {
      const res = await fetch(`${ENDPOINTS.cloneFolderBase}/${folderId}/clone`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify(payload)
      });

      const text = await res.text();
      let data = {};

      try {
        data = JSON.parse(text);
      } catch (err) {
        throw new Error('Der Server hat keine gültige JSON-Antwort zurückgegeben.');
      }

      if (!res.ok || !data.success) {
        throw new Error(data.message || 'Ordner konnte nicht geklont werden.');
      }

      closeCloneFolderModal();
      await loadData();
      renderList();
      toast('ok', 'Geklont', data.message || 'Ordner wurde erfolgreich geklont.');
    } catch (err) {
      openAlertModal({
        title: 'Fehler',
        message: esc(err.message),
        type: 'alert'
      });
    } finally {
      submitBtn.disabled = false;
    }
  }

  function openAlertModal({ title = 'Hinweis', message = '', type = 'alert', confirmText = 'OK', cancelText = 'Abbrechen' }) {
    const modal = document.getElementById('alert-modal'); const actionsEl = document.getElementById('alert-modal-actions');
    if (!modal) return Promise.resolve(false);
    document.getElementById('alert-modal-title').textContent = title;
    document.getElementById('alert-modal-message').innerHTML = message;
    if (type === 'confirm') { actionsEl.innerHTML = `<button type="button" class="oc-btn-soft" onclick="resolveAlertModal(false)">${cancelText}</button><button type="button" class="oc-btn danger" onclick="resolveAlertModal(true)">${confirmText}</button>`; }
    else { actionsEl.innerHTML = `<button type="button" class="oc-btn" onclick="resolveAlertModal(true)">${confirmText}</button>`; }
    modal.classList.add('open');
    return new Promise(resolve => { alertModalResolver = resolve; });
  }
  function resolveAlertModal(value) { document.getElementById('alert-modal')?.classList.remove('open'); if (typeof alertModalResolver === 'function') alertModalResolver(value); alertModalResolver = null; }
  function closeAlertModal() { resolveAlertModal(false); }
  function closeDuplicateOfferModal() { document.getElementById('duplicate-offer-modal')?.classList.remove('open'); }

  function openDuplicateOfferModal(existingOffer) {
    const content = document.getElementById('duplicate-offer-content'); const openBtn = document.getElementById('duplicate-offer-open-btn');
    if (!existingOffer) {
      content.innerHTML = `<div class="oc-empty" style="padding:20px;">Kein bestehendes Angebot gefunden.</div>`; openBtn.onclick = closeDuplicateOfferModal;
      document.getElementById('duplicate-offer-modal').classList.add('open'); return;
    }
    content.innerHTML = `<div style="border:1px solid var(--border); border-radius:14px; padding:16px; background:#fafafa;">
      <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;"><span class="oc-mini-badge">Angebot #${existingOffer.id}</span><span class="oc-mini-badge">${esc(STATUS_MAP[existingOffer.status]?.label || 'Entwurf')}</span><span class="oc-mini-badge">${getFolderCount(existingOffer)} Ordner</span></div>
      <div style="display:grid; gap:10px;"><div><strong>Kunde:</strong> ${esc(getCustomerName(existingOffer))}</div><div><strong>Objekt:</strong> ${esc(getObjectName(existingOffer))}</div><div><strong>Produkt:</strong> ${esc(getProductName(existingOffer))}</div><div><strong>Erstellt von:</strong> ${esc(getCreatorName(existingOffer))}</div></div>
    </div>`;
    openBtn.onclick = function () {
      closeDuplicateOfferModal();
      if(state.viewMode === 'split') { selectOfferInSplitView(existingOffer.id); } 
      else { state.expanded[existingOffer.id] = true; renderList(); const row = document.querySelector(`[onclick="toggleOffer(${existingOffer.id})"]`); if (row) row.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    };
    document.getElementById('duplicate-offer-modal').classList.add('open');
  }

  function openModal(id = null) {
    const modal = document.getElementById('crud-modal'); const form = document.getElementById('crud-form');
    form.reset(); $('#inp-customer-object').val(null).trigger('change'); $('#inp-product').html('<option value="">Bitte zuerst Kunde & Objekt wählen</option>').prop('disabled', true);
    document.getElementById('inp-customer').value = ''; document.getElementById('inp-alternative').value = '';
    if (id) {
      const offer = state.offers.find(x => Number(x.id) === Number(id)); if (!offer) return;
      document.getElementById('modal-title').textContent = 'Angebot bearbeiten';
      document.getElementById('inp-id').value = offer.id; document.getElementById('inp-service').value = offer.service || '';
      document.getElementById('btn-submit').textContent = 'Änderungen speichern';
      document.getElementById('folder-creation-section').style.display = 'none'; document.getElementById('inp-folder-name').required = false;
      document.getElementById('inp-customer').value = offer.customer_id || ''; document.getElementById('inp-alternative').value = offer.alternative_id || '';
      const newOption = new Option(`${getCustomerName(offer)} | Objekt: ${getObjectName(offer)}`, offer.alternative_id || `lead_${offer.customer_id}`, true, true);
      $('#inp-customer-object').append(newOption).trigger('change');
      fetchProducts(offer.customer_id, offer.alternative_id, offer.product_id);
    } else {
      document.getElementById('modal-title').textContent = 'Neues Angebot & Ordner'; document.getElementById('inp-id').value = ''; document.getElementById('btn-submit').textContent = 'Erstellen';
      document.getElementById('folder-creation-section').style.display = 'block'; document.getElementById('inp-folder-name').required = true; document.getElementById('inp-folder-color').value = '#93c21c';
    }
    modal.classList.add('open');
  }
  function closeModal() { document.getElementById('crud-modal').classList.remove('open'); }

  function fetchProducts(customerId, alternativeId, selectedProductId = null) {
    const prodSel = $('#inp-product'); const currentOfferId = document.getElementById('inp-id').value || '';
    prodSel.prop('disabled', true).html('<option>Lade...</option>');
    fetch(`${ENDPOINTS.getProducts}?customer_id=${encodeURIComponent(customerId || '')}&alternative_id=${encodeURIComponent(alternativeId || '')}&offer_id=${encodeURIComponent(currentOfferId)}`)
      .then(r => r.json()).then(products => {
        let html = '<option value="">Produkt wählen...</option>';
        (products || []).forEach(p => {
          const sel = (selectedProductId && Number(p.id) === Number(selectedProductId)) ? 'selected' : '';
          const duplicateText = p.has_existing_offer ? ` — Angebot existiert #${p.existing_offer_id}` : '';
          html += `<option value="${p.id}" ${sel} data-has-existing-offer="${p.has_existing_offer ? 1 : 0}" data-existing-offer-id="${p.existing_offer_id || ''}">${esc(p.text + duplicateText)}</option>`;
        });
        prodSel.html(html).prop('disabled', false);
      }).catch(() => { prodSel.html('<option value="">Fehler beim Laden</option>').prop('disabled', false); });
  }

  async function submitForm(e) {
      e.preventDefault();
      const id = document.getElementById('inp-id').value; const isUpdate = !!id;
      const payload = { customer_id: document.getElementById('inp-customer').value, alternative_id: document.getElementById('inp-alternative').value, product_id: document.getElementById('inp-product').value, service: document.getElementById('inp-service').value };
      if (!isUpdate) { payload.folder_name = document.getElementById('inp-folder-name').value; payload.folder_color = document.getElementById('inp-folder-color').value; }
      if (!payload.customer_id || !payload.alternative_id || !payload.product_id) return openAlertModal({ title: 'Pflichtfelder fehlen', message: 'Bitte Kunde, Objekt und Produkt auswählen.', type: 'alert' });
      const submitBtn = document.getElementById('btn-submit'); submitBtn.disabled = true;
      try {
          const existingSameOffer = state.offers.find(o =>
            Number(o.customer_id || 0) === Number(payload.customer_id || 0) &&
            Number(o.alternative_id || 0) === Number(payload.alternative_id || 0) &&
            Number(o.product_id || 0) === Number(payload.product_id || 0)
          );

          if (!isUpdate && existingSameOffer && Array.isArray(existingSameOffer.folders)) {
            const hasFinalFolder = existingSameOffer.folders.some(folder => isFinalFolderStatus(folder.status));
            if (hasFinalFolder) {
              await showOfferFinalizedMessage();
              submitBtn.disabled = false;
              return;
            }
          }
          const res = await fetch(isUpdate ? `${ENDPOINTS.updateBase}/${id}` : ENDPOINTS.store, { method: isUpdate ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify(payload) });
          const text = await res.text(); let data = {}; try { data = JSON.parse(text); } catch(err) { throw new Error('Der Server hat keine gültige JSON-Antwort zurückgegeben.'); }
          if (!res.ok || !data.success) {
              if (data.code === 'OFFER_ALREADY_EXISTS') { closeModal(); openDuplicateOfferModal(data.existing_offer || null); return; }
              throw new Error(data.message || 'Speichern fehlgeschlagen.');
          }
          if (isUpdate) { const idx = state.offers.findIndex(x => Number(x.id) === Number(id)); if (idx !== -1) state.offers[idx] = data.data; toast('ok', 'Aktualisiert', 'Angebot gespeichert.'); } 
          else { state.offers.unshift(data.data); toast('ok', 'Erstellt', 'Angebot & Ordner angelegt.'); state.selectedOfferId = data.data.id; } 
          renderAnalytics(); populateFilters(); renderList(); closeModal();
      } catch (err) { openAlertModal({ title: 'Fehler', message: esc(err.message), type: 'alert' }); } 
      finally { submitBtn.disabled = false; }
  }

  function openFolderModal(offerId, folder = null) {
      const isEdit = !!folder?.id;

      if (!isEdit && offerHasFinalFolder(offerId)) {
          showOfferFinalizedMessage();
          return;
      }

      document.getElementById('folder-offer-id').value = offerId || '';
      document.getElementById('folder-id').value = folder?.id || '';
      document.getElementById('folder-name').value = folder?.name || '';
      document.getElementById('folder-color').value = folder?.color || '#93c21c';
      document.getElementById('folder-status').value = folder?.status || 'draft';
      document.getElementById('folder-modal-title').textContent = folder?.id ? 'Ordner bearbeiten' : 'Ordner erstellen';
      document.getElementById('folder-submit-btn').textContent = folder?.id ? 'Aktualisieren' : 'Erstellen';
      document.getElementById('folder-modal').classList.add('open');
  }
  function closeFolderModal() { document.getElementById('folder-modal').classList.remove('open'); }

 async function submitFolderForm(e) {
    e.preventDefault();

    const offerId = document.getElementById('folder-offer-id').value;
    const folderId = document.getElementById('folder-id').value;
    const isEdit = !!folderId;

    const payload = {
      name: document.getElementById('folder-name').value.trim(),
      color: document.getElementById('folder-color').value,
      status: document.getElementById('folder-status').value
    };

    if (!payload.name) {
      return openAlertModal({
        title: 'Pflichtfeld fehlt',
        message: 'Bitte Ordnernamen eingeben.',
        type: 'alert'
      });
    }

    const submitBtn = document.getElementById('folder-submit-btn');
    submitBtn.disabled = true;

    if (!isEdit && offerHasFinalFolder(offerId)) {
      submitBtn.disabled = false;
      await showOfferFinalizedMessage();
      return;
    }

    try {
      const res = await fetch(
        isEdit
          ? `${ENDPOINTS.folderUpdateBase}/${folderId}`
          : `${ENDPOINTS.folderStoreBase}/${offerId}/folders`,
        {
          method: isEdit ? 'PUT' : 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CSRF
          },
          body: JSON.stringify(payload)
        }
      );

      const text = await res.text();
      let data = JSON.parse(text);

      if (!res.ok || !data.success) throw new Error(data.message || 'Ordner konnte nicht gespeichert werden.');

      closeFolderModal();
      await loadData();
      renderList();
      toast('ok', isEdit ? 'Aktualisiert' : 'Erstellt', data.message || 'Erfolgreich.');
    } catch (e) {
      openAlertModal({ title: 'Fehler', message: esc(e.message), type: 'alert' });
    } finally {
      submitBtn.disabled = false;
    }
}

  async function deleteFolder(offerId, folderId) {
      const confirmed = await openAlertModal({ title: 'Ordner löschen', message: 'Wirklich löschen?', type: 'confirm', confirmText: 'Ja, löschen' });
      if (!confirmed) return;
      try {
          const res = await fetch(`${ENDPOINTS.folderUpdateBase}/${folderId}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF } });
          const data = await res.json();
          if (!res.ok || !data.success) throw new Error(data.message || 'Löschen fehlgeschlagen.');
          await loadData(); renderList(); toast('ok', 'Gelöscht', data.message || 'Ordner wurde entfernt.');
      } catch (e) { openAlertModal({ title: 'Fehler', message: esc(e.message), type: 'alert' }); }
  }

  async function deleteOffer(id) {
    const confirmed = await openAlertModal({ title: 'Angebot löschen', message: 'Möchten Sie dieses Angebot wirklich löschen?', type: 'confirm', confirmText: 'Ja, löschen' });
    if (!confirmed) return;
    try {
      const res = await fetch(`${ENDPOINTS.updateBase}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
      const data = await res.json();
      if (data.success) { state.offers = state.offers.filter(x => Number(x.id) !== Number(id)); renderAnalytics(); populateFilters(); renderList(); toast('ok', 'Gelöscht', 'Angebot wurde entfernt.'); } 
      else { openAlertModal({ title: 'Fehler', message: esc(data.message), type: 'alert' }); }
    } catch (e) { openAlertModal({ title: 'Netzwerkfehler', message: 'Konnte Angebot nicht löschen.', type: 'alert' }); }
  }

  async function quickStatus(id, newStatus) {
    try {
      const res = await fetch(`${ENDPOINTS.updateBase}/${id}/status`, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify({ status: newStatus }) });
      const data = await res.json();
      if (data.success) { const offer = state.offers.find(x => Number(x.id) === Number(id)); if (offer) offer.status = newStatus; renderAnalytics(); renderList(); toast('ok', 'Status aktualisiert', 'Angebot aktualisiert.'); } 
      else { openAlertModal({ title: 'Fehler', message: esc(data.message), type: 'alert' }); }
    } catch (e) { openAlertModal({ title: 'Netzwerkfehler', message: 'Netzwerkfehler beim Statuswechsel.', type: 'alert' }); }
  }

  function openFolderPreview(folderId) { window.location.href = `/admin/offers/folders/${folderId}`; }

  $(document).ready(function () {
    const $customerObject = $('#inp-customer-object');
    $customerObject.select2({ dropdownParent: $('#crud-modal .oc-modal'), width: '100%', placeholder: 'Kunde oder Objekt suchen...', allowClear: true, minimumInputLength: 1, ajax: { url: ENDPOINTS.searchCustomerObjects, dataType: 'json', delay: 250, cache: true, data: function (params) { return { q: params.term || '' }; }, processResults: function (data) { return { results: data.results || data.data?.results || [] }; } } });
    $customerObject.on('select2:select', function (e) { const data = e.params.data || {}; document.getElementById('inp-customer').value = data.customer_id || ''; document.getElementById('inp-alternative').value = data.alternative_id || ''; fetchProducts(data.customer_id, data.alternative_id); });
    $customerObject.on('select2:clear', function () { document.getElementById('inp-customer').value = ''; document.getElementById('inp-alternative').value = ''; $('#inp-product').html('<option value="">Bitte zuerst Kunde & Objekt wählen</option>').prop('disabled', true); });
    $('#inp-product').on('change', function () { const sel = this.options[this.selectedIndex]; if (!sel) return; if (String(sel.dataset.hasExistingOffer || '0') === '1' && sel.dataset.existingOfferId) { const existing = state.offers.find(o => Number(o.id) === Number(sel.dataset.existingOfferId)); if (existing) openDuplicateOfferModal(existing); } });
    
    syncSortMarks(); loadData();
  });
</script>
@endpush
@endonce