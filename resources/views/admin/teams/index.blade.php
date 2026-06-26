@extends('admin.layouts.app')
@section('title', 'Teams')

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

$isPaginator = $teams instanceof LengthAwarePaginator || $teams instanceof AbstractPaginator;
$teamItems = $isPaginator ? collect($teams->items()) : collect($teams);

$totalCount = $isPaginator ? $teams->total() : $teamItems->count();
$activeCount = (int) $teamItems->where('status', 'Published')->count();
$inactiveCount = (int) $teamItems->filter(fn($item) => ($item->status ?? '') !== 'Published')->count();
$withLeaderCount = (int) $teamItems->filter(fn($item) => !empty($item->leader) && !empty(optional($item->leader)->employee))->count();
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
    font-family:Inter, system-ui, -apple-system, sans-serif;
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

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }

  .oc-list-head{
    display:grid;
    grid-template-columns:90px minmax(220px,1.2fr) minmax(170px,.9fr) minmax(220px,1.25fr) minmax(220px,1.25fr) 120px 170px;
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

  .oc-item-row{
    padding:16px;
    display:grid;
    gap:16px;
    align-items:center;
    grid-template-columns:90px minmax(220px,1.2fr) minmax(170px,.9fr) minmax(220px,1.25fr) minmax(220px,1.25fr) 120px 170px;
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

  .oc-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-avatar-stack{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:8px;
  }

  .oc-avatar{
    width:38px;
    height:38px;
    border-radius:999px;
    object-fit:cover;
    border:2px solid #fff;
    box-shadow:0 0 0 1px var(--border);
    background:#f9fafb;
    flex:0 0 auto;
  }

  .oc-avatar-sm{
    width:34px;
    height:34px;
  }

  .oc-avatar-more{
    min-width:34px;
    height:34px;
    padding:0 10px;
    border-radius:999px;
    background:var(--gray-light);
    color:var(--text-muted);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:800;
    border:1px solid var(--border);
  }

  .oc-person{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
  }

  .oc-person-meta{min-width:0;}
  .oc-person-name{
    font-size:14px;
    font-weight:800;
    color:#111827;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .oc-person-role{
    font-size:12px;
    color:var(--text-muted);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .oc-badge{
    display:inline-flex;
    align-items:center;
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:800;
    line-height:1;
  }
  .oc-badge.blue{
    background:var(--blue-light);
    color:var(--blue);
  }

  .oc-inline-count{
    display:flex;
    align-items:center;
    gap:8px;
    flex-wrap:wrap;
  }

  .oc-inline-count strong{
    font-size:14px;
    color:#111827;
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
    max-width:620px;
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
  @media(max-width:760px){ .oc-form-grid{grid-template-columns:1fr;} }

  .oc-form-group{margin-bottom:16px;}
  .oc-label{
    display:block;
    font-size:13px;
    font-weight:700;
    color:var(--text-main);
    margin-bottom:6px;
  }
  .oc-input-form,.oc-select,.oc-textarea{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid var(--border);
    background:#fff;
    font-size:14px;
    outline:none;
    transition:var(--transition);
  }
  .oc-input-form:focus,.oc-select:focus,.oc-textarea:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }
  .oc-textarea{
    min-height:90px;
    resize:vertical;
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

  @media(max-width:991px){
    .oc-wrap{
      padding:24px 18px 24px 18px;
      margin:10px auto;
    }
  }
</style>
@endpush
@endonce

@section('content')
<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">TEAMS</div>
        <div class="oc-sub">Verwalten Sie Teams, Abteilungen, Teamleiter und Mitglieder zentral an einem Ort.</div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
          <span>›</span>
          <span>HR</span>
          <span>›</span>
          <span class="current">Teams</span>
        </div>
      </div>

      <div>
        <button type="button" class="oc-btn" onclick="openModal('createTeamModal')">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"></path>
          </svg>
          Team erstellen
        </button>
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
        <div class="oc-stat-label">Gesamt</div>
        <div class="oc-stat-value">{{ $totalCount }}</div>
        <div class="oc-stat-sub">Alle Teams im System</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Aktiv</div>
        <div class="oc-stat-value">{{ $activeCount }}</div>
        <div class="oc-stat-sub">Veröffentlichte Teams</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Inaktiv</div>
        <div class="oc-stat-value">{{ $inactiveCount }}</div>
        <div class="oc-stat-sub">Nicht aktive Teams</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Mit Leitung</div>
        <div class="oc-stat-value">{{ $withLeaderCount }}</div>
        <div class="oc-stat-sub">Teams mit Teamleiter</div>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div style="margin-bottom:16px;padding:12px 14px;border:1px solid #c7f2df;background:#ecfdf5;color:#065f46;border-radius:12px;font-weight:700;">
      {{ session('success') }}
    </div>
  @endif

  <form method="GET" action="{{ route('teams.index') }}" class="oc-toolbar">
    <div class="oc-toolbar-left">
      <div class="oc-filter-block search">
        <label class="oc-filter-label">Suche</label>
        <input
          type="text"
          name="search"
          value="{{ $search ?? request('search') }}"
          class="oc-input"
          placeholder="Teamname, Abteilung oder Leiter suchen..."
        >
      </div>
    </div>

    <div class="oc-toolbar-right">
      <button class="oc-btn-soft" type="submit">Suchen</button>
      @if(($search ?? request('search')))
        <a href="{{ route('teams.index') }}" class="oc-btn-soft">Zurücksetzen</a>
      @endif
    </div>
  </form>

  <div class="oc-card">
    <div class="oc-list-head">
      <div>ID</div>
      <div>Team</div>
      <div>Abteilung</div>
      <div>Teamleiter</div>
      <div>Mitglieder / Reserve</div>
      <div>Status</div>
      <div style="text-align:right;">Aktionen</div>
    </div>

    <div class="oc-list">
      @forelse($teams as $team)
        @php
  $leaderEmployee = optional(optional($team->leader)->employee);
  $leaderImage = $leaderEmployee && $leaderEmployee->image
    ? asset('images/employee/' . $leaderEmployee->image)
    : asset('images/employee/default.png');

  $statusClass = ($team->status ?? 'Published') === 'Published' ? 'green' : 'orange';
  $statusLabel = ($team->status ?? 'Published') === 'Published' ? 'Aktiv' : 'Inaktiv';

  $members = collect($team->members ?? []);
  $reserves = collect($team->reserves ?? []);

  $memberPreview = $members->take(4);
  $reservePreview = $reserves->take(3);
        @endphp

        <div class="oc-item">
          <div class="oc-item-row">
            <div class="oc-cell">
              <div class="oc-cell-title">ID</div>
              <span class="oc-id-badge">#{{ $team->id }}</span>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Team</div>
              <div class="oc-main">
                <div class="oc-ttl">{{ $team->name }}</div>
                <div class="oc-subt">{{ $team->description ?: 'Keine Beschreibung vorhanden' }}</div>
              </div>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Abteilung</div>
              <span class="oc-badge blue">{{ $team->department->department_name ?? '—' }}</span>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Teamleiter</div>

              @if($leaderEmployee && ($leaderEmployee->name || $leaderEmployee->lastname))
                <div class="oc-person">
                  <img
                    src="{{ $leaderImage }}"
                    alt="Teamleiter"
                    class="oc-avatar"
                    title="{{ trim(($leaderEmployee->lastname ?? '') . ' ' . ($leaderEmployee->name ?? '')) }}"
                  >
                  <div class="oc-person-meta">
                    <div class="oc-person-name">
                      {{ trim(($leaderEmployee->lastname ?? '') . ' ' . ($leaderEmployee->name ?? '')) }}
                    </div>
                    <div class="oc-person-role">Teamleitung</div>
                  </div>
                </div>
              @else
                <div class="oc-main">
                  <div class="oc-subt">Nicht festgelegt</div>
                </div>
              @endif
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Mitglieder / Reserve</div>

              <div class="oc-inline-count" style="margin-bottom:8px;">
                <strong>{{ $members->count() }}</strong>
                <span class="oc-subt" style="white-space:normal;overflow:visible;">Mitglieder</span>
                <span style="color:#d1d5db;">•</span>
                <strong>{{ $reserves->count() }}</strong>
                <span class="oc-subt" style="white-space:normal;overflow:visible;">Reserve</span>
              </div>

              <div class="oc-avatar-stack" style="margin-bottom:8px;">
                @foreach($memberPreview as $m)
                  @php
    $emp = optional($m->employee);
    $img = $emp && $emp->image ? asset('images/employee/' . $emp->image) : asset('images/employee/default.png');
    $name = trim(($emp->lastname ?? '') . ' ' . ($emp->name ?? ''));
    $position = optional($m->position)->position;
                  @endphp
                  <img
                    src="{{ $img }}"
                    class="oc-avatar oc-avatar-sm"
                    alt="Mitglied"
                    title="{{ $name }}@if($position) — {{ $position }}@endif"
                  >
                @endforeach

                @if($members->count() > 4)
                  <span class="oc-avatar-more" title="Weitere Mitglieder">+{{ $members->count() - 4 }}</span>
                @endif
              </div>

              @if($reserves->count())
                <div class="oc-avatar-stack">
                  @foreach($reservePreview as $r)
                    @php
      $emp = optional($r->employee);
      $img = $emp && $emp->image ? asset('images/employee/' . $emp->image) : asset('images/employee/default.png');
      $name = trim(($emp->lastname ?? '') . ' ' . ($emp->name ?? ''));
      $position = optional($r->position)->position;
                    @endphp
                    <img
                      src="{{ $img }}"
                      class="oc-avatar oc-avatar-sm"
                      alt="Reserve"
                      title="{{ $name }}@if($position) — {{ $position }}@endif"
                    >
                  @endforeach

                  @if($reserves->count() > 3)
                    <span class="oc-avatar-more" title="Weitere Reserven">+{{ $reserves->count() - 3 }}</span>
                  @endif
                </div>
              @endif
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Status</div>
              <span class="oc-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>

            <div class="oc-cell">
              <div class="oc-cell-title">Aktionen</div>
              <div class="oc-actions">
                <a href="{{ route('teams.edit', $team) }}" class="oc-btn-ic primary" title="Mitglieder verwalten">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </a>

                <form method="POST" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('Dieses Team wirklich löschen?')" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="oc-btn-ic danger" title="Löschen">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="oc-empty">Noch keine Teams vorhanden.</div>
      @endforelse
    </div>
  </div>

  @if($isPaginator && method_exists($teams, 'links') && $teams->hasPages())
    <div class="oc-pagination">
      <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
        <div style="font-size:12px;color:#6b7280;">
          Zeige <strong>{{ $teams->firstItem() ?? 0 }}</strong>
          bis <strong>{{ $teams->lastItem() ?? 0 }}</strong>
          von <strong>{{ $teams->total() }}</strong> Einträgen
        </div>
        <div>
          {{ $teams->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  @endif
</div>

<div class="oc-modal-backdrop" id="createTeamModal">
  <div class="oc-modal">
    <div class="oc-modal-h">
      <h3 class="oc-modal-ttl">Neues Team</h3>
      <button class="oc-btn-ic" type="button" onclick="closeModal('createTeamModal')">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('teams.store') }}">
      @csrf
      <div class="oc-modal-b">
        <div class="oc-form-grid">
          <div class="oc-form-group">
            <label class="oc-label">Teamname *</label>
            <input type="text" name="name" class="oc-input-form" value="{{ old('name') }}" required>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Abteilung *</label>
            <select name="department_id" class="oc-select" required>
              <option value="">— auswählen —</option>
              @foreach($departments as $d)
                <option value="{{ $d->id }}" @selected(old('department_id') == $d->id)>{{ $d->department_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Status</label>
            <select name="status" class="oc-select">
              <option value="Published" @selected(old('status', 'Published') === 'Published')>Aktiv</option>
              <option value="Unpublished" @selected(old('status') === 'Unpublished')>Inaktiv</option>
            </select>
          </div>

          <div class="oc-form-group" style="grid-column:1 / -1;">
            <label class="oc-label">Beschreibung</label>
            <textarea name="description" class="oc-textarea" rows="3">{{ old('description') }}</textarea>
          </div>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" onclick="closeModal('createTeamModal')">Abbrechen</button>
        <button type="submit" class="oc-btn">Speichern</button>
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
    wrap.appendChild(el);
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

  @if(session('success'))
    toast('ok', 'Erfolgreich', @json(session('success')));
  @endif

  @if($errors->any())
    toast('bad', 'Fehler', @json($errors->first()));
    openModal('createTeamModal');
  @endif
</script>
@endpush
@endonce

@push('scripts')
  <script>
    window.GlobalBreadcrumbs =[
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Mitarbeiterliste',
        url: "{{ url('emp?status_tab=active')}}",
      },
      {
        label: 'Teamliste',
        url: "{{url('admin/teams')}}", 
      }

    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush