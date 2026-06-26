@extends('admin.layouts.app')

@section('title', $materialDocumentTitle ?? 'Materialliste')

@section('style')
    <style>
        :root {
            --ml-bg: #f3f4f6;
            --ml-card: #ffffff;
            --ml-text: #111827;
            --ml-muted: #64748b;
            --ml-border: #e5e7eb;
            --ml-border-strong: #cbd5e1;
            --ml-green: #93c21c;
            --ml-green-dark: #7baa18;
            --ml-blue: #74b2d4;
            --ml-orange: #f59e0b;
            --ml-red: #ef4444;
            --ml-success: #10b981;
            --ml-soft-green: #f4fae7;
            --ml-soft-blue: #eff6ff;
            --ml-soft-orange: #fffbeb;
            --ml-soft-red: #fef2f2;
            --ml-radius: 16px;
            --ml-shadow: 0 12px 30px -18px rgba(15, 23, 42, .42), 0 2px 8px -4px rgba(15, 23, 42, .18);
        }

        .ml-wrap {
            color: var(--ml-text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            width: 100%;
            max-width: 100%;
        }

        .ml-titlebar {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid var(--ml-border);
            border-radius: 22px;
            padding: 20px;
            box-shadow: var(--ml-shadow);
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .ml-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--ml-soft-green);
            color: #55720d;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 9px;
        }

        .ml-title {
            margin: 0;
            font-size: 26px;
            font-weight: 950;
            letter-spacing: -.03em;
            color: #0f172a;
            line-height: 1.15;
        }

        .ml-sub {
            margin-top: 7px;
            color: var(--ml-muted);
            font-size: 13px;
            line-height: 1.55;
            max-width: 980px;
        }

        .ml-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 11px;
            font-size: 12px;
            color: var(--ml-muted);
            font-weight: 800;
        }

        .ml-breadcrumb a {
            color: var(--ml-muted);
            text-decoration: none;
        }

        .ml-breadcrumb a:hover {
            color: #0f172a;
        }

        .ml-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
        }

        .ml-btn {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--ml-green);
            background: var(--ml-green);
            color: #fff !important;
            border-radius: 12px;
            padding: 9px 13px;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none !important;
            cursor: pointer;
            white-space: nowrap;
            transition: .18s ease;
        }

        .ml-btn:hover {
            background: var(--ml-green-dark);
            border-color: var(--ml-green-dark);
        }

        .ml-btn.soft {
            background: #fff;
            border-color: var(--ml-border-strong);
            color: #0f172a !important;
        }

        .ml-btn.soft:hover {
            background: #f8fafc;
        }

        .ml-btn.blue {
            background: var(--ml-blue);
            border-color: var(--ml-blue);
        }

        .ml-btn.orange {
            background: var(--ml-orange);
            border-color: var(--ml-orange);
        }

        .ml-btn.red {
            background: var(--ml-red);
            border-color: var(--ml-red);
        }

        .ml-btn[disabled] {
            opacity: .6;
            cursor: not-allowed;
        }



        .ml-top-menu {
            position: relative;
            display: inline-block;
        }
        .ml-top-menu > summary {
            list-style: none;
        }
        .ml-top-menu > summary::-webkit-details-marker {
            display: none;
        }
        .ml-top-menu-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 210px;
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 14px;
            padding: 8px;
            box-shadow: 0 18px 48px rgba(15,23,42,.20);
            z-index: 90;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .ml-top-menu-panel .ml-small-btn {
            width: 100%;
            justify-content: flex-start;
        }

        .ml-source-tabs,        .ml-workflow,
        .ml-stats,
        .ml-toolbar,
        .ml-card {
            margin-bottom: 16px;
        }

        .ml-source-tabs {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 18px;
            padding: 10px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            box-shadow: var(--ml-shadow);
        }

        .ml-source-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 13px;
            border-radius: 12px;
            border: 1px solid transparent;
            color: #475569;
            background: #f8fafc;
            font-size: 12px;
            font-weight: 950;
            text-decoration: none !important;
        }

        .ml-source-tab.active {
            background: var(--ml-soft-green);
            border-color: #d9ef9d;
            color: #55720d;
        }

        .ml-workflow {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        @media(max-width:1100px) {
            .ml-workflow {
                grid-template-columns: 1fr;
            }
        }

        .ml-step {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 18px;
            padding: 15px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .ml-step-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            font-weight: 950;
        }

        .ml-step-icon.need {
            background: var(--ml-soft-blue);
            color: #2563eb;
        }

        .ml-step-icon.stock {
            background: var(--ml-soft-green);
            color: #55720d;
        }

        .ml-step-icon.order {
            background: var(--ml-soft-orange);
            color: #b45309;
        }

        .ml-step-title {
            font-size: 13px;
            font-weight: 950;
            color: #111827;
        }

        .ml-step-text {
            color: var(--ml-muted);
            font-size: 12px;
            line-height: 1.5;
            margin-top: 3px;
        }

        .ml-stats {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
        }

        @media(max-width:1300px) {
            .ml-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media(max-width:760px) {
            .ml-stats {
                grid-template-columns: 1fr;
            }
        }

        .ml-stat {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 18px;
            padding: 15px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
        }

        .ml-stat-label {
            font-size: 10px;
            color: var(--ml-muted);
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .ml-stat-value {
            margin-top: 6px;
            font-size: 23px;
            color: #111827;
            font-weight: 950;
            line-height: 1.1;
        }

        .ml-stat-sub {
            color: var(--ml-muted);
            margin-top: 4px;
            font-size: 11px;
            font-weight: 700;
        }

        .ml-toolbar {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ml-toolbar-left,
        .ml-toolbar-right {
            display: flex;
            align-items: end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ml-toolbar-left {
            flex: 1;
        }

        .ml-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 170px;
        }

        .ml-field.search {
            flex: 1;
            min-width: 280px;
        }

        .ml-label {
            color: var(--ml-muted);
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .ml-input,
        .ml-select,
        .ml-textarea {
            width: 100%;
            border: 1px solid var(--ml-border-strong);
            background: #fff;
            border-radius: 10px;
            padding: 8px 10px;
            min-height: 36px;
            font-size: 12px;
            font-weight: 750;
            color: #111827;
            outline: none;
        }

        .ml-input:focus,
        .ml-select:focus,
        .ml-textarea:focus {
            border-color: var(--ml-green);
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
        }

        .ml-textarea {
            min-height: 58px;
            resize: vertical;
            line-height: 1.45;
        }

        .ml-pane-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ml-pane-tab {
            border: 1px solid var(--ml-border);
            background: #fff;
            border-radius: 12px;
            padding: 9px 12px;
            color: #475569;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
        }

        .ml-pane-tab.active {
            color: #55720d;
            background: var(--ml-soft-green);
            border-color: #d9ef9d;
        }

        .ml-card {
            background: #fff;
            border: 1px solid var(--ml-border);
            border-radius: 18px;
            box-shadow: var(--ml-shadow);
            overflow: hidden;
        }

        .ml-table-wrap {
            overflow: auto;
            max-height: calc(100vh - 280px);
            min-height: 420px;
        }

        .ml-table {
            width: 100%;
            min-width: 1550px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .ml-table th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8fafc;
            border-bottom: 1px solid var(--ml-border);
            color: #475569;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 11px 10px;
            text-align: left;
            white-space: nowrap;
        }

        .ml-table td {
            border-bottom: 1px solid #eef2f7;
            padding: 10px;
            vertical-align: top;
            background: #fff;
        }

        .ml-table tr:hover td {
            background: #fcfcfd;
        }

        .ml-article {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            gap: 10px;
            align-items: start;
            min-width: 280px;
        }

        .ml-img {
            width: 52px;
            height: 52px;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
            object-fit: contain;
            background: #fff;
            padding: 4px;
        }

        .ml-article-title {
            font-size: 13px;
            font-weight: 950;
            color: #111827;
            line-height: 1.3;
        }

        .ml-article-meta {
            font-size: 11px;
            color: var(--ml-muted);
            line-height: 1.45;
            margin-top: 4px;
        }

        .ml-code {
            display: inline-flex;
            max-width: 100%;
            padding: 2px 6px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            font-weight: 900;
            margin-top: 4px;
            word-break: break-all;
        }

        .ml-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 5px 8px;
            font-size: 10px;
            font-weight: 950;
            white-space: nowrap;
        }

        .ml-pill.lager {
            background: var(--ml-soft-green);
            color: #55720d;
            border: 1px solid #d9ef9d;
        }

        .ml-pill.bestellen {
            background: var(--ml-soft-red);
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .ml-pill.teilweise {
            background: var(--ml-soft-orange);
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .ml-pill.unbekannt {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .ml-pill.changed {
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .ml-pill.added {
            background: var(--ml-soft-green);
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .ml-pill.removed {
            background: var(--ml-soft-red);
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .ml-pill.same {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .ml-qty-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            min-width: 160px;
        }

        .ml-mini {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 7px;
        }

        .ml-mini-label {
            display: block;
            color: #64748b;
            font-size: 9px;
            font-weight: 950;
            text-transform: uppercase;
        }

        .ml-mini-val {
            margin-top: 2px;
            color: #111827;
            font-size: 13px;
            font-weight: 950;
        }

        .ml-loc-grid,
        .ml-order-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 6px;
            min-width: 250px;
        }

        .ml-loc-grid .full,
        .ml-order-grid .full {
            grid-column: 1 / -1;
        }

        .ml-row-actions {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 145px;
        }

        .ml-row-actions-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ml-small-btn {
            min-height: 30px;
            border: 1px solid var(--ml-border-strong);
            background: #fff;
            color: #334155;
            border-radius: 9px;
            padding: 6px 8px;
            font-size: 10px;
            font-weight: 950;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .ml-small-btn:hover {
            background: #f8fafc;
        }

        .ml-small-btn.ok {
            border-color: #bbf7d0;
            background: #ecfdf5;
            color: #047857;
        }

        .ml-small-btn.warn {
            border-color: #fde68a;
            background: #fffbeb;
            color: #b45309;
        }

        .ml-small-btn.bad {
            border-color: #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .ml-small-btn.info {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #2563eb;
        }

        /* =========================================================
               Compact row + collapsible Lager / Bestellung panels
               ========================================================= */
            .ml-table td {
                padding-top: 7px;
                padding-bottom: 7px;
            }

            .ml-table tr.is-row-open td {
                background: #fcfcfd;
            }

            .ml-compact-summary {
                min-width: 260px;
                display: flex;
                flex-direction: column;
                gap: 7px;
            }

            .ml-summary-line {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                border-radius: 12px;
                padding: 8px 10px;
                min-height: 38px;
            }

            .ml-summary-main {
                min-width: 0;
            }

            .ml-summary-title {
                color: #111827;
                font-size: 12px;
                font-weight: 950;
                line-height: 1.25;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 230px;
            }

            .ml-summary-sub {
                color: #64748b;
                font-size: 10px;
                font-weight: 800;
                margin-top: 2px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 230px;
            }

            .ml-toggle-panel {
                min-height: 28px;
                border: 1px solid #cbd5e1;
                background: #fff;
                color: #334155;
                border-radius: 9px;
                padding: 5px 8px;
                font-size: 10px;
                font-weight: 950;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                flex: 0 0 auto;
            }

            .ml-toggle-panel:hover {
                background: #f1f5f9;
            }

            .ml-collapse-panel {
                display: none;
                border: 1px solid #e2e8f0;
                background: #ffffff;
                border-radius: 14px;
                padding: 9px;
                box-shadow: inset 0 0 0 1px rgba(241,245,249,.72);
            }

            .mat-row.is-lager-open .ml-collapse-panel[data-panel="lager"],
            .mat-row.is-order-open .ml-collapse-panel[data-panel="order"],
            .mat-row.is-note-open .ml-collapse-panel[data-panel="note"] {
                display: block;
            }

            .ml-inline-actions {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            .ml-sticky-actions {
                position: sticky;
                right: 0;
                background: linear-gradient(90deg, rgba(255,255,255,.88), #fff 28%);
                z-index: 2;
            }



            /* Row action menu instead of many visible buttons */
            .ml-action-menu {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            .ml-action-menu > summary {
                list-style: none;
                min-height: 34px;
                border: 1px solid #bfdbfe;
                background: #eff6ff;
                color: #2563eb;
                border-radius: 10px;
                padding: 7px 10px;
                font-size: 11px;
                font-weight: 950;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                user-select: none;
            }

            .ml-action-menu > summary::-webkit-details-marker {
                display: none;
            }

            .ml-action-menu[open] > summary {
                background: #dbeafe;
                border-color: #93c5fd;
            }

            .ml-action-menu-panel {
                position: absolute;
                top: calc(100% + 8px);
                right: 0;
                z-index: 80;
                width: 220px;
                padding: 8px;
                border: 1px solid #dbe4ee;
                border-radius: 14px;
                background: #ffffff;
                box-shadow: 0 18px 48px rgba(15,23,42,.20);
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .ml-action-menu-panel .ml-small-btn {
                width: 100%;
                justify-content: flex-start;
                min-height: 34px;
                font-size: 11px;
            }

            .ml-action-menu-sep {
                height: 1px;
                background: #e5e7eb;
                margin: 3px 0;
            }

            .ml-sticky-actions {
                overflow: visible !important;
            }

            .ml-row-save-line {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
                margin-top: 8px;
            }

            .ml-note-preview {
                min-width: 180px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                border-radius: 12px;
                padding: 8px 10px;
                color: #475569;
                font-size: 11px;
                line-height: 1.45;
                font-weight: 750;
            }

            .ml-table.is-lager-tab th:nth-child(4),
            .ml-table.is-lager-tab td:nth-child(4) {
                display: none;
            }

            .ml-table.is-order-tab th:nth-child(3),
            .ml-table.is-order-tab td:nth-child(3) {
                display: none;
            }

            .ml-table.is-lager-tab,
            .ml-table.is-order-tab {
                min-width: 1180px;
            }

            @media(max-width:900px) {
                .ml-table {
                    min-width: 1120px;
                }

                .ml-summary-title,
                .ml-summary-sub {
                    max-width: 190px;
                }
            }

            .ml-history-list {
                display: flex;
                flex-direction: column;
                gap: 9px;
                max-height: 60vh;
                overflow: auto;
                padding-right: 4px;
            }

            .ml-history-item {
                border: 1px solid var(--ml-border);
                background: #fff;
                border-radius: 14px;
                padding: 11px;
            }

            .ml-history-head {
                display: flex;
                justify-content: space-between;
                gap: 8px;
                font-size: 12px;
                font-weight: 950;
                color: #111827;
                margin-bottom: 5px;
            }

            .ml-history-meta {
                color: var(--ml-muted);
                font-size: 11px;
                line-height: 1.45;
                white-space: pre-wrap;
            }

            .ml-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1300;
                background: rgba(15, 23, 42, .58);
                display: none;
                align-items: center;
                justify-content: center;
                padding: 18px;
            }

            .ml-modal-backdrop.show {
                display: flex;
            }

            .ml-modal {
                width: min(760px, 100%);
                background: #fff;
                border-radius: 22px;
                overflow: hidden;
                box-shadow: 0 30px 90px rgba(15, 23, 42, .30);
            }

            .ml-modal-head {
                padding: 15px 18px;
                border-bottom: 1px solid var(--ml-border);
                background: #f8fafc;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .ml-modal-title {
                margin: 0;
                font-size: 17px;
                font-weight: 950;
                color: #111827;
            }

            .ml-modal-body {
                padding: 18px;
                max-height: 72vh;
                overflow: auto;
            }

            .ml-toast-wrap {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 99999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .ml-toast {
                width: min(390px, calc(100vw - 32px));
                background: #fff;
                border: 1px solid var(--ml-border);
                border-left: 5px solid var(--ml-green);
                border-radius: 16px;
                box-shadow: var(--ml-shadow);
                padding: 13px;
                display: flex;
                gap: 10px;
            }

            .ml-toast.error {
                border-left-color: var(--ml-red);
            }

            .ml-toast-title {
                font-size: 13px;
                font-weight: 950;
            }

            .ml-toast-msg {
                margin-top: 3px;
                color: var(--ml-muted);
                font-size: 12px;
                line-height: 1.45;
                white-space: pre-wrap;
            }

            .print-only {
                display: none;
            }

            @media(max-width:900px) {
                .ml-titlebar {
                    padding: 16px;
                }

                .ml-title {
                    font-size: 22px;
                }

                .ml-actions {
                    justify-content: flex-start;
                }

                .ml-field,
                .ml-field.search {
                    min-width: 100%;
                    width: 100%;
                }

                .ml-toolbar-left,
                .ml-toolbar-right {
                    width: 100%;
                }

                .ml-btn {
                    width: 100%;
                }

                .ml-pane-tabs {
                    width: 100%;
                }

                .ml-pane-tab {
                    flex: 1;
                }

                .ml-table-wrap {
                    max-height: none;
                    min-height: 0;
                }
            }

            @media print {
                @page {
                    size: A4 landscape;
                    margin: 10mm;
                }

                body * {
                    visibility: hidden !important;
                }

                .material-print-scope,
                .material-print-scope * {
                    visibility: visible !important;
                }

                .material-print-scope {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    background: #fff;
                    margin: 0;
                    padding: 0;
                }

                .no-print {
                    display: none !important;
                }

                .print-only {
                    display: block !important;
                }

                .ml-titlebar,
                .ml-card {
                    border: 0;
                    box-shadow: none;
                    border-radius: 0;
                    padding: 0;
                }

                .ml-table-wrap {
                    overflow: visible !important;
                    max-height: none !important;
                }

                .ml-table {
                    min-width: 100% !important;
                    font-size: 10px;
                }

                .ml-table th {
                    position: static !important;
                    background: #f1f5f9 !important;
                    color: #111827 !important;
                }

                .ml-table th.no-print-col,
                .ml-table td.no-print-col {
                    display: none !important;
                }

                .ml-row-actions,
                .ml-input,
                .ml-select,
                .ml-textarea,
                .ml-small-btn {
                    display: none !important;
                }

                html[data-material-print-mode="lager"] .mat-row:not(.print-lager-row) {
                    display: none !important;
                }

                html[data-material-print-mode="order"] .mat-row:not(.print-order-row) {
                    display: none !important;
                }

                html[data-material-print-mode="normal"] .print-lager-extra,
                html[data-material-print-mode="normal"] .print-order-extra {
                    display: none !important;
                }

                html[data-material-print-mode="lager"] .print-order-extra {
                    display: none !important;
                }

                html[data-material-print-mode="order"] .print-lager-extra {
                    display: none !important;
                }
            }
        </style>
@endsection

@php
    use Illuminate\Support\Str;

    $source = $source ?? request('source', 'compare');
    if (!in_array($source, ['offer', 'feinaufmass', 'compare'], true)) {
        $source = 'compare';
    }

    $allMaterials = collect($materials ?? []);
    $employees = collect($employees ?? []);
    $distributors = collect($distributors ?? []);
    $brands = collect($brands ?? []);

    $statusLabels = [
        'lager' => 'Im Lager',
        'bestellen' => 'Bestellen',
        'teilweise' => 'Teilweise',
        'unbekannt' => 'Unbekannt',
    ];

    $changeLabels = [
        'same' => 'Unverändert',
        'changed' => 'Geändert',
        'added' => 'Neu',
        'removed' => 'Entfernt',
    ];

    $sourceLabels = [
        'offer' => 'Angebot',
        'feinaufmass' => 'Feinaufmaß',
        'compare' => 'Alt / Neu Vergleich',
    ];

    $customerName = trim(($customer->firma ?? '') . ' ' . ($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) ?: '—';
    $documentTitle = $materialDocumentTitle ?? ('Materialliste ' . ($offerDetail->offer_no ?? ('#' . $offerDetail->id)));
    $documentSubtitle = $materialDocumentSubtitle ?? ('Kunde: ' . $customerName);

    $routeWithSource = function (string $targetSource) use ($offerDetail) {
        return route('deal.material.list', array_merge(
            ['offerDetail' => $offerDetail->id],
            request()->except('source'),
            ['source' => $targetSource]
        ));
    };

    $fmtQty = fn($value) => number_format((float) $value, 2, ',', '.');

    $imageUrl = function ($img) {
        if (!$img) {
            return asset('images/icons/placeholder.svg');
        }

        $img = (string) $img;

        if (Str::startsWith($img, ['http://', 'https://', 'data:', '/'])) {
            return $img;
        }

        if (Str::contains($img, ['images/products/', 'storage/'])) {
            return asset(ltrim($img, '/'));
        }

        return asset('images/products/' . ltrim($img, '/'));
    };

    $safeHistory = function ($row) {
        $history = $row['material_history'] ?? [];
        if (!is_array($history)) {
            return [];
        }
        return array_values($history);
    };

    $printModes = $materialPrintModes ?? [
        'normal' => 'Normaldruck',
        'lager' => 'Lagerdruck',
        'order' => 'Bestelldruck',
    ];
@endphp

@section('content')
<div class="ml-wrap material-print-scope">
    <div class="ml-titlebar">
        <div>
            <div class="ml-kicker">Materialplanung</div>
            <h1 class="ml-title">{{ $documentTitle }}</h1>
            <div class="ml-sub">
                {{ $documentSubtitle }}
                <span class="print-only">
                    <br>Gedruckt am: {{ now()->format('d.m.Y H:i') }}
                </span>
            </div>
            <div class="ml-breadcrumb no-print">
                <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                <span>›</span>
                <a href="{{ url('/deal_all_list') }}">Aufträge</a>
                <span>›</span>
                <span>{{ $sourceLabels[$source] ?? 'Materialliste' }}</span>
            </div>
        </div>

        <div class="ml-actions no-print">
            <a href="{{ url()->previous() }}" class="ml-btn soft">Zurück</a>
            <details class="ml-top-menu no-print">
                <summary class="ml-btn soft">☰ Drucken / Aktionen</summary>
                <div class="ml-top-menu-panel">
                    <button type="button" class="ml-small-btn info js-print-mode" data-print-mode="normal">Normal drucken</button>
                    <button type="button" class="ml-small-btn ok js-print-mode" data-print-mode="lager">Lager drucken</button>
                    <button type="button" class="ml-small-btn warn js-print-mode" data-print-mode="order">Bestellung drucken</button>
                </div>
            </details>
            @if(($canApplyFeinaufmass ?? false) && Route::has('deal.material.apply-feinaufmass'))
                <button type="button" class="ml-btn red js-apply-feinaufmass">Freigegebene übernehmen</button>
            @endif
        </div>
    </div>

    <div class="ml-source-tabs no-print">
        <a href="{{ $routeWithSource('offer') }}" class="ml-source-tab {{ $source === 'offer' ? 'active' : '' }}">
            Angebot
            <span class="ml-code">{{ $offerMaterialsCount ?? 0 }}</span>
        </a>

        @if($hasFeinaufmassData ?? false)
            <a href="{{ $routeWithSource('feinaufmass') }}"
                class="ml-source-tab {{ $source === 'feinaufmass' ? 'active' : '' }}">
                Feinaufmaß
                <span class="ml-code">{{ $measurementMaterialsCount ?? 0 }}</span>
            </a>
            <a href="{{ $routeWithSource('compare') }}" class="ml-source-tab {{ $source === 'compare' ? 'active' : '' }}">
                Alt / Neu
            </a>
        @else
            <span class="ml-source-tab">Kein Feinaufmaß vorhanden</span>
        @endif

        <span class="ml-source-tab">
            Quelle: {{ $snapshotSourceLabel ?? ($measurementSnapshotSource ?? '—') }}
        </span>
    </div>

    <div class="ml-workflow no-print">
        <div class="ml-step">
            <div class="ml-step-icon need">1</div>
            <div>
                <div class="ml-step-title">Bedarf prüfen</div>
                <div class="ml-step-text">Menge kommt aus Angebot, Feinaufmaß oder Vergleich. Pflicht ist die benötigte
                    Menge.</div>
            </div>
        </div>
        <div class="ml-step">
            <div class="ml-step-icon stock">2</div>
            <div>
                <div class="ml-step-title">Lager zuordnen</div>
                <div class="ml-step-text">Wenn Material vorhanden ist, Lagerort, Raum, Regal, Reihe und Fach speichern.
                </div>
            </div>
        </div>
        <div class="ml-step">
            <div class="ml-step-icon order">3</div>
            <div>
                <div class="ml-step-title">Bestellung planen</div>
                <div class="ml-step-text">Wenn Material fehlt, Lieferant/Hersteller/manuelle Quelle und Liefertermin
                    speichern.</div>
            </div>
        </div>
    </div>

    <div class="ml-stats no-print">
        <div class="ml-stat">
            <div class="ml-stat-label">Positionen</div>
            <div class="ml-stat-value">{{ $analytics['total_positions'] ?? $allMaterials->count() }}</div>
            <div class="ml-stat-sub">sichtbare Zeilen</div>
        </div>
        <div class="ml-stat">
            <div class="ml-stat-label">Artikel</div>
            <div class="ml-stat-value">
                {{ $analytics['unique_articles'] ?? $allMaterials->pluck('item_key')->unique()->count() }}</div>
            <div class="ml-stat-sub">eindeutig</div>
        </div>
        <div class="ml-stat">
            <div class="ml-stat-label">Gesamtmenge</div>
            <div class="ml-stat-value">{{ $fmtQty($analytics['total_qty'] ?? $allMaterials->sum('qty')) }}</div>
            <div class="ml-stat-sub">alle Positionen</div>
        </div>
        <div class="ml-stat">
            <div class="ml-stat-label">Im Lager</div>
            <div class="ml-stat-value" style="color:var(--ml-success);">
                {{ $analytics['lager_count'] ?? $allMaterials->where('stock_status', 'lager')->count() }}</div>
            <div class="ml-stat-sub">vollständig da</div>
        </div>
        <div class="ml-stat">
            <div class="ml-stat-label">Teilweise</div>
            <div class="ml-stat-value" style="color:var(--ml-orange);">
                {{ $analytics['teilweise_count'] ?? $allMaterials->where('stock_status', 'teilweise')->count() }}</div>
            <div class="ml-stat-sub">Lager + Bestellung</div>
        </div>
        <div class="ml-stat">
            <div class="ml-stat-label">Bestellen</div>
            <div class="ml-stat-value" style="color:var(--ml-red);">
                {{ $analytics['bestellen_count'] ?? $allMaterials->whereIn('stock_status', ['bestellen', 'teilweise'])->count() }}
            </div>
            <div class="ml-stat-sub">offener Bedarf</div>
        </div>
    </div>

    <div class="ml-toolbar no-print">
        <div class="ml-toolbar-left">
            <div class="ml-field search">
                <label class="ml-label">Suche</label>
                <input type="text" class="ml-input" id="mlSearch"
                    placeholder="Artikel, Nummer, Lieferant, Ort, Bestellung suchen...">
            </div>
            <div class="ml-field">
                <label class="ml-label">Status</label>
                <select class="ml-select" id="mlStatusFilter">
                    <option value="">Alle</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if($source === 'compare')
                <div class="ml-field">
                    <label class="ml-label">Änderung</label>
                    <select class="ml-select" id="mlChangeFilter">
                        <option value="">Alle</option>
                        @foreach($changeLabels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <div class="ml-toolbar-right">
            <div class="ml-pane-tabs">
                <button type="button" class="ml-pane-tab active" data-local-tab="all">Alle</button>
                <button type="button" class="ml-pane-tab" data-local-tab="lager">Lager</button>
                <button type="button" class="ml-pane-tab" data-local-tab="order">Bestellung</button>
                <button type="button" class="ml-pane-tab" data-local-tab="unknown">Offen</button>
            </div>
            <div class="ml-inline-actions" style="margin-top:8px;">
                <button type="button" class="ml-small-btn info js-toggle-all" data-panel-target="lager">Alle Lagerorte auf/zu</button>
                <button type="button" class="ml-small-btn warn js-toggle-all" data-panel-target="order">Alle Bestellungen auf/zu</button>
            </div>
        </div>
    </div>

    <div class="ml-card">
        <div class="ml-table-wrap">
            <table class="ml-table" id="mlMaterialTable">
                <thead>
                    <tr>
                        <th>Artikel</th>
                        <th>Menge / Status</th>
                        <th>Lagerort</th>
                        <th>Bestellung</th>
                        <th>Notiz</th>
                        <th class="no-print-col">Aktionen</th>
                    </tr>
                </thead>
                <tbody id="mlRows">
                    @forelse($allMaterials as $material)
                    @php
                        $itemKey = (string) ($material['item_key'] ?? md5(json_encode($material)));
                        $requiredQty = (float) ($material['required_qty'] ?? $material['qty'] ?? $material['verbrauch_qty'] ?? 0);
                        $foundQty = (float) ($material['found_qty'] ?? $material['stock_qty'] ?? 0);
                        $missingQty = (float) ($material['missing_qty'] ?? max($requiredQty - $foundQty, 0));
                        $orderQty = (float) ($material['order_qty'] ?? $missingQty);
                        $status = $material['stock_status'] ?? null;

                        if (!$status || !isset($statusLabels[$status])) {
                            if ($requiredQty > 0 && $foundQty >= $requiredQty) {
                                $status = 'lager';
                            } elseif ($foundQty > 0 && $orderQty > 0) {
                                $status = 'teilweise';
                            } elseif ($orderQty > 0 || $missingQty > 0) {
                                $status = 'bestellen';
                            } else {
                                $status = 'unbekannt';
                            }
                        }

                        $changeType = $material['change_type'] ?? 'same';
                        $location = $material['location_details'] ?? $material['location'] ?? [];
                        if (!is_array($location)) {
                            $location = [];
                        }

                        $order = $material['order_details'] ?? $material['purchase_order'] ?? [];
                        if (!is_array($order)) {
                            $order = [];
                        }

                        $historyRows = $safeHistory($material);

                        $searchBlob = strtolower(trim(implode(' ', [
                            $material['name'] ?? '',
                            $material['article_no'] ?? '',
                            $material['distributor_article_no'] ?? '',
                            $material['distributor_name'] ?? '',
                            $material['supplier_name'] ?? '',
                            $material['brand_name'] ?? '',
                            $location['location_label'] ?? '',
                            $location['room_name'] ?? '',
                            $location['rack_name'] ?? '',
                            $order['source_name'] ?? '',
                            $order['order_no'] ?? '',
                        ])));

                        $rowClasses = [
                            'mat-row',
                            in_array($status, ['lager', 'teilweise'], true) ? 'print-lager-row' : '',
                            in_array($status, ['bestellen', 'teilweise'], true) ? 'print-order-row' : '',
                        ];

                        $historyJson = e(json_encode($historyRows, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_QUOT));
                    @endphp

                    <tr class="{{ implode(' ', array_filter($rowClasses)) }}" data-item-key="{{ $itemKey }}"
                        data-status="{{ $status }}" data-change="{{ $changeType }}" data-search="{{ e($searchBlob) }}"
                        data-history="{{ $historyJson }}">
                        <td>
                            <div class="ml-article">
                                <img class="ml-img" src="{{ $imageUrl($material['img'] ?? null) }}" alt="">
                                <div>
                                    <div class="ml-article-title">{{ $material['name'] ?? 'Unbekanntes Material' }}
                                    </div>
                                    <div class="ml-article-meta">
                                        {{ $material['section_title'] ?? 'Material' }}
                                        @if(!empty($material['master_set_name']))
                                            <br>Set: {{ $material['master_set_name'] }}
                                        @endif
                                        @if(!empty($material['description']))
                                            <br>{{ Str::limit((string) $material['description'], 110) }}
                                        @endif
                                    </div>
                                    <span
                                        class="ml-code">{{ $material['article_no'] ?? $material['distributor_article_no'] ?? 'ohne Artikelnummer' }}</span>
                                    @if($source === 'compare')
                                        <span
                                            class="ml-pill {{ $changeType }}">{{ $changeLabels[$changeType] ?? $changeType }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="ml-qty-grid">
                                <div class="ml-mini">
                                    <span class="ml-mini-label">Bedarf</span>
                                    <span class="ml-mini-val">{{ $fmtQty($requiredQty) }}
                                        {{ $material['unit'] ?? $material['measure'] ?? '' }}</span>
                                </div>
                                <div class="ml-mini">
                                    <span class="ml-mini-label">Lager</span>
                                    <span class="ml-mini-val">{{ $fmtQty($foundQty) }}</span>
                                </div>
                                <div class="ml-mini">
                                    <span class="ml-mini-label">Bestellen</span>
                                    <span class="ml-mini-val">{{ $fmtQty($orderQty) }}</span>
                                </div>
                                <div class="ml-mini">
                                    <span class="ml-mini-label">Status</span>
                                    <span class="ml-pill {{ $status }}">{{ $statusLabels[$status] ?? $status }}</span>
                                </div>
                            </div>

                            <div class="no-print" style="margin-top:8px;display:grid;gap:6px;">
                                <input class="ml-input js-required-qty" type="number" step="0.01" min="0"
                                    value="{{ $requiredQty }}" placeholder="Bedarf">
                                <input class="ml-input js-found-qty" type="number" step="0.01" min="0"
                                    value="{{ $foundQty }}" placeholder="Im Lager gefunden">
                                <select class="ml-select js-stock-status">
                                    @foreach($statusLabels as $value => $label)
                                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>

                        <td class="print-lager-extra">
                            <div class="ml-compact-summary">
                                <div class="ml-summary-line">
                                    <div class="ml-summary-main">
                                        <div class="ml-summary-title js-location-summary-title">
                                            {{ $location['location_label'] ?? 'Kein Lagerort' }}
                                        </div>
                                        <div class="ml-summary-sub js-location-summary-sub">
                                            Raum {{ $location['room_name'] ?? $location['room_number'] ?? '—' }} · Regal {{ $location['rack_name'] ?? '—' }} · Fach {{ $location['shelf'] ?? '—' }}
                                        </div>
                                    </div>
                                    <button type="button" class="ml-toggle-panel js-toggle-panel" data-panel-target="lager">Lager öffnen</button>
                                </div>

                                <div class="ml-collapse-panel" data-panel="lager">
                                    <div class="ml-loc-grid">
                                        <input class="ml-input js-location-label full"
                                            value="{{ $location['location_label'] ?? '' }}" placeholder="Lager / Standort">
                                        <input class="ml-input js-room-name" value="{{ $location['room_name'] ?? '' }}"
                                            placeholder="Raum">
                                        <input class="ml-input js-room-number" value="{{ $location['room_number'] ?? '' }}"
                                            placeholder="Raum-Nr.">
                                        <input class="ml-input js-rack-name" value="{{ $location['rack_name'] ?? '' }}"
                                            placeholder="Regal">
                                        <input class="ml-input js-row" value="{{ $location['row'] ?? '' }}" placeholder="Reihe">
                                        <input class="ml-input js-column" value="{{ $location['column'] ?? '' }}"
                                            placeholder="Spalte">
                                        <input class="ml-input js-shelf" value="{{ $location['shelf'] ?? '' }}"
                                            placeholder="Fach">
                                    </div>
                                    <div class="ml-row-save-line">
                                        <button type="button" class="ml-small-btn ok js-save-stock">Lager speichern</button>
                                    </div>
                                </div>
                            </div>

                            <div class="print-only">
                                <strong>Lagerort:</strong>
                                {{ $location['location_label'] ?? '—' }},
                                Raum {{ $location['room_name'] ?? $location['room_number'] ?? '—' }},
                                Regal {{ $location['rack_name'] ?? '—' }},
                                Reihe {{ $location['row'] ?? '—' }},
                                Fach {{ $location['shelf'] ?? '—' }}
                            </div>
                        </td>

                        <td class="print-order-extra">
                            <div class="ml-compact-summary">
                                <div class="ml-summary-line">
                                    <div class="ml-summary-main">
                                        <div class="ml-summary-title js-order-summary-title">
                                            {{ $order['source_name'] ?? $order['manual_source_name'] ?? $material['distributor_name'] ?? 'Keine Bestellung' }}
                                        </div>
                                        <div class="ml-summary-sub js-order-summary-sub">
                                            Menge {{ $fmtQty($order['order_qty'] ?? $orderQty) }} · Status {{ $order['order_status'] ?? $material['order_status'] ?? 'offen' }} · Lieferung {{ $order['expected_delivery_at'] ?? '—' }}
                                        </div>
                                    </div>
                                    <button type="button" class="ml-toggle-panel js-toggle-panel" data-panel-target="order">Bestellung öffnen</button>
                                </div>

                                <div class="ml-collapse-panel" data-panel="order">
                                    <div class="ml-order-grid">
                                <input class="ml-input js-order-qty" type="number" step="0.01" min="0"
                                    value="{{ $order['order_qty'] ?? $orderQty }}" placeholder="Bestellmenge">
                                <select class="ml-select js-order-status">
                                    @foreach(['open' => 'Offen', 'ordered' => 'Bestellt', 'delivered' => 'Geliefert', 'cancelled' => 'Storniert'] as $value => $label)
                                        <option value="{{ $value }}" @selected(($order['order_status'] ?? $material['order_status'] ?? 'open') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>

                                <select class="ml-select js-source-type">
                                    @foreach(['manual' => 'Manuell', 'distributor' => 'Großhändler', 'brand' => 'Hersteller'] as $value => $label)
                                        <option value="{{ $value }}" @selected(($order['source_type'] ?? 'manual') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>

                                <select class="ml-select js-distributor-id">
                                    <option value="">Großhändler</option>
                                    @foreach($distributors as $distributor)
                                        <option value="{{ $distributor->id }}" @selected((string) ($order['distributor_id'] ?? '') === (string) $distributor->id)>
                                            {{ $distributor->name ?? $distributor->short_name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select class="ml-select js-brand-id">
                                    <option value="">Hersteller</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected((string) ($order['brand_id'] ?? '') === (string) $brand->id)>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <input class="ml-input js-manual-source"
                                    value="{{ $order['manual_source_name'] ?? $order['source_name'] ?? '' }}"
                                    placeholder="Quelle manuell">

                                <select class="ml-select js-ordered-by">
                                    <option value="">Bestellt von</option>
                                    @foreach($employees as $employee)
                                    @php($empName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: ('#' . $employee->id))
                                    <option value="{{ $employee->id }}" @selected((string) ($order['ordered_by_employee_id'] ?? '') === (string) $employee->id)>
                                        {{ $empName }}
                                    </option>
                                    @endforeach
                                </select>

                                <input class="ml-input js-order-no" value="{{ $order['order_no'] ?? '' }}"
                                    placeholder="Bestellnummer">
                                <input class="ml-input js-ordered-at" type="date"
                                    value="{{ $order['ordered_at'] ?? '' }}">
                                <input class="ml-input js-expected-delivery" type="date"
                                    value="{{ $order['expected_delivery_at'] ?? '' }}">
                                <select class="ml-select js-delivery-target">
                                    @foreach(['company' => 'Firma', 'warehouse' => 'Lager', 'customer' => 'Kunde'] as $value => $label)
                                        <option value="{{ $value }}" @selected(($order['delivery_target'] ?? '') === $value)>
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                                <input class="ml-input js-delivery-address full"
                                    value="{{ $order['delivery_address'] ?? '' }}" placeholder="Lieferadresse">
                                    </div>
                                    <div class="ml-row-save-line">
                                        <button type="button" class="ml-small-btn warn js-save-order">Bestellung speichern</button>
                                    </div>
                                </div>
                            </div>

                            <div class="print-only">
                                <strong>Bestellung:</strong>
                                {{ $order['source_name'] ?? $order['manual_source_name'] ?? $material['distributor_name'] ?? '—' }}
                                · Status: {{ $order['order_status'] ?? $material['order_status'] ?? 'offen' }}
                                · Lieferung: {{ $order['expected_delivery_at'] ?? '—' }}
                                · Bestell-Nr.: {{ $order['order_no'] ?? '—' }}
                            </div>
                        </td>

                        <td>
                            <div class="ml-compact-summary">
                                <div class="ml-summary-line">
                                    <div class="ml-summary-main">
                                        <div class="ml-summary-title">Notiz</div>
                                        <div class="ml-summary-sub js-note-summary">
                                            {{ Str::limit((string) ($material['note'] ?? $material['lager_note'] ?? 'Keine Notiz'), 72) }}
                                        </div>
                                    </div>
                                    <button type="button" class="ml-toggle-panel js-toggle-panel" data-panel-target="note">Notiz öffnen</button>
                                </div>

                                <div class="ml-collapse-panel" data-panel="note">
                                    <textarea class="ml-textarea js-note"
                                        placeholder="Notiz für Lager oder Bestellung">{{ $material['note'] ?? $material['lager_note'] ?? '' }}</textarea>
                                </div>
                            </div>

                            @if(!empty($material['updated_by_name']) || !empty($material['updated_at']))
                                <div class="ml-article-meta" style="margin-top:6px;">
                                    Letzte Änderung:
                                    {{ $material['updated_by_name'] ?? '—' }}
                                    @if(!empty($material['updated_at']))
                                        · {{ \Carbon\Carbon::parse($material['updated_at'])->format('d.m.Y H:i') }}
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td class="no-print-col ml-sticky-actions">
                            <details class="ml-action-menu">
                                <summary>☰ Menü</summary>
                                <div class="ml-action-menu-panel">
                                    <button type="button" class="ml-small-btn info js-toggle-panel" data-panel-target="lager">Lagerort öffnen / schließen</button>
                                    <button type="button" class="ml-small-btn warn js-toggle-panel" data-panel-target="order">Bestellung öffnen / schließen</button>
                                    <button type="button" class="ml-small-btn info js-toggle-panel" data-panel-target="note">Notiz öffnen / schließen</button>
                                    <div class="ml-action-menu-sep"></div>
                                    <button type="button" class="ml-small-btn ok js-auto-lager">Alles Lager</button>
                                    <button type="button" class="ml-small-btn bad js-auto-order">Alles bestellen</button>
                                    <div class="ml-action-menu-sep"></div>
                                    <button type="button" class="ml-small-btn ok js-save-stock">Lager speichern</button>
                                    <button type="button" class="ml-small-btn warn js-save-order">Bestellung speichern</button>
                                    <button type="button" class="ml-small-btn info js-history">Historie ({{ count($historyRows) }})</button>
                                </div>
                            </details>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div style="padding:50px;text-align:center;color:#64748b;font-weight:900;">Keine
                                Materialpositionen gefunden.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="ml-modal-backdrop no-print" id="mlHistoryModal">
    <div class="ml-modal">
        <div class="ml-modal-head">
            <h3 class="ml-modal-title" id="mlHistoryTitle">Materialhistorie</h3>
            <button type="button" class="ml-small-btn js-close-modal">Schließen</button>
        </div>
        <div class="ml-modal-body">
            <div class="ml-history-list" id="mlHistoryList"></div>
        </div>
    </div>
</div>

<div class="ml-toast-wrap no-print" id="mlToastWrap"></div>
@endsection

@push('scripts')
    <script>
        (function () {
            'use strict';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            const routes = {
                update: @json(route('deal.material.update', $offerDetail->id)),
                order: @json(route('deal.material.order-details', $offerDetail->id)),
                apply: @json(Route::has('deal.material.apply-feinaufmass') ? route('deal.material.apply-feinaufmass', $offerDetail->id) : null),
            };

            const $ = (selector, context = document) => context.querySelector(selector);
            const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

            function toast(type, title, message) {
                const wrap = $('#mlToastWrap');
                if (!wrap) return;

                const el = document.createElement('div');
                el.className = 'ml-toast ' + (type === 'error' ? 'error' : '');
                el.innerHTML = `
                <div style="width:32px;height:32px;border-radius:12px;background:${type === 'error' ? '#fef2f2' : '#ecfdf5'};color:${type === 'error' ? '#b91c1c' : '#047857'};display:flex;align-items:center;justify-content:center;font-weight:950;">
                    ${type === 'error' ? '!' : '✓'}
                </div>
                <div style="flex:1;">
                    <div class="ml-toast-title"></div>
                    <div class="ml-toast-msg"></div>
                </div>
                <button type="button" style="border:0;background:transparent;color:#94a3b8;font-size:18px;" onclick="this.closest('.ml-toast').remove()">×</button>
            `;

                $('.ml-toast-title', el).textContent = title || '';
                $('.ml-toast-msg', el).textContent = message || '';

                wrap.appendChild(el);
                setTimeout(() => { try { el.remove(); } catch (e) { } }, 4500);
            }

            async function postJson(url, payload) {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const text = await response.text();
                let data = {};
                try {
                    data = text ? JSON.parse(text) : {};
                } catch (e) {
                    throw new Error(text || 'Ungültige Serverantwort.');
                }

                if (!response.ok || data.success === false) {
                    let msg = data.message || 'Serverfehler.';
                    if (data.errors) {
                        const details = Object.values(data.errors).flat().join('\n');
                        if (details) msg += '\n' + details;
                    }
                    throw new Error(msg);
                }

                return data;
            }

            function rowPayloadBase(row) {
                return {
                    item_key: row.dataset.itemKey,
                    source: @json($source),
                    required_qty: parseFloat($('.js-required-qty', row)?.value || '0') || 0,
                    found_qty: parseFloat($('.js-found-qty', row)?.value || '0') || 0,
                    found_unit: @json($allMaterials->first()['unit'] ?? null),
                    note: $('.js-note', row)?.value || ''
                };
            }

            function stockPayload(row) {
                return {
                    ...rowPayloadBase(row),
                    stock_status: $('.js-stock-status', row)?.value || 'unbekannt',
                    location_label: $('.js-location-label', row)?.value || '',
                    room_name: $('.js-room-name', row)?.value || '',
                    room_number: $('.js-room-number', row)?.value || '',
                    rack_name: $('.js-rack-name', row)?.value || '',
                    row: $('.js-row', row)?.value || '',
                    column: $('.js-column', row)?.value || '',
                    shelf: $('.js-shelf', row)?.value || ''
                };
            }

            function orderPayload(row) {
                return {
                    item_key: row.dataset.itemKey,
                    order_qty: parseFloat($('.js-order-qty', row)?.value || '0') || 0,
                    order_status: $('.js-order-status', row)?.value || 'open',
                    source_type: $('.js-source-type', row)?.value || 'manual',
                    distributor_id: $('.js-distributor-id', row)?.value || null,
                    brand_id: $('.js-brand-id', row)?.value || null,
                    manual_source_name: $('.js-manual-source', row)?.value || '',
                    ordered_by_employee_id: $('.js-ordered-by', row)?.value || null,
                    ordered_at: $('.js-ordered-at', row)?.value || null,
                    expected_delivery_at: $('.js-expected-delivery', row)?.value || null,
                    delivery_target: $('.js-delivery-target', row)?.value || null,
                    delivery_address: $('.js-delivery-address', row)?.value || '',
                    order_no: $('.js-order-no', row)?.value || '',
                    note: $('.js-note', row)?.value || ''
                };
            }

            function setButtonLoading(btn, text) {
                if (!btn) return () => { };
                const old = btn.textContent;
                btn.disabled = true;
                btn.textContent = text || 'Speichert...';
                return function () {
                    btn.disabled = false;
                    btn.textContent = old;
                };
            }

            function statusLabel(status) {
                const labels = { lager: 'Im Lager', bestellen: 'Bestellen', teilweise: 'Teilweise', unbekannt: 'Unbekannt' };
                return labels[status] || status || 'Unbekannt';
            }

            function updateRowSummaries(row) {
                const locationTitle = $('.js-location-summary-title', row);
                const locationSub = $('.js-location-summary-sub', row);
                const orderTitle = $('.js-order-summary-title', row);
                const orderSub = $('.js-order-summary-sub', row);
                const noteSummary = $('.js-note-summary', row);

                const locationLabel = $('.js-location-label', row)?.value || 'Kein Lagerort';
                const room = $('.js-room-name', row)?.value || $('.js-room-number', row)?.value || '—';
                const rack = $('.js-rack-name', row)?.value || '—';
                const shelf = $('.js-shelf', row)?.value || '—';

                if (locationTitle) locationTitle.textContent = locationLabel;
                if (locationSub) locationSub.textContent = `Raum ${room} · Regal ${rack} · Fach ${shelf}`;

                const sourceType = $('.js-source-type', row)?.value || 'manual';
                const manual = $('.js-manual-source', row)?.value || '';
                const distributor = $('.js-distributor-id option:checked', row)?.textContent?.trim() || '';
                const brand = $('.js-brand-id option:checked', row)?.textContent?.trim() || '';
                const sourceName = sourceType === 'distributor' ? distributor : (sourceType === 'brand' ? brand : manual);
                const orderQty = parseFloat($('.js-order-qty', row)?.value || '0') || 0;
                const orderStatus = $('.js-order-status', row)?.value || 'open';
                const delivery = $('.js-expected-delivery', row)?.value || '—';

                if (orderTitle) orderTitle.textContent = sourceName || 'Keine Bestellung';
                if (orderSub) orderSub.textContent = `Menge ${orderQty.toFixed(2)} · Status ${orderStatus} · Lieferung ${delivery}`;

                const note = $('.js-note', row)?.value || '';
                if (noteSummary) noteSummary.textContent = note ? (note.length > 72 ? note.slice(0, 72) + '…' : note) : 'Keine Notiz';
            }

            function updateStatusPill(row, status) {
                const pill = $('.ml-pill.lager, .ml-pill.bestellen, .ml-pill.teilweise, .ml-pill.unbekannt', row);
                if (!pill) return;
                pill.classList.remove('lager', 'bestellen', 'teilweise', 'unbekannt');
                pill.classList.add(status);
                pill.textContent = statusLabel(status);
            }

            function recalcRow(row) {
                const required = parseFloat($('.js-required-qty', row)?.value || '0') || 0;
                const found = parseFloat($('.js-found-qty', row)?.value || '0') || 0;
                const orderQty = Math.max(required - found, 0);
                const orderInput = $('.js-order-qty', row);
                const statusSelect = $('.js-stock-status', row);
                let newStatus = 'unbekannt';

                if (orderInput) orderInput.value = orderQty.toFixed(2);

                if (required > 0 && found >= required) {
                    newStatus = 'lager';
                } else if (found > 0 && orderQty > 0) {
                    newStatus = 'teilweise';
                } else if (orderQty > 0) {
                    newStatus = 'bestellen';
                }

                if (statusSelect) statusSelect.value = newStatus;
                row.dataset.status = newStatus;
                updateStatusPill(row, newStatus);
                updateRowSummaries(row);
                filterRows();
            }

            function filterRows() {
                const q = ($('#mlSearch')?.value || '').toLowerCase().trim();
                const status = $('#mlStatusFilter')?.value || '';
                const change = $('#mlChangeFilter')?.value || '';
                const tab = $('.ml-pane-tab.active')?.dataset.localTab || 'all';
                const table = $('#mlMaterialTable');
                if (table) {
                    table.classList.toggle('is-lager-tab', tab === 'lager');
                    table.classList.toggle('is-order-tab', tab === 'order');
                }

                $$('.mat-row').forEach(row => {
                    let visible = true;

                    if (q && !(row.dataset.search || '').includes(q)) visible = false;
                    if (status && row.dataset.status !== status) visible = false;
                    if (change && row.dataset.change !== change) visible = false;

                    if (tab === 'lager' && !['lager', 'teilweise'].includes(row.dataset.status)) visible = false;
                    if (tab === 'order' && !['bestellen', 'teilweise'].includes(row.dataset.status)) visible = false;
                    if (tab === 'unknown' && row.dataset.status !== 'unbekannt') visible = false;

                    row.style.display = visible ? '' : 'none';
                });
            }

            function historyLabel(type) {
                const labels = {
                    lager_check: 'Lagerprüfung',
                    material_move: 'Materialbewegung',
                    order_details_update: 'Bestelldetails',
                    material_update: 'Materialänderung'
                };

                return labels[type] || type || 'Änderung';
            }

            function renderHistory(row) {
                const list = $('#mlHistoryList');
                const title = $('#mlHistoryTitle');
                if (!list || !title) return;

                title.textContent = 'Materialhistorie · ' + ($('.ml-article-title', row)?.textContent || row.dataset.itemKey);

                let history = [];
                try {
                    history = JSON.parse(row.dataset.history || '[]');
                } catch (e) {
                    history = [];
                }

                if (!Array.isArray(history) || !history.length) {
                    list.innerHTML = '<div style="padding:24px;text-align:center;color:#64748b;font-weight:900;">Keine Historie vorhanden.</div>';
                    return;
                }

                list.innerHTML = history.slice().reverse().map(entry => {
                    const by = entry.changed_by || entry.checked_by || entry.updated_by_data || {};
                    const name = by.name || entry.updated_by_name || 'Unbekannt';
                    const date = entry.created_at || entry.updated_at || entry.checked_at || '';
                    const reason = entry.reason || entry.note || '';
                    const qty = entry.qty ?? entry.order_details?.order_qty ?? '';
                    const status = entry.status || entry.order_details?.order_status || '';
                    const source = entry.order_details?.source_name || entry.order_details?.manual_source_name || '';

                    return `
                    <div class="ml-history-item">
                        <div class="ml-history-head">
                            <span>${historyLabel(entry.type)}</span>
                            <span>${date}</span>
                        </div>
                        <div class="ml-history-meta">
                            Mitarbeiter: ${name}
                            ${qty !== '' ? '\nMenge: ' + qty : ''}
                            ${status ? '\nStatus: ' + status : ''}
                            ${source ? '\nQuelle: ' + source : ''}
                            ${reason ? '\nNotiz: ' + reason : ''}
                        </div>
                    </div>
                `;
                }).join('');
            }


            function updateToggleButtonText(row, panel) {
                const isOpen = panel === 'order'
                    ? row.classList.contains('is-order-open')
                    : (panel === 'note' ? row.classList.contains('is-note-open') : row.classList.contains('is-lager-open'));

                $$('.js-toggle-panel[data-panel-target="' + panel + '"]', row).forEach(btn => {
                    if (panel === 'order') btn.textContent = isOpen ? 'Bestellung schließen' : 'Bestellung öffnen';
                    else if (panel === 'note') btn.textContent = isOpen ? 'Notiz schließen' : 'Notiz öffnen';
                    else btn.textContent = isOpen ? 'Lager schließen' : 'Lager öffnen';
                });
            }

            document.addEventListener('input', function (event) {
                if (event.target.matches('.js-required-qty, .js-found-qty')) {
                    const row = event.target.closest('.mat-row');
                    if (row) recalcRow(row);
                }

                if (event.target.matches('.js-location-label, .js-room-name, .js-room-number, .js-rack-name, .js-row, .js-column, .js-shelf, .js-order-qty, .js-manual-source, .js-order-no, .js-expected-delivery, .js-delivery-address, .js-note')) {
                    const row = event.target.closest('.mat-row');
                    if (row) updateRowSummaries(row);
                }

                if (event.target.matches('#mlSearch')) {
                    filterRows();
                }
            });

            document.addEventListener('change', function (event) {
                if (event.target.matches('.js-stock-status')) {
                    const row = event.target.closest('.mat-row');
                    if (row) {
                        row.dataset.status = event.target.value || 'unbekannt';
                        updateStatusPill(row, row.dataset.status);
                        filterRows();
                    }
                }

                if (event.target.matches('.js-source-type, .js-distributor-id, .js-brand-id, .js-order-status, .js-ordered-by, .js-ordered-at, .js-delivery-target')) {
                    const row = event.target.closest('.mat-row');
                    if (row) updateRowSummaries(row);
                }

                if (event.target.matches('#mlStatusFilter, #mlChangeFilter')) {
                    filterRows();
                }
            });

            document.addEventListener('click', async function (event) {
                const tab = event.target.closest('.ml-pane-tab');
                if (tab) {
                    $$('.ml-pane-tab').forEach(btn => btn.classList.toggle('active', btn === tab));
                    filterRows();
                    return;
                }

                const row = event.target.closest('.mat-row');

                if (!event.target.closest('.ml-action-menu') && !event.target.closest('.ml-top-menu')) {
                    $$('.ml-action-menu[open], .ml-top-menu[open]').forEach(menu => menu.removeAttribute('open'));
                }

                const togglePanel = event.target.closest('.js-toggle-panel');
                if (togglePanel && row) {
                    const panel = togglePanel.dataset.panelTarget;
                    const className = panel === 'order' ? 'is-order-open' : (panel === 'note' ? 'is-note-open' : 'is-lager-open');
                    row.classList.toggle(className);
                    row.classList.toggle('is-row-open', row.classList.contains('is-lager-open') || row.classList.contains('is-order-open') || row.classList.contains('is-note-open'));
                    updateToggleButtonText(row, panel);
                    return;
                }

                const toggleAll = event.target.closest('.js-toggle-all');
                if (toggleAll) {
                    const panel = toggleAll.dataset.panelTarget;
                    const className = panel === 'order' ? 'is-order-open' : 'is-lager-open';
                    const visibleRows = $$('.mat-row').filter(r => r.style.display !== 'none');
                    const shouldOpen = visibleRows.some(r => !r.classList.contains(className));
                    visibleRows.forEach(r => {
                        r.classList.toggle(className, shouldOpen);
                        r.classList.toggle('is-row-open', r.classList.contains('is-lager-open') || r.classList.contains('is-order-open') || r.classList.contains('is-note-open'));
                    });
                    return;
                }

                if (event.target.closest('.js-auto-lager') && row) {
                    const required = parseFloat($('.js-required-qty', row)?.value || '0') || 0;
                    $('.js-found-qty', row).value = required.toFixed(2);
                    recalcRow(row);
                    return;
                }

                if (event.target.closest('.js-auto-order') && row) {
                    $('.js-found-qty', row).value = '0.00';
                    recalcRow(row);
                    return;
                }

                const saveStock = event.target.closest('.js-save-stock');
                if (saveStock && row) {
                    const restore = setButtonLoading(saveStock, 'Speichert...');
                    try {
                        const data = await postJson(routes.update, stockPayload(row));
                        toast('success', 'Gespeichert', data.message || 'Lagerdaten gespeichert.');
                        setTimeout(() => window.location.reload(), 650);
                    } catch (error) {
                        toast('error', 'Fehler', error.message);
                    } finally {
                        restore();
                    }
                    return;
                }

                const saveOrder = event.target.closest('.js-save-order');
                if (saveOrder && row) {
                    const restore = setButtonLoading(saveOrder, 'Speichert...');
                    try {
                        const data = await postJson(routes.order, orderPayload(row));
                        toast('success', 'Gespeichert', data.message || 'Bestelldetails gespeichert.');
                        setTimeout(() => window.location.reload(), 650);
                    } catch (error) {
                        toast('error', 'Fehler', error.message);
                    } finally {
                        restore();
                    }
                    return;
                }

                if (event.target.closest('.js-history') && row) {
                    renderHistory(row);
                    $('#mlHistoryModal')?.classList.add('show');
                    return;
                }

                if (event.target.closest('.js-close-modal') || event.target.classList.contains('ml-modal-backdrop')) {
                    $$('.ml-modal-backdrop').forEach(modal => modal.classList.remove('show'));
                    return;
                }

                const printButton = event.target.closest('.js-print-mode');
                if (printButton) {
                    document.documentElement.dataset.materialPrintMode = printButton.dataset.printMode || 'normal';
                    window.print();
                    return;
                }

                const applyBtn = event.target.closest('.js-apply-feinaufmass');
                if (applyBtn) {
                    if (!routes.apply) {
                        toast('error', 'Route fehlt', 'Route deal.material.apply-feinaufmass ist nicht registriert.');
                        return;
                    }

                    if (!confirm('Freigegebene Feinaufmaß-Materialien wirklich in das Auftragsdokument übernehmen?')) {
                        return;
                    }

                    const restore = setButtonLoading(applyBtn, 'Übernehme...');
                    try {
                        const data = await postJson(routes.apply, {});
                        toast('success', 'Übernommen', data.message || 'Feinaufmaß wurde übernommen.');
                        setTimeout(() => window.location.reload(), 900);
                    } catch (error) {
                        toast('error', 'Fehler', error.message);
                    } finally {
                        restore();
                    }
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    $$('.ml-modal-backdrop').forEach(modal => modal.classList.remove('show'));
                }
            });

            document.documentElement.dataset.materialPrintMode = 'normal';
            $$('.mat-row').forEach(row => { updateRowSummaries(row); ['lager','order','note'].forEach(panel => updateToggleButtonText(row, panel)); });
            filterRows();
        })();
    </script>
@endpush