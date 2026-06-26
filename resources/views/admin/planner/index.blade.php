@extends('admin.layouts.app')
@section('title', 'Montage Planung')

@once
    @push('style')
        <meta name="planner-base-url" content="{{ url('/planner') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <style>
            #pmo,
            #pmo * {
                box-sizing: border-box;
            }

            #pmo {
                --pmo-bg: #f3f4f6;
                --pmo-card: #ffffff;
                --pmo-card-soft: #f8fafc;
                --pmo-border: #e5e7eb;
                --pmo-border-strong: #dbe3ea;
                --pmo-text: #0f172a;
                --pmo-muted: #64748b;
                --pmo-soft: #94a3b8;
                --pmo-primary: #93c21c;
                --pmo-primary-dark: #6f9915;
                --pmo-primary-soft: #f4fae7;
                --pmo-blue: #74b2d4;
                --pmo-blue-soft: #eff6ff;
                --pmo-navy: #164191;
                --pmo-green: #10b981;
                --pmo-green-soft: #ecfdf5;
                --pmo-red: #ef4444;
                --pmo-red-soft: #fef2f2;
                --pmo-orange: #f59e0b;
                --pmo-orange-soft: #fff7ed;
                --pmo-purple: #8b5cf6;
                --pmo-purple-soft: #f5f3ff;
                --pmo-shadow: 0 18px 46px -34px rgba(15, 23, 42, .70);
                --pmo-shadow-strong: 0 30px 90px -46px rgba(2, 6, 23, .86);
                --pmo-radius: 22px;
                font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: var(--pmo-text);
                padding: 0 0 34px;
            }

            #pmo .pmo-hidden {
                display: none !important;
            }

            #pmo .pmo-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 16px;
            }

            #pmo .pmo-title {
                margin: 0;
                font-size: 28px;
                line-height: 1.05;
                font-weight: 950;
                letter-spacing: -.035em;
                color: #111827;
            }

            #pmo .pmo-subtitle {
                margin-top: 7px;
                color: var(--pmo-muted);
                font-size: 13px;
                font-weight: 700;
                line-height: 1.45;
                max-width: 820px;
            }

            #pmo .pmo-breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-top: 10px;
                color: var(--pmo-soft);
                font-size: 12px;
                font-weight: 850;
                flex-wrap: wrap;
            }

            #pmo .pmo-breadcrumb a {
                color: var(--pmo-muted);
                text-decoration: none;
            }

            #pmo .pmo-breadcrumb a:hover {
                color: var(--pmo-navy);
            }

            #pmo .pmo-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                background: var(--pmo-card);
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 8px;
                margin-bottom: 16px;
                box-shadow: 0 12px 34px -30px rgba(15, 23, 42, .7);
                position: sticky;
                top: 8px;
                z-index: 40;
            }

            #pmo .pmo-tabs,
            #pmo .pmo-actions,
            #pmo .pmo-view-switch,
            #pmo .pmo-inline {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            #pmo .pmo-tab,
            #pmo .pmo-btn,
            #pmo .pmo-btn-soft,
            #pmo .pmo-icon-btn {
                border: 0;
                outline: 0;
                cursor: pointer;
                font-family: inherit;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: all .18s ease;
                white-space: nowrap;
            }

            #pmo .pmo-tab {
                min-height: 40px;
                padding: 0 15px;
                border-radius: 13px;
                background: transparent;
                color: var(--pmo-muted);
                font-size: 13px;
                font-weight: 950;
                border: 1px solid transparent;
            }

            #pmo .pmo-tab:hover {
                background: var(--pmo-card-soft);
                color: var(--pmo-text);
            }

            #pmo .pmo-tab.is-active {
                color: var(--pmo-primary-dark);
                background: var(--pmo-primary-soft);
                border-color: rgba(147, 194, 28, .38);
            }

            #pmo .pmo-btn {
                min-height: 40px;
                padding: 0 15px;
                border-radius: 13px;
                color: #fff;
                background: var(--pmo-primary);
                font-size: 13px;
                font-weight: 950;
                border: 1px solid var(--pmo-primary);
            }

            #pmo .pmo-btn:hover {
                background: var(--pmo-primary-dark);
                border-color: var(--pmo-primary-dark);
                color: #fff;
                transform: translateY(-1px);
            }

            #pmo .pmo-btn-soft {
                min-height: 40px;
                padding: 0 14px;
                border-radius: 13px;
                color: var(--pmo-text);
                background: #fff;
                border: 1px solid var(--pmo-border);
                font-size: 13px;
                font-weight: 950;
            }

            #pmo .pmo-btn-soft:hover {
                border-color: var(--pmo-blue);
                background: var(--pmo-blue-soft);
                color: var(--pmo-navy);
                transform: translateY(-1px);
            }

            #pmo .pmo-icon-btn {
                width: 38px;
                height: 38px;
                border-radius: 13px;
                color: var(--pmo-muted);
                background: #fff;
                border: 1px solid var(--pmo-border);
            }

            #pmo .pmo-icon-btn:hover,
            #pmo .pmo-icon-btn.is-active {
                color: var(--pmo-primary-dark);
                background: var(--pmo-primary-soft);
                border-color: rgba(147, 194, 28, .45);
            }

            #pmo .pmo-icon-btn.pmo-danger:hover {
                color: #b91c1c;
                background: var(--pmo-red-soft);
                border-color: rgba(239, 68, 68, .25);
            }

            #pmo .pmo-card {
                background: var(--pmo-card);
                border: 1px solid var(--pmo-border-strong);
                border-radius: var(--pmo-radius);
                box-shadow: var(--pmo-shadow);
                overflow: hidden;
            }

            #pmo .pmo-card-pad {
                padding: 18px;
            }

            #pmo .pmo-project-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.55fr) minmax(320px, .8fr);
                gap: 16px;
                margin-bottom: 16px;
            }

            #pmo .pmo-hero {
                position: relative;
                overflow: hidden;
                min-height: 230px;
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 56%, #f4fae7 100%);
            }

            #pmo .pmo-hero::before,
            #pmo .pmo-hero::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                pointer-events: none;
            }

            #pmo .pmo-hero::before {
                right: -90px;
                top: -100px;
                width: 260px;
                height: 260px;
                background: rgba(147, 194, 28, .16);
            }

            #pmo .pmo-hero::after {
                left: -90px;
                bottom: -120px;
                width: 260px;
                height: 260px;
                background: rgba(116, 178, 212, .14);
            }

            #pmo .pmo-hero-inner {
                position: relative;
                z-index: 1;
                padding: 22px;
                display: grid;
                grid-template-columns: 76px minmax(0, 1fr);
                gap: 16px;
            }

            #pmo .pmo-avatar {
                width: 76px;
                height: 76px;
                border-radius: 24px;
                background: var(--pmo-navy);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 22px 42px -26px rgba(22, 65, 145, .9);
            }

            #pmo .pmo-kicker {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 950;
                letter-spacing: .06em;
                text-transform: uppercase;
            }

            #pmo .pmo-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border-radius: 999px;
                padding: 5px 9px;
                font-size: 11px;
                font-weight: 950;
                color: var(--pmo-navy);
                background: var(--pmo-blue-soft);
                border: 1px solid #dbeafe;
            }

            #pmo .pmo-chip.green {
                color: #047857;
                background: var(--pmo-green-soft);
                border-color: #bbf7d0;
            }

            #pmo .pmo-chip.orange {
                color: #c2410c;
                background: var(--pmo-orange-soft);
                border-color: #fed7aa;
            }

            #pmo .pmo-chip.red {
                color: #b91c1c;
                background: var(--pmo-red-soft);
                border-color: #fecaca;
            }

            #pmo .pmo-chip.gray {
                color: var(--pmo-muted);
                background: #f8fafc;
                border-color: var(--pmo-border);
            }

            #pmo .pmo-customer-name {
                margin-top: 8px;
                font-size: 27px;
                line-height: 1.12;
                font-weight: 950;
                color: var(--pmo-text);
                letter-spacing: -.025em;
            }

            #pmo .pmo-customer-sub {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 8px;
                color: var(--pmo-muted);
                font-size: 13px;
                font-weight: 750;
            }

            #pmo .pmo-info-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
                margin-top: 17px;
            }

            #pmo .pmo-info-box {
                background: rgba(255, 255, 255, .78);
                border: 1px solid rgba(226, 232, 240, .96);
                border-radius: 16px;
                padding: 13px;
                min-width: 0;
            }

            #pmo .pmo-label {
                display: block;
                font-size: 10px;
                font-weight: 950;
                color: var(--pmo-soft);
                letter-spacing: .07em;
                text-transform: uppercase;
                margin-bottom: 6px;
            }

            #pmo .pmo-value {
                color: var(--pmo-text);
                font-size: 13px;
                font-weight: 950;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pmo .pmo-note {
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 750;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                margin-top: 4px;
            }

            #pmo .pmo-control-card {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 18px;
            }

            #pmo .pmo-control-title {
                color: var(--pmo-text);
                font-size: 13px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            #pmo .pmo-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
                min-width: 0;
            }

            #pmo .pmo-input,
            #pmo .pmo-select,
            #pmo .pmo-textarea {
                width: 100%;
                min-height: 42px;
                border: 1px solid var(--pmo-border);
                background: #f8fafc;
                color: var(--pmo-text);
                border-radius: 13px;
                padding: 10px 12px;
                outline: 0;
                font: 800 13px/1.3 Inter, system-ui, sans-serif;
                transition: all .16s ease;
            }

            #pmo .pmo-textarea {
                min-height: 92px;
                resize: vertical;
            }

            #pmo .pmo-input:focus,
            #pmo .pmo-select:focus,
            #pmo .pmo-textarea:focus {
                background: #fff;
                border-color: var(--pmo-primary);
                box-shadow: 0 0 0 4px rgba(147, 194, 28, .14);
            }

            #pmo .pmo-summary-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
            }

            #pmo .pmo-summary-card {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 14px;
                box-shadow: 0 14px 30px -28px rgba(15, 23, 42, .65);
            }

            #pmo .pmo-summary-card strong {
                display: block;
                margin-top: 5px;
                font-size: 25px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-summary-card span {
                color: var(--pmo-soft);
                font-size: 10px;
                font-weight: 950;
                letter-spacing: .08em;
                text-transform: uppercase;
            }

            #pmo .pmo-main-card {
                min-height: 720px;
            }

            #pmo .pmo-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 16px 18px;
                background: linear-gradient(135deg, #ffffff, #f8fafc);
                border-bottom: 1px solid var(--pmo-border);
            }

            #pmo .pmo-section-title {
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 0;
                font-size: 15px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-section-title::before {
                content: "";
                width: 8px;
                height: 26px;
                border-radius: 999px;
                background: var(--pmo-primary);
            }

            #pmo .pmo-section-sub {
                margin-top: 3px;
                color: var(--pmo-muted);
                font-size: 12px;
                font-weight: 750;
            }

            #pmo .pmo-workspace {
                padding: 16px;
                background: #f8fafc;
                min-height: 650px;
            }

            #pmo .pmo-board {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(318px, 1fr));
                gap: 14px;
                align-items: start;
            }

            #pmo .pmo-employee-column {
                min-width: 0;
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 22px;
                box-shadow: 0 20px 44px -36px rgba(15, 23, 42, .75);
                overflow: hidden;
            }

            #pmo .pmo-employee-head {
                padding: 15px;
                background: linear-gradient(135deg, #ffffff, #f8fafc);
                border-bottom: 1px solid #edf2f7;
            }

            #pmo .pmo-employee-profile {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                min-width: 0;
            }

            #pmo .pmo-person-left {
                display: flex;
                align-items: center;
                gap: 11px;
                min-width: 0;
            }

            #pmo .pmo-person-left img,
            #pmo .pmo-photo {
                width: 48px;
                height: 48px;
                border-radius: 16px;
                object-fit: cover;
                background: #e2e8f0;
                border: 2px solid #fff;
                box-shadow: 0 8px 20px -14px rgba(15, 23, 42, .85);
                flex: 0 0 auto;
            }

            #pmo .pmo-person-name {
                font-size: 14px;
                font-weight: 950;
                color: var(--pmo-text);
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pmo .pmo-role-row {
                display: flex;
                align-items: center;
                gap: 5px;
                flex-wrap: wrap;
                margin-top: 4px;
            }

            #pmo .pmo-role {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                border-radius: 999px;
                padding: 3px 7px;
                font-size: 9px;
                font-weight: 950;
                letter-spacing: .03em;
                text-transform: uppercase;
                border: 1px solid var(--pmo-border);
                color: var(--pmo-muted);
                background: #f8fafc;
            }

            #pmo .pmo-role.pm {
                color: var(--pmo-navy);
                background: var(--pmo-blue-soft);
                border-color: #bfdbfe;
            }

            #pmo .pmo-role.field {
                color: #c2410c;
                background: var(--pmo-orange-soft);
                border-color: #fed7aa;
            }

            #pmo .pmo-role.team {
                color: var(--pmo-primary-dark);
                background: var(--pmo-primary-soft);
                border-color: #d9efaa;
            }

            #pmo .pmo-role.work {
                color: #475569;
                background: #f8fafc;
                border-color: #e5e7eb;
            }

            #pmo .pmo-mini-stats {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 7px;
                margin-top: 14px;
            }

            #pmo .pmo-mini-stat {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                min-height: 33px;
                border-radius: 13px;
                background: #f8fafc;
                border: 1px solid var(--pmo-border);
                color: #475569;
                font-size: 11px;
                font-weight: 950;
            }

            #pmo .pmo-items {
                min-height: 360px;
                padding: 12px;
                display: flex;
                flex-direction: column;
                gap: 10px;
                background: #f8fafc;
            }

            #pmo .pmo-work-card {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 13px;
                cursor: pointer;
                transition: all .18s ease;
                box-shadow: 0 12px 30px -28px rgba(15, 23, 42, .75);
            }

            #pmo .pmo-work-card:hover {
                border-color: rgba(147, 194, 28, .75);
                box-shadow: 0 24px 48px -32px rgba(15, 23, 42, .85);
                transform: translateY(-1px);
            }

            #pmo .pmo-work-top {
                display: flex;
                gap: 11px;
                align-items: flex-start;
            }

            #pmo .pmo-type-icon {
                width: 42px;
                height: 42px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            #pmo .pmo-type-icon.kanban_task {
                background: var(--pmo-blue-soft);
                color: #2563eb;
            }

            #pmo .pmo-type-icon.personal_task {
                background: var(--pmo-orange-soft);
                color: #ea580c;
            }

            #pmo .pmo-type-icon.appointment {
                background: var(--pmo-purple-soft);
                color: #7c3aed;
            }

            #pmo .pmo-type-icon.ticket {
                background: var(--pmo-red-soft);
                color: #e11d48;
            }

            #pmo .pmo-type-icon.phase_activity,
            #pmo .pmo-type-icon.task_phase {
                background: var(--pmo-green-soft);
                color: #047857;
            }

            #pmo .pmo-work-title {
                font-size: 13px;
                font-weight: 950;
                line-height: 1.3;
                color: var(--pmo-text);
                margin-bottom: 5px;
            }

            #pmo .pmo-work-desc {
                font-size: 12px;
                font-weight: 700;
                line-height: 1.38;
                color: var(--pmo-muted);
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            #pmo .pmo-work-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-top: 11px;
                padding-top: 10px;
                border-top: 1px solid #f1f5f9;
            }



            #pmo .pmo-status-inline {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            #pmo .pmo-status-select {
                min-height: 30px;
                max-width: 138px;
                border-radius: 999px;
                border: 1px solid var(--pmo-border);
                background: #fff;
                color: var(--pmo-text);
                font: 950 11px/1.2 Inter, system-ui, sans-serif;
                padding: 4px 26px 4px 9px;
                cursor: pointer;
                outline: 0;
            }

            #pmo .pmo-status-select:focus {
                border-color: var(--pmo-primary);
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .14);
            }

            #pmo .pmo-status-box {
                display: grid;
                gap: 10px;
            }

            #pmo .pmo-status-current {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 11px;
                border-radius: 16px;
                border: 1px solid var(--pmo-border);
                background: #f8fafc;
            }

            #pmo .pmo-status-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            #pmo .pmo-status-buttons .pmo-btn-soft {
                min-height: 34px;
                padding: 0 11px;
                border-radius: 999px;
                font-size: 12px;
            }

            #pmo .pmo-empty {
                border: 1px dashed #cbd5e1;
                border-radius: 18px;
                background: #fff;
                color: var(--pmo-soft);
                font-size: 12px;
                font-weight: 850;
                text-align: center;
                padding: 28px 14px;
            }

            #pmo .pmo-gantt {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 22px;
                overflow: auto;
                height: 650px;
            }

            #pmo .pmo-gantt-inner {
                min-width: 1120px;
                height: 100%;
            }

            #pmo .pmo-gantt-head {
                display: grid;
                grid-template-columns: 240px 1fr;
                height: 52px;
                background: linear-gradient(135deg, #f8fafc, #eff6ff);
                border-bottom: 1px solid #dbeafe;
            }

            #pmo .pmo-gantt-left-head {
                display: flex;
                align-items: center;
                padding: 0 16px;
                color: #475569;
                font-size: 11px;
                font-weight: 950;
                text-transform: uppercase;
                border-right: 1px solid #dbeafe;
            }

            #pmo .pmo-gantt-scale {
                position: relative;
                height: 52px;
            }

            #pmo .pmo-gantt-hour {
                position: absolute;
                top: 0;
                bottom: 0;
                border-left: 1px solid #dbeafe;
                padding: 17px 0 0 8px;
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 950;
            }

            #pmo .pmo-gantt-row {
                display: grid;
                grid-template-columns: 240px 1fr;
                min-height: 78px;
                border-bottom: 1px solid #edf2f7;
            }

            #pmo .pmo-gantt-person {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 14px;
                background: #fff;
                border-right: 1px solid #edf2f7;
                min-width: 0;
            }

            #pmo .pmo-gantt-person img {
                width: 38px;
                height: 38px;
                border-radius: 13px;
                object-fit: cover;
            }

            #pmo .pmo-gantt-person strong {
                display: block;
                max-width: 160px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 12px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-gantt-person span {
                display: block;
                margin-top: 2px;
                font-size: 10px;
                font-weight: 900;
                color: var(--pmo-soft);
            }

            #pmo .pmo-gantt-line {
                position: relative;
                min-height: 78px;
                background: linear-gradient(90deg, #f8fafc 0, #f8fafc 49%, #fff 50%, #fff 100%);
                background-size: 120px 100%;
            }

            #pmo .pmo-gantt-bar {
                position: absolute;
                top: 14px;
                height: 50px;
                border-radius: 15px;
                background: #fff;
                border: 1px solid #bfdbfe;
                box-shadow: 0 18px 36px -26px rgba(15, 23, 42, .75);
                display: flex;
                align-items: center;
                gap: 9px;
                padding: 0 12px;
                overflow: hidden;
                cursor: pointer;
            }

            #pmo .pmo-gantt-bar strong {
                display: block;
                max-width: 220px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 12px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-gantt-bar small {
                display: block;
                font-size: 10px;
                font-weight: 850;
                color: var(--pmo-muted);
            }

            #pmo .pmo-list {
                display: grid;
                gap: 12px;
            }

            #pmo .pmo-list-day {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 20px;
                box-shadow: 0 16px 34px -30px rgba(15, 23, 42, .65);
                overflow: hidden;
            }

            #pmo .pmo-list-day summary {
                cursor: pointer;
                list-style: none;
                padding: 15px 16px;
                background: linear-gradient(135deg, #fff, #f8fafc);
                font-size: 13px;
                font-weight: 950;
                display: flex;
                justify-content: space-between;
                gap: 12px;
            }

            #pmo .pmo-list-day summary::-webkit-details-marker {
                display: none;
            }

            #pmo .pmo-list-row {
                display: grid;
                grid-template-columns: 110px minmax(0, 1fr) 180px 160px;
                gap: 14px;
                align-items: center;
                padding: 13px 16px;
                border-top: 1px solid #f1f5f9;
            }

            #pmo .pmo-list-time {
                color: var(--pmo-navy);
                font-size: 12px;
                font-weight: 950;
            }

            #pmo .pmo-team-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
                gap: 12px;
            }

            #pmo .pmo-team-card {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 20px;
                padding: 15px;
                box-shadow: 0 16px 34px -30px rgba(15, 23, 42, .7);
                display: flex;
                gap: 12px;
                align-items: flex-start;
            }

            #pmo .pmo-team-card-body {
                min-width: 0;
                flex: 1;
            }

            #pmo .pmo-team-card-actions {
                display: flex;
                gap: 7px;
                align-items: center;
                flex-wrap: wrap;
                margin-top: 12px;
            }

            #pmo .pmo-history {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 22px;
                padding: 18px;
                min-height: 600px;
                overflow: auto;
            }

            #pmo .pmo-history-line {
                position: relative;
                padding-left: 30px;
                margin-bottom: 16px;
            }

            #pmo .pmo-history-line::before {
                content: "";
                position: absolute;
                left: 8px;
                top: 5px;
                width: 12px;
                height: 12px;
                border-radius: 999px;
                background: var(--pmo-primary);
                box-shadow: 0 0 0 5px var(--pmo-primary-soft);
            }

            #pmo .pmo-history-line::after {
                content: "";
                position: absolute;
                left: 13px;
                top: 24px;
                width: 2px;
                height: calc(100% + 4px);
                background: #e5e7eb;
            }

            #pmo .pmo-history-line:last-child::after {
                display: none;
            }

            #pmo .pmo-history-card {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 14px;
                box-shadow: 0 14px 30px -28px rgba(15, 23, 42, .7);
            }

            #pmo .pmo-history-title {
                font-size: 13px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-history-meta {
                margin-top: 4px;
                font-size: 11px;
                font-weight: 850;
                color: var(--pmo-soft);
            }

            #pmo .pmo-history-note {
                margin-top: 8px;
                padding: 9px 10px;
                border-radius: 13px;
                background: #f8fafc;
                border: 1px solid var(--pmo-border);
                color: var(--pmo-muted);
                font-size: 12px;
                font-weight: 750;
                line-height: 1.45;
            }

            #pmo .pmo-settings-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 13px;
            }

            #pmo .pmo-settings-card {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 20px;
                padding: 16px;
                box-shadow: 0 16px 34px -30px rgba(15, 23, 42, .7);
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }

            #pmo .pmo-settings-icon {
                width: 46px;
                height: 46px;
                border-radius: 16px;
                background: var(--pmo-primary-soft);
                color: var(--pmo-primary-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            #pmo .pmo-settings-title {
                font-size: 14px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-settings-text {
                margin-top: 5px;
                color: var(--pmo-muted);
                font-size: 12px;
                font-weight: 750;
                line-height: 1.45;
            }

            #pmo .pmo-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 100000;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background: rgba(15, 23, 42, .58);
                backdrop-filter: blur(8px);
            }

            #pmo .pmo-modal-backdrop.is-open {
                display: flex;
            }

            #pmo .pmo-modal {
                width: min(920px, 100%);
                max-height: 92vh;
                overflow: hidden;
                background: #fff;
                border: 1px solid rgba(226, 232, 240, .96);
                border-radius: 28px;
                box-shadow: var(--pmo-shadow-strong);
                display: flex;
                flex-direction: column;
            }

            #pmo .pmo-modal-sm {
                width: min(560px, 100%);
            }

            #pmo .pmo-modal-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
                padding: 20px 22px;
                background: linear-gradient(135deg, #ffffff, #f8fafc);
                border-bottom: 1px solid var(--pmo-border);
            }

            #pmo .pmo-modal-title {
                margin: 0;
                display: flex;
                align-items: center;
                gap: 10px;
                color: var(--pmo-text);
                font-size: 18px;
                font-weight: 950;
                letter-spacing: -.02em;
            }

            #pmo .pmo-modal-sub {
                margin-top: 5px;
                color: var(--pmo-muted);
                font-size: 12px;
                font-weight: 750;
                line-height: 1.45;
            }

            #pmo .pmo-modal-body {
                padding: 20px 22px;
                overflow: auto;
            }

            #pmo .pmo-modal-foot {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                padding: 16px 22px;
                background: #f8fafc;
                border-top: 1px solid var(--pmo-border);
                flex-wrap: wrap;
            }

            #pmo .pmo-modal-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }

            #pmo .pmo-span-2 {
                grid-column: 1 / -1;
            }

            #pmo .pmo-toast-wrap {
                position: fixed;
                right: 18px;
                bottom: 18px;
                z-index: 110000;
                display: grid;
                gap: 10px;
            }

            #pmo .pmo-toast {
                width: min(380px, calc(100vw - 36px));
                display: flex;
                gap: 11px;
                align-items: flex-start;
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 13px;
                box-shadow: var(--pmo-shadow-strong);
            }

            #pmo .pmo-toast strong {
                display: block;
                font-size: 13px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-toast span {
                display: block;
                margin-top: 3px;
                color: var(--pmo-muted);
                font-size: 12px;
                font-weight: 750;
            }


            /* =========================================================
                    PMO Material Request Inbox
                    ========================================================= */
            #pmo .pmo-material-request-inbox {
                display: grid;
                gap: 14px;
            }

            #pmo .pmo-request-employee-card {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 22px;
                box-shadow: 0 18px 42px -34px rgba(15, 23, 42, .72);
                overflow: hidden;
            }

            #pmo .pmo-request-employee-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 14px 16px;
                background: linear-gradient(135deg, #ffffff, #fff7ed);
                border-bottom: 1px solid #fed7aa;
            }

            #pmo .pmo-request-employee-left {
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 0;
            }

            #pmo .pmo-request-avatar {
                width: 42px;
                height: 42px;
                border-radius: 15px;
                background: var(--pmo-orange-soft);
                color: #c2410c;
                border: 1px solid #fed7aa;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
                font-size: 13px;
                font-weight: 950;
            }

            #pmo .pmo-request-employee-name {
                color: var(--pmo-text);
                font-size: 14px;
                font-weight: 950;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pmo .pmo-request-employee-meta {
                margin-top: 3px;
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 850;
            }

            #pmo .pmo-request-table-wrap {
                overflow: auto;
            }

            #pmo .pmo-request-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            #pmo .pmo-request-table th,
            #pmo .pmo-request-table td {
                padding: 10px 9px;
                border-bottom: 1px solid #f1f5f9;
                text-align: left;
                vertical-align: top;
            }

            #pmo .pmo-request-table th {
                color: var(--pmo-soft);
                text-transform: uppercase;
                letter-spacing: .06em;
                font-size: 10px;
                font-weight: 950;
                background: #fff;
                position: sticky;
                top: 0;
                z-index: 1;
            }

            #pmo .pmo-request-material-title {
                color: var(--pmo-text);
                font-weight: 950;
            }

            #pmo .pmo-request-note {
                margin-top: 4px;
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 750;
                line-height: 1.35;
            }

            #pmo .pmo-request-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 7px;
                flex-wrap: wrap;
            }

            #pmo .pmo-request-row.is-rejected {
                opacity: .68;
                background: #fef2f2;
            }

            #pmo .pmo-request-row.is-accepted {
                background: #ecfdf5;
            }

            #pmo .pmo-request-row.is-open {
                background: #fffaf3;
            }


            @media (max-width: 1180px) {
                #pmo .pmo-project-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 820px) {

                #pmo .pmo-header,
                #pmo .pmo-topbar,
                #pmo .pmo-toolbar {
                    flex-direction: column;
                    align-items: stretch;
                }

                #pmo .pmo-tabs,
                #pmo .pmo-actions,
                #pmo .pmo-view-switch {
                    width: 100%;
                }

                #pmo .pmo-tab,
                #pmo .pmo-btn,
                #pmo .pmo-btn-soft {
                    flex: 1;
                }

                #pmo .pmo-hero-inner {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-info-grid,
                #pmo .pmo-summary-grid,
                #pmo .pmo-modal-grid {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-list-row {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-main-card {
                    min-height: 620px;
                }

                #pmo .pmo-workspace {
                    min-height: 540px;
                }

                #pmo .pmo-gantt {
                    height: 560px;
                }
            }


            /* PMO upgrade: compact workload, side detail drawer, bulk work creation */
            #pmo .pmo-actions-wide {
                align-items: center;
                justify-content: flex-end;
            }

            #pmo .pmo-board-filter {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                padding: 6px;
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 16px;
            }

            #pmo .pmo-board-filter .pmo-input,
            #pmo .pmo-board-filter .pmo-select {
                min-height: 36px;
                border-radius: 12px;
                font-size: 12px;
                min-width: 150px;
            }

            #pmo .pmo-board-filter .pmo-input {
                min-width: 220px;
            }

            #pmo .pmo-board-filter .pmo-check {
                min-height: 36px;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 0 10px;
                border-radius: 12px;
                border: 1px solid var(--pmo-border);
                background: #f8fafc;
                font-size: 12px;
                font-weight: 950;
                color: var(--pmo-muted);
                cursor: pointer;
            }

            #pmo .pmo-board {
                grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            }

            #pmo .pmo-employee-column {
                max-height: 760px;
                display: flex;
                flex-direction: column;
            }

            #pmo .pmo-employee-head {
                flex: 0 0 auto;
            }

            #pmo .pmo-items {
                min-height: 220px;
                max-height: 610px;
                overflow: auto;
                scrollbar-width: thin;
            }

            #pmo .pmo-column-tools {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-top: 10px;
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 900;
            }

            #pmo .pmo-employee-schedule {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 8px;
                align-items: center;
                margin-top: 10px;
                padding: 10px 11px;
                border-radius: 15px;
                background: linear-gradient(135deg, #f8fafc, #eff6ff);
                border: 1px solid #dbeafe;
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 950;
            }

            #pmo .pmo-employee-schedule strong {
                color: var(--pmo-navy);
                font-size: 12px;
                white-space: nowrap;
            }

            #pmo .pmo-date-group {
                display: grid;
                gap: 9px;
            }

            #pmo .pmo-date-group+.pmo-date-group {
                margin-top: 8px;
            }

            #pmo .pmo-date-head {
                position: sticky;
                top: 0;
                z-index: 2;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 8px 10px;
                border-radius: 14px;
                background: rgba(248, 250, 252, .96);
                border: 1px solid var(--pmo-border);
                backdrop-filter: blur(10px);
                color: var(--pmo-text);
                font-size: 11px;
                font-weight: 950;
            }

            #pmo .pmo-date-head span {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                min-width: 0;
            }

            #pmo .pmo-date-head small {
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 950;
                white-space: nowrap;
            }

            #pmo .pmo-work-timebar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 9px;
                padding: 7px 9px;
                border-radius: 13px;
                background: var(--pmo-primary-soft);
                border: 1px solid rgba(147, 194, 28, .28);
                color: var(--pmo-primary-dark);
                font-size: 10px;
                font-weight: 950;
            }

            #pmo .pmo-work-timebar span,
            #pmo .pmo-work-timebar strong {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                white-space: nowrap;
            }

            #pmo .pmo-work-card {
                position: relative;
                padding: 11px;
            }

            #pmo .pmo-work-card::after {
                content: "";
                position: absolute;
                inset: 0;
                border-radius: 18px;
                pointer-events: none;
                opacity: 0;
                box-shadow: inset 0 0 0 2px rgba(147, 194, 28, .35);
                transition: opacity .16s ease;
            }

            #pmo .pmo-work-card:hover::after {
                opacity: 1;
            }

            #pmo .pmo-work-card.is-done {
                opacity: .68;
            }

            #pmo .pmo-work-progress {
                height: 7px;
                overflow: hidden;
                border-radius: 999px;
                background: #e5e7eb;
                margin-top: 10px;
            }

            #pmo .pmo-work-progress>span {
                display: block;
                height: 100%;
                border-radius: 999px;
                background: var(--pmo-primary);
            }

            #pmo .pmo-done-meta {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
                margin-top: 9px;
                padding: 8px 9px;
                border-radius: 13px;
                background: var(--pmo-green-soft);
                border: 1px solid #bbf7d0;
                color: #047857;
                font-size: 10px;
                font-weight: 950;
            }

            #pmo .pmo-history-mini {
                display: grid;
                gap: 8px;
            }

            #pmo .pmo-history-mini-row {
                border: 1px solid var(--pmo-border);
                border-radius: 14px;
                background: #fff;
                padding: 9px 10px;
                font-size: 12px;
                font-weight: 850;
                color: var(--pmo-text);
            }

            #pmo .pmo-history-mini-row small {
                display: block;
                margin-top: 3px;
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 900;
            }

            #pmo .pmo-gantt-gap {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 900;
            }


            #pmo .pmo-pill-row {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 6px;
            }

            #pmo .pmo-more-row {
                padding: 0 12px 12px;
            }

            #pmo .pmo-drawer-backdrop {
                position: fixed;
                inset: 0;
                z-index: 2400;
                background: rgba(15, 23, 42, .36);
                opacity: 0;
                visibility: hidden;
                transition: .2s ease;
            }

            #pmo .pmo-drawer-backdrop.is-open {
                opacity: 1;
                visibility: visible;
            }

            #pmo .pmo-job-drawer {
                position: fixed;
                top: 0;
                right: 0;
                width: min(720px, 96vw);
                height: 100vh;
                z-index: 2401;
                background: #f8fafc;
                border-left: 1px solid #dbeafe;
                box-shadow: -26px 0 86px rgba(15, 23, 42, .28);
                transform: translateX(105%);
                transition: transform .24s ease;
                display: flex;
                flex-direction: column;
            }

            #pmo .pmo-job-drawer.is-open {
                transform: translateX(0);
            }

            #pmo .pmo-job-head {
                padding: 18px;
                border-bottom: 1px solid #dbeafe;
                background: linear-gradient(135deg, #ffffff, #eff6ff);
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
            }

            #pmo .pmo-job-title {
                margin: 8px 0 0;
                font-size: 21px;
                line-height: 1.2;
                font-weight: 950;
                letter-spacing: -.02em;
                color: var(--pmo-text);
            }

            #pmo .pmo-job-meta {
                margin-top: 8px;
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }

            #pmo .pmo-job-body {
                padding: 16px 18px 22px;
                overflow: auto;
                flex: 1;
            }

            #pmo .pmo-detail-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            #pmo .pmo-detail-card {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 14px;
                box-shadow: 0 16px 32px -30px rgba(15, 23, 42, .65);
            }

            #pmo .pmo-detail-card.pmo-span-2 {
                grid-column: 1 / -1;
            }

            #pmo .pmo-detail-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 12px;
                font-weight: 950;
                color: var(--pmo-muted);
                letter-spacing: .05em;
                text-transform: uppercase;
                margin-bottom: 10px;
            }



            /* PMO drawer tabs: each task part is separated inside the sidebar */
            #pmo .pmo-job-tabs {
                position: sticky;
                top: -16px;
                z-index: 8;
                display: flex;
                gap: 8px;
                overflow-x: auto;
                padding: 0 0 12px;
                margin-bottom: 12px;
                background: #f8fafc;
                scrollbar-width: thin;
            }

            #pmo .pmo-job-tab {
                min-height: 38px;
                border: 1px solid var(--pmo-border);
                border-radius: 999px;
                background: #fff;
                color: var(--pmo-muted);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                padding: 0 12px;
                font-family: inherit;
                font-size: 12px;
                font-weight: 950;
                cursor: pointer;
                white-space: nowrap;
                transition: all .16s ease;
            }

            #pmo .pmo-job-tab:hover,
            #pmo .pmo-job-tab.is-active {
                color: var(--pmo-primary-dark);
                background: var(--pmo-primary-soft);
                border-color: rgba(147, 194, 28, .45);
                transform: translateY(-1px);
            }

            #pmo .pmo-job-tab-count {
                min-width: 20px;
                height: 20px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0 6px;
                background: #f1f5f9;
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 950;
            }

            #pmo .pmo-job-tab.is-active .pmo-job-tab-count {
                background: #fff;
                color: var(--pmo-primary-dark);
            }

            #pmo .pmo-job-panel {
                display: none;
            }

            #pmo .pmo-job-panel.is-active {
                display: block;
            }

            #pmo .pmo-job-panel .pmo-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            @media(max-width: 900px) {
                #pmo .pmo-job-panel .pmo-detail-grid {
                    grid-template-columns: 1fr;
                }
            }

            #pmo .pmo-team-list {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            #pmo .pmo-team-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: 1px solid var(--pmo-border);
                border-radius: 999px;
                background: #f8fafc;
                padding: 6px 9px 6px 6px;
                font-size: 12px;
                font-weight: 900;
                color: var(--pmo-text);
            }

            #pmo .pmo-team-pill img {
                width: 24px;
                height: 24px;
                border-radius: 999px;
                object-fit: cover;
            }

            #pmo .pmo-tabstrip {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin: 14px 0;
            }

            #pmo .pmo-tabstrip button {
                border: 1px solid var(--pmo-border);
                border-radius: 999px;
                min-height: 34px;
                padding: 0 12px;
                background: #fff;
                color: var(--pmo-muted);
                font-weight: 950;
                font-size: 12px;
                cursor: pointer;
            }

            #pmo .pmo-tabstrip button.is-active {
                color: var(--pmo-primary-dark);
                background: var(--pmo-primary-soft);
                border-color: rgba(147, 194, 28, .45);
            }

            #pmo .pmo-gallery-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
            }

            #pmo .pmo-gallery-card {
                min-height: 92px;
                border: 1px dashed #cbd5e1;
                border-radius: 16px;
                background: #fff;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--pmo-soft);
                font-size: 11px;
                font-weight: 950;
                text-align: center;
            }

            #pmo .pmo-gallery-card img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            #pmo .pmo-material-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            #pmo .pmo-material-table th,
            #pmo .pmo-material-table td {
                padding: 9px 7px;
                border-bottom: 1px solid #eef2f7;
                text-align: left;
            }

            #pmo .pmo-material-table th {
                color: var(--pmo-soft);
                text-transform: uppercase;
                font-size: 10px;
                font-weight: 950;
            }

            #pmo .pmo-step-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            #pmo .pmo-step-item {
                display: grid;
                grid-template-columns: 30px minmax(0, 1fr) auto;
                gap: 10px;
                align-items: center;
                border: 1px solid var(--pmo-border);
                border-radius: 15px;
                padding: 10px;
                background: #fff;
            }

            #pmo .pmo-step-number {
                width: 30px;
                height: 30px;
                border-radius: 10px;
                background: var(--pmo-primary-soft);
                color: var(--pmo-primary-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 950;
            }

            #pmo .pmo-work-mode {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                margin-bottom: 14px;
            }

            #pmo .pmo-mode-card {
                border: 1px solid var(--pmo-border);
                border-radius: 16px;
                padding: 12px;
                background: #fff;
                cursor: pointer;
                display: flex;
                gap: 10px;
                align-items: flex-start;
            }

            #pmo .pmo-mode-card input {
                margin-top: 3px;
            }

            #pmo .pmo-mode-card strong {
                display: block;
                color: var(--pmo-text);
                font-size: 13px;
                font-weight: 950;
            }

            #pmo .pmo-mode-card span {
                display: block;
                color: var(--pmo-muted);
                font-size: 11px;
                line-height: 1.35;
                margin-top: 3px;
                font-weight: 750;
            }

            #pmo .pmo-mode-card:has(input:checked) {
                border-color: rgba(147, 194, 28, .55);
                background: var(--pmo-primary-soft);
            }

            #pmo .pmo-bulk-area {
                margin-top: 16px;
                border: 1px solid #dbeafe;
                background: #f8fbff;
                border-radius: 18px;
                padding: 12px;
            }

            #pmo .pmo-bulk-head {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                align-items: center;
                margin-bottom: 10px;
            }

            #pmo .pmo-bulk-step {
                display: grid;
                grid-template-columns: minmax(180px, 1.2fr) minmax(120px, .8fr) minmax(110px, .6fr) 38px;
                gap: 8px;
                margin-bottom: 8px;
                align-items: center;
            }



            #pmo .pmo-gantt {
                position: relative;
            }

            #pmo .pmo-gantt-inner {
                position: relative;
            }

            #pmo .pmo-gantt-bar {
                z-index: 4;
            }

            #pmo .pmo-gantt-bar.is-link-source {
                outline: 3px solid rgba(147, 194, 28, .48);
                outline-offset: 2px;
            }

            #pmo .pmo-gantt-seq {
                width: 24px;
                height: 24px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
                background: var(--pmo-primary);
                color: #fff;
                font-size: 11px;
                font-weight: 950;
                box-shadow: 0 10px 22px -16px rgba(15, 23, 42, .9);
            }

            #pmo .pmo-dependency-svg {
                position: absolute;
                inset: 0;
                pointer-events: none;
                z-index: 3;
                overflow: visible;
            }

            #pmo .pmo-dependency-line {
                fill: none;
                stroke: #164191;
                stroke-width: 2.4;
                stroke-linecap: round;
                stroke-linejoin: round;
                opacity: .78;
            }

            #pmo .pmo-dependency-label-bg {
                fill: #ffffff;
                stroke: #dbeafe;
                stroke-width: 1;
            }

            #pmo .pmo-dependency-label-text {
                fill: #164191;
                font-size: 11px;
                font-weight: 900;
            }

            #pmo .pmo-org-chart {
                display: grid;
                gap: 14px;
            }

            #pmo .pmo-org-node {
                position: relative;
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 14px;
                box-shadow: 0 16px 34px -30px rgba(15, 23, 42, .7);
            }

            #pmo .pmo-org-node-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
            }

            #pmo .pmo-org-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-org-meta {
                margin-top: 6px;
                color: var(--pmo-muted);
                font-size: 11px;
                font-weight: 800;
            }

            #pmo .pmo-org-children {
                margin-top: 12px;
                margin-left: 28px;
                padding-left: 18px;
                border-left: 2px dashed #cbd5e1;
                display: grid;
                gap: 12px;
            }

            #pmo .pmo-org-gap {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 8px;
                padding: 5px 9px;
                border-radius: 999px;
                background: var(--pmo-blue-soft);
                color: var(--pmo-navy);
                font-size: 11px;
                font-weight: 950;
            }

            #pmo .pmo-dependency-box {
                display: grid;
                gap: 8px;
            }

            #pmo .pmo-dependency-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                border: 1px solid var(--pmo-border);
                background: #fff;
                border-radius: 14px;
                padding: 10px 12px;
                font-size: 12px;
                font-weight: 850;
                color: var(--pmo-text);
            }


            #pmo.is-dependency-mode .pmo-work-card,
            #pmo.is-dependency-mode .pmo-list-row,
            #pmo.is-dependency-mode .pmo-gantt-bar {
                cursor: crosshair !important;
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .16), 0 18px 36px -28px rgba(15, 23, 42, .75);
            }

            #pmo .pmo-work-card.is-link-source,
            #pmo .pmo-list-row.is-link-source,
            #pmo .pmo-gantt-bar.is-link-source {
                outline: 3px solid rgba(147, 194, 28, .58);
                outline-offset: 2px;
            }





            /* PMO attendance, travel, pause, and daily report - compact employee card */
            #pmo .pmo-attendance-panel {
                margin-top: 10px;
                border: 1px solid #dbeafe;
                border-radius: 16px;
                background: linear-gradient(135deg, #ffffff, #f8fbff);
                padding: 9px 10px;
                display: grid;
                gap: 7px;
            }

            #pmo .pmo-attendance-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                min-width: 0;
            }

            #pmo .pmo-attendance-main {
                display: grid;
                gap: 3px;
                min-width: 0;
            }

            #pmo .pmo-attendance-status {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                min-width: 0;
                color: var(--pmo-text);
                font-size: 11px;
                font-weight: 950;
            }

            #pmo .pmo-attendance-status span:last-child {
                min-width: 0;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            #pmo .pmo-attendance-dot {
                width: 9px;
                height: 9px;
                border-radius: 999px;
                background: #94a3b8;
                box-shadow: 0 0 0 4px #f1f5f9;
                flex: 0 0 auto;
            }

            #pmo .pmo-attendance-dot.present,
            #pmo .pmo-attendance-dot.working,
            #pmo .pmo-attendance-dot.arrived {
                background: #10b981;
                box-shadow: 0 0 0 4px rgba(16, 185, 129, .12);
            }

            #pmo .pmo-attendance-dot.traveling {
                background: #3b82f6;
                box-shadow: 0 0 0 4px rgba(59, 130, 246, .12);
            }

            #pmo .pmo-attendance-dot.paused {
                background: #f59e0b;
                box-shadow: 0 0 0 4px rgba(245, 158, 11, .14);
            }

            #pmo .pmo-attendance-dot.checked_out {
                background: #64748b;
                box-shadow: 0 0 0 4px rgba(100, 116, 139, .14);
            }

            #pmo .pmo-attendance-latest {
                color: var(--pmo-soft);
                font-size: 10px;
                font-weight: 900;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pmo .pmo-attendance-quick {
                display: inline-flex;
                align-items: center;
                justify-content: flex-end;
                gap: 6px;
                flex: 0 0 auto;
            }

            #pmo .pmo-mini-btn {
                min-height: 30px;
                border: 1px solid var(--pmo-border);
                border-radius: 999px;
                background: #fff;
                color: var(--pmo-muted);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                padding: 0 9px;
                font-family: inherit;
                font-size: 10px;
                font-weight: 950;
                cursor: pointer;
                transition: all .16s ease;
                white-space: nowrap;
            }

            #pmo .pmo-mini-btn:hover,
            #pmo .pmo-mini-btn.is-active {
                color: var(--pmo-navy);
                background: var(--pmo-blue-soft);
                border-color: #bfdbfe;
                transform: translateY(-1px);
            }

            #pmo .pmo-mini-btn.green:hover {
                color: #047857;
                background: var(--pmo-green-soft);
                border-color: #bbf7d0;
            }

            #pmo .pmo-mini-btn.orange:hover {
                color: #c2410c;
                background: var(--pmo-orange-soft);
                border-color: #fed7aa;
            }

            #pmo .pmo-attendance-summary-line {
                display: flex;
                align-items: center;
                gap: 6px;
                min-width: 0;
                overflow: hidden;
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 900;
                white-space: nowrap;
            }

            #pmo .pmo-attendance-summary-line span {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pmo .pmo-attendance-summary-line b {
                color: var(--pmo-text);
                font-weight: 950;
            }

            #pmo .pmo-attendance-timegrid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
            }

            #pmo .pmo-attendance-timebox {
                min-width: 0;
                border: 1px solid var(--pmo-border);
                border-radius: 13px;
                background: #fff;
                padding: 7px 8px;
            }

            #pmo .pmo-attendance-timebox span {
                display: block;
                color: var(--pmo-soft);
                font-size: 9px;
                font-weight: 950;
                letter-spacing: .05em;
                text-transform: uppercase;
            }

            #pmo .pmo-attendance-timebox strong {
                display: block;
                margin-top: 3px;
                color: var(--pmo-text);
                font-size: 11px;
                font-weight: 950;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #pmo .pmo-attendance-destination {
                display: flex;
                align-items: center;
                gap: 6px;
                min-width: 0;
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 850;
                border-top: 1px dashed #dbeafe;
                padding-top: 6px;
            }

            #pmo .pmo-attendance-destination span:last-child {
                min-width: 0;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            #pmo .pmo-attendance-actions {
                display: none;
            }

            #pmo .pmo-attendance-action-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            #pmo .pmo-attendance-action-btn {
                width: 100%;
                min-height: 64px;
                border: 1px solid var(--pmo-border);
                border-radius: 17px;
                background: #fff;
                color: var(--pmo-text);
                display: flex;
                align-items: center;
                justify-content: flex-start;
                gap: 10px;
                padding: 11px 12px;
                font-family: inherit;
                cursor: pointer;
                transition: all .16s ease;
                text-align: left;
            }

            #pmo .pmo-attendance-action-btn:hover {
                transform: translateY(-1px);
                border-color: #bfdbfe;
                background: var(--pmo-blue-soft);
            }

            #pmo .pmo-attendance-action-btn strong {
                display: block;
                font-size: 12px;
                font-weight: 950;
                color: var(--pmo-text);
            }

            #pmo .pmo-attendance-action-btn span:last-child {
                display: block;
                margin-top: 2px;
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 800;
                line-height: 1.35;
            }

            @media(max-width: 720px) {
                #pmo .pmo-attendance-action-grid {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-attendance-top {
                    align-items: flex-start;
                }
            }

            #pmo .pmo-report-section {
                display: grid;
                gap: 10px;
            }

            #pmo .pmo-report-block {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 16px;
                padding: 13px;
            }

            #pmo .pmo-report-block h4 {
                margin: 0 0 8px;
                color: var(--pmo-text);
                font-size: 13px;
                font-weight: 950;
            }

            #pmo .pmo-report-list {
                display: grid;
                gap: 7px;
            }

            #pmo .pmo-report-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                border: 1px solid #eef2f7;
                border-radius: 12px;
                background: #f8fafc;
                padding: 8px 10px;
                font-size: 11px;
                font-weight: 850;
                color: var(--pmo-text);
            }

            /* PMO project-plan Gantt like timeline view */
            #pmo .pmo-project-gantt-wrap {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 22px;
                overflow: auto;
                height: 650px;
            }

            #pmo .pmo-project-gantt-inner {
                position: relative;
                min-height: 100%;
                background: #fff;
            }

            #pmo .pmo-project-gantt-head,
            #pmo .pmo-project-gantt-row {
                display: grid;
                grid-template-columns: 230px 105px minmax(900px, 1fr);
            }

            #pmo .pmo-project-gantt-head {
                position: sticky;
                top: 0;
                z-index: 15;
                background: #fff;
                border-bottom: 1px solid #dbe3ea;
            }

            #pmo .pmo-project-gantt-left,
            #pmo .pmo-project-gantt-status,
            #pmo .pmo-project-gantt-months {
                min-height: 42px;
                display: flex;
                align-items: center;
                padding: 0 10px;
                font-size: 11px;
                font-weight: 950;
                color: #111827;
                border-right: 1px solid #dbe3ea;
                background: #fff;
            }

            #pmo .pmo-project-gantt-months {
                padding: 0;
                position: relative;
                overflow: hidden;
            }

            #pmo .pmo-project-gantt-month {
                position: absolute;
                top: 0;
                bottom: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border-right: 1px solid #dbe3ea;
                font-size: 11px;
                font-weight: 950;
                color: #111827;
                background: #f8fafc;
            }

            #pmo .pmo-project-gantt-row {
                min-height: 52px;
                border-bottom: 1px solid #edf2f7;
            }

            #pmo .pmo-project-gantt-task,
            #pmo .pmo-project-gantt-state {
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 0;
                padding: 8px 10px;
                border-right: 1px solid #edf2f7;
                background: #fff;
                font-size: 12px;
                font-weight: 950;
                color: #111827;
            }

            #pmo .pmo-project-gantt-task input {
                width: 14px;
                height: 14px;
                flex: 0 0 auto;
                border: 1px solid #cbd5e1;
                border-radius: 4px;
                pointer-events: none;
            }

            #pmo .pmo-project-gantt-name {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            #pmo .pmo-project-gantt-state {
                align-items: flex-start;
                flex-direction: column;
                justify-content: center;
                gap: 4px;
                font-size: 11px;
                font-weight: 850;
                color: #475569;
            }

            #pmo .pmo-project-gantt-status-line {
                display: flex;
                align-items: center;
                gap: 7px;
            }

            #pmo .pmo-project-gantt-dot {
                width: 9px;
                height: 9px;
                border-radius: 999px;
                flex: 0 0 auto;
                background: #94a3b8;
            }

            #pmo .pmo-project-gantt-dot.green {
                background: #10b981;
            }

            #pmo .pmo-project-gantt-dot.orange {
                background: #f59e0b;
            }

            #pmo .pmo-project-gantt-dot.blue {
                background: #3b82f6;
            }

            #pmo .pmo-project-gantt-dot.lime {
                background: #93c21c;
            }

            #pmo .pmo-project-gantt-dot.red {
                background: #ef4444;
            }

            #pmo .pmo-project-gantt-line {
                position: relative;
                min-height: 52px;
                background:
                    repeating-linear-gradient(90deg, rgba(248, 250, 252, .95) 0, rgba(248, 250, 252, .95) 36px, rgba(255, 255, 255, .95) 36px, rgba(255, 255, 255, .95) 72px),
                    linear-gradient(#fff, #fff);
            }

            #pmo .pmo-project-gantt-dayline {
                position: absolute;
                top: 0;
                bottom: 0;
                width: 1px;
                background: rgba(203, 213, 225, .75);
                pointer-events: none;
            }

            #pmo .pmo-project-gantt-bar {
                position: absolute;
                top: 13px;
                height: 22px;
                min-width: 44px;
                border-radius: 5px;
                background: #38bdf8;
                border: 1px solid #0284c7;
                box-shadow: 0 10px 24px -18px rgba(15, 23, 42, .9);
                cursor: pointer;
                z-index: 4;
                overflow: hidden;
            }

            #pmo .pmo-project-gantt-bar.is-done {
                background: #84cc16;
                border-color: #65a30d;
            }

            #pmo .pmo-project-gantt-progress {
                position: absolute;
                inset: 0 auto 0 0;
                background: rgba(132, 204, 22, .65);
                min-width: 0;
            }

            #pmo .pmo-project-gantt-percent {
                position: relative;
                z-index: 2;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                font-weight: 950;
                color: #0f172a;
            }

            #pmo .pmo-project-gantt-bar.is-link-source,
            #pmo .pmo-work-card.is-link-source,
            #pmo .pmo-list-row.is-link-source {
                outline: 3px solid rgba(239, 68, 68, .45);
                outline-offset: 2px;
            }

            #pmo .pmo-project-gantt-dependency-list {
                position: relative;
                z-index: 6;
                padding: 12px 14px;
                border-top: 1px solid #dbe3ea;
                background: #fff;
            }

            #pmo .pmo-project-gantt-dependency-list strong {
                display: block;
                margin-bottom: 8px;
                font-size: 13px;
                font-weight: 950;
                color: #111827;
            }

            #pmo .pmo-project-gantt-dependency-row {
                border: 1px solid #dbe3ea;
                background: #f8fafc;
                border-radius: 9px;
                padding: 8px 10px;
                margin-top: 7px;
                font-size: 11px;
                font-weight: 950;
                color: #111827;
            }

            #pmo .pmo-dependency-line {
                stroke: #ef4444;
                stroke-width: 2;
                opacity: .95;
            }

            #pmo .pmo-dependency-label-bg,
            #pmo .pmo-dependency-label-text {
                display: none;
            }

            #pmo .pmo-dependency-editor {
                display: grid;
                gap: 9px;
                margin-bottom: 12px;
                padding: 12px;
                border: 1px solid #dbeafe;
                border-radius: 16px;
                background: #f8fbff;
            }

            #pmo .pmo-dependency-select {
                min-height: 110px;
            }

            #pmo .pmo-dependency-editor-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }



            #pmo .pmo-master-detail-modal {
                width: min(1180px, 98vw);
            }

            #pmo .pmo-master-detail-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                flex-wrap: wrap;
                margin-bottom: 12px;
            }

            #pmo .pmo-master-detail-grid {
                display: grid;
                grid-template-columns: 1.15fr .85fr;
                gap: 12px;
            }

            #pmo .pmo-master-detail-section {
                background: #fff;
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                padding: 14px;
                box-shadow: 0 16px 32px -30px rgba(15, 23, 42, .65);
                min-width: 0;
            }

            #pmo .pmo-master-component-row {
                display: grid;
                grid-template-columns: 42px minmax(0, 1fr) auto;
                gap: 10px;
                align-items: center;
                border: 1px solid #edf2f7;
                border-radius: 14px;
                padding: 9px;
                background: #f8fafc;
                margin-bottom: 8px;
            }

            #pmo .pmo-master-component-row.is-child {
                margin-left: 28px;
                border-style: dashed;
                background: #fff;
            }

            #pmo .pmo-master-component-row img {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                object-fit: cover;
                background: #e2e8f0;
            }

            #pmo .pmo-master-detail-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            #pmo .pmo-master-detail-table th,
            #pmo .pmo-master-detail-table td {
                padding: 9px 7px;
                border-bottom: 1px solid #eef2f7;
                text-align: left;
                vertical-align: top;
            }

            #pmo .pmo-master-detail-table th {
                color: var(--pmo-soft);
                text-transform: uppercase;
                font-size: 10px;
                font-weight: 950;
            }

            #pmo .pmo-master-group-card {
                border: 1px solid var(--pmo-border);
                border-radius: 18px;
                background: linear-gradient(135deg, #fff, #f8fafc);
                padding: 14px;
                display: grid;
                gap: 10px;
                box-shadow: 0 18px 34px -30px rgba(15, 23, 42, .7);
            }

            #pmo .pmo-master-group-color {
                width: 12px;
                height: 12px;
                border-radius: 999px;
                display: inline-flex;
                background: var(--pmo-primary);
                border: 1px solid rgba(15, 23, 42, .08);
            }

            @media(max-width: 900px) {
                #pmo .pmo-master-detail-grid {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-master-component-row,
                #pmo .pmo-master-component-row.is-child {
                    grid-template-columns: 36px minmax(0, 1fr);
                    margin-left: 0;
                }
            }

            @media(max-width: 900px) {
                #pmo .pmo-project-grid {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-board-filter {
                    width: 100%;
                }

                #pmo .pmo-board-filter .pmo-input,
                #pmo .pmo-board-filter .pmo-select {
                    min-width: 100%;
                }

                #pmo .pmo-detail-grid {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-gallery-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                #pmo .pmo-bulk-step {
                    grid-template-columns: 1fr;
                }
            }


            /* Select2 design inside PMO dependency drawer.
                                                                                               Do not use dropdownCssClass/selectionCssClass in JS. */
            #pmo .select2-container {
                width: 100% !important;
                font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }

            #pmo .select2-container--default .select2-selection--multiple {
                min-height: 46px;
                border: 1px solid var(--pmo-border) !important;
                border-radius: 14px !important;
                background: #f8fafc !important;
                padding: 5px 7px;
                outline: 0 !important;
            }

            #pmo .select2-container--default.select2-container--focus .select2-selection--multiple {
                border-color: var(--pmo-primary) !important;
                box-shadow: 0 0 0 4px rgba(147, 194, 28, .14);
                background: #fff !important;
            }

            #pmo .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background: var(--pmo-primary-soft) !important;
                border: 1px solid rgba(147, 194, 28, .45) !important;
                border-radius: 999px !important;
                color: var(--pmo-primary-dark) !important;
                font-size: 12px;
                font-weight: 900;
                line-height: 1.2;
                padding: 4px 9px 4px 24px;
                margin-top: 4px;
            }

            #pmo .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: var(--pmo-primary-dark) !important;
                border-right: 0 !important;
                font-weight: 950;
                margin-left: 4px;
                margin-right: 4px;
            }

            #pmo .select2-container--default .select2-search--inline .select2-search__field {
                min-height: 30px;
                margin-top: 4px;
                font-size: 13px;
                font-weight: 800;
                color: var(--pmo-text);
                font-family: Inter, system-ui, sans-serif;
            }

            #pmo .select2-dropdown {
                border: 1px solid var(--pmo-border) !important;
                border-radius: 14px !important;
                overflow: hidden;
                box-shadow: 0 24px 54px -34px rgba(15, 23, 42, .85);
                z-index: 999999 !important;
            }

            #pmo .select2-search--dropdown {
                padding: 8px;
                background: #fff;
            }

            #pmo .select2-container--default .select2-search--dropdown .select2-search__field {
                min-height: 38px;
                border: 1px solid var(--pmo-border) !important;
                border-radius: 11px !important;
                outline: 0 !important;
                padding: 8px 10px;
                font-size: 13px;
                font-weight: 800;
                color: var(--pmo-text);
            }

            #pmo .select2-results__option {
                padding: 10px 12px;
                font-size: 13px;
                font-weight: 850;
            }

            #pmo .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background: var(--pmo-primary) !important;
                color: #fff !important;
            }

            #pmo .select2-container--default .select2-results__option[aria-selected=true] {
                background: var(--pmo-primary-soft) !important;
                color: var(--pmo-primary-dark) !important;
            }

            #pmo .pmo-select2-fallback {
                width: 100%;
                min-height: 140px;
                border: 1px solid var(--pmo-border);
                border-radius: 14px;
                background: #f8fafc;
                padding: 10px;
                color: var(--pmo-text);
                font: 800 13px/1.35 Inter, system-ui, sans-serif;
            }

            /* Planner item steps + material manager */
            #pmo .pmo-section-action-row {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 12px;
            }

            #pmo .pmo-step-add-box,
            #pmo .pmo-material-manual-box {
                border: 1px solid #dbeafe;
                background: #f8fbff;
                border-radius: 18px;
                padding: 12px;
                margin-bottom: 12px;
            }

            #pmo .pmo-step-actions,
            #pmo .pmo-step-row-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 10px;
            }

            #pmo .pmo-inline-check {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
            }

            #pmo .pmo-inline-check input {
                width: 16px;
                height: 16px;
                accent-color: var(--pmo-primary);
            }

            #pmo .pmo-material-scroll {
                width: 100%;
                overflow: auto;
                border: 1px solid #eef2f7;
                border-radius: 16px;
                background: #fff;
            }

            #pmo .pmo-material-table-rich {
                min-width: 920px;
            }

            #pmo .pmo-material-table-rich td,
            #pmo .pmo-material-table-rich th {
                vertical-align: middle;
            }

            #pmo .pmo-material-table-rich tr.is-inactive {
                opacity: .55;
                background: #f8fafc;
            }

            #pmo .pmo-material-table-rich tr.is-request {
                opacity: 1;
                background: #fff7ed;
            }

            #pmo .pmo-material-request-note {
                margin-top: 5px;
                color: #c2410c;
                font-size: 11px;
                font-weight: 850;
            }

            #pmo .pmo-material-img {
                width: 42px;
                height: 42px;
                object-fit: cover;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
            }



            #pmo .pmo-card-material-stats {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 10px;
                padding-top: 9px;
                border-top: 1px solid #f1f5f9;
            }

            #pmo .pmo-card-material-stats .pmo-chip {
                font-size: 10px;
                padding: 4px 7px;
            }

            #pmo .pmo-chip.blue {
                color: var(--pmo-navy);
                background: var(--pmo-blue-soft);
                border-color: #bfdbfe;
            }

            #pmo .pmo-chip.lime {
                color: var(--pmo-primary-dark);
                background: var(--pmo-primary-soft);
                border-color: #d9efaa;
            }

            #pmo .pmo-material-modal {
                width: min(1180px, 98vw);
            }

            #pmo .pmo-material-tabs {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 14px;
            }

            #pmo .pmo-material-tabs button {
                border: 1px solid var(--pmo-border);
                background: #fff;
                border-radius: 999px;
                min-height: 38px;
                padding: 0 12px;
                cursor: pointer;
                color: var(--pmo-muted);
                font-size: 12px;
                font-weight: 950;
                display: inline-flex;
                align-items: center;
                gap: 7px;
            }

            #pmo .pmo-material-tabs button span {
                min-width: 22px;
                min-height: 22px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #f1f5f9;
                color: #64748b;
                font-size: 11px;
            }

            #pmo .pmo-material-tabs button.is-active {
                color: var(--pmo-primary-dark);
                background: var(--pmo-primary-soft);
                border-color: rgba(147, 194, 28, .45);
            }

            #pmo .pmo-material-tabs button.is-active span {
                background: #fff;
                color: var(--pmo-primary-dark);
            }

            #pmo .pmo-material-source-list {
                display: grid;
                gap: 10px;
            }

            #pmo .pmo-material-source-card {
                display: grid;
                grid-template-columns: 76px minmax(0, 1fr) 180px;
                gap: 12px;
                align-items: center;
                border: 1px solid var(--pmo-border);
                background: #fff;
                border-radius: 18px;
                padding: 12px;
                box-shadow: 0 14px 34px -30px rgba(15, 23, 42, .7);
            }

            #pmo .pmo-material-source-card img {
                width: 76px;
                height: 76px;
                border-radius: 16px;
                object-fit: cover;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
            }

            #pmo .pmo-material-source-body {
                min-width: 0;
            }

            #pmo .pmo-material-source-actions {
                display: grid;
                gap: 8px;
            }

            #pmo .pmo-material-search-row {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 10px;
                margin-bottom: 12px;
            }

            @media(max-width: 860px) {
                #pmo .pmo-material-source-card {
                    grid-template-columns: 1fr;
                }

                #pmo .pmo-material-source-card img {
                    width: 100%;
                    height: 160px;
                }

                #pmo .pmo-material-search-row {
                    grid-template-columns: 1fr;
                }
            }

            #pmo .pmo-shared-material-panel,
            #pmo .pmo-shared-material-box {
                margin-top: 10px;
                border: 1px solid #c7d2fe;
                background: linear-gradient(135deg, #f5f3ff, #ffffff);
                border-radius: 16px;
                padding: 8px;
            }

            #pmo .pmo-shared-material-panel.is-collapsed,
            #pmo .pmo-shared-material-box.is-collapsed {
                padding-bottom: 8px;
            }

            #pmo .pmo-shared-material-title {
                width: 100%;
                min-height: 34px;
                border: 0;
                outline: 0;
                border-radius: 12px;
                background: rgba(255, 255, 255, .72);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 9px;
                color: #5b21b6;
                font-family: inherit;
                font-size: 11px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .05em;
                padding: 7px 9px;
                cursor: pointer;
            }

            #pmo .pmo-shared-material-title:hover {
                background: #fff;
                box-shadow: 0 12px 26px -22px rgba(91, 33, 182, .65);
            }

            #pmo .pmo-shared-material-title-left,
            #pmo .pmo-shared-material-title-right {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                min-width: 0;
            }

            #pmo .pmo-shared-material-title-left span {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            #pmo .pmo-shared-material-count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 22px;
                height: 22px;
                padding: 0 7px;
                border-radius: 999px;
                color: #5b21b6;
                background: #fff;
                border: 1px solid #ddd6fe;
                font-size: 10px;
                font-weight: 950;
            }

            #pmo .pmo-shared-material-summary {
                color: var(--pmo-muted);
                font-size: 10px;
                font-weight: 950;
                text-transform: none;
                letter-spacing: 0;
                white-space: nowrap;
            }

            #pmo .pmo-shared-material-toggle {
                color: #7c3aed;
                transition: transform .16s ease;
            }

            #pmo .pmo-shared-material-panel:not(.is-collapsed) .pmo-shared-material-toggle,
            #pmo .pmo-shared-material-box:not(.is-collapsed) .pmo-shared-material-toggle {
                transform: rotate(180deg);
            }

            #pmo .pmo-shared-material-body {
                margin-top: 7px;
            }

            #pmo .pmo-shared-material-panel.is-collapsed .pmo-shared-material-body,
            #pmo .pmo-shared-material-box.is-collapsed .pmo-shared-material-body {
                display: none;
            }

            #pmo .pmo-shared-material-row {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto auto;
                align-items: center;
                gap: 8px;
                padding: 8px 0;
                border-top: 1px solid rgba(199, 210, 254, .65);
                font-size: 11px;
                font-weight: 850;
                color: var(--pmo-text);
            }

            #pmo .pmo-shared-material-row:first-of-type {
                border-top: 0;
            }

            #pmo .pmo-shared-material-row small {
                display: block;
                color: var(--pmo-soft);
                font-weight: 800;
                margin-top: 2px;
            }

            #pmo .pmo-shared-material-row b {
                color: #5b21b6;
                white-space: nowrap;
            }

            #pmo .pmo-shared-material-row em {
                font-style: normal;
                color: var(--pmo-muted);
                white-space: nowrap;
            }

            #pmo .pmo-chip.purple {
                color: #5b21b6;
                background: var(--pmo-purple-soft);
                border-color: #ddd6fe;
            }
        </style>
    @endpush
@endonce

@section('content')
    @php
        $initial = $plannerConfig['initial'] ?? [];
        $endpoints = $plannerConfig['endpoints'] ?? [];
    @endphp

    <div id="pmo">
        <div class="pmo-header">
            <div>
                <h1 class="pmo-title">Montage Planung</h1>
                <div class="pmo-subtitle">Automatische Einsatzplanung aus Montage-Phase, Sub-Stages, Aufgaben, Terminen,
                    Tickets und Personal Tasks.</div>
                <div class="pmo-breadcrumb">
                    <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                    <span>›</span>
                    <a href="{{ route('planner.projects') }}">Projektübersicht</a>
                    <span>›</span>
                    <span>Projektplanung</span>
                </div>
            </div>
        </div>

        <div class="pmo-topbar">
            <div class="pmo-tabs" role="tablist">
                <button type="button" class="pmo-tab is-active" data-pmo-tab="planning"><span
                        data-lucide="layout-dashboard"></span> Planung</button>
                <button type="button" class="pmo-tab" data-pmo-tab="material_requests"><span data-lucide="bell-ring"></span>
                    Mitarbeiter Anfragen <span id="pmo-request-tab-count" class="pmo-job-tab-count">0</span></button>
                <button type="button" class="pmo-tab" data-pmo-tab="team"><span data-lucide="users"></span> Team</button>
                <button type="button" class="pmo-tab" data-pmo-tab="history"><span data-lucide="history"></span>
                    Historie</button>
                <button type="button" class="pmo-tab" data-pmo-tab="org"><span data-lucide="network"></span>
                    Organisationschart</button>
                <button type="button" class="pmo-tab" data-pmo-tab="settings"><span data-lucide="settings"></span>
                    Einstellung</button>
            </div>

            <div class="pmo-actions">
                <span id="pmo-sync-chip" class="pmo-chip green pmo-hidden"><span data-lucide="radio"></span> Sync
                    aktiv</span>
                <button type="button" class="pmo-btn-soft" id="pmo-refresh-btn"><span data-lucide="refresh-cw"></span> Neu
                    laden</button>
                <a href="{{ route('planner.projects') }}" class="pmo-btn-soft"><span data-lucide="arrow-left"></span>
                    Zurück</a>
            </div>
        </div>

        <div class="pmo-project-grid">
            <section class="pmo-card pmo-hero">
                <div class="pmo-hero-inner">
                    <div class="pmo-avatar"><span data-lucide="building-2"></span></div>
                    <div>
                        <div class="pmo-kicker">
                            <span class="pmo-chip"><span data-lucide="git-branch"></span> Montage Projekt</span>
                            <span class="pmo-chip green" id="pmo-stage-chip">Stage wird geladen</span>
                        </div>
                        <div class="pmo-customer-name" id="pmo-customer-name">Kunde wird geladen…</div>
                        <div class="pmo-customer-sub">
                            <span id="pmo-customer-no">Kundennummer: —</span>
                            <span>•</span>
                            <span id="pmo-customer-contact">Kontakt: —</span>
                        </div>
                        <div class="pmo-info-grid">
                            <div class="pmo-info-box">
                                <span class="pmo-label">Produkt</span>
                                <div class="pmo-value" id="pmo-product-name">—</div>
                                <div class="pmo-note" id="pmo-product-status">Status: —</div>
                            </div>
                            <div class="pmo-info-box">
                                <span class="pmo-label">Objekt</span>
                                <div class="pmo-value" id="pmo-object-name">—</div>
                                <div class="pmo-note" id="pmo-object-address">Adresse: —</div>
                            </div>
                            <div class="pmo-info-box">
                                <span class="pmo-label">Plan</span>
                                <div class="pmo-value" id="pmo-plan-title">—</div>
                                <div class="pmo-note" id="pmo-plan-meta">Noch nicht geladen</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="pmo-card pmo-control-card">
                <div class="pmo-control-title">Zeitraum & Ansicht</div>
                <div class="pmo-field">
                    <label class="pmo-label" for="pmo-mode">Planungszeitraum</label>
                    <select id="pmo-mode" class="pmo-select">
                        <option value="day">Tag</option>
                        <option value="week">Woche</option>
                        <option value="month">Monat</option>
                    </select>
                </div>
                <div class="pmo-inline">
                    <button type="button" class="pmo-icon-btn" id="pmo-prev-date"><span
                            data-lucide="chevron-left"></span></button>
                    <input type="date" id="pmo-date" class="pmo-input" style="flex:1;min-width:170px;">
                    <button type="button" class="pmo-icon-btn" id="pmo-next-date"><span
                            data-lucide="chevron-right"></span></button>
                </div>
                <div class="pmo-summary-grid" id="pmo-summary-mini">
                    <div class="pmo-summary-card"><span>Tickets</span><strong>0</strong></div>
                    <div class="pmo-summary-card"><span>Tasks</span><strong>0</strong></div>
                    <div class="pmo-summary-card"><span>Termine</span><strong>0</strong></div>
                    <div class="pmo-summary-card"><span>Team</span><strong>0</strong></div>
                </div>
            </aside>
        </div>

        <section class="pmo-card pmo-main-card" data-pmo-panel="planning">
            <div class="pmo-toolbar">
                <div>
                    <h3 class="pmo-section-title">Ressourcenplan</h3>
                    <div class="pmo-section-sub">Alle Montage-relevanten Arbeiten werden automatisch je Mitarbeiter
                        gruppiert.</div>
                </div>
                <div class="pmo-actions pmo-actions-wide">
                    <div class="pmo-board-filter" aria-label="Arbeiten filtern">
                        <input type="text" id="pmo-work-search" class="pmo-input"
                            placeholder="Arbeit, Ticket, Termin suchen...">
                        <select id="pmo-work-filter-type" class="pmo-select">
                            <option value="">Alle Typen</option>
                            <option value="kanban_task">Montage Aufgaben</option>
                            <option value="personal_task">Personal Tasks</option>
                            <option value="appointment">Termine</option>
                            <option value="ticket">Tickets</option>
                            <option value="phase_activity">Phasen-Aktivitäten</option>
                            <option value="task_phase">Task-Phasen</option>
                        </select>
                        <label class="pmo-check"><input type="checkbox" id="pmo-show-done"> Erledigte
                            anzeigen</label>
                    </div>
                    <div class="pmo-view-switch">
                        <button type="button" class="pmo-icon-btn is-active" data-pmo-view="board" title="Kanban"><span
                                data-lucide="kanban-square"></span></button>
                        <button type="button" class="pmo-icon-btn" data-pmo-view="gantt" title="Gantt"><span
                                data-lucide="chart-no-axes-gantt"></span></button>
                        <button type="button" class="pmo-icon-btn" data-pmo-view="list" title="Liste"><span
                                data-lucide="list-checks"></span></button>
                        <button type="button" class="pmo-btn-soft" id="pmo-dependency-mode"
                            title="Abhängigkeiten verbinden"><span data-lucide="git-branch"></span> Abhängigkeit</button>
                        <button type="button" class="pmo-icon-btn" id="pmo-gantt-zoom-out" title="Gantt verkleinern"><span
                                data-lucide="zoom-out"></span></button>
                        <button type="button" class="pmo-icon-btn" id="pmo-gantt-zoom-in" title="Gantt vergrößern"><span
                                data-lucide="zoom-in"></span></button>
                    </div>
                    <button type="button" class="pmo-btn" id="pmo-open-team-modal"><span data-lucide="user-plus"></span>
                        Mitarbeiter</button>
                </div>
            </div>

            <div class="pmo-workspace">
                <div id="pmo-board" class="pmo-board"></div>
                <div id="pmo-gantt" class="pmo-gantt pmo-hidden"></div>
                <div id="pmo-list" class="pmo-list pmo-hidden"></div>
            </div>
        </section>


        <section class="pmo-card pmo-main-card pmo-hidden" data-pmo-panel="material_requests">
            <div class="pmo-toolbar">
                <div>
                    <h3 class="pmo-section-title">Mitarbeiter Material-Anfragen</h3>
                    <div class="pmo-section-sub">
                        Alle Nuriva-Anfragen separat nach Mitarbeiter gruppiert. Hier kannst du jede Anfrage annehmen oder
                        ablehnen.
                    </div>
                </div>
                <div class="pmo-actions pmo-actions-wide">
                    <div class="pmo-board-filter" aria-label="Material-Anfragen filtern">
                        <input type="text" id="pmo-material-request-search" class="pmo-input"
                            placeholder="Mitarbeiter, Material, Job suchen...">
                        <select id="pmo-material-request-status" class="pmo-select">
                            <option value="open">Offene Anfragen</option>
                            <option value="accepted">Angenommen</option>
                            <option value="rejected">Abgelehnt</option>
                            <option value="all">Alle Anfragen</option>
                        </select>
                        <button type="button" class="pmo-btn-soft" id="pmo-material-request-refresh">
                            <span data-lucide="refresh-cw"></span> Aktualisieren
                        </button>
                    </div>
                </div>
            </div>
            <div class="pmo-workspace">
                <div id="pmo-material-request-summary" class="pmo-summary-grid" style="margin-bottom:14px;"></div>
                <div id="pmo-material-request-inbox" class="pmo-material-request-inbox"></div>
            </div>
        </section>


        <section class="pmo-card pmo-main-card pmo-hidden" data-pmo-panel="org">
            <div class="pmo-toolbar">
                <div>
                    <h3 class="pmo-section-title">Organisationschart</h3>
                    <div class="pmo-section-sub">
                        Abhängigkeiten, Reihenfolge, Zeitabstände, Gesamtstunden und beteiligte Mitarbeiter.
                    </div>
                </div>
            </div>

            <div class="pmo-workspace">
                <div id="pmo-org-summary" class="pmo-summary-grid" style="margin-bottom:14px;"></div>
                <div id="pmo-org-chart" class="pmo-org-chart"></div>
            </div>
        </section>

        <section class="pmo-card pmo-main-card pmo-hidden" data-pmo-panel="team">
            <div class="pmo-toolbar">
                <div>
                    <h3 class="pmo-section-title">Projektteam</h3>
                    <div class="pmo-section-sub">Projektleiter, Außendienst und alle Mitarbeiter mit Montage-Arbeiten.</div>
                </div>
                <button type="button" class="pmo-btn" id="pmo-open-team-modal-2"><span data-lucide="user-plus"></span> Team
                    bearbeiten</button>
            </div>
            <div class="pmo-workspace">
                <div id="pmo-team-grid" class="pmo-team-grid"></div>
            </div>
        </section>

        <section class="pmo-card pmo-main-card pmo-hidden" data-pmo-panel="history">
            <div class="pmo-toolbar">
                <div>
                    <h3 class="pmo-section-title">Historie</h3>
                    <div class="pmo-section-sub">Änderungen aus Lead Product, Planner Items und neu angelegten Arbeiten.
                    </div>
                </div>
            </div>
            <div class="pmo-workspace">
                <div id="pmo-history" class="pmo-history"></div>
            </div>
        </section>

        <section class="pmo-card pmo-main-card pmo-hidden" data-pmo-panel="settings">
            <div class="pmo-toolbar">
                <div>
                    <h3 class="pmo-section-title">Einstellung</h3>
                    <div class="pmo-section-sub">Projektteam und Montage-Daten werden direkt mit lead_product_lists
                        synchronisiert.</div>
                </div>
            </div>
            <div class="pmo-workspace">
                <div class="pmo-settings-grid">
                    <div class="pmo-settings-card">
                        <div class="pmo-settings-icon"><span data-lucide="users"></span></div>
                        <div>
                            <div class="pmo-settings-title">Team aus lead_product_lists</div>
                            <div class="pmo-settings-text">Mitarbeiter hinzufügen oder entfernen aktualisiert automatisch
                                <strong>lead_product_lists.teams</strong>.
                            </div>
                            <div style="margin-top:12px;"><button type="button" class="pmo-btn"
                                    id="pmo-open-team-modal-3"><span data-lucide="users-round"></span> Team
                                    verwalten</button></div>
                        </div>
                    </div>
                    <div class="pmo-settings-card">
                        <div class="pmo-settings-icon"><span data-lucide="git-branch"></span></div>
                        <div>
                            <div class="pmo-settings-title">Montage Stage</div>
                            <div class="pmo-settings-text">Geladen werden nur Daten aus Montage / project LeadStage und den
                                aktiven Sub-Stages.</div>
                        </div>
                    </div>
                    <div class="pmo-settings-card">
                        <div class="pmo-settings-icon"><span data-lucide="plus-circle"></span></div>
                        <div>
                            <div class="pmo-settings-title">Direkt erstellen</div>
                            <div class="pmo-settings-text">Jede Mitarbeiterkarte besitzt ein Plus. Damit werden Termin,
                                Ticket, Personal Task oder Montage Task direkt für diesen Mitarbeiter erstellt.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="pmo-modal-backdrop" id="pmo-summary-modal">
            <div class="pmo-modal pmo-modal-sm">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title"><span data-lucide="clipboard-list"></span> Montage Zusammenfassung</h3>
                        <div class="pmo-modal-sub" id="pmo-summary-period">Zeitraum wird geladen…</div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-summary-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body">
                    <div class="pmo-summary-grid" id="pmo-summary-modal-grid"></div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn" data-pmo-close="pmo-summary-modal"><span
                            data-lucide="check"></span> Verstanden</button>
                </div>
            </div>
        </div>

        <div class="pmo-modal-backdrop" id="pmo-team-modal">
            <div class="pmo-modal">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title"><span data-lucide="users-round"></span> Projektteam verwalten</h3>
                        <div class="pmo-modal-sub">Mitarbeiter werden automatisch in
                            <strong>lead_product_lists.teams</strong> gespeichert.
                        </div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-team-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body">
                    <div class="pmo-modal-grid">
                        <div class="pmo-field">
                            <label class="pmo-label">Mitarbeiter auswählen</label>
                            <select id="pmo-team-select" class="pmo-select"></select>
                        </div>
                        <div class="pmo-field">
                            <label class="pmo-label">Aktion</label>
                            <button type="button" class="pmo-btn" id="pmo-add-team-member"><span
                                    data-lucide="user-plus"></span> Zum Team hinzufügen</button>
                        </div>
                    </div>
                    <div style="margin-top:18px;" id="pmo-team-modal-list" class="pmo-team-grid"></div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn-soft" data-pmo-close="pmo-team-modal">Schließen</button>
                </div>
            </div>
        </div>

        <div class="pmo-drawer-backdrop" id="pmo-job-backdrop"></div>
        <aside class="pmo-job-drawer" id="pmo-job-drawer" aria-hidden="true">
            <div class="pmo-job-head">
                <div>
                    <div class="pmo-pill-row" id="pmo-job-pills"></div>
                    <h3 class="pmo-job-title" id="pmo-job-title">Arbeit</h3>
                    <div class="pmo-job-meta" id="pmo-job-meta"></div>
                </div>
                <button type="button" class="pmo-icon-btn" id="pmo-job-close"><span data-lucide="x"></span></button>
            </div>
            <div class="pmo-job-body" id="pmo-job-body">
                <div class="pmo-empty">Wähle eine Arbeit aus.</div>
            </div>
        </aside>

        <div class="pmo-modal-backdrop" id="pmo-work-modal">
            <div class="pmo-modal">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title"><span data-lucide="plus-circle"></span> Arbeit erstellen</h3>
                        <div class="pmo-modal-sub" id="pmo-work-modal-sub">Für ausgewählten Mitarbeiter erstellen.</div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-work-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body">
                    <input type="hidden" id="pmo-work-employee-id">

                    <div class="pmo-work-mode">
                        <label class="pmo-mode-card">
                            <input type="radio" name="pmo-work-mode" value="single" checked>
                            <span><strong>Single Job</strong><span>Eine einzelne Arbeit für den Mitarbeiter
                                    erstellen.</span></span>
                        </label>
                        <label class="pmo-mode-card">
                            <input type="radio" name="pmo-work-mode" value="bulk">
                            <span><strong>Bulk / Schritte</strong><span>Einen Hauptjob mit mehreren Keys, Schritten und
                                    Zuständigkeiten erstellen.</span></span>
                        </label>
                    </div>

                    <div class="pmo-modal-grid">
                        <div class="pmo-field">
                            <label class="pmo-label">Typ</label>
                            <select id="pmo-work-type" class="pmo-select">
                                <option value="kanban_task">Montage Aufgabe</option>
                                <option value="personal_task">Personal Task</option>
                                <option value="appointment">Termin</option>
                                <option value="ticket">Ticket</option>
                            </select>
                        </div>
                        <div class="pmo-field">
                            <label class="pmo-label">Datum</label>
                            <input type="date" id="pmo-work-date" class="pmo-input">
                        </div>
                        <div class="pmo-field pmo-span-2">
                            <label class="pmo-label">Titel</label>
                            <input type="text" id="pmo-work-title" class="pmo-input" placeholder="Titel eingeben...">
                        </div>
                        <div class="pmo-field">
                            <label class="pmo-label">Start</label>
                            <input type="time" id="pmo-work-start" class="pmo-input" value="08:00">
                        </div>
                        <div class="pmo-field">
                            <label class="pmo-label">Ende</label>
                            <input type="time" id="pmo-work-end" class="pmo-input" value="09:00">
                        </div>
                        <div class="pmo-field pmo-span-2">
                            <label class="pmo-label">Beschreibung</label>
                            <textarea id="pmo-work-description" class="pmo-textarea" placeholder="Optional..."></textarea>
                        </div>
                    </div>

                    <div class="pmo-bulk-area pmo-hidden" id="pmo-bulk-area">
                        <div class="pmo-bulk-head">
                            <div>
                                <div class="pmo-detail-title" style="margin-bottom:2px;"><span
                                        data-lucide="list-plus"></span> Schritte / Keys</div>
                                <div class="pmo-note">Jeder Schritt kann eine eigene Person, Datum und Uhrzeit bekommen.
                                </div>
                            </div>
                            <button type="button" class="pmo-btn-soft" id="pmo-add-step"><span data-lucide="plus"></span>
                                Schritt</button>
                        </div>
                        <div id="pmo-bulk-steps"></div>
                    </div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn-soft" data-pmo-close="pmo-work-modal">Abbrechen</button>
                    <button type="button" class="pmo-btn" id="pmo-save-work"><span data-lucide="save"></span>
                        Speichern</button>
                </div>
            </div>
        </div>


        <div class="pmo-modal-backdrop" id="pmo-material-modal">
            <div class="pmo-modal pmo-material-modal">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title" id="pmo-material-modal-title"><span data-lucide="package-plus"></span>
                            Material</h3>
                        <div class="pmo-modal-sub" id="pmo-material-modal-sub">Material für ausgewählten Job verwalten.
                        </div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-material-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body" id="pmo-material-modal-body">
                    <div class="pmo-empty">Materialquellen werden geladen...</div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn-soft" data-pmo-close="pmo-material-modal">Schließen</button>
                </div>
            </div>
        </div>

        <div class="pmo-modal-backdrop" id="pmo-master-detail-modal">
            <div class="pmo-modal pmo-master-detail-modal">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title" id="pmo-master-detail-title"><span data-lucide="boxes"></span> MasterSet
                            Details</h3>
                        <div class="pmo-modal-sub" id="pmo-master-detail-sub">Hauptkomponenten, Unterkomponenten, Labor und
                            Schritte.</div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-master-detail-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body" id="pmo-master-detail-body">
                    <div class="pmo-empty">Details werden geladen...</div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn-soft" data-pmo-close="pmo-master-detail-modal">Schließen</button>
                </div>
            </div>
        </div>



        <div class="pmo-modal-backdrop" id="pmo-attendance-actions-modal">
            <div class="pmo-modal pmo-modal-sm">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title"><span data-lucide="sliders-horizontal"></span> Anwesenheit Aktionen</h3>
                        <div class="pmo-modal-sub" id="pmo-attendance-actions-sub">Aktionen für Mitarbeiter und ausgewählten
                            Tag.</div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-attendance-actions-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body" id="pmo-attendance-actions-body">
                    <div class="pmo-empty">Aktionen werden geladen...</div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn-soft"
                        data-pmo-close="pmo-attendance-actions-modal">Schließen</button>
                </div>
            </div>
        </div>


        <div class="pmo-modal-backdrop" id="pmo-attendance-report-modal">
            <div class="pmo-modal">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title"><span data-lucide="clipboard-check"></span> Tagesbericht</h3>
                        <div class="pmo-modal-sub" id="pmo-attendance-report-sub">Anwesenheit, Fahrt, Pause, Aufgaben,
                            Galerie und Material.</div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-attendance-report-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body" id="pmo-attendance-report-body">
                    <div class="pmo-empty">Bericht wird geladen...</div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn-soft"
                        data-pmo-close="pmo-attendance-report-modal">Schließen</button>
                </div>
            </div>
        </div>

        <div class="pmo-modal-backdrop" id="pmo-completion-report-modal">
            <div class="pmo-modal pmo-modal-sm">
                <div class="pmo-modal-head">
                    <div>
                        <h3 class="pmo-modal-title"><span data-lucide="clipboard-check"></span> Fertig-Bericht</h3>
                        <div class="pmo-modal-sub" id="pmo-completion-report-sub">Bericht schreiben oder ohne Bericht
                            fertigstellen.</div>
                    </div>
                    <button type="button" class="pmo-icon-btn" data-pmo-close="pmo-completion-report-modal"><span
                            data-lucide="x"></span></button>
                </div>
                <div class="pmo-modal-body">
                    <div class="pmo-field">
                        <label class="pmo-label">Bericht</label>
                        <textarea id="pmo-completion-report-text" class="pmo-textarea"
                            placeholder="Was wurde erledigt? Was ist wichtig für den nächsten Schritt?"></textarea>
                    </div>
                    <div class="pmo-modal-grid" style="margin-top:12px;">
                        <div class="pmo-field">
                            <label class="pmo-label">Nächster Schritt</label>
                            <input type="text" id="pmo-completion-report-next-step" class="pmo-input"
                                placeholder="Optional">
                        </div>
                        <div class="pmo-field">
                            <label class="pmo-label">Fällig am</label>
                            <input type="date" id="pmo-completion-report-due-date" class="pmo-input">
                        </div>
                    </div>
                    <div class="pmo-empty" style="margin-top:12px;text-align:left;padding:12px;">
                        Personal Task → personal_task_comments · Termin → appointment_reports · Ticket → ticket_reports ·
                        Kunden-/Montagearbeit → customer_reports.
                    </div>
                </div>
                <div class="pmo-modal-foot">
                    <button type="button" class="pmo-btn-soft" id="pmo-completion-report-skip"><span
                            data-lucide="check-circle-2"></span> Ohne Bericht fertigstellen</button>
                    <button type="button" class="pmo-btn" id="pmo-completion-report-save"><span data-lucide="save"></span>
                        Bericht speichern & fertig</button>
                </div>
            </div>
        </div>

        <div class="pmo-toast-wrap" id="pmo-toast-wrap"></div>
    </div>
@endsection

@once
    @push('scripts')
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
        <script>
            (() => {
                const config = @json($plannerConfig ?? []);
                const plannerBaseUrl = document.querySelector('meta[name="planner-base-url"]')?.content || '/planner';
                config.endpoints = {
                    itemStepStore: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/steps`,
                    itemStepUpdate: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/steps/___STEP___`,
                    itemStepDestroy: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/steps/___STEP___`,
                    itemMaterialSources: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/materials/sources`,
                    itemMaterialImportDeal: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/materials/import-deal`,
                    itemMaterialStore: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/materials`,
                    itemMaterialUpdate: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/materials/___MATERIAL___`,
                    itemMaterialDestroy: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/materials/___MATERIAL___`,
                    itemMaterialRequestStatus: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/material-requests/___REQUEST___/status`,
                    itemMaterialProducts: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/materials/products`,
                    planGroupMaterialStore: `${plannerBaseUrl}/plans/___PLAN___/group-materials`,
                    attendanceDay: `${plannerBaseUrl}/plans/___PLAN___/attendance/day`,
                    attendanceReport: `${plannerBaseUrl}/plans/___PLAN___/attendance/report`,
                    attendanceCheckIn: `${plannerBaseUrl}/plans/___PLAN___/attendance/check-in`,
                    attendanceCheckOut: `${plannerBaseUrl}/plans/___PLAN___/attendance/check-out`,
                    attendanceTravelStart: `${plannerBaseUrl}/plans/___PLAN___/attendance/travel-start`,
                    attendanceLocation: `${plannerBaseUrl}/plans/___PLAN___/attendance/location`,
                    attendanceArrived: `${plannerBaseUrl}/plans/___PLAN___/attendance/arrived`,
                    attendanceWorkStart: `${plannerBaseUrl}/plans/___PLAN___/attendance/work-start`,
                    attendanceWorkEnd: `${plannerBaseUrl}/plans/___PLAN___/attendance/work-end`,
                    attendancePauseStart: `${plannerBaseUrl}/plans/___PLAN___/attendance/pause-start`,
                    attendancePauseEnd: `${plannerBaseUrl}/plans/___PLAN___/attendance/pause-end`,
                    itemCommentStore: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/comments`,
                    itemCommentDestroy: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/comments/___COMMENT___`,
                    itemStatusUpdate: `${plannerBaseUrl}/plans/___PLAN___/items/___ITEM___/status`,
                    ...(config.endpoints || {}),
                };
                window.plannerConfig = config;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const root = document.getElementById('pmo');

                const state = {
                    projectId: Number(config?.initial?.projectId || new URLSearchParams(location.search).get('project_id') || 0),
                    planId: Number(config?.initial?.planId || new URLSearchParams(location.search).get('plan_id') || 0),
                    date: new URLSearchParams(location.search).get('date') || new Date().toISOString().slice(0, 10),
                    mode: 'day',
                    tab: 'planning',
                    view: 'board',
                    payload: null,
                    loading: false,
                    workSearch: '',
                    workType: '',
                    showDone: false,
                    expandedEmployees: {},
                    collapsedSharedMaterials: {},
                    attendance: { by_employee: {} },
                    attendanceLocationTimers: {},
                    activeWorkKey: null,
                    dependencyMode: false,
                    dependencySourceId: null,
                    ganttZoom: 1,
                    materialModal: null,
                    materialRequestFilter: 'open',
                    materialRequestSearch: '',
                    masterDetailModal: null,
                };

                const endpoint = (key, projectId = state.projectId, planId = state.planId, itemId = 0) => {
                    const raw = config?.endpoints?.[key] || '';

                    if (!raw) {
                        console.error('Missing planner endpoint:', key, config?.endpoints || {});
                        return null;
                    }

                    return raw
                        .replaceAll('___PROJECT___', projectId || '')
                        .replaceAll('__PROJECT__', projectId || '')
                        .replaceAll('___PLAN___', planId || '')
                        .replaceAll('__PLAN__', planId || '')
                        .replaceAll('___ITEM___', itemId || '')
                        .replaceAll('__ITEM__', itemId || '');
                };

                const icon = (name, size = 18) => `<span data-lucide="${name}" style="width:${size}px;height:${size}px;"></span>`;
                const esc = (value) => String(value ?? '').replace(/[&<>'"]/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[s]));
                const arr = (v) => Array.isArray(v) ? v : [];
                const moneyDate = (v) => v ? new Date(v).toLocaleDateString('de-DE') : 'Ohne Datum';
                const timeOnly = (v) => {
                    if (!v) return '—';
                    const s = String(v);
                    if (/^\d{2}:\d{2}/.test(s)) return s.slice(0, 5);
                    try { return new Date(s).toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }); } catch (e) { return s.slice(11, 16) || '—'; }
                };

                function redrawIcons() {
                    try { window.lucide?.createIcons({ attrs: { 'stroke-width': 2.1 } }); } catch (e) { }
                }

                function toast(title, message, type = 'ok') {
                    const wrap = document.getElementById('pmo-toast-wrap');
                    if (!wrap) return;
                    const el = document.createElement('div');
                    el.className = 'pmo-toast';
                    el.innerHTML = `<div class="pmo-type-icon ${type === 'bad' ? 'ticket' : 'phase_activity'}">${icon(type === 'bad' ? 'triangle-alert' : 'check', 18)}</div><div><strong>${esc(title)}</strong><span>${esc(message)}</span></div>`;
                    wrap.appendChild(el);
                    redrawIcons();
                    setTimeout(() => el.remove(), 4500);
                }

                async function requestJson(url, options = {}) {
                    if (!url) {
                        throw new Error('Planner endpoint URL is missing. Check plannerConfig.endpoints.');
                    }

                    const fetchOptions = {
                        ...options,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(options.headers || {}),
                        },
                    };

                    const res = await fetch(url, fetchOptions);
                    const text = await res.text();
                    let json = {};

                    try {
                        json = text ? JSON.parse(text) : {};
                    } catch (e) {
                        json = {};
                    }

                    if (!res.ok || json.ok === false) {
                        const message = json.message || text || `HTTP ${res.status}`;
                        throw new Error(message);
                    }

                    return json;
                }


                async function requestForm(url, formData, options = {}) {
                    if (!url) {
                        throw new Error('Planner endpoint URL is missing. Check plannerConfig.endpoints.');
                    }

                    const res = await fetch(url, {
                        ...options,
                        method: options.method || 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(options.headers || {}),
                        },
                        body: formData,
                    });

                    const text = await res.text();
                    let json = {};

                    try {
                        json = text ? JSON.parse(text) : {};
                    } catch (e) {
                        json = {};
                    }

                    if (!res.ok || json.ok === false) {
                        const message = json.message || text || `HTTP ${res.status}`;
                        throw new Error(message);
                    }

                    return json;
                }

                async function loadPayload(showSummary = false) {
                    if (!state.projectId) {
                        document.getElementById('pmo-board').innerHTML = `<div class="pmo-empty">Kein Projekt ausgewählt. Öffne diese Seite mit <strong>?project_id=...</strong>.</div>`;
                        return;
                    }
                    state.loading = true;
                    const payloadEndpoint = endpoint('montageWorkPayload');
                    if (!payloadEndpoint) {
                        throw new Error('Montage work payload endpoint is missing.');
                    }
                    const url = new URL(payloadEndpoint, window.location.origin);
                    url.searchParams.set('date', state.date);
                    url.searchParams.set('mode', state.mode);
                    if (state.planId) url.searchParams.set('plan_id', state.planId);
                    url.searchParams.set('show_done', state.showDone ? '1' : '0');
                    const json = await requestJson(url.toString());
                    state.payload = json.data || json;
                    state.planId = Number(state.payload?.plan?.id || state.planId || 0);
                    await loadAttendanceDay();
                    state.loading = false;
                    renderAll();
                    const key = `pmo-summary-${state.projectId}-${state.date}-${state.mode}`;
                    if (showSummary || !sessionStorage.getItem(key)) {
                        renderSummaryModal();
                        openModal('pmo-summary-modal');
                        sessionStorage.setItem(key, '1');
                    }
                }

                function renderAll() {
                    renderProject();
                    renderSummaryMini();
                    renderBoard();
                    renderGantt();
                    renderList();
                    renderTeam();
                    renderHistory();
                    renderOrgChart();
                    renderMaterialRequestsInbox();
                    populateEmployeeSelects();
                    redrawIcons();
                }

                function renderProject() {
                    const p = state.payload?.project || {};
                    const plan = state.payload?.plan || {};
                    document.getElementById('pmo-customer-name').textContent = p.customer_name || p.customer_display_name || `Kunde #${p.customer_id || '—'}`;
                    document.getElementById('pmo-customer-no').textContent = `Kundennummer: ${p.customer_no || p.customer_id || '—'}`;
                    document.getElementById('pmo-customer-contact').textContent = `Kontakt: ${p.customer_phone || p.customer_email || '—'}`;
                    document.getElementById('pmo-product-name').textContent = p.product_name || '—';
                    document.getElementById('pmo-product-status').textContent = `Stage: ${p.stage_name || p.status || '—'} / ${p.sub_stage_name || '—'}`;
                    document.getElementById('pmo-object-name').textContent = p.object_name || 'Objekt';
                    document.getElementById('pmo-object-address').textContent = p.object_full_address || p.object_address || 'Adresse: —';
                    document.getElementById('pmo-plan-title').textContent = plan.title || `Plan #${plan.id || state.planId || '—'}`;
                    document.getElementById('pmo-plan-meta').textContent = plan.id ? `Plan ID ${plan.id} · Projekt ${p.id || state.projectId}` : 'Noch nicht synchronisiert';
                    document.getElementById('pmo-stage-chip').textContent = p.sub_stage_name || p.stage_name || 'Montage';
                    document.getElementById('pmo-sync-chip')?.classList.remove('pmo-hidden');
                }

                function renderSummaryMini() {
                    const s = state.payload?.summary || {};
                    document.getElementById('pmo-summary-mini').innerHTML = `
                                                                                                                        <div class="pmo-summary-card"><span>Tickets</span><strong>${Number(s.tickets || 0)}</strong></div>
                                                                                                                        <div class="pmo-summary-card"><span>Tasks</span><strong>${Number((s.kanban_tasks || 0) + (s.personal_tasks || 0))}</strong></div>
                                                                                                                        <div class="pmo-summary-card"><span>Termine</span><strong>${Number(s.appointments || 0)}</strong></div>
                                                                                                                        <div class="pmo-summary-card"><span>Team</span><strong>${arr(state.payload?.employees).length}</strong></div>`;
                }

                function renderSummaryModal() {
                    const s = state.payload?.summary || {};
                    document.getElementById('pmo-summary-period').textContent = s.period_label || `${state.date} · ${state.mode}`;
                    document.getElementById('pmo-summary-modal-grid').innerHTML = `
                                                                                                                        <div class="pmo-summary-card"><span>Tickets</span><strong>${Number(s.tickets || 0)}</strong></div>
                                                                                                                        <div class="pmo-summary-card"><span>Montage Tasks</span><strong>${Number(s.kanban_tasks || 0)}</strong></div>
                                                                                                                        <div class="pmo-summary-card"><span>Personal Tasks</span><strong>${Number(s.personal_tasks || 0)}</strong></div>
                                                                                                                        <div class="pmo-summary-card"><span>Termine</span><strong>${Number(s.appointments || 0)}</strong></div>`;
                }


                async function loadAttendanceDay() {
                    if (!state.planId) {
                        state.attendance = { by_employee: {} };
                        return;
                    }

                    try {
                        const url = new URL(endpoint('attendanceDay'), window.location.origin);
                        url.searchParams.set('date', state.date);
                        url.searchParams.set('mode', state.mode);
                        const json = await requestJson(url.toString());
                        state.attendance = json.data || { by_employee: {} };
                    } catch (err) {
                        console.warn('Attendance could not be loaded:', err);
                        state.attendance = { by_employee: {} };
                    }
                }

                function attendanceForEmployee(employeeId) {
                    const map = state.attendance?.by_employee || {};
                    return map[employeeId] || map[String(employeeId)] || { status: 'absent', status_label: 'Nicht anwesend' };
                }

                function shortDateTime(value) {
                    if (!value) return '—';
                    const s = String(value);
                    const m = s.match(/(\d{2}:\d{2})/);
                    return m ? m[1] : s;
                }

                function reportCountLabel(value, singular, plural) {
                    const n = Number(value || 0);
                    return `${n} ${n === 1 ? singular : plural}`;
                }

                function defaultDestinationForEmployee(items = []) {
                    const project = state.payload?.project || {};
                    const firstItem = arr(items).find(item => item.destination || item.address || item.object_address || item.full_address) || {};
                    return firstItem.destination
                        || firstItem.object_address
                        || firstItem.full_address
                        || firstItem.address
                        || project.object_full_address
                        || project.object_address
                        || project.full_address
                        || project.customer_address
                        || '';
                }

                function attendanceLatestLabel(attendance) {
                    if (attendance.check_out) return `Feierabend ${shortDateTime(attendance.check_out)}`;
                    if (attendance.pause_started_at || attendance.pause_start) return `Pause seit ${shortDateTime(attendance.pause_started_at || attendance.pause_start)}`;
                    if (attendance.work_started_at || attendance.work_start) return `Arbeit seit ${shortDateTime(attendance.work_started_at || attendance.work_start)}`;
                    if (attendance.arrived_at) return `Ankunft ${shortDateTime(attendance.arrived_at)}`;
                    if (attendance.travel_started_at || attendance.travel_start) return `Fahrt seit ${shortDateTime(attendance.travel_started_at || attendance.travel_start)}`;
                    if (attendance.check_in) return `Check-in ${shortDateTime(attendance.check_in)}`;
                    return 'Heute noch kein Status';
                }

                function renderEmployeeAttendance(emp, items = []) {
                    const attendance = attendanceForEmployee(emp.id);
                    const status = String(attendance.status || 'absent');
                    const destination = attendance.destination || defaultDestinationForEmployee(items) || 'Kein Ziel gesetzt';
                    const presentText = attendance.status_label || 'Nicht anwesend';
                    const summary = [
                        `Fahrt <b>${esc(attendance.travel_total_label || '00:00')}</b>`,
                        `Pause <b>${esc(attendance.pause_total_label || '00:00')}</b>`,
                        `Arbeit <b>${esc(attendance.work_total_label || '00:00')}</b>`,
                    ].join(' · ');

                    return `<div class="pmo-attendance-panel" data-pmo-attendance-employee="${esc(emp.id)}">
                                                                <div class="pmo-attendance-top">
                                                                    <div class="pmo-attendance-main">
                                                                        <div class="pmo-attendance-status"><span class="pmo-attendance-dot ${esc(status)}"></span><span>${esc(presentText)}</span></div>
                                                                        <div class="pmo-attendance-latest">${esc(attendanceLatestLabel(attendance))}</div>
                                                                    </div>
                                                                    <div class="pmo-attendance-quick">
                                                                        <button type="button" class="pmo-mini-btn is-active" data-pmo-attendance-open="${esc(emp.id)}">${icon('sliders-horizontal', 13)} Aktionen</button>
                                                                        <button type="button" class="pmo-mini-btn" data-pmo-attendance-report="${esc(emp.id)}">${icon('clipboard-list', 13)} Bericht</button>
                                                                    </div>
                                                                </div>
                                                                <div class="pmo-attendance-summary-line"><span>${icon('clock-3', 12)}</span><span>${summary}</span></div>
                                                                <div class="pmo-attendance-destination"><span>${icon('navigation', 12)}</span><span>${esc(destination)}</span></div>
                                                            </div>`;
                }

                function attendanceActionDefinitions(employeeId) {
                    return [
                        { key: 'check-in', icon: 'log-in', title: 'Anwesend', note: 'Mitarbeiter ist heute da.', tone: 'green' },
                        { key: 'travel-start', icon: 'car-front', title: 'Fahrt starten', note: 'Live-Standort alle 10 Sekunden senden.', tone: '' },
                        { key: 'arrived', icon: 'map-pin-check', title: 'Angekommen', note: 'Fahrt beenden und Fahrzeit berechnen.', tone: 'green' },
                        { key: 'work-start', icon: 'play', title: 'Arbeit starten', note: 'Arbeitszeit für die heutigen Jobs beginnen.', tone: 'green' },
                        { key: 'pause-start', icon: 'utensils', title: 'Mittag / Pause', note: 'Pause starten.', tone: 'orange' },
                        { key: 'pause-end', icon: 'circle-play', title: 'Pause Ende', note: 'Pause beenden und weiterarbeiten.', tone: '' },
                        { key: 'work-end', icon: 'square', title: 'Arbeit Ende', note: 'Arbeitszeit beenden.', tone: '' },
                        { key: 'check-out', icon: 'log-out', title: 'Feierabend', note: 'Tag abschließen.', tone: '' },
                    ].map(action => `<button type="button" class="pmo-attendance-action-btn ${esc(action.tone || '')}" data-pmo-attendance-action="${esc(action.key)}" data-employee-id="${esc(employeeId)}">
                                                                <span>${icon(action.icon, 18)}</span>
                                                                <span><strong>${esc(action.title)}</strong><span>${esc(action.note)}</span></span>
                                                            </button>`).join('');
                }

                function openAttendanceActions(employeeId) {
                    const emp = employeeById(employeeId) || {};
                    const body = document.getElementById('pmo-attendance-actions-body');
                    const sub = document.getElementById('pmo-attendance-actions-sub');
                    if (!body) return;
                    if (sub) sub.textContent = `${emp.full_name || emp.name || ('Mitarbeiter #' + employeeId)} · ${state.date}`;
                    body.innerHTML = `<div class="pmo-attendance-action-grid">${attendanceActionDefinitions(employeeId)}</div>`;
                    openModal('pmo-attendance-actions-modal');
                    redrawIcons();
                }

                async function postAttendance(actionKey, employeeId, payload = {}) {
                    const url = endpoint(actionKey);
                    const json = await requestJson(url, {
                        method: 'POST',
                        body: JSON.stringify({
                            employee_id: Number(employeeId),
                            date: state.date,
                            mode: state.mode,
                            ...payload,
                        }),
                    });

                    await loadAttendanceDay();
                    renderBoard();
                    redrawIcons();

                    return json.data || json;
                }

                function stopAttendanceLocationTimer(employeeId) {
                    const key = String(employeeId);
                    if (state.attendanceLocationTimers?.[key]) {
                        clearInterval(state.attendanceLocationTimers[key]);
                        delete state.attendanceLocationTimers[key];
                    }
                }

                function sendEmployeeLocation(employeeId) {
                    if (!navigator.geolocation || !state.planId) return;

                    navigator.geolocation.getCurrentPosition(async pos => {
                        try {
                            await requestJson(endpoint('attendanceLocation'), {
                                method: 'POST',
                                body: JSON.stringify({
                                    employee_id: Number(employeeId),
                                    date: state.date,
                                    lat: pos.coords.latitude,
                                    lng: pos.coords.longitude,
                                    accuracy: pos.coords.accuracy,
                                    speed: pos.coords.speed,
                                    heading: pos.coords.heading,
                                }),
                            });
                        } catch (err) {
                            console.warn('Location ping failed:', err);
                        }
                    }, err => {
                        console.warn('Location permission/error:', err);
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 7000,
                        timeout: 9000,
                    });
                }

                function startAttendanceLocationTimer(employeeId) {
                    stopAttendanceLocationTimer(employeeId);
                    sendEmployeeLocation(employeeId);
                    state.attendanceLocationTimers[String(employeeId)] = setInterval(() => sendEmployeeLocation(employeeId), 10000);
                }

                async function handleAttendanceAction(button) {
                    const employeeId = Number(button.dataset.employeeId || 0);
                    const action = button.dataset.pmoAttendanceAction || '';
                    const items = employeeItems(employeeId);
                    const visibleItemIds = items.map(item => Number(itemPlannerId(item) || item.id || 0)).filter(Boolean);
                    const destinationDefault = defaultDestinationForEmployee(items);

                    const map = {
                        'check-in': 'attendanceCheckIn',
                        'check-out': 'attendanceCheckOut',
                        'travel-start': 'attendanceTravelStart',
                        'arrived': 'attendanceArrived',
                        'work-start': 'attendanceWorkStart',
                        'work-end': 'attendanceWorkEnd',
                        'pause-start': 'attendancePauseStart',
                        'pause-end': 'attendancePauseEnd',
                    };

                    const endpointKey = map[action];
                    if (!endpointKey || !employeeId) return;

                    const extra = {};
                    if (action === 'travel-start') {
                        const destination = prompt('Ziel / Adresse für die Fahrt:', destinationDefault || '');
                        if (destination === null) return;
                        extra.destination = destination;
                        extra.item_ids = visibleItemIds;
                    }
                    if (action === 'pause-start') {
                        extra.pause_type = 'mittag_essen';
                    }

                    try {
                        await postAttendance(endpointKey, employeeId, extra);
                        if (action === 'travel-start') {
                            startAttendanceLocationTimer(employeeId);
                            toast('Fahrt gestartet', 'Standort wird alle 10 Sekunden per Reverb/Endpoint aktualisiert.');
                        } else if (action === 'arrived' || action === 'check-out') {
                            stopAttendanceLocationTimer(employeeId);
                            toast('Anwesenheit aktualisiert', 'Status wurde gespeichert.');
                        } else {
                            toast('Anwesenheit aktualisiert', 'Status wurde gespeichert.');
                        }
                        closeModal('pmo-attendance-actions-modal');
                    } catch (err) {
                        toast('Fehler', err.message || 'Anwesenheit konnte nicht gespeichert werden.', 'bad');
                    }
                }

                async function openAttendanceReport(employeeId) {
                    try {
                        const url = new URL(endpoint('attendanceReport'), window.location.origin);
                        url.searchParams.set('employee_id', employeeId);
                        url.searchParams.set('date', state.date);
                        const json = await requestJson(url.toString());
                        renderAttendanceReport(json.data || {}, employeeId);
                        openModal('pmo-attendance-report-modal');
                    } catch (err) {
                        toast('Bericht Fehler', err.message || 'Tagesbericht konnte nicht geladen werden.', 'bad');
                    }
                }

                function renderAttendanceReport(data, employeeId) {
                    const emp = employeeById(employeeId) || {};
                    const att = data.attendance || {};
                    const body = document.getElementById('pmo-attendance-report-body');
                    document.getElementById('pmo-attendance-report-sub').textContent = `${emp.full_name || emp.name || ('Mitarbeiter #' + employeeId)} · ${data.date || state.date}`;

                    const events = arr(data.events);
                    const items = arr(data.items);
                    const done = arr(data.done_items);
                    const comments = arr(data.comments);
                    const gallery = arr(data.gallery);
                    const materials = arr(data.materials);
                    const shared = arr(data.shared_materials);

                    body.innerHTML = `<div class="pmo-report-section">
                                                                <div class="pmo-report-block">
                                                                    <h4>Anwesenheit</h4>
                                                                    <div class="pmo-attendance-timegrid">
                                                                        <div class="pmo-attendance-timebox"><span>Status</span><strong>${esc(att.status_label || att.status || '—')}</strong></div>
                                                                        <div class="pmo-attendance-timebox"><span>Fahrt</span><strong>${esc(att.travel_total_label || '00:00')}</strong></div>
                                                                        <div class="pmo-attendance-timebox"><span>Arbeit</span><strong>${esc(att.work_total_label || '00:00')}</strong></div>
                                                                    </div>
                                                                    <div class="pmo-note" style="margin-top:8px;">Ziel: ${esc(att.destination || data.project?.destination || '—')}</div>
                                                                </div>
                                                                <div class="pmo-report-block"><h4>Aufgaben</h4><div class="pmo-report-list">
                                                                    <div class="pmo-report-row"><span>${reportCountLabel(items.length, 'Aufgabe geplant', 'Aufgaben geplant')}</span><strong>${reportCountLabel(done.length, 'erledigt', 'erledigt')}</strong></div>
                                                                    ${items.slice(0, 12).map(item => `<div class="pmo-report-row"><span>${esc(item.title || item.name || ('Job #' + item.id))}</span><strong>${esc(item.status || 'open')}</strong></div>`).join('') || '<div class="pmo-empty">Keine Aufgaben für diesen Tag.</div>'}
                                                                </div></div>
                                                                <div class="pmo-report-block"><h4>Berichte / Kommentare</h4><div class="pmo-report-list">
                                                                    ${comments.slice(0, 10).map(row => `<div class="pmo-report-row"><span>${esc(row.comment || row.body || row.note || row.text || 'Kommentar')}</span><strong>${esc(shortDateTime(row.created_at))}</strong></div>`).join('') || '<div class="pmo-empty">Keine Kommentare.</div>'}
                                                                </div></div>
                                                                <div class="pmo-report-block"><h4>Galerie</h4><div class="pmo-report-list">
                                                                    <div class="pmo-report-row"><span>${reportCountLabel(gallery.length, 'Datei', 'Dateien')}</span><strong>Kundengalerie</strong></div>
                                                                </div></div>
                                                                <div class="pmo-report-block"><h4>Material</h4><div class="pmo-report-list">
                                                                    <div class="pmo-report-row"><span>Task Material</span><strong>${materials.length}</strong></div>
                                                                    <div class="pmo-report-row"><span>Einmaliges Gruppenmaterial</span><strong>${shared.length}</strong></div>
                                                                </div></div>
                                                                <div class="pmo-report-block"><h4>Timeline</h4><div class="pmo-report-list">
                                                                    ${events.map(ev => `<div class="pmo-report-row"><span>${esc(ev.event_type || 'event')}</span><strong>${esc(shortDateTime(ev.event_at))}</strong></div>`).join('') || '<div class="pmo-empty">Keine Zeitereignisse.</div>'}
                                                                </div></div>
                                                            </div>`;
                    redrawIcons();
                }

                function itemKey(item) {
                    return `${item.source_type || 'work'}:${item.source_id || item.id}`;
                }

                function isDoneStatus(status) {
                    return ['done', 'completed', 'finished', 'closed', 'ended', 'erledigt'].includes(String(status || '').toLowerCase());
                }

                const plannerStatusOptions = [
                    { value: 'open', label: 'Offen' },
                    { value: 'planned', label: 'Geplant' },
                    { value: 'in_progress', label: 'In Arbeit' },
                    { value: 'done', label: 'Fertig' },
                    { value: 'blocked', label: 'Blockiert' },
                    { value: 'paused', label: 'Pausiert' },
                    { value: 'cancelled', label: 'Storniert' },
                ];

                function normalizePlannerStatus(status) {
                    const st = String(status || 'open').toLowerCase().trim();
                    if (['completed', 'finished', 'closed', 'ended', 'erledigt'].includes(st)) return 'done';
                    if (['working', 'arbeit', 'in-arbeit', 'inarbeit'].includes(st)) return 'in_progress';
                    if (['cancel', 'canceled', 'cancelled', 'storniert'].includes(st)) return 'cancelled';
                    if (st === 'geplant') return 'planned';
                    if (st === 'offen') return 'open';
                    return plannerStatusOptions.some(o => o.value === st) ? st : 'open';
                }

                function plannerStatusLabel(status) {
                    const key = normalizePlannerStatus(status);
                    return plannerStatusOptions.find(o => o.value === key)?.label || 'Offen';
                }

                function plannerStatusChipClass(status) {
                    const key = normalizePlannerStatus(status);
                    if (key === 'done') return 'green';
                    if (key === 'in_progress' || key === 'planned') return '';
                    if (key === 'blocked' || key === 'cancelled') return 'red';
                    if (key === 'paused') return 'orange';
                    return 'gray';
                }

                function plannerStatusOptionsHtml(current) {
                    const key = normalizePlannerStatus(current);
                    return plannerStatusOptions.map(opt => `<option value="${esc(opt.value)}" ${opt.value === key ? 'selected' : ''}>${esc(opt.label)}</option>`).join('');
                }

                function renderStatusSelect(item, extraClass = '') {
                    const plannerId = itemPlannerId(item);
                    return `<select class="pmo-status-select ${esc(extraClass)}" data-pmo-status-select data-item-id="${plannerId}" onclick="event.stopPropagation();" aria-label="Status ändern">
                                                                ${plannerStatusOptionsHtml(item.status)}
                                                            </select>`;
                }

                function allWorkItems() {
                    const q = String(state.workSearch || '').toLowerCase().trim();
                    const type = String(state.workType || '').trim();
                    return arr(state.payload?.items).filter(item => {
                        if (!state.showDone && isDoneStatus(item.status)) return false;
                        if (type && item.source_type !== type) return false;
                        if (!q) return true;
                        return [item.title, item.description, item.sub_stage_name, item.source_type, item.status, item.done_by_name]
                            .map(v => String(v || '').toLowerCase())
                            .some(v => v.includes(q));
                    });
                }

                function ganttWorkItems() {
                    const q = String(state.workSearch || '').toLowerCase().trim();
                    const type = String(state.workType || '').trim();
                    return arr(state.payload?.gantt_items || state.payload?.items).filter(item => {
                        if (type && item.source_type !== type) return false;
                        if (!q) return true;
                        return [item.title, item.description, item.source_type, item.status, item.done_by_name]
                            .map(v => String(v || '').toLowerCase())
                            .some(v => v.includes(q));
                    });
                }

                function employeeItems(employeeId) {
                    return allWorkItems().filter(item => arr(item.employee_ids).map(Number).includes(Number(employeeId)));
                }

                function countsFor(items) {
                    return {
                        ticket: items.filter(i => i.source_type === 'ticket').length,
                        appointment: items.filter(i => i.source_type === 'appointment').length,
                        personal: items.filter(i => i.source_type === 'personal_task').length,
                        task: items.filter(i => ['kanban_task', 'phase_activity', 'task_phase'].includes(i.source_type)).length,
                    };
                }

                function itemScheduleValue(item) {
                    return item.schedule_date_key
                        || item.start_at
                        || item.planned_start_at
                        || item.planned_date
                        || item.date
                        || null;
                }

                function dateKeyFromValue(value) {
                    if (!value) return 'no-date';
                    const s = String(value);
                    if (/^\d{4}-\d{2}-\d{2}/.test(s)) return s.slice(0, 10);

                    const d = new Date(s);
                    if (Number.isNaN(d.getTime())) return 'no-date';

                    const y = d.getFullYear();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${y}-${m}-${day}`;
                }

                function itemDateKey(item) {
                    return dateKeyFromValue(itemScheduleValue(item));
                }

                function dateLabelFromKey(key) {
                    if (!key || key === 'no-date') return 'Ohne Datum';
                    try {
                        return new Date(`${key}T12:00:00`).toLocaleDateString('de-DE', {
                            weekday: 'short',
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                        });
                    } catch (e) {
                        return key;
                    }
                }

                function itemStartMinutes(item) {
                    const raw = item.schedule_time_label || item.start_time || item.start_at || item.planned_start_at || '';
                    const s = String(raw || '');
                    const match = s.match(/(\d{1,2}):(\d{2})/);
                    if (!match) return 24 * 60 + 999;
                    return (Number(match[1]) * 60) + Number(match[2]);
                }

                function itemTimeRange(item) {
                    if (item.schedule_range_label) return item.schedule_range_label;
                    const start = timeOnly(item.start_at || item.planned_start_at || item.start_time || item.schedule_time_label);
                    const end = timeOnly(item.end_at || item.planned_end_at || item.end_time);
                    if (start !== '—' && end !== '—') return `${start} - ${end}`;
                    if (start !== '—') return start;
                    return 'Ohne Uhrzeit';
                }

                function sortItemsBySchedule(items) {
                    return arr(items).slice().sort((a, b) => {
                        const ak = itemDateKey(a);
                        const bk = itemDateKey(b);
                        if (ak !== bk) {
                            if (ak === 'no-date') return 1;
                            if (bk === 'no-date') return -1;
                            return ak.localeCompare(bk);
                        }

                        const timeDiff = itemStartMinutes(a) - itemStartMinutes(b);
                        if (timeDiff !== 0) return timeDiff;
                        return String(a.title || '').localeCompare(String(b.title || ''));
                    });
                }

                function groupItemsByScheduleDate(items) {
                    const groups = [];
                    const map = {};

                    sortItemsBySchedule(items).forEach(item => {
                        const key = itemDateKey(item);
                        if (!map[key]) {
                            map[key] = { key, label: dateLabelFromKey(key), items: [] };
                            groups.push(map[key]);
                        }
                        map[key].items.push(item);
                    });

                    return groups;
                }

                function employeeScheduleSummary(items) {
                    const sorted = sortItemsBySchedule(items).filter(item => itemDateKey(item) !== 'no-date');
                    const period = state.payload?.summary?.period_label || `${state.date} · ${state.mode}`;

                    if (!sorted.length) {
                        return { period, range: 'Keine geplante Uhrzeit' };
                    }

                    const first = sorted[0];
                    const last = sorted[sorted.length - 1];
                    const days = [...new Set(sorted.map(itemDateKey))];
                    const dayLabel = days.length === 1 ? dateLabelFromKey(days[0]) : `${dateLabelFromKey(days[0])} - ${dateLabelFromKey(days[days.length - 1])}`;
                    const times = sorted.map(itemStartMinutes).filter(v => Number.isFinite(v) && v < 24 * 60 + 900);

                    let range = dayLabel;
                    if (times.length) {
                        const min = Math.min(...times);
                        const max = Math.max(...times);
                        const minLabel = `${String(Math.floor(min / 60)).padStart(2, '0')}:${String(min % 60).padStart(2, '0')}`;
                        const maxLabel = `${String(Math.floor(max / 60)).padStart(2, '0')}:${String(max % 60).padStart(2, '0')}`;
                        range = `${dayLabel} · ${minLabel} - ${maxLabel}`;
                    }

                    return { period, range };
                }

                function renderEmployeeDateGroups(items) {
                    return groupItemsByScheduleDate(items).map(group => `
                                                                                                                                <section class="pmo-date-group" data-pmo-date-group="${esc(group.key)}">
                                                                                                                                    <div class="pmo-date-head">
                                                                                                                                        <span>${icon('calendar-days', 14)} ${esc(group.label)}</span>
                                                                                                                                        <small>${group.items.length} Arbeit${group.items.length === 1 ? '' : 'en'}</small>
                                                                                                                                    </div>
                                                                                                                                    ${group.items.map(renderWorkCard).join('')}
                                                                                                                                </section>
                                                                                                                            `).join('');
                }

                function workProgress(item) {
                    const steps = arr(item.steps || item.checklist);
                    if (!steps.length) return isDoneStatus(item.status) ? 100 : 0;
                    const done = steps.filter(step => step.is_completed || step.done || isDoneStatus(step.status)).length;
                    return Math.round((done / steps.length) * 100);
                }

                function roleHtml(emp) {
                    const roles = arr(emp.roles_clean || emp.roles).slice(0, 4);
                    if (!roles.length) return `<span class="pmo-role work">Operativ</span>`;
                    const map = { 'Projektleiter': 'pm', 'Außendienst': 'field', 'Team': 'team', 'Lead': 'pm', 'Operativ': 'work', 'Arbeit': 'work' };
                    return roles.map(r => `<span class="pmo-role ${map[r] || 'work'}">${esc(r)}</span>`).join('');
                }

                function renderBoard() {
                    const employees = arr(state.payload?.employees);
                    const board = document.getElementById('pmo-board');
                    if (!employees.length) {
                        board.innerHTML = `<div class="pmo-empty">Noch kein Team geladen. Füge Mitarbeiter über Team bearbeiten hinzu.</div>`;
                        return;
                    }
                    board.innerHTML = employees.map(emp => {
                        const items = sortItemsBySchedule(employeeItems(emp.id));
                        const c = countsFor(items);
                        const expanded = !!state.expandedEmployees[emp.id];
                        const visibleItems = expanded ? items : items.slice(0, 12);
                        const schedule = employeeScheduleSummary(items);
                        return `<article class="pmo-employee-column">
                                                                                                                            <div class="pmo-employee-head">
                                                                                                                                <div class="pmo-employee-profile">
                                                                                                                                    <div class="pmo-person-left">
                                                                                                                                        <img src="${esc(emp.photo_url || emp.image_url || '/images/icons/user.png')}" alt="${esc(emp.full_name)}">
                                                                                                                                        <div style="min-width:0;">
                                                                                                                                            <div class="pmo-person-name">${esc(emp.full_name || emp.name || ('Mitarbeiter #' + emp.id))}</div>
                                                                                                                                            <div class="pmo-role-row">${roleHtml(emp)}</div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                    <div class="pmo-inline" style="gap:6px;flex:0 0 auto;">
                                                                                                                                        <button type="button" class="pmo-icon-btn" data-pmo-open-group-material="${emp.id}" title="Gruppenmaterial für alle sichtbaren Jobs">${icon('package-plus')}</button>
                                                                                                                                        <button type="button" class="pmo-icon-btn" data-pmo-add-work="${emp.id}" title="Arbeit erstellen">${icon('plus')}</button>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                                <div class="pmo-mini-stats">
                                                                                                                                    <span class="pmo-mini-stat">${icon('ticket', 14)} ${c.ticket}</span>
                                                                                                                                    <span class="pmo-mini-stat">${icon('calendar', 14)} ${c.appointment}</span>
                                                                                                                                    <span class="pmo-mini-stat">${icon('user-check', 14)} ${c.personal}</span>
                                                                                                                                    <span class="pmo-mini-stat">${icon('list-checks', 14)} ${c.task}</span>
                                                                                                                                </div>
                                                                                                                                <div class="pmo-employee-schedule">
                                                                                                                                    <span>${icon('calendar-range', 14)} ${esc(schedule.period)}</span>
                                                                                                                                    <strong>${esc(schedule.range)}</strong>
                                                                                                                                </div>
                                                                                                                                ${renderEmployeeAttendance(emp, items)}
                                                                                                                                ${renderEmployeeGroupMaterials(emp.id, items)}
                                                                                                                                <div class="pmo-column-tools">
                                                                                                                                    <span>${items.length} Arbeiten für diesen Zeitraum</span>
                                                                                                                                    ${items.length > 12 ? `<button type="button" class="pmo-btn-soft" data-pmo-toggle-employee="${emp.id}">${expanded ? 'Weniger' : `Mehr (${items.length - 12})`}</button>` : ''}
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="pmo-items">
                                                                                                                                ${visibleItems.length ? renderEmployeeDateGroups(visibleItems) : `<div class="pmo-empty">Keine Arbeiten für diesen Zeitraum oder Filter.</div>`}
                                                                                                                            </div>
                                                                                                                        </article>`;
                    }).join('');
                }

                function typeLabel(type) {
                    return {
                        kanban_task: 'Montage Aufgabe',
                        task_phase: 'Task Phase',
                        phase_activity: 'Phase-Aktivität',
                        personal_task: 'Personal Task',
                        appointment: 'Termin',
                        ticket: 'Ticket',
                    }[type] || type;
                }

                function typeIcon(type) {
                    return {
                        kanban_task: 'list-checks',
                        task_phase: 'workflow',
                        phase_activity: 'git-branch',
                        personal_task: 'user-check',
                        appointment: 'calendar-clock',
                        ticket: 'ticket',
                    }[type] || 'circle';
                }

                function renderWorkCard(item) {
                    const progress = workProgress(item);
                    const done = isDoneStatus(item.status);
                    const plannerId = itemPlannerId(item);
                    const sourceClass = Number(state.dependencySourceId || 0) === plannerId ? 'is-link-source' : '';
                    return `<div class="pmo-work-card ${done ? 'is-done' : ''} ${sourceClass}" data-pmo-work="${esc(itemKey(item))}" data-pmo-link-card="${plannerId}" data-item-id="${plannerId}">
                                                                                                                        <div class="pmo-work-timebar">
                                                                                                                            <span>${icon('calendar-days', 13)} ${esc(item.schedule_date_label || dateLabelFromKey(itemDateKey(item)))}</span>
                                                                                                                            <strong>${icon('clock-3', 13)} ${esc(itemTimeRange(item))}</strong>
                                                                                                                        </div>
                                                                                                                        <div class="pmo-work-top">
                                                                                                                            <div class="pmo-type-icon ${esc(item.source_type)}">${icon(typeIcon(item.source_type), 18)}</div>
                                                                                                                            <div style="min-width:0;flex:1;">
                                                                                                                                <div class="pmo-work-title">${esc(item.title)}</div>
                                                                                                                                <div class="pmo-work-desc">${esc(item.description || item.sub_stage_name || item.phase_name || 'Keine Beschreibung')}</div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="pmo-work-meta">
                                                                                                                            <div class="pmo-pill-row">
                                                                                                                                <span class="pmo-chip gray">${esc(typeLabel(item.source_type))}</span>
                                                                                                                                <span class="pmo-chip ${plannerStatusChipClass(item.status)}">${esc(plannerStatusLabel(item.status))}</span>
                                                                                                                                ${renderStatusSelect(item, 'pmo-card-status-select')}
                                                                                                                            </div>
                                                                                                                            <span class="pmo-chip">${timeOnly(item.start_at || item.planned_start_at)}</span>
                                                                                                                        </div>
                                                                                                                        ${renderDoneMeta(item)}
                                                                                                                        ${renderCardMaterialStats(item)}
                                                                                                                        <div class="pmo-work-progress"><span style="width:${progress}%"></span></div>
                                                                                                                    </div>`;
                }

                function findWorkItem(key) {
                    return arr(state.payload?.items)
                        .concat(arr(state.payload?.gantt_items))
                        .find(item => itemKey(item) === key) || null;
                }

                function findWorkItemByPlannerId(plannerItemId) {
                    return arr(state.payload?.items)
                        .concat(arr(state.payload?.gantt_items))
                        .find(item => Number(itemPlannerId(item)) === Number(plannerItemId)) || null;
                }

                function employeeById(id) {
                    return arr(state.payload?.employees).concat(arr(state.payload?.employees_active)).find(e => Number(e.id) === Number(id));
                }

                function itemTeams(item) {
                    return arr(item.employee_ids).map(id => employeeById(id)).filter(Boolean);
                }

                function doneByName(item) {
                    return item.done_by_name
                        || item.done_by_employee?.full_name
                        || item.done_by_employee?.name
                        || (item.done_by_employee_id ? `Mitarbeiter #${item.done_by_employee_id}` : '');
                }

                function doneAtLabel(item) {
                    return item.done_at ? shortDateTime(item.done_at) : '';
                }

                function renderDoneMeta(item) {
                    if (!isDoneStatus(item.status)) return '';
                    const by = doneByName(item);
                    const at = doneAtLabel(item);
                    return `<div class="pmo-done-meta">${icon('badge-check', 13)} <span>Erledigt${by ? ` von ${esc(by)}` : ''}${at ? ` · ${esc(at)}` : ''}</span></div>`;
                }

                function renderStatusHistoryMini(item) {
                    const rows = arr(item.status_history || item.history || []).slice(0, 8);
                    if (!rows.length) {
                        return `<div class="pmo-empty" style="margin:0;padding:14px;">Noch keine Status-Historie vorhanden.</div>`;
                    }

                    return `<div class="pmo-history-mini">${rows.map(row => {
                        const from = row.old_status_label || plannerStatusLabel(row.old_status || 'open');
                        const to = row.new_status_label || row.status_label || plannerStatusLabel(row.new_status || row.status || 'open');
                        const by = row.changed_by_name || row.employee_name || '—';
                        const when = row.changed_at_label || row.changed_at || row.created_at || '—';
                        return `<div class="pmo-history-mini-row"><strong>${esc(from)} → ${esc(to)}</strong><small>${esc(when)} · ${esc(by)}${row.note ? ` · ${esc(row.note)}` : ''}</small></div>`;
                    }).join('')}</div>`;
                }

                function renderTeamPillsForItem(item) {
                    const team = itemTeams(item);
                    return team.length ? team.map(emp => `<span class="pmo-team-pill"><img src="${esc(emp.photo_url || emp.image_url || '/images/icons/user.png')}" alt="${esc(emp.full_name || emp.name || '')}">${esc(emp.full_name || emp.name || ('#' + emp.id))}</span>`).join('') : `<div class="pmo-empty" style="margin:0;padding:14px;">Keine Mitarbeiter zugewiesen.</div>`;
                }

                function stepRowsForItem(item) {
                    return arr(item.steps || item.checklist || item.keys || item.step_items);
                }


                function setLocalItemStatus(plannerItemId, status, returnedItem = null) {
                    const normalized = normalizePlannerStatus(returnedItem?.status || status);
                    const apply = (row) => {
                        if (Number(itemPlannerId(row)) === Number(plannerItemId)) {
                            row.status = normalized;
                            row.status_label = returnedItem?.status_label || plannerStatusLabel(normalized);
                            row.is_done = normalized === 'done';
                            if (returnedItem?.done_at !== undefined) row.done_at = returnedItem.done_at;
                            if (returnedItem?.done_by_employee_id !== undefined) row.done_by_employee_id = returnedItem.done_by_employee_id;
                            if (returnedItem?.done_by_employee !== undefined) row.done_by_employee = returnedItem.done_by_employee;
                            if (returnedItem?.done_by_name !== undefined) row.done_by_name = returnedItem.done_by_name;
                            if (returnedItem?.status_history !== undefined) row.status_history = returnedItem.status_history;
                        }
                    };
                    arr(state.payload?.items).forEach(apply);
                    arr(state.payload?.gantt_items).forEach(apply);
                }

                async function updateItemStatus(plannerItemId, status, note = '', extra = {}) {
                    plannerItemId = Number(plannerItemId || 0);
                    if (!plannerItemId) return;

                    const url = itemEndpoint('itemStatusUpdate', plannerItemId);

                    if (!url) {
                        toast('Endpoint fehlt', 'itemStatusUpdate fehlt in plannerConfig.endpoints.', 'bad');
                        return;
                    }

                    const normalized = normalizePlannerStatus(status);

                    const json = await requestJson(url, {
                        method: 'PATCH',
                        body: JSON.stringify({ status: normalized, note, ...(extra || {}) }),
                    });

                    setLocalItemStatus(plannerItemId, normalized, json.item || null);
                    toast('Status aktualisiert', `${plannerStatusLabel(normalized)} wurde gespeichert.`, 'good');

                    if (json.report_saved) {
                        toast('Bericht gespeichert', 'Der Fertig-Bericht wurde im passenden Modul gespeichert.', 'good');
                    }

                    if (json.item?.comments) {
                        const row = findWorkItemByPlannerId(plannerItemId);
                        if (row) {
                            row.comments = json.item.comments;
                            row.reports = json.item.reports || json.item.comments;
                        }
                    }

                    renderAll();

                    if (state.activeWorkKey) {
                        const activeItem = findWorkItem(state.activeWorkKey);
                        if (activeItem && Number(itemPlannerId(activeItem)) === plannerItemId) {
                            if (!state.showDone && normalized === 'done') {
                                closeJobDrawer();
                            } else {
                                openJobDrawer(state.activeWorkKey);
                            }
                        }
                    }
                }

                function openCompletionReportModal(plannerItemId, status = 'done') {
                    plannerItemId = Number(plannerItemId || 0);
                    if (!plannerItemId) return;

                    const item = findWorkItemByPlannerId(plannerItemId);
                    state.completionReport = {
                        plannerItemId,
                        status: normalizePlannerStatus(status || 'done'),
                        previousStatus: normalizePlannerStatus(item?.status || 'open'),
                    };

                    document.getElementById('pmo-completion-report-sub').textContent = item
                        ? `${typeLabel(item.source_type)} · ${item.title || 'Arbeit'}`
                        : 'Bericht schreiben oder ohne Bericht fertigstellen.';

                    document.getElementById('pmo-completion-report-text').value = '';
                    document.getElementById('pmo-completion-report-next-step').value = '';
                    document.getElementById('pmo-completion-report-due-date').value = '';

                    openModal('pmo-completion-report-modal');
                    setTimeout(() => document.getElementById('pmo-completion-report-text')?.focus(), 120);
                    redrawIcons();
                }

                async function submitCompletionReport(skipReport = false) {
                    const modal = state.completionReport || {};
                    const plannerItemId = Number(modal.plannerItemId || 0);

                    if (!plannerItemId) {
                        closeModal('pmo-completion-report-modal');
                        return;
                    }

                    const report = String(document.getElementById('pmo-completion-report-text')?.value || '').trim();
                    const nextStep = String(document.getElementById('pmo-completion-report-next-step')?.value || '').trim();
                    const dueDate = String(document.getElementById('pmo-completion-report-due-date')?.value || '').trim();

                    if (!skipReport && !report) {
                        toast('Bericht fehlt', 'Bitte Bericht schreiben oder „Ohne Bericht fertigstellen“ wählen.', 'bad');
                        return;
                    }

                    await updateItemStatus(plannerItemId, 'done', report, {
                        report,
                        report_text: report,
                        report_next_step: nextStep,
                        report_due_date: dueDate || null,
                        skip_report: skipReport ? 1 : 0,
                    });

                    closeModal('pmo-completion-report-modal');
                    state.completionReport = null;
                    await loadPayload(false);
                }

                function renderTaskStatusManager(item) {
                    const plannerId = itemPlannerId(item);
                    const current = normalizePlannerStatus(item.status);
                    return `<div class="pmo-status-box">
                                                                <div class="pmo-status-current">
                                                                    <div>
                                                                        <span class="pmo-label">Aktueller Status</span>
                                                                        <span class="pmo-chip ${plannerStatusChipClass(current)}">${icon(isDoneStatus(current) ? 'check-circle-2' : 'activity', 13)} ${esc(plannerStatusLabel(current))}</span>
                                                                    </div>
                                                                    ${renderStatusSelect(item)}
                                                                </div>
                                                                <div class="pmo-status-buttons">
                                                                    <button type="button" class="pmo-btn-soft" data-pmo-status-btn="open" data-item-id="${plannerId}">${icon('circle', 13)} Offen</button>
                                                                    <button type="button" class="pmo-btn-soft" data-pmo-status-btn="in_progress" data-item-id="${plannerId}">${icon('play-circle', 13)} In Arbeit</button>
                                                                    <button type="button" class="pmo-btn-soft" data-pmo-status-btn="done" data-item-id="${plannerId}">${icon('check-circle-2', 13)} Fertig</button>
                                                                    <button type="button" class="pmo-btn-soft" data-pmo-status-btn="blocked" data-item-id="${plannerId}">${icon('ban', 13)} Blockiert</button>
                                                                </div>
                                                                ${renderDoneMeta(item)}
                                                                <div class="pmo-note">Dieser Status wird auch in der Quelle aktualisiert: Ticket, Personal Task, Termin, Kanban Task oder Phase Activity.</div>
                                                                <div style="margin-top:12px;">${renderStatusHistoryMini(item)}</div>
                                                            </div>`;
                }

                function materialRowsForItem(item) {
                    return arr(item.materials || item.material_list || item.planner_materials);
                }

                function sharedMaterialRowsForItem(item) {
                    return arr(item.shared_materials || item.group_materials || item.shared_group_materials);
                }

                function sharedMaterialSummaryForItem(item) {
                    const rows = sharedMaterialRowsForItem(item);
                    const preset = item?.shared_material_summary || item?.group_material_summary;
                    return {
                        total: Number(preset?.total ?? rows.length ?? 0),
                        added: Number(preset?.added ?? rows.length ?? 0),
                    };
                }

                function groupMaterialKey(row) {
                    return String(row?.material_group_uuid || row?.group_uuid || row?.id || `${row?.name || 'material'}-${row?.article_no || ''}`);
                }

                function groupMaterialsForEmployee(employeeId, visibleItems = []) {
                    const itemIds = arr(visibleItems).map(item => Number(itemPlannerId(item) || 0)).filter(Boolean);
                    const itemIdSet = new Set(itemIds);
                    const rows = arr(state.payload?.group_materials || state.payload?.shared_materials);
                    const seen = new Set();

                    return rows.filter(row => {
                        const rowEmp = Number(row.employee_id || row.material_scope_employee_id || row.group_employee_id || 0);
                        const linkedIds = arr(row.item_ids || row.linked_item_ids).map(Number).filter(Boolean);
                        const employeeMatches = !rowEmp || rowEmp === Number(employeeId);
                        const itemsMatch = !linkedIds.length || linkedIds.some(id => itemIdSet.has(Number(id)));
                        const key = groupMaterialKey(row);

                        if (!employeeMatches || !itemsMatch || seen.has(key)) return false;
                        seen.add(key);
                        return true;
                    });
                }

                function sharedMaterialCollapseKey(scope, id) {
                    return `${scope}:${id || 'global'}`;
                }

                function isSharedMaterialCollapsed(key, defaultValue = true) {
                    if (!state.collapsedSharedMaterials) state.collapsedSharedMaterials = {};
                    return Object.prototype.hasOwnProperty.call(state.collapsedSharedMaterials, key)
                        ? !!state.collapsedSharedMaterials[key]
                        : defaultValue;
                }

                function sharedMaterialHeaderHtml(key, title, rows, options = {}) {
                    const collapsed = isSharedMaterialCollapsed(key, options.defaultCollapsed ?? true);
                    const totalQty = rows.reduce((sum, row) => sum + Number(row.qty ?? row.quantity ?? 0), 0);
                    const jobs = rows.reduce((max, row) => Math.max(max, Number(row.linked_item_count || arr(row.item_ids || row.linked_item_ids).length || 0)), 0);
                    const summary = options.summary || `${rows.length} Position${rows.length === 1 ? '' : 'en'}${jobs ? ` · ${jobs} Jobs` : ''}${totalQty ? ` · ${totalQty} Stk` : ''}`;

                    return `<button type="button" class="pmo-shared-material-title" data-pmo-toggle-shared-material="${esc(key)}" aria-expanded="${collapsed ? 'false' : 'true'}">
                                                                <span class="pmo-shared-material-title-left">${icon('package-check', 14)} <span>${esc(title)}</span> <b class="pmo-shared-material-count">${rows.length}</b></span>
                                                                <span class="pmo-shared-material-title-right"><span class="pmo-shared-material-summary">${esc(summary)}</span><span class="pmo-shared-material-toggle">${icon('chevron-down', 14)}</span></span>
                                                            </button>`;
                }

                function renderEmployeeGroupMaterials(employeeId, visibleItems = []) {
                    const rows = groupMaterialsForEmployee(employeeId, visibleItems);
                    if (!rows.length) return '';
                    const key = sharedMaterialCollapseKey('employee', employeeId);
                    const collapsed = isSharedMaterialCollapsed(key, true);

                    return `<div class="pmo-shared-material-panel ${collapsed ? 'is-collapsed' : ''}">
                                                                                ${sharedMaterialHeaderHtml(key, 'Einmaliges Gruppenmaterial', rows, { defaultCollapsed: true })}
                                                                                <div class="pmo-shared-material-body">
                                                                                    ${rows.map(row => {
                        const qty = row.qty ?? row.quantity ?? 1;
                        const unit = row.unit || row.measure || row.measure_unit || 'Stk';
                        const linked = Number(row.linked_item_count || arr(row.item_ids || row.linked_item_ids).length || 0);
                        return `<div class="pmo-shared-material-row">
                                                                                                    <span><strong>${esc(row.name || row.title || 'Material')}</strong><small>${esc(row.article_no || row.sku || row.distributor_article_no || '')}</small></span>
                                                                                                    <b>${esc(qty)} ${esc(unit)}</b>
                                                                                                    <em>${linked ? `${linked} Jobs verknüpft` : 'Für diesen Zeitraum'}</em>
                                                                                                </div>`;
                    }).join('')}
                                                                                </div>
                                                                            </div>`;
                }

                function renderSharedMaterialRows(item) {
                    const rows = sharedMaterialRowsForItem(item);
                    if (!rows.length) return '';
                    const key = sharedMaterialCollapseKey('item', itemPlannerId(item) || item?.id || item?.source_id);
                    const collapsed = isSharedMaterialCollapsed(key, true);

                    return `<div class="pmo-shared-material-box ${collapsed ? 'is-collapsed' : ''}">
                                                                                ${sharedMaterialHeaderHtml(key, 'Gemeinsames Material — nur einmal nehmen', rows, { defaultCollapsed: true })}
                                                                                <div class="pmo-shared-material-body"><div class="pmo-material-scroll"><table class="pmo-material-table pmo-material-table-rich">
                                                                                    <thead><tr><th>Material</th><th>Menge</th><th>Einheit</th><th>Gültig für</th><th>Hinweis</th></tr></thead>
                                                                                    <tbody>${rows.map(row => {
                        const qty = row.qty ?? row.quantity ?? 1;
                        const unit = row.unit || row.measure || row.measure_unit || 'Stk';
                        const linked = Number(row.linked_item_count || arr(row.item_ids || row.linked_item_ids).length || 0);
                        return `<tr>
                                                                                            <td><strong>${esc(row.name || row.title || 'Gruppenmaterial')}</strong><div class="pmo-note">${esc(row.article_no || row.sku || row.distributor_article_no || '')}</div></td>
                                                                                            <td>${esc(qty)}</td>
                                                                                            <td>${esc(unit)}</td>
                                                                                            <td>${esc(row.period_label || row.material_group_name || row.group_name || 'Zeitraum')}</td>
                                                                                            <td><span class="pmo-chip green">Einmalig für ${linked || 'mehrere'} Jobs</span></td>
                                                                                        </tr>`;
                    }).join('')}</tbody>
                                                                                </table></div></div>
                                                                            </div>`;
                }

                function materialIsRequest(row) {
                    const origin = String(row?.origin_type || row?.source_origin || row?.origin || '').toLowerCase();
                    return ['employee_request', 'asked_by_employee', 'material_request', 'request'].includes(origin)
                        || !!row?.requested_at
                        || !!row?.requested_by_employee_id
                        || row?.is_request === true;
                }

                function materialIsResponded(row) {
                    const status = String(row?.status || row?.response_status || '').toLowerCase();
                    return !!row?.responded_at
                        || !!row?.approved_at
                        || !!row?.rejected_at
                        || !!row?.ordered_at
                        || !!row?.received_at
                        || ['approved', 'accepted', 'rejected', 'declined', 'responded', 'ordered', 'received', 'done', 'completed', 'added'].includes(status);
                }

                function materialIsOpenRequest(row) {
                    if (row?.is_request_open === true) return true;
                    if (row?.is_request_open === false) return false;
                    return materialIsRequest(row) && !materialIsResponded(row);
                }

                function materialIsActive(row) {
                    return row?.active !== false && row?.is_active !== false && Number(row?.active ?? row?.is_active ?? 1) !== 0;
                }

                function materialSummaryForItem(item) {
                    const preset = item?.material_summary || item?.materials_summary || item?.material_counts;
                    if (preset && typeof preset === 'object') {
                        const requestedOpen = Number(preset.requested_open ?? preset.requested_not_responded ?? preset.open_requests ?? 0);
                        return {
                            total: Number(preset.total ?? 0),
                            active: Number(preset.active ?? 0),
                            inactive: Number(preset.inactive ?? 0),
                            added: Number(preset.added ?? preset.active ?? 0),
                            requested_total: Number(preset.requested_total ?? preset.requested ?? 0),
                            requested_open: requestedOpen,
                            requested_not_responded: requestedOpen,
                            requested_responded: Number(preset.requested_responded ?? 0),
                        };
                    }

                    const materials = materialRowsForItem(item);
                    let active = 0;
                    let inactive = 0;
                    let added = 0;
                    let requestedTotal = 0;
                    let requestedOpen = 0;
                    let requestedResponded = 0;

                    materials.forEach(row => {
                        const isActive = materialIsActive(row);
                        const isReq = materialIsRequest(row);
                        const isOpen = materialIsOpenRequest(row);

                        if (isActive) active += 1;
                        else inactive += 1;

                        if (isReq) {
                            requestedTotal += 1;
                            if (isOpen) requestedOpen += 1;
                            else requestedResponded += 1;
                        }

                        if (isActive && !isOpen) added += 1;
                    });

                    return {
                        total: materials.length,
                        active,
                        inactive,
                        added,
                        requested_total: requestedTotal,
                        requested_open: requestedOpen,
                        requested_not_responded: requestedOpen,
                        requested_responded: requestedResponded,
                    };
                }

                function renderMaterialSummaryChips(item, compact = false) {
                    const s = materialSummaryForItem(item);
                    const shared = sharedMaterialSummaryForItem(item);
                    const sizeLabel = compact ? 'Mat.' : 'Material';
                    return `
                                                                                <span class="pmo-chip blue">${icon('package', 12)} ${sizeLabel}: ${esc(s.total)}</span>
                                                                                <span class="pmo-chip green">${icon('check-circle-2', 12)} Hinzugefügt: ${esc(s.added)}</span>
                                                                                ${shared.total > 0 ? `<span class="pmo-chip purple">${icon('package-check', 12)} Gemeinsam: ${esc(shared.total)}</span>` : ''}
                                                                                <span class="pmo-chip ${s.requested_open > 0 ? 'orange' : 'gray'}">${icon('message-square-warning', 12)} Offen: ${esc(s.requested_open)}</span>
                                                                            `;
                }

                function renderCardMaterialStats(item) {
                    return `<div class="pmo-card-material-stats">${renderMaterialSummaryChips(item, true)}</div>`;
                }

                function originLabel(origin) {
                    const key = String(origin || 'manual').toLowerCase();
                    const map = {
                        predefined: 'Vordefiniert',
                        manual: 'Manuell',
                        employee_request: 'Mitarbeiter Anfrage',
                        asked_by_employee: 'Mitarbeiter Anfrage',
                        master_set_task: 'MasterSet Task',
                        master_set_predefined: 'MasterSet',
                        copied_from_personal_task: 'Personal Task',
                        deal_final: 'Deal Material',
                        auftrag_final: 'Auftrag Material',
                        product_library: 'Produktkatalog',
                        offer_detail: 'Auftrag',
                        group_material: 'Gruppenmaterial',
                        group_manual: 'Gruppenmaterial',
                    };
                    return map[key] || origin || 'Manuell';
                }

                function originChipClass(origin) {
                    const key = String(origin || '').toLowerCase();
                    if (['deal_final', 'auftrag_final', 'offer_detail'].includes(key)) return 'green';
                    if (['group_material', 'group_manual'].includes(key)) return 'purple';
                    if (['employee_request', 'asked_by_employee'].includes(key)) return 'orange';
                    if (['master_set_task', 'master_set_predefined', 'predefined'].includes(key)) return '';
                    return 'gray';
                }

                function formatMoney(value) {
                    const n = Number(value || 0);
                    if (!Number.isFinite(n) || n === 0) return '—';
                    return n.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
                }

                function materialImageUrl(m) {
                    return m.image_url || m.img || m.image || m.product_image || m.main_image_url || '/images/icons/placeholder.svg';
                }

                function plannerItemIdOrToast(item) {
                    const plannerItemId = itemPlannerId(item);
                    if (!plannerItemId) {
                        toast('Nicht möglich', 'Diese Karte hat noch keine PlannerItem-ID.', 'bad');
                        return 0;
                    }
                    return plannerItemId;
                }

                function renderSteps(item) {
                    const plannerItemId = itemPlannerId(item);
                    const steps = stepRowsForItem(item);

                    const addBox = plannerItemId ? `
                                                                                <div class="pmo-step-add-box pmo-hidden" id="pmo-step-add-box-${plannerItemId}">
                                                                                    <div class="pmo-modal-grid">
                                                                                        <div class="pmo-field pmo-span-2">
                                                                                            <label class="pmo-label">Schritt Titel</label>
                                                                                            <input type="text" class="pmo-input" data-pmo-step-title placeholder="z.B. Vorbereitung, Kontrolle, Montage...">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Datum</label>
                                                                                            <input type="date" class="pmo-input" data-pmo-step-date value="${esc(state.date)}">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Zeit</label>
                                                                                            <input type="time" class="pmo-input" data-pmo-step-time value="${esc(timeOnly(item.start_at || item.planned_start_at) || '08:00')}">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Quelle</label>
                                                                                            <select class="pmo-select" data-pmo-step-origin>
                                                                                                <option value="manual">Manuell</option>
                                                                                                <option value="employee_request">Mitarbeiter Anfrage</option>
                                                                                                <option value="predefined">Vordefiniert</option>
                                                                                                <option value="master_set_task">MasterSet Task</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Pflicht</label>
                                                                                            <select class="pmo-select" data-pmo-step-required>
                                                                                                <option value="1">Ja</option>
                                                                                                <option value="0">Nein</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="pmo-field pmo-span-2">
                                                                                            <label class="pmo-label">Beschreibung</label>
                                                                                            <textarea class="pmo-textarea" data-pmo-step-description placeholder="Optional..."></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="pmo-step-actions">
                                                                                        <button type="button" class="pmo-btn" data-pmo-step-save="${plannerItemId}">${icon('save', 14)} Schritt speichern</button>
                                                                                        <button type="button" class="pmo-btn-soft" data-pmo-step-cancel="${plannerItemId}">Abbrechen</button>
                                                                                    </div>
                                                                                </div>` : '';

                    const rows = steps.length
                        ? `<div class="pmo-step-list">${steps.map((step, idx) => {
                            const done = step.is_completed || step.done || isDoneStatus(step.status);
                            const origin = step.origin_type || step.source_origin || step.origin || (step.is_predefined ? 'predefined' : 'manual');
                            return `<div class="pmo-step-item" data-step-id="${esc(step.id || '')}">
                                                                                        <div class="pmo-step-number">${idx + 1}</div>
                                                                                        <div>
                                                                                            <div class="pmo-work-title">
                                                                                                <label class="pmo-inline-check">
                                                                                                    <input type="checkbox" ${done ? 'checked' : ''} data-pmo-step-toggle="${esc(step.id || '')}" data-item-id="${plannerItemId}">
                                                                                                    <span>${esc(step.title || step.key_name || step.name || ('Schritt #' + (idx + 1)))}</span>
                                                                                                </label>
                                                                                            </div>
                                                                                            <div class="pmo-work-desc">${esc(step.description || step.note || '')}</div>
                                                                                            <div class="pmo-job-meta">
                                                                                                <span class="pmo-chip gray">${esc(step.due_at || step.due_date || step.date || 'Ohne Datum')}</span>
                                                                                                <span class="pmo-chip gray">${esc(step.due_time || step.time || 'Ohne Zeit')}</span>
                                                                                                <span class="pmo-chip ${originChipClass(origin)}">${esc(originLabel(origin))}</span>
                                                                                                ${step.requested_at ? `<span class="pmo-chip orange">Angefragt: ${esc(step.requested_at)}</span>` : ''}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="pmo-step-row-actions">
                                                                                            <span class="pmo-chip ${done ? 'green' : 'gray'}">${done ? 'Done' : 'Open'}</span>
                                                                                            ${step.id ? `<button type="button" class="pmo-icon-btn pmo-danger" data-pmo-step-delete="${esc(step.id)}" data-item-id="${plannerItemId}" title="Schritt löschen">${icon('trash-2', 14)}</button>` : ''}
                                                                                        </div>
                                                                                    </div>`;
                        }).join('')}</div>`
                        : `<div class="pmo-empty" style="margin:0;padding:18px;">Noch keine Schritte vorhanden. Du kannst für jede Karte eigene Schritte hinzufügen.</div>`;

                    return `<div class="pmo-section-action-row">
                                                                                <button type="button" class="pmo-btn-soft" data-pmo-step-open="${plannerItemId}">${icon('plus', 14)} Schritt hinzufügen</button>
                                                                                <span class="pmo-chip gray">${esc(typeLabel(item.source_type))}</span>
                                                                            </div>${addBox}${rows}`;
                }

                function renderMaterials(item) {
                    const plannerItemId = itemPlannerId(item);
                    const materials = materialRowsForItem(item);
                    const sharedBlock = renderSharedMaterialRows(item);

                    const toolbar = `<div class="pmo-section-action-row">
                                                                                <button type="button" class="pmo-btn" data-pmo-open-material-modal="${plannerItemId}">${icon('package-plus', 14)} Material</button>
                                                                                <button type="button" class="pmo-btn-soft" data-pmo-import-deal-materials="${plannerItemId}">${icon('download-cloud', 14)} Auftrag/Deal importieren</button>
                                                                                ${renderMaterialSummaryChips(item)}
                                                                                <span class="pmo-chip gray">Aktiv/Inaktiv je Job steuerbar</span>
                                                                            </div>`;

                    if (!materials.length) {
                        return `${toolbar}${sharedBlock}<div class="pmo-empty" style="margin:0;padding:18px;">Noch keine eigene Materialliste vorhanden. Gemeinsames Material wird oben nur einmal angezeigt und nicht pro Aufgabe gezählt.</div>`;
                    }

                    return `${toolbar}${sharedBlock}<div class="pmo-material-scroll"><table class="pmo-material-table pmo-material-table-rich">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Aktiv</th>
                                                                                        <th>Bild</th>
                                                                                        <th>Material</th>
                                                                                        <th>Quelle</th>
                                                                                        <th>Anfrage</th>
                                                                                        <th>Menge</th>
                                                                                        <th>Einheit</th>
                                                                                        <th>EK</th>
                                                                                        <th>VK</th>
                                                                                        <th>Distributor</th>
                                                                                        <th></th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    ${materials.map(m => {
                        const id = m.id || m.material_id || '';
                        const isRequest = materialIsRequest(m);
                        const isOpenRequest = materialIsOpenRequest(m);
                        const active = !isRequest && m.active !== false && m.is_active !== false && Number(m.active ?? 1) !== 0;
                        const origin = m.origin_type || m.source_origin || m.origin || m.source_type || 'manual';
                        const qty = m.qty ?? m.quantity ?? 1;
                        const unit = m.unit || m.measure || m.measure_unit || 'Stk';
                        const purchase = m.purchase_price ?? m.ek ?? m.unit_purchase_price ?? 0;
                        const price = m.unit_price ?? m.price ?? m.vk ?? 0;
                        return `<tr class="${active ? '' : 'is-inactive'} ${isRequest ? 'is-request' : ''}">
                                                                                            <td>${isRequest ? `<span class="pmo-chip orange">Anfrage</span>` : `<input type="checkbox" ${active ? 'checked' : ''} data-pmo-material-toggle="${esc(id)}" data-item-id="${plannerItemId}">`}</td>
                                                                                            <td><img class="pmo-material-img" src="${esc(materialImageUrl(m))}" alt="${esc(m.name || m.title || 'Material')}"></td>
                                                                                            <td>
                                                                                                <strong>${esc(m.name || m.title || m.product || 'Material')}</strong>
                                                                                                <div class="pmo-note">${esc(m.article_no || m.distributor_article_no || m.sku || '')}</div>
                                                                                                ${isRequest && (m.note || m.description) ? `<div class="pmo-material-request-note">${esc(m.note || m.description)}</div>` : ''}
                                                                                                ${isRequest && m.requested_by_name ? `<div class="pmo-note">Angefragt von: ${esc(m.requested_by_name)}</div>` : ''}
                                                                                            </td>
                                                                                            <td><span class="pmo-chip ${originChipClass(origin)}">${esc(originLabel(origin))}</span></td>
                                                                                            <td><span class="pmo-chip ${isOpenRequest ? 'orange' : (isRequest ? 'green' : 'gray')}">${isOpenRequest ? 'Offen' : (isRequest ? 'Beantwortet' : '—')}</span></td>
                                                                                            <td>${esc(qty)}</td>
                                                                                            <td>${esc(unit)}</td>
                                                                                            <td>${esc(formatMoney(purchase))}</td>
                                                                                            <td>${esc(formatMoney(price))}</td>
                                                                                            <td>${esc(m.distributor_name || m.supplier || '—')}</td>
                                                                                            <td>${isRequest ? `<span class="pmo-chip orange">Nuriva</span>` : (id ? `<button type="button" class="pmo-icon-btn pmo-danger" data-pmo-material-delete="${esc(id)}" data-item-id="${plannerItemId}" title="Material löschen">${icon('trash-2', 14)}</button>` : '')}</td>
                                                                                        </tr>`;
                    }).join('')}
                                                                                </tbody>
                                                                            </table></div>`;
                }


                function materialRequestId(row) {
                    const raw = row?.material_request_id
                        ?? row?.request_id
                        ?? row?.id
                        ?? row?.source_id
                        ?? '';

                    const str = String(raw ?? '').trim();
                    const match = str.match(/\d+/);
                    return match ? Number(match[0]) : 0;
                }

                function materialRequestStatus(row) {
                    const status = String(row?.request_status || row?.status || '').toLowerCase().trim();

                    if (row?.is_request_open === true || ['requested', 'open', 'pending', 'new', 'waiting'].includes(status)) {
                        return 'open';
                    }

                    if (row?.accepted_at || row?.approved_at || ['accepted', 'approved', 'added', 'ordered', 'received', 'done', 'completed'].includes(status)) {
                        return 'accepted';
                    }

                    if (row?.rejected_at || ['rejected', 'declined', 'denied', 'cancelled', 'canceled'].includes(status)) {
                        return 'rejected';
                    }

                    if (materialIsOpenRequest(row)) {
                        return 'open';
                    }

                    return status || 'open';
                }

                function materialRequestEmployeeId(row) {
                    return Number(row?.requested_by_employee_id || row?.employee_id || row?.requested_by?.id || 0);
                }

                function materialRequestEmployeeName(row) {
                    const id = materialRequestEmployeeId(row);
                    const emp = id ? employeeById(id) : null;

                    return row?.requested_by_name
                        || row?.requested_by_employee_name
                        || row?.employee_name
                        || row?.requested_by?.full_name
                        || row?.requested_by?.name
                        || emp?.full_name
                        || emp?.name
                        || (id ? `Mitarbeiter #${id}` : 'Unbekannter Mitarbeiter');
                }

                function materialRequestRows() {
                    const rows = [];
                    arr(state.payload?.items).forEach(item => {
                        materialRowsForItem(item)
                            .filter(row => materialIsRequest(row))
                            .forEach(row => {
                                const requestId = materialRequestId(row);
                                const plannerItemId = Number(row?.planner_item_id || itemPlannerId(item) || item?.id || 0);
                                rows.push({
                                    requestId,
                                    plannerItemId,
                                    item,
                                    row,
                                    status: materialRequestStatus(row),
                                    employeeId: materialRequestEmployeeId(row),
                                    employeeName: materialRequestEmployeeName(row),
                                });
                            });
                    });

                    return rows.sort((a, b) => {
                        const ao = a.status === 'open' ? 0 : (a.status === 'accepted' ? 1 : 2);
                        const bo = b.status === 'open' ? 0 : (b.status === 'accepted' ? 1 : 2);
                        if (ao !== bo) return ao - bo;
                        return String(a.employeeName).localeCompare(String(b.employeeName), 'de');
                    });
                }

                function materialRequestQtyLabel(row) {
                    const qty = row?.qty ?? row?.quantity ?? 1;
                    const unit = row?.unit || row?.measure || row?.measure_unit || 'Stk';
                    return `${qty} ${unit}`;
                }

                function materialRequestStatusChip(status) {
                    if (status === 'accepted') {
                        return `<span class="pmo-chip green">${icon('check-circle-2', 12)} Angenommen</span>`;
                    }

                    if (status === 'rejected') {
                        return `<span class="pmo-chip red">${icon('x-circle', 12)} Abgelehnt</span>`;
                    }

                    return `<span class="pmo-chip orange">${icon('clock', 12)} Offen</span>`;
                }

                function renderMaterialRequestsInbox() {
                    const wrap = document.getElementById('pmo-material-request-inbox');
                    const summary = document.getElementById('pmo-material-request-summary');
                    const tabCount = document.getElementById('pmo-request-tab-count');
                    if (!wrap) return;

                    const all = materialRequestRows();
                    const openCount = all.filter(r => r.status === 'open').length;
                    const acceptedCount = all.filter(r => r.status === 'accepted').length;
                    const rejectedCount = all.filter(r => r.status === 'rejected').length;

                    if (tabCount) {
                        tabCount.textContent = openCount;
                        tabCount.classList.toggle('pmo-hidden', openCount <= 0);
                    }

                    if (summary) {
                        summary.innerHTML = `
                                    <div class="pmo-summary-card"><span>Offen</span><strong>${esc(openCount)}</strong></div>
                                    <div class="pmo-summary-card"><span>Angenommen</span><strong>${esc(acceptedCount)}</strong></div>
                                    <div class="pmo-summary-card"><span>Abgelehnt</span><strong>${esc(rejectedCount)}</strong></div>
                                    <div class="pmo-summary-card"><span>Gesamt</span><strong>${esc(all.length)}</strong></div>
                                `;
                    }

                    const filter = String(state.materialRequestFilter || 'open');
                    const q = String(state.materialRequestSearch || '').toLowerCase().trim();

                    const filtered = all.filter(entry => {
                        if (filter !== 'all' && entry.status !== filter) return false;

                        if (!q) return true;

                        const row = entry.row || {};
                        const item = entry.item || {};
                        return [
                            entry.employeeName,
                            row.name,
                            row.title,
                            row.article_name,
                            row.article_no,
                            row.note,
                            row.description,
                            item.title,
                            item.description,
                            item.source_type,
                        ].map(v => String(v || '').toLowerCase()).some(v => v.includes(q));
                    });

                    if (!filtered.length) {
                        wrap.innerHTML = `<div class="pmo-empty">Keine Material-Anfragen für diesen Filter gefunden.</div>`;
                        redrawIcons();
                        return;
                    }

                    const groups = {};
                    filtered.forEach(entry => {
                        const key = `${entry.employeeId || 0}:${entry.employeeName || 'Unbekannt'}`;
                        if (!groups[key]) {
                            groups[key] = {
                                employeeId: entry.employeeId,
                                employeeName: entry.employeeName,
                                rows: [],
                            };
                        }
                        groups[key].rows.push(entry);
                    });

                    wrap.innerHTML = Object.values(groups).map(group => {
                        const groupOpen = group.rows.filter(r => r.status === 'open').length;
                        const initials = String(group.employeeName || 'MA')
                            .split(/\s+/)
                            .filter(Boolean)
                            .slice(0, 2)
                            .map(part => part.slice(0, 1).toUpperCase())
                            .join('') || 'MA';

                        return `<section class="pmo-request-employee-card">
                                    <div class="pmo-request-employee-head">
                                        <div class="pmo-request-employee-left">
                                            <div class="pmo-request-avatar">${esc(initials)}</div>
                                            <div>
                                                <div class="pmo-request-employee-name">${esc(group.employeeName)}</div>
                                                <div class="pmo-request-employee-meta">${esc(group.rows.length)} Anfrage(n) · ${esc(groupOpen)} offen</div>
                                            </div>
                                        </div>
                                        <div class="pmo-pill-row">
                                            <span class="pmo-chip ${groupOpen ? 'orange' : 'green'}">${icon(groupOpen ? 'bell-ring' : 'check-circle-2', 12)} ${groupOpen ? `${groupOpen} offen` : 'alles beantwortet'}</span>
                                        </div>
                                    </div>
                                    <div class="pmo-request-table-wrap">
                                        <table class="pmo-request-table">
                                            <thead>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Job / Aufgabe</th>
                                                    <th>Menge</th>
                                                    <th>Status</th>
                                                    <th>Datum</th>
                                                    <th>Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${group.rows.map(entry => {
                            const row = entry.row || {};
                            const item = entry.item || {};
                            const requestId = entry.requestId;
                            const plannerItemId = entry.plannerItemId;
                            const name = row.name || row.title || row.article_name || 'Material Anfrage';
                            const note = row.note || row.description || '';
                            const created = row.requested_at || row.created_at || '';
                            const statusChip = materialRequestStatusChip(entry.status);
                            const rowClass = entry.status === 'accepted' ? 'is-accepted' : (entry.status === 'rejected' ? 'is-rejected' : 'is-open');
                            const canAnswer = entry.status === 'open' && requestId && plannerItemId;

                            return `<tr class="pmo-request-row ${rowClass}">
                                                        <td>
                                                            <div class="pmo-request-material-title">${esc(name)}</div>
                                                            ${row.article_no ? `<div class="pmo-note">${esc(row.article_no)}</div>` : ''}
                                                            ${note ? `<div class="pmo-request-note">${esc(note)}</div>` : ''}
                                                        </td>
                                                        <td>
                                                            <strong>${esc(item.title || ('Job #' + plannerItemId))}</strong>
                                                            <div class="pmo-note">${esc(typeLabel(item.source_type || ''))} · Planner Item #${esc(plannerItemId || '—')}</div>
                                                        </td>
                                                        <td><strong>${esc(materialRequestQtyLabel(row))}</strong></td>
                                                        <td>${statusChip}</td>
                                                        <td>${esc(created ? shortDateTime(created) : '—')}</td>
                                                        <td>
                                                            <div class="pmo-request-actions">
                                                                ${canAnswer ? `
                                                                    <button type="button" class="pmo-btn-soft" data-pmo-material-request-action="accepted" data-item-id="${plannerItemId}" data-request-id="${requestId}">
                                                                        ${icon('check', 13)} Annehmen
                                                                    </button>
                                                                    <button type="button" class="pmo-btn-soft pmo-danger" data-pmo-material-request-action="rejected" data-item-id="${plannerItemId}" data-request-id="${requestId}">
                                                                        ${icon('x', 13)} Ablehnen
                                                                    </button>
                                                                ` : `<span class="pmo-chip gray">Keine Aktion</span>`}
                                                            </div>
                                                        </td>
                                                    </tr>`;
                        }).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                </section>`;
                    }).join('');

                    redrawIcons();
                }

                async function updateMaterialRequestStatus(plannerItemId, requestId, status) {
                    plannerItemId = Number(plannerItemId || 0);
                    requestId = Number(requestId || 0);
                    status = String(status || '').toLowerCase();

                    if (!plannerItemId || !requestId || !['accepted', 'rejected'].includes(status)) {
                        toast('Material-Anfrage', 'Die Anfrage konnte nicht eindeutig erkannt werden.', 'bad');
                        return;
                    }

                    let responseNote = '';

                    if (status === 'rejected') {
                        responseNote = prompt('Warum wird diese Material-Anfrage abgelehnt?') || '';
                    }

                    const url = itemEndpoint('itemMaterialRequestStatus', plannerItemId, { request: requestId });
                    if (!url) {
                        toast('Route fehlt', 'Endpoint itemMaterialRequestStatus fehlt in plannerConfig.endpoints.', 'bad');
                        return;
                    }

                    await requestJson(url, {
                        method: 'PATCH',
                        body: JSON.stringify({
                            status,
                            response_status: status,
                            response_note: responseNote,
                        }),
                    });

                    toast(
                        'Material-Anfrage aktualisiert',
                        status === 'accepted' ? 'Die Anfrage wurde angenommen.' : 'Die Anfrage wurde abgelehnt.'
                    );

                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    renderMaterialRequestsInbox();

                    if (activeKey) {
                        openJobDrawer(activeKey);
                    }
                }


                function itemEndpoint(key, itemId, replacements = {}) {
                    let url = endpoint(key, state.projectId, state.planId, itemId);
                    if (!url) return null;
                    Object.entries(replacements || {}).forEach(([k, v]) => {
                        url = url
                            .replaceAll(`___${String(k).toUpperCase()}___`, v || '')
                            .replaceAll(`__${String(k).toUpperCase()}__`, v || '');
                    });
                    return url;
                }

                async function storeItemStep(plannerItemId, box) {
                    const payload = {
                        title: box.querySelector('[data-pmo-step-title]')?.value || '',
                        description: box.querySelector('[data-pmo-step-description]')?.value || '',
                        due_date: box.querySelector('[data-pmo-step-date]')?.value || state.date,
                        due_time: box.querySelector('[data-pmo-step-time]')?.value || '',
                        origin_type: box.querySelector('[data-pmo-step-origin]')?.value || 'manual',
                        is_required: Number(box.querySelector('[data-pmo-step-required]')?.value || 1),
                    };

                    if (!payload.title.trim()) {
                        toast('Schritt fehlt', 'Bitte einen Titel für den Schritt eingeben.', 'bad');
                        return;
                    }

                    await requestJson(itemEndpoint('itemStepStore', plannerItemId), {
                        method: 'POST',
                        body: JSON.stringify(payload),
                    });

                    toast('Schritt gespeichert', 'Der Schritt wurde zur Karte hinzugefügt.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function updateItemStep(plannerItemId, stepId, payload) {
                    await requestJson(itemEndpoint('itemStepUpdate', plannerItemId, { step: stepId }), {
                        method: 'PATCH',
                        body: JSON.stringify(payload),
                    });

                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function destroyItemStep(plannerItemId, stepId) {
                    if (!confirm('Schritt wirklich löschen?')) return;

                    await requestJson(itemEndpoint('itemStepDestroy', plannerItemId, { step: stepId }), {
                        method: 'DELETE',
                    });

                    toast('Schritt gelöscht', 'Der Schritt wurde entfernt.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function updateItemMaterial(plannerItemId, materialId, payload) {
                    await requestJson(itemEndpoint('itemMaterialUpdate', plannerItemId, { material: materialId }), {
                        method: 'PATCH',
                        body: JSON.stringify(payload),
                    });

                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function destroyItemMaterial(plannerItemId, materialId) {
                    if (!confirm('Material wirklich löschen?')) return;

                    await requestJson(itemEndpoint('itemMaterialDestroy', plannerItemId, { material: materialId }), {
                        method: 'DELETE',
                    });

                    toast('Material gelöscht', 'Das Material wurde entfernt.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function importDealMaterials(plannerItemId) {
                    await requestJson(itemEndpoint('itemMaterialImportDeal', plannerItemId), {
                        method: 'POST',
                        body: JSON.stringify({ origin_type: 'deal_final' }),
                    });

                    toast('Material importiert', 'Auftrag/Deal Material wurde für diesen Job übernommen.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                function normalizeMaterialSourcePayload(json) {
                    const data = json?.data || json || {};
                    return {
                        deal: arr(data.deal || data.deal_materials || data.auftrag || data.auftrag_materials),
                        master_sets: arr(data.master_sets || data.masterSet || data.master_sets_materials),
                        master_groups: arr(data.master_groups || data.master_set_groups || data.group_sets || data.groups),
                        products: arr(data.products || data.product_results),
                        current: arr(data.current || data.current_materials),
                        has_deal: !!(data.has_deal || data.has_auftrag || arr(data.deal || data.auftrag).length),
                        meta: data.meta || {},
                    };
                }

                function sourceMaterialPayload(row, originType = 'manual') {
                    const productId = row.product_id || row.productId || row.id || null;
                    return {
                        product_id: productId,
                        source_id: row.source_id || row.component_id || row.id || null,
                        source_type: row.source_type || row.item_type || (originType.includes('master') ? 'master_set_component' : (originType.includes('deal') || originType.includes('auftrag') ? 'offer_detail' : 'product')),
                        origin_type: row.origin_type || originType,
                        name: row.name || row.product || row.title || 'Material',
                        article_no: row.article_no || row.distributor_article_no || row.sku || '',
                        qty: row.qty ?? row.quantity ?? 1,
                        unit: row.unit || row.measure || row.measure_unit || 'Stk',
                        measure: row.measure || row.unit || 'Stk',
                        unit_price: row.unit_price ?? row.price ?? row.vk ?? 0,
                        purchase_price: row.purchase_price ?? row.ek ?? 0,
                        distributor_id: row.distributor_id || null,
                        distributor_price_id: row.distributor_price_id || null,
                        distributor_name: row.distributor_name || row.supplier || '',
                        image_url: materialImageUrl(row),
                        active: true,
                    };
                }

                function openMaterialModal(plannerItemId) {
                    const item = allWorkItems().find(row => Number(itemPlannerId(row)) === Number(plannerItemId));
                    if (!item) {
                        toast('Material', 'Die Karte wurde nicht gefunden.', 'bad');
                        return;
                    }

                    state.materialModal = {
                        mode: 'single',
                        plannerItemId: Number(plannerItemId),
                        itemIds: [Number(plannerItemId)],
                        employeeId: null,
                        groupName: '',
                        tab: 'deal',
                        sources: null,
                        search: '',
                        localSearch: '',
                        detailPayloads: {},
                        loading: true,
                        itemTitle: item.title || 'Arbeit',
                    };

                    renderMaterialModal();
                    openModal('pmo-material-modal');
                    loadMaterialSources(plannerItemId).catch(error => {
                        state.materialModal.loading = false;
                        state.materialModal.error = error.message;
                        renderMaterialModal();
                    });
                }

                function openGroupMaterialModal(employeeId) {
                    const emp = employeeById(employeeId) || {};
                    const items = sortItemsBySchedule(employeeItems(employeeId))
                        .filter(item => Number(itemPlannerId(item) || 0) > 0);

                    if (!items.length) {
                        toast('Gruppenmaterial', 'Für diesen Mitarbeiter gibt es in diesem Zeitraum keine Jobs mit Planner-ID.', 'bad');
                        return;
                    }

                    const firstItemId = Number(itemPlannerId(items[0]));
                    const empName = emp.full_name || emp.name || ('Mitarbeiter #' + employeeId);

                    state.materialModal = {
                        mode: 'group',
                        plannerItemId: firstItemId,
                        itemIds: items.map(item => Number(itemPlannerId(item))).filter(Boolean),
                        employeeId: Number(employeeId),
                        groupName: `Gruppenmaterial · ${empName} · ${state.payload?.summary?.period_label || state.date}`,
                        tab: 'products',
                        sources: null,
                        search: '',
                        localSearch: '',
                        detailPayloads: {},
                        loading: true,
                        itemTitle: `${empName} · ${items.length} Jobs`,
                    };

                    renderMaterialModal();
                    openModal('pmo-material-modal');
                    loadMaterialSources(firstItemId).catch(error => {
                        state.materialModal.loading = false;
                        state.materialModal.error = error.message;
                        renderMaterialModal();
                    });
                }

                async function loadMaterialSources(plannerItemId) {
                    state.materialModal.loading = true;
                    renderMaterialModal();

                    const url = itemEndpoint('itemMaterialSources', plannerItemId);
                    if (!url) {
                        state.materialModal.sources = normalizeMaterialSourcePayload({
                            data: { current: materialRowsForItem(allWorkItems().find(row => Number(itemPlannerId(row)) === Number(plannerItemId)) || {}) }
                        });
                        state.materialModal.loading = false;
                        state.materialModal.error = 'Endpoint itemMaterialSources fehlt in plannerConfig.endpoints.';
                        renderMaterialModal();
                        return;
                    }

                    const json = await requestJson(url, { method: 'GET' });
                    state.materialModal.sources = normalizeMaterialSourcePayload(json);

                    const s = state.materialModal.sources;
                    if (!s.deal.length && s.master_sets.length) {
                        state.materialModal.tab = 'master';
                    } else if (!s.deal.length && !s.master_sets.length && s.master_groups.length) {
                        state.materialModal.tab = 'groups';
                    } else if (!s.deal.length && !s.master_sets.length && !s.master_groups.length && s.products.length) {
                        state.materialModal.tab = 'products';
                    } else if (s.deal.length) {
                        state.materialModal.tab = 'deal';
                    }

                    state.materialModal.loading = false;
                    renderMaterialModal();
                }

                async function searchMaterialProducts() {
                    const mm = state.materialModal || {};
                    const q = document.getElementById('pmo-material-search')?.value || '';
                    mm.search = q;

                    const base = itemEndpoint('itemMaterialProducts', mm.plannerItemId);
                    if (!base) {
                        toast('Endpoint fehlt', 'itemMaterialProducts fehlt in plannerConfig.endpoints.', 'bad');
                        return;
                    }

                    const url = base + (base.includes('?') ? '&' : '?') + new URLSearchParams({ q }).toString();
                    const json = await requestJson(url, { method: 'GET' });
                    const sources = normalizeMaterialSourcePayload(json);
                    state.materialModal.sources = {
                        ...(state.materialModal.sources || normalizeMaterialSourcePayload({})),
                        products: sources.products,
                    };
                    state.materialModal.tab = 'products';
                    renderMaterialModal();
                }

                async function addMaterialToGroup(row, originType) {
                    const mm = state.materialModal || {};
                    const itemIds = arr(mm.itemIds).map(Number).filter(Boolean);

                    if (!itemIds.length) {
                        toast('Gruppenmaterial', 'Keine Jobs für das Gruppenmaterial gefunden.', 'bad');
                        return;
                    }

                    const url = endpoint('planGroupMaterialStore');
                    if (!url) {
                        toast('Endpoint fehlt', 'planGroupMaterialStore fehlt in plannerConfig.endpoints.', 'bad');
                        return;
                    }

                    const payload = {
                        ...sourceMaterialPayload(row, originType),
                        item_ids: itemIds,
                        employee_id: mm.employeeId || null,
                        group_name: mm.groupName || 'Gruppenmaterial',
                        scope_date_from: state.payload?.summary?.from || state.date || null,
                        scope_date_to: state.payload?.summary?.to || state.date || null,
                        scope_mode: state.mode || 'day',
                        period_label: state.payload?.summary?.period_label || state.date || '',
                        origin_type: originType || row.origin_type || 'group_material',
                    };

                    await requestJson(url, {
                        method: 'POST',
                        body: JSON.stringify(payload),
                    });

                    toast('Gruppenmaterial gespeichert', `Das Material wurde einmalig gespeichert und mit ${itemIds.length} Jobs verknüpft.`);
                    await loadPayload(false);
                    if (mm.plannerItemId) await loadMaterialSources(mm.plannerItemId);
                    if (state.activeWorkKey) openJobDrawer(state.activeWorkKey);
                }

                async function addMaterialToItem(plannerItemId, row, originType) {
                    if (state.materialModal?.mode === 'group') {
                        await addMaterialToGroup(row, originType || row.origin_type || 'group_material');
                        return;
                    }

                    await requestJson(itemEndpoint('itemMaterialStore', plannerItemId), {
                        method: 'POST',
                        body: JSON.stringify(sourceMaterialPayload(row, originType)),
                    });

                    toast('Material hinzugefügt', 'Das Material wurde diesem Job hinzugefügt.');
                    await loadPayload(false);
                    await loadMaterialSources(plannerItemId);
                    if (state.activeWorkKey) openJobDrawer(state.activeWorkKey);
                }



                function materialRowSearchText(row) {
                    return [
                        row.name,
                        row.product,
                        row.title,
                        row.description,
                        row.master_set_name,
                        row.article_no,
                        row.distributor_article_no,
                        row.sku,
                        row.distributor_name,
                        row.group_detail?.name,
                        row.group_detail?.description,
                        row.master_set_detail?.name,
                        row.master_set_detail?.description,
                    ].filter(Boolean).join(' ').toLowerCase();
                }

                function filterMaterialRows(rows, query) {
                    const q = String(query || '').trim().toLowerCase();
                    if (!q) return arr(rows);
                    return arr(rows).filter(row => materialRowSearchText(row).includes(q));
                }

                function registerMasterDetail(row, fallbackType = 'master_set') {
                    let detail = row.group_detail || row.master_set_detail || row.detail || null;

                    // Fallback: still open a details modal even when the backend row has no nested detail payload.
                    // This prevents the Details button from doing nothing and makes missing backend data visible.
                    if (!detail && (row.master_set_id || row.source_type === 'master_set')) {
                        detail = {
                            type: 'master_set',
                            id: Number(row.master_set_id || row.source_id || 0),
                            name: row.master_set_name || row.name || row.title || 'MasterSet',
                            description: row.description || '',
                            components: [],
                            labor: [],
                            tasks: [],
                            summary: { components: 0, labor: 0, tasks: 0 },
                        };
                    }

                    if (!detail && (row.group_id || row.source_type === 'master_set_group')) {
                        detail = {
                            type: 'master_set_group',
                            id: Number(row.group_id || row.source_id || 0),
                            name: row.name || row.title || 'Gruppen Set',
                            description: row.description || '',
                            master_sets: [],
                            summary: { master_sets: 0, components: 0, labor: 0, tasks: 0 },
                        };
                    }

                    if (!detail) return '';

                    const mm = state.materialModal || {};
                    mm.detailPayloads = mm.detailPayloads || {};
                    state.materialModal = mm;

                    const key = `${detail.type || fallbackType}-${detail.id || row.source_id || Math.random().toString(36).slice(2)}`;
                    mm.detailPayloads[key] = detail;
                    return key;
                }

                function renderMasterGroupCard(row) {
                    const detailKey = registerMasterDetail(row, 'master_set_group');
                    const color = row.color || row.group_detail?.color || '#93c21c';
                    const name = row.name || row.title || 'Gruppen Set';
                    const description = row.description || row.group_detail?.description || '';

                    return `<div class="pmo-master-group-card">
                                                                                <div class="pmo-inline" style="justify-content:space-between;align-items:flex-start;">
                                                                                    <div style="min-width:0;">
                                                                                        <div class="pmo-work-title"><span class="pmo-master-group-color" style="background:${esc(color)}"></span> ${esc(name)}</div>
                                                                                        <div class="pmo-note">${esc(description || 'MasterSet Gruppe mit mehreren Sets.')}</div>
                                                                                    </div>
                                                                                    ${detailKey ? `<button type="button" class="pmo-btn-soft" data-pmo-master-detail="${esc(detailKey)}">${icon('eye', 14)} Details</button>` : ''}
                                                                                </div>
                                                                                <div class="pmo-job-meta">
                                                                                    <span class="pmo-chip lime">Gruppen Set</span>
                                                                                    <span class="pmo-chip gray">Sets: ${esc(row.master_set_count || row.group_detail?.summary?.master_sets || 0)}</span>
                                                                                    <span class="pmo-chip gray">Komponenten: ${esc(row.component_count || row.group_detail?.summary?.components || 0)}</span>
                                                                                    <span class="pmo-chip gray">Arbeit: ${esc(row.labor_count || row.group_detail?.summary?.labor || 0)}</span>
                                                                                    <span class="pmo-chip gray">Schritte: ${esc(row.task_count || row.group_detail?.summary?.tasks || 0)}</span>
                                                                                </div>
                                                                            </div>`;
                }

                function materialLocalSearchBox(placeholder = 'In dieser Liste suchen...') {
                    const mm = state.materialModal || {};
                    return `<div class="pmo-material-search-row">
                                                                                <input type="text" id="pmo-material-local-search" class="pmo-input" placeholder="${esc(placeholder)}" value="${esc(mm.localSearch || '')}">
                                                                                <button type="button" class="pmo-btn-soft" id="pmo-material-clear-search">${icon('x', 14)} Leeren</button>
                                                                            </div>`;
                }

                function renderMaterialCard(row, originType) {
                    const payload = esc(JSON.stringify(sourceMaterialPayload(row, originType)));
                    const name = row.name || row.product || row.title || 'Material';
                    const article = row.article_no || row.distributor_article_no || row.sku || '';
                    const unit = row.unit || row.measure || row.measure_unit || 'Stk';
                    const price = row.unit_price ?? row.price ?? row.vk ?? 0;
                    const purchase = row.purchase_price ?? row.ek ?? 0;
                    const detailKey = registerMasterDetail(row, originType);

                    return `<div class="pmo-material-source-card">
                                                                                <img src="${esc(materialImageUrl(row))}" alt="${esc(name)}">
                                                                                <div class="pmo-material-source-body">
                                                                                    <div class="pmo-work-title">${esc(name)}</div>
                                                                                    <div class="pmo-note">${esc(row.master_set_name || '')}${row.master_set_name ? ' · ' : ''}${esc(article)} ${article ? '·' : ''} ${esc(row.distributor_name || row.supplier || '')}</div>
                                                                                    <div class="pmo-job-meta">
                                                                                        <span class="pmo-chip ${originChipClass(originType)}">${esc(originLabel(originType))}</span>
                                                                                        <span class="pmo-chip gray">Einheit: ${esc(unit)}</span>
                                                                                        <span class="pmo-chip gray">EK: ${esc(formatMoney(purchase))}</span>
                                                                                        <span class="pmo-chip gray">VK: ${esc(formatMoney(price))}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="pmo-material-source-actions">
                                                                                    ${detailKey ? `<button type="button" class="pmo-btn-soft" data-pmo-master-detail="${esc(detailKey)}">${icon('eye', 14)} Details</button>` : ''}
                                                                                    <input type="number" class="pmo-input" min="0" step="0.01" value="${esc(row.qty ?? row.quantity ?? 1)}" data-material-qty>
                                                                                    <button type="button" class="pmo-btn" data-pmo-material-add="${payload}">${icon('plus', 14)} ${state.materialModal?.mode === 'group' ? 'Einmalig verknüpfen' : 'Hinzufügen'}</button>
                                                                                </div>
                                                                            </div>`;
                }

                function renderMaterialModal() {
                    const mm = state.materialModal || {};
                    const body = document.getElementById('pmo-material-modal-body');
                    const title = document.getElementById('pmo-material-modal-title');
                    const sub = document.getElementById('pmo-material-modal-sub');
                    if (!body) return;

                    const isGroupMaterial = mm.mode === 'group';
                    if (title) title.innerHTML = `${icon('package-plus', 18)} ${isGroupMaterial ? 'Gruppenmaterial für mehrere Jobs' : 'Material für Job'}`;
                    if (sub) sub.textContent = isGroupMaterial
                        ? `${mm.itemTitle || 'Mitarbeiter'} · ${arr(mm.itemIds).length} Jobs · Material wird auf alle diese Jobs kopiert.`
                        : (mm.itemTitle || 'Materialliste laden...');

                    const sources = mm.sources || normalizeMaterialSourcePayload({});
                    const tabs = [
                        ['deal', 'Auftrag / Deal', sources.deal.length],
                        ['master', 'Master Set', sources.master_sets.length],
                        ['groups', 'Gruppen Set', sources.master_groups.length],
                        ['products', 'Produktkatalog', sources.products.length],
                        ['manual', 'Manuell / Anfrage', 0],
                    ];

                    if (mm.loading) {
                        body.innerHTML = `<div class="pmo-empty">Materialquellen werden geladen...</div>`;
                        redrawIcons();
                        return;
                    }

                    const activeTab = mm.tab || 'deal';
                    let content = '';

                    if (activeTab === 'deal') {
                        const rows = filterMaterialRows(sources.deal, mm.localSearch || '');
                        content = materialLocalSearchBox('Auftrag/Deal Material suchen...') + (rows.length
                            ? rows.map(row => renderMaterialCard(row, row.origin_type || 'deal_final')).join('')
                            : `<div class="pmo-empty">Kein finaler Auftrag/Deal Material gefunden. Du kannst Produktkatalog oder Manuell nutzen.</div>`);
                    }

                    if (activeTab === 'master') {
                        const rows = filterMaterialRows(sources.master_sets, mm.localSearch || '');
                        content = materialLocalSearchBox('MasterSet, Hauptkomponente, Unterkomponente suchen...') + (rows.length
                            ? rows.map(row => renderMaterialCard(row, row.origin_type || 'master_set_predefined')).join('')
                            : `<div class="pmo-empty">Keine MasterSet-Materialien für diese Karte gefunden.</div>`);
                    }

                    if (activeTab === 'groups') {
                        const rows = filterMaterialRows(sources.master_groups, mm.localSearch || '');
                        content = materialLocalSearchBox('Gruppen Set oder enthaltenes MasterSet suchen...') + (rows.length
                            ? rows.map(row => renderMasterGroupCard(row)).join('')
                            : `<div class="pmo-empty">Keine MasterSet-Gruppen gefunden.</div>`);
                    }

                    if (activeTab === 'products') {
                        content = `<div class="pmo-material-search-row">
                                                                                        <input type="text" id="pmo-material-search" class="pmo-input" placeholder="Produkt, Artikelnummer, SKU suchen..." value="${esc(mm.search || '')}">
                                                                                        <button type="button" class="pmo-btn" id="pmo-material-search-btn">${icon('search', 14)} Suchen</button>
                                                                                    </div>
                                                                                    ${sources.products.length ? sources.products.map(row => renderMaterialCard(row, 'product_library')).join('') : `<div class="pmo-empty">Keine Produkte gefunden. Nutze die Suche oder prüfe die Produkt-Tabelle.</div>`}`;
                    }

                    if (activeTab === 'manual') {
                        content = `<div class="pmo-material-manual-box">
                                                                                    <div class="pmo-modal-grid">
                                                                                        <div class="pmo-field pmo-span-2">
                                                                                            <label class="pmo-label">Materialname</label>
                                                                                            <input type="text" class="pmo-input" id="pmo-manual-material-name" placeholder="z.B. Kabel, Schraube, Modul...">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Menge</label>
                                                                                            <input type="number" class="pmo-input" id="pmo-manual-material-qty" value="1" min="0" step="0.01">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Einheit</label>
                                                                                            <input type="text" class="pmo-input" id="pmo-manual-material-unit" value="Stk">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">EK</label>
                                                                                            <input type="number" class="pmo-input" id="pmo-manual-material-purchase" value="0" min="0" step="0.01">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">VK</label>
                                                                                            <input type="number" class="pmo-input" id="pmo-manual-material-price" value="0" min="0" step="0.01">
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Quelle</label>
                                                                                            <select class="pmo-select" id="pmo-manual-material-origin">
                                                                                                <option value="manual">Manuell</option>
                                                                                                <option value="employee_request">Mitarbeiter Anfrage</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="pmo-field">
                                                                                            <label class="pmo-label">Artikelnummer</label>
                                                                                            <input type="text" class="pmo-input" id="pmo-manual-material-article">
                                                                                        </div>
                                                                                    </div>
                                                                                    ${state.materialModal?.mode === 'group' ? `<div class="pmo-field" style="margin-top:12px;"><label class="pmo-label">Gruppenname</label><input type="text" class="pmo-input" id="pmo-manual-material-group-name" value="${esc(state.materialModal?.groupName || 'Gruppenmaterial')}"></div>` : ''}
                                                                                    <div class="pmo-step-actions">
                                                                                        <button type="button" class="pmo-btn" id="pmo-manual-material-save">${icon('save', 14)} ${state.materialModal?.mode === 'group' ? 'Einmalig verknüpfen' : 'Material speichern'}</button>
                                                                                    </div>
                                                                                </div>`;
                    }

                    body.innerHTML = `
                                                                                ${mm.error ? `<div class="pmo-empty" style="margin-bottom:12px;color:#b91c1c;">${esc(mm.error)}</div>` : ''}
                                                                                ${mm.mode === 'group' ? `<div class="pmo-empty" style="margin-bottom:12px;text-align:left;padding:12px 14px;">${icon('users', 14)} <strong>Gruppenmaterial:</strong> Dieses Material wird <u>einmalig</u> für diesen Mitarbeiter und Zeitraum gespeichert und nur mit ${esc(arr(mm.itemIds).length)} Jobs verknüpft. Es wird nicht pro Aufgabe dupliziert.</div>` : ''}
                                                                                <div class="pmo-material-tabs">
                                                                                    ${tabs.map(([key, label, count]) => `<button type="button" class="${activeTab === key ? 'is-active' : ''}" data-pmo-material-tab="${key}">${esc(label)} <span>${count}</span></button>`).join('')}
                                                                                </div>
                                                                                <div class="pmo-material-source-list">${content}</div>`;

                    redrawIcons();
                }



                function detailSearchMatch(row, query) {
                    const q = String(query || '').trim().toLowerCase();
                    if (!q) return true;
                    return [
                        row.name,
                        row.title,
                        row.description,
                        row.article_no,
                        row.distributor_article_no,
                        row.sku,
                        row.distributor_name,
                        row.qualification_name,
                        row.department_name,
                        row.position_name,
                        row.employee_name,
                        row.phase_name,
                        row.stage_name,
                    ].filter(Boolean).join(' ').toLowerCase().includes(q);
                }

                function renderComponentTreeRows(components, query = '', depth = 0) {
                    return arr(components).map(component => {
                        const childHtml = renderComponentTreeRows(component.children || [], query, depth + 1);
                        const visible = detailSearchMatch(component, query) || childHtml.trim() !== '';
                        if (!visible) return '';
                        const indent = depth > 0 ? ' is-child' : '';
                        const title = component.name || component.product || 'Komponente';
                        return `<div class="pmo-master-component-row${indent}">
                                                                                    <img src="${esc(materialImageUrl(component))}" alt="${esc(title)}">
                                                                                    <div style="min-width:0;">
                                                                                        <div class="pmo-work-title">${depth > 0 ? '↳ ' : ''}${esc(title)}</div>
                                                                                        <div class="pmo-note">${esc(component.article_no || component.distributor_article_no || component.sku || 'Ohne Artikelnummer')} ${component.distributor_name ? '· ' + esc(component.distributor_name) : ''}</div>
                                                                                        <div class="pmo-job-meta">
                                                                                            <span class="pmo-chip ${depth > 0 ? 'gray' : 'lime'}">${depth > 0 ? 'Unterkomponente' : 'Hauptkomponente'}</span>
                                                                                            <span class="pmo-chip gray">${esc(component.qty ?? 1)} ${esc(component.unit || component.measure || 'Stk')}</span>
                                                                                            <span class="pmo-chip gray">EK ${esc(formatMoney(component.purchase_price || 0))}</span>
                                                                                            <span class="pmo-chip gray">VK ${esc(formatMoney(component.unit_price || 0))}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="pmo-value">${esc(formatMoney(component.total_price || ((component.qty || 1) * (component.unit_price || 0))))}</div>
                                                                                </div>${childHtml}`;
                    }).join('');
                }

                function renderMasterSetDetailBlock(detail, query = '') {
                    const components = renderComponentTreeRows(detail.components_tree || detail.components || [], query);
                    const laborRows = arr(detail.labor).filter(row => detailSearchMatch(row, query));
                    const taskRows = arr(detail.tasks).filter(row => detailSearchMatch(row, query));

                    return `<div class="pmo-master-detail-section">
                                                                                <div class="pmo-inline" style="justify-content:space-between;margin-bottom:10px;">
                                                                                    <div>
                                                                                        <div class="pmo-work-title">${esc(detail.name || 'MasterSet')}</div>
                                                                                        <div class="pmo-note">${esc(detail.description || detail.article_group_name || '')}</div>
                                                                                    </div>
                                                                                    <div class="pmo-job-meta">
                                                                                        <span class="pmo-chip lime">Haupt: ${esc(detail.summary?.main_components || 0)}</span>
                                                                                        <span class="pmo-chip gray">Unter: ${esc(detail.summary?.sub_components || 0)}</span>
                                                                                        <span class="pmo-chip orange">Arbeit: ${esc(detail.summary?.labor || 0)}</span>
                                                                                        <span class="pmo-chip blue">Schritte: ${esc(detail.summary?.tasks || 0)}</span>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="pmo-detail-title">${icon('boxes', 14)} Haupt- & Unterkomponenten</div>
                                                                                ${components || `<div class="pmo-empty" style="padding:14px;margin-bottom:10px;">Keine Komponenten gefunden.</div>`}

                                                                                <div class="pmo-master-detail-grid" style="margin-top:12px;">
                                                                                    <div>
                                                                                        <div class="pmo-detail-title">${icon('hard-hat', 14)} Labor / Personal</div>
                                                                                        ${laborRows.length ? `<table class="pmo-master-detail-table"><thead><tr><th>Qualifikation</th><th>Position</th><th>Std.</th><th>Satz</th></tr></thead><tbody>${laborRows.map(row => `<tr><td>${esc(row.qualification_name || row.employee_name || 'Arbeit')}</td><td>${esc(row.position_name || row.department_name || '—')}</td><td>${esc(row.hours || 0)}</td><td>${esc(formatMoney(row.hourly_rate || 0))}</td></tr>`).join('')}</tbody></table>` : `<div class="pmo-empty" style="padding:14px;">Keine Laborzeilen gefunden.</div>`}
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="pmo-detail-title">${icon('list-checks', 14)} MasterSet Schritte</div>
                                                                                        ${taskRows.length ? `<table class="pmo-master-detail-table"><thead><tr><th>Titel</th><th>Phase</th><th>Dauer</th></tr></thead><tbody>${taskRows.map(row => `<tr><td><strong>${esc(row.title || 'Schritt')}</strong><div class="pmo-note">${esc(row.description || '')}</div></td><td>${esc(row.phase_name || row.stage_name || '—')}</td><td>${esc(row.duration || row.hours || '—')} ${esc(row.duration_type || '')}</td></tr>`).join('')}</tbody></table>` : `<div class="pmo-empty" style="padding:14px;">Keine MasterSet-Schritte gefunden.</div>`}
                                                                                    </div>
                                                                                </div>
                                                                            </div>`;
                }

                function renderMasterDetailModal() {
                    const modal = state.masterDetailModal || {};
                    const detail = modal.detail || null;
                    const body = document.getElementById('pmo-master-detail-body');
                    const title = document.getElementById('pmo-master-detail-title');
                    const sub = document.getElementById('pmo-master-detail-sub');
                    if (!body) return;

                    if (!detail) {
                        body.innerHTML = `<div class="pmo-empty">Keine Details gefunden.</div>`;
                        redrawIcons();
                        return;
                    }

                    const q = modal.search || '';
                    if (title) title.innerHTML = `${icon(detail.type === 'master_set_group' ? 'folder-tree' : 'boxes', 18)} ${esc(detail.name || 'Details')}`;
                    if (sub) sub.textContent = detail.type === 'master_set_group' ? 'Gruppen Set mit enthaltenen MasterSets.' : 'MasterSet Komponenten, Unterkomponenten, Labor und Schritte.';

                    const summary = detail.summary || {};
                    let content = '';
                    if (detail.type === 'master_set_group') {
                        const sets = arr(detail.master_sets).filter(set => {
                            const ownMatch = detailSearchMatch(set, q);
                            const componentMatch = arr(set.components).some(row => detailSearchMatch(row, q));
                            const laborMatch = arr(set.labor).some(row => detailSearchMatch(row, q));
                            const taskMatch = arr(set.tasks).some(row => detailSearchMatch(row, q));
                            return !q || ownMatch || componentMatch || laborMatch || taskMatch;
                        });
                        content = sets.length ? sets.map(set => renderMasterSetDetailBlock(set, q)).join('') : `<div class="pmo-empty">Keine passenden MasterSets in dieser Gruppe gefunden.</div>`;
                    } else {
                        content = renderMasterSetDetailBlock(detail, q);
                    }

                    body.innerHTML = `<div class="pmo-master-detail-toolbar">
                                                                                <div class="pmo-job-meta">
                                                                                    ${detail.type === 'master_set_group' ? `<span class="pmo-chip lime">Sets: ${esc(summary.master_sets || arr(detail.master_sets).length)}</span>` : ''}
                                                                                    <span class="pmo-chip gray">Komponenten: ${esc(summary.components || 0)}</span>
                                                                                    <span class="pmo-chip orange">Arbeit: ${esc(summary.labor || 0)}</span>
                                                                                    <span class="pmo-chip blue">Schritte: ${esc(summary.tasks || 0)}</span>
                                                                                </div>
                                                                                <input type="text" class="pmo-input" id="pmo-master-detail-search" style="max-width:360px;" placeholder="Komponenten, Unterkomponenten, Labor suchen..." value="${esc(q)}">
                                                                            </div>
                                                                            ${content}`;

                    redrawIcons();
                }

                function openMasterDetailModal(key) {
                    const detail = state.materialModal?.detailPayloads?.[key] || null;
                    if (!detail) {
                        toast('Details fehlen', 'Für diesen Eintrag wurden keine MasterSet-Details geladen.', 'bad');
                        return;
                    }

                    state.masterDetailModal = { detail, search: '' };
                    renderMasterDetailModal();
                    openModal('pmo-master-detail-modal');
                }

                async function saveManualMaterial() {
                    const mm = state.materialModal || {};
                    const name = document.getElementById('pmo-manual-material-name')?.value || '';
                    if (!name.trim()) {
                        toast('Material fehlt', 'Bitte einen Materialnamen eingeben.', 'bad');
                        return;
                    }

                    const payload = {
                        name,
                        qty: Number(document.getElementById('pmo-manual-material-qty')?.value || 1),
                        unit: document.getElementById('pmo-manual-material-unit')?.value || 'Stk',
                        purchase_price: Number(document.getElementById('pmo-manual-material-purchase')?.value || 0),
                        unit_price: Number(document.getElementById('pmo-manual-material-price')?.value || 0),
                        article_no: document.getElementById('pmo-manual-material-article')?.value || '',
                        origin_type: document.getElementById('pmo-manual-material-origin')?.value || (mm.mode === 'group' ? 'group_material' : 'manual'),
                        source_type: mm.mode === 'group' ? 'group_manual' : 'manual',
                        active: true,
                    };

                    if (mm.mode === 'group') {
                        payload.group_name = document.getElementById('pmo-manual-material-group-name')?.value || mm.groupName || 'Gruppenmaterial';
                        await addMaterialToGroup(payload, payload.origin_type || 'group_material');
                        return;
                    }

                    await requestJson(itemEndpoint('itemMaterialStore', mm.plannerItemId), {
                        method: 'POST',
                        body: JSON.stringify(payload),
                    });

                    toast('Material gespeichert', 'Das Material wurde hinzugefügt.');
                    await loadPayload(false);
                    await loadMaterialSources(mm.plannerItemId);
                    if (state.activeWorkKey) openJobDrawer(state.activeWorkKey);
                }

                function dependencyParentIdsForItem(item) {
                    const plannerItemId = itemPlannerId(item);
                    if (!plannerItemId) return [];
                    return dependencyEdges()
                        .filter(edge => Number(edge.to) === Number(plannerItemId))
                        .map(edge => Number(edge.from))
                        .filter(Boolean);
                }

                function dependencyOptionsForItem(item) {
                    const currentId = itemPlannerId(item);
                    const selectedIds = new Set(dependencyParentIdsForItem(item));
                    return allWorkItems()
                        .filter(row => itemPlannerId(row) && Number(itemPlannerId(row)) !== Number(currentId))
                        .sort((a, b) => String(a.title || '').localeCompare(String(b.title || ''), 'de'))
                        .map(row => {
                            const id = itemPlannerId(row);
                            const label = `${row.title || 'Arbeit'} · ${typeLabel(row.source_type)} · ${moneyDate(row.planned_date || row.date || '')}`;
                            return `<option value="${id}" ${selectedIds.has(Number(id)) ? 'selected' : ''}>${esc(label)}</option>`;
                        })
                        .join('');
                }

                function renderDependenciesForItem(item) {
                    const plannerItemId = Number(item.planner_item_id || 0);
                    const edges = dependencyEdges();
                    const parents = edges.filter(edge => Number(edge.to) === plannerItemId);
                    const children = edges.filter(edge => Number(edge.from) === plannerItemId);

                    if (!plannerItemId) {
                        return `<div class="pmo-empty" style="margin:0;padding:18px;">Dieser Eintrag hat noch keine PlannerItem-ID und kann nicht verknüpft werden.</div>`;
                    }

                    const parentRows = parents.map(edge => `<div class="pmo-dependency-row"><span>${icon('arrow-left', 14)} Vorher: ${esc(edge.from_title || ('#' + edge.from))}</span><span class="pmo-chip gray">${esc(edge.gap_label || '—')}</span></div>`).join('');
                    const childRows = children.map(edge => `<div class="pmo-dependency-row"><span>${icon('arrow-right', 14)} Danach: ${esc(edge.to_title || ('#' + edge.to))}</span><span class="pmo-chip gray">${esc(edge.gap_label || '—')}</span></div>`).join('');
                    const listHtml = (parentRows || childRows)
                        ? `<div class="pmo-dependency-box">${parentRows}${childRows}</div>`
                        : `<div class="pmo-empty" style="margin:0;padding:14px;">Noch keine Abhängigkeiten vorhanden.</div>`;

                    return `<div class="pmo-dependency-editor">
                                                                                                                <label class="pmo-label" for="pmo-dependency-select">Diese Aufgabe hängt ab von</label>
                                                                                                                <select id="pmo-dependency-select" class="pmo-select pmo-dependency-select" multiple data-current-planner-id="${plannerItemId}">
                                                                                                                    ${dependencyOptionsForItem(item)}
                                                                                                                </select>
                                                                                                                <div class="pmo-dependency-editor-actions">
                                                                                                                    <button type="button" class="pmo-btn" id="pmo-save-drawer-dependencies">${icon('save', 14)} Abhängigkeiten speichern</button>
                                                                                                                    <span class="pmo-chip gray">Mehrfachauswahl möglich</span>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            ${listHtml}`;
                }

                function activateDependencySelect2() {
                    const select = document.getElementById('pmo-dependency-select');
                    if (!select) return;

                    const drawer = document.getElementById('pmo-job-drawer');
                    const $ = window.jQuery;

                    /*
                     * IMPORTANT:
                     * Do NOT use dropdownCssClass or selectionCssClass here.
                     * Your current Select2 build can throw:
                     * "No select2/compat/dropdownCss"
                     * Styling is handled only by CSS below.
                     */
                    if (!$ || !$.fn || !$.fn.select2) {
                        select.classList.add('pmo-select2-fallback');
                        select.style.display = 'block';
                        return;
                    }

                    const $select = $(select);

                    try {
                        if ($select.data('select2') || $select.hasClass('select2-hidden-accessible')) {
                            $select.select2('destroy');
                        }

                        $select.select2({
                            width: '100%',
                            placeholder: 'Vorgänger-Aufgaben auswählen...',
                            closeOnSelect: false,
                            dropdownParent: drawer ? $(drawer) : $(document.body),
                            language: {
                                noResults: function () {
                                    return 'Keine Aufgabe gefunden';
                                },
                                searching: function () {
                                    return 'Suche…';
                                }
                            }
                        });

                        select.classList.remove('pmo-select2-fallback');
                    } catch (error) {
                        console.warn('Select2 konnte nicht initialisiert werden. Fallback wird verwendet.', error);
                        try {
                            if ($select.data('select2') || $select.hasClass('select2-hidden-accessible')) {
                                $select.select2('destroy');
                            }
                        } catch (destroyError) {
                            console.warn('Select2 destroy fallback failed.', destroyError);
                        }

                        select.classList.add('pmo-select2-fallback');
                        select.style.display = 'block';
                    }
                }

                async function saveDrawerDependencies() {
                    const select = document.getElementById('pmo-dependency-select');
                    if (!select) return;

                    const currentId = Number(select.dataset.currentPlannerId || 0);
                    if (!currentId) {
                        toast('Nicht verknüpfbar', 'Diese Aufgabe hat keine PlannerItem-ID.', 'bad');
                        return;
                    }

                    const selected = Array.from(select.selectedOptions)
                        .map(option => Number(option.value || 0))
                        .filter(Boolean);

                    const oldParents = dependencyEdges()
                        .filter(edge => Number(edge.to) === currentId)
                        .map(edge => Number(edge.from))
                        .filter(Boolean);

                    const oldSet = new Set(oldParents);
                    const newSet = new Set(selected);
                    const toRemove = oldParents.filter(id => !newSet.has(id));
                    const toAdd = selected.filter(id => !oldSet.has(id));

                    for (const parentId of toRemove) {
                        await requestJson(endpoint('dependencyDestroy'), {
                            method: 'DELETE',
                            body: JSON.stringify({ item_id: currentId, depends_on_id: parentId }),
                        });
                    }

                    for (const parentId of toAdd) {
                        await requestJson(endpoint('dependencyStore'), {
                            method: 'POST',
                            body: JSON.stringify({ item_id: currentId, depends_on_id: parentId, reason: 'Detailbereich' }),
                        });
                    }

                    toast('Abhängigkeiten gespeichert', 'Die Vorgänger wurden aktualisiert.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                function renderGallery(item) {
                    const plannerItemId = itemPlannerId(item);
                    const assets = arr(item.assets || item.gallery || item.files);
                    const uploadBox = plannerItemId ? `
                                                                        <div class="pmo-section-action-row" style="margin-bottom:12px;">
                                                                            <input type="file" id="pmo-gallery-files-${plannerItemId}" class="pmo-input" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                                                            <button type="button" class="pmo-btn-soft" data-pmo-gallery-upload="${plannerItemId}">${icon('upload-cloud', 14)} In Kundengalerie speichern</button>
                                                                        </div>
                                                                        <div class="pmo-note" style="margin-bottom:12px;">Wird in <strong>images</strong> mit Kunde, Objekt, Produkt und Planner-Task gespeichert.</div>
                                                                    ` : `<div class="pmo-empty" style="margin:0 0 12px;padding:14px;">Diese Karte hat noch keine Planner-ID.</div>`;

                    const grid = assets.length
                        ? `<div class="pmo-gallery-grid">${assets.map(asset => {
                            const imageId = asset.image_id || asset.id || '';
                            const url = asset.url || asset.file_url || asset.path || asset.file_path || '';
                            const label = asset.name || asset.file_name || asset.image_name || 'Datei';
                            const isImg = /\.(png|jpe?g|webp|gif|svg)$/i.test(url || label);
                            return `<div class="pmo-gallery-card" style="position:relative;">
                                                                                <a href="${esc(url)}" target="_blank" rel="noopener" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;text-decoration:none;color:inherit;">
                                                                                    ${isImg ? `<img src="${esc(url)}" alt="${esc(label)}">` : esc(label)}
                                                                                </a>
                                                                                ${plannerItemId && imageId ? `<button type="button" class="pmo-icon-btn pmo-danger" data-pmo-gallery-delete="${esc(imageId)}" data-item-id="${plannerItemId}" title="Datei löschen" style="position:absolute;right:6px;top:6px;width:30px;height:30px;background:#fff;">${icon('trash-2', 13)}</button>` : ''}
                                                                            </div>`;
                        }).join('')}</div>`
                        : `<div class="pmo-gallery-grid"><div class="pmo-gallery-card">Noch keine Bilder oder Dateien vorhanden.</div><div class="pmo-gallery-card">Fotos, PDFs, Montagebilder</div><div class="pmo-gallery-card">Direkt in Kundengalerie</div></div>`;

                    return `${uploadBox}${grid}`;
                }

                function renderComments(item) {
                    const plannerItemId = itemPlannerId(item);
                    const comments = arr(item.comments || item.reports);
                    const rows = comments.length ? comments.map(c => {
                        const commentId = c.id || '';
                        return `<div class="pmo-history-card" style="margin-bottom:10px;">
                                                                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                                                                                <div>
                                                                                    <div class="pmo-history-title">${esc(c.title || c.author_name || 'Kommentar')}</div>
                                                                                    <div class="pmo-history-meta">${esc(c.created_at || c.date || '')}</div>
                                                                                </div>
                                                                                ${plannerItemId && commentId ? `<button type="button" class="pmo-icon-btn pmo-danger" data-pmo-comment-delete="${esc(commentId)}" data-item-id="${plannerItemId}" title="Kommentar löschen">${icon('trash-2', 13)}</button>` : ''}
                                                                            </div>
                                                                            <div class="pmo-history-note">${esc(c.body || c.comment || c.report || c.description || '')}</div>
                                                                        </div>`;
                    }).join('') : `<div class="pmo-empty" style="margin:0 0 12px;padding:18px;">Noch keine Kommentare oder Berichte vorhanden.</div>`;

                    const input = plannerItemId ? `
                                                                        <textarea id="pmo-comment-input-${plannerItemId}" class="pmo-textarea" placeholder="Kommentar / Bericht schreiben..."></textarea>
                                                                        <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
                                                                            <button type="button" class="pmo-btn-soft" data-pmo-comment-save="${plannerItemId}">${icon('save', 14)} Kommentar speichern</button>
                                                                        </div>
                                                                    ` : `<div class="pmo-empty" style="margin:0;padding:14px;">Diese Karte hat noch keine Planner-ID.</div>`;

                    return `${rows}${input}`;
                }


                async function storeItemComment(plannerItemId) {
                    const input = document.getElementById(`pmo-comment-input-${plannerItemId}`);
                    const body = (input?.value || '').trim();

                    if (!body) {
                        toast('Kommentar fehlt', 'Bitte zuerst einen Kommentar schreiben.', 'bad');
                        return;
                    }

                    await requestJson(itemEndpoint('itemCommentStore', plannerItemId), {
                        method: 'POST',
                        body: JSON.stringify({ body }),
                    });

                    toast('Kommentar gespeichert', 'Der Kommentar wurde zur Aufgabe hinzugefügt.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function destroyItemComment(plannerItemId, commentId) {
                    if (!commentId || !confirm('Kommentar wirklich löschen?')) return;

                    await requestJson(itemEndpoint('itemCommentDestroy', plannerItemId, { comment: commentId }), {
                        method: 'DELETE',
                    });

                    toast('Kommentar gelöscht', 'Der Kommentar wurde entfernt.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function uploadItemGallery(plannerItemId) {
                    const input = document.getElementById(`pmo-gallery-files-${plannerItemId}`);
                    const files = Array.from(input?.files || []);

                    if (!files.length) {
                        toast('Keine Datei ausgewählt', 'Bitte zuerst Bilder oder Dateien auswählen.', 'bad');
                        return;
                    }

                    const formData = new FormData();
                    files.forEach(file => formData.append('files[]', file));
                    formData.append('stage', 'planner_task');
                    formData.append('status', 'planner_gallery');

                    await requestForm(itemEndpoint('itemGalleryUpload', plannerItemId), formData, {
                        method: 'POST',
                    });

                    toast('Galerie gespeichert', 'Die Dateien wurden in der Kundengalerie gespeichert.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                async function destroyItemGallery(plannerItemId, imageId) {
                    if (!imageId || !confirm('Datei wirklich aus dieser Aufgabe entfernen?')) return;

                    await requestJson(itemEndpoint('itemGalleryDestroy', plannerItemId, { image: imageId }), {
                        method: 'DELETE',
                    });

                    toast('Datei gelöscht', 'Die Datei wurde entfernt.');
                    const activeKey = state.activeWorkKey;
                    await loadPayload(false);
                    if (activeKey) openJobDrawer(activeKey);
                }

                function drawerTabCount(value) {
                    const n = Number(value || 0);
                    return `<span class="pmo-job-tab-count">${n}</span>`;
                }

                function switchJobDrawerTab(tab = 'overview') {
                    const active = tab || 'overview';
                    document.querySelectorAll('#pmo-job-body [data-pmo-job-tab]').forEach(btn => {
                        btn.classList.toggle('is-active', btn.dataset.pmoJobTab === active);
                    });
                    document.querySelectorAll('#pmo-job-body [data-pmo-job-panel]').forEach(panel => {
                        panel.classList.toggle('is-active', panel.dataset.pmoJobPanel === active);
                    });
                    state.drawerTab = active;
                    redrawIcons();
                }

                function drawerTabButton(tab, label, iconName, count, activeTab) {
                    return `<button type="button" class="pmo-job-tab ${tab === activeTab ? 'is-active' : ''}" data-pmo-job-tab="${esc(tab)}">
                                                                                ${icon(iconName, 14)} <span>${esc(label)}</span>${Number(count || 0) > 0 ? drawerTabCount(count) : ''}
                                                                            </button>`;
                }

                function openJobDrawer(key) {
                    const item = findWorkItem(key);
                    if (!item) return;
                    state.activeWorkKey = key;
                    const done = isDoneStatus(item.status);
                    const materials = materialRowsForItem(item);
                    const steps = arr(item.steps || item.checklist || item.keys || item.subtasks);
                    const galleries = arr(item.assets || item.gallery || item.files);
                    const comments = arr(item.comments || item.reports);
                    const dependencies = arr(item.dependencies || item.blocked_by || item.depends_on);
                    const activeTab = state.drawerTab || 'overview';

                    document.getElementById('pmo-job-pills').innerHTML = `<span class="pmo-chip gray">${esc(typeLabel(item.source_type))}</span><span class="pmo-chip ${plannerStatusChipClass(item.status)}">${esc(plannerStatusLabel(item.status))}</span><span class="pmo-chip">${esc(item.sub_stage_name || 'Montage')}</span>${renderMaterialSummaryChips(item, true)}`;
                    document.getElementById('pmo-job-title').textContent = item.title || 'Arbeit';
                    document.getElementById('pmo-job-meta').innerHTML = `<span class="pmo-chip gray">${moneyDate(item.planned_date || item.date)}</span><span class="pmo-chip gray">${timeOnly(item.start_at || item.planned_start_at)} - ${timeOnly(item.end_at || item.planned_end_at)}</span><span class="pmo-chip gray">#${esc(item.source_id || item.id)}</span>`;

                    document.getElementById('pmo-job-body').innerHTML = `
                                                                        <div class="pmo-job-tabs" role="tablist" aria-label="Aufgabenbereiche">
                                                                            ${drawerTabButton('overview', 'Übersicht', 'align-left', 0, activeTab)}
                                                                            ${drawerTabButton('dependencies', 'Abhängigkeiten', 'git-branch', dependencies.length, activeTab)}
                                                                            ${drawerTabButton('steps', 'Schritte', 'list-checks', steps.length, activeTab)}
                                                                            ${drawerTabButton('materials', 'Materialliste', 'package', materials.length + sharedMaterialRowsForItem(item).length, activeTab)}
                                                                            ${drawerTabButton('gallery', 'Galerie', 'images', galleries.length, activeTab)}
                                                                            ${drawerTabButton('comments', 'Kommentare', 'message-square-text', comments.length, activeTab)}
                                                                        </div>

                                                                        <section class="pmo-job-panel ${activeTab === 'overview' ? 'is-active' : ''}" data-pmo-job-panel="overview">
                                                                            <div class="pmo-detail-grid">
                                                                                <div class="pmo-detail-card pmo-span-2">
                                                                                    <div class="pmo-detail-title">${icon('align-left', 15)} Übersicht</div>
                                                                                    <div class="pmo-work-desc" style="display:block;overflow:visible;">${esc(item.description || 'Keine Beschreibung vorhanden.')}</div>
                                                                                </div>
                                                                                <div class="pmo-detail-card">
                                                                                    <div class="pmo-detail-title">${icon('activity', 15)} Status</div>
                                                                                    ${renderTaskStatusManager(item)}
                                                                                </div>
                                                                                <div class="pmo-detail-card">
                                                                                    <div class="pmo-detail-title">${icon('users', 15)} Beteiligtes Team</div>
                                                                                    <div class="pmo-team-list">${renderTeamPillsForItem(item)}</div>
                                                                                </div>
                                                                            </div>
                                                                        </section>

                                                                        <section class="pmo-job-panel ${activeTab === 'dependencies' ? 'is-active' : ''}" data-pmo-job-panel="dependencies">
                                                                            <div class="pmo-detail-card pmo-span-2">
                                                                                <div class="pmo-detail-title">${icon('git-branch', 15)} Abhängigkeiten</div>
                                                                                ${renderDependenciesForItem(item)}
                                                                            </div>
                                                                        </section>

                                                                        <section class="pmo-job-panel ${activeTab === 'steps' ? 'is-active' : ''}" data-pmo-job-panel="steps">
                                                                            <div class="pmo-detail-card pmo-span-2">
                                                                                <div class="pmo-detail-title">${icon('list-checks', 15)} Schritte / Keys</div>
                                                                                ${renderSteps(item)}
                                                                            </div>
                                                                        </section>

                                                                        <section class="pmo-job-panel ${activeTab === 'materials' ? 'is-active' : ''}" data-pmo-job-panel="materials">
                                                                            <div class="pmo-detail-card pmo-span-2">
                                                                                <div class="pmo-detail-title">${icon('package', 15)} Materialliste</div>
                                                                                ${renderMaterials(item)}
                                                                            </div>
                                                                        </section>

                                                                        <section class="pmo-job-panel ${activeTab === 'gallery' ? 'is-active' : ''}" data-pmo-job-panel="gallery">
                                                                            <div class="pmo-detail-card pmo-span-2">
                                                                                <div class="pmo-detail-title">${icon('images', 15)} Galerie</div>
                                                                                ${renderGallery(item)}
                                                                            </div>
                                                                        </section>

                                                                        <section class="pmo-job-panel ${activeTab === 'comments' ? 'is-active' : ''}" data-pmo-job-panel="comments">
                                                                            <div class="pmo-detail-card pmo-span-2">
                                                                                <div class="pmo-detail-title">${icon('message-square-text', 15)} Kommentar / Bericht</div>
                                                                                ${renderComments(item)}
                                                                            </div>
                                                                        </section>`;

                    document.getElementById('pmo-job-backdrop')?.classList.add('is-open');
                    document.getElementById('pmo-job-drawer')?.classList.add('is-open');
                    switchJobDrawerTab(activeTab);
                    redrawIcons();
                    setTimeout(activateDependencySelect2, 0);
                }

                function closeJobDrawer() {
                    document.getElementById('pmo-job-backdrop')?.classList.remove('is-open');
                    document.getElementById('pmo-job-drawer')?.classList.remove('is-open');
                }

                function stepEmployeeOptions(selected = '') {
                    const active = arr(state.payload?.employees_active).length ? arr(state.payload?.employees_active) : arr(state.payload?.employees);
                    return `<option value="">Gleicher Mitarbeiter</option>` + active.map(e => `<option value="${e.id}" ${Number(selected) === Number(e.id) ? 'selected' : ''}>${esc(e.full_name || `${e.name || ''} ${e.lastname || ''}`.trim() || ('#' + e.id))}</option>`).join('');
                }

                function addStepRow(data = {}) {
                    const wrap = document.getElementById('pmo-bulk-steps');
                    if (!wrap) return;
                    const row = document.createElement('div');
                    row.className = 'pmo-bulk-step';
                    row.innerHTML = `<input type="text" class="pmo-input" data-step-title placeholder="Schritt / Key" value="${esc(data.title || '')}">
                                                                                                                        <select class="pmo-select" data-step-employee>${stepEmployeeOptions(data.employee_id || document.getElementById('pmo-work-employee-id')?.value || '')}</select>
                                                                                                                        <input type="time" class="pmo-input" data-step-time value="${esc(data.due_time || document.getElementById('pmo-work-start')?.value || '08:00')}">
                                                                                                                        <button type="button" class="pmo-icon-btn pmo-danger" data-remove-step>${icon('trash-2', 15)}</button>`;
                    wrap.appendChild(row);
                    redrawIcons();
                }

                function collectSteps() {
                    return Array.from(document.querySelectorAll('#pmo-bulk-steps .pmo-bulk-step')).map((row, idx) => ({
                        title: row.querySelector('[data-step-title]')?.value || '',
                        employee_id: row.querySelector('[data-step-employee]')?.value || document.getElementById('pmo-work-employee-id')?.value || '',
                        due_date: document.getElementById('pmo-work-date')?.value || state.date,
                        due_time: row.querySelector('[data-step-time]')?.value || document.getElementById('pmo-work-start')?.value || '08:00',
                        sort_order: idx + 1,
                    })).filter(step => step.title.trim() !== '');
                }

                function setWorkMode(mode) {
                    const isBulk = mode === 'bulk';
                    document.getElementById('pmo-bulk-area')?.classList.toggle('pmo-hidden', !isBulk);
                    if (isBulk && !document.querySelector('#pmo-bulk-steps .pmo-bulk-step')) {
                        addStepRow({ title: 'Vorbereitung' });
                        addStepRow({ title: 'Ausführung' });
                    }
                }


                function dateObj(value) {
                    if (!value) return null;
                    const normalized = String(value).replace(' ', 'T');
                    const d = new Date(normalized);
                    return Number.isNaN(d.getTime()) ? null : d;
                }

                function itemStart(item) {
                    return item.timeline_start_at || item.planned_start_at || item.start_at || item.date || item.created_at || null;
                }

                function itemEnd(item) {
                    if (item.timeline_end_at || item.planned_end_at || item.end_at || item.done_at) return item.timeline_end_at || item.planned_end_at || item.end_at || item.done_at;
                    const start = dateObj(itemStart(item));
                    if (!start) return null;
                    start.setMinutes(start.getMinutes() + Number(item.duration_minutes || 60));
                    return start.toISOString();
                }

                function itemPlannerId(item) {
                    return Number(item.planner_item_id || 0);
                }

                function dependencyEdges() {
                    return arr(state.payload?.dependency_edges);
                }

                function dependencySequence(item) {
                    const id = itemPlannerId(item);
                    const seq = state.payload?.dependency_sequence || {};
                    return id ? (seq[id] || seq[String(id)] || item.dependency_sequence || '') : '';
                }

                function startOfMonth(date) {
                    const d = new Date(date.getTime());
                    d.setDate(1);
                    d.setHours(0, 0, 0, 0);
                    return d;
                }

                function addMonths(date, months) {
                    const d = new Date(date.getTime());
                    d.setMonth(d.getMonth() + months);
                    return d;
                }

                function diffDays(a, b) {
                    const da = new Date(a.getFullYear(), a.getMonth(), a.getDate()).getTime();
                    const db = new Date(b.getFullYear(), b.getMonth(), b.getDate()).getTime();
                    return Math.round((db - da) / 86400000);
                }

                function monthName(date) {
                    return date.toLocaleDateString('de-DE', { month: 'long' });
                }

                function statusInfo(status) {
                    const st = String(status || 'open').toLowerCase();
                    if (['done', 'completed', 'finished', 'closed', 'ended', 'erledigt'].includes(st)) return { label: 'Erledigt', color: 'green' };
                    if (['in_progress', 'started', 'processing', 'bearbeitung', 'in bearbeitung'].includes(st)) return { label: 'In Bearbeitung', color: 'lime' };
                    if (['review', 'checking', 'pruefung', 'prüfung'].includes(st)) return { label: 'Zur Prüfung', color: 'blue' };
                    if (['blocked', 'cancel', 'canceled', 'storniert'].includes(st)) return { label: status || 'Blockiert', color: 'red' };
                    return { label: status || 'Offen', color: 'orange' };
                }

                function timelineConfig() {
                    const items = ganttWorkItems();
                    const starts = items.map(item => dateObj(itemStart(item))).filter(Boolean);
                    const ends = items.map(item => dateObj(itemEnd(item))).filter(Boolean);

                    let minDate = starts.length ? new Date(Math.min(...starts.map(d => d.getTime()))) : new Date();
                    let maxDate = ends.length ? new Date(Math.max(...ends.map(d => d.getTime()))) : addMonths(minDate, 2);

                    minDate = startOfMonth(minDate);
                    maxDate = addMonths(startOfMonth(maxDate), 1);

                    // keep the visual similar to the project-plan screenshot: several months visible
                    while (diffDays(minDate, maxDate) < 150) {
                        maxDate = addMonths(maxDate, 1);
                    }

                    const zoom = Number(state.ganttZoom || 1);
                    const pxPerDay = 7 * zoom;
                    const totalDays = Math.max(1, diffDays(minDate, maxDate));
                    const totalWidth = Math.max(900, totalDays * pxPerDay);
                    const months = [];
                    let cursor = startOfMonth(minDate);

                    while (cursor < maxDate) {
                        const next = addMonths(cursor, 1);
                        months.push({
                            left: diffDays(minDate, cursor) * pxPerDay,
                            width: Math.max(1, diffDays(cursor, next) * pxPerDay),
                            label: monthName(cursor),
                        });
                        cursor = next;
                    }

                    const gridLines = [];
                    for (let d = 0; d <= totalDays; d += 14) {
                        gridLines.push(d * pxPerDay);
                    }

                    return { minDate, maxDate, pxPerDay, totalDays, totalWidth, months, gridLines };
                }

                function renderGantt() {
                    const items = ganttWorkItems();
                    const cfg = timelineConfig();
                    const gantt = document.getElementById('pmo-gantt');

                    if (!items.length) {
                        gantt.innerHTML = `<div class="pmo-empty">Keine Aufgaben im gesamten Projektplan gefunden.</div>`;
                        return;
                    }

                    const sorted = [...items].sort((a, b) => {
                        const da = dateObj(itemStart(a))?.getTime() || 0;
                        const db = dateObj(itemStart(b))?.getTime() || 0;
                        return da - db || String(a.title || '').localeCompare(String(b.title || ''), 'de');
                    });

                    gantt.innerHTML = `
                                                                                                        <div class="pmo-project-gantt-wrap">
                                                                                                            <div class="pmo-project-gantt-inner" id="pmo-gantt-inner" style="min-width:${cfg.totalWidth + 335}px;">
                                                                                                                <svg class="pmo-dependency-svg" id="pmo-dependency-svg"></svg>

                                                                                                                <div class="pmo-project-gantt-head">
                                                                                                                    <div class="pmo-project-gantt-left">Name</div>
                                                                                                                    <div class="pmo-project-gantt-status">Status</div>
                                                                                                                    <div class="pmo-project-gantt-months" style="width:${cfg.totalWidth}px;">
                                                                                                                        ${cfg.months.map(m => `<div class="pmo-project-gantt-month" style="left:${m.left}px;width:${m.width}px;">${esc(m.label)}</div>`).join('')}
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                ${sorted.map((item, idx) => renderProjectGanttRow(item, idx, cfg, sorted[idx - 1] || null)).join('')}

                                                                                                                <div class="pmo-project-gantt-dependency-list">
                                                                                                                    <strong>Abhängigkeiten:</strong>
                                                                                                                    ${renderGanttDependencyList()}
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>`;

                    window.requestAnimationFrame(renderDependencyLines);
                    redrawIcons();
                }

                function ganttGapLabel(item, previous) {
                    if (!previous) return 'Start';
                    const prevEnd = dateObj(itemEnd(previous));
                    const currentStart = dateObj(itemStart(item));
                    if (!prevEnd || !currentStart) return 'Abstand: —';
                    const minutes = Math.round((currentStart.getTime() - prevEnd.getTime()) / 60000);
                    const abs = Math.abs(minutes);
                    const hours = Math.floor(abs / 60);
                    const mins = abs % 60;
                    const label = `${hours ? `${hours}h ` : ''}${mins}m`;
                    return minutes < 0 ? `Überlappt ${label}` : `Abstand ${label}`;
                }

                function renderProjectGanttRow(item, idx, cfg, previous = null) {
                    const plannerId = itemPlannerId(item);
                    const info = statusInfo(item.status);
                    const sourceClass = Number(state.dependencySourceId || 0) === plannerId ? 'is-link-source' : '';
                    const doneLine = isDoneStatus(item.status) ? ` · ${doneByName(item) || 'erledigt'}` : '';

                    return `<div class="pmo-project-gantt-row ${sourceClass}" data-pmo-work="${esc(itemKey(item))}" data-pmo-link-card="${plannerId}" data-item-id="${plannerId}">
                                                                                                                <div class="pmo-project-gantt-task">
                                                                                                                    <input type="checkbox" aria-hidden="true" ${isDoneStatus(item.status) ? 'checked' : ''}>
                                                                                                                    <span class="pmo-project-gantt-name" title="${esc(item.title || 'Arbeit')}">${esc(item.title || 'Arbeit')}</span>
                                                                                                                </div>
                                                                                                                <div class="pmo-project-gantt-state">
                                                                                                                    <span class="pmo-project-gantt-status-line"><span class="pmo-project-gantt-dot ${info.color}"></span>${esc(info.label)}${esc(doneLine)}</span>
                                                                                                                    <span class="pmo-gantt-gap">${icon('timer', 11)} ${esc(ganttGapLabel(item, previous))}</span>
                                                                                                                </div>
                                                                                                                <div class="pmo-project-gantt-line" style="width:${cfg.totalWidth}px;">
                                                                                                                    ${cfg.gridLines.map(x => `<span class="pmo-project-gantt-dayline" style="left:${x}px;"></span>`).join('')}
                                                                                                                    ${renderGanttBar(item, idx, cfg)}
                                                                                                                </div>
                                                                                                            </div>`;
                }

                function renderGanttBar(item, idx, cfg) {
                    const start = dateObj(itemStart(item));
                    const end = dateObj(itemEnd(item));
                    const plannerId = itemPlannerId(item);
                    const progress = workProgress(item);
                    const done = isDoneStatus(item.status);

                    let left = 95 + (idx % 8) * 13;
                    let width = 110;

                    if (start) {
                        left = Math.max(0, diffDays(cfg.minDate, start) * cfg.pxPerDay);
                    }

                    if (start && end) {
                        width = Math.max(70, Math.max(1, diffDays(start, end)) * cfg.pxPerDay);
                    }

                    const disabledTitle = plannerId ? '' : ' title="Für diesen Eintrag existiert noch kein PlannerItem"';

                    return `<div class="pmo-project-gantt-bar ${done ? 'is-done' : ''}"
                                                                                                                 data-pmo-gantt-card="${plannerId}"
                                                                                                                 data-pmo-work="${esc(itemKey(item))}"
                                                                                                                 data-item-id="${plannerId}"
                                                                                                                 style="left:${left}px;width:${width}px;"${disabledTitle}>
                                                                                                                <span class="pmo-project-gantt-progress" style="width:${progress}%"></span>
                                                                                                                <span class="pmo-project-gantt-percent">${progress}%</span>
                                                                                                            </div>`;
                }

                function renderGanttDependencyList() {
                    const edges = dependencyEdges();
                    if (!edges.length) {
                        return `<div class="pmo-project-gantt-dependency-row">Noch keine Abhängigkeiten vorhanden.</div>`;
                    }
                    return edges.map(edge => `<div class="pmo-project-gantt-dependency-row">${esc(edge.from_title || ('#' + edge.from))} → ${esc(edge.to_title || ('#' + edge.to))}</div>`).join('');
                }

                function renderDependencyLines() {
                    const svg = document.getElementById('pmo-dependency-svg');
                    const inner = document.getElementById('pmo-gantt-inner');

                    if (!svg || !inner) return;

                    const box = inner.getBoundingClientRect();
                    svg.setAttribute('width', box.width);
                    svg.setAttribute('height', box.height);
                    svg.setAttribute('viewBox', `0 0 ${box.width} ${box.height}`);

                    let html = `
                                                                                                        <defs>
                                                                                                            <marker id="pmo-arrow" markerWidth="10" markerHeight="10" refX="8" refY="3" orient="auto">
                                                                                                                <path d="M0,0 L0,6 L9,3 z" fill="#ef4444"></path>
                                                                                                            </marker>
                                                                                                        </defs>`;

                    dependencyEdges().forEach((edge) => {
                        const from = inner.querySelector(`[data-pmo-gantt-card="${edge.from}"]`);
                        const to = inner.querySelector(`[data-pmo-gantt-card="${edge.to}"]`);

                        if (!from || !to) return;

                        const a = from.getBoundingClientRect();
                        const b = to.getBoundingClientRect();

                        const x1 = a.right - box.left;
                        const y1 = a.top + a.height / 2 - box.top;
                        const x2 = b.left - box.left;
                        const y2 = b.top + b.height / 2 - box.top;

                        const sameRow = Math.abs(y2 - y1) < 8;
                        const bend = Math.max(28, Math.abs(y2 - y1) * .45);
                        const midX = sameRow ? x1 + 40 : Math.max(x1 + 36, Math.min(x2 - 36, (x1 + x2) / 2));
                        const path = sameRow
                            ? `M ${x1} ${y1} C ${x1 + 36} ${y1}, ${x2 - 36} ${y2}, ${x2} ${y2}`
                            : `M ${x1} ${y1} C ${midX + bend} ${y1}, ${midX - bend} ${y2}, ${x2} ${y2}`;

                        html += `<path class="pmo-dependency-line" d="${path}" marker-end="url(#pmo-arrow)"></path>`;
                    });

                    svg.innerHTML = html;
                }

                async function saveDependency(itemId, dependsOnId) {
                    if (!state.planId) {
                        toast('Plan fehlt', 'Bitte lade zuerst den Projektplan neu.', 'bad');
                        return;
                    }

                    await requestJson(endpoint('dependencyStore'), {
                        method: 'POST',
                        body: JSON.stringify({
                            item_id: Number(itemId),
                            depends_on_id: Number(dependsOnId),
                            reason: 'Gantt Verknüpfung',
                        }),
                    });

                    state.dependencySourceId = null;
                    state.dependencyMode = false;
                    root.classList.remove('is-dependency-mode');
                    document.getElementById('pmo-dependency-mode')?.classList.remove('is-active');

                    await loadPayload(false);
                    toast('Abhängigkeit gespeichert', 'Die Aufgaben wurden miteinander verbunden.');
                }

                async function removeDependency(itemId, dependsOnId) {
                    await requestJson(endpoint('dependencyDestroy'), {
                        method: 'DELETE',
                        body: JSON.stringify({
                            item_id: Number(itemId),
                            depends_on_id: Number(dependsOnId),
                        }),
                    });

                    await loadPayload(false);
                    toast('Abhängigkeit entfernt', 'Die Verbindung wurde gelöscht.');
                }

                async function handleDependencyClick(itemId) {
                    if (!itemId) {
                        toast('Nicht verknüpfbar', 'Dieser Balken hat noch keine PlannerItem-ID.', 'bad');
                        return;
                    }

                    if (!state.dependencySourceId) {
                        state.dependencySourceId = itemId;
                        renderAll();
                        toast('Start gewählt', 'Jetzt die Aufgabe anklicken, die davon abhängig ist.');
                        return;
                    }

                    if (Number(state.dependencySourceId) === Number(itemId)) {
                        state.dependencySourceId = null;
                        renderAll();
                        toast('Auswahl zurückgesetzt', 'Bitte zwei verschiedene Aufgaben wählen.');
                        return;
                    }

                    await saveDependency(itemId, state.dependencySourceId);
                }

                function renderList() {
                    const items = arr(state.payload?.items);
                    const grouped = items.reduce((acc, item) => {
                        const d = item.date || item.planned_date || state.date || 'no_date';
                        (acc[d] ||= []).push(item);
                        return acc;
                    }, {});
                    const keys = Object.keys(grouped).sort();
                    document.getElementById('pmo-list').innerHTML = keys.length ? keys.map(day => `<details class="pmo-list-day" open>
                                                                                                                        <summary><span>${esc(moneyDate(day))}</span><span>${grouped[day].length} Einträge</span></summary>
                                                                                                                        ${grouped[day].map(item => {
                        const plannerId = itemPlannerId(item);
                        const sourceClass = Number(state.dependencySourceId || 0) === plannerId ? 'is-link-source' : '';
                        return `<div class="pmo-list-row ${sourceClass}" data-pmo-work="${esc(itemKey(item))}" data-pmo-link-card="${plannerId}" data-item-id="${plannerId}" style="cursor:pointer;">
                                                                                                                            <div class="pmo-list-time">${timeOnly(item.start_at || item.planned_start_at)}</div>
                                                                                                                            <div><div class="pmo-work-title">${esc(item.title)}</div><div class="pmo-work-desc">${esc(item.description || '')}</div></div>
                                                                                                                            <div><span class="pmo-chip gray">${esc(typeLabel(item.source_type))}</span></div>
                                                                                                                            <div><span class="pmo-chip ${plannerStatusChipClass(item.status)}">${esc(plannerStatusLabel(item.status))}</span>${isDoneStatus(item.status) ? `<div class="pmo-note">${esc(doneByName(item) || 'Erledigt')}</div>` : ''}</div>
                                                                                                                        </div>`;
                    }).join('')}
                                                                                                                    </details>`).join('') : `<div class="pmo-empty">Keine Einträge für diesen Zeitraum.</div>`;
                }

                function renderTeam() {
                    const employees = arr(state.payload?.employees);
                    const html = employees.map(emp => {
                        const items = employeeItems(emp.id);
                        const c = countsFor(items);
                        const isProtected = arr(emp.roles_clean || emp.roles).some(r => ['Projektleiter', 'Außendienst', 'Lead'].includes(r));
                        return `<div class="pmo-team-card">
                                                                                                                            <img class="pmo-photo" src="${esc(emp.photo_url || emp.image_url || '/images/icons/user.png')}" alt="${esc(emp.full_name)}">
                                                                                                                            <div class="pmo-team-card-body">
                                                                                                                                <div class="pmo-person-name">${esc(emp.full_name || emp.name || ('Mitarbeiter #' + emp.id))}</div>
                                                                                                                                <div class="pmo-role-row">${roleHtml(emp)}</div>
                                                                                                                                <div class="pmo-mini-stats">
                                                                                                                                    <span class="pmo-mini-stat">${icon('ticket', 14)} ${c.ticket}</span>
                                                                                                                                    <span class="pmo-mini-stat">${icon('calendar', 14)} ${c.appointment}</span>
                                                                                                                                    <span class="pmo-mini-stat">${icon('user-check', 14)} ${c.personal}</span>
                                                                                                                                    <span class="pmo-mini-stat">${icon('list-checks', 14)} ${c.task}</span>
                                                                                                                                </div>
                                                                                                                                <div class="pmo-team-card-actions">
                                                                                                                                    <button type="button" class="pmo-btn-soft" data-pmo-add-work="${emp.id}">${icon('plus', 14)} Arbeit</button>
                                                                                                                                    ${isProtected ? '' : `<button type="button" class="pmo-btn-soft" data-pmo-remove-team="${emp.id}">${icon('user-minus', 14)} Entfernen</button>`}
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>`;
                    }).join('') || `<div class="pmo-empty">Noch kein Team vorhanden.</div>`;
                    document.getElementById('pmo-team-grid').innerHTML = html;
                    document.getElementById('pmo-team-modal-list').innerHTML = html;
                }


                function renderOrgChart() {
                    const summary = state.payload?.dependency_summary || {};
                    const tree = arr(state.payload?.dependency_tree);
                    const summaryEl = document.getElementById('pmo-org-summary');
                    const chartEl = document.getElementById('pmo-org-chart');

                    if (!summaryEl || !chartEl) return;

                    summaryEl.innerHTML = `
                                                                                                                <div class="pmo-summary-card"><span>Aufgaben</span><strong>${summary.total_tasks || 0}</strong></div>
                                                                                                                <div class="pmo-summary-card"><span>Abhängigkeiten</span><strong>${summary.total_dependencies || 0}</strong></div>
                                                                                                                <div class="pmo-summary-card"><span>Projektzeit</span><strong style="font-size:18px;">${esc(summary.project_span_label || '—')}</strong></div>
                                                                                                                <div class="pmo-summary-card"><span>Personenzeit</span><strong style="font-size:18px;">${esc(summary.person_label || '—')}</strong></div>`;

                    chartEl.innerHTML = tree.length
                        ? tree.map(node => renderOrgNode(node)).join('')
                        : `<div class="pmo-empty">Noch keine Abhängigkeiten vorhanden. Öffne Gantt und verbinde Aufgaben über den Button Abhängigkeit.</div>`;

                    redrawIcons();
                }

                function renderOrgNode(node) {
                    const children = arr(node.children);
                    const employees = arr(node.employees).map(e => e.name).filter(Boolean).join(', ');

                    return `<div class="pmo-org-node">
                                                                                                                ${node.gap_from_parent_label ? `<div class="pmo-org-gap">${icon('clock', 13)} Abstand: ${esc(node.gap_from_parent_label)}</div>` : ''}
                                                                                                                <div class="pmo-org-node-head">
                                                                                                                    <div>
                                                                                                                        <div class="pmo-org-title">
                                                                                                                            ${node.sequence ? `<span class="pmo-gantt-seq">${node.sequence}</span>` : ''}
                                                                                                                            ${esc(node.title || 'Aufgabe')}
                                                                                                                        </div>
                                                                                                                        <div class="pmo-org-meta">
                                                                                                                            Dauer: ${esc(node.duration_label || '—')}
                                                                                                                            · Status: ${esc(node.status || 'open')}
                                                                                                                            ${employees ? ` · Team: ${esc(employees)}` : ''}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                ${children.length ? `<div class="pmo-org-children">${children.map(child => renderOrgNode(child)).join('')}</div>` : ''}
                                                                                                            </div>`;
                }

                function renderHistory() {
                    const hist = arr(state.payload?.history);
                    const fallback = arr(state.payload?.items).map(item => ({
                        title: item.title,
                        meta: `${moneyDate(item.date || item.planned_date)} · ${typeLabel(item.source_type)}`,
                        reason: `Status: ${item.status || 'open'} · Sub-Stage: ${item.sub_stage_name || 'Montage'}`,
                    }));
                    const rows = hist.length ? hist : fallback;
                    document.getElementById('pmo-history').innerHTML = rows.length ? rows.map(h => `<div class="pmo-history-line"><div class="pmo-history-card"><div class="pmo-history-title">${esc(h.title || h.event || 'Änderung')}</div><div class="pmo-history-meta">${esc(h.meta || h.date || h.created_at || '—')}</div>${h.reason || h.description ? `<div class="pmo-history-note">${esc(h.reason || h.description)}</div>` : ''}</div></div>`).join('') : `<div class="pmo-empty">Noch keine Historie vorhanden.</div>`;
                }

                function populateEmployeeSelects() {
                    const active = arr(state.payload?.employees_active);
                    const currentIds = new Set(arr(state.payload?.employees).map(e => Number(e.id)));
                    const select = document.getElementById('pmo-team-select');
                    select.innerHTML = `<option value="">Mitarbeiter wählen...</option>` + active.filter(e => !currentIds.has(Number(e.id))).map(e => `<option value="${e.id}">${esc(e.full_name || `${e.name || ''} ${e.lastname || ''}`.trim() || ('#' + e.id))}</option>`).join('');
                }

                function openModal(id) {
                    document.getElementById(id)?.classList.add('is-open');
                    redrawIcons();
                }
                function closeModal(id) { document.getElementById(id)?.classList.remove('is-open'); }

                async function saveTeamMember(action, employeeId) {
                    if (!employeeId) return;
                    await requestJson(endpoint('projectTeamMember'), {
                        method: 'POST',
                        body: JSON.stringify({ action, employee_id: Number(employeeId) }),
                    });
                    toast('Team aktualisiert', action === 'add' ? 'Mitarbeiter wurde hinzugefügt.' : 'Mitarbeiter wurde entfernt.');
                    await loadPayload(false);
                }

                async function saveWorkItem() {
                    const employeeId = Number(document.getElementById('pmo-work-employee-id').value || 0);
                    const mode = document.querySelector('input[name="pmo-work-mode"]:checked')?.value || 'single';
                    const payload = {
                        employee_id: employeeId,
                        mode: mode,
                        type: document.getElementById('pmo-work-type').value,
                        title: document.getElementById('pmo-work-title').value,
                        description: document.getElementById('pmo-work-description').value,
                        date: document.getElementById('pmo-work-date').value,
                        start_time: document.getElementById('pmo-work-start').value,
                        end_time: document.getElementById('pmo-work-end').value,
                        steps: mode === 'bulk' ? collectSteps() : [],
                    };
                    if (!payload.employee_id || !payload.title) {
                        toast('Fehlende Daten', 'Bitte Mitarbeiter und Titel angeben.', 'bad');
                        return;
                    }
                    if (payload.mode === 'bulk' && !payload.steps.length) {
                        toast('Fehlende Schritte', 'Bitte mindestens einen Schritt / Key erfassen.', 'bad');
                        return;
                    }
                    await requestJson(endpoint('projectWorkItemStore'), { method: 'POST', body: JSON.stringify(payload) });
                    closeModal('pmo-work-modal');
                    toast('Gespeichert', payload.mode === 'bulk' ? 'Der Bulk Job mit Schritten wurde erstellt.' : 'Die Arbeit wurde erstellt und dem Mitarbeiter zugewiesen.');
                    await loadPayload(false);
                }

                function openWorkModal(employeeId) {
                    const emp = arr(state.payload?.employees).find(e => Number(e.id) === Number(employeeId));
                    document.getElementById('pmo-work-employee-id').value = employeeId;
                    document.getElementById('pmo-work-title').value = '';
                    document.getElementById('pmo-work-description').value = '';
                    document.getElementById('pmo-work-date').value = state.date;
                    document.querySelector('input[name="pmo-work-mode"][value="single"]')?.click();
                    document.getElementById('pmo-bulk-steps').innerHTML = '';
                    setWorkMode('single');
                    document.getElementById('pmo-work-modal-sub').textContent = `Für ${emp?.full_name || 'Mitarbeiter'} erstellen.`;
                    openModal('pmo-work-modal');
                }

                function switchTab(tab) {
                    state.tab = tab;
                    root.querySelectorAll('[data-pmo-tab]').forEach(btn => btn.classList.toggle('is-active', btn.dataset.pmoTab === tab));
                    root.querySelectorAll('[data-pmo-panel]').forEach(panel => panel.classList.toggle('pmo-hidden', panel.dataset.pmoPanel !== tab));
                    redrawIcons();
                }

                function switchView(view) {
                    state.view = view;
                    ['board', 'gantt', 'list'].forEach(v => {
                        document.getElementById('pmo-' + v)?.classList.toggle('pmo-hidden', v !== view);
                        root.querySelector(`[data-pmo-view="${v}"]`)?.classList.toggle('is-active', v === view);
                    });

                    if (view === 'gantt') {
                        window.requestAnimationFrame(renderDependencyLines);
                    }

                    redrawIcons();
                }

                root.addEventListener('click', async (e) => {
                    const close = e.target.closest('[data-pmo-close]');
                    if (close) closeModal(close.dataset.pmoClose);

                    const tab = e.target.closest('[data-pmo-tab]');
                    if (tab) switchTab(tab.dataset.pmoTab);

                    const statusButton = e.target.closest('[data-pmo-status-btn]');
                    if (statusButton) {
                        e.preventDefault();
                        e.stopPropagation();
                        const nextStatus = normalizePlannerStatus(statusButton.dataset.pmoStatusBtn || 'open');
                        if (nextStatus === 'done') {
                            openCompletionReportModal(statusButton.dataset.itemId, nextStatus);
                        } else {
                            updateItemStatus(statusButton.dataset.itemId, nextStatus);
                        }
                        return;
                    }

                    const jobTab = e.target.closest('[data-pmo-job-tab]');
                    if (jobTab) {
                        switchJobDrawerTab(jobTab.dataset.pmoJobTab || 'overview');
                        return;
                    }

                    const attendanceOpen = e.target.closest('[data-pmo-attendance-open]');
                    if (attendanceOpen) {
                        e.preventDefault();
                        e.stopPropagation();
                        openAttendanceActions(Number(attendanceOpen.dataset.pmoAttendanceOpen || 0));
                        return;
                    }

                    const attendanceAction = e.target.closest('[data-pmo-attendance-action]');
                    if (attendanceAction) {
                        e.preventDefault();
                        e.stopPropagation();
                        await handleAttendanceAction(attendanceAction);
                        return;
                    }

                    const attendanceReport = e.target.closest('[data-pmo-attendance-report]');
                    if (attendanceReport) {
                        e.preventDefault();
                        e.stopPropagation();
                        await openAttendanceReport(Number(attendanceReport.dataset.pmoAttendanceReport || 0));
                        return;
                    }

                    const view = e.target.closest('[data-pmo-view]');
                    if (view) switchView(view.dataset.pmoView);

                    const dependencyBtn = e.target.closest('#pmo-dependency-mode');
                    if (dependencyBtn) {
                        state.dependencyMode = !state.dependencyMode;
                        state.dependencySourceId = null;
                        dependencyBtn.classList.toggle('is-active', state.dependencyMode);
                        root.classList.toggle('is-dependency-mode', state.dependencyMode);
                        renderAll();
                        toast('Abhängigkeitsmodus', state.dependencyMode ? 'Eine Aufgabe anklicken, danach die abhängige Aufgabe anklicken. Funktioniert in Board, Gantt und Liste.' : 'Abhängigkeitsmodus wurde beendet.');
                        return;
                    }

                    if (e.target.closest('#pmo-gantt-zoom-in')) {
                        state.ganttZoom = Math.min(2.5, Number(state.ganttZoom || 1) + 0.15);
                        switchView('gantt');
                        renderGantt();
                        return;
                    }

                    if (e.target.closest('#pmo-gantt-zoom-out')) {
                        state.ganttZoom = Math.max(0.45, Number(state.ganttZoom || 1) - 0.15);
                        switchView('gantt');
                        renderGantt();
                        return;
                    }

                    const saveDrawerDeps = e.target.closest('#pmo-save-drawer-dependencies');
                    if (saveDrawerDeps) {
                        e.preventDefault();
                        e.stopPropagation();
                        await saveDrawerDependencies();
                        return;
                    }

                    const openStepBtn = e.target.closest('[data-pmo-step-open]');
                    if (openStepBtn) {
                        const id = openStepBtn.dataset.pmoStepOpen;
                        document.getElementById(`pmo-step-add-box-${id}`)?.classList.remove('pmo-hidden');
                        return;
                    }

                    const cancelStepBtn = e.target.closest('[data-pmo-step-cancel]');
                    if (cancelStepBtn) {
                        const id = cancelStepBtn.dataset.pmoStepCancel;
                        document.getElementById(`pmo-step-add-box-${id}`)?.classList.add('pmo-hidden');
                        return;
                    }

                    const saveStepBtn = e.target.closest('[data-pmo-step-save]');
                    if (saveStepBtn) {
                        const plannerItemId = Number(saveStepBtn.dataset.pmoStepSave || 0);
                        const box = saveStepBtn.closest('.pmo-step-add-box');
                        await storeItemStep(plannerItemId, box);
                        return;
                    }

                    const toggleStep = e.target.closest('[data-pmo-step-toggle]');
                    if (toggleStep) {
                        const stepId = toggleStep.dataset.pmoStepToggle;
                        const plannerItemId = Number(toggleStep.dataset.itemId || 0);
                        await updateItemStep(plannerItemId, stepId, { status: toggleStep.checked ? 'done' : 'open', is_completed: toggleStep.checked ? 1 : 0 });
                        return;
                    }

                    const deleteStep = e.target.closest('[data-pmo-step-delete]');
                    if (deleteStep) {
                        await destroyItemStep(Number(deleteStep.dataset.itemId || 0), deleteStep.dataset.pmoStepDelete);
                        return;
                    }

                    const toggleSharedMaterial = e.target.closest('[data-pmo-toggle-shared-material]');
                    if (toggleSharedMaterial) {
                        e.preventDefault();
                        e.stopPropagation();
                        const key = toggleSharedMaterial.dataset.pmoToggleSharedMaterial || '';
                        if (key) {
                            state.collapsedSharedMaterials = state.collapsedSharedMaterials || {};
                            state.collapsedSharedMaterials[key] = !isSharedMaterialCollapsed(key, true);
                            renderBoard();
                            if (state.activeWorkKey) renderDrawer();
                        }
                        return;
                    }

                    const openGroupMaterial = e.target.closest('[data-pmo-open-group-material]');
                    if (openGroupMaterial) {
                        openGroupMaterialModal(Number(openGroupMaterial.dataset.pmoOpenGroupMaterial || 0));
                        return;
                    }

                    const openMaterial = e.target.closest('[data-pmo-open-material-modal]');
                    if (openMaterial) {
                        openMaterialModal(Number(openMaterial.dataset.pmoOpenMaterialModal || 0));
                        return;
                    }

                    const importMaterials = e.target.closest('[data-pmo-import-deal-materials]');
                    if (importMaterials) {
                        await importDealMaterials(Number(importMaterials.dataset.pmoImportDealMaterials || 0));
                        return;
                    }

                    const materialTab = e.target.closest('[data-pmo-material-tab]');
                    if (materialTab) {
                        state.materialModal = { ...(state.materialModal || {}), tab: materialTab.dataset.pmoMaterialTab };
                        renderMaterialModal();
                        return;
                    }

                    const materialSearch = e.target.closest('#pmo-material-search-btn');
                    if (materialSearch) {
                        await searchMaterialProducts();
                        return;
                    }

                    const clearMaterialSearch = e.target.closest('#pmo-material-clear-search');
                    if (clearMaterialSearch) {
                        state.materialModal = { ...(state.materialModal || {}), localSearch: '' };
                        renderMaterialModal();
                        return;
                    }

                    const masterDetail = e.target.closest('[data-pmo-master-detail]');
                    if (masterDetail) {
                        openMasterDetailModal(masterDetail.dataset.pmoMasterDetail || '');
                        return;
                    }

                    const materialAdd = e.target.closest('[data-pmo-material-add]');
                    if (materialAdd) {
                        const payload = JSON.parse(materialAdd.dataset.pmoMaterialAdd || '{}');
                        const qtyInput = materialAdd.closest('.pmo-material-source-actions')?.querySelector('[data-material-qty]');
                        if (qtyInput) payload.qty = Number(qtyInput.value || payload.qty || 1);
                        await addMaterialToItem(Number(state.materialModal?.plannerItemId || 0), payload, payload.origin_type || 'manual');
                        return;
                    }

                    const manualMaterialSave = e.target.closest('#pmo-manual-material-save');
                    if (manualMaterialSave) {
                        await saveManualMaterial();
                        return;
                    }

                    const toggleMaterial = e.target.closest('[data-pmo-material-toggle]');
                    if (toggleMaterial) {
                        await updateItemMaterial(Number(toggleMaterial.dataset.itemId || 0), toggleMaterial.dataset.pmoMaterialToggle, { active: toggleMaterial.checked ? 1 : 0 });
                        return;
                    }

                    const deleteMaterial = e.target.closest('[data-pmo-material-delete]');
                    if (deleteMaterial) {
                        await destroyItemMaterial(Number(deleteMaterial.dataset.itemId || 0), deleteMaterial.dataset.pmoMaterialDelete);
                        return;
                    }

                    const materialRequestAction = e.target.closest('[data-pmo-material-request-action]');
                    if (materialRequestAction) {
                        e.preventDefault();
                        e.stopPropagation();
                        await updateMaterialRequestStatus(
                            Number(materialRequestAction.dataset.itemId || 0),
                            Number(materialRequestAction.dataset.requestId || 0),
                            materialRequestAction.dataset.pmoMaterialRequestAction || ''
                        );
                        return;
                    }


                    const saveComment = e.target.closest('[data-pmo-comment-save]');
                    if (saveComment) {
                        await storeItemComment(Number(saveComment.dataset.pmoCommentSave || 0));
                        return;
                    }

                    const deleteComment = e.target.closest('[data-pmo-comment-delete]');
                    if (deleteComment) {
                        await destroyItemComment(Number(deleteComment.dataset.itemId || 0), deleteComment.dataset.pmoCommentDelete);
                        return;
                    }

                    const uploadGallery = e.target.closest('[data-pmo-gallery-upload]');
                    if (uploadGallery) {
                        await uploadItemGallery(Number(uploadGallery.dataset.pmoGalleryUpload || 0));
                        return;
                    }

                    const deleteGallery = e.target.closest('[data-pmo-gallery-delete]');
                    if (deleteGallery) {
                        await destroyItemGallery(Number(deleteGallery.dataset.itemId || 0), deleteGallery.dataset.pmoGalleryDelete);
                        return;
                    }

                    const dependencyCard = e.target.closest('[data-pmo-gantt-card], [data-pmo-link-card]');
                    if (dependencyCard && state.dependencyMode) {
                        e.preventDefault();
                        e.stopPropagation();
                        await handleDependencyClick(Number(dependencyCard.dataset.itemId || dependencyCard.dataset.pmoLinkCard || dependencyCard.dataset.pmoGanttCard || 0));
                        return;
                    }

                    const add = e.target.closest('[data-pmo-add-work]');
                    if (add) openWorkModal(add.dataset.pmoAddWork);

                    const employeeToggle = e.target.closest('[data-pmo-toggle-employee]');
                    if (employeeToggle) {
                        const id = employeeToggle.dataset.pmoToggleEmployee;
                        state.expandedEmployees[id] = !state.expandedEmployees[id];
                        renderBoard();
                        redrawIcons();
                    }

                    const work = e.target.closest('[data-pmo-work]');
                    if (work && !e.target.closest('[data-pmo-add-work], [data-pmo-remove-team], button, a, input, select, textarea')) {
                        openJobDrawer(work.dataset.pmoWork);
                    }

                    const removeStep = e.target.closest('[data-remove-step]');
                    if (removeStep) removeStep.closest('.pmo-bulk-step')?.remove();

                    const remove = e.target.closest('[data-pmo-remove-team]');
                    if (remove) await saveTeamMember('remove', remove.dataset.pmoRemoveTeam);

                    if (e.target.closest('#pmo-open-team-modal, #pmo-open-team-modal-2, #pmo-open-team-modal-3')) openModal('pmo-team-modal');
                    if (e.target.closest('#pmo-refresh-btn')) await loadPayload(true);
                    if (e.target.closest('#pmo-add-team-member')) await saveTeamMember('add', document.getElementById('pmo-team-select').value);
                    if (e.target.closest('#pmo-save-work')) await saveWorkItem();
                    if (e.target.classList.contains('pmo-modal-backdrop')) closeModal(e.target.id);
                });

                document.getElementById('pmo-job-close')?.addEventListener('click', closeJobDrawer);
                document.getElementById('pmo-job-backdrop')?.addEventListener('click', closeJobDrawer);
                document.getElementById('pmo-completion-report-save')?.addEventListener('click', () => submitCompletionReport(false));
                document.getElementById('pmo-completion-report-skip')?.addEventListener('click', () => submitCompletionReport(true));
                document.getElementById('pmo-add-step')?.addEventListener('click', () => addStepRow());
                document.querySelectorAll('input[name="pmo-work-mode"]').forEach(radio => radio.addEventListener('change', () => setWorkMode(radio.value)));
                document.getElementById('pmo-work-search')?.addEventListener('input', e => { state.workSearch = e.target.value || ''; renderAll(); });
                document.getElementById('pmo-work-filter-type')?.addEventListener('change', e => { state.workType = e.target.value || ''; renderAll(); });
                document.getElementById('pmo-material-request-search')?.addEventListener('input', e => {
                    state.materialRequestSearch = e.target.value || '';
                    renderMaterialRequestsInbox();
                });
                document.getElementById('pmo-material-request-status')?.addEventListener('change', e => {
                    state.materialRequestFilter = e.target.value || 'open';
                    renderMaterialRequestsInbox();
                });
                document.getElementById('pmo-material-request-refresh')?.addEventListener('click', async () => {
                    await loadPayload(false);
                    renderMaterialRequestsInbox();
                    toast('Material-Anfragen', 'Die Liste wurde aktualisiert.');
                });
                document.addEventListener('change', e => {
                    const statusSelect = e.target.closest('[data-pmo-status-select]');
                    if (statusSelect) {
                        e.preventDefault();
                        e.stopPropagation();
                        const nextStatus = normalizePlannerStatus(statusSelect.value || 'open');
                        if (nextStatus === 'done') {
                            openCompletionReportModal(statusSelect.dataset.itemId, nextStatus);
                            renderAll();
                        } else {
                            updateItemStatus(statusSelect.dataset.itemId, nextStatus);
                        }
                    }
                });

                document.getElementById('pmo-show-done')?.addEventListener('change', e => {
                    state.showDone = !!e.target.checked;
                    loadPayload(false);
                });

                document.addEventListener('input', e => {
                    if (e.target?.id === 'pmo-material-local-search') {
                        state.materialModal = { ...(state.materialModal || {}), localSearch: e.target.value || '' };
                        renderMaterialModal();
                        return;
                    }

                    if (e.target?.id === 'pmo-master-detail-search') {
                        state.masterDetailModal = { ...(state.masterDetailModal || {}), search: e.target.value || '' };
                        renderMasterDetailModal();
                    }
                });

                document.getElementById('pmo-date').value = state.date;
                document.getElementById('pmo-date').addEventListener('change', async e => { state.date = e.target.value; await loadPayload(false); });
                document.getElementById('pmo-mode').addEventListener('change', async e => { state.mode = e.target.value; await loadPayload(true); });
                document.getElementById('pmo-prev-date').addEventListener('click', async () => { const d = new Date(state.date); d.setDate(d.getDate() - 1); state.date = d.toISOString().slice(0, 10); document.getElementById('pmo-date').value = state.date; await loadPayload(false); });
                document.getElementById('pmo-next-date').addEventListener('click', async () => { const d = new Date(state.date); d.setDate(d.getDate() + 1); state.date = d.toISOString().slice(0, 10); document.getElementById('pmo-date').value = state.date; await loadPayload(false); });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') {
                        state.dependencyMode = false;
                        state.dependencySourceId = null;
                        document.getElementById('pmo-dependency-mode')?.classList.remove('is-active');
                        root.classList.remove('is-dependency-mode');
                        closeModal('pmo-master-detail-modal');
                        closeModal('pmo-material-modal');
                        closeJobDrawer();
                        renderGantt();
                    }
                });
                window.addEventListener('resize', () => window.requestAnimationFrame(renderDependencyLines));
                document.getElementById('pmo-gantt')?.addEventListener('scroll', () => window.requestAnimationFrame(renderDependencyLines));

                window.addEventListener('sa:planner-material-requested', async (event) => {
                    const payload = event?.detail || {};
                    const employeeName = payload.requested_by_employee_name
                        || payload.employee_name
                        || payload.requested_by?.full_name
                        || payload.requested_by?.name
                        || 'Ein Mitarbeiter';

                    const materialName = payload.material?.name
                        || payload.material?.article_name
                        || payload.name
                        || 'Material';

                    const qty = [payload.material?.quantity || payload.material?.qty || payload.quantity || '', payload.material?.unit || payload.unit || '']
                        .filter(v => String(v || '').trim() !== '')
                        .join(' ');

                    toast('Neue Materialanfrage', `${employeeName} hat ${materialName}${qty ? ` (${qty})` : ''} angefragt.`, 'warn');

                    try {
                        const oldTitle = document.title;
                        document.title = '🔔 Materialanfrage · ' + oldTitle.replace(/^🔔 Materialanfrage · /, '');
                        setTimeout(() => { document.title = oldTitle; }, 12000);
                    } catch (e) { }

                    try {
                        await loadPayload(false);
                        renderMaterialRequestsInbox();

                        if (state.tab !== 'material_requests') {
                            const count = materialRequestRows().filter(row => row.status === 'open').length;
                            const chip = document.getElementById('pmo-request-tab-count');
                            if (chip) {
                                chip.textContent = count;
                                chip.classList.toggle('pmo-hidden', count <= 0);
                            }
                        }

                        if (state.activeWorkKey) {
                            openJobDrawer(state.activeWorkKey);
                        }
                    } catch (error) {
                        console.warn('Material request realtime reload failed:', error);
                    }
                });

                redrawIcons();
                loadPayload(true).catch(err => {
                    toast('Fehler', err.message, 'bad');
                    document.getElementById('pmo-board').innerHTML = `<div class="pmo-empty">${esc(err.message)}</div>`;
                });
            })();
        </script>
    @endpush
@endonce