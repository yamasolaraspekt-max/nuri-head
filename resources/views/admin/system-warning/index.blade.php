@extends('admin.layouts.app')
@section('title', 'Systemhinweis')

@php
$histories = collect($histories ?? []);

$totalHistoryCount = $histories->count();
$enabledCount = $histories->where('action', 'enabled')->count();
$disabledCount = $histories->where('action', 'disabled')->count();
$updatedCount = $histories->where('action', 'updated')->count();

$typeLabels = [
  'development' => 'Entwicklung',
  'uploading' => 'Upload läuft',
  'fixing' => 'Fehlerbehebung',
  'maintenance' => 'Wartung',
];

$themeLabels = [
  'amber' => 'Amber / Warnung',
  'blue' => 'Blau / Information',
  'red' => 'Rot / Kritisch',
  'green' => 'Grün / Erfolg',
  'purple' => 'Lila / Entwicklung',
];

$actionLabels = [
  'enabled' => 'Aktiviert',
  'disabled' => 'Deaktiviert',
  'updated' => 'Aktualisiert',
];

$currentTypeLabel = $typeLabels[$warning->type] ?? $warning->type;
@endphp

@once
@push('style')
<style>
  :root {
    --app-bg:#f3f4f6;
    --card-bg:#ffffff;
    --text-main:#1f2937;
    --text-muted:#6b7280;
    --border:#e5e7eb;
    --primary:var(--sa-accent);
    --primary-hover:var(--sa-accent-hover);
    --primary-light:var(--sa-accent-light);
    --blue:#74b2d4;
    --blue-light:#eff6ff;
    --success:#10b981;
    --success-light:#ecfdf5;
    --warning:#f59e0b;
    --warning-light:#fffbeb;
    --danger:#ef4444;
    --danger-hover:#dc2626;
    --danger-light:#fef2f2;
    --purple:#8b5cf6;
    --purple-light:#f5f3ff;
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
  .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px;max-width:760px;line-height:1.55}
  .oc-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
  }
  .oc-breadcrumb a{color:var(--text-muted);text-decoration:none;font-weight:700;}
  .oc-breadcrumb a:hover{color:var(--text-main);}
  .oc-breadcrumb span.current{color:#111827;font-weight:800;}

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
  }
  .oc-btn:hover{background:var(--primary-hover);color:#fff;text-decoration:none;}

  .oc-btn-dark{
    background:#111827;
    color:#fff;
    border:none;
    padding:11px 18px;
    border-radius:10px;
    font-weight:900;
    cursor:pointer;
    transition:var(--transition);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    text-decoration:none;
    width:100%;
  }
  .oc-btn-dark:hover{background:#020617;color:#fff;text-decoration:none;}

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
    display:inline-flex;
    align-items:center;
    gap:8px;
  }
  .oc-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));} }
  @media(max-width:700px){ .oc-analytics{grid-template-columns:1fr;} }

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
  .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
  .oc-stat-icon.active{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.inactive{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.type{background:var(--purple-light);color:var(--purple)}

  .oc-stat-meta{min-width:0}
  .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
  .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
    margin-bottom:18px;
  }

  .oc-hero{
    background:linear-gradient(135deg,#111827,#1f2937,#0f172a);
    color:#fff;
    padding:24px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:22px;
    flex-wrap:wrap;
  }

  .oc-hero-title{
    font-size:22px;
    font-weight:900;
    letter-spacing:-.025em;
    margin:12px 0 0 0;
  }

  .oc-hero-sub{
    color:#d1d5db;
    font-size:14px;
    line-height:1.6;
    max-width:760px;
    margin-top:8px;
  }

  .oc-live-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    background:rgba(255,255,255,.1);
    color:#fff;
    padding:7px 12px;
    font-size:12px;
    font-weight:900;
    border:1px solid rgba(255,255,255,.14);
  }

  .oc-live-dot{
    width:9px;
    height:9px;
    border-radius:999px;
    background:#94a3b8;
  }
  .oc-live-dot.on{
    background:#34d399;
    box-shadow:0 0 0 6px rgba(52,211,153,.15);
  }

  .oc-hero-illu{
    width:124px;
    height:104px;
    border-radius:22px;
    background:rgba(255,255,255,.09);
    border:1px solid rgba(255,255,255,.12);
    display:flex;
    align-items:center;
    justify-content:center;
  }

  .oc-main-grid{
    display:grid;
    grid-template-columns:minmax(0,1.7fr) minmax(320px,.8fr);
    gap:18px;
    padding:18px;
  }
  @media(max-width:1100px){ .oc-main-grid{grid-template-columns:1fr;} }

  .oc-panel{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:18px;
    box-shadow:var(--shadow-sm);
  }

  .oc-panel-soft{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:14px;
    padding:16px;
  }

  .oc-section-title{
    font-size:15px;
    font-weight:900;
    color:#111827;
    margin:0 0 14px 0;
    display:flex;
    align-items:center;
    gap:8px;
  }

  .oc-form-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:16px;
  }
  @media(max-width:760px){ .oc-form-grid{grid-template-columns:1fr;} }

  .oc-form-group{margin-bottom:16px;}
  .oc-form-group:last-child{margin-bottom:0;}

  .oc-label{
    display:block;
    font-size:13px;
    font-weight:800;
    color:var(--text-main);
    margin-bottom:7px;
  }

  .oc-input-form,
  .oc-select,
  .oc-textarea{
    width:100%;
    padding:11px 12px;
    border-radius:10px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
    color:#111827;
  }

  .oc-textarea{
    min-height:150px;
    resize:vertical;
    line-height:1.6;
  }

  .oc-input-form:focus,
  .oc-select:focus,
  .oc-textarea:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-help{
    font-size:12px;
    color:var(--text-muted);
    margin-top:6px;
    line-height:1.45;
  }

  .oc-switch-card{
    border:1px solid var(--border);
    background:#fff;
    border-radius:16px;
    padding:16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
  }

  .oc-switch-title{
    font-size:14px;
    font-weight:900;
    color:#111827;
  }

  .oc-switch-sub{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
    line-height:1.4;
  }

  .oc-switch{
    position:relative;
    display:inline-flex;
    align-items:center;
    cursor:pointer;
    flex:0 0 auto;
  }

  .oc-switch input{
    position:absolute;
    opacity:0;
    pointer-events:none;
  }

  .oc-switch-track{
    width:58px;
    height:30px;
    border-radius:999px;
    background:#d1d5db;
    transition:var(--transition);
    position:relative;
  }

  .oc-switch-thumb{
    position:absolute;
    width:24px;
    height:24px;
    left:3px;
    top:3px;
    border-radius:999px;
    background:#fff;
    box-shadow:0 2px 8px rgba(15,23,42,.2);
    transition:var(--transition);
  }

  .oc-switch input:checked + .oc-switch-track{
    background:var(--success);
  }

  .oc-switch input:checked + .oc-switch-track .oc-switch-thumb{
    transform:translateX(28px);
  }

  .oc-check-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    border:1px solid var(--border);
    background:#fff;
    border-radius:14px;
    padding:13px 14px;
  }

  .oc-check-row + .oc-check-row{
    margin-top:10px;
  }

  .oc-check-row span{
    font-size:13px;
    font-weight:800;
    color:#374151;
  }

  .oc-check-row input{
    width:18px;
    height:18px;
    accent-color:#93c21c;
  }

  .oc-preview{
    border:1px solid var(--border);
    background:linear-gradient(135deg,#f8fafc,#ffffff);
    border-radius:16px;
    padding:16px;
  }

  .oc-preview-card{
    border:1px solid rgba(229,231,235,.95);
    border-radius:18px;
    overflow:hidden;
    background:#fff;
    box-shadow:0 15px 30px -24px rgba(15,23,42,.35);
  }

  .oc-preview-visual{
    min-height:140px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:var(--preview-soft,#fff7ed);
  }

  .oc-preview-body{
    padding:16px;
  }

  .oc-preview-badge{
    display:inline-flex;
    align-items:center;
    gap:7px;
    background:var(--preview-soft,#fff7ed);
    color:var(--preview-dark,#92400e);
    border-radius:999px;
    padding:6px 10px;
    font-size:11px;
    font-weight:900;
  }

  .oc-preview-badge:before{
    content:"";
    width:7px;
    height:7px;
    border-radius:999px;
    background:var(--preview-main,#f59e0b);
  }

  .oc-preview-title{
    margin:12px 0 0;
    font-size:18px;
    font-weight:950;
    color:#111827;
    line-height:1.2;
  }

  .oc-preview-message{
    margin:8px 0 0;
    color:#64748b;
    font-size:13px;
    line-height:1.55;
    white-space:pre-line;
  }

  .oc-preview-btn{
    margin-top:14px;
    display:inline-flex;
    border:none;
    background:var(--preview-main,#f59e0b);
    color:#111827;
    border-radius:12px;
    padding:9px 12px;
    font-size:12px;
    font-weight:900;
  }

  .oc-theme-amber{--preview-main:#f59e0b;--preview-soft:#fff7ed;--preview-dark:#92400e;}
  .oc-theme-blue{--preview-main:#38bdf8;--preview-soft:#eff6ff;--preview-dark:#075985;}
  .oc-theme-red{--preview-main:#fb7185;--preview-soft:#fff1f2;--preview-dark:#9f1239;}
  .oc-theme-green{--preview-main:#34d399;--preview-soft:#ecfdf5;--preview-dark:#065f46;}
  .oc-theme-purple{--preview-main:#c084fc;--preview-soft:#faf5ff;--preview-dark:#6b21a8;}

  .oc-list-head{
    display:grid;
    grid-template-columns:170px 130px minmax(130px,.7fr) minmax(260px,1.4fr) 120px;
    gap:14px;
    align-items:center;
    padding:16px 16px 10px 16px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
  }
  @media(max-width:1100px){ .oc-list-head{display:none;} }

  .oc-list{
    display:flex;
    flex-direction:column;
    gap:12px;
    padding:0 0 16px 0;
  }

  .oc-item{
    background:var(--card-bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    transition:var(--transition);
    overflow:hidden;
    margin:0 16px;
  }

  .oc-item:hover{
    border-color:var(--primary);
    box-shadow:var(--shadow);
  }

  .oc-item-row{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:center;
    grid-template-columns:170px 130px minmax(130px,.7fr) minmax(260px,1.4fr) 120px;
  }
  @media(max-width:1100px){ .oc-item-row{grid-template-columns:1fr;} }

  .oc-cell{min-width:0}
  .oc-cell-title{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:4px;
    display:none;
  }
  @media(max-width:1100px){ .oc-cell-title{display:block;} }

  .oc-main{display:flex;flex-direction:column;min-width:0}
  .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
  .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

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
  .oc-status-pill.green{background:#ecfdf5;color:#047857;}
  .oc-status-pill.orange{background:#fffbeb;color:#b45309;}
  .oc-status-pill.gray{background:#f3f4f6;color:#4b5563;}
  .oc-status-pill.blue{background:#eff6ff;color:#1d4ed8;}
  .oc-status-pill.purple{background:#f5f3ff;color:#6d28d9;}

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
    margin:16px;
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

  .oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
  .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
  .oc-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
  .oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}
  .oc-toast-x{
    margin-left:auto;
    background:transparent;
    border:none;
    cursor:pointer;
    color:var(--text-muted);
  }

  .oc-result{
    display:none;
    border-radius:14px;
    padding:12px 14px;
    font-size:13px;
    font-weight:800;
    margin-top:12px;
  }

  .oc-result.ok{
    display:block;
    background:#ecfdf5;
    border:1px solid #bbf7d0;
    color:#047857;
  }

  .oc-result.bad{
    display:block;
    background:#fef2f2;
    border:1px solid #fecaca;
    color:#b91c1c;
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">SYSTEMHINWEIS</div> 

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <span class="current">Systemhinweis</span>
        </div>
      </div>

      <div>
        <span class="oc-status-pill {{ $warning->is_active ? 'green' : 'gray' }}">
          {{ $warning->is_active ? 'Aktiv' : 'Inaktiv' }}
        </span>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 12h18M3 6h18M3 18h18"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Verlauf</div>
        <div class="oc-stat-value">{{ $totalHistoryCount }}</div>
        <div class="oc-stat-sub">Änderungen insgesamt</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon active">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Aktiviert</div>
        <div class="oc-stat-value">{{ $enabledCount }}</div>
        <div class="oc-stat-sub">Einschaltungen</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon inactive">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Deaktiviert</div>
        <div class="oc-stat-value">{{ $disabledCount }}</div>
        <div class="oc-stat-sub">Ausschaltungen</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 3v18M3 12h18"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Aktueller Typ</div>
        <div class="oc-stat-value" style="font-size:18px;">{{ $currentTypeLabel }}</div>
        <div class="oc-stat-sub">{{ $warning->theme ?: 'amber' }}</div>
      </div>
    </div>
  </div>

  <div class="oc-card">
    <div class="oc-hero">
      <div>
        <div class="oc-live-pill">
          <span class="oc-live-dot {{ $warning->is_active ? 'on' : '' }}"></span>
          Realtime System Modal
        </div>

        <div class="oc-hero-title">Globaler Hinweis für alle Displays</div>

        <div class="oc-hero-sub">
          Aktivieren Sie den Schalter, um eine wichtige Meldung sofort in allen offenen Browser-Fenstern anzuzeigen.
          Perfekt für Uploads, Fixing, Wartung oder kurze Entwicklungsarbeiten.
        </div>
      </div>

      <div class="oc-hero-illu">
        <svg class="h-24 w-24" viewBox="0 0 220 180" width="98" height="88" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="30" y="30" width="160" height="105" rx="18" fill="white" fill-opacity=".12"/>
          <rect x="48" y="50" width="124" height="12" rx="6" fill="white" fill-opacity=".7"/>
          <rect x="48" y="75" width="90" height="9" rx="4.5" fill="white" fill-opacity=".35"/>
          <rect x="48" y="95" width="105" height="9" rx="4.5" fill="white" fill-opacity=".35"/>
          <circle cx="158" cy="118" r="31" fill="#f59e0b"/>
          <path d="M158 101v22" stroke="#111827" stroke-width="8" stroke-linecap="round"/>
          <circle cx="158" cy="135" r="4" fill="#111827"/>
        </svg>
      </div>
    </div>

    <form id="systemWarningForm">
      @csrf

      <div class="oc-main-grid">
        <div class="oc-panel">
          <h3 class="oc-section-title">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 5h16M4 12h16M4 19h10"/>
            </svg>
            Hinweis Inhalt
          </h3>

          <div class="oc-panel-soft">
            <div class="oc-form-group">
              <label class="oc-label">Titel</label>
              <input
                type="text"
                name="title"
                value="{{ $warning->title }}"
                class="oc-input-form"
                placeholder="z.B. Upload läuft gerade"
                required
              >
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Nachricht</label>
              <textarea
                name="message"
                rows="5"
                class="oc-textarea"
                placeholder="Schreiben Sie hier die Warnmeldung..."
              >{{ $warning->message }}</textarea>
              <div class="oc-help">
                Diese Nachricht wird im globalen Modal angezeigt.
              </div>
            </div>

            <div class="oc-form-grid">
              <div class="oc-form-group">
                <label class="oc-label">Typ</label>
                <select name="type" class="oc-select" id="warningTypeSelect">
                  <option value="development" @selected($warning->type === 'development')>Entwicklung</option>
                  <option value="uploading" @selected($warning->type === 'uploading')>Upload läuft</option>
                  <option value="fixing" @selected($warning->type === 'fixing')>Fehlerbehebung</option>
                  <option value="maintenance" @selected($warning->type === 'maintenance')>Wartung</option>
                </select>
              </div>

              <div class="oc-form-group">
                <label class="oc-label">Design-Farbe</label>
                <select name="theme" class="oc-select" id="warningThemeSelect">
                  <option value="amber" @selected($warning->theme === 'amber')>Amber / Warnung</option>
                  <option value="blue" @selected($warning->theme === 'blue')>Blau / Information</option>
                  <option value="red" @selected($warning->theme === 'red')>Rot / Kritisch</option>
                  <option value="green" @selected($warning->theme === 'green')>Grün / Erfolg</option>
                  <option value="purple" @selected($warning->theme === 'purple')>Lila / Entwicklung</option>
                </select>
              </div>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Button Text</label>
              <input
                type="text"
                name="button_text"
                value="{{ $warning->button_text }}"
                class="oc-input-form"
                placeholder="Verstanden"
              >
            </div>
          </div>
        </div>

        <div class="oc-panel">
          <h3 class="oc-section-title">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
            </svg>
            Aktivierung
          </h3>

          <div class="oc-switch-card">
            <div>
              <div class="oc-switch-title">Modal aktivieren</div>
              <div class="oc-switch-sub">
                Sofort auf allen Displays anzeigen.
              </div>
            </div>

            <label class="oc-switch">
              <input
                id="systemWarningSwitch"
                type="checkbox"
                name="is_active"
                value="1"
                @checked($warning->is_active)
              >
              <span class="oc-switch-track">
                <span class="oc-switch-thumb"></span>
              </span>
            </label>
          </div>

          <div style="height:14px;"></div>

          <div class="oc-panel-soft">
            <label class="oc-check-row">
              <span>Benutzer darf schließen</span>
              <input type="checkbox" name="can_close" value="1" @checked($warning->can_close)>
            </label>

            <label class="oc-check-row">
              <span>Backdrop anzeigen</span>
              <input type="checkbox" name="show_backdrop" value="1" @checked($warning->show_backdrop)>
            </label>
          </div>

          <div style="height:16px;"></div>

          <div class="oc-preview oc-theme-{{ $warning->theme ?: 'amber' }}" id="warningPreview">
            <div class="oc-preview-card">
              <div class="oc-preview-visual">
                <svg viewBox="0 0 260 210" width="170" height="140" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="35" y="45" width="190" height="120" rx="24" fill="white" stroke="rgba(15,23,42,.08)"/>
                  <rect x="58" y="70" width="105" height="13" rx="6.5" fill="rgba(15,23,42,.72)"/>
                  <rect x="58" y="96" width="145" height="10" rx="5" fill="rgba(15,23,42,.24)"/>
                  <rect x="58" y="118" width="125" height="10" rx="5" fill="rgba(15,23,42,.24)"/>
                  <circle cx="190" cy="145" r="38" fill="var(--preview-main)"/>
                  <path d="M190 124v28" stroke="#111827" stroke-width="8" stroke-linecap="round"/>
                  <circle cx="190" cy="164" r="4.5" fill="#111827"/>
                </svg>
              </div>

              <div class="oc-preview-body">
                <div class="oc-preview-badge" id="previewBadge">
                  {{ $currentTypeLabel }}
                </div>

                <div class="oc-preview-title" id="previewTitle">
                  {{ $warning->title ?: 'Systemhinweis' }}
                </div>

                <div class="oc-preview-message" id="previewMessage">
                  {{ $warning->message ?: 'Bitte speichern Sie Ihre Arbeit.' }}
                </div>

                <button type="button" class="oc-preview-btn" id="previewButton">
                  {{ $warning->button_text ?: 'Verstanden' }}
                </button>
              </div>
            </div>
          </div>

          <div style="height:16px;"></div>

          <button type="submit" class="oc-btn-dark">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
            Speichern & live senden
          </button>

          <div id="systemWarningResult" class="oc-result"></div>
        </div>
      </div>
    </form>
  </div>

  <div class="oc-card">
    <div style="padding:18px 18px 4px;">
      <h3 class="oc-section-title" style="margin-bottom:4px;">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 3v18h18"/>
          <path d="M7 14l3-3 3 2 5-6"/>
        </svg>
        Verlauf
      </h3>
      <div class="oc-help">
        Die letzten Änderungen am Systemhinweis.
      </div>
    </div>

    <div class="oc-list-head">
      <div>Zeit</div>
      <div>Aktion</div>
      <div>Typ</div>
      <div>Titel</div>
      <div>Status</div>
    </div>

    <div class="oc-list">
      @forelse($histories as $history)
        @php
  $action = $history->action ?? 'updated';
  $actionLabel = $actionLabels[$action] ?? $action;

  $actionClass = match ($action) {
    'enabled' => 'green',
    'disabled' => 'orange',
    default => 'blue',
  };

  $historyTypeLabel = $typeLabels[$history->type] ?? ($history->type ?: '—');
        @endphp

        <div class="oc-item">
          <div class="oc-item-row">
            <div class="oc-cell">
              <div class="oc-cell-title">Zeit</div>
              <div class="oc-main">
                <div class="oc-ttl">
                  {{ $history->created_at?->format('d.m.Y') ?: '—' }}
                </div>
                <div class="oc-subt">
                  {{ $history->created_at?->format('H:i') ?: '' }}
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Aktion</div>
              <span class="oc-status-pill {{ $actionClass }}">
                {{ $actionLabel }}
              </span>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Typ</div>
              <div class="oc-main">
                <div class="oc-ttl" style="font-size:14px;">
                  {{ $historyTypeLabel }}
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Titel</div>
              <div class="oc-main">
                <div class="oc-ttl">{{ $history->title ?: '—' }}</div>
                <div class="oc-subt">{{ $history->message ?: 'Keine Nachricht' }}</div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Status</div>
              @if($history->is_active)
                <span class="oc-status-pill green">Aktiv</span>
              @else
                <span class="oc-status-pill gray">Inaktiv</span>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="oc-empty">
          Noch kein Verlauf vorhanden.
        </div>
      @endforelse
    </div>
  </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
@push('scripts')
<script>
  function toast(kind, title, msg) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;

    const icons = {
      ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
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

    setTimeout(() => {
      try { el.remove(); } catch(e) {}
    }, 4000);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('systemWarningForm');
    const resultBox = document.getElementById('systemWarningResult');

    const titleInput = form?.querySelector('[name="title"]');
    const messageInput = form?.querySelector('[name="message"]');
    const buttonInput = form?.querySelector('[name="button_text"]');
    const typeSelect = document.getElementById('warningTypeSelect');
    const themeSelect = document.getElementById('warningThemeSelect');

    const preview = document.getElementById('warningPreview');
    const previewBadge = document.getElementById('previewBadge');
    const previewTitle = document.getElementById('previewTitle');
    const previewMessage = document.getElementById('previewMessage');
    const previewButton = document.getElementById('previewButton');

    const typeLabels = {
      development: 'Entwicklung',
      uploading: 'Upload läuft',
      fixing: 'Fehlerbehebung',
      maintenance: 'Wartung',
    };

    function showResult(message, success = true) {
      if (!resultBox) return;

      resultBox.textContent = message;
      resultBox.className = success ? 'oc-result ok' : 'oc-result bad';
    }

    function updatePreview() {
      if (!preview) return;

      const theme = themeSelect?.value || 'amber';

      preview.classList.remove(
        'oc-theme-amber',
        'oc-theme-blue',
        'oc-theme-red',
        'oc-theme-green',
        'oc-theme-purple'
      );

      preview.classList.add('oc-theme-' + theme);

      if (previewBadge) {
        previewBadge.textContent = typeLabels[typeSelect?.value] || 'Systemhinweis';
      }

      if (previewTitle) {
        previewTitle.textContent = titleInput?.value || 'Systemhinweis';
      }

      if (previewMessage) {
        previewMessage.textContent = messageInput?.value || 'Bitte speichern Sie Ihre Arbeit.';
      }

      if (previewButton) {
        previewButton.textContent = buttonInput?.value || 'Verstanden';
      }
    }

    [titleInput, messageInput, buttonInput, typeSelect, themeSelect].forEach((el) => {
      if (el) {
        el.addEventListener('input', updatePreview);
        el.addEventListener('change', updatePreview);
      }
    });

    updatePreview();

    if (!form) return;

    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const formData = new FormData(form);

      formData.set('is_active', form.querySelector('[name="is_active"]').checked ? '1' : '0');
      formData.set('can_close', form.querySelector('[name="can_close"]').checked ? '1' : '0');
      formData.set('show_backdrop', form.querySelector('[name="show_backdrop"]').checked ? '1' : '0');

      try {
        const response = await fetch("{{ route('admin.system-warning.update') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: formData,
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
          const message = data.message || 'Fehler beim Speichern.';
          showResult(message, false);
          toast('bad', 'Fehler', message);
          return;
        }

        const message = data.message || 'Systemhinweis wurde gespeichert.';
        showResult(message, true);
        toast('ok', 'Gespeichert', message);

      } catch (error) {
        showResult('Netzwerkfehler beim Speichern.', false);
        toast('bad', 'Netzwerkfehler', 'Die Anfrage konnte nicht gesendet werden.');
      }
    });
  });
</script>
@endpush
@endonce


@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Systemhinweis',
        url: "{{ url()->current() }}",
        clickable: false

      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush