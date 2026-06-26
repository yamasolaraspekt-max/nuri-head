@extends('admin.layouts.app')
@section('title', 'Berichtscenter')

@php
  $pageTitle = 'BERICHTSCENTER';

  $employeeName = '—';

  if (isset($employee) && $employee) {
    $employeeName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
  }

  if ($employeeName === '') {
    $employeeName = 'Mitarbeiter #' . ($employeeId ?? 0);
  }
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
        color: #111827;
      }

      .oc-sub {
        font-size: 14px;
        color: var(--text-muted);
        margin-top: 4px;
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
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
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
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }

      .oc-btn-soft:hover {
        background: #f9fafb;
        color: var(--text-main);
        text-decoration: none;
      }

      .oc-btn-soft.active {
        background: var(--primary-light);
        border-color: var(--primary);
        color: #4d7c0f;
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
        background: var(--primary-light);
      }

      .oc-btn-ic.danger {
        color: var(--danger);
        border-color: rgba(239, 68, 68, .18);
        background: var(--danger-light);
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

      .oc-stat-icon.today {
        background: var(--success-light);
        color: var(--success)
      }

      .oc-stat-icon.delay {
        background: var(--warning-light);
        color: #d97706
      }

      .oc-stat-icon.custom {
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
        min-width: 280px;
      }

      .oc-filter-label {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      .oc-input {
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

      .oc-input.search-input {
        padding-left: 36px;
        min-width: 280px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 10px center;
        background-size: 16px;
      }

      .oc-input:focus {
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
      }

      .oc-due-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
      }

      .oc-due-tab {
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text-main);
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 900;
        cursor: pointer;
        transition: var(--transition);
      }

      .oc-due-tab:hover {
        background: #f9fafb;
      }

      .oc-due-tab.active {
        background: var(--primary-light);
        border-color: var(--primary);
        color: #4d7c0f;
      }

      .oc-type-list {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
      }

      .oc-type-list label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 10px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        cursor: pointer;
      }

      .oc-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        overflow: visible;
      }

      .oc-list-head {
        display: grid;
        grid-template-columns: 54px 80px minmax(260px, 1.7fr) 120px 180px 110px 220px;
        gap: 14px;
        align-items: center;
        padding: 16px 16px 10px 16px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      @media(max-width:1280px) {
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
        overflow: visible;
        margin: 0 16px;
        position: relative;
      }

      .oc-item:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow);
      }

      .oc-item.selected {
        border-color: var(--primary);
        background: linear-gradient(135deg, #ffffff, #fbfff2);
      }

      .oc-item-row {
        padding: 16px;
        display: grid;
        gap: 16px;
        align-items: center;
        grid-template-columns: 54px 80px minmax(260px, 1.7fr) 120px 180px 110px 220px;
      }

      @media(max-width:1280px) {
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

      @media(max-width:1280px) {
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
        font-weight: 900;
      }

      .oc-main {
        display: flex;
        flex-direction: column;
        min-width: 0;
      }

      .oc-ttl {
        font-weight: 800;
        font-size: 15px;
        margin-bottom: 4px;
        color: #111827;
      }

      .oc-subt {
        font-size: 13px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .oc-status-pill,
      .oc-tag {
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

      .oc-status-pill.green,
      .oc-tag.ok {
        background: #ecfdf5;
        color: #047857;
      }

      .oc-status-pill.orange,
      .oc-tag.warn {
        background: #fffbeb;
        color: #b45309;
      }

      .oc-status-pill.red,
      .oc-tag.bad {
        background: #fef2f2;
        color: #b91c1c;
      }

      .oc-time-box {
        display: flex;
        flex-direction: column;
        gap: 4px;
      }

      .oc-overdue {
        font-weight: 900;
        color: #b45309;
        font-size: 13px;
      }

      .oc-overdue.high {
        color: #b91c1c;
      }



      .oc-actions {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: nowrap;
      }

      .oc-report-action {
        min-height: 36px;
        padding: 9px 13px;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1;
        white-space: nowrap;
      }

      .oc-action-menu-wrap {
        position: relative;
        display: inline-flex;
      }

      .oc-action-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 220px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        padding: 8px;
        z-index: 80;
        display: none;
      }

      .oc-action-menu.open {
        display: block;
        animation: ocMenuIn .15s ease-out both;
      }

      @keyframes ocMenuIn {
        from {
          opacity: 0;
          transform: translateY(-4px) scale(.98)
        }

        to {
          opacity: 1;
          transform: translateY(0) scale(1)
        }
      }

      .oc-menu-item {
        width: 100%;
        border: 0;
        background: transparent;
        color: #111827;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 11px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 850;
        text-align: left;
        transition: var(--transition);
      }

      .oc-menu-item:hover {
        background: #f9fafb;
        color: #111827;
        text-decoration: none;
      }

      .oc-menu-item.danger {
        color: #b91c1c;
      }

      .oc-menu-item.danger:hover {
        background: var(--danger-light);
      }

      .oc-menu-sep {
        height: 1px;
        background: var(--border);
        margin: 6px 4px;
      }

      .oc-menu-ico {
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }

      @media(max-width:1280px) {
        .oc-actions {
          justify-content: flex-start;
        }

        .oc-action-menu {
          right: auto;
          left: 0;
        }
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
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
      }

      .oc-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1200;
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
        max-width: 720px;
        background: #fff;
        border: 1px solid rgba(229, 231, 235, .9);
        border-radius: 16px;
        box-shadow: var(--shadow);
        transform: translateY(12px) scale(.985);
        transition: transform .22s ease;
        overflow: hidden;
      }

      .oc-modal.oc-modal-lg {
        max-width: 860px;
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
        font-weight: 900;
        font-size: 16px;
        line-height: 1.2;
        margin: 0;
        color: #111827;
      }

      .oc-modal-sub {
        margin: 4px 0 0 0;
        font-size: 13px;
        color: var(--text-muted);
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

      .oc-textarea {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: #fff;
        font-size: 14px;
        outline: none;
        resize: vertical;
      }

      .oc-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
      }

      .oc-history-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .oc-history-item {
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
      }

      .oc-history-date {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-muted);
        margin-bottom: 6px;
      }

      .oc-history-text {
        font-size: 14px;
        color: #111827;
        line-height: 1.5;
        white-space: pre-wrap;
      }

      .oc-error {
        margin-top: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        background: var(--danger-light);
        color: #b91c1c;
        font-size: 13px;
        font-weight: 800;
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

      .oc-toast-ic.warn {
        background: var(--warning-light);
        color: #d97706
      }

      .oc-toast-ttl {
        font-weight: 900;
        font-size: 13px;
        margin: 0;
        color: #111827;
      }

      .oc-toast-msg {
        font-size: 12px;
        color: #374151;
        margin: 4px 0 0 0;
        line-height: 1.4;
      }

      .oc-toast-x {
        margin-left: auto;
        background: transparent;
        border: none;
        cursor: pointer;
        color: var(--text-muted);
      }

      .oc-bulk-bar {
        position: fixed;
        left: 50%;
        bottom: 20px;
        transform: translateX(-50%) translateY(120%);
        z-index: 1100;
        background: #111827;
        color: #fff;
        padding: 12px 14px;
        border-radius: 16px;
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform .22s ease;
      }

      .oc-bulk-bar.open {
        transform: translateX(-50%) translateY(0);
      }

      .oc-bulk-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
      }

      .oc-bulk-bar .oc-btn-soft {
        color: #111827;
      }

      .hidden {
        display: none !important;
      }
    </style>
  @endpush
@endonce

@section('content')
  <div class="oc-wrap" id="oc-overdue">
    <div class="oc-toast-wrap" id="oc-toast-wrap"></div>

    <div class="oc-header">
      <div class="oc-titlebar">
        <div>
          <div class="oc-title">{{ $pageTitle }}</div>
          <div class="oc-sub">
            Überfällige Berichte für Anfragen, Aufgaben, Termine, Tickets und Leads zentral verwalten.
          </div>

          <div class="oc-breadcrumb">
            <a href="{{ url('/employee_dashboard') }}">Home</a>
            <span>›</span>
            <span class="current">{{ $pageTitle }}</span>
          </div>
        </div>

        <div class="oc-inline-actions">
          <button type="button" class="oc-btn-soft" id="oc-refresh">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 12a9 9 0 1 1-3-6.7" />
              <path d="M21 3v6h-6" />
            </svg>
            Aktualisieren
          </button>
        </div>
      </div>
    </div>

    <div class="oc-analytics">
      <div class="oc-stat">
        <div class="oc-stat-icon total">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 12h18M3 6h18M3 18h18" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Gesamt</div>
          <div class="oc-stat-value" id="oc-stat-total">0</div>
          <div class="oc-stat-sub">Gefundene Einträge</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon today">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M8 7V3m8 4V3" />
            <path d="M5 11h14" />
            <path d="M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Modus</div>
          <div class="oc-stat-value" id="oc-stat-mode">Heute</div>
          <div class="oc-stat-sub">Aktueller Zeitraum</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon delay">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 8v4l3 3" />
            <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Mitarbeiter</div>
          <div class="oc-stat-value" style="font-size:17px;">{{ $employeeName }}</div>
          <div class="oc-stat-sub">Aktueller Benutzer</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon custom">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 7h16M7 12h10M10 17h4" />
          </svg>
        </div>
        <div class="oc-stat-meta">
          <div class="oc-stat-label">Typen</div>
          <div class="oc-stat-value" id="oc-stat-types">3</div>
          <div class="oc-stat-sub">Aktive Kategorien</div>
        </div>
      </div>
    </div>

    <div class="oc-toolbar">
      <div class="oc-toolbar-left">
        <div class="oc-filter-block">
          <label class="oc-filter-label">Zeitraum</label>
          <div class="oc-due-tabs">
            <button type="button" class="oc-due-tab active" data-due-mode="today">Heute</button>
            <button type="button" class="oc-due-tab" data-due-mode="48h">48 Std.</button>
            <button type="button" class="oc-due-tab" data-due-mode="custom">Eigenes Datum</button>
          </div>
        </div>

        <div class="oc-filter-block hidden" id="oc-custom-date-wrap">
          <label class="oc-filter-label">Bis Datum/Uhrzeit</label>
          <input type="datetime-local" id="oc-custom-due-at" class="oc-input">
        </div>

        <div class="oc-filter-block search">
          <label class="oc-filter-label">Suche</label>
          <input type="text" id="oc-search" class="oc-input search-input"
            placeholder="Suche nach Titel, Kunde, Status oder Text">
        </div>
      </div>

      <div class="oc-toolbar-right">
        <div class="oc-filter-block">
          <label class="oc-filter-label">Sortierung</label>
          <select id="oc-sort" class="oc-input">
            <option value="oldest">Älteste zuerst</option>
            <option value="newest">Neueste zuerst</option>
            <option value="most_overdue">Am meisten überfällig</option>
          </select>
        </div>

        <div class="oc-filter-block">
          <label class="oc-filter-label">Pro Seite</label>
          <select id="oc-perpage" class="oc-input">
            <option value="12" selected>12</option>
            <option value="24">24</option>
            <option value="48">48</option>
          </select>
        </div>
      </div>
    </div>

    <div class="oc-toolbar">
      <div class="oc-toolbar-left">
        <div class="oc-filter-block" style="width:100%;">
          <label class="oc-filter-label">Kategorien</label>
          <div class="oc-type-list">
            <label><input type="checkbox" class="oc-type" value="inquiry"> Anfragen</label>
            <label><input type="checkbox" class="oc-type" value="task" checked> Aufgaben</label>
            <label><input type="checkbox" class="oc-type" value="appointment" checked> Termine</label>
            <label><input type="checkbox" class="oc-type" value="ticket" checked> Tickets</label>
            <label><input type="checkbox" class="oc-type" value="lead"> Leads</label>
          </div>
        </div>
      </div>
    </div>

    <div class="oc-card">
      <div class="oc-list-head">
        <div>
          <input type="checkbox" id="oc-select-all">
        </div>
        <div>ID</div>
        <div>Eintrag</div>
        <div>Typ</div>
        <div>Fällig</div>
        <div>Überfällig</div>
        <div style="text-align:right;">Aktionen</div>
      </div>

      <div id="oc-list" class="oc-list">
        <div class="oc-empty">Lädt...</div>
      </div>
    </div>

    <div class="oc-pagination">
      <button type="button" id="oc-prev" class="oc-btn-soft">Zurück</button>
      <div style="font-size:13px;color:#6b7280;font-weight:800;" id="oc-page">Seite 1</div>
      <button type="button" id="oc-next" class="oc-btn-soft">Weiter</button>
    </div>

    <div id="oc-bulk-bar" class="oc-bulk-bar">
      <strong id="oc-bulk-count">0 ausgewählt</strong>

      <div class="oc-bulk-actions">
        <button type="button" id="oc-bulk-reminder-btn" class="oc-btn-soft">Reminder</button>
        <button type="button" id="oc-bulk-report-btn" class="oc-btn">Sammelbericht</button>
        <button type="button" id="oc-bulk-cancel" class="oc-btn-soft">Abbrechen</button>
      </div>
    </div>
  </div>

  {{-- Reports / History Modal --}}
  <div class="oc-modal-backdrop" id="oc-backdrop">
    <div class="oc-modal oc-modal-lg">
      <div class="oc-modal-h">
        <div>
          <h3 class="oc-modal-ttl" id="oc-dtitle">Details</h3>
          <p class="oc-modal-sub" id="oc-dsub"></p>
        </div>

        <button class="oc-btn-ic" type="button" id="oc-dclose">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="oc-modal-b">
        <div id="oc-dbody"></div>

        <div id="oc-reportbox" class="hidden" style="margin-top:16px;">
          <textarea id="oc-report" class="oc-textarea" rows="5" placeholder="Bericht schreiben..."></textarea>
        </div>
      </div>

      <div class="oc-modal-f">
        <button type="button" class="oc-btn-soft" id="oc-dcancel">Schließen</button>
        <button type="button" class="oc-btn hidden" id="oc-save">Speichern</button>
      </div>
    </div>
  </div>

  {{-- Skip Modal --}}
  <div class="oc-modal-backdrop" id="oc-skip-modal">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <div>
          <h3 class="oc-modal-ttl">Bericht überspringen</h3>
          <p class="oc-modal-sub" id="oc-skip-sub"></p>
        </div>

        <button class="oc-btn-ic" type="button" id="oc-skip-close">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="oc-modal-b">
        <div id="oc-skip-meta" style="font-size:13px;color:#374151;margin-bottom:12px;"></div>

        <div class="oc-filter-block" style="margin-bottom:12px;">
          <label class="oc-filter-label">Vorlage</label>
          <select id="oc-skip-template" class="oc-input">
            <option value="">Grund auswählen...</option>
            <option value="customer_not_reachable">Kunde nicht erreichbar</option>
            <option value="waiting_external">Warte auf Rückmeldung extern</option>
            <option value="internal_clarification">Interne Klärung läuft</option>
            <option value="rescheduled">Follow-up wurde verschoben</option>
            <option value="duplicate">Doppelt / bereits erledigt</option>
            <option value="other">Sonstiges</option>
          </select>
        </div>

        <textarea id="oc-skip-reason" class="oc-textarea" rows="4" placeholder="Grund eingeben..."></textarea>
        <div id="oc-skip-error" class="oc-error hidden"></div>
      </div>

      <div class="oc-modal-f">
        <button type="button" id="oc-skip-cancel" class="oc-btn-soft">Abbrechen</button>
        <button type="button" id="oc-skip-confirm" class="oc-btn" style="background:var(--danger);">Überspringen</button>
      </div>
    </div>
  </div>

  {{-- Title Modal --}}
  <div class="oc-modal-backdrop" id="oc-title-modal">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <div>
          <h3 class="oc-modal-ttl" id="oc-title-modal-title">Details</h3>
          <p class="oc-modal-sub" id="oc-title-modal-sub"></p>
        </div>

        <button class="oc-btn-ic" type="button" id="oc-title-modal-close">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="oc-modal-b">
        <div id="oc-title-modal-body" style="white-space:pre-wrap;line-height:1.6;color:#111827;"></div>
      </div>

      <div class="oc-modal-f">
        <button type="button" id="oc-title-modal-ok" class="oc-btn">OK</button>
      </div>
    </div>
  </div>

  {{-- Bulk Report Modal --}}
  <div class="oc-modal-backdrop" id="oc-bulk-backdrop">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <div>
          <h3 class="oc-modal-ttl">Sammelbericht</h3>
          <p class="oc-modal-sub" id="oc-bulk-list-preview"></p>
        </div>

        <button class="oc-btn-ic" type="button" id="oc-bulk-dclose">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="oc-modal-b">
        <textarea id="oc-bulk-report-text" class="oc-textarea" rows="7"
          placeholder="Sammelbericht schreiben..."></textarea>
      </div>

      <div class="oc-modal-f">
        <button type="button" id="oc-bulk-save" class="oc-btn">Speichern</button>
      </div>
    </div>
  </div>

  {{-- Reminder Modal --}}
  <div class="oc-modal-backdrop" id="oc-reminder-modal">
    <div class="oc-modal">
      <div class="oc-modal-h">
        <div>
          <h3 class="oc-modal-ttl">Reminder setzen</h3>
          <p class="oc-modal-sub" id="oc-reminder-sub"></p>
        </div>

        <button class="oc-btn-ic" type="button" id="oc-reminder-close">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="oc-modal-b">
        <div class="oc-filter-block" style="margin-bottom:12px;">
          <label class="oc-filter-label">Zeitpunkt</label>
          <select id="oc-reminder-preset" class="oc-input">
            <option value="30">In 30 Minuten</option>
            <option value="60">In 1 Stunde</option>
            <option value="120" selected>In 2 Stunden</option>
            <option value="1440">Morgen</option>
            <option value="tomorrow_09">Morgen 09:00</option>
            <option value="next_week_09">Nächste Woche 09:00</option>
            <option value="custom">Eigenes Datum</option>
          </select>
        </div>

        <div class="oc-filter-block hidden" id="oc-reminder-custom-wrap" style="margin-bottom:12px;">
          <label class="oc-filter-label">Datum/Uhrzeit</label>
          <input type="datetime-local" id="oc-reminder-dt" class="oc-input">
        </div>

        <textarea id="oc-reminder-note" class="oc-textarea" rows="3" placeholder="Notiz optional..."></textarea>
        <div id="oc-reminder-error" class="oc-error hidden"></div>
      </div>

      <div class="oc-modal-f">
        <button type="button" id="oc-reminder-cancel" class="oc-btn-soft">Abbrechen</button>
        <button type="button" id="oc-reminder-save" class="oc-btn">Speichern</button>
      </div>
    </div>
  </div>
@endsection

@once
  @push('scripts')
    <script>
      window.initOverdueCenterWidget = function () {
        "use strict";

        const ROOT = document.getElementById("oc-overdue");
        if (!ROOT) return;

        if (ROOT.dataset.initialized === "true") return;
        ROOT.dataset.initialized = "true";

        const ENDPOINTS = {
          fetch: @json(route("admin.overdue.fetch")),
          history: @json(route("admin.overdue.history")),
          reports: @json(route("admin.overdue.reports.list")),
          store: @json(route("admin.overdue.reports.store")),
          skip: @json(route("admin.overdue.skip")),
          bulk: @json(route("admin.overdue.reports.bulk")),
          reminderUpsert: @json(route("admin.overdue.reminders.upsert")),
          reminderBulk: @json(route("admin.overdue.reminders.bulk")),
        };

        const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

        const esc = (s) => String(s ?? "")
          .replaceAll("&", "&amp;")
          .replaceAll("<", "&lt;")
          .replaceAll(">", "&gt;")
          .replaceAll('"', "&quot;")
          .replaceAll("'", "&#039;");

        const debounce = (fn, ms = 300) => {
          let t;
          return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), ms);
          };
        };

        const DOM = {
          list: $("#oc-list"),
          refresh: $("#oc-refresh"),
          prev: $("#oc-prev"),
          next: $("#oc-next"),
          page: $("#oc-page"),
          search: $("#oc-search"),
          sort: $("#oc-sort"),
          perpage: $("#oc-perpage"),
          typeChecks: $$(".oc-type"),
          dueTabs: $$(".oc-due-tab"),
          customDateWrap: $("#oc-custom-date-wrap"),
          customDueAt: $("#oc-custom-due-at"),

          statTotal: $("#oc-stat-total"),
          statMode: $("#oc-stat-mode"),
          statTypes: $("#oc-stat-types"),

          toastWrap: $("#oc-toast-wrap"),

          backdrop: $("#oc-backdrop"),
          dClose: $("#oc-dclose"),
          dCancel: $("#oc-dcancel"),
          dTitle: $("#oc-dtitle"),
          dSub: $("#oc-dsub"),
          dBody: $("#oc-dbody"),
          reportBox: $("#oc-reportbox"),
          reportText: $("#oc-report"),
          save: $("#oc-save"),

          skipBD: $("#oc-skip-modal"),
          skipOK: $("#oc-skip-confirm"),
          skipNO: $("#oc-skip-cancel"),
          skipX: $("#oc-skip-close"),
          skipErr: $("#oc-skip-error"),
          skipTpl: $("#oc-skip-template"),
          skipTxt: $("#oc-skip-reason"),
          skipSub: $("#oc-skip-sub"),
          skipMeta: $("#oc-skip-meta"),

          titleBD: $("#oc-title-modal"),
          titleX: $("#oc-title-modal-close"),
          titleOK: $("#oc-title-modal-ok"),
          titleTTL: $("#oc-title-modal-title"),
          titleSub: $("#oc-title-modal-sub"),
          titleBody: $("#oc-title-modal-body"),

          selectAll: $("#oc-select-all"),
          bulkBar: $("#oc-bulk-bar"),
          bulkCount: $("#oc-bulk-count"),
          bulkBtn: $("#oc-bulk-report-btn"),
          bulkCancel: $("#oc-bulk-cancel"),
          bulkBackdrop: $("#oc-bulk-backdrop"),
          bulkDClose: $("#oc-bulk-dclose"),
          bulkSave: $("#oc-bulk-save"),
          bulkText: $("#oc-bulk-report-text"),
          bulkListPreview: $("#oc-bulk-list-preview"),
          bulkReminderBtn: $("#oc-bulk-reminder-btn"),

          reminderBD: $("#oc-reminder-modal"),
          reminderX: $("#oc-reminder-close"),
          reminderCancel: $("#oc-reminder-cancel"),
          reminderSave: $("#oc-reminder-save"),
          reminderSub: $("#oc-reminder-sub"),
          reminderPreset: $("#oc-reminder-preset"),
          reminderCustomWrap: $("#oc-reminder-custom-wrap"),
          reminderDT: $("#oc-reminder-dt"),
          reminderNote: $("#oc-reminder-note"),
          reminderErr: $("#oc-reminder-error"),
        };

        const DEFAULT_TYPES = ["task", "appointment", "ticket"];

        const state = {
          page: 1,
          per_page: parseInt(DOM.perpage?.value || "12", 10) || 12,
          q: "",
          sort: DOM.sort?.value || "oldest",
          types: DOM.typeChecks.filter(x => x.checked).map(x => x.value),
          due_mode: "today",
          custom_due_at: "",
          last: [],
          active: null,
          skipTarget: null,
          reminderTarget: null,
        };

        if (!state.types.length) {
          state.types = DEFAULT_TYPES.slice();
        }

        function toast(kind, title, msg) {
          if (!DOM.toastWrap) return;

          const icons = {
            ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M20 6L9 17l-5-5"/></svg>`,
            bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M6 18L18 6M6 6l12 12"/></svg>`,
            warn: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M12 9v4m0 4h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>`
          };

          const el = document.createElement("div");
          el.className = "oc-toast";
          el.innerHTML = `
              <div class="oc-toast-ic ${esc(kind)}">${icons[kind] || icons.ok}</div>
              <div style="flex:1;">
                <p class="oc-toast-ttl">${esc(title)}</p>
                <p class="oc-toast-msg">${esc(msg)}</p>
              </div>
              <button class="oc-toast-x" type="button">×</button>
            `;

          DOM.toastWrap.appendChild(el);
          el.querySelector(".oc-toast-x")?.addEventListener("click", () => el.remove());
          setTimeout(() => { try { el.remove(); } catch (e) { } }, 4000);
        }

        function parseLaravel422(rawText) {
          try {
            const j = JSON.parse(rawText || "{}");
            const msg = j.message || "Validierung fehlgeschlagen.";

            if (j.errors && typeof j.errors === "object") {
              const lines = Object.values(j.errors).flat();
              return { message: msg, details: lines.join("\n") };
            }

            return { message: msg, details: "" };
          } catch (_) {
            return { message: "Fehler", details: String(rawText || "").slice(0, 500) };
          }
        }

        async function api(url, data = {}, method = "GET") {
          const headers = {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          };

          const opts = { method, headers };
          let finalUrl = url;

          if (method === "GET") {
            const payload = { ...data };

            if (Array.isArray(payload.types)) {
              payload.types = payload.types.join(",");
            }

            const qs = new URLSearchParams(payload).toString();

            if (qs) {
              finalUrl += (finalUrl.includes("?") ? "&" : "?") + qs;
            }
          } else {
            headers["Content-Type"] = "application/json";

            if (CSRF) {
              headers["X-CSRF-TOKEN"] = CSRF;
            }

            opts.body = JSON.stringify(data);
          }

          const res = await fetch(finalUrl, opts);
          const txt = await res.text().catch(() => "");

          if (!res.ok) {
            const err = new Error(`HTTP ${res.status}`);
            err.status = res.status;
            err.raw = txt;
            throw err;
          }

          try {
            return JSON.parse(txt || "{}");
          } catch (_) {
            return {};
          }
        }

        const typeLabel = (t) => ({
          inquiry: "Anfrage",
          task: "Aufgabe",
          appointment: "Termin",
          ticket: "Ticket",
          lead: "Lead",
        }[t] || t);

        const modeLabel = (mode) => ({
          today: "Heute",
          "48h": "48 Std.",
          custom: "Eigenes Datum",
        }[mode] || "Heute");

        function formatDateTime(s) {
          if (!s) return "—";

          try {
            const d = new Date(String(s).replace(" ", "T"));
            return d.toLocaleString("de-DE");
          } catch (_) {
            return String(s);
          }
        }

        function formatOverdue(h) {
          const hours = Math.max(0, Math.floor(Number(h || 0)));
          const d = Math.floor(hours / 24);
          const r = hours % 24;

          return d > 0 ? `${d}T ${r}Std.` : `${hours} Std.`;
        }

        function statusTone(v) {
          const k = String(v ?? "").toLowerCase().trim();

          if (["done", "completed", "closed", "verified", "approved", "success", "won", "published", "active"].includes(k)) {
            return "green";
          }

          if (["pending", "in_progress", "progress", "working", "running", "paused", "on_hold"].includes(k)) {
            return "orange";
          }

          if (["rejected", "declined", "failed", "lost", "cancel", "canceled"].includes(k)) {
            return "red";
          }

          return "orange";
        }

        function renderStats(stats) {
          if (DOM.statTotal) DOM.statTotal.textContent = Number(stats?.total ?? 0);
          if (DOM.statMode) DOM.statMode.textContent = modeLabel(state.due_mode);
          if (DOM.statTypes) DOM.statTypes.textContent = state.types.length;
        }

        function setPager(p) {
          const page = Number(p?.page ?? state.page);
          const per = Number(p?.per_page ?? state.per_page);
          const total = Number(p?.total ?? 0);
          const pages = Math.max(1, Math.ceil(total / Math.max(1, per)));

          if (DOM.page) {
            DOM.page.textContent = `Seite ${page} von ${pages} · ${total} Einträge`;
          }

          if (DOM.prev) {
            DOM.prev.disabled = page <= 1;
          }

          if (DOM.next) {
            DOM.next.disabled = !p?.has_more;
          }
        }

        function renderReports(rows) {
          if (!rows?.length) {
            return `<div class="oc-empty" style="margin:0;">Keine Berichte vorhanden.</div>`;
          }

          return `
              <div class="oc-history-list">
                ${rows.map(r => `
                  <div class="oc-history-item">
                    <div class="oc-history-date">${esc(formatDateTime(r.created_at || "—"))}</div>
                    <div style="font-weight:900;font-size:14px;margin-bottom:6px;color:#111827">
                      ${esc(r.report_by_name || r.employee_name || "—")}
                    </div>
                    <div class="oc-history-text">${esc(r.report || r.report_text || "")}</div>
                  </div>
                `).join("")}
              </div>
            `;
        }

        function renderHistory(type, res) {
          const h = res?.history || {};
          let rows = [];

          if (Array.isArray(h.changes)) {
            rows = rows.concat(h.changes.map(x => ({
              date: x.created_at || x.updated_at,
              title: x.status || x.change_reason || "Änderung",
              text: x.note || x.reason || x.change_reason || "",
            })));
          }

          if (Array.isArray(h.keys)) {
            rows = rows.concat(h.keys.map(x => ({
              date: x.created_at || x.done_date,
              title: x.task || x.status || "Aufgabenschritt",
              text: x.reason || `Status: ${x.status || "—"}`,
            })));
          }

          if (!rows.length && h.appointment) {
            rows.push({
              date: h.appointment.updated_at,
              title: h.appointment.status || "Termin",
              text: h.appointment.report || h.appointment.change_reason || "",
            });
          }

          if (!rows.length && h.inquiry) {
            rows.push({
              date: h.inquiry.updated_at,
              title: h.inquiry.status || "Anfrage",
              text: h.inquiry.note || h.inquiry.next_step || "",
            });
          }

          if (!rows.length && h.problem) {
            rows.push({
              date: h.problem.updated_at,
              title: h.problem.status || "Ticket",
              text: h.problem.problem || h.problem.progress || h.problem.solution || "",
            });
          }

          if (!rows.length) {
            return `<div class="oc-empty" style="margin:0;">Kein Verlauf vorhanden.</div>`;
          }

          return `
              <div class="oc-history-list">
                ${rows.map(r => `
                  <div class="oc-history-item">
                    <div class="oc-history-date">${esc(formatDateTime(r.date || "—"))}</div>
                    <div style="font-weight:900;font-size:14px;margin-bottom:6px;color:#111827">${esc(r.title || "Änderung")}</div>
                    <div class="oc-history-text">${esc(r.text || "")}</div>
                  </div>
                `).join("")}
              </div>
            `;
        }

        function updateBulkBar() {
          const selected = $$(".oc-item-cb:checked", ROOT);
          const count = selected.length;

          if (DOM.bulkCount) {
            DOM.bulkCount.textContent = `${count} ausgewählt`;
          }

          if (DOM.bulkBar) {
            DOM.bulkBar.classList.toggle("open", count > 0);
          }
        }

        function clearBulkSelection() {
          if (DOM.selectAll) DOM.selectAll.checked = false;

          $$(".oc-item-cb", ROOT).forEach(cb => {
            cb.checked = false;
            cb.closest(".oc-item")?.classList.remove("selected");
          });

          updateBulkBar();
        }

        function renderList(items) {
          if (!DOM.list) return;

          if (!items?.length) {
            DOM.list.innerHTML = `<div class="oc-empty">Keine überfälligen Einträge gefunden.</div>`;
            return;
          }

          DOM.list.innerHTML = items.map(item => {
            const type = String(item.type || "");
            const id = Number(item.id || 0);
            const title = item.title || item.task_title || item.name || item.subject || "—";
            const subtitle = item.subtitle || item.description || "";
            const status = item.status || "open";
            const statusClass = statusTone(status);
            const overdue = Number(item.overdue_hours || 0);
            const overdueClass = overdue >= 72 ? "high" : "";
            const link = item.link || "#";
            const safeType = esc(type);
            const safeTitle = esc(title);

            return `
                <div class="oc-item" data-id="${id}" data-type="${safeType}">
                  <div class="oc-item-row">
                    <div class="oc-cell">
                      <div class="oc-cell-title">Auswahl</div>
                      <input
                        type="checkbox"
                        class="oc-item-cb"
                        data-id="${id}"
                        data-type="${safeType}"
                        data-title="${safeTitle}"
                      >
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">ID</div>
                      <span class="oc-id-badge">#${id}</span>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Eintrag</div>
                      <div class="oc-main">
                        <div class="oc-ttl">${safeTitle}</div>
                        <div class="oc-subt">${esc(subtitle || "—")}</div>
                      </div>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Typ</div>
                      <span class="oc-status-pill ${esc(statusClass)}">${esc(typeLabel(type))}</span>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Fällig</div>
                      <div class="oc-time-box">
                        <strong style="font-size:13px;">${esc(formatDateTime(item.due_at))}</strong>
                        <span style="font-size:12px;color:var(--text-muted);">Zuletzt: ${esc(formatDateTime(item.last_activity_at))}</span>
                      </div>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Überfällig</div>
                      <div class="oc-overdue ${overdueClass}">${esc(formatOverdue(overdue))}</div>
                    </div>

                    <div class="oc-cell">
                      <div class="oc-cell-title">Aktionen</div>
                      <div class="oc-actions">
                        <button type="button" class="oc-btn oc-report-action"
                          data-oc-action="reports"
                          data-oc-type="${safeType}"
                          data-oc-id="${id}"
                          data-oc-title="${safeTitle}">
                          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                          </svg>
                          Bericht schreiben
                        </button>

                        <div class="oc-action-menu-wrap">
                          <button type="button" class="oc-btn-ic oc-menu-toggle" title="Weitere Aktionen" aria-label="Weitere Aktionen">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4">
                              <circle cx="12" cy="5" r="1"/>
                              <circle cx="12" cy="12" r="1"/>
                              <circle cx="12" cy="19" r="1"/>
                            </svg>
                          </button>

                          <div class="oc-action-menu">
                            <a href="${esc(link)}" target="_blank" rel="noopener" class="oc-menu-item">
                              <span class="oc-menu-ico">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/>
                                  <path d="M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                              </span>
                              Datensatz öffnen
                            </a>

                            <button type="button" class="oc-menu-item"
                              data-oc-action="reports-view"
                              data-oc-type="${safeType}"
                              data-oc-id="${id}"
                              data-oc-title="${safeTitle}">
                              <span class="oc-menu-ico">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M9 12h6m-6 4h6"/>
                                  <path d="M7 3h6l4 4v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                </svg>
                              </span>
                              Berichte anzeigen
                            </button>

                            <button type="button" class="oc-menu-item"
                              data-oc-action="history"
                              data-oc-type="${safeType}"
                              data-oc-id="${id}"
                              data-oc-title="${safeTitle}">
                              <span class="oc-menu-ico">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M3 3v5h5"/>
                                  <path d="M3.2 12a9 9 0 1 0 2.6-6.4"/>
                                  <path d="M12 7v5l3 3"/>
                                </svg>
                              </span>
                              Verlauf anzeigen
                            </button>

                            <button type="button" class="oc-menu-item"
                              data-oc-action="remind"
                              data-oc-type="${safeType}"
                              data-oc-id="${id}"
                              data-oc-title="${safeTitle}">
                              <span class="oc-menu-ico">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"/>
                                  <path d="M9 17a3 3 0 0 0 6 0"/>
                                </svg>
                              </span>
                              Reminder setzen
                            </button>

                            <div class="oc-menu-sep"></div>

                            <button type="button" class="oc-menu-item danger"
                              data-oc-action="skip"
                              data-oc-type="${safeType}"
                              data-oc-id="${id}"
                              data-oc-title="${safeTitle}">
                              <span class="oc-menu-ico">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M5 12h12"/>
                                  <path d="M13 6l6 6-6 6"/>
                                </svg>
                              </span>
                              Bericht überspringen
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              `;
          }).join("");

          updateBulkBar();
        }

        function closeAllActionMenus(exceptMenu = null) {
          $$(".oc-action-menu.open", ROOT).forEach(menu => {
            if (exceptMenu && menu === exceptMenu) return;
            menu.classList.remove("open");
          });
        }

        async function load() {
          if (DOM.list) {
            DOM.list.innerHTML = `<div class="oc-empty">Lädt...</div>`;
          }

          if (DOM.selectAll) {
            DOM.selectAll.checked = false;
          }

          updateBulkBar();

          try {
            const data = await api(ENDPOINTS.fetch, {
              q: state.q,
              sort: state.sort,
              page: state.page,
              per_page: state.per_page,
              types: state.types?.length ? state.types : DEFAULT_TYPES,
              due_mode: state.due_mode,
              custom_due_at: state.custom_due_at,
            }, "GET");

            state.last = Array.isArray(data?.items) ? data.items : [];

            renderStats(data?.stats || {});
            renderList(state.last);
            setPager(data?.pagination || {});
          } catch (e) {
            if (DOM.list) {
              DOM.list.innerHTML = `<div class="oc-empty" style="color:var(--danger);">Fehler beim Laden.</div>`;
            }

            toast("bad", "Fehler", "Daten konnten nicht geladen werden.");
          }
        }

        function openBackdrop(el) {
          el?.classList.add("open");
        }

        function closeBackdrop(el) {
          el?.classList.remove("open");
        }

        DOM.refresh?.addEventListener("click", () => load());

        DOM.prev?.addEventListener("click", () => {
          state.page = Math.max(1, state.page - 1);
          load();
        });

        DOM.next?.addEventListener("click", () => {
          state.page += 1;
          load();
        });

        DOM.search?.addEventListener("input", debounce((e) => {
          state.q = e.target.value || "";
          state.page = 1;
          load();
        }, 300));

        DOM.sort?.addEventListener("change", (e) => {
          state.sort = e.target.value || "oldest";
          state.page = 1;
          load();
        });

        DOM.perpage?.addEventListener("change", (e) => {
          state.per_page = parseInt(e.target.value, 10) || 12;
          state.page = 1;
          load();
        });

        DOM.typeChecks.forEach(cb => {
          cb.addEventListener("change", () => {
            state.types = DOM.typeChecks.filter(x => x.checked).map(x => x.value);
            state.page = 1;
            renderStats({ total: Number(DOM.statTotal?.textContent || 0) });
            load();
          });
        });

        DOM.dueTabs.forEach(tab => {
          tab.addEventListener("click", () => {
            const mode = tab.dataset.dueMode || "today";

            state.due_mode = mode;
            state.page = 1;

            DOM.dueTabs.forEach(x => x.classList.remove("active"));
            tab.classList.add("active");

            DOM.customDateWrap?.classList.toggle("hidden", mode !== "custom");

            if (mode !== "custom") {
              state.custom_due_at = "";
              load();
              return;
            }

            if (DOM.customDueAt?.value) {
              state.custom_due_at = DOM.customDueAt.value;
              load();
            } else {
              renderStats({ total: Number(DOM.statTotal?.textContent || 0) });
            }
          });
        });

        DOM.customDueAt?.addEventListener("change", e => {
          state.custom_due_at = e.target.value || "";
          state.page = 1;

          if (state.due_mode === "custom" && state.custom_due_at) {
            load();
          }
        });

        DOM.selectAll?.addEventListener("change", (e) => {
          const on = !!e.target.checked;

          $$(".oc-item-cb", ROOT).forEach(cb => {
            cb.checked = on;
            cb.closest(".oc-item")?.classList.toggle("selected", on);
          });

          updateBulkBar();
        });

        DOM.list?.addEventListener("change", (e) => {
          if (!e.target.classList.contains("oc-item-cb")) return;

          const row = e.target.closest(".oc-item");
          row?.classList.toggle("selected", !!e.target.checked);

          if (!e.target.checked && DOM.selectAll) {
            DOM.selectAll.checked = false;
          }

          updateBulkBar();
        });

        DOM.list?.addEventListener("click", async (e) => {
          const menuToggle = e.target.closest(".oc-menu-toggle");

          if (menuToggle) {
            e.preventDefault();
            e.stopPropagation();

            const menu = menuToggle.closest(".oc-action-menu-wrap")?.querySelector(".oc-action-menu");
            const shouldOpen = !menu?.classList.contains("open");

            closeAllActionMenus(menu || null);

            if (menu && shouldOpen) {
              menu.classList.add("open");
            }

            return;
          }

          const btn = e.target.closest("[data-oc-action]");
          if (!btn) return;

          closeAllActionMenus();

          const action = btn.dataset.ocAction;
          const type = btn.dataset.ocType;
          const id = Number(btn.dataset.ocId || 0);
          const title = btn.dataset.ocTitle || "";

          if (!action || !type || !id) return;

          state.active = { type, id, title };

          if (action === "reports") {
            DOM.dTitle.textContent = "Berichte";
            DOM.dSub.textContent = `${typeLabel(type)} • ${title}`;
            DOM.reportBox.classList.remove("hidden");
            DOM.save.classList.remove("hidden");
            DOM.reportText.value = "";

            openBackdrop(DOM.backdrop);

            try {
              const res = await api(ENDPOINTS.reports, { type, id }, "GET");
              DOM.dBody.innerHTML = renderReports(res?.rows || []);
            } catch (_) {
              toast("bad", "Fehler", "Berichte konnten nicht geladen werden.");
            }

            return;
          }

          if (action === "reports-view") {
            DOM.dTitle.textContent = "Berichte";
            DOM.dSub.textContent = `${typeLabel(type)} • ${title}`;
            DOM.reportBox.classList.add("hidden");
            DOM.save.classList.add("hidden");
            DOM.reportText.value = "";

            openBackdrop(DOM.backdrop);

            try {
              const res = await api(ENDPOINTS.reports, { type, id }, "GET");
              DOM.dBody.innerHTML = renderReports(res?.rows || []);
            } catch (_) {
              toast("bad", "Fehler", "Berichte konnten nicht geladen werden.");
            }

            return;
          }

          if (action === "history") {
            DOM.dTitle.textContent = "Verlauf";
            DOM.dSub.textContent = `${typeLabel(type)} • ${title}`;
            DOM.reportBox.classList.add("hidden");
            DOM.save.classList.add("hidden");

            openBackdrop(DOM.backdrop);

            try {
              const res = await api(ENDPOINTS.history, { type, id }, "GET");
              DOM.dBody.innerHTML = renderHistory(type, res);
            } catch (_) {
              toast("bad", "Fehler", "Verlauf konnte nicht geladen werden.");
            }

            return;
          }

          if (action === "skip") {
            state.skipTarget = { type, id, title };

            DOM.skipSub.textContent = `${typeLabel(type)} • ${title}`;
            DOM.skipMeta.innerHTML = `
                <div><b>Typ:</b> ${esc(typeLabel(type))}</div>
                <div style="margin-top:6px"><b>Titel:</b> ${esc(title || "—")}</div>
              `;

            DOM.skipErr.classList.add("hidden");
            DOM.skipTxt.value = "";
            DOM.skipTpl.value = "";

            openBackdrop(DOM.skipBD);
            return;
          }

          if (action === "remind") {
            state.reminderTarget = {
              mode: "single",
              items: [{ type, id, title }],
            };

            DOM.reminderSub.textContent = `${typeLabel(type)} • ${title}`;
            DOM.reminderErr.classList.add("hidden");
            DOM.reminderNote.value = "";
            DOM.reminderPreset.value = "120";
            DOM.reminderCustomWrap.classList.add("hidden");
            DOM.reminderDT.value = "";

            openBackdrop(DOM.reminderBD);
          }
        });

        document.addEventListener("click", (e) => {
          if (!ROOT.contains(e.target)) return;
          if (e.target.closest(".oc-action-menu-wrap")) return;
          closeAllActionMenus();
        });

        DOM.dClose?.addEventListener("click", () => closeBackdrop(DOM.backdrop));
        DOM.dCancel?.addEventListener("click", () => closeBackdrop(DOM.backdrop));

        DOM.backdrop?.addEventListener("click", e => {
          if (e.target === DOM.backdrop) closeBackdrop(DOM.backdrop);
        });

        DOM.save?.addEventListener("click", async () => {
          const text = (DOM.reportText?.value || "").trim();

          if (text.length < 3) {
            toast("warn", "Zu kurz", "Bitte mehr Text eingeben.");
            return;
          }

          if (!state.active?.type || !state.active?.id) return;

          DOM.save.disabled = true;

          try {
            await api(ENDPOINTS.store, {
              type: state.active.type,
              id: state.active.id,
              report: text,
            }, "POST");

            toast("ok", "Gespeichert", "Bericht gespeichert.");
            DOM.reportText.value = "";

            const res = await api(ENDPOINTS.reports, {
              type: state.active.type,
              id: state.active.id,
            }, "GET");

            DOM.dBody.innerHTML = renderReports(res?.rows || []);

            await load();
          } catch (e) {
            if (e?.status === 422) {
              const info = parseLaravel422(e.raw);
              toast("bad", "Validierungsfehler", info.details || info.message);
            } else {
              toast("bad", "Fehler", "Konnte nicht speichern.");
            }
          } finally {
            DOM.save.disabled = false;
          }
        });

        DOM.skipX?.addEventListener("click", () => closeBackdrop(DOM.skipBD));
        DOM.skipNO?.addEventListener("click", () => closeBackdrop(DOM.skipBD));

        DOM.skipBD?.addEventListener("click", e => {
          if (e.target === DOM.skipBD) closeBackdrop(DOM.skipBD);
        });

        DOM.skipTpl?.addEventListener("change", () => {
          const map = {
            customer_not_reachable: "Kunde nicht erreichbar.",
            waiting_external: "Warte auf Rückmeldung extern.",
            internal_clarification: "Interne Klärung läuft.",
            rescheduled: "Follow-up wurde verschoben.",
            duplicate: "Doppelt / bereits erledigt.",
            other: "",
          };

          DOM.skipTxt.value = map[DOM.skipTpl.value] ?? DOM.skipTxt.value;
        });

        DOM.skipOK?.addEventListener("click", async () => {
          const reason = (DOM.skipTxt?.value || "").trim();

          if (reason.length < 3) {
            toast("warn", "Grund fehlt", "Bitte Grund eingeben.");
            return;
          }

          if (!state.skipTarget?.type || !state.skipTarget?.id) return;

          DOM.skipOK.disabled = true;

          try {
            await api(ENDPOINTS.skip, {
              type: state.skipTarget.type,
              id: state.skipTarget.id,
              skip_reason: reason,
            }, "POST");

            toast("ok", "Übersprungen", "Eintrag entfernt.");
            closeBackdrop(DOM.skipBD);
            await load();
          } catch (e) {
            if (e?.status === 422) {
              const info = parseLaravel422(e.raw);
              toast("bad", "Validierungsfehler", info.details || info.message);
            } else {
              toast("bad", "Fehler", "Konnte nicht überspringen.");
            }
          } finally {
            DOM.skipOK.disabled = false;
          }
        });

        DOM.titleX?.addEventListener("click", () => closeBackdrop(DOM.titleBD));
        DOM.titleOK?.addEventListener("click", () => closeBackdrop(DOM.titleBD));

        DOM.bulkCancel?.addEventListener("click", clearBulkSelection);

        DOM.bulkBtn?.addEventListener("click", () => {
          const selected = $$(".oc-item-cb:checked", ROOT);
          if (!selected.length) return;

          const names = selected.slice(0, 3).map(cb => cb.dataset.title || "—").join(", ");
          const remaining = selected.length - 3;

          DOM.bulkListPreview.textContent = `Betrifft: ${names}${remaining > 0 ? " und " + remaining + " weitere" : ""}`;
          openBackdrop(DOM.bulkBackdrop);
        });

        DOM.bulkDClose?.addEventListener("click", () => closeBackdrop(DOM.bulkBackdrop));

        DOM.bulkBackdrop?.addEventListener("click", e => {
          if (e.target === DOM.bulkBackdrop) closeBackdrop(DOM.bulkBackdrop);
        });

        DOM.bulkSave?.addEventListener("click", async () => {
          const text = (DOM.bulkText?.value || "").trim();

          if (text.length < 3) {
            toast("warn", "Hinweis", "Bericht ist zu kurz.");
            return;
          }

          const items = $$(".oc-item-cb:checked", ROOT)
            .map(cb => ({
              id: Number(cb.dataset.id),
              type: String(cb.dataset.type || ""),
            }))
            .filter(x => x.id > 0 && x.type);

          if (!items.length) return;

          DOM.bulkSave.disabled = true;

          try {
            await api(ENDPOINTS.bulk, { items, report: text }, "POST");

            toast("ok", "Erfolg", `${items.length} Berichte gespeichert.`);
            closeBackdrop(DOM.bulkBackdrop);

            DOM.bulkText.value = "";
            clearBulkSelection();

            await load();
          } catch (_) {
            toast("bad", "Fehler", "Sammelbericht konnte nicht gespeichert werden.");
          } finally {
            DOM.bulkSave.disabled = false;
          }
        });

        function presetToPayload(preset) {
          const now = new Date();

          if (/^\d+$/.test(preset)) {
            return {
              minutes: parseInt(preset, 10),
              next_remind_at: null,
            };
          }

          if (preset === "tomorrow_09") {
            const d = new Date(now);
            d.setDate(d.getDate() + 1);
            d.setHours(9, 0, 0, 0);

            return {
              minutes: null,
              next_remind_at: d.toISOString(),
            };
          }

          if (preset === "next_week_09") {
            const d = new Date(now);
            d.setDate(d.getDate() + 7);
            d.setHours(9, 0, 0, 0);

            return {
              minutes: null,
              next_remind_at: d.toISOString(),
            };
          }

          return {
            minutes: null,
            next_remind_at: null,
          };
        }

        DOM.reminderPreset?.addEventListener("change", e => {
          DOM.reminderCustomWrap?.classList.toggle("hidden", e.target.value !== "custom");
        });

        DOM.reminderX?.addEventListener("click", () => closeBackdrop(DOM.reminderBD));
        DOM.reminderCancel?.addEventListener("click", () => closeBackdrop(DOM.reminderBD));

        DOM.reminderBD?.addEventListener("click", e => {
          if (e.target === DOM.reminderBD) closeBackdrop(DOM.reminderBD);
        });

        DOM.bulkReminderBtn?.addEventListener("click", () => {
          const items = $$(".oc-item-cb:checked", ROOT)
            .map(cb => ({
              type: String(cb.dataset.type || ""),
              id: Number(cb.dataset.id || 0),
              title: String(cb.dataset.title || ""),
            }))
            .filter(x => x.type && x.id > 0);

          if (!items.length) return;

          state.reminderTarget = {
            mode: "bulk",
            items,
          };

          const names = items.slice(0, 3).map(x => x.title || "—").join(", ");
          const more = items.length - 3;

          DOM.reminderSub.textContent = `Bulk • ${names}${more > 0 ? " und " + more + " weitere" : ""}`;
          DOM.reminderErr.classList.add("hidden");
          DOM.reminderNote.value = "";
          DOM.reminderPreset.value = "120";
          DOM.reminderCustomWrap.classList.add("hidden");
          DOM.reminderDT.value = "";

          openBackdrop(DOM.reminderBD);
        });

        DOM.reminderSave?.addEventListener("click", async () => {
          const target = state.reminderTarget;

          if (!target?.items?.length) return;

          DOM.reminderErr.classList.add("hidden");

          const preset = DOM.reminderPreset?.value || "120";
          const note = (DOM.reminderNote?.value || "").trim();

          let payload = presetToPayload(preset);

          if (preset === "custom") {
            const dt = DOM.reminderDT?.value;

            if (!dt) {
              DOM.reminderErr.textContent = "Bitte Datum/Uhrzeit auswählen.";
              DOM.reminderErr.classList.remove("hidden");
              return;
            }

            payload = {
              minutes: null,
              next_remind_at: new Date(dt).toISOString(),
            };
          }

          DOM.reminderSave.disabled = true;

          try {
            if (target.mode === "single") {
              const it = target.items[0];

              await api(ENDPOINTS.reminderUpsert, {
                type: it.type,
                id: it.id,
                minutes: payload.minutes,
                next_remind_at: payload.next_remind_at,
                note,
              }, "POST");

              toast("ok", "Reminder gesetzt", "Erinnerung gespeichert.");
            } else {
              await api(ENDPOINTS.reminderBulk, {
                items: target.items.map(x => ({ type: x.type, id: x.id })),
                minutes: payload.minutes,
                next_remind_at: payload.next_remind_at,
                note,
              }, "POST");

              toast("ok", "Bulk Reminder", `${target.items.length} Erinnerungen gespeichert.`);
            }

            closeBackdrop(DOM.reminderBD);
            await load();
          } catch (e) {
            if (e?.status === 422) {
              const info = parseLaravel422(e.raw);
              DOM.reminderErr.textContent = info.details || info.message;
            } else {
              DOM.reminderErr.textContent = "Fehler beim Speichern.";
            }

            DOM.reminderErr.classList.remove("hidden");
          } finally {
            DOM.reminderSave.disabled = false;
          }
        });

        document.addEventListener("keydown", e => {
          if (e.key === "Escape") {
            document.querySelectorAll(".oc-modal-backdrop.open").forEach(el => el.classList.remove("open"));
          }
        });

        load();
      };

      document.addEventListener("DOMContentLoaded", () => {
        if (typeof window.initOverdueCenterWidget === "function") {
          window.initOverdueCenterWidget();
        }
      });
    </script>
  @endpush
@endonce


@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      },
      {
        label: 'Berichte-Center',
        url: "{{ url()->current() }}",
        clickable: false
      },
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush