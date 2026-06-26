@extends('admin.layouts.app')

@section('title', 'Persönliche Aufgaben')

@php
use Illuminate\Support\Facades\Route;

$taskEmployees = collect($employees ?? $employeeOptions ?? []);
$taskTeams = collect($teams ?? []);
$taskProducts = collect($articleGroups ?? $products ?? []);
$taskLeadStages = collect($leadStages ?? []);
$taskLeadStagePayload = $taskLeadStages->map(function ($stage) {
    $subStages = collect(data_get($stage, 'activeSubStages') ?? data_get($stage, 'active_sub_stages') ?? data_get($stage, 'subStages') ?? data_get($stage, 'sub_stages') ?? []);

    return [
        'id' => data_get($stage, 'id'),
        'key' => data_get($stage, 'key'),
        'name' => data_get($stage, 'name'),
        'color' => data_get($stage, 'color') ?: '#74b2d4',
        'icon' => data_get($stage, 'icon'),
        'sub_stages' => $subStages->map(function ($subStage) {
            return [
                'id' => data_get($subStage, 'id'),
                'lead_stage_id' => data_get($subStage, 'lead_stage_id'),
                'key' => data_get($subStage, 'key'),
                'name' => data_get($subStage, 'name'),
                'color' => data_get($subStage, 'color') ?: '#93c21c',
                'icon' => data_get($subStage, 'icon'),
            ];
        })->values(),
    ];
})->values();

$storeRoute = Route::has('personal-tasks.personal.task.store')
    ? route('personal-tasks.personal.task.store')
    : (Route::has('personal.task.store')
        ? route('personal.task.store')
        : (Route::has('personal-tasks.store') ? route('personal-tasks.store') : null));

$updateRoute = route('personal-tasks.personal.task.update');
@endphp

@section('style')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

    <style>
        :root {
            --pt-bg: #f3f4f6;
            --pt-card: #ffffff;
            --pt-text: #1f2937;
            --pt-muted: #6b7280;
            --pt-border: #e5e7eb;
            --pt-primary: #93c21c;
            --pt-primary-hover: #7baa18;
            --pt-primary-light: #f4fae7;
            --pt-blue: #74b2d4;
            --pt-blue-light: #eff6ff;
            --pt-success: #10b981;
            --pt-success-light: #ecfdf5;
            --pt-warning: #f59e0b;
            --pt-warning-light: #fffbeb;
            --pt-danger: #ef4444;
            --pt-danger-light: #fef2f2;
            --pt-gray-light: #f9fafb;
            --pt-dark: #111827;
            --pt-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --pt-radius: 16px;
        }

        .pt-app {
            color: var(--pt-text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .pt-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .pt-title {
            font-size: 26px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            letter-spacing: -.025em;
        }

        .pt-subtitle {
            font-size: 13px;
            color: var(--pt-muted);
            margin-top: 4px;
        }

        .pt-header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .pt-btn {
            border: none;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: .18s ease;
            text-decoration: none;
            line-height: 1;
        }

        .pt-btn svg,
        .pt-btn i[data-lucide] {
            width: 16px;
            height: 16px;
        }

        .pt-btn-primary {
            background: var(--pt-primary);
            color: #fff;
            box-shadow: 0 12px 25px rgba(147, 194, 28, .28);
        }

        .pt-btn-primary:hover {
            background: var(--pt-primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }

        .pt-btn-soft {
            background: #fff;
            border: 1px solid var(--pt-border);
            color: var(--pt-text);
        }

        .pt-btn-soft:hover {
            background: #f9fafb;
            color: var(--pt-text);
        }

        .pt-btn-danger {
            background: var(--pt-danger-light);
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .pt-btn-danger:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .pt-toolbar {
            background: #fff;
            border: 1px solid var(--pt-border);
            border-radius: var(--pt-radius);
            padding: 14px;
            box-shadow: 0 1px 2px rgb(0 0 0 / .04);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .pt-toolbar-left,
        .pt-toolbar-right {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pt-toolbar-left {
            flex: 1;
        }

        .pt-filter {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 150px;
        }

        .pt-filter.search {
            min-width: 280px;
            flex: 1;
        }

        #ptLeadStageFilter,
        #ptLeadSubStageFilter {
            min-width: 190px;
        }

        .pt-label,
        .nt-field-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--pt-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 0;
        }

        .pt-input,
        .pt-select {
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--pt-border);
            background: #f9fafb;
            padding: 0 12px;
            font-size: 13px;
            outline: none;
            transition: .15s ease;
        }

        .pt-input:focus,
        .pt-select:focus {
            background: #fff;
            border-color: var(--pt-primary);
            box-shadow: 0 0 0 3px var(--pt-primary-light);
        }

        .pt-tabs {
            display: inline-flex;
            background: #f9fafb;
            border: 1px solid var(--pt-border);
            border-radius: 12px;
            padding: 4px;
            gap: 4px;
        }

        .pt-tab {
            border: none;
            background: transparent;
            border-radius: 9px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 900;
            color: var(--pt-muted);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            white-space: nowrap;
        }

        .pt-tab.is-active {
            background: #111827;
            color: #fff;
        }

        .pt-tab svg {
            width: 15px;
            height: 15px;
        }

        .pt-more {
            position: relative;
        }

        .pt-more-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 250px;
            background: #fff;
            border: 1px solid var(--pt-border);
            border-radius: 14px;
            box-shadow: var(--pt-shadow);
            padding: 8px;
            display: none;
            z-index: 1000;
        }

        .pt-more.is-open .pt-more-menu {
            display: block;
        }

        .pt-more-item {
            width: 100%;
            border: none;
            background: transparent;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--pt-text);
            text-align: left;
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
        }

        .pt-more-item:hover {
            background: var(--pt-primary-light);
        }

        .pt-more-item svg {
            width: 16px;
            height: 16px;
        }

        .pt-stats {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        @media(max-width:1280px) {
            .pt-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media(max-width:720px) {
            .pt-stats {
                grid-template-columns: 1fr;
            }
        }

        .pt-stat {
            background: #fff;
            border: 1px solid var(--pt-border);
            border-radius: 16px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 1px 2px rgb(0 0 0 / .04);
        }

        .pt-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .pt-stat-icon.open {
            background: var(--pt-blue-light);
            color: var(--pt-blue);
        }

        .pt-stat-icon.progress {
            background: var(--pt-warning-light);
            color: var(--pt-warning);
        }

        .pt-stat-icon.done {
            background: var(--pt-success-light);
            color: var(--pt-success);
        }

        .pt-stat-icon.pause,
        .pt-stat-icon.archive {
            background: #f3f4f6;
            color: #374151;
        }

        .pt-stat-icon.trash {
            background: var(--pt-danger-light);
            color: var(--pt-danger);
        }

        .pt-stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .pt-stat-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--pt-muted);
            letter-spacing: .06em;
        }

        .pt-stat-value {
            font-size: 22px;
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
            margin-top: 3px;
        }

        .pt-main-card {
            background: #fff;
            border: 1px solid var(--pt-border);
            border-radius: 18px;
            box-shadow: 0 1px 2px rgb(0 0 0 / .04);
            overflow: visible;
        }

        .pt-main-head {
            padding: 14px 16px;
            border-bottom: 1px solid var(--pt-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .pt-section-title {
            font-size: 14px;
            font-weight: 900;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pt-section-title svg {
            width: 17px;
            height: 17px;
            color: var(--pt-primary);
        }

        .pt-board {
            padding: 14px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            min-height: 520px;
        }

        @media(max-width:980px) {
            .pt-board {
                grid-template-columns: 1fr;
            }
        }

        .pt-column {
            background: #f9fafb;
            border: 1px solid var(--pt-border);
            border-radius: 16px;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .pt-column-head {
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--pt-border);
            background: #fff;
        }

        .pt-column-title {
            font-size: 13px;
            font-weight: 900;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pt-column-count {
            background: var(--pt-primary-light);
            color: #3f6212;
            border-radius: 999px;
            padding: 3px 9px;
            font-size: 11px;
            font-weight: 900;
        }

        .pt-column-body {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
            overflow-y: auto;
            max-height: calc(100vh - 360px);
        }

        .pt-column-body.is-over {
            outline: 2px dashed var(--pt-primary);
            outline-offset: -8px;
            background: #f8ffe9;
        }

        .pt-task-card {
            background: #fff;
            border: 1px solid var(--pt-border);
            border-left: 5px solid var(--card-color, var(--pt-primary));
            border-radius: 14px;
            padding: 12px;
            position: relative;
            transition: .18s ease;
            cursor: grab;
        }

        .pt-task-card:hover {
            border-color: var(--pt-primary);
            box-shadow: var(--pt-shadow);
            transform: translateY(-1px);
        }

        .pt-task-card.is-paused {
            opacity: .86;
        }

        .pt-task-card.is-paused .pt-card-inner {
            filter: blur(1.2px) grayscale(.25);
        }

        .pt-pause-layer {
            position: absolute;
            inset: 0;
            z-index: 5;
            border-radius: 14px;
            background: rgba(255, 255, 255, .74);
            backdrop-filter: blur(2px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
            padding: 16px;
        }

        .pt-pause-layer svg {
            width: 24px;
            height: 24px;
            color: var(--pt-warning);
        }

        .pt-pause-layer strong {
            font-size: 13px;
            color: #111827;
        }

        .pt-pause-layer span {
            font-size: 11px;
            color: var(--pt-muted);
            max-width: 240px;
        }

        .pt-card-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .pt-card-title {
            font-size: 14px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            line-height: 1.35;
        }

        .pt-card-code {
            font-size: 11px;
            color: var(--pt-muted);
            margin-top: 3px;
        }

        .pt-card-actions {
            display: flex;
            align-items: flex-start;
            gap: 4px;
        }

        .pt-icon-btn {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            border: 1px solid var(--pt-border);
            background: #fff;
            color: var(--pt-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .15s ease;
        }

        .pt-icon-btn:hover {
            background: var(--pt-primary-light);
            color: #111827;
            border-color: var(--pt-primary);
        }

        .pt-icon-btn svg {
            width: 15px;
            height: 15px;
        }

        .pt-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 9px 0;
        }

        .pt-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
            background: #f9fafb;
            border: 1px solid var(--pt-border);
            color: #4b5563;
        }

        .pt-pill svg {
            width: 12px;
            height: 12px;
        }

        .pt-pill.priority-high,
        .pt-pill.priority-very-high {
            background: var(--pt-danger-light);
            color: #b91c1c;
            border-color: #fecaca;
        }

        .pt-pill.priority-medium {
            background: var(--pt-warning-light);
            color: #92400e;
            border-color: #fde68a;
        }

        .pt-pill.priority-low,
        .pt-pill.priority-normal {
            background: var(--pt-success-light);
            color: #047857;
            border-color: #bbf7d0;
        }

        .pt-stage-pill {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
            max-width: 100%;
        }

        .pt-stage-pill small {
            padding-left: 7px;
            margin-left: 2px;
            color: #475569;
            font-weight: 900;
        }

        .nt-stage-preview {
            border: 1px dashed #cbd5e1;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-radius: 14px;
            padding: 10px;
            margin-top: 8px;
        }

        .nt-stage-preview .nt-chip {
            font-weight: 900;
        }

        .pt-desc {
            font-size: 12px;
            color: var(--pt-muted);
            line-height: 1.45;
            margin-top: 8px;
        }

        .pt-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 12px;
        }

        .pt-avatars {
            display: flex;
            align-items: center;
        }

        .pt-avatar {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: 2px solid #fff;
            background: #e5e7eb;
            margin-left: -7px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 900;
            color: #374151;
        }

        .pt-avatar:first-child {
            margin-left: 0;
        }

        .pt-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pt-progress {
            width: 100%;
            height: 6px;
            border-radius: 999px;
            background: #f3f4f6;
            overflow: hidden;
            margin-top: 10px;
        }

        .pt-progress-bar {
            height: 100%;
            background: var(--pt-primary);
            border-radius: 999px;
        }

        .pt-list {
            padding: 14px;
            overflow-x: auto;
        }

        .pt-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            min-width: 1120px;
        }

        .pt-table th {
            text-align: left;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--pt-muted);
            letter-spacing: .06em;
            padding: 0 12px;
        }

        .pt-table td {
            background: #fff;
            border-top: 1px solid var(--pt-border);
            border-bottom: 1px solid var(--pt-border);
            padding: 12px;
            vertical-align: middle;
            font-size: 13px;
        }

        .pt-table td:first-child {
            border-left: 1px solid var(--pt-border);
            border-radius: 14px 0 0 14px;
        }

        .pt-table td:last-child {
            border-right: 1px solid var(--pt-border);
            border-radius: 0 14px 14px 0;
        }

        .pt-row-paused td {
            background: #fafafa;
            opacity: .78;
        }

        .pt-action-dropdown {
            position: relative;
            display: inline-flex;
        }

        .pt-action-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: #fff;
            border: 1px solid var(--pt-border);
            border-radius: 14px;
            box-shadow: var(--pt-shadow);
            padding: 8px;
            display: none;
            z-index: 2500;
        }

        .pt-action-dropdown.is-open .pt-action-menu {
            display: block;
        }

        .pt-action-menu button {
            width: 100%;
            border: none;
            background: transparent;
            padding: 9px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            color: var(--pt-text);
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
            cursor: pointer;
        }

        .pt-action-menu button:hover {
            background: var(--pt-primary-light);
        }

        .pt-action-menu button.danger {
            color: var(--pt-danger);
        }

        .pt-empty {
            padding: 30px 16px;
            text-align: center;
            color: var(--pt-muted);
            font-size: 13px;
            border: 1px dashed #d1d5db;
            border-radius: 14px;
            background: #fff;
        }

        .pt-loader {
            padding: 50px;
            text-align: center;
            color: var(--pt-muted);
            font-weight: 800;
        }

        .d-none {
            display: none !important;
        }

        /* slide drawer */
        .new_task {
            position: fixed;
            top: 0;
            right: -100%;
            width: 1220px;
            max-width: 100%;
            height: 100vh;
            z-index: 1300;
            display: none;
            font-size: 13px;
            color: #020617;
        }

        .new_task_card {
            position: relative;
            height: 100%;
            background: #f9fafb;
            box-shadow: 0 25px 60px rgba(15, 23, 42, .32);
            border-radius: 1.25rem 0 0 1.25rem;
            border: 1px solid rgba(148, 163, 184, .4);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .nt-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1.2rem;
            background: linear-gradient(135deg, #93c21c, #cfe09b);
            color: #0b1120;
        }

        .nt-header-left {
            display: flex;
            flex-direction: column;
            gap: .1rem;
        }

        .nt-header-title {
            font-size: 1.1rem;
            font-weight: 900;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .nt-header-sub {
            font-size: .76rem;
            opacity: .9;
        }

        .nt-header-actions {
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .nt-close-btn {
            border: none;
            border-radius: 999px;
            padding: .35rem .7rem;
            background: rgba(15, 23, 42, .12);
            color: #0b1120;
            font-size: .8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            transition: .15s ease;
        }

        .nt-close-btn:hover {
            background: rgba(15, 23, 42, .22);
            transform: translateY(-1px);
        }

        .nt-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 1.1rem .75rem;
            background: radial-gradient(circle at top left, #eef5d8 0, #f9fafb 50%, #f3f4f6 100%);
        }

        .nt-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.05fr);
            gap: .9rem;
        }

        @media(max-width:900px) {
            .nt-grid {
                grid-template-columns: 1fr;
            }

            .new_task_card {
                border-radius: 0;
            }
        }

        .nt-section {
            background: #fff;
            border-radius: 1rem;
            padding: .8rem .9rem;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
            border: 1px solid rgba(209, 213, 219, .7);
            margin-bottom: .75rem;
        }

        .nt-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: .6rem;
            gap: .75rem;
        }

        .nt-section-title {
            font-size: .8rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .nt-section-badge {
            font-size: .7rem;
            padding: .15rem .55rem;
            border-radius: 999px;
            background: #ecfccb;
            color: #3f6212;
            border: 1px solid #a3e635;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
        }

        .nt-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .55rem;
            margin-bottom: .55rem;
        }

        .nt-row-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .55rem;
            margin-bottom: .55rem;
        }

        .nt-row-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .55rem;
            margin-bottom: .55rem;
        }

        @media(max-width:768px) {

            .nt-row,
            .nt-row-3,
            .nt-row-4 {
                grid-template-columns: 1fr;
            }
        }

        .nt-input,
        .nt-select,
        .nt-textarea {
            width: 100%;
            border-radius: .7rem;
            border: 1px solid #d1d5db;
            font-size: .8rem;
            padding: .45rem .65rem;
            background: #f9fafb;
            outline: none;
            transition: .15s ease;
            color: #111827;
        }

        .nt-select,
        .nt-input {
            min-height: 38px;
        }

        .nt-textarea {
            resize: vertical;
            min-height: 60px;
        }

        .nt-input:focus,
        .nt-select:focus,
        .nt-textarea:focus {
            background: #fff;
            border-color: #93c21c;
            box-shadow: 0 0 0 1px rgba(147, 194, 28, .4);
        }

        .nt-switch-row {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            margin-top: .25rem;
        }

        .nt-switch-label {
            font-size: .78rem;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .nt-top-right-row {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: .65rem;
            flex-wrap: wrap;
            margin-top: .15rem;
        }

        .nt-inline-toggle {
            font-size: .72rem;
            display: flex;
            flex-direction: column;
            gap: .1rem;
            align-items: flex-start;
        }

        .nt-inline-toggle p {
            margin-bottom: 0;
            font-size: .75rem;
            color: #4b5563;
        }

        .nt-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
        }

        .nt-chip {
            border-radius: 999px;
            padding: .2rem .6rem;
            font-size: .74rem;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            color: #4b5563;
        }

        .nt-footer {
            padding: .7rem 1rem;
            border-top: 1px solid rgba(209, 213, 219, .8);
            background: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: .55rem;
            flex-wrap: wrap;
        }

        .nt-btn {
            border-radius: 999px;
            padding: .45rem 1rem;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border: none;
            cursor: pointer;
            font-weight: 800;
        }

        .nt-btn svg {
            width: 15px;
            height: 15px;
        }

        .nt-btn-primary {
            background: linear-gradient(135deg, #020617, #1f2937);
            color: #f9fafb;
        }

        .nt-btn-ghost {
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .nt-btn-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        #key_task {
            width: 100%;
            border-collapse: collapse;
            font-size: .78rem;
        }

        #key_task th {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6b7280;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: .45rem;
        }

        #key_task td {
            padding: .45rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        #key_total_time {
            font-size: .75rem;
            color: #4b5563;
        }

        .select2-container {
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d1d5db;
            border-radius: .7rem;
            min-height: 38px;
            background: #f9fafb;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            color: #111827;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #ecfccb;
            border: 1px solid #a3e635;
            border-radius: 999px;
            color: #365314;
            padding: 2px 8px;
            font-size: 12px;
        }

        .nt-customer-option-title {
            font-weight: 800;
            color: #111827;
            font-size: 13px;
        }

        .nt-customer-option-meta {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.35;
            margin-top: 3px;
        }

        .pt-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .45);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 3000;
        }

        .pt-modal-backdrop.is-open {
            display: flex;
        }

        .pt-modal {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 18px;
            box-shadow: var(--pt-shadow);
            overflow: hidden;
        }

        .pt-modal-head {
            padding: 16px;
            border-bottom: 1px solid var(--pt-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pt-modal-title {
            font-size: 15px;
            font-weight: 900;
            color: #111827;
        }

        .pt-modal-body {
            padding: 16px;
        }

        .pt-modal-foot {
            padding: 14px 16px;
            border-top: 1px solid var(--pt-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .pt-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 4000;
            background: #111827;
            color: #fff;
            border-radius: 14px;
            padding: 12px 14px;
            display: none;
            align-items: center;
            gap: 9px;
            box-shadow: var(--pt-shadow);
            font-size: 13px;
            font-weight: 800;
        }

        .pt-toast.is-open {
            display: flex;
        }

        .pt-toast.success {
            background: #065f46;
        }

        .pt-toast.error {
            background: #991b1b;
        }


        /* ===== v2: employee avatars, profile, comments, step employee mode, conflict box ===== */
        .pt-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 7px;
            margin-left: 6px;
            border-radius: 999px;
            background: #e5e7eb;
            color: #111827;
            font-size: 11px;
            font-weight: 900;
        }

        .pt-tab.is-active .pt-tab-count {
            background: #93c21c;
            color: #fff;
        }

        .pt-card-actions .pt-icon-btn.profile {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .pt-card-actions .pt-icon-btn.comments {
            background: #f4fae7;
            color: #3f6212;
            border-color: #d9f99d;
            position: relative;
        }

        .pt-card-actions .pt-icon-btn.comments b {
            position: absolute;
            top: -7px;
            right: -7px;
            min-width: 17px;
            height: 17px;
            border-radius: 999px;
            background: #111827;
            color: #fff;
            font-size: 10px;
            line-height: 17px;
            text-align: center;
        }

        .pt-card-comments {
            margin-top: 10px;
            border-top: 1px dashed #d1d5db;
            padding-top: 10px;
            display: none;
        }

        .pt-task-card.is-comments-open .pt-card-comments {
            display: block;
        }

        .pt-comment-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 9px;
            margin-bottom: 8px;
        }

        .pt-comment-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 5px;
            font-size: 11px;
            color: #6b7280;
            font-weight: 800;
        }

        .pt-comment-author {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #111827;
            font-size: 12px;
            font-weight: 900;
        }

        .pt-comment-author img {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            object-fit: cover;
            background: #e5e7eb;
        }

        .pt-comment-text {
            font-size: 12px;
            color: #374151;
            line-height: 1.45;
        }

        .pt-comment-form {
            display: flex;
            gap: 7px;
            margin-top: 8px;
        }

        .pt-comment-form textarea {
            flex: 1;
            min-height: 38px;
            max-height: 120px;
            border: 1px solid #d1d5db;
            border-radius: 11px;
            padding: 8px;
            font-size: 12px;
            resize: vertical;
        }

        .pt-comment-form button {
            border: 0;
            border-radius: 11px;
            background: #93c21c;
            color: #fff;
            font-weight: 900;
            padding: 0 10px;
        }

        .pt-step-mode {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #dbeafe;
            background: #f8fafc;
            border-radius: 14px;
        }

        .pt-step-mode label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin: 0;
            font-size: 12px;
            font-weight: 900;
            color: #374151;
        }

        .pt-step-mode small {
            width: 100%;
            color: #64748b;
            font-size: 11px;
        }

        .pt-global-employee-box.is-hidden {
            display: none !important;
        }

        .pt-conflict-box {
            display: none;
            margin-top: 10px;
            border: 1px solid #fed7aa;
            background: #fff7ed;
            border-radius: 14px;
            padding: 10px;
            color: #9a3412;
            font-size: 12px;
        }

        .pt-conflict-box.is-open {
            display: block;
        }

        .pt-conflict-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-weight: 950;
            color: #9a3412;
            margin-bottom: 7px;
        }

        .pt-conflict-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pt-conflict-item {
            background: #fff;
            border: 1px solid #fed7aa;
            border-radius: 10px;
            padding: 7px;
        }

        .pt-conflict-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 9px;
        }

        .pt-conflict-actions button {
            border: 1px solid #fed7aa;
            background: #fff;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 900;
            color: #9a3412;
        }

        .pt-conflict-actions button.is-primary {
            background: #f97316;
            color: #fff;
            border-color: #f97316;
        }

        .nt-customer-product-row {
            padding: 8px 0;
            border-top: 1px solid #e5e7eb;
            margin-top: 6px;
        }

        .nt-customer-product-row:first-child {
            border-top: 0;
            margin-top: 0;
        }

        .nt-customer-product-title {
            font-weight: 900;
            color: #111827;
            font-size: 13px;
        }

        .nt-customer-product-meta {
            font-size: 11px;
            color: #64748b;
            line-height: 1.35;
            margin-top: 2px;
        }



        /* =========================================================
                       Priority/date sorting + due-date animation + reject reasons
                       ========================================================= */
        .pt-task-card.is-due-overdue {
            border-color: #fecaca;
            box-shadow: 0 0 0 1px rgba(239, 68, 68, .16), 0 16px 36px rgba(239, 68, 68, .12);
        }

        .pt-task-card.is-due-today {
            border-color: #fde68a;
            box-shadow: 0 0 0 1px rgba(245, 158, 11, .18), 0 16px 36px rgba(245, 158, 11, .10);
        }

        .pt-due-animated {
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        .pt-due-animated::after {
            content: "";
            position: absolute;
            inset: -40% -20%;
            background: linear-gradient(120deg, transparent 35%, rgba(255, 255, 255, .75) 50%, transparent 65%);
            transform: translateX(-120%);
            animation: ptDueShine 2.8s ease-in-out infinite;
            z-index: -1;
        }

        .pt-due-overdue {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #991b1b !important;
            animation: ptDuePulseRed 1.8s ease-in-out infinite;
        }

        .pt-due-today {
            background: #fffbeb !important;
            border-color: #fde68a !important;
            color: #92400e !important;
            animation: ptDuePulseAmber 2.1s ease-in-out infinite;
        }

        .pt-reject-note {
            margin-top: 10px;
            border: 1px solid #fee2e2;
            background: #fff7f7;
            color: #7f1d1d;
            border-radius: 12px;
            padding: 9px 10px;
            font-size: 12px;
            line-height: 1.45;
        }

        .pt-reject-note strong {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 900;
            margin-bottom: 3px;
        }

        .pt-reject-note small {
            color: #991b1b;
            font-weight: 700;
        }

        .pt-row-rejected td {
            background: #fff7f7 !important;
        }

        @keyframes ptDueShine {

            0%,
            55% {
                transform: translateX(-120%);
            }

            100% {
                transform: translateX(120%);
            }
        }

        @keyframes ptDuePulseRed {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, .22);
            }

            50% {
                box-shadow: 0 0 0 5px rgba(239, 68, 68, .06);
            }
        }

        @keyframes ptDuePulseAmber {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, .22);
            }

            50% {
                box-shadow: 0 0 0 5px rgba(245, 158, 11, .06);
            }
        }



        /* =========================================================
                       Vivid Laravel Validation Debug UI
                       Shows exact field errors, red borders, and top summary.
                       ========================================================= */
        .nt-validation-summary {
            display: none;
            border: 1px solid #fecaca;
            background: linear-gradient(135deg, #fff1f2, #fef2f2);
            color: #7f1d1d;
            border-radius: 16px;
            padding: 12px 14px;
            margin-bottom: 12px;
            box-shadow: 0 12px 30px rgba(239, 68, 68, .13);
        }

        .nt-validation-summary.is-open {
            display: block;
        }

        .nt-validation-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 950;
            margin-bottom: 8px;
        }

        .nt-validation-list {
            margin: 0;
            padding-left: 18px;
            font-size: 12px;
            line-height: 1.55;
            font-weight: 700;
        }

        .nt-validation-list button {
            border: 0;
            background: transparent;
            padding: 0;
            color: #b91c1c;
            font-weight: 950;
            text-decoration: underline;
            cursor: pointer;
        }

        .nt-field-error {
            margin-top: 6px;
            font-size: 11px;
            font-weight: 900;
            color: #dc2626;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 6px 8px;
            line-height: 1.35;
        }

        .nt-invalid,
        .nt-input.nt-invalid,
        .nt-select.nt-invalid,
        .nt-textarea.nt-invalid {
            border-color: #ef4444 !important;
            background: #fff1f2 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, .14) !important;
        }

        .select2-container.nt-invalid .select2-selection {
            border-color: #ef4444 !important;
            background: #fff1f2 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, .14) !important;
        }

        .nt-error-flash {
            animation: ntErrorFlash 1.2s ease-in-out 2;
        }



        /* =========================================================
                       List-only state management for paused/cancelled/archived/deleted
                       ========================================================= */
        .pt-list-only-note {
            display: none;
            margin: 0 14px 14px;
            border: 1px solid #dbeafe;
            background: linear-gradient(135deg, #eff6ff, #f8fafc);
            color: #1e3a8a;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 800;
            align-items: center;
            gap: 8px;
        }

        .pt-list-only-note.is-open {
            display: flex;
        }

        .pt-board-disabled {
            padding: 26px;
            text-align: center;
            color: #64748b;
            font-weight: 900;
        }

        .pt-action-menu button.restore-open {
            color: #2563eb;
        }

        .pt-action-menu button.restore-progress {
            color: #d97706;
        }

        @keyframes ntErrorFlash {

            0%,
            100% {
                outline: 0 solid rgba(239, 68, 68, 0);
            }

            50% {
                outline: 4px solid rgba(239, 68, 68, .22);
                outline-offset: 2px;
            }
        }
    </style>

    <style>
        /* =========================================================
                           Personal Task Board Scroll Fix
                           ========================================================= */

        html,
        body {
            height: 100%;
        }

        .pt-app {
            min-height: 0;
        }

        .pt-main-card {
            height: calc(100vh - 285px);
            min-height: 560px;
            display: flex;
            flex-direction: column;
            overflow: hidden !important;
        }

        .pt-main-head {
            flex: 0 0 auto;
        }

        #ptBoardView,
        #ptListView {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
        }

        .pt-board {
            height: 100%;
            min-height: 0 !important;
            overflow: hidden;
        }

        .pt-column {
            min-height: 0 !important;
            height: 100%;
            overflow: hidden !important;
        }

        .pt-column-body {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none !important;
            overflow-y: auto !important;
            overflow-x: hidden;
            overscroll-behavior: contain;
            padding-bottom: 70px;
        }

        .pt-column-body::-webkit-scrollbar,
        .pt-list::-webkit-scrollbar,
        .nt-body::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .pt-column-body::-webkit-scrollbar-track,
        .pt-list::-webkit-scrollbar-track,
        .nt-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .pt-column-body::-webkit-scrollbar-thumb,
        .pt-list::-webkit-scrollbar-thumb,
        .nt-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .pt-column-body::-webkit-scrollbar-thumb:hover,
        .pt-list::-webkit-scrollbar-thumb:hover,
        .nt-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .pt-list {
            height: 100%;
            overflow: auto !important;
            min-height: 0;
        }

        .pt-table {
            margin-bottom: 70px;
        }

        .new_task_card {
            min-height: 0;
        }

        .nt-body {
            min-height: 0;
            overflow-y: auto !important;
            overscroll-behavior: contain;
        }

        @media (max-width: 980px) {
            .pt-main-card {
                height: calc(100vh - 245px);
                min-height: 520px;
            }

            .pt-board {
                overflow-y: auto;
                padding-bottom: 70px;
            }

            .pt-column {
                min-height: 420px !important;
                max-height: 70vh;
            }
        }
    </style>
    <style>
        /* =========================================================
               New Task Drawer: collapsible sections + fixed action footer
               ========================================================= */

        #newTaskDrawer {
            overflow: hidden;
        }

        #newTaskDrawer .new_task_card {
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden !important;
        }

        #newTaskDrawer #task_form {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        #newTaskDrawer .nt-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto !important;
            overflow-x: hidden;
            overscroll-behavior: contain;
            padding-bottom: 1.15rem;
            scroll-padding-bottom: 110px;
        }

        #newTaskDrawer .nt-footer {
            position: sticky;
            bottom: 0;
            z-index: 40;
            flex: 0 0 auto;
            background: rgba(249, 250, 251, .96);
            backdrop-filter: blur(14px);
            border-top: 1px solid rgba(203, 213, 225, .95);
            box-shadow: 0 -18px 34px rgba(15, 23, 42, .12);
            padding: .8rem 1rem calc(.8rem + env(safe-area-inset-bottom));
        }

        #newTaskDrawer .nt-footer .nt-btn {
            min-height: 38px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
        }

        #newTaskDrawer .nt-footer .nt-btn-primary {
            box-shadow: 0 12px 25px rgba(2, 6, 23, .18);
        }

        .nt-section.is-collapsible {
            overflow: hidden;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .nt-section.is-collapsible .nt-section-header {
            cursor: pointer;
            user-select: none;
            margin: -.2rem -.25rem .6rem;
            padding: .25rem;
            border-radius: .85rem;
            transition: background .18s ease;
        }

        .nt-section.is-collapsible .nt-section-header:hover {
            background: #f8fafc;
        }

        .nt-section-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: .35rem;
            flex-wrap: wrap;
            margin-left: auto;
        }

        .nt-section-toggle {
            width: 28px;
            height: 28px;
            border: 1px solid #dbe3ef;
            background: #fff;
            color: #334155;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .18s ease;
            flex: 0 0 auto;
        }

        .nt-section-toggle:hover {
            border-color: #93c21c;
            background: #f4fae7;
            color: #365314;
            transform: translateY(-1px);
        }

        .nt-section-toggle svg {
            width: 15px;
            height: 15px;
            transition: transform .18s ease;
        }

        .nt-section.is-collapsed {
            padding-bottom: .8rem;
        }

        .nt-section.is-collapsed .nt-section-header {
            margin-bottom: 0;
        }

        .nt-section.is-collapsed> :not(.nt-section-header) {
            display: none !important;
        }

        .nt-section.is-collapsed .nt-section-toggle svg {
            transform: rotate(-90deg);
        }

        .nt-section.is-collapsed {
            box-shadow: 0 8px 22px rgba(15, 23, 42, .05);
        }

        .nt-section.has-validation-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, .10), 0 16px 35px rgba(239, 68, 68, .08) !important;
        }

        .nt-section.has-validation-error .nt-section-header {
            background: #fff1f2;
        }

        @media (max-width: 768px) {
            #newTaskDrawer .nt-footer {
                justify-content: stretch;
            }

            #newTaskDrawer .nt-footer .nt-btn {
                flex: 1 1 auto;
                justify-content: center;
            }
        }
    </style>

