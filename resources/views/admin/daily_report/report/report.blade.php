@extends('admin.layouts.app')
@section('title')
  Tagesbericht
@endsection

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/calendar.css')}}">

  <style>
    :root {
      --dr-bg: #f3f4f6;
      --dr-card: #ffffff;
      --dr-text: #111827;
      --dr-muted: #6b7280;
      --dr-border: #e5e7eb;
      --dr-primary: #93c21c;
      --dr-primary-hover: #7baa18;
      --dr-primary-soft: #f4fae7;
      --dr-blue: #74b2d4;
      --dr-blue-soft: #eff6ff;
      --dr-success: #10b981;
      --dr-success-soft: #ecfdf5;
      --dr-warning: #f59e0b;
      --dr-warning-soft: #fffbeb;
      --dr-danger: #ef4444;
      --dr-danger-soft: #fef2f2;
      --dr-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
      --dr-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
      --dr-radius: 16px;
      --dr-transition: all .2s ease-in-out;
    }

    .daily-report-modern {
      font-family: Inter, system-ui, -apple-system, sans-serif;
      color: var(--dr-text);  
    }

    .daily-report-modern * {
      box-sizing: border-box;
    }

    .daily-report-modern .content-wrapper {
      padding: 1.2rem 1.4rem;
    }



    /* Voice input final behavior: first show only Play; while recording show only Stop and live text under field */
    .dr-voice-wrap:not(.is-listening) .dr-voice-stop-btn {
      display: none !important;
    }

    .dr-voice-wrap.is-listening .dr-voice-play-btn {
      display: none !important;
    }

    .dr-voice-wrap.is-listening .dr-voice-stop-btn {
      display: inline-flex !important;
      opacity: 1;
      cursor: pointer;
      background: #dc2626;
      color: #fff;
      animation: drVoicePulse 1.1s infinite;
    }

    .dr-voice-status {
      display: none;
      width: 100%;
      margin-top: 8px;
      padding: 9px 11px;
      border-radius: 12px;
      background: #f9fafb;
      border: 1px dashed #d1d5db;
      color: #374151;
      font-size: 12px;
      font-weight: 800;
      line-height: 1.45;
      white-space: pre-wrap;
      word-break: break-word;
    }

    .dr-voice-status.is-visible,
    .dr-voice-wrap.is-listening .dr-voice-status {
      display: block;
    }

    .dr-voice-status::before {
      content: 'Live erkannt: ';
      color: #6b7280;
      font-weight: 900;
    }

    .dr-voice-status.is-empty::before {
      content: '';
    }

    @media(max-width: 760px) {
      .daily-report-modern .content-wrapper {
        padding: 0.8rem;
      }
    }

    /* --- Tabs Navigation --- */
    .dr-tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 24px;
      border-bottom: 2px solid var(--dr-border);
      padding-bottom: 12px;
      overflow-x: auto;
    }

    .dr-tab-btn {
      background: transparent;
      border: none;
      font-size: 16px;
      font-weight: 800;
      color: var(--dr-muted);
      cursor: pointer;
      padding: 10px 20px;
      border-radius: 8px;
      transition: var(--dr-transition);
      display: flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
      outline: none;
    }

    .dr-tab-btn:hover {
      background: var(--dr-border);
      color: var(--dr-text);
    }

    .dr-tab-btn.active {
      background: var(--dr-primary-soft);
      color: var(--dr-primary);
    }

    .tab-pane {
      display: none;
      animation: fadeIn 0.3s ease;
    }

    .tab-pane.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(5px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* --- Animated Remaining Time Dashboard --- */
    .remaining-time-card {
      background: #111827;
      color: white;
      padding: 24px;
      border-radius: var(--dr-radius);
      margin-bottom: 24px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      position: relative;
      overflow: hidden;
      transition: all 0.3s;
    }

    .remaining-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .remaining-title {
      font-size: 18px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .remaining-amount {
      font-size: 28px;
      font-weight: 900;
      color: var(--dr-primary);
    }

    .progress-track {
      background: #374151;
      height: 14px;
      border-radius: 8px;
      width: 100%;
      position: relative;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      background: var(--dr-primary);
      width: 100%;
      border-radius: 8px;
      transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s;
      position: relative;
    }

    .progress-fill::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      animation: progress-pulse 2s infinite;
    }

    @keyframes progress-pulse {
      0% {
        transform: translateX(-100%);
      }

      100% {
        transform: translateX(100%);
      }
    }

    /* --- Calendar Grid View (Tab 2) --- */
    .calendar-container {
      background: #fff;
      border: 1px solid var(--dr-border);
      border-radius: var(--dr-radius);
      padding: 24px;
      box-shadow: var(--dr-shadow-sm);
    }

    .calendar-header {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 10px;
      margin-bottom: 10px;
      text-align: center;
      font-weight: 900;
      color: var(--dr-muted);
      font-size: 12px;
      text-transform: uppercase;
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 10px;
    }

    .cal-day {
      border: 1px solid var(--dr-border);
      border-radius: 12px;
      padding: 12px;
      min-height: 100px;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s;
      cursor: pointer;
    }

    .cal-day:hover {
      transform: translateY(-3px);
      box-shadow: var(--dr-shadow-sm);
      border-color: var(--dr-primary);
    }

    .cal-day .date-num {
      font-size: 18px;
      font-weight: 900;
      margin-bottom: auto;
    }

    .cal-day.empty {
      border: none;
      background: transparent;
      cursor: default;
      box-shadow: none;
    }

    .cal-day.empty:hover {
      transform: none;
      border: none;
    }

    .cal-status {
      font-size: 11px;
      font-weight: 800;
      padding: 4px 8px;
      border-radius: 6px;
      display: inline-block;
      margin-top: 8px;
      text-align: center;
    }

    .cal-day.reported .cal-status {
      background: var(--dr-success-soft);
      color: var(--dr-success);
    }

    .cal-day.missing .cal-status {
      background: var(--dr-danger-soft);
      color: var(--dr-danger);
    }

    .cal-day.weekend {
      background: #f9fafb;
      color: #9ca3af;
    }

    .cal-day.future {
      opacity: 0.6;
    }

    @media(max-width: 760px) {

      .calendar-grid,
      .calendar-header {
        gap: 4px;
      }

      .cal-day {
        min-height: 70px;
        padding: 6px;
      }

      .cal-status {
        font-size: 9px;
        padding: 2px 4px;
      }
    }

    /* --- Headers & Layout --- */
    .dr-header {
      margin-bottom: 18px;
    }

    .dr-titlebar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 14px;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }

    .dr-title {
      font-size: 26px;
      font-weight: 900;
      letter-spacing: -.025em;
      color: #111827;
      text-transform: uppercase;
    }

    .dr-sub {
      font-size: 14px;
      color: var(--dr-muted);
      margin-top: 4px;
    }

    .dr-breadcrumb {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
      font-size: 13px;
      color: var(--dr-muted);
    }

    .dr-breadcrumb a {
      color: var(--dr-muted);
      text-decoration: none;
      font-weight: 800;
    }

    .dr-breadcrumb a:hover {
      color: var(--dr-text);
    }

    .dr-breadcrumb span.current {
      color: #111827;
      font-weight: 900;
    }

    .dr-btn {
      background: var(--dr-primary);
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: var(--dr-transition);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      min-height: 42px;
    }

    .dr-btn:hover {
      background: var(--dr-primary-hover);
      color: #fff;
    }

    .dr-btn-soft {
      background: #fff;
      color: var(--dr-text);
      border: 1px solid var(--dr-border);
      padding: 10px 14px;
      border-radius: 10px;
      font-weight: 800;
      cursor: pointer;
      transition: var(--dr-transition);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 42px;
    }

    .dr-btn-soft:hover {
      background: #f9fafb;
      color: var(--dr-text);
      border-color: #d1d5db;
    }

    .dr-analytics {
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 14px;
      margin-bottom: 18px;
    }

    @media(max-width:1200px) {
      .dr-analytics {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media(max-width:700px) {
      .dr-analytics {
        grid-template-columns: 1fr;
      }
    }

    .dr-stat {
      background: var(--dr-card);
      border: 1px solid var(--dr-border);
      border-radius: 16px;
      padding: 16px;
      box-shadow: var(--dr-shadow-sm);
      display: flex;
      align-items: center;
      gap: 12px;
      min-height: 92px;
    }

    .dr-stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }

    .dr-stat-icon.total {
      background: var(--dr-blue-soft);
      color: var(--dr-blue);
    }

    .dr-stat-icon.worked {
      background: var(--dr-success-soft);
      color: var(--dr-success);
    }

    .dr-stat-icon.missing {
      background: var(--dr-danger-soft);
      color: var(--dr-danger);
    }

    .dr-stat-icon.expected {
      background: var(--dr-warning-soft);
      color: #d97706;
    }

    .dr-stat-meta {
      min-width: 0;
    }

    .dr-stat-label {
      font-size: 11px;
      font-weight: 900;
      color: var(--dr-muted);
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .dr-stat-value {
      font-size: 24px;
      font-weight: 900;
      color: #111827;
      line-height: 1.1;
      margin-top: 4px;
    }

    .dr-stat-sub {
      font-size: 12px;
      color: var(--dr-muted);
      margin-top: 4px;
    }

    /* Month Analytics Details */
    .dr-month-analytics {
      background: #fff;
      border: 1px solid var(--dr-border);
      border-radius: 16px;
      box-shadow: var(--dr-shadow-sm);
      margin-bottom: 18px;
      overflow: hidden;
    }

    .dr-month-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      padding: 14px 16px;
      border-bottom: 1px solid var(--dr-border);
      background: #fafafa;
    }

    .dr-month-title {
      font-size: 15px;
      font-weight: 900;
      color: #111827;
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
    }

    .dr-month-sub {
      font-size: 12px;
      color: var(--dr-muted);
      margin-top: 3px;
    }

    .dr-month-body {
      padding: 16px;
      display: grid;
      grid-template-columns: 260px minmax(0, 1fr);
      gap: 16px;
      align-items: center;
    }

    .dr-month-score {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px;
      border: 1px solid var(--dr-border);
      border-radius: 14px;
      background: #f9fafb;
    }

    .dr-month-ring {
      --p: 0;
      width: 92px;
      height: 92px;
      border-radius: 50%;
      background: conic-gradient(var(--dr-primary) calc(var(--p) * 1%), #e5e7eb 0);
      display: grid;
      place-items: center;
      flex: 0 0 auto;
    }

    .dr-month-ring::before {
      content: attr(data-value);
      width: 68px;
      height: 68px;
      border-radius: 50%;
      background: #fff;
      display: grid;
      place-items: center;
      font-size: 18px;
      font-weight: 900;
      color: #111827;
      box-shadow: inset 0 0 0 1px var(--dr-border);
    }

    .dr-month-kpis {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 10px;
    }

    .dr-month-kpi {
      border: 1px solid var(--dr-border);
      border-radius: 14px;
      padding: 12px;
      background: #fff;
    }

    .dr-month-kpi .label {
      display: block;
      font-size: 11px;
      font-weight: 900;
      color: var(--dr-muted);
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .dr-month-kpi .value {
      display: block;
      margin-top: 5px;
      font-size: 22px;
      font-weight: 900;
      line-height: 1;
      color: #111827;
    }

    .dr-month-kpi .hint {
      display: block;
      margin-top: 5px;
      font-size: 12px;
      color: var(--dr-muted);
    }

    .dr-month-days {
      display: grid;
      grid-template-columns: repeat(15, minmax(0, 1fr));
      gap: 5px;
      margin-top: 14px;
    }

    .dr-month-day {
      height: 28px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 900;
      border: 1px solid var(--dr-border);
      color: #6b7280;
      background: #f9fafb;
      cursor: pointer;
    }

    .dr-month-day.done {
      background: var(--dr-success-soft);
      border-color: rgba(16, 185, 129, .35);
      color: #047857;
    }

    .dr-month-day.missing {
      background: var(--dr-danger-soft);
      border-color: rgba(239, 68, 68, .28);
      color: #b91c1c;
    }

    .dr-month-day.future {
      background: #f3f4f6;
      color: #9ca3af;
    }

    .dr-month-day.off {
      background: #fff;
      color: #cbd5e1;
      border-style: dashed;
    }

    @media(max-width:1100px) {
      .dr-month-body {
        grid-template-columns: 1fr;
      }

      .dr-month-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .dr-month-days {
        grid-template-columns: repeat(10, minmax(0, 1fr));
      }
    }

    @media(max-width:600px) {
      .dr-month-kpis {
        grid-template-columns: 1fr;
      }

      .dr-month-days {
        grid-template-columns: repeat(7, minmax(0, 1fr));
      }
    }

    /* Toolbar */
    .dr-toolbar {
      background: var(--dr-card);
      border: 1px solid var(--dr-border);
      border-radius: var(--dr-radius);
      padding: 14px 16px;
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 16px;
      box-shadow: var(--dr-shadow-sm);
    }

    .dr-toolbar-left,
    .dr-toolbar-right {
      display: flex;
      align-items: flex-end;
      gap: 12px;
      flex-wrap: wrap;
    }

    .dr-toolbar-left {
      flex: 1;
    }

    .dr-filter-block {
      display: flex;
      flex-direction: column;
      gap: 6px;
      min-width: 180px;
    }

    .dr-filter-block.grow {
      flex: 1;
      min-width: 260px;
    }

    .dr-filter-label {
      font-size: 11px;
      font-weight: 900;
      color: var(--dr-muted);
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .dr-select,
    .dr-input {
      width: 100%;
      background: #f9fafb;
      border: 1px solid var(--dr-border);
      border-radius: 10px;
      padding: 10px 12px;
      font-size: 14px;
      outline: none;
      transition: var(--dr-transition);
      min-height: 42px;
    }

    .dr-select:focus,
    .dr-input:focus {
      background: #fff;
      border-color: var(--dr-primary);
      box-shadow: 0 0 0 3px var(--dr-primary-soft);
    }

    /* Weekly Ribbon */
    .dr-week-card {
      background: #fff;
      border: 1px solid var(--dr-border);
      border-radius: 16px;
      box-shadow: var(--dr-shadow-sm);
      overflow: hidden;
      margin-bottom: 16px;
    }

    .dr-week-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      padding: 14px 16px;
      border-bottom: 1px solid var(--dr-border);
      background: #fafafa;
    }

    .dr-week-title {
      font-size: 14px;
      font-weight: 900;
      color: #111827;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .dr-week-body {
      padding: 16px;
      display: grid;
      grid-template-columns: minmax(0, 1fr) 190px;
      gap: 14px;
      align-items: stretch;
    }

    @media(max-width:992px) {
      .dr-week-body {
        grid-template-columns: 1fr;
      }
    }

    .daily_report_row {
      display: grid !important;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 12px;
      width: 100%;
    }

    @media(max-width:1280px) {
      .daily_report_row {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media(max-width:760px) {
      .daily_report_row {
        grid-template-columns: 1fr;
      }
    }

    .daily_card {
      background: #fff !important;
      border: 1px solid var(--dr-border) !important;
      border-radius: 16px !important;
      box-shadow: var(--dr-shadow-sm);
      transition: var(--dr-transition);
      margin: 0 !important;
      padding: 0 !important;
      min-height: 128px;
      cursor: pointer;
      overflow: hidden;
    }

    .daily_card:hover {
      border-color: var(--dr-primary) !important;
      box-shadow: var(--dr-shadow);
      transform: translateY(-1px);
    }

    .daily_card.active {
      border-color: var(--dr-primary) !important;
      background: var(--dr-primary-soft) !important;
      box-shadow: 0 0 0 3px rgba(147, 194, 28, .14);
    }

    .daily_card .daily_header {
      padding: 12px 14px;
      border-bottom: 1px solid var(--dr-border);
      background: #fafafa;
    }

    .daily_card .title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .daily_card .daily_report {
      font-size: 13px;
      font-weight: 900;
      color: #111827;
      text-transform: uppercase;
    }

    .daily_card .card-body {
      padding: 14px !important;
      text-align: left;
    }

    .daily_card .start-time {
      font-size: 20px;
      font-weight: 900;
      color: #111827;
      line-height: 1.1;
    }

    .daily_card .fail_time {
      font-size: 12px;
      font-weight: 800;
      color: var(--dr-danger);
      margin-top: 6px;
    }

    .daily_card .end_time {
      font-size: 12px;
      color: var(--dr-muted);
      margin-top: 3px;
    }

    .total_card {
      height: 100%;
      cursor: default;
      background: #111827 !important;
      color: #fff !important;
    }

    .total_card:hover {
      transform: none;
      border-color: #111827 !important;
    }

    .total_card .daily_header {
      background: rgba(255, 255, 255, .08);
      border-color: rgba(255, 255, 255, .12);
    }

    .total_card .daily_report,
    .total_card .start-time {
      color: #fff !important;
    }

    .total_card .fail_time {
      color: #fecaca !important;
    }

    .total_card .end_time {
      color: #d1d5db !important;
    }

    .dr-card {
      background: #fff;
      border: 1px solid var(--dr-border);
      border-radius: 16px;
      box-shadow: var(--dr-shadow-sm);
      overflow: hidden;
    }

    .dr-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      padding: 16px;
      border-bottom: 1px solid var(--dr-border);
      background: #fafafa;
    }

    .dr-card-title {
      font-size: 15px;
      font-weight: 900;
      color: #111827;
      margin: 0;
    }

    .dr-card-sub {
      font-size: 12px;
      color: var(--dr-muted);
      margin-top: 3px;
    }

    /* Custom Table as Cards Layout */
    #daily_report_table,
    .daily_report_table,
    .daily_report_table tbody,
    .daily_report_table tr.dr-entry-row,
    .daily_report_table td.dr-entry-cell {
      width: 100% !important;
    }

    .daily_report_table {
      border-collapse: separate !important;
      border-spacing: 0 12px !important;
    }

    .daily_report_table thead {
      display: none;
    }

    .dr-entry-cell {
      padding: 0 !important;
      border: 0 !important;
      background: transparent !important;
    }

    .daily_report_table tbody tr.dr-entry-row {
      display: table-row !important;
      background: transparent;
    }

    .daily_report_table tbody tr.dr-entry-row:hover {
      box-shadow: none !important;
      background: transparent !important;
    }

    .dr-entry-card {
      background: #fff;
      border: 1px solid var(--dr-border, #e5e7eb);
      border-radius: 16px;
      box-shadow: var(--dr-shadow-sm, 0 1px 2px 0 rgb(0 0 0 / .05));
      overflow: hidden;
      transition: all .2s ease-in-out;
      width: 100%;
    }

    .dr-entry-card:hover {
      border-color: var(--dr-primary, #93c21c);
      box-shadow: var(--dr-shadow, 0 10px 25px -10px rgb(0 0 0 / .25));
    }

    .dr-entry-card.is-missing {
      border-color: rgba(239, 68, 68, .28);
      background: linear-gradient(180deg, #fff, #fff7f7);
    }

    .dr-entry-main {
      padding: 14px;
    }

    .dr-entry-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 14px;
    }

    .dr-entry-typeblock {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
      flex: 1;
      cursor: pointer;
      user-select: none;
      background: transparent;
      border: none;
      text-align: left;
      padding: 0;
      outline: none;
    }

    .dr-entry-type-icon {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
      background: var(--dr-primary-soft, #f4fae7);
      color: var(--dr-primary, #93c21c);
    }

    .dr-entry-titleline {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      align-items: center;
    }

    .dr-entry-type-label {
      font-size: 15px;
      font-weight: 900;
      color: #111827;
    }

    .dr-entry-subline {
      margin-top: 3px;
      color: #6b7280;
      font-size: 13px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 620px;
    }

    .dr-summary-time {
      font-weight: 800;
      color: #111827;
    }

    .dr-entry-source-title {
      font-size: 13px;
      font-weight: 900;
      color: #374151;
    }

    .dr-entry-source-title::before {
      content: '– ';
      color: #9ca3af;
    }

    .dr-source-summary {
      color: #4b5563;
      font-weight: 700;
    }

    .dr-source-context {
      border: 1px solid rgba(116, 178, 212, .35);
      background: #eff6ff;
      border-radius: 14px;
      padding: 12px;
      margin-bottom: 10px;
    }

    .dr-source-context-title {
      display: flex;
      align-items: center;
      gap: 7px;
      color: #1d4ed8;
      font-size: 12px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: 5px;
    }

    .dr-source-context-text {
      color: #1f2937;
      font-size: 13px;
      line-height: 1.5;
      white-space: pre-wrap;
    }

    .dr-mini-badge.locked {
      background: #fff7ed;
      color: #c2410c;
      gap: 4px;
    }

    .dr-mini-badge.other-report {
      background: #f5f3ff;
      color: #6d28d9;
      gap: 4px;
    }

    .dr-mini-badge.locked svg,
    .dr-mini-badge.other-report svg {
      width: 12px;
      height: 12px;
    }

    .dr-report-lock-box {
      border-radius: 14px;
      padding: 12px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .dr-report-lock-box.is-mine {
      border: 1px solid rgba(249, 115, 22, .32);
      background: #fff7ed;
    }

    .dr-report-lock-box.is-other {
      border: 1px solid rgba(124, 58, 237, .25);
      background: #f5f3ff;
    }

    .dr-report-lock-main {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      min-width: 0;
      flex: 1;
    }

    .dr-report-lock-icon {
      width: 34px;
      height: 34px;
      border-radius: 12px;
      background: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #111827;
      flex: 0 0 auto;
    }

    .dr-report-lock-main strong {
      display: block;
      font-size: 13px;
      font-weight: 900;
      color: #111827;
    }

    .dr-report-lock-main span {
      display: block;
      margin-top: 3px;
      font-size: 12px;
      line-height: 1.45;
      color: #6b7280;
    }


    .dr-report-choice-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      flex-wrap: wrap;
    }

    @media(max-width:760px) {

      .dr-report-choice-actions,
      .dr-report-choice-actions .dr-btn,
      .dr-report-choice-actions .dr-btn-soft {
        width: 100%;
      }
    }

    .dr-other-report-preview {
      display: grid;
      gap: 8px;
      margin: 0 0 10px;
    }

    .dr-other-report-item {
      border: 1px dashed var(--dr-border);
      background: #fff;
      border-radius: 12px;
      padding: 10px;
    }

    .dr-other-report-item strong {
      display: block;
      font-size: 12px;
      font-weight: 900;
      color: #111827;
      margin-bottom: 3px;
    }

    .dr-other-report-item span {
      display: block;
      font-size: 12px;
      color: #6b7280;
      line-height: 1.45;
    }


    .dr-related-report-list {
      border: 1px solid rgba(17, 24, 39, .08);
      background: #f8fafc;
      border-radius: 16px;
      padding: 12px;
      margin-bottom: 12px;
      display: grid;
      gap: 10px;
    }

    .dr-related-report-list-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
      padding-bottom: 2px;
    }

    .dr-related-report-list-head strong {
      display: block;
      color: #111827;
      font-size: 13px;
      font-weight: 900;
    }

    .dr-related-report-list-head span {
      display: block;
      margin-top: 3px;
      color: #6b7280;
      font-size: 12px;
      line-height: 1.45;
    }

    .dr-related-report-item {
      background: #fff;
      border: 1px solid var(--dr-border);
      border-radius: 14px;
      padding: 12px;
      display: grid;
      gap: 10px;
    }

    .dr-related-report-item.is-mine {
      border-color: rgba(147, 194, 28, .38);
      background: linear-gradient(180deg, #fff, #fbfff1);
    }

    .dr-related-report-item.is-other {
      border-color: rgba(116, 178, 212, .35);
    }

    .dr-related-report-meta {
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .dr-related-avatar {
      width: 34px;
      height: 34px;
      border-radius: 12px;
      background: #f3f4f6;
      color: #111827;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }

    .dr-related-avatar svg {
      width: 16px;
      height: 16px;
    }

    .dr-related-report-meta strong {
      display: block;
      color: #111827;
      font-size: 13px;
      font-weight: 900;
    }

    .dr-related-report-meta span {
      display: block;
      color: #6b7280;
      font-size: 12px;
      line-height: 1.45;
      margin-top: 2px;
    }

    .dr-related-report-content {
      color: #1f2937;
      font-size: 13px;
      line-height: 1.6;
    }

    .dr-related-report-html {
      max-width: 100%;
      overflow-wrap: anywhere;
    }

    .dr-related-report-html p {
      margin: 0 0 8px;
    }

    .dr-related-report-html ul,
    .dr-related-report-html ol {
      padding-left: 22px;
      margin: 6px 0 8px;
    }

    .dr-related-report-html blockquote {
      border-left: 3px solid var(--dr-blue);
      margin: 8px 0;
      padding: 6px 10px;
      background: #eff6ff;
      border-radius: 8px;
    }

    .dr-related-report-text {
      white-space: pre-wrap;
      overflow-wrap: anywhere;
    }

    .dr-related-report-plain {
      margin-top: 8px;
      border-top: 1px dashed var(--dr-border);
      padding-top: 8px;
      color: #6b7280;
    }

    .dr-related-report-plain summary {
      cursor: pointer;
      font-size: 12px;
      font-weight: 900;
      color: #374151;
    }

    .dr-related-report-plain div {
      margin-top: 6px;
      white-space: pre-wrap;
      overflow-wrap: anywhere;
      font-size: 12px;
      line-height: 1.55;
    }

    .description_input.is-related-locked,
    .description_input.is-related-blocked {
      background: #f9fafb;
      color: #6b7280;
      cursor: not-allowed;
    }

    .description_input.is-related-unlocked {
      background: #fff;
      color: #111827;
      cursor: text;
      box-shadow: 0 0 0 3px var(--dr-primary-soft);
      border-color: var(--dr-primary) !important;
    }

    .dr-continue-prompt {
      margin: 12px 0 0;
      border: 1px solid rgba(147, 194, 28, .35);
      background: linear-gradient(135deg, #f4fae7, #ffffff);
      border-radius: 14px;
      padding: 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .dr-continue-prompt-main {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      min-width: 0;
    }

    .dr-continue-prompt-icon {
      width: 34px;
      height: 34px;
      border-radius: 11px;
      background: #fff;
      color: var(--dr-primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
      border: 1px solid rgba(147, 194, 28, .25);
    }

    .dr-continue-prompt-title {
      font-size: 13px;
      font-weight: 900;
      color: #111827;
    }

    .dr-continue-prompt-sub {
      font-size: 12px;
      color: #6b7280;
      margin-top: 2px;
    }

    .dr-continue-prompt-actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .dr-collapse-icon {
      transition: transform 0.2s ease;
      color: var(--dr-muted);
      margin-left: auto;
      flex: 0 0 auto;
    }

    .dr-entry-toggle[aria-expanded="true"] .dr-collapse-icon {
      transform: rotate(180deg);
    }

    .dr-mini-badge {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 3px 8px;
      font-size: 11px;
      font-weight: 900;
      line-height: 1;
    }

    .dr-mini-badge.saved {
      background: #ecfdf5;
      color: #047857;
    }

    .dr-mini-badge.source {
      background: #eff6ff;
      color: #1d4ed8;
    }

    .dr-mini-badge.missing {
      background: #fef2f2;
      color: #b91c1c;
    }

    .dr-entry-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: 7px;
    }

    .dr-row-action {
      min-height: 38px;
      border: 1px solid var(--dr-border, #e5e7eb);
      border-radius: 11px;
      padding: 8px 10px;
      background: #fff;
      color: #374151;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      cursor: pointer;
      transition: all .2s ease-in-out;
    }

    .dr-row-action.primary {
      background: var(--dr-primary, #93c21c);
      border-color: var(--dr-primary, #93c21c);
      color: #fff !important;
    }

    .dr-row-action.danger {
      background: #fef2f2;
      border-color: rgba(239, 68, 68, .24);
      color: #b91c1c;
    }

    .dr-row-action.ghost:hover {
      border-color: var(--dr-primary, #93c21c);
      color: #111827;
    }

    .dr-entry-body {
      border-top: 1px dashed var(--dr-border);
      margin-top: 12px;
      padding-top: 14px;
    }

    .dr-entry-grid {
      display: grid;
      grid-template-columns: minmax(260px, 320px) minmax(120px, 150px) minmax(220px, 260px) minmax(260px, 1fr);
      gap: 12px;
      align-items: end;
    }

    .dr-entry-grid.secondary {
      margin-top: 12px;
      grid-template-columns: minmax(220px, 280px) minmax(260px, 1fr) minmax(160px, 200px);
    }

    .dr-field-group label {
      display: block;
      margin-bottom: 5px;
      font-size: 11px;
      font-weight: 900;
      letter-spacing: .05em;
      text-transform: uppercase;
      color: #6b7280;
    }

    .dr-control,
    .dr-field-group .select2-container .select2-selection--single,
    .dr-field-group .select2-container .select2-selection--multiple {
      min-height: 40px !important;
      border-radius: 11px !important;
      border: 1px solid var(--dr-border, #e5e7eb) !important;
      width: 100%;
      padding: 8px 12px;
      outline: none;
      background: #fff;
    }

    textarea.dr-control {
      min-height: 94px;
      resize: vertical;
    }

    .dr-control:focus {
      border-color: var(--dr-primary) !important;
      box-shadow: 0 0 0 3px var(--dr-primary-soft);
    }

    .dr-time-pair {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      gap: 8px;
      align-items: center;
    }

    .dr-time-pair span {
      color: #9ca3af;
      font-weight: 900;
    }

    .dr-travel-toggle input {
      display: none;
    }

    .dr-travel-toggle label {
      min-height: 40px;
      border: 1px solid var(--dr-border, #e5e7eb);
      border-radius: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 8px 10px;
      cursor: pointer;
      background: #fff;
      color: #6b7280;
      font-weight: 900;
      text-transform: none;
      font-size: 13px;
      margin: 0;
    }

    .dr-travel-toggle input:checked+label {
      background: #eff6ff;
      color: #1d4ed8;
      border-color: rgba(116, 178, 212, .75);
    }

    .dr-entry-report-area {
      display: grid;
      grid-template-columns: 1fr;
      gap: 12px;
      margin-top: 12px;
      align-items: start;
    }

    .dr-customer-shares {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-top: 8px;
    }

    .dr-customer-share {
      display: grid;
      grid-template-columns: minmax(150px, .9fr) minmax(310px, 1.4fr) minmax(210px, 1fr) 95px minmax(170px, 1.2fr);
        gap: 8px;
        align-items: center;
        padding: 8px;
        border: 1px dashed var(--dr-border, #e5e7eb);
        border-radius: 12px;
        background: #f9fafb;
      }



      .dr-customer-object-product {
        display: grid;
        grid-template-columns: minmax(140px, 1fr) minmax(150px, 1fr);
        gap: 8px;
        min-width: 0;
      }

      .dr-customer-object-product select {
        min-width: 0;
        font-size: 12px;
        font-weight: 800;
      }

      .dr-customer-time-pair {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 6px;
        align-items: center;
      }

      .dr-customer-time-pair span {
        color: var(--dr-muted, #6b7280);
        font-weight: 900;
        font-size: 12px;
      }

      .dr-customer-share-hours {
        background: #fff !important;
        font-weight: 900;
        color: #111827;
      }

      .dr-customer-share-warning {
        grid-column: 1 / -1;
        display: none;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 10px;
        background: var(--dr-danger-soft, #fef2f2);
        color: #b91c1c;
        font-size: 12px;
        font-weight: 800;
      }

      .dr-customer-share-warning.is-visible {
        display: flex;
      }

      .dr-customer-share.is-invalid {
        border-color: rgba(239, 68, 68, .55);
        background: #fff7f7;
      }

      .dr-customer-share-total {
        margin-top: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 9px 10px;
        border-radius: 12px;
        border: 1px solid var(--dr-border, #e5e7eb);
        background: #fff;
        color: #374151;
        font-size: 12px;
        font-weight: 900;
      }

      .dr-customer-share-total.is-danger {
        border-color: rgba(239, 68, 68, .45);
        background: var(--dr-danger-soft, #fef2f2);
        color: #b91c1c;
      }

      .dr-customer-name {
        font-size: 12px;
        font-weight: 900;
        color: #374151;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .dr-total-footer td {
        border: 0 !important;
        padding: 0 !important;
        background: transparent !important;
      }

      .dr-total-box {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding: 13px 16px;
        border: 1px solid var(--dr-border, #e5e7eb);
        border-radius: 14px;
        background: #111827;
        color: #fff;
      }

      .dr-total-box span {
        text-transform: uppercase;
        letter-spacing: .05em;
        font-size: 12px;
        font-weight: 900;
        color: #d1d5db;
      }

      .dr-total-box strong {
        font-size: 18px;
        font-weight: 900;
      }

      .dr-total-box small {
        color: #9ca3af;
      }


      .dr-time-limit-warning,
      .dr-row-time-warning {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        border: 1px solid rgba(245, 158, 11, .35);
        background: #fffbeb;
        color: #92400e;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 800;
        margin-top: 10px;
      }

      .dr-time-limit-warning.is-danger,
      .dr-row-time-warning.is-danger {
        border-color: rgba(239, 68, 68, .35);
        background: #fef2f2;
        color: #991b1b;
      }

      .dr-total-box.is-over-limit {
        background: #7f1d1d;
        border-color: rgba(239, 68, 68, .5);
      }


      .dr-row-action[disabled],
      .dr-row-action.is-disabled {
        opacity: .55;
        cursor: not-allowed;
        filter: grayscale(.2);
      }

      .dr-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 36px;
        border: 1px dashed var(--dr-border, #e5e7eb);
        border-radius: 16px;
        background: #f9fafb;
        color: #6b7280;
      }

      @media(max-width: 1280px) {

        .dr-entry-grid,
        .dr-entry-grid.secondary,
        .dr-entry-report-area {
          grid-template-columns: 1fr 1fr;
        }

        .place-group,
        .description-group,
        .customer-group {
          grid-column: 1 / -1;
        }
      }

      @media(max-width: 760px) {

        .dr-entry-top,
        .dr-entry-actions,
        .dr-total-box {
          align-items: stretch;
          flex-direction: column;
        }

        .dr-entry-grid,
        .dr-entry-grid.secondary,
        .dr-entry-report-area,
        .dr-customer-share {
          grid-template-columns: 1fr;
        }

        .dr-row-action {
          width: 100%;
        }

        .dr-continue-prompt,
        .dr-continue-prompt-main,
        .dr-continue-prompt-actions {
          flex-direction: column;
          align-items: stretch;
        }

        .dr-collapse-icon {
          display: none;
        }
      }

      .dr-actions-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
      }

      .missing-row {
        background-color: var(--dr-danger-soft) !important;
      }

      .hours_spent_input.is-auto-calculated {
        background: #f8fafc;
        font-weight: 900;
        color: #111827;
      }

      /* Modals & Drawers */
      .dr-modal-backdrop,
      .notes-drawer-backdrop,
      .attach-drawer-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .42);
        opacity: 0;
        pointer-events: none;
        transition: opacity .2s ease;
        z-index: 1100;
      }

      .dr-modal {
        position: fixed;
        inset: 0;
        display: grid;
        place-items: center;
        padding: 16px;
        opacity: 0;
        pointer-events: none;
        transform: translateY(10px);
        transition: opacity .2s ease, transform .2s ease;
        z-index: 1101;
      }

      .dr-modal.is-open,
      .dr-modal-backdrop.is-open,
      .notes-drawer-backdrop.open,
      .attach-drawer-backdrop.open {
        opacity: 1;
        pointer-events: auto;
      }

      .dr-modal.is-open {
        transform: translateY(0);
      }

      .dr-modal-panel {
        width: min(560px, 100%);
        max-height: min(78vh, 680px);
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--dr-border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 80px rgba(15, 23, 42, .26);
      }

      .dr-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 16px;
        border-bottom: 1px solid var(--dr-border);
        background: #fafafa;
      }

      .dr-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        color: #111827;
      }

      .dr-modal-sub {
        margin: 4px 0 0;
        font-size: 12px;
        color: var(--dr-muted);
      }

      .dr-modal-body {
        padding: 16px;
        overflow: auto;
      }

      .dr-history-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 10px;
      }

      .dr-history-list li {
        border: 1px solid var(--dr-border);
        border-radius: 14px;
        padding: 12px;
        background: #fff;
      }

      .dr-history-list a {
        color: #1d4ed8;
        font-weight: 900;
        text-decoration: none;
      }

      .dr-dialog-icon {
        font-size: 24px;
        line-height: 1;
      }

      .dr-dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: wrap;
      }

      .dr-btn-danger {
        background: var(--dr-danger);
        color: #fff;
        border-color: var(--dr-danger);
      }

      .notes-drawer,
      .attach-drawer {
        position: fixed;
        top: 0;
        right: -460px;
        height: 100vh;
        width: 420px;
        max-width: 100vw;
        background: #fff;
        box-shadow: -16px 0 40px rgba(15, 23, 42, .18);
        transition: right .25s;
        z-index: 1105;
        display: flex;
        flex-direction: column;
      }

      .notes-drawer.open,
      .attach-drawer.open {
        right: 0;
      }

      .notes-header,
      .attach-header {
        padding: 14px 16px;
        border-bottom: 1px solid var(--dr-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafafa;
      }

      .dr-drawer-title {
        font-size: 15px;
        font-weight: 900;
        color: #111827;
      }

      .notes-list,
      .attach-list {
        padding: 12px 12px 80px;
        overflow: auto;
        flex: 1;
      }

      .note-item {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
      }

      .note-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        background: #eee;
      }

      .note-bubble {
        background: #f3f4f6;
        border-radius: 12px;
        padding: 8px 10px;
        max-width: 280px;
      }

      .dr-note-author {
        font-size: 12px;
        font-weight: 900;
        color: #111827;
        margin-bottom: 2px;
      }

      .note-meta {
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
      }

      .notes-inputbar,
      .attach-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        border-top: 1px solid var(--dr-border);
        background: #fff;
        padding: 10px;
      }

      .dr-inline-form {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
      }

      .dr-inline-form-file {
        align-items: flex-start;
        flex-direction: column;
      }

      .attach-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid var(--dr-border);
        border-radius: 12px;
        padding: 10px;
        margin-bottom: 8px;
      }

      .attach-thumb {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        border: 1px solid var(--dr-border);
        object-fit: cover;
        background: #f8fafc;
        margin-right: 10px;
      }


      /* Enhanced attachment drawer: search, filter, grid/list cards */
      .attach-toolbar {
        padding: 12px;
        border-bottom: 1px solid var(--dr-border);
        display: grid;
        grid-template-columns: minmax(180px, 1fr) minmax(150px, 190px);
        gap: 10px;
        background: #fff;
      }

      .attach-search-wrap {
        position: relative;
      }

      .attach-search-wrap i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--dr-muted);
        pointer-events: none;
      }

      .attach-search-wrap .dr-control {
        padding-left: 36px;
      }

      .attach-source-filter {
        min-height: 40px !important;
      }

      .attach-customer-context {
        padding: 10px 12px;
        border-bottom: 1px solid var(--dr-border);
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 12px;
        color: var(--dr-muted);
        font-weight: 800;
      }

      .attach-customer-context strong {
        color: #111827;
        font-weight: 900;
      }

      .attach-customer-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
      }

      .attach-customer-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 999px;
        background: var(--dr-primary-soft);
        color: #4d7c0f;
        font-size: 11px;
        font-weight: 900;
      }

      .attach-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
      }

      .attach-item {
        display: grid;
        grid-template-columns: 58px minmax(0, 1fr);
        gap: 10px;
        align-items: start;
        border: 1px solid var(--dr-border);
        border-radius: 14px;
        padding: 10px;
        margin-bottom: 0;
        background: #fff;
        box-shadow: var(--dr-shadow-sm);
      }

      .attach-item:hover {
        border-color: rgba(147, 194, 28, .55);
        box-shadow: 0 12px 28px -22px rgba(15, 23, 42, .45);
      }

      .attach-thumb {
        width: 58px;
        height: 58px;
        border-radius: 13px;
        border: 1px solid var(--dr-border);
        object-fit: cover;
        background: #f8fafc;
        margin-right: 0;
      }

      .attach-file-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--dr-primary);
        background: var(--dr-primary-soft);
        font-weight: 900;
        text-transform: uppercase;
        overflow: hidden;
      }

      .attach-file-icon svg {
        width: 22px;
        height: 22px;
      }

      .attach-item-body {
        min-width: 0;
      }

      .attach-item-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
      }

      .dr-attach-name {
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
      }

      .attach-source-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 7px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
        flex: 0 0 auto;
      }

      .attach-source-badge.report {
        background: #eff6ff;
        color: #1d4ed8;
      }

      .attach-source-badge.customer {
        background: #f4fae7;
        color: #4d7c0f;
      }

      .attach-meta-line {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
        color: var(--dr-muted);
        font-size: 12px;
        font-weight: 700;
      }

      .attach-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 10px;
        flex-wrap: wrap;
      }

      .attach-actions .dr-btn-soft {
        min-height: 34px;
        padding: 7px 10px;
        border-radius: 10px;
        font-size: 12px;
      }

      .attach-footer-hint {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 12px;
        color: var(--dr-muted);
        font-weight: 700;
        line-height: 1.45;
        padding: 2px 2px 8px;
      }

      .attach-footer-hint i {
        width: 16px;
        height: 16px;
        margin-top: 1px;
        color: var(--dr-blue);
        flex: 0 0 auto;
      }

      .attach-empty-state {
        border: 1px dashed var(--dr-border);
        border-radius: 14px;
        padding: 28px 14px;
        text-align: center;
        background: #f9fafb;
        color: var(--dr-muted);
        font-weight: 800;
      }

      .attach-empty-state i {
        display: block;
        margin: 0 auto 8px;
        color: #9ca3af;
      }

      .dr-attach-preview {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-top: 8px;
      }

      .dr-attach-preview-item {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        padding: 8px;
        border: 1px dashed var(--dr-border);
        border-radius: 11px;
        background: #f9fafb;
      }

      @media(max-width: 560px) {
        .attach-toolbar {
          grid-template-columns: 1fr;
        }

        .attach-item {
          grid-template-columns: 48px minmax(0, 1fr);
        }

        .attach-thumb {
          width: 48px;
          height: 48px;
        }
      }

      .dr-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        border: 1px solid var(--dr-border);
        background: #fff;
        color: var(--dr-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--dr-transition);
        flex: 0 0 auto;
      }

      .dr-icon-btn:hover {
        border-color: var(--dr-primary);
        background: var(--dr-primary-soft);
      }

      .dr-action-counter {
        position: relative;
        display: inline-flex;
      }

      .count-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 18px;
        height: 18px;
        line-height: 18px;
        background: #111827;
        color: #fff;
        border-radius: 9999px;
        font-size: 11px;
        text-align: center;
        padding: 0 5px;
      }

      .count-badge.hidden {
        display: none !important;
      }

      .dr-spinner {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid currentColor;
        border-right-color: transparent;
        display: inline-block;
        animation: drSpin .7s linear infinite;
        margin-right: 6px;
      }

      @keyframes drSpin {
        to {
          transform: rotate(360deg);
        }
      }


      /* ------------------------------------------------------------
           Voice input / microphone helper for daily report text fields
           ------------------------------------------------------------ */
      .dr-voice-language-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin: 0 0 16px;
        padding: 12px 14px;
        background: #fff;
        border: 1px solid var(--dr-border, #e5e7eb);
        border-radius: 14px;
        box-shadow: var(--dr-shadow-sm, 0 1px 2px 0 rgb(0 0 0 / .05));
      }

      .dr-voice-language-main {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        min-width: 0;
      }

      .dr-voice-language-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 900;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: .05em;
        white-space: nowrap;
      }

      .dr-voice-language-bar select {
        min-width: 220px;
        max-width: 100%;
        min-height: 38px;
        padding: 8px 10px;
        border: 1px solid var(--dr-border, #e5e7eb);
        border-radius: 10px;
        background: #f9fafb;
        font-size: 13px;
        font-weight: 800;
        color: #111827;
        outline: none;
      }

      .dr-voice-language-bar select:focus {
        background: #fff;
        border-color: var(--dr-primary, #93c21c);
        box-shadow: 0 0 0 3px var(--dr-primary-soft, #f4fae7);
      }

      .dr-voice-hint {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--dr-muted, #6b7280);
        font-weight: 700;
      }

      .dr-voice-wrap {
        position: relative;
        width: 100%;
      }

      .dr-voice-wrap textarea,
      .dr-voice-wrap input[type="text"],
      .dr-voice-wrap input[type="search"] {
        padding-right: 52px !important;
      }

      .dr-voice-btn {
        position: absolute;
        right: 10px;
        bottom: 10px;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 999px;
        background: var(--dr-primary-soft, #f4fae7);
        color: var(--dr-primary, #93c21c);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .2s ease-in-out;
        z-index: 9;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
      }

      .dr-voice-btn:hover {
        background: var(--dr-primary, #93c21c);
        color: #fff;
        transform: translateY(-1px);
      }

      .dr-voice-btn svg,
      .dr-voice-btn i {
        width: 18px;
        height: 18px;
      }

      .dr-voice-btn.is-listening {
        background: #ef4444;
        color: #fff;
        animation: drVoicePulse 1.1s infinite;
      }

      .dr-voice-status {
        display: none;
        margin-top: 6px;
        font-size: 11px;
        font-weight: 800;
        color: #ef4444;
      }

      .dr-voice-wrap.is-listening .dr-voice-status {
        display: block;
      }

      .dr-voice-global-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
      }

      .dr-voice-global-status::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #9ca3af;
        flex: 0 0 auto;
      }

      .dr-voice-global-status.is-active {
        background: #fef2f2;
        color: #b91c1c;
        border-color: rgba(239, 68, 68, .38);
      }

      .dr-voice-global-status.is-active::before {
        background: #ef4444;
        animation: drVoiceDotPulse 1s infinite;
      }

      .dr-voice-global-status.is-done {
        background: #ecfdf5;
        color: #047857;
        border-color: rgba(16, 185, 129, .38);
      }

      .dr-voice-global-status.is-done::before {
        background: #10b981;
      }

      .dr-voice-global-status.is-error {
        background: #fffbeb;
        color: #b45309;
        border-color: rgba(245, 158, 11, .45);
      }

      .dr-voice-global-status.is-error::before {
        background: #f59e0b;
      }

      @keyframes drVoiceDotPulse {
        0% {
          box-shadow: 0 0 0 0 rgba(239, 68, 68, .45);
        }

        100% {
          box-shadow: 0 0 0 9px rgba(239, 68, 68, 0);
        }
      }

      @keyframes drVoicePulse {
        0% {
          box-shadow: 0 0 0 0 rgba(239, 68, 68, .42);
        }

        100% {
          box-shadow: 0 0 0 13px rgba(239, 68, 68, 0);
        }
      }



      .dr-voice-controls {
        position: absolute;
        right: 10px;
        bottom: 10px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        z-index: 9;
      }

      .dr-voice-wrap textarea,
      .dr-voice-wrap input[type="text"],
      .dr-voice-wrap input[type="search"] {
        padding-right: 94px !important;
      }

      .dr-voice-play-btn,
      .dr-voice-stop-btn {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .2s ease-in-out;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
      }

      .dr-voice-play-btn {
        background: var(--dr-primary-soft, #f4fae7);
        color: var(--dr-primary, #93c21c);
      }

      .dr-voice-play-btn:hover {
        background: var(--dr-primary, #93c21c);
        color: #fff;
        transform: translateY(-1px);
      }

      .dr-voice-stop-btn {
        background: #fee2e2;
        color: #dc2626;
        opacity: .55;
        cursor: not-allowed;
      }

      .dr-voice-wrap.is-listening .dr-voice-play-btn {
        background: #ef4444;
        color: #fff;
        animation: drVoicePulse 1.1s infinite;
      }

      .dr-voice-wrap.is-listening .dr-voice-stop-btn {
        opacity: 1;
        cursor: pointer;
        background: #dc2626;
        color: #fff;
      }

      .dr-voice-wrap.is-listening .dr-voice-play-btn:hover,
      .dr-voice-wrap.is-listening .dr-voice-stop-btn:hover {
        transform: translateY(-1px);
      }

      .dr-voice-timer {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
      }

      .dr-voice-timer.is-active {
        color: #b91c1c;
        border-color: rgba(239, 68, 68, .38);
        background: #fef2f2;
      }


      /* Smoother voice control: one large clear Start/Stop toggle under every field */
      .dr-voice-controls {
        position: static !important;
        right: auto !important;
        bottom: auto !important;
        display: flex !important;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        width: 100%;
        margin-top: 8px;
        z-index: 1;
      }

      .dr-voice-wrap textarea,
      .dr-voice-wrap input[type="text"],
      .dr-voice-wrap input[type="search"] {
        padding-right: 12px !important;
      }

      .dr-voice-play-btn {
        width: auto !important;
        min-width: 148px;
        height: 44px !important;
        padding: 0 16px;
        border-radius: 12px !important;
        gap: 8px;
        font-size: 13px;
        font-weight: 900;
        background: var(--dr-primary-soft, #f4fae7);
        color: var(--dr-primary, #93c21c);
        border: 1px solid rgba(147, 194, 28, .32);
      }

      .dr-voice-play-btn svg,
      .dr-voice-play-btn i {
        width: 18px;
        height: 18px;
      }

      .dr-voice-play-btn::after {
        content: 'Start';
      }

      .dr-voice-wrap.is-listening .dr-voice-play-btn {
        display: inline-flex !important;
        min-width: 148px;
        background: #dc2626 !important;
        color: #fff !important;
        border-color: rgba(220, 38, 38, .55);
        animation: drVoicePulse 1.1s infinite;
      }

      .dr-voice-wrap.is-listening .dr-voice-play-btn::after {
        content: 'Stop';
      }

      .dr-voice-stop-btn {
        display: none !important;
      }

      .dr-voice-status {
        margin-top: 8px !important;
        padding: 10px 12px !important;
        border-radius: 12px !important;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        color: #374151;
        font-size: 12px;
        line-height: 1.45;
      }

      .dr-voice-wrap.is-listening .dr-voice-status {
        background: #fff7ed;
        border-color: rgba(245, 158, 11, .45);
      }

      @media(max-width: 760px) {
        .dr-voice-language-bar {
          align-items: stretch;
        }

        .dr-voice-language-main,
        .dr-voice-language-bar select,
        .dr-voice-hint {
          width: 100%;
        }
      }

      .dr-stat.is-overtime {
        border-color: rgba(245, 158, 11, .45);
        background: #fffbeb;
      }

      .dr-row-time-warning.is-overtime,
      .dr-time-limit-warning.is-overtime {
        background: var(--dr-warning-soft);
        color: #92400e;
        border-color: rgba(245, 158, 11, .35);
      }
    </style>
@endsection

@section('content')
  <div class="app-content daily-report-modern">
    <div class="content-wrapper">
      <div class="content-body">

        <!-- Header -->
        <div class="dr-header">
          <div class="dr-titlebar">
            <div>
              <div class="dr-title">Tagesbericht</div>
              <div class="dr-sub">Arbeitszeiten, Termine, Aufgaben, Kundenzeiten und fehlende Zeiten zentral prüfen.</div>
              <div class="dr-breadcrumb">
                <a href="{{ url('/') }}">Dashboard</a>
                <span>›</span>
                <span>Bericht</span>
                <span>›</span>
                <span class="current">{{ $employee_name }}</span>
              </div>
            </div>

            <div class="dr-toolbar-right">
              <a href="{{ route('work.place.index') }}" class="dr-btn-soft">
                <i data-lucide="map-pin"></i> Arbeitsplatz
              </a>
              <button class="dr-btn" type="button" onclick="document.querySelector('#completeDailyReport')?.click()">
                <i data-lucide="file-text"></i> PDF erstellen
              </button>
            </div>
          </div>
        </div>

        <!-- Hidden Data Context -->
        <input type="hidden" id="selected_date" value="{{ $start_date }}">
        <input type="hidden" id="employee_id" value="{{ $employee_id }}">
        <input type="hidden" id="expected_hours_per_day"
          value="{{ number_format($expectedHoursForDay ?? 0, 2, '.', '') }}">


        <!-- Voice Input Language -->
        <div class="dr-voice-language-bar" id="drVoiceLanguageBar">
          <div class="dr-voice-language-main">
            <label for="drVoiceLanguage" class="dr-voice-language-label">
              <i data-lucide="mic"></i> Spracheingabe
            </label>
            <select id="drVoiceLanguage" aria-label="Sprache für Mikrofon wählen">
              <option value="de-DE">Deutsch</option>
              <option value="en-US">English</option>
              <option value="fa-AF">دری / فارسی افغانستان</option>
              <option value="fa-IR">فارسی ایران</option>
              <option value="ps-AF">پښتو</option>
              <option value="ar-SA">العربية</option>
              <option value="tr-TR">Türkçe</option>
              <option value="ru-RU">Русский</option>
              <option value="fr-FR">Français</option>
              <option value="es-ES">Español</option>
              <option value="it-IT">Italiano</option>
              <option value="nl-NL">Nederlands</option>
              <option value="pl-PL">Polski</option>
              <option value="uk-UA">Українська</option>
              <option value="hi-IN">हिन्दी</option>
              <option value="ur-PK">اردو</option>
            </select>
          </div>
          <span id="drVoiceStatus" class="dr-voice-global-status">Mikrofon bereit</span>
          <span id="drVoiceTimer" class="dr-voice-timer">00:00</span>
          <span class="dr-voice-hint">
            <i data-lucide="info"></i>
            Sprache wählen, Mikrofon am Feld klicken und direkt in Bericht, Kundenbeschreibung oder Notiz diktieren.
          </span>
        </div>

        <!-- Tabs Navigation -->
        <div class="dr-tabs">
          <button class="dr-tab-btn active" onclick="switchTab('tab-tagesbericht', this)">
            <i data-lucide="pen-tool"></i> Tagesbericht
          </button>
          <button class="dr-tab-btn" onclick="switchTab('tab-kalender', this)">
            <i data-lucide="calendar"></i> Kalenderübersicht
          </button>
        </div>

        <!-- TAB 1: TAGESBERICHT -->
        <div id="tab-tagesbericht" class="tab-pane active">

          <!-- Animated Remaining Time Banner -->
          <div class="remaining-time-card">
            <div class="remaining-header">
              <div class="remaining-title">
                <i data-lucide="clock"></i> Restzeit / Überstunden heute
              </div>
              <div class="remaining-amount"><span
                  id="remainingTimeAnimated">{{ number_format($expectedHoursForDay ?? 0, 2, ',', '.') }}</span> Std.</div>
            </div>
            <div class="progress-track">
              <div class="progress-fill" id="remainingFill" style="width: 0%;"></div>
            </div>
            <div style="margin-top: 10px; font-size: 13px; color: #9ca3af;" id="progressText">
              Berechne erfasste Zeiten...
            </div>
          </div>

          <!-- Top Analytics -->
          <div class="dr-analytics">
            <div class="dr-stat">
              <div class="dr-stat-icon total"><i data-lucide="user"></i></div>
              <div class="dr-stat-meta">
                <div class="dr-stat-label">Mitarbeiter</div>
                <div class="dr-stat-value" style="font-size:18px;">{{ $employee_name }}</div>
                <div class="dr-stat-sub">Aktueller Tagesbericht</div>
              </div>
            </div>
            <div class="dr-stat">
              <div class="dr-stat-icon worked"><i data-lucide="check-circle"></i></div>
              <div class="dr-stat-meta">
                <div class="dr-stat-label">Gearbeitet</div>
                <div class="dr-stat-value" id="worked_total">0,00 Std.</div>
                <div class="dr-stat-sub">Summe des Tages</div>
              </div>
            </div>
            <div class="dr-stat">
              <div class="dr-stat-icon missing"><i data-lucide="alert-circle"></i></div>
              <div class="dr-stat-meta">
                <div class="dr-stat-label">Fehlend</div>
                <div class="dr-stat-value" id="missing_hours">0,00 Std.</div>
                <div class="dr-stat-sub">Noch offene Stunden</div>
              </div>
            </div>
            <div class="dr-stat" id="overtime_stat_card">
              <div class="dr-stat-icon expected"><i data-lucide="clock-plus"></i></div>
              <div class="dr-stat-meta">
                <div class="dr-stat-label">Überstunden</div>
                <div class="dr-stat-value" id="overtime_hours">0,00 Std.</div>
                <div class="dr-stat-sub">Zeit über der Soll-Zeit</div>
              </div>
            </div>
            <div class="dr-stat">
              <div class="dr-stat-icon expected"><i data-lucide="clock"></i></div>
              <div class="dr-stat-meta">
                <div class="dr-stat-label">Soll-Zeit</div>
                <div class="dr-stat-value">{{ number_format($expectedHoursForDay ?? 0, 2, ',', '') }} Std.</div>
                <div class="dr-stat-sub">Erwartete Tagesstunden</div>
              </div>
            </div>
          </div>

          <!-- Month Analytics Overview -->
          <div class="dr-month-analytics" id="monthAnalyticsCard">
            <div class="dr-month-head">
              <div>
                <h3 class="dr-month-title"><i data-lucide="bar-chart-2"></i> Monatsanalyse</h3>
                <div class="dr-month-sub" id="monthAnalyticsSubtitle">Berichte, fehlende Tage und Stunden für den
                  ausgewählten Monat.</div>
              </div>
              <button type="button" class="dr-btn-soft" id="refreshMonthAnalytics">
                <i data-lucide="refresh-cw"></i> Aktualisieren
              </button>
            </div>
            <div class="dr-month-body">
              <div class="dr-month-score">
                <div class="dr-month-ring" id="monthProgressRing" style="--p:0" data-value="0%"></div>
                <div>
                  <div class="dr-stat-label">Abdeckung</div>
                  <div class="dr-stat-value" id="monthCoverageText">0 / 0 Tage</div>
                  <div class="dr-stat-sub" id="monthOpenDaysText">0 offene Tage</div>
                </div>
              </div>
              <div>
                <div class="dr-month-kpis">
                  <div class="dr-month-kpi"><span class="label">Monatstage</span><span class="value"
                      id="monthTotalDays">0</span><span class="hint">Kalendertage im Monat</span></div>
                  <div class="dr-month-kpi"><span class="label">Berichte</span><span class="value"
                      id="monthReportedDays">0</span><span class="hint">Tage mit Tagesbericht</span></div>
                  <div class="dr-month-kpi"><span class="label">Offen</span><span class="value"
                      id="monthMissingDays">0</span><span class="hint">Tage ohne Bericht</span></div>
                  <div class="dr-month-kpi"><span class="label">Fehlstunden</span><span class="value"
                      id="monthMissingHours">0,00</span><span class="hint">Soll minus Ist</span></div>
                </div>
                <div class="dr-month-days" id="monthDaysGrid"></div>
              </div>
            </div>
          </div>

          <!-- Weekly Ribbon -->
          <div class="dr-week-card" id="daily_report_row">
            <div class="dr-week-head">
              <button class="dr-btn-soft" id="btnPrevWeek" type="button"><i data-lucide="chevron-left"></i> Vorherige
                Woche</button>
              <div class="dr-week-title"><i data-lucide="calendar"></i> <span class="week_title">Woche</span></div>
              <button class="dr-btn-soft" id="btnNextWeek" type="button">Nächste Woche <i
                  data-lucide="chevron-right"></i></button>
            </div>
            <div class="dr-week-body">
              <div class="daily_report_row"></div>
              <div class="daily_card total_card dr-total-day-card">
                <div class="daily_header">
                  <div class="title">
                    <div class="daily_date"><span class="daily_report">Gesamt</span></div>
                    <div class="status_date"><i data-lucide="circle" id="status" style="width:14px;height:14px;"></i>
                    </div>
                  </div>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    <p class="start-time">0 Std.</p>
                    <p class="fail_time">0 Std. fehlt</p>
                    <p class="end_time">0 Std.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Toolbar -->
          <div class="dr-toolbar">
            <div class="dr-toolbar-left">
              <div class="dr-filter-block grow">
                <label class="dr-filter-label">Ansicht</label>
                <div class="dr-input" style="display:flex;align-items:center;gap:8px;color:#6b7280;">
                  <i data-lucide="list"></i> Tagespositionen für das ausgewählte Datum
                </div>
              </div>
              <div class="dr-filter-block">
                <label class="dr-filter-label" for="filterType">Typ filtern</label>
                <select id="filterType" class="dr-select">
                  <option value="">Alle Typen</option>
                  <option value="Aufgabe">Aufgabe</option>
                  <option value="Termin">Termin</option>
                  <option value="Projekt">Projekt</option>
                  <option value="Angebot">Angebot</option>
                  <option value="Ticket">Ticket</option>
                  <option value="Pause">Pause</option>
                  <option value="Fehlend">Fehlend</option>
                  <option value="Manuell">Manuell</option>
                </select>
              </div>
            </div>
            <div class="dr-toolbar-right">
              <button class="dr-btn-soft" type="button" onclick="document.querySelector('#viewReportHistory')?.click()">
                <i data-lucide="clock"></i> Bericht-Historie
              </button>
            </div>
          </div>

          <!-- Main Report Entries Table Structure -->
          <div class="dr-card">
            <div class="dr-card-head">
              <div>
                <h3 class="dr-card-title">Tagespositionen</h3>
                <div class="dr-card-sub">Zeiten, Arbeitsort, Typ, Abrechnung/Kategorie, Kunde und Beschreibung.</div>
              </div>
            </div>
            <div class="dr-table-shell" id="daily_report_table">
              <div class="dr-table-scroll">
                <table class="daily_report_table">
                  <thead class="table-header">
                    <tr>
                      <th>ZEIT</th>
                      <th>STD.</th>
                      <th>ARBEITSORT</th>
                      <th>TYP</th>
                      <th>ABR./KAT.</th>
                      <th>KUNDE</th>
                      <th>BESCHREIBUNG</th>
                      <th>AKTIONEN</th>
                    </tr>
                  </thead>
                  <tbody id="daily_report_tbody">
                    @include('admin.daily_report.report.report_rows', ['entries' => $entries, 'customers' => $customers])
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="dr-actions-row">
            <button class="dr-btn-soft" id="viewReportHistory" type="button"><i data-lucide="clock"></i>
              Bericht-Historie</button>
            <button class="dr-btn" id="completeDailyReport" type="button"><i data-lucide="file-text"></i> Tagesbericht
              erstellen (PDF)</button>
          </div>

        </div> <!-- END TAB 1 -->

        <!-- TAB 2: KALENDERÜBERSICHT -->
        <div id="tab-kalender" class="tab-pane">
          <div class="calendar-container">
            <div
              style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px; flex-wrap:wrap; gap:14px;">
              <h3 style="margin: 0; font-weight: 900; font-size: 20px; display: flex; align-items: center; gap: 10px;"
                id="bigCalendarTitle">
                <i data-lucide="calendar"></i> <span id="bigCalendarMonthLabel">Monat Laden...</span>
              </h3>
              <div style="display:flex; gap:8px;">
                <!-- Future features to step through months can go here -->
                <button class="dr-btn-soft" onclick="document.getElementById('refreshMonthAnalytics').click()"><i
                    data-lucide="refresh-cw"></i> Kalender laden</button>
              </div>
            </div>

            <div class="calendar-header">
              <div>Mo</div>
              <div>Di</div>
              <div>Mi</div>
              <div>Do</div>
              <div>Fr</div>
              <div>Sa</div>
              <div>So</div>
            </div>
            <div class="calendar-grid" id="calendarGrid">
              <!-- Grid populated automatically by renderMonthAnalytics -->
            </div>
          </div>
        </div> <!-- END TAB 2 -->

        <!-- History Modal -->
        <div class="dr-modal-backdrop" id="reportHistoryBackdrop" data-dr-modal-close="reportHistoryModal"></div>
        <section class="dr-modal" id="reportHistoryModal" aria-hidden="true" role="dialog"
          aria-labelledby="reportHistoryLabel">
          <div class="dr-modal-panel" role="document">
            <header class="dr-modal-header">
              <div>
                <h3 class="dr-modal-title" id="reportHistoryLabel">Bericht-Historie</h3>
                <p class="dr-modal-sub">Gespeicherte PDF-Berichte für den ausgewählten Tag.</p>
              </div>
              <button type="button" class="dr-icon-btn" data-dr-modal-close="reportHistoryModal" aria-label="Schließen">
                <i data-lucide="x"></i>
              </button>
            </header>
            <div class="dr-modal-body">
              <ul id="reportHistoryList" class="dr-history-list">
                <li>Lade Daten…</li>
              </ul>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>

  <!-- Drawers for Notes and Attachments -->
  <div class="notes-drawer-backdrop" id="notesBackdrop"></div>
  <div class="notes-drawer" id="notesDrawer" aria-hidden="true">
    <div class="notes-header">
      <div>
        <div class="dr-drawer-title">Notizen</div>
        <small class="dr-muted" id="notesContext">—</small>
      </div>
      <button type="button" class="dr-icon-btn" id="notesClose" aria-label="Schließen"><i data-lucide="x"></i></button>
    </div>
    <div class="notes-list" id="notesList">
      <div class="dr-muted dr-small">Lädt…</div>
    </div>
    <div class="notes-inputbar">
      <form id="notesForm" class="dr-inline-form">
        @csrf
        <input type="hidden" name="date" id="notesDate">
        <input type="hidden" name="entry_id" id="notesEntry">
        <input type="text" name="message" id="notesMessage" class="dr-control" placeholder="Notiz schreiben…"
          maxlength="2000" required>
        <button class="dr-btn" type="submit">Senden</button>
      </form>
    </div>
  </div>

  <div class="attach-drawer-backdrop" id="attachBackdrop"></div>
  <div class="attach-drawer" id="attachDrawer" aria-hidden="true">
    <div class="attach-header">
      <div>
        <div class="dr-drawer-title">Anhänge</div>
        <small class="dr-muted" id="attachContext">—</small>
      </div>
      <button type="button" class="dr-icon-btn" id="attachClose" aria-label="Schließen"><i data-lucide="x"></i></button>
    </div>

    <div class="attach-toolbar">
      <div class="attach-search-wrap">
        <i data-lucide="search"></i>
        <input type="search" id="attachSearch" class="dr-control" placeholder="Anhang suchen… Name, Typ, Kunde">
      </div>
      <select id="attachSourceFilter" class="dr-control attach-source-filter">
        <option value="all">Alle Dateien</option>
        <option value="report">Nur Bericht-Anhänge</option>
        <option value="customer">Nur Kunden-Dateien</option>
      </select>
    </div>

    <div class="attach-customer-context" id="attachCustomerContext" hidden>
      <div>
        <strong>Kunden-Verknüpfung aktiv</strong>
        <span>Upload wird zusätzlich in Kunden-Dateien gespeichert.</span>
      </div>
      <div class="attach-customer-badges" id="attachCustomerBadges"></div>
    </div>

    <div class="attach-list" id="attachList">
      <div class="attach-empty-state"><i data-lucide="paperclip"></i>Keine Dateien.</div>
    </div>

    <div class="attach-footer">
      <form id="attachForm" class="dr-inline-form dr-inline-form-file">
        @csrf
        <input type="hidden" name="date" id="attachDate">
        <input type="hidden" name="entry_id" id="attachEntry">
        <input type="file" id="attachFiles" name="files[]" multiple class="dr-control" style="padding: 6px;"
          accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,image/*">
        <div class="attach-footer-hint">
          <i data-lucide="info"></i>
          <span>Wenn in der Zeile Kunden ausgewählt sind, wird der Upload zusätzlich in der Kunden-Bild/Dateiablage
            gespeichert. Ohne Kunde bleibt er nur als Bericht-Anhang gespeichert.</span>
        </div>
        <button type="submit" class="dr-btn" style="width:100%;">Upload</button>
      </form>
      <div id="attachPreview" class="dr-attach-preview" style="display:none"></div>
    </div>
  </div>
@endsection

@section('script')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js"></script>
  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyByZgrvtQbWdEfRWf9hXRk4ZWiEP2mLFMk&libraries=places"></script>
  <script src="{{ asset('app-assets/js/scripts/tooltip/tooltip.js') }}"></script>
  <!-- Replace Feather with Lucide -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <script>
    window.DR_CUSTOMER_WORK_OPTIONS = @json($customerWorkOptions ?? []);
    // Tab Switching Function
    window.switchTab = function (tabId, btn) {
      document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.dr-tab-btn').forEach(el => el.classList.remove('active'));
      document.getElementById(tabId).classList.add('active');
      btn.classList.add('active');
    };

    /** Canonical code -> German label */
    const TYPE_I18N = {
      Task: 'Aufgabe', Appointment: 'Termin', Project: 'Projekt', Offer: 'Angebot',
      Problem: 'Ticket', Pause: 'Pause', Manual: 'Manuell', Missing: 'Fehlend'
    };
    const TYPE_REV = Object.fromEntries(Object.entries(TYPE_I18N).map(([k, v]) => [v, k]));
    function typeToLabel(code) { return TYPE_I18N[code] || code; }
    function labelToCode(label) { return TYPE_REV[label] || label; }

    const escapeHtml = s => (s || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));

    const DRModal = (() => {
      const focusable = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
      let lastFocus = null;
      function backdropId(id) { return id === 'reportHistoryModal' ? 'reportHistoryBackdrop' : `${id}Backdrop`; }
      function open(id) {
        const modal = document.getElementById(id);
        const backdrop = document.getElementById(backdropId(id));
        if (!modal) return;
        lastFocus = document.activeElement;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        backdrop?.classList.add('is-open');
        document.documentElement.style.overflow = 'hidden';
        setTimeout(() => modal.querySelector(focusable)?.focus(), 30);
      }
      function close(id) {
        const modal = document.getElementById(id);
        const backdrop = document.getElementById(backdropId(id));
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        backdrop?.classList.remove('is-open');
        document.documentElement.style.overflow = '';
        lastFocus?.focus?.();
      }
      document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-dr-modal-close]');
        if (trigger) close(trigger.getAttribute('data-dr-modal-close'));
      });
      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') document.querySelectorAll('.dr-modal.is-open').forEach(m => close(m.id));
      });
      return { open, close };
    })();

    const DRDialog = (() => {
      let counter = 0;
      function ensure(title, text, icon = 'info', options = {}) {
        counter += 1;
        const id = `drDialog${counter}`;
        const backdrop = document.createElement('div');
        backdrop.className = 'dr-modal-backdrop is-open';
        backdrop.id = `${id}Backdrop`;
        const modal = document.createElement('section');
        modal.className = 'dr-modal is-open';
        modal.id = id;
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.innerHTML = `
                  <div class="dr-modal-panel">
                    <header class="dr-modal-header">
                      <div style="display:flex;gap:12px;align-items:flex-start;">
                        <div class="dr-dialog-icon">${icon === 'success' ? '✓' : icon === 'warning' ? '!' : icon === 'error' ? '×' : 'i'}</div>
                        <div>
                          <h3 class="dr-modal-title">${escapeHtml(title || 'Hinweis')}</h3>
                          <p class="dr-modal-sub">${escapeHtml(text || '')}</p>
                        </div>
                      </div>
                      <button type="button" class="dr-icon-btn" data-action="cancel" aria-label="Schließen"><i data-lucide="x"></i></button>
                    </header>
                    <div class="dr-modal-body">
                      <div class="dr-dialog-actions">
                        ${options.showCancelButton ? `<button type="button" class="dr-btn-soft" data-action="cancel">${escapeHtml(options.cancelButtonText || 'Abbrechen')}</button>` : ''}
                        <button type="button" class="dr-btn ${icon === 'warning' ? 'dr-btn-danger' : ''}" data-action="confirm">${escapeHtml(options.confirmButtonText || 'OK')}</button>
                      </div>
                    </div>
                  </div>`;
        document.body.append(backdrop, modal);
        document.documentElement.style.overflow = 'hidden';
        lucide.createIcons();
        return new Promise(resolve => {
          const done = confirmed => {
            modal.remove(); backdrop.remove();
            document.documentElement.style.overflow = '';
            resolve({ isConfirmed: confirmed });
          };
          modal.addEventListener('click', e => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            done(btn.dataset.action === 'confirm');
          });
          backdrop.addEventListener('click', () => done(false));
        });
      }
      return {
        fire: (arg1, arg2, arg3) => typeof arg1 === 'object'
          ? ensure(arg1.title, arg1.text || arg1.html || '', arg1.icon || 'info', arg1)
          : ensure(arg1, arg2, arg3 || 'info', {})
      };
    })();

    window.Swal = DRDialog;

  </script>

  <script>
    /* ---------- Small helpers ---------- */
    const $D = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

    function getEmployeeId() {
      return ($('#employee_id').val() || "{{ $employee_id }}").toString().trim();
    }
    function getSelectedDate() {
      const fromHidden = $('#selected_date').val();
      const fromQS = new URLSearchParams(location.search).get('date');
      return fromQS || fromHidden || "{{ $start_date }}";
    }
    function setSelectedDate(iso) {
      $('#selected_date').val(iso);
      const url = new URL(location.href);
      url.searchParams.set('date', iso);
      history.replaceState(null, '', url.toString());
    }
    function snap5(hhmm) {
      if (!/^\d{2}:\d{2}$/.test(hhmm)) return hhmm;
      let [h, m] = hhmm.split(':').map(Number);
      m = Math.round(m / 5) * 5;
      if (m === 60) { h = (h + 1) % 24; m = 0; }
      return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
    }
    function timeToMin(t) {
      if (!/^\d{2}:\d{2}$/.test(String(t || ''))) return null;
      const [h, m] = String(t).split(':').map(Number);
      if (Number.isNaN(h) || Number.isNaN(m)) return null;
      return h * 60 + m;
    }

    function diffHours(start, end) {
      const a = timeToMin(start);
      const b = timeToMin(end);
      if (a === null || b === null) return 0;
      const minutes = b - a;
      if (minutes <= 0) return 0;
      return +(minutes / 60).toFixed(2);
    }

    function rowsOverlap(aStart, aEnd, bStart, bEnd) {
      const aS = timeToMin(aStart);
      const aE = timeToMin(aEnd);
      const bS = timeToMin(bStart);
      const bE = timeToMin(bEnd);
      if ([aS, aE, bS, bE].some(v => v === null)) return false;
      if (aE <= aS || bE <= bS) return false;
      return aS < bE && bS < aE;
    }

    function setLoading(btn, on = true) {
      if (!btn) return;
      const $btn = $(btn);
      if (on) {
        $btn.prop('disabled', true).attr('data-loading', '1');
        if (!$btn.data('orig')) $btn.data('orig', $btn.html());
        $btn.html('<span class="dr-spinner"></span> Bitte warten');
      } else {
        $btn.prop('disabled', false).removeAttr('data-loading');
        if ($btn.data('orig')) $btn.html($btn.data('orig'));
      }
    }

    function formatDE(num) {
      return Number(num || 0).toFixed(2).replace('.', ',');
    }

    function renderMonthAnalytics(employeeId) {
      const date = getSelectedDate();
      $.get(`/daily_report_month_analytics/${employeeId}?date=${date}`, function (res) {
        if (!res || !res.success) return;

        const coverage = Math.max(0, Math.min(100, Number(res.coverage_percent || 0)));
        $('#monthAnalyticsSubtitle').text(`${res.month_label} • ${res.total_days} Tage im Monat`);
        $('#bigCalendarMonthLabel').text(res.month_label);
        $('#monthProgressRing').css('--p', coverage).attr('data-value', `${coverage}%`);

        $('#monthCoverageText').text(`${res.reported_days} / ${res.total_days} Tage`);
        $('#monthOpenDaysText').text(`${res.missing_days} offene Tage`);
        $('#monthTotalDays').text(res.total_days);
        $('#monthReportedDays').text(res.reported_days);
        $('#monthMissingDays').text(res.missing_days);
        $('#monthMissingHours').text(formatDE(res.missing_hours));

        // Small Widget Dots
        const htmlDots = (res.days || []).map(day => {
          const cls = day.is_off_day ? 'off' : (day.is_future ? 'future' : (day.has_report ? 'done' : 'missing'));
          const title = `${day.date_label} • ${day.status_label} • Ist: ${formatDE(day.worked_hours)} Std. • Soll: ${formatDE(day.expected_hours)} Std.`;
          return `<button type="button" class="dr-month-day ${cls}" data-date="${day.date}" title="${title}">${day.day}</button>`;
        }).join('');
        $('#monthDaysGrid').html(htmlDots);

        // Big Calendar Tab Grid
        const grid = document.getElementById('calendarGrid');
        if (grid && res.days && res.days.length > 0) {
          let htmlCal = '';
          const firstDate = moment(res.days[0].date);
          const offset = (firstDate.isoWeekday() - 1);
          for (let i = 0; i < offset; i++) {
            htmlCal += `<div class="cal-day empty"></div>`;
          }
          res.days.forEach(day => {
            let cls = 'future';
            let statusHtml = '';
            if (day.is_off_day) {
              cls = 'weekend';
              statusHtml = '<span class="cal-status">Wochenende</span>';
            } else if (day.is_future) {
              cls = 'future';
              statusHtml = '<span class="cal-status">Zukünftig</span>';
            } else if (day.has_report) {
              cls = 'reported';
              statusHtml = '<span class="cal-status">Erfasst</span>';
            } else {
              cls = 'missing';
              statusHtml = '<span class="cal-status">Fehlend</span>';
            }
            htmlCal += `
                        <div class="cal-day ${cls}" data-date="${day.date}" onclick="$('#tab-kalender').removeClass('active'); $('#tab-tagesbericht').addClass('active'); $('.dr-tab-btn').removeClass('active'); $('.dr-tab-btn').first().addClass('active'); loadDay(getEmployeeId(), '${day.date}');">
                            <div class="date-num">${day.day}</div>
                            ${statusHtml}
                            <div style="font-size:10px; color:var(--dr-muted); margin-top:4px;">${formatDE(day.worked_hours)} / ${formatDE(day.expected_hours)} Std.</div>
                        </div>`;
          });
          grid.innerHTML = htmlCal;
        }
      });
    }

    /* ---------- Weekly ribbon + totals ---------- */
    function renderWeeklyReport(employeeId) {
      const selected = moment(getSelectedDate(), 'YYYY-MM-DD');
      const monday = selected.clone().startOf('isoWeek').format('YYYY-MM-DD');
      setSelectedDate(selected.format('YYYY-MM-DD'));

      $.get(`/weekly_report/${employeeId}?date=${monday}`, function (res) {
        if (!res || !Array.isArray(res.days)) return;

        const $container = $('.daily_report_row'); $container.empty();
        $('.week_title').text(`Woche ${moment(monday).isoWeek()}`);
        const wk = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];

        res.days.forEach(day => {
          const fullDate = day.full_date;
          const wd = moment(fullDate).isoWeekday();
          if (wd > 5) return;
          const isSel = fullDate === getSelectedDate();
          const badge = day.has_report ? 'success' : 'danger';
          const label = `${wk[wd - 1]}, ${moment(fullDate).format('DD.MM.')}`;
          const w = Number(day.worked || 0), e = Number(day.expected || 0), f = Number(day.fail || 0);

          $container.append(`
                      <div class="daily_card ml-1 ${isSel ? 'active' : ''}" data-date="${fullDate}">
                        <div class="daily_header">
                          <div class="title">
                            <div class="daily_date"><span class="daily_report">${label}</span></div>
                            <div class="status_date"><i data-lucide="circle" class="text-${badge}" style="width:14px;height:14px;color:var(--dr-${badge})"></i></div>
                          </div>
                        </div>
                        <div class="card-content">
                          <div class="card-body">
                            <p class="start-time">${w.toFixed(2)} Std.</p>
                            <p class="fail_time">${f.toFixed(2)} Std. fehlt</p>
                            <p class="end_time">${e.toFixed(2)} Std.</p>
                          </div>
                        </div>
                      </div>
                    `);
        });

        const totalWorked = res.days.reduce((s, d) => s + Number(d.worked || 0), 0);
        const totalExpected = res.days.reduce((s, d) => s + Number(d.expected || 0), 0);
        const totalFail = Math.max(0, totalExpected - totalWorked);
        $('.total_card .start-time').text(`${totalWorked.toFixed(2)} Std.`);
        $('.total_card .fail_time').text(`${totalFail.toFixed(2)} Std. fehlt`);
        $('.total_card .end_time').text(`${totalExpected.toFixed(2)} Std.`);
        lucide.createIcons();
      });
    }

    /* ---------- Day loader ---------- */
    function loadDay(employeeId, iso) {
      setSelectedDate(iso);
      $('.daily_card').removeClass('active');
      $(`.daily_card[data-date="${iso}"]`).addClass('active');

      $.get(`/daily_report_reload/${employeeId}/${iso}`, function (res) {
        if (!res?.success) { alert('No data.'); return; }
        $('#daily_report_tbody').html(res.html);
        initSelects(); initAutocomplete();
        setupContinuationPrompts();
        recalculateTotals();
        refreshAllCounters();
        renderMonthAnalytics(employeeId);
        lucide.createIcons();
      });
    }


    /* ---------- Multi-customer shares ---------- */
    function customerShareRowHours($share) {
      const s = snap5($share.find('.customer_share_start_input').val() || '');
      const e = snap5($share.find('.customer_share_end_input').val() || '');
      const h = diffHours(s, e);
      const hours = Math.max(0, h);
      $share.find('.customer_share_hours_input').val(hours > 0 ? hours.toFixed(2) : '');
      return hours;
    }

    function customerShareTotal($row) {
      let total = 0;
      $row.find('.customer-share').each(function () {
        total += customerShareRowHours($(this));
      });
      return total;
    }

    function refreshCustomerShareValidation($row) {
      const rowHours = rowCalculatedHours($row);
      const total = customerShareTotal($row);
      const invalid = total > rowHours + 0.004;
      let missingTime = false;

      $row.find('.customer-share').each(function () {
        const $share = $(this);
        const id = String($share.data('id'));
        const selected = ($row.find('select.customer-multi').val() || []).map(String).includes(id);
        const start = $share.find('.customer_share_start_input').val();
        const end = $share.find('.customer_share_end_input').val();
        const hours = Number($share.find('.customer_share_hours_input').val() || 0);
        const rowInvalid = selected && (!start || !end || hours <= 0);
        missingTime = missingTime || rowInvalid;
        $share.toggleClass('is-invalid', rowInvalid || invalid);
        const $warn = $share.find('.dr-customer-share-warning');
        if (rowInvalid) {
          $warn.addClass('is-visible').html('<i data-lucide="alert-triangle"></i><span>Bitte Start- und Endzeit für diesen Kunden eintragen.</span>');
        } else if (invalid) {
          $warn.addClass('is-visible').html(`<i data-lucide="alert-triangle"></i><span>Die Kundenzeiten (${total.toFixed(2).replace('.', ',')} Std.) überschreiten die Positionszeit (${rowHours.toFixed(2).replace('.', ',')} Std.).</span>`);
        } else {
          $warn.removeClass('is-visible').empty();
        }
      });

      let $total = $row.find('.dr-customer-share-total');
      const $box = $row.find('.customer-shares');
      if (!$total.length && $box.length) {
        $total = $('<div class="dr-customer-share-total" hidden><span></span><strong></strong></div>');
        $box.after($total);
      }

      if ($row.find('.customer-share').length) {
        const remaining = Math.max(0, rowHours - total);
        $total.prop('hidden', false)
          .toggleClass('is-danger', invalid || missingTime)
          .find('span').text('Kundenzeiten gesamt');
        $total.find('strong').text(`${total.toFixed(2).replace('.', ',')} / ${rowHours.toFixed(2).replace('.', ',')} Std. · Rest ${remaining.toFixed(2).replace('.', ',')} Std.`);
      } else {
        $total.prop('hidden', true).removeClass('is-danger');
      }

      $row.toggleClass('is-customer-share-invalid', invalid || missingTime);
      if (window.lucide) lucide.createIcons();
      return { ok: !invalid && !missingTime, total, rowHours, missingTime, invalid };
    }

    function escapeAttr(value) {
      return escapeHtml(value == null ? '' : String(value)).replace(/`/g, '&#096;');
    }

    function customerWorkOptions(customerId) {
      const all = window.DR_CUSTOMER_WORK_OPTIONS || {};
      return all[String(customerId)] || all[Number(customerId)] || [];
    }

    function buildCustomerObjectOptions(customerId, selectedAlternativeId = '') {
      const seen = new Set();
      const options = customerWorkOptions(customerId).filter(opt => opt && opt.alternative_id).filter(opt => {
        const key = String(opt.alternative_id);
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
      });

      return '<option value="">Objekt wählen</option>' + options.map(opt => {
        const id = String(opt.alternative_id || '');
        const selected = String(selectedAlternativeId || '') === id ? ' selected' : '';
        return `<option value="${escapeAttr(id)}"${selected}>${escapeHtml(opt.object_label || ('Objekt #' + id))}</option>`;
      }).join('');
    }

    function buildCustomerProductOptions(customerId, selectedAlternativeId = '', selectedLeadProductListId = '') {
      const items = customerWorkOptions(customerId);
      return '<option value="">Produkt wählen</option>' + items.map(opt => {
        const lplId = String(opt.lead_product_list_id || '');
        const alternativeId = String(opt.alternative_id || '');
        const productId = String(opt.product_id || '');
        const selected = String(selectedLeadProductListId || '') === lplId ? ' selected' : '';
        const hidden = selectedAlternativeId && alternativeId !== String(selectedAlternativeId) ? ' hidden' : '';
        return `<option value="${escapeAttr(lplId)}" data-alternative-id="${escapeAttr(alternativeId)}" data-product-id="${escapeAttr(productId)}"${selected}${hidden}>${escapeHtml(opt.product_label || ('Produkt #' + productId))}</option>`;
      }).join('');
    }

    function refreshCustomerProductSelect($share) {
      const customerId = String($share.data('id') || '');
      const selectedAlternative = $share.find('.customer_object_select').val() || '';
      const $productSelect = $share.find('.customer_product_select');
      const currentProductRow = $productSelect.val() || '';
      $productSelect.html(buildCustomerProductOptions(customerId, selectedAlternative, currentProductRow));
      if ($productSelect.find(`option[value="${currentProductRow}"]:not([hidden])`).length) {
        $productSelect.val(currentProductRow);
      } else {
        $productSelect.val('');
        $share.find('.customer_product_id_input').val('');
      }
    }

    function syncCustomerProductId($share) {
      const $selected = $share.find('.customer_product_select option:selected');
      const productId = $selected.data('product-id') || '';
      const alternativeId = $selected.data('alternative-id') || '';
      $share.find('.customer_product_id_input').val(productId || '');
      if (alternativeId && !$share.find('.customer_object_select').val()) {
        $share.find('.customer_object_select').val(String(alternativeId));
        refreshCustomerProductSelect($share);
      }
    }

    function renderShares($row) {
      const ids = ($row.find('select.customer-multi').val() || []).map(String);
      const $box = $row.find('.customer-shares');
      const existing = new Set(
        $box.find('.customer-share').map(function () {
          return String($(this).data('id'));
        }).get()
      );

      $box.find('.customer-share').each(function () {
        if (!ids.includes(String($(this).data('id')))) $(this).remove();
      });

      const entryId = $row.data('id') || '';
      const dateVal = $('#selected_date').val() || "{{ $start_date ?? '' }}";
      const defaultStart = $row.find('[name="start_time"], .start_time_input').val() || '';
      const defaultEnd = $row.find('[name="end_time"], .end_time_input').val() || '';
      const selectedCount = ids.length || 1;
      const rowHours = rowCalculatedHours($row);

      ids.forEach(id => {
        if (existing.has(id)) return;
        const name = $row.find(`select.customer-multi option[value="${id}"]`).text().trim();
        const defaultHours = selectedCount === 1 ? rowHours.toFixed(2) : '';
        $box.append(`
                    <div class="customer-share dr-customer-share" data-id="${id}">
                      <div class="dr-customer-name">${escapeHtml(name)}</div>
                      <div class="dr-customer-object-product">
                        <select name="alternative_id[${id}]" class="dr-control customer_object_select" data-customer-id="${id}">${buildCustomerObjectOptions(id)}</select>
                        <select name="lead_product_list_id[${id}]" class="dr-control customer_product_select" data-customer-id="${id}">${buildCustomerProductOptions(id)}</select>
                        <input type="hidden" name="product_id[${id}]" class="customer_product_id_input" value="">
                      </div>
                      <div class="dr-customer-time-pair">
                        <input type="time" name="share_start_time[${id}]" class="dr-control customer_share_start_input" value="${selectedCount === 1 ? defaultStart : ''}" title="Startzeit Kunde">
                        <span>–</span>
                        <input type="time" name="share_end_time[${id}]" class="dr-control customer_share_end_input" value="${selectedCount === 1 ? defaultEnd : ''}" title="Endzeit Kunde">
                      </div>
                      <div>
                        <input type="number" step="0.01" min="0" name="share_hours[${id}]" class="dr-control customer_share_hours_input dr-customer-share-hours" placeholder="Std." value="${defaultHours}" readonly>
                      </div>
                      <div class="dr-customer-note-actions">
                        <input type="text" name="customer_note[${id}]" class="dr-control" placeholder="Notiz">
                        <button type="button" class="dr-row-action ghost btn-notes" title="Notizen" data-date="${dateVal}" data-entry="${entryId || '__null'}">
                          <i data-lucide="message-square"></i>
                        </button>
                        <button type="button" class="dr-row-action ghost btn-attach" title="Anhänge" data-date="${dateVal}" data-entry="${entryId}">
                          <i data-lucide="paperclip"></i>
                        </button>
                      </div>
                      <div class="dr-customer-share-warning"></div>
                    </div>
                  `);
      });
      refreshCustomerShareValidation($row);
      lucide.createIcons();
    }

    function initSelects() {
      $('.select2').select2({ width: '100%' });
      $(document).off('change.customerMulti').on('change.customerMulti', 'select.customer-multi', function () {
        renderShares($(this).closest('tr'));
      });

      $(document).off('change.customerObjectProduct')
        .on('change.customerObjectProduct', '.customer_object_select', function () {
          const $share = $(this).closest('.customer-share');
          refreshCustomerProductSelect($share);
        })
        .on('change.customerProductRow', '.customer_product_select', function () {
          syncCustomerProductId($(this).closest('.customer-share'));
        });
      $('#daily_report_tbody tr').each(function () {
        if ($(this).find('select.customer-multi').length) renderShares($(this));
        $(this).find('.customer-share').each(function () {
          syncCustomerProductId($(this));
        });
      });
    }

    function initAutocomplete() {
      $('.autocomplete-address').each(function () {
        if (this._gmAuto) return;
        this._gmAuto = new google.maps.places.Autocomplete(this, {
          types: ['geocode'], componentRestrictions: { country: "de" }
        });
      });
    }

    function validateRow($row) {
      const sRaw = $row.find('[name="start_time"]').val() || $row.find('.start_time_input').val() || '';
      const eRaw = $row.find('[name="end_time"]').val() || $row.find('.end_time_input').val() || '';
      const s = snap5(sRaw); const e = snap5(eRaw);

      if (!/^\d{2}:\d{2}$/.test(s) || !/^\d{2}:\d{2}$/.test(e)) {
        return { ok: false, msg: 'Time must be HH:MM' };
      }
      const h = diffHours(s, e);
      if (h <= 0) return { ok: false, msg: 'Endzeit muss nach der Startzeit liegen.' };

      $row.find('[name="hours_spent"],[name="total_time"],.hours_spent_input').val(h.toFixed(2)).addClass('is-auto-calculated');

      const currentId = $row.data('id') ? String($row.data('id')) : null;
      let conflictInfo = null;

      $('#daily_report_tbody tr.daily_report_tr').each(function () {
        const $r = $(this);
        if (this === $row[0] || $r.hasClass('missing-row')) return;
        const otherIdRaw = $r.data('id');
        if (!otherIdRaw) return;
        const otherId = String(otherIdRaw);
        if (currentId && otherId === currentId) return;
        const rs = $r.find('[name="start_time"],.start_time_input').val();
        const re = $r.find('[name="end_time"],.end_time_input').val();
        if (!rs || !re) return;

        if (rowsOverlap(s, e, rs, re)) {
          conflictInfo = { id: otherId, start: rs, end: re, type: $r.data('type') || '' };
          return false;
        }
      });

      if (conflictInfo) {
        return { ok: false, msg: `Überschneidung mit anderem Eintrag (${conflictInfo.start}–${conflictInfo.end})` };
      }
      $row.find('[name="start_time"],.start_time_input').val(s);
      $row.find('[name="end_time"],.end_time_input').val(e);
      return { ok: true };
    }

    function reloadReport() {
      const emp = getEmployeeId();
      const date = getSelectedDate();
      $.get(`/daily_report_reload/${emp}/${date}`, function (res) {
        if (!res?.success) { Swal.fire('Error', 'Reload failed.', 'error'); return; }
        $('#daily_report_tbody').html(res.html);
        if (res.expectedHours !== undefined) $('#expected_hours_per_day').val(Number(res.expectedHours).toFixed(2));
        initSelects(); initAutocomplete();
        setupContinuationPrompts();
        recalculateTotals();
        refreshAllCounters();
        lucide.createIcons();
      });
    }


    /* ---------- Planned context + continue-after-pause helper ---------- */
    const CONTINUABLE_TYPES = ['Task', 'Appointment', 'Problem', 'Project', 'Offer', 'Manual'];

    function rowType($row) {
      return String($row.find('[name="type"]').val() || $row.data('type') || '').trim();
    }
    function rowLabel($row) { return typeToLabel(rowType($row)); }
    function rowHours($row) {
      const s = snap5($row.find('[name="start_time"],.start_time_input').val() || '');
      const e = snap5($row.find('[name="end_time"],.end_time_input').val() || '');
      return diffHours(s, e);
    }
    function rowTitleText($row) {
      const sourceTitle = $row.find('.source_title_input').val() || $row.data('source-title') || '';
      const label = rowLabel($row);
      return sourceTitle && sourceTitle !== label ? `${label} – ${sourceTitle}` : label;
    }
    function refreshRowSummary($row) {
      const s = snap5($row.find('[name="start_time"],.start_time_input').val() || '');
      const e = snap5($row.find('[name="end_time"],.end_time_input').val() || '');
      const h = diffHours(s, e);
      const address = $row.find('[name="address"]').val() || 'Kein Arbeitsort gesetzt';
      const sourceDesc = $row.find('.source_description_input').val() || $row.data('source-description') || '';
      if (h > 0) $row.find('.dr-summary-time').text(`${s || '--:--'} – ${e || '--:--'} · ${h.toFixed(2).replace('.', ',')} Std.`);
      $row.find('.dr-summary-address').text(address);
      if (sourceDesc && $row.find('.dr-source-summary').length) {
        $row.find('.dr-source-summary').text(sourceDesc.length > 120 ? `${sourceDesc.slice(0, 117)}...` : sourceDesc);
      }
    }
    function findContinuationSourceRow($target) {
      const $pause = $target.prevAll('tr.daily_report_tr').first();
      if (!$pause.length || rowType($pause) !== 'Pause') return $();
      return $pause.prevAll('tr.daily_report_tr').filter(function () {
        const t = rowType($(this));
        return CONTINUABLE_TYPES.includes(t) && t !== 'Pause' && !$(this).hasClass('missing-row');
      }).first();
    }
    function copyJobDetailsFromPrevious($source, $target) {
      const sourceType = rowType($source) || 'Manual';
      $target.find('[name="type"]').val(sourceType).trigger('change.select2');
      $target.attr('data-type', sourceType).data('type', sourceType);
      $target.removeClass('missing-row').attr('data-missing', '0').data('missing', '0');
      $target.find('.dr-entry-card').removeClass('is-missing');
      $target.find('.dr-mini-badge.missing').remove();
      $target.find('[name="address"]').val($source.find('[name="address"]').val() || '');
      $target.find('[name="billing_type"]').val($source.find('[name="billing_type"]').val() || '').trigger('change');
      $target.find('[name="activity_category"]').val($source.find('[name="activity_category"]').val() || '');
      $target.find('.is_travel_input').prop('checked', $source.find('.is_travel_input').is(':checked'));
      const reportableType = $source.find('[name="reportable_type"]').val() || $source.data('reportable-type') || '';
      const reportableId = $source.find('[name="reportable_id"]').val() || $source.data('reportable-id') || '';
      $target.find('[name="reportable_type"]').val(reportableType);
      $target.find('[name="reportable_id"]').val(reportableId);
      $target.attr('data-reportable-type', reportableType).data('reportable-type', reportableType);
      $target.attr('data-reportable-id', reportableId).data('reportable-id', reportableId);
      const sourceTitle = $source.find('.source_title_input').val() || $source.data('source-title') || rowTitleText($source);
      const sourceDescription = $source.find('.source_description_input').val() || $source.data('source-description') || '';
      $target.find('.source_title_input').val(sourceTitle);
      $target.find('.source_description_input').val(sourceDescription);
      $target.attr('data-source-title', sourceTitle).data('source-title', sourceTitle);
      $target.attr('data-source-description', sourceDescription).data('source-description', sourceDescription);
      const ids = $source.find('select.customer-multi').val() || [];
      $target.find('select.customer-multi').val(ids).trigger('change.select2');
      renderShares($target);
      if (ids.length === 1) {
        const onlyId = ids[0];
        $target.find(`[name="share_start_time[${onlyId}]"]`).val($target.find('[name="start_time"], .start_time_input').val() || '');
        $target.find(`[name="share_end_time[${onlyId}]"]`).val($target.find('[name="end_time"], .end_time_input').val() || '');
        customerShareRowHours($target.find(`.customer-share[data-id="${onlyId}"]`));
      }
      ids.forEach(id => $target.find(`[name="customer_note[${id}]"]`).val($source.find(`[name="customer_note[${id}]"]`).val() || ''));
      refreshCustomerShareValidation($target);
      $target.find('[name="description"], .description_input').val('').attr('placeholder', 'Hier den Bericht für die Fortsetzung nach der Pause schreiben...');
      const $label = $target.find('.dr-entry-type-label');
      $label.text(typeToLabel(sourceType));
      if (sourceTitle && !$target.find('.dr-entry-source-title').length) $label.after(`<span class="dr-entry-source-title"></span>`);
      $target.find('.dr-entry-source-title').text(sourceTitle && sourceTitle !== typeToLabel(sourceType) ? sourceTitle : '');
      if (sourceDescription && !$target.find('.dr-source-context').length) {
        $target.find('.description-group').prepend(`<div class="dr-source-context"><div class="dr-source-context-title"><i data-lucide="info"></i> Geplante Beschreibung aus ${escapeHtml(typeToLabel(sourceType))}</div><div class="dr-source-context-text"></div></div>`);
      }
      $target.find('.dr-source-context-text').text(sourceDescription);
      refreshRowSummary($target); recalculateTotals(); if (window.lucide) lucide.createIcons();
    }
    function setupContinuationPrompts() {
      $('#daily_report_tbody .dr-continue-prompt').remove();
      $('#daily_report_tbody tr.daily_report_tr').each(function () {
        const $row = $(this); const t = rowType($row);
        if (!['Missing', 'Manual', 'Other', ''].includes(t)) return;
        if ($row.data('continuation-choice')) return;
        const $source = findContinuationSourceRow($row);
        if (!$source.length) return;
        const sourceTitle = rowTitleText($source);
        const sourceTime = $source.find('.dr-summary-time').text().trim();
        $row.find('.dr-entry-top').after(`<div class="dr-continue-prompt" data-continuation-prompt><div class="dr-continue-prompt-main"><span class="dr-continue-prompt-icon"><i data-lucide="corner-down-right"></i></span><div><div class="dr-continue-prompt-title">Nach der Pause weiterarbeiten?</div><div class="dr-continue-prompt-sub">Vorheriger Eintrag: ${escapeHtml(sourceTitle)}${sourceTime ? ' • ' + escapeHtml(sourceTime) : ''}</div></div></div><div class="dr-continue-prompt-actions"><button type="button" class="dr-btn dr-continue-yes">Ja, Details übernehmen</button><button type="button" class="dr-btn-soft dr-continue-no">Nein, neuer Eintrag</button></div></div>`);
      });
      if (window.lucide) lucide.createIcons();
    }


    function unlockRelatedReport($row, mode) {
      const $textarea = $row.find('.description_input');
      const action = mode === 'agree' ? 'agree_existing' : 'add_mine';
      $row.find('.related_report_action_input').val(action);

      if (action === 'agree_existing') {
        const msg = 'Ich stimme dem bereits erstellten Bericht zu.';
        $textarea
          .prop('readonly', true)
          .removeClass('is-related-locked is-related-blocked is-related-unlocked')
          .addClass('is-related-agreed')
          .val(msg)
          .attr('placeholder', msg);

        $row.find('.dr-mini-badge.locked').html('<i data-lucide="check-circle"></i> Bestätigung');
        $row.find('.dr-mini-badge.other-report').html('<i data-lucide="check-circle"></i> Bestätigung');
        $row.find('.dr-report-lock-box').slideUp(160);
        if (window.lucide) lucide.createIcons();
        return;
      }

      $textarea
        .prop('readonly', false)
        .removeClass('is-related-locked is-related-blocked is-related-agreed')
        .addClass('is-related-unlocked')
        .val('')
        .attr('placeholder', 'Hier deinen eigenen zusätzlichen Bericht schreiben. Dieser wird als neuer Bericht gespeichert und ersetzt keinen vorhandenen Bericht.');

      $row.find('.dr-report-lock-box').slideUp(160);
      $row.find('.dr-mini-badge.locked').html('<i data-lucide="plus-circle"></i> Neuer Bericht');
      $row.find('.dr-mini-badge.other-report').html('<i data-lucide="plus-circle"></i> Neuer Bericht');
      $textarea.focus();
      if (window.lucide) lucide.createIcons();
    }


    function rowCalculatedHours($row) {
      const s = snap5($row.find('[name="start_time"],.start_time_input').val() || '');
      const e = snap5($row.find('[name="end_time"],.end_time_input').val() || '');
      return Math.max(0, diffHours(s, e));
    }

    function totalHoursExceptRow($currentRow) {
      let total = 0;
      $('#daily_report_tbody tr.daily_report_tr').each(function () {
        const $row = $(this);
        if ($row.is($currentRow) || $row.hasClass('missing-row')) return;
        total += rowCalculatedHours($row);
      });
      return total;
    }

    function allowedHoursForRow($row) {
      const expected = Number($('#expected_hours_per_day').val() || 8);
      const otherTotal = totalHoursExceptRow($row);
      return Math.max(0, expected - otherTotal);
    }

    function markRowTimeState($row) {
      const rowHours = rowCalculatedHours($row);
      const expected = Number($('#expected_hours_per_day').val() || 8);
      const otherTotal = totalHoursExceptRow($row);
      const overtime = Math.max(0, otherTotal + rowHours - expected);

      // Day overtime is allowed. Only invalid start/end times or customer-share overflow should block save.
      $row.removeClass('is-time-invalid').toggleClass('is-overtime-row', overtime > 0.004);
      if (!$row.hasClass('is-customer-share-invalid')) {
        $row.find('.saveRow').prop('disabled', false).removeClass('is-disabled');
      }

      return { invalid: false, rowHours, allowed: Infinity, overtime };
    }


    function setRowAfterSave($row, id) {
      if (!id) return;

      $row.attr('data-id', id).data('id', id);
      $row.removeClass('missing-row');

      const $saveBtn = $row.find('.saveRow');
      $saveBtn.attr('data-id', id).data('id', id);
      $saveBtn.find('span').text('Speichern');
      $saveBtn.find('i').attr('data-lucide', 'save');

      const $notesBtn = $row.find('.btn-notes');
      $notesBtn.attr('data-entry', id).data('entry', id);

      const $attachBtn = $row.find('.btn-attach');
      $attachBtn.attr('data-entry', id).data('entry', id);

      $row.find('.count-badge').attr('data-entry', id);

      const $actions = $row.find('.dr-entry-actions').first();

      if (!$actions.find('.editRow').length) {
        $actions.prepend(`
          <button type="button"
                  class="dr-row-action ghost editRow"
                  data-id="${id}"
                  title="Eintrag bearbeiten">
            <i data-lucide="pencil"></i>
            <span>Bearbeiten</span>
          </button>
        `);
      }

      if (!$actions.find('.deleteRow').length) {
        $saveBtn.after(`
          <button type="button"
                  class="dr-row-action danger deleteRow"
                  data-id="${id}"
                  title="Löschen">
            <i data-lucide="trash-2"></i>
            <span>Löschen</span>
          </button>
        `);
      }

      $row.find('.dr-mini-badge.source').removeClass('source').addClass('saved').text('Gespeichert');

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    function openRowForEdit($row) {
      const $body = $row.find('.dr-entry-body').first();
      const $toggle = $row.find('.dr-entry-toggle').first();

      $body.prop('hidden', false);
      $toggle.attr('aria-expanded', 'true');

      $row.addClass('is-editing');

      $row.find('input, textarea, select, button').prop('disabled', false);
      $row.find('.saveRow').prop('disabled', false).removeClass('is-disabled');

      const firstField = $row.find('.description_input, [name="start_time"], .start_time_input').filter(':visible').first();
      if (firstField.length) {
        firstField.trigger('focus');
      }

      if (window.lucide) {
        window.lucide.createIcons();
      }
    }

    function saveRow($row, btn, endpoint) {
      const v = validateRow($row);
      if (!v.ok) { Swal.fire('Hinweis', v.msg, 'warning'); return; }

      if ($row.find('.description_input.is-related-blocked').length && ($row.find('.related_report_action_input').val() || 'auto') === 'auto') {
        Swal.fire('Hinweis', 'Dieser Termin / diese Aufgabe / dieses Ticket wurde bereits von einem anderen Mitarbeiter berichtet. Bitte wähle zuerst „Neuen eigenen Bericht schreiben“ oder „Ich stimme dem Bericht zu“. Der vorhandene Bericht wird nicht ersetzt.', 'warning');
        return;
      }

      const ids = $row.find('select.customer-multi').val() || [];
      const shareHours = {}; const sharePercent = {}; const customerNote = {}; const shareStartTime = {}; const shareEndTime = {};
      const alternativeId = {}; const leadProductListId = {}; const productId = {};

      const shareValidation = refreshCustomerShareValidation($row);
      if (!shareValidation.ok) {
        Swal.fire('Hinweis', shareValidation.invalid
          ? `Die Kundenzeiten (${shareValidation.total.toFixed(2).replace('.', ',')} Std.) dürfen nicht höher sein als die Positionszeit (${shareValidation.rowHours.toFixed(2).replace('.', ',')} Std.).`
          : 'Bitte bei jedem ausgewählten Kunden Start- und Endzeit eintragen.', 'warning');
        return;
      }

      ids.forEach(id => {
        const h = $row.find(`[name="share_hours[${id}]"]`).val();
        const p = $row.find(`[name="share_percent[${id}]"]`).val();
        const n = $row.find(`[name="customer_note[${id}]"]`).val();
        const ss = $row.find(`[name="share_start_time[${id}]"]`).val();
        const se = $row.find(`[name="share_end_time[${id}]"]`).val();
        const alt = $row.find(`[name="alternative_id[${id}]"]`).val();
        const lpl = $row.find(`[name="lead_product_list_id[${id}]"]`).val();
        const prod = $row.find(`[name="product_id[${id}]"]`).val();
        if (h !== undefined && h !== '') shareHours[id] = h;
        if (p !== undefined && p !== '') sharePercent[id] = p;
        if (n !== undefined && n !== '') customerNote[id] = n;
        if (ss !== undefined && ss !== '') shareStartTime[id] = ss;
        if (se !== undefined && se !== '') shareEndTime[id] = se;
        if (alt !== undefined && alt !== '') alternativeId[id] = alt;
        if (lpl !== undefined && lpl !== '') leadProductListId[id] = lpl;
        if (prod !== undefined && prod !== '') productId[id] = prod;
      });

      const start = $row.find('[name="start_time"], .start_time_input').val();
      const end = $row.find('[name="end_time"], .end_time_input').val();
      const hours = diffHours(start, end).toFixed(2);
      $row.find('[name="hours_spent"], [name="total_time"], .hours_spent_input').val(hours).addClass('is-auto-calculated');

      const payloadBase = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        employee_id: getEmployeeId(), date: getSelectedDate(),
        start_time: start, end_time: end,
        work_place_id: $row.find('[name="work_place_id"]').val() || null,
        type: $row.find('[name="type"]').val() || 'Manual',
        description: $row.find('[name="description"], .description_input').val() || '',
        address: $row.find('[name="address"]').val() || null,
        reportable_type: $row.find('[name="reportable_type"]').val() || $row.data('reportable-type') || null,
        reportable_id: $row.find('[name="reportable_id"]').val() || $row.data('reportable-id') || null,
        billing_type: $row.find('[name="billing_type"]').val() || null,
        activity_category: $row.find('[name="activity_category"]').val() || null,
        is_travel: $row.find('.is_travel_input').is(':checked') ? 1 : 0,
        related_report_action: $row.find('.related_report_action_input').val() || 'auto',
        customer_ids: ids,
        share_hours: shareHours,
        share_percent: sharePercent,
        customer_note: customerNote,
        share_start_time: shareStartTime,
        share_end_time: shareEndTime,
        alternative_id: alternativeId,
        lead_product_list_id: leadProductListId,
        product_id: productId,
        id: $row.data('id') || undefined
      };

      const payload = (endpoint === 'add_missing') ? { ...payloadBase, hours_spent: hours } : { ...payloadBase, hours: hours };

      // Daily overtime is allowed and is calculated as Überstunden.
      // Save is blocked only when customer sub-times exceed this row's own time.
      refreshRowTimeWarning($row);

      if (btn) setLoading(btn, true);

      $.post(endpoint === 'add_missing' ? "{{ route('daily.report.add_missing') }}" : "{{ route('daily.report.save') }}", payload)
        .done(res => {
          if (!res?.success && endpoint === 'save') {
            Swal.fire('Error', res?.message || 'Speichern fehlgeschlagen.', 'error');
            return;
          }

          if (res?.id) {
            setRowAfterSave($row, res.id);
          }

          Swal.fire('Gespeichert', res?.related_report_saved ? 'Eintrag und Modulbericht wurden gespeichert.' : 'Eintrag wurde gespeichert.', 'success');
          reloadReport();
        })
        .fail(xhr => { Swal.fire('Error', xhr.responseJSON?.message || 'Speichern fehlgeschlagen.', 'error'); })
        .always(() => { if (btn) setLoading(btn, false); });
    }

    const recalcDebounced = debounce(recalculateTotals, 150);
    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); } }

    function refreshRowTimeWarning($row) {
      const expected = Number($('#expected_hours_per_day').val() || 8);
      const s = snap5($row.find('[name="start_time"],.start_time_input').val() || '');
      const e = snap5($row.find('[name="end_time"],.end_time_input').val() || '');
      const rowHours = diffHours(s, e);
      const otherTotal = totalHoursExceptRow($row);
      const projectedTotal = otherTotal + rowHours;
      const remainingAfterRow = Math.max(0, expected - projectedTotal);
      const overtimeAfterRow = Math.max(0, projectedTotal - expected);
      const $warning = $row.find('.dr-row-time-warning');

      if (!$warning.length) return;

      if (!s || !e || rowHours <= 0) {
        $warning.prop('hidden', true).removeClass('is-danger is-overtime').text('');
        markRowTimeState($row);
        return;
      }

      if (overtimeAfterRow > 0.004) {
        $warning
          .prop('hidden', false)
          .removeClass('is-danger')
          .addClass('is-overtime')
          .html(`<i data-lucide="clock-plus"></i><span>Diese Position erzeugt Überstunden. Tages-Sollzeit: ${expected.toFixed(2).replace('.', ',')} Std., geplant/erfasst: ${projectedTotal.toFixed(2).replace('.', ',')} Std., Überstunden: ${overtimeAfterRow.toFixed(2).replace('.', ',')} Std.</span>`);
      } else if (remainingAfterRow > 0.004) {
        $warning
          .prop('hidden', false)
          .removeClass('is-danger is-overtime')
          .html(`<i data-lucide="clock"></i><span>Nach dieser Position bleiben noch ${remainingAfterRow.toFixed(2).replace('.', ',')} Std. bis zur Soll-Zeit offen.</span>`);
      } else {
        $warning
          .prop('hidden', false)
          .removeClass('is-danger')
          .addClass('is-overtime')
          .html(`<i data-lucide="check-circle"></i><span>Die Soll-Zeit ist mit dieser Position genau erfüllt.</span>`);
      }

      markRowTimeState($row);
      refreshCustomerShareValidation($row);
    }

    function recalculateTotals() {
      let total = 0;
      $('#daily_report_tbody tr.daily_report_tr').each(function () {
        const $row = $(this);
        if ($row.hasClass('missing-row')) return;
        const s = snap5($row.find('[name="start_time"],.start_time_input').val() || '');
        const e = snap5($row.find('[name="end_time"],.end_time_input').val() || '');
        const calculated = diffHours(s, e);
        if (calculated > 0) {
          $row.find('[name="hours_spent"],[name="total_time"],.hours_spent_input').val(calculated.toFixed(2)).addClass('is-auto-calculated');
          $row.data('last-hours', calculated);
          total += calculated;
        }
      });

      const expected = Number($('#expected_hours_per_day').val() || 8);
      const missing = Math.max(0, expected - total);
      const overtime = Math.max(0, total - expected);
      const fillPercent = Math.min(100, Math.max(0, (total / Math.max(expected, 0.01)) * 100));

      $('#worked_total').text(total.toFixed(2).replace('.', ',') + ' Std.');
      $('#missing_hours').text(missing.toFixed(2).replace('.', ',') + ' Std.');
      $('#overtime_hours').text(overtime.toFixed(2).replace('.', ',') + ' Std.');
      $('#overtime_stat_card').toggleClass('is-overtime', overtime > 0.004);
      $('.dr-total-worked-inline').text(total.toFixed(2).replace('.', ',') + ' Std.');

      const $totalBox = $('.dr-total-box');
      let $limitWarning = $('.dr-time-limit-warning');
      if (!$limitWarning.length && $totalBox.length) {
        $limitWarning = $('<div class="dr-time-limit-warning" hidden></div>');
        $totalBox.after($limitWarning);
      }

      $totalBox.toggleClass('is-over-limit', overtime > 0.004);
      if (overtime > 0.004) {
        $limitWarning
          .prop('hidden', false)
          .removeClass('is-danger')
          .addClass('is-overtime')
          .html(`<i data-lucide="clock-plus"></i><span>Überstunden: ${overtime.toFixed(2).replace('.', ',')} Std. über der Soll-Zeit (${expected.toFixed(2).replace('.', ',')} Std.). Restzeit bleibt 0,00 Std.</span>`);
      } else if (missing > 0.004) {
        $limitWarning
          .prop('hidden', false)
          .removeClass('is-danger is-overtime')
          .html(`<i data-lucide="clock"></i><span>Noch ${missing.toFixed(2).replace('.', ',')} Std. offen bis zur Soll-Zeit.</span>`);
      } else {
        $limitWarning
          .prop('hidden', false)
          .removeClass('is-danger')
          .addClass('is-overtime')
          .html(`<i data-lucide="check-circle"></i><span>Soll-Zeit erfüllt. Keine Restzeit offen.</span>`);
      }

      $('#daily_report_tbody tr.daily_report_tr').each(function () {
        refreshRowTimeWarning($(this));
      });

      if (window.lucide) lucide.createIcons();

      const fillBar = document.getElementById('remainingFill');
      const remainingText = document.getElementById('remainingTimeAnimated');
      const progressDesc = document.getElementById('progressText');
      const card = document.querySelector('.remaining-time-card');
      if (fillBar && remainingText && progressDesc) {
        fillBar.style.width = fillPercent + '%';
        remainingText.textContent = overtime > 0.004
          ? '+' + overtime.toFixed(2).replace('.', ',')
          : missing.toFixed(2).replace('.', ',');

        if (overtime > 0.004) {
          progressDesc.textContent = `Du hast ${total.toFixed(2).replace('.', ',')} Std. erfasst. Überstunden: ${overtime.toFixed(2).replace('.', ',')} Std.`;
          fillBar.style.background = 'var(--dr-warning)';
          if (card) card.style.boxShadow = '0 0 20px rgba(245, 158, 11, 0.35)';
        } else {
          progressDesc.textContent = `Du hast ${total.toFixed(2).replace('.', ',')} von ${expected.toFixed(2).replace('.', ',')} Stunden erfasst. Restzeit: ${missing.toFixed(2).replace('.', ',')} Std.`;
          fillBar.style.background = fillPercent >= 100 ? 'var(--dr-success)' : 'var(--dr-primary)';
          if (card) card.style.boxShadow = fillPercent >= 100 ? '0 0 20px rgba(16, 185, 129, 0.4)' : '0 10px 30px rgba(0,0,0,0.15)';
        }
      }
    }


    $(document).ready(function () {
      setSelectedDate(getSelectedDate());
      renderWeeklyReport(getEmployeeId());
      renderMonthAnalytics(getEmployeeId());
      initSelects(); initAutocomplete();
      setupContinuationPrompts();
      recalculateTotals();
      refreshAllCounters();
      lucide.createIcons();

      $(document).on('click', '.daily_card[data-date]', function () { loadDay(getEmployeeId(), $(this).data('date')); });
      $(document).on('click', '#refreshMonthAnalytics', function () { renderMonthAnalytics(getEmployeeId()); });
      $(document).on('click', '.dr-month-day[data-date]', function () {
        const iso = $(this).data('date'); if (!iso) return;
        loadDay(getEmployeeId(), iso); renderWeeklyReport(getEmployeeId()); renderMonthAnalytics(getEmployeeId());
      });

      function shiftWeek(days) {
        const newDate = moment(getSelectedDate()).add(days, 'days').startOf('isoWeek').format('YYYY-MM-DD');
        setSelectedDate(newDate); renderWeeklyReport(getEmployeeId()); renderMonthAnalytics(getEmployeeId()); loadDay(getEmployeeId(), newDate);
      }
      $('#btnPrevWeek').on('click', () => shiftWeek(-7));
      $('#btnNextWeek').on('click', () => shiftWeek(7));

      $(document).on('click', '.dr-add-own-report', function () {
        unlockRelatedReport($(this).closest('tr'), 'add');
      });

      $(document).on('click', '.dr-agree-report', function () {
        unlockRelatedReport($(this).closest('tr'), 'agree');
      });

      $(document).on('input change', '.customer_share_start_input, .customer_share_end_input', function () {
        const $row = $(this).closest('tr');
        customerShareRowHours($(this).closest('.customer-share'));
        refreshCustomerShareValidation($row);
      });

      $(document).on('click', '.saveRow', function () {
        const $row = $(this).closest('tr');
        const endpoint = $row.hasClass('missing-row') || !$row.data('id') ? 'add_missing' : 'save';
        saveRow($row, this, endpoint);
      });

      $(document).on('click', '.editRow', function () {
        const $row = $(this).closest('tr');
        openRowForEdit($row);
      });

      $(document).on('click', '.deleteRow', function () {
        const btn = this;
        const $row = $(btn).closest('tr');
        const id = $(btn).data('id') || $row.data('id');
        const token = $('meta[name="csrf-token"]').attr('content');

        if (!id) {
          Swal.fire('Hinweis', 'Dieser Eintrag wurde noch nicht gespeichert und kann direkt verworfen werden.', 'info');
          $row.remove();
          recalculateTotals();
          return;
        }

        Swal.fire({
          title: 'Eintrag löschen?',
          text: 'Der Tagesbericht-Eintrag, Kunden-Zeitanteile, Notizen und Anhänge dieser Zeile werden entfernt. Modulberichte bleiben erhalten.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ja, löschen',
          cancelButtonText: 'Abbrechen',
          confirmButtonColor: '#ef4444'
        }).then(r => {
          if (!r.isConfirmed) return;

          setLoading(btn, true);

          $.ajax({
            url: `/daily_report_time/${id}`,
            type: 'DELETE',
            data: { _token: token },
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
          })
            .done(res => {
              if (!res?.success) {
                Swal.fire('Error', res?.message || 'Löschen fehlgeschlagen.', 'error');
                return;
              }

              Swal.fire('Gelöscht', res.message || 'Eintrag entfernt.', 'success');
              $row.fadeOut(180, function () {
                $(this).remove();
                recalculateTotals();
              });

              reloadReport();
            })
            .fail(xhr => Swal.fire('Error', xhr.responseJSON?.message || 'Löschen fehlgeschlagen.', 'error'))
            .always(() => setLoading(btn, false));
        });
      });

      $(document).on('change', '[name="start_time"],[name="end_time"],.start_time_input,.end_time_input', function () {
        const $row = $(this).closest('tr');
        const s = snap5($row.find('[name="start_time"],.start_time_input').val());
        const e = snap5($row.find('[name="end_time"],.end_time_input').val());
        if (s) $row.find('[name="start_time"],.start_time_input').val(s);
        if (e) $row.find('[name="end_time"],.end_time_input').val(e);
        const h = diffHours(s, e);
        if (h > 0) {
          $row.find('[name="hours_spent"],[name="total_time"],.hours_spent_input').val(h.toFixed(2));
          refreshRowSummary($row);
        }
        setupContinuationPrompts();
        recalcDebounced();
      });
      $(document).on('input', '[name="hours_spent"],[name="total_time"],.hours_spent_input', recalcDebounced);

      $('#filterType').on('change', function () {
        const want = $(this).val();
        $('#daily_report_tbody tr.daily_report_tr').each(function () {
          const rawCode = $(this).data('type') || $(this).find('td:nth-child(4)').data('type') || $(this).find('td:nth-child(4)').text().trim();
          const label = typeToLabel(rawCode);
          $(this).toggle(!want || label === want);
        });
      });


      $(document).on('click', '.dr-continue-yes', function () {
        const $row = $(this).closest('tr.daily_report_tr');
        const $source = findContinuationSourceRow($row);
        if (!$source.length) return;
        $row.data('continuation-choice', 'yes').attr('data-continuation-choice', 'yes');
        copyJobDetailsFromPrevious($source, $row);
        $(this).closest('.dr-continue-prompt').remove();
        const body = $row.find('.dr-entry-body').get(0);
        const toggle = $row.find('.dr-entry-toggle').get(0);
        if (body) body.hidden = false;
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
        $row.find('.description_input').trigger('focus');
      });

      $(document).on('click', '.dr-continue-no', function () {
        const $row = $(this).closest('tr.daily_report_tr');
        $row.data('continuation-choice', 'no').attr('data-continuation-choice', 'no');
        $(this).closest('.dr-continue-prompt').remove();
        const body = $row.find('.dr-entry-body').get(0);
        const toggle = $row.find('.dr-entry-toggle').get(0);
        if (body) body.hidden = false;
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
        $row.find('[name="type"]').trigger('focus');
      });

      $(document).on('change', '[name="type"]', function () {
        const $row = $(this).closest('tr.daily_report_tr');
        $row.attr('data-type', $(this).val()).data('type', $(this).val());
        $row.find('.dr-entry-type-label').text(typeToLabel($(this).val()));
        refreshRowSummary($row);
        setupContinuationPrompts();
      });

      $(document).on('click', '#completeDailyReport', function () {
        const btn = this;
        Swal.fire({ title: 'Abschließen?', text: 'PDF wird erstellt und Bericht gespeichert.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ja, weiter' }).then(r => {
          if (!r.isConfirmed) return;
          setLoading(btn, true);
          $.post("{{ route('daily.report.complete') }}", { _token: $('meta[name="csrf-token"]').attr('content'), employee_id: getEmployeeId(), date: getSelectedDate() })
            .done(res => { window.open(res.pdf_url, '_blank'); Swal.fire('Fertig', 'Bericht gespeichert.', 'success'); })
            .fail(() => Swal.fire('Error', 'PDF Erstellung fehlgeschlagen.', 'error'))
            .always(() => setLoading(btn, false));
        });
      });

      $(document).on('click', '.dr-entry-toggle', function () {
        const expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        const body = this.closest('.dr-entry-card')?.querySelector('.dr-entry-body');
        if (body) body.hidden = expanded;
      });

      $(document).on('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
          e.preventDefault();
          const $focusedRow = $(document.activeElement).closest('tr');
          if ($focusedRow.length) saveRow($focusedRow, null, 'add_missing');
        }
      });
    });
  </script>

  <script>
    /* === Notes & Attachments JS Core === */
    const DEFAULT_AVATAR = "{{ asset('images/gender/male.png') }}";
    const EMPLOYEE_IMG_BASE = "{{ asset('images/employee') }}";
    let NOTES_CTX = { date: null, entry: "__null" };
    let ATTACH_CTX = { date: null, entry: "", note_id: null };

    const avatarUrl = v => (!v ? DEFAULT_AVATAR : (/^https?:\/\//i.test(v) || v.startsWith("/") ? v : (EMPLOYEE_IMG_BASE + "/" + v)));
    const humanSize = b => { const u = ['B', 'KB', 'MB', 'GB', 'TB']; let i = 0, n = +b || 0; while (n >= 1024 && i < u.length - 1) { n /= 1024; i++; } return (n < 10 && i ? n.toFixed(1) : Math.round(n)) + ' ' + u[i]; };
    const isImg = m => /^image\//i.test(m || '');
    const fileIcon = ext => { ext = (ext || '').toLowerCase(); if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return '<i data-lucide="image"></i>'; if (ext === 'pdf') return '<i data-lucide="file-text"></i>'; return '<i data-lucide="file"></i>'; };

    function setNoteCountOnRow(entryId, n) {
      if (!entryId) return;
      const $b = $(`.note-count[data-entry="${entryId}"]`);
      if (!$b.length) return;
      n = Number(n) || 0; $b.text(n).toggleClass('hidden', n === 0);
    }
    function setAttachCountOnRow(entryId, n) {
      if (!entryId) return;
      const $b = $(`.attach-count[data-entry="${entryId}"]`);
      if (!$b.length) return;
      n = Number(n) || 0; $b.text(n).toggleClass('hidden', n === 0);
    }
    function refreshRowCounters(entryId, date) {
      if (!entryId) return;
      const d = date || $('#selected_date').val() || "{{ $start_date ?? '' }}";
      $.get(`{{ route('daily.notes.index') }}`, { date: d, entry_id: String(entryId) }).done(res => setNoteCountOnRow(entryId, Array.isArray(res?.data) ? res.data.length : 0));
      $.get(`{{ route('daily.attach.index') }}`, { date: d, entry_id: String(entryId) }).done(res => setAttachCountOnRow(entryId, Array.isArray(res?.attachments) ? res.attachments.length : 0));
    }
    function refreshAllCounters() {
      const d = $('#selected_date').val() || "{{ $start_date ?? '' }}";
      $('#daily_report_tbody tr.daily_report_tr').each(function () {
        const id = $(this).data('id'); if (id) refreshRowCounters(String(id), d);
      });
    }

    function renderNotes(list) {
      if (!Array.isArray(list) || !list.length) { $("#notesList").html('<div class="dr-muted dr-small">Keine Notizen vorhanden.</div>'); return; }
      const html = list.map(n => `<div class="note-item"><img class="note-avatar" src="${avatarUrl(n.avatar)}" alt=""><div><div class="note-bubble"><div class="dr-note-author">${escapeHtml(n.author || "User")}</div><div>${escapeHtml(n.message || "")}</div></div><div class="note-meta">${escapeHtml(n.created || "")}</div></div></div>`).join("");
      $("#notesList").html(html);
      const el = document.getElementById("notesList"); if (el) el.scrollTop = el.scrollHeight;
    }
    function openNotes(date, entryId) {
      const eid = entryId ? String(entryId) : "__null"; NOTES_CTX = { date, entry: eid };
      $("#notesDate").val(date); $("#notesEntry").val(eid);
      $("#notesContext").text(`Date: ${moment(date, 'YYYY-MM-DD').format('DD.MM.YYYY')} • Row: ${eid === "__null" ? "—" : eid}`);
      $("#notesMessage").val(""); $("#notesList").html('<div class="dr-muted dr-small">Lädt…</div>');
      $.get(`{{ route('daily.notes.index') }}`, { date, entry_id: eid })
        .done(res => { const items = Array.isArray(res?.data) ? res.data : []; renderNotes(items); if (eid !== "__null") setNoteCountOnRow(eid, items.length); })
        .fail(() => $("#notesList").html('<div class="dr-danger-text dr-small">Laden fehlgeschlagen.</div>'));
      $("#notesBackdrop").addClass("open"); $("#notesDrawer").addClass("open").attr("aria-hidden", "false");
    }
    function closeNotes() { $("#notesBackdrop").removeClass("open"); $("#notesDrawer").removeClass("open").attr("aria-hidden", "true"); }
    $(document).on("click", ".btn-notes", function () { openNotes($(this).data("date") || $('#selected_date').val(), $(this).closest("tr").data("id") || $(this).data("entry") || "__null"); });
    $("#notesClose, #notesBackdrop").on("click", closeNotes);
    $("#notesForm").on("submit", function (e) {
      e.preventDefault(); const btn = $(this).find('button[type="submit"]')[0]; $(btn).prop("disabled", true).html('<span class="dr-spinner"></span>');
      $.post(`{{ route('daily.notes.store') }}`, $(this).serialize())
        .done(() => $.get(`{{ route('daily.notes.index') }}`, { date: NOTES_CTX.date, entry_id: NOTES_CTX.entry }).done(res => { const items = Array.isArray(res?.data) ? res.data : []; renderNotes(items); if (NOTES_CTX.entry !== "__null") setNoteCountOnRow(NOTES_CTX.entry, items.length); $("#notesMessage").val("").focus(); }))
        .fail(xhr => Swal.fire("Error", xhr.responseJSON?.message || "Save failed.", "error")).always(() => $(btn).prop("disabled", false).text("Senden"));
    });

    function getRowCustomerData(row) {
      const data = [];
      if (!row) return data;

      const select = row.querySelector('.customer-multi');
      if (!select) return data;

      Array.from(select.selectedOptions || []).forEach(option => {
        const id = option.value;
        if (!id) return;
        const share = row.querySelector(`.customer-share[data-id="${String(id)}"]`);
        data.push({
          id: String(id),
          name: (option.textContent || ('Kunde #' + id)).trim(),
          alternative_id: share?.querySelector('.customer_object_select')?.value || '',
          lead_product_list_id: share?.querySelector('.customer_product_select')?.value || '',
          product_id: share?.querySelector('.customer_product_id_input')?.value || ''
        });
      });

      return data;
    }

    function buildAttachParams(extra = {}) {
      const params = {
        date: ATTACH_CTX.date,
        entry_id: ATTACH_CTX.entry || '',
        source: $('#attachSourceFilter').val() || 'all',
        q: $('#attachSearch').val() || '',
        ...extra
      };

      const customerIds = (ATTACH_CTX.customers || []).map(c => c.id).filter(Boolean);
      if (customerIds.length) {
        params.customer_ids = customerIds;
      }

      return params;
    }

    function updateAttachCustomerContext() {
      const customers = ATTACH_CTX.customers || [];
      const $ctx = $('#attachCustomerContext');
      const $badges = $('#attachCustomerBadges').empty();

      if (!customers.length) {
        $ctx.prop('hidden', true);
        return;
      }

      customers.forEach(c => {
        const detail = [c.alternative_id ? 'Objekt #' + c.alternative_id : '', c.product_id ? 'Produkt #' + c.product_id : ''].filter(Boolean).join(' · ');
        $badges.append(`<span class="attach-customer-badge"><i data-lucide="user"></i>${escapeHtml(c.name || ('Kunde #' + c.id))}${detail ? ' <small>' + escapeHtml(detail) + '</small>' : ''}</span>`);
      });

      $ctx.prop('hidden', false);
      if (window.lucide) lucide.createIcons();
    }

    function renderAttachList(list) {
      if (!Array.isArray(list) || !list.length) {
        $('#attachList').html('<div class="attach-empty-state"><i data-lucide="paperclip"></i>Keine Dateien gefunden.</div>');
        if (window.lucide) lucide.createIcons();
        return;
      }

      const html = list.map(a => {
        const url = a.url || '#';
        const name = escapeHtml(a.name || 'Datei');
        const ext = escapeHtml((a.ext || '').toString().toLowerCase());
        const mime = a.mime || '';
        const size = a.size_label || (a.size ? humanSize(a.size) : '');
        const source = escapeHtml(a.source || 'report');
        const sourceLabel = escapeHtml(a.source_label || (a.source === 'customer' ? 'Kunden-Datei' : 'Bericht-Anhang'));
        const customerLabel = a.customer_id ? `Kunde #${escapeHtml(String(a.customer_id))}` : '';
        const typeLabel = ext || (mime.split('/').pop() || 'file');
        const thumb = (a.is_image || isImg(mime))
          ? `<a href="${url}" target="_blank" rel="noopener"><img class="attach-thumb" src="${url}" alt="${name}"></a>`
          : `<div class="attach-thumb attach-file-icon">${fileIcon(ext)}</div>`;
        const canDelete = !String(a.id || '').startsWith('image_') && source !== 'customer';

        return `
            <article class="attach-item" data-id="${a.id}">
              ${thumb}
              <div class="attach-item-body">
                <div class="attach-item-head">
                  <div class="dr-attach-name" title="${name}">${name}</div>
                  <span class="attach-source-badge ${source}">${sourceLabel}</span>
                </div>
                <div class="attach-meta-line">
                  <span>${escapeHtml(typeLabel).toUpperCase()}</span>
                  ${size ? `<span>•</span><span>${escapeHtml(size)}</span>` : ''}
                  ${customerLabel ? `<span>•</span><span>${customerLabel}</span>` : ''}
                </div>
                <div class="attach-actions">
                  <a class="dr-btn-soft" href="${url}" target="_blank" rel="noopener"><i data-lucide="external-link"></i> Öffnen</a>
                  ${canDelete ? `<button type="button" class="dr-btn-soft dr-btn-danger btn-attach-del" data-id="${a.id}"><i data-lucide="trash-2"></i> Löschen</button>` : ''}
                </div>
              </div>
            </article>`;
      }).join('');

      $('#attachList').html(html);
      if (window.lucide) lucide.createIcons();
    }

    function loadAttachments() {
      if (!ATTACH_CTX.date) return;
      $('#attachList').html('<div class="attach-empty-state"><i data-lucide="loader"></i>Lädt…</div>');
      if (window.lucide) lucide.createIcons();

      $.get(`{{ route('daily.attach.index') }}`, buildAttachParams())
        .done(res => {
          const items = res?.attachments || [];
          ATTACH_CTX.note_id = res?.note_id || null;
          renderAttachList(items);
          if (ATTACH_CTX.entry) {
            const reportCount = items.filter(a => (a.source || 'report') === 'report').length;
            setAttachCountOnRow(ATTACH_CTX.entry, reportCount || items.length);
          }
        })
        .fail(() => $('#attachList').html('<div class="attach-empty-state"><i data-lucide="alert-triangle"></i>Laden fehlgeschlagen.</div>'));
    }

    function openAttach(date, entryId, rowEl = null) {
      const customers = getRowCustomerData(rowEl);
      ATTACH_CTX = { date, entry: entryId || '', note_id: null, customers };

      $('#attachDate').val(date);
      $('#attachEntry').val(entryId || '');
      $('#attachSearch').val('');
      $('#attachSourceFilter').val('all');
      $('#attachFiles').val('');
      $('#attachPreview').hide().empty();

      const customerInfo = customers.length ? ` • Kunden: ${customers.map(c => c.name).join(', ')}` : '';
      $('#attachContext').text(`Datum: ${moment(date, 'YYYY-MM-DD').format('DD.MM.YYYY')} • Row: ${entryId || '—'}${customerInfo}`);

      updateAttachCustomerContext();
      loadAttachments();

      $('#attachBackdrop').addClass('open');
      $('#attachDrawer').addClass('open').attr('aria-hidden', 'false');
    }

    function closeAttach() {
      $('#attachBackdrop').removeClass('open');
      $('#attachDrawer').removeClass('open').attr('aria-hidden', 'true');
    }

    $(document).on('click', '.btn-attach', function () {
      const row = this.closest('tr.daily_report_tr');
      openAttach(
        $(this).data('date') || $('#selected_date').val(),
        $(row).data('id') || $(this).data('entry') || '',
        row
      );
    });

    $('#attachClose, #attachBackdrop').on('click', closeAttach);

    let attachSearchTimer = null;
    $('#attachSearch').on('input', function () {
      clearTimeout(attachSearchTimer);
      attachSearchTimer = setTimeout(loadAttachments, 250);
    });

    $('#attachSourceFilter').on('change', loadAttachments);

    $('#attachFiles').on('change', function () {
      const files = Array.from(this.files || []);
      const $wrap = $('#attachPreview').empty();
      if (!files.length) return $wrap.hide();

      files.forEach(f => {
        const ext = (f.name.split('.').pop() || '').toLowerCase();
        const img = /^image\//i.test(f.type);
        const thumb = img
          ? `<img src="${URL.createObjectURL(f)}" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">`
          : `<span class="attach-file-icon" style="width:42px;height:42px;border-radius:8px;">${fileIcon(ext)}</span>`;
        $wrap.append(`<div class="dr-attach-preview-item">${thumb}<small class="dr-text-truncate">${escapeHtml(f.name)}</small></div>`);
      });

      $wrap.show();
      if (window.lucide) lucide.createIcons();
    });

    $('#attachForm').on('submit', function (e) {
      e.preventDefault();
      const btn = $(this).find('button[type="submit"]')[0];
      const files = Array.from(document.getElementById('attachFiles').files || []);
      if (!files.length) return;

      const fd = new FormData();
      fd.append('date', $('#attachDate').val());
      fd.append('entry_id', $('#attachEntry').val());
      (ATTACH_CTX.customers || []).forEach(c => {
        fd.append('customer_ids[]', c.id);
        if (c.alternative_id) fd.append(`alternative_id[${c.id}]`, c.alternative_id);
        if (c.lead_product_list_id) fd.append(`lead_product_list_id[${c.id}]`, c.lead_product_list_id);
        if (c.product_id) fd.append(`product_id[${c.id}]`, c.product_id);
      });
      fd.append('stage_id', 'daily_report');
      fd.append('status', 'daily_report');
      files.forEach(f => fd.append('files[]', f));

      $(btn).prop('disabled', true).text('Lädt…');

      fetch(`{{ route('daily.attach.store') }}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        body: fd
      })
        .then(r => r.ok ? r.json() : r.json().then(Promise.reject))
        .then(() => {
          $('#attachFiles').val('');
          $('#attachPreview').hide().empty();
          loadAttachments();
        })
        .catch(err => Swal.fire('Fehler', err?.message || 'Upload fehlgeschlagen.', 'error'))
        .finally(() => $(btn).prop('disabled', false).text('Upload'));
    });

    $(document).on('click', '.btn-attach-del', function () {
      const id = $(this).data('id');
      const btn = this;
      if (!id) return;

      $(btn).prop('disabled', true).text('…');
      fetch(`{{ url('/daily-attachments') }}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
      })
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(() => loadAttachments())
        .catch(() => Swal.fire('Fehler', 'Löschen fehlgeschlagen.', 'error'))
        .finally(() => $(btn).prop('disabled', false).html('<i data-lucide="trash-2"></i> Löschen'));
    });
  </script>

  <script>
    (function () {
      'use strict';

      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

      let activeRecognition = null;
      let activePlayButton = null;
      let activeStopButton = null;
      let activeTarget = null;
      let activeWrapper = null;
      let currentFinalText = '';
      let shouldKeepListening = false;
      let manuallyStopped = false;
      let startedAt = null;
      let timerInterval = null;
      let restartTimer = null;

      function voiceLanguage() {
        const select = document.getElementById('drVoiceLanguage');
        return select && select.value ? select.value : 'de-DE';
      }

      function formatSeconds(totalSeconds) {
        totalSeconds = Math.max(0, parseInt(totalSeconds || 0, 10));
        const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');
        return minutes + ':' + seconds;
      }

      function setVoiceStatus(type, text) {
        const status = document.getElementById('drVoiceStatus');
        if (!status) return;

        status.classList.remove('is-active', 'is-done', 'is-error');
        if (type === 'active') status.classList.add('is-active');
        if (type === 'done') status.classList.add('is-done');
        if (type === 'error') status.classList.add('is-error');

        status.textContent = text || 'Mikrofon bereit';
      }

      function setTimer(active) {
        const timer = document.getElementById('drVoiceTimer');
        if (!timer) return;
        timer.classList.toggle('is-active', !!active);
        if (!active) timer.textContent = '00:00';
      }

      function startTimer() {
        startedAt = Date.now();
        setTimer(true);
        window.clearInterval(timerInterval);
        timerInterval = window.setInterval(function () {
          const timer = document.getElementById('drVoiceTimer');
          if (!timer || !startedAt) return;
          timer.textContent = formatSeconds((Date.now() - startedAt) / 1000);
        }, 250);
      }

      function stopTimer(reset = false) {
        window.clearInterval(timerInterval);
        timerInterval = null;
        if (reset) {
          startedAt = null;
          setTimer(false);
        }
      }

      function isEditableVoiceTarget(field) {
        if (!field) return false;
        if (field.disabled) return false;
        if (field.readOnly && !field.classList.contains('is-related-blocked')) return false;
        if (field.type === 'hidden' || field.type === 'file' || field.type === 'checkbox' || field.type === 'radio') return false;
        return true;
      }

      function insertAtCursor(field, text) {
        if (!field || !text || !text.trim()) return;
        const clean = text.trim();
        const current = field.value || '';
        const start = typeof field.selectionStart === 'number' ? field.selectionStart : current.length;
        const end = typeof field.selectionEnd === 'number' ? field.selectionEnd : current.length;
        const before = current.substring(0, start);
        const after = current.substring(end);
        const separator = before.trim() !== '' && !before.endsWith(' ') && !before.endsWith('\n') ? ' ' : '';

        field.value = before + separator + clean + after;

        const cursor = (before + separator + clean).length;
        try {
          field.focus();
          field.setSelectionRange(cursor, cursor);
        } catch (e) {
          field.focus();
        }

        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));

        if (window.jQuery) {
          try { window.jQuery(field).trigger('input').trigger('change'); } catch (e) { }
        }
      }

      function resetActiveVoice(options = {}) {
        const keepTimerText = !!options.keepTimerText;

        if (activePlayButton) {
          activePlayButton.classList.remove('is-listening');
          activePlayButton.title = 'Aufnahme starten';
          activePlayButton.setAttribute('aria-pressed', 'false');
          activePlayButton.innerHTML = '<i data-lucide="play"></i>';
        }

        if (activeStopButton) {
          activeStopButton.disabled = true;
          activeStopButton.title = 'Aufnahme ist nicht aktiv';
        }

        if (activeWrapper) {
          activeWrapper.classList.remove('is-listening');
          const status = activeWrapper.querySelector('.dr-voice-status');
          if (status) status.textContent = '';
        }

        window.clearTimeout(restartTimer);
        restartTimer = null;

        activeRecognition = null;
        activePlayButton = null;
        activeStopButton = null;
        activeTarget = null;
        activeWrapper = null;
        currentFinalText = '';
        shouldKeepListening = false;
        manuallyStopped = false;

        stopTimer(!keepTimerText);

        if (window.lucide) {
          try { window.lucide.createIcons(); } catch (e) { }
        }
      }

      function commitAndReset(doneText = 'Text wurde übernommen') {
        const hasText = activeTarget && currentFinalText.trim() !== '';

        if (hasText) {
          insertAtCursor(activeTarget, currentFinalText);
        }

        resetActiveVoice({ keepTimerText: hasText });

        if (hasText) {
          setVoiceStatus('done', doneText);
          window.setTimeout(function () {
            setVoiceStatus('', 'Mikrofon bereit');
            stopTimer(true);
          }, 1800);
        } else {
          setVoiceStatus('', 'Mikrofon bereit');
          stopTimer(true);
        }
      }

      function stopVoiceInput(commitText = true) {
        manuallyStopped = true;
        shouldKeepListening = false;
        window.clearTimeout(restartTimer);

        if (activeRecognition) {
          activeRecognition.__commitText = commitText;
          try { activeRecognition.stop(); } catch (e) {
            if (commitText) commitAndReset();
            else resetActiveVoice();
          }
        } else {
          if (commitText) commitAndReset();
          else resetActiveVoice();
        }
      }

      function buildRecognition() {
        const recognition = new SpeechRecognition();
        recognition.lang = voiceLanguage();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;
        recognition.__commitText = true;
        return recognition;
      }

      function attachRecognitionEvents(recognition) {
        recognition.onresult = function (event) {
          let interim = '';

          for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcript = event.results[i][0] && event.results[i][0].transcript ? event.results[i][0].transcript : '';
            if (event.results[i].isFinal) currentFinalText += transcript + ' ';
            else interim += transcript;
          }

          const fullPreview = (currentFinalText + interim).trim();
          const localStatus = activeWrapper ? activeWrapper.querySelector('.dr-voice-status') : null;

          if (localStatus) {
            localStatus.classList.add('is-visible');
            localStatus.classList.toggle('is-empty', !fullPreview);
            localStatus.textContent = fullPreview || 'Sprich jetzt. Der erkannte Text erscheint hier live und wird erst beim Stoppen in das Feld übernommen.';
          }

          setVoiceStatus('active', fullPreview ? ('Erkannt: ' + fullPreview.slice(0, 80) + (fullPreview.length > 80 ? '…' : '')) : 'Aufnahme läuft...');
        };

        recognition.onerror = function (event) {
          console.warn('Daily report voice input error:', event.error);

          const error = event.error || 'unbekannt';
          const localStatus = activeWrapper ? activeWrapper.querySelector('.dr-voice-status') : null;

          if (error === 'no-speech' && shouldKeepListening && !manuallyStopped) {
            if (localStatus && !localStatus.textContent.trim()) {
              localStatus.classList.add('is-visible', 'is-empty');
              localStatus.textContent = 'Noch aktiv – bitte weiter sprechen...';
            }
            setVoiceStatus('active', 'Noch aktiv – bitte weiter sprechen...');
            return;
          }

          if (localStatus) localStatus.textContent = 'Mikrofonfehler: ' + error;
          setVoiceStatus('error', 'Mikrofonfehler: ' + error);
        };

        recognition.onend = function () {
          if (shouldKeepListening && !manuallyStopped && activeTarget) {
            const localStatus = activeWrapper ? activeWrapper.querySelector('.dr-voice-status') : null;
            if (localStatus) localStatus.textContent = 'Aufnahme läuft weiter...';
            setVoiceStatus('active', 'Aufnahme läuft weiter...');

            restartTimer = window.setTimeout(function () {
              if (!shouldKeepListening || manuallyStopped || !activeTarget) return;
              try {
                activeRecognition = buildRecognition();
                attachRecognitionEvents(activeRecognition);
                activeRecognition.start();
              } catch (e) {
                console.warn('Could not restart voice input:', e);
                commitAndReset('Text wurde übernommen');
              }
            }, 250);

            return;
          }

          const shouldCommit = recognition.__commitText !== false;
          if (shouldCommit) commitAndReset('Text wurde übernommen');
          else resetActiveVoice();
        };
      }

      function startVoiceInput(field, playButton, stopButton) {
        if (!SpeechRecognition) {
          setVoiceStatus('error', 'Browser unterstützt Spracheingabe nicht');
          alert('Spracheingabe wird von diesem Browser nicht unterstützt. Bitte Chrome oder Edge verwenden.');
          return;
        }

        if (!isEditableVoiceTarget(field)) {
          setVoiceStatus('error', 'Feld ist gesperrt');
          alert('Dieses Feld ist aktuell gesperrt oder nicht bearbeitbar. Bitte zuerst entsperren/freigeben.');
          return;
        }

        if (activeRecognition) {
          stopVoiceInput(true);
          if (activeTarget === field) return;
        }

        currentFinalText = '';
        shouldKeepListening = true;
        manuallyStopped = false;
        activeTarget = field;
        activePlayButton = playButton;
        activeStopButton = stopButton;
        activeWrapper = playButton.closest('.dr-voice-wrap');
        activeRecognition = buildRecognition();
        attachRecognitionEvents(activeRecognition);

        playButton.classList.add('is-listening');
        playButton.title = 'Aufnahme stoppen und Text übernehmen';
        playButton.setAttribute('aria-pressed', 'true');
        playButton.innerHTML = '<i data-lucide="square"></i>';

        if (stopButton) {
          stopButton.disabled = false;
          stopButton.title = 'Aufnahme stoppen und Text übernehmen';
        }

        if (activeWrapper) {
          activeWrapper.classList.add('is-listening');
          const status = activeWrapper.querySelector('.dr-voice-status');
          if (status) {
            status.classList.add('is-visible', 'is-empty');
            status.textContent = 'Sprich jetzt. Der erkannte Text erscheint hier live und wird erst beim Stoppen in das Feld übernommen.';
          }
        }

        setVoiceStatus('active', 'Aufnahme läuft...');
        startTimer();

        if (window.lucide) {
          try { window.lucide.createIcons(); } catch (e) { }
        }

        try {
          activeRecognition.start();
        } catch (e) {
          console.warn('Could not start voice input:', e);
          setVoiceStatus('error', 'Mikrofon konnte nicht gestartet werden');
          resetActiveVoice();
        }
      }

      function wrapVoiceField(field) {
        if (!field || field.dataset.voiceReady === '1') return;
        if (field.closest('.select2-container')) return;
        if (!field.matches('textarea, input[type="text"], input[type="search"]')) return;
        field.dataset.voiceReady = '1';

        const parent = field.parentNode;
        if (!parent) return;

        let wrapper = field.closest('.dr-voice-wrap');
        if (!wrapper) {
          wrapper = document.createElement('div');
          wrapper.className = 'dr-voice-wrap';
          parent.insertBefore(wrapper, field);
          wrapper.appendChild(field);
        }

        if (wrapper.querySelector('.dr-voice-controls')) return;

        const controls = document.createElement('div');
        controls.className = 'dr-voice-controls';

        const playButton = document.createElement('button');
        playButton.type = 'button';
        playButton.className = 'dr-voice-play-btn';
        playButton.title = 'Aufnahme starten';
        playButton.setAttribute('aria-pressed', 'false');
        playButton.innerHTML = '<i data-lucide="play"></i>';

        const status = document.createElement('div');
        status.className = 'dr-voice-status';

        playButton.addEventListener('click', function () {
          if (activeWrapper === wrapper && activeRecognition) {
            stopVoiceInput(true);
            return;
          }

          startVoiceInput(field, playButton, null);
        });

        controls.appendChild(playButton);
        wrapper.appendChild(controls);
        wrapper.appendChild(status);
      }

      function initDailyReportVoiceInputs(root = document) {
        if (!root || !root.querySelectorAll) return;

        const selectors = [
          'textarea.description_input',
          'textarea[name="description"]',
          'textarea[name="note"]',
          'textarea[name="notes"]',
          'textarea[name^="customer_note"]',
          'textarea.customer_note_input',
          'textarea.note-input',
          'textarea.notes_input',
          'input[name^="customer_note"]',
          'input.customer_note_input',
          '.dr-notes-modal textarea',
          '.notes-modal textarea',
          '#notesModal textarea',
          '#noteModal textarea',
          '.modal textarea'
        ];

        root.querySelectorAll(selectors.join(',')).forEach(wrapVoiceField);

        if (window.lucide) {
          try { window.lucide.createIcons(); } catch (e) { }
        }
      }

      function scheduleVoiceInit(root = document) {
        window.clearTimeout(window.__drVoiceInitTimer);
        window.__drVoiceInitTimer = window.setTimeout(function () {
          initDailyReportVoiceInputs(root);
        }, 80);
      }

      document.addEventListener('DOMContentLoaded', function () {
        initDailyReportVoiceInputs();

        if (document.body && window.MutationObserver) {
          const observer = new MutationObserver(function (mutations) {
            let shouldInit = false;
            mutations.forEach(function (mutation) {
              if (mutation.addedNodes && mutation.addedNodes.length) shouldInit = true;
            });
            if (shouldInit) scheduleVoiceInit(document);
          });
          observer.observe(document.body, { childList: true, subtree: true });
        }
      });

      document.addEventListener('change', function (event) {
        if (event.target && event.target.id === 'drVoiceLanguage') {
          stopVoiceInput(true);
        }
      });

      document.addEventListener('click', function () { scheduleVoiceInit(document); });
      document.addEventListener('input', function () { scheduleVoiceInit(document); });

      window.initDailyReportVoiceInputs = initDailyReportVoiceInputs;
      window.stopDailyReportVoiceInput = stopVoiceInput;
    })();
  </script>

@endsection