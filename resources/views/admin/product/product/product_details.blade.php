@extends('admin.layouts.app')

@section('title') Produkt – {{ $data->product }} @endsection

@php
    $documentsCol = collect($documents ?? []);
    $descriptionsCol = collect($descriptions ?? []);
    $installationCol = collect($installation ?? []);
    $technicalPersonCol = collect($technical_person ?? []);

    $statusIsPublished = ($data->status ?? '') === 'Published';
    $statusLabel = $statusIsPublished ? 'Aktiv' : 'Inaktiv';
    $statusClass = $statusIsPublished ? 'green' : 'orange';

    $techCount = $descriptionsCol->count();
    $docsCount = $documentsCol->count();
    $installCount = $installationCol->count();
    $teamCount = $technicalPersonCol->count();
@endphp

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

    <style>
        :root {
            --app-bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-dark: #111827;
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
            --danger-hover: #dc2626;
            --danger-light: #fef2f2;
            --gray: #6b7280;
            --gray-light: #f3f4f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --radius: 14px;
            --transition: all .2s ease-in-out;
        }

        body {
            background: var(--app-bg) !important;
        }

        .prod-wrap {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text-main);
        }

        .prod-header {
            margin-bottom: 18px;
        }

        .prod-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .prod-title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -.025em;
            color: var(--text-dark);
            line-height: 1.15;
        }

        .prod-sub {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
            max-width: 780px;
        }

        .prod-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .prod-breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 800;
        }

        .prod-breadcrumb a:hover {
            color: var(--text-main);
        }

        .prod-breadcrumb span.current {
            color: var(--text-dark);
            font-weight: 900;
        }

        .prod-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .prod-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            line-height: 1.2;
        }

        .prod-btn:hover {
            background: var(--primary-hover);
            color: #fff;
            text-decoration: none;
        }

        .prod-btn-soft {
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--border);
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 800;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            line-height: 1.2;
        }

        .prod-btn-soft:hover {
            background: #f9fafb;
            color: var(--text-main);
            border-color: #d1d5db;
            text-decoration: none;
        }

        .prod-btn-soft.danger {
            color: var(--danger);
            background: var(--danger-light);
            border-color: rgba(239, 68, 68, .18);
        }

        .prod-btn-soft.success {
            color: #047857;
            background: var(--success-light);
            border-color: rgba(16, 185, 129, .22);
        }

        .prod-btn-ic {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .prod-btn-ic:hover {
            background: #f9fafb;
            color: var(--text-main);
            border-color: #d1d5db;
            text-decoration: none;
        }

        .prod-btn-ic.primary {
            color: var(--primary);
            border-color: var(--primary-light);
            background: var(--primary-light);
        }

        .prod-btn-ic.danger {
            color: var(--danger);
            border-color: rgba(239, 68, 68, .18);
            background: var(--danger-light);
        }

        .prod-btn-ic.success {
            color: var(--success);
            border-color: rgba(16, 185, 129, .22);
            background: var(--success-light);
        }

        .prod-btn-ic.warning {
            color: #d97706;
            border-color: #fde7b0;
            background: #fffbeb;
        }

        .prod-analytics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        @media(max-width:1200px) {
            .prod-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:700px) {
            .prod-analytics {
                grid-template-columns: 1fr;
            }
        }

        .prod-stat {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 92px;
        }

        .prod-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .prod-stat-icon.total {
            background: var(--blue-light);
            color: var(--blue);
        }

        .prod-stat-icon.published {
            background: var(--success-light);
            color: var(--success);
        }

        .prod-stat-icon.docs {
            background: var(--warning-light);
            color: #d97706;
        }

        .prod-stat-icon.team {
            background: var(--gray-light);
            color: var(--gray);
        }

        .prod-stat-meta {
            min-width: 0;
        }

        .prod-stat-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .prod-stat-value {
            font-size: 24px;
            font-weight: 900;
            color: var(--text-dark);
            line-height: 1.1;
            margin-top: 4px;
        }

        .prod-stat-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .prod-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .prod-card.pad {
            padding: 16px;
            overflow: visible;
        }

        .prod-toolbar {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
        }

        .prod-toolbar-left,
        .prod-toolbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .prod-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
            background: #f9fafb;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .prod-chip.blue {
            background: var(--blue-light);
            color: #2563eb;
            border-color: #dbeafe;
        }

        .prod-chip.green {
            background: var(--success-light);
            color: #047857;
            border-color: #c7f2df;
        }

        .prod-chip.orange {
            background: var(--warning-light);
            color: #b45309;
            border-color: #fde7b0;
        }

        .prod-chip.red {
            background: var(--danger-light);
            color: #b91c1c;
            border-color: rgba(239, 68, 68, .18);
        }

        .prod-chip.gray {
            background: var(--gray-light);
            color: #4b5563;
            border-color: var(--border);
        }

        .prod-hero-card {
            display: grid;
            grid-template-columns: minmax(280px, 420px) minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        @media(max-width:1100px) {
            .prod-hero-card {
                grid-template-columns: 1fr;
            }
        }

        .prod-media {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .prod-media-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
        }

        .prod-media-title {
            font-size: 14px;
            font-weight: 900;
            color: var(--text-dark);
        }

        .prod-media-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .prod-media-body {
            padding: 14px;
            background: linear-gradient(135deg, #ffffff, #f8fafc);
        }

        .prod-slider-box {
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            background: #020617;
            min-height: 180px;
        }

        .prod-doc-chips {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .prod-doc-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-main);
            background: #fff;
            text-decoration: none;
            max-width: 100%;
        }

        .prod-doc-chip:hover {
            background: #f9fafb;
            color: var(--text-main);
            text-decoration: none;
        }

        .prod-detail-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .prod-detail-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
        }

        .prod-detail-title {
            font-size: 18px;
            font-weight: 900;
            color: var(--text-dark);
            margin: 0;
            line-height: 1.2;
        }

        .prod-detail-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .prod-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0;
            border-bottom: 1px solid var(--border);
        }

        @media(max-width:900px) {
            .prod-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:560px) {
            .prod-info-grid {
                grid-template-columns: 1fr;
            }
        }

        .prod-info-item {
            padding: 14px 16px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            min-width: 0;
        }

        .prod-info-item:nth-child(3n) {
            border-right: 0;
        }

        @media(max-width:900px) {
            .prod-info-item:nth-child(3n) {
                border-right: 1px solid var(--border)
            }

            .prod-info-item:nth-child(2n) {
                border-right: 0;
            }
        }

        @media(max-width:560px) {
            .prod-info-item {
                border-right: 0 !important;
            }
        }

        .prod-info-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 5px;
        }

        .prod-info-value {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .prod-description {
            padding: 16px;
        }

        .prod-description-box {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #f9fafb;
            padding: 14px;
            color: #374151;
            max-height: 220px;
            overflow: auto;
            font-size: 14px;
            line-height: 1.65;
        }

        .prod-tabs-wrap {
            width: 100%;
            margin: 0 0 16px;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--shadow-sm);
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .prod-tabs-wrap::-webkit-scrollbar {
            height: 6px;
        }

        .prod-tabs-wrap::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 999px;
        }

        .prod-tabs.nav,
        .prod-tabs.nav-tabs {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            flex-wrap: nowrap !important;
            gap: 8px;
            width: max-content;
            min-width: 100%;
            border-bottom: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
        }

        .prod-tabs>.nav-item {
            display: block !important;
            width: auto !important;
            max-width: none !important;
            flex: 0 0 auto !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .prod-tabs>.nav-item>.nav-link {
            border: 1px solid var(--border) !important;
            border-radius: 999px !important;
            background: #fff;
            color: var(--text-muted);
            font-weight: 900;
            font-size: 13px;
            padding: 9px 13px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 7px;
            line-height: 1.1 !important;
            white-space: nowrap !important;
            transition: var(--transition);
        }

        .prod-tabs .nav-link:hover {
            background: #f9fafb;
            color: var(--text-main);
        }

        .prod-tabs .nav-link.active {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
            box-shadow: 0 10px 20px -14px rgba(147, 194, 28, .8);
        }

        .prod-panel-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            padding: 16px;
            margin-bottom: 16px;
            overflow: visible;
        }

        .prod-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .prod-panel-title {
            font-size: 16px;
            font-weight: 900;
            color: var(--text-dark);
            margin: 0;
        }

        .prod-panel-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .prod-list-head {
            display: grid;
            gap: 14px;
            align-items: center;
            padding: 14px 16px 8px;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .prod-list-head.two {
            grid-template-columns: minmax(180px, .75fr) minmax(0, 1.4fr) 120px;
        }

        .prod-list-head.docs {
            grid-template-columns: minmax(180px, 1fr) minmax(0, 1.2fr) 100px 130px;
        }

        .prod-list-head.inventory {
            grid-template-columns: 70px repeat(5, minmax(110px, 1fr)) 120px;
        }

        @media(max-width:1100px) {
            .prod-list-head {
                display: none !important;
            }
        }

        .prod-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 0 0 16px;
        }

        .prod-item {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            transition: var(--transition);
            overflow: hidden;
            margin: 0 16px;
        }

        .prod-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .prod-item-row {
            padding: 16px;
            display: grid;
            gap: 14px;
            align-items: center;
        }

        .prod-item-row.two {
            grid-template-columns: minmax(180px, .75fr) minmax(0, 1.4fr) 120px;
        }

        .prod-item-row.docs {
            grid-template-columns: minmax(180px, 1fr) minmax(0, 1.2fr) 100px 130px;
        }

        .prod-cell-title {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
            display: none;
        }

        @media(max-width:1100px) {
            .prod-item-row {
                grid-template-columns: 1fr !important
            }

            .prod-cell-title {
                display: block
            }

            .prod-actions {
                justify-content: flex-start;
            }
        }

        .prod-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .prod-main-title {
            font-weight: 900;
            font-size: 15px;
            color: var(--text-dark);
            margin-bottom: 4px;
            word-break: break-word;
        }

        .prod-main-sub {
            font-size: 13px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .prod-table-wrap {
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: auto;
            background: #fff;
        }

        .prod-table {
            width: 100%;
            margin: 0 !important;
        }

        .prod-table th {
            background: #fafafa;
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 900;
            border-top: 0 !important;
            border-bottom: 1px solid var(--border) !important;
            white-space: nowrap;
        }

        .prod-table td {
            font-size: 13px;
            color: var(--text-main);
            vertical-align: middle !important;
            border-top: 1px solid var(--border) !important;
        }

        .prod-form-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 14px;
            align-items: end;
        }

        .prod-field {
            grid-column: span 3;
        }

        .prod-field.sm {
            grid-column: span 2;
        }

        .prod-field.lg {
            grid-column: span 4;
        }

        @media(max-width:1100px) {

            .prod-field,
            .prod-field.sm,
            .prod-field.lg {
                grid-column: span 6;
            }
        }

        @media(max-width:700px) {

            .prod-field,
            .prod-field.sm,
            .prod-field.lg {
                grid-column: span 12;
            }
        }

        .prod-label,
        .supplier-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 900;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .prod-wrap .form-control,
        .xmodal .form-control {
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 10px 12px;
            font-size: 14px;
            box-shadow: none !important;
            min-height: 42px;
        }

        .prod-wrap .form-control:focus,
        .xmodal .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light) !important;
        }

        .supplier-input-group .input-group-text {
            font-size: 13px;
            font-weight: 900;
            background: #f9fafb;
            border-color: var(--border);
            border-radius: 9px;
        }

        .supplier-calc-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border-radius: 999px;
            background: var(--success-light);
            color: #047857;
            font-size: 12px;
            font-weight: 900;
            border: 1px solid rgba(16, 185, 129, .22);
        }

        .timeline {
            list-style: none;
            padding-left: 0;
            position: relative;
            margin: 0;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(148, 163, 184, .65);
        }

        .timeline-item {
            position: relative;
            padding-left: 34px;
            padding-bottom: 14px;
        }

        .timeline-marker {
            position: absolute;
            left: 3px;
            width: 15px;
            height: 15px;
            border-radius: 999px;
            background: var(--success);
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, .35);
            top: 3px;
        }

        .timeline-marker.danger {
            background: var(--danger);
            box-shadow: 0 0 0 2px rgba(239, 68, 68, .35);
        }

        .timeline-marker.warning {
            background: var(--warning);
            box-shadow: 0 0 0 2px rgba(245, 158, 11, .35);
        }

        .timeline-title {
            font-weight: 900;
            font-size: 14px;
            color: var(--text-dark);
        }

        .timeline-meta {
            font-size: 12px;
            color: var(--text-muted);
        }

        .tech-stars i {
            color: #facc15;
            font-size: 12px;
        }

        .prod-avatar {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .prod-empty {
            padding: 42px 20px;
            text-align: center;
            color: var(--text-muted);
            border: 1px dashed var(--border);
            border-radius: 16px;
            background: #fff;
        }

        /* Bootstrap modal polish */
        .modal-content {
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .modal-header,
        .modal-footer {
            background: #fafafa;
            border-color: var(--border);
        }

        .modal-title {
            font-weight: 900;
            color: var(--text-dark);
        }

        /* Custom supplier edit modal */
        .xmodal {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
        }

        .xmodal.is-open {
            display: block;
        }

        .xmodal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, .55);
            backdrop-filter: blur(3px);
        }

        .xmodal__panel {
            position: relative;
            width: min(1100px, calc(100% - 32px));
            margin: 32px auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            outline: none;
        }

        .xmodal__header,
        .xmodal__footer {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
        }

        .xmodal__footer {
            border-top: 1px solid var(--border);
            border-bottom: 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .xmodal__body {
            padding: 18px;
            max-height: calc(100vh - 180px);
            overflow: auto;
        }

        .xmodal__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .xmodal__title {
            margin: 0;
            font-weight: 900;
            color: var(--text-dark);
        }

        .xmodal__subtitle {
            color: var(--text-muted);
        }

        .xmodal__actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .xmodal__close {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            cursor: pointer;
        }

        .xmodal__close:hover {
            background: #f9fafb;
        }

        .xswitch {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .xswitch input {
            display: none;
        }

        .xswitch__track {
            width: 44px;
            height: 26px;
            border-radius: 999px;
            background: rgba(0, 0, 0, .12);
            position: relative;
            transition: .15s ease;
        }

        .xswitch__track::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .18);
            transition: .15s ease;
        }

        .xswitch input:checked+.xswitch__track {
            background: rgba(147, 194, 28, .45);
        }

        .xswitch input:checked+.xswitch__track::after {
            transform: translateX(18px);
        }

        .xswitch__label {
            font-weight: 900;
            font-size: .9rem;
            color: var(--text-main);
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid var(--border);
            border-radius: 9px;
            min-height: 42px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
@endsection

@section('content')
    <div class="app-content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            <div class="content-body">
                <div class="prod-wrap">
                    <div class="prod-header">
                        <div class="prod-titlebar">
                            <div>
                                <div class="prod-title">{{ $data->product }}</div>
                                <div class="prod-sub">
                                    Produktdetails, technische Daten, Lieferantenpreise, Dokumente, Inventar und
                                    Montageinformationen zentral verwalten.
                                </div>
                                <div class="prod-breadcrumb">
                                    <a href="{{ url('/') }}">Dashboard</a>
                                    <span>›</span>
                                    <a href="{{ route('product.info') }}">Produkte</a>
                                    <span>›</span>
                                    <span class="current">{{ $data->product }} – {{ $data->model ?: 'ohne Modell' }}</span>
                                </div>
                            </div>

                            <div class="prod-actions">
                                <a href="{{ url('/product/edit/' . $data->id) }}" class="prod-btn">
                                    <i class="feather icon-edit"></i> Bearbeiten
                                </a>
                                <a href="{{ url('/product_installation/' . $data->id) }}" class="prod-btn-soft">
                                    <i class="feather icon-clock"></i> Montagezeiten
                                </a>
                                @if($statusIsPublished)
                                    <a href="{{ url('/product_unpublish/' . $data->id) }}" class="prod-btn-soft danger">
                                        <i class="feather icon-slash"></i> Deaktivieren
                                    </a>
                                @else
                                    <a href="{{ url('/product_publish/' . $data->id) }}" class="prod-btn-soft success">
                                        <i class="feather icon-check"></i> Aktivieren
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="prod-analytics">
                        <div class="prod-stat">
                            <div class="prod-stat-icon total"><i class="feather icon-package"></i></div>
                            <div class="prod-stat-meta">
                                <div class="prod-stat-label">Produkt-ID</div>
                                <div class="prod-stat-value">#{{ $data->id }}</div>
                                <div class="prod-stat-sub">{{ $data->article_no ?: 'Keine Artikelnummer' }}</div>
                            </div>
                        </div>
                        <div class="prod-stat">
                            <div class="prod-stat-icon published"><i class="feather icon-activity"></i></div>
                            <div class="prod-stat-meta">
                                <div class="prod-stat-label">Status</div>
                                <div class="prod-stat-value">{{ $statusLabel }}</div>
                                <div class="prod-stat-sub">{{ $data->brandname ?: 'Hersteller unbekannt' }}</div>
                            </div>
                        </div>
                        <div class="prod-stat">
                            <div class="prod-stat-icon docs"><i class="feather icon-file-text"></i></div>
                            <div class="prod-stat-meta">
                                <div class="prod-stat-label">Dokumente</div>
                                <div class="prod-stat-value">{{ $docsCount }}</div>
                                <div class="prod-stat-sub">Technische Unterlagen</div>
                            </div>
                        </div>
                        <div class="prod-stat">
                            <div class="prod-stat-icon team"><i class="feather icon-users"></i></div>
                            <div class="prod-stat-meta">
                                <div class="prod-stat-label">Team</div>
                                <div class="prod-stat-value">{{ $teamCount }}</div>
                                <div class="prod-stat-sub">Technische Verantwortliche</div>
                            </div>
                        </div>
                    </div>

                    <div class="prod-toolbar" style="display:none;">
                        <div class="prod-toolbar-left">
                            <span class="prod-chip blue"><i class="feather icon-archive"></i>
                                {{ $data->brandname ?: 'Hersteller unbekannt' }}</span>
                            <span class="prod-chip gray">EAN: {{ $data->ean ?: '–' }}</span>
                            <span class="prod-chip {{ $statusIsPublished ? 'green' : 'orange' }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="prod-toolbar-right">
                            <span class="prod-chip gray">Kategorie: {{ $data->category ?: '–' }}</span>
                            <span class="prod-chip gray">Gruppe: {{ $data->article_group ?: '–' }}</span>
                        </div>
                    </div>

                    <div class="prod-hero-card mb-2">
                        <div class="prod-media">
                            <div class="prod-media-head">
                                <div>
                                    <div class="prod-media-title">Medien & Dateien</div>
                                    <div class="prod-media-sub">Bilder, Dokumente und technische Unterlagen</div>
                                </div>
                                <div class="prod-actions">
                                    <a href="{{ url('product_create_image/' . $data->id) }}" class="prod-btn-ic primary"
                                        data-toggle="tooltip" title="Bild hinzufügen"><i class="feather icon-image"></i></a>
                                    <a href="{{ url('product_create_document/' . $data->id) }}" class="prod-btn-ic primary"
                                        data-toggle="tooltip" title="Dokument hinzufügen"><i
                                            class="feather icon-file-plus"></i></a>
                                </div>
                            </div>
                            <div class="prod-media-body">
                                <div class="prod-slider-box">
                                    @include('admin.product.product.pages.slider', ['pro_images' => $pro_images])
                                </div>

                                <div class="prod-doc-chips">
                                    @forelse($documents as $doc)
                                        <a href="{{ asset('images/products/document/' . $doc->document) }}" target="_blank"
                                            class="prod-doc-chip">
                                            <i class="feather icon-file-text"></i>
                                            {{ $doc->title ?: $doc->document }}
                                        </a>
                                    @empty
                                        <span class="prod-chip gray">Noch keine Dokumente hinterlegt</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="prod-detail-card">
                            <div class="prod-detail-head">
                                <div>
                                    <h1 class="prod-detail-title">{{ $data->product }}</h1>
                                </div>
                                <span
                                    class="prod-chip {{ $statusIsPublished ? 'green' : 'orange' }}">{{ $statusLabel }}</span>
                            </div>

                            <div class="prod-info-grid">
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Hersteller</div>
                                    <div class="prod-info-value">{{ $data->brandname ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Modell</div>
                                    <div class="prod-info-value">{{ $data->model ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Hersteller Nr.</div>
                                    <div class="prod-info-value">{{ $data->article_no ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">EAN</div>
                                    <div class="prod-info-value">{{ $data->ean ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Farbe</div>
                                    <div class="prod-info-value">{{ $data->color ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Maße / Einheit</div>
                                    <div class="prod-info-value">{{ $data->measurement ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Preiseinheit</div>
                                    <div class="prod-info-value">{{ $data->price_unit ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Packungseinheit</div>
                                    <div class="prod-info-value">{{ $data->package_unit ?: '–' }}</div>
                                </div>
                                <div class="prod-info-item">
                                    <div class="prod-info-label">Kategorie</div>
                                    <div class="prod-info-value">{{ $data->category ?: '–' }}</div>
                                </div>
                            </div>

                            <div class="prod-description">
                                <div class="prod-info-label">Kurzbeschreibung</div>
                                <div class="prod-description-box">
                                    {!! $data->short_description ?: '<span class="text-muted">Noch keine Kurzbeschreibung hinterlegt.</span>' !!}
                                </div>

                                <div class="mt-1">
                                    @if($product_pv || $product_radiator)
                                        <span class="prod-chip blue"><i class="feather icon-sun"></i> PV / Heizkörper
                                            konfiguriert</span>
                                        @if($product_pv)
                                            <span class="prod-chip gray">PV: {{ $product_pv->pv_type ?? '' }} ·
                                                {{ $product_pv->power ?? '' }}</span>
                                        @endif
                                        @if($product_radiator)
                                            <span class="prod-chip gray">Heizkörper: {{ $product_radiator->radiator_type ?? '' }} ·
                                                {{ $product_radiator->power ?? '' }}</span>
                                        @endif
                                    @else
                                        <span class="prod-chip gray">Keine PV/Heizkörper-Konfiguration</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="prod-tabs-wrap">
                        <ul class="nav nav-tabs prod-tabs" id="productTabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" id="tab-overview" data-toggle="tab"
                                    href="#panel-overview" role="tab"><i class="feather icon-grid"></i> Übersicht</a></li>
                            <li class="nav-item"><a class="nav-link" id="tab-tech" data-toggle="tab" href="#panel-tech"
                                    role="tab"><i class="feather icon-cpu"></i> Technische Daten</a></li>
                            <li class="nav-item"><a class="nav-link" id="tab-suppliers" data-toggle="tab"
                                    href="#panel-suppliers" role="tab"><i class="feather icon-truck"></i> Lieferanten &
                                    Preise</a></li>
                            <li class="nav-item"><a class="nav-link" id="tab-docs" data-toggle="tab" href="#panel-docs"
                                    role="tab"><i class="feather icon-file-text"></i> Dokumente</a></li> 
                            <li class="nav-item"><a class="nav-link" id="tab-install" data-toggle="tab"
                                    href="#panel-install" role="tab"><i class="feather icon-clock"></i> Montage</a></li>
                            <li class="nav-item"><a class="nav-link" id="tab-team" data-toggle="tab" href="#panel-team"
                                    role="tab"><i class="feather icon-users"></i> Technisches Team</a></li>
                        </ul>
                    </div>

                    <div class="tab-content" id="productTabsContent">
                        <div class="tab-pane fade show active" id="panel-overview" role="tabpanel">
                            <div class="prod-panel-card">
                                <div class="prod-panel-head">
                                    <div>
                                        <h5 class="prod-panel-title">Technische Highlights</h5>
                                        <div class="prod-panel-sub">Wichtige Eigenschaften auf einen Blick.</div>
                                    </div>
                                    <button type="button" class="prod-btn-soft js-open-tech-tab" data-open-add="0">
                                        <i class="feather icon-cpu"></i> Technische Daten bearbeiten
                                    </button>
                                </div>

                                @if($descriptionsCol->count())
                                    <div class="prod-card mb-0">
                                        <div class="prod-list-head two">
                                            <div>Feld</div>
                                            <div>Beschreibung</div>
                                            <div>Status</div>
                                        </div>
                                        <div class="prod-list">
                                            @foreach($descriptionsCol->take(6) as $descript)
                                                <div class="prod-item">
                                                    <div class="prod-item-row two">
                                                        <div>
                                                            <div class="prod-cell-title">Feld</div>
                                                            <div class="prod-main-title">{{ $descript->field }}</div>
                                                        </div>
                                                        <div>
                                                            <div class="prod-cell-title">Beschreibung</div>
                                                            <div class="prod-main-sub">{{ Str::limit($descript->description, 140) }}
                                                            </div>@if($descript->remark)
                                                            <div class="prod-main-sub">{{ $descript->remark }}</div>@endif
                                                        </div>
                                                        <div>
                                                            <div class="prod-cell-title">Status</div><span
                                                                class="prod-chip gray">{{ $descript->status ?: '–' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="prod-empty">Noch keine technischen Details hinterlegt.</div>
                                @endif
                            </div>
                        </div>

                        <div class="tab-pane fade" id="panel-tech" role="tabpanel">
                            <div class="prod-panel-card" id="product-tech-tab-card" data-product-id="{{ $data->id }}">
                                <div class="prod-panel-head">
                                    <div>
                                        <h5 class="prod-panel-title">Technische Beschreibung</h5>
                                        <div class="prod-panel-sub">Alle technischen Felder mit Bearbeitung direkt im Tab.
                                        </div>
                                    </div>
                                    <button type="button" class="prod-btn" id="btn-open-add-tech-modal"><i
                                            class="feather icon-plus"></i> Hinzufügen</button>
                                </div>

                                <div class="prod-table-wrap">
                                    <table class="table table-sm prod-table" id="tech-description-table">
                                        <tbody>
                                            @forelse($descriptions as $descript)
                                                <tr id="tech-row-{{ $descript->id }}">
                                                    <th style="width:30%;">{{ $descript->field }}</th>
                                                    <td>
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="pr-1">
                                                                <strong>{{ $descript->description }}</strong>
                                                                @if($descript->remark)
                                                                <div class="text-muted">{{ $descript->remark }}</div>@endif
                                                                @if($descript->status)<span
                                                                class="prod-chip gray mt-50">{{ $descript->status }}</span>@endif
                                                            </div>
                                                            <div class="pl-1 text-nowrap">
                                                                <button type="button" class="prod-btn-ic primary btn-edit-tech"
                                                                    data-id="{{ $descript->id }}"
                                                                    data-field="{{ $descript->field }}"
                                                                    data-description="{{ $descript->description }}"
                                                                    data-remark="{{ $descript->remark }}"
                                                                    data-status="{{ $descript->status }}"><i
                                                                        class="feather icon-edit-2"></i></button>
                                                                <button type="button" class="prod-btn-ic danger btn-delete-tech"
                                                                    data-id="{{ $descript->id }}"><i
                                                                        class="feather icon-trash-2"></i></button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr id="tech-empty-row">
                                                    <td colspan="2" class="text-muted">Keine technischen Beschreibungen
                                                        hinterlegt.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-1 d-flex justify-content-between align-items-center flex-wrap"
                                    style="gap:10px;">
                                    <span class="prod-chip gray" id="tech-count-label">{{ $techCount }} Einträge</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="panel-suppliers" role="tabpanel">
                            <div class="prod-panel-card" id="supplier-panel" data-product-id="{{ $data->id }}"
                                data-url-load="{{ route('products.suppliers.data', $data->id) }}"
                                data-url-store="{{ route('products.distributor-prices.store', $data->id) }}">
                                <div class="prod-panel-head">
                                    <div>
                                        <h5 class="prod-panel-title"><i class="feather icon-truck mr-50"></i>Lieferanten &
                                            Preise</h5>
                                        <div class="prod-panel-sub">Einkaufs- und Rabattkonditionen pro Lieferant
                                            hinterlegen.</div>
                                    </div>
                                    <span class="prod-chip blue">Produkt-ID #{{ $data->id }}</span>
                                </div>

                                <form id="supplier-price-form">
                                    @csrf
                                    <div class="prod-toolbar mb-1">
                                        <label class="mb-0 d-flex align-items-center" style="gap:.5rem;cursor:pointer;">
                                            <input type="checkbox" id="supplier-advanced-toggle" class="mr-25">
                                            <span class="font-weight-bold">Erweitert</span>
                                            <small class="text-muted">UVP, Rabatte, Gruppe, Datum, Status</small>
                                        </label>
                                        <small class="text-muted">Standardansicht: EK + Art.Nr + Verfügbarkeit</small>
                                    </div>

                                    <div class="prod-form-grid supplier-form-row">
                                        <div class="prod-field lg">
                                            <label class="supplier-label"><i class="feather icon-user"></i>
                                                Lieferant</label>
                                            <select name="distributor_id" id="supplier_distributor_id" class="form-control">
                                                <option value="">– Lieferant auswählen –</option>
                                            </select>
                                            <small class="text-muted d-block mt-25">Fehlt der Lieferant? Legen Sie ihn
                                                zuerst im Lieferantenmodul an.</small>
                                        </div>

                                        <div class="prod-field sm">
                                            <label class="supplier-label"><i class="feather icon-shopping-cart"></i>
                                                Einkaufspreis</label>
                                            <div class="input-group supplier-input-group">
                                                <div class="input-group-prepend"><span class="input-group-text">€</span>
                                                </div><input type="number" step="0.01" name="purchase_price"
                                                    id="sp_purchase_price" class="form-control" placeholder="0,00">
                                            </div>
                                        </div>

                                        <div class="prod-field sm">
                                            <label class="supplier-label"><i class="feather icon-hash"></i> Art.Nr.</label>
                                            <input type="text" name="article_no" id="sp_article_no" class="form-control">
                                        </div>

                                        <div class="prod-field lg">
                                            <label class="supplier-label"><i class="feather icon-package"></i>
                                                Verfügbarkeit</label>
                                            <input type="text" name="availability" id="sp_availability" class="form-control"
                                                placeholder="z.B. Lagernd, 2-3 Wochen, auf Anfrage">
                                        </div>

                                        <div class="prod-field sm supplier-advanced-only">
                                            <label class="supplier-label"><i class="feather icon-dollar-sign"></i> UVP
                                                (€)</label>
                                            <div class="input-group supplier-input-group">
                                                <div class="input-group-prepend"><span class="input-group-text">€</span>
                                                </div><input type="number" step="0.01" name="price" id="sp_price"
                                                    class="form-control" placeholder="0,00">
                                            </div>
                                        </div>

                                        <div class="prod-field sm supplier-advanced-only">
                                            <label class="supplier-label"><i class="feather icon-percent"></i> Rabatt
                                                %</label>
                                            <div class="input-group supplier-input-group"><input type="number"
                                                    name="discount_percent" id="sp_discount_percent" class="form-control"
                                                    min="0" max="100" placeholder="20">
                                                <div class="input-group-append"><span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="prod-field sm supplier-advanced-only">
                                            <label class="supplier-label"><i class="feather icon-arrow-down-right"></i>
                                                Rabatt €</label>
                                            <div class="input-group supplier-input-group">
                                                <div class="input-group-prepend"><span class="input-group-text">€</span>
                                                </div><input type="number" step="0.01" name="discount_price"
                                                    id="sp_discount_price" class="form-control" placeholder="0,00">
                                            </div>
                                        </div>

                                        <div class="prod-field sm supplier-advanced-only">
                                            <label class="supplier-label"><i class="feather icon-calendar"></i>
                                                Datum</label>
                                            <input type="date" name="price_date" class="form-control"
                                                value="{{ now()->toDateString() }}">
                                        </div>

                                        <div class="prod-field lg supplier-advanced-only">
                                            <label class="supplier-label"><i class="feather icon-layers"></i>
                                                Rabattgruppe</label>
                                            <select name="discount_group_id" id="supplier_discount_group_id"
                                                class="form-control">
                                                <option value="">– keine –</option>
                                            </select>
                                        </div>

                                        <div class="prod-field sm supplier-advanced-only">
                                            <label class="supplier-label"><i class="feather icon-activity"></i>
                                                Status</label>
                                            <select name="status" class="form-control">
                                                <option value="Published">Aktiv</option>
                                                <option value="Unpublished">Inaktiv</option>
                                            </select>
                                        </div>

                                        <div class="prod-field sm">
                                            <label class="supplier-label">&nbsp;</label>
                                            <button type="submit" class="prod-btn w-100" id="supplier-save-btn"><span
                                                    class="spinner-border spinner-border-sm mr-25 d-none"
                                                    id="supplier-save-spinner"></span><i class="feather icon-save"></i>
                                                Speichern</button>
                                        </div>
                                    </div>

                                    <div class="supplier-calc-info mt-1 supplier-advanced-only" id="supplier-calc-info"
                                        style="display:none;">
                                        <div class="supplier-calc-pill"><i class="feather icon-info"></i><span
                                                id="supplier-calc-text"></span></div>
                                    </div>
                                    <div id="supplier-price-errors" class="text-danger mt-1"
                                        style="font-size:.9rem;display:none;"></div>
                                </form>

                                <div class="mt-2">
                                    <p class="text-muted" id="supplier-prices-empty">Es sind noch keine Lieferantenpreise
                                        für dieses Produkt hinterlegt.</p>
                                    <div class="prod-table-wrap d-none" id="supplier-prices-table-wrapper">
                                        <table class="table table-sm prod-table price-table">
                                            <thead>
                                                <tr>
                                                    <th>Art.Nr.</th>
                                                    <th>Lieferant</th>
                                                    <th class="supplier-col-advanced-only">UVP</th>
                                                    <th class="supplier-col-advanced-only">Rabatt €</th>
                                                    <th class="supplier-col-advanced-only">Rabatt %</th>
                                                    <th>EK</th>
                                                    <th class="supplier-col-advanced-only">Datum</th>
                                                    <th>Verfügbarkeit</th>
                                                    <th class="text-right" style="width:120px;">Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody id="supplier-prices-tbody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="panel-docs" role="tabpanel">
                            <div class="prod-panel-card">
                                <div class="prod-panel-head">
                                    <div>
                                        <h5 class="prod-panel-title">Dokumente & technische Unterlagen</h5>
                                        <div class="prod-panel-sub">Alle zum Produkt hinterlegten Dateien.</div>
                                    </div><a href="{{ url('product_create_document/' . $data->id) }}" class="prod-btn"><i
                                            class="feather icon-file-plus"></i> Dokument hinzufügen</a>
                                </div>
                                <div class="prod-card mb-0">
                                    <div class="prod-list-head docs">
                                        <div>Titel</div>
                                        <div>Datei</div>
                                        <div>Typ</div>
                                        <div style="text-align:right;">Aktion</div>
                                    </div>
                                    <div class="prod-list">
                                        @forelse($documents as $doc)
                                            @php $ext = pathinfo($doc->document, PATHINFO_EXTENSION); @endphp
                                            <div class="prod-item">
                                                <div class="prod-item-row docs">
                                                    <div>
                                                        <div class="prod-cell-title">Titel</div>
                                                        <div class="prod-main-title">{{ $doc->title ?: '—' }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="prod-cell-title">Datei</div>
                                                        <div class="prod-main-sub">{{ $doc->document }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="prod-cell-title">Typ</div><span
                                                            class="prod-chip gray">{{ strtoupper($ext) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="prod-cell-title">Aktion</div>
                                                        <div class="prod-actions"><a
                                                                href="{{ asset('images/products/document/' . $doc->document) }}"
                                                                target="_blank" class="prod-btn-ic"><i
                                                                    class="feather icon-eye"></i></a><a
                                                                href="{{ asset('images/products/document/' . $doc->document) }}"
                                                                download class="prod-btn-ic primary"><i
                                                                    class="feather icon-download"></i></a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="prod-empty">Keine Dokumente hinterlegt.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="panel-inventory" role="tabpanel">
                            <div class="prod-panel-card">
                                <div class="prod-panel-head">
                                    <div>
                                        <h5 class="prod-panel-title">Inventar</h5>
                                        <div class="prod-panel-sub">Seriennummern, Lagerorte und Bestände.</div>
                                    </div>
                                </div>
                                <form id="inventoryForm" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $data->id }}">
                                    <div class="prod-form-grid">
                                        <div class="prod-field"><label class="prod-label">Serien-Nr.</label><input
                                                type="text" name="serial_no" class="form-control"></div>
                                        <div class="prod-field"><label class="prod-label">Artikel-Nr.</label><input
                                                type="text" name="article_no" class="form-control"
                                                value="{{ $data->article_no }}"></div>
                                        <div class="prod-field sm"><label class="prod-label">EAN</label><input type="text"
                                                name="ean" class="form-control" value="{{ $data->ean }}"></div>
                                        <div class="prod-field sm"><label class="prod-label">Lagerort</label><input
                                                type="text" name="location" class="form-control"></div>
                                        <div class="prod-field sm"><label class="prod-label">Menge</label><input
                                                type="number" name="quantity" class="form-control" min="1" value="1"></div>
                                        <div class="prod-field sm"><label class="prod-label">&nbsp;</label><button
                                                type="submit" class="prod-btn w-100"><i class="feather icon-save"></i>
                                                Hinzufügen</button></div>
                                    </div>
                                </form>
                                <div class="prod-table-wrap">
                                    <table class="table table-sm prod-table inventory-table" id="inventoryTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Serien-Nr.</th>
                                                <th>Artikel-Nr.</th>
                                                <th>EAN</th>
                                                <th>Lagerort</th>
                                                <th>Menge</th>
                                                <th>Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="panel-install" role="tabpanel">
                            <div class="prod-panel-card">
                                <div class="prod-panel-head">
                                    <div>
                                        <h5 class="prod-panel-title">Geschätzte Montagezeit</h5>
                                        <div class="prod-panel-sub">Fälle und Aufwandseinschätzung.</div>
                                    </div><a href="{{ url('/product_installation/' . $data->id) }}" class="prod-btn-soft"><i
                                            class="feather icon-edit"></i> Bearbeiten</a>
                                </div>
                                <ul class="timeline">
                                    @forelse($installation as $install)
                                        @php $markerClass = $install->rate >= 5 ? 'danger' : ($install->rate >= 3 ? 'warning' : 'success'); @endphp
                                        <li class="timeline-item"><span class="timeline-marker {{ $markerClass }}"></span>
                                            <div class="timeline-title">{{ $install->case }}</div>
                                            <div class="timeline-meta">Aufwand: {{ $install->rate }}</div>
                                            <div class="mt-25">{{ Str::limit($install->description, 160) }}</div>
                                        </li>
                                    @empty
                                        <li class="timeline-item"><span class="timeline-marker"></span>
                                            <div class="timeline-title text-muted">Keine Montagefälle hinterlegt.</div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="panel-team" role="tabpanel">
                            <div class="prod-panel-card">
                                <div class="prod-panel-head">
                                    <div>
                                        <h5 class="prod-panel-title">Technisches Team</h5>
                                        <div class="prod-panel-sub">Kompetenzen je Mitarbeiter und Dienst.</div>
                                    </div>
                                </div>
                                @if($technical_person->count())
                                    <div class="prod-table-wrap mb-2">
                                        <table class="table table-sm prod-table">
                                            <thead>
                                                <tr>
                                                    <th>Person</th>
                                                    <th>Beratung</th>
                                                    <th>Planung</th>
                                                    <th>Kalkulation</th>
                                                    <th>Montage</th>
                                                    <th>Projektierung</th>
                                                    <th>Bauleitung</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($technical_person as $tech1)
                                                    <tr>
                                                        <td><a href="{{ url('next_employee/' . $tech1->empid) }}"
                                                                class="d-flex align-items-center"
                                                                style="gap:10px;color:inherit;text-decoration:none;"><img
                                                                    src="{{ asset('images/employee/' . $tech1->image) }}"
                                                                    alt="Avatar" class="prod-avatar"><strong>{{ $tech1->empname }}
                                                                    {{ $tech1->lastname }}</strong></a></td>
                                                        @foreach (['advice', 'plan', 'calculation', 'montage', 'project_planing', 'site_management'] as $field)
                                                            <td>
                                                                <div class="tech-stars">@for($i = 1; $i <= $tech1->$field; $i++)<i
                                                                class="fa fa-star"></i>@endfor</div>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="prod-table-wrap">
                                        <table class="table table-sm prod-table">
                                            <tbody>
                                                @php $categories = ['advice' => 'Beratung', 'plan' => 'Planung', 'calculation' => 'Kalkulation', 'montage' => 'Montage', 'project_planing' => 'Projektierung', 'site_management' => 'Bauleitung']; @endphp
                                                @foreach($categories as $key => $label)
                                                    <tr>
                                                        <th style="width:22%;">{{ $label }}</th>
                                                        <td>
                                                            <ul class="list-unstyled users-list d-flex align-items-center mb-0">
                                                                @foreach($technical_person as $tech2)
                                                                    @if($tech2->$key >= 1)
                                                                        <li class="avatar pull-up mr-50" data-toggle="tooltip"
                                                                            data-placement="bottom"
                                                                            data-original-title="{{ $tech2->empname }} {{ $tech2->lastname }}">
                                                                            <a href="{{ url('next_employee/' . $tech2->empid) }}"><img
                                                                                    class="media-object rounded-circle"
                                                                                    src="{{ asset('images/employee/' . $tech2->image) }}"
                                                                                    alt="Avatar" height="32" width="32"></a>
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="prod-empty">Für dieses Produkt sind noch keine technischen Verantwortlichen
                                        hinterlegt.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== ADD MULTIPLE DESCRIPTIONS MODAL ===================== --}}
    <div class="modal fade" id="technicalDescriptionModal" tabindex="-1" role="dialog"
        aria-labelledby="technicalDescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="technical-description-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $data->id }}">
                <input type="hidden" name="pro_id" value="{{ $data->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="technicalDescriptionModalLabel">Technische Daten hinzufügen</h5><button
                            type="button" class="close" data-dismiss="modal" aria-label="Schließen"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-1"><small class="text-muted">Mit <strong>+</strong> und <strong>−</strong> können
                                mehrere Zeilen ergänzt werden, bevor sie gemeinsam gespeichert werden.</small></div>
                        <div id="td-rows-container">
                            <div class="td-row border rounded p-1 mb-1">
                                <div class="form-row">
                                    <div class="form-group col-md-3"><label>Feld</label><input type="text" name="field[]"
                                            class="form-control" required></div>
                                    <div class="form-group col-md-3"><label>Beschreibung</label><input type="text"
                                            name="description[]" class="form-control"></div>
                                    <div class="form-group col-md-3"><label>Bemerkung</label><input type="text"
                                            name="remark[]" class="form-control"></div>
                                    <div class="form-group col-md-2"><label>Status</label><input type="text" name="status[]"
                                            class="form-control"></div>
                                    <div class="form-group col-md-1 d-flex align-items-end"><button type="button"
                                            class="prod-btn-ic danger td-btn-remove-row"><i
                                                class="feather icon-minus"></i></button></div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="prod-btn-soft" id="td-btn-add-row"><i class="feather icon-plus"></i>
                            Zeile hinzufügen</button>
                    </div>
                    <div class="modal-footer"><button type="button" class="prod-btn-soft"
                            data-dismiss="modal">Abbrechen</button><button type="submit" class="prod-btn">Speichern</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== EDIT SINGLE DESCRIPTION MODAL ===================== --}}
    <div class="modal fade" id="editTechnicalDescriptionModal" tabindex="-1" role="dialog"
        aria-labelledby="editTechnicalDescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="edit-technical-description-form">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-description-id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTechnicalDescriptionModalLabel">Technische Beschreibung bearbeiten
                        </h5><button type="button" class="close" data-dismiss="modal" aria-label="Schließen"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group"><label>Feld</label><input type="text" class="form-control" id="edit-field"
                                required></div>
                        <div class="form-group"><label>Beschreibung</label><input type="text" class="form-control"
                                id="edit-description"></div>
                        <div class="form-group"><label>Bemerkung</label><input type="text" class="form-control"
                                id="edit-remark"></div>
                        <div class="form-group"><label>Status</label><input type="text" class="form-control"
                                id="edit-status"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="prod-btn-soft"
                            data-dismiss="modal">Abbrechen</button><button type="submit" class="prod-btn">Speichern</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== EDIT SUPPLIER PRICE MODAL ===================== --}}
    <div id="editSupplierPriceModal" class="xmodal" aria-hidden="true" aria-labelledby="editSupplierPriceModalLabel"
        role="dialog">
        <div class="xmodal__backdrop" data-xmodal-close></div>

        <div class="xmodal__panel" role="document" tabindex="-1">
            <form id="edit-supplier-price-form" class="xmodal__form">
                @csrf
                @method('PUT')

                <input type="hidden" id="esp_id" value="">

                <div class="xmodal__header">
                    <div class="xmodal__titlewrap">
                        <h5 class="xmodal__title" id="editSupplierPriceModalLabel">Lieferantenpreis bearbeiten</h5>
                        <small class="xmodal__subtitle">Standard: nur EK sichtbar. Für Details „Erweitert“
                            aktivieren.</small>
                    </div>

                    <div class="xmodal__actions">
                        <label class="xswitch" title="Erweiterte Felder ein-/ausblenden">
                            <input type="checkbox" id="esp-advanced-toggle">
                            <span class="xswitch__track"></span>
                            <span class="xswitch__label">Erweitert</span>
                        </label>

                        <button type="button" class="xmodal__close" aria-label="Schließen" data-xmodal-close>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="xmodal__body">
                    <div class="row supplier-form-row">
                        <!-- Lieferant (always) -->
                        <div class="col-lg-4 col-md-6 mb-1">
                            <label class="supplier-label">
                                <i class="feather icon-user mr-25"></i> Lieferant
                            </label>
                            <select id="esp_distributor_id" class="form-control" required>
                                <option value="">– Lieferant auswählen –</option>
                            </select>
                        </div>

                        <!-- Einkaufspreis (EK) (always) -->
                        <div class="col-lg-2 col-md-6 mb-1">
                            <label class="supplier-label">
                                <i class="feather icon-shopping-cart mr-25"></i> Einkaufspreis
                            </label>
                            <div class="input-group input-group supplier-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">€</span>
                                </div>
                                <input type="number" step="0.01" id="esp_purchase_price" class="form-control"
                                    placeholder="0,00">
                            </div>
                        </div>

                        <!-- Advanced fields -->
                        <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
                            <label class="supplier-label"><i class="feather icon-hash mr-25"></i> Art.Nr.</label>
                            <input type="text" id="esp_article_no" class="form-control" value="">
                        </div>

                        <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
                            <label class="supplier-label"><i class="feather icon-dollar-sign mr-25"></i> UVP (€)</label>
                            <div class="input-group input-group supplier-input-group">
                                <div class="input-group-prepend"><span class="input-group-text">€</span></div>
                                <input type="number" step="0.01" id="esp_price" class="form-control" placeholder="0,00">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
                            <label class="supplier-label"><i class="feather icon-percent mr-25"></i> Rabatt %</label>
                            <div class="input-group input-group supplier-input-group">
                                <input type="number" id="esp_discount_percent" class="form-control" min="0" max="100"
                                    placeholder="z.B. 20">
                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
                            <label class="supplier-label"><i class="feather icon-arrow-down-right mr-25"></i> Rabatt
                                €</label>
                            <div class="input-group input-group supplier-input-group">
                                <div class="input-group-prepend"><span class="input-group-text">€</span></div>
                                <input type="number" step="0.01" id="esp_discount_price" class="form-control"
                                    placeholder="0,00">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
                            <label class="supplier-label"><i class="feather icon-calendar mr-25"></i> Datum</label>
                            <input type="date" id="esp_price_date" class="form-control">
                        </div>

                        <div class="col-lg-3 col-md-6 mb-1 esp-advanced d-none">
                            <label class="supplier-label"><i class="feather icon-package mr-25"></i> Verfügbarkeit</label>
                            <input type="text" id="esp_availability" class="form-control"
                                placeholder="z.B. Lagernd, 2-3 Wochen, auf Anfrage">
                        </div>

                        <div class="col-lg-3 col-md-6 mb-1 esp-advanced d-none">
                            <label class="supplier-label"><i class="feather icon-layers mr-25"></i> Rabattgruppe</label>
                            <select id="esp_discount_group_id" class="form-control">
                                <option value="">– keine –</option>
                            </select>
                            <small class="text-muted d-block mt-25" style="font-size:.9rem;">Auswahl übernimmt automatisch
                                Rabatt %.</small>
                        </div>

                        <!-- Status (always) -->
                        <div class="col-lg-2 col-md-6 mb-1">
                            <label class="supplier-label"><i class="feather icon-activity mr-25"></i> Status</label>
                            <select id="esp_status" class="form-control">
                                <option value="Published">Aktiv</option>
                                <option value="Unpublished">Inaktiv</option>
                            </select>
                        </div>
                    </div>

                    <!-- calc info (advanced only) -->
                    <div class="supplier-calc-info mt-25 esp-advanced d-none" id="esp-calc-info" style="display:none;">
                        <div class="supplier-calc-pill">
                            <i class="feather icon-info mr-25"></i>
                            <span id="esp-calc-text"></span>
                        </div>
                    </div>

                    <div id="esp-errors" class="text-danger mt-25" style="font-size:.95rem; display:none;"></div>
                </div>

                <div class="xmodal__footer">
                    <button type="button" class="btn btn-outline-secondary" data-xmodal-close>Abbrechen</button>
                    <button type="submit" class="btn btn-primary" id="esp-save-btn">
                        <span class="spinner-border spinner-border-sm mr-25 d-none" id="esp-save-spinner"></span>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>




@endsection


@section('script')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        (function ($) {
            'use strict';

            const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content') || '';
            const PRODUCT_ID = {{ $data->id }};

            const WF = {
                inv: { initialized: false },
                suppliers: {
                    loaded: false,
                    cache: {},        // id -> price object
                    advanced: false   // TAB advanced (default OFF)
                }
            };

            /* =========================================================
             * XMODAL (no Bootstrap JS)
             * ======================================================= */
            const xmodal = (() => {
                const state = { lastFocus: null };

                const qs = (sel, root = document) => root.querySelector(sel);
                const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

                function setModalAdvanced(modalEl, on) {
                    qsa('.esp-advanced', modalEl).forEach(n => n.classList.toggle('d-none', !on));
                    const calc = qs('#esp-calc-info', modalEl);
                    if (!on && calc) calc.style.display = 'none';
                }

                function open(id) {
                    const el = document.getElementById(id);
                    if (!el) return;

                    state.lastFocus = document.activeElement;

                    el.classList.add('is-open');
                    el.setAttribute('aria-hidden', 'false');

                    // default advanced OFF on every open
                    const adv = qs('#esp-advanced-toggle', el);
                    if (adv) adv.checked = false;
                    setModalAdvanced(el, false);

                    const panel = qs('.xmodal__panel', el);
                    if (panel) panel.focus();

                    document.body.style.overflow = 'hidden';
                }

                function close(id) {
                    const el = document.getElementById(id);
                    if (!el) return;

                    el.classList.remove('is-open');
                    el.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';

                    if (state.lastFocus && typeof state.lastFocus.focus === 'function') {
                        state.lastFocus.focus();
                    }
                }

                function bindOnce() {
                    qsa('.xmodal').forEach(el => {
                        qsa('[data-xmodal-close]', el).forEach(btn => btn.addEventListener('click', () => close(el.id)));
                        const backdrop = qs('.xmodal__backdrop', el);
                        if (backdrop) backdrop.addEventListener('click', () => close(el.id));

                        const adv = qs('#esp-advanced-toggle', el);
                        if (adv) adv.addEventListener('change', () => setModalAdvanced(el, !!adv.checked));
                    });

                    document.addEventListener('keydown', (e) => {
                        if (e.key !== 'Escape') return;
                        const openEl = document.querySelector('.xmodal.is-open');
                        if (openEl) close(openEl.id);
                    });
                }

                if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bindOnce);
                else bindOnce();

                return { open, close };
            })();

            /* =========================================================
             * HELPERS
             * ======================================================= */
            function escapeHtml(s) {
                return String(s == null ? '' : s)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Parse supports "12,3" and "12.3" (NO formatting while typing)
            function toFiniteNumber(val) {
                if (val === undefined || val === null) return null;
                if (typeof val === 'number') return Number.isFinite(val) ? val : null;

                let s = String(val).trim();
                if (s === '') return null;
                s = s.replace(',', '.');

                const n = parseFloat(s);
                return Number.isFinite(n) ? n : null;
            }

            function formatNumber(val, decimals) {
                const n = toFiniteNumber(val);
                if (n === null) return '';
                return n.toFixed(decimals);
            }

            function money(val, decimals) {
                const txt = formatNumber(val, decimals);
                return txt ? (txt + ' €') : '';
            }

            function percent(val) {
                const txt = formatNumber(val, 0);
                return txt ? (txt + ' %') : '';
            }

            function ajaxErrorToHtml(xhr) {
                if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let html = '<ul class="mb-0">';
                    $.each(xhr.responseJSON.errors, function (_field, messages) {
                        (messages || []).forEach(function (msg) {
                            html += '<li>' + escapeHtml(msg) + '</li>';
                        });
                    });
                    html += '</ul>';
                    return html;
                }
                return null;
            }

            function safeJsonFromJqXHR(xhr) {
                if (!xhr) return null;
                if (xhr.responseJSON) return xhr.responseJSON;
                try { return JSON.parse(xhr.responseText || '{}'); } catch (_e) { return null; }
            }

            function setBtnLoading($btn, $spinner, loading) {
                if ($spinner && $spinner.length) $spinner.toggleClass('d-none', !loading);
                if ($btn && $btn.length) $btn.prop('disabled', !!loading);
            }

            function ensureSelect2($el) {
                if ($.fn.select2 && $el && $el.length && !$el.hasClass('select2-hidden-accessible')) {
                    $el.select2({ width: '100%', placeholder: 'Bitte auswählen', allowClear: true });
                }
            }

            /* =========================================================
             * TAB ADVANCED TOGGLE (default OFF)
             * IMPORTANT: Blade now uses:
             *   - advanced-only fields: .supplier-advanced-only
             *   - advanced-only columns: .supplier-col-advanced-only
             *   - ALWAYS visible: EK + Art.Nr + Verfügbarkeit + Lieferant
             * ======================================================= */
            function setSupplierAdvanced(on) {
                WF.suppliers.advanced = !!on;
                $('.supplier-advanced-only').toggleClass('d-none', !WF.suppliers.advanced);
                $('.supplier-col-advanced-only').toggleClass('d-none', !WF.suppliers.advanced);
                if (!WF.suppliers.advanced) $('#supplier-calc-info').hide();
            }

            function applySupplierAdvancedDefault() {
                const $t = $('#supplier-advanced-toggle');
                if ($t.length) $t.prop('checked', false);
                setSupplierAdvanced(false);
            }

            /* =========================================================
             * INVENTORY
             * ======================================================= */
            function initInventoryTable() {
                if (WF.inv.initialized) return;
                WF.inv.initialized = true;

                if (!$.fn.DataTable) return;

                $('#inventoryTable').DataTable({
                    ajax: '/ajax/inventory/list/' + PRODUCT_ID,
                    columns: [
                        { data: null, render: function (_d, _t, _r, meta) { return meta.row + 1; } },
                        { data: 'serial_no' },
                        { data: 'article_no' },
                        { data: 'ean' },
                        { data: 'location' },
                        { data: 'quantity' },
                        {
                            data: 'id',
                            render: function (id) {
                                return '' +
                                    '<button class="btn btn-sm btn-outline-danger" onclick="deleteInventoryItem(' + id + ')">' +
                                    '<i class="feather icon-trash-2"></i>' +
                                    '</button>';
                            }
                        }
                    ],
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json' }
                });
            }

            window.deleteInventoryItem = function (id) {
                if (!confirm('Diesen Inventareintrag wirklich löschen?')) return;

                $.ajax({
                    url: '/ajax/inventory/delete/' + id,
                    type: 'DELETE',
                    data: { _token: CSRF_TOKEN },
                    success: function (res) {
                        toastr.success((res && res.message) ? res.message : 'Eintrag gelöscht');
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#inventoryTable')) {
                            $('#inventoryTable').DataTable().ajax.reload();
                        }
                    },
                    error: function () {
                        toastr.error('Löschen fehlgeschlagen.');
                    }
                });
            };

            function initInventoryForm() {
                const $form = $('#inventoryForm');
                if (!$form.length) return;

                $form.off('submit.inv').on('submit.inv', function (e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    formData.set('_token', CSRF_TOKEN);

                    $.ajax({
                        url: "{{ route('ajax.inventory.store') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (res) {
                            toastr.success((res && res.message) ? res.message : 'Inventar gespeichert');
                            $form[0].reset();

                            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#inventoryTable')) {
                                $('#inventoryTable').DataTable().ajax.reload();
                            }
                        },
                        error: function () {
                            toastr.error('Fehler beim Speichern des Inventars.');
                        }
                    });
                });
            }

            /* =========================================================
             * TECHNICAL DATA TAB + MODALS
             * ======================================================= */
            const TECH_URLS = {
                store: "{{ route('product.description.store.ajax') }}",
                list: "{{ url('/product/description/get') }}/" + PRODUCT_ID,
                update: "{{ url('/product/description/update') }}/:id",
                destroy: "{{ url('/product/description/delete') }}/:id"
            };

            function openBsModal(selector) {
                const $modal = $(selector);
                if (!$modal.length) return;

                if ($.fn.modal) {
                    $modal.modal('show');
                    return;
                }

                $modal.addClass('show').css('display', 'block').attr('aria-hidden', 'false');
                $('body').addClass('modal-open');
            }

            function closeBsModal(selector) {
                const $modal = $(selector);
                if (!$modal.length) return;

                if ($.fn.modal) {
                    $modal.modal('hide');
                    return;
                }

                $modal.removeClass('show').css('display', 'none').attr('aria-hidden', 'true');
                $('body').removeClass('modal-open');
            }

            function activateProductTab(target, openAddModal) {
                if (!target) return;

                const $link = $('#productTabs a[href="' + target + '"]');
                const $panel = $(target);

                if (!$link.length || !$panel.length) return;

                if ($.fn.tab) {
                    $link.tab('show');
                } else {
                    $('#productTabs .nav-link').removeClass('active').attr('aria-selected', 'false');
                    $link.addClass('active').attr('aria-selected', 'true');

                    $('#productTabsContent .tab-pane').removeClass('show active');
                    $panel.addClass('show active');
                }

                const tabsWrap = document.querySelector('.prod-tabs-wrap') || document.getElementById('productTabs');
                if (tabsWrap && tabsWrap.scrollIntoView) {
                    tabsWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                if (openAddModal) {
                    window.setTimeout(function () {
                        openBsModal('#technicalDescriptionModal');
                    }, 180);
                }
            }

            function bindProductTabsFallback() {
                $('#productTabs a[data-toggle="tab"]')
                    .off('click.productTabsFallback')
                    .on('click.productTabsFallback', function (e) {
                        if ($.fn.tab) return;

                        e.preventDefault();
                        activateProductTab($(this).attr('href'), false);
                    });

                $('.js-open-tech-tab')
                    .off('click.openTechTab')
                    .on('click.openTechTab', function (e) {
                        e.preventDefault();
                        activateProductTab('#panel-tech', String($(this).data('open-add')) === '1');
                    });
            }

            function tdBlankRowHtml() {
                return '' +
                    '<div class="td-row border rounded p-1 mb-1">' +
                    '  <div class="form-row">' +
                    '    <div class="form-group col-md-3"><label>Feld</label><input type="text" name="field[]" class="form-control" required></div>' +
                    '    <div class="form-group col-md-3"><label>Beschreibung</label><input type="text" name="description[]" class="form-control"></div>' +
                    '    <div class="form-group col-md-3"><label>Bemerkung</label><input type="text" name="remark[]" class="form-control"></div>' +
                    '    <div class="form-group col-md-2"><label>Status</label><input type="text" name="status[]" class="form-control"></div>' +
                    '    <div class="form-group col-md-1 d-flex align-items-end"><button type="button" class="prod-btn-ic danger td-btn-remove-row"><i class="feather icon-minus"></i></button></div>' +
                    '  </div>' +
                    '</div>';
            }

            function normalizeDescriptionsResponse(res) {
                if ($.isArray(res)) return res;
                if (res && $.isArray(res.descriptions)) return res.descriptions;
                if (res && $.isArray(res.data)) return res.data;
                if (res && res.data && $.isArray(res.data.descriptions)) return res.data.descriptions;
                if (res && res.description && res.description.id) return [res.description];
                return [];
            }

            function techRowHtml(item) {
                const id = item.id || item.description_id || '';
                const field = item.field || '';
                const description = item.description || '';
                const remark = item.remark || '';
                const status = item.status || '';

                return '' +
                    '<tr id="tech-row-' + escapeHtml(id) + '">' +
                    '  <th style="width:30%;">' + escapeHtml(field) + '</th>' +
                    '  <td>' +
                    '    <div class="d-flex justify-content-between align-items-start">' +
                    '      <div class="pr-1">' +
                    '        <strong>' + escapeHtml(description) + '</strong>' +
                    (remark ? '<div class="text-muted">' + escapeHtml(remark) + '</div>' : '') +
                    (status ? '<span class="prod-chip gray mt-50">' + escapeHtml(status) + '</span>' : '') +
                    '      </div>' +
                    '      <div class="pl-1 text-nowrap">' +
                    '        <button type="button" class="prod-btn-ic primary btn-edit-tech"' +
                    '          data-id="' + escapeHtml(id) + '"' +
                    '          data-field="' + escapeHtml(field) + '"' +
                    '          data-description="' + escapeHtml(description) + '"' +
                    '          data-remark="' + escapeHtml(remark) + '"' +
                    '          data-status="' + escapeHtml(status) + '"><i class="feather icon-edit-2"></i></button>' +
                    '        <button type="button" class="prod-btn-ic danger btn-delete-tech" data-id="' + escapeHtml(id) + '"><i class="feather icon-trash-2"></i></button>' +
                    '      </div>' +
                    '    </div>' +
                    '  </td>' +
                    '</tr>';
            }

            function renderTechnicalDescriptions(items) {
                const list = items || [];
                const $tbody = $('#tech-description-table tbody');
                $tbody.empty();

                if (!list.length) {
                    $tbody.append('<tr id="tech-empty-row"><td colspan="2" class="text-muted">Keine technischen Beschreibungen hinterlegt.</td></tr>');
                } else {
                    list.forEach(function (item) {
                        $tbody.append(techRowHtml(item));
                    });
                }

                $('#tech-count-label').text(list.length + ' Einträge');
            }

            function loadTechnicalDescriptions() {
                return $.ajax({
                    url: TECH_URLS.list,
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    success: function (res) {
                        renderTechnicalDescriptions(normalizeDescriptionsResponse(res));
                    }
                });
            }

            function bindTechnicalDescriptionUi() {
                $('#btn-open-add-tech-modal')
                    .off('click.techAddOpen')
                    .on('click.techAddOpen', function () {
                        openBsModal('#technicalDescriptionModal');
                    });

                $(document)
                    .off('click.techModalClose', '#technicalDescriptionModal [data-dismiss="modal"], #technicalDescriptionModal [data-bs-dismiss="modal"], #editTechnicalDescriptionModal [data-dismiss="modal"], #editTechnicalDescriptionModal [data-bs-dismiss="modal"], #technicalDescriptionModal .close, #editTechnicalDescriptionModal .close')
                    .on('click.techModalClose', '#technicalDescriptionModal [data-dismiss="modal"], #technicalDescriptionModal [data-bs-dismiss="modal"], #editTechnicalDescriptionModal [data-dismiss="modal"], #editTechnicalDescriptionModal [data-bs-dismiss="modal"], #technicalDescriptionModal .close, #editTechnicalDescriptionModal .close', function (e) {
                        if ($.fn.modal) return;
                        e.preventDefault();
                        const $modal = $(this).closest('.modal');
                        if ($modal.length) closeBsModal('#' + $modal.attr('id'));
                    });

                $('#td-btn-add-row')
                    .off('click.techAddRow')
                    .on('click.techAddRow', function () {
                        $('#td-rows-container').append(tdBlankRowHtml());
                    });

                $(document)
                    .off('click.techRemoveRow', '.td-btn-remove-row')
                    .on('click.techRemoveRow', '.td-btn-remove-row', function () {
                        const $rows = $('#td-rows-container .td-row');
                        if ($rows.length <= 1) {
                            $(this).closest('.td-row').find('input').val('');
                            return;
                        }
                        $(this).closest('.td-row').remove();
                    });

                $('#technical-description-form')
                    .off('submit.techStore')
                    .on('submit.techStore', function (e) {
                        e.preventDefault();

                        const $form = $(this);
                        const payload = $form.serialize() + '&product_id=' + encodeURIComponent(PRODUCT_ID) + '&pro_id=' + encodeURIComponent(PRODUCT_ID);
                        const $btn = $form.find('[type="submit"]');

                        $btn.prop('disabled', true);

                        $.ajax({
                            url: TECH_URLS.store,
                            type: 'POST',
                            data: payload,
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                            success: function (res) {
                                toastr.success((res && res.message) ? res.message : 'Technische Daten gespeichert.');
                                closeBsModal('#technicalDescriptionModal');
                                $('#td-rows-container').html(tdBlankRowHtml());
                                loadTechnicalDescriptions().fail(function () { window.location.reload(); });
                            },
                            error: function (xhr) {
                                const html = ajaxErrorToHtml(xhr);
                                if (html) toastr.error($(html).text());
                                else toastr.error('Technische Daten konnten nicht gespeichert werden.');
                            },
                            complete: function () {
                                $btn.prop('disabled', false);
                            }
                        });
                    });

                $('#tech-description-table')
                    .off('click.techEdit')
                    .on('click.techEdit', '.btn-edit-tech', function () {
                        const $btn = $(this);
                        $('#edit-description-id').val($btn.attr('data-id') || '');
                        $('#edit-field').val($btn.attr('data-field') || '');
                        $('#edit-description').val($btn.attr('data-description') || '');
                        $('#edit-remark').val($btn.attr('data-remark') || '');
                        $('#edit-status').val($btn.attr('data-status') || '');
                        openBsModal('#editTechnicalDescriptionModal');
                    })
                    .off('click.techDelete')
                    .on('click.techDelete', '.btn-delete-tech', function () {
                        const id = $(this).attr('data-id');
                        if (!id) return;
                        if (!confirm('Diese technischen Daten wirklich löschen?')) return;

                        $.ajax({
                            url: TECH_URLS.destroy.replace(':id', encodeURIComponent(id)),
                            type: 'DELETE',
                            data: { _token: CSRF_TOKEN },
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                            success: function (res) {
                                toastr.success((res && res.message) ? res.message : 'Technische Daten gelöscht.');
                                loadTechnicalDescriptions().fail(function () { window.location.reload(); });
                            },
                            error: function () {
                                toastr.error('Technische Daten konnten nicht gelöscht werden.');
                            }
                        });
                    });

                $('#edit-technical-description-form')
                    .off('submit.techUpdate')
                    .on('submit.techUpdate', function (e) {
                        e.preventDefault();

                        const id = $('#edit-description-id').val();
                        if (!id) return;

                        const $btn = $(this).find('[type="submit"]');
                        $btn.prop('disabled', true);

                        $.ajax({
                            url: TECH_URLS.update.replace(':id', encodeURIComponent(id)),
                            type: 'POST',
                            data: {
                                _token: CSRF_TOKEN,
                                product_id: PRODUCT_ID,
                                pro_id: PRODUCT_ID,
                                field: $('#edit-field').val(),
                                description: $('#edit-description').val(),
                                remark: $('#edit-remark').val(),
                                status: $('#edit-status').val()
                            },
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                            success: function (res) {
                                toastr.success((res && res.message) ? res.message : 'Technische Daten aktualisiert.');
                                closeBsModal('#editTechnicalDescriptionModal');
                                loadTechnicalDescriptions().fail(function () { window.location.reload(); });
                            },
                            error: function (xhr) {
                                const html = ajaxErrorToHtml(xhr);
                                if (html) toastr.error($(html).text());
                                else toastr.error('Technische Daten konnten nicht aktualisiert werden.');
                            },
                            complete: function () {
                                $btn.prop('disabled', false);
                            }
                        });
                    });
            }

            /* =========================================================
             * CALC UI
             * RULES:
             *  ✅ advanced OFF: EK typing FREE (no handlers)
             *  ✅ advanced ON: calc runs on blur/change (not input)
             *  ✅ never overwrite the focused input
             * ======================================================= */
            function updateCalcInfo($wrap, $txt, price, discEuro, discPercent, purchase) {
                if (!$wrap || !$wrap.length) return;

                if (price === null && purchase === null && discEuro === null && discPercent === null) {
                    $wrap.hide();
                    return;
                }

                let text = '';
                if (price !== null) text += 'UVP ' + formatNumber(price, 2) + ' €';
                if (discEuro !== null) text += ' – Rabatt ' + formatNumber(discEuro, 2) + ' €';
                if (discPercent !== null) text += ' (' + formatNumber(discPercent, 0) + ' %)';
                if (purchase !== null) text += ' → Einkaufspreis ' + formatNumber(purchase, 2) + ' €';

                $txt.text(text);
                $wrap.show();
            }

            function computeFromValues(price, discPercent, discEuro, purchase) {
                const hasPrice = price !== null && price > 0;
                const hasEuro = discEuro !== null && discEuro >= 0;
                const hasPercent = discPercent !== null && discPercent >= 0;
                const hasPurchase = purchase !== null && purchase > 0;

                if (!hasPrice && !hasEuro && !hasPercent && !hasPurchase) {
                    return { empty: true, ambiguous: false, ekOnly: false, price: null, discEuro: null, discPercent: null, purchase: null };
                }

                // EK-only allowed
                if (hasPurchase && !hasPrice && !hasEuro && !hasPercent) {
                    return { empty: false, ambiguous: false, ekOnly: true, price: null, discEuro: null, discPercent: null, purchase };
                }

                // ambiguous: discount without base
                if (!hasPrice && !hasPurchase && (hasEuro || hasPercent)) {
                    return { empty: false, ambiguous: true, ekOnly: false, price, discEuro, discPercent, purchase };
                }

                if (hasPrice && hasPercent) {
                    discEuro = price * discPercent / 100;
                    purchase = price - discEuro;
                } else if (hasPrice && hasEuro) {
                    discPercent = price > 0 ? (discEuro / price) * 100 : 0;
                    purchase = price - discEuro;
                } else if (hasPrice && hasPurchase) {
                    discEuro = price - purchase;
                    discPercent = price > 0 ? (discEuro / price) * 100 : 0;
                } else if (hasPurchase && hasPercent) {
                    if (discPercent >= 100) discPercent = 99;
                    price = purchase / (1 - discPercent / 100);
                    discEuro = price - purchase;
                } else if (hasPurchase && hasEuro) {
                    price = purchase + discEuro;
                    discPercent = price > 0 ? (discEuro / price) * 100 : 0;
                } else {
                    return { empty: false, ambiguous: true, ekOnly: false, price, discEuro, discPercent, purchase };
                }

                if (discPercent !== null && Number.isFinite(discPercent)) discPercent = Math.round(discPercent);
                return { empty: false, ambiguous: false, ekOnly: false, price, discEuro, discPercent, purchase };
            }

            function bindCalcController(opts) {
                const $price = $(opts.priceSel);
                const $discPercent = $(opts.discPercentSel);
                const $discEuro = $(opts.discEuroSel);
                const $purchase = $(opts.purchaseSel);

                const $wrap = $(opts.wrapSel);
                const $txt = $(opts.txtSel);

                const enabled = () => (typeof opts.enabledFn === 'function' ? !!opts.enabledFn() : true);
                const ns = opts.ns || '.calc';

                function focusedId() {
                    const el = document.activeElement;
                    return el && el.id ? el.id : null;
                }

                function setIfNotFocused($el, id, val) {
                    if (!$el || !$el.length) return;
                    if (!id) return;
                    if (focusedId() === id) return;
                    $el.val(val);
                }

                function recalc() {
                    if (!enabled()) return;

                    const price = toFiniteNumber($price.val());
                    const discPercent = toFiniteNumber($discPercent.val());
                    const discEuro = toFiniteNumber($discEuro.val());
                    const purchase = toFiniteNumber($purchase.val());

                    const r = computeFromValues(price, discPercent, discEuro, purchase);

                    if (r.empty || r.ambiguous) {
                        updateCalcInfo($wrap, $txt, null, null, null, null);
                        return r;
                    }

                    if (r.ekOnly) {
                        updateCalcInfo($wrap, $txt, null, null, null, r.purchase);
                        return r;
                    }

                    setIfNotFocused($price, $price.attr('id'), formatNumber(r.price, 2));
                    setIfNotFocused($discEuro, $discEuro.attr('id'), formatNumber(r.discEuro, 2));
                    setIfNotFocused($discPercent, $discPercent.attr('id'), formatNumber(r.discPercent, 0));
                    setIfNotFocused($purchase, $purchase.attr('id'), formatNumber(r.purchase, 2));

                    updateCalcInfo($wrap, $txt, r.price, r.discEuro, r.discPercent, r.purchase);
                    return r;
                }

                function detachAll() {
                    $price.add($discPercent).add($discEuro).add($purchase).off(ns);
                }

                function attachAdvancedOnly() {
                    $price.add($discPercent).add($discEuro).add($purchase)
                        .on('blur' + ns + ' change' + ns, recalc);

                    $purchase.on('blur' + ns, function () {
                        if (!enabled()) return;
                        const n = toFiniteNumber($purchase.val());
                        if (n !== null) $purchase.val(formatNumber(n, 2));
                    });
                }

                function refresh() {
                    detachAll();
                    if (!enabled()) {
                        updateCalcInfo($wrap, $txt, null, null, null, null);
                        return;
                    }
                    attachAdvancedOnly();
                }

                return { recalc, refresh, detachAll };
            }

            /* =========================================================
             * SUPPLIER TABLE (Blade now has always-visible Art.Nr + Verfügbarkeit)
             * ======================================================= */
            function supplierRowHtml(p) {
                WF.suppliers.cache[p.id] = p;

                return '' +
                    '<tr data-id="' + p.id + '">' +
                    // always visible
                    '<td>' + escapeHtml(p.article_no || '') + '</td>' +
                    '<td>' + escapeHtml(p.distributor_name || '') + '</td>' +

                    // advanced-only columns
                    '<td class="supplier-col-advanced-only">' + money(p.price, 2) + '</td>' +
                    '<td class="supplier-col-advanced-only">' + money(p.discount_price, 2) + '</td>' +
                    '<td class="supplier-col-advanced-only">' + percent(p.discount_percent) + '</td>' +

                    // always visible
                    '<td>' + money(p.purchase_price, 2) + '</td>' +

                    // advanced-only
                    '<td class="supplier-col-advanced-only">' + escapeHtml(p.price_date || '') + '</td>' +

                    // always visible
                    '<td>' + escapeHtml(p.availability || '') + '</td>' +

                    '<td class="text-right">' +
                    (document.getElementById('editSupplierPriceModal')
                        ? '<button type="button" class="btn btn-sm btn-outline-primary mr-25 js-edit-supplier-price" title="Bearbeiten">' +
                        '<i class="feather icon-edit-2"></i>' +
                        '</button>'
                        : ''
                    ) +
                    '<button type="button" class="btn btn-sm btn-outline-danger js-delete-supplier-price" title="Löschen">' +
                    '<i class="feather icon-trash-2"></i>' +
                    '</button>' +
                    '</td>' +
                    '</tr>';
            }

            function renderSupplierTable(prices) {
                const $tbody = $('#supplier-prices-tbody');
                const $empty = $('#supplier-prices-empty');
                const $wrap = $('#supplier-prices-table-wrapper');

                $tbody.empty();
                WF.suppliers.cache = {};

                if (prices && prices.length) {
                    prices.forEach(p => $tbody.append(supplierRowHtml(p)));
                    setSupplierAdvanced(WF.suppliers.advanced);
                    $empty.addClass('d-none');
                    $wrap.removeClass('d-none');
                } else {
                    $empty.removeClass('d-none');
                    $wrap.addClass('d-none');
                }
            }

            function upsertSupplierRow(p) {
                if (!p || !p.id) return;

                WF.suppliers.cache[p.id] = p;

                const $tbody = $('#supplier-prices-tbody');
                const $existing = $tbody.find('tr[data-id="' + p.id + '"]');

                if ($existing.length) $existing.replaceWith(supplierRowHtml(p));
                else $tbody.prepend(supplierRowHtml(p));

                $('#supplier-prices-empty').addClass('d-none');
                $('#supplier-prices-table-wrapper').removeClass('d-none');
                setSupplierAdvanced(WF.suppliers.advanced);
            }

            function fillSelectOptions(res) {
                const dists = (res && res.distributors) || [];
                const groups = (res && res.discountGroups) || [];

                const $dist = $('#supplier_distributor_id');
                $dist.find('option:not(:first)').remove();
                dists.forEach(d => $dist.append($('<option>', { value: d.id, text: d.name })));

                const $dg = $('#supplier_discount_group_id');
                $dg.find('option:not(:first)').remove();
                groups.forEach(g => {
                    $dg.append(
                        $('<option>', { value: g.id, text: g.discount_group + ' (' + g.discount + ' %)' })
                            .attr('data-discount', g.discount)
                    );
                });

                const $edist = $('#esp_distributor_id');
                $edist.find('option:not(:first)').remove();
                dists.forEach(d => $edist.append($('<option>', { value: d.id, text: d.name })));

                const $edg = $('#esp_discount_group_id');
                $edg.find('option:not(:first)').remove();
                groups.forEach(g => {
                    $edg.append(
                        $('<option>', { value: g.id, text: g.discount_group + ' (' + g.discount + ' %)' })
                            .attr('data-discount', g.discount)
                    );
                });
            }

            function pickPrice(res) {
                if (!res) return null;
                if (res.price && res.price.id) return res.price;
                if (res.data && res.data.price && res.data.price.id) return res.data.price;
                if (res.data && res.data.id) return res.data;
                return null;
            }

            /* =========================================================
             * SUPPLIERS PANEL
             * ======================================================= */
            function initSupplierPanel() {
                const $panel = $('#supplier-panel');
                if (!$panel.length) return;

                const loadUrl = $panel.data('url-load');
                const storeUrl = $panel.data('url-store');

                ensureSelect2($('#supplier_distributor_id'));
                ensureSelect2($('#supplier_discount_group_id'));
                ensureSelect2($('#esp_distributor_id'));
                ensureSelect2($('#esp_discount_group_id'));

                applySupplierAdvancedDefault();

                const tabCalc = bindCalcController({
                    ns: '.tabcalc',
                    priceSel: '#sp_price',
                    discPercentSel: '#sp_discount_percent',
                    discEuroSel: '#sp_discount_price',
                    purchaseSel: '#sp_purchase_price',
                    wrapSel: '#supplier-calc-info',
                    txtSel: '#supplier-calc-text',
                    enabledFn: () => document.getElementById('supplier-advanced-toggle')?.checked
                });

                const modalCalc = bindCalcController({
                    ns: '.modalcalc',
                    priceSel: '#esp_price',
                    discPercentSel: '#esp_discount_percent',
                    discEuroSel: '#esp_discount_price',
                    purchaseSel: '#esp_purchase_price',
                    wrapSel: '#esp-calc-info',
                    txtSel: '#esp-calc-text',
                    enabledFn: () => document.getElementById('esp-advanced-toggle')?.checked
                });

                tabCalc.refresh();
                modalCalc.refresh();

                $('#supplier-advanced-toggle')
                    .off('change.supadv')
                    .on('change.supadv', function () {
                        setSupplierAdvanced(this.checked);
                        tabCalc.refresh();
                        if (this.checked) tabCalc.recalc();
                    });

                $(document)
                    .off('change.espadv', '#esp-advanced-toggle')
                    .on('change.espadv', '#esp-advanced-toggle', function () {
                        modalCalc.refresh();
                        if (this.checked) modalCalc.recalc();
                    });

                $('#supplier_discount_group_id')
                    .off('change.dg')
                    .on('change.dg', function () {
                        if (!document.getElementById('supplier-advanced-toggle')?.checked) return;
                        const d = $(this).find('option:selected').data('discount');
                        if (d !== undefined && d !== null && d !== '') {
                            $('#sp_discount_percent').val(d);
                            tabCalc.recalc();
                        }
                    });

                $('#esp_discount_group_id')
                    .off('change.edg')
                    .on('change.edg', function () {
                        if (!document.getElementById('esp-advanced-toggle')?.checked) return;
                        const d = $(this).find('option:selected').data('discount');
                        if (d !== undefined && d !== null && d !== '') {
                            $('#esp_discount_percent').val(d);
                            modalCalc.recalc();
                        }
                    });

                function loadDataOnce() {
                    if (WF.suppliers.loaded) return;
                    WF.suppliers.loaded = true;

                    $.getJSON(loadUrl, function (res) {
                        fillSelectOptions(res);
                        renderSupplierTable(res.prices || []);
                    }).fail(function () {
                        toastr.error('Lieferanten & Preise konnten nicht geladen werden.');
                    });
                }

                $('a[data-toggle="tab"][href="#panel-suppliers"]')
                    .off('shown.bs.tab.sup')
                    .on('shown.bs.tab.sup', loadDataOnce);

                if ($('#panel-suppliers').hasClass('active') || $('#panel-suppliers').hasClass('show')) {
                    loadDataOnce();
                }

                /* --------------------------
                 * ADD submit
                 * IMPORTANT: since Art.Nr + Verfügbarkeit are now always visible,
                 * we must reset by IDs (sp_article_no/sp_availability) not name selectors.
                 * ------------------------ */
                $('#supplier-price-form')
                    .off('submit.sup')
                    .on('submit.sup', function (e) {
                        e.preventDefault();

                        const $form = $(this);
                        const $errors = $('#supplier-price-errors');
                        const $btn = $('#supplier-save-btn');
                        const $spinner = $('#supplier-save-spinner');

                        $errors.hide().empty();
                        setBtnLoading($btn, $spinner, true);

                        $.ajax({
                            url: storeUrl,
                            type: 'POST',
                            data: $form.serialize(),
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                            success: function (res) {
                                const p = pickPrice(res);
                                if (!p) {
                                    console.error('Unexpected store response:', res);
                                    toastr.error('Antwortformat unerwartet (console prüfen).');
                                    return;
                                }

                                upsertSupplierRow(p);
                                toastr.success(res.message || 'Lieferantenpreis gespeichert.');

                                // reset (keep distributor selected or not? -> here we clear all)
                                $('#sp_purchase_price').val('');
                                $('#sp_article_no').val('');
                                $('#sp_availability').val('');

                                // advanced-only fields reset
                                $('#sp_price, #sp_discount_percent, #sp_discount_price').val('');
                                $('#supplier_discount_group_id').val('').trigger('change');

                                $('#supplier-calc-info').hide();
                            },
                            error: function (xhr) {
                                const html = ajaxErrorToHtml(xhr);
                                if (html) $errors.html(html).show();
                                else {
                                    const j = safeJsonFromJqXHR(xhr);
                                    toastr.error((j && j.message) ? j.message : 'Fehler beim Speichern des Lieferantenpreises.');
                                }
                            },
                            complete: function () {
                                setBtnLoading($btn, $spinner, false);
                            }
                        });
                    });

                // DELETE
                $('#supplier-prices-tbody')
                    .off('click.supdel')
                    .on('click.supdel', '.js-delete-supplier-price', function () {
                        const $row = $(this).closest('tr');
                        const id = $row.data('id');
                        if (!id) return;

                        if (!confirm('Diesen Lieferantenpreis wirklich löschen?')) return;

                        $.ajax({
                            url: "{{ route('products.distributor-prices.destroy', ':id') }}".replace(':id', id),
                            type: 'POST',
                            data: { _token: CSRF_TOKEN, _method: 'DELETE' },
                            success: function (res) {
                                delete WF.suppliers.cache[id];
                                $row.remove();

                                if (!$('#supplier-prices-tbody tr').length) {
                                    $('#supplier-prices-empty').removeClass('d-none');
                                    $('#supplier-prices-table-wrapper').addClass('d-none');
                                }
                                toastr.success((res && res.message) ? res.message : 'Preis gelöscht.');
                            },
                            error: function () {
                                toastr.error('Fehler beim Löschen des Lieferantenpreises.');
                            }
                        });
                    });

                // EDIT open modal (unchanged UI expectations)
                $('#supplier-prices-tbody')
                    .off('click.supedit')
                    .on('click.supedit', '.js-edit-supplier-price', function () {
                        const id = $(this).closest('tr').data('id');
                        const p = WF.suppliers.cache[id];
                        if (!p) {
                            toastr.error('Cache fehlt. Bitte Tab neu laden.');
                            return;
                        }

                        $('#esp_id').val(p.id);
                        $('#esp_article_no').val(p.article_no || '');
                        $('#esp_price').val(p.price != null ? formatNumber(p.price, 2) : '');
                        $('#esp_discount_price').val(p.discount_price != null ? formatNumber(p.discount_price, 2) : '');
                        $('#esp_discount_percent').val(p.discount_percent != null ? formatNumber(p.discount_percent, 0) : '');
                        $('#esp_purchase_price').val(p.purchase_price != null ? formatNumber(p.purchase_price, 2) : '');
                        $('#esp_price_date').val(p.price_date || '');
                        $('#esp_availability').val(p.availability || '');
                        $('#esp_status').val(p.status || 'Published');

                        $('#esp_distributor_id').val(p.distributor_id || '').trigger('change');
                        $('#esp_discount_group_id').val(p.discount_group_id || '').trigger('change');

                        $('#esp-errors').hide().empty();

                        xmodal.open('editSupplierPriceModal');
                        modalCalc.refresh();
                    });

                // EDIT submit
                $('#edit-supplier-price-form')
                    .off('submit.supupd')
                    .on('submit.supupd', function (e) {
                        e.preventDefault();

                        const id = $('#esp_id').val();
                        if (!id) return;

                        const $errors = $('#esp-errors');
                        const $btn = $('#esp-save-btn');
                        const $spinner = $('#esp-save-spinner');

                        $errors.hide().empty();
                        setBtnLoading($btn, $spinner, true);

                        const url = "{{ route('products.distributor-prices.update', [':product', ':price']) }}"
                            .replace(':product', PRODUCT_ID)
                            .replace(':price', id);

                        $.ajax({
                            url: url,
                            type: 'POST',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                            data: {
                                _method: 'PUT',
                                distributor_id: $('#esp_distributor_id').val(),
                                article_no: $('#esp_article_no').val(),
                                price: $('#esp_price').val(),
                                discount_percent: $('#esp_discount_percent').val(),
                                discount_price: $('#esp_discount_price').val(),
                                purchase_price: $('#esp_purchase_price').val(),
                                price_date: $('#esp_price_date').val(),
                                availability: $('#esp_availability').val(),
                                discount_group_id: $('#esp_discount_group_id').val(),
                                status: $('#esp_status').val()
                            },
                            success: function (res) {
                                const p = pickPrice(res);
                                if (!p) {
                                    console.error('Unexpected update response:', res);
                                    toastr.error('Antwortformat unerwartet (console prüfen).');
                                    return;
                                }

                                upsertSupplierRow(p);
                                toastr.success(res.message || 'Lieferantenpreis aktualisiert.');
                                xmodal.close('editSupplierPriceModal');
                            },
                            error: function (xhr) {
                                const html = ajaxErrorToHtml(xhr);
                                if (html) $errors.html(html).show();
                                else {
                                    const j = safeJsonFromJqXHR(xhr);
                                    toastr.error((j && j.message) ? j.message : 'Fehler beim Aktualisieren des Lieferantenpreises.');
                                }
                            },
                            complete: function () {
                                setBtnLoading($btn, $spinner, false);
                            }
                        });
                    });
            }

            /* =========================================================
             * INIT
             * ======================================================= */
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();

                $('a[data-toggle="tab"][href="#panel-inventory"]')
                    .off('shown.bs.tab.inv')
                    .on('shown.bs.tab.inv', function () {
                        initInventoryTable();
                    });

                bindProductTabsFallback();
                bindTechnicalDescriptionUi();
                initInventoryForm();
                initSupplierPanel();
            });

        })(jQuery);
    </script>


@endsection