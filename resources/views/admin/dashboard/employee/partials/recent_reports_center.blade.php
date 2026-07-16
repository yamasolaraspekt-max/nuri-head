@extends('admin.layouts.app')

@section('title', 'Berichte Dashboard')

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .rr-wrap {
            --rr-primary: #73b2d4;
            --rr-primary-dark: var(--sa-accent-hover);
            --rr-primary-light: var(--sa-accent-light);
            --rr-secondary: #93c21c;
            --rr-secondary-light: #cfe09b;
            --rr-danger: #e50656;
            --rr-warning: #f8ac00;

            --rr-text-dark: #263445;
            --rr-text-muted: #748092;
            --rr-bg-body: #f0f4f8;
            --rr-surface: #ffffff;
            --rr-border: #e2e8f0;

            --rr-status-inquiry-bg: #e3f2fd;
            --rr-status-inquiry-text: #1565c0;
            --rr-status-lead-bg: var(--rr-secondary-light);
            --rr-status-lead-text: #5a7d0c;
            --rr-status-task-bg: #f1f5f9;
            --rr-status-task-text: #475569;
            --rr-status-appt-bg: var(--rr-primary-light);
            --rr-status-appt-text: #2c5c7a;
            --rr-status-ticket-bg: #fff3cd;
            --rr-status-ticket-text: #856404;

            font-family: 'Outfit', sans-serif;
            padding: 20px;
            background-color: var(--rr-bg-body);
            min-height: calc(100vh - 80px);
        }

        .rr-wrap * {
            box-sizing: border-box;
        }

        .rr-container {
            margin: 0 auto; 
        }

        .rr-flex-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .rr-mb-2 {
            margin-bottom: .5rem;
        }

        .rr-mb-4 {
            margin-bottom: 1.5rem;
        }

        .rr-me-2 {
            margin-right: .5rem;
        }

        .rr-gap-2 {
            gap: .5rem;
        }

        .rr-text-muted {
            color: var(--rr-text-muted);
        }

        .rr-small {
            font-size: .875rem;
        }

        .rr-fw-bold {
            font-weight: 700;
        }

        .rr-fw-medium {
            font-weight: 500;
        }

        .rr-text-right {
            text-align: right;
        }

        .rr-text-center {
            text-align: center;
        }

        .w-100 {
            width: 100%;
        }

        .rr-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
            background: linear-gradient(135deg, #ffffff, #f8fcff);
            border: 1px solid rgba(115, 178, 212, .22);
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, .05);
        }

        .rr-page-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--rr-primary-dark);
            background: rgba(115, 178, 212, .14);
            border: 1px solid rgba(115, 178, 212, .2);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 10px;
        }

        .rr-page-title {
            font-size: 28px;
            font-weight: 900;
            color: var(--rr-text-dark);
            margin: 0;
            line-height: 1.15;
        }

        .rr-page-subtitle {
            color: var(--rr-text-muted);
            margin: 8px 0 0;
            max-width: 780px;
            line-height: 1.55;
        }

        .rr-page-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .rr-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .rr-stat-card {
            background: var(--rr-surface);
            border-radius: 18px;
            padding: 1.35rem;
            box-shadow: 0 4px 20px rgba(115, 178, 212, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(226, 232, 240, .85);
        }

        .rr-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(115, 178, 212, 0.15);
        }

        .rr-stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 1rem;
        }

        .rr-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .rr-stat-value {
            font-size: 1.75rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 0.25rem;
            color: var(--rr-text-dark);
        }

        .rr-stat-label {
            font-size: 0.875rem;
            color: var(--rr-text-muted);
            font-weight: 600;
        }

        .rr-main-card {
            background: var(--rr-surface);
            border-radius: 22px;
            box-shadow: 0 2px 18px rgba(15, 23, 42, .05);
            border: 1px solid rgba(226, 232, 240, .95);
            overflow: hidden;
        }

        .rr-filter-bar {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--rr-border);
            padding: 1.5rem;
        }

        .rr-filter-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .rr-filter-group {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .rr-col-12 {
            grid-column: span 12;
        }

        .rr-col-6 {
            grid-column: span 6;
        }

        .rr-col-4 {
            grid-column: span 4;
        }

        .rr-col-3 {
            grid-column: span 3;
        }

        .rr-col-2 {
            grid-column: span 2;
        }

        .rr-filter-label {
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.55px;
            font-weight: 900;
            color: #91a0b4;
            margin-bottom: 7px;
        }

        .rr-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .rr-input-icon {
            position: absolute;
            left: 1rem;
            color: var(--rr-text-muted);
            pointer-events: none;
            z-index: 2;
        }

        .rr-form-control,
        .rr-form-select {
            width: 100%;
            border: 1px solid var(--rr-border);
            border-radius: 13px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            font-family: inherit;
            background-color: #fff;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background .2s;
            height: 44px;
            color: var(--rr-text-dark);
        }

        .rr-has-icon {
            padding-left: 2.65rem;
        }

        .rr-form-control:focus,
        .rr-form-select:focus {
            border-color: var(--rr-primary);
            box-shadow: 0 0 0 3px rgba(115, 178, 212, 0.2);
            background: #fff;
        }

        textarea.rr-form-control {
            height: auto;
            resize: vertical;
        }

        .rr-btn {
            border: none;
            border-radius: 13px;
            padding: 0.5rem 1rem;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
            height: 44px;
            background: #fff;
            font-size: .95rem;
            white-space: nowrap;
        }

        .rr-btn-sm {
            height: 34px;
            font-size: 0.85rem;
            padding: 0 0.8rem;
        }

        .rr-btn-primary-soft {
            background-color: var(--rr-primary);
            color: #fff;
            box-shadow: 0 10px 22px rgba(115, 178, 212, .2);
        }

        .rr-btn-primary-soft:hover {
            background-color: var(--rr-primary-dark);
            transform: translateY(-1px);
            color: #fff;
        }

        .rr-btn-primary-soft:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .rr-btn-light {
            background-color: #fff;
            border: 1px solid var(--rr-border);
            color: var(--rr-text-dark);
        }

        .rr-btn-light:hover {
            background-color: #f8fafc;
            border-color: rgba(115, 178, 212, .45);
        }

        .rr-btn-danger-soft {
            background: rgba(229, 6, 86, .08);
            color: var(--rr-danger);
            border: 1px solid rgba(229, 6, 86, .14);
        }

        .rr-btn-danger-soft:hover {
            background: rgba(229, 6, 86, .13);
            color: var(--rr-danger);
        }

        .rr-btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--rr-text-muted);
            background: transparent;
            border: 1px solid var(--rr-border);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .rr-btn-icon:hover {
            border-color: var(--rr-primary);
            color: var(--rr-primary);
            background: #f0f7fb;
        }

        .rr-btn-icon[disabled] {
            opacity: .45;
            cursor: not-allowed;
        }

        .rr-active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px;
            min-height: 1px;
        }

        .rr-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(115, 178, 212, .12);
            color: var(--rr-primary-dark);
            border: 1px solid rgba(115, 178, 212, .22);
            font-size: 12px;
            font-weight: 800;
        }

        .rr-chip button {
            border: 0;
            background: transparent;
            color: inherit;
            padding: 0;
            cursor: pointer;
            width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .rr-smart-search-box {
            position: relative;
        }

        .rr-smart-suggestions {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            border: 1px solid var(--rr-border);
            border-radius: 18px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .14);
            z-index: 2000;
            overflow: hidden;
            display: none;
            max-height: 340px;
            overflow-y: auto;
        }

        .rr-smart-suggestions.active {
            display: block;
        }

        .rr-suggestion-item {
            width: 100%;
            border: 0;
            background: #fff;
            padding: 12px 14px;
            display: grid;
            grid-template-columns: 38px 1fr auto;
            align-items: center;
            gap: 11px;
            cursor: pointer;
            text-align: left;
            transition: background .18s ease;
        }

        .rr-suggestion-item:hover,
        .rr-suggestion-item.active {
            background: #f8fafc;
        }

        .rr-suggestion-icon {
            width: 38px;
            height: 38px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(115, 178, 212, .14);
            color: var(--rr-primary-dark);
        }

        .rr-suggestion-title {
            color: var(--rr-text-dark);
            font-weight: 900;
            font-size: 13px;
            line-height: 1.25;
        }

        .rr-suggestion-sub {
            color: var(--rr-text-muted);
            font-size: 12px;
            margin-top: 3px;
            line-height: 1.3;
        }

        .rr-suggestion-badge {
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 4px 8px;
            white-space: nowrap;
        }

        .rr-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .rr-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        .rr-table th {
            background: #fff;
            color: var(--rr-text-muted);
            font-weight: 900;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.15rem 1.3rem;
            border-bottom: 2px solid var(--rr-border);
            text-align: left;
        }

        .rr-table td {
            padding: 1.15rem 1.3rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--rr-border);
            background: #fff;
            color: var(--rr-text-dark);
        }

        .rr-table tr:last-child td {
            border-bottom: none;
        }

        .rr-table tr:hover td {
            background-color: #fcfdfe;
        }

        .rr-status-badge {
            padding: 0.42em 0.82em;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .rr-badge-inquiry {
            background: var(--rr-status-inquiry-bg);
            color: var(--rr-status-inquiry-text);
        }

        .rr-badge-lead {
            background: var(--rr-status-lead-bg);
            color: var(--rr-status-lead-text);
        }

        .rr-badge-task {
            background: var(--rr-status-task-bg);
            color: var(--rr-status-task-text);
        }

        .rr-badge-appointment {
            background: var(--rr-status-appt-bg);
            color: var(--rr-status-appt-text);
        }

        .rr-badge-ticket {
            background: var(--rr-status-ticket-bg);
            color: var(--rr-status-ticket-text);
        }

        .rr-user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 190px;
        }

        .rr-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--rr-primary-light), #fff);
            color: var(--rr-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 0.85rem;
            border: 2px solid #fff;
            box-shadow: 0 2px 7px rgba(0, 0, 0, 0.06);
            flex-shrink: 0;
        }

        .rr-report-preview {
            max-width: 520px;
            white-space: pre-wrap;
            line-height: 1.45;
            color: var(--rr-text-dark);
            background: #f8fafc;
            border: 1px solid var(--rr-border);
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 13px;
        }

        .rr-report-meta-line {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 8px;
            color: var(--rr-text-muted);
            font-size: 12px;
        }

        .rr-report-meta-line span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }


        /* Report preview + source detail modal */
        .rr-report-preview-btn {
            width: 100%;
            max-width: 520px;
            border: 1px solid var(--rr-border);
            border-radius: 14px;
            padding: 10px 12px;
            background: #f8fafc;
            color: var(--rr-text-dark);
            font-size: 13px;
            line-height: 1.45;
            text-align: left;
            cursor: pointer;
            transition: all .2s ease;
        }

        .rr-report-preview-btn:hover {
            border-color: rgba(115, 178, 212, .55);
            background: #f0f7fb;
            transform: translateY(-1px);
        }

        .rr-report-preview-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            white-space: normal;
        }

        .rr-report-preview-more {
            margin-top: 7px;
            font-size: 11px;
            font-weight: 900;
            color: var(--rr-primary-dark);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .rr-detail-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 10060;
            background: rgba(15, 23, 42, .55);
            backdrop-filter: blur(6px);
            opacity: 0;
            visibility: hidden;
            transition: opacity .22s ease, visibility .22s ease;
        }

        .rr-detail-modal-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .rr-detail-modal {
            position: fixed;
            inset: 0;
            z-index: 10061;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .22s ease, visibility .22s ease;
        }

        .rr-detail-modal.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .rr-detail-card {
            width: min(1180px, 100%);
            max-height: 88vh;
            background: #fff;
            border-radius: 26px;
            box-shadow: 0 30px 90px rgba(15, 23, 42, .30);
            overflow: hidden;
            transform: translateY(16px) scale(.98);
            transition: transform .22s ease;
            border: 1px solid rgba(226, 232, 240, .95);
            display: flex;
            flex-direction: column;
        }

        .rr-detail-modal.active .rr-detail-card {
            transform: translateY(0) scale(1);
        }

        .rr-detail-header {
            display: grid;
            grid-template-columns: 58px 1fr 40px;
            gap: 14px;
            align-items: flex-start;
            padding: 22px 24px;
            background: radial-gradient(circle at top left, rgba(115, 178, 212, .20), transparent 38%), linear-gradient(135deg, #ffffff, #f8fcff);
            border-bottom: 1px solid var(--rr-border);
        }

        .rr-detail-icon {
            width: 58px;
            height: 58px;
            border-radius: 20px;
            background: rgba(115, 178, 212, .16);
            color: var(--rr-primary-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .rr-detail-title {
            margin: 0;
            font-size: 21px;
            font-weight: 900;
            color: var(--rr-text-dark);
            line-height: 1.2;
        }

        .rr-detail-sub {
            margin: 7px 0 0;
            font-size: 13px;
            color: var(--rr-text-muted);
            line-height: 1.45;
        }

        .rr-detail-close {
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 13px;
            background: rgba(255, 255, 255, .85);
            color: var(--rr-text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
        }

        .rr-detail-close:hover {
            background: #fff;
            color: var(--rr-danger);
            transform: translateY(-1px);
        }

        .rr-detail-body {
            padding: 22px 24px;
            overflow-y: auto;
        }

        .rr-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .rr-detail-info {
            border: 1px solid var(--rr-border);
            background: #f8fafc;
            border-radius: 16px;
            padding: 13px 14px;
        }

        .rr-detail-info-label {
            font-size: 11px;
            font-weight: 900;
            color: #91a0b4;
            text-transform: uppercase;
            letter-spacing: .55px;
            margin-bottom: 5px;
        }

        .rr-detail-info-value {
            font-size: 14px;
            font-weight: 800;
            color: var(--rr-text-dark);
            line-height: 1.35;
            word-break: break-word;
        }

        .rr-detail-report-box {
            border: 1px solid rgba(115, 178, 212, .25);
            background: linear-gradient(135deg, #f8fcff, #ffffff);
            border-radius: 20px;
            padding: 18px;
        }

        .rr-detail-report-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 900;
            color: var(--rr-primary-dark);
            text-transform: uppercase;
            letter-spacing: .55px;
            margin-bottom: 12px;
        }

        .rr-detail-report-text {
            white-space: pre-wrap;
            line-height: 1.65;
            color: var(--rr-text-dark);
            font-size: 15px;
        }

        .rr-detail-footer {
            padding: 16px 24px 22px;
            background: #fff;
            border-top: 1px solid var(--rr-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media(max-width: 768px) {
            .rr-detail-header {
                grid-template-columns: 48px 1fr 38px;
                padding: 18px;
            }

            .rr-detail-icon {
                width: 48px;
                height: 48px;
                border-radius: 16px;
                font-size: 19px;
            }

            .rr-detail-body {
                padding: 18px;
            }

            .rr-detail-grid {
                grid-template-columns: 1fr;
            }

            .rr-detail-footer {
                padding: 14px 18px 18px;
                flex-direction: column-reverse;
            }

            .rr-detail-footer .rr-btn {
                width: 100%;
            }
        }

        .rr-table-footer {
            padding: 1.35rem 1.5rem;
            border-top: 1px solid var(--rr-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            background: #fff;
        }

        .rr-sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, .38);
            backdrop-filter: blur(4px);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility .3s;
        }

        .rr-sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .rr-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 520px;
            height: 100%;
            background: #fff;
            z-index: 9999;
            box-shadow: -5px 0 35px rgba(15, 23, 42, 0.14);
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .rr-sidebar.active {
            transform: translateX(0);
        }

        .rr-sidebar-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--rr-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #fff, #f8fcff);
        }

        .rr-sidebar-body {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
        }

        .rr-close-btn {
            background: #fff;
            border: 1px solid var(--rr-border);
            border-radius: 12px;
            width: 38px;
            height: 38px;
            font-size: 1.1rem;
            color: var(--rr-text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .rr-highlight-box {
            background: linear-gradient(135deg, var(--rr-primary-light) 0%, #fff 100%);
            border-radius: 16px;
            padding: 1.25rem;
            border-left: 4px solid var(--rr-primary);
            margin-bottom: 1.5rem;
        }

        .rr-existing-box {
            border: 1px solid var(--rr-border);
            border-radius: 14px;
            padding: 12px;
            max-height: 300px;
            overflow: auto;
            background: #fff;
        }

        .rr-report-item {
            padding: 12px;
            border-bottom: 1px solid var(--rr-border);
            background: #fff;
        }

        .rr-report-item:last-child {
            border-bottom: none;
        }

        .rr-report-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .rr-report-who {
            font-weight: 900;
            color: var(--rr-text-dark);
            font-size: 13px;
        }

        .rr-report-when {
            font-size: 12px;
            color: var(--rr-text-muted);
            white-space: nowrap;
        }

        .rr-report-text {
            font-size: 13px;
            color: var(--rr-text-dark);
            white-space: pre-wrap;
            line-height: 1.5;
            background: #f8fafc;
            border: 1px solid var(--rr-border);
            border-radius: 12px;
            padding: 10px;
        }

        .rr-radio-group {
            display: flex;
            gap: 0.5rem;
        }

        .rr-radio-option {
            flex: 1;
            position: relative;
        }

        .rr-radio-option input {
            position: absolute;
            opacity: 0;
            height: 0;
            width: 0;
        }

        .rr-radio-label {
            display: block;
            text-align: center;
            padding: 0.65rem;
            border: 1px solid var(--rr-border);
            border-radius: 12px;
            cursor: pointer;
            color: var(--rr-text-muted);
            font-weight: 800;
            transition: all 0.2s;
        }

        .rr-radio-option input:checked+.rr-radio-label {
            border-color: var(--rr-secondary);
            color: var(--rr-secondary);
            background-color: #f7fcf0;
        }

        .rr-select2-wrap {
            position: relative;
        }

        .rr-select2-badge {
            position: absolute;
            right: 10px;
            top: 35px;
            font-size: 10px;
            padding: 4px 7px;
            border-radius: 999px;
            background: rgba(115, 178, 212, .15);
            color: var(--rr-primary-dark);
            border: 1px solid rgba(115, 178, 212, .25);
            pointer-events: none;
            z-index: 5;
        }

        .rr-wrap .select2-container {
            width: 100% !important;
            min-width: 0 !important;
        }

        .rr-wrap .select2-container--default .select2-selection--single {
            height: 44px;
            border: 1px solid var(--rr-border);
            border-radius: 13px;
            display: flex;
            align-items: center;
            padding-left: 8px;
            padding-right: 38px;
            background: #fff;
            transition: all .2s ease;
        }

        .rr-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            color: var(--rr-text-dark);
            padding-left: 8px;
            padding-right: 28px;
            font-weight: 700;
            width: 100%;
        }

        .rr-wrap .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
            font-weight: 600;
        }

        .rr-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
            right: 8px;
        }

        .rr-wrap .select2-container--default.select2-container--focus .select2-selection--single,
        .rr-wrap .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--rr-primary);
            box-shadow: 0 0 0 3px rgba(115, 178, 212, 0.2);
        }

        .select2-dropdown.rr-smart-select-dropdown {
            border: 1px solid var(--rr-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
        }

        .select2-dropdown.rr-smart-select-dropdown .select2-search--dropdown {
            padding: 10px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .select2-dropdown.rr-smart-select-dropdown .select2-search__field {
            border: 1px solid var(--rr-border);
            border-radius: 12px;
            padding: 10px 12px;
            outline: none;
            font-family: 'Outfit', sans-serif;
        }

        .select2-dropdown.rr-smart-select-dropdown .select2-results__option {
            padding: 10px 12px;
        }

        .select2-dropdown.rr-smart-select-dropdown .select2-results__options {
            max-height: 280px;
            overflow-y: auto;
        }

        .select2-dropdown.rr-smart-select-dropdown .select2-results__option--highlighted.select2-results__option--selectable {
            background: rgba(115, 178, 212, .12);
            color: var(--rr-text-dark);
        }

        .rr-select-option {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .rr-select-dot {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--rr-primary-light), #fff);
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: var(--rr-primary-dark);
            flex-shrink: 0;
            font-size: 12px;
        }

        .rr-select-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            min-width: 0;
        }

        .rr-select-name {
            font-weight: 800;
            color: var(--rr-text-dark);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rr-select-sub {
            font-size: 12px;
            color: var(--rr-text-muted);
            margin-top: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rr-error-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .52);
            backdrop-filter: blur(6px);
            z-index: 10050;
            opacity: 0;
            visibility: hidden;
            transition: opacity .22s ease, visibility .22s ease;
        }

        .rr-error-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .rr-error-modal {
            position: fixed;
            inset: 0;
            z-index: 10051;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .22s ease, visibility .22s ease;
        }

        .rr-error-modal.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .rr-error-card {
            width: min(560px, 100%);
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, .28);
            overflow: hidden;
            transform: translateY(18px) scale(.98);
            transition: transform .22s ease;
            border: 1px solid rgba(226, 232, 240, .95);
        }

        .rr-error-modal.active .rr-error-card {
            transform: translateY(0) scale(1);
        }

        .rr-error-header {
            display: grid;
            grid-template-columns: 54px 1fr 38px;
            gap: 14px;
            align-items: flex-start;
            padding: 22px 24px;
            background:
                radial-gradient(circle at top left, rgba(229, 6, 86, .15), transparent 34%),
                linear-gradient(135deg, #fff, #fff5f8);
            border-bottom: 1px solid rgba(229, 6, 86, .12);
        }

        .rr-error-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(229, 6, 86, .12);
            color: var(--rr-danger);
            font-size: 22px;
            box-shadow: inset 0 0 0 1px rgba(229, 6, 86, .12);
        }

        .rr-error-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 20px;
            font-weight: 900;
            line-height: 1.2;
        }

        .rr-error-header p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.45;
        }

        .rr-error-close {
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 12px;
            background: rgba(255, 255, 255, .82);
            color: #64748b;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
        }

        .rr-error-close:hover {
            background: #fff;
            color: var(--rr-danger);
            transform: translateY(-1px);
        }

        .rr-error-body {
            padding: 22px 24px;
        }

        .rr-error-message {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px 16px;
            color: #334155;
            font-size: 14px;
            line-height: 1.55;
            white-space: pre-wrap;
        }

        .rr-error-list {
            margin-top: 12px;
            border-radius: 16px;
            border: 1px solid rgba(229, 6, 86, .16);
            background: rgba(229, 6, 86, .045);
            padding: 12px 14px;
        }

        .rr-error-list ul {
            margin: 0;
            padding-left: 18px;
        }

        .rr-error-list li {
            color: #9f1239;
            font-size: 13px;
            line-height: 1.5;
            margin: 4px 0;
        }

        .rr-error-footer {
            padding: 16px 24px 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #fff;
        }

        @media (max-width: 1200px) {

            .rr-col-4,
            .rr-col-3,
            .rr-col-2 {
                grid-column: span 6;
            }
        }

        @media (max-width: 768px) {
            .rr-wrap {
                padding: 10px;
            }

            .rr-page-header {
                flex-direction: column;
                padding: 18px;
            }

            .rr-page-actions {
                width: 100%;
                justify-content: stretch;
            }

            .rr-page-actions .rr-btn {
                flex: 1;
            }

            .rr-filter-grid {
                grid-template-columns: 1fr;
            }

            .rr-col-12,
            .rr-col-6,
            .rr-col-4,
            .rr-col-3,
            .rr-col-2 {
                grid-column: span 1;
            }

            .rr-table-footer {
                flex-direction: column;
                align-items: flex-start;
            }

            .rr-sidebar {
                max-width: 100%;
            }

            .rr-sidebar-body {
                padding: 1.2rem;
            }

            .rr-btn-primary-soft {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .rr-error-header {
                grid-template-columns: 44px 1fr 34px;
                padding: 18px;
            }

            .rr-error-icon {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                font-size: 18px;
            }

            .rr-error-body {
                padding: 18px;
            }

            .rr-error-footer {
                padding: 14px 18px 18px;
                flex-direction: column-reverse;
            }

            .rr-error-footer .rr-btn {
                width: 100%;
            }
        }
    </style>

    <style>
        .rr-source-panel {
            grid-column: 1 / -1;
            border: 1px solid var(--rr-border);
            border-radius: 22px;
            background: #fff;
            overflow: hidden;
        }

        .rr-source-head {
            padding: 16px 18px;
            background: linear-gradient(135deg, #f8fcff, #ffffff);
            border-bottom: 1px solid var(--rr-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .rr-source-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 900;
            color: var(--rr-text-dark);
            font-size: 17px;
        }

        .rr-source-title i {
            color: var(--rr-primary-dark);
        }

        .rr-source-body {
            padding: 18px;
        }

        .rr-source-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .rr-source-item {
            background: #f8fafc;
            border: 1px solid var(--rr-border);
            border-radius: 15px;
            padding: 12px 13px;
        }

        .rr-source-item.full {
            grid-column: 1 / -1;
        }

        .rr-source-label {
            font-size: 11px;
            font-weight: 900;
            color: #91a0b4;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 5px;
        }

        .rr-source-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--rr-text-dark);
            line-height: 1.45;
            word-break: break-word;
        }

        .rr-map-box {
            grid-column: 1 / -1;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(115, 178, 212, .28);
            background: #f8fafc;
            min-height: 280px;
        }

        .rr-map-box iframe {
            width: 100%;
            height: 320px;
            border: 0;
            display: block;
        }

        .rr-mini-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .rr-mini-row {
            background: #fff;
            border: 1px solid var(--rr-border);
            border-radius: 12px;
            padding: 10px 11px;
        }

        .rr-mini-row strong {
            display: block;
            font-size: 13px;
            color: var(--rr-text-dark);
            margin-bottom: 4px;
        }

        .rr-mini-row small {
            color: var(--rr-text-muted);
            font-weight: 700;
        }

        .rr-progress-line {
            height: 10px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 8px;
        }

        .rr-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--rr-primary), var(--rr-secondary));
            border-radius: inherit;
        }

        .rr-collapse-stack {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .rr-collapse {
            border: 1px solid var(--rr-border);
            border-radius: 18px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .035);
        }

        .rr-collapse[open] {
            border-color: rgba(115, 178, 212, .35);
            box-shadow: 0 12px 26px rgba(115, 178, 212, .08);
        }

        .rr-collapse-summary {
            list-style: none;
            cursor: pointer;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: linear-gradient(135deg, #f8fcff, #ffffff);
            color: var(--rr-text-dark);
            font-weight: 900;
            user-select: none;
        }

        .rr-collapse-summary::-webkit-details-marker {
            display: none;
        }

        .rr-collapse-title {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .rr-collapse-title i {
            color: var(--rr-primary-dark);
        }

        .rr-collapse-chevron {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--rr-primary-dark);
            background: rgba(115, 178, 212, .12);
            transition: transform .18s ease;
            flex-shrink: 0;
        }

        .rr-collapse[open] .rr-collapse-chevron {
            transform: rotate(180deg);
        }

        .rr-collapse-body {
            padding: 14px;
            border-top: 1px solid var(--rr-border);
            background: #fff;
        }

        .rr-collapse-body .rr-source-grid {
            margin: 0;
        }

        .rr-ticket-panel .rr-source-body {
            background: #fbfdff;
        }


        @media(max-width: 768px) {
            .rr-source-grid {
                grid-template-columns: 1fr;
            }

            .rr-source-item.full,
            .rr-map-box {
                grid-column: span 1;
            }

            .rr-map-box iframe {
                height: 260px;
            }
        }
    </style>
    <style>
        .rr-employee-report-panel {
            background:
                radial-gradient(circle at top left, rgba(115, 178, 212, .22), transparent 34%),
                linear-gradient(135deg, #ffffff, #f8fcff);
            border: 1px solid rgba(115, 178, 212, .22);
            border-radius: 28px;
            margin-bottom: 1.5rem;
            box-shadow: 0 18px 52px rgba(15, 23, 42, .07);
            overflow: hidden;
        }

        .rr-employee-report-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 20px 22px 16px;
            border-bottom: 1px solid rgba(226, 232, 240, .78);
        }

        .rr-employee-report-head h2 {
            margin: 0;
            color: var(--rr-text-dark);
            font-size: 23px;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .rr-employee-report-head p {
            margin: 6px 0 0;
            color: var(--rr-text-muted);
            font-size: 14px;
            line-height: 1.45;
        }

        .rr-employee-head-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .rr-employee-summary-tabs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(226, 232, 240, .72);
            background: rgba(248, 250, 252, .72);
        }

        .rr-employee-tab {
            border: 1px solid var(--rr-border);
            background: #fff;
            color: var(--rr-text-muted);
            border-radius: 999px;
            padding: 8px 12px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            white-space: nowrap;
            transition: all .2s ease;
        }

        .rr-employee-tab:hover,
        .rr-employee-tab.active {
            color: var(--rr-primary-dark);
            border-color: rgba(115, 178, 212, .45);
            background: rgba(115, 178, 212, .10);
            box-shadow: 0 8px 18px rgba(115, 178, 212, .10);
        }

        .rr-employee-tab strong {
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #f1f5f9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--rr-text-dark);
            padding: 0 7px;
        }

        .rr-employee-workspace {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            min-height: 430px;
        }

        .rr-employee-report-panel.is-collapsed .rr-employee-workspace {
            grid-template-columns: 76px minmax(0, 1fr);
        }

        .rr-employee-summary-sidebar {
            border-right: 1px solid rgba(226, 232, 240, .82);
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            min-width: 0;
            transition: all .2s ease;
        }

        .rr-employee-sidebar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px;
            border-bottom: 1px solid var(--rr-border);
        }

        .rr-employee-sidebar-title {
            font-size: 12px;
            font-weight: 900;
            color: var(--rr-text-muted);
            text-transform: uppercase;
            letter-spacing: .55px;
        }

        .rr-employee-report-panel.is-collapsed .rr-employee-sidebar-title,
        .rr-employee-report-panel.is-collapsed .rr-employee-search,
        .rr-employee-report-panel.is-collapsed .rr-employee-list-meta,
        .rr-employee-report-panel.is-collapsed .rr-employee-name,
        .rr-employee-report-panel.is-collapsed .rr-employee-email,
        .rr-employee-report-panel.is-collapsed .rr-employee-counts {
            display: none;
        }

        .rr-employee-search {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(226, 232, 240, .72);
        }

        .rr-employee-search input {
            width: 100%;
            height: 40px;
            border: 1px solid var(--rr-border);
            border-radius: 13px;
            padding: 0 12px;
            outline: none;
            font-family: inherit;
            color: var(--rr-text-dark);
            background: #fff;
        }

        .rr-employee-search input:focus {
            border-color: var(--rr-primary);
            box-shadow: 0 0 0 3px rgba(115, 178, 212, .14);
        }

        .rr-employee-list-meta {
            padding: 10px 14px;
            font-size: 12px;
            color: var(--rr-text-muted);
            font-weight: 800;
        }

        .rr-employee-strip {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 520px;
            overflow-y: auto;
            padding: 0 10px 14px;
        }

        .rr-employee-card {
            background: #fff;
            border: 1px solid var(--rr-border);
            border-radius: 18px;
            padding: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
            cursor: pointer;
            text-align: left;
            width: 100%;
            font-family: inherit;
        }

        .rr-employee-card:hover,
        .rr-employee-card.active {
            transform: translateY(-1px);
            border-color: rgba(115, 178, 212, .55);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
            background: linear-gradient(135deg, #fff, #f8fcff);
        }

        .rr-employee-card-main {
            display: grid;
            grid-template-columns: 48px 1fr auto;
            gap: 11px;
            align-items: center;
        }

        .rr-employee-report-panel.is-collapsed .rr-employee-card {
            padding: 8px;
            border-radius: 16px;
        }

        .rr-employee-report-panel.is-collapsed .rr-employee-card-main {
            grid-template-columns: 42px;
            justify-content: center;
        }

        .rr-employee-photo {
            width: 48px;
            height: 48px;
            border-radius: 17px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .10);
            background: #f1f5f9;
        }

        .rr-employee-report-panel.is-collapsed .rr-employee-photo {
            width: 42px;
            height: 42px;
            border-radius: 15px;
        }

        .rr-employee-name {
            font-size: 14px;
            font-weight: 900;
            color: var(--rr-text-dark);
            line-height: 1.25;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rr-employee-email {
            font-size: 12px;
            color: var(--rr-text-muted);
            margin-top: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rr-employee-total-badge {
            min-width: 38px;
            height: 38px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
            font-size: 16px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 9px;
            box-shadow: 0 10px 20px rgba(115, 178, 212, .25);
        }

        .rr-employee-counts {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 6px;
            margin-top: 10px;
        }

        .rr-employee-count {
            border: 1px solid var(--rr-border);
            background: #f8fafc;
            border-radius: 12px;
            padding: 7px 4px;
            text-align: center;
        }

        .rr-employee-count i {
            display: block;
            margin-bottom: 3px;
            font-size: 12px;
        }

        .rr-employee-count strong {
            display: block;
            font-size: 14px;
            font-weight: 900;
            color: var(--rr-text-dark);
        }

        .rr-employee-count span {
            display: block;
            font-size: 9px;
            color: var(--rr-text-muted);
            font-weight: 900;
            text-transform: uppercase;
        }

        .rr-employee-count.ticket i {
            color: var(--rr-status-ticket-text);
        }

        .rr-employee-count.task i {
            color: var(--rr-status-task-text);
        }

        .rr-employee-count.lead i {
            color: var(--rr-status-lead-text);
        }

        .rr-employee-count.inquiry i {
            color: var(--rr-status-inquiry-text);
        }

        .rr-employee-count.appointment i {
            color: var(--rr-status-appt-text);
        }

        .rr-employee-detail {
            background: #fff;
            min-width: 0;
            padding: 18px;
        }

        .rr-employee-detail-card {
            border: 1px solid var(--rr-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(147, 194, 28, .12), transparent 30%),
                linear-gradient(135deg, #ffffff, #fbfdff);
            min-height: 100%;
            overflow: hidden;
        }

        .rr-employee-detail-empty,
        .rr-employee-strip-loading,
        .rr-employee-empty {
            padding: 26px;
            text-align: center;
            color: var(--rr-text-muted);
            font-weight: 800;
            border: 1px dashed var(--rr-border);
            border-radius: 18px;
            background: #fff;
        }

        .rr-employee-detail-hero {
            display: grid;
            grid-template-columns: 76px 1fr auto;
            gap: 16px;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--rr-border);
            background: linear-gradient(135deg, #fff, #f8fcff);
        }

        .rr-employee-detail-photo {
            width: 76px;
            height: 76px;
            border-radius: 24px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .12);
        }

        .rr-employee-detail-name {
            margin: 0;
            color: var(--rr-text-dark);
            font-size: 22px;
            font-weight: 900;
            line-height: 1.2;
        }

        .rr-employee-detail-email {
            color: var(--rr-text-muted);
            font-size: 13px;
            margin-top: 5px;
            font-weight: 700;
        }

        .rr-employee-detail-total {
            min-width: 82px;
            min-height: 82px;
            border-radius: 26px;
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 34px rgba(115, 178, 212, .28);
        }

        .rr-employee-detail-total strong {
            font-size: 30px;
            font-weight: 900;
            line-height: 1;
        }

        .rr-employee-detail-total span {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .45px;
            margin-top: 5px;
        }

        .rr-employee-detail-body {
            padding: 18px 20px 20px;
        }

        .rr-employee-summary-totals {
            display: grid;
            grid-template-columns: repeat(6, minmax(110px, 1fr));
            gap: 10px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(226, 232, 240, .72);
            background: #fff;
        }

        .rr-summary-pill {
            border: 1px solid var(--rr-border);
            background: #fff;
            border-radius: 16px;
            padding: 11px;
            display: grid;
            grid-template-columns: 34px 1fr auto;
            align-items: center;
            gap: 9px;
            color: var(--rr-text-dark);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .025);
        }

        .rr-summary-pill i {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: rgba(115, 178, 212, .14);
            color: var(--rr-primary-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .rr-summary-pill span {
            font-size: 11px;
            font-weight: 900;
            color: var(--rr-text-muted);
            text-transform: uppercase;
            letter-spacing: .45px;
        }

        .rr-summary-pill strong {
            font-size: 20px;
            font-weight: 900;
        }

        .rr-summary-pill.ticket i {
            background: var(--rr-status-ticket-bg);
            color: var(--rr-status-ticket-text);
        }

        .rr-summary-pill.task i {
            background: var(--rr-status-task-bg);
            color: var(--rr-status-task-text);
        }

        .rr-summary-pill.lead i {
            background: var(--rr-status-lead-bg);
            color: var(--rr-status-lead-text);
        }

        .rr-summary-pill.inquiry i {
            background: var(--rr-status-inquiry-bg);
            color: var(--rr-status-inquiry-text);
        }

        .rr-summary-pill.appointment i {
            background: var(--rr-status-appt-bg);
            color: var(--rr-status-appt-text);
        }

        .rr-employee-detail-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }

        .rr-employee-detail-metric {
            border: 1px solid var(--rr-border);
            background: #f8fafc;
            border-radius: 18px;
            padding: 14px;
            min-height: 110px;
        }

        .rr-employee-detail-metric i {
            width: 36px;
            height: 36px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            margin-bottom: 10px;
        }

        .rr-employee-detail-metric strong {
            display: block;
            font-size: 26px;
            color: var(--rr-text-dark);
            font-weight: 900;
            line-height: 1;
        }

        .rr-employee-detail-metric span {
            display: block;
            margin-top: 6px;
            color: var(--rr-text-muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .rr-employee-detail-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-wrap: wrap;
            padding-top: 18px;
            margin-top: 18px;
            border-top: 1px solid var(--rr-border);
        }

        @media (max-width: 1200px) {
            .rr-employee-workspace {
                grid-template-columns: 320px minmax(0, 1fr);
            }

            .rr-employee-summary-totals {
                grid-template-columns: repeat(3, minmax(120px, 1fr));
            }

            .rr-employee-detail-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {

            .rr-employee-workspace,
            .rr-employee-report-panel.is-collapsed .rr-employee-workspace {
                grid-template-columns: 1fr;
            }

            .rr-employee-summary-sidebar {
                border-right: 0;
                border-bottom: 1px solid rgba(226, 232, 240, .82);
            }

            .rr-employee-report-panel.is-collapsed .rr-employee-sidebar-title,
            .rr-employee-report-panel.is-collapsed .rr-employee-search,
            .rr-employee-report-panel.is-collapsed .rr-employee-list-meta,
            .rr-employee-report-panel.is-collapsed .rr-employee-name,
            .rr-employee-report-panel.is-collapsed .rr-employee-email,
            .rr-employee-report-panel.is-collapsed .rr-employee-counts {
                display: block;
            }

            .rr-employee-report-panel.is-collapsed .rr-employee-card-main {
                grid-template-columns: 48px 1fr auto;
            }

            .rr-employee-strip {
                max-height: 340px;
            }
        }

        @media (max-width: 768px) {
            .rr-employee-report-head {
                flex-direction: column;
            }

            .rr-employee-head-actions,
            .rr-employee-head-actions .rr-btn {
                width: 100%;
            }

            .rr-employee-summary-totals {
                grid-template-columns: repeat(2, minmax(120px, 1fr));
            }

            .rr-employee-detail-hero {
                grid-template-columns: 64px 1fr;
            }

            .rr-employee-detail-total {
                grid-column: 1 / -1;
                min-height: 64px;
                width: 100%;
                flex-direction: row;
                gap: 10px;
            }

            .rr-employee-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {

            .rr-employee-summary-totals,
            .rr-employee-detail-grid {
                grid-template-columns: 1fr;
            }

            .rr-employee-counts {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }




        /* =========================================================
                   ENTERPRISE REPORT NOTIFICATIONS INSIDE MITARBEITER VIEW
                   ========================================================= */

        .rr-employee-notification-panel {
            margin: 0 18px 16px;
            border: 1px solid rgba(115, 178, 212, .26);
            border-radius: 24px;
            background:
                radial-gradient(circle at top left, rgba(248, 172, 0, .18), transparent 30%),
                linear-gradient(135deg, #ffffff 0%, #f8fcff 100%);
            overflow: hidden;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .07);
        }

        .rr-employee-notification-head {
            padding: 16px 18px;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(226, 232, 240, .78);
            background: rgba(255, 255, 255, .70);
            backdrop-filter: blur(10px);
        }

        .rr-employee-notification-title {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .rr-employee-notification-title-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: linear-gradient(135deg, var(--rr-warning), #f97316);
            box-shadow: 0 14px 26px rgba(248, 172, 0, .24);
            flex-shrink: 0;
        }

        .rr-employee-notification-title strong {
            display: block;
            font-size: 15px;
            line-height: 1.2;
            font-weight: 900;
            color: var(--rr-text-dark);
        }

        .rr-employee-notification-title span {
            display: block;
            margin-top: 3px;
            font-size: 12px;
            font-weight: 700;
            color: var(--rr-text-muted);
            line-height: 1.35;
        }

        .rr-employee-notification-count {
            min-width: 46px;
            height: 46px;
            padding: 0 12px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--rr-warning), #f97316);
            color: #fff;
            border: 3px solid #fff;
            font-size: 18px;
            font-weight: 900;
            box-shadow: 0 14px 26px rgba(248, 172, 0, .24);
        }

        .rr-employee-notification-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(226, 232, 240, .72);
            background: #fff;
        }

        .rr-notification-stat {
            border: 1px solid var(--rr-border);
            border-radius: 18px;
            background: #f8fafc;
            padding: 12px;
            display: grid;
            grid-template-columns: 36px 1fr;
            align-items: center;
            gap: 10px;
        }

        .rr-notification-stat i {
            width: 36px;
            height: 36px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: var(--rr-primary-dark);
        }

        .rr-notification-stat strong {
            display: block;
            color: var(--rr-text-dark);
            font-size: 20px;
            font-weight: 900;
            line-height: 1;
        }

        .rr-notification-stat span {
            display: block;
            margin-top: 4px;
            color: var(--rr-text-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .rr-employee-notification-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            padding: 16px 18px 18px;
        }

        .rr-employee-notification-section {
            min-width: 0;
            border: 1px solid var(--rr-border);
            border-radius: 20px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .035);
        }

        .rr-employee-notification-section-head {
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: linear-gradient(135deg, #f8fcff, #ffffff);
            border-bottom: 1px solid var(--rr-border);
        }

        .rr-employee-notification-section-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 900;
            color: var(--rr-text-dark);
        }

        .rr-employee-notification-section-count {
            min-width: 26px;
            height: 24px;
            padding: 0 8px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(115, 178, 212, .12);
            color: var(--rr-primary-dark);
            border: 1px solid rgba(115, 178, 212, .20);
            font-size: 11px;
            font-weight: 900;
        }

        .rr-employee-notification-section-body {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 430px;
            overflow-y: auto;
        }

        .rr-employee-notification-item {
            border: 1px solid var(--rr-border);
            border-radius: 18px;
            background: #fff;
            padding: 12px;
            display: grid;
            grid-template-columns: 40px 1fr;
            align-items: start;
            gap: 11px;
            min-width: 0;
            position: relative;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
        }

        .rr-employee-notification-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
            border-color: rgba(115, 178, 212, .42);
        }

        .rr-employee-notification-item.is-new {
            border-color: rgba(248, 172, 0, .42);
            background:
                radial-gradient(circle at top left, rgba(248, 172, 0, .16), transparent 38%),
                linear-gradient(135deg, #fffaf0, #ffffff);
            box-shadow: 0 12px 28px rgba(248, 172, 0, .10);
        }

        .rr-employee-notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(115, 178, 212, .13);
            color: var(--rr-primary-dark);
        }

        .rr-employee-notification-item.is-new .rr-employee-notification-icon {
            background: rgba(248, 172, 0, .16);
            color: #9a6500;
        }

        .rr-employee-notification-main {
            min-width: 0;
        }

        .rr-employee-notification-topline {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 5px;
        }

        .rr-employee-notification-main strong {
            display: block;
            color: var(--rr-text-dark);
            font-size: 13px;
            font-weight: 900;
            line-height: 1.25;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rr-employee-notification-subtitle {
            color: var(--rr-text-muted);
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin: 5px 0 8px;
        }

        .rr-employee-notification-message {
            color: #334155;
            font-size: 12px;
            line-height: 1.45;
            background: rgba(248, 250, 252, .86);
            border: 1px solid rgba(226, 232, 240, .88);
            border-radius: 12px;
            padding: 9px 10px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .rr-employee-notification-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 9px;
            flex-wrap: wrap;
        }

        .rr-employee-notification-badge,
        .rr-notification-mini-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 4px 7px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .35px;
            background: #f1f5f9;
            color: var(--rr-text-muted);
            white-space: nowrap;
        }

        .rr-employee-notification-badge.is-new,
        .rr-notification-mini-pill.is-new {
            background: rgba(248, 172, 0, .16);
            color: #9a6500;
        }

        .rr-employee-notification-open {
            border: 1px solid rgba(115, 178, 212, .25);
            background: rgba(115, 178, 212, .10);
            color: var(--rr-primary-dark);
            border-radius: 999px;
            height: 28px;
            padding: 0 10px;
            font-size: 11px;
            font-weight: 900;
            cursor: pointer;
        }

        .rr-employee-notification-open:hover {
            background: rgba(115, 178, 212, .18);
        }

        .rr-employee-new-pill {
            position: absolute;
            top: -7px;
            right: -7px;
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--rr-warning), #f97316);
            color: #fff;
            border: 2px solid #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 900;
            box-shadow: 0 8px 18px rgba(248, 172, 0, .28);
        }

        .rr-employee-card {
            position: relative;
        }

        .rr-employee-card.has-new-report {
            border-color: rgba(248, 172, 0, .45);
            background:
                radial-gradient(circle at top left, rgba(248, 172, 0, .12), transparent 36%),
                linear-gradient(135deg, #fffaf0, #ffffff);
        }

        .rr-report-mini-card.is-new-report,
        .rr-table tr.is-new-report td {
            background:
                radial-gradient(circle at top left, rgba(248, 172, 0, .12), transparent 36%),
                linear-gradient(135deg, #fffaf0, #ffffff) !important;
        }

        .rr-report-mini-card.is-new-report {
            border-color: rgba(248, 172, 0, .45) !important;
            box-shadow: 0 10px 24px rgba(248, 172, 0, .10);
        }

        .rr-new-report-star {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: rgba(248, 172, 0, .16);
            color: #9a6500;
            margin-right: 8px;
            vertical-align: middle;
        }

        .rr-current-report-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .35px;
            background: #f1f5f9;
            color: var(--rr-text-muted);
        }

        .rr-current-report-label.is-new {
            background: rgba(248, 172, 0, .16);
            color: #9a6500;
        }

        .rr-report-mini-meta-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 7px;
            margin-top: 10px;
        }

        .rr-report-mini-meta {
            border: 1px solid var(--rr-border);
            background: #fff;
            border-radius: 11px;
            padding: 7px 8px;
            min-width: 0;
        }

        .rr-report-mini-meta span {
            display: block;
            color: var(--rr-text-muted);
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .rr-report-mini-meta strong {
            display: block;
            color: var(--rr-text-dark);
            font-size: 11px;
            font-weight: 900;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 3px;
        }

        .rr-employee-detail-alerts {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .rr-employee-detail-alert-card {
            border: 1px solid var(--rr-border);
            background: #fff;
            border-radius: 18px;
            padding: 14px;
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 11px;
            align-items: center;
        }

        .rr-employee-detail-alert-card.is-new {
            border-color: rgba(248, 172, 0, .42);
            background: linear-gradient(135deg, #fffaf0, #ffffff);
        }

        .rr-employee-detail-alert-card i {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(115, 178, 212, .13);
            color: var(--rr-primary-dark);
        }

        .rr-employee-detail-alert-card.is-new i {
            background: rgba(248, 172, 0, .16);
            color: #9a6500;
        }

        .rr-employee-detail-alert-card strong {
            display: block;
            font-size: 22px;
            font-weight: 900;
            color: var(--rr-text-dark);
            line-height: 1;
        }

        .rr-employee-detail-alert-card span {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            font-weight: 800;
            color: var(--rr-text-muted);
        }

        @media (max-width: 1200px) {

            .rr-employee-notification-stats,
            .rr-employee-notification-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {

            .rr-employee-notification-head,
            .rr-employee-notification-stats,
            .rr-employee-notification-list,
            .rr-employee-detail-alerts,
            .rr-report-mini-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <style>
        .rr-enterprise-shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .rr-enterprise-sidebar {
            position: sticky;
            top: 88px;
            min-height: calc(100vh - 120px);
            background: linear-gradient(180deg, #ffffff 0%, #f8fcff 100%);
            border: 1px solid rgba(115, 178, 212, .22);
            border-radius: 26px;
            padding: 14px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, .07);
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: width .22s ease, padding .22s ease;
        }

        .rr-enterprise-brand {
            display: grid;
            grid-template-columns: 46px 1fr;
            align-items: center;
            gap: 11px;
            padding: 10px;
            border-radius: 18px;
            background: rgba(115, 178, 212, .10);
            border: 1px solid rgba(115, 178, 212, .16);
        }

        .rr-enterprise-logo {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 10px 24px rgba(115, 178, 212, .25);
        }

        .rr-enterprise-brand-text strong {
            display: block;
            color: var(--rr-text-dark);
            font-size: 14px;
            font-weight: 900;
            line-height: 1.2;
        }

        .rr-enterprise-brand-text span {
            display: block;
            margin-top: 3px;
            color: var(--rr-text-muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .45px;
        }

        .rr-enterprise-nav {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .rr-enterprise-nav-item {
            border: 1px solid var(--rr-border);
            background: #fff;
            color: var(--rr-text-dark);
            border-radius: 18px;
            min-height: 56px;
            padding: 10px;
            display: grid;
            grid-template-columns: 40px 1fr auto;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .rr-enterprise-nav-item:hover,
        .rr-enterprise-nav-item.active {
            transform: translateY(-1px);
            border-color: rgba(115, 178, 212, .45);
            background: linear-gradient(135deg, #f8fcff, #fff);
            box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        }

        .rr-enterprise-nav-icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--rr-primary-dark);
            background: rgba(115, 178, 212, .13);
            font-size: 16px;
        }

        .rr-enterprise-nav-text {
            font-size: 14px;
            font-weight: 900;
            white-space: nowrap;
        }

        .rr-enterprise-nav-badge {
            min-width: 30px;
            height: 30px;
            border-radius: 11px;
            padding: 0 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: var(--rr-text-dark);
            font-weight: 900;
            font-size: 12px;
            border: 1px solid var(--rr-border);
        }

        .rr-enterprise-collapse {
            margin-top: auto;
            border: 1px solid var(--rr-border);
            background: #fff;
            color: var(--rr-text-muted);
            border-radius: 16px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 900;
            font-family: inherit;
            transition: all .18s ease;
        }

        .rr-enterprise-collapse:hover {
            color: var(--rr-primary-dark);
            border-color: rgba(115, 178, 212, .45);
            background: #f8fcff;
        }

        .rr-enterprise-main {
            min-width: 0;
        }

        .rr-enterprise-view[hidden] {
            display: none !important;
        }

        .rr-enterprise-view {
            animation: rrFadeUp .22s ease both;
        }

        @keyframes rrFadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .rr-view-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            background: linear-gradient(135deg, #ffffff, #f8fcff);
            border: 1px solid rgba(226, 232, 240, .95);
            border-radius: 22px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
        }

        .rr-view-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--rr-primary-dark);
            background: rgba(115, 178, 212, .12);
            border: 1px solid rgba(115, 178, 212, .18);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 8px;
        }

        .rr-view-head h2 {
            margin: 0;
            color: var(--rr-text-dark);
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .rr-view-head p {
            margin: 6px 0 0;
            color: var(--rr-text-muted);
            line-height: 1.45;
        }

        .rr-enterprise-shell.is-collapsed {
            grid-template-columns: 86px minmax(0, 1fr);
        }

        .rr-enterprise-shell.is-collapsed .rr-enterprise-sidebar {
            padding: 12px;
        }

        .rr-enterprise-shell.is-collapsed .rr-enterprise-brand {
            grid-template-columns: 1fr;
            justify-items: center;
        }

        .rr-enterprise-shell.is-collapsed .rr-enterprise-brand-text,
        .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-text,
        .rr-enterprise-shell.is-collapsed .rr-enterprise-collapse span,
        .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-badge {
            display: none;
        }

        .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-item {
            grid-template-columns: 1fr;
            justify-items: center;
            padding: 10px;
        }

        .rr-enterprise-shell.is-collapsed .rr-enterprise-collapse i {
            transform: rotate(180deg);
        }

        @media (max-width: 1100px) {

            .rr-enterprise-shell,
            .rr-enterprise-shell.is-collapsed {
                grid-template-columns: 1fr;
            }

            .rr-enterprise-sidebar {
                position: static;
                min-height: auto;
            }

            .rr-enterprise-brand,
            .rr-enterprise-shell.is-collapsed .rr-enterprise-brand {
                grid-template-columns: 46px 1fr;
                justify-items: stretch;
            }

            .rr-enterprise-brand-text,
            .rr-enterprise-shell.is-collapsed .rr-enterprise-brand-text {
                display: block;
            }

            .rr-enterprise-nav {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-item,
            .rr-enterprise-nav-item {
                grid-template-columns: 40px 1fr auto;
                justify-items: stretch;
            }

            .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-text,
            .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-badge {
                display: inline-flex;
            }

            .rr-enterprise-collapse {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .rr-enterprise-nav {
                grid-template-columns: 1fr;
            }

            .rr-view-head {
                padding: 16px;
            }
        }
    </style>

    <style>
        /* =========================================================
                            EMPLOYEE DETAIL REPORT GROUPS BY MONTH / YEAR
                            ========================================================= */

        .rr-employee-detail-metric {
            cursor: pointer;
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .rr-employee-detail-metric:hover,
        .rr-employee-detail-metric.active {
            transform: translateY(-1px);
            border-color: rgba(115, 178, 212, .55);
            box-shadow: 0 12px 26px rgba(15, 23, 42, .07);
            background: linear-gradient(135deg, #ffffff, #f8fcff);
        }

        .rr-employee-count {
            cursor: pointer;
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .rr-employee-count:hover,
        .rr-employee-count.active {
            transform: translateY(-1px);
            border-color: rgba(115, 178, 212, .55);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .06);
            background: #ffffff;
        }

        .rr-employee-report-groups {
            margin-top: 18px;
            border-top: 1px solid var(--rr-border);
            padding-top: 18px;
        }

        .rr-employee-report-group-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }

        .rr-employee-report-group-title {
            font-size: 15px;
            font-weight: 900;
            color: var(--rr-text-dark);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .rr-employee-report-group-sub {
            font-size: 12px;
            font-weight: 800;
            color: var(--rr-text-muted);
        }

        .rr-report-month-stack {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .rr-report-month {
            border: 1px solid var(--rr-border);
            border-radius: 18px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .035);
        }

        .rr-report-month[open] {
            border-color: rgba(115, 178, 212, .38);
            box-shadow: 0 12px 28px rgba(115, 178, 212, .08);
        }

        .rr-report-month-summary {
            list-style: none;
            cursor: pointer;
            padding: 13px 15px;
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #f8fcff, #ffffff);
            user-select: none;
        }

        .rr-report-month-summary::-webkit-details-marker {
            display: none;
        }

        .rr-report-month-title {
            font-size: 14px;
            font-weight: 900;
            color: var(--rr-text-dark);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .rr-report-month-count {
            min-width: 28px;
            height: 26px;
            border-radius: 999px;
            background: rgba(115, 178, 212, .14);
            color: var(--rr-primary-dark);
            border: 1px solid rgba(115, 178, 212, .22);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            padding: 0 9px;
        }

        .rr-report-month-chevron {
            width: 28px;
            height: 28px;
            border-radius: 10px;
            background: rgba(115, 178, 212, .12);
            color: var(--rr-primary-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .18s ease;
        }

        .rr-report-month[open] .rr-report-month-chevron {
            transform: rotate(180deg);
        }

        .rr-report-month-body {
            padding: 12px;
            border-top: 1px solid var(--rr-border);
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .rr-report-mini-card {
            width: 100%;
            border: 1px solid var(--rr-border);
            background: #f8fafc;
            border-radius: 15px;
            padding: 11px 12px;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
            transition: all .18s ease;
        }

        .rr-report-mini-card:hover {
            background: #ffffff;
            border-color: rgba(115, 178, 212, .45);
            transform: translateY(-1px);
        }

        .rr-report-mini-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 7px;
        }

        .rr-report-mini-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--rr-text-dark);
            line-height: 1.3;
        }

        .rr-report-mini-date {
            font-size: 11px;
            font-weight: 800;
            color: var(--rr-text-muted);
            white-space: nowrap;
        }

        .rr-report-mini-text {
            font-size: 12px;
            line-height: 1.45;
            color: var(--rr-text-muted);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>


    <style>
        /* =========================================================
                   SCALABLE MITARBEITER REPORT NOTIFICATION TABS
                   ========================================================= */
        .rr-employee-notification-panel {
            border-radius: 28px;
            border: 1px solid rgba(115, 178, 212, .28);
            background:
                radial-gradient(circle at 0 0, rgba(248, 172, 0, .18), transparent 34%),
                radial-gradient(circle at 100% 0, rgba(115, 178, 212, .16), transparent 32%),
                linear-gradient(135deg, #ffffff, #f8fcff);
        }

        .rr-employee-notification-tabs {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            padding: 16px 18px;
            background: #fff;
            border-bottom: 1px solid rgba(226, 232, 240, .82);
        }

        .rr-report-notification-tab {
            border: 1px solid var(--rr-border);
            background: #f8fafc;
            color: var(--rr-text-dark);
            border-radius: 20px;
            min-height: 76px;
            padding: 12px;
            display: grid;
            grid-template-columns: 44px 1fr auto;
            gap: 12px;
            align-items: center;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease;
        }

        .rr-report-notification-tab:hover,
        .rr-report-notification-tab.active {
            transform: translateY(-1px);
            border-color: rgba(115, 178, 212, .50);
            background: linear-gradient(135deg, #ffffff, #f8fcff);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .08);
        }

        .rr-report-notification-tab-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--rr-primary-dark);
            background: rgba(115, 178, 212, .13);
        }

        .rr-report-notification-tab[data-report-notification-tab="new"] .rr-report-notification-tab-icon,
        .rr-report-notification-tab.active[data-report-notification-tab="new"] .rr-report-notification-tab-icon {
            color: #9a6500;
            background: rgba(248, 172, 0, .18);
        }

        .rr-report-notification-tab-text strong {
            display: block;
            font-size: 14px;
            font-weight: 900;
            color: var(--rr-text-dark);
            line-height: 1.2;
        }

        .rr-report-notification-tab-text small {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            color: var(--rr-text-muted);
            font-weight: 800;
            line-height: 1.3;
        }

        .rr-report-notification-tab em {
            min-width: 34px;
            height: 34px;
            border-radius: 13px;
            background: #fff;
            border: 1px solid var(--rr-border);
            color: var(--rr-text-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            font-style: normal;
        }

        .rr-report-notification-tab.active em {
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
            border-color: transparent;
        }

        .rr-employee-notification-list {
            display: block !important;
            padding: 16px 18px 18px;
        }

        .rr-employee-notification-section {
            width: 100%;
            border-radius: 22px;
        }

        .rr-employee-notification-section-body {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
            gap: 12px;
            padding: 14px;
        }

        .rr-employee-notification-item {
            min-height: 168px;
            grid-template-columns: 42px 1fr !important;
            border-radius: 18px;
        }

        .rr-employee-notification-topline {
            gap: 10px;
        }

        .rr-employee-notification-subtitle {
            margin-top: 8px;
        }

        .rr-employee-notification-message {
            min-height: 48px;
        }

        .rr-employee-detail-notification-tabs {
            margin-bottom: 16px;
            border: 1px solid var(--rr-border);
            border-radius: 22px;
            background: #fff;
            overflow: hidden;
        }

        .rr-employee-detail-notification-tabs-head {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 12px;
            border-bottom: 1px solid var(--rr-border);
            background: #f8fafc;
        }

        .rr-employee-detail-notification-tab {
            border: 1px solid var(--rr-border);
            background: #fff;
            border-radius: 16px;
            min-height: 58px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: var(--rr-text-dark);
            cursor: pointer;
            font-family: inherit;
            font-weight: 900;
            transition: all .18s ease;
        }

        .rr-employee-detail-notification-tab:hover,
        .rr-employee-detail-notification-tab.active {
            border-color: rgba(115, 178, 212, .50);
            background: linear-gradient(135deg, #fff, #f8fcff);
            box-shadow: 0 10px 22px rgba(15, 23, 42, .06);
        }

        .rr-employee-detail-notification-tab span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .rr-employee-detail-notification-tab em {
            min-width: 28px;
            height: 28px;
            padding: 0 8px;
            border-radius: 999px;
            background: #f1f5f9;
            color: var(--rr-text-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-style: normal;
            font-size: 12px;
            font-weight: 900;
        }

        .rr-employee-detail-notification-tab.active em {
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
        }

        .rr-employee-detail-notification-tabs-body {
            padding: 12px;
        }

        .rr-employee-detail-notification-tabs-body .rr-employee-notification-section {
            border: 0;
            box-shadow: none;
        }

        .rr-employee-detail-notification-tabs-body .rr-employee-notification-section-head {
            display: none;
        }

        .rr-employee-detail-notification-tabs-body .rr-employee-notification-section-body {
            padding: 0;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }

        @media (max-width: 768px) {

            .rr-employee-notification-tabs,
            .rr-employee-detail-notification-tabs-head {
                grid-template-columns: 1fr;
            }

            .rr-report-notification-tab {
                min-height: 66px;
            }

            .rr-employee-notification-section-body,
            .rr-employee-detail-notification-tabs-body .rr-employee-notification-section-body {
                grid-template-columns: 1fr;
            }
        }
    </style>


    <style>
        /* =========================================================
                   MITARBEITER TOP NAV + NOTIFICATION TABLE LAYOUT
                   ========================================================= */
        .rr-employee-main-nav {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            padding: 16px 18px;
            background: #fff;
            border-bottom: 1px solid rgba(226, 232, 240, .78);
        }

        .rr-employee-main-tab {
            min-height: 74px;
            border: 1px solid var(--rr-border);
            border-radius: 20px;
            background: #fff;
            color: var(--rr-text-dark);
            padding: 13px;
            display: grid;
            grid-template-columns: 46px 1fr auto;
            gap: 12px;
            align-items: center;
            text-align: left;
            font-family: inherit;
            cursor: pointer;
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .rr-employee-main-tab:hover,
        .rr-employee-main-tab.active {
            transform: translateY(-1px);
            border-color: rgba(115, 178, 212, .48);
            background: linear-gradient(135deg, #f8fcff, #ffffff);
            box-shadow: 0 12px 26px rgba(15, 23, 42, .06);
        }

        .rr-employee-main-tab-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(115, 178, 212, .13);
            color: var(--rr-primary-dark);
            font-size: 17px;
        }

        .rr-employee-main-tab-icon.warning {
            background: rgba(248, 172, 0, .15);
            color: #9a6500;
        }

        .rr-employee-main-tab-text strong {
            display: block;
            color: var(--rr-text-dark);
            font-size: 14px;
            line-height: 1.15;
            font-weight: 900;
        }

        .rr-employee-main-tab-text small {
            display: block;
            margin-top: 4px;
            color: var(--rr-text-muted);
            font-size: 12px;
            line-height: 1.3;
            font-weight: 700;
        }

        .rr-employee-main-tab em {
            min-width: 34px;
            height: 34px;
            border-radius: 13px;
            background: #f1f5f9;
            color: var(--rr-text-dark);
            font-style: normal;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            border: 1px solid var(--rr-border);
        }

        .rr-employee-main-tab.active em {
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
            border-color: transparent;
            box-shadow: 0 10px 22px rgba(115, 178, 212, .20);
        }

        .rr-employee-main-pane[hidden] {
            display: none !important;
        }

        .rr-employee-notification-table-wrap {
            padding: 16px 18px 18px;
            background: #fff;
            overflow-x: auto;
        }

        .rr-notification-table-card {
            border: 1px solid var(--rr-border);
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .035);
            min-width: 920px;
        }

        .rr-notification-table-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 15px;
            background: linear-gradient(135deg, #f8fcff, #ffffff);
            border-bottom: 1px solid var(--rr-border);
        }

        .rr-notification-table-title {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--rr-text-dark);
            font-weight: 900;
            font-size: 14px;
        }

        .rr-notification-table-title i {
            color: var(--rr-primary-dark);
        }

        .rr-notification-table-count {
            min-width: 30px;
            height: 30px;
            border-radius: 12px;
            background: #f1f5f9;
            color: var(--rr-text-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 9px;
            font-size: 12px;
            font-weight: 900;
            border: 1px solid var(--rr-border);
        }

        .rr-notification-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .rr-notification-table th {
            background: #fff;
            color: var(--rr-text-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .45px;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid var(--rr-border);
            white-space: nowrap;
        }

        .rr-notification-table td {
            padding: 13px 14px;
            border-bottom: 1px solid var(--rr-border);
            vertical-align: top;
            color: var(--rr-text-dark);
            font-size: 13px;
        }

        .rr-notification-table tr:last-child td {
            border-bottom: 0;
        }

        .rr-notification-table tr.is-new {
            background: linear-gradient(90deg, rgba(248, 172, 0, .11), #fff 55%);
        }

        .rr-notification-table tr:hover td {
            background: #fcfdfe;
        }

        .rr-notification-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
            background: #f1f5f9;
            color: var(--rr-text-muted);
        }

        .rr-notification-status-pill.is-new {
            background: rgba(248, 172, 0, .16);
            color: #9a6500;
        }

        .rr-notification-ref strong {
            display: block;
            font-weight: 900;
            color: var(--rr-text-dark);
            max-width: 190px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .rr-notification-ref small {
            display: block;
            margin-top: 4px;
            color: var(--rr-text-muted);
            font-size: 11px;
            font-weight: 800;
        }

        .rr-notification-message-cell {
            min-width: 260px;
            max-width: 420px;
            color: var(--rr-text-muted);
            line-height: 1.45;
        }

        .rr-notification-open-table {
            border: 1px solid rgba(115, 178, 212, .32);
            background: rgba(115, 178, 212, .10);
            color: var(--rr-primary-dark);
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
            cursor: pointer;
        }

        .rr-notification-open-table:hover {
            background: rgba(115, 178, 212, .17);
        }

        @media (max-width: 900px) {
            .rr-employee-main-nav {
                grid-template-columns: 1fr;
            }

            .rr-employee-main-tab {
                min-height: 66px;
            }
        }
    </style>

    <style>
        /* =========================================================
               ACTIVE LOCATION STATES — SIDEBAR + MITARBEITER TOP NAV
               ========================================================= */
        .rr-enterprise-nav-item,
        .rr-employee-main-tab,
        .rr-employee-tab,
        .rr-report-notification-tab {
            position: relative;
        }

        .rr-enterprise-nav-item::before,
        .rr-employee-main-tab::before,
        .rr-employee-tab::before,
        .rr-report-notification-tab::before {
            content: "";
            position: absolute;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
        }

        .rr-enterprise-nav-item.active {
            border-color: rgba(115, 178, 212, .75) !important;
            background:
                radial-gradient(circle at top right, rgba(147, 194, 28, .18), transparent 36%),
                linear-gradient(135deg, rgba(115, 178, 212, .18), #ffffff) !important;
            box-shadow:
                0 18px 38px rgba(115, 178, 212, .18),
                inset 0 0 0 1px rgba(255, 255, 255, .85) !important;
            transform: translateY(-1px);
        }

        .rr-enterprise-nav-item.active::before {
            left: -14px;
            top: 12px;
            bottom: 12px;
            width: 5px;
            border-radius: 999px;
            background: linear-gradient(180deg, var(--rr-primary), var(--rr-secondary));
            opacity: 1;
            transform: translateX(0);
        }

        .rr-enterprise-nav-item.active .rr-enterprise-nav-icon {
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
            box-shadow: 0 12px 24px rgba(115, 178, 212, .25);
        }

        .rr-enterprise-nav-item.active .rr-enterprise-nav-text {
            color: var(--rr-primary-dark);
        }

        .rr-enterprise-nav-item.active .rr-enterprise-nav-badge {
            background: #fff;
            border-color: rgba(115, 178, 212, .45);
            color: var(--rr-primary-dark);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
        }

        .rr-enterprise-nav-item .rr-nav-current-dot,
        .rr-employee-main-tab .rr-nav-current-dot {
            display: none;
        }

        .rr-enterprise-nav-item.active .rr-nav-current-dot,
        .rr-employee-main-tab.active .rr-nav-current-dot {
            display: inline-flex;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--rr-secondary);
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .16);
            margin-left: 6px;
        }

        .rr-employee-main-tab.active {
            border-color: rgba(115, 178, 212, .78) !important;
            background:
                radial-gradient(circle at top right, rgba(115, 178, 212, .22), transparent 34%),
                linear-gradient(135deg, #ffffff, #f3fbff) !important;
            box-shadow:
                0 16px 34px rgba(115, 178, 212, .16),
                inset 0 0 0 1px rgba(255, 255, 255, .88) !important;
            transform: translateY(-1px);
        }

        .rr-employee-main-tab.active::before {
            left: 14px;
            right: 14px;
            bottom: -1px;
            height: 4px;
            border-radius: 999px 999px 0 0;
            background: linear-gradient(90deg, var(--rr-primary), var(--rr-secondary));
            opacity: 1;
        }

        .rr-employee-main-tab.active .rr-employee-main-tab-icon {
            background: linear-gradient(135deg, var(--rr-primary), var(--rr-secondary));
            color: #fff;
            box-shadow: 0 12px 24px rgba(115, 178, 212, .22);
        }

        .rr-employee-main-tab.active .rr-employee-main-tab-icon.warning {
            background: linear-gradient(135deg, var(--rr-warning), #f97316);
            color: #fff;
            box-shadow: 0 12px 24px rgba(248, 172, 0, .22);
        }

        .rr-employee-main-tab.active .rr-employee-main-tab-text strong {
            color: var(--rr-primary-dark);
        }

        .rr-employee-main-tab.active .rr-employee-main-tab-text small {
            color: #4b6478;
        }

        .rr-employee-tab.active,
        .rr-report-notification-tab.active {
            border-color: rgba(115, 178, 212, .75) !important;
            background: linear-gradient(135deg, rgba(115, 178, 212, .14), #ffffff) !important;
            color: var(--rr-primary-dark) !important;
            box-shadow: 0 10px 22px rgba(115, 178, 212, .13) !important;
        }

        .rr-employee-tab.active::before,
        .rr-report-notification-tab.active::before {
            left: 12px;
            right: 12px;
            bottom: -1px;
            height: 3px;
            border-radius: 999px 999px 0 0;
            background: linear-gradient(90deg, var(--rr-primary), var(--rr-secondary));
            opacity: 1;
        }

        .rr-current-view-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-top: 8px;
            padding: 7px 10px;
            border-radius: 999px;
            color: var(--rr-primary-dark);
            background: rgba(115, 178, 212, .12);
            border: 1px solid rgba(115, 178, 212, .22);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-item.active::before {
            left: -10px;
        }

        .rr-enterprise-shell.is-collapsed .rr-enterprise-nav-item.active {
            box-shadow: 0 12px 26px rgba(115, 178, 212, .16) !important;
        }

        @media (max-width: 768px) {
            .rr-enterprise-nav-item.active::before {
                left: 10px;
                top: auto;
                bottom: -1px;
                width: auto;
                right: 10px;
                height: 4px;
            }

            .rr-current-view-label {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $customerOptions = collect($customers ?? [])->map(function ($customer) {
            $name = trim(($customer->firma ?? '') ?: (($customer->lastname ?? '') . ' ' . ($customer->name ?? '')));
            return [
                'id' => $customer->id ?? null,
                'text' => $name ?: ('Kunde #' . ($customer->id ?? '')),
                'sub' => !empty($customer->customer_no) ? ('Kundennr. #' . $customer->customer_no) : ('ID #' . ($customer->id ?? '')),
                'type' => 'customer',
            ];
        })->filter(fn($x) => !empty($x['id']))->values();

        $employeeOptions = collect($employees ?? [])->map(function ($employee) {
            $name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
            return [
                'id' => $employee->id ?? null,
                'text' => $name ?: ('Mitarbeiter #' . ($employee->id ?? '')),
                'sub' => 'Mitarbeiter-ID #' . ($employee->id ?? ''),
                'type' => 'employee',
            ];
        })->filter(fn($x) => !empty($x['id']))->values();

        $productOptions = collect($products ?? [])->map(function ($product) {
            return [
                'id' => $product->id ?? null,
                'text' => $product->article_group ?? ('Produkt #' . ($product->id ?? '')),
                'sub' => !empty($product->initial) ? ('Kürzel: ' . $product->initial) : ('Produkt-ID #' . ($product->id ?? '')),
                'type' => 'product',
            ];
        })->filter(fn($x) => !empty($x['id']))->values();

        $departmentOptions = collect($departments ?? [])->map(function ($department) {
            $name = $department->name
                ?? $department->department
                ?? $department->department_name
                ?? ('Abteilung #' . ($department->id ?? ''));

            return [
                'id' => $department->id ?? null,
                'text' => $name,
                'sub' => 'Abteilungs-ID #' . ($department->id ?? ''),
                'type' => 'department',
            ];
        })->filter(fn($x) => !empty($x['id']))->values();

        $smartSearchSeed = collect()
            ->merge($employeeOptions)
            ->merge($customerOptions)
            ->merge($productOptions)
            ->merge($departmentOptions)
            ->values();
    @endphp

    <div class="rr-wrap">
        <div class="rr-container">
            <div class="rr-page-header">
                <div>
                    <div class="rr-page-kicker">
                        <i class="fa-solid fa-filter-circle-dollar"></i>
                        Smart Berichte
                    </div>
                    <h1 class="rr-page-title">Berichte Übersicht</h1>
                    <p class="rr-page-subtitle">
                        Suchen und filtern Sie Berichte nach Mitarbeiter, Kunde, Produkt, Abteilung, Typ und Zeitraum.
                        Die Hauptsuche erkennt auch Teilnamen und schlägt passende Einträge vor.
                    </p>
                </div>

                <div class="rr-page-actions">
                    <button class="rr-btn rr-btn-light" type="button" id="rr-refresh">
                        <i class="fa-solid fa-rotate"></i>
                        Aktualisieren
                    </button>

                    <button class="rr-btn rr-btn-danger-soft" type="button" id="rr-clear-all">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                        Filter löschen
                    </button>
                </div>
            </div>

            <div class="rr-enterprise-shell" id="rrEnterpriseShell">
                <aside class="rr-enterprise-sidebar" id="rrEnterpriseSidebar" aria-label="Berichte Navigation">
                    <div class="rr-enterprise-brand">
                        <div class="rr-enterprise-logo">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div class="rr-enterprise-brand-text">
                            <strong>Report Center</strong>
                            <span>Enterprise Workspace</span>
                        </div>
                    </div>

                    <nav class="rr-enterprise-nav">
                        <button type="button" class="rr-enterprise-nav-item active" data-enterprise-view="reports">
                            <i class="fa-solid fa-file-lines"></i>
                            <span>Berichte</span>
                            <strong id="rrNavReportsBadge" class="rr-nav-badge">0</strong>
                        </button>

                        <button type="button" class="rr-enterprise-nav-item" data-enterprise-view="employees">
                            <i class="fa-solid fa-users"></i>
                            <span>Mitarbeiter</span>
                        </button>
                    </nav>

                    <button type="button" class="rr-enterprise-collapse" id="rrEnterpriseSidebarToggle">
                        <i class="fa-solid fa-angles-left"></i>
                        <span>Sidebar einklappen</span>
                    </button>
                </aside>

                <main class="rr-enterprise-main">
                    <section class="rr-enterprise-view active" id="rrReportsView" data-enterprise-panel="reports">
                        <div class="rr-view-head">
                            <div>
                                <div class="rr-view-kicker"><i class="fa-solid fa-file-lines"></i> Berichtszentrale</div>
                                <h2>Berichte</h2>
                                <p>Filter, prüfen und öffnen Sie alle aktuellen Berichte aus Termin, Aufgabe, Ticket, Lead
                                    und Anfrage.</p>
                            </div>
                        </div>
                        <div class="rr-stats-grid">
                            <div class="rr-stat-card">
                                <div class="rr-stat-header">
                                    <div>
                                        <div class="rr-stat-value" id="rr-stat-total">—</div>
                                        <div class="rr-stat-label">Treffer gesamt</div>
                                    </div>
                                    <div class="rr-stat-icon"
                                        style="background: var(--rr-primary-light); color: var(--rr-primary);">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </div>
                                </div>
                                <div class="rr-small rr-text-muted rr-fw-medium">Aktueller Filter</div>
                            </div>

                            <div class="rr-stat-card">
                                <div class="rr-stat-header">
                                    <div>
                                        <div class="rr-stat-value" id="rr-stat-inquiry">—</div>
                                        <div class="rr-stat-label">Anfragen</div>
                                    </div>
                                    <div class="rr-stat-icon"
                                        style="background: var(--rr-status-inquiry-bg); color: var(--rr-status-inquiry-text);">
                                        <i class="fa-solid fa-envelope-open-text"></i>
                                    </div>
                                </div>
                                <div class="rr-small rr-text-muted rr-fw-medium">Diese Seite</div>
                            </div>

                            <div class="rr-stat-card">
                                <div class="rr-stat-header">
                                    <div>
                                        <div class="rr-stat-value" id="rr-stat-appointment">—</div>
                                        <div class="rr-stat-label">Termine</div>
                                    </div>
                                    <div class="rr-stat-icon"
                                        style="background: var(--rr-status-appt-bg); color: var(--rr-status-appt-text);">
                                        <i class="fa-regular fa-calendar-check"></i>
                                    </div>
                                </div>
                                <div class="rr-small rr-text-muted rr-fw-medium">Diese Seite</div>
                            </div>

                            <div class="rr-stat-card">
                                <div class="rr-stat-header">
                                    <div>
                                        <div class="rr-stat-value" id="rr-stat-lead">—</div>
                                        <div class="rr-stat-label">Leads</div>
                                    </div>
                                    <div class="rr-stat-icon"
                                        style="background: var(--rr-secondary-light); color: #5a7d0c;">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                                <div class="rr-small rr-text-muted rr-fw-medium">Diese Seite</div>
                            </div>
                        </div>

                        <div class="rr-main-card">

                            <div class="rr-filter-bar">
                                <div class="rr-filter-grid">
                                    <div class="rr-filter-group rr-col-12 rr-smart-search-box">
                                        <label class="rr-filter-label">Smart Suche</label>
                                        <div class="rr-input-wrapper">
                                            <i class="fa-solid fa-search rr-input-icon"></i>
                                            <input id="rr-q" type="text" class="rr-form-control rr-has-icon"
                                                autocomplete="off"
                                                placeholder="Kunde, Mitarbeiter, Produkt, Abteilung, Bericht, Ticket, Anfrage ...">
                                        </div>

                                        <div class="rr-smart-suggestions" id="rr-smart-suggestions"></div>
                                    </div>

                                    <div class="rr-filter-group rr-col-3">
                                        <label class="rr-filter-label">Typ</label>
                                        <select id="rr-type" class="rr-form-select rr-smart-select"
                                            data-placeholder="Alle Typen">
                                            <option value="">Alle Typen</option>
                                            <option value="inquiry" data-icon="fa-envelope-open-text"
                                                data-sub="Anfrageberichte">Anfrage
                                            </option>
                                            <option value="task" data-icon="fa-tasks" data-sub="Aufgabenberichte">Aufgabe
                                            </option>
                                            <option value="appointment" data-icon="fa-calendar-day"
                                                data-sub="Terminberichte">Termin
                                            </option>
                                            <option value="ticket" data-icon="fa-life-ring" data-sub="Ticketberichte">Ticket
                                            </option>
                                            <option value="lead" data-icon="fa-star" data-sub="Lead/Kundenberichte">Lead
                                            </option>
                                        </select>
                                    </div>

                                    <div class="rr-filter-group rr-col-3 rr-select2-wrap">
                                        <label class="rr-filter-label">Mitarbeiter</label>
                                        <select id="rr-employee" class="rr-form-select rr-smart-select"
                                            data-placeholder="Alle Mitarbeiter" data-smart-type="employee">
                                            <option value="">Alle Mitarbeiter</option>
                                            @foreach($employeeOptions as $e)
                                                <option value="{{ $e['id'] }}" data-sub="{{ $e['sub'] }}" data-type="employee"
                                                    data-icon="fa-user-pen">
                                                    {{ $e['text'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="rr-select2-badge">Suche</span>
                                    </div>

                                    <div class="rr-filter-group rr-col-3 rr-select2-wrap">
                                        <label class="rr-filter-label">Kunde</label>
                                        <select id="rr-customer" class="rr-form-select rr-smart-select"
                                            data-placeholder="Alle Kunden" data-smart-type="customer">
                                            <option value="">Alle Kunden</option>
                                            @foreach($customerOptions as $customer)
                                                <option value="{{ $customer['id'] }}" data-sub="{{ $customer['sub'] }}"
                                                    data-type="customer" data-icon="fa-building-user">
                                                    {{ $customer['text'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="rr-select2-badge">Suche</span>
                                    </div>

                                    <div class="rr-filter-group rr-col-3 rr-select2-wrap">
                                        <label class="rr-filter-label">Produkt</label>
                                        <select id="rr-product" class="rr-form-select rr-smart-select"
                                            data-placeholder="Alle Produkte" data-smart-type="product">
                                            <option value="">Alle Produkte</option>
                                            @foreach($productOptions as $product)
                                                <option value="{{ $product['id'] }}" data-sub="{{ $product['sub'] }}"
                                                    data-type="product" data-icon="fa-box">
                                                    {{ $product['text'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="rr-select2-badge">Suche</span>
                                    </div>

                                    <div class="rr-filter-group rr-col-3 rr-select2-wrap">
                                        <label class="rr-filter-label">Abteilung</label>
                                        <select id="rr-department" class="rr-form-select rr-smart-select"
                                            data-placeholder="Alle Abteilungen" data-smart-type="department">
                                            <option value="">Alle Abteilungen</option>
                                            @foreach($departmentOptions as $department)
                                                <option value="{{ $department['id'] }}" data-sub="{{ $department['sub'] }}"
                                                    data-type="department" data-icon="fa-sitemap">
                                                    {{ $department['text'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="rr-select2-badge">Suche</span>
                                    </div>

                                    <div class="rr-filter-group rr-col-3">
                                        <label class="rr-filter-label">Von</label>
                                        <input id="rr-from" type="date" class="rr-form-control">
                                    </div>

                                    <div class="rr-filter-group rr-col-3">
                                        <label class="rr-filter-label">Bis</label>
                                        <input id="rr-to" type="date" class="rr-form-control">
                                    </div>

                                    <div class="rr-filter-group rr-col-3">
                                        <label class="rr-filter-label">Aktion</label>
                                        <button id="rr-reset" class="rr-btn rr-btn-light w-100" type="button">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            Reset
                                        </button>
                                    </div>
                                </div>

                                <div class="rr-active-filters" id="rr-active-filters"></div>
                            </div>

                            <div class="rr-table-wrapper">
                                <table class="rr-table">
                                    <thead>
                                        <tr>
                                            <th style="padding-left: 2rem;">Typ</th>
                                            <th>Betreff / Referenz</th>
                                            <th>Mitarbeiter</th>
                                            <th>Datum</th>
                                            <th>Letzter Bericht</th>
                                            <th class="rr-text-right" style="padding-right: 2rem;">Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rr-tbody">
                                        <tr>
                                            <td colspan="6" class="rr-text-center"
                                                style="padding: 2rem; color: var(--rr-text-muted); text-align:center;">
                                                Lade Daten...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="rr-table-footer">
                                <div class="rr-text-muted rr-small rr-fw-medium" id="rr-meta">Lade Daten...</div>

                                <div class="rr-flex-row rr-gap-2" style="justify-content: flex-end;">
                                    <button class="rr-btn rr-btn-light rr-btn-sm" id="rr-prev" type="button">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Zurück
                                    </button>

                                    <button class="rr-btn rr-btn-light rr-btn-sm" id="rr-next" type="button">
                                        Weiter
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rr-enterprise-view" id="rrEmployeesView" data-enterprise-panel="employees" hidden>

                        <div class="rr-employee-report-panel" id="rrEmployeeReportPanel">
                            <div class="rr-employee-report-head">
                                <div>
                                    <div class="rr-page-kicker">
                                        <i class="fa-solid fa-users-viewfinder"></i>
                                        Enterprise Report Control
                                    </div>
                                    <h2>Offene Berichte nach Mitarbeiter</h2>
                                    <p>Übersicht, Priorisierung und Detailansicht der offenen Berichte je Mitarbeiter.</p>
                                </div>

                                <div class="rr-employee-head-actions">
                                    <button class="rr-btn rr-btn-light" type="button" id="rr-employee-summary-collapse">
                                        <i class="fa-solid fa-table-columns"></i>
                                        Sidebar einklappen
                                    </button>
                                    <button class="rr-btn rr-btn-light" type="button" id="rr-employee-summary-refresh">
                                        <i class="fa-solid fa-rotate"></i>
                                        Neu laden
                                    </button>
                                </div>
                            </div>

                            <div class="rr-employee-main-nav" id="rrEmployeeMainNav">
                                <button type="button" class="rr-employee-main-tab active" data-employee-main-tab="overview"
                                    aria-current="page" aria-pressed="true">
                                    <span class="rr-employee-main-tab-icon"><i
                                            class="fa-solid fa-users-viewfinder"></i></span>
                                    <span class="rr-employee-main-tab-text">
                                        <strong>Mitarbeiter Übersicht <i class="rr-nav-current-dot"></i></strong>
                                        <small>Offene Berichte nach Typ, Mitarbeiter und Zuständigkeit</small>
                                    </span>
                                    <em id="rrEmployeeOverviewBadge">—</em>
                                </button>

                                <button type="button" class="rr-employee-main-tab" data-employee-main-tab="notifications"
                                    aria-current="false" aria-pressed="false">
                                    <span class="rr-employee-main-tab-icon warning"><i class="fa-solid fa-bell"></i></span>
                                    <span class="rr-employee-main-tab-text">
                                        <strong>Report-Benachrichtigungen <i class="rr-nav-current-dot"></i></strong>
                                        <small>Neue und aktuelle Reports als eigene Tabelle</small>
                                    </span>
                                    <em id="rrEmployeeNotificationsBadge">0</em>
                                </button>
                            </div>

                            <div class="rr-employee-main-pane" id="rrEmployeeOverviewPane" data-employee-pane="overview">
                                <div class="rr-employee-summary-totals" id="rrEmployeeSummaryTotals">
                                    <div class="rr-summary-pill">
                                        <i class="fa-solid fa-layer-group"></i>
                                        <span>Gesamt</span>
                                        <strong>—</strong>
                                    </div>
                                    <div class="rr-summary-pill ticket">
                                        <i class="fa-solid fa-life-ring"></i>
                                        <span>Tickets</span>
                                        <strong>—</strong>
                                    </div>
                                    <div class="rr-summary-pill task">
                                        <i class="fa-solid fa-tasks"></i>
                                        <span>Aufgaben</span>
                                        <strong>—</strong>
                                    </div>
                                    <div class="rr-summary-pill lead">
                                        <i class="fa-solid fa-star"></i>
                                        <span>Leads</span>
                                        <strong>—</strong>
                                    </div>
                                    <div class="rr-summary-pill inquiry">
                                        <i class="fa-solid fa-envelope-open-text"></i>
                                        <span>Anfragen</span>
                                        <strong>—</strong>
                                    </div>
                                    <div class="rr-summary-pill appointment">
                                        <i class="fa-solid fa-calendar-day"></i>
                                        <span>Termine</span>
                                        <strong>—</strong>
                                    </div>
                                </div>

                                <div class="rr-employee-summary-tabs" id="rrEmployeeSummaryTabs">
                                    <button type="button" class="rr-employee-tab active" data-summary-type="all">
                                        <i class="fa-solid fa-layer-group"></i> Alle <strong>—</strong>
                                    </button>
                                    <button type="button" class="rr-employee-tab" data-summary-type="new">
                                        <i class="fa-solid fa-star"></i> Neu <strong>—</strong>
                                    </button>
                                    <button type="button" class="rr-employee-tab" data-summary-type="ticket">
                                        <i class="fa-solid fa-life-ring"></i> Tickets <strong>—</strong>
                                    </button>
                                    <button type="button" class="rr-employee-tab" data-summary-type="task">
                                        <i class="fa-solid fa-tasks"></i> Aufgaben <strong>—</strong>
                                    </button>
                                    <button type="button" class="rr-employee-tab" data-summary-type="lead">
                                        <i class="fa-solid fa-star"></i> Leads <strong>—</strong>
                                    </button>
                                    <button type="button" class="rr-employee-tab" data-summary-type="inquiry">
                                        <i class="fa-solid fa-envelope-open-text"></i> Anfragen <strong>—</strong>
                                    </button>
                                    <button type="button" class="rr-employee-tab" data-summary-type="appointment">
                                        <i class="fa-solid fa-calendar-day"></i> Termine <strong>—</strong>
                                    </button>
                                </div>

                                <div class="rr-employee-workspace">
                                    <aside class="rr-employee-summary-sidebar">
                                        <div class="rr-employee-sidebar-top">
                                            <div class="rr-employee-sidebar-title">Mitarbeiter</div>
                                            <button class="rr-btn-icon" type="button" id="rr-employee-summary-collapse-mini"
                                                title="Sidebar einklappen">
                                                <i class="fa-solid fa-angles-left"></i>
                                            </button>
                                        </div>

                                        <div class="rr-employee-search">
                                            <input type="search" id="rrEmployeeSummarySearch"
                                                placeholder="Mitarbeiter suchen...">
                                        </div>

                                        <div class="rr-employee-list-meta" id="rrEmployeeSummaryMeta">Lade Mitarbeiter...
                                        </div>

                                        <div class="rr-employee-strip" id="rrEmployeeReportStrip">
                                            <div class="rr-employee-strip-loading">
                                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                                Lade Mitarbeiterübersicht...
                                            </div>
                                        </div>
                                    </aside>

                                    <section class="rr-employee-detail" id="rrEmployeeSummaryDetail">
                                        <div class="rr-employee-detail-empty">
                                            <i class="fa-solid fa-user-check"></i>
                                            Wählen Sie links einen Mitarbeiter aus, um die Details zu sehen.
                                        </div>
                                    </section>
                                </div>
                            </div>

                            <div class="rr-employee-main-pane" id="rrEmployeeNotificationsPane"
                                data-employee-pane="notifications" hidden>
                                <div class="rr-employee-notification-panel" id="rrEmployeeNotificationPanel">
                                    <div class="rr-employee-notification-head">
                                        <div class="rr-employee-notification-title">
                                            <span class="rr-employee-notification-title-icon">
                                                <i class="fa-solid fa-bell"></i>
                                            </span>
                                            <div>
                                                <strong>Report-Benachrichtigungen <i
                                                        class="rr-nav-current-dot"></i></strong>
                                                <span>Neue und aktuelle Reports sind getrennt, filterbar und als Tabelle
                                                    dargestellt.</span>
                                            </div>
                                        </div>
                                        <div class="rr-employee-notification-count" id="rrEmployeeNotificationCount">0</div>
                                    </div>

                                    <div class="rr-employee-notification-stats" id="rrEmployeeNotificationStats">
                                        <div class="rr-notification-stat">
                                            <i class="fa-solid fa-bell"></i>
                                            <div><strong>0</strong><span>Neue</span></div>
                                        </div>
                                        <div class="rr-notification-stat">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                            <div><strong>0</strong><span>Aktuell</span></div>
                                        </div>
                                        <div class="rr-notification-stat">
                                            <i class="fa-solid fa-users"></i>
                                            <div><strong>0</strong><span>Mitarbeiter</span></div>
                                        </div>
                                        <div class="rr-notification-stat">
                                            <i class="fa-solid fa-layer-group"></i>
                                            <div><strong>0</strong><span>Gesamt</span></div>
                                        </div>
                                    </div>

                                    <div class="rr-employee-notification-tabs" id="rrEmployeeNotificationTabs">
                                        <button type="button" class="rr-report-notification-tab active"
                                            data-report-notification-tab="new">
                                            <span class="rr-report-notification-tab-icon"><i
                                                    class="fa-solid fa-bell"></i></span>
                                            <span class="rr-report-notification-tab-text">
                                                <strong>Neue Reports</strong>
                                                <small>Ungelesene Benachrichtigungen</small>
                                            </span>
                                            <em>0</em>
                                        </button>

                                        <button type="button" class="rr-report-notification-tab"
                                            data-report-notification-tab="current">
                                            <span class="rr-report-notification-tab-icon"><i
                                                    class="fa-solid fa-clock-rotate-left"></i></span>
                                            <span class="rr-report-notification-tab-text">
                                                <strong>Aktuelle Reports</strong>
                                                <small>Bereits geladene / gelesene Reports</small>
                                            </span>
                                            <em>0</em>
                                        </button>
                                    </div>

                                    <div class="rr-employee-notification-table-wrap" id="rrEmployeeNotificationList">
                                        <div class="rr-employee-empty" style="grid-column:1/-1; margin:0;">
                                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                                            Lade Report-Benachrichtigungen...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </section>
                </main>
            </div>
        </div>
    </div>

    {{-- Custom Sidebar --}}
    <div class="rr-sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="rr-sidebar" id="rrSidebar">
        <div class="rr-sidebar-header">
            <div>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.25rem;">Bericht hinzufügen</h3>
                <small class="rr-text-muted">Erstellen Sie einen neuen Eintrag.</small>
            </div>

            <button class="rr-close-btn" id="sidebarCloseBtn" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="rr-sidebar-body">
            <form id="rrAddForm">
                @csrf

                <input type="hidden" name="type" id="rrAddType">
                <input type="hidden" name="id" id="rrAddEntityId">

                <div class="rr-highlight-box">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-link" style="color:var(--rr-secondary);"></i>
                        <span class="rr-small rr-fw-bold"
                            style="text-transform: uppercase; color:var(--rr-secondary);">Referenz</span>
                    </div>

                    <div id="rrAddRef" class="rr-fw-bold" style="font-size: 1.1rem; color: var(--rr-text-dark);">...</div>
                </div>

                <div class="rr-filter-group rr-mb-4">
                    <label class="rr-filter-label">Vorhandene Berichte</label>
                    <div id="rrExistingReports" class="rr-existing-box">
                        <div class="rr-text-muted rr-small">Wählen Sie einen Eintrag…</div>
                    </div>
                </div>

                <div class="rr-filter-group rr-mb-4">
                    <label class="rr-filter-label">Bericht</label>
                    <textarea name="report" id="rrAddReport" class="rr-form-control" rows="8" required
                        placeholder="Was ist passiert? Schreiben Sie hier..." style="padding: 1rem;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="rr-mb-4">
                    <div class="rr-filter-group">
                        <label class="rr-filter-label">Datum</label>
                        <input type="date" name="report_date" id="rrAddReportDate" class="rr-form-control">
                    </div>

                    <div class="rr-filter-group">
                        <label class="rr-filter-label">Wiedervorlage</label>
                        <input type="date" name="due_date" id="rrAddDueDate" class="rr-form-control">
                    </div>
                </div>

                <div class="rr-filter-group rr-mb-4">
                    <label class="rr-filter-label">Status Update</label>
                    <div class="rr-radio-group">
                        <div class="rr-radio-option">
                            <input type="radio" name="status" id="st1" value="unchanged" checked>
                            <label for="st1" class="rr-radio-label">Unverändert</label>
                        </div>

                        <div class="rr-radio-option">
                            <input type="radio" name="status" id="st2" value="done">
                            <label for="st2" class="rr-radio-label">Erledigt</label>
                        </div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 2rem;">
                    <button type="submit" class="rr-btn rr-btn-primary-soft w-100" id="rrAddSubmit"
                        style="padding: 0.8rem;">
                        <i class="fa-solid fa-save"></i>
                        Speichern bestätigen
                    </button>

                    <button type="button" class="rr-btn rr-btn-light w-100" id="sidebarCancelBtn">
                        Abbrechen
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Error Modal --}}
    <div class="rr-error-backdrop" id="rrErrorBackdrop"></div>

    <div class="rr-error-modal" id="rrErrorModal" role="dialog" aria-modal="true" aria-labelledby="rrErrorTitle">
        <div class="rr-error-card">
            <div class="rr-error-header">
                <div class="rr-error-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div>
                    <h3 id="rrErrorTitle">Bericht konnte nicht gespeichert werden</h3>
                    <p id="rrErrorSubtitle">Bitte prüfen Sie die Angaben und versuchen Sie es erneut.</p>
                </div>

                <button type="button" class="rr-error-close" id="rrErrorCloseBtn">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="rr-error-body">
                <div class="rr-error-message" id="rrErrorMessage">
                    Unbekannter Fehler.
                </div>

                <div class="rr-error-list" id="rrErrorList" style="display:none;"></div>
            </div>

            <div class="rr-error-footer">
                <button type="button" class="rr-btn rr-btn-light" id="rrErrorCancelBtn">
                    Schließen
                </button>

                <button type="button" class="rr-btn rr-btn-primary-soft" id="rrErrorOkBtn">
                    Verstanden
                </button>
            </div>
        </div>
    </div>

    {{-- Report Detail / Source Modal --}}
    <div class="rr-detail-modal-backdrop" id="rrReportDetailBackdrop"></div>

    <div class="rr-detail-modal" id="rrReportDetailModal" role="dialog" aria-modal="true">
        <div class="rr-detail-card">
            <div class="rr-detail-header">
                <div class="rr-detail-icon" id="rrReportDetailIcon">
                    <i class="fa-solid fa-file-lines"></i>
                </div>

                <div>
                    <h3 class="rr-detail-title" id="rrReportDetailTitle">Bericht</h3>
                    <p class="rr-detail-sub" id="rrReportDetailSub">Herkunft und Details dieses Berichts.</p>
                </div>

                <button type="button" class="rr-detail-close" id="rrReportDetailClose">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="rr-detail-body">
                <div class="rr-detail-grid" id="rrReportDetailGrid"></div>

                <div class="rr-detail-report-box">
                    <div class="rr-detail-report-label">
                        <i class="fa-solid fa-align-left"></i>
                        Vollständiger Bericht
                    </div>

                    <div class="rr-detail-report-text" id="rrReportDetailText"></div>
                </div>
            </div>

            <div class="rr-detail-footer">
                <button type="button" class="rr-btn rr-btn-light" id="rrReportDetailCancel">Schließen</button>

                <a href="#" target="_blank" rel="noopener" class="rr-btn rr-btn-primary-soft" id="rrReportDetailOpen">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    Datensatz öffnen
                </a>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const API = {
                fetch: "{{ route('admin.overdue-center.reports.fetch') }}",
                store: "{{ route('admin.overdue-center.report.store') }}",
                recordReports: "{{ route('admin.recent-reports.record-reports') }}",
                sourceDetails: "{{ route('admin.recent-reports.source-details') }}",
                employeeSummary: "{{ route('admin.recent-reports.employee-summary') }}",
                reportNotifications: "{{ route('admin.overdue.reports.notifications') }}",
            };

            const SMART_SEED = @json($smartSearchSeed);

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const els = {
                q: document.getElementById('rr-q'),
                suggestions: document.getElementById('rr-smart-suggestions'),
                type: document.getElementById('rr-type'),
                employee: document.getElementById('rr-employee'),
                customer: document.getElementById('rr-customer'),
                product: document.getElementById('rr-product'),
                department: document.getElementById('rr-department'),
                from: document.getElementById('rr-from'),
                to: document.getElementById('rr-to'),
                reset: document.getElementById('rr-reset'),
                refresh: document.getElementById('rr-refresh'),
                clearAll: document.getElementById('rr-clear-all'),
                activeFilters: document.getElementById('rr-active-filters'),
                employeeSummaryRefresh: document.getElementById('rr-employee-summary-refresh'),
                employeeSummaryCollapse: document.getElementById('rr-employee-summary-collapse'),
                employeeSummaryCollapseMini: document.getElementById('rr-employee-summary-collapse-mini'),
                employeeSummaryPanel: document.getElementById('rrEmployeeReportPanel'),
                employeeSummaryTabs: document.getElementById('rrEmployeeSummaryTabs'),
                employeeSummarySearch: document.getElementById('rrEmployeeSummarySearch'),
                employeeSummaryMeta: document.getElementById('rrEmployeeSummaryMeta'),
                employeeReportStrip: document.getElementById('rrEmployeeReportStrip'),
                employeeSummaryTotals: document.getElementById('rrEmployeeSummaryTotals'),
                employeeSummaryDetail: document.getElementById('rrEmployeeSummaryDetail'),
                employeeNotificationPanel: document.getElementById('rrEmployeeNotificationPanel'),
                employeeNotificationCount: document.getElementById('rrEmployeeNotificationCount'),
                employeeNotificationList: document.getElementById('rrEmployeeNotificationList'),
                employeeNotificationStats: document.getElementById('rrEmployeeNotificationStats'),
                employeeNotificationTabs: document.getElementById('rrEmployeeNotificationTabs'),
                employeeMainNav: document.getElementById('rrEmployeeMainNav'),
                employeeOverviewPane: document.getElementById('rrEmployeeOverviewPane'),
                employeeNotificationsPane: document.getElementById('rrEmployeeNotificationsPane'),
                employeeOverviewBadge: document.getElementById('rrEmployeeOverviewBadge'),
                employeeNotificationsBadge: document.getElementById('rrEmployeeNotificationsBadge'),

                tbody: document.getElementById('rr-tbody'),
                meta: document.getElementById('rr-meta'),
                prev: document.getElementById('rr-prev'),
                next: document.getElementById('rr-next'),

                statTotal: document.getElementById('rr-stat-total'),
                statInquiry: document.getElementById('rr-stat-inquiry'),
                statAppointment: document.getElementById('rr-stat-appointment'),
                statLead: document.getElementById('rr-stat-lead'),

                sidebar: document.getElementById('rrSidebar'),
                backdrop: document.getElementById('sidebarBackdrop'),
                closeBtn: document.getElementById('sidebarCloseBtn'),
                cancelBtn: document.getElementById('sidebarCancelBtn'),

                addForm: document.getElementById('rrAddForm'),
                addType: document.getElementById('rrAddType'),
                addId: document.getElementById('rrAddEntityId'),
                addRef: document.getElementById('rrAddRef'),
                existingReports: document.getElementById('rrExistingReports'),
                addReport: document.getElementById('rrAddReport'),
                addReportDate: document.getElementById('rrAddReportDate'),
                addDueDate: document.getElementById('rrAddDueDate'),
                addSubmit: document.getElementById('rrAddSubmit'),

                errorBackdrop: document.getElementById('rrErrorBackdrop'),
                errorModal: document.getElementById('rrErrorModal'),
                errorTitle: document.getElementById('rrErrorTitle'),
                errorSubtitle: document.getElementById('rrErrorSubtitle'),
                errorMessage: document.getElementById('rrErrorMessage'),
                errorList: document.getElementById('rrErrorList'),
                errorCloseBtn: document.getElementById('rrErrorCloseBtn'),
                errorCancelBtn: document.getElementById('rrErrorCancelBtn'),
                errorOkBtn: document.getElementById('rrErrorOkBtn'),

                reportDetailBackdrop: document.getElementById('rrReportDetailBackdrop'),
                reportDetailModal: document.getElementById('rrReportDetailModal'),
                reportDetailClose: document.getElementById('rrReportDetailClose'),
                reportDetailCancel: document.getElementById('rrReportDetailCancel'),
                reportDetailTitle: document.getElementById('rrReportDetailTitle'),
                reportDetailSub: document.getElementById('rrReportDetailSub'),
                reportDetailIcon: document.getElementById('rrReportDetailIcon'),
                reportDetailGrid: document.getElementById('rrReportDetailGrid'),
                reportDetailText: document.getElementById('rrReportDetailText'),
                reportDetailOpen: document.getElementById('rrReportDetailOpen'),

                enterpriseShell: document.getElementById('rrEnterpriseShell'),
                enterpriseSidebarToggle: document.getElementById('rrEnterpriseSidebarToggle'),
                enterpriseNavItems: document.querySelectorAll('[data-enterprise-view]'),
                reportsView: document.getElementById('rrReportsView'),
                employeesView: document.getElementById('rrEmployeesView'),
                navReportsBadge: document.getElementById('rrNavReportsBadge'),
                navEmployeesBadge: document.getElementById('rrNavEmployeesBadge'),
            };

            const state = {
                page: 1,
                perPage: 20,
                total: 0,
                hasMore: false,
                loading: false,
                timer: null,
                suggestionIndex: -1,
                currentRows: [],
                employeeSummaryItems: [],
                employeeSummaryActiveType: 'all',
                employeeSummarySelectedId: null,
                employeeSummarySearch: '',

                employeeSummaryDetailType: 'all',
                employeeSummaryDetailRows: [],
                employeeSummaryDetailLoading: false,
                reportNotifications: [],
                unreadReportKeys: {},
                unreadReportsByEmployee: {},
                employeeNotificationTab: 'new',
                employeeDetailNotificationTab: 'new',
                employeeMainTab: 'overview',
            };

            function escapeHtml(s) {
                return String(s ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }


            function ensureCurrentLocationLabel() {
                let label = document.getElementById('rrCurrentLocationLabel');
                if (label) return label;

                const header = document.querySelector('.rr-page-header .rr-page-subtitle') || document.querySelector('.rr-page-header');
                if (!header) return null;

                label = document.createElement('div');
                label.id = 'rrCurrentLocationLabel';
                label.className = 'rr-current-view-label';
                label.innerHTML = '<i class="fa-solid fa-location-dot"></i><span>Berichte</span>';

                header.insertAdjacentElement('afterend', label);
                return label;
            }

            function updateCurrentLocationLabel() {
                const label = ensureCurrentLocationLabel();
                if (!label) return;

                const main = state.enterpriseView === 'employees' ? 'Mitarbeiter' : 'Berichte';
                const sub = state.enterpriseView === 'employees'
                    ? (state.employeeMainTab === 'notifications' ? 'Report-Benachrichtigungen' : 'Mitarbeiter Übersicht')
                    : 'Reportliste';

                label.innerHTML = `
                        <i class="fa-solid fa-location-dot"></i>
                        <span>${escapeHtml(main)} / ${escapeHtml(sub)}</span>
                    `;
            }

            function switchEnterpriseView(view) {
                const target = view === 'employees' ? 'employees' : 'reports';
                state.enterpriseView = target;

                els.enterpriseNavItems?.forEach(btn => {
                    const isActive = btn.getAttribute('data-enterprise-view') === target;
                    btn.classList.toggle('active', isActive);
                    btn.setAttribute('aria-current', isActive ? 'page' : 'false');
                    btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                if (els.reportsView) {
                    els.reportsView.hidden = target !== 'reports';
                    els.reportsView.classList.toggle('active', target === 'reports');
                }

                if (els.employeesView) {
                    els.employeesView.hidden = target !== 'employees';
                    els.employeesView.classList.toggle('active', target === 'employees');
                }

                updateCurrentLocationLabel();

                if (target === 'employees' && !state.employeeSummaryItems.length) {
                    loadEmployeeReportSummary();
                    loadReportNotifications();
                }
            }

            function switchEmployeeMainTab(tab) {
                const target = tab === 'notifications' ? 'notifications' : 'overview';
                state.employeeMainTab = target;

                els.employeeMainNav?.querySelectorAll('[data-employee-main-tab]').forEach(btn => {
                    const isActive = btn.getAttribute('data-employee-main-tab') === target;
                    btn.classList.toggle('active', isActive);
                    btn.setAttribute('aria-current', isActive ? 'page' : 'false');
                    btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                if (els.employeeOverviewPane) {
                    els.employeeOverviewPane.hidden = target !== 'overview';
                    els.employeeOverviewPane.classList.toggle('active', target === 'overview');
                }

                if (els.employeeNotificationsPane) {
                    els.employeeNotificationsPane.hidden = target !== 'notifications';
                    els.employeeNotificationsPane.classList.toggle('active', target === 'notifications');
                }

                updateCurrentLocationLabel();

                if (target === 'notifications') {
                    renderEmployeeNotificationPanel();
                }
            }

            function updateEnterpriseNavBadges() {
                // Only Berichte nav should show report count
                if (els.navReportsBadge) {
                    els.navReportsBadge.textContent = Number(state.total || 0);
                    els.navReportsBadge.hidden = Number(state.total || 0) <= 0;
                }

                // Mitarbeiter nav should NOT show report badge
                if (els.navEmployeesBadge) {
                    els.navEmployeesBadge.textContent = '';
                    els.navEmployeesBadge.hidden = true;
                    els.navEmployeesBadge.style.display = 'none';
                }
            }


            function reportEntityKey(type, id) {
                return `${String(type || '').toLowerCase()}:${String(id || '')}`;
            }

            function rowEntityId(row) {
                return row?.target_id || row?.entity_id || row?.id || row?.source_id || '';
            }

            function isNewReport(row) {
                const key = reportEntityKey(row?.type, rowEntityId(row));
                return !!state.unreadReportKeys[key];
            }

            function newReportCountForEmployee(employeeId) {
                return Number(state.unreadReportsByEmployee[String(employeeId || '')] || 0);
            }

            function notificationTargetId(item) {
                return item?.target_id || item?.entity_id || item?.item_id || item?.source_id || item?.id || '';
            }

            function notificationEntityKey(item) {
                return reportEntityKey(item?.type, notificationTargetId(item));
            }

            function notificationEmployeeId(item) {
                return String(item?.employee_id || item?.report_by || item?.report_by_id || '');
            }

            function notificationEmployeeName(item) {
                const direct = item?.employee || item?.employee_name || item?.report_by_name || '';
                if (direct) return direct;

                const empId = notificationEmployeeId(item);
                const emp = (state.employeeSummaryItems || []).find(row => String(row.id) === String(empId));
                return emp?.name || 'Unbekannt';
            }

            function notificationTitle(item) {
                const b = badgeMeta(item?.type || 'report');
                return item?.title
                    || item?.target_label
                    || item?.ref_title
                    || item?.target_no
                    || `${b.label} #${notificationTargetId(item) || ''}`.trim();
            }

            function notificationReportText(item) {
                return item?.report || item?.report_text || item?.message || item?.subtitle || item?.target_status || '';
            }

            function normalizeNotificationToRow(item) {
                const b = badgeMeta(item?.type || 'report');
                const targetId = notificationTargetId(item);

                return {
                    ...item,
                    id: targetId || item?.id,
                    entity_id: targetId,
                    target_id: targetId,
                    report_id: item?.id || item?.report_id || '',
                    type: item?.type || 'report',
                    title: notificationTitle(item),
                    ref_title: notificationTitle(item),
                    target_label: item?.target_label || b.label,
                    target_no: item?.target_no || (targetId ? `#${targetId}` : ''),
                    report: notificationReportText(item),
                    report_text: notificationReportText(item),
                    employee_name: notificationEmployeeName(item),
                    created_at: item?.created_at || item?.performed_at || item?.report_date || '',
                    report_date: item?.report_date || item?.performed_at || item?.created_at || '',
                    status: item?.target_status || item?.status || '',
                    priority: item?.target_priority || item?.priority || '',
                    link: item?.target_link || item?.link || '#',
                    is_unread: !!item?.is_unread,
                };
            }

            async function loadReportNotifications() {
                if (!API.reportNotifications) return;

                try {
                    const res = await fetch(API.reportNotifications, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await res.json().catch(() => ({}));
                    const items = Array.isArray(data.items) ? data.items : [];

                    state.reportNotifications = items;
                    state.unreadReportKeys = {};
                    state.unreadReportsByEmployee = {};

                    items.forEach(item => {
                        if (!item?.is_unread) return;

                        const key = notificationEntityKey(item);
                        if (key && key !== ':') {
                            state.unreadReportKeys[key] = true;
                        }

                        const empId = notificationEmployeeId(item);
                        if (empId) {
                            state.unreadReportsByEmployee[empId] = Number(state.unreadReportsByEmployee[empId] || 0) + 1;
                        }
                    });

                    renderEmployeeNotificationPanel();
                    renderEmployeeSummaryTabs();
                    renderEmployeeSidebar();
                    renderEmployeeSummaryDetail();

                    if (state.currentRows?.length) {
                        render(state.currentRows, {
                            total: state.total,
                            has_more: state.hasMore,
                        });
                    }
                } catch (error) {
                    console.error('Report notifications failed:', error);
                    renderEmployeeNotificationPanel(true);
                }
            }

            function renderEmployeeNotificationStats(items) {
                if (!els.employeeNotificationStats) return;

                const unread = (items || []).filter(item => item.is_unread).length;
                const current = Math.max(0, (items || []).length - unread);
                const employeeIds = new Set((items || []).map(notificationEmployeeId).filter(Boolean));

                const values = [unread, current, employeeIds.size, (items || []).length];
                els.employeeNotificationStats.querySelectorAll('.rr-notification-stat strong').forEach((el, index) => {
                    el.textContent = Number(values[index] || 0);
                });
            }

            function renderEmployeeNotificationPanel(failed = false) {
                if (!els.employeeNotificationList) return;

                const allItems = (state.reportNotifications || []);
                const unreadItems = allItems.filter(item => item.is_unread);
                const currentItems = allItems.filter(item => !item.is_unread);
                const activeTab = state.employeeNotificationTab || 'new';
                const selectedItems = activeTab === 'current' ? currentItems : unreadItems;
                const isNewSection = activeTab !== 'current';

                if (els.employeeNotificationCount) {
                    els.employeeNotificationCount.textContent = String(unreadItems.length || 0);
                    els.employeeNotificationCount.title = `${unreadItems.length} neue Report-Benachrichtigungen`;
                }

                if (els.employeeNotificationsBadge) {
                    els.employeeNotificationsBadge.textContent = String(unreadItems.length || allItems.length || 0);
                    els.employeeNotificationsBadge.title = `${unreadItems.length} neu · ${currentItems.length} aktuell`;
                }

                renderEmployeeNotificationStats(allItems);
                renderEmployeeNotificationTabs(unreadItems.length, currentItems.length);

                if (failed) {
                    els.employeeNotificationList.innerHTML = `
                                <div class="rr-employee-empty" style="grid-column:1/-1; margin:0;">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Report-Benachrichtigungen konnten nicht geladen werden.
                                </div>
                            `;
                    return;
                }

                if (!allItems.length) {
                    els.employeeNotificationList.innerHTML = `
                                <div class="rr-employee-empty" style="grid-column:1/-1; margin:0;">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Keine Report-Benachrichtigungen gefunden.
                                </div>
                            `;
                    return;
                }

                els.employeeNotificationList.innerHTML = renderNotificationSection(
                    isNewSection ? 'Neue Reports' : 'Aktuelle Reports',
                    isNewSection ? 'fa-bell' : 'fa-clock-rotate-left',
                    selectedItems,
                    isNewSection,
                    24
                );
            }

            function renderEmployeeNotificationTabs(unreadCount, currentCount) {
                if (!els.employeeNotificationTabs) return;

                els.employeeNotificationTabs.querySelectorAll('[data-report-notification-tab]').forEach(btn => {
                    const tab = btn.getAttribute('data-report-notification-tab') || 'new';
                    const value = tab === 'current' ? currentCount : unreadCount;
                    btn.classList.toggle('active', (state.employeeNotificationTab || 'new') === tab);

                    const countEl = btn.querySelector('em');
                    if (countEl) countEl.textContent = Number(value || 0);
                });
            }

            function renderNotificationSection(title, icon, items, isNewSection, limit = 50) {
                const visibleItems = (items || []).slice(0, limit);
                const hiddenCount = Math.max(0, Number((items || []).length) - Number(visibleItems.length));

                return `
                            <div class="rr-notification-table-card">
                                <div class="rr-notification-table-head">
                                    <div class="rr-notification-table-title">
                                        <i class="fa-solid ${escapeHtml(icon)}"></i>
                                        ${escapeHtml(title)}
                                    </div>
                                    <div class="rr-notification-table-count">${Number((items || []).length)}</div>
                                </div>

                                ${visibleItems.length ? `
                                    <table class="rr-notification-table">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Typ</th>
                                                <th>Referenz</th>
                                                <th>Mitarbeiter</th>
                                                <th>Datum</th>
                                                <th>Bericht</th>
                                                <th>Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${visibleItems.map(item => renderNotificationTableRow(item)).join('')}
                                        </tbody>
                                    </table>
                                ` : `
                                    <div class="rr-employee-empty" style="margin:16px; padding:22px;">
                                        <i class="fa-solid ${isNewSection ? 'fa-circle-check' : 'fa-file-circle-check'}"></i>
                                        ${isNewSection ? 'Keine neuen Reports.' : 'Keine aktuellen Reports.'}
                                    </div>
                                `}

                                ${hiddenCount ? `
                                    <div class="rr-employee-empty" style="margin:16px; padding:18px;">
                                        ${hiddenCount} weitere Einträge ausgeblendet. Nutzen Sie den Mitarbeiter-Filter oder die Suche für mehr Details.
                                    </div>
                                ` : ''}
                            </div>
                        `;
            }

            function renderNotificationTableRow(item) {
                const b = badgeMeta(item.type || 'report');
                const isUnread = !!item.is_unread;
                const title = notificationTitle(item);
                const message = notificationReportText(item);
                const targetNo = item.target_no || (notificationTargetId(item) ? `#${notificationTargetId(item)}` : '—');
                const employeeName = notificationEmployeeName(item);
                const date = formatDateDisplay(item.created_at || item.performed_at || item.report_date || '');
                const index = state.reportNotifications.findIndex(row => String(row.id) === String(item.id));

                return `
                            <tr class="${isUnread ? 'is-new' : ''}">
                                <td>
                                    <span class="rr-notification-status-pill ${isUnread ? 'is-new' : ''}">
                                        <i class="fa-solid ${escapeHtml(isUnread ? 'fa-star' : 'fa-clock')}"></i>
                                        ${isUnread ? 'Neu' : 'Aktuell'}
                                    </span>
                                </td>
                                <td>
                                    <span class="rr-status-badge ${escapeHtml(b.class || '')}">
                                        <i class="fa-solid ${escapeHtml(b.icon)}"></i>
                                        ${escapeHtml(b.label)}
                                    </span>
                                </td>
                                <td>
                                    <div class="rr-notification-ref">
                                        <strong>${escapeHtml(title)}</strong>
                                        <small>${escapeHtml(targetNo)}</small>
                                    </div>
                                </td>
                                <td>${escapeHtml(employeeName)}</td>
                                <td>${escapeHtml(date)}</td>
                                <td class="rr-notification-message-cell">
                                    ${escapeHtml(shortReportText(message, 160) || 'Kein Berichtstext vorhanden.')}
                                </td>
                                <td>
                                    <button type="button" class="rr-notification-open-table" data-notification-index="${index}">
                                        Details öffnen
                                    </button>
                                </td>
                            </tr>
                        `;
            }

            function renderNotificationCard(item) {
                return renderNotificationTableRow(item);
            }

            async function loadEmployeeReportSummary() {
                if (!els.employeeReportStrip || !API.employeeSummary) return;

                els.employeeReportStrip.innerHTML = `
                                            <div class="rr-employee-strip-loading">
                                                <i class="fa-solid fa-circle-notch fa-spin"></i>
                                                Lade Mitarbeiterübersicht...
                                            </div>
                                        `;

                try {
                    const res = await fetch(API.employeeSummary, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok || !data.success) {
                        throw new Error(data?.message || 'Mitarbeiterübersicht konnte nicht geladen werden.');
                    }

                    renderEmployeeSummaryTotals(data.totals || {});
                    renderEmployeeReportStrip(data.items || []);
                    updateEnterpriseNavBadges();
                    loadReportNotifications();

                } catch (error) {
                    els.employeeReportStrip.innerHTML = `
                                                <div class="rr-employee-empty">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                    ${escapeHtml(error?.message || 'Fehler beim Laden.')}
                                                </div>
                                            `;
                }
            }


            function renderEmployeeSummaryTotals(totals) {
                if (!els.employeeSummaryTotals) return;

                const map = [
                    ['total', 'Gesamt'],
                    ['ticket', 'Tickets'],
                    ['task', 'Aufgaben'],
                    ['lead', 'Leads'],
                    ['inquiry', 'Anfragen'],
                    ['appointment', 'Termine'],
                ];

                if (els.employeeOverviewBadge) {
                    els.employeeOverviewBadge.textContent = Number(totals.total || 0);
                    els.employeeOverviewBadge.title = `${Number(totals.total || 0)} offene Berichte gesamt`;
                }

                map.forEach(([key]) => {
                    const pill = els.employeeSummaryTotals.querySelector(`.rr-summary-pill.${key}`) ||
                        (key === 'total' ? els.employeeSummaryTotals.querySelector('.rr-summary-pill') : null);

                    if (!pill) return;

                    const strong = pill.querySelector('strong');
                    if (strong) {
                        strong.textContent = Number(totals[key] || 0);
                    }
                });
            }

            function renderEmployeeReportStrip(items) {
                if (!els.employeeReportStrip) return;

                state.employeeSummaryItems = Array.isArray(items) ? items : [];

                if (!state.employeeSummarySelectedId && state.employeeSummaryItems.length) {
                    state.employeeSummarySelectedId = String(state.employeeSummaryItems[0].id || '');
                }

                renderEmployeeSummaryTabs();
                renderEmployeeSidebar();
                renderEmployeeSummaryDetail();
            }

            function renderEmployeeSummaryTabs() {
                if (!els.employeeSummaryTabs) return;

                const totals = calculateEmployeeSummaryTotals(state.employeeSummaryItems);
                const labels = {
                    all: 'Alle',
                    new: 'Neue Berichte',
                    ticket: 'Tickets',
                    task: 'Aufgaben',
                    lead: 'Leads',
                    inquiry: 'Anfragen',
                    appointment: 'Termine',
                };

                els.employeeSummaryTabs.querySelectorAll('.rr-employee-tab').forEach(tab => {
                    const type = tab.getAttribute('data-summary-type') || 'all';
                    tab.classList.toggle('active', type === state.employeeSummaryActiveType);
                    const strong = tab.querySelector('strong');
                    if (strong) strong.textContent = Number(totals[type] || 0);
                    tab.title = `${labels[type] || type}: ${Number(totals[type] || 0)} offene Berichte`;
                });
            }

            function calculateEmployeeSummaryTotals(items) {
                const totals = { all: 0, new: 0, ticket: 0, task: 0, lead: 0, inquiry: 0, appointment: 0 };

                (items || []).forEach(item => {
                    const counts = item.counts || {};
                    totals.ticket += Number(counts.ticket || 0);
                    totals.task += Number(counts.task || 0);
                    totals.lead += Number(counts.lead || 0);
                    totals.inquiry += Number(counts.inquiry || 0);
                    totals.appointment += Number(counts.appointment || 0);
                    totals.new += newReportCountForEmployee(item.id);
                    totals.all += Number(item.total || 0);
                });

                return totals;
            }

            function filteredEmployeeSummaryItems() {
                const type = state.employeeSummaryActiveType || 'all';
                const query = normalizeText(state.employeeSummarySearch || '');

                return (state.employeeSummaryItems || []).filter(item => {
                    const counts = item.counts || {};
                    const matchesType = type === 'all'
                        ? Number(item.total || 0) > 0
                        : (type === 'new' ? newReportCountForEmployee(item.id) > 0 : Number(counts[type] || 0) > 0);
                    const matchesQuery = !query || normalizeText(`${item.name || ''} ${item.email || ''}`).includes(query);
                    return matchesType && matchesQuery;
                });
            }

            function renderEmployeeSidebar() {
                if (!els.employeeReportStrip) return;

                const items = filteredEmployeeSummaryItems();
                const type = state.employeeSummaryActiveType || 'all';

                if (els.employeeSummaryMeta) {
                    const label = type === 'all' ? 'alle Typen' : typeLabelForEmployeeSummary(type);
                    els.employeeSummaryMeta.textContent = `${items.length} Mitarbeiter · ${label}`;
                }

                if (!items.length) {
                    els.employeeReportStrip.innerHTML = `
                                                <div class="rr-employee-empty">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    Keine offenen Berichte in dieser Ansicht.
                                                </div>
                                            `;
                    if (!state.employeeSummarySelectedId) renderEmployeeSummaryDetail();
                    return;
                }

                if (!items.some(item => String(item.id) === String(state.employeeSummarySelectedId))) {
                    state.employeeSummarySelectedId = String(items[0].id || '');
                }

                els.employeeReportStrip.innerHTML = items.map(item => {
                    const counts = item.counts || {};
                    const selected = String(item.id) === String(state.employeeSummarySelectedId);
                    const newCount = newReportCountForEmployee(item.id);

                    return `
                                                <button type="button" class="rr-employee-card ${selected ? 'active' : ''} ${newCount > 0 ? 'has-new-report' : ''}" data-employee-id="${escapeAttr(item.id)}" title="Details für ${escapeAttr(item.name || 'Mitarbeiter')}">
                                                    ${newCount > 0 ? `<span class="rr-employee-new-pill" title="Neue Report-Benachrichtigungen">${newCount}</span>` : ''}
                                                    <div class="rr-employee-card-main">
                                                        <img class="rr-employee-photo"
                                                             src="${escapeAttr(item.image || '')}"
                                                             alt="${escapeAttr(item.name || 'Mitarbeiter')}"
                                                             onerror="this.src='{{ asset('images/gender/male.png') }}'">

                                                        <div>
                                                            <div class="rr-employee-name">${escapeHtml(item.name || 'Mitarbeiter')}</div>
                                                            <div class="rr-employee-email">${escapeHtml(item.email || '—')}</div>
                                                        </div>

                                                        <div class="rr-employee-total-badge" title="Offene Berichte gesamt">
                                                            ${Number(item.total || 0)}
                                                        </div>
                                                    </div>

                                                    <div class="rr-employee-counts">
                                                        ${employeeCountBadge('ticket', 'fa-life-ring', 'Ticket', counts.ticket)}
                                                        ${employeeCountBadge('task', 'fa-tasks', 'Aufgabe', counts.task)}
                                                        ${employeeCountBadge('lead', 'fa-star', 'Lead', counts.lead)}
                                                        ${employeeCountBadge('inquiry', 'fa-envelope-open-text', 'Anfrage', counts.inquiry)}
                                                        ${employeeCountBadge('appointment', 'fa-calendar-day', 'Termin', counts.appointment)}
                                                    </div>
                                                </button>
                                            `;
                }).join('');
            }

            function monthYearKey(value) {
                const raw = value || '';
                const date = new Date(String(raw).replace(' ', 'T'));

                if (Number.isNaN(date.getTime())) {
                    return {
                        key: 'unknown',
                        label: 'Ohne Datum',
                        sort: 0,
                    };
                }

                const year = date.getFullYear();
                const month = date.getMonth();

                return {
                    key: `${year}-${String(month + 1).padStart(2, '0')}`,
                    label: date.toLocaleDateString('de-DE', {
                        month: 'long',
                        year: 'numeric',
                    }),
                    sort: year * 100 + month,
                };
            }

            function groupEmployeeReportsByMonth(rows) {
                const grouped = {};

                (rows || []).forEach(row => {
                    const dateValue = row.report_date || row.created_at || row.performed_at || row.date;
                    const meta = monthYearKey(dateValue);

                    if (!grouped[meta.key]) {
                        grouped[meta.key] = {
                            label: meta.label,
                            sort: meta.sort,
                            items: [],
                        };
                    }

                    grouped[meta.key].items.push(row);
                });

                return Object.values(grouped).sort((a, b) => b.sort - a.sort);
            }

            function employeeReportTypeMeta(type) {
                const map = {
                    all: {
                        icon: 'fa-layer-group',
                        label: 'Alle Berichte',
                    },
                    new: {
                        icon: 'fa-star',
                        label: 'Neue Berichte',
                    },
                    ticket: {
                        icon: 'fa-life-ring',
                        label: 'Tickets',
                    },
                    task: {
                        icon: 'fa-tasks',
                        label: 'Aufgaben',
                    },
                    lead: {
                        icon: 'fa-star',
                        label: 'Leads',
                    },
                    inquiry: {
                        icon: 'fa-envelope-open-text',
                        label: 'Anfragen',
                    },
                    appointment: {
                        icon: 'fa-calendar-day',
                        label: 'Termine',
                    },
                };

                return map[type] || map.all;
            }

            function renderEmployeeGroupedReports(rows, type) {
                const meta = employeeReportTypeMeta(type);
                const groups = groupEmployeeReportsByMonth(rows);

                if (state.employeeSummaryDetailLoading) {
                    return `
                                            <div class="rr-employee-report-groups">
                                                <div class="rr-employee-detail-empty">
                                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                                    Daten werden geladen...
                                                </div>
                                            </div>
                                        `;
                }

                if (!groups.length) {
                    return `
                                            <div class="rr-employee-report-groups">
                                                <div class="rr-employee-detail-empty">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    Keine ${escapeHtml(meta.label)} für diesen Mitarbeiter gefunden.
                                                </div>
                                            </div>
                                        `;
                }

                return `
                                        <div class="rr-employee-report-groups">
                                            <div class="rr-employee-report-group-head">
                                                <div>
                                                    <div class="rr-employee-report-group-title">
                                                        <i class="fa-solid ${escapeHtml(meta.icon)}"></i>
                                                        ${escapeHtml(meta.label)}
                                                    </div>
                                                    <div class="rr-employee-report-group-sub">
                                                        ${Number(rows.length || 0)} Einträge nach Monat und Jahr gruppiert
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rr-report-month-stack">
                                                ${groups.map((group, groupIndex) => `
                                                    <details class="rr-report-month" ${groupIndex === 0 ? 'open' : ''}>
                                                        <summary class="rr-report-month-summary">
                                                            <span class="rr-report-month-title">
                                                                <i class="fa-solid fa-calendar-days"></i>
                                                                ${escapeHtml(group.label)}
                                                            </span>

                                                            <span class="rr-report-month-count">${group.items.length}</span>

                                                            <span class="rr-report-month-chevron">
                                                                <i class="fa-solid fa-chevron-down"></i>
                                                            </span>
                                                        </summary>

                                                        <div class="rr-report-month-body">
                                                            ${group.items.map(row => renderEmployeeMiniReport(row)).join('')}
                                                        </div>
                                                    </details>
                                                `).join('')}
                                            </div>
                                        </div>
                                    `;
            }

            function renderEmployeeMiniReport(row) {
                const index = state.employeeSummaryDetailRows.findIndex(item => String(item.id) === String(row.id));
                const b = badgeMeta(row.type || 'report');
                const title = row.ref_title || row.title || row.target_label || row.customer_name || row.name || 'Bericht';
                const date = row.report_date || row.created_at || row.performed_at || row.date || '';
                const report = row.report_text || row.report || row.description || row.note || '';
                const employee = row.employee_name || row.report_by_name || row.employee || '—';
                const status = row.status || row.target_status || row.status_raw || '—';
                const priority = row.priority || row.target_priority || row.priority_raw || '—';
                const targetNo = row.target_no || row.entity_id || row.target_id || row.id || '—';
                const isNew = isNewReport(row);

                return `
                                <button
                                    type="button"
                                    class="rr-report-mini-card ${isNew ? 'is-new-report' : ''}"
                                    data-employee-report-index="${index}"
                                >
                                    <div class="rr-report-mini-top">
                                        <div class="rr-report-mini-title">
                                            ${isNew ? `<span class="rr-new-report-star"><i class="fa-solid fa-star"></i></span>` : ''}
                                            ${escapeHtml(title)}
                                        </div>
                                        <div class="rr-report-mini-date">${escapeHtml(formatDateDisplay(date))}</div>
                                    </div>

                                    <div class="rr-report-mini-text">
                                        ${escapeHtml(shortReportText(report, 260))}
                                    </div>

                                    <div class="rr-report-mini-meta-grid">
                                        <div class="rr-report-mini-meta">
                                            <span>Typ</span>
                                            <strong><i class="fa-solid ${escapeHtml(b.icon)}"></i> ${escapeHtml(b.label)}</strong>
                                        </div>
                                        <div class="rr-report-mini-meta">
                                            <span>Referenz</span>
                                            <strong>${escapeHtml(String(targetNo).startsWith('#') ? targetNo : '#' + targetNo)}</strong>
                                        </div>
                                        <div class="rr-report-mini-meta">
                                            <span>Status</span>
                                            <strong>${escapeHtml(status)}</strong>
                                        </div>
                                        <div class="rr-report-mini-meta">
                                            <span>Priorität</span>
                                            <strong>${escapeHtml(priority)}</strong>
                                        </div>
                                    </div>

                                    <div class="rr-employee-notification-footer">
                                        <span class="rr-current-report-label ${isNew ? 'is-new' : ''}">
                                            <i class="fa-solid ${isNew ? 'fa-star' : 'fa-clock'}"></i>
                                            ${isNew ? 'Neuer Report' : 'Aktueller Report'}
                                        </span>
                                        <span class="rr-notification-mini-pill">
                                            <i class="fa-solid fa-user-pen"></i>
                                            ${escapeHtml(employee)}
                                        </span>
                                    </div>
                                </button>
                            `;
            }

            async function loadEmployeeDetailReports(type = 'all') {
                const employeeId = state.employeeSummarySelectedId || '';

                if (!employeeId) {
                    state.employeeSummaryDetailRows = [];
                    renderEmployeeSummaryDetail();
                    return;
                }

                state.employeeSummaryDetailType = type || 'all';
                state.employeeSummaryDetailLoading = true;
                state.employeeSummaryDetailRows = [];

                renderEmployeeSidebar();
                renderEmployeeSummaryDetail();

                try {
                    const url = new URL(API.fetch, window.location.origin);

                    url.searchParams.set('employee_id', employeeId);
                    url.searchParams.set('sort', 'newest');
                    url.searchParams.set('page', '1');
                    url.searchParams.set('per_page', '500');

                    if (state.employeeSummaryDetailType !== 'all' && state.employeeSummaryDetailType !== 'new') {
                        url.searchParams.set('type', state.employeeSummaryDetailType);
                    }

                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        throw new Error(data?.message || 'Mitarbeiterdaten konnten nicht geladen werden.');
                    }

                    const rows = data.rows || data.items || [];
                    state.employeeSummaryDetailRows = state.employeeSummaryDetailType === 'new'
                        ? rows.filter(row => isNewReport(row))
                        : rows;
                } catch (error) {
                    console.error('Employee detail reports failed:', error);
                    state.employeeSummaryDetailRows = [];
                } finally {
                    state.employeeSummaryDetailLoading = false;
                    renderEmployeeSidebar();
                    renderEmployeeSummaryDetail();
                }
            }

            function renderEmployeeSummaryDetail() {
                if (!els.employeeSummaryDetail) return;

                const item = (state.employeeSummaryItems || []).find(row => {
                    return String(row.id) === String(state.employeeSummarySelectedId);
                });

                if (!item) {
                    els.employeeSummaryDetail.innerHTML = `
                                    <div class="rr-employee-detail-empty">
                                        <i class="fa-solid fa-user-check"></i>
                                        Wählen Sie links einen Mitarbeiter aus, um die Details zu sehen.
                                    </div>
                                `;
                    return;
                }

                const counts = item.counts || {};
                const activeType = state.employeeSummaryDetailType || state.employeeSummaryActiveType || 'all';
                const employeeNewCount = newReportCountForEmployee(item.id);
                const employeeNotifications = notificationsForEmployee(item.id);
                const employeeCurrentCount = employeeNotifications.filter(n => !n.is_unread).length;

                els.employeeSummaryDetail.innerHTML = `
                                <div class="rr-employee-detail-card">
                                    <div class="rr-employee-detail-hero">
                                        <img class="rr-employee-detail-photo"
                                             src="${escapeAttr(item.image || '')}"
                                             alt="${escapeAttr(item.name || 'Mitarbeiter')}"
                                             onerror="this.src='{{ asset('images/gender/male.png') }}'">

                                        <div>
                                            <h3 class="rr-employee-detail-name">${escapeHtml(item.name || 'Mitarbeiter')}</h3>
                                            <div class="rr-employee-detail-email">${escapeHtml(item.email || 'Keine E-Mail hinterlegt')}</div>
                                        </div>

                                        <div class="rr-employee-detail-total">
                                            <strong>${Number(item.total || 0)}</strong>
                                            <span>offen</span>
                                        </div>
                                    </div>

                                    <div class="rr-employee-detail-body">
                                        <div class="rr-employee-detail-alerts">
                                            <div class="rr-employee-detail-alert-card is-new">
                                                <i class="fa-solid fa-bell"></i>
                                                <div>
                                                    <strong>${Number(employeeNewCount || 0)}</strong>
                                                    <span>Neue Report-Benachrichtigungen für diesen Mitarbeiter</span>
                                                </div>
                                            </div>
                                            <div class="rr-employee-detail-alert-card">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                                <div>
                                                    <strong>${Number(employeeCurrentCount || 0)}</strong>
                                                    <span>Aktuelle/geladene Report-Benachrichtigungen</span>
                                                </div>
                                            </div>
                                        </div>

                                        ${renderEmployeeDetailNotificationPreview(item.id)}

                                        <div class="rr-employee-detail-grid">
                                            ${employeeDetailMetric('ticket', 'fa-life-ring', 'Tickets', counts.ticket)}
                                            ${employeeDetailMetric('task', 'fa-tasks', 'Aufgaben', counts.task)}
                                            ${employeeDetailMetric('lead', 'fa-star', 'Leads', counts.lead)}
                                            ${employeeDetailMetric('inquiry', 'fa-envelope-open-text', 'Anfragen', counts.inquiry)}
                                            ${employeeDetailMetric('appointment', 'fa-calendar-day', 'Termine', counts.appointment)}
                                        </div>

                                        <div class="rr-employee-detail-actions">
                                            <button type="button" class="rr-btn rr-btn-light" data-employee-detail-load-type="all">
                                                <i class="fa-solid fa-layer-group"></i>
                                                Alle laden
                                            </button>

                                            <button type="button" class="rr-btn rr-btn-light" data-employee-detail-load-type="new">
                                                <i class="fa-solid fa-star"></i>
                                                Neue Berichte
                                            </button>

                                            <button type="button" class="rr-btn rr-btn-primary-soft" data-employee-detail-filter="${escapeAttr(activeType)}">
                                                <i class="fa-solid fa-filter"></i>
                                                In Hauptliste anzeigen
                                            </button>
                                        </div>

                                        ${renderEmployeeGroupedReports(state.employeeSummaryDetailRows, activeType)}
                                    </div>
                                </div>
                            `;
            }

            function notificationsForEmployee(employeeId) {
                const empId = String(employeeId || '');
                return (state.reportNotifications || []).filter(item => notificationEmployeeId(item) === empId);
            }

            function renderEmployeeDetailNotificationPreview(employeeId) {
                const allItems = notificationsForEmployee(employeeId);
                const unreadItems = allItems.filter(item => item.is_unread);
                const currentItems = allItems.filter(item => !item.is_unread);
                const activeTab = state.employeeDetailNotificationTab || 'new';
                const selectedItems = activeTab === 'current' ? currentItems : unreadItems;
                const isNewSection = activeTab !== 'current';

                return `
                            <div class="rr-employee-detail-notification-tabs">
                                <div class="rr-employee-detail-notification-tabs-head">
                                    <button type="button"
                                            class="rr-employee-detail-notification-tab ${activeTab === 'new' ? 'active' : ''}"
                                            data-employee-detail-notification-tab="new">
                                        <span><i class="fa-solid fa-bell"></i> Neue Reports</span>
                                        <em>${Number(unreadItems.length || 0)}</em>
                                    </button>

                                    <button type="button"
                                            class="rr-employee-detail-notification-tab ${activeTab === 'current' ? 'active' : ''}"
                                            data-employee-detail-notification-tab="current">
                                        <span><i class="fa-solid fa-clock-rotate-left"></i> Aktuelle Reports</span>
                                        <em>${Number(currentItems.length || 0)}</em>
                                    </button>
                                </div>

                                <div class="rr-employee-detail-notification-tabs-body">
                                    ${renderNotificationSection(
                    isNewSection ? 'Neue Reports dieses Mitarbeiters' : 'Aktuelle Reports dieses Mitarbeiters',
                    isNewSection ? 'fa-bell' : 'fa-clock-rotate-left',
                    selectedItems,
                    isNewSection,
                    12
                )}
                                </div>
                            </div>
                        `;
            }

            function employeeDetailMetric(type, icon, label, value) {
                const active = state.employeeSummaryDetailType === type;

                return `
                                        <button
                                            type="button"
                                            class="rr-employee-detail-metric ${escapeHtml(type)} ${active ? 'active' : ''}"
                                            data-employee-detail-load-type="${escapeAttr(type)}"
                                        >
                                            <i class="fa-solid ${escapeHtml(icon)}"></i>
                                            <strong>${Number(value || 0)}</strong>
                                            <span>${escapeHtml(label)}</span>
                                        </button>
                                    `;
            }

            function employeeCountBadge(type, icon, label, value) {
                const count = Number(value || 0);

                return `
                                        <div
                                            class="rr-employee-count ${escapeHtml(type)} ${state.employeeSummaryDetailType === type ? 'active' : ''}"
                                            title="${escapeAttr(label)} anzeigen"
                                            data-employee-count-type="${escapeAttr(type)}"
                                            role="button"
                                            tabindex="0"
                                        >
                                            <i class="fa-solid ${escapeHtml(icon)}"></i>
                                            <strong>${count}</strong>
                                            <span>${escapeHtml(label)}</span>
                                        </div>
                                    `;
            }

            function typeLabelForEmployeeSummary(type) {
                const labels = {
                    new: 'neue Berichte',
                    ticket: 'Tickets',
                    task: 'Aufgaben',
                    lead: 'Leads',
                    inquiry: 'Anfragen',
                    appointment: 'Termine',
                };
                return labels[type] || 'alle Typen';
            }

            function applyEmployeeSummaryFilter(type = 'all') {
                const employeeId = state.employeeSummarySelectedId || '';
                if (!employeeId || !els.employee) return;

                $(els.employee).val(employeeId).trigger('change');

                if (type && type !== 'all' && type !== 'new' && els.type) {
                    $(els.type).val(type).trigger('change');
                } else if (type === 'new' && els.type) {
                    $(els.type).val('').trigger('change');
                }

                state.page = 1;
                renderActiveFilters();
                load();
            }


            function normalizeText(value) {
                return String(value || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/ä/g, 'a')
                    .replace(/ö/g, 'o')
                    .replace(/ü/g, 'u')
                    .replace(/ß/g, 'ss')
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            function initials(name) {
                const n = String(name || '').trim();
                if (!n) return '?';

                const p = n.split(/\s+/).filter(Boolean);
                const a = (p[0]?.[0] || '');
                const b = (p[1]?.[0] || '');

                return (a + b).toUpperCase() || a.toUpperCase() || '?';
            }

            function iconForType(type) {
                if (type === 'employee') return 'fa-user-pen';
                if (type === 'customer') return 'fa-building-user';
                if (type === 'product') return 'fa-box';
                if (type === 'department') return 'fa-sitemap';
                if (type === 'report') return 'fa-file-lines';
                if (type === 'inquiry') return 'fa-envelope-open-text';
                if (type === 'appointment') return 'fa-calendar-day';
                if (type === 'ticket') return 'fa-life-ring';
                if (type === 'lead') return 'fa-star';
                return 'fa-magnifying-glass';
            }

            function labelForSmartType(type) {
                if (type === 'employee') return 'Mitarbeiter';
                if (type === 'customer') return 'Kunde';
                if (type === 'product') return 'Produkt';
                if (type === 'department') return 'Abteilung';
                if (type === 'report') return 'Bericht';
                return 'Suche';
            }

            function badgeMeta(type) {
                if (type === 'inquiry') {
                    return { cls: 'rr-badge-inquiry', icon: 'fa-envelope-open-text', label: 'Anfrage' };
                }

                if (type === 'appointment') {
                    return { cls: 'rr-badge-appointment', icon: 'fa-calendar-day', label: 'Termin' };
                }

                if (type === 'lead') {
                    return { cls: 'rr-badge-lead', icon: 'fa-star', label: 'Lead' };
                }

                if (type === 'ticket') {
                    return { cls: 'rr-badge-ticket', icon: 'fa-life-ring', label: 'Ticket' };
                }

                return { cls: 'rr-badge-task', icon: 'fa-tasks', label: 'Aufgabe' };
            }



            function typeLabel(type) {
                return badgeMeta(type).label || 'Bericht';
            }

            function formatDateDisplay(value) {
                if (!value) return '—';

                try {
                    const raw = String(value).replace(' ', 'T');
                    const d = new Date(raw);

                    if (Number.isNaN(d.getTime())) {
                        return String(value).slice(0, 16);
                    }

                    return d.toLocaleString('de-DE', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    });
                } catch (_) {
                    return String(value).slice(0, 16);
                }
            }

            function shortReportText(text, limit = 180) {
                const clean = String(text || '').replace(/\s+/g, ' ').trim();
                if (!clean) return '—';
                return clean.length > limit ? clean.slice(0, limit).trim() + '...' : clean;
            }

            function detailItem(label, value, icon = 'fa-circle-info') {
                return `
                                                                <div class="rr-detail-info">
                                                                    <div class="rr-detail-info-label">
                                                                        <i class="fa-solid ${escapeHtml(icon)} rr-me-2"></i>${escapeHtml(label)}
                                                                    </div>
                                                                    <div class="rr-detail-info-value">${escapeHtml(value || '—')}</div>
                                                                </div>
                                                            `;
            }

            async function openReportDetailModal(row) {
                if (!row) return;

                const b = badgeMeta(row.type);
                const report = String(row.report_text || row.report || '');
                const refTitle = row.ref_title || row.title || '—';
                const employee = row.employee_name || row.report_by_name || row.employee || '—';

                const entityId = row.entity_id || row.target_id || row.item_id || row.source_id || row.id || '';
                const reportId = row.report_id || row.reportId || row.id || '';

                const created = formatDateDisplay(row.created_at || row.report_date);
                const reportDate = row.report_date || created || '—';
                const dueDate = row.due_date || '—';
                const nextStep = row.next_step || '—';
                const link = row.link || row.target_link || '#';

                els.reportDetailIcon.innerHTML = `<i class="fa-solid ${escapeHtml(b.icon)}"></i>`;
                els.reportDetailTitle.textContent = row.type === 'appointment'
                    ? 'Termin Bericht'
                    : (row.type === 'task'
                        ? 'Aufgabe Bericht'
                        : (row.type === 'ticket' ? 'Ticket Bericht' : `${b.label} Bericht`));
                els.reportDetailSub.textContent = `Quelle: ${refTitle}`;
                els.reportDetailText.textContent = report || '—';

                const baseGrid = [
                    detailItem('Typ', b.label, b.icon),
                    detailItem('Referenz', refTitle, 'fa-link'),
                    detailItem('Datensatz-ID', entityId ? `#${entityId}` : '—', 'fa-hashtag'),
                    detailItem('Report-ID', reportId ? `#${reportId}` : '—', 'fa-file-lines'),
                    detailItem('Mitarbeiter', employee, 'fa-user-pen'),
                    detailItem('Erstellt am', created, 'fa-calendar-days'),
                    detailItem('Berichtsdatum', reportDate, 'fa-calendar-check'),
                    detailItem('Wiedervorlage', dueDate, 'fa-clock'),
                    detailItem('Nächster Schritt', nextStep, 'fa-list-check'),
                ].join('');

                els.reportDetailGrid.innerHTML = baseGrid + `
                                                        <div class="rr-source-panel">
                                                            <div class="rr-source-head">
                                                                <div class="rr-source-title">
                                                                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                                                                    Details werden geladen
                                                                </div>
                                                            </div>
                                                            <div class="rr-source-body">
                                                                <div class="rr-source-value">
                                                                    ${row.type === 'appointment'
                        ? 'Termin-Details und Karte werden geladen...'
                        : (row.type === 'task'
                            ? 'Aufgabe-Details werden geladen...'
                            : (row.type === 'ticket'
                                ? 'Ticket-Details werden geladen...'
                                : 'Weitere Details werden geprüft...'))}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;

                if (link && link !== '#') {
                    els.reportDetailOpen.href = link;
                    els.reportDetailOpen.style.display = '';
                } else {
                    els.reportDetailOpen.href = '#';
                    els.reportDetailOpen.style.display = 'none';
                }

                els.reportDetailBackdrop.classList.add('active');
                els.reportDetailModal.classList.add('active');

                if (!['appointment', 'task', 'ticket'].includes(row.type) || !entityId) {
                    els.reportDetailGrid.innerHTML = baseGrid;
                    return;
                }

                try {
                    const url = new URL(API.sourceDetails, window.location.origin);
                    url.searchParams.set('type', row.type);
                    url.searchParams.set('id', entityId);

                    if (reportId) {
                        url.searchParams.set('report_id', reportId);
                    }

                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok || !data.success) {
                        throw new Error(data?.message || 'Details konnten nicht geladen werden.');
                    }

                    els.reportDetailGrid.innerHTML = baseGrid + renderSourceDetails(data, row);
                } catch (error) {
                    els.reportDetailGrid.innerHTML = baseGrid + `
                                                            <div class="rr-source-panel" style="border-color: rgba(229, 6, 86, .25);">
                                                                <div class="rr-source-head" style="background: rgba(229, 6, 86, .04);">
                                                                    <div class="rr-source-title" style="color: var(--rr-danger);">
                                                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                                                        Details konnten nicht geladen werden
                                                                    </div>
                                                                </div>
                                                                <div class="rr-source-body">
                                                                    <div class="rr-source-value">${escapeHtml(error?.message || 'Unbekannter Fehler.')}</div>
                                                                </div>
                                                            </div>
                                                        `;
                }
            }

            function renderSourceDetails(data) {
                if (data.type === 'appointment' && data.appointment) {
                    return renderTerminModalDetails(data.appointment, data.report);
                }

                if (data.type === 'task' && data.task) {
                    return renderAufgabeModalDetails(data.task, data.report, data.appointments || []);
                }

                if (data.type === 'ticket' && data.ticket) {
                    return renderTicketModalDetails(data.ticket, data.report, data.ticket_reports || [], data.appointments || []);
                }

                return `
                                                        <div class="rr-source-panel">
                                                            <div class="rr-source-head">
                                                                <div class="rr-source-title">
                                                                    <i class="fa-solid fa-circle-info"></i>
                                                                    Weitere Details
                                                                </div>
                                                            </div>
                                                            <div class="rr-source-body">
                                                                <div class="rr-source-value">Keine erweiterten Details gefunden.</div>
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderTicketModalDetails(ticket, report = null, reports = [], appointments = []) {
                const customer = customerName(ticket.customer);
                const product = ticket.product?.article_group || ticket.product?.name || '—';

                const employees = Array.isArray(ticket.employees) && ticket.employees.length
                    ? ticket.employees.map(emp => fullName(emp)).join(', ')
                    : '—';

                const errors = Array.isArray(ticket.errors) && ticket.errors.length
                    ? ticket.errors.map(e => e.name || e.title || e.error || e.error_name || ('Fehler #' + e.id)).join(', ')
                    : '—';

                const ticketTasks = ticket.ticket_tasks || ticket.ticketTasks || [];
                const ticketComments = ticket.comments || [];

                return `
                                                <div class="rr-source-panel rr-ticket-panel">
                                                    <div class="rr-source-head">
                                                        <div class="rr-source-title">
                                                            <i class="fa-solid fa-life-ring"></i>
                                                            Ticket Details
                                                        </div>

                                                        <span class="rr-status-badge rr-badge-ticket">
                                                            ${escapeHtml(ticket.status || '—')}
                                                        </span>
                                                    </div>

                                                    <div class="rr-source-body">
                                                        <div class="rr-collapse-stack">

                                                            <details class="rr-collapse" open>
                                                                <summary class="rr-collapse-summary">
                                                                    <span class="rr-collapse-title">
                                                                        <i class="fa-solid fa-circle-info"></i>
                                                                        Übersicht
                                                                    </span>
                                                                    <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                </summary>

                                                                <div class="rr-collapse-body">
                                                                    <div class="rr-source-grid">
                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Ticket-Nr.</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.ticket_no || ('#' + ticket.id))}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Artikel / Betreff</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.article_name || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Status</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.status || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Priorität</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.priority || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Kunde</div>
                                                                            <div class="rr-source-value">${escapeHtml(customer)}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Produkt</div>
                                                                            <div class="rr-source-value">${escapeHtml(product)}</div>
                                                                        </div>

                                                                        <div class="rr-source-item full">
                                                                            <div class="rr-source-label">Zuständige Mitarbeiter</div>
                                                                            <div class="rr-source-value">${escapeHtml(employees)}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </details>

                                                            <details class="rr-collapse">
                                                                <summary class="rr-collapse-summary">
                                                                    <span class="rr-collapse-title">
                                                                        <i class="fa-solid fa-bug"></i>
                                                                        Fehler & Artikel
                                                                    </span>
                                                                    <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                </summary>

                                                                <div class="rr-collapse-body">
                                                                    <div class="rr-source-grid">
                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Fehlercode</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.error_code || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Fehlertyp</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.error_type || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Artikel SN</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.article_sn || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Installationsdatum</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.installation_date || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item full">
                                                                            <div class="rr-source-label">Verknüpfte Fehler</div>
                                                                            <div class="rr-source-value">${escapeHtml(errors)}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </details>

                                                            <details class="rr-collapse">
                                                                <summary class="rr-collapse-summary">
                                                                    <span class="rr-collapse-title">
                                                                        <i class="fa-solid fa-shield-halved"></i>
                                                                        Garantie & Zeiten
                                                                    </span>
                                                                    <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                </summary>

                                                                <div class="rr-collapse-body">
                                                                    <div class="rr-source-grid">
                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Garantieart</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.warranty_type || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Garantie Dauer</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.warranty_duration || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Garantie Rest</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.warranty_remaining || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Finanzierung bis</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.finance_to || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Erstellt am</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.date || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">In Bearbeitung seit</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.progress_date || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Beendet am</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.end_date || '—')}</div>
                                                                        </div>

                                                                        <div class="rr-source-item">
                                                                            <div class="rr-source-label">Gesamtzeit</div>
                                                                            <div class="rr-source-value">${escapeHtml(ticket.total_time || '—')}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </details>

                                                            <details class="rr-collapse" open>
                                                                <summary class="rr-collapse-summary">
                                                                    <span class="rr-collapse-title">
                                                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                                                        Problem, Fortschritt & Lösung
                                                                    </span>
                                                                    <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                </summary>

                                                                <div class="rr-collapse-body">
                                                                    <div class="rr-source-grid">
                                                                        <div class="rr-source-item full">
                                                                            <div class="rr-source-label">Problem</div>
                                                                            <div class="rr-source-value">${nl2br(escapeHtml(ticket.problem || '—'))}</div>
                                                                        </div>

                                                                        <div class="rr-source-item full">
                                                                            <div class="rr-source-label">Fortschritt</div>
                                                                            <div class="rr-source-value">${nl2br(escapeHtml(ticket.progress || '—'))}</div>
                                                                        </div>

                                                                        <div class="rr-source-item full">
                                                                            <div class="rr-source-label">Lösung</div>
                                                                            <div class="rr-source-value">${nl2br(escapeHtml(ticket.solution || '—'))}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </details>

                                                            <details class="rr-collapse">
                                                                <summary class="rr-collapse-summary">
                                                                    <span class="rr-collapse-title">
                                                                        <i class="fa-solid fa-users-gear"></i>
                                                                        Personen
                                                                    </span>
                                                                    <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                </summary>
                                                                <div class="rr-collapse-body">${renderTicketPeople(ticket)}</div>
                                                            </details>

                                                            ${Array.isArray(ticketTasks) && ticketTasks.length ? `
                                                                <details class="rr-collapse">
                                                                    <summary class="rr-collapse-summary">
                                                                        <span class="rr-collapse-title">
                                                                            <i class="fa-solid fa-list-check"></i>
                                                                            Ticket Aufgaben (${ticketTasks.length})
                                                                        </span>
                                                                        <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                    </summary>
                                                                    <div class="rr-collapse-body">${renderTicketTasks(ticketTasks)}</div>
                                                                </details>
                                                            ` : ''}

                                                            ${Array.isArray(ticketComments) && ticketComments.length ? `
                                                                <details class="rr-collapse">
                                                                    <summary class="rr-collapse-summary">
                                                                        <span class="rr-collapse-title">
                                                                            <i class="fa-solid fa-comments"></i>
                                                                            Ticket Kommentare (${ticketComments.length})
                                                                        </span>
                                                                        <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                    </summary>
                                                                    <div class="rr-collapse-body">${renderTicketComments(ticketComments)}</div>
                                                                </details>
                                                            ` : ''}

                                                            ${Array.isArray(reports) && reports.length ? `
                                                                <details class="rr-collapse">
                                                                    <summary class="rr-collapse-summary">
                                                                        <span class="rr-collapse-title">
                                                                            <i class="fa-solid fa-file-lines"></i>
                                                                            Ticket Berichte (${reports.length})
                                                                        </span>
                                                                        <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                    </summary>
                                                                    <div class="rr-collapse-body">${renderTicketReports(reports)}</div>
                                                                </details>
                                                            ` : ''}

                                                            ${Array.isArray(appointments) && appointments.length ? `
                                                                <details class="rr-collapse">
                                                                    <summary class="rr-collapse-summary">
                                                                        <span class="rr-collapse-title">
                                                                            <i class="fa-solid fa-calendar-check"></i>
                                                                            Verknüpfte Termine (${appointments.length})
                                                                        </span>
                                                                        <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                                    </summary>
                                                                    <div class="rr-collapse-body">${renderLinkedAppointmentsForTask(appointments)}</div>
                                                                </details>
                                                            ` : ''}
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
            }



            function renderTicketPeople(ticket) {
                return `
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Ticket Personen</div>
                                                        <div class="rr-source-value rr-mini-list">
                                                            <div class="rr-mini-row">
                                                                <strong>Verantwortlich</strong>
                                                                <div>${escapeHtml(fullName(ticket.responsible_employee || ticket.responsibleEmployee) || '—')}</div>
                                                            </div>

                                                            <div class="rr-mini-row">
                                                                <strong>Erster Kontakt</strong>
                                                                <div>${escapeHtml(fullName(ticket.first_contact_employee || ticket.firstContactEmployee || ticket.first_contact) || '—')}</div>
                                                            </div>

                                                            <div class="rr-mini-row">
                                                                <strong>Gestartet von</strong>
                                                                <div>${escapeHtml(fullName(ticket.start_user || ticket.startUser) || '—')}</div>
                                                            </div>

                                                            <div class="rr-mini-row">
                                                                <strong>Bearbeitet von</strong>
                                                                <div>${escapeHtml(fullName(ticket.progress_user || ticket.progressUser) || '—')}</div>
                                                            </div>

                                                            <div class="rr-mini-row">
                                                                <strong>Beendet von</strong>
                                                                <div>${escapeHtml(fullName(ticket.end_user || ticket.endUser) || '—')}</div>
                                                            </div>

                                                            <div class="rr-mini-row">
                                                                <strong>Zuletzt geändert von</strong>
                                                                <div>${escapeHtml(fullName(ticket.edit_user || ticket.editUser) || '—')}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
            }

            function renderTicketTasks(tasks) {
                if (!Array.isArray(tasks) || !tasks.length) return '';

                return `
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Ticket Aufgaben</div>
                                                        <div class="rr-source-value rr-mini-list">
                                                            ${tasks.map(task => `
                                                                <div class="rr-mini-row">
                                                                    <strong>${escapeHtml(task.title || task.task_title || task.name || ('Aufgabe #' + task.id))}</strong>
                                                                    <div>${nl2br(escapeHtml(task.description || task.note || task.task || ''))}</div>
                                                                    <small>${escapeHtml(task.status || task.task_status || '')}</small>
                                                                </div>
                                                            `).join('')}
                                                        </div>
                                                    </div>
                                                `;
            }

            function renderTicketComments(comments) {
                if (!Array.isArray(comments) || !comments.length) return '';

                return `
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Ticket Kommentare</div>
                                                        <div class="rr-source-value rr-mini-list">
                                                            ${comments.map(comment => `
                                                                <div class="rr-mini-row">
                                                                    <strong>${escapeHtml(comment.employee ? fullName(comment.employee) : 'Kommentar')}</strong>
                                                                    <div>${nl2br(escapeHtml(comment.comment || comment.note || comment.text || '—'))}</div>
                                                                    <small>${escapeHtml(comment.created_at || '')}</small>
                                                                </div>
                                                            `).join('')}
                                                        </div>
                                                    </div>
                                                `;
            }

            function renderTicketReports(reports) {
                if (!Array.isArray(reports) || !reports.length) return '';

                return `
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Ticket Berichte</div>
                                                        <div class="rr-source-value rr-mini-list">
                                                            ${reports.map(report => `
                                                                <div class="rr-mini-row">
                                                                    <strong>${escapeHtml(report.title || (report.employee ? fullName(report.employee) : 'Bericht'))}</strong>
                                                                    <div>${nl2br(escapeHtml(report.report || '—'))}</div>
                                                                    <small>${escapeHtml(report.report_date || report.created_at || '')}</small>
                                                                </div>
                                                            `).join('')}
                                                        </div>
                                                    </div>
                                                `;
            }
            function rrCollapse(title, icon, body, open = false) {
                return `
                                                <details class="rr-collapse" ${open ? 'open' : ''}>
                                                    <summary class="rr-collapse-summary">
                                                        <span class="rr-collapse-title">
                                                            <i class="fa-solid ${escapeHtml(icon)}"></i>
                                                            ${escapeHtml(title)}
                                                        </span>
                                                        <span class="rr-collapse-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                                    </summary>
                                                    <div class="rr-collapse-body">${body}</div>
                                                </details>
                                            `;
            }

            function renderTerminModalDetails(a, report = null) {
                const employees = Array.isArray(a.employees) && a.employees.length
                    ? a.employees.map(emp => fullName(emp)).join(', ')
                    : '—';

                const customer = customerName(a.customer);
                const address = a.full_address || buildFullAddress(a) || '—';

                const overview = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Titel</div>
                                                        <div class="rr-source-value">${escapeHtml(a.name || 'Termin #' + (a.id || ''))}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Status</div>
                                                        <div class="rr-source-value">${escapeHtml(a.status || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Priorität</div>
                                                        <div class="rr-source-value">${escapeHtml(a.priority || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Terminart</div>
                                                        <div class="rr-source-value">${escapeHtml(a.appointment_type || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Ausführung</div>
                                                        <div class="rr-source-value">${escapeHtml(a.execution_type || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Start</div>
                                                        <div class="rr-source-value">${escapeHtml(dateLine(a.start_date, a.start_time))}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Ende</div>
                                                        <div class="rr-source-value">${escapeHtml(dateLine(a.end_date, a.end_time))}</div>
                                                    </div>
                                                </div>
                                            `;

                const addressMap = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Adresse</div>
                                                        <div class="rr-source-value">${escapeHtml(address)}</div>
                                                    </div>
                                                    ${renderGoogleMap(a)}
                                                </div>
                                            `;

                const contactPeople = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Kunde</div>
                                                        <div class="rr-source-value">${escapeHtml(customer)}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Kontaktmodus</div>
                                                        <div class="rr-source-value">${escapeHtml(a.contact_mode || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">E-Mail</div>
                                                        <div class="rr-source-value">${escapeHtml(a.email || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Telefon</div>
                                                        <div class="rr-source-value">${escapeHtml(a.phone || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Mitarbeiter</div>
                                                        <div class="rr-source-value">${escapeHtml(employees)}</div>
                                                    </div>
                                                </div>
                                            `;

                const notesReports = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Nächster Schritt</div>
                                                        <div class="rr-source-value">${nl2br(escapeHtml(a.next_step || '—'))}</div>
                                                    </div>
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Notiz</div>
                                                        <div class="rr-source-value">${nl2br(escapeHtml(a.note || '—'))}</div>
                                                    </div>
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Termin-Bericht</div>
                                                        <div class="rr-source-value">${nl2br(escapeHtml(a.report || '—'))}</div>
                                                    </div>
                                                </div>
                                            `;

                return `
                                                <div class="rr-source-panel rr-termin-panel">
                                                    <div class="rr-source-head">
                                                        <div class="rr-source-title">
                                                            <i class="fa-solid fa-calendar-day"></i>
                                                            Termin Details
                                                        </div>
                                                        <span class="rr-status-badge rr-badge-appointment">
                                                            ${escapeHtml(a.status || '—')}
                                                        </span>
                                                    </div>
                                                    <div class="rr-source-body">
                                                        <div class="rr-collapse-stack">
                                                            ${rrCollapse('Übersicht', 'fa-circle-info', overview, true)}
                                                            ${rrCollapse('Adresse & Google Map', 'fa-map-location-dot', addressMap, true)}
                                                            ${rrCollapse('Kontakt & Mitarbeiter', 'fa-users', contactPeople, false)}
                                                            ${rrCollapse('Notiz, Bericht & nächster Schritt', 'fa-align-left', notesReports, false)}
                                                            ${a.reports && a.reports.length ? rrCollapse('Weitere Termin-Berichte', 'fa-file-lines', renderTerminReports(a.reports), false) : ''}
                                                            ${a.comments && a.comments.length ? rrCollapse('Termin-Kommentare', 'fa-comments', renderTerminComments(a.comments), false) : ''}
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
            }

            function renderAufgabeModalDetails(t, report = null, appointments = []) {
                const employees = Array.isArray(t.employees) && t.employees.length
                    ? t.employees.map(emp => {
                        const status = emp.pivot?.status ? ` (${emp.pivot.status})` : '';
                        return `${fullName(emp)}${status}`;
                    }).join(', ')
                    : '—';

                const customer = customerName(t.customer);
                const progress = Number(t.progress || 0);

                const overview = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Titel</div>
                                                        <div class="rr-source-value">${escapeHtml(t.task_title || 'Aufgabe #' + (t.id || ''))}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Status</div>
                                                        <div class="rr-source-value">${escapeHtml(t.task_status || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Priorität</div>
                                                        <div class="rr-source-value">${escapeHtml(t.priority || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Typ</div>
                                                        <div class="rr-source-value">${escapeHtml(t.type || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Kunde</div>
                                                        <div class="rr-source-value">${escapeHtml(customer)}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Produkt</div>
                                                        <div class="rr-source-value">${escapeHtml(t.product?.article_group || t.product?.name || '—')}</div>
                                                    </div>
                                                </div>
                                            `;

                const planningProgress = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Startdatum</div>
                                                        <div class="rr-source-value">${escapeHtml(t.start_date || '—')}</div>
                                                    </div>
                                                    <div class="rr-source-item">
                                                        <div class="rr-source-label">Fällig</div>
                                                        <div class="rr-source-value">${escapeHtml(dateLine(t.due_date, t.due_time))}</div>
                                                    </div>
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Fortschritt</div>
                                                        <div class="rr-source-value">
                                                            ${escapeHtml(progress)}%
                                                            <div class="rr-progress-line">
                                                                <div class="rr-progress-fill" style="width:${Math.max(0, Math.min(100, progress))}%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;

                const description = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Beschreibung</div>
                                                        <div class="rr-source-value">${nl2br(escapeHtml(t.description || '—'))}</div>
                                                    </div>
                                                </div>
                                            `;

                const assigned = `
                                                <div class="rr-source-grid">
                                                    <div class="rr-source-item full">
                                                        <div class="rr-source-label">Zugewiesen an</div>
                                                        <div class="rr-source-value">${escapeHtml(employees)}</div>
                                                    </div>
                                                </div>
                                            `;

                return `
                                                <div class="rr-source-panel rr-task-panel">
                                                    <div class="rr-source-head">
                                                        <div class="rr-source-title">
                                                            <i class="fa-solid fa-tasks"></i>
                                                            Aufgabe Details
                                                        </div>
                                                        <span class="rr-status-badge rr-badge-task">
                                                            ${escapeHtml(t.task_status || '—')}
                                                        </span>
                                                    </div>
                                                    <div class="rr-source-body">
                                                        <div class="rr-collapse-stack">
                                                            ${rrCollapse('Übersicht', 'fa-circle-info', overview, true)}
                                                            ${rrCollapse('Planung & Fortschritt', 'fa-chart-line', planningProgress, true)}
                                                            ${rrCollapse('Beschreibung', 'fa-align-left', description, false)}
                                                            ${rrCollapse('Mitarbeiter', 'fa-users', assigned, false)}
                                                            ${(t.steps || t.keys)?.length ? rrCollapse('Schritte', 'fa-list-check', renderTaskSteps(t.steps || t.keys), false) : ''}
                                                            ${t.attachments?.length ? rrCollapse('Anhänge', 'fa-paperclip', renderTaskAttachments(t.attachments), false) : ''}
                                                            ${t.comments?.length ? rrCollapse('Kommentare', 'fa-comments', renderTaskComments(t.comments), false) : ''}
                                                            ${t.histories?.length ? rrCollapse('Historie', 'fa-clock-rotate-left', renderTaskHistories(t.histories), false) : ''}
                                                            ${appointments?.length ? rrCollapse('Verknüpfte Termine', 'fa-calendar-check', renderLinkedAppointmentsForTask(appointments), false) : ''}
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
            }

            function renderGoogleMap(a) {
                const lat = a.latitude || '';
                const lng = a.longitude || '';
                const address = a.full_address || buildFullAddress(a) || '';

                let query = '';

                if (lat && lng) {
                    query = `${lat},${lng}`;
                } else if (address) {
                    query = address;
                }

                if (!query) {
                    return `
                                                            <div class="rr-source-item full">
                                                                <div class="rr-source-label">Google Map</div>
                                                                <div class="rr-source-value">Keine Adresse oder Koordinaten vorhanden.</div>
                                                            </div>
                                                        `;
                }

                const mapUrl = `https://www.google.com/maps?q=${encodeURIComponent(query)}&output=embed`;
                const openUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`;

                return `
                                                        <div class="rr-map-box">
                                                            <iframe
                                                                loading="lazy"
                                                                referrerpolicy="no-referrer-when-downgrade"
                                                                src="${escapeAttr(mapUrl)}">
                                                            </iframe>
                                                        </div>

                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Google Maps öffnen</div>
                                                            <div class="rr-source-value">
                                                                <a href="${escapeAttr(openUrl)}" target="_blank" rel="noopener">
                                                                    Adresse in Google Maps öffnen
                                                                </a>
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderTerminReports(reports) {
                if (!Array.isArray(reports) || !reports.length) return '';

                return `
                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Weitere Termin-Berichte</div>
                                                            <div class="rr-source-value rr-mini-list">
                                                                ${reports.map(r => `
                                                                    <div class="rr-mini-row">
                                                                        <strong>${escapeHtml(r.reporter ? fullName(r.reporter) : 'Bericht')}</strong>
                                                                        <div>${nl2br(escapeHtml(r.report || '—'))}</div>
                                                                        <small>${escapeHtml(r.report_date || r.created_at || '')}</small>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderTerminComments(comments) {
                if (!Array.isArray(comments) || !comments.length) return '';

                return `
                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Termin-Kommentare</div>
                                                            <div class="rr-source-value rr-mini-list">
                                                                ${comments.map(c => `
                                                                    <div class="rr-mini-row">
                                                                        <div>${nl2br(escapeHtml(c.comment || c.note || c.text || '—'))}</div>
                                                                        <small>${escapeHtml(c.created_at || '')}</small>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderTaskSteps(steps) {
                if (!Array.isArray(steps) || !steps.length) return '';

                return `
                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Aufgaben-Schritte</div>
                                                            <div class="rr-source-value rr-mini-list">
                                                                ${steps.map(s => `
                                                                    <div class="rr-mini-row">
                                                                        <strong>${escapeHtml(s.task || s.title || s.name || s.key || 'Schritt')}</strong>
                                                                        <div>${escapeHtml(s.status || s.work_status || '')}</div>
                                                                        <small>${escapeHtml(s.created_at || '')}</small>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderTaskAttachments(files) {
                if (!Array.isArray(files) || !files.length) return '';

                return `
                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Anhänge</div>
                                                            <div class="rr-source-value rr-mini-list">
                                                                ${files.map(f => {
                    const name = f.name || f.file_name || f.filename || 'Anhang';
                    const url = f.url || f.path || f.file_path || '#';

                    return `
                                                                        <div class="rr-mini-row">
                                                                            <i class="fa-solid fa-paperclip rr-me-2"></i>
                                                                            ${url && url !== '#'
                            ? `<a href="${escapeAttr(url)}" target="_blank" rel="noopener">${escapeHtml(name)}</a>`
                            : escapeHtml(name)
                        }
                                                                        </div>
                                                                    `;
                }).join('')}
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderTaskComments(comments) {
                if (!Array.isArray(comments) || !comments.length) return '';

                return `
                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Aufgaben-Kommentare</div>
                                                            <div class="rr-source-value rr-mini-list">
                                                                ${comments.map(c => `
                                                                    <div class="rr-mini-row">
                                                                        <strong>${escapeHtml(c.author ? fullName(c.author) : 'Kommentar')}</strong>
                                                                        <div>${nl2br(escapeHtml(c.comment || '—'))}</div>
                                                                        <small>${escapeHtml(c.created_at || '')}</small>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderTaskHistories(histories) {
                if (!Array.isArray(histories) || !histories.length) return '';

                return `
                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Historie</div>
                                                            <div class="rr-source-value rr-mini-list">
                                                                ${histories.map(h => `
                                                                    <div class="rr-mini-row">
                                                                        <strong>${escapeHtml(h.title || h.type || h.status || 'Historie')}</strong>
                                                                        <div>${nl2br(escapeHtml(h.description || h.note || h.message || ''))}</div>
                                                                        <small>${escapeHtml(h.created_at || '')}</small>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                    `;
            }

            function renderLinkedAppointmentsForTask(appointments) {
                if (!Array.isArray(appointments) || !appointments.length) return '';

                return `
                                                        <div class="rr-source-item full">
                                                            <div class="rr-source-label">Verknüpfte Termine</div>
                                                            <div class="rr-source-value rr-mini-list">
                                                                ${appointments.map(a => `
                                                                    <div class="rr-mini-row">
                                                                        <strong>${escapeHtml(a.name || 'Termin #' + a.id)}</strong>
                                                                        <div>${escapeHtml(dateLine(a.start_date, a.start_time))}</div>
                                                                        <small>${escapeHtml(a.status || '')}</small>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </div>
                                                    `;
            }

            function fullName(person) {
                if (!person) return '';
                return String(`${person.name || ''} ${person.lastname || ''}`).trim() || `#${person.id || ''}`;
            }

            function customerName(customer) {
                if (!customer) return '—';

                return customer.firma
                    || String(`${customer.lastname || ''} ${customer.name || ''}`).trim()
                    || customer.customer_no
                    || `#${customer.id || ''}`;
            }

            function dateLine(date, time) {
                return [date, time].filter(Boolean).join(' ') || '—';
            }

            function buildFullAddress(item) {
                return [item.street, item.postcode, item.city].filter(Boolean).join(', ');
            }

            function nl2br(value) {
                return String(value || '').replace(/\n/g, '<br>');
            }

            function escapeAttr(value) {
                return escapeHtml(value).replaceAll('`', '&#096;');
            }

            function closeReportDetailModal() {
                els.reportDetailBackdrop.classList.remove('active');
                els.reportDetailModal.classList.remove('active');
            }

            function bindReportDetailModalEvents() {
                els.reportDetailClose?.addEventListener('click', closeReportDetailModal);
                els.reportDetailCancel?.addEventListener('click', closeReportDetailModal);
                els.reportDetailBackdrop?.addEventListener('click', closeReportDetailModal);

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && els.reportDetailModal.classList.contains('active')) {
                        closeReportDetailModal();
                    }
                });
            }

            bindReportDetailModalEvents();

            function collectValidationErrors(payload) {
                const out = [];

                if (!payload || typeof payload !== 'object') {
                    return out;
                }

                if (payload.errors && typeof payload.errors === 'object') {
                    Object.values(payload.errors).forEach(value => {
                        if (Array.isArray(value)) {
                            value.forEach(msg => out.push(String(msg)));
                        } else if (value) {
                            out.push(String(value));
                        }
                    });
                }

                return out;
            }

            function showErrorModal(options = {}) {
                const title = options.title || 'Fehler';
                const subtitle = options.subtitle || 'Die Aktion konnte nicht abgeschlossen werden.';
                const message = options.message || 'Unbekannter Fehler.';
                const errors = Array.isArray(options.errors) ? options.errors.filter(Boolean) : [];

                els.errorTitle.textContent = title;
                els.errorSubtitle.textContent = subtitle;
                els.errorMessage.textContent = message;

                if (errors.length) {
                    els.errorList.style.display = '';
                    els.errorList.innerHTML = `
                                                                    <ul>
                                                                        ${errors.map(err => `<li>${escapeHtml(err)}</li>`).join('')}
                                                                    </ul>
                                                                `;
                } else {
                    els.errorList.style.display = 'none';
                    els.errorList.innerHTML = '';
                }

                els.errorBackdrop.classList.add('active');
                els.errorModal.classList.add('active');
            }

            function closeErrorModal() {
                els.errorBackdrop.classList.remove('active');
                els.errorModal.classList.remove('active');
            }

            function bindErrorModalEvents() {
                els.errorCloseBtn?.addEventListener('click', closeErrorModal);
                els.errorCancelBtn?.addEventListener('click', closeErrorModal);
                els.errorOkBtn?.addEventListener('click', closeErrorModal);
                els.errorBackdrop?.addEventListener('click', closeErrorModal);

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && els.errorModal.classList.contains('active')) {
                        closeErrorModal();
                    }
                });
            }

            bindErrorModalEvents();

            function select2Template(data) {
                if (!data.id) {
                    return data.text;
                }

                const el = data.element ? $(data.element) : null;
                const sub = el ? (el.data('sub') || '') : '';
                const icon = el ? (el.data('icon') || iconForType(el.data('type'))) : 'fa-circle';
                const name = data.text || '';
                const ini = initials(name);

                return $(`
                                                                <div class="rr-select-option">
                                                                    <span class="rr-select-dot">
                                                                        ${icon ? `<i class="fa-solid ${escapeHtml(icon)}"></i>` : escapeHtml(ini)}
                                                                    </span>
                                                                    <span class="rr-select-meta">
                                                                        <span class="rr-select-name">${escapeHtml(name)}</span>
                                                                        ${sub ? `<span class="rr-select-sub">${escapeHtml(sub)}</span>` : ''}
                                                                    </span>
                                                                </div>
                                                            `);
            }

            function select2Selection(data) {
                if (!data.id) {
                    return data.text;
                }

                const el = data.element ? $(data.element) : null;
                const icon = el ? (el.data('icon') || iconForType(el.data('type'))) : '';
                const name = data.text || '';

                return $(`
                                                                <span style="display:inline-flex;align-items:center;gap:8px;min-width:0;">
                                                                    ${icon ? `<i class="fa-solid ${escapeHtml(icon)}" style="color:var(--rr-primary-dark);"></i>` : ''}
                                                                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(name)}</span>
                                                                </span>
                                                            `);
            }

            function initSelect2() {
                $('.rr-smart-select').each(function () {
                    const $sel = $(this);
                    const placeholder = $sel.data('placeholder') || 'Auswählen';

                    $sel.select2({
                        placeholder: placeholder,
                        allowClear: true,
                        width: '100%',
                        templateResult: select2Template,
                        templateSelection: select2Selection,
                        dropdownAutoWidth: false,
                        dropdownCssClass: 'rr-smart-select-dropdown',
                    });

                    $sel.on('change', function () {
                        state.page = 1;
                        renderActiveFilters();
                        debouncedReload();
                    });
                });
            }

            initSelect2();

            function smartSeedFromRows() {
                return (state.currentRows || []).flatMap(row => {
                    const items = [];

                    if (row.ref_title) {
                        items.push({
                            id: row.entity_id || row.report_id,
                            type: 'report',
                            text: row.ref_title,
                            sub: row.report_text || '',
                            action: 'query',
                            value: row.ref_title,
                        });
                    }

                    if (row.employee_name) {
                        items.push({
                            id: row.report_by || row.employee_id || row.report_id,
                            type: 'employee',
                            text: row.employee_name,
                            sub: 'aus aktuellen Ergebnissen',
                            action: 'query',
                            value: row.employee_name,
                        });
                    }

                    if (row.report_text) {
                        items.push({
                            id: row.report_id,
                            type: 'report',
                            text: String(row.report_text).slice(0, 80),
                            sub: row.ref_title || 'Bericht',
                            action: 'query',
                            value: String(row.report_text).slice(0, 80),
                        });
                    }

                    return items;
                });
            }

            function getSmartSuggestions(term) {
                const q = normalizeText(term);

                if (q.length < 2) {
                    return [];
                }

                const base = [
                    ...SMART_SEED.map(x => ({
                        id: x.id,
                        type: x.type,
                        text: x.text,
                        sub: x.sub,
                        action: 'filter',
                        value: x.id,
                    })),
                    ...smartSeedFromRows(),
                ];

                const seen = new Set();

                return base
                    .filter(item => {
                        const haystack = normalizeText(`${item.text || ''} ${item.sub || ''}`);
                        return haystack.includes(q);
                    })
                    .filter(item => {
                        const key = `${item.type}:${item.id}:${item.text}`;
                        if (seen.has(key)) return false;
                        seen.add(key);
                        return true;
                    })
                    .slice(0, 10);
            }

            function renderSmartSuggestions(items) {
                state.suggestionIndex = -1;

                if (!items.length) {
                    els.suggestions.classList.remove('active');
                    els.suggestions.innerHTML = '';
                    return;
                }

                els.suggestions.innerHTML = items.map((item, index) => {
                    const icon = iconForType(item.type);
                    const label = labelForSmartType(item.type);

                    return `
                                                                    <button type="button"
                                                                            class="rr-suggestion-item"
                                                                            data-index="${index}"
                                                                            data-type="${escapeHtml(item.type || '')}"
                                                                            data-action="${escapeHtml(item.action || 'query')}"
                                                                            data-value="${escapeHtml(item.value || '')}"
                                                                            data-text="${escapeHtml(item.text || '')}">
                                                                        <span class="rr-suggestion-icon">
                                                                            <i class="fa-solid ${escapeHtml(icon)}"></i>
                                                                        </span>

                                                                        <span>
                                                                            <span class="rr-suggestion-title">${escapeHtml(item.text || '—')}</span>
                                                                            <span class="rr-suggestion-sub">${escapeHtml(item.sub || 'Zum Suchen auswählen')}</span>
                                                                        </span>

                                                                        <span class="rr-suggestion-badge">${escapeHtml(label)}</span>
                                                                    </button>
                                                                `;
                }).join('');

                els.suggestions.classList.add('active');

                els.suggestions.querySelectorAll('.rr-suggestion-item').forEach(btn => {
                    btn.addEventListener('click', function () {
                        applySmartSuggestion(this);
                    });
                });
            }

            function applySmartSuggestion(btn) {
                const type = btn.getAttribute('data-type') || '';
                const action = btn.getAttribute('data-action') || 'query';
                const value = btn.getAttribute('data-value') || '';
                const text = btn.getAttribute('data-text') || '';

                if (action === 'filter') {
                    if (type === 'employee') {
                        $(els.employee).val(value).trigger('change');
                        els.q.value = '';
                    } else if (type === 'customer') {
                        $(els.customer).val(value).trigger('change');
                        els.q.value = '';
                    } else if (type === 'product') {
                        $(els.product).val(value).trigger('change');
                        els.q.value = '';
                    } else if (type === 'department') {
                        $(els.department).val(value).trigger('change');
                        els.q.value = '';
                    } else {
                        els.q.value = text;
                    }
                } else {
                    els.q.value = text;
                    state.page = 1;
                    debouncedReload();
                }

                hideSuggestions();
                renderActiveFilters();
            }

            function hideSuggestions() {
                els.suggestions.classList.remove('active');
                els.suggestions.innerHTML = '';
                state.suggestionIndex = -1;
            }

            function handleSuggestionKeyboard(e) {
                const buttons = Array.from(els.suggestions.querySelectorAll('.rr-suggestion-item'));

                if (!buttons.length || !els.suggestions.classList.contains('active')) {
                    return;
                }

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    state.suggestionIndex = Math.min(buttons.length - 1, state.suggestionIndex + 1);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    state.suggestionIndex = Math.max(0, state.suggestionIndex - 1);
                } else if (e.key === 'Enter') {
                    if (state.suggestionIndex >= 0 && buttons[state.suggestionIndex]) {
                        e.preventDefault();
                        applySmartSuggestion(buttons[state.suggestionIndex]);
                    }
                    return;
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                    return;
                } else {
                    return;
                }

                buttons.forEach(btn => btn.classList.remove('active'));

                if (buttons[state.suggestionIndex]) {
                    buttons[state.suggestionIndex].classList.add('active');
                    buttons[state.suggestionIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            function selectedText(selectEl) {
                if (!selectEl) return '';

                const opt = selectEl.options[selectEl.selectedIndex];

                if (!opt || !opt.value) {
                    return '';
                }

                return opt.textContent.trim();
            }

            function renderActiveFilters() {
                const chips = [];

                const q = (els.q.value || '').trim();
                if (q) {
                    chips.push({ key: 'q', icon: 'fa-search', label: `Suche: ${q}` });
                }

                const typeText = selectedText(els.type);
                if (typeText) {
                    chips.push({ key: 'type', icon: 'fa-layer-group', label: `Typ: ${typeText}` });
                }

                const empText = selectedText(els.employee);
                if (empText) {
                    chips.push({ key: 'employee', icon: 'fa-user-pen', label: `Mitarbeiter: ${empText}` });
                }

                const customerText = selectedText(els.customer);
                if (customerText) {
                    chips.push({ key: 'customer', icon: 'fa-building-user', label: `Kunde: ${customerText}` });
                }

                const productText = selectedText(els.product);
                if (productText) {
                    chips.push({ key: 'product', icon: 'fa-box', label: `Produkt: ${productText}` });
                }

                const departmentText = selectedText(els.department);
                if (departmentText) {
                    chips.push({ key: 'department', icon: 'fa-sitemap', label: `Abteilung: ${departmentText}` });
                }

                if (els.from.value) {
                    chips.push({ key: 'from', icon: 'fa-calendar', label: `Von: ${els.from.value}` });
                }

                if (els.to.value) {
                    chips.push({ key: 'to', icon: 'fa-calendar-check', label: `Bis: ${els.to.value}` });
                }

                if (!chips.length) {
                    els.activeFilters.innerHTML = '';
                    return;
                }

                els.activeFilters.innerHTML = chips.map(chip => `
                                                                <span class="rr-chip">
                                                                    <i class="fa-solid ${escapeHtml(chip.icon)}"></i>
                                                                    ${escapeHtml(chip.label)}
                                                                    <button type="button" data-clear-filter="${escapeHtml(chip.key)}">
                                                                        <i class="fa-solid fa-xmark"></i>
                                                                    </button>
                                                                </span>
                                                            `).join('');

                els.activeFilters.querySelectorAll('[data-clear-filter]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        clearSingleFilter(this.getAttribute('data-clear-filter'));
                    });
                });
            }

            function clearSingleFilter(key) {
                if (key === 'q') els.q.value = '';
                if (key === 'type') $(els.type).val(null).trigger('change');
                if (key === 'employee') $(els.employee).val(null).trigger('change');
                if (key === 'customer') $(els.customer).val(null).trigger('change');
                if (key === 'product') $(els.product).val(null).trigger('change');
                if (key === 'department') $(els.department).val(null).trigger('change');
                if (key === 'from') els.from.value = '';
                if (key === 'to') els.to.value = '';

                state.page = 1;
                renderActiveFilters();
                load();
            }

            function clearAllFilters() {
                els.q.value = '';
                els.from.value = '';
                els.to.value = '';

                $(els.type).val(null).trigger('change');
                $(els.employee).val(null).trigger('change');
                $(els.customer).val(null).trigger('change');
                $(els.product).val(null).trigger('change');
                $(els.department).val(null).trigger('change');

                state.page = 1;
                hideSuggestions();
                renderActiveFilters();
                load();
            }

            function renderExistingEmpty(msg) {
                els.existingReports.innerHTML = `<div class="rr-text-muted rr-small">${escapeHtml(msg)}</div>`;
            }

            async function loadSidebarReports(type, id) {
                if (!type || !id) {
                    return renderExistingEmpty('Keine Daten');
                }

                renderExistingEmpty('Lade Berichte…');

                try {
                    const u = new URL(API.recordReports, window.location.origin);
                    u.searchParams.set('type', type);
                    u.searchParams.set('id', id);

                    const res = await fetch(u.toString(), {
                        headers: { 'Accept': 'application/json' }
                    });

                    const j = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        throw new Error(j?.message || 'Fehler');
                    }

                    const rows = Array.isArray(j.rows) ? j.rows : [];

                    if (!rows.length) {
                        return renderExistingEmpty('Keine Berichte vorhanden');
                    }

                    els.existingReports.innerHTML = rows.map(r => {
                        const who = escapeHtml(r.employee_name || r.report_by_name || 'Unbekannt');
                        const employeeId = escapeHtml(r.employee_id || r.report_by || '');
                        const whenRaw = r.created_at || r.report_date || '';
                        const when = escapeHtml(String(whenRaw).replace('T', ' ').slice(0, 16));
                        const txt = escapeHtml(r.report_text || r.report || '—');

                        const due = r.due_date
                            ? `<div class="rr-small rr-text-muted" style="margin-top:6px;">Wiedervorlage: ${escapeHtml(r.due_date)}</div>`
                            : '';

                        const next = r.next_step
                            ? `<div class="rr-small rr-text-muted" style="margin-top:6px;">Nächster Schritt: ${escapeHtml(r.next_step)}</div>`
                            : '';

                        return `
                                                                        <div class="rr-report-item">
                                                                            <div class="rr-report-head">
                                                                                <div>
                                                                                    <div class="rr-report-who">
                                                                                        <i class="fa-solid fa-user-pen rr-me-2"></i>${who}
                                                                                    </div>

                                                                                    ${employeeId ? `<div class="rr-small rr-text-muted">Mitarbeiter-ID: #${employeeId}</div>` : ''}
                                                                                </div>

                                                                                <div class="rr-report-when">${when || '—'}</div>
                                                                            </div>

                                                                            <div class="rr-report-text">${txt}</div>
                                                                            ${due}
                                                                            ${next}
                                                                        </div>
                                                                    `;
                    }).join('');
                } catch (e) {
                    renderExistingEmpty('Fehler beim Laden');

                    showErrorModal({
                        title: 'Berichte konnten nicht geladen werden',
                        subtitle: 'Die vorhandenen Berichte für diesen Eintrag konnten nicht angezeigt werden.',
                        message: e?.message || 'Bitte prüfen Sie die Verbindung oder die Route.'
                    });
                }
            }

            function openSidebar(row) {
                els.addType.value = row.type || '';
                els.addId.value = row.entity_id || '';
                els.addRef.textContent = row.ref_title || '...';
                els.addReport.value = '';
                els.addReportDate.valueAsDate = new Date();
                els.addDueDate.value = '';

                loadSidebarReports(row.type, row.entity_id);

                els.sidebar.classList.add('active');
                els.backdrop.classList.add('active');
            }

            function closeSidebar() {
                els.sidebar.classList.remove('active');
                els.backdrop.classList.remove('active');
            }

            els.closeBtn.addEventListener('click', closeSidebar);
            els.cancelBtn.addEventListener('click', closeSidebar);
            els.backdrop.addEventListener('click', closeSidebar);

            function buildFetchUrl() {
                const u = new URL(API.fetch, window.location.origin);

                const params = {
                    q: (els.q.value || '').trim(),
                    type: els.type.value || '',
                    employee_id: $(els.employee).val() || '',
                    customer_id: $(els.customer).val() || '',
                    product_id: $(els.product).val() || '',
                    department_id: $(els.department).val() || '',
                    date_from: els.from.value || '',
                    date_to: els.to.value || '',
                    sort: 'newest',
                    page: state.page,
                    per_page: state.perPage,
                };

                Object.entries(params).forEach(([k, v]) => {
                    if (v !== '' && v != null) {
                        u.searchParams.set(k, v);
                    }
                });

                return u.toString();
            }

            function renderEmpty(msg) {
                els.tbody.innerHTML = `
                                                                <tr>
                                                                    <td colspan="6" class="rr-text-center" style="padding: 2rem; color: var(--rr-text-muted); text-align:center;">
                                                                        ${escapeHtml(msg)}
                                                                    </td>
                                                                </tr>
                                                            `;
            }

            function render(rows, pagination) {
                const data = Array.isArray(rows) ? rows : [];

                state.currentRows = data;
                state.total = pagination?.total || 0;
                state.hasMore = !!pagination?.has_more;

                const counts = { inquiry: 0, appointment: 0, lead: 0 };

                data.forEach(r => {
                    if (counts[r.type] !== undefined) {
                        counts[r.type]++;
                    }
                });

                els.statTotal.textContent = String(state.total || 0);
                els.statInquiry.textContent = String(counts.inquiry || 0);
                els.statAppointment.textContent = String(counts.appointment || 0);
                els.statLead.textContent = String(counts.lead || 0);

                if (!data.length) {
                    renderEmpty('Keine Ergebnisse gefunden');
                    els.meta.textContent = '0 Einträge';
                    els.prev.disabled = state.page <= 1;
                    els.next.disabled = !state.hasMore;
                    return;
                }

                const start = ((state.page - 1) * state.perPage) + 1;
                const end = Math.min(state.page * state.perPage, state.total);

                els.meta.textContent = `Zeige ${start}-${end} von ${state.total} Einträgen`;
                els.prev.disabled = state.page <= 1;
                els.next.disabled = !state.hasMore;

                els.tbody.innerHTML = data.map((row, reportIndex) => {
                    const b = badgeMeta(row.type);
                    const emp = row.employee_name || row.report_by_name || '—';
                    const av = initials(emp);
                    const created = String(row.created_at || '').replace('T', ' ').slice(0, 16);
                    const report = String(row.report_text || row.report || '');
                    const short = shortReportText(report, 180);
                    const isNew = isNewReport(row);

                    const rowJson = escapeHtml(JSON.stringify({
                        type: row.type || '',
                        entity_id: row.entity_id || row.id || '',
                        ref_title: row.ref_title || row.title || '',
                    }));

                    const addBtn = `
                                                                    <button class="rr-btn-icon rr-add-btn"
                                                                            style="border-color: var(--rr-primary); color: var(--rr-primary);"
                                                                            type="button"
                                                                            data-row="${rowJson}"
                                                                            title="Bericht hinzufügen / ansehen">
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </button>
                                                                `;

                    return `
                                                                    <tr class="${isNew ? 'is-new-report' : ''}">
                                                                        <td style="padding-left: 2rem;">
                                                                            <div class="rr-status-badge ${b.cls}">
                                                                                <i class="fa-solid ${b.icon}"></i>
                                                                                ${b.label}
                                                                            </div>
                                                                        </td>

                                                                        <td>
                                                                            <div class="rr-fw-bold" style="color: var(--rr-text-dark); margin-bottom: 2px;">
                                                                                ${isNew ? `<span class="rr-new-report-star"><i class="fa-solid fa-star"></i></span>` : ''}
                                                                                ${escapeHtml(row.ref_title || row.title || '—')}
                                                                            </div>

                                                                            <div class="rr-small rr-text-muted" style="font-size: 0.75rem;">
                                                                                Bericht-ID: #${escapeHtml(row.report_id || '')}
                                                                            </div>
                                                                        </td>

                                                                        <td>
                                                                            <div class="rr-user-cell">
                                                                                <div class="rr-avatar">${escapeHtml(av)}</div>
                                                                                <span>
                                                                                    <span class="rr-fw-bold rr-small" style="display:block;">${escapeHtml(emp)}</span>
                                                                                    ${row.report_by ? `<span class="rr-small rr-text-muted">ID #${escapeHtml(row.report_by)}</span>` : ''}
                                                                                </span>
                                                                            </div>
                                                                        </td>

                                                                        <td class="rr-text-muted rr-small">${escapeHtml(created || '—')}</td>

                                                                        <td>
                                                                            <button type="button"
                                                                                    class="rr-report-preview-btn"
                                                                                    data-report-detail-index="${reportIndex}"
                                                                                    title="Berichtdetails anzeigen">
                                                                                <span class="rr-report-preview-text">${escapeHtml(short || '—')}</span>
                                                                                <span class="rr-report-preview-more">
                                                                                    <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                                                                                    Details anzeigen
                                                                                </span>
                                                                            </button>

                                                                            <div class="rr-report-meta-line">
                                                                                <span class="rr-current-report-label ${isNew ? 'is-new' : ''}">
                                                                                    <i class="fa-solid ${isNew ? 'fa-star' : 'fa-clock'}"></i>
                                                                                    ${isNew ? 'Neuer Report' : 'Aktueller Report'}
                                                                                </span>

                                                                                <span>
                                                                                    <i class="fa-solid fa-user-pen"></i>
                                                                                    Geschrieben von: <strong>${escapeHtml(emp || 'Unbekannt')}</strong>
                                                                                </span>

                                                                                ${row.report_date ? `
                                                                                    <span>
                                                                                        <i class="fa-solid fa-calendar"></i>
                                                                                        Bericht: ${escapeHtml(row.report_date)}
                                                                                    </span>
                                                                                ` : ''}

                                                                                ${row.due_date ? `
                                                                                    <span>
                                                                                        <i class="fa-solid fa-clock"></i>
                                                                                        Wiedervorlage: ${escapeHtml(row.due_date)}
                                                                                    </span>
                                                                                ` : ''}
                                                                            </div>
                                                                        </td>

                                                                        <td class="rr-text-right" style="padding-right: 2rem;">
                                                                            <button class="rr-btn-icon rr-me-2"
                                                                                    type="button"
                                                                                    data-report-detail-index="${reportIndex}"
                                                                                    title="Berichtdetails anzeigen">
                                                                                <i class="fa-solid fa-eye"></i>
                                                                            </button>

                                                                            <a class="rr-btn-icon rr-me-2"
                                                                               href="${escapeHtml(row.link || '#')}"
                                                                               target="_blank"
                                                                               rel="noopener"
                                                                               title="Datensatz öffnen">
                                                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                                            </a>

                                                                            ${addBtn}
                                                                        </td>
                                                                    </tr>
                                                                `;
                }).join('');

                document.querySelectorAll('.rr-add-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const raw = btn.getAttribute('data-row') || '{}';
                        let row = {};

                        try {
                            row = JSON.parse(raw);
                        } catch (e) { }

                        openSidebar(row);
                    });
                });
            }


            els.tbody.addEventListener('click', function (e) {
                const detailBtn = e.target.closest('[data-report-detail-index]');

                if (!detailBtn) {
                    return;
                }

                const index = Number(detailBtn.getAttribute('data-report-detail-index'));

                if (!Number.isNaN(index) && state.currentRows[index]) {
                    openReportDetailModal(state.currentRows[index]);
                }
            });

            async function load() {
                if (state.loading) {
                    return;
                }

                state.loading = true;

                renderEmpty('Lade Daten...');
                els.meta.textContent = 'Lade Daten...';

                try {
                    const res = await fetch(buildFetchUrl(), {
                        headers: { 'Accept': 'application/json' }
                    });

                    const j = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        throw new Error(j?.message || 'Fehler beim Laden');
                    }

                    render(j.rows || j.items || [], j.pagination || {});
                    renderActiveFilters();
                } catch (e) {
                    renderEmpty('Fehler beim Laden');
                    els.meta.textContent = '';

                    showErrorModal({
                        title: 'Berichte konnten nicht geladen werden',
                        subtitle: 'Die Berichtsliste konnte nicht geladen werden.',
                        message: e?.message || 'Bitte prüfen Sie die Route oder den Controller.'
                    });
                } finally {
                    state.loading = false;
                }
            }

            function debouncedReload() {
                clearTimeout(state.timer);

                state.timer = setTimeout(() => {
                    state.page = 1;
                    load();
                }, 280);
            }

            els.q.addEventListener('input', function () {
                const term = this.value || '';
                renderSmartSuggestions(getSmartSuggestions(term));
                state.page = 1;
                renderActiveFilters();
                debouncedReload();
            });

            els.q.addEventListener('keydown', handleSuggestionKeyboard);

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.rr-smart-search-box')) {
                    hideSuggestions();
                }
            });

            els.type.addEventListener('change', debouncedReload);
            els.from.addEventListener('change', function () {
                state.page = 1;
                renderActiveFilters();
                load();
            });

            els.to.addEventListener('change', function () {
                state.page = 1;
                renderActiveFilters();
                load();
            });

            els.reset.addEventListener('click', clearAllFilters);
            els.clearAll.addEventListener('click', clearAllFilters);

            els.refresh.addEventListener('click', function () {
                load();
                loadEmployeeReportSummary();
            });

            els.enterpriseNavItems?.forEach(btn => {
                btn.addEventListener('click', () => switchEnterpriseView(btn.getAttribute('data-enterprise-view') || 'reports'));
            });

            els.enterpriseSidebarToggle?.addEventListener('click', function () {
                els.enterpriseShell?.classList.toggle('is-collapsed');
            });

            els.employeeSummaryRefresh?.addEventListener('click', loadEmployeeReportSummary);

            els.employeeMainNav?.addEventListener('click', function (event) {
                const tab = event.target.closest('[data-employee-main-tab]');
                if (!tab) return;

                switchEmployeeMainTab(tab.getAttribute('data-employee-main-tab') || 'overview');
            });

            els.employeeSummaryCollapse?.addEventListener('click', function () {
                els.employeeSummaryPanel?.classList.toggle('is-collapsed');
                const collapsed = els.employeeSummaryPanel?.classList.contains('is-collapsed');
                this.innerHTML = collapsed
                    ? '<i class="fa-solid fa-table-columns"></i> Sidebar ausklappen'
                    : '<i class="fa-solid fa-table-columns"></i> Sidebar einklappen';
                if (els.employeeSummaryCollapseMini) {
                    els.employeeSummaryCollapseMini.innerHTML = collapsed
                        ? '<i class="fa-solid fa-angles-right"></i>'
                        : '<i class="fa-solid fa-angles-left"></i>';
                }
            });

            els.employeeSummaryCollapseMini?.addEventListener('click', function () {
                els.employeeSummaryCollapse?.click();
            });

            els.employeeSummaryTabs?.addEventListener('click', function (event) {
                const tab = event.target.closest('.rr-employee-tab');
                if (!tab) return;

                const type = tab.getAttribute('data-summary-type') || 'all';

                state.employeeSummaryActiveType = type;
                state.employeeSummaryDetailType = type;

                renderEmployeeSummaryTabs();
                renderEmployeeSidebar();

                if (state.employeeSummarySelectedId) {
                    loadEmployeeDetailReports(type);
                } else {
                    renderEmployeeSummaryDetail();
                }
            });

            els.employeeSummarySearch?.addEventListener('input', function () {
                state.employeeSummarySearch = this.value || '';
                renderEmployeeSidebar();
            });

            els.employeeNotificationTabs?.addEventListener('click', function (event) {
                const tab = event.target.closest('[data-report-notification-tab]');
                if (!tab) return;

                state.employeeNotificationTab = tab.getAttribute('data-report-notification-tab') || 'new';
                renderEmployeeNotificationPanel();
            });

            els.employeeNotificationList?.addEventListener('click', function (event) {
                const btn = event.target.closest('[data-notification-index]');
                if (!btn) return;

                const index = Number(btn.getAttribute('data-notification-index'));
                const item = state.reportNotifications[index];

                if (item) {
                    openReportDetailModal(normalizeNotificationToRow(item));
                }
            });

            els.employeeReportStrip?.addEventListener('click', function (event) {
                const countBox = event.target.closest('[data-employee-count-type]');
                const card = event.target.closest('.rr-employee-card');

                if (!card) return;

                const employeeId = card.getAttribute('data-employee-id') || '';
                if (!employeeId) return;

                state.employeeSummarySelectedId = employeeId;

                if (countBox) {
                    const type = countBox.getAttribute('data-employee-count-type') || 'all';
                    state.employeeSummaryActiveType = type;
                    loadEmployeeDetailReports(type);
                    return;
                }

                renderEmployeeSidebar();
                loadEmployeeDetailReports(state.employeeSummaryActiveType || 'all');
            });

            els.employeeSummaryDetail?.addEventListener('click', function (event) {
                const detailNotificationTab = event.target.closest('[data-employee-detail-notification-tab]');
                if (detailNotificationTab) {
                    state.employeeDetailNotificationTab = detailNotificationTab.getAttribute('data-employee-detail-notification-tab') || 'new';
                    renderEmployeeSummaryDetail();
                    return;
                }

                const loadBtn = event.target.closest('[data-employee-detail-load-type]');
                if (loadBtn) {
                    const type = loadBtn.getAttribute('data-employee-detail-load-type') || 'all';

                    if (type !== 'all') {
                        state.employeeSummaryActiveType = type;
                    }

                    loadEmployeeDetailReports(type);
                    return;
                }

                const notificationBtn = event.target.closest('[data-notification-index]');
                if (notificationBtn) {
                    const index = Number(notificationBtn.getAttribute('data-notification-index'));
                    const item = state.reportNotifications[index];
                    if (item) {
                        openReportDetailModal(normalizeNotificationToRow(item));
                    }
                    return;
                }

                const reportBtn = event.target.closest('[data-employee-report-index]');
                if (reportBtn) {
                    const index = Number(reportBtn.getAttribute('data-employee-report-index'));

                    if (!Number.isNaN(index) && state.employeeSummaryDetailRows[index]) {
                        openReportDetailModal(state.employeeSummaryDetailRows[index]);
                    }

                    return;
                }

                const filterBtn = event.target.closest('[data-employee-detail-filter]');
                if (filterBtn) {
                    applyEmployeeSummaryFilter(filterBtn.getAttribute('data-employee-detail-filter') || 'all');
                }
            });

            els.prev.addEventListener('click', () => {
                if (state.page <= 1) {
                    return;
                }

                state.page--;
                load();
            });

            els.next.addEventListener('click', () => {
                if (!state.hasMore) {
                    return;
                }

                state.page++;
                load();
            });

            els.addForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const reportValue = String(els.addReport.value || '').trim();

                if (reportValue.length < 3) {
                    showErrorModal({
                        title: 'Bericht ist zu kurz',
                        subtitle: 'Der Bericht braucht mindestens 3 Zeichen.',
                        message: 'Bitte schreiben Sie einen klaren Bericht, bevor Sie speichern.'
                    });

                    els.addReport.focus();
                    return;
                }

                const fd = new FormData(els.addForm);

                els.addSubmit.disabled = true;

                const old = els.addSubmit.innerHTML;
                els.addSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin rr-me-2"></i>Speichere...';

                try {
                    const res = await fetch(API.store, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: fd
                    });

                    const j = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        const error = new Error(j?.message || 'Speichern fehlgeschlagen');
                        error.errors = collectValidationErrors(j);
                        throw error;
                    }

                    const t = els.addType.value;
                    const id = els.addId.value;

                    await loadSidebarReports(t, id);
                    await load();
                    loadEmployeeReportSummary();

                    closeSidebar();
                } catch (err) {
                    showErrorModal({
                        title: 'Bericht ist nicht korrekt',
                        subtitle: 'Der Bericht konnte nicht gespeichert werden.',
                        message: err?.message || 'Bitte prüfen Sie den Bericht und versuchen Sie es erneut.',
                        errors: err?.errors || []
                    });
                } finally {
                    els.addSubmit.disabled = false;
                    els.addSubmit.innerHTML = old;
                }
            });

            renderActiveFilters();
            load();
            loadEmployeeReportSummary();
        });
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
                label: 'Berichte Übersicht',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush