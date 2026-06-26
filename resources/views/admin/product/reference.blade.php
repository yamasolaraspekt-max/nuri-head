@extends('admin.layouts.app')

@section('title') REFERENZEN @stop

@section('style')
<style>
    :root {
        --ref-primary: #93c21c;
        --ref-bg: #f5f7fb;
        --ref-card: #ffffff;
        --ref-border: #e5e7eb;
        --ref-text: #111827;
        --ref-muted: #6b7280;
        --ref-radius: 18px;
        --ref-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .app-content .content-wrapper {
        background: var(--ref-bg);
    }

    .reference-shell {
        background: var(--ref-card);
        border-radius: var(--ref-radius);
        box-shadow: var(--ref-shadow);
        border: 1px solid var(--ref-border);
        padding: 1.25rem 1.5rem 1.5rem;
    }

    /* Map area */
    .reference-map-wrapper {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #00000005;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.10);
    }

    #map {
        width: 100%;
        height: 520px;
    }

    /* Search toolbar on top of map */
    .reference-toolbar {
        position: absolute;
        top: 1rem;
        left: 1rem;
        right: 1rem;
        z-index: 10;
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: flex-end;
    }

    .reference-toolbar .form-group {
        margin-bottom: 0;
    }

    .reference-toolbar .form-control,
    .reference-toolbar .btn {
        border-radius: 999px;
    }

    .reference-toolbar .form-control {
        background: rgba(255, 255, 255, 0.97);
        border-color: rgba(148, 163, 184, 0.45);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }

    .reference-toolbar .form-control:focus {
        box-shadow: 0 0 0 2px rgba(147, 194, 28, 0.45);
        border-color: rgba(147, 194, 28, 0.85);
    }

    .reference-toolbar .btn-primary {
        background: var(--ref-primary);
        border-color: var(--ref-primary);
        font-weight: 600;
        letter-spacing: .02em;
        box-shadow: 0 12px 30px rgba(147, 194, 28, 0.55);
    }

    .reference-toolbar .btn-primary:hover {
        filter: brightness(0.95);
        box-shadow: 0 18px 45px rgba(147, 194, 28, 0.55);
    }

    /* Ensure Google autocomplete dropdown is visible */
    .pac-container {
        z-index: 20000 !important;
    }

    /* KPI cards */
    .kpi-card {
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.06);
        background: var(--ref-card);
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, background .16s ease;
    }

    .kpi-card .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, .04);
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.10);
        border-color: rgba(147, 194, 28, 0.45);
    }

    .kpi-card.clickable {
        cursor: pointer;
    }

    .kpi-card.kpi-active {
        box-shadow:
            0 0 0 1px rgba(147, 194, 28, 0.4),
            0 16px 40px rgba(147, 194, 28, 0.15);
        border-color: rgba(147, 194, 28, 0.7);
        background: linear-gradient(135deg, #fafff2, #ffffff);
    }

    /* NEW: custom InfoWindow design */
    .gm-style .ref-infowindow {
        font-family: inherit;
        min-width: 240px;
        max-width: 280px;
        padding: 0.75rem 0.85rem;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(148, 163, 184, 0.4);
    }

    .gm-style .ref-infowindow-header {
        display: flex;
        align-items: center;
        margin-bottom: .35rem;
    }

    .gm-style .ref-infowindow-avatar {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: rgba(147, 194, 28, 0.12);
        color: #3f6212;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .8rem;
        margin-right: .55rem;
    }

    .gm-style .ref-infowindow-name {
        font-weight: 600;
        color: var(--ref-text);
        font-size: .9rem;
        line-height: 1.1;
    }

    .gm-style .ref-infowindow-id {
        font-size: .7rem;
        color: var(--ref-muted);
    }

    .gm-style .ref-infowindow-address {
        font-size: .78rem;
        color: var(--ref-muted);
        margin-bottom: .35rem;
        display: flex;
        align-items: flex-start;
        gap: .25rem;
    }

    .gm-style .ref-infowindow-address i {
        font-size: .85rem;
        margin-top: .1rem;
    }

    .gm-style .ref-infowindow-products {
        margin-bottom: .4rem;
    }

    .gm-style .ref-infowindow-products .badge {
        font-size: .68rem;
        border-radius: 999px;
        padding: .17rem .42rem;
    }

    .gm-style .ref-infowindow-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .5rem;
    }

    .gm-style .ref-infowindow-footer .distance-chip {
        font-size: .7rem;
        border-radius: 999px;
        padding: .15rem .5rem;
        background: #f3f4ff;
        color: #4338ca;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }

    .gm-style .ref-infowindow-footer .btn-ref-profile {
        border-radius: 999px;
        border: 1px solid rgba(147, 194, 28, 0.8);
        padding: .22rem .65rem;
        font-size: .72rem;
        font-weight: 600;
        color: #3f6212;
        background: linear-gradient(135deg, #f7fee7, #ecfccb);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }

    .gm-style .ref-infowindow-footer .btn-ref-profile:hover {
        text-decoration: none;
        filter: brightness(.97);
    }

    /* Results / table */
    #result-list .table {
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    #result-list .table thead {
        background: #f9fafb;
    }

    #result-list .table thead th {
        border-bottom-width: 0;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ref-muted);
    }

    .result-row {
        position: relative;
        transition: background-color .18s ease, transform .12s ease, box-shadow .18s ease;
        cursor: pointer;
    }

    .result-row:hover {
        background: #f9fdf3;
        transform: translateY(-1px);
    }

    .result-row-active {
        background: #f2ffe0 !important;
        box-shadow: inset 0 0 0 1px rgba(147, 194, 28, 0.55);
    }

    .result-row-active::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(circle at center, rgba(147, 194, 28, 0.18), transparent 55%);
        opacity: 1;
    }

    .result-row-distance {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding-inline: .5rem;
        border-radius: 999px;
    }

    .result-row-badge-pulse::after {
        content: "";
        position: absolute;
        inset: -2px;
        border-radius: 999px;
        border: 2px solid rgba(147, 194, 28, 0.5);
        animation: ref-pulse 1.25s ease-out 2;
    }

    /* Map focus ring (center) when selecting a row/marker */
    .map-focus-ring {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 90px;
        height: 90px;
        border-radius: 999px;
        border: 2px solid rgba(147, 194, 28, 0.45);
        box-shadow: 0 0 0 6px rgba(147, 194, 28, 0.25);
        pointer-events: none;
        transform: translate(-50%, -50%);
        animation: ref-pulse-soft 1.6s ease-out 1;
    }

    /* Tabs */
    .nav-tabs.reference-tabs {
        border-bottom-color: rgba(148, 163, 184, 0.4);
    }

    .nav-tabs.reference-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        font-weight: 600;
        color: var(--ref-muted);
        padding-bottom: .4rem;
    }

    .nav-tabs.reference-tabs .nav-link.active {
        color: var(--ref-text);
        border-color: var(--ref-primary);
    }

    /* Analytics chart boxes */
    .chart-box {
        position: relative;
        height: 260px;
    }

    .chart-box canvas {
        width: 100% !important;
        height: 100% !important;
    }

    /* Live search */
    #liveSearch {
        border-radius: 999px;
        padding-left: 2.25rem;
        background: #f9fafb;
        border-color: rgba(15, 23, 42, 0.06);
    }
    /* Remove native Google InfoWindow chrome and scrollbar */
    .gm-style .gm-style-iw-c {
        padding: 0 !important;
        max-width: none !important;
        max-height: none !important;
        background: transparent !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    .gm-style .gm-style-iw-d {
        overflow: visible !important;
        max-height: none !important;
    }

    /* Remove the inner chrome wrapper spacing */
    .gm-style .gm-style-iw-chr {
        padding: 0 !important;
    }

    /* Hide default close (X) button */
    .gm-style .gm-ui-hover-effect {
        display: none !important;
    }

    .gm-style .gm-style-iw {
        background: transparent !important;
        box-shadow: none !important;
    }



    #liveSearch:focus {
        background: #fff;
        box-shadow: 0 0 0 2px rgba(147, 194, 28, 0.35);
        border-color: rgba(147, 194, 28, 0.8);
    }

    .search-icon-inline {
        position: absolute;
        left: .9rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: .9rem;
        color: var(--ref-muted);
    }

        /* Row that matches searched address */
    .result-row-exact {
          background: #f0fdf4;
          box-shadow:
              inset 0 0 0 1px rgba(34, 197, 94, 0.7),
              0 6px 18px rgba(22, 163, 74, 0.18);
          position: relative;
      }

      .result-row-exact::before {
          content: "";
          position: absolute;
          left: 0;
          top: 0;
          bottom: 0;
          width: 3px;
          border-radius: 999px;
          background: linear-gradient(to bottom, #22c55e, #4ade80);
      }

      .result-match-badge {
          font-size: .7rem;
          border-radius: 999px;
          padding: .1rem .55rem;
          background: #dcfce7;
          color: #166534;
          display: inline-flex;
          align-items: center;
          gap: .25rem;
      }

      .result-match-badge i {
          font-size: .85rem;
      }

      /* InfoWindow pill for matched address */
      .gm-style .ref-infowindow-match {
          font-size: .7rem;
          border-radius: 999px;
          padding: .15rem .55rem;
          background: #dcfce7;
          color: #166534;
          display: inline-flex;
          align-items: center;
          gap: .25rem;
          margin-left: .35rem;
      }

      .ref-infowindow {
    position: relative;
}

.ref-infowindow-close {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 24px;
    height: 24px;
    border-radius: 999px;
    border: none;
    background: rgba(15,23,42,0.05);
    color: #6b7280;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.ref-infowindow-close:hover {
    background: rgba(15,23,42,0.12);
    color: #111827;
}

    /* Responsive map toolbar */
    @media (max-width: 767.98px) {
        .reference-toolbar {
            position: static;
            margin: .75rem;
        }

        .reference-map-wrapper {
            border-radius: 14px;
        }

        #map {
            height: 420px;
        }
    }

    /* Animations */
    @keyframes ref-pulse {
        0% {
            opacity: 1;
            transform: scale(1);
        }

        100% {
            opacity: 0;
            transform: scale(1.35);
        }
    }

    @keyframes ref-pulse-soft {
        0% {
            opacity: .85;
            transform: translate(-50%, -50%) scale(0.85);
        }

        100% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(1.3);
        }
    }
</style>
@endsection


@section('content')

@php
    $kpi = [
        'customers' => $totalCustomers ?? ($totals['customers'] ?? 0),
        'offers'    => $totalOffers ?? ($totals['offers'] ?? 0),
        'deals'     => $totalDeals ?? ($totals['deals'] ?? 0),
        'projects'  => $totalProjects ?? ($totals['projects'] ?? 0),
        'tickets'   => $totalTickets ?? ($totals['tickets'] ?? 0),
        'products'  => $totalProducts ?? ($totals['products'] ?? 0),
    ];
@endphp

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">REFERENZEN</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="#">Liste</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="reference-shell">

                {{-- MAP + SEARCH --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="reference-map-wrapper">
                            <div id="map"></div>
                            <div id="mapFocusRing" class="map-focus-ring" style="display:none;"></div>

                            <div class="reference-toolbar">
                                <div class="flex-grow-1">
                                    <div class="form-group mb-0">
                                        <label for="address" class="small text-uppercase text-muted mb-1">Adresse</label>
                                        <input type="text" id="address" class="form-control"
                                               placeholder="Adresse eingeben oder wählen…">
                                    </div>
                                </div>
                                <div style="width: 140px;">
                                    <div class="form-group mb-0">
                                        <label for="radius" class="small text-uppercase text-muted mb-1">Radius (km)</label>
                                        <input type="number" id="radius" class="form-control"
                                               value="5" min="1">
                                    </div>
                                </div>
                                <div style="width: 150px;">
                                    <button class="btn btn-primary btn-block" type="button" onclick="searchNearby()">
                                        <i class="feather icon-target mr-50"></i> Suchen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI CARDS --}}
                <div class="row g-2 g-md-3 mb-3">
                    @php $t = $totals ?? ['customers'=>0,'offers'=>0,'deals'=>0,'projects'=>0,'tickets'=>0,'products'=>0]; @endphp

                    <div class="col-6 col-sm-4 col-lg-2">
                        <div id="kpi-customers-card" class="card shadow-sm border-0 h-100 kpi-card clickable">
                            <div class="card-body d-flex align-items-center gap-2">
                                <div class="badge badge-primary p-1 mr-1 rounded">
                                    <i class="feather icon-users"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Kunden</div>
                                    <div class="h4 mb-0">
                                        <span id="kpi-customers">{{ number_format(($totals['customers'] ?? 0)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Offers --}}
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div id="kpi-offers-card" class="card shadow-sm border-0 h-100 kpi-card clickable" data-stage="offer">
                            <div class="card-body d-flex align-items-center gap-2">
                                <div class="badge badge-warning p-1 mr-1 rounded">
                                    <i class="feather icon-file-text"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Angebote</div>
                                    <div class="h4 mb-0">
                                        <span id="kpi-offers">{{ number_format(($totals['offers'] ?? 0)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Deals --}}
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div id="kpi-deals-card" class="card shadow-sm border-0 h-100 kpi-card clickable" data-stage="deal">
                            <div class="card-body d-flex align-items-center gap-2">
                                <div class="badge badge-success p-1 mr-1 rounded">
                                    <i class="feather icon-check-circle"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Abschlüsse</div>
                                    <div class="h4 mb-0">
                                        <span id="kpi-deals">{{ number_format(($totals['deals'] ?? 0)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Projects --}}
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div id="kpi-projects-card" class="card shadow-sm border-0 h-100 kpi-card clickable" data-stage="project">
                            <div class="card-body d-flex align-items-center gap-2">
                                <div class="badge badge-info p-1 mr-1 rounded">
                                    <i class="feather icon-briefcase"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Projekte</div>
                                    <div class="h4 mb-0">
                                        <span id="kpi-projects">{{ number_format(($totals['projects'] ?? 0)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tickets --}}
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div id="kpi-tickets-card" class="card shadow-sm border-0 h-100 kpi-card clickable">
                            <div class="card-body d-flex align-items-center gap-2">
                                <div class="badge badge-danger p-1 mr-1 rounded">
                                    <i class="feather icon-life-buoy"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Tickets</div>
                                    <div class="h4 mb-0">
                                        <span id="kpi-tickets">{{ number_format(($totals['tickets'] ?? 0)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Products + dropdown --}}
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="dropdown w-100">
                            <div id="kpi-products-card"
                                 class="card shadow-sm border-0 h-100 kpi-card dropdown-toggle"
                                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                 style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center gap-2">
                                    <div class="badge badge-secondary p-1 mr-1 rounded">
                                        <i class="feather icon-box"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-muted small d-flex align-items-center justify-content-between">
                                            <span>Produkte</span>
                                            <i class="feather icon-chevron-down"></i>
                                        </div>
                                        <div class="h4 mb-0">
                                            <span id="kpi-products">{{ number_format(($totals['products'] ?? 0)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="productsMenu" class="dropdown-menu dropdown-menu-right w-100"
                                 style="max-height: 260px; overflow:auto;">
                                <a class="dropdown-item" href="#" data-product="">Alle Produkte</a>
                                <div class="dropdown-divider"></div>
                                <div class="px-3 py-1 text-muted small">Lade…</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RESULTS + ANALYTICS --}}
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="mb-0 mr-2">Gefundene Einträge:</h5>
                            <span class="badge badge-light-primary ml-50 px-1 py-50">
                                <span id="count">0</span>
                            </span>
                        </div>

                        <ul class="nav nav-tabs mb-3 reference-tabs" id="resultsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-list" data-toggle="tab"
                                   href="#pane-list" role="tab" aria-controls="pane-list"
                                   aria-selected="true">
                                    <i class="feather icon-list mr-50"></i> Liste
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-analytics" data-toggle="tab"
                                   href="#pane-analytics" role="tab" aria-controls="pane-analytics"
                                   aria-selected="false">
                                    <i class="feather icon-pie-chart mr-50"></i> Analytics
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            {{-- LIST TAB --}}
                            <div class="tab-pane fade show active" id="pane-list" role="tabpanel" aria-labelledby="tab-list">
                                <div class="position-relative mb-3">
                                    <i class="feather icon-search search-icon-inline"></i>
                                    <input type="text" id="liveSearch" class="form-control"
                                           placeholder="Ergebnisse durchsuchen…">
                                </div>
                                <div id="result-list" class="row g-3"></div>
                            </div>

                            {{-- ANALYTICS TAB --}}
                            <div class="tab-pane fade" id="pane-analytics" role="tabpanel" aria-labelledby="tab-analytics">
                                <div class="row">
                                    <div class="col-lg-5 mb-3">
                                        <div class="card shadow-sm border-0 h-100">
                                            <div class="card-header d-flex align-items-center">
                                                <i class="feather icon-pie-chart mr-50"></i>
                                                <strong>Stage-Verteilung</strong>
                                            </div>
                                            <div class="card-body">
                                                <div class="chart-box">
                                                    <canvas id="stagePieChart"></canvas>
                                                </div>
                                                <div id="pieEmpty" class="text-center text-muted small mt-2" style="display:none;">
                                                    Keine Daten für diese Auswahl.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7 mb-3">
                                        <div class="card shadow-sm border-0 h-100">
                                            <div class="card-header d-flex align-items-center">
                                                <i class="feather icon-bar-chart-2 mr-50"></i>
                                                <strong>Top-Produkte</strong>
                                            </div>
                                            <div class="card-body">
                                                <div class="chart-box">
                                                    <canvas id="topProductsBarChart"></canvas>
                                                </div>
                                                <div id="barEmpty" class="text-center text-muted small mt-2" style="display:none;">
                                                    Keine Daten für diese Auswahl.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="small text-muted mt-1" id="analyticsSummary"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- /reference-shell --}}
        </div>
    </div>
</div>
@endsection
 @section('script')
<script>
    // Toastr flash messages
    $(function () {
        @if(Session::has('update_msg'))
            toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script>

<script>
    // ============================================================
    // GLOBAL STATE
    // ============================================================
    window.activeFilter  = { stage: null, product: null };
    window.allMarkers    = [];   // [{ marker, card, item, infoWindow, isExactMatch }]
    window.lastResults   = [];   // raw JSON response from /leads-nearby

    let map;
    let geocoder;
    let autocomplete;
    let clusterer        = null;
    let searchCircle     = null;
    let currentInfoWindow = null;

    // Default center (Frankfurt)
    let userLat = 50.1109;
    let userLon = 8.6821;

    const charts = {
        stagePie:   null,
        productBar: null,
    };

    // ============================================================
    // MAP INITIALIZATION + AUTOCOMPLETE
    // ============================================================
    function initMap() {
          geocoder = new google.maps.Geocoder();

          map = new google.maps.Map(document.getElementById('map'), {
              center: { lat: userLat, lng: userLon },
              zoom: 15,
              mapTypeControl: false,
              streetViewControl: false,

              // IMPORTANT: remove "Ctrl + scroll" overlay
              gestureHandling: 'greedy',   // scroll zoom always
              scrollwheel: true            // (optional, usually implied by greedy)
          });

          map.addListener('click', () => {
                if (currentInfoWindow) {
                    currentInfoWindow.close();
                    currentInfoWindow = null;
                }
            });


          const input = document.getElementById('address');
          if (input && google.maps.places) {
              autocomplete = new google.maps.places.Autocomplete(input, {
                  fields: ['geometry', 'formatted_address'],
                  componentRestrictions: { country: 'DE' }
              });

              autocomplete.addListener('place_changed', () => {
                  const place = autocomplete.getPlace();
                  if (!place || !place.geometry) {
                      alert('Keine gültige Adresse gefunden.');
                      return;
                  }

                  userLat = place.geometry.location.lat();
                  userLon = place.geometry.location.lng();
                  const radius = parseFloat(document.getElementById('radius')?.value || '5');

                  focusMap(userLat, userLon, radius);
                  fetchNearbyLocations(userLat, userLon, radius);
                  showMapFocusRing();
              });
          }

          fetchNearbyLocations();
      }

    window.initMap = initMap;

    // ============================================================
    // MAP VIEW / CIRCLE / FOCUS RING
    // ============================================================
    function focusMap(lat, lon, radiusKm = 5) {
        const center = {
            lat: parseFloat(lat),
            lng: parseFloat(lon),
        };

        map.panTo(center);
        setTimeout(() => map.setZoom(12), 250);

        const radiusMeters = (parseFloat(radiusKm) || 0) * 1000;

        if (!searchCircle) {
            searchCircle = new google.maps.Circle({
                strokeColor: '#0d6efd',
                strokeOpacity: 0.6,
                strokeWeight: 1,
                fillColor: '#0d6efd',
                fillOpacity: 0.08,
                map,
                center,
                radius: radiusMeters,
                clickable: false,
            });
        } else {
            searchCircle.setCenter(center);
            searchCircle.setRadius(radiusMeters);
            searchCircle.setMap(map);
        }
    }

    function showMapFocusRing() {
        const ring = document.getElementById('mapFocusRing');
        if (!ring) return;

        ring.style.display   = 'block';
        ring.style.animation = 'none';
        // force reflow to restart animation
        void ring.offsetWidth;
        ring.style.animation = '';
        setTimeout(() => { ring.style.display = 'none'; }, 1600);
    }

    // ============================================================
    // FETCH + RENDER NEARBY LOCATIONS
    // ============================================================
    function fetchNearbyLocations(lat = null, lon = null, radius = null) {
        const list    = document.getElementById('result-list');
        const countEl = document.getElementById('count');

        // reset global InfoWindow
        if (currentInfoWindow) {
            currentInfoWindow.close();
            currentInfoWindow = null;
        }

        // clear markers / clusters
        if (clusterer && typeof clusterer.clearMarkers === 'function') {
            clusterer.clearMarkers();
        }
        window.allMarkers.forEach(obj => obj.marker.setMap(null));
        window.allMarkers = [];

        if (countEl) countEl.textContent = '...';
        if (list) {
            list.innerHTML = `
                <div class="col-12 text-center text-muted py-3">
                    <div class="spinner-border spinner-border-sm mr-25"></div> Lade Daten...
                </div>`;
        }

        let url = '/leads-nearby';
        if (lat && lon && radius) {
            url += `?lat=${lat}&lon=${lon}&radius=${radius}`;
        }

        fetch(url)
            .then(r => r.json())
            .then(data => {
                window.lastResults = Array.isArray(data) ? data : [];
                if (countEl) countEl.textContent = window.lastResults.length;

                if (list) {
                    list.innerHTML = `
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0" id="resultsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:36px;"></th>
                                            <th>Name</th>
                                            <th>Adresse</th>
                                            <th>Produkte</th>
                                            <th class="text-nowrap">Entfernung</th>
                                            <th style="width:130px;">Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>`;
                }

                const tbody = list?.querySelector('tbody');
                const gMarkers = [];
                window.allMarkers = [];

                const hasCenter = (lat !== null && lat !== undefined &&
                                   lon !== null && lon !== undefined);

                window.lastResults.forEach(item => {
                    if (!item.lat || !item.lon) return;

                    const position = {
                        lat: parseFloat(item.lat),
                        lng: parseFloat(item.lon),
                    };

                    const iconUrl      = getMarkerIcon(item.product_statuses || '');
                    const productsHtml = buildProductsBadges(item.product_statuses);

                    let numericDistance = null;
                    let distanceLabel   = '–';

                    if (hasCenter) {
                        numericDistance = haversineDistance(lat, lon, item.lat, item.lon);
                        distanceLabel   = `${numericDistance.toFixed(1)} km`;
                    }

                    // "Exact" match: within 50m of search center
                    const isExactMatch = hasCenter && numericDistance !== null && numericDistance < 0.05;

                    const marker = new google.maps.Marker({
                        position,
                        map,
                        icon: {
                            url: iconUrl,
                            scaledSize: new google.maps.Size(36, 36),
                            anchor: new google.maps.Point(18, 36),
                        },
                    });
                    gMarkers.push(marker);

                    const initials = (
                        (item.customer_name || 'K').charAt(0) +
                        (item.customer_lastname || '').charAt(0)
                    ).toUpperCase();

                    const matchPillHtml = isExactMatch
                        ? `<span class="ref-infowindow-match"><i class="feather icon-check"></i> Adresse aus Suche</span>`
                        : '';

                        const infoWindow = new google.maps.InfoWindow({
                          content: `
                              <div class="ref-infowindow" data-customer-id="${item.customer_id}">
                                  <button type="button" class="ref-infowindow-close" title="Schließen">×</button>
                                  <div class="ref-infowindow-header">
                                      <div class="ref-infowindow-avatar">${initials}</div>
                                      <div>
                                          <div class="ref-infowindow-name">
                                              ${(item.customer_name ?? '')} ${(item.customer_lastname ?? '')}
                                          </div>
                                          <div class="ref-infowindow-id">Kunde #${item.customer_id}</div>
                                      </div>
                                  </div>
                                  <div class="ref-infowindow-address">
                                      <i class="feather icon-map-pin"></i>
                                      <span>${item.full_address ?? 'Keine Adresse'}</span>
                                  </div>
                                  <div class="ref-infowindow-products">
                                      ${productsHtml}
                                  </div>
                                  <div class="ref-infowindow-footer">
                                      <div>
                                          <span class="distance-chip">
                                              <i class="feather icon-rss"></i> ${distanceLabel}
                                          </span>
                                          ${matchPillHtml}
                                      </div>
                                      <a href="/new_lead_profile/${item.customer_id}" target="_blank"
                                        class="btn-ref-profile">
                                          <i class="feather icon-user-check"></i> Profil
                                      </a>
                                  </div>
                              </div>`
                      });


                      infoWindow.addListener('domready', () => {
                            const selector = `.ref-infowindow[data-customer-id="${item.customer_id}"] .ref-infowindow-close`;
                            const btn = document.querySelector(selector);
                            if (btn) {
                                btn.addEventListener('click', () => {
                                    infoWindow.close();
                                    if (currentInfoWindow === infoWindow) {
                                        currentInfoWindow = null;
                                    }
                                });
                            }
                        });

                    let rowElement = null;

                    if (tbody) {
                        const tr = document.createElement('tr');
                        tr.className = 'result-row' + (isExactMatch ? ' result-row-exact' : '');

                        const matchBadgeHtml = isExactMatch
                            ? `
                                <div class="mt-25">
                                    <span class="badge badge-success result-match-badge">
                                        <i class="feather icon-check"></i> Adresse aus Suche
                                    </span>
                                </div>`
                            : '';

                        tr.innerHTML = `
                            <td class="text-center align-middle">
                                <img src="${iconUrl}" alt="pin" style="width:22px;height:22px;">
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <i class="feather icon-user mr-50"></i>
                                    <div>
                                        <div class="font-weight-semibold">
                                            ${(item.customer_name ?? '')} ${(item.customer_lastname ?? '')}
                                        </div>
                                        <div class="small text-muted">#${item.customer_id}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <div>
                                        <i class="feather icon-map-pin mr-50"></i>
                                        <span>${item.full_address ?? ''}</span>
                                    </div>
                                    ${matchBadgeHtml}
                                </div>
                            </td>
                            <td class="align-middle">
                                ${productsHtml}
                            </td>
                            <td class="align-middle text-nowrap">
                                <span class="result-row-distance">
                                    <i class="feather icon-rss mr-50"></i>${distanceLabel}
                                </span>
                            </td>
                            <td class="align-middle">
                                <a href="/new_lead_profile/${item.customer_id}" target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather icon-user-check mr-50"></i>Profil
                                </a>
                            </td>`;

                        tr.addEventListener('click', e => {
                            if (e.target.closest('a,button')) return;
                            focusOnMarker(marker, position, infoWindow, tr);
                        });

                        tbody.appendChild(tr);
                        rowElement = tr;
                    }

                    marker.addListener('click', () => {
                        focusOnMarker(marker, position, infoWindow, rowElement);
                    });

                    window.allMarkers.push({
                        marker,
                        card: rowElement,
                        item,
                        infoWindow,
                        isExactMatch,
                    });
                });

                // clustering (if lib is loaded)
                if (window.markerClusterer && typeof markerClusterer.MarkerClusterer === 'function') {
                    clusterer = new markerClusterer.MarkerClusterer({ map, markers: gMarkers });
                }

                updateKpisFromResults(window.lastResults);
                populateProductsMenuFromResults(window.lastResults);
                applyFilters();
            })
            .catch(err => {
                console.error('Fetch /leads-nearby failed:', err);
                if (list) {
                    list.innerHTML = `
                        <div class="col-12 text-center text-danger py-3">
                            Fehler beim Laden der Daten.
                        </div>`;
                }
                if (countEl) countEl.textContent = '0';
            });
    }
    window.fetchNearbyLocations = fetchNearbyLocations;

    // Focus helper: pan map, open InfoWindow, highlight row
    function focusOnMarker(marker, position, infoWindow, row) {
        if (!marker || !position) return;

        map.panTo(position);
        marker.setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => marker.setAnimation(null), 1200);

        // Close previous InfoWindow
        if (currentInfoWindow && currentInfoWindow !== infoWindow) {
            currentInfoWindow.close();
        }
        if (infoWindow) {
            infoWindow.open(map, marker);
            currentInfoWindow = infoWindow;
        }

        highlightRow(row);
        showMapFocusRing();
    }

    function highlightRow(row) {
        const table = document.getElementById('resultsTable');
        if (!table || !row) return;

        table.querySelectorAll('tbody tr.result-row')
            .forEach(r => r.classList.remove('result-row-active'));

        row.classList.add('result-row-active');

        table.querySelectorAll('.result-row-distance')
            .forEach(el => el.classList.remove('result-row-badge-pulse'));

        const dist = row.querySelector('.result-row-distance');
        if (dist) dist.classList.add('result-row-badge-pulse');

        row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    // ============================================================
    // SEARCH (BUTTON + ENTER + GEOCODE)
    // ============================================================
    function searchNearby() {
        const addressVal = document.getElementById('address')?.value.trim() || '';
        const radius     = parseFloat(document.getElementById('radius')?.value || '5');

        // empty address -> use current center
        if (!addressVal) {
            focusMap(userLat, userLon, radius);
            fetchNearbyLocations(userLat, userLon, radius);
            showMapFocusRing();
            return;
        }

        if (!geocoder) {
            geocoder = new google.maps.Geocoder();
        }

        geocoder.geocode(
            { address: addressVal, componentRestrictions: { country: 'DE' } },
            (results, status) => {
                if (status !== 'OK' || !results?.length) {
                    alert('Keine gültige Adresse gefunden.');
                    return;
                }

                const loc = results[0].geometry.location;
                userLat = loc.lat();
                userLon = loc.lng();

                focusMap(userLat, userLon, radius);
                fetchNearbyLocations(userLat, userLon, radius);
                showMapFocusRing();
            }
        );
    }
    window.searchNearby = searchNearby;

    // ============================================================
    // KPI + ANALYTICS
    // ============================================================
    function computeAnalytics(items) {
        const stageBuckets = {
            'Anfrage':      0,
            'Angebot':      0,
            'Abschluss':    0,
            'Projekt':      0,
            'Abgeschlossen':0,
            'Abgelehnt':    0,
            'Sonstiges':    0,
        };

        const productCounts = new Map();

        (items || []).forEach(it => {
            const tokens = parseProductStatuses(it.product_statuses);

            tokens.forEach(({ name, stage }) => {
                if (name) {
                    productCounts.set(name, (productCounts.get(name) || 0) + 1);
                }

                const s = (stage || '').toLowerCase();
                if (s.includes('lead'))           stageBuckets['Anfrage']++;
                else if (s.includes('offer'))     stageBuckets['Angebot']++;
                else if (s.includes('deal'))      stageBuckets['Abschluss']++;
                else if (s.includes('project'))   stageBuckets['Projekt']++;
                else if (s.includes('completed')) stageBuckets['Abgeschlossen']++;
                else if (s.includes('reject') || s.includes('absage'))
                    stageBuckets['Abgelehnt']++;
                else                              stageBuckets['Sonstiges']++;
            });
        });

        const topProducts = Array.from(productCounts.entries())
            .sort((a, b) => b[1] - a[1])
            .slice(0, 10);

        return {
            stageBuckets,
            topProducts,
            totalProducts: productCounts.size,
        };
    }

    function resetCanvasSize(canvas) {
        if (!canvas) return;
        canvas.style.width  = '';
        canvas.style.height = '';
        canvas.removeAttribute('width');
        canvas.removeAttribute('height');
    }

    function updateAnalyticsCharts(items) {
        const { stageBuckets, topProducts, totalProducts } = computeAnalytics(items);

        // --- PIE ---
        const pieCanvas = document.getElementById('stagePieChart');
        const pieEmpty  = document.getElementById('pieEmpty');
        const pieLabels = Object.keys(stageBuckets);
        const pieData   = Object.values(stageBuckets);
        const pieTotal  = pieData.reduce((a, b) => a + b, 0);

        if (charts.stagePie) {
            charts.stagePie.destroy();
            charts.stagePie = null;
        }
        resetCanvasSize(pieCanvas);

        if (!pieTotal) {
            if (pieEmpty) pieEmpty.style.display = 'block';
        } else {
            if (pieEmpty) pieEmpty.style.display = 'none';
            charts.stagePie = new Chart(pieCanvas.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieData,
                        backgroundColor: [
                            '#4e79a7','#f28e2b','#59a14f',
                            '#76b7b2','#9c755f','#e15759',
                            '#edc949'
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.label}: ${ctx.parsed}`
                            }
                        }
                    }
                }
            });
        }

        // --- BAR ---
        const barCanvas = document.getElementById('topProductsBarChart');
        const barEmpty  = document.getElementById('barEmpty');
        const barLabels = topProducts.map(([n]) => n);
        const barData   = topProducts.map(([, c]) => c);
        const barTotal  = barData.reduce((a, b) => a + b, 0);

        if (charts.productBar) {
            charts.productBar.destroy();
            charts.productBar = null;
        }
        resetCanvasSize(barCanvas);

        if (!barTotal) {
            if (barEmpty) barEmpty.style.display = 'block';
        } else {
            if (barEmpty) barEmpty.style.display = 'none';
            charts.productBar = new Chart(barCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Anzahl',
                        data: barData,
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        const summary = document.getElementById('analyticsSummary');
        if (summary) {
            const customers = Array.isArray(items) ? items.length : 0;
            summary.textContent =
                `Auswahl: ${customers} Kunden · ${pieTotal} Produkt-Status-Vorkommen · ${totalProducts} unterschiedliche Produkte`;
        }
    }

    function updateKpisFromResults(data) {
        const nf  = new Intl.NumberFormat();
        const set = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = nf.format(val);
        };

        const customers = Array.isArray(data) ? data.length : 0;
        let offers = 0;
        let deals  = 0;
        let projects = 0;

        const seenO = new Set();
        const seenD = new Set();
        const seenP = new Set();
        const uniqueProducts = new Set();

        (data || []).forEach(it => {
            const cid = it.customer_id;
            let hasO = false;
            let hasD = false;
            let hasP = false;

            parseProductStatuses(it.product_statuses).forEach(({ name, stage }) => {
                if (name) uniqueProducts.add(name);
                const s = stage.toLowerCase();
                if (s.includes('offer'))   hasO = true;
                if (s.includes('deal'))    hasD = true;
                if (s.includes('project')) hasP = true;
            });

            if (hasO && !seenO.has(cid)) { offers++;   seenO.add(cid); }
            if (hasD && !seenD.has(cid)) { deals++;    seenD.add(cid); }
            if (hasP && !seenP.has(cid)) { projects++; seenP.add(cid); }
        });

        set('kpi-customers', customers);
        set('kpi-offers',   offers);
        set('kpi-deals',    deals);
        set('kpi-projects', projects);
        set('kpi-products', uniqueProducts.size);
    }
    window.updateKpisFromResults = updateKpisFromResults;

    function populateProductsMenuFromResults(data) {
        const menu = document.getElementById('productsMenu');
        if (!menu) return;

        const names = new Set();
        (data || []).forEach(it => {
            parseProductStatuses(it.product_statuses)
                .forEach(({ name }) => { if (name) names.add(name); });
        });

        const sorted = Array.from(names)
            .sort((a, b) => a.localeCompare(b, 'de'));

        menu.innerHTML = [
            `<a class="dropdown-item" href="#" data-product="">Alle Produkte</a>`,
            `<div class="dropdown-divider"></div>`,
            ...sorted.map(n =>
                `<a class="dropdown-item" href="#" data-product="${n.replace(/"/g, '&quot;')}">${n}</a>`
            )
        ].join('');
    }
    window.populateProductsMenuFromResults = populateProductsMenuFromResults;

    // ============================================================
    // FILTER ENGINE
    // ============================================================
    function applyFilters() {
        const term = (document.getElementById('liveSearch')?.value || '').toLowerCase();

        let visibleCount = 0;
        const visibleItems = [];

        window.allMarkers.forEach(({ item, marker, card, infoWindow }) => {
            if (!item || !card) return;

            let ok = true;

            if (activeFilter.stage) {
                const statuses = (item.product_statuses || '').toLowerCase();
                ok = statuses.includes(activeFilter.stage);
            }

            if (ok && activeFilter.product) {
                const hasProduct = parseProductStatuses(item.product_statuses)
                    .some(({ name }) =>
                        name.toLowerCase() === activeFilter.product.toLowerCase()
                    );
                ok = hasProduct;
            }

            if (ok && term) {
                ok = card.innerText.toLowerCase().includes(term);
            }

            card.style.display = ok ? '' : 'none';
            marker.setMap(ok ? map : null);

            // close InfoWindow of filtered-out marker
            if (!ok && currentInfoWindow === infoWindow) {
                currentInfoWindow.close();
                currentInfoWindow = null;
            }

            if (ok) {
                visibleCount++;
                visibleItems.push(item);
            }
        });

        const countEl = document.getElementById('count');
        if (countEl) countEl.textContent = visibleCount;

        updateKpisFromResults(visibleItems);
        updateAnalyticsCharts(visibleItems);
    }
    window.applyFilters = applyFilters;

    function setStageFilter(stage) {
        window.activeFilter.stage = stage;
        updateActiveKpiUi();
        applyFilters();
    }
    window.setStageFilter = setStageFilter;

    function setProductFilter(productName) {
        window.activeFilter.product = productName || null;
        updateActiveKpiUi();
        applyFilters();
    }
    window.setProductFilter = setProductFilter;

    function updateActiveKpiUi() {
        const ids = ['customers','offers','deals','projects','tickets','products'];

        ids.forEach(id => {
            document.getElementById(`kpi-${id}-card`)
                ?.classList.remove('kpi-active');
        });

        // default: customers (no filter)
        if (!activeFilter.stage && !activeFilter.product) {
            document.getElementById('kpi-customers-card')
                ?.classList.add('kpi-active');
        }

        if (activeFilter.stage) {
            const stageMap = { offer: 'offers', deal: 'deals', project: 'projects' };
            const key = stageMap[activeFilter.stage];
            if (key) {
                document.getElementById(`kpi-${key}-card`)
                    ?.classList.add('kpi-active');
            }
        }

        if (activeFilter.product) {
            document.getElementById('kpi-products-card')
                ?.classList.add('kpi-active');
        }
    }
    window.updateActiveKpiUi = updateActiveKpiUi;

    // ============================================================
    // UTILITIES
    // ============================================================
    function parseProductStatuses(raw) {
        if (!raw) return [];
        return raw
            .split(',')
            .map(s => s.trim())
            .filter(Boolean)
            .map(tok => {
                const m = tok.match(/(.+?)\s*\((.+?)\)/);
                return {
                    name:  (m?.[1] || tok).trim(),
                    stage: (m?.[2] || '').trim(),
                };
            });
    }

    function buildProductsBadges(raw) {
        const tokens = parseProductStatuses(raw);
        if (!tokens.length) {
            return '<span class="badge badge-secondary">Keine</span>';
        }
        return tokens.map(({ name, stage }) =>
            `<span class="badge badge-info mr-50 mb-25">${name} (${translateStatusToGerman(stage)})</span>`
        ).join('');
    }

    function translateStatusToGerman(status) {
        const s = (status || '').toLowerCase();
        if (s.includes('lead'))       return 'Anfrage';
        if (s.includes('offer'))      return 'Angebot';
        if (s.includes('deal'))       return 'Abschluss';
        if (s.includes('project'))    return 'Projekt';
        if (s.includes('completed'))  return 'Abgeschlossen';
        if (s.includes('open'))       return 'Offen';
        if (s.includes('cancel'))     return 'Absage';
        if (s.includes('accept'))     return 'Offen';
        if (s.includes('archive'))    return 'Archiv';
        if (s.includes('reject') || s.includes('absage')) return 'Abgelehnt';
        return status || '';
    }

    function getMarkerIcon(statuses) {
        const s = (statuses || '').toLowerCase();
        if (s.includes('lead'))      return '/images/pins/blue.png';
        if (s.includes('offer'))     return '/images/pins/orange.png';
        if (s.includes('deal'))      return '/images/pins/green.png';
        if (s.includes('project'))   return '/images/pins/teal.png';
        if (s.includes('completed')) return '/images/pins/gray.png';
        if (s.includes('reject') || s.includes('absage')) return '/images/pins/red.png';
        return '/images/pins/map.png';
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R   = 6371;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);

        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                  Math.sin(dLon / 2) ** 2;

        return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
    }

    function toRad(val) {
        return val * Math.PI / 180;
    }

    // ============================================================
    // DOM READY BINDINGS
    // ============================================================
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('liveSearch')
            ?.addEventListener('input', applyFilters);

        ['offers','deals','projects'].forEach(key => {
            document.getElementById(`kpi-${key}-card`)
                ?.addEventListener('click', () => setStageFilter(key.slice(0, -1)));
        });

        document.getElementById('kpi-customers-card')
            ?.addEventListener('click', () => {
                window.activeFilter = { stage: null, product: null };
                updateActiveKpiUi();
                applyFilters();
            });

        document.getElementById('kpi-tickets-card')
            ?.addEventListener('click', () => {
                window.location.href = '/tickets';
            });

        document.getElementById('productsMenu')
            ?.addEventListener('click', e => {
                const a = e.target.closest('a[data-product]');
                if (!a) return;
                e.preventDefault();
                setProductFilter(a.getAttribute('data-product') || null);
            });

        document.getElementById('address')
            ?.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchNearby();
                }
            });

        updateActiveKpiUi();
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places&callback=initMap" async defer></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
