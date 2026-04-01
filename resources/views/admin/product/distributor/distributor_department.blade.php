@extends('admin.layouts.app')
@section('title', 'Lieferant – Abteilungen & Kontakte')

@once
@push('style')
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

    --blue:#2563eb;
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
    max-width:1550px;
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

  .oc-title{font-size:26px;font-weight:800;letter-spacing:-.025em;color:#111827}
  .oc-sub{font-size:14px;color:var(--text-muted);margin-top:4px}

  .oc-breadcrumb{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
    font-size:13px;
    color:var(--text-muted);
  }
  .oc-breadcrumb a{
    color:var(--text-muted);
    text-decoration:none;
    font-weight:700;
  }
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
  .oc-btn.success{background:var(--success)}
  .oc-btn.success:hover{background:#0d9668}
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
    text-decoration:none;
  }
  .oc-btn-soft:hover{background:#f9fafb;color:var(--text-main);text-decoration:none;}

  .oc-btn-ic{
    width:36px;height:36px;border-radius:8px;border:1px solid var(--border);background:#fff;
    display:inline-flex;align-items:center;justify-content:center;color:var(--text-muted);cursor:pointer;transition:var(--transition);
    text-decoration:none;
  }
  .oc-btn-ic:hover{background:#f9fafb;color:var(--text-main);border-color:#d1d5db;text-decoration:none;}
  .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .oc-btn-ic.primary:hover{border-color:var(--primary)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:1200px){
    .oc-analytics{grid-template-columns:repeat(2, minmax(0,1fr));}
  }
  @media(max-width:700px){
    .oc-analytics{grid-template-columns:repeat(1, minmax(0,1fr));}
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
  .oc-stat-icon.total{background:var(--blue-light);color:var(--blue)}
  .oc-stat-icon.mail{background:#eef2ff;color:#4f46e5}
  .oc-stat-icon.phone{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.office{background:var(--warning-light);color:#d97706}

  .oc-stat-meta{min-width:0}
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
    box-shadow:var(--shadow-sm)
  }

  .oc-toolbar-left,.oc-toolbar-right{
    display:flex;
    align-items:flex-end;
    gap:12px;
    flex-wrap:wrap;
  }
  .oc-toolbar-left{flex:1;}

  .oc-filter-block{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:170px;
  }
  .oc-filter-block.search{flex:1;min-width:300px;}
  .oc-filter-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }

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

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    margin-bottom:16px;
    overflow:hidden;
  }
  .oc-card-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
    flex-wrap:wrap;
  }
  .oc-card-title{
    font-size:16px;
    font-weight:900;
    color:#111827;
    margin:0;
  }
  .oc-card-body{
    padding:18px;
  }

  .oc-table-wrap{
    overflow:auto;
    width:100%;
  }

  .oc-table{
    width:100%;
    border-collapse:separate;
    border-spacing:0;
    min-width:1050px;
  }
  .oc-table thead th{
    text-align:left;
    padding:12px 12px;
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:var(--text-muted);
    border-bottom:1px solid var(--border);
    background:#fafafa;
    white-space:nowrap;
  }
  .oc-table tbody td{
    padding:14px 12px;
    border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
  }
  .oc-table tbody tr:hover{
    background:#fcfcfc;
  }

  .oc-link{
    color:var(--blue);
    text-decoration:none;
    font-weight:700;
  }
  .oc-link:hover{text-decoration:underline;}

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
  }

  .oc-actions{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-inline-actions{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
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
  .oc-modal-backdrop.open{opacity:1;pointer-events:auto}

  .oc-modal{
    width:100%;
    max-width:720px;
    background:#fff;
    border:1px solid rgba(229,231,235,.9);
    border-radius:16px;
    box-shadow:var(--shadow);
    transform:translateY(12px) scale(.985);
    transition:transform .22s ease;
    overflow:hidden;
  }
  .oc-modal-backdrop.open .oc-modal{transform:translateY(0) scale(1)}

  .oc-modal-h{
    display:flex;
    gap:12px;
    align-items:center;
    justify-content:space-between;
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
  }
  .oc-modal-ttl{font-weight:900;font-size:16px;line-height:1.2;margin:0;color:#111827}
  .oc-modal-b{padding:20px 18px;max-height:72vh;overflow-y:auto;}
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
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:16px;
  }
  @media(max-width:760px){
    .oc-form-grid{grid-template-columns:1fr;}
  }

  .oc-form-group{margin-bottom:16px;}
  .oc-label{display:block;font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:6px;}
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
  .oc-input-form:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);}
  .oc-help{font-size:12px;color:var(--text-muted);margin-top:6px;}

  .oc-row-toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin-top:14px;
    flex-wrap:wrap;
  }

  .oc-note{
    padding:12px 14px;
    border-radius:12px;
    border:1px solid #fee2e2;
    background:#fff7f7;
    color:#991b1b;
    margin-bottom:16px;
    font-size:13px;
  }
  .oc-note ul{margin:0;padding-left:18px;}

  .oc-pagination{
    margin-top:18px;
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:14px 16px;
    box-shadow:var(--shadow-sm);
  }

  .oc-badge{
    display:inline-flex;
    align-items:center;
    padding:5px 10px;
    border-radius:999px;
    border:1px solid var(--border);
    background:#fff;
    font-size:12px;
    font-weight:800;
    color:var(--text-muted);
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
  .oc-toast-ic{width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 auto}
  .oc-toast-ic.ok{background:var(--success-light);color:var(--success)}
  .oc-toast-ic.bad{background:var(--danger-light);color:var(--danger)}
  .oc-toast-ttl{font-weight:900;font-size:13px;margin:0;color:#111827}
  .oc-toast-msg{font-size:12px;color:#374151;margin:4px 0 0 0;line-height:1.4}
  .oc-toast-x{margin-left:auto;background:transparent;border:none;cursor:pointer;color:var(--text-muted);}
</style>
@endpush
@endonce

@section('content')
@php
  $distributor = $distributor ?? $brand ?? null;

  $totalContacts = $departments->total();
  $mailCount = collect($departments->items())->filter(fn($d) => !empty($d->email))->count();
  $phoneCount = collect($departments->items())->filter(fn($d) => !empty($d->phone))->count();
  $officeCount = collect($departments->items())->filter(fn($d) => !empty($d->office))->count();
@endphp

<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">Lieferant – {{ $distributor?->name ?? 'Unbekannt' }}</div>
        <div class="oc-sub">Abteilungen, Ansprechpartner und Kommunikationsdaten zentral verwalten.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <a href="{{ route('distributors.index') }}">Lieferanten</a>
          <span>›</span>
          <span class="current">Abteilungen & Kontakte</span>
        </div>
      </div>

      <div class="oc-inline-actions">
        <a href="{{ route('distributors.index') }}" class="oc-btn-soft">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
          </svg>
          Zurück zu Lieferanten
        </a>

        <button type="button" class="oc-btn" onclick="openModal('modalCreateRow')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
          </svg>
          Kontakt hinzufügen
        </button>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 7h16M4 12h16M4 17h16"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Gesamt</div>
        <div class="oc-stat-value">{{ $totalContacts }}</div>
        <div class="oc-stat-sub">Kontakte in dieser Ansicht</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon mail">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 4h16v16H4z"></path>
          <path d="M22 6l-10 7L2 6"></path>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">E-Mail</div>
        <div class="oc-stat-value">{{ $mailCount }}</div>
        <div class="oc-stat-sub">Mit E-Mail-Adresse</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon phone">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 16.92V19a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 4.18 2 2 0 0 1 5 2h2.09a2 2 0 0 1 2 1.72l.3 2.11a2 2 0 0 1-.57 1.72l-1.27 1.27a16 16 0 0 0 6.91 6.91l1.27-1.27a2 2 0 0 1 1.72-.57l2.11.3A2 2 0 0 1 22 16.92z"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Handy</div>
        <div class="oc-stat-value">{{ $phoneCount }}</div>
        <div class="oc-stat-sub">Mit Mobilnummer</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon office">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 21h18"></path>
          <path d="M5 21V7l8-4v18"></path>
          <path d="M19 21V11l-6-4"></path>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Büro</div>
        <div class="oc-stat-value">{{ $officeCount }}</div>
        <div class="oc-stat-sub">Mit Büro-Telefon</div>
      </div>
    </div>
  </div>

  <form action="{{ route('distributors.departments.index', $distributor->id) }}" method="GET" class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          class="oc-input"
          name="q"
          value="{{ request('q') }}"
          placeholder="Name, Abteilung, Position, E-Mail, Telefon …"
        >
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Pro Seite</label>
        <select name="perPage" class="oc-select" onchange="this.form.submit()">
          @foreach([10,25,50,100] as $n)
            <option value="{{ $n }}" {{ (int)request('perPage',25) === $n ? 'selected' : '' }}>{{ $n }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button class="oc-btn-soft" type="submit">Filtern</button>
      <a href="{{ route('distributors.departments.index', $distributor->id) }}" class="oc-btn-soft">Zurücksetzen</a>
    </div>
  </form>

  <div class="oc-card">
    <div class="oc-card-head">
      <h3 class="oc-card-title">Neue Kontakte erfassen</h3>
      <span class="oc-badge">Mehrere Datensätze gleichzeitig</span>
    </div>

    <div class="oc-card-body">
      @if ($errors->any())
        <div class="oc-note">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="bulkCreateForm" action="{{ route('distributors.departments.store', $distributor->id) }}" method="POST">
        @csrf

        <div class="oc-table-wrap">
          <table class="oc-table" id="add_department">
            <thead>
              <tr>
                <th>Ansprechpartner</th>
                <th>Abteilung</th>
                <th>Position</th>
                <th>E-Mail</th>
                <th>Handy</th>
                <th>Festnetz</th>
                <th>Büro</th>
                <th style="text-align:right;">Aktion</th>
              </tr>
            </thead>
            <tbody id="deptRows">
              <tr>
                <td>
                  <input type="hidden" name="d[0][d_id]" value="{{ $distributor->id }}">
                  <input type="hidden" name="d[0][status]" value="Unpublished">
                  <input type="text" class="oc-input-form" name="d[0][name]" placeholder="Gesprächspartner">
                </td>
                <td><input type="text" class="oc-input-form" name="d[0][d_department]" placeholder="Abteilung"></td>
                <td><input type="text" class="oc-input-form" name="d[0][position]" placeholder="Position"></td>
                <td><input type="email" class="oc-input-form" name="d[0][email]" placeholder="E-Mail"></td>
                <td><input type="text" class="oc-input-form" name="d[0][phone]" placeholder="Handynummer"></td>
                <td><input type="text" class="oc-input-form" name="d[0][home]" placeholder="Festnetznummer"></td>
                <td><input type="text" class="oc-input-form" name="d[0][office]" placeholder="Büro-Telefon"></td>
                <td style="text-align:right;">
                  <button type="button" class="oc-btn-ic primary addRow" title="Zeile hinzufügen">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 5v14M5 12h14"></path>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="oc-row-toolbar">
          <div class="oc-inline-actions">
            <button type="submit" class="oc-btn success">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <path d="M17 21v-8H7v8"></path>
                <path d="M7 3v5h8"></path>
              </svg>
              Datensätze speichern
            </button>

            <button type="button" class="oc-btn-soft addRow">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"></path>
              </svg>
              Weitere Zeile
            </button>
          </div>

          <div class="oc-help">Neue Kontakte werden standardmäßig als <strong>Unpublished</strong> gespeichert.</div>
        </div>
      </form>
    </div>
  </div>

  <div class="oc-card">
    <div class="oc-card-head">
      <h3 class="oc-card-title">Vorhandene Kontakte</h3>
      <span class="oc-badge">{{ $departments->total() }} Einträge</span>
    </div>

    <div class="oc-card-body">
      @if($departments->count())
        <div class="oc-table-wrap">
          <table class="oc-table" id="brand_table">
            <thead>
              <tr>
                <th>Ansprechpartner</th>
                <th>Abteilung</th>
                <th>Position</th>
                <th>E-Mail</th>
                <th>Handy</th>
                <th>Festnetz</th>
                <th>Büro</th>
                <th style="text-align:right;">Aktion</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($departments as $dept)
                <tr>
                  <td>{{ $dept->name ?: '—' }}</td>
                  <td>{{ $dept->d_department ?: '—' }}</td>
                  <td>{{ $dept->position ?: '—' }}</td>
                  <td>
                    @if($dept->email)
                      <a href="mailto:{{ $dept->email }}" class="oc-link">{{ $dept->email }}</a>
                    @else
                      —
                    @endif
                  </td>
                  <td>{{ $dept->phone ?: '—' }}</td>
                  <td>{{ $dept->home ?: '—' }}</td>
                  <td>{{ $dept->office ?: '—' }}</td>
                  <td>
                    <div class="oc-actions">
                      <button type="button" class="oc-btn-ic primary" onclick="openModal('editDept{{ $dept->id }}')" title="Bearbeiten">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                      </button>

                      <form action="{{ route('distributors.departments.destroy', [$distributor->id, $dept->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Diesen Kontakt löschen?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="oc-btn-ic danger" title="Löschen">
                          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                          </svg>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>

                {{-- Edit Modal --}}
                <div class="oc-modal-backdrop" id="editDept{{ $dept->id }}">
                  <div class="oc-modal">
                    <div class="oc-modal-h">
                      <h3 class="oc-modal-ttl">Kontakt bearbeiten</h3>
                      <button class="oc-btn-ic" type="button" onclick="closeModal('editDept{{ $dept->id }}')">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </button>
                    </div>

                    <form action="{{ route('distributors.departments.update', [$distributor->id, $dept->id]) }}" method="POST">
                      @csrf
                      @method('PUT')

                      <div class="oc-modal-b">
                        <div class="oc-form-grid">
                          <div class="oc-form-group">
                            <label class="oc-label">Abteilung</label>
                            <input type="text" class="oc-input-form" name="d_department" value="{{ $dept->d_department }}">
                          </div>

                          <div class="oc-form-group">
                            <label class="oc-label">Gesprächspartner</label>
                            <input type="text" class="oc-input-form" name="name" value="{{ $dept->name }}">
                          </div>

                          <div class="oc-form-group">
                            <label class="oc-label">Position</label>
                            <input type="text" class="oc-input-form" name="position" value="{{ $dept->position }}">
                          </div>

                          <div class="oc-form-group">
                            <label class="oc-label">E-Mail</label>
                            <input type="email" class="oc-input-form" name="email" value="{{ $dept->email }}">
                          </div>

                          <div class="oc-form-group">
                            <label class="oc-label">Handy</label>
                            <input type="text" class="oc-input-form" name="phone" value="{{ $dept->phone }}">
                          </div>

                          <div class="oc-form-group">
                            <label class="oc-label">Festnetz</label>
                            <input type="text" class="oc-input-form" name="home" value="{{ $dept->home }}">
                          </div>

                          <div class="oc-form-group" style="grid-column:1 / -1;">
                            <label class="oc-label">Büro</label>
                            <input type="text" class="oc-input-form" name="office" value="{{ $dept->office }}">
                          </div>
                        </div>

                        <input type="hidden" name="status" value="{{ $dept->status ?? 'Unpublished' }}">
                      </div>

                      <div class="oc-modal-f">
                        <button type="button" class="oc-btn-soft" onclick="closeModal('editDept{{ $dept->id }}')">Abbrechen</button>
                        <button class="oc-btn" type="submit">Speichern</button>
                      </div>
                    </form>
                  </div>
                </div>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="oc-pagination">
          {{ $departments->appends(request()->query())->links() }}
        </div>
      @else
        <div class="oc-empty">Keine Kontakte vorhanden.</div>
      @endif
    </div>
  </div>
</div>

{{-- Single Contact Create Modal --}}
<div class="oc-modal-backdrop" id="modalCreateRow">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Kontakt hinzufügen</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('modalCreateRow')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form action="{{ route('distributors.departments.store', $distributor->id) }}" method="POST">
      @csrf

      <div class="oc-modal-b">
        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Gesprächspartner</label>
            <input type="text" class="oc-input-form" name="d[0][name]" placeholder="Gesprächspartner">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Abteilung</label>
            <input type="text" class="oc-input-form" name="d[0][d_department]" placeholder="Abteilung">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Position</label>
            <input type="text" class="oc-input-form" name="d[0][position]" placeholder="Position">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">E-Mail</label>
            <input type="email" class="oc-input-form" name="d[0][email]" placeholder="E-Mail">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Handy</label>
            <input type="text" class="oc-input-form" name="d[0][phone]" placeholder="Handynummer">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Festnetz</label>
            <input type="text" class="oc-input-form" name="d[0][home]" placeholder="Festnetznummer">
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Büro</label>
            <input type="text" class="oc-input-form" name="d[0][office]" placeholder="Büro-Telefon">
          </div>
        </div>

        <input type="hidden" name="d[0][d_id]" value="{{ $distributor->id }}">
        <input type="hidden" name="d[0][status]" value="Unpublished">
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('modalCreateRow')">Abbrechen</button>
        <button class="oc-btn" type="submit">Speichern</button>
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
    document.getElementById('toast-wrap').appendChild(el);
    setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
  }

  document.addEventListener('click', function(e){
    if (e.target.classList.contains('oc-modal-backdrop')) {
      e.target.classList.remove('open');
    }
  });

  document.addEventListener('keydown', function(e){
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

  (function(){
    let i = 0;
    const tbody = document.getElementById('deptRows');
    if (!tbody) return;

    function template(idx){
      return `
        <tr>
          <td>
            <input type="hidden" name="d[${idx}][d_id]" value="{{ $distributor->id }}">
            <input type="hidden" name="d[${idx}][status]" value="Unpublished">
            <input type="text" class="oc-input-form" name="d[${idx}][name]" placeholder="Gesprächspartner">
          </td>
          <td><input type="text" class="oc-input-form" name="d[${idx}][d_department]" placeholder="Abteilung"></td>
          <td><input type="text" class="oc-input-form" name="d[${idx}][position]" placeholder="Position"></td>
          <td><input type="email" class="oc-input-form" name="d[${idx}][email]" placeholder="E-Mail"></td>
          <td><input type="text" class="oc-input-form" name="d[${idx}][phone]" placeholder="Handynummer"></td>
          <td><input type="text" class="oc-input-form" name="d[${idx}][home]" placeholder="Festnetznummer"></td>
          <td><input type="text" class="oc-input-form" name="d[${idx}][office]" placeholder="Büro-Telefon"></td>
          <td style="text-align:right;">
            <button type="button" class="oc-btn-ic danger removeRow" title="Zeile entfernen">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14"></path>
              </svg>
            </button>
          </td>
        </tr>
      `;
    }

    document.querySelectorAll('.addRow').forEach(btn => {
      btn.addEventListener('click', function(){
        i++;
        tbody.insertAdjacentHTML('beforeend', template(i));
      });
    });

    tbody.addEventListener('click', function(e){
      const btn = e.target.closest('.removeRow');
      if (!btn) return;

      e.preventDefault();
      const tr = btn.closest('tr');
      if (!tr) return;

      const rows = tbody.querySelectorAll('tr');
      if (rows.length <= 1) {
        tr.querySelectorAll('input').forEach(input => {
          if (input.type !== 'hidden') input.value = '';
        });
        return;
      }

      tr.remove();
    });
  })();

  @if($errors->any())
    openModal('modalCreateRow');
  @endif
</script>
@endpush
@endonce