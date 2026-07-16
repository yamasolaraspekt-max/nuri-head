@extends('admin.layouts.app')

@section('title', 'Krankheit & Urlaub Analyse')

@php
    $monthOptions = [
        '' => 'Ganzes Jahr',
        '1' => 'Januar',
        '2' => 'Februar',
        '3' => 'März',
        '4' => 'April',
        '5' => 'Mai',
        '6' => 'Juni',
        '7' => 'Juli',
        '8' => 'August',
        '9' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Dezember',
    ];

    $chartLabels = $rows->pluck('name')->values();
    $chartHoliday = $rows->pluck('holiday_days')->values();
    $chartSick = $rows->pluck('sick_days')->values();
    $chartEfficiency = $rows->pluck('efficiency')->values();

    $safeMinYear = $minYear ?? now()->year;
    $safeMaxYear = $maxYear ?? now()->year + 1;

    $routeAnalyser = Route::has('employee.sickness-holiday-analyser')
        ? route('employee.sickness-holiday-analyser')
        : url('/employee/sickness-holiday-analyser');
@endphp

@push('style')
    <style>
        :root {
            --app-bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --primary: var(--sa-accent);
            --primary-hover: var(--sa-accent-hover);
            --primary-light: var(--sa-accent-light);
            --blue: #74b2d4;
            --blue-light: #eff6ff;
            --success: #10b981;
            --success-light: #ecfdf5;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --gray-light: #f3f4f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --radius: 16px;
            --transition: all .2s ease-in-out;
        }

        .oc-wrap {
            font-family: Inter, system-ui, -apple-system, sans-serif;
            color: var(--text-main);
        }

        .oc-titlebar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .oc-title {
            font-size: 26px;
            font-weight: 900;
            color: #111827;
            letter-spacing: -.025em;
        }

        .oc-sub {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .oc-breadcrumb {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 10px;
            font-size: 13px;
            color: var(--text-muted);
            flex-wrap: wrap;
        }

        .oc-breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 800;
        }

        .oc-breadcrumb a:hover {
            color: #111827;
        }

        .oc-breadcrumb .current {
            color: #111827;
            font-weight: 900;
        }

        .oc-inline-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .oc-btn,
        .oc-btn-soft {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
        }

        .oc-btn {
            background: var(--primary);
            color: #fff;
        }

        .oc-btn:hover {
            background: var(--primary-hover);
            color: #fff;
            text-decoration: none;
        }

        .oc-btn-soft {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .oc-btn-soft:hover {
            background: #f9fafb;
            color: var(--text-main);
            text-decoration: none;
        }

        .oc-analytics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        @media(max-width:1200px) {
            .oc-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:700px) {
            .oc-analytics {
                grid-template-columns: 1fr;
            }
        }

        .oc-stat {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            box-shadow: var(--shadow-sm);
            display: flex;
            gap: 12px;
            align-items: center;
            min-height: 94px;
            transition: var(--transition);
        }

        .oc-stat:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            border-color: rgba(147, 194, 28, .35);
        }

        .oc-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .oc-stat-icon.total {
            background: var(--blue-light);
            color: var(--blue);
        }

        .oc-stat-icon.success {
            background: var(--success-light);
            color: var(--success);
        }

        .oc-stat-icon.warning {
            background: var(--warning-light);
            color: #b45309;
        }

        .oc-stat-icon.danger {
            background: var(--danger-light);
            color: var(--danger);
        }

        .oc-stat-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .oc-stat-value {
            font-size: 25px;
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
            margin-top: 4px;
        }

        .oc-stat-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .oc-toolbar {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
        }

        .oc-toolbar-left,
        .oc-toolbar-right {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .oc-toolbar-left {
            flex: 1;
        }

        .oc-filter-block {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 170px;
        }

        .oc-filter-block.search {
            min-width: 260px;
            flex: 1;
        }

        .oc-filter-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .oc-input-form,
        .oc-select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: #fff;
            outline: none;
            transition: var(--transition);
            font-size: 14px;
        }

        .oc-input-form:focus,
        .oc-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .oc-grid-2 {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 16px;
            margin-bottom: 0;
        }

        @media(max-width:1100px) {
            .oc-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .oc-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .oc-card-pad {
            padding: 16px;
        }

        .oc-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .oc-chart-title {
            font-weight: 900;
            font-size: 15px;
            color: #111827;
        }

        .oc-muted {
            color: var(--text-muted);
            font-size: 12px;
        }

        .oc-tabs {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .oc-tabs-nav {
            display: flex;
            gap: 8px;
            padding: 12px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
            flex-wrap: wrap;
        }

        .oc-tab-btn {
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text-muted);
            border-radius: 10px;
            padding: 9px 13px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .oc-tab-btn:hover {
            background: var(--primary-light);
            border-color: rgba(147, 194, 28, .35);
            color: var(--primary-hover);
        }

        .oc-tab-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .oc-tab-panel {
            display: none;
            padding: 16px;
        }

        .oc-tab-panel.active {
            display: block;
        }

        .oc-list-head {
            display: grid;
            grid-template-columns: minmax(250px, 1.5fr) 150px 140px 130px 130px 150px 120px;
            gap: 14px;
            padding: 16px 16px 10px;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        @media(max-width:1280px) {
            .oc-list-head {
                display: none;
            }
        }

        .oc-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-bottom: 16px;
        }

        .oc-item {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            margin: 0 16px;
            overflow: hidden;
            transition: var(--transition);
        }

        .oc-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .oc-item-row {
            display: grid;
            grid-template-columns: minmax(250px, 1.5fr) 150px 140px 130px 130px 150px 120px;
            gap: 14px;
            align-items: center;
            padding: 8px;
        }

        @media(max-width:1280px) {
            .oc-item-row {
                grid-template-columns: 1fr;
            }
        }

        .oc-item-row>div {
            min-height: 82px;
            border-radius: 14px;
            padding: 10px;
            transition:
                background-color .18s ease,
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease;
        }

        .oc-item-row>div:hover {
            background: linear-gradient(135deg, rgba(147, 194, 28, .10), rgba(116, 178, 212, .10));
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -16px rgba(17, 24, 39, .35);
        }

        .oc-item-row>div:hover .oc-number,
        .oc-item-row>div:hover .emp-name {
            color: var(--primary-hover);
        }

        .oc-item-row>div:hover .oc-pill {
            transform: scale(1.03);
        }

        .oc-cell-title {
            display: none;
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        @media(max-width:1280px) {
            .oc-cell-title {
                display: block;
            }
        }

        .emp-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            text-decoration: none;
            color: inherit;
            border-radius: 14px;
            padding: 4px;
            transition: var(--transition);
        }

        .emp-link:hover {
            background: rgba(147, 194, 28, .10);
            color: #111827;
            text-decoration: none;
        }

        .emp-photo {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            object-fit: cover;
            border: 1px solid var(--border);
            background: #f9fafb;
            flex: 0 0 auto;
        }

        .emp-initials {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--primary-light);
            color: var(--primary-hover);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 16px;
            flex: 0 0 auto;
        }

        .emp-name {
            font-weight: 900;
            color: #111827;
            font-size: 15px;
            transition: var(--transition);
        }

        .emp-meta {
            color: var(--text-muted);
            font-size: 12px;
            margin-top: 3px;
        }

        .oc-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .oc-pill.green {
            background: var(--success-light);
            color: #047857;
        }

        .oc-pill.orange {
            background: var(--warning-light);
            color: #b45309;
        }

        .oc-pill.red {
            background: var(--danger-light);
            color: #b91c1c;
        }

        .oc-pill.blue {
            background: var(--blue-light);
            color: #2563eb;
        }

        .oc-pill.gray {
            background: #f3f4f6;
            color: #4b5563;
        }

        .oc-progress {
            height: 9px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
            margin-top: 8px;
        }

        .oc-progress span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: var(--primary);
        }

        .oc-progress.danger span {
            background: var(--danger);
        }

        .oc-number {
            font-weight: 900;
            color: #111827;
            transition: var(--transition);
        }

        .oc-btn-ic {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .oc-btn-ic:hover {
            background: #f9fafb;
            color: #111827;
            transform: translateY(-1px);
        }

        .oc-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1200;
            background: rgba(17, 24, 39, .55);
            backdrop-filter: blur(3px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .22s ease;
            padding: 18px;
        }

        .oc-modal-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }

        .oc-modal {
            width: 100%;
            max-width: 960px;
            background: #fff;
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
            transform: translateY(12px) scale(.985);
            transition: transform .22s ease;
        }

        .oc-modal-backdrop.open .oc-modal {
            transform: translateY(0) scale(1);
        }

        .oc-modal-h {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: #fafafa;
            border-bottom: 1px solid var(--border);
        }

        .oc-modal-ttl {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111827;
        }

        .oc-modal-b {
            padding: 18px;
            max-height: 72vh;
            overflow-y: auto;
        }

        .oc-modal-f {
            padding: 14px 18px;
            border-top: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .oc-detail-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        @media(max-width:900px) {
            .oc-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:600px) {
            .oc-detail-grid {
                grid-template-columns: 1fr;
            }
        }

        .oc-detail-box {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px;
            background: #fff;
        }

        .oc-detail-label {
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .oc-detail-value {
            font-weight: 900;
            color: #111827;
            margin-top: 4px;
        }

        .oc-table-wrap {
            width: 100%;
            overflow: auto;
        }

        .oc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .oc-table th {
            text-align: left;
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 10px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .oc-table td {
            padding: 10px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .oc-table tbody tr {
            transition: var(--transition);
        }

        .oc-table tbody tr:hover {
            background: #f9fafb;
        }

        .oc-empty {
            text-align: center;
            padding: 46px;
            color: var(--text-muted);
            background: #fff;
            border: 1px dashed var(--border);
            border-radius: 16px;
            margin: 16px;
        }

        .oc-toast-wrap {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .oc-toast {
            pointer-events: auto;
            min-width: 280px;
            max-width: 360px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 12px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            animation: ocToastIn .3s ease forwards;
        }

        @keyframes ocToastIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .oc-toast-ic {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .oc-toast-ic.ok {
            background: var(--success-light);
            color: var(--success);
        }

        .oc-toast-ic.bad {
            background: var(--danger-light);
            color: var(--danger);
        }

        .oc-toast-ttl {
            font-weight: 900;
            font-size: 13px;
            margin: 0;
            color: #111827;
        }

        .oc-toast-msg {
            font-size: 12px;
            color: #374151;
            margin: 4px 0 0;
            line-height: 1.4;
        }

        .oc-toast-x {
            margin-left: auto;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
        }

        @media(max-width:600px) {
            .oc-title {
                font-size: 22px;
            }

            .oc-toolbar,
            .oc-toolbar-left,
            .oc-toolbar-right {
                align-items: stretch;
                width: 100%;
            }

            .oc-filter-block {
                min-width: 100%;
            }

            .oc-btn,
            .oc-btn-soft {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="oc-wrap">
        <div class="oc-titlebar">
            <div>
                <div class="oc-title">Krankheit & Urlaub Analyse</div>

                <div class="oc-sub">
                    Analyse von Urlaub, Krankheit, Arbeitstagen und Effizienz ohne Wochenenden und Feiertage.
                    Zeitraum: {{ $periodStart->format('d.m.Y') }} – {{ $periodEnd->format('d.m.Y') }}
                </div>

                <div class="oc-breadcrumb">
                    <a href="{{ url('/employee_dashboard') }}">Home</a>
                    <span>›</span>
                    <a href="{{ url('/emp_info') }}">Mitarbeiter</a>
                    <span>›</span>
                    <span class="current">Krankheit & Urlaub Analyse</span>
                </div>
            </div>

            <div class="oc-inline-actions">
                <a href="{{ url('/emp_info') }}" class="oc-btn-soft">
                    Mitarbeiterübersicht
                </a>

                <a href="{{ $routeAnalyser }}" class="oc-btn-soft">
                    Zurücksetzen
                </a>
            </div>
        </div>

        <form action="{{ $routeAnalyser }}" method="GET" class="oc-toolbar">
            <div class="oc-toolbar-left">
                <div class="oc-filter-block">
                    <label class="oc-filter-label">Jahr</label>
                    <select name="year" class="oc-select">
                        @for($y = $safeMaxYear; $y >= $safeMinYear; $y--)
                            <option value="{{ $y }}" @selected((int) $year === (int) $y)>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Monat</label>
                    <select name="month" class="oc-select">
                        @foreach($monthOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('month') === (string) $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Filiale</label>
                    <select name="branch" class="oc-select">
                        <option value="">Alle Filialen</option>

                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) request('branch') === (string) $branch->id)>
                                {{ $branch->branch }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Abteilung</label>
                    <select name="department" class="oc-select">
                        <option value="">Alle Abteilungen</option>

                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected((string) request('department') === (string) $department->id)>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block search">
                    <label class="oc-filter-label">Suche</label>
                    <input type="text" name="search" class="oc-input-form" value="{{ request('search') }}"
                        placeholder="Name, E-Mail oder Telefon">
                </div>
            </div>

            <div class="oc-toolbar-right">
                <button type="submit" class="oc-btn">
                    Analysieren
                </button>
            </div>
        </form>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>

                <div>
                    <div class="oc-stat-label">Mitarbeiter</div>
                    <div class="oc-stat-value">{{ $summary['employees'] ?? 0 }}</div>
                    <div class="oc-stat-sub">{{ $summary['working_days'] ?? 0 }} Arbeitstage im Zeitraum</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon success">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </div>

                <div>
                    <div class="oc-stat-label">Ø Effizienz</div>
                    <div class="oc-stat-value">{{ $summary['avg_efficiency'] ?? 0 }}%</div>
                    <div class="oc-stat-sub">{{ $summary['worked_hours'] ?? 0 }} / {{ $summary['target_hours'] ?? 0 }} Std.
                    </div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon warning">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 2v4M16 2v4M3 10h18" />
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                    </svg>
                </div>

                <div>
                    <div class="oc-stat-label">Urlaub</div>
                    <div class="oc-stat-value">{{ $summary['holiday_days'] ?? 0 }}</div>
                    <div class="oc-stat-sub">Genommene Urlaubstage</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon danger">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M2 12h20" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                </div>

                <div>
                    <div class="oc-stat-label">Krankheit</div>
                    <div class="oc-stat-value">{{ $summary['sick_days'] ?? 0 }}</div>
                    <div class="oc-stat-sub">{{ $summary['avg_absence_rate'] ?? 0 }}% Ø Abwesenheit</div>
                </div>
            </div>
        </div>

        <div class="oc-tabs">
            <div class="oc-tabs-nav">
                <button type="button" class="oc-tab-btn active" data-tab-target="analyticsTab">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18" />
                        <path d="M7 14l3-3 4 4 5-8" />
                    </svg>
                    Analyse & Diagramme
                </button>

                <button type="button" class="oc-tab-btn" data-tab-target="publicHolidayTab">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    Feiertage im Zeitraum
                    <span class="oc-pill blue" style="padding:3px 8px;">
                        {{ $summary['public_holiday_count'] ?? 0 }}
                    </span>
                </button>
            </div>

            <div class="oc-tab-panel active" id="analyticsTab">
                <div class="oc-grid-2">
                    <div class="oc-card" style="margin-bottom:0;">
                        <div class="oc-card-pad">
                            <div class="oc-card-head">
                                <div>
                                    <div class="oc-chart-title">Urlaub vs. Krankheit pro Mitarbeiter</div>
                                    <div class="oc-muted">Vergleich von Urlaub, Krankheit und Effizienz.</div>
                                </div>

                                <span class="oc-pill blue">Effizienz inklusive</span>
                            </div>

                            <canvas id="absenceBarChart" height="110"></canvas>
                        </div>
                    </div>

                    <div class="oc-card" style="margin-bottom:0;">
                        <div class="oc-card-pad">
                            <div class="oc-card-head">
                                <div>
                                    <div class="oc-chart-title">Gesamtverteilung</div>
                                    <div class="oc-muted">Arbeitsstunden, Urlaub und Krankheit.</div>
                                </div>

                                <span class="oc-pill gray">{{ $summary['public_holiday_count'] ?? 0 }} Feiertage</span>
                            </div>

                            <canvas id="summaryPieChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="oc-tab-panel" id="publicHolidayTab">
                <div class="oc-card-head">
                    <div>
                        <div class="oc-chart-title">Feiertage im Zeitraum</div>
                        <div class="oc-muted">
                            Diese Tage werden automatisch aus den Arbeitstagen herausgerechnet.
                        </div>
                    </div>

                    <span class="oc-pill blue">{{ $summary['public_holiday_count'] ?? 0 }} Tage</span>
                </div>

                @if(isset($publicHolidayRecords) && $publicHolidayRecords->isNotEmpty())
                    <div class="oc-table-wrap">
                        <table class="oc-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Zeitraum</th>
                                    <th>Ort</th>
                                    <th>Kommentar</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($publicHolidayRecords as $holiday)
                                    <tr>
                                        <td>
                                            <strong>{{ $holiday->name }}</strong>
                                        </td>

                                        <td>
                                            {{ optional($holiday->start_date)->format('d.m.Y') }}
                                            —
                                            {{ optional($holiday->end_date ?: $holiday->start_date)->format('d.m.Y') }}
                                        </td>

                                        <td>
                                            {{ collect([$holiday->city, $holiday->state, $holiday->country])->filter()->implode(', ') ?: 'Allgemein' }}
                                        </td>

                                        <td>
                                            {{ $holiday->comment ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="oc-empty" style="margin:0;">
                        Keine Feiertage für diesen Zeitraum gefunden.
                    </div>
                @endif
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-list-head">
                <div>Mitarbeiter</div>
                <div>Abteilung</div>
                <div>Urlaub</div>
                <div>Krank</div>
                <div>Abwesenheit</div>
                <div>Effizienz</div>
                <div style="text-align:right;">Details</div>
            </div>

            @if($rows->isEmpty())
                <div class="oc-empty">Keine Mitarbeiter für diese Filter gefunden.</div>
            @else
                <div class="oc-list">
                    @foreach($rows as $row)
                        @php
                            $employeeUrl = url('next_employee/' . $row['id']);

                            $initials = collect(explode(' ', $row['name']))
                                ->filter()
                                ->map(fn($part) => mb_substr($part, 0, 1))
                                ->join('');

                            $effClass = $row['efficiency'] >= 90
                                ? 'green'
                                : ($row['efficiency'] >= 75 ? 'orange' : 'red');
                        @endphp

                        <div class="oc-item">
                            <div class="oc-item-row">
                                <div>
                                    <div class="oc-cell-title">Mitarbeiter</div>

                                    <a href="{{ $employeeUrl }}" class="emp-link" title="Mitarbeiterprofil öffnen">
                                        @if($row['image'])
                                            <img src="{{ $row['image'] }}" class="emp-photo" alt="{{ $row['name'] }}">
                                        @else
                                            <div class="emp-initials">{{ $initials ?: 'MA' }}</div>
                                        @endif

                                        <div>
                                            <div class="emp-name">{{ $row['name'] }}</div>
                                            <div class="emp-meta">{{ $row['position'] ?: 'Keine Position' }}</div>
                                            <div class="emp-meta">{{ $row['weekly_hours'] }} Std./Woche</div>
                                        </div>
                                    </a>
                                </div>

                                <div>
                                    <div class="oc-cell-title">Abteilung</div>
                                    <div class="oc-number">{{ $row['department'] ?: '—' }}</div>
                                    <div class="oc-muted">{{ $row['daily_hours'] }} Std./Tag</div>
                                </div>

                                <div>
                                    <div class="oc-cell-title">Urlaub</div>
                                    <span class="oc-pill orange">{{ $row['holiday_days'] }} Tage</span>
                                    <div class="oc-muted">{{ $row['holiday_hours'] }} Std.</div>
                                </div>

                                <div>
                                    <div class="oc-cell-title">Krank</div>
                                    <span class="oc-pill red">{{ $row['sick_days'] }} Tage</span>
                                    <div class="oc-muted">{{ $row['sick_hours'] }} Std.</div>
                                </div>

                                <div>
                                    <div class="oc-cell-title">Abwesenheit</div>
                                    <div class="oc-number">{{ $row['absence_rate'] }}%</div>

                                    <div class="oc-progress danger">
                                        <span style="width:{{ min(100, $row['absence_rate']) }}%"></span>
                                    </div>
                                </div>

                                <div>
                                    <div class="oc-cell-title">Effizienz</div>
                                    <span class="oc-pill {{ $effClass }}">{{ $row['efficiency'] }}%</span>

                                    <div class="oc-progress">
                                        <span style="width:{{ min(100, $row['efficiency']) }}%"></span>
                                    </div>
                                </div>

                                <div style="text-align:right;">
                                    <div class="oc-cell-title">Details</div>

                                    <button type="button" class="oc-btn-ic js-open-employee-modal"
                                        data-modal="employeeModal{{ $row['id'] }}" title="Analyse Details">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 16v-4M12 8h.01" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="oc-modal-backdrop" id="employeeModal{{ $row['id'] }}">
                            <div class="oc-modal">
                                <div class="oc-modal-h">
                                    <h3 class="oc-modal-ttl">{{ $row['name'] }} — Analyse Details</h3>

                                    <button type="button" class="oc-btn-ic" onclick="closeModal('employeeModal{{ $row['id'] }}')">
                                        ×
                                    </button>
                                </div>

                                <div class="oc-modal-b">
                                    <div class="oc-detail-grid">
                                        <div class="oc-detail-box">
                                            <div class="oc-detail-label">Soll-Arbeitstage</div>
                                            <div class="oc-detail-value">{{ $row['target_working_days'] }}</div>
                                        </div>

                                        <div class="oc-detail-box">
                                            <div class="oc-detail-label">Geleistete Tage</div>
                                            <div class="oc-detail-value">{{ $row['worked_days'] }}</div>
                                        </div>

                                        <div class="oc-detail-box">
                                            <div class="oc-detail-label">Soll-Stunden</div>
                                            <div class="oc-detail-value">{{ $row['target_hours'] }}</div>
                                        </div>

                                        <div class="oc-detail-box">
                                            <div class="oc-detail-label">Arbeitsstunden nach Abwesenheit</div>
                                            <div class="oc-detail-value">{{ $row['worked_hours'] }}</div>
                                        </div>
                                    </div>

                                    <div
                                        style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; flex-wrap:wrap;">
                                        <div>
                                            <div class="oc-chart-title">Urlaubseinträge</div>
                                            <div class="oc-muted">Nur Einträge im ausgewählten Zeitraum.</div>
                                        </div>

                                        <a href="{{ $employeeUrl }}" class="oc-btn-soft">
                                            Mitarbeiter öffnen
                                        </a>
                                    </div>

                                    <div class="oc-table-wrap">
                                        <table class="oc-table">
                                            <thead>
                                                <tr>
                                                    <th>Typ</th>
                                                    <th>Zeitraum</th>
                                                    <th>Tage</th>
                                                    <th>Status</th>
                                                    <th>Erstellt von</th>
                                                    <th>Akzeptiert von</th>
                                                    <th>Grund</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse($row['leave_details'] as $leave)
                                                    <tr>
                                                        <td>{{ $leave['type'] }}</td>
                                                        <td>{{ $leave['start'] }} – {{ $leave['end'] }}</td>
                                                        <td>{{ $leave['days'] }}</td>
                                                        <td>{{ $leave['approved'] ?: '—' }}</td>
                                                        <td>{{ trim($leave['created_by']) ?: '—' }}</td>
                                                        <td>{{ trim($leave['accepted_by']) ?: '—' }}</td>
                                                        <td>{{ $leave['reason'] ?: '—' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">Keine Urlaubseinträge im Zeitraum.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="oc-chart-title" style="margin-top:22px;">Krankheitseinträge</div>
                                    <div class="oc-muted" style="margin-bottom:12px;">Nur Einträge im ausgewählten Zeitraum.</div>

                                    <div class="oc-table-wrap">
                                        <table class="oc-table">
                                            <thead>
                                                <tr>
                                                    <th>Typ</th>
                                                    <th>Zeitraum</th>
                                                    <th>Tage</th>
                                                    <th>Status</th>
                                                    <th>Erstellt von</th>
                                                    <th>Nachricht</th>
                                                    <th>Dokumente</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse($row['sick_details'] as $sick)
                                                    <tr>
                                                        <td>{{ $sick['type'] }}</td>
                                                        <td>{{ $sick['start'] }} – {{ $sick['end'] }}</td>
                                                        <td>{{ $sick['days'] }}</td>
                                                        <td>{{ $sick['status'] ?: '—' }}</td>
                                                        <td>{{ trim($sick['created_by']) ?: '—' }}</td>
                                                        <td>{{ $sick['message'] ?: '—' }}</td>
                                                        <td>
                                                            @if(!empty($sick['documents']))
                                                                @foreach($sick['documents'] as $doc)
                                                                    <a href="{{ $doc }}" target="_blank" class="oc-pill blue"
                                                                        style="margin:2px;">
                                                                        Öffnen
                                                                    </a>
                                                                @endforeach
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">Keine Krankheitseinträge im Zeitraum.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="oc-modal-f">
                                    <a href="{{ $employeeUrl }}" class="oc-btn">
                                        Mitarbeiterprofil öffnen
                                    </a>

                                    <button type="button" class="oc-btn-soft" onclick="closeModal('employeeModal{{ $row['id'] }}')">
                                        Schließen
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function openModal(id) {
            const el = document.getElementById(id);

            if (el) {
                el.classList.add('open');
            }
        }

        function closeModal(id) {
            const el = document.getElementById(id);

            if (el) {
                el.classList.remove('open');
            }
        }

        function toast(kind, title, msg) {
            const wrap = document.getElementById('toast-wrap');

            if (!wrap) {
                return;
            }

            const icons = {
                ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M20 6L9 17l-5-5"/></svg>`,
                bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M6 18L18 6M6 6l12 12"/></svg>`
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
                try {
                    el.remove();
                } catch (e) { }
            }, 4000);
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-open-employee-modal');

            if (btn) {
                openModal(btn.dataset.modal);
            }

            if (e.target.classList.contains('oc-modal-backdrop')) {
                e.target.classList.remove('open');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.oc-modal-backdrop.open').forEach(function (el) {
                    el.classList.remove('open');
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.oc-tab-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const target = btn.dataset.tabTarget;

                    document.querySelectorAll('.oc-tab-btn').forEach(function (item) {
                        item.classList.remove('active');
                    });

                    document.querySelectorAll('.oc-tab-panel').forEach(function (panel) {
                        panel.classList.remove('active');
                    });

                    btn.classList.add('active');

                    const panel = document.getElementById(target);

                    if (panel) {
                        panel.classList.add('active');
                    }
                });
            });

            toast('ok', 'Analyse geladen', 'Die Mitarbeiterdaten wurden erfolgreich berechnet.');

            const labels = @json($chartLabels);
            const holidayData = @json($chartHoliday);
            const sickData = @json($chartSick);
            const efficiencyData = @json($chartEfficiency);

            const barCanvas = document.getElementById('absenceBarChart');

            if (barCanvas) {
                new Chart(barCanvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Urlaubstage',
                                data: holidayData
                            },
                            {
                                label: 'Krankheitstage',
                                data: sickData
                            },
                            {
                                label: 'Effizienz %',
                                data: efficiencyData,
                                type: 'line',
                                yAxisID: 'percentage'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Tage'
                                }
                            },
                            percentage: {
                                beginAtZero: true,
                                max: 100,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Effizienz %'
                                }
                            }
                        }
                    }
                });
            }

            const pieCanvas = document.getElementById('summaryPieChart');

            if (pieCanvas) {
                new Chart(pieCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Arbeitsstunden', 'Urlaub Stunden', 'Krank Stunden'],
                        datasets: [{
                            data: [
                            {{ (float) ($summary['worked_hours'] ?? 0) }},
                            {{ (float) (($summary['holiday_days'] ?? 0) * 8) }},
                                {{ (float) (($summary['sick_days'] ?? 0) * 8) }}
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush