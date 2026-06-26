@extends('admin.layouts.app')

@section('title')
    Tagesbericht - Mitarbeiter-Zeiten & Aktivitäten
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        :root {
            --dr-bg: #f3f4f6;
            --dr-card: #ffffff;
            --dr-text: #111827;
            --dr-muted: #6b7280;
            --dr-border: #e5e7eb;
            --dr-green: #93c21c;
            --dr-green-dark: #7baa18;
            --dr-green-soft: #f4fae7;
            --dr-blue: #74b2d4;
            --dr-blue-soft: #eff6ff;
            --dr-danger: #ef4444;
            --dr-danger-soft: #fef2f2;
            --dr-warning: #f59e0b;
            --dr-warning-soft: #fffbeb;
            --dr-success: #10b981;
            --dr-success-soft: #ecfdf5;
            --dr-radius: 16px;
            --dr-shadow-sm: 0 1px 2px rgba(15, 23, 42, .05);
            --dr-shadow: 0 18px 45px -25px rgba(15, 23, 42, .35), 0 6px 14px -8px rgba(15, 23, 42, .18);
            --dr-transition: all .2s ease;
        }

        .daily-dashboard {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--dr-text);
        }

        .dr-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 52%, #eef9ff 100%);
            border: 1px solid rgba(229, 231, 235, .95);
            border-radius: 22px;
            padding: 22px;
            box-shadow: var(--dr-shadow-sm);
            margin-bottom: 16px;
        }

        .dr-hero::before {
            content: "";
            position: absolute;
            top: -80px;
            right: -80px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: rgba(147, 194, 28, .16);
            pointer-events: none;
        }

        .dr-hero-row {
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .dr-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3f6212;
            background: var(--dr-green-soft);
            border: 1px solid rgba(147, 194, 28, .35);
            border-radius: 999px;
            padding: 6px 11px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 10px;
        }

        .dr-title {
            font-size: 30px;
            line-height: 1.05;
            font-weight: 900;
            letter-spacing: -.035em;
            margin: 0;
            color: #0f172a;
        }

        .dr-subtitle {
            margin: 7px 0 0 0;
            color: var(--dr-muted);
            font-size: 14px;
        }

        .dr-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
            font-size: 13px;
            color: var(--dr-muted);
        }

        .dr-breadcrumb a {
            color: var(--dr-muted);
            text-decoration: none;
            font-weight: 800;
        }

        .dr-breadcrumb a:hover {
            color: var(--dr-text);
        }

        .dr-breadcrumb .current {
            color: var(--dr-text);
            font-weight: 900;
        }

        .dr-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dr-btn,
        .dr-btn-soft,
        .dr-btn-danger {
            min-height: 42px;
            border-radius: 12px;
            border: 1px solid transparent;
            padding: 10px 15px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: var(--dr-transition);
            text-decoration: none;
            white-space: nowrap;
        }

        .dr-btn {
            background: var(--dr-green);
            color: #fff;
            box-shadow: 0 10px 20px -12px rgba(147, 194, 28, .95);
        }

        .dr-btn:hover {
            background: var(--dr-green-dark);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .dr-btn-soft {
            background: #fff;
            color: var(--dr-text);
            border-color: var(--dr-border);
        }

        .dr-btn-soft:hover {
            background: #f9fafb;
            color: var(--dr-text);
            text-decoration: none;
        }

        .dr-btn-danger {
            background: var(--dr-warning-soft);
            color: #b45309;
            border-color: rgba(245, 158, 11, .25);
        }

        .dr-btn-danger:hover {
            border-color: rgba(245, 158, 11, .55);
            color: #92400e;
            text-decoration: none;
        }

        .dr-analytics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .dr-stat {
            background: var(--dr-card);
            border: 1px solid var(--dr-border);
            border-radius: var(--dr-radius);
            padding: 16px;
            box-shadow: var(--dr-shadow-sm);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 94px;
        }

        .dr-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .dr-stat-icon.total {
            background: var(--dr-blue-soft);
            color: var(--dr-blue);
        }

        .dr-stat-icon.done {
            background: var(--dr-success-soft);
            color: var(--dr-success);
        }

        .dr-stat-icon.open {
            background: var(--dr-warning-soft);
            color: var(--dr-warning);
        }

        .dr-stat-icon.absent {
            background: var(--dr-danger-soft);
            color: var(--dr-danger);
        }

        .dr-stat-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--dr-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dr-stat-value {
            font-size: 25px;
            font-weight: 950;
            line-height: 1.1;
            margin-top: 4px;
            color: #0f172a;
        }

        .dr-stat-sub {
            font-size: 12px;
            color: var(--dr-muted);
            margin-top: 3px;
        }

        .dr-toolbar {
            background: var(--dr-card);
            border: 1px solid var(--dr-border);
            border-radius: var(--dr-radius);
            padding: 14px 16px;
            box-shadow: var(--dr-shadow-sm);
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .dr-toolbar-left,
        .dr-toolbar-right {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dr-toolbar-left {
            flex: 1;
        }

        .dr-filter-block {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 180px;
        }

        .dr-filter-block.search {
            flex: 1;
            min-width: 280px;
        }

        .dr-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--dr-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        #datePicker,
        .dr-input {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--dr-border);
            background: #f9fafb;
            border-radius: 12px;
            outline: none;
            padding: 10px 12px;
            font-size: 14px;
            transition: var(--dr-transition);
            color: var(--dr-text);
        }

        #datePicker:focus,
        .dr-input:focus {
            background: #fff;
            border-color: var(--dr-green);
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .14);
        }

        .dr-search-wrap {
            position: relative;
        }

        .dr-search-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .dr-search-wrap .dr-input {
            padding-left: 38px;
        }

        .dr-period-btn {
            min-width: 180px;
            min-height: 42px;
            border-radius: 12px !important;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fff;
            border: 1px solid var(--dr-border);
            color: var(--dr-text);
            font-weight: 900;
            padding: 10px 12px;
        }

        .dropdown-menu.filter {
            border: 1px solid var(--dr-border);
            border-radius: 14px;
            box-shadow: var(--dr-shadow);
            padding: 8px;
        }

        .dropdown-menu.filter .dropdown-item {
            border-radius: 10px;
            font-weight: 800;
            font-size: 13px;
            padding: 9px 10px;
        }

        .dropdown-menu.filter .dropdown-item.active,
        .dropdown-menu.filter .dropdown-item:active {
            background: var(--dr-green-soft);
            color: #3f6212;
        }

        .dr-panel {
            background: var(--dr-card);
            border: 1px solid var(--dr-border);
            border-radius: 18px;
            box-shadow: var(--dr-shadow-sm);
            overflow: hidden;
        }

        .dr-panel-head {
            padding: 16px 18px;
            border-bottom: 1px solid var(--dr-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            background: #fff;
        }

        .dr-panel-title {
            margin: 0;
            font-size: 16px;
            font-weight: 950;
            color: #0f172a;
        }

        .dr-panel-sub {
            margin-top: 3px;
            color: var(--dr-muted);
            font-size: 12px;
        }

        .employee-grid {
            padding: 16px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            min-height: 140px;
        }

        .employee-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--dr-border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--dr-shadow-sm);
            transition: var(--dr-transition);
        }

        .employee-card:hover {
            transform: translateY(-2px);
            border-color: rgba(147, 194, 28, .65);
            box-shadow: var(--dr-shadow);
        }

        .employee-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: var(--emp-color, var(--dr-green));
        }

        .employee-card-top {
            padding: 16px 16px 12px 20px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        }

        .employee-avatar-wrap {
            position: relative;
            flex: 0 0 auto;
        }

        .employee-avatar {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            object-fit: cover;
            border: 1px solid var(--dr-border);
            background: #f8fafc;
        }

        .employee-avatar-status {
            position: absolute;
            right: -3px;
            bottom: -3px;
            width: 15px;
            height: 15px;
            border-radius: 999px;
            border: 2px solid #fff;
            background: var(--dr-green);
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .12);
        }

        .employee-title {
            min-width: 0;
            flex: 1;
        }

        .employee-name {
            font-size: 15px;
            font-weight: 950;
            color: #0f172a;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .employee-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 7px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            line-height: 1;
            border-radius: 999px;
            padding: 6px 8px;
            border: 1px solid transparent;
            max-width: 100%;
            white-space: nowrap;
        }

        .chip-soft {
            background: var(--dr-blue-soft);
            color: #2563eb;
            border-color: rgba(116, 178, 212, .24);
        }

        .chip-muted {
            background: #f9fafb;
            color: var(--dr-muted);
            border-color: var(--dr-border);
        }

        .chip-status {
            background: var(--dr-green-soft);
            color: #3f6212;
            border-color: rgba(147, 194, 28, .35);
        }

        .chip-danger {
            background: var(--dr-danger-soft);
            color: #b91c1c;
            border-color: rgba(239, 68, 68, .24);
        }

        .employee-body {
            padding: 14px 16px 12px 20px;
        }

        .work-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .work-label {
            color: var(--dr-muted);
            font-size: 12px;
            font-weight: 800;
        }

        .work-percent {
            color: #0f172a;
            font-size: 12px;
            font-weight: 950;
        }

        .progress-thin {
            height: 9px;
            border-radius: 999px;
            overflow: hidden;
            background: #e5e7eb;
        }

        .progress-thin .progress-bar {
            border-radius: 999px;
        }

        .hour-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .hour-box,
        .stat-pill {
            background: #f9fafb;
            border: 1px solid var(--dr-border);
            border-radius: 14px;
            padding: 9px 10px;
            min-width: 0;
        }

        .hour-box .label,
        .stat-pill .label {
            display: block;
            color: #9ca3af;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 4px;
        }

        .hour-box .value,
        .stat-pill .value {
            display: block;
            color: #111827;
            font-weight: 950;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .timeline {
            margin-top: 12px;
            border: 1px solid #eef2f7;
            border-radius: 15px;
            overflow: hidden;
            background: #fff;
        }

        .timeline-header {
            padding: 10px 11px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #eef2f7;
            background: #fbfdff;
        }

        .timeline-title {
            font-weight: 950;
            font-size: 12px;
            color: #111827;
        }

        .timeline-meta {
            color: var(--dr-muted);
            font-size: 11px;
            white-space: nowrap;
        }

        .timeline-list {
            max-height: 150px;
            overflow: auto;
            padding: 6px 10px;
        }

        .timeline-item {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            padding: 7px 0;
            border-bottom: 1px dashed #eef2f7;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-badge {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            margin-top: 5px;
            flex: 0 0 auto;
        }

        .timeline-badge-task {
            background: var(--dr-blue);
        }

        .timeline-badge-appointment {
            background: var(--dr-green);
        }

        .timeline-badge-problem {
            background: var(--dr-danger);
        }

        .timeline-badge-offer {
            background: #3b82f6;
        }

        .timeline-badge-project {
            background: var(--dr-warning);
        }

        .timeline-content {
            min-width: 0;
            flex: 1;
        }

        .timeline-title-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 8px;
        }

        .timeline-label {
            font-size: 12px;
            color: #0f172a;
            font-weight: 900;
        }

        .timeline-time {
            font-size: 11px;
            color: #9ca3af;
            white-space: nowrap;
        }

        .timeline-text {
            font-size: 12px;
            color: #4b5563;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .timeline-address {
            font-size: 11px;
            color: var(--dr-muted);
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .employee-footer {
            padding: 12px 16px 14px 20px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            background: #fff;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        .btn-icon-ghost {
            min-width: 36px;
            min-height: 36px;
            border-radius: 12px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 10px !important;
        }

        .employee-details {
            display: none;
            margin: 0 16px 16px 20px;
            border: 1px solid #eef2f7;
            background: #fbfdff;
            border-radius: 15px;
            padding: 12px;
            animation: drFadeIn .18s ease both;
        }

        .employee-details.active {
            display: block;
        }

        @keyframes drFadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .details-kv {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-bottom: 10px;
        }

        .detail-kv-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            background: #fff;
            border: 1px solid var(--dr-border);
            border-radius: 12px;
            padding: 8px 9px;
            font-size: 12px;
        }

        .detail-kv-row span:first-child {
            color: var(--dr-muted);
            font-weight: 800;
        }

        .detail-kv-row span:last-child {
            color: #111827;
            font-weight: 950;
            text-align: right;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .detail-group-title {
            color: #111827;
            font-size: 12px;
            font-weight: 950;
            margin-bottom: 6px;
        }

        .detail-list {
            padding-left: 16px;
            margin: 0;
            color: #374151;
            font-size: 12px;
        }

        .detail-list li {
            margin-bottom: 5px;
        }

        .detail-empty {
            color: var(--dr-muted);
            font-size: 12px;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 48px 18px;
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            color: var(--dr-muted);
            background: #f8fafc;
            font-size: 14px;
            font-weight: 700;
        }

        .dr-pagination {
            padding: 0 16px 16px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .dr-pagination .btn {
            border-radius: 10px !important;
            min-width: 38px;
            font-weight: 900;
        }

        /* List/Grid view switch */
        .dr-view-switch {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px;
            border: 1px solid var(--dr-border);
            border-radius: 14px;
            background: #f8fafc;
        }

        .dr-view-btn {
            min-height: 34px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: var(--dr-muted);
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: var(--dr-transition);
        }

        .dr-view-btn:hover {
            background: #fff;
            color: var(--dr-text);
        }

        .dr-view-btn.active {
            background: var(--dr-green);
            color: #fff;
            box-shadow: 0 8px 16px -12px rgba(147, 194, 28, .9);
        }

        .employee-grid.is-list-view {
            display: block;
            padding: 12px;
        }

        .employee-list-wrap {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--dr-border);
            border-radius: 16px;
            background: #fff;
        }

        .employee-list-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1120px;
        }

        .employee-list-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #f8fafc;
            border-bottom: 1px solid var(--dr-border);
            color: var(--dr-muted);
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 12px 12px;
            white-space: nowrap;
        }

        .employee-list-table tbody td {
            border-bottom: 1px solid #eef2f7;
            padding: 12px;
            vertical-align: middle;
            color: #111827;
            font-size: 13px;
        }

        .employee-list-table tbody tr:hover td {
            background: #fbfdff;
        }

        .employee-list-main-row {
            cursor: pointer;
        }

        .employee-list-main-row:hover td {
            background: #f4fae7 !important;
        }

        .employee-list-main-row .list-actions,
        .employee-list-main-row .list-actions * {
            cursor: auto;
        }

        .employee-list-table tbody tr:last-child td {
            border-bottom: none;
        }

        .employee-list-person {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 240px;
        }

        .employee-list-avatar {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            object-fit: cover;
            border: 1px solid var(--dr-border);
            background: #f8fafc;
            flex: 0 0 auto;
        }

        .employee-list-name {
            font-size: 13px;
            font-weight: 950;
            color: #0f172a;
            line-height: 1.25;
        }

        .employee-list-sub {
            margin-top: 3px;
            color: var(--dr-muted);
            font-size: 11px;
            font-weight: 800;
        }

        .list-progress-cell {
            min-width: 170px;
        }

        .list-progress-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 6px;
        }

        .list-hours {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 6px;
            min-width: 210px;
        }

        .list-hour-pill {
            background: #f9fafb;
            border: 1px solid var(--dr-border);
            border-radius: 11px;
            padding: 7px 8px;
        }

        .list-hour-pill span {
            display: block;
            color: #9ca3af;
            font-size: 9px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 3px;
        }

        .list-hour-pill strong {
            display: block;
            color: #0f172a;
            font-size: 12px;
            font-weight: 950;
            white-space: nowrap;
        }

        .list-activity-mini {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            min-width: 220px;
        }

        .list-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 7px;
            min-width: 150px;
        }

        .list-details-row td {
            background: #fbfdff !important;
            padding: 0 12px 12px !important;
        }

        .list-details-panel {
            border: 1px solid #eef2f7;
            border-radius: 15px;
            background: #fff;
            padding: 12px;
            margin-top: 4px;
        }

        @media (max-width: 700px) {

            .dr-view-switch,
            .dr-view-btn {
                width: 100%;
            }

            .employee-grid.is-list-view {
                padding: 10px;
            }
        }

        @media (max-width: 1320px) {
            .employee-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 920px) {
            .dr-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .employee-grid {
                grid-template-columns: 1fr;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .daily-dashboard {
                padding: 0 2px 24px;
            }

            .dr-hero {
                padding: 17px;
                border-radius: 18px;
            }

            .dr-title {
                font-size: 24px;
            }

            .dr-analytics {
                grid-template-columns: 1fr;
            }

            .dr-toolbar-left,
            .dr-toolbar-right,
            .dr-filter-block,
            .dr-filter-block.search,
            .dr-period-btn,
            .dr-actions,
            .dr-btn,
            .dr-btn-soft,
            .dr-btn-danger {
                width: 100%;
            }

            .employee-grid {
                padding: 12px;
            }

            .hour-grid,
            .quick-stats,
            .details-kv {
                grid-template-columns: 1fr;
            }

            .employee-footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .footer-left {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="app-content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="daily-dashboard">
                    <div class="dr-hero">
                        <div class="dr-hero-row">
                            <div>
                                <div class="dr-kicker">
                                    <i class="feather icon-clock"></i>
                                    Tagesbericht Übersicht
                                </div>
                                <h1 class="dr-title">Mitarbeiter-Zeiten & Aktivitäten</h1>
                                <p class="dr-subtitle">Schnelle Kontrolle von Arbeitszeit, Abwesenheit, Aufgaben, Terminen
                                    und Tickets.</p>
                                <div class="dr-breadcrumb">
                                    <a href="{{ url('/') }}">Dashboard</a>
                                    <span>›</span>
                                    <span class="current">Tagesbericht</span>
                                </div>
                            </div>

                            <div class="dr-actions">
                                <button type="button" class="dr-btn-danger" onclick="verifyAdmin()">
                                    <i class="feather icon-lock"></i>
                                    Admin-Zugang
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="dr-analytics">
                        <div class="dr-stat">
                            <div class="dr-stat-icon total"><i class="feather icon-users"></i></div>
                            <div>
                                <div class="dr-stat-label">Mitarbeiter</div>
                                <div class="dr-stat-value" id="statTotalEmployees">0</div>
                                <div class="dr-stat-sub">im gewählten Zeitraum</div>
                            </div>
                        </div>
                        <div class="dr-stat">
                            <div class="dr-stat-icon done"><i class="feather icon-check-circle"></i></div>
                            <div>
                                <div class="dr-stat-label">Erledigt</div>
                                <div class="dr-stat-value" id="statCompletedEmployees">0</div>
                                <div class="dr-stat-sub">mindestens 100% Arbeitszeit</div>
                            </div>
                        </div>
                        <div class="dr-stat">
                            <div class="dr-stat-icon open"><i class="feather icon-alert-circle"></i></div>
                            <div>
                                <div class="dr-stat-label">Offen</div>
                                <div class="dr-stat-value" id="statOpenHours">0.00h</div>
                                <div class="dr-stat-sub">fehlende Arbeitszeit</div>
                            </div>
                        </div>
                        <div class="dr-stat">
                            <div class="dr-stat-icon absent"><i class="feather icon-heart"></i></div>
                            <div>
                                <div class="dr-stat-label">Abwesend</div>
                                <div class="dr-stat-value" id="statAbsentEmployees">0</div>
                                <div class="dr-stat-sub">Urlaub oder krank</div>
                            </div>
                        </div>
                    </div>

                    <div class="dr-toolbar">
                        <div class="dr-toolbar-left">
                            <div class="dr-filter-block">
                                <label class="dr-label">Datum</label>
                                <input type="text" id="datePicker" placeholder="Datum wählen">
                            </div>

                            <div class="dr-filter-block">
                                <label class="dr-label">Zeitraum</label>
                                <div class="dropdown daily-filter-btn">
                                    <button type="button" class="dr-period-btn dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" id="filterDropdownBtn">
                                        <span><i class="feather icon-filter"></i> Täglich</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right filter">
                                        <a class="dropdown-item filter-option active" href="#"
                                            data-value="daily">Täglich</a>
                                        <a class="dropdown-item filter-option" href="#" data-value="weekly">Wöchentlich</a>
                                        <a class="dropdown-item filter-option" href="#" data-value="monthly">Monatlich</a>
                                    </div>
                                </div>
                            </div>

                            <div class="dr-filter-block search">
                                <label class="dr-label">Suche</label>
                                <div class="dr-search-wrap">
                                    <i class="feather icon-search"></i>
                                    <input type="text" class="dr-input" name="search" placeholder="Mitarbeiter suchen ...">
                                </div>
                            </div>
                        </div>

                        <div class="dr-toolbar-right">
                            <button type="button" class="dr-btn-soft" id="clearSearchBtn">
                                <i class="feather icon-x"></i>
                                Zurücksetzen
                            </button>
                            <button type="button" class="dr-btn" id="manualSearchBtn">
                                <i class="feather icon-search"></i>
                                Suchen
                            </button>
                        </div>
                    </div>

                    <div class="dr-panel">
                        <div class="dr-panel-head">
                            <div>
                                <h2 class="dr-panel-title">Mitarbeiterliste</h2>
                                <div class="dr-panel-sub" id="panelSubtitle">Daten werden geladen ...</div>
                            </div>
                            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                <div class="chip chip-muted" id="panelRangeLabel">Täglich</div>
                                <div class="dr-view-switch" aria-label="Ansicht wechseln">
                                    <button type="button" class="dr-view-btn active" data-view="grid" id="gridViewBtn">
                                        <i class="feather icon-grid"></i>
                                        Karten
                                    </button>
                                    <button type="button" class="dr-view-btn" data-view="list" id="listViewBtn">
                                        <i class="feather icon-list"></i>
                                        Liste
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="employee-grid" id="employeeGrid"></div>
                        <div id="pagination" class="dr-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/de.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        let currentPage = 1;
        let selectedRange = 'daily';
        let currentSearch = '';
        let selectedDate = moment().format('YYYY-MM-DD');
        let searchTimer = null;
        let currentView = localStorage.getItem('dailyReportEmployeeView') || 'list';

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            if (Number.isNaN(date.getTime())) return dateStr;
            return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        function formatTimeRange(start, end) {
            if (!start && !end) return '';
            const s = (start || '').toString().substring(0, 5);
            const e = (end || '').toString().substring(0, 5);
            if (s && e) return `${s}–${e}`;
            return s || e;
        }

        function labelForType(type) {
            switch (type) {
                case 'appointment': return 'Termin';
                case 'task': return 'Aufgabe';
                case 'problem': return 'Ticket';
                case 'offer': return 'Angebot';
                case 'project': return 'Projekt';
                default: return type || '-';
            }
        }

        function badgeClassForType(type) {
            switch (type) {
                case 'appointment': return 'timeline-badge timeline-badge-appointment';
                case 'task': return 'timeline-badge timeline-badge-task';
                case 'problem': return 'timeline-badge timeline-badge-problem';
                case 'offer': return 'timeline-badge timeline-badge-offer';
                case 'project': return 'timeline-badge timeline-badge-project';
                default: return 'timeline-badge timeline-badge-task';
            }
        }

        function progressClass(percent) {
            if (percent < 50) return 'bg-danger';
            if (percent < 80) return 'bg-warning';
            return 'bg-success';
        }

        function renderEventPreview(events, maxItems = 4) {
            if (!events || !events.length) {
                return '<div class="detail-empty" style="padding:8px 0;">Keine Aktivitäten im Zeitraum.</div>';
            }

            return events.slice(0, maxItems).map(ev => `
                    <div class="timeline-item">
                        <div class="${badgeClassForType(ev.type)}"></div>
                        <div class="timeline-content">
                            <div class="timeline-title-row">
                                <span class="timeline-label">${escapeHtml(labelForType(ev.type))}</span>
                                <span class="timeline-time">${escapeHtml(formatDate(ev.start_date))} ${escapeHtml(formatTimeRange(ev.start_time, ev.end_time))}</span>
                            </div>
                            <div class="timeline-text">${escapeHtml(ev.title ?? '-')}</div>
                            ${ev.full_address ? `<div class="timeline-address">${escapeHtml(ev.full_address)}</div>` : ''}
                        </div>
                    </div>
                `).join('');
        }

        function renderDetailGroup(title, events, includeAddress = false) {
            if (!events || !events.length) {
                return `
                        <div>
                            <div class="detail-group-title">${escapeHtml(title)}</div>
                            <div class="detail-empty">Keine ${escapeHtml(title.toLowerCase())} im Zeitraum.</div>
                        </div>
                    `;
            }

            return `
                    <div>
                        <div class="detail-group-title">${escapeHtml(title)}</div>
                        <ul class="detail-list">
                            ${events.map(ev => `
                                <li>
                                    <span>${escapeHtml(formatDate(ev.start_date))} ${escapeHtml(formatTimeRange(ev.start_time, ev.end_time))}</span>
                                    — <strong>${escapeHtml(ev.title ?? '-')}</strong>
                                    ${includeAddress && ev.full_address ? `<br><span class="text-muted">${escapeHtml(ev.full_address)}</span>` : ''}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
        }

        function updateOverviewStats(data) {
            const total = data.length;
            const completed = data.filter(emp => Number(emp.progress || 0) >= 100).length;
            const absent = data.filter(emp => emp.on_leave_today || emp.on_sick_today).length;
            const openMinutes = data.reduce((sum, emp) => {
                const expected = Number(emp.expected_minutes || 0);
                const worked = Number(emp.worked_minutes || 0);
                return sum + Math.max(0, expected - worked);
            }, 0);

            $('#statTotalEmployees').text(total);
            $('#statCompletedEmployees').text(completed);
            $('#statAbsentEmployees').text(absent);
            $('#statOpenHours').text((openMinutes / 60).toFixed(2) + 'h');
            $('#panelSubtitle').text(`${total} Mitarbeiter geladen · ${completed} vollständig · ${(openMinutes / 60).toFixed(2)}h offen`);
        }

        function setLoadingState() {
            $('#employeeGrid').html('<div class="empty-state"><span class="spinner-border spinner-border-sm mr-1"></span> Daten werden geladen ...</div>');
        }

        function fetchEmployees(showLoader = true) {
            if (showLoader) setLoadingState();

            $.ajax({
                url: "{{ route('daily.report.employee.list.search') }}",
                method: "GET",
                data: {
                    page: currentPage,
                    filter: selectedRange,
                    search: currentSearch,
                    date: selectedDate
                },
                success: function (res) {
                    Swal.close();
                    const data = res.data || [];

                    if (data.length > 0) {
                        renderCards(data);
                    } else {
                        $('#employeeGrid').html('<div class="empty-state">Keine Mitarbeiter für diesen Zeitraum gefunden.</div>');
                    }

                    updateOverviewStats(data);
                    renderPagination(res.current_page || 1, res.last_page || 1);
                },
                error: function () {
                    Swal.close();
                    $('#employeeGrid').html('<div class="empty-state text-danger">Fehler beim Laden der Mitarbeiterdaten.</div>');
                }
            });
        }


        function setViewMode(view, shouldRender = true) {
            currentView = view === 'list' ? 'list' : 'grid';
            localStorage.setItem('dailyReportEmployeeView', currentView);

            $('.dr-view-btn').removeClass('active');
            $(`.dr-view-btn[data-view="${currentView}"]`).addClass('active');

            $('#employeeGrid').toggleClass('is-list-view', currentView === 'list');

            if (shouldRender) {
                fetchEmployees(true);
            }
        }

        function renderListRows(data) {
            const grid = $('#employeeGrid');
            const imagePath = @json(asset('images/employee'));
            const filterLbl = $('#filterDropdownBtn span').text().trim() || 'Täglich';
            const dateLabel = formatDate(selectedDate);

            $('#panelRangeLabel').text(filterLbl);
            grid.addClass('is-list-view');
            grid.empty();

            let rows = '';

            data.forEach(emp => {
                const workedMinutes = Number(emp.worked_minutes || 0);
                const expectedMinutes = Number(emp.expected_minutes || 0);
                const workedHours = (workedMinutes / 60).toFixed(2);
                const expectedHours = (expectedMinutes / 60).toFixed(2);
                const remainingHours = (Math.max(0, expectedMinutes - workedMinutes) / 60).toFixed(2);
                const overtimeHours = (Math.max(0, workedMinutes - expectedMinutes) / 60).toFixed(2);
                const percent = Math.max(0, Math.min(100, Math.round(emp.progress || 0)));
                const events = emp.events || [];
                const tasks = events.filter(e => e.type === 'task');
                const appointments = events.filter(e => e.type === 'appointment');
                const tickets = events.filter(e => e.type === 'problem');
                const offers = events.filter(e => e.type === 'offer');
                const projects = events.filter(e => e.type === 'project');
                const eventsWithCoords = events.filter(e => e.full_address && e.latitude && e.longitude);
                const canShowMap = eventsWithCoords.length > 0;
                const eventsJson = encodeURIComponent(JSON.stringify(eventsWithCoords));
                const fullName = `${emp.name || ''} ${emp.lastname || ''}`.trim() || 'Mitarbeiter';
                const statusChip = emp.on_sick_today
                    ? '<span class="chip chip-danger">Krank</span>'
                    : emp.on_leave_today
                        ? '<span class="chip chip-status">Urlaub</span>'
                        : '<span class="chip chip-muted">Aktiv</span>';

                const reportUrl = `/employee_daily_report/${escapeHtml(emp.employee_id)}/${selectedDate}/${selectedDate}`;

                rows += `
                        <tr class="employee-list-main-row" data-report-url="${reportUrl}" title="Tagesbericht öffnen">
                            <td>
                                <div class="employee-list-person">
                                    <img src="${imagePath}/${escapeHtml(emp.image || 'avatar.png')}"
                                         class="employee-list-avatar"
                                         alt="${escapeHtml(fullName)}"
                                         onerror="this.src='${imagePath}/avatar.png';">
                                    <div>
                                        <div class="employee-list-name">${escapeHtml(fullName)}</div>
                                        <div class="employee-list-sub">ID #${escapeHtml(emp.employee_id)} · ${escapeHtml(emp.status || 'Mitarbeiter')}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${statusChip}</td>
                            <td class="list-progress-cell">
                                <div class="list-progress-top">
                                    <span>Arbeitszeit</span>
                                    <strong>${percent}%</strong>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar ${progressClass(percent)}" role="progressbar" style="width:${percent}%"></div>
                                </div>
                            </td>
                            <td>
                                <div class="list-hours">
                                    <div class="list-hour-pill"><span>Geleistet</span><strong>${workedHours}h</strong></div>
                                    <div class="list-hour-pill"><span>Offen</span><strong>${remainingHours}h</strong></div>
                                    <div class="list-hour-pill"><span>Über</span><strong>${overtimeHours}h</strong></div>
                                </div>
                            </td>
                            <td>
                                <div class="list-activity-mini">
                                    <span class="chip chip-soft">${tasks.length} Aufgaben</span>
                                    <span class="chip chip-status">${appointments.length} Termine</span>
                                    <span class="chip chip-danger">${tickets.length} Tickets</span>
                                </div>
                            </td>
                            <td>
                                <div class="list-activity-mini">
                                    <span class="chip chip-muted">Urlaub ${escapeHtml(emp.leave_used_days ?? 0)} / ${escapeHtml(emp.annual_leave_days ?? 0)}</span>
                                    <span class="chip chip-muted">Krank ${escapeHtml(emp.sick_days ?? 0)}</span>
                                </div>
                            </td>
                            <td>
                                <div class="list-actions">
                                    <button type="button" class="btn btn-light btn-icon-ghost btn-sm btn-info-toggle" data-emp-id="${escapeHtml(emp.employee_id)}" title="Details anzeigen">
                                        <i class="feather icon-info"></i>
                                    </button>
                                    <a class="btn btn-success btn-icon-ghost btn-sm" href="${reportUrl}" title="Tagesbericht öffnen">
                                        <i class="feather icon-play"></i>
                                    </a>
                                    ${canShowMap ? `
                                        <button type="button" class="btn btn-outline-primary btn-icon-ghost btn-sm btn-map" data-events="${eventsJson}" title="Karte anzeigen">
                                            <i class="feather icon-map-pin"></i>
                                        </button>
                                    ` : ``}
                                </div>
                            </td>
                        </tr>
                        <tr class="list-details-row">
                            <td colspan="7">
                                <div class="employee-details" id="details-${escapeHtml(emp.employee_id)}">
                                    <div class="details-kv">
                                        <div class="detail-kv-row"><span>Datum</span><span>${escapeHtml(dateLabel)}</span></div>
                                        <div class="detail-kv-row"><span>Geplant</span><span>${expectedHours}h</span></div>
                                        <div class="detail-kv-row"><span>Resturlaub</span><span>${escapeHtml(emp.leave_remaining_days ?? 0)} Tage</span></div>
                                        <div class="detail-kv-row"><span>Abwesenheit</span><span>${escapeHtml(emp.recurring_weekly_days ?? 0)} Tag(e) / Woche</span></div>
                                    </div>
                                    <div class="details-grid">
                                        ${renderDetailGroup('Aufgaben', tasks, false)}
                                        ${renderDetailGroup('Termine', appointments, true)}
                                        ${renderDetailGroup('Tickets', tickets, false)}
                                        ${renderDetailGroup('Angebote', offers, false)}
                                        ${renderDetailGroup('Projekte', projects, false)}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
            });

            grid.html(`
                    <div class="employee-list-wrap">
                        <table class="employee-list-table">
                            <thead>
                                <tr>
                                    <th>Mitarbeiter</th>
                                    <th>Status</th>
                                    <th>Fortschritt</th>
                                    <th>Stunden</th>
                                    <th>Aktivitäten</th>
                                    <th>Abwesenheit</th>
                                    <th style="text-align:right;">Aktion</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `);

            if (window.feather) {
                window.feather.replace();
            }
        }

        function renderCards(data) {
            if (currentView === 'list') {
                renderListRows(data);
                return;
            }

            const grid = $('#employeeGrid');
            const imagePath = @json(asset('images/employee'));
            const filterLbl = $('#filterDropdownBtn span').text().trim() || 'Täglich';

            $('#panelRangeLabel').text(filterLbl);
            grid.removeClass('is-list-view');
            grid.empty();

            data.forEach(emp => {
                const workedMinutes = Number(emp.worked_minutes || 0);
                const expectedMinutes = Number(emp.expected_minutes || 0);
                const workedHours = (workedMinutes / 60).toFixed(2);
                const expectedHours = (expectedMinutes / 60).toFixed(2);
                const remainingHours = Math.max(0, expectedMinutes - workedMinutes) / 60;
                const percent = Math.max(0, Math.min(100, Math.round(emp.progress || 0)));
                const borderColor = emp.color || '#93c21c';
                const events = emp.events || [];
                const tasks = events.filter(e => e.type === 'task');
                const appointments = events.filter(e => e.type === 'appointment');
                const tickets = events.filter(e => e.type === 'problem');
                const offers = events.filter(e => e.type === 'offer');
                const projects = events.filter(e => e.type === 'project');
                const eventsWithCoords = events.filter(e => e.full_address && e.latitude && e.longitude);
                const canShowMap = eventsWithCoords.length > 0;
                const eventsJson = encodeURIComponent(JSON.stringify(eventsWithCoords));
                const dateLabel = formatDate(selectedDate);
                const fullName = `${emp.name || ''} ${emp.lastname || ''}`.trim() || 'Mitarbeiter';

                const cardHtml = `
                        <article class="employee-card" style="--emp-color:${escapeHtml(borderColor)}">
                            <div class="employee-card-top">
                                <div class="employee-avatar-wrap">
                                    <img src="${imagePath}/${escapeHtml(emp.image || 'avatar.png')}"
                                         class="employee-avatar"
                                         alt="${escapeHtml(fullName)}"
                                         onerror="this.src='${imagePath}/avatar.png';">
                                    <div class="employee-avatar-status"></div>
                                </div>

                                <div class="employee-title">
                                    <div class="employee-name">${escapeHtml(fullName)}</div>
                                    <div class="employee-chips">
                                        <span class="chip chip-soft">ID #${escapeHtml(emp.employee_id)}</span>
                                        ${emp.status ? `<span class="chip chip-status">${escapeHtml(emp.status)}</span>` : ''}
                                        ${emp.on_leave_today ? `<span class="chip chip-status">Heute im Urlaub</span>` : ''}
                                        ${emp.on_sick_today ? `<span class="chip chip-danger">Krank gemeldet</span>` : ''}
                                        ${emp.recurring_leaves_count ? `<span class="chip chip-muted">${escapeHtml(emp.recurring_leaves_count)} feste Abwesenheit(en)</span>` : ''}
                                        <span class="chip chip-muted">${escapeHtml(dateLabel)}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="employee-body">
                                <div class="work-row">
                                    <span class="work-label">Arbeitszeit</span>
                                    <span class="work-percent">${percent}% erledigt</span>
                                </div>

                                <div class="progress progress-thin">
                                    <div class="progress-bar ${progressClass(percent)}" role="progressbar" style="width:${percent}%"></div>
                                </div>

                                <div class="hour-grid">
                                    <div class="hour-box">
                                        <span class="label">Geleistet</span>
                                        <span class="value">${workedHours}h</span>
                                    </div>
                                    <div class="hour-box">
                                        <span class="label">Offen</span>
                                        <span class="value">${remainingHours.toFixed(2)}h</span>
                                    </div>
                                    <div class="hour-box">
                                        <span class="label">Geplant</span>
                                        <span class="value">${expectedHours}h</span>
                                    </div>
                                </div>

                                <div class="quick-stats">
                                    <div class="stat-pill">
                                        <span class="label">Urlaub</span>
                                        <span class="value">${escapeHtml(emp.leave_used_days ?? 0)} / ${escapeHtml(emp.annual_leave_days ?? 0)} Tage</span>
                                    </div>
                                    <div class="stat-pill">
                                        <span class="label">Resturlaub</span>
                                        <span class="value">${escapeHtml(emp.leave_remaining_days ?? 0)} Tage</span>
                                    </div>
                                    <div class="stat-pill">
                                        <span class="label">Kranktage</span>
                                        <span class="value">${escapeHtml(emp.sick_days ?? 0)} Tage</span>
                                    </div>
                                    <div class="stat-pill">
                                        <span class="label">Wöchentlich abwesend</span>
                                        <span class="value">${escapeHtml(emp.recurring_weekly_days ?? 0)} Tag(e)</span>
                                    </div>
                                </div>

                                <div class="timeline">
                                    <div class="timeline-header">
                                        <span class="timeline-title">Aktivitäten (${escapeHtml(filterLbl)})</span>
                                        <span class="timeline-meta">${tasks.length} Aufgaben · ${appointments.length} Termine · ${tickets.length} Tickets</span>
                                    </div>
                                    <div class="timeline-list">
                                        ${renderEventPreview(events)}
                                    </div>
                                </div>
                            </div>

                            <div class="employee-footer">
                                <div class="footer-left">
                                    <button type="button" class="btn btn-light btn-icon-ghost btn-sm btn-info-toggle" data-emp-id="${escapeHtml(emp.employee_id)}" title="Details anzeigen">
                                        <i class="feather icon-info"></i>
                                    </button>

                                    <a class="btn btn-success btn-icon-ghost btn-sm" href="${reportUrl}" title="Tagesbericht öffnen">
                                        <i class="feather icon-play"></i>
                                    </a>

                                    ${canShowMap ? `
                                        <button type="button" class="btn btn-outline-primary btn-icon-ghost btn-sm btn-map" data-events="${eventsJson}" title="Karte anzeigen">
                                            <i class="feather icon-map-pin"></i>
                                        </button>
                                    ` : ``}
                                </div>

                                <small class="text-muted">Zeitraum: ${escapeHtml(filterLbl)}</small>
                            </div>

                            <div class="employee-details" id="details-${escapeHtml(emp.employee_id)}">
                                <div class="details-kv">
                                    <div class="detail-kv-row"><span>Urlaub</span><span>${escapeHtml(emp.leave_used_days ?? 0)} / ${escapeHtml(emp.annual_leave_days ?? 0)} Tage</span></div>
                                    <div class="detail-kv-row"><span>Resturlaub</span><span>${escapeHtml(emp.leave_remaining_days ?? 0)} Tage</span></div>
                                    <div class="detail-kv-row"><span>Kranktage</span><span>${escapeHtml(emp.sick_days ?? 0)} Tage</span></div>
                                    <div class="detail-kv-row"><span>Abwesenheit</span><span>${escapeHtml(emp.recurring_weekly_days ?? 0)} Tag(e) / Woche</span></div>
                                </div>

                                <div class="details-grid">
                                    ${renderDetailGroup('Aufgaben', tasks, false)}
                                    ${renderDetailGroup('Termine', appointments, true)}
                                    ${renderDetailGroup('Tickets', tickets, false)}
                                    ${renderDetailGroup('Angebote', offers, false)}
                                    ${renderDetailGroup('Projekte', projects, false)}
                                </div>
                            </div>
                        </article>
                    `;

                grid.append(cardHtml);
            });

            if (window.feather) {
                window.feather.replace();
            }
        }

        function renderPagination(current, last) {
            const container = $('#pagination');
            container.html('');
            if (last <= 1) return;

            const addBtn = (page, label, active = false, disabled = false) => {
                container.append(`
                        <button type="button"
                                class="btn btn-sm btn-${active ? 'primary' : 'light'}"
                                ${disabled ? 'disabled' : ''}
                                onclick="goToPage(${page})">${label}</button>
                    `);
            };

            addBtn(Math.max(1, current - 1), '‹', false, current <= 1);

            const start = Math.max(1, current - 2);
            const end = Math.min(last, current + 2);
            for (let i = start; i <= end; i++) {
                addBtn(i, i, i === current, false);
            }

            addBtn(Math.min(last, current + 1), '›', false, current >= last);
        }

        function goToPage(page) {
            currentPage = page;
            Swal.fire({
                title: 'Lade Daten...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            fetchEmployees(false);
        }

        function showMap(events) {
            Swal.fire({
                title: 'Standorte der Termine / Aufgaben',
                html: '<div id="leafletMap" style="width:100%; height:500px; border-radius:14px; overflow:hidden;"></div>',
                width: '80%',
                didOpen: () => {
                    const map = L.map('leafletMap').setView([51.1657, 10.4515], 6);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);

                    let bounds = [];

                    events.forEach(event => {
                        if (!event.latitude || !event.longitude) return;
                        const lat = parseFloat(event.latitude);
                        const lon = parseFloat(event.longitude);
                        const marker = L.marker([lat, lon]).addTo(map);
                        marker.bindPopup(`<strong>${escapeHtml(event.title ?? labelForType(event.type))}</strong><br>${escapeHtml(event.full_address ?? '')}`);
                        bounds.push([lat, lon]);
                    });

                    if (bounds.length) {
                        map.fitBounds(bounds, { padding: [30, 30] });
                    }

                    setTimeout(() => map.invalidateSize(), 200);
                }
            });
        }

        flatpickr('#datePicker', {
            locale: 'de',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd. F Y',
            defaultDate: 'today',
            allowInput: true,
            onChange: function (selectedDates, dateStr) {
                selectedDate = dateStr;
                currentPage = 1;
                Swal.fire({
                    title: 'Lade Daten...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                fetchEmployees(false);
            }
        });

        $('input[name="search"]').on('input', function () {
            currentSearch = $(this).val();
            currentPage = 1;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchEmployees(false), 300);
        });

        $('#manualSearchBtn').on('click', function () {
            currentSearch = $('input[name="search"]').val();
            currentPage = 1;
            fetchEmployees();
        });

        $('#clearSearchBtn').on('click', function () {
            $('input[name="search"]').val('');
            currentSearch = '';
            currentPage = 1;
            fetchEmployees();
        });

        $('.filter-option').on('click', function (e) {
            e.preventDefault();
            $('.filter-option').removeClass('active');
            $(this).addClass('active');

            selectedRange = $(this).data('value');
            $('#filterDropdownBtn span').html(`<i class="feather icon-filter"></i> ${$(this).text()}`);
            $('#panelRangeLabel').text($(this).text());
            currentPage = 1;

            Swal.fire({
                title: 'Lade Daten...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            fetchEmployees(false);

            if (window.feather) {
                setTimeout(() => window.feather.replace(), 0);
            }
        });

        $('#employeeGrid').on('click', '.btn-info-toggle', function () {
            const empId = $(this).data('emp-id');
            const details = $('#details-' + empId);
            details.toggleClass('active');
            $(this).toggleClass('btn-primary btn-light');
        });

        $('#employeeGrid').on('click', '.btn-map', function () {
            const raw = $(this).attr('data-events') || '[]';
            const events = JSON.parse(decodeURIComponent(raw));
            showMap(events);
        });

        $('#employeeGrid').on('click', '.employee-list-main-row', function (event) {
            if ($(event.target).closest('a, button, .btn, .list-actions').length) {
                return;
            }

            const url = $(this).data('report-url');
            if (url) {
                window.location.href = url;
            }
        });


        $(document).on('click', '.dr-view-btn', function () {
            const view = $(this).data('view') || 'grid';
            setViewMode(view, true);
        });

        $(document).ready(() => {
            setViewMode(currentView, false);

            Swal.fire({
                title: 'Lade Daten...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            fetchEmployees(false);

            if (window.feather) {
                window.feather.replace();
            }
        });
    </script>

    <script>
        function verifyAdmin() {
            Swal.fire({
                title: 'Admin-Zugang',
                html:
                    `<input type="email" id="swal-email" class="swal2-input" placeholder="E-Mail-Adresse">` +
                    `<input type="password" id="swal-password" class="swal2-input" placeholder="Passwort">`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Einloggen',
                cancelButtonText: 'Abbrechen',
                preConfirm: () => {
                    const email = document.getElementById('swal-email').value;
                    const password = document.getElementById('swal-password').value;
                    if (!email || !password) {
                        Swal.showValidationMessage('Bitte E-Mail und Passwort eingeben');
                        return false;
                    }
                    return { email, password };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const { email, password } = result.value;

                    Swal.fire({
                        title: 'Überprüfe Zugang...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('/verify-admin', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ email, password })
                    })
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Zugriff gewährt',
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Zugriff verweigert',
                                    text: 'E-Mail oder Passwort ist falsch oder keine Admin-Rechte.',
                                });
                            }
                        })
                        .catch(() => {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: 'Serverfehler beim Überprüfen des Zugangs.',
                            });
                        });
                }
            });
        }
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
                label: 'Tagesbericht',
                url: "{{ url()->current() }}",
            },
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush