@extends('admin.layouts.app')
@section('title', 'Lead E-Mails')

@php
    $emailItems = $emails instanceof \Illuminate\Pagination\AbstractPaginator ? collect($emails->items()) : collect($emails);

    $totalCount = $emails instanceof \Illuminate\Pagination\AbstractPaginator ? $emails->total() : $emailItems->count();
    $unreadCount = (int) $emailItems->where('is_read', false)->count();
    $readCount = (int) $emailItems->where('is_read', true)->count();
    $domainCount = (int) $emailItems->pluck('domain')->filter()->unique()->count();
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
    padding:39px;
    padding-right:79px;
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
  .oc-btn-ic:hover{
    background:#f9fafb;
    color:var(--text-main);
    border-color:#d1d5db;
    text-decoration:none;
  }
  .oc-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
  .oc-btn-ic.primary:hover{border-color:var(--primary)}
  .oc-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
  .oc-btn-ic.warning:hover{border-color:#f59e0b}
  .oc-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
  .oc-btn-ic.success:hover{border-color:var(--success)}
  .oc-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
  .oc-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

  .oc-analytics{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
  }
  @media(max-width:1200px){ .oc-analytics{grid-template-columns:repeat(2,minmax(0,1fr));} }
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
  .oc-stat-icon.published{background:var(--success-light);color:var(--success)}
  .oc-stat-icon.unpublished{background:var(--warning-light);color:#d97706}
  .oc-stat-icon.type{background:var(--gray-light);color:var(--gray)}

  .oc-stat-meta{min-width:0}
  .oc-stat-label{font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;}
  .oc-stat-value{font-size:24px;font-weight:900;color:#111827;line-height:1.1;margin-top:4px;}
  .oc-stat-sub{font-size:12px;color:var(--text-muted);margin-top:4px;}

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
  .oc-filter-block.search{
    flex:1;
    min-width:280px;
  }
  .oc-filter-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
  }

  .oc-input,.oc-select{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:8px;
    padding:10px 12px;
    font-size:14px;
    outline:none;
    transition:var(--transition);
    min-width:180px;
    width:100%;
  }
  .oc-input.search{
    padding-left:36px;
    min-width:240px;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:10px center;
    background-size:16px;
  }
  .oc-input:focus,.oc-select:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }

  .oc-list-head{
    display:grid;
    grid-template-columns:minmax(220px,1.3fr) minmax(240px,1.6fr) 140px 140px 190px;
    gap:14px;
    align-items:center;
    padding:16px 16px 10px 16px;
    color:var(--text-muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
  }
  @media(max-width:1280px){ .oc-list-head{display:none;} }

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
  .oc-item.unread{
    border-color:#cfe8a0;
    background:linear-gradient(180deg,#fbfef5 0%,#ffffff 100%);
  }

  .oc-item-row{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:center;
    grid-template-columns:minmax(220px,1.3fr) minmax(240px,1.6fr) 140px 140px 190px;
  }
  @media(max-width:1280px){ .oc-item-row{grid-template-columns:1fr;} }

  .oc-cell{min-width:0}
  .oc-cell-title{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    margin-bottom:4px;
    display:none;
  }
  @media(max-width:1280px){ .oc-cell-title{display:block;} }

  .oc-main{display:flex;flex-direction:column;min-width:0}
  .oc-ttl{font-weight:800;font-size:15px;margin-bottom:4px;color:#111827}
  .oc-subt{font-size:13px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

  .oc-from{
    display:flex;
    align-items:flex-start;
    gap:12px;
  }
  .oc-avatar{
    width:46px;
    height:46px;
    border-radius:14px;
    background:var(--blue-light);
    color:var(--blue);
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
    border:1px solid #dbeafe;
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
  .oc-status-pill.green{background:#ecfdf5;color:#047857;}
  .oc-status-pill.orange{background:#fffbeb;color:#b45309;}

  .oc-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
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
  .oc-modal-backdrop.open{
    opacity:1;
    pointer-events:auto;
  }

  .oc-modal{
    width:100%;
    max-width:860px;
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

  .oc-empty{
    text-align:center;
    padding:60px;
    color:var(--text-muted);
    background:#fff;
    border:1px dashed var(--border);
    border-radius:16px;
    margin:16px;
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
  .oc-toast-x{margin-left:auto;background:transparent;border:none;cursor:pointer;color:var(--text-muted);}

  .oc-mail-body{
    font-size:14px;
    line-height:1.7;
    color:#374151;
    white-space:pre-wrap;
    word-break:break-word;
  }
  .oc-detail-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:12px;
    margin-bottom:18px;
  }
  @media(max-width:760px){ .oc-detail-grid{grid-template-columns:1fr;} }

  .oc-detail-box{
    border:1px solid var(--border);
    background:#f9fafb;
    border-radius:12px;
    padding:12px;
  }
  .oc-detail-label{
    font-size:11px;
    font-weight:800;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
    margin-bottom:5px;
  }
  .oc-detail-value{
    font-size:14px;
    color:#111827;
    font-weight:700;
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">LEAD E-MAILS</div>
        <div class="oc-sub">Verwalten Sie gespeicherte E-Mails, Live-Abruf, Filter und Detailansichten zentral.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Home</a>
          <span>›</span>
          <span class="current">Lead E-Mails</span>
        </div>
      </div>

      <div class="d-flex flex-wrap" style="gap:10px;">
        <button type="button" class="oc-btn" id="fetchEmailsNow">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12a9 9 0 1 1-2.64-6.36"/>
            <path d="M21 3v9h-9"/>
          </svg>
          Jetzt abrufen
        </button>

        <a href="{{ route('lead.email.export.csv') }}" class="oc-btn-soft">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 3v12"/>
            <path d="M7 10l5 5 5-5"/>
            <path d="M5 21h14"/>
          </svg>
          CSV
        </a>

        <a href="{{ route('lead.email.export.pdf') }}" class="oc-btn-soft">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
          </svg>
          PDF
        </a>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 6h16"/>
          <path d="M4 12h16"/>
          <path d="M4 18h16"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Gesamt</div>
        <div class="oc-stat-value">{{ $totalCount }}</div>
        <div class="oc-stat-sub">Gespeicherte E-Mails</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Gelesen</div>
        <div class="oc-stat-value" id="readCountValue">{{ $readCount }}</div>
        <div class="oc-stat-sub">Bereits geprüft</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 8v4"/>
          <path d="M12 16h.01"/>
          <circle cx="12" cy="12" r="10"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Ungelesen</div>
        <div class="oc-stat-value" id="leadUnreadValue">{{ $unreadCount }}</div>
        <div class="oc-stat-sub">Neue E-Mails</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 7h18"/>
          <path d="M6 3h12l3 4-3 4H6L3 7l3-4z"/>
          <path d="M8 14h8"/>
          <path d="M10 18h4"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Domains</div>
        <div class="oc-stat-value">{{ $domainCount }}</div>
        <div class="oc-stat-sub">Unterschiedliche Absender-Domains</div>
      </div>
    </div>
  </div>

  <form action="{{ route('lead.email.inbox') }}" method="GET" class="oc-toolbar" id="emailFilterForm">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          class="oc-input search"
          id="filterSearch"
          placeholder="Suche nach Absender, Betreff oder Inhalt"
          name="search"
          value="{{ request('search') }}"
        >
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Domain</label>
        <select name="domain" id="filterDomain" class="oc-select">
          <option value="">Alle Domains</option>
          @foreach($availableDomains as $domain)
            <option value="{{ $domain }}" {{ request('domain') == $domain ? 'selected' : '' }}>
              {{ $domain }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="oc-filter-block">
        <label class="oc-filter-label">Datum</label>
        <input type="date" name="date" id="filterDate" class="oc-input" value="{{ request('date') }}">
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button class="oc-btn-soft" type="submit">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 21l-6-6"/>
          <circle cx="10" cy="10" r="7"/>
        </svg>
        Suchen
      </button>

      @if(request('search') || request('domain') || request('date'))
        <a href="{{ route('lead.email.inbox') }}" class="oc-btn-soft">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 6L6 18M6 6l12 12"/>
          </svg>
          Zurücksetzen
        </a>
      @endif
    </div>
  </form>

  <div class="oc-card">
    <div class="oc-list-head">
      <div>Absender</div>
      <div>Betreff</div>
      <div>Domain</div>
      <div>Datum</div>
      <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="oc-list" id="leadEmailTableBody">
      @forelse($emails as $email)
        <div class="oc-item {{ !$email->is_read ? 'unread' : '' }}" id="email-row-{{ $email->id }}">
          <div class="oc-item-row">
            <div class="oc-cell">
              <div class="oc-cell-title">Absender</div>
              <div class="oc-from">
                <div class="oc-avatar">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16v16H4z"/>
                    <path d="M22 6l-10 7L2 6"/>
                  </svg>
                </div>
                <div class="oc-main">
                  <div class="oc-ttl">{{ $email->from }}</div>
                  <div class="oc-subt">{{ $email->is_read ? 'Gelesen' : 'Ungelesen' }}</div>
                </div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Betreff</div>
              <div class="oc-main">
                <div class="oc-ttl">{{ $email->subject ?: '(Kein Betreff)' }}</div>
                <div class="oc-subt">{{ \Illuminate\Support\Str::limit(strip_tags($email->body), 80, '...') }}</div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Domain</div>
              <div class="oc-main">
                <div class="oc-ttl" style="font-size:14px;">{{ $email->domain ?: '—' }}</div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Datum</div>
              <div class="oc-main">
                <div class="oc-ttl" style="font-size:14px;">{{ optional($email->date)->format('d.m.Y H:i') }}</div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Aktionen</div>
              <div class="oc-actions">
                <button type="button"
                        class="oc-btn-ic primary view-email"
                        title="E-Mail anzeigen"
                        data-id="{{ $email->id }}">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>

                <button type="button"
                        class="oc-btn-ic success ai-verify"
                        title="AI Verification"
                        data-id="{{ $email->id }}">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v4"/>
                    <path d="M12 18v4"/>
                    <path d="M4.93 4.93l2.83 2.83"/>
                    <path d="M16.24 16.24l2.83 2.83"/>
                    <path d="M2 12h4"/>
                    <path d="M18 12h4"/>
                    <path d="M4.93 19.07l2.83-2.83"/>
                    <path d="M16.24 7.76l2.83-2.83"/>
                    <circle cx="12" cy="12" r="4"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="oc-empty">Keine E-Mails gefunden.</div>
      @endforelse
    </div>
  </div>

  @if(method_exists($emails, 'links') && $emails->hasPages())
    <div class="oc-pagination">
      <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
        <div style="font-size:12px;color:#6b7280;">
          Zeige <strong>{{ $emails->firstItem() ?? 0 }}</strong>
          bis <strong>{{ $emails->lastItem() ?? 0 }}</strong>
          von <strong>{{ $emails->total() }}</strong> Einträgen
        </div>
        <div>
          {{ $emails->withQueryString()->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  @endif
</div>

<div class="oc-modal-backdrop" id="emailDetailModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">E-Mail Details</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('emailDetailModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="oc-modal-b" id="email-detail-body">
      <div class="text-center text-muted">Lädt...</div>
    </div>
  </div>
</div>

<div class="oc-modal-backdrop" id="aiModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">AI Verifizierte Anfrage</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('aiModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div class="oc-modal-b">
      <form id="aiSaveForm">
        <div id="ai-form-content">
          <div class="text-center text-muted">Bitte E-Mail auswählen.</div>
        </div>
      </form>
    </div>
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
    wrap.appendChild(el);
    setTimeout(() => { try { el.remove(); } catch(e) {} }, 4000);
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
  }

  function updateUnreadStats(unread) {
    const unreadValue = document.getElementById('leadUnreadValue');
    const readValue = document.getElementById('readCountValue');

    if (unreadValue) unreadValue.textContent = unread;
    if (readValue) {
      const total = {{ (int) $totalCount }};
      readValue.textContent = Math.max(total - unread, 0);
    }
  }

  function renderEmailRows(rows) {
    const tbody = document.getElementById('leadEmailTableBody');
    if (!tbody) return;

    if (!rows || !rows.length) {
      tbody.innerHTML = `<div class="oc-empty">Keine E-Mails gefunden.</div>`;
      return;
    }

    tbody.innerHTML = rows.map(email => `
      <div class="oc-item ${!email.is_read ? 'unread' : ''}" id="email-row-${email.id}">
        <div class="oc-item-row">
          <div class="oc-cell">
            <div class="oc-cell-title">Absender</div>
            <div class="oc-from">
              <div class="oc-avatar">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16v16H4z"/>
                  <path d="M22 6l-10 7L2 6"/>
                </svg>
              </div>
              <div class="oc-main">
                <div class="oc-ttl">${escapeHtml(email.from || '')}</div>
                <div class="oc-subt">${email.is_read ? 'Gelesen' : 'Ungelesen'}</div>
              </div>
            </div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Betreff</div>
            <div class="oc-main">
              <div class="oc-ttl">${escapeHtml(email.subject || '(Kein Betreff)')}</div>
              <div class="oc-subt">Lead E-Mail Eintrag</div>
            </div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Domain</div>
            <div class="oc-main">
              <div class="oc-ttl" style="font-size:14px;">${escapeHtml(email.domain || '—')}</div>
            </div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Datum</div>
            <div class="oc-main">
              <div class="oc-ttl" style="font-size:14px;">${escapeHtml(email.date || '')}</div>
            </div>
          </div>

          <div class="oc-cell">
            <div class="oc-cell-title">Aktionen</div>
            <div class="oc-actions">
              <button type="button" class="oc-btn-ic primary view-email" title="E-Mail anzeigen" data-id="${email.id}">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>

              <button type="button" class="oc-btn-ic success ai-verify" title="AI Verification" data-id="${email.id}">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 2v4"/>
                  <path d="M12 18v4"/>
                  <path d="M4.93 4.93l2.83 2.83"/>
                  <path d="M16.24 16.24l2.83 2.83"/>
                  <path d="M2 12h4"/>
                  <path d="M18 12h4"/>
                  <path d="M4.93 19.07l2.83-2.83"/>
                  <path d="M16.24 7.76l2.83-2.83"/>
                  <circle cx="12" cy="12" r="4"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    `).join('');
  }

  function loadRealtimeEmails() {
    $.get(`{{ route('lead.email.realtime.list') }}`, {
      search: $('#filterSearch').val(),
      domain: $('#filterDomain').val(),
      date: $('#filterDate').val()
    }, function(response) {
      renderEmailRows(response.rows || []);
      updateUnreadStats(response.unread_count || 0);
    });
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

  $(document).on('click', '#fetchEmailsNow', function () {
    const btn = $(this);
    btn.prop('disabled', true);

    $.ajax({
      url: `{{ route('lead.email.fetch') }}`,
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      success: function (response) {
        toast('ok', 'Aktualisiert', `Neu gespeichert: ${response.saved || 0}`);
        updateUnreadStats(response.unread_count || 0);
        loadRealtimeEmails();
      },
      error: function () {
        toast('bad', 'Fehler', 'E-Mails konnten nicht abgerufen werden.');
      },
      complete: function () {
        btn.prop('disabled', false);
      }
    });
  });

  $(document).on('click', '.view-email', function () {
    const id = $(this).data('id');
    const modalBody = $('#email-detail-body');

    modalBody.html('<div class="text-center text-muted">Lädt...</div>');

    $.get(`{{ url('admin/lead-email/show') }}/${id}`, function (data) {
      modalBody.html(`
        <div class="oc-detail-grid">
          <div class="oc-detail-box">
            <div class="oc-detail-label">Von</div>
            <div class="oc-detail-value">${data.from || ''}</div>
          </div>
          <div class="oc-detail-box">
            <div class="oc-detail-label">Datum</div>
            <div class="oc-detail-value">${data.date || ''}</div>
          </div>
          <div class="oc-detail-box">
            <div class="oc-detail-label">Betreff</div>
            <div class="oc-detail-value">${data.subject || ''}</div>
          </div>
          <div class="oc-detail-box">
            <div class="oc-detail-label">Domain</div>
            <div class="oc-detail-value">${data.domain || ''}</div>
          </div>
        </div>
        <div class="oc-detail-box">
          <div class="oc-detail-label">Inhalt</div>
          <div class="oc-mail-body">${data.body || ''}</div>
        </div>
      `);

      $.post(`{{ url('admin/lead-email/mark-read') }}/${id}`, {
        _token: '{{ csrf_token() }}'
      }, function(markResponse) {
        $(`#email-row-${id}`).removeClass('unread');
        updateUnreadStats(markResponse.unread_count || 0);
      });

      openModal('emailDetailModal');
    }).fail(function () {
      modalBody.html('<div class="text-danger">Fehler beim Laden der E-Mail.</div>');
      openModal('emailDetailModal');
    });
  });

  $(document).on('click', '.ai-verify', function () {
    const id = $(this).data('id');
    const form = $('#ai-form-content');

    form.html('<div class="text-center text-muted">AI Analyse läuft...</div>');
    openModal('aiModal');

    $.get(`/lead/email/api/${id}`, function () {
      $.get(`https://sadid2024.app.n8n.cloud/webhook-test/email-leads?id=${id}`, function (ai) {
        const fields = [
          'pre_type', 'source', 'title', 'type', 'type_extra', 'firma', 'lastname', 'name', 'street',
          'latitude', 'longitude', 'elevation', 'postcode', 'full_address', 'city', 'phone', 'telephone',
          'email', 'note', 'reason', 'status', 'periority', 'next_step'
        ];

        let html = '<div class="row">';
        fields.forEach(key => {
          const value = ai[key] ?? '';
          if (key === 'note') {
            html += `<div class="col-md-12 mb-2"><label>${key}</label><textarea name="${key}" class="form-control">${value}</textarea></div>`;
          } else {
            html += `<div class="col-md-6 mb-2"><label>${key}</label><input type="text" name="${key}" class="form-control" value="${value}"></div>`;
          }
        });
        html += `<div class="col-12 mt-3">
                  <button type="submit" class="oc-btn">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 21H5a2 2 0 0 1-2-2V7l5-4h8l5 4v12a2 2 0 0 1-2 2z"/>
                      <path d="M17 21v-8H7v8"/>
                      <path d="M7 3v4h10V3"/>
                    </svg>
                    Speichern
                  </button>
                </div>`;
        html += '</div>';

        form.html(html);
      }).fail(() => {
        form.html('<div class="text-danger">Fehler bei AI Analyse.</div>');
      });
    });
  });

  $('#aiSaveForm').submit(function (e) {
    e.preventDefault();
    const data = $(this).serialize();

    $.post('/admin/inquiries/ai-save', data, function (res) {
      toast('ok', 'Gespeichert', res.message || 'Anfrage erfolgreich gespeichert.');
      closeModal('aiModal');
    }).fail(() => {
      toast('bad', 'Fehler', 'Fehler beim Speichern.');
    });
  });

  @if(session('save_msg'))
    toast('ok', 'Gespeichert', @json(session('save_msg')));
  @endif

  @if(session('delete_msg'))
    toast('bad', 'Fehler', @json(session('delete_msg')));
  @endif

  setInterval(function () {
    loadRealtimeEmails();
  }, 15000);
</script>
@endpush
@endonce