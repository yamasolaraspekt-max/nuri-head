@extends('admin.layouts.app')
@section('title', 'Team verwalten: ' . $team->name)

@php
$leaderCount = $team->leader && $team->leader->employee ? 1 : 0;
$memberCount = $team->members->count();
$reserveCount = $team->reserves->count();
$poolCount = max(($deptEmployees->count() ?? 0) - ($team->membersAll->count() ?? 0), 0);
@endphp

@once
@push('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css">

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

  .oc-grid{
    display:grid;
    grid-template-columns:380px minmax(0, 1fr);
    gap:18px;
    align-items:start;
  }
  @media(max-width:1200px){
    .oc-grid{grid-template-columns:1fr;}
  }

  .oc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    box-shadow:var(--shadow-sm);
    overflow:hidden;
  }

  .oc-card-h{
    padding:16px 18px;
    border-bottom:1px solid var(--border);
    background:#fafafa;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
  }
  .oc-card-ttl{
    font-size:15px;
    font-weight:900;
    color:#111827;
    margin:0;
  }
  .oc-card-sub{
    font-size:12px;
    color:var(--text-muted);
    margin-top:4px;
  }
  .oc-card-b{
    padding:18px;
  }

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
    min-height:92px;
    resize:vertical;
  }
  .oc-form-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
    padding-top:4px;
  }

  .oc-search{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:10px;
    padding:10px 12px 10px 38px;
    font-size:14px;
    outline:none;
    width:100%;
    transition:var(--transition);
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:12px center;
    background-size:16px;
  }
  .oc-search:focus{
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-light);
  }

  .oc-stacks{
    display:grid;
    grid-template-columns:1.1fr 1fr 1fr;
    gap:16px;
  }
  @media(max-width:1200px){
    .oc-stacks{grid-template-columns:1fr;}
  }

  .oc-stack-wrap{
    min-width:0;
  }

  .oc-stack-title{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    margin-bottom:10px;
  }
  .oc-stack-title h4{
    margin:0;
    font-size:15px;
    font-weight:900;
    color:#111827;
  }
  .oc-stack-title span{
    font-size:12px;
    color:var(--text-muted);
    font-weight:700;
  }

  .oc-stack{
    min-height:180px;
    border:1px dashed #d1d5db;
    border-radius:16px;
    padding:12px;
    background:linear-gradient(180deg, #fbfbfd 0%, #f8fafc 100%);
    transition:var(--transition);
  }
  .oc-stack:hover{
    border-color:var(--primary);
    background:#fff;
  }

  .oc-person{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:10px;
    margin-bottom:10px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    transition:var(--transition);
    box-shadow:var(--shadow-sm);
  }
  .oc-person:last-child{margin-bottom:0;}
  .oc-person:hover{
    border-color:#d1d5db;
    box-shadow:0 8px 20px -15px rgba(0,0,0,.22);
  }

  .oc-person-info{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
    flex:1;
  }
  .oc-avatar{
    width:42px;
    height:42px;
    border-radius:999px;
    object-fit:cover;
    border:2px solid #fff;
    box-shadow:0 0 0 1px var(--border);
    background:#fff;
    flex:0 0 auto;
  }
  .oc-person-name{
    font-size:14px;
    font-weight:800;
    color:#111827;
    line-height:1.2;
  }
  .oc-person-role{
    font-size:12px;
    color:var(--text-muted);
    margin-top:3px;
  }

  .oc-note{
    font-size:12px;
    color:var(--text-muted);
    margin-top:10px;
    line-height:1.45;
  }

  .oc-inline-form{
    display:flex;
    align-items:flex-end;
    gap:12px;
    flex-wrap:wrap;
  }
  .oc-inline-item{
    display:flex;
    flex-direction:column;
    gap:6px;
    min-width:220px;
    flex:1;
  }

  .select2-container--bootstrap4 .select2-selection{
    border-radius:10px !important;
    border-color:var(--border) !important;
    min-height:40px !important;
    padding:4px 6px !important;
    font-size:14px !important;
    box-shadow:none !important;
  }
  .select2-container--bootstrap4.select2-container--focus .select2-selection{
    border-color:var(--primary) !important;
    box-shadow:0 0 0 3px var(--primary-light) !important;
  }
  .select2-container{width:100% !important;}
  .oc-person .select2-container{
    min-width:180px;
    max-width:220px;
  }
  @media(max-width:576px){
    .oc-person{
      flex-direction:column;
      align-items:stretch;
    }
    .oc-person .select2-container{
      max-width:100%;
    }
  }

  .oc-row{
    display:grid;
    grid-template-columns:1fr;
    gap:18px;
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
      padding:24px 18px;
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
        <div class="oc-title">TEAM VERWALTEN: {{ $team->name }}</div>
        <div class="oc-sub">
          {{ $team->department->department_name ?? '—' }} • Verwalten Sie Teamdaten, Leitung, Mitglieder und Reserve.
        </div>

        <div class="oc-breadcrumb">
          <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
          <span>›</span>
          <a href="{{ route('teams.index') }}">Teams</a>
          <span>›</span>
          <span class="current">Verwalten</span>
        </div>
      </div>

      <div>
        <a href="{{ route('teams.index') }}" class="oc-btn-soft">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5"></path>
            <path d="M12 19l-7-7 7-7"></path>
          </svg>
          Zurück
        </a>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon total">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Pool</div>
        <div class="oc-stat-value">{{ $poolCount }}</div>
        <div class="oc-stat-sub">Verfügbare Mitarbeitende</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon published">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Leitung</div>
        <div class="oc-stat-value">{{ $leaderCount }}</div>
        <div class="oc-stat-sub">Maximal eine Person</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon type">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="8.5" cy="7" r="4"/>
          <path d="M20 8v6M23 11h-6"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Mitglieder</div>
        <div class="oc-stat-value">{{ $memberCount }}</div>
        <div class="oc-stat-sub">Aktive Teammitglieder</div>
      </div>
    </div>

    <div class="oc-stat">
      <div class="oc-stat-icon unpublished">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M20 8v6M23 11h-6"/>
        </svg>
      </div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Reserve</div>
        <div class="oc-stat-value">{{ $reserveCount }}</div>
        <div class="oc-stat-sub">Ersatzmitglieder</div>
      </div>
    </div>
  </div>

  <div class="oc-grid">
    <div class="oc-row">
      <div class="oc-card">
        <div class="oc-card-h">
          <div>
            <h3 class="oc-card-ttl">Teamdaten</h3>
            <div class="oc-card-sub">Name, Status und Beschreibung des Teams bearbeiten.</div>
          </div>
        </div>

        <div class="oc-card-b">
          <form method="POST" action="{{ route('teams.update', $team) }}" id="metaForm">
            @csrf
            @method('PUT')

            <div class="oc-form-group">
              <label class="oc-label">Teamname</label>
              <input type="text" name="name" class="oc-input-form" value="{{ old('name', $team->name) }}" required>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Status</label>
              <select name="status" class="oc-select select2" data-placeholder="Status wählen">
                <option value="Published" {{ $team->status === 'Published' ? 'selected' : '' }}>Aktiv</option>
                <option value="Unpublished" {{ $team->status === 'Unpublished' ? 'selected' : '' }}>Inaktiv</option>
              </select>
            </div>

            <div class="oc-form-group">
              <label class="oc-label">Beschreibung</label>
              <textarea name="description" class="oc-textarea" rows="3">{{ old('description', $team->description) }}</textarea>
            </div>

            <div class="oc-form-actions">
              <button class="oc-btn" type="submit">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                  <path d="M17 21v-8H7v8"/>
                  <path d="M7 3v5h8"/>
                </svg>
                Speichern
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="oc-card">
        <div class="oc-card-h">
          <div>
            <h3 class="oc-card-ttl">Mitarbeitende</h3>
            <div class="oc-card-sub">{{ $team->department->department_name ?? '' }} • Per Drag & Drop in die Bereiche ziehen.</div>
          </div>
        </div>

        <div class="oc-card-b">
          <div class="oc-form-group">
            <input type="text" id="searchPool" class="oc-search" placeholder="Nach Namen suchen…">
          </div>

          <div id="pool" class="oc-stack">
            @php $usedIds = $team->membersAll->pluck('employee_id')->toArray(); @endphp

            @foreach($deptEmployees as $e)
              @continue(in_array($e->id, $usedIds))
              <div class="oc-person person" data-employee-id="{{ $e->id }}">
                <div class="oc-person-info">
                  <img src="{{ $e->image ? asset('images/employee/' . $e->image) : asset('images/employee/default.png') }}" alt="Foto" class="oc-avatar">
                  <div>
                    <div class="oc-person-name">{{ $e->lastname }} {{ $e->name }}</div>
                    <div class="oc-person-role">Verfügbar</div>
                  </div>
                </div>

                <div>
                  <select class="oc-select position select2" data-placeholder="Position wählen">
                    <option value=""></option>
                    @foreach($positions as $p)
                      <option value="{{ $p->id }}">{{ $p->position }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            @endforeach
          </div>

          <div class="oc-note">
            Ziehen Sie Personen in <strong>Leitung</strong>, <strong>Mitglieder</strong> oder <strong>Reserve</strong>. Die Leitung darf nur eine Person enthalten.
          </div>
        </div>
      </div>
    </div>

    <div class="oc-row">
      <div class="oc-card">
        <div class="oc-card-h">
          <div>
            <h3 class="oc-card-ttl">Teamstruktur</h3>
            <div class="oc-card-sub">Ordnen Sie Personen per Drag & Drop zu und speichern Sie anschließend die Teamstruktur.</div>
          </div>
        </div>

        <div class="oc-card-b">
          <div class="oc-stacks">
            <div class="oc-stack-wrap">
              <div class="oc-stack-title">
                <h4>Leitung</h4>
                <span>Max. 1</span>
              </div>

              <div id="leader" class="oc-stack">
                @if($team->leader && $team->leader->employee)
                  <div class="oc-person person" data-employee-id="{{ $team->leader->employee_id }}">
                    <div class="oc-person-info">
                      <img src="{{ $team->leader->employee->image ? asset('images/employee/' . $team->leader->employee->image) : asset('images/employee/default.png') }}" alt="Foto" class="oc-avatar">
                      <div>
                        <div class="oc-person-name">{{ $team->leader->employee->lastname }} {{ $team->leader->employee->name }}</div>
                        <div class="oc-person-role">Leitung</div>
                      </div>
                    </div>

                    <div>
                      <select class="oc-select position select2" data-placeholder="Position wählen">
                        <option value=""></option>
                        @foreach($positions as $p)
                          <option value="{{ $p->id }}" {{ $team->leader->position_id == $p->id ? 'selected' : '' }}>{{ $p->position }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                @endif
              </div>
            </div>

            <div class="oc-stack-wrap">
              <div class="oc-stack-title">
                <h4>Mitglieder</h4>
                <span>{{ $team->members->count() }} Personen</span>
              </div>

              <div id="members" class="oc-stack">
                @foreach($team->members as $m)
                  <div class="oc-person person" data-employee-id="{{ $m->employee_id }}">
                    <div class="oc-person-info">
                      <img src="{{ $m->employee->image ? asset('images/employee/' . $m->employee->image) : asset('images/employee/default.png') }}" alt="Foto" class="oc-avatar">
                      <div>
                        <div class="oc-person-name">{{ $m->employee->lastname }} {{ $m->employee->name }}</div>
                        <div class="oc-person-role">Mitglied</div>
                      </div>
                    </div>

                    <div>
                      <select class="oc-select position select2" data-placeholder="Position wählen">
                        <option value=""></option>
                        @foreach($positions as $p)
                          <option value="{{ $p->id }}" {{ $m->position_id == $p->id ? 'selected' : '' }}>{{ $p->position }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="oc-stack-wrap">
              <div class="oc-stack-title">
                <h4>Reserve</h4>
                <span>{{ $team->reserves->count() }} Personen</span>
              </div>

              <div id="reserves" class="oc-stack">
                @foreach($team->reserves as $r)
                  <div class="oc-person person" data-employee-id="{{ $r->employee_id }}">
                    <div class="oc-person-info">
                      <img src="{{ $r->employee->image ? asset('images/employee/' . $r->employee->image) : asset('images/employee/default.png') }}" alt="Foto" class="oc-avatar">
                      <div>
                        <div class="oc-person-name">{{ $r->employee->lastname }} {{ $r->employee->name }}</div>
                        <div class="oc-person-role">Reserve</div>
                      </div>
                    </div>

                    <div>
                      <select class="oc-select position select2" data-placeholder="Position wählen">
                        <option value=""></option>
                        @foreach($positions as $p)
                          <option value="{{ $p->id }}" {{ $r->position_id == $p->id ? 'selected' : '' }}>{{ $p->position }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="oc-form-actions" style="margin-top:18px;">
            <button id="saveMembers" class="oc-btn" type="button">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <path d="M17 21v-8H7v8"/>
                <path d="M7 3v5h8"/>
              </svg>
              Teammitglieder speichern
            </button>
          </div>
        </div>
      </div>

      <div class="oc-card">
        <div class="oc-card-h">
          <div>
            <h3 class="oc-card-ttl">Reserve befördern</h3>
            <div class="oc-card-sub">Übernehmen Sie eine Reserve direkt in das Team und ersetzen Sie optional ein Mitglied.</div>
          </div>
        </div>

        <div class="oc-card-b">
          <form method="POST" action="{{ route('teams.promote.reserve', $team) }}" id="promoteForm">
            @csrf

            <div class="oc-inline-form">
              <div class="oc-inline-item">
                <label class="oc-label">Reserve wählen</label>
                <select name="reserve_employee_id" class="oc-select select2" data-placeholder="Reserve wählen">
                  <option value=""></option>
                  @foreach($team->reserves as $r)
                    <option value="{{ $r->employee_id }}">{{ $r->employee->lastname }} {{ $r->employee->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="oc-inline-item">
                <label class="oc-label">Mitglied ersetzen (optional)</label>
                <select name="replace_member_employee_id" class="oc-select select2" data-placeholder="Mitglied wählen">
                  <option value=""></option>
                  @foreach($team->members as $m)
                    <option value="{{ $m->employee_id }}">{{ $m->employee->lastname }} {{ $m->employee->name }}</option>
                  @endforeach
                </select>
              </div>

              <div style="display:flex;align-items:flex-end;">
                <button class="oc-btn-soft" type="submit">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"></path>
                  </svg>
                  Übernehmen
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="oc-toast-wrap" id="toast-wrap"></div>

@if(session('success'))
  <script>window._flashSuccess = @json(session('success'));</script>
@endif
@if($errors->any())
  <script>window._flashErrors = @json($errors->all());</script>
@endif
@endsection

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
(function() {
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

  if (window._flashSuccess) {
    toast('ok', 'Erfolgreich', window._flashSuccess);
    Swal.fire({ icon: 'success', title: 'Erfolgreich', text: window._flashSuccess, confirmButtonText: 'OK' });
  }

  if (window._flashErrors && window._flashErrors.length) {
    toast('bad', 'Fehler', window._flashErrors[0]);
    Swal.fire({
      icon: 'error',
      title: 'Fehler',
      html: '<ul style="text-align:left;margin:0;padding-left:18px;">' +
            window._flashErrors.map(e => `<li>${e}</li>`).join('') +
            '</ul>',
      confirmButtonText: 'OK'
    });
  }

  function initSelect2(scope) {
    var $scope = scope ? $(scope) : $(document);
    $scope.find('select.select2').each(function(){
      var $el = $(this);
      if ($el.hasClass('select2-hidden-accessible')) return;
      $el.select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: $el.data('placeholder') || '',
        allowClear: true,
        dropdownParent: $('body')
      });
    });
  }
  initSelect2();

  var searchPool = document.getElementById('searchPool');
  if (searchPool) {
    searchPool.addEventListener('input', function(){
      var q = this.value.toLowerCase();
      document.querySelectorAll('#pool .person').forEach(function(el){
        el.style.display = el.innerText.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
      });
    });
  }

  ['pool','leader','members','reserves'].forEach(function(id){
    var target = document.getElementById(id);
    if (!target) return;

    new Sortable(target, {
      group: 'people',
      animation: 150,
      sort: true,
      onAdd: function(evt) {
        if (evt.to.id === 'leader' && evt.to.children.length > 1) {
          evt.from.appendChild(evt.item);
          Swal.fire({ icon: 'warning', title: 'Hinweis', text: 'Die Leitung darf nur eine Person enthalten.' });
          return;
        }
        initSelect2(evt.item);
      },
      onUpdate: function(evt){
        initSelect2(evt.item);
      }
    });
  });

  function collectStack(containerId) {
    var arr = [];
    document.querySelectorAll('#'+containerId+' .person').forEach(function(el, idx){
      var $select = $(el).find('select.position');
      var posVal = $select.length ? $select.val() : null;
      arr.push({
        employee_id: el.getAttribute('data-employee-id'),
        position_id: posVal || null,
        sort_order: idx
      });
    });
    return arr;
  }

  function collectPayload() {
    var leader = collectStack('leader');
    if (leader.length > 1) leader = [leader[0]];
    return {
      leader: leader,
      members: collectStack('members'),
      reserves: collectStack('reserves')
    };
  }

  var saveMembers = document.getElementById('saveMembers');
  if (saveMembers) {
    saveMembers.addEventListener('click', function(){
      var payload = collectPayload();

      fetch("{{ route('teams.members.sync', $team) }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
      })
      .then(r => {
        if (!r.ok) throw new Error('Speichern fehlgeschlagen');
        return r.text();
      })
      .then(() => {
        toast('ok', 'Gespeichert', 'Teammitglieder wurden aktualisiert.');
        Swal.fire({ icon:'success', title:'Gespeichert', text:'Teammitglieder wurden aktualisiert.' })
          .then(() => location.reload());
      })
      .catch(err => {
        toast('bad', 'Fehler', err.message || 'Fehler beim Speichern.');
        Swal.fire({ icon:'error', title:'Fehler', text: err.message || 'Fehler beim Speichern.' });
      });
    });
  }

  var promoteForm = document.getElementById('promoteForm');
  if (promoteForm) {
    promoteForm.addEventListener('submit', function(e){
      e.preventDefault();
      var form = this;
      var fd = new FormData(form);

      if (!fd.get('reserve_employee_id')) {
        Swal.fire({ icon:'warning', title:'Hinweis', text:'Bitte eine Reserve-Person auswählen.' });
        return;
      }

      fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: fd
      })
      .then(r => {
        if (!r.ok) throw new Error('Aktion fehlgeschlagen');
        return r.text();
      })
      .then(() => {
        toast('ok', 'Befördert', 'Die Reserve wurde in das Team übernommen.');
        Swal.fire({ icon:'success', title:'Befördert', text:'Die Reserve wurde in das Team übernommen.' })
          .then(() => location.reload());
      })
      .catch(err => {
        toast('bad', 'Fehler', err.message || 'Aktion fehlgeschlagen.');
        Swal.fire({ icon:'error', title:'Fehler', text: err.message || 'Aktion fehlgeschlagen.' });
      });
    });
  }
})();
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
        label: 'Mitarbeiterliste',
        url: "{{ url('emp?status_tab=active')}}",
      },
      {
        label: 'Teamliste',
        url: "{{url('admin/teams')}}",
      },
      {
        label: '{{ $team->name }}',
        url: "{{ url()->current() }}",
        clickable:false
      }

    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush