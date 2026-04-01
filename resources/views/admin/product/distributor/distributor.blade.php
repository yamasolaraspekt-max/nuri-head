@extends('admin.layouts.app')

@section('title', 'Lieferanten')

@once
@push('style')
<style>
  :root{
    --app-bg:#f3f4f6;
    --card-bg:#ffffff;
    --card-soft:#fafafa;
    --text-main:#111827;
    --text-muted:#6b7280;
    --border:#e5e7eb;

    --primary:#93c21c;
    --primary-hover:#7baa18;
    --primary-light:#f4fae7;

    --blue:#74b2d4;
    --blue-light:#eff6ff;

    --success:#10b981;
    --success-hover:#0d9668;
    --success-light:#ecfdf5;

    --warning:#f59e0b;
    --warning-light:#fffbeb;
    --warning-dark:#b45309;

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

  .oc-wrap {
      font-family: Inter, system-ui, -apple-system, sans-serif;
      color: var(--text-main);
      max-width: 1500px;
      margin: 20px auto;
      padding: 39px;
      padding-right: 79px;
  }

  .oc-header{
    margin:103px 0 18px;
  }

  .oc-titlebar{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:12px;
    margin-bottom:16px;
    flex-wrap:wrap;
  }

  .oc-title{
    font-size:26px;
    font-weight:800;
    letter-spacing:-.025em;
    color:#111827;
  }

  .oc-sub{
    font-size:14px;
    color:var(--text-muted);
    margin-top:4px;
  }

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
    text-decoration:none;
    box-shadow:var(--shadow-sm);
  }

  .oc-btn:hover{
    background:var(--primary-hover);
    color:#fff;
    text-decoration:none;
  }

  .oc-btn.success{
    background:var(--success);
  }

  .oc-btn.success:hover{
    background:var(--success-hover);
  }

  .oc-btn.danger{
    background:var(--danger);
  }

  .oc-btn.danger:hover{
    background:var(--danger-hover);
  }

  .oc-btn-soft{
    background:#fff;
    color:var(--text-main);
    border:1px solid var(--border);
    padding:10px 14px;
    border-radius:10px;
    font-weight:800;
    cursor:pointer;
    transition:var(--transition);
    text-decoration:none;
    box-shadow:var(--shadow-sm);
  }

  .oc-btn-soft:hover{
    background:#f9fafb;
    color:var(--text-main);
    text-decoration:none;
  }

  .oc-btn-ic{
    width:36px;
    height:36px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:var(--text-muted);
    cursor:pointer;
    transition:var(--transition);
    text-decoration:none;
    box-shadow:var(--shadow-sm);
  }

  .oc-btn-ic:hover{
    background:#f9fafb;
    color:var(--text-main);
    border-color:#d1d5db;
    text-decoration:none;
  }

  .oc-btn-ic.primary{
    color:var(--primary);
    border-color:var(--primary-light);
    background:var(--primary-light);
  }

  .oc-btn-ic.primary:hover{
    border-color:var(--primary);
  }

  .oc-btn-ic.warning{
    color:#d97706;
    border-color:#fde7b0;
    background:#fffbeb;
  }

  .oc-btn-ic.warning:hover{
    border-color:#f59e0b;
  }

  .oc-btn-ic.success{
    color:var(--success);
    border-color:#c7f2df;
    background:var(--success-light);
  }

  .oc-btn-ic.success:hover{
    border-color:var(--success);
  }

  .oc-btn-ic.danger{
    color:var(--danger);
    border-color:rgba(239,68,68,.18);
    background:var(--danger-light);
  }

  .oc-btn-ic.danger:hover{
    border-color:rgba(239,68,68,.35);
  }

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }

  @media(max-width:1200px){
    .oc-analytics{
      grid-template-columns:repeat(2,minmax(0,1fr));
    }
  }

  @media(max-width:700px){
    .oc-analytics{
      grid-template-columns:1fr;
    }
  }

  .oc-stat{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:16px;
    padding:16px;
    box-shadow:var(--shadow-sm);
    display:flex;
    align-items:center;
    gap:12px;
    min-height:92px;
  }

  .oc-stat-icon{
    width:48px;
    height:48px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .oc-stat-icon.total{background:var(--blue-light);color:var(--blue);}
  .oc-stat-icon.active{background:var(--success-light);color:var(--success);}
  .oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706;}
  .oc-stat-icon.contact{background:var(--gray-light);color:var(--gray);}

  .oc-stat-meta{min-width:0;}

  .oc-stat-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  .oc-stat-value{
    font-size:24px;
    font-weight:900;
    color:#111827;
    line-height:1.1;
    margin-top:4px;
  }

  .oc-stat-sub{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
  }

  .oc-toolbar{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:14px 16px;
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    align-items:flex-end;
    justify-content:space-between;
    margin-bottom:16px;
    box-shadow:var(--shadow-sm);
  }

  .oc-toolbar-left,
  .oc-toolbar-right{
    display:flex;
    align-items:flex-end;
    gap:12px;
    flex-wrap:wrap;
  }

  .oc-toolbar-left{
    flex:1;
  }

  .oc-filter-block{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:170px;
  }

  .oc-filter-block.search{
    flex:1;
    min-width:260px;
  }

  .oc-filter-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  .oc-input{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:8px;
    padding:10px 12px 10px 36px;
    font-size:14px;
    outline:none;
    transition:var(--transition);
    min-width:240px;
    width:100%;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:10px center;
    background-size:16px;
  }

  .oc-input:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-select{
    padding:10px 34px 10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background-color:#fff;
    font-size:13px;
    cursor:pointer;
    outline:none;
    appearance:none;
    min-height:42px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:14px;
  }

  .oc-list-head{
    display:grid;
    grid-template-columns:90px 80px minmax(260px,1.4fr) minmax(260px,1.2fr) minmax(220px,1fr) 140px 170px;
    gap:14px;
    align-items:center;
    padding:0 16px 10px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  @media(max-width:1180px){
    .oc-list-head{
      display:none;
    }
  }

  .oc-head-link{
    color:inherit;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-weight:900;
  }

  .oc-head-link:hover{
    color:var(--text-main);
    text-decoration:none;
  }

  .oc-list{
    display:flex;
    flex-direction:column;
    gap:12px;
  }

  .oc-item{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    transition:var(--transition);
    position:relative;
    overflow:hidden;
  }

  .oc-item:hover{
    border-color:var(--primary);
    box-shadow:var(--shadow);
  }

  .oc-item-header{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:center;
    grid-template-columns:90px 80px minmax(260px,1.4fr) minmax(260px,1.2fr) minmax(220px,1fr) 140px 170px;
  }

  @media(max-width:1180px){
    .oc-item-header{
      grid-template-columns:70px 1fr;
      gap:12px;
    }

    .oc-responsive-col{
      grid-column:2;
    }

    .oc-actions-wrap{
      grid-column:2;
      justify-self:start;
    }
  }

  .oc-cell{
    min-width:0;
  }

  .oc-cell-title{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:4px;
    display:none;
  }

  @media(max-width:1180px){
    .oc-cell-title{
      display:block;
    }
  }

  .oc-id-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:54px;
    height:36px;
    padding:0 12px;
    border-radius:10px;
    background:var(--blue-light);
    color:var(--blue);
    font-size:13px;
    font-weight:900;
  }

  .oc-logo{
    width:52px;
    height:52px;
    border-radius:14px;
    border:1px solid var(--border);
    background:#fff;
    object-fit:contain;
    padding:6px;
    box-shadow:var(--shadow-sm);
  }

  .oc-main{
    display:flex;
    flex-direction:column;
    min-width:0;
  }

  .oc-ttl{
    font-weight:800;
    font-size:15px;
    margin-bottom:4px;
    color:#111827;
  }

  .oc-subt{
    font-size:13px;
    color:var(--text-muted);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .oc-mini{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
    line-height:1.45;
  }

  .oc-status-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    white-space:nowrap;
  }

  .oc-status-pill.gray{background:#f3f4f6;color:#4b5563;}
  .oc-status-pill.green{background:#ecfdf5;color:#047857;}
  .oc-status-pill.orange{background:#fffbeb;color:#b45309;}
  .oc-status-pill.red{background:#fef2f2;color:#b91c1c;}

  .oc-actions-wrap{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
  }

  .oc-pagination{
    margin-top:18px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px 16px;
    box-shadow:var(--shadow-sm);
  }

  .oc-pagination .pagination{
    margin:0;
    display:flex;
    flex-wrap:wrap;
    gap:6px;
  }

  .oc-pagination .page-item .page-link{
    border-radius:10px !important;
    border:1px solid var(--border);
    color:var(--text-main);
    padding:8px 12px;
    line-height:1.1;
    box-shadow:none !important;
  }

  .oc-pagination .page-item.active .page-link{
    background:var(--primary);
    border-color:var(--primary);
    color:#fff;
  }

  .oc-pagination .page-item.disabled .page-link{
    color:#9ca3af;
    background:#f9fafb;
  }

  .oc-modal-backdrop{
    position:fixed;
    inset:0;
    z-index:1200;
    background:rgba(17,24,39,.55);
    backdrop-filter:blur(3px);
    opacity:0;
    pointer-events:none;
    transition:opacity .22s ease;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:18px;
  }

  .oc-modal-backdrop.open{
    opacity:1;
    pointer-events:auto;
  }

  .oc-modal{
    width:100%;
    max-width:760px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
  }

  .oc-modal-backdrop.open .oc-modal{
    transform:translateY(0) scale(1);
  }

  .oc-modal-h{
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:space-between;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
  }

  .oc-modal-ttl{
    font-weight:900;
    font-size:16px;
    line-height:1.2;
    margin:0;
    color:#111827;
  }

  .oc-modal-b{
    padding:20px 18px;
    max-height:72vh;
    overflow-y:auto;
  }

  .oc-modal-f{
    padding:14px 18px;
    border-top:1px solid var(--border);
    background:#fafafa;
    display:flex;
    gap:10px;
    justify-content:flex-end;
    flex-wrap:wrap;
  }

  .oc-form-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:16px;
  }

  @media(max-width:700px){
    .oc-form-grid{
      grid-template-columns:1fr;
    }
  }

  .oc-form-group{
    margin-bottom:16px;
  }

  .oc-label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:var(--text-main);
    margin-bottom:6px;
  }

  .oc-input-form{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }

  .oc-input-form:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-help{
    font-size:12px;
    color:var(--text-muted);
    margin-top:6px;
  }

  .oc-thumb-preview{
    margin-top:10px;
    width:72px;
    height:72px;
    border-radius:12px;
    border:1px solid var(--border);
    object-fit:contain;
    padding:6px;
    background:#fff;
  }

  .oc-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:9999;
    display:flex;
    flex-direction:column;
    gap:10px;
    pointer-events:none;
  }

  .oc-toast{
    pointer-events:auto;
    min-width:280px;
    max-width:360px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    box-shadow:var(--shadow);
    padding:12px;
    display:flex;
    gap:10px;
    align-items:flex-start;
    animation:ocToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
  }

  @keyframes ocToastIn{
    from{transform:translateX(100%);opacity:0}
    to{transform:translateX(0);opacity:1}
  }

  .oc-toast-ic{
    width:34px;
    height:34px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
  }

  .oc-toast-ic.ok{background:var(--success-light);color:var(--success);}
  .oc-toast-ic.warn{background:var(--warning-light);color:var(--warning);}
  .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger);}

  .oc-toast-ttl{
    font-weight:900;
    font-size:13px;
    margin:0;
    color:#111827;
  }

  .oc-toast-msg{
    font-size:12px;
    color:#374151;
    margin:4px 0 0 0;
    line-height:1.4;
  }

  .oc-toast-x{
    margin-left:auto;
    background:transparent;
    border:none;
    cursor:pointer;
    color:var(--text-muted);
  }

  .oc-image-large{
    width:100%;
    max-height:70vh;
    object-fit:contain;
    border-radius:12px;
    border:1px solid var(--border);
    background:#fff;
    padding:10px;
  }

  .oc-inline-stack{
    display:flex;
    flex-wrap:wrap;
    gap:6px;
    align-items:center;
  }

  .oc-chip{
    display:inline-flex;
    align-items:center;
    padding:4px 8px;
    border-radius:999px;
    font-size:11px;
    font-weight:800;
    background:#f3f4f6;
    color:#4b5563;
  }

  .oc-chip.blue{background:#eff6ff;color:#1d4ed8;}
  .oc-chip.green{background:#ecfdf5;color:#047857;}
  .oc-chip.orange{background:#fffbeb;color:#b45309;}
</style>
@endpush
@endonce

@section('content')
@php
    $items = collect($data->items());

    $totalCount = $data->total();
    $activeCount = $items->filter(fn($item) => in_array(($item->status ?? ''), ['active', 'Published'], true))->count();
    $unpublishedCount = $items->filter(fn($item) => ($item->status ?? '') === 'Unpublished' || empty($item->status))->count();
    $withContactCount = $items->filter(fn($item) => !empty($item->phone) || !empty($item->email) || !empty($item->mobile))->count();

    $currentSortBy = request('sort_by', 'id');
    $currentSortDir = request('sort_dir', 'desc');
@endphp

<div class="oc-wrap">
    <div class="oc-header">
        <div class="oc-titlebar">
            <div>
                <div class="oc-title">Lieferantenverwaltung</div>
                <div class="oc-sub">Übersicht, Filter, Import und Verwaltung aller Lieferanten.</div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button class="oc-btn success" type="button" onclick="openModal('modalImportCsv')">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 3v12"></path>
                        <path d="M7 10l5 5 5-5"></path>
                        <path d="M5 21h14"></path>
                    </svg>
                    CSV-Import
                </button>

                <button class="oc-btn" type="button" onclick="openModal('modalCreate')">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg>
                    Neuer Lieferant
                </button>
            </div>
        </div>
    </div>

    <div class="oc-analytics">
        <div class="oc-stat">
            <div class="oc-stat-icon total">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 7h18M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Gesamt</div>
                <div class="oc-stat-value">{{ $totalCount }}</div>
                <div class="oc-stat-sub">Alle Lieferanten</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon active">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Aktiv</div>
                <div class="oc-stat-value">{{ $activeCount }}</div>
                <div class="oc-stat-sub">Veröffentlicht oder aktiv</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon unpublished">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Unveröffentlicht</div>
                <div class="oc-stat-value">{{ $unpublishedCount }}</div>
                <div class="oc-stat-sub">Noch nicht veröffentlicht</div>
            </div>
        </div>

        <div class="oc-stat">
            <div class="oc-stat-icon contact">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 16.92V19a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 4.18 2 2 0 0 1 5 2h2.09a2 2 0 0 1 2 1.72l.3 2.11a2 2 0 0 1-.57 1.72l-1.27 1.27a16 16 0 0 0 6.91 6.91l1.27-1.27a2 2 0 0 1 1.72-.57l2.11.3A2 2 0 0 1 22 16.92z"/>
                </svg>
            </div>
            <div class="oc-stat-meta">
                <div class="oc-stat-label">Mit Kontakt</div>
                <div class="oc-stat-value">{{ $withContactCount }}</div>
                <div class="oc-stat-sub">Telefon, Mobil oder E-Mail vorhanden</div>
            </div>
        </div>
    </div>

    <form action="{{ route('distributors.index') }}" method="GET" class="oc-toolbar">
        <div class="oc-toolbar-left">
            <div class="oc-filter-block search">
                <label class="oc-filter-label">Suche</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="oc-input"
                    placeholder="Name, Kurzname, Straße, Stadt, E-Mail, Skonto oder Zahlungsziel ..."
                >
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Status</label>
                <select name="status" class="oc-select">
                    <option value="">Alle</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktiv</option>
                    <option value="Published" {{ request('status') === 'Published' ? 'selected' : '' }}>Veröffentlicht</option>
                    <option value="Unpublished" {{ request('status') === 'Unpublished' ? 'selected' : '' }}>Unveröffentlicht</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inaktiv</option>
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Sortieren nach</label>
                <select name="sort_by" class="oc-select">
                    <option value="id" {{ request('sort_by', 'id') === 'id' ? 'selected' : '' }}>ID</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                    <option value="city" {{ request('sort_by') === 'city' ? 'selected' : '' }}>Stadt</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Erstellt am</option>
                </select>
            </div>

            <div class="oc-filter-block">
                <label class="oc-filter-label">Richtung</label>
                <select name="sort_dir" class="oc-select">
                    <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>Aufsteigend</option>
                    <option value="desc" {{ request('sort_dir', 'desc') === 'desc' ? 'selected' : '' }}>Absteigend</option>
                </select>
            </div>
        </div>

        <div class="oc-toolbar-right">
            <button class="oc-btn-soft" type="submit">Filtern</button>
            <a href="{{ route('distributors.index') }}" class="oc-btn-soft">Zurücksetzen</a>
        </div>
    </form>

    @php
        $sortLink = function ($column) use ($currentSortBy, $currentSortDir) {
            $nextDir = ($currentSortBy === $column && $currentSortDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery([
                'sort_by' => $column,
                'sort_dir' => $nextDir,
            ]);
        };

        $sortIcon = function ($column) use ($currentSortBy, $currentSortDir) {
            if ($currentSortBy !== $column) {
                return '↕';
            }
            return $currentSortDir === 'asc' ? '↑' : '↓';
        };
    @endphp

    <div class="oc-list-head">
        <a class="oc-head-link" href="{{ $sortLink('id') }}">
            ID
            <span>{{ $sortIcon('id') }}</span>
        </a>

        <div>Logo</div>

        <a class="oc-head-link" href="{{ $sortLink('name') }}">
            Lieferant
            <span>{{ $sortIcon('name') }}</span>
        </a>

        <a class="oc-head-link" href="{{ $sortLink('city') }}">
            Adresse
            <span>{{ $sortIcon('city') }}</span>
        </a>

        <div>Kontakt / Konditionen</div>

        <a class="oc-head-link" href="{{ $sortLink('status') }}">
            Status
            <span>{{ $sortIcon('status') }}</span>
        </a>

        <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="oc-list">
        @forelse($data as $item)
            @php
                $status = (string) ($item->status ?? '');
                $isActive = in_array($status, ['active', 'Published'], true);

                $logoSrc = $item->image
                    ? asset('images/distributor/' . $item->image)
                    : asset('images/icons/placeholder.svg');

                $addressParts = array_filter([
                    $item->street ?? null,
                    trim(($item->postal_code ?? '') . ' ' . ($item->city ?? '')) ?: null,
                ]);

                $addressText = count($addressParts)
                    ? implode(', ', $addressParts)
                    : 'Keine Adresse';

                $kontaktText = collect([
                    $item->phone ? 'Tel: ' . $item->phone : null,
                    $item->mobile ? 'Mobil: ' . $item->mobile : null,
                    $item->email ? 'E-Mail: ' . $item->email : null,
                ])->filter()->implode(' • ');
            @endphp

            <div class="oc-item">
                <div class="oc-item-header">
                    <div class="oc-cell">
                        <div class="oc-cell-title">ID</div>
                        <span class="oc-id-badge">#{{ $item->id }}</span>
                    </div>

                    <div class="oc-cell">
                        <div class="oc-cell-title">Logo</div>
                        <button
                            type="button"
                            class="oc-btn-ic js-open-image"
                            data-name="{{ $item->name }}"
                            data-logo="{{ $logoSrc }}"
                            title="Logo anzeigen"
                            style="width:auto;height:auto;padding:0;border:none;background:transparent;"
                        >
                            <img src="{{ $logoSrc }}" alt="Logo" class="oc-logo">
                        </button>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Lieferant</div>
                        <div class="oc-main">
                            <div class="oc-ttl">{{ $item->name ?: 'Ohne Namen' }}</div>

                            @if(!empty($item->short_name))
                                <div class="oc-subt">{{ $item->short_name }}</div>
                            @else
                                <div class="oc-subt">Kein Kurzname hinterlegt</div>
                            @endif

                            <div class="oc-inline-stack" style="margin-top:8px;">
                                @if(!empty($item->account_number))
                                    <span class="oc-chip blue">Kontonummer: {{ $item->account_number }}</span>
                                @endif

                                @if(!is_null($item->cash_discount) && $item->cash_discount !== '')
                                    <span class="oc-chip green">Skonto: {{ number_format((float) $item->cash_discount, 2, ',', '.') }}%</span>
                                @endif

                                @if(!empty($item->payment_terms))
                                    <span class="oc-chip orange">Zahlungsziel: {{ $item->payment_terms }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Adresse</div>
                        <div class="oc-main">
                            <div class="oc-ttl" style="font-size:14px;">{{ $addressText }}</div>

                            @if(!empty($item->city) || !empty($item->postal_code))
                                <div class="oc-subt">{{ trim(($item->postal_code ?? '') . ' ' . ($item->city ?? '')) }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Kontakt / Konditionen</div>
                        <div class="oc-main">
                            <div class="oc-ttl" style="font-size:14px;">
                                {{ $kontaktText ?: 'Keine Kontaktdaten' }}
                            </div>

                            <div class="oc-mini">
                                @if($item->phone)<span>Telefon vorhanden</span>@endif
                                @if($item->phone && ($item->email || $item->mobile)) <span> • </span> @endif
                                @if($item->mobile)<span>Mobil vorhanden</span>@endif
                                @if(($item->phone || $item->mobile) && $item->email) <span> • </span> @endif
                                @if($item->email)<span>E-Mail vorhanden</span>@endif
                            </div>

                            @if(!is_null($item->cash_discount) || !empty($item->payment_terms))
                                <div class="oc-mini">
                                    @if(!is_null($item->cash_discount) && $item->cash_discount !== '')
                                        <span>Skonto: {{ number_format((float) $item->cash_discount, 2, ',', '.') }}%</span>
                                    @endif

                                    @if(!is_null($item->cash_discount) && $item->cash_discount !== '' && !empty($item->payment_terms))
                                        <span> • </span>
                                    @endif

                                    @if(!empty($item->payment_terms))
                                        <span>Zahlungsziel: {{ $item->payment_terms }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="oc-cell oc-responsive-col">
                        <div class="oc-cell-title">Status</div>

                        @if($status === 'inactive')
                            <span class="oc-status-pill red">Inaktiv</span>
                        @elseif($isActive)
                            <span class="oc-status-pill green">Aktiv</span>
                        @elseif($status === 'Unpublished' || empty($status))
                            <span class="oc-status-pill orange">Unveröffentlicht</span>
                        @else
                            <span class="oc-status-pill gray">{{ $status ?: '–' }}</span>
                        @endif
                    </div>

                    <div class="oc-actions-wrap">
                        @if($item->phone)
                            <a class="oc-btn-ic" href="tel:{{ $item->phone }}" title="Anrufen">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92V19a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 4.18 2 2 0 0 1 5 2h2.09a2 2 0 0 1 2 1.72l.3 2.11a2 2 0 0 1-.57 1.72l-1.27 1.27a16 16 0 0 0 6.91 6.91l1.27-1.27a2 2 0 0 1 1.72-.57l2.11.3A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </a>
                        @endif

                        @if($item->email)
                            <a class="oc-btn-ic" href="mailto:{{ $item->email }}" title="E-Mail senden">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16v16H4z"></path>
                                    <path d="M22 6l-10 7L2 6"></path>
                                </svg>
                            </a>
                        @endif

                        <a class="oc-btn-ic primary" href="{{ route('distributors.departments.index', $item->id) }}" title="Abteilungen">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92V19a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 4.18 2 2 0 0 1 5 2h2.09a2 2 0 0 1 2 1.72l.3 2.11a2 2 0 0 1-.57 1.72l-1.27 1.27a16 16 0 0 0 6.91 6.91l1.27-1.27a2 2 0 0 1 1.72-.57l2.11.3A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </a>

                        @if($status === 'Unpublished' || empty($status))
                            <a href="{{ url('/distributor_publish/'.$item->id) }}" class="oc-btn-ic success" title="Veröffentlichen">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ url('/distributor_unpublish/'.$item->id) }}" class="oc-btn-ic warning" title="Unveröffentlichen">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </a>
                        @endif

                        <button
                            type="button"
                            class="oc-btn-ic js-open-edit"
                            title="Bearbeiten"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-short_name="{{ $item->short_name }}"
                            data-account_number="{{ $item->account_number }}"
                            data-street="{{ $item->street }}"
                            data-postal_code="{{ $item->postal_code }}"
                            data-city="{{ $item->city }}"
                            data-phone="{{ $item->phone }}"
                            data-mobile="{{ $item->mobile }}"
                            data-email="{{ $item->email }}"
                            data-cash_discount="{{ $item->cash_discount }}"
                            data-payment_terms="{{ $item->payment_terms }}"
                            data-status="{{ $item->status }}"
                            data-image="{{ $item->image ? asset('images/distributor/'.$item->image) : '' }}"
                            data-update_url="{{ route('distributors.update', ['distributor' => $item->id]) }}"
                        >
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="oc-btn-ic danger js-open-delete"
                            title="Löschen"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-delete_url="{{ url('/distributor_destroy/'.$item->id) }}"
                        >
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="oc-empty">Keine Daten gefunden.</div>
        @endforelse
    </div>

    @if($data->hasPages())
        <div class="oc-pagination">
            {{ $data->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

<div class="oc-modal-backdrop" id="globalImageModal">
    <div class="oc-modal" style="max-width:700px;">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl" id="globalImageTitle">Logo-Vorschau</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('globalImageModal')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="oc-modal-b text-center">
            <img src="" alt="Logo" class="oc-image-large" id="globalImagePreview">
        </div>
    </div>
</div>

<div class="oc-modal-backdrop" id="globalDeleteModal">
    <div class="oc-modal" style="max-width:520px;">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Eintrag löschen</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('globalDeleteModal')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="oc-modal-b">
            <p style="margin-bottom:12px;">Möchtest du diesen Eintrag wirklich löschen?</p>

            <div style="padding:12px 14px; border:1px solid var(--border); border-radius:12px; background:#fafafa;">
                <div><strong>ID:</strong> <span id="deleteDistributorId">–</span></div>
                <div style="margin-top:6px;"><strong>Name:</strong> <span id="deleteDistributorName">–</span></div>
            </div>
        </div>

        <div class="oc-modal-f">
            <button type="button" class="oc-btn-soft" onclick="closeModal('globalDeleteModal')">Abbrechen</button>
            <a href="#" class="oc-btn danger" id="globalDeleteLink">Ja, löschen</a>
        </div>
    </div>
</div>

<div class="oc-modal-backdrop" id="globalEditModal">
    <div class="oc-modal">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Lieferant bearbeiten</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('globalEditModal')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="globalEditForm">
            @csrf
            @method('PUT')

            <div class="oc-modal-b">
                <div class="oc-form-grid">
                    <div class="oc-form-group" style="grid-column:1 / -1;">
                        <label class="oc-label">Firmen- / Lieferantenname</label>
                        <input type="text" id="edit_name" name="name" class="oc-input-form" required>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Kurzname</label>
                        <input type="text" id="edit_short_name" name="short_name" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Kontonummer</label>
                        <input type="text" id="edit_account_number" name="account_number" class="oc-input-form">
                    </div>

                    <div class="oc-form-group" style="grid-column:1 / -1;">
                        <label class="oc-label">Straße</label>
                        <input type="text" id="edit_street" name="street" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Postleitzahl</label>
                        <input type="text" id="edit_postal_code" name="postal_code" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Stadt</label>
                        <input type="text" id="edit_city" name="city" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Telefon</label>
                        <input type="text" id="edit_phone" name="phone" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Mobil</label>
                        <input type="text" id="edit_mobile" name="mobile" class="oc-input-form">
                    </div>

                    <div class="oc-form-group" style="grid-column:1 / -1;">
                        <label class="oc-label">E-Mail</label>
                        <input type="email" id="edit_email" name="email" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Skonto (%)</label>
                        <input type="number" step="0.01" min="0" max="100" id="edit_cash_discount" name="cash_discount" class="oc-input-form">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Zahlungsziel</label>
                        <input type="text" id="edit_payment_terms" name="payment_terms" class="oc-input-form" placeholder="z. B. 14 Tage netto">
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Status</label>
                        <select id="edit_status" name="status" class="oc-select" style="width:100%;">
                            <option value="Published">Veröffentlicht</option>
                            <option value="Unpublished">Unveröffentlicht</option>
                            <option value="active">Aktiv</option>
                            <option value="inactive">Inaktiv</option>
                        </select>
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Logo</label>
                        <input type="file" name="image" class="oc-input-form">
                        <div class="oc-help">Leer lassen, um das vorhandene Logo zu behalten.</div>
                        <img src="" alt="Logo" class="oc-thumb-preview" id="edit_logo_preview" style="display:none;">
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('globalEditModal')">Abbrechen</button>
                <button type="submit" class="oc-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>

<div class="oc-modal-backdrop" id="modalCreate">
    <div class="oc-modal">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">Neuen Lieferanten anlegen</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('modalCreate')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('distributors.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="oc-modal-b">
                <div class="oc-form-grid">
                    <div class="oc-form-group" style="grid-column:1 / -1;">
                        <label class="oc-label">Firmen- / Lieferantenname</label>
                        <input type="text" class="oc-input-form" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Kurzname</label>
                        <input type="text" class="oc-input-form" name="short_name" value="{{ old('short_name') }}">
                        @error('short_name')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Kontonummer</label>
                        <input type="text" class="oc-input-form" name="account_number" value="{{ old('account_number') }}">
                        @error('account_number')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group" style="grid-column:1 / -1;">
                        <label class="oc-label">Straße</label>
                        <input type="text" class="oc-input-form" name="street" value="{{ old('street') }}">
                        @error('street')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Postleitzahl</label>
                        <input type="text" class="oc-input-form" name="postal_code" value="{{ old('postal_code') }}">
                        @error('postal_code')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Stadt</label>
                        <input type="text" class="oc-input-form" name="city" value="{{ old('city') }}">
                        @error('city')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Telefon</label>
                        <input type="text" class="oc-input-form" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Mobil</label>
                        <input type="text" class="oc-input-form" name="mobile" value="{{ old('mobile') }}">
                        @error('mobile')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group" style="grid-column:1 / -1;">
                        <label class="oc-label">E-Mail</label>
                        <input type="email" class="oc-input-form" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Skonto (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="oc-input-form" name="cash_discount" value="{{ old('cash_discount') }}">
                        @error('cash_discount')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Zahlungsziel</label>
                        <input type="text" class="oc-input-form" name="payment_terms" value="{{ old('payment_terms') }}" placeholder="z. B. 14 Tage netto">
                        @error('payment_terms')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Status</label>
                        <select name="status" class="oc-select" style="width:100%;">
                            <option value="Unpublished" {{ old('status', 'Unpublished') === 'Unpublished' ? 'selected' : '' }}>Unveröffentlicht</option>
                            <option value="Published" {{ old('status') === 'Published' ? 'selected' : '' }}>Veröffentlicht</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktiv</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inaktiv</option>
                        </select>
                        @error('status')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="oc-form-group">
                        <label class="oc-label">Logo</label>
                        <input type="file" class="oc-input-form" name="image">
                        <div class="oc-help">Optionales Lieferantenlogo.</div>
                        @error('image')
                            <div class="oc-help" style="color:var(--danger);">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('modalCreate')">Abbrechen</button>
                <button class="oc-btn" type="submit">Anlegen</button>
            </div>
        </form>
    </div>
</div>

<div class="oc-modal-backdrop" id="modalImportCsv">
    <div class="oc-modal" style="max-width:560px;">
        <div class="oc-modal-h">
            <h3 class="oc-modal-ttl">CSV importieren</h3>
            <button class="oc-btn-ic" type="button" onclick="closeModal('modalImportCsv')">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('distributors.importCsv') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="oc-modal-b">
                <div class="oc-form-group">
                    <label class="oc-label">CSV-Datei</label>
                    <input type="file" name="csv_file" class="oc-input-form" accept=".csv,text/csv" required>
                    <div class="oc-help">
                        Spaltenüberschriften können z. B. Kurzname, Name, Straße, PLZ, Ort, Skonto oder Zahlungsziel enthalten.
                    </div>
                </div>
            </div>

            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('modalImportCsv')">Abbrechen</button>
                <button class="oc-btn success" type="submit">Import starten</button>
            </div>
        </form>
    </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
@push('scripts')
<script>
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
  }

  function toast(kind, title, msg) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;

    const icons = {
      ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
      warn: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>`,
      bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
    };

    const el = document.createElement('div');
    el.className = 'oc-toast';
    el.innerHTML = `
      <div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
      <div style="flex:1;">
        <p class="oc-toast-ttl">${title}</p>
        <p class="oc-toast-msg">${msg}</p>
      </div>
      <button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
    `;
    wrap.appendChild(el);
    setTimeout(() => { try { el.remove(); } catch (e) {} }, 4000);
  }

  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('oc-modal-backdrop')) {
      e.target.classList.remove('open');
      return;
    }

    const imageBtn = e.target.closest('.js-open-image');
    if (imageBtn) {
      document.getElementById('globalImageTitle').textContent = 'Logo-Vorschau — ' + (imageBtn.dataset.name || '');
      document.getElementById('globalImagePreview').src = imageBtn.dataset.logo || '';
      openModal('globalImageModal');
      return;
    }

    const deleteBtn = e.target.closest('.js-open-delete');
    if (deleteBtn) {
      document.getElementById('deleteDistributorId').textContent = deleteBtn.dataset.id || '–';
      document.getElementById('deleteDistributorName').textContent = deleteBtn.dataset.name || '–';
      document.getElementById('globalDeleteLink').href = deleteBtn.dataset.delete_url || '#';
      openModal('globalDeleteModal');
      return;
    }

    const editBtn = e.target.closest('.js-open-edit');
    if (editBtn) {
      document.getElementById('globalEditForm').action = editBtn.dataset.update_url || '';
      document.getElementById('edit_name').value = editBtn.dataset.name || '';
      document.getElementById('edit_short_name').value = editBtn.dataset.short_name || '';
      document.getElementById('edit_account_number').value = editBtn.dataset.account_number || '';
      document.getElementById('edit_street').value = editBtn.dataset.street || '';
      document.getElementById('edit_postal_code').value = editBtn.dataset.postal_code || '';
      document.getElementById('edit_city').value = editBtn.dataset.city || '';
      document.getElementById('edit_phone').value = editBtn.dataset.phone || '';
      document.getElementById('edit_mobile').value = editBtn.dataset.mobile || '';
      document.getElementById('edit_email').value = editBtn.dataset.email || '';
      document.getElementById('edit_cash_discount').value = editBtn.dataset.cash_discount || '';
      document.getElementById('edit_payment_terms').value = editBtn.dataset.payment_terms || '';
      document.getElementById('edit_status').value = editBtn.dataset.status || 'Unpublished';

      const preview = document.getElementById('edit_logo_preview');
      if (editBtn.dataset.image) {
        preview.src = editBtn.dataset.image;
        preview.style.display = 'block';
      } else {
        preview.src = '';
        preview.style.display = 'none';
      }

      openModal('globalEditModal');
    }
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
    }
  });

  @if(session('update_msg'))
    toast('ok', 'Aktualisiert', @json(session('update_msg')));
  @endif

  @if(session('save_msg'))
    toast('ok', 'Gespeichert', @json(session('save_msg')));
  @endif

  @if(session('delete_msg'))
    toast('bad', 'Gelöscht', @json(session('delete_msg')));
  @endif

  @if($errors->any())
    openModal('modalCreate');
  @endif
</script>
@endpush
@endonce