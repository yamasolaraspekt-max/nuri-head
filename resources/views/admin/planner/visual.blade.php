@extends('admin.layouts.app')

@section('title', 'Projektprofil')

@php
    $projectId = $projectId ?? request()->route('project');
    $pageTitle = 'PROJEKT COCKPIT';
    $pageSubtitle = 'Ausgewähltes Montage-Projekt mit Kunde, Objekt, Team, Aufgaben, Abhängigkeiten, Gantt, Historie und Organisationsübersicht.';

    $profileConfig = $config ?? [];

    $profileConfig = array_merge([
        'projectId' => (int) $projectId,
        'dataUrl' => Route::has('planner.projects.profile.data')
            ? route('planner.projects.profile.data', ['project' => $projectId])
            : url('/planner/projects/' . $projectId . '/profile/data'),
        'backUrl' => Route::has('planner.projects') ? route('planner.projects') : url('/planner/projects'),
        'syncUrl' => Route::has('planner.plans.sync') ? route('planner.plans.sync') : url('/planner/plans/sync'),
        'boardUrl' => Route::has('planner.index') ? route('planner.index') : url('/planner'),
        'historyUrl' => Route::has('planner.projects.history')
            ? route('planner.projects.history', ['project' => $projectId])
            : url('/planner/projects/' . $projectId . '/history'),
    ], $profileConfig);
@endphp

@push('style')
    <style>
        :root {
            --pc-bg: #f3f4f6;
            --pc-card: #ffffff;
            --pc-text: #111827;
            --pc-muted: #6b7280;
            --pc-border: #e5e7eb;
            --pc-primary: #93c21c;
            --pc-primary-dark: #7baa18;
            --pc-primary-soft: #f4fae7;
            --pc-blue: #74b2d4;
            --pc-blue-soft: #eef7fb;
            --pc-success: #10b981;
            --pc-warning: #f59e0b;
            --pc-danger: #ef4444;
            --pc-purple: #8b5cf6;
            --pc-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
            --pc-shadow: 0 18px 46px rgba(15, 23, 42, .12);
            --pc-radius: 16px;
        }

        .pc-wrap {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--pc-text);
        }

        .pc-header {
            margin-bottom: 18px;
        }

        .pc-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .pc-title {
            font-size: 27px;
            font-weight: 950;
            letter-spacing: -.035em;
            text-transform: uppercase;
            color: #0f172a;
        }

        .pc-sub {
            font-size: 13px;
            font-weight: 700;
            color: var(--pc-muted);
            line-height: 1.45;
            margin-top: 4px;
            max-width: 860px;
        }

        .pc-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            font-size: 13px;
            font-weight: 800;
            color: var(--pc-muted);
        }

        .pc-breadcrumb a {
            color: var(--pc-muted);
            text-decoration: none;
        }

        .pc-breadcrumb a:hover {
            color: #0f172a;
            text-decoration: none;
        }

        .pc-breadcrumb .current {
            color: #0f172a;
        }

        .pc-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pc-btn,
        .pc-btn-soft,
        .pc-icon-btn {
            border-radius: 10px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: .16s ease;
            white-space: nowrap;
        }

        .pc-btn {
            border: 0;
            background: var(--pc-primary);
            color: #fff;
            padding: 10px 16px;
            box-shadow: 0 9px 18px rgba(147, 194, 28, .22);
        }

        .pc-btn:hover {
            background: var(--pc-primary-dark);
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .pc-btn-soft {
            border: 1px solid var(--pc-border);
            background: #fff;
            color: #334155;
            padding: 10px 14px;
        }

        .pc-btn-soft:hover,
        .pc-btn-soft.is-active {
            background: var(--pc-primary-soft);
            border-color: rgba(147, 194, 28, .45);
            color: #365314;
            text-decoration: none;
        }

        .pc-icon-btn {
            width: 36px;
            height: 36px;
            border: 1px solid var(--pc-border);
            background: #fff;
            color: #64748b;
        }

        .pc-icon-btn:hover {
            background: #f8fafc;
            color: #0f172a;
            text-decoration: none;
        }

        .pc-icon-btn.info {
            background: var(--pc-blue-soft);
            border-color: rgba(116, 178, 212, .25);
            color: #0369a1;
        }

        .pc-icon-btn.success {
            background: #ecfdf5;
            border-color: #bbf7d0;
            color: #047857;
        }

        .pc-icon-btn.warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .pc-icon-btn.danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .pc-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(360px, .85fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        @media(max-width:1200px) {
            .pc-hero {
                grid-template-columns: 1fr;
            }
        }

        .pc-panel {
            background: #fff;
            border: 1px solid var(--pc-border);
            border-radius: var(--pc-radius);
            box-shadow: var(--pc-shadow-sm);
            overflow: hidden;
        }

        .pc-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 15px 16px;
            border-bottom: 1px solid var(--pc-border);
            background: linear-gradient(135deg, #fff, #f8fafc);
        }

        .pc-panel-title {
            font-size: 15px;
            font-weight: 950;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pc-panel-sub {
            font-size: 12px;
            font-weight: 750;
            color: var(--pc-muted);
            margin-top: 3px;
        }

        .pc-panel-body {
            padding: 16px;
        }

        .pc-project-main {
            display: grid;
            grid-template-columns: 74px minmax(0, 1fr);
            gap: 14px;
            align-items: flex-start;
        }

        .pc-project-avatar {
            width: 74px;
            height: 74px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--pc-blue-soft), var(--pc-primary-soft));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0369a1;
            font-size: 28px;
            font-weight: 950;
            border: 1px solid #dbeafe;
        }

        .pc-project-title {
            font-size: 22px;
            font-weight: 950;
            color: #0f172a;
            line-height: 1.15;
            margin-bottom: 4px;
        }

        .pc-project-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 9px;
        }

        .pc-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 900;
        }

        .pc-chip.green {
            background: #ecfdf5;
            border-color: #bbf7d0;
            color: #047857;
        }

        .pc-chip.blue {
            background: var(--pc-blue-soft);
            border-color: #cfe8f3;
            color: #0369a1;
        }

        .pc-chip.orange {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .pc-chip.purple {
            background: #f5f3ff;
            border-color: #ddd6fe;
            color: #6d28d9;
        }

        .pc-chip.red {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .pc-info-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        @media(max-width:1000px) {
            .pc-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:640px) {
            .pc-info-grid {
                grid-template-columns: 1fr;
            }
        }

        .pc-info-box {
            background: #f8fafc;
            border: 1px solid var(--pc-border);
            border-radius: 14px;
            padding: 12px;
            min-width: 0;
        }

        .pc-info-label {
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--pc-muted);
        }

        .pc-info-value {
            font-size: 13px;
            font-weight: 950;
            color: #0f172a;
            margin-top: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pc-info-help {
            font-size: 12px;
            font-weight: 700;
            color: var(--pc-muted);
            margin-top: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pc-stat-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        @media(max-width:1400px) {
            .pc-stat-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media(max-width:800px) {
            .pc-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:560px) {
            .pc-stat-grid {
                grid-template-columns: 1fr;
            }
        }

        .pc-stat {
            background: #fff;
            border: 1px solid var(--pc-border);
            border-radius: 16px;
            padding: 14px;
            box-shadow: var(--pc-shadow-sm);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .pc-stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950;
            font-size: 18px;
            flex: 0 0 auto;
        }

        .pc-stat-icon.blue {
            background: var(--pc-blue-soft);
            color: #0369a1;
        }

        .pc-stat-icon.green {
            background: #ecfdf5;
            color: #047857;
        }

        .pc-stat-icon.orange {
            background: #fffbeb;
            color: #b45309;
        }

        .pc-stat-icon.purple {
            background: #f5f3ff;
            color: #6d28d9;
        }

        .pc-stat-icon.red {
            background: #fef2f2;
            color: #b91c1c;
        }

        .pc-stat-label {
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--pc-muted);
        }

        .pc-stat-value {
            font-size: 24px;
            font-weight: 950;
            line-height: 1.1;
            color: #0f172a;
            margin-top: 4px;
        }

        .pc-stat-sub {
            font-size: 12px;
            font-weight: 750;
            color: var(--pc-muted);
            margin-top: 3px;
        }

        .pc-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px;
            background: #fff;
            border: 1px solid var(--pc-border);
            border-radius: 14px;
            padding: 8px;
            box-shadow: var(--pc-shadow-sm);
        }

        .pc-tab {
            border: 1px solid transparent;
            background: transparent;
            color: #64748b;
            border-radius: 11px;
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .pc-tab:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .pc-tab.is-active {
            background: var(--pc-primary-soft);
            border-color: rgba(147, 194, 28, .45);
            color: #365314;
        }

        .pc-view {
            display: none;
        }

        .pc-view.is-active {
            display: block;
        }

        .pc-board-grid {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 16px;
        }

        @media(max-width:1200px) {
            .pc-board-grid {
                grid-template-columns: 1fr;
            }
        }

        .pc-team-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        @media(max-width:1300px) {
            .pc-team-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width:760px) {
            .pc-team-grid {
                grid-template-columns: 1fr;
            }
        }

        .pc-employee-card {
            border: 1px solid var(--pc-border);
            border-radius: 16px;
            background: #fff;
            padding: 13px;
            box-shadow: var(--pc-shadow-sm);
        }

        .pc-employee-top {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .pc-avatar,
        .pc-avatar-initial {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .12);
            background: #e5e7eb;
            flex: 0 0 auto;
        }

        .pc-avatar {
            object-fit: cover;
        }

        .pc-avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            color: #fff;
            font-size: 13px;
            font-weight: 950;
        }

        .pc-employee-name {
            font-size: 14px;
            font-weight: 950;
            color: #0f172a;
            line-height: 1.2;
        }

        .pc-employee-role {
            font-size: 12px;
            font-weight: 750;
            color: var(--pc-muted);
            margin-top: 2px;
        }

        .pc-workload {
            margin-top: 11px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 7px;
        }

        .pc-workload div {
            background: #f8fafc;
            border: 1px solid var(--pc-border);
            border-radius: 12px;
            padding: 8px;
            text-align: center;
        }

        .pc-workload strong {
            display: block;
            font-size: 15px;
            font-weight: 950;
            color: #0f172a;
        }

        .pc-workload span {
            display: block;
            font-size: 9px;
            font-weight: 950;
            color: var(--pc-muted);
            text-transform: uppercase;
            margin-top: 3px;
        }

        .pc-canvas-shell {
            height: 620px;
            background: #f8fafc;
            border: 1px solid var(--pc-border);
            border-radius: 16px;
            overflow: hidden;
            position: relative;
        }

        .pc-canvas-toolbar {
            position: absolute;
            left: 12px;
            top: 12px;
            right: 12px;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            pointer-events: none;
        }

        .pc-canvas-toolbar>* {
            pointer-events: auto;
        }

        .pc-canvas-title {
            background: rgba(255, 255, 255, .92);
            border: 1px solid var(--pc-border);
            border-radius: 13px;
            padding: 8px 11px;
            box-shadow: var(--pc-shadow-sm);
        }

        .pc-canvas-title strong {
            font-size: 12px;
            font-weight: 950;
            color: #0f172a;
            display: block;
        }

        .pc-canvas-title span {
            font-size: 11px;
            font-weight: 750;
            color: var(--pc-muted);
            display: block;
            margin-top: 1px;
        }

        .pc-canvas-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, .92);
            border: 1px solid var(--pc-border);
            border-radius: 13px;
            padding: 5px;
            box-shadow: var(--pc-shadow-sm);
        }

        .pc-canvas-area {
            position: absolute;
            inset: 0;
            overflow: hidden;
            cursor: grab;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 22px 22px;
        }

        .pc-canvas-area:active {
            cursor: grabbing;
        }

        .pc-canvas-stage {
            position: absolute;
            left: 0;
            top: 0;
            width: 3200px;
            height: 2200px;
            transform-origin: 0 0;
        }

        .pc-canvas-svg {
            position: absolute;
            left: 0;
            top: 0;
            width: 3200px;
            height: 2200px;
            overflow: visible;
            pointer-events: none;
        }

        .pc-node {
            position: absolute;
            width: 220px;
            background: #fff;
            border: 2px solid #fff;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .12);
            overflow: hidden;
            cursor: pointer;
            transition: border-color .15s ease, transform .15s ease, box-shadow .15s ease;
        }

        .pc-node:hover,
        .pc-node.is-active {
            border-color: var(--pc-blue);
            box-shadow: 0 18px 42px rgba(15, 23, 42, .17);
            transform: translateY(-1px);
        }

        .pc-node-head {
            padding: 10px 12px;
            color: #fff;
            font-weight: 950;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .pc-node-body {
            padding: 10px 12px;
            background: #fff;
        }

        .pc-node-body strong {
            display: block;
            font-size: 13px;
            color: #0f172a;
            line-height: 1.25;
        }

        .pc-node-body span {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: var(--pc-muted);
            margin-top: 4px;
            line-height: 1.35;
        }

        .pc-canvas-side {
            position: absolute;
            top: 72px;
            right: 12px;
            bottom: 12px;
            width: 360px;
            z-index: 18;
            background: rgba(255, 255, 255, .96);
            border: 1px solid var(--pc-border);
            border-radius: 16px;
            box-shadow: var(--pc-shadow);
            overflow: auto;
            display: none;
        }

        .pc-canvas-side.is-open {
            display: block;
        }

        .pc-canvas-side-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 14px;
            border-bottom: 1px solid var(--pc-border);
            background: linear-gradient(135deg, #fff, #f8fafc);
        }

        .pc-canvas-side-body {
            padding: 14px;
        }

        .pc-gantt-shell {
            background: #fff;
            border: 1px solid var(--pc-border);
            border-radius: 16px;
            overflow: auto;
            box-shadow: var(--pc-shadow-sm);
        }

        .pc-gantt {
            min-width: 980px;
            padding: 16px;
        }

        .pc-gantt-row {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 12px;
            align-items: center;
            min-height: 48px;
            border-bottom: 1px solid #f1f5f9;
        }

        .pc-gantt-row:last-child {
            border-bottom: 0;
        }

        .pc-gantt-label {
            font-size: 13px;
            font-weight: 950;
            color: #0f172a;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pc-gantt-line {
            position: relative;
            height: 18px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
        }

        .pc-gantt-bar {
            position: absolute;
            top: 0;
            bottom: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--pc-blue), var(--pc-primary));
            min-width: 8px;
        }

        .pc-gantt-date {
            font-size: 11px;
            font-weight: 800;
            color: var(--pc-muted);
            margin-top: 4px;
        }

        .pc-dep-list,
        .pc-daily-list,
        .pc-history-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pc-dep-item,
        .pc-day,
        .pc-history-item {
            background: #fff;
            border: 1px solid var(--pc-border);
            border-radius: 16px;
            box-shadow: var(--pc-shadow-sm);
            overflow: hidden;
        }

        .pc-dep-item {
            padding: 14px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 40px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
        }

        @media(max-width:800px) {
            .pc-dep-item {
                grid-template-columns: 1fr;
            }

            .pc-dep-arrow {
                transform: rotate(90deg);
            }
        }

        .pc-dep-box {
            background: #f8fafc;
            border: 1px solid var(--pc-border);
            border-radius: 13px;
            padding: 11px;
            min-width: 0;
        }

        .pc-dep-title {
            font-size: 13px;
            font-weight: 950;
            color: #0f172a;
        }

        .pc-dep-meta {
            font-size: 11px;
            font-weight: 800;
            color: var(--pc-muted);
            margin-top: 4px;
        }

        .pc-dep-arrow {
            text-align: center;
            color: var(--pc-blue);
            font-weight: 950;
        }

        .pc-day-head {
            width: 100%;
            border: 0;
            background: linear-gradient(135deg, #fff, #f8fafc);
            padding: 14px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            text-align: left;
        }

        .pc-day-title {
            font-size: 14px;
            font-weight: 950;
            color: #0f172a;
        }

        .pc-day-meta {
            font-size: 12px;
            font-weight: 800;
            color: var(--pc-muted);
            margin-top: 3px;
        }

        .pc-day-body {
            display: none;
            padding: 0 15px 15px;
        }

        .pc-day.is-open .pc-day-body {
            display: block;
        }

        .pc-employee-group {
            border: 1px solid var(--pc-border);
            background: #fff;
            border-radius: 14px;
            margin-top: 10px;
            overflow: hidden;
        }

        .pc-employee-group-head {
            padding: 11px 12px;
            background: #f8fafc;
            border-bottom: 1px solid var(--pc-border);
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .pc-activity {
            display: grid;
            grid-template-columns: 88px minmax(0, 1fr) 120px;
            gap: 10px;
            padding: 11px 12px;
            border-bottom: 1px solid #f1f5f9;
            align-items: center;
        }

        .pc-activity:last-child {
            border-bottom: 0;
        }

        @media(max-width:760px) {
            .pc-activity {
                grid-template-columns: 1fr;
            }
        }

        .pc-activity-time {
            font-size: 12px;
            font-weight: 950;
            color: #0369a1;
        }

        .pc-activity-title {
            font-size: 13px;
            font-weight: 950;
            color: #0f172a;
        }

        .pc-activity-meta {
            font-size: 11px;
            font-weight: 800;
            color: var(--pc-muted);
            margin-top: 3px;
        }

        .pc-activity-status {
            text-align: right;
        }

        @media(max-width:760px) {
            .pc-activity-status {
                text-align: left;
            }
        }

        .pc-history-item {
            position: relative;
            padding: 14px 14px 14px 44px;
        }

        .pc-history-item:before {
            content: "";
            position: absolute;
            left: 19px;
            top: 18px;
            width: 11px;
            height: 11px;
            border-radius: 999px;
            background: var(--pc-primary);
            box-shadow: 0 0 0 4px var(--pc-primary-soft);
        }

        .pc-history-item:after {
            content: "";
            position: absolute;
            left: 24px;
            top: 32px;
            bottom: -18px;
            width: 1px;
            background: #dbeafe;
        }

        .pc-history-item:last-child:after {
            display: none;
        }

        .pc-history-title {
            font-size: 14px;
            font-weight: 950;
            color: #0f172a;
        }

        .pc-history-meta {
            font-size: 12px;
            font-weight: 800;
            color: var(--pc-muted);
            margin-top: 4px;
        }

        .pc-history-reason {
            margin-top: 8px;
            background: #f8fafc;
            border: 1px solid var(--pc-border);
            border-radius: 12px;
            padding: 9px;
            font-size: 12px;
            font-weight: 800;
            color: #334155;
        }

        .pc-history-diff {
            display: inline-flex;
            margin-top: 8px;
            border-radius: 999px;
            background: var(--pc-blue-soft);
            color: #0369a1;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 950;
        }

        .pc-empty {
            background: #fff;
            border: 1px dashed var(--pc-border);
            border-radius: 16px;
            padding: 42px;
            text-align: center;
            color: var(--pc-muted);
            font-weight: 850;
        }

        .pc-loading {
            background: #fff;
            border: 1px solid var(--pc-border);
            border-radius: 16px;
            padding: 34px;
            text-align: center;
            color: var(--pc-muted);
            font-weight: 900;
        }

        .pc-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 16px;
            padding: 16px;
            font-weight: 850;
        }
    </style>
@endpush

@section('content')
    <div class="pc-wrap" id="projectProfileApp" data-config='@json($profileConfig)'>
        <div class="pc-header">
            <div class="pc-titlebar">
                <div>
                    <div class="pc-title">{{ $pageTitle }}</div>
                    <div class="pc-sub">{{ $pageSubtitle }}</div>
                    <div class="pc-breadcrumb">
                        <a href="{{ $profileConfig['backUrl'] }}">Projektplanung</a>
                        <span>/</span>
                        <span class="current" id="pcBreadcrumbTitle">Projekt #{{ $projectId }}</span>
                    </div>
                </div>
                <div class="pc-actions">
                    <a href="{{ $profileConfig['backUrl'] }}" class="pc-btn-soft">← Zurück</a>
                    <button type="button" class="pc-btn-soft" id="pcRefreshBtn">Aktualisieren</button>
                    <button type="button" class="pc-btn" id="pcSyncBtn">Plan synchronisieren</button>
                </div>
            </div>
        </div>

        <div id="pcAlert"></div>

        <div class="pc-hero">
            <section class="pc-panel">
                <div class="pc-panel-head">
                    <div>
                        <h3 class="pc-panel-title">Projektübersicht</h3>
                        <div class="pc-panel-sub">Nur Daten des ausgewählten Kundenprodukts werden geladen.</div>
                    </div>
                    <span class="pc-chip green" id="pcStageChip">Lädt...</span>
                </div>
                <div class="pc-panel-body">
                    <div class="pc-project-main">
                        <div class="pc-project-avatar" id="pcProjectAvatar">P</div>
                        <div>
                            <div class="pc-project-title" id="pcProjectTitle">Projekt wird geladen...</div>
                            <div class="pc-sub" id="pcProjectSubtitle">Bitte warten.</div>
                            <div class="pc-project-meta" id="pcProjectMeta"></div>
                        </div>
                    </div>
                    <div class="pc-info-grid" id="pcInfoGrid"></div>
                </div>
            </section>

            <section class="pc-panel">
                <div class="pc-panel-head">
                    <div>
                        <h3 class="pc-panel-title">Letzte Aktivität</h3>
                        <div class="pc-panel-sub">Aktuellster Job und Statuswechsel im Projekt.</div>
                    </div>
                    <button type="button" class="pc-icon-btn info" data-pc-tab="history">↗</button>
                </div>
                <div class="pc-panel-body" id="pcLatestJob">
                    <div class="pc-loading">Letzte Aktivität wird geladen...</div>
                </div>
            </section>
        </div>

        <div class="pc-stat-grid" id="pcStats"></div>

        <nav class="pc-tabs" aria-label="Projekt Bereiche">
            <button type="button" class="pc-tab is-active" data-pc-tab="overview">Cockpit</button>
            <button type="button" class="pc-tab" data-pc-tab="canvas">Org-Canvas</button>
            <button type="button" class="pc-tab" data-pc-tab="gantt">Gantt</button>
            <button type="button" class="pc-tab" data-pc-tab="daily">Tagesliste</button>
            <button type="button" class="pc-tab" data-pc-tab="dependencies">Abhängigkeiten</button>
            <button type="button" class="pc-tab" data-pc-tab="history">Historie</button>
        </nav>

        <section class="pc-view is-active" id="pcView-overview">
            <div class="pc-board-grid">
                <section class="pc-panel">
                    <div class="pc-panel-head">
                        <div>
                            <h3 class="pc-panel-title">Team & Arbeit</h3>
                            <div class="pc-panel-sub">Wer macht was in diesem Projekt.</div>
                        </div>
                    </div>
                    <div class="pc-panel-body">
                        <div class="pc-team-grid" id="pcTeamGrid"></div>
                    </div>
                </section>
                <section class="pc-panel">
                    <div class="pc-panel-head">
                        <div>
                            <h3 class="pc-panel-title">Projektstruktur</h3>
                            <div class="pc-panel-sub">Kunde, Plan, Aufgaben und operative Module.</div>
                        </div>
                    </div>
                    <div class="pc-panel-body" id="pcStructureSummary"></div>
                </section>
            </div>
        </section>

        <section class="pc-view" id="pcView-canvas">
            <div class="pc-canvas-shell">
                <div class="pc-canvas-toolbar">
                    <div class="pc-canvas-title">
                        <strong>Organisations-Canvas</strong>
                        <span>Knoten ziehen, zoomen und Details öffnen</span>
                    </div>
                    <div class="pc-canvas-actions">
                        <button type="button" class="pc-icon-btn" id="pcCanvasZoomOut">−</button>
                        <span class="pc-chip" id="pcCanvasZoomLabel">100%</span>
                        <button type="button" class="pc-icon-btn" id="pcCanvasZoomIn">+</button>
                        <button type="button" class="pc-icon-btn" id="pcCanvasReset">Reset</button>
                    </div>
                </div>
                <div class="pc-canvas-area" id="pcCanvasArea">
                    <div class="pc-canvas-stage" id="pcCanvasStage">
                        <svg class="pc-canvas-svg" id="pcCanvasSvg"></svg>
                        <div id="pcCanvasNodes"></div>
                    </div>
                </div>
                <aside class="pc-canvas-side" id="pcCanvasSide">
                    <div class="pc-canvas-side-head">
                        <div>
                            <h3 class="pc-panel-title" id="pcCanvasSideTitle">Details</h3>
                            <div class="pc-panel-sub" id="pcCanvasSideSub">Knoteninformation</div>
                        </div>
                        <button type="button" class="pc-icon-btn" id="pcCanvasSideClose">×</button>
                    </div>
                    <div class="pc-canvas-side-body" id="pcCanvasSideBody"></div>
                </aside>
            </div>
        </section>

        <section class="pc-view" id="pcView-gantt">
            <div class="pc-gantt-shell">
                <div class="pc-gantt" id="pcGantt"></div>
            </div>
        </section>

        <section class="pc-view" id="pcView-daily">
            <div class="pc-daily-list" id="pcDailyList"></div>
        </section>

        <section class="pc-view" id="pcView-dependencies">
            <div class="pc-dep-list" id="pcDependencies"></div>
        </section>

        <section class="pc-view" id="pcView-history">
            <div class="pc-history-list" id="pcHistory"></div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            'use strict';

            const app = document.getElementById('projectProfileApp');
            if (!app) return;

            const config = JSON.parse(app.dataset.config || '{}');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            const state = {
                data: null,
                canvas: { x: 60, y: 70, scale: 0.75, dragging: null, selected: null },
                nodes: [],
                edges: [],
            };

            const el = id => document.getElementById(id);
            const esc = value => String(value ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[c]));
            const arr = value => Array.isArray(value) ? value : [];
            const lower = value => String(value ?? '').toLowerCase();

            function money(value) {
                const n = Number(value || 0);
                return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(n);
            }

            function fmtDate(value) {
                if (!value) return '—';
                try { return new Intl.DateTimeFormat('de-DE', { dateStyle: 'medium' }).format(new Date(value)); } catch (e) { return value; }
            }

            function fmtDateTime(value) {
                if (!value) return '—';
                try { return new Intl.DateTimeFormat('de-DE', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value)); } catch (e) { return value; }
            }

            function initials(name) {
                return String(name || 'P').split(/\s+/).filter(Boolean).slice(0, 2).map(x => x.charAt(0).toUpperCase()).join('') || 'P';
            }

            function setAlert(type, message) {
                const box = el('pcAlert');
                if (!box) return;
                if (!message) { box.innerHTML = ''; return; }
                box.innerHTML = `<div class="${type === 'error' ? 'pc-error' : 'pc-loading'}" style="margin-bottom:16px;">${esc(message)}</div>`;
            }

            function normalizePayload(json) {
                const data = json?.data || json || {};
                const project = data.project || data.lead_product || data.lpl || {};
                const customer = data.customer || project.customer || {};
                const object = data.object || data.alternative || project.object || {};
                const product = data.product || project.product || {};
                const plan = data.plan || project.plan || {};
                const items = arr(data.items || data.activities || data.planner_items);
                const team = arr(data.team || data.employees || data.members);
                const dependencies = arr(data.dependencies);
                const history = arr(data.history || data.timeline);
                const daily = arr(data.daily || data.daily_groups || data.calendar_groups);
                const org = data.org || data.organization || {};
                const stats = data.stats || data.summary || {};

                return { project, customer, object, product, plan, items, team, dependencies, history, daily, org, stats };
            }

            async function loadProfile() {
                setAlert('', '');
                showLoading();

                try {
                    const res = await fetch(config.dataUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    const json = await res.json();
                    if (!res.ok || json.ok === false) throw new Error(json.message || 'Projektprofil konnte nicht geladen werden.');
                    state.data = normalizePayload(json);
                    renderAll();
                } catch (err) {
                    setAlert('error', err.message || 'Projektprofil konnte nicht geladen werden.');
                }
            }

            function showLoading() {
                ['pcStats', 'pcTeamGrid', 'pcStructureSummary', 'pcGantt', 'pcDailyList', 'pcDependencies', 'pcHistory'].forEach(id => {
                    if (el(id)) el(id).innerHTML = '<div class="pc-loading">Wird geladen...</div>';
                });
            }

            function renderAll() {
                renderHeader();
                renderStats();
                renderTeam();
                renderStructure();
                renderCanvas();
                renderGantt();
                renderDaily();
                renderDependencies();
                renderHistory();
            }

            function renderHeader() {
                const d = state.data;
                const project = d.project || {};
                const customer = d.customer || {};
                const object = d.object || {};
                const product = d.product || {};
                const plan = d.plan || {};
                const customerName = project.customer_name || customer.name || customer.full_name || customer.firma || customer.company || `Kunde #${project.customer_id || ''}`;
                const productName = project.product_name || product.name || product.article_group || `Produkt #${project.product_id || ''}`;
                const objectName = project.object_name || object.name || object.object_name || 'Objekt';
                const stageName = project.sub_stage_name || project.stage_name || project.stage || project.status || 'Projekt';

                el('pcBreadcrumbTitle').textContent = `Projekt #${project.id || config.projectId}`;
                el('pcProjectAvatar').textContent = initials(productName);
                el('pcProjectTitle').textContent = `${customerName}`;
                el('pcProjectSubtitle').textContent = `${productName} · ${objectName}`;
                el('pcStageChip').textContent = stageName;

                el('pcProjectMeta').innerHTML = [
                    `<span class="pc-chip blue">#${esc(project.id || config.projectId)}</span>`,
                    `<span class="pc-chip green">${esc(stageName)}</span>`,
                    plan.id ? `<span class="pc-chip purple">Plan #${esc(plan.id)}</span>` : `<span class="pc-chip orange">Plan fehlt</span>`,
                    project.price || project.price_latest ? `<span class="pc-chip">${esc(money(project.price_latest || project.price))}</span>` : '',
                ].filter(Boolean).join('');

                el('pcInfoGrid').innerHTML = [
                    ['Kunde', customerName, customer.email || project.customer_email || customer.phone || project.customer_phone],
                    ['Objekt', objectName, object.address || project.object_address || object.full_address],
                    ['Produkt', productName, product.initial || project.product_initial || ''],
                    ['Projektstatus', stageName, `Aktualisiert: ${fmtDateTime(project.updated_at)}`],
                    ['Kundennummer', customer.customer_no || project.customer_no || '—', 'CRM Referenz'],
                    ['Adresse', object.address || project.object_address || object.full_address || '—', object.city || project.object_city || ''],
                    ['Service', project.service_name || project.service || '—', 'Phase / Bereich'],
                    ['Projektzeit', project.project_minutes ? `${project.project_minutes} Min.` : '—', 'Geplante Dauer'],
                ].map(([label, value, help]) => `
                <div class="pc-info-box">
                    <div class="pc-info-label">${esc(label)}</div>
                    <div class="pc-info-value" title="${esc(value)}">${esc(value || '—')}</div>
                    <div class="pc-info-help" title="${esc(help)}">${esc(help || '')}</div>
                </div>
            `).join('');

                const latest = arr(d.history)[0] || arr(d.items).sort((a, b) => new Date(b.updated_at || b.created_at || 0) - new Date(a.updated_at || a.created_at || 0))[0];
                el('pcLatestJob').innerHTML = latest ? `
                <div class="pc-info-box" style="background:#fff;">
                    <div class="pc-info-label">Neuester Job</div>
                    <div class="pc-info-value" style="white-space:normal;">${esc(latest.title || latest.event || latest.action || latest.source_type || 'Aktivität')}</div>
                    <div class="pc-info-help">${esc(fmtDateTime(latest.created_at || latest.updated_at || latest.when || latest.date))}</div>
                    ${latest.reason || latest.description || latest.text ? `<div class="pc-history-reason">${esc(latest.reason || latest.description || latest.text)}</div>` : ''}
                </div>
            ` : '<div class="pc-empty">Noch keine Aktivität vorhanden.</div>';
            }

            function renderStats() {
                const d = state.data;
                const items = arr(d.items);
                const team = arr(d.team);
                const deps = arr(d.dependencies);
                const history = arr(d.history);
                const done = items.filter(i => ['done', 'completed', 'finished', 'erledigt'].includes(lower(i.status))).length;
                const open = items.length - done;
                const progress = items.length ? Math.round((done / items.length) * 100) : 0;

                el('pcStats').innerHTML = [
                    ['Aufgaben', items.length, `${open} offen`, 'blue'],
                    ['Erledigt', done, `${progress}% Fortschritt`, 'green'],
                    ['Mitarbeiter', team.length, 'im Projekt', 'purple'],
                    ['Abhängigkeiten', deps.length, 'kritische Reihenfolge', 'orange'],
                    ['Historie', history.length, 'Ereignisse', 'red'],
                ].map(([label, value, sub, color]) => `
                <div class="pc-stat">
                    <div class="pc-stat-icon ${color}">${esc(String(label).charAt(0))}</div>
                    <div>
                        <div class="pc-stat-label">${esc(label)}</div>
                        <div class="pc-stat-value">${esc(value)}</div>
                        <div class="pc-stat-sub">${esc(sub)}</div>
                    </div>
                </div>
            `).join('');
            }

            function renderTeam() {
                const d = state.data;
                const items = arr(d.items);
                const team = arr(d.team);
                const byEmployee = new Map();

                items.forEach(item => {
                    const members = arr(item.members || item.employees || item.assignees);
                    if (item.lead) members.unshift(item.lead);
                    members.forEach(m => {
                        const id = m.id || m.employee_id;
                        if (!id) return;
                        if (!byEmployee.has(id)) byEmployee.set(id, { employee: m, items: [] });
                        byEmployee.get(id).items.push(item);
                    });
                });

                team.forEach(emp => {
                    const id = emp.id || emp.employee_id;
                    if (id && !byEmployee.has(id)) byEmployee.set(id, { employee: emp, items: [] });
                });

                const rows = Array.from(byEmployee.values());
                el('pcTeamGrid').innerHTML = rows.length ? rows.map(row => {
                    const emp = row.employee || {};
                    const name = emp.full_name || [emp.name, emp.lastname].filter(Boolean).join(' ') || `Mitarbeiter #${emp.id || emp.employee_id || ''}`;
                    const done = row.items.filter(i => ['done', 'completed', 'finished', 'erledigt'].includes(lower(i.status))).length;
                    const planned = row.items.filter(i => ['planned', 'in_progress', 'progress'].includes(lower(i.status))).length;
                    const avatar = emp.photo_url || emp.image_url || emp.image;
                    return `
                    <div class="pc-employee-card">
                        <div class="pc-employee-top">
                            ${avatar ? `<img class="pc-avatar" src="${esc(avatar)}" alt="${esc(name)}">` : `<div class="pc-avatar-initial">${esc(initials(name))}</div>`}
                            <div style="min-width:0;">
                                <div class="pc-employee-name">${esc(name)}</div>
                                <div class="pc-employee-role">${esc(emp.role || emp.stage_label || 'Projektteam')}</div>
                            </div>
                        </div>
                        <div class="pc-workload">
                            <div><strong>${row.items.length}</strong><span>gesamt</span></div>
                            <div><strong>${planned}</strong><span>geplant</span></div>
                            <div><strong>${done}</strong><span>fertig</span></div>
                        </div>
                    </div>
                `;
                }).join('') : '<div class="pc-empty">Noch keine Mitarbeiter im Projekt.</div>';
            }

            function renderStructure() {
                const d = state.data;
                const plan = d.plan || {};
                const items = arr(d.items);
                const sourceCounts = {};
                items.forEach(i => sourceCounts[i.source_type || i.category || 'manual'] = (sourceCounts[i.source_type || i.category || 'manual'] || 0) + 1);
                el('pcStructureSummary').innerHTML = `
                <div class="pc-info-grid" style="grid-template-columns:repeat(2,minmax(0,1fr));margin-top:0;">
                    <div class="pc-info-box"><div class="pc-info-label">Plan</div><div class="pc-info-value">${plan.id ? 'Plan #' + esc(plan.id) : 'Noch kein Plan'}</div><div class="pc-info-help">${esc(plan.title || '')}</div></div>
                    <div class="pc-info-box"><div class="pc-info-label">Status</div><div class="pc-info-value">${esc(plan.status || 'active')}</div><div class="pc-info-help">PlannerPlan</div></div>
                    ${Object.entries(sourceCounts).map(([key, total]) => `<div class="pc-info-box"><div class="pc-info-label">${esc(key)}</div><div class="pc-info-value">${esc(total)}</div><div class="pc-info-help">Einträge</div></div>`).join('')}
                </div>
            `;
            }

            function buildCanvasData() {
                const d = state.data;
                const project = d.project || {};
                const customer = d.customer || {};
                const object = d.object || {};
                const product = d.product || {};
                const plan = d.plan || {};
                const items = arr(d.items);
                const team = arr(d.team);
                const deps = arr(d.dependencies);

                if (d.org && arr(d.org.nodes).length) {
                    return { nodes: arr(d.org.nodes), edges: arr(d.org.edges) };
                }

                const nodes = [
                    { id: 'customer', type: 'customer', title: customer.name || project.customer_name || `Kunde #${project.customer_id || ''}`, subtitle: 'Kunde', x: 80, y: 130, color: '#74b2d4', detail: customer.email || customer.phone || '' },
                    { id: 'object', type: 'object', title: object.name || project.object_name || 'Objekt', subtitle: object.address || project.object_address || 'Projektobjekt', x: 380, y: 130, color: '#8b5cf6', detail: object.address || '' },
                    { id: 'product', type: 'product', title: product.name || project.product_name || 'Produkt', subtitle: 'Produkt / Gewerk', x: 680, y: 130, color: '#93c21c', detail: product.initial || '' },
                    { id: 'plan', type: 'plan', title: plan.title || `Plan #${plan.id || '—'}`, subtitle: 'PlannerPlan', x: 980, y: 130, color: '#f59e0b', detail: `${items.length} Aufgaben` },
                ];
                const edges = [['customer', 'object', 'hat Objekt'], ['object', 'product', 'hat Produkt'], ['product', 'plan', 'erstellt Plan']];

                team.slice(0, 10).forEach((emp, idx) => {
                    const name = emp.full_name || [emp.name, emp.lastname].filter(Boolean).join(' ') || `Mitarbeiter #${emp.id || emp.employee_id}`;
                    nodes.push({ id: `emp_${emp.id || emp.employee_id || idx}`, type: 'employee', title: name, subtitle: emp.role || 'Mitarbeiter', x: 160 + (idx % 4) * 260, y: 390 + Math.floor(idx / 4) * 180, color: '#3b82f6', detail: emp.email || emp.phone || '' });
                    edges.push(['plan', `emp_${emp.id || emp.employee_id || idx}`, 'arbeitet an']);
                });

                items.slice(0, 16).forEach((item, idx) => {
                    nodes.push({ id: `item_${item.id || idx}`, type: 'task', title: item.title || `Aufgabe #${item.id || idx + 1}`, subtitle: item.source_type || item.status || 'Aufgabe', x: 120 + (idx % 5) * 245, y: 780 + Math.floor(idx / 5) * 150, color: '#10b981', detail: item.description || item.status || '' });
                    edges.push(['plan', `item_${item.id || idx}`, 'enthält']);
                });

                deps.slice(0, 20).forEach((dep, idx) => {
                    const from = dep.from_item_id || dep.planner_item_id || dep.item_id;
                    const to = dep.to_item_id || dep.depends_on_item_id || dep.depends_on_id;
                    if (from && to) edges.push([`item_${from}`, `item_${to}`, dep.reason || 'abhängig']);
                });

                return { nodes, edges: edges.map(e => ({ from: e[0], to: e[1], label: e[2] })) };
            }

            function renderCanvas() {
                const data = buildCanvasData();
                state.nodes = data.nodes;
                state.edges = data.edges;
                updateCanvasTransform();
                drawEdges();
                drawNodes();
            }

            function updateCanvasTransform() {
                const stage = el('pcCanvasStage');
                if (!stage) return;
                stage.style.transform = `translate(${state.canvas.x}px, ${state.canvas.y}px) scale(${state.canvas.scale})`;
                el('pcCanvasZoomLabel').textContent = `${Math.round(state.canvas.scale * 100)}%`;
            }

            function drawEdges() {
                const svg = el('pcCanvasSvg');
                if (!svg) return;
                const nodeMap = new Map(state.nodes.map(n => [n.id, n]));
                svg.innerHTML = `
                <defs><marker id="pcArrow" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto"><polygon points="0 0, 10 3.5, 0 7" fill="#94a3b8"></polygon></marker></defs>
                ${state.edges.map(edge => {
                    const a = nodeMap.get(edge.from); const b = nodeMap.get(edge.to);
                    if (!a || !b) return '';
                    const x1 = Number(a.x || 0) + 220; const y1 = Number(a.y || 0) + 42;
                    const x2 = Number(b.x || 0); const y2 = Number(b.y || 0) + 42;
                    const c = Math.max(80, Math.abs(x2 - x1) * .35);
                    const path = `M ${x1} ${y1} C ${x1 + c} ${y1}, ${x2 - c} ${y2}, ${x2} ${y2}`;
                    return `<path d="${path}" fill="none" stroke="#94a3b8" stroke-width="2" opacity=".45" marker-end="url(#pcArrow)"></path><text x="${(x1 + x2) / 2}" y="${(y1 + y2) / 2 - 6}" text-anchor="middle" fill="#64748b" font-size="11" font-weight="800">${esc(edge.label || '')}</text>`;
                }).join('')}
            `;
            }

            function drawNodes() {
                const wrap = el('pcCanvasNodes');
                if (!wrap) return;
                wrap.innerHTML = state.nodes.map(n => `
                <div class="pc-node" data-node-id="${esc(n.id)}" style="left:${Number(n.x || 0)}px;top:${Number(n.y || 0)}px;">
                    <div class="pc-node-head" style="background:${esc(n.color || '#74b2d4')};"><span>●</span><span>${esc(n.title)}</span></div>
                    <div class="pc-node-body"><strong>${esc(n.subtitle || n.type || '')}</strong><span>${esc(n.detail || '')}</span></div>
                </div>
            `).join('');

                wrap.querySelectorAll('.pc-node').forEach(nodeEl => {
                    nodeEl.addEventListener('mousedown', e => startNodeDrag(e, nodeEl.dataset.nodeId));
                    nodeEl.addEventListener('click', e => openCanvasNode(nodeEl.dataset.nodeId));
                });
            }

            function startNodeDrag(e, nodeId) {
                e.stopPropagation();
                const node = state.nodes.find(n => n.id === nodeId);
                if (!node) return;
                state.canvas.dragging = { type: 'node', nodeId, sx: e.clientX, sy: e.clientY, ox: Number(node.x || 0), oy: Number(node.y || 0) };
            }

            function startPan(e) {
                if (e.target.closest('.pc-node') || e.target.closest('.pc-canvas-toolbar') || e.target.closest('.pc-canvas-side')) return;
                state.canvas.dragging = { type: 'pan', sx: e.clientX, sy: e.clientY, ox: state.canvas.x, oy: state.canvas.y };
            }

            function moveCanvas(e) {
                const d = state.canvas.dragging;
                if (!d) return;
                if (d.type === 'pan') {
                    state.canvas.x = d.ox + (e.clientX - d.sx);
                    state.canvas.y = d.oy + (e.clientY - d.sy);
                    updateCanvasTransform();
                }
                if (d.type === 'node') {
                    const node = state.nodes.find(n => n.id === d.nodeId);
                    if (!node) return;
                    node.x = d.ox + ((e.clientX - d.sx) / state.canvas.scale);
                    node.y = d.oy + ((e.clientY - d.sy) / state.canvas.scale);
                    drawEdges();
                    drawNodes();
                }
            }

            function stopCanvas() { state.canvas.dragging = null; }

            function openCanvasNode(nodeId) {
                const node = state.nodes.find(n => n.id === nodeId);
                if (!node) return;
                state.canvas.selected = node;
                el('pcCanvasSide').classList.add('is-open');
                el('pcCanvasSideTitle').textContent = node.title || 'Details';
                el('pcCanvasSideSub').textContent = node.subtitle || node.type || '';
                const related = state.edges.filter(e => e.from === node.id || e.to === node.id);
                el('pcCanvasSideBody').innerHTML = `
                <div class="pc-info-box" style="margin-bottom:12px;"><div class="pc-info-label">Beschreibung</div><div class="pc-info-value" style="white-space:normal;">${esc(node.detail || 'Keine Details')}</div></div>
                <div class="pc-info-label" style="margin-bottom:8px;">Verbindungen</div>
                ${related.length ? related.map(e => `<div class="pc-dep-box" style="margin-bottom:8px;"><div class="pc-dep-title">${esc(e.from)} → ${esc(e.to)}</div><div class="pc-dep-meta">${esc(e.label || '')}</div></div>`).join('') : '<div class="pc-empty" style="padding:22px;margin:0;">Keine Verbindungen</div>'}
            `;
            }

            function renderGantt() {
                const items = arr(state.data.items);
                if (!items.length) { el('pcGantt').innerHTML = '<div class="pc-empty">Keine Gantt-Aufgaben vorhanden.</div>'; return; }
                const dates = items.map(i => new Date(i.planned_start_at || i.start_at || i.created_at || Date.now()).getTime()).filter(Boolean);
                const min = Math.min(...dates);
                const max = Math.max(...items.map(i => new Date(i.planned_end_at || i.end_at || i.planned_start_at || i.created_at || Date.now()).getTime()).filter(Boolean), min + 86400000);
                const span = Math.max(86400000, max - min);
                el('pcGantt').innerHTML = items.map(item => {
                    const s = new Date(item.planned_start_at || item.start_at || item.created_at || min).getTime();
                    const e = new Date(item.planned_end_at || item.end_at || item.planned_start_at || item.created_at || s + 3600000).getTime();
                    const left = Math.max(0, Math.min(100, ((s - min) / span) * 100));
                    const width = Math.max(2, Math.min(100 - left, ((e - s) / span) * 100));
                    return `
                    <div class="pc-gantt-row">
                        <div><div class="pc-gantt-label">${esc(item.title || 'Aufgabe')}</div><div class="pc-gantt-date">${fmtDateTime(item.planned_start_at || item.created_at)} – ${fmtDateTime(item.planned_end_at || item.updated_at)}</div></div>
                        <div class="pc-gantt-line"><div class="pc-gantt-bar" style="left:${left}%;width:${width}%;"></div></div>
                    </div>
                `;
                }).join('');
            }

            function renderDaily() {
                let groups = arr(state.data.daily);
                if (!groups.length) {
                    const map = new Map();
                    arr(state.data.items).forEach(item => {
                        const day = (item.planned_start_at || item.created_at || '').slice(0, 10) || 'Ohne Datum';
                        if (!map.has(day)) map.set(day, []);
                        map.get(day).push(item);
                    });
                    groups = Array.from(map.entries()).map(([date, items]) => ({ date, items }));
                }
                el('pcDailyList').innerHTML = groups.length ? groups.map((group, idx) => {
                    const items = arr(group.items || group.activities);
                    const byEmp = new Map();
                    items.forEach(item => {
                        const people = arr(item.members || item.employees || item.assignees);
                        const name = people[0]?.full_name || people[0]?.name || item.employee_name || 'Ohne Mitarbeiter';
                        if (!byEmp.has(name)) byEmp.set(name, []);
                        byEmp.get(name).push(item);
                    });
                    return `
                    <article class="pc-day ${idx === 0 ? 'is-open' : ''}">
                        <button type="button" class="pc-day-head">
                            <div><div class="pc-day-title">${esc(fmtDate(group.date))}</div><div class="pc-day-meta">${items.length} Aktivitäten · ${byEmp.size} Mitarbeiter</div></div>
                            <span class="pc-chip">öffnen</span>
                        </button>
                        <div class="pc-day-body">
                            ${Array.from(byEmp.entries()).map(([name, empItems]) => `
                                <div class="pc-employee-group">
                                    <div class="pc-employee-group-head"><strong>${esc(name)}</strong><span class="pc-chip blue">${empItems.length}</span></div>
                                    ${empItems.map(item => `<div class="pc-activity"><div class="pc-activity-time">${esc((item.planned_start_at || item.created_at || '').slice(11, 16) || '—')}</div><div><div class="pc-activity-title">${esc(item.title || 'Aktivität')}</div><div class="pc-activity-meta">${esc(item.source_type || item.description || '')}</div></div><div class="pc-activity-status"><span class="pc-chip ${['done', 'completed'].includes(lower(item.status)) ? 'green' : 'orange'}">${esc(item.status || 'offen')}</span></div></div>`).join('')}
                                </div>
                            `).join('')}
                        </div>
                    </article>
                `;
                }).join('') : '<div class="pc-empty">Keine Tagesaktivitäten vorhanden.</div>';
                document.querySelectorAll('.pc-day-head').forEach(btn => btn.addEventListener('click', () => btn.closest('.pc-day')?.classList.toggle('is-open')));
            }

            function renderDependencies() {
                const deps = arr(state.data.dependencies);
                const itemsById = new Map(arr(state.data.items).map(i => [String(i.id), i]));
                el('pcDependencies').innerHTML = deps.length ? deps.map(dep => {
                    const fromId = dep.from_item_id || dep.planner_item_id || dep.item_id;
                    const toId = dep.to_item_id || dep.depends_on_item_id || dep.depends_on_id;
                    const from = itemsById.get(String(fromId)) || dep.from || {};
                    const to = itemsById.get(String(toId)) || dep.to || {};
                    return `
                    <div class="pc-dep-item">
                        <div class="pc-dep-box"><div class="pc-dep-title">${esc(from.title || dep.from_title || 'Aufgabe')}</div><div class="pc-dep-meta">${esc(from.status || '')}</div></div>
                        <div class="pc-dep-arrow">→</div>
                        <div class="pc-dep-box"><div class="pc-dep-title">${esc(to.title || dep.to_title || dep.depends_on_title || 'Abhängig')}</div><div class="pc-dep-meta">${esc(dep.reason || 'Abhängigkeit')}</div></div>
                    </div>
                `;
                }).join('') : '<div class="pc-empty">Keine Abhängigkeiten vorhanden.</div>';
            }

            function renderHistory() {
                const history = arr(state.data.history);
                el('pcHistory').innerHTML = history.length ? history.map((h, idx) => `
                <article class="pc-history-item">
                    <div class="pc-history-title">${esc(h.title || h.event || h.action || h.status || 'Ereignis')}</div>
                    <div class="pc-history-meta">${esc(fmtDateTime(h.created_at || h.date || h.when))} ${h.user_name || h.employee_name ? ' · ' + esc(h.user_name || h.employee_name) : ''}</div>
                    ${h.reason || h.description || h.text ? `<div class="pc-history-reason">${esc(h.reason || h.description || h.text)}</div>` : ''}
                    ${h.diff_human || h.diff_from_previous ? `<span class="pc-history-diff">${esc(h.diff_human || h.diff_from_previous)}</span>` : idx > 0 ? `<span class="pc-history-diff">Abstand zum vorherigen Ereignis</span>` : ''}
                </article>
            `).join('') : '<div class="pc-empty">Noch keine Historie vorhanden.</div>';
            }

            async function syncPlan() {
                const d = state.data || {};
                const project = d.project || {};
                const url = new URL(config.syncUrl, window.location.origin);
                url.searchParams.set('customer_id', project.customer_id || d.customer?.id || '');
                url.searchParams.set('project_id', project.id || config.projectId);
                if (project.product_id) url.searchParams.set('product_id', project.product_id);

                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();
                if (!res.ok || json.ok === false) throw new Error(json.message || 'Synchronisierung fehlgeschlagen.');
                await loadProfile();
            }

            document.querySelectorAll('[data-pc-tab]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tab = btn.dataset.pcTab;
                    document.querySelectorAll('.pc-tab').forEach(x => x.classList.toggle('is-active', x.dataset.pcTab === tab));
                    document.querySelectorAll('.pc-view').forEach(x => x.classList.toggle('is-active', x.id === `pcView-${tab}`));
                    if (tab === 'canvas') setTimeout(() => { updateCanvasTransform(); drawEdges(); }, 60);
                });
            });

            el('pcRefreshBtn')?.addEventListener('click', loadProfile);
            el('pcSyncBtn')?.addEventListener('click', async () => {
                try { await syncPlan(); } catch (e) { setAlert('error', e.message); }
            });

            el('pcCanvasArea')?.addEventListener('mousedown', startPan);
            window.addEventListener('mousemove', moveCanvas);
            window.addEventListener('mouseup', stopCanvas);
            el('pcCanvasZoomIn')?.addEventListener('click', () => { state.canvas.scale = Math.min(2, state.canvas.scale + .1); updateCanvasTransform(); });
            el('pcCanvasZoomOut')?.addEventListener('click', () => { state.canvas.scale = Math.max(.25, state.canvas.scale - .1); updateCanvasTransform(); });
            el('pcCanvasReset')?.addEventListener('click', () => { state.canvas.x = 60; state.canvas.y = 70; state.canvas.scale = .75; updateCanvasTransform(); });
            el('pcCanvasSideClose')?.addEventListener('click', () => el('pcCanvasSide')?.classList.remove('is-open'));
            el('pcCanvasArea')?.addEventListener('wheel', e => {
                if (!e.ctrlKey && !e.metaKey) return;
                e.preventDefault();
                state.canvas.scale = Math.max(.25, Math.min(2, state.canvas.scale - e.deltaY * .001));
                updateCanvasTransform();
            }, { passive: false });

            loadProfile();
        })();
    </script>
@endpush