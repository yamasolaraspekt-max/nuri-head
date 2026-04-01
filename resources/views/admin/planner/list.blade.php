{{-- resources/views/admin/planner/list.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Planung & Fortschritt')

@section('style')
  <style>
    :root{
      --mc-shell-bg:#f0f0f0;
      --mc-border: rgba(148, 163, 184, 0.35);
      --mc-border-soft: rgba(148, 163, 184, 0.18);
      --mc-text:#0b1120;
      --mc-muted:#424242;
      --mc-accent:#74b2d4;
      --mc-success:#93c21c;
      --mc-danger:#f97373;
      --mc-warning:#fbbf24;
      --mc-radius-lg:18px;
      --mc-radius-xl:22px;
    }

    .mc-page{ padding:18px 12px 30px; background:var(--mc-shell-bg); min-height:calc(100vh - 80px); }
    .mc-container{ max-width: 100%; margin:0 auto; }
    .mc-shell{ border-radius:var(--mc-radius-xl); padding:16px 18px 18px; }

    .mc-header{ display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:14px; }
    .mc-title{ font-size:1.15rem; font-weight:700; letter-spacing:-0.02em; color:var(--mc-text); }
    .mc-subtitle{ font-size:0.78rem; color:var(--mc-muted); }

    .mc-header-right{ display:flex; gap:16px; flex-wrap:wrap; align-items:center; justify-content:flex-end; }
    .mc-stat-group{ display:flex; gap:12px; padding-right:12px; border-right:1px solid var(--mc-border); }
    .mc-stat-item{ text-align:center; }
    .mc-stat-label{ font-size:0.65rem; font-weight:700; text-transform:uppercase; color:var(--mc-muted); }
    .mc-stat-val{ font-size:1rem; font-weight:800; color:var(--mc-text); line-height:1.2; }

    .mc-btn{ border-radius:999px; border:1px solid transparent; padding:7px 14px; font-size:0.78rem; display:inline-flex; align-items:center; gap:6px; cursor:pointer; background:transparent; color:var(--mc-text); white-space:nowrap; text-decoration: none; }
    .mc-btn:hover{ text-decoration: none; }
    .mc-btn-ghost{ border-color:var(--mc-border); background:#fff; color:var(--mc-muted); }
    .mc-btn-primary{ border-color:var(--mc-accent); background:var(--mc-accent); color:#fff; }
    .mc-btn-primary:hover{ color:#fff; opacity:0.9; }

    .mc-toolbar{ display:grid; grid-template-columns: 2fr 1.7fr auto; gap:10px; margin-bottom:12px; align-items:center; }
    @media (max-width: 992px){ .mc-toolbar{ grid-template-columns: 1fr; } }
    .mc-input,.mc-select{ width:100%; border-radius:999px; border:1px solid var(--mc-border-soft); background:#fff; padding:7px 10px; font-size:0.78rem; color:var(--mc-muted); outline:none; }
    .mc-input:focus,.mc-select:focus{ border-color:var(--mc-accent); }

    .mc-table-shell{ border-radius:var(--mc-radius-lg); border:1px solid var(--mc-border-soft); overflow:hidden; background:#fff; }
    .mc-table{ width:100%; border-collapse:collapse; font-size:0.78rem; }
    .mc-table th,.mc-table td{ padding:10px 12px; border-bottom:1px solid rgba(2,6,23,0.08); vertical-align:top; }
    .mc-table th{ text-transform:uppercase; letter-spacing:0.08em; font-size:0.7rem; color:var(--mc-muted); white-space:nowrap; background:rgba(2,6,23,0.03); text-align: left; }
    .mc-row-main{ transition: background 0.2s; }
    .mc-row-main:hover{ background: rgba(147,194,28,0.05); }

    .mc-row-title{ font-weight:700; color:#0b1120; }
    .mc-row-sub{ font-size:0.73rem; color:var(--mc-muted); margin-top:2px; line-height:1.3; }

    .mc-status-pill{ border-radius:999px; padding:2px 8px; font-size:0.68rem; text-transform:uppercase; letter-spacing:0.06em; display:inline-flex; align-items:center; gap:6px; }
    .mc-status-draft{ background:rgba(148,163,184,0.22); color:#334155; }
    .mc-status-published{ background:rgba(147,194,28,0.20); color:#365314; }
    .mc-status-archived{ background:rgba(251,191,36,0.20); color:#7c2d12; }

    .mc-tag{ border-radius:999px; padding:2px 8px; font-size:0.68rem; border:1px solid var(--mc-border-soft); color:var(--mc-muted); background:#fff; display:inline-flex; gap:6px; align-items:center; }
    .mc-progress-track{ width:100%; height:6px; background:rgba(0,0,0,0.06); border-radius:99px; overflow:hidden; margin-top:6px; }
    .mc-progress-bar{ height:100%; border-radius:99px; transition:width 0.4s ease; }

    .mc-expand-content{ background:#fafafa; border-bottom:1px solid rgba(2,6,23,0.08); padding:20px; box-shadow: inset 0 2px 6px rgba(0,0,0,0.02); }
    .mc-pagination{ padding:8px 10px 4px; font-size:0.74rem; color:var(--mc-muted); display:flex; justify-content:space-between; align-items:center; gap:6px; }

    .mc-avatar{ width:28px; height:28px; border-radius:99px; object-fit:cover; border:1px solid #fff; box-shadow:0 1px 2px rgba(0,0,0,0.1); }
    .mc-avatar-sm{ width:22px; height:22px; margin-left:-6px; }
    .mc-avatar-initials{ width:28px; height:28px; border-radius:99px; background:var(--mc-text); color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; border:1px solid #fff; }

    /* Inner Item Table */
    .mc-item-table{ width:100%; font-size:0.75rem; border-collapse: separate; border-spacing: 0 4px; }
    .mc-item-table th { font-size:0.65rem; color:var(--mc-muted); text-transform: uppercase; padding:0 8px; text-align:left; font-weight:700; }
    .mc-item-table td { background:#fff; padding:8px 10px; border-top:1px solid var(--mc-border-soft); border-bottom:1px solid var(--mc-border-soft); vertical-align: middle; }
    .mc-item-table tr td:first-child{ border-left:1px solid var(--mc-border-soft); border-top-left-radius:8px; border-bottom-left-radius:8px; }
    .mc-item-table tr td:last-child{ border-right:1px solid var(--mc-border-soft); border-top-right-radius:8px; border-bottom-right-radius:8px; }

    /* Badges */
    .mc-badge-item-status { font-size:0.65rem; padding:2px 6px; border-radius:4px; font-weight:700; text-transform:uppercase; letter-spacing:0.03em; }
    .mc-badge-open { background: #e0f2fe; color: #0369a1; }
    .mc-badge-done { background: #dcfce7; color: #15803d; }
    .mc-badge-progress { background: #fff7ed; color: #c2410c; }
    .mc-badge-scheduled { background: #f3f4f6; color: #475569; }

    [x-cloak] { display: none !important; }
  </style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="col-12">
        <h2 class="content-header-title float-left mb-0">Planung</h2>
        <div class="breadcrumb-wrapper col-12">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Planung &amp; Fortschritt</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="mc-page">
        <div class="mc-container">
          <div class="mc-shell">

            {{-- Header --}}
            <div class="mc-header">
              <div>
                <div class="mc-title">Aktive Pläne</div>
                <div class="mc-subtitle">Übersicht und Fortschrittskontrolle aller Projekte.</div>
              </div>

              <div class="mc-header-right">
                <div class="mc-stat-group hidden md:flex">
                  <div class="mc-stat-item">
                    <div class="mc-stat-label">Aktiv</div>
                    <div class="mc-stat-val">{{ $stats['total_active'] }}</div>
                  </div>
                  <div class="mc-stat-item">
                    <div class="mc-stat-label">Rate</div>
                    <div class="mc-stat-val" style="color:var(--mc-success);">{{ $stats['completion_rate'] }}%</div>
                  </div>
                  <div class="mc-stat-item">
                    <div class="mc-stat-label">Offen</div>
                    <div class="mc-stat-val" style="color:var(--mc-warning);">{{ $stats['open_tasks'] }}</div>
                  </div>
                </div>

                <a href="{{ route('planner.index') }}" class="mc-btn mc-btn-primary">
                  <i class="fa fa-columns"></i><span>Zur Tafel</span>
                </a>
              </div>
            </div>

            {{-- Toolbar --}}
            <form method="GET" action="{{ route('planner.list') }}">
              <div class="mc-toolbar">
                <div>
                  <input type="text" name="q" value="{{ $search }}" class="mc-input"
                         placeholder="Suche nach Plan, Kunde, Referenz...">
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                  <select name="status" class="mc-select" style="min-width: 150px;">
                    <option value="">Status (alle)</option>
                    <option value="draft" @selected($status === 'draft')>Entwurf</option>
                    <option value="published" @selected($status === 'published')>Veröffentlicht</option>
                    <option value="archived" @selected($status === 'archived')>Archiviert</option>
                  </select>
                  <div style="display:flex; align-items:center; gap:4px; background:#fff; border:1px solid var(--mc-border-soft); border-radius:999px; padding:2px 8px;">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="mc-input" style="border:none; padding:4px; width:auto;">
                    <span style="font-size:0.7rem; color:var(--mc-muted);">-</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="mc-input" style="border:none; padding:4px; width:auto;">
                  </div>
                </div>
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                  <button type="submit" class="mc-btn mc-btn-ghost"><i class="fa fa-filter"></i><span>Filter</span></button>
                  @if($search || $status || request('date_from'))
                    <a href="{{ route('planner.list') }}" class="mc-btn mc-btn-ghost"><i class="fa fa-rotate-right"></i><span>Reset</span></a>
                  @endif
                </div>
              </div>
            </form>

            {{-- List Table --}}
            <div class="mc-table-shell">
              <table class="mc-table">
                <thead>
                  <tr>
                    <th style="width: 40px;"></th>
                    <th>Plan / Projekt</th>
                    <th>Kunde</th>
                    <th>Manager & Team</th>
                    <th style="width: 200px;">Fortschritt</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aktionen</th>
                  </tr>
                </thead>
                
                @forelse($plans as $plan)
                    @php
                        $percent = $plan->total_items > 0 ? round(($plan->done_items / $plan->total_items) * 100) : 0;
                        $pm = $employees[$plan->pm_id] ?? null;
                        
                        $teamMembers = [];
                        if(!empty($plan->project_teams)) {
                            $decoded = json_decode($plan->project_teams, true);
                            $ids = [];
                            if (is_array($decoded)) {
                                if (array_is_list($decoded)) {
                                    foreach($decoded as $v) {
                                        if (is_numeric($v)) $ids[] = (int)$v;
                                        elseif (is_array($v)) $ids[] = (int)($v['id'] ?? $v['employee_id'] ?? 0);
                                    }
                                } else {
                                    if (!empty($decoded['ids'])) foreach($decoded['ids'] as $v) $ids[] = (int)$v;
                                    if (!empty($decoded['team'])) foreach($decoded['team'] as $v) $ids[] = (int)($v['id'] ?? $v);
                                }
                            }
                            foreach(array_unique($ids) as $id) {
                                if(isset($employees[$id])) $teamMembers[] = $employees[$id];
                            }
                        }

                        $progColor = 'var(--mc-accent)';
                        if($percent >= 100) $progColor = 'var(--mc-success)';
                        elseif($percent < 30) $progColor = 'var(--mc-warning)';

                        $stClass = 'mc-status-draft';
                        if($plan->status == 'published') $stClass = 'mc-status-published';
                        elseif($plan->status == 'archived') $stClass = 'mc-status-archived';
                        
                        $prodName = $plan->product_name ?? '–';
                        $prodImg  = $plan->product_image; 
                    @endphp

                    {{-- Alpine Scope --}}
                    <tbody class="border-b border-slate-100 last:border-0" x-data="planItemsLoader({{ $plan->id }})">
                        
                        <tr class="mc-row-main">
                          <td>
                            <button type="button" @click="toggleExpand()" class="mc-btn mc-btn-ghost" style="padding:4px 8px;">
                                <i class="fa fa-chevron-right transition-transform duration-200" :class="expanded ? 'rotate-90' : ''" style="font-size:0.7rem;"></i>
                            </button>
                          </td>
                          
                          <td>
                            <div class="mc-row-title">{{ $plan->title ?: 'Unbenannter Plan' }}</div>
                            <div class="mc-row-sub">
                                <span class="mc-tag" style="background:transparent; border:none; padding-left:0;">
                                    {{ $plan->stage }}
                                </span>
                                @if($plan->created_at)
                                 · Erstellt: {{ $plan->created_at->format('d.m.Y') }}
                                @endif
                            </div>
                          </td>

                          <td>
                            <div class="mc-row-title">{{ $plan->firma ?: ($plan->cust_name . ' ' . $plan->cust_last) ?: '–' }}</div>
                            <div class="mc-row-sub">ID: {{ $plan->customer_id }}</div>
                          </td>

                          <td>
                            <div style="display:flex; flex-direction:column; gap:6px;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    @if($pm)
                                        @if(!empty($pm->photo) || !empty($pm->avatar))
                                            <img src="{{ asset('images/employee/' . ($pm->photo ?? $pm->avatar)) }}" class="mc-avatar">
                                        @else
                                            <div class="mc-avatar-initials">{{ substr($pm->name ?? '?', 0, 1) }}</div>
                                        @endif
                                        <div>
                                            <div class="mc-row-title" style="font-size:0.75rem;">{{ $pm->name }} {{ $pm->lastname ?? '' }}</div>
                                            <div class="mc-row-sub" style="font-size:0.65rem;">Projektleiter</div>
                                        </div>
                                    @else
                                        <span class="mc-row-sub" style="font-style:italic;">Kein PM</span>
                                    @endif
                                </div>
                                
                                @if(count($teamMembers) > 0)
                                    <div style="display:flex; padding-left:4px;">
                                        @foreach(array_slice($teamMembers, 0, 4) as $tm)
                                            @if(!empty($tm->photo) || !empty($tm->avatar))
                                                <img src="{{ asset('images/employee/' . ($tm->photo ?? $tm->avatar)) }}" class="mc-avatar mc-avatar-sm" title="{{ $tm->name }}">
                                            @else
                                                <div class="mc-avatar-initials mc-avatar-sm" style="font-size:0.5rem;" title="{{ $tm->name }}">{{ substr($tm->name ?? '?', 0, 1) }}</div>
                                            @endif
                                        @endforeach
                                        @if(count($teamMembers) > 4)
                                            <div class="mc-avatar-initials mc-avatar-sm" style="background:#e2e8f0; color:#64748b; font-size:0.6rem;">+{{ count($teamMembers) - 4 }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                          </td>

                          <td>
                            <div style="display:flex; justify-content:space-between; font-size:0.7rem; font-weight:700; color:var(--mc-muted); margin-bottom:2px;">
                                <span>{{ $percent }}%</span>
                                <span>{{ $plan->done_items }} / {{ $plan->total_items }}</span>
                            </div>
                            <div class="mc-progress-track">
                                <div class="mc-progress-bar" style="width: {{ $percent }}%; background: {{ $progColor }};"></div>
                            </div>
                          </td>

                          <td>
                            <span class="mc-status-pill {{ $stClass }}">
                                <i class="fa fa-circle" style="font-size:0.5rem;"></i> {{ ucfirst($plan->status) }}
                            </span>
                          </td>

                          <td style="text-align:right;">
                            <a href="{{ route('planner.index') }}?project_id={{ $plan->project_id }}&customer_id={{ $plan->customer_id }}" class="mc-btn mc-btn-ghost" title="Zur Tafel">
                                <i class="fa fa-arrow-right"></i>
                            </a>
                          </td>
                        </tr>

                        {{-- Expanded Row --}}
                        <tr x-show="expanded" x-cloak class="mc-row-detail">
                            <td colspan="7" style="padding:0; border-top:1px dashed rgba(2,6,23,0.08);">
                                <div class="mc-expand-content">
                                    
                                    {{-- Meta Info Block --}}
                                    <div class="flex items-center gap-6 mb-6 border-b border-slate-200 pb-4">
                                        <div class="flex items-center gap-3">
                                            @if($prodImg)
                                                <img src="{{ asset('storage/'.$prodImg) }}" style="width:40px; height:40px; border-radius:6px; object-fit:cover;">
                                            @endif
                                            <div>
                                                <div class="mc-row-title">{{ $prodName }}</div>
                                                <div class="mc-row-sub">Produkt ID: {{ $plan->project_id }}</div>
                                            </div>
                                        </div>
                                        <div class="h-8 w-px bg-slate-200"></div>
                                        <div>
                                            <div class="mc-row-sub text-[10px] uppercase font-bold">Startdatum</div>
                                            <div class="font-bold text-sm text-slate-700">
                                                {{ $plan->published_at ? \Carbon\Carbon::parse($plan->published_at)->format('d.m.Y H:i') : '–' }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mc-row-sub text-[10px] uppercase font-bold">Zuletzt aktualisiert</div>
                                            <div class="font-bold text-sm text-slate-700">
                                                {{ $plan->updated_at ? \Carbon\Carbon::parse($plan->updated_at)->diffForHumans() : '–' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Items Toolbar --}}
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="font-bold text-slate-800 text-sm">Aufgabenliste</h3>
                                        <div class="flex gap-2">
                                            <input type="text" x-model.debounce.500ms="search" placeholder="Aufgaben durchsuchen..." class="mc-input" style="padding:4px 10px; font-size:0.75rem; width:200px;">
                                            <button @click="fetchItems(1)" class="mc-btn mc-btn-ghost" style="padding:4px 8px;"><i class="fa fa-rotate"></i></button>
                                        </div>
                                    </div>

                                    {{-- Items Table --}}
                                    <div style="background:#fff; border:1px solid var(--mc-border-soft); border-radius:8px; padding:0 1px;">
                                        <div x-show="loading" class="p-6 text-center text-slate-400 text-sm">
                                            <i class="fa fa-spinner fa-spin mr-2"></i> Lade Aufgaben...
                                        </div>
                                        
                                        <div x-show="!loading && items.length === 0" class="p-6 text-center text-slate-400 text-sm italic">
                                            Keine Aufgaben gefunden.
                                        </div>

                                        <table class="mc-item-table" x-show="!loading && items.length > 0">
                                            <thead>
                                                <tr>
                                                    <th style="width:40px">#</th>
                                                    <th>Aufgabe & Beschreibung</th>
                                                    <th>Zuständig / Team</th>
                                                    <th>Assets</th>
                                                    <th>Abhängigkeit</th>
                                                    <th style="text-align:right">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="item in items" :key="item.id">
                                                    <tr>
                                                        <td class="text-center font-mono text-slate-400" x-text="item.sort_order || '-'"></td>
                                                        <td>
                                                            <div class="font-bold text-slate-700" x-text="item.title"></div>
                                                            <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1" x-text="item.description || 'Keine Beschreibung'"></div>
                                                        </td>
                                                        <td>
                                                            <div class="flex flex-col gap-1">
                                                                <!-- Lead -->
                                                                <template x-if="item.lead">
                                                                    <div class="flex items-center gap-2">
                                                                        <div class="w-5 h-5 rounded-full bg-brandDark text-white flex items-center justify-center text-[10px] font-bold">L</div>
                                                                        <span class="text-xs font-bold text-slate-700" x-text="item.lead.name + ' ' + (item.lead.lastname || '')"></span>
                                                                    </div>
                                                                </template>
                                                                <!-- Team Members -->
                                                                <div class="flex -space-x-1 pl-1" x-show="item.team && item.team.length > 0">
                                                                    <template x-for="emp in item.team" :key="emp.id">
                                                                        <div class="mc-avatar-initials mc-avatar-sm" style="font-size:0.5rem; background:#cbd5e1; color:#1e293b;" :title="emp.name + ' ' + (emp.lastname||'')">
                                                                            <span x-text="(emp.name || '?').substr(0,1)"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                <span x-show="!item.lead && (!item.team || item.team.length === 0)" class="text-[10px] text-slate-400 italic">Nicht zugewiesen</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="flex flex-wrap gap-1">
                                                                <template x-for="asset in item.assets" :key="asset.id">
                                                                    <span class="mc-tag" style="font-size:0.6rem; padding:1px 6px;">
                                                                        <span x-text="asset.qty > 1 ? asset.qty + 'x ' : ''"></span>
                                                                        <span x-text="asset.name"></span>
                                                                    </span>
                                                                </template>
                                                                <span x-show="!item.assets || item.assets.length === 0" class="text-[10px] text-slate-400 italic">–</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <template x-for="dep in item.dependencies" :key="dep.id">
                                                                <div class="text-[10px] text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full inline-block border border-orange-100">
                                                                    <i class="fa fa-link mr-1"></i> <span x-text="dep.title"></span>
                                                                </div>
                                                            </template>
                                                            <span x-show="!item.dependencies || item.dependencies.length === 0" class="text-[10px] text-slate-400 italic">–</span>
                                                        </td>
                                                        <td style="text-align:right">
                                                            <span :class="{
                                                                'mc-badge-item-status': true,
                                                                'mc-badge-open': item.status === 'open',
                                                                'mc-badge-done': item.status === 'done' || item.status === 'completed',
                                                                'mc-badge-progress': item.status === 'in_progress' || item.status === 'started',
                                                                'mc-badge-scheduled': item.status === 'scheduled' || item.status === 'planned'
                                                            }" x-text="item.status"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Pagination --}}
                                    <div class="flex justify-between items-center mt-3 text-xs text-slate-500" x-show="!loading && lastPage > 1">
                                        <span>Seite <span x-text="page"></span> von <span x-text="lastPage"></span></span>
                                        <div class="flex gap-2">
                                            <button @click="fetchItems(page - 1)" :disabled="page <= 1" class="mc-btn mc-btn-ghost px-2 py-1 disabled:opacity-50">Zurück</button>
                                            <button @click="fetchItems(page + 1)" :disabled="page >= lastPage" class="mc-btn mc-btn-ghost px-2 py-1 disabled:opacity-50">Weiter</button>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                          <td colspan="7" style="text-align:center; padding:30px 0; color:var(--mc-muted);">
                            Keine Pläne gefunden.
                          </td>
                        </tr>
                    </tbody>
                @endforelse
              </table>

              <div class="mc-pagination">
                <div>
                    @if($plans->total() > 0)
                    Zeige {{ $plans->firstItem() }}–{{ $plans->lastItem() }} von {{ $plans->total() }}
                    @endif
                </div>
                <div>
                    {{ $plans->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
  <script src="[https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js](https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js)" defer></script>
  <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('planItemsLoader', (planId) => ({
            expanded: false,
            loading: false,
            items: [],
            page: 1,
            lastPage: 1,
            search: '',

            toggleExpand() {
                this.expanded = !this.expanded;
                if (this.expanded && this.items.length === 0) {
                    this.fetchItems();
                }
            },

            async fetchItems(page = 1) {
                this.loading = true;
                this.page = page;

                try {
                    const params = new URLSearchParams({
                        q: this.search,
                        page: this.page
                    });

                    const response = await fetch(`/planner/plans/${planId}/items/search?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    
                    // Laravel paginate response structure: { data: [...], current_page: 1, last_page: 5, ... }
                    this.items = data.data;
                    this.lastPage = data.last_page;
                    this.page = data.current_page;

                } catch (error) {
                    console.error('Error fetching items:', error);
                } finally {
                    this.loading = false;
                }
            },

            init() {
                this.$watch('search', () => {
                    this.page = 1;
                    this.fetchItems(1);
                });
            }
        }));
    });
  </script>
@endsection