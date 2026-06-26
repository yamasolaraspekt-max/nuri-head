@extends('admin.layouts.app')
@section('title') Anfragevorschläge @stop

@section('style')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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

      body{background:var(--app-bg)!important;}

      .oc-wrap{
        font-family:Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color:var(--text-main);
      }

      .oc-header{margin-bottom:18px;}
      .oc-titlebar{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:12px;
        margin-bottom:16px;
        flex-wrap:wrap;
      }
      .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827;text-transform:uppercase;}
      .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px;max-width:760px;}
      .oc-breadcrumb{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-top:10px;font-size:13px;color:var(--text-muted);}
      .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
      .oc-breadcrumb a:hover{color:var(--text-main);}
      .oc-breadcrumb span.current{color:#111827;font-weight:800;}
      .oc-inline-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}

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
        justify-content:center;
        gap:8px;
        text-decoration:none;
        min-height:42px;
      }
      .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}
      .oc-btn:disabled{opacity:.55;cursor:not-allowed;}

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
        min-height:42px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
      }
      .oc-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}
      .oc-btn-soft.danger{color:var(--danger);background:var(--danger-light);border-color:rgba(239,68,68,.18);}
      .oc-btn-soft.danger:hover{border-color:rgba(239,68,68,.35);}
      .oc-btn-soft.primary{color:var(--primary);background:var(--primary-light);border-color:var(--primary-light);}
      .oc-btn-soft.primary:hover{border-color:var(--primary);}

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
      }
      .oc-btn-ic:hover{background:#f9fafb;color:var(--text-main);border-color:#d1d5db;text-decoration:none;}
      .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
      .oc-btn-ic.primary:hover{border-color:var(--primary)}
      .oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
      .oc-btn-ic.warning:hover{border-color:#f59e0b}
      .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
      .oc-btn-ic.success:hover{border-color:var(--success)}
      .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
      .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

      .oc-analytics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px;}
      @media(max-width:1200px){.oc-analytics{grid-template-columns:repeat(2,minmax(0,1fr));}}
      @media(max-width:700px){.oc-analytics{grid-template-columns:1fr;}}
      .oc-stat{background:var(--card-bg);border:1px solid var(--border);border-radius:16px;padding:16px;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:12px;min-height:92px;}
      .oc-stat-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;}
      .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
      .oc-stat-icon.published{background:var(--success-light);color:var(--success)}
      .oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706}
      .oc-stat-icon.type{background:var(--gray-light);color:var(--gray)}
      .oc-stat-meta{min-width:0;}
      .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
      .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
      .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

      .oc-toolbar{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;justify-content:space-between;margin-bottom:16px;box-shadow:var(--shadow-sm);}
      .oc-toolbar-left,.oc-toolbar-right{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;}
      .oc-toolbar-left{flex:1;}
      .oc-filter-block{display:flex;flex-direction:column;gap:6px;min-width:170px;}
      .oc-filter-block.search{flex:1;min-width:280px;}
      .oc-filter-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}

      .oc-input{background:#f9fafb;border:1px solid var(--border);border-radius:8px;padding:10px 12px 10px 36px;font-size:14px;outline:none;transition:var(--transition);min-width:240px;width:100%;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");background-repeat:no-repeat;background-position:10px center;background-size:16px;}
      .oc-input:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}
      .oc-select,.oc-input-form{width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border);background:#fff;font-size:14px;outline:none;transition:var(--transition);min-height:42px;}
      .oc-select:focus,.oc-input-form:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}

      .oc-card{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow-sm);overflow:visible;margin-bottom:16px;}
      .oc-card-h{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:16px;border-bottom:1px solid var(--border);background:#fff;border-radius:16px 16px 0 0;}
      .oc-card-title{font-size:16px;font-weight:900;color:#111827;margin:0;display:flex;align-items:center;gap:8px;}
      .oc-card-sub{font-size:13px;color:var(--text-muted);margin-top:4px;}
      .oc-card-b{padding:16px;}

      .oc-builder-table{width:100%;border-collapse:separate;border-spacing:0 10px;}
      .oc-builder-table thead th{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);font-weight:900;padding:0 10px 4px;}
      .oc-builder-table tbody tr{background:#fff;border-radius:14px;box-shadow:var(--shadow-sm);}
      .oc-builder-table tbody td{border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:10px;vertical-align:top;}
      .oc-builder-table tbody td:first-child{border-left:1px solid var(--border);border-radius:14px 0 0 14px;}
      .oc-builder-table tbody td:last-child{border-right:1px solid var(--border);border-radius:0 14px 14px 0;}
      @media(max-width:1100px){
        .oc-builder-table,.oc-builder-table thead,.oc-builder-table tbody,.oc-builder-table tr,.oc-builder-table td{display:block;width:100%;}
        .oc-builder-table thead{display:none;}
        .oc-builder-table tbody tr{margin-bottom:12px;display:block;border:1px solid var(--border);}
        .oc-builder-table tbody td{border:none!important;border-radius:0!important;}
        .oc-builder-table tbody td:before{content:attr(data-label);display:block;font-size:11px;font-weight:900;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px;letter-spacing:.06em;}
      }

      .pp-position-stack{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;min-width:260px;}
      @media(max-width:900px){.pp-position-stack{grid-template-columns:1fr;}}
      .pp-position-mini-label{font-size:11px;color:var(--text-muted);font-weight:900;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;display:flex;align-items:center;gap:6px;}

      .select2-container{width:100%!important;}
      .select2-container .select2-selection--single{height:42px!important;border-radius:8px!important;border-color:var(--border)!important;display:flex!important;align-items:center!important;}
      .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:40px!important;color:#111827!important;}
      .select2-container--default .select2-selection--single .select2-selection__arrow{height:40px!important;}
      .select2-container .select2-selection--multiple{min-height:42px!important;border-radius:8px!important;border-color:var(--border)!important;padding:3px 6px!important;}
      .select2-container--default.select2-container--focus .select2-selection--multiple,
      .select2-container--default.select2-container--focus .select2-selection--single{border-color:var(--primary)!important;box-shadow:0 0 0 3px var(--primary-light)!important;}

      .pos-option{display:flex;align-items:center;justify-content:space-between;gap:.5rem;width:100%;}
      .pos-option-label{font-size:.85rem;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
      .pos-option-avatars{display:flex;align-items:center;gap:2px;}
      .pos-avatar{width:22px;height:22px;border-radius:999px;object-fit:cover;border:1px solid #e5e7eb;background:#fff;}
      .pos-avatar-more{font-size:.7rem;color:#6b7280;padding:0 4px;}

      .oc-product-list{display:flex;flex-direction:column;gap:14px;}
      .oc-product-group{border:1px solid var(--border);border-radius:16px;background:#fff;box-shadow:var(--shadow-sm);overflow:hidden;}
      .oc-product-header{width:100%;border:none;background:linear-gradient(180deg,#fff,#fbfcfd);padding:16px;display:flex;align-items:center;justify-content:space-between;gap:14px;cursor:pointer;text-align:left;transition:var(--transition);}
      .oc-product-header:hover{background:var(--primary-light);}
      .oc-product-title{display:flex;align-items:center;gap:12px;min-width:0;}
      .oc-product-icon{width:46px;height:46px;border-radius:14px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;flex:0 0 auto;}
      .oc-product-name{font-size:18px;font-weight:900;color:#111827;line-height:1.15;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
      .oc-product-meta{font-size:13px;color:var(--text-muted);margin-top:3px;}
      .oc-product-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;}
      .oc-count-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900;background:var(--gray-light);color:var(--gray);white-space:nowrap;}
      .oc-count-pill.green{background:var(--success-light);color:#047857;}
      .oc-chevron{transition:transform .2s ease;}
      .oc-product-group.is-collapsed .oc-chevron{transform:rotate(-90deg);}
      .oc-product-body{padding:0 16px 16px;display:block;}
      .oc-product-group.is-collapsed .oc-product-body{display:none;}

      .oc-service-group{border:1px solid var(--border);border-radius:14px;background:#fafafa;margin-top:12px;overflow:hidden;}
      .oc-service-header{width:100%;border:none;background:#fff;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:12px;cursor:pointer;text-align:left;}
      .oc-service-title{display:flex;align-items:center;gap:10px;min-width:0;}
      .oc-service-badge{width:34px;height:34px;border-radius:12px;background:var(--blue-light);color:var(--blue);display:flex;align-items:center;justify-content:center;flex:0 0 auto;}
      .oc-service-name{font-size:15px;font-weight:900;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
      .oc-service-meta{font-size:12px;color:var(--text-muted);margin-top:2px;}
      .oc-service-body{display:block;padding:0 12px 12px;}
      .oc-service-group.is-collapsed .oc-service-body{display:none;}
      .oc-service-group.is-collapsed .oc-chevron{transform:rotate(-90deg);}

      .oc-suggestion-head{display:grid;grid-template-columns:38px 70px 120px minmax(170px,.9fr) minmax(230px,1.5fr) minmax(230px,1.5fr) 138px;gap:12px;align-items:center;padding:12px 12px 6px;color:var(--text-muted);font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;}
      .oc-suggestion-row{display:grid;grid-template-columns:38px 70px 120px minmax(170px,.9fr) minmax(230px,1.5fr) minmax(230px,1.5fr) 138px;gap:12px;align-items:start;background:#fff;border:1px solid var(--border);border-radius:14px;padding:12px;margin-bottom:10px;transition:var(--transition);}
      .oc-suggestion-row:hover{border-color:var(--primary);box-shadow:var(--shadow);}
      @media(max-width:1280px){
        .oc-suggestion-head{display:none;}
        .oc-suggestion-row{grid-template-columns:1fr;}
        .oc-cell-title{display:block;}
        .oc-actions{justify-content:flex-start!important;}
      }
      .oc-cell{min-width:0;}
      .oc-cell-title{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;display:none;}
      .oc-id-badge{display:inline-flex;align-items:center;justify-content:center;min-width:54px;height:36px;padding:0 12px;border-radius:10px;background:var(--blue-light);color:var(--blue);font-size:13px;font-weight:900;}
      .oc-main{display:flex;flex-direction:column;min-width:0;}
      .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827;}
      .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
      .oc-status-pill{display:inline-flex;align-items:center;justify-content:center;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:900;white-space:nowrap;}
      .oc-status-pill.green{background:#ecfdf5;color:#047857;}
      .oc-status-pill.orange{background:#fffbeb;color:#b45309;}
      .oc-status-pill.blue{background:var(--blue-light);color:#2563eb;}
      .oc-status-pill.gray{background:var(--gray-light);color:var(--gray);}
      .oc-status-pill.purple{background:#f5f3ff;color:#7c3aed;}
      .oc-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap;}

      .pp-position-cell{display:flex;flex-direction:column;gap:8px;}
      .pp-position-card{border:1px solid var(--border);border-radius:12px;background:#fafafa;padding:9px 10px;display:flex;align-items:center;justify-content:space-between;gap:10px;}
      .pp-position-info{min-width:0;}
      .pp-position-name{font-size:13px;font-weight:900;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
      .pp-position-count{font-size:11px;color:var(--text-muted);margin-top:2px;}
      .pp-avatar-stack{display:flex;align-items:center;justify-content:flex-end;min-width:70px;}
      .pp-avatar{width:30px;height:30px;border-radius:999px;border:2px solid #fff;background:#e5e7eb;color:#374151;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;position:relative;overflow:hidden;margin-left:-8px;box-shadow:0 1px 3px rgba(0,0,0,.10);}
      .pp-avatar:first-child{margin-left:0;}
      .pp-avatar img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;background:#fff;}
      .pp-avatar-more{width:30px;height:30px;border-radius:999px;border:2px solid #fff;background:#111827;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;margin-left:-8px;box-shadow:0 1px 3px rgba(0,0,0,.10);}
      .pp-empty-inline{font-size:12px;color:var(--text-muted);background:#fff;border:1px dashed var(--border);border-radius:10px;padding:8px 10px;}

      .oc-empty{text-align:center;padding:42px;color:var(--text-muted);background:#fff;border:1px dashed var(--border);border-radius:16px;margin:16px;}
      .oc-empty.slim{padding:18px;margin:0;}

      .oc-modal-backdrop{position:fixed;inset:0;z-index:1200;background:rgba(17,24,39,.55);backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:opacity .22s ease;display:flex;align-items:center;justify-content:center;padding:18px;}
      .oc-modal-backdrop.open{opacity:1;pointer-events:auto;}
      .oc-modal{width:100%;max-width:840px;background:#fff;border:1px solid rgba(229,231,235,.9);border-radius:16px;box-shadow:var(--shadow);transform:translateY(12px) scale(.985);transition:transform .22s ease;overflow:hidden;}
      .oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1);}
      .oc-modal-h{display:flex;gap:12px;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border);background:#fafafa;}
      .oc-modal-ttl{font-weight:900;font-size:16px;line-height:1.2;margin:0;color:#111827;}
      .oc-modal-b{padding:20px 18px;max-height:72vh;overflow-y:auto;}
      .oc-modal-f{padding:14px 18px;border-top:1px solid var(--border);background:#fafafa;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;}
      .oc-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;}
      @media(max-width:760px){.oc-form-grid{grid-template-columns:1fr;}}
      .oc-form-group{margin-bottom:16px;}
      .oc-label{display:block;font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:6px;}
      .oc-help{font-size:12px;color:var(--text-muted);margin-top:6px;}

      .form-check-inline{display:flex;align-items:center;gap:.35rem;margin:0;}
    </style>
@endsection

@section('content')
<div class="app-content">
  <div class="content-wrapper">
    <div class="content-body">
      <div class="oc-wrap">
        <div class="oc-header">
          <div class="oc-titlebar">
            <div>
              <div class="oc-title">Anfragevorschläge</div>
              <div class="oc-sub">Produktbasierte Vorschläge mit Leistungen, Stufen, Abteilungen und direkt sichtbaren Mitarbeitern pro Position.</div>
              <div class="oc-breadcrumb">
                <a href="{{ url('/employee_dashboard') }}">Home</a>
                <span>›</span>
                <span class="current">Anfragevorschläge</span>
              </div>
            </div>

            <div class="oc-inline-actions">
              <button id="addRow" type="button" class="oc-btn">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Neue Zeile
              </button>
              <button id="saveRows" type="button" class="oc-btn-soft primary">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/></svg>
                Speichern
              </button>
            </div>
          </div>
        </div>

        <div class="oc-analytics">
          <div class="oc-stat">
            <div class="oc-stat-icon total"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg></div>
            <div class="oc-stat-meta"><div class="oc-stat-label">Gesamt</div><div id="statTotal" class="oc-stat-value">0</div><div class="oc-stat-sub">Vorschläge insgesamt</div></div>
          </div>
          <div class="oc-stat">
            <div class="oc-stat-icon published"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M7 12h10M10 17h4"/></svg></div>
            <div class="oc-stat-meta"><div class="oc-stat-label">Produkte</div><div id="statProducts" class="oc-stat-value">0</div><div class="oc-stat-sub">Gruppen nach Produkt</div></div>
          </div>
          <div class="oc-stat">
            <div class="oc-stat-icon unpublished"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg></div>
            <div class="oc-stat-meta"><div class="oc-stat-label">Leistungen</div><div id="statServices" class="oc-stat-value">0</div><div class="oc-stat-sub">Gefundene Leistungsgruppen</div></div>
          </div>
          <div class="oc-stat">
            <div class="oc-stat-icon type"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div class="oc-stat-meta"><div class="oc-stat-label">Mitarbeiter</div><div id="statEmployees" class="oc-stat-value">0</div><div class="oc-stat-sub">In sichtbaren Zeilen</div></div>
          </div>
        </div>

        <div class="oc-card">
          <div class="oc-card-h">
            <div>
              <h3 class="oc-card-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                Vorschläge erstellen
              </h3>
              <div class="oc-card-sub">Neue Zeilen werden wie bisher gespeichert, aber die gespeicherte Ansicht ist jetzt produktbasiert gruppiert.</div>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="copyInquiryToStages">
              <label class="form-check-label small text-muted" for="copyInquiryToStages">Anfrage auf andere Stufen kopieren</label>
            </div>
          </div>

          <div class="oc-card-b">
            <div class="table-responsive">
              <table class="oc-builder-table" id="dynamicTable">
                <thead>
                  <tr>
                    <th style="width:12rem;">Stufe</th>
                    <th style="width:16rem;">Produkt</th>
                    <th style="width:16rem;">Leistung</th>
                    <th style="width:16rem;">Abteilung</th>
                    <th>Positionen</th>
                    <th style="width:70px;text-align:center;">Aktion</th>
                  </tr>
                </thead>
                <tbody id="rowContainer">
                  <tr>
                    <td colspan="6">
                      <div class="oc-empty slim">Noch keine Eingaben. Klicken Sie auf <strong>Neue Zeile</strong>, um einen Vorschlag zu erstellen.</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <form class="oc-toolbar" onsubmit="return false;">
          <div class="oc-toolbar-left">
            <div class="oc-filter-block search">
              <label class="oc-filter-label">Suche</label>
              <input id="savedRecordsSearch" type="text" class="oc-input" placeholder="Suche nach Produkt, Leistung, Stufe, Abteilung, Position oder Mitarbeiter">
            </div>
            <div class="oc-filter-block">
              <label class="oc-filter-label">Produkt</label>
              <select id="savedFilterProduct" class="oc-select"><option value="">Alle Produkte</option></select>
            </div>
            <div class="oc-filter-block">
              <label class="oc-filter-label">Leistung</label>
              <select id="savedFilterService" class="oc-select"><option value="">Alle Leistungen</option></select>
            </div>
            <div class="oc-filter-block">
              <label class="oc-filter-label">Stufe</label>
              <select id="savedFilterStage" class="oc-select"><option value="">Alle Stufen</option></select>
            </div>
            <div class="oc-filter-block">
              <label class="oc-filter-label">Abteilung</label>
              <select id="savedFilterDepartment" class="oc-select"><option value="">Alle Abteilungen</option></select>
            </div>
            <div class="oc-filter-block">
              <label class="oc-filter-label">Sortierung</label>
              <select id="savedSortBy" class="oc-select">
                <option value="service">Leistung</option>
                <option value="stage">Stufe</option>
                <option value="department">Abteilung</option>
                <option value="id">Neueste</option>
              </select>
            </div>
          </div>
          <div class="oc-toolbar-right">
            <button id="savedSortDirToggle" type="button" class="oc-btn-soft" title="Sortierreihenfolge"><span data-dir="asc">↑</span></button>
            <button id="expandAllGroups" type="button" class="oc-btn-soft">Alle öffnen</button>
            <button id="collapseAllGroups" type="button" class="oc-btn-soft">Alle schließen</button>
            <button id="btnBulkDuplicate" type="button" class="oc-btn-soft primary" disabled>Duplizieren</button>
            <button id="btnBulkDelete" type="button" class="oc-btn-soft danger" disabled>Löschen</button>
          </div>
          <div class="oc-toolbar-left" style="width:100%;margin-top:4px;">
            <div class="oc-filter-block" style="min-width:280px;flex:1;">
              <label class="oc-filter-label">Position Intern</label>
              <select id="savedFilterPosInternal" class="oc-select" multiple></select>
            </div>
            <div class="oc-filter-block" style="min-width:280px;flex:1;">
              <label class="oc-filter-label">Position Extern</label>
              <select id="savedFilterPosExternal" class="oc-select" multiple></select>
            </div>
          </div>
        </form>

        <div id="savedRecordsTable" class="oc-product-list">
          <div class="oc-empty">Keine Datensätze gefunden.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="editModal" class="oc-modal-backdrop" aria-hidden="true">
  <div class="oc-modal">
    <form id="editForm">
      <div class="oc-modal-h">
        <h3 class="oc-modal-ttl">Vorschlag bearbeiten</h3>
        <button type="button" class="oc-btn-ic" data-close-edit title="Schließen">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="oc-modal-b">
        <input type="hidden" id="editId">
        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Stufe</label>
            <select id="editStage" class="oc-select" style="width:100%;">
              <option value="">Stufe wählen</option>
              <option value="inquiry">Anfrage</option>
              <option value="lead">Lead</option>
              <option value="offer">Angebot</option>
              <option value="deals">Abschluss</option>
              <option value="project">Projekt</option>
              <option value="ticket">Ticket</option>
            </select>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Produkt</label>
            <select id="editProduct" class="oc-select" style="width:100%;"></select>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Leistung</label>
            <select id="editService" class="oc-select" style="width:100%;"></select>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Abteilung</label>
            <select id="editDepartment" class="oc-select" style="width:100%;"></select>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Intern / Innendienst</label>
            <select id="editPositionsInternal" class="oc-select" multiple style="width:100%;"></select>
          </div>
          <div class="oc-form-group">
            <label class="oc-label">Extern / Außendienst</label>
            <select id="editPositionsExternal" class="oc-select" multiple style="width:100%;"></select>
          </div>
        </div>
      </div>
      <div class="oc-modal-f">
        <button type="submit" class="oc-btn">Aktualisieren</button>
        <button type="button" class="oc-btn-soft" data-close-edit>Abbrechen</button>
      </div>
    </form>
  </div>
</div>
@stop

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function () {
        let articleGroups = [];
        let departments = [];
        let allSavedRecords = [];
        let positionEmployeeMap = {};

        const CSRF_TOKEN = '{{ csrf_token() }}';
        const STAGES_COPY_TARGETS = ['lead', 'offer', 'deals', 'project', 'ticket'];
        const EMPTY_ROW_HTML = `
            <tr>
                <td colspan="6">
                    <div class="oc-empty slim">Noch keine Eingaben. Klicken Sie auf <strong>Neue Zeile</strong>, um einen Vorschlag zu erstellen.</div>
                </td>
            </tr>
        `;

        init();

        function init() {
            initStaticSelect2();
            showFlashMessages();
            bindGlobalEvents();
            fetchData();
            fetchSavedRecords();
        }

        function initStaticSelect2() {
            $('#savedFilterProduct, #savedFilterService, #savedFilterStage, #savedFilterDepartment, #savedSortBy').select2({
                width: '100%',
                minimumResultsForSearch: 8
            });

            $('#savedFilterPosInternal, #savedFilterPosExternal').select2({
                width: '100%',
                placeholder: 'Alle Positionen',
                allowClear: true
            });
        }

        function showFlashMessages() {
            @if(Session::has('update_msg'))
                if (window.toastr) toastr.success("{{ session('update_msg') }}");
            @endif

            @if(Session::has('save_msg'))
                if (window.toastr) toastr.success("{{ session('save_msg') }}");
            @endif

            @if(Session::has('delete_msg'))
                if (window.toastr) toastr.error("{{ session('delete_msg') }}");
            @endif
        }

        function bindGlobalEvents() {
            $('#addRow').on('click', addRow);
            $('#saveRows').on('click', saveRows);
            $('#copyInquiryToStages').on('change', handleCopyInquiryToggle);

            $(document).on('input', '#savedRecordsSearch', applySavedFiltersAndSort);
            $('#savedFilterProduct, #savedFilterService, #savedFilterStage, #savedFilterDepartment').on('change', applySavedFiltersAndSort);
            $('#savedFilterPosInternal, #savedFilterPosExternal').on('change', applySavedFiltersAndSort);
            $('#savedSortBy').on('change', applySavedFiltersAndSort);

            $('#savedSortDirToggle').on('click', function () {
                const $span = $(this).find('span');
                const current = $span.data('dir') || 'asc';
                const next = current === 'asc' ? 'desc' : 'asc';
                $span.data('dir', next).text(next === 'asc' ? '↑' : '↓');
                applySavedFiltersAndSort();
            });

            $('#expandAllGroups').on('click', function () {
                $('.oc-product-group, .oc-service-group').removeClass('is-collapsed');
            });

            $('#collapseAllGroups').on('click', function () {
                $('.oc-product-group, .oc-service-group').addClass('is-collapsed');
            });

            $('#btnBulkDuplicate').on('click', handleBulkDuplicate);
            $('#btnBulkDelete').on('click', handleBulkDelete);
            $('#editForm').on('submit', handleEditFormSubmit);

            $(document).on('click', '.js-product-toggle', function () {
                $(this).closest('.oc-product-group').toggleClass('is-collapsed');
            });

            $(document).on('click', '.js-service-toggle', function () {
                $(this).closest('.oc-service-group').toggleClass('is-collapsed');
            });

            $(document).on('change', '.savedSelectAll', function () {
                const checked = $(this).is(':checked');
                $('.savedRowCheckbox').prop('checked', checked);
                updateBulkActionState();
            });

            $(document).on('change', '.savedRowCheckbox', function () {
                const $all = $('.savedRowCheckbox');
                const checkedCount = $all.filter(':checked').length;
                $('.savedSelectAll').prop('checked', checkedCount === $all.length && $all.length > 0);
                updateBulkActionState();
            });

            $(document).on('click', '.editRecord', openEditRecord);
            $(document).on('click', '.deleteRecord', deleteRecord);

            $('[data-close-edit]').on('click', closeEditModal);
            $('#editModal').on('click', function (event) {
                if (event.target === this) closeEditModal();
            });
        }

        function fetchData() {
            $.get('/product-position/article-groups', function (data) {
                articleGroups = data || [];
            });

            $.get('/product-position/departments', function (data) {
                departments = data || [];
            });
        }

        function fetchSavedRecords() {
            $.get('/product-position/records', function (records) {
                allSavedRecords = records || [];
                buildSavedFilterOptions(allSavedRecords);
                applySavedFiltersAndSort();
            });
        }

        function ensureEmptyRowIfNeeded() {
            if (!$('#rowContainer tr').length) {
                $('#rowContainer').append(EMPTY_ROW_HTML);
            }
        }

        function clearEmptyRow() {
            $('#rowContainer').find('.oc-empty').closest('tr').remove();
        }

        function addRow() {
            clearEmptyRow();

            const rowHtml = `
                <tr>
                    <td data-label="Stufe">
                        <select class="oc-select stage-select">
                            <option value="">Stufe wählen</option>
                            <option value="inquiry">Anfrage</option>
                            <option value="lead">Lead</option>
                            <option value="offer">Angebot</option>
                            <option value="deals">Abschluss</option>
                            <option value="project">Projekt</option>
                            <option value="ticket">Ticket</option>
                        </select>
                    </td>
                    <td data-label="Produkt">
                        <select class="oc-select produkt-select">
                            <option value="">Produkt wählen</option>
                        </select>
                    </td>
                    <td data-label="Leistung">
                        <select class="oc-select service-select">
                            <option value="">Leistung wählen</option>
                        </select>
                    </td>
                    <td data-label="Abteilung">
                        <select class="oc-select department-select">
                            <option value="">Abteilung wählen</option>
                        </select>
                    </td>
                    <td data-label="Positionen">
                        <div class="pp-position-stack">
                            <div>
                                <div class="pp-position-mini-label">Intern / Innendienst</div>
                                <select class="oc-select position-internal-select" multiple="multiple" style="width:100%"></select>
                            </div>
                            <div>
                                <div class="pp-position-mini-label">Extern / Außendienst</div>
                                <select class="oc-select position-external-select" multiple="multiple" style="width:100%"></select>
                            </div>
                        </div>
                    </td>
                    <td data-label="Aktion" class="text-center">
                        <button type="button" class="oc-btn-ic danger removeRow" title="Zeile entfernen">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                </tr>
            `;

            $('#rowContainer').append(rowHtml);

            const $newRow = $('#rowContainer tr:last');
            populateProductOptions($newRow.find('.produkt-select'));
            populateDepartmentOptions($newRow.find('.department-select'));

            $newRow.find('.stage-select, .produkt-select, .service-select, .department-select').select2({
                width: '100%',
                minimumResultsForSearch: 8
            });

            initPositionSelect2($newRow.find('.position-internal-select'));
            initPositionSelect2($newRow.find('.position-external-select'));
            bindRowEvents($newRow);

            return $newRow;
        }

        function populateProductOptions($select) {
            $select.empty().append('<option value="">Produkt wählen</option>');
            articleGroups.forEach(group => {
                $select.append(`<option value="${escapeAttr(group.id)}">${escapeHtml(group.article_group)}</option>`);
            });
        }

        function populateDepartmentOptions($select) {
            $select.empty().append('<option value="">Abteilung wählen</option>');
            departments.forEach(dept => {
                $select.append(`<option value="${escapeAttr(dept.id)}">${escapeHtml(dept.department_name)}</option>`);
            });
        }

        function bindRowEvents($row) {
            $row.find('.removeRow').on('click', function () {
                const $tr = $(this).closest('tr');
                const isClone = $tr.attr('data-auto-inquiry-clone') === '1';
                $tr.remove();
                ensureEmptyRowIfNeeded();

                if (isClone && !$('#rowContainer tr[data-auto-inquiry-clone="1"]').length) {
                    $('#copyInquiryToStages').prop('checked', false);
                }
            });

            $row.find('.produkt-select').on('change', function () {
                const productId = $(this).val();
                const $serviceSelect = $(this).closest('tr').find('.service-select');
                $serviceSelect.empty().append('<option value="">Leistung wählen</option>');

                if (!productId) {
                    $serviceSelect.trigger('change.select2');
                    return;
                }

                $.get(`/product-position/services/${productId}`, function (data) {
                    (data || []).forEach(service => {
                        $serviceSelect.append(`<option value="${escapeAttr(service.id)}">${escapeHtml(service.label)}</option>`);
                    });
                    $serviceSelect.trigger('change.select2');
                });
            });

            $row.find('.department-select').on('change', function () {
                const departmentId = $(this).val();
                const $currentRow = $(this).closest('tr');
                const $posInt = $currentRow.find('.position-internal-select');
                const $posExt = $currentRow.find('.position-external-select');

                $posInt.empty();
                $posExt.empty();

                if (!departmentId) {
                    $posInt.trigger('change');
                    $posExt.trigger('change');
                    return;
                }

                $.get(`/product-position/positions/${departmentId}`, function (data) {
                    (data || []).forEach(pos => {
                        const optionHtml = `<option value="${escapeAttr(pos.id)}" data-department-id="${escapeAttr(departmentId)}">${escapeHtml(pos.position)}</option>`;
                        $posInt.append(optionHtml);
                        $posExt.append(optionHtml);
                    });

                    initPositionSelect2($posInt);
                    initPositionSelect2($posExt);
                    buildPositionEmployeeMapForDepartment(departmentId, data || []);
                });
            });
        }

        function handleCopyInquiryToggle() {
            const checked = $('#copyInquiryToStages').is(':checked');

            if (!checked) {
                removeInquiryClones();
                return;
            }

            const $rows = $('#rowContainer tr').filter(function () {
                return !$(this).find('.oc-empty').length;
            });

            if (!$rows.length) {
                $('#copyInquiryToStages').prop('checked', false);
                Swal.fire({icon:'info',title:'Keine Anfrage-Zeile',text:'Legen Sie zuerst eine Anfrage-Zeile an und füllen Sie alle Felder aus.',timer:2200,showConfirmButton:false});
                return;
            }

            const $master = $rows.first();

            if (!isInquiryRowComplete($master)) {
                $('#copyInquiryToStages').prop('checked', false);
                Swal.fire({icon:'info',title:'Anfrage unvollständig',text:'Die erste Zeile muss Stufe "Anfrage" haben und Produkt, Leistung, Abteilung und mindestens eine Position enthalten.',timer:2600,showConfirmButton:false});
                return;
            }

            Swal.fire({
                icon: 'question',
                title: 'Auf andere Stufen kopieren?',
                html: 'Die Anfrage-Zeile wird für <strong>Lead, Angebot, Abschluss, Projekt und Ticket</strong> kopiert. Wenn die Leistung <strong>Komplett</strong> ist, werden zusätzlich alle Einzel-Leistungen erzeugt.',
                showCancelButton: true,
                confirmButtonText: 'Ja, kopieren',
                cancelButtonText: 'Abbrechen'
            }).then(result => {
                if (!result.isConfirmed) {
                    $('#copyInquiryToStages').prop('checked', false);
                    return;
                }

                removeInquiryClones();
                cloneInquiryRowToOtherStages($master);
            });
        }

        function isInquiryRowComplete($row) {
            const stage = $row.find('.stage-select').val();
            const produkt = $row.find('.produkt-select').val();
            const service = $row.find('.service-select').val();
            const department = $row.find('.department-select').val();
            const internal = $row.find('.position-internal-select').val() || [];
            const external = $row.find('.position-external-select').val() || [];

            if (stage !== 'inquiry') return false;
            if (!produkt || !service || !department) return false;
            if (!internal.length && !external.length) return false;

            return true;
        }

        function removeInquiryClones() {
            $('#rowContainer tr[data-auto-inquiry-clone="1"]').remove();
            ensureEmptyRowIfNeeded();
        }

        function getSelectedServiceLabel($serviceSelect) {
            return $.trim($serviceSelect.find('option:selected').text() || '').toLowerCase();
        }

        function getServicesToClone($sourceRow) {
            const $serviceSelect = $sourceRow.find('.service-select');
            const selectedServiceId = $serviceSelect.val();
            const selectedLabel = getSelectedServiceLabel($serviceSelect);
            const serviceOptions = [];

            $serviceSelect.find('option').each(function () {
                const value = $(this).val();
                const label = $.trim($(this).text());
                if (!value) return;
                serviceOptions.push({id: value, label: label, normalized: label.toLowerCase()});
            });

            if (selectedLabel === 'komplett') return serviceOptions;

            const found = serviceOptions.find(service => String(service.id) === String(selectedServiceId));
            return found ? [found] : [];
        }

        function createClonedRow(config) {
            const {stageVal, produktVal, departmentVal, serviceToSet, masterServiceHtml, masterInternalHtml, masterExternalHtml, internalVals, externalVals} = config;
            const $row = addRow();
            $row.attr('data-auto-inquiry-clone', '1');

            const $stageSelect = $row.find('.stage-select');
            const $produktSelect = $row.find('.produkt-select');
            const $serviceSelect = $row.find('.service-select');
            const $deptSelect = $row.find('.department-select');
            const $posInt = $row.find('.position-internal-select');
            const $posExt = $row.find('.position-external-select');

            $stageSelect.val(stageVal).trigger('change.select2');
            if (produktVal) $produktSelect.val(produktVal).trigger('change.select2');
            if (departmentVal) $deptSelect.val(departmentVal).trigger('change.select2');

            $serviceSelect.html(masterServiceHtml);
            if (serviceToSet && serviceToSet.id) $serviceSelect.val(serviceToSet.id).trigger('change.select2');

            $posInt.html(masterInternalHtml);
            $posExt.html(masterExternalHtml);

            initPositionSelect2($posInt);
            initPositionSelect2($posExt);

            if (internalVals.length) $posInt.val(internalVals).trigger('change.select2');
            if (externalVals.length) $posExt.val(externalVals).trigger('change.select2');

            return $row;
        }

        function cloneInquiryRowToOtherStages($sourceRow) {
            const produktVal = $sourceRow.find('.produkt-select').val();
            const departmentVal = $sourceRow.find('.department-select').val();
            const internalVals = $sourceRow.find('.position-internal-select').val() || [];
            const externalVals = $sourceRow.find('.position-external-select').val() || [];
            const $masterService = $sourceRow.find('.service-select');
            const $masterPosInt = $sourceRow.find('.position-internal-select');
            const $masterPosExt = $sourceRow.find('.position-external-select');
            const servicesToClone = getServicesToClone($sourceRow);

            if (!servicesToClone.length) {
                $('#copyInquiryToStages').prop('checked', false);
                Swal.fire({icon:'info',title:'Keine Leistung gefunden',text:'Für die Anfrage-Zeile konnte keine gültige Leistung ermittelt werden.',timer:2200,showConfirmButton:false});
                return;
            }

            STAGES_COPY_TARGETS.forEach(stageVal => {
                servicesToClone.forEach(serviceToSet => {
                    createClonedRow({
                        stageVal,
                        produktVal,
                        departmentVal,
                        serviceToSet,
                        masterServiceHtml: $masterService.html(),
                        masterInternalHtml: $masterPosInt.html(),
                        masterExternalHtml: $masterPosExt.html(),
                        internalVals,
                        externalVals
                    });
                });
            });
        }

        function getPositionEmployeesForOption(optionElement) {
            if (!optionElement) return [];
            const $opt = $(optionElement);
            const deptId = $opt.data('department-id');
            const posName = $.trim($opt.text());
            if (!deptId || !posName) return [];
            const departmentMap = positionEmployeeMap[deptId] || {};
            return departmentMap[posName] || [];
        }

        function positionTemplateResult(state) {
            if (!state.id) return state.text;
            const employees = getPositionEmployeesForOption(state.element);
            const $container = $('<span class="pos-option"></span>');
            const $label = $('<span class="pos-option-label"></span>').text(state.text);
            const $avatarsWrap = $('<span class="pos-option-avatars"></span>');

            employees.slice(0, 3).forEach(emp => {
                const $img = $('<img class="pos-avatar" />').attr('src', employeeImageUrl(emp)).attr('alt', employeeName(emp));
                $avatarsWrap.append($img);
            });

            if (employees.length > 3) $avatarsWrap.append($('<span class="pos-avatar-more"></span>').text('+' + (employees.length - 3)));
            $container.append($label).append($avatarsWrap);
            return $container;
        }

        function positionTemplateSelection(state) {
            if (!state.id) return state.text;
            const employees = getPositionEmployeesForOption(state.element);
            const $container = $('<span class="pos-option"></span>');
            const $label = $('<span class="pos-option-label"></span>').text(state.text);
            const $avatarsWrap = $('<span class="pos-option-avatars"></span>');

            employees.slice(0, 2).forEach(emp => {
                const $img = $('<img class="pos-avatar" />').attr('src', employeeImageUrl(emp)).attr('alt', employeeName(emp));
                $avatarsWrap.append($img);
            });

            if (employees.length > 2) $avatarsWrap.append($('<span class="pos-avatar-more"></span>').text('+' + (employees.length - 2)));
            $container.append($label).append($avatarsWrap);
            return $container;
        }

        function initPositionSelect2($select) {
            if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
            const $modalParent = $select.closest('.oc-modal-backdrop');

            $select.select2({
                width: '100%',
                templateResult: positionTemplateResult,
                templateSelection: positionTemplateSelection,
                dropdownParent: $modalParent.length ? $modalParent : $(document.body),
                escapeMarkup: markup => markup
            });
        }

        function buildPositionEmployeeMapForDepartment(departmentId, positionsArray) {
            const ids = (positionsArray || []).map(position => position.id);
            if (!ids.length) return;

            $.post('/product-position/employees', {
                position_ids: { internal: ids, external: [] },
                _token: CSRF_TOKEN
            }, function (employees) {
                const mapByName = {};
                (employees || []).forEach(emp => {
                    (emp.positions || []).forEach(posName => {
                        const normalizedName = $.trim(posName);
                        if (!mapByName[normalizedName]) mapByName[normalizedName] = [];
                        mapByName[normalizedName].push(emp);
                    });
                });
                positionEmployeeMap[departmentId] = mapByName;
            });
        }

        function collectRowsFromBuilder() {
            const rows = [];
            $('#rowContainer tr').each(function () {
                const $row = $(this);
                if ($row.find('.oc-empty').length) return;

                const stage = $row.find('.stage-select').val();
                const produkt = $row.find('.produkt-select').val();
                const service = $row.find('.service-select').val();
                const department = $row.find('.department-select').val();
                const internal = $row.find('.position-internal-select').val() || [];
                const external = $row.find('.position-external-select').val() || [];

                if (stage && produkt && service && department && (internal.length || external.length)) {
                    rows.push({stage, produkt, service, department, positions: {internal, external}});
                }
            });
            return rows;
        }

        function sameArray(a, b) {
            const arrA = a || [];
            const arrB = b || [];
            if (arrA.length !== arrB.length) return false;
            const sortedA = [...arrA].sort();
            const sortedB = [...arrB].sort();
            for (let i = 0; i < sortedA.length; i++) {
                if (String(sortedA[i]) !== String(sortedB[i])) return false;
            }
            return true;
        }

        function sameRecordKey(existing, row) {
            if ((existing.stage || '') !== row.stage) return false;
            if (String(existing.article_group_id || '') !== String(row.produkt || '')) return false;
            if (String(existing.service_id || '') !== String(row.service || '')) return false;
            if (String(existing.department_id || '') !== String(row.department || '')) return false;

            const existingPositions = existing.position_ids || {};
            return sameArray(existingPositions.internal || [], row.positions.internal || [])
                && sameArray(existingPositions.external || [], row.positions.external || []);
        }

        function findConflictingRows(rows) {
            const conflicts = [];
            (rows || []).forEach((row, idx) => {
                const matches = (allSavedRecords || []).filter(record => sameRecordKey(record, row));
                if (matches.length) conflicts.push({rowIndex: idx, row, existing: matches});
            });
            return conflicts;
        }

        function saveRows() {
            const rows = collectRowsFromBuilder();
            if (!rows.length) {
                Swal.fire({icon:'info',title:'Unvollständige Eingabe',text:'Bitte füllen Sie mindestens eine vollständige Zeile aus.',timer:1800,showConfirmButton:false});
                return;
            }

            const conflicts = findConflictingRows(rows);
            if (!conflicts.length) {
                doSaveRows(rows);
                return;
            }

            const htmlList = conflicts.map(conflict => {
                const record = conflict.existing[0];
                return `<li><strong>${escapeHtml(stageLabelDe(conflict.row.stage))}</strong> / ${escapeHtml(productName(record))} / ${escapeHtml(serviceName(record))} / ${escapeHtml(departmentName(record))}</li>`;
            }).join('');

            Swal.fire({
                icon: 'warning',
                title: 'Doppelte Vorschläge gefunden',
                html: `<p>Für einige Kombinationen existieren bereits Vorschläge:</p><ul style="text-align:left;">${htmlList}</ul><p>Wie möchten Sie fortfahren?</p>`,
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Überschreiben',
                denyButtonText: 'Ignorieren',
                cancelButtonText: 'Abbrechen'
            }).then(result => {
                if (result.isConfirmed) {
                    const idsToDelete = [];
                    conflicts.forEach(conflict => conflict.existing.forEach(record => idsToDelete.push(record.id)));
                    const uniqueIds = [...new Set(idsToDelete)];
                    $.ajax({
                        url: '/product-position/bulk-delete',
                        method: 'POST',
                        data: { ids: uniqueIds },
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                        success: function () { doSaveRows(rows); }
                    });
                } else if (result.isDenied) {
                    const conflictIndexes = new Set(conflicts.map(conflict => conflict.rowIndex));
                    const filteredRows = rows.filter((row, idx) => !conflictIndexes.has(idx));
                    if (!filteredRows.length) {
                        Swal.fire({icon:'info',title:'Keine neuen Vorschläge',text:'Alle Zeilen sind bereits vorhanden.',timer:1800,showConfirmButton:false});
                        return;
                    }
                    doSaveRows(filteredRows);
                }
            });
        }

        function doSaveRows(rows) {
            if (!rows.length) return;
            $.ajax({
                url: '/product-position/save',
                method: 'POST',
                data: { rows },
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                success: function (response) {
                    Swal.fire({icon:'success',title:'Aktualisiert',text:response.message,timer:1500,showConfirmButton:false});
                    $('#rowContainer').empty().append(EMPTY_ROW_HTML);
                    $('#copyInquiryToStages').prop('checked', false);
                    fetchSavedRecords();
                }
            });
        }

        function buildSavedFilterOptions(records) {
            const products = new Set();
            const services = new Set();
            const departmentsSet = new Set();
            const stages = new Set();
            const posIntSet = new Set();
            const posExtSet = new Set();

            (records || []).forEach(record => {
                if (productName(record) !== '-') products.add(productName(record));
                if (serviceName(record) !== '-') services.add(serviceName(record));
                if (departmentName(record) !== '-') departmentsSet.add(departmentName(record));
                if (record.stage) stages.add(stageLabelDe(record.stage));
                (record.position_internal || []).forEach(name => posIntSet.add(name));
                (record.position_external || []).forEach(name => posExtSet.add(name));
            });

            fillSingleFilter('#savedFilterProduct', products, 'Alle Produkte');
            fillSingleFilter('#savedFilterService', services, 'Alle Leistungen');
            fillSingleFilter('#savedFilterDepartment', departmentsSet, 'Alle Abteilungen');
            fillSingleFilter('#savedFilterStage', stages, 'Alle Stufen');
            fillMultiFilter('#savedFilterPosInternal', posIntSet);
            fillMultiFilter('#savedFilterPosExternal', posExtSet);
        }

        function fillSingleFilter(selector, valuesSet, placeholder) {
            const $select = $(selector);
            const currentValue = $select.val();
            $select.empty().append(`<option value="">${escapeHtml(placeholder)}</option>`);
            Array.from(valuesSet).sort().forEach(value => $select.append(`<option value="${escapeAttr(value)}">${escapeHtml(value)}</option>`));
            if (currentValue) $select.val(currentValue);
            if ($select.hasClass('select2-hidden-accessible')) $select.trigger('change.select2');
        }

        function fillMultiFilter(selector, valuesSet) {
            const $select = $(selector);
            const selectedValues = $select.val() || [];
            $select.empty();
            Array.from(valuesSet).sort().forEach(value => {
                const selected = selectedValues.includes(value) ? 'selected' : '';
                $select.append(`<option value="${escapeAttr(value)}" ${selected}>${escapeHtml(value)}</option>`);
            });
            if ($select.hasClass('select2-hidden-accessible')) $select.trigger('change.select2');
        }

        function applySavedFiltersAndSort() {
            let filtered = [...(allSavedRecords || [])];
            const search = ($('#savedRecordsSearch').val() || '').toLowerCase();
            const filterProduct = $('#savedFilterProduct').val() || '';
            const filterService = $('#savedFilterService').val() || '';
            const filterStage = $('#savedFilterStage').val() || '';
            const filterDepartment = $('#savedFilterDepartment').val() || '';
            const posInternalValues = $('#savedFilterPosInternal').val() || [];
            const posExternalValues = $('#savedFilterPosExternal').val() || [];
            const sortBy = $('#savedSortBy').val() || 'service';
            const sortDir = $('#savedSortDirToggle span').data('dir') || 'asc';

            filtered = filtered.filter(record => {
                const posIntNames = record.position_internal || [];
                const posExtNames = record.position_external || [];
                const employeeNames = [...(record.employees_internal || []), ...(record.employees_external || [])].map(employeeName).join(' ');
                const haystack = `${record.id} ${record.stage || ''} ${stageLabelDe(record.stage)} ${productName(record)} ${serviceName(record)} ${departmentName(record)} ${posIntNames.join(' ')} ${posExtNames.join(' ')} ${employeeNames}`.toLowerCase();

                if (search && !haystack.includes(search)) return false;
                if (filterProduct && productName(record) !== filterProduct) return false;
                if (filterService && serviceName(record) !== filterService) return false;
                if (filterStage && stageLabelDe(record.stage) !== filterStage) return false;
                if (filterDepartment && departmentName(record) !== filterDepartment) return false;

                if (posInternalValues.length && !posInternalValues.every(value => posIntNames.includes(value))) return false;
                if (posExternalValues.length && !posExternalValues.every(value => posExtNames.includes(value))) return false;

                return true;
            });

            filtered.sort((a, b) => {
                const getSortValue = record => {
                    if (sortBy === 'stage') return stageLabelDe(record.stage);
                    if (sortBy === 'department') return departmentName(record);
                    if (sortBy === 'id') return Number(record.id || 0);
                    return serviceName(record);
                };

                const aValue = getSortValue(a);
                const bValue = getSortValue(b);

                if (sortBy === 'id') return sortDir === 'asc' ? aValue - bValue : bValue - aValue;

                const aText = String(aValue).toLowerCase();
                const bText = String(bValue).toLowerCase();
                if (aText < bText) return sortDir === 'asc' ? -1 : 1;
                if (aText > bText) return sortDir === 'asc' ? 1 : -1;
                return 0;
            });

            updateStats(filtered);
            renderSavedRecords(filtered);
        }

        function updateStats(records) {
            const productSet = new Set();
            const serviceSet = new Set();
            const employeeSet = new Set();

            (records || []).forEach(record => {
                if (productName(record) !== '-') productSet.add(productName(record));
                if (serviceName(record) !== '-') serviceSet.add(serviceName(record));
                [...(record.employees_internal || []), ...(record.employees_external || [])].forEach(emp => employeeSet.add(emp.id || employeeName(emp)));
            });

            $('#statTotal').text(records.length);
            $('#statProducts').text(productSet.size);
            $('#statServices').text(serviceSet.size);
            $('#statEmployees').text(employeeSet.size);
        }

        function renderSavedRecords(records) {
            if (!records.length) {
                $('#savedRecordsTable').html('<div class="oc-empty">Keine Datensätze gefunden.</div>');
                updateBulkActionState();
                return;
            }

            const groupedProducts = groupBy(records, productName);
            let html = '';

            Object.keys(groupedProducts).sort().forEach((product, productIndex) => {
                const productRecords = groupedProducts[product];
                const services = groupBy(productRecords, serviceName);
                const productEmployees = uniqueEmployees(productRecords);

                html += `
                    <div class="oc-product-group" data-product-index="${productIndex}">
                        <button type="button" class="oc-product-header js-product-toggle">
                            <div class="oc-product-title">
                                <span class="oc-product-icon">${iconSvg('box')}</span>
                                <span class="oc-main">
                                    <span class="oc-product-name">${escapeHtml(product)}</span>
                                    <span class="oc-product-meta">${Object.keys(services).length} Leistungen • ${productRecords.length} Vorschläge</span>
                                </span>
                            </div>
                            <div class="oc-product-right">
                                ${renderAvatarStack(productEmployees, 5)}
                                <span class="oc-count-pill green">${productRecords.length} Zeilen</span>
                                <span class="oc-chevron">${iconSvg('chevron-down')}</span>
                            </div>
                        </button>
                        <div class="oc-product-body">
                `;

                Object.keys(services).sort().forEach((service, serviceIndex) => {
                    const serviceRecords = services[service];
                    const stagesInService = [...new Set(serviceRecords.map(r => stageLabelDe(r.stage)))].filter(Boolean);

                    html += `
                        <div class="oc-service-group" data-service-index="${productIndex}-${serviceIndex}">
                            <button type="button" class="oc-service-header js-service-toggle">
                                <div class="oc-service-title">
                                    <span class="oc-service-badge">${iconSvg('layers')}</span>
                                    <span class="oc-main">
                                        <span class="oc-service-name">${escapeHtml(service)}</span>
                                        <span class="oc-service-meta">Stufen: ${escapeHtml(stagesInService.join(', ') || '-')}</span>
                                    </span>
                                </div>
                                <div class="oc-product-right">
                                    <span class="oc-count-pill">${serviceRecords.length} Vorschläge</span>
                                    <span class="oc-chevron">${iconSvg('chevron-down')}</span>
                                </div>
                            </button>
                            <div class="oc-service-body">
                                <div class="oc-suggestion-head">
                                    <div><input type="checkbox" class="savedSelectAll"></div>
                                    <div>ID</div>
                                    <div>Stufe</div>
                                    <div>Abteilung</div>
                                    <div>Intern</div>
                                    <div>Extern</div>
                                    <div style="text-align:right;">Aktionen</div>
                                </div>
                    `;

                    serviceRecords.forEach(record => {
                        html += renderSuggestionRow(record);
                    });

                    html += `</div></div>`;
                });

                html += `</div></div>`;
            });

            $('#savedRecordsTable').html(html);
            updateBulkActionState();
        }

        function renderSuggestionRow(record) {
            return `
                <div class="oc-suggestion-row" data-record-id="${escapeAttr(record.id)}">
                    <div class="oc-cell">
                        <div class="oc-cell-title">Auswahl</div>
                        <input type="checkbox" class="savedRowCheckbox" data-id="${escapeAttr(record.id)}">
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">ID</div>
                        <span class="oc-id-badge">#${escapeHtml(record.id)}</span>
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Stufe</div>
                        <span class="oc-status-pill ${stageClass(record.stage)}">${escapeHtml(stageLabelDe(record.stage))}</span>
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Abteilung</div>
                        <div class="oc-main">
                            <div class="oc-ttl">${escapeHtml(departmentName(record))}</div>
                            <div class="oc-subt">${escapeHtml(productName(record))} • ${escapeHtml(serviceName(record))}</div>
                        </div>
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Intern</div>
                        ${renderPositionItems(record.position_internal_items, record.position_internal, record.employees_internal)}
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Extern</div>
                        ${renderPositionItems(record.position_external_items, record.position_external, record.employees_external)}
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Aktionen</div>
                        <div class="oc-actions">
                            <button type="button" class="oc-btn-ic primary editRecord" title="Bearbeiten" data-id="${escapeAttr(record.id)}">${iconSvg('edit')}</button>
                            <button type="button" class="oc-btn-ic danger deleteRecord" title="Löschen" data-id="${escapeAttr(record.id)}">${iconSvg('trash')}</button>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPositionItems(positionItems, fallbackNames, fallbackEmployees) {
            let items = Array.isArray(positionItems) ? positionItems : [];

            if (!items.length && Array.isArray(fallbackNames) && fallbackNames.length) {
                items = fallbackNames.map(name => ({name, employees: fallbackEmployees || []}));
            }

            if (!items.length) return '<div class="pp-empty-inline">Keine Positionen</div>';

            return `<div class="pp-position-cell">${items.map(item => {
                const employees = item.employees || [];
                return `
                    <div class="pp-position-card">
                        <div class="pp-position-info">
                            <div class="pp-position-name">${escapeHtml(item.name || '-')}</div>
                            <div class="pp-position-count">${employees.length} Mitarbeiter</div>
                        </div>
                        ${renderAvatarStack(employees, 4)}
                    </div>
                `;
            }).join('')}</div>`;
        }

        function renderAvatarStack(employees, limit) {
            const list = uniqueEmployeeList(employees || []);
            if (!list.length) return '<span class="pp-empty-inline">—</span>';
            const visible = list.slice(0, limit);
            let html = '<div class="pp-avatar-stack">';
            visible.forEach(emp => {
                html += `<span class="pp-avatar" title="${escapeAttr(employeeName(emp))}"><span>${escapeHtml(employeeInitials(emp))}</span>${emp.image ? `<img src="${escapeAttr(employeeImageUrl(emp))}" alt="${escapeAttr(employeeName(emp))}" onerror="this.remove()">` : ''}</span>`;
            });
            if (list.length > limit) html += `<span class="pp-avatar-more">+${list.length - limit}</span>`;
            html += '</div>';
            return html;
        }

        function handleBulkDuplicate() {
            const ids = getSelectedSavedIds();
            if (!ids.length) return;

            Swal.fire({icon:'question',title:'Duplizieren',text:'Ausgewählte Vorschläge duplizieren?',showCancelButton:true,confirmButtonText:'Ja, duplizieren',cancelButtonText:'Abbrechen'}).then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '/product-position/bulk-duplicate',
                    method: 'POST',
                    data: { ids },
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    success: function (response) {
                        Swal.fire({icon:'success',title:'Dupliziert',text:response.message,timer:1500,showConfirmButton:false});
                        fetchSavedRecords();
                    }
                });
            });
        }

        function handleBulkDelete() {
            const ids = getSelectedSavedIds();
            if (!ids.length) return;

            Swal.fire({icon:'warning',title:'Mehrfach löschen',text:'Ausgewählte Vorschläge wirklich löschen?',showCancelButton:true,confirmButtonText:'Ja, löschen',cancelButtonText:'Abbrechen',confirmButtonColor:'#d33',cancelButtonColor:'#6c757d'}).then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: '/product-position/bulk-delete',
                    method: 'POST',
                    data: { ids },
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    success: function (response) {
                        Swal.fire({icon:'success',title:'Gelöscht',text:response.message,timer:1500,showConfirmButton:false});
                        fetchSavedRecords();
                    },
                    error: function () {
                        Swal.fire({icon:'error',title:'Fehler',text:'Die Datensätze konnten nicht gelöscht werden.',timer:1800,showConfirmButton:false});
                    }
                });
            });
        }

        function getSelectedSavedIds() {
            return $('.savedRowCheckbox:checked').map(function () { return $(this).data('id'); }).get();
        }

        function updateBulkActionState() {
            const anyChecked = $('.savedRowCheckbox:checked').length > 0;
            $('#btnBulkDuplicate, #btnBulkDelete').prop('disabled', !anyChecked);
        }

        function openEditRecord() {
            const recordId = $(this).data('id');
            const record = (allSavedRecords || []).find(item => String(item.id) === String(recordId));
            if (!record) return;

            $('#editId').val(record.id);
            openEditModal();

            $('#editStage').val(record.stage);

            $('#editProduct').empty().append('<option value="">Produkt wählen</option>');
            articleGroups.forEach(group => $('#editProduct').append(`<option value="${escapeAttr(group.id)}">${escapeHtml(group.article_group)}</option>`));
            $('#editProduct').val(record.article_group_id);

            $('#editDepartment').empty().append('<option value="">Abteilung wählen</option>');
            departments.forEach(dept => $('#editDepartment').append(`<option value="${escapeAttr(dept.id)}">${escapeHtml(dept.department_name)}</option>`));
            $('#editDepartment').val(record.department_id);

            initPlainSelect2($('#editStage, #editProduct, #editService, #editDepartment'));

            $('#editPositionsInternal').empty();
            $('#editPositionsExternal').empty();

            if (record.article_group_id) {
                $.get(`/product-position/services/${record.article_group_id}`, function (data) {
                    $('#editService').empty().append('<option value="">Leistung wählen</option>');
                    (data || []).forEach(service => $('#editService').append(`<option value="${escapeAttr(service.id)}">${escapeHtml(service.label)}</option>`));
                    $('#editService').val(record.service_id).trigger('change.select2');
                });
            } else {
                $('#editService').empty().append('<option value="">Leistung wählen</option>').trigger('change.select2');
            }

            if (record.department_id) {
                $.get(`/product-position/positions/${record.department_id}`, function (data) {
                    (data || []).forEach(position => {
                        const optionHtml = `<option value="${escapeAttr(position.id)}" data-department-id="${escapeAttr(record.department_id)}">${escapeHtml(position.position)}</option>`;
                        $('#editPositionsInternal').append(optionHtml);
                        $('#editPositionsExternal').append(optionHtml);
                    });

                    initPositionSelect2($('#editPositionsInternal'));
                    initPositionSelect2($('#editPositionsExternal'));
                    buildPositionEmployeeMapForDepartment(record.department_id, data || []);

                    const posIds = record.position_ids || {};
                    $('#editPositionsInternal').val(posIds.internal || []).trigger('change.select2');
                    $('#editPositionsExternal').val(posIds.external || []).trigger('change.select2');
                });
            } else {
                initPositionSelect2($('#editPositionsInternal'));
                initPositionSelect2($('#editPositionsExternal'));
            }
        }

        function initPlainSelect2($selects) {
            $selects.each(function () {
                const $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
                $select.select2({width:'100%', dropdownParent: $('#editModal'), minimumResultsForSearch: 8});
            });
        }

        function openEditModal() {
            $('#editModal').addClass('open').attr('aria-hidden', 'false');
        }

        function closeEditModal() {
            $('#editModal').removeClass('open').attr('aria-hidden', 'true');
        }

        function handleEditFormSubmit(e) {
            e.preventDefault();
            const id = $('#editId').val();
            const payload = {
                stage: $('#editStage').val(),
                produkt: $('#editProduct').val(),
                service: $('#editService').val(),
                department: $('#editDepartment').val(),
                positions: {
                    internal: $('#editPositionsInternal').val() || [],
                    external: $('#editPositionsExternal').val() || []
                }
            };

            const $button = $(this).find('button[type=submit]');
            $button.prop('disabled', true).text('Aktualisieren…');

            $.ajax({
                url: `/product-position/update/${id}`,
                method: 'POST',
                data: payload,
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                success: function (response) {
                    Swal.fire({icon:'success',title:'Aktualisiert',text:response.message,timer:1500,showConfirmButton:false});
                    closeEditModal();
                    fetchSavedRecords();
                },
                complete: function () {
                    $button.prop('disabled', false).text('Aktualisieren');
                }
            });
        }

        function deleteRecord() {
            const id = $(this).data('id');
            Swal.fire({title:'Sind Sie sicher?',text:'Dieser Vorgang kann nicht rückgängig gemacht werden.',icon:'warning',showCancelButton:true,confirmButtonText:'Ja, löschen',cancelButtonText:'Abbrechen',confirmButtonColor:'#d33',cancelButtonColor:'#6c757d'}).then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/product-position/delete/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    success: function (response) {
                        Swal.fire({icon:'success',title:'Gelöscht',text:response.message,timer:1500,showConfirmButton:false});
                        fetchSavedRecords();
                    },
                    error: function () {
                        Swal.fire({icon:'error',title:'Fehler',text:'Der Datensatz konnte nicht gelöscht werden.',timer:1800,showConfirmButton:false});
                    }
                });
            });
        }

        function productName(record) {
            return record.article_group && record.article_group.article_group ? record.article_group.article_group : '-';
        }

        function departmentName(record) {
            return record.department && record.department.department_name ? record.department.department_name : '-';
        }

        function serviceName(record) {
            return record.service ? translateService(record.service.phase_section) : '-';
        }

        function translateService(value) {
            const map = {complete:'Komplett', montage:'Montage', product:'Produkt', plan:'Planung', maintenance:'Wartung', repair:'Reparatur', others:'Others'};
            return map[String(value || '').toLowerCase()] || value || '-';
        }

        function stageLabelDe(value) {
            const map = {inquiry:'Anfrage', lead:'Lead', offer:'Angebot', deals:'Abschluss', project:'Projekt', ticket:'Ticket'};
            return map[String(value || '').toLowerCase()] || value || '-';
        }

        function stageClass(value) {
            const map = {inquiry:'blue', lead:'gray', offer:'orange', deals:'green', project:'purple', ticket:'gray'};
            return map[String(value || '').toLowerCase()] || 'gray';
        }

        function groupBy(records, callback) {
            return (records || []).reduce((groups, record) => {
                const key = callback(record) || '-';
                if (!groups[key]) groups[key] = [];
                groups[key].push(record);
                return groups;
            }, {});
        }

        function uniqueEmployees(records) {
            const map = {};
            (records || []).forEach(record => {
                [...(record.employees_internal || []), ...(record.employees_external || [])].forEach(emp => {
                    map[emp.id || employeeName(emp)] = emp;
                });
            });
            return Object.values(map);
        }

        function uniqueEmployeeList(employees) {
            const map = {};
            (employees || []).forEach(emp => {
                map[emp.id || employeeName(emp)] = emp;
            });
            return Object.values(map);
        }

        function employeeName(emp) {
            return $.trim(`${emp?.name || ''} ${emp?.lastname || ''}`) || 'Mitarbeiter';
        }

        function employeeInitials(emp) {
            const first = (emp?.name || '').charAt(0);
            const last = (emp?.lastname || '').charAt(0);
            return (first + last || 'MA').toUpperCase();
        }

        function employeeImageUrl(emp) {
            return '/images/employee/' + encodeURIComponent(emp?.image || 'default.png');
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>'"]/g, function (char) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char];
            });
        }

        function escapeAttr(value) {
            return escapeHtml(value);
        }

        function iconSvg(name) {
            const icons = {
                box: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg>',
                layers: '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
                edit: '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
                trash: '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>',
                'chevron-down': '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>'
            };
            return icons[name] || '';
        }
    });
    </script>
@endsection
