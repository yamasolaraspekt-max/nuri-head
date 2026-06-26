@extends('admin.layouts.app')
@section('title', 'Projektplanung')

@php
    $pageTitle = 'PROJEKTPLANUNG';
    $pageSubtitle = 'Montage-Projekte nach Kunde, Objekt, Produkt, Team, Terminen, Aufgaben, Tickets und Fortschritt verwalten.';
    $plannerCockpitUrl = \Illuminate\Support\Facades\Route::has('planner.cockpit')
        ? route('planner.cockpit')
        : url('/planner/cockpit');
@endphp

@push('style')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kanban.css') }}?v={{ time() }}">
@endpush

@once
    @push('style')
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
                --purple: #8b5cf6;
                --purple-light: #f5f3ff;
                --gray: #6b7280;
                --gray-light: #f3f4f6;
                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
                --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
                --shadow-float: 0 28px 80px rgba(15, 23, 42, .24);
                --radius: 14px;
                --transition: all .2s ease-in-out;
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
                font-weight: 900;
                letter-spacing: -.025em;
                color: #111827;
                text-transform: uppercase;
            }

            .oc-sub {
                font-size: 14px;
                color: var(--text-muted);
                margin-top: 4px;
                max-width: 820px;
                line-height: 1.45;
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
                font-weight: 800;
            }

            .oc-breadcrumb a:hover {
                color: var(--text-main);
            }

            .oc-breadcrumb span.current {
                color: #111827;
                font-weight: 900;
            }

            .oc-inline-actions {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }

            .oc-btn,
            .oc-btn-soft,
            .oc-btn-ic {
                transition: var(--transition);
            }

            .oc-btn {
                background: var(--primary);
                color: #fff;
                border: none;
                padding: 10px 16px;
                border-radius: 10px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                white-space: nowrap;
            }

            .oc-btn:hover {
                background: var(--primary-hover);
                color: #fff;
                text-decoration: none;
            }

            .oc-btn:disabled {
                opacity: .65;
                cursor: not-allowed;
            }

            .oc-btn-soft {
                background: #fff;
                color: var(--text-main);
                border: 1px solid var(--border);
                padding: 10px 14px;
                border-radius: 10px;
                font-weight: 850;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                white-space: nowrap;
            }

            .oc-btn-soft:hover {
                background: #f9fafb;
                color: var(--text-main);
                text-decoration: none;
            }

            .oc-btn-soft.is-active {
                background: var(--primary-light);
                border-color: rgba(147, 194, 28, .45);
                color: #5f870f;
            }

            .oc-btn-ic {
                width: 36px;
                height: 36px;
                border-radius: 9px;
                border: 1px solid var(--border);
                background: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: var(--text-muted);
                cursor: pointer;
                text-decoration: none;
                flex: 0 0 auto;
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
                background: var(--primary-light);
            }

            .oc-btn-ic.info {
                color: var(--blue);
                border-color: rgba(116, 178, 212, .20);
                background: var(--blue-light);
            }

            .oc-btn-ic.success {
                color: var(--success);
                border-color: #c7f2df;
                background: var(--success-light);
            }

            .oc-btn-ic.warning {
                color: #d97706;
                border-color: #fde7b0;
                background: #fffbeb;
            }

            .oc-btn-ic.danger {
                color: var(--danger);
                border-color: rgba(239, 68, 68, .18);
                background: var(--danger-light);
            }

            .oc-analytics {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 18px;
            }

            @media(max-width:1400px) {
                .oc-analytics {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media(max-width:900px) {
                .oc-analytics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:620px) {
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
                color: var(--blue);
            }

            .oc-stat-icon.project {
                background: var(--purple-light);
                color: var(--purple);
            }

            .oc-stat-icon.tasks {
                background: var(--success-light);
                color: var(--success);
            }

            .oc-stat-icon.tickets {
                background: var(--warning-light);
                color: #d97706;
            }

            .oc-stat-icon.progress {
                background: var(--primary-light);
                color: var(--primary);
            }

            .oc-stat-meta {
                min-width: 0;
            }

            .oc-stat-label {
                font-size: 11px;
                font-weight: 900;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .oc-stat-value {
                font-size: 24px;
                font-weight: 950;
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
                min-width: 180px;
            }

            .oc-filter-block.search {
                flex: 1;
                min-width: 280px;
            }

            .oc-filter-label {
                font-size: 11px;
                font-weight: 900;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .oc-input {
                background: #f9fafb;
                border: 1px solid var(--border);
                border-radius: 8px;
                padding: 10px 12px 10px 36px;
                font-size: 14px;
                outline: none;
                transition: var(--transition);
                min-width: 240px;
                width: 100%;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: 10px center;
                background-size: 16px;
            }

            .oc-select {
                width: 100%;
                background: #f9fafb;
                border: 1px solid var(--border);
                border-radius: 8px;
                padding: 10px 12px;
                font-size: 14px;
                outline: none;
                transition: var(--transition);
                min-width: 180px;
            }

            .oc-input:focus,
            .oc-select:focus {
                background: #fff;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px var(--primary-light);
            }

            .oc-view-tabs {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                align-items: center;
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
                grid-template-columns: 72px minmax(250px, 1.45fr) minmax(190px, 1fr) minmax(140px, .75fr) minmax(150px, .85fr) minmax(180px, 1fr) 150px 190px;
                gap: 14px;
                align-items: center;
                padding: 16px 16px 10px;
                color: var(--text-muted);
                font-size: 11px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            @media(max-width:1420px) {
                .oc-list-head {
                    display: none;
                }
            }

            .oc-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 0 0 16px;
            }

            .oc-item {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                transition: var(--transition);
                overflow: hidden;
                margin: 0 16px;
            }

            .oc-item:hover {
                border-color: var(--primary);
                box-shadow: var(--shadow);
            }

            .oc-item-row {
                padding: 16px;
                display: grid;
                gap: 16px;
                align-items: center;
                grid-template-columns: 72px minmax(250px, 1.45fr) minmax(190px, 1fr) minmax(140px, .75fr) minmax(150px, .85fr) minmax(180px, 1fr) 150px 190px;
            }

            @media(max-width:1420px) {
                .oc-item-row {
                    grid-template-columns: 1fr;
                }
            }

            .oc-cell {
                min-width: 0;
            }

            .oc-cell-title {
                font-size: 11px;
                font-weight: 900;
                color: var(--text-muted);
                text-transform: uppercase;
                margin-bottom: 4px;
                display: none;
            }

            @media(max-width:1420px) {
                .oc-cell-title {
                    display: block;
                }
            }

            .oc-id-badge {
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
                font-weight: 950;
            }

            .oc-main {
                display: flex;
                flex-direction: column;
                min-width: 0;
            }

            .oc-ttl {
                font-weight: 900;
                font-size: 15px;
                margin-bottom: 4px;
                color: #111827;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .oc-subt {
                font-size: 13px;
                color: var(--text-muted);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.35;
            }

            .oc-product {
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 0;
            }

            .oc-product-img,
            .oc-product-placeholder {
                width: 54px;
                height: 54px;
                border-radius: 14px;
                border: 1px solid var(--border);
                background: #fff;
                flex: 0 0 auto;
            }

            .oc-product-img {
                object-fit: contain;
                padding: 6px;
            }

            .oc-product-placeholder {
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--blue-light), var(--primary-light));
                color: var(--blue);
                font-weight: 950;
            }

            .oc-status-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 950;
                white-space: nowrap;
                max-width: 100%;
            }

            .oc-status-pill.green {
                background: #ecfdf5;
                color: #047857;
            }

            .oc-status-pill.orange {
                background: #fffbeb;
                color: #b45309;
            }

            .oc-status-pill.blue {
                background: var(--blue-light);
                color: #0369a1;
            }

            .oc-status-pill.purple {
                background: var(--purple-light);
                color: #6d28d9;
            }

            .oc-status-pill.gray {
                background: #f3f4f6;
                color: #4b5563;
            }

            .oc-team {
                display: flex;
                align-items: center;
                min-height: 36px;
            }

            .oc-avatar,
            .oc-avatar-initial,
            .oc-team-more {
                width: 34px;
                height: 34px;
                border-radius: 999px;
                border: 2px solid #fff;
                margin-right: -8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
            }

            .oc-avatar {
                object-fit: cover;
                background: #e5e7eb;
            }

            .oc-avatar-initial,
            .oc-team-more {
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 950;
            }

            .oc-avatar-initial {
                background: #111827;
                color: #fff;
            }

            .oc-team-more {
                min-width: 34px;
                padding: 0 8px;
                background: #f3f4f6;
                color: #374151;
            }


            .oc-product-media {
                width: 54px;
                height: 54px;
                border-radius: 14px;
                flex: 0 0 auto;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .oc-product-media .oc-product-img,
            .oc-product-media .oc-product-placeholder {
                margin: 0;
            }

            .oc-product-placeholder {
                text-transform: uppercase;
                letter-spacing: .04em;
            }

            .oc-avatar-wrap {
                width: 34px;
                height: 34px;
                border-radius: 999px;
                border: 2px solid #fff;
                margin-right: -8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .12);
                background: #e5e7eb;
                flex: 0 0 auto;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                position: relative;
            }

            .oc-avatar-wrap .oc-avatar,
            .oc-avatar-wrap .oc-avatar-initial {
                width: 100%;
                height: 100%;
                border: 0;
                margin: 0;
                box-shadow: none;
            }

            .oc-avatar-wrap .oc-avatar {
                display: block;
                object-fit: cover;
            }

            .oc-avatar-wrap .oc-avatar-initial {
                display: none;
            }

            .oc-module-grid {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 6px;
            }

            .oc-module-box {
                border: 1px solid var(--border);
                border-radius: 10px;
                background: #f9fafb;
                padding: 6px 4px;
                text-align: center;
                min-width: 0;
            }

            .oc-module-box strong {
                display: block;
                font-size: 13px;
                font-weight: 950;
                color: #111827;
                line-height: 1;
            }

            .oc-module-box span {
                display: block;
                font-size: 9px;
                font-weight: 950;
                color: var(--text-muted);
                text-transform: uppercase;
                margin-top: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .oc-progress-wrap {
                min-width: 0;
            }

            .oc-progress-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                font-size: 12px;
                font-weight: 950;
                color: var(--text-muted);
                margin-bottom: 6px;
            }

            .oc-progress-track {
                height: 8px;
                border-radius: 999px;
                background: #e5e7eb;
                overflow: hidden;
            }

            .oc-progress-bar {
                height: 100%;
                border-radius: 999px;
                background: var(--primary);
                transition: width .3s ease;
            }

            .oc-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                flex-wrap: wrap;
            }

            .oc-empty {
                width: 100%;
                text-align: center;
                padding: 60px;
                color: var(--text-muted);
                background: #fff;
                border: 1px dashed var(--border);
                border-radius: 16px;
                margin: 16px;
            }

            .oc-loading {
                text-align: center;
                padding: 34px;
                color: var(--text-muted);
                font-weight: 900;
            }

            .oc-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
                padding: 16px;
            }

            @media(max-width:1400px) {
                .oc-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:800px) {
                .oc-grid {
                    grid-template-columns: 1fr;
                }
            }

            .oc-project-card {
                border: 1px solid var(--border);
                border-radius: 16px;
                background: #fff;
                padding: 16px;
                box-shadow: var(--shadow-sm);
                transition: var(--transition);
            }

            .oc-project-card:hover {
                border-color: var(--primary);
                box-shadow: var(--shadow);
            }

            .oc-project-top {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: flex-start;
                margin-bottom: 12px;
            }

            .oc-project-meta {
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 12px 0;
                padding: 10px;
                border: 1px solid var(--border);
                border-radius: 14px;
                background: #f9fafb;
            }

            /* Main Kanban style wrapper */
            .pp-kanban-view {
                display: none;
                padding: 0;
                background: #fff;
            }

            .kanban-zoom-card {
                --kb-zoom: 1;
                background: #fff;
                border: 0;
                border-radius: 0;
                overflow: hidden;
            }

            .kanban-zoom-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 10px 12px;
                border-bottom: 1px solid #e5eef5;
                background: linear-gradient(135deg, #f8fafc 0%, #eef7fb 100%);
                flex-wrap: wrap;
            }

            .kanban-zoom-title {
                display: block;
                font-size: 13px;
                font-weight: 950;
                color: #0f172a;
            }

            .kanban-zoom-sub {
                display: block;
                font-size: 11px;
                font-weight: 750;
                color: #64748b;
                margin-top: 2px;
            }

            .kanban-zoom-actions {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            .kbz-btn {
                border: 1px solid #dbeafe;
                background: #fff;
                color: #334155;
                border-radius: 12px;
                height: 32px;
                min-width: 42px;
                padding: 0 10px;
                font-size: 12px;
                font-weight: 950;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                transition: .15s ease;
            }

            .kbz-btn:hover,
            .kbz-btn.is-active {
                background: #74b2d4;
                color: #fff;
                border-color: #74b2d4;
                transform: translateY(-1px);
            }

            .kanban-zoom-area {
                overflow: auto;
                width: 100%;
                min-height: calc(100vh - 330px);
                padding: 8px;
                background: #f8fafc;
            }

            .kanban-zoom-area .kanban-container {
                zoom: var(--kb-zoom);
                min-width: max-content;
            }

            .pp-kanban-card.is-dragging {
                opacity: .55;
                transform: rotate(1deg);
            }

            .pp-kanban-dropzone.is-over {
                background: rgba(147, 194, 28, .10);
                outline: 2px dashed rgba(147, 194, 28, .55);
                outline-offset: -6px;
            }

            /* Drawers and modals */
            .oc-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 2200;
                background: rgba(17, 24, 39, .55);
                backdrop-filter: blur(3px);
                opacity: 0;
                pointer-events: none;
                transition: opacity .22s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 18px;
            }

            .oc-modal-backdrop.open {
                opacity: 1;
                pointer-events: auto;
            }

            .oc-modal {
                width: 100%;
                max-width: 620px;
                background: #fff;
                border: 1px solid rgba(229, 231, 235, .9);
                border-radius: 16px;
                box-shadow: var(--shadow-float);
                transform: translateY(12px) scale(.985);
                transition: transform .22s ease;
                overflow: hidden;
            }

            .oc-modal-backdrop.open .oc-modal {
                transform: translateY(0) scale(1);
            }

            .oc-modal-h {
                display: flex;
                gap: 12px;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                border-bottom: 1px solid var(--border);
                background: #fafafa;
            }

            .oc-modal-ttl {
                font-weight: 950;
                font-size: 16px;
                line-height: 1.2;
                margin: 0;
                color: #111827;
            }

            .oc-modal-b {
                padding: 20px 18px;
                max-height: 72vh;
                overflow-y: auto;
            }

            .oc-modal-f {
                padding: 14px 18px;
                border-top: 1px solid var(--border);
                background: #fafafa;
                display: flex;
                gap: 10px;
                justify-content: flex-end;
                flex-wrap: wrap;
            }

            .oc-form-group {
                margin-bottom: 16px;
            }

            .oc-label {
                display: block;
                font-size: 13px;
                font-weight: 850;
                color: var(--text-main);
                margin-bottom: 6px;
            }

            .oc-help {
                font-size: 12px;
                color: var(--text-muted);
                margin-top: 6px;
            }

            .oc-input-form,
            .oc-textarea {
                width: 100%;
                padding: 10px 12px;
                border-radius: 8px;
                border: 1px solid var(--border);
                background: #fff;
                font-size: 14px;
                outline: none;
                transition: var(--transition);
            }

            .oc-textarea {
                min-height: 120px;
                resize: vertical;
            }

            .oc-input-form:focus,
            .oc-textarea:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 3px var(--primary-light);
            }

            .oc-warning-box {
                margin-top: 12px;
                border: 1px solid rgba(245, 158, 11, .35);
                background: #fffbeb;
                color: #92400e;
                border-radius: 12px;
                padding: 12px;
                font-size: 13px;
                font-weight: 850;
            }

            .pp-drawer-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .36);
                z-index: 2300;
                opacity: 0;
                visibility: hidden;
                transition: .2s ease;
            }

            .pp-drawer-backdrop.open {
                opacity: 1;
                visibility: visible;
            }

            .pp-history-drawer {
                position: fixed;
                top: 0;
                right: 0;
                width: min(560px, 96vw);
                height: 100vh;
                background: #f8fafc;
                z-index: 2301;
                box-shadow: -24px 0 70px rgba(15, 23, 42, .25);
                transform: translateX(105%);
                transition: transform .24s ease;
                display: flex;
                flex-direction: column;
                border-left: 1px solid #dbeafe;
            }

            .pp-history-drawer.open {
                transform: translateX(0);
            }

            .pp-history-head {
                padding: 16px 18px;
                background: linear-gradient(135deg, #ffffff, #eef7fb);
                border-bottom: 1px solid #dbeafe;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .pp-history-title {
                margin: 0;
                font-size: 17px;
                font-weight: 950;
                color: #0f172a;
            }

            .pp-history-sub {
                margin-top: 4px;
                font-size: 12px;
                font-weight: 750;
                color: #64748b;
                line-height: 1.4;
            }

            .pp-history-body {
                padding: 16px 18px;
                overflow-y: auto;
                flex: 1;
            }

            .pp-latest-job {
                background: #fff;
                border: 1px solid #dbeafe;
                border-radius: 16px;
                padding: 14px;
                margin-bottom: 16px;
                box-shadow: var(--shadow-sm);
            }

            .pp-latest-job-label {
                font-size: 11px;
                color: #64748b;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .pp-latest-job-title {
                margin-top: 5px;
                font-size: 15px;
                color: #0f172a;
                font-weight: 950;
                line-height: 1.35;
            }

            .pp-latest-job-meta {
                margin-top: 6px;
                font-size: 12px;
                color: #64748b;
                font-weight: 750;
            }

            .pp-timeline {
                position: relative;
                padding-left: 20px;
            }

            .pp-timeline:before {
                content: "";
                position: absolute;
                left: 7px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #dbeafe;
            }

            .pp-timeline-item {
                position: relative;
                background: #fff;
                border: 1px solid #e5eef8;
                border-radius: 15px;
                padding: 12px 12px 12px 14px;
                margin-bottom: 12px;
                box-shadow: var(--shadow-sm);
            }

            .pp-timeline-item:before {
                content: "";
                position: absolute;
                left: -18px;
                top: 15px;
                width: 12px;
                height: 12px;
                border-radius: 999px;
                background: #93c21c;
                border: 3px solid #fff;
                box-shadow: 0 0 0 1px #93c21c;
            }

            .pp-timeline-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 10px;
            }

            .pp-timeline-title {
                font-size: 13px;
                font-weight: 950;
                color: #0f172a;
                line-height: 1.35;
            }

            .pp-timeline-date {
                font-size: 11px;
                font-weight: 850;
                color: #64748b;
                text-align: right;
                white-space: nowrap;
            }

            .pp-timeline-text {
                margin-top: 6px;
                font-size: 12px;
                color: #334155;
                line-height: 1.45;
                font-weight: 750;
            }

            .pp-timeline-reason {
                margin-top: 8px;
                border-left: 3px solid #93c21c;
                background: #f7fbef;
                border-radius: 10px;
                padding: 8px 10px;
                font-size: 12px;
                color: #365314;
                font-weight: 800;
            }

            .pp-timeline-diff {
                margin-top: 8px;
                display: inline-flex;
                padding: 4px 8px;
                border-radius: 999px;
                background: #eef7fb;
                color: #0369a1;
                font-size: 11px;
                font-weight: 950;
            }

            .oc-pagination {
                margin-top: 18px;
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 14px 16px;
                box-shadow: var(--shadow-sm);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .oc-page-info {
                font-size: 12px;
                color: var(--text-muted);
                font-weight: 850;
            }

            .oc-page-actions {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .oc-toast-wrap {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 2600;
                display: flex;
                flex-direction: column;
                gap: 10px;
                pointer-events: none;
            }

            .oc-toast {
                pointer-events: auto;
                min-width: 280px;
                max-width: 380px;
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 14px;
                box-shadow: var(--shadow);
                padding: 12px;
                display: flex;
                gap: 10px;
                align-items: flex-start;
                animation: ocToastIn .25s ease forwards;
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
                font-weight: 950;
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


            /* PROJECT COCKPIT RESPONSIVE LATEST ACTIVITY + ACTION MENU */
            .oc-card,
            .oc-list,
            .oc-item,
            .oc-item-row,
            .oc-cell {
                overflow: visible;
            }

            .oc-list-head,
            .oc-item-row {
                grid-template-columns:
                    72px minmax(220px, 1.25fr) minmax(170px, .9fr) minmax(120px, .65fr) minmax(130px, .75fr) minmax(150px, .85fr) minmax(130px, .65fr) minmax(230px, 1.05fr) minmax(132px, auto) !important;
            }

            .oc-cell-latest,
            .oc-cell-actions {
                position: relative;
                z-index: 20;
                overflow: visible;
            }

            .oc-cell-actions {
                z-index: 45;
            }

            .oc-latest-activity {
                display: grid;
                grid-template-columns: 34px minmax(0, 1fr);
                gap: 9px;
                align-items: center;
                min-width: 0;
                border: 1px solid #e5e7eb;
                background: linear-gradient(135deg, #ffffff, #f8fafc);
                border-radius: 13px;
                padding: 9px 10px;
                box-shadow: 0 10px 22px -20px rgba(15, 23, 42, .58);
            }

            .oc-latest-activity-icon {
                width: 34px;
                height: 34px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #eff6ff;
                color: #2563eb;
                font-weight: 950;
                flex: 0 0 auto;
            }

            .oc-latest-activity.is-done .oc-latest-activity-icon {
                background: #ecfdf5;
                color: #047857;
            }

            .oc-latest-activity.is-warning .oc-latest-activity-icon {
                background: #fffbeb;
                color: #b45309;
            }

            .oc-latest-activity-title,
            .oc-latest-activity-meta,
            .oc-latest-activity-desc {
                display: block;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .oc-latest-activity-title {
                color: #0f172a;
                font-size: 12px;
                font-weight: 950;
                line-height: 1.25;
            }

            .oc-latest-activity-meta {
                margin-top: 2px;
                color: #64748b;
                font-size: 10px;
                font-weight: 850;
            }

            .oc-latest-activity-desc {
                margin-top: 2px;
                color: #475569;
                font-size: 10px;
                font-weight: 800;
            }

            .oc-actions {
                position: relative;
                z-index: 2;
                display: grid;
                grid-template-columns: 42px minmax(150px, 1fr);
                align-items: start;
                justify-content: stretch;
                gap: 8px;
                overflow: visible;
                pointer-events: auto;
                min-width: 0;
            }

            .oc-action-menu {
                position: static;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                width: 100%;
                min-width: 0;
                z-index: 2;
            }

            .oc-action-menu-toggle {
                width: 100%;
                min-height: 38px;
                border: 1px solid #e5e7eb;
                background: #ffffff;
                color: #0f172a;
                border-radius: 12px;
                padding: 0 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                font-family: inherit;
                font-size: 12px;
                font-weight: 950;
                cursor: pointer;
                white-space: nowrap;
            }

            .oc-action-menu-toggle:hover,
            .oc-action-menu.is-open .oc-action-menu-toggle {
                background: #f4fae7;
                border-color: rgba(147, 194, 28, .5);
                color: #5f870f;
            }

            .oc-action-menu-panel {
                position: static;
                right: auto;
                top: auto;
                width: 100%;
                min-width: 220px;
                margin-top: 7px;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                box-shadow: 0 14px 38px rgba(15, 23, 42, .14);
                padding: 7px;
                display: none;
                gap: 5px;
                z-index: 2;
            }

            .oc-action-menu.is-open .oc-action-menu-panel {
                display: grid;
            }

            .oc-action-menu-panel a,
            .oc-action-menu-panel button {
                width: 100%;
                min-height: 38px;
                border: 0;
                background: transparent;
                border-radius: 11px;
                padding: 0 10px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #0f172a;
                font-family: inherit;
                font-size: 12px;
                font-weight: 900;
                text-decoration: none;
                cursor: pointer;
                text-align: left;
            }

            .oc-action-menu-panel a:hover,
            .oc-action-menu-panel button:hover {
                background: #f8fafc;
            }

            .oc-project-card .oc-latest-activity {
                margin-top: 12px;
            }

            .pp-kanban-card .oc-latest-activity {
                margin-top: 9px;
                grid-template-columns: 28px minmax(0, 1fr);
                padding: 7px;
            }

            .pp-kanban-card .oc-latest-activity-icon {
                width: 28px;
                height: 28px;
                border-radius: 9px;
                font-size: 11px;
            }

            @media(max-width: 1580px) {
                .oc-list-head {
                    display: none !important;
                }

                .oc-item-row {
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }

                .oc-cell-actions,
                .oc-cell-latest {
                    grid-column: 1 / -1;
                }

                .oc-actions {
                    justify-content: stretch;
                }

                .oc-action-menu-panel {
                    left: auto;
                    right: auto;
                }
            }

            @media(max-width: 900px) {

                .oc-titlebar,
                .oc-toolbar,
                .oc-toolbar-left,
                .oc-toolbar-right,
                .oc-inline-actions,
                .oc-view-tabs {
                    align-items: stretch;
                    width: 100%;
                }

                .oc-toolbar-left,
                .oc-toolbar-right,
                .oc-inline-actions,
                .oc-view-tabs {
                    display: grid;
                    grid-template-columns: 1fr;
                }

                .oc-filter-block,
                .oc-filter-block.search,
                .oc-input,
                .oc-select,
                .oc-btn,
                .oc-btn-soft {
                    width: 100%;
                    min-width: 0;
                }

                .oc-item-row {
                    grid-template-columns: 1fr !important;
                    gap: 12px;
                }

                .oc-actions {
                    width: 100%;
                    display: grid;
                    grid-template-columns: 42px minmax(0, 1fr);
                }

                .oc-action-menu,
                .oc-action-menu-toggle {
                    width: 100%;
                }

                .oc-action-menu-toggle {
                    justify-content: center;
                }

                .oc-action-menu-panel {
                    position: static;
                    width: 100%;
                    min-width: 0;
                    margin-top: 7px;
                    box-shadow: none;
                }
            }

            @media(max-width: 720px) {
                .oc-item {
                    margin: 0 10px;
                }

                .oc-latest-activity {
                    grid-template-columns: 30px minmax(0, 1fr);
                    padding: 8px;
                }

                .oc-latest-activity-icon {
                    width: 30px;
                    height: 30px;
                    border-radius: 10px;
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
                    <div class="oc-title">{{ $pageTitle }}</div>
                    <div class="oc-sub">{{ $pageSubtitle }}</div>
                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Home</a>
                        <span>›</span>
                        <span>Projekte</span>
                        <span>›</span>
                        <span class="current">Projektplanung</span>
                    </div>
                </div>
                <div class="oc-inline-actions">
                    <button type="button" class="oc-btn" id="ppAddProjectBtn">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        Projekt hinzufügen
                    </button>
                    <button type="button" class="oc-btn-soft" data-view="kanban">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="18" rx="2" />
                            <rect x="14" y="3" width="7" height="18" rx="2" />
                        </svg>
                        Plan-Tafel
                    </button>
                    <button type="button" class="oc-btn-soft" id="ppRefresh">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 1 1-2.64-6.36" />
                            <path d="M21 3v6h-6" />
                        </svg>
                        Aktualisieren
                    </button>
                </div>
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total"><svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18" />
                    </svg></div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Projekte</div>
                    <div class="oc-stat-value" data-stat="projects">0</div>
                    <div class="oc-stat-sub">Montage-Projekte</div>
                </div>
            </div>
            <div class="oc-stat">
                <div class="oc-stat-icon project"><svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg></div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Termine</div>
                    <div class="oc-stat-value" data-stat="appointments">0</div>
                    <div class="oc-stat-sub">Verknüpfte Termine</div>
                </div>
            </div>
            <div class="oc-stat">
                <div class="oc-stat-icon tasks"><svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg></div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Aufgaben</div>
                    <div class="oc-stat-value" data-stat="personal_tasks">0</div>
                    <div class="oc-stat-sub">Persönliche Aufgaben</div>
                </div>
            </div>
            <div class="oc-stat">
                <div class="oc-stat-icon tickets"><svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path
                            d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        <path d="M12 9v4M12 17h.01" />
                    </svg></div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Tickets</div>
                    <div class="oc-stat-value" data-stat="tickets">0</div>
                    <div class="oc-stat-sub">Offene Probleme</div>
                </div>
            </div>
            <div class="oc-stat">
                <div class="oc-stat-icon progress"><svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18" />
                        <path d="M18 17V9" />
                        <path d="M13 17V5" />
                        <path d="M8 17v-3" />
                    </svg></div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Fortschritt</div>
                    <div class="oc-stat-value" data-stat="avg_progress">0%</div>
                    <div class="oc-stat-sub">Durchschnitt</div>
                </div>
            </div>
        </div>

        <form class="oc-toolbar" id="ppFilterForm" onsubmit="return false;">
            <div class="oc-toolbar-left">
                <div class="oc-filter-block search">
                    <label class="oc-filter-label">Suche</label>
                    <input type="text" class="oc-input" id="ppSearch"
                        placeholder="Kunde, Objekt, Adresse oder Produkt suchen">
                </div>
                <div class="oc-filter-block">
                    <label class="oc-filter-label">Mitarbeiter</label>
                    <select class="oc-select" id="ppEmployee">
                        <option value="">Alle</option>
                        @foreach($employees ?? [] as $employee)
                            <option value="{{ $employee->id }}">
                                {{ trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="oc-filter-block">
                    <label class="oc-filter-label">Umfang</label>
                    <select class="oc-select" id="ppScope">
                        <option value="">Alle Projekte</option>
                        <option value="1">Meine Projekte</option>
                    </select>
                </div>
            </div>
            <div class="oc-toolbar-right">
                <div class="oc-view-tabs">
                    <button type="button" class="oc-btn-soft" data-view="list">Liste</button>
                    <button type="button" class="oc-btn-soft" data-view="cards">Karten</button>
                    <button type="button" class="oc-btn-soft" data-view="kanban">Kanban</button>
                </div>
                <button type="button" class="oc-btn-soft" id="ppReset">Zurücksetzen</button>
            </div>
        </form>

        <div class="oc-card">
            <div id="ppListView">
                <div class="oc-list-head">
                    <div>ID</div>
                    <div>Kunde / Objekt</div>
                    <div>Produkt</div>
                    <div>Stufe</div>
                    <div>Team</div>
                    <div>Module</div>
                    <div>Fortschritt</div>
                    <div>Letzte Aktivität</div>
                    <div style="text-align:right;">Aktionen</div>
                </div>
                <div class="oc-list" id="ppList"></div>
            </div>

            <div class="oc-grid" id="ppCardGrid" style="display:none;"></div>
            <div class="pp-kanban-view" id="ppKanban" style="display:none;"></div>
        </div>

        <div class="oc-pagination" id="ppPagination">
            <div class="oc-page-info" id="ppPageInfo">Zeige 0 von 0 Projekten</div>
            <div class="oc-page-actions">
                <button type="button" class="oc-btn-soft" id="ppPrev">Zurück</button>
                <button type="button" class="oc-btn-soft" id="ppNext">Weiter</button>
            </div>
        </div>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>

    {{-- Add Project Modal --}}
    <div class="oc-modal-backdrop" id="ppAddProjectModal">
        <div class="oc-modal">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl">Projekt hinzufügen</h3>
                <button class="oc-btn-ic" type="button" id="ppAddProjectClose">×</button>
            </div>
            <div class="oc-modal-b">
                <div class="oc-form-group">
                    <label class="oc-label">Kunde / Objekt / Produkt suchen</label>
                    <select id="ppProjectCandidateSelect" style="width:100%;"></select>
                    <div class="oc-help">Suche nach Kundennummer, Name, Objekt, Adresse oder Produkt.</div>
                </div>
                <div id="ppMontageWarning" class="oc-warning-box" style="display:none;">Dieser Kunde ist aktuell nicht in
                    Montage. Beim Speichern wird er in Montage verschoben.</div>
            </div>
            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" id="ppAddProjectCancel">Abbrechen</button>
                <button type="button" class="oc-btn" id="ppAddProjectSave">Projekt erstellen</button>
            </div>
        </div>
    </div>

    {{-- Move Reason Modal --}}
    <div class="oc-modal-backdrop" id="ppMoveReasonModal">
        <div class="oc-modal">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl">Warum wird dieses Projekt verschoben?</h3>
                <button class="oc-btn-ic" type="button" id="ppMoveReasonClose">×</button>
            </div>
            <div class="oc-modal-b">
                <div class="oc-warning-box" id="ppMoveReasonInfo">Projekt wird verschoben.</div>
                <div class="oc-form-group" style="margin-top:14px;">
                    <label class="oc-label">Grund / Notiz *</label>
                    <textarea id="ppMoveReasonText" class="oc-textarea"
                        placeholder="z.B. Montage vorbereitet, Kunde bestätigt, Material fehlt, Termin verschoben ..."></textarea>
                    <div class="oc-help">Dieser Grund wird in der Projekt-Historie und in
                        <code>lead_product_lists.stage_history</code> gespeichert.
                    </div>
                </div>
            </div>
            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" id="ppMoveReasonCancel">Abbrechen</button>
                <button type="button" class="oc-btn" id="ppMoveReasonSave">Verschieben speichern</button>
            </div>
        </div>
    </div>

    {{-- History Sidebar --}}
    <div class="pp-drawer-backdrop" id="ppHistoryBackdrop"></div>
    <aside class="pp-history-drawer" id="ppHistoryDrawer" aria-hidden="true">
        <div class="pp-history-head">
            <div>
                <h3 class="pp-history-title">Projekt-Historie</h3>
                <div class="pp-history-sub" id="ppHistorySubtitle">Letzte Aktivitäten und Zeitabstände</div>
            </div>
            <button class="oc-btn-ic" type="button" id="ppHistoryClose">×</button>
        </div>
        <div class="pp-history-body">
            <div id="ppLatestJob" class="pp-latest-job">
                <div class="pp-latest-job-label">Letzter Job</div>
                <div class="pp-latest-job-title">Noch keine Daten geladen.</div>
            </div>
            <div class="pp-timeline" id="ppHistoryTimeline"></div>
        </div>
    </aside>
@endsection

@php
    $employeeDirectoryForJs = collect($employees ?? [])->map(function ($employee) {
        $fullName = trim((string) (data_get($employee, 'name', '') . ' ' . data_get($employee, 'lastname', '')));
        $initials = trim(
            mb_substr((string) data_get($employee, 'name', ''), 0, 1) .
            mb_substr((string) data_get($employee, 'lastname', ''), 0, 1)
        );

        return [
            'id' => (int) data_get($employee, 'id'),
            'name' => data_get($employee, 'name'),
            'lastname' => data_get($employee, 'lastname'),
            'full_name' => $fullName !== '' ? $fullName : ('Mitarbeiter #' . data_get($employee, 'id')),
            'image' => data_get($employee, 'image'),
            'photo' => data_get($employee, 'photo'),
            'avatar' => data_get($employee, 'avatar'),
            'photo_url' => data_get($employee, 'photo_url'),
            'image_url' => data_get($employee, 'image_url'),
            'initials' => mb_strtoupper($initials !== '' ? $initials : 'MA'),
        ];
    })->values();
@endphp

@push('scripts')
    <script src="{{ asset('js/select2.min.js') }}"></script>
@endpush

@once
    @push('scripts')
        <script>
            (function () {
                // OPEN_COCKPIT_FIXED_FULL_BLADE_V3
                const routeConfig = {
                    dataUrl: @json(route('planner.projects.data')),
                    kanbanUrl: @json(route('planner.projects.kanban')),
                    candidatesUrl: @json(route('planner.projects.candidates')),
                    storeProjectUrl: @json(route('planner.projects.store')),
                    ensurePlanUrlTemplate: @json(route('planner.projects.ensure_plan', ['project' => '___PROJECT___'])),
                    moveProjectUrlTemplate: @json(route('planner.projects.move', ['project' => '___PROJECT___'])),
                    historyUrlTemplate: @json(route('planner.projects.history', ['project' => '___PROJECT___'])),
                    profileUrlTemplate: @json(route('planner.projects.profile', ['project' => '___PROJECT___'])),
                    profileDataUrlTemplate: @json(route('planner.projects.profile.data', ['project' => '___PROJECT___'])),

                    // Opens the selected project cockpit blade: admin.planner.index
                    cockpitUrl: @json($plannerCockpitUrl),
                    boardUrl: @json($plannerCockpitUrl),
                };

                const serverConfig = @json($config ?? $plannerConfig ?? []);
                const config = Object.assign({}, routeConfig, serverConfig || {});

                Object.keys(routeConfig).forEach(function (key) {
                    if (!config[key] || config[key] === 'undefined' || String(config[key]).trim() === '') {
                        config[key] = routeConfig[key];
                    }
                });

                // Never allow an old controller config to override the open-plan URL back to /planner/projects.
                config.cockpitUrl = @json($plannerCockpitUrl);
                config.boardUrl = config.cockpitUrl;

                const appBaseUrl = @json(url('/'));
                const employeeDirectory = @json($employeeDirectoryForJs ?? []);
                const employeeLookup = new Map((Array.isArray(employeeDirectory) ? employeeDirectory : []).map(employee => [Number(employee.id), employee]));
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                const state = {
                    loading: false,
                    view: localStorage.getItem('planner_project_view') || 'list',
                    page: 1,
                    projects: [],
                    kanbanColumns: [],
                    pagination: { total: 0, current_page: 1, last_page: 1, per_page: 18, from: 0, to: 0 },
                    filters: { q: '', employee_id: '', my: '' },
                    zoom: Number(localStorage.getItem('planner_project_kanban_zoom') || 1),
                    pendingMove: null,
                };

                const els = {};
                const $one = (s) => document.querySelector(s);
                const $all = (s) => Array.from(document.querySelectorAll(s));

                function boot() {
                    Object.assign(els, {
                        search: $one('#ppSearch'), employee: $one('#ppEmployee'), scope: $one('#ppScope'),
                        list: $one('#ppList'), cardGrid: $one('#ppCardGrid'), kanban: $one('#ppKanban'), listView: $one('#ppListView'),
                        pageInfo: $one('#ppPageInfo'), prev: $one('#ppPrev'), next: $one('#ppNext'), pagination: $one('#ppPagination'),
                        refresh: $one('#ppRefresh'), reset: $one('#ppReset'),
                        addProjectBtn: $one('#ppAddProjectBtn'), addProjectModal: $one('#ppAddProjectModal'), addProjectClose: $one('#ppAddProjectClose'), addProjectCancel: $one('#ppAddProjectCancel'), addProjectSave: $one('#ppAddProjectSave'),
                        moveModal: $one('#ppMoveReasonModal'), moveInfo: $one('#ppMoveReasonInfo'), moveText: $one('#ppMoveReasonText'), moveClose: $one('#ppMoveReasonClose'), moveCancel: $one('#ppMoveReasonCancel'), moveSave: $one('#ppMoveReasonSave'),
                        historyDrawer: $one('#ppHistoryDrawer'), historyBackdrop: $one('#ppHistoryBackdrop'), historyClose: $one('#ppHistoryClose'), historySubtitle: $one('#ppHistorySubtitle'), latestJob: $one('#ppLatestJob'), historyTimeline: $one('#ppHistoryTimeline'),
                    });

                    bindEvents();
                    updateViewButtons();
                    updateViewVisibility();
                    initAddProjectSelect2();
                    fetchCurrentView();
                    setGlobalBreadcrumbsIfAvailable();
                }

                function bindEvents() {
                    let timer = null;
                    els.search?.addEventListener('input', () => {
                        clearTimeout(timer);
                        timer = setTimeout(() => { state.filters.q = els.search.value || ''; state.page = 1; fetchCurrentView(); }, 280);
                    });
                    els.employee?.addEventListener('change', () => { state.filters.employee_id = els.employee.value || ''; state.page = 1; fetchCurrentView(); });
                    els.scope?.addEventListener('change', () => { state.filters.my = els.scope.value || ''; state.page = 1; fetchCurrentView(); });
                    els.refresh?.addEventListener('click', fetchCurrentView);
                    els.reset?.addEventListener('click', () => {
                        if (els.search) els.search.value = '';
                        if (els.employee) els.employee.value = '';
                        if (els.scope) els.scope.value = '';
                        state.filters = { q: '', employee_id: '', my: '' };
                        state.page = 1;
                        fetchCurrentView();
                    });
                    els.prev?.addEventListener('click', () => { if (state.pagination.current_page > 1) fetchProjects(state.pagination.current_page - 1); });
                    els.next?.addEventListener('click', () => { if (state.pagination.current_page < state.pagination.last_page) fetchProjects(state.pagination.current_page + 1); });

                    $all('[data-view]').forEach(btn => btn.addEventListener('click', () => {
                        state.view = btn.dataset.view || 'list';
                        localStorage.setItem('planner_project_view', state.view);
                        updateViewButtons();
                        updateViewVisibility();
                        fetchCurrentView();
                    }));

                    document.addEventListener('click', function (e) {
                        const openPlan = e.target.closest('[data-open-plan]');
                        if (openPlan) { e.preventDefault(); openPlannerPlan(openPlan.dataset.projectId); return; }

                        const hist = e.target.closest('[data-open-history]');
                        if (hist) { e.preventDefault(); openProjectHistory(hist.dataset.projectId); return; }
                    });



                    document.addEventListener('click', function (event) {
                        const toggle = event.target.closest('[data-project-action-menu]');

                        if (toggle) {
                            event.preventDefault();
                            event.stopPropagation();

                            const menu = toggle.closest('.oc-action-menu');
                            const wasOpen = menu?.classList.contains('is-open');

                            document.querySelectorAll('.oc-action-menu.is-open').forEach(openMenu => {
                                if (openMenu !== menu) { openMenu.classList.remove('is-open'); openMenu.querySelector('[data-project-action-menu]')?.setAttribute('aria-expanded', 'false'); }
                            });

                            if (menu) {
                                menu.classList.toggle('is-open', !wasOpen);
                                toggle.setAttribute('aria-expanded', (!wasOpen).toString());
                            }
                            return;
                        }

                        if (!event.target.closest('.oc-action-menu')) {
                            document.querySelectorAll('.oc-action-menu.is-open').forEach(menu => { menu.classList.remove('is-open'); menu.querySelector('[data-project-action-menu]')?.setAttribute('aria-expanded', 'false'); });
                        }
                    }, true);

                    els.addProjectBtn?.addEventListener('click', openAddProjectModal);
                    els.addProjectClose?.addEventListener('click', closeAddProjectModal);
                    els.addProjectCancel?.addEventListener('click', closeAddProjectModal);
                    els.addProjectSave?.addEventListener('click', () => saveAddProject(false));

                    els.moveClose?.addEventListener('click', closeMoveReasonModal);
                    els.moveCancel?.addEventListener('click', closeMoveReasonModal);
                    els.moveSave?.addEventListener('click', confirmProjectMove);

                    els.historyClose?.addEventListener('click', closeProjectHistory);
                    els.historyBackdrop?.addEventListener('click', closeProjectHistory);

                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') {
                            closeAddProjectModal(); closeMoveReasonModal(); closeProjectHistory();
                        }
                    });

                    document.addEventListener('click', function (e) {
                        if (e.target.classList.contains('oc-modal-backdrop')) e.target.classList.remove('open');
                    });
                }

                function updateViewButtons() {
                    $all('[data-view]').forEach(b => b.classList.toggle('is-active', b.dataset.view === state.view));
                }

                function updateViewVisibility() {
                    if (els.listView) els.listView.style.display = state.view === 'list' ? '' : 'none';
                    if (els.cardGrid) els.cardGrid.style.display = state.view === 'cards' ? 'grid' : 'none';
                    if (els.kanban) els.kanban.style.display = state.view === 'kanban' ? 'block' : 'none';
                    if (els.pagination) els.pagination.style.display = state.view === 'kanban' ? 'none' : 'flex';
                }

                async function fetchCurrentView() {
                    if (state.view === 'kanban') return fetchKanban();
                    return fetchProjects(state.page || 1);
                }

                async function fetchProjects(page = 1) {
                    state.loading = true; state.page = page; renderLoading();
                    const params = new URLSearchParams({ page: String(page), view: state.view, q: state.filters.q || '', employee_id: state.filters.employee_id || '', my: state.filters.my || '' });
                    try {
                        const res = await fetch(`${config.dataUrl}?${params.toString()}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                        const json = await res.json();
                        if (!res.ok || !json.ok) throw new Error(json.message || 'Projektliste konnte nicht geladen werden.');
                        state.projects = json.data?.data || [];
                        state.pagination = { total: Number(json.data?.total || 0), current_page: Number(json.data?.current_page || 1), last_page: Number(json.data?.last_page || 1), per_page: Number(json.data?.per_page || 18), from: json.data?.from || 0, to: json.data?.to || 0 };
                    } catch (error) {
                        console.error(error); state.projects = []; state.pagination = { total: 0, current_page: 1, last_page: 1, per_page: 18, from: 0, to: 0 }; toast('bad', 'Fehler', error.message || 'Projektliste konnte nicht geladen werden.');
                    } finally { state.loading = false; render(); }
                }

                async function fetchKanban() {
                    if (!config.kanbanUrl) { toast('bad', 'Fehler', 'config.kanbanUrl fehlt.'); return; }
                    state.loading = true; renderLoading();
                    const url = new URL(config.kanbanUrl, window.location.origin);
                    url.searchParams.set('q', state.filters.q || '');
                    url.searchParams.set('employee_id', state.filters.employee_id || '');
                    url.searchParams.set('my', state.filters.my || '');
                    try {
                        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                        const json = await res.json();
                        if (!res.ok || !json.ok) throw new Error(json.message || 'Kanban konnte nicht geladen werden.');
                        state.kanbanColumns = json.columns || [];
                        renderKanban();
                        renderKanbanStats();
                    } catch (error) { console.error(error); state.kanbanColumns = []; toast('bad', 'Fehler', error.message || 'Kanban konnte nicht geladen werden.'); renderKanban(); }
                    finally { state.loading = false; }
                }

                function renderLoading() {
                    if (state.view === 'list' && els.list) els.list.innerHTML = `<div class="oc-loading">Projekte werden geladen...</div>`;
                    if (state.view === 'cards' && els.cardGrid) els.cardGrid.innerHTML = `<div class="oc-loading">Projekte werden geladen...</div>`;
                    if (state.view === 'kanban' && els.kanban) els.kanban.innerHTML = `<div class="oc-loading">Kanban wird geladen...</div>`;
                }

                function render() {
                    renderStats(); renderPagination();
                    if (state.view === 'list') renderList();
                    if (state.view === 'cards') renderCards();
                    if (state.view === 'kanban') renderKanban();
                }

                function renderStats() {
                    const totals = state.projects.reduce((acc, p) => { acc.appointments += Number(p.counts?.appointments || 0); acc.personal_tasks += Number(p.counts?.personal_tasks || 0); acc.tickets += Number(p.counts?.tickets || 0); acc.progress += Number(p.progress || 0); return acc; }, { appointments: 0, personal_tasks: 0, tickets: 0, progress: 0 });
                    const avg = state.projects.length ? Math.round(totals.progress / state.projects.length) : 0;
                    setText('[data-stat="projects"]', state.pagination.total || 0); setText('[data-stat="appointments"]', totals.appointments); setText('[data-stat="personal_tasks"]', totals.personal_tasks); setText('[data-stat="tickets"]', totals.tickets); setText('[data-stat="avg_progress"]', `${avg}%`);
                }

                function renderKanbanStats() {
                    const items = state.kanbanColumns.flatMap(c => c.items || []);
                    setText('[data-stat="projects"]', items.length);
                    setText('[data-stat="appointments"]', '—'); setText('[data-stat="personal_tasks"]', '—'); setText('[data-stat="tickets"]', '—'); setText('[data-stat="avg_progress"]', '—');
                }

                function renderPagination() {
                    if (!els.pageInfo) return;
                    els.pageInfo.innerHTML = `Zeige <strong>${state.pagination.from || 0}</strong> bis <strong>${state.pagination.to || 0}</strong> von <strong>${state.pagination.total || 0}</strong> Projekten`;
                    if (els.prev) els.prev.disabled = state.pagination.current_page <= 1;
                    if (els.next) els.next.disabled = state.pagination.current_page >= state.pagination.last_page;
                }

                function renderList() {
                    if (!els.list) return;
                    if (!state.projects.length) { els.list.innerHTML = `<div class="oc-empty">Keine Montage-Projekte gefunden.</div>`; return; }
                    els.list.innerHTML = state.projects.map(projectListRowHtml).join('');
                }

                function renderCards() {
                    if (!els.cardGrid) return;
                    if (!state.projects.length) { els.cardGrid.innerHTML = `<div class="oc-empty">Keine Montage-Projekte gefunden.</div>`; return; }
                    els.cardGrid.innerHTML = state.projects.map(projectCardHtml).join('');
                }

                function renderKanban() {
                    if (!els.kanban) return;
                    const columns = state.kanbanColumns || [];
                    if (!columns.length) { els.kanban.innerHTML = `<div class="oc-empty">Keine Montage-Unterphasen gefunden.</div>`; return; }
                    els.kanban.innerHTML = `
                                                    <div class="kanban-zoom-card" id="ppKanbanZoomCard" style="--kb-zoom:${state.zoom};">
                                                        <div class="kanban-zoom-toolbar">
                                                            <div>
                                                                <span class="kanban-zoom-title">Montage Kanban</span>
                                                                <span class="kanban-zoom-sub">Spalten kommen direkt aus lead_stage_sub_stages · Drag & Drop fragt nach Grund</span>
                                                            </div>
                                                            <div class="kanban-zoom-actions">
                                                                <button type="button" class="kbz-btn" id="ppZoomOut">−</button>
                                                                <button type="button" class="kbz-btn" id="ppZoomReset">${Math.round(state.zoom * 100)}%</button>
                                                                <button type="button" class="kbz-btn" id="ppZoomIn">+</button>
                                                                <button type="button" class="kbz-btn" id="ppKanbanReload">Neu laden</button>
                                                            </div>
                                                        </div>
                                                        <div class="kanban-zoom-area">
                                                            <div class="kanban-container" id="ppDynamicKanban">
                                                                ${columns.map(col => kanbanColumnHtml(col)).join('')}
                                                            </div>
                                                        </div>
                                                    </div>`;
                    bindKanbanZoomButtons(); bindProjectKanbanDragDrop();
                }

                function kanbanColumnHtml(column) {
                    const items = column.items || [];
                    return `<div class="column pp-kanban-column" data-sub-stage-id="${escapeHtml(column.id)}" data-sub-stage-name="${escapeAttr(column.name || '')}">
                                                    <h3 style="background:${escapeHtml(column.color || '#93c21c')}">
                                                        <span class="kb-column-head-left"><span class="kb-column-title">${escapeHtml(column.name || 'Unterphase')} <span class="count-badge">${items.length}</span></span></span>
                                                    </h3>
                                                    <div class="column-content pp-kanban-dropzone" data-sub-stage-id="${escapeHtml(column.id)}" data-sub-stage-name="${escapeAttr(column.name || '')}">
                                                        ${items.length ? items.map(projectKanbanCardHtml).join('') : `<div class="oc-empty" style="margin:0;padding:28px 12px;">Keine Projekte</div>`}
                                                    </div>
                                                </div>`;
                }

                function projectListRowHtml(project) {
                    return `<div class="oc-item"><div class="oc-item-row">
                                                    <div class="oc-cell"><div class="oc-cell-title">ID</div><span class="oc-id-badge">#${escapeHtml(project.id)}</span></div>
                                                    <div class="oc-cell"><div class="oc-cell-title">Kunde / Objekt</div>${customerBlockHtml(project)}</div>
                                                    <div class="oc-cell"><div class="oc-cell-title">Produkt</div>${productBlockHtml(project)}</div>
                                                    <div class="oc-cell"><div class="oc-cell-title">Stufe</div>${stagePillHtml(project)}</div>
                                                    <div class="oc-cell"><div class="oc-cell-title">Team</div>${teamHtml(project.team || [])}</div>
                                                    <div class="oc-cell"><div class="oc-cell-title">Module</div>${moduleCountsHtml(project.counts || {})}</div>
                                                    <div class="oc-cell"><div class="oc-cell-title">Fortschritt</div>${progressHtml(project.progress || 0)}</div>
                                                    <div class="oc-cell oc-cell-latest"><div class="oc-cell-title">Letzte Aktivität</div>${latestActivityHtml(project)}</div>
                                                    <div class="oc-cell oc-cell-actions"><div class="oc-cell-title">Aktionen</div>${actionButtonsHtml(project)}</div>
                                                </div></div>`;
                }

                function projectCardHtml(project) {
                    return `<div class="oc-project-card">
                                                    <div class="oc-project-top"><div>${customerBlockHtml(project)}</div><span class="oc-id-badge">#${escapeHtml(project.id)}</span></div>
                                                    ${productBlockHtml(project)}
                                                    <div class="oc-project-meta">${stagePillHtml(project)}</div>
                                                    ${teamHtml(project.team || [])}
                                                    <div style="margin-top:12px;">${moduleCountsHtml(project.counts || {})}</div>
                                                    <div style="margin-top:12px;">${progressHtml(project.progress || 0)}</div>
                                                    ${latestActivityHtml(project)}
                                                    <div style="margin-top:14px;" class="oc-actions">${actionButtonsHtml(project)}</div>
                                                </div>`;
                }

                function projectKanbanCardHtml(project) {
                    const cockpitUrl = plannerCockpitUrl(project);

                    return `<div class="card pp-kanban-card" draggable="true" data-project-id="${escapeHtml(project.id)}">
                                                    <div class="card-header"><strong>${escapeHtml(project.customer?.name || 'Kunde')}</strong><span class="circle">#${escapeHtml(project.id)}</span></div>
                                                    <div style="font-size:12px;color:#64748b;font-weight:850;">${escapeHtml(project.customer?.number || '')}</div>
                                                    <div style="font-size:13px;font-weight:950;color:#0f172a;margin-top:4px;">${escapeHtml(project.product?.name || 'Produkt')}</div>
                                                    <div class="kb-stage-time">
                                                        <div class="kb-stage-time-row"><strong>Objekt:</strong><span>${escapeHtml(project.object?.name || '—')}</span></div>
                                                        <div class="kb-stage-time-row"><strong>Adresse:</strong><span>${escapeHtml(project.object?.address || '—')}</span></div>
                                                    </div>
                                                    ${latestActivityHtml(project)}
                                                    <div class="card-actions">
                                                        <a class="btn-icon btn-play" href="${escapeAttr(cockpitUrl)}" data-open-plan data-project-id="${escapeHtml(project.id)}" title="Plan öffnen">▶</a>
                                                        <button type="button" class="btn-icon" data-open-history data-project-id="${escapeHtml(project.id)}" title="Historie">🕘</button>
                                                    </div>
                                                </div>`;
                }

                function customerBlockHtml(project) {
                    return `<div class="oc-main"><div class="oc-ttl">${escapeHtml(project.customer?.name || 'Kunde')}</div><div class="oc-subt">${escapeHtml(project.customer?.number || '')}${project.object?.name ? ' · ' + escapeHtml(project.object.name) : ''}</div><div class="oc-subt">${escapeHtml(project.object?.address || '')}</div></div>`;
                }

                function findProjectById(projectId) {
                    const id = Number(projectId || 0);
                    if (!id) return null;

                    const fromList = (state.projects || []).find(project => Number(project.id) === id);
                    if (fromList) return fromList;

                    for (const column of (state.kanbanColumns || [])) {
                        const found = (column.items || []).find(project => Number(project.id) === id);
                        if (found) return found;
                    }

                    return null;
                }

                function plannerCockpitUrl(projectOrId, overrides = {}) {
                    const project = typeof projectOrId === 'object' ? projectOrId : findProjectById(projectOrId);

                    const projectId = Number(
                        overrides.project_id ||
                        (typeof projectOrId === 'object' ? projectOrId?.id : projectOrId) ||
                        project?.id ||
                        0
                    );

                    const customerId =
                        overrides.customer_id ||
                        project?.customer?.id ||
                        project?.customer_id ||
                        '';

                    const planId =
                        overrides.plan_id ||
                        project?.planner_plan_id ||
                        project?.plan_id ||
                        '';

                    const base = String(config.cockpitUrl || config.boardUrl || @json($plannerCockpitUrl)).trim()
                        || @json($plannerCockpitUrl);

                    const url = new URL(base, window.location.origin);

                    if (projectId) {
                        url.searchParams.set('project_id', String(projectId));
                    }

                    if (customerId) {
                        url.searchParams.set('customer_id', String(customerId));
                    }

                    if (planId) {
                        url.searchParams.set('plan_id', String(planId));
                    }

                    return url.toString();
                }

                function normalizeUrlPath(path) {
                    return String(path || '').replace(/\\/g, '/').replace(/^\/+/, '');
                }

                function absoluteAsset(path) {
                    const base = String(appBaseUrl || window.location.origin).replace(/\/+$/, '');
                    return `${base}/${normalizeUrlPath(path)}`;
                }

                function resolveAssetUrl(value, type) {
                    const raw = String(value || '').trim();

                    if (!raw) {
                        return { src: '', alt: '' };
                    }

                    if (/^https?:\/\//i.test(raw)) {
                        let alt = '';
                        if (type === 'article_group') {
                            if (raw.includes('/images/article_group/')) {
                                alt = raw.replace('/images/article_group/', '/images/article_groups/');
                            } else if (raw.includes('/images/article_groups/')) {
                                alt = raw.replace('/images/article_groups/', '/images/article_group/');
                            }
                        }

                        if (type === 'employee') {
                            if (raw.includes('/images/employees/')) {
                                alt = raw.replace('/images/employees/', '/images/employee/');
                            } else if (raw.includes('/images/employee/')) {
                                alt = raw.replace('/images/employee/', '/images/employees/');
                            }
                        }

                        return { src: raw, alt: alt && alt !== raw ? alt : '' };
                    }

                    if (raw.startsWith('/')) {
                        const src = absoluteAsset(raw);
                        let alt = '';

                        if (type === 'article_group') {
                            if (raw.includes('/images/article_group/')) {
                                alt = absoluteAsset(raw.replace('/images/article_group/', '/images/article_groups/'));
                            } else if (raw.includes('/images/article_groups/')) {
                                alt = absoluteAsset(raw.replace('/images/article_groups/', '/images/article_group/'));
                            }
                        }

                        if (type === 'employee') {
                            if (raw.includes('/images/employees/')) {
                                alt = absoluteAsset(raw.replace('/images/employees/', '/images/employee/'));
                            } else if (raw.includes('/images/employee/')) {
                                alt = absoluteAsset(raw.replace('/images/employee/', '/images/employees/'));
                            }
                        }

                        return { src, alt };
                    }

                    if (/^(images|storage|uploads)\//i.test(raw)) {
                        const src = absoluteAsset(raw);
                        let alt = '';

                        if (type === 'article_group') {
                            if (raw.includes('images/article_group/')) {
                                alt = absoluteAsset(raw.replace('images/article_group/', 'images/article_groups/'));
                            } else if (raw.includes('images/article_groups/')) {
                                alt = absoluteAsset(raw.replace('images/article_groups/', 'images/article_group/'));
                            }
                        }

                        if (type === 'employee') {
                            if (raw.includes('images/employees/')) {
                                alt = absoluteAsset(raw.replace('images/employees/', 'images/employee/'));
                            } else if (raw.includes('images/employee/')) {
                                alt = absoluteAsset(raw.replace('images/employee/', 'images/employees/'));
                            }
                        }

                        return { src, alt };
                    }

                    if (type === 'article_group') {
                        return {
                            src: absoluteAsset(`images/article_groups/${raw}`),
                            alt: absoluteAsset(`images/article_group/${raw}`),
                        };
                    }

                    return {
                        src: absoluteAsset(`images/employee/${raw}`),
                        alt: absoluteAsset(`images/employees/${raw}`),
                    };
                }

                function imageErrorHandler() {
                    return "if(this.dataset.altSrc){this.src=this.dataset.altSrc;this.dataset.altSrc='';}else{this.style.display='none';this.nextElementSibling.style.display='flex';}";
                }

                function productInitial(project) {
                    const p = project.product || {};
                    const raw = p.initial || p.article_initial || p.product_initial || project.product_initial || '';

                    if (raw) {
                        return String(raw).trim().substring(0, 4).toUpperCase();
                    }

                    const name = p.name || project.product_name || 'AG';
                    const letters = String(name)
                        .trim()
                        .split(/\s+/)
                        .filter(Boolean)
                        .slice(0, 2)
                        .map(part => part.charAt(0))
                        .join('')
                        .toUpperCase();

                    return letters || 'AG';
                }

                function productBlockHtml(project) {
                    const p = project.product || {};
                    const name = p.name || project.product_name || 'Produkt';
                    const imageRaw = p.image || p.image_url || project.product_image || project.product_image_url || '';
                    const initial = productInitial(project);
                    const resolved = resolveAssetUrl(imageRaw, 'article_group');

                    const media = resolved.src
                        ? `<span class="oc-product-media">
                                                                <img class="oc-product-img" src="${escapeAttr(resolved.src)}" ${resolved.alt ? `data-alt-src="${escapeAttr(resolved.alt)}"` : ''} alt="${escapeAttr(name)}" onerror="${imageErrorHandler()}">
                                                                <span class="oc-product-placeholder" style="display:none;">${escapeHtml(initial)}</span>
                                                           </span>`
                        : `<span class="oc-product-placeholder">${escapeHtml(initial)}</span>`;

                    return `<div class="oc-product">
                                                        ${media}
                                                        <div class="oc-main">
                                                            <div class="oc-ttl">${escapeHtml(name)}</div>
                                                            <div class="oc-subt">${escapeHtml(project.service_name || 'Montage')}</div>
                                                        </div>
                                                    </div>`;
                }

                function stagePillHtml(project) {
                    const color = project.sub_stage?.color || project.stage?.color || '#74b2d4';
                    const label = project.sub_stage?.name || project.stage?.name || project.stage?.key || 'Montage';
                    return `<span class="oc-status-pill blue" style="background:${hexToSoft(color)};color:${escapeAttr(color)};">${escapeHtml(label)}</span>`;
                }

                function employeeFromDirectory(employee) {
                    const id = Number(employee?.id || 0);
                    const fallback = id ? (employeeLookup.get(id) || {}) : {};

                    return {
                        ...fallback,
                        ...(employee || {}),
                        photo_url: employee?.photo_url || employee?.image_url || employee?.image || employee?.photo || employee?.avatar || fallback.photo_url || fallback.image_url || fallback.image || fallback.photo || fallback.avatar || '',
                        initials: employee?.initials || fallback.initials || initials(employee?.full_name || employee?.name || fallback.full_name || fallback.name || 'M'),
                        full_name: employee?.full_name || fallback.full_name || `${employee?.name || fallback.name || ''} ${employee?.lastname || fallback.lastname || ''}`.trim() || `Mitarbeiter #${id || ''}`,
                    };
                }

                function employeeAvatarHtml(employee) {
                    const e = employeeFromDirectory(employee);
                    const name = e.full_name || e.name || `Mitarbeiter #${e.id || ''}`;
                    const initial = String(e.initials || initials(name || 'M')).substring(0, 3).toUpperCase();
                    const resolved = resolveAssetUrl(e.photo_url, 'employee');

                    if (!resolved.src) {
                        return `<span class="oc-avatar-initial" title="${escapeAttr(name)}">${escapeHtml(initial)}</span>`;
                    }

                    return `<span class="oc-avatar-wrap" title="${escapeAttr(name)}">
                                                        <img class="oc-avatar" src="${escapeAttr(resolved.src)}" ${resolved.alt ? `data-alt-src="${escapeAttr(resolved.alt)}"` : ''} alt="${escapeAttr(name)}" onerror="${imageErrorHandler()}">
                                                        <span class="oc-avatar-initial">${escapeHtml(initial)}</span>
                                                    </span>`;
                }

                function teamHtml(team) {
                    team = Array.isArray(team) ? team : [];

                    if (!team.length) {
                        return `<span class="oc-status-pill gray">Kein Team</span>`;
                    }

                    const shown = team.slice(0, 4).map(employeeAvatarHtml).join('');

                    return `<div class="oc-team">
                                                        ${shown}
                                                        ${team.length > 4 ? `<span class="oc-team-more">+${team.length - 4}</span>` : ''}
                                                    </div>`;
                }
                function moduleCountsHtml(c) {
                    return `<div class="oc-module-grid"><div class="oc-module-box"><strong>${c.appointments || 0}</strong><span>Termine</span></div><div class="oc-module-box"><strong>${c.personal_tasks || 0}</strong><span>Tasks</span></div><div class="oc-module-box"><strong>${c.tickets || 0}</strong><span>Tickets</span></div><div class="oc-module-box"><strong>${c.kanban_tasks || 0}</strong><span>Kanban</span></div><div class="oc-module-box"><strong>${c.planner_total || c.planner_items || 0}</strong><span>Plan</span></div></div>`;
                }
                function progressHtml(progress) {
                    progress = Math.max(0, Math.min(100, Number(progress || 0)));
                    return `<div class="oc-progress-wrap"><div class="oc-progress-meta"><span>${progress}%</span><span>Fertig</span></div><div class="oc-progress-track"><div class="oc-progress-bar" style="width:${progress}%"></div></div></div>`;
                }
                function latestActivityHtml(project) {
                    const a = project.latest_activity || project.latest || project.last_activity || null;

                    if (!a) {
                        return `<div class="oc-latest-activity">
                                                        <span class="oc-latest-activity-icon">↺</span>
                                                        <span class="min-w-0">
                                                            <span class="oc-latest-activity-title">Noch keine Projektaktivität</span>
                                                            <span class="oc-latest-activity-meta">Keine Cockpit-Aktion gefunden</span>
                                                        </span>
                                                    </div>`;
                    }

                    const title = a.title || a.event || a.label || 'Aktivität';
                    const employee = a.employee_name || a.changed_by_name || a.done_by_name || a.user_name || '';
                    const date = a.created_at_label || a.changed_at_label || a.date_label || a.created_at || a.changed_at || a.date || '';
                    const meta = [formatDateTime(date), employee].filter(v => v && v !== '—').join(' · ');
                    const desc = a.description || a.reason || a.text || a.note || '';
                    const combined = `${a.type || ''} ${a.status || ''} ${title} ${desc}`;
                    const isDone = /done|erledigt|fertig|completed|abgeschlossen|check/i.test(combined);
                    const isWarning = /ticket|problem|fehler|material|warn|block/i.test(combined);
                    const stateClass = isDone ? ' is-done' : (isWarning ? ' is-warning' : '');
                    const icon = isDone ? '✓' : (isWarning ? '!' : '↺');

                    return `<div class="oc-latest-activity${stateClass}">
                                                    <span class="oc-latest-activity-icon">${icon}</span>
                                                    <span class="min-w-0">
                                                        <span class="oc-latest-activity-title">${escapeHtml(title)}</span>
                                                        <span class="oc-latest-activity-meta">${escapeHtml(meta || '—')}</span>
                                                        ${desc ? `<span class="oc-latest-activity-desc">${escapeHtml(desc)}</span>` : ''}
                                                    </span>
                                                </div>`;
                }

                function actionButtonsHtml(project) {
                    const cockpitUrl = plannerCockpitUrl(project);
                    const profileUrl = config.profileUrlTemplate
                        ? config.profileUrlTemplate.replace('___PROJECT___', encodeURIComponent(project.id))
                        : cockpitUrl;

                    return `<div class="oc-actions" data-project-actions="${escapeHtml(project.id)}">
                                                    <a class="oc-btn-ic primary" href="${escapeAttr(cockpitUrl)}" data-open-plan data-project-id="${escapeHtml(project.id)}" title="Plan öffnen">
                                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M13 5l7 7-7 7"/></svg>
                                                    </a>
                                                    <div class="oc-action-menu">
                                                        <button type="button" class="oc-action-menu-toggle" data-project-action-menu aria-expanded="false">
                                                            Aktionen <span>⌄</span>
                                                        </button>
                                                        <div class="oc-action-menu-panel">
                                                            <a href="${escapeAttr(cockpitUrl)}" data-open-plan data-project-id="${escapeHtml(project.id)}">▶ Plan öffnen</a>
                                                            <button type="button" data-open-history data-project-id="${escapeHtml(project.id)}">🕘 Aktivitäten / Historie</button>
                                                            <a href="${escapeAttr(profileUrl)}">👤 Projektprofil</a>
                                                        </div>
                                                    </div>
                                                </div>`;
                }

                function bindKanbanZoomButtons() {
                    $one('#ppZoomOut')?.addEventListener('click', () => setKanbanZoom(state.zoom - .1));
                    $one('#ppZoomIn')?.addEventListener('click', () => setKanbanZoom(state.zoom + .1));
                    $one('#ppZoomReset')?.addEventListener('click', () => setKanbanZoom(1));
                    $one('#ppKanbanReload')?.addEventListener('click', fetchKanban);
                }
                function setKanbanZoom(value) {
                    state.zoom = Math.max(.55, Math.min(1.45, Number(value.toFixed(2))));
                    localStorage.setItem('planner_project_kanban_zoom', state.zoom);
                    const card = $one('#ppKanbanZoomCard');
                    if (card) card.style.setProperty('--kb-zoom', state.zoom);
                    const reset = $one('#ppZoomReset');
                    if (reset) reset.textContent = Math.round(state.zoom * 100) + '%';
                }

                function bindProjectKanbanDragDrop() {
                    $all('.pp-kanban-card').forEach(card => {
                        card.addEventListener('dragstart', e => { e.dataTransfer.setData('text/plain', card.dataset.projectId); card.classList.add('is-dragging'); });
                        card.addEventListener('dragend', () => card.classList.remove('is-dragging'));
                    });
                    $all('.pp-kanban-dropzone').forEach(zone => {
                        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('is-over'); });
                        zone.addEventListener('dragleave', () => zone.classList.remove('is-over'));
                        zone.addEventListener('drop', e => {
                            e.preventDefault(); zone.classList.remove('is-over');
                            const projectId = e.dataTransfer.getData('text/plain');
                            const subStageId = zone.dataset.subStageId;
                            const subStageName = zone.dataset.subStageName || 'Unterphase';
                            if (projectId && subStageId) openMoveReasonModal(projectId, subStageId, subStageName);
                        });
                    });
                }

                function openMoveReasonModal(projectId, subStageId, subStageName) {
                    state.pendingMove = { projectId, subStageId, subStageName };
                    if (els.moveInfo) els.moveInfo.textContent = `Projekt #${projectId} wird nach „${subStageName}” verschoben.`;
                    if (els.moveText) els.moveText.value = '';
                    els.moveModal?.classList.add('open');
                    setTimeout(() => els.moveText?.focus(), 80);
                }
                function closeMoveReasonModal() { state.pendingMove = null; els.moveModal?.classList.remove('open'); }
                async function confirmProjectMove() {
                    if (!state.pendingMove) return;
                    const reason = (els.moveText?.value || '').trim();
                    if (reason.length < 3) { toast('bad', 'Grund fehlt', 'Bitte geben Sie einen kurzen Grund für die Verschiebung ein.'); return; }
                    const { projectId, subStageId } = state.pendingMove;
                    const url = (config.moveProjectUrlTemplate || '').replace('___PROJECT___', projectId);
                    if (!url) { toast('bad', 'Fehler', 'config.moveProjectUrlTemplate fehlt.'); return; }
                    try {
                        const res = await fetch(url, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ sub_stage_id: subStageId, reason }) });
                        const json = await res.json();
                        if (!res.ok || !json.ok) throw new Error(json.message || 'Projekt konnte nicht verschoben werden.');
                        closeMoveReasonModal(); toast('ok', 'Gespeichert', json.message || 'Projekt wurde verschoben.'); await fetchKanban();
                    } catch (error) { console.error(error); toast('bad', 'Fehler', error.message || 'Projekt konnte nicht verschoben werden.'); }
                }

                function initAddProjectSelect2() {
                    if (!window.jQuery || !jQuery.fn.select2 || !jQuery('#ppProjectCandidateSelect').length) return;
                    const select = jQuery('#ppProjectCandidateSelect');
                    if (select.hasClass('select2-hidden-accessible')) return;
                    select.select2({
                        dropdownParent: jQuery('#ppAddProjectModal'), width: '100%', placeholder: 'Kunde, Objekt oder Produkt suchen...', minimumInputLength: 1,
                        ajax: { url: config.candidatesUrl, dataType: 'json', delay: 250, data: params => ({ q: params.term || '' }), processResults: data => ({ results: data.results || [] }) },
                        templateResult: formatProjectCandidate,
                        templateSelection: item => item.text || 'Projekt auswählen'
                    });
                    select.on('select2:select', function (e) {
                        const item = e.params.data || null;
                        const warning = $one('#ppMontageWarning');
                        if (warning) warning.style.display = item && !item.is_montage ? 'block' : 'none';
                    });
                }
                function formatProjectCandidate(item) {
                    if (!item.id) return item.text;
                    return jQuery(`<div style="display:flex;flex-direction:column;gap:3px;"><strong>${escapeHtml(item.customer || item.text)}</strong><small>${escapeHtml(item.object || 'Objekt')} · ${escapeHtml(item.product || 'Produkt')}</small><small>Status: ${escapeHtml(item.stage_name || item.status || '—')} ${item.is_montage ? '✅ Montage' : '⚠️ nicht Montage'}</small></div>`);
                }
                function openAddProjectModal() { els.addProjectModal?.classList.add('open'); initAddProjectSelect2(); }
                function closeAddProjectModal() { els.addProjectModal?.classList.remove('open'); }
                async function saveAddProject(forceMontage = false) {
                    const id = window.jQuery ? jQuery('#ppProjectCandidateSelect').val() : null;
                    if (!id) { toast('bad', 'Auswahl fehlt', 'Bitte zuerst einen Kunden / ein Produkt auswählen.'); return; }
                    try {
                        const res = await fetch(config.storeProjectUrl, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ lead_product_list_id: id, force_montage: forceMontage ? 1 : 0 }) });
                        const json = await res.json();
                        if (res.status === 409 && json.requires_confirmation) { if (confirm(json.message || 'Nicht in Montage. Trotzdem verschieben?')) return saveAddProject(true); return; }
                        if (!res.ok || !json.ok) throw new Error(json.message || 'Projekt konnte nicht erstellt werden.');
                        closeAddProjectModal(); if (window.jQuery) jQuery('#ppProjectCandidateSelect').val(null).trigger('change'); toast('ok', 'Gespeichert', json.message || 'Projekt wurde erstellt.'); await fetchCurrentView();
                    } catch (error) { console.error(error); toast('bad', 'Fehler', error.message || 'Projekt konnte nicht erstellt werden.'); }
                }

                async function openPlannerPlan(projectId) {
                    if (!projectId) return;

                    const project = findProjectById(projectId);
                    const fallbackUrl = plannerCockpitUrl(project || projectId);
                    const url = (config.ensurePlanUrlTemplate || '').replace('___PROJECT___', projectId);

                    if (!url || url === 'undefined') {
                        window.location.assign(fallbackUrl);
                        return;
                    }

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({})
                        });

                        const json = await res.json().catch(() => ({}));

                        if (!res.ok || json.ok === false) {
                            throw new Error(json.message || 'Plan konnte nicht geöffnet werden.');
                        }

                        window.location.assign(plannerCockpitUrl(project || projectId, {
                            customer_id: json.customer_id || project?.customer?.id || '',
                            plan_id: json.plan_id || project?.planner_plan_id || '',
                            project_id: json.project_id || projectId,
                        }));
                    } catch (error) {
                        console.error(error);
                        window.location.assign(fallbackUrl);
                    }
                }

                async function openProjectHistory(projectId) {
                    if (!projectId) return;
                    els.historyDrawer?.classList.add('open'); els.historyBackdrop?.classList.add('open');
                    if (els.historySubtitle) els.historySubtitle.textContent = `Projekt #${projectId}`;
                    if (els.latestJob) els.latestJob.innerHTML = `<div class="pp-latest-job-label">Letzter Job</div><div class="pp-latest-job-title">Wird geladen...</div>`;
                    if (els.historyTimeline) els.historyTimeline.innerHTML = `<div class="oc-loading">Historie wird geladen...</div>`;
                    const template = config.historyUrlTemplate || '';
                    if (!template) {
                        if (els.historyTimeline) els.historyTimeline.innerHTML = `<div class="oc-empty">config.historyUrlTemplate fehlt.</div>`;
                        return;
                    }
                    try {
                        const res = await fetch(template.replace('___PROJECT___', projectId), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                        const json = await res.json();
                        if (!res.ok || !json.ok) throw new Error(json.message || 'Historie konnte nicht geladen werden.');
                        renderProjectHistory(json);
                    } catch (error) { console.error(error); if (els.historyTimeline) els.historyTimeline.innerHTML = `<div class="oc-empty">${escapeHtml(error.message || 'Historie konnte nicht geladen werden.')}</div>`; }
                }
                function closeProjectHistory() { els.historyDrawer?.classList.remove('open'); els.historyBackdrop?.classList.remove('open'); }
                function renderProjectHistory(json) {
                    const items = json.items || [];
                    const latest = json.latest || items[0] || null;
                    if (els.historySubtitle) els.historySubtitle.textContent = json.project?.title || json.project?.customer || 'Projekt-Historie';
                    if (els.latestJob) {
                        els.latestJob.innerHTML = latest ? `<div class="pp-latest-job-label">Letzter Job</div><div class="pp-latest-job-title">${escapeHtml(latest.title || latest.event || 'Aktivität')}</div><div class="pp-latest-job-meta">${escapeHtml(formatDateTime(latest.created_at || latest.date))}${latest.diff_from_now ? ' · ' + escapeHtml(latest.diff_from_now) : ''}</div>${latest.reason ? `<div class="pp-timeline-reason">${escapeHtml(latest.reason)}</div>` : ''}` : `<div class="pp-latest-job-label">Letzter Job</div><div class="pp-latest-job-title">Keine Historie gefunden.</div>`;
                    }
                    if (!items.length) { if (els.historyTimeline) els.historyTimeline.innerHTML = `<div class="oc-empty">Keine Timeline-Einträge gefunden.</div>`; return; }
                    if (els.historyTimeline) els.historyTimeline.innerHTML = items.map((item, idx) => historyItemHtml(item, items[idx + 1])).join('');
                }
                function historyItemHtml(item, nextItem) {
                    const date = item.created_at || item.date || null;
                    const nextDate = nextItem ? (nextItem.created_at || nextItem.date || null) : null;
                    const diff = item.diff_from_previous || (date && nextDate ? humanDateDiff(nextDate, date) : null);
                    return `<div class="pp-timeline-item"><div class="pp-timeline-top"><div class="pp-timeline-title">${escapeHtml(item.title || item.event || 'Aktivität')}</div><div class="pp-timeline-date">${escapeHtml(formatDateTime(date))}</div></div>${item.description || item.text ? `<div class="pp-timeline-text">${escapeHtml(item.description || item.text)}</div>` : ''}${item.reason ? `<div class="pp-timeline-reason">Grund: ${escapeHtml(item.reason)}</div>` : ''}${diff ? `<div class="pp-timeline-diff">Abstand: ${escapeHtml(diff)}</div>` : ''}</div>`;
                }

                function toast(kind, title, msg) {
                    const wrap = $one('#toast-wrap'); if (!wrap) return;
                    const icons = { ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M20 6L9 17l-5-5"/></svg>`, bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M6 18L18 6M6 6l12 12"/></svg>` };
                    const el = document.createElement('div'); el.className = 'oc-toast';
                    el.innerHTML = `<div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div><div style="flex:1;"><p class="oc-toast-ttl">${escapeHtml(title)}</p><p class="oc-toast-msg">${escapeHtml(msg)}</p></div><button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>`;
                    wrap.appendChild(el); setTimeout(() => { try { el.remove(); } catch (e) { } }, 4500);
                }
                function setText(selector, value) { const el = $one(selector); if (el) el.textContent = value; }
                function initials(text) { return String(text || '').split(' ').filter(Boolean).slice(0, 2).map(s => s[0]).join('').toUpperCase() || 'M'; }
                function escapeHtml(v) { return String(v ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[c])); }
                function escapeAttr(v) { return escapeHtml(v).replace(/`/g, '&#096;'); }
                function hexToSoft(hex) { return String(hex || '#74b2d4') + '18'; }
                function formatDateTime(value) { if (!value) return '—'; try { return new Date(value).toLocaleString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }); } catch (e) { return value; } }
                function humanDateDiff(from, to) {
                    try {
                        const a = new Date(from), b = new Date(to); let ms = Math.abs(b - a);
                        const days = Math.floor(ms / 86400000); ms -= days * 86400000;
                        const hours = Math.floor(ms / 3600000); ms -= hours * 3600000;
                        const mins = Math.floor(ms / 60000);
                        const parts = []; if (days) parts.push(days + ' Tag' + (days === 1 ? '' : 'e')); if (hours) parts.push(hours + ' Std.'); if (!days && mins) parts.push(mins + ' Min.');
                        return parts.length ? parts.join(' ') : 'weniger als 1 Min.';
                    } catch (e) { return null; }
                }
                function setGlobalBreadcrumbsIfAvailable() {
                    window.GlobalBreadcrumbs = [{ label: 'Dashboard', url: '{{ url('/') }}' }, { label: 'Projekte', url: '{{ url()->current() }}', clickable: false }];
                    if (window.setGlobalBreadcrumbs) window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
                }

                document.addEventListener('DOMContentLoaded', boot);
            })();
        </script>
    @endpush
@endonce