@extends('admin.layouts.app')
@section('title', 'Tickets')

@php
  $counts = $counts ?? [
    'mine' => 0,
    'created' => 0,
    'progress' => 0,
    'completed' => 0,
    'junk' => 0,
    'all' => 0,
  ];

  $viewMode = $viewMode ?? request('view', 'list');
@endphp

@once
  @push('style')
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

      .oc-status-select {
        border: 1px solid var(--border);
        background: #fff;
        color: #111827;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 12px;
        font-weight: 900;
        outline: none;
        cursor: pointer;
        max-width: 150px;
      }

      .oc-status-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
      }

      .kanban-body {
        min-height: 220px;
      }

      .kanban-body.drag-over {
        background: var(--primary-light);
        outline: 2px dashed var(--primary);
        outline-offset: -6px;
        border-radius: 14px;
      }

      .kanban-ticket {
        cursor: grab;
      }

      .kanban-ticket:active {
        cursor: grabbing;
      }

      .kanban-ticket.dragging {
        opacity: .55;
        transform: scale(.98);
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

      .oc-stat-icon.published {
        background: var(--success-light);
        color: var(--success)
      }

      .oc-stat-icon.unpublished {
        background: var(--warning-light);
        color: #d97706
      }

      .oc-stat-icon.type {
        background: var(--gray-light);
        color: var(--gray)
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
        background: #f9fafb;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
        min-width: 180px;
        width: 100%;
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

      .oc-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
      }

      .oc-tab {
        border: 1px solid var(--border);
        background: #fff;
        color: #374151;
        border-radius: 999px;
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: var(--transition);
      }

      .oc-tab:hover {
        border-color: #d1d5db;
        background: #f9fafb
      }

      .oc-tab.active {
        background: var(--primary-light);
        color: #54730f;
        border-color: #d9edaa;
      }

      .oc-tab-all {
        background: #111827;
        color: #fff;
        border-color: #111827;
      }

      .oc-tab-all:hover,
      .oc-tab-all.active {
        background: #020617;
        color: #fff;
        border-color: #020617;
      }

      .oc-tab-badge {
        margin-left: 7px;
        min-width: 22px;
        height: 22px;
        padding: 0 7px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .22);
        color: inherit;
        font-size: 11px;
        font-weight: 950;
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
        grid-template-columns: 90px minmax(250px, 1.3fr) 140px minmax(180px, .9fr) minmax(210px, 1fr) minmax(230px, 1.2fr) 150px;
        gap: 14px;
        align-items: center;
        padding: 16px 16px 10px 16px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      @media(max-width:1450px) {
        .oc-list-head {
          display: none;
        }
      }

      .oc-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 0 0 16px 0;
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
        align-items: start;
        grid-template-columns: 90px minmax(250px, 1.3fr) 140px minmax(180px, .9fr) minmax(210px, 1fr) minmax(230px, 1.2fr) 150px;
      }

      @media(max-width:1450px) {
        .oc-item-row {
          grid-template-columns: 1fr;
        }
      }

      .oc-cell {
        min-width: 0
      }

      .oc-cell-title {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 4px;
        display: none;
      }

      @media(max-width:1450px) {
        .oc-cell-title {
          display: block;
        }
      }

      .oc-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 74px;
        height: 48px;
        padding: 0 5px;
        border-radius: 10px;
        background: var(--blue-light);
        color: var(--blue);
        font-size: 10px;
        font-weight: 900;
      }

      .oc-main {
        display: flex;
        flex-direction: column;
        min-width: 0
      }

      .oc-ttl {
        font-weight: 800;
        font-size: 15px;
        margin-bottom: 4px;
        color: #111827
      }

      .oc-subt {
        font-size: 13px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
      }

      .oc-subt-wrap {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.45
      }

      .oc-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
      }

      .oc-status-pill.open {
        background: #eff6ff;
        color: #1d4ed8;
      }

      .oc-status-pill.process {
        background: #fffbeb;
        color: #b45309;
      }

      .oc-status-pill.end {
        background: #ecfdf5;
        color: #047857;
      }

      .oc-status-pill.junk {
        background: #fef2f2;
        color: #b91c1c;
      }

      .oc-priority {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
        background: #f3f4f6;
        color: #374151;
      }

      .oc-priority.high {
        background: #fef2f2;
        color: #b91c1c;
      }

      .oc-priority.medium {
        background: #fffbeb;
        color: #b45309;
      }

      .oc-priority.low {
        background: #ecfdf5;
        color: #047857;
      }

      .oc-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
      }

      .oc-empty {
        text-align: center;
        padding: 60px;
        color: var(--text-muted);
        background: #fff;
        border: 1px dashed var(--border);
        border-radius: 16px;
        margin: 16px;
      }

      .oc-pagination {
        margin-top: 18px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: var(--shadow-sm);
      }

      .oc-pagination .pagination {
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
      }

      .oc-pagination .page-item .page-link {
        border-radius: 10px !important;
        border: 1px solid var(--border);
        color: var(--text-main);
        padding: 8px 12px;
        line-height: 1.1;
        box-shadow: none !important;
      }

      .oc-pagination .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
      }

      .oc-pagination .page-item.disabled .page-link {
        color: #9ca3af;
        background: #f9fafb;
      }

      .oc-loading {
        padding: 40px 20px;
        text-align: center;
        color: var(--text-muted);
        font-weight: 700;
      }

      .oc-view-panels {
        position: relative;
      }

      .oc-view-panel {
        display: none;
      }

      .oc-view-panel.active {
        display: block;
      }

      .oc-card-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        padding: 16px;
      }

      @media(max-width:1350px) {
        .oc-card-grid {
          grid-template-columns: repeat(2, minmax(0, 1fr));
        }
      }

      @media(max-width:760px) {
        .oc-card-grid {
          grid-template-columns: 1fr;
        }
      }

      .ticket-card {
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #fff;
        box-shadow: var(--shadow-sm);
        padding: 16px;
        transition: var(--transition);
      }

      .ticket-card:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow);
      }

      .ticket-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
      }

      .ticket-card-title {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
      }

      .ticket-card-meta {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
        line-height: 1.45;
      }

      .ticket-card-section {
        margin-top: 14px;
      }

      .ticket-card-label {
        font-size: 11px;
        font-weight: 900;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
      }

      .ticket-card-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 16px;
      }

      .kanban-wrap {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        padding: 16px;
        align-items: start;
      }

      @media(max-width:1400px) {
        .kanban-wrap {
          grid-template-columns: repeat(2, minmax(0, 1fr));
        }
      }

      @media(max-width:760px) {
        .kanban-wrap {
          grid-template-columns: 1fr;
        }
      }

      .kanban-col {
        background: #f9fafb;
        border: 1px solid var(--border);
        border-radius: 16px;
        min-height: 280px;
        overflow: hidden;
      }

      .kanban-col-head {
        padding: 14px 14px 10px 14px;
        border-bottom: 1px solid var(--border);
        background: #fff;
      }

      .kanban-col-title {
        font-size: 14px;
        font-weight: 900;
        color: #111827;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
      }

      .kanban-count {
        min-width: 28px;
        height: 28px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 900;
        background: var(--gray-light);
        color: #374151;
      }

      .kanban-body {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .kanban-ticket {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px;
        box-shadow: var(--shadow-sm);
      }

      .kanban-ticket:hover {
        border-color: var(--primary);
      }

      .kanban-ticket-no {
        font-size: 12px;
        font-weight: 900;
        color: var(--blue);
        margin-bottom: 6px;
      }

      .kanban-ticket-title {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        line-height: 1.35;
        margin-bottom: 6px;
      }

      .kanban-ticket-sub {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.45;
      }

      .oc-user-stack {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
      }

      .oc-avatar {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        object-fit: cover;
        background: #fff;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px var(--border);
        flex: 0 0 auto;
      }

      .oc-avatar.sm {
        width: 28px;
        height: 28px;
      }

      .oc-avatar.lg {
        width: 42px;
        height: 42px;
      }

      .oc-avatar-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
      }

      .oc-avatar-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
      }

      .oc-avatar-name {
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
      }

      .oc-avatar-role {
        font-size: 11px;
        color: var(--text-muted);
        line-height: 1.3;
      }

      .oc-mini-users {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
      }

      .oc-history {
        display: flex;
        flex-direction: column;
        gap: 8px;
      }

      .oc-history-item {
        display: flex;
        gap: 10px;
        align-items: flex-start;
      }

      .oc-history-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        margin-top: 5px;
        flex: 0 0 auto;
      }

      .oc-history-dot.created {
        background: #3b82f6;
      }

      .oc-history-dot.progress {
        background: #f59e0b;
      }

      .oc-history-dot.edited {
        background: #6b7280;
      }

      .oc-history-dot.ended {
        background: #10b981;
      }

      .oc-history-body {
        min-width: 0
      }

      .oc-history-label {
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
      }

      .oc-history-sub {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.45;
      }

      .oc-kanban-users {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
      }

      .oc-toast-wrap {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
      }

      .oc-toast {
        pointer-events: auto;
        min-width: 280px;
        max-width: 360px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        padding: 12px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        animation: ocToastIn .3s cubic-bezier(.175, .885, .32, 1.275) forwards;
      }

      @keyframes ocToastIn {
        from {
          transform: translateX(100%);
          opacity: 0
        }

        to {
          transform: translateX(0);
          opacity: 1
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
        color: var(--success)
      }

      .oc-toast-ic.bad {
        background: var(--danger-light);
        color: var(--danger)
      }

      .oc-toast-ttl {
        font-weight: 900;
        font-size: 13px;
        margin: 0;
        color: #111827
      }

      .oc-toast-msg {
        font-size: 12px;
        color: #374151;
        margin: 4px 0 0 0;
        line-height: 1.4
      }

      .oc-toast-x {
        margin-left: auto;
        background: transparent;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
      }
    </style>

    <style>
      .oc-latest-history-btn {
        width: 100%;
        border: 1px solid var(--border);
        background: #fff;
        border-radius: 12px;
        padding: 10px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        text-align: left;
        transition: var(--transition);
      }

      .oc-latest-history-btn:hover {
        border-color: var(--primary);
        background: var(--primary-light);
      }

      .oc-latest-history-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        flex: 0 0 auto;
      }

      .oc-latest-history-dot.created {
        background: #3b82f6;
      }

      .oc-latest-history-dot.progress {
        background: #f59e0b;
      }

      .oc-latest-history-dot.edited {
        background: #6b7280;
      }

      .oc-latest-history-dot.ended,
      .oc-latest-history-dot.end {
        background: #10b981;
      }

      .oc-latest-history-dot.junk {
        background: #ef4444;
      }

      .oc-latest-history-content {
        min-width: 0;
        flex: 1;
      }

      .oc-latest-history-label {
        font-size: 12px;
        font-weight: 900;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .oc-latest-history-meta {
        margin-top: 3px;
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .oc-latest-history-icon {
        color: var(--text-muted);
        flex: 0 0 auto;
      }

      /* Modal */
      .oc-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .52);
        z-index: 9998;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 22px;
      }

      .oc-modal-backdrop.active {
        display: flex;
      }

      .oc-modal {
        width: min(720px, 100%);
        max-height: 85vh;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 30px 90px rgba(15, 23, 42, .35);
        overflow: hidden;
        display: flex;
        flex-direction: column;
      }

      .oc-modal-head {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        background: #f9fafb;
      }

      .oc-modal-title {
        font-size: 18px;
        font-weight: 950;
        color: #111827;
        margin: 0;
      }

      .oc-modal-sub {
        margin-top: 4px;
        font-size: 13px;
        color: var(--text-muted);
      }

      .oc-modal-close {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: #fff;
        color: #6b7280;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        line-height: 1;
      }

      .oc-modal-close:hover {
        color: #111827;
        border-color: #d1d5db;
      }

      .oc-modal-body {
        padding: 18px 20px;
        overflow: auto;
      }

      .oc-history-timeline {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .oc-history-full-item {
        position: relative;
        display: grid;
        grid-template-columns: 28px 1fr;
        gap: 12px;
      }

      .oc-history-full-item:not(:last-child)::after {
        content: "";
        position: absolute;
        left: 13px;
        top: 28px;
        bottom: -14px;
        width: 2px;
        background: #e5e7eb;
      }

      .oc-history-full-dot {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        position: relative;
        z-index: 1;
      }

      .oc-history-full-dot::after {
        content: "";
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #6b7280;
      }

      .oc-history-full-dot.created::after {
        background: #3b82f6;
      }

      .oc-history-full-dot.progress::after {
        background: #f59e0b;
      }

      .oc-history-full-dot.edited::after {
        background: #6b7280;
      }

      .oc-history-full-dot.ended::after,
      .oc-history-full-dot.end::after {
        background: #10b981;
      }

      .oc-history-full-dot.junk::after {
        background: #ef4444;
      }

      .oc-history-full-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px 14px;
        background: #fff;
      }

      .oc-history-full-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
      }

      .oc-history-full-label {
        font-size: 14px;
        font-weight: 950;
        color: #111827;
      }

      .oc-history-full-date {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-muted);
        white-space: nowrap;
      }

      .oc-history-full-user {
        margin-top: 8px;
        font-size: 13px;
        color: #374151;
      }

      .oc-history-full-note {
        margin-top: 8px;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
      }
    </style>
  @endpush
@endonce

@section('content')
  <div class="oc-wrap" id="ticketBoardApp" data-fetch-url="{{ route('tickets.fetch') }}"
    data-kanban-url="{{ route('tickets.kanban') }}"
    data-default-view="{{ $viewMode === 'kanban' ? 'kanban' : $viewMode }}"
    data-profile-base="{{ url('problem/profile') }}" data-status-url-base="{{ url('/tickets') }}">

    {{-- CI-Vereinheitlichung 2026-07-15: Alt-Kopf (oc-titlebar) durch das gemeinsame Bauteil ersetzt. --}}
    <x-page-head title="Tickets"
        sub="Verwalten Sie erstellte, zugewiesene, laufende, abgeschlossene und Junk-Tickets zentral."
        current="Tickets">
        <x-slot:actions>
            <a href="{{ route('problem.create') }}" class="oc-btn">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Neues Ticket
            </a>
        </x-slot:actions>
    </x-page-head>

    <div class="oc-analytics">
      <div class="oc-stat">
        <div class="oc-stat-icon total">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 12h18M3 6h18M3 18h18" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Meine Tickets</div>
          <div class="oc-stat-value" id="stat-mine">{{ $counts['mine'] ?? 0 }}</div>
          <div class="oc-stat-sub">Mir zugewiesen</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon published">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Erstellt</div>
          <div class="oc-stat-value" id="stat-created">{{ $counts['created'] ?? 0 }}</div>
          <div class="oc-stat-sub">Von mir erstellt</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon unpublished">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v6m0 4v10m10-10h-6M8 12H2" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">In Bearbeitung</div>
          <div class="oc-stat-value" id="stat-process">{{ $counts['progress'] ?? 0 }}</div>
          <div class="oc-stat-sub">Laufende Tickets</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon type">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 6L9 17l-5-5" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Beendet</div>
          <div class="oc-stat-value" id="stat-end">{{ $counts['completed'] ?? 0 }}</div>
          <div class="oc-stat-sub">Abgeschlossene Tickets</div>
        </div>
      </div>
    </div>

    <div class="oc-tabs" id="filterTabs">
      <button type="button" class="oc-tab active" data-filter="active">Aktiv</button>
      <button type="button" class="oc-tab" data-filter="mine">Meine aktiven Tickets</button>
      <button type="button" class="oc-tab" data-filter="created">Von mir erstellt</button>
      <button type="button" class="oc-tab" data-filter="progress">In Bearbeitung</button>
      <button type="button" class="oc-tab" data-filter="archive">Archiv</button>
      <button type="button" class="oc-tab" data-filter="junk">Junk</button>
      <button type="button" class="oc-tab oc-tab-all" data-filter="all">
        Alle Tickets
        <span class="oc-tab-badge" id="tab-all-count">{{ $counts['all'] ?? 0 }}</span>
      </button>
    </div>

    <form class="oc-toolbar" id="ticketToolbar" onsubmit="return false;">
      <div class="oc-toolbar-left">
        <div class="oc-filter-block search">
          <label class="oc-filter-label">Suche</label>
          <input type="text" class="oc-input search" id="searchInput"
            placeholder="Suche nach Ticketnummer, Kunde, Produkt, Adresse, Fehlercode">
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Priorität</label>
          <select class="oc-select" id="priorityFilter">
            <option value="">Alle</option>
            <option value="high">Hoch</option>
            <option value="medium">Mittel</option>
            <option value="low">Niedrig</option>
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Ansicht</label>
          <div class="oc-tabs" style="margin-bottom:0;" id="viewTabs">
            <button type="button" class="oc-tab {{ $viewMode === 'list' ? 'active' : '' }}"
              data-view="list">Liste</button>
            <button type="button" class="oc-tab {{ $viewMode === 'cards' ? 'active' : '' }}"
              data-view="cards">Karten</button>
            <button type="button" class="oc-tab {{ $viewMode === 'kanban' ? 'active' : '' }}"
              data-view="kanban">Kanban</button>
          </div>
        </div>
      </div>

      <div class="oc-toolbar-right">
        <button class="oc-btn-soft" type="button" id="reloadBtn">Aktualisieren</button>
        <button class="oc-btn-soft" type="button" id="resetBtn">Zurücksetzen</button>
      </div>
    </form>

    <div class="oc-card">
      <div class="oc-view-panels">
        <div class="oc-view-panel {{ $viewMode !== 'kanban' ? 'active' : '' }}" id="standardPanel">
          <div id="ticketsContent">
            <div class="oc-loading">Daten werden geladen ...</div>
          </div>
        </div>

        <div class="oc-view-panel {{ $viewMode === 'kanban' ? 'active' : '' }}" id="kanbanPanel">
          <div id="kanbanContent">
            <div class="oc-loading">Kanban wird geladen ...</div>
          </div>
        </div>
      </div>
    </div>

    <div id="ticketsPagination"></div>
  </div>

  <div class="oc-toast-wrap" id="toast-wrap"></div>

  <div class="oc-modal-backdrop" id="historyModal" aria-hidden="true">
    <div class="oc-modal" role="dialog" aria-modal="true" aria-labelledby="historyModalTitle">
      <div class="oc-modal-head">
        <div>
          <h3 class="oc-modal-title" id="historyModalTitle">Status-Historie</h3>
          <div class="oc-modal-sub" id="historyModalSub">Alle Änderungen dieses Tickets</div>
        </div>

        <button type="button" class="oc-modal-close" data-history-modal-close aria-label="Schließen">
          ×
        </button>
      </div>

      <div class="oc-modal-body" id="historyModalBody">
        <div class="oc-subt">Keine Historie</div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    (function () {
      "use strict";

      const app = document.getElementById('ticketBoardApp');
      if (!app) return;

      const fetchUrl = app.dataset.fetchUrl;
      const kanbanUrl = app.dataset.kanbanUrl;
      const defaultView = app.dataset.defaultView || 'list';
      const profileBase = app.dataset.profileBase || '';
      const statusUrlBase = app.dataset.statusUrlBase || '';

      const state = {
        filter: 'active',
        view: defaultView,
        search: '',
        priority: '',
        page: 1
      };

      const els = {
        filterTabs: document.getElementById('filterTabs'),
        viewTabs: document.getElementById('viewTabs'),
        searchInput: document.getElementById('searchInput'),
        priorityFilter: document.getElementById('priorityFilter'),
        ticketsContent: document.getElementById('ticketsContent'),
        kanbanContent: document.getElementById('kanbanContent'),
        ticketsPagination: document.getElementById('ticketsPagination'),
        standardPanel: document.getElementById('standardPanel'),
        kanbanPanel: document.getElementById('kanbanPanel'),
        reloadBtn: document.getElementById('reloadBtn'),
        resetBtn: document.getElementById('resetBtn'),
      };

      function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      }

      const ERROR_TYPE_LABELS = {
        complaint: 'Reklamation',
        emergency_service: 'Notdienst',
        repair: 'Reparatur',
        maintenance: 'Wartung',
        malfunction: 'Störung',
        installation: 'Installation',
        configuration_error: 'Konfiguration',
        system_outage: 'Systemausfall',
        security_issue: 'Sicherheitsproblem',
        user_error: 'Bedienungsfehler',
        network_problem: 'Netzwerkfehler',
        software_bug: 'Softwarefehler',
        hardware_defect: 'Hardwarefehler',
        spare_part_request: 'Ersatzteilanfrage',
        timeout: 'Zeitüberschreitung',
        communication_failure: 'Kommunikationsproblem',
        power_outage: 'Energieausfall',
        update_failure: 'Updatefehler',
        access_issue: 'Zugriffsproblem',
        other: 'Sonstiges'
      };


      const ticketHistoryStore = new Map();

      function rememberTicketHistory(ticket) {
        if (!ticket || !ticket.id) return;

        ticketHistoryStore.set(String(ticket.id), {
          id: ticket.id,
          ticket_no: ticket.ticket_no || ticket.id,
          customer: ticket.customer || 'Kein Kunde',
          history: Array.isArray(ticket.history) ? ticket.history : []
        });
      }

      function openHistoryModal(ticketId) {
        const modal = document.getElementById('historyModal');
        const title = document.getElementById('historyModalTitle');
        const sub = document.getElementById('historyModalSub');
        const body = document.getElementById('historyModalBody');

        if (!modal || !body) return;

        const ticket = ticketHistoryStore.get(String(ticketId));

        if (!ticket) {
          body.innerHTML = `<div class="oc-subt">Historie konnte nicht gefunden werden.</div>`;
        } else {
          if (title) {
            title.textContent = `Status-Historie #${ticket.ticket_no}`;
          }

          if (sub) {
            sub.textContent = ticket.customer || 'Alle Änderungen dieses Tickets';
          }

          body.innerHTML = renderHistoryModalBody(ticket.history);
        }

        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
      }

      function closeHistoryModal() {
        const modal = document.getElementById('historyModal');

        if (!modal) return;

        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
      }

      function bindHistoryModal() {
        document.querySelectorAll('[data-history-open]').forEach(btn => {
          if (btn.dataset.bound === '1') return;
          btn.dataset.bound = '1';

          btn.addEventListener('click', function () {
            openHistoryModal(this.dataset.ticketId);
          });
        });
      }

      function escapeHtml(value) {
        return String(value ?? '')
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#039;');
      }

      function normalizeKey(value) {
        return String(value ?? '')
          .trim()
          .toLowerCase()
          .replaceAll('-', '_')
          .replace(/\s+/g, '_');
      }

      function humanizeGermanFallback(value) {
        const raw = String(value ?? '').trim();

        if (!raw) return 'Kein Fehlertyp';

        return raw
          .replaceAll('_', ' ')
          .replaceAll('-', ' ')
          .split(' ')
          .filter(Boolean)
          .map(word => word.charAt(0).toUpperCase() + word.slice(1))
          .join(' ');
      }

      function errorTypeLabel(type, serverLabel = null) {
        const server = String(serverLabel ?? '').trim();

        if (server && server !== type) {
          return server;
        }

        const key = normalizeKey(type);

        if (ERROR_TYPE_LABELS[key]) {
          return ERROR_TYPE_LABELS[key];
        }

        return humanizeGermanFallback(type);
      }

      function ticketErrorTypeLabel(ticket) {
        return errorTypeLabel(ticket?.error_type, ticket?.error_type_label);
      }

      function toast(kind, title, msg) {
        const wrap = document.getElementById('toast-wrap');
        if (!wrap) return;

        const icons = {
          ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
          bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
        };

        const el = document.createElement('div');
        el.className = 'oc-toast';
        el.innerHTML = `
                  <div class="oc-toast-ic ${escapeHtml(kind)}">${icons[kind] || icons.ok}</div>
                  <div style="flex:1;">
                      <p class="oc-toast-ttl">${escapeHtml(title)}</p>
                      <p class="oc-toast-msg">${escapeHtml(msg)}</p>
                  </div>
                  <button class="oc-toast-x" type="button">×</button>
              `;

        el.querySelector('.oc-toast-x')?.addEventListener('click', () => el.remove());
        wrap.appendChild(el);

        setTimeout(() => {
          try {
            el.remove();
          } catch (e) { }
        }, 4000);
      }

      function setActiveButton(container, value, attr) {
        if (!container) return;

        container.querySelectorAll(`[${attr}]`).forEach(btn => {
          btn.classList.toggle('active', btn.getAttribute(attr) === value);
        });
      }

      function formatDate(dateStr, withTime = true) {
        if (!dateStr) return '—';

        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return escapeHtml(dateStr);

        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yyyy = d.getFullYear();

        if (!withTime) return `${dd}.${mm}.${yyyy}`;

        const hh = String(d.getHours()).padStart(2, '0');
        const ii = String(d.getMinutes()).padStart(2, '0');

        return `${dd}.${mm}.${yyyy} ${hh}:${ii}`;
      }

      function statusClass(status) {
        switch (normalizeKey(status)) {
          case 'process':
          case 'in_bearbeitung':
            return 'process';

          case 'end':
          case 'done':
          case 'beendet':
            return 'end';

          case 'junk':
            return 'junk';

          case 'offen':
          case 'open':
          default:
            return 'open';
        }
      }

      function statusLabel(status) {
        switch (normalizeKey(status)) {
          case 'process':
          case 'in_bearbeitung':
            return 'In Bearbeitung';

          case 'end':
          case 'done':
          case 'beendet':
            return 'Beendet';

          case 'junk':
            return 'Junk';

          case 'offen':
          case 'open':
          default:
            return 'Offen';
        }
      }

      function priorityClass(priority) {
        switch (normalizeKey(priority)) {
          case 'high':
          case 'dringend':
          case 'sehr_dringend':
          case 'very_high':
            return 'high';

          case 'medium':
          case 'mittel':
            return 'medium';

          case 'low':
          case 'normal':
            return 'low';

          default:
            return '';
        }
      }

      function priorityLabel(priority) {
        if (!priority) return '—';

        switch (normalizeKey(priority)) {
          case 'high':
            return 'Hoch';

          case 'medium':
            return 'Mittel';

          case 'low':
            return 'Niedrig';

          case 'dringend':
            return 'Dringend';

          case 'sehr_dringend':
          case 'very_high':
            return 'Sehr dringend';

          case 'normal':
            return 'Normal';

          default:
            return humanizeGermanFallback(priority);
        }
      }

      function avatar(url, name, size = 'sm') {
        const fallback = "{{ asset('images/gender/male.png') }}";
        const imgUrl = url || fallback;

        return `
                  <img src="${escapeHtml(imgUrl)}"
                       alt="${escapeHtml(name || '')}"
                       title="${escapeHtml(name || '')}"
                       class="oc-avatar ${escapeHtml(size)}">
              `;
      }

      function renderAssignedUsers(users) {
        if (!Array.isArray(users) || !users.length) {
          return `<div class="oc-subt">Niemand zugewiesen</div>`;
        }

        const avatars = users.map(u => avatar(u.avatar_url, u.name)).join('');
        const names = users.map(u => escapeHtml(u.name || '')).join(', ');

        return `
                  <div class="oc-avatar-group">${avatars}</div>
                  <div class="oc-subt-wrap" style="margin-top:8px; display:none;">${names}</div>
              `;
      }

      function renderCreator(user, date) {
        if (!user) {
          return `<div class="oc-subt">Kein Ersteller</div>`;
        }

        return `
                  <div class="oc-user-stack">
                      ${avatar(user.avatar_url, user.name, 'lg')}
                      <div class="oc-avatar-meta">
                          <div class="oc-avatar-name">${escapeHtml(user.name || '')}</div>
                          <div class="oc-avatar-role">${formatDate(date)}</div>
                      </div>
                  </div>
              `;
      }

      function sortHistoryLatestFirst(history) {
        if (!Array.isArray(history)) return [];

        return [...history].sort((a, b) => {
          const dateA = new Date(a?.date || a?.created_at || 0).getTime();
          const dateB = new Date(b?.date || b?.created_at || 0).getTime();

          return dateB - dateA;
        });
      }

      function renderLatestHistory(ticket) {
        const history = sortHistoryLatestFirst(ticket?.history || []);

        if (!history.length) {
          return `<div class="oc-subt">Keine Historie</div>`;
        }

        const latest = history[0];
        const ticketNo = ticket?.ticket_no || ticket?.id || '';

        return `
                <button type="button"
                        class="oc-latest-history-btn"
                        data-history-open
                        data-ticket-id="${escapeHtml(ticket.id)}"
                        title="Alle Statusänderungen anzeigen">
                    <span class="oc-latest-history-dot ${escapeHtml(latest.type || '')}"></span>

                    <span class="oc-latest-history-content">
                        <span class="oc-latest-history-label">
                            ${escapeHtml(latest.label || 'Status geändert')}
                        </span>

                        <span class="oc-latest-history-meta">
                            ${escapeHtml(latest?.user?.name || 'Unbekannt')} · ${formatDate(latest.date)}
                        </span>
                    </span>

                    <span class="oc-latest-history-icon">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </span>
                </button>
            `;
      }

      function renderHistoryModalBody(history) {
        const items = sortHistoryLatestFirst(history || []);

        if (!items.length) {
          return `<div class="oc-subt">Keine Historie vorhanden.</div>`;
        }

        return `
                <div class="oc-history-timeline">
                    ${items.map(item => `
                        <div class="oc-history-full-item">
                            <div class="oc-history-full-dot ${escapeHtml(item.type || '')}"></div>

                            <div class="oc-history-full-card">
                                <div class="oc-history-full-top">
                                    <div class="oc-history-full-label">
                                        ${escapeHtml(item.label || 'Status geändert')}
                                    </div>

                                    <div class="oc-history-full-date">
                                        ${formatDate(item.date)}
                                    </div>
                                </div>

                                <div class="oc-history-full-user">
                                    Von: <strong>${escapeHtml(item?.user?.name || 'Unbekannt')}</strong>
                                </div>

                                ${item.note || item.description || item.comment ? `
                                    <div class="oc-history-full-note">
                                        ${escapeHtml(item.note || item.description || item.comment)}
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
      }

      function profileUrl(ticket) {
        if (ticket.profile_url) return ticket.profile_url;
        return `${profileBase}/${ticket.id}`;
      }

      function editUrl(ticket) {
        if (ticket.edit_url) return ticket.edit_url;
        return `/problem_edit/${ticket.id}`;
      }

      function deleteUrl(ticket) {
        if (ticket.delete_url) return ticket.delete_url;
        return `/problem_destroy/${ticket.id}`;
      }

      function statusSelect(ticket) {
        const current = normalizeKey(ticket.status || 'offen') === 'open'
          ? 'offen'
          : normalizeKey(ticket.status || 'offen');

        return `
              <select class="oc-status-select"
                      data-ticket-status-select
                      data-ticket-id="${escapeHtml(ticket.id)}"
                      title="Status ändern">
                  <option value="offen" ${current === 'offen' ? 'selected' : ''}>Offen</option>
                  <option value="process" ${current === 'process' ? 'selected' : ''}>In Bearbeitung</option>
                  <option value="end" ${current === 'end' ? 'selected' : ''}>Beendet</option>
                  <option value="junk" ${current === 'junk' ? 'selected' : ''}>Junk</option>
              </select>
          `;
      }
      function actionButtons(ticket) {
        return `
              ${statusSelect(ticket)}

              <a href="${escapeHtml(profileUrl(ticket))}"
                class="oc-btn-ic primary"
                title="Anzeigen">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                      <circle cx="12" cy="12" r="3"/>
                  </svg>
              </a>

              <a href="${escapeHtml(editUrl(ticket))}"
                class="oc-btn-ic warning"
                title="Bearbeiten">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
              </a>

              <a href="${escapeHtml(deleteUrl(ticket))}"
                class="oc-btn-ic danger"
                title="Löschen"
                onclick="return confirm('Möchten Sie dieses Ticket wirklich löschen?')">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                  </svg>
              </a>
          `;
      }


      function renderListFromJson(items) {
        if (!Array.isArray(items) || !items.length) {
          return `<div class="oc-empty">Keine Tickets gefunden.</div>`;
        }

        return `
                    <div class="oc-list-head">
                        <div>Ticket</div>
                        <div>Kunde / Problem</div>
                        <div>Status</div>
                        <div>Erstellt von</div>
                        <div>Zuständig</div>
                        <div>Letzte Historie</div>
                        <div style="text-align:right;">Aktionen</div>
                    </div>

                    <div class="oc-list">
                        ${items.map(ticket => {
          rememberTicketHistory(ticket);

          return `
                                <div class="oc-item">
                                    <div class="oc-item-row">
                                        <div class="oc-cell">
                                            <div class="oc-cell-title">Ticket</div>
                                            <span class="oc-id-badge">#${escapeHtml(ticket.ticket_no || ticket.id)}</span>
                                        </div>

                                        <div class="oc-cell">
                                            <div class="oc-cell-title">Kunde / Problem</div>
                                            <div class="oc-main">
                                                <div class="oc-ttl">${escapeHtml(ticket.customer || 'Kein Kunde')}</div>
                                                <div class="oc-subt-wrap">
                                                    ${escapeHtml(ticket.product || '—')}<br>
                                                    ${escapeHtml(ticketErrorTypeLabel(ticket))}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="oc-cell">
                                            <div class="oc-cell-title">Status</div>
                                            <div class="oc-main">
                                                <span class="oc-status-pill ${statusClass(ticket.status)}">${statusLabel(ticket.status)}</span>
                                                <div style="margin-top:8px;">
                                                    <span class="oc-priority ${priorityClass(ticket.priority)}">${escapeHtml(priorityLabel(ticket.priority))}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="oc-cell">
                                            <div class="oc-cell-title">Erstellt von</div>
                                            ${renderCreator(ticket.creator_user || ticket.created_by_user, ticket.start_date || ticket.created_at)}
                                        </div>

                                        <div class="oc-cell">
                                            <div class="oc-cell-title">Zuständig</div>
                                            ${renderAssignedUsers(ticket.employees)}
                                        </div>

                                        <div class="oc-cell">
                                            <div class="oc-cell-title">Letzte Historie</div>
                                            ${renderLatestHistory(ticket)}
                                        </div>

                                        <div class="oc-cell">
                                            <div class="oc-cell-title">Aktionen</div>
                                            <div class="oc-actions">
                                                ${actionButtons(ticket)}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
        }).join('')}
                    </div>
                `;
      }
      function renderCardsFromJson(items) {
        if (!Array.isArray(items) || !items.length) {
          return `<div class="oc-empty">Keine Tickets gefunden.</div>`;
        }

        return `
                  <div class="oc-card-grid">
                      ${items.map(ticket => `
                          <div class="ticket-card">
                              <div class="ticket-card-top">
                                  <div>
                                      <div class="ticket-card-title">#${escapeHtml(ticket.ticket_no || ticket.id)}</div>
                                      <div class="ticket-card-meta">${escapeHtml(ticket.customer || 'Kein Kunde')}</div>
                                  </div>

                                  <span class="oc-status-pill ${statusClass(ticket.status)}">${statusLabel(ticket.status)}</span>
                              </div>

                              <div class="ticket-card-section">
                                  <div class="ticket-card-label">Produkt</div>
                                  <div class="ticket-card-meta">${escapeHtml(ticket.product || '—')}</div>
                              </div>

                              <div class="ticket-card-section">
                                  <div class="ticket-card-label">Fehlertyp</div>
                                  <div class="ticket-card-meta">${escapeHtml(ticketErrorTypeLabel(ticket))}</div>
                              </div>

                              <div class="ticket-card-section">
                                  <div class="ticket-card-label">Priorität</div>
                                  <span class="oc-priority ${priorityClass(ticket.priority)}">${escapeHtml(priorityLabel(ticket.priority))}</span>
                              </div>

                              <div class="ticket-card-section">
                                  <div class="ticket-card-label">Erstellt von</div>
                                  ${renderCreator(ticket.creator_user || ticket.created_by_user, ticket.start_date || ticket.created_at)}
                              </div>

                              <div class="ticket-card-section">
                                  <div class="ticket-card-label">Zuständig</div>
                                  ${renderAssignedUsers(ticket.employees)}
                              </div>

                              <div class="ticket-card-section">
                                  <div class="ticket-card-label">Historie</div>
                                   ${renderLatestHistory(ticket)}
                              </div>

                              <div class="ticket-card-actions">
                                  ${actionButtons(ticket)}
                              </div>
                          </div>
                      `).join('')}
                  </div>
              `;
      }

      function renderKanban(data) {
        const groups = {
          offen: [],
          process: [],
          end: [],
          junk: []
        };

        (data || []).forEach(ticket => {
          let key = normalizeKey(ticket.status || 'offen');

          if (key === 'open') key = 'offen';
          if (key === 'in_bearbeitung') key = 'process';
          if (key === 'done' || key === 'beendet') key = 'end';

          if (groups[key]) {
            groups[key].push(ticket);
          } else {
            groups.offen.push(ticket);
          }
        });

        const cols = [
          ['offen', 'Offen'],
          ['process', 'In Bearbeitung'],
          ['end', 'Beendet'],
          ['junk', 'Junk'],
        ];

        let html = `<div class="kanban-wrap">`;

        cols.forEach(([key, label]) => {
          html += `
                <div class="kanban-col" data-kanban-col="${escapeHtml(key)}">
                    <div class="kanban-col-head">
                        <div class="kanban-col-title">
                            <span>${label}</span>
                            <span class="kanban-count">${groups[key].length}</span>
                        </div>
                    </div>

                    <div class="kanban-body"
                         data-kanban-dropzone
                         data-status="${escapeHtml(key)}">
            `;

          if (!groups[key].length) {
            html += `<div class="oc-empty" style="margin:0;" data-empty-kanban>Keine Tickets</div>`;
          } else {
            groups[key].forEach(ticket => {
              const creator = ticket.creator_user || ticket.created_by_user;

              html += `
                        <div class="kanban-ticket"
                             draggable="true"
                             data-ticket-card
                             data-ticket-id="${escapeHtml(ticket.id)}"
                             data-current-status="${escapeHtml(ticket.status || 'offen')}">

                            <div class="kanban-ticket-no">#${escapeHtml(ticket.ticket_no || ticket.id)}</div>
                            <div class="kanban-ticket-title">${escapeHtml(ticket.customer || 'Kein Kunde')}</div>
                            <div class="kanban-ticket-sub">${escapeHtml(ticket.product || '—')}</div>
                            <div class="kanban-ticket-sub">${escapeHtml(ticketErrorTypeLabel(ticket))}</div>

                            ${creator ? `
                                <div class="oc-user-stack" style="margin-top:10px;">
                                    ${avatar(creator.avatar_url, creator.name, 'sm')}
                                    <div class="oc-avatar-meta">
                                        <div class="oc-avatar-name">${escapeHtml(creator.name || '')}</div>
                                        <div class="oc-avatar-role">${formatDate(ticket.start_date || ticket.created_at)}</div>
                                    </div>
                                </div>
                            ` : ''}

                            ${(ticket.employees && ticket.employees.length) ? `
                                <div class="oc-kanban-users">
                                    ${ticket.employees.map(u => avatar(u.avatar_url, u.name, 'sm')).join('')}
                                </div>
                            ` : ''}

                            <div class="ticket-card-actions" style="margin-top:10px;">
                                <span class="oc-status-pill ${statusClass(ticket.status)}">${statusLabel(ticket.status)}</span>
                                ${actionButtons(ticket)}
                            </div>
                        </div>
                    `;
            });
          }

          html += `
                    </div>
                </div>
            `;
        });

        html += `</div>`;

        els.kanbanContent.innerHTML = html;

        bindStatusSelects();
        bindHistoryModal();
        bindKanbanDragDrop();
      }

      function updateAnalytics(analytics) {
        if (!analytics) return;

        const created = document.getElementById('stat-created');
        const process = document.getElementById('stat-process');
        const end = document.getElementById('stat-end');
        const mine = document.getElementById('stat-mine');

        const allBadge = document.getElementById('tab-all-count');

        if (created && analytics.created !== undefined) created.textContent = analytics.created;
        if (process && analytics.process !== undefined) process.textContent = analytics.process;
        if (end && analytics.end !== undefined) end.textContent = analytics.end;
        if (mine && analytics.mine !== undefined) mine.textContent = analytics.mine;
        if (allBadge && analytics.all !== undefined) allBadge.textContent = analytics.all;
      }

      async function loadStandard(page = 1) {
        state.page = page;

        els.standardPanel?.classList.add('active');
        els.kanbanPanel?.classList.remove('active');

        if (els.ticketsContent) {
          els.ticketsContent.innerHTML = `<div class="oc-loading">Daten werden geladen ...</div>`;
        }

        if (els.ticketsPagination) {
          els.ticketsPagination.innerHTML = '';
        }

        const params = new URLSearchParams({
          filter: state.filter,
          mode: state.view,
          search: state.search,
          priority: state.priority,
          page: state.page
        });

        try {
          const res = await fetch(`${fetchUrl}?${params.toString()}`, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          });

          if (!res.ok) throw new Error('Fehler beim Laden');

          const data = await res.json();

          if (data.items && Array.isArray(data.items)) {
            els.ticketsContent.innerHTML = state.view === 'cards'
              ? renderCardsFromJson(data.items)
              : renderListFromJson(data.items);
          } else {
            els.ticketsContent.innerHTML = data.html || `<div class="oc-empty">Keine Tickets gefunden.</div>`;
          }

          els.ticketsPagination.innerHTML = data.pagination
            ? `<div class="oc-pagination">${data.pagination}</div>`
            : '';

          updateAnalytics(data.analytics);
          bindPagination();
          bindStatusSelects();
          bindHistoryModal();
        } catch (e) {
          if (els.ticketsContent) {
            els.ticketsContent.innerHTML = `<div class="oc-empty">Die Ticketliste konnte nicht geladen werden.</div>`;
          }

          toast('bad', 'Fehler', 'Die Ticketliste konnte nicht geladen werden.');
        }
      }

      async function loadKanban() {
        els.standardPanel?.classList.remove('active');
        els.kanbanPanel?.classList.add('active');

        if (els.kanbanContent) {
          els.kanbanContent.innerHTML = `<div class="oc-loading">Kanban wird geladen ...</div>`;
        }

        if (els.ticketsPagination) {
          els.ticketsPagination.innerHTML = '';
        }

        const params = new URLSearchParams({
          filter: state.filter,
          search: state.search
        });

        try {
          const res = await fetch(`${kanbanUrl}?${params.toString()}`, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          });

          if (!res.ok) throw new Error('Fehler beim Laden');

          const data = await res.json();
          renderKanban(data);
        } catch (e) {
          if (els.kanbanContent) {
            els.kanbanContent.innerHTML = `<div class="oc-empty">Das Kanban konnte nicht geladen werden.</div>`;
          }

          toast('bad', 'Fehler', 'Das Kanban konnte nicht geladen werden.');
        }
      }

      async function updateTicketStatus(ticketId, status) {
        if (!ticketId || !status) return false;

        try {
          const res = await fetch(`${statusUrlBase}/${ticketId}/status`, {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ status })
          });

          const data = await res.json().catch(() => ({}));

          if (!res.ok || data.success === false) {
            throw new Error(data.message || 'Status konnte nicht geändert werden.');
          }

          toast('ok', 'Aktualisiert', data.message || 'Status aktualisiert.');
          return true;
        } catch (e) {
          toast('bad', 'Fehler', e.message || 'Status konnte nicht geändert werden.');
          return false;
        }
      }

      function bindKanbanDragDrop() {
        let draggedTicketId = null;
        let draggedEl = null;

        document.querySelectorAll('[data-ticket-card]').forEach(card => {
          card.addEventListener('dragstart', function (e) {
            draggedTicketId = this.dataset.ticketId;
            draggedEl = this;

            this.classList.add('dragging');

            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', draggedTicketId);
          });

          card.addEventListener('dragend', function () {
            this.classList.remove('dragging');

            document.querySelectorAll('[data-kanban-dropzone]').forEach(zone => {
              zone.classList.remove('drag-over');
            });

            draggedTicketId = null;
            draggedEl = null;
          });
        });

        document.querySelectorAll('[data-kanban-dropzone]').forEach(zone => {
          zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('drag-over');
            e.dataTransfer.dropEffect = 'move';
          });

          zone.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
          });

          zone.addEventListener('drop', async function (e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            const ticketId = draggedTicketId || e.dataTransfer.getData('text/plain');
            const newStatus = this.dataset.status;

            if (!ticketId || !newStatus) return;

            const currentStatus = normalizeKey(draggedEl?.dataset?.currentStatus || '');

            if (currentStatus === newStatus || (currentStatus === 'open' && newStatus === 'offen')) {
              return;
            }

            if (draggedEl) {
              this.querySelector('[data-empty-kanban]')?.remove();
              this.appendChild(draggedEl);
            }

            const ok = await updateTicketStatus(ticketId, newStatus);

            if (!ok) {
              loadKanban();
              return;
            }

            loadKanban();
          });
        });
      }

      function bindStatusSelects() {
        document.querySelectorAll('[data-ticket-status-select]').forEach(select => {
          if (select.dataset.bound === '1') return;
          select.dataset.bound = '1';

          select.addEventListener('change', async function () {
            const ticketId = this.dataset.ticketId;
            const newStatus = this.value;
            const oldValue = this.dataset.oldValue || this.getAttribute('data-old-value') || '';

            this.disabled = true;

            const ok = await updateTicketStatus(ticketId, newStatus);

            this.disabled = false;

            if (!ok && oldValue) {
              this.value = oldValue;
              return;
            }

            reloadCurrent();
          });

          select.dataset.oldValue = select.value;
        });
      }

      document.addEventListener('click', function (e) {
        if (e.target.closest('[data-history-modal-close]')) {
          closeHistoryModal();
          return;
        }

        const modal = document.getElementById('historyModal');

        if (modal && e.target === modal) {
          closeHistoryModal();
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          closeHistoryModal();
        }
      });
      function reloadCurrent() {
        if (state.view === 'kanban') {
          loadKanban();
        } else {
          loadStandard(1);
        }
      }

      function bindPagination() {
        if (!els.ticketsPagination) return;

        els.ticketsPagination.querySelectorAll('a').forEach(a => {
          a.addEventListener('click', function (e) {
            e.preventDefault();

            const url = new URL(this.href);
            const page = url.searchParams.get('page') || 1;

            loadStandard(page);
          });
        });
      }

      els.filterTabs?.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-filter]');
        if (!btn) return;

        state.filter = btn.dataset.filter;
        setActiveButton(els.filterTabs, state.filter, 'data-filter');
        reloadCurrent();
      });

      els.viewTabs?.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-view]');
        if (!btn) return;

        state.view = btn.dataset.view;
        setActiveButton(els.viewTabs, state.view, 'data-view');
        reloadCurrent();
      });

      let searchTimer = null;

      els.searchInput?.addEventListener('input', function () {
        state.search = this.value.trim();

        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => reloadCurrent(), 350);
      });

      els.priorityFilter?.addEventListener('change', function () {
        state.priority = this.value;

        if (state.view === 'kanban') {
          toast('bad', 'Hinweis', 'Prioritätsfilter ist nur in Liste und Karten aktiv.');
          return;
        }

        reloadCurrent();
      });

      els.reloadBtn?.addEventListener('click', reloadCurrent);

      els.resetBtn?.addEventListener('click', function () {
        state.filter = 'active';
        state.view = 'list';
        state.search = '';
        state.priority = '';
        state.page = 1;

        if (els.searchInput) els.searchInput.value = '';
        if (els.priorityFilter) els.priorityFilter.value = '';

        setActiveButton(els.filterTabs, 'active', 'data-filter');
        setActiveButton(els.viewTabs, 'list', 'data-view');

        reloadCurrent();
      });

      setActiveButton(els.filterTabs, state.filter, 'data-filter');
      setActiveButton(els.viewTabs, state.view, 'data-view');

      reloadCurrent();

      @if(session('update_msg'))
        toast('ok', 'Aktualisiert', @json(session('update_msg')));
      @endif

      @if(session('save_msg'))
        toast('ok', 'Gespeichert', @json(session('save_msg')));
      @endif

      @if(session('delete_msg'))
        toast('bad', 'Gelöscht', @json(session('delete_msg')));
      @endif
      })();
  </script>
@endpush

@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Ticketliste',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush