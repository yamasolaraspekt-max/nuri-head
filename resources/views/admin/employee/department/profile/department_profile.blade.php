@extends('admin.layouts.app')
@section('title', 'Abteilungsprofil')

@php
    $departmentStatus = $department->status === 'Published' ? 'Aktiv' : 'Inaktiv';
    $departmentStatusClass = $department->status === 'Published' ? 'is-active' : 'is-inactive';

    $leaderImage = $department->emp_image
        ? asset('images/employee/' . $department->emp_image)
        : asset('images/gender/male.png');

    $teamCount = isset($employees) ? $employees->count() : 0;
@endphp

@section('style')
    <style>
        :root {
            --dpf-bg: #f3f4f6;
            --dpf-card: #ffffff;
            --dpf-text: #1f2937;
            --dpf-heading: #111827;
            --dpf-muted: #6b7280;
            --dpf-border: #e5e7eb;

            --dpf-green: #93c21c;
            --dpf-green-hover: #7baa18;
            --dpf-green-soft: #f4fae7;

            --dpf-blue: #74b2d4;
            --dpf-blue-soft: #eff6ff;

            --dpf-success: #10b981;
            --dpf-success-soft: #ecfdf5;

            --dpf-warning: #f59e0b;
            --dpf-warning-soft: #fffbeb;

            --dpf-danger: #ef4444;
            --dpf-danger-soft: #fef2f2;

            --dpf-purple: #6366f1;
            --dpf-purple-soft: #eef2ff;

            --dpf-gray-soft: #f9fafb;

            --dpf-radius-sm: 10px;
            --dpf-radius: 16px;
            --dpf-radius-lg: 22px;

            --dpf-shadow-sm: 0 1px 2px rgba(15, 23, 42, .05);
            --dpf-shadow: 0 14px 36px rgba(15, 23, 42, .08);
            --dpf-shadow-lg: 0 24px 70px rgba(15, 23, 42, .14);

            --dpf-transition: all .2s ease;
        }

        .dpf-wrap {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--dpf-text);
        }

        .dpf-topbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 18px;
        }

        .dpf-page-kicker {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--dpf-blue);
            background: rgba(116, 178, 212, .12);
            border: 1px solid rgba(116, 178, 212, .2);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 8px;
        }

        .dpf-page-title {
            font-size: 28px;
            font-weight: 950;
            letter-spacing: -.035em;
            color: var(--dpf-heading);
            margin: 0;
            line-height: 1.1;
        }

        .dpf-page-subtitle {
            color: var(--dpf-muted);
            font-size: 14px;
            margin-top: 6px;
        }

        .dpf-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dpf-btn {
            border: none;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: var(--dpf-transition);
            white-space: nowrap;
        }

        .dpf-btn svg {
            width: 17px;
            height: 17px;
        }

        .dpf-btn-primary {
            background: var(--dpf-green);
            color: #fff;
            box-shadow: 0 10px 22px rgba(147, 194, 28, .22);
        }

        .dpf-btn-primary:hover {
            background: var(--dpf-green-hover);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .dpf-btn-soft {
            background: #fff;
            color: var(--dpf-text);
            border: 1px solid var(--dpf-border);
        }

        .dpf-btn-soft:hover {
            background: var(--dpf-gray-soft);
            color: var(--dpf-heading);
            text-decoration: none;
        }

        .dpf-btn-blue {
            background: var(--dpf-blue);
            color: #fff;
            box-shadow: 0 10px 22px rgba(116, 178, 212, .24);
        }

        .dpf-btn-blue:hover {
            background: #5f9fc5;
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .dpf-hero {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(116, 178, 212, .18), transparent 32%),
                radial-gradient(circle at bottom left, rgba(147, 194, 28, .16), transparent 34%),
                linear-gradient(135deg, #ffffff, #fbfdf7);
            border: 1px solid var(--dpf-border);
            border-radius: 26px;
            box-shadow: var(--dpf-shadow);
            margin-bottom: 18px;
        }

        .dpf-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(116, 178, 212, .06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(116, 178, 212, .06) 1px, transparent 1px);
            background-size: 34px 34px;
            pointer-events: none;
            opacity: .55;
        }

        .dpf-hero-inner {
            position: relative;
            padding: 24px;
        }

        .dpf-hero-main {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 18px;
            align-items: stretch;
        }

        @media (max-width: 1100px) {
            .dpf-hero-main {
                grid-template-columns: 1fr;
            }
        }

        .dpf-department-head {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            min-width: 0;
        }

        @media (max-width: 640px) {
            .dpf-department-head {
                flex-direction: column;
            }
        }

        .dpf-hero-mark {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--dpf-blue), var(--dpf-green));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 38px rgba(116, 178, 212, .25);
            flex: 0 0 auto;
        }

        .dpf-hero-mark svg {
            width: 34px;
            height: 34px;
        }

        .dpf-department-title-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 8px;
        }

        .dpf-department-title {
            font-size: 30px;
            font-weight: 950;
            color: var(--dpf-heading);
            margin: 0;
            letter-spacing: -.035em;
            line-height: 1.15;
        }

        .dpf-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 950;
            white-space: nowrap;
        }

        .dpf-status-pill::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: currentColor;
        }

        .dpf-status-pill.is-active {
            background: var(--dpf-success-soft);
            color: #047857;
        }

        .dpf-status-pill.is-inactive {
            background: var(--dpf-danger-soft);
            color: #b91c1c;
        }

        .dpf-description {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.7;
            max-width: 850px;
            margin: 0 0 14px;
        }

        .dpf-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dpf-meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #374151;
            background: rgba(255, 255, 255, .72);
            border: 1px solid rgba(229, 231, 235, .9);
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 800;
        }

        .dpf-meta-chip svg {
            width: 15px;
            height: 15px;
            color: var(--dpf-blue);
        }

        .dpf-leader-card {
            background: rgba(255, 255, 255, .84);
            border: 1px solid rgba(229, 231, 235, .95);
            backdrop-filter: blur(12px);
            border-radius: 22px;
            padding: 18px;
            box-shadow: var(--dpf-shadow-sm);
            height: 100%;
        }

        .dpf-leader-inner {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .dpf-avatar-lg {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 1px var(--dpf-border), 0 10px 20px rgba(15, 23, 42, .08);
            background: var(--dpf-gray-soft);
            flex: 0 0 auto;
        }

        .dpf-avatar-md {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px var(--dpf-border);
            background: var(--dpf-gray-soft);
            flex: 0 0 auto;
        }

        .dpf-avatar-sm {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px var(--dpf-border);
            background: var(--dpf-gray-soft);
        }

        .dpf-label {
            font-size: 11px;
            font-weight: 950;
            color: var(--dpf-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dpf-leader-name {
            color: var(--dpf-heading);
            font-size: 15px;
            font-weight: 950;
            line-height: 1.25;
            margin-top: 3px;
        }

        .dpf-online {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #047857;
            font-size: 12px;
            font-weight: 800;
            margin-top: 5px;
        }

        .dpf-online::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #34d399;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, .12);
        }

        .dpf-leader-actions {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .dpf-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        @media (max-width: 1200px) {
            .dpf-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .dpf-kpi-grid {
                grid-template-columns: 1fr;
            }
        }

        .dpf-kpi {
            background: #fff;
            border: 1px solid var(--dpf-border);
            border-radius: 20px;
            padding: 16px;
            box-shadow: var(--dpf-shadow-sm);
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .dpf-kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .dpf-kpi-icon svg {
            width: 23px;
            height: 23px;
        }

        .dpf-kpi-icon.projects {
            background: var(--dpf-blue-soft);
            color: var(--dpf-blue);
        }

        .dpf-kpi-icon.tickets {
            background: var(--dpf-warning-soft);
            color: #d97706;
        }

        .dpf-kpi-icon.team {
            background: var(--dpf-green-soft);
            color: #5f8512;
        }

        .dpf-kpi-icon.calendar {
            background: var(--dpf-purple-soft);
            color: var(--dpf-purple);
        }

        .dpf-kpi-label {
            font-size: 11px;
            font-weight: 950;
            color: var(--dpf-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dpf-kpi-value {
            font-size: 27px;
            font-weight: 950;
            color: var(--dpf-heading);
            line-height: 1.05;
            margin-top: 4px;
        }

        .dpf-kpi-sub {
            font-size: 12px;
            color: var(--dpf-muted);
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dpf-tabs-shell {
            position: sticky;
            top: 76px;
            z-index: 50;
            background: rgba(255, 255, 255, .92);
            border: 1px solid var(--dpf-border);
            border-radius: 20px;
            box-shadow: var(--dpf-shadow-sm);
            padding: 8px;
            margin-bottom: 16px;
            backdrop-filter: blur(14px);
        }

        .dpf-tabs-scroll {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .dpf-tab-btn {
            border: none;
            background: transparent;
            color: #374151;
            border-radius: 14px;
            padding: 10px 13px;
            font-size: 13px;
            font-weight: 950;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            cursor: pointer;
            transition: var(--dpf-transition);
        }

        .dpf-tab-btn svg {
            width: 17px;
            height: 17px;
        }

        .dpf-tab-btn:hover {
            background: var(--dpf-gray-soft);
        }

        .dpf-tab-btn.active {
            background: var(--dpf-green-soft);
            color: #4d6d11;
        }

        .dpf-tab-count {
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid var(--dpf-border);
            color: #374151;
            font-size: 11px;
            font-weight: 950;
        }

        .dpf-tab-tools {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
        }

        @media (max-width: 1000px) {
            .dpf-tab-tools {
                display: none;
            }
        }

        .dpf-input,
        .dpf-select {
            height: 38px;
            border: 1px solid var(--dpf-border);
            border-radius: 12px;
            background: #fff;
            color: var(--dpf-text);
            font-size: 13px;
            font-weight: 700;
            outline: none;
            transition: var(--dpf-transition);
        }

        .dpf-input {
            padding: 0 12px;
            min-width: 230px;
        }

        .dpf-select {
            padding: 0 34px 0 12px;
            min-width: 165px;
        }

        .dpf-input:focus,
        .dpf-select:focus {
            border-color: var(--dpf-green);
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .12);
        }

        .dpf-panel {
            background: #fff;
            border: 1px solid var(--dpf-border);
            border-radius: 22px;
            box-shadow: var(--dpf-shadow-sm);
            padding: 18px;
            margin-bottom: 16px;
        }

        .dpf-panel.d-none {
            display: none !important;
        }

        .dpf-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .dpf-section-title {
            color: var(--dpf-heading);
            font-size: 17px;
            font-weight: 950;
            margin: 0;
        }

        .dpf-section-sub {
            color: var(--dpf-muted);
            font-size: 12px;
            font-weight: 700;
            margin-top: 3px;
        }

        .dpf-filter-row {
            display: flex;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dpf-filter {
            min-width: 180px;
        }

        .dpf-filter label {
            display: block;
            color: var(--dpf-muted);
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 5px;
        }

        .dpf-card {
            background: #fff;
            border: 1px solid var(--dpf-border);
            border-radius: 18px;
            padding: 15px;
            box-shadow: var(--dpf-shadow-sm);
        }

        .dpf-card-muted {
            background: var(--dpf-gray-soft);
            border: 1px solid var(--dpf-border);
            border-radius: 16px;
            padding: 14px;
        }

        .dpf-table-wrap {
            border: 1px solid var(--dpf-border);
            border-radius: 18px;
            overflow: hidden;
        }

        .dpf-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .dpf-table thead th {
            background: var(--dpf-gray-soft);
            color: var(--dpf-muted);
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid var(--dpf-border);
            padding: 13px 14px;
            white-space: nowrap;
        }

        .dpf-table tbody td {
            padding: 14px;
            border-bottom: 1px solid var(--dpf-border);
            vertical-align: middle;
        }

        .dpf-table tbody tr:last-child td {
            border-bottom: none;
        }

        .dpf-table tbody tr:hover {
            background: #fbfdff;
        }

        .dpf-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 950;
            white-space: nowrap;
        }

        .dpf-pill.info {
            background: rgba(59, 130, 246, .11);
            color: #2563eb;
        }

        .dpf-pill.primary {
            background: rgba(99, 102, 241, .11);
            color: #4f46e5;
        }

        .dpf-pill.success {
            background: rgba(16, 185, 129, .11);
            color: #059669;
        }

        .dpf-pill.warning {
            background: rgba(245, 158, 11, .13);
            color: #b45309;
        }

        .dpf-pill.danger {
            background: rgba(239, 68, 68, .12);
            color: #b91c1c;
        }

        .dpf-pill.secondary {
            background: rgba(148, 163, 184, .14);
            color: #4b5563;
        }

        .dpf-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            display: inline-block;
            flex: 0 0 auto;
        }

        .dpf-progress {
            width: 100%;
            height: 7px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
        }

        .dpf-progress-bar {
            height: 100%;
            border-radius: 999px;
            background: var(--dpf-blue);
            transition: width .25s ease;
        }

        .dpf-ticket-card {
            border: 1px solid var(--dpf-border);
            border-radius: 18px;
            padding: 14px;
            background: #fff;
            box-shadow: var(--dpf-shadow-sm);
            margin-bottom: 10px;
        }

        .dpf-small-label {
            color: #9ca3af;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }

        .dpf-team-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        @media (max-width: 1200px) {
            .dpf-team-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .dpf-team-grid {
                grid-template-columns: 1fr;
            }
        }

        .dpf-team-card {
            background: #fff;
            border: 1px solid var(--dpf-border);
            border-radius: 20px;
            padding: 16px;
            box-shadow: var(--dpf-shadow-sm);
            transition: var(--dpf-transition);
        }

        .dpf-team-card:hover {
            box-shadow: var(--dpf-shadow);
            transform: translateY(-1px);
        }

        .dpf-team-top {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .dpf-team-name {
            color: var(--dpf-heading);
            font-size: 15px;
            font-weight: 950;
            line-height: 1.25;
        }

        .dpf-team-email {
            color: var(--dpf-muted);
            font-size: 12px;
            margin-top: 4px;
            word-break: break-word;
        }

        .dpf-team-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-top: 13px;
        }

        .dpf-team-metric {
            background: var(--dpf-gray-soft);
            border: 1px solid var(--dpf-border);
            border-radius: 14px;
            padding: 10px;
        }

        .dpf-team-metric strong {
            color: var(--dpf-heading);
            font-size: 16px;
            font-weight: 950;
            display: block;
        }

        .dpf-team-metric span {
            color: var(--dpf-muted);
            font-size: 11px;
            font-weight: 800;
        }

        .dpf-task-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 430px;
            gap: 14px;
        }

        @media (max-width: 1100px) {
            .dpf-task-grid {
                grid-template-columns: 1fr;
            }
        }

        .dpf-task-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .dpf-task-item {
            background: var(--dpf-gray-soft);
            border: 1px solid var(--dpf-border);
            border-radius: 16px;
            padding: 13px;
            margin-bottom: 10px;
        }

        .dpf-task-item:last-child {
            margin-bottom: 0;
        }

        .dpf-metric-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        @media (max-width: 520px) {
            .dpf-metric-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .dpf-mini-metric {
            background: var(--dpf-gray-soft);
            border: 1px solid var(--dpf-border);
            border-radius: 16px;
            padding: 12px;
            text-align: center;
        }

        .dpf-mini-metric strong {
            display: block;
            color: var(--dpf-heading);
            font-size: 24px;
            font-weight: 950;
            line-height: 1.05;
            margin-top: 7px;
        }

        .dpf-calendar-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 16px;
        }

        @media (max-width: 1100px) {
            .dpf-calendar-layout {
                grid-template-columns: 1fr;
            }
        }

        .dpf-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
        }

        .dpf-calendar-header {
            color: var(--dpf-muted);
            font-size: 12px;
            font-weight: 950;
            text-align: center;
            text-transform: uppercase;
            padding: 6px 0;
        }

        .dpf-day-cell {
            min-height: 58px;
            border: 1px solid var(--dpf-border);
            border-radius: 16px;
            background: #fff;
            cursor: pointer;
            padding: 8px;
            transition: var(--dpf-transition);
        }

        .dpf-day-cell:hover {
            border-color: rgba(116, 178, 212, .45);
            box-shadow: var(--dpf-shadow-sm);
        }

        .dpf-day-cell.is-empty {
            visibility: hidden;
        }

        .dpf-day-number {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dpf-heading);
            font-size: 12px;
            font-weight: 950;
        }

        .dpf-day-cell.is-today .dpf-day-number {
            background: var(--dpf-purple-soft);
            color: var(--dpf-purple);
        }

        .dpf-day-cell.has-events {
            background: linear-gradient(135deg, rgba(116, 178, 212, .09), rgba(147, 194, 28, .08));
            border-color: rgba(116, 178, 212, .24);
        }

        .dpf-day-event-count {
            margin-top: 8px;
            font-size: 10px;
            color: var(--dpf-blue);
            font-weight: 950;
        }

        .dpf-event-panel {
            background: var(--dpf-gray-soft);
            border: 1px solid var(--dpf-border);
            border-radius: 20px;
            padding: 14px;
            min-height: 280px;
        }

        .dpf-event-card {
            border: 1px solid var(--dpf-border);
            border-left: 4px solid var(--dpf-blue);
            border-radius: 16px;
            padding: 14px;
            background: #fff;
            box-shadow: var(--dpf-shadow-sm);
            margin-bottom: 10px;
        }

        .dpf-expense-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        @media (max-width: 800px) {
            .dpf-expense-grid {
                grid-template-columns: 1fr;
            }
        }

        .dpf-expense-card {
            border: 1px solid var(--dpf-border);
            border-radius: 20px;
            padding: 16px;
            background: #fff;
            box-shadow: var(--dpf-shadow-sm);
        }

        .dpf-expense-value {
            color: var(--dpf-heading);
            font-size: 26px;
            font-weight: 950;
            margin-top: 6px;
        }

        .dpf-empty {
            padding: 38px 18px;
            text-align: center;
            border: 1px dashed var(--dpf-border);
            border-radius: 18px;
            background: var(--dpf-gray-soft);
            color: var(--dpf-muted);
            font-size: 13px;
            font-weight: 800;
        }

        .dpf-empty svg {
            display: block;
            width: 32px;
            height: 32px;
            margin: 0 auto 8px;
            color: var(--dpf-blue);
        }

        @media (max-width: 767.98px) {
            .dpf-filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .dpf-filter {
                width: 100%;
            }

            .dpf-select,
            .dpf-input {
                width: 100%;
                min-width: 0;
            }

            .dpf-panel {
                padding: 14px;
                border-radius: 18px;
            }

            .dpf-hero-inner {
                padding: 18px;
            }

            .dpf-department-title {
                font-size: 24px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="dpf-wrap">

        <div class="dpf-topbar">
            <div>
                <div class="dpf-page-kicker">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 21h18M6 21V4h12v17M9 8h6M9 12h6M9 16h6" />
                    </svg>
                    Abteilung
                </div>

                <h1 class="dpf-page-title">Abteilungsprofil</h1>
                <div class="dpf-page-subtitle">Zentrale Übersicht für Projekte, Tickets, Team, Aufgaben, Kalender und
                    Kosten.</div>
            </div>

            <div class="dpf-actions">
                <a href="{{ url('department_view') }}" class="dpf-btn dpf-btn-soft">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Zurück
                </a>

                <button type="button" class="dpf-btn dpf-btn-blue" data-dpf-tab-jump="calendar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" />
                    </svg>
                    Kalender
                </button>
            </div>
        </div>

        <section class="dpf-hero">
            <div class="dpf-hero-inner">
                <div class="dpf-hero-main">
                    <div class="dpf-department-head">
                        <div class="dpf-hero-mark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 21h18M6 21V4h12v17M9 8h6M9 12h6M9 16h6" />
                            </svg>
                        </div>

                        <div style="min-width:0;">
                            <div class="dpf-department-title-row">
                                <h2 class="dpf-department-title">{{ $department->department_name }}</h2>
                                <span class="dpf-status-pill {{ $departmentStatusClass }}">{{ $departmentStatus }}</span>
                            </div>

                            <p class="dpf-description">
                                {{ $department->description ?: 'Keine Beschreibung hinterlegt.' }}
                            </p>

                            <div class="dpf-meta-row">
                                <span class="dpf-meta-chip">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s7-5.686 7-12a7 7 0 1 0-14 0c0 6.314 7 12 7 12z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    {{ $department->branch ?: 'Kein Standort' }}
                                </span>

                                <span class="dpf-meta-chip">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 7h16M4 12h16M4 17h16" />
                                    </svg>
                                    ID: {{ $department->id }}
                                </span>

                                <span class="dpf-meta-chip">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                    {{ $teamCount }} Teammitglieder
                                </span>
                            </div>
                        </div>
                    </div>

                    <aside class="dpf-leader-card">
                        <div class="dpf-leader-inner">
                            <img class="dpf-avatar-lg" src="{{ $leaderImage }}" alt="Abteilungsleiter"
                                onerror="this.src='{{ asset('images/gender/male.png') }}'">

                            <div style="min-width:0;">
                                <div class="dpf-label">Abteilungsleiter</div>
                                <div class="dpf-leader-name">
                                    {{ trim(($department->emp_name ?? '') . ' ' . ($department->emp_lastname ?? '')) ?: 'Nicht zugeordnet' }}
                                </div>
                                <div class="dpf-online">Verfügbar</div>
                            </div>
                        </div>

                        <div class="dpf-leader-actions">
                            <button type="button" class="dpf-btn dpf-btn-soft">Nachricht</button>
                            <button type="button" class="dpf-btn dpf-btn-soft">Termin planen</button>
                            <button type="button" class="dpf-btn dpf-btn-soft">Anrufen</button>
                        </div>
                    </aside>
                </div>

                <div class="dpf-kpi-grid">
                    <div class="dpf-kpi">
                        <div class="dpf-kpi-icon projects">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                        </div>
                        <div style="min-width:0;">
                            <div class="dpf-kpi-label">Aktive Projekte</div>
                            <div class="dpf-kpi-value">{{ $projectsCount ?? 0 }}</div>
                            <div class="dpf-kpi-sub">Aktuell in Bearbeitung</div>
                        </div>
                    </div>

                    <div class="dpf-kpi">
                        <div class="dpf-kpi-icon tickets">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 6h4M10 12h4M10 18h4" />
                                <path d="M4 4h16v16H4z" />
                            </svg>
                        </div>
                        <div style="min-width:0;">
                            <div class="dpf-kpi-label">Offene Tickets</div>
                            <div class="dpf-kpi-value" id="dpf-kpi-ticket-value">{{ $ticketsCount ?? 0 }}</div>
                            <div class="dpf-kpi-sub">SLA-Risiko: {{ $slaRiskCount ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="dpf-kpi">
                        <div class="dpf-kpi-icon team">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            </svg>
                        </div>
                        <div style="min-width:0;">
                            <div class="dpf-kpi-label">Teammitglieder</div>
                            <div class="dpf-kpi-value" id="dpf-kpi-team-value">{{ $teamCount }}</div>
                            <div class="dpf-kpi-sub">Mitarbeiter im Team</div>
                        </div>
                    </div>

                    <div class="dpf-kpi">
                        <div class="dpf-kpi-icon calendar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path
                                    d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" />
                            </svg>
                        </div>
                        <div style="min-width:0;">
                            <div class="dpf-kpi-label">Termine</div>
                            <div class="dpf-kpi-value">{{ $appointmentsCount ?? 0 }}</div>
                            <div class="dpf-kpi-sub">Kommende Termine</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <nav class="dpf-tabs-shell">
            <div class="dpf-tabs-scroll" id="dpf-tab-nav">
                <button type="button" class="dpf-tab-btn active" data-tab="projects">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                    Projekte
                    <span class="dpf-tab-count" id="dpf-count-projects">{{ $projectsCount ?? 0 }}</span>
                </button>

                <button type="button" class="dpf-tab-btn" data-tab="tickets">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v16H4z" />
                        <path d="M9 9h6M9 15h6" />
                    </svg>
                    Tickets
                    <span class="dpf-tab-count" id="dpf-count-tickets">{{ $ticketsCount ?? 0 }}</span>
                </button>

                <button type="button" class="dpf-tab-btn" data-tab="team">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                    </svg>
                    Team
                    <span class="dpf-tab-count" id="dpf-count-team">{{ $teamCount }}</span>
                </button>

                <button type="button" class="dpf-tab-btn" data-tab="tasks">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                    Aufgaben
                    <span class="dpf-tab-count" id="dpf-count-tasks">0</span>
                </button>

                <button type="button" class="dpf-tab-btn" data-tab="calendar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" />
                    </svg>
                    Kalender
                    <span class="dpf-tab-count" id="dpf-count-calendar">{{ $appointmentsCount ?? 0 }}</span>
                </button>

                <button type="button" class="dpf-tab-btn" data-tab="expenses">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                    Kosten
                    <span class="dpf-tab-count" id="dpf-count-expenses">0</span>
                </button>

                <div class="dpf-tab-tools">
                    <input type="text" class="dpf-input" id="dpf-global-search" placeholder="Globale Suche…">
                    <select class="dpf-select" id="dpf-global-filter">
                        <option value="">Alle</option>
                        <option value="active">Aktiv</option>
                        <option value="archived">Archiviert</option>
                    </select>
                </div>
            </div>
        </nav>

        <section id="dpf-tab-projects" class="dpf-panel dpf-tab-pane">
            <div class="dpf-panel-head">
                <div>
                    <h3 class="dpf-section-title">Projekte</h3>
                    <div class="dpf-section-sub">Projektübersicht der Abteilung.</div>
                </div>
            </div>

            <div class="dpf-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                Projekte können hier später geladen oder direkt aus Ihrem Projektmodul eingebunden werden.
            </div>
        </section>

        <section id="dpf-tab-tickets" class="dpf-panel dpf-tab-pane d-none">
            <div class="dpf-panel-head">
                <div>
                    <h3 class="dpf-section-title">Tickets</h3>
                    <div class="dpf-section-sub">Gefilterte Tickets mit Priorität, Status und SLA-Fortschritt.</div>
                </div>

                <div class="dpf-filter-row">
                    <div class="dpf-filter">
                        <label for="dpf-ticket-priority">Priorität</label>
                        <select id="dpf-ticket-priority" class="dpf-select">
                            <option value="">Alle Prioritäten</option>
                            <option value="Critical">Kritisch</option>
                            <option value="High">Hoch</option>
                            <option value="Medium">Mittel</option>
                            <option value="Low">Niedrig</option>
                        </select>
                    </div>

                    <div class="dpf-filter">
                        <label for="dpf-ticket-status">Status</label>
                        <select id="dpf-ticket-status" class="dpf-select">
                            <option value="">Alle Status</option>
                            <option value="Open">Offen</option>
                            <option value="In Progress">In Bearbeitung</option>
                            <option value="Waiting">Warten</option>
                            <option value="Resolved">Gelöst</option>
                        </select>
                    </div>

                    <div class="dpf-filter">
                        <label for="dpf-ticket-sort">Sortierung</label>
                        <select id="dpf-ticket-sort" class="dpf-select">
                            <option value="sla">Nach SLA</option>
                            <option value="priority">Nach Priorität</option>
                            <option value="updated">Nach Aktualisierung</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="dpf-table-wrap d-none d-md-block">
                <table class="dpf-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Betreff</th>
                            <th>Anfragender</th>
                            <th>Priorität</th>
                            <th>Status</th>
                            <th>SLA</th>
                            <th class="text-right">Aktion</th>
                        </tr>
                    </thead>
                    <tbody id="dpf-tickets-body">
                        <tr>
                            <td colspan="7">
                                <div class="dpf-empty">Tickets werden geladen...</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-block d-md-none" id="dpf-tickets-cards">
                <div class="dpf-empty">Tickets werden geladen...</div>
            </div>
        </section>

        <section id="dpf-tab-team" class="dpf-panel dpf-tab-pane d-none">
            <div class="dpf-panel-head">
                <div>
                    <h3 class="dpf-section-title">Team</h3>
                    <div class="dpf-section-sub"><span id="dpf-team-count-label">{{ $teamCount }}</span> Teammitglieder in
                        dieser Abteilung.</div>
                </div>
            </div>

            <div class="dpf-team-grid" id="dpf-team-grid">
                <div class="dpf-empty">Team wird geladen...</div>
            </div>
        </section>

        <section id="dpf-tab-tasks" class="dpf-panel dpf-tab-pane d-none">
            <div class="dpf-panel-head">
                <div>
                    <h3 class="dpf-section-title">Aufgaben</h3>
                    <div class="dpf-section-sub">Aufgabenübersicht inklusive Sprint-Metriken.</div>
                </div>

                <div class="dpf-filter-row">
                    <div class="dpf-filter">
                        <label for="dpf-task-filter">Status</label>
                        <select id="dpf-task-filter" class="dpf-select">
                            <option value="">Alle</option>
                            <option value="open">Offen</option>
                            <option value="in_progress">In Bearbeitung</option>
                            <option value="completed">Erledigt</option>
                            <option value="rejected">Abgelehnt</option>
                            <option value="junk">Junk</option>
                        </select>
                    </div>

                    <div class="dpf-filter">
                        <label for="dpf-task-sort">Sortierung</label>
                        <select id="dpf-task-sort" class="dpf-select">
                            <option value="due">Nach Fälligkeit</option>
                            <option value="priority">Nach Priorität</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="dpf-task-grid">
                <div class="dpf-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="dpf-label">Meine Aufgaben</div>
                            <h4 class="dpf-section-title mt-1">Aufgabenliste</h4>
                        </div>
                        <span class="dpf-pill secondary"><span id="dpf-task-count">0</span> Einträge</span>
                    </div>

                    <ul class="dpf-task-list" id="dpf-task-list">
                        <li class="dpf-empty">Aufgaben werden geladen...</li>
                    </ul>
                </div>

                <div class="dpf-card">
                    <div class="dpf-label">Sprint-Übersicht</div>
                    <h4 class="dpf-section-title mt-1 mb-3">Metriken</h4>

                    <div class="dpf-metric-grid">
                        <div class="dpf-mini-metric">
                            <span class="dpf-pill info">Offen</span>
                            <strong id="dpf-metric-open">0</strong>
                        </div>

                        <div class="dpf-mini-metric">
                            <span class="dpf-pill primary">In Arbeit</span>
                            <strong id="dpf-metric-inprogress">0</strong>
                        </div>

                        <div class="dpf-mini-metric">
                            <span class="dpf-pill success">Erledigt</span>
                            <strong id="dpf-metric-completed">0</strong>
                        </div>

                        <div class="dpf-mini-metric">
                            <span class="dpf-pill warning">Abgelehnt</span>
                            <strong id="dpf-metric-rejected">0</strong>
                        </div>

                        <div class="dpf-mini-metric">
                            <span class="dpf-pill danger">Junk</span>
                            <strong id="dpf-metric-junk">0</strong>
                        </div>

                        <div class="dpf-mini-metric">
                            <span class="dpf-pill secondary">Sonstige</span>
                            <strong id="dpf-metric-other">0</strong>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="dpf-label">Burndown</span>
                            <span class="dpf-label" id="dpf-burndown-label">0%</span>
                        </div>
                        <div class="dpf-progress">
                            <div id="dpf-burndown-bar" class="dpf-progress-bar" style="width:0%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="dpf-tab-calendar" class="dpf-panel dpf-tab-pane d-none">
            <div class="dpf-panel-head">
                <div>
                    <h3 class="dpf-section-title">Kalender</h3>
                    <div class="dpf-section-sub" id="calendarTitle">—</div>
                </div>

                <div class="dpf-actions">
                    <button id="prevMonth" type="button" class="dpf-btn dpf-btn-soft">‹ Vorheriger Monat</button>
                    <button id="nextMonth" type="button" class="dpf-btn dpf-btn-soft">Nächster Monat ›</button>
                </div>
            </div>

            <div class="dpf-calendar-layout">
                <div>
                    <div id="calendar" class="dpf-calendar-grid"></div>
                </div>

                <aside class="dpf-event-panel" id="dayEventsPanel">
                    <div class="dpf-label">Ausgewählter Tag</div>
                    <h4 class="dpf-section-title mt-1" id="selectedDate">Kein Tag ausgewählt</h4>
                    <div id="eventCards" class="mt-3">
                        <div class="dpf-empty">Wählen Sie einen Tag im Kalender aus.</div>
                    </div>
                </aside>
            </div>
        </section>

        <section id="dpf-tab-expenses" class="dpf-panel dpf-tab-pane d-none">
            <div class="dpf-panel-head">
                <div>
                    <h3 class="dpf-section-title">Abteilungskosten</h3>
                    <div class="dpf-section-sub">Monatliche, quartalsweise und jährliche Kostenübersicht.</div>
                </div>
            </div>

            <div class="dpf-expense-grid">
                <div class="dpf-expense-card">
                    <div class="dpf-label">Monatlich</div>
                    <div class="dpf-expense-value" id="dpf-exp-monthly">0 €</div>
                </div>

                <div class="dpf-expense-card">
                    <div class="dpf-label">Quartal</div>
                    <div class="dpf-expense-value" id="dpf-exp-quarterly">0 €</div>
                </div>

                <div class="dpf-expense-card">
                    <div class="dpf-label">Jährlich</div>
                    <div class="dpf-expense-value" id="dpf-exp-yearly">0 €</div>
                </div>
            </div>

            <div class="dpf-card">
                <div class="d-flex justify-content-between mb-2">
                    <span class="dpf-label">Verteilung Monat / Jahr</span>
                    <span class="dpf-label" id="dpf-exp-dist-label">0%</span>
                </div>

                <div class="dpf-progress mb-4">
                    <div id="dpf-exp-dist-bar" class="dpf-progress-bar" style="width:0%;"></div>
                </div>

                <canvas id="expenseChart" height="140"></canvas>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        (function () {
            'use strict';

            const departmentId = @json($department->id);

            const routes = {
                tickets: @json(url('get/department/ticket')) + '/' + departmentId,
                team: @json(route('department.profile.json', ['id' => $department->id])),
                tasks: @json(url('department')) + '/' + departmentId + '/tasks/json',
                expenses: @json(url('department')) + '/' + departmentId + '/expense/json',
                calendar: @json(route('department.calendar', ':id')).replace(':id', departmentId),
                employeeImageBase: @json(asset('images/employee')),
                defaultAvatar: @json(asset('images/gender/male.png')),
                appointmentBase: @json(url('appointment_details')),
                problemBase: @json(url('problem/profile')),
            };

            const state = {
                tickets: [],
                team: [],
                tasks: [],
                expenses: { monthly: 0, quarterly: 0, yearly: 0 },
                expenseChart: null,
                events: [],
                currentDate: new Date(),
            };

            const dom = {
                ticketBody: document.getElementById('dpf-tickets-body'),
                ticketCards: document.getElementById('dpf-tickets-cards'),
                teamGrid: document.getElementById('dpf-team-grid'),
                taskList: document.getElementById('dpf-task-list'),
                calendar: document.getElementById('calendar'),
                calendarTitle: document.getElementById('calendarTitle'),
                dayEventsPanel: document.getElementById('dayEventsPanel'),
                selectedDate: document.getElementById('selectedDate'),
                eventCards: document.getElementById('eventCards'),
            };

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function toNumber(value) {
                const number = Number(value ?? 0);
                return Number.isFinite(number) ? number : 0;
            }

            function formatMoney(value) {
                return toNumber(value).toLocaleString('de-DE') + ' €';
            }

            function formatDateKey(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');

                return `${y}-${m}-${d}`;
            }

            function imageUrl(filename) {
                if (!filename) return routes.defaultAvatar;
                if (String(filename).startsWith('http')) return filename;
                return routes.employeeImageBase + '/' + filename;
            }

            function emptyHtml(text, icon = 'M3 7h18M3 12h18M3 17h18') {
                return `
                <div class="dpf-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="${icon}"/>
                    </svg>
                    ${escapeHtml(text)}
                </div>
            `;
            }

            function priorityBadgeClass(priority) {
                const p = String(priority || '').toLowerCase();

                if (p === 'critical') return 'dpf-pill danger';
                if (p === 'high') return 'dpf-pill warning';
                if (p === 'medium') return 'dpf-pill primary';

                return 'dpf-pill secondary';
            }

            function priorityDotStyle(priority) {
                const p = String(priority || '').toLowerCase();

                if (p === 'critical') return 'background:#ef4444;';
                if (p === 'high') return 'background:#f59e0b;';
                if (p === 'medium') return 'background:#6366f1;';

                return 'background:#9ca3af;';
            }

            function statusBadgeClass(status) {
                const s = String(status || '').toLowerCase().trim();

                if (['open', 'new', 'offen', 'in_progress', 'in progress', 'progress', 'in bearbeitung'].includes(s)) {
                    return 'dpf-pill info';
                }

                if (['review', 'pending', 'waiting', 'warten'].includes(s)) {
                    return 'dpf-pill warning';
                }

                if (['done', 'completed', 'resolved', 'erledigt', 'gelöst'].includes(s)) {
                    return 'dpf-pill success';
                }

                if (['blocked', 'cancel', 'cancelled', 'rejected', 'junk', 'abgelehnt'].includes(s)) {
                    return 'dpf-pill danger';
                }

                return 'dpf-pill secondary';
            }

            function taskStatusLabel(status) {
                const s = String(status || '').toLowerCase();

                const labels = {
                    open: 'Offen',
                    in_progress: 'In Bearbeitung',
                    completed: 'Erledigt',
                    rejected: 'Abgelehnt',
                    junk: 'Junk',
                    other: 'Sonstige',
                };

                return labels[s] || 'Sonstige';
            }

            async function fetchJson(url) {
                const response = await fetch(url, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                return response.json();
            }

            function setCount(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            }

            function initTabs() {
                document.querySelectorAll('.dpf-tab-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const tab = this.dataset.tab;
                        activateTab(tab);
                    });
                });

                document.querySelectorAll('[data-dpf-tab-jump]').forEach(button => {
                    button.addEventListener('click', function () {
                        activateTab(this.dataset.dpfTabJump);
                    });
                });
            }

            function activateTab(tab) {
                if (!tab) return;

                document.querySelectorAll('.dpf-tab-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.tab === tab);
                });

                document.querySelectorAll('.dpf-tab-pane').forEach(panel => {
                    panel.classList.add('d-none');
                });

                const target = document.getElementById('dpf-tab-' + tab);
                if (target) {
                    target.classList.remove('d-none');
                }
            }

            async function loadTickets() {
                try {
                    const res = await fetchJson(routes.tickets);

                    const tickets = Array.isArray(res.tickets) ? res.tickets : [];

                    state.tickets = tickets.map(t => {
                        const employees = Array.isArray(t.dept_employees) ? t.dept_employees : [];
                        const firstEmployee = employees[0] || {};

                        return {
                            id: t.id,
                            code: t.ticket_no ? '#' + t.ticket_no : '#' + t.id,
                            subject: t.title || '(Kein Titel)',
                            product: t.product || '',
                            priority: t.priority || 'Medium',
                            status: t.status || 'Open',
                            sla: t.sla || '—',
                            slaProgress: Math.min(Math.max(toNumber(t.sla_progress), 0), 100),
                            deptCount: t.dept_employee_count || employees.length || 0,
                            requester: {
                                name: firstEmployee.name || 'Unzugeordnet',
                                avatar: imageUrl(firstEmployee.image || null),
                            },
                            updated: t.updated_at || t.created_at || new Date().toISOString(),
                        };
                    });

                    setCount('dpf-count-tickets', state.tickets.length);
                    setCount('dpf-kpi-ticket-value', state.tickets.length);

                    renderTickets();
                } catch (error) {
                    console.error('Tickets konnten nicht geladen werden:', error);

                    if (dom.ticketBody) {
                        dom.ticketBody.innerHTML = `<tr><td colspan="7">${emptyHtml('Tickets konnten nicht geladen werden.')}</td></tr>`;
                    }

                    if (dom.ticketCards) {
                        dom.ticketCards.innerHTML = emptyHtml('Tickets konnten nicht geladen werden.');
                    }
                }
            }

            function renderTickets() {
                const priorityFilter = document.getElementById('dpf-ticket-priority')?.value || '';
                const statusFilter = document.getElementById('dpf-ticket-status')?.value || '';
                const sortMode = document.getElementById('dpf-ticket-sort')?.value || 'sla';

                let list = [...state.tickets];

                if (priorityFilter) {
                    list = list.filter(t => String(t.priority) === priorityFilter);
                }

                if (statusFilter) {
                    list = list.filter(t => String(t.status) === statusFilter);
                }

                if (sortMode === 'priority') {
                    const order = { Critical: 0, High: 1, Medium: 2, Low: 3 };
                    list.sort((a, b) => (order[a.priority] ?? 9) - (order[b.priority] ?? 9));
                } else if (sortMode === 'updated') {
                    list.sort((a, b) => new Date(b.updated) - new Date(a.updated));
                } else {
                    list.sort((a, b) => b.slaProgress - a.slaProgress);
                }

                if (!list.length) {
                    if (dom.ticketBody) {
                        dom.ticketBody.innerHTML = `<tr><td colspan="7">${emptyHtml('Keine Tickets gefunden.')}</td></tr>`;
                    }

                    if (dom.ticketCards) {
                        dom.ticketCards.innerHTML = emptyHtml('Keine Tickets gefunden.');
                    }

                    return;
                }

                if (dom.ticketBody) {
                    dom.ticketBody.innerHTML = list.map(t => `
                    <tr>
                        <td><strong class="text-muted">${escapeHtml(t.code)}</strong></td>

                        <td>
                            <div class="font-weight-bold text-dark">${escapeHtml(t.subject)}</div>
                            <div class="small text-muted">${escapeHtml(t.product || '—')}</div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${escapeHtml(t.requester.avatar)}" class="dpf-avatar-sm mr-2" alt="" onerror="this.src='${routes.defaultAvatar}'">
                                <span>${escapeHtml(t.requester.name)}</span>
                            </div>
                        </td>

                        <td>
                            <span class="${priorityBadgeClass(t.priority)}">
                                <span class="dpf-dot" style="${priorityDotStyle(t.priority)}"></span>
                                ${escapeHtml(t.priority)}
                            </span>
                        </td>

                        <td>
                            <span class="${statusBadgeClass(t.status)}">${escapeHtml(t.status)}</span>
                        </td>

                        <td style="min-width:170px;">
                            <div class="d-flex align-items-center">
                                <div class="dpf-progress mr-2">
                                    <div class="dpf-progress-bar" style="width:${t.slaProgress}%;"></div>
                                </div>
                                <span class="small text-muted">${escapeHtml(t.sla)}</span>
                            </div>
                        </td>

                        <td class="text-right">
                            <a href="${routes.problemBase}/${encodeURIComponent(t.id)}" class="dpf-btn dpf-btn-soft" style="padding:7px 10px;">
                                Details
                            </a>
                        </td>
                    </tr>
                `).join('');
                }

                if (dom.ticketCards) {
                    dom.ticketCards.innerHTML = list.map(t => `
                    <article class="dpf-ticket-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong class="text-muted">${escapeHtml(t.code)}</strong>
                            <a href="${routes.problemBase}/${encodeURIComponent(t.id)}" class="dpf-btn dpf-btn-soft" style="padding:7px 10px;">Details</a>
                        </div>

                        <div class="mb-2">
                            <div class="dpf-small-label">Betreff</div>
                            <div class="font-weight-bold text-dark">${escapeHtml(t.subject)}</div>
                            <div class="small text-muted">${escapeHtml(t.product || '—')}</div>
                        </div>

                        <div class="mb-2">
                            <div class="dpf-small-label">Anfragender</div>
                            <div class="d-flex align-items-center">
                                <img src="${escapeHtml(t.requester.avatar)}" class="dpf-avatar-sm mr-2" alt="" onerror="this.src='${routes.defaultAvatar}'">
                                <span>${escapeHtml(t.requester.name)}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between mb-2">
                            <span class="${priorityBadgeClass(t.priority)}">
                                <span class="dpf-dot" style="${priorityDotStyle(t.priority)}"></span>
                                ${escapeHtml(t.priority)}
                            </span>

                            <span class="${statusBadgeClass(t.status)}">${escapeHtml(t.status)}</span>
                        </div>

                        <div>
                            <div class="dpf-small-label">SLA</div>
                            <div class="d-flex align-items-center">
                                <div class="dpf-progress mr-2">
                                    <div class="dpf-progress-bar" style="width:${t.slaProgress}%;"></div>
                                </div>
                                <span class="small text-muted">${escapeHtml(t.sla)}</span>
                            </div>
                        </div>
                    </article>
                `).join('');
                }
            }

            async function loadTeam() {
                try {
                    const res = await fetchJson(routes.team);
                    const employees = Array.isArray(res.employees) ? res.employees : [];

                    state.team = employees.map(e => ({
                        id: e.id,
                        name: `${e.name || ''} ${e.lastname || ''}`.trim() || 'Unbekannt',
                        role: e.position || '—',
                        status: String(e.status || '').toLowerCase() === 'active' ? 'online' : 'offline',
                        email: e.email || '',
                        avatar: imageUrl(e.image || null),
                        departmentCount: e.department_count || 0,
                        positionCount: e.position_count || 0,
                        leaveDays: e.leave_days || 0,
                        sickDays: e.sick_days || 0,
                        recurringRules: e.recurring_rules || 0,
                        recurringWeeklyDays: e.recurring_weekly_days || 0,
                    }));

                    setCount('dpf-count-team', state.team.length);
                    setCount('dpf-team-count-label', state.team.length);
                    setCount('dpf-kpi-team-value', state.team.length);

                    renderTeam();
                } catch (error) {
                    console.error('Team konnte nicht geladen werden:', error);

                    if (dom.teamGrid) {
                        dom.teamGrid.innerHTML = emptyHtml('Team konnte nicht geladen werden.');
                    }
                }
            }

            function renderTeam() {
                if (!dom.teamGrid) return;

                if (!state.team.length) {
                    dom.teamGrid.innerHTML = emptyHtml('Keine Teammitglieder gefunden.');
                    return;
                }

                dom.teamGrid.innerHTML = state.team.map(member => {
                    const statusText = member.status === 'online' ? 'Online' : 'Offline';
                    const statusColor = member.status === 'online' ? '#34d399' : '#9ca3af';

                    const recurringText = member.recurringRules > 0
                        ? `${member.recurringWeeklyDays} Tage/Woche`
                        : 'Keine Regeln';

                    return `
                    <article class="dpf-team-card">
                        <div class="dpf-team-top">
                            <img src="${escapeHtml(member.avatar)}" class="dpf-avatar-md" alt="${escapeHtml(member.name)}" onerror="this.src='${routes.defaultAvatar}'">

                            <div style="min-width:0;flex:1;">
                                <div class="d-flex align-items-center flex-wrap" style="gap:7px;">
                                    <div class="dpf-team-name">${escapeHtml(member.name)}</div>
                                    <span class="dpf-pill secondary">${escapeHtml(member.role)}</span>
                                </div>

                                <div class="dpf-team-email">${escapeHtml(member.email || 'Keine E-Mail')}</div>

                                <div class="small mt-2 d-flex align-items-center">
                                    <span class="dpf-dot mr-1" style="background:${statusColor};"></span>
                                    <strong>${statusText}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="dpf-team-metrics">
                            <div class="dpf-team-metric">
                                <strong>${member.departmentCount}</strong>
                                <span>Abteilungen</span>
                            </div>

                            <div class="dpf-team-metric">
                                <strong>${member.positionCount}</strong>
                                <span>Positionen</span>
                            </div>

                            <div class="dpf-team-metric">
                                <strong>${member.leaveDays}</strong>
                                <span>Urlaubstage</span>
                            </div>

                            <div class="dpf-team-metric">
                                <strong>${member.sickDays}</strong>
                                <span>Kranktage</span>
                            </div>
                        </div>

                        <div class="mt-3 small text-muted">
                            Wiederkehrend: <strong>${escapeHtml(recurringText)}</strong>
                        </div>

                        <div class="mt-3">
                            <a href="/employee_profile/${encodeURIComponent(member.id)}" class="dpf-btn dpf-btn-soft">
                                Profil öffnen
                            </a>
                        </div>
                    </article>
                `;
                }).join('');
            }

            async function loadTasks() {
                try {
                    const res = await fetchJson(routes.tasks);
                    const tasks = Array.isArray(res.tasks) ? res.tasks : [];

                    state.tasks = tasks.map(t => {
                        const raw = String(t.status || '').toLowerCase().trim();
                        let status = 'other';

                        if (['open', 'offen', 'new'].includes(raw)) status = 'open';
                        else if (['in progress', 'in_progress', 'progress', 'in bearbeitung'].includes(raw)) status = 'in_progress';
                        else if (['done', 'completed', 'erledigt'].includes(raw)) status = 'completed';
                        else if (['rejected', 'reject', 'abgelehnt'].includes(raw)) status = 'rejected';
                        else if (['junk', 'spam'].includes(raw)) status = 'junk';

                        return {
                            id: t.id,
                            title: t.title || 'Unbekannte Aufgabe',
                            status,
                            priority: t.priority || 'Medium',
                            start: t.start || '',
                            due: t.due || '',
                            assignees: Array.isArray(t.assignees) ? t.assignees : [],
                        };
                    });

                    setCount('dpf-count-tasks', state.tasks.length);
                    renderTasks();
                } catch (error) {
                    console.error('Aufgaben konnten nicht geladen werden:', error);

                    if (dom.taskList) {
                        dom.taskList.innerHTML = `<li>${emptyHtml('Aufgaben konnten nicht geladen werden.')}</li>`;
                    }
                }
            }

            function renderTasks() {
                if (!dom.taskList) return;

                const statusFilter = document.getElementById('dpf-task-filter')?.value || '';
                const sortMode = document.getElementById('dpf-task-sort')?.value || 'due';

                let list = [...state.tasks];

                if (statusFilter) {
                    list = list.filter(task => task.status === statusFilter);
                }

                if (sortMode === 'priority') {
                    const order = { Critical: 0, High: 1, Medium: 2, Low: 3 };
                    list.sort((a, b) => (order[a.priority] ?? 9) - (order[b.priority] ?? 9));
                } else {
                    list.sort((a, b) => new Date(a.due || '2999-12-31') - new Date(b.due || '2999-12-31'));
                }

                const all = state.tasks;
                const open = all.filter(t => t.status === 'open').length;
                const inProgress = all.filter(t => t.status === 'in_progress').length;
                const completed = all.filter(t => t.status === 'completed').length;
                const rejected = all.filter(t => t.status === 'rejected').length;
                const junk = all.filter(t => t.status === 'junk').length;
                const other = all.filter(t => !['open', 'in_progress', 'completed', 'rejected', 'junk'].includes(t.status)).length;
                const total = Math.max(all.length, 1);
                const burndown = Math.round((completed / total) * 100);

                setCount('dpf-task-count', list.length);
                setCount('dpf-metric-open', open);
                setCount('dpf-metric-inprogress', inProgress);
                setCount('dpf-metric-completed', completed);
                setCount('dpf-metric-rejected', rejected);
                setCount('dpf-metric-junk', junk);
                setCount('dpf-metric-other', other);
                setCount('dpf-burndown-label', burndown + '%');

                const burndownBar = document.getElementById('dpf-burndown-bar');
                if (burndownBar) burndownBar.style.width = burndown + '%';

                if (!list.length) {
                    dom.taskList.innerHTML = `<li>${emptyHtml('Keine Aufgaben gefunden.')}</li>`;
                    return;
                }

                dom.taskList.innerHTML = list.map(task => {
                    const assignees = task.assignees.map(a => `
                    <img src="${escapeHtml(a.avatar || routes.defaultAvatar)}"
                         class="dpf-avatar-sm ml-1"
                         title="${escapeHtml(a.name || '')}"
                         alt=""
                         onerror="this.src='${routes.defaultAvatar}'">
                `).join('');

                    return `
                    <li class="dpf-task-item">
                        <div class="d-flex justify-content-between align-items-start" style="gap:10px;">
                            <div style="min-width:0;">
                                <div class="font-weight-bold text-dark">${escapeHtml(task.title)}</div>
                                <div class="mt-2">
                                    <span class="${statusBadgeClass(task.status)}">${taskStatusLabel(task.status)}</span>
                                </div>
                            </div>

                            <div class="small text-muted text-right">${escapeHtml(task.due || '—')}</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="${priorityBadgeClass(task.priority)}">
                                <span class="dpf-dot" style="${priorityDotStyle(task.priority)}"></span>
                                ${escapeHtml(task.priority || '—')}
                            </span>

                            <div class="d-flex">${assignees}</div>
                        </div>
                    </li>
                `;
                }).join('');
            }

            async function loadExpenses() {
                try {
                    const res = await fetchJson(routes.expenses);
                    const exp = res.expenses && typeof res.expenses === 'object' ? res.expenses : res;

                    const monthly = toNumber(exp.monthly ?? exp.month);
                    const quarterly = toNumber(exp.quarterly ?? exp.quarter);
                    const yearly = toNumber(exp.yearly ?? exp.year);

                    state.expenses = { monthly, quarterly, yearly };

                    setCount('dpf-exp-monthly', formatMoney(monthly));
                    setCount('dpf-exp-quarterly', formatMoney(quarterly));
                    setCount('dpf-exp-yearly', formatMoney(yearly));
                    setCount('dpf-count-expenses', yearly > 0 || monthly > 0 || quarterly > 0 ? 1 : 0);

                    const distribution = yearly > 0 ? Math.min((monthly / yearly) * 100, 100) : 0;

                    const distBar = document.getElementById('dpf-exp-dist-bar');
                    const distLabel = document.getElementById('dpf-exp-dist-label');

                    if (distBar) distBar.style.width = distribution.toFixed(1) + '%';
                    if (distLabel) distLabel.textContent = distribution.toFixed(1) + '%';

                    renderExpenseChart(monthly, quarterly, yearly);
                } catch (error) {
                    console.error('Kosten konnten nicht geladen werden:', error);
                }
            }

            function renderExpenseChart(monthly, quarterly, yearly) {
                const canvas = document.getElementById('expenseChart');
                if (!canvas || !window.Chart) return;

                if (state.expenseChart) {
                    state.expenseChart.destroy();
                }

                state.expenseChart = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: ['Monat', 'Quartal', 'Jahr'],
                        datasets: [{
                            label: 'Kosten (€)',
                            data: [monthly, quarterly, yearly],
                            backgroundColor: ['#74b2d4', '#c0d8ea', '#93c21c'],
                            borderRadius: 10,
                            maxBarThickness: 52,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => formatMoney(ctx.raw),
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => Number(value).toLocaleString('de-DE') + ' €',
                                },
                                grid: {
                                    color: 'rgba(192,216,234,.4)',
                                },
                            },
                            x: {
                                grid: { display: false },
                            },
                        },
                    },
                });
            }

            async function loadCalendar() {
                try {
                    const res = await fetchJson(routes.calendar);
                    state.events = Array.isArray(res.data) ? res.data : [];
                    renderCalendar();
                } catch (error) {
                    console.error('Kalender konnte nicht geladen werden:', error);

                    if (dom.calendar) {
                        dom.calendar.innerHTML = emptyHtml('Kalender konnte nicht geladen werden.');
                    }
                }
            }

            function renderCalendar() {
                if (!dom.calendar) return;

                const date = state.currentDate;
                const year = date.getFullYear();
                const month = date.getMonth();
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                if (dom.calendarTitle) {
                    dom.calendarTitle.textContent = date.toLocaleDateString('de-DE', {
                        month: 'long',
                        year: 'numeric',
                    });
                }

                const weekdays = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];

                let html = weekdays.map(day => `<div class="dpf-calendar-header">${day}</div>`).join('');

                for (let i = 0; i < firstDay; i++) {
                    html += `<div class="dpf-day-cell is-empty"></div>`;
                }

                const todayKey = formatDateKey(new Date());

                for (let d = 1; d <= daysInMonth; d++) {
                    const thisDate = new Date(year, month, d);
                    const dateKey = formatDateKey(thisDate);
                    const eventsForDay = state.events.filter(ev => ev.start_date && String(ev.start_date).startsWith(dateKey));

                    const classes = [
                        'dpf-day-cell',
                        todayKey === dateKey ? 'is-today' : '',
                        eventsForDay.length ? 'has-events' : '',
                    ].filter(Boolean).join(' ');

                    html += `
                    <div class="${classes}" data-date="${dateKey}">
                        <div class="dpf-day-number">${d}</div>
                        ${eventsForDay.length ? `<div class="dpf-day-event-count">${eventsForDay.length} Termin${eventsForDay.length > 1 ? 'e' : ''}</div>` : ''}
                    </div>
                `;
                }

                dom.calendar.innerHTML = html;

                dom.calendar.querySelectorAll('.dpf-day-cell[data-date]').forEach(cell => {
                    cell.addEventListener('click', function () {
                        showDayEvents(this.dataset.date);
                    });
                });
            }

            function showDayEvents(dateKey) {
                const eventsForDay = state.events.filter(ev => ev.start_date && String(ev.start_date).startsWith(dateKey));

                if (dom.selectedDate) {
                    dom.selectedDate.textContent = new Date(dateKey + 'T00:00:00').toLocaleDateString('de-DE', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                    });
                }

                if (!dom.eventCards) return;

                if (!eventsForDay.length) {
                    dom.eventCards.innerHTML = emptyHtml('Für diesen Tag gibt es keine Termine.');
                    return;
                }

                dom.eventCards.innerHTML = eventsForDay.map(ev => {
                    const color = ev.taskColor || '#74b2d4';
                    const startTime = ev.start_time || 'Ganztägig';
                    const endTime = ev.end_time ? '– ' + ev.end_time : '';
                    const desc = ev.description || 'Keine Beschreibung';

                    const employees = Array.isArray(ev.employees) ? ev.employees.map(emp => `
                    <img src="${routes.employeeImageBase}/${escapeHtml(emp.image || '')}"
                         class="dpf-avatar-sm mr-1"
                         title="${escapeHtml((emp.name || '') + ' ' + (emp.lastname || ''))}"
                         alt=""
                         onerror="this.src='${routes.defaultAvatar}'">
                `).join('') : '';

                    return `
                    <article class="dpf-event-card" style="border-left-color:${escapeHtml(color)};">
                        <div class="d-flex justify-content-between align-items-start" style="gap:12px;">
                            <div style="min-width:0;">
                                <div class="font-weight-bold text-dark">${escapeHtml(ev.title || 'Termin')}</div>
                                <div class="small text-muted mt-1">${escapeHtml(desc)}</div>
                                <div class="small text-muted mt-1">${escapeHtml(startTime)} ${escapeHtml(endTime)}</div>
                                <div class="mt-2 d-flex flex-wrap">${employees}</div>
                            </div>

                            <a href="${routes.appointmentBase}/${encodeURIComponent(ev.id)}" class="dpf-btn dpf-btn-soft" style="padding:7px 10px;">
                                Details
                            </a>
                        </div>
                    </article>
                `;
                }).join('');
            }

            function bindFilters() {
                ['dpf-ticket-priority', 'dpf-ticket-status', 'dpf-ticket-sort'].forEach(id => {
                    document.getElementById(id)?.addEventListener('change', renderTickets);
                });

                ['dpf-task-filter', 'dpf-task-sort'].forEach(id => {
                    document.getElementById(id)?.addEventListener('change', renderTasks);
                });

                document.getElementById('prevMonth')?.addEventListener('click', function () {
                    state.currentDate.setMonth(state.currentDate.getMonth() - 1);
                    renderCalendar();
                });

                document.getElementById('nextMonth')?.addEventListener('click', function () {
                    state.currentDate.setMonth(state.currentDate.getMonth() + 1);
                    renderCalendar();
                });
            }

            function boot() {
                initTabs();
                bindFilters();

                activateTab('projects');

                loadTickets();
                loadTeam();
                loadTasks();
                loadExpenses();
                loadCalendar();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
            } else {
                boot();
            }
        })();
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
                label: 'Abteilung Liste',
                url: "{{ url('department_view') }}"
            },
            {
                label: 'Abteilungsprofil',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush