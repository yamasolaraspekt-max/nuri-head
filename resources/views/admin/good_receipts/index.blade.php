@extends('admin.layouts.app')

@section('title', 'LogisSync ERP | Wareneingang')

@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    :root{
        --erp-bg:#f4f7fa;
        --erp-panel:#ffffff;
        --erp-panel-soft:#f9fbfc;
        --erp-line:#c0d8ea;
        --erp-line-2:#dfeaf2;
        --erp-text:#2f2f2f;
        --erp-muted:#617b8c;
        --erp-muted-2:#8ca8ba;
        --erp-blue:#74b2d4;
        --erp-blue-dark:#5a98bd;
        --erp-green:#93c21c;
        --erp-green-dark:#82ad18;
        --erp-green-soft:#eff7dd;
        --erp-red:#c0392b;
        --erp-red-soft:#fff1f1;
        --erp-shadow:0 8px 24px rgba(39,72,99,.08);
        --erp-shadow-lg:0 22px 48px rgba(24,44,64,.18);
        --erp-radius-lg:18px;
        --erp-font: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    }

    .erp-page,
    .erp-page *{ box-sizing:border-box; font-family:var(--erp-font); }

    .erp-page{ padding:16px; background:var(--erp-bg); }
    .erp-hide{ display:none !important; }

    .custom-scrollbar::-webkit-scrollbar{ width:6px; height:6px; }
    .custom-scrollbar::-webkit-scrollbar-track{ background:transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb{ background:#c0d8ea; border-radius:10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover{ background:#74b2d4; }

    @keyframes erpFadeIn{ from{opacity:0;} to{opacity:1;} }
    .erp-fade-in{ animation:erpFadeIn .2s ease-in-out; }

    .erp-app{
        display:flex; flex-direction:column; height:calc(100vh - 32px); min-height:820px;
        background:var(--erp-panel); border:1px solid var(--erp-line); border-radius:var(--erp-radius-lg);
        box-shadow:var(--erp-shadow); overflow:hidden; color:var(--erp-text); margin-top:86px;
    }

    .erp-topbar{ padding:12px 16px; background:#f0f4f8; border-bottom:1px solid var(--erp-line); flex:0 0 auto; }
    .erp-topnav{ display:flex; align-items:center; gap:8px; overflow-x:auto; white-space:nowrap; }
    .erp-main{ display:flex; flex-direction:column; min-height:0; flex:1 1 auto; background:var(--erp-panel); }
    .erp-module-header{ flex:0 0 auto; border-bottom:1px solid var(--erp-line); background:var(--erp-panel-soft); }
    .erp-module-body{ flex:1 1 auto; min-height:0; overflow-y:auto; padding:24px; background:var(--erp-panel-soft); }

    .erp-shell{ max-width:1420px; margin:0 auto; }
    .erp-shell-md{ max-width:1240px; margin:0 auto; }
    .erp-shell-sm{ max-width:1060px; margin:0 auto; }

    .erp-row{ display:flex; gap:16px; align-items:flex-start; }
    .erp-row-middle{ align-items:center; }
    .erp-row-between{ display:flex; gap:16px; align-items:center; justify-content:space-between; }
    .erp-wrap{ flex-wrap:wrap; }
    .erp-grow{ flex:1 1 auto; }

    .erp-grid{ display:grid; gap:16px; }
    .erp-grid-2{ grid-template-columns:repeat(2,minmax(0,1fr)); }
    .erp-grid-3{ grid-template-columns:repeat(3,minmax(0,1fr)); }
    .erp-grid-4{ grid-template-columns:repeat(4,minmax(0,1fr)); }
    .erp-grid-5{ grid-template-columns:repeat(5,minmax(0,1fr)); }

    .erp-title-xl{ font-size:30px; line-height:1.15; font-weight:800; color:var(--erp-text); }
    .erp-title-lg{ font-size:22px; line-height:1.2; font-weight:800; color:var(--erp-text); }
    .erp-title-md{ font-size:18px; line-height:1.25; font-weight:800; color:var(--erp-text); }
    .erp-title-sm{ font-size:14px; line-height:1.3; font-weight:800; color:var(--erp-text); }

    .erp-text-sm{ font-size:13px; }
    .erp-text-xs{ font-size:12px; }
    .erp-text-muted{ color:var(--erp-muted); }
    .erp-text-soft{ color:var(--erp-muted-2); }
    .erp-text-blue{ color:var(--erp-blue); }
    .erp-text-green{ color:#4f6e0b; }
    .erp-text-red{ color:#b42318; }

    .erp-kicker{
        font-size:11px; font-weight:800; text-transform:uppercase;
        letter-spacing:.08em; color:var(--erp-muted-2);
    }

    .erp-divider{ height:1px; margin:0; border:0; background:var(--erp-line); }

    .erp-card{
        background:var(--erp-panel); border:1px solid var(--erp-line);
        border-radius:var(--erp-radius-lg); box-shadow:var(--erp-shadow);
    }

    .erp-card-soft{ background:var(--erp-panel-soft); }
    .erp-card-green{ background:var(--erp-green-soft); border-color:#d6e9a9; }
    .erp-card-red{ background:var(--erp-red-soft); border-color:#ffd8d8; }

    .erp-card-body{ padding:20px; }
    .erp-card-head{ padding:16px 20px; border-bottom:1px solid var(--erp-line); background:#f0f4f8; }

    .erp-stat{ min-height:132px; cursor:pointer; transition:.15s ease; }
    .erp-stat:hover{ border-color:var(--erp-blue); }
    .erp-stat-top{ display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .erp-stat-label{ font-size:13px; font-weight:700; color:var(--erp-muted); }
    .erp-stat-value{ margin-top:18px; font-size:40px; line-height:1; font-weight:800; color:var(--erp-blue); }

    .erp-tab{
        display:inline-flex; align-items:center; gap:8px; border:0; padding:10px 14px; background:transparent;
        border-radius:10px; font-size:14px; font-weight:700; color:var(--erp-muted); cursor:pointer; transition:.15s ease;
    }
    .erp-tab:hover{ background:rgba(192,216,234,.4); }
    .erp-tab.is-active{ background:var(--erp-line); color:var(--erp-text); box-shadow:0 2px 8px rgba(0,0,0,.05); }
    .erp-tab-icon{ color:var(--erp-muted-2); display:inline-flex; align-items:center; }
    .erp-tab.is-active .erp-tab-icon{ color:var(--erp-blue); }

    .erp-subtabs{ display:flex; gap:24px; margin-top:14px; }
    .erp-subtab{
        border:0; background:transparent; padding:0 0 12px 0; border-bottom:2px solid transparent;
        margin-bottom:-2px; font-size:14px; font-weight:800; color:var(--erp-muted-2); cursor:pointer;
    }
    .erp-subtab.is-active{ color:var(--erp-blue); border-bottom-color:var(--erp-blue); }

    .erp-badge{
        display:inline-flex; align-items:center; justify-content:center; min-width:20px; height:20px;
        padding:0 6px; border-radius:999px; font-size:10px; line-height:1; font-weight:800;
    }
    .erp-badge-soft{ background:var(--erp-line); color:var(--erp-text); }
    .erp-badge-blue{ background:var(--erp-blue); color:#fff; }
    .erp-badge-green{ background:var(--erp-green); color:#fff; }
    .erp-badge-red{ background:var(--erp-red); color:#fff; }

    .erp-status{
        display:inline-flex; align-items:center; gap:6px; padding:4px 8px; border-radius:8px;
        font-size:11px; line-height:1; font-weight:800; border:1px solid; white-space:nowrap;
    }
    .erp-status-pending{ background:rgba(192,216,234,.35); color:#4a85a8; border-color:#c0d8ea; }
    .erp-status-processing{ background:rgba(207,224,155,.45); color:#6a8f10; border-color:#cfe09b; }
    .erp-status-completed{ background:rgba(147,194,28,.18); color:#4f6e0b; border-color:rgba(147,194,28,.35); }
    .erp-status-issued{ background:rgba(116,178,212,.18); color:#3b718f; border-color:rgba(116,178,212,.35); }
    .erp-status-issue{ background:#fff1f2; color:#b42318; border-color:#ffdadd; }
    .erp-status-neutral{ background:#f0f4f8; color:var(--erp-muted); border-color:var(--erp-line); }

    .erp-input-wrap{
        display:flex; align-items:center; gap:10px; min-height:44px; padding:10px 12px;
        border:1px solid var(--erp-line); border-radius:12px; background:#fff;
    }
    .erp-input-icon{ color:var(--erp-blue); display:inline-flex; align-items:center; }

    .erp-input,
    .erp-select,
    .erp-textarea{
        width:100%; min-height:44px; padding:10px 14px; border:1px solid var(--erp-line);
        border-radius:12px; background:#fff; color:var(--erp-text); font-size:14px; outline:none; transition:.15s ease;
    }

    .erp-input-wrap .erp-input{
        min-height:auto; padding:0; border:0; background:transparent; box-shadow:none;
    }

    .erp-input:focus,
    .erp-select:focus,
    .erp-textarea:focus{
        border-color:var(--erp-blue); box-shadow:0 0 0 3px rgba(116,178,212,.18);
    }

    .erp-textarea{ min-height:96px; resize:vertical; line-height:1.45; }
    .erp-field{ display:flex; flex-direction:column; gap:6px; }
    .erp-label{ font-size:13px; font-weight:700; color:var(--erp-text); }

    .erp-btn{
        display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:42px;
        padding:10px 16px; border:1px solid transparent; border-radius:12px; background:transparent;
        color:var(--erp-text); font-size:14px; font-weight:800; text-decoration:none; cursor:pointer; transition:.15s ease;
    }
    .erp-btn:disabled{ opacity:.5; pointer-events:none; }
    .erp-btn-primary{ background:var(--erp-green); color:#fff; }
    .erp-btn-primary:hover{ background:var(--erp-green-dark); }
    .erp-btn-secondary{ background:var(--erp-blue); color:#fff; }
    .erp-btn-secondary:hover{ background:var(--erp-blue-dark); }
    .erp-btn-light{ background:#fff; color:var(--erp-muted); border-color:var(--erp-line); }
    .erp-btn-light:hover{ background:var(--erp-panel-soft); color:var(--erp-blue); border-color:var(--erp-blue); }
    .erp-btn-ghost{ color:var(--erp-muted); background:transparent; }
    .erp-btn-ghost:hover{ background:#edf4f8; color:var(--erp-blue); }
    .erp-btn-danger{ background:var(--erp-red); color:#fff; }
    .erp-btn-danger:hover{ background:#a93226; }
    .erp-btn-icon{ width:38px; min-width:38px; height:38px; padding:0; border-radius:10px; }

    .erp-toggle{
        display:flex; gap:4px; padding:4px; background:#fff; border:1px solid var(--erp-line);
        border-radius:14px; box-shadow:var(--erp-shadow);
    }

    .erp-toggle-btn{
        display:inline-flex; align-items:center; justify-content:center; min-width:40px; min-height:38px;
        border:0; border-radius:10px; background:transparent; color:var(--erp-muted-2); cursor:pointer; transition:.15s ease;
    }
    .erp-toggle-btn.is-active{ background:var(--erp-line); color:var(--erp-text); box-shadow:0 2px 8px rgba(0,0,0,.05); }

    .erp-table-wrap{
        background:#fff; border:1px solid var(--erp-line); border-radius:var(--erp-radius-lg);
        overflow:hidden; box-shadow:var(--erp-shadow);
    }

    .erp-table{ width:100%; border-collapse:collapse; table-layout:auto; }
    .erp-table thead tr{ background:#f0f4f8; color:var(--erp-muted); }
    .erp-table th, .erp-table td{ padding:14px 16px; text-align:left; vertical-align:middle; font-size:14px; }
    .erp-table th{ font-weight:700; border-bottom:1px solid var(--erp-line); }
    .erp-table td{ border-bottom:1px solid var(--erp-line-2); }
    .erp-table tbody tr:hover{ background:#f9fbfc; }
    .erp-table-right{ text-align:right !important; }
    .erp-table-click{ cursor:pointer; }
    .erp-table-sort{ cursor:pointer; user-select:none; }

    .erp-pagination{
        display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 16px;
        border-top:1px solid var(--erp-line); background:#f0f4f8;
    }
    .erp-pagination-pages{ display:flex; flex-wrap:wrap; gap:8px; }
    .erp-page-btn{
        width:36px; height:36px; border:1px solid var(--erp-line); border-radius:10px; background:#fff;
        color:var(--erp-muted); font-size:13px; font-weight:800; cursor:pointer;
    }
    .erp-page-btn.is-active{ background:var(--erp-blue); color:#fff; border-color:var(--erp-blue); }

    .erp-kanban{ display:flex; gap:20px; min-height:580px; overflow-x:auto; padding-bottom:10px; }
    .erp-kanban-col{
        flex:1 1 300px; min-width:300px; border:1px solid var(--erp-line);
        border-radius:var(--erp-radius-lg); overflow:hidden; background:#fff;
    }
    .erp-kanban-head{
        display:flex; align-items:center; justify-content:space-between; gap:8px; padding:14px 16px;
        border-bottom:1px solid var(--erp-line); font-size:14px; font-weight:800; background:#f7fbfe;
    }
    .erp-kanban-body{ padding:12px; min-height:260px; }
    .erp-kanban-body.is-drag-over{
        background:#eef7fc;
        outline:2px dashed var(--erp-blue);
        outline-offset:-8px;
    }
    .erp-kanban-card{
        padding:14px; border:1px solid var(--erp-line); border-radius:14px; background:#fff;
        box-shadow:0 4px 14px rgba(52,84,117,.05); cursor:grab; transition:.15s ease;
    }
    .erp-kanban-card:active{ cursor:grabbing; }
    .erp-kanban-card.is-dragging{ opacity:.5; }
    .erp-kanban-card + .erp-kanban-card{ margin-top:12px; }
    .erp-kanban-card:hover{ border-color:var(--erp-blue); }

    .erp-stock-item{
        display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px;
        border:1px solid var(--erp-line); border-radius:12px; background:#fff;
    }
    .erp-stock-item + .erp-stock-item{ margin-top:10px; }
    .erp-stock-item.is-highlight{ background:#eef7fc; border-color:var(--erp-blue); }

    .erp-stock-head{ display:flex; align-items:center; justify-content:space-between; gap:14px; padding:18px 20px; cursor:pointer; }
    .erp-stock-details{ padding:18px 20px; border-top:1px solid var(--erp-line); background:#f9fbfc; }

    .erp-note{ padding:12px 14px; border:1px solid var(--erp-line); border-radius:14px; background:#f9fbfc; }
    .erp-notification{
        padding:20px; border:1px solid var(--erp-line); border-radius:var(--erp-radius-lg);
        background:#fff; box-shadow:var(--erp-shadow);
    }
    .erp-notification + .erp-notification{ margin-top:16px; }

    .erp-kv{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-top:16px; }
    .erp-kv-item{ padding:12px 14px; border:1px solid var(--erp-line); border-radius:14px; background:#f9fbfc; }
    .erp-kv-label{
        font-size:11px; font-weight:800; text-transform:uppercase;
        letter-spacing:.06em; color:var(--erp-muted-2);
    }
    .erp-kv-value{ margin-top:6px; font-size:13px; font-weight:700; color:var(--erp-text); }

    .erp-meta{ font-size:12px; color:var(--erp-muted); }
    .erp-code{
        display:inline-flex; align-items:center; min-height:28px; padding:4px 10px; border:1px solid var(--erp-line);
        border-radius:10px; background:#fff; color:var(--erp-blue); font-size:12px; font-weight:800;
        font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;
    }

    .erp-modal-layer{
        position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:16px;
    }
    .erp-modal-backdrop{ position:absolute; inset:0; background:rgba(47,47,47,.6); backdrop-filter:blur(4px); }
    .erp-modal{
        position:relative; width:100%; max-width:1180px; max-height:92vh; display:flex; flex-direction:column;
        background:#fff; border-radius:28px; box-shadow:var(--erp-shadow-lg); overflow:hidden; border:1px solid rgba(255,255,255,.7);
    }
    .erp-modal-sm{ max-width:760px; }
    .erp-modal-lg{ max-width:1280px; }
    .erp-modal-head{
        display:flex; align-items:center; justify-content:space-between; gap:16px; padding:18px 24px;
        border-bottom:1px solid var(--erp-line); background:#f0f4f8;
    }
    .erp-modal-body{ flex:1 1 auto; min-height:0; overflow-y:auto; padding:24px; background:#fff; }
    .erp-modal-foot{
        display:flex; align-items:center; justify-content:flex-end; gap:12px; padding:16px 24px;
        border-top:1px solid var(--erp-line); background:#f0f4f8;
    }

    .erp-form-section + .erp-form-section{ margin-top:24px; }
    .erp-form-grid{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    .erp-form-grid-3{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
    .erp-span-2{ grid-column:span 2; }
    .erp-span-3{ grid-column:span 3; }

    .erp-chip-group{
        display:flex; flex-wrap:wrap; gap:8px; padding:6px; border:1px solid var(--erp-line);
        border-radius:16px; background:#f0f4f8; width:max-content; max-width:100%;
    }
    .erp-chip{
        border:0; padding:10px 14px; border-radius:12px; background:transparent; color:var(--erp-muted-2);
        font-size:14px; font-weight:800; cursor:pointer; transition:.15s ease;
    }
    .erp-chip.is-active{ background:#fff; color:var(--erp-blue); box-shadow:0 2px 10px rgba(0,0,0,.05); }

    .erp-empty{ padding:48px 20px; text-align:center; color:var(--erp-muted-2); font-size:14px; font-weight:600; }
    .erp-i-sm{ width:16px; height:16px; }
    .erp-i-md{ width:20px; height:20px; }
    .erp-i-lg{ width:24px; height:24px; }

    .erp-select2-wrap .select2-container{ width:100% !important; }
    .erp-select2-wrap .select2-container .select2-selection--single{
        min-height:44px; border:1px solid var(--erp-line); border-radius:12px; background:#fff;
        display:flex; align-items:center; padding:0 40px 0 14px;
    }
    .erp-select2-wrap .select2-container--default.select2-container--focus .select2-selection--single,
    .erp-select2-wrap .select2-container--default.select2-container--open .select2-selection--single{
        border-color:var(--erp-blue); box-shadow:0 0 0 3px rgba(116,178,212,.18);
    }
    .erp-select2-wrap .select2-container .select2-selection__rendered{
        color:var(--erp-text) !important; line-height:42px !important; padding-left:0 !important; padding-right:0 !important; font-size:14px;
    }
    .erp-select2-wrap .select2-container .select2-selection__placeholder{ color:var(--erp-muted-2) !important; }
    .erp-select2-wrap .select2-container .select2-selection__arrow{ height:42px !important; right:10px !important; }
    .select2-dropdown{ border:1px solid var(--erp-line) !important; border-radius:14px !important; overflow:hidden; box-shadow:var(--erp-shadow-lg); }
    .select2-search--dropdown{ padding:10px !important; background:#f9fbfc; border-bottom:1px solid var(--erp-line); }
    .select2-search--dropdown .select2-search__field{
        border:1px solid var(--erp-line) !important; border-radius:10px !important; padding:10px 12px !important; font-size:14px !important;
    }
    .select2-results__option{ padding:10px 12px !important; font-size:13px; }
    .select2-results__option--highlighted[aria-selected]{ background:var(--erp-blue) !important; color:#fff !important; }
    .erp-s2-option-main{ font-weight:800; color:var(--erp-text); font-size:13px; }
    .erp-s2-option-sub{ margin-top:2px; font-size:11px; color:var(--erp-muted); }
    .select2-results__option--highlighted .erp-s2-option-main,
    .select2-results__option--highlighted .erp-s2-option-sub{ color:#fff !important; }

    .erp-avatar-option{ display:flex; align-items:center; gap:10px; }
    .erp-avatar-option img,
    .erp-avatar-option .erp-avatar-fallback{
        width:30px; height:30px; border-radius:999px; flex:0 0 30px; object-fit:cover;
        border:1px solid var(--erp-line); background:#f0f4f8;
    }
    .erp-avatar-fallback{
        display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:var(--erp-muted);
    }
    .erp-avatar-option-text{ display:flex; flex-direction:column; min-width:0; }
    .erp-avatar-option-name{ font-size:13px; font-weight:700; color:var(--erp-text); line-height:1.2; }
    .erp-avatar-option-sub{ font-size:11px; color:var(--erp-muted); line-height:1.2; }

    .erp-attachment-zone{
        border:2px dashed var(--erp-line);
        border-radius:16px;
        background:#fbfdff;
        padding:16px;
    }

    .erp-attachment-actions{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
    }

    .erp-attachment-list{
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:12px;
        margin-top:14px;
    }

    .erp-attachment-card{
        border:1px solid var(--erp-line);
        border-radius:16px;
        background:#fff;
        overflow:hidden;
        box-shadow:0 4px 14px rgba(52,84,117,.05);
    }

    .erp-attachment-preview{
        height:150px;
        background:#f5f9fc;
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        border-bottom:1px solid var(--erp-line);
    }

    .erp-attachment-preview img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }

    .erp-attachment-doc{
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        gap:8px;
        color:var(--erp-muted);
        padding:16px;
        text-align:center;
    }

    .erp-attachment-body{
        padding:12px;
        display:flex;
        flex-direction:column;
        gap:8px;
    }

    .erp-attachment-name{
        font-size:12px;
        font-weight:800;
        color:var(--erp-text);
        line-height:1.35;
        word-break:break-word;
    }

    .erp-attachment-meta{
        font-size:11px;
        color:var(--erp-muted);
    }

    .erp-attachment-tools{
        display:flex;
        gap:8px;
        flex-wrap:wrap;
    }

    .erp-gallery-modal-image{
        width:100%;
        max-height:72vh;
        object-fit:contain;
        display:block;
        border-radius:18px;
        background:#f5f9fc;
    }

    .erp-gallery-modal-frame{
        width:100%;
        height:72vh;
        border:1px solid var(--erp-line);
        border-radius:18px;
        background:#f5f9fc;
    }

    .erp-hidden-input{ display:none !important; }

    @media (max-width:1200px){
        .erp-grid-5{ grid-template-columns:repeat(3,minmax(0,1fr)); }
        .erp-grid-4{ grid-template-columns:repeat(2,minmax(0,1fr)); }
        .erp-attachment-list{ grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media (max-width:900px){
        .erp-form-grid, .erp-form-grid-3, .erp-kv{ grid-template-columns:1fr; }
        .erp-span-2, .erp-span-3{ grid-column:auto; }
        .erp-row, .erp-row-between{ flex-direction:column; align-items:stretch; }
    }

    @media (max-width:768px){
        .erp-grid-5, .erp-grid-4, .erp-grid-3, .erp-grid-2{ grid-template-columns:1fr; }
        .erp-attachment-list{ grid-template-columns:1fr; }
        .erp-module-body{ padding:16px; }
        .erp-card-body{ padding:16px; }
        .erp-card-head{ padding:14px 16px; }
        .erp-modal-head, .erp-modal-body, .erp-modal-foot{ padding-left:16px; padding-right:16px; }
    }
</style>
@endsection

@section('content')
@php
    $goodsNotifications = collect();

    if (auth()->check() && method_exists(auth()->user(), 'notifications')) {
        $goodsNotifications = auth()->user()
            ->notifications()
            ->latest()
            ->get()
            ->filter(function ($n) {
                return !empty(data_get($n, 'data.goods_receipt_id'));
            })
            ->take(50)
            ->values()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => data_get($n, 'data.title', 'Warenbewegung'),
                    'message' => data_get($n, 'data.message'),
                    'action' => data_get($n, 'data.action'),
                    'goods_receipt_id' => data_get($n, 'data.goods_receipt_id'),
                    'goods_receipt_code' => data_get($n, 'data.goods_receipt_code'),
                    'actor_employee_id' => data_get($n, 'data.actor_employee_id'),
                    'actor_employee_name' => data_get($n, 'data.actor_employee_name'),
                    'status' => data_get($n, 'data.status'),
                    'inspection_status' => data_get($n, 'data.inspection_status'),
                    'happened_at' => data_get($n, 'data.happened_at'),
                    'created_at' => optional($n->created_at)->toDateTimeString(),
                    'read_at' => optional($n->read_at)->toDateTimeString(),
                ];
            });
    }

    $erpDepartments = \App\Models\Department::query()
        ->select('id', 'department_name')
        ->orderBy('department_name')
        ->get()
        ->map(fn ($department) => [
            'id' => $department->id,
            'text' => $department->department_name,
        ])
        ->values();

    $erpEmployees = \App\Models\Employee::query()
        ->select('id', 'title', 'name', 'midname', 'lastname', 'image')
        ->orderBy('name')
        ->orderBy('lastname')
        ->get()
        ->map(function ($employee) {
            $fullName = trim(implode(' ', array_filter([
                $employee->title,
                $employee->name,
                $employee->midname,
                $employee->lastname,
            ])));

            return [
                'id' => $employee->id,
                'text' => $fullName ?: ('Mitarbeiter #' . $employee->id),
                'name' => $employee->name,
                'lastname' => $employee->lastname,
                'image' => $employee->image ? asset('images/employee/' . $employee->image) : null,
            ];
        })
        ->values();
@endphp

<div class="erp-page">
    <div id="erp-app-root" class="erp-app">
        <div class="erp-topbar">
            <nav id="top-nav" class="erp-topnav custom-scrollbar"></nav>
        </div>

        <main class="erp-main">
            <div id="module-header" class="erp-module-header"></div>
            <div id="module-body" class="erp-module-body custom-scrollbar"></div>
        </main>

        <div id="modal-container"></div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
window.__GOODS_NOTIFICATIONS__ = @json($goodsNotifications);
window.__AUTH_EMPLOYEE_ID__ = @json(is_numeric(optional(auth()->user())->name) ? (int) auth()->user()->name : null);
window.__ERP_DEPARTMENTS__ = @json($erpDepartments);
window.__ERP_EMPLOYEES__ = @json($erpEmployees);

const endpoints = {
    index: @json(route('admin.goods-receipts.index')),
    data: @json(route('admin.goods-receipts.data')),
    store: @json(route('admin.goods-receipts.store')),
    relationOptions: @json(route('admin.goods-receipts.relation-options')),
    showBase: @json(url('/admin/goods-receipts')),
};

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

let state = {
    deliveries: [],
    notifications: Array.isArray(window.__GOODS_NOTIFICATIONS__) ? window.__GOODS_NOTIFICATIONS__ : [],
    activeModule: 'dashboard',
    activeView: 'list',
    activeWarenausgangTab: 'verfuegbar',
    searchQuery: '',
    filters: { status: '', inspection_status: '', destination: '' },
    sortConfig: { key: 'received_at', direction: 'desc' },
    currentPage: 1,
    lastPage: 1,
    itemsPerPage: 10,
    totalItems: 0,
    strategy: 'FIFO',
    expandedGroups: {},
    isModalOpen: false,
    issueModalOpen: false,
    galleryModalOpen: false,
    galleryItem: null,
    selectedDelivery: null,
    selectedForIssue: null,
    notifSearch: '',
    draggingId: null,
};

let searchTimer = null;

function icon(name, cls = 'erp-i-sm') {
    return `<i data-lucide="${name}" class="${cls}"></i>`;
}

function formatDate(value) {
    if (!value) return '-';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function formatDateTime(value) {
    if (!value) return '-';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleDateString('de-DE', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function fullName(employee) {
    if (!employee) return '-';
    return [employee.name, employee.lastname].filter(Boolean).join(' ') || employee.name || '-';
}

function statusLabel(status) {
    return {
        pending: 'Erfasst',
        processing: 'In Prüfung',
        completed: 'Lagernd / Erledigt',
        issued: 'Ausgegeben'
    }[status] || status || '-';
}

function getStatusBadgeHtml(status) {
    const cls = {
        pending: 'erp-status erp-status-pending',
        processing: 'erp-status erp-status-processing',
        completed: 'erp-status erp-status-completed',
        issued: 'erp-status erp-status-issued',
    }[status] || 'erp-status erp-status-neutral';

    return `<span class="${cls}">${statusLabel(status)}</span>`;
}

function getInspectionBadgeHtml(status) {
    if (status === 'ok') return `<span class="erp-status erp-status-completed">${icon('shield-check')}OK</span>`;
    if (status === 'issue') return `<span class="erp-status erp-status-issue">${icon('alert-triangle')}Mangel</span>`;
    return `<span class="erp-status erp-status-neutral">${icon('help-circle')}Ungeprüft</span>`;
}

function employeeById(id) {
    return (window.__ERP_EMPLOYEES__ || []).find(emp => String(emp.id) === String(id)) || null;
}

function renderDepartmentOptions(selectedId) {
    return (window.__ERP_DEPARTMENTS__ || []).map(dep => `
        <option value="${dep.id}" ${String(selectedId || '') === String(dep.id) ? 'selected' : ''}>${escapeHtml(dep.text)}</option>
    `).join('');
}

function renderEmployeeOptions(selectedId) {
    return (window.__ERP_EMPLOYEES__ || []).map(emp => `
        <option value="${emp.id}" ${String(selectedId || '') === String(emp.id) ? 'selected' : ''}>${escapeHtml(emp.text)}</option>
    `).join('');
}

function formatEmployeeOption(item) {
    if (!item || !item.id) return item?.text || 'Bitte wählen...';

    const employee = item.employeeData || employeeById(item.id) || item;
    const name = employee.text || [employee.name, employee.lastname].filter(Boolean).join(' ') || ('Mitarbeiter #' + employee.id);
    const sub = 'ID: ' + employee.id;

    const avatar = employee.image
        ? `<img src="${employee.image}" alt="${escapeHtml(name)}">`
        : `<span class="erp-avatar-fallback">${escapeHtml(String(name).substring(0,1).toUpperCase())}</span>`;

    return $(`
        <div class="erp-avatar-option">
            ${avatar}
            <div class="erp-avatar-option-text">
                <span class="erp-avatar-option-name">${escapeHtml(name)}</span>
                <span class="erp-avatar-option-sub">${escapeHtml(sub)}</span>
            </div>
        </div>
    `);
}

function formatRelationOption(item) {
    if (!item || !item.id) return item?.text || 'Bitte wählen...';

    const main = item.text || '';
    const sub = [
        item.customer_name ? ('Kunde: ' + item.customer_name) : '',
        item.object_name ? ('Objekt: ' + item.object_name) : '',
        item.product_name ? ('Produkt: ' + item.product_name) : '',
    ].filter(Boolean).join(' | ');

    return $(`
        <div>
            <div class="erp-s2-option-main">${escapeHtml(main)}</div>
            <div class="erp-s2-option-sub">${escapeHtml(sub)}</div>
        </div>
    `);
}

function notificationFiltered() {
    const q = state.notifSearch.trim().toLowerCase();
    if (!q) return state.notifications;

    return state.notifications.filter(n =>
        [n.title, n.message, n.action, n.goods_receipt_code, n.actor_employee_name]
            .some(v => String(v || '').toLowerCase().includes(q))
    );
}

function normalizeAttachment(item) {
    if (!item) return null;
    return {
        id: item.id || null,
        label: item.label || item.original_name || item.name || 'Datei',
        original_name: item.original_name || item.name || 'Datei',
        file_url: item.file_url || item.url || null,
        mime_type: item.mime_type || '',
        is_image: !!item.is_image || String(item.mime_type || '').startsWith('image/'),
        scope: item.scope || 'inbound',
        isQueued: !!item.isQueued,
        queueIndex: typeof item.queueIndex === 'number' ? item.queueIndex : null,
        file: item.file || null,
        file_size: item.file_size || (item.file ? item.file.size : null),
    };
}

function humanFileSize(bytes) {
    const n = Number(bytes || 0);
    if (!n) return '-';
    if (n < 1024) return n + ' B';
    if (n < 1024 * 1024) return (n / 1024).toFixed(1) + ' KB';
    return (n / (1024 * 1024)).toFixed(1) + ' MB';
}

function setModule(module) { state.activeModule = module; renderApp(); }
function setView(view) { state.activeView = view; renderModuleBody(); }
function setWarenausgangTab(tab) { state.activeWarenausgangTab = tab; renderApp(); }
function setStrategy(strategy) { state.strategy = strategy; renderModuleBody(); }
function setNotifSearch(value) { state.notifSearch = value; renderModuleBody(); }
function toggleGroup(desc) { state.expandedGroups[desc] = !state.expandedGroups[desc]; renderModuleBody(); }

function setPage(page) {
    if (page < 1 || page > state.lastPage) return;
    state.currentPage = page;
    loadDeliveries();
}

function setSort(key) {
    if (state.sortConfig.key === key) {
        state.sortConfig.direction = state.sortConfig.direction === 'asc' ? 'desc' : 'asc';
    } else {
        state.sortConfig.key = key;
        state.sortConfig.direction = 'asc';
    }
    state.currentPage = 1;
    loadDeliveries();
}

function setSearch(value) {
    state.searchQuery = value;
    state.currentPage = 1;
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadDeliveries, 300);
}

function setFilter(key, value) {
    state.filters[key] = value;
    state.currentPage = 1;
    loadDeliveries();
}

function renderApp() {
    renderTopNav();
    renderModuleHeader();
    renderModuleBody();
    renderModals();
    lucide.createIcons();
}

function renderTopNav() {
    const pendingCount = state.deliveries.filter(d => ['pending','processing'].includes(d.status)).length;
    const stockCount = state.deliveries.filter(d => d.status === 'completed').length;
    const notificationCount = state.notifications.filter(n => !n.read_at).length || state.notifications.length;

    const tabs = [
        { id: 'dashboard', icon: 'home', label: 'Startseite' },
        { id: 'wareneingang', icon: 'package', label: 'Wareneingang', badge: pendingCount },
        { id: 'bestand', icon: 'layers', label: 'Warenbestand', badge: stockCount },
        { id: 'warenausgang', icon: 'truck', label: 'Warenausgang' },
        { id: 'notifications', icon: 'bell-ring', label: 'Benachrichtigungen', badge: notificationCount },
    ];

    document.getElementById('top-nav').innerHTML = tabs.map(t => {
        const active = state.activeModule === t.id;
        const badge = t.badge > 0 ? `<span class="erp-badge ${active ? 'erp-badge-blue' : 'erp-badge-soft'}">${t.badge}</span>` : '';

        return `
            <button onclick="setModule('${t.id}')" class="erp-tab ${active ? 'is-active' : ''}">
                <span class="erp-tab-icon">${icon(t.icon)}</span>
                <span>${t.label}</span>
                ${badge}
            </button>
        `;
    }).join('');
}

function renderModuleHeader() {
    const header = document.getElementById('module-header');

    if (state.activeModule === 'wareneingang') {
        header.className = 'erp-module-header';
        header.innerHTML = `
            <div class="erp-shell">
                <div class="erp-card-head">
                    <div class="erp-row-between erp-wrap">
                        <div class="erp-row erp-wrap erp-grow">
                            <div class="erp-input-wrap" style="min-width:320px;flex:1 1 320px;">
                                <span class="erp-input-icon">${icon('search')}</span>
                                <input type="text" class="erp-input" placeholder="Suchen nach Kennung, Beschreibung, Kunde..." value="${escapeHtml(state.searchQuery)}" oninput="setSearch(this.value)">
                            </div>

                            <div style="min-width:180px;flex:0 0 180px;">
                                <select onchange="setFilter('status', this.value)" class="erp-select">
                                    <option value="">Alle Status</option>
                                    <option value="pending" ${state.filters.status === 'pending' ? 'selected' : ''}>Erfasst</option>
                                    <option value="processing" ${state.filters.status === 'processing' ? 'selected' : ''}>In Prüfung</option>
                                    <option value="completed" ${state.filters.status === 'completed' ? 'selected' : ''}>Lagernd</option>
                                    <option value="issued" ${state.filters.status === 'issued' ? 'selected' : ''}>Ausgegeben</option>
                                </select>
                            </div>

                            <div style="min-width:180px;flex:0 0 180px;">
                                <select onchange="setFilter('inspection_status', this.value)" class="erp-select">
                                    <option value="">Alle Prüfungen</option>
                                    <option value="pending" ${state.filters.inspection_status === 'pending' ? 'selected' : ''}>Ungeprüft</option>
                                    <option value="ok" ${state.filters.inspection_status === 'ok' ? 'selected' : ''}>OK</option>
                                    <option value="issue" ${state.filters.inspection_status === 'issue' ? 'selected' : ''}>Mangel</option>
                                </select>
                            </div>
                        </div>

                        <div class="erp-row erp-row-middle erp-wrap">
                            <div class="erp-toggle">
                                <button type="button" onclick="setView('list')" class="erp-toggle-btn ${state.activeView === 'list' ? 'is-active' : ''}">${icon('layout-list')}</button>
                                <button type="button" onclick="setView('kanban')" class="erp-toggle-btn ${state.activeView === 'kanban' ? 'is-active' : ''}">${icon('kanban')}</button>
                                <button type="button" onclick="setView('analytics')" class="erp-toggle-btn ${state.activeView === 'analytics' ? 'is-active' : ''}">${icon('bar-chart-2')}</button>
                            </div>

                            <button type="button" onclick="openNewDeliveryModal()" class="erp-btn erp-btn-primary">
                                ${icon('plus')} Ware erfassen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    if (state.activeModule === 'bestand') {
        header.className = 'erp-module-header';
        header.innerHTML = `
            <div class="erp-shell">
                <div class="erp-card-head">
                    <div class="erp-row-between erp-wrap">
                        <div>
                            <div class="erp-title-lg">Warenbestand & Chargen</div>
                            <div class="erp-text-sm erp-text-muted">Gruppierte Übersicht nach Artikel mit Entnahme-Empfehlung.</div>
                        </div>

                        <div class="erp-row erp-row-middle">
                            <span class="erp-text-sm erp-text-soft" style="font-weight:700;">Entnahme-Strategie:</span>
                            <div class="erp-toggle">
                                <button type="button" onclick="setStrategy('FIFO')" class="erp-toggle-btn ${state.strategy === 'FIFO' ? 'is-active' : ''}">FIFO</button>
                                <button type="button" onclick="setStrategy('LIFO')" class="erp-toggle-btn ${state.strategy === 'LIFO' ? 'is-active' : ''}">LIFO</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    if (state.activeModule === 'warenausgang') {
        const avail = state.deliveries.filter(d => d.status === 'completed').length;
        const hist = state.deliveries.filter(d => d.status === 'issued').length;

        header.className = 'erp-module-header';
        header.innerHTML = `
            <div class="erp-shell">
                <div class="erp-card-head">
                    <div class="erp-title-lg">Warenausgang</div>
                    <div class="erp-subtabs">
                        <button type="button" onclick="setWarenausgangTab('verfuegbar')" class="erp-subtab ${state.activeWarenausgangTab === 'verfuegbar' ? 'is-active' : ''}">Bereit zur Ausgabe (${avail})</button>
                        <button type="button" onclick="setWarenausgangTab('historie')" class="erp-subtab ${state.activeWarenausgangTab === 'historie' ? 'is-active' : ''}">Ausgabe-Historie (${hist})</button>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    if (state.activeModule === 'notifications') {
        header.className = 'erp-module-header';
        header.innerHTML = `
            <div class="erp-shell">
                <div class="erp-card-head">
                    <div class="erp-row-between erp-wrap">
                        <div>
                            <div class="erp-title-lg">Benachrichtigungen</div>
                            <div class="erp-text-sm erp-text-muted">Aktivitäten aus dem Laravel Notifications-System.</div>
                        </div>

                        <div class="erp-row erp-row-middle erp-wrap">
                            <div class="erp-input-wrap" style="min-width:320px;">
                                <span class="erp-input-icon">${icon('search')}</span>
                                <input type="text" class="erp-input" placeholder="Nach Titel, Aktion, Kennung suchen..." value="${escapeHtml(state.notifSearch)}" oninput="setNotifSearch(this.value)">
                            </div>

                            <button type="button" onclick="loadDeliveries(true)" class="erp-btn erp-btn-light">
                                ${icon('refresh-cw')} Aktualisieren
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    header.className = 'erp-module-header erp-hide';
    header.innerHTML = '';
}

function renderModuleBody() {
    const body = document.getElementById('module-body');

    if (state.activeModule === 'dashboard') {
        body.innerHTML = `<div class="erp-shell">${getDashboardHtml()}</div>`;
    } else if (state.activeModule === 'wareneingang') {
        body.innerHTML = `<div class="erp-shell">${getWareneingangViewHtml()}</div>`;
    } else if (state.activeModule === 'bestand') {
        body.innerHTML = `<div class="erp-shell-md">${getWarenbestandHtml()}</div>`;
    } else if (state.activeModule === 'warenausgang') {
        body.innerHTML = `<div class="erp-shell-md">${getWarenausgangHtml()}</div>`;
    } else if (state.activeModule === 'notifications') {
        body.innerHTML = `<div class="erp-shell-md">${getNotificationsHtml()}</div>`;
    }

    lucide.createIcons();
}

function getDashboardHtml() {
    const actionItems = state.deliveries.filter(d => ['pending', 'processing'].includes(d.status));
    const issues = state.deliveries.filter(d => d.inspection_status === 'issue');
    const readyToIssue = state.deliveries.filter(d => d.status === 'completed').length;
    const issuedToday = state.deliveries.filter(d =>
        d.status === 'issued' && d.outbound_at && d.outbound_at.slice(0, 10) === new Date().toISOString().slice(0, 10)
    ).length;

    const rows = actionItems.slice(0, 8).map(d => `
        <tr>
            <td>
                <div style="font-weight:800;">${escapeHtml(d.code)}</div>
                <div class="erp-meta">${formatDateTime(d.received_at)}</div>
            </td>
            <td>
                <div style="font-weight:700;">${escapeHtml(d.description)}</div>
                ${d.issue_description ? `<div><span class="erp-status erp-status-issue">Mangel gemeldet</span></div>` : ''}
            </td>
            <td>${escapeHtml(d.department?.department_name || '-')}</td>
            <td>
                <select onchange="quickStatus(${d.id}, this.value)" class="erp-select">
                    <option value="pending" ${d.status === 'pending' ? 'selected' : ''}>Erfasst</option>
                    <option value="processing" ${d.status === 'processing' ? 'selected' : ''}>In Prüfung</option>
                    <option value="completed" ${d.status === 'completed' ? 'selected' : ''}>Lagernd</option>
                </select>
            </td>
        </tr>
    `).join('');

    return `
        <div style="display:flex;flex-direction:column;gap:24px;" class="erp-fade-in">
            <div class="erp-row-between erp-wrap" style="padding-bottom:24px;border-bottom:1px solid var(--erp-line);">
                <div>
                    <div class="erp-title-xl">LogisSync ERP</div>
                    <div class="erp-text-sm erp-text-muted">Live-Daten aus Wareneingang, Warenbestand, Warenausgang und Benachrichtigungen.</div>
                </div>
                <div class="erp-code">${new Date().toLocaleDateString('de-DE', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}</div>
            </div>

            <div class="erp-grid erp-grid-5">
                <div class="erp-card erp-stat" onclick="setModule('wareneingang')">
                    <div class="erp-card-body">
                        <div class="erp-stat-top"><span class="erp-stat-label">Offene Aufgaben</span><span class="erp-text-blue">${icon('clock','erp-i-lg')}</span></div>
                        <div class="erp-stat-value">${actionItems.length}</div>
                    </div>
                </div>

                <div class="erp-card erp-card-red">
                    <div class="erp-card-body">
                        <div class="erp-stat-top"><span class="erp-stat-label" style="color:#b42318;">Reklamationen</span><span class="erp-text-red">${icon('alert-triangle','erp-i-lg')}</span></div>
                        <div class="erp-stat-value" style="color:#b42318;">${issues.length}</div>
                    </div>
                </div>

                <div class="erp-card erp-card-green erp-stat" onclick="setModule('bestand')">
                    <div class="erp-card-body">
                        <div class="erp-stat-top"><span class="erp-stat-label" style="color:#4f6e0b;">Im Bestand</span><span class="erp-text-green">${icon('layers','erp-i-lg')}</span></div>
                        <div class="erp-stat-value" style="color:#4f6e0b;">${readyToIssue}</div>
                    </div>
                </div>

                <div class="erp-card erp-stat" onclick="setModule('warenausgang')">
                    <div class="erp-card-body">
                        <div class="erp-stat-top"><span class="erp-stat-label">Heute Ausgegeben</span><span class="erp-text-blue">${icon('truck','erp-i-lg')}</span></div>
                        <div class="erp-stat-value">${issuedToday}</div>
                    </div>
                </div>

                <div class="erp-card erp-stat" onclick="setModule('notifications')">
                    <div class="erp-card-body">
                        <div class="erp-stat-top"><span class="erp-stat-label">Benachrichtigungen</span><span class="erp-text-blue">${icon('bell-ring','erp-i-lg')}</span></div>
                        <div class="erp-stat-value">${state.notifications.length}</div>
                    </div>
                </div>
            </div>

            <div>
                <div class="erp-kicker" style="margin-bottom:12px;">Handlungsbedarf</div>
                <div class="erp-table-wrap">
                    <table class="erp-table">
                        <thead>
                            <tr>
                                <th>Kennung / Datum</th>
                                <th>Ware / Beschreibung</th>
                                <th>Abteilung</th>
                                <th>Schnell-Bearbeitung</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows || `<tr><td colspan="4" class="erp-empty">Keine offenen Aufgaben.</td></tr>`}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
}

function getAttachmentCount(item, scope) {
    const list = Array.isArray(item?.attachments) ? item.attachments : [];
    return list.filter(x => String(x.scope || 'inbound') === String(scope)).length;
}

function getWareneingangViewHtml() {
    if (state.activeView === 'kanban') return getWareneingangKanbanHtml();
    if (state.activeView === 'analytics') return getWareneingangAnalyticsHtml();

    const rows = state.deliveries.map(d => `
        <tr class="erp-table-click" onclick="openEditDeliveryModal(${d.id})">
            <td style="font-weight:800;">${escapeHtml(d.code)}</td>
            <td>${formatDateTime(d.received_at)}</td>
            <td>
                <div style="font-weight:700;">${escapeHtml(d.description)}</div>
                <div class="erp-meta">Kunde: ${escapeHtml((d.customer?.firma || '') || [d.customer?.name, d.customer?.lastname].filter(Boolean).join(' ') || '-')}</div>
            </td>
            <td>${getStatusBadgeHtml(d.status)}</td>
            <td>${getInspectionBadgeHtml(d.inspection_status)}</td>
            <td>${escapeHtml(fullName(d.accepted_by_employee))}</td>
            <td>
                <div class="erp-row erp-row-middle" style="gap:6px;flex-wrap:wrap;">
                    <span class="erp-badge erp-badge-soft">IN ${getAttachmentCount(d, 'inbound')}</span>
                    <span class="erp-badge erp-badge-blue">OUT ${getAttachmentCount(d, 'outbound')}</span>
                </div>
            </td>
            <td class="erp-table-right">
                <div class="erp-row erp-row-middle erp-wrap" style="justify-content:flex-end;gap:8px;">
                    <button type="button" onclick="event.stopPropagation(); openEditDeliveryModal(${d.id})" class="erp-btn erp-btn-light">Bearbeiten</button>
                    ${d.can_be_issued ? `<button type="button" onclick="event.stopPropagation(); openIssueModal(${d.id})" class="erp-btn erp-btn-secondary">Ausbuchen</button>` : ''}
                </div>
            </td>
        </tr>
    `).join('');

    return `
        <div style="display:flex;flex-direction:column;gap:16px;" class="erp-fade-in">
            <div class="erp-row-between erp-wrap">
                <div class="erp-title-md">Alle Lieferungen</div>
                <span class="erp-code">${state.totalItems} Einträge gefunden</span>
            </div>

            <div class="erp-table-wrap">
                <table class="erp-table">
                    <thead>
                        <tr>
                            <th class="erp-table-sort" onclick="setSort('code')">Kennung</th>
                            <th class="erp-table-sort" onclick="setSort('received_at')">Datum</th>
                            <th class="erp-table-sort" onclick="setSort('description')">Beschreibung</th>
                            <th class="erp-table-sort" onclick="setSort('status')">Status</th>
                            <th>Qualität</th>
                            <th>Angenommen</th>
                            <th>Belege</th>
                            <th class="erp-table-right">Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows || `<tr><td colspan="8" class="erp-empty">Keine Daten gefunden.</td></tr>`}
                    </tbody>
                </table>
                ${getPaginationHtml()}
            </div>
        </div>
    `;
}

function getPaginationHtml() {
    if (state.lastPage <= 1) return '';

    let pages = '';
    for (let i = 1; i <= state.lastPage; i++) {
        pages += `<button type="button" onclick="setPage(${i})" class="erp-page-btn ${state.currentPage === i ? 'is-active' : ''}">${i}</button>`;
    }

    return `
        <div class="erp-pagination">
            <span class="erp-text-sm erp-text-muted" style="font-weight:700;">Seite ${state.currentPage} von ${state.lastPage}</span>
            <div class="erp-pagination-pages">
                <button type="button" onclick="setPage(${Math.max(1, state.currentPage - 1)})" class="erp-btn erp-btn-light" ${state.currentPage === 1 ? 'disabled' : ''}>Zurück</button>
                ${pages}
                <button type="button" onclick="setPage(${Math.min(state.lastPage, state.currentPage + 1)})" class="erp-btn erp-btn-light" ${state.currentPage === state.lastPage ? 'disabled' : ''}>Weiter</button>
            </div>
        </div>
    `;
}

function getWareneingangKanbanHtml() {
    const cols = [
        { id: 'pending', title: 'Erfasst', badge: 'erp-badge-soft' },
        { id: 'processing', title: 'In Prüfung', badge: 'erp-badge-soft' },
        { id: 'completed', title: 'Lagernd', badge: 'erp-badge-green' },
        { id: 'issued', title: 'Ausgegeben', badge: 'erp-badge-blue' },
    ];

    return `
        <div class="erp-kanban custom-scrollbar erp-fade-in">
            ${cols.map(col => {
                const items = state.deliveries.filter(d => d.status === col.id);

                return `
                    <div class="erp-kanban-col">
                        <div class="erp-kanban-head">
                            <span>${col.title}</span>
                            <span class="erp-badge ${col.badge}">${items.length}</span>
                        </div>

                        <div class="erp-kanban-body"
                             data-kanban-status="${col.id}"
                             ondragover="kanbanDragOver(event)"
                             ondragleave="kanbanDragLeave(event)"
                             ondrop="kanbanDrop(event, '${col.id}')">
                            ${items.map(d => `
                                <div class="erp-kanban-card"
                                     draggable="true"
                                     data-drag-id="${d.id}"
                                     ondragstart="kanbanDragStart(event, ${d.id})"
                                     ondragend="kanbanDragEnd(event)"
                                     ondblclick="openEditDeliveryModal(${d.id})">
                                    <div class="erp-row-between" style="gap:8px;">
                                        <span class="erp-code">${escapeHtml(d.code)}</span>
                                        ${getInspectionBadgeHtml(d.inspection_status)}
                                    </div>
                                    <div class="erp-title-sm" style="margin-top:10px;">${escapeHtml(d.description)}</div>
                                    <div class="erp-meta" style="margin-top:10px;">${escapeHtml(d.department?.department_name || '-')}</div>
                                    <div class="erp-row erp-row-middle" style="gap:6px;margin-top:10px;flex-wrap:wrap;">
                                        <span class="erp-badge erp-badge-soft">IN ${getAttachmentCount(d, 'inbound')}</span>
                                        <span class="erp-badge erp-badge-blue">OUT ${getAttachmentCount(d, 'outbound')}</span>
                                    </div>
                                </div>
                            `).join('') || `<div class="erp-empty">Keine Einträge</div>`}
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
}

function getWareneingangAnalyticsHtml() {
    const t = state.deliveries.length;
    const p = state.deliveries.filter(d => d.status === 'pending').length;
    const pr = state.deliveries.filter(d => d.status === 'processing').length;
    const c = state.deliveries.filter(d => d.status === 'completed').length;
    const i = state.deliveries.filter(d => d.status === 'issued').length;

    const bar = (lbl, count, tot, color) => {
        const pct = tot === 0 ? 0 : Math.round((count / tot) * 100);
        return `
            <div>
                <div class="erp-row-between" style="margin-bottom:8px;">
                    <span class="erp-text-sm" style="font-weight:700;color:var(--erp-text);">${lbl}</span>
                    <span class="erp-text-sm erp-text-muted">${count} (${pct}%)</span>
                </div>
                <div style="height:10px;border-radius:999px;background:#f0f4f8;border:1px solid var(--erp-line);overflow:hidden;">
                    <div style="height:100%;width:${pct}%;background:${color};border-radius:999px;"></div>
                </div>
            </div>
        `;
    };

    return `
        <div style="display:flex;flex-direction:column;gap:24px;" class="erp-fade-in">
            <div class="erp-title-lg">Übersicht & Metriken</div>

            <div class="erp-grid erp-grid-4">
                <div class="erp-card"><div class="erp-card-body"><div class="erp-stat-label">Gesamt</div><div class="erp-stat-value" style="font-size:34px;">${t}</div></div></div>
                <div class="erp-card"><div class="erp-card-body"><div class="erp-stat-label">Offen</div><div class="erp-stat-value" style="font-size:34px;">${p}</div></div></div>
                <div class="erp-card"><div class="erp-card-body"><div class="erp-stat-label">In Prüfung</div><div class="erp-stat-value" style="font-size:34px;color:#6a8f10;">${pr}</div></div></div>
                <div class="erp-card"><div class="erp-card-body"><div class="erp-stat-label">Ausgegeben</div><div class="erp-stat-value" style="font-size:34px;color:#3b718f;">${i}</div></div></div>
            </div>

            <div class="erp-card">
                <div class="erp-card-body">
                    <div class="erp-kicker" style="margin-bottom:16px;">Status Verteilung</div>
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        ${bar('Erfasst', p, t, '#c0d8ea')}
                        ${bar('In Prüfung', pr, t, '#cfe09b')}
                        ${bar('Lagernd', c, t, '#93c21c')}
                        ${bar('Ausgegeben', i, t, '#74b2d4')}
                    </div>
                </div>
            </div>
        </div>
    `;
}

function getWarenbestandHtml() {
    const stockItems = state.deliveries.filter(d => d.status === 'completed');
    const groups = {};

    stockItems.forEach(item => {
        const key = item.article_group?.article_group || item.description;
        if (!groups[key]) groups[key] = [];
        groups[key].push(item);
    });

    const grouped = Object.entries(groups).map(([description, items]) => {
        items.sort((a, b) => {
            const dateA = new Date(a.received_at).getTime();
            const dateB = new Date(b.received_at).getTime();
            return state.strategy === 'FIFO' ? dateA - dateB : dateB - dateA;
        });

        return {
            description,
            count: items.length,
            items,
            department: items[0]?.department?.department_name || '-',
        };
    }).sort((a, b) => a.description.localeCompare(b.description));

    if (!grouped.length) return `<div class="erp-empty">Keine Artikel im Bestand vorhanden.</div>`;

    return grouped.map(group => {
        const expanded = !!state.expandedGroups[group.description];

        return `
            <div class="erp-card" style="margin-top:16px;">
                <div class="erp-stock-head" onclick="toggleGroup('${escapeHtml(group.description)}')">
                    <div class="erp-row erp-row-middle">
                        <div class="erp-card-soft" style="padding:12px;border-radius:12px;color:var(--erp-blue);display:inline-flex;align-items:center;justify-content:center;">
                            ${icon('layers', 'erp-i-lg')}
                        </div>
                        <div>
                            <div class="erp-title-sm">${escapeHtml(group.description)}</div>
                            <div class="erp-meta">Abteilung: ${escapeHtml(group.department)}</div>
                        </div>
                    </div>

                    <div class="erp-row erp-row-middle" style="gap:18px;">
                        <div style="text-align:right;">
                            <div style="font-size:28px;line-height:1;font-weight:800;color:var(--erp-blue);">${group.count}</div>
                            <div class="erp-kicker" style="margin-top:4px;">Einheiten</div>
                        </div>
                        <div class="erp-text-soft">${expanded ? icon('chevron-up','erp-i-md') : icon('chevron-down','erp-i-md')}</div>
                    </div>
                </div>

                ${expanded ? `
                    <div class="erp-stock-details">
                        <div class="erp-kicker" style="margin-bottom:12px;">Einzelne Kennungen (Sortiert nach ${state.strategy})</div>

                        ${group.items.map((item, index) => `
                            <div class="erp-stock-item ${index === 0 ? 'is-highlight' : ''}">
                                <div class="erp-row erp-row-middle erp-wrap">
                                    ${index === 0 ? `<span class="erp-badge erp-badge-green">Nächste Entnahme</span>` : ''}
                                    <span class="erp-code">${escapeHtml(item.code)}</span>
                                </div>

                                <div class="erp-row erp-row-middle erp-wrap" style="gap:18px;">
                                    <span class="erp-meta">Eingang: <strong style="color:var(--erp-text);">${formatDate(item.received_at)}</strong></span>
                                    <span>${getInspectionBadgeHtml(item.inspection_status)}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

function getWarenausgangHtml() {
    const available = state.deliveries.filter(d => d.status === 'completed');
    const history = state.deliveries.filter(d => d.status === 'issued').sort((a, b) => new Date(b.outbound_at || 0) - new Date(a.outbound_at || 0));

    if (state.activeWarenausgangTab === 'verfuegbar') {
        return `
            <div style="display:flex;flex-direction:column;gap:16px;" class="erp-fade-in">
                <div class="erp-text-sm erp-text-muted">Wählen Sie einen Posten aus, um ihn zuzuordnen und auszubuchen.</div>

                <div class="erp-table-wrap">
                    <table class="erp-table">
                        <thead>
                            <tr>
                                <th>Ware / Beschreibung</th>
                                <th>Eingangsdatum</th>
                                <th>Abteilung / Ziel</th>
                                <th>Ausgangsbelege</th>
                                <th class="erp-table-right">Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${available.map(d => `
                                <tr>
                                    <td>
                                        <div style="font-weight:700;">${escapeHtml(d.description)}</div>
                                        <div class="erp-meta">Kennung: ${escapeHtml(d.code)}</div>
                                    </td>
                                    <td>${formatDate(d.received_at)}</td>
                                    <td>
                                        ${escapeHtml(d.department?.department_name || '-')}
                                        ${d.destination === 'kommission' ? `<span style="margin-left:8px;" class="erp-status erp-status-completed">Kommission</span>` : ''}
                                    </td>
                                    <td><span class="erp-badge erp-badge-blue">${getAttachmentCount(d, 'outbound')}</span></td>
                                    <td class="erp-table-right">
                                        <button type="button" onclick="openIssueModal(${d.id})" class="erp-btn erp-btn-secondary">${icon('log-out')} Ausbuchen</button>
                                    </td>
                                </tr>
                            `).join('') || `<tr><td colspan="5" class="erp-empty">Aktuell keine freigegebenen Waren im Bestand.</td></tr>`}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    return `
        <div class="erp-fade-in">
            <div class="erp-table-wrap">
                <table class="erp-table">
                    <thead>
                        <tr>
                            <th>Ausgabe-Datum</th>
                            <th>Ware / Kennung</th>
                            <th>Empfänger</th>
                            <th>Projekt</th>
                            <th>Ausgegeben von</th>
                            <th>Belege</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${history.map(d => `
                            <tr>
                                <td>
                                    <div style="font-weight:700;">${formatDate(d.outbound_at)}</div>
                                    <div class="erp-meta">${formatDateTime(d.outbound_at)}</div>
                                </td>
                                <td>
                                    <div style="font-weight:700;">${escapeHtml(d.description)}</div>
                                    <div class="erp-meta">${escapeHtml(d.code)}</div>
                                </td>
                                <td style="font-weight:700;">${escapeHtml(d.outbound_recipient || '-')}</td>
                                <td>${escapeHtml(d.outbound_project || '-')}</td>
                                <td>${escapeHtml(fullName(d.issued_by_employee))}</td>
                                <td><span class="erp-badge erp-badge-blue">${getAttachmentCount(d, 'outbound')}</span></td>
                            </tr>
                        `).join('') || `<tr><td colspan="6" class="erp-empty">Noch keine Warenausgänge erfasst.</td></tr>`}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function getNotificationsHtml() {
    const items = notificationFiltered();

    if (!items.length) return `<div class="erp-empty">Keine Benachrichtigungen gefunden.</div>`;

    return `
        <div style="display:flex;flex-direction:column;gap:16px;" class="erp-fade-in">
            <div class="erp-row-between erp-wrap">
                <div class="erp-text-sm erp-text-muted">${items.length} Benachrichtigungen</div>
                <div class="erp-text-xs erp-text-soft">Datenquelle: Laravel notifications Tabelle</div>
            </div>

            ${items.map(n => `
                <div class="erp-notification">
                    <div class="erp-row-between erp-wrap">
                        <div class="erp-grow">
                            <div class="erp-row erp-row-middle erp-wrap" style="gap:8px;">
                                <span class="erp-status erp-status-neutral">${escapeHtml(n.action || 'activity')}</span>
                                ${n.goods_receipt_code ? `<span class="erp-code">${escapeHtml(n.goods_receipt_code)}</span>` : ''}
                                ${n.read_at ? `<span class="erp-badge erp-badge-soft">Gelesen</span>` : `<span class="erp-badge erp-badge-blue">Neu</span>`}
                            </div>

                            <div class="erp-title-sm" style="margin-top:12px;">${escapeHtml(n.title || 'Benachrichtigung')}</div>
                            <div class="erp-text-sm erp-text-muted" style="margin-top:6px;">${escapeHtml(n.message || '-')}</div>

                            <div class="erp-kv">
                                <div class="erp-kv-item">
                                    <div class="erp-kv-label">Ausgeführt von</div>
                                    <div class="erp-kv-value">${escapeHtml(n.actor_employee_name || ('Mitarbeiter #' + (n.actor_employee_id || '-')))}</div>
                                </div>
                                <div class="erp-kv-item">
                                    <div class="erp-kv-label">Status</div>
                                    <div class="erp-kv-value">${escapeHtml(statusLabel(n.status))}</div>
                                </div>
                                <div class="erp-kv-item">
                                    <div class="erp-kv-label">Zeitpunkt</div>
                                    <div class="erp-kv-value">${escapeHtml(formatDateTime(n.happened_at || n.created_at))}</div>
                                </div>
                            </div>
                        </div>

                        ${n.goods_receipt_id ? `
                            <button type="button" onclick="openEditDeliveryModal(${n.goods_receipt_id})" class="erp-btn erp-btn-light">
                                ${icon('external-link')} Öffnen
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

function openNewDeliveryModal() {
    state.selectedDelivery = {
        isNew: true,
        description: '',
        note: '',
        status: 'pending',
        inspection_status: 'pending',
        issue_description: '',
        destination: '',
        commission_details: '',
        customer_id: '',
        object_id: '',
        lead_product_list_id: '',
        product_id: '',
        article_group_id: '',
        customer_display: '',
        object_display: '',
        product_display: '',
        relation_picker_text: '',
        department_id: '',
        accepted_by_employee_id: '',
        orderer_employee_id: '',
        qty: '',
        unit: '',
        purchase_price: '',
        outbound_customer_id: '',
        outbound_object_id: '',
        attachments: [],
        inbound_attachments: [],
        outbound_attachments: [],
        inbound_queue: [],
    };

    state.isModalOpen = true;
    renderModals();
}

async function openEditDeliveryModal(id) {
    try {
        const res = await fetch(`${endpoints.showBase}/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        const result = await res.json();

        if (!res.ok || !result.success) {
            alert(result.message || 'Datensatz konnte nicht geladen werden.');
            return;
        }

        const d = result.data;

        state.selectedDelivery = {
            id: d.id,
            code: d.code,
            isNew: false,
            description: d.description || '',
            note: d.note || '',
            status: d.status || 'pending',
            inspection_status: d.inspection_status || 'pending',
            issue_description: d.issue_description || '',
            destination: d.destination || '',
            commission_details: d.commission_details || '',
            customer_id: d.customer_id || '',
            object_id: d.object_id || '',
            lead_product_list_id: d.lead_product_list_id || '',
            product_id: d.product_id || d.article_group_id || '',
            article_group_id: d.article_group_id || '',
            customer_display: d.customer_display || '',
            object_display: d.object_display || '',
            product_display: d.product_display || '',
            relation_picker_text: d.relation_picker_text || '',
            department_id: d.department_id || '',
            accepted_by_employee_id: d.accepted_by_employee_id || '',
            orderer_employee_id: d.orderer_employee_id || '',
            qty: d.qty || '',
            unit: d.unit || '',
            purchase_price: d.purchase_price || '',
            outbound_customer_id: d.outbound_customer_id || '',
            outbound_object_id: d.outbound_object_id || '',
            attachments: Array.isArray(d.attachments) ? d.attachments.map(normalizeAttachment) : [],
            inbound_attachments: Array.isArray(d.inbound_attachments) ? d.inbound_attachments.map(normalizeAttachment) : [],
            outbound_attachments: Array.isArray(d.outbound_attachments) ? d.outbound_attachments.map(normalizeAttachment) : [],
            inbound_queue: [],
        };

        state.isModalOpen = true;
        renderModals();
    } catch (e) {
        alert('Fehler beim Laden.');
    }
}

function closeDeliveryModal() {
    state.isModalOpen = false;
    state.selectedDelivery = null;
    renderModals();
}

function closeIssueModal() {
    state.issueModalOpen = false;
    state.selectedForIssue = null;
    renderModals();
}

function openGallery(item) {
    state.galleryItem = normalizeAttachment(item);
    state.galleryModalOpen = true;
    renderModals();
}

function closeGalleryModal() {
    state.galleryItem = null;
    state.galleryModalOpen = false;
    renderModals();
}

function setModalInspection(value) {
    if (!state.selectedDelivery) return;
    state.selectedDelivery.inspection_status = value;
    renderModals();
}

function openIssueModal(id) {
    const item = state.deliveries.find(d => d.id === id);
    if (!item) return;

    state.selectedForIssue = {
        ...item,
        outbound_queue: [],
    };
    state.issueModalOpen = true;
    renderModals();
}

function applyRelationSelection(item, prefix = 'mdl') {
    const customerIdEl = document.getElementById(`${prefix}-customer-id`);
    const objectIdEl = document.getElementById(`${prefix}-object-id`);
    const lplIdEl = document.getElementById(`${prefix}-lpl-id`);
    const productIdEl = document.getElementById(`${prefix}-product-id`);
    const agIdEl = document.getElementById(`${prefix}-article-group-id`);

    if (customerIdEl) customerIdEl.value = item.customer_id || '';
    if (objectIdEl) objectIdEl.value = item.object_id || '';
    if (lplIdEl) lplIdEl.value = item.lead_product_list_id || '';
    if (productIdEl) productIdEl.value = item.product_id || '';
    if (agIdEl) agIdEl.value = item.article_group_id || '';

    const previewCustomer = document.getElementById(`${prefix}-preview-customer`);
    const previewObject = document.getElementById(`${prefix}-preview-object`);
    const previewProduct = document.getElementById(`${prefix}-preview-product`);

    if (previewCustomer) previewCustomer.textContent = item.customer_name || '-';
    if (previewObject) previewObject.textContent = item.object_name || '-';
    if (previewProduct) previewProduct.textContent = item.product_name || '-';

    const deptEl = document.getElementById('mdl-dept-id');
    if (deptEl && item.department_id && !deptEl.value) {
        deptEl.value = item.department_id;
        $(deptEl).trigger('change');
    }
}

function clearRelationSelection(prefix = 'mdl') {
    ['customer-id','object-id','lpl-id','product-id','article-group-id'].forEach(key => {
        const el = document.getElementById(`${prefix}-${key}`);
        if (el) el.value = '';
    });

    ['customer','object','product'].forEach(key => {
        const el = document.getElementById(`${prefix}-preview-${key}`);
        if (el) el.textContent = '-';
    });
}

function initRelationPicker(selector, delivery, prefix = 'mdl') {
    const $el = $(selector);
    if (!$el.length) return;

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
    }

    $el.select2({
        width: '100%',
        placeholder: 'Kunde, Objekt oder Produkt suchen...',
        allowClear: true,
        dropdownParent: $('.erp-modal:visible').last(),
        ajax: {
            url: endpoints.relationOptions,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term || '' };
            },
            processResults: function (data) {
                return { results: data.results || [] };
            },
            cache: true
        },
        templateResult: function (item) {
            if (item.loading) return item.text;
            return formatRelationOption(item);
        },
        templateSelection: function (item) {
            return item.text || item.id || '';
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    $el.off('select2:select').on('select2:select', function (e) {
        applyRelationSelection(e.params.data || {}, prefix);
    });

    $el.off('select2:clear').on('select2:clear', function () {
        clearRelationSelection(prefix);
    });

    if (delivery && delivery.lead_product_list_id && delivery.relation_picker_text) {
        const option = new Option(delivery.relation_picker_text, delivery.lead_product_list_id, true, true);
        $el.append(option).trigger('change');

        applyRelationSelection({
            customer_id: delivery.customer_id,
            object_id: delivery.object_id,
            lead_product_list_id: delivery.lead_product_list_id,
            product_id: delivery.product_id,
            article_group_id: delivery.article_group_id,
            customer_name: delivery.customer_display,
            object_name: delivery.object_display,
            product_name: delivery.product_display,
            department_id: delivery.department_id,
        }, prefix);
    }
}

function initEmployeeSelect2(selector) {
    const $el = $(selector);
    if (!$el.length) return;

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
    }

    $el.select2({
        width: '100%',
        placeholder: 'Bitte wählen...',
        allowClear: true,
        dropdownParent: $('.erp-modal:visible').last(),
        templateResult: function (item) {
            if (item.loading) return item.text;
            return formatEmployeeOption(item);
        },
        templateSelection: function (item) {
            if (!item.id) return item.text || 'Bitte wählen...';
            const employee = employeeById(item.id);
            return employee ? employee.text : (item.text || '');
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
}

function pushFilesToState(files, targetKey) {
    const items = Array.from(files || []);
    if (!items.length) return;

    if (state.selectedDelivery && targetKey === 'inbound_queue') {
        if (!Array.isArray(state.selectedDelivery.inbound_queue)) state.selectedDelivery.inbound_queue = [];
        items.forEach((file, index) => {
            state.selectedDelivery.inbound_queue.push(normalizeAttachment({
                id: null,
                label: file.name,
                original_name: file.name,
                mime_type: file.type,
                is_image: String(file.type || '').startsWith('image/'),
                scope: 'inbound',
                isQueued: true,
                queueIndex: state.selectedDelivery.inbound_queue.length + index,
                file,
            }));
        });
    }

    if (state.selectedForIssue && targetKey === 'outbound_queue') {
        if (!Array.isArray(state.selectedForIssue.outbound_queue)) state.selectedForIssue.outbound_queue = [];
        items.forEach((file, index) => {
            state.selectedForIssue.outbound_queue.push(normalizeAttachment({
                id: null,
                label: file.name,
                original_name: file.name,
                mime_type: file.type,
                is_image: String(file.type || '').startsWith('image/'),
                scope: 'outbound',
                isQueued: true,
                queueIndex: state.selectedForIssue.outbound_queue.length + index,
                file,
            }));
        });
    }

    renderModals();
}

function handleInboundFileInput(event) {
    pushFilesToState(event.target.files, 'inbound_queue');
    event.target.value = '';
}

function handleOutboundFileInput(event) {
    pushFilesToState(event.target.files, 'outbound_queue');
    event.target.value = '';
}

function removeQueuedInbound(index) {
    if (!state.selectedDelivery || !Array.isArray(state.selectedDelivery.inbound_queue)) return;
    state.selectedDelivery.inbound_queue.splice(index, 1);
    renderModals();
}

function removeQueuedOutbound(index) {
    if (!state.selectedForIssue || !Array.isArray(state.selectedForIssue.outbound_queue)) return;
    state.selectedForIssue.outbound_queue.splice(index, 1);
    renderModals();
}

function removeExistingAttachment(id, scope) {
    if (state.selectedDelivery) {
        state.selectedDelivery.attachments = (state.selectedDelivery.attachments || []).filter(x => String(x.id) !== String(id));
        state.selectedDelivery.inbound_attachments = (state.selectedDelivery.inbound_attachments || []).filter(x => String(x.id) !== String(id));
        state.selectedDelivery.outbound_attachments = (state.selectedDelivery.outbound_attachments || []).filter(x => String(x.id) !== String(id));
    }

    if (state.selectedForIssue && scope === 'outbound') {
        state.selectedForIssue.attachments = (state.selectedForIssue.attachments || []).filter(x => String(x.id) !== String(id));
        state.selectedForIssue.outbound_attachments = (state.selectedForIssue.outbound_attachments || []).filter(x => String(x.id) !== String(id));
    }

    // UI only until backend delete endpoint is added
    renderModals();
}

function renderAttachmentCards(items, scope, editable = false) {
    const list = (Array.isArray(items) ? items : []).map(normalizeAttachment).filter(Boolean);

    if (!list.length) {
        return `<div class="erp-empty" style="padding:20px;">Keine Dateien vorhanden.</div>`;
    }

    return `
        <div class="erp-attachment-list">
            ${list.map((item, index) => `
                <div class="erp-attachment-card">
                    <div class="erp-attachment-preview">
                        ${item.is_image && item.file_url
                            ? `<img src="${escapeHtml(item.file_url)}" alt="${escapeHtml(item.label)}">`
                            : item.is_image && item.file
                                ? `<img src="${escapeHtml(URL.createObjectURL(item.file))}" alt="${escapeHtml(item.label)}">`
                                : `
                                    <div class="erp-attachment-doc">
                                        ${icon('file-text','erp-i-lg')}
                                        <div class="erp-text-xs">${item.mime_type ? escapeHtml(item.mime_type) : 'Dokument'}</div>
                                    </div>
                                `
                        }
                    </div>
                    <div class="erp-attachment-body">
                        <div class="erp-attachment-name">${escapeHtml(item.label || item.original_name || 'Datei')}</div>
                        <div class="erp-attachment-meta">${item.isQueued ? 'Neu / noch nicht gespeichert' : 'Gespeichert'} · ${humanFileSize(item.file_size)}</div>
                        <div class="erp-attachment-tools">
                            ${(item.file_url || item.file) ? `
                                <button type="button" class="erp-btn erp-btn-light" onclick='openGallery(${JSON.stringify({
                                    id: item.id,
                                    label: item.label,
                                    original_name: item.original_name,
                                    file_url: item.file_url,
                                    mime_type: item.mime_type,
                                    is_image: item.is_image,
                                    scope: item.scope
                                }).replace(/'/g, "\\'")})'>
                                    ${icon('image')} Galerie
                                </button>
                            ` : ''}

                            ${item.file_url ? `
                                <a href="${escapeHtml(item.file_url)}" target="_blank" class="erp-btn erp-btn-light">
                                    ${icon('external-link')} Öffnen
                                </a>
                            ` : ''}

                            ${editable ? (
                                item.isQueued
                                    ? `<button type="button" class="erp-btn erp-btn-danger" onclick="${scope === 'inbound' ? `removeQueuedInbound(${index})` : `removeQueuedOutbound(${index})`}">${icon('trash-2')} Entfernen</button>`
                                    : `<button type="button" class="erp-btn erp-btn-danger" onclick="removeExistingAttachment('${escapeHtml(item.id || '')}','${scope}')">${icon('trash-2')} Entfernen</button>`
                            ) : ''}
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

async function saveDelivery(event) {
    event.preventDefault();

    const d = state.selectedDelivery;
    if (!d) return;

    const url = d.isNew ? endpoints.store : `${endpoints.showBase}/${d.id}`;
    const method = d.isNew ? 'POST' : 'POST';

    const fd = new FormData();
    fd.append('description', document.getElementById('mdl-desc').value);
    fd.append('note', document.getElementById('mdl-note').value || '');
    fd.append('status', document.getElementById('mdl-stat').value);
    fd.append('inspection_status', d.inspection_status);
    fd.append('issue_description', document.getElementById('mdl-issue-desc').value || '');
    fd.append('destination', document.getElementById('mdl-dest').value || '');
    fd.append('commission_details', document.getElementById('mdl-comm').value || '');

    fd.append('customer_id', document.getElementById('mdl-customer-id').value || '');
    fd.append('object_id', document.getElementById('mdl-object-id').value || '');
    fd.append('lead_product_list_id', document.getElementById('mdl-lpl-id').value || '');
    fd.append('article_group_id', document.getElementById('mdl-article-group-id').value || '');

    fd.append('department_id', document.getElementById('mdl-dept-id').value || '');
    fd.append('accepted_by_employee_id', document.getElementById('mdl-acc-emp-id').value || '');
    fd.append('orderer_employee_id', document.getElementById('mdl-ord-emp-id').value || '');

    fd.append('qty', document.getElementById('mdl-qty').value || '');
    fd.append('unit', document.getElementById('mdl-unit').value || '');
    fd.append('purchase_price', document.getElementById('mdl-price').value || '');

    if (!d.isNew) {
        fd.append('_method', 'PUT');
    }

    (d.inbound_queue || []).forEach((item, idx) => {
        if (item?.file) fd.append(`inbound_files[${idx}]`, item.file);
    });

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: fd
        });

        const result = await res.json();

        if (!res.ok || !result.success) {
            alert(result.message || 'Speichern fehlgeschlagen.');
            return;
        }

        closeDeliveryModal();

        pushLocalNotification({
            action: d.isNew ? 'create' : 'update',
            title: d.isNew ? 'Wareneingang erstellt' : 'Wareneingang aktualisiert',
            message: result.message || (d.isNew ? 'Ein neuer Wareneingang wurde angelegt.' : 'Ein Wareneingang wurde bearbeitet.'),
            record: result.data,
        });

        await loadDeliveries(false);
    } catch (e) {
        alert('Fehler beim Speichern.');
    }
}

async function deleteDelivery(id) {
    if (!confirm('Möchten Sie diesen Eintrag wirklich löschen?')) return;

    try {
        const res = await fetch(`${endpoints.showBase}/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const result = await res.json();

        if (!res.ok || !result.success) {
            alert(result.message || 'Löschen fehlgeschlagen.');
            return;
        }

        const current = state.selectedDelivery;
        closeDeliveryModal();

        pushLocalNotification({
            action: 'delete',
            title: 'Wareneingang gelöscht',
            message: result.message || 'Ein Wareneingang wurde gelöscht.',
            record: current ? { id: current.id, code: current.code, status: 'deleted', inspection_status: current.inspection_status } : null,
        });

        await loadDeliveries(false);
    } catch (e) {
        alert('Fehler beim Löschen.');
    }
}

async function saveIssue(event) {
    event.preventDefault();

    const item = state.selectedForIssue;
    if (!item) return;

    const fd = new FormData();
    fd.append('outbound_recipient', document.getElementById('issue-rec').value);
    fd.append('outbound_project', document.getElementById('issue-proj').value || '');
    fd.append('outbound_customer_id', document.getElementById('issue-customer-id').value || '');
    fd.append('outbound_object_id', document.getElementById('issue-object-id').value || '');

    (item.outbound_queue || []).forEach((fileItem, idx) => {
        if (fileItem?.file) fd.append(`outbound_files[${idx}]`, fileItem.file);
    });

    try {
        const res = await fetch(`${endpoints.showBase}/${item.id}/issue`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: fd
        });

        const result = await res.json();

        if (!res.ok || !result.success) {
            alert(result.message || 'Ausbuchen fehlgeschlagen.');
            return;
        }

        closeIssueModal();

        pushLocalNotification({
            action: 'issue',
            title: 'Warenausgang gebucht',
            message: result.message || 'Die Ware wurde ausgebucht.',
            record: result.data,
        });

        await loadDeliveries(false);
    } catch (e) {
        alert('Fehler beim Ausbuchen.');
    }
}

async function quickStatus(id, value) {
    if (!value) return;

    try {
        const res = await fetch(`${endpoints.showBase}/${id}/quick-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ status: value })
        });

        const result = await res.json();

        if (!res.ok || !result.success) {
            alert(result.message || 'Status konnte nicht geändert werden.');
            return;
        }

        const local = state.deliveries.find(x => x.id === id);

        pushLocalNotification({
            action: 'quick_status',
            title: 'Status geändert',
            message: result.message || 'Der Status wurde per Schnellaktion geändert.',
            record: local ? { ...local, status: value } : { id, status: value },
        });

        await loadDeliveries(false);
    } catch (e) {
        alert('Fehler beim Statuswechsel.');
    }
}

async function quickMoveStatus(id, status) {
    if (!id || !['pending','processing','completed'].includes(status)) return;
    await quickStatus(id, status);
}

function pushLocalNotification({ action, title, message, record }) {
    const code = record?.code || null;

    state.notifications.unshift({
        id: 'local-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8),
        title,
        message,
        action,
        goods_receipt_id: record?.id || null,
        goods_receipt_code: code,
        actor_employee_id: window.__AUTH_EMPLOYEE_ID__,
        actor_employee_name: null,
        status: record?.status || null,
        inspection_status: record?.inspection_status || null,
        happened_at: new Date().toISOString(),
        created_at: new Date().toISOString(),
        read_at: null,
    });

    if (state.notifications.length > 100) {
        state.notifications = state.notifications.slice(0, 100);
    }
}

function kanbanDragStart(event, id) {
    state.draggingId = id;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', String(id));
    const el = event.currentTarget;
    if (el) el.classList.add('is-dragging');
}

function kanbanDragEnd(event) {
    state.draggingId = null;
    document.querySelectorAll('.erp-kanban-body').forEach(el => el.classList.remove('is-drag-over'));
    const el = event.currentTarget;
    if (el) el.classList.remove('is-dragging');
}

function kanbanDragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    const zone = event.currentTarget;
    if (zone) zone.classList.add('is-drag-over');
}

function kanbanDragLeave(event) {
    const zone = event.currentTarget;
    if (zone) zone.classList.remove('is-drag-over');
}

async function kanbanDrop(event, newStatus) {
    event.preventDefault();

    const zone = event.currentTarget;
    if (zone) zone.classList.remove('is-drag-over');

    const id = Number(event.dataTransfer.getData('text/plain') || state.draggingId || 0);
    if (!id) return;

    const item = state.deliveries.find(x => Number(x.id) === id);
    if (!item) return;
    if (item.status === newStatus) return;

    if (newStatus === 'issued') {
        alert('Status "Ausgegeben" bitte über Warenausgang buchen.');
        return;
    }

    await quickMoveStatus(id, newStatus);
}

function renderModals() {
    const container = document.getElementById('modal-container');
    let html = '';

    if (state.isModalOpen && state.selectedDelivery) {
        const d = state.selectedDelivery;

        html += `
            <div class="erp-modal-layer erp-fade-in">
                <div class="erp-modal-backdrop" onclick="closeDeliveryModal()"></div>

                <div class="erp-modal erp-modal-lg">
                    <div class="erp-modal-head">
                        <div class="erp-row erp-row-middle">
                            <span class="erp-code">${d.isNew ? 'NEUER EINTRAG' : escapeHtml(d.code)}</span>
                            <div class="erp-title-md">${d.isNew ? 'Wareneingang erfassen' : 'Lieferung bearbeiten'}</div>
                        </div>

                        <div class="erp-row erp-row-middle">
                            ${!d.isNew ? `<button type="button" onclick="deleteDelivery(${d.id})" class="erp-btn erp-btn-ghost erp-btn-icon">${icon('trash-2')}</button>` : ''}
                            <button type="button" onclick="closeDeliveryModal()" class="erp-btn erp-btn-ghost erp-btn-icon">${icon('x')}</button>
                        </div>
                    </div>

                    <div class="erp-modal-body custom-scrollbar">
                        <form id="crud-form" onsubmit="saveDelivery(event)">
                            <div class="erp-form-section">
                                <div class="erp-kicker">Basisdaten</div>

                                <div class="erp-form-grid">
                                    <div class="erp-field erp-span-2">
                                        <label class="erp-label">Beschreibung *</label>
                                        <input type="text" id="mdl-desc" required value="${escapeHtml(d.description)}" class="erp-input" placeholder="Genaue Beschreibung...">
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Menge</label>
                                        <input type="number" step="0.01" id="mdl-qty" value="${escapeHtml(d.qty || '')}" class="erp-input" placeholder="z. B. 10">
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Einheit</label>
                                        <input type="text" id="mdl-unit" value="${escapeHtml(d.unit || '')}" class="erp-input" placeholder="Stk / Palette / m">
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Einkaufspreis</label>
                                        <input type="number" step="0.01" id="mdl-price" value="${escapeHtml(d.purchase_price || '')}" class="erp-input" placeholder="0.00">
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Ziel</label>
                                        <select id="mdl-dest" class="erp-select">
                                            <option value="">Bitte wählen...</option>
                                            <option value="lager" ${d.destination === 'lager' ? 'selected' : ''}>Für das Lager</option>
                                            <option value="kommission" ${d.destination === 'kommission' ? 'selected' : ''}>Für Kommission</option>
                                        </select>
                                    </div>

                                    <div class="erp-field erp-span-2">
                                        <label class="erp-label">Kommission / Projekt</label>
                                        <input type="text" id="mdl-comm" value="${escapeHtml(d.commission_details || '')}" class="erp-input">
                                    </div>

                                    <div class="erp-field erp-span-2">
                                        <label class="erp-label">Notiz</label>
                                        <textarea id="mdl-note" class="erp-textarea">${escapeHtml(d.note || '')}</textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="erp-divider" style="margin:24px 0;">

                            <div class="erp-form-section">
                                <div class="erp-kicker">Verknüpfungen & Zuweisung</div>

                                <div class="erp-form-grid-3">
                                    <div class="erp-field erp-span-3">
                                        <label class="erp-label">Kunde / Objekt / Produkt</label>
                                        <div class="erp-select2-wrap">
                                            <select id="mdl-relation-picker" class="erp-select">
                                                <option value="">Bitte Kunde, Objekt und Produkt suchen...</option>
                                            </select>
                                        </div>

                                        <div class="erp-note" style="margin-top:8px;">
                                            <div class="erp-kicker">Automatisch verknüpft</div>
                                            <div class="erp-kv" style="margin-top:10px;">
                                                <div class="erp-kv-item">
                                                    <div class="erp-kv-label">Kunde</div>
                                                    <div class="erp-kv-value" id="mdl-preview-customer">${escapeHtml(d.customer_display || '-')}</div>
                                                </div>
                                                <div class="erp-kv-item">
                                                    <div class="erp-kv-label">Objekt</div>
                                                    <div class="erp-kv-value" id="mdl-preview-object">${escapeHtml(d.object_display || '-')}</div>
                                                </div>
                                                <div class="erp-kv-item">
                                                    <div class="erp-kv-label">Produkt</div>
                                                    <div class="erp-kv-value" id="mdl-preview-product">${escapeHtml(d.product_display || '-')}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="mdl-customer-id" value="${escapeHtml(d.customer_id || '')}">
                                        <input type="hidden" id="mdl-object-id" value="${escapeHtml(d.object_id || '')}">
                                        <input type="hidden" id="mdl-lpl-id" value="${escapeHtml(d.lead_product_list_id || '')}">
                                        <input type="hidden" id="mdl-product-id" value="${escapeHtml(d.product_id || d.article_group_id || '')}">
                                        <input type="hidden" id="mdl-article-group-id" value="${escapeHtml(d.article_group_id || '')}">
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Abteilung</label>
                                        <div class="erp-select2-wrap">
                                            <select id="mdl-dept-id" class="erp-select">
                                                <option value="">Bitte wählen...</option>
                                                ${renderDepartmentOptions(d.department_id)}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Angenommen von</label>
                                        <div class="erp-select2-wrap">
                                            <select id="mdl-acc-emp-id" class="erp-select">
                                                <option value="">Bitte wählen...</option>
                                                ${renderEmployeeOptions(d.accepted_by_employee_id)}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Besteller / Zuständig</label>
                                        <div class="erp-select2-wrap">
                                            <select id="mdl-ord-emp-id" class="erp-select">
                                                <option value="">Bitte wählen...</option>
                                                ${renderEmployeeOptions(d.orderer_employee_id)}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="erp-field">
                                        <label class="erp-label">Status</label>
                                        <select id="mdl-stat" class="erp-select">
                                            <option value="pending" ${d.status === 'pending' ? 'selected' : ''}>Erfasst</option>
                                            <option value="processing" ${d.status === 'processing' ? 'selected' : ''}>In Prüfung</option>
                                            <option value="completed" ${d.status === 'completed' ? 'selected' : ''}>Lagernd</option>
                                            <option value="issued" ${d.status === 'issued' ? 'selected' : ''} disabled>Ausgegeben</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="erp-divider" style="margin:24px 0;">

                            <div class="erp-form-section">
                                <div class="erp-kicker">Qualitätsprüfung</div>

                                <div class="erp-chip-group">
                                    <button type="button" onclick="setModalInspection('pending')" class="erp-chip ${d.inspection_status === 'pending' ? 'is-active' : ''}">Ungeprüft</button>
                                    <button type="button" onclick="setModalInspection('ok')" class="erp-chip ${d.inspection_status === 'ok' ? 'is-active' : ''}">OK</button>
                                    <button type="button" onclick="setModalInspection('issue')" class="erp-chip ${d.inspection_status === 'issue' ? 'is-active' : ''}">Mangel</button>
                                </div>

                                <div class="erp-field">
                                    <label class="erp-label">Mangelbeschreibung</label>
                                    <textarea id="mdl-issue-desc" class="erp-textarea">${escapeHtml(d.issue_description || '')}</textarea>
                                </div>
                            </div>

                            <hr class="erp-divider" style="margin:24px 0;">

                            <div class="erp-form-section">
                                <div class="erp-row-between erp-wrap">
                                    <div>
                                        <div class="erp-kicker">Eingangsbelege / Galerie</div>
                                        <div class="erp-text-sm erp-text-muted">Lieferschein, Fotos und weitere Dokumente prüfen, hinzufügen oder entfernen.</div>
                                    </div>
                                    <span class="erp-badge erp-badge-soft">${(d.inbound_attachments || []).length + (d.inbound_queue || []).length}</span>
                                </div>

                                <div class="erp-attachment-zone" style="margin-top:12px;">
                                    <div class="erp-attachment-actions">
                                        <div class="erp-text-sm erp-text-muted">JPG, PNG, WEBP, PDF</div>
                                        <div class="erp-row erp-row-middle" style="gap:8px;">
                                            <input id="mdl-inbound-files" type="file" class="erp-hidden-input" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" onchange="handleInboundFileInput(event)">
                                            <button type="button" class="erp-btn erp-btn-secondary" onclick="document.getElementById('mdl-inbound-files').click()">
                                                ${icon('paperclip')} Hinzufügen
                                            </button>
                                        </div>
                                    </div>

                                    <div style="margin-top:14px;">
                                        <div class="erp-kicker" style="margin-bottom:8px;">Bereits gespeichert</div>
                                        ${renderAttachmentCards(d.inbound_attachments || [], 'inbound', true)}
                                    </div>

                                    <div style="margin-top:16px;">
                                        <div class="erp-kicker" style="margin-bottom:8px;">Neu hinzugefügt (noch nicht gespeichert)</div>
                                        ${renderAttachmentCards(d.inbound_queue || [], 'inbound', true)}
                                    </div>
                                </div>
                            </div>

                            ${!d.isNew ? `
                                <hr class="erp-divider" style="margin:24px 0;">
                                <div class="erp-form-section">
                                    <div class="erp-row-between erp-wrap">
                                        <div>
                                            <div class="erp-kicker">Ausgangsbelege Historie</div>
                                            <div class="erp-text-sm erp-text-muted">Hier sehen Sie, was beim Warenausgang hinzugefügt wurde.</div>
                                        </div>
                                        <span class="erp-badge erp-badge-blue">${(d.outbound_attachments || []).length}</span>
                                    </div>

                                    <div class="erp-attachment-zone" style="margin-top:12px;">
                                        ${renderAttachmentCards(d.outbound_attachments || [], 'outbound', false)}
                                    </div>
                                </div>
                            ` : ''}
                        </form>
                    </div>

                    <div class="erp-modal-foot">
                        <button type="button" onclick="closeDeliveryModal()" class="erp-btn erp-btn-light">Abbrechen</button>
                        <button type="submit" form="crud-form" class="erp-btn erp-btn-primary">${d.isNew ? 'Lieferung erstellen' : 'Änderungen speichern'}</button>
                    </div>
                </div>
            </div>
        `;
    }

    if (state.issueModalOpen && state.selectedForIssue) {
        const item = state.selectedForIssue;
        const oldOut = Array.isArray(item.outbound_attachments) ? item.outbound_attachments : [];

        html += `
            <div class="erp-modal-layer erp-fade-in">
                <div class="erp-modal-backdrop" onclick="closeIssueModal()"></div>

                <div class="erp-modal erp-modal-sm">
                    <div class="erp-modal-head">
                        <div class="erp-title-md">${icon('file-output')} Warenausgabe buchen</div>
                        <button type="button" onclick="closeIssueModal()" class="erp-btn erp-btn-ghost erp-btn-icon">${icon('x')}</button>
                    </div>

                    <div class="erp-modal-body custom-scrollbar">
                        <div class="erp-note">
                            <div class="erp-kicker">Ausgewählte Ware</div>
                            <div class="erp-title-sm" style="margin-top:6px;">${escapeHtml(item.description)}</div>
                            <div class="erp-meta" style="margin-top:6px;">Eingang: ${formatDate(item.received_at)} | Kennung: ${escapeHtml(item.code)}</div>
                        </div>

                        <form id="issue-form" onsubmit="saveIssue(event)" style="margin-top:20px;">
                            <div style="display:flex;flex-direction:column;gap:16px;">
                                <div class="erp-field">
                                    <label class="erp-label">Empfänger / Kunde / Techniker *</label>
                                    <input type="text" id="issue-rec" required class="erp-input" placeholder="Wer nimmt die Ware entgegen?">
                                </div>

                                <div class="erp-field">
                                    <label class="erp-label">Projekt</label>
                                    <input type="text" id="issue-proj" value="${escapeHtml(item.commission_details || '')}" class="erp-input">
                                </div>

                                <div class="erp-field">
                                    <label class="erp-label">Outbound Kunde / Objekt / Produkt</label>
                                    <div class="erp-select2-wrap">
                                        <select id="issue-relation-picker" class="erp-select">
                                            <option value="">Bitte Kunde, Objekt und Produkt suchen...</option>
                                        </select>
                                    </div>

                                    <div class="erp-note" style="margin-top:8px;">
                                        <div class="erp-kicker">Automatisch verknüpft</div>
                                        <div class="erp-kv" style="margin-top:10px;">
                                            <div class="erp-kv-item">
                                                <div class="erp-kv-label">Kunde</div>
                                                <div class="erp-kv-value" id="issue-preview-customer">-</div>
                                            </div>
                                            <div class="erp-kv-item">
                                                <div class="erp-kv-label">Objekt</div>
                                                <div class="erp-kv-value" id="issue-preview-object">-</div>
                                            </div>
                                            <div class="erp-kv-item">
                                                <div class="erp-kv-label">Produkt</div>
                                                <div class="erp-kv-value" id="issue-preview-product">-</div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="issue-customer-id" value="">
                                    <input type="hidden" id="issue-object-id" value="">
                                    <input type="hidden" id="issue-lpl-id" value="">
                                    <input type="hidden" id="issue-product-id" value="">
                                    <input type="hidden" id="issue-article-group-id" value="">
                                </div>

                                <div class="erp-field">
                                    <label class="erp-label">Ausgangsbelege / Fotos</label>
                                    <div class="erp-attachment-zone">
                                        <div class="erp-attachment-actions">
                                            <div class="erp-text-sm erp-text-muted">Ausgabe-Fotos, Empfangsbeleg, PDF</div>
                                            <div class="erp-row erp-row-middle" style="gap:8px;">
                                                <input id="issue-outbound-files" type="file" class="erp-hidden-input" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" onchange="handleOutboundFileInput(event)">
                                                <button type="button" class="erp-btn erp-btn-secondary" onclick="document.getElementById('issue-outbound-files').click()">
                                                    ${icon('paperclip')} Hinzufügen
                                                </button>
                                            </div>
                                        </div>

                                        <div style="margin-top:14px;">
                                            <div class="erp-kicker" style="margin-bottom:8px;">Bereits vorhanden</div>
                                            ${renderAttachmentCards(oldOut, 'outbound', true)}
                                        </div>

                                        <div style="margin-top:16px;">
                                            <div class="erp-kicker" style="margin-bottom:8px;">Neu hinzugefügt</div>
                                            ${renderAttachmentCards(item.outbound_queue || [], 'outbound', true)}
                                        </div>
                                    </div>
                                </div>

                                <div class="erp-field">
                                    <label class="erp-label">Ausgeführt durch</label>
                                    <input type="text" readonly value="${escapeHtml(String(window.__AUTH_EMPLOYEE_ID__ || ''))}" class="erp-input" style="background:#f0f4f8;">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="erp-modal-foot">
                        <button type="button" onclick="closeIssueModal()" class="erp-btn erp-btn-light">Abbrechen</button>
                        <button type="submit" form="issue-form" class="erp-btn erp-btn-secondary">${icon('log-out')} Ware ausbuchen</button>
                    </div>
                </div>
            </div>
        `;
    }

    if (state.galleryModalOpen && state.galleryItem) {
        const g = state.galleryItem;
        html += `
            <div class="erp-modal-layer erp-fade-in">
                <div class="erp-modal-backdrop" onclick="closeGalleryModal()"></div>

                <div class="erp-modal erp-modal-sm">
                    <div class="erp-modal-head">
                        <div>
                            <div class="erp-title-md">Galerie / Vorschau</div>
                            <div class="erp-text-sm erp-text-muted">${escapeHtml(g.label || g.original_name || 'Datei')}</div>
                        </div>
                        <button type="button" onclick="closeGalleryModal()" class="erp-btn erp-btn-ghost erp-btn-icon">${icon('x')}</button>
                    </div>

                    <div class="erp-modal-body">
                        ${g.is_image && g.file_url ? `
                            <img src="${escapeHtml(g.file_url)}" alt="${escapeHtml(g.label || 'Bild')}" class="erp-gallery-modal-image">
                        ` : g.file_url ? `
                            <iframe src="${escapeHtml(g.file_url)}" class="erp-gallery-modal-frame"></iframe>
                        ` : `
                            <div class="erp-note">
                                Diese Datei wurde lokal ausgewählt und ist noch nicht gespeichert. Sie wird nach dem Speichern verfügbar.
                            </div>
                        `}
                    </div>

                    <div class="erp-modal-foot">
                        <button type="button" onclick="closeGalleryModal()" class="erp-btn erp-btn-light">Schließen</button>
                        ${g.file_url ? `<a href="${escapeHtml(g.file_url)}" target="_blank" class="erp-btn erp-btn-secondary">${icon('external-link')} Neu öffnen</a>` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
    lucide.createIcons();

    if (state.isModalOpen && state.selectedDelivery) {
        initRelationPicker('#mdl-relation-picker', state.selectedDelivery, 'mdl');
        initEmployeeSelect2('#mdl-acc-emp-id');
        initEmployeeSelect2('#mdl-ord-emp-id');
        $('#mdl-dept-id').select2({
            width: '100%',
            placeholder: 'Bitte wählen...',
            allowClear: true,
            dropdownParent: $('.erp-modal:visible').last()
        });
    }

    if (state.issueModalOpen && state.selectedForIssue) {
        initRelationPicker('#issue-relation-picker', null, 'issue');
    }
}

async function loadDeliveries(softRefreshOnly = false) {
    try {
        const params = new URLSearchParams({
            page: state.currentPage,
            per_page: state.itemsPerPage,
            search: state.searchQuery || '',
            status: state.filters.status || '',
            inspection_status: state.filters.inspection_status || '',
            destination: state.filters.destination || '',
            sort_by: state.sortConfig.key,
            sort_dir: state.sortConfig.direction,
        });

        const res = await fetch(`${endpoints.data}?${params.toString()}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        const result = await res.json();

        if (!res.ok || !result.success) {
            throw new Error(result.message || 'Fehler beim Laden');
        }

        state.deliveries = Array.isArray(result.data) ? result.data : [];
        state.currentPage = result.meta?.current_page || 1;
        state.lastPage = result.meta?.last_page || 1;
        state.itemsPerPage = result.meta?.per_page || 10;
        state.totalItems = result.meta?.total || state.deliveries.length;

        if (!softRefreshOnly) {
            renderApp();
        } else {
            renderTopNav();
            renderModuleHeader();
            renderModuleBody();
            renderModals();
        }
    } catch (e) {
        const body = document.getElementById('module-body');
        if (body) {
            body.innerHTML = `
                <div class="erp-shell-sm">
                    <div class="erp-card erp-card-red">
                        <div class="erp-card-body" style="color:#b42318;font-weight:700;">Daten konnten nicht geladen werden.</div>
                    </div>
                </div>
            `;
        }
    } finally {
        lucide.createIcons();
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    renderApp();
    await loadDeliveries();
});
</script>
@endsection