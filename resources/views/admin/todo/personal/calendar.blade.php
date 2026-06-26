@extends('admin.layouts.app')
@section('title')
  Mein Kalendar
@endsection

@php
  $appointmentLeadStages = collect($leadStages ?? []);
  $appointmentLeadStagePayload = collect($leadStageOptions ?? $appointmentLeadStages->map(function ($stage) {
    $subStages = collect(
      data_get($stage, 'activeSubStages')
      ?? data_get($stage, 'active_sub_stages')
      ?? data_get($stage, 'subStages')
      ?? data_get($stage, 'sub_stages')
      ?? []
    );

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
  })->values());

  $appointmentLeadStageContextRoute = \Illuminate\Support\Facades\Route::has('main-appointments.lead-stage-context')
    ? route('main-appointments.lead-stage-context')
    : url('/main-appointments/lead-stage-context');
@endphp


@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
  <style>
    /* Theme */
    :root {
      --brand: #8fc73e;
      --brand-2: #74b2d4;
      --accent: #00aaff;
      --fc-day-bg: #f8f9fa;
      --muted: #626262;
    }

    /* FullCalendar base */
    .fc .fc-button {
      background: var(--brand) !important;
      border: 0 !important;
      margin-right: 3px !important
    }

    .fc .fc-button-active {
      background: var(--brand-2) !important
    }

    .fc .fc-toolbar-title {
      color: var(--muted)
    }

    .fc .fc-view,
    .fc-daygrid {
      background: #fff
    }

    .fc .fc-day-today {
      background: #f1f1f1 !important
    }

    .fc-h-event {
      border: 1px solid #e8eaec !important;
      border-left-width: 0
    }

    .fc-v-event {
      background: #fff !important
    }

    .fc-timegrid-slot-minor {
      display: none !important
    }

    .fc-license-message {
      display: none !important
    }

    .fc-popover {
      position: absolute !important
    }

    .fc-timeGridWeek-view,
    .fc-timeGridDay-view,
    .fc-listWeek-view {
      background: #fff !important;
      height: auto !important;
      overflow-y: auto
    }

    /* DayGrid events */
    .fc-daygrid-event {
      display: block;
      width: 100%;
      background: var(--fc-day-bg);
      border-left: 4px solid var(--accent);
      padding: 10px;
      border-radius: 6px;
      color: #333;
      text-decoration: none;
      transition: background-color .3s ease;
      white-space: normal !important;
      word-wrap: break-word !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important
    }

    .fc-daygrid-event:hover {
      background: #f2f2f2
    }

    /* TimeGrid events */
    .fc-timeGridWeek-view .fc-timegrid-event {
      max-height: 600px;
      overflow-y: auto
    }

    .fc-popover .fc-timegrid-event {
      display: flex !important;
      position: relative !important;
      min-height: 20px !important;
      width: auto !important;
      white-space: normal;
      font-size: 12px;
      padding: 4px
    }

    .fc-popover .fc-timegrid-slot {
      height: 50px
    }

    .fc-timegrid-event {
      background-color: inherit !important;
      color: inherit !important
    }

    /* Custom event content */
    .custom-event {
      display: flex;
      flex-direction: column;
      gap: 0
    }

    .custom-event-status {
      display: flex;
      align-items: center;
      font-size: .9rem;
      color: #28a745;
      font-weight: 600
    }

    .custom-event-status i {
      margin-right: 5px
    }

    .custom-event-title {
      font-size: 1rem;
      font-weight: 700;
      color: #333;
      margin: 0 0 5px
    }

    .custom-event-product {
      display: flex;
      justify-content: space-between;
      font-size: .9rem;
      color: #007bff
    }

    .custom-event-product ul {
      margin: 0;
      padding: 0;
      display: flex;
      gap: 5px
    }

    .custom-event-product ul li img {
      border-radius: 50%
    }

    .custom-event-product-status {
      font-weight: 600
    }

    .custom-event-time {
      font-size: .8rem;
      color: #666
    }

    /* Dropdown */
    .custom-dropdown-menu {
      display: none;
      position: absolute;
      background: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
      border-radius: 5px;
      z-index: 100;
      margin-top: -116px;
      margin-left: 249px;
      padding: 10px
    }

    .custom-dropdown-menu ul {
      list-style: none;
      margin: 0;
      padding: 0
    }

    .custom-dropdown-menu ul li {
      padding: 8px 15px;
      cursor: pointer
    }

    .custom-dropdown-menu ul li:hover {
      background: #f0f0f0
    }

    .event_drop_down {
      cursor: pointer;
      position: relative
    }

    /* Utilities */
    .emp_active {
      border: 3px solid var(--brand)
    }

    .task-bg,
    .task-event {
      background: #D6EAF9 !important
    }

    .appointmetn-bg,
    .appointment-event {
      background: #E5F0D5 !important
    }

    .calendar {
      height: 100% !important;
      overflow-y: auto
    }

    .fc-more-link {
      width: 45px;
      background: #f1f1f1
    }

    .fc-more-link .fc-timegrid-more-link-inner {
      font-size: 22px;
      justify-self: anchor-center
    }

    .fc-timegrid-slots table tr {
      height: 34px !important
    }

    .fc-timegrid-slots {
      overflow-y: auto;
      max-height: 100%
    }

    .fc-event-main-frame,
    .fc-event-main {
      display: none !important
    }

    .select2-selection__choice {
      border: 0 !important
    }

    .line {
      width: 90%;
      border-bottom: 2px solid #b8b8b8;
      margin: 6px 0
    }

    .fc-ticket-link:hover {
      opacity: .8
    }

    .mobile_view_event {
      font-family: Arial, sans-serif;
      font-size: 11px;
      line-height: 1.3;
      word-wrap: break-word;
      overflow-wrap: break-word
    }

    /* Public holidays */
    .public-holiday-cell {
      background: #d3d3d3 !important
    }

    .fc .public-holiday-cell {
      background: #f8f9fa !important
    }

    /* All-day styles */
    .fc .fc-all-day-event {
      background: #e3f2fd !important;
      border-left: 4px solid #2196f3 !important;
      font-size: 12px;
      font-weight: 700;
      padding: 4px
    }

    .custom-all-day {
      background: #ffedcc !important;
      border-left: 4px solid #ff9800 !important;
      color: #333 !important;
      font-weight: 700;
      padding: 4px 6px
    }

    .custom-all-day .custom-event-header {
      display: none !important
    }

    .custom-all-day .custom-event-header .custom-event-title>p {
      margin: 0;
      padding: 0
    }

    /* Recurring leave */
    .fc-event.recurring-leave {
      background: repeating-linear-gradient(45deg, #6c757d, #6c757d 5px, #9ca3af 5px, #9ca3af 10px);
      color: #000 !important;
      /* <--- Changed to Black */
      border: 1px solid #6c757d !important;
      border-radius: 6px;
      font-size: 11px;
      padding: 4px;
      font-weight: 600;
      /* Added bold for better visibility */
    }

    .fc-event.recurring-home-office {
      background: linear-gradient(135deg, #e0f2fe, #dcfce7) !important;
      border: 1px solid #74b2d4 !important;
      border-left: 4px solid #8fc73e !important;
      color: #1f2937 !important;
      border-radius: 8px;
      font-weight: 800;
    }



    /* Animations */
    @keyframes pulseScale {
      0% {
        transform: scale(1);
        color: currentColor
      }

      100% {
        transform: scale(1.2);
        color: var(--pulse-color, currentColor)
      }
    }

    #bellIcon {
      --pulse-color: red;
      animation: pulseScale 1s ease-in-out infinite
    }

    .warning_text {
      --pulse-color: #ff9f43;
      animation: pulseScale 1s ease-in-out infinite
    }

    .edited-event {
      animation: blink-effect 1s ease-in-out 3;
      border: 3px solid red !important
    }

    @keyframes blink-effect {

      0%,
      100% {
        opacity: 1
      }

      50% {
        opacity: .2
      }
    }

    /* Mini calendar */
    #mini_calendar .fc-daygrid-day-events {
      display: none !important
    }

    #mini_calendar .fc-dayGridMonth-view {
      background: white
    }

    #mini_calendar .fc-daygrid-day-bottom {
      display: none !important
    }

    #mini_calendar .fc-day-selected .fc-daygrid-day-frame::after {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 30px;
      height: 30px;
      background: #d4d4e4 !important;
      border-radius: 50%;
      transform: translate(-50%, -50%);
      z-index: -1
    }

    #mini_calendar .fc-day-selected .fc-daygrid-day-frame {
      background: #d4d4e4 !important;
      border-radius: 50%
    }

    #mini_calendar .fc-day {
      padding: 0 !important;
      justify-items: center
    }

    #mini_calendar .fc-toolbar-title {
      font-size: 19px
    }

    /* Sidebar */

    .employee_lists::-webkit-scrollbar,
    #slider_section::-webkit-scrollbar {
      width: 6px
    }

    .employee_lists::-webkit-scrollbar-thumb,
    #slider_section::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 3px
    }

    #slider_section::-webkit-scrollbar-thumb {
      border-radius: 4px
    }

    /* Calendar menu */
    .calendar_menu {
      position: absolute !important;
      bottom: 173px !important;
      left: 86% !important
    }

    .calendar_menu button {
      color: #fff;
      font-size: 18px
    }

    /* SweetAlert tuning */
    .custom-swal-popup {
      color: #fff !important;
      border-radius: 10px;
      text-align: left;
      background: #2d3e50
    }

    .custom-confirm-btn {
      background: #c23a1c !important;
      color: #fff !important;
      font-weight: 700;
      border-radius: 5px
    }

    .custom-cancel-btn {
      background: var(--brand-2) !important;
      color: #fff !important;
      font-weight: 700;
      border-radius: 5px
    }

    .swal2-html-container .custom-event a {
      font-size: 14px;
      color: #2c3e50 !important
    }

    .swal2-html-container .custom-event p {
      font-size: 12px;
      color: var(--brand-2) !important
    }

    .swal2-title,
    .swal2-html-container {
      text-align: left !important
    }

    .swal2-close {
      color: #fff !important
    }

    .swal2-close:hover {
      color: red !important
    }

    /* Picker UI */
    .picker-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #dee2e6
    }

    .picker-card {
      border: 1px solid #e9ecef;
      border-radius: .5rem;
      padding: .5rem
    }

    .picker-chip {
      display: flex;
      align-items: center;
      gap: .5rem;
      padding: .35rem .5rem;
      border: 1px solid #e9ecef;
      border-radius: 9999px;
      cursor: pointer
    }

    .picker-chip.active {
      border-color: var(--brand);
      background: #f6fff1
    }

    .picker-list-item {
      padding: .35rem .5rem;
      cursor: pointer;
      border-bottom: 1px dashed #eee
    }

    .picker-list-item:hover {
      background: #f8f9fa
    }

    /* Modal: TERMIN ERSTELLEN */
    .new_task {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
      width: 60% !important;
      max-width: 1100px !important;
      max-height: 85vh;
      overflow-y: auto;
      background: #f5f6f8;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
      padding: 0
    }

    .new_task .modal-body {
      max-height: 85vh;
      overflow-y: auto;
      padding: 15px
    }

    .new_task .modal-header {
      position: sticky;
      top: 0;
      background: #fff;
      z-index: 10;
      padding: 10px;
      border-bottom: 1px solid #ddd
    }

    .new_task .modal-footer {
      position: sticky;
      bottom: 0;
      background: #e7e6e6 !important;
      z-index: 10;
      padding: 10px;
      border-top: 1px solid #ddd
    }

    .new_task .card-header {
      border-bottom: 1px solid #dee2e6;
      padding: 12px 20px
    }

    .new_task .card-body {
      padding: 16px 20px
    }

    .new_task .modal-body .row>[class^="col-"],
    .new_task .modal-body .row>[class*=" col-"] {
      margin-bottom: 10px
    }

    @media (min-width:992px) {
      .new_task .form-body .row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        grid-column-gap: 16px
      }

      .new_task .form-body .row>.col-md-12,
      .new_task .form-body .row>.col-12 {
        grid-column: 1/-1
      }
    }

    @media (max-width:991.98px) {
      .new_task {
        width: 95% !important;
        max-width: 95% !important;
        top: 52%
      }

      .new_task .card-body {
        padding: 12px 12px 80px
      }

      .new_task label,
      .new_task .form-control,
      .new_task .select2-container--default .select2-selection--single,
      .new_task .select2-container--default .select2-selection--multiple {
        font-size: 13px
      }

      .new_task .form-control,
      .new_task .select2-container--default .select2-selection--single,
      .new_task .select2-container--default .select2-selection--multiple {
        min-height: 34px
      }
    }

    .new_task_close {
      position: absolute;
      z-index: 4;
      left: -135px;
      top: 16%
    }

    #inquiryPreviewWrapper {
      border-radius: 8px;
      border: 1px solid #dee2e6;
      padding: 8px;
      background: #fff
    }

    #inquiryPreviewTable th,
    #inquiryPreviewTable td {
      vertical-align: middle;
      font-size: 12px
    }

    #inquiryPreviewTable th {
      background: #f8f9fa;
      white-space: nowrap
    }

    #participantsBlock.hidden-by-inquiry {
      display: none !important
    }

    /* View-specific helpers */
    .fc-timeGridWeek-view .mobile_title {
      transform: rotate(90deg) !important;
      color: gray
    }

    .fc-timeGridWeek-view .mobile_view {
      display: flex;
      align-items: center;
      flex-direction: column
    }

    .custom-ticket-btn {
      background: #f8ac00 !important;
      color: #111827 !important;
      font-weight: 800 !important;
      border-radius: 5px !important;
    }

    .custom-ticket-btn svg {
      margin-right: 6px;
      vertical-align: -2px;
    }

    /* Responsive tweaks */
    @media (max-width:768px) {
      .fc-header-toolbar {
        flex-direction: column
      }

      .fc-daygrid-day {
        min-height: 100px !important
      }

      .fc-daygrid-day-frame {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center
      }

      .fc-daygrid-day-events {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center
      }

      .fc-daygrid-event {
        font-size: 14px !important;
        padding: 8px !important;
        min-width: 80%;
        text-align: center;
        display: inline-block
      }
    }

    @media (max-width:1394px) {

      #calendar_icons,
      #calendar_times {
        display: none !important;
      }

      #mini_calendar {
        display: block !important;
        min-height: 260px;
      }
    }

    #mini_calendar {
      display: block !important;
      width: 100%;
      min-height: 270px;
    }

    #mini_calendar .fc {
      width: 100%;
    }

    #mini_calendar .fc-daygrid-day {
      cursor: pointer;
    }

    #mini_calendar .fc-day-selected .fc-daygrid-day-frame {
      background: #d4d4e4 !important;
      border-radius: 50%;
    }

    @media (max-width:576px) {

      .employee_search_input,
      .task_search_input,
      .appointment_search_input {
        margin-bottom: 10px
      }
    }
  </style>

  <style>
    .customer-products-box {
      margin-top: 12px;
      padding: 12px;
      border-radius: 12px;
      background: #f8fafc;
      border: 1px solid #e5e7eb;
    }

    .customer-products-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
      flex-wrap: wrap;
    }

    .customer-products-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 700;
      color: #1f2937;
    }

    .customer-products-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 999px;
      background: linear-gradient(135deg, #ecfdf5, #d1fae5);
      color: #065f46;
      border: 1px solid #a7f3d0;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: all .2s ease;
    }

    .customer-products-badge:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 14px rgba(16, 185, 129, .15);
    }

    .customer-products-summary {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 8px;
    }

    .customer-products-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 10px;
      border-radius: 999px;
      background: #ffffff;
      border: 1px solid #dbeafe;
      color: #1e3a8a;
      font-size: 11px;
      font-weight: 600;
    }

    .customer-products-empty {
      padding: 10px 0;
      color: #6b7280;
      font-size: 12px;
    }

    .swal-products-list {
      max-height: 420px;
      overflow: auto;
      padding-right: 4px;
      text-align: left;
    }

    .swal-product-card {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      padding: 12px;
      margin-bottom: 10px;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      background: #fff;
      box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
    }

    .swal-product-icon {
      width: 42px;
      height: 42px;
      min-width: 42px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #eff6ff, #dbeafe);
      color: #1d4ed8;
      border: 1px solid #bfdbfe;
    }

    .swal-product-content {
      flex: 1;
      min-width: 0;
    }

    .swal-product-name {
      font-size: 14px;
      font-weight: 700;
      color: #111827;
      margin-bottom: 5px;
      word-break: break-word;
    }

    .swal-product-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
    }

    .swal-product-pill {
      display: inline-flex;
      align-items: center;
      padding: 4px 8px;
      border-radius: 999px;
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      color: #4b5563;
      font-size: 11px;
      font-weight: 600;
    }

    /* Department employee accordion */
    .employee-sidebar-actions {
      display: flex;
      gap: 6px;
      margin-bottom: 10px;
    }

    .employee-sidebar-actions button {
      flex: 1;
      border: 1px solid #dbeafe;
      background: #f8fafc;
      color: #334155;
      border-radius: 8px;
      padding: 6px 8px;
      font-size: 11px;
      font-weight: 700;
      cursor: pointer;
    }

    .employee-sidebar-actions button:hover {
      background: #eef6ff;
    }

    .employee-department {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #ffffff;
      margin-bottom: 10px;
      overflow: hidden;
    }

    .employee-department-header {
      width: 100%;
      border: 0;
      background: linear-gradient(135deg, #f8fafc, #eef6ff);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 8px 10px;
      cursor: pointer;
    }

    .employee-department-header:hover {
      background: linear-gradient(135deg, #eef6ff, #e6f7ff);
    }

    .employee-department-left {
      display: flex;
      align-items: center;
      gap: 7px;
      min-width: 0;
    }

    .employee-department-arrow {
      font-size: 12px;
      color: #64748b;
      transition: transform .18s ease;
    }

    .employee-department.is-collapsed .employee-department-arrow {
      transform: rotate(-90deg);
    }

    .employee-department-name {
      font-size: 11px;
      font-weight: 900;
      color: #1f2937;
      text-transform: uppercase;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .employee-department-count {
      min-width: 24px;
      height: 22px;
      padding: 0 7px;
      border-radius: 999px;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      color: #475569;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 900;
    }

    .employee-department-body {
      padding: 7px 8px 8px;
    }

    .employee-department.is-collapsed .employee-department-body {
      display: none;
    }

    .employee-department .list-item {
      border-radius: 10px;
      padding: 3px 4px;
      margin-bottom: 4px;
      transition: background .15s ease;
    }

    .employee-department .list-item:hover {
      background: #f8fafc;
    }

    /* Custom delayed calendar tooltip */
    .calendar-smart-tooltip {
      position: fixed;
      z-index: 999999;
      max-width: 420px;
      min-width: 260px;
      background: #111827;
      color: #ffffff;
      border-radius: 14px;
      padding: 14px 42px 14px 14px;
      box-shadow: 0 18px 45px rgba(0, 0, 0, .28);
      border: 1px solid rgba(255, 255, 255, .12);
      font-size: 12px;
      line-height: 1.45;
      pointer-events: auto;
      opacity: 0;
      transform: translateY(6px) scale(.98);
      transition: opacity .16s ease, transform .16s ease;
    }

    .calendar-smart-tooltip.is-visible {
      opacity: 1;
      transform: translateY(0) scale(1);
    }

    .calendar-smart-tooltip-close {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 24px;
      height: 24px;
      border: 0;
      border-radius: 999px;
      background: rgba(255, 255, 255, .12);
      color: #ffffff;
      font-size: 17px;
      font-weight: 900;
      line-height: 22px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .calendar-smart-tooltip-close:hover {
      background: rgba(239, 68, 68, .92);
    }

    .calendar-smart-tooltip-title {
      font-size: 13px;
      font-weight: 800;
      color: #d9f99d;
      margin-bottom: 8px;
    }

    .calendar-smart-tooltip-row {
      display: flex;
      gap: 8px;
      margin-top: 5px;
    }

    .calendar-smart-tooltip-label {
      min-width: 88px;
      color: #9ca3af;
      font-weight: 700;
    }

    .calendar-smart-tooltip-value {
      flex: 1;
      color: #f9fafb;
      word-break: break-word;
    }

    .calendar-event-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
      margin-top: 3px;
    }

    .calendar-event-chip {
      display: inline-flex;
      align-items: center;
      max-width: 100%;
      padding: 2px 6px;
      border-radius: 999px;
      background: rgba(255, 255, 255, .62);
      color: #334155;
      font-size: 8.5px;
      font-weight: 800;
      line-height: 1.2;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .calendar-event-address {
      margin-top: 3px;
      font-size: 8.5px;
      color: #475569;
      line-height: 1.2;
      max-height: 22px;
      overflow: hidden;
    }

    /* SweetAlert calendar menu: fixed bottom-right inside popup */
    .swal2-container .custom-swal-popup {
      position: relative !important;
      overflow: visible !important;
    }

    .swal2-container .swal2-html-container {
      overflow: visible !important;
    }

    .swal2-container .calendar_menu {
      position: absolute !important;
      right: 18px !important;
      bottom: 82px !important;
      left: auto !important;
      top: auto !important;
      display: block !important;
      z-index: 1000000 !important;
    }

    .swal2-container .calendar-action-menu {
      position: relative !important;
      top: auto !important;
      right: auto !important;
      bottom: auto !important;
      left: auto !important;
      display: inline-block !important;
      z-index: 1000000 !important;
    }

    .swal2-container #swalCalendarMenu {
      position: absolute !important;
      right: 0 !important;
      bottom: 38px !important;
      top: auto !important;
      min-width: 185px !important;
      z-index: 1000001 !important;
    }

    .swal2-container .swal-calendar-menu-item {
      color: #111827 !important;
    }

    .swal2-container .swal-calendar-menu-item:hover {
      background: #f3f4f6 !important;
    }

    .swal2-actions {
      position: relative !important;
      overflow: visible !important;
      align-items: center !important;
    }

    .calendar-action-menu {
      position: relative !important;
      display: inline-flex !important;
      align-items: center !important;
      margin: 0 5px !important;
      z-index: 1000000 !important;
    }

    .swal-calendar-dropdown {
      display: none;
      position: absolute !important;
      right: 0 !important;
      bottom: 46px !important;
      min-width: 190px !important;
      background: #fff !important;
      color: #111827 !important;
      border-radius: 10px !important;
      box-shadow: 0 18px 45px rgba(0, 0, 0, .25) !important;
      overflow: hidden !important;
      z-index: 1000001 !important;
    }

    .swal-calendar-menu-item {
      width: 100%;
      border: 0;
      background: #fff;
      padding: 10px 14px;
      text-align: left;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      color: #111827 !important;
    }

    .swal-calendar-menu-item:hover {
      background: #f3f4f6 !important;
    }

    .swal-calendar-menu-item.text-danger {
      color: #dc2626 !important;
    }
  </style>


  <style>
    /* =========================================================
                                       PRO LEFT CALENDAR SIDEBAR
                                       Colors:
                                       Blue  #74b2d4
                                       Green #8fc73e
                                       Text  dark gray
                                    ========================================================= */

    :root {
      --cal-blue: #74b2d4;
      --cal-green: #8fc73e;
      --cal-text: #374151;
      --cal-muted: #6b7280;
      --cal-border: #e5edf3;
      --cal-soft-blue: #eef8fd;
      --cal-soft-green: #f3faea;
      --cal-white: #ffffff;
      --cal-shadow: 0 14px 35px rgba(15, 23, 42, .08);
    }

    /* Keep employee sidebar stretched to the same height as the main calendar column */
    .content-body>.row {
      align-items: stretch;
    }

    .calendar-left-sidebar {
      height: auto;
      min-height: calc(100vh - 135px);
      align-self: stretch;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: all .25s ease;
    }

    .calendar-sidebar-shell {
      flex: 1 1 auto;
      min-height: 0;
      height: auto;
      display: flex;
      flex-direction: column;
      gap: 14px;
      overflow: hidden;
    }

    .calendar-sidebar-card {
      background: var(--cal-white);
      border: 1px solid var(--cal-border);
      border-radius: 22px;
      box-shadow: var(--cal-shadow);
      overflow: hidden;
    }

    .calendar-sidebar-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 15px 15px 12px;
      border-bottom: 1px solid #eef2f7;
    }

    .calendar-sidebar-kicker {
      display: block;
      font-size: 10px;
      font-weight: 900;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--cal-green);
      margin-bottom: 3px;
    }

    .calendar-sidebar-card-head h5 {
      margin: 0;
      font-size: 15px;
      font-weight: 900;
      color: var(--cal-text);
    }

    .calendar-sidebar-icon {
      width: 38px;
      height: 38px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      background: linear-gradient(135deg, var(--cal-blue), var(--cal-green));
      box-shadow: 0 10px 22px rgba(116, 178, 212, .25);
    }

    .calendar-mini-card {
      flex: 0 0 auto;
    }

    .employee-filter-card {
      flex: 1 1 auto;
      min-height: 0;
      display: flex;
      flex-direction: column;
    }

    /* Mini Calendar polish */
    #mini_calendar {
      padding: 10px;
    }

    #mini_calendar .fc {
      font-size: 11px;
    }

    #mini_calendar .fc-toolbar {
      margin-bottom: 7px !important;
    }

    #mini_calendar .fc-toolbar-title {
      font-size: 14px !important;
      font-weight: 900;
      color: var(--cal-text);
    }

    #mini_calendar .fc-button {
      width: 28px;
      height: 28px;
      padding: 0 !important;
      border-radius: 10px !important;
      background: var(--cal-soft-blue) !important;
      color: var(--cal-blue) !important;
      box-shadow: none !important;
    }

    #mini_calendar .fc-col-header-cell-cushion,
    #mini_calendar .fc-daygrid-day-number {
      color: var(--cal-text);
      font-weight: 800;
      text-decoration: none;
    }

    #mini_calendar .fc-day-today {
      background: var(--cal-soft-green) !important;
    }

    #mini_calendar .fc-daygrid-day-frame {
      border-radius: 12px;
    }

    /* Search */
    .employee-search-wrap {
      position: relative;
      margin: 14px 14px 10px;
    }

    .employee-search-wrap i {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--cal-blue);
      font-size: 16px;
      z-index: 2;
    }

    .employee-search-wrap .form-control {
      height: 42px;
      border-radius: 15px;
      border: 1px solid #dbeafe;
      background: #f8fafc;
      color: var(--cal-text);
      font-size: 12px;
      font-weight: 700;
      padding-left: 40px;
      box-shadow: none;
      transition: all .18s ease;
    }

    .employee-search-wrap .form-control::placeholder {
      color: #9ca3af;
      font-weight: 600;
    }

    .employee-search-wrap .form-control:focus {
      border-color: var(--cal-blue);
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
    }

    /* Selected bar */
    .employee-selected-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin: 0 14px 10px;
      padding: 8px 10px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--cal-soft-blue), var(--cal-soft-green));
      border: 1px solid #dbeafe;
    }

    .employee-selected-bar span {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--cal-text);
      font-size: 11px;
      font-weight: 900;
    }

    .employee-selected-bar span i {
      color: var(--cal-green);
    }

    .employee-selected-bar button {
      border: 0;
      background: #ffffff;
      color: var(--cal-muted);
      border-radius: 999px;
      padding: 5px 9px;
      font-size: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: all .18s ease;
    }

    .employee-selected-bar button:hover {
      color: #ffffff;
      background: var(--cal-blue);
    }

    /* Employee list */
    .employee_lists {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
      padding: 0 12px 14px;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
    }

    .employee_lists::-webkit-scrollbar,
    #slider_section::-webkit-scrollbar {
      width: 6px;
    }

    .employee_lists::-webkit-scrollbar-thumb,
    #slider_section::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 999px;
    }

    /* Department actions */
    .employee-sidebar-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 12px;
    }

    .employee-sidebar-actions button {
      border: 1px solid #dbeafe;
      background: #ffffff;
      color: var(--cal-text);
      border-radius: 13px;
      padding: 8px 9px;
      font-size: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: all .18s ease;
    }

    .employee-sidebar-actions button:hover {
      background: var(--cal-blue);
      color: #ffffff;
      border-color: var(--cal-blue);
    }

    /* Department accordion */
    .employee-department {
      border: 1px solid #e5edf3;
      border-radius: 18px;
      background: #ffffff;
      margin-bottom: 10px;
      overflow: hidden;
      box-shadow: 0 8px 18px rgba(15, 23, 42, .045);
    }

    .employee-department-header {
      width: 100%;
      border: 0;
      background: linear-gradient(135deg, #ffffff, #f8fbfd);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      padding: 11px 12px;
      cursor: pointer;
    }

    .employee-department-header:hover {
      background: linear-gradient(135deg, var(--cal-soft-blue), #ffffff);
    }

    .employee-department-left {
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
    }

    .employee-department-arrow {
      color: var(--cal-blue);
      font-size: 14px;
      transition: transform .18s ease;
    }

    .employee-department.is-collapsed .employee-department-arrow {
      transform: rotate(-90deg);
    }

    .employee-department-name {
      font-size: 11px;
      font-weight: 900;
      color: var(--cal-text);
      text-transform: uppercase;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .employee-department-count {
      min-width: 26px;
      height: 24px;
      padding: 0 8px;
      border-radius: 999px;
      background: var(--cal-soft-green);
      border: 1px solid rgba(143, 199, 62, .28);
      color: #4f7d16;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 900;
    }

    .employee-department-body {
      padding: 8px;
      background: #ffffff;
    }

    .employee-department.is-collapsed .employee-department-body {
      display: none;
    }

    /* Employee card */
    .employee-card-item {
      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      border: 1px solid transparent;
      border-radius: 16px;
      padding: 9px;
      margin-bottom: 7px;
      background: #ffffff;
      cursor: pointer;
      transition: all .18s ease;
    }

    .employee-card-item:hover {
      background: #f8fafc;
      border-color: #e5edf3;
      transform: translateY(-1px);
    }

    .employee-card-item.is-selected {
      background: linear-gradient(135deg, rgba(116, 178, 212, .12), rgba(143, 199, 62, .12));
      border-color: rgba(116, 178, 212, .35);
    }

    .employee-avatar-wrap {
      position: relative;
      width: 44px;
      min-width: 44px;
      height: 44px;
    }

    .employee-avatar-wrap img {
      width: 44px;
      height: 44px;
      border-radius: 16px;
      object-fit: cover;
      border: 2px solid transparent;
      padding: 2px;
      background: #ffffff;
      transition: all .18s ease;
    }

    .employee-card-item.is-selected .employee-avatar-wrap img,
    .employee-avatar-wrap img.emp_active {
      border-color: var(--cal-green) !important;
      box-shadow: 0 0 0 4px rgba(143, 199, 62, .15);
    }

    .employee-status-dot {
      position: absolute;
      right: -1px;
      bottom: -1px;
      width: 13px;
      height: 13px;
      border-radius: 999px;
      background: var(--cal-green);
      border: 2px solid #ffffff;
    }

    .employee-card-content {
      min-width: 0;
      flex: 1;
    }

    .employee-name {
      display: block;
      color: var(--cal-text);
      font-size: 12px;
      font-weight: 900;
      line-height: 1.2;
      text-transform: uppercase;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .employee-lastname {
      display: block;
      color: var(--cal-muted);
      font-size: 11px;
      font-weight: 700;
      line-height: 1.2;
      margin-top: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .employee-card-check {
      width: 24px;
      height: 24px;
      min-width: 24px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #cbd5e1;
      background: #f8fafc;
      transition: all .18s ease;
    }

    .employee-card-item.is-selected .employee-card-check {
      color: #ffffff;
      background: linear-gradient(135deg, var(--cal-blue), var(--cal-green));
    }

    /* Empty/error */
    .employee-empty-state {
      padding: 18px 12px;
      text-align: center;
      color: var(--cal-muted);
      font-size: 12px;
      font-weight: 700;
    }

    .employee-empty-state i {
      display: block;
      color: var(--cal-blue);
      font-size: 22px;
      margin-bottom: 7px;
    }

    /* Calendar width / height adjustment */
    .calender_section {
      min-height: calc(100vh - 135px);
      display: flex;
      flex-direction: column;
      transition: all .25s ease;
    }

    .calendar {
      flex: 1 1 auto;
      min-height: calc(100vh - 135px);
      background: #ffffff;
      border-radius: 22px;
      border: 1px solid #e5edf3;
      box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
      padding: 12px;
    }

    .employee-selected-bar-pro {
      align-items: flex-start;
      flex-direction: column;
    }

    .employee-selected-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 7px;
      width: 100%;
    }

    .employee-selected-actions button {
      width: 100%;
      border: 1px solid #dbeafe;
      background: #ffffff;
      color: var(--cal-text);
      border-radius: 12px;
      padding: 7px 8px;
      font-size: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: all .18s ease;
    }

    .employee-selected-actions button:hover {
      background: var(--cal-blue);
      color: #ffffff;
      border-color: var(--cal-blue);
    }

    .employee-department-tools {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px;
      padding: 8px 8px 0;
    }

    .employee-department-tools button {
      border: 1px solid #e5edf3;
      background: #f8fafc;
      color: #475569;
      border-radius: 10px;
      padding: 6px 7px;
      font-size: 10px;
      font-weight: 900;
      cursor: pointer;
      transition: all .18s ease;
    }

    .employee-department-tools button:hover {
      background: var(--cal-soft-blue);
      color: var(--cal-blue);
      border-color: #bfdbfe;
    }


    /* Responsive */
    @media (max-width: 1199.98px) {

      .calendar-left-sidebar,
      .calender_section,
      .calendar {
        height: auto;
        min-height: auto;
      }

      .calendar-left-sidebar {
        margin-bottom: 16px;
      }

      .calendar-sidebar-shell {
        height: auto;
      }

      .employee-filter-card {
        max-height: 420px;
      }

      .employee_lists {
        max-height: 310px;
      }
    }

    @media (max-width: 991.98px) {
      .calendar-sidebar-shell {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: start;
      }

      .calendar-mini-card,
      .employee-filter-card {
        min-width: 0;
      }
    }

    @media (max-width: 767.98px) {
      .calendar-sidebar-shell {
        grid-template-columns: 1fr;
      }

      .calendar {
        padding: 8px;
        border-radius: 18px;
      }

      .employee-filter-card {
        max-height: none;
      }

      .employee_lists {
        max-height: 360px;
      }
    }

    /* ==========================================
                                        ALL-DAY / GANZTAG EVENT DESIGN
                                      ========================================== */
    .fc .all-day-pill {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      min-height: 38px;
      padding: 7px 9px;
      border-radius: 12px;
      border: 1px solid transparent;
      box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
      overflow: hidden;
      transition: transform .15s ease, box-shadow .15s ease;
    }

    .fc .all-day-pill:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 18px rgba(15, 23, 42, 0.12);
    }

    .fc .all-day-pill__icon {
      width: 28px;
      min-width: 28px;
      height: 28px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, .55);
      backdrop-filter: blur(4px);
    }

    .fc .all-day-pill__icon svg {
      width: 16px;
      height: 16px;
      display: block;
    }

    .fc .all-day-pill__body {
      min-width: 0;
      flex: 1;
    }

    .fc .all-day-pill__label {
      display: inline-flex;
      align-items: center;
      padding: 2px 7px;
      border-radius: 999px;
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .03em;
      text-transform: uppercase;
      margin-bottom: 3px;
      background: rgba(255, 255, 255, .55);
    }

    .fc .all-day-pill__title {
      font-size: 11px;
      font-weight: 800;
      line-height: 1.2;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .fc .all-day-pill__meta {
      margin-top: 2px;
      font-size: 9px;
      line-height: 1.2;
      opacity: .88;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Urlaub */
    .fc .all-day-pill--holiday {
      background: linear-gradient(135deg, #fff3cd, #ffe4a3);
      border-color: #f6c96b;
      color: #7c4a03;
    }

    .fc .all-day-pill--holiday .all-day-pill__label {
      color: #7c4a03;
    }

    /* Krank - same visual style as Urlaub */
    .fc .all-day-pill--sick {
      background: linear-gradient(135deg, #fff3cd, #ffe4a3);
      border-color: #f6c96b;
      color: #7c4a03;
    }

    .fc .all-day-pill--sick .all-day-pill__label {
      color: #7c4a03;
    }

    /* Wiederkehrender Termin */
    .fc .all-day-pill--recurring {
      background: linear-gradient(135deg, #dff3ff, #c8e8fb);
      border-color: #8ecae6;
      color: #0b4f6c;
    }

    .fc .all-day-pill--recurring .all-day-pill__label {
      color: #0b4f6c;
    }

    /* Urlaubsantrag */
    .fc .all-day-pill--leave-request {
      background: linear-gradient(135deg, #e8f7d8, #d5efb7);
      border-color: #9bc56b;
      color: #335c12;
    }

    .fc .all-day-pill--leave-request .all-day-pill__label {
      color: #335c12;
    }

    /* Feiertag */
    .fc .all-day-pill--public-holiday {
      background: linear-gradient(135deg, #ffe5dc, #ffd0c2);
      border-color: #f29b7c;
      color: #8a2d14;
    }

    .fc .all-day-pill--public-holiday .all-day-pill__label {
      color: #8a2d14;
    }

    /* Cancelled recurring */
    .fc .all-day-pill--cancelled {
      background: linear-gradient(135deg, #f3f4f6, #e5e7eb) !important;
      border-color: #cbd5e1 !important;
      color: #6b7280 !important;
      opacity: .82;
    }

    .fc .all-day-pill--cancelled .all-day-pill__title {
      text-decoration: line-through;
    }

    .emp-select2-option {
      display: flex;
      align-items: center;
      gap: 10px;
      min-height: 42px;
      padding: 4px 2px;
    }

    .emp-select2-avatar {
      width: 32px;
      height: 32px;
      min-width: 32px;
      border-radius: 12px;
      object-fit: cover;
      border: 1px solid #e5e7eb;
      background: #fff;
    }

    .emp-select2-body {
      min-width: 0;
      flex: 1;
    }

    .emp-select2-name {
      font-size: 12px;
      font-weight: 900;
      color: #1f2937;
      line-height: 1.2;
    }

    .emp-select2-status {
      margin-top: 4px;
    }

    .emp-status-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      max-width: 100%;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 9px;
      font-weight: 900;
      line-height: 1.15;
      white-space: nowrap;
    }

    .emp-status-dot-ui {
      width: 6px;
      height: 6px;
      min-width: 6px;
      border-radius: 999px;
      background: currentColor;
    }

    .emp-status-badge--available {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
    }

    .emp-status-badge--leave {
      background: #fff7ed;
      color: #9a3412;
      border: 1px solid #fed7aa;
    }

    .emp-status-badge--sick {
      background: #fef2f2;
      color: #b91c1c;
      border: 1px solid #fecaca;
    }

    .emp-status-badge--series {
      background: #eff6ff;
      color: #1d4ed8;
      border: 1px solid #bfdbfe;
    }

    .emp-status-badge--request {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
    }


    .employee-availability-badge-wrap {
      margin-top: 6px;
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
    }

    .employee-card-item.has-unavailable-today {
      border-color: rgba(251, 146, 60, .32);
      background: linear-gradient(135deg, #ffffff, #fff7ed);
    }

    .employee-card-item.has-unavailable-today.is-selected {
      background: linear-gradient(135deg, rgba(116, 178, 212, .12), rgba(251, 146, 60, .13));
      border-color: rgba(251, 146, 60, .48);
    }

    .employee-status-dot.is-unavailable-today {
      background: #f97316;
    }

    .employee-status-dot.is-sick-today {
      background: #dc2626;
    }

    /* Keep selected chips compact */
    .select2-selection__choice .emp-select2-status {
      display: none;
    }

    .select2-selection__choice .emp-select2-option {
      min-height: 20px;
      gap: 5px;
      padding: 0;
    }

    .select2-selection__choice .emp-select2-avatar {
      width: 18px;
      height: 18px;
      min-width: 18px;
      border-radius: 50%;
    }

    .select2-selection__choice .emp-select2-name {
      font-size: 11px;
    }

    /* =========================================================
                                       ALL-DAY ABSENCE DETAILS MODAL
                                       For: Urlaub, Krank, Feiertag, Urlaubsantrag, Wiederkehrend
                                    ========================================================= */
    .fc .fc-event.calendar-absence-clickable,
    .fc .fc-event.recurring-leave,
    .fc .fc-event.sick-event,
    .fc .fc-event.holiday-event {
      cursor: pointer !important;
    }

    .fc .fc-event.calendar-absence-clickable:hover {
      filter: brightness(.97);
      box-shadow: 0 6px 18px rgba(15, 23, 42, .16) !important;
    }

    .absence-detail-popup {
      border-radius: 24px !important;
      padding: 0 !important;
      overflow: hidden !important;
      background: #ffffff !important;
      color: #374151 !important;
    }

    .absence-detail-popup .swal2-html-container {
      margin: 0 !important;
      padding: 0 !important;
      overflow: visible !important;
    }

    .absence-detail-popup .swal2-actions {
      padding: 0 20px 18px !important;
    }

    .absence-detail-modal {
      text-align: left;
      color: #374151;
      padding: 18px;
    }

    .absence-detail-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      padding: 18px;
      border-radius: 20px;
      background: linear-gradient(135deg, #eef8fd, #f3faea);
      border: 1px solid #e5edf3;
      margin-bottom: 14px;
    }

    .absence-detail-kicker {
      display: block;
      font-size: 10px;
      font-weight: 900;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: #8fc73e;
      margin-bottom: 4px;
    }

    .absence-detail-head h3 {
      margin: 0;
      font-size: 20px;
      line-height: 1.25;
      font-weight: 900;
      color: #374151;
    }

    .absence-detail-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
      padding: 8px 12px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 900;
      background: #ffffff;
      border: 1px solid #dbeafe;
      color: #374151;
    }

    .absence-detail-badge.sick {
      background: #fee2e2;
      color: #991b1b;
      border-color: #fecaca;
    }

    .absence-detail-badge.holiday,
    .absence-detail-badge.leave_request {
      background: #dcfce7;
      color: #166534;
      border-color: #bbf7d0;
    }

    .absence-detail-badge.recurring_leave {
      background: #ede9fe;
      color: #5b21b6;
      border-color: #ddd6fe;
    }

    .absence-detail-badge.public_holiday {
      background: #e0f2fe;
      color: #075985;
      border-color: #bae6fd;
    }

    .absence-detail-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
    }

    .absence-detail-card,
    .absence-detail-section {
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 12px;
      background: #ffffff;
      box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
    }

    .absence-detail-card strong,
    .absence-detail-section strong {
      display: block;
      font-size: 10px;
      color: #6b7280;
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-bottom: 6px;
    }

    .absence-detail-card span,
    .absence-detail-section p {
      display: block;
      margin: 0;
      font-size: 13px;
      line-height: 1.45;
      font-weight: 700;
      color: #111827;
      word-break: break-word;
    }

    .absence-detail-section {
      margin-top: 10px;
    }

    .absence-recurring-box {
      background: #faf5ff;
      border-color: #ddd6fe;
    }

    .absence-cancelled-box {
      background: #fff1f2;
      border-color: #fecdd3;
    }

    .absence-detail-actions {
      display: flex;
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 14px;
    }

    @media (max-width: 768px) {
      .absence-detail-grid {
        grid-template-columns: 1fr;
      }

      .absence-detail-head {
        flex-direction: column;
      }

      .absence-detail-badge {
        white-space: normal;
      }
    }

    /* Krank / Krankheit - same red visual style as Feiertag */
    .fc .all-day-pill--sick {
      background: linear-gradient(135deg, #ffe5dc, #ffd0c2);
      border-color: #f29b7c;
      color: #8a2d14;
    }

    .fc .all-day-pill--sick .all-day-pill__label {
      color: #8a2d14;
    }

    /* Recurring all-day custom typography */
    .fc .all-day-pill__recurring-badge {
      display: inline-flex;
      align-items: center;
      max-width: 100%;
      padding: 2px 7px;
      border-radius: 999px;
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .02em;
      background: rgba(255, 255, 255, .55);
      color: inherit;
      line-height: 1.2;
      margin-bottom: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .fc .all-day-pill__employee {
      font-size: 12px;
      font-weight: 900;
      line-height: 1.2;
      color: inherit;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .fc .all-day-pill__serie {
      margin-top: 2px;
      font-size: 8.5px;
      font-weight: 800;
      line-height: 1.2;
      opacity: .82;
      text-transform: uppercase;
      letter-spacing: .04em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Recurring all-day: same design, only adjusted hierarchy */
    .fc .all-day-pill--recurring .all-day-pill__label {
      font-size: 9px;
      font-weight: 900;
      margin-bottom: 3px;
      background: rgba(255, 255, 255, .55);
      color: inherit;
    }

    .fc .all-day-pill--recurring .all-day-pill__title {
      font-size: 12px;
      font-weight: 900;
      line-height: 1.2;
      color: inherit;
    }

    .fc .all-day-pill--recurring .all-day-pill__meta {
      font-size: 8.5px;
      font-weight: 800;
      opacity: .82;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
  </style>

  <style>
    /* =========================================================
                           BLOCKED DAYS: Feiertag / Urlaub / Krank / Serie
                        ========================================================= */

    .fc .fc-daygrid-day.is-calendar-blocked-day,
    .fc .fc-timegrid-col.is-calendar-blocked-day {
      background: linear-gradient(135deg, #fff1f2, #ffe4e6) !important;
      position: relative;
    }

    .fc .fc-daygrid-day.is-public-holiday-blocked,
    .fc .fc-timegrid-col.is-public-holiday-blocked {
      background: linear-gradient(135deg, #ffe5dc, #ffd0c2) !important;
    }

    .fc .fc-daygrid-day.is-employee-absence-blocked,
    .fc .fc-timegrid-col.is-employee-absence-blocked {
      background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
    }

    .calendar-day-lock-badge {
      position: absolute;
      top: 4px;
      right: 5px;
      z-index: 8;
      max-width: calc(100% - 10px);
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 7px;
      border-radius: 999px;
      background: rgba(255, 255, 255, .88);
      border: 1px solid rgba(248, 113, 113, .35);
      box-shadow: 0 6px 14px rgba(15, 23, 42, .10);
      color: #991b1b;
      font-size: 9px;
      font-weight: 900;
      line-height: 1.15;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      pointer-events: none;
    }

    .calendar-day-lock-badge.is-employee {
      color: #9a3412;
      border-color: rgba(251, 146, 60, .38);
    }

    .calendar-day-lock-word {
      display: inline-block;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .fc-timegrid-col .calendar-day-lock-badge {
      top: 3px;
      right: 4px;
      max-width: 92%;
    }

    .fc-timegrid-slot,
    .fc-daygrid-day-frame {
      position: relative;
    }

    .fc .fc-highlight {
      background: rgba(239, 68, 68, .14) !important;
    }

    /* =========================================================
                           KALENDER PAPIERKORB: gelöschte Termine
                        ========================================================= */
    .fc .fc-trashMode-button {
      background: #334155 !important;
      color: #fff !important;
      border-radius: 8px !important;
      font-weight: 800 !important;
    }

    .fc .fc-trashMode-button.is-active-trash,
    .fc .fc-trashMode-button.fc-button-active {
      background: #dc2626 !important;
      color: #fff !important;
    }

    .calendar-trash-banner {
      display: none;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin: 0 0 12px;
      padding: 10px 14px;
      border-radius: 14px;
      background: linear-gradient(135deg, #fff1f2, #ffe4e6);
      border: 1px solid #fecdd3;
      color: #991b1b;
      font-size: 13px;
      font-weight: 800;
    }

    .calendar-trash-banner.is-visible {
      display: flex;
    }

    .fc-event.calendar-deleted-appointment {
      opacity: .78;
      filter: grayscale(.25);
      border: 1px dashed #ef4444 !important;
      border-left: 5px solid #dc2626 !important;
    }

    .fc-event.calendar-deleted-appointment .fc-event-title,
    .fc-event.calendar-deleted-appointment .custom-event-title {
      text-decoration: line-through;
    }
  </style>

  <style>
    /* =========================================================
                       CRM Stage/Sub Stage for Main Appointment + linked Personal Task
                       ========================================================= */
    .ma-stage-box {
      border: 1px solid #e2e8f0;
      background: linear-gradient(135deg, #f8fafc, #ffffff);
      border-radius: 14px;
      padding: 12px;
      margin-top: 10px;
    }

    .ma-stage-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
    }

    @media (max-width: 768px) {
      .ma-stage-grid {
        grid-template-columns: 1fr;
      }
    }

    .ma-stage-label {
      display: block;
      font-size: 11px;
      font-weight: 900;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: .06em;
      margin-bottom: 5px;
    }

    .ma-stage-hint {
      margin-top: 7px;
      color: #64748b;
      font-size: 11px;
      font-weight: 700;
      line-height: 1.35;
    }

    .ma-stage-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #cbd5e1;
      border-left-width: 4px;
      border-radius: 999px;
      padding: 5px 10px;
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
      background: #fff;
      margin: 2px 4px 2px 0;
      max-width: 100%;
    }

    .ma-stage-pill small {
      color: #64748b;
      font-weight: 900;
      padding-left: 6px;
    }

    .ma-stage-empty {
      color: #94a3b8;
      font-size: 12px;
      font-weight: 800;
    }

    .calendar-event-chip.ma-stage-calendar-chip {
      background: rgba(255, 255, 255, .82);
      border: 1px solid rgba(15, 23, 42, .08);
      color: #0f172a;
    }

    .swal-stage-box {
      border: 1px solid rgba(255, 255, 255, .13);
      border-radius: 14px;
      background: rgba(255, 255, 255, .07);
      padding: 10px 12px;
      margin-top: 10px;
    }

    .swal-stage-box .ma-stage-label {
      color: #d1d5db;
      margin-bottom: 7px;
    }

    .swal-stage-box .ma-stage-pill {
      background: rgba(255, 255, 255, .95);
      color: #0f172a;
    }
  </style>



  <style>
    /* =========================================================
                     MODERN APPOINTMENT MODAL v3
                     - No visible scrollbars, body still scrolls
                     - Fixed header/footer
                     - Collapsible cards
                     - Select2 dropdowns remain clickable above modal
                     ========================================================= */
    html.ma-modal-open,
    body.ma-modal-open {
      overflow: hidden !important;
    }

    body.ma-modal-open::before {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 99980;
      background: rgba(15, 23, 42, .46);
      backdrop-filter: blur(8px);
      pointer-events: none;
    }

    .new_task.new_task_card {
      position: fixed !important;
      top: 50% !important;
      left: 50% !important;
      transform: translate(-50%, -50%) !important;
      width: min(1240px, calc(100vw - 34px)) !important;
      height: min(92vh, 920px) !important;
      max-width: none !important;
      max-height: 92vh !important;
      padding: 0 !important;
      border: 1px solid rgba(226, 232, 240, .95) !important;
      border-radius: 28px !important;
      background: linear-gradient(135deg, #f8fafc, #eef8fd 52%, #f3faea) !important;
      box-shadow: 0 35px 110px rgba(15, 23, 42, .36) !important;
      overflow: hidden !important;
      z-index: 99990 !important;
    }

    .new_task .card-header {
      flex: 0 0 auto;
      position: relative !important;
      top: auto !important;
      z-index: 5 !important;
      padding: 16px 20px !important;
      border-bottom: 1px solid rgba(226, 232, 240, .95) !important;
      background: rgba(255, 255, 255, .94) !important;
      backdrop-filter: blur(12px);
      box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
    }

    .new_task .card-header .title {
      margin: 0 !important;
      color: #0f172a !important;
      font-size: 20px !important;
      font-weight: 950 !important;
      letter-spacing: -.025em;
    }

    .new_task .card-header .title::after {
      content: "CRM Termin / Aufgabe";
      display: block;
      margin-top: 3px;
      color: #64748b;
      font-size: 11px;
      font-weight: 850;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    .new_task .card-header .btn {
      border-radius: 999px !important;
      font-weight: 900 !important;
    }

    .new_task .card-body {
      height: calc(100% - 73px) !important;
      min-height: 0 !important;
      padding: 0 !important;
      display: flex !important;
      flex-direction: column !important;
      overflow: hidden !important;
      background: transparent !important;
    }

    #task-store-form {
      flex: 1 1 auto;
      min-height: 0;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .new_task .modal-body {
      flex: 1 1 auto;
      min-height: 0;
      max-height: none !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      overscroll-behavior: contain;
      scrollbar-width: none;
      -ms-overflow-style: none;
      padding: 18px 22px 26px !important;
      background: transparent !important;
    }

    .new_task .modal-body::-webkit-scrollbar,
    .new_task::-webkit-scrollbar,
    .new_task .card-body::-webkit-scrollbar,
    .new_task .section-box::-webkit-scrollbar {
      width: 0 !important;
      height: 0 !important;
      display: none !important;
    }

    .new_task .modal-footer {
      flex: 0 0 auto;
      position: relative !important;
      bottom: auto !important;
      z-index: 6 !important;
      margin: 0 !important;
      padding: 14px 20px !important;
      border-top: 1px solid rgba(226, 232, 240, .95) !important;
      background: rgba(255, 255, 255, .94) !important;
      backdrop-filter: blur(12px);
      box-shadow: 0 -14px 30px rgba(15, 23, 42, .08);
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 10px;
    }

    .new_task .modal-footer .btn {
      min-height: 40px;
      border-radius: 999px !important;
      padding: 8px 18px !important;
      font-weight: 900 !important;
    }

    .new_task .modal-footer .save-task {
      background: linear-gradient(135deg, #0f172a, #1e293b) !important;
      border-color: #0f172a !important;
      box-shadow: 0 16px 32px rgba(15, 23, 42, .18);
    }

    .new_task .section-title {
      position: relative;
      margin: 0 0 -1px 0 !important;
      padding: 14px 48px 14px 16px !important;
      border: 1px solid #e2e8f0;
      border-radius: 18px 18px 0 0;
      background: linear-gradient(135deg, #ffffff, #f8fbfd);
      color: #0f172a !important;
      font-size: 12px !important;
      font-weight: 950 !important;
      letter-spacing: .08em;
      text-transform: uppercase;
      cursor: pointer;
      user-select: none;
      box-shadow: 0 8px 20px rgba(15, 23, 42, .045);
    }

    .new_task .section-title::before {
      content: "";
      display: inline-flex;
      width: 10px;
      height: 10px;
      margin-right: 9px;
      border-radius: 999px;
      background: linear-gradient(135deg, #74b2d4, #8fc73e);
      box-shadow: 0 0 0 5px rgba(116, 178, 212, .12);
      vertical-align: middle;
    }

    .new_task .section-title::after {
      content: "⌄";
      position: absolute;
      right: 16px;
      top: 50%;
      width: 28px;
      height: 28px;
      transform: translateY(-50%) rotate(0deg);
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #f1f5f9;
      color: #334155;
      font-size: 18px;
      font-weight: 950;
      transition: .18s ease;
    }

    .new_task .section-title.is-collapsed {
      border-radius: 18px;
      margin-bottom: 12px !important;
    }

    .new_task .section-title.is-collapsed::after {
      transform: translateY(-50%) rotate(-90deg);
      background: #eef8fd;
      color: #0f172a;
    }

    .new_task .section-title.is-collapsed+.section-box {
      display: none !important;
    }

    .new_task .section-box {
      margin: 0 0 14px 0 !important;
      padding: 16px !important;
      border: 1px solid #e2e8f0 !important;
      border-radius: 0 0 18px 18px !important;
      background: rgba(255, 255, 255, .92) !important;
      box-shadow: 0 14px 34px rgba(15, 23, 42, .07) !important;
      overflow: visible !important;
    }

    .new_task label {
      color: #475569 !important;
      font-size: 11px !important;
      font-weight: 900 !important;
      text-transform: uppercase;
      letter-spacing: .045em;
      margin-bottom: 6px !important;
    }

    .new_task .form-control,
    .new_task select.form-control,
    .new_task textarea.form-control,
    .new_task .select2-container--default .select2-selection--single,
    .new_task .select2-container--default .select2-selection--multiple {
      min-height: 42px !important;
      border: 1px solid #dbeafe !important;
      border-radius: 14px !important;
      background: #f8fafc !important;
      color: #0f172a !important;
      box-shadow: none !important;
      font-size: 13px !important;
      transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
    }

    .new_task textarea.form-control {
      min-height: 88px !important;
    }

    .new_task .form-control:focus,
    .new_task select.form-control:focus,
    .new_task textarea.form-control:focus,
    .new_task .select2-container--open .select2-selection {
      background: #ffffff !important;
      border-color: #74b2d4 !important;
      box-shadow: 0 0 0 4px rgba(116, 178, 212, .16) !important;
    }

    .new_task .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 40px !important;
      color: #0f172a !important;
      padding-left: 12px !important;
    }

    .new_task .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 40px !important;
      right: 8px !important;
    }

    .new_task .select2-container--default .select2-selection--multiple {
      padding: 4px 6px !important;
    }

    .new_task .select2-container--default .select2-selection--multiple .select2-selection__choice {
      border: 1px solid #bfdbfe !important;
      background: #eff6ff !important;
      color: #1e3a8a !important;
      border-radius: 999px !important;
      font-weight: 850 !important;
      padding: 3px 8px !important;
      margin-top: 4px !important;
    }

    .select2-container,
    .select2-dropdown,
    .select2-container--open {
      z-index: 100500 !important;
    }

    .select2-container--open .select2-dropdown {
      border: 1px solid #bfdbfe !important;
      border-radius: 16px !important;
      overflow: hidden;
      box-shadow: 0 24px 60px rgba(15, 23, 42, .24) !important;
    }


    /* SweetAlert must always appear above the custom appointment modal */
    body .swal2-container,
    body .swal2-container.swal2-backdrop-show,
    body .swal2-container.swal2-noanimation {
      z-index: 2147483647 !important;
    }

    body .swal2-popup {
      z-index: 2147483647 !important;
    }

    body .swal2-backdrop-show {
      background: rgba(15, 23, 42, .58) !important;
      backdrop-filter: blur(6px);
    }

    /* Select2 must be above the appointment modal, but below SweetAlert */
    body .select2-container--open,
    body .select2-dropdown {
      z-index: 2147483000 !important;
    }

    .appointment-stage-contact-block {
      margin-top: 12px;
    }

    .appointment-stage-contact-block.is-ready .ma-stage-box {
      border-color: rgba(143, 199, 62, .35);
      background: linear-gradient(135deg, #f7fee7, #ffffff);
    }

    .ma-stage-box {
      border-radius: 18px !important;
      padding: 14px !important;
      background: linear-gradient(135deg, #f8fafc, #ffffff) !important;
      border: 1px solid #dbeafe !important;
      box-shadow: 0 12px 26px rgba(15, 23, 42, .055);
    }

    .ma-stage-grid {
      gap: 12px !important;
    }

    .ma-stage-hint {
      color: #64748b !important;
      font-size: 11px !important;
      line-height: 1.45 !important;
    }

    .ma-stage-unavailable {
      padding: 11px 12px;
      border: 1px dashed #cbd5e1;
      border-radius: 14px;
      background: #f8fafc;
      color: #64748b;
      font-size: 12px;
      font-weight: 800;
    }

    #appointmentStagePreview {
      margin-top: 10px !important;
      padding-top: 8px;
      border-top: 1px dashed #cbd5e1;
    }

    .ma-collapse-tools {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-right: 8px;
    }

    .ma-collapse-tools .btn {
      background: #f8fafc !important;
      border: 1px solid #dbeafe !important;
      color: #334155 !important;
    }

    .ma-collapse-tools .btn:hover {
      background: #eef8fd !important;
      color: #0f172a !important;
    }

    @media (max-width: 991.98px) {
      .new_task.new_task_card {
        width: calc(100vw - 18px) !important;
        height: calc(100vh - 18px) !important;
        max-height: calc(100vh - 18px) !important;
        border-radius: 22px !important;
      }

      .new_task .card-header {
        padding: 13px 14px !important;
        align-items: flex-start !important;
        gap: 10px;
      }

      .new_task .card-header>div:last-child {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 6px;
      }

      .new_task .modal-body {
        padding: 14px 14px 22px !important;
      }

      .new_task .modal-footer {
        padding: 12px 14px !important;
      }
    }
  </style>



  <style>
    /* =========================================================
         URLAUBS-GANTT MODE
         Employees as rows + day/month timeline with filters and zoom.
      ========================================================= */
    .urlaub-gantt-panel {
      display: none;
      flex: 1 1 auto;
      min-height: calc(100vh - 135px);
      background: #ffffff;
      border-radius: 22px;
      border: 1px solid #e5edf3;
      box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
      overflow: hidden;
    }

    .urlaub-gantt-panel.is-visible {
      display: flex;
      flex-direction: column;
    }

    .calender_section.is-urlaub-gantt-mode .calendar {
      display: none !important;
    }

    .urlaub-gantt-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 14px;
      padding: 16px 18px;
      border-bottom: 1px solid #e5edf3;
      background: linear-gradient(135deg, #eef8fd, #f3faea);
    }

    .urlaub-gantt-kicker {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-size: 10px;
      font-weight: 900;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: #8fc73e;
      margin-bottom: 4px;
    }

    .urlaub-gantt-title {
      margin: 0;
      font-size: 18px;
      line-height: 1.25;
      font-weight: 900;
      color: #374151;
    }

    .urlaub-gantt-subtitle {
      margin-top: 4px;
      font-size: 12px;
      font-weight: 700;
      color: #64748b;
    }

    .urlaub-gantt-actions,
    .urlaub-gantt-tools,
    .urlaub-gantt-filter-row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 8px;
    }

    .urlaub-gantt-actions {
      justify-content: flex-end;
    }

    .urlaub-gantt-actions button,
    .urlaub-gantt-tools button,
    .urlaub-gantt-tools select {
      border: 1px solid #dbeafe;
      background: #ffffff;
      color: #374151;
      border-radius: 12px;
      padding: 8px 11px;
      font-size: 11px;
      font-weight: 900;
      cursor: pointer;
      transition: all .18s ease;
      min-height: 36px;
    }

    .urlaub-gantt-tools select {
      min-width: 160px;
      cursor: pointer;
    }

    .urlaub-gantt-actions button:hover,
    .urlaub-gantt-tools button:hover {
      background: #74b2d4;
      color: #ffffff;
      border-color: #74b2d4;
    }

    .urlaub-gantt-actions .urlaub-gantt-close-btn:hover {
      background: #dc2626;
      border-color: #dc2626;
    }

    .urlaub-gantt-toolbar {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      padding: 12px 18px;
      border-bottom: 1px solid #eef2f7;
      background: #ffffff;
    }

    .urlaub-gantt-filter-row label {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin: 0;
      padding: 7px 10px;
      border-radius: 999px;
      border: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #374151;
      font-size: 10px;
      font-weight: 900;
      cursor: pointer;
      user-select: none;
    }

    .urlaub-gantt-filter-row input {
      margin: 0;
      cursor: pointer;
    }

    .urlaub-gantt-zoom-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 52px;
      padding: 7px 9px;
      border-radius: 999px;
      background: #f8fafc;
      border: 1px solid #e5edf3;
      color: #64748b;
      font-size: 10px;
      font-weight: 900;
    }

    .urlaub-gantt-summary {
      display: grid;
      grid-template-columns: repeat(6, minmax(0, 1fr));
      gap: 10px;
      padding: 14px 18px;
      border-bottom: 1px solid #eef2f7;
      background: #ffffff;
    }

    .urlaub-gantt-stat {
      border: 1px solid #e5edf3;
      background: #f8fafc;
      border-radius: 16px;
      padding: 11px 12px;
    }

    .urlaub-gantt-stat strong {
      display: block;
      font-size: 18px;
      font-weight: 900;
      color: #111827;
      line-height: 1.1;
    }

    .urlaub-gantt-stat span {
      display: block;
      margin-top: 4px;
      font-size: 10px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #64748b;
    }

    .urlaub-gantt-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      padding: 0 18px 14px;
      background: #ffffff;
    }

    .urlaub-gantt-legend span {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 9px;
      border-radius: 999px;
      border: 1px solid #e5e7eb;
      font-size: 10px;
      font-weight: 900;
      color: #374151;
      background: #fff;
    }

    .urlaub-gantt-dot {
      width: 9px;
      height: 9px;
      min-width: 9px;
      border-radius: 999px;
      display: inline-block;
    }

    .urlaub-gantt-dot--holiday {
      background: #f97316;
    }

    .urlaub-gantt-dot--sick {
      background: #dc2626;
    }

    .urlaub-gantt-dot--recurring {
      background: #2563eb;
    }

    .urlaub-gantt-dot--request {
      background: #16a34a;
    }

    .urlaub-gantt-dot--work {
      background: #22c55e;
    }

    .urlaub-gantt-scroll {
      flex: 1 1 auto;
      min-height: 0;
      overflow: auto;
      background: #ffffff;
      position: relative;
    }

    .urlaub-gantt-table {
      width: max-content;
      border-collapse: separate;
      border-spacing: 0;
    }

    .urlaub-gantt-month-header,
    .urlaub-gantt-row {
      display: grid;
    }

    .urlaub-gantt-left-head,
    .urlaub-gantt-employee-cell {
      position: sticky;
      left: 0;
      z-index: 4;
      background: #ffffff;
      border-right: 1px solid #e5edf3;
    }

    .urlaub-gantt-left-head {
      top: 0;
      z-index: 12;
      padding: 12px 14px;
      font-size: 11px;
      font-weight: 900;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: .05em;
      border-bottom: 1px solid #e5edf3;
      background: #f8fafc;
    }

    .urlaub-gantt-days {
      position: sticky;
      top: 0;
      z-index: 10;
      display: grid;
      background: #f8fafc;
      border-bottom: 1px solid #e5edf3;
    }

    .urlaub-gantt-day {
      min-height: 44px;
      padding: 6px 4px;
      border-right: 1px solid #e5edf3;
      color: #334155;
      font-size: 10px;
      font-weight: 900;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      gap: 2px;
      text-align: center;
      line-height: 1.05;
    }

    .urlaub-gantt-day small {
      font-size: 8px;
      color: #94a3b8;
      font-weight: 900;
    }

    .urlaub-gantt-day.is-weekend {
      background: rgba(248, 250, 252, .92);
      color: #94a3b8;
    }

    .urlaub-gantt-day.is-month-start {
      border-left: 2px solid #74b2d4;
    }

    .urlaub-gantt-employee-cell {
      min-height: 58px;
      padding: 9px 12px;
      display: flex;
      align-items: center;
      gap: 10px;
      border-bottom: 1px solid #eef2f7;
    }

    .urlaub-gantt-avatar {
      width: 38px;
      height: 38px;
      min-width: 38px;
      border-radius: 14px;
      object-fit: cover;
      border: 1px solid #e5e7eb;
      background: #ffffff;
    }

    .urlaub-gantt-employee-name {
      min-width: 0;
      flex: 1;
    }

    .urlaub-gantt-employee-name strong {
      display: block;
      font-size: 12px;
      font-weight: 900;
      color: #111827;
      line-height: 1.2;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .urlaub-gantt-employee-name span {
      display: block;
      margin-top: 3px;
      font-size: 10px;
      font-weight: 800;
      color: #64748b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .urlaub-gantt-line {
      position: relative;
      min-height: 58px;
      border-bottom: 1px solid #eef2f7;
      background-image: linear-gradient(to right, rgba(226, 232, 240, .95) 1px, transparent 1px);
      background-repeat: repeat;
    }

    .urlaub-gantt-working {
      position: absolute;
      top: 50%;
      left: 14px;
      transform: translateY(-50%);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 10px;
      border-radius: 999px;
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
      font-size: 10px;
      font-weight: 900;
    }

    .urlaub-gantt-bar {
      position: absolute;
      height: 34px;
      min-width: 10px;
      border-radius: 999px;
      padding: 0 10px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      color: #ffffff;
      font-size: 10px;
      font-weight: 900;
      line-height: 1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      box-shadow: 0 8px 16px rgba(15, 23, 42, .12);
      cursor: pointer;
    }

    .urlaub-gantt-bar small {
      opacity: .88;
      font-size: 9px;
      font-weight: 900;
    }

    .urlaub-gantt-bar--holiday {
      background: linear-gradient(135deg, #fb923c, #f97316);
    }

    .urlaub-gantt-bar--sick {
      background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .urlaub-gantt-bar--recurring {
      background: linear-gradient(135deg, #60a5fa, #2563eb);
    }

    .urlaub-gantt-bar--request {
      background: linear-gradient(135deg, #4ade80, #16a34a);
    }

    .urlaub-gantt-empty {
      padding: 38px 18px;
      text-align: center;
      color: #64748b;
      font-size: 13px;
      font-weight: 800;
    }

    .urlaub-gantt-empty i {
      display: block;
      margin-bottom: 10px;
      color: #74b2d4;
      font-size: 28px;
    }

    .fc .fc-vacationGantt-button.is-active-urlaub,
    .fc .fc-vacationGantt-button.fc-button-active {
      background: #f97316 !important;
      color: #ffffff !important;
    }

    @media (max-width: 1199.98px) {
      .urlaub-gantt-summary {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 991.98px) {

      .urlaub-gantt-head,
      .urlaub-gantt-toolbar {
        flex-direction: column;
      }

      .urlaub-gantt-actions {
        justify-content: flex-start;
      }
    }

    @media (max-width: 575.98px) {
      .urlaub-gantt-summary {
        grid-template-columns: 1fr;
      }
    }
  </style>

@endsection
@section('content')

  <div class="app-content">
    <div class="content-wrapper">
      <div class="content-body">
        <div class="text-right mb-2">
          <button class="btn" data-toggle="modal" data-target="#calendarSettingsModal">
            <i class="feather icon-settings"></i> Einstellungen
          </button>
        </div>

        <div id="calendarTrashBanner" class="calendar-trash-banner">
          <span><i class="feather icon-trash-2"></i> Papierkorb-Modus aktiv: Es werden nur gelöschte Termine
            angezeigt.</span>
          <span>Wiederherstellen oder endgültig löschen über das Termin-Menü.</span>
        </div>

        <div class="row">
          <!-- Sidebar (Mini Calendar + Employee Filter) -->
          <div class="col-xl-2 col-lg-3 col-md-4 col-12 calendar-left-sidebar" id="slider_section">
            <div class="calendar-sidebar-shell">

              {{-- Mini Calendar --}}
              <div class="calendar-sidebar-card calendar-mini-card">
                <div class="calendar-sidebar-card-head">
                  <div>
                    <span class="calendar-sidebar-kicker">Kalender</span>
                    <h5>Übersicht</h5>
                  </div>
                  <span class="calendar-sidebar-icon">
                    <i class="feather icon-calendar"></i>
                  </span>
                </div>

                <div id="mini_calendar"></div>
              </div>

              {{-- Employee Filter --}}
              <div class="calendar-sidebar-card employee-filter-card">
                <div class="calendar-sidebar-card-head">
                  <div>
                    <span class="calendar-sidebar-kicker">Team</span>
                    <h5>Mitarbeiter</h5>
                  </div>
                  <span class="calendar-sidebar-icon">
                    <i class="feather icon-users"></i>
                  </span>
                </div>

                <div class="employee-search-wrap">
                  <i class="feather icon-search"></i>
                  <input type="text" class="form-control" name="searchEmployee" id="employee_get"
                    placeholder="Mitarbeiter suchen..." autocomplete="off">
                </div>

                <div class="employee-selected-bar employee-selected-bar-pro">
                  <span>
                    <i class="feather icon-check-circle"></i>
                    <strong id="selectedEmployeeCount">0</strong> ausgewählt
                  </span>

                  <div class="employee-selected-actions">
                    <button type="button" id="selectAllSidebarEmployees" title="Alle sichtbaren Mitarbeiter auswählen">
                      <i class="feather icon-check-square"></i>
                      Alle
                    </button>

                    <button type="button" id="clearSidebarEmployees" title="Alle Mitarbeiter abwählen">
                      <i class="feather icon-x"></i>
                      Keine
                    </button>
                  </div>
                </div>

                <div class="employee_lists" id="search_emp_result">
                  <!-- Dynamic employees -->
                </div>
              </div>

            </div>
          </div>

          <!-- Calendar Section -->
          <div class="col-xl-10 col-lg-9 col-md-8 col-12 calender_section">
            <div class="calendar"></div>

            <div id="urlaubGanttPanel" class="urlaub-gantt-panel" aria-live="polite">
              <div class="urlaub-gantt-head">
                <div>
                  <span class="urlaub-gantt-kicker">
                    <i class="feather icon-briefcase"></i> Urlaubsplanung
                  </span>
                  <h4 class="urlaub-gantt-title">Urlaubs-Gantt für ausgewählte Mitarbeiter</h4>
                  <div class="urlaub-gantt-subtitle" id="urlaubGanttSubtitle">
                    Wähle links Mitarbeiter aus und prüfe Urlaub, Krankheit, Serien und Urlaubsanträge nach Jahr oder
                    Monat.
                  </div>
                </div>

                <div class="urlaub-gantt-actions">
                  <button type="button" id="urlaubGanttPrevYear">
                    <i class="feather icon-chevron-left"></i> Vorjahr
                  </button>
                  <button type="button" id="urlaubGanttCurrentYear">
                    <span id="urlaubGanttYearLabel">Jahr</span>
                  </button>
                  <button type="button" id="urlaubGanttNextYear">
                    Nächstes Jahr <i class="feather icon-chevron-right"></i>
                  </button>
                  <button type="button" id="urlaubGanttRefresh">
                    <i class="feather icon-refresh-cw"></i> Aktualisieren
                  </button>
                  <button type="button" id="urlaubGanttClose" class="urlaub-gantt-close-btn">
                    <i class="feather icon-calendar"></i> Kalender
                  </button>
                </div>
              </div>

              <div class="urlaub-gantt-toolbar">
                <div class="urlaub-gantt-tools">
                  <select id="urlaubGanttMonthSelect" title="Monat wählen">
                    <option value="0">Alle Monate</option>
                    <option value="1">Januar</option>
                    <option value="2">Februar</option>
                    <option value="3">März</option>
                    <option value="4">April</option>
                    <option value="5">Mai</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Dezember</option>
                  </select>

                  <button type="button" id="urlaubGanttZoomOut" title="Zoom raus">
                    <i class="feather icon-zoom-out"></i>
                  </button>
                  <span class="urlaub-gantt-zoom-label" id="urlaubGanttZoomLabel">100%</span>
                  <button type="button" id="urlaubGanttZoomIn" title="Zoom rein">
                    <i class="feather icon-zoom-in"></i>
                  </button>
                  <button type="button" id="urlaubGanttZoomReset" title="Zoom zurücksetzen">Reset</button>
                </div>

                <div class="urlaub-gantt-filter-row" id="urlaubGanttFilterRow">
                  <label><input type="checkbox" data-urlaub-filter="holiday" checked> Urlaub</label>
                  <label><input type="checkbox" data-urlaub-filter="sick" checked> Krank</label>
                  <label><input type="checkbox" data-urlaub-filter="recurring_leave" checked> Serien</label>
                  <label><input type="checkbox" data-urlaub-filter="leave_request" checked> Urlaubsanträge</label>
                </div>
              </div>

              <div class="urlaub-gantt-summary" id="urlaubGanttSummary">
                <div class="urlaub-gantt-stat"><strong>0</strong><span>Mitarbeiter Gesamt</span></div>
                <div class="urlaub-gantt-stat"><strong>0</strong><span>Im Urlaub/Abwesend</span></div>
                <div class="urlaub-gantt-stat"><strong>0</strong><span>Arbeitet</span></div>
                <div class="urlaub-gantt-stat"><strong>0</strong><span>Abwesenheiten</span></div>
                <div class="urlaub-gantt-stat"><strong>0</strong><span>Urlaubstage</span></div>
                <div class="urlaub-gantt-stat"><strong>0</strong><span>Krank/Serie</span></div>
              </div>

              <div class="urlaub-gantt-legend">
                <span><i class="urlaub-gantt-dot urlaub-gantt-dot--holiday"></i> Urlaub</span>
                <span><i class="urlaub-gantt-dot urlaub-gantt-dot--sick"></i> Krank</span>
                <span><i class="urlaub-gantt-dot urlaub-gantt-dot--recurring"></i> Serie</span>
                <span><i class="urlaub-gantt-dot urlaub-gantt-dot--request"></i> Urlaubsantrag</span>
                <span><i class="urlaub-gantt-dot urlaub-gantt-dot--work"></i> Arbeitet</span>
              </div>

              <div class="urlaub-gantt-scroll" id="urlaubGanttScroll">
                <div class="urlaub-gantt-table" id="urlaubGanttTable">
                  <div class="urlaub-gantt-empty">
                    <i class="feather icon-loader"></i>
                    Urlaubsübersicht wird geladen...
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="cards new_task_card new_task" style="display:none">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="title">TERMIN ERSTELLEN</h3>
            <div>
              <span class="ma-collapse-tools">
                <button type="button" class="btn btn-sm" id="maExpandSections" title="Alle Karten öffnen">
                  <i class="feather icon-maximize-2"></i> Öffnen
                </button>
                <button type="button" class="btn btn-sm" id="maCollapseSections" title="Alle Karten schließen">
                  <i class="feather icon-minimize-2"></i> Schließen
                </button>
              </span>
              <button type="button" class="btn btn-sm btn-success mr-2" onclick="openWizard()"
                style="background-color: #74b91f; border-color: #74b91f; color: white;">
                <i class="feather icon-zap"></i> Fachwizard (Neuer Kunde)
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary close_task_window">
                <i class="feather icon-x"></i>
              </button>
            </div>
          </div>

          <div class="card-body">
            <form id="task-store-form">
              @csrf
              <input type="hidden" name="id" id="appointment_id">
              <input type="hidden" name="contact_mode" id="contact_mode" value="new">
              <input type="hidden" id="products" name="products">
              <input type="hidden" id="appointment_lead_product_list_id" name="lead_product_list_id">

              <div class="modal-body">

                {{-- SECTION: Kontakt / Ticketwahl --}}
                <div class="section-title">Kontakt</div>
                <div class="section-box">
                  <div class="form-row">
                    <div class="col-md-12 mb-1">
                      <label>Typ</label><br>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input contact-type-toggle" type="radio" name="contact_mode_radio"
                          id="newContact" value="new" checked>
                        <label class="form-check-label" for="newContact">Neu</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input contact-type-toggle" type="radio" name="contact_mode_radio"
                          id="selectContact" value="select">
                        <label class="form-check-label" for="selectContact">Kontakt</label>
                      </div>

                    </div>
                  </div>

                  {{-- Neu / Kontakt-Auswahl --}}
                  <div class="form-row">
                    <div class="col-md-12 contact-name-block">
                      <label for="name" id="contactNameLabel">Kunde *</label>
                      <input type="text" id="name" class="form-control name" name="name">
                    </div>

                    <div class="col-md-12 contact-select-block d-none">
                      <label for="customer_id" id="contactSelectLabel">Kunde/Kontakt *</label>
                      <select name="customer_id" id="customer_id" class="contact_list" style="width:100%"></select>
                      <input type="hidden" name="contact_type" id="contact_type" value="">
                    </div>

                    <div class="col-md-12 product-select-block d-none">
                      <label for="productSelect">Object/Produkt</label>
                      <select id="productSelect" name="productSelect[]" class="form-control" multiple
                        style="width:100%"></select>

                    </div>

                    <div class="col-md-12 appointment-stage-contact-block d-none" id="appointmentStageContactBlock">
                      <div class="ma-stage-box">
                        <div class="d-flex align-items-start justify-content-between flex-wrap"
                          style="gap:10px;margin-bottom:10px;">
                          <div>
                            <label class="ma-stage-label" style="margin-bottom:2px;">CRM Stage nach Produkt</label>
                            <div class="ma-stage-hint" style="margin-top:0;">Wählen Sie zuerst einen Kontakt und danach
                              ein Produkt. Dann wird die Stage/Sub Stage hier aktiv.</div>
                          </div>
                          <span class="customer-products-badge" id="maStageProductStatus"><i
                              class="feather icon-link"></i> Produkt erforderlich</span>
                        </div>
                        <div class="ma-stage-grid">
                          <div>
                            <label class="ma-stage-label" for="appointment_lead_stage_id">Stage</label>
                            <select name="lead_stage_id" id="appointment_lead_stage_id"
                              class="form-control appointment-stage-select" style="width:100%">
                              <option value="">Keine Stage</option>
                              @foreach($appointmentLeadStagePayload as $stage)
                                <option value="{{ data_get($stage, 'id') }}" data-color="{{ data_get($stage, 'color') }}"
                                  data-key="{{ data_get($stage, 'key') }}">
                                  {{ data_get($stage, 'name') }}
                                </option>
                              @endforeach
                            </select>
                            <div class="ma-stage-hint">Diese Stage wird auf dem Termin und auf der verknüpften
                              persönlichen
                              Aufgabe gespeichert.</div>
                          </div>

                          <div>
                            <label class="ma-stage-label" for="appointment_lead_stage_sub_stage_id">Sub Stage</label>
                            <select name="lead_stage_sub_stage_id" id="appointment_lead_stage_sub_stage_id"
                              class="form-control appointment-sub-stage-select" style="width:100%">
                              <option value="">Zuerst Stage wählen</option>
                            </select>
                            <div class="ma-stage-hint">Die Sub Stage lädt abhängig von der gewählten Stage.</div>
                          </div>
                        </div>

                        <div class="mt-1" id="appointmentStagePreview">
                          <span class="ma-stage-empty">Noch keine Stage gewählt.</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- SECTION: Termin-Daten --}}
                <div class="section-title">Termin</div>
                <div class="section-box">
                  <div class="form-row">
                    <div class="col-md-10 col-10 mb-1">
                      <label for="appointment_type">Art des Termins</label>
                      <input type="text" class="form-control" id="appointment_type" name="appointment_type"
                        value="{{ old('appointment_type') }}">
                    </div>
                    <div class="col-md-2 col-2 mb-1 d-flex align-items-end">
                      <input type="hidden" name="color" id="color" value="#8fc73e">
                      <div class="btn-group dropup dropdown-icon-wrapper w-100" id="color_drop_down">
                        <button type="button" class="btn btn-light btn-block" data-toggle="dropdown" aria-haspopup="true"
                          aria-expanded="true">
                          <i class="fa fa-square" id="colorIcon" style="color:#8fc73e;"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                          <span class="dropdown-item" data-value="#8fc73e"><i class="fa fa-square"
                              style="color:#8fc73e;"></i> Grün</span>
                          <span class="dropdown-item" data-value="#ff0000"><i class="fa fa-square"
                              style="color:#ff0000;"></i> Rot</span>
                          <span class="dropdown-item" data-value="#0000ff"><i class="fa fa-square"
                              style="color:#0000ff;"></i> Blau</span>
                          <span class="dropdown-item" data-value="#ffff00"><i class="fa fa-square"
                              style="color:#ffff00;"></i> Gelb</span>
                          <span class="dropdown-item" data-value="#ff00ff"><i class="fa fa-square"
                              style="color:#ff00ff;"></i> Magenta</span>
                          <span class="dropdown-item" data-value="#00ffff"><i class="fa fa-square"
                              style="color:#00ffff;"></i> Cyan</span>
                          <span class="dropdown-item" data-value="#000000"><i class="fa fa-square"
                              style="color:#000000;"></i> Schwarz</span>
                          <span class="dropdown-item" data-value="#808080"><i class="fa fa-square"
                              style="color:#808080;"></i> Grau</span>
                          <span class="dropdown-item" data-value="#ffa500"><i class="fa fa-square"
                              style="color:#ffa500;"></i> Orange</span>
                          <span class="dropdown-item" data-value="#800080"><i class="fa fa-square"
                              style="color:#800080;"></i> Lila</span>
                          <span class="dropdown-item" data-value="#8b4513"><i class="fa fa-square"
                              style="color:#8b4513;"></i> Braun</span>
                          <span class="dropdown-item" data-value="#4682b4"><i class="fa fa-square"
                              style="color:#4682b4;"></i> Stahlblau</span>
                          <span class="dropdown-item" data-value="#5f9ea0"><i class="fa fa-square"
                              style="color:#5f9ea0;"></i> Kadettenblau</span>
                          <span class="dropdown-item" data-value="#d2691e"><i class="fa fa-square"
                              style="color:#d2691e;"></i> Schokoladenbraun</span>
                          <span class="dropdown-item" data-value="#2e8b57"><i class="fa fa-square"
                              style="color:#2e8b57;"></i> Seegrün</span>
                          <span class="dropdown-item" data-value="#dc143c"><i class="fa fa-square"
                              style="color:#dc143c;"></i> Karmesinrot</span>
                          <span class="dropdown-item" data-value="#7fffd4"><i class="fa fa-square"
                              style="color:#7fffd4;"></i> Aquamarin</span>
                          <span class="dropdown-item" data-value="#9932cc"><i class="fa fa-square"
                              style="color:#9932cc;"></i> Dunkles Lila</span>
                          <span class="dropdown-item" data-value="#ff6347"><i class="fa fa-square"
                              style="color:#ff6347;"></i> Tomate</span>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-6 col-12 mb-1">
                      <label for="start_date">Startdatum *</label>
                      <input type="date" id="start_date" class="form-control" name="start_date">
                    </div>
                    <div class="col-md-6 col-12 mb-1">
                      <label for="end_date">Enddatum *</label>
                      <input type="date" id="end_date" class="form-control" name="end_date">
                    </div>

                    <div class="col-md-4 col-12 mb-1">
                      <label for="start_time">Startzeit *</label>
                      <input type="time" id="start_time" class="form-control" name="start_time">
                    </div>
                    <div class="col-md-4 col-12 mb-1">
                      <label for="end_time">Endzeit</label>
                      <input type="time" id="end_time" class="form-control" name="end_time">
                    </div>
                    <div class="col-md-4 col-12 mb-1">
                      <label for="total_time">Dauer</label>
                      <input type="number" id="total_time" class="form-control" name="total_time">
                    </div>
                  </div>
                </div>

                {{-- SECTION: Einstellungen / Anfrage --}}
                <div class="section-title">Einstellungen</div>
                <div class="section-box">
                  <div class="form-row">
                    <div class="col-md-4 mb-1">
                      <label for="switchPublic">Öffentlich</label>
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="switchPublic" name="public" checked>
                        <label class="custom-control-label" for="switchPublic">
                          <span class="switch-icon-left"><i class="feather icon-unlock"></i></span>
                          <span class="switch-icon-right"><i class="feather icon-lock"></i></span>
                        </label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-1">
                      <label for="switchContact">Anfrage</label>
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="switchContact" name="is_contact">
                        <label class="custom-control-label" for="switchContact">
                          <span class="switch-icon-left"><i class="feather icon-user"></i></span>
                          <span class="switch-icon-right"><i class="feather icon-user-x"></i></span>
                        </label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-1">
                      <label for="switchReport">Report</label>
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="switchReport" name="is_report">
                        <label class="custom-control-label" for="switchReport">
                          <span class="switch-icon-left"><i class="feather icon-file-text"></i></span>
                          <span class="switch-icon-right"><i class="feather icon-file"></i></span>
                        </label>
                      </div>
                    </div>

                    <div class="col-md-6 mb-1" id="preTypeBox" style="display:none;">
                      <label for="pre_type">Typ</label>
                      <select name="pre_type" id="pre_type" class="form-control select2">
                        <option value="">Auswählen</option>
                        <option value="Lead">Lead</option>
                        <option value="Lieferant">Lieferant</option>
                        <option value="Hersteller">Hersteller</option>
                        <option value="Kooperationspartner">Kooperationspartner</option>
                        <option value="Architekt">Architekt</option>
                        <option value="Nachunternehmer">Nachunternehmer</option>
                        <option value="Bank">Bank</option>
                        <option value="Versicherung">Versicherung</option>
                        <option value="Bewerber">Bewerber</option>
                        <option value="Sonstige">Sonstige</option>
                      </select>
                    </div>

                    <div class="col-md-6 mb-1" id="sourceBox" style="display:none;">
                      <label for="source">Quelle</label>
                      <select name="source" id="source" class="form-control" style="width:100%">
                        <option></option>
                        <option value="Telefonisch">Telefonisch</option>
                        <option value="Persönlich">Persönlich</option>
                        <option value="Mail">Mail</option>
                        <option value="Nachbar">Nachbar</option>
                        <option value="Empfehlung">Empfehlung</option>
                        <option value="Solarrechner">Solarrechner</option>
                        <option value="Herstellerlead">Herstellerlead</option>
                        <option value="Event">Event</option>
                        <option value="Messe">Messe</option>
                        <option value="Hausmesse">Hausmesse</option>
                        <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit</option>
                      </select>
                    </div>
                  </div>

                  <div class="mt-2 d-none" id="inquiryPreviewWrapper">
                    <label>Anfrage-Übersicht</label>
                    <div class="table-responsive">
                      <table class="table table-sm table-bordered mb-0" id="inquiryPreviewTable">
                        <thead>
                          <tr>
                            <th>Produkt</th>
                            <th>Abteilung</th>
                            <th>Leistung/Service</th>
                            <th>Innendienst</th>
                            <th>Außendienst</th>
                          </tr>
                        </thead>
                        <tbody id="inquiryPreviewBody">
                        </tbody>
                      </table>
                    </div>
                    <div class="mt-1 text-right">
                      <button type="button" class="btn btn-sm btn-outline-primary" id="addInquiryRow">
                        <i class="feather icon-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>

                {{-- SECTION: Teilnehmer --}}
                <div class="section-title">Teilnehmer</div>
                <div class="section-box" id="participantsBlock">
                  <div class="d-flex align-items-center mb-1">
                    <button type="button" id="btnClearEmployees" class="btn btn-sm btn-light mr-1">
                      <i class="feather icon-x-circle"></i> Auswahl leeren
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="openPickerBtn">
                      <i class="feather icon-users"></i> Auswahl öffnen
                    </button>
                  </div>

                  <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                    @foreach ($employees as $emp)
                      <option value="{{ $emp->id }}"
                        data-image="{{ asset('images/employee/' . ($emp->image ?: 'default-avatar.png')) }}"
                        data-leave-from="{{ $emp->current_leave_from }}" data-leave-to="{{ $emp->current_leave_to }}"
                        data-leave-type="{{ $emp->current_leave_type }}"
                        data-leave-reason="{{ $emp->current_leave_reason }}"
                        data-leave-status="{{ $emp->current_leave_status }}" data-sick-from="{{ $emp->current_sick_from }}"
                        data-sick-to="{{ $emp->current_sick_to }}" data-sick-status="{{ $emp->current_sick_status }}"
                        data-sick-msg="{{ $emp->current_sick_msg }}">
                        {{ trim($emp->name . ' ' . $emp->lastname) }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- SECTION: Ort & Kontakt --}}
                <div class="section-title">Ort & Kontakt</div>
                <div class="section-box">
                  <div class="form-row">
                    <div class="col-md-6 mb-1" id="intern" style="display:none;">
                      <label for="branch_address_id">Adresse (Betrieb)</label>
                      <select name="branch_address_id" class="form-control">
                        <option></option>
                        @foreach ($branch_addresses as $address)
                          <option value="{{ $address->id }}" data-street="{{ $address->street }}"
                            data-latitude="{{ $address->latitude }}" data-longitude="{{ $address->longitude }}"
                            data-city="{{ $address->city }}" data-postcode="{{ $address->postcode }}">
                            {{ $address->branch_initial }} - {{ $address->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-6 mb-1" id="extern">
                      <label for="full_address">Adresse</label>
                      <input id="full_address" type="text" class="form-control form-element"
                        placeholder="Adresse eingeben" name="full_address">

                      <div id="appointmentAddressChoiceWrap" class="mt-1 d-none">
                        <label for="appointment_address_choice" class="mb-25" style="font-size:12px;font-weight:800;">
                          Welche Adresse verwenden?
                        </label>
                        <select id="appointment_address_choice" class="form-control" style="width:100%"></select>
                        <small class="text-muted d-block mt-25">
                          Kunde, Objekt oder Produkt kann eine andere Adresse haben.
                        </small>
                      </div>

                      <input type="hidden" id="address_source_type" name="address_source_type">
                      <input type="hidden" id="address_source_id" name="address_source_id">
                      <input type="hidden" id="selected_alternative_id" name="selected_alternative_id">

                      <input type="hidden" id="street-input" name="street">
                      <input type="hidden" id="city-input" name="city">
                      <input type="hidden" id="latitude-input" name="latitude">
                      <input type="hidden" id="longitude-input" name="longitude">
                      <input type="hidden" id="postal_code-input" name="postcode">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="execution_type">Ort des Termins</label>
                      <select name="execution_type" id="execution_type" class="form-control">
                        <option value="internal">Intern</option>
                        <option value="external" selected>Extern</option>
                        <option value="online">Online</option>
                        <option value="telephone">Telefon</option>
                      </select>
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="phone">Telefon</label>
                      <input type="text" class="form-control phone" name="phone" id="phone" value="{{ old('phone') }}">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="email">Email <small>Optional</small></label>
                      <input type="email" class="form-control email" name="email" id="email" value="{{ old('email') }}">
                    </div>

                    <div class="col-md-6 mb-1" id="link_section" style="display:none;">
                      <label for="link">Link</label>
                      <input type="text" class="form-control" id="link" name="link" value="{{ old('link') }}">
                    </div>

                    <div class="col-md-6 mb-1">
                      <label for="branch_id">Betrieb</label>
                      <select name="branch_id" id="branch_id" class="selectables" style="width:100%">
                        <option></option>
                        @foreach($branches as $br)
                          <option value="{{ $br->id }}">{{ $br->branch }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-12 mb-1">
                      <label for="description">Beschreibung</label>
                      <textarea name="description" class="form-control" id="description" rows="2"></textarea>
                    </div>
                  </div>
                </div>

                {{-- SECTION: Nachfass / Nächste Schritte --}}
                <div class="section-title">Nachfass & nächste Schritte</div>
                <div class="section-box">
                  <div class="form-row">
                    <div class="col-md-4 col-12 mb-1">
                      <label for="reminder_date">Nachfasstermin</label>
                      <input type="date" name="reminder_date" class="form-control" id="reminder_date">
                    </div>
                    <div class="col-md-4 col-12 mb-1">
                      <label for="next_step">Nächster Schritt</label>
                      <select name="next_step" class="form-control select2" id="next_step" style="width:100%">
                        <option value="">Bitte wählen</option>
                        <option value="Rückruf erledigen">Rückruf erledigen</option>
                        <option value="Problem klären">Problem klären</option>
                        <option value="E-Mail senden">E-Mail senden</option>
                        <option value="Angebot nachfassen">Angebot nachfassen</option>
                        <option value="Projektbesprechung vorbereiten">Projektbesprechung vorbereiten</option>
                        <option value="Kein weiterer Schritt">Kein weiterer Schritt</option>
                      </select>
                    </div>
                    <div class="col-md-4 col-12 mb-1">
                      <label for="report_responsible">Verantwortlicher</label>
                      <select name="report_responsible[]" class="form-control select2" id="report_responsible"
                        style="width:100%">
                        <option value="">Bitte wählen</option>
                        @foreach ($allEmployees as $employee)
                          <option value="{{ $employee->id }}">{{ $employee->name }} {{ $employee->lastname }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                {{-- SECTION: Priorität & Wiederholung --}}
                <div class="section-title">Priorität & Wiederholung</div>
                <div class="section-box">
                  <div class="form-row">
                    <div class="col-md-6 col-12 mb-1">
                      <label for="priority">Priorität</label>
                      <select name="priority" class="form-control" id="priority">
                        <option value="normal">Keiner</option>
                        <option value="medium">Medium</option>
                        <option value="high">Hoch</option>
                        <option value="very high">Sehr Wichtig</option>
                      </select>
                    </div>
                    <div class="col-md-6 col-12 mb-1">
                      <label for="date_type">Wiederholung</label>
                      <select name="date_type" id="date_type" class="form-control">
                        <option>Wählen</option>
                        <option value="day">Ganzer Tag</option>
                        <option value="week">7 Tage (Eine Woche)</option>
                        <option value="daily">Täglich</option>
                        <option value="weekly">Wochen</option>
                        <option value="monthly">Monatlich</option>
                      </select>
                    </div>

                    <div class="col-md-6 col-12 mb-1" id="week_dropdown_container" style="display:none;">
                      <label for="week_select">Wähle Woche(n)</label>
                      <select id="week_select" name="week_select[]" class="form-control" style="width:100%;"></select>
                    </div>

                    <div class="col-md-6 col-12 mb-1 from_day">
                      <label for="from_day">Von (Wochentag)</label>
                      <select name="from_day" id="from_day" class="form-control">
                        <option value="monday">Montag</option>
                        <option value="tuesday">Dienstag</option>
                        <option value="wednesday">Mittwoch</option>
                        <option value="thursday">Donnerstag</option>
                        <option value="friday">Freitag</option>
                        <option value="saturday">Samstag</option>
                        <option value="sunday">Sonntag</option>
                      </select>
                    </div>

                    <div class="col-md-6 col-12 mb-1 to_day">
                      <label for="to_day">Zu (Wochentag)</label>
                      <select name="to_day" id="to_day" class="form-control">
                        <option value="monday">Montag</option>
                        <option value="tuesday">Dienstag</option>
                        <option value="wednesday">Mittwoch</option>
                        <option value="thursday">Donnerstag</option>
                        <option value="friday">Freitag</option>
                        <option value="saturday">Samstag</option>
                        <option value="sunday">Sonntag</option>
                      </select>
                    </div>

                    <div class="col-md-6 col-12 mb-1 from_month">
                      <label for="from_month">Von (Monat)</label>
                      <select name="from_month" id="from_month" class="form-control">
                        <option value="january">Januar</option>
                        <option value="february">Februar</option>
                        <option value="march">März</option>
                        <option value="april">April</option>
                        <option value="may">Mai</option>
                        <option value="june">Juni</option>
                        <option value="july">Juli</option>
                        <option value="august">August</option>
                        <option value="september">September</option>
                        <option value="october">Oktober</option>
                        <option value="november">November</option>
                        <option value="december">Dezember</option>
                      </select>
                    </div>

                    <div class="col-md-6 col-12 mb-1 to_month">
                      <label for="to_month">Zu (Monat)</label>
                      <select name="to_month" id="to_month" class="form-control">
                        <option value="january">Januar</option>
                        <option value="february">Februar</option>
                        <option value="march">März</option>
                        <option value="april">April</option>
                        <option value="may">Mai</option>
                        <option value="june">Juni</option>
                        <option value="july">Juli</option>
                        <option value="august">August</option>
                        <option value="september">September</option>
                        <option value="october">Oktober</option>
                        <option value="november">November</option>
                        <option value="december">Dezember</option>
                      </select>
                    </div>
                  </div>
                </div>

              </div>{{-- /modal-body --}}

              <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm close_task_window">
                  <i class="feather icon-x"></i> abbrechen
                </button>
                <button type="button" class="btn btn-primary btn-sm save-task">
                  <i class="feather icon-save"></i> speichern
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Team/Employee Picker Modal -->
  <div class="modal fade" id="pickerModal" tabindex="-1" role="dialog" aria-labelledby="pickerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title" id="pickerModalLabel">Teilnehmer auswählen</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body p-0">
          <ul class="nav nav-tabs px-2 pt-2" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="tab-employees" data-toggle="tab" href="#pane-employees"
                role="tab">Mitarbeiter</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="tab-teams" data-toggle="tab" href="#pane-teams" role="tab">Teams</a>
            </li>
          </ul>

          <div class="tab-content p-2">
            <!-- Tab 1: Employees -->
            <div class="tab-pane fade show active" id="pane-employees" role="tabpanel" aria-labelledby="tab-employees">
              <div class="form-group mb-2">
                <input type="text" class="form-control" id="pickerEmployeeSearch" placeholder="Mitarbeiter suchen…">
              </div>
              <div id="pickerEmployeeGrid" class="d-flex flex-wrap" style="gap:10px;"></div>
            </div>

            <!-- Tab 2: Teams -->
            <div class="tab-pane fade" id="pane-teams" role="tabpanel" aria-labelledby="tab-teams">
              <div class="row no-gutters">
                <div class="col-md-4 border-right">
                  <div class="form-group px-2">
                    <input type="text" class="form-control" id="pickerTeamSearch" placeholder="Team suchen…">
                  </div>
                  <div id="pickerTeamList" style="max-height: 60vh; overflow:auto;"></div>
                </div>
                <div class="col-md-8">
                  <div class="d-flex justify-content-between align-items-center px-2">
                    <h6 class="m-0"><span id="pickerTeamTitle">Team</span></h6>
                    <div>
                      <button class="btn btn-sm btn-light" id="pickerSelectAllTeam">Alle markieren</button>
                      <button class="btn btn-sm btn-light" id="pickerClearTeam">Leeren</button>
                      <button class="btn btn-sm btn-success" id="pickerApplyTeam"><i class="feather icon-check"></i>
                        Übernehmen</button>
                    </div>
                  </div>
                  <div id="pickerTeamMembers" class="p-2"></div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /modal-body -->

        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
          <button type="button" class="btn btn-primary" id="pickerApplyAll"><i class="feather icon-save"></i> Auswahl
            übernehmen</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Calendar Settings Modal -->
  <div class="modal fade" id="calendarSettingsModal" tabindex="-1" role="dialog" aria-labelledby="calendarSettingsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form id="calendarSettingsForm" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="calendarSettingsLabel">Einstellungen</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <!-- Favorite Employees -->
          <div class="form-group">
            <label for="favoriteEmployees">Favoriten Mitarbeiter</label>
            <select id="favoriteEmployees" class="form-control employee" multiple style="width:100%;">
              @foreach($allEmployees as $emp)
                <option value="{{ $emp->id }}" data-image="/images/employee/{{ $emp->image }}">
                  {{ $emp->name }} {{ $emp->lastname }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Hidden Views -->
          <div class="form-group">
            <label>Ausgeblendete Ansichten</label><br>
            <label><input type="checkbox" name="hidden_views[]" value="multiMonthYear"> Jahr</label><br>
            <label><input type="checkbox" name="hidden_views[]" value="dayGridMonth"> Monat</label><br>
            <label><input type="checkbox" name="hidden_views[]" value="timeGridWeek"> Woche</label><br>
            <label><input type="checkbox" name="hidden_views[]" value="timeGridDay"> Tag</label><br>
            <label><input type="checkbox" name="hidden_views[]" value="listWeek"> Übersicht</label><br>
            <label><input type="checkbox" name="hidden_views[]" value="vacationGantt"> Urlaubs</label><br>
          </div>

          <!-- Calendar Color -->
          <div class="form-group">
            <label for="calendarColorPicker">Kalenderfarbe</label>
            <select id="calendarColorPicker" class="form-control">
              <option value="default">Standard</option>
              <option value="black">Schwarz</option>
              <option value="red">Rot</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Speichern</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
        </div>
      </form>
    </div>
  </div>

  @include('admin.todo.personal.partials._wizard_modal')

@endsection

@section('script')

  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="{{asset('app-assets/js/scripts/tooltip/tooltip.js')}}"></script>
  <script>
    $(document).ready(function () {
      $('.selectables').select2({
        tags: true,
        placeholder: "Wählen",
        allowClear: true
      });
    });
  </script>


  <script>
    const baseUrl = "{{ asset('images/employee/')}}";
  </script>
  <script>
    const settings = {
      favorite_employee_ids: @json($favorite_employee_ids)
    };
    window.favoriteEmployeeIds = settings.favorite_employee_ids || [];

    (function normalizeFavoriteIds() {
      const fromBlade = (settings.favorite_employee_ids || settings.favorite_employees || []);
      window.favoriteEmployeeIds = fromBlade.map(String);

    })();
  </script>

  <script>
    window.addEventListener('DOMContentLoaded', function () {
      // Define the mobile/tablet breakpoint
      const maxWidthForMobile = 1024;

      // Check screen width and redirect if it's mobile or tablet
      if (window.innerWidth < maxWidthForMobile) {
        window.location.href = "{{ route('mobile.mobile_calendar.index') }}";

      }
    });
  </script>


  @php
    $userKey = auth()->user()->name;

    $truthyValues = ['on', '1', 1, true];

    $isAdmin = \DB::table('user_rolls')
      ->where('user_id', $userKey)
      ->where('item_id', 'Administrator')
      ->whereIn('is_read', $truthyValues)
      ->whereIn('is_update', $truthyValues)
      ->whereIn('is_delete', $truthyValues)
      ->whereIn('is_add', $truthyValues)

      ->exists();
  @endphp


  <script>
    // Normalize to true/false in JS
    window.calendarHasAdminAccess = Boolean(@json($hasAdminAccess ? 1 : 0));
    console.log('calendarHasAdminAccess =', window.calendarHasAdminAccess);
  </script>


  <script>
    window.APPOINTMENT_LEAD_STAGES = @json($appointmentLeadStagePayload);
    window.APPOINTMENT_LEAD_STAGE_CONTEXT_ROUTE = @json($appointmentLeadStageContextRoute);
  </script>

  <script>
    /*
      CalendarApp — lean rewrite (same routes/IDs/UI)
      - Single IIFE, minimal globals
      - Centralized fetch + CSRF
      - Deterministic Select2 init/destroy
      - One event mapper reused everywhere
      - Integrated "leave_request" events with modal + approve/reject/not_responsible
    */
    (() => {
      "use strict";

      // =========================
      // Config
      // =========================
      const AUTH_EMPLOYEE_ID = String("{{ auth()->user()->name }}"); // name holds employee_id
      const IS_ADMIN = Boolean(window.calendarHasAdminAccess);
      console.log('IS_ADMIN =', IS_ADMIN);
      const APPOINTMENT_LEAD_STAGES = Array.isArray(window.APPOINTMENT_LEAD_STAGES) ? window.APPOINTMENT_LEAD_STAGES : [];

      const CSRF = () => $('meta[name="csrf-token"]').attr('content') || '';

      const ROUTE = {
        searchSuggest: "{{ route('calendar.search.suggest') }}",
        getCalendar: "/get_personal_task_calendar",
        duplicateAppointment: "{{ route('appointment.duplicate') }}",
        changeAppointment: "{{ route('personal.task.change.appointment') }}",
        deleteAppointment: id => `${location.origin}/calendar/appointments/destroy/${id}`,
        restoreAppointment: id => `${location.origin}/calendar/appointments/restore/${id}`,
        forceDeleteAppointment: id => `${location.origin}/calendar/appointments/force-delete/${id}`,
        deleteTask: id => `${location.origin}/calendar/personal_task_delete/${id}`,
        appointmentDetails: id => `/customer/appointments/${id}`,
        taskDetails: id => `/personal_task_details/${id}`,
        getCustomerDetails: id => `/get_customer_details/${id}`,
        getEmployees: (q, f) => `/getEmployees?search=${encodeURIComponent(q)}&filter=${encodeURIComponent(f)}`,
        problemProfile: id => `/problem/profile/${id}`,
        contactList: "{{ route('get.contact.list') }}",
        productsByCustomer: "{{ route('get.products.by.customer') }}",
        inquiryDeptEmployees: "{{ route('calender.department.employees') }}", // kept for compatibility
        storeMainAppointment: "{{ route('main.appointments.store') }}",
        fetchMainAppointment: id => `/main-appointments/${id}/fetch`,
        updateMainAppointment: id => `/main-appointments/${id}`,
        calendarSettingsGet: "/calendar-settings",
        calendarSettingsSave: "/calendar-settings/save",
        leadStageContext: window.APPOINTMENT_LEAD_STAGE_CONTEXT_ROUTE || "/main-appointments/lead-stage-context",
      };

      // =========================
      // State
      // =========================
      const S = {
        fc: null,
        mini: null,
        currentSearch: "",
        publicHolidayDates: new Set(),                     // yyyy-mm-dd
        publicHolidayMap: new Map(),                       // yyyy-mm-dd -> Feiertag name
        favoriteEmployeeIds: (window.favoriteEmployeeIds || []).map(String),
        selectedEmployeeIds: new Set((window.favoriteEmployeeIds || []).map(String)),
        showDeletedAppointmentsOnly: false,
        lastEmployeeWarningSignature: "",
        empAbort: null,
        didAutoselectFavorites: false,
        productMap: {},                                    // uid -> {product_id, alternative_id, product_name, customer_id, city, lead_product_list_id, address}
        selectedCustomerData: null,                         // selected Kunde/Objekt from contact Select2
        preselectedObjectId: "",                            // object id when user picked an Objekt directly
        lastCalendarRangeKey: "",
        calendarRangeReloadTimer: null,
        lastCalendarMobileMode: null,
        lastDesktopCalendarView: "timeGridWeek",
        urlaubGanttMode: false,
        urlaubGanttYear: (new Date()).getFullYear(),
        urlaubGanttMonth: 0,
        urlaubGanttZoom: 1,
        urlaubGanttRows: [],
        urlaubGanttFilters: {
          holiday: true,
          sick: true,
          recurring_leave: true,
          leave_request: true,
        },
      };

      // limited window surface
      window.authEmployeeId = AUTH_EMPLOYEE_ID;
      window.selectedEmployeeIds = S.selectedEmployeeIds;

      // =========================
      // DOM
      // =========================
      const D = {
        cal: document.querySelector(".calendar"),
        mini: document.getElementById("mini_calendar"),
        urlaubGanttPanel: document.getElementById("urlaubGanttPanel"),
        urlaubGanttTable: document.getElementById("urlaubGanttTable"),
        urlaubGanttScroll: document.getElementById("urlaubGanttScroll"),
        newTaskCard: document.querySelector(".new_task"),
      };

      // =========================
      // Utils
      // =========================
      const U = {
        q: (s, ctx = document) => ctx.querySelector(s),
        qa: (s, ctx = document) => Array.from(ctx.querySelectorAll(s)),
        pad2: n => String(n).padStart(2, "0"),
        isoDate(d) { return `${d.getFullYear()}-${this.pad2(d.getMonth() + 1)}-${this.pad2(d.getDate())}`; },
        isoDT(d) { return `${this.isoDate(d)}T${this.pad2(d.getHours())}:${this.pad2(d.getMinutes())}:00`; },
        shortHM(t) { return (!t || t === "null" || t === "undefined") ? "N/A" : t.split(":").slice(0, 2).join(":"); },
        hexRGBA(hex = "#006400", a = 1) {
          hex = hex.replace(/^#/, ""); if (hex.length === 3) hex = hex.split("").map(c => c + c).join("");
          const r = parseInt(hex.slice(0, 2), 16), g = parseInt(hex.slice(2, 4), 16), b = parseInt(hex.slice(4, 6), 16);
          return `rgba(${r}, ${g}, ${b}, ${a})`;
        },
        trunc(s, n) { return s && s.length > n ? s.slice(0, n) + "…" : (s || ""); },
        weekStart(dateStr, firstDay = 1) {
          const d = new Date(dateStr), s = new Date(d), dw = d.getDay(); s.setDate(d.getDate() - ((dw + 7 - firstDay) % 7)); return U.isoDate(s);
        },
        isMobile: () => window.innerWidth <= 768,
        makeTicketUrl(problemId) {
          if (!problemId) return null;
          return ROUTE.problemProfile(problemId);
        },
        selectedFromDOM() {
          const checks = U.qa(".employee_check:checked").map(cb => String(cb.dataset.id));
          const sel = ($("#employee").val() || []).map(String);
          return new Set([...checks, ...sel, ...Array.from(S.selectedEmployeeIds)]);
        },
        ensureOption($sel, id, text) {
          if ($sel.find(`option[value="${id}"]`).length) return;
          const opt = new Option(text || `#${id}`, id, false, false);
          $sel.append(opt);
        },
        extractHolidayDates(events) {
          S.publicHolidayDates = new Set();
          S.publicHolidayMap = new Map();

          (events || []).forEach(ev => {
            const type = ev.extendedProps?.type;

            if (type !== "public_holiday") return;

            const date = String(ev.start || "").split("T")[0];
            if (!date) return;

            const title =
              ev.title ||
              ev.extendedProps?.absence_title ||
              ev.extendedProps?.description ||
              "Feiertag";

            S.publicHolidayDates.add(date);
            S.publicHolidayMap.set(date, title);
          });
        },
        hasHolidayBetween(startStr, endStr) {
          if (!startStr || !endStr) return null;
          const s = new Date(startStr), e = new Date(endStr);
          for (let d = new Date(s); d <= e; d.setDate(d.getDate() + 1)) {
            const iso = U.isoDate(d); if (S.publicHolidayDates.has(iso)) return iso;
          }
          return null;
        },
        // fetch helpers
        async getJSON(url, data) {
          const qs = data ? (url.includes('?') ? '&' : '?') + new URLSearchParams(data) : '';
          const r = await fetch(url + qs, { headers: { Accept: "application/json" } });
          if (!r.ok) throw new Error(`HTTP ${r.status}`);
          return r.json();
        },
        async postJSON(url, body) {
          const r = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json", "X-CSRF-TOKEN": CSRF() },
            body: JSON.stringify(body || {}),
          });
          const text = await r.text();
          try { return { ok: r.ok, status: r.status, json: JSON.parse(text) }; }
          catch { return { ok: r.ok, status: r.status, json: { raw: text } }; }
        },
        async send(method, url, formData) {
          const r = await fetch(url, { method, headers: { "X-CSRF-TOKEN": CSRF() }, body: formData });
          if (!r.ok) throw new Error(`HTTP ${r.status}`);
          return r.json();
        }
      };

      // =========================
      // Select2 lifecycle
      // =========================
      function initSelect2Singleton($el, options) {
        if (!$el?.length) return;
        if ($el.data('select2')) { $el.off(); $el.select2('destroy'); }
        $el.select2(Object.assign({ width: '100%', allowClear: true, placeholder: 'Bitte wählen' }, options || {}));
      }


      // =========================
      // CRM Stage/Sub Stage helpers
      // =========================
      function maStageById(stageId) {
        if (!stageId) return null;
        return (APPOINTMENT_LEAD_STAGES || []).find(item => String(item.id) === String(stageId)) || null;
      }

      function maNormalizeSubStages(stage) {
        if (!stage) return [];
        return Array.isArray(stage.sub_stages)
          ? stage.sub_stages
          : (Array.isArray(stage.activeSubStages)
            ? stage.activeSubStages
            : (Array.isArray(stage.active_sub_stages) ? stage.active_sub_stages : []));
      }

      function maSubStageById(stage, subStageId) {
        if (!stage || !subStageId) return null;
        return maNormalizeSubStages(stage).find(item => String(item.id) === String(subStageId)) || null;
      }

      function maStageColor(value, fallback = '#74b2d4') {
        const color = String(value || '').trim();
        return /^#[0-9a-fA-F]{6}$/.test(color) ? color : fallback;
      }


      function maOnlyIntegerId(value) {
        if (value === null || value === undefined) return '';
        if (Array.isArray(value)) value = value[0];
        const cleaned = String(value).trim();
        return /^\d+$/.test(cleaned) ? cleaned : '';
      }

      function maSanitizeAppointmentStageInputs() {
        const $leadProduct = $('#appointment_lead_product_list_id');
        const $stage = $('#appointment_lead_stage_id');
        const $subStage = $('#appointment_lead_stage_sub_stage_id');

        if ($leadProduct.length) $leadProduct.val(maOnlyIntegerId($leadProduct.val()));
        if ($stage.length) $stage.val(maOnlyIntegerId($stage.val()));
        if ($subStage.length) $subStage.val(maOnlyIntegerId($subStage.val()));
      }

      window.maOnlyIntegerId = maOnlyIntegerId;
      window.maSanitizeAppointmentStageInputs = maSanitizeAppointmentStageInputs;

      function maStageLabelFromProps(props = {}) {
        const stageName = props.lead_stage_name || props.lead_stage_context?.lead_stage_name || '';
        const subStageName = props.lead_stage_sub_stage_name || props.lead_stage_context?.lead_stage_sub_stage_name || '';
        return [stageName, subStageName].filter(Boolean).join(' / ');
      }

      function maRenderStagePills(props = {}, dark = false) {
        const stageName = props.lead_stage_name || props.lead_stage_context?.lead_stage_name || '';
        const subStageName = props.lead_stage_sub_stage_name || props.lead_stage_context?.lead_stage_sub_stage_name || '';
        const stageColor = maStageColor(props.lead_stage_color || props.lead_stage_context?.lead_stage_color, '#74b2d4');
        const subStageColor = maStageColor(props.lead_stage_sub_stage_color || props.lead_stage_context?.lead_stage_sub_stage_color, '#93c21c');

        if (!stageName && !subStageName) {
          return dark
            ? '<span class="ma-stage-empty">Keine CRM Stage gespeichert.</span>'
            : '<span class="ma-stage-empty">Noch keine Stage gewählt.</span>';
        }

        return `
                          ${stageName ? `<span class="ma-stage-pill" style="border-left-color:${stageColor};border-color:${stageColor};">${escapeHtml(stageName)}</span>` : ''}
                          ${subStageName ? `<span class="ma-stage-pill" style="border-left-color:${subStageColor};border-color:${subStageColor};">${escapeHtml(subStageName)}</span>` : ''}
                        `;
      }

      function maRenderFormStagePreview() {
        const stageId = $('#appointment_lead_stage_id').val();
        const subStageId = $('#appointment_lead_stage_sub_stage_id').val();
        const stage = maStageById(stageId);
        const sub = maSubStageById(stage, subStageId);

        const props = {
          lead_stage_name: stage?.name || '',
          lead_stage_color: stage?.color || '#74b2d4',
          lead_stage_sub_stage_name: sub?.name || '',
          lead_stage_sub_stage_color: sub?.color || '#93c21c'
        };

        $('#appointmentStagePreview').html(maRenderStagePills(props));
      }

      async function maFetchSubStagesFromServer(stageId, selectedSubStageId = '') {
        if (!stageId || !ROUTE.leadStageContext) return false;

        try {
          const response = await $.ajax({
            url: ROUTE.leadStageContext,
            type: 'GET',
            dataType: 'json',
            data: { lead_stage_id: maOnlyIntegerId(stageId), lead_product_list_id: maOnlyIntegerId($('#appointment_lead_product_list_id').val()) }
          });

          const context = response?.context || response?.lead_stage_context || response || {};
          const subStages = context.sub_stages || context.subStages || response?.sub_stages || response?.subStages || [];

          if (!Array.isArray(subStages) || !subStages.length) return false;

          const $sub = $('#appointment_lead_stage_sub_stage_id');

          if ($sub.hasClass('select2-hidden-accessible')) {
            $sub.select2('destroy');
          }

          $sub.html('<option value="">Keine Sub Stage</option>');

          subStages.forEach(function (subStage) {
            const option = new Option(subStage.name || ('Sub Stage #' + subStage.id), subStage.id, false, false);
            option.dataset.color = subStage.color || '#93c21c';
            $sub.append(option);
          });

          $sub.prop('disabled', false);

          if (selectedSubStageId) {
            $sub.val(String(selectedSubStageId));
          }

          $sub.select2({
            width: '100%',
            placeholder: 'Sub Stage wählen',
            allowClear: true,
            dropdownParent: $(document.body),
          });

          maRenderFormStagePreview();
          return true;
        } catch (e) {
          console.warn('CRM Sub Stage AJAX fallback failed:', e);
          return false;
        }
      }

      async function maRebuildAppointmentSubStages(selectedSubStageId = '') {
        const stageId = $('#appointment_lead_stage_id').val() || '';
        const stage = maStageById(stageId);
        const subStages = maNormalizeSubStages(stage);
        const $sub = $('#appointment_lead_stage_sub_stage_id');

        if ($sub.hasClass('select2-hidden-accessible')) {
          $sub.select2('destroy');
        }

        $sub.html('<option value="">Keine Sub Stage</option>');

        subStages.forEach(function (subStage) {
          const option = new Option(subStage.name || ('Sub Stage #' + subStage.id), subStage.id, false, false);
          option.dataset.color = subStage.color || '#93c21c';
          $sub.append(option);
        });

        if (selectedSubStageId && subStages.some(item => String(item.id) === String(selectedSubStageId))) {
          $sub.val(String(selectedSubStageId));
        } else {
          $sub.val('');
        }

        $sub.prop('disabled', !stageId || !subStages.length);

        $sub.select2({
          width: '100%',
          placeholder: !stageId ? 'Zuerst Stage wählen' : 'Sub Stage wählen',
          allowClear: true,
          dropdownParent: $(document.body),
        });

        if (stageId && !subStages.length) {
          await maFetchSubStagesFromServer(stageId, selectedSubStageId);
        }

        maRenderFormStagePreview();
      }

      function maInitAppointmentStageSelect2() {
        const $stage = $('#appointment_lead_stage_id');
        const $sub = $('#appointment_lead_stage_sub_stage_id');

        if (!$stage.length || !$sub.length) return;

        if (typeof $.fn.select2 !== 'function') {
          console.warn('Select2 is not loaded. CRM Stage fields will use native selects.');
          maRebuildAppointmentSubStages($sub.val() || '');
          return;
        }

        if ($stage.hasClass('select2-hidden-accessible')) {
          $stage.select2('destroy');
        }

        $stage.select2({
          width: '100%',
          placeholder: 'CRM Stage wählen',
          allowClear: true,
          dropdownParent: $(document.body),
        });

        maRebuildAppointmentSubStages($sub.val() || '');

        $(document)
          .off('change.appointmentLeadStage', '#appointment_lead_stage_id')
          .on('change.appointmentLeadStage', '#appointment_lead_stage_id', function () {
            maRebuildAppointmentSubStages('');
          });

        $(document)
          .off('change.appointmentLeadSubStage', '#appointment_lead_stage_sub_stage_id')
          .on('change.appointmentLeadSubStage', '#appointment_lead_stage_sub_stage_id', function () {
            maRenderFormStagePreview();
          });
      }

      // Backward-compatible alias used by boot().
      // Without this alias, boot throws: initAppointmentStageSelect2 is not defined.
      function initAppointmentStageSelect2() {
        return maInitAppointmentStageSelect2();
      }

      window.initAppointmentStageSelect2 = initAppointmentStageSelect2;

      function maResetAppointmentStageFields() {
        $('#appointment_lead_product_list_id').val('');
        $('#appointment_lead_stage_id').val('').trigger('change.select2');
        maRebuildAppointmentSubStages('');
        maRenderFormStagePreview();
      }

      function maSetAppointmentStageFields(data = {}) {
        const props = data.extendedProps || data || {};
        const stageId = props.lead_stage_id || props.lead_stage_context?.lead_stage_id || '';
        const subStageId = props.lead_stage_sub_stage_id || props.lead_stage_context?.lead_stage_sub_stage_id || '';
        const leadProductListId = maOnlyIntegerId(props.lead_product_list_id || props.lead_stage_context?.lead_product_list_id || '');

        $('#appointment_lead_product_list_id').val(leadProductListId);

        $('#appointment_lead_stage_id')
          .val(maOnlyIntegerId(stageId))
          .trigger('change.select2');

        maRebuildAppointmentSubStages(maOnlyIntegerId(subStageId));
      }

      window.maSetAppointmentStageFields = maSetAppointmentStageFields;
      window.maResetAppointmentStageFields = maResetAppointmentStageFields;

      // =========================
      // Tickets module
      // =========================
      const TICKETS = {
        svg: `
                                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true">
                                              <path fill="currentColor" d="M3 7a2 2 0 0 1 2-2h5l1 2h3a2 2 0 1 0 0 4h-3l-1 2H5a2 2 0 0 1-2-2V7zM14 5h5a2 2 0 0 1 2 2v2a3 3 0 0 0 0 6v2a2 2 0 0 1-2 2h-5l-2-4 2-4z"/>
                                          </svg>
                                                                    `
      };


      // =========================
      // Event mapping
      // =========================
      /**
      * Fully rewritten mapper to handle cancellations and modified recurring leaves.
      */
      function mapServerItemToEvents(item) {
        const out = [];

        // Standardize dates
        const startDT = new Date(`${item.start_date}T${item.start_time}`);
        const endDT = new Date(`${item.end_date || item.start_date}T${item.end_time}`);

        // Loop through days (for multi-day spans)
        for (let d = new Date(startDT); d <= endDT; d.setDate(d.getDate() + 1)) {
          const dateStr = U.isoDate(d);

          // Determine the specific times for this day of the event
          const sTime = dateStr === item.start_date ? item.start_time : "07:30:00";
          const eTime = (dateStr === (item.end_date || item.start_date)) ? item.end_time : "16:00:00";

          const endObj = new Date(`${dateStr}T${eTime}`);

          // FullCalendar fix for exact hour/half-hour markers
          if (endObj.getSeconds() === 0 && endObj.getMilliseconds() === 0 && (endObj.getMinutes() === 0 || endObj.getMinutes() === 30)) {
            endObj.setMinutes(endObj.getMinutes() + 1);
          }

          // --- CANCELLATION / DELETED LOGIC ---
          // Check if the backend flagged this specific occurrence as cancelled/deleted
          const isCancelled = item.is_cancelled === true || item.status === 'cancelled';
          const isDeleted = item.is_deleted === true || item.deleted_at || item.status === 'deleted';

          // If it's cancelled, we can either:
          // 1. Return empty (hides it from calendar)
          // 2. Push it with a strikethrough title (shows it was removed)
          // Here we push it so the user sees the "Cancelled" status you wanted.

          let displayTitle = item.title || "-";
          if (item.type === "recurring_leave") {
            displayTitle = item.absence_title || item.recurring_title || item.real_title || item.original_title || item.title || "Wiederkehrender Termin";
          }
          if (isCancelled && !displayTitle.includes('(ABGESAGT)')) {
            displayTitle = `🚫 ${displayTitle} (ABGESAGT)`;
          }
          if (isDeleted && !displayTitle.includes('(GELÖSCHT)')) {
            displayTitle = `🗑️ ${displayTitle} (GELÖSCHT)`;
          }

          out.push({
            id: `${item.id}-${dateStr}-${sTime}`,
            title: displayTitle,
            start: `${dateStr}T${sTime}`,
            end: U.isoDT(endObj),
            // If cancelled, force a soft grey or red color override
            color: isDeleted ? "#f3f4f6" : (isCancelled ? "#ffcccc" : (item.taskColor || "#cccccc")),
            textColor: isDeleted ? "#991b1b" : (isCancelled ? "#cc0000" : null),
            classNames: [
              ...(item.type === 'recurring_leave' && (item.recurring_event_kind === 'home_office' || item.event_kind === 'home_office') ? ['recurring-home-office'] : []),
              ...(isDeleted ? ['calendar-deleted-appointment'] : []),
            ],
            allDay: ["public_holiday", "holiday", "sick", "recurring_leave", "leave_request"].includes(item.type),

            extendedProps: {
              is_cancelled: isCancelled,
              is_deleted: isDeleted,
              deleted_at: item.deleted_at || null,
              status: item.status || (isDeleted ? 'deleted' : (isCancelled ? 'cancelled' : 'normal')),

              created_by: item.created_by || null,
              creator: item.creator || null,
              employees: item.employees || [],
              priority: item.priority || "-",
              public: item.public_view || "-",
              report: item.is_report || "-",
              type: item.type || "-",
              absence_title: item.absence_title || item.recurring_title || item.real_title || item.original_title || item.title || null,
              recurring_title: item.recurring_title || item.absence_title || item.real_title || item.original_title || null,
              original_title: item.original_title || null,

              execution_type: item.execution_type || "-",

              start_time: sTime,
              end_time: eTime,
              city: item.city || "-",
              street: item.street || "",
              postcode: item.postcode || "",
              phone: item.phone || "-",
              email: item.email || "-",
              full_address: buildAppointmentFullAddress(item.street || "", item.postcode || "", item.city || "", item.full_address || "") || "-",
              appointment_type: item.appointment_type || "-",
              description: item.description || "-",

              customer_id: item.customer_id ?? null,
              contact_id: item.contact_id ?? null,
              next_step: item.next_step || null,
              responsible_report: item.responsible_report || [],
              has_ticket: !!item.ticket_problem_id,
              ticket_problem_id: item.ticket_problem_id || null,
              emp_personal_id: item.emp_personal_id || null,

              leave_type: item.leave_type ?? null,
              leave_reason: item.leave_reason ?? item.reason ?? null,
              leave_status: item.leave_status ?? item.status ?? null,
              reason: item.reason ?? null,

              employee_id: item.employee_id || item.employeeId || item.employee?.id || null,
              employee_name: item.employee_name || item.employee?.name || item.name || null,
              employee_lastname: item.employee_lastname || item.employee?.lastname || item.lastname || null,
              name: item.name ?? item.employee_name ?? null,
              lastname: item.lastname ?? item.employee_lastname ?? null,
              gender: item.gender ?? null,
              image: item.image ?? null,

              recurring_leave_id: item.recurring_leave_id ?? item.leave_id ?? null,
              recurring_rule_type: item.recurring_rule_type ?? item.rule_type ?? null,
              recurring_type: item.recurring_type ?? item.recurring_rule_type ?? item.type_rule ?? null,
              recurring_frequency: item.recurring_frequency ?? item.frequency ?? null,
              recurring_interval: item.recurring_interval ?? item.interval ?? null,
              recurring_weekdays: item.recurring_weekdays ?? item.weekdays ?? item.day_of_week ?? null,
              recurring_day_of_month: item.recurring_day_of_month ?? item.day_of_month ?? null,
              recurring_start_date: item.recurring_start_date ?? item.series_start_date ?? null,
              recurring_end_date: item.recurring_end_date ?? item.series_end_date ?? null,
              recurring_original_date: item.recurring_original_date ?? item.original_date ?? null,
              recurring_final_date: item.recurring_final_date ?? item.final_date ?? null,
              recurring_duration_days: item.recurring_duration_days ?? item.duration_days ?? null,
              recurring_event_kind: item.recurring_event_kind ?? item.event_kind ?? item.kind ?? 'absence',
              can_create_appointment: !!(item.can_create_appointment || item.recurring_event_kind === 'home_office' || item.event_kind === 'home_office'),
              has_override: !!(item.has_override || item.override_id),
              override_id: item.override_id ?? null,
              override_note: item.override_note ?? null,
              is_cancelled: isCancelled,
              is_deleted: isDeleted,
              deleted_at: item.deleted_at || null,

              products: item.product_json ?? item.products ?? null,

              lead_product_list_id: item.lead_product_list_id ?? null,
              lead_stage_id: item.lead_stage_id ?? item.lead_stage_context?.lead_stage_id ?? null,
              lead_stage_name: item.lead_stage_name ?? item.lead_stage_context?.lead_stage_name ?? null,
              lead_stage_color: item.lead_stage_color ?? item.lead_stage_context?.lead_stage_color ?? '#74b2d4',
              lead_stage_sub_stage_id: item.lead_stage_sub_stage_id ?? item.lead_stage_context?.lead_stage_sub_stage_id ?? null,
              lead_stage_sub_stage_name: item.lead_stage_sub_stage_name ?? item.lead_stage_context?.lead_stage_sub_stage_name ?? null,
              lead_stage_sub_stage_color: item.lead_stage_sub_stage_color ?? item.lead_stage_context?.lead_stage_sub_stage_color ?? '#93c21c',
              lead_stage_context: item.lead_stage_context ?? null,
            }
          });
        }
        return out;
      }
      // =========================
      // Calendar UI
      // =========================
      async function loadCalendarTasks(cb) {
        const employeeData = getSelectedEmployeeData();

        const fallbackStart = new Date();
        fallbackStart.setHours(0, 0, 0, 0);

        // FullCalendar is not ready on the first call. Use the current week from Monday,
        // otherwise appointments from Monday/Tuesday can be missed when today is later in the week.
        const currentDay = fallbackStart.getDay(); // Sunday = 0
        const diffToMonday = (currentDay + 6) % 7;
        fallbackStart.setDate(fallbackStart.getDate() - diffToMonday);

        const fallbackEnd = new Date(fallbackStart);
        fallbackEnd.setDate(fallbackEnd.getDate() + 7);

        const calendarRange = S.fc?.view
          ? {
            start: U.isoDate(S.fc.view.activeStart),
            end: U.isoDate(S.fc.view.activeEnd)
          }
          : {
            start: U.isoDate(fallbackStart),
            end: U.isoDate(fallbackEnd)
          };

        try {
          const res = await U.getJSON(ROUTE.getCalendar, {
            employee_data: JSON.stringify(employeeData),
            search: S.currentSearch || "",
            include_all_absences: S.showDeletedAppointmentsOnly ? 0 : 1,
            only_deleted_appointments: S.showDeletedAppointmentsOnly ? 1 : 0,
            start: calendarRange.start,
            end: calendarRange.end
          });

          const rows = Array.isArray(res?.data) ? res.data : [];
          const events = rows.flatMap(mapServerItemToEvents);

          updateTrashModeUi();
          U.extractHolidayDates(events);
          initializeCalendar(events);

          if (D.mini) {
            initializeMiniCalendar(events);
          }

          setTimeout(() => {
            refreshCalendarBlockedDayStyles();
            refreshSidebarEmployeeTodayBadges();
            showEmployeeSelectionWarnings();
          }, 80);

          if (typeof cb === "function") cb();
        } catch (err) {
          console.error("loadCalendarTasks:", err);
        }
      }
      function updateTrashModeUi() {
        const btn = document.querySelector(".fc-trashMode-button");
        const banner = document.getElementById("calendarTrashBanner");

        if (btn) {
          btn.innerHTML = S.showDeletedAppointmentsOnly
            ? '<i class="feather icon-calendar"></i> Aktive Termine'
            : '<i class="feather icon-trash-2"></i> Papierkorb';
          btn.classList.toggle("is-active-trash", S.showDeletedAppointmentsOnly);
        }

        if (banner) {
          banner.classList.toggle("is-visible", S.showDeletedAppointmentsOnly);
        }

        if (window.feather) window.feather.replace();
      }

      function mountCalendarSearch() {
        if (document.getElementById("customCalendarSearchWrap")) {
          const stray = document.querySelector(".fc-searchBox-button");
          if (stray) stray.remove();
          return;
        }

        const btn = document.querySelector(".fc-searchBox-button");
        if (!btn) return;

        const wrap = document.createElement("div");
        wrap.id = "customCalendarSearchWrap";
        wrap.style.display = "inline-block";
        wrap.style.minWidth = "320px";
        wrap.style.position = "relative";

        wrap.innerHTML = `
                                                                      <div style="position: relative; width: 100%;">
                                                                          <input type="text" id="customCalendarSearch" class="form-control" placeholder="Suchen… (Termin, Aufgabe, Mitarbeiter, Ort)" autocomplete="off" style="width: 100%; border-radius: 4px;">
                                                                          <i class="feather icon-search" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: var(--brand);"></i>
                                                                          <div id="customSearchDropdown" style="
                                                                              display: none; 
                                                                              position: absolute; 
                                                                              top: calc(100% + 5px); 
                                                                              left: 0; 
                                                                              width: 100%; 
                                                                              background: #fff; 
                                                                              border: 1px solid #ddd; 
                                                                              border-radius: 6px; 
                                                                              box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
                                                                              z-index: 1050; 
                                                                              max-height: 350px; 
                                                                              overflow-y: auto;">
                                                                          </div>
                                                                      </div>
                                                                  `;

        btn.replaceWith(wrap);

        const input = document.getElementById("customCalendarSearch");
        const dropdown = document.getElementById("customSearchDropdown");
        let searchTimeout;

        input.addEventListener("input", function () {
          clearTimeout(searchTimeout);
          const query = this.value.trim();

          if (query.length === 0) {
            dropdown.style.display = "none";
            dropdown.innerHTML = "";
            clearSearchState();
            return;
          }

          searchTimeout = setTimeout(async () => {
            const params = new URLSearchParams({ q: query });
            Array.from(S.selectedEmployeeIds).forEach(id => params.append("employee_ids[]", id));

            try {
              const res = await fetch(`${ROUTE.searchSuggest}?${params.toString()}`);
              const data = await res.json();
              renderDropdown(data.results || []);
            } catch (err) {
              console.error("Calendar Search Error:", err);
            }
          }, 300);
        });

        document.addEventListener("click", function (e) {
          if (!wrap.contains(e.target)) {
            dropdown.style.display = "none";
          }
        });

        function clearSearchState() {
          S.currentSearch = "";
          const url = new URL(window.location);
          url.searchParams.delete("task_id");
          window.history.pushState({}, "", url);
          reloadCalendarWithSearch();
        }

        function renderDropdown(results) {
          dropdown.innerHTML = "";

          if (results.length === 0) {
            dropdown.innerHTML = `<div style="padding: 10px 15px; color: #888; font-size: 12px; text-align: center;">Keine Ergebnisse gefunden</div>`;
            dropdown.style.display = "block";
            return;
          }

          results.forEach(item => {
            const div = document.createElement("div");
            div.className = "custom-search-item";
            div.style.padding = "10px 12px";
            div.style.cursor = "pointer";
            div.style.borderBottom = "1px solid #f1f1f1";
            div.style.transition = "background-color 0.2s";

            // Visual & Deleted State Logic
            const icons = { appointment: "icon-calendar", task: "icon-check-circle", employee: "icon-user", city: "icon-map-pin" };
            const icon = icons[item.type] || "icon-search";
            const pill = { appointment: "Termin", task: "Aufgabe", employee: "Mitarbeiter", city: "Ort" }[item.type] || item.type;
            const dateSpan = item.date ? `<span style="font-size: 11px; color: #888; margin-top: 3px; display: block;"><i class="feather icon-clock"></i> ${item.date}</span>` : "";

            const isDeleted = item.is_deleted;
            const iconColor = isDeleted ? "#ccc" : "var(--brand)";
            const titleStyle = isDeleted ? "text-decoration: line-through; color: #999;" : "color: #333;";
            const badgeHtml = isDeleted ? `<span class="badge" style="background-color: #ff4d4f; color: white; font-size: 10px; margin-left: 8px;">Gelöscht</span>` : "";

            div.innerHTML = `
                                                                          <div style="display: flex; align-items: center; justify-content: space-between;">
                                                                              <div style="display: flex; align-items: flex-start; gap: 12px;">
                                                                                  <i class="feather ${icon}" style="color: ${iconColor}; font-size: 18px; margin-top: 2px;"></i>
                                                                                  <div style="display: flex; flex-direction: column;">
                                                                                      <div style="display: flex; align-items: center;">
                                                                                          <strong style="font-size: 13px; ${titleStyle} line-height: 1.2;">${item.label || item.text}</strong>
                                                                                          ${badgeHtml}
                                                                                      </div>
                                                                                      ${dateSpan}
                                                                                  </div>
                                                                              </div>
                                                                              <span class="badge badge-light" style="font-size: 10px; align-self: center;">${pill}</span>
                                                                          </div>
                                                                      `;

            div.addEventListener("mouseenter", () => div.style.backgroundColor = "#f8f9fa");
            div.addEventListener("mouseleave", () => div.style.backgroundColor = "transparent");

            div.addEventListener("click", () => {
              dropdown.style.display = "none";
              input.value = item.label || item.text;

              // 1. Activate involved employees in the sidebar
              let needsCalendarReload = false;

              if (item.employee_ids && item.employee_ids.length > 0) {
                item.employee_ids.forEach(empId => {
                  const idStr = String(empId);

                  if (!S.selectedEmployeeIds.has(idStr)) {
                    S.selectedEmployeeIds.add(idStr);
                    needsCalendarReload = true;

                    // Check the hidden input checkbox
                    const cb = document.querySelector(`.employee_check[data-id="${idStr}"]`);
                    if (cb) cb.checked = true;

                    // Highlight the avatar ring
                    const img = document.getElementById(`employeeCheck${idStr}`);
                    if (img) {
                      img.classList.add("emp_active");
                      img.style.borderColor = "rgb(0, 159, 227)";
                    }

                    U.ensureOption($("#employee"), idStr, item.label);
                  }
                });

                // Sync the backend arrays silently
                if (needsCalendarReload) {
                  $("#employee").val(Array.from(S.selectedEmployeeIds)).trigger("change.select2");
                  const $m = $("#mobileEmployeeSelect");
                  if ($m.length) {
                    $m.val(Array.from(S.selectedEmployeeIds)).trigger("change.select2");
                  }
                }
              }

              // 2. Fix the Date format for FullCalendar (DD.MM.YYYY -> YYYY-MM-DD)
              let isoDate = null;
              if (item.date) {
                if (item.date.includes('.')) {
                  let parts = item.date.split('.');
                  isoDate = `${parts[2]}-${parts[1]}-${parts[0]}`; // YYYY-MM-DD
                } else {
                  isoDate = item.date;
                }
              }

              // 3. URL & Search Logic
              if (item.type === "appointment" || item.type === "task") {
                const url = new URL(window.location);
                url.searchParams.set("task_id", item.id);
                window.history.pushState({}, "", url);
                S.currentSearch = "";
              } else {
                S.currentSearch = item.label || item.text || "";
              }

              // 4. Reload Calendar and jump safely to the date!
              reloadCalendarWithSearch(async () => {
                if (isoDate) {
                  S.fc.gotoDate(isoDate);
                }
              });
            });

            dropdown.appendChild(div);
          });

          dropdown.style.display = "block";
        }
      }
      function getEventDateOnly(ev) {
        if (!ev || !ev.start) return "";
        return U.isoDate(ev.start);
      }

      function getEmployeeIdsFromEvent(ev) {
        const xp = ev.extendedProps || {};
        const ids = new Set();

        if (xp.employee_id) ids.add(String(xp.employee_id));
        if (xp.employeeId) ids.add(String(xp.employeeId));
        if (xp.created_by) ids.add(String(xp.created_by));

        if (Array.isArray(xp.employees)) {
          xp.employees.forEach(emp => {
            if (emp?.id) ids.add(String(emp.id));
            if (emp?.employee_id) ids.add(String(emp.employee_id));
            if (emp?.employeeId) ids.add(String(emp.employeeId));
          });
        }

        return Array.from(ids);
      }

      function getBlockingEventLabel(ev) {
        const xp = ev.extendedProps || {};
        const type = String(xp.type || "");

        if (type === "public_holiday") {
          return "Feiertag";
        }

        if (type === "holiday") {
          return "Urlaub";
        }

        if (type === "sick") {
          return "Krank";
        }

        if (type === "recurring_leave") {
          return "Serie";
        }

        if (type === "leave_request") {
          return "Urlaubsantrag";
        }

        return "Gesperrt";
      }

      function getEmployeeNameFromEvent(ev) {
        const xp = ev.extendedProps || {};
        return getEventEmployeeNames(xp) || "Mitarbeiter";
      }

      function isRecurringHomeOfficeEvent(evOrXp) {
        const xp = evOrXp?.extendedProps || evOrXp || {};
        return String(xp.type || '') === 'recurring_leave'
          && String(xp.recurring_event_kind || xp.event_kind || '').toLowerCase() === 'home_office'
          && xp.is_cancelled !== true
          && xp.status !== 'cancelled';
      }

      function isBlockingAbsenceType(type) {
        return ["holiday", "sick", "recurring_leave"].includes(String(type || ""));
      }

      function isPublicHolidayType(type) {
        return String(type || "") === "public_holiday";
      }

      function getCalendarBlockersForDate(dateStr, employeeIds = null) {
        const selectedIds = employeeIds
          ? employeeIds.map(String)
          : Array.from(S.selectedEmployeeIds || []).map(String);

        const selectedSet = new Set(selectedIds);
        const events = S.fc ? S.fc.getEvents() : [];

        return events
          .filter(ev => {
            const xp = ev.extendedProps || {};
            const type = xp.type || "";
            const evDate = getEventDateOnly(ev);

            if (evDate !== dateStr) return false;
            if (xp.is_cancelled === true || xp.status === "cancelled") return false;
            if (isRecurringHomeOfficeEvent(ev)) return false;

            if (isPublicHolidayType(type)) return true;

            if (!isBlockingAbsenceType(type)) return false;

            const evEmpIds = getEmployeeIdsFromEvent(ev);

            if (selectedSet.size === 0) return false;
            if (evEmpIds.length === 0) return true;

            return evEmpIds.some(id => selectedSet.has(String(id)));
          })
          .map(ev => {
            const xp = ev.extendedProps || {};
            return {
              event: ev,
              type: xp.type || "",
              date: dateStr,
              label: getBlockingEventLabel(ev),
              employee: getEmployeeNameFromEvent(ev),
              employeeIds: getEmployeeIdsFromEvent(ev)
            };
          });
      }

      function getPublicHolidayForDate(dateStr) {
        return getCalendarBlockersForDate(dateStr, [])
          .find(item => item.type === "public_holiday") || null;
      }

      function getSingleEmployeeAbsenceForDate(dateStr) {
        const ids = Array.from(S.selectedEmployeeIds || []).map(String);
        if (ids.length !== 1) return null;

        return getCalendarBlockersForDate(dateStr, ids)
          .find(item => isBlockingAbsenceType(item.type)) || null;
      }

      function showCalendarBlockedAlert(blocker) {
        if (!blocker) return;

        const isPublicHoliday = blocker.type === "public_holiday";

        Swal.fire({
          icon: "warning",
          title: isPublicHoliday ? "Feiertag" : "Mitarbeiter nicht verfügbar",
          html: `
                                <div style="text-align:left">
                                  <div style="font-weight:900;font-size:15px;margin-bottom:6px;">
                                    ${escapeHtml(blocker.label)}
                                  </div>
                                  ${!isPublicHoliday
              ? `<div><strong>Mitarbeiter:</strong> ${escapeHtml(blocker.employee)}</div>`
              : ""
            }
                                  <div><strong>Datum:</strong> ${escapeHtml(blocker.date.split("-").reverse().join("."))}</div>
                                  <div style="margin-top:8px;color:#6b7280;">
                                    An diesem Tag kann kein Termin erstellt werden.
                                  </div>
                                </div>
                              `,
          confirmButtonText: "OK",
          confirmButtonColor: "#d92127"
        });
      }

      function calendarDateToStartOfDay(value) {
        if (!value) return null;

        const date = value instanceof Date
          ? new Date(value)
          : new Date(String(value).includes("T") ? String(value) : `${String(value)}T00:00:00`);

        if (Number.isNaN(date.getTime())) return null;

        date.setHours(0, 0, 0, 0);
        return date;
      }

      function formatDateShortDE(value) {
        const date = calendarDateToStartOfDay(value);
        if (!date) return "";

        return date.toLocaleDateString("de-DE", {
          day: "2-digit",
          month: "2-digit",
          year: "numeric"
        });
      }

      function getEventDateRangeLabel(ev) {
        const startStr = getEventDateOnly(ev);
        const startDate = calendarDateToStartOfDay(startStr);

        if (!startDate) return "";

        let endDate = null;

        if (ev.end) {
          endDate = new Date(ev.end);
          endDate.setHours(0, 0, 0, 0);

          if (ev.allDay && endDate > startDate) {
            endDate.setDate(endDate.getDate() - 1);
          }
        }

        if (!endDate || Number.isNaN(endDate.getTime()) || endDate.getTime() === startDate.getTime()) {
          return formatDateShortDE(startStr);
        }

        return `${formatDateShortDE(startDate)} – ${formatDateShortDE(endDate)}`;
      }

      function getWarningEmployeeName(ev, selectedIds) {
        const xp = ev.extendedProps || {};
        const selectedSet = new Set((selectedIds || []).map(String));

        if (Array.isArray(xp.employees) && xp.employees.length) {
          const selectedEmployee = xp.employees.find(emp => {
            const id = String(emp?.employee_id || emp?.employeeId || emp?.id || "");
            return selectedSet.has(id);
          });

          if (selectedEmployee) {
            return `${selectedEmployee.name || ""} ${selectedEmployee.lastname || ""}`.trim() || "Mitarbeiter";
          }

          const first = xp.employees[0];
          return `${first?.name || ""} ${first?.lastname || ""}`.trim() || "Mitarbeiter";
        }

        return getEmployeeNameFromEvent(ev) || "Mitarbeiter";
      }

      function getAbsencePriority(type) {
        const order = {
          sick: 1,
          holiday: 2,
          recurring_leave: 3,
          leave_request: 4,
          public_holiday: 5
        };

        return order[String(type || "")] || 99;
      }

      function renderAvailabilityLabelChip(label) {
        let bg = "#f3f4f6";
        let color = "#374151";
        let border = "#e5e7eb";

        if (label === "Urlaub") {
          bg = "#fff7ed";
          color = "#9a3412";
          border = "#fed7aa";
        }

        if (label === "Krank") {
          bg = "#fef2f2";
          color = "#b91c1c";
          border = "#fecaca";
        }

        if (label === "Serie") {
          bg = "#eff6ff";
          color = "#1d4ed8";
          border = "#bfdbfe";
        }

        if (label === "Urlaubsantrag") {
          bg = "#ecfdf5";
          color = "#047857";
          border = "#a7f3d0";
        }

        return `
                    <span style="
                      display:inline-flex;
                      align-items:center;
                      padding:3px 8px;
                      border-radius:999px;
                      background:${bg};
                      color:${color};
                      border:1px solid ${border};
                      font-size:11px;
                      font-weight:900;
                      margin:2px 3px 2px 0;
                      white-space:nowrap;
                    ">
                      ${escapeHtml(label)}
                    </span>
                  `;
      }

      function showEmployeeSelectionWarnings() {
        /*
        |--------------------------------------------------------------------------
        | Informative only
        |--------------------------------------------------------------------------
        | Do not show "Mitarbeiter nicht verfügbar" popups when selecting/deselecting
        | employees. Availability is shown as badges in the employee list and in
        | the employee Select2. Employees can still be selected and appointments can
        | still be saved.
        */
        return;
      }

      function refreshCalendarBlockedDayStyles() {
        if (!S.fc) return;

        S.fc.el.querySelectorAll(".calendar-day-lock-badge").forEach(el => el.remove());

        S.fc.el
          .querySelectorAll(".is-calendar-blocked-day, .is-public-holiday-blocked, .is-employee-absence-blocked")
          .forEach(el => {
            el.classList.remove("is-calendar-blocked-day", "is-public-holiday-blocked", "is-employee-absence-blocked");
          });

        const dayEls = S.fc.el.querySelectorAll("[data-date]");

        dayEls.forEach(el => {
          const dateStr = el.getAttribute("data-date");
          if (!dateStr) return;

          const publicHoliday = getPublicHolidayForDate(dateStr);
          const employeeAbsence = getSingleEmployeeAbsenceForDate(dateStr);
          const blocker = publicHoliday || employeeAbsence;

          if (!blocker) return;

          el.classList.add("is-calendar-blocked-day");

          if (publicHoliday) {
            el.classList.add("is-public-holiday-blocked");
          } else {
            el.classList.add("is-employee-absence-blocked");
          }

          const badge = document.createElement("div");
          badge.className = `calendar-day-lock-badge ${publicHoliday ? "" : "is-employee"}`;
          badge.innerHTML = `
                                <span>${publicHoliday ? "🔒" : "⛔"}</span>
                                <span class="calendar-day-lock-word">${escapeHtml(blocker.label)}</span>
                              `;

          el.appendChild(badge);
        });
      }


      // =========================
      // Urlaubs-Gantt mode
      // =========================
      const URLAUB_GANTT_TYPES = ["holiday", "sick", "recurring_leave", "leave_request"];
      const URLAUB_MONTH_NAMES = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
      const URLAUB_WEEKDAY_NAMES = ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"];

      function bindVacationGanttPanel() {
        const bind = (id, eventName, handler) => {
          const el = document.getElementById(id);
          if (!el || el.dataset.urlaubBound) return;
          el.dataset.urlaubBound = "1";
          el.addEventListener(eventName, handler);
        };

        bind("urlaubGanttPrevYear", "click", () => {
          S.urlaubGanttYear = Number(S.urlaubGanttYear || new Date().getFullYear()) - 1;
          loadVacationGanttData();
        });

        bind("urlaubGanttNextYear", "click", () => {
          S.urlaubGanttYear = Number(S.urlaubGanttYear || new Date().getFullYear()) + 1;
          loadVacationGanttData();
        });

        bind("urlaubGanttCurrentYear", "click", () => {
          S.urlaubGanttYear = (new Date()).getFullYear();
          loadVacationGanttData();
        });

        bind("urlaubGanttRefresh", "click", () => loadVacationGanttData());
        bind("urlaubGanttClose", "click", () => toggleVacationGanttMode(false));

        bind("urlaubGanttMonthSelect", "change", function () {
          S.urlaubGanttMonth = parseInt(this.value || "0", 10) || 0;
          renderVacationGantt();
        });

        bind("urlaubGanttZoomIn", "click", () => {
          S.urlaubGanttZoom = Math.min(3, Number(S.urlaubGanttZoom || 1) + 0.2);
          renderVacationGantt({ keepScroll: true });
        });

        bind("urlaubGanttZoomOut", "click", () => {
          S.urlaubGanttZoom = Math.max(0.45, Number(S.urlaubGanttZoom || 1) - 0.2);
          renderVacationGantt({ keepScroll: true });
        });

        bind("urlaubGanttZoomReset", "click", () => {
          S.urlaubGanttZoom = 1;
          renderVacationGantt({ keepScroll: true });
        });

        const filterRow = document.getElementById("urlaubGanttFilterRow");
        if (filterRow && !filterRow.dataset.urlaubBound) {
          filterRow.dataset.urlaubBound = "1";
          filterRow.addEventListener("change", function (event) {
            const input = event.target.closest("[data-urlaub-filter]");
            if (!input) return;

            const key = input.dataset.urlaubFilter;
            S.urlaubGanttFilters[key] = !!input.checked;
            renderVacationGantt();
          });
        }
      }

      function syncVacationGanttButtonUi() {
        const btn = document.querySelector(".fc-vacationGantt-button");
        if (!btn) return;
        btn.classList.toggle("is-active-urlaub", !!S.urlaubGanttMode);
        btn.setAttribute("aria-pressed", S.urlaubGanttMode ? "true" : "false");
      }

      function toggleVacationGanttMode(force = null) {
        const next = force === null ? !S.urlaubGanttMode : Boolean(force);
        S.urlaubGanttMode = next;

        const section = document.querySelector(".calender_section");
        if (section) section.classList.toggle("is-urlaub-gantt-mode", next);
        if (D.urlaubGanttPanel) D.urlaubGanttPanel.classList.toggle("is-visible", next);

        syncVacationGanttButtonUi();

        if (next) {
          const currentDate = S.fc?.getDate?.() || new Date();
          S.urlaubGanttYear = currentDate.getFullYear();
          S.urlaubGanttMonth = currentDate.getMonth() + 1;
          bindVacationGanttPanel();

          const monthSelect = document.getElementById("urlaubGanttMonthSelect");
          if (monthSelect) monthSelect.value = String(S.urlaubGanttMonth || 0);

          loadVacationGanttData();
        } else {
          setTimeout(() => {
            if (S.fc) {
              S.fc.updateSize();
              refreshCalendarBlockedDayStyles();
            }
          }, 50);
        }
      }

      function getVacationSelectedEmployees() {
        const ids = Array.from(S.selectedEmployeeIds || []).map(String).filter(Boolean);
        const finalIds = ids.length ? ids : [String(AUTH_EMPLOYEE_ID || "")].filter(Boolean);

        return finalIds.map(id => {
          const selectorId = (window.CSS && typeof CSS.escape === "function")
            ? CSS.escape(id)
            : id.replace(/[^a-zA-Z0-9_-]/g, "\\$&");

          const card = document.querySelector(`.employee-card-item[data-employee-id="${selectorId}"]`);
          const option = document.querySelector(`#employee option[value="${selectorId}"]`);

          const cardName = card
            ? `${card.querySelector(".employee-name")?.textContent || ""} ${card.querySelector(".employee-lastname")?.textContent || ""}`.replace(/\s+/g, " ").trim()
            : "";

          const optionName = option ? (option.textContent || "").replace(/\s+/g, " ").trim() : "";
          const image = option?.dataset?.image || card?.querySelector("img")?.getAttribute("src") || "/images/employee/default-avatar.png";

          return {
            id,
            name: cardName || optionName || `Mitarbeiter #${id}`,
            image
          };
        });
      }

      function vacationGanttTypeInfo(type) {
        const key = String(type || "");
        if (key === "sick") return { label: "Krank", cls: "sick", priority: 2 };
        if (key === "recurring_leave") return { label: "Serie", cls: "recurring", priority: 3 };
        if (key === "leave_request") return { label: "Urlaubsantrag", cls: "request", priority: 4 };
        return { label: "Urlaub", cls: "holiday", priority: 1 };
      }

      function vacationStartOfDay(value) {
        if (!value) return null;
        const datePart = String(value).split("T")[0];
        const d = new Date(`${datePart}T00:00:00`);
        if (Number.isNaN(d.getTime())) return null;
        d.setHours(0, 0, 0, 0);
        return d;
      }

      function vacationAddDays(date, days) {
        const d = new Date(date);
        d.setDate(d.getDate() + days);
        d.setHours(0, 0, 0, 0);
        return d;
      }

      function vacationDiffDays(start, end) {
        return Math.round((end - start) / 86400000);
      }

      function vacationDateRangeLabel(start, end) {
        const fmt = d => `${U.pad2(d.getDate())}.${U.pad2(d.getMonth() + 1)}.${d.getFullYear()}`;
        return start.getTime() === end.getTime() ? fmt(start) : `${fmt(start)} - ${fmt(end)}`;
      }

      function vacationSelectedRange() {
        const year = Number(S.urlaubGanttYear || new Date().getFullYear());
        const month = Number(S.urlaubGanttMonth || 0);
        const start = month ? new Date(year, month - 1, 1) : new Date(year, 0, 1);
        const end = month ? new Date(year, month, 0) : new Date(year, 11, 31);
        start.setHours(0, 0, 0, 0);
        end.setHours(0, 0, 0, 0);
        return { year, month, start, end };
      }

      function vacationRangeDays(start, end) {
        const days = [];
        for (let d = new Date(start); d <= end; d = vacationAddDays(d, 1)) {
          days.push(new Date(d));
        }
        return days;
      }

      function vacationEmployeeIdsForRow(row) {
        const ids = new Set();
        const xp = row?.extendedProps || {};
        const push = value => {
          if (value !== null && value !== undefined && String(value).trim() !== "") ids.add(String(value));
        };

        push(row?.employee_id);
        push(row?.employeeId);
        push(row?.emp_id);
        push(row?.employee?.id);
        push(xp.employee_id);
        push(xp.employeeId);
        push(xp.emp_id);

        const employees = row?.employees || xp.employees || [];
        if (Array.isArray(employees)) {
          employees.forEach(emp => {
            if (typeof emp === "object" && emp !== null) {
              push(emp.id ?? emp.employee_id ?? emp.emp_id ?? emp.value);
            } else {
              push(emp);
            }
          });
        }

        return Array.from(ids);
      }

      function vacationRowMatchesEmployee(row, employeeId) {
        employeeId = String(employeeId);
        const ids = vacationEmployeeIdsForRow(row);
        if (!ids.length) return true;
        return ids.map(String).includes(employeeId);
      }

      function vacationNormalizeRow(row, employeeId, range) {
        const xp = row?.extendedProps || {};
        const type = String(row?.type || xp.type || "");

        if (!URLAUB_GANTT_TYPES.includes(type)) return null;
        if (!S.urlaubGanttFilters[type]) return null;
        if (!vacationRowMatchesEmployee(row, employeeId)) return null;

        let rawStart = vacationStartOfDay(row?.start_date || row?.start || xp.start_date || xp.start || row?.recurring_start_date || xp.recurring_start_date);
        let rawEnd = vacationStartOfDay(row?.end_date || row?.end || xp.end_date || xp.end || row?.recurring_end_date || xp.recurring_end_date || row?.start_date || row?.start || xp.start_date || xp.start);

        if (!rawStart) return null;
        if (!rawEnd) rawEnd = new Date(rawStart);

        // FullCalendar event.end is often exclusive for all-day events. If the source came from FC and end is after start, show the inclusive previous day.
        if ((row?.end || xp.end) && !(row?.end_date || xp.end_date) && rawEnd > rawStart) {
          rawEnd = vacationAddDays(rawEnd, -1);
        }

        if (rawEnd < rawStart) rawEnd = new Date(rawStart);

        if (rawEnd < range.start || rawStart > range.end) return null;

        const start = rawStart < range.start ? new Date(range.start) : rawStart;
        const end = rawEnd > range.end ? new Date(range.end) : rawEnd;
        const rangeDays = vacationDiffDays(range.start, range.end) + 1;
        const leftDays = vacationDiffDays(range.start, start);
        const days = vacationDiffDays(start, end) + 1;
        const info = vacationGanttTypeInfo(type);
        const title = row?.absence_title || xp.absence_title || row?.recurring_title || xp.recurring_title || row?.title || xp.title || info.label;

        return {
          type,
          info,
          title,
          rawStart,
          rawEnd,
          start,
          end,
          leftDays,
          days,
          rangeDays,
          rangeLabel: vacationDateRangeLabel(start, end),
        };
      }

      function vacationGanttHeaderHtml(range, leftWidth, timelineWidth, dayWidth) {
        const days = vacationRangeDays(range.start, range.end);
        const columns = `repeat(${days.length}, ${dayWidth}px)`;

        const cells = days.map((d, idx) => {
          const isWeekend = d.getDay() === 0 || d.getDay() === 6;
          const isMonthStart = d.getDate() === 1 || idx === 0;
          const monthLabel = range.month ? URLAUB_WEEKDAY_NAMES[d.getDay()] : URLAUB_MONTH_NAMES[d.getMonth()].slice(0, 3);

          return `
              <div class="urlaub-gantt-day ${isWeekend ? "is-weekend" : ""} ${isMonthStart ? "is-month-start" : ""}">
                <span>${U.pad2(d.getDate())}</span>
                <small>${monthLabel}</small>
              </div>
            `;
        }).join("");

        return `
            <div class="urlaub-gantt-month-header" style="grid-template-columns:${leftWidth}px ${timelineWidth}px;min-width:${leftWidth + timelineWidth}px;">
              <div class="urlaub-gantt-left-head">Mitarbeiter</div>
              <div class="urlaub-gantt-days" style="grid-template-columns:${columns};width:${timelineWidth}px;">
                ${cells}
              </div>
            </div>
          `;
      }

      function updateVacationGanttSummary(employees, itemsByEmployee, range) {
        const summary = document.getElementById("urlaubGanttSummary");
        const subtitle = document.getElementById("urlaubGanttSubtitle");
        const yearLabel = document.getElementById("urlaubGanttYearLabel");
        const zoomLabel = document.getElementById("urlaubGanttZoomLabel");

        if (yearLabel) yearLabel.textContent = String(range.year);
        if (zoomLabel) zoomLabel.textContent = `${Math.round(Number(S.urlaubGanttZoom || 1) * 100)}%`;

        const allItems = Array.from(itemsByEmployee.values()).flat();
        const absentEmployees = employees.filter(emp => (itemsByEmployee.get(String(emp.id)) || []).length > 0).length;
        const workingEmployees = Math.max(0, employees.length - absentEmployees);
        const totalDays = allItems.reduce((sum, item) => sum + Number(item.days || 0), 0);
        const holidayDays = allItems.filter(item => item.type === "holiday" || item.type === "leave_request").reduce((sum, item) => sum + Number(item.days || 0), 0);
        const sickOrSeries = allItems.filter(item => item.type === "sick" || item.type === "recurring_leave").length;
        const periodLabel = range.month ? `${URLAUB_MONTH_NAMES[range.month - 1]} ${range.year}` : `Jahr ${range.year}`;

        if (subtitle) {
          subtitle.textContent = `${periodLabel} · ${employees.length} Mitarbeiter · ${absentEmployees} abwesend · ${workingEmployees} arbeitet · ${allItems.length} Einträge`;
        }

        if (summary) {
          summary.innerHTML = `
              <div class="urlaub-gantt-stat"><strong>${employees.length}</strong><span>Mitarbeiter Gesamt</span></div>
              <div class="urlaub-gantt-stat"><strong>${absentEmployees}</strong><span>Im Urlaub/Abwesend</span></div>
              <div class="urlaub-gantt-stat"><strong>${workingEmployees}</strong><span>Arbeitet</span></div>
              <div class="urlaub-gantt-stat"><strong>${allItems.length}</strong><span>Abwesenheiten</span></div>
              <div class="urlaub-gantt-stat"><strong>${holidayDays}</strong><span>Urlaubstage</span></div>
              <div class="urlaub-gantt-stat"><strong>${sickOrSeries}</strong><span>Krank/Serie</span></div>
            `;
        }
      }

      function renderVacationGantt(options = {}) {
        const table = D.urlaubGanttTable || document.getElementById("urlaubGanttTable");
        if (!table) return;

        const oldScrollLeft = D.urlaubGanttScroll ? D.urlaubGanttScroll.scrollLeft : 0;
        const range = vacationSelectedRange();
        const employees = getVacationSelectedEmployees();
        const rows = Array.isArray(S.urlaubGanttRows) ? S.urlaubGanttRows : [];
        const itemsByEmployee = new Map();

        employees.forEach(emp => {
          const items = rows
            .map(row => vacationNormalizeRow(row, emp.id, range))
            .filter(Boolean)
            .sort((a, b) => a.start - b.start || a.info.priority - b.info.priority);

          itemsByEmployee.set(String(emp.id), items);
        });

        updateVacationGanttSummary(employees, itemsByEmployee, range);

        if (!employees.length) {
          table.innerHTML = `
              <div class="urlaub-gantt-empty">
                <i class="feather icon-users"></i>
                Bitte wähle mindestens einen Mitarbeiter aus.
              </div>
            `;
          if (window.feather) feather.replace();
          return;
        }

        const leftWidth = 250;
        const baseDayWidth = range.month ? 34 : 22;
        const zoom = Number(S.urlaubGanttZoom || 1);
        const dayWidth = Math.max(12, Math.round(baseDayWidth * zoom));
        const rangeDays = vacationDiffDays(range.start, range.end) + 1;
        const timelineWidth = rangeDays * dayWidth;
        const totalWidth = leftWidth + timelineWidth;

        const body = employees.map(emp => {
          const items = itemsByEmployee.get(String(emp.id)) || [];
          const minHeight = items.length > 1 ? Math.max(74, 24 + (items.length * 30)) : 58;

          const bars = items.length
            ? items.map((item, index) => {
              const topOffset = items.length > 1 ? 12 + (index * 30) : 12;
              const height = items.length > 1 ? 24 : 34;
              const left = (item.leftDays * dayWidth) + 4;
              const width = Math.max(12, (item.days * dayWidth) - 8);
              const title = `${item.info.label}: ${item.title} · ${item.rangeLabel} · ${item.days} Tag(e)`;

              return `
                  <div
                    class="urlaub-gantt-bar urlaub-gantt-bar--${item.info.cls}"
                    title="${escapeHtml(title)}"
                    style="left:${left}px;width:${width}px;top:${topOffset}px;height:${height}px;"
                  >
                    <span>${escapeHtml(item.info.label)}</span>
                    <small>${escapeHtml(item.rangeLabel)}</small>
                  </div>
                `;
            }).join("")
            : `<span class="urlaub-gantt-working"><i class="urlaub-gantt-dot urlaub-gantt-dot--work"></i> Arbeitet / kein Urlaub</span>`;

          return `
              <div class="urlaub-gantt-row" style="grid-template-columns:${leftWidth}px ${timelineWidth}px;min-width:${totalWidth}px;min-height:${minHeight}px;">
                <div class="urlaub-gantt-employee-cell" style="min-height:${minHeight}px">
                  <img class="urlaub-gantt-avatar" src="${escapeHtml(emp.image)}" alt="${escapeHtml(emp.name)}">
                  <div class="urlaub-gantt-employee-name">
                    <strong>${escapeHtml(emp.name)}</strong>
                    <span>${items.length ? `${items.length} Abwesenheit(en)` : "Arbeitet im ausgewählten Zeitraum"}</span>
                  </div>
                </div>
                <div class="urlaub-gantt-line" style="min-height:${minHeight}px;width:${timelineWidth}px;background-size:${dayWidth}px 100%;">${bars}</div>
              </div>
            `;
        }).join("");

        table.style.minWidth = `${totalWidth}px`;
        table.innerHTML = vacationGanttHeaderHtml(range, leftWidth, timelineWidth, dayWidth) + body;

        if (options.keepScroll && D.urlaubGanttScroll) {
          D.urlaubGanttScroll.scrollLeft = oldScrollLeft;
        }

        if (window.feather) feather.replace();
      }

      async function loadVacationGanttData() {
        const range = vacationSelectedRange();
        S.urlaubGanttYear = range.year;

        if (D.urlaubGanttTable) {
          D.urlaubGanttTable.innerHTML = `
              <div class="urlaub-gantt-empty">
                <i class="feather icon-loader"></i>
                Urlaubsübersicht wird geladen...
              </div>
            `;
          if (window.feather) feather.replace();
        }

        const start = `${range.year}-01-01`;
        const end = `${range.year + 1}-01-01`;
        const employeeData = getSelectedEmployeeData();

        try {
          const res = await U.getJSON(ROUTE.getCalendar, {
            employee_data: JSON.stringify(employeeData),
            search: S.currentSearch || "",
            include_all_absences: 1,
            only_deleted_appointments: 0,
            start,
            end
          });

          S.urlaubGanttRows = Array.isArray(res?.data) ? res.data : [];
          renderVacationGantt();
        } catch (err) {
          console.error("loadVacationGanttData:", err);
          if (D.urlaubGanttTable) {
            D.urlaubGanttTable.innerHTML = `
                <div class="urlaub-gantt-empty">
                  <i class="feather icon-alert-triangle"></i>
                  Urlaubsübersicht konnte nicht geladen werden.
                </div>
              `;
            if (window.feather) feather.replace();
          }
        }
      }

      function initializeCalendar(events) {
        if (S.fc) {
          S.fc.getEventSources().forEach(s => s.remove());
          S.fc.addEventSource(events);
          S.fc.refetchEvents();
          setTimeout(refreshCalendarBlockedDayStyles, 80);
          return;
        }
        S.fc = new FullCalendar.Calendar(D.cal, {
          initialView: U.isMobile() ? "listWeek" : "timeGridWeek",
          locale: "de",
          firstDay: 1,
          weekNumbers: true,
          weekNumberCalculation: "ISO",
          allDaySlot: true,
          allDayText: "Ganztägig",
          dayHeaderFormat: { weekday: "short", day: "numeric" },
          eventDisplay: "block",
          slotMinTime: "07:00:00",
          slotMaxTime: "23:59:59",
          slotDuration: "00:30:00",
          slotLabelInterval: "01:00:00",
          nowIndicator: true,
          displayEventTime: true,
          eventTimeFormat: { hour: "2-digit", minute: "2-digit", hour12: false },
          height: "auto",
          expandRows: true,
          dayMaxEvents: 6,
          dayMaxEventRows: 6,
          slotLabelFormat: { hour: "2-digit", minute: "2-digit", omitZeroMinute: false, meridiem: false },
          headerToolbar: {
            left: "prev,next today toggleSlider toggleAllDay verfgBtn trashMode searchBox",
            center: "title",
            right: "multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek vacationGantt",
          },
          views: {
            multiMonthYear: {
              type: "multiMonth",
              duration: { months: 12 },
              buttonText: "Jahr",
              multiMonthMaxColumns: 3,
              fixedWeekCount: false,
              showNonCurrentDates: true,
            },
            dayGridMonth: {
              buttonText: "Monat",
              fixedWeekCount: false,
              showNonCurrentDates: true,
            },
            timeGridWeek: {
              buttonText: "Woche",
            },
            timeGridDay: {
              buttonText: "Tag",
            },
            listWeek: {
              buttonText: "Übersicht",
            },
          },

          editable: true,
          eventResizableFromStart: true,
          events,
          customButtons: {
            toggleSlider: {
              text: "⇔", click() {
                const $slider = $("#slider_section"), $cal = $(".calender_section");
                const hidden = $slider.hasClass("d-none");
                if (hidden) {
                  $slider.removeClass("d-none");
                  $cal
                    .removeClass("col-xl-12 col-lg-12 col-md-12")
                    .addClass("col-xl-10 col-lg-9 col-md-8");

                  setTimeout(() => S.mini && S.mini.render(), 10);
                } else {
                  $slider.addClass("d-none");
                  $cal
                    .removeClass("col-xl-10 col-lg-9 col-md-8")
                    .addClass("col-xl-12 col-lg-12 col-md-12");
                }
                S.fc.updateSize();
              }
            },
            toggleAllDay: {
              text: "Ganztag aus",
              click() {
                const current = S.fc.getOption("allDaySlot");
                const next = !current;

                S.fc.setOption("allDaySlot", next);

                const btn = document.querySelector(".fc-toggleAllDay-button");
                if (btn) {
                  btn.innerText = next ? "Ganztag aus" : "Ganztag an";
                }
              }
            },
            verfgBtn: { text: "Verfügbarkeit", click: () => (window.location.href = "/employee-availability") },
            trashMode: {
              text: "Papierkorb",
              click() {
                const view = S.fc?.view?.type || "timeGridWeek";
                const date = S.fc?.getDate?.() || new Date();
                S.showDeletedAppointmentsOnly = !S.showDeletedAppointmentsOnly;
                loadCalendarTasks(() => {
                  S.fc.changeView(view);
                  S.fc.gotoDate(date);
                  updateTrashModeUi();
                });
              }
            },
            vacationGantt: {
              text: "Urlaubs",
              click() {
                toggleVacationGanttMode(true);
              }
            },
            searchBox: { text: "Suche", click() { } },
          },
          buttonText: {
            today: "Heute",
            month: "Monat",
            week: "Woche",
            day: "Tag",
            list: "Übersicht",
          },
          windowResize() {
            const mobile = U.isMobile();

            if (S.lastCalendarMobileMode === null) {
              S.lastCalendarMobileMode = mobile;
              S.fc.updateSize();
              return;
            }

            if (mobile === S.lastCalendarMobileMode) {
              S.fc.updateSize();
              return;
            }

            S.lastCalendarMobileMode = mobile;

            if (mobile) {
              if (S.fc.view.type !== "listWeek") {
                S.lastDesktopCalendarView = S.fc.view.type || "timeGridWeek";
              }

              S.fc.changeView("listWeek");
            } else {
              S.fc.changeView(S.lastDesktopCalendarView || "timeGridWeek");
            }

            setTimeout(() => S.fc.updateSize(), 50);
          },
          dayCellDidMount(info) {
            setTimeout(refreshCalendarBlockedDayStyles, 0);
          },
          dateClick(info) {
            if (S.showDeletedAppointmentsOnly) {
              Swal.fire({
                icon: "info",
                title: "Papierkorb-Modus",
                text: "Im Papierkorb können keine neuen Termine erstellt werden. Bitte zuerst zurück zu den aktiven Terminen wechseln.",
                confirmButtonColor: "#93c21c"
              });
              return;
            }

            const date = info.dateStr.split("T")[0];

            const publicHoliday = getPublicHolidayForDate(date);
            if (publicHoliday) {
              showCalendarBlockedAlert(publicHoliday);
              return;
            }

            // Employee absence is only informative now. Do not block opening the appointment modal.

            document.getElementById("task-store-form").reset();
            maResetAppointmentStageFields();
            $("#appointment_id").val("");
            $(".title").text("TERMIN ERSTELLEN");

            const time = info.dateStr.includes("T") ? info.dateStr.split("T")[1].slice(0, 5) : "00:00";
            $("#start_date").val(date);
            $("#end_date").val(date);
            $("#start_time").val(time);

            prepareNewAppointmentEmployeeSelection();
            if (D.newTaskCard) D.newTaskCard.style.display = "block";
          },
          moreLinkClick(info) {
            const d = info.date, list = S.fc.getEvents().filter(ev => ev.start.toDateString() === d.toDateString());
            showDayEventsModal(list, d); return false;
          },
          eventClick(info) {
            const t = info.event.extendedProps.type;
            if (isAbsenceCalendarType(t)) {
              showAbsenceDetailsModal(info.event);
              return;
            }
            showEventDetailsModal(info.event);
          },
          eventDidMount: decorateEventEl,
          eventDrop: handleEventUpdate,
          eventResize: handleEventUpdate,
        });

        // anchor to ?task_id=
        const taskId = new URLSearchParams(location.search).get("task_id");
        if (taskId) {
          const ev = events.find(e => e.id.split("-")[0] === taskId);
          if (ev) S.fc.gotoDate(ev.start);
        }

        S.fc.render();
        mountCalendarSearch();
        S.fc.on("datesSet", mountCalendarSearch);
        S.fc.on("datesSet", () => setTimeout(refreshCalendarBlockedDayStyles, 0));
        S.fc.on("datesSet", () => syncMiniCalendarWithMain(true));
        S.fc.on("datesSet", function (info) {
          const key = [
            info.view.type,
            U.isoDate(info.start),
            U.isoDate(info.end),
            Array.from(S.selectedEmployeeIds).sort().join(","),
            S.currentSearch || "",
            S.showDeletedAppointmentsOnly ? "trash" : "active"
          ].join("|");

          if (S.lastCalendarRangeKey === key) return;
          S.lastCalendarRangeKey = key;

          clearTimeout(S.calendarRangeReloadTimer);
          S.calendarRangeReloadTimer = setTimeout(() => loadCalendarTasks(), 150);
        });
      }

      function markMiniSelectedDate(dateStr) {
        if (!dateStr || !D.mini) return;

        U.qa("#mini_calendar .fc-day-selected").forEach(el => {
          el.classList.remove("fc-day-selected");
          el.removeAttribute("aria-selected");
        });

        const dayCell = D.mini.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
        if (!dayCell) return;

        dayCell.classList.add("fc-day-selected");
        dayCell.setAttribute("aria-selected", "true");
      }

      function syncMiniCalendarWithMain(forceMonth = false) {
        if (!S.fc || !D.mini) return;

        const mainDate = S.fc.getDate ? S.fc.getDate() : new Date();
        const mainDateStr = U.isoDate(mainDate);

        if (forceMonth && S.mini && typeof S.mini.gotoDate === "function" && typeof S.mini.getDate === "function") {
          const miniDate = S.mini.getDate();
          const mainMonth = `${mainDate.getFullYear()}-${U.pad2(mainDate.getMonth() + 1)}`;
          const miniMonth = `${miniDate.getFullYear()}-${U.pad2(miniDate.getMonth() + 1)}`;

          if (mainMonth !== miniMonth) {
            S.mini.gotoDate(mainDateStr);
          }
        }

        setTimeout(() => markMiniSelectedDate(mainDateStr), 0);
      }

      // Backward-compatible globals for older inline/calendar handlers.
      window.markMiniSelectedDate = markMiniSelectedDate;
      window.syncMiniCalendarWithMain = syncMiniCalendarWithMain;

      function initializeMiniCalendar(events) {
        if (!D.mini) {
          console.warn("Mini calendar element #mini_calendar not found.");
          return;
        }

        if (S.mini) {
          S.mini.destroy();
          S.mini = null;
        }

        S.mini = new FullCalendar.Calendar(D.mini, {
          initialView: "dayGridMonth",
          locale: "de",
          firstDay: 1,
          selectable: true,
          height: "auto",
          fixedWeekCount: false,
          showNonCurrentDates: true,
          events: events || [],

          headerToolbar: {
            left: "prev",
            center: "title",
            right: "next"
          },

          dateClick(info) {
            if (!S.fc) return;

            S.fc.gotoDate(info.dateStr);

            if (S.fc.view.type !== "timeGridWeek" && S.fc.view.type !== "timeGridDay") {
              S.fc.changeView("timeGridWeek");
            }

            markMiniSelectedDate(info.dateStr);
          },

          datesSet() {
            syncMiniCalendarWithMain();
          }
        });

        S.mini.render();

        setTimeout(() => {
          if (S.fc) {
            markMiniSelectedDate(U.isoDate(S.fc.getDate()));
          }

          if (window.feather) {
            feather.replace();
          }
        }, 50);
      }

      function reloadCalendarWithSearch(done) {
        const view = S.fc.view.type, date = S.fc.getDate();
        loadCalendarTasks(() => { S.fc.changeView(view); S.fc.gotoDate(date); if (typeof done === "function") done(); });
      }

      function normalizeExecutionType(value) {
        const key = String(value || '').toLowerCase();

        const map = {
          internal: 'Intern',
          intern: 'Intern',
          external: 'Extern',
          extern: 'Extern',
          online: 'Online',
          telephone: 'Telefon',
          telefon: 'Telefon'
        };

        return map[key] || value || '-';
      }

      function normalizeCalendarType(value) {
        const key = String(value || '').toLowerCase();

        const map = {
          appointment: 'Termin',
          task: 'Aufgabe',
          public_holiday: 'Feiertag',
          holiday: 'Urlaub',
          sick: 'Krank',
          recurring_leave: 'Wiederkehrender Termin',
          leave_request: 'Urlaubsantrag'
        };

        return map[key] || value || '-';
      }

      function buildCalendarTooltipHtml(event) {
        const xp = event.extendedProps || {};
        const employees = (xp.employees || [])
          .map(e => `${e.name || ''} ${e.lastname || ''}`.trim())
          .filter(Boolean)
          .join(', ');

        const address = xp.full_address && xp.full_address !== '-'
          ? xp.full_address
          : [xp.street, xp.postcode, xp.city].filter(Boolean).join(' ') || '-';

        return `
                                                                <div class="calendar-smart-tooltip-title">${escapeHtml(event.title || '-')}</div>

                                                                <div class="calendar-smart-tooltip-row">
                                                                  <div class="calendar-smart-tooltip-label">Typ</div>
                                                                  <div class="calendar-smart-tooltip-value">${escapeHtml(xp.appointment_type || normalizeCalendarType(xp.type))}</div>
                                                                </div>

                                                                <div class="calendar-smart-tooltip-row">
                                                                  <div class="calendar-smart-tooltip-label">Ort</div>
                                                                  <div class="calendar-smart-tooltip-value">${escapeHtml(normalizeExecutionType(xp.execution_type))}</div>
                                                                </div>

                                                                <div class="calendar-smart-tooltip-row">
                                                                  <div class="calendar-smart-tooltip-label">Adresse</div>
                                                                  <div class="calendar-smart-tooltip-value">${escapeHtml(address)}</div>
                                                                </div>

                                                                <div class="calendar-smart-tooltip-row">
                                                                  <div class="calendar-smart-tooltip-label">Zeit</div>
                                                                  <div class="calendar-smart-tooltip-value">${escapeHtml(U.shortHM(xp.start_time))} - ${escapeHtml(U.shortHM(xp.end_time))}</div>
                                                                </div>

                                                                ${maStageLabelFromProps(xp) ? `
                                                                  <div class="calendar-smart-tooltip-row">
                                                                    <div class="calendar-smart-tooltip-label">CRM Stage</div>
                                                                    <div class="calendar-smart-tooltip-value">${escapeHtml(maStageLabelFromProps(xp))}</div>
                                                                  </div>
                                                                ` : ''}

                                                                ${employees ? `
                                                                  <div class="calendar-smart-tooltip-row">
                                                                    <div class="calendar-smart-tooltip-label">Mitarbeiter</div>
                                                                    <div class="calendar-smart-tooltip-value">${escapeHtml(employees)}</div>
                                                                  </div>
                                                                ` : ''}

                                                                ${xp.phone && xp.phone !== '-' ? `
                                                                  <div class="calendar-smart-tooltip-row">
                                                                    <div class="calendar-smart-tooltip-label">Telefon</div>
                                                                    <div class="calendar-smart-tooltip-value">${escapeHtml(xp.phone)}</div>
                                                                  </div>
                                                                ` : ''}

                                                                ${xp.email && xp.email !== '-' ? `
                                                                  <div class="calendar-smart-tooltip-row">
                                                                    <div class="calendar-smart-tooltip-label">E-Mail</div>
                                                                    <div class="calendar-smart-tooltip-value">${escapeHtml(xp.email)}</div>
                                                                  </div>
                                                                ` : ''}

                                                                ${xp.description && xp.description !== '-' ? `
                                                                  <div class="calendar-smart-tooltip-row">
                                                                    <div class="calendar-smart-tooltip-label">Text</div>
                                                                    <div class="calendar-smart-tooltip-value">${escapeHtml(xp.description)}</div>
                                                                  </div>
                                                                ` : ''}
                                                              `;
      }

      const CalendarSmartTooltip = (() => {
        let timer = null;
        let tooltip = null;
        let locked = false;
        let activeEl = null;

        const clearTimer = () => {
          if (timer) {
            clearTimeout(timer);
            timer = null;
          }
        };

        const remove = () => {
          clearTimer();
          locked = false;
          activeEl = null;
          if (tooltip) {
            tooltip.remove();
            tooltip = null;
          }
        };

        const position = (mouseEvent) => {
          if (!tooltip || !mouseEvent) return;

          const offset = 14;
          const rect = tooltip.getBoundingClientRect();

          let left = mouseEvent.clientX + offset;
          let top = mouseEvent.clientY + offset;

          if (left + rect.width > window.innerWidth - 10) {
            left = window.innerWidth - rect.width - 10;
          }

          if (top + rect.height > window.innerHeight - 10) {
            top = mouseEvent.clientY - rect.height - offset;
          }

          tooltip.style.left = `${Math.max(10, left)}px`;
          tooltip.style.top = `${Math.max(10, top)}px`;
        };

        const show = (el, event, mouseEvent) => {
          remove();
          activeEl = el;

          tooltip = document.createElement('div');
          tooltip.className = 'calendar-smart-tooltip';
          tooltip.innerHTML = `
                                                      <button type="button" class="calendar-smart-tooltip-close" aria-label="Tooltip schließen">×</button>
                                                      ${buildCalendarTooltipHtml(event)}
                                                    `;

          tooltip.querySelector('.calendar-smart-tooltip-close')?.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            remove();
          });

          tooltip.addEventListener('mouseenter', function () {
            locked = true;
            clearTimer();
          });

          tooltip.addEventListener('mouseleave', function () {
            remove();
          });

          document.body.appendChild(tooltip);
          position(mouseEvent);

          requestAnimationFrame(() => {
            if (tooltip) tooltip.classList.add('is-visible');
          });
        };

        const schedule = (el, event, mouseEvent) => {
          remove();
          activeEl = el;
          timer = setTimeout(() => show(el, event, mouseEvent), 450);
        };

        const move = (el, mouseEvent) => {
          if (!tooltip || locked || activeEl !== el) return;
          position(mouseEvent);
        };

        const leave = (el) => {
          clearTimer();
          if (locked || activeEl !== el) return;
          remove();
        };

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') remove();
        });

        document.addEventListener('click', function (e) {
          if (tooltip && !tooltip.contains(e.target) && !(activeEl && activeEl.contains(e.target))) {
            remove();
          }
        }, true);

        return { schedule, move, leave, remove };
      })();

      function attachDelayedCalendarTooltip(el, event) {
        if (!el || !event) return;

        el.addEventListener('mouseenter', function (e) {
          CalendarSmartTooltip.schedule(el, event, e);
        });

        el.addEventListener('mousemove', function (e) {
          CalendarSmartTooltip.move(el, e);
        });

        el.addEventListener('mouseleave', function () {
          CalendarSmartTooltip.leave(el);
        });

        el.addEventListener('mousedown', function () {
          CalendarSmartTooltip.remove();
        });
      }

      const ALL_DAY_EVENT_UI = {
        public_holiday: {
          label: "Feiertag",
          className: "all-day-pill--public-holiday",
          icon: `
                                                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <rect x="3" y="4" width="18" height="17" rx="3"></rect>
                                                        <line x1="8" y1="2.5" x2="8" y2="6"></line>
                                                        <line x1="16" y1="2.5" x2="16" y2="6"></line>
                                                        <line x1="3" y1="9" x2="21" y2="9"></line>
                                                        <path d="M12 12l.9 1.8 2 .3-1.5 1.5.4 2.1-1.8-.9-1.8.9.4-2.1-1.5-1.5 2-.3z"></path>
                                                      </svg>
                                                    `
        },
        holiday: {
          label: "Urlaub",
          className: "all-day-pill--holiday",
          icon: `
                                                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <circle cx="17" cy="7" r="2.5"></circle>
                                                        <path d="M7 20V10"></path>
                                                        <path d="M7 10c3 0 5-1.2 6-4"></path>
                                                        <path d="M7 13c3 0 5 1.1 7 3"></path>
                                                        <path d="M4 20h7"></path>
                                                      </svg>
                                                    `
        },
        sick: {
          label: "Krank",
          className: "all-day-pill--sick",
          icon: `
                                                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <rect x="4" y="4" width="16" height="16" rx="4"></rect>
                                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                                      </svg>
                                                    `
        },
        recurring_leave: {
          label: "Serie",
          className: "all-day-pill--recurring",
          icon: `
                                                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M17 1l4 4-4 4"></path>
                                                        <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                                                        <path d="M7 23l-4-4 4-4"></path>
                                                        <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                                                      </svg>
                                                    `
        },
        leave_request: {
          label: "Antrag",
          className: "all-day-pill--leave-request",
          icon: `
                                                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"></path>
                                                        <path d="M14 2v5h5"></path>
                                                        <path d="M9 13l2 2 4-4"></path>
                                                      </svg>
                                                    `
        }
      };

      function getAllDayUi(type, isCancelled = false) {
        const base = ALL_DAY_EVENT_UI[type] || ALL_DAY_EVENT_UI.public_holiday;

        return {
          ...base,
          className: isCancelled
            ? `${base.className} all-day-pill--cancelled`
            : base.className
        };
      }

      function getEventEmployeeNames(xp) {
        const fromRoster = (xp.employees || [])
          .map(e => `${e.name || ""} ${e.lastname || ""}`.trim())
          .filter(Boolean);

        const directName = [xp.employee_name || xp.name, xp.employee_lastname || xp.lastname]
          .filter(Boolean)
          .join(" ")
          .trim();

        return [...fromRoster, directName]
          .filter(Boolean)
          .filter((name, index, arr) => arr.indexOf(name) === index)
          .join(", ");
      }

      function cleanRecurringDisplayTitle(xp, ev) {
        const employeeNames = getEventEmployeeNames(xp);
        const genericTitles = [
          "Wiederkehrender Termin",
          "Wiederkehrend",
          "Recurring Leave",
          "Recurring",
          "Urlaub"
        ].map(v => v.toLowerCase());

        const candidates = [
          xp.recurring_title,
          xp.absence_title,
          xp.real_title,
          xp.original_title,
          ev?.title
        ];

        for (let candidate of candidates) {
          let title = String(candidate || "").trim();
          if (!title || genericTitles.includes(title.toLowerCase())) continue;

          if (employeeNames) {
            employeeNames.split(",").map(n => n.trim()).filter(Boolean).forEach(name => {
              title = title
                .replace(new RegExp(`^${escapeRegExp(name)}\\s*[–-]\\s*`, "i"), "")
                .replace(new RegExp(`^${escapeRegExp(name)}\\s*`, "i"), "")
                .trim();
            });
          }

          if (title && !genericTitles.includes(title.toLowerCase())) {
            return title;
          }
        }

        return "Termin";
      }

      function escapeRegExp(value) {
        return String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
      }

      function getAllDayMetaText(type, ev, xp) {
        if (type === "sick") {
          return xp.description && xp.description !== "-" ? xp.description : "Krankmeldung";
        }

        if (type === "holiday") {
          return xp.leave_type || xp.leave_reason || "Genehmigter Urlaub";
        }

        if (type === "recurring_leave") {
          const recurringTitle = cleanRecurringDisplayTitle(xp, ev);
          if (xp.is_cancelled) return recurringTitle ? `${recurringTitle} · Abgesagt / storniert` : "Abgesagt / storniert";
          return recurringTitle || normalizeRecurringRule(xp.recurring_rule_type || xp.recurring_type) || "";
        }

        if (type === "leave_request") {
          return xp.leave_status || xp.leave_reason || "Offener Urlaubsantrag";
        }

        if (type === "public_holiday") {
          return xp.city && xp.city !== "-" ? xp.city : "Feiertag";
        }

        return "";
      }

      function renderAllDayEvent(ev, el, xp) {
        const type = xp.type || "";
        const isCancelled = xp.is_cancelled === true || xp.status === "cancelled";
        const ui = getAllDayUi(type, isCancelled);

        const employeeNames = getEventEmployeeNames(xp);
        let meta = getAllDayMetaText(type, ev, xp);

        let title = ev.title || "-";

        if (type === "recurring_leave") {
          // Required order for all-day recurring cards:
          // 1) recurring event name, 2) employee name, 3) Serie label.
          title = cleanRecurringDisplayTitle(xp, ev)
            || xp.recurring_title
            || xp.absence_title
            || ev.title
            || "Wiederkehrender Termin";

          meta = employeeNames || "Mitarbeiter nicht zugeordnet";

          if (isCancelled && !String(title).toLowerCase().includes("abgesagt")) {
            title = `${title} · Abgesagt / storniert`;
          }
        } else if (employeeNames) {
          title = employeeNames;
        }

        Object.assign(el.style, {
          border: "0",
          background: "transparent",
          padding: "0",
          boxShadow: "none",
          overflow: "hidden"
        });

        el.innerHTML = `
                                      <div class="all-day-pill ${ui.className}">
                                        <div class="all-day-pill__icon">
                                          ${ui.icon}
                                        </div>

                                        <div class="all-day-pill__body">
                                          ${type === "recurring_leave"
            ? `
                                                <div class="all-day-pill__label">${escapeHtml(title)}</div>
                                                <div class="all-day-pill__title">${escapeHtml(U.trunc(meta, 60))}</div>
                                                <div class="all-day-pill__meta">${escapeHtml(ui.label || "Serie")}</div>
                                              `
            : `
                                                <div class="all-day-pill__label">${ui.label}</div>
                                                <div class="all-day-pill__title">${escapeHtml(title)}</div>
                                                ${meta ? `<div class="all-day-pill__meta">${escapeHtml(U.trunc(meta, 60))}</div>` : ""}
                                              `
          }
                                        </div>
                                      </div>
                                    `;
      }
      // =========================
      // Event rendering & modals
      // =========================
      function decorateEventEl(info) {
        const ev = info.event;
        const el = info.el;
        const xp = ev.extendedProps || {};
        if (isAbsenceCalendarType(xp.type)) {
          el.classList.add('calendar-absence-clickable');
          el.setAttribute('title', 'Details anzeigen');
        }
        const {
          type,
          employees,
          priority,
          public: isPublic,
          start_time,
          end_time
        } = xp;

        const taskIdFromUrl = new URLSearchParams(location.search).get("task_id");
        if (taskIdFromUrl && ev.id.split("-")[0] === taskIdFromUrl) {
          el.classList.add("edited-event");
          el.scrollIntoView({ behavior: "smooth", block: "center" });
          setTimeout(() => el.classList.remove("edited-event"), 3000);
        }

        /*
        |--------------------------------------------------------------------------
        | Unified all-day / ganztag rendering
        |--------------------------------------------------------------------------
        */
        if (["public_holiday", "holiday", "sick", "recurring_leave", "leave_request"].includes(type)) {
          renderAllDayEvent(ev, el, xp);
          return;
        }

        /*
        |--------------------------------------------------------------------------
        | Existing normal timed events
        |--------------------------------------------------------------------------
        */
        if (window.innerWidth <= 500 && S.fc.view.type === "timeGridWeek") {
          const bg = ev.backgroundColor || ev.extendedProps?.color || "#006400";
          el.setAttribute(
            "style",
            `background-color:${bg}!important;color:#fff!important;border-left:4px solid ${bg}!important;border-radius:6px!important;padding:5px!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;font-size:11px!important;text-align:left!important;max-width:100px!important;`
          );
          el.innerHTML = `
                                            <div><strong>${U.trunc(ev.title, 20)}</strong></div>
                                            <div style="font-size:10px;">${U.shortHM(start_time)} - ${U.shortHM(end_time)}</div>
                                          `;
          attachDelayedCalendarTooltip(el, ev);
          return;
        }

        el.classList.add("fc-daygrid-dot-event", "fc-event");
        el.innerHTML = "";

        const bg = ev.backgroundColor || ev.extendedProps?.color || "#006400";
        el.setAttribute(
          "style",
          `white-space:normal!important;border:0!important;border-left:5px solid ${bg}!important;background-color:${U.hexRGBA(bg, .4)}!important;`
        );

        const { has_ticket, ticket_problem_id } = xp;
        const ticketUrl = has_ticket && ticket_problem_id ? U.makeTicketUrl(ticket_problem_id) : null;

        const ticketBtn = ticketUrl
          ? `
                                            <a href="${ticketUrl}"
                                               class="fc-ticket-link"
                                               title="Ticket öffnen"
                                               target="_blank"
                                               rel="noopener"
                                               style="display:inline-flex;align-items:center;margin-left:6px;text-decoration:none;color:#444">
                                               ${TICKETS.svg}
                                            </a>
                                          `
          : "";

        const names = (employees || []).map(e => `${e.name} ${e.lastname}`).join(", ");
        const appointmentTypeLabel = xp.appointment_type && xp.appointment_type !== "-"
          ? xp.appointment_type
          : normalizeCalendarType(type);

        const executionTypeLabel = normalizeExecutionType(xp.execution_type);

        const addressLabel = xp.full_address && xp.full_address !== "-"
          ? xp.full_address
          : [xp.street, xp.postcode, xp.city].filter(Boolean).join(" ");

        const stageLabel = maStageLabelFromProps(xp);

        el.innerHTML = `
                                          <div class="custom-event">
                                            <div class="custom-event-header d-flex align-items-center" id="calendar_icons">
                                              <i class="fa ${isPublic !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
                                              <i class="fa ${priority === "very high" ? "fa-fire warning mr-1" : (priority === "high" ? "fa-bell important mr-1" : "")}"></i>

                                              <p class="p-0 m-0" id="calendar_times" style="font-size:10px;color:${type === "task" ? "#74b2d4" : "#4c4c4c"};">
                                                ${U.shortHM(start_time)} - ${U.shortHM(end_time)}
                                              </p>

                                              ${ticketBtn}
                                            </div>

                                            <div class="custom-event-title m-0">
                                              <p style="font-size:10px;margin:0;color:${type === "task" ? "#74b2d4" : "#4c4c4c"};font-weight:bold;">
                                                ${escapeHtml(U.trunc(ev.title, 28))}
                                              </p>

                                              <div class="calendar-event-meta">
                                                <span class="calendar-event-chip">${escapeHtml(appointmentTypeLabel)}</span>
                                                <span class="calendar-event-chip">${escapeHtml(executionTypeLabel)}</span>
                                                ${stageLabel ? `<span class="calendar-event-chip ma-stage-calendar-chip">${escapeHtml(U.trunc(stageLabel, 35))}</span>` : ''}
                                              </div>

                                              ${addressLabel ? `
                                                <div class="calendar-event-address">
                                                  <i class="feather icon-map-pin"></i> ${escapeHtml(U.trunc(addressLabel, 55))}
                                                </div>
                                              ` : ""}

                                              ${names ? `
                                                <p style="font-size:8px;color:${type === "task" ? "#74b2d4" : "#4c4c4c"};margin-top:2px;">
                                                  ${escapeHtml(U.trunc(names, 45))}
                                                </p>
                                              ` : ""}
                                            </div>
                                          </div>
                                        `;

        attachDelayedCalendarTooltip(el, ev);

        if (ticketUrl) {
          el.querySelector(".fc-ticket-link")?.addEventListener("click", (evt) => evt.stopPropagation());
        }
      }


      function escapeHtml(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }


      async function fetchSelectedProductsForEvent(cleanId, xp) {
        let products = extractCustomerProductsFromEvent(xp);

        if (products.length) {
          return products;
        }

        try {
          const data = await U.getJSON(ROUTE.fetchMainAppointment(cleanId));
          const fromDetail = {
            products: data.product_json ?? data.products ?? null
          };
          return extractCustomerProductsFromEvent(fromDetail);
        } catch (e) {
          console.error('Could not fetch selected products for event:', e);
          return [];
        }
      }

      function extractCustomerProductsFromEvent(xp) {
        let raw = xp.products ?? xp.product_json ?? xp.products_json ?? null;

        try {
          if (typeof raw === 'string') raw = JSON.parse(raw);
          if (typeof raw === 'string') raw = JSON.parse(raw);
        } catch (e) {
          raw = null;
        }

        if (!raw) return [];

        if (Array.isArray(raw)) {
          return raw.map(item => ({
            uid: item.uid || `${item.name || item.product_name || 'Produkt'}_${item.alternative_id || ''}`,
            name: item.product_name || item.name || item.text || 'Produkt',
            alternative_id: item.alternative_id || item.alt_id || null,
            product_id: item.product_id || null,
            customer_id: item.customer_id || null,
            city: item.city || null
          }));
        }

        if (typeof raw === 'object') {
          return Object.entries(raw).map(([name, tuple]) => ({
            uid: `${name}_${Array.isArray(tuple) ? tuple[0] : ''}`,
            name: name || 'Produkt',
            alternative_id: Array.isArray(tuple) ? tuple[0] : null,
            product_id: Array.isArray(tuple) ? tuple[1] : null,
            customer_id: Array.isArray(tuple) ? tuple[2] : null,
            city: null
          }));
        }

        return [];
      }
      function renderInlineCustomerProducts(products) {
        if (!products || !products.length) {
          return `<div class="customer-products-empty">Keine gespeicherten Produkte gefunden.</div>`;
        }

        const seen = new Set();
        const uniqueProducts = products.filter(product => {
          const key = product.uid || `${product.name}_${product.alternative_id}_${product.product_id}`;
          if (seen.has(key)) return false;
          seen.add(key);
          return true;
        });

        const chips = uniqueProducts.slice(0, 6).map(product => `
                                                                <span class="customer-products-chip">
                                                                  <svg viewBox="0 0 24 24" width="12" height="12" fill="none">
                                                                    <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                    <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                  </svg>
                                                                  ${escapeHtml(product.name)}
                                                                </span>
                                                                `).join('');

        const extra = uniqueProducts.length > 6
          ? `<span class="customer-products-chip">+${uniqueProducts.length - 6} weitere</span>`
          : '';

        return `<div class="customer-products-summary">${chips}${extra}</div>`;
      }

      function renderCustomerProductsPopup(products, customerName) {
        if (!products || !products.length) {
          return `
                                                                    <div class="swal-products-list">
                                                                      <div class="customer-products-empty">Für ${escapeHtml(customerName)} wurden keine Produkte gefunden.</div>
                                                                    </div>
                                                                  `;
        }

        return `
                                                                  <div class="swal-products-list">
                                                                    ${products.map(product => `
                                                                      <div class="swal-product-card">
                                                                        <div class="swal-product-icon">
                                                                          <svg viewBox="0 0 24 24" width="20" height="20" fill="none">
                                                                            <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                            <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                            <path d="M3 16.5L12 21l9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                          </svg>
                                                                        </div>
                                                                        <div class="swal-product-content">
                                                                          <div class="swal-product-name">${escapeHtml(product.name)}</div>
                                                                          <div class="swal-product-meta">
                                                                            ${product.product_id ? `<span class="swal-product-pill">Produkt-ID: ${escapeHtml(product.product_id)}</span>` : ''}
                                                                            ${product.alternative_id ? `<span class="swal-product-pill">Alternative-ID: ${escapeHtml(product.alternative_id)}</span>` : ''}
                                                                            ${product.city ? `<span class="swal-product-pill">Ort: ${escapeHtml(product.city)}</span>` : ''}
                                                                          </div>
                                                                        </div>
                                                                      </div>
                                                                    `).join('')}
                                                                  </div>
                                                                `;
      }

      function showCustomerProductsPopup(customerName, products = []) {
        Swal.fire({
          title: `Ausgewählte Produkte von ${escapeHtml(customerName || 'Kunde')}`,
          html: renderCustomerProductsPopup(products, customerName || 'Kunde'),
          width: 760,
          confirmButtonText: 'Schließen',
          customClass: {
            popup: 'custom-swal-popup',
            confirmButton: 'custom-confirm-btn'
          }
        });
      }
      function loadCustomerProductsPreview(products = []) {
        const previewEl = document.getElementById('customerProductsPreview');
        if (!previewEl) return;
        previewEl.innerHTML = renderInlineCustomerProducts(products);
      }


      function isAbsenceCalendarType(type) {
        return ["public_holiday", "holiday", "sick", "recurring_leave", "leave_request"].includes(String(type || ""));
      }

      function safeModalValue(value) {
        if (value === null || value === undefined || value === "" || value === "-" || value === "null") return "—";
        if (Array.isArray(value)) return value.length ? escapeHtml(value.join(", ")) : "—";
        return escapeHtml(String(value));
      }

      function formatCalendarDateDE(date) {
        if (!date) return "—";
        const d = date instanceof Date ? date : new Date(date);
        if (Number.isNaN(d.getTime())) return "—";
        return d.toLocaleDateString("de-DE", { day: "2-digit", month: "2-digit", year: "numeric" });
      }

      function formatWeekdaysDE(days) {
        if (!days) return "—";
        let parsed = days;
        if (typeof parsed === "string") {
          try { parsed = JSON.parse(parsed); } catch (_) { parsed = parsed.split(","); }
        }
        if (!Array.isArray(parsed)) return safeModalValue(parsed);
        const map = { 1: "Mo", 2: "Di", 3: "Mi", 4: "Do", 5: "Fr", 6: "Sa", 7: "So", 0: "So" };
        return parsed.map(d => map[parseInt(d, 10)] || d).join(", ") || "—";
      }

      function normalizeRecurringRule(type) {
        const key = String(type || "").toLowerCase();
        const map = {
          weekly: "Wöchentlich",
          monthly: "Monatlich",
          interval: "Intervall",
          one_time: "Einmalig",
          daily: "Täglich",
          yearly: "Jährlich"
        };
        return map[key] || type || "—";
      }

      function buildAbsenceActionButtons(type, cleanId) {
        if (type !== "leave_request" || !IS_ADMIN) return "";
        return `
                                                    <div class="absence-detail-actions">
                                                      <button type="button" class="swal2-confirm swal2-styled" data-leave-action="approve" data-leave-id="${escapeHtml(cleanId)}" style="background:#10b981;border:none;">Genehmigen</button>
                                                      <button type="button" class="swal2-confirm swal2-styled" data-leave-action="reject" data-leave-id="${escapeHtml(cleanId)}" style="background:#f97373;border:none;">Ablehnen</button>
                                                      <button type="button" class="swal2-confirm swal2-styled" data-leave-action="not_responsible" data-leave-id="${escapeHtml(cleanId)}" style="background:#6b7280;border:none;">Nicht zuständig</button>
                                                    </div>
                                                  `;
      }


      function openAppointmentFromRecurringHomeOffice(event) {
        const xp = event.extendedProps || {};
        const date = (xp.recurring_final_date || xp.recurring_original_date || (event.start ? U.isoDate(event.start) : '') || '').split('T')[0];
        const startTime = xp.start_time && xp.start_time !== '00:00:00' ? U.shortHM(xp.start_time) : '09:00';
        const endTime = xp.end_time && xp.end_time !== '23:59:59' ? U.shortHM(xp.end_time) : '17:00';
        const employeeId = xp.employee_id || (xp.employees && xp.employees[0] ? xp.employees[0].employee_id : null);
        const employeeLabel = [xp.employee_name || xp.name, xp.employee_lastname || xp.lastname].filter(Boolean).join(' ') || (employeeId ? `Mitarbeiter #${employeeId}` : '');
        const recurringTitle = cleanRecurringDisplayTitle(xp, event) || xp.recurring_title || event.title || 'Home Office';

        Swal.close();

        const form = document.getElementById('task-store-form');
        if (form) form.reset();
        maResetAppointmentStageFields();

        $('#appointment_id').val('');
        $('.title').text('TERMIN ERSTELLEN');

        $('#name').val(`Termin - ${recurringTitle}`);
        $('#note').val(xp.description && xp.description !== '-' ? xp.description : `Aus Home-Office-Serie erstellt: ${recurringTitle}`);
        $('#appointment_type').val('Home Office');
        $('#execution_type').val('Home Office').trigger('change');
        $('#start_date').val(date);
        $('#end_date').val(date);
        $('#start_time').val(startTime);
        $('#end_time').val(endTime);

        if (employeeId) {
          const $employee = $('#employee');
          if ($employee.length) {
            if ($employee.find(`option[value="${employeeId}"]`).length === 0) {
              $employee.append(new Option(employeeLabel || `Mitarbeiter #${employeeId}`, employeeId, true, true));
            }
            $employee.val([String(employeeId)]).trigger('change');
          }
        }

        // Optional source fields, useful if you later want to link the appointment back to the recurrence.
        const sourceFields = {
          recurring_leave_id: xp.recurring_leave_id || '',
          recurring_original_date: xp.recurring_original_date || date,
          recurring_final_date: xp.recurring_final_date || date,
          source_type: 'recurring_home_office'
        };
        Object.entries(sourceFields).forEach(([name, value]) => {
          let input = form ? form.querySelector(`[name="${name}"]`) : null;
          if (!input && form) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
          }
          if (input) input.value = value;
        });

        if (D.newTaskCard) {
          D.newTaskCard.style.display = 'block';
          D.newTaskCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          $('.new_task_card, .new_task').show();
        }
      }

      function showAbsenceDetailsModal(event) {
        const xp = event.extendedProps || {};
        const type = xp.type || "-";
        const cleanId = String(event.id || "").split("-")[0];

        const employeeName = [xp.employee_name || xp.name, xp.employee_lastname || xp.lastname]
          .filter(Boolean)
          .join(" ") || ((xp.employees || [])[0] ? `${(xp.employees || [])[0].name || ""} ${(xp.employees || [])[0].lastname || ""}`.trim() : "—");

        const typeLabel = normalizeCalendarType(type);
        const modalTitle = type === "recurring_leave"
          ? cleanRecurringDisplayTitle(xp, event)
          : (event.title || typeLabel);
        const startDate = formatCalendarDateDE(event.start);
        const endDate = formatCalendarDateDE(event.end || event.start);
        const startTime = xp.start_time && xp.start_time !== "00:00:00" ? U.shortHM(xp.start_time) : "—";
        const endTime = xp.end_time && xp.end_time !== "23:59:59" ? U.shortHM(xp.end_time) : "—";
        const description = xp.leave_reason || xp.reason || xp.description || "—";
        const status = xp.leave_status || xp.status || "—";
        const allDayLabel = event.allDay ? "Ja" : "Nein";

        const recurringHtml = type === "recurring_leave" ? `
                                                    <div class="absence-detail-section absence-recurring-box">
                                                      <strong>Wiederholungsregel</strong>
                                                      <p>
                                                        Regel: ${safeModalValue(normalizeRecurringRule(xp.recurring_rule_type || xp.recurring_type))}<br>
                                                        Intervall: ${safeModalValue(xp.recurring_interval)}<br>
                                                        Wochentage: ${safeModalValue(formatWeekdaysDE(xp.recurring_weekdays))}<br>
                                                        Tag im Monat: ${safeModalValue(xp.recurring_day_of_month)}<br>
                                                        Serienbeginn: ${safeModalValue(xp.recurring_start_date)}<br>
                                                        Serienende: ${safeModalValue(xp.recurring_end_date)}<br>
                                                        Originaldatum: ${safeModalValue(xp.recurring_original_date)}<br>
                                                        Finales Datum: ${safeModalValue(xp.recurring_final_date)}<br>
                                                        Änderung/Override: ${xp.has_override ? "Ja" : "Nein"}
                                                      </p>
                                                    </div>
                                                  ` : "";

        const cancelledHtml = xp.is_cancelled ? `
                                                    <div class="absence-detail-section absence-cancelled-box">
                                                      <strong>Status</strong>
                                                      <p>Dieser wiederkehrende Eintrag wurde für dieses Datum abgesagt.</p>
                                                    </div>
                                                  ` : "";

        const html = `
                                                    <div class="absence-detail-modal">
                                                      <div class="absence-detail-head">
                                                        <div>
                                                          <span class="absence-detail-kicker">${safeModalValue(typeLabel)}</span>
                                                          <h3>${safeModalValue(modalTitle)}</h3>
                                                        </div>
                                                        <span class="absence-detail-badge ${escapeHtml(String(type))}">${safeModalValue(typeLabel)}</span>
                                                      </div>

                                                      <div class="absence-detail-grid">
                                                        <div class="absence-detail-card"><strong>Mitarbeiter</strong><span>${safeModalValue(employeeName)}</span></div>
                                                        <div class="absence-detail-card"><strong>Typ</strong><span>${safeModalValue(typeLabel)}</span></div>
                                                        <div class="absence-detail-card"><strong>Von</strong><span>${safeModalValue(startDate)}</span></div>
                                                        <div class="absence-detail-card"><strong>Bis</strong><span>${safeModalValue(endDate)}</span></div>
                                                        <div class="absence-detail-card"><strong>Ganztägig</strong><span>${safeModalValue(allDayLabel)}</span></div>
                                                        <div class="absence-detail-card"><strong>Zeit</strong><span>${safeModalValue(startTime)} – ${safeModalValue(endTime)}</span></div>
                                                        <div class="absence-detail-card"><strong>Status</strong><span>${safeModalValue(status)}</span></div>
                                                        <div class="absence-detail-card"><strong>Urlaubstyp</strong><span>${safeModalValue(xp.leave_type)}</span></div>
                                                      </div>

                                                      <div class="absence-detail-section">
                                                        <strong>Grund / Beschreibung</strong>
                                                        <p>${safeModalValue(description)}</p>
                                                      </div>

                                                      ${recurringHtml}
                                                      ${cancelledHtml}
                                                      ${type === 'recurring_leave' && isRecurringHomeOfficeEvent(xp) ? `
                                                        <div class="absence-detail-actions" style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
                                                          <button type="button" class="swal2-confirm swal2-styled" id="createAppointmentFromHomeOfficeBtn" style="background:#93c21c;border:none;">
                                                            <i class="feather icon-calendar"></i> Termin für Home Office erstellen
                                                          </button>
                                                        </div>
                                                      ` : ''}
                                                      ${buildAbsenceActionButtons(type, cleanId)}
                                                    </div>
                                                  `;

        Swal.fire({
          title: "",
          html,
          width: 780,
          showCloseButton: true,
          showConfirmButton: true,
          confirmButtonText: "Schließen",
          customClass: {
            popup: "absence-detail-popup",
            confirmButton: "custom-cancel-btn"
          },
          didOpen: () => {
            const popup = Swal.getPopup();
            if (!popup) return;
            const homeOfficeBtn = popup.querySelector('#createAppointmentFromHomeOfficeBtn');
            if (homeOfficeBtn) {
              homeOfficeBtn.addEventListener('click', () => openAppointmentFromRecurringHomeOffice(event));
            }

            popup.querySelectorAll("[data-leave-action]").forEach(btn => {
              btn.addEventListener("click", async () => {
                const action = btn.getAttribute("data-leave-action");
                try {
                  await handleLeaveActionFromCalendar(cleanId, action);
                } catch (err) {
                  Swal.fire("Fehler", err.message || String(err), "error");
                }
              });
            });
          }
        });
      }

      function showEventDetailsModal(event) {
        const xp = event.extendedProps || {};
        const cleanId = String(event.id || "").split("-")[0];
        const type = xp.type;

        if (isAbsenceCalendarType(type)) {
          showAbsenceDetailsModal(event);
          return;
        }

        const detailUrl = type === "appointment"
          ? ROUTE.appointmentDetails(cleanId)
          : ROUTE.taskDetails(cleanId);

        const ticketUrl = xp.has_ticket && xp.ticket_problem_id
          ? U.makeTicketUrl(xp.ticket_problem_id)
          : null;

        const ticketAnchor = ticketUrl
          ? `
                                                                        <a href="${ticketUrl}"
                                                                          target="_blank"
                                                                          rel="noopener"
                                                                          title="Ticket öffnen"
                                                                          style="margin-left:8px;display:inline-flex;align-items:center;color:#fff">
                                                                            ${TICKETS.svg}
                                                                        </a>
                                                                    `
          : "";



        const hasCustomer = !!xp.customer_id && xp.customer_id !== "Null" && xp.customer_id !== "-";
        const hasContact = !!xp.contact_id && xp.contact_id !== "Null" && xp.contact_id !== "-";
        const customerLink = hasCustomer ? `/new_lead_profile/${xp.customer_id}` : (hasContact ? `/inquiry_show/${xp.contact_id}` : "#");
        const customerIcon = hasCustomer || hasContact ? '<i class="feather icon-users white"></i>' : '<i class="feather icon-user-x white"></i>';
        const priorityIcon = xp.priority === "very high"
          ? '<i class="fa fa-fire warning mr-1"></i>'
          : (xp.priority === "high" ? '<i class="fa fa-bell important mr-1"></i>' : "");
        const reportIcon = xp.report === "1" ? '<i class="feather icon-file-text warning mr-1"></i>' : "";
        const typeIcon = type === "appointment" ? '<i class="feather icon-calendar"></i>' : '<i class="fa fa-tasks"></i>';
        const displayAddress = xp.full_address && xp.full_address !== "-" && xp.full_address !== "null" ? xp.full_address : "-";
        const displayExecutionType = normalizeExecutionType(xp.execution_type);
        const creator = xp.creator || null;

        const creatorFullName = creator
          ? `${creator.name || ''} ${creator.lastname || ''}`.trim()
          : '';

        const creatorImage = creator && creator.image
          ? `/images/employee/${creator.image}`
          : `/images/employee/default-avatar.png`;

        const creatorHtml = creator && creatorFullName
          ? `
                                                                    <div class="mt-2" style="
                                                                      display:flex;
                                                                      align-items:center;
                                                                      gap:10px;
                                                                      padding:10px 12px;
                                                                      border-radius:12px;
                                                                      background:rgba(255,255,255,.08);
                                                                      border:1px solid rgba(255,255,255,.12);
                                                                    ">
                                                                      <img
                                                                        src="${escapeHtml(creatorImage)}"
                                                                        alt="Verfasser"
                                                                        style="
                                                                          width:38px;
                                                                          height:38px;
                                                                          border-radius:50%;
                                                                          object-fit:cover;
                                                                          border:2px solid #93c21c;
                                                                        "
                                                                      >
                                                                      <div style="line-height:1.25;">
                                                                        <div style="font-size:11px;color:#9ca3af;font-weight:700;text-transform:uppercase;">
                                                                          Verfasser
                                                                        </div>
                                                                        <div style="font-size:13px;color:#fff;font-weight:700;">
                                                                          ${escapeHtml(creatorFullName)}
                                                                        </div>
                                                                      </div>
                                                                    </div>
                                                                  `
          : `
                                                                    <div class="mt-2" style="
                                                                      padding:9px 12px;
                                                                      border-radius:12px;
                                                                      background:rgba(255,255,255,.06);
                                                                      color:#9ca3af;
                                                                      font-size:12px;
                                                                    ">
                                                                      <strong>Verfasser:</strong> -
                                                                    </div>
                                                                  `;

        const employeeList = (xp.employees || []).map(e => `
                                                                  <li data-toggle="tooltip" title="${escapeHtml((e.name || '') + ' ' + (e.lastname || ''))}">
                                                                    <img src="/images/employee/${e.image || 'default-avatar.png'}" alt="Avatar" height="30" width="30" class="rounded-circle">
                                                                  </li>
                                                                `).join("");

        const isDeletedAppointment = type === "appointment" && !!xp.is_deleted;
        const actionMenuItems = isDeletedAppointment
          ? `
                                                                      <button type="button" class="swal-calendar-menu-item" data-action="restore" data-event-type="${type}" data-event-id="${cleanId}">
                                                                        <i class="feather icon-rotate-ccw"></i> Wiederherstellen
                                                                      </button>

                                                                      <button type="button" class="swal-calendar-menu-item text-danger" data-action="force-delete" data-event-type="${type}" data-event-id="${cleanId}">
                                                                        <i class="feather icon-trash-2"></i> Endgültig löschen
                                                                      </button>
                                                                    `
          : `
                                                                      <button type="button" class="swal-calendar-menu-item" data-action="duplicate" data-event-id="${cleanId}">
                                                                        <i class="feather icon-copy"></i> Duplizieren
                                                                      </button>

                                                                      <button type="button" class="swal-calendar-menu-item" data-action="edit" data-event-id="${cleanId}">
                                                                        <i class="feather icon-edit"></i> Bearbeiten
                                                                      </button>

                                                                      <button type="button" class="swal-calendar-menu-item text-danger" data-action="delete" data-event-type="${type}" data-event-id="${cleanId}">
                                                                        <i class="feather icon-trash"></i> Löschen
                                                                      </button>
                                                                    `;

        const actionMenu = `
                                                                  <div class="calendar-action-menu">
                                                                    <button
                                                                      type="button"
                                                                      id="swalCalendarMenuBtn"
                                                                      class="swal2-styled"
                                                                      style="
                                                                        background:#2c3e50;
                                                                        color:#fff;
                                                                        border:0;
                                                                        font-weight:800;
                                                                        border-radius:5px;
                                                                        padding:9px 14px;
                                                                        margin:0 5px;
                                                                      "
                                                                    >
                                                                      <i class="feather icon-more-vertical"></i>
                                                                    </button>

                                                                    <div id="swalCalendarMenu" class="swal-calendar-dropdown">
                                                                      ${actionMenuItems}
                                                                    </div>
                                                                  </div>
                                                                `;

        let fallbackProducts = extractCustomerProductsFromEvent(xp);
        const customerDisplayName = `${xp.customerName || ''} ${xp.customerLastname || ''}`.trim() || event.title || 'Kunde';

        const html = `
                                                                  <div class="custom-event">
                                                                    <div class="custom-event-header d-flex align-items-center">
                                                                      <i class="fa ${xp.public !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
                                                                      ${priorityIcon}
                                                                      ${reportIcon}
                                                                      ${ticketAnchor}
                                                                      <span class="custom-event-status-text">
                                                                        ${typeIcon}
                                                                        <i class="feather icon-info warning info_popup" data-id="${cleanId}" data-type="${type}"></i>
                                                                        ${type === "appointment" ? `<i class="feather icon-map show_map" data-id="${cleanId}"></i>` : ""} 
                                                                        <a href="${customerLink}" target="_blank" style="margin-left:8px;">${customerIcon}</a>
                                                                      </span>
                                                                    </div>

                                                                    <div class="custom-event-title mt-1">
                                                                      <a href="${detailUrl}" style="font-size:13px;color:${type === "task" ? "#74b2d4" : "#93c21c"};">
                                                                        ${escapeHtml(xp.description || event.title)}
                                                                      </a>
                                                                      ${xp.appointment_type && xp.appointment_type !== "-" ? `<p style="font-size:12px;color:#fff;"><strong>Typ:</strong> ${escapeHtml(xp.appointment_type)}</p>` : ""}
                                                                      ${displayExecutionType && displayExecutionType !== "-" ? `
                                                                        <p style="font-size:12px;color:#fff;">
                                                                          <strong>Ort des Termins:</strong> ${escapeHtml(displayExecutionType)}
                                                                        </p>
                                                                      ` : ""}
                                                                      <p style="font-size:13px;color:${type === "task" ? "#74b2d4" : "#93c21c"};">
                                                                        <i class="feather icon-calendar"></i> ${new Date(event.end || event.start).toLocaleDateString("de-DE", { day: "numeric", month: "short", year: "numeric" })}
                                                                      </p>
                                                                      <p style="font-size:13px;color:${type === "task" ? "#74b2d4" : "#93c21c"};">
                                                                        <i class="feather icon-clock"></i> ${U.shortHM(xp.start_time)} - ${U.shortHM(xp.end_time)}
                                                                      </p>

                                                                      <div class="swal-stage-box">
                                                                        <label class="ma-stage-label">CRM Stage</label>
                                                                        ${maRenderStagePills(xp, true)}
                                                                      </div>

                                                                      ${creatorHtml}
                                                                    </div>

                                                                    <div class="mt-2">
                                                                      ${xp.phone && xp.phone !== "-" ? `<p style="font-size:13px;"><i class="feather icon-phone"></i> ${escapeHtml(xp.phone)}</p>` : ""}
                                                                      ${xp.email && xp.email !== "-" ? `<p style="font-size:13px;"><i class="feather icon-mail"></i> ${escapeHtml(xp.email)}</p>` : ""}
                                                                      ${displayAddress && displayAddress !== "-" ? `<p style="font-size:13px;"><i class="feather icon-map-pin"></i> ${escapeHtml(displayAddress)}</p>` : ""}
                                                                    </div>

                                                                    <ul class="list-unstyled users-list m-0 d-flex align-items-center mt-3">
                                                                      ${employeeList}
                                                                    </ul>


                                                                    ${hasCustomer ? `
                                                                      <div class="customer-products-box">
                                                                        <div class="customer-products-head">
                                                                          <div class="customer-products-title">
                                                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                                                                              <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                              <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                              <path d="M3 16.5L12 21l9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                            </svg>
                                                                            Kundenprodukte
                                                                          </div>

                                                                          <button
                                                                            type="button"
                                                                            class="customer-products-badge"
                                                                            id="openCustomerProductsBtn"
                                                                            data-customer-id="${escapeHtml(xp.customer_id)}"
                                                                            data-customer-name="${escapeHtml(customerDisplayName)}"
                                                                          >
                                                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none">
                                                                              <path d="M3 7.5L12 3l9 4.5-9 4.5L3 7.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                              <path d="M3 12l9 4.5 9-4.5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                                            </svg>
                                                                            Alle Produkte anzeigen
                                                                          </button>
                                                                        </div>

                                                                        <div id="customerProductsPreview">
                                                                          ${renderInlineCustomerProducts(fallbackProducts)}
                                                                        </div>
                                                                      </div>
                                                                    ` : ""}
                                                                  </div>`;

        const ticketFooterButton = ticketUrl
          ? `
                                                                          <a href="${ticketUrl}"
                                                                            target="_blank"
                                                                            rel="noopener"
                                                                            class="swal2-confirm swal2-styled"
                                                                            style="
                                                                                background:#f8ac00;
                                                                                color:#111827;
                                                                                font-weight:800;
                                                                                text-decoration:none;
                                                                                display:inline-flex;
                                                                                align-items:center;
                                                                                gap:7px;
                                                                            ">
                                                                              ${TICKETS.svg}
                                                                              Ticket ansehen
                                                                          </a>
                                                                      `
          : "";
        Swal.fire({
          title: event.title,
          html,
          showCloseButton: true,

          confirmButtonText: "abbrechen",
          cancelButtonText: "weitere Details anzeigen",
          showCancelButton: true,

          confirmButtonColor: "#d92127",
          cancelButtonColor: "#93c21c",

          customClass: {
            popup: "custom-swal-popup",
            confirmButton: "custom-confirm-btn",
            cancelButton: "custom-cancel-btn"
          },
          didOpen: async () => {
            $('[data-toggle="tooltip"]').tooltip();

            const popup = Swal.getPopup();
            if (popup) popup.style.background = "#2c3e50";

            const actions = Swal.getActions();

            if (actions && !document.getElementById("swalCalendarMenuBtn")) {
              const menuWrap = document.createElement("div");
              menuWrap.innerHTML = actionMenu.trim();

              const menuElement = menuWrap.firstElementChild;
              const cancelBtn = Swal.getCancelButton();

              if (cancelBtn) {
                cancelBtn.insertAdjacentElement("afterend", menuElement);
              } else {
                actions.appendChild(menuElement);
              }
            }

            if (actions && ticketUrl && !document.getElementById("swalTicketFooterBtn")) {
              const ticketBtn = document.createElement("a");
              ticketBtn.id = "swalTicketFooterBtn";
              ticketBtn.href = ticketUrl;
              ticketBtn.target = "_blank";
              ticketBtn.rel = "noopener";
              ticketBtn.className = "swal-ticket-footer-btn";
              ticketBtn.innerHTML = `${TICKETS.svg} Ticket ansehen`;

              ticketBtn.addEventListener("click", function (e) {
                e.stopPropagation();
              });

              actions.appendChild(ticketBtn);
            }

            const menuBtn = document.getElementById("swalCalendarMenuBtn");
            const menu = document.getElementById("swalCalendarMenu");

            if (menuBtn && menu) {
              menuBtn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                menu.style.display = menu.style.display === "block" ? "none" : "block";
              });

              menu.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
              });

              document.addEventListener("click", function closeMenuOutside(e) {
                if (!Swal.isVisible()) {
                  document.removeEventListener("click", closeMenuOutside);
                  return;
                }

                if (!menu.contains(e.target) && !menuBtn.contains(e.target)) {
                  menu.style.display = "none";
                }
              });
            }

            document.querySelectorAll(".swal-calendar-menu-item").forEach(btn => {
              btn.addEventListener("click", async function (e) {
                e.preventDefault();
                e.stopPropagation();

                const action = this.getAttribute("data-action");
                const eventId = this.getAttribute("data-event-id");
                const eventType = this.getAttribute("data-event-type");

                if (action === "duplicate") {
                  Swal.close();

                  const r = await Swal.fire({
                    title: "Duplizieren auf neues Datum",
                    input: "date",
                    inputLabel: "Wähle ein Datum",
                    inputAttributes: {
                      min: new Date().toISOString().split("T")[0]
                    },
                    showCancelButton: true,
                    confirmButtonText: "Duplizieren",
                    cancelButtonText: "Abbrechen",
                    inputValidator: value => {
                      if (!value) return "Datum ist erforderlich!";
                    }
                  });

                  if (!r.isConfirmed) return;

                  try {
                    const res = await U.send(
                      "POST",
                      ROUTE.duplicateAppointment,
                      new URLSearchParams({
                        appointment_id: eventId,
                        new_date: r.value
                      })
                    );

                    Swal.fire("Erfolgreich!", res.message || "Dupliziert", "success");

                    loadCalendarTasks(() => {
                      S.fc.gotoDate(res?.data?.start_date || r.value);
                    });
                  } catch (err) {
                    console.error(err);
                    Swal.fire("Fehler!", "Unbekannter Serverfehler", "error");
                  }

                  return;
                }

                if (action === "edit") {
                  Swal.close();

                  try {
                    const data = await U.getJSON(ROUTE.fetchMainAppointment(eventId));

                    $(".new_task_card").show();
                    $(".title").text("TERMIN BEARBEITEN");

                    $("#appointment_id").val(data.id);
                    $("#name").val(data.name ?? "");
                    $("#note").val(data.note ?? "");
                    $("#color").val(data.color ?? "").trigger("change");
                    $("#colorIcon").css("color", data.color ?? "#000");

                    $("#appointment_type").val(data.appointment_type ?? "");
                    $("#execution_type").val(data.execution_type ?? "").trigger("change");
                    $("#priority").val(data.priority ?? "").trigger("change");
                    $("#date_type").val(data.date_type ?? "").trigger("change");
                    $("#repeat").val(data.repeat ?? "").trigger("change");

                    $("#start_date").val(data.start_date ?? "");
                    $("#end_date").val(data.end_date ?? "");
                    $("#start_time").val(data.start_time ?? "");
                    $("#end_time").val(data.end_time ?? "");
                    $("#total_time").val(data.total_time ?? "");

                    $("#reminder_date").val(data.reminder_date ?? "");
                    $("#reminder_time").val(data.reminder_time ?? "");

                    if (data.next_step) {
                      if (!$(`#next_step option[value="${data.next_step}"]`).length) {
                        $("#next_step").append(new Option(data.next_step, data.next_step, true, true));
                      }

                      $("#next_step").val(data.next_step).trigger("change");
                    } else {
                      $("#next_step").val("").trigger("change");
                    }

                    try {
                      const responsible = Array.isArray(data.responsible_report)
                        ? data.responsible_report
                        : JSON.parse(data.responsible_report || "[]");

                      $("#report_responsible").val(responsible).trigger("change");
                    } catch {
                      $("#report_responsible").val([]).trigger("change");
                    }

                    maSetAppointmentStageFields(data);

                    setAppointmentAddressFromOption({
                      label: "Terminadresse",
                      source_type: data.address_source_type || "",
                      source_id: data.address_source_id || "",
                      alternative_id: data.alternative_id ?? data.selected_alternative_id ?? "",
                      street: data.street ?? "",
                      postcode: data.postcode ?? "",
                      city: data.city ?? "",
                      latitude: data.latitude ?? "",
                      longitude: data.longitude ?? "",
                      full_address: data.full_address ?? ""
                    });

                    $("#phone").val(data.phone ?? "");
                    $("#email").val(data.email ?? "");
                    $("#link").val(data.link ?? "");
                    $("#contact_type").val(data.contact_type ?? "");
                    $("#description").val(data.description ?? "");

                    const mode = data.contact_mode || "new";
                    $("#contact_mode").val(mode);

                    $(`input.contact-type-toggle[value="${mode}"]`)
                      .prop("checked", true)
                      .trigger("change");

                    if (mode === "select" && data.customer_id) {
                      const $cl = $("#customer_id");
                      const label = data.customer_name || data.contact_name || data.name || `Kunde #${data.customer_id}`;

                      if ($cl.find(`option[value="${data.customer_id}"]`).length === 0) {
                        $cl.append(new Option(label, data.customer_id, true, true));
                      }

                      $cl.val(String(data.customer_id)).trigger("change");
                    }

                    const toBool = v => String(v ?? 0) === "1" || v === true || v === 1;

                    $("#switchPublic").prop("checked", toBool(data.public));
                    $("#switchReport").prop("checked", toBool(data.is_report));

                    const hasInquiry = Array.isArray(data.product_inquiry) && data.product_inquiry.length > 0;
                    $("#switchContact").prop("checked", hasInquiry);

                    if (hasInquiry) {
                      $("#switchContact").trigger("change");
                    } else {
                      $("#appointmentWrapper").hide();
                      $("#pre_type").val("").trigger("change");
                      $("#source").val("").trigger("change");
                    }

                    $("#pre_type").val(data.pre_type ?? "").trigger("change");

                    if (data.source) {
                      if (!$(`#source option[value="${data.source}"]`).length) {
                        $("#source").append(new Option(data.source, data.source, true, true));
                      }

                      $("#source").val(data.source).trigger("change");
                    } else {
                      $("#source").val("").trigger("change");
                    }

                    $("#branch_id").val(data.branch_id ?? "").trigger("change");
                    $("#branch_address_id").val(data.branch_address_id ?? "").trigger("change");
                    $("#employee").val(data.employee_ids ?? []).trigger("change");

                    let parsed = data.product_json ?? data.products ?? null;

                    try {
                      if (typeof parsed === "string") parsed = JSON.parse(parsed);
                      if (typeof parsed === "string") parsed = JSON.parse(parsed);
                    } catch (_) {
                      parsed = null;
                    }

                    let ids = [];

                    if (Array.isArray(parsed)) {
                      ids = parsed
                        .map(item => item.uid || item.id || (item.lead_product_list_id ? `lpl_${item.lead_product_list_id}` : `${item.name}_${item.alternative_id}`))
                        .filter(Boolean);

                      $("#products").val(JSON.stringify(parsed));
                    } else if (parsed && typeof parsed === "object") {
                      ids = Object.entries(parsed)
                        .map(([name, tuple]) => `${name}_${tuple?.[0]}`)
                        .filter(Boolean);

                      const converted = Object.entries(parsed).map(([name, tuple]) => ({
                        uid: `${name}_${tuple?.[0]}`,
                        name: name || "",
                        alternative_id: tuple?.[0] || null,
                        product_id: tuple?.[1] || null,
                        customer_id: tuple?.[2] || null,
                        city: null
                      }));

                      $("#products").val(JSON.stringify(converted));
                    }

                    loadCustomerProducts(data.customer_id, ids.length ? ids : undefined);

                    if (
                      String(data.is_contact) === "1" &&
                      Array.isArray(data.product_inquiry) &&
                      data.product_inquiry.length > 0 &&
                      window.prefillInquiryFromSnapshot
                    ) {
                      window.prefillInquiryFromSnapshot(data.product_inquiry);
                    }
                  } catch (err) {
                    console.error(err);
                    Swal.fire("Fehler", "Der Termin konnte nicht geladen werden.", "error");
                  }

                  return;
                }

                if (action === "restore") {
                  if (eventType !== "appointment") return;

                  const view = S.fc.view.type;
                  const date = S.fc.getDate();

                  const confirm = await Swal.fire({
                    title: "Termin wiederherstellen?",
                    text: "Der Termin wird zurück in den aktiven Kalender verschoben.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ja, wiederherstellen",
                    cancelButtonText: "Abbrechen",
                    confirmButtonColor: "#93c21c",
                    cancelButtonColor: "#64748b"
                  });

                  if (!confirm.isConfirmed) return;

                  try {
                    const r = await fetch(ROUTE.restoreAppointment(eventId), {
                      method: "POST",
                      headers: {
                        "X-CSRF-TOKEN": CSRF(),
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                      }
                    });
                    const d = await r.json();

                    if (d.status === "success" || d.success === true) {
                      Swal.fire({ icon: "success", title: "Wiederhergestellt", text: "Der Termin wurde wiederhergestellt.", timer: 1500, showConfirmButton: false });
                      loadCalendarTasks(() => { S.fc.changeView(view); S.fc.gotoDate(date); });
                    } else {
                      Swal.fire({ icon: "error", title: "Fehler", text: d.message || "Der Termin konnte nicht wiederhergestellt werden." });
                    }
                  } catch (err) {
                    console.error(err);
                    Swal.fire({ icon: "error", title: "Fehler", text: "Beim Wiederherstellen ist ein Fehler aufgetreten." });
                  }

                  return;
                }

                if (action === "force-delete") {
                  if (eventType !== "appointment") return;

                  const view = S.fc.view.type;
                  const date = S.fc.getDate();

                  const confirm = await Swal.fire({
                    title: "Termin endgültig löschen?",
                    text: "Diese Aktion löscht den Termin dauerhaft und kann nicht rückgängig gemacht werden.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ja, endgültig löschen",
                    cancelButtonText: "Abbrechen",
                    confirmButtonColor: "#dc2626",
                    cancelButtonColor: "#93c21c"
                  });

                  if (!confirm.isConfirmed) return;

                  try {
                    const r = await fetch(ROUTE.forceDeleteAppointment(eventId), {
                      method: "DELETE",
                      headers: {
                        "X-CSRF-TOKEN": CSRF(),
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                      }
                    });
                    const d = await r.json();

                    if (d.status === "success" || d.success === true) {
                      Swal.fire({ icon: "success", title: "Endgültig gelöscht", text: "Der Termin wurde dauerhaft gelöscht.", timer: 1500, showConfirmButton: false });
                      loadCalendarTasks(() => { S.fc.changeView(view); S.fc.gotoDate(date); });
                    } else {
                      Swal.fire({ icon: "error", title: "Fehler", text: d.message || "Der Termin konnte nicht endgültig gelöscht werden." });
                    }
                  } catch (err) {
                    console.error(err);
                    Swal.fire({ icon: "error", title: "Fehler", text: "Beim endgültigen Löschen ist ein Fehler aufgetreten." });
                  }

                  return;
                }

                if (action === "delete") {
                  if (["holiday", "sick", "public_holiday", "recurring_leave", "leave_request"].includes(eventType)) {
                    Swal.fire({
                      icon: "warning",
                      title: "Löschen nicht erlaubt",
                      text: "Dieser Termin kann nicht gelöscht werden.",
                      confirmButtonColor: "#d92127"
                    });

                    return;
                  }

                  const deleteUrl = eventType === "appointment"
                    ? ROUTE.deleteAppointment(eventId)
                    : ROUTE.deleteTask(eventId);

                  const view = S.fc.view.type;
                  const date = S.fc.getDate();

                  const confirm = await Swal.fire({
                    title: "Termin wirklich löschen?",
                    text: "Diese Aktion kann nicht rückgängig gemacht werden.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ja, löschen",
                    cancelButtonText: "Abbrechen",
                    confirmButtonColor: "#d92127",
                    cancelButtonColor: "#93c21c"
                  });

                  if (!confirm.isConfirmed) return;

                  try {
                    const r = await fetch(deleteUrl, {
                      method: "DELETE",
                      headers: {
                        "X-CSRF-TOKEN": CSRF(),
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                      }
                    });

                    const d = await r.json();

                    if (d.status === "success") {
                      Swal.fire({
                        icon: "success",
                        title: "Gelöscht",
                        text: "Der Termin wurde erfolgreich gelöscht.",
                        timer: 1500,
                        showConfirmButton: false
                      });

                      loadCalendarTasks(() => {
                        S.fc.changeView(view);
                        S.fc.gotoDate(date);
                      });
                    } else {
                      Swal.fire({
                        icon: "error",
                        title: "Fehler",
                        text: d.message || "Der Termin konnte nicht gelöscht werden."
                      });
                    }
                  } catch (err) {
                    console.error(err);
                    Swal.fire({
                      icon: "error",
                      title: "Fehler",
                      text: "Beim Löschen ist ein Fehler aufgetreten."
                    });
                  }

                  return;
                }
              });
            });

            if (hasCustomer) {
              fallbackProducts = await fetchSelectedProductsForEvent(cleanId, xp);
              loadCustomerProductsPreview(fallbackProducts);

              const btn = document.getElementById("openCustomerProductsBtn");
              if (btn) {
                btn.addEventListener("click", function () {
                  showCustomerProductsPopup(
                    this.getAttribute("data-customer-name"),
                    fallbackProducts
                  );
                });
              }
            }
          }
        }).then(res => {
          if (res.dismiss === Swal.DismissReason.cancel) {
            window.location.href = detailUrl;
          }
        });
      }
      async function handleLeaveActionFromCalendar(id, action) {
        // reject needs reason textarea
        if (action === 'reject') {
          const res = await Swal.fire({
            title: 'Urlaub ablehnen',
            html: '<textarea id="leave_reject_reason" class="swal2-textarea" placeholder="Bitte Grund angeben"></textarea>',
            showCancelButton: true,
            confirmButtonText: 'Ablehnen',
            cancelButtonText: 'Abbrechen',
            preConfirm: () => {
              const txt = document.getElementById('leave_reject_reason').value.trim();
              if (!txt) {
                Swal.showValidationMessage('Bitte einen Grund eingeben.');
                return false;
              }
              return txt;
            }
          });

          if (!res.isConfirmed) return;

          const noteText = res.value;

          const { ok, json } = await U.postJSON('/my/mark-done', {
            id,
            type: 'leave',
            action: 'reject',
            note_text: noteText,
          });

          if (!ok || json?.success === false) {
            throw new Error(json?.message || 'Fehler beim Ablehnen des Urlaubs.');
          }

          Swal.fire('Abgelehnt', 'Der Urlaubsantrag wurde abgelehnt.', 'success');
          const view = S.fc.view.type;
          const date = S.fc.getDate();
          await loadCalendarTasks(() => {
            S.fc.changeView(view);
            S.fc.gotoDate(date);
          });
          return;
        }

        // approve / not_responsible
        const { ok, json } = await U.postJSON('/my/mark-done', {
          id,
          type: 'leave',
          action,
        });

        if (!ok || json?.success === false) {
          throw new Error(json?.message || 'Fehler beim Aktualisieren des Urlaubs.');
        }

        let msg = 'Urlaubsantrag aktualisiert.';
        if (action === 'approve') msg = 'Urlaub genehmigt.';
        if (action === 'not_responsible') msg = 'Antrag aus deiner Zuständigkeit entfernt.';

        Swal.fire('Erfolg', msg, 'success');

        const view = S.fc.view.type;
        const date = S.fc.getDate();
        await loadCalendarTasks(() => {
          S.fc.changeView(view);
          S.fc.gotoDate(date);
        });
      }

      function showLeaveRequestModal(event) {
        const xp = event.extendedProps || {};
        const cleanId = String(event.id || '').split('-')[0];

        const employees = xp.employees || [];
        const empLabel = employees.length
          ? `${employees[0].name || ''} ${employees[0].lastname || ''}`.trim()
          : 'Mitarbeiter';

        const leaveType = xp.leave_type || 'Urlaub';
        const reason = xp.description || xp.leave_reason || '-';
        const isAdmin = IS_ADMIN;

        const start = event.start ? new Date(event.start) : null;
        const end = event.end ? new Date(event.end) : start;

        const dateRange = start
          ? `${start.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' })}${end
            ? ' – ' + end.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' })
            : ''
          }`
          : '-';

        const svgIcon = `
                                                                  <svg width="40" height="40" viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path fill="#8fc73e" d="M12 2C8 2 4.7 4.7 3.6 8.4c-.2.6.2 1.1.8 1.1H11v11a1 1 0 0 0 2 0V9.5h6.6c.6 0 1-.5.8-1.1C19.3 4.7 16 2 12 2z"/>
                                                                    <path fill="#4b5563" d="M10 22h4v1a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-1z"/>
                                                                  </svg>
                                                                `;

        const adminButtonsHtml = isAdmin
          ? `
                                                                    <div class="mt-3" style="display:flex; justify-content:flex-end; gap:0.5rem; flex-wrap:wrap;">
                                                                      <button type="button" class="swal2-confirm swal2-styled"
                                                                              data-leave-action="approve"
                                                                              style="background:#10b981;border:none;">
                                                                        Genehmigen
                                                                      </button>
                                                                      <button type="button" class="swal2-confirm swal2-styled"
                                                                              data-leave-action="reject"
                                                                              style="background:#f97373;border:none;">
                                                                        Ablehnen
                                                                      </button>
                                                                      <button type="button" class="swal2-confirm swal2-styled"
                                                                              data-leave-action="not_responsible"
                                                                              style="background:#6b7280;border:none;">
                                                                        Nicht zuständig
                                                                      </button>
                                                                    </div>
                                                                  `
          : '';

        const html = `
                                                                  <div style="display:flex; align-items:flex-start; gap:1rem;">
                                                                    <div>${svgIcon}</div>
                                                                    <div style="flex:1; min-width:0;">
                                                                      <div style="font-size:14px; font-weight:bold; margin-bottom:0.25rem;">
                                                                        ${leaveType} – ${empLabel}
                                                                      </div>
                                                                      <div style="font-size:13px; margin-bottom:0.25rem;">
                                                                        <i class="feather icon-calendar"></i>
                                                                        <strong> Zeitraum:</strong> ${dateRange}
                                                                      </div>
                                                                      <div style="font-size:13px; margin-bottom:0.25rem;">
                                                                        <i class="feather icon-info"></i>
                                                                        <strong> Grund:</strong> ${reason || '-'}
                                                                      </div>
                                                                      ${xp.leave_status
            ? `<div style="font-size:12px; color:#9ca3af;">
                                                                              Status: ${xp.leave_status}
                                                                            </div>`
            : ''
          }
                                                                    </div>
                                                                  </div>
                                                                  ${adminButtonsHtml}
                                                                `;

        Swal.fire({
          title: 'Urlaubsantrag',
          html,
          showCloseButton: true,
          showConfirmButton: !isAdmin,
          confirmButtonText: 'Schließen',
          background: '#2c3e50',
          customClass: {
            popup: 'custom-swal-popup',
            confirmButton: 'custom-cancel-btn',
          },
          didOpen: () => {
            if (!isAdmin) return;
            const popup = Swal.getPopup();
            popup.querySelectorAll('[data-leave-action]').forEach(btn => {
              btn.addEventListener('click', async () => {
                const action = btn.getAttribute('data-leave-action');
                try {
                  await handleLeaveActionFromCalendar(cleanId, action);
                } catch (err) {
                  Swal.fire('Fehler', err.message || String(err), 'error');
                }
              });
            });
          },
        });
      }

      function showDayEventsModal(events, date) {
        const dateLabel = new Date(date).toLocaleDateString("de-DE", { weekday: "long", day: "numeric", month: "long" });
        const html = events.map(ev => {
          const title = ev.title || "-";
          const start = ev.start ? new Date(ev.start).toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" }) : "-";
          const color = ev.backgroundColor || "#ccc";
          return `<div class="clickable-event" data-event-id="${ev.id}" style="border-left:5px solid ${color};padding:5px 10px;margin-bottom:5px;cursor:pointer;"><strong>${title}</strong><br><small>${start}</small></div>`;
        }).join("");

        Swal.fire({
          title: `Alle Termine am ${dateLabel}`, html, showCloseButton: true, confirmButtonText: "Schließen", width: "600px", background: "#f9f9f9",
          didOpen: () => {
            U.qa(".clickable-event").forEach(el => el.addEventListener("click", function () {
              const id = this.getAttribute("data-event-id");
              let clicked = S.fc.getEventById(id);
              if (!clicked && id.includes("-")) {
                const base = id.split("-")[0];
                clicked = S.fc.getEvents().find(e => e.id && e.id.toString().split("-")[0] === base);
              }
              if (clicked) { Swal.close(); setTimeout(() => showEventDetailsModal(clicked), 100); }
            }));
          }
        });
      }

      // =========================
      // CRUD: drag/resize, delete, duplicate, edit
      // =========================
      function handleEventUpdate(info) {
        const t = info.event.extendedProps.type;
        if (["public_holiday", "holiday", "sick", "leave_request", "recurring_leave"].includes(t)) return info.revert();

        Swal.fire({
          title: "Geben Sie einen Grund für die Änderung an",
          html: `<textarea id="change_reason" class="swal2-textarea" placeholder="Geben Sie einen Grund für die Änderung an"></textarea>`,
          showCancelButton: true, confirmButtonText: "Speichern", cancelButtonText: "Abbrechen",
          preConfirm: () => {
            const txt = document.getElementById("change_reason").value.trim();
            if (!txt) Swal.showValidationMessage("Änderungsgrund ist erforderlich."); return txt;
          }
        }).then(async (r) => {
          if (!r.isConfirmed) return info.revert();
          const taskId = info.event.id.split("-")[0];
          const start = new Date(info.event.start);
          const end = info.event.end ? new Date(info.event.end) : start;

          const { ok, json } = await U.postJSON(ROUTE.changeAppointment, {
            task_id: taskId,
            emp_personal_id: info.event.extendedProps.emp_personal_id || null,
            start_date: U.isoDate(start),
            end_date: U.isoDate(end),
            start_time: `${U.pad2(start.getHours())}:${U.pad2(start.getMinutes())}`,
            end_time: `${U.pad2(end.getHours())}:${U.pad2(end.getMinutes())}`,
            change_reason: r.value,
            type: t,
          });

          if (ok && json?.success) {
            Swal.fire("Success!", "Veranstaltung erfolgreich aktualisiert.", "success").then(loadCalendarTasks);
          } else {
            Swal.fire("Error!", json?.message || "Failed to update event.", "error");
            info.revert();
          }
        });
      }

      document.addEventListener("click", async (e) => {
        const btn = e.target.closest("#delete_event"); if (!btn) return;
        e.preventDefault();
        const id = btn.getAttribute("data-event-id");
        const type = btn.getAttribute("data-event-type");

        if (["holiday", "sick", "public_holiday", "recurring_leave", "leave_request"].includes(type)) {
          Swal.fire({ icon: "warning", title: "Löschen nicht erlaubt", text: "Dieser Termin kann nicht gelöscht werden.", confirmButtonColor: "#d92127" });
          return;
        }

        const url = type === "appointment" ? ROUTE.deleteAppointment(id) : ROUTE.deleteTask(id);
        const view = S.fc.view.type, date = S.fc.getDate();

        const res = await Swal.fire({
          title: "Termin wirklich löschen?", text: "Der Termin wird in den Papierkorb verschoben.", icon: "warning",
          showCancelButton: true, confirmButtonText: "Ja, löschen", cancelButtonText: "Abbrechen",
          confirmButtonColor: "#d92127", cancelButtonColor: "#93c21c",
        });
        if (!res.isConfirmed) return;

        try {
          const r = await fetch(url, { method: "DELETE", headers: { "X-CSRF-TOKEN": CSRF(), "Content-Type": "application/json" } });
          const d = await r.json();
          if (d.status === "success") {
            loadCalendarTasks(() => { S.fc.changeView(view); S.fc.gotoDate(date); });
            Swal.fire({ icon: "success", title: "Gelöscht", text: "Der Termin wurde in den Papierkorb verschoben.", timer: 1500, showConfirmButton: false });
          } else {
            Swal.fire({ icon: "error", title: "Fehler", text: "Der Termin konnte nicht gelöscht werden." });
          }
        } catch {
          Swal.fire({ icon: "error", title: "Fehler", text: "Beim Löschen ist ein Fehler aufgetreten." });
        }
      });

      $(document).on("click", ".duplicate-event", async function (e) {
        e.preventDefault();
        const id = $(this).data("event-id");
        const r = await Swal.fire({
          title: "Duplizieren auf neues Datum", input: "date", inputLabel: "Wähle ein Datum",
          inputAttributes: { min: new Date().toISOString().split("T")[0] },
          showCancelButton: true, confirmButtonText: "Duplizieren", cancelButtonText: "Abbrechen",
          inputValidator: (v) => (!v ? "Datum ist erforderlich!" : undefined),
        });
        if (!r.isConfirmed) return;
        try {
          const res = await U.send("POST", ROUTE.duplicateAppointment, new URLSearchParams({ appointment_id: id, new_date: r.value }));
          Swal.fire("Erfolgreich!", res.message || "Dupliziert", "success");
          loadCalendarTasks(() => S.fc.gotoDate(res?.data?.start_date || r.value));
        } catch {
          Swal.fire("Fehler!", "Unbekannter Serverfehler", "error");
        }
      });

      $(document).on("click", ".edit-event", async function (e) {
        e.preventDefault(); Swal.close();
        const id = $(this).data("event-id");

        const data = await U.getJSON(ROUTE.fetchMainAppointment(id));
        $(".new_task_card").show(); $(".title").text("TERMIN BEARBEITEN");

        // Basics
        $("#appointment_id").val(data.id);
        $("#name").val(data.name ?? ""); $("#note").val(data.note ?? "");
        $("#color").val(data.color ?? "").trigger("change"); $("#colorIcon").css("color", data.color ?? "#000");

        // Selects
        $("#appointment_type").val(data.appointment_type ?? "");
        $("#execution_type").val(data.execution_type ?? "").trigger("change");
        $("#priority").val(data.priority ?? "").trigger("change");
        $("#date_type").val(data.date_type ?? "").trigger("change");
        $("#repeat").val(data.repeat ?? "").trigger("change");

        // Dates/Times
        $("#start_date").val(data.start_date ?? ""); $("#end_date").val(data.end_date ?? "");
        $("#start_time").val(data.start_time ?? ""); $("#end_time").val(data.end_time ?? "");
        $("#total_time").val(data.total_time ?? "");

        // Reminder & next steps
        $("#reminder_date").val(data.reminder_date ?? ""); $("#reminder_time").val(data.reminder_time ?? "");
        if (data.next_step) {
          if (!$(`#next_step option[value="${data.next_step}"]`).length) $("#next_step").append(new Option(data.next_step, data.next_step, true, true));
          $("#next_step").val(data.next_step).trigger("change");
        } else { $("#next_step").val("").trigger("change"); }

        try {
          const responsible = Array.isArray(data.responsible_report) ? data.responsible_report : JSON.parse(data.responsible_report || "[]");
          $("#report_responsible").val(responsible).trigger("change");
        } catch { $("#report_responsible").val([]).trigger("change"); }

        maSetAppointmentStageFields(data);

        // Address
        setAppointmentAddressFromOption({
          label: "Terminadresse",
          source_type: data.address_source_type || "",
          source_id: data.address_source_id || "",
          alternative_id: data.alternative_id ?? data.selected_alternative_id ?? "",
          street: data.street ?? "",
          postcode: data.postcode ?? "",
          city: data.city ?? "",
          latitude: data.latitude ?? "",
          longitude: data.longitude ?? "",
          full_address: data.full_address ?? ""
        });

        // Contact
        $("#phone").val(data.phone ?? ""); $("#email").val(data.email ?? ""); $("#link").val(data.link ?? "");
        $("#contact_type").val(data.contact_type ?? ""); $("#description").val(data.description ?? "");
        // ---- contact mode -> select correct radio + trigger UI ----
        const mode = (data.contact_mode || "new"); // "new" | "select" | "ticket"
        $("#contact_mode").val(mode);

        // your UI toggle listens to input.contact-type-toggle change:
        $(`input.contact-type-toggle[value="${mode}"]`)
          .prop("checked", true)
          .trigger("change");

        // ---- preselect customer (only if mode=select) ----
        if (mode === "select" && data.customer_id) {
          const $cl = $("#customer_id"); // this is your .contact_list select
          const label = data.customer_name || data.contact_name || data.name || `Kunde #${data.customer_id}`;

          if ($cl.find(`option[value="${data.customer_id}"]`).length === 0) {
            $cl.append(new Option(label, data.customer_id, true, true));
          }
          $cl.val(String(data.customer_id)).trigger("change");
        }


        const toBool = (v) => String(v ?? 0) === "1" || v === true || v === 1;

        $("#switchPublic").prop("checked", toBool(data.public));
        const hasInquiry =
          Array.isArray(data.product_inquiry) && data.product_inquiry.length > 0;

        const wantsInquiry = hasInquiry; // only open if snapshot exists

        $("#switchContact").prop("checked", wantsInquiry);

        // if your UI needs to react, trigger change only when true
        if (wantsInquiry) {
          $("#switchContact").trigger("change");
        } else {
          // hard-close inquiry UI (adjust selector to your wrapper)
          $("#appointmentWrapper").hide();
          $("#pre_type").val("").trigger("change");
          $("#source").val("").trigger("change");
        }

        $("#switchReport").prop("checked", toBool(data.is_report));

        $("#pre_type").val(data.pre_type ?? "").trigger("change");

        if (data.source) {
          if (!$(`#source option[value="${data.source}"]`).length) $("#source").append(new Option(data.source, data.source, true, true));
          $("#source").val(data.source).trigger("change");
        } else { $("#source").val("").trigger("change"); }

        $("#branch_id").val(data.branch_id ?? "").trigger("change");
        $("#branch_address_id").val(data.branch_address_id ?? "").trigger("change");
        $("#employee").val(data.employee_ids ?? []).trigger("change");
        $("#change_date").val(data.change_date ?? ""); $("#change_reason").val(data.change_reason ?? "");

        $(".audit-info").html(`
                                                                  <div>Erstellt von: <strong>${data.created_by_name ?? "-"}</strong></div>
                                                                  <div>Geändert von: <strong>${data.changed_by_name ?? "-"}</strong></div>
                                                                  <div>Erstellt am: ${data.created_at ?? "-"} | Geändert am: ${data.updated_at ?? "-"}</div>`);


        // Products (prefer product_json from fetch; fallback to products)
        let parsed = data.product_json ?? data.products ?? null;

        try {
          if (typeof parsed === "string") parsed = JSON.parse(parsed);
          if (typeof parsed === "string") parsed = JSON.parse(parsed);
        } catch (_) {
          parsed = null;
        }

        let ids = [];

        if (Array.isArray(parsed)) {
          ids = parsed
            .map(item => item.uid || item.id || (item.lead_product_list_id ? `lpl_${item.lead_product_list_id}` : `${item.name}_${item.alternative_id}`))
            .filter(Boolean);

          $("#products").val(JSON.stringify(parsed));
        } else if (parsed && typeof parsed === "object") {
          // backward compatibility with old saved object format
          ids = Object.entries(parsed)
            .map(([name, tuple]) => `${name}_${tuple?.[0]}`)
            .filter(Boolean);

          const converted = Object.entries(parsed).map(([name, tuple]) => ({
            uid: `${name}_${tuple?.[0]}`,
            name: name || '',
            alternative_id: tuple?.[0] || null,
            product_id: tuple?.[1] || null,
            customer_id: tuple?.[2] || null,
            city: null
          }));

          $("#products").val(JSON.stringify(converted));
        }

        loadCustomerProducts(data.customer_id, ids.length ? ids : undefined);

        // Inquiry snapshot
        const isInquiry = String(data.is_contact) === "1";
        const hasInquirySnapshot =
          Array.isArray(data.product_inquiry) && data.product_inquiry.length > 0;

        if (isInquiry && hasInquirySnapshot && window.prefillInquiryFromSnapshot) {
          window.prefillInquiryFromSnapshot(data.product_inquiry);
        }
      });

      // =========================
      // Filter by date (from mini)
      // =========================
      async function filterMainCalendarByDate(date) {
        if (!date) return;

        const employeeData = getSelectedEmployeeData();

        try {
          const res = await U.getJSON(ROUTE.getCalendar, {
            employee_data: JSON.stringify(employeeData),
            search: S.currentSearch || "",
            filter_date: date,
            start: date,
            end: date,
            include_all_absences: S.showDeletedAppointmentsOnly ? 0 : 1,
            only_deleted_appointments: S.showDeletedAppointmentsOnly ? 1 : 0
          });

          const rows = Array.isArray(res?.data) ? res.data : [];
          const events = rows.flatMap(mapServerItemToEvents);

          U.extractHolidayDates(events);
          initializeCalendar(events);
          S.fc.gotoDate(date);

          setTimeout(() => {
            refreshCalendarBlockedDayStyles();
            refreshSidebarEmployeeTodayBadges();
            showEmployeeSelectionWarnings();
          }, 80);
        } catch (err) {
          console.error("filterMainCalendarByDate:", err);
        }
      }

      // =========================
      // Employee picker
      // =========================
      function formatDateDE(value) {
        if (!value) return "";

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return value;

        return date.toLocaleDateString("de-DE", {
          day: "2-digit",
          month: "2-digit",
          year: "numeric"
        });
      }

      function employeeStatusBadgeFromOption($option) {
        const leaveFrom = $option.attr("data-leave-from") || "";
        const leaveTo = $option.attr("data-leave-to") || "";
        const leaveType = $option.attr("data-leave-type") || "";
        const leaveReason = $option.attr("data-leave-reason") || "";

        const sickFrom = $option.attr("data-sick-from") || "";
        const sickTo = $option.attr("data-sick-to") || "";
        const sickMsg = $option.attr("data-sick-msg") || "";

        if (sickFrom && sickTo) {
          return `
                                          <span class="emp-status-badge emp-status-badge--sick" title="${escapeHtml(sickMsg || 'Krank')}">
                                            <span class="emp-status-dot-ui"></span>
                                            Krank ${formatDateDE(sickFrom)} - ${formatDateDE(sickTo)}
                                          </span>
                                        `;
        }

        if (leaveFrom && leaveTo) {
          const label = leaveType || leaveReason || "Urlaub";

          return `
                                          <span class="emp-status-badge emp-status-badge--leave" title="${escapeHtml(label)}">
                                            <span class="emp-status-dot-ui"></span>
                                            Urlaub ${formatDateDE(leaveFrom)} - ${formatDateDE(leaveTo)}
                                          </span>
                                        `;
        }

        return `
                                        <span class="emp-status-badge emp-status-badge--available">
                                          <span class="emp-status-dot-ui"></span>
                                          Verfügbar
                                        </span>
                                      `;
      }

      function calendarEventCoversDate(ev, dateStr) {
        if (!ev || !ev.start || !dateStr) return false;

        const target = calendarDateToStartOfDay(dateStr);
        const start = calendarDateToStartOfDay(ev.start);

        if (!target || !start) return false;

        let end = ev.end ? calendarDateToStartOfDay(ev.end) : null;

        if (!end || Number.isNaN(end.getTime())) {
          end = new Date(start);
        }

        // FullCalendar all-day end is exclusive. Example: 24.06 - 25.06 means only 24.06.
        if (ev.allDay && ev.end && end > start) {
          end.setDate(end.getDate() - 1);
        }

        return target >= start && target <= end;
      }

      function getEmployeeTodayCalendarAbsence(employeeId) {
        if (!S.fc || !employeeId) return null;

        const todayStr = U.isoDate(new Date());
        const id = String(employeeId);

        const items = S.fc.getEvents()
          .filter(ev => {
            const xp = ev.extendedProps || {};
            const type = String(xp.type || "");

            if (!["sick", "holiday", "recurring_leave", "leave_request"].includes(type)) return false;
            if (isRecurringHomeOfficeEvent(ev)) return false;
            if (xp.is_cancelled === true || xp.status === "cancelled") return false;
            if (!calendarEventCoversDate(ev, todayStr)) return false;

            const ids = getEmployeeIdsFromEvent(ev).map(String);

            // Do not mark everyone as unavailable if the absence event has no employee id.
            if (!ids.length) return false;

            return ids.includes(id);
          })
          .map(ev => {
            const xp = ev.extendedProps || {};
            const type = String(xp.type || "");

            return {
              event: ev,
              type,
              label: getBlockingEventLabel(ev),
              title: ev.title || xp.title || getBlockingEventLabel(ev),
              dateLabel: getEventDateRangeLabel(ev)
            };
          })
          .sort((a, b) => getAbsencePriority(a.type) - getAbsencePriority(b.type));

        return items[0] || null;
      }

      function employeeTodayStatusBadgeFromCalendarAbsence(absence) {
        if (!absence) return "";

        let cls = "emp-status-badge--leave";
        let text = "Heute abwesend";

        if (absence.type === "sick") {
          cls = "emp-status-badge--sick";
          text = "Heute krank";
        } else if (absence.type === "holiday") {
          cls = "emp-status-badge--leave";
          text = "Heute Urlaub";
        } else if (absence.type === "recurring_leave") {
          cls = "emp-status-badge--series";
          text = "Heute Serie";
        } else if (absence.type === "leave_request") {
          cls = "emp-status-badge--request";
          text = "Heute Urlaubsantrag";
        }

        const title = [absence.label, absence.title, absence.dateLabel]
          .filter(Boolean)
          .join(" · ");

        return `
                <span class="emp-status-badge ${cls}" title="${escapeHtml(title || text)}">
                  <span class="emp-status-dot-ui"></span>
                  ${escapeHtml(text)}
                </span>
              `;
      }

      function refreshSidebarEmployeeTodayBadges() {
        document.querySelectorAll(".employee-card-item[data-employee-id]").forEach(card => {
          const id = card.dataset.employeeId || "";
          const absence = getEmployeeTodayCalendarAbsence(id);

          if (!absence) return;

          const wrap = card.querySelector(".employee-availability-badge-wrap");
          if (wrap) {
            wrap.innerHTML = employeeTodayStatusBadgeFromCalendarAbsence(absence);
          }

          card.classList.add("has-unavailable-today");

          const dot = card.querySelector(".employee-status-dot");
          if (dot) {
            dot.classList.remove("is-sick-today", "is-unavailable-today");
            dot.classList.add(absence.type === "sick" ? "is-sick-today" : "is-unavailable-today");
          }
        });
      }

      function employeeTodayStatusBadgeFromData(emp) {
        emp = emp || {};

        const calendarAbsence = getEmployeeTodayCalendarAbsence(emp.id || emp.employee_id);
        if (calendarAbsence) {
          return employeeTodayStatusBadgeFromCalendarAbsence(calendarAbsence);
        }

        const leaveFrom = emp.current_leave_from || emp.leave_from || emp.leaveFrom || "";
        const leaveTo = emp.current_leave_to || emp.leave_to || emp.leaveTo || "";
        const leaveType = emp.current_leave_type || emp.leave_type || emp.leaveType || "";
        const leaveReason = emp.current_leave_reason || emp.leave_reason || emp.leaveReason || "";

        const sickFrom = emp.current_sick_from || emp.sick_from || emp.sickFrom || "";
        const sickTo = emp.current_sick_to || emp.sick_to || emp.sickTo || "";
        const sickMsg = emp.current_sick_msg || emp.sick_msg || emp.sickMsg || "";

        if (sickFrom && sickTo) {
          return `
                  <span class="emp-status-badge emp-status-badge--sick" title="${escapeHtml(sickMsg || 'Krank heute')}">
                    <span class="emp-status-dot-ui"></span>
                    Heute krank
                  </span>
                `;
        }

        if (leaveFrom && leaveTo) {
          const label = leaveType || leaveReason || "Urlaub heute";

          return `
                  <span class="emp-status-badge emp-status-badge--leave" title="${escapeHtml(label)}">
                    <span class="emp-status-dot-ui"></span>
                    Heute abwesend
                  </span>
                `;
        }

        return `
                <span class="emp-status-badge emp-status-badge--available" title="Heute verfügbar">
                  <span class="emp-status-dot-ui"></span>
                  Heute verfügbar
                </span>
              `;
      }

      function employeeTodayAvailabilityType(emp) {
        emp = emp || {};

        const calendarAbsence = getEmployeeTodayCalendarAbsence(emp.id || emp.employee_id);
        if (calendarAbsence) {
          return calendarAbsence.type === "sick" ? "sick" : "leave";
        }

        if ((emp.current_sick_from || emp.sick_from || emp.sickFrom) && (emp.current_sick_to || emp.sick_to || emp.sickTo)) {
          return "sick";
        }

        if ((emp.current_leave_from || emp.leave_from || emp.leaveFrom) && (emp.current_leave_to || emp.leave_to || emp.leaveTo)) {
          return "leave";
        }

        return "available";
      }

      function formatEmployee(e) {
        if (!e.id) return e.text;

        const $option = $(e.element);
        const img = $option.attr("data-image") || "/images/default-avatar.png";
        const statusBadge = employeeStatusBadgeFromOption($option);

        return `
                                        <div class="emp-select2-option">
                                          <img src="${img}" class="emp-select2-avatar" alt="">
                                          <div class="emp-select2-body">
                                            <div class="emp-select2-name">${escapeHtml(e.text || "")}</div>
                                            <div class="emp-select2-status">${statusBadge}</div>
                                          </div>
                                        </div>
                                      `;
      }
      function initEmployeeSelect2() {
        $(".employee").select2({ templateResult: formatEmployee, templateSelection: formatEmployee, escapeMarkup: (m) => m });

        /*
        |--------------------------------------------------------------------------
        | Appointment participants must NOT overwrite sidebar calendar filters
        |--------------------------------------------------------------------------
        | #employee belongs to the appointment form. The left sidebar employee list
        | controls S.selectedEmployeeIds and calendar filtering. Before this fix,
        | changing participants in the modal replaced S.selectedEmployeeIds, which
        | removed the already selected sidebar employee and forced the user to
        | select it again.
        */
        $("#employee").off("change.calendarEmployee")
          .on("change.calendarEmployee", function () {
            // Keep appointment participants independent from the sidebar filter.
            // Saving still uses $('#employee').val(), so no save behavior is lost.
          });
      }

      function updateSelectedEmployeeCount() {
        const counter = document.getElementById("selectedEmployeeCount");
        if (counter) {
          counter.textContent = S.selectedEmployeeIds.size;
        }
      }

      function setEmployeeCardState(id, on) {
        id = String(id);

        const cb = document.getElementById(`check${id}`);
        const img = document.getElementById(`employeeCheck${id}`);
        const card = document.querySelector(`.employee-card-item[data-employee-id="${id}"]`);

        if (cb) cb.checked = on;

        if (card) {
          card.classList.toggle("is-selected", on);
        }

        if (img) {
          img.classList.toggle("emp_active", on);
          img.style.borderColor = on ? (img.dataset.color || "#8fc73e") : "transparent";
        }
      }

      function ensureEmployeeOption(id, fallbackName = null) {
        id = String(id);

        const $employee = $("#employee");
        if (!$employee.length) return;

        if ($employee.find(`option[value="${id}"]`).length) return;

        const card = document.querySelector(`.employee-card-item[data-employee-id="${id}"]`);

        const name =
          fallbackName ||
          card?.querySelector(".employee-name")?.innerText?.trim() ||
          card?.querySelector(".employee-lastname")?.innerText?.trim() ||
          `#${id}`;

        U.ensureOption($employee, id, name);
      }

      function getSidebarSelectedEmployeeIds() {
        return Array.from(S.selectedEmployeeIds || []).map(String).filter(Boolean);
      }

      function isAppointmentModalOpen() {
        const modal = D.newTaskCard || document.querySelector('.new_task_card, .new_task');
        return !!(modal && window.getComputedStyle(modal).display !== 'none');
      }

      function setAppointmentEmployeeSelection(ids) {
        ids = (ids || []).map(String).filter(Boolean);

        const $employee = $('#employee');
        if (!$employee.length) return;

        ids.forEach(id => ensureEmployeeOption(id));
        $employee.val(ids).trigger('change.select2');
      }

      function getDefaultAppointmentEmployeeIds() {
        const selected = getSidebarSelectedEmployeeIds();
        if (selected.length) return selected;

        const currentId = String(AUTH_EMPLOYEE_ID || '').trim();
        return currentId ? [currentId] : [];
      }

      function prepareNewAppointmentEmployeeSelection() {
        const ids = getDefaultAppointmentEmployeeIds();
        setAppointmentEmployeeSelection(ids);
      }

      // Expose these for later script blocks in this Blade that open/reset the same appointment form.
      window.setAppointmentEmployeeSelection = setAppointmentEmployeeSelection;
      window.prepareNewAppointmentEmployeeSelection = prepareNewAppointmentEmployeeSelection;

      function syncEmployeeUi(shouldReload = true) {
        const ids = Array.from(S.selectedEmployeeIds).map(String);

        ids.forEach(id => ensureEmployeeOption(id));

        U.qa(".employee_check").forEach(cb => {
          const id = String(cb.dataset.id);
          setEmployeeCardState(id, S.selectedEmployeeIds.has(id));
        });

        window.selectedEmployeeIds = S.selectedEmployeeIds;

        // Keep the hidden/form participant select synced while the modal is closed.
        // Once the modal is open, user edits in #employee must not be overwritten
        // by sidebar refresh/search/select actions.
        if (!isAppointmentModalOpen()) {
          setAppointmentEmployeeSelection(ids);
        }

        if ($("#mobileEmployeeSelect").length) {
          $("#mobileEmployeeSelect").val(ids).trigger("change.select2");
        }

        updateSelectedEmployeeCount();

        if (shouldReload && typeof loadCalendarTasks === "function") {
          loadCalendarTasks();
        }

        if (S.urlaubGanttMode && typeof loadVacationGanttData === "function") {
          loadVacationGanttData();
        }
      }

      // Backward-compatible alias for older code in this Blade.
      function syncCheckboxWithDropdown(shouldReload = true) {
        syncEmployeeUi(shouldReload);
      }

      function selectEmployeeIds(ids, mode = "add", shouldReload = true) {
        ids = (ids || []).map(String).filter(Boolean);

        if (mode === "replace") {
          S.selectedEmployeeIds.clear();
        }

        ids.forEach(id => {
          S.selectedEmployeeIds.add(id);
          ensureEmployeeOption(id);
        });

        syncEmployeeUi(shouldReload);
      }

      function deselectEmployeeIds(ids, shouldReload = true) {
        ids = (ids || []).map(String).filter(Boolean);

        ids.forEach(id => {
          S.selectedEmployeeIds.delete(id);
        });

        syncEmployeeUi(shouldReload);
      }

      function clearAllSidebarEmployees() {
        S.selectedEmployeeIds.clear();
        syncEmployeeUi(true);
      }

      function selectAllVisibleSidebarEmployees() {
        let ids = U.qa("#search_emp_result .employee_check")
          .map(cb => String(cb.dataset.id))
          .filter(Boolean);

        if (!ids.length && $("#mobileEmployeeSelect").length) {
          ids = $("#mobileEmployeeSelect option")
            .map(function () {
              return String(this.value);
            })
            .get()
            .filter(Boolean);
        }

        selectEmployeeIds(ids, "add", true);
      }

      window.clearAllSidebarEmployees = clearAllSidebarEmployees;
      window.selectAllVisibleSidebarEmployees = selectAllVisibleSidebarEmployees;

      function getDepartmentEmployeeIds(departmentEl) {
        if (!departmentEl) return [];

        return U.qa(".employee_check", departmentEl)
          .map(cb => String(cb.dataset.id))
          .filter(Boolean);
      }

      // Anfrage -> Teilnehmer sync
      window.updateParticipantsFromInquiry = function () {
        const ids = new Set();

        $("#inquiryPreviewBody .inquiry-employee-select, #inquiryPreviewBody .inquiry-field-employee-select").each(function () {
          const v = $(this).val();
          if (Array.isArray(v)) {
            v.forEach(id => id && ids.add(String(id)));
          } else if (v) {
            ids.add(String(v));
          }
        });

        const idArray = Array.from(ids);
        const $emp = $("#employee");

        idArray.forEach(id => {
          if (!$emp.find(`option[value="${id}"]`).length) {
            const label =
              $(`#inquiryPreviewBody select option[value="${id}"]:first`).text().trim() ||
              `#${id}`;
            U.ensureOption($emp, id, label);
          }
        });

        // Anfrage employees are appointment participants only.
        // Do not replace the sidebar calendar filter here.
        setAppointmentEmployeeSelection(idArray);
      };

      async function fetchEmployees(q = "", filter = "employee") {
        const box = document.getElementById("search_emp_result");
        if (!box) return;

        box.innerHTML = "";

        if (S.empAbort) S.empAbort.abort();
        S.empAbort = new AbortController();

        try {
          const r = await fetch(ROUTE.getEmployees(q, filter), {
            signal: S.empAbort.signal,
            headers: {
              Accept: "application/json"
            }
          });

          if (!r.ok) throw new Error(`HTTP ${r.status}`);

          const result = await r.json();

          const groups = Array.isArray(result.groups) && result.groups.length
            ? result.groups
            : buildGroupsFromFlatData(result.data || []);

          if (!groups.length) {
            box.innerHTML = `
                                                                    <div class="employee-empty-state">
                                                                      <i class="feather icon-user-x"></i>
                                                                      Keine Mitarbeiter gefunden.
                                                                    </div>
                                                                  `;
            updateSelectedEmployeeCount();
            if (window.feather) feather.replace();
            return;
          }

          const seen = new Set();
          const fav = (S.favoriteEmployeeIds || []).map(String);

          /*
          |--------------------------------------------------------------------------
          | Mobile: Select2 optgroups
          |--------------------------------------------------------------------------
          */
          if (U.isMobile()) {
            const select = document.createElement("select");
            select.id = "mobileEmployeeSelect";
            select.className = "form-control employee";
            select.setAttribute("multiple", "multiple");

            groups.forEach(group => {
              const employees = Array.isArray(group.employees) ? group.employees : [];
              if (!employees.length) return;

              const optgroup = document.createElement("optgroup");
              optgroup.label = `${group.department_name || "Ohne Abteilung"} (${employees.length})`;

              employees.forEach(emp => {
                const id = String(emp.id);
                if (seen.has(id)) return;
                seen.add(id);

                const fullName = `${emp.name || ""} ${emp.lastname || ""}`.trim() || `#${id}`;

                const opt = document.createElement("option");
                opt.value = id;
                opt.text = fullName;
                opt.setAttribute("data-image", `/images/employee/${emp.image || "default-avatar.png"}`);
                opt.setAttribute("data-leave-from", emp.current_leave_from || "");
                opt.setAttribute("data-leave-to", emp.current_leave_to || "");
                opt.setAttribute("data-leave-type", emp.current_leave_type || "");
                opt.setAttribute("data-leave-reason", emp.current_leave_reason || "");
                opt.setAttribute("data-leave-status", emp.current_leave_status || "");
                opt.setAttribute("data-sick-from", emp.current_sick_from || "");
                opt.setAttribute("data-sick-to", emp.current_sick_to || "");
                opt.setAttribute("data-sick-status", emp.current_sick_status || "");
                opt.setAttribute("data-sick-msg", emp.current_sick_msg || "");

                optgroup.appendChild(opt);

                ensureEmployeeOption(id, fullName);
              });

              if (optgroup.children.length) {
                select.appendChild(optgroup);
              }
            });

            box.appendChild(select);

            $("#mobileEmployeeSelect").select2({
              templateResult: formatEmployee,
              templateSelection: formatEmployee,
              placeholder: "Mitarbeiter auswählen",
              width: "100%",
              escapeMarkup: (m) => m,
              dropdownParent: $("#search_emp_result")
            })
              .off("change.calendarMobileEmployee")
              .on("change.calendarMobileEmployee", function () {
                S.selectedEmployeeIds = new Set(($(this).val() || []).map(String));
                window.selectedEmployeeIds = S.selectedEmployeeIds;

                syncEmployeeUi(true);
              });

            const pre = Array.from(S.selectedEmployeeIds);

            if (pre.length) {
              $("#mobileEmployeeSelect").val(pre).trigger("change.select2");
              syncEmployeeUi(false);
            } else if (fav.length) {
              fav.forEach(id => S.selectedEmployeeIds.add(String(id)));
              $("#mobileEmployeeSelect").val(fav).trigger("change.select2");
              syncEmployeeUi(true);
            } else {
              updateSelectedEmployeeCount();
            }

            if (window.feather) feather.replace();
            return;
          }

          /*
          |--------------------------------------------------------------------------
          | Desktop: Department accordion
          |--------------------------------------------------------------------------
          */
          const actions = document.createElement("div");
          actions.className = "employee-sidebar-actions";
          actions.innerHTML = `
                                                                  <button type="button" id="expandAllDepartments">
                                                                    <i class="feather icon-plus-circle"></i> Alle öffnen
                                                                  </button>
                                                                  <button type="button" id="collapseAllDepartments">
                                                                    <i class="feather icon-minus-circle"></i> Alle schließen
                                                                  </button>
                                                                `;
          box.appendChild(actions);

          groups.forEach(group => {
            const employees = Array.isArray(group.employees) ? group.employees : [];
            if (!employees.length) return;

            const departmentId = group.department_id ?? "no_department";
            const departmentName = group.department_name || "Ohne Abteilung";
            const count = employees.length;

            const department = document.createElement("div");
            department.className = "employee-department";
            department.dataset.departmentId = departmentId;

            department.innerHTML = `
                                                                    <button type="button" class="employee-department-header">
                                                                      <span class="employee-department-left">
                                                                        <i class="feather icon-chevron-down employee-department-arrow"></i>
                                                                        <span class="employee-department-name">${escapeHtmlLocal(departmentName)}</span>
                                                                      </span>
                                                                      <span class="employee-department-count">${count}</span>
                                                                    </button>

                                                                    <div class="employee-department-body">
                                                                      <div class="employee-department-tools">
                                                                        <button type="button" class="select-department-employees">
                                                                          <i class="feather icon-check-square"></i> Abteilung
                                                                        </button>
                                                                        <button type="button" class="clear-department-employees">
                                                                          <i class="feather icon-x-square"></i> Leeren
                                                                        </button>
                                                                      </div>
                                                                    </div>
                                                                  `;

            const body = department.querySelector(".employee-department-body");

            employees.forEach(emp => {
              const id = String(emp.id);

              if (seen.has(id)) return;
              seen.add(id);

              const border = emp.color || "#8fc73e";
              const checked = S.selectedEmployeeIds.has(id);
              const image = emp.image || "default-avatar.png";
              const fullName = `${emp.name || ""} ${emp.lastname || ""}`.trim() || `#${id}`;
              const availabilityType = employeeTodayAvailabilityType(emp);
              const availabilityBadge = employeeTodayStatusBadgeFromData(emp);
              const unavailableClass = availabilityType !== "available" ? "has-unavailable-today" : "";
              const dotUnavailableClass = availabilityType === "sick" ? "is-sick-today" : (availabilityType === "leave" ? "is-unavailable-today" : "");

              ensureEmployeeOption(id, fullName);

              const div = document.createElement("div");
              div.className = `employee-card-item ${checked ? "is-selected" : ""} ${unavailableClass}`.trim();
              div.dataset.employeeId = id;

              div.innerHTML = `
                                                                      <input
                                                                        type="checkbox"
                                                                        class="employee_check"
                                                                        data-id="${id}"
                                                                        id="check${id}"
                                                                        style="display:none"
                                                                        ${checked ? "checked" : ""}
                                                                      >

                                                                      <div class="employee-avatar-wrap">
                                                                        <img
                                                                          src="/images/employee/${escapeHtmlAttrLocal(image)}"
                                                                          alt="${escapeHtmlAttrLocal(fullName)}"
                                                                          width="44"
                                                                          height="44"
                                                                          data-id="${id}"
                                                                          data-color="${escapeHtmlAttrLocal(border)}"
                                                                          class="employee_checkbox ${checked ? "emp_active" : ""}"
                                                                          id="employeeCheck${id}"
                                                                          style="border-color:${checked ? escapeHtmlAttrLocal(border) : "transparent"};"
                                                                        >
                                                                        <span class="employee-status-dot ${dotUnavailableClass}"></span>
                                                                      </div>

                                                                      <div class="employee-card-content">
                                                                        <span class="employee-name">${escapeHtmlLocal(emp.name || "")}</span>
                                                                        <span class="employee-lastname">
                                                                          ${escapeHtmlLocal(emp.lastname || "Mitarbeiter")}
                                                                          ${emp.position_name ? " · " + escapeHtmlLocal(emp.position_name) : ""}
                                                                          ${emp.is_department_representative ? " · Vertreter" : ""}
                                                                        </span>
                                                                        <div class="employee-availability-badge-wrap">
                                                                          ${availabilityBadge}
                                                                        </div>
                                                                      </div>

                                                                      <span class="employee-card-check">
                                                                        <i class="feather icon-check"></i>
                                                                      </span>
                                                                    `;

              function toggleEmployeeCard(forceValue = null) {
                const currentlySelected = S.selectedEmployeeIds.has(id);
                const on = forceValue === null ? !currentlySelected : Boolean(forceValue);

                if (on) {
                  S.selectedEmployeeIds.add(id);
                } else {
                  S.selectedEmployeeIds.delete(id);
                }

                ensureEmployeeOption(id, fullName);
                syncEmployeeUi(true);
              }

              div.addEventListener("click", function (e) {
                e.preventDefault();
                toggleEmployeeCard();
              });

              const cb = div.querySelector(`#check${id}`);
              if (cb) {
                cb.addEventListener("change", function (e) {
                  e.stopPropagation();
                  toggleEmployeeCard(this.checked);
                });
              }

              body.appendChild(div);
            });

            box.appendChild(department);
          });

          /*
          |--------------------------------------------------------------------------
          | Collapse / expand
          |--------------------------------------------------------------------------
          */
          box.querySelectorAll(".employee-department-header").forEach(header => {
            header.addEventListener("click", function () {
              const wrapper = this.closest(".employee-department");
              if (!wrapper) return;

              wrapper.classList.toggle("is-collapsed");
            });
          });

          const expandAllBtn = document.getElementById("expandAllDepartments");
          const collapseAllBtn = document.getElementById("collapseAllDepartments");

          if (expandAllBtn) {
            expandAllBtn.addEventListener("click", function () {
              box.querySelectorAll(".employee-department").forEach(dep => {
                dep.classList.remove("is-collapsed");
              });
            });
          }

          if (collapseAllBtn) {
            collapseAllBtn.addEventListener("click", function () {
              box.querySelectorAll(".employee-department").forEach(dep => {
                dep.classList.add("is-collapsed");
              });
            });
          }

          /*
          |--------------------------------------------------------------------------
          | Department select / clear
          |--------------------------------------------------------------------------
          */
          box.querySelectorAll(".select-department-employees").forEach(btn => {
            btn.addEventListener("click", function (e) {
              e.preventDefault();
              e.stopPropagation();

              const departmentEl = this.closest(".employee-department");
              const ids = getDepartmentEmployeeIds(departmentEl);

              selectEmployeeIds(ids, "add", true);
            });
          });

          box.querySelectorAll(".clear-department-employees").forEach(btn => {
            btn.addEventListener("click", function (e) {
              e.preventDefault();
              e.stopPropagation();

              const departmentEl = this.closest(".employee-department");
              const ids = getDepartmentEmployeeIds(departmentEl);

              deselectEmployeeIds(ids, true);
            });
          });

          syncEmployeeUi(false);
          refreshSidebarEmployeeTodayBadges();

          if (window.feather) feather.replace();

        } catch (err) {
          if (err.name === "AbortError") return;

          console.error("fetchEmployees:", err);
          box.innerHTML = `
                                                                  <div class="employee-empty-state" style="color:#dc2626;">
                                                                    <i class="feather icon-alert-triangle"></i>
                                                                    Mitarbeiter konnten nicht geladen werden.
                                                                  </div>
                                                                `;
          if (window.feather) feather.replace();
        }
      }

      function buildGroupsFromFlatData(data) {
        const map = new Map();

        (data || []).forEach(emp => {
          const departmentId = emp.department_id || "no_department";
          const departmentName = emp.department_name || "Ohne Abteilung";

          if (!map.has(departmentId)) {
            map.set(departmentId, {
              department_id: departmentId,
              department_name: departmentName,
              employees: []
            });
          }

          map.get(departmentId).employees.push(emp);
        });

        return Array.from(map.values());
      }

      function escapeHtmlLocal(value) {
        return String(value ?? "")
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;")
          .replace(/"/g, "&quot;")
          .replace(/'/g, "&#039;");
      }

      function escapeHtmlAttrLocal(value) {
        return escapeHtmlLocal(value).replace(/`/g, "&#096;");
      }

      function bindSearchControls() {
        U.qa('input[name="filter"]').forEach(r => {
          r.addEventListener("change", function () {
            S.selectedEmployeeIds = U.selectedFromDOM();
            fetchEmployees("", this.value);
          });
        });

        const input = document.getElementById("employee_get");

        if (input && !input.dataset.bound) {
          input.dataset.bound = "1";

          let timer = null;

          input.addEventListener("input", function () {
            const value = this.value.trim();

            clearTimeout(timer);

            timer = setTimeout(() => {
              S.selectedEmployeeIds = U.selectedFromDOM();
              fetchEmployees(value, "employee");
            }, 250);
          });
        }

        fetchEmployees("", "employee");
      }

      // =========================
      // Inquiry + products
      // =========================
      function cleanAppointmentAddressValue(value) {
        return String(value ?? "")
          .replace(/\s*\|\s*Lat\s*:.*$/iu, "")
          .replace(/\s*\|\s*Latitude\s*:.*$/iu, "")
          .replace(/\s*Lat\s*:\s*[-0-9.,]+\s*\/\s*Lng\s*:\s*[-0-9.,]+/iu, "")
          .replace(/\s*Latitude\s*:\s*[-0-9.,]+\s*\/\s*Longitude\s*:\s*[-0-9.,]+/iu, "")
          .replace(/\s*\|\s*$/g, "")
          .trim();
      }

      function splitAppointmentAddressFromFullAddress(fullAddress) {
        const clean = cleanAppointmentAddressValue(fullAddress);
        const out = { street: "", postcode: "", city: "", full_address: clean };

        if (!clean) return out;

        const readPart = part => {
          part = cleanAppointmentAddressValue(part);
          if (!part) return;

          const zipMatch = part.match(/^(\d{4,5})\s+(.+)$/u);
          if (zipMatch) {
            if (!out.postcode) out.postcode = zipMatch[1];
            if (!out.city) out.city = zipMatch[2].trim();
            return;
          }

          if (!out.street && !/^\d{4,5}\s+/.test(part)) {
            out.street = part;
          }
        };

        clean.split("|").map(v => v.trim()).filter(Boolean).forEach(readPart);
        clean.split(",").map(v => v.trim()).filter(Boolean).forEach(readPart);

        return out;
      }

      function buildAppointmentFullAddress(street, postcode, city, fullAddress = "") {
        street = cleanAppointmentAddressValue(street);
        postcode = cleanAppointmentAddressValue(postcode);
        city = cleanAppointmentAddressValue(city);

        const parsed = splitAppointmentAddressFromFullAddress(fullAddress);

        if (!street) street = parsed.street || "";
        if (!postcode) postcode = parsed.postcode || "";
        if (!city) city = parsed.city || "";

        const postcodeCity = [postcode, city].filter(Boolean).join(" ").trim();
        const visible = [street, postcodeCity].filter(Boolean).join(", ").trim();

        return visible || parsed.full_address || "";
      }

      function normalizeAppointmentAddressOption(raw) {
        if (!raw) return null;

        const street = cleanAppointmentAddressValue(raw.street || "");
        const postcode = cleanAppointmentAddressValue(raw.postcode || "");
        const city = cleanAppointmentAddressValue(raw.city || "");
        const latitude = cleanAppointmentAddressValue(raw.latitude ?? raw.lat ?? "");
        const longitude = cleanAppointmentAddressValue(raw.longitude ?? raw.lon ?? "");
        const fullAddress = buildAppointmentFullAddress(street, postcode, city, raw.full_address || "");

        if (!street && !postcode && !city && !fullAddress) return null;

        return {
          label: raw.label || raw.text || "Adresse",
          source_type: raw.source_type || "",
          source_id: raw.source_id || raw.id || "",
          alternative_id: raw.alternative_id || "",
          lead_product_list_id: raw.lead_product_list_id || "",
          street,
          postcode,
          city,
          latitude,
          longitude,
          full_address: fullAddress
        };
      }

      function appointmentAddressUniqueKey(item) {
        return [
          item.source_type,
          item.source_id,
          item.alternative_id,
          item.street,
          item.postcode,
          item.city,
          item.latitude,
          item.longitude
        ].join("|").toLowerCase();
      }

      function setAppointmentAddressFromOption(option) {
        if (!option) return;

        const normalized = normalizeAppointmentAddressOption(option);
        if (!normalized) return;

        const visibleAddress = buildAppointmentFullAddress(
          normalized.street || "",
          normalized.postcode || "",
          normalized.city || "",
          normalized.full_address || ""
        );

        // Visible field: human-readable address only. No Lat/Lng text here.
        $("#full_address").val(visibleAddress);
        $("#street-input").val(normalized.street || "");
        $("#city-input").val(normalized.city || "");
        $("#postal_code-input").val(normalized.postcode || "");

        // Hidden fields: coordinates stay here for Google/maps/backend recognition.
        $("#latitude-input").val(normalized.latitude || "");
        $("#longitude-input").val(normalized.longitude || "");

        $("#address_source_type").val(normalized.source_type || "");
        $("#address_source_id").val(normalized.source_id || "");
        $("#selected_alternative_id").val(normalized.alternative_id || "");
      }

      function clearAppointmentAddressChoices() {
        const $wrap = $("#appointmentAddressChoiceWrap");
        const $select = $("#appointment_address_choice");

        if ($select.length && $select.data("select2")) {
          $select.off("change.appointmentAddressChoice").select2("destroy");
        }

        $select.empty().removeData("address-options");
        $wrap.addClass("d-none");
      }

      function renderAppointmentAddressChoices(options, config = {}) {
        const $wrap = $("#appointmentAddressChoiceWrap");
        const $select = $("#appointment_address_choice");
        if (!$select.length) return;

        const unique = [];
        const seen = new Set();

        (options || []).forEach(raw => {
          const item = normalizeAppointmentAddressOption(raw);
          if (!item) return;

          const key = appointmentAddressUniqueKey(item);
          if (seen.has(key)) return;

          seen.add(key);
          unique.push(item);
        });

        clearAppointmentAddressChoices();

        if (!unique.length) return;

        if (unique.length === 1) {
          if (!config.preserveExisting) setAppointmentAddressFromOption(unique[0]);
          return;
        }

        unique.forEach((item, index) => {
          const label = item.full_address ? `${item.label}: ${item.full_address}` : item.label;
          $select.append(new Option(label, String(index), false, false));
        });

        $select.data("address-options", unique);
        $wrap.removeClass("d-none");

        $select.select2({
          width: "100%",
          placeholder: "Adresse wählen",
          allowClear: false,
          dropdownParent: $(".new_task_card").length ? $(".new_task_card") : $(document.body)
        });

        $select.off("change.appointmentAddressChoice").on("change.appointmentAddressChoice", function () {
          const opts = $(this).data("address-options") || [];
          setAppointmentAddressFromOption(opts[Number($(this).val())]);
        });

        let selectedIndex = 0;
        const wantedObjectId = cleanAppointmentAddressValue(config.preselectedObjectId || S.preselectedObjectId || $("#selected_alternative_id").val());

        if (wantedObjectId) {
          const foundIndex = unique.findIndex(item => String(item.alternative_id || item.source_id) === wantedObjectId);
          if (foundIndex >= 0) selectedIndex = foundIndex;
        }

        $select.val(String(selectedIndex)).trigger("change.select2");

        if (!config.preserveExisting) {
          setAppointmentAddressFromOption(unique[selectedIndex]);
        }
      }

      function collectAddressOptionsFromProductGroups(groups, selectedCustomer) {
        const options = [];
        const c = selectedCustomer || S.selectedCustomerData || null;

        if (c) {
          options.push({
            label: "Kundenadresse",
            source_type: "customer",
            source_id: c.id,
            alternative_id: "",
            street: c.street || "",
            postcode: c.postcode || "",
            city: c.city || "",
            latitude: c.latitude || "",
            longitude: c.longitude || "",
            full_address: buildAppointmentFullAddress(c.street, c.postcode, c.city, c.full_address || "")
          });
        }

        (groups || []).forEach(group => {
          if (group?.address) {
            options.push({
              label: group.text || "Objektadresse",
              source_type: group.type === "customer" ? "customer" : "object",
              source_id: group.alternative_id || c?.id || "",
              alternative_id: group.alternative_id || "",
              ...group.address
            });
          }

          (group?.children || []).forEach(product => {
            const address = product.address || group.address || null;
            if (!address) return;

            options.push({
              label: product.product_name ? `Produkt/Objekt: ${product.product_name}` : (group.text || "Objektadresse"),
              source_type: "lead_product_list",
              source_id: product.lead_product_list_id || product.id || "",
              lead_product_list_id: product.lead_product_list_id || "",
              alternative_id: product.alternative_id || group.alternative_id || "",
              ...address
            });
          });
        });

        return options;
      }

      function loadCustomerProducts(customerId, preselectIds, selectedCustomer = null) {
        const $block = $('.product-select-block'), $select = $('#productSelect');
        $block.removeClass('d-none');

        S.productMap = {};

        if ($select.length) {
          if ($select.data('select2')) {
            $select.off('change.products').select2('destroy');
          }
          $select.empty();
        }

        if (!customerId) {
          $('#products').val('');
          clearAppointmentAddressChoices();
          initSelect2Singleton($select, { multiple: true });
          return;
        }

        $.ajax({
          url: ROUTE.productsByCustomer,
          method: "GET",
          data: { customer_id: customerId },
          dataType: "json"
        })
          .done(groups => {
            let hasAny = false;
            const seenUids = new Set();
            const norm = value => String(value ?? "").trim();

            const isEditing = !!$("#appointment_id").val();
            const hasAddressAlready = !!norm($("#full_address").val());
            const addressOptions = collectAddressOptionsFromProductGroups(groups, selectedCustomer);

            renderAppointmentAddressChoices(addressOptions, {
              preselectedObjectId: S.preselectedObjectId,
              preserveExisting: isEditing && hasAddressAlready
            });

            (groups || []).forEach(group => {
              if (!group || !Array.isArray(group.children) || !group.children.length) return;

              const groupAddress = group.address || null;
              const $og = $('<optgroup>').attr('label', group.text || 'Gruppe');

              group.children.forEach(product => {
                const uid = norm(product.uid || product.id || (product.lead_product_list_id ? `lpl_${product.lead_product_list_id}` : `${product.product_name}_${product.alternative_id}`));
                if (!uid || seenUids.has(uid)) return;

                seenUids.add(uid);

                const info = {
                  ...product,
                  uid,
                  address: product.address || groupAddress || null,
                  lead_product_list_id: product.lead_product_list_id || null
                };

                S.productMap[uid] = info;

                const productLabel = product.product_name || product.text || 'Ohne Artikelgruppe';
                const objectLabel = group.text && group.text !== productLabel ? ` · ${group.text}` : '';
                const cityLabel = product.city ? ` (${product.city})` : '';

                $og.append(
                  $('<option>')
                    .val(uid)
                    .text(`${productLabel}${cityLabel}${objectLabel}`)
                );

                hasAny = true;
              });

              if ($og.children().length) $select.append($og);
            });

            initSelect2Singleton($select, { multiple: true });

            if (!hasAny) {
              $select.empty().append($('<option disabled>').text('— Keine Produkte für diesen Kontakt gefunden —'));
              initSelect2Singleton($select, { multiple: true });
              $('#products').val('');
              if (typeof maToggleAppointmentStageCard === "function") maToggleAppointmentStageCard();
              return;
            }

            let ids = [];

            if (Array.isArray(preselectIds) && preselectIds.length) {
              ids = preselectIds.map(norm).filter(Boolean);
            } else {
              const savedJson = $('#products').val();
              if (savedJson) {
                try {
                  const parsed = JSON.parse(savedJson);

                  if (Array.isArray(parsed)) {
                    ids = parsed
                      .map(item => norm(item.uid || item.id || (item.lead_product_list_id ? `lpl_${item.lead_product_list_id}` : `${item.name}_${item.alternative_id}`)))
                      .filter(Boolean);
                  } else if (parsed && typeof parsed === 'object') {
                    ids = Object.entries(parsed)
                      .map(([name, tuple]) => norm(`${name}_${tuple?.[0]}`))
                      .filter(Boolean);
                  }
                } catch { }
              }
            }

            const optionValues = new Set(Array.from($select[0].options).map(option => option.value));
            ids = ids.filter(uid => optionValues.has(uid));

            $select.off('change.products').on('change.products', function () {
              const val = $(this).val() || [];
              const out = [];

              val.forEach(uid => {
                const info = S.productMap[uid];
                if (!info) return;

                out.push({
                  uid,
                  lead_product_list_id: info.lead_product_list_id || null,
                  name: info.product_name || info.text || '',
                  alternative_id: info.alternative_id || null,
                  product_id: info.product_id || null,
                  customer_id: info.customer_id || null,
                  city: info.city || null,
                  address: info.address || null
                });
              });

              $('#products').val(out.length ? JSON.stringify(out) : '');

              if (out.length === 1 && out[0].address) {
                setAppointmentAddressFromOption({
                  label: out[0].name || "Produktadresse",
                  source_type: "lead_product_list",
                  source_id: out[0].lead_product_list_id || "",
                  lead_product_list_id: out[0].lead_product_list_id || "",
                  alternative_id: out[0].alternative_id || "",
                  ...out[0].address
                });
              }
            });

            if (ids.length) {
              $select.val(ids).trigger('change');
            } else {
              $('#products').val('');
            }

            if (typeof maToggleAppointmentStageCard === "function") maToggleAppointmentStageCard();
          })
          .fail(xhr => {
            console.error('loadCustomerProducts failed', xhr);
            initSelect2Singleton($select, { multiple: true });
          });
      }

      // =========================
      // Form save
      // =========================
      function getSelectedEmployeeData() {
        const ids = Array.from(S.selectedEmployeeIds).map(String);
        if (ids.length) return ids.map(id => ({ employee_id: id, tasks_only: 0, appointments_only: 1 }));
        if (U.isMobile()) {
          const m = ($("#mobileEmployeeSelect").val() || []).map(String);
          if (m.length) return m.map(id => ({ employee_id: id, tasks_only: 0, appointments_only: 1 }));
        } else {
          const checks = U.qa(".employee_check:checked").map(cb => String(cb.dataset.id));
          if (checks.length) return checks.map(id => ({ employee_id: id, tasks_only: 0, appointments_only: 1 }));
        }
        return [{ employee_id: AUTH_EMPLOYEE_ID, tasks_only: 0, appointments_only: 1 }];
      }

      $(document)
        .off("click.calendarSave", ".save-task")
        .on("click.calendarSave", ".save-task", async function (e) {
          e.preventDefault();
          e.stopPropagation();

          const $btn = $(this);
          if ($btn.data("saving") === true) return;
          $btn.data("saving", true).prop("disabled", true).html('<i class="feather icon-loader"></i> speichern...');

          syncEmployeeUi(false);

          if (TICKETS && typeof TICKETS.initSelects === "function") {
            TICKETS.initSelects();
          }

          const $form = $("#task-store-form");

          const mode = $("#contact_mode").val() || "new";

          const rawName = ($("#name").val() || "").trim();
          const selContact = $("#customer_id").select2("data");
          const contactText = (selContact && selContact[0]?.text ? selContact[0].text.split(" - ")[0] : "").trim();
          const selProb = $("#ticket_problem_id").select2("data");
          const probTxt = (selProb && selProb[0]?.text ? selProb[0].text : "").trim();
          const selTask = $("#ticket_task_id").select2("data");
          const taskTxt = (selTask && selTask[0]?.text ? selTask[0].text : "").trim();

          maSanitizeAppointmentStageInputs();

          const errs = [];
          const employee = $("#employee").val();
          const startDate = $("#start_date").val();
          const endDate = $("#end_date").val();
          const reminderDate = $("#reminder_date").val();
          const nextStep = $("#next_step").val();
          const responsible = $("#report_responsible").val();

          if (!employee || employee.length === 0) errs.push("Bitte weisen Sie mindestens einen Mitarbeiter zu.");

          let title = rawName;
          if (!title) {
            if (mode === "select") title = contactText;
            else if (mode === "ticket") {
              title = taskTxt || probTxt || contactText;
              if (!title && ticketAutoCreate) title = `Ticket ${ticketProblemId || ""}`.trim();
            }
          }
          if (!title) title = ($("#appointment_type").val() || "").trim() || ($("#full_address").val() || "").trim();
          $("#name").val(title);


          if (!title) errs.push("Der Titel darf nicht leer sein.");
          if (!startDate) errs.push("Das Startdatum darf nicht leer sein.");
          if (!endDate) errs.push("Das Enddatum darf nicht leer sein.");
          if (startDate && endDate && new Date(startDate) > new Date(endDate)) errs.push("Das Startdatum darf nicht größer als das Enddatum sein.");
          if (startDate && endDate) {
            const holidayOn = U.hasHolidayBetween(startDate, endDate);
            if (holidayOn) {
              const holidayLabel = S.publicHolidayMap?.get(holidayOn) || "Feiertag";
              errs.push(`Am ${holidayOn.split("-").reverse().join(".")} ist Feiertag: ${holidayLabel}.`);
            }
          }

          // Employee availability is informative only. Do not block saving appointments
          // when a selected employee has Urlaub/Krank/Serie on the appointment date.
          if (reminderDate) {
            if (!nextStep) errs.push("Bitte wählen Sie einen nächsten Schritt.");
            if (!responsible || responsible.length === 0) errs.push("Bitte wählen Sie einen Verantwortlichen.");
            else {
              const jsonResponsible = JSON.stringify([responsible]);
              if (!$("#responsible_json").length) $("<input>", { type: "hidden", id: "responsible_json", name: "responsible_json", value: jsonResponsible }).appendTo($form);
              else $("#responsible_json").val(jsonResponsible);
            }
          }

          if (errs.length) {
            $btn.data("saving", false).prop("disabled", false).html('<i class="feather icon-save"></i> speichern');
            Swal.fire({ icon: "error", title: "Fehlerhafte Eingabe", html: `<ul style="text-align:left;">${errs.map(e => `<li>${e}</li>`).join("")}</ul>` });
            return;
          }

          const phoneValue = ($("#phone").val() || $(".phone").val() || "").trim();
          const emailValue = ($("#email").val() || $(".email").val() || "").trim();

          if (!phoneValue || !emailValue) {
            const missing = [];
            if (!phoneValue) missing.push("Telefon");
            if (!emailValue) missing.push("E-Mail");

            const confirmMissingContact = await Swal.fire({
              icon: "info",
              title: "Kontaktdaten fehlen",
              html: `
                        <div style="text-align:left;">
                          <p>Folgende Kontaktdaten fehlen:</p>
                          <ul>${missing.map(item => `<li>${item}</li>`).join("")}</ul>
                          <p>Möchten Sie den Termin trotzdem speichern?</p>
                        </div>
                      `,
              showCancelButton: true,
              confirmButtonText: "Ja, trotzdem speichern",
              cancelButtonText: "Abbrechen",
              confirmButtonColor: "#8fc73e",
              cancelButtonColor: "#d33"
            });

            if (!confirmMissingContact.isConfirmed) {
              $btn.data("saving", false).prop("disabled", false).html('<i class="feather icon-save"></i> speichern');
              return;
            }
          }

          const appointmentId = $("#appointment_id").val();
          const method = appointmentId ? "PUT" : "POST";
          const url = appointmentId ? ROUTE.updateMainAppointment(appointmentId) : ROUTE.storeMainAppointment;

          try {
            maSanitizeAppointmentStageInputs();
            await $.ajax({ url, type: method, data: $form.serialize() });
            $(".new_task_card").hide();
            $form.trigger("reset");
            $("#appointment_id").val("");
            $("#customer_id").val(null).trigger("change");
            maResetAppointmentStageFields();
            $("#name, #name_display, #contact_type, #phone, #email, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input, #full_address, #address_source_type, #address_source_id, #selected_alternative_id").val("");
            clearAppointmentAddressChoices();
            $("#contact_mode").val("new"); $("#newContact").prop("checked", true).trigger("change");

            Swal.fire({ icon: "success", title: "Erfolg", text: appointmentId ? "Termin erfolgreich aktualisiert!" : "Termin erfolgreich gespeichert!" });

            const view = S.fc.view.type;
            // Use the startDate from your form so the calendar jumps to the newly created/edited event.
            // If it's missing for any reason, fallback to the current calendar date.
            const targetDate = startDate || S.fc.getDate();

            loadCalendarTasks(() => {
              S.fc.changeView(view);
              S.fc.gotoDate(targetDate);
            });
          } catch (xhr) {
            const errors = xhr?.responseJSON?.errors || {};
            const html = Object.values(errors).flat().map(m => `<li>${m}</li>`).join("");
            Swal.fire({ icon: "error", title: "Fehler", html: `<ul>${html || "Unbekannter Fehler aufgetreten."}</ul>` });
          } finally {
            $btn.data("saving", false).prop("disabled", false).html('<i class="feather icon-save"></i> speichern');
          }
        });

      // =========================
      // Settings
      // =========================
      function loadSettingsIntoModal(settings) {
        if (settings.favorite_employees) $("#favoriteEmployees").val(settings.favorite_employees.map(String)).trigger("change");
        if (settings.hidden_views) {
          const hiddenViews = settings.hidden_views.map(v => {
            if (v === "year") return "multiMonthYear";
            if (v === "month") return "dayGridMonth";
            if (v === "week") return "timeGridWeek";
            return v;
          });
          $('input[name="hidden_views[]"]').each(function () { $(this).prop("checked", hiddenViews.includes($(this).val())); });
        }
        if (settings.calendar_color) $("#calendarColorPicker").val(settings.calendar_color);
      }
      function applySettingsToCalendar(settings) {
        if (settings.favorite_employees?.length && $("#mobileEmployeeSelect").length) $("#mobileEmployeeSelect").val(settings.favorite_employees).trigger("change");
        if (settings.hidden_views) {
          settings.hidden_views.forEach(v => {
            if (v === "year") v = "multiMonthYear";
            if (v === "month") v = "dayGridMonth";
            if (v === "week") v = "timeGridWeek";
            const btn = document.querySelector(`.fc-${v}-button`);
            if (btn) btn.style.display = "none";
          });
        }
        const fcEl = document.querySelector(".fc"); if (!fcEl) return;
        if (settings.calendar_color === "black") { fcEl.style.backgroundColor = "#111"; fcEl.style.color = "#fff"; }
        else if (settings.calendar_color === "red") { fcEl.style.backgroundColor = "#ffefef"; fcEl.style.color = ""; }
        else { fcEl.style.backgroundColor = ""; fcEl.style.color = ""; }
      }
      function loadUserCalendarSettings() {
        fetch(ROUTE.calendarSettingsGet)
          .then(r => r.json())
          .then(({ calendar_settings }) => {
            const favs = (calendar_settings.favorite_employee_ids || calendar_settings.favorite_employees || []).map(String);
            S.favoriteEmployeeIds = favs;
            S.selectedEmployeeIds = new Set([...Array.from(S.selectedEmployeeIds), ...favs]);
            loadSettingsIntoModal(calendar_settings);
            applySettingsToCalendar(calendar_settings);
            fetchEmployees("", "employee");
          });
      }
      $("#calendarSettingsForm").on("submit", async function (e) {
        e.preventDefault();
        const $f = $(this), $btn = $f.find('button[type="submit"]');
        const settings = {
          favorite_employees: $("#favoriteEmployees").val() || [],
          hidden_views: $('input[name="hidden_views[]"]:checked').map((_, el) => el.value).get(),
          calendar_color: $("#calendarColorPicker").val(),
        };
        $btn.prop("disabled", true).text("Speichern…");
        try {
          const res = await fetch(ROUTE.calendarSettingsSave, {
            method: "POST", credentials: "same-origin",
            headers: { "Content-Type": "application/json", Accept: "application/json", "X-CSRF-TOKEN": CSRF() },
            body: JSON.stringify({ calendar_settings: settings }),
          });
          const text = await res.text(); let payload; try { payload = JSON.parse(text); } catch { payload = { raw: text }; }
          if (!res.ok) {
            const msg = payload?.message || payload?.error || (payload?.raw && payload.raw.slice(0, 200)) || `HTTP ${res.status}`;
            Swal.fire({ icon: "error", title: "Fehler", text: msg }); return;
          }
          if (payload.status === "success") {
            try { applySettingsToCalendar(settings); } catch { }
            $("#calendarSettingsModal").one("hidden.bs.modal", function () {
              Swal.fire({ icon: "success", title: "Gespeichert!", text: "Einstellungen wurden gespeichert.", timer: 1200, showConfirmButton: false })
                .then(() => location.reload());
            }).modal("hide");
          } else {
            const msg = payload?.message || (payload?.errors && Object.values(payload.errors).flat().join("\n")) || "Einstellungen konnten nicht gespeichert werden.";
            Swal.fire({ icon: "error", title: "Fehler", text: msg });
          }
        } catch (err) {
          Swal.fire({ icon: "error", title: "Netzwerkfehler", text: "Bitte erneut versuchen." });
        } finally {
          $btn.prop("disabled", false).text("Speichern");
        }
      });
      $("#calendarSettingsModal").on("shown.bs.modal", loadUserCalendarSettings);
      $("#favoriteEmployees").select2({ dropdownParent: $("#calendarSettingsModal"), width: "100%" });

      // =========================
      // Contact/ticket toggles + contacts select2
      // =========================
      $("input.contact-type-toggle").on("change", function () {
        const mode = $(this).val();
        $("#contact_mode").val(mode);

        if (mode === "new") {
          $(".contact-name-block").removeClass("d-none");
          $(".contact-select-block").addClass("d-none");
          $("#contactNameLabel").text("Kunde *");
        }

        if (mode === "select") {
          $(".contact-name-block").addClass("d-none");
          $(".contact-select-block").removeClass("d-none");
          $("#contactSelectLabel").text("Kunde *");
        }
      });

      $("input.contact-type-toggle:checked").trigger("change");

      function modelMatcher(params, data) {
        if ($.trim(params.term) === '') return data;
        if (typeof data.text === 'undefined') return null;

        const searchTerm = params.term.toLowerCase().trim();
        const optionText = data.text.toLowerCase().trim();

        return optionText.indexOf(searchTerm) > -1 ? data : null;
      }

      function resetCustomerProductAndAddressState() {
        S.selectedCustomerData = null;
        S.preselectedObjectId = "";
        S.productMap = {};

        $(".product-select-block").addClass("d-none");
        $("#productSelect").empty().trigger("change");
        $("#products").val("");
        $("#appointment_lead_product_list_id").val("");
        $("#address_source_type, #address_source_id, #selected_alternative_id").val("");
        clearAppointmentAddressChoices();

        if (typeof maToggleAppointmentStageCard === "function") maToggleAppointmentStageCard();
      }

      $(".contact_list").select2({
        placeholder: "Kunde suchen",
        allowClear: true,
        minimumInputLength: 0,
        matcher: modelMatcher,
        ajax: {
          url: ROUTE.contactList,
          type: "GET",
          dataType: "json",
          delay: 250,
          data: params => ({ search: (params.term || "").trim() }),
          processResults: data => ({
            results: $.map(data || [], item => {
              const street = cleanAppointmentAddressValue(item.street || "");
              const postcode = cleanAppointmentAddressValue(item.postcode || "");
              const city = cleanAppointmentAddressValue(item.city || "");
              const fullAddress = buildAppointmentFullAddress(street, postcode, city, item.full_address || "");

              const label = item.text || item.name || item.firma || `#${item.main_id || item.id}`;

              return {
                id: item.main_id || item.id,
                sub_id: null,
                alternative_id: null,
                text: label,
                type: "Kunde",
                phone: item.phone || "",
                email: item.email || "",
                street,
                postcode,
                city,
                longitude: item.longitude || "",
                latitude: item.latitude || "",
                full_address: fullAddress,
                object_name: "",
                customer_no: item.customer_no || "",
                firma: item.firma || ""
              };
            })
          })
        }
      }).on("select2:select", function (e) {
        const selected = e.params.data;

        S.selectedCustomerData = selected;
        S.preselectedObjectId = "";

        $("#contact_type").val("Kunde");
        $(".phone, #phone").val(selected.phone || "");
        $(".email, #email").val(selected.email || "");

        setAppointmentAddressFromOption({
          label: "Kundenadresse",
          source_type: "customer",
          source_id: selected.id,
          alternative_id: "",
          street: selected.street || "",
          postcode: selected.postcode || "",
          city: selected.city || "",
          latitude: selected.latitude || "",
          longitude: selected.longitude || "",
          full_address: selected.full_address || ""
        });

        if (selected.text) $("#name").val(selected.text);

        $(".product-select-block").removeClass("d-none");
        loadCustomerProducts(selected.id, undefined, selected);
      }).on("select2:clear", function () {
        $("#contact_type, .phone, .email, #phone, #email, #full_address, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input").val("");
        resetCustomerProductAndAddressState();
      }).on("select2:open", function () {
        $(".select2-search__field").attr("placeholder", "Kunde nach Name, Firma, Telefon oder E-Mail suchen");
      });

      // =========================
      // Misc
      // =========================
      $(document)
        .off('click.calendarClearAppointmentEmployees', '#btnClearEmployees')
        .on('click.calendarClearAppointmentEmployees', '#btnClearEmployees', function (e) {
          e.preventDefault();
          e.stopPropagation();

          // This button belongs to the appointment modal.
          // It must clear only the participant Select2, not the left sidebar employee filter.
          $('[id^="appointmentWrapper"]').hide();

          const $employee = $('#employee');
          if ($employee.length) {
            $employee.val([]).trigger('change.select2');
          }

          if (typeof window.feather !== 'undefined' && window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
          }
        });

      function selectCurrentUserOnly() {
        // Backward-compatible name, but now it only prepares appointment participants.
        // It no longer replaces the sidebar selected employees.
        const currentId = String(AUTH_EMPLOYEE_ID || '').trim();
        setAppointmentEmployeeSelection(currentId ? [currentId] : []);
      }

      document.addEventListener("click", e => {
        if (!e.target.classList.contains("show_new_task")) return;

        // Fully reset before opening
        document.getElementById("task-store-form").reset();
        maResetAppointmentStageFields();
        $("#appointment_id").val("");
        $("#address_source_type, #address_source_id, #selected_alternative_id").val("");
        clearAppointmentAddressChoices();
        $(".title").text("TERMIN ERSTELLEN");

        prepareNewAppointmentEmployeeSelection();
        if (D.newTaskCard) D.newTaskCard.style.display = "block";
      });

      document.addEventListener("change", function (e) {
        if (e.target.classList.contains("employee_check")) syncEmployeeUi(true);
      });

      function autoSelectFavoriteEmployees() {
        const favoriteIds = (S.favoriteEmployeeIds || []).map(String);
        if (!favoriteIds.length) return;

        selectEmployeeIds(favoriteIds, "replace", true);
        S.didAutoselectFavorites = true;
      }

      // =========================
      // Boot
      // =========================


      // =========================
      // Modern modal/collapsible cards + product-aware CRM Stage
      // =========================
      function maGetSelectedProductInfos() {
        const selected = $('#productSelect').val() || [];
        return selected.map(uid => S.productMap[uid]).filter(Boolean);
      }

      function maExtractLeadProductListId(info) {
        if (!info) return '';

        // Only explicit lead-product-list fields are allowed here.
        // Do NOT fall back to product uid/name/article-group id, because Laravel validates
        // lead_product_list_id as an integer from lead_product_lists.id.
        const candidates = [
          info.lead_product_list_id,
          info.leadProductListId,
          info.lead_product_list?.id,
          info.leadProductList?.id,
          info.pivot?.lead_product_list_id,
          info.meta?.lead_product_list_id,
        ];

        for (const candidate of candidates) {
          const id = maOnlyIntegerId(candidate);
          if (id) return id;
        }

        return '';
      }

      function maToggleAppointmentStageCard() {
        const mode = $('#contact_mode').val();
        const hasContact = mode === 'select' && !!($('#customer_id').val());
        const selectedProducts = $('#productSelect').val() || [];
        const hasProduct = selectedProducts.length > 0;
        const $block = $('#appointmentStageContactBlock');

        if (!$block.length) return;

        if (hasContact && hasProduct) {
          $block.removeClass('d-none').addClass('is-ready');
          $('#maStageProductStatus').html('<i class="feather icon-check-circle"></i> Produkt verbunden');
        } else if (hasContact) {
          $block.removeClass('d-none').removeClass('is-ready');
          $('#maStageProductStatus').html('<i class="feather icon-alert-circle"></i> Produkt wählen');
        } else {
          $block.addClass('d-none').removeClass('is-ready');
          $('#maStageProductStatus').html('<i class="feather icon-link"></i> Produkt erforderlich');
        }
      }

      async function maLoadStageContextFromSelectedProduct() {
        const infos = maGetSelectedProductInfos();
        const leadProductListId = maOnlyIntegerId(maExtractLeadProductListId(infos[0]));

        $('#appointment_lead_product_list_id').val(leadProductListId);
        maToggleAppointmentStageCard();

        if (!leadProductListId || !ROUTE.leadStageContext) {
          return;
        }

        try {
          const response = await $.ajax({
            url: ROUTE.leadStageContext,
            type: 'GET',
            dataType: 'json',
            data: { lead_product_list_id: maOnlyIntegerId(leadProductListId) }
          });

          const context = response?.context || response?.lead_stage_context || response || {};
          const stageId = context.lead_stage_id || '';
          const subStageId = context.lead_stage_sub_stage_id || '';

          if (stageId) {
            $('#appointment_lead_stage_id').val(String(stageId)).trigger('change.select2');
            await maRebuildAppointmentSubStages(subStageId ? String(subStageId) : '');
          } else {
            maRenderFormStagePreview();
          }
        } catch (e) {
          console.warn('Could not auto-load appointment stage from selected product:', e);
        }
      }

      function maInitCollapsibleAppointmentCards() {
        const $modal = $('.new_task');
        const $titles = $modal.find('.modal-body > .section-title');

        $titles.attr({ role: 'button', tabindex: '0', 'aria-expanded': 'true' });

        $titles.off('click.maCollapse keydown.maCollapse')
          .on('click.maCollapse', function () {
            const $title = $(this);
            const collapsed = !$title.hasClass('is-collapsed');
            $title.toggleClass('is-collapsed', collapsed).attr('aria-expanded', collapsed ? 'false' : 'true');
          })
          .on('keydown.maCollapse', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              $(this).trigger('click.maCollapse');
            }
          });

        $('#maCollapseSections').off('click.maCollapseAll').on('click.maCollapseAll', function () {
          $titles.addClass('is-collapsed').attr('aria-expanded', 'false');
        });

        $('#maExpandSections').off('click.maExpandAll').on('click.maExpandAll', function () {
          $titles.removeClass('is-collapsed').attr('aria-expanded', 'true');
        });
      }

      function maOpenImportantModalCards() {
        const importantTitles = ['Kontakt', 'Termin', 'Nachfass & nächste Schritte'];
        $('.new_task .modal-body > .section-title').each(function () {
          const title = $(this).text().trim();
          if (importantTitles.some(label => title.includes(label))) {
            $(this).removeClass('is-collapsed').attr('aria-expanded', 'true');
          }
        });
      }

      $(document)
        .off('change.maStageProductReady', '#productSelect')
        .on('change.maStageProductReady', '#productSelect', function () {
          maLoadStageContextFromSelectedProduct();
        });

      $(document)
        .off('change.maStageContactMode', 'input.contact-type-toggle')
        .on('change.maStageContactMode', 'input.contact-type-toggle', function () {
          setTimeout(maToggleAppointmentStageCard, 80);
        });

      $(document)
        .off('select2:select.maStageCustomer select2:clear.maStageCustomer', '#customer_id')
        .on('select2:select.maStageCustomer select2:clear.maStageCustomer', '#customer_id', function () {
          setTimeout(maToggleAppointmentStageCard, 120);
        });

      $(document)
        .off('click.maModalOpenState', '.create_new_task, .add_new_task, .fc-addEventButton-button')
        .on('click.maModalOpenState', '.create_new_task, .add_new_task, .fc-addEventButton-button', function () {
          $('html, body').addClass('ma-modal-open');
          setTimeout(function () {
            maInitCollapsibleAppointmentCards();
            maOpenImportantModalCards();
            initAppointmentStageSelect2();
            maInitCollapsibleAppointmentCards();
            maToggleAppointmentStageCard();
            maToggleAppointmentStageCard();
          }, 120);
        });

      $(document)
        .off('click.maModalCloseState', '.close_task_window, .save-task')
        .on('click.maModalCloseState', '.close_task_window, .save-task', function () {
          setTimeout(function () {
            if (!$('.new_task').is(':visible')) {
              $('html, body').removeClass('ma-modal-open');
            }
          }, 250);
        });

      (function boot() {
        S.favoriteEmployeeIds = (window.favoriteEmployeeIds || []).map(String);
        S.selectedEmployeeIds = new Set((S.favoriteEmployeeIds || []).map(String));

        initEmployeeSelect2();
        initAppointmentStageSelect2();
        maInitCollapsibleAppointmentCards();
        maToggleAppointmentStageCard();
        bindSearchControls();

        if (typeof autoSelectFavoriteEmployees === "function" && S.favoriteEmployeeIds.length) {
          autoSelectFavoriteEmployees();
        } else {
          loadCalendarTasks();
        }

        document.addEventListener("change", function (e) {
          if (e.target.classList.contains("employee_check") || e.target.classList.contains("employeeAppointment")) loadCalendarTasks();
        });
      })();


      // Keep body state in sync even when the legacy code opens/closes the custom modal directly.
      if (D.newTaskCard && !D.newTaskCard.dataset.maObserverAttached) {
        D.newTaskCard.dataset.maObserverAttached = '1';
        new MutationObserver(function () {
          const visible = $('.new_task').is(':visible');
          $('html, body').toggleClass('ma-modal-open', visible);
          if (visible) {
            maInitCollapsibleAppointmentCards();
            maToggleAppointmentStageCard();
          }
        }).observe(D.newTaskCard, { attributes: true, attributeFilter: ['style', 'class'] });
      }

    })();
  </script>


  <script>
    window.ALL_DEPARTMENTS = @json($departments ?? []);
    window.ALL_PRODUCTS = @json($products ?? []);
    window.ALL_SERVICES = @json($services ?? []);
  </script>

  <script>
    /* =========================================================
       Inquiry UI: placeholder + visibility tied to "Anfrage" switch
       ========================================================= */
    (function () {
      "use strict";

      function ensureInquiryPlaceholder() {
        const $tb = $("#inquiryPreviewBody");
        if (!$tb.find("tr").length) {
          $tb.html(
            '<tr data-placeholder="1">' +
            '<td colspan="6" class="text-center text-muted">' +
            'Bitte Produkt/Abteilung/Service wählen…' +
            '</td>' +
            '</tr>'
          );
        }
      }

      function clearInquiryPlaceholder() {
        $("#inquiryPreviewBody tr[data-placeholder='1']").remove();
      }

      function toggleInquiryWrapperVisibility() {
        const on = $("#switchContact").is(":checked");

        if (on) {
          // Anfrage ON:
          // - show inquiry table
          // - hide manual Teilnehmer block
          $("#inquiryPreviewWrapper").removeClass("d-none");
          $("#participantsBlock").addClass("d-none");

          ensureInquiryPlaceholder();
          if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
        } else {
          // Anfrage OFF:
          // - hide inquiry table
          // - show manual Teilnehmer block again
          $("#inquiryPreviewWrapper").addClass("d-none");
          $("#participantsBlock").removeClass("d-none");

          // If you want to fully reset when off, uncomment:
          // $("#inquiryPreviewBody").empty();
          // if (window.S) window.S.inquiryRowIndex = 0;
        }
      }


      // Bind + run on ready
      $(document).on("change", "#switchContact", toggleInquiryWrapperVisibility);
      $(toggleInquiryWrapperVisibility);

      // Clear placeholder whenever rows appear
      const tbody = document.getElementById("inquiryPreviewBody");
      if (tbody) {
        const mo = new MutationObserver(() => clearInquiryPlaceholder());
        mo.observe(tbody, { childList: true });
      }

      // If a previous fetch function exists, wrap it to ensure UI visibility
      if (window.fetchInquiryDepartmentEmployees) {
        const _origFetchInquiry = window.fetchInquiryDepartmentEmployees;
        window.fetchInquiryDepartmentEmployees = async function (selectedUids) {
          await _origFetchInquiry(selectedUids);
          if ($("#inquiryPreviewBody tr").length) clearInquiryPlaceholder();
          if ($("#switchContact").is(":checked")) {
            $("#inquiryPreviewWrapper").removeClass("d-none");
          }
        };
      }
    })();
  </script>

  <script>
    (function () {
      "use strict";

      // ---- Blade globals (already injected above this script) ----
      const ALL_DEPARTMENTS = Array.isArray(window.ALL_DEPARTMENTS) ? window.ALL_DEPARTMENTS : (window.ALL_DEPARTMENTS || []);
      const ALL_PRODUCTS = Array.isArray(window.ALL_PRODUCTS) ? window.ALL_PRODUCTS : (window.ALL_PRODUCTS || []);
      const ALL_SERVICES = Array.isArray(window.ALL_SERVICES) ? window.ALL_SERVICES : (window.ALL_SERVICES || []);

      // ---- Routes (merge if Script 1 set window.ROUTE) ----
      window.ROUTE = Object.assign({}, window.ROUTE || {}, {
        datasets: (window.ROUTE && window.ROUTE.datasets) || "/calendar/datasets",
      });

      // ---- Dataset service ----
      const DS = {
        _loading: null,
        departments: [],
        products: [],
        services: [],

        _normalizeArray(arr) {
          return (Array.isArray(arr) ? arr : []).map(o => {
            const id =
              o.id ??
              o.value ??
              o.department_id ??
              o.product_id ??
              o.service_id;

            const name =
              o.localized_name ??
              o.department_name ??
              o.article_group ??
              o.product_name ??
              o.phase_section ??
              o.title ??
              o.name ??
              `#${id ?? "?"}`;

            return { ...o, id, name };
          }).filter(x => x.id != null);
        },

        _seedFromBlade() {
          let seeded = false;
          if (ALL_DEPARTMENTS.length && !this.departments.length) {
            this.departments = this._normalizeArray(ALL_DEPARTMENTS);
            seeded = true;
          }
          if (ALL_PRODUCTS.length && !this.products.length) {
            this.products = this._normalizeArray(ALL_PRODUCTS);
            seeded = true;
          }
          if (ALL_SERVICES.length && !this.services.length) {
            this.services = this._normalizeArray(ALL_SERVICES);
            seeded = true;
          }
          return seeded;
        },

        async ensure() {
          if (this.departments.length || this.products.length || this.services.length) return;

          // 1) Seed from Blade (no network)
          const gotBlade = this._seedFromBlade();
          if (gotBlade) return;

          // 2) Fallback to API
          if (!window.ROUTE || !window.ROUTE.datasets) return;
          if (this._loading) return this._loading;

          this._loading = fetch(window.ROUTE.datasets, { headers: { Accept: "application/json" } })
            .then(r => r.ok ? r.json() : ({ departments: [], products: [], services: [] }))
            .then(j => {
              // Normalize and set only if still empty (prefer Blade if present later)
              const dep = this._normalizeArray(j.departments || []);
              const pro = this._normalizeArray(j.products || []);
              const svc = this._normalizeArray(j.services || []);
              if (!this.departments.length) this.departments = dep;
              if (!this.products.length) this.products = pro;
              if (!this.services.length) this.services = svc;
            })
            .catch(() => { /* keep current */ })
            .finally(() => { this._loading = null; });

          return this._loading;
        },

        depName(id) {
          const x = this.departments.find(d => String(d.id) === String(id));
          return x ? (x.localized_name || x.department_name || x.name || `Abteilung #${id}`) : `Abteilung #${id}`;
        },
        prodName(id) {
          const x = this.products.find(p => String(p.id) === String(id));
          return x ? (x.localized_name || x.article_group || x.name || x.product_name || `Produkt #${id}`) : `Produkt #${id}`;
        },
        svcName(id) {
          const x = this.services.find(s => String(s.id) === String(id));
          const raw = x ? (x.localized_name || x.name || x.phase_section || x.title || `Service #${id}`) : `Service #${id}`;
          return translateService(raw);
        },
      };

      // ---- Label normalization for services ----
      function translateService(raw) {
        const key = String(raw || "").toLowerCase().trim();
        const map = {
          complete: "Komplett", komplett: "Komplett", komplet: "Komplett",
          montage: "Montage", assembly: "Montage", einbau: "Montage", installation: "Montage",
          plan: "Planung", plane: "Planung", planung: "Planung", design: "Planung",
          repair: "Reparatur", reparatur: "Reparatur", fix: "Reparatur", instandsetzung: "Reparatur",
          maintenance: "Wartung", wartung: "Wartung",
          service: "Service",
          beratung: "Beratung", consulting: "Beratung",
          angebot: "Angebot", offer: "Angebot"
        };
        return map[key] || raw;
      }

      // ---- Option builders (use DS only; DS is seeded from Blade or API) ----
      function buildProductOptions() {
        return (DS.products || [])
          .map(p => `<option value="${p.id}">${DS.prodName(p.id)}</option>`)
          .join("");
      }
      function buildDepartmentOptions() {
        return (DS.departments || [])
          .map(d => `<option value="${d.id}">${DS.depName(d.id)}</option>`)
          .join("");
      }
      function buildServiceOptions() {
        return (DS.services || [])
          .map(s => {
            const label = translateService(s.localized_name || s.name || s.phase_section || s.title || `Service #${s.id}`);
            return `<option value="${s.id}">${label}</option>`;
          })
          .join("");
      }

      // ---- Row injection + UI wiring ----
      function appendInquiryRow(i, row, innHtml, outHtml) {
        const safeProduct = row.product_name || DS.prodName(row.product_id);
        const safeDept = row.department || DS.depName(row.department_id);
        const safeService = row.service_id ? DS.svcName(row.service_id) : translateService(row.service || "");

        const tr = `
                                                                      <tr data-index="${i}">
                                                                        <td>${safeProduct}
                                                                          <input type="hidden" name="inquiries[${i}][product_id]" value="${row.product_id || ""}">
                                                                        </td>
                                                                        <td>${safeDept}
                                                                          <input type="hidden" name="inquiries[${i}][department_id]" value="${row.department_id || ""}">
                                                                        </td>
                                                                        <td>${safeService}
                                                                          <input type="hidden" name="inquiries[${i}][service_id]" value="${row.service_id || ""}">
                                                                        </td>
                                                                        <td>
                                                                          <select name="inquiries[${i}][employee_id]" class="form-control inquiry-employee-select">
                                                                            <option value="">Innendienst wählen</option>${innHtml}
                                                                          </select>
                                                                        </td>
                                                                        <td>
                                                                          <select name="inquiries[${i}][field_employee_id]" class="form-control inquiry-field-employee-select">
                                                                            <option value="">Außendienst wählen</option>${outHtml}
                                                                          </select>
                                                                        </td>
                                                                        <td class="text-center" style="width:48px;">
                                                                          <button type="button" class="btn btn-sm btn-light remove-inquiry-row" title="Zeile entfernen">✕</button>
                                                                        </td>
                                                                      </tr>`;
        $("#inquiryPreviewBody").append(tr);
      }

      function hydrateInquiryTableUI() {
        $(".inquiry-employee-select, .inquiry-field-employee-select").select2({
          width: "100%",
          placeholder: "Mitarbeiter wählen",
          allowClear: true
        });

        $("#inquiryPreviewBody")
          .off("change.inquirySync", ".inquiry-employee-select, .inquiry-field-employee-select")
          .on("change.inquirySync", ".inquiry-employee-select, .inquiry-field-employee-select", function () {
            if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
          });
      }

      function reindexInquiryRows() {
        $("#inquiryPreviewBody tr").each(function (idx) {
          $(this).attr("data-index", idx);
          $(this).find("input[name*='inquiries[']").each(function () {
            this.name = this.name.replace(/inquiries\[\d+\]/, `inquiries[${idx}]`);
          });
          $(this).find("select[name*='inquiries[']").each(function () {
            this.name = this.name.replace(/inquiries\[\d+\]/, `inquiries[${idx}]`);
          });
        });
        if (window.S) window.S.inquiryRowIndex = $("#inquiryPreviewBody tr").length;
      }

      $(document).on("click", ".remove-inquiry-row", function () {
        $(this).closest("tr").remove();
        reindexInquiryRows();
        if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
      });

      // ---- Backend fetch for Innendienst/Außendienst per product ----
      async function fetchInquiryDepartmentEmployees(selectedUids) {
        await DS.ensure();

        if (!selectedUids || !selectedUids.length) {
          $("#inquiryPreviewWrapper").addClass("d-none");
          $("#inquiryPreviewBody").empty();
          if (window.S) window.S.inquiryRowIndex = 0;
          return;
        }

        const payload = selectedUids
          .map(uid => (window.S && window.S.productMap ? window.S.productMap[uid] : null))
          .filter(Boolean)
          .map(p => ({ product_id: p.product_id, alternative_id: p.alternative_id, customer_id: p.customer_id }));

        $.ajax({
          url: window.ROUTE.inquiryDeptEmployees,
          type: "POST",
          dataType: "json",
          data: { _token: $('meta[name="csrf-token"]').attr('content') || '', products: JSON.stringify(payload) },
          success(res) {
            const rows = res.data || res || [];
            const $tb = $("#inquiryPreviewBody");
            $tb.empty();

            if (!rows.length) {
              $("#inquiryPreviewWrapper").addClass("d-none");
              if (window.S) window.S.inquiryRowIndex = 0;
              return;
            }

            rows.forEach((row, i) => {
              const inn = (row.innendienst_employees || [])
                .map(e => `<option value="${e.id}">${(e.name || "")} ${(e.lastname || "")}</option>`).join("");
              const out = (row.aussendienst_employees || [])
                .map(e => `<option value="${e.id}">${(e.name || "")} ${(e.lastname || "")}</option>`).join("");
              appendInquiryRow(i, row, inn, out);
            });

            if (window.S) window.S.inquiryRowIndex = rows.length;

            hydrateInquiryTableUI();

            if ($("#switchContact").is(':checked')) {
              $("#inquiryPreviewWrapper").removeClass('d-none');
              if (window.updateParticipantsFromInquiry) window.updateParticipantsFromInquiry();
            } else {
              $("#inquiryPreviewWrapper").addClass('d-none');
            }
          },
          error(xhr) {
            console.error("inquiry.department.employees:", xhr.responseText);
            $("#inquiryPreviewWrapper").addClass("d-none");
            $("#inquiryPreviewBody").empty();
            if (window.S) window.S.inquiryRowIndex = 0;
          }
        });
      }

      // ---- “+” button: add an empty row built from DS (no backend roundtrip) ----
      $(document).on("click", "#addInquiryRow", async function () {
        await DS.ensure();
        $("#inquiryPreviewWrapper").removeClass("d-none");

        const index = window.S ? (window.S.inquiryRowIndex = (window.S.inquiryRowIndex || 0) + 1) - 1
          : $("#inquiryPreviewBody tr").length;

        // employees fallback: copy first row, else from #employee select
        const $tb = $("#inquiryPreviewBody");
        let inn = $tb.find("tr:first-child select.inquiry-employee-select").html() || "";
        let fld = $tb.find("tr:first-child select.inquiry-field-employee-select").html() || "";
        if (!inn || !fld) {
          const all = $("#employee option").map(function () {
            return `<option value="${this.value}">${$(this).text()}</option>`;
          }).get().join("");
          inn = inn || all;
          fld = fld || all;
        }

        // Build options from DS (seeded from Blade or API)
        const productOptions = buildProductOptions();
        const deptOptions = buildDepartmentOptions();
        const svcOptions = buildServiceOptions();

        const row = `
                                                                      <tr data-index="${index}">
                                                                        <td>
                                                                          <select name="inquiries[${index}][product_id]" class="form-control select2-inquiry inquiry-product">
                                                                            <option value="">Produkt wählen…</option>${productOptions}
                                                                          </select>
                                                                        </td>
                                                                        <td>
                                                                          <select name="inquiries[${index}][department_id]" class="form-control select2-inquiry inquiry-department">
                                                                            <option value="">Abteilung wählen…</option>${deptOptions}
                                                                          </select>
                                                                        </td>
                                                                        <td>
                                                                          <select name="inquiries[${index}][service_id]" class="form-control select2-inquiry inquiry-service">
                                                                            <option value="">Leistung/Service wählen…</option>${svcOptions}
                                                                          </select>
                                                                        </td>
                                                                        <td>
                                                                          <select name="inquiries[${index}][employee_id]" class="form-control inquiry-employee-select">
                                                                            <option value="">Innendienst wählen</option>${inn}
                                                                          </select>
                                                                        </td>
                                                                        <td>
                                                                          <select name="inquiries[${index}][field_employee_id]" class="form-control inquiry-field-employee-select">
                                                                            <option value="">Außendienst wählen</option>${fld}
                                                                          </select>
                                                                        </td>
                                                                        <td class="text-center" style="width:48px;">
                                                                          <button type="button" class="btn btn-sm btn-light remove-inquiry-row" title="Zeile entfernen">✕</button>
                                                                        </td>
                                                                      </tr>`;
        $("#inquiryPreviewBody").append(row);

        $(".select2-inquiry").select2({ width: "100%", placeholder: "Bitte wählen", allowClear: true });
        hydrateInquiryTableUI();
      });

      // ---- Trigger backend fetch when product multiselect (#productSelect) changes in inquiry mode ----
      $(document).on("change", "#productSelect", function () {
        if (!$('#switchContact').is(':checked')) return;
        const val = $(this).val() || [];
        if (!val.length) {
          $('#inquiryPreviewWrapper').addClass('d-none');
          $('#inquiryPreviewBody').empty();
          if (window.S) window.S.inquiryRowIndex = 0;
          return;
        }
        fetchInquiryDepartmentEmployees(val);
      });

      // ---- Prefill from backend snapshot on edit ----
      window.prefillInquiryFromSnapshot = async function (snapshot) {
        if (!Array.isArray(snapshot) || !snapshot.length) return;

        await DS.ensure();

        const $tb = $("#inquiryPreviewBody");
        $tb.empty();

        // Build generic employee option list from main Teilnehmer select
        const allEmpOptions = $("#employee option").map(function () {
          const text = $(this).text().trim();
          const val = this.value;
          if (!val) return "";
          return `<option value="${val}">${text}</option>`;
        }).get().join("");

        snapshot.forEach((item, idx) => {
          const row = {
            product_id: item.product_id || item.productId,
            department_id: item.department_id || item.departmentId,
            service_id: item.service_id || item.serviceId,
            service: null,
            product_name: null,
            department: null,
          };

          // Uses DS to resolve names
          appendInquiryRow(idx, row, allEmpOptions, allEmpOptions);

          const $row = $("#inquiryPreviewBody tr").last();
          if (item.employee_id) {
            $row.find("select.inquiry-employee-select").val(String(item.employee_id));
          }
          const fe = item.field_employee_id || item.field_employee;
          if (fe) {
            $row.find("select.inquiry-field-employee-select").val(String(fe));
          }
        });

        if (window.S) window.S.inquiryRowIndex = snapshot.length;

        hydrateInquiryTableUI();

        // Show inquiry block and sync Teilnehmer
        $("#inquiryPreviewWrapper").removeClass("d-none");
        if (window.updateParticipantsFromInquiry) {
          window.updateParticipantsFromInquiry();
        }
      };



      // Warm up DS so first click has data
      $(function () { DS.ensure(); });

      // Export (optional)
      window.DS = DS;
      window.translateService = translateService;
      window.fetchInquiryDepartmentEmployees = fetchInquiryDepartmentEmployees;
    })();
  </script>



  <script>
    var authUserName = "{{ auth()->user()->name }}";
  </script>
  <!-- Serach and Filter by Employee and Task Title, Date :end_date -->
  <!-- moving from menu to kalender tab  -->
  <script>
    $(document).ready(function () {
      // Check if the URL contains a hash
      if (window.location.hash) {
        let tabHash = window.location.hash;

        // Find the tab and activate it
        let targetTab = $(`a[href="${tabHash}"]`);
        if (targetTab.length) {
          targetTab.tab('show'); // Bootstrap's tab method to show the tab
        }
      }

      // Update the URL hash when switching tabs
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href");
        history.replaceState(null, null, target);
      });
    });
  </script>

  <!-- Information Popup  -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      document.addEventListener("click", function (event) {
        if (event.target.classList.contains("info_popup")) {
          let infoId = event.target.getAttribute("data-id");
          let infoType = event.target.getAttribute("data-type");

          fetch(`/get/info/${infoId}/${infoType}`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                let detailsTable = `
                                                                                        <table style="width:100%; border-collapse: collapse;">
                                                                                            <tr><th style="text-align:left; padding:5px;">Titel</th><td>${data.title}</td></tr>
                                                                                            <tr><th style="text-align:left; padding:5px;">Beschreibung</th><td>${data.description}</td></tr>
                                                                                            ${data.execution_type ? `<tr><th style="text-align:left; padding:5px;">Ausführungstyp</th><td>${data.execution_type}</td></tr>` : ""}
                                                                                            <tr><th style="text-align:left; padding:5px;">Startdatum</th><td>${data.start_date}</td></tr>
                                                                                            <tr><th style="text-align:left; padding:5px;">Enddatum</th><td>${data.end_date}</td></tr>
                                                                                            <tr><th style="text-align:left; padding:5px;">Startzeit</th><td>${data.start_time}</td></tr>
                                                                                            <tr><th style="text-align:left; padding:5px;">Endzeit</th><td>${data.end_time}</td></tr>
                                                                                        </table>
                                                                                    `;

                Swal.fire({
                  title: "Beschreibung",
                  html: detailsTable,
                  icon: "info",
                  confirmButtonText: "OK",
                  customClass: {
                    popup: 'swal-wide' // Optional: CSS class to widen the modal
                  }
                });
              } else {
                Swal.fire({
                  title: "Error",
                  text: data.message,
                  icon: "error",
                  confirmButtonText: "OK"
                });
              }
            })
            .catch(error => {
              console.error("Error fetching event info:", error);
              Swal.fire({
                title: "Error",
                text: "Something went wrong. Please try again.",
                icon: "error",
                confirmButtonText: "OK"
              });
            });
        }
      });

      document.addEventListener("click", function (e) {
        const clearBtn = e.target.closest("#clearSidebarEmployees");
        if (clearBtn) {
          e.preventDefault();

          if (typeof window.clearAllSidebarEmployees === "function") {
            window.clearAllSidebarEmployees();
          }

          return;
        }

        const selectAllBtn = e.target.closest("#selectAllSidebarEmployees");
        if (selectAllBtn) {
          e.preventDefault();

          if (typeof window.selectAllVisibleSidebarEmployees === "function") {
            window.selectAllVisibleSidebarEmployees();
          }
        }
      });
    });
  </script>
  <!-- Information Popup: end  -->

  <!-- show map:  -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      document.addEventListener("click", function (event) {
        if (event.target.classList.contains("show_map")) {
          let appointmentId = event.target.getAttribute("data-id");

          // Show loading dialog
          Swal.fire({
            title: "Fetching Location...",
            text: "Please wait while we load the map...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          fetch(`/get/map/${appointmentId}`, {
            method: "GET",
            headers: {
              "Content-Type": "application/json"
            }
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                let destination = {
                  lat: parseFloat(data.latitude),
                  lng: parseFloat(data.longitude)
                };

                if (navigator.geolocation) {
                  navigator.geolocation.getCurrentPosition(function (position) {
                    let origin = {
                      lat: position.coords.latitude,
                      lng: position.coords.longitude,
                    };

                    // Once the location is retrieved, show the map
                    showMapWithRoute(origin, destination, data.title);
                  }, function () {
                    Swal.fire("Error", "Could not get your location.", "error");
                  });
                } else {
                  Swal.fire("Error", "Geolocation is not supported by your browser.",
                    "error");
                }
              } else {
                Swal.fire("Error", data.message, "error");
              }
            })
            .catch(error => {
              console.error("Error fetching map data:", error);
              Swal.fire("Error", "Something went wrong. Please try again.", "error");
            });
        }
      });
    });

    // Function to show the map with route and open Google Maps button
    function showMapWithRoute(origin, destination, locationTitle) {
      let googleMapsAPIKey = "AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo"; // Replace with your Google API Key
      let mapContainer = document.createElement("div");
      mapContainer.id = "map";
      mapContainer.style = "width: 100%; height: 400px; margin-top: 10px;";

      // Replace loading message with actual map
      Swal.fire({
        title: `Termin: ${locationTitle}`,
        html: `<div id="map" style="width: 100%; height: 400px;"></div>
                                                                                <p><strong>Distance:</strong> <span id="distance"></span></p>
                                                                                <p><strong>Estimated Time:</strong> <span id="duration"></span></p>
                                                                                <a href="https://www.google.com/maps/dir/?api=1&origin=${origin.lat},${origin.lng}&destination=${destination.lat},${destination.lng}&travelmode=driving"
                                                                                    target="_blank" class="swal2-confirm swal2-styled">Open in Google Maps</a>`,
        icon: "info",
        didOpen: () => {
          let map = new google.maps.Map(document.getElementById("map"), {
            center: origin,
            zoom: 10,
          });

          let directionsService = new google.maps.DirectionsService();
          let directionsRenderer = new google.maps.DirectionsRenderer();
          directionsRenderer.setMap(map);

          directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING,
          },
            function (response, status) {
              if (status === "OK") {
                directionsRenderer.setDirections(response);
                let route = response.routes[0].legs[0];

                document.getElementById("distance").textContent = route.distance.text;
                document.getElementById("duration").textContent = route.duration.text;
              } else {
                console.error("Directions request failed due to " + status);
                Swal.fire("Error", "Could not get directions.", "error");
              }
            }
          );
        },
        width: 600,
        showCancelButton: false,
        showConfirmButton: false,
      });
    }
  </script>
  <!-- show map end  -->
  <!-- script for hidding the day and month drop down:  -->

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const startDateInput = document.getElementById("start_date");
      const weekSelect = document.getElementById("week_select");
      const weekDropdownContainer = document.getElementById("week_dropdown_container");
      const dateType = document.getElementById("date_type");

      function getWeekNumber(date) {
        const tempDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        const dayNum = tempDate.getUTCDay() || 7;
        tempDate.setUTCDate(tempDate.getUTCDate() + 4 - dayNum);
        const yearStart = new Date(Date.UTC(tempDate.getUTCFullYear(), 0, 1));
        return Math.ceil((((tempDate - yearStart) / 86400000) + 1) / 7);
      }

      function updateWeekDropdown() {
        const startDate = new Date(startDateInput.value);
        if (isNaN(startDate)) return;

        const currentWeek = getWeekNumber(startDate);
        const totalWeeks = 52;

        // Clear old options
        weekSelect.innerHTML = "";

        for (let i = currentWeek; i <= totalWeeks; i++) {
          const option = document.createElement("option");
          option.value = i;
          option.textContent = `Woche ${i}`;
          weekSelect.appendChild(option);
        }

        // Reinitialize Select2 for weekSelect (in case it was used)
        $('#week_select').select2({
          placeholder: "Wähle Woche(n)",
          allowClear: true
        });

        weekDropdownContainer.style.display = "block";
      }

      function toggleFields() {
        const selectedValue = $("#date_type").val();

        $(".from_day, .to_day, .from_month, .to_month").hide();
        $("#week_dropdown_container").hide();

        if (selectedValue === "daily") {
          $(".from_day, .to_day").show();
        } else if (selectedValue === "monthly") {
          $(".from_month, .to_month").show();
        } else if (selectedValue === "weekly") {
          if (startDateInput.value) {
            updateWeekDropdown();
          }
        }
      }

      // Setup event listeners
      $("#date_type").on("change", toggleFields);
      $("#start_date").on("change", function () {
        if ($("#date_type").val() === "weekly") {
          updateWeekDropdown();
        }
      });

      // Initial setup
      toggleFields();
    });
  </script>
  <!-- Color: start  -->
  <script>
    $(document).ready(function () {
      $('#color-select').select2({
        templateResult: formatColor,
        templateSelection: formatColor,
        escapeMarkup: function (markup) {
          return markup;
        }
      });

      function formatColor(color) {
        if (!color.id) {
          return color.text;
        }

        var colorValue = $(color.element).data('color');
        var colorName = color.text;

        var markup = `
                                                                          <div style="display: flex; align-items: center;">
                                                                              <span style="width: 15px; height: 15px; background: ${colorValue}; border-radius: 50%; margin-right: 8px;"></span>
                                                                              <span>${colorName}</span>
                                                                          </div>
                                                                      `;

        return markup;
      }
    });
  </script>
  <!-- moving from menu to kalender tab  -->
  <script>
    $(document).ready(function () {
      // Check if the URL contains a hash
      if (window.location.hash) {
        let tabHash = window.location.hash;

        // Find the tab and activate it
        let targetTab = $(`a[href="${tabHash}"]`);
        if (targetTab.length) {
          targetTab.tab('show'); // Bootstrap's tab method to show the tab
        }
      }

      // Update the URL hash when switching tabs
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let target = $(e.target).attr("href");
        history.replaceState(null, null, target);
      });
    });
  </script>

  <!-- Dupllicate: start  -->
  <!-- <script>
                                                              $.ajaxSetup({
                                                                  headers: {
                                                                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                                  }
                                                              });

                                                              $(document).on("click", ".duplicate-event", function(e) {
                                                                  e.preventDefault();
                                                                  const eventId = $(this).data("event-id");

                                                                  Swal.fire({
                                                                      title: "Duplizieren auf neues Datum",
                                                                      input: "date",
                                                                      inputLabel: "Wähle ein Datum",
                                                                      inputAttributes: {
                                                                          min: new Date().toISOString().split("T")[0]
                                                                      },
                                                                      showCancelButton: true,
                                                                      confirmButtonText: "Duplizieren",
                                                                      cancelButtonText: "Abbrechen",
                                                                      inputValidator: (value) => {
                                                                          if (!value) {
                                                                              return "Datum ist erforderlich!";
                                                                          }
                                                                      }
                                                                  }).then((result) => {
                                                                      if (result.isConfirmed) {
                                                                          const selectedDate = result.value;

                                                                          $.ajax({
                                                                              url: "{{ route('appointment.duplicate') }}",
                                                                              method: "POST",
                                                                              data: {
                                                                                  appointment_id: eventId,
                                                                                  new_date: selectedDate
                                                                              },
                                                                              success: function(response) {
                                                                                  Swal.fire("Erfolgreich!", response.message, "success").then(() => {
                                                                                      loadCalendarTasks(() => {
                                                                                          calendar.gotoDate(response.data
                                                                                          .start_date); // optional: scroll to new event
                                                                                      });
                                                                                  });
                                                                              },
                                                                              error: function(xhr) {
                                                                                  console.log(xhr.responseJSON);
                                                                                  if (xhr.status === 422) {
                                                                                      let errors = xhr.responseJSON.errors;
                                                                                      let errorMessages = Object.values(errors).map(errArr => errArr.join(
                                                                                          ', ')).join('<br>');
                                                                                      Swal.fire("Validierungsfehler", errorMessages, "error");
                                                                                  } else {
                                                                                      Swal.fire("Fehler!", "Unbekannter Serverfehler", "error");
                                                                                  }
                                                                              }
                                                                          });

                                                                      }
                                                                  });
                                                              });
                                                            </script> -->

  <!-- Dupllicate: end  -->
  <!-- Menu Close and Open Button: start  -->
  <script>
    $(document).ready(function () {
      // Show the .new_task when the "Erstellen" button is clicked
      $('.create_new_task').on('click', function () {
        $('.new_task').css({
          right: '-100%', // Start offscreen (adjust based on your layout)
          display: 'block', // Ensure it's visible
        }).animate({
          right: '0', // Slide into view
        }, 500); // Animation duration in ms
      });

      // Hide the .new_task when the "abbrechen" button is clicked
      $('.new_task').on('click', '.close_task_window', function () {
        $('.new_task').animate({
          right: '-100%', // Slide out of view
        }, 500, function () {
          $(this).hide(); // Hide after animation completes
        });
      });
    });
  </script>

  <script>
    document.addEventListener("keydown", function (event) {
      const newTaskDiv = document.querySelector(".new_task");

      if (event.key === "Escape" && newTaskDiv.style.display === "block") {
        newTaskDiv.style.display = "none"; // Hide the new_task div
      }
    });
  </script>
  <!-- Menu Close and Open Button: end  -->

  <!-- Priority Script  -->
  <script>
    $(document).ready(function () {
      // Add click event listener to each dropdown-item
      $('#color_drop_down .dropdown-item').on('click', function () {
        // Get the selected color value from the data-value attribute
        const selectedColor = $(this).data('value');

        // Update the hidden input value
        $('#color').val(selectedColor);

        // Update the icon's color
        $('#colorIcon').css('color', selectedColor);
      });


    });
  </script>

  <!-- Priority Script end  -->
  <!-- showing online Link:  -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const appointmentTypeDropdown = document.getElementById("execution_type");
      const internDiv = document.getElementById("intern");
      const externDiv = document.getElementById("extern");
      const linkDiv = document.getElementById("link_section");
      const branchSelect = document.querySelector("[name='branch_address_id']");
      const externInput = document.getElementById("full_address");

      function toggleSections() {
        const appointmentType = appointmentTypeDropdown.value;

        internDiv.style.display = "none";
        externDiv.style.display = "none";
        linkDiv.style.display = "none";

        resetHiddenInputs();

        if (appointmentType === "internal") {
          internDiv.style.display = "block";
          branchSelect.value = "";
        } else if (appointmentType === "external") {
          externDiv.style.display = "block";
        } else if (appointmentType === "online") {
          linkDiv.style.display = "block";
        } else if (appointmentType === "telephone") {
          // Do nothing for telephone appointments
        } else {
          externDiv.style.display = "block"; // Default to external
        }
      }

      function populateInternalAddress() {
        const selectedOption = branchSelect.options[branchSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
          resetHiddenInputs();
          return;
        }

        const branchStreet = selectedOption.getAttribute("data-street") || "";
        const branchCity = selectedOption.getAttribute("data-city") || "";
        const branchPostcode = selectedOption.getAttribute("data-postcode") || "";

        document.getElementById("full_address").value = buildAppointmentFullAddress(branchStreet, branchPostcode, branchCity, selectedOption.innerText || "");
        document.getElementById("street-input").value = cleanAppointmentAddressValue(branchStreet);
        document.getElementById("city-input").value = cleanAppointmentAddressValue(branchCity);
        document.getElementById("postal_code-input").value = cleanAppointmentAddressValue(branchPostcode);
        document.getElementById("latitude-input").value = selectedOption.getAttribute("data-latitude") || "";
        document.getElementById("longitude-input").value = selectedOption.getAttribute("data-longitude") || "";
      }

      function resetHiddenInputs() {
        document.getElementById("full_address").value = "";
        document.getElementById("street-input").value = "";
        document.getElementById("city-input").value = "";
        document.getElementById("postal_code-input").value = "";
        document.getElementById("latitude-input").value = "";
        document.getElementById("longitude-input").value = "";
      }

      // Ensure initializeAutocomplete is globally accessible
      window.initializeAutocomplete = function () {
        if (!externInput) return;

        const autocomplete = new google.maps.places.Autocomplete(externInput, {
          types: ['geocode'],
          componentRestrictions: {
            country: 'DE'
          }
        });

        autocomplete.addListener('place_changed', () => {
          const place = autocomplete.getPlace();

          if (!place.geometry) {
            console.error("No details available for input: '" + place.name + "'");
            return;
          }

          let street = "",
            streetNumber = "",
            city = "",
            postalCode = "",
            latitude = "",
            longitude = "";

          place.address_components.forEach(component => {
            const types = component.types;

            if (types.includes("route")) {
              street = component.long_name;
            }
            if (types.includes("street_number")) {
              streetNumber = component.long_name;
            }
            if (types.includes("locality") || types.includes("sublocality")) {
              city = component.long_name;
            }
            if (types.includes("postal_code")) {
              postalCode = component.long_name;
            }
          });

          latitude = place.geometry.location.lat();
          longitude = place.geometry.location.lng();

          const completeStreet = [street, streetNumber].filter(Boolean).join(" ").trim();

          // Populate inputs with external address data.
          // Visible field stays clean; coordinates stay hidden.
          document.getElementById("full_address").value = buildAppointmentFullAddress(completeStreet, postalCode, city, place.formatted_address || "");
          document.getElementById("street-input").value = completeStreet;
          document.getElementById("city-input").value = city;
          document.getElementById("postal_code-input").value = postalCode;
          document.getElementById("latitude-input").value = latitude;
          document.getElementById("longitude-input").value = longitude;
        });
      };

      function loadGoogleMapsAPI() {
        if (!window.google || !window.google.maps) {
          const script = document.createElement("script");
          script.src =
            "https://maps.googleapis.com/maps/api/js?key=AIzaSyByZgrvtQbWdEfRWf9hXRk4ZWiEP2mLFMk&libraries=places";
          script.async = true;
          script.defer = true;
          script.onload = function () {
            initializeAutocomplete();
          };
          document.head.appendChild(script);
        } else {
          initializeAutocomplete();
        }
      }

      appointmentTypeDropdown.addEventListener("change", toggleSections);
      branchSelect.addEventListener("change", populateInternalAddress);

      toggleSections();
      loadGoogleMapsAPI();
    });
  </script>
  <!-- Start Date and End date same value  -->

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const startDateInput = document.getElementById("start_date");
      const endDateInput = document.getElementById("end_date");

      function setEndDate() {
        if (!startDateInput.value) return; // If no start date, do nothing
        endDateInput.value = startDateInput.value; // Set end date to match start date
      }

      // Event listener to update end date when start date changes
      startDateInput.addEventListener("input", setEndDate);

      // Set default value on page load (if start date is already set)
      setEndDate();
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const startDateInput = document.getElementById("start_date");
      const startTimeInput = document.getElementById("start_time");
      const endTimeInput = document.getElementById("end_time");
      const totalTimeInput = document.getElementById("total_time");
      const endDateInput = document.getElementById("end_date");
      const dateTypeInput = $("#date_type"); // Select2 uses jQuery selector

      // Function to set default working hours when selecting "Whole Day"
      function setWholeDayTime() {
        if (dateTypeInput.val() === "day") {
          startTimeInput.value = "08:00";
          endTimeInput.value = "16:00";
          totalTimeInput.value = 8; // 8 hours total
        }
      }

      // Function to set total_time to 8 hours when start_date is selected
      function setDefaultTotalTime() {
        if (startDateInput.value) {
          totalTimeInput.value = 8; // Default 8 hours
          endDateInput.value = startDateInput.value; // Set end_date same as start_date
        }
      }

      // Function to calculate time difference in hours
      function calculateTotalTime() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (!startTime || !endTime) return;

        // Convert time to Date objects for calculation
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        // Ensure end time is after start time
        if (end < start) {
          showAlert("Fehler", "Endzeit muss nach der Startzeit liegen.", "error");
          endTimeInput.value = ""; // Reset end time
          return;
        }

        // Calculate difference in hours
        const diffInMs = end - start;
        const diffInHours = diffInMs / (1000 * 60 * 60); // Convert milliseconds to hours

        totalTimeInput.value = diffInHours.toFixed(2); // Display in hours

        // Validate if time is within working hours (06:00 - 19:00)
        const startHour = start.getHours();
        const endHour = end.getHours();

        if (startHour < 6 || startHour >= 19 || endHour < 6 || endHour >= 19) {
          showAlert(
            "Achtung!",
            "Ihre gewählte Zeit liegt außerhalb der Arbeitszeit (06:00 - 19:00 Uhr).",
            "warning"
          );
        }
      }

      // Function to show SweetAlert2 alerts
      function showAlert(title, text, icon) {
        Swal.fire({
          title: title,
          text: text,
          icon: icon,
          confirmButtonText: "OK"
        });
      }

      // Event Listeners
      startDateInput.addEventListener("change", setDefaultTotalTime);
      startTimeInput.addEventListener("change", calculateTotalTime);
      endTimeInput.addEventListener("change", calculateTotalTime);



      // Initialize values on page load
      setDefaultTotalTime();
    });
  </script>

  <!-- Start Date and End date same value : start -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const startDateInput = document.getElementById("start_date");
      const endDateInput = document.getElementById("end_date");

      function setEndDate() {
        if (!startDateInput.value) return; // If no start date, do nothing
        endDateInput.value = startDateInput.value; // Set end date to match start date
      }

      // Event listener to update end date when start date changes
      startDateInput.addEventListener("input", setEndDate);

      // Set default value on page load (if start date is already set)
      setEndDate();
    });
  </script>


  <script>
    $(document).ready(function () {
      $('#source').select2({
        tags: true,
        placeholder: "Quelle auswählen",
        allowClear: true
      });
    });
  </script>

  <script>
    function togglePreTypeAndSource() {
      const contactSwitch = document.getElementById('switchContact');
      const preTypeBox = document.getElementById('preTypeBox');
      const sourceBox = document.getElementById('sourceBox');

      const show = contactSwitch.checked;
      preTypeBox.style.display = show ? 'block' : 'none';
      sourceBox.style.display = show ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
      const contactSwitch = document.getElementById('switchContact');
      contactSwitch.addEventListener('change', togglePreTypeAndSource);
      togglePreTypeAndSource(); // Run on page load
    });
  </script>
  <script>
    $(document).ready(function () {
      $('#next_step').select2({
        placeholder: 'Nächster Schritt auswählen',
        allowClear: true,
        tags: true
      });

      $('#report_responsible').select2({
        placeholder: 'Nächster Schritt auswählen',
        allowClear: true
      });


    });
  </script>


  <script>
    (function () {
      // === Data sources (adjust routes if needed) ===============================
      // Server should return:
      //  GET /picker/employees?search= -> [{id, name, lastname, image}]
      //  GET /picker/teams?search=     -> [{id, name}]
      //  GET /picker/teams/{id}        -> {id, name, members: [{id, name, lastname, image, position}]}

      const ROUTES = {
        employees: "{{ route('picker.employees') }}",   // expects ?search=
        teams: "{{ route('picker.teams') }}",           // expects ?search=
        teamMembers: (id) => "{{ url('/picker/teams') }}/" + id
      };

      // Fallback: if you already have employees in blade, you can inline:

      const BOOT_EMPLOYEES = null; // leave null to use AJAX

      // Refs
      const $modal = $('#pickerModal');
      const $openBtn = $('#openPickerBtn');
      const $applyAll = $('#pickerApplyAll');

      const $empSearch = $('#pickerEmployeeSearch');
      const $empGrid = $('#pickerEmployeeGrid');

      const $teamSearch = $('#pickerTeamSearch');
      const $teamList = $('#pickerTeamList');
      const $teamMembers = $('#pickerTeamMembers');
      const $teamTitle = $('#pickerTeamTitle');
      const $teamSelectAll = $('#pickerSelectAllTeam');
      const $teamClear = $('#pickerClearTeam');
      const $teamApply = $('#pickerApplyTeam');

      // State
      window.selectedEmployeeIds = new Set(($('#employee').val() || []).map(String));
      let currentTeamId = null;
      let currentTeamMembers = []; // {id, name, lastname, image, position}

      // Utils
      const imgUrl = (img) => img ? `/images/employee/${img}` : `/images/employee/default.png`;
      const fullName = (e) => [e.name, e.lastname].filter(Boolean).join(' ');
      const posText = (pos) => pos ? ` — ${pos}` : '';

      function toggleId(set, id) {
        id = String(id);
        if (set.has(id)) set.delete(id); else set.add(id);
      }

      // === Employees Tab ========================================================

      function renderEmployeeGrid(list) {
        $empGrid.empty();
        if (!list || !list.length) {
          $empGrid.html('<div class="text-muted p-2">Keine Ergebnisse.</div>');
          return;
        }
        list.forEach(e => {
          const id = String(e.id);
          const active = window.selectedEmployeeIds.has(id) ? 'active' : '';
          const $chip = $(`
                                                                    <div class="picker-chip ${active}" data-id="${id}" title="${fullName(e)}">
                                                                      <img src="${imgUrl(e.image)}" class="picker-avatar" alt="">
                                                                      <span style="font-size:12px; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                                        ${fullName(e)}
                                                                      </span>
                                                                    </div>
                                                                  `);
          $chip.on('click', () => {
            toggleId(window.selectedEmployeeIds, id);
            $chip.toggleClass('active', selectedEmployeeIds.has(id));
          });
          $empGrid.append($chip);
        });
      }

      async function loadEmployees(search = '') {
        if (BOOT_EMPLOYEES && !search) return renderEmployeeGrid(BOOT_EMPLOYEES);
        const url = new URL(ROUTES.employees, window.location.origin);
        if (search) url.searchParams.set('search', search);
        const res = await fetch(url.toString());
        const json = await res.json();
        renderEmployeeGrid(json.data || []);
      }

      // === Teams Tab ============================================================

      function renderTeamList(list) {
        $teamList.empty();
        if (!list || !list.length) {
          $teamList.html('<div class="text-muted p-2">Keine Teams gefunden.</div>');
          return;
        }
        list.forEach(t => {
          const $item = $(`<div class="picker-list-item" data-id="${t.id}">${t.name}</div>`);
          $item.on('click', () => selectTeam(t.id, t.name));
          $teamList.append($item);
        });
      }

      function renderTeamMembers(members) {
        $teamMembers.empty();
        if (!members || !members.length) {
          $teamMembers.html('<div class="text-muted p-2">Keine Mitglieder.</div>');
          return;
        }
        const $wrap = $('<div class="d-flex flex-wrap" style="gap:8px;"></div>');
        members.forEach(m => {
          const id = String(m.id);
          const active = window.selectedEmployeeIds.has(id) ? 'active' : '';
          const $chip = $(`
                                                                    <div class="picker-chip ${active}" data-id="${id}" title="${fullName(m)}${posText(m.position)}">
                                                                      <img src="${imgUrl(m.image)}" class="picker-avatar" alt="">
                                                                      <div style="display:flex;flex-direction:column;line-height:1;">
                                                                        <span style="font-size:12px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${fullName(m)}</span>
                                                                        <small class="text-muted" style="font-size:10px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.position || ''}</small>
                                                                      </div>
                                                                    </div>
                                                                  `);
          $chip.on('click', () => {
            toggleId(window.selectedEmployeeIds, id);
            $chip.toggleClass('active', selectedEmployeeIds.has(id));
          });
          $wrap.append($chip);
        });
        $teamMembers.append($wrap);
      }

      async function loadTeams(search = '') {
        const url = new URL(ROUTES.teams, window.location.origin);
        if (search) url.searchParams.set('search', search);
        const res = await fetch(url.toString());
        const json = await res.json();
        renderTeamList(json.data || []);
      }

      async function selectTeam(id, name = 'Team') {
        currentTeamId = id;
        $teamTitle.text(name);
        const res = await fetch(ROUTES.teamMembers(id));
        const json = await res.json();
        currentTeamMembers = (json.members || []).map(m => ({
          id: m.id,
          name: m.name,
          lastname: m.lastname,
          image: m.image,
          position: m.position || (m.pivot && m.pivot.position) || null
        }));
        renderTeamMembers(currentTeamMembers);
      }

      // === Apply to #employee and allow edit afterwards =========================

      function ensureOptionInSelect2(id, text, image) {
        const $sel = $('#employee');
        const exists = $sel.find(`option[value="${id}"]`).length > 0;
        if (!exists) {
          const opt = new Option(text, id, true, true);
          $(opt).attr('data-image', imgUrl(image));
          $sel.append(opt);
        }
      }

      function applySelectionToEmployeeSelect() {
        const ids = Array.from(window.selectedEmployeeIds);
        // Ensure options exist
        // If you have an endpoint to resolve names by IDs, use it; otherwise we trust Select2 existing options
        ids.forEach(id => {
          // If option missing, create a generic label; your formatEmployee renderer shows avatar anyway
          if ($('#employee').find(`option[value="${id}"]`).length === 0) {
            ensureOptionInSelect2(id, `ID ${id}`, null);
          }
        });
        $('#employee').val(ids).trigger('change');
      }

      // === Wire up ==============================================================
      $openBtn.on('click', async () => {
        // Sync current selection from Select2 to chips
        selectedEmployeeIds = new Set(($('#employee').val() || []).map(String));

        // Default load Employees tab + Teams list
        await Promise.all([loadEmployees(''), loadTeams('')]);
        // If you want a default team selected, call selectTeam(firstId)
        $modal.modal('show');
      });

      // Search fields
      let empTimer = null;
      $empSearch.on('input', (e) => {
        clearTimeout(empTimer);
        empTimer = setTimeout(() => loadEmployees(e.target.value.trim()), 250);
      });

      let teamTimer = null;
      $teamSearch.on('input', (e) => {
        clearTimeout(teamTimer);
        teamTimer = setTimeout(() => loadTeams(e.target.value.trim()), 250);
      });

      // Team actions
      $teamSelectAll.on('click', () => {
        currentTeamMembers.forEach(m => selectedEmployeeIds.add(String(m.id)));
        renderTeamMembers(currentTeamMembers);
      });
      $teamClear.on('click', () => {
        currentTeamMembers.forEach(m => selectedEmployeeIds.delete(String(m.id)));
        renderTeamMembers(currentTeamMembers);
      });
      $teamApply.on('click', () => {
        // Ensure team members exist as options with their names + avatars
        currentTeamMembers.forEach(m => {
          ensureOptionInSelect2(String(m.id), fullName(m), m.image);
          selectedEmployeeIds.add(String(m.id));
        });
        applySelectionToEmployeeSelect();
        // Keep modal open so user can switch teams; or close if you prefer:
        // $modal.modal('hide');
      });

      // Apply all (from both tabs)
      $applyAll.on('click', () => {
        applySelectionToEmployeeSelect();
        $modal.modal('hide');
      });

    })();
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const calEl = document.getElementById('inquiry-mini-calendar');
      let calendar = new FullCalendar.Calendar(calEl, {
        initialView: 'timeGridWeek',
        locale: 'de',
        firstDay: 1,
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        height: 420,
        headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
        initialDate: new Date(),
        events: [] // we load programmatically
      });
      calendar.render();

      // --- helpers ---
      function gatherSelection() {
        const internal = new Set();
        const external = new Set();
        const dates = [];

        $('#inquiryProductTable tbody tr').each(function () {
          const idx = $(this).data('index');
          const inVal = $(`.employee-select[data-index="${idx}"]`).val();
          const outVal = $(`.field-employee-select[data-index="${idx}"]`).val();
          const dtVal = $(`.termin-input[data-index="${idx}"]`).val(); // datetime-local

          if (inVal && !isNaN(inVal)) internal.add(parseInt(inVal, 10));
          if (outVal && !isNaN(outVal)) external.add(parseInt(outVal, 10));
          if (dtVal) {
            const d = dtVal.split('T')[0];
            if (d) dates.push(d);
          }
        });

        let anchorDate = (dates.length ? dates.sort()[0] : new Date().toISOString().slice(0, 10));
        return {
          internal_ids: Array.from(internal),
          external_ids: Array.from(external),
          date: anchorDate
        };
      }

      // --- debounced refresher with stale-response guard ---
      let lastAnchor = null;
      let requestSeq = 0;   // increment per request
      let pendingSeq = 0;   // last request we care about

      const debounce = (fn, ms) => {
        let t;
        return function (...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), ms); };
      };

      const refreshCalendar = debounce(function () {
        const sel = gatherSelection();

        // Move calendar to correct week only if anchor changed
        if (sel.date !== lastAnchor) {
          lastAnchor = sel.date;
          calendar.gotoDate(sel.date);
        }

        calendar.removeAllEvents();

        // Nothing selected? show empty week (no fetch)
        if (!sel.internal_ids.length && !sel.external_ids.length) return;

        // Build URL with params
        const params = new URLSearchParams();
        sel.internal_ids.forEach(id => params.append('internal_ids[]', id));
        sel.external_ids.forEach(id => params.append('external_ids[]', id));
        params.append('date', sel.date);

        // Mark this request as the newest we care about
        const mySeq = ++requestSeq;
        pendingSeq = mySeq;

        $.getJSON('{{ route("inquiries.calendar.availability") }}?' + params.toString())
          .done(function (resp) {
            // Ignore stale responses
            if (mySeq !== pendingSeq) return;

            (resp.events || []).forEach(ev => calendar.addEvent(ev));
            if (resp.weekStart) calendar.gotoDate(resp.weekStart);
          })
          .fail(function (xhr) {
            // Ignore aborts / network hiccups
            if (xhr && xhr.statusText === 'abort') return;
            console.error('Calendar fetch error', xhr?.status, xhr?.responseText || xhr);
            if (window.toastr) toastr.error('Kalender konnte nicht geladen werden.');
          });
      }, 250);

      // --- bindings ---
      $(document).on('change', '.employee-select, .field-employee-select, .termin-input', refreshCalendar);
      $(document).on('click', '#addRow', () => setTimeout(refreshCalendar, 200));

      // first paint
      setTimeout(refreshCalendar, 300);
    });
  </script>

  <!-- Opening the Wizard of New Customer  -->
  <script>
    // 2. Define the Bridge Function that the Wizard calls on success
    window.openCalendarFromWizard = function (data) {
      // 1. Close Wizard safely (checks which closing function exists)
      if (typeof closeBladeWizard === 'function') {
        closeBladeWizard();
      } else if (typeof closeWizard === 'function') {
        closeWizard();
      }

      // 2. Ensure new_task modal is open and form is reset
      // --- FIX: SAVE THE CALENDAR DATES & TIMES BEFORE RESETTING ---
      let tempStartDate = $("#start_date").val();
      let tempEndDate = $("#end_date").val();
      let tempStartTime = $("#start_time").val();
      let tempEndTime = $("#end_time").val();
      let tempTotalTime = $("#total_time").val();

      document.getElementById("task-store-form").reset();
      if (typeof window.maResetAppointmentStageFields === "function") window.maResetAppointmentStageFields();

      // --- FIX: RESTORE THE CALENDAR DATES & TIMES AFTER RESETTING ---
      $("#start_date").val(tempStartDate);
      $("#end_date").val(tempEndDate);
      $("#start_time").val(tempStartTime);
      $("#end_time").val(tempEndTime);
      $("#total_time").val(tempTotalTime);

      $("#appointment_id").val("");
      $(".title").text("TERMIN ERSTELLEN");
      if (typeof window.prepareNewAppointmentEmployeeSelection === "function") {
        window.prepareNewAppointmentEmployeeSelection();
      }

      $('.new_task').css({
        right: '-100%',
        display: 'block'
      }).animate({
        right: '0',
      }, 500);

      // 3. Switch Contact mode to "select" (Kontakt-Auswahl)
      $("#contact_mode").val("select");
      $("input.contact-type-toggle[value='select']").prop("checked", true).trigger("change");

      // 4. Auto-fill the Customer in the Select2 dropdown
      let custName = `${data.lead.name || ''} ${data.lead.lastname || ''}`.trim();
      let $custSelect = $("#customer_id");

      // Add option if it doesn't exist yet, then select it
      if ($custSelect.find(`option[value="${data.lead.id}"]`).length === 0) {
        $custSelect.append(new Option(custName, data.lead.id, true, true));
      }
      $custSelect.val(data.lead.id).trigger("change");

      // 5. Unhide and Auto-fill the Products
      $(".product-select-block").removeClass("d-none");
      let $prodSelect = $("#productSelect");
      let productUids = [];
      let productsJson = [];

      if (data.products && data.products.length > 0) {
        data.products.forEach(p => {
          productUids.push(p.uid);
          productsJson.push(p);

          // Append product option dynamically if it doesn't already exist
          if ($prodSelect.find(`option[value="${p.uid}"]`).length === 0) {
            $prodSelect.append(new Option(p.name, p.uid, true, true));
          }
        });

        // Select items in Select2 
        $prodSelect.val(productUids).trigger("change");

        // CRITICAL: Update the hidden JSON field so the backend can process the products on save
        $("#products").val(JSON.stringify(productsJson));
      }

      // 6. Auto-fill Contact Info
      $(".phone").val(data.lead.phone || '');
      $(".email").val(data.lead.email || '');

      // 7. Auto-fill Address Fields (Using data.address from the updated controller)
      if (data.address) {
        const fullAddr = buildAppointmentFullAddress(
          data.address.street || '',
          data.address.postcode || '',
          data.address.city || '',
          data.address.full_address || ''
        );

        $("#full_address").val(fullAddr);
        $("#street-input").val(cleanAppointmentAddressValue(data.address.street || ''));
        $("#city-input").val(cleanAppointmentAddressValue(data.address.city || ''));
        $("#postal_code-input").val(cleanAppointmentAddressValue(data.address.postcode || ''));
        $("#latitude-input").val(data.address.latitude || '');
        $("#longitude-input").val(data.address.longitude || '');
        $("#address_source_type").val(data.address.source_type || 'object');
        $("#address_source_id").val(data.address.source_id || data.address.alternative_id || '');
        $("#selected_alternative_id").val(data.address.alternative_id || '');
      }

      // 8. Auto-fill "Art des Termins" (Appointment Type)
      $("#appointment_type").val("Neukunden-Termin");

      // 9. Auto-select Employees (Current User + Aussendienst/Innendienst from Wizard)
      if (data.employees && data.employees.length > 0) {
        // Collect currently selected (if any) and merge new employees to avoid overwriting
        let currentSelections = new Set($("#employee").val() || []);
        data.employees.forEach(empId => currentSelections.add(String(empId)));
        let newEmpArray = Array.from(currentSelections);

        // Update the main Select2
        $("#employee").val(newEmpArray).trigger("change");

        // Sync visual employee checkboxes/avatars in the UI panel
        if (typeof window.selectedEmployeeIds !== 'undefined') {
          window.selectedEmployeeIds = new Set(newEmpArray);
          $('.employee_check').each(function () {
            let empId = String($(this).data('id'));
            let isChecked = newEmpArray.includes(empId);
            $(this).prop('checked', isChecked);

            let img = document.getElementById(`employeeCheck${empId}`);
            if (img) {
              img.classList.toggle("emp_active", isChecked);
              img.style.borderColor = isChecked ? (img.dataset.color || "rgb(0, 159, 227)") : "transparent";
            }
          });
        }
      }

      // 10. Success notification
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Kunde, Produkte & Mitarbeiter geladen! Bitte Datum prüfen.',
          showConfirmButton: false,
          timer: 4000
        });
      }
    };
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
        label: 'Kalender',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush