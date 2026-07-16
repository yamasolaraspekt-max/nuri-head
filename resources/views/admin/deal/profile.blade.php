@extends('admin.layouts.app')

@section('title', 'Auftrag Profil')

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        :root {
            --dp-text: #111827;
            --dp-muted: #6b7280;
            --dp-line: #e5e7eb;
            --dp-soft: #f8fafc;
            --dp-card: #ffffff;
            --dp-primary: var(--sa-accent);
            --dp-primary-dark: var(--sa-accent-hover);
            --dp-success: #10b981;
            --dp-warning: #f59e0b;
            --dp-danger: #ef4444;
            --dp-radius: 18px;
            --dp-shadow: 0 12px 32px rgba(15, 23, 42, .08);
        }

        .dp-wrap {
            padding: 18px;
            color: var(--dp-text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .dp-header {
            background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
            border: 1px solid var(--dp-line);
            border-radius: 24px;
            box-shadow: var(--dp-shadow);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .dp-title {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -.03em;
        }

        .dp-sub {
            margin-top: 8px;
            color: var(--dp-muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .dp-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 0 14px;
            border: 0;
            border-radius: 12px;
            background: var(--dp-primary);
            color: #fff !important;
            font-weight: 900;
            text-decoration: none !important;
            cursor: pointer;
            transition: .2s;
        }

        .dp-btn:hover {
            background: var(--dp-primary-dark);
            color: #fff !important;
        }

        .dp-btn.soft {
            background: #fff;
            color: var(--dp-text) !important;
            border: 1px solid var(--dp-line);
        }

        .dp-btn.danger {
            background: var(--dp-danger);
        }

        .dp-grid {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            gap: 18px;
            margin-top: 18px;
        }

        .dp-card {
            background: var(--dp-card);
            border: 1px solid var(--dp-line);
            border-radius: var(--dp-radius);
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            overflow: hidden;
        }

        .dp-card+.dp-card {
            margin-top: 18px;
        }

        .dp-card-head {
            padding: 14px 16px;
            background: #fafafa;
            border-bottom: 1px solid var(--dp-line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .dp-card-title {
            margin: 0;
            font-size: 15px;
            font-weight: 900;
        }

        .dp-card-body {
            padding: 16px;
        }

        .dp-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .dp-info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px dashed var(--dp-line);
            padding-bottom: 10px;
        }

        .dp-info-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .dp-info-key {
            color: var(--dp-muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dp-info-val {
            text-align: right;
            font-size: 13px;
            font-weight: 800;
            word-break: break-word;
        }

        .dp-banner {
            margin-top: 14px;
            border: 1px solid #d9ef9d;
            background: linear-gradient(135deg, #f4fae7, #fff);
            border-radius: 18px;
            padding: 14px 16px;
            display: grid;
            grid-template-columns: 1.3fr repeat(3, minmax(120px, .4fr));
            gap: 14px;
            align-items: center;
        }

        .dp-banner-title {
            font-weight: 900;
            font-size: 14px;
        }

        .dp-banner-text {
            color: #55720d;
            font-size: 12px;
            line-height: 1.6;
            margin-top: 4px;
        }

        .dp-banner-metric {
            background: #fff;
            border: 1px solid #e5f3c3;
            border-radius: 14px;
            padding: 10px 12px;
        }

        .dp-banner-label {
            font-size: 10px;
            font-weight: 900;
            color: var(--dp-muted);
            text-transform: uppercase;
        }

        .dp-banner-value {
            font-size: 18px;
            font-weight: 900;
            margin-top: 4px;
        }

        .dp-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .dp-stat {
            background: #fff;
            border: 1px solid var(--dp-line);
            border-radius: 16px;
            padding: 14px;
        }

        .dp-stat-label {
            color: var(--dp-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dp-stat-value {
            margin-top: 6px;
            font-size: 22px;
            font-weight: 900;
        }

        .dp-stepper {
            display: flex;
            overflow-x: auto;
            gap: 0;
            padding-bottom: 8px;
        }

        .dp-step {
            min-width: 150px;
            padding: 12px 16px;
            border: 0;
            clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%, 12px 50%);
            background: #d9eab5;
            color: #3f4f18;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
        }

        .dp-step:first-child {
            clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%);
        }

        .dp-step.current {
            background: var(--dp-primary);
            color: #fff;
        }

        .dp-step.past {
            background: #74b2d4;
            color: #fff;
        }

        .dp-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding: 10px 12px 0;
            border-bottom: 1px solid var(--dp-line);
        }

        .dp-tab {
            border: 1px solid transparent;
            background: transparent;
            padding: 10px 12px;
            border-radius: 12px 12px 0 0;
            color: var(--dp-muted);
            font-weight: 900;
            cursor: pointer;
        }

        .dp-tab.active {
            background: var(--dp-soft);
            border-color: var(--dp-line);
            border-bottom-color: var(--dp-soft);
            color: var(--dp-text);
        }

        .dp-tab-count {
            margin-left: 6px;
            min-width: 20px;
            height: 20px;
            border-radius: 999px;
            background: #edf8d2;
            color: #55720d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            padding: 0 6px;
        }

        .dp-panel {
            display: none;
            padding: 16px;
        }

        .dp-panel.active {
            display: block;
        }

        .dp-table-wrap {
            overflow: auto;
            border: 1px solid var(--dp-line);
            border-radius: 16px;
        }

        .dp-table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse;
        }

        .dp-table th,
        .dp-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            font-size: 12px;
            vertical-align: top;
        }

        .dp-table th {
            background: #f8fafc;
            color: var(--dp-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dp-material-main {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .dp-material-img {
            width: 46px;
            height: 46px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--dp-line);
            background: #f3f4f6;
            flex: 0 0 auto;
        }

        .dp-material-placeholder {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            border: 1px dashed var(--dp-line);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            flex: 0 0 auto;
        }

        .dp-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 11px;
            font-weight: 900;
            border: 1px solid #e5e7eb;
        }

        .dp-pill.success {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .dp-pill.warning {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }

        .dp-pill.danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .dp-progress {
            height: 9px;
            border-radius: 999px;
            overflow: hidden;
            background: #e5e7eb;
        }

        .dp-progress span {
            display: block;
            height: 100%;
            background: var(--dp-primary);
        }

        .dp-empty {
            padding: 32px;
            border: 1px dashed var(--dp-line);
            border-radius: 16px;
            text-align: center;
            color: var(--dp-muted);
            background: #fff;
        }

        .dp-report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .dp-report-item {
            border: 1px solid var(--dp-line);
            border-radius: 16px;
            background: #fff;
            padding: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .dp-report-dot {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #9ca3af;
            flex: 0 0 auto;
        }

        .dp-report-item.done .dp-report-dot {
            background: #ecfdf5;
            color: #047857;
        }

        .dp-file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 12px;
        }

        .dp-file {
            border: 1px solid var(--dp-line);
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
        }

        .dp-file-preview {
            height: 130px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
        }

        .dp-file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dp-file-preview i {
            font-size: 34px;
            color: #9ca3af;
        }

        .dp-file-body {
            padding: 10px;
        }

        .dp-file-title {
            font-weight: 900;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dp-file-actions {
            display: flex;
            gap: 6px;
            margin-top: 10px;
        }

        .dp-file-actions .dp-btn {
            min-height: 32px;
            padding: 0 10px;
            font-size: 11px;
        }

        .dp-note {
            border: 1px solid var(--dp-line);
            border-radius: 14px;
            padding: 12px;
            background: #fff;
            margin-bottom: 10px;
        }

        .dp-note-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dp-note-author {
            font-weight: 900;
        }

        .dp-note-date {
            color: var(--dp-muted);
            font-size: 12px;
        }

        .dp-form-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: start;
        }

        .dp-textarea {
            width: 100%;
            border: 1px solid var(--dp-line);
            border-radius: 14px;
            padding: 12px;
            min-height: 88px;
            outline: none;
            resize: vertical;
        }

        .dp-textarea:focus {
            border-color: var(--dp-primary);
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
        }

        .dp-input,
        .dp-select {
            width: 100%;
            height: 42px;
            border: 1px solid var(--dp-line);
            border-radius: 12px;
            padding: 0 12px;
            outline: none;
            background: #fff;
        }

        .dp-input:focus,
        .dp-select:focus {
            border-color: var(--dp-primary);
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
        }

        .dp-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .dp-modal-backdrop.show {
            display: flex;
        }

        .dp-modal {
            width: min(860px, 100%);
            max-height: 90vh;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .22);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .dp-modal-head {
            padding: 16px 18px;
            border-bottom: 1px solid var(--dp-line);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            background: #fafafa;
        }

        .dp-modal-body {
            padding: 18px;
            overflow: auto;
        }

        .dp-modal-image {
            max-width: 100%;
            max-height: 65vh;
            display: block;
            margin: auto;
            border-radius: 14px;
        }

        .dp-assignment-card {
            border: 1px solid #d9ef9d;
            background: linear-gradient(135deg, #ffffff, #fbfff3);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .dp-assignment-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .dp-assignment-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #111827;
        }

        .dp-assignment-sub {
            color: var(--dp-muted);
            font-size: 12px;
            line-height: 1.6;
            margin-top: 4px;
        }

        .dp-assignment-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .dp-assignment-box {
            background: #fff;
            border: 1px solid var(--dp-line);
            border-radius: 14px;
            padding: 12px;
        }

        .dp-assignment-label {
            font-size: 10px;
            font-weight: 900;
            color: var(--dp-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dp-assignment-value {
            font-size: 13px;
            font-weight: 900;
            color: #111827;
            margin-top: 6px;
            line-height: 1.45;
        }

        .dp-assignment-desc {
            background: #fff;
            border: 1px dashed var(--dp-line);
            border-radius: 14px;
            padding: 12px;
            color: #374151;
            font-size: 13px;
            line-height: 1.65;
            white-space: pre-wrap;
            margin-bottom: 14px;
        }

        .dp-assignment-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .dp-form-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .dp-form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .dp-label {
            display: block;
            font-size: 11px;
            font-weight: 900;
            color: var(--dp-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }

        .dp-checkline {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
        }

        .dp-measurement-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }


        /* Beautiful Select2 employee picker */
        .dp-employee-select-wrap {
            position: relative;
        }

        .dp-employee-select-wrap .select2-container {
            width: 100% !important;
        }

        .dp-employee-select-wrap .select2-container--default .select2-selection--single {
            min-height: 58px !important;
            height: 58px !important;
            border: 1px solid #dbe3ef !important;
            border-radius: 18px !important;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            display: flex !important;
            align-items: center !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06) !important;
            transition: all .18s ease !important;
            overflow: hidden !important;
        }

        .dp-employee-select-wrap .select2-container--default.select2-container--open .select2-selection--single,
        .dp-employee-select-wrap .select2-container--default .select2-selection--single:focus {
            border-color: var(--dp-primary) !important;
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .15), 0 12px 28px rgba(15, 23, 42, .08) !important;
        }

        .dp-employee-select-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
            width: 100% !important;
            height: 100% !important;
            line-height: normal !important;
            padding: 8px 48px 8px 10px !important;
            display: flex !important;
            align-items: center !important;
        }

        .dp-employee-select-wrap .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
            font-size: 13px !important;
            font-weight: 800 !important;
        }

        .dp-employee-select-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 58px !important;
            width: 42px !important;
            right: 8px !important;
            top: 0 !important;
        }

        .dp-employee-select-wrap .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent !important;
            border-width: 6px 5px 0 5px !important;
            margin-left: -5px !important;
            margin-top: -2px !important;
        }

        .dp-employee-select-wrap .select2-container--default .select2-selection__clear {
            position: absolute !important;
            right: 38px !important;
            top: 17px !important;
            width: 22px !important;
            height: 22px !important;
            line-height: 20px !important;
            border-radius: 999px !important;
            background: #f1f5f9 !important;
            color: #64748b !important;
            text-align: center !important;
            font-size: 16px !important;
            z-index: 2 !important;
        }

        .select2-container--open {
            z-index: 10050 !important;
        }

        .select2-dropdown.dp-employee-dropdown {
            border: 1px solid #dbe3ef !important;
            border-radius: 18px !important;
            overflow: hidden !important;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .18) !important;
            background: #fff !important;
            padding: 8px !important;
        }

        .dp-employee-dropdown .select2-search--dropdown {
            padding: 8px !important;
        }

        .dp-employee-dropdown .select2-search__field {
            height: 44px !important;
            border: 1px solid #dbe3ef !important;
            border-radius: 14px !important;
            outline: none !important;
            padding: 0 14px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            background: #f8fafc !important;
        }

        .dp-employee-dropdown .select2-search__field:focus {
            border-color: var(--dp-primary) !important;
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .14) !important;
            background: #fff !important;
        }

        .dp-employee-dropdown .select2-results__options {
            max-height: 280px !important;
            padding: 4px !important;
        }

        .dp-employee-dropdown .select2-results__option {
            border-radius: 16px !important;
            padding: 8px !important;
            margin: 4px 0 !important;
            color: #111827 !important;
            transition: all .15s ease !important;
        }

        .dp-employee-dropdown .select2-results__option--highlighted[aria-selected],
        .dp-employee-dropdown .select2-results__option--selected {
            background: linear-gradient(135deg, #f4fae7, #eef8d5) !important;
            color: #111827 !important;
        }

        .dp-employee-option,
        .dp-employee-selection {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            width: 100%;
        }

        .dp-employee-option-avatar-wrap,
        .dp-employee-selection-avatar-wrap {
            position: relative;
            width: 42px;
            height: 42px;
            flex: 0 0 auto;
        }

        .dp-employee-option img,
        .dp-employee-selection img,
        .dp-assignment-avatar {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid #fff;
            background: #f3f4f6;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .12);
            flex: 0 0 auto;
        }

        .dp-employee-selection img {
            width: 38px;
            height: 38px;
            border-radius: 14px;
        }

        .dp-employee-option-status-dot,
        .dp-employee-selection-status-dot {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: #22c55e;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(34, 197, 94, .35);
        }

        .dp-employee-option-main,
        .dp-employee-selection-main {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
            flex: 1;
        }

        .dp-employee-option-name,
        .dp-employee-selection-name {
            color: #111827;
            font-size: 14px;
            font-weight: 950;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -.01em;
        }

        .dp-employee-option-meta,
        .dp-employee-selection-meta {
            display: inline-flex;
            align-items: center;
            width: max-content;
            gap: 5px;
            color: #15803d;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 2px 7px;
        }

        .dp-employee-option-meta:before,
        .dp-employee-selection-meta:before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: #22c55e;
        }

        .dp-responsible-inline {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }



        /* Toast + custom alert/confirm UI */
        .dp-toast-stack {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 12000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: min(380px, calc(100vw - 36px));
            pointer-events: none;
        }

        .dp-toast {
            pointer-events: auto;
            background: #fff;
            border: 1px solid var(--dp-line);
            border-left: 5px solid var(--dp-primary);
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, .18);
            padding: 13px 14px;
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr) 24px;
            gap: 10px;
            align-items: flex-start;
            animation: dpToastIn .22s ease-out;
        }

        .dp-toast.is-leaving {
            animation: dpToastOut .2s ease-in forwards;
        }

        .dp-toast-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #edf8d2;
            color: #55720d;
            font-weight: 900;
        }

        .dp-toast-title {
            font-size: 13px;
            font-weight: 950;
            color: #111827;
            line-height: 1.3;
        }

        .dp-toast-message {
            font-size: 12px;
            color: #64748b;
            line-height: 1.55;
            margin-top: 3px;
            white-space: pre-wrap;
        }

        .dp-toast-close {
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            padding: 0;
        }

        .dp-toast.success {
            border-left-color: var(--dp-success);
        }

        .dp-toast.success .dp-toast-icon {
            background: #ecfdf5;
            color: #047857;
        }

        .dp-toast.warning {
            border-left-color: var(--dp-warning);
        }

        .dp-toast.warning .dp-toast-icon {
            background: #fffbeb;
            color: #b45309;
        }

        .dp-toast.error {
            border-left-color: var(--dp-danger);
        }

        .dp-toast.error .dp-toast-icon {
            background: #fef2f2;
            color: #b91c1c;
        }

        .dp-toast.info {
            border-left-color: #3b82f6;
        }

        .dp-toast.info .dp-toast-icon {
            background: #eff6ff;
            color: #1d4ed8;
        }

        @keyframes dpToastIn {
            from {
                opacity: 0;
                transform: translateY(-8px) translateX(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0) translateX(0);
            }
        }

        @keyframes dpToastOut {
            from {
                opacity: 1;
                transform: translateY(0) translateX(0);
            }

            to {
                opacity: 0;
                transform: translateY(-6px) translateX(16px);
            }
        }

        .dp-alert-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .56);
            z-index: 11900;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .dp-alert-backdrop.show {
            display: flex;
        }

        .dp-alert-box {
            width: min(460px, 100%);
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 30px 90px rgba(15, 23, 42, .28);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .45);
            animation: dpAlertIn .18s ease-out;
        }

        .dp-alert-body {
            padding: 22px;
            text-align: center;
        }

        .dp-alert-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #edf8d2;
            color: #55720d;
            font-size: 22px;
        }

        .dp-alert-box.error .dp-alert-icon {
            background: #fef2f2;
            color: #b91c1c;
        }

        .dp-alert-box.warning .dp-alert-icon {
            background: #fffbeb;
            color: #b45309;
        }

        .dp-alert-box.success .dp-alert-icon {
            background: #ecfdf5;
            color: #047857;
        }

        .dp-alert-box.info .dp-alert-icon {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .dp-alert-title {
            font-size: 18px;
            font-weight: 950;
            color: #111827;
            letter-spacing: -.02em;
        }

        .dp-alert-message {
            margin-top: 8px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.65;
            white-space: pre-wrap;
        }

        .dp-alert-actions {
            padding: 0 22px 22px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dp-alert-btn {
            min-height: 42px;
            border: 0;
            border-radius: 14px;
            padding: 0 16px;
            font-weight: 950;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .dp-alert-btn.primary {
            background: var(--dp-primary);
            color: #fff;
        }

        .dp-alert-btn.primary:hover {
            background: var(--dp-primary-dark);
        }

        .dp-alert-btn.soft {
            background: #fff;
            color: #111827;
            border: 1px solid var(--dp-line);
        }

        .dp-alert-btn.danger {
            background: var(--dp-danger);
            color: #fff;
        }

        @keyframes dpAlertIn {
            from {
                opacity: 0;
                transform: scale(.96) translateY(8px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }



        /* Feinaufmaß link cards for task/calendar integration */
        .dp-measurement-link-box {
            border: 1px solid #bfdbfe;
            background: linear-gradient(135deg, #eff6ff, #fff);
            border-radius: 18px;
            padding: 14px;
            margin: 0 0 14px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dp-measurement-link-title {
            font-size: 14px;
            font-weight: 950;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dp-measurement-link-text {
            color: #475569;
            font-size: 12px;
            line-height: 1.55;
            margin-top: 4px;
        }

        .dp-mini-code {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #dbeafe;
            background: #fff;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 950;
            white-space: nowrap;
        }

        .dp-link-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dp-link-health-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin:12px 0 14px;}
        .dp-link-health-card{border:1px solid var(--dp-line);background:#fff;border-radius:16px;padding:13px;display:flex;flex-direction:column;gap:9px;}
        .dp-link-health-card.active{border-color:#a7f3d0;background:linear-gradient(135deg,#ecfdf5,#fff);}
        .dp-link-health-card.deleted{border-color:#fecaca;background:linear-gradient(135deg,#fef2f2,#fff);}
        .dp-link-health-card.missing{border-color:#fde68a;background:linear-gradient(135deg,#fffbeb,#fff);}
        .dp-link-health-top{display:flex;align-items:center;justify-content:space-between;gap:10px;}
        .dp-link-health-title{font-size:13px;font-weight:950;color:#111827;display:flex;align-items:center;gap:7px;}
        .dp-link-health-meta{font-size:12px;color:#475569;line-height:1.55;}
        .dp-link-health-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:2px;}
        .dp-link-health-btn{min-height:30px;border-radius:10px;border:1px solid var(--dp-line);background:#fff;color:#334155;font-size:11px;font-weight:950;padding:0 10px;display:inline-flex;align-items:center;gap:6px;text-decoration:none!important;cursor:pointer;}
        .dp-link-health-btn:hover{border-color:#93c21c;color:#55720d;}
        .dp-link-health-btn.danger{background:#fef2f2;color:#b91c1c;border-color:#fecaca;}
        .dp-link-health-btn.warning{background:#fffbeb;color:#b45309;border-color:#fde68a;}
        #dpMeasurementAssignmentModal{z-index:10020;}
        #dpPreviewModal{z-index:10030;}
        .select2-container--open{z-index:10050!important;}

        @media(max-width:1100px){.dp-grid{grid-template-columns:1fr;}.dp-stats{grid-template-columns:repeat(2,minmax(0,1fr));}.dp-banner{grid-template-columns:1fr 1fr;}.dp-assignment-summary,.dp-form-grid-4{grid-template-columns:repeat(2,minmax(0,1fr));}}
        @media(max-width:700px){.dp-stats,.dp-banner,.dp-form-row,.dp-assignment-summary,.dp-form-grid-4,.dp-form-grid-2{grid-template-columns:1fr;}}
        </style>
@endsection

@section('content')
    @php
        /*
         * Safe defaults for profile.blade.php.
         * Some controllers open this Blade without passing $currentStatus or workflow maps.
         * Define them here before any usage to avoid PHP1412 / undefined variable errors.
         */
        $workflowStages = collect($workflowStages ?? $dealWorkflowStages ?? []);
        $workflowLabelMap = $workflowLabelMap ?? [];
        $workflowColorMap = $workflowColorMap ?? [];

        if (empty($workflowLabelMap) && $workflowStages->isNotEmpty()) {
            $workflowLabelMap = $workflowStages
                ->mapWithKeys(fn($stage) => [
                    (string) ($stage->key ?? $stage['key'] ?? '') => (string) ($stage->label ?? $stage->name ?? $stage['label'] ?? $stage['name'] ?? $stage->key ?? $stage['key'] ?? 'Status')
                ])
                ->filter(fn($label, $key) => $key !== '')
                ->all();
        }

        if (empty($workflowColorMap) && $workflowStages->isNotEmpty()) {
            $workflowColorMap = $workflowStages
                ->mapWithKeys(fn($stage) => [
                    (string) ($stage->key ?? $stage['key'] ?? '') => (string) ($stage->color ?? $stage['color'] ?? '#93c21c')
                ])
                ->filter(fn($color, $key) => $key !== '')
                ->all();
        }

        $firstWorkflowStage = $workflowStages->first();
        $fallbackStatus = $firstWorkflowStage
            ? (string) ($firstWorkflowStage->key ?? $firstWorkflowStage['key'] ?? 'open')
            : 'open';

        $currentStatus = (string) ($currentStatus
            ?? $deal->status
            ?? $deal->project_status
            ?? $deal->deal_status
            ?? $fallbackStatus);

        $customerName = trim(($dealRow->firma ?? '') . ' ' . ($dealRow->name ?? '') . ' ' . ($dealRow->lastname ?? '')) ?: 'Unbekannter Kunde';
        $auftragNo = $deal->order_number ?? $deal->deal_no ?? $deal->offer_number ?? ('#' . $deal->id);
        $statusLabel = $workflowLabelMap[$currentStatus] ?? $currentStatus;
        $statusColor = $workflowColorMap[$currentStatus] ?? '#93c21c';
        $measurementStatus = $measurement?->status ?? 'Nicht erstellt';
        $invoiceOpen = collect($invoices ?? [])->sum(fn($invoice) => (float) ($invoice->open_amount ?? 0));
        $mainEmployee = $employeeMap[(string) ($deal->employee_id ?? '')]['name'] ?? trim(($dealRow->emp_name ?? '') . ' ' . ($dealRow->emp_lastname ?? '')) ?: 'Nicht zugewiesen';

        $measurementId = $measurement?->id;
        $measurementNo = $measurement?->measurement_no ?? ($measurementId ? ('#' . $measurementId) : '–');
        $measurementAppointmentId = $measurement?->appointment_id;
        $measurementTaskId = $measurement?->personal_task_id;

        /*
         * Live link status check for Feinaufmaß → Termin/Aufgabe.
         * We use withTrashed() so the profile can show when the saved link points
         * to a soft-deleted appointment/task. This is the exact problem case: the
         * old ID exists in deal_measurements, but the target row is deleted, so the
         * next save must recreate it instead of trying to update the deleted row.
         */
        $measurementAppointmentRecord = null;
        $measurementTaskRecord = null;

        if ($measurementId && class_exists(\App\Models\MainAppointment::class)) {
            $measurementAppointmentQuery = \App\Models\MainAppointment::withTrashed();
            $measurementAppointmentQuery->where(function ($query) use ($measurementId, $measurementAppointmentId) {
                if ($measurementAppointmentId) {
                    $query->where('id', $measurementAppointmentId);
                } else {
                    $query->whereRaw('1 = 0');
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('main_appointments', 'deal_measurement_id')) {
                    $query->orWhere('deal_measurement_id', $measurementId);
                }

                $query->orWhere(function ($fallback) use ($measurementId) {
                    $fallback->where('source', 'deal_measurement')
                        ->where('other_id', $measurementId);
                });
            });

            $measurementAppointmentRecord = $measurementAppointmentQuery->latest('id')->first();
        }

        if ($measurementId && class_exists(\App\Models\PersonalTask::class)) {
            $measurementTaskQuery = \App\Models\PersonalTask::withTrashed();
            $measurementTaskQuery->where(function ($query) use ($measurementId, $measurementTaskId) {
                if ($measurementTaskId) {
                    $query->where('id', $measurementTaskId);
                } else {
                    $query->whereRaw('1 = 0');
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('personal_tasks', 'deal_measurement_id')) {
                    $query->orWhere('deal_measurement_id', $measurementId);
                }
            });

            $measurementTaskRecord = $measurementTaskQuery->latest('id')->first();
        }

        $appointmentIsSoftDeleted = $measurementAppointmentRecord && method_exists($measurementAppointmentRecord, 'trashed') && $measurementAppointmentRecord->trashed();
        $taskIsSoftDeleted = $measurementTaskRecord && method_exists($measurementTaskRecord, 'trashed') && $measurementTaskRecord->trashed();

        $appointmentHealthClass = $measurementAppointmentRecord ? ($appointmentIsSoftDeleted ? 'deleted' : 'active') : 'missing';
        $taskHealthClass = $measurementTaskRecord ? ($taskIsSoftDeleted ? 'deleted' : 'active') : 'missing';

        $appointmentHealthLabel = $measurementAppointmentRecord
            ? ($appointmentIsSoftDeleted ? 'Gelöscht / neu erstellen nötig' : 'Aktiv verknüpft')
            : 'Nicht verknüpft';

        $taskHealthLabel = $measurementTaskRecord
            ? ($taskIsSoftDeleted ? 'Gelöscht / neu erstellen nötig' : 'Aktiv verknüpft')
            : 'Nicht verknüpft';

        $appointmentProfileUrl = $measurementAppointmentRecord && !$appointmentIsSoftDeleted ? url('/customer/appointments/' . $measurementAppointmentRecord->id) : null;
        $taskProfileUrl = $measurementTaskRecord && !$taskIsSoftDeleted ? url('/personal-tasks/' . $measurementTaskRecord->id . '/profile') : null;

    @endphp

    <div class="dp-wrap" data-deal-id="{{ $deal->id }}" data-measurement-id="{{ $measurementId ?? '' }}">
        <div class="dp-header">
            <div>
                <h1 class="dp-title">Auftrag {{ $auftragNo }}</h1>
                <div class="dp-sub">
                    {{ $customerName }} · {{ $dealRow->article_group ?? 'Produkt' }} · {{ $dealRow->city ?? 'Ort unbekannt' }}<br>
                    Status: <strong id="dpCurrentStatusLabel" style="color:{{ $statusColor }}">{{ $statusLabel }}</strong> · Zuständig: <strong>{{ $mainEmployee }}</strong>
                </div>
            </div>
            <div class="dp-actions">
                <a href="{{ route('deal.details') }}" class="dp-btn soft"><i class="fa fa-arrow-left"></i> Zurück</a>
                <a href="{{ url('new_lead_profile/' . $deal->customer_id) }}" class="dp-btn soft"><i class="fa fa-user"></i> Kundenprofil</a>
                <button type="button" class="dp-btn" id="dpOpenStatusModal"><i class="fa fa-random"></i> Status ändern</button>
                @if($measurement)
                    <a href="{{ route('deal.measurements.show', $measurement) }}" class="dp-btn"><i class="fa fa-ruler"></i> Feinaufmaß öffnen</a>
                @else
                    <button type="button" class="dp-btn" data-create-measurement><i class="fa fa-ruler"></i> Feinaufmaß erstellen & planen</button>
                @endif
            </div>
        </div>

        <div class="dp-banner">
            <div>
                <div class="dp-banner-title">Auftrag Informationsbanner</div>
                <div class="dp-banner-text">Alle Informationen zu Status, Material, Feinaufmaß, Dokumenten und Notizen sind hier zusammengeführt. Änderungen werden ohne Seiten-Neuladen aktualisiert.</div>
            </div>
            <div class="dp-banner-metric"><div class="dp-banner-label">Dokumente</div><div class="dp-banner-value" data-document-count>{{ $documentCount }}</div></div>
            <div class="dp-banner-metric"><div class="dp-banner-label">Notizen</div><div class="dp-banner-value" data-note-count>{{ $noteCount }}</div></div>
            <div class="dp-banner-metric"><div class="dp-banner-label">Feinaufmaß</div><div class="dp-banner-value">{{ $measurementReport['percent'] ?? 0 }}%</div></div>
        </div>

        <div class="dp-grid">
            <aside>
                <div class="dp-card">
                    <div class="dp-card-head"><h3 class="dp-card-title">Kunde & Objekt</h3></div>
                    <div class="dp-card-body"><div class="dp-info">
                        <div class="dp-info-row"><span class="dp-info-key">Kunde</span><span class="dp-info-val">{{ $customerName }}</span></div>
                        <div class="dp-info-row"><span class="dp-info-key">Kundennr.</span><span class="dp-info-val">{{ $dealRow->customer_no ?? '–' }}</span></div>
                        <div class="dp-info-row"><span class="dp-info-key">Kontakt</span><span class="dp-info-val">{{ $dealRow->email ?? '–' }}<br>{{ $dealRow->phone ?? $dealRow->telephone ?? '–' }}</span></div>
                        <div class="dp-info-row"><span class="dp-info-key">Objekt</span><span class="dp-info-val">{{ $dealRow->object_name ?? '–' }}<br>{{ $dealRow->full_address ?? trim(($dealRow->street ?? '') . ' ' . ($dealRow->postcode ?? '') . ' ' . ($dealRow->city ?? '')) }}</span></div>
                    </div></div>
                </div>
                <div class="dp-card">
                    <div class="dp-card-head"><h3 class="dp-card-title">Auftrag Details</h3></div>
                    <div class="dp-card-body"><div class="dp-info">
                        <div class="dp-info-row"><span class="dp-info-key">Produkt</span><span class="dp-info-val">{{ $dealRow->article_group ?? '–' }}</span></div>
                        <div class="dp-info-row"><span class="dp-info-key">Service</span><span class="dp-info-val">{{ $deal->service ?? '–' }}</span></div>
                        <div class="dp-info-row"><span class="dp-info-key">Mitarbeiter</span><span class="dp-info-val">{{ $mainEmployee }}</span></div>
                        <div class="dp-info-row"><span class="dp-info-key">Angebot</span><span class="dp-info-val">{{ $deal->offer_number ?? $deal->offer_id ?? '–' }}</span></div>
                        <div class="dp-info-row"><span class="dp-info-key">Preis</span><span class="dp-info-val">{{ number_format((float) ($deal->price ?? 0), 2, ',', '.') }} €</span></div>
                    </div></div>
                </div>
            </aside>

            <main>
                <div class="dp-stats">
                    <div class="dp-stat"><div class="dp-stat-label">Status</div><div class="dp-stat-value" id="dpStatusStat" style="color:{{ $statusColor }}">{{ $statusLabel }}</div></div>
                    <div class="dp-stat"><div class="dp-stat-label">Material</div><div class="dp-stat-value">{{ $materialStats['approved'] }}/{{ $materialStats['total'] }}</div></div>
                    <div class="dp-stat"><div class="dp-stat-label">Feinaufmaß</div><div class="dp-stat-value">{{ $measurementReport['done'] ?? 0 }}/{{ $measurementReport['total'] ?? 0 }}</div></div>
                    <div class="dp-stat"><div class="dp-stat-label">Offene Rechnung</div><div class="dp-stat-value">{{ number_format($invoiceOpen, 2, ',', '.') }} €</div></div>
                </div>

                <div class="dp-card">
                    <div class="dp-card-head"><h3 class="dp-card-title">Auftrag Status</h3><span class="dp-pill" id="dpStatusPill" style="border-color:{{ $statusColor }};color:{{ $statusColor }}">{{ $statusLabel }}</span></div>
                    <div class="dp-card-body"><div class="dp-stepper">
                        @php
                            $currentIndex = collect($workflowStages)->search(fn($s) => (string) ($s->key ?? $s['key'] ?? '') === (string) $currentStatus);
                            $currentIndex = $currentIndex === false ? 0 : (int) $currentIndex;
                        @endphp
                        @forelse($workflowStages as $index => $stage)
                            @php
                                $stageKey = (string) ($stage->key ?? $stage['key'] ?? '');
                                $stageLabel = $stage->label ?? $stage->name ?? $stage['label'] ?? $stage['name'] ?? $stageKey;
                                $cls = $index < $currentIndex ? 'past' : ($index === $currentIndex ? 'current' : '');
                            @endphp
                            <div class="dp-step {{ $cls }}">{{ $stageLabel }}</div>
                        @empty
                            <div class="dp-empty">Keine Auftrag-Unterphasen konfiguriert.</div>
                        @endforelse
                    </div></div>
                </div>

                <div class="dp-card" style="margin-top:18px;">
                    <div class="dp-tabs">
                        <button type="button" class="dp-tab active" data-dp-tab="materials">Materialliste</button>
                        <button type="button" class="dp-tab" data-dp-tab="measurement">Feinaufmaß</button>
                        <button type="button" class="dp-tab" data-dp-tab="invoices">Rechnungen</button>
                        <button type="button" class="dp-tab" data-dp-tab="files">Dokumente <span class="dp-tab-count" data-document-count>{{ $documentCount }}</span></button>
                        <button type="button" class="dp-tab" data-dp-tab="notes">Notizen <span class="dp-tab-count" data-note-count>{{ $noteCount }}</span></button>
                    </div>

                    <div class="dp-panel active" data-dp-panel="materials">
                        <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                            <div><strong>{{ $materialSource }}</strong><div class="dp-sub" style="margin-top:2px;">Plan: {{ number_format($materialStats['plan'], 2, ',', '.') }} · Ist: {{ number_format($materialStats['verbrauch'], 2, ',', '.') }} · Diff: {{ number_format($materialStats['delta'], 2, ',', '.') }}</div></div>
                            <div style="min-width:220px;"><div class="dp-progress"><span style="width:{{ $materialStats['percent'] }}%"></span></div><div class="dp-sub" style="margin-top:5px;">{{ $materialStats['percent'] }}% erledigt</div></div>
                        </div>
                        @if(!empty($materialSections))
                            <div class="dp-table-wrap"><table class="dp-table"><thead><tr><th>Bereich / Ort</th><th>Material</th><th>Artikel</th><th>Plan</th><th>Ist</th><th>Diff</th><th>Status</th></tr></thead><tbody>
                            @foreach($materialSections as $section)
                                @foreach(($section['items'] ?? []) as $item)
                                    @php $plan = (float) ($item['plan_qty'] ?? $item['qty'] ?? 0);
                                        $ist = (float) ($item['verbrauch_qty'] ?? 0);
                                    $diff = $ist - $plan; @endphp
                                    <tr>
                                        <td><strong>{{ $section['title'] ?? 'Material' }}</strong><div class="dp-sub">Ort: {{ $item['location'] ?? '–' }}</div></td>
                                        <td><div class="dp-material-main">@if(!empty($item['image_url']))<img class="dp-material-img" src="{{ $item['image_url'] }}" alt="">@else<div class="dp-material-placeholder"><i class="fa fa-image"></i></div>@endif<div><strong>{{ $item['name'] ?? 'Unbenannt' }}</strong>@if(!empty($item['description']))<div class="dp-sub">{{ $item['description'] }}</div>@endif</div></div></td>
                                        <td>{{ $item['article_no'] ?? '–' }}</td><td>{{ number_format($plan, 2, ',', '.') }} {{ $item['unit'] ?? '' }}</td><td>{{ number_format($ist, 2, ',', '.') }} {{ $item['unit'] ?? '' }}</td><td>{{ number_format($diff, 2, ',', '.') }}</td>
                                        <td>@if(!empty($item['approved']))<span class="dp-pill success">Freigegeben</span>@else<span class="dp-pill warning">Offen</span>@endif</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody></table></div>
                        @else <div class="dp-empty">Keine Materialliste gefunden.</div> @endif
                    </div>

                    <div class="dp-panel" data-dp-panel="measurement">
                        @php
                            $employeeCollection = collect($employees ?? \App\Models\Employee::query()->where('status', 'Active')->orderBy('name')->orderBy('lastname')->get(['id', 'name', 'lastname', 'image', 'status']));
                            $responsibleEmployee = $measurement && $measurement->responsible_employee_id
                                ? $employeeCollection->firstWhere('id', (int) $measurement->responsible_employee_id)
                                : null;

                            $responsibleName = $responsibleEmployee
                                ? trim(($responsibleEmployee->name ?? '') . ' ' . ($responsibleEmployee->lastname ?? ''))
                                : 'Noch nicht zugewiesen';

                            $responsibleImageRaw = $responsibleEmployee->image ?? null;
                            $responsibleImage = $responsibleImageRaw
                                ? (str_starts_with($responsibleImageRaw, 'http://') || str_starts_with($responsibleImageRaw, 'https://') || str_starts_with($responsibleImageRaw, 'data:')
                                    ? $responsibleImageRaw
                                    : asset('images/employee/' . ltrim($responsibleImageRaw, '/')))
                                : asset('images/icons/placeholder.svg');

                            $scheduledStartDate = $measurement?->scheduled_start_date ?? null;
                            $scheduledEndDate = $measurement?->scheduled_end_date ?? null;

                            $scheduledStartDateFormatted = $scheduledStartDate ? \Carbon\Carbon::parse($scheduledStartDate)->format('d.m.Y') : null;
                            $scheduledEndDateFormatted = $scheduledEndDate ? \Carbon\Carbon::parse($scheduledEndDate)->format('d.m.Y') : null;
                            $scheduledStartDateInput = $scheduledStartDate ? \Carbon\Carbon::parse($scheduledStartDate)->format('Y-m-d') : '';
                            $scheduledEndDateInput = $scheduledEndDate ? \Carbon\Carbon::parse($scheduledEndDate)->format('Y-m-d') : '';

                            $scheduledStartTime = $measurement?->scheduled_start_time ? substr((string) $measurement->scheduled_start_time, 0, 5) : '';
                            $scheduledEndTime = $measurement?->scheduled_end_time ? substr((string) $measurement->scheduled_end_time, 0, 5) : '';

                            $assignmentStatus = $measurement?->assignment_status ?? 'not_assigned';
                            $assignmentStatusLabel = match ($assignmentStatus) {
                                'task_created' => 'Termin & Aufgabe erstellt',
                                'appointment_created' => 'Termin erstellt',
                                'assigned' => 'Zugewiesen',
                                'completed' => 'Abgeschlossen',
                                'canceled' => 'Abgebrochen',
                                default => 'Noch nicht geplant',
                            };
                            $assignmentStatusClass = in_array($assignmentStatus, ['task_created', 'appointment_created', 'assigned', 'completed'], true) ? 'success' : 'warning';
                        @endphp

                        <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
                            <div>
                                <strong>Feinaufmaß Report</strong>
                                <div class="dp-sub">Zeigt Verantwortliche, Termin, Aufgabe und den Fortschritt des Feinaufmaßes.</div>
                            </div>
                            <div style="min-width:260px;">
                                <div class="dp-progress"><span style="width:{{ $measurementReport['percent'] ?? 0 }}%"></span></div>
                                <div class="dp-sub" style="margin-top:5px;">{{ $measurementReport['done'] ?? 0 }}/{{ $measurementReport['total'] ?? 0 }} erledigt · {{ $measurementReport['percent'] ?? 0 }}%</div>
                            </div>
                        </div>

                        @if($measurement)
                            <div class="dp-measurement-link-box">
                                <div>
                                    <div class="dp-measurement-link-title"><i class="fa fa-link"></i> Feinaufmaß-Verknüpfung für Termin & Aufgabe</div>
                                    <div class="dp-measurement-link-text">
                                        Hier siehst du live, ob dieses Feinaufmaß wirklich mit einem aktiven Kalendertermin und einer persönlichen Aufgabe verbunden ist.
                                        Wenn der alte Termin oder die alte Aufgabe per Soft Delete gelöscht wurde, zeigt das Profil dies als <strong>Gelöscht / neu erstellen nötig</strong>.
                                    </div>
                                </div>
                                <div class="dp-link-actions">
                                    <span class="dp-mini-code">Feinaufmaß {{ $measurementNo }}</span>
                                    <span class="dp-mini-code">ID {{ $measurementId }}</span>
                                </div>
                            </div>

                            <div class="dp-link-health-grid" id="dpMeasurementLinkHealth">
                                <div class="dp-link-health-card {{ $appointmentHealthClass }}" id="dpAppointmentHealthCard">
                                    <div class="dp-link-health-top">
                                        <div class="dp-link-health-title"><i class="fa fa-calendar"></i> Kalendertermin</div>
                                        <span class="dp-pill {{ $appointmentHealthClass === 'active' ? 'success' : ($appointmentHealthClass === 'deleted' ? 'danger' : 'warning') }}" id="dpAppointmentHealthLabel">{{ $appointmentHealthLabel }}</span>
                                    </div>
                                    <div class="dp-link-health-meta" id="dpAppointmentHealthMeta">
                                        @if($measurementAppointmentRecord)
                                            ID: #{{ $measurementAppointmentRecord->id }}<br>
                                            Datum: {{ $measurementAppointmentRecord->start_date ? \Carbon\Carbon::parse($measurementAppointmentRecord->start_date)->format('d.m.Y') : '–' }}
                                            {{ $measurementAppointmentRecord->start_time ? ' · ' . substr((string) $measurementAppointmentRecord->start_time, 0, 5) : '' }}
                                            {{ $measurementAppointmentRecord->end_time ? ' - ' . substr((string) $measurementAppointmentRecord->end_time, 0, 5) : '' }}<br>
                                            deal_measurement_id: {{ $measurementAppointmentRecord->deal_measurement_id ?? '–' }} · source/other_id: {{ $measurementAppointmentRecord->source ?? '–' }}/{{ $measurementAppointmentRecord->other_id ?? '–' }}
                                            @if($appointmentIsSoftDeleted)
                                                <br><strong>Soft deleted:</strong> {{ optional($measurementAppointmentRecord->deleted_at)->format('d.m.Y H:i') }}
                                            @endif
                                        @else
                                            Kein aktiver oder gelöschter Termin für dieses Feinaufmaß gefunden. Beim nächsten Speichern soll ein neuer Termin erstellt werden.
                                        @endif
                                    </div>
                                    <div class="dp-link-health-actions">
                                        @if($appointmentProfileUrl)
                                            <a href="{{ $appointmentProfileUrl }}" class="dp-link-health-btn" target="_blank"><i class="fa fa-external-link"></i> Termin öffnen</a>
                                        @endif
                                        <button type="button" class="dp-link-health-btn warning" data-force-new-appointment><i class="fa fa-plus"></i> Beim Speichern neuen Termin erstellen</button>
                                        <button type="button" class="dp-link-health-btn danger" data-unlink-appointment><i class="fa fa-unlink"></i> Verknüpfung lösen</button>
                                    </div>
                                </div>

                                <div class="dp-link-health-card {{ $taskHealthClass }}" id="dpTaskHealthCard">
                                    <div class="dp-link-health-top">
                                        <div class="dp-link-health-title"><i class="fa fa-check-square-o"></i> Persönliche Aufgabe</div>
                                        <span class="dp-pill {{ $taskHealthClass === 'active' ? 'success' : ($taskHealthClass === 'deleted' ? 'danger' : 'warning') }}" id="dpTaskHealthLabel">{{ $taskHealthLabel }}</span>
                                    </div>
                                    <div class="dp-link-health-meta" id="dpTaskHealthMeta">
                                        @if($measurementTaskRecord)
                                            ID: #{{ $measurementTaskRecord->id }}<br>
                                            Titel: {{ $measurementTaskRecord->task_title ?? '–' }}<br>
                                            Fällig: {{ $measurementTaskRecord->due_date ? \Carbon\Carbon::parse($measurementTaskRecord->due_date)->format('d.m.Y') : '–' }} {{ $measurementTaskRecord->due_time ? ' · ' . substr((string) $measurementTaskRecord->due_time, 0, 5) : '' }}<br>
                                            deal_measurement_id: {{ $measurementTaskRecord->deal_measurement_id ?? '–' }}
                                            @if($taskIsSoftDeleted)
                                                <br><strong>Soft deleted:</strong> {{ optional($measurementTaskRecord->deleted_at)->format('d.m.Y H:i') }}
                                            @endif
                                        @else
                                            Keine aktive oder gelöschte Aufgabe für dieses Feinaufmaß gefunden. Wenn „Zusätzlich als persönliche Aufgabe erstellen“ aktiv ist, wird beim Speichern eine neue Aufgabe erstellt.
                                        @endif
                                    </div>
                                    <div class="dp-link-health-actions">
                                        @if($taskProfileUrl)
                                            <a href="{{ $taskProfileUrl }}" class="dp-link-health-btn" target="_blank"><i class="fa fa-external-link"></i> Aufgabe öffnen</a>
                                        @endif
                                        <button type="button" class="dp-link-health-btn warning" data-force-new-task><i class="fa fa-plus"></i> Beim Speichern neue Aufgabe erstellen</button>
                                        <button type="button" class="dp-link-health-btn danger" data-unlink-task><i class="fa fa-unlink"></i> Verknüpfung lösen</button>
                                    </div>
                                </div>
                            </div>

                            <div class="dp-assignment-card" id="dpMeasurementAssignmentCard">
                                <div class="dp-assignment-head">
                                    <div>
                                        <h3 class="dp-assignment-title"><i class="fa fa-calendar-check-o"></i> Feinaufmaß Termin & Aufgabe</h3>
                                        <div class="dp-assignment-sub">Plane das Feinaufmaß direkt als Kalendertermin und erstelle optional eine persönliche Aufgabe für den zuständigen Mitarbeiter.</div>
                                    </div>
                                    <span class="dp-pill {{ $assignmentStatusClass }}" id="dpAssignmentStatusPill">{{ $assignmentStatusLabel }}</span>
                                </div>

                                <div class="dp-assignment-summary">
                                    <div class="dp-assignment-box">
                                        <div class="dp-assignment-label">Verantwortlich</div>
                                        <div class="dp-assignment-value" id="dpAssignmentResponsible"><span class="dp-responsible-inline"><img class="dp-assignment-avatar" src="{{ $responsibleImage }}" alt=""><span>{{ $responsibleName }}</span></span></div>
                                    </div>
                                    <div class="dp-assignment-box">
                                        <div class="dp-assignment-label">Datum</div>
                                        <div class="dp-assignment-value" id="dpAssignmentDate">
                                            @if($scheduledStartDateFormatted)
                                                {{ $scheduledStartDateFormatted }}
                                                @if($scheduledEndDateFormatted && $scheduledEndDateFormatted !== $scheduledStartDateFormatted)
                                                    - {{ $scheduledEndDateFormatted }}
                                                @endif
                                            @else
                                                Kein Datum gesetzt
                                            @endif
                                        </div>
                                    </div>
                                    <div class="dp-assignment-box">
                                        <div class="dp-assignment-label">Zeit</div>
                                        <div class="dp-assignment-value" id="dpAssignmentTime">{{ $scheduledStartTime ?: '–' }}{{ $scheduledEndTime ? ' - ' . $scheduledEndTime : '' }}</div>
                                    </div>
                                    <div class="dp-assignment-box">
                                        <div class="dp-assignment-label">Verknüpfung</div>
                                        <div class="dp-assignment-value" id="dpAssignmentLinks">
                                            Feinaufmaß: {{ $measurementNo }}<br>
                                            Termin: {{ $measurementAppointmentId ? '#' . $measurementAppointmentId : '–' }}<br>
                                            Aufgabe: {{ $measurementTaskId ? '#' . $measurementTaskId : '–' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="dp-assignment-desc" id="dpAssignmentDescriptionView">{{ $measurement->assignment_description ?: 'Noch keine Arbeitsbeschreibung hinterlegt.' }}</div>

                                <div class="dp-actions" style="justify-content:flex-end;">
                                    <button type="button" class="dp-btn" id="dpOpenMeasurementAssignmentModal"><i class="fa fa-calendar-plus-o"></i> Mitarbeiter, Termin & Aufgabe planen</button>
                                    <a href="{{ route('deal.measurements.show', $measurement) }}" class="dp-btn soft"><i class="fa fa-ruler"></i> Feinaufmaß öffnen</a>
                                </div>
                            </div>

                            <div class="dp-modal-backdrop" id="dpMeasurementAssignmentModal">
                                <div class="dp-modal">
                                    <div class="dp-modal-head">
                                        <strong>Feinaufmaß planen</strong>
                                        <button type="button" class="dp-btn soft" data-close-dp-modal>Schließen</button>
                                    </div>
                                    <div class="dp-modal-body">
                                        <form id="dpMeasurementAssignmentForm" class="dp-assignment-form">
                                    @csrf
                                    <input type="hidden" name="deal_measurement_id" value="{{ $measurementId }}">
                                    <input type="hidden" name="measurement_id" value="{{ $measurementId }}">
                                    <input type="hidden" name="deal_id" value="{{ $deal->id }}">
                                    <input type="hidden" name="customer_id" value="{{ $deal->customer_id }}">
                                    <input type="hidden" name="alternative_id" value="{{ $deal->alternative_id }}">
                                    <input type="hidden" name="product_id" value="{{ $deal->product_id }}">
                                    <input type="hidden" name="existing_appointment_id" value="{{ $measurementAppointmentRecord?->id }}">
                                    <input type="hidden" name="existing_task_id" value="{{ $measurementTaskRecord?->id }}">
                                    <input type="hidden" name="appointment_is_soft_deleted" value="{{ $appointmentIsSoftDeleted ? 1 : 0 }}">
                                    <input type="hidden" name="task_is_soft_deleted" value="{{ $taskIsSoftDeleted ? 1 : 0 }}">
                                    <input type="hidden" name="force_new_appointment" id="dpForceNewAppointment" value="{{ $appointmentIsSoftDeleted ? 1 : 0 }}">
                                    <input type="hidden" name="force_new_task" id="dpForceNewTask" value="{{ $taskIsSoftDeleted ? 1 : 0 }}">
                                    <input type="hidden" name="unlink_appointment" id="dpUnlinkAppointment" value="0">
                                    <input type="hidden" name="unlink_task" id="dpUnlinkTask" value="0">
                                    <div class="dp-form-grid-4">
                                        <div class="dp-employee-select-wrap">
                                            <label class="dp-label">Mitarbeiter / Verantwortlich</label>
                                            <select name="employee_id" class="dp-select dp-employee-select" required data-placeholder="Aktiven Mitarbeiter wählen">
                                                <option value="">Bitte wählen</option>
                                                @foreach($employeeCollection as $employee)
                                                    @php
                                                        $employeeName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: ('Mitarbeiter #' . $employee->id);
                                                        $employeeImageRaw = $employee->image ?? null;
                                                        $employeeImage = $employeeImageRaw
                                                            ? (str_starts_with($employeeImageRaw, 'http://') || str_starts_with($employeeImageRaw, 'https://') || str_starts_with($employeeImageRaw, 'data:')
                                                                ? $employeeImageRaw
                                                                : asset('images/employee/' . ltrim($employeeImageRaw, '/')))
                                                            : asset('images/icons/placeholder.svg');
                                                    @endphp
                                                    <option
                                                        value="{{ $employee->id }}"
                                                        data-image="{{ $employeeImage }}"
                                                        data-name="{{ $employeeName }}"
                                                        data-status="Active"
                                                        @selected((int) ($measurement->responsible_employee_id ?? 0) === (int) $employee->id)
                                                    >{{ $employeeName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="dp-label">Startdatum</label>
                                            <input type="date" name="start_date" class="dp-input" value="{{ $scheduledStartDateInput }}" required>
                                        </div>
                                        <div>
                                            <label class="dp-label">Startzeit</label>
                                            <input type="time" name="start_time" class="dp-input" value="{{ $scheduledStartTime }}" required>
                                        </div>
                                        <div>
                                            <label class="dp-label">Endzeit</label>
                                            <input type="time" name="end_time" class="dp-input" value="{{ $scheduledEndTime }}" required>
                                        </div>
                                    </div>

                                    <div class="dp-form-grid-2">
                                        <div>
                                            <label class="dp-label">Enddatum</label>
                                            <input type="date" name="end_date" class="dp-input" value="{{ $scheduledEndDateInput }}">
                                        </div>
                                        <div>
                                            <label class="dp-label">Priorität</label>
                                            <select name="task_priority" class="dp-select">
                                                <option value="normal">Normal</option>
                                                <option value="high">Hoch</option>
                                                <option value="urgent">Dringend</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="dp-label">Beschreibung / Arbeitsanweisung</label>
                                        <textarea name="description" class="dp-textarea" placeholder="Was soll beim Feinaufmaß geprüft oder erledigt werden?">{{ $measurement->assignment_description }}</textarea>
                                    </div>

                                    <label class="dp-checkline">
                                        <input type="checkbox" name="create_task" value="1" @checked($measurementTaskRecord && !$taskIsSoftDeleted)>
                                        <span>Zusätzlich als persönliche Aufgabe erstellen</span>
                                    </label>

                                    <div class="dp-measurement-link-text" style="margin-top:-4px;">
                                        Beim Speichern wird <strong>deal_measurement_id = {{ $measurementId }}</strong> an den Controller gesendet. Der Controller muss diese ID in <code>personal_tasks.deal_measurement_id</code> und <code>main_appointments.deal_measurement_id</code> speichern.
                                    </div>

                                            <div class="dp-actions" style="justify-content:flex-end;">
                                                <button type="button" class="dp-btn soft" data-close-dp-modal>Abbrechen</button>
                                                <button type="submit" class="dp-btn"><i class="fa fa-save"></i> Termin / Aufgabe speichern</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="dp-empty">
                                Für diesen Auftrag wurde noch kein Feinaufmaß erstellt. Erstelle zuerst das Feinaufmaß, danach öffnet sich automatisch das Planungsfenster.
                                <button type="button" class="dp-btn" data-create-measurement style="margin-top:12px;">Feinaufmaß erstellen & Termin planen</button>
                            </div>
                        @endif

                        <div class="dp-report-grid">
                            @foreach(($measurementReport['items'] ?? []) as $reportItem)
                                <div class="dp-report-item {{ !empty($reportItem['done']) ? 'done' : '' }}">
                                    <div class="dp-report-dot"><i class="fa {{ !empty($reportItem['done']) ? 'fa-check' : 'fa-clock-o' }}"></i></div>
                                    <div><strong>{{ $reportItem['label'] }}</strong><div class="dp-sub">{{ $reportItem['detail'] }}</div></div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="dp-panel" data-dp-panel="invoices">
                        @if($invoices->count())<div class="dp-table-wrap"><table class="dp-table"><thead><tr><th>Rechnung</th><th>Typ</th><th>Betrag</th><th>Bezahlt</th><th>Offen</th><th>Status</th><th>Fällig</th></tr></thead><tbody>@foreach($invoices as $invoice)<tr><td>{{ $invoice->invoice_number }}</td><td>{{ $invoice->invoice_type }}</td><td>{{ number_format((float) $invoice->invoice_amount, 2, ',', '.') }} €</td><td>{{ number_format((float) $invoice->paid_amount, 2, ',', '.') }} €</td><td>{{ number_format((float) $invoice->open_amount, 2, ',', '.') }} €</td><td><span class="dp-pill">{{ $invoice->status }}</span></td><td>{{ $invoice->due_date ?? '–' }}</td></tr>@endforeach</tbody></table></div>@else<div class="dp-empty">Keine Rechnungen vorhanden.</div>@endif
                    </div>

                    <div class="dp-panel" data-dp-panel="files">
                        <form id="dpDocumentUploadForm" enctype="multipart/form-data" style="margin-bottom:14px;"><div class="dp-form-row"><input type="file" name="document" class="dp-input" required><button type="submit" class="dp-btn"><i class="fa fa-upload"></i> Hochladen</button></div></form>
                        <div class="dp-file-grid" id="dpDocumentGrid">
                            @foreach($documents as $doc)
                                @php
                                    $documentSource = $doc['source'] ?? 'image';
                                    $deleteUrl = route('deal.profile.documents.destroy', [
                                        'deal' => $deal->id,
                                        'source' => $documentSource,
                                        'id' => $doc['id'],
                                    ]);
                                @endphp
                                <div class="dp-file" data-document-card data-delete-url="{{ $deleteUrl }}"><div class="dp-file-preview" data-preview-url="{{ $doc['preview_url'] }}" data-preview-title="{{ $doc['title'] }}" data-is-image="{{ $doc['is_image'] ? '1' : '0' }}">@if($doc['is_image'])<img src="{{ $doc['preview_url'] }}" alt="">@else<i class="fa fa-file-o"></i>@endif</div><div class="dp-file-body"><div class="dp-file-title">{{ $doc['title'] }}</div><div class="dp-sub">{{ $doc['stage'] ?? 'Dokument' }} · {{ $doc['created_at'] ?? '–' }}</div><div class="dp-file-actions"><button type="button" class="dp-btn soft" data-preview-url="{{ $doc['preview_url'] }}" data-preview-title="{{ $doc['title'] }}" data-is-image="{{ $doc['is_image'] ? '1' : '0' }}">Ansehen</button><button type="button" class="dp-btn danger" data-delete-document>Löschen</button></div></div></div>
                            @endforeach
                        </div>
                        @if($documents->isEmpty())<div class="dp-empty" id="dpDocumentEmpty">Keine Dokumente oder Bilder vorhanden.</div>@endif
                    </div>

                    <div class="dp-panel" data-dp-panel="notes">
                        <form id="dpNoteForm" style="margin-bottom:14px;"><div class="dp-form-row"><textarea name="description" class="dp-textarea" placeholder="Neue Notiz schreiben..." required></textarea><button type="submit" class="dp-btn"><i class="fa fa-save"></i> Notiz speichern</button></div></form>
                        <div id="dpNotesList">
                            @forelse($notes as $note)
                                <div class="dp-note"><div class="dp-note-head"><span class="dp-note-author">{{ $employeeMap[(string) ($note->created_by ?? '')]['name'] ?? ('Mitarbeiter #' . ($note->created_by ?? '')) }}</span><span class="dp-note-date">{{ optional($note->created_at)->format('d.m.Y H:i') }}</span></div><div style="margin-top:8px;white-space:pre-wrap;">{{ $note->description }}</div></div>
                            @empty <div class="dp-empty" id="dpNotesEmpty">Keine Notizen vorhanden.</div> @endforelse
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <div class="dp-toast-stack" id="dpToastStack" aria-live="polite" aria-atomic="true"></div>
    <div class="dp-alert-backdrop" id="dpCustomAlert" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="dp-alert-box info" id="dpCustomAlertBox"><div class="dp-alert-body"><div class="dp-alert-icon" id="dpCustomAlertIcon"><i class="fa fa-info"></i></div><div class="dp-alert-title" id="dpCustomAlertTitle">Hinweis</div><div class="dp-alert-message" id="dpCustomAlertMessage"></div></div><div class="dp-alert-actions" id="dpCustomAlertActions"></div></div>
    </div>

    <div class="dp-modal-backdrop" id="dpPreviewModal"><div class="dp-modal"><div class="dp-modal-head"><strong id="dpPreviewTitle">Vorschau</strong><button type="button" class="dp-btn soft" data-close-dp-modal>Schließen</button></div><div class="dp-modal-body" id="dpPreviewBody"></div></div></div>
    <div class="dp-modal-backdrop" id="dpStatusModal"><div class="dp-modal"><div class="dp-modal-head"><strong>Status ändern</strong><button type="button" class="dp-btn soft" data-close-dp-modal>Schließen</button></div><div class="dp-modal-body"><form id="dpStatusForm"><label class="dp-info-key">Neue Unterphase</label><select name="status" class="dp-select" required>@foreach($workflowStages as $stage)
        @php
            $stageKey = (string) ($stage->key ?? $stage['key'] ?? '');
            $stageLabel = $stage->label ?? $stage->name ?? $stage['label'] ?? $stage['name'] ?? $stageKey;
        @endphp
        <option value="{{ $stageKey }}" @selected($stageKey === (string) $currentStatus)>{{ $stageLabel }}</option>
    @endforeach</select><div style="height:12px"></div><label class="dp-info-key">Begründung</label><textarea name="reason" class="dp-textarea" placeholder="Warum wird der Auftragstatus geändert?" required></textarea><div style="margin-top:12px;display:flex;justify-content:flex-end;gap:8px;"><button type="button" class="dp-btn soft" data-close-dp-modal>Abbrechen</button><button type="submit" class="dp-btn">Status speichern</button></div></form></div></div></div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    (function(){
        'use strict';
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const routes = {
            noteStore: @json(route('deal.profile.notes.store', $deal)),
            uploadDocument: @json(route('deal.profile.documents.upload', $deal)),
            updateStatus: @json(route('deal.profile.status.update', $deal)),
            createMeasurement: @json(route('deal.measurements.store-from-deal', $deal)),
            assignMeasurement: @json($measurement ? route('deal.measurements.assign-work', $measurement) : null),
        };
        function esc(v){return String(v ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
        function dpIcon(type){return type==='success'?'fa-check':(type==='error'?'fa-times':(type==='warning'?'fa-exclamation-triangle':'fa-info'));}
        function dpToast(type='info', title='Hinweis', message='', duration=4200){
            const stack=document.getElementById('dpToastStack'); if(!stack) return;
            const toast=document.createElement('div'); toast.className=`dp-toast ${type}`;
            toast.innerHTML=`<div class="dp-toast-icon"><i class="fa ${dpIcon(type)}"></i></div><div><div class="dp-toast-title">${esc(title)}</div>${message?`<div class="dp-toast-message">${esc(message)}</div>`:''}</div><button type="button" class="dp-toast-close" aria-label="Schließen">&times;</button>`;
            const close=()=>{toast.classList.add('is-leaving');setTimeout(()=>toast.remove(),220);}; toast.querySelector('.dp-toast-close')?.addEventListener('click',close); stack.appendChild(toast); if(duration>0) setTimeout(close,duration);
        }
        function dpAlert(title='Hinweis', message='', type='info', buttonText='OK'){
            return new Promise(resolve=>{const b=document.getElementById('dpCustomAlert'),box=document.getElementById('dpCustomAlertBox'),ic=document.getElementById('dpCustomAlertIcon'),t=document.getElementById('dpCustomAlertTitle'),m=document.getElementById('dpCustomAlertMessage'),a=document.getElementById('dpCustomAlertActions'); if(!b||!box||!a){dpToast(type,title,message);resolve(true);return;} box.className=`dp-alert-box ${type}`; ic.innerHTML=`<i class="fa ${dpIcon(type)}"></i>`; t.textContent=title; m.textContent=message||''; a.innerHTML=`<button type="button" class="dp-alert-btn primary" data-alert-ok>${esc(buttonText)}</button>`; const close=()=>{b.classList.remove('show');b.setAttribute('aria-hidden','true');resolve(true);}; a.querySelector('[data-alert-ok]')?.addEventListener('click',close,{once:true}); b.classList.add('show'); b.setAttribute('aria-hidden','false'); setTimeout(()=>a.querySelector('[data-alert-ok]')?.focus(),50);});
        }
        function dpConfirm(title='Bestätigung', message='', options={}){
            return new Promise(resolve=>{const b=document.getElementById('dpCustomAlert'),box=document.getElementById('dpCustomAlertBox'),ic=document.getElementById('dpCustomAlertIcon'),t=document.getElementById('dpCustomAlertTitle'),m=document.getElementById('dpCustomAlertMessage'),a=document.getElementById('dpCustomAlertActions'); if(!b||!box||!a){dpAlert(title,message,'warning').then(()=>resolve(false));return;} const type=options.type||'warning'; box.className=`dp-alert-box ${type}`; ic.innerHTML=`<i class="fa ${dpIcon(type)}"></i>`; t.textContent=title; m.textContent=message||''; a.innerHTML=`<button type="button" class="dp-alert-btn soft" data-alert-cancel>${esc(options.cancelText||'Abbrechen')}</button><button type="button" class="dp-alert-btn ${type==='error'||type==='warning'?'danger':'primary'}" data-alert-confirm>${esc(options.confirmText||'Bestätigen')}</button>`; const done=v=>{b.classList.remove('show');b.setAttribute('aria-hidden','true');resolve(v);}; a.querySelector('[data-alert-cancel]')?.addEventListener('click',()=>done(false),{once:true}); a.querySelector('[data-alert-confirm]')?.addEventListener('click',()=>done(true),{once:true}); b.classList.add('show'); b.setAttribute('aria-hidden','false'); setTimeout(()=>a.querySelector('[data-alert-confirm]')?.focus(),50);});
        }
        async function fetchJson(url, options={}){
            const headers = {'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrf, ...(options.headers || {})};
            const response = await fetch(url, {...options, headers});
            const text = await response.text();
            let data = {};
            try{ data = text ? JSON.parse(text) : {}; }catch(e){ throw new Error(text || 'Ungültige Serverantwort.'); }
            if(!response.ok || data.success === false){
                let message = data.message || 'Serverfehler.';
                if(data.errors){
                    const detail = Object.values(data.errors).flat().filter(Boolean).join('\n');
                    if(detail) message += '\n\n' + detail;
                }
                throw new Error(message);
            }
            return data;
        }
        function setCount(selector, value){document.querySelectorAll(selector).forEach(el => el.textContent = value);}
        function employeeTemplate(option){
            if(!option.id){ return option.text; }
            const $option = window.jQuery ? window.jQuery(option.element) : null;
            const image = $option ? ($option.data('image') || '') : '';
            const name = $option ? ($option.data('name') || option.text) : option.text;
            const status = $option ? ($option.data('status') || 'Active') : 'Active';
            return window.jQuery(`<div class="dp-employee-option"><span class="dp-employee-option-avatar-wrap"><img src="${esc(image)}" alt=""><span class="dp-employee-option-status-dot"></span></span><span class="dp-employee-option-main"><span class="dp-employee-option-name">${esc(name)}</span><span class="dp-employee-option-meta">${esc(status)}</span></span></div>`);
        }
        function employeeSelection(option){
            if(!option.id){ return option.text; }
            const $option = window.jQuery ? window.jQuery(option.element) : null;
            const image = $option ? ($option.data('image') || '') : '';
            const name = $option ? ($option.data('name') || option.text) : option.text;
            return window.jQuery(`<div class="dp-employee-selection"><span class="dp-employee-selection-avatar-wrap"><img src="${esc(image)}" alt=""><span class="dp-employee-selection-status-dot"></span></span><span class="dp-employee-selection-main"><span class="dp-employee-selection-name">${esc(name)}</span><span class="dp-employee-selection-meta">Active</span></span></div>`);
        }
        function initEmployeeSelect2(){
            if(!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2){ return; }
            const $select = window.jQuery('.dp-employee-select');
            if(!$select.length || $select.hasClass('select2-hidden-accessible')){ return; }
            const $parent = window.jQuery('#dpMeasurementAssignmentModal .dp-modal');
            $select.select2({
                width: '100%',
                placeholder: $select.data('placeholder') || 'Aktiven Mitarbeiter wählen',
                allowClear: true,
                dropdownParent: $parent.length ? $parent : window.jQuery(document.body),
                templateResult: employeeTemplate,
                templateSelection: employeeSelection,
                escapeMarkup: function(markup){ return markup; }
            });

            $select.on('select2:open', function(){
                setTimeout(function(){
                    window.jQuery('.select2-container--open .select2-dropdown').addClass('dp-employee-dropdown');
                }, 0);
            });
        }
        document.addEventListener('click', function(e){
            const tab = e.target.closest('[data-dp-tab]');
            if(tab){const name = tab.dataset.dpTab; document.querySelectorAll('[data-dp-tab]').forEach(el=>el.classList.remove('active')); document.querySelectorAll('[data-dp-panel]').forEach(el=>el.classList.remove('active')); tab.classList.add('active'); document.querySelector('[data-dp-panel="'+name+'"]')?.classList.add('active'); return;}
            const preview = e.target.closest('[data-preview-url]');
            if(preview){const url=preview.dataset.previewUrl; const title=preview.dataset.previewTitle || 'Vorschau'; const isImage=preview.dataset.isImage === '1'; document.getElementById('dpPreviewTitle').textContent = title; document.getElementById('dpPreviewBody').innerHTML = isImage ? `<img class="dp-modal-image" src="${esc(url)}" alt="">` : `<iframe src="${esc(url)}" style="width:100%;height:70vh;border:0;border-radius:14px;"></iframe>`; document.getElementById('dpPreviewModal').classList.add('show'); return;}
            if(e.target.closest('[data-close-dp-modal]')){document.querySelectorAll('.dp-modal-backdrop').forEach(m=>m.classList.remove('show')); return;}
            if(e.target.closest('#dpOpenStatusModal')){document.getElementById('dpStatusModal').classList.add('show'); return;}
            if(e.target.closest('#dpOpenMeasurementAssignmentModal')){document.getElementById('dpMeasurementAssignmentModal')?.classList.add('show'); setTimeout(initEmployeeSelect2, 0); return;}
            const createMeasurement = e.target.closest('[data-create-measurement]');
            if(createMeasurement){
                createMeasurement.disabled = true;
                fetchJson(routes.createMeasurement, {method:'POST', body:new FormData()})
                    .then(data => { window.location.href = data.redirect_url || (window.location.pathname + '?tab=measurement&setup=1'); })
                    .catch(err => dpToast('error','Fehler',err.message))
                    .finally(() => { createMeasurement.disabled = false; });
                return;
            }
            const del = e.target.closest('[data-delete-document]');
            if(del){
                const card = del.closest('[data-document-card]'); const url = card?.dataset.deleteUrl; if(!url) return;
                dpConfirm('Dokument löschen?', 'Dieses Dokument wird aus dem Auftrag entfernt.', {type:'warning', confirmText:'Ja, löschen', cancelText:'Abbrechen'}).then(ok => {
                    if(!ok) return;
                    fetchJson(url, {method:'DELETE'}).then(data => {card.remove(); setCount('[data-document-count]', data.document_count ?? document.querySelectorAll('[data-document-card]').length); if(!document.querySelector('[data-document-card]')){document.getElementById('dpDocumentGrid').insertAdjacentHTML('afterend','<div class="dp-empty" id="dpDocumentEmpty">Keine Dokumente oder Bilder vorhanden.</div>');} dpToast('success','Gelöscht','Dokument wurde gelöscht.');}).catch(err => dpToast('error','Fehler',err.message));
                });
            }
        });
        document.getElementById('dpNoteForm')?.addEventListener('submit', async function(e){
            e.preventDefault(); const btn=this.querySelector('button[type="submit"]'); btn.disabled=true;
            try{const fd=new FormData(this); const data=await fetchJson(routes.noteStore,{method:'POST',body:fd}); document.getElementById('dpNotesEmpty')?.remove(); const note=data.note; document.getElementById('dpNotesList').insertAdjacentHTML('afterbegin', `<div class="dp-note"><div class="dp-note-head"><span class="dp-note-author">${esc(note.created_by_name)}</span><span class="dp-note-date">${esc(note.created_at)}</span></div><div style="margin-top:8px;white-space:pre-wrap;">${esc(note.description)}</div></div>`); this.reset(); setCount('[data-note-count]', data.note_count ?? ''); document.querySelector('[data-dp-tab="notes"]').click(); dpToast('success','Gespeichert','Notiz wurde gespeichert.');}catch(err){dpToast('error','Fehler',err.message);}finally{btn.disabled=false;}
        });
        document.getElementById('dpDocumentUploadForm')?.addEventListener('submit', async function(e){
            e.preventDefault(); const btn=this.querySelector('button[type="submit"]'); btn.disabled=true;
            try{const data=await fetchJson(routes.uploadDocument,{method:'POST',body:new FormData(this)}); document.getElementById('dpDocumentEmpty')?.remove(); const d=data.document; const delSource = d.source || 'image';
                const delUrl = @json(url('/deal')) + '/' + @json($deal->id) + '/profile/documents/' + encodeURIComponent(delSource) + '/' + encodeURIComponent(d.id); document.getElementById('dpDocumentGrid').insertAdjacentHTML('afterbegin', `<div class="dp-file" data-document-card data-delete-url="${esc(delUrl)}"><div class="dp-file-preview" data-preview-url="${esc(d.preview_url)}" data-preview-title="${esc(d.title)}" data-is-image="${d.is_image ? '1':'0'}">${d.is_image ? `<img src="${esc(d.preview_url)}" alt="">` : '<i class="fa fa-file-o"></i>'}</div><div class="dp-file-body"><div class="dp-file-title">${esc(d.title)}</div><div class="dp-sub">${esc(d.stage || 'Dokument')} · ${esc(d.created_at || '')}</div><div class="dp-file-actions"><button type="button" class="dp-btn soft" data-preview-url="${esc(d.preview_url)}" data-preview-title="${esc(d.title)}" data-is-image="${d.is_image ? '1':'0'}">Ansehen</button><button type="button" class="dp-btn danger" data-delete-document>Löschen</button></div></div></div>`); this.reset(); setCount('[data-document-count]', data.document_count ?? ''); document.querySelector('[data-dp-tab="files"]').click(); dpToast('success','Hochgeladen','Dokument wurde hochgeladen.');}catch(err){dpToast('error','Fehler',err.message);}finally{btn.disabled=false;}
        });
        document.addEventListener('click', function(e){
            const forceAppointmentBtn = e.target.closest('[data-force-new-appointment]');
            if(forceAppointmentBtn){
                const input = document.getElementById('dpForceNewAppointment');
                if(input) input.value = '1';
                dpToast('info','Neuer Termin','Beim nächsten Speichern wird ein neuer Kalendertermin erstellt.');
                return;
            }
            const forceTaskBtn = e.target.closest('[data-force-new-task]');
            if(forceTaskBtn){
                const input = document.getElementById('dpForceNewTask');
                if(input) input.value = '1';
                const createTask = document.querySelector('#dpMeasurementAssignmentForm [name="create_task"]');
                if(createTask) createTask.checked = true;
                dpToast('info','Neue Aufgabe','Beim nächsten Speichern wird eine neue persönliche Aufgabe erstellt.');
                return;
            }
            const unlinkAppointmentBtn = e.target.closest('[data-unlink-appointment]');
            if(unlinkAppointmentBtn){
                const input = document.getElementById('dpUnlinkAppointment');
                if(input) input.value = '1';
                const force = document.getElementById('dpForceNewAppointment');
                if(force) force.value = '1';
                dpToast('warning','Verknüpfung lösen','Beim nächsten Speichern wird die alte Termin-Verknüpfung gelöst und ein neuer Termin erstellt.');
                return;
            }
            const unlinkTaskBtn = e.target.closest('[data-unlink-task]');
            if(unlinkTaskBtn){
                const input = document.getElementById('dpUnlinkTask');
                if(input) input.value = '1';
                const force = document.getElementById('dpForceNewTask');
                if(force) force.value = '1';
                const createTask = document.querySelector('#dpMeasurementAssignmentForm [name="create_task"]');
                if(createTask) createTask.checked = true;
                dpToast('warning','Verknüpfung lösen','Beim nächsten Speichern wird die alte Aufgaben-Verknüpfung gelöst und eine neue Aufgabe erstellt.');
                return;
            }
        });

        document.getElementById('dpMeasurementAssignmentForm')?.addEventListener('submit', async function(e){
            e.preventDefault();
            if(!routes.assignMeasurement){ dpAlert('Feinaufmaß fehlt','Für diesen Auftrag wurde noch kein Feinaufmaß erstellt. Erstelle zuerst das Feinaufmaß, danach öffnet sich automatisch das Planungsfenster.','warning'); return; }

            const employeeId = this.querySelector('[name="employee_id"]')?.value || '';
            const startDate = this.querySelector('[name="start_date"]')?.value || '';
            const startTime = this.querySelector('[name="start_time"]')?.value || '';
            const endTime = this.querySelector('[name="end_time"]')?.value || '';
            if(!employeeId || !startDate || !startTime || !endTime){
                dpAlert('Pflichtfelder fehlen','Bitte Mitarbeiter, Startdatum, Startzeit und Endzeit ausfüllen.','warning');
                return;
            }

            const btn=this.querySelector('button[type="submit"]');
            if(btn) btn.disabled=true;
            try{
                const fd=new FormData(this);
                if(!fd.has('create_task')) fd.append('create_task','0');
                if(!fd.get('deal_measurement_id')) fd.append('deal_measurement_id', @json($measurementId));
                if(!fd.get('measurement_id')) fd.append('measurement_id', @json($measurementId));
                if(!fd.get('deal_id')) fd.append('deal_id', @json($deal->id));
                if(!fd.get('customer_id')) fd.append('customer_id', @json($deal->customer_id));
                if(!fd.get('alternative_id')) fd.append('alternative_id', @json($deal->alternative_id));
                if(!fd.get('product_id')) fd.append('product_id', @json($deal->product_id));
                if(!fd.get('force_new_appointment')) fd.append('force_new_appointment', document.getElementById('dpForceNewAppointment')?.value || '0');
                if(!fd.get('force_new_task')) fd.append('force_new_task', document.getElementById('dpForceNewTask')?.value || '0');
                if(!fd.get('unlink_appointment')) fd.append('unlink_appointment', document.getElementById('dpUnlinkAppointment')?.value || '0');
                if(!fd.get('unlink_task')) fd.append('unlink_task', document.getElementById('dpUnlinkTask')?.value || '0');
                const data=await fetchJson(routes.assignMeasurement,{method:'POST',body:fd});
                dpToast('success','Gespeichert',data.message || 'Feinaufmaß wurde geplant.');

                if(data.appointment_id || data.personal_task_id){
                    const linkBox = document.getElementById('dpAssignmentLinks');
                    if(linkBox){
                        linkBox.innerHTML = `Feinaufmaß: ${esc(@json($measurementNo))}<br>Termin: ${data.appointment_id ? '#' + esc(data.appointment_id) : '–'}<br>Aufgabe: ${data.personal_task_id ? '#' + esc(data.personal_task_id) : '–'}`;
                    }
                    const appointmentMeta = document.getElementById('dpAppointmentHealthMeta');
                    const appointmentLabel = document.getElementById('dpAppointmentHealthLabel');
                    if(appointmentMeta && data.appointment_id){ appointmentMeta.innerHTML = `ID: #${esc(data.appointment_id)}<br>Gerade gespeichert und aktiv verknüpft.<br>deal_measurement_id: ${esc(@json($measurementId))}`; }
                    if(appointmentLabel && data.appointment_id){ appointmentLabel.textContent = 'Aktiv verknüpft'; appointmentLabel.className = 'dp-pill success'; }
                    const taskMeta = document.getElementById('dpTaskHealthMeta');
                    const taskLabel = document.getElementById('dpTaskHealthLabel');
                    if(taskMeta){ taskMeta.innerHTML = data.personal_task_id ? `ID: #${esc(data.personal_task_id)}<br>Gerade gespeichert und aktiv verknüpft.<br>deal_measurement_id: ${esc(@json($measurementId))}` : 'Keine persönliche Aufgabe verknüpft.'; }
                    if(taskLabel){ taskLabel.textContent = data.personal_task_id ? 'Aktiv verknüpft' : 'Nicht verknüpft'; taskLabel.className = data.personal_task_id ? 'dp-pill success' : 'dp-pill warning'; }
                }
                setTimeout(() => window.location.href = window.location.pathname + '?tab=measurement', 850);
            }catch(err){
                dpToast('error','Fehler',err.message);
            }finally{
                if(btn) btn.disabled=false;
            }
        });

        const params = new URLSearchParams(window.location.search);
        if(params.get('tab') === 'measurement'){
            document.querySelector('[data-dp-tab="measurement"]')?.click();
        }
        if(params.get('setup') === '1'){
            setTimeout(() => { document.getElementById('dpMeasurementAssignmentModal')?.classList.add('show'); initEmployeeSelect2(); }, 250);
        }

        initEmployeeSelect2();

        document.getElementById('dpStatusForm')?.addEventListener('submit', async function(e){
            e.preventDefault(); const btn=this.querySelector('button[type="submit"]'); btn.disabled=true;
            try{const data=await fetchJson(routes.updateStatus,{method:'POST',body:new FormData(this)}); document.getElementById('dpCurrentStatusLabel').textContent=data.status_label; document.getElementById('dpCurrentStatusLabel').style.color=data.status_color; document.getElementById('dpStatusStat').textContent=data.status_label; document.getElementById('dpStatusStat').style.color=data.status_color; document.getElementById('dpStatusPill').textContent=data.status_label; document.getElementById('dpStatusPill').style.color=data.status_color; document.getElementById('dpStatusPill').style.borderColor=data.status_color; document.querySelectorAll('.dp-modal-backdrop').forEach(m=>m.classList.remove('show')); if(data.note){document.getElementById('dpNotesEmpty')?.remove(); document.getElementById('dpNotesList').insertAdjacentHTML('afterbegin', `<div class="dp-note"><div class="dp-note-head"><span class="dp-note-author">${esc(data.note.created_by_name)}</span><span class="dp-note-date">${esc(data.note.created_at)}</span></div><div style="margin-top:8px;white-space:pre-wrap;">${esc(data.note.description)}</div></div>`); setCount('[data-note-count]', data.note_count ?? '');} dpToast('success','Status gespeichert','Auftragstatus wurde aktualisiert.');}catch(err){dpToast('error','Fehler',err.message);}finally{btn.disabled=false;}
        });
    })();
    </script>
@endsection
