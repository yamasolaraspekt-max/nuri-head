@extends('admin.layouts.app')

@section('title', 'Termine – Übersicht')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

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
            --danger-hover: #dc2626;
            --danger-light: #fef2f2;
            --gray: #6b7280;
            --gray-light: #f3f4f6;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --radius: 14px;
            --radius-lg: 16px;
            --radius-xl: 18px;
            --transition: all .2s ease-in-out;
        }

        body {
            background: var(--app-bg);
        }

        .oc-wrap {
            font-family: Inter, system-ui, -apple-system, sans-serif;
            color: var(--text-main);
        }

        .oc-header {
            margin-bottom: 18px;
        }

        .oc-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .oc-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -.025em;
            color: #111827
        }

        .oc-sub {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px
        }

        .oc-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .oc-breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 700;
        }

        .oc-breadcrumb a:hover {
            color: var(--text-main);
        }

        .oc-breadcrumb span.current {
            color: #111827;
            font-weight: 800;
        }

        .oc-inline-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .oc-btn {
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
            box-shadow: var(--shadow-sm);
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
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 800;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .oc-btn-soft:hover {
            background: #f9fafb;
            color: var(--text-main);
            text-decoration: none;
        }

        .oc-btn-ic {
            width: 36px;
            height: 36px;
            border-radius: 8px;
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

        .oc-btn-ic:hover {
            background: #f9fafb;
            color: var(--text-main);
            border-color: #d1d5db;
            text-decoration: none;
        }

        .oc-btn-ic.primary {
            color: var(--primary);
            border-color: var(--primary-light);
            background: var(--primary-light)
        }

        .oc-btn-ic.primary:hover {
            border-color: var(--primary)
        }

        .oc-btn-ic.warning {
            color: #d97706;
            border-color: #fde7b0;
            background: #fffbeb
        }

        .oc-btn-ic.warning:hover {
            border-color: #f59e0b
        }

        .oc-btn-ic.success {
            color: var(--success);
            border-color: #c7f2df;
            background: var(--success-light)
        }

        .oc-btn-ic.success:hover {
            border-color: var(--success)
        }

        .oc-btn-ic.danger {
            color: var(--danger);
            border-color: rgba(239, 68, 68, .18);
            background: var(--danger-light)
        }

        .oc-btn-ic.danger:hover {
            border-color: rgba(239, 68, 68, .35)
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
            color: var(--blue)
        }

        .oc-stat-icon.mine {
            background: var(--primary-light);
            color: var(--primary)
        }

        .oc-stat-icon.delayed {
            background: var(--danger-light);
            color: var(--danger)
        }

        .oc-stat-icon.due {
            background: var(--warning-light);
            color: #d97706
        }

        .oc-stat-icon.archived {
            background: var(--gray-light);
            color: var(--gray)
        }

        .oc-stat-icon.junk {
            background: #ffe4e6;
            color: #e11d48
        }

        .oc-stat-icon.deleted {
            background: #f3f4f6;
            color: #6b7280
        }

        .oc-stat-meta {
            min-width: 0
        }

        .oc-stat-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .oc-stat-value {
            font-size: 24px;
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
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: flex-end;
            justify-content: space-between;
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
            flex: 1;
            min-width: 320px;
        }

        .oc-filter-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .oc-input,
        .oc-select {
            width: 100%;
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            min-height: 42px;
        }

        .oc-input.search {
            padding-left: 36px;
            min-width: 240px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 10px center;
            background-size: 16px;
        }

        .oc-input:focus,
        .oc-select:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }


        .oc-owner-scope-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            padding: 12px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .oc-owner-scope-title {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 220px;
        }

        .oc-owner-scope-title strong {
            font-size: 13px;
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .oc-owner-scope-title span {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .oc-owner-switch {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .owner-filter-btn {
            border: 1px solid var(--border);
            background: #f9fafb;
            color: #111827;
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: var(--transition);
        }

        .owner-filter-btn:hover {
            background: #fff;
            border-color: #cbd5e1;
        }

        .owner-filter-btn--active {
            background: #111827;
            color: #fff;
            border-color: #111827;
            box-shadow: var(--shadow-sm);
        }

        .owner-filter-btn--created.owner-filter-btn--active {
            background: var(--primary);
            border-color: var(--primary);
        }

        .owner-filter-btn--all.owner-filter-btn--active {
            background: var(--blue);
            border-color: var(--blue);
        }

        .owner-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #f9fafb;
            color: #374151;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
        }

        .owner-badge--assigned {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .owner-badge--created {
            background: var(--primary-light);
            color: #365314;
            border-color: #d9f99d;
        }

        .owner-badge--both {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .oc-tabs-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .oc-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tab-button {
            border-radius: 999px;
            border: 1px solid var(--border);
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 800;
            background: #fff;
            color: #111827;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            white-space: nowrap;
            box-shadow: var(--shadow-sm);
        }

        .tab-button:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .tab-button--active {
            background: #111827;
            color: #fff;
            border-color: #111827;
            box-shadow: var(--shadow);
        }

        .view-switch-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .view-switch {
            border-radius: 10px;
            border: 1px solid var(--border);
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            background: #fff;
            color: #111827;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .view-switch:hover {
            background: #f9fafb;
        }

        .view-switch--active {
            background: var(--blue-light);
            color: #111827;
            border-color: #bfdbfe;
        }

        .app-notification-bar {
            margin-bottom: 16px;
            display: flex;
            align-items: stretch;
            background: #fff;
            border-radius: 16px;
            padding: 10px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
            position: relative;
        }

        .app-notification-bar.is-hidden {
            display: none;
        }

        .app-notification-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 900;
        }

        .app-notification-pill-icon {
            display: inline-flex;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            align-items: center;
            justify-content: center;
            background: #a3e635;
            font-size: 12px;
        }

        .app-notification-track {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 12px;
            margin: 0 10px;
            border-radius: 12px;
            background: linear-gradient(135deg, #111827 0%, #020617 100%);
            color: #e5e7eb;
            min-width: 0;
            gap: 10px;
        }

        .app-notification-type-badge {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, .4);
            background: rgba(15, 23, 42, .85);
            white-space: nowrap;
        }

        .app-notification-type-badge--due {
            border-color: #fdba74;
            background: rgba(249, 115, 22, .12);
            color: #fed7aa;
        }

        .app-notification-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }

        .app-notification-title {
            font-size: 13px;
            font-weight: 800;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .app-notification-message,
        .app-notification-meta {
            font-size: 12px;
            color: #cbd5e1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .app-notification-controls {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .app-notification-control {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fff;
            color: #111827;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            padding: 0;
            transition: var(--transition);
        }

        .app-notification-control:hover {
            background: #f9fafb;
        }

        .oc-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .kanban-shell {
            padding: 16px;
        }

        .kanban-grid {
            display: grid;
            gap: 16px;
        }

        @media (min-width: 1200px) {
            .kanban-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (min-width: 700px) and (max-width: 1199px) {
            .kanban-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .kanban-column {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--border);
            min-height: 140px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .kanban-column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 14px;
            background: #fafafa;
            border-bottom: 1px solid var(--border);
        }

        .kanban-column-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #111827;
        }

        .kanban-column-count {
            font-size: 12px;
            color: #6b7280;
            padding: 4px 10px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--border);
            font-weight: 800;
        }

        .kanban-droppable {
            flex: 1;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #fcfcfd;
        }

        .kanban-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            padding: 14px;
            font-size: 13px;
            cursor: grab;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: var(--transition);
        }

        .kanban-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .kanban-card.is-dragging {
            opacity: .55;
            transform: scale(.98);
        }

        .kanban-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .kanban-card-title {
            font-weight: 800;
            font-size: 15px;
            color: #111827;
            word-break: break-word;
        }

        .kanban-card-subtitle {
            font-size: 12px;
            color: #6b7280;
            word-break: break-word;
        }

        .kanban-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #6b7280;
            gap: 8px;
            flex-wrap: wrap;
        }

        .kanban-card-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin-top: 2px;
        }

        .kanban-card-customer {
            border: none;
            background: #e0f2fe;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            color: #0369a1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            border: 1px solid #bfdbfe;
        }

        .kanban-card-customer:hover {
            background: #bfdbfe;
        }

        .employee-avatar-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, .8);
            background: #e5e7eb;
            box-shadow: var(--shadow-sm);
        }

        .employee-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .btn-link-sm {
            font-size: 12px;
            border: none;
            background: none;
            padding: 0;
            color: #6b7280;
            cursor: pointer;
            text-decoration: underline;
            font-weight: 700;
        }

        .btn-link-sm:hover {
            color: #111827;
        }

        .priority-badge,
        .status-badge {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            border-width: 1px;
            border-style: solid;
            white-space: nowrap;
            font-weight: 800;
            text-transform: capitalize;
        }

        .priority-badge--urgent {
            border-color: #b91c1c;
            background: #fee2e2;
            color: #7f1d1d;
        }

        .priority-badge--high {
            border-color: #c05621;
            background: #ffedd5;
            color: #9a3412;
        }

        .priority-badge--normal {
            border-color: var(--primary);
            background: var(--primary-light);
            color: #1f2933;
        }

        .priority-badge--low {
            border-color: #9ca3af;
            background: #e5e7eb;
            color: #374151;
        }

        .priority-badge--default {
            border-color: #d1d5db;
            background: #f9fafb;
            color: #4b5563;
        }

        .status-badge--planned {
            border-color: var(--blue);
            background: #e0f2fe;
            color: #1d4ed8;
        }

        .status-badge--in_progress {
            border-color: #4f46e5;
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-badge--done {
            border-color: var(--primary);
            background: var(--primary-light);
            color: #14532d;
        }

        .status-badge--archived {
            border-color: #9ca3af;
            background: #f3f4f6;
            color: #4b5563;
        }

        .status-badge--junk {
            border-color: #e11d48;
            background: #ffe4e6;
            color: #9f1239;
        }

        .status-badge--default {
            border-color: #d1d5db;
            background: #f9fafb;
            color: #4b5563;
        }

        .report-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #f9fafb;
            font-size: 11px;
            padding: 6px 10px;
            cursor: pointer;
            color: #374151;
            white-space: nowrap;
            transition: var(--transition);
        }

        .report-pill:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .report-pill-counts {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .report-pill-count {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .report-pill-count-text strong {
            font-weight: 900;
        }

        .appointments-list-shell {
            padding: 0;
        }

        .appointments-list-container {
            overflow-x: auto;
            background: #fff;
        }

        .appointments-table {
            min-width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .appointments-table thead {
            background: #fafafa;
        }

        .appointments-table th {
            text-align: left;
            padding: 16px 14px 10px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: .06em;
            white-space: nowrap;
            border-bottom: 1px solid var(--border);
        }

        .appointments-table td {
            padding: 14px;
            vertical-align: top;
            border-top: 1px solid #f1f5f9;
            color: #111827;
        }

        .appointments-table tr:hover td {
            background: #fafafa;
        }

        .appointments-table-title {
            font-weight: 800;
            font-size: 14px;
            color: #111827;
            margin-bottom: 4px;
        }

        .appointments-table-subtitle {
            font-size: 12px;
            color: #6b7280;
        }

        .table-actions {
            text-align: right;
            white-space: nowrap;
        }

        .btn-table {
            border: none;
            background: none;
            font-size: 12px;
            cursor: pointer;
            padding: 0;
            margin-left: 8px;
            font-weight: 800;
        }

        .btn-table--edit {
            color: #4b5563;
        }

        .btn-table--edit:hover {
            color: #111827;
        }

        .btn-table--archive {
            color: #b45309;
        }

        .btn-table--archive:hover {
            color: #92400e;
        }

        .btn-table--delete {
            color: #b91c1c;
        }

        .btn-table--delete:hover {
            color: #991b1b;
        }

        .appointments-table th.sortable {
            cursor: pointer;
            position: relative;
            user-select: none;
            padding-right: 24px;
        }

        .appointments-table th.sortable::after {
            content: '';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            color: #9ca3af;
        }

        .appointments-table th.sortable.is-sorted-asc::after {
            content: '▲';
        }

        .appointments-table th.sortable.is-sorted-desc::after {
            content: '▼';
        }

        .appointment-drawer {
            position: fixed;
            inset: 0;
            z-index: 1200;
            pointer-events: none;
            opacity: 0;
            transition: opacity .22s ease;
        }

        .appointment-drawer.is-open {
            pointer-events: auto;
            opacity: 1;
        }

        .appointment-drawer-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, .55);
            backdrop-filter: blur(3px);
        }

        .appointment-drawer-panel {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            width: min(1180px, 100%);
            background: #fff;
            box-shadow: -24px 0 70px rgba(15, 23, 42, .25);
            border-radius: 24px 0 0 24px;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .22s ease-out;
            overflow: hidden;
        }

        .appointment-drawer.is-open .appointment-drawer-panel {
            transform: translateX(0);
        }

        .appointment-drawer-panel .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid var(--border) !important;
            background: #fafafa;
        }

        .appointment-drawer-panel .card-body {
            padding: 20px 18px;
            overflow-y: auto;
        }

        .appointment-drawer-panel .modal-body {
            max-height: none;
            overflow: visible;
        }

        .appointment-drawer-panel .modal-footer {
            border-top: 1px solid var(--border);
            padding: 14px 18px;
            background: #fafafa;
        }

        .section-title {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #111827;
            margin: 20px 0 10px;
        }

        .section-box {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            box-shadow: var(--shadow-sm);
        }

        .section-box label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .section-box .form-control,
        .section-box .select2-selection,
        .section-box select,
        .section-box textarea,
        .section-box input {
            border-radius: 8px !important;
        }

        .customer-modal,
        .report-modal {
            position: fixed;
            inset: 0;
            z-index: 1250;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .customer-modal.is-open,
        .report-modal.is-open {
            display: flex;
        }

        .customer-modal-backdrop,
        .report-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, .55);
            backdrop-filter: blur(3px);
        }

        .customer-modal-dialog,
        .report-modal-dialog {
            position: relative;
            z-index: 1;
            background: #fff;
            border: 1px solid rgba(229, 231, 235, .9);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .customer-modal-dialog {
            width: min(1759px, 96vw);
            height: min(84vh, 920px);
        }

        .report-modal-dialog {
            width: min(1100px, 96vw);
            height: min(84vh, 920px);
            display: flex;
            flex-direction: column;
        }

        .customer-modal-header,
        .report-modal-header {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
            background: #fafafa;
        }

        .customer-modal-title,
        .report-modal-title {
            font-weight: 900;
            font-size: 16px;
            line-height: 1.2;
            margin: 0;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .customer-modal-close,
        .report-modal-close {
            border: none;
            background: transparent;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            color: #6b7280;
        }

        .customer-modal-close:hover,
        .report-modal-close:hover {
            color: #111827;
        }

        .customer-modal-body {
            flex: 1;
            padding: 0;
        }

        .customer-modal-body iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .report-modal-body {
            flex: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.2fr);
            overflow: hidden;
        }

        @media (max-width: 900px) {
            .report-modal-body {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr 1fr;
            }
        }

        .report-modal-column {
            padding: 18px;
            overflow-y: auto;
        }

        .report-modal-column+.report-modal-column {
            border-left: 1px solid var(--border);
        }

        .report-modal-section-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #111827;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .report-modal-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .report-modal-input,
        .report-modal-textarea {
            width: 100%;
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 10px 12px;
            font-size: 14px;
            resize: vertical;
            box-sizing: border-box;
            background: #fff;
            outline: none;
            transition: var(--transition);
        }

        .report-modal-input:focus,
        .report-modal-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .report-modal-textarea {
            min-height: 90px;
        }

        .report-modal-form-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .report-modal-form-row--followup {
            grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
        }

        .report-modal-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .report-modal-actions {
            margin-top: 12px;
            text-align: right;
        }

        .report-modal-btn {
            border-radius: 10px;
            border: none;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff;
            transition: var(--transition);
        }

        .report-modal-btn:hover {
            background: var(--primary-hover);
        }

        .report-modal-list {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .report-modal-item,
        .report-modal-comment-item {
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fff;
            padding: 12px;
            font-size: 13px;
            box-shadow: var(--shadow-sm);
        }

        .report-modal-item-header,
        .report-modal-comment-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 6px;
        }

        .report-modal-item-author,
        .report-modal-comment-author {
            font-weight: 800;
            color: #111827;
        }

        .report-modal-item-time,
        .report-modal-comment-time {
            font-size: 12px;
            color: #9ca3af;
        }

        .report-modal-item-body,
        .report-modal-comment-body {
            color: #4b5563;
            white-space: pre-line;
        }

        .report-modal-item-followup {
            margin-top: 10px;
            padding: 10px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px dashed #bfdbfe;
            font-size: 12px;
        }

        .report-modal-item-followup-title {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 800;
            color: #1d4ed8;
            margin-bottom: 6px;
        }

        .report-modal-item-followup-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .followup-col {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .followup-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6b7280;
            font-weight: 800;
        }

        .followup-value {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }

        .icon-report,
        .icon-comment,
        .icon-followup {
            width: 14px;
            height: 14px;
            display: inline-block;
            flex-shrink: 0;
        }

        .is-hidden {
            display: none !important;
        }

        @media (max-width: 991px) {
            .oc-wrap {
                padding: 20px;
                padding-right: 20px;
                margin: 10px auto;
            }

            .oc-header {
                margin-top: 40px;
            }
        }

        @media (max-width: 768px) {
            .app-notification-bar {
                flex-direction: column;
                gap: 10px;
            }

            .app-notification-track {
                margin: 0;
                min-height: 72px;
                padding: 10px 12px;
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
@endsection

@section('content')
    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">TERMINE</div>
                    <div class="oc-sub">Kanban & Listenansicht mit Filtern, Suche und Verknüpfungen.</div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Home</a>
                        <span>›</span>
                        <span class="current">Termine</span>
                    </div>
                </div>

                <div class="oc-inline-actions">
                    <button id="btnCreateAppointment" type="button" class="oc-btn">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        Neuer Termin
                    </button>
                </div>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Alle</div>
                    <div class="oc-stat-value" id="analytics-all">0</div>
                    <div class="oc-stat-sub">Gesamt</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon mine">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21a8 8 0 1 0-16 0" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Meine</div>
                    <div class="oc-stat-value" id="analytics-mine">0</div>
                    <div class="oc-stat-sub">Mir zugeordnet</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon delayed">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7v6l4 2" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Überfällig</div>
                    <div class="oc-stat-value" id="analytics-delayed">0</div>
                    <div class="oc-stat-sub">Vergangene Termine offen</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon due">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M8 2v4M16 2v4M3 10h18" />
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Heute fällig</div>
                    <div class="oc-stat-value" id="analytics-due">0</div>
                    <div class="oc-stat-sub">Start heute</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon archived">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 8v13H3V8" />
                        <path d="M1 3h22v5H1z" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Archiv</div>
                    <div class="oc-stat-value" id="analytics-archived">0</div>
                    <div class="oc-stat-sub">Archiviert</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon junk">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Junk</div>
                    <div class="oc-stat-value" id="analytics-junk">0</div>
                    <div class="oc-stat-sub">Nicht relevant</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon deleted">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7" />
                        <path d="M10 11v6M14 11v6M4 7h16M9 7V4h6v3" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Gelöscht</div>
                    <div class="oc-stat-value" id="analytics-deleted">0</div>
                    <div class="oc-stat-sub">Papierkorb (aktueller Tab)</div>
                </div>
            </div>
        </div>

        {{-- Notification ticker --}}
        <div id="appointmentNotificationBar" class="app-notification-bar is-hidden">
            <div class="app-notification-pill">
                <span class="app-notification-pill-icon">⚡</span>
            </div>

            <div class="app-notification-track">
                <div class="app-notification-type-badge" id="notifKindBadge">Aufgabe</div>
                <div class="app-notification-main">
                    <div class="app-notification-title" id="notifTitle">–</div>
                    <div class="app-notification-message" id="notifMessage">–</div>
                </div>
                <div class="app-notification-meta" id="notifMeta">–</div>
            </div>

            <div class="app-notification-controls">
                <button class="app-notification-control" data-notif-action="prev" title="Vorherige">
                    <span>&lt;</span>
                </button>
                <button class="app-notification-control" data-notif-action="toggle" title="Pause">
                    <span id="notifPlayPauseIcon">❚❚</span>
                </button>
                <button class="app-notification-control" data-notif-action="next" title="Nächste">
                    <span>&gt;</span>
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="oc-toolbar">
            <div class="oc-toolbar-left">
                <div class="oc-filter-block search">
                    <label class="oc-filter-label">Suche</label>
                    <input id="filterSearch" type="text" class="oc-input search"
                        placeholder="Titel, Kunde, Ort, Telefon, E-Mail …">
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Von-Datum</label>
                    <input id="filterFromDate" type="date" class="oc-input">
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Bis-Datum</label>
                    <input id="filterToDate" type="date" class="oc-input">
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Mitarbeiter</label>
                    <select id="filterEmployee" class="oc-select">
                        <option value="">Alle</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ trim($employee->title . ' ' . $employee->name . ' ' . $employee->lastname) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Betrieb</label>
                    <select id="filterBranch" class="oc-select">
                        <option value="">Alle</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="oc-toolbar-right">
                <button type="button" id="btnResetAppointmentFilters" class="oc-btn-soft">Zurücksetzen</button>
            </div>
        </div>

        {{-- Tabs + View switch --}}
        <div class="oc-owner-scope-row">
            <div class="oc-owner-scope-title">
                <strong>Termin-Zuordnung</strong>
                <span>Wähle, ob du Termine sehen möchtest, die für dich erstellt wurden oder die du selbst erstellt
                    hast.</span>
            </div>

            <div class="oc-owner-switch" id="ownerScopeSwitch">
                <button type="button" class="owner-filter-btn owner-filter-btn--active" data-owner-scope="assigned_to_me">
                    Für mich erstellt
                </button>

                <button type="button" class="owner-filter-btn owner-filter-btn--created" data-owner-scope="created_by_me">
                    Von mir erstellt
                </button>

                <button type="button" class="owner-filter-btn owner-filter-btn--all" data-owner-scope="all_mine">
                    Alle meine Termine
                </button>
            </div>
        </div>

        <div class="oc-tabs-row">
            <div class="oc-tabs">
                @php
                    $tabs = [
                        'all' => 'Alle',
                        'mine' => 'Offene',
                        'delayed' => 'Überfällig',
                        'due' => 'Heute fällig',
                        'archived' => 'Archiv',
                        'junk' => 'Junk',
                        'deleted' => 'Gelöscht',
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <button class="tab-button {{ $key === 'all' ? 'tab-button--active' : '' }}" data-tab="{{ $key }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="view-switch-group">
                <button id="btnViewKanban" class="view-switch view-switch--active" data-view="kanban">Kanban</button>
                <button id="btnViewList" class="view-switch" data-view="list">Liste</button>
            </div>
        </div>

        {{-- Kanban --}}
        <div id="kanbanView" class="oc-card">
            <div class="kanban-shell">
                <div class="kanban-grid">
                    @php
                        $columns = [
                            'planned' => 'Geplant',
                            'in_progress' => 'In Arbeit',
                            'done' => 'Erledigt',
                            'archived' => 'Archiviert',
                        ];
                    @endphp

                    @foreach($columns as $status => $label)
                        <div class="kanban-column" data-status="{{ $status }}">
                            <div class="kanban-column-header">
                                <span class="kanban-column-title">{{ $label }}</span>
                                <span class="kanban-column-count" data-count="{{ $status }}">0</span>
                            </div>
                            <div class="kanban-droppable" data-status="{{ $status }}">
                                {{-- Karten per JS --}}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- List --}}
        <div id="listView" class="oc-card appointments-list-shell is-hidden" style="margin-top:16px;">
            <div class="appointments-list-container">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th class="sortable" data-sort="name">Titel</th>
                            <th class="sortable" data-sort="start_date">Zeitraum</th>
                            <th class="sortable" data-sort="customer_name">Kunde/Kontakt</th>
                            <th class="sortable" data-sort="city">Ort</th>
                            <th class="sortable" data-sort="employees">Mitarbeiter</th>
                            <th class="sortable" data-sort="status">Status</th>
                            <th class="sortable" data-sort="priority">Priorität</th>
                            <th class="table-actions">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentsTableBody">
                        {{-- Zeilen per JS --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Appointment Drawer --}}
        <div id="appointmentDrawer" class="appointment-drawer">
            <div id="appointmentDrawerBackdrop" class="appointment-drawer-backdrop"></div>

            <div class="appointment-drawer-panel cards new_task_card new_task">
                <div class="card-header">
                    <h3 class="title mb-0" style="font-size:16px;font-weight:900;color:#111827;">
                        TERMIN ERSTELLEN / BEARBEITEN
                    </h3>
                    <button type="button" class="oc-btn-ic close_task_window" title="Schließen">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="card-body">
                    <form id="task-store-form">
                        @csrf
                        <input type="hidden" name="id" id="appointment_id">
                        <input type="hidden" name="contact_mode" id="contact_mode" value="new">
                        <input type="hidden" name="contact_type" id="contact_type">

                        <div class="modal-body">

                            {{-- SECTION: Kontakt --}}
                            <div class="section-title">Kontakt</div>
                            <div class="section-box">
                                <div class="form-row">
                                    <div class="col-md-12 mb-1">
                                        <label>Typ</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input contact-type-toggle" type="radio"
                                                name="contact_mode_radio" id="newContact" value="new" checked>
                                            <label class="form-check-label" for="newContact">Neu</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input contact-type-toggle" type="radio"
                                                name="contact_mode_radio" id="selectContact" value="select">
                                            <label class="form-check-label" for="selectContact">Kontakt</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-12 contact-name-block">
                                        <label for="name">Kunde/Kontakt *</label>
                                        <input type="text" id="name" class="form-control name" name="name">
                                    </div>

                                    <div class="col-md-12 contact-select-block d-none">
                                        <label for="customer_id">Kunde/Kontakt *</label>
                                        <select name="customer_id" id="customer_id" class="contact_list"
                                            style="width:100%"></select>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION: Termin-Daten --}}
                            <div class="section-title">Termin</div>
                            <div class="section-box">
                                <div class="form-row">
                                    <div class="col-md-10 col-10 mb-1">
                                        <label for="appointment_type">Art des Termins</label>
                                        <input type="text" class="form-control" id="appointment_type"
                                            name="appointment_type" value="{{ old('appointment_type') }}">
                                    </div>
                                    <div class="col-md-2 col-2 mb-1 d-flex align-items-end">
                                        <input type="hidden" name="color" id="color" value="#8fc73e">
                                        <div class="btn-group dropup dropdown-icon-wrapper w-100" id="color_drop_down">
                                            <button type="button" class="btn btn-light btn-block" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="true">
                                                <i class="fa fa-square" id="colorIcon" style="color:#8fc73e;"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <span class="dropdown-item" data-value="#8fc73e"><i class="fa fa-square"
                                                        style="color:#8fc73e;"></i> Grün</span>
                                                <span class="dropdown-item" data-value="#ff0000"><i class="fa fa-square"
                                                        style="color:#ff0000;"></i> Rot</span>
                                                <span class="dropdown-item" data-value="#0000ff"><i class="fa fa-square"
                                                        style="color:#0000ff;"></i> Blau</span>
                                                <span class="dropdown-item" data-value="#ffff00"><i class="fa fa-square"
                                                        style="color:#ffff00;"></i> Gelb</span>
                                                <span class="dropdown-item" data-value="#ff00ff"><i class="fa fa-square"
                                                        style="color:#ff00ff;"></i> Magenta</span>
                                                <span class="dropdown-item" data-value="#00ffff"><i class="fa fa-square"
                                                        style="color:#00ffff;"></i> Cyan</span>
                                                <span class="dropdown-item" data-value="#000000"><i class="fa fa-square"
                                                        style="color:#1f2937;"></i> Schwarz</span>
                                                <span class="dropdown-item" data-value="#808080"><i class="fa fa-square"
                                                        style="color:#808080;"></i> Grau</span>
                                                <span class="dropdown-item" data-value="#ffa500"><i class="fa fa-square"
                                                        style="color:#ffa500;"></i> Orange</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12 mb-1">
                                        <label for="start_date">Startdatum *</label>
                                        <input type="date" id="start_date" class="form-control" name="start_date">
                                    </div>
                                    <div class="col-md-6 col-12 mb-1">
                                        <label for="end_date">Enddatum *</label>
                                        <input type="date" id="end_date" class="form-control" name="end_date">
                                    </div>

                                    <div class="col-md-4 col-12 mb-1">
                                        <label for="start_time">Startzeit *</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time">
                                    </div>
                                    <div class="col-md-4 col-12 mb-1">
                                        <label for="end_time">Endzeit</label>
                                        <input type="time" id="end_time" class="form-control" name="end_time">
                                    </div>
                                    <div class="col-md-4 col-12 mb-1">
                                        <label for="total_time">Dauer (Minuten)</label>
                                        <input type="number" id="total_time" class="form-control" name="total_time">
                                    </div>

                                    <div class="col-md-6 col-12 mb-1">
                                        <label for="priority">Priorität</label>
                                        <select name="priority" class="form-control" id="priority">
                                            <option value="normal">Keiner</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">Hoch</option>
                                            <option value="very high">Sehr wichtig</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION: Teilnehmer --}}
                            <div class="section-title">Teilnehmer</div>
                            <div class="section-box" id="participantsBlock">
                                <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}"
                                            data-image="{{ asset('images/employee/' . $emp->image) }}">
                                            {{ $emp->name }} {{ $emp->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- SECTION: Ort & Kontakt --}}
                            <div class="section-title">Ort & Kontakt</div>
                            <div class="section-box">
                                <div class="form-row">
                                    <div class="col-md-6 mb-1" id="intern" style="display:none;">
                                        <label for="branch_address_id">Adresse (Betrieb)</label>
                                        <select name="branch_address_id" id="branch_address_id" class="form-control">
                                            <option></option>
                                            @foreach ($branch_addresses as $address)
                                                <option value="{{ $address->id }}" data-street="{{ $address->street }}"
                                                    data-latitude="{{ $address->latitude }}"
                                                    data-longitude="{{ $address->longitude }}" data-city="{{ $address->city }}"
                                                    data-postcode="{{ $address->postcode }}">
                                                    {{ $address->branch_initial }} - {{ $address->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-1" id="extern">
                                        <label for="full_address">Adresse</label>
                                        <input id="full_address" type="text" class="form-control form-element"
                                            placeholder="Adresse eingeben" name="full_address">

                                        <input type="hidden" id="street-input" name="street">
                                        <input type="hidden" id="city-input" name="city">
                                        <input type="hidden" id="latitude-input" name="latitude">
                                        <input type="hidden" id="longitude-input" name="longitude">
                                        <input type="hidden" id="postal_code-input" name="postcode">
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label for="execution_type">Ort des Termins</label>
                                        <select name="execution_type" id="execution_type" class="form-control">
                                            <option value="external" selected>Extern</option>
                                            <option value="internal">Intern</option>
                                            <option value="online">Online</option>
                                            <option value="telephone">Telefon</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label for="phone">Telefon</label>
                                        <input type="text" class="form-control phone" name="phone" id="phone"
                                            value="{{ old('phone') }}">
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label for="email">E-Mail <small>Optional</small></label>
                                        <input type="email" class="form-control email" name="email" id="email"
                                            value="{{ old('email') }}">
                                    </div>

                                    <div class="col-md-6 mb-1" id="link_section" style="display:none;">
                                        <label for="link">Link (Online-Termin)</label>
                                        <input type="text" class="form-control" id="link" name="link"
                                            value="{{ old('link') }}">
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label for="branch_id">Betrieb</label>
                                        <select name="branch_id" id="branch_id" class="selectables" style="width:100%">
                                            <option></option>
                                            @foreach($branches as $br)
                                                <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mb-1">
                                        <label for="description">Beschreibung</label>
                                        <textarea name="description" class="form-control" id="description"
                                            rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="oc-btn-soft close_task_window">
                                <i class="feather icon-x"></i> Abbrechen
                            </button>
                            <button type="button" class="oc-btn save-task">
                                <i class="feather icon-save"></i> Speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Profile Modal --}}
    <div id="customerProfileModal" class="customer-modal">
        <div class="customer-modal-backdrop"></div>
        <div class="customer-modal-dialog">
            <div class="customer-modal-header">
                <h3 class="customer-modal-title" id="customerModalTitle">Kunde</h3>
                <button type="button" class="customer-modal-close" id="customerModalClose">&times;</button>
            </div>
            <div class="customer-modal-body">
                <iframe id="customerProfileFrame" src="" loading="lazy"></iframe>
            </div>
        </div>
    </div>

    {{-- Report Modal --}}
    <div id="appointmentReportModal" class="report-modal">
        <div class="report-modal-backdrop"></div>
        <div class="report-modal-dialog">
            <div class="report-modal-header">
                <h3 class="report-modal-title" id="reportModalTitle">
                    <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6"
                            stroke-linejoin="round" />
                        <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                    </svg>
                    <span>Reports &amp; Kommentare</span>
                </h3>
                <button type="button" class="report-modal-close" id="reportModalClose" aria-label="Schließen">
                    &times;
                </button>
            </div>

            <div class="report-modal-body">
                {{-- LEFT: Reports --}}
                <div class="report-modal-column">
                    <div class="report-modal-section-title">
                        <span class="section-title-icon">
                            <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>Neuer Report</span>
                    </div>

                    <label class="report-modal-label" for="reportModalReportText">Report / Besuchsbericht</label>
                    <textarea id="reportModalReportText" class="report-modal-textarea"
                        placeholder="Report / Besuchsbericht …"></textarea>

                    <div class="report-modal-form-row">
                        <div class="report-modal-field">
                            <label class="report-modal-label" for="reportModalReportDate">Berichtsdatum</label>
                            <input id="reportModalReportDate" type="date" class="report-modal-input" placeholder="Datum">
                        </div>
                    </div>

                    <div class="report-modal-form-row report-modal-form-row--followup">
                        <div class="report-modal-field">
                            <label class="report-modal-label" for="reportModalNextStep">Nächster Schritt</label>
                            <input id="reportModalNextStep" type="text" class="report-modal-input"
                                placeholder="z. B. Angebot senden, Rückruf, Termin vereinbaren …">
                        </div>
                        <div class="report-modal-field">
                            <label class="report-modal-label" for="reportModalDueDate">Fällig bis</label>
                            <input id="reportModalDueDate" type="date" class="report-modal-input" placeholder="Fällig bis">
                        </div>
                    </div>

                    <div class="report-modal-actions">
                        <button type="button" class="report-modal-btn" id="reportModalSaveReport">
                            <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M10 13h4" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <path d="M12 11v4" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <span>Report speichern</span>
                        </button>
                    </div>

                    <div class="report-modal-section-title report-modal-section-title--list">
                        <span class="section-title-icon">
                            <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M9 11h6M9 15h4" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                        <span>Vorhandene Reports</span>
                    </div>

                    <div id="reportModalReportList" class="report-modal-list">
                        {{-- per JS --}}
                    </div>
                </div>

                {{-- RIGHT: Kommentare --}}
                <div class="report-modal-column">
                    <div class="report-modal-section-title">
                        <span class="section-title-icon">
                            <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                        <span>Neuer Kommentar</span>
                    </div>

                    <label class="report-modal-label" for="reportModalCommentText">Kommentar</label>
                    <textarea id="reportModalCommentText" class="report-modal-textarea"
                        placeholder="Kommentar hinzufügen …"></textarea>

                    <div class="report-modal-actions">
                        <button type="button" class="report-modal-btn" id="reportModalSaveComment">
                            <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <span>Kommentar speichern</span>
                        </button>
                    </div>

                    <div class="report-modal-section-title report-modal-section-title--list">
                        <span class="section-title-icon">
                            <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linejoin="round" />
                                <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                        <span>Kommentare</span>
                    </div>

                    <div id="reportModalCommentList" class="report-modal-list">
                        {{-- per JS --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        // Globale Variablen aus Blade ins JS
        window.CURRENT_EMPLOYEE_ID = {{ (int) auth()->user()->name }};
        window.FAVORITE_EMPLOYEE_IDS = @json($favorite_employee_ids ?? []);
        window.EMPLOYEE_IMAGE_BASE = "{{ asset('images/employee') }}/";

        window.ROUTE = Object.assign({}, window.ROUTE || {}, {
            appointmentsData: "{{ route('customer.appointments.data') }}",
            appointmentsStore: "{{ route('customer.appointments.store') }}",
            appointmentsBase: "{{ url('customer/appointments') }}",
            contactList: "{{ route('get.contact.list') }}",
            customerProfileBase: "{{ url('new_lead_profile') }}",
            appointmentReportsBase: "{{ url('customer/appointments') }}",
            appointmentCommentsBase: "{{ url('customer/appointments') }}",
            appointmentsNotifications: "{{ route('customer.appointments.notificationsTicker') }}",
        });
    </script>

    <script>
        (function () {
            "use strict";

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // -------------------------------------------------
            // DOM refs
            // -------------------------------------------------
            const drawer = document.getElementById('appointmentDrawer');
            const drawerBackdrop = document.getElementById('appointmentDrawerBackdrop');
            const closeButtons = document.querySelectorAll('.close_task_window');
            const btnCreate = document.getElementById('btnCreateAppointment');

            const filterSearch = document.getElementById('filterSearch');
            const filterFromDate = document.getElementById('filterFromDate');
            const filterToDate = document.getElementById('filterToDate');
            const filterEmployee = document.getElementById('filterEmployee');
            const filterBranch = document.getElementById('filterBranch');
            const btnResetAppointmentFilters = document.getElementById('btnResetAppointmentFilters');

            const kanbanView = document.getElementById('kanbanView');
            const listView = document.getElementById('listView');
            const tableBody = document.getElementById('appointmentsTableBody');

            const tabButtons = document.querySelectorAll('.tab-button');
            const ownerFilterButtons = document.querySelectorAll('.owner-filter-btn');
            const viewButtons = document.querySelectorAll('.view-switch');
            const sortableHeaders = document.querySelectorAll('.appointments-table th.sortable');

            const analyticsAll = document.getElementById('analytics-all');
            const analyticsMine = document.getElementById('analytics-mine');
            const analyticsDelayed = document.getElementById('analytics-delayed');
            const analyticsDue = document.getElementById('analytics-due');
            const analyticsArchived = document.getElementById('analytics-archived');
            const analyticsJunk = document.getElementById('analytics-junk');
            const analyticsDeleted = document.getElementById('analytics-deleted');

            // Customer modal
            const customerModal = document.getElementById('customerProfileModal');
            const customerModalFrame = document.getElementById('customerProfileFrame');
            const customerModalTitle = document.getElementById('customerModalTitle');
            const customerModalClose = document.getElementById('customerModalClose');
            const customerModalBackdrop = customerModal ? customerModal.querySelector('.customer-modal-backdrop') : null;

            // Report modal
            const reportModal = document.getElementById('appointmentReportModal');
            const reportModalBackdrop = reportModal ? reportModal.querySelector('.report-modal-backdrop') : null;
            const reportModalClose = document.getElementById('reportModalClose');
            const reportModalTitle = document.getElementById('reportModalTitle');

            const reportTextEl = document.getElementById('reportModalReportText');
            const reportDateEl = document.getElementById('reportModalReportDate');
            const reportDueDateEl = document.getElementById('reportModalDueDate');
            const reportNextStepEl = document.getElementById('reportModalNextStep');
            const reportSaveBtn = document.getElementById('reportModalSaveReport');
            const reportListEl = document.getElementById('reportModalReportList');

            const commentTextEl = document.getElementById('reportModalCommentText');
            const commentSaveBtn = document.getElementById('reportModalSaveComment');
            const commentListEl = document.getElementById('reportModalCommentList');

            // Notification ticker
            const notifBar = document.getElementById('appointmentNotificationBar');
            const notifTitleEl = document.getElementById('notifTitle');
            const notifMessageEl = document.getElementById('notifMessage');
            const notifMetaEl = document.getElementById('notifMeta');
            const notifKindBadgeEl = document.getElementById('notifKindBadge');
            const notifPlayPauseEl = document.getElementById('notifPlayPauseIcon');

            // -------------------------------------------------
            // State
            // -------------------------------------------------
            let currentView = 'kanban';
            let currentOwnerScope = 'assigned_to_me';
            let currentTab = 'all';
            let currentSort = 'start_date';
            let currentDirection = 'asc';
            let appointmentsCache = [];
            let currentReportAppointmentId = null;

            const notifState = {
                items: [],
                index: 0,
                timer: null,
                playing: true,
                interval: 8000
            };

            // -------------------------------------------------
            // Generic helpers
            // -------------------------------------------------
            function escapeHtml(str) {
                return (str || '').toString().replace(/[&<>"']/g, function (m) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    })[m];
                });
            }

            function showError(message) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        text: message || 'Es ist ein Fehler aufgetreten.'
                    });
                } else {
                    alert(message || 'Es ist ein Fehler aufgetreten.');
                }
            }

            function showSuccess(message) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Erfolgreich',
                        text: message || 'Aktion erfolgreich.'
                    });
                } else {
                    alert(message || 'Aktion erfolgreich.');
                }
            }

            function svgReportIcon() {
                return `
                <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M9 11h6M9 15h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            `;
            }

            function svgCommentIcon() {
                return `
                <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            `;
            }

            function reportPillInnerHtml(reportsCount, commentsCount) {
                return `
                <span class="report-pill-count">
                    ${svgReportIcon()}
                    <span class="report-pill-count-text"><strong>${reportsCount}</strong> Reports</span>
                </span>
                <span class="report-pill-count">
                    ${svgCommentIcon()}
                    <span class="report-pill-count-text"><strong>${commentsCount}</strong> Kommentare</span>
                </span>
            `;
            }


            function ownerBadgeHtml(a) {
                const created = !!a.is_created_by_me;
                const assigned = !!a.is_assigned_to_me;

                if (created && assigned) {
                    return '<span class="owner-badge owner-badge--both">Von mir & für mich</span>';
                }

                if (created) {
                    return '<span class="owner-badge owner-badge--created">Von mir erstellt</span>';
                }

                if (assigned) {
                    return '<span class="owner-badge owner-badge--assigned">Für mich erstellt</span>';
                }

                return '';
            }

            function priorityClass(p) {
                switch ((p || '').toLowerCase()) {
                    case 'very high':
                    case 'urgent':
                        return 'priority-badge--urgent';
                    case 'high':
                        return 'priority-badge--high';
                    case 'medium':
                        return 'priority-badge--normal';
                    case 'normal':
                        return 'priority-badge--low';
                    default:
                        return 'priority-badge--default';
                }
            }

            function statusClass(s) {
                switch ((s || '').toLowerCase()) {
                    case 'planned':
                        return 'status-badge--planned';
                    case 'in_progress':
                        return 'status-badge--in_progress';
                    case 'done':
                        return 'status-badge--done';
                    case 'archived':
                        return 'status-badge--archived';
                    case 'junk':
                        return 'status-badge--junk';
                    default:
                        return 'status-badge--default';
                }
            }

            function employeeAvatarsHtml(employees) {
                if (!Array.isArray(employees) || !employees.length) return '';

                return employees.map(function (e) {
                    const rawImage = e.avatar_url || e.image_url || e.image || '';
                    let avatarUrl = '';

                    if (rawImage) {
                        avatarUrl = (/^https?:\/\//i.test(rawImage) || rawImage.startsWith('/'))
                            ? rawImage
                            : (window.EMPLOYEE_IMAGE_BASE || '') + rawImage;
                    } else {
                        avatarUrl = (window.EMPLOYEE_IMAGE_BASE || '') + 'default.png';
                    }

                    const fullName = e.full_name || [e.name || '', e.lastname || ''].filter(Boolean).join(' ');
                    const safeName = escapeHtml(fullName || 'Mitarbeiter');

                    return `
                    <span class="employee-avatar-wrapper" title="${safeName}">
                        <img src="${avatarUrl}" alt="${safeName}" class="employee-avatar">
                    </span>
                `;
                }).join('');
            }

            // -------------------------------------------------
            // Appointment count helpers
            // -------------------------------------------------
            function getReportCountFromAppointment(a) {
                if (!a) return 0;
                if (typeof a.reports_count === 'number') return a.reports_count;
                if (typeof a.report_count === 'number') return a.report_count;
                if (typeof a.reports === 'number') return a.reports;
                if (Array.isArray(a.reports)) return a.reports.length;
                if (typeof a.appointment_reports_count === 'number') return a.appointment_reports_count;
                return 0;
            }

            function getCommentCountFromAppointment(a) {
                if (!a) return 0;
                if (typeof a.comments_count === 'number') return a.comments_count;
                if (typeof a.comment_count === 'number') return a.comment_count;
                if (typeof a.comments === 'number') return a.comments;
                if (Array.isArray(a.comments)) return a.comments.length;
                if (typeof a.appointment_comments_count === 'number') return a.appointment_comments_count;
                return 0;
            }

            // -------------------------------------------------
            // Sorting
            // -------------------------------------------------
            function compareForSort(a, b, key) {
                const dir = currentDirection === 'desc' ? -1 : 1;
                let va = '';
                let vb = '';

                switch (key) {
                    case 'name':
                        va = (a.name || a.appointment_type || '').toLowerCase();
                        vb = (b.name || b.appointment_type || '').toLowerCase();
                        break;
                    case 'customer_name':
                        va = (a.customer_name || '').toLowerCase();
                        vb = (b.customer_name || '').toLowerCase();
                        break;
                    case 'city':
                        va = (a.city || '').toLowerCase();
                        vb = (b.city || '').toLowerCase();
                        break;
                    case 'status':
                        va = (a.status || '').toLowerCase();
                        vb = (b.status || '').toLowerCase();
                        break;
                    case 'priority':
                        va = (a.priority || '').toLowerCase();
                        vb = (b.priority || '').toLowerCase();
                        break;
                    case 'employees':
                        va = (((a.employees || [])[0]?.full_name) || '').toLowerCase();
                        vb = (((b.employees || [])[0]?.full_name) || '').toLowerCase();
                        break;
                    case 'start_date':
                    case 'end_date':
                        va = a[key] || '';
                        vb = b[key] || '';
                        break;
                    default:
                        va = (a[key] ?? '').toString().toLowerCase();
                        vb = (b[key] ?? '').toString().toLowerCase();
                }

                if (va < vb) return -1 * dir;
                if (va > vb) return 1 * dir;
                return 0;
            }

            function getSortedAppointments() {
                const arr = appointmentsCache.slice();
                if (!currentSort) return arr;
                return arr.sort((a, b) => compareForSort(a, b, currentSort));
            }

            function updateHeaderSortState() {
                sortableHeaders.forEach(th => {
                    th.classList.remove('is-sorted-asc', 'is-sorted-desc');
                    const key = th.dataset.sort;
                    if (key === currentSort) {
                        th.classList.add(currentDirection === 'asc' ? 'is-sorted-asc' : 'is-sorted-desc');
                    }
                });
            }

            // -------------------------------------------------
            // Drawer
            // -------------------------------------------------
            function openDrawer() {
                if (!drawer) return;
                drawer.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                if (!drawer) return;
                drawer.classList.remove('is-open');
                document.body.style.overflow = '';
            }

            function resetFormForCreate() {
                const form = document.getElementById('task-store-form');
                if (form) form.reset();

                const idField = document.getElementById('appointment_id');
                if (idField) idField.value = '';

                const contactMode = document.getElementById('contact_mode');
                if (contactMode) contactMode.value = 'new';

                $('.contact-name-block').removeClass('d-none');
                $('.contact-select-block').addClass('d-none');

                if ($.fn.select2) {
                    $('#customer_id').val(null).trigger('change');
                    $('#employee').val(null).trigger('change');
                    $('#branch_id').val(null).trigger('change');
                } else {
                    $('#customer_id').val('');
                    $('#employee').val('');
                    $('#branch_id').val('');
                }

                $('#execution_type').val('external').trigger('change');

                if (window.FAVORITE_EMPLOYEE_IDS && window.FAVORITE_EMPLOYEE_IDS.length && $.fn.select2) {
                    $('#employee').val(window.FAVORITE_EMPLOYEE_IDS.map(String)).trigger('change');
                }

                $('#color').val('#8fc73e');
                $('#colorIcon').css('color', '#8fc73e');
            }

            function openFormForEdit(appointment) {
                const form = document.getElementById('task-store-form');
                if (!form || !appointment) return;

                form.reset();

                $('#appointment_id').val(appointment.id || '');
                $('#appointment_type').val(appointment.appointment_type || appointment.name || '');
                $('#start_date').val(appointment.start_date || '');
                $('#end_date').val(appointment.end_date || '');
                $('#start_time').val(appointment.start_time || '');
                $('#end_time').val(appointment.end_time || '');
                $('#total_time').val(appointment.total_time || '');
                $('#priority').val(appointment.priority || 'normal');

                if (appointment.color) {
                    $('#color').val(appointment.color);
                    $('#colorIcon').css('color', appointment.color);
                }

                $('#execution_type').val(appointment.execution_type || 'external').trigger('change');
                $('#link').val(appointment.link || '');
                $('#description').val(appointment.description || '');
                $('#phone').val(appointment.phone || '');
                $('#email').val(appointment.email || '');

                const addr = appointment.full_address
                    ? appointment.full_address
                    : [appointment.street, appointment.postcode, appointment.city].filter(Boolean).join(', ');

                $('#full_address').val(addr);
                $('#street-input').val(appointment.street || '');
                $('#city-input').val(appointment.city || '');
                $('#postal_code-input').val(appointment.postcode || '');
                $('#latitude-input').val(appointment.latitude || '');
                $('#longitude-input').val(appointment.longitude || '');

                if ($.fn.select2 && appointment.branch_id) {
                    $('#branch_id').val(String(appointment.branch_id)).trigger('change');
                } else {
                    $('#branch_id').val(appointment.branch_id || '');
                }

                if (appointment.branch_address_id) {
                    $('#branch_address_id').val(String(appointment.branch_address_id));
                }

                if ($.fn.select2 && Array.isArray(appointment.employees)) {
                    const ids = appointment.employees.map(e => String(e.id));
                    $('#employee').val(ids).trigger('change');
                }

                if (appointment.customer_id) {
                    $('#contact_mode').val('select');
                    $('#selectContact').prop('checked', true);
                    $('.contact-name-block').addClass('d-none');
                    $('.contact-select-block').removeClass('d-none');

                    if ($.fn.select2) {
                        const optionText = appointment.customer_name || ('Kontakt #' + appointment.customer_id);
                        const option = new Option(optionText, appointment.customer_id, true, true);
                        $('#customer_id').append(option).trigger('change');
                    } else {
                        $('#customer_id').val(String(appointment.customer_id));
                    }

                    $('#name').val(appointment.customer_name || '');
                } else {
                    $('#contact_mode').val('new');
                    $('#newContact').prop('checked', true);
                    $('.contact-name-block').removeClass('d-none');
                    $('.contact-select-block').addClass('d-none');
                    $('#name').val(appointment.name || appointment.customer_name || '');
                    $('#customer_id').val(null).trigger('change');
                }

                openDrawer();
            }

            // -------------------------------------------------
            // Customer modal
            // -------------------------------------------------
            function openCustomerModal(customerId, customerName) {
                if (!customerModal || !customerModalFrame || !customerId) return;

                const base = (window.ROUTE && window.ROUTE.customerProfileBase)
                    ? window.ROUTE.customerProfileBase
                    : '/new_lead_profile';

                const url = base.replace(/\/$/, '') + '/' + encodeURIComponent(customerId);

                customerModalFrame.src = url;
                if (customerModalTitle) {
                    customerModalTitle.textContent = customerName ? ('Kunde: ' + customerName) : 'Kundenprofil';
                }

                customerModal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeCustomerModal() {
                if (!customerModal) return;
                customerModal.classList.remove('is-open');
                if (customerModalFrame) customerModalFrame.src = '';
                document.body.style.overflow = '';
            }

            // -------------------------------------------------
            // Report / comment helpers
            // -------------------------------------------------
            function normalizeReportsResponse(json) {
                let reports = [];
                let comments = [];

                if (!json) return { reports, comments };

                if (Array.isArray(json)) {
                    reports = json;
                    return { reports, comments };
                }

                if (Array.isArray(json.reports)) {
                    reports = json.reports;
                } else if (Array.isArray(json.data)) {
                    reports = json.data;
                } else if (Array.isArray(json.items)) {
                    reports = json.items;
                }

                if (Array.isArray(json.comments)) {
                    comments = json.comments;
                } else if (Array.isArray(json.appointment_comments)) {
                    comments = json.appointment_comments;
                }

                return { reports, comments };
            }

            function getReportAuthor(r) {
                if (!r) return 'Mitarbeiter';

                if (r.employee) {
                    const emp = r.employee;
                    if (emp.full_name) return emp.full_name;
                    const full = [emp.name || '', emp.lastname || ''].filter(Boolean).join(' ');
                    if (full) return full;
                }

                return r.employee_name || r.author_name || r.created_by_name || r.created_by || 'Mitarbeiter';
            }

            function getReportCreatedAt(r) {
                if (!r) return '';
                return r.created_at_human || r.created_at_formatted || r.created_diff || r.created_at || '';
            }

            function getReportNextStep(r) {
                if (!r) return '';
                return r.next_step || r.next_action || r.follow_up || r.followup || r.next || '';
            }

            function getReportDueDate(r) {
                if (!r) return '';
                return r.due_date || r.next_step_due || r.next_due || r.due_to || '';
            }

            function renderReportModalReports(reports) {
                if (!reportListEl) return;
                reportListEl.innerHTML = '';

                reports.forEach(r => {
                    const div = document.createElement('div');
                    div.className = 'report-modal-item';

                    const author = getReportAuthor(r);
                    const created = getReportCreatedAt(r);
                    const reportHtml = r.report_html || escapeHtml(r.report || '');
                    const nextStep = getReportNextStep(r);
                    const dueDate = getReportDueDate(r);

                    div.innerHTML = `
                    <div class="report-modal-item-header">
                        <span class="report-modal-item-author">${escapeHtml(author)}</span>
                        <span class="report-modal-item-time">${escapeHtml(created)}</span>
                    </div>

                    <div class="report-modal-item-body">${reportHtml}</div>

                    <div class="report-modal-item-followup">
                        <div class="report-modal-item-followup-title">
                            <svg class="icon-followup" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 12h14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Follow-up</span>
                        </div>
                        <div class="report-modal-item-followup-row">
                            <div class="followup-col">
                                <span class="followup-label">Nächster Schritt</span>
                                <span class="followup-value">${escapeHtml(nextStep || '–')}</span>
                            </div>
                            <div class="followup-col">
                                <span class="followup-label">Fällig bis</span>
                                <span class="followup-value">${escapeHtml(dueDate || '–')}</span>
                            </div>
                        </div>
                    </div>
                `;

                    reportListEl.appendChild(div);
                });
            }

            function renderReportModalComments(comments) {
                if (!commentListEl) return;
                commentListEl.innerHTML = '';

                comments.forEach(c => {
                    const div = document.createElement('div');
                    div.className = 'report-modal-comment-item';

                    const author = getReportAuthor(c);
                    const created = getReportCreatedAt(c);
                    const text = c.comment || c.text || c.body || '';

                    div.innerHTML = `
                    <div class="report-modal-comment-header">
                        <span class="report-modal-comment-author">${escapeHtml(author)}</span>
                        <span class="report-modal-comment-time">${escapeHtml(created)}</span>
                    </div>
                    <div class="report-modal-comment-body">${escapeHtml(text)}</div>
                `;

                    commentListEl.appendChild(div);
                });
            }

            function updateReportBadges(appointmentId, reportCount, commentCount) {
                const selector = '.report-pill.js-open-reports[data-id="' + appointmentId + '"]';
                document.querySelectorAll(selector).forEach(btn => {
                    const countsEl = btn.querySelector('.report-pill-counts');
                    if (countsEl) countsEl.innerHTML = reportPillInnerHtml(reportCount, commentCount);
                });

                const item = appointmentsCache.find(a => String(a.id) === String(appointmentId));
                if (item) {
                    item.reports_count = reportCount;
                    item.comments_count = commentCount;
                }
            }

            async function loadReportsAndComments(appointmentId) {
                if (!window.ROUTE || !window.ROUTE.appointmentReportsBase) return;

                const base = window.ROUTE.appointmentReportsBase.replace(/\/$/, '');
                const url = base + '/' + encodeURIComponent(appointmentId) + '/reports';

                try {
                    const resp = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!resp.ok) return;

                    const json = await resp.json();
                    const normalized = normalizeReportsResponse(json);

                    renderReportModalReports(normalized.reports);
                    renderReportModalComments(normalized.comments);
                    updateReportBadges(appointmentId, normalized.reports.length, normalized.comments.length);
                } catch (e) {
                    console.error(e);
                }
            }

            function openReportModal(appointmentId, title) {
                if (!reportModal || !appointmentId) return;

                currentReportAppointmentId = appointmentId;

                if (reportModalTitle) {
                    reportModalTitle.innerHTML = `
                    <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    </svg>
                    <span>${title ? ('Reports – ' + escapeHtml(title)) : 'Reports & Kommentare'}</span>
                `;
                }

                if (reportTextEl) reportTextEl.value = '';
                if (reportDateEl) reportDateEl.value = '';
                if (reportDueDateEl) reportDueDateEl.value = '';
                if (reportNextStepEl) reportNextStepEl.value = '';
                if (commentTextEl) commentTextEl.value = '';

                loadReportsAndComments(appointmentId);

                reportModal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeReportModal() {
                if (!reportModal) return;
                reportModal.classList.remove('is-open');
                currentReportAppointmentId = null;
                document.body.style.overflow = '';
            }

            // -------------------------------------------------
            // AJAX load appointments
            // -------------------------------------------------
            function buildQuery() {
                const params = new URLSearchParams();
                params.set('view', currentView);
                params.set('owner_scope', currentOwnerScope);
                params.set('tab', currentTab);
                params.set('sort', currentSort);
                params.set('direction', currentDirection);

                if (filterSearch && filterSearch.value.trim()) params.set('search', filterSearch.value.trim());
                if (filterFromDate && filterFromDate.value) params.set('from_date', filterFromDate.value);
                if (filterToDate && filterToDate.value) params.set('to_date', filterToDate.value);
                if (filterEmployee && filterEmployee.value) params.set('employee_id', filterEmployee.value);
                if (filterBranch && filterBranch.value) params.set('branch_id', filterBranch.value);

                return params.toString();
            }

            async function loadAppointments() {
                if (!window.ROUTE || !window.ROUTE.appointmentsData) return;

                const query = buildQuery();
                const url = window.ROUTE.appointmentsData + '?' + query;

                try {
                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        showError('Termine konnten nicht geladen werden.');
                        return;
                    }

                    const json = await response.json();
                    appointmentsCache = json.data || json || [];

                    renderKanban();
                    renderList();
                    updateColumnCounts();
                    updateAnalytics();
                    updateHeaderSortState();
                } catch (e) {
                    console.error(e);
                    showError('Fehler beim Laden der Termine.');
                }
            }

            // -------------------------------------------------
            // Kanban / list rendering
            // -------------------------------------------------
            function renderKanban() {
                document.querySelectorAll('.kanban-droppable').forEach(col => {
                    col.innerHTML = '';
                });

                const list = getSortedAppointments();

                list.forEach(a => {
                    const status = a.status || 'planned';
                    const col = document.querySelector('.kanban-droppable[data-status="' + status + '"]');
                    if (!col) return;

                    const card = document.createElement('div');
                    card.className = 'kanban-card';
                    card.dataset.id = a.id;

                    const title = a.name || a.appointment_type || '';
                    const customerName = a.customer_name || '';
                    const customerId = a.customer_id || null;
                    const reportsCount = getReportCountFromAppointment(a);
                    const commentsCount = getCommentCountFromAppointment(a);
                    const ownerBadge = ownerBadgeHtml(a);

                    const customerHtml = customerName && customerId
                        ? `<button type="button" class="kanban-card-customer js-open-customer" data-customer-id="${customerId}">${escapeHtml(customerName)}</button>`
                        : (customerName ? escapeHtml(customerName) : '');

                    card.innerHTML = `
                    <div class="kanban-card-header">
                        <div>
                            <div class="kanban-card-title">${escapeHtml(title)}</div>
                            <div class="kanban-card-subtitle">${customerHtml}</div>
                            ${ownerBadge ? `<div style="margin-top:7px;">${ownerBadge}</div>` : ''}
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
                            ${a.priority ? `<span class="priority-badge ${priorityClass(a.priority)}">${escapeHtml(a.priority)}</span>` : ''}
                            <button class="btn-link-sm js-appointment-profile" data-id="${a.id}">Profil</button>
                            <button class="btn-link-sm js-appointment-edit" data-id="${a.id}">Bearbeiten</button>
                        </div>
                    </div>
                    <div class="kanban-card-meta">
                        <span>${escapeHtml(a.start_date || '')}${a.end_date ? ' → ' + escapeHtml(a.end_date) : ''}</span>
                        <span>${escapeHtml(a.city || '')}</span>
                    </div>
                    <div class="kanban-card-footer">
                        ${employeeAvatarsHtml(a.employees)}
                        <button type="button"
                                class="report-pill js-open-reports"
                                data-id="${a.id}"
                                data-title="${escapeHtml(title)}">
                            <span class="report-pill-counts">
                                ${reportPillInnerHtml(reportsCount, commentsCount)}
                            </span>
                        </button>
                    </div>
                `;

                    col.appendChild(card);
                });

                setupKanbanDragAndDrop();
            }

            function renderList() {
                if (!tableBody) return;
                tableBody.innerHTML = '';

                const list = getSortedAppointments();

                list.forEach(a => {
                    const tr = document.createElement('tr');
                    const title = a.name || a.appointment_type || '';
                    const customerName = a.customer_name || '';
                    const customerId = a.customer_id || null;
                    const reportsCount = getReportCountFromAppointment(a);
                    const commentsCount = getCommentCountFromAppointment(a);
                    const ownerBadge = ownerBadgeHtml(a);

                    const customerHtml = customerName && customerId
                        ? `<button type="button" class="kanban-card-customer js-open-customer" data-customer-id="${customerId}">${escapeHtml(customerName)}</button>`
                        : escapeHtml(customerName);

                    tr.innerHTML = `
                    <td>
                        <div class="appointments-table-title">${escapeHtml(title)}</div>
                        <div class="appointments-table-subtitle">${customerHtml}</div>
                        ${ownerBadge ? `<div style="margin-top:7px;">${ownerBadge}</div>` : ''}
                        <div style="margin-top:8px;">
                            <button type="button"
                                    class="report-pill js-open-reports"
                                    data-id="${a.id}"
                                    data-title="${escapeHtml(title)}">
                                <span class="report-pill-counts">
                                    ${reportPillInnerHtml(reportsCount, commentsCount)}
                                </span>
                            </button>
                        </div>
                    </td>
                    <td>${escapeHtml(a.start_date || '')}${a.end_date ? ' → ' + escapeHtml(a.end_date) : ''}</td>
                    <td>${customerHtml}</td>
                    <td>${escapeHtml([a.street, a.postcode, a.city].filter(Boolean).join(', '))}</td>
                    <td>${employeeAvatarsHtml(a.employees)}</td>
                    <td>
                        <span class="status-badge ${statusClass(a.status)}">${escapeHtml(a.status || '')}</span>
                    </td>
                    <td>${escapeHtml(a.priority || '')}</td>
                    <td class="table-actions">
                        <button class="btn-table btn-table--edit js-appointment-profile" data-id="${a.id}">Profil</button>
                        <button class="btn-table btn-table--edit js-appointment-edit" data-id="${a.id}">Bearbeiten</button>
                    </td>
                `;

                    tableBody.appendChild(tr);
                });
            }

            // -------------------------------------------------
            // Kanban drag & drop
            // -------------------------------------------------
            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.kanban-card:not(.is-dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;

                    if (offset < 0 && offset > closest.offset) {
                        return { offset, element: child };
                    }
                    return closest;
                }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
            }

            async function updateAppointmentStatus(id, status) {
                try {
                    const url = window.ROUTE.appointmentsBase + '/' + id + '/status';
                    const resp = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ status })
                    });

                    if (!resp.ok) {
                        showError('Status konnte nicht aktualisiert werden.');
                        return;
                    }

                    const item = appointmentsCache.find(a => String(a.id) === String(id));
                    if (item) item.status = status;

                    updateColumnCounts();
                    updateAnalytics();
                } catch (e) {
                    console.error(e);
                    showError('Fehler beim Aktualisieren des Status.');
                }
            }

            function setupKanbanDragAndDrop() {
                const cards = document.querySelectorAll('.kanban-card');
                const columns = document.querySelectorAll('.kanban-droppable');
                let draggedId = null;

                cards.forEach(card => {
                    card.setAttribute('draggable', 'true');

                    card.addEventListener('dragstart', e => {
                        draggedId = card.dataset.id;
                        card.classList.add('is-dragging');
                        if (e.dataTransfer) {
                            e.dataTransfer.effectAllowed = 'move';
                            e.dataTransfer.setData('text/plain', draggedId);
                        }
                    });

                    card.addEventListener('dragend', () => {
                        card.classList.remove('is-dragging');
                        draggedId = null;
                    });
                });

                columns.forEach(col => {
                    col.addEventListener('dragover', e => {
                        e.preventDefault();
                        const afterElement = getDragAfterElement(col, e.clientY);
                        const dragging = document.querySelector('.kanban-card.is-dragging');
                        if (!dragging) return;

                        if (afterElement == null) {
                            col.appendChild(dragging);
                        } else {
                            col.insertBefore(dragging, afterElement);
                        }
                    });

                    col.addEventListener('drop', async e => {
                        e.preventDefault();
                        const newStatus = col.dataset.status;
                        const id = draggedId;
                        draggedId = null;

                        const dragging = document.querySelector('.kanban-card.is-dragging');
                        if (dragging) dragging.classList.remove('is-dragging');

                        if (!id || !newStatus) return;
                        await updateAppointmentStatus(id, newStatus);
                    });
                });
            }

            // -------------------------------------------------
            // Analytics
            // -------------------------------------------------
            function updateColumnCounts() {
                const counts = {};
                appointmentsCache.forEach(a => {
                    const status = a.status || 'planned';
                    counts[status] = (counts[status] || 0) + 1;
                });

                document.querySelectorAll('[data-count]').forEach(el => {
                    const status = el.getAttribute('data-count');
                    el.textContent = counts[status] || 0;
                });
            }

            function updateAnalytics() {
                const todayStr = new Date().toISOString().slice(0, 10);

                let all = 0;
                let mine = 0;
                let delayed = 0;
                let due = 0;
                let archived = 0;
                let junk = 0;
                let deleted = 0;

                appointmentsCache.forEach(a => {
                    all++;

                    const status = a.status || '';
                    const start = a.start_date || null;
                    const end = a.end_date || a.start_date || null;

                    if ((a.employees || []).some(e => String(e.id) === String(window.CURRENT_EMPLOYEE_ID))) {
                        mine++;
                    }

                    if (status === 'archived') archived++;
                    if (status === 'junk') junk++;
                    if (status === 'deleted') deleted++;

                    if (start && !['done', 'archived', 'canceled', 'junk'].includes(status)) {
                        if (start <= todayStr && end && end < todayStr) delayed++;
                        if (start <= todayStr && (!end || end >= todayStr)) {
                            if (start === todayStr || (!end || end === todayStr)) due++;
                        }
                    }
                });

                if (analyticsAll) analyticsAll.textContent = all;
                if (analyticsMine) analyticsMine.textContent = mine;
                if (analyticsDelayed) analyticsDelayed.textContent = delayed;
                if (analyticsDue) analyticsDue.textContent = due;
                if (analyticsArchived) analyticsArchived.textContent = archived;
                if (analyticsJunk) analyticsJunk.textContent = junk;
                if (analyticsDeleted) analyticsDeleted.textContent = deleted;
            }

            // -------------------------------------------------
            // Save appointment
            // -------------------------------------------------
            const saveBtn = document.querySelector('.save-task');
            if (saveBtn) {
                saveBtn.addEventListener('click', async function () {
                    const form = document.getElementById('task-store-form');
                    if (!form) return;

                    const formData = new FormData(form);
                    const id = formData.get('id');

                    let url = '';
                    const method = 'POST';

                    if (id) {
                        url = window.ROUTE.appointmentsBase + '/' + id;
                        formData.append('_method', 'PUT');
                    } else {
                        url = window.ROUTE.appointmentsStore;
                    }

                    try {
                        const resp = await fetch(url, {
                            method,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const contentType = resp.headers.get('content-type') || '';
                        let data = null;

                        if (contentType.includes('application/json')) {
                            data = await resp.json().catch(() => null);
                        }

                        if (!resp.ok) {
                            let message = 'Fehler beim Speichern des Termins.';

                            if (resp.status === 422 && data && data.errors) {
                                const messages = [];
                                Object.values(data.errors).forEach(arr => {
                                    (arr || []).forEach(msg => messages.push(msg));
                                });
                                if (messages.length) message = messages.join('\n');
                            } else if (data && data.message) {
                                message = data.message;
                            }

                            showError(message);
                            return;
                        }

                        showSuccess('Der Termin wurde gespeichert.');
                        closeDrawer();
                        await loadAppointments();
                    } catch (e) {
                        console.error(e);
                        showError('Es ist ein Fehler beim Speichern aufgetreten.');
                    }
                });
            }

            // -------------------------------------------------
            // Save report / comment
            // -------------------------------------------------
            if (reportSaveBtn) {
                reportSaveBtn.addEventListener('click', async function () {
                    if (!currentReportAppointmentId) return;

                    const text = (reportTextEl?.value || '').trim();
                    const reportDate = reportDateEl?.value || '';
                    const dueDate = reportDueDateEl?.value || '';
                    const nextStep = (reportNextStepEl?.value || '').trim();

                    if (!text) return;

                    const base = window.ROUTE.appointmentReportsBase.replace(/\/$/, '');
                    const url = base + '/' + encodeURIComponent(currentReportAppointmentId) + '/reports';

                    try {
                        const fd = new FormData();
                        fd.append('report', text);
                        if (reportDate) fd.append('report_date', reportDate);
                        if (dueDate) fd.append('due_date', dueDate);
                        if (nextStep) fd.append('next_step', nextStep);

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: fd
                        });

                        if (!resp.ok) {
                            showError('Report konnte nicht gespeichert werden.');
                            return;
                        }

                        if (reportTextEl) reportTextEl.value = '';
                        if (reportDateEl) reportDateEl.value = '';
                        if (reportDueDateEl) reportDueDateEl.value = '';
                        if (reportNextStepEl) reportNextStepEl.value = '';

                        await loadReportsAndComments(currentReportAppointmentId);
                        showSuccess('Report gespeichert.');
                    } catch (e) {
                        console.error(e);
                        showError('Fehler beim Speichern des Reports.');
                    }
                });
            }

            if (commentSaveBtn) {
                commentSaveBtn.addEventListener('click', async function () {
                    if (!currentReportAppointmentId) return;

                    const text = (commentTextEl?.value || '').trim();
                    if (!text) return;

                    const base = window.ROUTE.appointmentCommentsBase.replace(/\/$/, '');
                    const url = base + '/' + encodeURIComponent(currentReportAppointmentId) + '/comments';

                    try {
                        const fd = new FormData();
                        fd.append('comment', text);

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: fd
                        });

                        if (!resp.ok) {
                            showError('Kommentar konnte nicht gespeichert werden.');
                            return;
                        }

                        if (commentTextEl) commentTextEl.value = '';

                        await loadReportsAndComments(currentReportAppointmentId);
                        showSuccess('Kommentar gespeichert.');
                    } catch (e) {
                        console.error(e);
                        showError('Fehler beim Speichern des Kommentars.');
                    }
                });
            }

            // -------------------------------------------------
            // Notifications ticker
            // -------------------------------------------------
            async function loadAppointmentNotifications() {
                if (!window.ROUTE || !window.ROUTE.appointmentsNotifications) return;

                try {
                    const resp = await fetch(window.ROUTE.appointmentsNotifications + '?limit=10', {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!resp.ok) return;

                    const json = await resp.json();
                    notifState.items = json.data || json || [];
                    if (!Array.isArray(notifState.items)) notifState.items = [];
                    notifState.index = 0;

                    renderNotificationBar();
                    setupNotificationAuto();
                } catch (e) {
                    console.error(e);
                }
            }

            function renderNotificationBar() {
                if (!notifBar) return;

                if (!notifState.items.length) {
                    notifBar.classList.add('is-hidden');
                    clearInterval(notifState.timer);
                    return;
                }

                const item = notifState.items[notifState.index];
                notifBar.classList.remove('is-hidden');

                const title = item.title || 'Termin';
                const message = item.message || '';
                const meta = item.created_at || '';
                const kind = item.kind || 'generic';

                if (notifTitleEl) notifTitleEl.textContent = title;
                if (notifMessageEl) notifMessageEl.textContent = message;
                if (notifMetaEl) notifMetaEl.textContent = meta;

                if (notifKindBadgeEl) {
                    notifKindBadgeEl.textContent = kind === 'due'
                        ? 'Heute fällig'
                        : (kind === 'status' ? 'Status' : 'Aufgabe');

                    notifKindBadgeEl.classList.toggle('app-notification-type-badge--due', kind === 'due');
                }

                notifBar.onclick = null;
                if (item.appointment_id && window.ROUTE && window.ROUTE.appointmentsBase) {
                    notifBar.onclick = function (e) {
                        if (e.target.closest('.app-notification-control')) return;
                        const url = window.ROUTE.appointmentsBase.replace(/\/$/, '') + '/' + item.appointment_id;
                        window.location.href = url;
                    };
                }
            }

            function notifNext() {
                if (!notifState.items.length) return;
                notifState.index = (notifState.index + 1) % notifState.items.length;
                renderNotificationBar();
            }

            function notifPrev() {
                if (!notifState.items.length) return;
                notifState.index = (notifState.index - 1 + notifState.items.length) % notifState.items.length;
                renderNotificationBar();
            }

            function setupNotificationAuto() {
                clearInterval(notifState.timer);
                if (!notifState.playing || !notifState.items.length) return;
                notifState.timer = setInterval(notifNext, notifState.interval);
            }

            // -------------------------------------------------
            // Event bindings
            // -------------------------------------------------
            closeButtons.forEach(btn => btn.addEventListener('click', closeDrawer));
            if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);

            if (btnCreate) {
                btnCreate.addEventListener('click', function () {
                    resetFormForCreate();
                    openDrawer();
                });
            }

            if (customerModalClose) customerModalClose.addEventListener('click', closeCustomerModal);
            if (customerModalBackdrop) customerModalBackdrop.addEventListener('click', closeCustomerModal);

            if (reportModalClose) reportModalClose.addEventListener('click', closeReportModal);
            if (reportModalBackdrop) reportModalBackdrop.addEventListener('click', closeReportModal);

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;

                if (drawer && drawer.classList.contains('is-open')) closeDrawer();
                if (customerModal && customerModal.classList.contains('is-open')) closeCustomerModal();
                if (reportModal && reportModal.classList.contains('is-open')) closeReportModal();
            });

            sortableHeaders.forEach(th => {
                th.addEventListener('click', () => {
                    const key = th.dataset.sort;
                    if (!key) return;

                    if (currentSort === key) {
                        currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort = key;
                        currentDirection = 'asc';
                    }

                    updateHeaderSortState();
                    renderList();
                    renderKanban();
                });
            });

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabButtons.forEach(b => b.classList.remove('tab-button--active'));
                    btn.classList.add('tab-button--active');

                    currentTab = btn.dataset.tab || 'all';
                    loadAppointments();
                });
            });

            ownerFilterButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    ownerFilterButtons.forEach(b => b.classList.remove('owner-filter-btn--active'));
                    btn.classList.add('owner-filter-btn--active');

                    currentOwnerScope = btn.dataset.ownerScope || 'assigned_to_me';
                    loadAppointments();
                });
            });

            viewButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    viewButtons.forEach(b => b.classList.remove('view-switch--active'));
                    btn.classList.add('view-switch--active');

                    currentView = btn.dataset.view || 'kanban';

                    if (currentView === 'kanban') {
                        kanbanView.classList.remove('is-hidden');
                        listView.classList.add('is-hidden');
                    } else {
                        kanbanView.classList.add('is-hidden');
                        listView.classList.remove('is-hidden');
                    }
                });
            });

            [filterSearch, filterFromDate, filterToDate, filterEmployee, filterBranch].forEach(el => {
                if (!el) return;

                if (el === filterSearch) {
                    let timer = null;
                    el.addEventListener('input', function () {
                        clearTimeout(timer);
                        timer = setTimeout(loadAppointments, 250);
                    });
                } else {
                    el.addEventListener('change', loadAppointments);
                }
            });

            if (btnResetAppointmentFilters) {
                btnResetAppointmentFilters.addEventListener('click', function () {
                    if (filterSearch) filterSearch.value = '';
                    if (filterFromDate) filterFromDate.value = '';
                    if (filterToDate) filterToDate.value = '';
                    if (filterEmployee) filterEmployee.value = '';
                    if (filterBranch) filterBranch.value = '';

                    currentOwnerScope = 'assigned_to_me';
                    ownerFilterButtons.forEach(btn => {
                        btn.classList.toggle('owner-filter-btn--active', btn.dataset.ownerScope === 'assigned_to_me');
                    });

                    if (window.jQuery) {
                        $('#filterEmployee').trigger('change');
                        $('#filterBranch').trigger('change');
                    }

                    loadAppointments();
                });
            }

            document.querySelectorAll('[data-notif-action]').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const action = btn.getAttribute('data-notif-action');

                    if (action === 'prev') notifPrev();
                    if (action === 'next') notifNext();

                    if (action === 'toggle') {
                        notifState.playing = !notifState.playing;
                        if (notifPlayPauseEl) notifPlayPauseEl.textContent = notifState.playing ? '❚❚' : '▶';
                        setupNotificationAuto();
                    }

                    e.stopPropagation();
                });
            });

            document.addEventListener('click', function (e) {
                const customerBtn = e.target.closest('.js-open-customer');
                if (customerBtn) {
                    openCustomerModal(customerBtn.dataset.customerId, customerBtn.textContent.trim());
                    return;
                }

                const reportBtn = e.target.closest('.js-open-reports');
                if (reportBtn) {
                    const id = reportBtn.dataset.id;
                    const title = reportBtn.dataset.title || '';
                    if (id) openReportModal(id, title);
                    return;
                }

                const profileBtn = e.target.closest('.js-appointment-profile');
                if (profileBtn) {
                    const id = profileBtn.dataset.id;
                    if (id && window.ROUTE && window.ROUTE.appointmentsBase) {
                        const base = window.ROUTE.appointmentsBase.replace(/\/$/, '');
                        window.location.href = base + '/' + encodeURIComponent(id);
                    }
                    return;
                }

                const editBtn = e.target.closest('.js-appointment-edit');
                if (editBtn) {
                    const id = editBtn.dataset.id;
                    const appointment = appointmentsCache.find(a => String(a.id) === String(id));
                    if (!appointment) {
                        showError('Termin konnte nicht geladen werden.');
                        return;
                    }
                    openFormForEdit(appointment);
                }
            });

            // -------------------------------------------------
            // jQuery / Select2 init
            // -------------------------------------------------
            $(function () {
                if (!$.fn.select2) {
                    console.error('Select2 not found – prüfe, ob js/select2.min.js nach jQuery geladen wird.');
                } else {
                    $('#employee').select2({
                        placeholder: 'Teilnehmer auswählen',
                        width: '100%'
                    });

                    if (window.FAVORITE_EMPLOYEE_IDS && window.FAVORITE_EMPLOYEE_IDS.length) {
                        $('#employee').val(window.FAVORITE_EMPLOYEE_IDS.map(String)).trigger('change');
                    }

                    $('#branch_id').select2({
                        placeholder: 'Betrieb auswählen',
                        width: '100%'
                    });

                    $('#customer_id').select2({
                        placeholder: 'Kontakt auswählen',
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: window.ROUTE.contactList,
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return { search: params.term || '' };
                            },
                            processResults: function (data) {
                                let rows = [];
                                if (data && Array.isArray(data.data)) {
                                    rows = data.data;
                                } else if (Array.isArray(data)) {
                                    rows = data;
                                }

                                return {
                                    results: rows.map(function (item) {
                                        const id = item.main_id != null ? item.main_id : item.id;
                                        let text = ((item.name || '') + ' ' + (item.lastname || '')).trim();
                                        if (!text) text = 'Kontakt #' + id;
                                        if (item.type) text += ' – ' + item.type;

                                        return { id, text, raw: item };
                                    })
                                };
                            },
                            cache: true
                        }
                    }).on('select2:select', function (e) {
                        const selected = e.params.data || {};
                        const raw = selected.raw || selected;

                        $('#contact_type').val(raw.type || '');
                        $('#name').val([raw.name, raw.lastname].filter(Boolean).join(' '));

                        if (raw.phone) $('#phone').val(raw.phone);
                        if (raw.email) $('#email').val(raw.email);

                        if (raw.street || raw.city || raw.postcode) {
                            const addr = [raw.street, raw.postcode, raw.city].filter(Boolean).join(', ');
                            $('#full_address').val(addr);
                            $('#street-input').val(raw.street || '');
                            $('#city-input').val(raw.city || '');
                            $('#postal_code-input').val(raw.postcode || '');
                            $('#latitude-input').val(raw.latitude || '');
                            $('#longitude-input').val(raw.longitude || '');
                        }
                    });
                }

                $('.contact-type-toggle').on('change', function () {
                    const mode = $(this).val();
                    $('#contact_mode').val(mode);

                    if (mode === 'new') {
                        $('.contact-name-block').removeClass('d-none');
                        $('.contact-select-block').addClass('d-none');
                    } else {
                        $('.contact-name-block').addClass('d-none');
                        $('.contact-select-block').removeClass('d-none');
                    }
                });

                $('#execution_type').on('change', function () {
                    const v = $(this).val();

                    if (v === 'internal') {
                        $('#intern').show();
                        $('#extern').hide();
                    } else {
                        $('#intern').hide();
                        $('#extern').show();
                    }

                    if (v === 'online') {
                        $('#link_section').show();
                    } else {
                        $('#link_section').hide();
                    }
                }).trigger('change');

                $('#color_drop_down .dropdown-item').on('click', function () {
                    const val = $(this).data('value');
                    $('#color').val(val);
                    $('#colorIcon').css('color', val);
                });

                loadAppointments();
                loadAppointmentNotifications();
            });

            // Expose
            window.openAppointmentForEdit = openFormForEdit;
        })();
    </script>

    {{-- Google Places für full_address --}}
    <script>
        function initAddressAutocomplete() {
            var input = document.getElementById('full_address');
            if (!input || !window.google || !google.maps || !google.maps.places) {
                return;
            }

            var autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'],
                componentRestrictions: { country: 'de' }
            });

            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place || !place.address_components) return;

                var street = '', streetNumber = '', city = '', postcode = '';

                place.address_components.forEach(function (component) {
                    var types = component.types || [];
                    if (types.indexOf('route') > -1) street = component.long_name;
                    if (types.indexOf('street_number') > -1) streetNumber = component.long_name;
                    if (types.indexOf('locality') > -1) city = component.long_name;
                    if (types.indexOf('postal_code') > -1) postcode = component.long_name;
                });

                var streetFull = [street, streetNumber].filter(Boolean).join(' ');
                var full = [streetFull, postcode, city].filter(Boolean).join(', ');

                $('#full_address').val(full);
                $('#street-input').val(streetFull);
                $('#city-input').val(city);
                $('#postal_code-input').val(postcode);

                if (place.geometry && place.geometry.location) {
                    $('#latitude-input').val(place.geometry.location.lat());
                    $('#longitude-input').val(place.geometry.location.lng());
                }
            });
        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&language=de&callback=initAddressAutocomplete"
        async defer></script>
@endsection

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Terminliste',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush