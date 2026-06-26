@extends('admin.layouts.app')

@section('title', 'LOHN / VOLLKOSTEN')

@section('style')
  <style>
    :root {
      --app-bg: #f3f4f6;
      --card-bg: #ffffff;
      --text-main: #1f2937;
      --text-muted: #6b7280;
      --border: #e5e7eb;
      --primary: #8fc73e;
      --primary-hover: #7baa18;
      --primary-light: #f4fae7;
      --blue: #74b2d4;
      --blue-light: #eff6ff;
      --success: #10b981;
      --success-light: #ecfdf5;
      --warning: #f59e0b;
      --warning-light: #fffbeb;
      --danger: #ef4444;
      --danger-light: #fef2f2;
      --gray: #6b7280;
      --gray-light: #f3f4f6;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
      --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
      --radius: 16px;
      --transition: all .2s ease-in-out;
    }

    .oc-wrap {
      font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color: var(--text-main);
      margin: 20px auto;
      padding-right: 79px;
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
      font-weight: 900;
      letter-spacing: -.025em;
      color: #111827;
      text-transform: uppercase;
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
      font-weight: 800;
    }

    .oc-breadcrumb a:hover {
      color: var(--text-main);
    }

    .oc-breadcrumb span.current {
      color: #111827;
      font-weight: 900;
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
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      line-height: 1.2;
    }

    .oc-btn:hover {
      background: var(--primary-hover);
      color: #fff;
      text-decoration: none;
    }

    .oc-btn-blue {
      background: var(--blue);
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      line-height: 1.2;
    }

    .oc-btn-blue:hover {
      background: #559fc7;
      color: #fff;
      text-decoration: none;
    }

    .oc-btn-success {
      background: var(--success);
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      line-height: 1.2;
    }

    .oc-btn-success:hover {
      background: #059669;
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
      justify-content: center;
      gap: 8px;
    }

    .oc-btn-soft:hover {
      background: #f9fafb;
      color: var(--text-main);
      text-decoration: none;
    }

    .oc-btn-soft.active {
      background: var(--primary-light);
      color: #365314;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(143, 199, 62, .12);
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
      color: var(--blue);
    }

    .oc-stat-icon.created {
      background: var(--success-light);
      color: var(--success);
    }

    .oc-stat-icon.updated {
      background: var(--warning-light);
      color: #d97706;
    }

    .oc-stat-icon.period {
      background: var(--primary-light);
      color: #365314;
    }

    .oc-stat-label {
      font-size: 11px;
      font-weight: 900;
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

    .oc-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      margin-bottom: 18px;
    }

    .oc-card-header {
      padding: 16px 18px;
      border-bottom: 1px solid var(--border);
      background: #fafafa;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .oc-card-title {
      margin: 0;
      font-size: 16px;
      font-weight: 900;
      color: #111827;
      text-transform: uppercase;
    }

    .oc-card-sub {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .oc-card-body {
      padding: 18px;
    }

    .oc-filter-form {
      display: grid;
      grid-template-columns: minmax(220px, 1fr) 190px 130px auto;
      gap: 12px;
      align-items: end;
    }

    @media(max-width:1000px) {
      .oc-filter-form {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media(max-width:650px) {
      .oc-filter-form {
        grid-template-columns: 1fr;
      }
    }

    .oc-form-group {
      min-width: 0;
    }

    .oc-label {
      display: block;
      font-size: 12px;
      font-weight: 900;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-bottom: 7px;
    }

    .oc-input,
    .oc-select {
      width: 100%;
      padding: 11px 12px;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: #fff;
      color: #111827;
      font-size: 14px;
      outline: none;
      transition: var(--transition);
      min-height: 42px;
    }

    .oc-input:focus,
    .oc-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--primary-light);
    }

    .oc-view-toggle {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }

    .salary-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    @media(max-width:1400px) {
      .salary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media(max-width:900px) {
      .salary-grid {
        grid-template-columns: 1fr;
      }
    }

    .salary-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 18px;
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      transition: var(--transition);
    }

    .salary-card:hover {
      box-shadow: var(--shadow);
      transform: translateY(-2px);
    }

    .salary-card-top {
      background: #74b2d4;
      padding: 22px;
      text-align: center;
      color: #fff;
      border-bottom: 1px solid rgba(255, 255, 255, .25);
    }

    .salary-avatar {
      width: 68px;
      height: 68px;
      border-radius: 999px;
      object-fit: cover;
      box-shadow: 0 0 0 4px rgba(255, 255, 255, .45);
      background: #fff;
      margin-bottom: 10px;
    }

    .salary-employee-name {
      font-size: 16px;
      font-weight: 900;
      color: #fff;
      line-height: 1.25;
    }

    .salary-period {
      font-size: 12px;
      font-weight: 800;
      color: rgba(255, 255, 255, .88);
      margin-top: 3px;
    }

    .salary-hour-label {
      font-size: 12px;
      color: rgba(255, 255, 255, .86);
      font-weight: 800;
      margin-top: 14px;
    }

    .salary-hour-value {
      font-size: 28px;
      font-weight: 900;
      color: #fff;
      line-height: 1.1;
      margin-top: 3px;
    }

    .salary-card-body {
      padding: 18px;
    }

    .salary-main-cost {
      text-align: center;
      margin-bottom: 16px;
    }

    .salary-main-cost-label {
      font-size: 12px;
      color: var(--text-muted);
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .salary-main-cost-value {
      font-size: 32px;
      font-weight: 900;
      color: #111827;
      line-height: 1.1;
      margin-top: 4px;
      font-variant-numeric: tabular-nums;
    }

    .oc-badge-row {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 7px;
      margin-top: 12px;
    }

    .oc-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      padding: 5px 9px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 900;
      line-height: 1.1;
      white-space: nowrap;
    }

    .oc-badge.blue {
      background: var(--blue-light);
      color: #075985;
    }

    .oc-badge.primary {
      background: var(--primary-light);
      color: #365314;
    }

    .oc-badge.warning {
      background: var(--warning-light);
      color: #b45309;
    }

    .salary-mini-list {
      border: 1px solid var(--border);
      border-radius: 14px;
      overflow: hidden;
      margin-bottom: 16px;
    }

    .salary-mini-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 12px;
      border-bottom: 1px solid var(--border);
      background: #fff;
    }

    .salary-mini-row:last-child {
      border-bottom: 0;
    }

    .salary-mini-row span {
      font-size: 13px;
      color: var(--text-muted);
      font-weight: 800;
    }

    .salary-mini-row strong {
      font-size: 13px;
      color: #111827;
      font-weight: 900;
      font-variant-numeric: tabular-nums;
      text-align: right;
    }

    .salary-actions {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 8px;
    }

    @media(max-width:520px) {
      .salary-actions {
        grid-template-columns: 1fr;
      }
    }

    .salary-edit-panel {
      margin-top: 16px;
      padding: 14px;
      border-radius: 16px;
      border: 1px solid var(--border);
      background: #fafafa;
    }

    .salary-edit-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    @media(max-width:520px) {
      .salary-edit-grid {
        grid-template-columns: 1fr;
      }
    }

    .salary-tax-wrap {
      grid-column: 1 / -1;
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: 14px;
      background: #fff;
    }

    .salary-tax-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px;
    }

    @media(max-width:650px) {
      .salary-tax-grid {
        grid-template-columns: 1fr;
      }
    }

    .salary-checkline {
      grid-column: 1 / -1;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: #fff;
      font-size: 13px;
      color: #374151;
      font-weight: 800;
      cursor: pointer;
    }

    .salary-checkline input {
      width: 18px;
      height: 18px;
      accent-color: var(--primary);
    }

    .salary-live-summary {
      grid-column: 1 / -1;
      padding: 14px;
      border-radius: 14px;
      border: 1px solid var(--border);
      background: #fff;
    }

    .salary-live-row {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding: 7px 0;
      border-bottom: 1px dashed #e5e7eb;
    }

    .salary-live-row:last-child {
      border-bottom: 0;
    }

    .salary-live-row span {
      font-size: 13px;
      font-weight: 800;
      color: var(--text-muted);
    }

    .salary-live-row strong {
      font-size: 13px;
      font-weight: 900;
      color: #111827;
      font-variant-numeric: tabular-nums;
    }

    .salary-save-row {
      grid-column: 1 / -1;
      display: flex;
      justify-content: flex-end;
    }

    .oc-table-wrap {
      overflow-x: auto;
    }

    .oc-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 10px;
      min-width: 1180px;
    }

    .oc-table thead th {
      font-size: 11px;
      font-weight: 900;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: .06em;
      padding: 8px 10px;
      border: 0;
      white-space: nowrap;
      text-align: left;
    }

    .oc-table thead th.text-right {
      text-align: right;
    }

    .oc-table tbody tr {
      background: #fff;
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .oc-table tbody td {
      padding: 12px 10px;
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
      background: #fff;
    }

    .oc-table tbody td:first-child {
      border-left: 1px solid var(--border);
      border-radius: 14px 0 0 14px;
    }

    .oc-table tbody td:last-child {
      border-right: 1px solid var(--border);
      border-radius: 0 14px 14px 0;
    }

    .text-right {
      text-align: right;
    }

    .money {
      font-variant-numeric: tabular-nums;
    }

    .salary-list-employee {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 240px;
    }

    .salary-list-avatar {
      width: 42px;
      height: 42px;
      object-fit: cover;
      border-radius: 999px;
      box-shadow: 0 0 0 3px #fff, 0 0 0 4px var(--border);
    }

    .salary-list-name {
      font-size: 14px;
      font-weight: 900;
      color: #111827;
      line-height: 1.2;
    }

    .salary-list-sub {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 3px;
      font-weight: 700;
    }

    .oc-empty {
      padding: 34px 18px !important;
      text-align: center;
      color: var(--text-muted);
      font-weight: 800;
    }

    .oc-pagination {
      margin-top: 18px;
      display: flex;
      justify-content: flex-end;
    }

    .salary-custom-modal {
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 18px;
    }

    .salary-custom-modal.is-open {
      display: flex;
    }

    .salary-modal-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, .58);
      backdrop-filter: blur(5px);
    }

    .salary-modal-panel {
      position: relative;
      width: min(920px, 100%);
      max-height: 90vh;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 24px;
      box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
      overflow: hidden;
      transform: translateY(12px) scale(.98);
      opacity: 0;
      transition: all .18s ease;
    }

    .salary-custom-modal.is-open .salary-modal-panel {
      transform: translateY(0) scale(1);
      opacity: 1;
    }

    .salary-modal-header {
      padding: 18px 20px;
      border-bottom: 1px solid var(--border);
      background: #fafafa;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .salary-modal-title-wrap {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
    }

    .salary-modal-icon {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      background: var(--blue-light);
      color: var(--blue);
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }

    .salary-modal-title {
      margin: 0;
      font-size: 17px;
      font-weight: 900;
      color: #111827;
      line-height: 1.25;
    }

    .salary-modal-subtitle {
      font-size: 12px;
      color: var(--text-muted);
      font-weight: 800;
      margin-top: 3px;
    }

    .salary-modal-close {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: #fff;
      color: #6b7280;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
    }

    .salary-modal-close:hover {
      color: #111827;
      background: #f3f4f6;
    }

    .salary-modal-body {
      padding: 20px;
      overflow: auto;
      max-height: calc(90vh - 150px);
    }

    .salary-modal-footer {
      padding: 14px 20px;
      border-top: 1px solid var(--border);
      background: #fafafa;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .details-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    @media(max-width:768px) {
      .details-grid {
        grid-template-columns: 1fr;
      }
    }

    .details-box {
      padding: 16px;
      border-radius: 16px;
      border: 1px solid var(--border);
      background: #fff;
    }

    .details-title {
      font-size: 12px;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: .05em;
      font-weight: 900;
      margin-bottom: 10px;
    }

    .details-line {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      padding: 8px 0;
      border-bottom: 1px dashed #e5e7eb;
      font-size: 13px;
    }

    .details-line:last-child {
      border-bottom: 0;
    }

    .details-line span {
      color: var(--text-muted);
      font-weight: 800;
    }

    .details-line strong {
      color: #111827;
      font-weight: 900;
      text-align: right;
    }

    @media(max-width:768px) {
      .oc-wrap {
        padding: 18px;
        margin: 0;
      }

      .oc-header {
        margin-top: 70px;
      }

      .oc-title {
        font-size: 21px;
      }

      .oc-card-body {
        padding: 14px;
      }
    }
  </style>
@endsection

@section('content')
  <div class="oc-wrap">
    @php
      $periodLabel = str_pad($period['month'], 2, '0', STR_PAD_LEFT) . '.' . $period['year'];
      $employeeCount = method_exists($employees, 'total') ? $employees->total() : $employees->count();
    @endphp

    <div class="oc-header">
      <div class="oc-titlebar">
        <div>
          <div class="oc-title">Lohn & Vollkosten</div>
          <div class="oc-sub">
            Mitarbeiterlöhne, Arbeitgeberkosten, Gemeinkosten und produktive EK-Stundensätze verwalten.
          </div>

          <div class="oc-breadcrumb">
            <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
            <span>›</span>
            <span class="current">Lohn / Vollkosten</span>
          </div>
        </div>
      </div>
    </div>

    <div class="oc-analytics">
      <div class="oc-stat">
        <div class="oc-stat-icon total">
          <i class="feather icon-users"></i>
        </div>
        <div>
          <div class="oc-stat-label">Mitarbeiter</div>
          <div class="oc-stat-value">{{ $employeeCount }}</div>
          <div class="oc-stat-sub">Im aktuellen Filter</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon period">
          <i class="feather icon-calendar"></i>
        </div>
        <div>
          <div class="oc-stat-label">Zeitraum</div>
          <div class="oc-stat-value">{{ $periodLabel }}</div>
          <div class="oc-stat-sub">Abrechnungsmonat</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon created">
          <i class="feather icon-plus-circle"></i>
        </div>
        <div>
          <div class="oc-stat-label">Neu</div>
          <div class="oc-stat-value">{{ $stats['created'] ?? 0 }}</div>
          <div class="oc-stat-sub">Neue Datensätze</div>
        </div>
      </div>

      <div class="oc-stat">
        <div class="oc-stat-icon updated">
          <i class="feather icon-refresh-cw"></i>
        </div>
        <div>
          <div class="oc-stat-label">Aktualisiert</div>
          <div class="oc-stat-value">{{ $stats['updated'] ?? 0 }}</div>
          <div class="oc-stat-sub">Geänderte Datensätze</div>
        </div>
      </div>
    </div>

    <div class="oc-card">
      <div class="oc-card-header">
        <div>
          <h3 class="oc-card-title">Filter & Ansicht</h3>
          <div class="oc-card-sub">Zeitraum und Mitarbeiter filtern</div>
        </div>

        <div class="oc-view-toggle">
          <button type="button" class="oc-btn-soft active" id="btnViewCards">
            <i class="feather icon-grid"></i>
            Cards
          </button>

          <button type="button" class="oc-btn-soft" id="btnViewList">
            <i class="feather icon-list"></i>
            Liste
          </button>
        </div>
      </div>

      <div class="oc-card-body">
        <form action="{{ route('salary.index') }}" method="GET" class="oc-filter-form">
          <div class="oc-form-group">
            <label class="oc-label">Mitarbeiter</label>
            <input type="text" name="search" placeholder="Mitarbeiter suchen..." value="{{ request('search') }}"
              class="oc-input">
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Monat</label>
            <select name="month" class="oc-select">
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ (int) $period['month'] === $m ? 'selected' : '' }}>
                  {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
              @endfor
            </select>
          </div>

          <div class="oc-form-group">
            <label class="oc-label">Jahr</label>
            <select name="year" class="oc-select">
              @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ (int) $period['year'] === $y ? 'selected' : '' }}>
                  {{ $y }}
                </option>
              @endfor
            </select>
          </div>

          <div class="oc-form-group">
            <button type="submit" class="oc-btn">
              <i class="feather icon-filter"></i>
              Filtern
            </button>
          </div>
        </form>
      </div>
    </div>

    <div id="viewCards">
      <div class="salary-grid">
        @forelse($employees as $emp)
          @php
            $s = $emp->salaries->first();

            $hourly = (float) data_get($s, 'base_hourly', 0);
            $weekly = (float) data_get($s, 'base_weekly', 0);
            $monthly = (float) data_get($s, 'base_monthly', 0);
            $yearly = (float) data_get($s, 'base_yearly', 0);

            $hpw = (int) data_get($s, 'working_hours_per_week', $emp->working_hour ?? 40);
            $wdpw = (int) data_get($s, 'working_days_per_week', 5);
            $ct = (string) data_get($s, 'contract_type', 'hourly');

            $taxed = (bool) data_get($s, 'is_taxed', true);
            $tIncome = (float) data_get($s, 'income_tax_rate_pct', 21.000);
            $tSocEmp = (float) data_get($s, 'social_rate_employee_pct', 19.700);
            $tSocEr = (float) data_get($s, 'social_rate_employer_pct', 20.450);

            $gross = (float) data_get($s, 'gross_monthly', $monthly);
            $net = (float) data_get($s, 'net_monthly', $monthly);
            $erTotal = (float) data_get($s, 'employer_total_monthly', $monthly);

            $overheadPct = (float) data_get($s, 'overhead_rate_pct', 0.0);
            $ekProd = data_get($s, 'ek_productive_hourly');
            $prodHoursM = data_get($s, 'productive_hours_period');
            $fullyLoaded = data_get($s, 'fully_loaded_total_monthly');

            $avatar = $emp->image
              ? asset('images/employee/' . $emp->image)
              : asset('images/default-avatar.png');

            $sheetId = data_get($s, 'id');
          @endphp

          <div class="salary-card" data-sheet-id="{{ $sheetId ?? '' }}" data-emp-id="{{ $emp->id }}"
            data-period-year="{{ $period['year'] }}" data-period-month="{{ $period['month'] }}" data-ct="{{ $ct }}"
            data-hpw="{{ $hpw }}" data-wdpw="{{ $wdpw }}" data-taxed="{{ $taxed ? 1 : 0 }}" data-income="{{ $tIncome }}"
            data-socemp="{{ $tSocEmp }}" data-socer="{{ $tSocEr }}" data-hourly="{{ $hourly }}" data-weekly="{{ $weekly }}"
            data-monthly="{{ $monthly }}" data-yearly="{{ $yearly }}" data-gross="{{ $gross }}" data-net="{{ $net }}"
            data-ertotal="{{ $erTotal }}" data-overhead="{{ $overheadPct }}"
            data-ekprod="{{ is_null($ekProd) ? '' : $ekProd }}"
            data-prodhoursm="{{ is_null($prodHoursM) ? '' : $prodHoursM }}"
            data-fullyloaded="{{ is_null($fullyLoaded) ? '' : $fullyLoaded }}">
            <div class="salary-card-top">
              <img src="{{ $avatar }}" alt="avatar" class="salary-avatar">

              <div class="salary-employee-name">
                {{ $emp->name }} {{ $emp->lastname }}
              </div>

              <div class="salary-period">{{ $periodLabel }}</div>

              <div class="salary-hour-label">Stundenlohn</div>
              <div class="salary-hour-value money" data-text="hourly">
                {{ number_format($hourly, 2, ',', '.') }} €
              </div>
            </div>

            <div class="salary-card-body">
              <div class="salary-main-cost">
                <div class="salary-main-cost-label">Monatlicher Lohn / Basis</div>
                <div class="salary-main-cost-value money" data-text="monthly">
                  {{ number_format($monthly, 2, ',', '.') }} €
                </div>

                <div class="oc-badge-row">
                  <span class="oc-badge blue">
                    AG:
                    <span class="money" data-text="ertotal">
                      {{ number_format($erTotal, 2, ',', '.') }} €
                    </span>
                  </span>

                  <span class="oc-badge warning">
                    GK:
                    <span class="money" data-text="overhead">
                      {{ number_format($overheadPct, 1, ',', '.') }}%
                    </span>
                  </span>

                  <span class="oc-badge primary">
                    EK €/h:
                    <span class="money" data-text="ekprod">
                      {{ is_null($ekProd) ? '—' : number_format((float) $ekProd, 2, ',', '.') . ' €' }}
                    </span>
                  </span>
                </div>
              </div>

              <div class="salary-mini-list">
                <div class="salary-mini-row">
                  <span>Vertragstyp</span>
                  <strong data-text="ct">{{ $ct }}</strong>
                </div>

                <div class="salary-mini-row">
                  <span>Std/Woche</span>
                  <strong data-text="hpw">{{ $hpw }}</strong>
                </div>

                <div class="salary-mini-row">
                  <span>Brutto / Monat</span>
                  <strong class="money" data-text="gross">{{ number_format($gross, 2, ',', '.') }} €</strong>
                </div>

                <div class="salary-mini-row">
                  <span>Netto / Monat</span>
                  <strong class="money" data-text="net">{{ number_format($net, 2, ',', '.') }} €</strong>
                </div>

                <div class="salary-mini-row">
                  <span>Vollkosten / Monat</span>
                  <strong class="money" data-text="fullyloaded">
                    {{ is_null($fullyLoaded) ? number_format($erTotal, 2, ',', '.') . ' €' : number_format((float) $fullyLoaded, 2, ',', '.') . ' €' }}
                  </strong>
                </div>

                <div class="salary-mini-row">
                  <span>Prod. Std / Monat</span>
                  <strong class="money" data-text="prodhoursm">
                    {{ is_null($prodHoursM) ? '—' : number_format((float) $prodHoursM, 2, ',', '.') }}
                  </strong>
                </div>
              </div>

              <div class="salary-actions">
                <button type="button" class="oc-btn-soft" data-action="toggle-edit">
                  <i class="feather icon-edit"></i>
                  Bearbeiten
                </button>

                <button type="button" class="oc-btn-blue" data-action="open-details">
                  <i class="feather icon-info"></i>
                  Details
                </button>

                <a class="oc-btn-soft" href="{{ url('next_employee/' . $emp->id) }}">
                  <i class="feather icon-user"></i>
                  Profil
                </a>
              </div>

              <div class="salary-edit-panel d-none" data-panel="edit">
                <div class="salary-edit-grid">
                  <div class="oc-form-group">
                    <label class="oc-label">Vertragstyp</label>
                    <select class="oc-select" data-input="ct">
                      <option value="monthly" {{ $ct === 'monthly' ? 'selected' : '' }}>Monatsgehalt</option>
                      <option value="hourly" {{ $ct === 'hourly' ? 'selected' : '' }}>Stundenlohn</option>
                      <option value="weekly" {{ $ct === 'weekly' ? 'selected' : '' }}>Wochenlohn</option>
                      <option value="yearly" {{ $ct === 'yearly' ? 'selected' : '' }}>Jahreslohn</option>
                    </select>
                  </div>

                  <div class="oc-form-group">
                    <label class="oc-label">Std/Woche</label>
                    <input type="number" class="oc-input" min="1" step="1" data-input="hpw" value="{{ $hpw }}">
                  </div>

                  <div class="oc-form-group">
                    <label class="oc-label">Arbeitstage/Woche</label>
                    <input type="number" class="oc-input" min="1" max="7" step="1" data-input="wdpw" value="{{ $wdpw }}">
                  </div>

                  <div class="oc-form-group">
                    <label class="oc-label">Gemeinkosten (%)</label>
                    <input type="number" class="oc-input" step="0.001" data-input="overhead"
                      value="{{ number_format($overheadPct, 3, '.', '') }}">
                  </div>

                  <div class="oc-form-group">
                    <label class="oc-label">Stundenlohn (€)</label>
                    <input type="number" class="oc-input" step="0.01" data-input="hourly"
                      value="{{ number_format($hourly, 2, '.', '') }}">
                  </div>

                  <div class="oc-form-group">
                    <label class="oc-label">Monatslohn (€)</label>
                    <input type="number" class="oc-input" step="0.01" data-input="monthly"
                      value="{{ number_format($monthly, 2, '.', '') }}">
                  </div>

                  <label class="salary-checkline" for="taxed-{{ $emp->id }}">
                    <input type="checkbox" id="taxed-{{ $emp->id }}" data-input="taxed" {{ $taxed ? 'checked' : '' }}>
                    Steuern / Abgaben berechnen
                  </label>

                  <div class="salary-tax-wrap" data-wrap="tax" {{ $taxed ? '' : 'style=display:none;' }}>
                    <div class="salary-tax-grid">
                      <div class="oc-form-group">
                        <label class="oc-label">Steuer (%)</label>
                        <input type="number" step="0.001" class="oc-input" data-input="income"
                          value="{{ number_format($tIncome, 3, '.', '') }}">
                      </div>

                      <div class="oc-form-group">
                        <label class="oc-label">Sozial AN (%)</label>
                        <input type="number" step="0.001" class="oc-input" data-input="socemp"
                          value="{{ number_format($tSocEmp, 3, '.', '') }}">
                      </div>

                      <div class="oc-form-group">
                        <label class="oc-label">Sozial AG (%)</label>
                        <input type="number" step="0.001" class="oc-input" data-input="socer"
                          value="{{ number_format($tSocEr, 3, '.', '') }}">
                      </div>
                    </div>
                  </div>

                  <div class="salary-live-summary">
                    <div class="salary-live-row">
                      <span>Brutto</span>
                      <strong class="money" data-live="gross">{{ number_format($gross, 2, ',', '.') }} €</strong>
                    </div>

                    <div class="salary-live-row">
                      <span>Abzüge AN</span>
                      <strong class="money" data-live="empded">0,00 €</strong>
                    </div>

                    <div class="salary-live-row">
                      <span>Netto</span>
                      <strong class="money" data-live="net">{{ number_format($net, 2, ',', '.') }} €</strong>
                    </div>

                    <div class="salary-live-row">
                      <span>AG-Beitrag</span>
                      <strong class="money" data-live="ercontrib">0,00 €</strong>
                    </div>

                    <div class="salary-live-row">
                      <span>AG-Gesamt</span>
                      <strong class="money" data-live="ertotal">{{ number_format($erTotal, 2, ',', '.') }} €</strong>
                    </div>

                    <div class="salary-live-row">
                      <span>Vollkosten mit GK</span>
                      <strong class="money" data-live="fullyloaded">—</strong>
                    </div>

                    <div class="salary-live-row">
                      <span>EK €/h produktiv</span>
                      <strong class="money" data-live="ekprod">—</strong>
                    </div>
                  </div>

                  <div class="salary-save-row">
                    <button type="button" class="oc-btn-success" data-action="save">
                      <i class="feather icon-save"></i>
                      Speichern
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="oc-card">
            <div class="oc-card-body oc-empty">
              <i class="feather icon-users"></i>
              Keine Mitarbeiter gefunden.
            </div>
          </div>
        @endforelse
      </div>

      <div class="oc-pagination">
        {{ $employees->links() }}
      </div>
    </div>

    <div id="viewList" class="d-none">
      <div class="oc-card">
        <div class="oc-card-header">
          <div>
            <h3 class="oc-card-title">Listenansicht</h3>
            <div class="oc-card-sub">Kompakte Übersicht aller Lohn- und Vollkostenwerte</div>
          </div>
        </div>

        <div class="oc-card-body">
          <div class="oc-table-wrap">
            <table class="oc-table">
              <thead>
                <tr>
                  <th>Mitarbeiter</th>
                  <th class="text-right">Std/Woche</th>
                  <th class="text-right">Vertrag</th>
                  <th class="text-right">Std-Lohn</th>
                  <th class="text-right">Basis/Monat</th>
                  <th class="text-right">AG/Monat</th>
                  <th class="text-right">GK</th>
                  <th class="text-right">Vollkosten</th>
                  <th class="text-right">Prod.Std</th>
                  <th class="text-right">EK €/h</th>
                  <th class="text-right">Aktion</th>
                </tr>
              </thead>

              <tbody>
                @forelse($employees as $emp)
                  @php
                    $s = $emp->salaries->first();

                    $avatar = $emp->image
                      ? asset('images/employee/' . $emp->image)
                      : asset('images/default-avatar.png');

                    $hourly = (float) data_get($s, 'base_hourly', 0);
                    $monthly = (float) data_get($s, 'base_monthly', 0);
                    $ct = (string) data_get($s, 'contract_type', 'hourly');
                    $hpw = (int) data_get($s, 'working_hours_per_week', $emp->working_hour ?? 40);

                    $erTotal = (float) data_get($s, 'employer_total_monthly', $monthly);

                    $overheadPct = (float) data_get($s, 'overhead_rate_pct', 0.0);
                    $fullyLoaded = data_get($s, 'fully_loaded_total_monthly');
                    $prodHoursM = data_get($s, 'productive_hours_period');
                    $ekProd = data_get($s, 'ek_productive_hourly');
                  @endphp

                  <tr>
                    <td>
                      <div class="salary-list-employee">
                        <img src="{{ $avatar }}" class="salary-list-avatar" alt="avatar">

                        <div>
                          <div class="salary-list-name">{{ $emp->name }} {{ $emp->lastname }}</div>
                          <div class="salary-list-sub">{{ $periodLabel }}</div>
                        </div>
                      </div>
                    </td>

                    <td class="text-right">{{ $hpw }}</td>
                    <td class="text-right">{{ ucfirst($ct) }}</td>
                    <td class="text-right money">{{ number_format($hourly, 2, ',', '.') }} €</td>
                    <td class="text-right money">{{ number_format($monthly, 2, ',', '.') }} €</td>
                    <td class="text-right money">{{ number_format($erTotal, 2, ',', '.') }} €</td>
                    <td class="text-right">{{ number_format($overheadPct, 1, ',', '.') }}%</td>

                    <td class="text-right money">
                      {{ is_null($fullyLoaded) ? '—' : number_format((float) $fullyLoaded, 2, ',', '.') . ' €' }}
                    </td>

                    <td class="text-right">
                      {{ is_null($prodHoursM) ? '—' : number_format((float) $prodHoursM, 2, ',', '.') }}
                    </td>

                    <td class="text-right money">
                      {{ is_null($ekProd) ? '—' : number_format((float) $ekProd, 2, ',', '.') . ' €' }}
                    </td>

                    <td class="text-right">
                      <a class="oc-btn-soft" href="{{ url('next_employee/' . $emp->id) }}">
                        <i class="feather icon-user"></i>
                        Profil
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="11" class="oc-empty">
                      Keine Mitarbeiter gefunden.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="oc-pagination">
            {{ $employees->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="salary-custom-modal" id="salaryDetailsModal" aria-hidden="true">
    <div class="salary-modal-backdrop" data-modal-close></div>

    <div class="salary-modal-panel" role="dialog" aria-modal="true" aria-labelledby="dTitle">
      <div class="salary-modal-header">
        <div class="salary-modal-title-wrap">
          <div class="salary-modal-icon">
            <i class="feather icon-info"></i>
          </div>

          <div>
            <h5 id="dTitle" class="salary-modal-title">Details</h5>
            <div class="salary-modal-subtitle">Arbeitszeiten, Monatskosten und produktiver EK-Stundensatz</div>
          </div>
        </div>

        <button type="button" class="salary-modal-close" data-modal-close aria-label="Schließen">
          <i class="feather icon-x"></i>
        </button>
      </div>

      <div class="salary-modal-body">
        <div class="details-grid">
          <div class="details-box">
            <div class="details-title">Arbeitszeiten</div>

            <div class="details-line">
              <span>Std/Woche</span>
              <strong id="dHPW"></strong>
            </div>

            <div class="details-line">
              <span>Std/Tag</span>
              <strong id="dHPD"></strong>
            </div>

            <div class="details-line">
              <span>Geplanter Monat</span>
              <strong id="dPlannedM"></strong>
            </div>

            <div class="details-line">
              <span>Prod. Std / Monat</span>
              <strong id="dProdM"></strong>
            </div>
          </div>

          <div class="details-box">
            <div class="details-title">Kosten</div>

            <div class="details-line">
              <span>Stundenlohn</span>
              <strong id="dBH"></strong>
            </div>

            <div class="details-line">
              <span>Monatslohn Basis</span>
              <strong id="dBM"></strong>
            </div>

            <div class="details-line">
              <span>Brutto / Monat</span>
              <strong id="dGross"></strong>
            </div>

            <div class="details-line">
              <span>Netto / Monat</span>
              <strong id="dNet"></strong>
            </div>

            <div class="details-line">
              <span>AG-Gesamt / Monat</span>
              <strong id="dER"></strong>
            </div>

            <div class="details-line">
              <span>Vollkosten / Monat</span>
              <strong id="dFL"></strong>
            </div>

            <div class="details-line">
              <span>EK €/h produktiv</span>
              <strong id="dEK"></strong>
            </div>
          </div>
        </div>
      </div>

      <div class="salary-modal-footer">
        <button type="button" class="oc-btn-soft" data-modal-close>
          Schließen
        </button>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <script>
    (function () {
      'use strict';

      const VIEW_KEY = 'salary_view_mode';

      const btnCards = document.getElementById('btnViewCards');
      const btnList = document.getElementById('btnViewList');
      const viewCards = document.getElementById('viewCards');
      const viewList = document.getElementById('viewList');

      function setMode(mode) {
        const cards = mode === 'cards';

        if (viewCards) viewCards.classList.toggle('d-none', !cards);
        if (viewList) viewList.classList.toggle('d-none', cards);

        if (btnCards) btnCards.classList.toggle('active', cards);
        if (btnList) btnList.classList.toggle('active', !cards);

        try {
          localStorage.setItem(VIEW_KEY, mode);
        } catch (e) { }
      }

      btnCards?.addEventListener('click', function () {
        setMode('cards');
      });

      btnList?.addEventListener('click', function () {
        setMode('list');
      });

      let initial = 'cards';

      try {
        initial = localStorage.getItem(VIEW_KEY) || 'cards';
      } catch (e) { }

      setMode(initial);
    })();
  </script>

  <script>
    (function () {
      'use strict';

      const UPSERT_URL = @json(route('salary_sheets.upsert'));
      const TAX_URL_TPL = @json(route('employees.tax_defaults', ['employee' => '__ID__']));

      const WEEKS_PER_YEAR = 52.1429;
      const MONTHS_PER_YEAR = 12;
      const AVG_WEEKS_PER_MONTH = WEEKS_PER_YEAR / MONTHS_PER_YEAR;

      const DEFAULT_INCOME = 21.000;
      const DEFAULT_SOCEMP = 19.700;
      const DEFAULT_SOCER = 20.450;

      const moneyFormatter = new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2
      });

      function fmtMoney(value) {
        return moneyFormatter.format(Number(value) || 0);
      }

      function fmtNumber(value, digits = 2) {
        return Number(value || 0).toFixed(digits).replace('.', ',');
      }

      function round2(value) {
        return Math.round((Number(value) + Number.EPSILON) * 100) / 100;
      }

      function round3(value) {
        return Math.round((Number(value) + Number.EPSILON) * 1000) / 1000;
      }

      function notify(message, isError = false) {
        if (typeof toastr !== 'undefined') {
          isError ? toastr.error(message) : toastr.success(message);
          return;
        }

        alert(message);
      }

      function openCustomModal(modal) {
        if (!modal) return;

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        const closeBtn = modal.querySelector('[data-modal-close]');
        if (closeBtn) {
          setTimeout(() => closeBtn.focus(), 50);
        }
      }

      function closeCustomModal(modal) {
        if (!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }

      function getState(card) {
        return {
          sheet_id: card.dataset.sheetId || null,
          emp_id: Number(card.dataset.empId),
          period_year: Number(card.dataset.periodYear),
          period_month: Number(card.dataset.periodMonth),

          ct: card.dataset.ct || 'hourly',
          hpw: Number(card.dataset.hpw) || 40,
          wdpw: Number(card.dataset.wdpw) || 5,

          taxed: Number(card.dataset.taxed) === 1,
          income: parseFloat(card.dataset.income ?? DEFAULT_INCOME),
          socemp: parseFloat(card.dataset.socemp ?? DEFAULT_SOCEMP),
          socer: parseFloat(card.dataset.socer ?? DEFAULT_SOCER),

          hourly: parseFloat(card.dataset.hourly ?? '0'),
          weekly: parseFloat(card.dataset.weekly ?? '0'),
          monthly: parseFloat(card.dataset.monthly ?? '0'),
          yearly: parseFloat(card.dataset.yearly ?? '0'),

          gross: parseFloat(card.dataset.gross ?? card.dataset.monthly ?? '0'),
          net: parseFloat(card.dataset.net ?? card.dataset.monthly ?? '0'),
          ertotal: parseFloat(card.dataset.ertotal ?? card.dataset.monthly ?? '0'),

          overhead: parseFloat(card.dataset.overhead ?? '0'),
          fullyloaded: parseFloat(card.dataset.fullyloaded ?? '0') || null,
          prodhoursm: parseFloat(card.dataset.prodhoursm ?? '0') || null,
          ekprod: parseFloat(card.dataset.ekprod ?? '0') || null,
        };
      }

      function bindInputs(card) {
        return {
          ct: card.querySelector('[data-input="ct"]'),
          hpw: card.querySelector('[data-input="hpw"]'),
          wdpw: card.querySelector('[data-input="wdpw"]'),
          overhead: card.querySelector('[data-input="overhead"]'),
          hourly: card.querySelector('[data-input="hourly"]'),
          monthly: card.querySelector('[data-input="monthly"]'),
          taxed: card.querySelector('[data-input="taxed"]'),
          income: card.querySelector('[data-input="income"]'),
          socemp: card.querySelector('[data-input="socemp"]'),
          socer: card.querySelector('[data-input="socer"]'),
        };
      }

      function showEdit(card, show) {
        const panel = card.querySelector('[data-panel="edit"]');
        const btn = card.querySelector('[data-action="toggle-edit"]');

        if (panel) panel.classList.toggle('d-none', !show);

        if (btn) {
          btn.innerHTML = show
            ? '<i class="feather icon-x"></i> Abbrechen'
            : '<i class="feather icon-edit"></i> Bearbeiten';
        }

        if (window.feather) {
          window.feather.replace();
        }
      }

      function deriveAll(state) {
        const hpw = Math.max(1, parseInt(state.hpw || 40, 10));

        let hourly = state.hourly;
        let weekly = state.weekly;
        let monthly = state.monthly;
        let yearly = state.yearly;

        if (state.ct === 'hourly' && hourly > 0) {
          weekly = hourly * hpw;
          monthly = weekly * AVG_WEEKS_PER_MONTH;
          yearly = monthly * MONTHS_PER_YEAR;
        } else if (state.ct === 'weekly' && weekly > 0) {
          hourly = hpw ? weekly / hpw : 0;
          monthly = weekly * AVG_WEEKS_PER_MONTH;
          yearly = monthly * MONTHS_PER_YEAR;
        } else if (state.ct === 'monthly' && monthly > 0) {
          weekly = monthly / AVG_WEEKS_PER_MONTH;
          hourly = hpw ? weekly / hpw : 0;
          yearly = monthly * MONTHS_PER_YEAR;
        } else if (state.ct === 'yearly' && yearly > 0) {
          monthly = yearly / MONTHS_PER_YEAR;
          weekly = monthly / AVG_WEEKS_PER_MONTH;
          hourly = hpw ? weekly / hpw : 0;
        }

        state.hourly = round2(hourly);
        state.weekly = round2(weekly);
        state.monthly = round2(monthly);
        state.yearly = round2(yearly);

        return state;
      }

      function applyTax(state) {
        const base = Number(state.monthly) || 0;

        let empDed = 0;
        let net = base;
        let erContrib = 0;
        let erTotal = base;

        if (state.taxed && base > 0) {
          empDed = base * ((Number(state.income) + Number(state.socemp)) / 100);
          net = base - empDed;
          erContrib = base * (Number(state.socer) / 100);
          erTotal = base + erContrib;
        }

        state.gross = round2(base);
        state.empded = round2(empDed);
        state.net = round2(net);
        state.ercontrib = round2(erContrib);
        state.ertotal = round2(erTotal);

        return state;
      }

      function applyOverheadAndEk(state) {
        const overheadRate = Math.max(0, Number(state.overhead || 0)) / 100;
        const planned = round3((Number(state.hpw) || 0) * AVG_WEEKS_PER_MONTH);
        const hoursPerDay = Number(state.wdpw || 5) ? ((Number(state.hpw) || 0) / Number(state.wdpw || 5)) : 0;

        const fallbackProductiveHours = round3(planned * 0.80);
        const baseEmployer = Number(state.ertotal || 0);
        const fullyLoaded = baseEmployer + (baseEmployer * overheadRate);

        state.prodhoursm = state.prodhoursm ?? fallbackProductiveHours;
        state.fullyloaded = state.fullyloaded ?? round2(fullyLoaded);
        state.ekprod = state.ekprod ?? (state.prodhoursm > 0 ? state.fullyloaded / state.prodhoursm : null);

        state._plannedMonth = planned;
        state._hoursPerDay = round3(hoursPerDay);

        return state;
      }

      function setText(card, selector, value) {
        const element = card.querySelector(selector);
        if (element) element.textContent = value;
      }

      function renderCard(card, state) {
        setText(card, '[data-text="hourly"]', fmtMoney(state.hourly));
        setText(card, '[data-text="monthly"]', fmtMoney(state.monthly));
        setText(card, '[data-text="ct"]', state.ct);
        setText(card, '[data-text="hpw"]', state.hpw);
        setText(card, '[data-text="gross"]', fmtMoney(state.gross));
        setText(card, '[data-text="net"]', fmtMoney(state.net));
        setText(card, '[data-text="ertotal"]', fmtMoney(state.ertotal));
        setText(card, '[data-text="overhead"]', fmtNumber(state.overhead, 1) + '%');
        setText(card, '[data-text="fullyloaded"]', state.fullyloaded == null ? '—' : fmtMoney(state.fullyloaded));
        setText(card, '[data-text="prodhoursm"]', state.prodhoursm == null ? '—' : fmtNumber(state.prodhoursm, 2));
        setText(card, '[data-text="ekprod"]', state.ekprod == null ? '—' : fmtMoney(state.ekprod));

        setText(card, '[data-live="gross"]', fmtMoney(state.gross));
        setText(card, '[data-live="empded"]', fmtMoney(state.empded || 0));
        setText(card, '[data-live="net"]', fmtMoney(state.net));
        setText(card, '[data-live="ercontrib"]', fmtMoney(state.ercontrib || 0));
        setText(card, '[data-live="ertotal"]', fmtMoney(state.ertotal));
        setText(card, '[data-live="fullyloaded"]', state.fullyloaded == null ? '—' : fmtMoney(state.fullyloaded));
        setText(card, '[data-live="ekprod"]', state.ekprod == null ? '—' : fmtMoney(state.ekprod));

        card.dataset.ct = state.ct;
        card.dataset.hpw = state.hpw;
        card.dataset.wdpw = state.wdpw;
        card.dataset.taxed = state.taxed ? 1 : 0;
        card.dataset.income = state.income;
        card.dataset.socemp = state.socemp;
        card.dataset.socer = state.socer;

        card.dataset.hourly = state.hourly;
        card.dataset.weekly = state.weekly;
        card.dataset.monthly = state.monthly;
        card.dataset.yearly = state.yearly;

        card.dataset.gross = state.gross;
        card.dataset.net = state.net;
        card.dataset.ertotal = state.ertotal;

        card.dataset.overhead = state.overhead ?? 0;
        card.dataset.fullyloaded = state.fullyloaded ?? '';
        card.dataset.prodhoursm = state.prodhoursm ?? '';
        card.dataset.ekprod = state.ekprod ?? '';
      }

      function rebuildFromInputs(card) {
        const state = getState(card);
        const inputs = bindInputs(card);

        state.ct = inputs.ct?.value || state.ct;
        state.hpw = parseInt(inputs.hpw?.value || state.hpw || '40', 10);
        state.wdpw = parseInt(inputs.wdpw?.value || state.wdpw || '5', 10);
        state.overhead = parseFloat(inputs.overhead?.value || state.overhead || '0');

        const hourlyInput = parseFloat(inputs.hourly?.value || '0');
        const monthlyInput = parseFloat(inputs.monthly?.value || '0');

        if (state.ct === 'hourly') {
          state.hourly = hourlyInput;
        } else if (state.ct === 'monthly') {
          state.monthly = monthlyInput;
        } else if (state.ct === 'weekly') {
          state.weekly = monthlyInput > 0 ? monthlyInput / AVG_WEEKS_PER_MONTH : 0;
        } else if (state.ct === 'yearly') {
          state.yearly = monthlyInput * MONTHS_PER_YEAR;
        }

        state.taxed = !!inputs.taxed?.checked;
        state.income = parseFloat(inputs.income?.value || DEFAULT_INCOME);
        state.socemp = parseFloat(inputs.socemp?.value || DEFAULT_SOCEMP);
        state.socer = parseFloat(inputs.socer?.value || DEFAULT_SOCER);

        deriveAll(state);
        applyTax(state);

        state.fullyloaded = null;
        state.prodhoursm = null;
        state.ekprod = null;

        applyOverheadAndEk(state);
        renderCard(card, state);
      }

      async function loadTaxDefaults(card) {
        const empId = card.dataset.empId;
        if (!empId) return;

        const url = TAX_URL_TPL.replace('__ID__', empId);

        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json'
          }
        });

        if (!response.ok) {
          throw new Error('HTTP ' + response.status);
        }

        const json = await response.json();
        const inputs = bindInputs(card);

        if (inputs.income) inputs.income.value = Number(json.income_tax_rate_pct ?? DEFAULT_INCOME).toFixed(3);
        if (inputs.socemp) inputs.socemp.value = Number(json.social_rate_employee_pct ?? DEFAULT_SOCEMP).toFixed(3);
        if (inputs.socer) inputs.socer.value = Number(json.social_rate_employer_pct ?? DEFAULT_SOCER).toFixed(3);

        if (inputs.taxed && !inputs.taxed.checked) {
          inputs.taxed.checked = true;

          const wrap = card.querySelector('[data-wrap="tax"]');
          if (wrap) wrap.style.display = '';
        }

        rebuildFromInputs(card);
      }

      async function saveCard(card) {
        const state = getState(card);

        deriveAll(state);
        applyTax(state);

        const overhead = parseFloat(card.dataset.overhead || '0') || 0;

        const payload = {
          sheet_id: card.dataset.sheetId || null,
          emp_id: state.emp_id,
          period_year: state.period_year,
          period_month: state.period_month,

          contract_type: state.ct,
          working_hours_per_week: state.hpw,
          working_days_per_week: state.wdpw,

          base_hourly: state.hourly.toFixed(2),
          base_weekly: state.weekly.toFixed(2),
          base_monthly: state.monthly.toFixed(2),
          base_yearly: state.yearly.toFixed(2),

          is_taxed: state.taxed ? 1 : 0,
          tax_source: 'employee_profile',
          income_tax_rate_pct: state.income,
          social_rate_employee_pct: state.socemp,
          social_rate_employer_pct: state.socer,

          gross_monthly: state.gross,
          employee_deductions_monthly: state.empded || 0,
          net_monthly: state.net,
          employer_contrib_monthly: state.ercontrib || 0,
          employer_total_monthly: state.ertotal,

          overhead_rate_pct: overhead,
          currency: 'EUR'
        };

        const response = await fetch(UPSERT_URL, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token()),
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        if (!response.ok) {
          throw new Error('HTTP ' + response.status);
        }

        const json = await response.json();

        if (json?.sheet_id) {
          card.dataset.sheetId = json.sheet_id;
        }

        if (json?.data) {
          const data = json.data;

          card.dataset.ct = data.contract_type ?? card.dataset.ct;
          card.dataset.hpw = data.working_hours_per_week ?? card.dataset.hpw;
          card.dataset.wdpw = data.working_days_per_week ?? card.dataset.wdpw;

          card.dataset.hourly = data.base_hourly ?? card.dataset.hourly;
          card.dataset.weekly = data.base_weekly ?? card.dataset.weekly;
          card.dataset.monthly = data.base_monthly ?? card.dataset.monthly;
          card.dataset.yearly = data.base_yearly ?? card.dataset.yearly;

          card.dataset.gross = data.gross_monthly ?? card.dataset.gross;
          card.dataset.net = data.net_monthly ?? card.dataset.net;
          card.dataset.ertotal = data.employer_total_monthly ?? card.dataset.ertotal;

          if (data.overhead_rate_pct != null) card.dataset.overhead = data.overhead_rate_pct;
          if (data.fully_loaded_total_monthly != null) card.dataset.fullyloaded = data.fully_loaded_total_monthly;
          if (data.productive_hours_period != null) card.dataset.prodhoursm = data.productive_hours_period;
          if (data.ek_productive_hourly != null) card.dataset.ekprod = data.ek_productive_hourly;
        }

        const freshState = getState(card);

        deriveAll(freshState);
        applyTax(freshState);
        applyOverheadAndEk(freshState);
        renderCard(card, freshState);

        showEdit(card, false);
        notify('Gespeichert');
      }

      function openDetails(card) {
        const state = getState(card);

        deriveAll(state);
        applyTax(state);
        applyOverheadAndEk(state);

        const hoursPerDay = state.wdpw ? state.hpw / state.wdpw : state.hpw;
        const plannedMonth = state._plannedMonth ?? round3(state.hpw * AVG_WEEKS_PER_MONTH);

        const name = card.querySelector('.salary-employee-name')?.textContent.trim() || 'Mitarbeiter';

        document.getElementById('dTitle').textContent = 'Details – ' + name;
        document.getElementById('dHPW').textContent = state.hpw;
        document.getElementById('dHPD').textContent = fmtNumber(hoursPerDay, 2);
        document.getElementById('dPlannedM').textContent = fmtNumber(plannedMonth, 2);
        document.getElementById('dProdM').textContent = state.prodhoursm == null ? '—' : fmtNumber(state.prodhoursm, 2);

        document.getElementById('dBH').textContent = fmtMoney(state.hourly);
        document.getElementById('dBM').textContent = fmtMoney(state.monthly);
        document.getElementById('dGross').textContent = fmtMoney(state.gross);
        document.getElementById('dNet').textContent = fmtMoney(state.net);
        document.getElementById('dER').textContent = fmtMoney(state.ertotal);
        document.getElementById('dFL').textContent = state.fullyloaded == null ? '—' : fmtMoney(state.fullyloaded);
        document.getElementById('dEK').textContent = state.ekprod == null ? '—' : fmtMoney(state.ekprod);

        openCustomModal(document.getElementById('salaryDetailsModal'));

        if (window.feather) {
          window.feather.replace();
        }
      }

      document.addEventListener('DOMContentLoaded', function () {
        const salaryModal = document.getElementById('salaryDetailsModal');

        document.querySelectorAll('[data-modal-close]').forEach(function (button) {
          button.addEventListener('click', function () {
            closeCustomModal(salaryModal);
          });
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && salaryModal?.classList.contains('is-open')) {
            closeCustomModal(salaryModal);
          }
        });

        document.querySelectorAll('.salary-card').forEach(function (card) {
          const state = getState(card);

          deriveAll(state);
          applyTax(state);
          applyOverheadAndEk(state);
          renderCard(card, state);

          const inputs = bindInputs(card);

          card.querySelector('[data-action="toggle-edit"]')?.addEventListener('click', async function () {
            const panel = card.querySelector('[data-panel="edit"]');
            const becomingVisible = panel?.classList.contains('d-none');

            showEdit(card, becomingVisible);

            if (becomingVisible) {
              const taxed = card.querySelector('[data-input="taxed"]');

              if (taxed && taxed.checked) {
                try {
                  await loadTaxDefaults(card);
                } catch (e) { }
              }

              rebuildFromInputs(card);
            }
          });

          inputs.taxed?.addEventListener('change', async function () {
            const wrap = card.querySelector('[data-wrap="tax"]');

            if (wrap) {
              wrap.style.display = inputs.taxed.checked ? '' : 'none';
            }

            if (inputs.taxed.checked) {
              try {
                await loadTaxDefaults(card);
              } catch (e) { }
            }

            rebuildFromInputs(card);
          });

          [
            inputs.ct,
            inputs.hpw,
            inputs.wdpw,
            inputs.overhead,
            inputs.hourly,
            inputs.monthly,
            inputs.income,
            inputs.socemp,
            inputs.socer
          ].forEach(function (input) {
            if (!input) return;

            input.addEventListener('input', function () {
              rebuildFromInputs(card);
            });

            input.addEventListener('change', function () {
              rebuildFromInputs(card);
            });
          });

          card.querySelector('[data-action="open-details"]')?.addEventListener('click', function () {
            openDetails(card);
          });

          card.querySelector('[data-action="save"]')?.addEventListener('click', async function () {
            try {
              await saveCard(card);
            } catch (error) {
              console.error(error);
              notify('Speichern fehlgeschlagen', true);
            }
          });
        });

        if (window.feather) {
          window.feather.replace();
        }
      });
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
        label: 'Mitarbeiterliste',
        url: "{{ url('emp?status_tab=active') }}"
      },
      {
        label: 'Lohn / Vollkosten',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush