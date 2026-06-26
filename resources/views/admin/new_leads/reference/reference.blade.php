@extends('admin.layouts.app')

@section('title') REFERENZEN @stop

@php
    $googleMapsKey = config('services.google.maps_key', 'AIzaSyByZgrvtQbWdEfRWf9hXRk4ZWiEP2mLFMk');
@endphp

@section('style')
    <style>
        :root {
            --app-bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --primary: #93c21c;
            --primary-hover: #7baa18;
            --primary-light: #f4fae7;
            --blue: #74b2d4;
            --blue-light: #eff6ff;
            --success: #10b981;
            --success-light: #ecfdf5;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --gray: #6b7280;
            --gray-light: #f3f4f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --radius: 14px;
            --transition: all .2s ease-in-out;
        }

        .app-content .content-wrapper,
        .app-content.content .content-wrapper {
            background: var(--app-bg);
        }

        .content-body {
            color: var(--text-main);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .reference-shell {
            background: transparent;
            border: 0;
            border-radius: 0;
            box-shadow: none;
            padding: 0;
        }

        .content-header-title {
            font-weight: 900;
            letter-spacing: -.025em;
            color: #111827;
        }

        .breadcrumb,
        .breadcrumb a {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 800;
        }

        .reference-map-wrapper {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: #ffffff;
            box-shadow: var(--shadow-sm);
            margin-bottom: 18px;
        }

        #map {
            width: 100%;
            height: 560px;
            background: #e5e7eb;
        }

        .reference-toolbar {
            position: absolute;
            top: 16px;
            left: 16px;
            right: 16px;
            z-index: 10;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: flex-end;
            background: rgba(255, 255, 255, .94);
            border: 1px solid rgba(229, 231, 235, .92);
            border-radius: var(--radius);
            padding: 14px 16px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        .reference-toolbar .form-group {
            margin-bottom: 0;
        }

        .reference-toolbar label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted) !important;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .reference-toolbar .form-control {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            height: 42px;
        }

        .reference-toolbar .form-control:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .reference-toolbar .btn-primary,
        .oc-btn {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            text-decoration: none;
            box-shadow: none !important;
        }

        .reference-toolbar .btn-primary:hover,
        .oc-btn:hover {
            background: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
            color: #fff !important;
            text-decoration: none;
        }

        .kpi-card {
            background: var(--card-bg) !important;
            border: 1px solid var(--border) !important;
            border-radius: 16px !important;
            padding: 0;
            box-shadow: var(--shadow-sm) !important;
            min-height: 92px;
            cursor: pointer;
            transition: var(--transition);
            overflow: hidden;
        }

        .kpi-card:hover {
            border-color: var(--primary) !important;
            box-shadow: var(--shadow) !important;
            transform: translateY(-2px);
        }

        .kpi-card.kpi-active {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--primary-light), var(--shadow-sm) !important;
            background: linear-gradient(135deg, #fafff2, #ffffff) !important;
        }

        .kpi-card .card-body {
            min-height: 92px;
            padding: 16px !important;
            gap: 12px !important;
        }

        .kpi-card .badge {
            width: 48px;
            height: 48px;
            border-radius: 14px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .kpi-card .badge-primary {
            background: var(--blue-light) !important;
            color: var(--blue) !important;
        }

        .kpi-card .badge-warning {
            background: var(--warning-light) !important;
            color: #d97706 !important;
        }

        .kpi-card .badge-success {
            background: var(--success-light) !important;
            color: var(--success) !important;
        }

        .kpi-card .badge-info {
            background: var(--primary-light) !important;
            color: var(--primary-hover) !important;
        }

        .kpi-card .badge-danger {
            background: var(--danger-light) !important;
            color: var(--danger) !important;
        }

        .kpi-card .badge-secondary {
            background: var(--gray-light) !important;
            color: var(--gray) !important;
        }

        .kpi-card .text-muted.small {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted) !important;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .kpi-card .h4 {
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
            margin-top: 4px;
        }

        .reference-tabs {
            background: #fff;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius);
            padding: 8px;
            box-shadow: var(--shadow-sm);
            gap: 6px;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            width: 100%;
            list-style: none;
        }

        .reference-tabs.nav-tabs {
            border-bottom: 1px solid var(--border) !important;
        }

        .reference-tabs .nav-item {
            display: inline-flex !important;
            width: auto !important;
            margin: 0 !important;
        }

        .reference-tabs .nav-link {
            border: 0 !important;
            border-radius: 10px !important;
            color: var(--text-muted) !important;
            font-weight: 900;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            white-space: nowrap;
            width: auto !important;
            min-width: 110px;
        }

        .reference-tabs .nav-link.active {
            background: var(--primary-light) !important;
            color: #365314 !important;
        }

        .reference-tabbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .reference-tabbar-title {
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            margin: 0;
        }

        .reference-tabbar-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        #liveSearch {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px 10px 36px;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            min-height: 42px;
        }

        #liveSearch:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .search-icon-inline {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            width: 17px;
            height: 17px;
            color: var(--text-muted);
            z-index: 2;
        }

        .oc-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .oc-list-head {
            display: grid;
            grid-template-columns: 70px minmax(230px, 1.4fr) minmax(260px, 1.7fr) minmax(260px, 1.5fr) 130px 140px;
            gap: 14px;
            align-items: center;
            padding: 16px 16px 10px 16px;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .oc-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 0 0 16px 0;
        }

        .oc-item,
        .result-row {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            transition: var(--transition);
            overflow: hidden;
            margin: 0 16px;
            cursor: pointer;
        }

        .oc-item:hover,
        .result-row:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .result-row-active {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--primary-light), var(--shadow-sm) !important;
            background: #fafff2 !important;
        }

        .result-row-exact {
            border-color: var(--success) !important;
            background: #f0fdf4 !important;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, .15);
        }

        .oc-item-row {
            padding: 16px;
            display: grid;
            gap: 16px;
            align-items: center;
            grid-template-columns: 70px minmax(230px, 1.4fr) minmax(260px, 1.7fr) minmax(260px, 1.5fr) 130px 140px;
        }

        .oc-cell {
            min-width: 0;
        }

        .oc-cell-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
            display: none;
        }

        .oc-id-badge,
        .result-row-distance {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 54px;
            height: 36px;
            padding: 0 12px;
            border-radius: 10px;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 13px;
            font-weight: 900;
            gap: 6px;
            position: relative;
        }

        .oc-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .oc-ttl {
            font-weight: 800;
            font-size: 15px;
            margin-bottom: 4px;
            color: #111827;
        }

        .oc-subt {
            font-size: 13px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .oc-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .oc-btn-soft,
        .btn-ref-profile,
        .btn.btn-sm.btn-outline-primary {
            background: #fff !important;
            color: var(--text-main) !important;
            border: 1px solid var(--border) !important;
            padding: 8px 12px !important;
            border-radius: 10px !important;
            font-weight: 800 !important;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            box-shadow: none !important;
        }

        .oc-btn-soft:hover,
        .btn-ref-profile:hover,
        .btn.btn-sm.btn-outline-primary:hover {
            background: var(--primary-light) !important;
            border-color: var(--primary) !important;
            color: #365314 !important;
            text-decoration: none !important;
        }

        .badge.badge-info,
        .oc-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            background: var(--primary-light) !important;
            color: #365314 !important;
            border: 1px solid rgba(147, 194, 28, .25);
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .badge.badge-secondary {
            background: var(--gray-light) !important;
            color: var(--gray) !important;
            border-radius: 999px;
        }

        .result-match-badge {
            font-size: 12px;
            border-radius: 999px;
            padding: 4px 10px;
            background: var(--success-light) !important;
            color: #047857 !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .result-row-badge-pulse::after {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: 999px;
            border: 2px solid rgba(147, 194, 28, .55);
            animation: refPulse 1.25s ease-out 2;
        }

        .chart-box {
            position: relative;
            height: 270px;
        }

        .chart-box canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .tab-pane .card,
        #pane-analytics .card {
            border: 1px solid var(--border) !important;
            border-radius: 16px !important;
            box-shadow: var(--shadow-sm) !important;
            overflow: hidden;
        }

        #pane-analytics .card-header {
            background: #fafafa !important;
            border-bottom: 1px solid var(--border) !important;
            font-weight: 900;
            color: #111827;
        }

        .map-focus-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 90px;
            height: 90px;
            border-radius: 999px;
            border: 2px solid rgba(147, 194, 28, .45);
            box-shadow: 0 0 0 6px rgba(147, 194, 28, .25);
            pointer-events: none;
            transform: translate(-50%, -50%);
            animation: refPulseSoft 1.6s ease-out 1;
            z-index: 9;
        }

        .gm-style .gm-style-iw-c,
        .gm-style .gm-style-iw,
        .gm-style .gm-style-iw-d {
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .gm-style .gm-ui-hover-effect {
            display: none !important;
        }

        .gm-style .gm-style-iw-chr {
            padding: 0 !important;
        }

        .ref-infowindow {
            position: relative;
            min-width: 260px;
            max-width: 320px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 14px;
            font-family: Inter, system-ui, sans-serif;
        }

        .ref-infowindow-close {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
        }

        .ref-infowindow-close:hover {
            background: var(--gray-light);
            color: #111827;
        }

        .ref-infowindow-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding-right: 24px;
        }

        .ref-infowindow-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: var(--primary-light);
            color: #365314;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
        }

        .ref-infowindow-name {
            font-weight: 900;
            color: #111827;
            font-size: 15px;
        }

        .ref-infowindow-id {
            font-size: 12px;
            color: var(--text-muted);
        }

        .ref-infowindow-address {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            gap: 6px;
            margin-bottom: 10px;
        }

        .ref-infowindow-products {
            margin-bottom: 10px;
        }

        .ref-infowindow-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .distance-chip,
        .ref-infowindow-match {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 900;
            border-radius: 999px;
            padding: 5px 10px;
            background: var(--blue-light);
            color: var(--blue);
        }

        .ref-infowindow-match {
            background: var(--success-light);
            color: #047857;
        }

        .customer-hover-card {
            pointer-events: none;
        }

        .result-row.preview-active {
            border-color: var(--blue) !important;
            box-shadow: 0 0 0 3px var(--blue-light), var(--shadow-sm) !important;
            background: #f8fbff !important;
        }

        .pac-container {
            z-index: 999999 !important;
        }

        @media(max-width:1280px) {
            .oc-list-head {
                display: none;
            }

            .oc-item-row {
                grid-template-columns: 1fr;
            }

            .oc-cell-title {
                display: block;
            }

            .oc-actions {
                justify-content: flex-start;
            }
        }

        @media(max-width:767.98px) {
            .reference-toolbar {
                position: static;
                margin: 12px;
            }

            #map {
                height: 430px;
            }

            .content-body {
                padding: 0 8px;
            }
        }

        @keyframes refPulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }

            100% {
                opacity: 0;
                transform: scale(1.35);
            }
        }

        @keyframes refPulseSoft {
            0% {
                opacity: .85;
                transform: translate(-50%, -50%) scale(.85);
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
            'offers' => $totalOffers ?? ($totals['offers'] ?? 0),
            'deals' => $totalDeals ?? ($totals['deals'] ?? 0),
            'projects' => $totalProjects ?? ($totals['projects'] ?? 0),
            'tickets' => $totalTickets ?? ($totals['tickets'] ?? 0),
            'products' => $totalProducts ?? ($totals['products'] ?? 0),
        ];
    @endphp

    <div class="app-content">
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
                                    <li class="breadcrumb-item"><a href="{{ url('new_lead_view') }}">Kundeliste</a></li>
                                    <li class="breadcrumb-item active">Referenzen & Karte</li>
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
                                            <label for="address"
                                                class="small text-uppercase text-muted mb-1">Adresse</label>
                                            <input type="text" id="address" class="form-control"
                                                placeholder="Adresse eingeben oder wählen…">
                                        </div>
                                    </div>
                                    <div style="width: 140px;">
                                        <div class="form-group mb-0">
                                            <label for="radius" class="small text-uppercase text-muted mb-1">Radius
                                                (km)</label>
                                            <input type="number" id="radius" class="form-control" value="5" min="1">
                                        </div>
                                    </div>
                                    <div style="width: 150px;">
                                        <button class="btn btn-primary btn-block" type="button" onclick="searchNearby()">
                                            <i data-lucide="map-pin" class="mr-50"></i> Suchen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KPI CARDS --}}
                    <div class="row g-2 g-md-3 mb-3">
                        @php $t = $totals ?? ['customers' => 0, 'offers' => 0, 'deals' => 0, 'projects' => 0, 'tickets' => 0, 'products' => 0]; @endphp

                        <div class="col-6 col-sm-4 col-lg-2">
                            <div id="kpi-customers-card" class="card shadow-sm border-0 h-100 kpi-card clickable">
                                <div class="card-body d-flex align-items-center gap-2">
                                    <div class="badge badge-primary p-1 mr-1 rounded">
                                        <i data-lucide="users" class=""></i>
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
                            <div id="kpi-offers-card" class="card shadow-sm border-0 h-100 kpi-card clickable"
                                data-stage="offer">
                                <div class="card-body d-flex align-items-center gap-2">
                                    <div class="badge badge-warning p-1 mr-1 rounded">
                                        <i data-lucide="file-text" class=""></i>
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
                            <div id="kpi-deals-card" class="card shadow-sm border-0 h-100 kpi-card clickable"
                                data-stage="deal">
                                <div class="card-body d-flex align-items-center gap-2">
                                    <div class="badge badge-success p-1 mr-1 rounded">
                                        <i data-lucide="check-circle" class=""></i>
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
                            <div id="kpi-projects-card" class="card shadow-sm border-0 h-100 kpi-card clickable"
                                data-stage="project">
                                <div class="card-body d-flex align-items-center gap-2">
                                    <div class="badge badge-info p-1 mr-1 rounded">
                                        <i data-lucide="briefcase" class=""></i>
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
                                        <i data-lucide="life-buoy" class=""></i>
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
                                <div id="kpi-products-card" class="card shadow-sm border-0 h-100 kpi-card dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    style="cursor:pointer;">
                                    <div class="card-body d-flex align-items-center gap-2">
                                        <div class="badge badge-secondary p-1 mr-1 rounded">
                                            <i data-lucide="box" class=""></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-muted small d-flex align-items-center justify-content-between">
                                                <span>Produkte</span>
                                                <i data-lucide="chevron-down" class=""></i>
                                            </div>
                                            <div class="h4 mb-0">
                                                <span
                                                    id="kpi-products">{{ number_format(($totals['products'] ?? 0)) }}</span>
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

                            <div class="reference-tabbar mb-3">
                                <div>
                                    <h3 class="reference-tabbar-title">Auswertung</h3>
                                    <div class="reference-tabbar-sub">Liste und Analytics der gefundenen Referenzen</div>
                                </div>
                                <ul class="nav nav-tabs reference-tabs" id="resultsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-list" data-toggle="tab" data-bs-toggle="tab"
                                            href="#pane-list" role="tab" aria-controls="pane-list" aria-selected="true">
                                            <i data-lucide="list" class="mr-50"></i> Liste
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="tab-analytics" data-toggle="tab" data-bs-toggle="tab"
                                            href="#pane-analytics" role="tab" aria-controls="pane-analytics"
                                            aria-selected="false">
                                            <i data-lucide="pie-chart" class="mr-50"></i> Analytics
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content">
                                {{-- LIST TAB --}}
                                <div class="tab-pane fade show active" id="pane-list" role="tabpanel"
                                    aria-labelledby="tab-list">
                                    <div class="position-relative mb-3">
                                        <i data-lucide="search" class=" search-icon-inline"></i>
                                        <input type="text" id="liveSearch" class="form-control"
                                            placeholder="Ergebnisse durchsuchen…">
                                    </div>
                                    <div id="result-list" class="row g-3"></div>
                                </div>

                                {{-- ANALYTICS TAB --}}
                                <div class="tab-pane fade" id="pane-analytics" role="tabpanel"
                                    aria-labelledby="tab-analytics">
                                    <div class="row">
                                        <div class="col-lg-5 mb-3">
                                            <div class="card shadow-sm border-0 h-100">
                                                <div class="card-header d-flex align-items-center">
                                                    <i data-lucide="pie-chart" class="mr-50"></i>
                                                    <strong>Stage-Verteilung</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="chart-box">
                                                        <canvas id="stagePieChart"></canvas>
                                                    </div>
                                                    <div id="pieEmpty" class="text-center text-muted small mt-2"
                                                        style="display:none;">
                                                        Keine Daten für diese Auswahl.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7 mb-3">
                                            <div class="card shadow-sm border-0 h-100">
                                                <div class="card-header d-flex align-items-center">
                                                    <i data-lucide="bar-chart-2" class="mr-50"></i>
                                                    <strong>Top-Produkte</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="chart-box">
                                                        <canvas id="topProductsBarChart"></canvas>
                                                    </div>
                                                    <div id="barEmpty" class="text-center text-muted small mt-2"
                                                        style="display:none;">
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
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

    <script>
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
        'use strict';

        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Kundeliste',
                url: "{{ url('new_lead_view') }}"
            },
            {
                label: 'Referenzen & Karte',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }

        window.referenceNearbyUrl = @json(route('lead.reference.nearby'));
        window.activeFilter = { stage: null, product: null };
        window.allMarkers = [];
        window.lastResults = [];

        let map = null;
        let geocoder = null;
        let autocomplete = null;
        let clusterer = null;
        let searchCircle = null;
        let currentInfoWindow = null;

        let userLat = 50.1109;
        let userLon = 8.6821;

        const charts = {
            stagePie: null,
            productBar: null,
        };

        function refreshReferenceIcons() {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }

        function safeText(value, fallback = '') {
            const text = String(value ?? '').trim();
            return text || fallback;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildUrl(baseUrl, params = {}) {
            const url = new URL(baseUrl, window.location.origin);

            Object.keys(params).forEach(key => {
                const value = params[key];
                if (value !== null && value !== undefined && value !== '') {
                    url.searchParams.set(key, value);
                }
            });

            return url.toString();
        }

        function initMap() {
            const mapElement = document.getElementById('map');

            if (!mapElement || !window.google || !google.maps) {
                console.error('Google Maps is not ready or #map was not found.');
                return;
            }

            geocoder = new google.maps.Geocoder();

            map = new google.maps.Map(mapElement, {
                center: { lat: userLat, lng: userLon },
                zoom: 15,
                mapTypeControl: false,
                streetViewControl: false,
                gestureHandling: 'greedy',
                scrollwheel: true,
            });

            map.addListener('click', function () {
                if (currentInfoWindow) {
                    currentInfoWindow.close();
                    currentInfoWindow = null;
                }
            });

            const input = document.getElementById('address');

            if (input && google.maps.places) {
                autocomplete = new google.maps.places.Autocomplete(input, {
                    fields: ['geometry', 'formatted_address'],
                    componentRestrictions: { country: 'DE' },
                });

                autocomplete.addListener('place_changed', function () {
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
            refreshReferenceIcons();
        }

        window.initMap = initMap;

        function focusMap(lat, lon, radiusKm = 5) {
            if (!map) return;

            const center = {
                lat: parseFloat(lat),
                lng: parseFloat(lon),
            };

            map.panTo(center);
            setTimeout(function () {
                map.setZoom(12);
            }, 250);

            const radiusMeters = (parseFloat(radiusKm) || 0) * 1000;

            if (!searchCircle) {
                searchCircle = new google.maps.Circle({
                    strokeColor: '#0d6efd',
                    strokeOpacity: 0.6,
                    strokeWeight: 1,
                    fillColor: '#0d6efd',
                    fillOpacity: 0.08,
                    map: map,
                    center: center,
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

            ring.style.display = 'block';
            ring.style.animation = 'none';
            void ring.offsetWidth;
            ring.style.animation = '';

            setTimeout(function () {
                ring.style.display = 'none';
            }, 1600);
        }

        function fetchNearbyLocations(lat = null, lon = null, radius = null) {
            const list = document.getElementById('result-list');
            const countEl = document.getElementById('count');

            if (currentInfoWindow) {
                currentInfoWindow.close();
                currentInfoWindow = null;
            }

            if (clusterer && typeof clusterer.clearMarkers === 'function') {
                clusterer.clearMarkers();
            }

            window.allMarkers.forEach(obj => {
                if (obj.marker) obj.marker.setMap(null);
            });

            window.allMarkers = [];

            if (countEl) countEl.textContent = '...';

            if (list) {
                list.innerHTML = `
                            <div class="col-12 text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm mr-25"></div> Lade Daten...
                            </div>`;
            }

            const url = buildUrl(window.referenceNearbyUrl, {
                lat: lat,
                lon: lon,
                radius: radius,
            });

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    window.lastResults = Array.isArray(data) ? data : [];

                    if (countEl) countEl.textContent = window.lastResults.length;

                    renderResultTableShell(list);
                    renderMarkersAndRows(window.lastResults, lat, lon);

                    updateKpisFromResults(window.lastResults);
                    populateProductsMenuFromResults(window.lastResults);
                    updateActiveKpiUi();
                    applyFilters();
                    refreshReferenceIcons();
                })
                .catch(error => {
                    console.error('Fetch reference nearby failed:', error);

                    if (list) {
                        list.innerHTML = `
                                    <div class="col-12 text-center text-danger py-3">
                                        Fehler beim Laden der Daten.
                                    </div>`;
                    }

                    if (countEl) countEl.textContent = '0';
                    updateKpisFromResults([]);
                    updateAnalyticsCharts([]);
                    refreshReferenceIcons();
                });
        }

        window.fetchNearbyLocations = fetchNearbyLocations;

        function renderResultTableShell(list) {
            if (!list) return;

            list.innerHTML = `
                        <div class="col-12 p-0">
                            <div class="oc-card" id="resultsTable">
                                <div class="oc-list-head">
                                    <div>Pin</div>
                                    <div>Kunde</div>
                                    <div>Adresse</div>
                                    <div>Produkte</div>
                                    <div>Entfernung</div>
                                    <div style="text-align:right;">Aktion</div>
                                </div>
                                <div class="oc-list" id="resultsListRows"></div>
                            </div>
                        </div>`;
        }

        function renderMarkersAndRows(items, lat = null, lon = null) {
            const tbody = document.querySelector('#resultsListRows');
            const googleMarkers = [];
            const hasCenter = lat !== null && lat !== undefined && lon !== null && lon !== undefined;

            window.allMarkers = [];

            (items || []).forEach(item => {
                if (!item.lat || !item.lon) return;

                const position = {
                    lat: parseFloat(item.lat),
                    lng: parseFloat(item.lon),
                };

                const iconUrl = getMarkerIcon(item.product_statuses || '');
                const productsHtml = buildProductsBadges(item.product_statuses);

                let numericDistance = null;
                let distanceLabel = '–';

                if (hasCenter) {
                    numericDistance = haversineDistance(lat, lon, item.lat, item.lon);
                    distanceLabel = `${numericDistance.toFixed(1)} km`;
                }

                const isExactMatch = hasCenter && numericDistance !== null && numericDistance < 0.05;

                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    icon: {
                        url: iconUrl,
                        scaledSize: new google.maps.Size(36, 36),
                        anchor: new google.maps.Point(18, 36),
                    },
                });

                googleMarkers.push(marker);

                const initials = (
                    safeText(item.customer_name, 'K').charAt(0) +
                    safeText(item.customer_lastname, '').charAt(0)
                ).toUpperCase();

                const matchPillHtml = isExactMatch
                    ? `<span class="ref-infowindow-match"><i data-lucide="check" class=""></i> Adresse aus Suche</span>`
                    : '';

                const customerName = `${safeText(item.customer_name)} ${safeText(item.customer_lastname)}`.trim() || 'Unbekannter Kunde';
                const address = safeText(item.full_address, 'Keine Adresse');
                const customerId = item.customer_id || '';

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                                <div class="ref-infowindow" data-customer-id="${escapeHtml(customerId)}">
                                    <button type="button" class="ref-infowindow-close" title="Schließen">×</button>
                                    <div class="ref-infowindow-header">
                                        <div class="ref-infowindow-avatar">${escapeHtml(initials)}</div>
                                        <div>
                                            <div class="ref-infowindow-name">${escapeHtml(customerName)}</div>
                                            <div class="ref-infowindow-id">Kunde #${escapeHtml(customerId)}</div>
                                        </div>
                                    </div>
                                    <div class="ref-infowindow-address">
                                        <i data-lucide="map-pin" class=""></i>
                                        <span>${escapeHtml(address)}</span>
                                    </div>
                                    <div class="ref-infowindow-products">${productsHtml}</div>
                                    <div class="ref-infowindow-footer">
                                        <div>
                                            <span class="distance-chip">
                                                <i data-lucide="radio" class=""></i> ${escapeHtml(distanceLabel)}
                                            </span>
                                            ${matchPillHtml}
                                        </div>
                                        <a href="/new_lead_profile/${encodeURIComponent(customerId)}" target="_blank" class="btn-ref-profile">
                                            <i data-lucide="user-check" class=""></i> Profil
                                        </a>
                                    </div>
                                </div>`,
                });

                infoWindow.addListener('domready', function () {
                    refreshReferenceIcons();

                    const selector = `.ref-infowindow[data-customer-id="${CSS.escape(String(customerId))}"] .ref-infowindow-close`;
                    const btn = document.querySelector(selector);

                    if (btn) {
                        btn.addEventListener('click', function () {
                            infoWindow.close();
                            if (currentInfoWindow === infoWindow) {
                                currentInfoWindow = null;
                            }
                        });
                    }
                });

                let rowElement = null;

                if (tbody) {
                    const tr = document.createElement('div');
                    tr.className = 'result-row' + (isExactMatch ? ' result-row-exact' : '');

                    const matchBadgeHtml = isExactMatch
                        ? `
                                    <div class="mt-1">
                                        <span class="result-match-badge">
                                            <i data-lucide="check" class=""></i> Adresse aus Suche
                                        </span>
                                    </div>`
                        : '';

                    tr.innerHTML = `
                                <div class="oc-item-row">
                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Pin</div>
                                        <img src="${escapeHtml(iconUrl)}" alt="pin" style="width:34px;height:34px;">
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Kunde</div>
                                        <div class="oc-main">
                                            <div class="oc-ttl"><i data-lucide="user" class="mr-50"></i>${escapeHtml(customerName)}</div>
                                            <div class="oc-subt">#${escapeHtml(customerId)}</div>
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Adresse</div>
                                        <div class="oc-main">
                                            <div class="oc-subt" style="white-space:normal;"><i data-lucide="map-pin" class="mr-50"></i>${escapeHtml(address)}</div>
                                            ${matchBadgeHtml}
                                        </div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Produkte</div>
                                        <div>${productsHtml}</div>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Entfernung</div>
                                        <span class="result-row-distance"><i data-lucide="radio" class="mr-50"></i>${escapeHtml(distanceLabel)}</span>
                                    </div>

                                    <div class="oc-cell">
                                        <div class="oc-cell-title">Aktion</div>
                                        <div class="oc-actions">
                                            <a href="/new_lead_profile/${encodeURIComponent(customerId)}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i data-lucide="user-check" class="mr-50"></i>Profil
                                            </a>
                                        </div>
                                    </div>
                                </div>`;

                    tr.addEventListener('click', function (event) {
                        if (event.target.closest('a,button')) return;
                        focusOnMarker(marker, position, infoWindow, tr);
                    });

                    tr.addEventListener('mouseenter', function () {
                        previewMarkerTooltip(marker, position, infoWindow, tr);
                    });

                    tr.addEventListener('mouseleave', function () {
                        tr.classList.remove('preview-active');
                    });

                    tbody.appendChild(tr);
                    rowElement = tr;
                }

                marker.addListener('click', function () {
                    focusOnMarker(marker, position, infoWindow, rowElement);
                });

                window.allMarkers.push({
                    marker: marker,
                    card: rowElement,
                    item: item,
                    infoWindow: infoWindow,
                    isExactMatch: isExactMatch,
                });
            });

            if (window.markerClusterer && typeof window.markerClusterer.MarkerClusterer === 'function') {
                clusterer = new window.markerClusterer.MarkerClusterer({
                    map: map,
                    markers: googleMarkers,
                });
            }

            refreshReferenceIcons();
        }

        function focusOnMarker(marker, position, infoWindow, row) {
            if (!marker || !position || !map) return;

            map.panTo(position);

            if (window.google?.maps?.Animation) {
                marker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(function () {
                    marker.setAnimation(null);
                }, 1200);
            }

            if (currentInfoWindow && currentInfoWindow !== infoWindow) {
                currentInfoWindow.close();
            }

            if (infoWindow) {
                infoWindow.open(map, marker);
                currentInfoWindow = infoWindow;
            }

            highlightRow(row);
            showMapFocusRing();
            refreshReferenceIcons();
        }

        function previewMarkerTooltip(marker, position, infoWindow, row) {
            if (!marker || !position || !map || !infoWindow) return;

            if (currentInfoWindow && currentInfoWindow !== infoWindow) {
                currentInfoWindow.close();
            }

            infoWindow.open(map, marker);
            currentInfoWindow = infoWindow;

            document.querySelectorAll('.result-row.preview-active')
                .forEach(item => item.classList.remove('preview-active'));

            if (row) {
                row.classList.add('preview-active');
            }

            refreshReferenceIcons();
        }

        function highlightRow(row) {
            const table = document.getElementById('resultsTable');
            if (!table || !row) return;

            table.querySelectorAll('.result-row')
                .forEach(r => r.classList.remove('result-row-active'));

            row.classList.add('result-row-active');

            table.querySelectorAll('.result-row-distance')
                .forEach(el => el.classList.remove('result-row-badge-pulse'));

            const distance = row.querySelector('.result-row-distance');
            if (distance) distance.classList.add('result-row-badge-pulse');

            row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }

        function searchNearby() {
            const addressVal = document.getElementById('address')?.value.trim() || '';
            const radius = parseFloat(document.getElementById('radius')?.value || '5');

            if (!addressVal) {
                focusMap(userLat, userLon, radius);
                fetchNearbyLocations(userLat, userLon, radius);
                showMapFocusRing();
                return;
            }

            if (!geocoder && window.google?.maps?.Geocoder) {
                geocoder = new google.maps.Geocoder();
            }

            if (!geocoder) {
                alert('Google Maps ist noch nicht bereit.');
                return;
            }

            geocoder.geocode({
                address: addressVal,
                componentRestrictions: { country: 'DE' },
            }, function (results, status) {
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
            });
        }

        window.searchNearby = searchNearby;

        function computeAnalytics(items) {
            const stageBuckets = {
                'Anfrage': 0,
                'Angebot': 0,
                'Abschluss': 0,
                'Projekt': 0,
                'Abgeschlossen': 0,
                'Abgelehnt': 0,
                'Sonstiges': 0,
            };

            const productCounts = new Map();

            (items || []).forEach(item => {
                parseProductStatuses(item.product_statuses).forEach(({ name, stage }) => {
                    if (name) {
                        productCounts.set(name, (productCounts.get(name) || 0) + 1);
                    }

                    const stageKey = (stage || '').toLowerCase();

                    if (stageKey.includes('lead')) stageBuckets['Anfrage']++;
                    else if (stageKey.includes('offer')) stageBuckets['Angebot']++;
                    else if (stageKey.includes('deal')) stageBuckets['Abschluss']++;
                    else if (stageKey.includes('project')) stageBuckets['Projekt']++;
                    else if (stageKey.includes('completed')) stageBuckets['Abgeschlossen']++;
                    else if (stageKey.includes('reject') || stageKey.includes('absage')) stageBuckets['Abgelehnt']++;
                    else stageBuckets['Sonstiges']++;
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
            canvas.style.width = '';
            canvas.style.height = '';
            canvas.removeAttribute('width');
            canvas.removeAttribute('height');
        }

        function updateAnalyticsCharts(items) {
            const { stageBuckets, topProducts, totalProducts } = computeAnalytics(items);

            const pieCanvas = document.getElementById('stagePieChart');
            const pieEmpty = document.getElementById('pieEmpty');
            const pieLabels = Object.keys(stageBuckets);
            const pieData = Object.values(stageBuckets);
            const pieTotal = pieData.reduce((a, b) => a + b, 0);

            if (charts.stagePie) {
                charts.stagePie.destroy();
                charts.stagePie = null;
            }

            resetCanvasSize(pieCanvas);

            if (!window.Chart || !pieCanvas || !pieTotal) {
                if (pieEmpty) pieEmpty.style.display = 'block';
            } else {
                if (pieEmpty) pieEmpty.style.display = 'none';

                charts.stagePie = new Chart(pieCanvas.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            data: pieData,
                            backgroundColor: ['#4e79a7', '#f28e2b', '#59a14f', '#76b7b2', '#9c755f', '#e15759', '#edc949'],
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.label}: ${ctx.parsed}`,
                                },
                            },
                        },
                    },
                });
            }

            const barCanvas = document.getElementById('topProductsBarChart');
            const barEmpty = document.getElementById('barEmpty');
            const barLabels = topProducts.map(([name]) => name);
            const barData = topProducts.map(([, count]) => count);
            const barTotal = barData.reduce((a, b) => a + b, 0);

            if (charts.productBar) {
                charts.productBar.destroy();
                charts.productBar = null;
            }

            resetCanvasSize(barCanvas);

            if (!window.Chart || !barCanvas || !barTotal) {
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
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                            },
                        },
                        plugins: { legend: { display: false } },
                    },
                });
            }

            const summary = document.getElementById('analyticsSummary');
            if (summary) {
                const customers = Array.isArray(items) ? items.length : 0;
                summary.textContent = `Auswahl: ${customers} Kunden · ${pieTotal} Produkt-Status-Vorkommen · ${totalProducts} unterschiedliche Produkte`;
            }
        }

        function updateKpisFromResults(data) {
            const formatter = new Intl.NumberFormat('de-DE');
            const setValue = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = formatter.format(value || 0);
            };

            const customers = Array.isArray(data) ? data.length : 0;
            let offers = 0;
            let deals = 0;
            let projects = 0;

            const seenOffers = new Set();
            const seenDeals = new Set();
            const seenProjects = new Set();
            const uniqueProducts = new Set();

            (data || []).forEach(item => {
                const customerId = item.customer_id;
                let hasOffer = false;
                let hasDeal = false;
                let hasProject = false;

                parseProductStatuses(item.product_statuses).forEach(({ name, stage }) => {
                    if (name) uniqueProducts.add(name);

                    const stageKey = stage.toLowerCase();

                    if (stageKey.includes('offer')) hasOffer = true;
                    if (stageKey.includes('deal')) hasDeal = true;
                    if (stageKey.includes('project')) hasProject = true;
                });

                if (hasOffer && !seenOffers.has(customerId)) {
                    offers++;
                    seenOffers.add(customerId);
                }

                if (hasDeal && !seenDeals.has(customerId)) {
                    deals++;
                    seenDeals.add(customerId);
                }

                if (hasProject && !seenProjects.has(customerId)) {
                    projects++;
                    seenProjects.add(customerId);
                }
            });

            setValue('kpi-customers', customers);
            setValue('kpi-offers', offers);
            setValue('kpi-deals', deals);
            setValue('kpi-projects', projects);
            setValue('kpi-products', uniqueProducts.size);
        }

        window.updateKpisFromResults = updateKpisFromResults;

        function populateProductsMenuFromResults(data) {
            const menu = document.getElementById('productsMenu');
            if (!menu) return;

            const names = new Set();

            (data || []).forEach(item => {
                parseProductStatuses(item.product_statuses).forEach(({ name }) => {
                    if (name) names.add(name);
                });
            });

            const sorted = Array.from(names).sort((a, b) => a.localeCompare(b, 'de'));

            menu.innerHTML = [
                `<a class="dropdown-item" href="#" data-product="">Alle Produkte</a>`,
                `<div class="dropdown-divider"></div>`,
                ...sorted.map(name => `<a class="dropdown-item" href="#" data-product="${escapeHtml(name)}">${escapeHtml(name)}</a>`),
            ].join('');
        }

        window.populateProductsMenuFromResults = populateProductsMenuFromResults;

        function applyFilters() {
            const term = (document.getElementById('liveSearch')?.value || '').toLowerCase();

            let visibleCount = 0;
            const visibleItems = [];

            window.allMarkers.forEach(({ item, marker, card, infoWindow }) => {
                if (!item || !card) return;

                let ok = true;

                if (window.activeFilter.stage) {
                    const statuses = (item.product_statuses || '').toLowerCase();
                    ok = statuses.includes(window.activeFilter.stage);
                }

                if (ok && window.activeFilter.product) {
                    ok = parseProductStatuses(item.product_statuses).some(({ name }) => {
                        return name.toLowerCase() === window.activeFilter.product.toLowerCase();
                    });
                }

                if (ok && term) {
                    ok = card.innerText.toLowerCase().includes(term);
                }

                card.style.display = ok ? '' : 'none';

                if (marker) {
                    marker.setMap(ok ? map : null);
                }

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
            refreshReferenceIcons();
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
            ['customers', 'offers', 'deals', 'projects', 'tickets', 'products'].forEach(id => {
                document.getElementById(`kpi-${id}-card`)?.classList.remove('kpi-active');
            });

            if (!window.activeFilter.stage && !window.activeFilter.product) {
                document.getElementById('kpi-customers-card')?.classList.add('kpi-active');
            }

            if (window.activeFilter.stage) {
                const stageMap = {
                    offer: 'offers',
                    deal: 'deals',
                    project: 'projects',
                };

                const key = stageMap[window.activeFilter.stage];
                if (key) {
                    document.getElementById(`kpi-${key}-card`)?.classList.add('kpi-active');
                }
            }

            if (window.activeFilter.product) {
                document.getElementById('kpi-products-card')?.classList.add('kpi-active');
            }
        }

        window.updateActiveKpiUi = updateActiveKpiUi;

        function parseProductStatuses(raw) {
            if (!raw) return [];

            return String(raw)
                .split(',')
                .map(item => item.trim())
                .filter(Boolean)
                .map(token => {
                    const match = token.match(/(.+?)\s*\((.+?)\)/);

                    return {
                        name: (match?.[1] || token).trim(),
                        stage: (match?.[2] || '').trim(),
                    };
                });
        }

        function buildProductsBadges(raw) {
            const tokens = parseProductStatuses(raw);

            if (!tokens.length) {
                return '<span class="badge badge-secondary">Keine</span>';
            }

            return tokens.map(({ name, stage }) => {
                return `<span class="badge badge-info mr-50 mb-25">${escapeHtml(name)} (${escapeHtml(translateStatusToGerman(stage))})</span>`;
            }).join('');
        }

        function translateStatusToGerman(status) {
            const value = String(status || '').toLowerCase();

            if (value.includes('lead')) return 'Anfrage';
            if (value.includes('offer')) return 'Angebot';
            if (value.includes('deal')) return 'Abschluss';
            if (value.includes('project')) return 'Projekt';
            if (value.includes('completed')) return 'Abgeschlossen';
            if (value.includes('open')) return 'Offen';
            if (value.includes('cancel')) return 'Absage';
            if (value.includes('accept')) return 'Offen';
            if (value.includes('archive')) return 'Archiv';
            if (value.includes('reject') || value.includes('absage')) return 'Abgelehnt';

            return status || '';
        }

        function getMarkerIcon(statuses) {
            const value = String(statuses || '').toLowerCase();

            if (value.includes('lead')) return '/images/pins/blue.png';
            if (value.includes('offer')) return '/images/pins/orange.png';
            if (value.includes('deal')) return '/images/pins/green.png';
            if (value.includes('project')) return '/images/pins/teal.png';
            if (value.includes('completed')) return '/images/pins/gray.png';
            if (value.includes('reject') || value.includes('absage')) return '/images/pins/red.png';

            return '/images/pins/map.png';
        }

        function haversineDistance(lat1, lon1, lat2, lon2) {
            const radius = 6371;
            const dLat = toRad(parseFloat(lat2) - parseFloat(lat1));
            const dLon = toRad(parseFloat(lon2) - parseFloat(lon1));

            const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(toRad(parseFloat(lat1))) * Math.cos(toRad(parseFloat(lat2))) *
                Math.sin(dLon / 2) ** 2;

            return radius * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
        }

        function toRad(value) {
            return parseFloat(value) * Math.PI / 180;
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('liveSearch')?.addEventListener('input', applyFilters);

            ['offers', 'deals', 'projects'].forEach(key => {
                document.getElementById(`kpi-${key}-card`)?.addEventListener('click', function () {
                    setStageFilter(key.slice(0, -1));
                });
            });

            document.getElementById('kpi-customers-card')?.addEventListener('click', function () {
                window.activeFilter = { stage: null, product: null };
                updateActiveKpiUi();
                applyFilters();
            });

            document.getElementById('kpi-tickets-card')?.addEventListener('click', function () {
                window.location.href = '/tickets';
            });

            document.getElementById('productsMenu')?.addEventListener('click', function (event) {
                const link = event.target.closest('a[data-product]');
                if (!link) return;

                event.preventDefault();
                setProductFilter(link.getAttribute('data-product') || null);
            });

            document.getElementById('address')?.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchNearby();
                }
            });

            updateActiveKpiUi();
            refreshReferenceIcons();
        });
    </script>

    <script>
        window.gm_authFailure = function () {
            const mapBox = document.getElementById('map');
            if (mapBox) {
                mapBox.innerHTML = '<div style="padding:24px;color:#ef4444;font-weight:800;background:#fff;height:100%;display:flex;align-items:center;justify-content:center;text-align:center;">Google Maps API key failed. Check GOOGLE_MAPS_KEY / restrictions / billing.</div>';
            }
            console.error('Google Maps authentication failed. Check GOOGLE_MAPS_KEY, API restrictions, billing, and referrer restrictions.');
        };
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places,marker,drawing&callback=initMap&loading=async&solution_channel=GMP_QB_addressselection_v2_cAB"
        async defer></script>
@endsection