<style>
    
            .pt-app {
                position: relative;
                isolation: isolate;
            }

            .pt-task-card,
            .pt-card-inner,
            .pt-card-top,
            .pt-card-actions,
            .pt-action-dropdown {
                overflow: visible !important;
            }

            .pt-task-card {
                position: relative;
                z-index: 1;
                isolation: isolate;
            }

            .pt-task-card:hover {
                z-index: 50;
            }

            .pt-task-card:has(.pt-action-dropdown.is-open),
            .pt-task-card:has(.pt-action-menu:hover) {
                z-index: 99999 !important;
                transform: none !important;
            }

            .pt-main-card:has(.pt-action-dropdown.is-open),
            #ptBoardView:has(.pt-action-dropdown.is-open),
            .pt-board:has(.pt-action-dropdown.is-open),
            .pt-column:has(.pt-action-dropdown.is-open),
            .pt-column-body:has(.pt-action-dropdown.is-open) {
                overflow: visible !important;
            }

            .pt-action-dropdown {
                position: relative;
                z-index: 999999;
            }

            .pt-action-menu {
                position: absolute !important;
                top: calc(100% + 8px) !important;
                right: 0 !important;
                left: auto !important;

                display: none;
                width: 230px;
                max-height: min(420px, 70vh);
                overflow-y: auto;

                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                padding: 8px;

                z-index: 999999 !important;
                box-shadow:
                    0 24px 70px rgba(15, 23, 42, .24),
                    0 8px 22px rgba(15, 23, 42, .12);

                transform: translateZ(0);
            }

            .pt-action-dropdown.is-open .pt-action-menu {
                display: block !important;
                pointer-events: auto;
            }

            .pt-action-menu button {
                position: relative;
                z-index: 1;
                white-space: nowrap;
            }

            .pt-action-menu::-webkit-scrollbar {
                width: 7px;
            }

            .pt-action-menu::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 999px;
            }

            /* Fix top "Mehr" dropdown too */
            .pt-toolbar,
            .pt-toolbar-right,
            .pt-more {
                overflow: visible !important;
                position: relative;
                z-index: 9999;
            }

            .pt-more-menu {
                z-index: 999999 !important;
                max-height: min(430px, 70vh);
                overflow-y: auto;
            }

            .pt-more.is-open .pt-more-menu {
                display: block !important;
            }

            /* List view action menu fix */
            .pt-list:has(.pt-action-dropdown.is-open),
            .pt-table:has(.pt-action-dropdown.is-open),
            .pt-table tr:has(.pt-action-dropdown.is-open),
            .pt-table td:has(.pt-action-dropdown.is-open) {
                overflow: visible !important;
                position: relative;
                z-index: 99999 !important;
            }

            .pt-table tr:has(.pt-action-dropdown.is-open) {
                transform: none !important;
            }

            /* Mobile safe behavior */
            @media (max-width: 768px) {
                .pt-action-menu {
                    right: 0 !important;
                    width: 220px;
                    max-width: calc(100vw - 32px);
                }
            }
        </style>
    @endsection

@section('content')
    <div class="pt-app" id="personalTaskApp" data-auth-employee-id="{{ auth()->user()->name ?? '' }}">
        <div class="pt-header">
            <div>
                <h1 class="pt-title">Persönliche Aufgaben</h1>
                <div class="pt-subtitle">Klare AJAX-Aufgabenverwaltung mit Kanban, Liste und vollständigem Aufgaben-Drawer.
                </div>
            </div>

            <div class="pt-header-actions">
                <button type="button" class="pt-btn pt-btn-soft" id="ptRefreshBtn"><i
                        data-lucide="refresh-cw"></i>Aktualisieren</button>
                <button type="button" class="pt-btn pt-btn-primary create_new_task" id="ptNewTaskBtn"><i
                        data-lucide="plus"></i>Aufgabe erstellen</button>
            </div>
        </div>

        <div class="pt-toolbar">
            <div class="pt-toolbar-left">
                <div class="pt-filter search">
                    <label class="pt-label">Suche</label>
                    <input type="text" class="pt-input" id="ptSearchInput" placeholder="Aufgabe, Kunde, Objekt, Produkt...">
                </div>
                <div class="pt-filter">
                    <label class="pt-label">Priorität</label>
                    <select class="pt-select" id="ptPriorityFilter">
                        <option value="">Alle</option>
                        <option value="normal">Keiner</option>
                        <option value="medium">Medium</option>
                        <option value="high">Hoch</option>
                        <option value="very high">Sehr wichtig</option>
                    </select>
                </div>
                <div class="pt-filter">
                    <label class="pt-label">Fälligkeit</label>
                    <select class="pt-select" id="ptDueFilter">
                        <option value="all">Alle</option>
                        <option value="overdue">Überfällig</option>
                        <option value="today">Heute</option>
                        <option value="this_week">Diese Woche</option>
                        <option value="next_14_days">Nächste 14 Tage</option>
                        <option value="this_month">Dieser Monat</option>
                        <option value="no_due">Ohne Datum</option>
                    </select>
                </div>
                <div class="pt-filter">
                    <label class="pt-label">Mitarbeiter</label>
                    <select class="pt-select" id="ptEmployeeFilter">
                        <option value="">Alle</option>
                        @foreach($taskEmployees as $employee)
                            <option value="{{ data_get($employee, 'id') }}">
                                {{ trim((data_get($employee, 'name') ?? '') . ' ' . (data_get($employee, 'lastname') ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-filter">
                    <label class="pt-label">Lead Stage</label>
                    <select class="pt-select" id="ptLeadStageFilter">
                        <option value="">Alle Stages</option>
                        @foreach($taskLeadStagePayload as $stage)
                            <option value="{{ data_get($stage, 'id') }}" data-color="{{ data_get($stage, 'color') }}">
                                {{ data_get($stage, 'name') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-filter">
                    <label class="pt-label">Sub Stage</label>
                    <select class="pt-select" id="ptLeadSubStageFilter" disabled>
                        <option value="">Alle Sub Stages</option>
                    </select>
                </div>
            </div>

            <div class="pt-toolbar-right">
                <div class="pt-tabs" id="ptScopeTabs">
                    <button type="button" class="pt-tab is-active" data-scope="my"><i data-lucide="user-check"></i>Meine
                        Jobs <span class="pt-tab-count" data-tab-count="my">0</span></button>
                    <button type="button" class="pt-tab" data-scope="created"><i data-lucide="send"></i>Erstellt von mir
                        <span class="pt-tab-count" data-tab-count="created">0</span></button>
                </div>
                <div class="pt-tabs" id="ptViewTabs">
                    <button type="button" class="pt-tab is-active" data-view="board"><i
                            data-lucide="kanban"></i>Kanban</button>
                    <button type="button" class="pt-tab" data-view="list"><i data-lucide="list-todo"></i>Liste</button>
                </div>
                <div class="pt-more" id="ptMoreDropdown">
                    <button type="button" class="pt-btn pt-btn-soft" id="ptMoreBtn"><i
                            data-lucide="more-horizontal"></i>Mehr</button>
                    <div class="pt-more-menu">
                        <button type="button" class="pt-more-item" data-state="active"><i
                                data-lucide="layout-dashboard"></i>Aktive Aufgaben <span class="pt-tab-count"
                                data-tab-count="active">0</span></button>
                        <button type="button" class="pt-more-item" data-state="pause"><i
                                data-lucide="pause-circle"></i>Pausierte Aufgaben <span class="pt-tab-count"
                                data-tab-count="pause">0</span></button>
                        <button type="button" class="pt-more-item" data-state="cancel"><i
                                data-lucide="x-circle"></i>Abgebrochen von mir <span class="pt-tab-count"
                                data-tab-count="cancel">0</span></button>
                        <button type="button" class="pt-more-item" data-state="archived"><i
                                data-lucide="archive"></i>Archiviert <span class="pt-tab-count"
                                data-tab-count="archived">0</span></button>
                        <button type="button" class="pt-more-item" data-state="deleted"><i
                                data-lucide="trash-2"></i>Gelöscht <span class="pt-tab-count"
                                data-tab-count="deleted">0</span></button>
                        <button type="button" class="pt-more-item" data-state="rejected"><i data-lucide="ban"></i>Abgelehnt
                            <span class="pt-tab-count" data-tab-count="rejected">0</span></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-stats" id="ptStats">
            <div class="pt-stat">
                <div class="pt-stat-icon open"><i data-lucide="circle"></i></div>
                <div>
                    <div class="pt-stat-label">Offen</div>
                    <div class="pt-stat-value" data-stat="open">{{ $stats['open'] ?? 0 }}</div>
                </div>
            </div>
            <div class="pt-stat">
                <div class="pt-stat-icon progress"><i data-lucide="loader"></i></div>
                <div>
                    <div class="pt-stat-label">In Bearbeitung</div>
                    <div class="pt-stat-value" data-stat="in_progress">{{ $stats['in_progress'] ?? 0 }}</div>
                </div>
            </div>
            <div class="pt-stat">
                <div class="pt-stat-icon done"><i data-lucide="check-circle-2"></i></div>
                <div>
                    <div class="pt-stat-label">Erledigt</div>
                    <div class="pt-stat-value" data-stat="completed">{{ $stats['completed'] ?? 0 }}</div>
                </div>
            </div>
            <div class="pt-stat">
                <div class="pt-stat-icon pause"><i data-lucide="pause-circle"></i></div>
                <div>
                    <div class="pt-stat-label">Pausiert</div>
                    <div class="pt-stat-value" data-stat="pause">{{ $stats['paused'] ?? $stats['pause'] ?? 0 }}</div>
                </div>
            </div>
            <div class="pt-stat">
                <div class="pt-stat-icon archive"><i data-lucide="archive"></i></div>
                <div>
                    <div class="pt-stat-label">Archiviert</div>
                    <div class="pt-stat-value" data-stat="archived">{{ $stats['archived'] ?? 0 }}</div>
                </div>
            </div>
            <div class="pt-stat">
                <div class="pt-stat-icon trash"><i data-lucide="trash-2"></i></div>
                <div>
                    <div class="pt-stat-label">Gelöscht</div>
                    <div class="pt-stat-value" data-stat="deleted">{{ $stats['deleted'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="pt-main-card">
            <div class="pt-main-head">
                <div class="pt-section-title"><i data-lucide="kanban"></i><span id="ptMainTitle">Kanban Ansicht</span></div>
                <div class="pt-subtitle" id="ptResultText">Lade Aufgaben...</div>
            </div>

            <div class="pt-list-only-note" id="ptListOnlyNote">
                <i data-lucide="list-checks"></i>
                <span>Dieser Bereich ist eine Verwaltungs-Liste. Pausierte, abgebrochene, archivierte und gelöschte Aufgaben
                    werden nicht im Kanban angezeigt.</span>
            </div>

            <div id="ptBoardView">
                <div class="pt-board" id="ptBoard">
                    <div class="pt-loader">Lade Kanban...</div>
                </div>
            </div>

            <div id="ptListView" class="d-none">
                <div class="pt-list">
                    <table class="pt-table">
                        <thead>
                            <tr>
                                <th>Aufgabe</th>
                                <th>Status</th>
                                <th>Priorität</th>
                                <th>Fällig</th>
                                <th>Kunde / Objekt</th>
                                <th>CRM Status</th>
                                <th>Mitarbeiter</th>
                                <th>Controller</th>
                                <th>Fortschritt</th>
                                <th>Aktion</th>
                            </tr>
                        </thead>
                        <tbody id="ptListBody">
                            <tr>
                                <td colspan="10">Lade Liste...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="new_task" id="newTaskDrawer">
        <div class="new_task_card">
            <div class="nt-header">
                <div class="nt-header-left">
                    <div class="nt-header-title" id="drawerModeTitle">Aufgabe erstellen</div>
                    <div class="nt-header-sub">Mitarbeiter, Controller, Kunde, Objekt, Produkt, Priorität, Farbe und
                        Aufgaben-Schlüssel.</div>
                </div>
                <div class="nt-header-actions"><button type="button" class="nt-close-btn close_task_window"><i
                            data-lucide="x"></i>Schließen</button></div>
            </div>

            <form id="task_form" method="POST" action="{{ $storeRoute ?: $updateRoute }}"
                data-store-route="{{ $storeRoute }}" data-update-route="{{ $updateRoute }}">
                @csrf
                <input type="hidden" name="id" id="task_edit_id">
                <input type="hidden" name="same_id" value="same">
                <input type="hidden" name="color" id="color" value="#93c21c">
                <input type="hidden" name="lead_product_list_id" id="lead_product_list_id">

                <div class="nt-body">
                    <div id="taskValidationSummary" class="nt-validation-summary" aria-live="polite">
                        <div class="nt-validation-title"><i data-lucide="alert-triangle"></i> Bitte prüfen Sie diese Felder
                        </div>
                        <ol id="taskValidationList" class="nt-validation-list"></ol>
                    </div>
                    <div class="nt-grid">
                        <div>
                            <div class="nt-section">
                                <div class="nt-section-header">
                                    <div class="nt-section-title"><i data-lucide="clipboard-list"></i>Grunddaten</div><span
                                        class="nt-section-badge">Pflicht</span>
                                </div>
                                <div class="nt-row">
                                    <div>
                                        <label class="nt-field-label" for="task_title">Aufgabentitel</label>
                                        <input type="text" id="task_title" name="task_title" class="nt-input">
                                    </div>
                                    <div>
                                        <label class="nt-field-label" for="task_status">Status</label>
                                        <select id="task_status" name="task_status" class="nt-select">
                                            <option value="open">Offen</option>
                                            <option value="on_progress">In Bearbeitung</option>
                                            <option value="completed">Erledigt</option>
                                            <option value="pause">Pausiert</option>
                                            <option value="cancel">Abgebrochen</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="nt-row">
                                    <div>
                                        <label class="nt-field-label">Farbe & Priorität</label>
                                        <div class="nt-top-right-row">
                                            <div class="dropdown" id="color_drop_down">
                                                <button type="button" class="nt-btn nt-btn-ghost dropdown-toggle"
                                                    data-toggle="dropdown"><i id="colorIcon" data-lucide="square"
                                                        style="color:#93c21c"></i>Farbe</button>
                                                <div class="dropdown-menu">
                                                    @foreach(['#93c21c' => 'Grün', '#74b2d4' => 'Blau', '#f59e0b' => 'Orange', '#ef4444' => 'Rot', '#111827' => 'Dunkel', '#8b4513' => 'Braun', '#800080' => 'Lila', '#808080' => 'Grau'] as $hex => $label)
                                                        <button type="button" class="dropdown-item"
                                                            data-value="{{ $hex }}"><span
                                                                style="display:inline-block;width:12px;height:12px;border-radius:3px;background:{{ $hex }};margin-right:6px;"></span>{{ $label }}</button>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div id="priority_select" style="min-width:180px;">
                                                <label class="nt-field-label" for="priority"
                                                    style="margin-bottom:4px;display:block;">Priorität</label>
                                                <select name="priority" id="priority" class="nt-select"
                                                    data-allowed="normal,medium,high,very high">
                                                    <option value="normal">Keiner</option>
                                                    <option value="medium" selected>Mittel</option>
                                                    <option value="high">Hoch</option>
                                                    <option value="very high">Sehr wichtig</option>
                                                </select>
                                                <small id="priorityDebugHint"
                                                    style="display:block;margin-top:4px;color:#64748b;font-size:11px;font-weight:800;">Gesendet:
                                                    medium</small>
                                            </div>
                                            <div class="nt-inline-toggle">
                                                <p>Öffentlich</p><label><input type="checkbox" id="public_switch"
                                                        name="public" value="1" checked> Ja</label>
                                            </div>
                                            <div class="nt-inline-toggle">
                                                <p>Kunde</p><label><input type="checkbox" id="customerSwitch"
                                                        name="is_customer" value="1"> Ja</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="nt-field-label">Bericht / Typ</label>
                                        <div class="nt-row" style="margin-bottom:0;">
                                            <label class="nt-switch-label"><input type="checkbox" id="is_report"
                                                    name="is_report" value="1"> Berichtspflichtig</label>
                                            <select name="type" id="type" class="nt-select">
                                                <option value="task">Aufgabe</option>
                                                <option value="appointment">Termin</option>
                                                <option value="report">Bericht</option>
                                                <option value="follow_up">Nachfassen</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="nt-field-label" for="description">Beschreibung</label>
                                    <textarea name="description" id="description" class="nt-textarea" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="nt-section">
                                <div class="nt-section-header">
                                    <div class="nt-section-title"><i data-lucide="calendar-clock"></i>Zeitplanung</div>
                                </div>
                                <div class="nt-row-4">
                                    <div><label class="nt-field-label">Startdatum</label><input type="date" id="start_date"
                                            name="start_date" class="nt-input" value="{{ now()->format('Y-m-d') }}"></div>
                                    <div><label class="nt-field-label">Fälligkeitsdatum</label><input type="date"
                                            id="due_date" name="due_date" class="nt-input"></div>
                                    <div><label class="nt-field-label">Fälligkeitsuhrzeit</label><input type="time"
                                            id="due_time" name="due_time" class="nt-input"></div>
                                    <div><label class="nt-field-label">Fortschritt %</label><input type="number" min="0"
                                            max="100" id="progress" name="progress" class="nt-input" value="0"></div>
                                </div>
                                <div class="nt-row">
                                    <div><label class="nt-field-label">Gesamttage</label><input type="number" step="0.01"
                                            id="total_day" name="total_day" class="nt-input" readonly></div>
                                    <div><label class="nt-field-label">Gesamtstunden</label><input type="number" step="0.25"
                                            id="total_time" name="total_time" class="nt-input" readonly></div>
                                </div>
                                <div class="pt-conflict-box" id="appointmentConflictBox">
                                    <div class="pt-conflict-head"><span><i data-lucide="alert-triangle"></i>
                                            Terminüberschneidung gefunden</span><button type="button"
                                            id="ptConflictClose">×</button></div>
                                    <div class="pt-conflict-list" id="appointmentConflictList"></div>
                                    <div class="pt-conflict-actions">
                                        <button type="button" id="ptConflictChange">Datum ändern</button>
                                        <button type="button" class="is-primary" id="ptConflictAnyway">Trotzdem
                                            auswählen</button>
                                    </div>
                                </div>
                            </div>

                            <div class="nt-section" id="customerSelectContainer" style="display:none;">
                                <div class="nt-section-header">
                                    <div class="nt-section-title"><i data-lucide="building-2"></i>Kunde / Objekt / Produkt
                                    </div><span class="nt-section-badge">CRM</span>
                                </div>
                                <div class="nt-row">
                                    <div><label class="nt-field-label">Kunde suchen</label><select name="customer_id"
                                            id="customer_id" class="nt-select"></select></div>
                                    <div><label class="nt-field-label">Objekt</label><select name="alternative_id"
                                            id="alternative_id" class="nt-select">
                                            <option value="">Zuerst Kunde wählen</option>
                                        </select></div>
                                </div>
                                <div class="nt-row">
                                    <div><label class="nt-field-label">Produkt</label><select name="product_id"
                                            id="product_id" class="nt-select">
                                            <option value="">Produkt wählen</option>@foreach($taskProducts as $product)
                                                <option value="{{ data_get($product, 'id') }}">
                                                    {{ data_get($product, 'article_group') ?? data_get($product, 'name') ?? ('#' . data_get($product, 'id')) }}
                                            </option>@endforeach
                                        </select></div>
                                    <div><label class="nt-field-label">Kundeninfo</label>
                                        <div class="nt-chip-row" id="customerInfoChips"><span class="nt-chip">Kein Kunde
                                                gewählt</span></div>
                                    </div>
                                </div>
                                <div class="nt-row">
                                    <div>
                                        <label class="nt-field-label">Lead Stage</label>
                                        <select name="lead_stage_id" id="lead_stage_id" class="nt-select">
                                            <option value="">Stage automatisch übernehmen</option>
                                            @foreach($taskLeadStagePayload as $stage)
                                                <option value="{{ data_get($stage, 'id') }}"
                                                    data-color="{{ data_get($stage, 'color') }}">
                                                    {{ data_get($stage, 'name') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="nt-field-label">Sub Stage</label>
                                        <select name="lead_stage_sub_stage_id" id="lead_stage_sub_stage_id"
                                            class="nt-select">
                                            <option value="">Zuerst Stage wählen</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="nt-stage-preview">
                                    <div class="nt-chip-row" id="leadStageInfoChips">
                                        <span class="nt-chip">Stage wird automatisch vom Kundenprodukt übernommen</span>
                                    </div>
                                </div>
                            </div>

                            <div class="nt-section">
                                <div class="nt-section-header">
                                    <div class="nt-section-title"><i data-lucide="list-checks"></i>Aufgaben-Schlüssel</div>
                                    <button type="button" class="nt-btn nt-btn-primary add-task-steps"><i
                                            data-lucide="plus"></i>Schritt</button>
                                </div>
                                <input type="hidden" name="step_employee_mode" id="step_employee_mode" value="all">
                                <div class="pt-step-mode" id="stepEmployeeModeBox">
                                    <label><input type="radio" name="step_employee_mode_radio" value="all" checked> Gleiche
                                        Mitarbeiter für alle Schritte</label>
                                    <label><input type="radio" name="step_employee_mode_radio" value="per_step"> Jeder
                                        Schritt hat eigene Mitarbeiter</label>
                                    <small>Bei eigenen Schritt-Mitarbeitern wird die große Mitarbeiter-Auswahl oben
                                        ausgeblendet und pro Schritt gespeichert.</small>
                                </div>
                                <div style="overflow-x:auto;">
                                    <table id="key_task">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Aufgabenschritt</th>
                                                <th>Dauer<br><small id="key_total_time">0.00 Stunden</small></th>
                                                <th>Zugewiesen</th>
                                                <th>Status</th>
                                                <th>Beschreibung</th>
                                                <th>Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="nt-section">
                                <div class="nt-section-header">
                                    <div class="nt-section-title"><i data-lucide="users"></i>Zuweisung</div><span
                                        class="nt-section-badge">Team</span>
                                </div>
                                <div class="nt-form-group pt-global-employee-box" id="globalEmployeeBox"
                                    style="margin-bottom:12px;"><label class="nt-field-label">Mitarbeiter</label><select
                                        name="employee[]" id="employee" class="nt-select"
                                        multiple>@foreach($taskEmployees as $employee)<option
                                            value="{{ data_get($employee, 'id') }}">
                                            {{ trim((data_get($employee, 'name') ?? '') . ' ' . (data_get($employee, 'lastname') ?? '')) }}
                                        </option>@endforeach</select></div>
                                <div class="nt-form-group" style="margin-bottom:12px;"><label
                                        class="nt-field-label">Controller / Kontrolle</label><select name="controller[]"
                                        id="controller" class="nt-select" multiple>@foreach($taskEmployees as $employee)
                                            <option value="{{ data_get($employee, 'id') }}">
                                                {{ trim((data_get($employee, 'name') ?? '') . ' ' . (data_get($employee, 'lastname') ?? '')) }}
                                        </option>@endforeach
                                    </select></div>
                                <div class="nt-form-group"><label class="nt-field-label">Team</label><select name="team_id"
                                        id="team_id" class="nt-select">
                                        <option value="">Kein Team</option>@foreach($taskTeams as $team)<option
                                            value="{{ data_get($team, 'id') }}">
                                            {{ data_get($team, 'name') ?? data_get($team, 'team_name') ?? ('Team #' . data_get($team, 'id')) }}
                                        </option>@endforeach
                                    </select></div>
                            </div>

                            <div class="nt-section">
                                <div class="nt-section-header">
                                    <div class="nt-section-title"><i data-lucide="repeat"></i>Wiederholung / Erinnerung
                                    </div>
                                </div>
                                <div class="nt-switch-row">
                                    <label class="nt-switch-label"><input type="checkbox" id="repeated" name="repeat"
                                            value="daily"> Wiederholen</label>
                                    <label class="nt-switch-label"><input type="checkbox" id="reminder_check" value="1">
                                        Erinnerung</label>
                                </div>
                                <div class="repeated_area" style="display:none;margin-top:12px;"><label
                                        class="nt-field-label">Wiederholung</label><select name="repeat_type"
                                        id="repeat_type" class="nt-select">
                                        <option value="daily">Täglich</option>
                                        <option value="weekly">Wöchentlich</option>
                                        <option value="monthly">Monatlich</option>
                                        <option value="yearly">Jährlich</option>
                                    </select></div>
                                <div class="reminder_area" style="display:none;margin-top:12px;">
                                    <div class="nt-row">
                                        <div><label class="nt-field-label">Erinnerungsdatum</label><input type="date"
                                                name="reminder_date" id="reminder_date" class="nt-input"></div>
                                        <div><label class="nt-field-label">Erinnerungszeit</label><input type="time"
                                                name="reminder_time" id="reminder_time" class="nt-input"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="nt-section">
                                <div class="nt-section-header">
                                    <div class="nt-section-title"><i data-lucide="info"></i>Hinweise</div>
                                </div>
                                <div class="nt-chip-row">
                                    <span class="nt-chip"><strong>Meine Jobs:</strong> Aufgaben, die mir zugewiesen
                                        sind</span>
                                    <span class="nt-chip"><strong>Erstellt von mir:</strong> Aufgaben, die ich verteilt
                                        habe</span>
                                    <span class="nt-chip"><strong>Pause:</strong> bleibt im Board mit Blur und Grund</span>
                                    <span class="nt-chip"><strong>CRM Stage:</strong> wird vom Kundenprodukt übernommen und
                                        kann manuell angepasst werden</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nt-footer">
                    <button type="button" class="nt-btn nt-btn-ghost close_task_window"><i
                            data-lucide="x"></i>Abbrechen</button>
                    <button type="button" class="nt-btn nt-btn-primary save-task-continue"><i
                            data-lucide="save"></i>Speichern & weiter</button>
                    <button type="button" class="nt-btn nt-btn-primary save-task-close"><i data-lucide="check"></i>Speichern
                        & schließen</button>
                </div>
            </form>
        </div>
    </div>

    <div class="pt-modal-backdrop" id="ptReasonModal">
        <div class="pt-modal">
            <div class="pt-modal-head">
                <div class="pt-modal-title" id="ptReasonTitle">Grund angeben</div><button type="button" class="pt-icon-btn"
                    data-close-reason><i data-lucide="x"></i></button>
            </div>
            <div class="pt-modal-body">
                <input type="hidden" id="ptReasonTaskId"><input type="hidden" id="ptReasonAction">
                <label class="pt-label">Grund</label><textarea class="nt-textarea" id="ptReasonText"
                    placeholder="Bitte Grund eingeben..."></textarea>
            </div>
            <div class="pt-modal-foot"><button type="button" class="pt-btn pt-btn-soft"
                    data-close-reason>Abbrechen</button><button type="button" class="pt-btn pt-btn-primary"
                    id="ptReasonSubmit"><i data-lucide="check"></i>Speichern</button></div>
        </div>
    </div>

    <div class="pt-toast" id="ptToast"><i data-lucide="check-circle-2"></i><span id="ptToastText">Gespeichert</span></div>
@endsection

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        (function () {
            'use strict';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const hasStoreRoute = @json((bool) $storeRoute);
            const routes = {
                ajaxTasks: @json(route('personal-tasks.ajax.tasks')),
                ajaxStats: @json(route('personal-tasks.ajax.stats')),
                customersSearch: @json(route('personal-tasks.customers.search')),
                leadStageContext: @json(Route::has('personal-tasks.lead-stage-context') ? route('personal-tasks.lead-stage-context') : '#'),
                store: @json($storeRoute),
                update: @json($updateRoute),
                edit: @json(route('personal-tasks.personal.task.edit', ['id' => '__TASK__'])),
                status: @json(route('personal-tasks.status', ['task' => '__TASK__'])),
                archive: @json(route('personal-tasks.archive', ['task' => '__TASK__'])),
                destroy: @json(route('personal-tasks.destroy', ['task' => '__TASK__'])),
                restore: @json(route('personal-tasks.restore', ['task' => '__TASK__'])),
                pause: @json(route('personal-tasks.pause', ['task' => '__TASK__'])),
                resume: @json(route('personal-tasks.resume', ['task' => '__TASK__'])),
                cancel: @json(route('personal-tasks.cancel', ['task' => '__TASK__'])),
                reject: @json(route('personal-tasks.reject', ['task' => '__TASK__'])),
                accept: @json(route('personal-tasks.accept', ['task' => '__TASK__'])),
                color: @json(route('personal-tasks.color', ['task' => '__TASK__'])),
                profile: @json(Route::has('personal-tasks.profile') ? route('personal-tasks.profile', ['task' => '__TASK__']) : '#'),
                commentsStore: @json(Route::has('personal-tasks.comments.store') ? route('personal-tasks.comments.store', ['task' => '__TASK__']) : '#'),
                appointmentConflicts: @json(Route::has('personal-tasks.appointment-conflicts') ? route('personal-tasks.appointment-conflicts') : '#'),
            };

            const leadStageOptions = @json($taskLeadStagePayload);
            const state = { view: 'board', scope: 'my', state: 'active', search: '', priority: '', due: 'all', employee: '', leadStage: '', leadSubStage: '', tasks: [], stats: {}, draggingTaskId: null };
            const columns = [{ key: 'open', label: 'Offen', icon: 'circle' }, { key: 'in_progress', label: 'In Bearbeitung', icon: 'loader' }, { key: 'completed', label: 'Erledigt', icon: 'check-circle-2' }];
            const els = {
                board: document.getElementById('ptBoard'), boardView: document.getElementById('ptBoardView'), listView: document.getElementById('ptListView'), listBody: document.getElementById('ptListBody'), mainTitle: document.getElementById('ptMainTitle'), resultText: document.getElementById('ptResultText'), search: document.getElementById('ptSearchInput'), priority: document.getElementById('ptPriorityFilter'), due: document.getElementById('ptDueFilter'), employee: document.getElementById('ptEmployeeFilter'), leadStage: document.getElementById('ptLeadStageFilter'), leadSubStage: document.getElementById('ptLeadSubStageFilter'), refresh: document.getElementById('ptRefreshBtn'), more: document.getElementById('ptMoreDropdown'), moreBtn: document.getElementById('ptMoreBtn'), toast: document.getElementById('ptToast'), toastText: document.getElementById('ptToastText'), reasonModal: document.getElementById('ptReasonModal'), reasonTaskId: document.getElementById('ptReasonTaskId'), reasonAction: document.getElementById('ptReasonAction'), reasonText: document.getElementById('ptReasonText'), reasonTitle: document.getElementById('ptReasonTitle'), listOnlyNote: document.getElementById('ptListOnlyNote')
            };

            function route(name, taskId) { return (routes[name] || '').replace('__TASK__', taskId); }
            function esc(value) { return String(value ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;'); }
            function icon(name) { return `<i data-lucide="${name}"></i>`; }
            function refreshIcons() { if (window.lucide) { window.lucide.createIcons(); } }
            function stripHtml(html) { const div = document.createElement('div'); div.innerHTML = html || ''; return div.textContent || div.innerText || ''; }
            function toast(message, type = 'success') { els.toast.classList.remove('success', 'error'); els.toast.classList.add('is-open', type); els.toastText.textContent = message; setTimeout(() => els.toast.classList.remove('is-open'), 2500); refreshIcons(); }
            function debounce(fn, delay = 350) { let timer; return function (...args) { clearTimeout(timer); timer = setTimeout(() => fn.apply(this, args), delay); }; }
            function formData(data) { const fd = new FormData(); Object.entries(data).forEach(([key, value]) => fd.append(key, value)); return fd; }
            async function requestJson(url, options = {}) {
                const response = await fetch(url, { headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', ...(options.headers || {}) }, ...options });
                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json') ? await response.json().catch(() => ({})) : { success: response.ok, html: await response.text().catch(() => '') };
                if (!response.ok || data.success === false) {
                    const error = new Error(data.message || 'Serverfehler');
                    error.status = response.status;
                    error.errors = data.errors || {};
                    error.response = data;
                    throw error;
                }
                return data;
            }

            function queryString() {
                const params = new URLSearchParams({
                    view: state.view,
                    scope: state.scope,
                    state: state.state,
                    search: state.search,
                    priority: state.priority,
                    due: state.due,
                    due_filter: state.due,
                    employee: state.employee,
                    employee_id: state.employee,
                });

                if (state.leadStage) {
                    params.set('lead_stage_id', state.leadStage);
                }

                if (state.leadSubStage) {
                    params.set('lead_stage_sub_stage_id', state.leadSubStage);
                }

                return params.toString();
            }
            function setLoading() { if (state.view === 'board') { els.board.innerHTML = '<div class="pt-loader">Lade Aufgaben...</div>'; } else { els.listBody.innerHTML = '<tr><td colspan="10">Lade Aufgaben...</td></tr>'; } }
            async function loadTasks() { setLoading(); try { const data = await requestJson(`${routes.ajaxTasks}?${queryString()}`); state.tasks = sortTasks(data.tasks || []); state.stats = data.stats || {}; renderStats(); render(); } catch (error) { els.board.innerHTML = `<div class="pt-empty">${esc(error.message)}</div>`; els.listBody.innerHTML = `<tr><td colspan="10">${esc(error.message)}</td></tr>`; toast(error.message, 'error'); } }
            function priorityRank(priority) { const value = String(priority || 'medium').toLowerCase().replace(/\s+/g, '_').replace(/-/g, '_'); return { 'very_high': 1, 'high': 2, 'medium': 3, 'normal': 4, 'low': 5 }[value] || 4; }
            function normalizePriorityForBlade(value) {
                const raw = String(value || '').trim().toLowerCase().replace(/[_.-]+/g, ' ').replace(/\s+/g, ' ');
                const map = {
                    'keiner': 'normal', 'none': 'normal', 'normal': 'normal', 'no priority': 'normal',
                    'mittel': 'medium', 'medium': 'medium', 'med': 'medium',
                    'hoch': 'high', 'high': 'high',
                    'sehr hoch': 'very high', 'sehr wichtig': 'very high', 'very high': 'very high', 'urgent': 'very high', 'dringend': 'very high', 'very_high': 'very high',
                    'low': 'normal', 'niedrig': 'normal'
                };
                return map[raw] || 'medium';
            }
            function priorityLabel(value) {
                const v = normalizePriorityForBlade(value);
                return { normal: 'Keiner', medium: 'Mittel', high: 'Hoch', 'very high': 'Sehr wichtig' }[v] || 'Mittel';
            }
            function setPriorityValue(value) {
                const normalized = normalizePriorityForBlade(value);
                const input = document.getElementById('priority');
                if (input) input.value = normalized;
                const label = document.querySelector('#priority_select .nt-priority-label');
                if (label) label.textContent = priorityLabel(normalized);
                return normalized;
            }
            function dueRank(task) { if (!task.due_date) return 9999999999999; const time = task.due_time || '23:59:59'; const stamp = Date.parse(`${task.due_date}T${time}`); return Number.isNaN(stamp) ? 9999999999999 : stamp; }
            function sortTasks(tasks) { return [...tasks].sort((a, b) => { const p = priorityRank(a.priority) - priorityRank(b.priority); if (p !== 0) return p; const d = dueRank(a) - dueRank(b); if (d !== 0) return d; return Number(b.id || 0) - Number(a.id || 0); }); }
            function statValue(key) {
                const aliases = { pause: 'paused', paused: 'pause' };
                if (state.stats && Object.prototype.hasOwnProperty.call(state.stats, key)) return state.stats[key] ?? 0;
                const alias = aliases[key];
                if (alias && state.stats && Object.prototype.hasOwnProperty.call(state.stats, alias)) return state.stats[alias] ?? 0;
                return 0;
            }

            function renderStats() {
                document.querySelectorAll('[data-stat]').forEach(el => {
                    const key = el.getAttribute('data-stat');
                    el.textContent = statValue(key);
                });
                document.querySelectorAll('[data-tab-count]').forEach(el => {
                    const key = el.getAttribute('data-tab-count');
                    el.textContent = statValue(key);
                });
            }
            function isListOnlyState() { return !['active', 'my', 'created'].includes(String(state.state || 'active')); }
            function stateLabel() {
                return { active: 'Aktive Aufgaben', pause: 'Pausierte Aufgaben', cancel: 'Abgebrochene Aufgaben', archived: 'Archivierte Aufgaben', deleted: 'Gelöschte Aufgaben', rejected: 'Abgelehnte Aufgaben' }[state.state] || 'Aufgaben';
            }
            function forceListForManagedState() {
                if (!isListOnlyState()) return;
                state.view = 'list';
                document.querySelectorAll('#ptViewTabs .pt-tab').forEach(b => b.classList.toggle('is-active', b.dataset.view === 'list'));
            }
            function render() {
                forceListForManagedState();
                els.resultText.textContent = `${state.tasks.length} Aufgaben gefunden`;
                if (els.listOnlyNote) els.listOnlyNote.classList.toggle('is-open', isListOnlyState());
                if (state.view === 'board') {
                    els.mainTitle.textContent = 'Kanban Ansicht';
                    els.boardView.classList.remove('d-none');
                    els.listView.classList.add('d-none');
                    renderBoard();
                } else {
                    els.mainTitle.textContent = isListOnlyState() ? `${stateLabel()} · Listenverwaltung` : 'Listen Ansicht';
                    els.boardView.classList.add('d-none');
                    els.listView.classList.remove('d-none');
                    renderList();
                }
                refreshIcons();
            }
            function boardColumn(task) { if (task.task_status === 'completed') return 'completed'; if (['on_progress', 'on_going', 'working', 'in_progress'].includes(task.task_status)) return 'in_progress'; return 'open'; }
            function isActiveKanbanTask(task) {
                if (task.deleted_at || task.archived_at) return false;
                return !['pause', 'cancel', 'junk', 'rejected'].includes(String(task.task_status || '').toLowerCase());
            }
            function renderBoard() {
                const activeTasks = state.tasks.filter(isActiveKanbanTask);
                const grouped = { open: [], in_progress: [], completed: [] };
                activeTasks.forEach(task => grouped[boardColumn(task)].push(task));
                els.board.innerHTML = columns.map(column => { const tasks = grouped[column.key] || []; return `<section class="pt-column" data-column="${column.key}"><div class="pt-column-head"><div class="pt-column-title">${icon(column.icon)}${esc(column.label)}</div><div class="pt-column-count">${tasks.length}</div></div><div class="pt-column-body" data-drop-column="${column.key}">${tasks.length ? tasks.map(renderCard).join('') : '<div class="pt-empty">Keine aktiven Aufgaben</div>'}</div></section>`; }).join('');
            }
            function profileUrl(taskId) { return route('profile', taskId); }

            function cleanStageColor(color, fallback = '#74b2d4') {
                const value = String(color || '').trim();
                return /^#[0-9a-f]{3,8}$/i.test(value) ? value : fallback;
            }

            function getStageById(stageId) {
                return (leadStageOptions || []).find(stage => String(stage.id) === String(stageId)) || null;
            }

            function getSubStageById(stage, subStageId) {
                if (!stage || !Array.isArray(stage.sub_stages)) return null;
                return stage.sub_stages.find(subStage => String(subStage.id) === String(subStageId)) || null;
            }

            function destroySelect2IfNeeded(selector) {
                if (!window.jQuery || !$.fn.select2) return;
                const $el = $(selector);
                if ($el.hasClass('select2-hidden-accessible')) $el.select2('destroy');
            }

            function initDrawerSelect2(selector, options = {}) {
                if (!window.jQuery || !$.fn.select2) return;
                $(selector).select2({ width: '100%', dropdownParent: $('#newTaskDrawer'), ...options });
            }

            function setSubStageOptions(stageId, selectedSubStageId = '') {
                const select = document.getElementById('lead_stage_sub_stage_id');
                if (!select) return;

                const stage = getStageById(stageId);
                const subStages = stage && Array.isArray(stage.sub_stages) ? stage.sub_stages : [];

                destroySelect2IfNeeded('#lead_stage_sub_stage_id');
                select.innerHTML = '<option value="">Sub Stage automatisch übernehmen</option>';

                subStages.forEach(subStage => {
                    const option = new Option(subStage.name || ('Sub Stage #' + subStage.id), subStage.id);
                    option.dataset.color = subStage.color || '#93c21c';
                    option.dataset.key = subStage.key || '';
                    select.appendChild(option);
                });

                if (selectedSubStageId && subStages.some(item => String(item.id) === String(selectedSubStageId))) {
                    select.value = String(selectedSubStageId);
                }

                initDrawerSelect2('#lead_stage_sub_stage_id', { placeholder: 'Sub Stage wählen', allowClear: true });
            }

            function filterSubStagesForStage(stageId) {
                const stage = getStageById(stageId);
                if (!stage) return [];
                return Array.isArray(stage.sub_stages) ? stage.sub_stages : [];
            }

            function rebuildFilterSubStageSelect(subStages = [], selectedSubStageId = '', stageId = '') {
                const select = document.getElementById('ptLeadSubStageFilter');
                if (!select) return;

                if (window.jQuery && $.fn.select2) {
                    const $select = $('#ptLeadSubStageFilter');
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                }

                select.innerHTML = '<option value="">Alle Sub Stages</option>';

                (subStages || []).forEach(subStage => {
                    const option = new Option(subStage.name || ('Sub Stage #' + subStage.id), subStage.id);
                    option.dataset.color = subStage.color || '#93c21c';
                    option.dataset.key = subStage.key || '';
                    select.appendChild(option);
                });

                select.disabled = !stageId || !(subStages || []).length;

                if (selectedSubStageId && (subStages || []).some(item => String(item.id) === String(selectedSubStageId))) {
                    select.value = String(selectedSubStageId);
                } else {
                    select.value = '';
                }

                if (window.jQuery && $.fn.select2) {
                    $('#ptLeadSubStageFilter')
                        .prop('disabled', select.disabled)
                        .select2({
                            width: '100%',
                            placeholder: select.disabled ? 'Keine Sub Stages' : 'Alle Sub Stages',
                            allowClear: true,
                        })
                        .trigger('change.select2');
                }
            }

            function setFilterSubStageOptions(stageId, selectedSubStageId = '') {
                const subStages = filterSubStagesForStage(stageId);
                rebuildFilterSubStageSelect(subStages, selectedSubStageId, stageId);
                return subStages;
            }

            async function loadFilterSubStagesForStage(stageId, selectedSubStageId = '') {
                const localSubStages = setFilterSubStageOptions(stageId, selectedSubStageId);

                if (!stageId || localSubStages.length) {
                    return localSubStages;
                }

                if (!routes.leadStageContext || routes.leadStageContext === '#') {
                    return [];
                }

                try {
                    const data = await requestJson(`${routes.leadStageContext}?lead_stage_id=${encodeURIComponent(stageId)}`);
                    const remoteSubStages = Array.isArray(data.sub_stage_options) ? data.sub_stage_options : [];

                    if (remoteSubStages.length) {
                        const stage = getStageById(stageId);
                        if (stage) {
                            stage.sub_stages = remoteSubStages;
                        }
                        rebuildFilterSubStageSelect(remoteSubStages, selectedSubStageId, stageId);
                    }

                    return remoteSubStages;
                } catch (error) {
                    console.warn('Filter sub stages could not be loaded:', error);
                    return [];
                }
            }

            let filterStageTimer = null;

            function handleFilterStageChanged() {
                window.clearTimeout(filterStageTimer);
                filterStageTimer = window.setTimeout(async () => {
                    state.leadStage = document.getElementById('ptLeadStageFilter')?.value || '';
                    state.leadSubStage = '';
                    await loadFilterSubStagesForStage(state.leadStage, '');
                    loadTasks();
                }, 40);
            }

            function handleFilterSubStageChanged() {
                state.leadSubStage = document.getElementById('ptLeadSubStageFilter')?.value || '';
                loadTasks();
            }

            function currentLeadStageContextFromSelects() {
                const stageId = document.getElementById('lead_stage_id')?.value || '';
                const subStageId = document.getElementById('lead_stage_sub_stage_id')?.value || '';
                const stage = getStageById(stageId);
                const subStage = getSubStageById(stage, subStageId);

                return {
                    lead_stage_id: stage?.id || stageId || '',
                    lead_stage_name: stage?.name || '',
                    lead_stage_color: stage?.color || '#74b2d4',
                    lead_stage_sub_stage_id: subStage?.id || subStageId || '',
                    lead_stage_sub_stage_name: subStage?.name || '',
                    lead_stage_sub_stage_color: subStage?.color || '#93c21c',
                };
            }

            function updateLeadStageChips(context = null) {
                const chips = document.getElementById('leadStageInfoChips');
                if (!chips) return;

                const data = context || currentLeadStageContextFromSelects();
                const stageName = data.lead_stage_name || 'Keine Stage';
                const subStageName = data.lead_stage_sub_stage_name || 'Keine Sub Stage';
                const stageColor = cleanStageColor(data.lead_stage_color, '#74b2d4');
                const subStageColor = cleanStageColor(data.lead_stage_sub_stage_color, '#93c21c');

                chips.innerHTML = `
                            <span class="nt-chip" style="border-color:${esc(stageColor)}">${icon('git-branch')}${esc(stageName)}</span>
                            <span class="nt-chip" style="border-color:${esc(subStageColor)}">${icon('workflow')}${esc(subStageName)}</span>
                        `;
                refreshIcons();
            }

            function renderLeadStageBadge(task) {
                const stageName = task.lead_stage_name || task.lead_stage_context?.lead_stage_name || '';
                const subStageName = task.lead_stage_sub_stage_name || task.lead_stage_context?.lead_stage_sub_stage_name || '';
                if (!stageName && !subStageName) return '';

                const stageColor = cleanStageColor(task.lead_stage_color || task.lead_stage_context?.lead_stage_color, '#74b2d4');
                const subStageColor = cleanStageColor(task.lead_stage_sub_stage_color || task.lead_stage_context?.lead_stage_sub_stage_color, '#93c21c');

                return `<span class="pt-pill pt-stage-pill" style="border-color:${esc(stageColor)}">
                            ${icon('git-branch')}${esc(stageName || 'Stage')}
                            ${subStageName ? `<small style="border-left:3px solid ${esc(subStageColor)}">${esc(subStageName)}</small>` : ''}
                        </span>`;
            }

            function applyLeadStageContext(context = {}) {
                const leadProductInput = document.getElementById('lead_product_list_id');
                const stageSelect = document.getElementById('lead_stage_id');
                const subStageSelect = document.getElementById('lead_stage_sub_stage_id');

                if (leadProductInput && context.lead_product_list_id) leadProductInput.value = context.lead_product_list_id;

                if (stageSelect) {
                    const stageId = context.lead_stage_id || '';
                    stageSelect.value = stageId ? String(stageId) : '';
                    setSubStageOptions(stageSelect.value, context.lead_stage_sub_stage_id || '');
                    if (window.jQuery && $.fn.select2) $('#lead_stage_id').trigger('change.select2');
                }

                if (subStageSelect && context.lead_stage_sub_stage_id) {
                    subStageSelect.value = String(context.lead_stage_sub_stage_id);
                    if (window.jQuery && $.fn.select2) $('#lead_stage_sub_stage_id').trigger('change.select2');
                }

                updateLeadStageChips(context);
            }

            async function loadLeadStageContext(preferred = {}) {
                const stageSelect = document.getElementById('lead_stage_id');
                const subStageSelect = document.getElementById('lead_stage_sub_stage_id');
                const stageId = preferred.lead_stage_id ?? stageSelect?.value ?? '';
                const subStageId = preferred.lead_stage_sub_stage_id ?? subStageSelect?.value ?? '';

                if (stageId) {
                    setSubStageOptions(stageId, subStageId);
                    updateLeadStageChips();
                }

                if (!routes.leadStageContext || routes.leadStageContext === '#') return;

                const params = new URLSearchParams();
                const leadProductListId = preferred.lead_product_list_id ?? document.getElementById('lead_product_list_id')?.value ?? '';
                const customerId = preferred.customer_id ?? document.getElementById('customer_id')?.value ?? '';
                const alternativeId = preferred.alternative_id ?? document.getElementById('alternative_id')?.value ?? '';
                const productId = preferred.product_id ?? document.getElementById('product_id')?.value ?? '';

                if (leadProductListId) params.set('lead_product_list_id', leadProductListId);
                if (customerId) params.set('customer_id', customerId);
                if (alternativeId) params.set('alternative_id', alternativeId);
                if (productId) params.set('product_id', productId);
                if (stageId) params.set('lead_stage_id', stageId);
                if (subStageId) params.set('lead_stage_sub_stage_id', subStageId);
                if (!params.toString()) return;

                try {
                    const data = await requestJson(`${routes.leadStageContext}?${params.toString()}`);
                    applyLeadStageContext(data.context || {});
                } catch (error) {
                    console.warn('Lead stage context could not be loaded:', error);
                }
            }
            function dueState(task) {
                if (!task.due_date) return 'none';
                const time = task.due_time || '23:59:59';
                const due = new Date(`${task.due_date}T${time}`);
                if (Number.isNaN(due.getTime())) return 'none';
                const now = new Date();
                const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                const dueDay = new Date(due.getFullYear(), due.getMonth(), due.getDate());
                if (due < now && task.task_status !== 'completed') return 'overdue';
                if (dueDay.getTime() === today.getTime()) return 'today';
                return 'future';
            }
            function dueCardClass(task) { const state = dueState(task); return state === 'overdue' ? 'is-due-overdue' : (state === 'today' ? 'is-due-today' : ''); }
            function taskEmployeeImage(employee) {
                const raw = employee.image_url || employee.image || '';
                if (!raw) return '';
                if (/^https?:\/\//i.test(raw) || raw.startsWith('/')) return raw;
                const clean = raw.replace(/^\/+/, '');
                if (clean.includes('images/employee/')) return '/' + clean;
                return '/images/employee/' + clean;
            }
            function renderCard(task) {
                const paused = task.task_status === 'pause';
                const color = task.color || '#93c21c';
                const progress = Math.max(0, Math.min(100, Number(task.progress || 0)));
                const commentsCount = Number(task.comments_count || (task.comments ? task.comments.length : 0));
                return `<article class="pt-task-card ${paused ? 'is-paused' : ''} ${dueCardClass(task)}" style="--card-color:${esc(color)}" data-task-id="${task.id}" draggable="${paused ? 'false' : 'true'}">
                                    ${paused ? renderPauseLayer(task) : ''}
                                    <div class="pt-card-inner">
                                        <div class="pt-card-top">
                                            <div>
                                                <h3 class="pt-card-title">${esc(task.task_title || 'Ohne Titel')}</h3>
                                                <div class="pt-card-code">${esc(task.task_id || ('#' + task.id))}</div>
                                            </div>
                                            <div class="pt-card-actions">
                                                <a class="pt-icon-btn profile" href="${esc(profileUrl(task.id))}" title="Profil öffnen">${icon('external-link')}</a>
                                                <button type="button" class="pt-icon-btn comments" data-action="toggle-comments" data-id="${task.id}" title="Kommentare">${icon('message-circle')}<b>${commentsCount}</b></button>
                                                <button type="button" class="pt-icon-btn" data-action="edit" data-id="${task.id}" title="Bearbeiten">${icon('pencil')}</button>
                                                ${renderActionDropdown(task)}
                                            </div>
                                        </div>
                                        <div class="pt-card-meta">
                                            ${renderPriority(task.priority)}${renderDue(task)}${renderLeadStageBadge(task)}
                                            ${task.customer_name ? `<span class="pt-pill">${icon('building-2')}${esc(task.customer_name)}</span>` : ''}
                                            ${task.object_name ? `<span class="pt-pill">${icon('map-pin')}${esc(task.object_name)}</span>` : ''}
                                            ${task.product_name ? `<span class="pt-pill">${icon('package')}${esc(task.product_name)}</span>` : ''}
                                        </div>
                                        ${task.description ? `<div class="pt-desc">${esc(stripHtml(task.description)).slice(0, 150)}</div>` : ''}
                                        ${renderRejectedReasons(task)}
                                        <div class="pt-progress"><div class="pt-progress-bar" style="width:${progress}%"></div></div>
                                        <div class="pt-card-footer">${renderAvatars(task.employees || [])}<span class="pt-pill">${icon('list-checks')}${task.keys_count || (task.keys ? task.keys.length : 0)} Schritte</span></div>
                                        <div class="pt-card-comments" data-comments-for="${task.id}">${renderComments(task)}</div>
                                    </div>
                                </article>`;
            }
            function renderComments(task) {
                const comments = task.comments || [];
                const list = comments.length ? comments.map(comment => {
                    const author = comment.author || comment.employee || {};
                    const name = `${author.name || ''} ${author.lastname || ''}`.trim() || 'Unbekannt';
                    const img = taskEmployeeImage(author);
                    return `<div class="pt-comment-box"><div class="pt-comment-head"><span class="pt-comment-author">${img ? `<img src="${esc(img)}" alt="${esc(name)}">` : ''}${esc(name)}</span><span>${esc(comment.created_at || '')}</span></div><div class="pt-comment-text">${comment.comment || ''}</div></div>`;
                }).join('') : '<div class="pt-empty" style="padding:12px">Noch keine Kommentare</div>';
                return `${list}<form class="pt-comment-form" data-comment-form="${task.id}"><textarea name="comment" placeholder="Kommentar schreiben..."></textarea><button type="submit">${icon('send')}</button></form>`;
            }
            function renderPauseLayer(task) { return `<div class="pt-pause-layer">${icon('pause-circle')}<strong>Pausiert</strong><span>${esc(task.pause_reason || task.reason || task.change_reason || 'Kein Grund angegeben')}</span><button type="button" class="pt-btn pt-btn-soft" data-action="resume" data-id="${task.id}">${icon('play')}Fortsetzen</button></div>`; }
            function renderPriority(priority) { const clean = normalizePriorityForBlade(priority); const value = clean.replace(' ', '-'); const labels = { 'very-high': 'Sehr wichtig', high: 'Hoch', medium: 'Mittel', normal: 'Keiner' }; const icons = { 'very-high': 'siren', high: 'flame', medium: 'signal-medium', normal: 'minus-circle' }; return `<span class="pt-pill priority-${esc(value)}">${icon(icons[value] || 'signal-medium')}${esc(labels[value] || 'Mittel')}</span>`; }
            function renderDue(task) { if (!task.due_date) return `<span class="pt-pill">${icon('calendar-x')}Ohne Datum</span>`; let text = task.due_date; if (task.due_time) text += ` ${String(task.due_time).slice(0, 5)} Uhr`; const state = dueState(task); const cls = state === 'overdue' ? 'pt-due-overdue pt-due-animated' : (state === 'today' ? 'pt-due-today pt-due-animated' : ''); const label = state === 'overdue' ? 'Überfällig' : (state === 'today' ? 'Heute fällig' : 'Fällig'); return `<span class="pt-pill ${cls}">${icon(state === 'overdue' ? 'alarm-clock' : (state === 'today' ? 'calendar-clock' : 'calendar'))}${esc(label)}: ${esc(text)}</span>`; }
            function renderRejectedReasons(task) { const items = task.rejected_employees || []; if (!items.length) return ''; return `<div class="pt-reject-note"><strong>${icon('ban')}Abgelehnte Mitarbeiter</strong>${items.map(item => `<div><small>${esc(item.employee_name || 'Mitarbeiter')}</small>: ${esc(item.reason || 'Kein Grund angegeben')}</div>`).join('')}</div>`; }
            function renderAvatars(employees) {
                if (!employees.length) return '<div class="pt-avatars"><span class="pt-avatar">?</span></div>';
                return `<div class="pt-avatars">${employees.slice(0, 4).map(employee => {
                    const name = `${employee.name || ''} ${employee.lastname || ''}`.trim();
                    const initials = name ? name.split(' ').map(part => part[0]).join('').slice(0, 2).toUpperCase() : '?';
                    const image = taskEmployeeImage(employee);
                    if (image) { return `<span class="pt-avatar" title="${esc(name)}"><img src="${esc(image)}" alt="${esc(name)}" onerror="this.closest('.pt-avatar').textContent='${esc(initials)}';this.remove();"></span>`; }
                    return `<span class="pt-avatar" title="${esc(name)}">${esc(initials)}</span>`;
                }).join('')}</div>`;
            }
            function renderActionDropdown(task) {
                const isArchived = !!task.archived_at;
                const isDeleted = !!task.deleted_at;
                const status = String(task.task_status || 'open').toLowerCase();
                const isPaused = status === 'pause';
                const isCancelled = status === 'cancel';
                const isManaged = isArchived || isDeleted || isPaused || isCancelled || status === 'rejected';

                let managedActions = '';
                if (isManaged) {
                    managedActions = `
                                    <button type="button" class="restore-open" data-action="restore-status" data-status="open" data-id="${task.id}">${icon('rotate-ccw')}Wiederherstellen: Offen</button>
                                    <button type="button" class="restore-progress" data-action="restore-status" data-status="on_progress" data-id="${task.id}">${icon('play')}Wiederherstellen: In Bearbeitung</button>
                                `;
                }

                return `<div class="pt-action-dropdown"><button type="button" class="pt-icon-btn" data-action="toggle-menu">${icon('more-vertical')}</button><div class="pt-action-menu">
                                <button type="button" data-action="edit" data-id="${task.id}">${icon('pencil')}Bearbeiten</button>
                                ${isManaged ? managedActions : `<button type="button" data-action="status" data-status="completed" data-id="${task.id}">${icon('check-circle-2')}Erledigt + Archivieren</button>`}
                                ${(!isManaged && !isPaused) ? `<button type="button" data-action="reason" data-reason-action="pause" data-id="${task.id}">${icon('pause-circle')}Pausieren</button>` : ''}
                                ${(!isManaged) ? `<button type="button" data-action="reason" data-reason-action="reject" data-id="${task.id}">${icon('ban')}Job ablehnen</button>` : ''}
                                ${(!isManaged) ? `<button type="button" data-action="reason" data-reason-action="cancel" data-id="${task.id}">${icon('x-circle')}Abbrechen</button>` : ''}
                                ${(!isManaged) ? `<button type="button" data-action="archive" data-id="${task.id}">${icon('archive')}Archivieren</button>` : ''}
                                ${isDeleted ? `<button type="button" data-action="restore-status" data-status="open" data-id="${task.id}">${icon('rotate-ccw')}Aus Papierkorb wiederherstellen</button>` : `<button type="button" class="danger" data-action="delete" data-id="${task.id}">${icon('trash-2')}Löschen</button>`}
                            </div></div>`;
            }
            function renderStatusPill(task) { const map = { open: ['Offen', 'circle'], on_progress: ['In Bearbeitung', 'loader'], in_progress: ['In Bearbeitung', 'loader'], completed: ['Erledigt', 'check-circle-2'], pause: ['Pausiert', 'pause-circle'], cancel: ['Abgebrochen', 'x-circle'], rejected: ['Abgelehnt', 'ban'] }; const item = map[task.task_status] || [task.task_status || 'Offen', 'circle']; return `<span class="pt-pill">${icon(item[1])}${esc(item[0])}</span>`; }
            function renderList() {
                if (!state.tasks.length) {
                    els.listBody.innerHTML = '<tr><td colspan="10"><div class="pt-empty">Keine Aufgaben gefunden</div></td></tr>';
                    return;
                }

                els.listBody.innerHTML = state.tasks.map(task => `
                            <tr class="${task.task_status === 'pause' ? 'pt-row-paused' : ''} ${(task.rejected_employees || []).length ? 'pt-row-rejected' : ''}">
                                <td>
                                    <strong>${esc(task.task_title || 'Ohne Titel')}</strong>
                                    <div class="pt-subtitle">${esc(task.task_id || ('#' + task.id))}</div>
                                    ${renderRejectedReasons(task)}
                                </td>
                                <td>${renderStatusPill(task)}</td>
                                <td>${renderPriority(task.priority)}</td>
                                <td>${renderDue(task)}</td>
                                <td>
                                    ${esc(task.customer_name || '-')}
                                    ${task.object_name ? `<div class="pt-subtitle">${esc(task.object_name)}</div>` : ''}
                                    ${task.product_name ? `<div class="pt-subtitle">${esc(task.product_name)}</div>` : ''}
                                </td>
                                <td>${renderLeadStageBadge(task) || '-'}</td>
                                <td>${renderAvatars(task.employees || [])}</td>
                                <td>${renderAvatars(task.controllers || [])}</td>
                                <td>
                                    <div class="pt-progress"><div class="pt-progress-bar" style="width:${Number(task.progress || 0)}%"></div></div>
                                    <div class="pt-subtitle">${Number(task.progress || 0)}%</div>
                                </td>
                                <td>${renderActionDropdown(task)}</td>
                            </tr>
                        `).join('');
            }

            async function updateStatus(taskId, status) { const data = await requestJson(route('status', taskId), { method: 'POST', body: formData({ status }) }); toast(status === 'completed' ? 'Aufgabe erledigt und automatisch archiviert' : (data.message || 'Status aktualisiert')); await loadTasks(); }
            async function archiveTask(taskId) { await requestJson(route('archive', taskId), { method: 'POST' }); toast('Aufgabe archiviert und aus dem Kanban entfernt'); await loadTasks(); }
            async function deleteTask(taskId) { if (!confirm('Aufgabe wirklich löschen?')) return; await requestJson(route('destroy', taskId), { method: 'DELETE' }); toast('Aufgabe gelöscht'); await loadTasks(); }
            async function restoreTask(taskId, status = 'open') { await requestJson(route('restore', taskId), { method: 'POST', body: formData({ status }) }); toast(status === 'on_progress' ? 'Aufgabe wurde in Bearbeitung wiederhergestellt' : 'Aufgabe wurde offen wiederhergestellt'); state.state = 'active'; state.view = 'board'; syncTopNavigation(); await loadTasks(); }
            async function resumeTask(taskId, status = 'open') { await requestJson(route('resume', taskId), { method: 'POST', body: formData({ status }) }); toast('Aufgabe fortgesetzt'); state.state = 'active'; state.view = 'board'; syncTopNavigation(); await loadTasks(); }
            function openReasonModal(taskId, action) { els.reasonTaskId.value = taskId; els.reasonAction.value = action; els.reasonText.value = ''; els.reasonTitle.textContent = { pause: 'Aufgabe pausieren', cancel: 'Aufgabe abbrechen', reject: 'Aufgabe ablehnen' }[action] || 'Grund angeben'; els.reasonModal.classList.add('is-open'); refreshIcons(); }
            function closeReasonModal() { els.reasonModal.classList.remove('is-open'); }
            async function submitReason() { const taskId = els.reasonTaskId.value; const action = els.reasonAction.value; const reason = els.reasonText.value.trim(); if (!reason) { toast('Bitte Grund eingeben', 'error'); return; } const data = await requestJson(route(action, taskId), { method: 'POST', body: formData({ reason }) }); closeReasonModal(); toast(data.message || (action === 'reject' ? 'Job abgelehnt' : 'Grund gespeichert')); await loadTasks(); }

            function openTaskDrawer() {
                const drawer = document.getElementById('newTaskDrawer');
                const body = drawer?.querySelector('.nt-body');
                initCollapsibleTaskSections();

                if (window.jQuery) {
                    $('.new_task').show().animate({ right: '0' }, 350, function () {
                        if (body) body.scrollTop = 0;
                        refreshIcons();
                    });
                } else {
                    drawer.style.display = 'block';
                    drawer.style.right = '0';
                    if (body) body.scrollTop = 0;
                    refreshIcons();
                }
            }
            function closeTaskDrawer() { if (window.jQuery) { $('.new_task').animate({ right: '-100%' }, 350, function () { $(this).hide(); }); } else { document.getElementById('newTaskDrawer').style.right = '-100%'; document.getElementById('newTaskDrawer').style.display = 'none'; } }
            function resetTaskDrawerForm() {
                clearTaskValidationErrors();
                clearDrawerValidationSectionMarks();
                document.querySelectorAll('#newTaskDrawer .nt-section.is-collapsed').forEach(section => {
                    section.classList.remove('is-collapsed');
                    section.querySelector(':scope > .nt-section-header .nt-section-toggle')?.setAttribute('aria-expanded', 'true');
                });
                const form = document.getElementById('task_form');
                if (form) form.reset();

                document.getElementById('drawerModeTitle').textContent = 'Aufgabe erstellen';
                document.getElementById('task_edit_id').value = '';
                document.getElementById('lead_product_list_id').value = '';
                document.getElementById('color').value = '#93c21c';
                setPriorityValue('medium');
                document.getElementById('colorIcon')?.style.setProperty('color', '#93c21c');
                document.getElementById('customerSelectContainer').style.display = 'none';

                setSubStageOptions('', '');
                updateLeadStageChips({
                    lead_stage_name: 'Keine Stage',
                    lead_stage_sub_stage_name: 'Keine Sub Stage',
                    lead_stage_color: '#74b2d4',
                    lead_stage_sub_stage_color: '#93c21c',
                });

                if (window.jQuery) {
                    $('#employee,#controller,#customer_id,#alternative_id,#product_id,#team_id,#lead_stage_id,#lead_stage_sub_stage_id').val(null).trigger('change');
                }

                resetKeyRows();
            }
            function resetKeyRows() { const tbody = document.querySelector('#key_task tbody'); tbody.innerHTML = ''; addKeyRow(); updateTotalDuration(); }
            function addKeyRow(prefill = {}) {
                const tbody = document.querySelector('#key_task tbody');
                const index = tbody.querySelectorAll('tr').length;
                const options = Array.from(document.querySelectorAll('#employee option')).map(opt => `<option value="${esc(opt.value)}">${esc(opt.textContent)}</option>`).join('');
                tbody.insertAdjacentHTML('beforeend', `<tr>
                                    <td>${index + 1}<input type="hidden" name="key[${index}][id]" value="${esc(prefill.id || '')}"></td>
                                    <td><input type="text" name="key[${index}][task]" class="nt-input" value="${esc(prefill.task || '')}" placeholder="Schritt"></td>
                                    <td><input type="number" step="0.25" name="key[${index}][duration]" class="nt-input task-duration" value="${esc(prefill.duration || '')}" placeholder="0.5"></td>
                                    <td class="pt-key-employee-cell"><select name="key[${index}][employee_id][]" class="nt-select employee-select key-employee-select" multiple>${options}</select></td>
                                    <td><select name="key[${index}][status]" class="nt-select"><option value="accepted">Offen</option><option value="on_progress">In Bearbeitung</option><option value="completed">Erledigt</option></select></td>
                                    <td><textarea name="key[${index}][key_description]" class="nt-textarea" rows="2">${esc(prefill.key_description || '')}</textarea></td>
                                    <td><button type="button" class="nt-btn nt-btn-danger remove-task-steps">${icon('minus')}</button></td>
                                </tr>`);
                if (window.jQuery && $.fn.select2) {
                    const $row = $('#key_task tbody tr').last();
                    $row.find('.key-employee-select').select2({ width: '100%', dropdownParent: $('#newTaskDrawer') });
                    if (prefill.employee_id) {
                        let ids = [];
                        try { ids = Array.isArray(prefill.employee_id) ? prefill.employee_id : JSON.parse(prefill.employee_id || '[]'); } catch (e) { ids = []; }
                        $row.find('.key-employee-select').val(ids.map(String)).trigger('change');
                    }
                    $row.find('select[name$="[status]"]').val(prefill.status || 'accepted');
                }
                applyStepEmployeeMode();
                refreshIcons();
            }
            function updateTotalDuration() {
                let total = 0;
                document.querySelectorAll('.task-duration').forEach(input => {
                    const value = String(input.value || '').replace(',', '.');
                    total += parseFloat(value) || 0;
                });
                const totalDays = total > 0 ? (total / 8) : 0;
                const totalTimeEl = document.getElementById('total_time');
                const totalDayEl = document.getElementById('total_day');
                const keyTotalEl = document.getElementById('key_total_time');
                if (keyTotalEl) keyTotalEl.textContent = total.toFixed(2) + ' Stunden / ' + totalDays.toFixed(2) + ' Tage';
                if (totalTimeEl) totalTimeEl.value = total.toFixed(2);
                if (totalDayEl) totalDayEl.value = totalDays.toFixed(2);
            }

            function normalizeTaskFromState(taskId) { return state.tasks.find(t => String(t.id) === String(taskId)); }
            async function editTask(taskId) {
                const task = normalizeTaskFromState(taskId);
                if (!task) {
                    toast('Aufgabe in aktueller Liste nicht gefunden', 'error');
                    return;
                }

                resetTaskDrawerForm();
                document.getElementById('drawerModeTitle').textContent = 'Aufgabe bearbeiten';
                document.getElementById('task_edit_id').value = task.id || '';
                document.getElementById('task_title').value = task.task_title || '';
                document.getElementById('description').value = stripHtml(task.description || '');
                document.getElementById('task_status').value = task.task_status || 'open';
                document.getElementById('due_date').value = task.due_date || '';
                document.getElementById('due_time').value = task.due_time || '';
                document.getElementById('start_date').value = task.start_date || '{{ now()->format('Y-m-d') }}';
                document.getElementById('progress').value = task.progress || 0;
                document.getElementById('total_day').value = task.total_day || '';
                document.getElementById('total_time').value = task.total_time || '';
                document.getElementById('lead_product_list_id').value = task.lead_product_list_id || '';
                document.getElementById('color').value = task.color || '#93c21c';
                document.getElementById('colorIcon')?.style.setProperty('color', task.color || '#93c21c');
                setPriorityValue(task.priority || 'medium');
                document.getElementById('public_switch').checked = !!Number(task.public ?? 0);
                document.getElementById('customerSwitch').checked = !!Number((task.is_customer ?? task.customer_id) ? 1 : 0);
                document.getElementById('customerSelectContainer').style.display = document.getElementById('customerSwitch').checked ? 'block' : 'none';

                if (window.jQuery) {
                    $('#employee').val((task.employees || []).map(e => String(e.id))).trigger('change');
                    $('#controller').val((task.controllers || []).map(e => String(e.id))).trigger('change');

                    if (task.customer_id) {
                        const customerText = task.customer_name || ('Kunde #' + task.customer_id);
                        if (!$('#customer_id option[value="' + task.customer_id + '"]').length) {
                            $('#customer_id').append(new Option(customerText, task.customer_id, true, true));
                        }
                        $('#customer_id').val(String(task.customer_id)).trigger('change.select2');
                    }

                    if (task.alternative_id) {
                        const altText = task.object_name || ('Objekt #' + task.alternative_id);
                        if (!$('#alternative_id option[value="' + task.alternative_id + '"]').length) {
                            $('#alternative_id').append(new Option(altText, task.alternative_id, true, true));
                        }
                        $('#alternative_id').val(String(task.alternative_id)).trigger('change.select2');
                    }

                    if (task.product_id) $('#product_id').val(String(task.product_id)).trigger('change.select2');
                }

                applyLeadStageContext(task.lead_stage_context || task);

                document.getElementById('customerInfoChips').innerHTML = `
                            ${task.customer_name ? `<span class="nt-chip">${esc(task.customer_name)}</span>` : '<span class="nt-chip">Kein Kunde gewählt</span>'}
                            ${task.object_name ? `<span class="nt-chip">${esc(task.object_name)}</span>` : ''}
                            ${task.product_name ? `<span class="nt-chip">${esc(task.product_name)}</span>` : ''}
                        `;

                document.querySelector('#key_task tbody').innerHTML = '';
                (task.keys || task.task_keys || []).forEach(key => addKeyRow(key));
                if (!document.querySelector('#key_task tbody tr')) addKeyRow();
                updateTotalDuration();
                openTaskDrawer();
            }
            function validationFieldLabel(field) {
                const labels = {
                    task_title: 'Aufgabentitel', description: 'Beschreibung', task_status: 'Status', due_date: 'Fälligkeitsdatum', due_time: 'Fälligkeitsuhrzeit', start_date: 'Startdatum', progress: 'Fortschritt', total_day: 'Gesamttage', total_time: 'Gesamtstunden', priority: 'Priorität', employee: 'Mitarbeiter', controller: 'Controller', customer_id: 'Kunde', alternative_id: 'Objekt', product_id: 'Produkt', lead_stage_id: 'Lead Stage', lead_stage_sub_stage_id: 'Sub Stage', color: 'Farbe', type: 'Typ'
                };
                const keyMatch = String(field).match(/^key\.(\d+)\.(.+)$/);
                if (keyMatch) {
                    const row = Number(keyMatch[1]) + 1;
                    const part = keyMatch[2];
                    if (part === 'task') return 'Schritt ' + row + ' – Titel';
                    if (part === 'duration') return 'Schritt ' + row + ' – Dauer';
                    if (part === 'key_description') return 'Schritt ' + row + ' – Beschreibung';
                    if (part.startsWith('employee_id')) return 'Schritt ' + row + ' – Mitarbeiter';
                    if (part === 'status') return 'Schritt ' + row + ' – Status';
                    return 'Schritt ' + row;
                }
                return labels[field] || field;
            }

            function validationSelector(field) {
                const map = { task_title: '#task_title', description: '#description', task_status: '#task_status', due_date: '#due_date', due_time: '#due_time', start_date: '#start_date', progress: '#progress', total_day: '#total_day', total_time: '#total_time', priority: '#priority', employee: '#employee', controller: '#controller', customer_id: '#customer_id', alternative_id: '#alternative_id', product_id: '#product_id', lead_stage_id: '#lead_stage_id', lead_stage_sub_stage_id: '#lead_stage_sub_stage_id', color: '#color', type: '#type' };
                if (map[field]) return map[field];
                const keyMatch = String(field).match(/^key\.(\d+)\.(.+)$/);
                if (keyMatch) {
                    const i = keyMatch[1];
                    const part = keyMatch[2];
                    if (part === 'task') return `[name="key[${i}][task]"]`;
                    if (part === 'duration') return `[name="key[${i}][duration]"]`;
                    if (part === 'key_description') return `[name="key[${i}][key_description]"]`;
                    if (part === 'status') return `[name="key[${i}][status]"]`;
                    if (part.startsWith('employee_id')) return `[name="key[${i}][employee_id][]"]`;
                }
                return null;
            }

            function clearTaskValidationErrors() {
                document.querySelectorAll('.nt-field-error').forEach(el => el.remove());
                document.querySelectorAll('.nt-invalid').forEach(el => el.classList.remove('nt-invalid', 'nt-error-flash'));
                clearDrawerValidationSectionMarks();
                const summary = document.getElementById('taskValidationSummary');
                const list = document.getElementById('taskValidationList');
                if (summary) summary.classList.remove('is-open');
                if (list) list.innerHTML = '';
            }

            function markTaskFieldInvalid(field, message) {
                const selector = validationSelector(field);
                if (!selector) return null;
                const input = document.querySelector(selector);
                if (!input) return null;
                expandDrawerSectionForElement(input);
                input.classList.add('nt-invalid', 'nt-error-flash');
                if (window.jQuery && input.tagName === 'SELECT' && $(input).hasClass('select2-hidden-accessible')) {
                    $(input).next('.select2-container').addClass('nt-invalid', 'nt-error-flash');
                }
                const error = document.createElement('div');
                error.className = 'nt-field-error';
                error.textContent = message;
                const target = input.closest('td') || input.closest('div') || input.parentElement;
                if (target) target.appendChild(error);
                return input;
            }

            function showTaskValidationErrors(errors) {
                clearTaskValidationErrors();
                const summary = document.getElementById('taskValidationSummary');
                const list = document.getElementById('taskValidationList');
                const normalized = [];
                Object.keys(errors || {}).forEach(field => {
                    const msgs = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
                    msgs.forEach(msg => normalized.push({ field, message: String(msg || 'Ungültiger Wert') }));
                });
                if (!normalized.length) {
                    normalized.push({ field: 'task_title', message: 'Validierungsfehler. Bitte prüfen Sie das Formular.' });
                }
                let firstInput = null;
                normalized.forEach(item => {
                    const input = markTaskFieldInvalid(item.field, item.message);
                    if (!firstInput && input) firstInput = input;
                    if (list) {
                        const li = document.createElement('li');
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.textContent = validationFieldLabel(item.field);
                        btn.addEventListener('click', () => {
                            const target = document.querySelector(validationSelector(item.field));
                            if (target) { expandDrawerSectionForElement(target); target.scrollIntoView({ behavior: 'smooth', block: 'center' }); setTimeout(() => target.focus?.(), 250); }
                        });
                        li.appendChild(btn);
                        li.appendChild(document.createTextNode(': ' + item.message));
                        list.appendChild(li);
                    }
                });
                if (summary) summary.classList.add('is-open');
                if (firstInput) {
                    firstInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => firstInput.focus?.(), 300);
                }
                toast(normalized[0].message, 'error');
                console.warn('Laravel validation errors:', errors);
                refreshIcons();
            }

            function showClientValidationErrors(messages) {
                const errors = {};
                messages.forEach(item => { errors[item.field] = [item.message]; });
                showTaskValidationErrors(errors);
            }
            function validateTaskForm() {
                const errors = [];
                const title = document.getElementById('task_title')?.value.trim() || '';
                if (!title) errors.push({ field: 'task_title', message: 'Bitte geben Sie einen Aufgabentitel ein.' });

                const currentPriority = normalizePriorityForBlade(document.getElementById('priority')?.value || 'medium');
                setPriorityValue(currentPriority);

                const dueDate = document.getElementById('due_date')?.value || '';
                if (dueDate && Number.isNaN(Date.parse(dueDate))) errors.push({ field: 'due_date', message: 'Das Fälligkeitsdatum ist ungültig.' });

                const mode = document.getElementById('step_employee_mode')?.value || 'all';
                const employeeSelect = document.getElementById('employee');
                const hasGlobal = employeeSelect ? Array.from(employeeSelect.selectedOptions || []).length > 0 : false;
                const hasStep = Array.from(document.querySelectorAll('.key-employee-select')).some(sel => Array.from(sel.selectedOptions || []).length > 0);
                if (mode === 'all' && !hasGlobal) errors.push({ field: 'employee', message: 'Bitte wählen Sie Mitarbeiter für die Aufgabe aus.' });
                if (mode === 'per_step' && !hasStep) errors.push({ field: 'key.0.employee_id', message: 'Bitte wählen Sie mindestens einen Mitarbeiter direkt im Schritt aus.' });

                document.querySelectorAll('.task-duration').forEach(input => {
                    const raw = String(input.value || '').replace(',', '.');
                    if (raw !== '' && (Number.isNaN(Number(raw)) || Number(raw) < 0)) {
                        const name = input.getAttribute('name') || '';
                        const match = name.match(/key\[(\d+)\]/);
                        const idx = match ? match[1] : '0';
                        errors.push({ field: `key.${idx}.duration`, message: 'Die Dauer muss eine positive Zahl sein, z.B. 0.5 oder 1.25.' });
                    }
                });
                return errors;
            }
            function formSubmitUrl() { const isEdit = !!document.getElementById('task_edit_id').value; if (isEdit) return routes.update; if (routes.store) return routes.store; return routes.update; }
            async function submitTaskForm(closeAfterSave) {
                setPriorityValue(document.getElementById('priority')?.value || 'medium');
                console.log('Task priority submitted from Blade:', document.getElementById('priority')?.value);
                clearTaskValidationErrors();
                const errors = validateTaskForm();
                if (errors.length) { showClientValidationErrors(errors); return; }
                if (!document.getElementById('task_edit_id').value && !hasStoreRoute) {
                    showTaskValidationErrors({ task_title: ['Store-Route fehlt: bitte Route personal.task.store oder personal-tasks.store hinzufügen.'] });
                    return;
                }
                updateTotalDuration();
                const selectedPriority = setPriorityValue(document.getElementById('priority')?.value || 'medium');
                console.info('Task priority submitted from Blade:', selectedPriority);
                const form = document.getElementById('task_form');
                try {
                    const data = await requestJson(formSubmitUrl(), { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json' } });
                    clearTaskValidationErrors();
                    toast(data.message || 'Aufgabe gespeichert', 'success');
                    if (closeAfterSave) closeTaskDrawer(); else resetTaskDrawerForm();
                    await loadTasks();
                } catch (error) {
                    if (error.status === 422 || error.errors) {
                        showTaskValidationErrors(error.errors || {});
                        return;
                    }
                    toast(error.message || 'Speichern fehlgeschlagen', 'error');
                    console.error('Task save failed:', error);
                }
            }

            function initSelect2() {
                if (!window.jQuery || !$.fn.select2) return;

                $('#ptEmployeeFilter').select2({ width: '100%', placeholder: 'Mitarbeiter', allowClear: true });
                $('#ptLeadStageFilter').select2({ width: '100%', placeholder: 'Alle Stages', allowClear: true });
                $('#ptLeadSubStageFilter').select2({ width: '100%', placeholder: 'Alle Sub Stages', allowClear: true });

                loadFilterSubStagesForStage($('#ptLeadStageFilter').val() || '', $('#ptLeadSubStageFilter').val() || '');

                $('#ptLeadStageFilter')
                    .off('change.ptFilterStage select2:select.ptFilterStage select2:clear.ptFilterStage')
                    .on('change.ptFilterStage select2:select.ptFilterStage select2:clear.ptFilterStage', handleFilterStageChanged);

                $('#ptLeadSubStageFilter')
                    .off('change.ptFilterSubStage select2:select.ptFilterSubStage select2:clear.ptFilterSubStage')
                    .on('change.ptFilterSubStage select2:select.ptFilterSubStage select2:clear.ptFilterSubStage', handleFilterSubStageChanged);

                $('#employee,#controller,#team_id,#product_id,#alternative_id,#lead_stage_id,#lead_stage_sub_stage_id')
                    .select2({ width: '100%', dropdownParent: $('#newTaskDrawer'), allowClear: true });

                setSubStageOptions($('#lead_stage_id').val() || '', $('#lead_stage_sub_stage_id').val() || '');

                $('#lead_stage_id').off('change.ptStage').on('change.ptStage', function () {
                    const stageId = this.value || '';
                    setSubStageOptions(stageId, '');
                    updateLeadStageChips();
                });

                $('#lead_stage_sub_stage_id').off('change.ptStage').on('change.ptStage', function () {
                    updateLeadStageChips();
                });

                $('#product_id,#alternative_id').off('change.ptLeadContext').on('change.ptLeadContext', debounce(() => {
                    loadLeadStageContext();
                }, 200));

                $('#customer_id').select2({
                    width: '100%',
                    dropdownParent: $('#newTaskDrawer'),
                    placeholder: 'Kunde / Objekt / Produkt suchen',
                    allowClear: true,
                    ajax: {
                        url: routes.customersSearch,
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term || '', page: params.page || 1 }),
                        processResults: data => data,
                    },
                    escapeMarkup: m => m,
                    templateResult: item => item.html || item.text,
                    templateSelection: item => item.text || item.name || '',
                }).on('select2:select', function (e) {
                    const item = e.params.data || {};
                    const customerId = item.customer_id || item.id;
                    const alternativeId = item.alternative_id || '';
                    const productId = item.product_id || '';
                    const leadProductListId = item.lead_product_list_id || item.leadProductListId || '';

                    if (leadProductListId) document.getElementById('lead_product_list_id').value = leadProductListId;
                    if (customerId) $('#customer_id').val(String(customerId)).trigger('change.select2');

                    if (alternativeId) {
                        const altText = item.object_name || item.object_address || ('Objekt #' + alternativeId);
                        if (!$('#alternative_id option[value="' + alternativeId + '"]').length) {
                            $('#alternative_id').append(new Option(altText, alternativeId, true, true));
                        }
                        $('#alternative_id').val(String(alternativeId)).trigger('change.select2');
                    }

                    if (productId) {
                        if (!$('#product_id option[value="' + productId + '"]').length) {
                            $('#product_id').append(new Option(item.product_name || ('Produkt #' + productId), productId, true, true));
                        }
                        $('#product_id').val(String(productId)).trigger('change.select2');
                    }

                    document.getElementById('customerInfoChips').innerHTML = `
                                <span class="nt-chip">${esc(item.customer_name || item.text || '')}</span>
                                ${item.object_name ? `<span class="nt-chip">${esc(item.object_name)}</span>` : ''}
                                ${item.product_name ? `<span class="nt-chip">${esc(item.product_name)}</span>` : ''}
                            `;

                    if (item.lead_stage_context) {
                        applyLeadStageContext(item.lead_stage_context);
                    } else {
                        loadLeadStageContext({
                            lead_product_list_id: leadProductListId,
                            customer_id: customerId,
                            alternative_id: alternativeId,
                            product_id: productId,
                        });
                    }
                }).on('select2:clear', function () {
                    document.getElementById('lead_product_list_id').value = '';
                    document.getElementById('customerInfoChips').innerHTML = '<span class="nt-chip">Kein Kunde gewählt</span>';
                    applyLeadStageContext({});
                });

                $('.key-employee-select').select2({ width: '100%', dropdownParent: $('#newTaskDrawer') });
            }


            function applyStepEmployeeMode() {
                const mode = document.querySelector('input[name="step_employee_mode_radio"]:checked')?.value || 'all';
                document.getElementById('step_employee_mode').value = mode;
                document.getElementById('globalEmployeeBox')?.classList.toggle('is-hidden', mode === 'per_step');
                document.querySelectorAll('.pt-key-employee-cell').forEach(cell => cell.style.display = mode === 'per_step' ? '' : 'none');
                if (mode === 'per_step' && window.jQuery) { $('#employee').val(null).trigger('change'); }
            }
            async function checkAppointmentConflicts() {
                if (!routes.appointmentConflicts || routes.appointmentConflicts === '#') return;
                const date = document.getElementById('due_date').value;
                const time = document.getElementById('due_time').value;
                if (!date) return;
                let employeeIds = [];
                if (document.getElementById('step_employee_mode').value === 'per_step') {
                    document.querySelectorAll('.key-employee-select').forEach(sel => Array.from(sel.selectedOptions).forEach(o => employeeIds.push(o.value)));
                } else {
                    employeeIds = Array.from(document.getElementById('employee').selectedOptions).map(o => o.value);
                }
                employeeIds = [...new Set(employeeIds.filter(Boolean))];
                if (!employeeIds.length) return;
                const params = new URLSearchParams();
                params.set('date', date); params.set('time', time || ''); employeeIds.forEach(id => params.append('employee_ids[]', id));
                try {
                    const data = await requestJson(`${routes.appointmentConflicts}?${params.toString()}`);
                    const box = document.getElementById('appointmentConflictBox');
                    const list = document.getElementById('appointmentConflictList');
                    if (data.conflicts && data.conflicts.length) {
                        list.innerHTML = data.conflicts.map(c => `<div class="pt-conflict-item"><strong>${esc(c.employee_name || 'Mitarbeiter')}</strong><br>${esc(c.title || 'Termin')} · ${esc(c.start || '')} - ${esc(c.end || '')}</div>`).join('');
                        box.classList.add('is-open');
                    } else { box.classList.remove('is-open'); list.innerHTML = ''; }
                    refreshIcons();
                } catch (e) { console.warn(e); }
            }
            async function submitTaskComment(taskId, textarea) {
                const comment = textarea.value.trim();
                if (!comment) { toast('Bitte Kommentar schreiben', 'error'); return; }
                await requestJson(route('commentsStore', taskId), { method: 'POST', body: formData({ comment }) });
                toast('Kommentar gespeichert');
                await loadTasks();
                setTimeout(() => document.querySelector(`[data-task-id="${taskId}"]`)?.classList.add('is-comments-open'), 100);
            }

            function syncTopNavigation() {
                document.querySelectorAll('#ptScopeTabs .pt-tab').forEach(b => b.classList.toggle('is-active', b.dataset.scope === state.scope));
                document.querySelectorAll('#ptViewTabs .pt-tab').forEach(b => b.classList.toggle('is-active', b.dataset.view === state.view));
            }


            function initCollapsibleTaskSections() {
                const drawer = document.getElementById('newTaskDrawer');
                if (!drawer) return;

                drawer.querySelectorAll('.nt-section').forEach((section, index) => {
                    const header = section.querySelector(':scope > .nt-section-header');
                    if (!header || header.dataset.collapsibleReady === '1') return;

                    section.classList.add('is-collapsible');
                    header.dataset.collapsibleReady = '1';

                    let actions = header.querySelector(':scope > .nt-section-actions');
                    if (!actions) {
                        actions = document.createElement('div');
                        actions.className = 'nt-section-actions';

                        Array.from(header.children).forEach(child => {
                            if (!child.classList.contains('nt-section-title')) {
                                actions.appendChild(child);
                            }
                        });

                        header.appendChild(actions);
                    }

                    const toggle = document.createElement('button');
                    toggle.type = 'button';
                    toggle.className = 'nt-section-toggle';
                    toggle.setAttribute('aria-expanded', section.classList.contains('is-collapsed') ? 'false' : 'true');
                    toggle.setAttribute('title', 'Abschnitt ein-/ausklappen');
                    toggle.innerHTML = icon('chevron-down');
                    actions.insertBefore(toggle, actions.firstChild);

                    const toggleSection = () => {
                        const collapsed = section.classList.toggle('is-collapsed');
                        toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                        refreshIcons();
                    };

                    toggle.addEventListener('click', event => {
                        event.preventDefault();
                        event.stopPropagation();
                        toggleSection();
                    });

                    header.addEventListener('click', event => {
                        if (event.target.closest('button,a,input,select,textarea,label,.select2-container,.dropdown-menu')) return;
                        toggleSection();
                    });
                });

                refreshIcons();
            }

            function expandDrawerSectionForElement(element) {
                const field = typeof element === 'string' ? document.querySelector(element) : element;
                if (!field) return;

                const section = field.closest('.nt-section');
                if (!section) return;

                section.classList.remove('is-collapsed');
                section.classList.add('has-validation-error');

                const toggle = section.querySelector(':scope > .nt-section-header .nt-section-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'true');

                refreshIcons();
            }

            function clearDrawerValidationSectionMarks() {
                document.querySelectorAll('#newTaskDrawer .nt-section.has-validation-error')
                    .forEach(section => section.classList.remove('has-validation-error'));
            }

            function bindEvents() {
                els.search.addEventListener('input', debounce(() => { state.search = els.search.value.trim(); loadTasks(); }));
                els.priority.addEventListener('change', () => { state.priority = els.priority.value; loadTasks(); });
                els.due.addEventListener('change', () => { state.due = els.due.value; loadTasks(); });
                els.employee.addEventListener('change', () => { state.employee = els.employee.value; loadTasks(); });
                els.leadStage.addEventListener('change', handleFilterStageChanged);
                els.leadSubStage.addEventListener('change', handleFilterSubStageChanged);
                els.refresh.addEventListener('click', loadTasks);
                document.querySelectorAll('#ptScopeTabs .pt-tab').forEach(btn => btn.addEventListener('click', () => { document.querySelectorAll('#ptScopeTabs .pt-tab').forEach(b => b.classList.remove('is-active')); btn.classList.add('is-active'); state.scope = btn.dataset.scope; loadTasks(); }));
                document.querySelectorAll('#ptViewTabs .pt-tab').forEach(btn => btn.addEventListener('click', () => { if (btn.dataset.view === 'board' && isListOnlyState()) { toast('Dieser Bereich wird nur als Liste angezeigt.', 'error'); state.view = 'list'; syncTopNavigation(); render(); return; } document.querySelectorAll('#ptViewTabs .pt-tab').forEach(b => b.classList.remove('is-active')); btn.classList.add('is-active'); state.view = btn.dataset.view; render(); }));
                els.moreBtn.addEventListener('click', () => els.more.classList.toggle('is-open'));
                document.querySelectorAll('.pt-more-item').forEach(btn => btn.addEventListener('click', () => { state.state = btn.dataset.state; if (isListOnlyState()) state.view = 'list'; syncTopNavigation(); els.more.classList.remove('is-open'); loadTasks(); }));
                document.querySelector('.create_new_task').addEventListener('click', () => { resetTaskDrawerForm(); openTaskDrawer(); });
                document.querySelectorAll('.close_task_window').forEach(btn => btn.addEventListener('click', closeTaskDrawer));
                document.querySelector('.save-task-close').addEventListener('click', () => submitTaskForm(true));
                document.querySelector('.save-task-continue').addEventListener('click', () => submitTaskForm(false));
                document.getElementById('customerSwitch').addEventListener('change', function () { document.getElementById('customerSelectContainer').style.display = this.checked ? 'block' : 'none'; });
                document.getElementById('repeated').addEventListener('change', function () { document.querySelector('.repeated_area').style.display = this.checked ? 'block' : 'none'; });
                document.getElementById('reminder_check').addEventListener('change', function () { document.querySelector('.reminder_area').style.display = this.checked ? 'block' : 'none'; });
                document.querySelector('.add-task-steps').addEventListener('click', () => addKeyRow());
                const prioritySelect = document.getElementById('priority');
                if (prioritySelect) {
                    prioritySelect.addEventListener('change', function () {
                        setPriorityValue(this.value);
                        console.log('Task priority selected in Blade:', this.value);
                    });
                }

                document.querySelectorAll('input[name="step_employee_mode_radio"]').forEach(r => r.addEventListener('change', applyStepEmployeeMode));
                document.getElementById('due_date').addEventListener('change', checkAppointmentConflicts);
                document.getElementById('due_time').addEventListener('change', checkAppointmentConflicts);
                document.getElementById('employee').addEventListener('change', checkAppointmentConflicts);
                document.getElementById('ptConflictClose')?.addEventListener('click', () => document.getElementById('appointmentConflictBox').classList.remove('is-open'));
                document.getElementById('ptConflictChange')?.addEventListener('click', () => document.getElementById('due_date').focus());
                document.getElementById('ptConflictAnyway')?.addEventListener('click', () => document.getElementById('appointmentConflictBox').classList.remove('is-open'));
                document.addEventListener('input', event => { if (event.target.classList.contains('task-duration')) updateTotalDuration(); });
                document.addEventListener('click', async event => {
                    const colorItem = event.target.closest('#color_drop_down .dropdown-item'); if (colorItem) { event.preventDefault(); const color = colorItem.dataset.value; document.getElementById('color').value = color; document.getElementById('colorIcon')?.style.setProperty('color', color); refreshIcons(); return; }
                    const priorityItem = event.target.closest('#priority_select .dropdown-item'); if (priorityItem) { event.preventDefault(); setPriorityValue(priorityItem.dataset.value || priorityItem.textContent.trim()); refreshIcons(); return; }
                    const removeKey = event.target.closest('.remove-task-steps'); if (removeKey) { const rows = document.querySelectorAll('#key_task tbody tr'); if (rows.length <= 1) { toast('Es muss mindestens ein Schritt bleiben', 'error'); return; } removeKey.closest('tr').remove(); renumberKeys(); updateTotalDuration(); return; }
                    const closeReason = event.target.closest('[data-close-reason]'); if (closeReason) { closeReasonModal(); return; }
                    const actionBtn = event.target.closest('[data-action]'); if (!actionBtn) { document.querySelectorAll('.pt-action-dropdown.is-open').forEach(el => el.classList.remove('is-open')); if (!event.target.closest('#ptMoreDropdown')) els.more.classList.remove('is-open'); return; }
                    const action = actionBtn.dataset.action; const taskId = actionBtn.dataset.id;
                    if (action === 'toggle-menu') { const dropdown = actionBtn.closest('.pt-action-dropdown'); document.querySelectorAll('.pt-action-dropdown.is-open').forEach(el => { if (el !== dropdown) el.classList.remove('is-open'); }); dropdown.classList.toggle('is-open'); return; }
                    if (action === 'toggle-comments') { const card = actionBtn.closest('.pt-task-card'); card?.classList.toggle('is-comments-open'); refreshIcons(); return; }
                    if (!taskId) return;
                    try { if (action === 'edit') await editTask(taskId); if (action === 'status') await updateStatus(taskId, actionBtn.dataset.status); if (action === 'archive') await archiveTask(taskId); if (action === 'delete') await deleteTask(taskId); if (action === 'restore') await restoreTask(taskId, 'open'); if (action === 'restore-status') await restoreTask(taskId, actionBtn.dataset.status || 'open'); if (action === 'resume') await resumeTask(taskId, actionBtn.dataset.status || 'open'); if (action === 'reason') openReasonModal(taskId, actionBtn.dataset.reasonAction); } catch (error) { toast(error.message, 'error'); }
                });
                document.addEventListener('submit', async event => { const form = event.target.closest('[data-comment-form]'); if (!form) return; event.preventDefault(); try { await submitTaskComment(form.dataset.commentForm, form.querySelector('textarea')); } catch (error) { toast(error.message, 'error'); } });
                document.getElementById('ptReasonSubmit').addEventListener('click', () => submitReason().catch(error => toast(error.message, 'error')));
                document.addEventListener('dragstart', event => { const card = event.target.closest('.pt-task-card'); if (!card) return; state.draggingTaskId = card.dataset.taskId; event.dataTransfer.effectAllowed = 'move'; });
                document.addEventListener('dragover', event => { const zone = event.target.closest('[data-drop-column]'); if (zone) { event.preventDefault(); zone.classList.add('is-over'); } });
                document.addEventListener('dragleave', event => { const zone = event.target.closest('[data-drop-column]'); if (zone) zone.classList.remove('is-over'); });
                document.addEventListener('drop', async event => { const zone = event.target.closest('[data-drop-column]'); if (!zone || !state.draggingTaskId) return; event.preventDefault(); zone.classList.remove('is-over'); const statusMap = { open: 'open', in_progress: 'on_progress', completed: 'completed' }; try { await updateStatus(state.draggingTaskId, statusMap[zone.dataset.dropColumn] || 'open'); } catch (error) { toast(error.message, 'error'); } finally { state.draggingTaskId = null; } });
            }
            function renumberKeys() { document.querySelectorAll('#key_task tbody tr').forEach((row, index) => { row.children[0].textContent = index + 1; row.querySelectorAll('input,textarea,select').forEach(input => { if (!input.name) return; input.name = input.name.replace(/key\[\d+\]/, `key[${index}]`); }); }); }

            document.addEventListener('DOMContentLoaded', () => { resetKeyRows(); initSelect2(); initCollapsibleTaskSections(); bindEvents(); refreshIcons(); loadTasks(); });
        })();
    </script>
@endsection