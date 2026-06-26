@extends('admin.layouts.app')

@section('title', 'Abteilung')

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

<style>
    :root{
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
        --radius:16px;
        --transition:all .2s ease-in-out;
    }

    .dp-wrap{
        font-family:Inter, system-ui, -apple-system, sans-serif;
        color:var(--text-main); 
    }

    .dp-header{
        margin-bottom:18px; 
    }

    .dp-titlebar{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:12px;
        margin-bottom:16px;
        flex-wrap:wrap;
    }

    .dp-title{
        font-size:26px;
        font-weight:800;
        letter-spacing:-.025em;
        color:#111827;
    }

    .dp-sub{
        font-size:14px;
        color:var(--text-muted);
        margin-top:4px;
    }

    .dp-breadcrumb{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:8px;
        margin-top:10px;
        font-size:13px;
        color:var(--text-muted);
    }

    .dp-breadcrumb a{
        color:var(--text-muted);
        text-decoration:none;
        font-weight:700;
    }

    .dp-breadcrumb a:hover{color:var(--text-main);}
    .dp-breadcrumb span.current{color:#111827;font-weight:800;}

    .dp-btn{
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

    .dp-btn:hover{
        background:var(--primary-hover);
        color:#fff;
        text-decoration:none;
    }

    .dp-btn-soft{
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

    .dp-btn-soft:hover{
        background:#f9fafb;
        color:var(--text-main);
        text-decoration:none;
    }

    .dp-btn-ic{
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

    .dp-btn-ic:hover{
        background:#f9fafb;
        color:var(--text-main);
        border-color:#d1d5db;
        text-decoration:none;
    }

    .dp-btn-ic.primary{color:var(--primary);border-color:var(--primary-light);background:var(--primary-light)}
    .dp-btn-ic.primary:hover{border-color:var(--primary)}
    .dp-btn-ic.success{color:var(--success);border-color:#c7f2df;background:var(--success-light)}
    .dp-btn-ic.success:hover{border-color:var(--success)}
    .dp-btn-ic.warning{color:#d97706;border-color:#fde7b0;background:#fffbeb}
    .dp-btn-ic.warning:hover{border-color:#f59e0b}
    .dp-btn-ic.danger{color:var(--danger);border-color:rgba(239,68,68,.18);background:var(--danger-light)}
    .dp-btn-ic.danger:hover{border-color:rgba(239,68,68,.35)}

    .dp-analytics{
        display:grid;
        grid-template-columns:repeat(4, minmax(0,1fr));
        gap:14px;
        margin-bottom:18px;
    }

    @media(max-width:1200px){.dp-analytics{grid-template-columns:repeat(2, minmax(0,1fr));}}
    @media(max-width:700px){.dp-analytics{grid-template-columns:1fr;}}

    .dp-stat{
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

    .dp-stat-icon{
        width:48px;
        height:48px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }

    .dp-stat-icon.total{background:var(--blue-light);color:var(--blue)}
    .dp-stat-icon.active{background:var(--success-light);color:var(--success)}
    .dp-stat-icon.head{background:var(--warning-light);color:#d97706}
    .dp-stat-icon.employee{background:var(--gray-light);color:var(--gray)}

    .dp-stat-meta{min-width:0}
    .dp-stat-label{
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        letter-spacing:.06em;
    }
    .dp-stat-value{
        font-size:24px;
        font-weight:900;
        color:#111827;
        line-height:1.1;
        margin-top:4px;
    }
    .dp-stat-sub{
        font-size:12px;
        color:var(--text-muted);
        margin-top:4px;
    }

    .dp-toolbar{
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

    .dp-toolbar-left,.dp-toolbar-right{
        display:flex;
        align-items:flex-end;
        gap:12px;
        flex-wrap:wrap;
    }
    .dp-toolbar-left{flex:1}

    .dp-filter-block{
        display:flex;
        flex-direction:column;
        gap:6px;
        min-width:170px;
    }

    .dp-filter-block.search{
        flex:1;
        min-width:280px;
    }

    .dp-filter-label{
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .dp-input,.dp-select{
        background:#f9fafb;
        border:1px solid var(--border);
        border-radius:8px;
        padding:10px 12px;
        font-size:14px;
        outline:none;
        transition:var(--transition);
        width:100%;
        min-width:160px;
    }

    .dp-input.search{
        padding-left:36px;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
        background-repeat:no-repeat;
        background-position:10px center;
        background-size:16px;
    }

    .dp-input:focus,.dp-select:focus{
        background:#fff;
        border-color:var(--primary);
        box-shadow:0 0 0 3px var(--primary-light);
    }

    .dp-sort-group{
        display:inline-flex;
        border-radius:999px;
        overflow:hidden;
        border:1px solid rgba(148,163,184,.7);
        background:#f9fafb;
    }

    .dp-sort-trigger{
        padding:8px 12px;
        font-size:11px;
        border:none;
        background:transparent;
        cursor:pointer;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#6b7280;
        font-weight:800;
    }

    .dp-sort-trigger + .dp-sort-trigger{
        border-left:1px solid rgba(148,163,184,.5);
    }

    .dp-sort-trigger.active{
        background:#4f46e5;
        color:#fff;
    }

    .dp-card{
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        box-shadow:var(--shadow-sm);
        overflow:hidden;
    }

    .dp-list-head{
        display:grid;
        grid-template-columns:70px minmax(240px,1.6fr) minmax(170px,1fr) minmax(170px,1fr) minmax(140px,.9fr) minmax(140px,.9fr) 120px 140px;
        gap:14px;
        align-items:center;
        padding:16px 16px 10px 16px;
        color:var(--text-muted);
        font-size:11px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    @media(max-width:1380px){.dp-list-head{display:none;}}

    .dp-list{
        display:flex;
        flex-direction:column;
        gap:12px;
        padding:0 0 16px 0;
    }

    .dp-row{
        background:var(--card-bg);
        border:1px solid var(--border);
        border-radius:var(--radius);
        transition:var(--transition);
        overflow:visible;
        margin:0 16px;
        position:relative;
    }

    .dp-row:hover{
        border-color:var(--primary);
        box-shadow:var(--shadow);
    }

    .dp-row-inner{
        padding:16px;
        display:grid;
        gap:16px;
        align-items:center;
        grid-template-columns:70px minmax(240px,1.6fr) minmax(170px,1fr) minmax(170px,1fr) minmax(140px,.9fr) minmax(140px,.9fr) 120px 140px;
    }

    @media(max-width:1380px){.dp-row-inner{grid-template-columns:1fr;}}

    .dp-cell{min-width:0}
    .dp-cell-title{
        font-size:11px;
        font-weight:800;
        color:var(--text-muted);
        text-transform:uppercase;
        margin-bottom:4px;
        display:none;
    }
    @media(max-width:1380px){.dp-cell-title{display:block;}}

    .dp-id-badge{
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

    .dp-main{
        display:flex;
        flex-direction:column;
        min-width:0;
    }
    .dp-name{
        font-weight:800;
        font-size:15px;
        margin-bottom:4px;
        color:#111827;
        display:flex;
        align-items:center;
        gap:8px;
        flex-wrap:wrap;
    }
    .dp-subline{
        font-size:13px;
        color:var(--text-muted);
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .dp-name a{
        color:#111827;
        text-decoration:none;
    }
    .dp-name a:hover{text-decoration:underline;}

    .toggle-children{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:22px;
        height:22px;
        border-radius:999px;
        border:1px solid rgba(148,163,184,.7);
        background:#f9fafb;
        transition:var(--transition);
        cursor:pointer;
        flex:0 0 auto;
    }

    .toggle-children i{
        font-size:12px;
        color:#4b5563;
    }

    .toggle-children:hover{
        background:#e5e7eb;
        border-color:#4f46e5;
        box-shadow:0 4px 10px rgba(148,163,184,.35);
        transform:translateY(-1px);
    }

    .dp-person{
        display:inline-flex;
        align-items:center;
        gap:6px;
        font-size:14px;
        font-weight:700;
        color:#111827;
        white-space:nowrap;
    }

    .dp-person::before{
        content:"";
        width:8px;
        height:8px;
        border-radius:999px;
        display:inline-block;
        flex:0 0 auto;
    }

    .dp-person.head::before{background:#22c55e;}
    .dp-person.rep::before{background:#3b82f6;}

    .dp-assign-btn{
        border:none;
        background:var(--primary-light);
        color:#4d6d11;
        font-size:12px;
        font-weight:800;
        border-radius:999px;
        padding:7px 10px;
        cursor:pointer;
        transition:var(--transition);
    }
    .dp-assign-btn:hover{background:#e8f5c8;}

    .dp-customer-main{
        font-weight:900;
        font-size:16px;
        color:#111827;
    }

    .dp-customer-sub{
        font-size:12px;
        color:var(--text-muted);
        line-height:1.45;
        margin-top:4px;
    }

    .dp-cost{
        white-space:nowrap;
        font-weight:900;
        font-size:15px;
        color:#111827;
    }

    .dp-status-pill{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:6px 10px;
        border-radius:999px;
        font-size:12px;
        font-weight:900;
        white-space:nowrap;
    }
    .dp-status-pill.green{background:#ecfdf5;color:#047857;}
    .dp-status-pill.red{background:#fef2f2;color:#b91c1c;}

    .dept-actions-cell{
        position:relative;
        white-space:nowrap;
    }

    .dept-actions-wrapper{
        position:relative;
        display:inline-block;
    }

    .dept-actions-trigger{
        width:36px;
        height:36px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:0;
        cursor:pointer;
        transition:var(--transition);
    }

    .dept-actions-trigger i{
        font-size:1rem;
        color:#4b5563;
    }

    .dept-actions-wrapper.open .dept-actions-trigger{
        background:#eef2ff;
        border-color:#4f46e5;
    }

    .dept-actions-menu{
        position:absolute;
        right:0;
        top:110%;
        min-width:210px;
        padding:6px;
        border-radius:12px;
        background:#ffffff;
        box-shadow:0 14px 30px rgba(15,23,42,.18);
        border:1px solid rgba(148,163,184,.35);
        z-index:999;
        display:none;
        max-height:280px;
        overflow-y:auto;
    }

    .dept-actions-wrapper.open .dept-actions-menu{display:block;}
    .dept-actions-wrapper.drop-up .dept-actions-menu{top:auto;bottom:110%;}

    .dept-actions-item{
        width:100%;
        border:none;
        background:transparent;
        display:flex;
        align-items:center;
        gap:8px;
        padding:8px 10px;
        border-radius:8px;
        font-size:13px;
        color:#374151;
        cursor:pointer;
        text-align:left;
    }

    .dept-actions-item i{
        font-size:14px;
        color:#6b7280;
    }

    .dept-actions-item:hover{
        background:#eff6ff;
        color:#111827;
    }

    .dp-empty{
        text-align:center;
        padding:60px;
        color:var(--text-muted);
        background:#fff;
        border:1px dashed var(--border);
        border-radius:16px;
        margin:16px;
    }

    .dp-modal-backdrop{
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

    .dp-modal-backdrop.open{
        opacity:1;
        pointer-events:auto;
    }

    .dp-modal{
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

    .dp-modal-backdrop.open .dp-modal{transform:translateY(0) scale(1)}

    .dp-modal-h{
        display:flex;
        gap:12px;
        align-items:center;
        justify-content:space-between;
        padding:16px 18px;
        border-bottom:1px solid var(--border);
        background:#fafafa;
    }

    .dp-modal-ttl{
        font-weight:900;
        font-size:16px;
        line-height:1.2;
        margin:0;
        color:#111827;
    }

    .dp-modal-b{
        padding:20px 18px;
        max-height:72vh;
        overflow-y:auto;
    }

    .dp-modal-f{
        padding:14px 18px;
        border-top:1px solid var(--border);
        background:#fafafa;
        display:flex;
        gap:10px;
        justify-content:flex-end;
        flex-wrap:wrap;
    }

    .dp-form-grid{
        display:grid;
        grid-template-columns:repeat(2, minmax(0,1fr));
        gap:16px;
    }

    @media(max-width:760px){.dp-form-grid{grid-template-columns:1fr;}}

    .dp-form-group{margin-bottom:16px;}
    .dp-label{
        display:block;
        font-size:13px;
        font-weight:700;
        color:var(--text-main);
        margin-bottom:6px;
    }

    .dp-input-form,.dp-select-form{
        width:100%;
        padding:10px 12px;
        border-radius:8px;
        border:1px solid var(--border);
        background:#fff;
        font-size:14px;
        outline:none;
        transition:var(--transition);
    }

    .dp-input-form:focus,.dp-select-form:focus{
        border-color:var(--primary);
        box-shadow:0 0 0 3px var(--primary-light);
    }

    .select2-container--default .select2-selection--single{
        border-radius:8px !important;
        border:1px solid var(--border) !important;
        height:42px !important;
        background:#fff !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height:40px !important;
        padding-left:12px !important;
        font-size:14px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height:40px !important;
    }
</style>
@endsection

@section('content')
<div class="dp-wrap">
    <div class="dp-header">
        <div class="dp-titlebar">
            <div>
                <div class="dp-title">ABTEILUNG</div>
                <div class="dp-sub">Verwalten Sie Abteilungen, Hierarchien, Leiter, Stellvertretungen und Mitarbeiter zentral.</div>

                <div class="dp-breadcrumb">
                    <a href="{{ url('/') }}">Dashboard</a>
                    <span>›</span>
                    <span class="current">Abteilung Liste</span>
                </div>
            </div>

            <div>
                <button type="button" class="dp-btn" onclick="openDpModal('createDepartmentModal')">
                    <i class="feather icon-plus"></i>
                    Neue Abteilung
                </button>
            </div>
        </div>
    </div>

    <div class="dp-analytics">
        <div class="dp-stat">
            <div class="dp-stat-icon total">
                <i class="feather icon-layers"></i>
            </div>
            <div class="dp-stat-meta">
                <div class="dp-stat-label">Abteilungen</div>
                <div class="dp-stat-value" id="deptTotalCount">{{ $analytics['total'] ?? 0 }}</div>
                <div class="dp-stat-sub" id="deptActivePctPill">—</div>
            </div>
        </div>

        <div class="dp-stat">
            <div class="dp-stat-icon active">
                <i class="feather icon-check-circle"></i>
            </div>
            <div class="dp-stat-meta">
                <div class="dp-stat-label">Aktiv</div>
                <div class="dp-stat-value" id="deptActiveCount">{{ $analytics['active'] ?? 0 }}</div>
                <div class="dp-stat-sub">Inaktiv: <span id="deptInactiveCount">{{ $analytics['inactive'] ?? 0 }}</span></div>
            </div>
        </div>

        <div class="dp-stat">
            <div class="dp-stat-icon head">
                <i class="feather icon-user-check"></i>
            </div>
            <div class="dp-stat-meta">
                <div class="dp-stat-label">Leiter / Ohne Leiter</div>
                <div class="dp-stat-value">
                    <span id="deptWithHeadCount">{{ $analytics['with_head'] ?? 0 }}</span>
                    <span style="font-size:14px;color:#6b7280;"> / </span>
                    <span id="deptWithoutHeadCount">{{ $analytics['without_head'] ?? 0 }}</span>
                </div>
                <div class="dp-stat-sub">Mit definierter Leitung</div>
            </div>
        </div>

        <div class="dp-stat">
            <div class="dp-stat-icon employee">
                <i class="feather icon-users"></i>
            </div>
            <div class="dp-stat-meta">
                <div class="dp-stat-label">Mitarbeiter</div>
                <div class="dp-stat-value" id="deptEmployeesCount">{{ $analytics['employees'] ?? 0 }}</div>
                <div class="dp-stat-sub">Über alle Abteilungen des Betriebs</div>
            </div>
        </div>
    </div>

    <div class="dp-toolbar">
        <div class="dp-toolbar-left">
            <div class="dp-filter-block search">
                <label class="dp-filter-label">Suche</label>
                <input type="text" id="searchDepartment" class="dp-input search" placeholder="Abteilung suchen…">
            </div>

            <div class="dp-filter-block">
                <label class="dp-filter-label">Betrieb</label>
                <select name="filter" id="branchFilter" class="dp-select">
                    @foreach ($branch as $br)
                        <option value="{{ $br->id }}" {{ $selectedBranch == $br->id ? 'selected' : '' }}>
                            {{ $br->branch }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="dp-filter-block">
                <label class="dp-filter-label">Von Datum</label>
                <input type="date" id="dateFrom" class="dp-input" value="{{ $dateFrom ?? '' }}">
            </div>

            <div class="dp-filter-block">
                <label class="dp-filter-label">Bis Datum</label>
                <input type="date" id="dateTo" class="dp-input" value="{{ $dateTo ?? '' }}">
            </div>

            <div class="dp-filter-block">
                <label class="dp-filter-label">Status</label>
                <select id="statusFilter" class="dp-select">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Alle</option>
                    <option value="active" {{ ($statusFilter ?? 'all') === 'active' ? 'selected' : '' }}>Nur aktiv</option>
                    <option value="inactive" {{ ($statusFilter ?? 'all') === 'inactive' ? 'selected' : '' }}>Nur inaktiv</option>
                </select>
            </div>

            <div class="dp-filter-block">
                <label class="dp-filter-label">Leiter</label>
                <select id="headFilter" class="dp-select">
                    <option value="all" {{ ($headFilter ?? 'all') === 'all' ? 'selected' : '' }}>Alle</option>
                    <option value="with_head" {{ ($headFilter ?? 'all') === 'with_head' ? 'selected' : '' }}>Nur mit Leiter</option>
                    <option value="without_head" {{ ($headFilter ?? 'all') === 'without_head' ? 'selected' : '' }}>Nur ohne Leiter</option>
                </select>
            </div>
        </div>

        <div class="dp-toolbar-right">
            <div class="dp-filter-block">
                <label class="dp-filter-label">Sortierung</label>
                <div class="dp-sort-group" id="deptSortGroup">
                    <button type="button" class="dp-sort-trigger {{ ($sortBy ?? 'order') === 'order' ? 'active' : '' }}" data-sort-by="order" data-sort-dir="asc">Standard</button>
                    <button type="button" class="dp-sort-trigger {{ ($sortBy ?? '') === 'name' && ($sortDir ?? '') === 'asc' ? 'active' : '' }}" data-sort-by="name" data-sort-dir="asc">Name A–Z</button>
                    <button type="button" class="dp-sort-trigger {{ ($sortBy ?? '') === 'name' && ($sortDir ?? '') === 'desc' ? 'active' : '' }}" data-sort-by="name" data-sort-dir="desc">Name Z–A</button>
                    <button type="button" class="dp-sort-trigger {{ ($sortBy ?? '') === 'created_at' && ($sortDir ?? '') === 'desc' ? 'active' : '' }}" data-sort-by="created_at" data-sort-dir="desc">Neueste</button>
                </div>
            </div>

            <button class="dp-btn-soft" type="button" onclick="loadDepartments()">Suchen</button>
        </div>
    </div>

    <div class="dp-card">
        <div class="dp-list-head">
            <div>ID</div>
            <div>Abteilung</div>
            <div>Abteilungsleiter</div>
            <div>Stellvertretung</div>
            <div>Kunden</div>
            <div>Kosten (Monat)</div>
            <div>Status</div>
            <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="dp-list" id="sortable">
            @include('admin.employee.department.partials.table_rows', [
    'structuredDepartments' => $structuredDepartments ?? collect(),
    'employeeData' => $employeeData ?? collect(),
    'departmentStats' => $departmentStats ?? [],
])
        </div>
    </div>
</div>

<div class="dp-modal-backdrop" id="createDepartmentModal">
    <div class="dp-modal">
        <div class="dp-modal-h">
            <h3 class="dp-modal-ttl">Neue Abteilung</h3>
            <button class="dp-btn-ic" type="button" onclick="closeDpModal('createDepartmentModal')">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <form id="createDepartmentForm" novalidate>
            @csrf
            <div class="dp-modal-b">
                <div class="dp-form-grid">
                    <div class="dp-form-group" style="grid-column:1 / -1;">
                        <label class="dp-label">Abteilung</label>
                        <input type="text" class="dp-input-form" name="department_name" required>
                    </div>

                    <div class="dp-form-group">
                        <label class="dp-label">Betrieb</label>
                        <select name="branch_id" id="branchFilters" class="selectables dp-select-form" style="width:100%">
                            <option value="">-- Betrieb wählen --</option>
                            @foreach ($branch as $br)
                                <option value="{{ $br->id }}">{{ $br->branch }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dp-form-group">
                        <label class="dp-label">Master-Abteilung</label>
                        <select name="parent_id" id="parentDepartmentFilter" class="selectables dp-select-form" style="width:100%">
                            <option value="">-- Master-Abteilung wählen --</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="dp-modal-f">
                <button type="button" class="dp-btn-soft" onclick="closeDpModal('createDepartmentModal')">Abbrechen</button>
                <button type="submit" class="dp-btn">Speichern</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    const CSRF_TOKEN        = '{{ csrf_token() }}';
    const ROUTE_INDEX       = '{{ route('department.info') }}';
    const ROUTE_CREATE      = '{{ route('department.create') }}';
    const ROUTE_UPDATE      = '{{ route('department.update') }}';
    const ROUTE_DESTROY     = '{{ route('department.destroy', ['id' => '___ID___']) }}'.replace('%5B%22id%22%5D', '___ID___');
    const ROUTE_ORDER       = '{{ route('departments.update.order') }}';
    const ROUTE_FILTER_BRANCH = '{{ route('departments.filterByBranch') }}';
    const ROUTE_GET_EMP     = '{{ route('orgnaization.get.employee') }}';
    const ROUTE_STORE_HEAD  = '{{ route('orgnaization.store.head.employee') }}';
    const ROUTE_STORE_REP   = '{{ route('orgnaization.store.representative.employee') }}';
    const ROUTE_POSITIONS_ASSIGN    = '{{ route('department.positions.assign') }}';
    const ROUTE_POSITIONS_REMAINING = '{{ route('department.positions.remaining') }}';
    const ROUTE_DEPT_EMPLOYEES      = '{{ route('departments.employees', ['department' => '___ID___']) }}';
    const ROUTE_POSITIONS_UPDATE    = '{{ route('department.positions.update') }}';
    const POSITIONS = @json($positions ?? []);
    const INITIAL_ANALYTICS = @json($analytics ?? []);
    window.DEPT_SORT_BY  = @json($sortBy ?? 'order');
    window.DEPT_SORT_DIR = @json($sortDir ?? 'asc');

    function openDpModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('open');
    }

    function closeDpModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('open');
    }

    function refreshFeather() {
        if (window.feather) feather.replace();
    }

    function updateAnalyticsCards(analytics) {
        if (!analytics) return;

        const total        = Number(analytics.total ?? 0);
        const active       = Number(analytics.active ?? 0);
        const inactive     = Number(analytics.inactive ?? 0);
        const withHead     = Number(analytics.with_head ?? 0);
        const withoutHead  = Number(analytics.without_head ?? 0);
        const employees    = Number(analytics.employees ?? 0);

        $('#deptTotalCount').text(total);
        $('#deptActiveCount').text(active);
        $('#deptInactiveCount').text(inactive);
        $('#deptWithHeadCount').text(withHead);
        $('#deptWithoutHeadCount').text(withoutHead);
        $('#deptEmployeesCount').text(employees);

        const pctActive = total > 0 ? Math.round((active / total) * 100) : 0;
        $('#deptActivePctPill').text(pctActive + '% aktiv');
    }

    function toastSuccess(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: msg,
            showConfirmButton: false,
            timer: 2000
        });
    }

    function toastError(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: msg,
            showConfirmButton: false,
            timer: 2500
        });
    }

    window.toggleChildren = function (parentId) {
        const firstChild = document.querySelector(`.dp-row[data-parent='${parentId}']`);
        if (!firstChild) return;

        const shouldShow = firstChild.style.display === 'none' || firstChild.style.display === '';

        const toggleRecursive = (id, show) => {
            const rows = document.querySelectorAll(`.dp-row[data-parent='${id}']`);
            rows.forEach(row => {
                row.style.display = show ? 'block' : 'none';
                if (!show) {
                    const childId = row.getAttribute('data-id');
                    toggleRecursive(childId, false);
                }
            });
        };

        toggleRecursive(parentId, shouldShow);

        const icon = document.querySelector(`.dp-row[data-id='${parentId}'] .toggle-children i`);
        if (icon) {
            icon.classList.toggle('icon-chevron-down', shouldShow);
            icon.classList.toggle('icon-chevron-up', !shouldShow);
        }
    };

    window.initializeDragAndDrop = function () {
        const sortableTable = document.getElementById('sortable');
        if (!sortableTable || typeof Sortable === 'undefined') return;

        new Sortable(sortableTable, {
            group: "departments",
            animation: 150,
            draggable: ".dp-row",
            onEnd: function () {
                const rows = document.querySelectorAll("#sortable .dp-row");
                const departmentData = [];

                rows.forEach((row, index) => {
                    const rowId = row.getAttribute("data-id");
                    let rowParentId = row.getAttribute("data-parent");
                    rowParentId = rowParentId && rowParentId !== "null" ? rowParentId : null;

                    departmentData.push({
                        id: rowId,
                        parent_id: rowParentId,
                        order: index + 1
                    });
                });

                $.ajax({
                    url: ROUTE_ORDER,
                    type: "POST",
                    data: {
                        departments: departmentData,
                        _token: CSRF_TOKEN
                    },
                    success: function (response) {
                        if (response.success) {
                            toastSuccess("Die Abteilungsstruktur wurde aktualisiert.");
                        } else {
                            toastError(response.message || "Fehler beim Aktualisieren der Struktur.");
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        toastError("Fehler beim Aktualisieren der Struktur.");
                    }
                });
            }
        });
    };

    function loadDepartments() {
        const branchId = $('#branchFilter').val() || '{{ $selectedBranch ?? '' }}';
        const search   = $('#searchDepartment').val() || '';
        const status   = $('#statusFilter').val() || 'all';
        const head     = $('#headFilter').val() || 'all';
        const dateFrom = $('#dateFrom').val() || '';
        const dateTo   = $('#dateTo').val() || '';

        $.ajax({
            url: ROUTE_INDEX,
            type: 'GET',
            data: {
                branch_id: branchId,
                search: search,
                status: status,
                head: head,
                sort_by: window.DEPT_SORT_BY || 'order',
                sort_dir: window.DEPT_SORT_DIR || 'asc',
                date_from: dateFrom,
                date_to: dateTo
            },
            success: function (resp) {
                if (!resp.success) {
                    toastError(resp.message || 'Abteilungen konnten nicht geladen werden.');
                    return;
                }
                $('#sortable').html(resp.html);
                initializeDragAndDrop();
                refreshFeather();

                if (resp.analytics) {
                    updateAnalyticsCards(resp.analytics);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                toastError('Fehler beim Laden der Abteilungen.');
            }
        });
    }

    window.deleteDepartment = function (departmentId) {
        Swal.fire({
            title: "Sind Sie sicher?",
            text: "Diese Abteilung wird gelöscht.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ja, löschen",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (!result.isConfirmed) return;

            const url = ROUTE_DESTROY.replace('___ID___', departmentId);

            $.ajax({
                url: url,
                type: "DELETE",
                data: { _token: CSRF_TOKEN },
                success: function (resp) {
                    if (resp.success) {
                        toastSuccess(resp.message || 'Abteilung wurde gelöscht.');
                        loadDepartments();
                    } else {
                        toastError(resp.message || 'Abteilung konnte nicht gelöscht werden.');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    toastError('Fehler beim Löschen der Abteilung.');
                }
            });
        });
    };

    window.editDepartment = function (departmentId) {
        const currentName =
            $(`.dp-row[data-id='${departmentId}'] .dp-name a`).first().text().trim() ||
            $(`.dp-row[data-id='${departmentId}']`).find('[data-department-name]').first().text().trim();

        Swal.fire({
            title: "Abteilung bearbeiten",
            input: "text",
            inputValue: currentName,
            showCancelButton: true,
            confirmButtonText: "Speichern",
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage("Bitte einen Namen eingeben");
                    return false;
                }

                return $.ajax({
                    url: ROUTE_UPDATE,
                    type: "POST",
                    data: {
                        id: departmentId,
                        department_name: value,
                        _token: CSRF_TOKEN
                    }
                }).catch(() => {
                    Swal.showValidationMessage("Fehler beim Speichern");
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                toastSuccess("Abteilung wurde aktualisiert.");
                loadDepartments();
            }
        });
    };

    $(document).on('click', '.view_department_employees', function () {
        const deptId = $(this).data('department-id');
        const url = ROUTE_DEPT_EMPLOYEES.replace('___ID___', deptId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (resp) {
                if (!resp.success) {
                    toastError(resp.message || 'Mitarbeiter konnten nicht geladen werden.');
                    return;
                }

                const rows = resp.data || [];

                if (!rows.length) {
                    Swal.fire({
                        title: resp.department.name,
                        html: `
                            <p style="font-size:13px;">
                                In dieser Abteilung sind aktuell keine Mitarbeiter zugeordnet.
                            </p>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-employee-in-modal">
                                + Mitarbeiter hinzufügen
                            </button>
                        `,
                        didOpen: () => {
                            $('#btn-add-employee-in-modal').on('click', function () {
                                Swal.close();
                                chooseEmployeeForDepartment(deptId);
                            });
                        },
                        icon: 'info'
                    });
                    return;
                }

                const htmlRows = rows.map(r => {
                    const percent = Number(r.percent ?? 0).toFixed(2);
                    const montagePercent = Number(r.montage_percent ?? 0).toFixed(2);
                    const officePercent = Number(r.office_percent ?? 0).toFixed(2);
                    const hours = Number(r.working_hours ?? 0).toFixed(2);
                    const baseHours = Number(r.base_working_hour ?? 0).toFixed(2);

                    return `
                        <tr class="dept-emp-row"
                            data-dp-id="${r.id}"
                            data-employee-id="${r.employee_id}"
                            data-position-id="${r.position_id}"
                            data-percent="${percent}"
                            data-montage-percent="${montagePercent}"
                            data-office-percent="${officePercent}"
                            data-working-hours="${hours}"
                            data-base-hours="${baseHours}">
                            <td>${r.name} ${r.lastname}</td>
                            <td>${r.position}</td>
                            <td>${percent}%</td>
                            <td>${montagePercent}%</td>
                            <td>${officePercent}%</td>
                            <td>${hours}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-dept-emp">
                                    <i class="feather icon-edit"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                Swal.fire({
                    title: `Mitarbeiter – ${resp.department.name}`,
                    width: 900,
                    html: `
                        <div class="mb-1" style="text-align:right;">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-employee-in-modal">
                                + Mitarbeiter hinzufügen
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Mitarbeiter</th>
                                        <th>Position</th>
                                        <th>Gesamt %</th>
                                        <th>Montage %</th>
                                        <th>Office %</th>
                                        <th>Std.</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>${htmlRows}</tbody>
                            </table>
                        </div>
                    `,
                    didOpen: () => {
                        $('#btn-add-employee-in-modal').on('click', function () {
                            Swal.close();
                            chooseEmployeeForDepartment(deptId);
                        });
                        refreshFeather();
                    }
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                toastError('Fehler beim Laden der Mitarbeiter.');
            }
        });
    });

    $(document).on('click', '.btn-edit-dept-emp', function () {
        const $row = $(this).closest('tr.dept-emp-row');

        const cfg = {
            dpId: $row.data('dp-id'),
            employeeId: $row.data('employee-id'),
            positionId: $row.data('position-id'),
            percent: parseFloat($row.data('percent') || '0'),
            montagePercent: parseFloat($row.data('montage-percent') || '0'),
            officePercent: parseFloat($row.data('office-percent') || '0'),
            workingHours: parseFloat($row.data('working-hours') || '0'),
            baseHours: parseFloat($row.data('base-hours') || '0'),
        };

        openAllocationEditModal(cfg);
    });

    function openAllocationEditModal(cfg) {
        const { dpId, employeeId, positionId, percent, montagePercent, officePercent, workingHours, baseHours } = cfg;
        const baseHoursLabel = (baseHours || 0).toFixed(2);

        const positionOptions = POSITIONS.map(p =>
            `<option value="${p.id}">${p.position}</option>`
        ).join('');

        Swal.fire({
            title: "Zuordnung bearbeiten",
            width: 650,
            html: `
                <div style="font-size:12px; text-align:left;">
                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                        <div style="flex:1 1 260px; min-width:260px;">
                            <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Position</label>
                            <select id="swal-position-edit" class="form-control" style="width:100%; margin-bottom:10px;">
                                <option value="">-- Position auswählen --</option>
                                ${positionOptions}
                            </select>

                            <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Prozent in dieser Abteilung (%)</label>
                            <input id="swal-percent-edit" type="number" min="0" max="100" class="swal2-input" value="${percent}" style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                            <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Arbeitsstunden für diese Abteilung</label>
                            <input id="swal-working-hours-edit" type="number" step="0.01" class="swal2-input" value="${workingHours}" style="width:100%; box-sizing:border-box; margin:4px 0 6px;">

                            <small style="color:#6b7280;">Basis-Arbeitszeit Mitarbeiter: <strong>${baseHoursLabel} h / Woche</strong></small>
                        </div>

                        <div style="flex:1 1 260px; min-width:260px;">
                            <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Montage-Anteil (%)</label>
                            <input id="swal-montage-percent-edit" type="number" min="0" max="100" class="swal2-input" value="${montagePercent}" style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                            <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Office-Anteil (%)</label>
                            <input id="swal-office-percent-edit" type="number" min="0" max="100" class="swal2-input" value="${officePercent}" style="width:100%; box-sizing:border-box; margin:4px 0 10px;">
                        </div>
                    </div>
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: "Speichern",
            cancelButtonText: "Abbrechen",
            didOpen: () => {
                $('#swal-position-edit').select2({
                    width: '100%',
                    dropdownParent: $('.swal2-container'),
                    placeholder: '-- Position auswählen --',
                    allowClear: true
                });

                $('#swal-position-edit').val(String(positionId)).trigger('change');

                const percentInput = document.getElementById('swal-percent-edit');
                const workingHoursInput = document.getElementById('swal-working-hours-edit');

                const recalc = () => {
                    const p = parseFloat(percentInput.value || '0');
                    const hours = baseHours * (p / 100);
                    if (!isNaN(hours)) workingHoursInput.value = hours.toFixed(2);
                };

                percentInput.addEventListener('input', recalc);
            },
            preConfirm: () => {
                const newPositionId = $('#swal-position-edit').val();
                const newPercent = parseFloat(document.getElementById('swal-percent-edit').value || '0');
                const newMontage = parseFloat(document.getElementById('swal-montage-percent-edit').value || '0');
                const newOffice = parseFloat(document.getElementById('swal-office-percent-edit').value || '0');
                const newWorkingHours = parseFloat(document.getElementById('swal-working-hours-edit').value || '0');

                if (!newPositionId) {
                    Swal.showValidationMessage("Bitte eine Position auswählen");
                    return false;
                }
                if (newPercent <= 0 || newPercent > 100) {
                    Swal.showValidationMessage("Bitte einen gültigen Prozentwert (1–100) eingeben");
                    return false;
                }
                if (Math.abs((newMontage + newOffice) - newPercent) > 0.0001) {
                    Swal.showValidationMessage("Montage% + Office% müssen zusammen dem Gesamt-Prozent entsprechen.");
                    return false;
                }
                if (newWorkingHours < 0) {
                    Swal.showValidationMessage("Arbeitsstunden müssen ≥ 0 sein");
                    return false;
                }

                return {
                    id: dpId,
                    position_id: newPositionId,
                    percent: newPercent,
                    montage_percent: newMontage,
                    office_percent: newOffice,
                    working_hours: newWorkingHours,
                    employee_id: employeeId
                };
            }
        }).then(result => {
            if (!result.isConfirmed) return;

            const payload = result.value;

            $.ajax({
                url: ROUTE_POSITIONS_UPDATE,
                type: "POST",
                data: {
                    id: payload.id,
                    position_id: payload.position_id,
                    percent: payload.percent,
                    montage_percent: payload.montage_percent,
                    office_percent: payload.office_percent,
                    working_hours: payload.working_hours,
                    employee_id: payload.employee_id,
                    _token: CSRF_TOKEN
                },
                success: function (resp) {
                    if (resp.success) {
                        toastSuccess(resp.message || "Zuordnung aktualisiert.");
                        Swal.close();
                    } else {
                        toastError(resp.message || "Zuordnung konnte nicht aktualisiert werden.");
                    }
                },
                error: function (xhr) {
                    let msg = "Fehler beim Aktualisieren der Zuordnung.";
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    console.error(xhr.responseText);
                    toastError(msg);
                }
            });
        });
    }

    function assignDepartmentHead(departmentId) {
        $.ajax({
            url: ROUTE_GET_EMP,
            type: "GET",
            success: function (employees) {
                if (!employees.length) {
                    toastError("Keine aktiven Mitarbeiter gefunden.");
                    return;
                }

                const options = employees.map(emp =>
                    `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`
                ).join("");

                Swal.fire({
                    title: "Abteilungsleiter festlegen",
                    html: `<select id="employeeSelect" class="swal2-select" style="width:100%;">${options}</select>`,
                    showCancelButton: true,
                    confirmButtonText: "Speichern",
                    preConfirm: () => {
                        const selected = document.getElementById('employeeSelect').value;
                        if (!selected) Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen.");
                        return selected;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: ROUTE_STORE_HEAD,
                        type: "POST",
                        data: {
                            employee_id: result.value,
                            department_id: departmentId,
                            _token: CSRF_TOKEN
                        },
                        success: function () {
                            toastSuccess("Abteilungsleiter gespeichert.");
                            loadDepartments();
                        },
                        error: function () {
                            toastError("Abteilungsleiter konnte nicht gespeichert werden.");
                        }
                    });
                });
            },
            error: function () {
                toastError("Mitarbeiter konnten nicht geladen werden.");
            }
        });
    }

    function assignDepartmentRepresentative(departmentId) {
        $.ajax({
            url: ROUTE_GET_EMP,
            type: "GET",
            success: function (employees) {
                if (!employees.length) {
                    toastError("Keine aktiven Mitarbeiter gefunden.");
                    return;
                }

                const options = employees.map(emp =>
                    `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`
                ).join("");

                Swal.fire({
                    title: "Stellvertretung festlegen",
                    html: `<select id="repSelect" class="swal2-select" style="width:100%;">${options}</select>`,
                    showCancelButton: true,
                    confirmButtonText: "Speichern",
                    preConfirm: () => {
                        const selected = document.getElementById('repSelect').value;
                        if (!selected) Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen.");
                        return selected;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: ROUTE_STORE_REP,
                        type: "POST",
                        data: {
                            employee_id: result.value,
                            department_id: departmentId,
                            _token: CSRF_TOKEN
                        },
                        success: function (resp) {
                            toastSuccess(resp.message || "Stellvertretung gespeichert.");
                            loadDepartments();
                        },
                        error: function () {
                            toastError("Stellvertretung konnte nicht gespeichert werden.");
                        }
                    });
                });
            },
            error: function () {
                toastError("Mitarbeiter konnten nicht geladen werden.");
            }
        });
    }

    $(document).on('click', '.change_employee', function () {
        assignDepartmentHead($(this).data('department-id'));
    });

    $(document).on('click', '.change_representative', function () {
        assignDepartmentRepresentative($(this).data('department-id'));
    });

    $(document).on('click', '.assign_employee', function () {
        chooseEmployeeForDepartment($(this).data('department-id'));
    });

    function chooseEmployeeForDepartment(departmentId) {
        $.ajax({
            url: ROUTE_GET_EMP,
            type: "GET",
            success: function (employees) {
                if (!employees.length) {
                    toastError("Keine aktiven Mitarbeiter gefunden.");
                    return;
                }

                const options = employees.map(emp =>
                    `<option value="${emp.id}" data-working-hour="${emp.working_hour ?? 0}">
                        ${emp.name} ${emp.lastname}
                     </option>`
                ).join("");

                Swal.fire({
                    title: "Mitarbeiter auswählen",
                    html: `<select id="empSelect" class="swal2-select" style="width:100%;">${options}</select>`,
                    showCancelButton: true,
                    confirmButtonText: "Weiter",
                    didOpen: () => {
                        $('#empSelect').select2({
                            width: '100%',
                            dropdownParent: $('.swal2-container'),
                            placeholder: 'Mitarbeiter wählen'
                        });
                    },
                    preConfirm: () => {
                        const id = $('#empSelect').val();
                        if (!id) {
                            Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen.");
                            return false;
                        }
                        const baseHours = parseFloat($('#empSelect option:selected').data('working-hour') || '0');
                        return { id, baseHours };
                    }
                }).then(result => {
                    if (!result.isConfirmed) return;
                    openAllocationModal(result.value.id, departmentId, result.value.baseHours);
                });
            },
            error: function () {
                toastError("Mitarbeiter konnten nicht geladen werden.");
            }
        });
    }

    function openAllocationModal(employeeId, departmentId, baseHours) {
        const baseHoursLabel = (baseHours || 0).toFixed(2);

        $.ajax({
            url: ROUTE_POSITIONS_REMAINING,
            type: "GET",
            data: { employee_id: employeeId },
            success: function (res) {
                if (!res.success) {
                    toastError(res.message || "Fehler beim Prüfen der Auslastung.");
                    return;
                }

                const used = parseFloat(res.used ?? 0);
                const remaining = parseFloat(res.remaining ?? 0);

                if (remaining <= 0) {
                    toastError("Dieser Mitarbeiter ist bereits zu 100% verplant.");
                    return;
                }

                const positionOptions = POSITIONS.map(p =>
                    `<option value="${p.id}">${p.position}</option>`
                ).join('');

                Swal.fire({
                    title: "Position & Verteilung festlegen",
                    width: 650,
                    html: `
                        <div style="font-size:12px; text-align:left;">
                            <div style="margin-bottom:8px; padding:6px 10px; border-radius:8px; background:#ecfdf3; border:1px solid #bbf7d0;">
                                <div style="font-size:11px; color:#166534;">
                                    Bereits verplant: <strong>${used.toFixed(2)}%</strong> – Verfügbar: <strong>${remaining.toFixed(2)}%</strong>
                                </div>
                            </div>

                            <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                <div style="flex:1 1 260px; min-width:260px;">
                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Position</label>
                                    <select id="swal-position" class="form-control" style="width:100%; margin-bottom:10px;">
                                        <option value="">-- Position auswählen --</option>
                                        ${positionOptions}
                                    </select>

                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                        Prozent in dieser Abteilung (%) – max. ${remaining.toFixed(2)}%
                                    </label>
                                    <input id="swal-percent" type="number" min="0" max="100" class="swal2-input" style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                        Arbeitsstunden für diese Abteilung
                                    </label>
                                    <input id="swal-working-hours" type="number" step="0.01" class="swal2-input" style="width:100%; box-sizing:border-box; margin:4px 0 6px;">

                                    <small style="color:#6b7280;">Basis-Arbeitszeit Mitarbeiter: <strong>${baseHoursLabel} h / Woche</strong></small>
                                </div>

                                <div style="flex:1 1 260px; min-width:260px;">
                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Montage-Anteil (%)</label>
                                    <input id="swal-montage-percent" type="number" min="0" max="100" class="swal2-input" style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Office-Anteil (%)</label>
                                    <input id="swal-office-percent" type="number" min="0" max="100" class="swal2-input" style="width:100%; box-sizing:border-box; margin:4px 0 10px;">
                                </div>
                            </div>
                        </div>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: "Speichern",
                    cancelButtonText: "Abbrechen",
                    didOpen: () => {
                        $('#swal-position').select2({
                            width: '100%',
                            dropdownParent: $('.swal2-container'),
                            placeholder: '-- Position auswählen --',
                            allowClear: true
                        });

                        const percentInput = document.getElementById('swal-percent');
                        const workingHoursInput = document.getElementById('swal-working-hours');

                        const recalc = () => {
                            const p = parseFloat(percentInput.value || '0');
                            const hours = baseHours * (p / 100);
                            if (!isNaN(hours)) workingHoursInput.value = hours.toFixed(2);
                        };

                        percentInput.addEventListener('input', recalc);
                    },
                    preConfirm: () => {
                        const positionId = $('#swal-position').val();
                        const percent = parseFloat(document.getElementById('swal-percent').value || '0');
                        const montagePercent = parseFloat(document.getElementById('swal-montage-percent').value || '0');
                        const officePercent = parseFloat(document.getElementById('swal-office-percent').value || '0');
                        const workingHours = parseFloat(document.getElementById('swal-working-hours').value || '0');

                        if (!positionId) {
                            Swal.showValidationMessage("Bitte eine Position auswählen");
                            return false;
                        }
                        if (percent <= 0 || percent > 100) {
                            Swal.showValidationMessage("Bitte einen gültigen Prozentwert (1–100) eingeben");
                            return false;
                        }
                        if (percent > remaining + 0.0001) {
                            Swal.showValidationMessage("Dieser Mitarbeiter hat nur noch " + remaining.toFixed(2) + "% verfügbar.");
                            return false;
                        }
                        if (Math.abs((montagePercent + officePercent) - percent) > 0.0001) {
                            Swal.showValidationMessage("Montage% + Office% müssen zusammen dem Gesamt-Prozent entsprechen.");
                            return false;
                        }
                        if (workingHours < 0) {
                            Swal.showValidationMessage("Arbeitsstunden müssen ≥ 0 sein");
                            return false;
                        }

                        return {
                            position_id: positionId,
                            percent: percent,
                            montage_percent: montagePercent,
                            office_percent: officePercent,
                            working_hours: workingHours
                        };
                    }
                }).then(result => {
                    if (!result.isConfirmed) return;

                    const payload = result.value;

                    $.ajax({
                        url: ROUTE_POSITIONS_ASSIGN,
                        type: "POST",
                        data: {
                            employee_id: employeeId,
                            department_id: departmentId,
                            position_id: payload.position_id,
                            percent: payload.percent,
                            montage_percent: payload.montage_percent,
                            office_percent: payload.office_percent,
                            working_hours: payload.working_hours,
                            _token: CSRF_TOKEN
                        },
                        success: function (resp) {
                            if (resp.success) {
                                toastSuccess(resp.message || "Mitarbeiter-Zuordnung gespeichert.");
                            } else {
                                toastError(resp.message || "Zuordnung konnte nicht gespeichert werden.");
                            }
                        },
                        error: function (xhr) {
                            let msg = "Fehler beim Speichern der Zuordnung.";
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            console.error(xhr.responseText);
                            toastError(msg);
                        }
                    });
                });
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                toastError("Fehler beim Prüfen der Auslastung.");
            }
        });
    }

    $(function () {
        $('.selectables').select2({
            placeholder: "Wählen...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#createDepartmentModal .dp-modal')
        });

        $('#branchFilter').select2({
            placeholder: "Betrieb wählen",
            allowClear: true,
            width: '100%'
        });

        updateAnalyticsCards(INITIAL_ANALYTICS);

        $('#branchFilter, #dateFrom, #dateTo, #statusFilter, #headFilter').on('change', function () {
            loadDepartments();
        });

        $('#searchDepartment').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                loadDepartments();
            }
        });

        $(document).on('click', '.dp-sort-trigger', function (e) {
            e.preventDefault();

            const $btn = $(this);
            window.DEPT_SORT_BY = $btn.data('sortBy');
            window.DEPT_SORT_DIR = $btn.data('sortDir');

            $('.dp-sort-trigger').removeClass('active');
            $btn.addClass('active');

            loadDepartments();
        });

        initializeDragAndDrop();
        refreshFeather();
    });

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.dept-actions-trigger');
        const wrappers = document.querySelectorAll('.dept-actions-wrapper');

        if (trigger) {
            const wrapper = trigger.closest('.dept-actions-wrapper');
            const isOpen = wrapper.classList.contains('open');

            wrappers.forEach(w => {
                w.classList.remove('open');
                w.classList.remove('drop-up');
            });

            if (!isOpen) {
                wrapper.classList.add('open');
                const menu = wrapper.querySelector('.dept-actions-menu');

                if (menu) {
                    menu.style.display = 'block';
                    const rect = menu.getBoundingClientRect();
                    const spaceBelow = window.innerHeight - rect.top;
                    const needed = rect.height + 16;

                    if (spaceBelow < needed) {
                        wrapper.classList.add('drop-up');
                    } else {
                        wrapper.classList.remove('drop-up');
                    }
                    menu.style.display = '';
                }
            }

            return;
        }

        if (e.target.closest('.dept-actions-menu')) {
            wrappers.forEach(w => {
                w.classList.remove('open');
                w.classList.remove('drop-up');
            });
            return;
        }

        wrappers.forEach(w => {
            w.classList.remove('open');
            w.classList.remove('drop-up');
        });

        if (e.target.classList.contains('dp-modal-backdrop')) {
            e.target.classList.remove('open');
        }
    });

    window.viewDepartmentEmployeesFromMenu = function (deptId) {
        const tempBtn = document.createElement('button');
        tempBtn.type = 'button';
        tempBtn.style.display = 'none';
        tempBtn.className = 'view_department_employees';
        tempBtn.setAttribute('data-department-id', String(deptId));

        document.body.appendChild(tempBtn);
        $(tempBtn).trigger('click');
        tempBtn.remove();
    };
</script>
@endsection


@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            }, 
            {
                label: 'Abteilung Liste',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush