@extends('admin.layouts.app')
@section('title') PROZESS @stop

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />


  <link rel="stylesheet" href="{{ asset('css/kanban.css') }}?v={{ time() }}">
@endsection


@section('content')
@php
$canManageKanbanLeadStages = auth()->check() && \App\Models\UserRoll::query()
  ->where(function ($q) {
    $q->where('user_id', auth()->id());

    if (auth()->user() && auth()->user()->name) {
      $q->orWhere('user_id', auth()->user()->name);
    }
  })
  ->where('item_id', 'Administrator')
  ->where(function ($q) {
    $q->where('is_read', true)
      ->orWhere('is_add', true)
      ->orWhere('is_update', true)
      ->orWhere('is_delete', true);
  })
  ->exists();
@endphp

@php
$leadStageNamesForJs = $stageNames ?? [
  'lead' => 'Lead',
  'offer' => 'Angebot',
  'follow_up' => 'Nachfassen',
  'accepted' => 'Annehmen',
  'deal' => 'Auftrag',
  'project' => 'Montage',
  'completed' => 'Abschluss',
  'archive' => 'Archive',
  'junk' => 'Junk',
];

$leadStageMetaForJs = $stageMeta ?? [];

$kanbanStageNamesForJs = collect($leadStageNamesForJs)
  ->reject(fn($label, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanStageMetaForJs = collect($leadStageMetaForJs)
  ->reject(fn($meta, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanProductsForJs = collect($products ?? [])
  ->map(fn($product) => [
    'id' => $product->id ?? null,
    'name' => $product->article_group ?? $product->initial ?? ('Produkt #' . ($product->id ?? '')),
    'initial' => $product->initial ?? null,
  ])
  ->filter(fn($product) => !empty($product['id']))
  ->values()
  ->toArray();
@endphp
<div class="app-content"> 
  <div class="content-wrapper">   
     {{-- ======= MAIN SHELL ======= --}}
  <section id="basic-tabs-components">


<div class="pro-layout">
        <div class="pro-main">
            <div class="row">
                <div class="col-sm-12">

                    {{-- Top Tabs Header --}}
                    <div class="pro-tabs-shell">
                        <div class="pro-tabs-topbar">

                            {{-- Navigation Tabs --}}
                            <ul class="nav nav-tabs pro-tabs-nav" role="tablist">

                                {{-- Kanban --}}
                                <li class="nav-item">
                                    <a class="nav-link active"
                                       id="home-tab"
                                       data-toggle="tab"
                                       href="#home"
                                       role="tab"
                                       aria-controls="home"
                                       aria-selected="true">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                                <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                                <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                                <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Kanban
                                                <span class="pro-tab-count" id="tabCountKanban">
                                                    {{ $tabCounts['kanban'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Board Ansicht</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Liste --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="profile-tab"
                                       data-toggle="tab"
                                       href="#profile"
                                       role="tab"
                                       aria-controls="profile"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                                <circle cx="4" cy="6" r="1.5"></circle>
                                                <circle cx="4" cy="12" r="1.5"></circle>
                                                <circle cx="4" cy="18" r="1.5"></circle>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Liste
                                                <span class="pro-tab-count" id="tabCountList">
                                                    {{ $tabCounts['list'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Tabellen Ansicht</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Junk --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="junk-tab"
                                       data-toggle="tab"
                                       href="#junk"
                                       role="tab"
                                       aria-controls="junk"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Junk
                                                <span class="pro-tab-count" id="tabCountJunk">
                                                    {{ $tabCounts['junk'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Archiv / Junk</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Ticket --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="ticket-tab"
                                       data-toggle="tab"
                                       href="#ticket"
                                       role="tab"
                                       aria-controls="ticket"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M3 9a3 3 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a3 3 0 0 0 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"></path>
                                                <path d="M9 9h6"></path>
                                                <path d="M9 13h6"></path>
                                                <path d="M9 17h3"></path>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Ticket
                                                <span class="pro-tab-count" id="tabCountTicket">
                                                    {{ $tabCounts['ticket'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Service Fälle</span>
                                        </span>
                                    </a>
                                </li>

                            </ul>

                            {{-- Sort Dropdown --}}
                            <div class="pro-sort-box">
                                <span class="pro-sort-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M3 6h12"></path>
                                        <path d="M3 12h8"></path>
                                        <path d="M3 18h6"></path>
                                        <path d="M18 6v12"></path>
                                        <path d="M15 15l3 3 3-3"></path>
                                    </svg>
                                </span>

                                <label for="listSortSelect" class="pro-sort-label">
                                    Sortieren
                                </label>

                                <select id="listSortSelect" class="custom-select custom-select-sm pro-sort-select">
                                    <optgroup label="Datum">
                                        <option value="created_at|desc" selected>Datum (neueste zuerst)</option>
                                        <option value="created_at|asc">Datum (älteste zuerst)</option>
                                    </optgroup>

                                    <optgroup label="Zuletzt aktualisiert">
                                        <option value="updated_at|desc">Aktualisiert (neueste)</option>
                                        <option value="updated_at|asc">Aktualisiert (älteste)</option>
                                    </optgroup>

                                    <optgroup label="Kunde">
                                        <option value="customer_lastname|asc">Kunde (A-Z)</option>
                                        <option value="customer_lastname|desc">Kunde (Z-A)</option>
                                    </optgroup>

                                    <optgroup label="Ort">
                                        <option value="city|asc">Ort (A-Z)</option>
                                        <option value="city|desc">Ort (Z-A)</option>
                                    </optgroup>

                                    <optgroup label="Status">
                                        <option value="status|asc">Status (A-Z)</option>
                                        <option value="status|desc">Status (Z-A)</option>
                                    </optgroup>
                                </select>

                                <button type="button" class="lsm-btn lsm-btn--phase" id="btnOpenStageManager" title="Phasen verwalten">
                                    <span class="lsm-btn-icon"><i class="feather icon-sliders"></i></span>
                                    <span>Phasen</span>
                                </button>

                                @if($canManageKanbanLeadStages)
                                @endif

                                <button type="button" class="lsm-btn lsm-btn--filter" id="btnOpenDrawer" title="Übersicht & Filter">
                                    <span class="lsm-btn-icon"><i class="feather icon-filter"></i></span>
                                    <span>Filter</span>
                                    <span id="filterBadge" class="rail-badge d-none">0</span>
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- Tabs Content --}}
                    <div class="pro-tabs-content-card">
                        <div class="tab-content">

                            {{-- Kanban --}}
                            <div class="tab-pane show active"
                                 id="home"
                                 aria-labelledby="home-tab"
                                 role="tabpanel">
                                <div class="kanban-zoom-card">
                                    <div class="kanban-zoom-toolbar" aria-label="Kanban Anzeige">
                                        <div class="kanban-zoom-left">
                                            <span class="kanban-zoom-title">Kanban Ansicht</span>
                                            <span class="kanban-zoom-sub">Größe anpassen, damit mehr Spalten sichtbar sind</span>
                                        </div>
 

                                        <div class="kanban-zoom-actions">
                                            <button type="button" class="kbz-btn" data-kb-zoom="1">100%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.9">90%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.8">80%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.7">70%</button>
                                            <button type="button" class="kbz-btn kbz-btn--ghost" id="kbZoomOutBtn" title="Eine Stufe kleiner">
                                                <i class="feather icon-zoom-out"></i>
                                            </button>
                                            <button type="button" class="kbz-btn kbz-btn--ghost" id="kbZoomInBtn" title="Eine Stufe größer">
                                                <i class="feather icon-zoom-in"></i>
                                            </button>
                                            <select id="kbColumnWidthSelect" class="kbz-select" title="Spaltenbreite">
                                                <option value="normal">Normal</option>
                                                <option value="compact">Schmal</option>
                                                <option value="wide">Breit</option>
                                            </select>
                                            <label class="kbz-compact-toggle">
                                                <input type="checkbox" id="kbCompactToggle">
                                                <span>Kompakt</span>
                                            </label>
                                            <label class="kbz-compact-toggle" title="Wenn aus, bleibt jede Spalte grün (#93c21c)">
                                                <input type="checkbox" id="kbUseStageColorsToggle">
                                                <span>Spaltenfarbe nutzen</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="kanban-zoom-area" id="kanbanZoomArea">
                                        <div id="kanban" class="kanban-container"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- List --}}
                            <div class="tab-pane"
                                 id="profile"
                                 aria-labelledby="profile-tab"
                                 role="tabpanel">
                                <div class="table-responsive p-0">
                                    <table class="table pro-list-table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="sortable active desc" data-sort="created_at">
                                                    <span><i class="feather icon-calendar"></i> Datum</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="customer_lastname">
                                                    <span><i class="feather icon-user"></i> Kunde</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="city">
                                                    <span><i class="feather icon-map-pin"></i> Ort</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="product">
                                                    <span><i class="feather icon-box"></i> Produkt</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="employee">
                                                    <span><i class="feather icon-users"></i> Mitarbeiter</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="updated_at">
                                                    <span><i class="feather icon-activity"></i> Status</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="status">
                                                    <span><i class="feather icon-layers"></i> Phase</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="kanbanTableBody">
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    Lade Daten…
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div id="listPagination" class="d-flex justify-content-center mt-3"></div>
                                </div>
                            </div>

                            {{-- Junk --}}
                            <div class="tab-pane"
                                 id="junk"
                                 aria-labelledby="junk-tab"
                                 role="tabpanel">
                                @include('admin.kanban.partials.junk', ['junk' => $junk])
                            </div>

                            {{-- Ticket --}}
                            <div class="tab-pane"
                                 id="ticket"
                                 aria-labelledby="ticket-tab"
                                 role="tabpanel">
                                @include('admin.kanban.partials.ticket', [
  'tickets' => $tickets ?? null,
  'total' => $tabCounts['ticket'] ?? 0
])
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
</section>

    {{-- ======= SINGLE DRAWER: Übersicht & Filter ======= --}}
    <div class="drawer-backdrop" id="drawerBackdrop"></div>
    <aside class="drawer" id="sideDrawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
      <div class="drawer-header">
        <div class="d-flex align-items-center">
          <i class="feather icon-sliders mr-2"></i>
          <h5 id="drawerTitle" class="mb-0">Übersicht &amp; Filter</h5>
          <span id="tabFilterCount" class="tab-badge-inline d-none ml-2">0</span>
        </div>
        <div class="d-flex align-items-center">
          <button class="btn btn-sm btn-outline-secondary mr-1" id="btnClearFilters"><i class="feather icon-rotate-ccw"></i> Alles löschen</button>
          <button class="btn btn-sm btn-primary" id="btnApplyFilters"><i class="feather icon-check-circle"></i> Anwenden</button>
          <button class="btn btn-sm btn-outline-secondary ml-1" data-close-drawer><i class="feather icon-x"></i></button>
        </div>
      </div>

      {{-- Chips summary of active filters --}}
      <div class="px-3 pt-2">
        <div id="activeFilterChips" class="chips"></div>
      </div>

      <div class="drawer-body">
        <!-- SUMMARY (top) -->
        <div id="view-summary" class="mb-1">
          <div class="row text-center" id="summaryStats" style="justify-content:center">
            <div id="cardEmployees" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Verantwortliche</strong>
                <div id="totalEmployees" class="h4">{{ $totalEmployees ?? 0 }}</div>
              </div>
            </div>
            <div id="cardProducts" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Produkt</strong>
                <div id="totalProduct" class="h4">{{ $totalProducts ?? 0 }}</div>
              </div>
            </div>
            <div id="cardCustomers" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Kunde</strong>
                <div id="totalCustomer" class="h4">{{ $totalCustomers ?? 0 }}</div>
              </div>
            </div>
            <div id="cardAnfragen" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Nachfrage</strong>
                <div id="totalAnfrage" class="h4">{{ ($tabCounts['kanban'] ?? 0) }}</div>
              </div>
            </div>

            <div id="cardOffen" class="col-12 summary-card mb-2">
              <div class="border rounded py-2 bg-orange text-white" style="background:#f49f43;color:white!important;">
                <strong>Offen</strong>
                <div id="statusOffen" class="h4 text-white">
                  {{ $statusCounts['offen'] ?? 0 }} <small>({{ $statusPercentages['offen'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardZusage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-primary text-white">
                <strong>Zusage</strong>
                <div id="statusZusage" class="h4 text-white">
                  {{ $statusCounts['zusage'] ?? 0 }} <small>({{ $statusPercentages['zusage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardAbsage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-danger text-white">
                <strong>Absage</strong>
                <div id="statusAbsage" class="h4 text-white">
                  {{ $statusCounts['absage'] ?? 0 }} <small>({{ $statusPercentages['absage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <hr class="my-2">

        <!-- FILTER (below summary) -->
        <div id="view-filter">
          <form id="kanbanFilterForm" class="row align-items-end g-2">
            <div class="col-md-6">
              <label for="customerFilter" class="form-label d-flex align-items-center">
                Kunde <span class="badge badge-secondary ml-2 d-none" id="countCustomers">{{ $totalCustomers ?? 0 }}</span>
              </label>
              <select name="customer" id="customerFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($customers as $customer)
                  <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="stageFilter" class="form-label">Phase</label>
              <select name="stage" id="stageFilter" class="form-control select2 stage-color-select">
                <option value="">Alle Phasen</option>
                @foreach(($stageNames ?? []) as $key => $label)
                  @php
  $stageKey = strtolower((string) $key);
  $meta = $stageMeta[$key] ?? [];
  $color = $meta['color'] ?? '#93c21c';
  $icon = $meta['icon'] ?? 'circle';
                  @endphp
                  @if(!in_array($stageKey, ['junk', 'ticket'], true))
                    <option value="{{ $key }}" data-color="{{ $color }}" data-icon="{{ $icon }}">{{ $label }}</option>
                  @endif
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="leadAgeFilter" class="form-label">Lead-Alter (Farbe)</label>
              <select name="lead_age" id="leadAgeFilter" class="form-control select2">
                <option value="">Alle Zeiten</option>
                <option value="green">🟢 Neu (< 24h)</option>
                <option value="orange">🟠 Letzter Tag (24 - 48h)</option>
                <option value="red">🔴 Überfällig (> 48h)</option>
              </select>
            </div>

            <div class="col-md-6">
            <label for="branchFilter" class="form-label d-flex align-items-center">
              Filiale
              <span class="badge badge-secondary ml-2 d-none" id="countBranches">{{ count($branches ?? []) }}</span>
            </label>

            <select name="branch" id="branchFilter" class="form-control select2">
              <option value="">Alle</option>
              @foreach (($branches ?? []) as $b)
                <option value="{{ $b->id }}" data-color="{{ $b->color ?? '#93c21c' }}">
                  {{ $b->branch }}
                </option>
              @endforeach
            </select>
          </div>


            <div class="col-md-6">
              <label for="employeeFilter" class="form-label d-flex align-items-center">
                Mitarbeiter <span class="badge badge-secondary ml-2 d-none" id="countEmployees">{{ $totalEmployees ?? 0 }}</span>
              </label>
             <select name="employee" id="employeeFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($employees as $employee)
                  <option value="{{ $employee->id }}">
                    {{ $employee->name }} {{ $employee->lastname }}
                  </option>
                @endforeach
              </select>

            </div>

            <div class="col-md-6">
              <label for="departmentFilter" class="form-label d-flex align-items-center">
                Abteilung <span class="badge badge-secondary ml-2 d-none" id="countDepartments">{{ $totalDepartments ?? 0 }}</span>
              </label>
              <select name="department" id="departmentFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($departments as $department)
                  <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="productFilter" class="form-label d-flex align-items-center">
                Produkt <span class="badge badge-secondary ml-2 d-none" id="countProducts">{{ $totalProducts ?? 0 }}</span>
              </label>
              <select name="product" id="productFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($products as $product)
                  <option value="{{ $product->id }}">{{ $product->article_group }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="interestFilter" class="form-label">Interesse</label>
              <select name="interest" id="interestFilter" class="form-control select2">
                <option value="">Alle Interessen</option>
                <option value="interest">Kaufinteresse</option>
                <option value="intent">Kaufabsicht</option>
                <option value="option">Kaufoption</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="dateFrom" class="form-label">Von (Datum)</label>
              <input type="date" name="date_from" id="dateFrom" class="form-control" />
            </div>

            <div class="col-md-6">
              <label for="dateTo" class="form-label">Bis (Datum)</label>
              <input type="date" name="date_to" id="dateTo" class="form-control" />
            </div>


           <div class="col-12">
              <hr class="my-2">
              <label class="form-label mb-3 font-weight-bold text-dark">
                  <i class="feather icon-layout mr-1"></i> Spalten Sichtbarkeit
              </label>

              <div class="d-flex flex-wrap" id="columnTogglesContainer">
                  @foreach(($kanbanStageNamesForJs ?? ['lead' => 'Lead', 'offer' => 'Angebot', 'deal' => 'Auftrag', 'project' => 'Montage', 'completed' => 'Abschluss', 'archive' => 'Archiv']) as $key => $label)
                    <div class="custom-control custom-checkbox mr-3 mb-2">
                        <input type="checkbox" 
                              class="custom-control-input col-toggle-checkbox" 
                              id="toggleCol_{{ $key }}" 
                              value="{{ $key }}"
                              {{ $key !== 'archive' ? 'checked' : '' }}>

                        <label class="custom-control-label d-flex align-items-center" for="toggleCol_{{ $key }}" style="cursor: pointer; user-select: none;">
                            {{-- Icon for ON --}}
                            <span class="toggle-icon-on mr-1">
                                <i class="feather icon-eye text-success"></i>
                            </span>
                            {{-- Icon for OFF --}}
                            <span class="toggle-icon-off mr-1">
                                <i class="feather icon-eye-off text-muted"></i>
                            </span>

                            {{-- Text Label --}}
                            <span class="toggle-label-text">{{ $label }}</span>
                        </label>
                    </div>
                  @endforeach
              </div>
          </div>

            <div class="col-12 small text-muted mt-2">
              Tipp: <kbd>Enter</kbd> = Anwenden, <kbd>Esc</kbd> = Schließen.
            </div>
          </form>
        </div>
      </div>
    </aside>


    {{-- ======= UNDERPHASE SIDEBAR: opens when clicking Unterphasen of a Hauptphase ======= --}}
    <div class="kb-understage-sidebar-backdrop" id="kbUnderstageSidebarBackdrop" data-understage-close></div>
    <aside class="kb-understage-sidebar" id="kbUnderstageSidebar" aria-hidden="true">
      <div class="kb-understage-sidebar-head">
        <div>
          <div class="kb-understage-sidebar-title">
            <i class="feather icon-git-branch"></i>
            <span id="kbUnderstageSidebarTitle">Unterphasen</span>
          </div>
          <div class="kb-understage-sidebar-subtitle" id="kbUnderstageSidebarSubtitle">
            Hauptphase auswählen, um die Unterphasen hier zu sehen. Das Haupt-Kanban bleibt im Hintergrund unverändert.
          </div>
        </div>
        <div class="kb-understage-sidebar-actions">
          <button type="button" class="kb-understage-refresh" id="kbUnderstageRefresh">
            <i class="feather icon-refresh-cw"></i> Neu laden
          </button>
          <button type="button" class="kb-understage-close" data-understage-close>
            <i class="feather icon-x"></i> Schließen
          </button>
        </div>
      </div>
      <div class="kb-understage-sidebar-body">
        <div class="kb-understage-board" id="kbUnderstageBoard">
          <div class="kb-understage-sidebar-empty">Noch keine Hauptphase ausgewählt.</div>
        </div>
      </div>
    </aside>

    {{-- ======= DYNAMIC LEAD STAGE MANAGER MODAL ======= --}}
    <div id="leadStageManagerModal" class="lsm-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="leadStageManagerTitle">
      <div class="lsm-backdrop" data-lsm-close></div>
      <div class="lsm-panel" tabindex="-1">
        <div class="lsm-head">
          <div class="lsm-title">
            <span class="lsm-title-icon"><i class="feather icon-sliders"></i></span>
            <div>
              <h5 id="leadStageManagerTitle">Pipeline-Phasen verwalten</h5>
              <p>Standard-Phasen können umbenannt werden. Löschen ist nur möglich, wenn keine Daten darin sind.</p>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-lsm-close aria-label="Schließen">
            <i class="feather icon-x"></i>
          </button>
        </div>

        <div class="lsm-body">
          <div class="lsm-grid">
            <div class="lsm-card">
              <div class="lsm-card-head">
                <strong id="lsmFormTitle">Neue Phase</strong>
                <button type="button" class="btn btn-sm btn-light border" id="lsmResetForm">
                  <i class="feather icon-refresh-cw"></i> Neu
                </button>
              </div>
              <div class="lsm-card-body">
                <form id="leadStageForm" class="lsm-form" autocomplete="off">
                  @csrf
                  <input type="hidden" id="lsmStageId">

                  <div class="lsm-form-group lsm-form-group--full">
                    <label>Name der Phase</label>
                    <input type="text" id="lsmStageName" class="form-control" maxlength="80" placeholder="z.B. Beratung" required>
                  </div>

                  <div class="lsm-form-grid">
                    <div class="lsm-form-group">
                      <label>Farbe</label>
                      <div class="lsm-color-input-wrap">
                        <input type="color" id="lsmStageColor" class="form-control" value="#74b2d4">
                        <span id="lsmStageColorText">#74b2d4</span>
                      </div>
                    </div>
                    <div class="lsm-form-group">
                      <label>Icon</label>
                      <select id="lsmStageIcon" class="form-control" style="width:100%"></select>
                    </div>
                  </div>

                  <div class="lsm-toggle-grid">
                    <label class="lsm-toggle-card" for="lsmStageActive">
                      <input type="checkbox" id="lsmStageActive" checked>
                      <span class="lsm-toggle-icon"><i class="feather icon-eye"></i></span>
                      <span>
                        <strong>Aktiv</strong>
                        <small>Phase im Board und Filter anzeigen</small>
                      </span>
                    </label>

                    <label class="lsm-toggle-card" for="lsmStageClosed">
                      <input type="checkbox" id="lsmStageClosed">
                      <span class="lsm-toggle-icon"><i class="feather icon-lock"></i></span>
                      <span>
                        <strong>Geschlossene Phase</strong>
                        <small>Zählt als abgeschlossen / beendet</small>
                      </span>
                    </label>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block lsm-save-btn">
                    <i class="feather icon-save"></i> Speichern
                  </button>
                </form>

                 
              </div>
            </div>

            <div class="lsm-card">
              <div class="lsm-card-head">
                <strong>Alle Phasen</strong>
                <div class="d-flex align-items-center" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="lsmReloadStages">
                    <i class="feather icon-refresh-cw"></i> Laden
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="lsmSaveOrder">
                    <i class="feather icon-list"></i> Sortierung speichern
                  </button>
                </div>
              </div>

              <div class="lsm-stage-row lsm-stage-head">
                <div><span class="lsm-drag-handle"><i class="feather icon-move"></i></span></div>
                <div>Phase</div>
                <div>Key</div>
                <div>Daten</div>
                <div>Aktion</div>
              </div>
              <div id="leadStagesList">
                <div class="lsm-empty">Phasen werden geladen…</div>
              </div>
            </div>
          </div>
        </div>


        {{-- ======= RELATED SUBSTAGE CONFIGURATION DRAWER INSIDE PHASE CONFIG ======= --}}
        <aside class="lsm-substage-drawer" id="lsmSubstageDrawer" aria-hidden="true">
          <div class="lsm-substage-head">
            <div>
              <h5 class="lsm-substage-title" id="lsmSubstageTitle">Unterphasen</h5>
              <div class="lsm-substage-subtitle" id="lsmSubstageSubtitle">
                Wähle eine Hauptphase, um die zugehörigen Unterphasen hier direkt zu konfigurieren.
              </div>
              <span class="lsm-substage-open-note"><i class="feather icon-info"></i> Direkt an der ausgewählten Phase konfigurieren</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="lsmCloseSubstageDrawer" aria-label="Unterphasen schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>

          <div class="lsm-substage-body">
            <div class="lsm-substage-create">
              <div class="lsm-substage-create-title">Neue Unterphase für ausgewählte Hauptphase</div>
              <input type="hidden" id="lsmSubstageStageId">
              <div class="lsm-substage-form-grid">
                <input type="text" id="lsmSubstageName" class="form-control" placeholder="Name, z.B. Rückruf vereinbart">
                <input type="text" id="lsmSubstageKey" class="form-control" placeholder="Key auto">
                <input type="color" id="lsmSubstageColor" class="form-control" value="#93c21c" title="Farbe">
                <input type="text" id="lsmSubstageIcon" class="form-control" value="list" placeholder="Icon">
              </div>
              <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:8px;">
                <label class="mb-0 small font-weight-bold text-muted d-inline-flex align-items-center" style="gap:6px;">
                  <input type="checkbox" id="lsmSubstageActive" checked> Aktiv
                </label>
                <button type="button" class="lsm-mini-btn primary" id="lsmCreateSubstage">
                  <i class="feather icon-plus"></i> Unterphase erstellen
                </button>
              </div>
            </div>

            <div class="lsm-substage-list-card">
              <div class="d-flex align-items-center justify-content-between mb-1" style="gap:8px;">
                <div class="lsm-substage-list-title mb-0">Unterphasen dieser Hauptphase</div>
                <button type="button" class="lsm-mini-btn blue" id="lsmSaveSubstageOrder">
                  <i class="feather icon-list"></i> Sortierung speichern
                </button>
              </div>
              <div id="lsmSubstageList">
                <div class="lsm-substage-empty">Noch keine Hauptphase ausgewählt.</div>
              </div>
            </div>
          </div>
        </aside>

        <div class="lsm-footer">
          <div class="small text-muted">
            Nach Änderungen wird die Seite automatisch neu geladen, damit Kanban-Spalten, Filter und Listen synchron bleiben.
          </div>
          <button type="button" class="btn btn-light border" data-lsm-close>Schließen</button>
        </div>
      </div>
    </div>


    @if($canManageKanbanLeadStages)
      {{-- ======= KANBAN LEADSTAGE + SUBSTAGE ADMIN MODAL ======= --}}
      <div class="kbsa-backdrop" id="kanbanStageAdminModal" aria-hidden="true">
          <div class="kbsa-modal" role="dialog" aria-modal="true">
              <div class="kbsa-head">
                  <div>
                      <h3 class="kbsa-title">Phasen / Unterphasen verwalten</h3>
                      <div class="kbsa-sub">
                          Phasen und Unterphasen zentral konfigurieren und per Drag & Drop sortieren.
                      </div>
                  </div>
                  <button type="button" class="kbsa-close" data-kbsa-close>&times;</button>
              </div>

              <div class="kbsa-body">
                  <div class="kbsa-toolbar">
                      <div>
                          <label class="kbsa-label">Phasenname</label>
                          <input type="text" class="kbsa-input" id="kbsaStageName" placeholder="z. B. Beratung geplant">
                      </div>
                      <div>
                          <label class="kbsa-label">Key</label>
                          <input type="text" class="kbsa-input" id="kbsaStageKey" placeholder="auto">
                      </div>
                      <div>
                          <label class="kbsa-label">Farbe</label>
                          <input type="color" class="kbsa-input" id="kbsaStageColor" value="#93c21c">
                      </div>
                      <div>
                          <label class="kbsa-label">Icon</label>
                          <input type="text" class="kbsa-input" id="kbsaStageIcon" value="columns">
                      </div>
                      <label class="kbsa-check">
                          <input type="checkbox" id="kbsaStageActive" checked> Aktiv
                      </label>
                      <button type="button" class="kbsa-btn" id="kbsaCreateStage">
                          <i class="feather icon-plus"></i> Erstellen
                      </button>
                  </div>

                  <div class="kbsa-error" id="kbsaError"></div>
                  <div class="kbsa-small">Phasen</div>
                  <div id="kbsaStageList">
                      <div class="kbsa-small">Lade LeadStages...</div>
                  </div>
              </div>

              <div class="kbsa-foot">
                  <button type="button" class="kbsa-btn-soft" id="kbsaReloadStages">
                      <i class="feather icon-refresh-cw"></i> Neu laden
                  </button>
                  <button type="button" class="kbsa-btn-soft" data-kbsa-close>Schließen</button>
              </div>
          </div>
      </div>
    @endif


    <!-- Live Feed Modal Backdrop -->
      <div id="liveFeedModalBackdrop" class="lfm-backdrop" style="display:none;"></div>

      <!-- Live Feed Modal -->
      <div id="liveFeedModal"
          class="lfm-shell"
          role="dialog"
          aria-modal="true"
          aria-labelledby="liveFeedModalTitle"
          style="display:none;">

        <div class="lfm-header">
          <div>
            <h3 id="liveFeedModalTitle" class="lfm-title">Aktivitäten</h3>
            <div class="lfm-subtitle" id="liveFeedModalSubtitle">Kunde</div>
          </div>

          <div class="lfm-header-right">
            <div class="lfm-filters" id="liveFeedTypeFilters">
              <button type="button" class="lfm-filter-btn is-active" data-type="all">
                Alle
              </button>
              <button type="button" class="lfm-filter-btn" data-type="task">
                Aufgaben
              </button>
              <button type="button" class="lfm-filter-btn" data-type="appointment">
                Termine
              </button>
              <button type="button" class="lfm-filter-btn" data-type="ticket">
                Tickets
              </button>
            </div>

            <button type="button"
                    class="lfm-icon-btn"
                    id="liveFeedModalClose"
                    aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </div>

        <div class="lfm-body">
          <div class="lfm-meta">
            <span class="lfm-pill" id="liveFeedModalCount">0 Einträge</span>
            <span class="lfm-pill muted">
              <i class="feather icon-clock"></i>
              nach Nähe zu jetzt sortiert
            </span>
          </div>

          <div class="lfm-list" id="liveFeedModalList">
            <!-- Dynamisch gefüllt -->
          </div>
        </div>
      </div>

 

      <!-- Lead History Drawer -->
      <div id="lh-drawer" class="lh-root" aria-hidden="true" role="dialog" aria-labelledby="lh-title">
        <div class="lh-backdrop" data-lh-close></div>

        <aside class="lh-panel" tabindex="-1">
          <header class="lh-header">
            <h5 id="lh-title" class="mb-0">
              <i class="feather icon-activity mr-2"></i>
              <span id="lh-title-text">Verlauf</span>
            </h5>
            <button class="btn btn-sm btn-outline-secondary" data-lh-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </header>

          <section class="lh-body">
            <div class="row no-gutters">
              <div class="col-lg-7 pr-lg-2 border-right">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-trending-up mr-1"></i> Phasenverlauf</h6>
                  <ul id="lh-timeline" class="lh-timeline list-unstyled mb-0"></ul>
                </div>
              </div>
              <div class="col-lg-5 pl-lg-2">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-list mr-1"></i> Aktivitäten & Notizen</h6>
                  <div id="lh-activities" class="lh-list list-group"></div>
                </div>
              </div>
            </div>
          </section>
        </aside>
      </div>
 
      <div id="notesBackdrop" class="notes-backdrop"></div>
      <aside id="notesDrawer"
             class="notes-drawer kb-customer-panel-drawer"
             role="dialog"
             aria-modal="true"
             aria-labelledby="notesTitle"
             data-customer-id=""
             data-alternative-id=""
             data-product-id=""
             data-lead-product-list-id="">
        <div class="notes-head kb-cp-head">
          <div class="notes-title kb-cp-title">
            <span class="kb-cp-head-icon">
              <i class="feather icon-message-square"></i>
            </span>
            <div>
              <div class="kb-cp-title-line">
                <span id="notesTitle">Kunden Kommunikation</span>
                <span id="notesCountBadge" class="badge badge-secondary" data-count="0">0</span>
              </div>
              <div class="kb-cp-subtitle">Notizen, Kunden Bericht und Termin Bericht an einem Ort.</div>
            </div>
          </div>

          <div class="kb-cp-head-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-notes-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </div>

        <div class="notes-tabs kb-cp-tabs">
          <button type="button" class="notes-tab notes-tab--active" data-notes-tab="notes" aria-selected="true">
            <i class="feather icon-message-square mr-25"></i>
            Notizen
            <span id="tabBadgeNotes" class="badge badge-light ml-25">0</span>
          </button>

          <button type="button" class="notes-tab" data-notes-tab="customerReport" aria-selected="false">
            <i class="feather icon-file-text mr-25"></i>
            Kunden Bericht
            <span id="tabBadgeCustomerReport" class="badge badge-light ml-25">0</span>
          </button>

          <button type="button" class="notes-tab" data-notes-tab="report" aria-selected="false">
            <i class="feather icon-calendar mr-25"></i>
            Termin Bericht
            <span id="tabBadgeTerminReport" class="badge badge-light ml-25">0</span>
          </button>
        </div>

        <div class="notes-body kb-cp-body">
          <div id="notesList" class="kb-note-chat" data-notes-panel="notes" aria-live="polite">
            <div class="kb-empty-state">
              Notizen werden geladen…
            </div>
          </div>

          <div id="customerReportList" class="d-none" data-notes-panel="customerReport">
            <div class="kb-panel-header">
              <div>
                <div class="kb-panel-header-title">
                  <span class="kb-panel-icon"><i class="feather icon-file-text"></i></span>
                  <span>Kunden Bericht</span>
                </div>
                <div class="kb-panel-header-sub">
                  Aktuelle Berichte zu diesem Kunden, Objekt und Produkt. Neueste Berichte werden oben angezeigt.
                </div>
              </div>

              <button type="button" class="btn btn-primary kb-btn-brand kb-new-customer-report">
                <i class="feather icon-plus"></i>
                Bericht hinzufügen
              </button>
            </div>

            <div id="kbCustomerReportContent">
              <div class="kb-empty-state">Kunden Bericht wird geladen…</div>
            </div>
          </div>

          <div id="notesReport" class="d-none" data-notes-panel="report">
            <div class="kb-panel-header">
              <div>
                <div class="kb-panel-header-title">
                  <span class="kb-panel-icon"><i class="feather icon-calendar"></i></span>
                  <span>Termin Bericht</span>
                </div>
                <div class="kb-panel-header-sub">
                  Termine werden als Kalender-Klappkarten angezeigt. Jeder Termin zeigt, ob ein Bericht vorhanden ist.
                  <span id="kbTerminOpenInfo" class="ml-50"></span>
                </div>
              </div>
            </div>

            <div id="kbTerminReportContent">
              <div class="kb-empty-state">Termin Bericht wird geladen…</div>
            </div>
          </div>
        </div>

        <div class="notes-foot kb-note-composer-foot">
          <form id="notesForm" class="notes-composer kb-chatgpt-composer" autocomplete="off">
            <div class="kb-chatgpt-editor-wrap">
              <div id="noteEditor" class="notes-quill kb-chatgpt-editor"></div>
              <input type="hidden" id="noteText">
            </div>

            <button class="btn btn-primary kb-chatgpt-send" type="submit" title="Notiz senden">
              <i class="feather icon-send"></i>
            </button>
          </form>

          <input type="hidden" id="notesCustomerId">
          <input type="hidden" id="notesAlternativeId">
          <input type="hidden" id="notesProductId">
          <input type="hidden" id="notesLeadProductListId">
        </div>
      </aside>

      <div id="kbReportModalBackdrop" class="kb-report-modal-backdrop" aria-hidden="true">
        <div class="kb-report-modal" role="dialog" aria-modal="true" aria-labelledby="kbReportModalTitleText">
          <div class="kb-report-modal-head">
            <div class="kb-report-modal-title">
              <span id="kbReportModalIcon"><i class="feather icon-file-text"></i></span>
              <div>
                <div id="kbReportModalTitleText">Bericht hinzufügen</div>
                <small class="text-muted">Speichern wird direkt mit dem Kanban Customer Panel verbunden.</small>
              </div>
            </div>

            <button type="button" class="kb-report-modal-close" data-kb-report-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>

          <form id="kbReportForm" autocomplete="off">
            @csrf
            <div class="kb-report-modal-body">
              <div id="kbReportAppointmentInfo" class="alert alert-info" hidden></div>

              <div class="kb-form-grid">
                <div class="kb-form-group">
                  <label>Titel</label>
                  <input type="text" name="title" class="kb-form-control" placeholder="Titel schreiben…">
                </div>

                <div class="kb-form-group">
                  <label>Datum</label>
                  <input type="date" name="report_date" class="kb-form-control" value="{{ now()->toDateString() }}">
                </div>
              </div>

              <div class="kb-form-grid">
                <div class="kb-form-group">
                  <label>Phase</label>
                  <select name="stage" class="kb-form-control">
                    <option value="">Phase wählen…</option>
                    <option value="lead">Lead</option>
                    <option value="offer">Angebot</option>
                    <option value="deal">Auftrag</option>
                    <option value="project">Montage</option>
                    <option value="completed">Abschluss</option>
                    <option value="Kunden Bericht">Kunden Bericht</option>
                  </select>
                </div>

                <div class="kb-form-group">
                  <label>Typ</label>
                  <input type="text" name="type" class="kb-form-control" value="Kunden Bericht" readonly>
                </div>
              </div>

              <div class="kb-form-group">
                <label>Bericht *</label>
                <textarea name="report" class="kb-form-control" rows="6" placeholder="Bericht schreiben…" required></textarea>
              </div>

              <div class="kb-form-grid">
                <div class="kb-form-group">
                  <label>Nächster Schritt</label>
                  <textarea name="next_step" class="kb-form-control" rows="3" placeholder="Optional, besonders für Termin Bericht…"></textarea>
                </div>

                <div class="kb-form-group">
                  <label>Fällig am</label>
                  <input type="date" name="due_date" class="kb-form-control">
                </div>
              </div>
            </div>

            <div class="kb-report-modal-foot">
              <button type="button" class="btn btn-outline-secondary" data-kb-report-close>Abbrechen</button>
              <button type="submit" class="btn btn-primary kb-btn-brand" data-kb-report-submit>
                <i class="feather icon-save"></i>
                Speichern
              </button>
            </div>
          </form>
        </div>
      </div>


          {{-- Appointment Drawer --}}
    <!-- BACKDROP -->
      <div id="ap-backdrop" class="notes-backdrop" data-ap-close></div>

      <!-- DRAWER -->
      <aside id="ap-drawer" class="notes-drawer ap-drawer" role="dialog" aria-modal="true" aria-labelledby="ap-title">
        <!-- Header -->
        <header class="notes-head ap-head">
          <div class="notes-title ap-head-left">
            <span class="ap-head-icon"><i class="feather icon-calendar"></i></span>
            <div class="ap-head-title">
              <div class="ap-head-row">
                <span id="ap-title" class="ap-title">Termine</span>
                <span id="ap-count" class="badge badge-secondary ml-2">0</span>
              </div>
              <div class="ap-head-sub text-muted small">
                Kalender • Liste • Mitarbeiter-Filter
              </div>
            </div>
          </div>

          <div class="ap-head-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-ap-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </header>

        <!-- Tabs -->
        <nav class="ap-tabs" role="tablist" aria-label="Termine Navigation">
          <button type="button" class="ap-tab-link active" data-tab="calendar" role="tab" aria-selected="true">
            <i class="feather icon-layout"></i>
            <span>Übersicht</span>
          </button>
          <button type="button" class="ap-tab-link" data-tab="form" role="tab" aria-selected="false">
            <i class="feather icon-plus-circle"></i>
            <span>Neu / Bearbeiten</span>
          </button>
        </nav>

        <!-- CONTENT -->
        <section class="ap-body">
          <!-- TAB: Calendar/List -->
          <div id="ap-tab-calendar" class="ap-tab-content active" role="tabpanel">
            <!-- Toolbar -->
            <div class="ap-toolbar border-bottom bg-white">
              <div class="ap-toolbar-left">
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary active" data-view="calendar">
                    <i class="feather icon-grid"></i> Kalender
                  </button>
                  <button type="button" class="btn btn-outline-secondary" data-view="cards">
                    <i class="feather icon-list"></i> Liste
                  </button>
                </div>
              </div>

              <div class="ap-toolbar-right">
                <!-- Employee Filter (search appointments by employee) -->
                <div class="ap-filter">
                  <label for="ap-emp-filter" class="ap-filter-label text-muted small d-none d-lg-inline">Mitarbeiter</label>
                  <select id="ap-emp-filter" class="form-control form-control-sm select2" style="width:100%">
                    <option value="">Alle Mitarbeiter</option>
                  </select>
                </div>

                <!-- Jump to appointment -->
                <div class="ap-filter">
                  <label for="ap-jump" class="ap-filter-label text-muted small d-none d-lg-inline">Schnellsuche</label>
                  <select id="ap-jump" class="form-control form-control-sm select2" style="width:100%">
                    <option value="">— Termin auswählen (Springen) —</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Calendar -->
            <div id="ap-calendar-wrap" class="ap-calendar-wrap">
              <div id="ap-fullcalendar" class="ap-fullcalendar"></div>
            </div>

            <!-- Cards/List -->
            <div id="ap-card-view" class="ap-card-view" style="display:none;">
              <div class="text-center text-muted small my-2">Keine Termine geladen.</div>
            </div>
          </div>

          <!-- TAB: Form -->
          <div id="ap-tab-form" class="ap-tab-content" role="tabpanel">
            <div class="p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 id="ap-form-title" class="mb-0 font-weight-bold">Neuer Termin</h5>

                <button type="button" class="btn btn-sm btn-light border" id="ap-btn-back-to-cal">
                  <i class="feather icon-arrow-left"></i> Zurück
                </button>
              </div>

              <form id="ap-form" autocomplete="off">
                <input type="hidden" id="ap-id">
                <input type="hidden" id="ap-customer_id">
                <input type="hidden" id="ap-alternative_id">
                <input type="hidden" id="ap-product_id">

                <div class="form-group mb-2">
                  <label class="small mb-1 font-weight-bold">Titel*</label>
                  <input type="text" class="form-control" id="ap-name" required placeholder="z.B. Beratungsgespräch">
                </div>

                <div class="form-group mb-2">
                  <label class="small mb-1">Notiz / Beschreibung</label>
                  <textarea class="form-control" id="ap-note" rows="2"></textarea>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6 mb-2">
                    <label class="small mb-1">Datum*</label>
                    <input type="date" class="form-control" id="ap-start_date" required>
                  </div>
                  <div class="form-group col-3 mb-2">
                    <label class="small mb-1">Von</label>
                    <input type="time" class="form-control" id="ap-start_time">
                  </div>
                  <div class="form-group col-3 mb-2">
                    <label class="small mb-1">Bis</label>
                    <input type="time" class="form-control" id="ap-end_time">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-4 mb-2">
                    <label class="small mb-1">Art</label>
                    <select class="form-control" id="ap-appointment_type">
                      <option value="">–</option>
                      <option value="Besichtigung">Besichtigung</option>
                      <option value="Beratung">Beratung</option>
                      <option value="Telefonat">Telefonat</option>
                      <option value="Online-Meeting">Online-Meeting</option>
                    </select>
                  </div>
                  <div class="form-group col-md-4 mb-2">
                    <label class="small mb-1">Kontaktweg</label>
                    <select class="form-control" id="ap-contact_mode">
                      <option value="">–</option>
                      <option value="telefon">Telefon</option>
                      <option value="online">Online</option>
                      <option value="vor Ort">Vor Ort</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label class="small mb-1">Prio</label>
                    <select class="form-control" id="ap-priority">
                      <option value="normal">Normal</option>
                      <option value="high">Hoch</option>
                      <option value="low">Niedrig</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label class="small mb-1">Farbe</label>
                    <input type="color" class="form-control" id="ap-color" value="#74b2d4" style="height:36px; padding:2px;">
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label class="small mb-1 font-weight-bold">Mitarbeiter zuweisen</label>
                  <select id="ap-employee_ids" class="form-control select2" multiple style="width:100%">
                    @foreach ($employees as $e)
                      <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="p-2 bg-light border rounded mb-3">
                  <label class="small text-muted text-uppercase font-weight-bold mb-1">Adresse / Ort</label>
                  <input type="text" class="form-control mb-1 form-control-sm" id="ap-full_address" placeholder="Adresse suchen...">
                  <div class="form-row">
                    <div class="col-8 mb-1">
                      <input type="text" id="ap-street" class="form-control form-control-sm" placeholder="Straße">
                    </div>
                    <div class="col-4 mb-1">
                      <input type="text" id="ap-postcode" class="form-control form-control-sm" placeholder="PLZ">
                    </div>
                  </div>
                  <input type="text" class="form-control mb-1 form-control-sm" id="ap-city" placeholder="Ort">
                  <input type="hidden" id="ap-latitude">
                  <input type="hidden" id="ap-longitude">
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                  <button type="button" class="btn btn-outline-danger d-none" id="ap-btn-delete">
                    <i class="feather icon-trash-2"></i> Löschen
                  </button>
                  <button type="submit" class="btn btn-primary ml-auto">
                    <i class="feather icon-save"></i> Speichern
                  </button>
                </div>
              </form>
            </div>
          </div>
        </section>
      </aside>
<div id="pt-backdrop" class="notes-backdrop"></div>
      <aside id="pt-drawer" class="notes-drawer" role="dialog" aria-modal="true" aria-labelledby="pt-title" style="width:1300px !important;">
        <div class="notes-head">
          <div class="notes-title">
            <i class="feather icon-check-square"></i>
            <span id="pt-title">Aufgaben</span>
            <span id="pt-count" class="badge badge-secondary">0</span>
          </div>
          <div>
            <button class="btn btn-sm btn-outline-secondary" data-pt-close><i class="feather icon-x"></i></button>
          </div>
  
        </div>

        <div class="notes-body" id="pt-list" style="background:#f8fafc">
          <div class="text-center text-muted my-2">Lade Aufgaben…</div>
        </div>

        <div class="notes-foot">
          <form id="pt-form" class="notes-composer" autocomplete="off">
            <div class="w-100">
              <input class="form-control mb-1" id="pt-task_title" placeholder="Aufgabentitel*" required>
              <textarea class="form-control mb-1" id="pt-description" placeholder="Beschreibung (optional)"></textarea>
              <div class="d-flex flex-wrap gap-2">
                <input type="date" class="form-control mr-1 mb-1" id="pt-start_date" style="max-width:180px">
                <input type="date" class="form-control mr-1 mb-1" id="pt-due_date" style="max-width:180px">
                <input type="time" class="form-control mr-1 mb-1" id="pt-due_time" style="max-width:140px">
                <select class="form-control mr-1 mb-1" id="pt-priority" style="max-width:150px">
                  <option value="normal">Normal</option>
                  <option value="high">Hoch</option>
                  <option value="low">Niedrig</option>
                </select>
                <input type="color" class="form-control mb-1" id="pt-color" value="#8fc73e" style="max-width:70px; padding:0 2px;">
              </div>

              {{-- Hide this whole block when steps are used --}}
              <div id="pt-employee-wrap" class="mt-1">
                <label class="small text-muted mb-1">Mitarbeiter (für gesamte Aufgabe)</label>
                <select id="pt-employee_ids" class="form-control select2" multiple data-width="100%">
                  @foreach ($employees as $e)
                    <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Steps UI --}}
              <div class="border rounded p-2 mt-2 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Arbeitsschritte</strong>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="pt-add-step"><i class="feather icon-plus"></i> Schritt</button>
                </div>
                <div id="pt-steps" class="mt-2"></div>
                <small class="text-muted d-block mt-1">Wenn mindestens ein Schritt existiert, wird die Mitarbeiterauswahl der Hauptaufgabe ausgeblendet und pro Schritt vergeben.</small>
              </div>
            </div>
            <button class="btn btn-primary ml-2"><i class="feather icon-save"></i></button>
          </form>

          {{-- Hidden context from Kanban card --}}
          <input type="hidden" id="pt-customer_id">
          <input type="hidden" id="pt-alternative_id">
          <input type="hidden" id="pt-product_id">
        </div>
      </aside>
 
  </div><!-- /content-wrapper -->
</div>


 
<div id="kbTaskModalBackdrop" class="kb-task-backdrop" aria-hidden="true"></div>

<div id="kbTaskModal" class="kb-task-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kbTaskModalTitle">
  <div class="kb-task-modal-header">
    <div>
      <div class="kb-task-modal-title" id="kbTaskModalTitle">
        <i class="feather icon-list"></i>
        Aufgabenmanagement
      </div>
      <div class="kb-task-modal-sub" id="kbTaskContextText">Kunde • Objekt • Produkt • Stage</div>
    </div>

    <button type="button" class="kb-task-close" id="kbTaskModalClose" aria-label="Schließen">×</button>
  </div>

  <div class="kb-task-toolbar">
    <input type="search" id="kbTaskSearch" class="kb-task-search" placeholder="Aufgabe suchen …">

    <select id="kbTaskStatusFilter" class="kb-task-filter">
      <option value="">Alle Status</option>
      <option value="open">Offen</option>
      <option value="scheduled">Geplant</option>
      <option value="in_progress">In Bearbeitung</option>
      <option value="done">Erledigt</option>
      <option value="cancelled">Abgebrochen</option>
    </select>

    <button type="button" class="kb-task-primary" id="kbManualTaskBtn">
      <i class="feather icon-plus"></i>
      Manuelle Aufgabe
    </button>
  </div>

  <div id="kbTaskSequenceSummary" class="kb-task-sequence-summary">
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-log-in"></i> Seit Stage-Start</div>
      <div class="kb-task-seq-value" id="kbTaskSeqLanded">-</div>
      <div class="kb-task-seq-muted">Zeitpunkt, seit dem diese Karte in der aktuellen Stage liegt.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-check-circle"></i> Vorherige Aufgabe</div>
      <div class="kb-task-seq-value" id="kbTaskSeqPrevious">-</div>
      <div class="kb-task-seq-muted">Letzte erledigte Aufgabe.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-activity"></i> Aktuelle / eingehende Aufgabe</div>
      <div class="kb-task-seq-value" id="kbTaskSeqCurrent">-</div>
      <div class="kb-task-seq-muted">Nächste offene Aufgabe in dieser Stage/Sub-Stage.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</div>
      <div class="kb-task-seq-value" id="kbTaskSeqNext">-</div>
      <div class="kb-task-seq-muted">Folgeschritt gemäß Task-Phase-Sequenz.</div>
    </div>
  </div>

  <div class="kb-task-body">
    <div class="kb-task-column">
      <div class="kb-task-section-title">
        <i class="feather icon-layers"></i>
        Aufgaben aus aktueller Stage / Sub-Stage
      </div>
      <div id="kbTaskTemplates" class="kb-task-list">
        <div class="kb-task-empty">Aufgaben werden erst beim Öffnen geladen.</div>
      </div>
    </div>

    <div class="kb-task-column">
      <div class="kb-task-section-title">
        <i class="feather icon-check-square"></i>
        Erledigt / Nächste Aktion
      </div>
      <div id="kbTaskSaved" class="kb-task-list">
        <div class="kb-task-empty">Noch keine Aufgabe gespeichert.</div>
      </div>
    </div>
  </div>
</div>

<div id="kbTaskFormBackdrop" class="kb-task-form-backdrop" aria-hidden="true"></div>

<div id="kbTaskFormModal" class="kb-task-form-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kbTaskFormTitle">
  <div class="kb-task-form-header">
    <strong id="kbTaskFormTitle">Aufgabe planen</strong>
    <button type="button" class="kb-task-close" id="kbTaskFormClose" aria-label="Schließen">×</button>
  </div>

  <form id="kbTaskForm" autocomplete="off">
    @csrf
    <input type="hidden" id="kbFormLeadProductListId">
    <input type="hidden" id="kbFormTaskPhaseId">
    <input type="hidden" id="kbFormPhaseActivityId">
    <input type="hidden" id="kbFormExistingTaskId">
    <input type="hidden" id="kbFormMode" value="manual">

    <div class="kb-task-field">
      <label>Titel</label>
      <input type="text" id="kbFormTitle" required placeholder="z. B. Kunde anrufen / Montage vorbereiten">
    </div>

    <div class="kb-task-field">
      <label>Beschreibung</label>
      <textarea id="kbFormDescription" rows="3" placeholder="Was muss erledigt werden?"></textarea>
    </div>

    <div class="kb-task-grid">
      <div class="kb-task-field">
        <label>Start</label>
        <input type="datetime-local" id="kbFormStart">
      </div>

      <div class="kb-task-field">
        <label>Ende</label>
        <input type="datetime-local" id="kbFormEnd">
      </div>
    </div>

    <div class="kb-task-grid">
      <div class="kb-task-field">
        <label>Geschätzte Minuten</label>
        <input type="number" id="kbFormMinutes" min="1" placeholder="z. B. 60">
      </div>

      <div class="kb-task-field">
        <label>Performer</label>
        <select id="kbFormPerformer" class="kb-task-select2"></select>
      </div>
    </div>

    <div class="kb-task-field">
      <label>
        <input type="checkbox" id="kbFormScheduled">
        Aufgabe ist geplant / terminiert
      </label>
    </div>

    <div class="kb-task-field">
      <label>Weitere Mitarbeiter, die diese Aufgabe machen können</label>
      <select id="kbFormEmployees" multiple class="kb-task-select2"></select>
    </div>

    <div class="kb-task-convert-box">
      <label class="kb-task-check">
          <input type="checkbox" id="kbFormCreatePersonalTask">
          <span>Auch als persönliche Aufgabe erstellen</span>
      </label>

      <label class="kb-task-check">
          <input type="checkbox" id="kbFormCreateAppointment">
          <span>Auch als Termin erstellen</span>
      </label>

      <div id="kbAppointmentOptions" class="kb-appointment-options d-none">
          <div class="row">
              <div class="col-md-4">
                  <label>Terminart</label>
                  <select id="kbFormAppointmentType" class="form-control">
                      <option value="kanban_task">Kanban Aufgabe</option>
                      <option value="customer_appointment">Kundentermin</option>
                      <option value="internal">Intern</option>
                      <option value="phone">Telefon</option>
                      <option value="online">Online</option>
                  </select>
              </div>

              <div class="col-md-4">
                  <label>Kontaktart</label>
                  <select id="kbFormAppointmentContactMode" class="form-control">
                      <option value="">Keine</option>
                      <option value="phone">Telefon</option>
                      <option value="email">E-Mail</option>
                      <option value="onsite">Vor Ort</option>
                      <option value="online">Online</option>
                  </select>
              </div>

              <div class="col-md-4">
                  <label>Priorität</label>
                  <select id="kbFormAppointmentPriority" class="form-control">
                      <option value="normal">Normal</option>
                      <option value="high">Hoch</option>
                      <option value="urgent">Dringend</option>
                  </select>
              </div>
          </div>
      </div>
  </div>

    <div class="kb-task-field">
      <label>Interne Beschreibung / Ablauf</label>
      <textarea id="kbFormInternalNote" rows="3" placeholder="Interne Hinweise: wie die Arbeit gemacht werden soll"></textarea>
    </div>

    <div class="kb-task-form-actions">
      <button type="button" class="kb-task-secondary" id="kbTaskFormCancel">Abbrechen</button>
      <button type="submit" class="kb-task-primary">
        <i class="feather icon-save"></i>
        Speichern
      </button>
    </div>
  </form>
</div>
@stop


<div id="leadReminderToastWrap" class="lead-reminder-toast-wrap" aria-live="polite" aria-atomic="false"></div>

 @section('script')
@php
$leadStageNamesForJs = $stageNames ?? [
  'lead' => 'Lead',
  'offer' => 'Angebot',
  'follow_up' => 'Nachfassen',
  'accepted' => 'Annehmen',
  'deal' => 'Auftrag',
  'project' => 'Montage',
  'completed' => 'Abschluss',
  'archive' => 'Archive',
  'junk' => 'Junk',
];

$leadStageMetaForJs = $stageMeta ?? [];

// Kanban columns must not show Junk/Ticket because those have their own tabs.
$kanbanStageNamesForJs = collect($leadStageNamesForJs)
  ->reject(fn($label, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanStageMetaForJs = collect($leadStageMetaForJs)
  ->reject(fn($meta, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();
          @endphp

          <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
          <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
          <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/de.js"></script>
          <script src="{{ asset('js/select2.min.js') }}"></script>
          <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&language=de&region=DE">

          </script>


          <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
          


<!-- Kanban Column Script  -->


<!-- Aufgabe Script  -->
<!-- Termin Script -->


@if($canManageKanbanLeadStages)
@endif



          @php
            $kanbanBootProductsForJs = $kanbanProductsForJs ?? collect($products ?? [])
              ->map(function ($product) {
                return [
                  'id' => $product->id ?? null,
                  'name' => $product->article_group ?? $product->initial ?? ('Produkt #' . ($product->id ?? '')),
                  'initial' => $product->initial ?? null,
                ];
              })
              ->filter(function ($product) {
                return !empty($product['id']);
              })
              ->values()
              ->toArray();

            $kanbanBootBranchColorMap = collect($branches ?? [])
              ->mapWithKeys(function ($branch) {
                $name = mb_strtolower(trim((string) ($branch->branch ?? '')));
                $color = (string) ($branch->color ?? '#93c21c');

                return [$name => $color];
              })
              ->all();
          @endphp


          <script>
            window.KANBAN_CUSTOMER_PANEL_ROUTES = {
              counts: @json(route('kanban.customer-panel.counts')),
              customerReportsIndex: @json(route('kanban.customer-panel.customer-reports.index')),
              customerReportsStore: @json(route('kanban.customer-panel.customer-reports.store')),
              appointmentsIndex: @json(route('kanban.customer-panel.appointments.index')),
              appointmentReportsIndex: @json(route('kanban.customer-panel.appointments.reports.index', ['appointment' => '__APPOINTMENT__'])),
              appointmentReportsStore: @json(route('kanban.customer-panel.appointments.reports.store', ['appointment' => '__APPOINTMENT__']))
            };
          </script>

          <script>
            window.KANBAN_BOOT = {
              csrf: @json(csrf_token()),
              authUserId: @json(auth()->user()->name ?? ''),
              employees: @json($employees ?? []),
              leadStageNamesForJs: @json($leadStageNamesForJs ?? []),
              leadStageMetaForJs: @json($leadStageMetaForJs ?? []),
              kanbanStageNamesForJs: @json($kanbanStageNamesForJs ?? []),
              kanbanStageMetaForJs: @json($kanbanStageMetaForJs ?? []),
              kanbanProductsForJs: @json($kanbanBootProductsForJs),
              branchColorMap: @json($kanbanBootBranchColorMap)
            };
          </script>

          <script src="{{ asset('js/kanban.js') }}?v={{ time() }}"></script>
@endsection
