@extends('admin.layouts.app')
@section('title', 'Feedback')

@php
    $currentEmployeeId = (int) auth()->user()->name;
@endphp

@once
    @push('style')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

        <style>
          :root {
            --fb-bg:#f3f4f6;
            --fb-card:#ffffff;
            --fb-text:#1f2937;
            --fb-muted:#6b7280;
            --fb-border:#e5e7eb;
            --fb-primary:#93c21c;
            --fb-primary-hover:#7baa18;
            --fb-primary-light:#f4fae7;
            --fb-blue:#74b2d4;
            --fb-blue-light:#eff6ff;
            --fb-success:#10b981;
            --fb-success-light:#ecfdf5;
            --fb-warning:#f59e0b;
            --fb-warning-light:#fffbeb;
            --fb-danger:#ef4444;
            --fb-danger-light:#fef2f2;
            --fb-shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
            --fb-shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --fb-radius:14px;
            --fb-transition:all .2s ease-in-out;
          }

          .fb-wrap {
            font-family: Inter, system-ui, -apple-system, sans-serif;
            color: var(--fb-text);
          }

          .fb-header {
            margin-bottom:18px;
          }

          .fb-titlebar {
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap:12px;
            margin-bottom:16px;
            flex-wrap:wrap;
          }

          .fb-title {
            font-size:26px;
            font-weight:900;
            letter-spacing:-.025em;
            color:#111827;
          }

          .fb-sub {
            font-size:14px;
            color:var(--fb-muted);
            margin-top:4px;
          }

          .fb-breadcrumb {
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:8px;
            margin-top:10px;
            font-size:13px;
            color:var(--fb-muted);
          }

          .fb-breadcrumb a {
            color:var(--fb-muted);
            text-decoration:none;
            font-weight:700;
          }

          .fb-breadcrumb a:hover {
            color:var(--fb-text);
          }

          .fb-breadcrumb .current {
            color:#111827;
            font-weight:800;
          }

          .fb-btn {
            background:var(--fb-primary);
            color:#fff;
            border:none;
            padding:10px 16px;
            border-radius:10px;
            font-weight:900;
            cursor:pointer;
            transition:var(--fb-transition);
            display:inline-flex;
            align-items:center;
            gap:8px;
            text-decoration:none;
          }

          .fb-btn:hover {
            background:var(--fb-primary-hover);
            color:#fff;
            text-decoration:none;
          }

          .fb-btn-soft {
            background:#fff;
            color:var(--fb-text);
            border:1px solid var(--fb-border);
            padding:10px 14px;
            border-radius:10px;
            font-weight:800;
            cursor:pointer;
            transition:var(--fb-transition);
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
          }

          .fb-btn-soft:hover {
            background:#f9fafb;
            color:var(--fb-text);
            text-decoration:none;
          }

          .fb-btn-ic {
            width:36px;
            height:36px;
            border-radius:8px;
            border:1px solid var(--fb-border);
            background:#fff;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:var(--fb-muted);
            cursor:pointer;
            transition:var(--fb-transition);
            text-decoration:none;
          }

          .fb-btn-ic:hover {
            background:#f9fafb;
            color:var(--fb-text);
            border-color:#d1d5db;
            text-decoration:none;
          }

          .fb-btn-ic.primary {
            color:var(--fb-primary);
            border-color:var(--fb-primary-light);
            background:var(--fb-primary-light);
          }

          .fb-btn-ic.warning {
            color:#d97706;
            border-color:#fde7b0;
            background:#fffbeb;
          }

          .fb-btn-ic.success {
            color:var(--fb-success);
            border-color:#c7f2df;
            background:var(--fb-success-light);
          }

          .fb-btn-ic.danger {
            color:var(--fb-danger);
            border-color:rgba(239,68,68,.18);
            background:var(--fb-danger-light);
          }

          .fb-analytics {
            display:grid;
            grid-template-columns:repeat(4, minmax(0,1fr));
            gap:14px;
            margin-bottom:18px;
          }

          @media(max-width:1200px) {
            .fb-analytics {
              grid-template-columns:repeat(2, minmax(0,1fr));
            }
          }

          @media(max-width:700px) {
            .fb-analytics {
              grid-template-columns:1fr;
            }
          }

          .fb-stat {
            background:var(--fb-card);
            border:1px solid var(--fb-border);
            border-radius:16px;
            padding:16px;
            box-shadow:var(--fb-shadow-sm);
            display:flex;
            align-items:center;
            gap:12px;
            min-height:92px;
          }

          .fb-stat-icon {
            width:48px;
            height:48px;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex:0 0 auto;
          }

          .fb-stat-icon.total {
            background:var(--fb-blue-light);
            color:var(--fb-blue);
          }

          .fb-stat-icon.fixed {
            background:var(--fb-success-light);
            color:var(--fb-success);
          }

          .fb-stat-icon.progress {
            background:var(--fb-warning-light);
            color:#d97706;
          }

          .fb-stat-icon.open {
            background:var(--fb-danger-light);
            color:var(--fb-danger);
          }

          .fb-stat-label {
            font-size:11px;
            font-weight:800;
            color:var(--fb-muted);
            text-transform:uppercase;
            letter-spacing:.06em;
          }

          .fb-stat-value {
            font-size:24px;
            font-weight:900;
            color:#111827;
            line-height:1.1;
            margin-top:4px;
          }

          .fb-stat-sub {
            font-size:12px;
            color:var(--fb-muted);
            margin-top:4px;
          }

          .fb-toolbar {
            background:var(--fb-card);
            border:1px solid var(--fb-border);
            border-radius:var(--fb-radius);
            padding:14px 16px;
            display:flex;
            flex-wrap:wrap;
            gap:14px;
            align-items:flex-end;
            justify-content:space-between;
            margin-bottom:16px;
            box-shadow:var(--fb-shadow-sm);
          }

          .fb-toolbar-left,
          .fb-toolbar-right {
            display:flex;
            align-items:flex-end;
            gap:12px;
            flex-wrap:wrap;
          }

          .fb-toolbar-left {
            flex:1;
          }

          .fb-filter-block {
            display:flex;
            flex-direction:column;
            gap:6px;
            min-width:170px;
          }

          .fb-filter-block.search {
            flex:1;
            min-width:280px;
          }

          .fb-filter-label {
            font-size:11px;
            font-weight:800;
            color:var(--fb-muted);
            text-transform:uppercase;
            letter-spacing:.06em;
          }

          .fb-input {
            background:#f9fafb;
            border:1px solid var(--fb-border);
            border-radius:8px;
            padding:10px 12px 10px 36px;
            font-size:14px;
            outline:none;
            transition:var(--fb-transition);
            min-width:240px;
            width:100%;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
            background-repeat:no-repeat;
            background-position:10px center;
            background-size:16px;
          }

          .fb-input:focus {
            background:#fff;
            border-color:var(--fb-primary);
            box-shadow:0 0 0 3px var(--fb-primary-light);
          }

          .fb-select {
            width:100%;
            padding:10px 12px;
            border-radius:8px;
            border:1px solid var(--fb-border);
            background:#fff;
            font-size:14px;
            outline:none;
            transition:var(--fb-transition);
          }

          .fb-select:focus {
            border-color:var(--fb-primary);
            box-shadow:0 0 0 3px var(--fb-primary-light);
          }

          .fb-card {
            background:#fff;
            border:1px solid var(--fb-border);
            border-radius:16px;
            box-shadow:var(--fb-shadow-sm);
            overflow:hidden;
          }

          .fb-list-head {
            display:grid;
            grid-template-columns:90px minmax(220px,1.4fr) 110px 150px 140px 140px 230px;
            gap:14px;
            align-items:center;
            padding:16px 16px 10px 16px;
            color:var(--fb-muted);
            font-size:11px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.06em;
          }

          @media(max-width:1280px) {
            .fb-list-head {
              display:none;
            }
          }

          .fb-list {
            display:flex;
            flex-direction:column;
            gap:12px;
            padding:0 0 16px 0;
          }

          .fb-item {
            background:var(--fb-card);
            border:1px solid var(--fb-border);
            border-radius:var(--fb-radius);
            transition:var(--fb-transition);
            overflow:hidden;
            margin:0 16px;
          }

          .fb-item:hover {
            border-color:var(--fb-primary);
            box-shadow:var(--fb-shadow);
          }

          .fb-item-row {
            padding:16px;
            display:grid;
            gap:16px;
            align-items:center;
            grid-template-columns:90px minmax(220px,1.4fr) 110px 150px 140px 140px 230px;
          }

          @media(max-width:1280px) {
            .fb-item-row {
              grid-template-columns:1fr;
            }
          }

          .fb-cell {
            min-width:0;
          }

          .fb-cell-title {
            font-size:11px;
            font-weight:800;
            color:var(--fb-muted);
            text-transform:uppercase;
            margin-bottom:4px;
            display:none;
          }

          @media(max-width:1280px) {
            .fb-cell-title {
              display:block;
            }
          }

          .fb-id-badge {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:54px;
            height:36px;
            padding:0 12px;
            border-radius:10px;
            background:var(--fb-blue-light);
            color:var(--fb-blue);
            font-size:13px;
            font-weight:900;
          }

          .fb-main {
            display:flex;
            flex-direction:column;
            min-width:0;
          }

          .fb-ttl {
            font-weight:900;
            font-size:15px;
            margin-bottom:4px;
            color:#111827;
          }

          .fb-subt {
            font-size:13px;
            color:var(--fb-muted);
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
          }

          .fb-avatar {
            width:38px;
            height:38px;
            border-radius:999px;
            object-fit:cover;
            border:2px solid #fff;
            box-shadow:0 0 0 1px var(--fb-border);
          }

          .fb-person {
            display:flex;
            align-items:center;
            gap:10px;
            min-width:0;
          }

          .fb-status-pill {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:6px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:900;
            white-space:nowrap;
          }

          .fb-status-pill.fixed {
            background:var(--fb-success-light);
            color:#047857;
          }

          .fb-status-pill.progress {
            background:var(--fb-warning-light);
            color:#b45309;
          }

          .fb-status-pill.open {
            background:var(--fb-danger-light);
            color:#b91c1c;
          }

          .fb-actions {
            display:flex;
            align-items:center;
            justify-content:flex-end;
            gap:8px;
            flex-wrap:wrap;
          }

          .fb-empty {
            text-align:center;
            padding:60px;
            color:var(--fb-muted);
            background:#fff;
            border:1px dashed var(--fb-border);
            border-radius:16px;
            margin:16px;
          }

          .fb-pagination {
            margin-top:18px;
            background:#fff;
            border:1px solid var(--fb-border);
            border-radius:14px;
            padding:14px 16px;
            box-shadow:var(--fb-shadow-sm);
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
          }

          .fb-page-buttons {
            display:flex;
            gap:6px;
            flex-wrap:wrap;
          }

          .fb-page-btn {
            border:1px solid var(--fb-border);
            background:#fff;
            color:var(--fb-text);
            border-radius:10px;
            padding:8px 12px;
            font-weight:800;
            cursor:pointer;
          }

          .fb-page-btn.active {
            background:var(--fb-primary);
            color:#fff;
            border-color:var(--fb-primary);
          }

          .fb-page-btn:disabled {
            opacity:.45;
            cursor:not-allowed;
          }

          .fb-modal-backdrop {
            position:fixed;
            inset:0;
            z-index:1200;
            background:rgba(17,24,39,.55);
            backdrop-filter:blur(3px);
            opacity:0;
            pointer-events:none;
            transition:opacity .22s ease;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:18px;
          }

          .fb-modal-backdrop.open {
            opacity:1;
            pointer-events:auto;
          }

          .fb-modal {
            width:100%;
            max-width:720px;
            background:#fff;
            border:1px solid rgba(229,231,235,.9);
            border-radius:16px;
            box-shadow:var(--fb-shadow);
            transform:translateY(12px) scale(.985);
            transition:transform .22s ease;
            overflow:hidden;
          }

          .fb-modal.fb-modal-lg {
            max-width:980px;
          }

          .fb-modal-backdrop.open .fb-modal {
            transform:translateY(0) scale(1);
          }

          .fb-modal-h {
            display:flex;
            gap:12px;
            align-items:center;
            justify-content:space-between;
            padding:16px 18px;
            border-bottom:1px solid var(--fb-border);
            background:#fafafa;
          }

          .fb-modal-ttl {
            font-weight:900;
            font-size:16px;
            line-height:1.2;
            margin:0;
            color:#111827;
          }

          .fb-modal-b {
            padding:20px 18px;
            max-height:72vh;
            overflow-y:auto;
          }

          .fb-modal-f {
            padding:14px 18px;
            border-top:1px solid var(--fb-border);
            background:#fafafa;
            display:flex;
            gap:10px;
            justify-content:flex-end;
            flex-wrap:wrap;
          }

          .fb-form-group {
            margin-bottom:16px;
          }

          .fb-label {
            display:block;
            font-size:13px;
            font-weight:800;
            color:var(--fb-text);
            margin-bottom:6px;
          }

          .fb-input-form {
            width:100%;
            padding:10px 12px;
            border-radius:8px;
            border:1px solid var(--fb-border);
            background:#fff;
            font-size:14px;
            outline:none;
            transition:var(--fb-transition);
          }

          .fb-input-form:focus {
            border-color:var(--fb-primary);
            box-shadow:0 0 0 3px var(--fb-primary-light);
          }

          .fb-quill {
            height:320px;
            background:#fff;
          }

          .fb-modal-content-html {
            font-size:14px;
            line-height:1.7;
            color:#111827;
          }

          .fb-toast-wrap {
            position:fixed;
            right:20px;
            bottom:20px;
            z-index:9999;
            display:flex;
            flex-direction:column;
            gap:10px;
            pointer-events:none;
          }

          .fb-toast {
            pointer-events:auto;
            min-width:280px;
            max-width:360px;
            background:#fff;
            border:1px solid var(--fb-border);
            border-radius:14px;
            box-shadow:var(--fb-shadow);
            padding:12px;
            display:flex;
            gap:10px;
            align-items:flex-start;
            animation:fbToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
          }

          @keyframes fbToastIn {
            from {
              transform:translateX(100%);
              opacity:0;
            }
            to {
              transform:translateX(0);
              opacity:1;
            }
          }

          .fb-toast-ic {
            width:34px;
            height:34px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex:0 0 auto;
          }

          .fb-toast-ic.ok {
            background:var(--fb-success-light);
            color:var(--fb-success);
          }

          .fb-toast-ic.bad {
            background:var(--fb-danger-light);
            color:var(--fb-danger);
          }

          .fb-toast-ttl {
            font-weight:900;
            font-size:13px;
            margin:0;
            color:#111827;
          }

          .fb-toast-msg {
            font-size:12px;
            color:#374151;
            margin:4px 0 0 0;
            line-height:1.4;
          }

          .fb-toast-x {
            margin-left:auto;
            background:transparent;
            border:none;
            cursor:pointer;
            color:var(--fb-muted);
          }

          .fb-loading {
            opacity:.55;
            pointer-events:none;
          }
        </style>
    @endpush
@endonce

@section('content')
    <div class="fb-wrap">
      <div class="fb-header">
        <div class="fb-titlebar">
          <div>
            <div class="fb-title">SYSTEM FEEDBACK</div>
            <div class="fb-sub">Feedback, Ideen, Fehlerberichte und Entwickler-Antworten zentral verwalten.</div>

            <div class="fb-breadcrumb">
              <a href="{{ url('/employee_dashboard') }}">Home</a>
              <span>›</span>
              <span class="current">Feedback</span>
            </div>
          </div>

          <button type="button" class="fb-btn" onclick="openFbModal('createFeedbackModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 5v14M5 12h14"></path>
            </svg>
            Neue Idee
          </button>
        </div>
      </div>

      <div class="fb-analytics">
        <div class="fb-stat">
          <div class="fb-stat-icon total">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 12h18M3 6h18M3 18h18"/>
            </svg>
          </div>
          <div>
            <div class="fb-stat-label">Gesamt</div>
            <div class="fb-stat-value" id="statTotal">{{ $stats['total'] }}</div>
            <div class="fb-stat-sub">Alle Einträge</div>
          </div>
        </div>

        <div class="fb-stat">
          <div class="fb-stat-icon fixed">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5"/>
            </svg>
          </div>
          <div>
            <div class="fb-stat-label">Abgeschlossen</div>
            <div class="fb-stat-value" id="statFixed">{{ $stats['fixed'] }}</div>
            <div class="fb-stat-sub">Erledigte Aufgaben</div>
          </div>
        </div>

        <div class="fb-stat">
          <div class="fb-stat-icon progress">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 8v4l3 3"/>
              <path d="M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0z"/>
            </svg>
          </div>
          <div>
            <div class="fb-stat-label">In Bearbeitung</div>
            <div class="fb-stat-value" id="statProgress">{{ $stats['progress'] }}</div>
            <div class="fb-stat-sub">Aktive Umsetzung</div>
          </div>
        </div>

        <div class="fb-stat">
          <div class="fb-stat-icon open">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 9v4"/>
              <path d="M12 17h.01"/>
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            </svg>
          </div>
          <div>
            <div class="fb-stat-label">Reste</div>
            <div class="fb-stat-value" id="statOpen">{{ $stats['open'] }}</div>
            <div class="fb-stat-sub">Noch offen</div>
          </div>
        </div>
      </div>

      <div class="fb-toolbar">
        <div class="fb-toolbar-left">
          <div class="fb-filter-block search">
            <label class="fb-filter-label">Suche</label>
            <input type="text" class="fb-input" id="feedbackSearch" placeholder="Suche nach Ticket, Titel, Mitarbeiter oder Status">
          </div>

          <div class="fb-filter-block">
            <label class="fb-filter-label">Status</label>
            <select class="fb-select" id="feedbackStatus">
              <option value="all">Alle</option>
              <option value="open">Neu / Reste</option>
              <option value="progress">In Bearbeitung</option>
              <option value="fixed">Abgeschlossen</option>
            </select>
          </div>
        </div>

        <div class="fb-toolbar-right">
          <button type="button" class="fb-btn-soft" id="feedbackSearchBtn">Suchen</button>
          <button type="button" class="fb-btn-soft" id="feedbackResetBtn">Zurücksetzen</button>
        </div>
      </div>

      <div class="fb-card" id="feedbackCard">
        <div class="fb-list-head">
          <div>Ticket</div>
          <div>Titel</div>
          <div>Beschreibung</div>
          <div>Mitarbeiter</div>
          <div>Erstellt</div>
          <div>Status</div>
          <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="fb-list" id="feedbackList">
          <div class="fb-empty">Feedback wird geladen...</div>
        </div>
      </div>

      <div class="fb-pagination" id="feedbackPagination" style="display:none;">
        <div id="feedbackPaginationInfo" style="font-size:12px;color:#6b7280;"></div>
        <div class="fb-page-buttons" id="feedbackPaginationButtons"></div>
      </div>
    </div>

    {{-- Create Modal --}}
    <div class="fb-modal-backdrop" id="createFeedbackModal">
      <div class="fb-modal fb-modal-lg">
        <div class="fb-modal-h">
          <h3 class="fb-modal-ttl">Neue Idee / Feedback</h3>
          <button class="fb-btn-ic" type="button" onclick="closeFbModal('createFeedbackModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form id="createFeedbackForm">
          @csrf

          <div class="fb-modal-b">
            <div class="fb-form-group">
              <label class="fb-label">Feedback von</label>
              <select class="fb-select" disabled>
                @foreach($employees as $employee)
                      <option value="{{ $employee->id }}" @selected($employee->id == $currentEmployeeId)>
                        {{ $employee->name }} {{ $employee->lastname }}
                      </option>
                @endforeach
              </select>
            </div>

            <div class="fb-form-group">
              <label class="fb-label">Titel *</label>
              <input type="text" name="title" id="createFeedbackTitle" class="fb-input-form" required>
            </div>

            <div class="fb-form-group">
              <label class="fb-label">Ideenbeschreibung *</label>
              <div id="createFeedbackEditor" class="fb-quill"></div>
              <textarea name="editor_text" id="createFeedbackText" hidden></textarea>
            </div>
          </div>

          <div class="fb-modal-f">
            <button type="button" class="fb-btn-soft" onclick="closeFbModal('createFeedbackModal')">Abbrechen</button>
            <button type="submit" class="fb-btn">Speichern</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Description Modal --}}
    <div class="fb-modal-backdrop" id="descriptionModal">
      <div class="fb-modal fb-modal-lg">
        <div class="fb-modal-h">
          <h3 class="fb-modal-ttl" id="descriptionModalTitle">Beschreibung</h3>
          <button class="fb-btn-ic" type="button" onclick="closeFbModal('descriptionModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="fb-modal-b">
          <div class="fb-modal-content-html" id="descriptionModalBody"></div>
        </div>

        <div class="fb-modal-f">
          <button type="button" class="fb-btn" onclick="closeFbModal('descriptionModal')">OK</button>
        </div>
      </div>
    </div>

    {{-- Answer View Modal --}}
    <div class="fb-modal-backdrop" id="answerViewModal">
      <div class="fb-modal fb-modal-lg">
        <div class="fb-modal-h">
          <h3 class="fb-modal-ttl" id="answerViewModalTitle">Antwort</h3>
          <button class="fb-btn-ic" type="button" onclick="closeFbModal('answerViewModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="fb-modal-b">
          <div class="fb-modal-content-html" id="answerViewModalBody"></div>
        </div>

        <div class="fb-modal-f">
          <button type="button" class="fb-btn" onclick="closeFbModal('answerViewModal')">OK</button>
        </div>
      </div>
    </div>

    {{-- Response Modal --}}
    <div class="fb-modal-backdrop" id="responseModal">
      <div class="fb-modal fb-modal-lg">
        <div class="fb-modal-h">
          <h3 class="fb-modal-ttl" id="responseModalTitle">Antwort schreiben</h3>
          <button class="fb-btn-ic" type="button" onclick="closeFbModal('responseModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form id="responseForm">
          @csrf

          <input type="hidden" name="id" id="responseFeedbackId">

          <div class="fb-modal-b">
            <div class="fb-form-group">
              <label class="fb-label">Antwort *</label>
              <div id="responseEditor" class="fb-quill"></div>
              <textarea name="response_text" id="responseText" hidden></textarea>
            </div>
          </div>

          <div class="fb-modal-f">
            <button type="button" class="fb-btn-soft" onclick="closeFbModal('responseModal')">Abbrechen</button>
            <button type="submit" class="fb-btn">Speichern</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Upload Modal --}}
    <div class="fb-modal-backdrop" id="uploadModal">
      <div class="fb-modal">
        <div class="fb-modal-h">
          <h3 class="fb-modal-ttl">Bild hochladen</h3>
          <button class="fb-btn-ic" type="button" onclick="closeFbModal('uploadModal')">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form id="uploadFeedbackForm" enctype="multipart/form-data">
          @csrf

          <input type="hidden" name="feedback_id" id="uploadFeedbackId">

          <div class="fb-modal-b">
            <div class="fb-form-group">
              <label class="fb-label">Titel</label>
              <input type="text" name="title" class="fb-input-form">
            </div>

            <div class="fb-form-group">
              <label class="fb-label">Bild</label>
              <input type="file" name="file" class="fb-input-form" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required>
            </div>
          </div>

          <div class="fb-modal-f">
            <button type="button" class="fb-btn-soft" onclick="closeFbModal('uploadModal')">Abbrechen</button>
            <button type="submit" class="fb-btn">Hochladen</button>
          </div>
        </form>
      </div>
    </div>

    <div class="fb-toast-wrap" id="fbToastWrap"></div>
@endsection

@once
    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

        <script>
        (function () {
          'use strict';

          const routes = {
            list: @json(route('system.feedback.ajax.list')),
            store: @json(route('system.feedback.ajax.store')),
            answer: @json(route('system.feedback.ajax.answer')),
            statusBase: @json(url('/feedback/ajax/status')),
            upload: @json(route('system.feedback.ajax.upload')),
            deleteBase: @json(url('/feedback/ajax/delete')),
          };

          const isProgrammer = @json($isProgrammer);

          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

          let state = {
            page: 1,
            search: '',
            status: 'all',
            items: [],
            isLoading: false,
          };

          const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ header: 1 }, { header: 2 }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ direction: 'rtl' }],
            [{ size: ['small', false, 'large', 'huge'] }],
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            [{ color: [] }, { background: [] }],
            [{ align: [] }],
            ['link', 'image', 'video'],
            ['clean']
          ];

          let createQuill = null;
          let responseQuill = null;

          function initEditors() {
            if (!createQuill) {
              createQuill = new Quill('#createFeedbackEditor', {
                modules: { toolbar: toolbarOptions },
                theme: 'snow'
              });
            }

            if (!responseQuill) {
              responseQuill = new Quill('#responseEditor', {
                modules: { toolbar: toolbarOptions },
                theme: 'snow'
              });
            }
          }

          window.openFbModal = function (id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('open');

            setTimeout(() => {
              if (id === 'createFeedbackModal' || id === 'responseModal') {
                initEditors();
              }
            }, 80);
          };

          window.closeFbModal = function (id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('open');
          };

          function escapeHtml(value) {
            return String(value ?? '')
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
          }

          function toast(kind, title, msg) {
            const wrap = document.getElementById('fbToastWrap');
            if (!wrap) return;

            const icons = {
              ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
              bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
            };

            const el = document.createElement('div');
            el.className = 'fb-toast';
            el.innerHTML = `
              <div class="fb-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
              <div style="flex:1;">
                <p class="fb-toast-ttl">${escapeHtml(title)}</p>
                <p class="fb-toast-msg">${escapeHtml(msg)}</p>
              </div>
              <button class="fb-toast-x" type="button" onclick="this.parentElement.remove()">×</button>
            `;

            wrap.appendChild(el);
            setTimeout(() => {
              try { el.remove(); } catch (e) {}
            }, 4500);
          }

          function iconSvg(name) {
            const icons = {
              file: `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8M10 9H8"/></svg>`,
              answer: `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>`,
              edit: `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>`,
              check: `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>`,
              clock: `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><path d="M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0z"/></svg>`,
              upload: `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>`,
              trash: `<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg>`
            };

            return icons[name] || icons.file;
          }

          function formatDate(value) {
            if (!value) return '—';

            try {
              return new Intl.DateTimeFormat('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
              }).format(new Date(value));
            } catch (e) {
              return value;
            }
          }

          function statusHtml(item) {
            if (item.status === 'fixed') {
              return `<span class="fb-status-pill fixed">Abgeschlossen</span>`;
            }

            if (item.status === 'progress') {
              return `<span class="fb-status-pill progress">In Bearbeitung</span>`;
            }

            return `<span class="fb-status-pill open">Neu</span>`;
          }

          function employeeImage(item) {
            if (item.employee_image) {
              return `{{ asset('images/employee') }}/${item.employee_image}`;
            }

            return `{{ asset('images/icons/placeholder.svg') }}`;
          }

          function renderItems(items) {
            const list = document.getElementById('feedbackList');

            if (!list) return;

            if (!items || !items.length) {
              list.innerHTML = `<div class="fb-empty">Keine Feedback-Einträge gefunden.</div>`;
              return;
            }

            list.innerHTML = items.map(item => {
              const employeeName = `${item.name || ''} ${item.lastname || ''}`.trim() || 'Unbekannt';
              const hasAnswer = !!item.response;

              const programmerActions = isProgrammer ? `
                <button type="button" class="fb-btn-ic success js-status" data-id="${item.id}" data-status="fixed" title="Fixed">
                  ${iconSvg('check')}
                </button>

                <button type="button" class="fb-btn-ic warning js-status" data-id="${item.id}" data-status="progress" title="Progress">
                  ${iconSvg('clock')}
                </button>

                <button type="button" class="fb-btn-ic primary js-response" data-id="${item.id}" data-ticket="${escapeHtml(item.ticket_no)}" data-response="${escapeHtml(item.response || '')}" title="Antwort schreiben">
                  ${iconSvg('edit')}
                </button>

                <button type="button" class="fb-btn-ic danger js-delete" data-id="${item.id}" title="Löschen">
                  ${iconSvg('trash')}
                </button>
              ` : '';

              return `
                <div class="fb-item" data-id="${item.id}">
                  <div class="fb-item-row">
                    <div class="fb-cell">
                      <div class="fb-cell-title">Ticket</div>
                      <span class="fb-id-badge">#${escapeHtml(item.ticket_no || item.id)}</span>
                    </div>

                    <div class="fb-cell">
                      <div class="fb-cell-title">Titel</div>
                      <div class="fb-main">
                        <div class="fb-ttl">${escapeHtml(item.title || 'Ohne Titel')}</div>
                        <div class="fb-subt">Feedback-ID: ${escapeHtml(item.id)}</div>
                      </div>
                    </div>

                    <div class="fb-cell">
                      <div class="fb-cell-title">Beschreibung</div>
                      <button type="button" class="fb-btn-ic warning js-description" data-ticket="${escapeHtml(item.ticket_no)}" data-title="${escapeHtml(item.title || '')}" data-description="${escapeHtml(item.description || '')}" title="Beschreibung anzeigen">
                        ${iconSvg('file')}
                      </button>
                    </div>

                    <div class="fb-cell">
                      <div class="fb-cell-title">Mitarbeiter</div>
                      <div class="fb-person">
                        <img src="${employeeImage(item)}" class="fb-avatar" alt="${escapeHtml(employeeName)}">
                        <div class="fb-main">
                          <div class="fb-ttl" style="font-size:14px;">${escapeHtml(employeeName)}</div>
                        </div>
                      </div>
                    </div>

                    <div class="fb-cell">
                      <div class="fb-cell-title">Erstellt</div>
                      <div class="fb-main">
                        <div class="fb-ttl" style="font-size:13px;">${formatDate(item.created_at)}</div>
                      </div>
                    </div>

                    <div class="fb-cell">
                      <div class="fb-cell-title">Status</div>
                      ${statusHtml(item)}
                    </div>

                    <div class="fb-cell">
                      <div class="fb-cell-title">Aktionen</div>
                      <div class="fb-actions">
                        <button type="button" class="fb-btn-ic ${hasAnswer ? 'primary' : 'danger'} js-answer-view" data-ticket="${escapeHtml(item.ticket_no)}" data-response="${escapeHtml(item.response || '')}" title="Antwort anzeigen">
                          ${iconSvg('answer')}
                        </button>

                        <button type="button" class="fb-btn-ic primary js-upload" data-id="${item.id}" title="Bild hochladen">
                          ${iconSvg('upload')}
                        </button>

                        ${programmerActions}
                      </div>
                    </div>
                  </div>
                </div>
              `;
            }).join('');
          }

          function renderPagination(pagination) {
            const wrap = document.getElementById('feedbackPagination');
            const info = document.getElementById('feedbackPaginationInfo');
            const buttons = document.getElementById('feedbackPaginationButtons');

            if (!wrap || !info || !buttons) return;

            if (!pagination || pagination.last_page <= 1) {
              wrap.style.display = 'none';
              return;
            }

            wrap.style.display = 'flex';

            info.innerHTML = `
              Zeige <strong>${pagination.from || 0}</strong>
              bis <strong>${pagination.to || 0}</strong>
              von <strong>${pagination.total || 0}</strong> Einträgen
            `;

            let html = '';

            html += `
              <button type="button" class="fb-page-btn" data-page="${pagination.current_page - 1}" ${pagination.current_page <= 1 ? 'disabled' : ''}>
                Zurück
              </button>
            `;

            for (let i = 1; i <= pagination.last_page; i++) {
              if (
                i === 1 ||
                i === pagination.last_page ||
                Math.abs(i - pagination.current_page) <= 1
              ) {
                html += `
                  <button type="button" class="fb-page-btn ${i === pagination.current_page ? 'active' : ''}" data-page="${i}">
                    ${i}
                  </button>
                `;
              }
            }

            html += `
              <button type="button" class="fb-page-btn" data-page="${pagination.current_page + 1}" ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}>
                Weiter
              </button>
            `;

            buttons.innerHTML = html;
          }

          function updateStats(stats) {
            if (!stats) return;

            document.getElementById('statTotal').textContent = stats.total ?? 0;
            document.getElementById('statFixed').textContent = stats.fixed ?? 0;
            document.getElementById('statProgress').textContent = stats.progress ?? 0;
            document.getElementById('statOpen').textContent = stats.open ?? 0;
          }

          async function loadFeedback(page = 1) {
            if (state.isLoading) return;

            state.isLoading = true;
            state.page = page;

            const card = document.getElementById('feedbackCard');
            card?.classList.add('fb-loading');

            const params = new URLSearchParams({
              page: state.page,
              search: state.search,
              status: state.status,
            });

            try {
              const response = await fetch(`${routes.list}?${params.toString()}`, {
                headers: {
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                },
              });

              const json = await response.json();

              if (!response.ok || !json.success) {
                throw new Error(json.message || 'Daten konnten nicht geladen werden.');
              }

              state.items = json.items || [];

              renderItems(state.items);
              renderPagination(json.pagination);
              updateStats(json.stats);
            } catch (error) {
              toast('bad', 'Fehler', error.message || 'Daten konnten nicht geladen werden.');
            } finally {
              state.isLoading = false;
              card?.classList.remove('fb-loading');
            }
          }

          async function postJson(url, data = {}, method = 'POST') {
            const response = await fetch(url, {
              method,
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
              },
              body: JSON.stringify(data),
            });

            const json = await response.json().catch(() => ({}));

            if (!response.ok || json.success === false) {
              throw new Error(json.message || 'Aktion fehlgeschlagen.');
            }

            return json;
          }

          document.addEventListener('click', function (e) {
            if (e.target.classList.contains('fb-modal-backdrop')) {
              e.target.classList.remove('open');
            }

            const pageBtn = e.target.closest('.fb-page-btn');
            if (pageBtn && !pageBtn.disabled) {
              loadFeedback(parseInt(pageBtn.dataset.page || '1', 10));
              return;
            }

            const descBtn = e.target.closest('.js-description');
            if (descBtn) {
              document.getElementById('descriptionModalTitle').textContent = `Beschreibung #${descBtn.dataset.ticket || ''}`;
              document.getElementById('descriptionModalBody').innerHTML = descBtn.dataset.description || '<div class="fb-empty">Keine Beschreibung vorhanden.</div>';
              openFbModal('descriptionModal');
              return;
            }

            const answerViewBtn = e.target.closest('.js-answer-view');
            if (answerViewBtn) {
              const response = answerViewBtn.dataset.response || '';

              document.getElementById('answerViewModalTitle').textContent = `Antwort #${answerViewBtn.dataset.ticket || ''}`;
              document.getElementById('answerViewModalBody').innerHTML = response || `
                <div style="padding:14px;border-radius:12px;background:#eff6ff;color:#1d4ed8;font-weight:800;">
                  Warten auf Antwort...
                </div>
              `;

              openFbModal('answerViewModal');
              return;
            }

            const responseBtn = e.target.closest('.js-response');
            if (responseBtn) {
              initEditors();

              document.getElementById('responseFeedbackId').value = responseBtn.dataset.id || '';
              document.getElementById('responseModalTitle').textContent = `Antwort schreiben #${responseBtn.dataset.ticket || ''}`;

              setTimeout(() => {
                responseQuill.root.innerHTML = responseBtn.dataset.response || '';
              }, 80);

              openFbModal('responseModal');
              return;
            }

            const statusBtn = e.target.closest('.js-status');
            if (statusBtn) {
              const id = statusBtn.dataset.id;
              const status = statusBtn.dataset.status;

              postJson(`${routes.statusBase}/${id}`, { status })
                .then(json => {
                  toast('ok', 'Aktualisiert', json.message || 'Status wurde aktualisiert.');
                  loadFeedback(state.page);
                })
                .catch(error => toast('bad', 'Fehler', error.message));
              return;
            }

            const uploadBtn = e.target.closest('.js-upload');
            if (uploadBtn) {
              document.getElementById('uploadFeedbackId').value = uploadBtn.dataset.id || '';
              document.getElementById('uploadFeedbackForm').reset();
              openFbModal('uploadModal');
              return;
            }

            const deleteBtn = e.target.closest('.js-delete');
            if (deleteBtn) {
              if (!confirm('Möchten Sie dieses Feedback wirklich löschen?')) {
                return;
              }

              const id = deleteBtn.dataset.id;

              fetch(`${routes.deleteBase}/${id}`, {
                method: 'DELETE',
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                },
              })
                .then(async response => {
                  const json = await response.json().catch(() => ({}));

                  if (!response.ok || json.success === false) {
                    throw new Error(json.message || 'Feedback konnte nicht gelöscht werden.');
                  }

                  toast('ok', 'Gelöscht', json.message || 'Feedback wurde gelöscht.');
                  loadFeedback(state.page);
                })
                .catch(error => toast('bad', 'Fehler', error.message));
            }
          });

          document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
              document.querySelectorAll('.fb-modal-backdrop.open').forEach(el => el.classList.remove('open'));
            }
          });

          document.getElementById('feedbackSearchBtn')?.addEventListener('click', function () {
            state.search = document.getElementById('feedbackSearch').value.trim();
            state.status = document.getElementById('feedbackStatus').value || 'all';
            loadFeedback(1);
          });

          document.getElementById('feedbackResetBtn')?.addEventListener('click', function () {
            document.getElementById('feedbackSearch').value = '';
            document.getElementById('feedbackStatus').value = 'all';

            state.search = '';
            state.status = 'all';

            loadFeedback(1);
          });

          document.getElementById('feedbackSearch')?.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
              state.search = this.value.trim();
              state.status = document.getElementById('feedbackStatus').value || 'all';
              loadFeedback(1);
            }
          });

          document.getElementById('feedbackStatus')?.addEventListener('change', function () {
            state.status = this.value || 'all';
            state.search = document.getElementById('feedbackSearch').value.trim();
            loadFeedback(1);
          });

          document.getElementById('createFeedbackForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();

            initEditors();

            const title = document.getElementById('createFeedbackTitle').value.trim();
            const editorText = createQuill.root.innerHTML;

            document.getElementById('createFeedbackText').value = editorText;

            try {
              const json = await postJson(routes.store, {
                title: title,
                editor_text: editorText,
              });

              toast('ok', 'Gespeichert', json.message || 'Feedback wurde gespeichert.');

              this.reset();
              createQuill.root.innerHTML = '';

              closeFbModal('createFeedbackModal');
              loadFeedback(1);
            } catch (error) {
              toast('bad', 'Fehler', error.message);
            }
          });

          document.getElementById('responseForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();

            initEditors();

            const id = document.getElementById('responseFeedbackId').value;
            const responseText = responseQuill.root.innerHTML;

            document.getElementById('responseText').value = responseText;

            try {
              const json = await postJson(routes.answer, {
                id: id,
                response_text: responseText,
              });

              toast('ok', 'Gespeichert', json.message || 'Antwort wurde gespeichert.');

              closeFbModal('responseModal');
              loadFeedback(state.page);
            } catch (error) {
              toast('bad', 'Fehler', error.message);
            }
          });

          document.getElementById('uploadFeedbackForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
              const response = await fetch(routes.upload, {
                method: 'POST',
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrfToken,
                  'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
              });

              const json = await response.json().catch(() => ({}));

              if (!response.ok || json.success === false) {
                throw new Error(json.message || 'Bild konnte nicht hochgeladen werden.');
              }

              toast('ok', 'Hochgeladen', json.message || 'Bild wurde hochgeladen.');

              this.reset();
              closeFbModal('uploadModal');
            } catch (error) {
              toast('bad', 'Fehler', error.message);
            }
          });

          window.GlobalBreadcrumbs = [
            {
              label: 'Dashboard',
              url: "{{ url('/') }}"
            },
            {
              label: 'Feedback',
              url: "{{ url()->current() }}",
              clickable: false
            }
          ];

          if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
          }

          loadFeedback(1);
        })();
        </script>
    @endpush
@endonce