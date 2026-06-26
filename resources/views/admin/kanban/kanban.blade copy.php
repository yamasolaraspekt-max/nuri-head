@extends('admin.layouts.app')
@section('title') PROZESS @stop

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
  <style>
    /* Appointment Tabs */


    /* --- New Appointment Drawer Styles --- */
    .ap-tabs {
      display: flex;
      background: #f8f9fa;
      border-bottom: 1px solid #e9ecef;
      padding: 0 1rem;
      flex-shrink: 0;
      /* Don't shrink */
    }

    .ap-tab-link {
      padding: 1rem 1.2rem;
      cursor: pointer;
      font-weight: 600;
      color: #6c757d;
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
    }

    .ap-tab-link:hover {
      color: #333;
      background: #f1f1f1;
    }

    .ap-tab-link.active {
      color: #93c21c;
      border-bottom-color: #93c21c;
      background: #fff;
    }

    /* Content Area */
    .ap-tab-content {
      display: none;
      flex-direction: column;
      height: calc(100vh - 120px);
      /* Fill remaining height */
      overflow-y: auto;
      position: relative;
    }

    .ap-tab-content.active {
      display: flex;
      /* Flex to allow calendar to grow */
    }

    /* Calendar Wrapper */
    #ap-calendar-wrap {
      flex: 1;
      /* Grow to fill space */
      padding: 15px;
      min-height: 500px;
    }

    /* FullCalendar Overrides */
    .fc-event {
      cursor: pointer;
      font-size: 0.85em;
    }

    .fc-header-toolbar {
      margin-bottom: 10px !important;
    }

    .fc-button {
      padding: 0.25rem 0.5rem !important;
      font-size: 0.85rem !important;
    }

    /* Card List (Grid) */
    #ap-card-view {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      padding: 15px;
    }

    @media (max-width: 900px) {
      #ap-card-view {
        grid-template-columns: 1fr;
      }
    }
  </style>
  <style>
    /* ======= Layout: main + right action rail ======= */
    .pro-layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 56px;
      gap: .75rem;
      align-items: start;
    }

    @media (max-width: 992px) {
      .pro-layout {
        grid-template-columns: 1fr 48px;
      }
    }

    .pro-rail {
      position: relative;
      width: 70px;
      height: 44px;
      color: #ffffff;
      display: grid;
      place-items: center;
      cursor: pointer;
      transition: transform .12s, background .12s, box-shadow .12s;
      right: 40px;
    }

    .rail-btn {
      position: relative;
      width: 44px;
      height: 44px;
      border: none;
      border-radius: 12px;
      background: #8fc73e;
      color: #333;
      display: grid;
      place-items: center;
      box-shadow: 0 1px 2px rgba(0, 0, 0, .08);
      cursor: pointer;
      transition: transform .12s, background .12s, box-shadow .12s;
    }

    .rail-btn:hover {
      transform: translateY(-1px);
      background: #eef1f6;
    }

    .rail-btn .feather {
      width: 20px;
      height: 20px;
    }

    .rail-btn--active {
      background: #e7f3d2;
      color: #2f5c00;
      box-shadow: 0 0 0 2px rgba(147, 194, 28, .25) inset;
    }

    .rail-badge {
      position: absolute;
      top: -6px;
      right: -6px;
      min-width: 18px;
      height: 18px;
      line-height: 18px;
      padding: 0 4px;
      border-radius: 10px;
      background: #93c21c;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      text-align: center;
    }

    .d-none {
      display: none !important;
    }

    a {
      border-radius: 0px !important;
    }

    /* ======= Drawer (single, no internal tabs) ======= */
    .drawer {
      position: fixed;
      inset: 0 0 0 auto;
      width: 480px;
      max-width: 92vw;
      transform: translateX(100%);
      transition: transform .22s ease;
      background: #fff;
      box-shadow: -12px 0 30px rgba(0, 0, 0, .12);
      z-index: 1080;
      display: flex;
      flex-direction: column;
    }

    .drawer.open {
      transform: translateX(0);
    }

    .drawer-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 14px;
      border-bottom: 1px solid #e5e7eb;
    }

    .drawer-body {
      padding: 14px;
      overflow: auto;
    }

    .drawer-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .25);
      opacity: 0;
      pointer-events: none;
      transition: opacity .2s ease;
      z-index: 1075;
    }

    .drawer-backdrop.show {
      opacity: 1;
      pointer-events: auto;
    }

    /* chips */
    .chips {
      display: flex;
      gap: .4rem;
      flex-wrap: wrap;
      align-items: center;
      margin: 6px 2px 0 2px;
    }

    .chip {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      padding: .15rem .5rem;
      background: #eef2f7;
      border-radius: 999px;
      font-size: .85rem;
    }

    .chip .x {
      cursor: pointer;
      opacity: .7;
    }

    .chip .x:hover {
      opacity: 1;
    }

    /* small badge inside title */
    .tab-badge-inline {
      margin-left: .4rem;
      padding: .05rem .35rem;
      border-radius: 10px;
      font-size: .75rem;
      background: #93c21c;
      color: #fff;
      font-weight: 700;
    }

    /* ======= Kanban ======= */
    .card {
      background: #fff;
      padding: 15px;
      margin: 10px 0;
      border-left: 5px solid #74b2d4;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, .2);
      cursor: grab;
      user-select: none;
      display: flex;
      flex-direction: column;
      gap: 4px;
      position: relative;
    }

    .card .card-header {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      justify-content: space-between;
      border-bottom: none;
      padding: 1px;
      background: transparent;
      font-size: 13px;
      text-transform: uppercase;
    }

    .card .circle {
      min-width: 42px;
      height: 24px;
      padding: 0 9px;
      border-radius: 7px;
      background: #b0d5f2;
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: 900;
      font-size: 11px;
      line-height: 1;
      letter-spacing: .03em;
      position: absolute;
      top: 4px;
      right: 5px;
      box-shadow: 0 2px 6px rgba(15, 23, 42, .14);
      white-space: nowrap;
    }

    .card.selected {
      background: #d1ecf1;
      border-left: 5px solid #17a2b8;
    }

    .card-actions {
      display: flex;
      gap: .25rem;
      justify-content: space-between;
      align-items: center;
      padding-top: 6px;
      z-index: 5;
    }

    .btn-icon {
      border: none;
      background: none;
      cursor: pointer;
      font-size: 18px;
      line-height: 1;
      padding: .35rem .45rem;
      border-radius: 10px;
      color: #7b93a7;
      transition: transform .12s, background .15s, color .15s;
    }

    .btn-icon:hover {
      transform: translateY(-1px);
      background: rgba(0, 0, 0, .04);
    }

    .btn-icon.is-active {
      box-shadow: inset 0 0 0 2px rgba(0, 0, 0, .06);
      background: rgba(0, 0, 0, .03);
    }

    .btn-play {
      color: #95c11f !important;
    }

    .card.status-playing {
      border-left-color: #95c11f !important;
    }

    .card.status-paused {
      border-left-color: #f3c12f !important;
    }

    .card.status-stopped {
      border-left-color: #c93a3a !important;
    }

    .card .card-status-overlay {
      position: absolute;
      inset: 0;
      backdrop-filter: blur(1.5px);
      background: rgba(255, 255, 255, .35);
      border-radius: 8px;
      display: none;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 10px;
      z-index: 3;
      pointer-events: none;
    }

    .card.card-has-overlay .card-status-overlay {
      display: flex;
    }

    .card .card-status-badge {
      display: inline-flex;
      gap: .4rem;
      align-items: center;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
      font-size: .85rem;
      padding: .35rem .6rem;
      border-radius: 14px;
      background: #eee;
      color: #555;
      box-shadow: 0 1px 2px rgba(0, 0, 0, .08);
    }

    .card.status-paused .card-status-badge {
      background: #fff4d6;
      color: #8a6d00;
    }

    .card.status-stopped .card-status-badge {
      background: #ffe2e2;
      color: #8a1f1f;
    }

    /* Kanban columns */
    .kanban-container {
      display: flex;
      gap: 0;
      overflow-x: auto;
      overflow-y: hidden;
      padding-bottom: 10px;
      align-items: stretch;
    }

    .column {
      background: #f1f1f1;
      width: 360px;
      min-width: 360px;
      flex: 0 0 360px;
      height: 1000px;
      display: flex;
      flex-direction: column;
      border-right: 2px dashed #c0baba;
      position: relative;
    }

    .column h3 {
      position: sticky;
      top: 0;
      z-index: 1;
      background: #95c11f;
      color: #fff;
      padding: 0;
      font-size: 17px;
      text-align: center;
      text-transform: uppercase;
      font-weight: bold;
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      justify-content: flex-start;
      gap: 0;
      white-space: normal;
      min-height: 72px;
      overflow: hidden;
    }

    .kb-column-head-left {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 8px 8px 6px;
      min-height: 36px;
      border-bottom: 1px solid rgba(255, 255, 255, .22);
    }

    .kb-column-actions {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      flex-wrap: wrap;
      padding: 5px 6px 7px;
      background: rgba(15, 23, 42, .10);
    }

    .kb-column-title {
      min-width: 0;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 15px;
      line-height: 1.2;
      letter-spacing: .03em;
      text-align: center;
    }

    .kb-column-title .feather {
      flex: 0 0 auto;
    }

    .column-content {
      overflow-y: auto;
      flex-grow: 1;
      padding: 10px;
    }

    .count-badge {
      background: #93c21c;
      color: #fff;
      font-size: .8rem;
      padding: 2px 8px;
      border-radius: 12px;
      margin-left: .5rem;
      font-weight: 600;
    }

    .kb-header-counts {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 4px;
      flex: 0 0 auto;
      flex-wrap: nowrap;
      margin-left: 6px;
      white-space: nowrap;
    }

    .kb-count-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 3px;
      min-width: 24px;
      height: 22px;
      padding: 0 6px;
      border-radius: 999px;
      font-size: 11px;
      line-height: 1;
      font-weight: 900;
      color: #fff;
      background: rgba(15, 23, 42, .22);
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .25);
    }

    .kb-count-pill--total {
      background: rgba(15, 23, 42, .28);
    }

    .kb-count-pill--green {
      background: #95c11f;
    }

    .kb-count-pill--orange {
      background: #f3c12f;
      color: #382b00;
    }

    .kb-count-pill--red {
      background: #c93a3a;
    }

    .kb-count-dot {
      width: 7px;
      height: 7px;
      border-radius: 999px;
      display: inline-block;
      background: currentColor;
      box-shadow: 0 0 0 2px rgba(255, 255, 255, .25);
    }

    .kb-count-pill--orange .kb-count-dot {
      background: #8a6d00;
    }

    .kanban-zoom-card.kb-compact .kb-header-counts {
      gap: 3px;
      margin-left: 4px;
    }

    .kanban-zoom-card.kb-compact .kb-count-pill {
      min-width: 20px;
      height: 18px;
      padding: 0 5px;
      font-size: 9px;
    }

    /* ===== Responsive Kanban zoom / compact mode ===== */
    .kanban-zoom-card {
      --kb-zoom: 1;
      --kb-card-font: 1;
      background: #fff;
      border: 1px solid #e5eef5;
      border-radius: 18px;
      overflow: hidden;
    }

    .kanban-zoom-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 12px;
      border-bottom: 1px solid #e5eef5;
      background: linear-gradient(135deg, #f8fafc 0%, #eef7fb 100%);
      flex-wrap: wrap;
    }

    .kanban-zoom-title {
      display: block;
      font-size: 13px;
      font-weight: 900;
      color: #0f172a;
    }

    .kanban-zoom-sub {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: #64748b;
      margin-top: 2px;
    }

    .kanban-zoom-actions {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .kbz-btn {
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 12px;
      height: 32px;
      min-width: 48px;
      padding: 0 10px;
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      transition: .15s ease;
    }

    .kbz-btn:hover,
    .kbz-btn.is-active {
      background: #74b2d4;
      color: #fff;
      border-color: #74b2d4;
      transform: translateY(-1px);
    }

    .kbz-btn--ghost {
      min-width: 36px;
      padding: 0 8px;
    }

    .kbz-compact-toggle {
      margin: 0;
      height: 32px;
      padding: 0 10px;
      border: 1px solid #dbeafe;
      background: #fff;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #334155;
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
    }

    .kbz-compact-toggle input {
      margin: 0;
    }

    .kanban-zoom-area {
      overflow: auto;
      width: 100%;
      min-height: calc(100vh - 230px);
      padding: 8px;
      background: #f8fafc;
    }

    .kanban-zoom-area .kanban-container {
      zoom: var(--kb-zoom);
      min-width: max-content;
    }

    .kanban-zoom-card.kb-compact .column {
      width: 305px;
      min-width: 305px;
      flex-basis: 305px;
      height: calc(100vh - 250px);
      min-height: 620px;
    }

    .kanban-zoom-card.kb-compact .column h3 {
      font-size: 13px;
      padding: 5px 7px;
      gap: 6px;
    }

    .kanban-zoom-card.kb-compact .column-toolbar {
      padding: 5px 6px;
    }

    .kanban-zoom-card.kb-compact .col-search-input {
      height: 24px;
      font-size: 10px;
    }

    .kanban-zoom-card.kb-compact .card {
      padding: 9px;
      margin: 7px 0;
      font-size: 12px;
      border-radius: 7px;
    }

    .kanban-zoom-card.kb-compact .card .card-header {
      font-size: 11px;
      padding-right: 28px;
    }

    .kanban-zoom-card.kb-compact .card .circle {
      min-width: 34px;
      height: 20px;
      padding: 0 7px;
      border-radius: 6px;
      font-size: 9px;
      top: 4px;
      right: 4px;
    }

    .kanban-zoom-card.kb-compact .btn-icon {
      font-size: 15px;
      padding: .25rem .35rem;
    }

    .kanban-zoom-card.kb-compact .kb-status {
      padding: .35rem .45rem;
      font-size: 11px;
    }

    .kanban-zoom-card.kb-compact .live-feed-bar {
      padding: 3px 6px;
      font-size: 10px;
    }

    @media (max-width: 991px) {
      .kanban-zoom-toolbar {
        align-items: stretch;
      }

      .kanban-zoom-left {
        width: 100%;
      }

      .kanban-zoom-actions {
        width: 100%;
      }

      .kbz-btn {
        flex: 1 1 auto;
      }

      .kanban-zoom-card.kb-compact .column {
        width: 295px;
        min-width: 295px;
        flex-basis: 295px;
      }
    }

    /* List: table + sort */
    th.sortable {
      cursor: pointer;
      user-select: none;
      white-space: nowrap;
    }

    th.sortable .sort-icon {
      font-size: .8rem;
      opacity: .4;
      transition: transform .2s, opacity .2s;
    }

    th.sortable.active .sort-icon {
      opacity: 1;
    }

    th.sortable.desc .sort-icon {
      transform: rotate(180deg);
    }

    /* Tooltips (custom) */
    .tooltip-trigger {
      cursor: pointer;
      display: inline-block;
      position: relative;
    }

    .tooltip-trigger .custom-tooltip {
      position: absolute;
      bottom: 130%;
      left: 50%;
      transform: translateX(-50%);
      background: #93c21c;
      color: #fff;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 12px;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity .15s, transform .15s;
      z-index: 50;
    }

    .tooltip-trigger:hover .custom-tooltip {
      opacity: 1;
      transform: translateX(-50%) translateY(-2px);
    }

    .tooltip-trigger .custom-tooltip::after {
      content: '';
      position: absolute;
      top: 100%;
      left: 50%;
      margin-left: -4px;
      border-width: 4px;
      border-style: solid;
      border-color: #93c21c transparent transparent transparent;
    }

    /* Priority dots */
    .prio-dot,
    .new-dot,
    .late-dot {
      font-size: 16px;
      vertical-align: middle;
    }

    .prio-high {
      color: #dc3545;
    }

    .prio-normal {
      color: #93c21c;
    }

    .prio-low {
      color: #6c757d;
    }

    .new-dot {
      color: #ffc107;
    }

    .late-dot {
      color: #f45b69;
    }

    /* Summary cards */
    .summary-card {
      cursor: pointer;
      transition: transform .15s ease;
      position: relative;
    }

    .summary-card:hover {
      transform: translateY(-2px);
    }

    .summary-card.active>div {
      border: 2px solid #93c21c !important;
      box-shadow: 0 0 6px rgba(147, 194, 28, .6);
    }

    .summary-card.active::after {
      content: "ausgewählt";
      position: absolute;
      bottom: -18px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 12px;
      color: #93c21c;
    }

    /* Misc */
    .tab-icon {
      width: 16px;
      height: 16px;
      margin-right: 6px;
      vertical-align: -2px;
    }

    .table thead th {
      vertical-align: bottom;
    }

    .timeline-item {
      list-style: none;
    }

    /* --- Kanban status block --- */
    .kb-status {
      margin-top: .35rem;
      border: 1px dashed rgba(0, 0, 0, .08);
      background: #fafbfc;
      border-radius: 10px;
      padding: .45rem .55rem;
    }

    .kb-status .badge {
      font-weight: 700;
      letter-spacing: .2px;
    }

    .kb-status .meta {
      display: grid;
      gap: .25rem;
      margin-top: .35rem;
      grid-template-columns: 1.1rem 1fr;
      align-items: start;
      font-size: .82rem;
      color: #5b6470;
    }

    .kb-status .meta i.feather {
      width: 16px;
      height: 16px;
      opacity: .75;
    }

    .kb-status .rowline {
      display: contents;
    }

    /* keep grid semantics */
    .kb-status .value {
      line-height: 1.2;
      word-break: break-word;
    }

    .kb-status .muted {
      opacity: .7;
    }

    .kb-status .time {
      font-variant-numeric: tabular-nums;
    }

    .kb-status {
      outline: 1px dashed rgba(147, 194, 28, .35);
    }

    /* put this near your existing .kb-status / overlay styles */
    .card .card-status-overlay {
      z-index: 1;
    }

    /* keep overlay below content */
    .kb-status {
      position: relative;
      z-index: 2;
    }

    /* ensure the block sits on top */


    /* ===== Kanban Stage Time / Duration ===== */
    .kb-stage-time {
      margin-top: 7px;
      padding: 7px 8px;
      border: 1px solid #dbeafe;
      background: linear-gradient(135deg, #f8fafc 0%, #eef7fb 100%);
      border-radius: 12px;
      display: flex;
      flex-direction: column;
      gap: 4px;
      font-size: 11px;
      color: #334155;
      position: relative;
      z-index: 2;
    }

    .kb-stage-time-row {
      display: flex;
      align-items: center;
      gap: 6px;
      line-height: 1.25;
    }

    .kb-stage-time-row .feather {
      width: 14px;
      height: 14px;
      color: #74b2d4;
    }

    .kb-stage-time strong {
      font-weight: 900;
      color: #0f172a;
    }

    .kanban-zoom-card.kb-compact .kb-stage-time {
      padding: 5px 6px;
      font-size: 10px;
      border-radius: 10px;
    }


    /* ===== Offer/Auftrag workflow status inside Lead Kanban card ===== */
    .kb-offer-workflow {
      margin-top: 8px;
      border: 1px solid #dbeafe;
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border-radius: 13px;
      padding: 0;
      position: relative;
      z-index: 2;
      overflow: hidden;
    }

    .kb-offer-workflow summary {
      list-style: none;
      cursor: pointer;
    }

    .kb-offer-workflow summary::-webkit-details-marker {
      display: none;
    }

    .kb-offer-workflow-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 8px 9px;
    }

    .kb-offer-workflow-left {
      display: flex;
      align-items: center;
      gap: 7px;
      min-width: 0;
    }

    .kb-offer-workflow-chevron {
      width: 18px;
      height: 18px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #eef7fb;
      color: #334155;
      flex: 0 0 auto;
      transition: transform .18s ease;
    }

    .kb-offer-workflow[open] .kb-offer-workflow-chevron {
      transform: rotate(90deg);
    }

    .kb-offer-workflow-title {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .04em;
      white-space: nowrap;
    }

    .kb-offer-workflow-title .feather {
      width: 14px;
      height: 14px;
      color: #74b2d4;
    }

    .kb-offer-workflow-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      max-width: 58%;
      border-radius: 999px;
      padding: 5px 8px;
      font-size: 11px;
      font-weight: 900;
      color: #fff;
      line-height: 1.2;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .kb-offer-workflow-body {
      display: flex;
      flex-direction: column;
      gap: 4px;
      font-size: 11px;
      color: #475569;
      line-height: 1.35;
      padding: 0 9px 9px;
      border-top: 1px solid #eaf2fb;
    }

    .kb-offer-workflow-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .kb-offer-workflow-label {
      color: #64748b;
      font-weight: 800;
    }

    .kb-offer-workflow-value {
      color: #0f172a;
      font-weight: 900;
      text-align: right;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .kb-offer-workflow-open {
      margin-top: 6px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      width: max-content;
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 999px;
      padding: 4px 8px;
      font-size: 11px;
      font-weight: 900;
      text-decoration: none;
    }

    .kb-offer-workflow-open:hover {
      background: #eef7fb;
      color: #0f172a;
      text-decoration: none;
    }

    .kb-offer-workflow-empty {
      margin-top: 8px;
      border: 1px dashed #cbd5e1;
      background: #f8fafc;
      color: #64748b;
      border-radius: 12px;
      padding: 7px 8px;
      font-size: 11px;
      font-weight: 800;
      line-height: 1.35;
    }

    .kanban-zoom-card.kb-compact .kb-offer-workflow {
      border-radius: 11px;
    }

    .kanban-zoom-card.kb-compact .kb-offer-workflow-head {
      padding: 6px 7px;
    }

    .kanban-zoom-card.kb-compact .kb-offer-workflow-body {
      padding: 0 7px 7px;
    }

    .kanban-zoom-card.kb-compact .kb-offer-workflow-title,
    .kanban-zoom-card.kb-compact .kb-offer-workflow-status,
    .kanban-zoom-card.kb-compact .kb-offer-workflow-body,
    .kanban-zoom-card.kb-compact .kb-offer-workflow-open {
      font-size: 10px;
    }

    /* ===== Offer/Auftrag status inside Lead List View ===== */
    .table .kb-offer-workflow {
      margin-top: 7px;
      max-width: 260px;
      min-width: 190px;
      border-radius: 12px;
      background: #ffffff;
    }

    .table .kb-offer-workflow-head {
      padding: 6px 8px;
    }

    .table .kb-offer-workflow-title,
    .table .kb-offer-workflow-status,
    .table .kb-offer-workflow-body,
    .table .kb-offer-workflow-open {
      font-size: 10px;
    }

    .table .kb-offer-workflow-status {
      max-width: 145px;
      padding: 4px 7px;
    }

    .table .kb-offer-workflow-body {
      padding: 0 8px 8px;
    }

    .table .kb-offer-workflow-empty {
      max-width: 260px;
      min-width: 190px;
      margin-top: 7px;
      font-size: 10px;
    }


    /* ===== Kanban stage team blocks ===== */
    .kb-stage-teams {
      margin-top: 8px;
      border-top: 1px solid #e5e7eb;
      padding-top: 8px;
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .kb-stage-team-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 6px 8px;
      border: 1px solid #eef2f7;
      border-radius: 12px;
      background: #ffffff;
    }

    .kb-stage-team-label {
      font-size: 11px;
      font-weight: 900;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: .04em;
      min-width: 74px;
    }

    .kb-stage-team-avatars {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      min-width: 0;
      flex: 1;
    }

    .kb-stage-team-avatars .avatar {
      margin-left: -7px;
    }

    .kb-stage-team-more {
      border: 0;
      background: #f1f5f9;
      color: #334155;
      min-width: 28px;
      height: 28px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 900;
      margin-left: 4px;
      cursor: pointer;
    }

    .kb-stage-team-more:hover {
      background: #e2e8f0;
    }

    .swal-stage-team-grid {
      display: flex;
      flex-direction: column;
      gap: 8px;
      max-height: 260px;
      overflow: auto;
      padding-right: 4px;
    }

    .swal-stage-team-row {
      display: grid;
      grid-template-columns: 120px minmax(0, 1fr);
      gap: 10px;
      align-items: start;
      padding: 8px 10px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #fff;
    }

    .swal-stage-team-title {
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
    }

    .swal-stage-team-member {
      font-size: 12px;
      color: #334155;
      line-height: 1.35;
      padding-bottom: 4px;
    }

    .swal-stage-team-member+.swal-stage-team-member {
      border-top: 1px dashed #e5e7eb;
      padding-top: 4px;
    }

    .swal-stage-team-empty {
      font-size: 12px;
      color: #94a3b8;
    }



    /* ===== Compact Kanban Teams Button ===== */
    .kb-card-team-compact {
      margin-top: 7px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }

    .kb-team-pill {
      border: 1px solid #dbeafe;
      background: #ffffff;
      color: #334155;
      border-radius: 999px;
      min-height: 28px;
      padding: 3px 8px 3px 5px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 900;
      cursor: pointer;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
    }

    .kb-team-pill:hover {
      background: #f8fafc;
      border-color: #74b2d4;
      color: #0f172a;
    }

    .kb-team-mini-avatars {
      display: inline-flex;
      align-items: center;
      padding-left: 7px;
    }

    .kb-team-mini-avatars img {
      width: 22px;
      height: 22px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #fff;
      margin-left: -7px;
      background: #eef2f7;
    }

    .kb-team-pill-count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 20px;
      height: 20px;
      border-radius: 999px;
      background: #eef2f7;
      color: #334155;
      font-size: 10px;
      font-weight: 900;
    }

    .swal-team-current-box {
      border: 1px solid #dbeafe;
      background: #f8fafc;
      border-radius: 14px;
      padding: 10px;
      margin-bottom: 10px;
    }

    .swal-team-current-title {
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
      margin-bottom: 7px;
    }

    .swal-team-current-list {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .swal-team-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #e5e7eb;
      background: #fff;
      border-radius: 999px;
      padding: 3px 8px 3px 4px;
      font-size: 12px;
      font-weight: 800;
      color: #334155;
    }

    .swal-team-chip img {
      width: 22px;
      height: 22px;
      border-radius: 999px;
      object-fit: cover;
    }

    .swal-stage-team-row.is-current-stage {
      border-color: #93c21c;
      background: #f7fbef;
    }


    /* ===== Notes Drawer ===== */
    .notes-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .25);
      opacity: 0;
      pointer-events: none;
      transition: opacity .2s;
      z-index: 1075;
    }

    .notes-backdrop.show {
      opacity: 1;
      pointer-events: auto;
    }

    .notes-drawer {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      width: 1112px;
      max-width: 95vw;
      background: #fff;
      transform: translateX(100%);
      transition: transform .24s ease;
      z-index: 1080;
      display: flex;
      flex-direction: column;
      box-shadow: -12px 0 30px rgba(0, 0, 0, .12);
    }

    .notes-drawer.open {
      transform: translateX(0);
    }

    /* ===== Notes Tabs (inside drawer) ===== */
    .notes-tabs {
      display: flex;
      align-items: flex-end;
      gap: .25rem;
      padding: 0 .75rem;
      border-bottom: 1px solid #e5e7eb;
      background: #f9fafb;
    }

    .notes-tab {
      border: none;
      background: transparent;
      padding: .45rem .8rem;
      font-size: .9rem;
      cursor: pointer;
      border-bottom: 2px solid transparent;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      color: #6b7280;
      outline: 0;
    }

    .notes-tab--active {
      background: #ffffff;
      border-color: #93c21c;
      color: #111827;
      font-weight: 600;
    }

    /* Customer Report styles inside notes drawer */
    #customerReportList {
      padding: 6px 8px 10px;
      max-height: 100%;
      overflow-y: auto;
    }

    .cr-shell {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .cr-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 6px 4px 4px;
      border-bottom: 1px solid rgba(148, 163, 184, 0.25);
    }

    .cr-title-row {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .cr-new-wrapper {
      border-radius: 10px;
      border: 1px dashed rgba(148, 163, 184, 0.6);
      padding: 8px;
      background: rgba(15, 23, 42, 0.02);
    }

    /* Cards */
    .cr-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .cr-card {
      border-radius: 12px;
      border: 1px solid rgba(148, 163, 184, 0.35);
      background: #ffffff;
      padding: 8px 9px;
      box-shadow: 0 5px 18px rgba(15, 23, 42, 0.06);
      transition: box-shadow .15s ease, transform .15s ease;
    }

    .cr-card:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.10);
    }

    /* Card header */
    .cr-card-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 4px;
    }

    .cr-author {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .cr-avatar {
      width: 28px;
      height: 28px;
      border-radius: 999px;
      object-fit: cover;
      box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.06);
    }

    .cr-author-name {
      font-size: 13px;
      font-weight: 600;
      color: #0f172a;
    }

    .cr-author-meta {
      font-size: 11px;
      color: #6b7280;
    }

    /* Body */
    .cr-card-body {
      font-size: 12px;
      line-height: 1.4;
      color: #111827;
      padding: 4px 0 2px;
      border-top: 1px dashed rgba(148, 163, 184, 0.4);
      border-bottom: 1px dashed rgba(148, 163, 184, 0.3);
      margin-top: 4px;
      margin-bottom: 4px;
      max-height: 140px;
      overflow-y: auto;
    }

    /* Footer */
    .cr-card-foot {
      display: flex;
      justify-content: flex-end;
    }

    /* Comments */
    .cr-comments {
      margin-top: 6px;
      border-top: 1px solid rgba(148, 163, 184, 0.30);
      padding-top: 6px;
    }

    .cr-comments-list {
      max-height: 120px;
      overflow-y: auto;
      margin-bottom: 4px;
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    /* Single comment */
    .cr-comment-row {
      display: flex;
      align-items: flex-start;
      gap: 6px;
    }

    .cr-comment-row--reply {
      margin-top: 4px;
      margin-left: 22px;
    }

    .cr-comment-avatar {
      width: 22px;
      height: 22px;
      border-radius: 999px;
      object-fit: cover;
    }

    .cr-comment-bubble {
      background: #f9fafb;
      border-radius: 10px;
      padding: 4px 6px;
      border: 1px solid rgba(209, 213, 219, 0.8);
      width: 100%;
    }

    .cr-comment-meta {
      display: flex;
      justify-content: space-between;
      font-size: 10px;
      color: #6b7280;
      margin-bottom: 2px;
    }

    .cr-comment-author {
      font-weight: 600;
    }

    .cr-comment-text {
      font-size: 11px;
      color: #111827;
    }

    /* Comment form */
    .cr-comment-form textarea {
      font-size: 12px;
    }

    .cr-comment-form .btn {
      padding: 2px 8px;
    }


    .notes-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: .75rem .9rem;
      border-bottom: 1px solid #e5e7eb;
    }

    .notes-title {
      display: flex;
      align-items: center;
      gap: .5rem;
      font-weight: 700;
      font-size: 1rem;
    }

    .notes-body {
      flex: 1;
      overflow: auto;
      padding: 12px;
      background: #f8fafc;
    }

    .notes-foot {
      border-top: 1px solid #e5e7eb;
      padding: .6rem .75rem;
      background: #fff;
    }

    .note-row {
      display: flex;
      align-items: flex-end;
      gap: .5rem;
      margin: 8px 0;
    }

    .note-row.me {
      justify-content: flex-end;
    }

    .note-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      object-fit: cover;
      background: #eee;
      flex: 0 0 34px;
    }

    .note-bubble {
      max-width: 500px;
      width: 500px;
      padding: .5rem .6rem;
      border-radius: 14px;
      position: relative;
      word-break: break-word;
    }

    .note-meta {
      font-size: .75rem;
      opacity: .8;
      margin-top: 4px;
    }

    .note-bubble.other {
      background: #cfe09b;
      color: #000;
    }

    .note-bubble.me {
      background: #cfe09b6e;
      color: #10212b;
      font-weight: 600;
    }

    .note-actions {
      border: 0px;
      background: #e6efd3;
    }

    /* little tail */
    .note-bubble.other::after {
      content: '';
      position: absolute;
      left: -6px;
      bottom: 6px;
      border: 7px solid transparent;
      border-right-color: #cfe09b;
    }

    .note-bubble.me::after {
      content: '';
      position: absolute;
      right: -6px;
      bottom: 6px;
      border: 7px solid transparent;
      border-left-color: #74b2d4;
    }

    /* composer */
    .notes-composer {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: .5rem;
    }

    .notes-composer textarea {
      resize: vertical;
      min-height: 42px;
      max-height: 140px;
    }

    /* Notes icon on card */
    /* Generic Icon Badges on Kanban Cards */
    .btn-icon {
      position: relative;
    }

    .btn-icon .badge-notes {
      position: absolute;
      top: -6px;
      right: -6px;
      min-width: 18px;
      height: 18px;
      line-height: 18px;
      padding: 0 4px;
      border-radius: 10px;
      background: #93c21c;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      text-align: center;
      pointer-events: none;
      /* Ensures clicking the badge triggers the button */
      z-index: 10;
    }

    .kb-menu-dropdown {
      position: absolute;
      top: 36px;
      left: 8px;
      background: #fff;
      border: 1px solid rgba(0, 0, 0, .08);
      border-radius: 10px;
      box-shadow: 0 6px 24px rgba(0, 0, 0, .12);
      padding: 6px;
      z-index: 4;
      display: flex;
      flex-direction: column;
      min-width: 140px;
    }

    .kb-menu-item {
      display: block;
      text-align: left;
      width: 100%;
      background: transparent;
      border: 0;
      padding: 8px 10px;
      border-radius: 8px;
      font-size: .95rem;
    }

    .kb-menu-item:hover {
      background: #f3f4f6;
    }


    /* ===== Appointment Reports (inside Notes -> Report tab) ================== */

    .ap-report-wrapper {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    /* Header (optional global overview above list) */
    .ap-report-header {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      padding: 8px 10px;
      margin-bottom: 6px;
      border-radius: 12px;
      background: linear-gradient(90deg, #cfe09b, #74b2d4);
      color: #10212b;
    }

    .ap-report-header-title {
      font-weight: 700;
      font-size: 14px;
    }

    .ap-report-header-meta {
      font-size: 12px;
      opacity: 0.9;
    }

    /* Single report card */
    .ap-report-card {
      position: relative;
      border-radius: 14px;
      border: 1px solid #e5e7eb;
      background: #ffffff;
      box-shadow: 0 3px 10px rgba(15, 23, 42, 0.06);
      padding: 10px 12px;
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      grid-row-gap: 6px;
    }

    /* Top row: title, date, stage */
    .ap-report-top {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: baseline;
      gap: 4px;
    }

    .ap-report-title {
      font-weight: 600;
      font-size: 13px;
      color: #111827;
    }

    .ap-report-sub {
      font-size: 11px;
      color: #6b7280;
    }

    /* Stage badge */
    .ap-report-stage {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 7px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 600;
      background: #eef2ff;
      color: #3730a3;
    }

    /* Body text (the report content) */
    .ap-report-body {
      font-size: 13px;
      color: #111827;
      line-height: 1.4;
      padding: 6px 0 2px 0;
      border-top: 1px dashed #e5e7eb;
    }

    .ap-report-body p {
      margin-bottom: 4px;
    }

    /* Footer: employee & reactions */
    .ap-report-footer {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 6px;
      padding-top: 4px;
      border-top: 1px dashed #e5e7eb;
    }

    /* Author / employees */
    .ap-report-author {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      color: #4b5563;
    }

    .ap-report-avatar {
      width: 24px;
      height: 24px;
      border-radius: 999px;
      object-fit: cover;
      background: #e5e7eb;
    }

    /* Like / dislike buttons */
    .ap-report-actions {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 11px;
    }

    .ap-report-like,
    .ap-report-dislike {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      padding: 2px 7px;
      border-radius: 999px;
      border: 1px solid #e5e7eb;
      background: #f9fafb;
      font-size: 11px;
      cursor: pointer;
    }

    .ap-report-like .feather,
    .ap-report-dislike .feather {
      width: 13px;
      height: 13px;
    }

    .ap-report-like.is-active {
      border-color: #93c21c;
      background: #e7f5d0;
      color: #2f5c00;
    }

    .ap-report-dislike.is-active {
      border-color: #f97373;
      background: #ffe2e2;
      color: #991b1b;
    }

    /* Comments area */
    .ap-report-comments-toggle {
      font-size: 11px;
      color: #4b5563;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 3px;
    }

    .ap-report-comments {
      margin-top: 6px;
      padding-top: 6px;
      border-top: 1px dashed #e5e7eb;
      font-size: 12px;
    }

    .ap-report-comment-row {
      display: flex;
      align-items: flex-start;
      gap: 6px;
      margin-bottom: 6px;
    }

    .ap-report-comment-avatar {
      width: 24px;
      height: 24px;
      border-radius: 999px;
      object-fit: cover;
      background: #e5e7eb;
    }

    .ap-report-comment-bubble {
      padding: 5px 8px;
      border-radius: 10px;
      background: #f3f4f6;
    }

    .ap-report-comment-meta {
      font-size: 11px;
      color: #6b7280;
      margin-bottom: 2px;
    }

    /* Comment composer (per report) */
    .ap-report-comment-composer {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 4px;
      margin-top: 4px;
    }

    .ap-report-comment-text {
      resize: vertical;
      min-height: 40px;
      max-height: 90px;
    }

    .ap-report-comment-submit {
      white-space: nowrap;
    }

    /* Readonly state: not in employee list */
    .ap-report-card.ap-report--readonly {
      background: #f9fafb;
    }

    .ap-report-card.ap-report--readonly .ap-report-actions,
    .ap-report-card.ap-report--readonly .ap-report-comment-composer {
      opacity: 0.6;
      pointer-events: none;
    }

    .ap-report-lock {
      position: absolute;
      top: 6px;
      right: 8px;
      padding: 2px 7px;
      border-radius: 999px;
      background: #fef9c3;
      color: #92400e;
      font-size: 10px;
      font-weight: 600;
    }


    /* =========================================================
         Kanban Lead Task Management Modal (replaces Next Step)
         ========================================================= */
    .kb-task-count-badge {
      position: absolute;
      top: -6px;
      right: -6px;
      min-width: 18px;
      height: 18px;
      line-height: 18px;
      padding: 0 4px;
      border-radius: 999px;
      background: #c93a3a;
      color: #fff;
      font-size: 10px;
      font-weight: 900;
      text-align: center;
      pointer-events: none;
      z-index: 10;
    }

    .kb-task-management-btn {
      color: #475569;
    }

    .kb-task-management-btn:hover,
    .kb-task-management-btn.is-active {
      color: #2f5c00;
      background: #e7f3d2;
    }

    .kb-task-backdrop,
    .kb-task-form-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .38);
      opacity: 0;
      pointer-events: none;
      z-index: 1085;
      transition: opacity .18s ease;
    }

    .kb-task-backdrop.show,
    .kb-task-form-backdrop.show {
      opacity: 1;
      pointer-events: auto;
    }

    .kb-task-modal {
      position: fixed;
      top: 4vh;
      right: 3vw;
      bottom: 4vh;
      width: 1180px;
      max-width: 94vw;
      background: #fff;
      border-radius: 22px;
      box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
      z-index: 1090;
      display: flex;
      flex-direction: column;
      transform: translateX(110%);
      transition: transform .24s ease;
      overflow: hidden;
    }

    .kb-task-modal.open {
      transform: translateX(0);
    }

    .kb-task-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 15px 18px;
      border-bottom: 1px solid #e5e7eb;
      background: linear-gradient(135deg, #f8fafc 0%, #eef7fb 100%);
    }

    .kb-task-modal-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 18px;
      font-weight: 900;
      color: #0f172a;
    }

    .kb-task-modal-sub {
      margin-top: 4px;
      color: #64748b;
      font-size: 12px;
      font-weight: 700;
    }

    .kb-task-close {
      width: 36px;
      height: 36px;
      border: 0;
      border-radius: 999px;
      background: #fff;
      color: #0f172a;
      font-size: 24px;
      line-height: 1;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .kb-task-toolbar {
      display: flex;
      gap: 8px;
      padding: 12px;
      border-bottom: 1px solid #e5e7eb;
      background: #fff;
      flex-wrap: wrap;
    }

    .kb-task-search,
    .kb-task-filter,
    .kb-task-field input,
    .kb-task-field textarea,
    .kb-task-field select {
      border: 1px solid #dbe3ee;
      border-radius: 12px;
      padding: 9px 11px;
      min-height: 38px;
      outline: none;
      width: 100%;
      background: #fff;
    }

    .kb-task-search {
      flex: 1 1 280px;
    }

    .kb-task-filter {
      flex: 0 0 180px;
    }

    .kb-task-primary,
    .kb-task-secondary {
      border: 0;
      border-radius: 12px;
      padding: 9px 13px;
      font-weight: 900;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      text-decoration: none;
      white-space: nowrap;
    }

    .kb-task-primary {
      background: #93c21c;
      color: #fff;
    }

    .kb-task-secondary {
      background: #eef2f7;
      color: #334155;
    }

    .kb-task-body {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
      gap: 0;
      min-height: 0;
      flex: 1;
    }

    .kb-task-column {
      min-width: 0;
      overflow: auto;
      padding: 14px;
    }

    .kb-task-column+.kb-task-column {
      border-left: 1px solid #e5e7eb;
    }

    .kb-task-section-title {
      font-size: 13px;
      font-weight: 900;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 7px;
    }

    .kb-task-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .kb-task-card {
      border: 1px solid #e5e7eb;
      background: #fff;
      border-radius: 16px;
      padding: 12px;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
      position: relative;
    }

    .kb-task-card.is-overdue {
      border-color: #c93a3a;
      animation: kbTaskOverduePulse 1.15s infinite;
    }

    @keyframes kbTaskOverduePulse {
      0% {
        box-shadow: 0 0 0 0 rgba(201, 58, 58, .30);
      }

      70% {
        box-shadow: 0 0 0 8px rgba(201, 58, 58, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(201, 58, 58, 0);
      }
    }

    .kb-task-card-title {
      font-weight: 900;
      color: #0f172a;
      padding-right: 72px;
      line-height: 1.25;
    }

    .kb-task-card-desc {
      color: #64748b;
      font-size: 12px;
      margin-top: 5px;
      line-height: 1.45;
      word-break: break-word;
    }

    .kb-task-card-meta {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      margin-top: 8px;
    }

    .kb-task-pill {
      border-radius: 999px;
      background: #f1f5f9;
      color: #334155;
      padding: 4px 8px;
      font-size: 11px;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .kb-task-pill.red {
      background: #ffe2e2;
      color: #8a1f1f;
    }

    .kb-task-pill.green {
      background: #e7f3d2;
      color: #2f5c00;
    }

    .kb-task-pill.blue {
      background: #eef7fb;
      color: #075985;
    }

    .kb-task-card-actions {
      display: flex;
      gap: 6px;
      margin-top: 10px;
      flex-wrap: wrap;
    }

    .kb-task-mini-btn {
      border: 1px solid #dbe3ee;
      background: #fff;
      color: #334155;
      border-radius: 10px;
      padding: 6px 9px;
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .kb-task-mini-btn:hover {
      background: #f8fafc;
      border-color: #74b2d4;
    }

    .kb-task-empty {
      border: 1px dashed #cbd5e1;
      border-radius: 16px;
      padding: 18px;
      color: #64748b;
      background: #f8fafc;
      font-size: 13px;
      font-weight: 700;
      text-align: center;
    }

    .kb-task-form-modal {
      position: fixed;
      top: 7vh;
      left: 50%;
      transform: translate(-50%, -18px);
      width: 650px;
      max-width: 94vw;
      max-height: 86vh;
      overflow: auto;
      background: #fff;
      border-radius: 20px;
      z-index: 1100;
      box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
      opacity: 0;
      pointer-events: none;
      transition: .18s ease;
    }

    .kb-task-form-modal.open {
      opacity: 1;
      pointer-events: auto;
      transform: translate(-50%, 0);
    }

    .kb-task-form-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 17px;
      border-bottom: 1px solid #e5e7eb;
    }

    #kbTaskForm {
      padding: 16px;
    }

    .kb-task-field {
      margin-bottom: 12px;
    }

    .kb-task-field label {
      display: block;
      font-size: 12px;
      font-weight: 900;
      color: #334155;
      margin-bottom: 5px;
    }

    .kb-task-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .kb-task-form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 16px;
    }

    .kb-task-card-next {
      margin-top: 8px;
      border-top: 1px dashed #e5e7eb;
      padding-top: 8px;
      color: #334155;
      font-size: 12px;
    }

    .kb-next-step-preview {
      margin-top: 8px;
      border: 1px solid #dbeafe;
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border-radius: 13px;
      padding: 8px;
      font-size: 11px;
      color: #334155;
      position: relative;
      z-index: 2;
    }

    .kb-next-step-preview-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 6px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: 6px;
    }

    .kb-next-step-preview-line {
      display: flex;
      align-items: center;
      gap: 5px;
      line-height: 1.3;
      margin-top: 3px;
    }

    .kb-next-step-preview-line .feather {
      width: 13px;
      height: 13px;
      color: #74b2d4;
    }

    .kb-next-step-preview-btn {
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 999px;
      padding: 4px 8px;
      font-size: 10px;
      font-weight: 900;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      white-space: nowrap;
    }

    .kb-next-step-preview-btn:hover {
      background: #eef7fb;
      border-color: #74b2d4;
      color: #0f172a;
    }

    .kb-task-sequence-summary {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 8px;
      padding: 12px;
      border-bottom: 1px solid #e5e7eb;
      background: #f8fafc;
    }

    .kb-task-seq-card {
      border: 1px solid #e5e7eb;
      background: #fff;
      border-radius: 14px;
      padding: 10px;
      min-width: 0;
    }

    .kb-task-seq-label {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 10px;
      font-weight: 900;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-bottom: 5px;
    }

    .kb-task-seq-value {
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
      line-height: 1.3;
      word-break: break-word;
    }

    .kb-task-seq-muted {
      font-size: 11px;
      color: #64748b;
      margin-top: 3px;
      line-height: 1.35;
    }

    @media (max-width: 900px) {
      .kb-task-sequence-summary {
        grid-template-columns: 1fr;
      }
    }

    .select2-container--open {
      z-index: 200001 !important;
    }

    @media (max-width: 800px) {
      .kb-task-body {
        grid-template-columns: 1fr;
      }

      .kb-task-column+.kb-task-column {
        border-left: 0;
        border-top: 1px solid #e5e7eb;
      }

      .kb-task-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
  <style>
    /* ===== Lead History (lh-) — no conflicts ===== */
    :root {
      --lh-bg: #fff;
      --lh-backdrop: rgba(15, 23, 42, .45);
      --lh-border: #e5e7eb;
      --lh-muted: #6b7280;
      --lh-shadow: 0 10px 30px rgba(0, 0, 0, .18), 0 2px 8px rgba(0, 0, 0, .08);
    }

    .lh-root {
      position: fixed;
      inset: 0;
      z-index: 1060;
      pointer-events: none;
      opacity: 0;
      transition: opacity .18s ease;
    }

    .lh-root[aria-hidden="false"] {
      pointer-events: auto;
      opacity: 1;
    }

    .lh-backdrop {
      position: absolute;
      inset: 0;
      background: var(--lh-backdrop);
    }

    /* Panel is FIXED to the right; slides from right -> to position 0 */
    .lh-panel {
      position: fixed;
      top: 0;
      right: 0;
      height: 100%;
      width: min(980px, 92vw);
      background: var(--lh-bg);
      color: #111827;
      border-left: 1px solid var(--lh-border);
      border-top-left-radius: 16px;
      border-bottom-left-radius: 16px;
      box-shadow: var(--lh-shadow);
      transform: translateX(100%);
      /* start off-screen to the RIGHT */
      transition: transform .26s cubic-bezier(.22, .9, .22, 1);
      display: flex;
      flex-direction: column;
      outline: 0;
    }

    .lh-root[aria-hidden="false"] .lh-panel {
      transform: translateX(0);
    }

    @media (prefers-reduced-motion:reduce) {

      .lh-root,
      .lh-panel {
        transition: none !important;
      }
    }

    /* Header */
    .lh-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 16px;
      border-bottom: 1px solid var(--lh-border);
      background: #fff;
      border-top-left-radius: 16px;
    }

    #lh-title {
      font-weight: 700;
      letter-spacing: .2px;
    }

    #lh-title-text {
      font-weight: 600;
    }

    /* Body */
    .lh-body {
      overflow: auto;
      height: 100%;
      background: #fff;
      border-bottom-left-radius: 16px;
    }

    .lh-muted {
      color: var(--lh-muted);
    }

    /* Timeline spine */
    .lh-timeline {
      position: relative;
      padding-left: 0;
      list-style: none;
      margin: 0;
    }

    .lh-timeline::before {
      content: "";
      position: absolute;
      left: 28px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: #eef2f7;
    }

    .lh-item {
      display: flex;
    }

    .lh-icowrap {
      width: 56px;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding-top: 6px;
    }

    .lh-ico {
      width: 26px;
      height: 26px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f3f6ff;
      border: 1px solid #e6ecff;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8);
    }

    .lh-content {
      flex: 1;
      padding: 8px 0 14px;
    }

    /* Badges (local variants; won’t clash with Bootstrap) */
    .lh-badge {
      display: inline-block;
      padding: .25rem .55rem;
      border-radius: 10px;
      border: 1px solid var(--lh-border);
      font-weight: 600;
      background: #f9fafb;
    }

    .lh-badge--success {
      background: #e8f7ed;
      border-color: #d0f0db;
    }

    .lh-badge--danger {
      background: #fde8e8;
      border-color: #f9cfcf;
    }

    .lh-badge--warning {
      background: #fff6e5;
      border-color: #ffe6b0;
    }

    .lh-badge--info {
      background: #e7f5ff;
      border-color: #cfe8ff;
    }

    .lh-badge--secondary {
      background: #f1f5f9;
      border-color: #e2e8f0;
    }

    .lh-badge--primary {
      background: #eef2ff;
      border-color: #dbe3ff;
    }

    /* Cards (right column) */
    .lh-list>.lh-card,
    .lh-list .list-group-item {
      border: 1px solid #eef2f7;
      background: #fff;
      border-radius: 12px;
      padding: 10px 12px;
      margin-bottom: 10px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
    }

    /* Skeletons */
    .lh-skel {
      background: linear-gradient(90deg, #f1f5f9 25%, #eef2f7 37%, #f1f5f9 63%);
      background-size: 400% 100%;
      animation: lh-sk 1.1s ease-in-out infinite;
      border-radius: 10px;
      height: 12px;
      margin: 8px 0;
    }

    @keyframes lh-sk {
      0% {
        background-position: 100% 0
      }

      100% {
        background-position: 0 0
      }
    }

    .kb-menu-dropdown {
      z-index: 2000;
    }


    /* ===== Task Drawer: wider + pro layout (ONLY affects #pt-drawer) ===== */

    /* Make the task drawer much wider on desktop, responsive on smaller screens */
    /* ===== Personal Task Kanban (unique classes: ptk-*) ===== */

    /* Board */
    #pt-drawer .ptk-board {
      padding: 8px;
    }

    #pt-drawer .ptk-board-row {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
    }

    @media (min-width: 720px) {
      #pt-drawer .ptk-board-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (min-width: 1200px) {
      #pt-drawer .ptk-board-row {
        grid-template-columns: repeat(5, minmax(0, 1fr));
      }
    }

    /* Columns */
    #pt-drawer .ptk-col {
      background: #f3f4f6;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      overflow: hidden;
    }

    #pt-drawer .ptk-col-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 10px;
      background: #eef2f7;
      border-bottom: 1px solid #e5e7eb;
    }

    #pt-drawer .ptk-col-title {
      font-weight: 700;
      font-size: 13px;
      color: #0f172a;
    }

    #pt-drawer .ptk-col-count {
      font-size: 12px;
      font-weight: 700;
      background: #fff;
      border: 1px solid #e5e7eb;
      padding: 2px 8px;
      border-radius: 999px;
      color: #374151;
    }

    #pt-drawer .ptk-col-body {
      padding: 8px;
      min-height: 120px;
    }

    #pt-drawer .ptk-col-body.ptk-over {
      outline: 2px dashed #9ca3af;
      outline-offset: -4px;
    }

    /* Cards */
    #pt-drawer .ptk-card {
      position: relative;
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 10px 12px 10px 14px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
      cursor: grab;
      margin-bottom: 8px;
    }

    #pt-drawer .ptk-card:active {
      cursor: grabbing;
    }

    #pt-drawer .ptk-card .ptk-card-color {
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      border-top-left-radius: 12px;
      border-bottom-left-radius: 12px;
    }

    #pt-drawer .ptk-card-title {
      font-weight: 700;
      color: #0f172a;
      font-size: 14px;
      line-height: 1.25;
    }

    #pt-drawer .ptk-card-desc {
      color: #374151;
      font-size: 13px;
      margin-top: 4px;
    }

    #pt-drawer .ptk-card-emps {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 6px;
    }

    #pt-drawer .ptk-emp {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 999px;
      padding: 2px 8px;
      font-size: 12px;
      color: #374151;
    }

    #pt-drawer .ptk-emp--xs {
      padding: 1px 6px;
      font-size: 11px;
    }

    #pt-drawer .ptk-ava {
      border-radius: 999px;
      display: block;
    }

    #pt-drawer .ptk-steps {
      margin-top: 8px;
      border-top: 1px dashed #e5e7eb;
      padding-top: 6px;
    }

    #pt-drawer .ptk-step {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 4px;
    }

    #pt-drawer .ptk-step-title {
      font-size: 12px;
      font-weight: 600;
      color: #111827;
    }

    #pt-drawer .ptk-step-emps {
      display: flex;
      gap: 4px;
      flex-wrap: wrap;
    }

    #pt-drawer .ptk-empty {
      font-size: 12px;
      color: #6b7280;
      border: 1px dashed #e5e7eb;
      background: #fff;
      border-radius: 10px;
      padding: 8px;
      text-align: center;
    }

    #pt-title.ptk-loading::after {
      content: '…';
      animation: ptk-ell 1s infinite steps(3);
      margin-left: 4px;
    }

    @keyframes ptk-ell {
      0% {
        content: '·';
      }

      33% {
        content: '··';
      }

      66% {
        content: '···';
      }
    }

    #pt-drawer .ptk-card .btn-icon {
      padding: .2rem .35rem;
      border-radius: 8px;
      font-size: 16px;
    }

    .swal2-container {
      z-index: 200000 !important;
    }

    .ptk-col-body {
      height: 100vh;
    }

    /* Task Drawer search + highlight */
    #ptk-search-wrap {
      flex: 1 1 auto;
    }

    #ptk-search::placeholder {
      color: #9aa3af;
    }

    .ptk-hl {
      background: #ffec99;
      padding: 0 .1em;
      border-radius: 3px;
    }

    /* ===== FOOTER COMPOSER: scrollable, with sticky Save button ===== */

    /* Footer shell stays pinned; no scrolling on the shell itself */
    #pt-drawer .notes-foot {
      background: #fff;
      border-top: 1px solid #e5e7eb;
      z-index: 2;

      /* two columns: form (scrolls) + save button */
      display: grid;
      grid-template-columns: 1fr auto;
      gap: .75rem;
      align-items: start;
      padding: .75rem;
    }

    /* The form column: it gets its own scrollbar when tall */
    #pt-drawer .notes-foot .notes-composer {
      min-height: 0;
      /* allow shrinking in grid */
      max-height: 44vh;
      /* cap height of the form column */
      overflow: auto;
      /* scroll inside the form */
      padding-right: .25rem;
      /* keep content clear of scrollbar */
    }

    /* Ensure the inner wrapper can shrink and not force overflow */
    #pt-drawer .notes-foot .notes-composer>.w-100 {
      min-width: 0;
    }

    /* Keep the Save button always reachable */
    #pt-drawer .notes-foot .btn.btn-primary {
      position: sticky;
      top: .25rem;
      /* sticks within the footer area */
      align-self: start;
      white-space: nowrap;
    }

    /* Inputs row: tidy gaps on wrap */
    #pt-drawer .notes-foot .d-flex.flex-wrap.gap-2 {
      gap: .5rem !important;
    }

    /* Match your inline widths via CSS (you can remove the inline styles if you like) */
    #pt-drawer #pt-start_date,
    #pt-drawer #pt-due_date {
      max-width: 180px;
    }

    #pt-drawer #pt-due_time {
      max-width: 140px;
    }

    #pt-drawer #pt-priority {
      max-width: 150px;
    }

    #pt-drawer #pt-color {
      max-width: 70px;
      padding: 0 2px;
    }

    /* Steps block inside the composer: cap height so Save stays visible */
    #pt-drawer #pt-steps {
      max-height: 24vh;
      /* slightly smaller than the form cap */
      overflow: auto;
      padding-right: 2px;
      scroll-behavior: smooth;
    }

    /* Select2 & Swal should float above the drawer */
    .select2-container--open {
      z-index: 2000 !important;
    }

    .swal2-container {
      z-index: 200000 !important;
    }

    /* ===== Appointment Drawer ===== */
    #ap-drawer {
      width: 520px;
      max-width: 95vw;
    }

    #ap-drawer .notes-body {
      background: #f8fafc;
    }

    #ap-drawer .ap-card {
      position: relative;
      display: flex;
      padding: 10px 10px 10px 12px;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 1px 3px rgba(15, 23, 42, .08);
      margin-bottom: 8px;
      border: 1px solid #e5e7eb;
    }

    #ap-drawer .ap-color {
      width: 4px;
      border-radius: 999px;
      background: #74b2d4;
      margin-right: 8px;
    }

    #ap-drawer .ap-main {
      flex: 1;
      min-width: 0;
    }

    #ap-drawer .ap-title {
      font-weight: 600;
      font-size: 14px;
    }

    #ap-drawer .ap-note {
      color: #4b5563;
    }

    #ap-drawer .ap-meta {
      font-size: 12px;
      color: #6b7280;
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      align-items: center;
    }

    #ap-drawer .ap-emps {
      margin-top: 4px;
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
    }

    #ap-drawer .ap-emp {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 6px;
      border-radius: 999px;
      border: 1px solid #e5e7eb;
      background: #f9fafb;
      font-size: 11px;
    }

    #ap-drawer .ap-emp-img {
      width: 18px;
      height: 18px;
      border-radius: 999px;
      object-fit: cover;
    }

    #ap-drawer .ap-actions .btn-icon {
      padding: .15rem .25rem;
      border-radius: 6px;
      font-size: 15px;
    }

    /* Footer layout for ap drawer */
    #ap-drawer .notes-foot {
      display: block;
      padding: .7rem .8rem;
    }

    #ap-drawer #ap-form .form-group {
      margin-bottom: .4rem;
    }

    #ap-drawer #ap-form small.text-muted {
      font-size: 11px;
    }

    #ap-customer_search_group {
      display: none !important;
    }

    /* Make the appointments drawer wider */
    #ap-drawer.notes-drawer {
      width: 960px;
      /* nice and wide */
      max-width: 90vw;
      /* don't explode on small screens */
    }

    /* Optional: on very big screens, give it even more room */
    @media (min-width: 1600px) {
      #ap-drawer.notes-drawer {
        width: 1100px;
      }
    }

    /* Flex layout: list + form side by side */
    #ap-drawer .ap-layout {
      display: flex;
      flex-direction: row;
      height: calc(100vh - 60px);
      /* 100vh minus header height */
      padding: 0.75rem 0.75rem 0.75rem 0.75rem;
      box-sizing: border-box;
    }

    /* Left side: appointments list */
    #ap-drawer .ap-list-wrapper {
      flex: 1.2;
      /* a bit wider than form */
      padding-right: 0.75rem;
      border-right: 1px solid #e5e5e5;
      overflow-y: auto;
    }

    /* Right side: form */
    #ap-drawer .ap-form-wrapper {
      flex: 1;
      padding-left: 0.75rem;
      overflow-y: auto;
    }

    /* Grid: 4 appointments per row */
    #ap-list {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      grid-gap: 0.5rem;
      /* space between cards */
    }

    /* Appointment card styling */
    #ap-list .ap-item {
      background: #fff;
      border: 1px solid #e2e6ea;
      border-radius: 4px;
      padding: 0.5rem 0.6rem;
      font-size: 12px;
      cursor: pointer;
      transition: box-shadow 0.15s ease, transform 0.15s ease;
    }

    #ap-list .ap-item:hover {
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      transform: translateY(-1px);
    }

    /* Title + meta inside card (optional) */
    #ap-list .ap-item-title {
      font-weight: 600;
      margin-bottom: 0.25rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    #ap-list .ap-item-meta {
      color: #6c757d;
      font-size: 11px;
    }

    @media (max-width: 1200px) {
      #ap-list {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 992px) {
      #ap-list {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 768px) {
      #ap-list {
        grid-template-columns: repeat(1, minmax(0, 1fr));
      }

      /* On small screens stack list and form vertically again */
      #ap-drawer .ap-layout {
        flex-direction: column;
        height: auto;
      }

      #ap-drawer .ap-list-wrapper,
      #ap-drawer .ap-form-wrapper {
        border: none;
        padding: 0;
        max-height: 50vh;
      }
    }

    .kb-menu-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .35rem;
      width: 100%;
    }

    .kb-menu-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 1.4rem;
      padding: 0 .35rem;
      border-radius: 999px;
      font-size: 11px;
      line-height: 1.3;
      background: #f1f3f5;
      color: #495057;
    }

    .kb-menu-pill--ap {
      background: #e7f5ff;
    }

    /* Termine */
    .kb-menu-pill--pt {
      background: #fff3bf;
    }

    /* Aufgaben */
    .ap-appointment-group {
      border-radius: 14px;
      border: 1px solid #e5e7eb;
      background: #ffffff;
      padding: 10px 12px;
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .ap-appointment-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 8px;
      padding-bottom: 6px;
      border-bottom: 1px dashed #e5e7eb;
    }

    .ap-appointment-title {
      font-size: 14px;
      font-weight: 600;
      color: #111827;
    }

    .ap-appointment-sub {
      font-size: 12px;
      color: #6b7280;
    }

    .ap-appointment-type {
      display: inline-block;
      padding: 1px 6px;
      border-radius: 999px;
      background: #eff6ff;
      color: #1d4ed8;
      font-size: 11px;
    }

    .ap-appointment-employees {
      margin-top: 4px;
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
    }

    .ap-appointment-employee {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 6px;
      border-radius: 999px;
      background: #f9fafb;
      font-size: 11px;
      color: #4b5563;
    }

    .ap-appointment-employee-avatar {
      width: 18px;
      height: 18px;
      border-radius: 999px;
      object-fit: cover;
    }

    .ap-appointment-actions {
      flex-shrink: 0;
    }
  </style>
  <style>
    /* ---------- PER-CARD LIVE FEED (COMPACT) ---------- */
    .live-feed-bar {
      display: flex;
      align-items: center;
      margin-top: 6px;
      border-radius: 999px;
      overflow: hidden;
      background: #ffffff;
      color: #e5e7eb;
      font-size: 11px;
      padding: 4px 8px;
    }

    .live-feed-left {
      flex: 0 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 26px;
      height: 26px;
      border-radius: 999px;
      background: linear-gradient(135deg, #95c11f, #95c11feb);
      margin-right: 8px;
    }

    .live-feed-icon {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .live-feed-icon i {
      font-size: 13px;
    }

    .live-feed-body {
      flex: 1 1 auto;
      min-width: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 2px;
    }

    .live-feed-line {
      display: flex;
      align-items: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      gap: .25rem;
    }

    .live-feed-title {
      font-weight: 600;
      color: #5b6470;
    }

    .live-feed-dot {
      color: rgba(248, 250, 252, .6);
    }

    .live-feed-text {
      color: #cbd5f5;
      opacity: .9;
      min-width: 0;
    }

    .live-feed-meta {
      display: flex;
      align-items: center;
      gap: .4rem;
      font-size: 10px;
      color: #9ca3af;
    }

    .live-feed-pill {
      display: inline-flex;
      align-items: center;
      padding: 1px .55rem;
      border-radius: 999px;
      border: 1px solid rgba(148, 163, 184, .6);
      background: rgba(15, 23, 42, .85);
      color: #e5e7eb;
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .08em;
      white-space: nowrap;
    }

    .live-feed-time {
      display: inline-flex;
      align-items: center;
      gap: .18rem;
    }

    .live-feed-counter {
      margin-left: auto;
      opacity: .7;
    }

    .live-feed-controls {
      flex: 0 0 auto;
      display: flex;
      align-items: center;
      gap: .15rem;
      margin-left: 6px;
    }

    .live-feed-btn {
      width: 24px;
      height: 24px;
      border-radius: 999px;
      border: 1px solid rgba(148, 163, 184, .35);
      background: rgba(15, 23, 42, .95);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
      color: white;
      transition: background .15s ease, transform .15s ease, border-color .15s ease;
    }

    .live-feed-btn:hover {
      background: #77a763;
      border-color: #77a763;
      transform: translateY(-1px);
    }

    .live-feed-btn i {
      font-size: 12px;
    }

    .live-feed-bar.live-feed--empty .live-feed-pill,
    .live-feed-bar.live-feed--empty .live-feed-time,
    .live-feed-bar.live-feed--empty .live-feed-counter {
      opacity: .55;
    }

    .live-feed-bar.live-feed--paused {
      opacity: .88;
    }

    @media (max-width:768px) {
      .live-feed-bar {
        border-radius: 16px;
        padding: 4px 8px;
      }
    }

    /* Slight extra slimming for cards */
    .card-live-feed {
      margin-top: 6px;
      border-radius: 999px;
      font-size: 11px;
    }

    .card-live-feed .live-feed-body {
      overflow: hidden;
      white-space: nowrap;
    }

    /* Ticker for long text */
    .live-feed-text.live-feed-animate {
      display: inline-block;
      animation: liveFeedTicker 10s linear infinite;
    }

    @keyframes liveFeedTicker {
      0% {
        transform: translateX(0);
      }

      100% {
        transform: translateX(-100%);
      }
    }
  </style>

  <style>
    /* ---- Live Feed Modal ---- */
    .lfm-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.55);
      z-index: 10000;
      /* Increased z-index to be above drawers */
      display: none;
      /* Default state */
    }

    .lfm-shell {
      position: fixed;
      inset: 5% 8%;
      max-width: 1200px;
      margin: 0 auto;
      background: #ffffff;
      color: #333;
      border-radius: 18px;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
      display: none;
      /* Default state */
      flex-direction: column;
      z-index: 10001;
      /* Increased z-index */
      overflow: hidden;
    }

    @media (max-width: 768px) {
      .lfm-shell {
        inset: 4% 2%;
      }
    }

    .lfm-header {
      padding: 16px 20px;
      border-bottom: 1px solid rgba(148, 163, 184, 0.4);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .lfm-title {
      margin: 0;
      font-size: 18px;
      font-weight: 600;
    }

    .lfm-subtitle {
      font-size: 13px;
      color: #9ca3af;
      margin-top: 2px;
    }

    /* Ensure flex display when active */
    .lfm-shell[style*="display: flex"],
    .lfm-shell[style*="display:flex"] {
      display: flex !important;
    }

    @media (max-width: 768px) {
      .lfm-shell {
        inset: 2% 2%;
      }
    }

    .lfm-header-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .lfm-body {
      padding: 12px 20px 18px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      height: 100%;
    }

    .lfm-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      font-size: 12px;
    }

    .lfm-pill {
      padding: 3px 10px;
      border-radius: 999px;
      border: 1px solid rgba(148, 163, 184, 0.45);
      background: rgba(15, 23, 42, 0.9);
      color: #e5e7eb;
    }

    .lfm-pill.muted {
      color: #9ca3af;
      border-style: dashed;
    }

    .lfm-filters {
      display: inline-flex;
      gap: 4px;
      padding: 2px;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(148, 163, 184, 0.6);
    }

    .lfm-filter-btn {
      border: none;
      background: transparent;
      color: #9ca3af;
      font-size: 12px;
      padding: 4px 10px;
      border-radius: 999px;
      cursor: pointer;
      white-space: nowrap;
    }

    .lfm-filter-btn.is-active {
      background: #85b22f;
      color: #022c22;
    }

    .lfm-icon-btn {
      border: none;
      background: rgba(15, 23, 42, 0.8);
      border-radius: 999px;
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #9ca3af;
    }

    .lfm-icon-btn:hover {
      background: rgba(30, 64, 175, .75);
      color: #e5e7eb;
    }

    .lfm-list {
      margin-top: 6px;
      padding: 6px 0 4px;
      border-radius: 12px;
      background: #ffffffff;
      overflow-y: auto;
      flex: 1;
    }

    /* Each item row */
    .lfm-item {
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: 10px;
      padding: 9px 12px;
      border-bottom: 1px solid rgba(30, 64, 175, 0.4);
      font-size: 13px;
    }

    .lfm-item:last-child {
      border-bottom: none;
    }

    .lfm-item-type {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 70px;
      padding: 4px 8px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 500;
    }

    .lfm-item-type.task {
      background: rgba(34, 197, 94, 0.1);
      color: #4ade80;
      border: 1px solid rgba(34, 197, 94, 0.6);
    }

    .lfm-item-type.appointment {
      background: rgba(59, 130, 246, 0.1);
      color: #60a5fa;
      border: 1px solid rgba(59, 130, 246, 0.6);
    }

    .lfm-item-type.ticket {
      background: rgba(248, 113, 113, 0.08);
      color: #fca5a5;
      border: 1px solid rgba(248, 113, 113, 0.6);
    }

    .lfm-item-main {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .lfm-item-title {
      font-size: 13px;
      font-weight: 600;
      color: #515152;
    }

    .lfm-item-sub {
      font-size: 12px;
      color: #aaaaaaff;
    }

    .lfm-item-meta {
      font-size: 11px;
      color: #515152;
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 2px;
    }

    .lfm-item-badge {
      padding: 2px 7px;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.95);
      border: 1px solid rgba(148, 163, 184, 0.7);
    }

    .lfm-item-time {
      font-size: 12px;
      color: #515152;
      text-align: right;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .lfm-item-time span:first-child {
      font-weight: 500;
    }

    .lfm-item-time span:last-child {
      font-size: 11px;
      color: #9ca3af;
    }

    /* Date Age Indicators */
    .age-dot {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      margin-right: 5px;
      vertical-align: middle;
    }

    .age-green {
      background-color: #93c21c;
      box-shadow: 0 0 4px rgba(40, 167, 69, 0.5);
    }

    .age-orange {
      background-color: #fd7e14;
      box-shadow: 0 0 4px rgba(253, 126, 20, 0.5);
    }

    .age-red {
      background-color: #dc3545;
      box-shadow: 0 0 4px rgba(220, 53, 69, 0.5);
    }

    /* Ensure the calendar icon and date stay aligned */
    .kb-meta-item.date-item,
    .list-date-cell {
      display: inline-flex;
      align-items: center;
      white-space: nowrap;
    }

    .lfm-item-link {
      font-size: 11px;
      color: #ffffffff;
      text-decoration: none;
    }

    .lfm-item-link:hover {
      text-decoration: underline;
    }

    /* empty state */
    .lfm-empty {
      padding: 16px;
      text-align: center;
      font-size: 13px;
      color: #9ca3af;
    }

    .notes-composer {
      display: flex;
      align-items: flex-start;
      gap: .5rem;
    }

    .notes-quill {
      width: 100%;
    }

    .notes-quill .ql-container {
      border-radius: .25rem;
    }

    .notes-quill .ql-editor {
      min-height: 80px;
      max-height: 220px;
      overflow-y: auto;
    }

    /* Quill inside notes drawer */
    #notesDrawer .ql-toolbar {
      border-radius: 8px 8px 0 0;
      border-color: #e5e7eb;
      background: #f9fafb;
      padding: 4px 8px;
    }

    #notesDrawer .ql-container {
      border-radius: 0 0 8px 8px;
      border-color: #e5e7eb;
      min-height: 80px;
      max-height: 150px;
      font-size: 0.875rem;
    }

    /* slightly smaller buttons */
    #notesDrawer .ql-toolbar .ql-formats button,
    #notesDrawer .ql-toolbar .ql-formats .ql-picker {
      height: 22px;
    }

    /* remove big margin between toolbar and content */
    #notesDrawer .ql-editor {
      padding: 6px 8px;
    }

    /* FORCE HIDE ARCHIVE COLUMN BY DEFAULT */
    .kanban-container #archive {
      display: none;
    }

    /* --- Logic for Icons --- */

    /* Default (Unchecked): Hide ON icon, Show OFF icon */
    .col-toggle-checkbox~.custom-control-label .toggle-icon-on {
      display: none;
    }

    .col-toggle-checkbox~.custom-control-label .toggle-icon-off {
      display: inline-block;
    }

    /* Checked: Show ON icon, Hide OFF icon */
    .col-toggle-checkbox:checked~.custom-control-label .toggle-icon-on {
      display: inline-block;
    }

    .col-toggle-checkbox:checked~.custom-control-label .toggle-icon-off {
      display: none;
    }

    /* --- Logic for Text Readability --- */

    /* Default (Unchecked): Grey text */
    .col-toggle-checkbox~.custom-control-label .toggle-label-text {
      color: #999;
      font-weight: 400;
    }

    /* Checked: Dark, bold text for better readability */
    .col-toggle-checkbox:checked~.custom-control-label .toggle-label-text {
      color: #333;
      font-weight: 600;
    }

    .kb-card-meta .kb-meta-row {
      display: flex;
      align-items: center;
      gap: .5rem;
      flex-wrap: wrap;
    }

    .kb-card-meta .kb-meta-item {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      font-size: 12px;
      opacity: .95;
    }

    .kb-card-meta .kb-meta-sep {
      opacity: .6;
      font-size: 12px;
    }

    .kb-card-meta .kb-meta-address {
      display: block;
      margin-top: 2px;
      opacity: .85;
    }

    .kb-branch-name {
      max-width: 160px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: inline-block;
    }
  </style>

  <style>
    .kb-branch {
      --branch-color: #93c21c;
    }

    /* SVG + name follow branch color */
    .kb-meta-item.kb-branch {
      color: var(--branch-color);
    }

    /* Product rectangle badge must win */
    .circle.product_circle {
      min-width: 42px !important;
      width: auto !important;
      height: 24px !important;
      padding: 0 9px !important;
      border-radius: 7px !important;
      background-color: var(--branch-color, #93c21c) !important;
      color: #fff !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-weight: 900 !important;
      letter-spacing: .03em !important;
      white-space: nowrap !important;
    }

    .list-action-bar {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 6px;
      opacity: 0.8;
    }

    /* ADD THESE TWO LINES */
    .list-live-feed {
      display: none !important;
    }

    .list-live-feed.force-show-feed {
      display: flex !important;
    }


    .list-action-bar:hover {
      opacity: 1;
    }

    .btn-list-icon {
      background: transparent;
      border: none;
      padding: 0;
      cursor: pointer;
      color: #6c757d;
      transition: transform 0.1s;
      font-size: 17px;
    }

    .btn-list-icon:hover {
      transform: scale(1.1);
      color: #333;
    }

    .btn-list-icon.note:hover {
      color: #74b2d4;
    }

    .customer-link {
      color: #333;
      font-weight: 600;
      text-decoration: none;
    }

    .customer-link:hover {
      color: #93c21c;
      text-decoration: underline;
    }

    /* Badge for List Icons */
    .btn-list-icon {
      position: relative;
      /* Needed for absolute positioning of badge */
    }

    .btn-list-icon .badge-notes {
      position: absolute;
      top: -6px;
      right: -8px;
      min-width: 16px;
      height: 16px;
      line-height: 16px;
      padding: 0 4px;
      border-radius: 10px;
      background: #93c21c;
      color: #fff;
      font-size: 10px;
      font-weight: 700;
      text-align: center;
      pointer-events: none;
      z-index: 10;
    }

    /* Hide badge if count is 0 or empty */
    .btn-list-icon .badge-notes:empty,
    .btn-list-icon .badge-notes[data-count="0"] {
      display: none;
    }
  </style>
  <style>
    /* 1. Allow SweetAlert content to overflow so dropdowns aren't cut off */
    .swal2-html-container {
      overflow: visible !important;
      z-index: 2;
    }

    .swal2-popup {
      overflow: visible !important;
    }

    /* 2. Force Select2 dropdown to be on top of SweetAlert (z-index 1060+) */
    .select2-container--default .select2-dropdown {
      z-index: 99999999 !important;
    }

    /* 3. Style the employee option in the dropdown */
    .employee-option {
      display: flex;
      align-items: center;
      padding: 4px;
    }

    .employee-option img {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      margin-right: 10px;
      object-fit: cover;
    }
  </style>

  <style>
    /* ---------- Team hover popover ---------- */
    .team-popover {
      position: fixed;
      z-index: 99999;
      width: 320px;
      max-width: calc(100vw - 24px);
      background: rgba(255, 255, 255, .92);
      border: 1px solid rgba(15, 23, 42, .10);
      box-shadow: 0 18px 50px rgba(15, 23, 42, .22);
      border-radius: 16px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 12px;
      transform: translateY(6px);
      opacity: 0;
      pointer-events: none;
      transition: opacity .12s ease, transform .12s ease;
    }

    /* Kanban Column Toolbar (Search & Sort) */
    .column-toolbar {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      background: #eef2f7;
      border-bottom: 1px solid #d1d5db;
    }

    .col-search-input {
      flex: 1;
      height: 26px;
      font-size: 11px;
      padding: 2px 8px;
      border-radius: 4px;
      border: 1px solid #cbd5e1;
      transition: border-color 0.2s;
    }

    .col-search-input:focus {
      outline: none;
      border-color: #93c21c;
      box-shadow: 0 0 0 2px rgba(147, 194, 28, .15);
    }

    .col-sort-btn {
      background: #fff;
      border: 1px solid #cbd5e1;
      color: #64748b;
      border-radius: 4px;
      width: 26px;
      height: 26px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
    }

    .col-sort-btn:hover {
      background: #f1f5f9;
      color: #93c21c;
      border-color: #93c21c;
    }

    .team-popover.is-open {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }

    .team-popover__title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
    }

    .team-popover__title .t1 {
      font-weight: 800;
      font-size: 13px;
      letter-spacing: .2px;
      color: #0f172a;
    }

    .team-popover__title .t2 {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
    }

    .team-popover__list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      max-height: 260px;
      overflow: auto;
      padding-right: 4px;
    }

    .team-popover__item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 10px;
      border-radius: 12px;
      border: 1px solid rgba(15, 23, 42, .06);
      background: rgba(255, 255, 255, .75);
    }

    .team-popover__item:hover {
      background: rgba(241, 245, 249, .85);
      border-color: rgba(15, 23, 42, .10);
    }

    /* Card Border Colors */
    .card.status-playing {
      border-left-color: #93c21c !important;
    }

    .card.status-paused {
      border-left-color: #f3c12f !important;
    }

    .card.status-stopped {
      border-left-color: #dc3545 !important;
    }

    /* Button Highlighting */
    .kb-menu-item[data-run="playing"]:hover {
      background: #e7f5d0;
    }

    .kb-menu-item[data-run="paused"]:hover {
      background: #fff4d6;
    }

    .kb-menu-item[data-run="stopped"]:hover {
      background: #fde8e8;
    }

    .team-popover__avatar {
      width: 34px;
      height: 34px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #fff;
      box-shadow: 0 8px 18px rgba(15, 23, 42, .12);
      flex: 0 0 auto;
    }

    .team-popover__name {
      font-weight: 800;
      font-size: 13px;
      color: #0f172a;
      line-height: 1.2;
    }

    .team-popover__meta {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
      line-height: 1.2;
      margin-top: 2px;
    }

    /* optional: subtle cursor hint on team stacks */
    ul[data-team-hover] {
      cursor: pointer;
    }

    /* ---------- Team hover popover (with Assigned by + Date) ---------- */
    .team-popover__meta {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
      line-height: 1.25;
      margin-top: 2px;
    }

    .team-popover__meta .lbl {
      color: #94a3b8;
      font-weight: 700;
      margin-right: 6px;
    }

    .team-popover__meta .val {
      color: #0f172a;
      font-weight: 800;
    }

    .team-popover__meta .sep {
      margin: 0 8px;
      color: #cbd5e1;
    }

    .team-popover__meta .date {
      white-space: nowrap;
    }

    /* --- List View Specific Overrides --- */

    /* 1. Status Block in Table */
    .table .kb-status {
      margin-top: 6px;
      padding: 6px 8px;
      background: #f8f9fa;
      /* Lighter background for table */
      border: 1px solid #e9ecef;
      /* Subtle border */
      font-size: 0.8rem;
      min-width: 180px;
      /* Ensure it doesn't get too squashed */
      max-width: 240px;
    }

    .table .kb-status .badge {
      margin-bottom: 4px;
      /* Space between badge and metadata */
    }

    /* 2. Live Feed in Table (Customer Column) */
    .table .live-feed-bar {
      margin-top: 8px;
      background: #fdfdfd;
      border: 1px solid #f0f0f0;
      width: 100%;
      max-width: 450px;
      /* Prevent it from stretching infinitely */
    }

    /* 3. Adjust Table alignment for top vertical alignment */
    .table tbody tr td {
      vertical-align: top !important;
    }
  </style>
  <style>
    #ap-tab-calendar {
      height: calc(100vh - 140px);
    }

    /* adjust if needed */
    #ap-calendar-wrap {
      height: calc(100% - 48px);
    }

    /* subtract header */
    #ap-fullcalendar {
      height: 100%;
    }

    /* Date Age Indicators (Traffic Light System) */
    .traffic-light-wrapper {
      display: inline-flex;
      gap: 4px;
      align-items: center;
      background: #f8f9fa;
      padding: 4px 6px;
      border-radius: 12px;
      border: 1px solid #e9ecef;
    }

    /* Kanban positioning: Top right beside the menu */
    .card .traffic-light-wrapper {
      position: absolute;
      top: 6px;
      right: 76px;
      z-index: 4;
    }

    /* List view adjustments */
    .list-date-cell .traffic-light-wrapper {
      margin-bottom: 6px;
    }

    /* Base dot styling */
    .tl-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: #d1d5db;
      /* Inactive Gray */
      transition: all 0.3s ease;
    }

    /* Active glowing states */
    .tl-dot.tl-green.is-active {
      background-color: #93c21c;
    }

    .tl-dot.tl-orange.is-active {
      background-color: #fd7e14;
    }

    .tl-dot.tl-red.is-active {
      background-color: #dc3545;
    }

    /* Splash Animations */
    @keyframes splash-green {
      0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
      }

      70% {
        box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
      }
    }

    @keyframes splash-orange {
      0% {
        box-shadow: 0 0 0 0 rgba(253, 126, 20, 0.7);
      }

      70% {
        box-shadow: 0 0 0 6px rgba(253, 126, 20, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(253, 126, 20, 0);
      }
    }

    @keyframes splash-red {
      0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
      }

      70% {
        box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
      }
    }

    .splash-green {
      animation: splash-green 1.5s infinite;
    }

    .splash-orange {
      animation: splash-orange 1.5s infinite;
    }

    .splash-red {
      animation: splash-red 1.5s infinite;
    }
  </style>
  <style>
    .junk-grid {
      display: grid;
      grid-template-columns:
        minmax(220px, 1.2fr) minmax(220px, 1.2fr) minmax(130px, .7fr) minmax(170px, .9fr) 110px minmax(240px, 1.3fr) minmax(250px, 1.4fr);
      gap: 14px;
      align-items: center;
    }

    #junkInner .oc-list-head {
      padding: 16px 16px 10px 16px;
      color: var(--text-muted);
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    #junkInner .oc-item-row {
      padding: 16px;
    }

    #junkInner .restore-select {
      min-width: 210px;
      max-width: 230px;
    }

    #junkInner .junk-reason {
      white-space: normal !important;
      overflow: visible !important;
      text-overflow: unset !important;
      line-height: 1.5;
    }

    #junkInner .oc-actions {
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: 8px;
    }

    @media (max-width: 1280px) {
      .junk-grid {
        grid-template-columns: 1fr !important;
      }

      #junkInner .oc-list-head {
        display: none;
      }
    }
  </style>

  <style>
    /* --- List View Status Collapse/Expand --- */
    .table .kb-status {
      cursor: pointer;
      transition: background-color 0.15s ease;
      position: relative;
    }

    .table .kb-status:hover {
      background-color: #eef2f7 !important;
    }

    /* Hide metadata and reasons by default in the table */
    .table .kb-status .meta,
    .table .kb-status .status-reason {
      display: none !important;
    }

    /* Show them when the expanded class is toggled */
    .table .kb-status.is-expanded .meta {
      display: grid !important;
      /* Preserves your grid semantics */
    }

    .table .kb-status.is-expanded .status-reason {
      display: block !important;
    }

    /* Optional: Smoothly rotate a little toggle arrow */
    .table .kb-status .meta-toggle-icon {
      transition: transform 0.2s ease;
      display: inline-block;
      margin-left: 4px;
    }

    .table .kb-status.is-expanded .meta-toggle-icon {
      transform: rotate(180deg);
    }

    .badge-success {
      background: #93c21c !important;
    }

    /* Ensure action icons align perfectly beside the customer name */
    .customer-name-wrapper {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.75rem;
    }

    /* Force table wrappers to allow dropdowns to break outside the borders */
    .table-responsive,
    .table-responsive tr,
    .table-responsive td {
      overflow: visible !important;
    }

    /* Reset margin on the action bar when placed inline */
    .table .list-action-bar {
      margin-top: 0 !important;
    }
  </style>
  <style>
    #basic-tabs-components .card,
    #basic-tabs-components .card-content,
    #basic-tabs-components .card-body {
      overflow: visible !important;
    }

    #basic-tabs-components .pro-main {
      min-width: 0;
    }

    /* Main tabs wrapper */
    #basic-tabs-components .pro-tabs-shell {
      background: #ffffff;
      border: 1px solid #e5eef5;
      border-radius: 22px;
      padding: 14px;
      margin-bottom: 16px;
      box-shadow: 0 12px 34px rgba(15, 23, 42, .06);
    }

    /* Same row: tabs left, sort right */
    #basic-tabs-components .pro-tabs-topbar {
      display: flex !important;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      width: 100%;
    }

    /* Force tabs horizontal */
    #basic-tabs-components .pro-tabs-nav,
    #basic-tabs-components ul.nav.nav-tabs.pro-tabs-nav {
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: flex-start !important;
      flex-wrap: wrap !important;
      gap: 10px !important;
      border: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
      width: auto !important;
      list-style: none !important;
    }

    /* Stop any theme from making li full-width / vertical */
    #basic-tabs-components .pro-tabs-nav .nav-item {
      display: block !important;
      width: auto !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    /* Tab button */
    #basic-tabs-components .pro-tabs-nav .nav-link {
      position: relative;
      min-height: 54px;
      padding: 8px 14px 8px 10px !important;
      border: 1px solid #dbeafe !important;
      border-radius: 18px !important;
      background: #f8fafc !important;
      color: #475569 !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 10px !important;
      font-size: 13px;
      font-weight: 800;
      line-height: 1;
      white-space: nowrap;
      transition: all .18s ease;
      box-shadow: none !important;
    }

    /* Important because your global CSS has: a { border-radius:0px !important; } */
    #basic-tabs-components .pro-tabs-nav .nav-link {
      border-radius: 18px !important;
    }

    #basic-tabs-components .pro-tabs-nav .nav-link:hover {
      background: #eff6ff !important;
      color: #2563eb !important;
      border-color: #bfdbfe !important;
      transform: translateY(-1px);
    }

    /* Active tab */
    #basic-tabs-components .pro-tabs-nav .nav-link.active {
      background: linear-gradient(135deg, #74b2d4 0%, #93c21c 100%) !important;
      color: #ffffff !important;
      border-color: transparent !important;
      box-shadow: 0 12px 26px rgba(116, 178, 212, .28) !important;
    }

    /* Icon box */
    #basic-tabs-components .pro-tab-icon {
      width: 36px;
      height: 36px;
      border-radius: 14px;
      background: #ffffff;
      color: #74b2d4;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
      box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
    }

    #basic-tabs-components .pro-tab-icon svg {
      width: 18px;
      height: 18px;
      stroke-width: 2.4;
    }

    #basic-tabs-components .pro-tabs-nav .nav-link.active .pro-tab-icon {
      background: rgba(255, 255, 255, .18);
      color: #ffffff;
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .24);
    }

    /* Text */
    #basic-tabs-components .pro-tab-text {
      display: inline-flex;
      flex-direction: column;
      gap: 5px;
      min-width: 0;
    }

    #basic-tabs-components .pro-tab-title {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      color: inherit;
    }

    #basic-tabs-components .pro-tab-sub {
      font-size: 10px;
      font-weight: 800;
      opacity: .65;
      color: inherit;
    }

    #basic-tabs-components .pro-tabs-nav .nav-link.active .pro-tab-sub {
      opacity: .9;
    }

    /* Count badge */
    #basic-tabs-components .pro-tab-count {
      min-width: 23px;
      height: 23px;
      padding: 0 7px;
      border-radius: 999px;
      background: #e2e8f0;
      color: #334155;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 900;
      line-height: 23px;
    }

    #basic-tabs-components .pro-tabs-nav .nav-link.active .pro-tab-count {
      background: rgba(255, 255, 255, .24);
      color: #ffffff;
    }

    /* Sort box right */
    #basic-tabs-components .pro-sort-box {
      min-height: 54px;
      padding: 8px 10px;
      border: 1px solid #e2e8f0;
      border-radius: 18px;
      background: #f8fafc;
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 0 0 auto;
    }

    #basic-tabs-components .pro-sort-icon {
      width: 36px;
      height: 36px;
      border-radius: 14px;
      background: #ffffff;
      color: #74b2d4;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
    }

    #basic-tabs-components .pro-sort-icon svg {
      width: 18px;
      height: 18px;
      stroke-width: 2.4;
    }

    #basic-tabs-components .pro-sort-label {
      margin: 0;
      color: #64748b;
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
      white-space: nowrap;
    }

    #basic-tabs-components .pro-sort-select {
      width: 240px;
      height: 36px;
      border: 0 !important;
      border-radius: 12px !important;
      background: #ffffff !important;
      color: #334155;
      font-size: 12px;
      font-weight: 700;
      box-shadow: inset 0 0 0 1px #e2e8f0;
      outline: none;
    }

    #basic-tabs-components .pro-sort-select:focus {
      box-shadow: inset 0 0 0 2px rgba(116, 178, 212, .45);
    }

    /* Content card */
    #basic-tabs-components .pro-tabs-content-card {
      background: #ffffff;
      border: 1px solid #e5eef5;
      border-radius: 22px;
      padding: 14px;
      box-shadow: 0 12px 34px rgba(15, 23, 42, .05);
    }

    /* Keep Bootstrap panes clean */
    #basic-tabs-components .tab-content {
      width: 100%;
    }

    #basic-tabs-components .tab-pane {
      width: 100%;
    }

    /* Responsive */
    @media (max-width: 991px) {
      #basic-tabs-components .pro-tabs-topbar {
        align-items: stretch;
      }

      #basic-tabs-components .pro-tabs-nav {
        width: 100% !important;
      }

      #basic-tabs-components .pro-tabs-nav .nav-item {
        flex: 1 1 calc(50% - 10px);
      }

      #basic-tabs-components .pro-tabs-nav .nav-link {
        width: 100%;
        justify-content: flex-start;
      }

      #basic-tabs-components .pro-sort-box {
        width: 100%;
      }

      #basic-tabs-components .pro-sort-select {
        width: 100%;
      }
    }

    @media (max-width: 575px) {

      #basic-tabs-components .pro-tabs-shell,
      #basic-tabs-components .pro-tabs-content-card {
        border-radius: 18px;
        padding: 10px;
      }

      #basic-tabs-components .pro-tabs-nav .nav-item {
        flex: 1 1 100%;
      }

      #basic-tabs-components .pro-tab-icon,
      #basic-tabs-components .pro-sort-icon {
        width: 32px;
        height: 32px;
        border-radius: 12px;
      }

      #basic-tabs-components .pro-sort-box {
        flex-wrap: wrap;
      }

      #basic-tabs-components .pro-sort-label {
        width: calc(100% - 44px);
      }

      #basic-tabs-components .pro-sort-select {
        width: 100%;
      }
    }
  </style>


  <style>
    /* =========================================================
             Dynamic Lead Stage Manager (lsm-) - scoped, safe
          ========================================================= */
    .lsm-btn {
      min-height: 56px;
      border: 1px solid #dbeafe;
      border-radius: 18px !important;
      background: #ffffff;
      color: #334155;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 8px 14px;
      font-size: 12px;
      font-weight: 900;
      box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
      transition: all .18s ease;
      white-space: nowrap;
    }

    .lsm-btn:hover {
      color: #2563eb;
      border-color: #bfdbfe;
      background: #eff6ff;
      transform: translateY(-1px);
    }

    .lsm-btn .lsm-btn-icon {
      width: 38px;
      height: 38px;
      border-radius: 14px;
      background: linear-gradient(135deg, #74b2d4, #93c21c);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }

    .lsm-modal {
      position: fixed;
      inset: 0;
      z-index: 200010;
      display: none;
    }

    .lsm-modal.is-open {
      display: block;
    }

    .lsm-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, .55);
    }

    .lsm-panel {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: min(1180px, 94vw);
      max-height: 92vh;
      background: #ffffff;
      border-radius: 24px;
      box-shadow: 0 30px 90px rgba(15, 23, 42, .32);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .lsm-head {
      padding: 18px 20px;
      border-bottom: 1px solid #e5eef5;
      background: linear-gradient(135deg, rgba(116, 178, 212, .12), rgba(147, 194, 28, .10));
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
    }

    .lsm-title {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
    }

    .lsm-title-icon {
      width: 44px;
      height: 44px;
      border-radius: 16px;
      background: linear-gradient(135deg, #74b2d4, #93c21c);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }

    .lsm-title h5 {
      margin: 0;
      color: #0f172a;
      font-weight: 900;
    }

    .lsm-title p {
      margin: 3px 0 0;
      color: #64748b;
      font-size: 12px;
      font-weight: 700;
    }

    .lsm-body {
      padding: 16px;
      overflow: auto;
      background: #f8fafc;
    }

    .lsm-grid {
      display: grid;
      grid-template-columns: 360px minmax(0, 1fr);
      gap: 16px;
    }

    .lsm-card {
      background: #ffffff;
      border: 1px solid #e5eef5;
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
      overflow: hidden;
    }

    .lsm-card-head {
      padding: 14px 16px;
      border-bottom: 1px solid #eef2f7;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .lsm-card-head strong {
      color: #0f172a;
      font-weight: 900;
    }

    .lsm-card-body {
      padding: 16px;
    }

    .lsm-help {
      border-radius: 16px;
      border: 1px dashed rgba(116, 178, 212, .55);
      background: rgba(116, 178, 212, .08);
      padding: 12px;
      color: #475569;
      font-size: 12px;
      line-height: 1.5;
      font-weight: 700;
    }

    .lsm-stage-row {
      display: grid;
      grid-template-columns: 70px minmax(180px, 1.3fr) 130px 110px 150px;
      gap: 10px;
      align-items: center;
      padding: 12px 14px;
      border-bottom: 1px solid #eef2f7;
    }

    .lsm-stage-row:last-child {
      border-bottom: 0;
    }

    .lsm-stage-head {
      color: #64748b;
      font-size: 10px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .05em;
      background: #f8fafc;
    }

    .lsm-stage-name {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
    }

    .lsm-color-dot {
      width: 16px;
      height: 16px;
      border-radius: 999px;
      box-shadow: 0 0 0 3px rgba(15, 23, 42, .05);
      flex: 0 0 auto;
    }

    .lsm-stage-name strong {
      display: block;
      color: #0f172a;
      font-weight: 900;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .lsm-stage-name small {
      display: block;
      color: #64748b;
      font-size: 11px;
      font-weight: 700;
    }

    .lsm-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 22px;
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 900;
      background: #eef2f7;
      color: #334155;
    }

    .lsm-badge--warn {
      background: #fff7ed;
      color: #9a3412;
    }

    .lsm-badge--ok {
      background: #ecfdf5;
      color: #166534;
    }

    .lsm-badge--blue {
      background: #eff6ff;
      color: #1d4ed8;
    }

    .lsm-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 6px;
    }

    .lsm-icon-btn {
      width: 34px;
      height: 34px;
      border-radius: 12px !important;
      border: 1px solid #e2e8f0;
      background: #ffffff;
      color: #475569;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .lsm-icon-btn:hover {
      background: #f8fafc;
      color: #2563eb;
    }

    .lsm-icon-btn.danger:hover {
      color: #e50656;
      border-color: #fecdd3;
      background: #fff1f2;
    }

    .lsm-empty {
      padding: 34px;
      text-align: center;
      color: #64748b;
      font-weight: 800;
    }

    .lsm-footer {
      padding: 14px 16px;
      border-top: 1px solid #e5eef5;
      background: #ffffff;
      display: flex;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
    }

    .lsm-sort-input {
      width: 64px;
      height: 34px;
      border-radius: 12px !important;
      font-size: 12px;
      font-weight: 800;
    }

    @media (max-width: 991px) {
      .lsm-grid {
        grid-template-columns: 1fr;
      }

      .lsm-stage-row {
        grid-template-columns: 56px minmax(160px, 1fr);
      }

      .lsm-stage-row>div:nth-child(3),
      .lsm-stage-row>div:nth-child(4),
      .lsm-stage-row>div:nth-child(5) {
        grid-column: 2;
      }

      .lsm-stage-head {
        display: none;
      }
    }
  </style>


  <style>
    /* =========================================================
             Final restore/UX patch: no gradients, cleaner drawers, full actions
          ========================================================= */
    #basic-tabs-components .pro-layout {
      grid-template-columns: minmax(0, 1fr) !important;
    }

    #basic-tabs-components .pro-rail {
      display: none !important;
    }

    #basic-tabs-components .pro-tabs-nav .nav-link.active {
      background: #74b2d4 !important;
      border-color: #74b2d4 !important;
    }

    .lsm-btn .lsm-btn-icon,
    .lsm-title-icon {
      background: #74b2d4 !important;
    }

    .lsm-head {
      background: #ffffff !important;
    }

    .lsm-btn--filter .lsm-btn-icon {
      background: #93c21c !important;
    }

    .lsm-btn--filter {
      position: relative;
    }

    .lsm-btn--filter .rail-badge {
      position: absolute;
      top: -6px;
      right: -6px;
    }

    .drawer {
      width: 560px;
      max-width: 96vw;
      border-top-left-radius: 24px;
      border-bottom-left-radius: 24px;
      overflow: hidden;
    }

    .drawer-header {
      background: #fff;
      border-bottom: 1px solid #e5eef5;
      padding: 16px 18px;
    }

    .drawer-header h5 {
      font-weight: 900;
      color: #0f172a;
    }

    .drawer-body {
      background: #f8fafc;
      padding: 16px;
    }

    #kanbanFilterForm {
      background: #fff;
      border: 1px solid #e5eef5;
      border-radius: 20px;
      padding: 14px;
      box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
    }

    #kanbanFilterForm label {
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    #kanbanFilterForm label::before {
      content: "";
      width: 26px;
      height: 26px;
      border-radius: 10px;
      background: #edf7fc;
      display: inline-block;
      flex: 0 0 auto;
    }

    #kanbanFilterForm .form-control {
      border-radius: 12px !important;
      border-color: #dbeafe;
      min-height: 40px;
    }

    #activeFilterChips .chip {
      background: #fff;
      border: 1px solid #e5eef5;
      color: #334155;
      font-weight: 800;
    }

    .notes-drawer {
      border-top-left-radius: 26px;
      border-bottom-left-radius: 26px;
      overflow: hidden;
      box-shadow: -24px 0 60px rgba(15, 23, 42, .22);
    }

    .notes-head {
      background: #ffffff;
      border-bottom: 1px solid #e5eef5;
      padding: 16px 18px;
    }

    .notes-title {
      font-weight: 900;
      color: #0f172a;
      gap: 10px;
    }

    .notes-title>i {
      width: 38px;
      height: 38px;
      border-radius: 14px;
      background: #edf7fc;
      color: #74b2d4;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .notes-tabs {
      padding: 8px 12px 0;
      background: #fff;
      gap: 8px;
    }

    .notes-tab {
      border: 1px solid #e5eef5;
      border-bottom: 0;
      background: #f8fafc;
      border-radius: 14px 14px 0 0;
      font-weight: 900;
      color: #64748b;
    }

    .notes-tab--active {
      background: #fff;
      color: #0f172a;
      border-color: #93c21c;
    }

    .notes-body {
      background: #f8fafc;
      padding: 14px;
    }

    .note-bubble {
      border: 1px solid rgba(15, 23, 42, .06);
      box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
    }

    .note-bubble.other {
      background: #fff;
    }

    .note-bubble.me {
      background: #eef8da;
    }

    .notes-foot {
      background: #fff;
      border-top: 1px solid #e5eef5;
      padding: 12px;
    }

    .kbz-select {
      height: 32px;
      border: 1px solid #dbeafe;
      border-radius: 999px;
      background: #fff;
      color: #334155;
      font-size: 12px;
      font-weight: 900;
      padding: 0 10px;
    }

    .kanban-zoom-card.kb-width-compact .column {
      width: 235px !important;
    }

    .kanban-zoom-card.kb-width-normal .column {
      width: 300px !important;
    }

    .kanban-zoom-card.kb-width-wide .column {
      width: 380px !important;
    }

    .column h3 {
      background: #93c21c !important;
    }

    .column[data-stage-color] h3 {
      background: var(--stage-color, #93c21c) !important;
    }

    .lsm-grid {
      grid-template-columns: 400px minmax(0, 1fr);
    }

    .lsm-form label {
      display: block;
      font-size: 11px;
      font-weight: 900;
      color: #64748b;
      text-transform: uppercase;
      margin-bottom: 6px;
    }

    .lsm-form-grid {
      display: grid;
      grid-template-columns: 140px minmax(0, 1fr);
      gap: 12px;
    }

    .lsm-color-input-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      border: 1px solid #dbeafe;
      border-radius: 14px;
      padding: 6px;
      background: #f8fafc;
      min-height: 44px;
    }

    .lsm-color-input-wrap input[type="color"] {
      width: 50px;
      height: 34px;
      padding: 0;
      border: 0;
      background: transparent;
    }

    .lsm-color-input-wrap span {
      font-weight: 900;
      color: #334155;
      font-size: 12px;
    }

    .lsm-toggle-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      margin: 12px 0;
    }

    .lsm-toggle-card {
      display: grid !important;
      grid-template-columns: auto 38px 1fr;
      gap: 10px;
      align-items: center;
      border: 1px solid #e5eef5;
      border-radius: 16px;
      padding: 10px 12px;
      background: #fff;
      cursor: pointer;
      margin: 0 !important;
    }

    .lsm-toggle-card input {
      width: 18px;
      height: 18px;
    }

    .lsm-toggle-icon {
      width: 38px;
      height: 38px;
      border-radius: 14px;
      background: #edf7fc;
      color: #74b2d4;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .lsm-toggle-card strong {
      display: block;
      font-size: 13px;
      color: #0f172a;
      font-weight: 900;
    }

    .lsm-toggle-card small {
      display: block;
      color: #64748b;
      font-size: 11px;
      font-weight: 700;
    }

    .lsm-save-btn {
      border-radius: 14px !important;
      min-height: 44px;
      font-weight: 900;
      background: #93c21c !important;
      border-color: #93c21c !important;
    }

    .lsm-stage-row {
      grid-template-columns: 42px minmax(190px, 1.4fr) 130px 110px 150px;
    }

    .lsm-drag-handle {
      cursor: grab;
      color: #94a3b8;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      border-radius: 12px;
      background: #f8fafc;
      border: 1px solid #e5eef5;
    }

    .lsm-stage-row.is-dragging {
      opacity: .45;
    }

    .lsm-stage-row.is-drop-target {
      outline: 2px dashed #93c21c;
      outline-offset: -4px;
    }

    .lsm-stage-head .lsm-drag-handle {
      visibility: hidden;
    }

    .select2-container .lsm-icon-option {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 800;
    }

    .select2-container .lsm-icon-option i {
      width: 22px;
      height: 22px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #74b2d4;
    }

    @media(max-width:991px) {
      .lsm-grid {
        grid-template-columns: 1fr;
      }

      .lsm-form-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>


  <style>
    /* =======================
           Professional List View + Stage Select2
        ======================= */
    .pro-list-table {
      border-collapse: separate !important;
      border-spacing: 0 10px !important;
      background: transparent !important;
    }

    .pro-list-table thead th {
      background: #f8fafc;
      color: #334155;
      border: 0 !important;
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
      padding: 10px 12px !important;
      white-space: nowrap;
      vertical-align: middle !important;
    }

    .pro-list-table thead th span {
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .pro-list-table thead th .feather {
      width: 14px;
      height: 14px;
    }

    .pro-list-table tbody tr {
      background: #fff;
      box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
      transition: transform .15s ease, box-shadow .15s ease;
    }

    .pro-list-table tbody tr:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 26px rgba(15, 23, 42, .10);
    }

    .pro-list-table tbody td {
      border-top: 1px solid #e5e7eb !important;
      border-bottom: 1px solid #e5e7eb !important;
      padding: 12px !important;
      vertical-align: middle !important;
    }

    .pro-list-table tbody td:first-child {
      border-left: 1px solid #e5e7eb !important;
      border-top-left-radius: 16px;
      border-bottom-left-radius: 16px;
    }

    .pro-list-table tbody td:last-child {
      border-right: 1px solid #e5e7eb !important;
      border-top-right-radius: 16px;
      border-bottom-right-radius: 16px;
    }

    .pro-list-table th.sortable {
      cursor: pointer;
      user-select: none;
    }

    .pro-list-table th.sortable:hover {
      color: #74b2d4;
    }

    .pro-list-table th.sortable .sort-icon {
      margin-left: 5px;
      opacity: .35;
      transition: .15s ease;
    }

    .pro-list-table th.sortable.active .sort-icon {
      opacity: 1;
      color: #93c21c;
    }

    .pro-list-table th.sortable.desc .sort-icon {
      transform: rotate(180deg);
    }

    .customer-name-wrapper {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
    }

    .customer-link {
      color: #0f172a;
      font-weight: 900;
      text-decoration: none;
    }

    .customer-link:hover {
      color: #74b2d4;
      text-decoration: none;
    }

    .list-action-bar {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      flex-wrap: nowrap;
    }

    .btn-list-icon {
      width: 30px;
      height: 30px;
      border: 1px solid #e5e7eb;
      background: #fff;
      color: #64748b;
      border-radius: 10px !important;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
    }

    .btn-list-icon:hover {
      background: #f8fafc;
      color: #74b2d4;
      border-color: #c0d8ea;
    }

    .btn-list-icon .feather {
      width: 15px;
      height: 15px;
    }

    .stage-select2-option,
    .stage-select2-selection {
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
    }

    .stage-color-dot {
      width: 11px;
      height: 11px;
      border-radius: 999px;
      flex: 0 0 11px;
      box-shadow: 0 0 0 3px rgba(15, 23, 42, .04);
    }

    .stage-select2-icon {
      width: 17px;
      height: 17px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
    }

    .stage-select2-icon .feather {
      width: 15px;
      height: 15px;
    }

    .stage-select2-label {
      font-size: 13px;
      font-weight: 800;
      color: #334155;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .stage-select {
      min-width: 160px;
    }

    .stage-select+.select2-container {
      min-width: 170px !important;
    }

    .stage-select+.select2-container .select2-selection--single,
    .stage-color-select+.select2-container .select2-selection--single {
      min-height: 36px;
      border-radius: 12px;
      border-color: #dbeafe;
      display: flex;
      align-items: center;
    }

    .stage-select+.select2-container .select2-selection__rendered,
    .stage-color-select+.select2-container .select2-selection__rendered {
      line-height: 34px !important;
      width: 100%;
    }

    .select2-results__option .stage-select2-option {
      padding: 2px 0;
    }

    @media(max-width:768px) {
      .customer-name-wrapper {
        flex-direction: column;
        align-items: flex-start
      }

      .list-action-bar {
        flex-wrap: wrap
      }

      .pro-list-table {
        min-width: 980px
      }
    }
  </style>


  <style>
    /* ===== Company/Product Stage Workflow Switch ===== */
    .kb-workflow-switch {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      padding: 8px;
      border: 1px solid #dbeafe;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }

    .kb-workflow-label {
      font-size: 11px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: #64748b;
      margin-right: 2px;
    }

    .kb-workflow-mode {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      border: 1px solid #e2e8f0;
      background: #f8fafc;
      color: #334155;
      border-radius: 12px;
      height: 32px;
      padding: 0 10px;
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
    }

    .kb-workflow-mode:hover {
      border-color: #74b2d4;
      background: #eef8fc;
    }

    .kb-workflow-mode.is-active {
      background: #93c21c;
      border-color: #93c21c;
      color: #fff;
    }

    .kb-workflow-product {
      height: 32px;
      min-width: 220px;
      border: 1px solid #dbeafe;
      border-radius: 12px;
      padding: 0 10px;
      font-size: 12px;
      font-weight: 800;
      color: #334155;
      background: #fff;
    }

    .kb-workflow-product:disabled {
      background: #f1f5f9;
      color: #94a3b8;
    }

    .kb-workflow-select2-option {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 3px 0;
    }

    .kb-workflow-select2-icon {
      width: 30px;
      height: 30px;
      border-radius: 10px;
      background: #eef8fc;
      color: #74b2d4;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 30px;
    }

    .kb-workflow-select2-text {
      display: flex;
      flex-direction: column;
      line-height: 1.15;
      min-width: 0;
    }

    .kb-workflow-select2-title {
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .kb-workflow-select2-sub {
      font-size: 10px;
      font-weight: 800;
      color: #64748b;
      margin-top: 2px;
    }

    .kb-workflow-switch .select2-container {
      min-width: 260px !important;
    }

    .kb-workflow-switch .select2-selection--single {
      height: 32px !important;
      border: 1px solid #dbeafe !important;
      border-radius: 12px !important;
      display: flex !important;
      align-items: center !important;
      background: #fff !important;
    }

    .kb-workflow-switch .select2-selection__rendered {
      line-height: 30px !important;
      font-size: 12px !important;
      font-weight: 800 !important;
      color: #334155 !important;
      padding-left: 10px !important;
      padding-right: 28px !important;
    }

    .kb-workflow-switch .select2-selection__arrow {
      height: 30px !important;
    }

    .kb-workflow-switch .select2-container--disabled .select2-selection--single {
      background: #f1f5f9 !important;
      color: #94a3b8 !important;
    }

    .kb-workflow-product-box {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      min-width: 0;
    }

    .kb-workflow-product-box.d-none {
      display: none !important;
    }

    .kb-workflow-apply {
      height: 32px;
      border: 1px solid #93c21c;
      background: #93c21c;
      color: #fff;
      border-radius: 12px;
      padding: 0 12px;
      font-size: 12px;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      cursor: pointer;
      white-space: nowrap;
    }

    .kb-workflow-apply:hover {
      background: #82ad17;
      border-color: #82ad17;
    }

    .kb-workflow-apply:disabled {
      background: #e2e8f0;
      border-color: #e2e8f0;
      color: #94a3b8;
      cursor: not-allowed;
    }

    .kb-workflow-switch.is-product-draft .kb-workflow-hint {
      color: #f59e0b;
    }

    .kb-workflow-switch .select2-container {
      min-width: 260px !important;
    }

    .kb-workflow-hint {
      font-size: 11px;
      color: #64748b;
      font-weight: 700;
    }

    .swal-workflow-box {
      border: 1px solid #dbeafe;
      background: #f8fafc;
      border-radius: 14px;
      padding: 10px;
      margin-bottom: 12px;
    }

    .swal-workflow-mode-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 10px;
    }

    .swal-workflow-mode-btn {
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 12px;
      padding: 8px 10px;
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
      text-align: center;
    }

    .swal-workflow-mode-btn.is-active {
      background: #93c21c;
      color: #fff;
      border-color: #93c21c;
    }

    .swal-workflow-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .swal-workflow-grid .form-group {
      margin-bottom: 0;
    }

    .swal-workflow-forward {
      margin-top: 8px;
      border: 1px dashed #93c21c;
      background: #fff;
      color: #334155;
      border-radius: 12px;
      height: 34px;
      width: 100%;
      font-weight: 900;
      cursor: pointer;
    }

    .swal-overflow-visible {
      overflow: visible !important;
      z-index: 200010 !important;
    }

    @media(max-width:768px) {

      .swal-workflow-grid,
      .swal-workflow-mode-row {
        grid-template-columns: 1fr
      }

      .kb-workflow-product {
        min-width: 100%;
      }
    }
  </style>



  <style>
    /* ===== Enterprise stage workflow UX ===== */
    .column.kb-drop-target {
      outline: 3px solid #93c21c;
      outline-offset: -6px;
      background: #f7fbef !important;
      box-shadow: inset 0 0 0 999px rgba(147, 194, 28, .06), 0 10px 28px rgba(15, 23, 42, .12);
    }

    .column.kb-drop-target h3 {
      background: #93c21c !important;
    }

    .column.kb-drop-target::after {
      content: "Hier ablegen";
      position: absolute;
      top: 54px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 5;
      background: #ffffff;
      color: #2f5c00;
      border: 1px solid rgba(147, 194, 28, .45);
      border-radius: 999px;
      padding: 4px 12px;
      font-size: 11px;
      font-weight: 900;
      box-shadow: 0 6px 16px rgba(15, 23, 42, .12);
      pointer-events: none;
    }

    .swal-enterprise-target {
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid #dbeafe;
      background: #f8fafc;
      border-radius: 14px;
      padding: 10px 12px;
      margin-bottom: 12px;
    }

    .swal-enterprise-target-icon {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: #93c21c;
      color: #fff;
      flex: 0 0 38px;
    }

    .swal-enterprise-target-title {
      font-weight: 900;
      color: #0f172a;
    }

    .swal-enterprise-target-sub {
      font-size: 12px;
      color: #64748b;
      margin-top: 2px;
    }

    .swal-product-info-box {
      border: 1px solid #e5e7eb;
      background: #fff;
      border-radius: 14px;
      padding: 10px 12px;
      margin-bottom: 12px;
    }

    .swal-product-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    @media(max-width:640px) {
      .swal-product-info-grid {
        grid-template-columns: 1fr;
      }
    }

    .product-stage-info-card {
      text-align: left;
      border: 1px solid #e5e7eb;
      background: #f8fafc;
      border-radius: 14px;
      padding: 12px;
    }

    .product-stage-info-row {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 8px 0;
      border-bottom: 1px dashed #e5e7eb;
    }

    .product-stage-info-row:last-child {
      border-bottom: 0;
    }

    .product-stage-info-row i {
      color: #93c21c;
      margin-top: 2px;
    }

    .swal-reminder-toggle-box {
      margin-top: 12px;
      border: 1px solid #dbeafe;
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border-radius: 16px;
      padding: 12px;
    }

    .swal-reminder-toggle-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .swal-reminder-toggle-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .swal-reminder-toggle-title i {
      color: #93c21c;
    }

    .swal-reminder-switch {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      font-weight: 900;
      color: #334155;
      cursor: pointer;
      user-select: none;
    }

    .swal-reminder-switch input {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }

    .swal-reminder-slider {
      width: 46px;
      height: 24px;
      border-radius: 999px;
      background: #cbd5e1;
      position: relative;
      transition: .18s ease;
      box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .08);
    }

    .swal-reminder-slider:before {
      content: '';
      position: absolute;
      left: 3px;
      top: 3px;
      width: 18px;
      height: 18px;
      border-radius: 999px;
      background: #fff;
      box-shadow: 0 1px 4px rgba(15, 23, 42, .25);
      transition: .18s ease;
    }

    .swal-reminder-switch input:checked+.swal-reminder-slider {
      background: #93c21c;
    }

    .swal-reminder-switch input:checked+.swal-reminder-slider:before {
      transform: translateX(22px);
    }

    .swal-reminder-fields {
      display: none;
      margin-top: 12px;
      border-top: 1px dashed #dbeafe;
      padding-top: 12px;
    }

    .swal-reminder-fields.is-open {
      display: block;
    }

    .swal-reminder-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .swal-reminder-grid-3 {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 10px;
    }

    .swal-reminder-field label {
      display: block;
      font-size: 11px;
      font-weight: 900;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: 4px;
    }

    .swal-reminder-field input,
    .swal-reminder-field select,
    .swal-reminder-field textarea {
      width: 100%;
      border: 1px solid #dbeafe;
      border-radius: 12px;
      padding: 8px 10px;
      font-size: 13px;
      color: #0f172a;
      background: #fff;
    }

    .swal-reminder-field textarea {
      min-height: 72px;
      resize: vertical;
    }

    .swal-reminder-field-full {
      grid-column: 1 / -1;
    }

    @media(max-width:720px) {

      .swal-reminder-grid,
      .swal-reminder-grid-3 {
        grid-template-columns: 1fr;
      }
    }

    .kb-reminder-summary {
      display: none;
    }

    .card.kb-summary-ready .kb-reminder-summary {
      display: block;
    }

    .kb-reminder-summary {
      margin-top: 8px;
      border: 1px solid #dbeafe;
      background: #fff;
      border-radius: 13px;
      padding: 8px 9px;
      position: relative;
      z-index: 2;
      display: flex;
      flex-direction: column;
      gap: 6px;
      overflow: hidden;
    }

    .kb-reminder-summary.is-empty {
      border-style: dashed;
      color: #64748b;
      background: #f8fafc;
    }

    .kb-reminder-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .kb-reminder-title {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 11px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .04em;
      min-width: 0;
    }

    .kb-reminder-title .feather,
    .kb-reminder-title i {
      width: 14px;
      height: 14px;
      color: #93c21c;
      flex: 0 0 auto;
    }

    .kb-reminder-priority {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 999px;
      padding: 4px 7px;
      font-size: 10px;
      font-weight: 900;
      background: #e2e8f0;
      color: #334155;
      white-space: nowrap;
    }

    .kb-reminder-priority.normal {
      background: #e7f5d0;
      color: #2f5c00;
    }

    .kb-reminder-priority.important {
      background: #fff3bf;
      color: #7c4a03;
    }

    .kb-reminder-priority.critical {
      background: #ffe2e2;
      color: #991b1b;
    }

    .kb-reminder-body {
      font-size: 11px;
      color: #334155;
      line-height: 1.35;
      display: grid;
      grid-template-columns: 14px 1fr;
      gap: 4px 6px;
    }

    .kb-reminder-body .feather,
    .kb-reminder-body i {
      width: 13px;
      height: 13px;
      color: #74b2d4;
      margin-top: 1px;
    }

    .kb-reminder-body strong {
      color: #0f172a;
    }

    .kb-reminder-due {
      font-variant-numeric: tabular-nums;
    }

    .kb-reminder-overdue {
      border-color: #dc2626;
      background: #fff7f7;
    }

    .kb-reminder-due-today {
      border-color: #f59e0b;
      background: #fffbeb;
    }

    .btn-reminder-create {
      color: #93c21c !important;
    }

    .btn-reminder-create:hover {
      background: #e7f5d0 !important;
      color: #2f5c00 !important;
    }

    .btn-reminder-create .badge-notes,
    .btn-reminder-create .kb-reminder-button-count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      position: absolute;
      top: -6px;
      right: -6px;
      min-width: 18px;
      height: 18px;
      line-height: 18px;
      padding: 0 4px;
      border-radius: 999px;
      background: #dc2626;
      color: #fff;
      font-size: 10px;
      font-weight: 900;
      text-align: center;
      pointer-events: none;
      z-index: 10;
    }

    .kb-card-reminder-carousel {
      position: relative;
    }

    .kb-card-reminder-track {
      display: flex;
      transition: transform .22s ease;
      will-change: transform;
    }

    .kb-card-reminder-slide {
      min-width: 100%;
      display: none;
    }

    .kb-card-reminder-slide.is-active {
      display: block;
    }

    .kb-card-reminder-nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-top: 5px;
      border-top: 1px dashed #dbeafe;
      padding-top: 5px;
    }

    .kb-card-reminder-nav button {
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 999px;
      width: 24px;
      height: 22px;
      display: inline-grid;
      place-items: center;
      cursor: pointer;
      font-size: 12px;
      line-height: 1;
    }

    .kb-card-reminder-nav button:hover {
      background: #eef7fb;
    }

    .kb-card-reminder-counter {
      font-size: 10px;
      font-weight: 900;
      color: #64748b;
    }

    .kb-card-summary-counts {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-wrap: wrap;
      margin-top: 3px;
    }

    .kb-card-summary-counts span {
      border-radius: 999px;
      padding: 2px 6px;
      font-size: 10px;
      font-weight: 900;
      background: #eef7fb;
      color: #334155;
    }

    .kb-card-summary-counts .is-reminder {
      background: #e7f5d0;
      color: #2f5c00;
    }

    .kb-card-summary-counts .is-activity {
      background: #dbeafe;
      color: #1e3a5f;
    }

    /* Custom activity modal */
    .kb-activity-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .45);
      z-index: 30000;
      opacity: 0;
      pointer-events: none;
      transition: opacity .18s ease;
    }

    .kb-activity-backdrop.is-open {
      opacity: 1;
      pointer-events: auto;
    }

    .kb-activity-modal {
      position: fixed;
      inset: 0;
      z-index: 30001;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      opacity: 0;
      pointer-events: none;
      transition: opacity .18s ease;
    }

    .kb-activity-modal.is-open {
      opacity: 1;
      pointer-events: auto;
    }

    .kb-activity-panel {
      width: min(1240px, 96vw);
      height: min(92vh, 900px);
      background: #fff;
      border: 1px solid #dbeafe;
      border-radius: 22px;
      box-shadow: 0 30px 90px rgba(15, 23, 42, .35);
      overflow: hidden;
      transform: translateY(14px) scale(.98);
      transition: transform .18s ease;
      display: flex;
      flex-direction: column;
    }

    .kb-activity-modal.is-open .kb-activity-panel {
      transform: translateY(0) scale(1);
    }

    .kb-activity-head {
      background: #74b2d4;
      color: #fff;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex: 0 0 auto;
    }

    .kb-activity-title {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 17px;
      font-weight: 900;
      line-height: 1.1;
    }

    .kb-activity-title i {
      font-size: 18px;
    }

    .kb-activity-subtitle {
      font-size: 12px;
      opacity: .94;
      margin-top: 4px;
      line-height: 1.35;
    }

    .kb-activity-close {
      width: 38px;
      height: 38px;
      border: 0;
      border-radius: 12px;
      background: rgba(255, 255, 255, .18);
      color: #fff;
      display: grid;
      place-items: center;
      cursor: pointer;
      transition: .15s ease;
    }

    .kb-activity-close:hover {
      background: rgba(255, 255, 255, .28);
      transform: translateY(-1px);
    }

    .kb-activity-body {
      padding: 16px;
      background: #f8fafc;
      overflow: hidden;
      flex: 1 1 auto;
      min-height: 0;
    }

    .kb-activity-grid {
      display: grid;
      grid-template-columns: 1.08fr .92fr;
      gap: 14px;
      align-items: stretch;
      height: 100%;
      min-height: 0;
    }

    @media(max-width:960px) {
      .kb-activity-modal {
        padding: 12px
      }

      .kb-activity-panel {
        height: 96vh
      }

      .kb-activity-grid {
        grid-template-columns: 1fr;
        overflow: auto
      }

      .kb-activity-body {
        overflow: auto
      }

      .kb-activity-box {
        min-height: 420px
      }

      .kb-nextstep-box {
        min-height: 650px
      }
    }

    .kb-activity-box {
      background: #fff;
      border: 1px solid #dbeafe;
      border-radius: 18px;
      padding: 14px;
      box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
      display: flex;
      flex-direction: column;
      min-height: 0;
      overflow: hidden;
    }

    .kb-activity-box-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      color: #0f172a;
      font-size: 13px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
      padding-bottom: 9px;
      border-bottom: 1px dashed #cfe09b;
      margin-bottom: 10px;
      flex: 0 0 auto;
    }

    .kb-activity-box-title span {
      display: flex;
      align-items: center;
      gap: 7px;
    }

    .kb-activity-count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 24px;
      height: 22px;
      padding: 0 7px;
      border-radius: 999px;
      background: #cfe09b;
      color: #0f172a;
      font-size: 11px;
      font-weight: 900;
    }

    .kb-modal-tools {
      display: grid;
      grid-template-columns: 1fr 155px;
      gap: 8px;
      margin-bottom: 10px;
      flex: 0 0 auto;
    }

    .kb-modal-tools input,
    .kb-modal-tools select {
      border: 1px solid #dbeafe;
      border-radius: 12px;
      min-height: 36px;
      padding: 7px 10px;
      font-size: 12px;
      font-weight: 800;
      background: #fff;
      color: #334155;
      outline: 0;
    }

    .kb-modal-tools input:focus,
    .kb-modal-tools select:focus {
      border-color: #74b2d4;
      box-shadow: 0 0 0 3px rgba(116, 178, 212, .18)
    }

    .kb-modal-tabbar {
      display: flex;
      gap: 6px;
      margin-bottom: 10px;
      flex: 0 0 auto;
      flex-wrap: wrap;
    }

    .kb-modal-tab {
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 999px;
      padding: 6px 10px;
      font-size: 11px;
      font-weight: 900;
      cursor: pointer;
    }

    .kb-modal-tab.is-active {
      background: #74b2d4;
      color: #fff;
      border-color: #74b2d4;
    }

    .kb-activity-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      overflow: auto;
      flex: 1 1 auto;
      min-height: 0;
      padding-right: 3px;
    }

    .kb-nextstep-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      overflow: auto;
      max-height: 245px;
      min-height: 110px;
      padding-right: 3px;
      flex: 0 0 auto;
    }

    .kb-activity-item {
      border-left: 4px solid #93c21c;
      background: #f8fafc;
      border-radius: 12px;
      padding: 9px 10px;
    }

    .kb-activity-item.is-reminder {
      border-left-color: #74b2d4;
    }

    .kb-activity-item.is-important {
      border-left-color: #f59e0b;
      background: #fffbeb;
    }

    .kb-activity-item.is-critical,
    .kb-activity-item.is-overdue {
      border-left-color: #dc2626;
      background: #fff7f7;
    }

    .kb-activity-top {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      font-size: 11px;
      font-weight: 900;
      color: #334155;
      line-height: 1.25;
    }

    .kb-activity-text {
      margin-top: 4px;
      font-size: 12px;
      color: #0f172a;
      line-height: 1.4;
      word-break: break-word;
    }

    .kb-activity-meta {
      margin-top: 5px;
      font-size: 11px;
      color: #64748b;
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      align-items: center;
    }

    .kb-empty-state {
      border: 1px dashed #cbd5e1;
      background: #f8fafc;
      color: #64748b;
      border-radius: 12px;
      padding: 12px;
      font-size: 12px;
      font-weight: 800;
      text-align: center;
    }

    .kb-nextstep-form {
      margin-top: 12px;
      border-top: 1px dashed #cfe09b;
      padding-top: 12px;
      background: #fff;
      position: sticky;
      bottom: 0;
      z-index: 3;
      box-shadow: 0 -8px 14px rgba(255, 255, 255, .92);
      flex: 1 1 auto;
      overflow: auto;
      min-height: 0;
      max-height: calc(100% - 300px);
      padding-right: 3px;
    }

    .kb-nextstep-form label {
      display: block;
      font-size: 11px;
      font-weight: 900;
      color: #334155;
      text-transform: uppercase;
      margin: 9px 0 4px;
      letter-spacing: .04em;
    }

    .kb-nextstep-form input,
    .kb-nextstep-form textarea,
    .kb-nextstep-form select {
      width: 100%;
      border: 1px solid #dbeafe;
      border-radius: 12px;
      min-height: 38px;
      padding: 8px 10px;
      font-size: 13px;
      font-weight: 700;
      background: #fff;
      color: #0f172a;
      outline: 0;
    }

    .kb-nextstep-form input:focus,
    .kb-nextstep-form textarea:focus,
    .kb-nextstep-form select:focus {
      border-color: #74b2d4;
      box-shadow: 0 0 0 3px rgba(116, 178, 212, .18);
    }

    .kb-nextstep-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    @media(max-width:640px) {
      .kb-nextstep-row {
        grid-template-columns: 1fr
      }

      .kb-modal-tools {
        grid-template-columns: 1fr
      }
    }

    .kb-nextstep-save {
      margin-top: 14px;
      width: 100%;
      min-height: 42px;
      border: 0;
      border-radius: 13px;
      background: #93c21c;
      color: #fff;
      font-size: 13px;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      cursor: pointer;
      transition: .15s ease;
    }

    .kb-nextstep-save:hover {
      background: #7aa817;
      transform: translateY(-1px);
    }

    .kb-nextstep-save:disabled {
      opacity: .65;
      cursor: not-allowed;
      transform: none;
    }

    .kanban-card-highlight-reminder {
      animation: reminderPulse 1.6s ease-in-out 4;
      box-shadow: 0 0 0 4px rgba(147, 194, 28, .35), 0 20px 45px rgba(15, 23, 42, .18) !important;
    }

    @keyframes reminderPulse {
      0% {
        transform: scale(1)
      }

      50% {
        transform: scale(1.025)
      }

      100% {
        transform: scale(1)
      }
    }

    .lead-reminder-toast-wrap {
      position: fixed;
      right: 22px;
      bottom: 22px;
      z-index: 200000;
      display: flex;
      flex-direction: column;
      gap: 10px;
      width: 380px;
      max-width: calc(100vw - 30px);
    }

    .lead-reminder-toast {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 18px 45px rgba(15, 23, 42, .25);
      border-left: 6px solid #93c21c;
      overflow: hidden;
      cursor: pointer;
      animation: toastIn .25s ease;
    }

    .lead-reminder-toast.error,
    .lead-reminder-toast.critical {
      border-left-color: #dc2626;
    }

    .lead-reminder-toast.important {
      border-left-color: #f59e0b;
    }

    .lead-reminder-toast.success {
      border-left-color: #93c21c;
    }

    .lead-reminder-toast-head {
      padding: 12px 14px;
      background: #f8fafc;
      font-weight: 900;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      color: #0f172a;
    }

    .lead-reminder-toast-body {
      padding: 12px 14px;
      font-size: 13px;
      color: #334155;
      line-height: 1.45;
    }

    .lead-reminder-toast-actions {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      padding: 0 14px 12px;
    }

    .lead-reminder-toast-btn {
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 999px;
      padding: 5px 9px;
      font-size: 11px;
      font-weight: 900;
      cursor: pointer;
    }

    .lead-reminder-toast-btn:hover {
      background: #eef7fb;
    }

    .lead-reminder-toast-close {
      border: 0;
      background: transparent;
      font-size: 18px;
      line-height: 1;
      color: #64748b;
      cursor: pointer;
    }

    @keyframes toastIn {
      from {
        transform: translateY(15px);
        opacity: 0
      }

      to {
        transform: translateY(0);
        opacity: 1
      }
    }

    .kanban-zoom-card.kb-compact .kb-reminder-summary {
      padding: 6px 7px;
      border-radius: 11px;
    }

    .kanban-zoom-card.kb-compact .kb-reminder-title,
    .kanban-zoom-card.kb-compact .kb-reminder-body {
      font-size: 10px;
    }
  </style>

  <style>
    /* ===== Stage/Sub-stage configuration toolbar ===== */
    .kb-stage-config-toolbar {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      padding: 6px 8px;
      border: 1px solid #dbeafe;
      background: #fff;
      border-radius: 14px;
    }

    .kb-stage-config-label {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .kb-stage-config-btn,
    .lsm-substage-btn {
      border: 1px solid #93c21c;
      background: #93c21c;
      color: #fff !important;
      border-radius: 999px !important;
      min-height: 32px;
      padding: 6px 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 900;
      text-decoration: none !important;
      cursor: pointer;
      white-space: nowrap;
      transition: .15s ease;
    }

    .kb-stage-config-btn:hover,
    .lsm-substage-btn:hover {
      background: #7faa18;
      border-color: #7faa18;
      color: #fff !important;
      transform: translateY(-1px);
      text-decoration: none !important;
    }

    .kb-stage-config-hint {
      font-size: 11px;
      font-weight: 800;
      color: #64748b;
    }

    .lsm-substage-btn {
      min-height: 30px;
      padding: 5px 9px;
      font-size: 11px;
    }

    .lsm-stage-row .lsm-actions {
      gap: 6px;
    }

    /* ===== Per-column sub-stage configuration button ===== */
    .kb-column-head-left {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      min-width: 0;
      flex: 1 1 auto;
      overflow: hidden;
    }

    .kb-column-substage-btn {
      width: 28px;
      height: 28px;
      border-radius: 10px !important;
      border: 1px solid rgba(255, 255, 255, .35);
      background: rgba(255, 255, 255, .18);
      color: #fff !important;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none !important;
      flex: 0 0 auto;
      transition: .15s ease;
    }

    .kb-column-substage-btn:hover {
      background: rgba(255, 255, 255, .30);
      border-color: rgba(255, 255, 255, .55);
      color: #fff !important;
      transform: translateY(-1px);
      text-decoration: none !important;
    }

    .kb-column-substage-btn .feather {
      width: 15px;
      height: 15px;
    }

    .kb-column-substage-count {
      position: absolute;
      top: -5px;
      right: -5px;
      min-width: 15px;
      height: 15px;
      padding: 0 4px;
      border-radius: 999px;
      background: #fff;
      color: #0f172a;
      font-size: 9px;
      font-weight: 900;
      line-height: 15px;
      text-align: center;
      box-shadow: 0 1px 4px rgba(15, 23, 42, .18);
    }

    .kb-column-substage-wrap {
      position: relative;
      display: inline-flex;
      flex: 0 0 auto;
    }
  </style>



  <style>
    /* ===== Compact Kanban cards + visible sub-stage controls ===== */
    .column {
      width: 315px;
      min-width: 315px;
      flex-basis: 315px;
    }

    .column-content {
      padding: 7px;
    }

    .column h3 {
      min-height: 38px;
      padding: 6px 7px;
      font-size: 13px;
    }

    .column-toolbar {
      padding: 5px 6px;
    }

    .col-search-input {
      height: 26px;
      font-size: 11px;
    }

    .col-sort-btn {
      height: 26px;
      width: 30px;
    }

    .kb-column-substage-wrap {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      margin-left: 4px;
    }

    .kb-column-substage-btn {
      width: 25px;
      height: 25px;
      border-radius: 9px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, .18);
      color: #fff;
      text-decoration: none;
    }

    .kb-column-substage-btn:hover {
      background: rgba(255, 255, 255, .30);
      color: #fff;
      text-decoration: none;
    }

    .kb-column-substage-count {
      min-width: 18px;
      height: 18px;
      padding: 0 5px;
      border-radius: 999px;
      background: rgba(15, 23, 42, .22);
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: 900;
    }

    .card.kb-card-compact {
      padding: 7px 7px 6px;
      margin: 6px 0;
      border-radius: 10px;
      border-left-width: 4px;
      gap: 5px;
      cursor: grab;
      box-shadow: 0 1px 4px rgba(15, 23, 42, .12);
    }

    .kb-card-compact .card-status-overlay {
      display: none !important;
    }

    .kb-card-topline {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 7px;
      min-height: 24px;
      padding-right: 26px;
    }

    .kb-card-customer {
      min-width: 0;
      max-width: 215px;
      font-size: 13px;
      line-height: 1.15;
      font-weight: 900;
      color: #0f172a;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .kb-card-date {
      margin-top: 2px;
      font-size: 10px;
      line-height: 1;
      font-weight: 800;
      color: #64748b;
      white-space: nowrap;
    }

    .kb-compact-product {
      position: absolute;
      top: 27px;
      right: 7px;
      min-width: 31px;
      height: 20px;
      padding: 0 6px;
      border-radius: 7px;
      background: #dbeafe;
      color: #0f172a;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 9px;
      font-weight: 900;
      letter-spacing: .03em;
      box-shadow: none;
    }

    .kb-compact-address {
      margin-top: -1px;
      font-size: 10px;
      line-height: 1.25;
      color: #64748b;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      padding-right: 38px;
    }

    .kb-compact-substage {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 2px;
    }

    .kb-compact-substage-label {
      font-size: 10px;
      font-weight: 900;
      color: #475569;
      white-space: nowrap;
    }

    .kb-substage-select {
      width: 100%;
      min-width: 0;
      height: 26px;
      border: 1px solid #dbeafe;
      border-radius: 9px;
      background: #fff;
      color: #0f172a;
      font-size: 11px;
      font-weight: 800;
      padding: 0 7px;
      outline: none;
    }

    .kb-substage-select:focus {
      border-color: #93c21c;
      box-shadow: 0 0 0 2px rgba(147, 194, 28, .16);
    }

    .kb-substage-select[disabled] {
      opacity: .75;
      background: #f8fafc;
    }

    .kb-card-compact .kb-menu--card {
      position: absolute;
      top: 4px;
      right: 4px;
      z-index: 20;
    }

    .kb-card-compact .kb-menu-toggle {
      width: 25px;
      height: 25px;
      display: grid;
      place-items: center;
      padding: 0;
      border-radius: 9px;
      background: #f8fafc;
      color: #334155;
    }

    .kb-card-compact .kb-menu-toggle:hover {
      background: #eef2f7;
    }

    .kb-card-compact .kb-menu-dropdown {
      top: 28px;
      right: 0;
      left: auto;
      min-width: 170px;
      z-index: 2000;
    }

    .kb-card-compact .kb-menu-item {
      font-size: 12px;
      padding: 7px 9px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .kb-card-compact .kb-menu-item .feather {
      width: 14px;
      height: 14px;
    }

    .kb-card-compact .badge-notes {
      transform: scale(.85);
      transform-origin: top right;
    }

    .kb-card-compact .kb-hidden-card-section,
    .kb-card-compact .kb-offer-workflow,
    .kb-card-compact .kb-reminder-summary,
    .kb-card-compact .kb-stage-time,
    .kb-card-compact .live-feed-bar {
      display: none !important;
    }
  </style>

  <style>
    /* ===== Kanban LeadStage/SubStage Admin: injected full manager ===== */
    .kb-stage-admin-open {
      border: 1px solid #93c21c !important;
      background: #93c21c !important;
      color: #fff !important;
    }

    .kb-stage-admin-open:hover {
      background: #7faa18 !important;
      border-color: #7faa18 !important;
      color: #fff !important;
    }

    .kbsa-backdrop {
      position: fixed;
      inset: 0;
      z-index: 200000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 18px;
      background: rgba(15, 23, 42, .58);
    }

    .kbsa-backdrop.is-open {
      display: flex;
    }

    .kbsa-modal {
      width: min(1180px, 96vw);
      max-height: calc(100vh - 36px);
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 24px;
      box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .kbsa-head,
    .kbsa-foot {
      padding: 15px 18px;
      background: #fafafa;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .kbsa-foot {
      border-top: 1px solid #e5e7eb;
      border-bottom: 0;
      justify-content: flex-end;
    }

    .kbsa-title {
      margin: 0;
      font-size: 17px;
      font-weight: 900;
      color: #111827;
      text-transform: uppercase;
    }

    .kbsa-sub {
      margin-top: 4px;
      font-size: 12px;
      color: #64748b;
      font-weight: 700;
    }

    .kbsa-close {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
      background: #fff;
      color: #64748b;
      cursor: pointer;
      font-size: 22px;
      line-height: 1;
    }

    .kbsa-body {
      padding: 16px;
      overflow: auto;
    }

    .kbsa-toolbar {
      display: grid;
      grid-template-columns: minmax(180px, 1fr) 150px 120px 120px auto auto;
      gap: 10px;
      align-items: end;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 12px;
      background: #f8fafc;
      margin-bottom: 14px;
    }

    .kbsa-label {
      display: block;
      margin-bottom: 6px;
      font-size: 11px;
      font-weight: 900;
      color: #64748b;
      text-transform: uppercase;
    }

    .kbsa-input {
      width: 100%;
      min-height: 38px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 8px 10px;
      color: #111827;
      background: #fff;
      outline: none;
    }

    .kbsa-input:focus {
      border-color: #93c21c;
      box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
    }

    .kbsa-check {
      min-height: 38px;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 8px 10px;
      font-size: 12px;
      font-weight: 900;
      background: #fff;
      margin: 0;
    }

    .kbsa-btn,
    .kbsa-btn-soft,
    .kbsa-btn-danger {
      min-height: 38px;
      border: 0;
      border-radius: 12px;
      padding: 8px 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      font-size: 12px;
      font-weight: 900;
      cursor: pointer;
      white-space: nowrap;
    }

    .kbsa-btn {
      background: #93c21c;
      color: #fff;
    }

    .kbsa-btn-soft {
      background: #fff;
      color: #334155;
      border: 1px solid #e5e7eb;
    }

    .kbsa-btn-danger {
      background: #ef4444;
      color: #fff;
    }

    .kbsa-error {
      display: none;
      white-space: pre-line;
      border: 1px solid #fecaca;
      background: #fef2f2;
      color: #991b1b;
      border-radius: 14px;
      padding: 10px 12px;
      font-size: 12px;
      font-weight: 800;
      margin-bottom: 12px;
    }

    .kbsa-error.is-visible {
      display: block;
    }

    .kbsa-stage {
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      overflow: hidden;
      margin-bottom: 12px;
      background: #fff;
    }

    .kbsa-stage-head {
      display: grid;
      grid-template-columns: 34px minmax(0, 1.2fr) minmax(0, .85fr) 90px 90px 90px 94px auto;
      gap: 8px;
      align-items: center;
      padding: 10px;
      background: #f9fafb;
      border-bottom: 1px solid #eef2f7;
    }

    .kbsa-sub-list {
      padding: 10px;
    }

    .kbsa-sub {
      display: grid;
      grid-template-columns: 34px minmax(0, 1.3fr) minmax(0, .8fr) 90px 86px 86px auto;
      gap: 8px;
      align-items: center;
      padding: 8px;
      border: 1px solid #eef2f7;
      border-radius: 12px;
      margin-bottom: 8px;
      background: #fcfcfd;
    }

    .kbsa-handle {
      cursor: grab;
      color: #64748b;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 38px;
    }

    .kbsa-small {
      font-size: 11px;
      font-weight: 900;
      color: #64748b;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .kbsa-usage {
      font-size: 11px;
      font-weight: 900;
      color: #334155;
      background: #f1f5f9;
      border-radius: 999px;
      padding: 5px 8px;
      text-align: center;
    }

    @media (max-width: 1100px) {

      .kbsa-toolbar,
      .kbsa-stage-head,
      .kbsa-sub {
        grid-template-columns: 1fr;
      }

      .kbsa-handle {
        justify-content: flex-start;
      }
    }


    /* ===== Underphase Sidebar: selected Hauptphase opens here instead of replacing the Kanban ===== */
    .kb-understage-sidebar-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .32);
      opacity: 0;
      pointer-events: none;
      transition: opacity .2s ease;
      z-index: 1098;
    }

    .kb-understage-sidebar-backdrop.show {
      opacity: 1;
      pointer-events: auto;
    }

    .kb-understage-sidebar {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      width: min(1180px, 96vw);
      background: #ffffff;
      transform: translateX(100%);
      transition: transform .24s ease;
      z-index: 1099;
      display: flex;
      flex-direction: column;
      box-shadow: -18px 0 42px rgba(15, 23, 42, .20);
      border-left: 1px solid #e5e7eb;
    }

    .kb-understage-sidebar.open {
      transform: translateX(0);
    }

    .kb-understage-sidebar-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 14px 16px;
      border-bottom: 1px solid #e5e7eb;
      background: linear-gradient(135deg, #f8fafc 0%, #eef7fb 100%);
      flex: 0 0 auto;
    }

    .kb-understage-sidebar-title {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
      font-size: 15px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .02em;
    }

    .kb-understage-sidebar-title i {
      width: 18px;
      height: 18px;
      color: #74b2d4;
      flex: 0 0 auto;
    }

    .kb-understage-sidebar-subtitle {
      margin-top: 3px;
      font-size: 12px;
      color: #64748b;
      font-weight: 700;
    }

    .kb-understage-sidebar-actions {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      flex: 0 0 auto;
    }

    .kb-understage-close,
    .kb-understage-refresh {
      border: 1px solid #dbeafe;
      background: #fff;
      color: #334155;
      border-radius: 12px;
      min-height: 36px;
      padding: 0 12px;
      font-size: 12px;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      cursor: pointer;
    }

    .kb-understage-close:hover,
    .kb-understage-refresh:hover {
      background: #eef7fb;
      color: #0f172a;
    }

    .kb-understage-sidebar-body {
      flex: 1 1 auto;
      min-height: 0;
      overflow: hidden;
      background: #f8fafc;
      padding: 10px;
    }

    .kb-understage-board {
      height: 100%;
      display: flex;
      gap: 10px;
      overflow: auto;
      padding-bottom: 10px;
      align-items: stretch;
    }

    .kb-understage-board .column {
      height: calc(100vh - 100px);
      min-height: 620px;
    }

    .kb-understage-board .column-content.drag-over {
      outline: 3px dashed #74b2d4;
      outline-offset: -5px;
      background: #eef7fb;
    }

    .kb-understage-sidebar-empty {
      padding: 18px;
      border: 1px dashed #cbd5e1;
      border-radius: 16px;
      background: #ffffff;
      color: #64748b;
      font-size: 13px;
      font-weight: 800;
      min-width: 320px;
    }

    @media (max-width: 768px) {
      .kb-understage-sidebar {
        width: 100vw;
      }

      .kb-understage-sidebar-head {
        align-items: flex-start;
        flex-direction: column;
      }

      .kb-understage-sidebar-actions {
        width: 100%;
      }

      .kb-understage-close,
      .kb-understage-refresh {
        flex: 1 1 auto;
      }
    }
  </style>

  <style>
    /* ===== Unified Phase/SubStage Configuration Drawer (Kanban + Phase config) ===== */
    .lsm-panel {
      position: relative;
    }

    .lsm-substage-drawer {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      width: min(560px, 94vw);
      background: #ffffff;
      border-left: 1px solid #e5e7eb;
      box-shadow: -18px 0 45px rgba(15, 23, 42, .18);
      transform: translateX(105%);
      transition: transform .22s ease;
      z-index: 6;
      display: flex;
      flex-direction: column;
    }

    .lsm-substage-drawer.is-open {
      transform: translateX(0);
    }

    .lsm-substage-head {
      padding: 15px 16px;
      border-bottom: 1px solid #e5e7eb;
      background: linear-gradient(135deg, rgba(116, 178, 212, .16), rgba(147, 194, 28, .13)), #fff;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
    }

    .lsm-substage-title {
      margin: 0;
      font-size: 16px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      line-height: 1.25;
    }

    .lsm-substage-subtitle {
      margin-top: 4px;
      font-size: 12px;
      color: #64748b;
      font-weight: 700;
      line-height: 1.35;
    }

    .lsm-substage-body {
      padding: 14px;
      overflow: auto;
      flex: 1;
      background: #f8fafc;
    }

    .lsm-substage-create,
    .lsm-substage-list-card {
      border: 1px solid #e5e7eb;
      background: #fff;
      border-radius: 16px;
      padding: 12px;
      margin-bottom: 12px;
      box-shadow: 0 4px 16px rgba(15, 23, 42, .05);
    }

    .lsm-substage-create-title,
    .lsm-substage-list-title {
      font-size: 12px;
      font-weight: 900;
      color: #334155;
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-bottom: 10px;
    }

    .lsm-substage-form-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 120px 64px 84px;
      gap: 8px;
      align-items: end;
    }

    .lsm-substage-form-grid .form-control {
      min-height: 38px;
    }

    .lsm-substage-row {
      display: grid;
      grid-template-columns: 30px minmax(0, 1fr) 110px 60px 80px 70px;
      gap: 7px;
      align-items: center;
      border: 1px solid #edf2f7;
      border-radius: 13px;
      padding: 8px;
      background: #fff;
      margin-bottom: 8px;
    }

    .lsm-substage-row.is-dragging {
      opacity: .55;
    }

    .lsm-substage-row.is-drop-target {
      outline: 2px dashed #74b2d4;
      outline-offset: 2px;
    }

    .lsm-substage-handle {
      cursor: grab;
      color: #64748b;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 34px;
    }

    .lsm-substage-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 5px;
    }

    .lsm-mini-btn {
      border: 1px solid #e5e7eb;
      background: #fff;
      color: #334155;
      border-radius: 10px;
      height: 34px;
      min-width: 34px;
      padding: 0 9px;
      font-size: 12px;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      cursor: pointer;
    }

    .lsm-mini-btn.primary {
      background: #93c21c;
      border-color: #93c21c;
      color: #fff;
    }

    .lsm-mini-btn.danger {
      background: #ef4444;
      border-color: #ef4444;
      color: #fff;
    }

    .lsm-mini-btn.blue {
      background: #74b2d4;
      border-color: #74b2d4;
      color: #fff;
    }

    .lsm-substage-empty {
      border: 1px dashed #cbd5e1;
      background: #f8fafc;
      color: #64748b;
      border-radius: 14px;
      padding: 16px;
      font-size: 12px;
      font-weight: 800;
      text-align: center;
    }

    .lsm-substage-open-note {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border-radius: 999px;
      background: #eef7fb;
      color: #075985;
      font-size: 11px;
      font-weight: 900;
      padding: 4px 8px;
      margin-top: 7px;
    }

    @media (max-width: 1200px) {

      .lsm-substage-form-grid,
      .lsm-substage-row {
        grid-template-columns: 1fr;
      }

      .lsm-substage-actions {
        justify-content: flex-start;
      }

      .lsm-substage-drawer {
        width: min(520px, 94vw);
      }
    }
  </style>


  <style>
    /* ===== Boss patch: single Phase Management entry + compact analytics/filter ===== */
    #btnOpenKanbanStageAdminTop,
    #btnOpenKanbanStageAdminMain,
    #kanbanStageAdminModal {
      display: none !important;
    }

    #kbStageConfigToolbar {
      width: 100%;
      justify-content: flex-start;
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border-color: #dbeafe;
      box-shadow: 0 6px 18px rgba(15, 23, 42, .05);
    }

    #kbStageConfigToolbar .kb-stage-config-btn,
    #btnOpenStageManager,
    #btnOpenStageManagerTop {
      background: #93c21c;
      border-color: #93c21c;
      color: #fff !important;
    }

    #kbStageConfigToolbar .kb-stage-config-hint {
      margin-left: auto;
      color: #64748b;
    }


    .lsm-substage-count-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 22px;
      height: 22px;
      padding: 0 7px;
      border-radius: 999px;
      background: #eef7fb;
      color: #075985;
      font-size: 11px;
      font-weight: 1000;
      border: 1px solid #dbeafe;
      margin-left: 5px;
    }

    .lsm-stage-subcount-line {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-top: 3px;
      font-size: 11px;
      color: #64748b;
      font-weight: 800;
    }

    .lsm-substage-btn .lsm-substage-count-pill {
      height: 18px;
      min-width: 18px;
      padding: 0 5px;
      font-size: 10px;
      background: rgba(255, 255, 255, .96);
      color: #334155;
      border-color: rgba(255, 255, 255, .55);
    }

    #view-filter {
      border-left: 4px solid #93c21c;
    }

    #kanbanFilterForm .select2-container--default .select2-selection--single,
    #kanbanFilterForm .form-control,
    #kanbanFilterForm .custom-select {
      border-radius: 12px !important;
      border-color: #dbeafe !important;
      min-height: 38px;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }

    #kanbanFilterForm label {
      color: #334155;
      display: flex;
      align-items: center;
      gap: 5px;
    }


    #summaryStats {
      display: grid !important;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      grid-auto-rows: minmax(62px, auto);
      gap: 8px;
      justify-content: stretch !important;
    }

    #summaryStats>.summary-card {
      flex: none !important;
      max-width: none !important;
      width: auto !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    #summaryStats .summary-card>div {
      min-height: 66px;
      padding: 8px 9px !important;
      border-radius: 14px !important;
      box-shadow: 0 5px 14px rgba(15, 23, 42, .06);
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 3px;
    }

    #summaryStats .summary-card strong {
      font-size: 11px;
      line-height: 1.2;
      text-transform: uppercase;
      letter-spacing: .04em;
      font-weight: 900;
    }

    #summaryStats .summary-card .h4 {
      font-size: 20px;
      line-height: 1.05;
      margin: 0;
      font-weight: 900;
    }

    #summaryStats #cardOffen {
      grid-column: auto;
    }

    #view-summary {
      margin-bottom: 10px !important;
    }

    #view-filter {
      border: 1px solid #e5e7eb;
      border-radius: 18px;
      background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
      padding: 13px;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, .85);
    }

    #kanbanFilterForm {
      row-gap: 10px;
    }

    #kanbanFilterForm>[class*="col-"] {
      margin-bottom: 0 !important;
    }

    #kanbanFilterForm label,
    #kanbanFilterForm .form-label {
      margin-bottom: 4px;
      font-size: 11px;
      font-weight: 900;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    #kanbanFilterForm .form-control,
    #kanbanFilterForm .custom-select,
    #kanbanFilterForm .select2-selection--single {
      min-height: 38px;
      border-radius: 12px !important;
      border-color: #dbe3ec !important;
      background: #fff;
    }

    #activeFilterChips {
      min-height: 0;
    }

    #leadStageManagerModal .lsm-panel {
      width: min(1180px, 96vw);
      max-width: 1180px;
    }

    #leadStageManagerModal .lsm-grid {
      grid-template-columns: 360px minmax(0, 1fr);
      align-items: stretch;
    }

    #leadStageManagerModal .lsm-substage-drawer {
      position: absolute;
      top: 72px;
      right: 16px;
      bottom: 16px;
      width: 953px;
      transform: translateX(calc(100% + 24px));
      z-index: 4;
      border-radius: 18px;
      border: 1px solid #dbeafe;
      box-shadow: -18px 0 45px rgba(15, 23, 42, .18);
    }

    #leadStageManagerModal .lsm-substage-drawer.is-open {
      transform: translateX(0);
    }

    #leadStageManagerModal .lsm-substage-head {
      background: linear-gradient(135deg, rgba(116, 178, 212, .18), rgba(147, 194, 28, .10)), #fff;
    }

    #leadStageManagerModal .lsm-substage-create,
    #leadStageManagerModal .lsm-substage-list-card {
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
    }

    #leadStageManagerModal .lsm-substage-row {
      border-radius: 14px;
      background: #fff;
    }

    @media (max-width: 1200px) {
      #summaryStats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }

      #leadStageManagerModal .lsm-grid {
        grid-template-columns: 1fr;
      }

      #leadStageManagerModal .lsm-substage-drawer {
        width: min(620px, 94vw);
        top: 64px;
        right: 10px;
        bottom: 10px;
      }
    }

    @media (max-width: 768px) {
      #summaryStats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      #kbStageConfigToolbar .kb-stage-config-hint {
        width: 100%;
        margin-left: 0;
      }
    }
  </style>
@endsection


<style>
  /* ===== Under Stage Board Fix ===== */
  .kb-understage-btn,
  .kb-column-substage-btn {
    min-height: 24px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, .35);
    background: rgba(255, 255, 255, .16);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 3px 7px;
    font-size: 10px;
    font-weight: 900;
    line-height: 1;
    text-decoration: none;
    white-space: nowrap;
  }

  .kb-understage-btn:hover,
  .kb-column-substage-btn:hover {
    background: rgba(255, 255, 255, .28);
    color: #fff;
    text-decoration: none;
  }

  .kb-understage-btn b,
  .kb-column-substage-count {
    min-width: 18px;
    height: 18px;
    border-radius: 999px;
    background: rgba(15, 23, 42, .22);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 10px;
    font-weight: 1000;
    padding: 0 5px;
  }

  .kb-column-substage-wrap {
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .kb-column-head-left {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    overflow: hidden;
  }

  .kb-column-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    flex-wrap: wrap;
    width: 100%;
  }

  .kb-understage-btn {
    height: 28px;
    border: 1px solid rgba(255, 255, 255, .35);
    background: rgba(255, 255, 255, .16);
    color: #fff;
    border-radius: 999px;
    padding: 0 7px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 900;
    cursor: pointer;
    white-space: nowrap;
  }

  .kb-understage-btn:hover {
    background: rgba(255, 255, 255, .28);
  }

  .kb-understage-btn .feather {
    width: 13px;
    height: 13px;
  }

  .kb-understage-btn b {
    min-width: 16px;
    height: 16px;
    border-radius: 999px;
    background: #fff;
    color: #334155;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
  }

  .kb-column-substage-wrap {
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .kb-column-substage-btn {
    width: 28px;
    height: 28px;
    border: 1px solid rgba(255, 255, 255, .35);
    background: rgba(255, 255, 255, .16);
    color: #fff;
    border-radius: 999px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }

  .kb-column-substage-btn:hover {
    background: rgba(255, 255, 255, .28);
    color: #fff;
    text-decoration: none;
  }

  .kb-column-substage-count {
    min-width: 16px;
    height: 16px;
    border-radius: 999px;
    background: #fff;
    color: #334155;
    font-size: 9px;
    font-weight: 900;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .kb-understage-loader {
    position: fixed;
    inset: 0;
    z-index: 200000;
    background: rgba(15, 23, 42, .38);
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 12px;
    color: #fff;
    font-weight: 900;
  }

  .kb-understage-loader.show {
    display: flex;
  }

  .kb-understage-spinner {
    width: 44px;
    height: 44px;
    border-radius: 999px;
    border: 4px solid rgba(255, 255, 255, .35);
    border-top-color: #fff;
    animation: kbSpin .75s linear infinite;
  }

  @keyframes kbSpin {
    to {
      transform: rotate(360deg);
    }
  }

  .kb-understage-back-column .column-content {
    padding: 12px;
  }

  .kb-understage-back-btn {
    width: 100%;
    border: 1px solid #dbeafe;
    background: #fff;
    color: #334155;
    border-radius: 12px;
    padding: 9px 10px;
    font-weight: 900;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .kb-understage-info {
    margin-top: 10px;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 10px;
    background: #f8fafc;
    font-size: 12px;
    color: #475569;
  }

  .kb-understage-empty {
    border: 1px dashed #cbd5e1;
    background: #fff;
    border-radius: 12px;
    padding: 12px;
    color: #64748b;
    font-size: 12px;
    font-weight: 800;
    text-align: center;
  }

  [data-understage-dropzone].drag-over {
    outline: 2px dashed #93c21c;
    outline-offset: -4px;
    background: #f7fbef;
  }

  .kb-understage-chip {
    margin-top: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: 1px solid #dbeafe;
    background: #f8fafc;
    color: #334155;
    border-radius: 999px;
    padding: 3px 7px;
    font-size: 10px;
    font-weight: 900;
  }

  .kb-understage-chip .feather {
    width: 12px;
    height: 12px;
  }


  /* ===== FINAL FIX: responsive Kanban column headers =====
       Keeps title, Unterphasen, settings and count badges visible in every column. */
  .column h3 {
    display: grid !important;
    grid-template-rows: auto auto !important;
    align-items: stretch !important;
    justify-content: stretch !important;
    gap: 0 !important;
    min-height: 88px !important;
    height: auto !important;
    padding: 0 !important;
    overflow: visible !important;
    white-space: normal !important;
  }

  .column h3 .kb-column-head-left {
    display: flex !important;
    width: 100% !important;
    min-width: 0 !important;
    flex: 0 0 auto !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    padding: 8px 8px 6px !important;
    overflow: visible !important;
    border-bottom: 1px solid rgba(255, 255, 255, .22) !important;
  }

  .column h3 .kb-column-title {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    text-align: center !important;
    font-size: 14px !important;
    line-height: 1.18 !important;
    letter-spacing: .03em !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  .column h3 .kb-column-title .feather,
  .column h3 .kb-column-title svg {
    width: 15px !important;
    height: 15px !important;
    flex: 0 0 auto !important;
  }

  .column h3 .kb-column-actions {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto auto !important;
    width: 100% !important;
    min-width: 0 !important;
    align-items: center !important;
    justify-items: center !important;
    gap: 4px !important;
    padding: 5px 6px 7px !important;
    background: rgba(15, 23, 42, .10) !important;
    overflow: visible !important;
  }

  .column h3 .kb-understage-btn {
    width: 100% !important;
    max-width: 132px !important;
    min-width: 0 !important;
    height: 26px !important;
    min-height: 26px !important;
    padding: 0 7px !important;
    border-radius: 999px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    overflow: hidden !important;
    white-space: nowrap !important;
  }

  .column h3 .kb-understage-btn span {
    min-width: 0 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  .column h3 .kb-understage-btn .feather,
  .column h3 .kb-understage-btn svg {
    width: 13px !important;
    height: 13px !important;
    flex: 0 0 auto !important;
  }

  .column h3 .kb-understage-btn b {
    min-width: 18px !important;
    height: 18px !important;
    padding: 0 5px !important;
    border-radius: 999px !important;
    background: #fff !important;
    color: #334155 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 9px !important;
    font-weight: 1000 !important;
    flex: 0 0 auto !important;
  }

  .column h3 .kb-column-substage-wrap {
    position: static !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 3px !important;
    margin: 0 !important;
    min-width: 0 !important;
    flex: 0 0 auto !important;
  }

  .column h3 .kb-column-substage-btn {
    width: 26px !important;
    height: 26px !important;
    min-width: 26px !important;
    min-height: 26px !important;
    padding: 0 !important;
    border-radius: 999px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 auto !important;
  }

  .column h3 .kb-column-substage-btn .feather,
  .column h3 .kb-column-substage-btn svg {
    width: 13px !important;
    height: 13px !important;
  }

  .column h3 .kb-column-substage-count {
    position: static !important;
    min-width: 18px !important;
    height: 18px !important;
    padding: 0 5px !important;
    border-radius: 999px !important;
    background: #fff !important;
    color: #334155 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 9px !important;
    font-weight: 1000 !important;
    line-height: 1 !important;
    box-shadow: none !important;
    flex: 0 0 auto !important;
  }

  .column h3 .kb-header-counts {
    grid-column: 1 / -1 !important;
    width: 100% !important;
    min-width: 0 !important;
    margin: 2px 0 0 !important;
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    align-items: center !important;
    justify-content: stretch !important;
    gap: 3px !important;
    overflow: visible !important;
    white-space: nowrap !important;
  }

  .column h3 .kb-count-pill {
    width: 100% !important;
    min-width: 0 !important;
    height: 19px !important;
    padding: 0 3px !important;
    border-radius: 999px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 2px !important;
    font-size: 9px !important;
    line-height: 1 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
  }

  .column h3 .kb-count-dot {
    width: 6px !important;
    height: 6px !important;
    flex: 0 0 auto !important;
  }

  .kanban-zoom-card.kb-compact .column h3 {
    min-height: 37px !important;
    padding: 0 !important;
  }

  .kanban-zoom-card.kb-compact .column h3 .kb-column-title {
    font-size: 12px !important;
  }

  .kanban-zoom-card.kb-compact .column h3 .kb-understage-btn {
    max-width: 118px !important;
    height: 24px !important;
    min-height: 24px !important;
    font-size: 9px !important;
    padding: 0 6px !important;
  }

  .kanban-zoom-card.kb-compact .column h3 .kb-column-substage-btn {
    width: 24px !important;
    height: 24px !important;
    min-width: 24px !important;
    min-height: 24px !important;
  }

  .kanban-zoom-card.kb-compact .column h3 .kb-header-counts {
    gap: 2px !important;
    margin-top: 2px !important;
  }

  .kanban-zoom-card.kb-compact .column h3 .kb-count-pill {
    height: 17px !important;
    font-size: 8px !important;
    padding: 0 2px !important;
  }

  @media (max-width: 768px) {
    .column h3 .kb-column-actions {
      grid-template-columns: minmax(0, 1fr) auto auto !important;
    }

    .column h3 .kb-understage-btn {
      max-width: 112px !important;
      font-size: 9px !important;
    }
  }
</style>
<style>
    /* ===== Boss final: column badges default OFF + clearer Unterphasen + next-step overdue ===== */
    .column.kb-analytics-hidden .kb-header-counts,
    .column.kb-analytics-hidden .kb-column-substage-wrap {
      display: none !important;
    }

    .column.kb-analytics-hidden {
      width: 318px !important;
      min-width: 318px !important;
      flex-basis: 318px !important;
    }

    .kb-toggle-analytics {
      width: 26px;
      height: 26px;
      border: 1px solid rgba(255,255,255,.35);
      background: rgba(255,255,255,.16);
      color: #fff;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
      flex: 0 0 auto;
    }

    .kb-toggle-analytics:hover,
    .kb-toggle-analytics.is-active {
      background: rgba(255,255,255,.30);
      color: #fff;
    }

    .kb-understage-btn {
      max-width: none !important;
      min-width: 112px !important;
    }

    .kb-understage-btn span {
      display: inline-flex !important;
    }

    .kb-understage-btn b {
      background: #fff !important;
      color: #334155 !important;
      font-size: 10px !important;
      min-width: 19px !important;
      height: 19px !important;
    }

    .kb-next-step-preview.is-loading {
      opacity: .78;
    }

    .kb-next-step-preview.is-overdue,
    .card.kb-task-overdue-card .kb-next-step-preview {
      border-color: #dc2626 !important;
      background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%) !important;
    }

    .card.kb-task-overdue-card {
      animation: kbTaskCardOverduePulse 1.25s ease-in-out infinite;
      border-left-color: #dc2626 !important;
    }

    @keyframes kbTaskCardOverduePulse {
      0% { box-shadow: 0 2px 4px rgba(0,0,0,.20), 0 0 0 0 rgba(220,38,38,.34); }
      70% { box-shadow: 0 2px 4px rgba(0,0,0,.20), 0 0 0 8px rgba(220,38,38,0); }
      100% { box-shadow: 0 2px 4px rgba(0,0,0,.20), 0 0 0 0 rgba(220,38,38,0); }
    }

    .kb-list-next-step-box {
      border: 1px solid #dbeafe;
      border-radius: 14px;
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      padding: 8px 9px;
      min-width: 220px;
      max-width: 320px;
      font-size: 11px;
      color: #334155;
    }

    .kb-list-next-step-box.is-overdue {
      border-color: #dc2626;
      background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
      animation: kbTaskListOverduePulse 1.4s ease-in-out infinite;
    }

    @keyframes kbTaskListOverduePulse {
      0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,.0); }
      50% { box-shadow: 0 0 0 4px rgba(220,38,38,.14); }
    }

    .kb-list-next-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 7px;
      margin-bottom: 5px;
      font-weight: 900;
      color: #0f172a;
    }

    .kb-list-next-title {
      display: flex;
      align-items: center;
      gap: 5px;
      min-width: 0;
    }

    .kb-list-next-title strong {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 190px;
    }

    .kb-list-next-line {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-top: 3px;
      line-height: 1.3;
    }

    .kb-list-next-line .feather,
    .kb-list-next-title .feather {
      width: 13px;
      height: 13px;
      color: #74b2d4;
      flex: 0 0 auto;
    }

    .kb-list-next-box-btn {
      border: 1px solid #dbeafe;
      background: #fff;
      border-radius: 999px;
      padding: 3px 7px;
      color: #334155;
      font-size: 10px;
      font-weight: 900;
      cursor: pointer;
      white-space: nowrap;
    }

    .kb-kanban-task-toast-wrap {
      position: fixed;
      right: 18px;
      bottom: 18px;
      width: 380px;
      max-width: calc(100vw - 30px);
      z-index: 250000;
      display: flex;
      flex-direction: column;
      gap: 10px;
      pointer-events: none;
    }

    .kb-kanban-task-toast {
      pointer-events: auto;
      border-left: 6px solid #dc2626;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 18px 45px rgba(15,23,42,.22);
      overflow: hidden;
      animation: kbTaskToastIn .22s ease;
      cursor: pointer;
    }

    @keyframes kbTaskToastIn {
      from { transform: translateY(12px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .kb-kanban-task-toast-head {
      padding: 10px 12px;
      background: #fff5f5;
      color: #991b1b;
      font-weight: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }

    .kb-kanban-task-toast-body {
      padding: 10px 12px;
      color: #334155;
      font-size: 12px;
      line-height: 1.4;
    }
  </style>

@section('content')
@php
$canManageKanbanLeadStages = auth()->check() && \App\Models\UserRoll::query()
  ->where(function ($q) {
    $q->where('user_id', auth()->id());

    if (auth()->user() && auth()->user()->name) {
      $q->orWhere('user_id', auth()->user()->name);
    }
  })
  ->where('item_id', 'Administrator')
  ->where(function ($q) {
    $q->where('is_read', true)
      ->orWhere('is_add', true)
      ->orWhere('is_update', true)
      ->orWhere('is_delete', true);
  })
  ->exists();
@endphp

@php
$leadStageNamesForJs = $stageNames ?? [
  'lead' => 'Lead',
  'offer' => 'Angebot',
  'follow_up' => 'Nachfassen',
  'accepted' => 'Annehmen',
  'deal' => 'Auftrag',
  'project' => 'Montage',
  'completed' => 'Abschluss',
  'archive' => 'Archive',
  'junk' => 'Junk',
];

$leadStageMetaForJs = $stageMeta ?? [];

$kanbanStageNamesForJs = collect($leadStageNamesForJs)
  ->reject(fn($label, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanStageMetaForJs = collect($leadStageMetaForJs)
  ->reject(fn($meta, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanProductsForJs = collect($products ?? [])
  ->map(fn($product) => [
    'id' => $product->id ?? null,
    'name' => $product->article_group ?? $product->initial ?? ('Produkt #' . ($product->id ?? '')),
    'initial' => $product->initial ?? null,
  ])
  ->filter(fn($product) => !empty($product['id']))
  ->values()
  ->toArray();
@endphp
<div class="app-content"> 
  <div class="content-wrapper">   
     {{-- ======= MAIN SHELL ======= --}}
  <section id="basic-tabs-components">
    <style>
        /* =========================================================
           Scoped Modern Tabs: Kanban / Liste / Junk / Ticket
           This is scoped to #basic-tabs-components to avoid conflicts.
        ========================================================= */

        #basic-tabs-components,
        #basic-tabs-components * {
            box-sizing: border-box;
        }

        #basic-tabs-components .pro-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 56px;
            gap: .75rem;
            align-items: start;
        }

        #basic-tabs-components .pro-main {
            min-width: 0;
            width: 100%;
        }

        #basic-tabs-components .pro-rail {
            position: relative;
            width: 70px;
            min-height: 44px;
            display: grid;
            place-items: center;
            right: 40px;
        }

        #basic-tabs-components .rail-btn {
            position: relative;
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 14px !important;
            background: #8fc73e;
            color: #23310f;
            display: grid;
            place-items: center;
            box-shadow: 0 8px 22px rgba(143, 199, 62, .25);
            cursor: pointer;
            transition: transform .15s ease, background .15s ease, box-shadow .15s ease;
        }

        #basic-tabs-components .rail-btn:hover {
            transform: translateY(-2px);
            background: #e7f3d2;
            box-shadow: 0 10px 26px rgba(147, 194, 28, .32);
        }

        #basic-tabs-components .rail-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 18px;
            height: 18px;
            line-height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: #e50656;
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            text-align: center;
        }

        #basic-tabs-components .d-none {
            display: none !important;
        }

        /* Header shell */
        #basic-tabs-components .pro-tabs-shell {
            background: #ffffff;
            border: 1px solid #e5eef5;
            border-radius: 24px;
            padding: 14px;
            box-shadow: 0 14px 36px rgba(15, 23, 42, .07);
            margin-bottom: 18px;
            overflow: visible;
        }

        #basic-tabs-components .pro-tabs-topbar {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            width: 100%;
        }

        /* Force horizontal tabs */
        #basic-tabs-components .pro-tabs-nav,
        #basic-tabs-components ul.nav.nav-tabs.pro-tabs-nav {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
            border: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            width: auto !important;
            list-style: none !important;
        }

        #basic-tabs-components .pro-tabs-nav .nav-item {
            display: block !important;
            width: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            float: none !important;
        }

        #basic-tabs-components .pro-tabs-nav .nav-link {
            position: relative;
            min-height: 56px;
            padding: 8px 14px 8px 10px !important;
            border: 1px solid #dbeafe !important;
            border-radius: 18px !important;
            background: #f8fafc !important;
            color: #475569 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 10px !important;
            font-size: 13px;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
            transition: all .18s ease;
            box-shadow: none !important;
            text-decoration: none !important;
        }

        #basic-tabs-components .pro-tabs-nav .nav-link:hover {
            background: #eff6ff !important;
            color: #2563eb !important;
            border-color: #bfdbfe !important;
            transform: translateY(-1px);
        }

        #basic-tabs-components .pro-tabs-nav .nav-link.active {
            background: linear-gradient(135deg, #74b2d4 0%, #93c21c 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            box-shadow: 0 14px 28px rgba(116, 178, 212, .30) !important;
        }

        #basic-tabs-components .pro-tab-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #ffffff;
            color: #74b2d4;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .07);
        }

        #basic-tabs-components .pro-tab-icon svg {
            width: 19px;
            height: 19px;
            stroke-width: 2.4;
        }

        #basic-tabs-components .pro-tabs-nav .nav-link.active .pro-tab-icon {
            background: rgba(255, 255, 255, .20);
            color: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .25);
        }

        #basic-tabs-components .pro-tab-text {
            display: inline-flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0;
        }

        #basic-tabs-components .pro-tab-title {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: inherit;
            white-space: nowrap;
        }

        #basic-tabs-components .pro-tab-sub {
            font-size: 10px;
            font-weight: 900;
            opacity: .66;
            color: inherit;
            white-space: nowrap;
        }

        #basic-tabs-components .pro-tabs-nav .nav-link.active .pro-tab-sub {
            opacity: .9;
        }

        #basic-tabs-components .pro-tab-count {
            min-width: 23px;
            height: 23px;
            padding: 0 7px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            line-height: 23px;
        }

        #basic-tabs-components .pro-tabs-nav .nav-link.active .pro-tab-count {
            background: rgba(255, 255, 255, .24);
            color: #ffffff;
        }

        /* Sort */
        #basic-tabs-components .pro-sort-box {
            min-height: 56px;
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        #basic-tabs-components .pro-sort-icon {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #ffffff;
            color: #74b2d4;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .07);
        }

        #basic-tabs-components .pro-sort-icon svg {
            width: 19px;
            height: 19px;
            stroke-width: 2.4;
        }

        #basic-tabs-components .pro-sort-label {
            margin: 0;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        #basic-tabs-components .pro-sort-select {
            width: 240px;
            height: 36px;
            border: 0 !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            box-shadow: inset 0 0 0 1px #e2e8f0;
            outline: none;
        }

        #basic-tabs-components .pro-sort-select:focus {
            box-shadow: inset 0 0 0 2px rgba(116, 178, 212, .45);
        }

        /* Content */
        #basic-tabs-components .pro-tabs-content-card {
            background: #ffffff;
            border: 1px solid #e5eef5;
            border-radius: 24px;
            padding: 14px;
            box-shadow: 0 14px 36px rgba(15, 23, 42, .05);
            overflow: visible;
        }

        #basic-tabs-components .tab-content,
        #basic-tabs-components .tab-pane {
            width: 100%;
        }

        #basic-tabs-components .table-responsive {
            overflow: visible !important;
        }

        #basic-tabs-components .table thead th {
            background: #f8fafc;
            color: #334155;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-color: #e5eef5;
            white-space: nowrap;
        }

        #basic-tabs-components .table tbody td {
            vertical-align: top !important;
        }

        /* Responsive */
        @media (max-width: 992px) {
            #basic-tabs-components .pro-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            #basic-tabs-components .pro-rail {
                position: fixed;
                right: 18px;
                bottom: 84px;
                z-index: 50;
                width: auto;
                min-height: auto;
            }
        }

        @media (max-width: 991px) {
            #basic-tabs-components .pro-tabs-topbar {
                align-items: stretch;
            }

            #basic-tabs-components .pro-tabs-nav {
                width: 100% !important;
            }

            #basic-tabs-components .pro-tabs-nav .nav-item {
                flex: 1 1 calc(50% - 10px);
            }

            #basic-tabs-components .pro-tabs-nav .nav-link {
                width: 100%;
            }

            #basic-tabs-components .pro-sort-box {
                width: 100%;
            }

            #basic-tabs-components .pro-sort-select {
                width: 100%;
            }
        }

        @media (max-width: 575px) {
            #basic-tabs-components .pro-tabs-shell,
            #basic-tabs-components .pro-tabs-content-card {
                border-radius: 18px;
                padding: 10px;
            }

            #basic-tabs-components .pro-tabs-nav .nav-item {
                flex: 1 1 100%;
            }

            #basic-tabs-components .pro-tabs-nav .nav-link {
                min-height: 52px;
            }

            #basic-tabs-components .pro-tab-icon,
            #basic-tabs-components .pro-sort-icon {
                width: 34px;
                height: 34px;
                border-radius: 12px;
            }

            #basic-tabs-components .pro-sort-box {
                flex-wrap: wrap;
            }

            #basic-tabs-components .pro-sort-label {
                width: calc(100% - 44px);
            }

            #basic-tabs-components .pro-sort-select {
                width: 100%;
            }
        }
    </style>
    <style>
      #basic-tabs-components .pro-layout{ grid-template-columns:minmax(0,1fr)!important; }
      #basic-tabs-components .pro-rail{ display:none!important; }
      #basic-tabs-components .pro-tabs-nav .nav-link.active{ background:#74b2d4!important; border-color:#74b2d4!important; box-shadow:0 14px 28px rgba(116,178,212,.25)!important; }
      #basic-tabs-components .pro-sort-box{ flex-wrap:wrap; }
      .lsm-btn .lsm-btn-icon,.lsm-title-icon{ background:#74b2d4!important; }
      .lsm-btn--filter .lsm-btn-icon{ background:#93c21c!important; }
      .lsm-head{ background:#fff!important; }
      .column h3{ background:#93c21c!important; }
      .column[data-stage-color="1"] h3{ background:var(--stage-color,#93c21c)!important; }
    </style>

    <style>
      /* ===== Kanban LeadStage/SubStage Admin ===== */
      .kb-stage-admin-btn {
        border: 1px solid #93c21c;
        background: #93c21c;
        color: #fff !important;
        border-radius: 12px !important;
        min-height: 34px;
        padding: 7px 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 900;
        text-decoration: none !important;
        cursor: pointer;
        white-space: nowrap;
        transition: .15s ease;
      }
    
      .kb-stage-admin-btn:hover {
        background: #7faa18;
        border-color: #7faa18;
        color: #fff !important;
        transform: translateY(-1px);
      }
    
      .kbsa-backdrop {
        position: fixed;
        inset: 0;
        z-index: 200000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 23, 42, .58);
      }
    
      .kbsa-backdrop.is-open {
        display: flex;
      }
    
      .kbsa-modal {
        width: min(1180px, 96vw);
        max-height: calc(100vh - 36px);
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
        display: flex;
        flex-direction: column;
        overflow: hidden;
      }
    
      .kbsa-head,
      .kbsa-foot {
        padding: 15px 18px;
        background: #fafafa;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
      }
    
      .kbsa-foot {
        border-top: 1px solid #e5e7eb;
        border-bottom: 0;
        justify-content: flex-end;
      }
    
      .kbsa-title {
        margin: 0;
        font-size: 17px;
        font-weight: 900;
        color: #111827;
        text-transform: uppercase;
      }
    
      .kbsa-sub {
        margin-top: 4px;
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
      }
    
      .kbsa-close {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #64748b;
        cursor: pointer;
        font-size: 22px;
        line-height: 1;
      }
    
      .kbsa-body {
        padding: 16px;
        overflow: auto;
      }
    
      .kbsa-toolbar {
        display: grid;
        grid-template-columns: minmax(180px, 1fr) 150px 120px 120px auto auto;
        gap: 10px;
        align-items: end;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 12px;
        background: #f8fafc;
        margin-bottom: 14px;
      }
    
      .kbsa-label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 900;
        color: #64748b;
        text-transform: uppercase;
      }
    
      .kbsa-input {
        width: 100%;
        min-height: 38px;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        padding: 8px 10px;
        background: #fff;
        color: #111827;
        font-size: 13px;
        outline: none;
      }
    
      .kbsa-input:focus {
        border-color: #93c21c;
        box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
      }
    
      .kbsa-check {
        min-height: 38px;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        padding: 8px 10px;
        background: #fff;
        font-size: 12px;
        font-weight: 900;
      }
    
      .kbsa-btn,
      .kbsa-btn-soft,
      .kbsa-btn-danger {
        border: 0;
        border-radius: 12px;
        min-height: 38px;
        padding: 8px 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        white-space: nowrap;
      }
    
      .kbsa-btn {
        background: #93c21c;
        color: #fff;
      }
    
      .kbsa-btn-soft {
        background: #fff;
        color: #334155;
        border: 1px solid #dbeafe;
      }
    
      .kbsa-btn-danger {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
      }
    
      .kbsa-error {
        display: none;
        margin-bottom: 12px;
        padding: 10px 12px;
        border-radius: 14px;
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
        font-size: 12px;
        font-weight: 800;
        white-space: pre-line;
      }
    
      .kbsa-error.is-visible {
        display: block;
      }
    
      .kbsa-stage {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        margin-bottom: 12px;
        overflow: hidden;
      }
    
      .kbsa-stage-head {
        display: grid;
        grid-template-columns: 34px minmax(0, 1.2fr) minmax(0, .9fr) 82px 88px 88px 96px auto;
        gap: 8px;
        align-items: center;
        padding: 10px;
        background: #f9fafb;
        border-bottom: 1px solid #eef2f7;
      }
    
      .kbsa-sub-list {
        padding: 10px;
      }
    
      .kbsa-sub {
        display: grid;
        grid-template-columns: 34px minmax(0, 1.4fr) minmax(0, .9fr) 82px 90px auto;
        gap: 8px;
        align-items: center;
        padding: 8px;
        border: 1px solid #eef2f7;
        border-radius: 12px;
        margin-bottom: 8px;
        background: #fcfcfd;
      }
    
      .kbsa-handle {
        cursor: grab;
        color: #64748b;
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }
    
      .kbsa-small {
        font-size: 11px;
        font-weight: 900;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 8px;
      }
    
      @media (max-width: 1100px) {
    
        .kbsa-toolbar,
        .kbsa-stage-head,
        .kbsa-sub {
          grid-template-columns: 1fr;
        }
    
        .kbsa-handle {
          justify-content: flex-start;
        }
      }
    </style>




<style>
/* =========================================================
   Sidebar + Pipeline UX patch
   - filter drawer redesigned
   - pipeline manager behaves like right sidebar
   - visible toggle pills
   - cleaner appointment/task drawers
========================================================= */
.drawer{
  width:min(680px,96vw)!important;
  border-top-left-radius:28px!important;
  border-bottom-left-radius:28px!important;
  box-shadow:-22px 0 55px rgba(15,23,42,.22)!important;
}
.drawer-header{
  min-height:76px;
  padding:16px 18px!important;
  background:#ffffff!important;
}
.drawer-header .btn{
  border-radius:12px!important;
  font-weight:800;
}
.drawer-body{
  padding:16px!important;
  background:#f8fafc!important;
}
#view-summary{
  background:#fff;
  border:1px solid #e5eef5;
  border-radius:22px;
  padding:12px;
  box-shadow:0 12px 30px rgba(15,23,42,.06);
}
#summaryStats .summary-card > div{
  border:1px solid #e5eef5!important;
  border-radius:16px!important;
  box-shadow:none!important;
}
#kanbanFilterForm{
  display:grid!important;
  grid-template-columns:repeat(2,minmax(0,1fr));
  gap:12px!important;
  background:#fff!important;
  border:1px solid #e5eef5!important;
  border-radius:22px!important;
  padding:16px!important;
  box-shadow:0 12px 30px rgba(15,23,42,.06)!important;
}
#kanbanFilterForm > [class*="col-"]{
  max-width:100%!important;
  flex:none!important;
  padding-left:0!important;
  padding-right:0!important;
}
#kanbanFilterForm label{
  margin-bottom:6px!important;
  font-size:11px!important;
  font-weight:900!important;
  color:#475569!important;
  text-transform:uppercase!important;
  letter-spacing:.04em;
}
#kanbanFilterForm label::before{ display:none!important; }
#kanbanFilterForm .form-control,
#kanbanFilterForm .select2-container .select2-selection--single{
  min-height:42px!important;
  border-radius:14px!important;
  border-color:#dbeafe!important;
  background:#f8fafc!important;
}
#kanbanFilterForm .select2-container--default .select2-selection--single .select2-selection__rendered{
  line-height:40px!important;
  font-weight:700!important;
  color:#334155!important;
}
#kanbanFilterForm .select2-container--default .select2-selection--single .select2-selection__arrow{
  height:40px!important;
}
#columnTogglesContainer{
  display:grid!important;
  grid-template-columns:repeat(2,minmax(0,1fr));
  gap:8px!important;
}
#columnTogglesContainer .custom-control{
  margin:0!important;
  padding:0!important;
}
#columnTogglesContainer .custom-control-input{
  position:absolute!important;
  opacity:0!important;
}
#columnTogglesContainer .custom-control-label{
  width:100%;
  min-height:42px;
  display:flex!important;
  align-items:center!important;
  gap:8px!important;
  padding:8px 10px!important;
  border:1px solid #e5eef5;
  border-radius:14px;
  background:#f8fafc;
  color:#64748b;
  font-weight:900;
}
#columnTogglesContainer .custom-control-label::before,
#columnTogglesContainer .custom-control-label::after{
  display:none!important;
}
#columnTogglesContainer .custom-control-input:checked + .custom-control-label{
  border-color:#93c21c;
  background:#f2f8e7;
  color:#1f2937;
}
#columnTogglesContainer .toggle-icon-off{ display:inline-flex!important; }
#columnTogglesContainer .toggle-icon-on{ display:none!important; }
#columnTogglesContainer .custom-control-input:checked + .custom-control-label .toggle-icon-on{ display:inline-flex!important; }
#columnTogglesContainer .custom-control-input:checked + .custom-control-label .toggle-icon-off{ display:none!important; }
#columnTogglesContainer .toggle-label-text{
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}

/* Pipeline manager as right sidebar */
.lsm-modal{ display:block!important; pointer-events:none; }
.lsm-modal.is-open{ pointer-events:auto; }
.lsm-backdrop{ opacity:0; pointer-events:none; transition:opacity .2s ease; }
.lsm-modal.is-open .lsm-backdrop{ opacity:1; pointer-events:auto; }
.lsm-panel{
  position:fixed!important;
  top:0!important;
  right:0!important;
  left:auto!important;
  bottom:0!important;
  width:min(1089px,96vw)!important;
  height:100vh!important;
  max-height:none!important;
  border-radius:28px 0 0 28px!important;
  transform:translateX(100%)!important;
  transition:transform .24s ease!important;
  box-shadow:-22px 0 55px rgba(15,23,42,.24)!important;
}
.lsm-modal.is-open .lsm-panel{ transform:translateX(0)!important; }
.lsm-head{ min-height:76px; background:#fff!important; }
.lsm-body{ flex:1; overflow:auto; }
.lsm-grid{ grid-template-columns:360px minmax(0,1fr)!important; align-items:start; }
.lsm-card{ border-radius:22px!important; }
.lsm-stage-row{ grid-template-columns:42px minmax(150px,1.3fr) 110px 105px 138px!important; }
.lsm-stage-row[data-stage-id]{ cursor:grab; }
.lsm-stage-row[data-stage-id]:active{ cursor:grabbing; }
.lsm-stage-row.is-dragging{ opacity:.42; background:#f8fafc; }
.lsm-stage-row.is-drop-target{ outline:2px dashed #93c21c; outline-offset:-4px; }
#lsmSaveOrder{ border-color:#93c21c!important; color:#3f6212!important; font-weight:900!important; }
#lsmSaveOrder:hover{ background:#f2f8e7!important; }

/* Appointment + task drawer polish */
.ap-drawer,
#pt-drawer{
  border-top-left-radius:28px!important;
  border-bottom-left-radius:28px!important;
  box-shadow:-24px 0 60px rgba(15,23,42,.23)!important;
}
.ap-tabs,
#pt-drawer .pt-tabs{
  gap:8px!important;
  padding:10px 14px 0!important;
  background:#fff!important;
}
.ap-tab-link,
#pt-drawer .pt-tab{
  border:1px solid #e5eef5!important;
  border-bottom:0!important;
  border-radius:16px 16px 0 0!important;
  background:#f8fafc!important;
  color:#64748b!important;
  font-weight:900!important;
}
.ap-tab-link.active,
#pt-drawer .pt-tab.is-active{
  background:#fff!important;
  color:#0f172a!important;
  border-color:#93c21c!important;
}
.ap-toolbar{
  border:1px solid #e5eef5!important;
  border-radius:18px!important;
  margin:12px!important;
  padding:10px!important;
  box-shadow:0 10px 24px rgba(15,23,42,.05);
}
.ap-toolbar .btn{ border-radius:12px!important; font-weight:800; }
.ap-filter .select2-container{ min-width:260px; }
.ap-filter .select2-selection--single{
  min-height:34px!important;
  border-radius:12px!important;
  border-color:#dbeafe!important;
}
@media(max-width:991px){
  #kanbanFilterForm{ grid-template-columns:1fr; }
  #columnTogglesContainer{ grid-template-columns:1fr; }
  .lsm-grid{ grid-template-columns:1fr!important; }
  .lsm-stage-head{ display:none!important; }
  .lsm-stage-row{ grid-template-columns:42px minmax(0,1fr)!important; }
  .lsm-stage-row > div:nth-child(n+3){ grid-column:2; }
}
.kb-task-convert-box {
    margin-top: 14px;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #f8fafc;
}

.kb-task-check {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    margin-bottom: 8px;
    cursor: pointer;
}

.kb-task-check input {
    width: 16px;
    height: 16px;
}

.kb-appointment-options {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed #d1d5db;
}
</style>

<style>
/* =========================================================
   FIX: SweetAlert must stay above Phase/Stufen modal/sidebar
========================================================= */

/* SweetAlert2 main container */
.swal2-container {
    z-index: 999999 !important;
}

/* SweetAlert popup */
.swal2-popup {
    z-index: 1000000 !important;
}

/* If Bootstrap modal/backdrop is open behind SweetAlert */
.modal-backdrop {
    z-index: 1040 !important;
}

.modal {
    z-index: 1050 !important;
}

/* Your phase/sidebar drawers should stay under SweetAlert */
.lead-stage-modal,
.stage-manager-modal,
.phase-pipeline-modal,
.lsm-modal,
.drawer,
.notes-drawer,
.lh-root,
.lh-panel {
    z-index: 1080 !important;
}

/* Backdrops under drawer, but still under SweetAlert */
.drawer-backdrop,
.notes-backdrop,
.lh-backdrop {
    z-index: 1075 !important;
}
</style>

    <div class="pro-layout">
        <div class="pro-main">
            <div class="row">
                <div class="col-sm-12">

                    {{-- Top Tabs Header --}}
                    <div class="pro-tabs-shell">
                        <div class="pro-tabs-topbar">

                            {{-- Navigation Tabs --}}
                            <ul class="nav nav-tabs pro-tabs-nav" role="tablist">

                                {{-- Kanban --}}
                                <li class="nav-item">
                                    <a class="nav-link active"
                                       id="home-tab"
                                       data-toggle="tab"
                                       href="#home"
                                       role="tab"
                                       aria-controls="home"
                                       aria-selected="true">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                                <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                                <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                                <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Kanban
                                                <span class="pro-tab-count" id="tabCountKanban">
                                                    {{ $tabCounts['kanban'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Board Ansicht</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Liste --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="profile-tab"
                                       data-toggle="tab"
                                       href="#profile"
                                       role="tab"
                                       aria-controls="profile"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                                <circle cx="4" cy="6" r="1.5"></circle>
                                                <circle cx="4" cy="12" r="1.5"></circle>
                                                <circle cx="4" cy="18" r="1.5"></circle>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Liste
                                                <span class="pro-tab-count" id="tabCountList">
                                                    {{ $tabCounts['list'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Tabellen Ansicht</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Junk --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="junk-tab"
                                       data-toggle="tab"
                                       href="#junk"
                                       role="tab"
                                       aria-controls="junk"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Junk
                                                <span class="pro-tab-count" id="tabCountJunk">
                                                    {{ $tabCounts['junk'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Archiv / Junk</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Ticket --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="ticket-tab"
                                       data-toggle="tab"
                                       href="#ticket"
                                       role="tab"
                                       aria-controls="ticket"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M3 9a3 3 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a3 3 0 0 0 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"></path>
                                                <path d="M9 9h6"></path>
                                                <path d="M9 13h6"></path>
                                                <path d="M9 17h3"></path>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Ticket
                                                <span class="pro-tab-count" id="tabCountTicket">
                                                    {{ $tabCounts['ticket'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Service Fälle</span>
                                        </span>
                                    </a>
                                </li>

                            </ul>

                            {{-- Sort Dropdown --}}
                            <div class="pro-sort-box">
                                <span class="pro-sort-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M3 6h12"></path>
                                        <path d="M3 12h8"></path>
                                        <path d="M3 18h6"></path>
                                        <path d="M18 6v12"></path>
                                        <path d="M15 15l3 3 3-3"></path>
                                    </svg>
                                </span>

                                <label for="listSortSelect" class="pro-sort-label">
                                    Sortieren
                                </label>

                                <select id="listSortSelect" class="custom-select custom-select-sm pro-sort-select">
                                    <optgroup label="Datum">
                                        <option value="created_at|desc" selected>Datum (neueste zuerst)</option>
                                        <option value="created_at|asc">Datum (älteste zuerst)</option>
                                    </optgroup>

                                    <optgroup label="Zuletzt aktualisiert">
                                        <option value="updated_at|desc">Aktualisiert (neueste)</option>
                                        <option value="updated_at|asc">Aktualisiert (älteste)</option>
                                    </optgroup>

                                    <optgroup label="Kunde">
                                        <option value="customer_lastname|asc">Kunde (A-Z)</option>
                                        <option value="customer_lastname|desc">Kunde (Z-A)</option>
                                    </optgroup>

                                    <optgroup label="Ort">
                                        <option value="city|asc">Ort (A-Z)</option>
                                        <option value="city|desc">Ort (Z-A)</option>
                                    </optgroup>

                                    <optgroup label="Status">
                                        <option value="status|asc">Status (A-Z)</option>
                                        <option value="status|desc">Status (Z-A)</option>
                                    </optgroup>
                                </select>

                                <button type="button" class="lsm-btn lsm-btn--phase" id="btnOpenStageManager" title="Phasen verwalten">
                                    <span class="lsm-btn-icon"><i class="feather icon-sliders"></i></span>
                                    <span>Phasen</span>
                                </button>

                                @if($canManageKanbanLeadStages)
                                @endif

                                <button type="button" class="lsm-btn lsm-btn--filter" id="btnOpenDrawer" title="Übersicht & Filter">
                                    <span class="lsm-btn-icon"><i class="feather icon-filter"></i></span>
                                    <span>Filter</span>
                                    <span id="filterBadge" class="rail-badge d-none">0</span>
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- Tabs Content --}}
                    <div class="pro-tabs-content-card">
                        <div class="tab-content">

                            {{-- Kanban --}}
                            <div class="tab-pane show active"
                                 id="home"
                                 aria-labelledby="home-tab"
                                 role="tabpanel">
                                <div class="kanban-zoom-card">
                                    <div class="kanban-zoom-toolbar" aria-label="Kanban Anzeige">
                                        <div class="kanban-zoom-left">
                                            <span class="kanban-zoom-title">Kanban Ansicht</span>
                                            <span class="kanban-zoom-sub">Größe anpassen, damit mehr Spalten sichtbar sind</span>
                                        </div>
 

                                        <div class="kanban-zoom-actions">
                                            <button type="button" class="kbz-btn" data-kb-zoom="1">100%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.9">90%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.8">80%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.7">70%</button>
                                            <button type="button" class="kbz-btn kbz-btn--ghost" id="kbZoomOutBtn" title="Eine Stufe kleiner">
                                                <i class="feather icon-zoom-out"></i>
                                            </button>
                                            <button type="button" class="kbz-btn kbz-btn--ghost" id="kbZoomInBtn" title="Eine Stufe größer">
                                                <i class="feather icon-zoom-in"></i>
                                            </button>
                                            <select id="kbColumnWidthSelect" class="kbz-select" title="Spaltenbreite">
                                                <option value="normal">Normal</option>
                                                <option value="compact">Schmal</option>
                                                <option value="wide">Breit</option>
                                            </select>
                                            <label class="kbz-compact-toggle">
                                                <input type="checkbox" id="kbCompactToggle">
                                                <span>Kompakt</span>
                                            </label>
                                            <label class="kbz-compact-toggle" title="Wenn aus, bleibt jede Spalte grün (#93c21c)">
                                                <input type="checkbox" id="kbUseStageColorsToggle">
                                                <span>Spaltenfarbe nutzen</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="kanban-zoom-area" id="kanbanZoomArea">
                                        <div id="kanban" class="kanban-container"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- List --}}
                            <div class="tab-pane"
                                 id="profile"
                                 aria-labelledby="profile-tab"
                                 role="tabpanel">
                                <div class="table-responsive p-0">
                                    <table class="table pro-list-table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="sortable active desc" data-sort="created_at">
                                                    <span><i class="feather icon-calendar"></i> Datum</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="customer_lastname">
                                                    <span><i class="feather icon-user"></i> Kunde</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="city">
                                                    <span><i class="feather icon-map-pin"></i> Ort</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="product">
                                                    <span><i class="feather icon-box"></i> Produkt</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="employee">
                                                    <span><i class="feather icon-users"></i> Mitarbeiter</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="updated_at">
                                                    <span><i class="feather icon-activity"></i> Status</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="status">
                                                    <span><i class="feather icon-layers"></i> Phase</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="kanbanTableBody">
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    Lade Daten…
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div id="listPagination" class="d-flex justify-content-center mt-3"></div>
                                </div>
                            </div>

                            {{-- Junk --}}
                            <div class="tab-pane"
                                 id="junk"
                                 aria-labelledby="junk-tab"
                                 role="tabpanel">
                                @include('admin.kanban.partials.junk', ['junk' => $junk])
                            </div>

                            {{-- Ticket --}}
                            <div class="tab-pane"
                                 id="ticket"
                                 aria-labelledby="ticket-tab"
                                 role="tabpanel">
                                @include('admin.kanban.partials.ticket', [
  'tickets' => $tickets ?? null,
  'total' => $tabCounts['ticket'] ?? 0
])
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
</section>

    {{-- ======= SINGLE DRAWER: Übersicht & Filter ======= --}}
    <div class="drawer-backdrop" id="drawerBackdrop"></div>
    <aside class="drawer" id="sideDrawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
      <div class="drawer-header">
        <div class="d-flex align-items-center">
          <i class="feather icon-sliders mr-2"></i>
          <h5 id="drawerTitle" class="mb-0">Übersicht &amp; Filter</h5>
          <span id="tabFilterCount" class="tab-badge-inline d-none ml-2">0</span>
        </div>
        <div class="d-flex align-items-center">
          <button class="btn btn-sm btn-outline-secondary mr-1" id="btnClearFilters"><i class="feather icon-rotate-ccw"></i> Alles löschen</button>
          <button class="btn btn-sm btn-primary" id="btnApplyFilters"><i class="feather icon-check-circle"></i> Anwenden</button>
          <button class="btn btn-sm btn-outline-secondary ml-1" data-close-drawer><i class="feather icon-x"></i></button>
        </div>
      </div>

      {{-- Chips summary of active filters --}}
      <div class="px-3 pt-2">
        <div id="activeFilterChips" class="chips"></div>
      </div>

      <div class="drawer-body">
        <!-- SUMMARY (top) -->
        <div id="view-summary" class="mb-1">
          <div class="row text-center" id="summaryStats" style="justify-content:center">
            <div id="cardEmployees" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Verantwortliche</strong>
                <div id="totalEmployees" class="h4">{{ $totalEmployees ?? 0 }}</div>
              </div>
            </div>
            <div id="cardProducts" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Produkt</strong>
                <div id="totalProduct" class="h4">{{ $totalProducts ?? 0 }}</div>
              </div>
            </div>
            <div id="cardCustomers" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Kunde</strong>
                <div id="totalCustomer" class="h4">{{ $totalCustomers ?? 0 }}</div>
              </div>
            </div>
            <div id="cardAnfragen" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Nachfrage</strong>
                <div id="totalAnfrage" class="h4">{{ ($tabCounts['kanban'] ?? 0) }}</div>
              </div>
            </div>

            <div id="cardOffen" class="col-12 summary-card mb-2">
              <div class="border rounded py-2 bg-orange text-white" style="background:#f49f43;color:white!important;">
                <strong>Offen</strong>
                <div id="statusOffen" class="h4 text-white">
                  {{ $statusCounts['offen'] ?? 0 }} <small>({{ $statusPercentages['offen'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardZusage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-primary text-white">
                <strong>Zusage</strong>
                <div id="statusZusage" class="h4 text-white">
                  {{ $statusCounts['zusage'] ?? 0 }} <small>({{ $statusPercentages['zusage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardAbsage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-danger text-white">
                <strong>Absage</strong>
                <div id="statusAbsage" class="h4 text-white">
                  {{ $statusCounts['absage'] ?? 0 }} <small>({{ $statusPercentages['absage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <hr class="my-2">

        <!-- FILTER (below summary) -->
        <div id="view-filter">
          <form id="kanbanFilterForm" class="row align-items-end g-2">
            <div class="col-md-6">
              <label for="customerFilter" class="form-label d-flex align-items-center">
                Kunde <span class="badge badge-secondary ml-2 d-none" id="countCustomers">{{ $totalCustomers ?? 0 }}</span>
              </label>
              <select name="customer" id="customerFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($customers as $customer)
                  <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="stageFilter" class="form-label">Phase</label>
              <select name="stage" id="stageFilter" class="form-control select2 stage-color-select">
                <option value="">Alle Phasen</option>
                @foreach(($stageNames ?? []) as $key => $label)
                  @php
  $stageKey = strtolower((string) $key);
  $meta = $stageMeta[$key] ?? [];
  $color = $meta['color'] ?? '#93c21c';
  $icon = $meta['icon'] ?? 'circle';
                  @endphp
                  @if(!in_array($stageKey, ['junk', 'ticket'], true))
                    <option value="{{ $key }}" data-color="{{ $color }}" data-icon="{{ $icon }}">{{ $label }}</option>
                  @endif
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="leadAgeFilter" class="form-label">Lead-Alter (Farbe)</label>
              <select name="lead_age" id="leadAgeFilter" class="form-control select2">
                <option value="">Alle Zeiten</option>
                <option value="green">🟢 Neu (< 24h)</option>
                <option value="orange">🟠 Letzter Tag (24 - 48h)</option>
                <option value="red">🔴 Überfällig (> 48h)</option>
              </select>
            </div>

            <div class="col-md-6">
            <label for="branchFilter" class="form-label d-flex align-items-center">
              Filiale
              <span class="badge badge-secondary ml-2 d-none" id="countBranches">{{ count($branches ?? []) }}</span>
            </label>

            <select name="branch" id="branchFilter" class="form-control select2">
              <option value="">Alle</option>
              @foreach (($branches ?? []) as $b)
                <option value="{{ $b->id }}" data-color="{{ $b->color ?? '#93c21c' }}">
                  {{ $b->branch }}
                </option>
              @endforeach
            </select>
          </div>


            <div class="col-md-6">
              <label for="employeeFilter" class="form-label d-flex align-items-center">
                Mitarbeiter <span class="badge badge-secondary ml-2 d-none" id="countEmployees">{{ $totalEmployees ?? 0 }}</span>
              </label>
             <select name="employee" id="employeeFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($employees as $employee)
                  <option value="{{ $employee->id }}">
                    {{ $employee->name }} {{ $employee->lastname }}
                  </option>
                @endforeach
              </select>

            </div>

            <div class="col-md-6">
              <label for="departmentFilter" class="form-label d-flex align-items-center">
                Abteilung <span class="badge badge-secondary ml-2 d-none" id="countDepartments">{{ $totalDepartments ?? 0 }}</span>
              </label>
              <select name="department" id="departmentFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($departments as $department)
                  <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="productFilter" class="form-label d-flex align-items-center">
                Produkt <span class="badge badge-secondary ml-2 d-none" id="countProducts">{{ $totalProducts ?? 0 }}</span>
              </label>
              <select name="product" id="productFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($products as $product)
                  <option value="{{ $product->id }}">{{ $product->article_group }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="interestFilter" class="form-label">Interesse</label>
              <select name="interest" id="interestFilter" class="form-control select2">
                <option value="">Alle Interessen</option>
                <option value="interest">Kaufinteresse</option>
                <option value="intent">Kaufabsicht</option>
                <option value="option">Kaufoption</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="dateFrom" class="form-label">Von (Datum)</label>
              <input type="date" name="date_from" id="dateFrom" class="form-control" />
            </div>

            <div class="col-md-6">
              <label for="dateTo" class="form-label">Bis (Datum)</label>
              <input type="date" name="date_to" id="dateTo" class="form-control" />
            </div>


           <div class="col-12">
              <hr class="my-2">
              <label class="form-label mb-3 font-weight-bold text-dark">
                  <i class="feather icon-layout mr-1"></i> Spalten Sichtbarkeit
              </label>

              <div class="d-flex flex-wrap" id="columnTogglesContainer">
                  @foreach(($kanbanStageNamesForJs ?? ['lead' => 'Lead', 'offer' => 'Angebot', 'deal' => 'Auftrag', 'project' => 'Montage', 'completed' => 'Abschluss', 'archive' => 'Archiv']) as $key => $label)
                    <div class="custom-control custom-checkbox mr-3 mb-2">
                        <input type="checkbox" 
                              class="custom-control-input col-toggle-checkbox" 
                              id="toggleCol_{{ $key }}" 
                              value="{{ $key }}"
                              {{ $key !== 'archive' ? 'checked' : '' }}>

                        <label class="custom-control-label d-flex align-items-center" for="toggleCol_{{ $key }}" style="cursor: pointer; user-select: none;">
                            {{-- Icon for ON --}}
                            <span class="toggle-icon-on mr-1">
                                <i class="feather icon-eye text-success"></i>
                            </span>
                            {{-- Icon for OFF --}}
                            <span class="toggle-icon-off mr-1">
                                <i class="feather icon-eye-off text-muted"></i>
                            </span>

                            {{-- Text Label --}}
                            <span class="toggle-label-text">{{ $label }}</span>
                        </label>
                    </div>
                  @endforeach
              </div>
          </div>

            <div class="col-12 small text-muted mt-2">
              Tipp: <kbd>Enter</kbd> = Anwenden, <kbd>Esc</kbd> = Schließen.
            </div>
          </form>
        </div>
      </div>
    </aside>




    {{-- ======= UNDERPHASE SIDEBAR: opens when clicking Unterphasen of a Hauptphase ======= --}}
    <div class="kb-understage-sidebar-backdrop" id="kbUnderstageSidebarBackdrop" data-understage-close></div>
    <aside class="kb-understage-sidebar" id="kbUnderstageSidebar" aria-hidden="true">
      <div class="kb-understage-sidebar-head">
        <div>
          <div class="kb-understage-sidebar-title">
            <i class="feather icon-git-branch"></i>
            <span id="kbUnderstageSidebarTitle">Unterphasen</span>
          </div>
          <div class="kb-understage-sidebar-subtitle" id="kbUnderstageSidebarSubtitle">
            Hauptphase auswählen, um die Unterphasen hier zu sehen. Das Haupt-Kanban bleibt im Hintergrund unverändert.
          </div>
        </div>
        <div class="kb-understage-sidebar-actions">
          <button type="button" class="kb-understage-refresh" id="kbUnderstageRefresh">
            <i class="feather icon-refresh-cw"></i> Neu laden
          </button>
          <button type="button" class="kb-understage-close" data-understage-close>
            <i class="feather icon-x"></i> Schließen
          </button>
        </div>
      </div>
      <div class="kb-understage-sidebar-body">
        <div class="kb-understage-board" id="kbUnderstageBoard">
          <div class="kb-understage-sidebar-empty">Noch keine Hauptphase ausgewählt.</div>
        </div>
      </div>
    </aside>

    {{-- ======= DYNAMIC LEAD STAGE MANAGER MODAL ======= --}}
    <div id="leadStageManagerModal" class="lsm-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="leadStageManagerTitle">
      <div class="lsm-backdrop" data-lsm-close></div>
      <div class="lsm-panel" tabindex="-1">
        <div class="lsm-head">
          <div class="lsm-title">
            <span class="lsm-title-icon"><i class="feather icon-sliders"></i></span>
            <div>
              <h5 id="leadStageManagerTitle">Pipeline-Phasen verwalten</h5>
              <p>Standard-Phasen können umbenannt werden. Löschen ist nur möglich, wenn keine Daten darin sind.</p>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-lsm-close aria-label="Schließen">
            <i class="feather icon-x"></i>
          </button>
        </div>

        <div class="lsm-body">
          <div class="lsm-grid">
            <div class="lsm-card">
              <div class="lsm-card-head">
                <strong id="lsmFormTitle">Neue Phase</strong>
                <button type="button" class="btn btn-sm btn-light border" id="lsmResetForm">
                  <i class="feather icon-refresh-cw"></i> Neu
                </button>
              </div>
              <div class="lsm-card-body">
                <form id="leadStageForm" class="lsm-form" autocomplete="off">
                  @csrf
                  <input type="hidden" id="lsmStageId">

                  <div class="lsm-form-group lsm-form-group--full">
                    <label>Name der Phase</label>
                    <input type="text" id="lsmStageName" class="form-control" maxlength="80" placeholder="z.B. Beratung" required>
                  </div>

                  <div class="lsm-form-grid">
                    <div class="lsm-form-group">
                      <label>Farbe</label>
                      <div class="lsm-color-input-wrap">
                        <input type="color" id="lsmStageColor" class="form-control" value="#74b2d4">
                        <span id="lsmStageColorText">#74b2d4</span>
                      </div>
                    </div>
                    <div class="lsm-form-group">
                      <label>Icon</label>
                      <select id="lsmStageIcon" class="form-control" style="width:100%"></select>
                    </div>
                  </div>

                  <div class="lsm-toggle-grid">
                    <label class="lsm-toggle-card" for="lsmStageActive">
                      <input type="checkbox" id="lsmStageActive" checked>
                      <span class="lsm-toggle-icon"><i class="feather icon-eye"></i></span>
                      <span>
                        <strong>Aktiv</strong>
                        <small>Phase im Board und Filter anzeigen</small>
                      </span>
                    </label>

                    <label class="lsm-toggle-card" for="lsmStageClosed">
                      <input type="checkbox" id="lsmStageClosed">
                      <span class="lsm-toggle-icon"><i class="feather icon-lock"></i></span>
                      <span>
                        <strong>Geschlossene Phase</strong>
                        <small>Zählt als abgeschlossen / beendet</small>
                      </span>
                    </label>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block lsm-save-btn">
                    <i class="feather icon-save"></i> Speichern
                  </button>
                </form>

                 
              </div>
            </div>

            <div class="lsm-card">
              <div class="lsm-card-head">
                <strong>Alle Phasen</strong>
                <div class="d-flex align-items-center" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="lsmReloadStages">
                    <i class="feather icon-refresh-cw"></i> Laden
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="lsmSaveOrder">
                    <i class="feather icon-list"></i> Sortierung speichern
                  </button>
                </div>
              </div>

              <div class="lsm-stage-row lsm-stage-head">
                <div><span class="lsm-drag-handle"><i class="feather icon-move"></i></span></div>
                <div>Phase</div>
                <div>Key</div>
                <div>Daten</div>
                <div>Aktion</div>
              </div>
              <div id="leadStagesList">
                <div class="lsm-empty">Phasen werden geladen…</div>
              </div>
            </div>
          </div>
        </div>



        {{-- ======= RELATED SUBSTAGE CONFIGURATION DRAWER INSIDE PHASE CONFIG ======= --}}
        <aside class="lsm-substage-drawer" id="lsmSubstageDrawer" aria-hidden="true">
          <div class="lsm-substage-head">
            <div>
              <h5 class="lsm-substage-title" id="lsmSubstageTitle">Unterphasen</h5>
              <div class="lsm-substage-subtitle" id="lsmSubstageSubtitle">
                Wähle eine Hauptphase, um die zugehörigen Unterphasen hier direkt zu konfigurieren.
              </div>
              <span class="lsm-substage-open-note"><i class="feather icon-info"></i> Direkt an der ausgewählten Phase konfigurieren</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="lsmCloseSubstageDrawer" aria-label="Unterphasen schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>

          <div class="lsm-substage-body">
            <div class="lsm-substage-create">
              <div class="lsm-substage-create-title">Neue Unterphase für ausgewählte Hauptphase</div>
              <input type="hidden" id="lsmSubstageStageId">
              <div class="lsm-substage-form-grid">
                <input type="text" id="lsmSubstageName" class="form-control" placeholder="Name, z.B. Rückruf vereinbart">
                <input type="text" id="lsmSubstageKey" class="form-control" placeholder="Key auto">
                <input type="color" id="lsmSubstageColor" class="form-control" value="#93c21c" title="Farbe">
                <input type="text" id="lsmSubstageIcon" class="form-control" value="list" placeholder="Icon">
              </div>
              <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:8px;">
                <label class="mb-0 small font-weight-bold text-muted d-inline-flex align-items-center" style="gap:6px;">
                  <input type="checkbox" id="lsmSubstageActive" checked> Aktiv
                </label>
                <button type="button" class="lsm-mini-btn primary" id="lsmCreateSubstage">
                  <i class="feather icon-plus"></i> Unterphase erstellen
                </button>
              </div>
            </div>

            <div class="lsm-substage-list-card">
              <div class="d-flex align-items-center justify-content-between mb-1" style="gap:8px;">
                <div class="lsm-substage-list-title mb-0">Unterphasen dieser Hauptphase</div>
                <button type="button" class="lsm-mini-btn blue" id="lsmSaveSubstageOrder">
                  <i class="feather icon-list"></i> Sortierung speichern
                </button>
              </div>
              <div id="lsmSubstageList">
                <div class="lsm-substage-empty">Noch keine Hauptphase ausgewählt.</div>
              </div>
            </div>
          </div>
        </aside>

        <div class="lsm-footer">
          <div class="small text-muted">
            Nach Änderungen wird die Seite automatisch neu geladen, damit Kanban-Spalten, Filter und Listen synchron bleiben.
          </div>
          <button type="button" class="btn btn-light border" data-lsm-close>Schließen</button>
        </div>
      </div>
    </div>


    @if($canManageKanbanLeadStages)
      {{-- ======= KANBAN LEADSTAGE + SUBSTAGE ADMIN MODAL ======= --}}
      <div class="kbsa-backdrop" id="kanbanStageAdminModal" aria-hidden="true">
          <div class="kbsa-modal" role="dialog" aria-modal="true">
              <div class="kbsa-head">
                  <div>
                      <h3 class="kbsa-title">Phasen / Unterphasen verwalten</h3>
                      <div class="kbsa-sub">
                          Phasen und Unterphasen zentral konfigurieren und per Drag & Drop sortieren.
                      </div>
                  </div>
                  <button type="button" class="kbsa-close" data-kbsa-close>&times;</button>
              </div>

              <div class="kbsa-body">
                  <div class="kbsa-toolbar">
                      <div>
                          <label class="kbsa-label">Phasenname</label>
                          <input type="text" class="kbsa-input" id="kbsaStageName" placeholder="z. B. Beratung geplant">
                      </div>
                      <div>
                          <label class="kbsa-label">Key</label>
                          <input type="text" class="kbsa-input" id="kbsaStageKey" placeholder="auto">
                      </div>
                      <div>
                          <label class="kbsa-label">Farbe</label>
                          <input type="color" class="kbsa-input" id="kbsaStageColor" value="#93c21c">
                      </div>
                      <div>
                          <label class="kbsa-label">Icon</label>
                          <input type="text" class="kbsa-input" id="kbsaStageIcon" value="columns">
                      </div>
                      <label class="kbsa-check">
                          <input type="checkbox" id="kbsaStageActive" checked> Aktiv
                      </label>
                      <button type="button" class="kbsa-btn" id="kbsaCreateStage">
                          <i class="feather icon-plus"></i> Erstellen
                      </button>
                  </div>

                  <div class="kbsa-error" id="kbsaError"></div>
                  <div class="kbsa-small">Phasen</div>
                  <div id="kbsaStageList">
                      <div class="kbsa-small">Lade LeadStages...</div>
                  </div>
              </div>

              <div class="kbsa-foot">
                  <button type="button" class="kbsa-btn-soft" id="kbsaReloadStages">
                      <i class="feather icon-refresh-cw"></i> Neu laden
                  </button>
                  <button type="button" class="kbsa-btn-soft" data-kbsa-close>Schließen</button>
              </div>
          </div>
      </div>
    @endif


    <!-- Live Feed Modal Backdrop -->
      <div id="liveFeedModalBackdrop" class="lfm-backdrop" style="display:none;"></div>

      <!-- Live Feed Modal -->
      <div id="liveFeedModal"
          class="lfm-shell"
          role="dialog"
          aria-modal="true"
          aria-labelledby="liveFeedModalTitle"
          style="display:none;">

        <div class="lfm-header">
          <div>
            <h3 id="liveFeedModalTitle" class="lfm-title">Aktivitäten</h3>
            <div class="lfm-subtitle" id="liveFeedModalSubtitle">Kunde</div>
          </div>

          <div class="lfm-header-right">
            <div class="lfm-filters" id="liveFeedTypeFilters">
              <button type="button" class="lfm-filter-btn is-active" data-type="all">
                Alle
              </button>
              <button type="button" class="lfm-filter-btn" data-type="task">
                Aufgaben
              </button>
              <button type="button" class="lfm-filter-btn" data-type="appointment">
                Termine
              </button>
              <button type="button" class="lfm-filter-btn" data-type="ticket">
                Tickets
              </button>
            </div>

            <button type="button"
                    class="lfm-icon-btn"
                    id="liveFeedModalClose"
                    aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </div>

        <div class="lfm-body">
          <div class="lfm-meta">
            <span class="lfm-pill" id="liveFeedModalCount">0 Einträge</span>
            <span class="lfm-pill muted">
              <i class="feather icon-clock"></i>
              nach Nähe zu jetzt sortiert
            </span>
          </div>

          <div class="lfm-list" id="liveFeedModalList">
            <!-- Dynamisch gefüllt -->
          </div>
        </div>
      </div>

 

      <!-- Lead History Drawer -->
      <div id="lh-drawer" class="lh-root" aria-hidden="true" role="dialog" aria-labelledby="lh-title">
        <div class="lh-backdrop" data-lh-close></div>

        <aside class="lh-panel" tabindex="-1">
          <header class="lh-header">
            <h5 id="lh-title" class="mb-0">
              <i class="feather icon-activity mr-2"></i>
              <span id="lh-title-text">Verlauf</span>
            </h5>
            <button class="btn btn-sm btn-outline-secondary" data-lh-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </header>

          <section class="lh-body">
            <div class="row no-gutters">
              <div class="col-lg-7 pr-lg-2 border-right">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-trending-up mr-1"></i> Phasenverlauf</h6>
                  <ul id="lh-timeline" class="lh-timeline list-unstyled mb-0"></ul>
                </div>
              </div>
              <div class="col-lg-5 pl-lg-2">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-list mr-1"></i> Aktivitäten & Notizen</h6>
                  <div id="lh-activities" class="lh-list list-group"></div>
                </div>
              </div>
            </div>
          </section>
        </aside>
      </div>
 
      <div id="notesBackdrop" class="notes-backdrop"></div>
      <aside id="notesDrawer" class="notes-drawer" role="dialog" aria-modal="true" aria-labelledby="notesTitle">
          <div class="notes-head">
              <div class="notes-title">
                  <i class="feather icon-message-square"></i>
                  <span id="notesTitle">Kunden-Notizen</span>
                  <span id="notesCountBadge" class="badge badge-secondary" data-count="0">0</span>
              </div>
              <div>
                  <button class="btn btn-sm btn-outline-secondary" data-notes-close>
                      <i class="feather icon-x"></i>
                  </button>
              </div>
          </div>

          <div class="notes-tabs">
              <button type="button"
                      class="notes-tab notes-tab--active"
                      data-notes-tab="notes"
                      aria-selected="true">
                  <i class="feather icon-message-square mr-25"></i> Notizen
                  <span id="tabBadgeNotes" class="badge badge-light ml-25" style="background:#eef2f7; color:#333;">0</span>
              </button>
              
              <button type="button"
                      class="notes-tab"
                      data-notes-tab="customerReport"
                      aria-selected="false">
                  <i class="feather icon-bar-chart-2 mr-25"></i> Kunde Report
                  <span id="tabBadgeCustomerReport" class="badge badge-light ml-25 d-none" style="background:#eef2f7; color:#333;">0</span>
              </button>

              <button type="button"
                      class="notes-tab"
                      data-notes-tab="report"
                      aria-selected="false">
                  <i class="feather icon-bar-chart-2 mr-25"></i> Termin Report
                  <span id="tabBadgeTerminReport" class="badge badge-light ml-25 d-none" style="background:#eef2f7; color:#333;">0</span>
              </button>
          </div>

          <div class="notes-body">
              <div id="notesList" data-notes-panel="notes" aria-live="polite"></div>

              <div id="notesReport" data-notes-panel="report" class="d-none">
                  <div class="text-muted small p-2">
                      Report wird geladen, sobald der Tab „Report“ geöffnet wird.
                  </div>
              </div>

              <div id="customerReportList" data-notes-panel="customerReport" class="d-none">
                  <div class="text-muted small p-2">
                      Report wird geladen, sobald der Tab „Report“ geöffnet wird.
                  </div>
              </div>
          </div>

            <div class="notes-foot">
              <form id="notesForm" class="notes-composer">
                  {{-- Quill editor container --}}
                  <div id="noteEditor" class="notes-quill flex-grow-1"></div>

                  {{-- optional hidden field for fallback / future use --}}
                  <input type="hidden" id="noteText" />

                  <button class="btn btn-primary ml-50" type="submit">
                      <i class="feather icon-send"></i>
                  </button>
              </form>

              <input type="hidden" id="notesCustomerId">
              <input type="hidden" id="notesAlternativeId">
              <input type="hidden" id="notesProductId">
          </div>

      </aside>


          {{-- Appointment Drawer --}}
    <!-- BACKDROP -->
      <div id="ap-backdrop" class="notes-backdrop" data-ap-close></div>

      <!-- DRAWER -->
      <aside id="ap-drawer" class="notes-drawer ap-drawer" role="dialog" aria-modal="true" aria-labelledby="ap-title">
        <!-- Header -->
        <header class="notes-head ap-head">
          <div class="notes-title ap-head-left">
            <span class="ap-head-icon"><i class="feather icon-calendar"></i></span>
            <div class="ap-head-title">
              <div class="ap-head-row">
                <span id="ap-title" class="ap-title">Termine</span>
                <span id="ap-count" class="badge badge-secondary ml-2">0</span>
              </div>
              <div class="ap-head-sub text-muted small">
                Kalender • Liste • Mitarbeiter-Filter
              </div>
            </div>
          </div>

          <div class="ap-head-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-ap-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </header>

        <!-- Tabs -->
        <nav class="ap-tabs" role="tablist" aria-label="Termine Navigation">
          <button type="button" class="ap-tab-link active" data-tab="calendar" role="tab" aria-selected="true">
            <i class="feather icon-layout"></i>
            <span>Übersicht</span>
          </button>
          <button type="button" class="ap-tab-link" data-tab="form" role="tab" aria-selected="false">
            <i class="feather icon-plus-circle"></i>
            <span>Neu / Bearbeiten</span>
          </button>
        </nav>

        <!-- CONTENT -->
        <section class="ap-body">
          <!-- TAB: Calendar/List -->
          <div id="ap-tab-calendar" class="ap-tab-content active" role="tabpanel">
            <!-- Toolbar -->
            <div class="ap-toolbar border-bottom bg-white">
              <div class="ap-toolbar-left">
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary active" data-view="calendar">
                    <i class="feather icon-grid"></i> Kalender
                  </button>
                  <button type="button" class="btn btn-outline-secondary" data-view="cards">
                    <i class="feather icon-list"></i> Liste
                  </button>
                </div>
              </div>

              <div class="ap-toolbar-right">
                <!-- Employee Filter (search appointments by employee) -->
                <div class="ap-filter">
                  <label for="ap-emp-filter" class="ap-filter-label text-muted small d-none d-lg-inline">Mitarbeiter</label>
                  <select id="ap-emp-filter" class="form-control form-control-sm select2" style="width:100%">
                    <option value="">Alle Mitarbeiter</option>
                  </select>
                </div>

                <!-- Jump to appointment -->
                <div class="ap-filter">
                  <label for="ap-jump" class="ap-filter-label text-muted small d-none d-lg-inline">Schnellsuche</label>
                  <select id="ap-jump" class="form-control form-control-sm select2" style="width:100%">
                    <option value="">— Termin auswählen (Springen) —</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Calendar -->
            <div id="ap-calendar-wrap" class="ap-calendar-wrap">
              <div id="ap-fullcalendar" class="ap-fullcalendar"></div>
            </div>

            <!-- Cards/List -->
            <div id="ap-card-view" class="ap-card-view" style="display:none;">
              <div class="text-center text-muted small my-2">Keine Termine geladen.</div>
            </div>
          </div>

          <!-- TAB: Form -->
          <div id="ap-tab-form" class="ap-tab-content" role="tabpanel">
            <div class="p-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 id="ap-form-title" class="mb-0 font-weight-bold">Neuer Termin</h5>

                <button type="button" class="btn btn-sm btn-light border" id="ap-btn-back-to-cal">
                  <i class="feather icon-arrow-left"></i> Zurück
                </button>
              </div>

              <form id="ap-form" autocomplete="off">
                <input type="hidden" id="ap-id">
                <input type="hidden" id="ap-customer_id">
                <input type="hidden" id="ap-alternative_id">
                <input type="hidden" id="ap-product_id">

                <div class="form-group mb-2">
                  <label class="small mb-1 font-weight-bold">Titel*</label>
                  <input type="text" class="form-control" id="ap-name" required placeholder="z.B. Beratungsgespräch">
                </div>

                <div class="form-group mb-2">
                  <label class="small mb-1">Notiz / Beschreibung</label>
                  <textarea class="form-control" id="ap-note" rows="2"></textarea>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6 mb-2">
                    <label class="small mb-1">Datum*</label>
                    <input type="date" class="form-control" id="ap-start_date" required>
                  </div>
                  <div class="form-group col-3 mb-2">
                    <label class="small mb-1">Von</label>
                    <input type="time" class="form-control" id="ap-start_time">
                  </div>
                  <div class="form-group col-3 mb-2">
                    <label class="small mb-1">Bis</label>
                    <input type="time" class="form-control" id="ap-end_time">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-4 mb-2">
                    <label class="small mb-1">Art</label>
                    <select class="form-control" id="ap-appointment_type">
                      <option value="">–</option>
                      <option value="Besichtigung">Besichtigung</option>
                      <option value="Beratung">Beratung</option>
                      <option value="Telefonat">Telefonat</option>
                      <option value="Online-Meeting">Online-Meeting</option>
                    </select>
                  </div>
                  <div class="form-group col-md-4 mb-2">
                    <label class="small mb-1">Kontaktweg</label>
                    <select class="form-control" id="ap-contact_mode">
                      <option value="">–</option>
                      <option value="telefon">Telefon</option>
                      <option value="online">Online</option>
                      <option value="vor Ort">Vor Ort</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label class="small mb-1">Prio</label>
                    <select class="form-control" id="ap-priority">
                      <option value="normal">Normal</option>
                      <option value="high">Hoch</option>
                      <option value="low">Niedrig</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2 mb-2">
                    <label class="small mb-1">Farbe</label>
                    <input type="color" class="form-control" id="ap-color" value="#74b2d4" style="height:36px; padding:2px;">
                  </div>
                </div>

                <div class="form-group mb-3">
                  <label class="small mb-1 font-weight-bold">Mitarbeiter zuweisen</label>
                  <select id="ap-employee_ids" class="form-control select2" multiple style="width:100%">
                    @foreach ($employees as $e)
                      <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="p-2 bg-light border rounded mb-3">
                  <label class="small text-muted text-uppercase font-weight-bold mb-1">Adresse / Ort</label>
                  <input type="text" class="form-control mb-1 form-control-sm" id="ap-full_address" placeholder="Adresse suchen...">
                  <div class="form-row">
                    <div class="col-8 mb-1">
                      <input type="text" id="ap-street" class="form-control form-control-sm" placeholder="Straße">
                    </div>
                    <div class="col-4 mb-1">
                      <input type="text" id="ap-postcode" class="form-control form-control-sm" placeholder="PLZ">
                    </div>
                  </div>
                  <input type="text" class="form-control mb-1 form-control-sm" id="ap-city" placeholder="Ort">
                  <input type="hidden" id="ap-latitude">
                  <input type="hidden" id="ap-longitude">
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                  <button type="button" class="btn btn-outline-danger d-none" id="ap-btn-delete">
                    <i class="feather icon-trash-2"></i> Löschen
                  </button>
                  <button type="submit" class="btn btn-primary ml-auto">
                    <i class="feather icon-save"></i> Speichern
                  </button>
                </div>
              </form>
            </div>
          </div>
        </section>
      </aside>

      <style>
        /* Layout helpers (safe; no theme override) */
        .ap-drawer { display:flex; flex-direction:column; }
        .ap-body { flex: 1 1 auto; min-height: 0; display:flex; flex-direction:column; }
        .ap-tab-content { flex: 1 1 auto; min-height: 0; }
        .ap-toolbar { padding: 10px 12px; display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
        .ap-toolbar-right { display:flex; gap:10px; align-items:center; justify-content:flex-end; flex-wrap:wrap; }
        .ap-filter { display:flex; gap:6px; align-items:center; }
        .ap-filter select { min-width: 260px; }
        .ap-calendar-wrap { height: calc(100vh - 220px); min-height: 420px; padding: 8px; }
        .ap-fullcalendar { height: 100%; }
        .ap-card-view { padding: 12px; display: grid; grid-template-columns: 1fr; gap: 10px; }
      </style>

 
      <div id="pt-backdrop" class="notes-backdrop"></div>
      <aside id="pt-drawer" class="notes-drawer" role="dialog" aria-modal="true" aria-labelledby="pt-title" style="width:1300px !important;">
        <div class="notes-head">
          <div class="notes-title">
            <i class="feather icon-check-square"></i>
            <span id="pt-title">Aufgaben</span>
            <span id="pt-count" class="badge badge-secondary">0</span>
          </div>
          <div>
            <button class="btn btn-sm btn-outline-secondary" data-pt-close><i class="feather icon-x"></i></button>
          </div>
  
        </div>

        <div class="notes-body" id="pt-list" style="background:#f8fafc">
          <div class="text-center text-muted my-2">Lade Aufgaben…</div>
        </div>

        <div class="notes-foot">
          <form id="pt-form" class="notes-composer" autocomplete="off">
            <div class="w-100">
              <input class="form-control mb-1" id="pt-task_title" placeholder="Aufgabentitel*" required>
              <textarea class="form-control mb-1" id="pt-description" placeholder="Beschreibung (optional)"></textarea>
              <div class="d-flex flex-wrap gap-2">
                <input type="date" class="form-control mr-1 mb-1" id="pt-start_date" style="max-width:180px">
                <input type="date" class="form-control mr-1 mb-1" id="pt-due_date" style="max-width:180px">
                <input type="time" class="form-control mr-1 mb-1" id="pt-due_time" style="max-width:140px">
                <select class="form-control mr-1 mb-1" id="pt-priority" style="max-width:150px">
                  <option value="normal">Normal</option>
                  <option value="high">Hoch</option>
                  <option value="low">Niedrig</option>
                </select>
                <input type="color" class="form-control mb-1" id="pt-color" value="#8fc73e" style="max-width:70px; padding:0 2px;">
              </div>

              {{-- Hide this whole block when steps are used --}}
              <div id="pt-employee-wrap" class="mt-1">
                <label class="small text-muted mb-1">Mitarbeiter (für gesamte Aufgabe)</label>
                <select id="pt-employee_ids" class="form-control select2" multiple data-width="100%">
                  @foreach ($employees as $e)
                    <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Steps UI --}}
              <div class="border rounded p-2 mt-2 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Arbeitsschritte</strong>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="pt-add-step"><i class="feather icon-plus"></i> Schritt</button>
                </div>
                <div id="pt-steps" class="mt-2"></div>
                <small class="text-muted d-block mt-1">Wenn mindestens ein Schritt existiert, wird die Mitarbeiterauswahl der Hauptaufgabe ausgeblendet und pro Schritt vergeben.</small>
              </div>
            </div>
            <button class="btn btn-primary ml-2"><i class="feather icon-save"></i></button>
          </form>

          {{-- Hidden context from Kanban card --}}
          <input type="hidden" id="pt-customer_id">
          <input type="hidden" id="pt-alternative_id">
          <input type="hidden" id="pt-product_id">
        </div>
      </aside>
 
  </div><!-- /content-wrapper -->
</div>


 
<div id="kbTaskModalBackdrop" class="kb-task-backdrop" aria-hidden="true"></div>

<div id="kbTaskModal" class="kb-task-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kbTaskModalTitle">
  <div class="kb-task-modal-header">
    <div>
      <div class="kb-task-modal-title" id="kbTaskModalTitle">
        <i class="feather icon-list"></i>
        Aufgabenmanagement
      </div>
      <div class="kb-task-modal-sub" id="kbTaskContextText">Kunde • Objekt • Produkt • Stage</div>
    </div>

    <button type="button" class="kb-task-close" id="kbTaskModalClose" aria-label="Schließen">×</button>
  </div>

  <div class="kb-task-toolbar">
    <input type="search" id="kbTaskSearch" class="kb-task-search" placeholder="Aufgabe suchen …">

    <select id="kbTaskStatusFilter" class="kb-task-filter">
      <option value="">Alle Status</option>
      <option value="open">Offen</option>
      <option value="scheduled">Geplant</option>
      <option value="in_progress">In Bearbeitung</option>
      <option value="done">Erledigt</option>
      <option value="cancelled">Abgebrochen</option>
    </select>

    <button type="button" class="kb-task-primary" id="kbManualTaskBtn">
      <i class="feather icon-plus"></i>
      Manuelle Aufgabe
    </button>
  </div>

  <div id="kbTaskSequenceSummary" class="kb-task-sequence-summary">
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-log-in"></i> Seit Stage-Start</div>
      <div class="kb-task-seq-value" id="kbTaskSeqLanded">-</div>
      <div class="kb-task-seq-muted">Zeitpunkt, seit dem diese Karte in der aktuellen Stage liegt.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-check-circle"></i> Vorherige Aufgabe</div>
      <div class="kb-task-seq-value" id="kbTaskSeqPrevious">-</div>
      <div class="kb-task-seq-muted">Letzte erledigte Aufgabe.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-activity"></i> Aktuelle / eingehende Aufgabe</div>
      <div class="kb-task-seq-value" id="kbTaskSeqCurrent">-</div>
      <div class="kb-task-seq-muted">Nächste offene Aufgabe in dieser Stage/Sub-Stage.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</div>
      <div class="kb-task-seq-value" id="kbTaskSeqNext">-</div>
      <div class="kb-task-seq-muted">Folgeschritt gemäß Task-Phase-Sequenz.</div>
    </div>
  </div>

  <div class="kb-task-body">
    <div class="kb-task-column">
      <div class="kb-task-section-title">
        <i class="feather icon-layers"></i>
        Aufgaben aus aktueller Stage / Sub-Stage
      </div>
      <div id="kbTaskTemplates" class="kb-task-list">
        <div class="kb-task-empty">Aufgaben werden erst beim Öffnen geladen.</div>
      </div>
    </div>

    <div class="kb-task-column">
      <div class="kb-task-section-title">
        <i class="feather icon-check-square"></i>
        Erledigt / Nächste Aktion
      </div>
      <div id="kbTaskSaved" class="kb-task-list">
        <div class="kb-task-empty">Noch keine Aufgabe gespeichert.</div>
      </div>
    </div>
  </div>
</div>

<div id="kbTaskFormBackdrop" class="kb-task-form-backdrop" aria-hidden="true"></div>

<div id="kbTaskFormModal" class="kb-task-form-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kbTaskFormTitle">
  <div class="kb-task-form-header">
    <strong id="kbTaskFormTitle">Aufgabe planen</strong>
    <button type="button" class="kb-task-close" id="kbTaskFormClose" aria-label="Schließen">×</button>
  </div>

  <form id="kbTaskForm" autocomplete="off">
    @csrf
    <input type="hidden" id="kbFormLeadProductListId">
    <input type="hidden" id="kbFormTaskPhaseId">
    <input type="hidden" id="kbFormPhaseActivityId">
    <input type="hidden" id="kbFormExistingTaskId">
    <input type="hidden" id="kbFormMode" value="manual">

    <div class="kb-task-field">
      <label>Titel</label>
      <input type="text" id="kbFormTitle" required placeholder="z. B. Kunde anrufen / Montage vorbereiten">
    </div>

    <div class="kb-task-field">
      <label>Beschreibung</label>
      <textarea id="kbFormDescription" rows="3" placeholder="Was muss erledigt werden?"></textarea>
    </div>

    <div class="kb-task-grid">
      <div class="kb-task-field">
        <label>Start</label>
        <input type="datetime-local" id="kbFormStart">
      </div>

      <div class="kb-task-field">
        <label>Ende</label>
        <input type="datetime-local" id="kbFormEnd">
      </div>
    </div>

    <div class="kb-task-grid">
      <div class="kb-task-field">
        <label>Geschätzte Minuten</label>
        <input type="number" id="kbFormMinutes" min="1" placeholder="z. B. 60">
      </div>

      <div class="kb-task-field">
        <label>Performer</label>
        <select id="kbFormPerformer" class="kb-task-select2"></select>
      </div>
    </div>

    <div class="kb-task-field">
      <label>
        <input type="checkbox" id="kbFormScheduled">
        Aufgabe ist geplant / terminiert
      </label>
    </div>

    <div class="kb-task-field">
      <label>Weitere Mitarbeiter, die diese Aufgabe machen können</label>
      <select id="kbFormEmployees" multiple class="kb-task-select2"></select>
    </div>

    <div class="kb-task-convert-box">
      <label class="kb-task-check">
          <input type="checkbox" id="kbFormCreatePersonalTask">
          <span>Auch als persönliche Aufgabe erstellen</span>
      </label>

      <label class="kb-task-check">
          <input type="checkbox" id="kbFormCreateAppointment">
          <span>Auch als Termin erstellen</span>
      </label>

      <div id="kbAppointmentOptions" class="kb-appointment-options d-none">
          <div class="row">
              <div class="col-md-4">
                  <label>Terminart</label>
                  <select id="kbFormAppointmentType" class="form-control">
                      <option value="kanban_task">Kanban Aufgabe</option>
                      <option value="customer_appointment">Kundentermin</option>
                      <option value="internal">Intern</option>
                      <option value="phone">Telefon</option>
                      <option value="online">Online</option>
                  </select>
              </div>

              <div class="col-md-4">
                  <label>Kontaktart</label>
                  <select id="kbFormAppointmentContactMode" class="form-control">
                      <option value="">Keine</option>
                      <option value="phone">Telefon</option>
                      <option value="email">E-Mail</option>
                      <option value="onsite">Vor Ort</option>
                      <option value="online">Online</option>
                  </select>
              </div>

              <div class="col-md-4">
                  <label>Priorität</label>
                  <select id="kbFormAppointmentPriority" class="form-control">
                      <option value="normal">Normal</option>
                      <option value="high">Hoch</option>
                      <option value="urgent">Dringend</option>
                  </select>
              </div>
          </div>
      </div>
  </div>

    <div class="kb-task-field">
      <label>Interne Beschreibung / Ablauf</label>
      <textarea id="kbFormInternalNote" rows="3" placeholder="Interne Hinweise: wie die Arbeit gemacht werden soll"></textarea>
    </div>

    <div class="kb-task-form-actions">
      <button type="button" class="kb-task-secondary" id="kbTaskFormCancel">Abbrechen</button>
      <button type="submit" class="kb-task-primary">
        <i class="feather icon-save"></i>
        Speichern
      </button>
    </div>
  </form>
</div>
@stop


<div id="leadReminderToastWrap" class="lead-reminder-toast-wrap" aria-live="polite" aria-atomic="false"></div>

 @section('script')
    <script>
      window.KB_DND_MIME = window.KB_DND_MIME || 'application/x-leadui-cards';
    </script>

          @php
$leadStageNamesForJs = $stageNames ?? [
  'lead' => 'Lead',
  'offer' => 'Angebot',
  'follow_up' => 'Nachfassen',
  'accepted' => 'Annehmen',
  'deal' => 'Auftrag',
  'project' => 'Montage',
  'completed' => 'Abschluss',
  'archive' => 'Archive',
  'junk' => 'Junk',
];

$leadStageMetaForJs = $stageMeta ?? [];

// Kanban columns must not show Junk/Ticket because those have their own tabs.
$kanbanStageNamesForJs = collect($leadStageNamesForJs)
  ->reject(fn($label, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanStageMetaForJs = collect($leadStageMetaForJs)
  ->reject(fn($meta, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();
          @endphp

          <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
          <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
          <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/de.js"></script>
          <script src="{{ asset('js/select2.min.js') }}"></script>
          <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&language=de&region=DE">

          </script>


          <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
          <script>
              window.ALL_EMPLOYEES = @json($employees); 
          </script>

          <script>
            window.escapeHTML = window.escapeHTML || function(value) {
              return String(value ?? '').replace(/[&<>"']/g, function(m) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
              });
            };
            window.featherRefreshSoon = window.featherRefreshSoon || function() {
              requestAnimationFrame(function(){ if (window.feather && typeof window.feather.replace === 'function') window.feather.replace(); });
            };
          </script>

          <script>
          /* =============================================================================
           * LeadUI – Core (Segment 1/2)
           * - Config, State, Storage, URL Sync
           * - Utilities + Polyfills
           * - Network layer: safeFetchJSON / postJSON
           * - Filters + Drawer
           * - Kanban renderers
           * - Notes drawer
           * - Junk partial loaders
           * - LiveFeed: per-card mini feed + full-screen modal (LiveFeedModal)
           * =============================================================================*/
          (function () {
            "use strict";

            /* --- Polyfills --- */
            window.requestIdleCallback ||= (cb) => setTimeout(() => cb({ timeRemaining: () => 10 }), 0);

            if (!window.CSS || !CSS.escape) {
              window.CSS = {
                ...(window.CSS || {}),
                escape: (s) => String(s).replace(/[^a-zA-Z0-9_\-]/g, "\\$&"),
              };
            }

            /* --- Config --- */
            const APP = {
              EMP_SRC: "{{ asset('images/employee') }}",
              endpoints: {
                kanbanSearch: "/lead/kanban/search", 
                listSearch: "/lead/kanban/ajax", 
                changeStage: "/lead-product/change-stage", 
                progress: "/lead-product/progress", 
                purge: "/lead-product/purge", 

                notesIndex: "/customer-notes", 
                notesStore: "/customer-notes", 
                notesInlineUpdate: (id) => `/customer-notes/inline-update/${id}`, 
                notesDestroy: (id) => `/customer-notes/delete/${id}`, 

                junk: "/lead/kanban/junk", 

                personalTasksIndex: "/personal-tasks/index", 
                personalTasksStore: "/personal-tasks/store", 
                personalTasksUpdate: (id) => `/personal-tasks/${id}/update`, 
                personalTasksDestroy: (id) => `/personal-tasks/${id}/destroy`, 
                ptEmployeesSync: (id) => `/personal-tasks/${id}/employees/sync`, 

                ptStepsIndex: (taskId) => `/personal-tasks/${taskId}/steps`, 
                ptStepsStore: (taskId) => `/personal-tasks/${taskId}/steps`, 
                ptStepsUpdate: (stepId) => `/personal-tasks/steps/${stepId}`, 
                ptStepsDestroy: (stepId) => `/personal-tasks/steps/${stepId}`, 
                ptStepsEmpSync: (stepId) => `/personal-tasks/steps/${stepId}/employees/sync`, 

                ticketize: (id) => `/lead-product/ticketize/${id}`,
                tickets: "/lead/kanban/tickets", 

                appointmentsIndex: "appointments/index", 
                appointmentsStore: "appointments/store", 
                appointmentsUpdate: (id) => `appointments/${id}/update`, 
                appointmentsDestroy: (id) => `appointments/${id}/destroy`, 
                appointmentsCustomerSearch: "appointments/customer-search", 

                reportsIndex: "{{ url('kanban/appointments/reports') }}",
                reportsReact: (id) => "{{ url('kanban/appointments/reports') }}/" + id + "/react",
                reportsComment: (id) => "{{ url('kanban/appointments/reports') }}/" + id + "/comment",
                reportsStore: (appointmentId) => "{{ url('kanban/appointments') }}/" + appointmentId + "/reports",

                customerReportsIndex: "{{ url('kanban/customer-reports') }}", 
                customerReportsStore: "{{ url('kanban/customer-reports') }}", 
                customerReportsComment: (id) => "{{ url('kanban/customer-reports') }}/" + id + "/comment",

                liveFeed: "{{ url('/lead/kanban/feed') }}",

                remindersStore: "{{ url('/kanban/reminders') }}",
                remindersCardsSummary: "{{ url('/kanban/reminders/cards-summary') }}",

                leadStagesIndex: "{{ url('/admin/lead-stages') }}",
                leadStagesStore: "{{ url('/admin/lead-stages') }}",
                leadStagesUpdate: (id) => `{{ url('/admin/lead-stages') }}/${id}`,
                leadStagesDestroy: (id) => `{{ url('/admin/lead-stages') }}/${id}`,
                leadStagesReorder: "{{ url('/lead-stages/reorder') }}",
                leadStageSubStagesIndex: (id) => `{{ url('/admin/kanban/stages') }}/${id}/sub-stages`,
                updateLeadSubStage: (id) => `{{ url('/kanban/lead-product') }}/${encodeURIComponent(id)}/sub-stage`,

                stageWorkflowConfig: "{{ url('/kanban-stage-workflow/config') }}",
                stageWorkflowMove: (id) => `{{ url('/kanban-stage-workflow/move') }}/${id}`,
                stageWorkflowMoveNext: (id) => `{{ url('/kanban-stage-workflow/move-next') }}/${id}`,
              },
              stageNames: @json($leadStageNamesForJs),
              stageMeta: @json($leadStageMetaForJs),
              kanbanStageNames: @json($kanbanStageNamesForJs),
              kanbanStageMeta: @json($kanbanStageMetaForJs),
              companyKanbanStageNames: @json($kanbanStageNamesForJs),
              companyKanbanStageMeta: @json($kanbanStageMetaForJs),
              products: @json($kanbanProductsForJs),
              stageWorkflow: {
                mode: "company",
                productId: null,
                productStages: [],
                productStageMeta: {},
                productStageNames: {},
                previousProductFilter: undefined,
              },
              stageAlias: {
                open: "lead",
                neue: "lead",
                new: "lead",
                Lead: "lead",
                angebot: "offer",
                offer: "offer",
                nachfassen: "follow_up",
                follow_up: "follow_up",
                annehmen: "accepted",
                accepted: "accepted",
                accept: "accepted",
                auftrag: "deal",
                deal: "deal",
                montage: "project",
                project: "project",
                abschluss: "completed",
                complete: "completed",
                completed: "completed",
                archiv: "archive",
                archive: "archive",
                reject: "junk",
                rejeck: "junk",
                junk: "junk",
              },
              defaults: {
                sort: { key: "created_at", dir: "desc" },
                page: 1,
              },
              authUserId: "{{ auth()->user()->name ?? '' }}",
            };

            window.APP = APP;
            window.KanbanAPP = APP;


            function refreshEnterpriseKanbanRealtime() {
              try {
                if (window.LeadUI?.silentRefreshBoth) {
                  window.LeadUI.silentRefreshBoth();
                  return;
                }
                if (typeof window.LeadUIFetchKanban === 'function') {
                  window.LeadUIFetchKanban(State?.filtersQS || buildFilterQS?.() || '');
                }
              } catch (e) {
                console.warn('Realtime refresh failed', e);
              }
            }

            function initEnterpriseOfferConsistencyRealtime() {
              if (typeof window.Echo === 'undefined' || window.__enterpriseOfferConsistencyRealtime) return;
              window.__enterpriseOfferConsistencyRealtime = true;

              const handle = (event) => {
                const type = String(event?.type || event?.action || '').toLowerCase();
                if (
                  type.includes('kanban_offer_consistency') ||
                  type.includes('accepted_from_kanban') ||
                  type.includes('auto_cancelled_by_kanban') ||
                  type.includes('offer_sub_stage_synced_from_kanban') ||
                  type.includes('deal_sub_stage_synced_from_kanban')
                ) {
                  refreshEnterpriseKanbanRealtime();
                }
              };

              try {
                window.Echo.channel('offers')
                  .listen('OffersChanged', handle)
                  .listen('.OffersChanged', handle)
                  .listen('OfferFolderUpdated', handle)
                  .listen('.OfferFolderUpdated', handle);
              } catch (e) {
                console.warn('Offer realtime channel unavailable', e);
              }
            }

            initEnterpriseOfferConsistencyRealtime();

            const RUN = {
              badgeTone: { playing: "success", paused: "warning", stopped: "danger" },
              icon: { playing: "icon-play", paused: "icon-pause", stopped: "icon-square" },
              label: { playing: "Aktiv", paused: "Pausiert", stopped: "Gestoppt" },
            };

            /* --- Quill for Notes --- */
            let noteQuill = null;
            function ensureNoteQuill() {
                if (typeof window.Quill === "undefined") return null;
                if (noteQuill) return noteQuill;

                let editorHost = document.getElementById("noteEditor");
                const textarea = document.getElementById("noteText");

                if (!editorHost && textarea) {
                    editorHost = document.createElement("div");
                    editorHost.id = "noteEditor";
                    textarea.parentNode.insertBefore(editorHost, textarea);
                    textarea.style.display = "none";
                }

                if (!editorHost) return null;

                noteQuill = new Quill("#" + editorHost.id, {
                    theme: "snow",
                    placeholder: "Neue Notiz schreiben …",
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['link']
                        ]
                    }
                });

                return noteQuill;
            }
            function getNoteEditorHTML() {
              const textarea = document.getElementById("noteText");
              if (noteQuill) {
                return (noteQuill.root.innerHTML || "").trim();
              }
              return (textarea?.value || "").trim();
            }
            function setNoteEditorHTML(html) {
              const textarea = document.getElementById("noteText");
              if (noteQuill) {
                noteQuill.root.innerHTML = html || "";
                try {
                  const len = noteQuill.getLength();
                  noteQuill.setSelection(len, len);
                } catch {}
              } else if (textarea) {
                textarea.value = html || "";
              }
            }

            /* --- State --- */
            const STORAGE_KEY = "leadOverview.filters.v4";
            const State = {
              sort: { ...APP.defaults.sort },
              page: APP.defaults.page,
              filtersQS: "",
              lastAppliedQS: "",
              lastKanbanData: [],
              loaded: { kanban: false, list: false },
              req: { kanban: null, list: null },
              statusGroup: null,
              selectedIds: new Set(),
            };

            /* --- Utils --- */
            const qs = (s, ctx = document) => ctx.querySelector(s);
            const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
            const CSRF = () => qs('meta[name="csrf-token"]')?.content || "";
            const isLikelyHTML = (t) => /^\s*</.test(t || "");
            const fmtDE = (v) => {
              try {
                return v ? new Date(v).toLocaleString("de-DE") : "";
              } catch {
                return "";
              }
            };

              const getDateAgeIndicator = (dateString, stage) => {
                // We removed the 'if (currentStage !== "lead") return;' check 
                // so this now runs for ALL columns.

                if (!dateString) return '';
                const targetDate = new Date(dateString);
                if (isNaN(targetDate.getTime())) return '';

                const now = new Date();
                const diffMs = now - targetDate;
                const diffHours = diffMs / (1000 * 60 * 60);

                let state = 'green';
                let title = 'Neu (Unter 24 Stunden)';

                if (diffHours > 48) {
                    state = 'red';
                    title = 'Überfällig (Älter als 48 Stunden)';
                } else if (diffHours > 24) {
                    state = 'orange';
                    title = 'Letzter Tag (Läuft in unter 24h ab)';
                }

                return `
                <div class="traffic-light-wrapper" title="${title}">
                    <span class="tl-dot tl-green ${state === 'green' ? 'is-active splash-green' : ''}"></span>
                    <span class="tl-dot tl-orange ${state === 'orange' ? 'is-active splash-orange' : ''}"></span>
                    <span class="tl-dot tl-red ${state === 'red' ? 'is-active splash-red' : ''}"></span>
                </div>`;
            };
            const featherRefreshSoon = () => {
              if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
            };
            const shortNum = (n) => {
              n = Number(n || 0);
              if (n < 1e3) return "" + n;
              if (n < 1e6) return (n / 1e3).toFixed(n % 1e3 ? 1 : 0).replace(/\.0$/, "") + "k";
              if (n < 1e9) return (n / 1e6).toFixed(n % 1e6 ? 1 : 0).replace(/\.0$/, "") + "M";
              return (n / 1e9).toFixed(n % 1e9 ? 1 : 0).replace(/\.0$/, "") + "B";
            };
            const canonicalStage = (s) => {
              const k = String(s || "").toLowerCase();
              if (k.startsWith("product_stage_")) return k;
              return APP.stageNames[k] ? k : APP.stageAlias[k] || "lead";
            };
            const escapeHTML = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));
            window.escapeHTML = escapeHTML;
            window.featherRefreshSoon = featherRefreshSoon;

            const branchSVG = (size = 14) => `
              <svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" style="vertical-align:-2px;">
                <path d="M3 21h18"/>
                <path d="M5 21V7a2 2 0 0 1 2-2h3v16"/>
                <path d="M10 21V4h7a2 2 0 0 1 2 2v15"/>
                <path d="M8 9h1"/>
                <path d="M8 12h1"/>
                <path d="M8 15h1"/>
                <path d="M13 9h1"/>
                <path d="M13 12h1"/>
                <path d="M13 15h1"/>
              </svg>
            `;

            function orderedStageEntries(namesObj) {
              const names = namesObj || {};
              const meta = APP.kanbanStageMeta || APP.stageMeta || {};
              return Object.entries(names).sort((a, b) => {
                const ao = Number(meta?.[a[0]]?.sort_order ?? 999999);
                const bo = Number(meta?.[b[0]]?.sort_order ?? 999999);
                if (ao !== bo) return ao - bo;
                return String(a[1]).localeCompare(String(b[1]), "de");
              });
            }
            window.orderedStageEntries = orderedStageEntries;

            const STAGE_ORDER = orderedStageEntries(APP.stageNames || {}).map(([key]) => key);
            const stageRank = (s) => STAGE_ORDER.indexOf(canonicalStage(s));
            const isBackward = (from, to) => stageRank(to) < stageRank(from);

            function enforceActionVisibility(cardOrStage) {
              const cards = cardOrStage && cardOrStage.nodeType === 1 ? [cardOrStage] : Array.from(document.querySelectorAll(".card"));
              cards.forEach((c) => {
                const stage = canonicalStage(c.dataset.stage || c.closest(".column")?.id || "lead");
                const hideJunk = stageRank(stage) >= stageRank("deal"); 
                const junkBtn = c.querySelector('[data-act="delete"]');
                if (junkBtn) {
                  junkBtn.disabled = hideJunk;
                  junkBtn.classList.toggle("d-none", hideJunk);
                  junkBtn.setAttribute("aria-hidden", hideJunk ? "true" : "false");
                }
              });
            }

            function stageFilterExcludes(newStage) {
              const p = new URLSearchParams(State.filtersQS || "");
              const f = p.get("stage");
              if (!f) return false;
              return canonicalStage(f) !== canonicalStage(newStage);
            }

            function saveToLocal() {
              try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                  sort: State.sort,
                  page: State.page,
                  filtersQS: State.filtersQS,
                  statusGroup: State.statusGroup,
                }));
              } catch {}
            }

            function restoreFromLocal() {
              try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) return;
                const { sort, page, filtersQS, statusGroup } = JSON.parse(raw);
                if (sort?.key && sort?.dir) State.sort = sort;
                if (page) State.page = Number(page) || 1;
                if (typeof filtersQS === "string") State.filtersQS = filtersQS;
                if (statusGroup === null || ["offen", "zusage", "absage"].includes(statusGroup)) State.statusGroup = statusGroup;
              } catch {}
            }

            function syncURL() {
              const url = new URL(location.href);
              const p = new URLSearchParams(State.filtersQS || "");
              p.set("sort_by", State.sort.key);
              p.set("sort_dir", State.sort.dir);
              p.set("page", String(State.page));
              const newQS = p.toString();
              if (url.search.slice(1) !== newQS) {
                url.search = newQS;
                history.replaceState(null, "", url.toString());
              }
            }

            function initFromURL() {
              const p = new URLSearchParams(location.search);
              const form = qs("#kanbanFilterForm");
              if (form && p.size) {
                p.forEach((v, k) => {
                  const el = form.elements[k];
                  if (el) {
                    try { el.value = v; } catch {}
                  }
                });
                if (window.jQuery) {
                  jQuery(form).find(".select2").each(function () {
                    const name = this.getAttribute("name");
                    if (name && p.has(name)) jQuery(this).val(p.get(name)).trigger("change");
                  });
                }
              }
              State.sort.key = p.get("sort_by") || State.sort.key;
              State.sort.dir = (p.get("sort_dir") || State.sort.dir).toLowerCase() === "asc" ? "asc" : "desc";
              State.page = parseInt(p.get("page") || State.page, 10) || 1;
              State.filtersQS = buildFilterQS();
            }

            /* --- Networking --- */
            function cancel(key) {
              try { State.req[key]?.abort(); } catch {}
              State.req[key] = new AbortController();
              return State.req[key].signal;
            }
            async function safeFetchJSON(url, { method = "GET", headers = {}, body, signal, retries = 0, retryDelay = 240 } = {}) {
              const go = async () => {
                const res = await fetch(url, {
                  method, credentials: "same-origin",
                  headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", ...headers },
                  body, signal,
                });
                const text = await res.text();
                let data = {};
                try { data = text ? JSON.parse(text) : {}; } catch { data = { message: text || `HTTP ${res.status} ${res.statusText}` }; }

                // Business decision from backend: not a technical error.
                // Example: the user must select which offer folder is accepted before leaving Angebot.
                if (data?.requires_offer_selection) {
                  return data;
                }

                if (!res.ok || isLikelyHTML(text) || data?.success === false) {
                  const message = data?.message || data?.help_text || `HTTP ${res.status} ${res.statusText}`;
                  const error = new Error(message);
                  error.status = res.status;
                  error.payload = data;
                  throw error;
                }

                return data;
              };
              try {
                return await go();
              } catch (err) {
                if (retries > 0 && method === "GET") {
                  await new Promise((r) => setTimeout(r, retryDelay));
                  return safeFetchJSON(url, { method, headers, body, signal, retries: retries - 1, retryDelay: retryDelay * 1.6 });
                }
                throw err;
              }
            }
            const postJSON = (url, payload = {}) =>
              safeFetchJSON(url, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() },
                body: JSON.stringify(payload),
              });

            function safeJSON(value, fallback = null) {
              if (value === null || value === undefined || value === '') return fallback;
              if (typeof value !== 'string') return value;
              try {
                return JSON.parse(value);
              } catch (error) {
                return fallback;
              }
            }

            /* --- Company/Product workflow stage support --- */
            function workflowColumnKey(item) {
              if (APP.stageWorkflow?.mode === "product") {
                const psId = Number(item?.product_stage_id || item?.productStageId || 0);
                return psId > 0 ? `product_stage_${psId}` : Object.keys(APP.stageWorkflow.productStageNames || {})[0] || "lead";
              }
              return canonicalStage(item?.stage || "lead");
            }

            function workflowLabel(key) {
              if (APP.stageWorkflow?.mode === "product") {
                return APP.stageWorkflow.productStageNames?.[key] || key;
              }
              return APP.stageNames?.[canonicalStage(key)] || key;
            }

            function workflowStageIdFromKey(key) {
              const m = String(key || "").match(/^product_stage_(\d+)$/);
              return m ? Number(m[1]) : null;
            }
            window.workflowColumnKey = workflowColumnKey;
            window.workflowLabel = workflowLabel;
            window.workflowStageIdFromKey = workflowStageIdFromKey;

            function initWorkflowProductSelect2() {
              if (!window.jQuery || !window.jQuery.fn.select2) return;
              const $sel = window.jQuery("#kbWorkflowProduct");
              if (!$sel.length) return;
              if ($sel.hasClass("select2-hidden-accessible")) $sel.select2("destroy");

              const formatProduct = (option) => {
                if (!option.id) return option.text;
                const el = option.element;
                const initial = el?.dataset?.initial || "";
                const name = el?.dataset?.name || option.text || "";
                return window.jQuery(`
                  <span class="kb-workflow-select2-option">
                    <span class="kb-workflow-select2-icon"><i class="feather icon-box"></i></span>
                    <span class="kb-workflow-select2-text">
                      <span class="kb-workflow-select2-title">${escapeHTML(name || option.text)}</span>
                      <span class="kb-workflow-select2-sub">${escapeHTML(initial ? ('Kürzel: ' + initial) : 'Produkt-Workflow')}</span>
                    </span>
                  </span>
                `);
              };

              $sel.select2({
                placeholder: "Produkt für Workflow wählen…",
                allowClear: true,
                width: "260px",
                dropdownParent: window.jQuery(document.body),
                templateResult: formatProduct,
                templateSelection: formatProduct,
                escapeMarkup: (m) => m,
              });

              setTimeout(() => { if (window.feather) window.feather.replace(); }, 30);
            }

            function syncWorkflowProductSelect(productId = null) {
              const productSelect = document.getElementById("kbWorkflowProduct");
              if (!productSelect) return;
              productSelect.disabled = APP.stageWorkflow.mode !== "product";
              if (productId !== null && productId !== undefined) productSelect.value = String(productId || "");
              if (window.jQuery && window.jQuery.fn.select2) {
                window.jQuery(productSelect).prop("disabled", productSelect.disabled).trigger("change.select2");
              }
            }

            function refreshWorkflowBoardFromCache() {
              const board = qs("#kanban");
              if (board) board.innerHTML = "";
              ensureColumns();
              renderKanbanDiff(State.lastKanbanData || []);
              updateCounts();
              featherRefreshSoon();
              enforceActionVisibility();
            }

            function reloadKanbanAfterWorkflowSwitch() {
              State.page = 1;
              State.filtersQS = buildFilterQS();
              saveToLocal();
              syncURL();

              if (typeof window.LeadUIFetchKanban === "function") {
                State.loaded.kanban = false;
                return window.LeadUIFetchKanban(State.filtersQS);
              }

              refreshWorkflowBoardFromCache();
              return Promise.resolve();
            }

            function setWorkflowMode(mode, productId = null) {
              APP.stageWorkflow.mode = mode === "product" ? "product" : "company";
              APP.stageWorkflow.productId = productId ? Number(productId) : null;
              document.querySelectorAll("[data-kb-workflow-mode]").forEach((btn) => {
                btn.classList.toggle("is-active", btn.dataset.kbWorkflowMode === APP.stageWorkflow.mode);
              });
              syncWorkflowProductSelect(productId);
              const hint = document.getElementById("kbWorkflowHint");
              if (hint) hint.textContent = APP.stageWorkflow.mode === "product" ? "Produktphasen aktiv" : "Unternehmensphasen aktiv";
            }

            async function loadWorkflowColumns(mode = "company", productId = null) {
              const productFilter = qs("#productFilter");

              if (mode === "product") {
                if (!productId) {
                  setWorkflowMode("product", null);
                  Swal.fire("Produkt wählen", "Bitte wählen Sie zuerst ein Produkt für den Produkt-Workflow.", "info");
                  return false;
                }

                if (APP.stageWorkflow.previousProductFilter === undefined) {
                  APP.stageWorkflow.previousProductFilter = productFilter ? productFilter.value : "";
                }

                setWorkflowMode("product", productId);

                if (productFilter) {
                  productFilter.value = String(productId);
                  if (window.jQuery && window.jQuery.fn.select2) window.jQuery(productFilter).trigger("change.select2");
                }

                const url = `${APP.endpoints.stageWorkflowConfig}?mode=product&product_id=${encodeURIComponent(productId)}`;
                const res = await safeFetchJSON(url);
                if (!res?.success) {
                  Swal.fire("Fehler", res?.message || "Produktphasen konnten nicht geladen werden.", "error");
                  return false;
                }

                const names = {};
                const meta = {};
                (res.stages || []).forEach((stage, idx) => {
                  const key = `product_stage_${stage.id}`;
                  names[key] = stage.name || `Produktphase #${stage.id}`;
                  meta[key] = {
                    id: stage.id,
                    key,
                    color: stage.color || "#93c21c",
                    icon: stage.icon || "layers",
                    sort_order: Number(stage.sort_order ?? ((idx + 1) * 10)),
                    phases: Array.isArray(stage.phases) ? stage.phases : [],
                    product_id: stage.product_id,
                    section_name: stage.section_name || "",
                  };
                });

                APP.stageWorkflow.productStages = res.stages || [];
                APP.stageWorkflow.productStageNames = names;
                APP.stageWorkflow.productStageMeta = meta;
                APP.kanbanStageNames = names;
                APP.kanbanStageMeta = meta;

                refreshWorkflowBoardFromCache();
                return true;
              }

              // Unternehmen: restore the real company stages and restore/clear the product filter.
              setWorkflowMode("company", null);
              APP.kanbanStageNames = { ...(APP.companyKanbanStageNames || APP.stageNames || {}) };
              APP.kanbanStageMeta = { ...(APP.companyKanbanStageMeta || APP.stageMeta || {}) };
              APP.stageWorkflow.productStages = [];
              APP.stageWorkflow.productStageNames = {};
              APP.stageWorkflow.productStageMeta = {};

              if (productFilter) {
                const oldValue = APP.stageWorkflow.previousProductFilter;
                productFilter.value = oldValue !== undefined ? String(oldValue || "") : "";
                if (window.jQuery && window.jQuery.fn.select2) window.jQuery(productFilter).trigger("change.select2");
              }
              APP.stageWorkflow.previousProductFilter = undefined;

              refreshWorkflowBoardFromCache();
              return true;
            }

            function setWorkflowToolbarDraft(mode = "company") {
              const switchEl = qs("#kbWorkflowSwitch");
              const productBox = qs("#kbWorkflowProductBox");
              const productSelect = qs("#kbWorkflowProduct");
              const applyBtn = qs("#kbWorkflowApplyProduct");
              const hint = qs("#kbWorkflowHint");

              qsa("[data-kb-workflow-mode]").forEach((btn) => {
                btn.classList.toggle("is-active", btn.dataset.kbWorkflowMode === mode);
              });

              if (mode === "product") {
                switchEl?.classList.add("is-product-draft");
                productBox?.classList.remove("d-none");
                if (productSelect) productSelect.disabled = false;
                if (applyBtn) applyBtn.disabled = !productSelect?.value;
                if (hint) hint.textContent = "Produkt wählen und Anwenden klicken";
              } else {
                switchEl?.classList.remove("is-product-draft");
                productBox?.classList.add("d-none");
                if (productSelect) productSelect.disabled = true;
                if (applyBtn) applyBtn.disabled = true;
                if (hint) hint.textContent = "Unternehmensphasen aktiv";
              }

              if (window.jQuery && window.jQuery.fn.select2 && productSelect) {
                window.jQuery(productSelect).prop("disabled", productSelect.disabled).trigger("change.select2");
              }
              featherRefreshSoon();
            }

            async function applyWorkflowFromToolbar() {
              const productId = qs("#kbWorkflowProduct")?.value || null;
              if (!productId) {
                Swal.fire("Produkt wählen", "Bitte wählen Sie zuerst ein Produkt aus und klicken Sie dann auf Anwenden.", "info");
                return false;
              }

              const ok = await loadWorkflowColumns("product", productId);
              if (!ok) return false;

              qs("#kbWorkflowSwitch")?.classList.remove("is-product-draft");
              const hint = qs("#kbWorkflowHint");
              if (hint) hint.textContent = "Produktphasen aktiv";
              await reloadKanbanAfterWorkflowSwitch();
              return true;
            }

            function bindWorkflowControls() {
              initWorkflowProductSelect2();

              const productSelect = qs("#kbWorkflowProduct");
              const applyBtn = qs("#kbWorkflowApplyProduct");

              qsa("[data-kb-workflow-mode]").forEach((btn) => {
                btn.addEventListener("click", async (e) => {
                  e.preventDefault();
                  e.stopImmediatePropagation();

                  const mode = btn.dataset.kbWorkflowMode || "company";

                  if (mode === "product") {
                    setWorkflowToolbarDraft("product");
                    return;
                  }

                  const ok = await loadWorkflowColumns("company", null);
                  if (ok) {
                    setWorkflowToolbarDraft("company");
                    await reloadKanbanAfterWorkflowSwitch();
                  }
                }, true);
              });

              productSelect?.addEventListener("change", (e) => {
                const hasProduct = !!e.target.value;
                if (applyBtn) applyBtn.disabled = !hasProduct;
                const hint = qs("#kbWorkflowHint");
                if (hint) hint.textContent = hasProduct ? "Jetzt Anwenden klicken" : "Produkt wählen und Anwenden klicken";
              }, true);

              applyBtn?.addEventListener("click", async (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();
                await applyWorkflowFromToolbar();
              }, true);

              // Initial UI state: company mode, product select hidden until Product tab is clicked.
              setWorkflowToolbarDraft(APP.stageWorkflow?.mode === "product" ? "product" : "company");
            }

            window.KanbanWorkflow = Object.assign(window.KanbanWorkflow || {}, {
              initWorkflowProductSelect2,
              syncWorkflowProductSelect,
              setWorkflowMode,
              setWorkflowToolbarDraft,
              loadWorkflowColumns,
              applyWorkflowFromToolbar,
              reloadKanbanAfterWorkflowSwitch,
            });

            /* --- Filters/UI --- */
            function initSelect2(root = null) {
              if (!window.jQuery || !jQuery.fn.select2) return;
              const $root = root ? jQuery(root) : jQuery("#sideDrawer");

              function stageTemplate(option, mode = "option") {
                if (!option.id) return option.text;
                const el = option.element;
                const color = el?.dataset?.color || APP.stageMeta?.[option.id]?.color || "#93c21c";
                const icon = el?.dataset?.icon || APP.stageMeta?.[option.id]?.icon || "circle";
                const label = option.text || APP.stageNames?.[option.id] || option.id;
                return jQuery(`
                  <span class="stage-select2-${mode}">
                    <span class="stage-color-dot" style="background:${escapeHTML(color)}"></span>
                    <span class="stage-select2-icon"><i class="feather icon-${escapeHTML(icon)}"></i></span>
                    <span class="stage-select2-label">${escapeHTML(label)}</span>
                  </span>
                `);
              }

              $root.find(".select2").each(function () {
                const $el = jQuery(this);
                if ($el.hasClass("select2-hidden-accessible")) $el.select2("destroy");
                const isStage = this.id === "stageFilter" || $el.hasClass("stage-color-select");
                const $dropdownParent = $root.closest(".drawer, .notes-drawer, .modal").length
                  ? $root.closest(".drawer, .notes-drawer, .modal")
                  : jQuery(document.body);

                $el.select2({
                  placeholder: "Auswählen…",
                  allowClear: true,
                  width: "100%",
                  dropdownParent: $dropdownParent,
                  templateResult: isStage ? (option) => stageTemplate(option, "option") : undefined,
                  templateSelection: isStage ? (option) => stageTemplate(option, "selection") : undefined,
                  escapeMarkup: (m) => m,
                });
              });

              setTimeout(() => { if (window.feather) window.feather.replace(); }, 30);
            }

            function getFilterValues() {
              const f = qs("#kanbanFilterForm");
              if (!f) return {};
              const fd = new FormData(f), obj = {};
              fd.forEach((v, k) => (obj[k] = v === "" ? null : v));
              return obj;
            }

          function updateFilterBadges() {
              const vals = getFilterValues(); 
              const keys = ["customer", "stage", "employee", "department", "product", "interest", "date_from", "date_to", "lead_age"];
              const n = keys.reduce((t, k) => t + (vals[k] && String(vals[k]).trim() ? 1 : 0), 0) + (State.statusGroup ? 1 : 0);
              const rail = qs("#filterBadge");
              const tab = qs("#tabFilterCount");
              const btn = qs("#btnOpenDrawer");
              if (rail) { rail.textContent = n; rail.classList.toggle("d-none", !n); }
              if (tab) { tab.textContent = n; tab.classList.toggle("d-none", !n); }
              if (btn) btn.classList.toggle("rail-btn--active", !!n);
            }

            function buildFilterQS() {
              const form = qs("#kanbanFilterForm") || document.createElement("form");
              const p = new URLSearchParams(new FormData(form));
              if (State.statusGroup) {
                p.set("status_group", State.statusGroup);
                p.delete("stage");
                const stageSel = qs("#stageFilter");
                if (stageSel) stageSel.value = "";
              } else {
                p.delete("status_group");
              }
              p.set("sort_by", State.sort.key);
              p.set("sort_dir", State.sort.dir);
              p.delete("page");
              return p.toString();
            }

            const Drawer = (() => {
              const el = qs("#sideDrawer"), bd = qs("#drawerBackdrop");
              function open() {
                el?.classList.add("open");
                bd?.classList.add("show");
                document.body.style.overflow = "hidden";
                setTimeout(initSelect2, 10);
                updateFilterBadges();
              }
              function close() {
                el?.classList.remove("open");
                bd?.classList.remove("show");
                document.body.style.overflow = "";
              }
              bd?.addEventListener("click", close);
              qsa("[data-close-drawer]").forEach((b) => b.addEventListener("click", close));
              qs("#btnOpenDrawer")?.addEventListener("click", open);
              return { open, close };
            })();

            function closeOverlays() {
              qs("#drawerBackdrop")?.classList.remove("show");
              qs("#sideDrawer")?.classList.remove("open");
              qs("#notesBackdrop")?.classList.remove("show");
              qs("#notesDrawer")?.classList.remove("open");
              document.body.style.overflow = "";
            }

            /* --- Kanban DOM --- */
          /* --- Kanban DOM --- */
          function ensureColumns() {
              const board = qs("#kanban");
              if (!board) return;
              if (board.querySelector(".column")) return;

              const frag = document.createDocumentFragment();
              orderedStageEntries(APP.kanbanStageNames || APP.stageNames).forEach(([id, title]) => {
                const col = document.createElement("div");
                const meta = APP.kanbanStageMeta?.[id] || APP.stageMeta?.[id] || {};
                const stageDbId = meta.id || meta.stage_id || null;
                const subStages = Array.isArray(meta.sub_stages) ? meta.sub_stages : (Array.isArray(meta.subStages) ? meta.subStages : []);
                const subStageCount = subStages.length || Number(meta.sub_stage_count || meta.sub_stages_count || meta.subStageCount || 0);
                const subStageUrl = stageDbId && APP.endpoints?.leadStageSubStagesIndex
                  ? APP.endpoints.leadStageSubStagesIndex(stageDbId)
                  : "#";

                const safeTitle = escapeHTML(title || id);
                const safeId = escapeHTML(id);
                const safeIcon = escapeHTML(meta.icon || "columns");

                const underStageButtonHTML = `
                  <button type="button"
                          class="kb-understage-btn"
                          data-understage-stage="${safeId}"
                          title="Unterphasen von ${safeTitle} anzeigen">
                    <i class="feather icon-git-branch"></i>
                    <span>Unterphasen</span>
                    <b>${subStageCount}</b>
                  </button>`;

                const subStageConfigButtonHTML = `
                  <span class="kb-column-substage-wrap">
                    <a href="${escapeHTML(subStageUrl)}"
                       class="kb-column-substage-btn"
                       data-substage-config-link="1"
                       title="Unterphasen für ${safeTitle} konfigurieren">
                      <i class="feather icon-settings"></i>
                    </a>
                    <span class="kb-column-substage-count" title="${subStageCount} Unterphasen">${subStageCount}</span>
                  </span>`;

                col.className = "column";
                col.id = id;
                col.ondragover = (e) => e.preventDefault();

                col.innerHTML = `
                  <h3 data-workflow-stage-key="${safeId}">
                    <span class="kb-column-head-left">
                      <span class="kb-column-title"><i class="feather icon-${safeIcon}"></i> ${safeTitle}</span>
                    </span>
                    <span class="kb-column-actions">
                      ${underStageButtonHTML}
                      <button type="button"
                              class="kb-toggle-analytics"
                              data-kb-toggle-analytics="${safeId}"
                              title="Analyse-Badges ein-/ausblenden">
                        <i class="feather icon-bar-chart-2"></i>
                      </button>
                      ${subStageConfigButtonHTML}
                      <span class="kb-header-counts" data-count-for="${safeId}" aria-live="polite" title="Gesamt / Neu / 24-48 Std. / Über 48 Std.">
                        <span class="kb-count-pill kb-count-pill--total" title="Gesamt">0</span>
                        <span class="kb-count-pill kb-count-pill--green" title="Neu / Unter 24 Stunden"><span class="kb-count-dot"></span>0</span>
                        <span class="kb-count-pill kb-count-pill--orange" title="24 bis 48 Stunden"><span class="kb-count-dot"></span>0</span>
                        <span class="kb-count-pill kb-count-pill--red" title="Überfällig / Älter als 48 Stunden"><span class="kb-count-dot"></span>0</span>
                      </span>
                    </span>
                  </h3>
                  <div class="column-toolbar">
                    <input type="text" class="col-search-input" data-col="${safeId}" placeholder="In ${safeTitle} suchen...">
                    <button type="button" class="col-sort-btn" data-col="${safeId}" data-sort="desc" title="Nach Datum sortieren">
                      <i class="feather icon-arrow-down"></i>
                    </button>
                  </div>
                  <div class="column-content"></div>
                `;

                const header = col.querySelector("h3");
                if (header) header.style.background = (window.localStorage?.getItem("kb_use_stage_colors") === "1" && meta.color) ? meta.color : "#93c21c";
                frag.appendChild(col);
              });

              board.appendChild(frag);
              bindColumnTools();
              featherRefreshSoon();
            }

          document.addEventListener("click", function (event) {
            const link = event.target.closest("[data-substage-config-link]");
            if (!link) return;
            event.preventDefault();
            event.stopPropagation();
            const header = link.closest("h3[data-workflow-stage-key]");
            const stageKey = header?.dataset?.workflowStageKey || "";
            const meta = (APP.kanbanStageMeta?.[stageKey] || APP.stageMeta?.[stageKey] || {});
            const stageDbId = meta.id || meta.stage_id || null;
            const stageName = (APP.kanbanStageNames || APP.stageNames || {})[stageKey] || meta.name || stageKey || "Hauptphase";
            if (!stageDbId) {
              if (window.Swal) Swal.fire("Fehler", "Unterphasen-Link konnte nicht erstellt werden. Stage-ID fehlt.", "error");
              else alert("Unterphasen-Link konnte nicht erstellt werden. Stage-ID fehlt.");
              return;
            }
            if (typeof window.openLeadStageSubstageConfig === "function") {
              window.openLeadStageSubstageConfig(stageDbId);
            }
          });

          // Column search + sorting
          function bindColumnTools() {
              // 1. Search Logic (Filters cards by text)
              document.querySelectorAll('.col-search-input').forEach(input => {
                  input.addEventListener('input', function() {
                      const colId = this.dataset.col;
                      const term = this.value.toLowerCase().trim();
                      const container = document.querySelector(`#${colId} .column-content`);
                      if (!container) return;

                      const cards = container.querySelectorAll('.card');
                      cards.forEach(card => {
                          // Check all text inside the card (Name, City, Tags, etc.)
                          const cardText = card.innerText.toLowerCase();
                          card.style.display = cardText.includes(term) ? '' : 'none';
                      });

                      updateCounts();
                  });
              });

              // 2. Date Sorting Logic (Sorts cards up/down)
              document.querySelectorAll('.col-sort-btn').forEach(btn => {
                  btn.addEventListener('click', function() {
                      const colId = this.dataset.col;
                      const isDesc = this.dataset.sort === 'desc';

                      // Toggle state
                      this.dataset.sort = isDesc ? 'asc' : 'desc';
                      this.innerHTML = isDesc ? '<i class="feather icon-arrow-up"></i>' : '<i class="feather icon-arrow-down"></i>';

                      const container = document.querySelector(`#${colId} .column-content`);
                      if (!container) return;

                      const cards = Array.from(container.querySelectorAll('.card'));

                      // Sort cards based on the data-updated-at attribute you already set
                      cards.sort((a, b) => {
                          const dateA = new Date(a.dataset.updatedAt || 0).getTime();
                          const dateB = new Date(b.dataset.updatedAt || 0).getTime();

                          return isDesc ? (dateB - dateA) : (dateA - dateB); 
                      });

                      // Re-append sorted cards to the DOM (this physically moves them)
                      cards.forEach(card => container.appendChild(card));

                      updateCounts();
                  });
              });
          }
            function clearColumns() {
              qsa(".column .column-content").forEach((el) => (el.innerHTML = ""));
              qsa("#kanban > :not(.column)").forEach((n) => n.remove());
            }

            const colContent = (s) => qs(`#${CSS.escape(s)} .column-content`);

            function getCardTrafficLightState(card) {
              if (!card) return null;

              const wrapper = card.querySelector(".traffic-light-wrapper");
              if (!wrapper) return null;

              if (wrapper.querySelector(".tl-green.is-active")) return "green";
              if (wrapper.querySelector(".tl-orange.is-active")) return "orange";
              if (wrapper.querySelector(".tl-red.is-active")) return "red";

              return null;
            }

            function updateCounts() {
              qsa(".column").forEach((col) => {
                const cards = Array.from(col.querySelectorAll(".column-content .card"))
                  .filter((card) => card.style.display !== "none");

                const counts = {
                  total: cards.length,
                  green: 0,
                  orange: 0,
                  red: 0,
                };

                cards.forEach((card) => {
                  const state = getCardTrafficLightState(card);

                  if (state === "green") counts.green++;
                  else if (state === "orange") counts.orange++;
                  else if (state === "red") counts.red++;
                });

                const oldBadge = col.querySelector(".count-badge");
                if (oldBadge) oldBadge.textContent = String(counts.total);

                const wrap = col.querySelector(".kb-header-counts");
                if (!wrap) return;

                const totalEl = wrap.querySelector(".kb-count-pill--total");
                const greenEl = wrap.querySelector(".kb-count-pill--green");
                const orangeEl = wrap.querySelector(".kb-count-pill--orange");
                const redEl = wrap.querySelector(".kb-count-pill--red");

                if (totalEl) totalEl.textContent = shortNum(counts.total);
                if (greenEl) greenEl.innerHTML = '<span class="kb-count-dot"></span>' + shortNum(counts.green);
                if (orangeEl) orangeEl.innerHTML = '<span class="kb-count-dot"></span>' + shortNum(counts.orange);
                if (redEl) redEl.innerHTML = '<span class="kb-count-dot"></span>' + shortNum(counts.red);

                wrap.setAttribute(
                  "title",
                  `Gesamt: ${counts.total} / Neu: ${counts.green} / 24-48 Std.: ${counts.orange} / Über 48 Std.: ${counts.red}`
                );
              });
            }


            /* -------------------------------------------------------------------------- */
            /* Under Stage Board                                                           */
            /* -------------------------------------------------------------------------- */
            APP.underStage = APP.underStage || { active: false, stageKey: null };
            APP.allLeads = APP.allLeads || [];

            function showKanbanLoading(message = "Lade Unterphasen...") {
              let loader = document.getElementById("kb-understage-loader");
              if (!loader) {
                loader = document.createElement("div");
                loader.id = "kb-understage-loader";
                loader.className = "kb-understage-loader";
                loader.innerHTML = `<div class="kb-understage-spinner"></div><div class="kb-understage-loader-text"></div>`;
                document.body.appendChild(loader);
              }
              const text = loader.querySelector(".kb-understage-loader-text");
              if (text) text.textContent = message;
              loader.classList.add("show");
            }

            function hideKanbanLoading() {
              document.getElementById("kb-understage-loader")?.classList.remove("show");
            }

            function underStageMeta(stageKey) {
              const key = canonicalStage(stageKey || 'lead');
              const meta = APP.companyKanbanStageMeta?.[key] || APP.kanbanStageMeta?.[key] || APP.stageMeta?.[key] || {};
              const subStages = Array.isArray(meta.sub_stages)
                ? meta.sub_stages
                : (Array.isArray(meta.subStages) ? meta.subStages : []);
              return { ...meta, sub_stages: subStages };
            }

            function getLeadSubStageId(item) {
              const raw = item?.lead_stage_sub_stage_id
                ?? item?.leadStageSubStageId
                ?? item?.lead_stage_substage_id
                ?? item?.stage_sub_stage_id
                ?? item?.sub_stage_id
                ?? item?.leadStageSubStage?.id
                ?? item?.lead_stage_sub_stage?.id
                ?? null;
              if (raw === null || raw === undefined || raw === '' || raw === 0 || raw === '0') return '';
              return String(raw);
            }

            function getLeadProductKey(item) {
              return String(item?.lead_product_id ?? item?.lead_product_list_id ?? item?.id ?? '');
            }

            function setLeadSubStageOnCachedData(leadProductId, subStageId, subStageMeta = null) {
              const updater = (arr) => {
                if (!Array.isArray(arr)) return;
                const item = arr.find((x) => getLeadProductKey(x) === String(leadProductId));
                if (!item) return;
                item.lead_stage_sub_stage_id = subStageId || null;
                item.lead_stage_sub_stage_name = subStageMeta?.name || '';
                item.lead_stage_sub_stage_color = subStageMeta?.color || '';
                item.lead_stage_sub_stage_icon = subStageMeta?.icon || '';
              };
              updater(APP.allLeads);
              updater(State?.lastKanbanData);
            }

            function findUnderStageMetaById(stageKey, subStageId) {
              const meta = underStageMeta(stageKey);
              const subStages = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
              return subStages.find((s) => String(s.id) === String(subStageId)) || null;
            }

            function setKanbanMainColumns() {
              APP.underStage.active = false;
              APP.underStage.stageKey = null;
              APP.kanbanStageNames = { ...(APP.companyKanbanStageNames || APP.stageNames || {}) };
              APP.kanbanStageMeta = { ...(APP.companyKanbanStageMeta || APP.stageMeta || {}) };
            }

            function openUnderStageSidebar(stageKey, meta = {}) {
              const sidebar = qs("#kbUnderstageSidebar");
              const backdrop = qs("#kbUnderstageSidebarBackdrop");
              const title = qs("#kbUnderstageSidebarTitle");
              const subtitle = qs("#kbUnderstageSidebarSubtitle");
              const stageLabel = meta.name || APP.stageNames?.[stageKey] || stageKey || "Hauptphase";

              if (title) title.textContent = `Unterphasen · ${stageLabel}`;
              if (subtitle) {
                subtitle.innerHTML = `Karten der Hauptphase <strong>${escapeHTML(stageLabel)}</strong>. Drag & Drop ändert nur die Unterphase, nicht die Hauptphase.`;
              }

              sidebar?.classList.add("open");
              sidebar?.setAttribute("aria-hidden", "false");
              backdrop?.classList.add("show");
              document.body.classList.add("kb-understage-sidebar-open");
              featherRefreshSoon();
            }

            function closeUnderStageSidebar() {
              qs("#kbUnderstageSidebar")?.classList.remove("open");
              qs("#kbUnderstageSidebar")?.setAttribute("aria-hidden", "true");
              qs("#kbUnderstageSidebarBackdrop")?.classList.remove("show");
              document.body.classList.remove("kb-understage-sidebar-open");
              APP.underStage.active = false;
              APP.underStage.stageKey = null;
            }

            function buildUnderStageColumns(stageKey) {
              const meta = underStageMeta(stageKey);
              const subStages = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
              const board = qs("#kbUnderstageBoard");
              if (!board) return;
              board.innerHTML = "";

              openUnderStageSidebar(stageKey, meta);

              if (!subStages.length) {
                const empty = document.createElement("div");
                empty.className = "kb-understage-sidebar-empty";
                empty.innerHTML = `Für <strong>${escapeHTML(meta.name || stageKey)}</strong> sind noch keine Unterphasen konfiguriert. Öffne <strong>Phasen konfigurieren</strong> und klicke bei der Hauptphase auf <strong>Unterphasen</strong>, um Unterphasen anzulegen.`;
                board.appendChild(empty);
              }

              const makeColumn = (sub) => {
                const id = sub?.id ? String(sub.id) : "";
                const name = sub?.name || "Ohne Unterphase";
                const icon = sub?.icon || (id ? "list" : "help-circle");
                const color = sub?.color || (id ? meta.color : "#64748b") || "#93c21c";
                const col = document.createElement("div");
                col.className = "column";
                col.dataset.subStageId = id;
                col.innerHTML = `
                  <h3 style="background:${escapeHTML(color)};">
                    <span class="kb-column-title"><i class="feather icon-${escapeHTML(icon)}"></i> ${escapeHTML(name)}</span>
                    <span class="kb-header-counts"><span class="kb-count-pill kb-count-pill--total" data-understage-count="${escapeHTML(id)}">0</span></span>
                  </h3>
                  <div class="column-content" data-understage-dropzone="1" data-stage-key="${escapeHTML(stageKey)}" data-sub-stage-id="${escapeHTML(id)}"></div>`;
                return col;
              };

              board.appendChild(makeColumn({ id: "", name: "Ohne Unterphase", icon: "help-circle", color: "#64748b" }));
              subStages.forEach((sub) => board.appendChild(makeColumn(sub)));
              featherRefreshSoon();
            }

            function renderUnderStageBoard(stageKey) {
              APP.underStage.active = true;
              APP.underStage.stageKey = stageKey;
              showKanbanLoading("Lade Unterphasen...");
              window.setTimeout(() => {
                try {
                  buildUnderStageColumns(stageKey);
                  let leads = [];
                  if (Array.isArray(APP.allLeads) && APP.allLeads.length) leads = APP.allLeads;
                  else if (Array.isArray(State?.lastKanbanData) && State.lastKanbanData.length) leads = State.lastKanbanData;
                  const mainStageLeads = leads.filter((item) => canonicalStage(item?.stage || item?.status || item?.company_stage || 'lead') === canonicalStage(stageKey));
                  mainStageLeads.forEach((item) => {
                    const subId = getLeadSubStageId(item);
                    const selector = `[data-understage-dropzone][data-sub-stage-id="${CSS.escape(subId)}"]`;
                    const zone = qs(selector) || qs('[data-understage-dropzone][data-sub-stage-id=""]');
                    if (!zone) return;
                    const card = mountOrUpdateCard(stageKey, item, null);
                    card.dataset.understageCard = "1";
                    card.dataset.leadStageSubStageId = subId;
                    const subName = item.lead_stage_sub_stage_name || item.sub_stage_name || "";
                    if (subName && !card.querySelector(".kb-understage-chip")) {
                      const chip = document.createElement("div");
                      chip.className = "kb-understage-chip";
                      chip.innerHTML = `<i class="feather icon-git-branch"></i>${escapeHTML(subName)}`;
                      card.appendChild(chip);
                    }
                    zone.appendChild(card);
                  });
                  updateUnderStageCounts();
                  featherRefreshSoon();
                } finally {
                  hideKanbanLoading();
                }
              }, 180);
            }

            function updateUnderStageCounts() {
              qsa("[data-understage-count]").forEach((pill) => {
                const subId = pill.getAttribute("data-understage-count") || "";
                const count = qsa(`[data-understage-dropzone][data-sub-stage-id="${CSS.escape(subId)}"] .card`).length;
                pill.textContent = shortNum(count);
              });
            }

            async function saveLeadSubStage(leadProductId, subStageId, reason = '') {
              if (!leadProductId) throw new Error("LeadProduct-ID fehlt.");
              const url = APP.endpoints?.updateLeadSubStage
                ? APP.endpoints.updateLeadSubStage(encodeURIComponent(leadProductId))
                : `{{ url('/admin/kanban/lead-product') }}/${encodeURIComponent(leadProductId)}/sub-stage`;
              const data = await postJSON(url, {
                lead_stage_sub_stage_id: subStageId || null,
                reason: reason || '',
              });
              if (!data?.success) throw new Error(data?.message || "Unterphase konnte nicht gespeichert werden.");
              return data;
            }

            async function askUnderStageReason(subStageId, stageKey) {
              const subMeta = findUnderStageMetaById(stageKey, subStageId);
              const targetLabel = subMeta?.name || (subStageId ? `Unterphase #${subStageId}` : 'Ohne Unterphase');

              if (!window.Swal) {
                return { confirmed: true, reason: '' };
              }

              const result = await Swal.fire({
                title: 'Unterphase ändern',
                html: `
                  <div style="text-align:left">
                    <div class="mb-2 small text-muted">Ziel-Unterphase</div>
                    <div style="border:1px solid #dbeafe;background:#f8fafc;border-radius:12px;padding:10px;font-weight:900;color:#0f172a;">
                      ${escapeHTML(targetLabel)}
                    </div>
                    <label class="small text-muted font-weight-bold text-uppercase mt-3">Grund / Notiz</label>
                    <textarea id="swal-understage-reason" class="form-control" rows="3" placeholder="Warum wird die Unterphase geändert?"></textarea>
                  </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                width: 520,
                preConfirm: () => ({
                  reason: document.getElementById('swal-understage-reason')?.value || '',
                }),
              });

              return { confirmed: !!result.isConfirmed, reason: result.value?.reason || '' };
            }

            document.addEventListener("click", function (event) {
              const btn = event.target.closest("[data-understage-stage]");
              if (!btn) return;
              event.preventDefault();
              event.stopPropagation();
              const stageKey = btn.dataset.understageStage;
              if (!stageKey) return;
              const hasData = (Array.isArray(APP.allLeads) && APP.allLeads.length) || (Array.isArray(State?.lastKanbanData) && State.lastKanbanData.length);
              if (!hasData && typeof window.LeadUIFetchKanban === 'function') {
                showKanbanLoading('Lade Daten...');
                Promise.resolve(window.LeadUIFetchKanban(State?.filtersQS || ''))
                  .finally(() => renderUnderStageBoard(stageKey));
                return;
              }
              renderUnderStageBoard(stageKey);
            });

            document.addEventListener("click", function (event) {
              const btn = event.target.closest("[data-understage-close]");
              if (!btn) return;
              event.preventDefault();
              event.stopPropagation();
              closeUnderStageSidebar();
            });

            document.addEventListener("click", function (event) {
              const btn = event.target.closest("#kbUnderstageRefresh");
              if (!btn) return;
              event.preventDefault();
              event.stopPropagation();
              const stageKey = APP.underStage?.stageKey;
              if (!stageKey) return;
              if (typeof window.LeadUIFetchKanban === 'function') {
                showKanbanLoading('Aktualisiere Unterphasen...');
                Promise.resolve(window.LeadUIFetchKanban(State?.filtersQS || ''))
                  .finally(() => renderUnderStageBoard(stageKey));
              } else {
                renderUnderStageBoard(stageKey);
              }
            });

            document.addEventListener("keydown", function (event) {
              if (event.key === "Escape" && qs("#kbUnderstageSidebar")?.classList.contains("open")) {
                closeUnderStageSidebar();
              }
            });

            document.addEventListener("dragover", function (event) {
              const zone = event.target.closest("[data-understage-dropzone]");
              if (!zone) return;
              event.preventDefault();
              event.stopPropagation();
              zone.classList.add("drag-over");
            }, true);

            document.addEventListener("dragleave", function (event) {
              const zone = event.target.closest("[data-understage-dropzone]");
              if (!zone) return;
              const next = event.relatedTarget;
              if (!next || !zone.contains(next)) zone.classList.remove("drag-over");
            }, true);

            document.addEventListener("drop", async function (event) {
              const zone = event.target.closest("[data-understage-dropzone]");
              if (!zone) return;
              event.preventDefault();
              event.stopPropagation();
              event.stopImmediatePropagation();
              zone.classList.remove("drag-over");

              const raw =
                event.dataTransfer?.getData(window.KB_DND_MIME || "application/x-leadui-cards") ||
                event.dataTransfer?.getData("text/plain") ||
                "";
              const parsedDragIds = safeJSON(raw, []);
              const ids = Array.isArray(parsedDragIds) ? parsedDragIds : [];
              const draggedId = ids.length ? String(ids[0]) : "";
              const localRoot = zone.closest("#kbUnderstageBoard") || zone.closest(".kb-understage-sidebar") || document;
              let card = draggedId ? localRoot.querySelector(`#${CSS.escape(draggedId)}`) : null;

              // The main Kanban and the Unterphasen sidebar can contain cards with the same id.
              // Prefer the card inside the Unterphasen board, otherwise the browser may pick
              // the card from the main Kanban and the drop appears to do nothing.
              if (!card && draggedId) {
                card = Array.from(document.querySelectorAll(`#${CSS.escape(draggedId)}`))
                  .find((el) => el.closest("#kbUnderstageBoard") || el.closest(".kb-understage-sidebar")) || qs(`#${CSS.escape(draggedId)}`);
              }

              if (!card) card = event.target.closest(".card");
              if (!card) return;

              const leadProductId = card.dataset.leadProductId || card.dataset.leadProductListId || card.id?.replace("card-", "");
              const subStageId = zone.dataset.subStageId || "";
              const stageKey = zone.dataset.stageKey || APP.underStage?.stageKey || canonicalStage(card.dataset.companyStage || card.dataset.stage || 'lead');

              if (String(card.dataset.leadStageSubStageId || '') === String(subStageId || '')) {
                updateUnderStageCounts();
                return;
              }

              const previousParent = card.parentElement;
              const ask = await askUnderStageReason(subStageId || null, stageKey);
              if (!ask.confirmed) {
                updateUnderStageCounts();
                return;
              }

              zone.appendChild(card);
              try {
                await saveLeadSubStage(leadProductId, subStageId || null, ask.reason || '');
                card.dataset.leadStageSubStageId = subStageId;

                const subMeta = findUnderStageMetaById(stageKey, subStageId);
                setLeadSubStageOnCachedData(leadProductId, subStageId || null, subMeta);

                const oldChip = card.querySelector('.kb-understage-chip');
                if (oldChip) oldChip.remove();
                if (subMeta?.name) {
                  const chip = document.createElement('div');
                  chip.className = 'kb-understage-chip';
                  chip.innerHTML = `<i class="feather icon-git-branch"></i>${escapeHTML(subMeta.name)}`;
                  card.appendChild(chip);
                }

                updateUnderStageCounts();
                featherRefreshSoon();
              } catch (err) {
                if (previousParent) previousParent.appendChild(card);
                updateUnderStageCounts();
                Swal.fire("Fehler", err?.message || "Unterphase konnte nicht gespeichert werden.", "error");
              }
            }, true);

            function statusBadge(stage) {
              if (["lead", "offer", "follow_up"].includes(stage)) return ["Offen", "warning", "text-dark"];
              if (["accepted", "deal", "project", "completed"].includes(stage)) return ["Zusage", "success", ""];
              if (["archive", "archiv"].includes(stage)) return ["Archiv", "secondary", ""];
              if (["junk"].includes(stage)) return ["Junk", "danger", ""];
              return [APP.stageNames?.[stage] || stage || "Phase", "primary", ""];
            }

            function buildStatusBlock(lead) {
              const ws = String(lead.work_status || "").toLowerCase();

              // 👇 If Paused or Stopped
              if (ws === 'paused' || ws === 'stopped') {
                  let reason = "Kein Grund angegeben.";
                  try {
                      const historyStr = typeof lead.stage_history === 'string' ? lead.stage_history : JSON.stringify(lead.stage_history || "[]");
                      const history = JSON.parse(historyStr);
                      if (Array.isArray(history) && history.length > 0) {
                          const latest = history[history.length - 1];
                          if (latest && latest.description) {
                              reason = latest.description;
                          }
                      }
                  } catch(e) {
                      console.warn("Could not parse stage_history for status block", e);
                  }

                  // Added the 'status-reason' class so CSS can collapse it
                  const reasonHtml = `<div class="mt-1 small status-reason" style="color: #666; font-style: italic; line-height: 1.2; word-wrap: break-word; background: #fff; padding: 4px; border-radius: 4px; border: 1px dashed #ccc;">
                      <strong>Grund:</strong> ${escapeHTML(reason)}
                  </div>`;

                  const stateLabel = ws === "paused" ? "Pausiert" : "Gestoppt";
                  const tone = ws === "paused" ? "warning" : "danger";
                  const iconClass = ws === "paused" ? "icon-pause" : "icon-square";
                  const textClass = ws === "paused" ? "text-dark" : "";

                  return `
                    <div class="kb-status" title="Klicken zum Aus-/Einklappen">
                      <div>
                        <span class="badge bg-${tone} ${textClass} ">
                          <i class="feather ${iconClass}"></i> ${stateLabel} 
                          <i class="feather icon-chevron-down meta-toggle-icon"></i>
                        </span>
                      </div>
                      ${reasonHtml}
                    </div>`;
              }

              // 👇 If Active (Playing): show the preloaded Kanban next-step summary.
              const s = canonicalStage(lead.stage);
              const [txt, tone, extra] = statusBadge(s);

              const nextTitle =
                lead.next_kanban_task_title ||
                lead.next_task_title ||
                lead.kanban_next_step?.title ||
                lead.latest_activity ||
                lead.latest_phase ||
                "Noch keine Aufgabe";

              const previousTitle =
                lead.previous_kanban_task_title ||
                lead.kanban_next_step?.previous_title ||
                "-";

              const landedAt =
                lead.stage_landed_at ||
                lead.kanban_next_step?.stage_landed_at ||
                lead.done_date ||
                lead.updated_at;

              const landedText = fmtDE(landedAt) || "-";
              const openCount = Number(lead.kanban_open_task_count || lead.kanban_next_step?.open_count || 0);
              const doneCount = Number(lead.kanban_done_task_count || lead.kanban_next_step?.done_count || 0);

              return `
                <div class="kb-status" title="Nächster Schritt">
                  <div class="d-flex align-items-center gap-1">
                    <span class="badge bg-${tone} badge-${tone} ${extra} mr-1">${txt}</span>
                    <span class="badge bg-primary">
                      <i class="feather icon-arrow-right-circle"></i> Nächster Schritt
                      <i class="feather icon-chevron-down meta-toggle-icon"></i>
                    </span>
                  </div>
                  <div class="meta">
                    <div class="rowline"><i class="feather icon-log-in"></i></div>
                    <div class="rowline value">Seit Stage-Start: <strong>${escapeHTML(landedText)}</strong></div>

                    <div class="rowline"><i class="feather icon-check-circle"></i></div>
                    <div class="rowline value">Vorher: ${escapeHTML(previousTitle)}</div>

                    <div class="rowline"><i class="feather icon-list"></i></div>
                    <div class="rowline value"><strong>${escapeHTML(nextTitle)}</strong></div>

                    <div class="rowline"><i class="feather icon-activity"></i></div>
                    <div class="rowline value">Offen: ${openCount} · Erledigt: ${doneCount}</div>
                  </div>
                </div>`;
            }

            function applyRunStateUI(card, state) {
              const cls = { playing: "status-playing", paused: "status-paused", stopped: "status-stopped" };
              state = ["playing", "paused", "stopped"].includes(String(state || "").toLowerCase()) ? String(state).toLowerCase() : "playing";
              card.dataset.runState = state;
              card.classList.remove("status-playing", "status-paused", "status-stopped", "card-has-overlay");
              card.classList.add(cls[state] || cls.playing);
              const overlay = card.querySelector(".card-status-overlay");
              if (!overlay) return;

              if (state === "paused" || state === "stopped") {
                card.classList.add("card-has-overlay");
                overlay.style.display = "flex";
                overlay.style.flexDirection = "column"; 

                let reason = "Kein Grund angegeben.";
                try {
                    const historyStr = typeof card.dataset.stageHistory === 'string' ? card.dataset.stageHistory : JSON.stringify(card.dataset.stageHistory || "[]");
                    const history = JSON.parse(historyStr);
                    if (Array.isArray(history) && history.length > 0) {
                        const latest = history[history.length - 1];
                        if (latest && latest.description) {
                            reason = latest.description;
                        }
                    }
                } catch(e) {
                    console.warn("Could not parse stage_history for overlay", e);
                }

                const safeReason = escapeHTML(reason);
                const reasonHtml = safeReason 
                    ? `<div style="margin-top: 8px; font-size: 12px; font-weight: 600; color: #444; background: rgba(255,255,255,0.85); padding: 4px 8px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 90%; word-wrap: break-word;">${safeReason}</div>` 
                    : '';

                // 👇 Hardcoded German labels 👇
                const stateLabel = state === "paused" ? "Pausiert" : "Gestoppt";
                const iconClass = state === "paused" ? "icon-pause" : "icon-square";

                overlay.innerHTML = `
                  <span class="card-status-badge">
                    <i class="feather ${iconClass}"></i> 
                    ${stateLabel}
                  </span>
                  ${reasonHtml}
                `;
              } else {
                overlay.style.display = "none";
                overlay.style.flexDirection = "";
                overlay.innerHTML = "";
              }
              card.dataset.runState = state;
            }

            const cardId = (it) => `card-${it.lead_product_id}`;

            function parseStageHistorySafe(value) {
              if (!value) return [];

              if (Array.isArray(value)) return value;

              try {
                  const parsed = JSON.parse(value);
                  return Array.isArray(parsed) ? parsed : [];
              } catch (e) {
                  return [];
              }
          }

          function normalizeDateValue(value) {
              if (!value) return null;

              const d = new Date(value);
              return isNaN(d.getTime()) ? null : d;
          }

          function currentStageEnteredAt(item, stageKey) {
              const currentStage = String(stageKey || item?.stage || "lead").toLowerCase();
              const history = parseStageHistorySafe(item?.stage_history);

              const matching = history
                  .filter(row => {
                      const rowStage = String(row?.stage || row?.to || "").toLowerCase();
                      return rowStage === currentStage && row?.changed_at;
                  })
                  .sort((a, b) => new Date(b.changed_at) - new Date(a.changed_at));

              if (matching.length) {
                  return matching[0].changed_at;
              }

              return item?.created_at || item?.updated_at || null;
          }

          function formatDateTimeDE(value) {
              const d = normalizeDateValue(value);
              if (!d) return "-";

              return d.toLocaleString("de-DE", {
                  day: "2-digit",
                  month: "2-digit",
                  year: "numeric",
                  hour: "2-digit",
                  minute: "2-digit",
              });
          }

          function stageDurationText(value) {
              const start = normalizeDateValue(value);
              if (!start) return "-";

              const now = new Date();
              let diffMs = now - start;

              if (diffMs < 0) diffMs = 0;

              const minutes = Math.floor(diffMs / 60000);
              const hours = Math.floor(minutes / 60);
              const days = Math.floor(hours / 24);

              const restHours = hours % 24;
              const restMinutes = minutes % 60;

              if (days > 0) {
                  return `${days} Tag${days === 1 ? "" : "e"} ${restHours} Std.`;
              }

              if (hours > 0) {
                  return `${hours} Std. ${restMinutes} Min.`;
              }

              return `${Math.max(1, minutes)} Min.`;
          }

          function stageTimeHTML(item, stageKey) {
              const enteredAt = currentStageEnteredAt(item, stageKey);

              return `
                  <div class="kb-stage-time"
                      data-stage-entered-at="${escapeHTML(enteredAt || "")}">
                      <div class="kb-stage-time-row">
                          <i class="feather icon-calendar"></i>
                          <span>Seit: <strong>${escapeHTML(formatDateTimeDE(enteredAt))}</strong></span>
                      </div>
                      <div class="kb-stage-time-row">
                          <i class="feather icon-clock"></i>
                          <span>Dauer: <strong data-stage-duration>${escapeHTML(stageDurationText(enteredAt))}</strong></span>
                      </div>
                  </div>
              `;
          }


          function refreshVisibleStageDurations() {
              document.querySelectorAll(".kb-stage-time").forEach((box) => {
                  const enteredAt = box.dataset.stageEnteredAt || "";
                  const target = box.querySelector("[data-stage-duration]");
                  if (target) target.textContent = stageDurationText(enteredAt);
              });
          }

          function refreshCardStageTime(card, item, stageKey) {
              if (!card) return;
              const box = card.querySelector(".kb-stage-time");
              const html = stageTimeHTML(item || {}, stageKey || card.dataset.stage || card.dataset.companyStage || "lead");
              if (box) {
                  box.outerHTML = html;
              } else {
                  const meta = card.querySelector(".kb-card-meta");
                  if (meta) meta.insertAdjacentHTML("afterend", html);
              }
              refreshVisibleStageDurations();
              featherRefreshSoon?.();
          }


            function offerWorkflowHTML(item) {
                const safeStr = (v) => (v == null ? "" : String(v));
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);

                const workflow = item?.offer_workflow || item?.offerWorkflow || null;
                const leadStage = canonicalStage(item?.stage || "lead");
                const shouldShowEmpty = ["offer", "deal", "auftrag"].includes(leadStage);

                if (!workflow || workflow.exists === false) {
                    if (!shouldShowEmpty) return "";

                    return `
                      <div class="kb-offer-workflow-empty">
                        Dieser Kunde ist in ${leadStage === "offer" ? "Angebot" : "Auftrag"}, aber es wurde noch kein passender Angebot-/Auftrag-Ordner gefunden.
                      </div>`;
                }

                const documentLabel = workflow.document_status_label
                    || (String(workflow.document_status || "").toLowerCase() === "deal" ? "Auftrag" : "Angebot");

                const color = safeStr(workflow.status_color || (documentLabel === "Auftrag" ? "#10b981" : "#74b2d4"));
                const statusLabel = safeStr(workflow.status_label || workflow.status_key || "-");
                const offerNo = safeStr(workflow.offer_no || "").trim();
                const folderName = safeStr(workflow.folder_name || "").trim();
                const updatedRaw = safeStr(workflow.updated_at || "").trim();
                const updated = (() => {
                    if (!updatedRaw) return "-";
                    const d = new Date(updatedRaw);
                    return Number.isNaN(d.getTime()) ? updatedRaw : d.toLocaleDateString("de-DE");
                })();

                const openUrl = safeStr(workflow.url || "").trim();

                return `
                  <details class="kb-offer-workflow" data-offer-workflow="${esc(documentLabel)}">
                    <summary class="kb-offer-workflow-head">
                      <div class="kb-offer-workflow-left">
                        <span class="kb-offer-workflow-chevron"><i class="feather icon-chevron-right"></i></span>
                        <div class="kb-offer-workflow-title">
                          <i class="feather icon-activity"></i>
                          <span>Status</span>
                        </div>
                      </div>
                      <span class="kb-offer-workflow-status" style="background:${esc(color)}" title="${esc(statusLabel)}">
                        ${esc(statusLabel)}
                      </span>
                    </summary>

                    <div class="kb-offer-workflow-body">
                      ${offerNo ? `
                        <div class="kb-offer-workflow-row">
                          <span class="kb-offer-workflow-label">Nr.</span>
                          <span class="kb-offer-workflow-value">${esc(offerNo)}</span>
                        </div>` : ``}

                      ${folderName ? `
                        <div class="kb-offer-workflow-row">
                          <span class="kb-offer-workflow-label">Ordner</span>
                          <span class="kb-offer-workflow-value" title="${esc(folderName)}">${esc(folderName)}</span>
                        </div>` : ``}

                      <div class="kb-offer-workflow-row">
                        <span class="kb-offer-workflow-label">Aktualisiert</span>
                        <span class="kb-offer-workflow-value">${esc(updated)}</span>
                      </div>

                      ${openUrl ? `
                        <a class="kb-offer-workflow-open" href="${esc(openUrl)}" target="_blank" rel="noopener">
                          Öffnen <i class="feather icon-external-link"></i>
                        </a>` : ``}
                    </div>
                  </details>`;
            }



            function reminderSummaryHTML(item) {
                const safeStr = (v) => (v == null ? "" : String(v));
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);

                const r = item?.latest_reminder || item?.next_reminder || item?.reminder || null;
                if (!r) {
                    return `
                      <div class="kb-reminder-summary is-empty">
                        <div class="kb-reminder-head">
                          <div class="kb-reminder-title"><i class="feather icon-bell"></i> Keine Erinnerung</div>
                          <span class="kb-reminder-priority normal">Offen</span>
                        </div>
                        <div class="kb-reminder-body">
                          <i class="feather icon-info"></i>
                          <span>Noch kein nächster Schritt geplant.</span>
                        </div>
                      </div>`;
                }

                const title = safeStr(r.title || r.task_title || "Reminder").trim();
                const desc = safeStr(r.description || "").trim();
                const priority = safeStr(r.priority || "normal").toLowerCase();
                const dueDate = safeStr(r.reminder_date || r.due_date || "").slice(0, 10);
                const dueTime = safeStr(r.reminder_time || r.due_time || "").slice(0, 5);
                const responsible = safeStr(r.responsible_name || r.employee_name || r.owner_name || "").trim();
                const today = new Date();
                const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
                const boxState = dueDate && dueDate < todayStr ? " kb-reminder-overdue" : (dueDate === todayStr ? " kb-reminder-due-today" : "");

                return `
                  <div class="kb-reminder-summary${boxState}">
                    <div class="kb-reminder-head">
                      <div class="kb-reminder-title"><i class="feather icon-bell"></i> Nächster Schritt</div>
                      <span class="kb-reminder-priority ${esc(priority)}">${esc(priority || "normal")}</span>
                    </div>
                    <div class="kb-reminder-body">
                      <i class="feather icon-check-square"></i>
                      <span><strong>${esc(title)}</strong>${desc ? `<br>${esc(desc).slice(0, 120)}` : ``}</span>
                      <i class="feather icon-calendar"></i>
                      <span class="kb-reminder-due">${esc(dueDate || "kein Datum")}${dueTime ? `, ${esc(dueTime)} Uhr` : ``}</span>
                      <i class="feather icon-user"></i>
                      <span>${esc(responsible || "Automatisch / nicht zugewiesen")}</span>
                    </div>
                  </div>`;
            }


            function currentStageLandedAt(item, fallbackDate = null) {
                const safeStr = (v) => (v == null ? "" : String(v));
                const stage = canonicalStage(item?.stage || item?.status || item?.company_stage || "lead");
                let history = [];
                try {
                    history = Array.isArray(item?.stage_history)
                      ? item.stage_history
                      : JSON.parse(safeStr(item?.stage_history || "[]"));
                } catch (e) {
                    history = [];
                }

                if (Array.isArray(history) && history.length) {
                    for (let i = history.length - 1; i >= 0; i--) {
                        const h = history[i] || {};
                        const to = canonicalStage(h.to || h.stage || h.status || "");
                        if (to === stage && (h.changed_at || h.created_at || h.date)) {
                            return h.changed_at || h.created_at || h.date;
                        }
                    }
                }

                return fallbackDate || item?.updated_at || item?.created_at || null;
            }

            function formatDateTimeDE(value) {
                if (!value) return "Neu / gerade gestartet";
                const d = new Date(value);
                if (!d || Number.isNaN(d.getTime())) return "Neu / gerade gestartet";
                return d.toLocaleDateString("de-DE", { day: "2-digit", month: "2-digit", year: "2-digit" }) + " " +
                       d.toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" });
            }

            function nextStepPreviewHTML(item) {
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);

                const landed = formatDateTimeDE(item?.stage_landed_at || item?.kanban_next_step?.stage_landed_at || currentStageLandedAt(item));
                const nextTitle =
                    item?.next_kanban_task_title ||
                    item?.next_task_title ||
                    item?.kanban_next_step?.title ||
                    item?.latest_activity ||
                    item?.latest_phase ||
                    item?.product_task_phase_name ||
                    "Noch keine Aufgabe";
                const previousTitle = item?.previous_kanban_task_title || item?.kanban_next_step?.previous_title || "-";
                const openCount = Number(item?.kanban_open_task_count || item?.kanban_next_step?.open_count || 0);
                const doneCount = Number(item?.kanban_done_task_count || item?.kanban_next_step?.done_count || 0);
                const lpId = item?.lead_product_id || item?.lead_product_list_id || item?.id || "";

                return `
                  <div class="kb-next-step-preview">
                    <div class="kb-next-step-preview-head">
                      <span><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</span>
                      <button type="button"
                              class="kb-next-step-preview-btn"
                              data-open-kanban-task-management
                              data-lead-product-list-id="${esc(lpId)}">
                        Details
                      </button>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-log-in"></i>
                      <span>Seit: <strong>${esc(landed)}</strong></span>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-check-circle"></i>
                      <span>Vorher: <strong>${esc(previousTitle)}</strong></span>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-list"></i>
                      <span>${esc(nextTitle)}</span>
                    </div>
                    <div class="kb-next-step-preview-line">
                      <i class="feather icon-activity"></i>
                      <span>Offen: <strong>${esc(openCount)}</strong> · Erledigt: <strong>${esc(doneCount)}</strong></span>
                    </div>
                  </div>`;
            }


            function cardHTML(item, stageKey) {
                "use strict";

                const safeStr = (v) => (v == null ? "" : String(v));
                const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[m]);
                const isNonEmpty = (v) => safeStr(v).trim().length > 0;
                const stage = canonicalStage(stageKey || item?.stage || item?.status || "lead");

                const fullName = (() => {
                    const fn = safeStr(item?.customer_name).trim();
                    const ln = safeStr(item?.customer_lastname).trim();
                    const firma = safeStr(item?.firma).trim();
                    const name = `${fn} ${ln}`.trim();
                    return name || firma || "Unbekannt";
                })();

                const address = [item?.street, item?.postcode, item?.city]
                    .map((v) => safeStr(v).trim())
                    .filter(Boolean)
                    .join(", ");

                const productInitial = safeStr(item?.initial || item?.product_initial || item?.article_group_initial || "").trim();
                const leadProductId = safeStr(item?.lead_product_id || item?.lead_product_list_id || item?.id || "");

                const createdRaw = item?.created_at || item?.updated_at || null;
                const compactDate = (() => {
                    const d = createdRaw ? new Date(createdRaw) : null;
                    if (!d || Number.isNaN(d.getTime())) return "–";
                    return d.toLocaleDateString("de-DE", { day: "2-digit", month: "2-digit" }) + " " + d.toLocaleTimeString("de-DE", { hour: "2-digit", minute: "2-digit" });
                })();

                const currentSubStageId = typeof getLeadSubStageId === "function" ? getLeadSubStageId(item) : safeStr(item?.lead_stage_sub_stage_id || item?.sub_stage_id || item?.stage_sub_stage_id || "");
                const subStageName = safeStr(item?.lead_stage_sub_stage_name || item?.sub_stage_name || "").trim();
                const subStageChip = subStageName
                    ? `<div class="kb-understage-chip"><i class="feather icon-git-branch"></i>${esc(subStageName)}</div>`
                    : (currentSubStageId ? `<div class="kb-understage-chip"><i class="feather icon-git-branch"></i>Unterphase #${esc(currentSubStageId)}</div>` : ``);

                const employee = item?.employee && (item.employee.employee_id || item.employee.id) ? item.employee : null;
                const fieldEmployee = item?.field_employee && (item.field_employee.employee_id || item.field_employee.id) ? item.field_employee : null;

                const mkEmp = (emp, fallbackTitle) => {
                    if (!emp) return null;
                    const title = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || fallbackTitle;
                    return {
                        title,
                        image: safeStr(emp?.image).trim(),
                        id: Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0,
                    };
                };

                const empList = [mkEmp(employee, "Innendienst"), mkEmp(fieldEmployee, "Außendienst")].filter(Boolean);

                const empHTML = empList.length > 0
                    ? `<ul class="list-unstyled users-list m-0 d-flex align-items-center">
                        ${empList.map((e) => `
                          <li class="avatar pull-up" title="${esc(e.title)}">
                            <img class="media-object rounded-circle"
                                 src="${APP.EMP_SRC}/${esc(e.image || "noimage.png")}"
                                 height="26" width="26" alt=""
                                 style="object-fit:cover;border:2px solid #fff;">
                          </li>`).join("")}
                      </ul>`
                    : `<small>&ndash;</small>`;

                const teamHTML = (() => {
                    const currentAssignments = Array.isArray(item?.current_team_assignments) && item.current_team_assignments.length
                        ? item.current_team_assignments
                        : (Array.isArray(item?.team_assignments)
                            ? item.team_assignments.filter(a => canonicalStage(a?.stage || stage) === stage)
                            : []);

                    const fallbackMembers = Array.isArray(item?.team_members) ? item.team_members.map(m => ({ member: m })) : [];
                    const list = currentAssignments.length ? currentAssignments : fallbackMembers;
                    const visible = list.slice(0, 2);
                    const rest = Math.max(0, list.length - visible.length);

                    const avatarHtml = visible.map((x) => {
                        const emp = x?.member || x || {};
                        const img = emp?.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
                        const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || "Team";
                        return `<img src="${esc(img)}" alt="${esc(name)}" title="${esc(name)}">`;
                    }).join("");

                    return `
                      <div class="kb-card-team-compact">
                        <button type="button"
                                class="kb-team-pill"
                                data-show-stage-team="${esc(stage)}"
                                title="Teamübersicht öffnen">
                          <span class="kb-team-mini-avatars">${avatarHtml}</span>
                          <span>Teams</span>
                          <span class="kb-team-pill-count">${list.length}</span>
                          ${rest > 0 ? `<span class="kb-team-pill-count">+${rest}</span>` : ``}
                        </button>
                      </div>`;
                })();

                const hideJunk = stageRank(stage) >= stageRank("deal");

                return `
                  <div class="card-status-overlay" aria-hidden="true"></div>

                  ${getDateAgeIndicator(item?.created_at, stage)}

                  <div class="kb-menu kb-menu--card" aria-label="Kartenmenü">
                    <button type="button" class="btn-icon kb-menu-toggle" data-act="custom-menu-toggle" title="Menü" aria-haspopup="menu" aria-expanded="false">
                      <i class="feather icon-more-vertical" aria-hidden="true"></i>
                    </button>

                    <div class="kb-menu-dropdown" role="menu" aria-label="Menü" hidden>
                      <button type="button" class="kb-menu-item" data-menu="verlauf" role="menuitem"><i class="feather icon-clock mr-50"></i> Verlauf</button>
                      <button type="button" class="kb-menu-item" data-menu="termin" role="menuitem"><i class="feather icon-calendar mr-50"></i> Termin</button>
                      <button type="button" class="kb-menu-item" data-menu="aufgabe" role="menuitem"><i class="feather icon-check-square mr-50"></i> Aufgabe</button>
                      <button type="button" class="kb-menu-item" data-open-notes data-customer="${esc(item.customer_id)}" data-alt="${esc(item.alternative_id)}" data-product="${esc(item.product_id)}" role="menuitem"><i class="feather icon-message-square mr-50"></i> Notizen</button>
                      <a class="kb-menu-item" href="/new_lead_profile/${encodeURIComponent(safeStr(item?.customer_id))}" role="menuitem"><i class="feather icon-eye mr-50"></i> Profil</a>
                      <hr class="my-50">
                      <button type="button" class="kb-menu-item text-success" data-run="playing" role="menuitem"><i class="feather icon-play mr-50"></i> Start</button>
                      <button type="button" class="kb-menu-item text-warning" data-run="paused" role="menuitem"><i class="feather icon-pause mr-50"></i> Pause</button>
                      <button type="button" class="kb-menu-item text-danger" data-run="stopped" role="menuitem"><i class="feather icon-square mr-50"></i> Stopp</button>
                      ${!hideJunk ? `<button type="button" class="kb-menu-item text-danger" data-act="delete" role="menuitem"><i class="feather icon-trash-2 mr-50"></i> Junk</button>` : ``}
                      ${stage === "completed" ? `<button type="button" class="kb-menu-item" data-act="archive" role="menuitem"><i class="feather icon-archive mr-50"></i> Archivieren</button>` : ``}
                    </div>
                  </div>

                  <div class="card-header card-header--kb">
                    <div class="card-title">
                      <strong class="card-name" title="${esc(fullName)}">${esc(fullName)}</strong>
                      ${productInitial ? `<div class="circle product_circle" aria-hidden="true">${esc(productInitial)}</div>` : ``}
                    </div>
                  </div>

                  <div class="kb-card-meta">
                    <div class="kb-meta-row">
                      <span class="kb-meta-item"><i class="feather icon-calendar"></i> ${esc(compactDate)}</span>
                    </div>
                    ${isNonEmpty(address) ? `<small class="kb-meta-address" title="${esc(address)}">${esc(address)}</small>` : ``}
                  </div>

                  ${subStageChip}
                  ${nextStepPreviewHTML(item)}

                  <div class="employeeList d-flex align-items-center mt-2">
                    ${empHTML}
                    ${teamHTML}
                  </div>

                  <div class="card-actions" role="group" aria-label="Aktionen">
                    <div class="left-actions">
                      <button class="btn-icon" data-menu="termin" title="Termin">
                        <i class="feather icon-calendar"></i>
                        <span class="badge-notes" data-ap-count style="display:none">0</span>
                      </button>
                      <button class="btn-icon" data-menu="aufgabe" title="Aufgabe">
                        <i class="feather icon-check-square"></i>
                        <span class="badge-notes" data-pt-count style="display:none">0</span>
                      </button>
                      <button type="button"
                              class="btn-icon kb-task-management-btn"
                              data-open-kanban-task-management
                              data-lead-product-list-id="${esc(leadProductId)}"
                              data-customer-id="${esc(item.customer_id || '')}"
                              data-alternative-id="${esc(item.alternative_id || '')}"
                              data-product-id="${esc(item.product_id || '')}"
                              data-customer-name="${esc(fullName)}"
                              data-product-name="${esc(item.article_group || item.product_name || item.product || item.initial || '')}"
                              title="Aufgabenmanagement">
                        <i class="feather icon-list"></i>
                        <span class="kb-task-count-badge d-none" data-kanban-task-count>0</span>
                      </button>
                    </div>

                    <div class="right-actions">
                      <button type="button" class="btn-icon btn-notes note" data-open-notes data-customer="${esc(item.customer_id)}" data-alt="${esc(item.alternative_id)}" data-product="${esc(item.product_id)}" title="Notizen">
                        <i class="feather icon-message-square"></i>
                        <span class="badge-notes" data-count="0" style="display:none">0</span>
                      </button>
                      <a href="/new_lead_profile/${encodeURIComponent(safeStr(item?.customer_id))}" class="btn-icon" title="Profil">
                        <i class="feather icon-eye"></i>
                      </a>
                      ${!hideJunk ? `<button class="btn-icon" data-act="delete" title="In Junk verschieben"><i class="feather icon-trash-2"></i></button>` : ``}
                      ${stage === "completed" ? `<button class="btn-icon" data-act="archive" title="Archivieren"><i class="feather icon-archive"></i></button>` : ``}
                    </div>
                  </div>
                `;
            }

            async function updateCardLeadSubStage(select) {
                const card = select.closest(".card");
                const leadProductId = Number(select.dataset.leadProductId || card?.dataset.leadProductId || 0);
                const stageKey = canonicalStage(select.dataset.stageKey || card?.dataset.companyStage || card?.dataset.stage || "lead");
                const subStageId = select.value || null;
                const previous = select.dataset.previousValue || "";

                if (!leadProductId || !APP.endpoints?.stageWorkflowMove) {
                    if (window.Swal) Swal.fire("Fehler", "LeadProduct-ID oder Speicherroute fehlt.", "error");
                    else alert("LeadProduct-ID oder Speicherroute fehlt.");
                    select.value = previous;
                    return;
                }

                select.disabled = true;

                try {
                    const payload = {
                        mode: "company",
                        company_stage_key: stageKey,
                        lead_stage_sub_stage_id: subStageId,
                        reason: "Unterphase geändert",
                        teams: card?.dataset?.teamIds ? safeJSON(card.dataset.teamIds, []) : []
                    };

                    const data = await postJSON(APP.endpoints.stageWorkflowMove(leadProductId), payload);
                    if (!data?.success) throw new Error(data?.message || "Unterphase konnte nicht gespeichert werden.");

                    select.dataset.previousValue = subStageId || "";
                    if (card) {
                        card.dataset.leadStageSubStageId = subStageId || "";
                    }
                } catch (error) {
                    select.value = previous;
                    if (window.Swal) Swal.fire("Fehler", error.message || "Unterphase konnte nicht gespeichert werden.", "error");
                    else alert(error.message || "Unterphase konnte nicht gespeichert werden.");
                } finally {
                    select.disabled = false;
                }
            }

            document.addEventListener("change", function (event) {
                const select = event.target.closest("[data-substage-change]");
                if (!select) return;
                updateCardLeadSubStage(select);
            });

            document.addEventListener("focusin", function (event) {
                const select = event.target.closest("[data-substage-change]");
                if (!select) return;
                select.dataset.previousValue = select.value || "";
            });




            function renderStageTeamRowsForSwal(assignments, currentStage = null) {
              const arr = Array.isArray(assignments) ? assignments : [];
              const stageKeys = orderedStageEntries(APP.stageNames || {}).map(([k]) => k).filter((k) => !["junk", "ticket"].includes(k));
              const stage = canonicalStage(currentStage || "lead");
              const currentIdx = stageKeys.indexOf(stage);
              const visibleStages = currentIdx >= 0 ? stageKeys.slice(0, currentIdx + 1) : stageKeys;

              const byStage = new Map();
              arr.forEach((a) => {
                const st = canonicalStage(a?.stage || stage || "lead");
                if (!byStage.has(st)) byStage.set(st, []);
                byStage.get(st).push(a);
              });

              const currentMembers = byStage.get(stage) || [];
              const memberChip = (x) => {
                const emp = x?.member || {};
                const name = `${emp?.lastname || ""} ${emp?.name || ""}`.trim() || `Mitarbeiter #${x?.employee_id || ""}`;
                const img = emp?.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
                return `<span class="swal-team-chip"><img src="${escapeHTML(img)}" alt="">${escapeHTML(name)}</span>`;
              };

              return `
                <div style="text-align:left">
                  <div class="swal-team-current-box">
                    <div class="swal-team-current-title">Aktuelles Team in ${escapeHTML(APP.stageNames?.[stage] || stage)}</div>
                    <div class="swal-team-current-list">
                      ${currentMembers.length ? currentMembers.map(memberChip).join("") : `<span class="swal-stage-team-empty">Kein aktuelles Team gespeichert</span>`}
                    </div>
                  </div>

                  <div class="swal-stage-team-grid">
                    ${visibleStages.map((st) => {
                      const members = byStage.get(st) || [];
                      const isCurrent = st === stage ? " is-current-stage" : "";
                      return `<div class="swal-stage-team-row${isCurrent}">
                        <div class="swal-stage-team-title">${escapeHTML(APP.stageNames?.[st] || st)}</div>
                        <div>
                          ${members.length ? members.map((x) => {
                            const emp = x?.member || {};
                            const name = `${emp?.lastname || ""} ${emp?.name || ""}`.trim() || `Mitarbeiter #${x?.employee_id || ""}`;
                            const u = x?.assigned_by_user || {};
                            const by = `${u?.lastname || ""} ${u?.name || ""}`.trim() || (x?.assigned_by ? `Mitarbeiter #${x.assigned_by}` : "-");
                            const at = x?.assigned_at ? fmtDE(x.assigned_at) : "-";
                            return `<div class="swal-stage-team-member"><strong>${escapeHTML(name)}</strong><br><span class="text-muted">von ${escapeHTML(by)} • ${escapeHTML(at)}</span></div>`;
                          }).join("") : `<div class="swal-stage-team-empty">Kein Team gespeichert</div>`}
                        </div>
                      </div>`;
                    }).join("")}
                  </div>
                </div>`;
            }

            function openStageTeamModal(holder, stageKey) {
              if (!holder) return;
              let assignments = [];
              try {
                assignments = JSON.parse(holder.dataset.teamAssignments || holder.dataset.teams || "[]");
              } catch (_) {
                assignments = [];
              }
              const stage = canonicalStage(stageKey || holder.dataset.stage || "lead");
              const html = renderStageTeamRowsForSwal(assignments, stage);
              Swal.fire({
                title: `Teams`,
                html,
                width: 780,
                confirmButtonText: "Schließen"
              });
            }

            document.addEventListener("click", (e) => {
              const btn = e.target.closest("[data-show-stage-team]");
              if (!btn) return;
              e.preventDefault();
              e.stopPropagation();
              const holder = btn.closest(".card, tr.list-row-item, [data-team-assignments]");
              openStageTeamModal(holder, btn.dataset.showStageTeam);
            });

              function normalizeTeamIds(item) {
                const toId = (x) => {
                  const n = Number(
                    x?.id ??
                    x?.employee_id ??
                    x?.emp_id ??
                    x
                  );
                  return Number.isFinite(n) && n > 0 ? n : null;
                };

                // preferred: backend sends ids directly
                const direct =
                  item?.team_ids ??
                  item?.teamIds ??
                  item?.teams_ids ??
                  item?.teamsIds ??
                  null;

                if (Array.isArray(direct)) return direct.map(toId).filter(Boolean);

                // fallback: arrays of objects
                const arr =
                  Array.isArray(item?.team_members) ? item.team_members :
                  Array.isArray(item?.teams) ? item.teams :
                  [];

                return arr.map(toId).filter(Boolean);
              }

            function mountOrUpdateCard(stageKey, item, existing) {
              let card = existing;
              if (!card) {
                card = document.createElement("div");
                card.className = "card";
                card.id = cardId(item);
                card.draggable = true;
                card.dataset.customerId = item.customer_id ?? "";
                card.dataset.alternativeId = item.alternative_id ?? "";
                card.dataset.productId = item.product_id ?? "";
                card.dataset.leadProductId = item.lead_product_id ?? item.lead_product_list_id ?? item.id ?? "";
                card.dataset.leadProductListId = item.lead_product_id ?? item.lead_product_list_id ?? item.id ?? "";
              }
              card.dataset.employeeId = item.employee?.employee_id ?? 0;
              card.dataset.fieldEmployeeId = item.field_employee?.employee_id ?? 0;
              card.dataset.service = item.service ?? "complete";
              card.dataset.serviceId = item.service_id ?? 0;
              card.dataset.departmentId = item.department_id ?? 0;
              const columnKey = workflowColumnKey(item);
              card.dataset.stage = columnKey;
              card.dataset.companyStage = canonicalStage(item.stage);
              card.dataset.productStageId = item.product_stage_id || "";
              card.dataset.productTaskPhaseId = item.product_task_phase_id || "";
              card.dataset.productStageName = item.product_stage_name || "";
              card.dataset.productTaskPhaseName = item.product_task_phase_name || "";
              card.dataset.stageMode = item.stage_mode || APP.stageWorkflow.mode || "company";
              card.dataset.latestPhase = item.latest_phase || "";
              card.dataset.latestActivity = item.latest_activity || "";
              card.dataset.doneDate = item.done_date || "";
              card.dataset.createdAt = item.created_at || "";
              card.dataset.updatedAt = item.updated_at || "";
              card.dataset.fullAddress = item.full_address || "";
              card.dataset.street = item.street || "";
              card.dataset.postcode = item.postcode || "";
              card.dataset.city = item.city || "";
              card.dataset.phone = item.phone || "";
              card.dataset.email = item.email || "";
              card.dataset.latitude = item.latitude || "";
              card.dataset.longitude = item.longitude || "";
              card.dataset.teamIds = JSON.stringify(Array.isArray(item.team_ids) ? item.team_ids : normalizeTeamIds(item));
              card.dataset.teamAssignments = JSON.stringify(Array.isArray(item.team_assignments) ? item.team_assignments : []);
              card.dataset.stageHistory = typeof item.stage_history === 'string' ? item.stage_history : JSON.stringify(item.stage_history || []);
              card.dataset.leadStageSubStageId = getLeadSubStageId(item);

              card.innerHTML = cardHTML(item, stageKey);
              enforceActionVisibility(card);
              const ws = (item.work_status || "playing").toString().toLowerCase();
              applyRunStateUI(card, ["playing", "paused", "stopped"].includes(ws) ? ws : "playing");
              return card;
            }

            function renderKanbanDiff(leads) {
              APP.allLeads = Array.isArray(leads) ? leads : [];
              if (APP.underStage?.active && APP.underStage.stageKey) { renderUnderStageBoard(APP.underStage.stageKey); return; }
              ensureColumns();
              const existing = new Map();
              qsa("#kanban .card").forEach((el) => existing.set(el.id, el));
              const visibleStageNames = APP.kanbanStageNames || APP.stageNames;
              const stageBuckets = new Map(Object.keys(visibleStageNames).map((k) => [k, []]));

              const filtered = (leads || []).filter((it) => !["junk", "ticket"].includes(canonicalStage(it.stage)));

              for (const it of filtered) {
                const s = workflowColumnKey(it);
                if (stageBuckets.has(s)) stageBuckets.get(s).push(it);
              }

              for (const [stage, arr] of stageBuckets) {
                const container = colContent(stage);
                if (!container) continue;
                const frag = document.createDocumentFragment();
                for (const item of arr) {
                  const id = cardId(item);
                  const prev = existing.get(id) || null;
                  const card = mountOrUpdateCard(stage, item, prev);
                  frag.appendChild(card);
                  existing.delete(id);
                }
                container.innerHTML = "";
                container.appendChild(frag);
              }
              for (const [, el] of existing) el.remove();
              updateCounts();
              featherRefreshSoon();
              updateNoteBadgesForVisibleCards();
              // Compact cards: live feed is intentionally not loaded on cards.
            }

            function autoChunk() {
              const low = (navigator.hardwareConcurrency || 4) < 6;
              const narrow = window.matchMedia?.("(max-width: 768px)").matches;
              return low || narrow ? 24 : 60;
            }

            function renderKanbanIncremental(leads, chunkSize = autoChunk(), done = () => {}) {
              APP.allLeads = Array.isArray(leads) ? leads : [];
              if (APP.underStage?.active && APP.underStage.stageKey) { renderUnderStageBoard(APP.underStage.stageKey); done?.(); return; }
              ensureColumns();
              clearColumns();
              const list = (leads || []).filter((it) => !["junk", "ticket"].includes(canonicalStage(it?.stage)));
              let i = 0;
              (function pump() {
                const frags = new Map();
                const getFrag = (s) => {
                  if (!frags.has(s)) frags.set(s, document.createDocumentFragment());
                  return frags.get(s);
                };
                for (let c = 0; c < chunkSize && i < list.length; c++, i++) {
                  const item = list[i];
                  const stage = workflowColumnKey(item);
                  if ((APP.kanbanStageNames || APP.stageNames)[stage] || APP.stageAlias[stage]) {
                    const card = mountOrUpdateCard(stage, item, null);
                    getFrag(stage).appendChild(card);
                  }
                }
                for (const [stage, frag] of frags) colContent(stage)?.appendChild(frag);
                if (i < list.length) {
                  requestIdleCallback(pump);
                } else {
                  updateCounts();
                  featherRefreshSoon();
                  updateNoteBadgesForVisibleCards();
                  enforceActionVisibility();
                  // Compact cards: live feed is intentionally not loaded on cards.
                  refreshVisibleStageDurations();
                  done();
                }
              })();
            }

            /* --- Note Logic (Unified for List & Kanban) --- */
            const visibleCardTuples = () => {
              const cards = qsa("#kanban .card");
              const rows = qsa("#kanbanTableBody tr.list-row-item");
              return [...cards, ...rows].map((el) => ({
                el,
                customer_id: el.dataset.customerId,
                alternative_id: el.dataset.alternativeId,
                product_id: el.dataset.productId || null,
              }));
            };

            async function fetchNoteCountOnce(t) {
              const params = new URLSearchParams({ customer_id: t.customer_id, alternative_id: t.alternative_id, per_page: 1 });
              if (t.product_id) params.set("product_id", t.product_id);
              try {
                const p = await safeFetchJSON(`${APP.endpoints.notesIndex}?${params.toString()}`);
                return Number(p?.total || 0);
              } catch { return 0; }
            }

            function updateBadge(el, n) {
              const bd = el.querySelector(".badge-notes");
              if (!bd) return;
              bd.dataset.count = String(n);
              bd.textContent = shortNum(n);
              bd.style.display = n > 0 ? 'block' : 'none'; 
            }

            function updateNoteBadgesForVisibleCards() {
              const tuples = visibleCardTuples();
              tuples.forEach((t) => updateBadge(t.el, 0));
              let i = 0;
              (function next() {
                const batch = tuples.slice(i, (i += 4));
                if (!batch.length) return;
                Promise.all(batch.map(async (t) => updateBadge(t.el, await fetchNoteCountOnce(t)))).finally(() => setTimeout(next, 30));
              })();
            }

           function setNotesTab(tab) {
                const tabs = document.querySelectorAll("[data-notes-tab]");
                const panels = document.querySelectorAll("[data-notes-panel]");
                tabs.forEach((btn) => {
                  const isActive = btn.dataset.notesTab === tab;
                  btn.classList.toggle("notes-tab--active", isActive);
                  btn.setAttribute("aria-selected", isActive ? "true" : "false");
                });
                panels.forEach((panel) => {
                  const isActive = panel.dataset.notesPanel === tab;
                  panel.classList.toggle("d-none", !isActive);
                });

                // 🔴 FIX: Hide the Quill Editor footer if we are not on the "notes" tab
                const footer = document.querySelector("#notesDrawer .notes-foot");
                if (footer) {
                    footer.style.display = (tab === 'notes') ? 'block' : 'none';
                }
            }

            async function loadNotesReport() {
                const panel = document.getElementById("notesReport");
                if (!panel) return;
                const cId = document.getElementById("notesCustomerId")?.value || "";
                const aId = document.getElementById("notesAlternativeId")?.value || "";
                const pId = document.getElementById("notesProductId")?.value || "";

                if (!cId || !aId) {
                    panel.innerHTML = `<div class="text-muted small p-2">Kein Kontext (Kunde/Alternative) vorhanden.</div>`;
                    return;
                }

                // Only show loading if empty (prevents flashing during background load)
                if(panel.innerHTML.trim() === "") {
                    panel.innerHTML = `<div class="text-muted small p-2">Report wird geladen…</div>`;
                }

                try {
                    const params = new URLSearchParams({ customer_id: cId, alternative_id: aId });
                    if (pId) params.set("product_id", pId);
                    const res = await fetch(`${APP.endpoints.reportsIndex}?${params.toString()}`, { method: "GET", credentials: "same-origin", headers: { Accept: "text/html,application/json", "X-Requested-With": "XMLHttpRequest" } });
                    const text = await res.text();
                    if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
                    let html = text;
                    const ct = res.headers.get("content-type") || "";
                    if (ct.includes("application/json")) {
                        try {
                            const json = JSON.parse(text);
                            html = typeof json.html === "string" ? json.html : `<pre class="small p-2 bg-light border rounded mb-0" style="max-height: 320px; overflow:auto;">${JSON.stringify(json, null, 2)}</pre>`;
                        } catch { html = text; }
                    }
                    panel.innerHTML = html;

                    // 🔴 FIX: Count the reports and update the badge
                    const count = panel.querySelectorAll('.ap-report-card').length;
                    const badge = document.getElementById('tabBadgeTerminReport');
                    if (badge) {
                        badge.textContent = count;
                        badge.classList.remove('d-none');
                    }

                } catch (e) {
                    panel.innerHTML = `<div class="text-danger small p-2">Report konnte nicht geladen werden.<br>${(e && e.message) ? e.message : ''}</div>`;
                }
            }

          async function loadCustomerReport() {
                const panel = document.getElementById("customerReportList");
                if (!panel) return;
                const cId = document.getElementById("notesCustomerId")?.value || "";
                const aId = document.getElementById("notesAlternativeId")?.value || "";
                const pId = document.getElementById("notesProductId")?.value || "";

                if (!cId || !aId) {
                    panel.innerHTML = `<div class="text-muted small p-2">Kein Kontext (Kunde/Alternative) vorhanden.</div>`;
                    return;
                }

                // Only show loading if empty (prevents flashing during background load)
                if(panel.innerHTML.trim() === "") {
                    panel.innerHTML = `<div class="text-muted small p-2">Kundenreport wird geladen…</div>`;
                }

                try {
                    const params = new URLSearchParams({ customer_id: cId, alternative_id: aId });
                    if (pId) params.set("product_id", pId);
                    const res = await safeFetchJSON(`${APP.endpoints.customerReportsIndex}?${params.toString()}`, { method: "GET" });
                    if (!res || typeof res.html !== "string") throw new Error(res?.message || "Unerwartete Serverantwort.");
                    panel.innerHTML = res.html;

                    // 🔴 FIX: Count the reports and update the badge
                    const count = panel.querySelectorAll('.cr-card, .ap-report-card').length;
                    const badge = document.getElementById('tabBadgeCustomerReport');
                    if (badge) {
                        badge.textContent = count;
                        badge.classList.remove('d-none');
                    }

                } catch (e) {
                    panel.innerHTML = `<div class="text-danger small p-2">Kundenreport konnte nicht geladen werden.<br>${(e && e.message) ? e.message : ''}</div>`;
                }
            }

            // NOTE HANDLERS
            function noteHTML(n) {
              const me = String(n.created_by ?? "") === String(APP.authUserId);
              const img = n?.author?.image ? `${APP.EMP_SRC}/${n.author.image}` : `${APP.EMP_SRC}/noimage.png`;
              const who = n.author ? `${n.author.lastname ?? ""} ${n.author.name ?? ""}`.trim() : "Unbekannt";
              const when = n.created_at ? new Date(n.created_at).toLocaleString("de-DE") : "";
              const bubble = `<div class="note-bubble ${me ? "me" : "other"}"><div class="note-bubble-body" data-note-body>${n.description || ""}</div><div class="note-meta"><span class="note-meta-author">${who}</span><span class="note-meta-sep">•</span><span class="note-meta-time">${when}</span></div>${me ? `<div class="note-actions"><button type="button" class="note-action note-action-edit" data-note-edit data-note-id="${n.id}"><i class="feather icon-edit-2"></i></button><button type="button" class="note-action note-action-delete" data-note-delete data-note-id="${n.id}"><i class="feather icon-trash-2"></i></button></div>` : ""}</div>`;
              return `<div class="note-row ${me ? "me" : "other"}" data-note-id="${n.id}">${me ? bubble + `<img class="note-avatar" src="${img}" alt="">` : `<img class="note-avatar" src="${img}" alt="">` + bubble}</div>`;
            }

            function adjustNotesCounters(delta) {
                const badge = document.getElementById("notesCountBadge");
                if (badge) {
                  const next = Math.max(0, Number(badge.dataset.count || 0) + delta);
                  badge.dataset.count = String(next);
                  badge.textContent = shortNum(next);
                }
                const cId = document.getElementById("notesCustomerId")?.value;
                const aId = document.getElementById("notesAlternativeId")?.value;
                const pId = document.getElementById("notesProductId")?.value;

                if (!cId || !aId) return;

                const selector = `
                    .card[data-customer-id="${CSS.escape(cId)}"][data-alternative-id="${CSS.escape(aId)}"][data-product-id="${CSS.escape(pId)}"] .badge-notes,
                    tr[data-customer-id="${CSS.escape(cId)}"][data-alternative-id="${CSS.escape(aId)}"][data-product-id="${CSS.escape(pId)}"] .badge-notes
                `;

                document.querySelectorAll(selector).forEach((b) => {
                  const next = Math.max(0, Number(b.dataset.count || 0) + delta);
                  b.dataset.count = String(next);
                  b.textContent = shortNum(next);
                  b.style.display = next > 0 ? 'block' : 'none';
                });
            }

          async function openNotesDrawerFor(cId, aId, pId, title, lId) { // <--- Added lId parameter
              const drawer = qs("#notesDrawer"), list = qs("#notesList"), titleEl = qs("#notesTitle");
              const fC = qs("#notesCustomerId"), fA = qs("#notesAlternativeId"), fP = qs("#notesProductId");
              const fL = qs("#notesLeadProductListId"); // <--- Select the new hidden input

              titleEl.textContent = title || "Kunden-Notizen";
              drawer.classList.add("open");
              qs("#notesBackdrop").classList.add("show");
              document.body.style.overflow = "hidden";

              ensureNoteQuill();
              setNoteEditorHTML("");
              setNotesTab("notes");

              fC.value = cId; 
              fA.value = aId; 
              fP.value = pId || "";
              if (fL) fL.value = lId || ""; // <--- Set the new hidden input value

              // Clear old report panels so they don't show wrong data briefly
              const rPanel = document.getElementById("notesReport");
              const cPanel = document.getElementById("customerReportList");
              if(rPanel) rPanel.innerHTML = "";
              if(cPanel) cPanel.innerHTML = "";

              try {
                  const params = new URLSearchParams({ customer_id: cId, alternative_id: aId, per_page: 50 });
                  if (pId) params.set("product_id", pId);

                  const payload = await safeFetchJSON(`${APP.endpoints.notesIndex}?${params.toString()}`);
                  const items = (Array.isArray(payload?.notes) ? payload.notes : payload || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                  list.innerHTML = items.map(noteHTML).join("");

                  const total = payload?.total ?? items.length;

                  // Update Header Badge
                  const headerBadge = document.getElementById("notesCountBadge");
                  if (headerBadge) { headerBadge.dataset.count = String(total); headerBadge.textContent = shortNum(total); }

                  // Update the specific Tab Badge
                  const tabBadge = document.getElementById("tabBadgeNotes");
                  if (tabBadge) { tabBadge.textContent = total; }

                  list.scrollTop = list.scrollHeight;
              } catch (e) { 
                  Swal.fire("Fehler", e.message, "error"); 
              }

              loadNotesReport();
              loadCustomerReport();
          }
            // NOTE DRAWER CLOSE LOGIC
            const closeNotes = () => {
                qs("#notesDrawer")?.classList.remove("open");
                qs("#notesBackdrop")?.classList.remove("show");
                document.body.style.overflow = "";
            };
            qs("#notesBackdrop")?.addEventListener("click", closeNotes);
            qsa("[data-notes-close]").forEach(b => b.addEventListener("click", closeNotes));

            // NOTE SUBMIT LOGIC
          qs("#notesForm").onsubmit = async (ev) => {
              ev.preventDefault();
              const text = getNoteEditorHTML();
              if (!text) return;

              // Grab all hidden inputs
              const fC = qs("#notesCustomerId");
              const fA = qs("#notesAlternativeId");
              const fP = qs("#notesProductId");
              const fL = qs("#notesLeadProductListId"); // <--- Grab the new hidden input

              try {
                  const res = await safeFetchJSON(APP.endpoints.notesStore, { 
                      method: "POST", 
                      headers: { 
                          "Content-Type": "application/json", 
                          "X-CSRF-TOKEN": CSRF(), 
                          "X-Requested-With": "XMLHttpRequest" 
                      }, 
                      body: JSON.stringify({ 
                          customer_id: Number(fC.value), 
                          alternative_id: Number(fA.value), 
                          product_id: fP.value ? Number(fP.value) : null, 
                          lead_product_list_id: fL && fL.value ? Number(fL.value) : null, // <--- Add to payload
                          description: text 
                      }) 
                  });

                  qs("#notesList").insertAdjacentHTML("beforeend", noteHTML(res.note || res));
                  qs("#notesList").scrollTop = qs("#notesList").scrollHeight;
                  setNoteEditorHTML("");
                  adjustNotesCounters(+1);
              } catch (e) { 
                  Swal.fire("Fehler", e.message, "error"); 
              }
          };
            document.addEventListener("submit", async (e) => {
              const form = e.target.closest(".ap-report-create-form");
              if (!form) return;
              e.preventDefault();
              const title = (form.querySelector('input[name="title"]')?.value || "").trim();
              const content = (form.querySelector('textarea[name="content"]')?.value || "").trim();
              if (!title || !content) { Swal.fire("Hinweis", "Titel und Text sind Pflichtfelder.", "info"); return; }
              const appointmentId = form.dataset.appointmentId || null;
              try {
                const payload = { title, content, stage: (form.querySelector('select[name="stage"]')?.value || "").trim(), report: `${title}\n\n${content}`, report_date: form.querySelector('input[name="report_date"]')?.value || null };
                const res = await safeFetchJSON(APP.endpoints.reportsStore(appointmentId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify(payload) });
                if (!res || res.status !== "ok") throw new Error(res?.message || "Fehler.");
                const group = form.closest(".ap-appointment-group");
                group?.querySelector(".ap-report-list")?.insertAdjacentHTML("afterbegin", res.html);
                form.reset();
                group.querySelector(".ap-report-create-wrapper").style.display = "none";
                Swal.fire("Gespeichert", "Report wurde hinzugefügt.", "success");
              } catch (err) { Swal.fire("Fehler", err.message, "error"); }
            });

            document.addEventListener("click", async (e) => {
              const btn = e.target.closest(".ap-report-like, .ap-report-dislike");
              if (!btn) return;
              const card = btn.closest(".ap-report-card");
              const reportId = card.getAttribute("data-report-id");
              if (!reportId) return;
              let reaction = btn.classList.contains("ap-report-like") ? "like" : "dislike";
              if (btn.classList.contains("is-active")) reaction = "none";
              try {
                const res = await safeFetchJSON(APP.endpoints.reportsReact(reportId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify({ reaction }) });
                card.querySelector(".ap-report-like-count").textContent = res.likes ?? 0;
                card.querySelector(".ap-report-dislike-count").textContent = res.dislikes ?? 0;
                card.querySelectorAll(".ap-report-like, .ap-report-dislike").forEach((b) => b.classList.remove("is-active"));
                if (res.my_reaction === "like") card.querySelector(".ap-report-like")?.classList.add("is-active");
                else if (res.my_reaction === "dislike") card.querySelector(".ap-report-dislike")?.classList.add("is-active");
              } catch (err) { Swal.fire("Fehler", err.message, "error"); }
            });

            document.addEventListener("click", (e) => {
              const btn = e.target.closest(".ap-open-report-form");
              if (!btn) return;
              const wrapper = btn.closest(".ap-appointment-group").querySelector(".ap-report-create-wrapper");
              const isVisible = wrapper.style.display !== "none";
              wrapper.style.display = isVisible ? "none" : "block";
              if (!btn.dataset.originalLabel) btn.dataset.originalLabel = btn.innerHTML;
              btn.innerHTML = !isVisible ? `<i class="feather icon-file-text"></i> Report schließen` : btn.dataset.originalLabel;
            });

            document.addEventListener("click", (e) => {
              const toggleBtn = e.target.closest("[data-report-toggle-comments]");
              if (!toggleBtn) return;
              const section = toggleBtn.closest(".ap-report-card").querySelector(".ap-report-comments");
              if (section.hasAttribute("hidden")) section.removeAttribute("hidden"); else section.setAttribute("hidden", "");
            });

            document.addEventListener("click", async (e) => {
              const submitBtn = e.target.closest(".ap-report-comment-submit");
              if (!submitBtn) return;
              const card = submitBtn.closest(".ap-report-card");
              const reportId = card.getAttribute("data-report-id");
              const textarea = card.querySelector(".ap-report-comment-text");
              const text = textarea.value.trim();
              if (!text) return;
              try {
                const res = await safeFetchJSON(APP.endpoints.reportsComment(reportId), { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF() }, body: JSON.stringify({ comment: text }) });
                if (res && typeof res.html === "string") {
                  card.querySelector(".ap-report-comments-list").insertAdjacentHTML("beforeend", res.html);
                  const toggleBtn = card.querySelector("[data-report-toggle-comments]");
                  const current = parseInt(toggleBtn.textContent.match(/(\d+)/)?.[1] || 0, 10);
                  toggleBtn.innerHTML = `<i class="feather icon-message-circle mr-25"></i> Kommentare (${current + 1})`;
                }
                textarea.value = "";
              } catch (err) { Swal.fire("Fehler", err.message, "error"); }
            });

            document.addEventListener("click", (e) => {
              const btn = e.target.closest("[data-notes-tab]");
              if (!btn) return;
              const tab = btn.dataset.notesTab;
              setNotesTab(tab);
              if (tab === "report") loadNotesReport();
              else if (tab === "customerReport") loadCustomerReport();
            });

            /* --------------------- Custom card menu (kb-menu) ------------------------ */
            (function () {
              const closeAllMenus = () => {
                document.querySelectorAll(".kb-menu-dropdown").forEach((d) => d.setAttribute("hidden", ""));
                document.querySelectorAll('[data-act="custom-menu-toggle"][aria-expanded="true"]').forEach((btn) => btn.setAttribute("aria-expanded", "false"));
              };
              document.addEventListener("click", (e) => {
                const toggleBtn = e.target.closest('[data-act="custom-menu-toggle"]');
                if (toggleBtn) {
                  const dd = toggleBtn.parentElement.querySelector(".kb-menu-dropdown");
                  const isOpen = dd && !dd.hasAttribute("hidden");
                  closeAllMenus();
                  if (dd && !isOpen) { dd.removeAttribute("hidden"); toggleBtn.setAttribute("aria-expanded", "true"); }
                  e.stopImmediatePropagation();
                  return;
                }
                const item = e.target.closest(".kb-menu-item");
                if (item) {
                 const card = item.closest(".card");
                  const type = item.dataset.menu;
                  const runState = item.dataset.run;

                  // IMPORTANT: If it's a Play/Pause/Stop button, DO NOT call stopPropagation.
                  // Let it bubble up to the global "Run" handler.
                  if (runState) {
                      closeAllMenus();
                      return; // Let the global handler take over
                  }

                  closeAllMenus();

                  if (type === "verlauf" && card) {
                      const a = document.createElement("a");
                      a.href = `/lead/process/history/${encodeURIComponent(card.dataset.customerId)}/${encodeURIComponent(card.dataset.alternativeId)}/${encodeURIComponent(card.dataset.productId)}`;
                      a.setAttribute("data-lh-history", "");
                      a.style.display = "none";
                      document.body.appendChild(a);
                      a.click();
                      a.remove();
                  }
                  if (type === "verlauf" && card) {
                    const a = document.createElement("a");
                    a.href = `/lead/process/history/${encodeURIComponent(card.dataset.customerId)}/${encodeURIComponent(card.dataset.alternativeId)}/${encodeURIComponent(card.dataset.productId)}`;
                    a.setAttribute("data-lh-history", "");
                    a.style.display = "none";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                  }
                  if (type === "product-stage-info" && card) {
                    showProductStageInfoFromElement(card);
                  }
                  if (type === "ticket" && card) { /* Ticket Logic */ }
                  if (type === "termin" && card) {
                     const name = card.querySelector(".card-header strong")?.textContent?.trim() || "Kunde";
                     card.dispatchEvent(new CustomEvent("open-appointments", { bubbles: true, detail: { customerId: card.dataset.customerId, alternativeId: card.dataset.alternativeId, productId: card.dataset.productId, title: `Termine • ${name}`, full_address: card.dataset.fullAddress || "" } }));
                  }
                  if (type === "aufgabe" && card) {
                     const name = card.querySelector(".card-header strong")?.textContent?.trim() || "Kunde";
                     card.dispatchEvent(new CustomEvent("open-personal-tasks", { bubbles: true, detail: { customerId: card.dataset.customerId, alternativeId: card.dataset.alternativeId, productId: card.dataset.productId, title: `Aufgaben • ${name}` } }));
                  }
                  e.stopImmediatePropagation();
                }
              });
              document.addEventListener("click", (e) => { if (!e.target.closest(".kb-menu")) closeAllMenus(); });
            })();

            /* --------------------------- Junk tab ------------------------- */
              async function fetchJunkTab(qsStr) {
                const pane = document.querySelector("#junk");
                if (!pane) return;

                try {
                  const res = await fetch(`${APP.endpoints.junk}${qsStr ? `?${qsStr}` : ""}`, {
                    headers: { Accept: "text/html", "X-Requested-With": "XMLHttpRequest" },
                    credentials: "same-origin",
                  });

                  const html = await res.text();

                  // Replace the whole tab content (safe and avoids nested #junkInner)
                  pane.innerHTML = html;
                } catch (e) {}
              }


              document.addEventListener("click", async (e) => {
                const btn = e.target.closest(".btn-restore");
                if (!btn) return;

                const row = btn.closest(".oc-item") || btn.closest("tr");
                if (!row) return;

                const select = row.querySelector(".restore-select");
                const target = select?.value;

                if (!target) {
                    Swal.fire("Hinweis", "Bitte Zielphase wählen.", "info");
                    return;
                }

                const customerId = row.dataset.customerId || "";
                const alternativeId = row.dataset.alternativeId || row.dataset.altId || "";
                const productId = row.dataset.productId || "";
                const leadProductId = btn.dataset.id || row.dataset.leadProductId || "";

                if (!customerId || !alternativeId || !productId) {
                    Swal.fire("Fehler", "Fehlende IDs in der Zeile (customer/alternative/product).", "error");
                    return;
                }

                const { value: reason, isConfirmed } = await Swal.fire({
                    title: "Grund",
                    input: "textarea",
                    inputPlaceholder: "Optionaler Grund für die Wiederherstellung…",
                    showCancelButton: true,
                    confirmButtonText: "Wiederherstellen",
                    cancelButtonText: "Abbrechen"
                });

                if (!isConfirmed) return;

                try {
                    const url = `${APP.endpoints.changeStage}/${encodeURIComponent(customerId)}/${encodeURIComponent(alternativeId)}/${encodeURIComponent(productId)}`;

                    const res = await postJSON(url, {
                        lead_product_id: Number(leadProductId),
                        stage: target,
                        description: reason || "",
                        source: "junk"
                    });

                    if (!res?.success) {
                        throw new Error(res?.message || "Fehler beim Wiederherstellen");
                    }

                    row.remove();

                    Swal.fire({
                        icon: "success",
                        title: "Wiederhergestellt",
                        text: "Der Lead wurde erfolgreich verschoben.",
                        timer: 1400,
                        showConfirmButton: false
                    });

                    if (window.LeadUI?.silentRefreshBoth) {
                        window.LeadUI.silentRefreshBoth();
                    }
                } catch (err) {
                    Swal.fire("Fehler", err?.message || "Serverfehler", "error");
                }
            });

            /* ====================== Live Feed Modal ================== */
          /* ====================== Live Feed Modal (Robust) ================== */
            const LiveFeedModal = (() => {
              const modalId = "liveFeedModal";
              const backdropId = "liveFeedModalBackdrop";

              // Cache DOM elements
              const getEl = (id) => document.getElementById(id);
              const listEl = () => getEl("liveFeedModalList");
              const countEl = () => getEl("liveFeedModalCount");

              let allItems = [];
              let typeFilter = "all";

              function render() {
                const list = listEl();
                const count = countEl();
                if (!list) return;

                const items = typeFilter === "all" ? allItems : allItems.filter(i => i.type === typeFilter);

                if (count) count.textContent = `${items.length} von ${allItems.length} Einträgen`;

                list.innerHTML = items.length ? items.map(i => `
                  <div class="lfm-item">
                    <div class="lfm-item-type ${i.type === 'task' ? 'task' : i.type === 'appointment' ? 'appointment' : 'ticket'}">
                      ${i.type_label || i.type}
                    </div>
                    <div class="lfm-item-main">
                      <div class="lfm-item-title">${i.title}</div>
                      <div class="lfm-item-sub">${i.text}</div>
                    </div>
                    <div class="lfm-item-time">
                      <span>${i.when_human}</span>
                    </div>
                  </div>`).join("") : `<div class="lfm-empty">Keine Aktivitäten gefunden.</div>`;
              }

              function open(items) {
                console.log("Opening Modal with items:", items); // Debug
                allItems = Array.isArray(items) ? items : [];
                typeFilter = "all";

                const modal = getEl(modalId);
                const backdrop = getEl(backdropId);

                if(modal && backdrop) {
                    render();
                    modal.style.display = "flex"; // Force flex
                    backdrop.style.display = "block";
                    document.body.style.overflow = "hidden";
                } else {
                    console.error("LiveFeedModal elements not found in DOM.");
                }
              }

              function close() {
                const modal = getEl(modalId);
                const backdrop = getEl(backdropId);
                if (modal) modal.style.display = "none";
                if (backdrop) backdrop.style.display = "none";
                document.body.style.overflow = "";
              }

              // Attach global listeners once
              document.addEventListener("DOMContentLoaded", () => {
                  getEl(backdropId)?.addEventListener("click", close);
                  getEl("liveFeedModalClose")?.addEventListener("click", close);

                  getEl("liveFeedTypeFilters")?.addEventListener("click", (e) => {
                      const btn = e.target.closest(".lfm-filter-btn");
                      if (btn) {
                          typeFilter = btn.dataset.type;
                          document.querySelectorAll(".lfm-filter-btn").forEach(b => b.classList.toggle("is-active", b === btn));
                          render();
                      }
                  });
              });

              return {
                open,
                close,
                openForCard: (wrapper) => {
                   // This wrapper is the .card or .list-row-item
                   if(!wrapper) return;
                   // Use the shared LiveFeed module to get data
                   const items = window.LeadUI.liveFeed.getItemsForCard(wrapper);

                   if (items && items.length > 0) {
                       open(items);
                   } else {
                       // If data isn't loaded yet, try loading it then opening
                       // This uses the wrapper's dataset
                       if(window.LeadUI.liveFeed.loadForCard) {
                           window.LeadUI.liveFeed.loadForCard(wrapper).then(() => {
                               // Retry getting items after fetch
                               const freshItems = window.LeadUI.liveFeed.getItemsForCard(wrapper);
                               open(freshItems);
                           });
                       }
                   }
                }
              };
            })();

            /* ====================== Per-card Live Feed ================== */
            const LiveFeed = (() => {
              const registry = new WeakMap();
              function createInstance(root) {
                let items = [], index = 0, timer = null;
                const textEl = root.querySelector("[data-feed-text]"); 
                const render = () => {
                  if (!items.length) { root.style.display = "none"; return; }
                  root.style.display = "";
                  const item = items[index];
                  if(textEl) textEl.textContent = item.text || "";
                  root.querySelector("[data-feed-title]").textContent = item.title || "Aktivität";
                  root.querySelector("[data-feed-time]").textContent = item.when_human || "";
                };
                const go = (step) => { index = (index + step + items.length) % items.length; render(); };
                return { 
                  setItems: (next) => { items = next; index = 0; render(); }, 
                  loadForTuple: async (c, a, p, l) => {
                      try {
                          const res = await safeFetchJSON(`${APP.endpoints.liveFeed}?customer_id=${c}`);
                          items = res.items || [];
                          render();
                      } catch(e) { console.error(e); }
                  },
                  getItems: () => items 
                };
              }
              function getInstance(root) {
                  if (!root) return null;
                  if (!registry.has(root)) registry.set(root, createInstance(root));
                  return registry.get(root);
              }
              return {
                  loadForCard: (card) => getInstance(card.querySelector("[data-feed-root]"))?.loadForTuple(card.dataset.customerId),
                  getItemsForCard: (card) => getInstance(card.querySelector("[data-feed-root]"))?.getItems() || [],
                  bootstrapFromFirstCard: () => { const c = qs("#kanban .card"); if(c) getInstance(c.querySelector("[data-feed-root]"))?.loadForTuple(c.dataset.customerId); },
                  bootstrapAllCards: () => { qsa("#kanban .card").forEach(c => getInstance(c.querySelector("[data-feed-root]"))?.loadForTuple(c.dataset.customerId)); }
              };
            })();

            /* ------------------------------- Expose Core ----------------------------- */

            document.addEventListener("DOMContentLoaded", () => {
              try { bindWorkflowControls(); } catch (e) { console.warn("Workflow controls init failed", e); }
            });

            window.KanbanStageTime = {
              parseStageHistorySafe,
              currentStageEnteredAt,
              formatDateTimeDE,
              stageDurationText,
              stageTimeHTML,
              refreshVisibleStageDurations,
              refreshCardStageTime,
            };
            window.parseStageHistorySafe = parseStageHistorySafe;
            window.refreshCardStageTime = refreshCardStageTime;
            window.refreshVisibleStageDurations = refreshVisibleStageDurations;

            setInterval(refreshVisibleStageDurations, 60000);
            document.addEventListener("DOMContentLoaded", refreshVisibleStageDurations);

            window.LeadUI = {
              APP, State,
              utils: { qs, qsa, CSRF, fmtDE, getDateAgeIndicator, featherRefreshSoon, shortNum, canonicalStage, escapeHTML, stageFilterExcludes, saveToLocal, restoreFromLocal, syncURL, initFromURL, closeOverlays, enforceActionVisibility, isBackward, stageRank, workflowColumnKey, workflowLabel, workflowStageIdFromKey },
              net: { safeFetchJSON, postJSON, cancel },
              filters: { initSelect2, getFilterValues, updateFilterBadges, buildFilterQS, Drawer },
              kanban: { ensureColumns, clearColumns, colContent, updateCounts, statusBadge, buildStatusBlock, offerWorkflowHTML, applyRunStateUI, cardId, cardHTML, mountOrUpdateCard, renderKanbanDiff, renderKanbanIncremental, autoChunk },
              notes: { openNotesDrawerFor, updateNoteBadgesForVisibleCards },
              partials: { fetchJunkTab, fetchTicketsTab: async () => {} },
              liveFeed: LiveFeed,
              liveFeedModal: LiveFeedModal,
            };
          })();
          </script>

          <script>
            (function () {
              "use strict";

              // 1. Safe Access to Core Modules
              const LeadUI = window.LeadUI || {};
              const { APP, utils, net, notes, kanban, liveFeed, liveFeedModal } = LeadUI;

              // ---------------------------------------------------------
              // 2. Global Helpers for this Closure
              // ---------------------------------------------------------
              const safeStr = (v) => (v == null ? "" : String(v));
              const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

              const fmtDE = (v) => {
                try {
                  return v ? new Date(v).toLocaleDateString("de-DE") : "-";
                } catch {
                  return "-";
                }
              };

              // ---------------------------------------------------------
              // 3. HTML Generator: Live Feed Container
              // ---------------------------------------------------------
              function listFeedHTML() {
                return `
                  <div class="live-feed-bar list-live-feed card-live-feed"
                      data-feed-root
                      data-feed-count="0"
                      style="display:none; margin-top:0.6rem; width: 100%; max-width: 100%;">
                    <div class="live-feed-left">
                      <div class="live-feed-icon"><i class="feather icon-zap"></i></div>
                    </div>
                    <div class="live-feed-body">
                      <div class="live-feed-line" data-feed-empty>
                        <span class="live-feed-title">Keine Aktivitäten</span>
                        <span class="live-feed-dot">•</span>
                        <span class="live-feed-text">Noch keine Einträge.</span>
                      </div>
                      <div class="live-feed-line" data-feed-line>
                        <span class="live-feed-title" data-feed-title>Aktivität</span>
                        <span class="live-feed-dot">•</span>
                        <span class="live-feed-text" data-feed-text>Details…</span>
                      </div>
                      <div class="live-feed-meta">
                        <span class="live-feed-pill" data-feed-pill>Info</span>
                        <span class="live-feed-time">
                          <i class="feather icon-clock mr-25"></i>
                          <span data-feed-time>–</span>
                        </span>
                        <span class="live-feed-counter" data-feed-counter></span>
                      </div>
                    </div>
                    <div class="live-feed-controls">
                      <button type="button" class="live-feed-btn" title="Zurück" data-feed-prev>
                        <i class="feather icon-skip-back"></i>
                      </button>
                      <button type="button" class="live-feed-btn" title="Pause / Abspielen" data-feed-toggle>
                        <i class="feather icon-pause" data-feed-icon-pause></i>
                        <i class="feather icon-play d-none" data-feed-icon-play></i>
                      </button>
                      <button type="button" class="live-feed-btn" title="Weiter" data-feed-next>
                        <i class="feather icon-skip-forward"></i>
                      </button>
                      <button type="button" class="live-feed-btn" title="Vergrößern" data-feed-open-modal>
                        <i class="feather icon-maximize-2"></i>
                      </button>
                    </div>
                  </div>
                `;
              }

              // ---------------------------------------------------------
              // 4. HTML Generator: Avatar List Item
              // ---------------------------------------------------------
              function avatarLiFromEmp(emp, { withData = false, assignedBy = "", assignedAt = "", stageLabel = "" } = {}) {
                if (!emp) return "";
                const EMP_SRC = APP.EMP_SRC || '/images/employee';

                const id = Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0;
                const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;
                const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || `#${id}`;

                return `
                  <li class="avatar pull-up"
                      ${withData ? `data-emp-id="${esc(id)}"` : ""}
                      ${withData ? `data-assigned-by="${esc(assignedBy)}"` : ""}
                      ${withData ? `data-assigned-at="${esc(assignedAt)}"` : ""}
                      ${withData ? `data-stage-label="${esc(stageLabel)}"` : ""}
                      title="${esc(name)}"
                      style="margin-left:-8px;">
                    <img class="media-object rounded-circle"
                        src="${esc(img)}"
                        width="26" height="26"
                        alt="${esc(name)}"
                        style="border:2px solid #fff; object-fit:cover;">
                  </li>
                `;
              }

              // ---------------------------------------------------------
              // 5. HTML Generator: Employee & Team Column
              // ---------------------------------------------------------
              function listEmpAndTeamHTML(lead) {
                const stageKey = utils.canonicalStage(lead?.stage);
                const stageLabel = APP.stageNames?.[stageKey] || stageKey;

                const main = [];
                if (lead?.employee && (lead.employee.employee_id || lead.employee.id)) main.push(lead.employee);
                if (lead?.field_employee && (lead.field_employee.employee_id || lead.field_employee.id)) main.push(lead.field_employee);

                const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                let teamMembers = [];
                if (teamAssignments.length > 0) {
                    teamMembers = teamAssignments;
                } else if (Array.isArray(lead?.team_members)) {
                    teamMembers = lead.team_members.map(m => ({ member: m }));
                } else if (Array.isArray(lead?.teams)) {
                    teamMembers = lead.teams.map(m => ({ member: m }));
                }

                if (!main.length && !teamMembers.length) return `<span class="text-muted small">&ndash;</span>`;

                const mainHtml = main.length
                  ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center">
                      ${main.map((e) => avatarLiFromEmp(e, { withData: false })).join("")}
                    </ul>`
                  : "";

                const teamHtml = teamMembers.length
                  ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center"
                        data-team-hover
                        style="margin-left:10px; padding-left:10px; border-left:1px solid #e0e0e0;">
                      ${teamMembers.map((a) => {
                        const member = a?.member || a;
                        const u = a?.assigned_by_user;
                        let ab = "";
                        if (u && (u.name || u.lastname)) ab = `${safeStr(u.lastname)} ${safeStr(u.name)}`.trim();
                        else if (a?.assigned_by) ab = `Mitarbeiter #${a.assigned_by}`;
                        const at = safeStr(a?.assigned_at || "").trim();
                        return avatarLiFromEmp(member, { withData: true, assignedBy: ab, assignedAt: at, stageLabel });
                      }).join("")}
                    </ul>`
                  : "";

                return `<div class="d-flex align-items-center">${mainHtml}${teamHtml}</div>`;
              }

              // ---------------------------------------------------------
              // 6. MAIN FUNCTION: Build Table Row
              // ---------------------------------------------------------
              function buildRowHTML(lead) {
                    // 1. Define helper 'esc' immediately to avoid errors
                    const safeStr = (v) => (v == null ? "" : String(v));
                    const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        '"': "&quot;",
                        "'": "&#039;"
                    })[m]);

                    // Helper for Date formatting
                    const fmtDE = (v) => {
                        try {
                            return v ? new Date(v).toLocaleDateString("de-DE") : "-";
                        } catch {
                            return "-";
                        }
                    };

                    // 2. DEFINE DISPLAY NAME HERE (This fixes the "not defined" error!)
                    const cName = safeStr(lead?.customer_name).trim();
                    const cLastname = safeStr(lead?.customer_lastname).trim();
                    const cFirma = safeStr(lead?.firma).trim();
                    const displayName = `${cLastname} ${cName}`.trim() || cFirma || "Unbekannt";

                    const stageKey = (window.LeadUI && window.LeadUI.utils) ? window.LeadUI.utils.canonicalStage(lead?.stage) : (lead?.stage || "lead");
                    const cId = lead?.customer_id ?? "";
                    const aId = lead?.alternative_id ?? "";
                    const pId = lead?.product_id ?? "";
                    const lpId = lead?.lead_product_id ?? "";
                    const ws = String(lead?.work_status || "playing").toLowerCase();

                    // 3. Get Status Block from Kanban (Core)
                    const statusBlockHTML = (window.LeadUI && window.LeadUI.kanban) ? window.LeadUI.kanban.buildStatusBlock(lead) : `<span class="badge badge-secondary">${stageKey}</span>`;

                    // 4. Get Live Feed HTML
                    const liveFeedRow = typeof listFeedHTML === 'function' ? listFeedHTML() : '';

                    // 5. Meta Logic (Assigned By...)
                    const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                    let teamsRaw = lead?.teams;
                    if (typeof teamsRaw === "string") {
                        try { teamsRaw = JSON.parse(teamsRaw); } catch { teamsRaw = []; }
                    }
                    if (!Array.isArray(teamsRaw)) teamsRaw = [];

                    const assignments = teamAssignments.length ?
                        teamAssignments :
                        teamsRaw.map((t) => ({
                            assigned_at: t?.assigned_at ?? null,
                            assigned_at_iso: t?.assigned_at_iso ?? null,
                            assigned_by: t?.assigned_by ?? null,
                            assigned_by_user: t?.assigned_by_user ?? null,
                            stage_label: t?.stage_label ?? null,
                        }));

                    const parseAssignedAt = (a) => {
                        const raw = (a?.assigned_at_iso || a?.assigned_at || "").trim();
                        if (!raw) return 0;
                        const isoish = raw.includes("T") ? raw : raw.replace(" ", "T");
                        const ts = Date.parse(isoish);
                        return Number.isFinite(ts) ? ts : 0;
                    };

                    const latestA = assignments.reduce((best, a) => {
                        const ta = parseAssignedAt(a);
                        const tb = parseAssignedAt(best);
                        return ta > tb ? a : best;
                    }, null);

                    const assignedBy = (() => {
                        const u = latestA?.assigned_by_user;
                        if (u && (u.name || u.lastname)) return `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
                        const id = Number(latestA?.assigned_by ?? 0);
                        return id > 0 ? `Mitarbeiter #${id}` : "";
                    })();

                    const assignedAtRaw = (latestA?.assigned_at_iso || latestA?.assigned_at || "").trim();
                    const STAGE_DE = {
                        lead: "Lead",
                        offer: "Angebot",
                        follow_up: "Nachfassen",
                        accepted: "Annehmen",
                        deal: "Auftrag",
                        project: "Montage",
                        completed: "Abschluss",
                        archive: "Archiv",
                        junk: "Junk"
                    };

                    const phaseLabel = (() => {
                        const lbl = (latestA?.stage_label || "").trim();
                        if (lbl) return lbl;
                        const key = String(latestA?.stage || "").trim().toLowerCase();
                        return STAGE_DE[key] || "";
                    })();

                    const assignedMetaHTML =
                        assignedBy || assignedAtRaw || phaseLabel ?
                        `<div class="small text-muted mt-1">
                              ${phaseLabel ? `<span class="mr-2"><i class="feather icon-layers mr-25"></i><span>Phase: <strong>${esc(phaseLabel)}</strong></span></span><span class="mx-1">•</span>` : ``}
                              <i class="feather icon-user mr-25"></i><span>Zugewiesen von: <strong>${esc(assignedBy || "-")}</strong></span>
                              <span class="mx-1">•</span>
                              <i class="feather icon-calendar mr-25"></i><span>${esc(assignedAtRaw ? fmtDE(assignedAtRaw) : "-")}</span>
                            </div>` :
                        "";

                    return `
                        <tr id="row-${esc(lpId)}"
                            class="list-row-item"
                            data-customer-id="${esc(cId)}"
                            data-alternative-id="${esc(aId)}"
                            data-product-id="${esc(pId)}"
                            data-lead-product-id="${esc(lpId)}"
                            data-stage="${esc(stageKey)}"
                            data-product-stage-id="${esc(lead?.product_stage_id || '')}"
                            data-product-task-phase-id="${esc(lead?.product_task_phase_id || '')}"
                            data-product-stage-name="${esc(lead?.product_stage_name || '')}"
                            data-product-task-phase-name="${esc(lead?.product_task_phase_name || '')}"
                            data-initial="${esc(lead?.initial || '')}"
                            data-run-state="${esc(ws)}"
                            data-stage-history="${esc(typeof lead?.stage_history === 'string' ? lead?.stage_history : JSON.stringify(lead?.stage_history || []))}"
                            data-team-assignments="${esc(JSON.stringify(Array.isArray(lead?.team_assignments) ? lead.team_assignments : []))}">

                          <td style="width: 110px;" class="list-date-cell">
                            ${window.LeadUI.utils.getDateAgeIndicator(lead?.created_at, stageKey)}
                            ${lead?.created_at ? fmtDE(lead.created_at) : "-"}
                          </td>

                          <td style="min-width: 350px;">
                            <a href="/new_lead_profile/${encodeURIComponent(safeStr(cId))}" class="customer-link" style="font-size:1.05rem;">
                              ${esc(displayName)}
                            </a>

                            ${assignedMetaHTML}

                            <div class="list-action-bar">
                              <button type="button" class="btn-list-icon" data-menu="termin" title="Termin">
                                <i class="feather icon-calendar"></i>
                                <span class="badge-notes" data-ap-count style="display:none">0</span>
                              </button>
                              <button type="button" class="btn-list-icon" data-menu="aufgabe" title="Aufgabe">
                                <i class="feather icon-check-square"></i>
                                <span class="badge-notes" data-pt-count style="display:none">0</span>
                              </button>

                              <span style="border-left:1px solid #ddd; height:14px; margin:0 4px;"></span>

                              <button type="button" class="btn-list-icon note" data-open-notes data-customer="${esc(cId)}" data-alt="${esc(aId)}" data-product="${esc(pId)}">
                                <i class="feather icon-message-square"></i>
                                <span class="badge-notes" data-count="0" style="display:none">0</span>
                              </button>

                              <div class="btn-group">
                                <button type="button" class="btn-list-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="feather icon-more-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item text-success" data-run="playing"><i class="feather icon-play mr-50"></i> Start</button>
                                    <button class="dropdown-item text-warning" data-run="paused"><i class="feather icon-pause mr-50"></i> Pause</button>
                                    <button class="dropdown-item text-danger" data-run="stopped"><i class="feather icon-square mr-50"></i> Stopp</button>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/lead/process/history/${encodeURIComponent(safeStr(cId))}/${encodeURIComponent(safeStr(aId))}/${encodeURIComponent(safeStr(pId))}" data-lh-history>
                                        <i class="feather icon-activity mr-50"></i> Verlauf
                                    </a>
                                    <button type="button" class="dropdown-item" data-menu="product-stage-info">
                                        <i class="feather icon-info mr-50"></i> Produktstatus
                                    </button>
                                </div>
                              </div>
                            </div>

                            ${liveFeedRow}
                          </td>

                          <td>${esc(lead?.city ?? "")}</td>
                          <td>${esc(lead?.initial ?? "")}</td>
                          <td>${typeof listEmpAndTeamHTML === 'function' ? listEmpAndTeamHTML(lead) : ''}</td>

                          <td>
                            ${statusBlockHTML}
                            ${(window.LeadUI && window.LeadUI.kanban && typeof window.LeadUI.kanban.offerWorkflowHTML === "function") ? window.LeadUI.kanban.offerWorkflowHTML(lead) : ""}
                          </td>

                          <td>
                            <select class="form-control stage-select" data-id="${esc(lpId)}">
                              ${Object.entries((window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.stageNames) || {})
                                .filter(([k]) => !["junk", "ticket"].includes(String(k).toLowerCase()))
                                .map(([k, l]) => {
                                  const meta = (window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.stageMeta && window.LeadUI.APP.stageMeta[k]) || {};
                                  return `<option value="${esc(k)}" data-color="${esc(meta.color || "#93c21c")}" data-icon="${esc(meta.icon || "circle")}" ${stageKey === k ? "selected" : ""}>${esc(l)}</option>`;
                                })
                                .join("")}
                            </select>
                          </td>
                        </tr>
                      `;
              }
              // ---------------------------------------------------------
              // 7. Bootstrapper: Activates the Feed on List Load
              // ---------------------------------------------------------
              function bootstrapListLiveFeed(container) {
                  if (!liveFeed || typeof liveFeed.loadForCard !== "function") return;

                  const root = container || document;
                  // MATCH the class used in buildRowHTML (list-row-item)
                  const rows = root.querySelectorAll("tr.list-row-item");

                  if (!rows.length) return;

                  let i = 0;
                  const BATCH = 4;

                  (function pump() {
                    const slice = Array.prototype.slice.call(rows, i, i + BATCH);
                    i += BATCH;
                    slice.forEach((row) => {
                        if(row.dataset.customerId) {
                            // This call makes the AJAX request and removes 'display:none' if data is found
                            liveFeed.loadForCard(row);
                        }
                    });
                    if (i < rows.length) {
                        if ("requestIdleCallback" in window) requestIdleCallback(pump);
                        else setTimeout(pump, 0);
                    }
                  })();
              }

              // ---------------------------------------------------------
              // 8. Update & Fetch Logic
              // ---------------------------------------------------------
              function updateListView(leads, meta) {
                const tbody = utils.qs("#kanbanTableBody");
                if (!tbody) return;

                if (!Array.isArray(leads) || !leads.length) { 
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center p-3 text-muted">Keine Ergebnisse gefunden.</td></tr>'; 
                    return; 
                }

                // Inject HTML
                tbody.innerHTML = leads.map(buildRowHTML).join("");

                // Activate Features
                notes.updateNoteBadgesForVisibleCards(); 
                bootstrapListLiveFeed(tbody);
                if(utils.featherRefreshSoon) utils.featherRefreshSoon();
              }

              // Expose only functions that exist in this script scope.
              // fetchKanbanView is defined in the main Kanban script below, so referencing it here directly breaks the page.
              if (typeof fetchKanbanView === "function") {
                window.LeadUIFetchKanban = fetchKanbanView;
              }
              if (typeof fetchListView === "function") {
                window.LeadUIFetchList = fetchListView;
              }

              function fetchListView(qsStr) {
                  net.safeFetchJSON(`${APP.endpoints.listSearch}?${qsStr}`).then(res => {
                      const leads = res.leads || res.data || [];
                      updateListView(leads);
                  });
              }

              // Initial Load
              document.addEventListener("DOMContentLoaded", () => {
                fetchListView(""); 
              });

              /* ---------------------------------------------------------
                9. EVENT LISTENERS
              --------------------------------------------------------- */

              // A. Notes Click 
              document.addEventListener("click", (e) => {
                  const btn = e.target.closest("[data-open-notes]");
                  if (!btn) return;
                  e.stopPropagation();

                  let name = "Kunde";

                  // Check if we are in the List view
                  const row = btn.closest("tr");
                  if (row) {
                      const link = row.querySelector(".customer-link");
                      if (link) name = link.textContent.trim();
                  } else {
                      // Check if we are in the Kanban view
                      const card = btn.closest(".card");
                      if (card) {
                          const nameEl = card.querySelector(".card-name");
                          if (nameEl) name = nameEl.textContent.trim();
                      }
                  }

                  // Extract the lead_product_list_id from the row or card wrapper
                  const wrapper = row || btn.closest(".card");
                  const leadProductId = wrapper ? wrapper.dataset.leadProductId : null;

                  // Pass the leadProductId as the 5th parameter
                  notes.openNotesDrawerFor(
                      btn.dataset.customer,
                      btn.dataset.alt,
                      btn.dataset.product,
                      `Notizen • ${name}`,
                      leadProductId
                  );
              });
              // B. Live Feed Controls (Maximize, Prev, Next)
              document.addEventListener("click", (e) => {
                  // Look for any button inside the feed controls
                  const btn = e.target.closest(".live-feed-btn");
                  if (!btn) return;

                  const feedRoot = btn.closest(".live-feed-bar");
                  if (!feedRoot) return;

                  // The wrapper is the table row
                  const wrapper = feedRoot.closest("tr.list-row-item") || feedRoot.closest(".card");
                  if (!wrapper) return;

                  e.preventDefault();
                  e.stopPropagation();

                  // 1. Maximize Button
                  if (btn.hasAttribute("data-feed-open-modal")) {
                      if (liveFeedModal && typeof liveFeedModal.openForCard === 'function') {
                          liveFeedModal.openForCard(wrapper);
                      } else {
                          console.error("LeadUI.liveFeedModal is missing or invalid.");
                      }
                  }

                  // Note: Next/Prev/Pause are usually handled via internal state in your 'liveFeed' module. 
                  // If those buttons aren't working, it's because 'liveFeed.js' likely attaches listeners 
                  // locally or not at all for dynamically added list rows. 
                  // Ensure 'liveFeed.js' uses delegation or attaches listeners on 'loadForCard'.
              });

            })();


            document.addEventListener("click", (e) => {
                const btn = e.target.closest('[data-menu]');
                if (!btn) return;

                const menuType = btn.dataset.menu;
                const card = btn.closest('.card') || btn.closest('tr'); // Works for both Kanban and List

                if (menuType === 'termin') {
                    // Trigger your existing Appointment open logic
                    const event = new CustomEvent("open-appointments", {
                        bubbles: true,
                        detail: { 
                            customerId: card.dataset.customerId, 
                            alternativeId: card.dataset.alternativeId, 
                            productId: card.dataset.productId 
                        }
                    });
                    btn.dispatchEvent(event);
                }

                if (menuType === 'aufgabe') {
                    // Trigger your existing Task open logic
                    const event = new CustomEvent("open-personal-tasks", {
                        bubbles: true,
                        detail: { 
                            customerId: card.dataset.customerId, 
                            alternativeId: card.dataset.alternativeId, 
                            productId: card.dataset.productId 
                        }
                    });
                    btn.dispatchEvent(event);
                }
            });
          </script>

          <script>
            /* =============================================================================
            * LeadUI – Interactions & Boot (Segment 2/2) — REWRITE
            * - Selection + Drag & Drop (Kanban)
            * - Stage-change flow (SweetAlert + Select2 team + optional reason)
            * - List rendering + pagination (+ LiveFeed row under each list row)
            * - Fetchers (Kanban + List)
            * - All event bindings, keyboard shortcuts
            * - Bootstrap on DOMContentLoaded
            * ============================================================================= */
            (() => {
              "use strict";

              /* -------------------------------------------------------------------------- */
              /* Guard                                                                       */
              /* -------------------------------------------------------------------------- */
              if (!window.LeadUI) {
                console.error("LeadUI missing on window.");
                return;
              }

              const { APP, State, utils, net, filters, kanban, notes, partials, liveFeed } =
                window.LeadUI;

              const {
                qs,
                qsa,
                canonicalStage,
                featherRefreshSoon,
                stageFilterExcludes,
                saveToLocal,
                restoreFromLocal,
                syncURL,
                initFromURL,
                closeOverlays,
                enforceActionVisibility,
                isBackward,
                stageRank,
                workflowLabel: workflowLabelFromCore,
                workflowStageIdFromKey: workflowStageIdFromKeyFromCore,
                escapeHTML: escapeHTMLFromCore,
              } = utils;

              const workflowLabel = typeof workflowLabelFromCore === "function"
                ? workflowLabelFromCore
                : (key) => (APP.stageWorkflow?.mode === "product"
                    ? (APP.stageWorkflow.productStageNames?.[key] || key)
                    : (APP.stageNames?.[canonicalStage(key)] || key));

              const workflowStageIdFromKey = typeof workflowStageIdFromKeyFromCore === "function"
                ? workflowStageIdFromKeyFromCore
                : (key) => {
                    const m = String(key || "").match(/^product_stage_(\d+)$/);
                    return m ? Number(m[1]) : null;
                  };

              const { safeFetchJSON, postJSON, cancel } = net;

              const {
                ensureColumns,
                colContent,
                updateCounts,
                buildStatusBlock,
                applyRunStateUI,
                renderKanbanDiff,
                renderKanbanIncremental,
                autoChunk,
              } = kanban;


              const safeStr = (v) => (v == null ? "" : String(v));

              const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({
                  "&": "&amp;",
                  "<": "&lt;",
                  ">": "&gt;",
                  '"': "&quot;",
                  "'": "&#039;"
              })[m]);

              const escapeHTML = typeof escapeHTMLFromCore === "function" ? escapeHTMLFromCore : esc;

              const fmtDE = (v) => {
                try {
                  return v ? new Date(v).toLocaleDateString("de-DE") : "-";
                } catch {
                  return "-";
                }
              };
              /* -------------------------------------------------------------------------- */
              /* Constants                                                                   */
              /* -------------------------------------------------------------------------- */
              window.KB_DND_MIME = window.KB_DND_MIME || "application/x-leadui-cards";

              const interestIcons = {
                interest: { icon: "kaufinteresse.svg", label: "Kaufinteresse" },
                intent: { icon: "kaufabsicht.svg", label: "Kaufabsicht" },
                option: { icon: "kaufoption.svg", label: "Kaufoption" },
              };

              const servicesMap = {
                complete: "Komplett",
                montage: "Montage",
                product: "Produkt",
                plan: "Planung",
                maintenance: "Wartung",
                repair: "Reparatur",
                emergency: "Notdienst",
                others: "Sonstiges",
              };

              /* -------------------------------------------------------------------------- */
              /* Small helpers                                                               */
              /* -------------------------------------------------------------------------- */

              function parseDT(raw) {
                  const s = String(raw || "").trim();
                  if (!s) return null;

                  // MySQL "YYYY-MM-DD HH:MM:SS" -> ISO-like "YYYY-MM-DDTHH:MM:SS"
                  const isoLike = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(s) ? s.replace(" ", "T") : s;

                  const d = new Date(isoLike);
                  if (!Number.isFinite(d.getTime())) return null;
                  return d;
                }

                function fmtDEDate(raw) {
                  const d = parseDT(raw);
                  return d ? d.toLocaleDateString("de-DE") : "-";
                }

                function fmtDEDateTime(raw) {
                  const d = parseDT(raw);
                  return d ? d.toLocaleString("de-DE") : "-";
                }

              const toInt = (v, def = 0) => {
                const n = Number(v);
                return Number.isFinite(n) ? n : def;
              };

              const safeJSON = (raw, fallback) => {
                try {
                  return JSON.parse(raw);
                } catch (_) {
                  return fallback;
                }
              };

              function runIdle(fn) {
                if ("requestIdleCallback" in window) window.requestIdleCallback(fn);
                else window.setTimeout(fn, 0);
              }

              function addPage(qsStr, page) {
                const p = new URLSearchParams(qsStr || "");
                p.set("page", String(page));
                return p.toString();
              }

              function isKanbanActive() {
                return qs("#home")?.classList.contains("active");
              }

              function setTabCount(selector, n) {
                const el = qs(selector);
                if (el) el.textContent = String(toInt(n, 0));
              }

              function normalizePaginationMeta(input) {
                if (!input) return null;
                const direct = input.meta || input.pagination || input;

                const cp = toInt(direct.current_page ?? direct.currentPage ?? direct.page ?? 1, 1);
                const lp = toInt(
                  direct.last_page ??
                    direct.lastPage ??
                    (direct.total && direct.per_page ? Math.ceil(toInt(direct.total, 0) / toInt(direct.per_page, 1)) : 1),
                  1
                );

                return { current_page: Math.max(1, cp), last_page: Math.max(1, lp) };
              }

              /* -------------------------------------------------------------------------- */
              /* Selection (Kanban)                                                         */
              /* -------------------------------------------------------------------------- */
              function selectCard(card, ev) {
                if (!card) return;

                const multi = !!(ev?.ctrlKey || ev?.metaKey);

                if (!multi) {
                  qsa("#kanban .card.selected").forEach((c) => c.classList.remove("selected"));
                  State.selectedIds?.clear?.();
                }

                if (!State.selectedIds) State.selectedIds = new Set();

                if (multi && State.selectedIds.has(card.id)) {
                  card.classList.remove("selected");
                  State.selectedIds.delete(card.id);
                  return;
                }

                card.classList.add("selected");
                State.selectedIds.add(card.id);
              }

              /* -------------------------------------------------------------------------- */
              /* Drag & Drop (Kanban)                                                       */
              /* -------------------------------------------------------------------------- */
              function getDragIds(card) {
                if (!State.selectedIds) State.selectedIds = new Set();
                let ids = Array.from(State.selectedIds);
                if (!ids.length || !State.selectedIds.has(card.id)) ids = [card.id];
                return ids;
              }

              function onKanbanDragStart(ev, card) {
                if (!ev?.dataTransfer || !card) return;
                const ids = getDragIds(card);

                // Use a custom MIME to avoid browser default "open new tab" behavior elsewhere.
                ev.dataTransfer.setData(window.KB_DND_MIME, JSON.stringify(ids));
                ev.dataTransfer.effectAllowed = "move";
              }

              function refreshCardStatus(card, overrides = {}) {
                const s = canonicalStage(overrides.stage || card.dataset.stage || card.closest(".column")?.id || "lead");
                const ws = String(overrides.work_status || card.dataset.runState || "playing").toLowerCase();
                const stamp = overrides.updated_at || card.dataset.updatedAt || card.dataset.doneDate || new Date().toISOString();

                card.dataset.stage = s;

                if (overrides.latest_phase != null) card.dataset.latestPhase = overrides.latest_phase;
                if (overrides.latest_activity != null) card.dataset.latestActivity = overrides.latest_activity;
                if (overrides.updated_at != null) card.dataset.updatedAt = overrides.updated_at;

                const old = card.querySelector(".kb-status");
                if (old) {
                  old.outerHTML = buildStatusBlock({
                    stage: s,
                    work_status: ws,
                    latest_phase: overrides.latest_phase ?? card.dataset.latestPhase ?? "-",
                    latest_activity: overrides.latest_activity ?? card.dataset.latestActivity ?? "-",
                    updated_at: stamp,
                    done_date: stamp,
                  });
                }

                applyRunStateUI(card, ["playing", "paused", "stopped"].includes(ws) ? ws : "playing");
                featherRefreshSoon();
              }

             function moveOrRefreshKanbanCard({ newStage, cardFromDOM }) {
                const card = cardFromDOM;
                if (!card) return;

                if (stageFilterExcludes(newStage)) {
                  card.remove();
                } else {
                  const targetCol = colContent(newStage);
                  if (targetCol && card.parentElement !== targetCol) {
                      targetCol.prepend(card);
                      // Ensure moved/latest card is visible at top when dropped into a new column
                      card.style.display = ''; 
                  }

                  refreshCardStatus(card, { stage: newStage, updated_at: new Date().toISOString() });
                  if (targetCol && card.parentElement === targetCol) {
                    targetCol.prepend(card);
                    card.style.display = "";
                  }

                  card.classList.remove("selected");
                  State.selectedIds?.delete?.(card.id);
                }

                updateCounts();
              }


              window.orderedStageEntries = window.orderedStageEntries || function (namesObj) {
                const names = namesObj || {};
                const meta = window.APP?.kanbanStageMeta || window.APP?.stageMeta || {};

                return Object.entries(names)
                  .filter(([key]) => !["junk", "ticket"].includes(String(key).toLowerCase()))
                  .sort((a, b) => {
                    const ao = Number(meta?.[a[0]]?.sort_order ?? 999999);
                    const bo = Number(meta?.[b[0]]?.sort_order ?? 999999);

                    if (ao !== bo) return ao - bo;

                    return String(a[1] || a[0]).localeCompare(String(b[1] || b[0]), "de");
                  });
              };

              function buildStageTeamHistoryHTML(assignments = [], currentStage = null) {
                const arr = Array.isArray(assignments) ? assignments : [];

                const getOrderedStageEntriesSafe = () => {
                  const names = APP.stageNames || {};
                  const meta = APP.kanbanStageMeta || APP.stageMeta || {};

                  return Object.entries(names)
                    .filter(([key]) => !["junk", "ticket"].includes(canonicalStage(key)))
                    .sort((a, b) => {
                      const ak = canonicalStage(a[0]);
                      const bk = canonicalStage(b[0]);

                      const ao = Number(meta?.[ak]?.sort_order ?? meta?.[a[0]]?.sort_order ?? 999999);
                      const bo = Number(meta?.[bk]?.sort_order ?? meta?.[b[0]]?.sort_order ?? 999999);

                      if (ao !== bo) return ao - bo;

                      return String(a[1] || ak).localeCompare(String(b[1] || bk), "de");
                    });
                };

                const orderedStages = getOrderedStageEntriesSafe();
                const currentKey = currentStage ? canonicalStage(currentStage) : null;

                const currentIdx = currentKey
                  ? orderedStages.findIndex(([key]) => canonicalStage(key) === currentKey)
                  : -1;

                const visibleStages = currentIdx >= 0
                  ? orderedStages.slice(0, currentIdx + 1)
                  : orderedStages;

                const byStage = new Map();

                arr.forEach((a) => {
                  const st = canonicalStage(a?.stage || currentStage || "lead");

                  if (!byStage.has(st)) {
                    byStage.set(st, []);
                  }

                  byStage.get(st).push(a);
                });

                return `
                  <div class="mb-3">
                    <label class="small text-muted font-weight-bold text-uppercase d-block mb-2">
                      Bisherige Teams je Phase
                    </label>

                    <div class="swal-stage-team-grid">
                      ${visibleStages.map(([stageKey, stageLabel]) => {
                        const stage = canonicalStage(stageKey);
                        const members = byStage.get(stage) || [];
                        const isCurrent = currentKey && stage === currentKey;

                        return `
                          <div class="swal-stage-team-row ${isCurrent ? "is-current-stage" : ""}">
                            <div class="swal-stage-team-title">
                              ${esc(stageLabel || APP.stageNames?.[stage] || stage)}
                            </div>

                            <div>
                              ${
                                members.length
                                  ? members.map((x) => {
                                      const emp = x?.member || {};
                                      const name = `${emp?.lastname || ""} ${emp?.name || ""}`.trim()
                                        || `Mitarbeiter #${x?.employee_id || ""}`;

                                      const u = x?.assigned_by_user || {};
                                      const by = `${u?.lastname || ""} ${u?.name || ""}`.trim()
                                        || (x?.assigned_by ? `Mitarbeiter #${x.assigned_by}` : "-");

                                      const at = x?.assigned_at ? fmtDEDateTime(x.assigned_at) : "-";

                                      return `
                                        <div class="swal-stage-team-member">
                                          <strong>${esc(name)}</strong><br>
                                          <span class="text-muted">
                                            von ${esc(by)} • ${esc(at)}
                                          </span>
                                        </div>
                                      `;
                                    }).join("")
                                  : `<div class="swal-stage-team-empty">Kein Team gespeichert</div>`
                              }
                            </div>
                          </div>
                        `;
                      }).join("")}
                    </div>
                  </div>
                `;
              }

              /* -------------------------------------------------------------------------- */
              /* Stage-change confirm (SweetAlert + Select2 team + reason)                   */
              /* -------------------------------------------------------------------------- */
              async function loadProductStagesForModal(productId) {
                const pid = toInt(productId || 0);
                if (!pid) return { names: {}, meta: {}, stages: [] };

                const currentPid = toInt(APP.stageWorkflow?.productId || 0);
                const hasCurrent = currentPid === pid && APP.stageWorkflow?.productStages?.length;
                if (hasCurrent) {
                  return {
                    names: APP.stageWorkflow.productStageNames || {},
                    meta: APP.stageWorkflow.productStageMeta || {},
                    stages: APP.stageWorkflow.productStages || [],
                  };
                }

                try {
                  const res = await safeFetchJSON(`${APP.endpoints.stageWorkflowConfig}?mode=product&product_id=${encodeURIComponent(pid)}`);
                  if (!res?.success) return { names: {}, meta: {}, stages: [] };

                  const names = {};
                  const meta = {};
                  (res.stages || []).forEach((stage, idx) => {
                    const key = `product_stage_${stage.id}`;
                    names[key] = stage.name || `Produktphase #${stage.id}`;
                    meta[key] = {
                      id: stage.id,
                      key,
                      color: stage.color || "#93c21c",
                      icon: stage.icon || "layers",
                      sort_order: Number(stage.sort_order ?? ((idx + 1) * 10)),
                      phases: Array.isArray(stage.phases) ? stage.phases : [],
                      product_id: stage.product_id,
                      section_name: stage.section_name || "",
                    };
                  });

                  return { names, meta, stages: res.stages || [] };
                } catch (e) {
                  console.warn("Product stages could not be loaded for modal", e);
                  return { names: {}, meta: {}, stages: [] };
                }
              }

              function buildReadonlyTargetBox({ title, sub, icon = "arrow-right" }) {
                return `
                  <div class="swal-enterprise-target">
                    <div class="swal-enterprise-target-icon"><i class="feather icon-${escapeHTML(icon)}"></i></div>
                    <div>
                      <div class="swal-enterprise-target-title">${escapeHTML(title || "Zielphase")}</div>
                      <div class="swal-enterprise-target-sub">${escapeHTML(sub || "Die Ablage-Spalte entscheidet den Status.")}</div>
                    </div>
                  </div>`;
              }

              function buildProductStageSelectBox(productWorkflow, selectedKey = null, allowForward = true) {
                const names = productWorkflow?.names || {};
                const meta = productWorkflow?.meta || {};
                const entries = orderedStageEntries(names);
                const currentKey = selectedKey || entries?.[0]?.[0] || "";

                const productOptions = entries.map(([key, label]) => {
                  const id = workflowStageIdFromKey(key);
                  const icon = meta?.[key]?.icon || "layers";
                  const section = meta?.[key]?.section_name || "";
                  const selected = String(key) === String(currentKey) ? "selected" : "";
                  return `<option value="${id}" data-key="${escapeHTML(key)}" data-icon="${escapeHTML(icon)}" data-section="${escapeHTML(section)}" ${selected}>${escapeHTML(label)}</option>`;
                }).join("");

                const currentMeta = currentKey ? (meta?.[currentKey] || {}) : {};
                const taskOptions = [`<option value="">Keine Unterphase</option>`]
                  .concat((currentMeta.phases || []).map((phase) => `<option value="${phase.id}">${escapeHTML(phase.name || phase.phase_name || ('Phase #' + phase.id))}</option>`))
                  .join("");

                return `
                  <div class="swal-product-info-box">
                    <label class="small text-muted font-weight-bold text-uppercase">Produktstatus / Produktphase</label>
                    <div class="small text-muted mb-2">Optional: Produktfortschritt direkt mitführen, ohne die Unternehmensspalte zu wechseln.</div>
                    <div class="swal-product-info-grid">
                      <div class="form-group mb-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Produktphase</label>
                        <select id="swal-product-stage" class="form-control" style="width:100%;">${productOptions || '<option value="">Keine Produktphasen</option>'}</select>
                      </div>
                      <div class="form-group mb-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Unterphase</label>
                        <select id="swal-product-task-phase" class="form-control" style="width:100%;">${taskOptions}</select>
                      </div>
                    </div>
                    ${allowForward ? `<button type="button" id="swal-move-forward" class="swal-workflow-forward"><i class="feather icon-arrow-right"></i> Eine Produktphase weiter</button>` : ``}
                  </div>`;
              }

              function productStageInfoHTMLFromDataset(data = {}) {
                const stageName = data.productStageName || data.product_stage_name || "Noch keine Produktphase";
                const phaseName = data.productTaskPhaseName || data.product_task_phase_name || "Keine Unterphase";
                const productName = data.productName || data.initial || data.product || "Produkt";
                const mode = APP.stageWorkflow?.mode === "product" ? "Produkt-Workflow" : "Unternehmen-Workflow";
                return `
                  <div class="product-stage-info-card">
                    <div class="product-stage-info-row">
                      <i class="feather icon-box"></i>
                      <div><strong>Produkt</strong><br><span class="text-muted">${escapeHTML(productName)}</span></div>
                    </div>
                    <div class="product-stage-info-row">
                      <i class="feather icon-layers"></i>
                      <div><strong>Aktuelle Produktphase</strong><br><span class="text-muted">${escapeHTML(stageName)}</span></div>
                    </div>
                    <div class="product-stage-info-row">
                      <i class="feather icon-list"></i>
                      <div><strong>Unterphase</strong><br><span class="text-muted">${escapeHTML(phaseName)}</span></div>
                    </div>
                    <div class="product-stage-info-row">
                      <i class="feather icon-briefcase"></i>
                      <div><strong>Ansicht</strong><br><span class="text-muted">${escapeHTML(mode)}</span></div>
                    </div>
                  </div>`;
              }


              window.escapeHTML = window.escapeHTML || function (value) {
                  return String(value ?? '')
                      .replace(/&/g, '&amp;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;');
              };

              window.featherRefreshSoon = window.featherRefreshSoon || function () {
                  setTimeout(function () {
                      if (window.feather && typeof window.feather.replace === 'function') {
                          window.feather.replace();
                      }
                  }, 30);
              };

              window.productStageInfoHTMLFromDataset = function (data = {}) {
                  const stageName =
                      data.productStageName ||
                      data.product_stage_name ||
                      data.productStage ||
                      'Noch keine Produktphase';

                  const phaseName =
                      data.productTaskPhaseName ||
                      data.product_task_phase_name ||
                      data.productTaskPhase ||
                      'Keine Unterphase';

                  const productName =
                      data.productName ||
                      data.initial ||
                      data.product ||
                      'Produkt';

                  const companyStage =
                      data.companyStage ||
                      data.stage ||
                      'Unternehmen';

                  const mode =
                      window.APP?.stageWorkflow?.mode === 'product'
                          ? 'Produkt-Workflow'
                          : 'Unternehmen-Workflow';

                  return `
                      <div class="product-stage-info-card" style="text-align:left;">
                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-box"></i>
                              <div>
                                  <strong>Produkt</strong><br>
                                  <span class="text-muted">${window.escapeHTML(productName)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-layers"></i>
                              <div>
                                  <strong>Aktuelle Produktphase</strong><br>
                                  <span class="text-muted">${window.escapeHTML(stageName)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-list"></i>
                              <div>
                                  <strong>Unterphase</strong><br>
                                  <span class="text-muted">${window.escapeHTML(phaseName)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;margin-bottom:12px;">
                              <i class="feather icon-briefcase"></i>
                              <div>
                                  <strong>Unternehmensphase</strong><br>
                                  <span class="text-muted">${window.escapeHTML(companyStage)}</span>
                              </div>
                          </div>

                          <div class="product-stage-info-row" style="display:flex;gap:10px;">
                              <i class="feather icon-eye"></i>
                              <div>
                                  <strong>Ansicht</strong><br>
                                  <span class="text-muted">${window.escapeHTML(mode)}</span>
                              </div>
                          </div>
                      </div>
                  `;
              };

              window.showProductStageInfoFromElement = function (el) {
                  const d = el?.dataset || {};

                  if (!window.Swal) {
                      alert(
                          'Produktstatus:\n\n' +
                          'Produkt: ' + (d.productName || d.initial || d.product || 'Produkt') + '\n' +
                          'Produktphase: ' + (d.productStageName || d.product_stage_name || 'Noch keine Produktphase') + '\n' +
                          'Unterphase: ' + (d.productTaskPhaseName || d.product_task_phase_name || 'Keine Unterphase')
                      );
                      return;
                  }

                  Swal.fire({
                      title: 'Produktstatus',
                      html: window.productStageInfoHTMLFromDataset(d),
                      width: 520,
                      confirmButtonText: 'Schließen',
                      customClass: {
                          popup: 'swal-product-stage-info-popup'
                      },
                      didOpen: function () {
                          window.featherRefreshSoon();
                      }
                  });
              };

              function showProductStageInfoFromElement(el) {
                const d = el?.dataset || {};
                Swal.fire({
                  title: "Produktstatus",
                  html: productStageInfoHTMLFromDataset(d),
                  width: 520,
                  confirmButtonText: "Schließen",
                  didOpen: () => featherRefreshSoon(),
                });
              }

              /* -------------------------------------------------------------------------- */
              /* Stage-change confirm (Enterprise Workflow)                                 */
              /* -------------------------------------------------------------------------- */


              function tomorrowDateValue() {
                const d = new Date();
                d.setDate(d.getDate() + 1);
                return d.toISOString().slice(0, 10);
              }

              function buildStageReminderBox(opts = {}) {
                const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];
                const selectedEmployeeId = toInt(opts.employeeId || 0);
                const employeeOptions = [`<option value="">Automatisch / Keine</option>`].concat(employees.map((emp) => {
                  const id = toInt(emp.id);
                  const selected = selectedEmployeeId && selectedEmployeeId === id ? 'selected' : '';
                  const text = `${emp.lastname || ""} ${emp.name || ""}`.trim() || `Mitarbeiter #${id}`;
                  return `<option value="${id}" ${selected}>${escapeHTML(text)}</option>`;
                })).join("");

                const title = opts.title || 'Nächster Schritt';
                const description = opts.description || '';
                return `
                  <div class="swal-reminder-toggle-box">
                    <div class="swal-reminder-toggle-head">
                      <div class="swal-reminder-toggle-title"><i class="feather icon-bell"></i> Nächster Schritt / Erinnerung</div>
                      <label class="swal-reminder-switch">
                        <input type="checkbox" id="swal-create-reminder">
                        <span class="swal-reminder-slider"></span>
                        <span>Erstellen</span>
                      </label>
                    </div>

                    <div id="swal-reminder-fields" class="swal-reminder-fields">
                      <div class="swal-reminder-grid">
                        <div class="swal-reminder-field swal-reminder-field-full">
                          <label>Titel *</label>
                          <input type="text" id="swal-reminder-title" value="${escapeHTML(title)}" placeholder="z. B. Kunde morgen anrufen">
                        </div>
                        <div class="swal-reminder-field swal-reminder-field-full">
                          <label>Beschreibung</label>
                          <textarea id="swal-reminder-description" placeholder="Was ist der nächste Schritt?">${escapeHTML(description)}</textarea>
                        </div>
                      </div>
                      <div class="swal-reminder-grid-3 mt-2">
                        <div class="swal-reminder-field">
                          <label>Datum *</label>
                          <input type="date" id="swal-reminder-date" value="${tomorrowDateValue()}">
                        </div>
                        <div class="swal-reminder-field">
                          <label>Uhrzeit</label>
                          <input type="time" id="swal-reminder-time" value="09:00">
                        </div>
                        <div class="swal-reminder-field">
                          <label>Priorität</label>
                          <select id="swal-reminder-priority">
                            <option value="normal">Normal</option>
                            <option value="important" selected>Wichtig</option>
                            <option value="critical">Kritisch</option>
                          </select>
                        </div>
                      </div>
                      <div class="swal-reminder-grid mt-2">
                        <div class="swal-reminder-field">
                          <label>Verantwortlich</label>
                          <select id="swal-reminder-employee" style="width:100%;">${employeeOptions}</select>
                        </div>
                        <div class="swal-reminder-field">
                          <label>Abteilung</label>
                          <input type="number" id="swal-reminder-department" value="${escapeHTML(opts.departmentId || '')}" placeholder="Optional">
                        </div>
                      </div>
                    </div>
                  </div>`;
              }

              async function createReminderFromStageChange(context = {}, reminder = null) {
                if (!reminder || !reminder.enabled) return null;
                const leadProductId = toInt(context.leadProductId || context.lead_product_list_id || 0);
                if (!leadProductId) throw new Error('LeadProduct-ID fehlt für die Erinnerung.');

                const payload = {
                  lead_product_list_id: leadProductId,
                  title: reminder.title || 'Nächster Schritt',
                  description: reminder.description || '',
                  reminder_date: reminder.reminder_date,
                  reminder_time: reminder.reminder_time || null,
                  priority: reminder.priority || 'normal',
                  department_id: reminder.department_id || null,
                  responsible_employee_id: reminder.responsible_employee_id || null,
                };

                const url = APP.endpoints.remindersStore || "{{ url('/kanban/reminders') }}";
                const data = await postJSON(url, payload);
                if (!data?.status && !data?.success) throw new Error(data?.message || 'Erinnerung konnte nicht gespeichert werden.');
                return data;
              }

              async function confirmStageChange(newStage, currentStage, currentTeamIds = [], opts = {}) {
                const workflowMode = APP.stageWorkflow?.mode === "product" ? "product" : "company";
                const isProductWorkflow = workflowMode === "product";
                const labelNew = workflowLabel(newStage);
                const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];
                const teamSet = new Set((currentTeamIds || []).map((x) => toInt(x)));
                const modalProductId = toInt(opts.productId || APP.stageWorkflow?.productId || 0);
                const productWorkflow = await loadProductStagesForModal(modalProductId);

                const removedIds = (opts.removedTeamIds || []).map((x) => toInt(x)).filter(Boolean);
                const removedListHTML = removedIds.length
                  ? `<div class="mb-3 p-2" style="border:1px solid #f1c40f;background:#fff8e1;border-radius:8px;">
                      <div class="font-weight-bold mb-1">Achtung: Rückwärtswechsel</div>
                      <div class="small text-muted mb-2">Folgende Mitarbeiter werden in der vorherigen Phase nicht übernommen:</div>
                      <ul class="mb-0" style="padding-left:18px;">
                        ${removedIds.map((id) => {
                          const emp = employees.find((e) => toInt(e.id) === id);
                          const name = emp ? `${emp.lastname || ""} ${emp.name || ""}`.trim() : `#${id}`;
                          return `<li>${escapeHTML(name)}</li>`;
                        }).join("")}
                      </ul>
                    </div>`
                  : "";

                const teamOptions = employees.map((emp) => {
                  const id = toInt(emp.id);
                  const selected = teamSet.has(id) ? "selected" : "";
                  const imgUrl = emp.image ? `/images/employee/${emp.image}` : `/images/employee/noimage.png`;
                  const text = `${emp.lastname || ""} ${emp.name || ""}`.trim();
                  return `<option value="${id}" data-image="${escapeHTML(imgUrl)}" ${selected}>${escapeHTML(text)}</option>`;
                }).join("");

                const productTargetKey = isProductWorkflow
                  ? (String(newStage || "").startsWith("product_stage_") ? newStage : `product_stage_${workflowStageIdFromKey(newStage) || ""}`)
                  : (opts.productStageId ? `product_stage_${opts.productStageId}` : (opts.currentProductStageId ? `product_stage_${opts.currentProductStageId}` : Object.keys(productWorkflow.names || {})[0]));

                const workflowContent = isProductWorkflow
                  ? buildReadonlyTargetBox({
                      title: `Produktphase: ${labelNew}`,
                      sub: "Produkt-Workflow: Beim Verschieben werden nur Produktstatus, Team und Grund gespeichert.",
                      icon: "layers",
                    })
                  : buildReadonlyTargetBox({
                      title: `Unternehmensphase: ${labelNew}`,
                      sub: "Die Ablage-Spalte setzt den Hauptstatus. Unterphasen werden über den Under-Stage-Board verwaltet.",
                      icon: "briefcase",
                    });

                const htmlContent = `
                  <div style="text-align:left; overflow:visible;">
                    ${removedListHTML}
                    ${buildStageTeamHistoryHTML(opts.stageTeams || [], currentStage)}
                    ${workflowContent}
                    <div class="mb-3">
                      <label class="small text-muted font-weight-bold text-uppercase">Team zuweisen</label>
                      <select id="swal-team-select" class="form-control" multiple style="width:100%;">${teamOptions}</select>
                    </div>
                    <div class="mb-1">
                      <label class="small text-muted font-weight-bold text-uppercase">Grund / Notiz</label>
                      <textarea id="swal-reason-text" class="form-control" rows="3" placeholder="Optional: Grund für den Wechsel..."></textarea>
                    </div>
                    ${buildStageReminderBox({
                      title: `Nächster Schritt nach Wechsel zu ${labelNew}`,
                      description: `Bitte nächsten Schritt für Phase ${labelNew} prüfen.`,
                      employeeId: currentTeamIds?.[0] || opts.employeeId || '',
                      departmentId: opts.departmentId || ''
                    })}
                  </div>`;

                const formatEmployee = (state) => {
                  if (!state?.id) return state?.text || "";
                  const img = state.element?.dataset?.image;
                  if (!img) return state.text;
                  const wrap = document.createElement("span");
                  wrap.className = "employee-option";
                  wrap.innerHTML = `<img src="${img}" style="width:20px;height:20px;border-radius:999px;object-fit:cover;margin-right:8px;">${state.text}`;
                  return wrap;
                };

                const formatProductStage = (state) => {
                  if (!state?.id) return state?.text || "";
                  const icon = state.element?.dataset?.icon || "layers";
                  const section = state.element?.dataset?.section || "";
                  const wrap = document.createElement("span");
                  wrap.className = "kb-workflow-select2-option";
                  wrap.innerHTML = `<span class="kb-workflow-select2-icon"><i class="feather icon-${escapeHTML(icon)}"></i></span><span class="kb-workflow-select2-text"><span class="kb-workflow-select2-title">${escapeHTML(state.text || "")}</span><span class="kb-workflow-select2-sub">${escapeHTML(section || "Produktphase")}</span></span>`;
                  return wrap;
                };

                const result = await Swal.fire({
                  title: `Wechsel zu ${labelNew}`,
                  html: htmlContent,
                  showCancelButton: true,
                  confirmButtonText: "Speichern",
                  cancelButtonText: "Abbrechen",
                  width: isProductWorkflow ? 700 : 860,
                  customClass: { popup: "swal-overflow-visible" },
                  didOpen: () => {
                    const popup = Swal.getPopup();

                    const refreshProductTaskOptions = () => {
                      const sel = qs("#swal-product-stage", popup);
                      const taskSel = qs("#swal-product-task-phase", popup);
                      if (!sel || !taskSel) return;
                      const key = sel.selectedOptions?.[0]?.dataset?.key || `product_stage_${sel.value || ''}`;
                      const meta = productWorkflow?.meta?.[key] || {};
                      taskSel.innerHTML = `<option value="">Keine Unterphase</option>` + (meta.phases || []).map((phase) => `<option value="${phase.id}">${escapeHTML(phase.name || phase.phase_name || ('Phase #' + phase.id))}</option>`).join("");
                      if (window.jQuery && window.jQuery.fn.select2) window.jQuery(taskSel).trigger("change.select2");
                    };

                    const reminderToggle = qs("#swal-create-reminder", popup);
                    const reminderFields = qs("#swal-reminder-fields", popup);
                    reminderToggle?.addEventListener("change", () => {
                      reminderFields?.classList.toggle("is-open", reminderToggle.checked);
                    });

                    qs("#swal-product-stage", popup)?.addEventListener("change", refreshProductTaskOptions);
                    qs("#swal-move-forward", popup)?.addEventListener("click", () => {
                      const sel = qs("#swal-product-stage", popup);
                      if (!sel) return;
                      const idx = sel.selectedIndex;
                      if (idx < sel.options.length - 1) {
                        sel.selectedIndex = idx + 1;
                        sel.dispatchEvent(new Event("change"));
                        if (window.jQuery && window.jQuery.fn.select2) window.jQuery(sel).trigger("change.select2");
                      }
                    });

                    if (window.jQuery && window.jQuery.fn.select2) {
                      const selectors = isProductWorkflow
                        ? ["#swal-team-select", "#swal-reminder-employee"]
                        : ["#swal-team-select", "#swal-reminder-employee"];

                      selectors.forEach((selector) => {
                        const $sel = window.jQuery(selector);
                        if (!$sel.length) return;
                        $sel.select2({
                          dropdownParent: window.jQuery(popup),
                          width: "100%",
                          closeOnSelect: selector !== "#swal-team-select",
                          templateResult: selector === "#swal-team-select" ? formatEmployee : (selector === "#swal-product-stage" ? formatProductStage : undefined),
                          templateSelection: selector === "#swal-team-select" ? formatEmployee : (selector === "#swal-product-stage" ? formatProductStage : undefined),
                          escapeMarkup: (m) => m,
                        });
                      });
                    }

                    refreshProductTaskOptions();
                    featherRefreshSoon();
                  },
                  preConfirm: () => {
                    let teams = currentTeamIds.slice();
                    if (window.jQuery) {
                      const v = window.jQuery("#swal-team-select").val();
                      if (Array.isArray(v)) teams = v.map((x) => toInt(x)).filter(Boolean);
                    }

                    const selectedProductStageId = isProductWorkflow
                      ? (workflowStageIdFromKey(newStage) || toInt(opts.productStageId || 0) || null)
                      : null;

                    const createReminder = !!qs("#swal-create-reminder")?.checked;
                    const reminderTitle = (qs("#swal-reminder-title")?.value || "").trim();
                    const reminderDate = (qs("#swal-reminder-date")?.value || "").trim();
                    if (createReminder && (!reminderTitle || !reminderDate)) {
                      Swal.showValidationMessage("Bitte Titel und Datum für die Erinnerung ausfüllen.");
                      return false;
                    }

                    return {
                      mode: workflowMode,
                      reason: qs("#swal-reason-text")?.value || "",
                      teams,
                      companyStageKey: isProductWorkflow ? null : newStage,
                      productStageId: selectedProductStageId,
                      productTaskPhaseId: null,
                      reminder: createReminder ? {
                        enabled: true,
                        title: reminderTitle,
                        description: qs("#swal-reminder-description")?.value || "",
                        reminder_date: reminderDate,
                        reminder_time: qs("#swal-reminder-time")?.value || null,
                        priority: qs("#swal-reminder-priority")?.value || "normal",
                        responsible_employee_id: toInt(qs("#swal-reminder-employee")?.value || 0) || null,
                        department_id: toInt(qs("#swal-reminder-department")?.value || 0) || null,
                      } : null,
                    };
                  },
                });

                if (!result.isConfirmed) return { ok: false };
                return {
                  ok: true,
                  mode: workflowMode,
                  reasonHTML: result.value?.reason || "",
                  teams: Array.isArray(result.value?.teams) ? result.value.teams : [],
                  companyStageKey: isProductWorkflow ? null : newStage,
                  productStageId: result.value?.productStageId || (isProductWorkflow ? workflowStageIdFromKey(newStage) : null),
                  productTaskPhaseId: result.value?.productTaskPhaseId || null,
                  reminder: result.value?.reminder || null,
                };
              }


              function defaultLeadSubStageForStage(stageKey) {
                const key = canonicalStage(stageKey || "");
                const meta = APP.stageMeta?.[key] || APP.kanbanStageMeta?.[key] || APP.companyKanbanStageMeta?.[key] || {};
                const subs = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
                if (!subs.length) return null;
                const def = subs.find(s => s && (s.is_default === true || s.is_default === 1 || String(s.is_default) === "1"));
                return (def || subs[0])?.id || null;
              }

              function subStageMetaForStage(stageKey, subStageId) {
                const key = canonicalStage(stageKey || "");
                const meta = APP.stageMeta?.[key] || APP.kanbanStageMeta?.[key] || APP.companyKanbanStageMeta?.[key] || {};
                const subs = Array.isArray(meta.sub_stages) ? meta.sub_stages : [];
                return subs.find(s => String(s.id) === String(subStageId || "")) || null;
              }


              function formatOfferFolderPrice(value) {
                const n = Number(value || 0);
                if (!Number.isFinite(n) || n <= 0) return '';
                try {
                  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(n);
                } catch (e) {
                  return `${n.toFixed(2)} €`;
                }
              }

              async function askAcceptedOfferFolderSelection(payload) {
                const folders = Array.isArray(payload?.folders) ? payload.folders : [];

                if (!folders.length) {
                  if (window.Swal) {
                    await Swal.fire({
                      icon: 'warning',
                      title: 'Kein Angebot gefunden',
                      text: payload?.message || 'Bitte zuerst ein Angebot erstellen, bevor diese Phase verlassen wird.',
                    });
                  }
                  return null;
                }

                if (!window.Swal) {
                  return null;
                }

                const steps = Array.isArray(payload?.next_steps) ? payload.next_steps : [];
                const html = `
                  <div style="text-align:left">
                    <div style="border:1px solid #fde68a;background:#fffbeb;color:#92400e;border-radius:14px;padding:12px;margin-bottom:12px;line-height:1.55;font-size:13px;">
                      <div style="font-weight:900;margin-bottom:4px;">Warum wird der Kanban-Umzug gestoppt?</div>
                      <div>${escapeHTML(payload?.message || 'Bitte wählen Sie das angenommene Angebot aus.')}</div>
                      ${payload?.help_text ? `<div style="margin-top:8px;color:#78350f;">${escapeHTML(payload.help_text)}</div>` : ''}
                    </div>
                    ${steps.length ? `<div style="border:1px solid #e5e7eb;background:#f8fafc;border-radius:14px;padding:12px;margin-bottom:12px;font-size:12px;color:#374151;line-height:1.55;">
                      <div style="font-weight:900;margin-bottom:6px;">Was passiert danach?</div>
                      <ol style="margin:0;padding-left:18px;">${steps.map(step => `<li>${escapeHTML(step)}</li>`).join('')}</ol>
                    </div>` : ''}
                    <div style="font-size:12px;font-weight:900;color:#111827;margin-bottom:8px;">Verfügbare Angebotsordner</div>
                    <div style="display:flex;flex-direction:column;gap:10px;max-height:420px;overflow:auto;">
                      ${folders.map((folder, index) => {
                        const checked = folder.is_accepted || index === 0 ? 'checked' : '';
                        const price = formatOfferFolderPrice(folder.total_gross);
                        const doc = folder.document_status === 'deal' ? 'Auftrag' : 'Angebot';
                        const statusText = `${folder.status || '-'} · Angebot: ${folder.offer_status || '-'} · Auftrag: ${folder.deal_status || '-'}`;
                        return `
                          <label style="display:flex;gap:12px;align-items:flex-start;border:1px solid #e5e7eb;border-radius:14px;padding:12px;background:#fff;cursor:pointer;">
                            <input type="radio" name="accepted_offer_folder_id" value="${escapeHTML(folder.id)}" ${checked} style="margin-top:4px;accent-color:#93c21c;">
                            <span style="display:block;min-width:0;flex:1;">
                              <span style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                <strong style="font-size:14px;color:#111827;">${escapeHTML(folder.name || ('Ordner #' + folder.id))}</strong>
                                <span style="font-size:10px;font-weight:900;border:1px solid #d9ef9d;background:#f4fae7;color:#55720d;border-radius:999px;padding:4px 8px;white-space:nowrap;">${escapeHTML(doc)}</span>
                              </span>
                              <span style="display:block;margin-top:6px;color:#6b7280;font-size:12px;line-height:1.45;">
                                Angebot #${escapeHTML(folder.offer_id || '-')} · Ordner #${escapeHTML(folder.id || '-')} · ${escapeHTML(statusText)}
                                ${price ? ` · <strong>${escapeHTML(price)}</strong>` : ''}
                              </span>
                              <span style="display:block;margin-top:6px;color:#92400e;font-size:11px;line-height:1.4;">
                                Wenn Sie diesen Ordner auswählen, wird er Auftrag. Die anderen aktiven Ordner werden automatisch storniert.
                              </span>
                            </span>
                          </label>`;
                      }).join('')}
                    </div>
                  </div>`;

                const result = await Swal.fire({
                  icon: 'warning',
                  title: payload?.title || 'Welches Angebot wurde angenommen?',
                  html,
                  width: 760,
                  showCancelButton: true,
                  confirmButtonText: 'Dieses Angebot annehmen',
                  cancelButtonText: 'Abbrechen',
                  focusConfirm: false,
                  preConfirm: () => {
                    const selected = document.querySelector('input[name="accepted_offer_folder_id"]:checked');
                    if (!selected) {
                      Swal.showValidationMessage('Bitte ein Angebot auswählen.');
                      return false;
                    }
                    return Number(selected.value || 0);
                  },
                });

                return result.isConfirmed ? Number(result.value || 0) : null;
              }

              async function applyStageChange({
                customerId,
                alternativeId,
                productId,
                leadProductId,
                newStage,
                noteHTML,
                teams = [],
                mode = null,
                companyStageKey = null,
                productStageId = null,
                productTaskPhaseId = null,
                leadStageSubStageId = undefined,
              }) {
                const workflowMode = mode || APP.stageWorkflow?.mode || "company";
                const cleanTeams = Array.isArray(teams) ? teams.map((x) => toInt(x)).filter(Boolean) : [];

                if (workflowMode === "product") {
                  if (!leadProductId) throw new Error("LeadProduct-ID fehlt für Produkt-Workflow.");
                  const payload = {
                    mode: "product",
                    product_stage_id: productStageId || workflowStageIdFromKey(newStage),
                    product_task_phase_id: productTaskPhaseId || null,
                    reason: noteHTML || "",
                    teams: cleanTeams,
                  };
                  const data = await postJSON(APP.endpoints.stageWorkflowMove(toInt(leadProductId)), payload);
                  if (!data?.success) throw new Error(data?.message || "Fehler");
                  return data;
                }

                if (APP.endpoints.stageWorkflowMove && leadProductId) {
                  const resolvedCompanyStage = companyStageKey || newStage;
                  const resolvedSubStageId = leadStageSubStageId === undefined
                    ? defaultLeadSubStageForStage(resolvedCompanyStage)
                    : (leadStageSubStageId || null);

                  const payload = {
                    mode: "company",
                    company_stage_key: resolvedCompanyStage,
                    lead_stage_sub_stage_id: resolvedSubStageId,
                    reason: noteHTML || "",
                    teams: cleanTeams,
                  };
                  let data = await postJSON(APP.endpoints.stageWorkflowMove(toInt(leadProductId)), payload);

                  if (data?.requires_offer_selection) {
                    const acceptedFolderId = await askAcceptedOfferFolderSelection(data);
                    if (!acceptedFolderId) {
                      const cancelError = new Error('Aktion abgebrochen.');
                      cancelError.cancelled = true;
                      throw cancelError;
                    }

                    data = await postJSON(APP.endpoints.stageWorkflowMove(toInt(leadProductId)), {
                      ...payload,
                      accepted_offer_folder_id: acceptedFolderId,
                    });
                  }

                  if (data?.success) return data;
                  throw new Error(data?.message || 'Fehler');
                }

                const url = `${APP.endpoints.changeStage}/${encodeURIComponent(customerId)}/${encodeURIComponent(alternativeId)}/${encodeURIComponent(productId)}`;
                const payload = {
                  stage: companyStageKey || newStage,
                  description: noteHTML || "",
                  lead_product_id: toInt(leadProductId) || undefined,
                  teams: cleanTeams,
                };
                const data = await postJSON(url, payload);
                if (!data?.success) throw new Error(data?.message || "Fehler");
                return data;
              }

              /* -------------------------------------------------------------------------- */
              /* Kanban Drop                                                                 */
              /* -------------------------------------------------------------------------- */
              async function onKanbanDrop(ev) {
                ev.preventDefault();
                ev.stopPropagation();

                if (ev.target.closest("[data-understage-dropzone]")) return;
                const col = ev.target.closest(".column");
                if (!col) return;

                const raw = ev.dataTransfer?.getData(window.KB_DND_MIME) || "";
                const ids = Array.isArray(safeJSON(raw, [])) ? safeJSON(raw, []) : [];
                if (!ids.length) return;

                const card = qs(`#${CSS.escape(ids[0])}`);
                if (!card) return;

                const newStage = APP.stageWorkflow?.mode === "product" ? col.id : canonicalStage(col.id);
                const currentStage = APP.stageWorkflow?.mode === "product"
                  ? (card.dataset.productStageId ? `product_stage_${card.dataset.productStageId}` : (card.dataset.stage || ""))
                  : canonicalStage(card.dataset.companyStage || card.dataset.stage);
                if (currentStage === newStage) return;

                // 👇 ADDED PAUSE/STOP CHECK BLOCK 👇
                const runState = card.dataset.runState || 'playing';
                if (runState === 'paused' || runState === 'stopped') {
                    let reason = "Kein Grund angegeben.";
                    try {
                        const history = JSON.parse(card.dataset.stageHistory || "[]");
                        if (Array.isArray(history) && history.length > 0) {
                            // Get the most recent entry from the history array
                            const latest = history[history.length - 1];
                            if (latest && latest.description) {
                                reason = latest.description;
                            }
                        }
                    } catch(e) {
                        console.warn("Could not parse stage_history", e);
                    }

                    const stateDe = runState === 'paused' ? 'pausiert' : 'gestoppt';
                    Swal.fire({
                        icon: "warning",
                        title: "Aktion nicht möglich",
                        html: `Dieser Eintrag ist momentan <b>${stateDe}</b> und kann nicht verschoben werden.<br><br><b>Grund:</b> ${esc(reason)}`
                    });
                    return; // Block the drop!
                }
                // 👆 END PAUSE/STOP CHECK BLOCK 👆

                // teams from card (if you store it)
                let currentTeamIds = safeJSON(card.dataset.teamIds || "[]", []);
                if (!Array.isArray(currentTeamIds)) currentTeamIds = [];
                currentTeamIds = currentTeamIds.map((x) => toInt(x)).filter(Boolean);

                const backward = isBackward(currentStage, newStage);
                const removedTeamIds = backward ? currentTeamIds.slice() : [];

                const stageTeams = safeJSON(card.dataset.teamAssignments || "[]", []);
                const confirm = await confirmStageChange(newStage, currentStage, currentTeamIds, {
                  removedTeamIds,
                  stageTeams,
                  productId: card.dataset.productId,
                  productStageId: card.dataset.productStageId,
                  currentProductStageId: card.dataset.productStageId,
                });
                if (!confirm.ok) return;

                try {
                  const { customerId, alternativeId, productId, leadProductId } = card.dataset;

                  const stageResponse = await applyStageChange({
                    customerId,
                    alternativeId,
                    productId,
                    leadProductId,
                    newStage,
                    noteHTML: confirm.reasonHTML,
                    teams: confirm.teams,
                    mode: confirm.mode || APP.stageWorkflow?.mode || "company",
                    companyStageKey: confirm.companyStageKey || newStage,
                    productStageId: confirm.productStageId || workflowStageIdFromKey(newStage),
                    productTaskPhaseId: confirm.productTaskPhaseId || null,
                  });

                  if (confirm.reminder?.enabled) {
                    await createReminderFromStageChange({ leadProductId, customerId, alternativeId, productId }, confirm.reminder);
                    if (window.preloadLeadReminderSummaries) window.setTimeout(window.preloadLeadReminderSummaries, 350);
                  }

                  card.dataset.teamIds = JSON.stringify(stageResponse?.final?.team_ids || confirm.teams || []);
                  if (stageResponse?.final?.team_assignments) {
                    card.dataset.teamAssignments = JSON.stringify(stageResponse.final.team_assignments);
                  }
                  const finalColumnStage = (confirm.mode === "product" || APP.stageWorkflow?.mode === "product")
                    ? `product_stage_${stageResponse?.lead?.product_stage_id || confirm.productStageId || workflowStageIdFromKey(newStage)}`
                    : canonicalStage(stageResponse?.stage || stageResponse?.lead?.status || confirm.companyStageKey || newStage);
                  card.dataset.stage = finalColumnStage;
                  card.dataset.companyStage = stageResponse?.lead?.status || confirm.companyStageKey || card.dataset.companyStage || "";
                  card.dataset.productStageId = stageResponse?.lead?.product_stage_id || confirm.productStageId || card.dataset.productStageId || "";
                  card.dataset.productTaskPhaseId = stageResponse?.lead?.product_task_phase_id || confirm.productTaskPhaseId || "";
                  card.dataset.productStageName = stageResponse?.lead?.product_stage_name || stageResponse?.lead?.product_stage?.stage || card.dataset.productStageName || "";
                  card.dataset.productTaskPhaseName = stageResponse?.lead?.product_task_phase_name || stageResponse?.lead?.product_task_phase?.phase_name || card.dataset.productTaskPhaseName || "";

                  const finalSubStageId = stageResponse?.lead?.lead_stage_sub_stage_id
                    || stageResponse?.lead?.lead_sub_stage_id
                    || defaultLeadSubStageForStage(stageResponse?.lead?.status || confirm.companyStageKey || newStage)
                    || "";
                  card.dataset.leadStageSubStageId = finalSubStageId ? String(finalSubStageId) : "";
                  const finalSubMeta = subStageMetaForStage(stageResponse?.lead?.status || confirm.companyStageKey || newStage, finalSubStageId);
                  const oldSubChip = card.querySelector(".kb-understage-chip");
                  if (oldSubChip) oldSubChip.remove();
                  if (finalSubMeta?.name) {
                    const chip = document.createElement("div");
                    chip.className = "kb-understage-chip";
                    chip.innerHTML = `<i class="feather icon-git-branch"></i>${escapeHTML(finalSubMeta.name)}`;
                    const preview = card.querySelector(".kb-next-step-preview");
                    if (preview) card.insertBefore(chip, preview);
                    else card.appendChild(chip);
                  }

                  const stageHistoryFromResponse = stageResponse?.lead?.stage_history || stageResponse?.final?.stage_history || null;
                  if (stageHistoryFromResponse) {
                    card.dataset.stageHistory = typeof stageHistoryFromResponse === "string" ? stageHistoryFromResponse : JSON.stringify(stageHistoryFromResponse);
                  } else {
                    const fallbackHistory = (window.KanbanStageTime?.parseStageHistorySafe || window.parseStageHistorySafe || function(){ return []; })(card.dataset.stageHistory || "[]");
                    fallbackHistory.push({
                      from: currentStage,
                      to: newStage,
                      stage: newStage,
                      changed_at: new Date().toISOString(),
                      description: confirm.reasonHTML || "",
                    });
                    card.dataset.stageHistory = JSON.stringify(fallbackHistory);
                  }

                  (window.KanbanStageTime?.refreshCardStageTime || window.refreshCardStageTime)(card, {
                    stage: stageResponse?.stage || stageResponse?.lead?.status || confirm.companyStageKey || newStage,
                    status: stageResponse?.lead?.status || stageResponse?.stage || confirm.companyStageKey || newStage,
                    created_at: stageResponse?.lead?.created_at || card.dataset.createdAt || new Date().toISOString(),
                    updated_at: stageResponse?.lead?.updated_at || new Date().toISOString(),
                    stage_history: card.dataset.stageHistory,
                  }, finalColumnStage);

                  moveOrRefreshKanbanCard({ newStage: finalColumnStage, cardFromDOM: card });
                  enforceActionVisibility(card);

                  window.LeadUI?.silentRefreshBoth?.();

                  Swal.fire({
                    icon: "success",
                    title: "OK",
                    text: "Status & Team aktualisiert.",
                    timer: 1200,
                    showConfirmButton: false,
                  });
                } catch (err) {
                  if (err?.cancelled) return;
                  Swal.fire("Fehler", err?.message || "Serverfehler.", "error");
                }
              }
              /* -------------------------------------------------------------------------- */
              /* List rendering (+ LiveFeed row)                                             */
              /* -------------------------------------------------------------------------- */
                function priorityMeta(raw) {
                  const p = String(raw || "normal").toLowerCase();
                  if (p === "high" || p === "urgent") return { label: "Hoch", cls: "prio-high", icon: "alert-triangle" };
                  if (p === "low") return { label: "Niedrig", cls: "prio-low", icon: "arrow-down-circle" };
                  return { label: "Normal", cls: "prio-normal", icon: "circle" };
                }

                function employeeCellHTML(lead) {
                  const esc = (s) =>
                    String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                  const office = lead?.employee || null;
                  const field = lead?.field_employee || lead?.fieldEmployee || null;

                  // team can come as: team_members OR teams (array)
                  const teamArr = Array.isArray(lead?.team_members)
                    ? lead.team_members
                    : Array.isArray(lead?.teams)
                      ? lead.teams
                      : [];

                  const hasOffice = !!(office && (office.name || office.lastname));
                  const hasField  = !!(field && (field.name || field.lastname));
                  const hasTeam   = teamArr.length > 0;

                  if (!hasOffice && !hasField && !hasTeam) return "<small>&ndash;</small>";

                  const imgOrNo = (img) => (img ? esc(img) : "noimage.png");

                  const chunks = [];

                  // wrapper to align employees + team similar to blade
                  chunks.push(`<div class="d-flex align-items-start flex-wrap" style="gap:10px;">`);

                  // employee stack
                  if (hasOffice || hasField) {
                    const empChunks = [];

                    if (hasOffice) {
                      empChunks.push(`
                        <div class="d-flex align-items-center">
                          <img src="/images/employee/${imgOrNo(office.image)}" width="30" height="30" class="rounded-circle mr-1" alt="" style="object-fit:cover;">
                          <div>
                            <div style="line-height:1.1"><strong>${esc(office.lastname || "")}</strong> ${esc(office.name || "")}</div>
                            <small class="text-muted">Innendienst</small>
                          </div>
                        </div>
                      `);
                    }

                    if (hasField) {
                      empChunks.push(`
                        <div class="d-flex align-items-center">
                          <img src="/images/employee/${imgOrNo(field.image)}" width="26" height="26" class="rounded-circle mr-1" alt="" style="object-fit:cover;">
                          <div>
                            <div style="line-height:1.1"><strong>${esc(field.lastname || "")}</strong> ${esc(field.name || "")}</div>
                            <small class="text-muted">Außendienst</small>
                          </div>
                        </div>
                      `);
                    }

                    chunks.push(`<div class="d-flex flex-column" style="gap:6px;">${empChunks.join("")}</div>`);
                  }

                  // team avatars
                  if (hasTeam) {
                    const avatars = teamArr
                      .map((t) => {
                        const name = `${t?.lastname ?? ""} ${t?.name ?? ""}`.trim() || "Team";
                        const img = t?.image ? `/images/employee/${esc(t.image)}` : `/images/employee/noimage.png`;
                        return `
                          <li class="avatar pull-up" title="${esc(name)}" style="margin-left:-8px;">
                            <img class="media-object rounded-circle"
                                src="${img}"
                                width="26" height="26"
                                alt="${esc(name)}"
                                style="border:2px solid #fff; object-fit:cover;">
                          </li>`;
                      })
                      .join("");

                    chunks.push(`
                      <div class="d-flex align-items-center" style="margin-top:2px; padding-left:10px; border-left:1px solid #e0e0e0;">
                        <ul class="list-unstyled users-list m-0 d-flex align-items-center" style="gap:0; padding:0;">
                          ${avatars}
                        </ul>
                      </div>
                    `);
                  }

                  chunks.push(`</div>`);
                  return chunks.join("");
                }

                // ---------------------------------------------------------
                // 1. Helper: Live Feed HTML Structure
                // ---------------------------------------------------------
                  function listFeedHTML() {
                    return `
                    <div class="live-feed-bar list-live-feed card-live-feed"
                        data-feed-root
                        data-feed-count="0"
                        style="display:none; margin-top:0.5rem; width: 100%; max-width: 450px;">
                      <div class="live-feed-left">
                        <div class="live-feed-icon"><i class="feather icon-zap"></i></div>
                      </div>
                      <div class="live-feed-body">
                        <div class="live-feed-line" data-feed-empty>
                          <span class="live-feed-title">Keine Aktivitäten</span>
                          <span class="live-feed-dot">•</span>
                          <span class="live-feed-text">Noch keine Termine oder Aufgaben.</span>
                        </div>
                        <div class="live-feed-line" data-feed-line>
                          <span class="live-feed-title" data-feed-title>Aktivität</span>
                          <span class="live-feed-dot">•</span>
                          <span class="live-feed-text" data-feed-text>Details…</span>
                        </div>
                        <div class="live-feed-meta">
                          <span class="live-feed-pill" data-feed-pill>Info</span>
                          <span class="live-feed-time">
                            <i class="feather icon-clock mr-25"></i>
                            <span data-feed-time>–</span>
                          </span>
                          <span class="live-feed-counter" data-feed-counter></span>
                        </div>
                      </div>
                      <div class="live-feed-controls">
                        <button type="button" class="live-feed-btn" title="Zurück" data-feed-prev>
                          <i class="feather icon-skip-back"></i>
                        </button>
                        <button type="button" class="live-feed-btn" title="Pause / Abspielen" data-feed-toggle>
                          <i class="feather icon-pause" data-feed-icon-pause></i>
                          <i class="feather icon-play d-none" data-feed-icon-play></i>
                        </button>
                        <button type="button" class="live-feed-btn" title="Weiter" data-feed-next>
                          <i class="feather icon-skip-forward"></i>
                        </button>
                        <button type="button" class="live-feed-btn" title="Alle Aktivitäten anzeigen" data-feed-open-modal>
                            <i class="feather icon-maximize-2"></i>
                        </button>
                      </div>
                    </div>
                  `;
                }

                // ---------------------------------------------------------
                // 2. Helper: Avatar Generator
                // ---------------------------------------------------------
                function avatarLiFromEmp(emp, { withData = false, assignedBy = "", assignedAt = "", stage = "", stageLabel = "" } = {}) {
                  if (!emp) return "";

                  // Constants from parent scope or fallback
                  const EMP_SRC = (window.LeadUI && window.LeadUI.APP && window.LeadUI.APP.EMP_SRC) ? window.LeadUI.APP.EMP_SRC : '/images/employee';
                  const safeStr = (v) => (v == null ? "" : String(v));
                  const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                  const id = Number(emp?.employee_id ?? emp?.id ?? emp?.emp_id ?? 0) || 0;
                  const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;
                  const name = `${safeStr(emp?.lastname).trim()} ${safeStr(emp?.name).trim()}`.trim() || `#${id}`;

                  return `
                    <li class="avatar pull-up"
                        ${withData ? `data-emp-id="${esc(id)}"` : ""}
                        ${withData ? `data-assigned-by="${esc(assignedBy)}"` : ""}
                        ${withData ? `data-assigned-at="${esc(assignedAt)}"` : ""}
                        ${withData ? `data-stage="${esc(stage)}"` : ""}
                        ${withData ? `data-stage-label="${esc(stageLabel)}"` : ""}
                        title="${esc(name)}"
                        style="margin-left:-8px;">
                      <img class="media-object rounded-circle"
                          src="${esc(img)}"
                          width="26" height="26"
                          alt="${esc(name)}"
                          style="border:2px solid #fff; object-fit:cover;">
                    </li>
                  `;
                }

                // ---------------------------------------------------------
                // 3. Helper: Employee & Team Column Generator
                // ---------------------------------------------------------
                function listEmpAndTeamHTML(lead) {
                  const stageKey = window.LeadUI.utils.canonicalStage(lead?.stage);
                  const stageLabel = window.LeadUI.APP.stageNames?.[stageKey] || stageKey;
                  const safeStr = (v) => (v == null ? "" : String(v));
                  const esc = (v) => safeStr(v).replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                  const main = [];
                  if (lead?.employee && (lead.employee.employee_id || lead.employee.id)) main.push(lead.employee);
                  if (lead?.field_employee && (lead.field_employee.employee_id || lead.field_employee.id)) main.push(lead.field_employee);

                  const allAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                  const currentAssignments = Array.isArray(lead?.current_team_assignments) && lead.current_team_assignments.length
                    ? lead.current_team_assignments
                    : allAssignments.filter((a) => window.LeadUI.utils.canonicalStage(a?.stage || stageKey) === stageKey);

                  const fallbackCurrent = currentAssignments.length
                    ? currentAssignments
                    : (Array.isArray(lead?.team_members) ? lead.team_members.map((m) => ({ member: m, stage: stageKey, stage_label: stageLabel })) : []);

                  const visible = fallbackCurrent.slice(0, 2);
                  const rest = Math.max(0, fallbackCurrent.length - visible.length);

                  const mainHtml = main.length
                    ? `<ul class="list-unstyled users-list m-0 d-inline-flex align-items-center list-main-users" title="Hauptverantwortliche">
                        ${main.slice(0, 2).map((e) => avatarLiFromEmp(e, { withData: false })).join("")}
                      </ul>`
                    : "";

                  const miniAvatars = visible.map((a) => {
                    const member = a?.member || a || {};
                    const id = Number(member?.employee_id ?? member?.id ?? a?.employee_id ?? 0) || 0;
                    const img = member?.image ? `/images/employee/${member.image}` : `/images/employee/noimage.png`;
                    const name = `${safeStr(member?.lastname).trim()} ${safeStr(member?.name).trim()}`.trim() || `#${id}`;
                    const u = a?.assigned_by_user;
                    let ab = "";
                    if (u && (u.name || u.lastname)) ab = `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
                    else if (a?.assigned_by) ab = `Mitarbeiter #${a.assigned_by}`;
                    const at = safeStr(a?.assigned_at || a?.assigned_at_iso || "").trim();
                    const itemStage = window.LeadUI.utils.canonicalStage(a?.stage || stageKey);
                    const itemStageLabel = a?.stage_label || window.LeadUI.APP.stageNames?.[itemStage] || stageLabel;
                    return `<li class="avatar pull-up"
                                data-emp-id="${esc(id)}"
                                data-assigned-by="${esc(ab)}"
                                data-assigned-at="${esc(at)}"
                                data-stage="${esc(itemStage)}"
                                data-stage-label="${esc(itemStageLabel)}"
                                title="${esc(name)}">
                              <img class="media-object rounded-circle" src="${esc(img)}" width="24" height="24" alt="${esc(name)}" style="border:2px solid #fff; object-fit:cover;">
                            </li>`;
                  }).join("");

                  const teamButton = `
                    <button type="button"
                            class="kb-team-pill kb-team-pill--list"
                            data-show-stage-team="${esc(stageKey)}"
                            title="Team nach Phasen anzeigen">
                      <ul class="list-unstyled users-list m-0 d-inline-flex align-items-center" data-team-hover>
                        ${miniAvatars}
                      </ul>
                      <span>Teams</span>
                      <span class="kb-team-pill-count">${fallbackCurrent.length}</span>
                      ${rest > 0 ? `<span class="kb-team-pill-count">+${rest}</span>` : ``}
                    </button>`;

                  if (!main.length && !fallbackCurrent.length && !allAssignments.length) {
                    return `<button type="button" class="kb-team-pill kb-team-pill--list" data-show-stage-team="${esc(stageKey)}"><span>Teams</span><span class="kb-team-pill-count">0</span></button>`;
                  }

                  return `<div class="list-team-cell d-flex align-items-center" style="gap:8px; min-width:180px;">${mainHtml}${teamButton}</div>`;
                }

                // ---------------------------------------------------------
                // 4. Main Function: Build Row
                // ---------------------------------------------------------
                   function buildRowHTML(lead) {
                    // 1. Define helper 'esc' immediately to avoid errors
                    const safeStr = (v) => (v == null ? "" : String(v));
                    const esc = (s) => String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" })[m]);
                    const fmtDE = (v) => { try { return v ? new Date(v).toLocaleDateString("de-DE") : "-"; } catch { return "-"; } };

                    const cName = safeStr(lead?.customer_name).trim();
                    const cLastname = safeStr(lead?.customer_lastname).trim();
                    const cFirma = safeStr(lead?.firma).trim();
                    const displayName = `${cLastname} ${cName}`.trim() || cFirma || "Unbekannt";

                    const stageKey = utils.canonicalStage(lead?.stage);

                    const cId = lead?.customer_id ?? "";
                    const aId = lead?.alternative_id ?? "";
                    const pId = lead?.product_id ?? "";
                    const lpId = lead?.lead_product_id ?? "";

                    const ws = String(lead?.work_status || "playing").toLowerCase();

                    // 1. Get Status Block from Kanban (Core)
                    const statusBlockHTML = kanban ? kanban.buildStatusBlock(lead) : `<span class="badge badge-secondary">${stageKey}</span>`;

                    // 2. Get Live Feed HTML
                    const liveFeedRow = listFeedHTML();

                    // 3. Meta Logic (Assigned By...)
                    const teamAssignments = Array.isArray(lead?.team_assignments) ? lead.team_assignments : [];
                    let teamsRaw = lead?.teams;
                    if (typeof teamsRaw === "string") {
                        try {
                            teamsRaw = JSON.parse(teamsRaw);
                        } catch {
                            teamsRaw = [];
                        }
                    }
                    if (!Array.isArray(teamsRaw)) teamsRaw = [];

                    const assignments = teamAssignments.length ?
                        teamAssignments :
                        teamsRaw.map((t) => ({
                            assigned_at: t?.assigned_at ?? null,
                            assigned_at_iso: t?.assigned_at_iso ?? null,
                            assigned_by: t?.assigned_by ?? null,
                            assigned_by_user: t?.assigned_by_user ?? null,
                            stage_label: t?.stage_label ?? null,
                        }));

                    const parseAssignedAt = (a) => {
                        const raw = (a?.assigned_at_iso || a?.assigned_at || "").trim();
                        if (!raw) return 0;
                        const isoish = raw.includes("T") ? raw : raw.replace(" ", "T");
                        const ts = Date.parse(isoish);
                        return Number.isFinite(ts) ? ts : 0;
                    };

                    const latestA = assignments.reduce((best, a) => {
                        const ta = parseAssignedAt(a);
                        const tb = parseAssignedAt(best);
                        return ta > tb ? a : best;
                    }, null);

                    const assignedBy = (() => {
                        const u = latestA?.assigned_by_user;
                        if (u && (u.name || u.lastname)) return `${safeStr(u.lastname).trim()} ${safeStr(u.name).trim()}`.trim();
                        const id = Number(latestA?.assigned_by ?? 0);
                        return id > 0 ? `Mitarbeiter #${id}` : "";
                    })();

                    const assignedAtRaw = (latestA?.assigned_at_iso || latestA?.assigned_at || "").trim();
                    const STAGE_DE = {
                        lead: "Lead",
                        offer: "Angebot",
                        follow_up: "Nachfassen",
                        accepted: "Annehmen",
                        deal: "Auftrag",
                        project: "Montage",
                        completed: "Abschluss",
                        archive: "Archiv",
                        junk: "Junk"
                    };
                    const phaseLabel = (() => {
                        const lbl = (latestA?.stage_label || "").trim();
                        if (lbl) return lbl;
                        const key = String(latestA?.stage || "").trim().toLowerCase();
                        return STAGE_DE[key] || "";
                    })();

                    const assignedMetaHTML =
                        assignedBy || assignedAtRaw || phaseLabel ?
                        `<div class="small text-muted mt-1 w-100">
                              ${phaseLabel ? `<span class="mr-2"><i class="feather icon-layers mr-25"></i><span>Phase: <strong>${esc(phaseLabel)}</strong></span></span><span class="mx-1">•</span>` : ``}
                              <i class="feather icon-user mr-25"></i><span>Zugewiesen von: <strong>${esc(assignedBy || "-")}</strong></span>
                              <span class="mx-1">•</span>
                              <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E" style="vertical-align:-1px; margin-right:3px;" alt="" /><span>${esc(assignedAtRaw ? fmtDE(assignedAtRaw) : "-")}</span>
                            </div>` :
                        "";

                    // CHANGED: Grouped customer link and action bar into .customer-name-wrapper
                    return `
                        <tr id="row-${esc(lpId)}"
                            class="list-row-item"
                            data-customer-id="${esc(cId)}"
                            data-alternative-id="${esc(aId)}"
                            data-product-id="${esc(pId)}"
                            data-lead-product-id="${esc(lpId)}"
                            data-stage="${esc(stageKey)}"
                            data-product-stage-id="${esc(lead?.product_stage_id || '')}"
                            data-product-task-phase-id="${esc(lead?.product_task_phase_id || '')}"
                            data-product-stage-name="${esc(lead?.product_stage_name || '')}"
                            data-product-task-phase-name="${esc(lead?.product_task_phase_name || '')}"
                            data-initial="${esc(lead?.initial || '')}"
                            data-run-state="${esc(ws)}"
                            data-stage-history="${esc(typeof lead?.stage_history === 'string' ? lead?.stage_history : JSON.stringify(lead?.stage_history || []))}"
                            data-team-assignments="${esc(JSON.stringify(Array.isArray(lead?.team_assignments) ? lead.team_assignments : []))}">

                          <td style="width: 100px;">${lead?.created_at ? fmtDE(lead.created_at) : "-"}</td>

                          <td style="min-width: 350px;">

                            <div class="customer-name-wrapper">
                                <a href="/new_lead_profile/${encodeURIComponent(safeStr(cId))}" class="customer-link" style="font-size:1.05rem;">
                                  ${esc(displayName)}
                                </a>

                                <div class="list-action-bar">
                                  <button type="button" class="btn-list-icon" data-menu="termin" title="Termin">
                                    <i class="feather icon-calendar"></i>
                                    <span class="badge-notes" data-ap-count style="display:none">0</span>
                                  </button>
                                  <button type="button" class="btn-list-icon" data-menu="aufgabe" title="Aufgabe">
                                    <i class="feather icon-check-square"></i>
                                    <span class="badge-notes" data-pt-count style="display:none">0</span>
                                  </button>

                                  <span style="border-left:1px solid #ddd; height:14px; margin:0 4px;"></span>

                                  <button type="button" class="btn-list-icon note" data-open-notes data-customer="${esc(cId)}" data-alt="${esc(aId)}" data-product="${esc(pId)}">
                                    <i class="feather icon-message-square"></i>
                                    <span class="badge-notes" data-count="0" style="display:none">0</span>
                                  </button>

                                  <button type="button" class="btn-list-icon toggle-feed-btn" title="Aktivitäten anzeigen">
                                      <i class="feather icon-zap"></i>
                                  </button>

                                  <div class="kb-menu" style="position:relative; display:inline-block;">
                                    <button type="button" class="btn-list-icon kb-menu-toggle" data-act="custom-menu-toggle" aria-haspopup="menu" aria-expanded="false">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="kb-menu-dropdown" role="menu" hidden style="right:0; left:auto; top:100%; min-width:140px; z-index: 1050;">
                                        <button type="button" class="kb-menu-item text-success" data-run="playing"><i class="feather icon-play mr-50"></i> Start</button>
                                        <button type="button" class="kb-menu-item text-warning" data-run="paused"><i class="feather icon-pause mr-50"></i> Pause</button>
                                        <button type="button" class="kb-menu-item text-danger" data-run="stopped"><i class="feather icon-square mr-50"></i> Stopp</button>
                                        <hr class="my-50">
                                        <button type="button" class="kb-menu-item" data-menu="verlauf">
                                            <i class="feather icon-activity mr-50"></i> Verlauf
                                        </button>
                                        <button type="button" class="kb-menu-item" data-menu="product-stage-info">
                                            <i class="feather icon-info mr-50"></i> Produktstatus
                                        </button>
                                    </div>
                                  </div>
                                </div>
                            </div>

                            ${assignedMetaHTML}

                            ${liveFeedRow}
                          </td>

                          <td>${esc(lead?.city ?? "")}</td>
                          <td>${esc(lead?.initial ?? "")}</td>
                          <td>${typeof listEmpAndTeamHTML === 'function' ? listEmpAndTeamHTML(lead) : ''}</td>

                          <td>
                            ${statusBlockHTML}
                            ${(window.LeadUI && window.LeadUI.kanban && typeof window.LeadUI.kanban.offerWorkflowHTML === "function") ? window.LeadUI.kanban.offerWorkflowHTML(lead) : ""}
                          </td>

                          <td>
                            <select class="form-control stage-select" data-id="${esc(lpId)}">
                              ${Object.entries(APP.stageNames || {})
                                .filter(([k]) => !["junk", "ticket"].includes(String(k).toLowerCase()))
                                .map(([k, l]) => {
                                  const meta = APP.stageMeta?.[k] || {};
                                  return `<option value="${esc(k)}" data-color="${esc(meta.color || "#93c21c")}" data-icon="${esc(meta.icon || "circle")}" ${stageKey === k ? "selected" : ""}>${esc(l)}</option>`;
                                })
                                .join("")}
                            </select>
                          </td>
                        </tr>
                      `;
                  }
                // ---------------------------------------------------------
                // 5. Updated Bootstrapper
                // ---------------------------------------------------------
                function bootstrapListLiveFeed(container) {
                      if (!window.LeadUI.liveFeed || typeof window.LeadUI.liveFeed.loadForCard !== "function") return;

                      const root = container || document;
                      // CHANGED: .list-feed-row -> .list-row-item
                      const rows = root.querySelectorAll("tr.list-row-item"); 

                      if (!rows.length) return;

                      let i = 0;
                      const BATCH = 4; // Process in batches to avoid freezing UI

                      (function pump() {
                          const slice = Array.prototype.slice.call(rows, i, i + BATCH);
                          i += BATCH;
                          slice.forEach((row) => {
                              // IMPORTANT: Ensure the row has a customer ID before trying to load
                              if (row.dataset.customerId) {
                                  window.LeadUI.liveFeed.loadForCard(row);
                              }
                          });
                          if (i < rows.length) {
                              if ("requestIdleCallback" in window) requestIdleCallback(pump);
                              else setTimeout(pump, 0);
                          }
                      })();
                  }

                  // Expose helpers globally
                  window.listFeedHTML = listFeedHTML;
                  window.LeadUI.bootstrapListLiveFeed = bootstrapListLiveFeed;

                  function stageSelectTemplate(option, mode = "option") {
                      if (!option.id) return option.text;
                      const meta = APP.stageMeta?.[option.id] || window.LeadUI?.APP?.stageMeta?.[option.id] || {};
                      const color = option.element?.dataset?.color || meta.color || "#93c21c";
                      const icon = option.element?.dataset?.icon || meta.icon || "circle";
                      const label = option.text || APP.stageNames?.[option.id] || option.id;

                      return jQuery(`
                        <span class="stage-select2-${mode}">
                          <span class="stage-color-dot" style="background:${esc(color)}"></span>
                          <span class="stage-select2-icon"><i class="feather icon-${esc(icon)}"></i></span>
                          <span class="stage-select2-label">${esc(label)}</span>
                        </span>
                      `);
                  }

                  function initListStageSelect2(container = document) {
                      if (!window.jQuery || !jQuery.fn.select2) return;
                      const $root = jQuery(container);

                      $root.find("select.stage-select").each(function () {
                          const $el = jQuery(this);
                          if ($el.hasClass("select2-hidden-accessible")) $el.select2("destroy");

                          $el.select2({
                              width: "170px",
                              minimumResultsForSearch: 8,
                              dropdownParent: jQuery(document.body),
                              templateResult: (option) => stageSelectTemplate(option, "option"),
                              templateSelection: (option) => stageSelectTemplate(option, "selection"),
                              escapeMarkup: (m) => m,
                          });
                      });

                      setTimeout(() => { if (window.feather) window.feather.replace(); }, 30);
                  }

                  function syncListSortHeaders() {
                      const key = State.sort?.key || "created_at";
                      const dir = State.sort?.dir || "desc";

                      qsa("#profile th.sortable").forEach((th) => {
                          const active = th.dataset.sort === key;
                          th.classList.toggle("active", active);
                          th.classList.toggle("desc", active && dir === "desc");
                      });

                      if (window.feather) setTimeout(() => window.feather.replace(), 30);
                  }

              function syncSummary(data) {
                const setTxt = (sel, v) => {
                  const el = qs(sel);
                  if (el) el.textContent = String(v ?? "");
                };
                const setHTML = (sel, v) => {
                  const el = qs(sel);
                  if (el) el.innerHTML = v;
                };

                setTxt("#totalEmployees", data?.totalEmployees);
                setTxt("#totalProduct", data?.totalProducts);
                setTxt("#totalCustomer", data?.totalCustomers);

                setHTML("#statusOffen", `${data?.statusCounts?.offen ?? 0} <small>(${data?.statusPercentages?.offen ?? 0}%)</small>`);
                setHTML("#statusZusage", `${data?.statusCounts?.zusage ?? 0} <small>(${data?.statusPercentages?.zusage ?? 0}%)</small>`);
                setHTML("#statusAbsage", `${data?.statusCounts?.absage ?? 0} <small>(${data?.statusPercentages?.absage ?? 0}%)</small>`);

                setTxt("#countCustomers", data?.totalCustomers);
                setTxt("#countProducts", data?.totalProducts);
                setTxt("#countDepartments", data?.totalDepartments);
                setTxt("#countEmployees", data?.totalEmployees);
              }

              function updateListView(leads, meta) {
                const tbody = qs("#kanbanTableBody");
                if (!tbody) return;

                if (!Array.isArray(leads) || !leads.length) {
                  tbody.innerHTML = '<tr><td colspan="8" class="text-center">Keine Ergebnisse gefunden</td></tr>';
                  syncSummary(meta);
                  return;
                }

                const tmp = document.createElement("tbody");
                tmp.innerHTML = leads.map(buildRowHTML).join("");

                tbody.innerHTML = "";
                tbody.append(...tmp.childNodes);

                syncSummary(meta);
                featherRefreshSoon();

                // Notes badges (list)
                window.LeadUI?.notes?.updateNoteBadgesForVisibleCards?.();

                bootstrapListLiveFeed(tbody);
                initListStageSelect2(tbody);
                syncListSortHeaders();
              }

              function renderPagination(metaLike) {
                const wrap = qs("#listPagination");
                if (!wrap) return;

                const meta = normalizePaginationMeta(metaLike);
                if (!meta || meta.last_page <= 1) {
                  wrap.innerHTML = "";
                  return;
                }

                const { current_page, last_page } = meta;

                let html = `<nav aria-label="Seiten"><ul class="pagination mb-0">`;

                const add = (p, label, disabled = false, active = false) => {
                  if (disabled) html += `<li class="page-item disabled"><span class="page-link">${label}</span></li>`;
                  else if (active) html += `<li class="page-item active"><span class="page-link">${label}</span></li>`;
                  else html += `<li class="page-item"><a class="page-link" href="#" data-page="${p}">${label}</a></li>`;
                };

                add(current_page - 1, "«", current_page === 1);

                const win = 2;
                const st = Math.max(1, current_page - win);
                const en = Math.min(last_page, current_page + win);

                if (st > 1) {
                  add(1, "1", false, current_page === 1);
                  if (st > 2) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
                }

                for (let p = st; p <= en; p++) add(p, String(p), false, p === current_page);

                if (en < last_page) {
                  if (en < last_page - 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
                  add(last_page, String(last_page), false, current_page === last_page);
                }

                add(current_page + 1, "»", current_page === last_page);

                wrap.innerHTML = html + "</ul></nav>";
              }

              /* -------------------------------------------------------------------------- */
              /* Fetchers                                                                    */
              /* -------------------------------------------------------------------------- */
              function normalizeLead(raw) {
                const pick = (obj, ...keys) => {
                  for (const k of keys) {
                    const v = obj?.[k];
                    if (v !== undefined && v !== null && v !== "") return v;
                  }
                  return null;
                };
                const latest_phase = pick(raw, "latest_phase", "phase_name", "phase_title", "phase_section_title");
                const latest_activity = pick(raw, "latest_activity", "activity_title");
                const done_date = pick(raw, "done_date", "updated_at", "history_at");
                const updated_at = pick(raw, "updated_at", done_date);
                return { ...raw, latest_phase, latest_activity, done_date, updated_at };
              }

              function ensureLoadedMap() {
                if (!State.loaded || typeof State.loaded !== "object") State.loaded = { kanban: false, list: false };
                if (!("kanban" in State.loaded)) State.loaded.kanban = false;
                if (!("list" in State.loaded)) State.loaded.list = false;
              }

              function syncTabCountsFromListPayload(payload) {
                const total =
                  payload?.pagination?.total ||
                  payload?.meta?.total ||
                  (Array.isArray(payload?.leads) ? payload.leads.length : 0);

                setTabCount("#tabCountList", total);
                setTabCount("#tabCountKanban", total);
              }

              function syncTabCountsFromKanban(leads) {
                if (Array.isArray(leads)) setTabCount("#tabCountKanban", leads.length);
              }

              function fetchKanbanView(qsStr) {
                ensureLoadedMap();

                const signal = cancel("kanban");
                const board = qs("#kanban");
                if (board && !State.loaded.kanban) board.innerHTML = '<div class="p-2 text-muted">Lade Kanban…</div>';

                return safeFetchJSON(`${APP.endpoints.kanbanSearch}${qsStr ? `?${qsStr}` : ""}`, { signal, retries: 0 })
                  .then((payload) => {
                    const arr = Array.isArray(payload?.leads)
                      ? payload.leads
                      : Array.isArray(payload?.data)
                      ? payload.data
                      : Array.isArray(payload)
                      ? payload
                      : [];

                    State.lastKanbanData = arr.map(normalizeLead);

                    if (!State.loaded.kanban) {
                      renderKanbanIncremental(State.lastKanbanData, autoChunk(), () => {
                        ensureLoadedMap();
                        State.loaded.kanban = true;
                        syncTabCountsFromKanban(State.lastKanbanData);
                      });
                    } else {
                      renderKanbanDiff(State.lastKanbanData);
                      syncTabCountsFromKanban(State.lastKanbanData);
                    }
                  })
                  .catch((e) => {
                    if (e?.name !== "AbortError") Swal.fire("Fehler", e?.message || "Fehler", "error");
                  });
              }

              function fetchListView(qsStr) {
                ensureLoadedMap();

                const signal = cancel("list");
                const tbody = qs("#kanbanTableBody");
                if (tbody && !State.loaded.list) {
                  tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Lade Liste…</td></tr>';
                }

                return safeFetchJSON(`${APP.endpoints.listSearch}${qsStr ? `?${qsStr}` : ""}`, { signal, retries: 0 })
                  .then((payload) => {
                    ensureLoadedMap();
                    State.loaded.list = true;

                    const leads = Array.isArray(payload?.leads) ? payload.leads : Array.isArray(payload?.data) ? payload.data : [];
                    updateListView(leads, payload);

                    renderPagination(payload.pagination || payload.meta || payload);
                    syncTabCountsFromListPayload(payload);
                  })
                  .catch((e) => {
                    if (e?.name === "AbortError") return;
                    Swal.fire("Fehler", e?.message || "Serverfehler.", "error");
                    updateListView([], {});
                    renderPagination(null);
                  });
              }

              // Public fetch hooks used by the workflow switch (Unternehmen/Produkt).
              window.LeadUIFetchKanban = fetchKanbanView;
              window.LeadUIFetchList = fetchListView;

              /* -------------------------------------------------------------------------- */
              /* Partials: Ticket & Investment tabs                                          */
              /* -------------------------------------------------------------------------- */
              partials.fetchTicketsTab = async function (qsStr = "") {
                const pane = qs("#ticket");
                if (!pane) return;

                const url = `${APP.endpoints.tickets}${qsStr ? `?${qsStr}` : ""}`;

                try {
                  const res = await fetch(url, {
                    headers: { Accept: "text/html", "X-Requested-With": "XMLHttpRequest" },
                    credentials: "same-origin",
                  });

                  const html = await res.text();
                  pane.innerHTML = html;

                  const totalNode = pane.querySelector("[data-ticket-total]") || pane.querySelector("[data-total]");
                  const total = totalNode
                    ? toInt(
                        totalNode.getAttribute("data-ticket-total") ||
                          totalNode.getAttribute("data-total") ||
                          totalNode.dataset.ticketTotal ||
                          totalNode.dataset.total ||
                          0,
                        0
                      )
                    : 0;

                  const badge = qs("#tabCountTicket");
                  if (badge) badge.textContent = String(total);
                } catch (e) {
                  console.error("Ticket partial load failed:", e);
                }
              };



              function refreshArchiveAndJunk(qsStr) {
                partials.fetchJunkTab?.(qsStr);
                partials.fetchTicketsTab?.(qsStr);
              }

              /* -------------------------------------------------------------------------- */
              /* Unified run state prompt                                                    */
              /* -------------------------------------------------------------------------- */
              async function promptRunReason(state) {
                const label =
                  state === "playing" ? "Start" : state === "paused" ? "Pause" : state === "stopped" ? "Stopp" : state;

                const { value: reason, isConfirmed } = await Swal.fire({
                  title: `Grund für ${label}`,
                  input: "textarea",
                  showCancelButton: true,
                  confirmButtonText: "Speichern",
                  inputValidator: (v) => (!v?.trim() ? "Bitte Grund eingeben" : undefined),
                });

                if (!isConfirmed) return null;
                return String(reason || "").trim();
              }

              /* -------------------------------------------------------------------------- */
              /* Click handlers (Unified: List + Kanban)                                     */
              /* -------------------------------------------------------------------------- */
            /* -------------------------------------------------------------------------- */
          /* Click handlers (Unified: List + Kanban)                                    */
          /* -------------------------------------------------------------------------- */
          document.addEventListener("click", async (e) => {
            // 1. Find the button (works for both direct clicks and nested icon clicks)
            const actBtn = e.target.closest("[data-act], [data-run]");
            if (!actBtn) return;

            // 2. Identify if we are in Kanban or List
            const card = actBtn.closest(".card"); // Kanban
            const row = actBtn.closest("tr.list-row-item"); // List

            // 3. Handle the 'Run' (Play/Pause/Stop) logic
            if (actBtn.dataset.run) {
                e.preventDefault();
                e.stopPropagation();

                const state = actBtn.dataset.run;
                const target = card || row;
                const lpId = target.dataset.leadProductId;

                if (!lpId) {
                    console.error("Lead Product ID missing on target element");
                    return;
                }

                // Optional: Ask for a reason (matches your controller logic)
                const { value: reason } = await Swal.fire({
                    title: `Grund für ${state}`,
                    input: 'textarea',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern'
                });

                if (reason === undefined) return; // User cancelled

                try {
                    const res = await window.LeadUI.net.postJSON(`/lead-product/progress/${lpId}/${state}`, {
                        reason: reason
                    });

                    if (res.success) {
                        // Update the UI immediately
                        if (card) window.LeadUI.kanban.applyRunStateUI(card, state);
                        window.LeadUI.silentRefreshBoth(); // Sync the other view
                        Swal.fire("Aktualisiert", "", "success");
                    }
                } catch (err) {
                    Swal.fire("Fehler", "Status konnte nicht geändert werden", "error");
                }
                return;
            }

            // 👇 --- ADD THIS NEW BLOCK BELOW YOUR RUN LOGIC --- 👇

            // 4. Handle 'Archive' and 'Delete' (Junk) logic
            if (actBtn.dataset.act === "archive" || actBtn.dataset.act === "delete") {
                e.preventDefault();
                e.stopPropagation();

                const target = card || row;
                if (!target) return;

                const currentStage = target.dataset.stage;
                const newStage = actBtn.dataset.act === "archive" ? "archive" : "junk";

                // Extract current team IDs to prefill the SweetAlert Select2
                let currentTeamIds = [];
                try {
                    currentTeamIds = JSON.parse(target.dataset.teamIds || "[]").map(x => Number(x));
                } catch(err) {}

                // Prompt user with your existing SweetAlert logic
                const stageTeams = safeJSON(target.dataset.teamAssignments || "[]", []);
                const confirm = await confirmStageChange(newStage, currentStage, currentTeamIds, { stageTeams, productId: target.dataset.productId });
                if (!confirm.ok) return;

                try {
                    const { customerId, alternativeId, productId, leadProductId } = target.dataset;

                    const stageResponse = await applyStageChange({
                        customerId,
                        alternativeId,
                        productId,
                        leadProductId,
                        newStage,
                        noteHTML: confirm.reasonHTML,
                        teams: confirm.teams,
                    });

                    if (confirm.reminder?.enabled) {
                        await createReminderFromStageChange({ leadProductId, customerId, alternativeId, productId }, confirm.reminder);
                        if (window.preloadLeadReminderSummaries) window.setTimeout(window.preloadLeadReminderSummaries, 350);
                    }

                    if (stageResponse?.final?.team_assignments && target) {
                        target.dataset.teamAssignments = JSON.stringify(stageResponse.final.team_assignments);
                    }

                    // Update DOM gracefully
                    if (card) {
                        if (newStage === "junk" || stageFilterExcludes(newStage)) {
                            card.remove();
                        } else {
                            moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
                            enforceActionVisibility(card);
                        }
                    } else if (row) {
                        if (newStage === "junk" || stageFilterExcludes(newStage)) {
                            row.remove();
                        } else {
                            row.dataset.stage = newStage;
                        }
                    }

                    window.LeadUI?.silentRefreshBoth?.();
                    Swal.fire({
                        icon: "success",
                        title: newStage === "archive" ? "Archiviert" : "In Junk verschoben",
                        text: newStage === "archive" ? "Erfolgreich ins Archiv verschoben." : "Eintrag wurde aussortiert.",
                        timer: 1500,
                        showConfirmButton: false
                    });
                } catch (err) {
                    Swal.fire("Fehler", err?.message || "Serverfehler beim Verschieben.", "error");
                }
                return;
            }
          });

              /* -------------------------------------------------------------------------- */
              /* Kanban: click selection + dragstart delegation                              */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("click", (e) => {
                const card = e.target.closest("#kanban .card");
                if (!card) return;

                // Avoid selecting when clicking action buttons/links/inputs
                if (e.target.closest(".card-actions, button, a, input, select, textarea")) return;

                selectCard(card, e);
              });

              document.addEventListener("dragstart", (e) => {
                const card = e.target.closest("#kanban .card");
                if (!card) return;
                onKanbanDragStart(e, card);
              });

              // Enable drop only on columns (and avoid "open in new tab" elsewhere)
              document.addEventListener("dragover", (e) => {
                if (!e.dataTransfer) return;

                // Only handle our own DND type
                if (!Array.from(e.dataTransfer.types || []).includes(window.KB_DND_MIME)) return;

                const col = e.target.closest(".column");
                document.querySelectorAll("#kanban .column.kb-drop-target").forEach((c) => {
                  if (c !== col) c.classList.remove("kb-drop-target");
                });
                if (col) {
                  e.preventDefault();
                  col.classList.add("kb-drop-target");
                }
              });

              document.addEventListener("dragleave", (e) => {
                const col = e.target.closest?.(".column");
                if (!col) return;
                const next = e.relatedTarget;
                if (!next || !col.contains(next)) col.classList.remove("kb-drop-target");
              });

              document.addEventListener("dragend", () => {
                document.querySelectorAll("#kanban .column.kb-drop-target").forEach((c) => c.classList.remove("kb-drop-target"));
              });

              document.addEventListener(
                "drop",
                (e) => {
                  if (!e.dataTransfer) return;
                  if (!Array.from(e.dataTransfer.types || []).includes(window.KB_DND_MIME)) return;

                  const col = e.target.closest(".column");
                  if (!col) {
                    // Prevent browser from navigating when dropping our internal drag payload
                    e.preventDefault();
                    return;
                  }

                  document.querySelectorAll("#kanban .column.kb-drop-target").forEach((c) => c.classList.remove("kb-drop-target"));
                  onKanbanDrop(e);
                },
                true
              );

              /* -------------------------------------------------------------------------- */
              /* List: stage select change                                                   */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("change", async (e) => {
                const sel = e.target.closest("select.stage-select");
                if (!sel) return;

                const row = sel.closest("tr.list-row-item");
                if (!row) return;

                const newStage = sel.value;

                // old stage from defaultSelected (Laravel often renders the current one)
                const prevIndex = Array.from(sel.options).findIndex((o) => o.defaultSelected);
                const oldStage = prevIndex >= 0 ? canonicalStage(sel.options[prevIndex].value) : canonicalStage(row.dataset.stage);

                if (newStage === oldStage) return;

                // 👇 ADDED PAUSE/STOP CHECK BLOCK 👇
                const runState = row.dataset.runState || 'playing';
                if (runState === 'paused' || runState === 'stopped') {
                    // Revert the dropdown selection visually immediately
                    sel.selectedIndex = Math.max(0, prevIndex);

                    let reason = "Kein Grund angegeben.";
                    try {
                        const history = JSON.parse(row.dataset.stageHistory || "[]");
                        if (Array.isArray(history) && history.length > 0) {
                            // Get the most recent entry
                            const latest = history[history.length - 1];
                            if (latest && latest.description) {
                                reason = latest.description;
                            }
                        }
                    } catch(e) {
                        console.warn("Could not parse stage_history", e);
                    }

                    const stateDe = runState === 'paused' ? 'pausiert' : 'gestoppt';
                    Swal.fire({
                        icon: "warning",
                        title: "Aktion nicht möglich",
                        html: `Dieser Eintrag ist momentan <b>${stateDe}</b> und kann nicht verschoben werden.<br><br><b>Grund:</b> ${esc(reason)}`
                    });
                    return; // Stop execution!
                }
                // 👆 END PAUSE/STOP CHECK BLOCK 👆

                const customerId = row.dataset.customerId;
                const alternativeId = row.dataset.alternativeId;
                const productId = row.dataset.productId;
                const leadProductId = sel.dataset.id || row.dataset.leadProductId || row.id?.split("-")[1];

                // teams from row if you ever store them (optional)
                const currentTeamIds = Array.isArray(safeJSON(row.dataset.teamIds || "[]", []))
                  ? safeJSON(row.dataset.teamIds || "[]", [])
                  : [];

                try {
                  const confirm = await confirmStageChange(newStage, oldStage, currentTeamIds, {
                    productId: row.dataset.productId,
                    productStageId: row.dataset.productStageId,
                    currentProductStageId: row.dataset.productStageId,
                    stageTeams: safeJSON(row.dataset.teamAssignments || "[]", []),
                  });
                  if (!confirm.ok) {
                    sel.selectedIndex = Math.max(0, prevIndex);
                    return;
                  }

                  await applyStageChange({
                    customerId,
                    alternativeId,
                    productId,
                    leadProductId,
                    newStage,
                    noteHTML: confirm.reasonHTML,
                    teams: confirm.teams,
                    mode: confirm.mode || "company",
                    companyStageKey: confirm.companyStageKey || newStage,
                    productStageId: confirm.productStageId || null,
                    productTaskPhaseId: confirm.productTaskPhaseId || null,
                  });

                  if (confirm.reminder?.enabled) {
                    await createReminderFromStageChange({ leadProductId, customerId, alternativeId, productId }, confirm.reminder);
                    if (window.preloadLeadReminderSummaries) window.setTimeout(window.preloadLeadReminderSummaries, 350);
                  }

                  // Update defaultSelected to keep oldStage detection correct next time
                  sel.querySelectorAll("option").forEach((o) => (o.defaultSelected = false));
                  sel.options[sel.selectedIndex].defaultSelected = true;

                  // Remove list rows if excluded
                  if (stageFilterExcludes(newStage)) {
                    // remove main row + feed row
                    const feedRow = row.nextElementSibling?.classList?.contains("list-feed-row") ? row.nextElementSibling : null;
                    row.remove();
                    feedRow?.remove?.();
                  } else {
                    row.dataset.stage = canonicalStage(newStage);
                  }

                  // update kanban card if present
                  const card =
                    qs(`#card-${CSS.escape(String(leadProductId))}`) ||
                    qs(`#${CSS.escape(String(leadProductId))}`) ||
                    qs(`.card[data-lead-product-id="${CSS.escape(String(leadProductId))}"]`);

                  if (card) {
                    moveOrRefreshKanbanCard({ newStage, cardFromDOM: card });
                    enforceActionVisibility(card);
                  }

                  window.LeadUI?.silentRefreshBoth?.();
                  Swal.fire("OK", "Phase aktualisiert.", "success");
                } catch (err) {
                  sel.selectedIndex = Math.max(0, prevIndex);
                  Swal.fire("Fehler", err?.message || "Serverfehler.", "error");
                }
              });
              document.addEventListener("click", (e) => {
                const btn = e.target.closest('tr.list-row-item [data-menu="product-stage-info"]');
                if (!btn) return;
                const row = btn.closest("tr.list-row-item");
                if (!row) return;
                showProductStageInfoFromElement(row);
              });

              /* -------------------------------------------------------------------------- */
              /* Sorting + pagination clicks                                                 */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("click", (e) => {
                const th = e.target.closest("#profile th.sortable");
                if (!th) return;

                const key = th.dataset.sort;
                if (!key) return;

                State.sort = State.sort?.key === key
                  ? { key, dir: State.sort.dir === "asc" ? "desc" : "asc" }
                  : { key, dir: "asc" };

                qsa("#profile th.sortable").forEach((h) => h.classList.remove("active", "desc"));
                th.classList.add("active");
                if (State.sort.dir === "desc") th.classList.add("desc");

                State.page = 1;
                State.filtersQS = filters.buildFilterQS();
                saveToLocal();
                syncURL();

                fetchListView(addPage(State.filtersQS, State.page));
                if (isKanbanActive()) fetchKanbanView(State.filtersQS);
              });

              document.addEventListener("click", (e) => {
                const a = e.target.closest("#listPagination a.page-link[data-page]");
                if (!a) return;

                e.preventDefault();
                const p = toInt(a.getAttribute("data-page"), 1);
                State.page = p;

                saveToLocal();
                syncURL();

                fetchListView(addPage(State.filtersQS, State.page));
              });

              /* -------------------------------------------------------------------------- */
              /* Tabs                                                                        */
              /* -------------------------------------------------------------------------- */
              if (window.jQuery) {
                jQuery('a[data-toggle="tab"][href="#home"]').on("shown.bs.tab", () => {
                  ensureColumns();
                  renderKanbanDiff(State.lastKanbanData || []);
                  featherRefreshSoon();
                  enforceActionVisibility();
                });

                jQuery('a[data-toggle="tab"][href="#junk"]').on("shown.bs.tab", () => {
                  partials.fetchJunkTab?.(State.filtersQS);
                });

              }

              document.addEventListener("shown.bs.tab", (e) => {
                const trg = e.target?.getAttribute("href") || "";
                if (trg === "#ticket") {
                  const qsStr = filters.buildFilterQS();
                  partials.fetchTicketsTab?.(qsStr);
                }
              });

              /* -------------------------------------------------------------------------- */
              /* Summary cards + filter buttons                                              */
              /* -------------------------------------------------------------------------- */
              function setSummaryActive(id) {
                qsa(".summary-card").forEach((c) => c.classList.remove("active"));
                if (id) qs("#" + id)?.classList.add("active");
              }

              function applyStatusGroup(g, cardId) {
                State.statusGroup = g;
                State.page = 1;

                State.filtersQS = filters.buildFilterQS();
                saveToLocal();
                syncURL();

                const withPage = addPage(State.filtersQS, State.page);

                fetchListView(withPage);
                fetchKanbanView(State.filtersQS);
                refreshArchiveAndJunk(State.filtersQS);

                setSummaryActive(cardId || null);
                filters.updateFilterBadges?.();
              }

              qs("#cardOffen")?.addEventListener("click", () => applyStatusGroup("offen", "cardOffen"));
              qs("#cardZusage")?.addEventListener("click", () => applyStatusGroup("zusage", "cardZusage"));
              qs("#cardAbsage")?.addEventListener("click", () => applyStatusGroup("absage", "cardAbsage"));

              qs("#btnApplyFilters")?.addEventListener("click", () => {
                State.page = 1;
                State.filtersQS = filters.buildFilterQS();
                State.lastAppliedQS = State.filtersQS;

                saveToLocal();
                syncURL();

                const withPage = addPage(State.filtersQS, State.page);

                fetchListView(withPage);
                fetchKanbanView(State.filtersQS);
                refreshArchiveAndJunk(State.filtersQS);

                partials.fetchTicketsTab?.(State.filtersQS);

                closeOverlays();
              });

              qs("#btnClearFilters")?.addEventListener("click", () => {
                const form = qs("#kanbanFilterForm");
                if (!form) return;

                form.reset();
                if (window.jQuery) window.jQuery(form).find(".select2").val(null).trigger("change");

                State.statusGroup = null;
                setSummaryActive(null);

                State.page = 1;
                State.filtersQS = filters.buildFilterQS();

                saveToLocal();
                syncURL();

                filters.updateFilterBadges?.();

                const withPage = addPage(State.filtersQS, State.page);

                fetchListView(withPage);
                fetchKanbanView(State.filtersQS);
                refreshArchiveAndJunk(State.filtersQS); 
                partials.fetchTicketsTab?.(State.filtersQS);
              });

              /* -------------------------------------------------------------------------- */
              /* LiveFeed row click                                                          */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("click", (e) => {
                const row = e.target.closest("#kanbanTableBody tr.list-row-item");
                if (!row) return;
                if (e.target.closest("button, a, select, input, textarea")) return;

                if (liveFeed && typeof liveFeed.loadForCard === "function") liveFeed.loadForCard(row);
              });

              /* -------------------------------------------------------------------------- */
              /* Keyboard                                                                     */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("keydown", (e) => {
                if (e.ctrlKey && e.key.toLowerCase() === "f") {
                  e.preventDefault();
                  qs("#btnOpenDrawer")?.click();
                }
                if (e.key === "Escape") closeOverlays();
              });

              /* -------------------------------------------------------------------------- */
              /* Silent refresh (public)                                                     */
              /* -------------------------------------------------------------------------- */
              function silentRefreshBoth() {
                const qsStr = State.filtersQS || "";
                fetchListView(addPage(qsStr, State.page || 1));
                fetchKanbanView(qsStr);
                partials.fetchTicketsTab?.(qsStr);
              }
              window.LeadUI.silentRefreshBoth = silentRefreshBoth;

              /* -------------------------------------------------------------------------- */
              /* Boot                                                                         */
              /* -------------------------------------------------------------------------- */
              document.addEventListener("DOMContentLoaded", () => {
                featherRefreshSoon();
                filters.initSelect2?.();
                filters.updateFilterBadges?.();

                initFromURL();
                if (!location.search) restoreFromLocal();

                State.filtersQS = filters.buildFilterQS();
                saveToLocal();
                syncURL();

                ensureLoadedMap();
                State.loaded.kanban = false;
                State.loaded.list = false;

                // initial loads
                fetchListView(addPage(State.filtersQS, State.page || 1));
                fetchKanbanView(State.filtersQS);

                // side tabs initial refresh
                refreshArchiveAndJunk(State.filtersQS);
              });
            })();
          </script>

          <!-- Kanban Column Script  -->
          <script>
            (function() {
                "use strict";

                document.addEventListener("DOMContentLoaded", () => {
                    // Function to toggle column visibility
                    function toggleColumn(stageId, isVisible) {
                        const col = document.getElementById(stageId);
                        if (!col) return;

                        if (isVisible) {
                            // We use 'flex' because your .column class likely uses display:flex
                            col.style.display = 'flex'; 
                            col.classList.remove('d-none');
                        } else {
                            col.style.display = 'none';
                            col.classList.add('d-none');
                        }
                    }

                    // 1. Bind Click Events to Checkboxes
                    const toggles = document.querySelectorAll('.col-toggle-checkbox');
                    toggles.forEach(chk => {
                        // Initial check to sync JS with HTML state
                        // (Optional, but good if you have cached values)

                        chk.addEventListener('change', () => {
                            toggleColumn(chk.value, chk.checked);
                        });
                    });

                    // 2. Patch Kanban Renderer 
                    // This ensures that if the board re-renders (e.g. after a search),
                    // we re-apply the visibility rules based on the checkboxes.
                    if (window.LeadUI && window.LeadUI.kanban) {
                        const originalEnsureColumns = window.LeadUI.kanban.ensureColumns;

                        window.LeadUI.kanban.ensureColumns = function() {
                            originalEnsureColumns(); // Let the core create the columns

                            // Immediately apply visibility based on current checkbox state
                            document.querySelectorAll('.col-toggle-checkbox').forEach(chk => {
                                toggleColumn(chk.value, chk.checked);
                            });
                        };
                    }
                });
            })();
          </script>


           <script>
              (function(){
                "use strict";

              /* Maps */
                const DATE_FMT = { hour:'2-digit', minute:'2-digit', day:'2-digit', month:'2-digit', year:'numeric' };

                /* ✅ German labels for your stages */
                const LABEL = (s) => ({
                  // your set
                  lead:      'Lead',        // or 'Interessent'
                  offer:     'Angebot',
                  follow_up: 'Nachfassen',
                  accepted:  'Annehmen',
                  deal:      'Auftrag',
                  project:   'Projekt',     // or 'Montage'
                  junk:      'Aussortiert',
                  canceled:  'Abgebrochen', // or 'Storniert'
                  ticket:    'Ticket',
                  pause:     'Pausiert',

                  // optional extras (kept for safety; remove if unused)
                  completed: 'Abgeschlossen',
                  qualify:   'Qualifizierung',
                  negotiation:'Verhandlung',
                  won:       'Gewonnen',
                  lost:      'Verloren',
                  maintenance:'Wartung',
                  repair:    'Reparatur',
                  planning:  'Planung',
                  complete:  'Komplett'
                }[String(s||'').toLowerCase()] || (s ? String(s) : 'Unbekannt'));

                /* 🎨 Badge classes per stage (lh- namespaced) */
                const BADGE = (s) => ({
                  lead:      'lh-badge lh-badge--secondary',
                  offer:     'lh-badge lh-badge--info',
                  follow_up: 'lh-badge lh-badge--warning',
                  accepted:  'lh-badge lh-badge--success',
                  deal:      'lh-badge lh-badge--primary',
                  project:   'lh-badge lh-badge--primary',
                  completed: 'lh-badge lh-badge--success',
                  junk:      'lh-badge lh-badge--secondary',
                  canceled:  'lh-badge lh-badge--danger',
                  ticket:    'lh-badge lh-badge--secondary',
                  pause:     'lh-badge lh-badge--warning',

                  // optional extras
                  qualify:    'lh-badge lh-badge--secondary',
                  negotiation:'lh-badge lh-badge--warning',
                  won:        'lh-badge lh-badge--success',
                  lost:       'lh-badge lh-badge--danger',
                  maintenance:'lh-badge lh-badge--secondary',
                  repair:     'lh-badge lh-badge--secondary',
                  planning:   'lh-badge lh-badge--secondary',
                  complete:   'lh-badge lh-badge--primary'
                }[String(s||'').toLowerCase()] || 'lh-badge');


                const ICONS = {
                  lead:`<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M3 11h18v2H3z"/></svg>`,
                  qualify:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M9 16.2l-3.5-3.5 1.4-1.4L9 13.4l7.1-7.1 1.4 1.41z"/></svg>`,
                  offer:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 6h16v12H4zM6 8h12v2H6z"/></svg>`,
                  negotiation:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 4h10v6H4zM14 10l6 4-6 4z"/></svg>`,
                  won:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24 7.46 13.97 5.82 21z"/></svg>`,
                  lost:`<svg viewBox="0 0 24 24" width="18" height="18"><path d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L10.59 13.4l-6.3 6.3L2.88 18.3l6.3-6.29-6.3-6.3L4.3 4.29l6.29 6.3 6.3-6.3z"/></svg>`
                };

                /* DOM */
                const root = document.getElementById('lh-drawer');
                const panel = root?.querySelector('.lh-panel');
                const title = document.getElementById('lh-title-text');
                const tl    = document.getElementById('lh-timeline');
                const acts  = document.getElementById('lh-activities');
                if (!root || !panel || !title || !tl || !acts) return;

                /* Drawer controls */
                const open = () => { root.setAttribute('aria-hidden','false'); panel.focus({preventScroll:true}); document.body.style.overflow='hidden'; };
                const close = () => { root.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };
                document.addEventListener('click', e => { if (e.target.closest('[data-lh-close]')) close(); });
                document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

                /* Helpers */
                const esc = s => (s==null?'':String(s)).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

                let apAutocomplete = null;

                function initAddressAutocomplete(){
                  const input = qs('#ap-full_address');
                  if (!input) return;
                  if (!window.google || !google.maps || !google.maps.places) return;

                  if (apAutocomplete) return; // only once

                  apAutocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['geocode'],
                    componentRestrictions: { country: 'de' }
                  });

                  apAutocomplete.addListener('place_changed', () => {
                    const place = apAutocomplete.getPlace();
                    if (!place || !place.geometry) return;

                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    qs('#ap-latitude').value  = lat;
                    qs('#ap-longitude').value = lng;

                    let street = '', streetNo = '', postcode = '', city = '';

                    (place.address_components || []).forEach(c => {
                      const types = c.types || [];
                      if (types.includes('route'))          street   = c.long_name;
                      if (types.includes('street_number'))  streetNo = c.long_name;
                      if (types.includes('postal_code'))    postcode = c.long_name;
                      if (types.includes('locality'))       city     = c.long_name;
                      if (!city && types.includes('postal_town')) city = c.long_name;
                    });

                    const streetField   = qs('#ap-street');
                    const postcodeField = qs('#ap-postcode');
                    const cityField     = qs('#ap-city');

                    if (streetField && !streetField.value)
                      streetField.value = [street, streetNo].filter(Boolean).join(' ');
                    if (postcodeField && !postcodeField.value)
                      postcodeField.value = postcode;
                    if (cityField && !cityField.value)
                      cityField.value = city;
                  });
                }
                window.initAddressAutocomplete = initAddressAutocomplete;

                const fmt = s => s ? new Date(String(s).replace(' ','T')).toLocaleString('de-DE', DATE_FMT) : '';

                function skeleton(){
                  title.textContent = 'Verlauf wird geladen …';
                  tl.innerHTML = `
                    <li class="lh-item">
                      <div class="lh-icowrap"><div class="lh-ico"></div></div>
                      <div class="lh-content">
                        <div class="lh-skel" style="width:55%"></div>
                        <div class="lh-skel" style="width:35%"></div>
                        <div class="lh-skel" style="width:80%"></div>
                      </div>
                    </li>`;
                  acts.innerHTML = `
                    <div class="lh-card">
                      <div class="lh-skel" style="width:60%"></div>
                      <div class="lh-skel" style="width:40%"></div>
                      <div class="lh-skel" style="width:85%"></div>
                    </div>`;
                }

                function render(data){
                  title.textContent = 'Verlauf – ' + (data.customerName || '');

                  // Timeline
                  tl.innerHTML = (data.timeline?.length ? data.timeline : []).map(t => {
                    const key = String(t.to_stage||'').toLowerCase();
                    const to  = esc(LABEL(key));
                    const from = t.from_stage ? `<small class="lh-muted ml-2">von ${esc(LABEL(t.from_stage))}</small>` : '';
                    const when = t.changed_at ? `<small class="lh-muted ml-2">${fmt(t.changed_at)}</small>` : '';
                    const by   = t.changed_by ? `<small class="lh-muted ml-2">· ${esc(t.changed_by)}</small>` : '';
                    const desc = t.description ? `<div class="mt-2">${esc(t.description).replace(/\n/g,'<br>')}</div>` : '';
                    return `
                      <li class="lh-item">
                        <div class="lh-icowrap"><div class="lh-ico" title="${to}">${ICONS[key]||''}</div></div>
                        <div class="lh-content">
                          <div class="d-flex align-items-center flex-wrap">
                            <span class="${BADGE(key)} mr-2">${to}</span>${from}${when}${by}
                          </div>
                          ${desc}
                        </div>
                      </li>`;
                  }).join('') || `<li class="lh-muted" style="padding:.5rem 0">Kein Phasenverlauf vorhanden.</li>`;

                  // Activities
                  acts.innerHTML = (data.customerHistory?.length ? data.customerHistory : []).map(h => {
                    const when = h.at ? `<span class="lh-muted">${fmt(h.at)}</span>` : '';
                    const ch   = h.channel ? ` · <span class="lh-muted">#${esc(h.channel)}</span>` : '';
                    const note = h.note ? `<div class="mt-2">${esc(h.note).replace(/\n/g,'<br>')}</div>` : '';
                    const meta = (h.meta && typeof h.meta==='object')
                      ? `<div class="mt-2">` + Object.entries(h.meta).map(([k,v]) =>
                          `<span class="lh-badge" style="margin-right:6px;margin-bottom:6px">
                            <span class="lh-muted">${esc(k)}:</span> ${esc(typeof v==='string'||typeof v==='number'? v : JSON.stringify(v))}
                          </span>`
                        ).join('') + `</div>` : '';
                    return `
                      <div class="lh-card">
                        <div class="d-flex justify-content-between">
                          <div class="font-weight-bold">${esc(h.phase_name||'–')}${h.activity_title?` · ${esc(h.activity_title)}`:''}</div>
                          ${when}
                        </div>
                        <div class="lh-muted mt-1"><i class="feather icon-user" style="font-size:12px"></i> ${esc(h.by||'Unbekannt')}${ch}</div>
                        ${note}${meta}
                      </div>`;
                  }).join('') || `<div class="lh-muted" style="padding:.5rem 0">Keine Aktivitäten gefunden.</div>`;
                }

                async function fetchJSON(href){
                  const url = href.includes('?') ? `${href}&format=json` : `${href}?format=json`;
                  const res = await fetch(url, {
                    headers:{ 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
                    credentials:'same-origin', cache:'no-store'
                  });
                  const ct = res.headers.get('content-type') || '';
                  if (!ct.includes('application/json')) throw new Error('Non-JSON response: ' + ct);
                  if (!res.ok) throw new Error('HTTP ' + res.status);
                  return res.json();
                }

                function onClick(e){
                  const a = e.target.closest('a[data-lh-history]');
                  if (!a) return;
                  e.preventDefault();
                  open(); skeleton();
                  fetchJSON(a.href).then(render).catch(err=>{
                    console.error('[lh] fetch failed:', err);
                    title.textContent = 'Fehler beim Laden';
                    tl.innerHTML = '<li class="lh-muted" style="color:#b91c1c;padding:.5rem 0">Fehler beim Laden des Verlaufs.</li>';
                    acts.innerHTML = '';
                  });
                }

                document.addEventListener('click', onClick);
                document.addEventListener('turbo:load',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
                document.addEventListener('turbolinks:load',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
                document.addEventListener('livewire:navigated',()=>{document.removeEventListener('click',onClick);document.addEventListener('click',onClick);});
              })();
          </script>

          <script>
            (function () {
              "use strict";

              // ------------------------------------------------
              // Bootstrap from LeadUI (with safe fallbacks)
              // ------------------------------------------------
              const { APP = {}, net = {}, utils = {} } = window.LeadUI || {};

              const {
                safeFetchJSON: leadSafeFetchJSON
              } = net;

              const {
                qs: leadQs,
                qsa: leadQsa,
                CSRF: leadCSRF,
                featherRefreshSoon: leadFeatherRefreshSoon,
              } = utils;

              const qs =
                leadQs ||
                function (selector, ctx = document) {
                  return ctx.querySelector(selector);
                };

              const qsa =
                leadQsa ||
                function (selector, ctx = document) {
                  return Array.from(ctx.querySelectorAll(selector));
                };

              const CSRF =
                leadCSRF ||
                function () {
                  return (
                    document.querySelector('meta[name="csrf-token"]')?.content || ""
                  );
                };

              const featherRefreshSoon =
                leadFeatherRefreshSoon ||
                function () {
                  /* noop */
                };

              const safeFetchJSON =
                leadSafeFetchJSON ||
                async function (url, { method = "GET", headers = {}, body, retries = 0 } = {}) {
                  async function go() {
                    const res = await fetch(url, {
                      method,
                      credentials: "same-origin",
                      headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        ...headers,
                      },
                      body,
                    });

                    const text = await res.text();
                    if (!res.ok) {
                      throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
                    }

                    try {
                      return JSON.parse(text);
                    } catch {
                      throw new Error("Invalid JSON response");
                    }
                  }

                  try {
                    return await go();
                  } catch (err) {
                    if (retries > 0 && method === "GET") {
                      await new Promise((r) => setTimeout(r, 200));
                      return safeFetchJSON(url, { method, headers, body, retries: retries - 1 });
                    }
                    throw err;
                  }
                };

              // ------------------------------------------------
              // Tiny helpers
              // ------------------------------------------------
              const esc = (val) =>
                String(val ?? "").replace(/[&<>]/g, (m) => {
                  return { "&": "&amp;", "<": "&lt;", ">": "&gt;" }[m];
                });

              const norm = (val) => String(val || "").toLowerCase().trim();

              const debounce = (fn, ms = 200) => {
                let t;
                return (...args) => {
                  clearTimeout(t);
                  t = setTimeout(() => fn(...args), ms);
                };
              };

              // safe highlight for simple plain text
              function hl(text, query) {
                const src = esc(text ?? "");
                const q = norm(query);
                if (!q) return src;
                const re = new RegExp(
                  `(${q.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`,
                  "ig"
                );
                return src.replace(re, '<mark class="ptk-hl">$1</mark>');
              }

              // ------------------------------------------------
              // Config: columns & labels
              // ------------------------------------------------
              const PTK_STATUS = [
                { key: "open",        label: "Offen" },
                { key: "in_progress", label: "In Arbeit" },
                { key: "paused",      label: "Pausiert" },
                { key: "done",        label: "Erledigt" },
                { key: "canceled",    label: "Storniert" },
              ];

              const STATUS_ORDER = PTK_STATUS.map((s) => s.key);

              const statusLabel = (key) =>
                PTK_STATUS.find((s) => s.key === key)?.label || key;

              // ------------------------------------------------
              // Personal Tasks Kanban (PTK)
              // ------------------------------------------------
              const PTK = {
                _tasks: [],
                _query: "",
                _ctx: null,
                _searchEl: null,
                _editingId: null,

                // --------------- public API ----------------

                open(customerId, alternativeId, productId, title) {
                  const titleEl = qs("#pt-title");
                  if (titleEl) {
                    titleEl.textContent = title || "Aufgaben";
                  }

                  const cField = qs("#pt-customer_id");
                  const aField = qs("#pt-alternative_id");
                  const pField = qs("#pt-product_id");

                  if (cField) cField.value = customerId || "";
                  if (aField) aField.value = alternativeId || "";
                  if (pField) pField.value = productId || "";

                  this._ctx = {
                    customerId: customerId || "",
                    alternativeId: alternativeId || "",
                    productId: productId || "",
                  };

                  this._editingId = null;

                  const form = qs("#pt-form");
                  if (form) form.reset();

                  if (window.jQuery) {
                    jQuery("#pt-employee_ids").val(null).trigger("change");
                  }

                  this.show();
                  this.ensureBoard();
                  this.ensureSearchBar();
                  this.renderSkeletonContent();
                  this.loadTasks();
                },

                show() {
                  qs("#pt-backdrop")?.classList.add("show");
                  qs("#pt-drawer")?.classList.add("open");
                  document.body.style.overflow = "hidden";
                },

                hide() {
                  qs("#pt-backdrop")?.classList.remove("show");
                  qs("#pt-drawer")?.classList.remove("open");
                  document.body.style.overflow = "";
                  this._editingId = null;
                },

                setQuery(query) {
                  this._query = norm(query || "");
                  this.renderFiltered();
                },

                updateCardBadge() {
                  const ctx = this._ctx;
                  if (!ctx) return;

                  const c = ctx.customerId || "";
                  const a = ctx.alternativeId || "";
                  const p = ctx.productId || "";
                  const count = this._tasks.length;

                  const selector = `.card[data-customer-id="${c}"][data-alternative-id="${a}"][data-product-id="${p}"]`;

                  qsa(selector).forEach((card) => {
                    const btn = card.querySelector('.kb-menu-item[data-menu="aufgabe"]');
                    if (!btn) return;

                    let pill = btn.querySelector("[data-pt-count]");
                    if (!pill) {
                      pill = document.createElement("span");
                      pill.className = "kb-menu-pill kb-menu-pill--pt";
                      pill.setAttribute("data-pt-count", "");
                      btn.appendChild(pill);
                    }

                    pill.textContent = String(count);
                    pill.style.display = count ? "inline-flex" : "none";
                  });
                },

                // --------------- board shell ----------------

                ensureBoard() {
                  const wrap = qs("#pt-list");
                  if (!wrap) return;

                  if (wrap.dataset.ptkBoard === "1") {
                    return;
                  }

                  wrap.classList.add("ptk-board");
                  wrap.dataset.ptkBoard = "1";

                  wrap.innerHTML = `
                    <div class="ptk-board-row">
                      ${PTK_STATUS.map(
                        (s) => `
                          <section class="ptk-col" data-ptk-col="${s.key}">
                            <header class="ptk-col-head">
                              <span class="ptk-col-title">${s.label}</span>
                              <span class="ptk-col-count" data-ptk-count="${s.key}">0</span>
                            </header>
                            <div class="ptk-col-body"
                                data-ptk-dropzone="${s.key}"
                                aria-label="${s.label}"
                                tabindex="0"></div>
                          </section>
                        `
                      ).join("")}
                    </div>
                  `;

                  this.bindDND();
                },

                ensureSearchBar() {
                  const drawer = qs("#pt-drawer");
                  const body = qs("#pt-list");
                  if (!drawer || !body) return;

                  let bar = drawer.querySelector("#ptk-search-bar");
                  if (!bar) {
                    bar = document.createElement("div");
                    bar.id = "ptk-search-bar";
                    bar.className = "p-2 bg-white border-bottom";
                    bar.innerHTML = `
                      <div class="input-group" id="ptk-search-wrap" style="max-width:520px">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="feather icon-search"></i>
                          </span>
                        </div>
                        <input id="ptk-search"
                              class="form-control"
                              placeholder="Aufgaben durchsuchen… (Titel, Beschreibung, Mitarbeiter)"
                              autocomplete="off">
                        <div class="input-group-append">
                          <button id="ptk-search-clear"
                                  class="btn btn-outline-secondary"
                                  type="button"
                                  title="Leeren">&times;</button>
                        </div>
                      </div>
                    `;

                    const head = drawer.querySelector(".notes-head");
                    if (head) {
                      head.insertAdjacentElement("afterend", bar);
                    } else {
                      drawer.insertBefore(bar, body);
                    }
                  }

                  this._searchEl = qs("#ptk-search");
                  const clearBtn = qs("#ptk-search-clear");

                  if (this._searchEl && !this._searchEl._wired) {
                    const run = debounce((ev) => this.setQuery(ev.target.value), 120);
                    this._searchEl.addEventListener("input", run);
                    this._searchEl.addEventListener("keydown", (e) => {
                      if (e.key === "Escape") {
                        this._searchEl.value = "";
                        this.setQuery("");
                        this._searchEl.blur();
                      }
                    });
                    this._searchEl._wired = true;
                  }

                  if (clearBtn && !clearBtn._wired) {
                    clearBtn.addEventListener("click", () => {
                      if (!this._searchEl) return;
                      this._searchEl.value = "";
                      this.setQuery("");
                      this._searchEl.focus();
                    });
                    clearBtn._wired = true;
                  }
                },

                renderSkeletonContent() {
                  qsa("[data-ptk-dropzone]").forEach((zone) => {
                    zone.innerHTML = `<div class="ptk-empty">Lade Aufgaben…</div>`;
                  });
                  const head = qs("#pt-title");
                  if (head) head.classList.add("ptk-loading");
                },

                setLoading(on) {
                  const head = qs("#pt-title");
                  if (!head) return;
                  head.classList.toggle("ptk-loading", !!on);
                },

                // --------------- data IO ----------------

                async loadTasks() {
                  const c = qs("#pt-customer_id")?.value;
                  const a = qs("#pt-alternative_id")?.value;
                  const p = qs("#pt-product_id")?.value || "";

                  if (!c || !a) {
                    this._tasks = [];
                    this.renderFiltered();
                    this.updateCardBadge();
                    return;
                  }

                  const url =
                    `${APP.endpoints.personalTasksIndex}` +
                    `?customer_id=${encodeURIComponent(c)}` +
                    `&alternative_id=${encodeURIComponent(a)}` +
                    (p ? `&product_id=${encodeURIComponent(p)}` : "");

                  try {
                    this.setLoading(true);
                    const res = await safeFetchJSON(url, { retries: 0 });

                    const tasks = Array.isArray(res?.tasks)
                      ? res.tasks
                      : Array.isArray(res)
                      ? res
                      : [];

                    this._tasks = tasks;
                    this.renderFiltered();

                    const badge = qs("#pt-count");
                    if (badge) badge.textContent = String(tasks.length);

                    this.updateCardBadge();
                  } catch (err) {
                    qsa("[data-ptk-dropzone]").forEach((zone) => {
                      zone.innerHTML = `
                        <div class="text-danger p-2 small">
                          Aufgaben konnten nicht geladen werden.<br>${esc(err.message || "")}
                        </div>
                      `;
                    });
                  } finally {
                    this.setLoading(false);
                  }
                },

                // --------------- filtering + rendering ---------------

                getFilteredTasks() {
                  const q = this._query;
                  if (!q) return this._tasks.slice();

                  const has = (txt) => txt && norm(txt).includes(q);

                  return this._tasks.filter((task) => {
                    if (has(task.task_title)) return true;
                    if (has(task.description)) return true;

                    const statusKey = String(task.task_status || "open").toLowerCase();
                    if (has(statusLabel(statusKey))) return true;

                    // task-level employees
                    if (Array.isArray(task.employees)) {
                      for (const emp of task.employees) {
                        const name = `${emp.lastname || ""} ${emp.name || ""}`;
                        if (has(name) || has(emp.lastname) || has(emp.name)) return true;
                      }
                    }

                    // step titles / descriptions / employees
                    if (Array.isArray(task.steps)) {
                      for (const step of task.steps) {
                        if (has(step.title) || has(step.description)) return true;

                        if (Array.isArray(step.employees)) {
                          for (const emp of step.employees) {
                            const name = `${emp.lastname || ""} ${emp.name || ""}`;
                            if (has(name) || has(emp.lastname) || has(emp.name)) {
                              return true;
                            }
                          }
                        }
                      }
                    }

                    return false;
                  });
                },

                renderFiltered() {
                  const filtered = this.getFilteredTasks();

                  // group by status
                  const buckets = Object.fromEntries(STATUS_ORDER.map((k) => [k, []]));
                  for (const t of filtered) {
                    const key = String(t.task_status || "open").toLowerCase();
                    (buckets[key] || buckets.open).push(t);
                  }

                  for (const key of STATUS_ORDER) {
                    const zone = qs(`[data-ptk-dropzone="${key}"]`);
                    const badge = qs(`[data-ptk-count="${key}"]`);
                    const arr = buckets[key] || [];

                    if (badge) badge.textContent = String(arr.length);

                    if (!zone) continue;

                    zone.innerHTML =
                      arr.map((t) => this.cardHTML(t, this._query)).join("") ||
                      `<div class="ptk-empty">Keine Aufgaben</div>`;
                  }

                  const totalBadge = qs("#pt-count");
                  if (totalBadge) totalBadge.textContent = String(filtered.length);

                  featherRefreshSoon();
                  this.bindCardEvents();
                  this.recountColumns();
                },

                // --------------- card rendering ---------------

                cardHTML(task, query) {
                  const q = query || "";
                  const status = String(task.task_status || "open").toLowerCase();
                  const color = task.color || "#8fc73e";

                  const title = task.task_title || "Aufgabe";
                  const descBlock = task.description
                    ? `<div class="ptk-card-desc">${hl(task.description, q)}</div>`
                    : "";

                  const steps = Array.isArray(task.steps) ? task.steps : [];
                  let stepsBlock = "";

                  if (steps.length) {
                    const rows = steps
                      .map((step) => {
                        const emps = Array.isArray(step.employees)
                          ? step.employees
                          : [];
                        const empHTML = emps
                          .map((emp) => {
                            const name = `${emp.lastname || ""} ${emp.name || ""}`.trim();
                            return `
                              <span class="ptk-emp ptk-emp--xs">
                                <img src="/images/employee/${emp.image || ""}"
                                    alt=""
                                    width="16"
                                    height="16"
                                    class="ptk-ava">
                                ${hl(name || emp.lastname || "", q)}
                              </span>
                            `;
                          })
                          .join("");

                        return `
                          <div class="ptk-step">
                            <div class="ptk-step-emps">${empHTML}</div>
                          </div>
                        `;
                      })
                      .join("");

                    stepsBlock = `
                      <div class="ptk-steps">
                        <div class="small text-muted mb-1">Verantwortliche</div>
                        ${rows}
                      </div>
                    `;
                  } else if (Array.isArray(task.employees) && task.employees.length) {
                    const emps = task.employees
                      .map((emp) => {
                        const name = `${emp.lastname || ""} ${emp.name || ""}`.trim();
                        return `
                          <span class="ptk-emp ptk-emp--xs">
                            <img src="/images/employee/${emp.image || ""}"
                                alt=""
                                width="16"
                                height="16"
                                class="ptk-ava">
                            ${hl(name || emp.lastname || "", q)}
                          </span>
                        `;
                      })
                      .join("");

                    stepsBlock = `
                      <div class="ptk-steps">
                        <div class="small text-muted mb-1">Verantwortliche</div>
                        <div class="ptk-step">
                          <div class="ptk-step-emps">${emps}</div>
                        </div>
                      </div>
                    `;
                  }

                  return `
                    <article class="ptk-card"
                            draggable="true"
                            data-ptk-card
                            data-id="${task.id}"
                            data-status="${status}">
                      <i class="ptk-card-color" style="background:${color}"></i>

                      <div style="position:absolute; right:8px; top:8px; display:flex; gap:4px; z-index:2">
                        <button type="button"
                                class="btn-icon"
                                title="Bearbeiten"
                                data-ptk-edit="${task.id}">
                          <i class="feather icon-edit-2"></i>
                        </button>
                        <button type="button"
                                class="btn-icon"
                                title="Löschen"
                                data-ptk-del="${task.id}">
                          <i class="feather icon-trash-2"></i>
                        </button>
                      </div>

                      <div class="ptk-card-main">
                        <div class="ptk-card-title">${hl(title, q)}</div>
                        ${descBlock}
                        ${stepsBlock}
                      </div>
                    </article>
                  `;
                },

                // --------------- DnD ---------------

                bindDND() {
                  qsa("[data-ptk-dropzone]").forEach((zone) => {
                    if (zone._ptkDnd) return;
                    zone._ptkDnd = true;

                    zone.addEventListener("dragover", (e) => {
                      e.preventDefault();
                      zone.classList.add("ptk-over");
                    });

                    zone.addEventListener("dragleave", () => {
                      zone.classList.remove("ptk-over");
                    });

                    zone.addEventListener("drop", async (e) => {
                      e.preventDefault();
                      zone.classList.remove("ptk-over");

                      const id = e.dataTransfer.getData("text/ptk-id");
                      const from = e.dataTransfer.getData("text/ptk-from");
                      const to = zone.getAttribute("data-ptk-dropzone");

                      if (!id || !to || from === to) return;

                      const card = qs(`[data-ptk-card][data-id="${id}"]`);
                      if (card) {
                        zone.appendChild(card);
                        card.dataset.status = to;
                      }

                      try {
                        await this.updateStatus(id, to);
                      } catch (err) {
                        Swal.fire("Fehler", err.message || "Status konnte nicht gespeichert werden.", "error");
                        this.loadTasks();
                      }
                    });
                  });
                },

                bindCardEvents() {
                  qsa("[data-ptk-card]").forEach((card) => {
                    if (card._ptkBound) return;
                    card._ptkBound = true;

                    card.addEventListener("dragstart", (e) => {
                      e.dataTransfer.setData("text/ptk-id", card.dataset.id);
                      e.dataTransfer.setData("text/ptk-from", card.dataset.status || "open");
                      e.dataTransfer.effectAllowed = "move";
                      setTimeout(() => card.classList.add("ptk-dragging"), 0);
                    });

                    card.addEventListener("dragend", () => {
                      card.classList.remove("ptk-dragging");
                    });
                  });

                  this.recountColumns();
                },

                recountColumns() {
                  for (const key of STATUS_ORDER) {
                    const n = qsa(
                      `[data-ptk-dropzone="${key}"] > [data-ptk-card]`
                    ).length;
                    const badge = qs(`[data-ptk-count="${key}"]`);
                    if (badge) badge.textContent = String(n);
                  }
                },

                // --------------- CRUD ---------------

                async updateStatus(id, status) {
                  const url = APP.endpoints.personalTasksUpdate(id);
                  const resp = await fetch(url, {
                    method: "PUT",
                    credentials: "same-origin",
                    headers: {
                      "Content-Type": "application/json",
                      "X-Requested-With": "XMLHttpRequest",
                      "X-CSRF-TOKEN": CSRF(),
                    },
                    body: JSON.stringify({ task_status: status }),
                  });

                  const json = await resp.json().catch(() => ({}));
                  if (!resp.ok || json?.success === false) {
                    throw new Error(json?.message || "Status konnte nicht gespeichert werden.");
                  }

                  const t = this._tasks.find((x) => String(x.id) === String(id));
                  if (t) t.task_status = status;

                  this.recountColumns();
                  this.updateCardBadge();
                },

                async submitForm(ev) {
                  ev.preventDefault();

                  const title = qs("#pt-task_title")?.value.trim() || "";
                  if (!title) {
                    Swal.fire("Fehler", "Aufgabentitel ist erforderlich.", "error");
                    return;
                  }

                  const customerId = Number(qs("#pt-customer_id")?.value || 0);
                  const alternativeId = Number(qs("#pt-alternative_id")?.value || 0);
                  const productIdRaw = qs("#pt-product_id")?.value || "";

                  if (!customerId || !alternativeId) {
                    Swal.fire("Fehler", "Der Kontext (Kunde/Alternative) fehlt.", "error");
                    return;
                  }

                  const payload = {
                    customer_id: customerId,
                    alternative_id: alternativeId,
                    product_id: productIdRaw ? Number(productIdRaw) : null,

                    task_title: title,
                    description: qs("#pt-description")?.value.trim() || null,
                    start_date: qs("#pt-start_date")?.value || null,
                    due_date: qs("#pt-due_date")?.value || null,
                    due_time: qs("#pt-due_time")?.value || null,
                    priority: qs("#pt-priority")?.value || "normal",
                    color: qs("#pt-color")?.value || "#8fc73e",
                  };

                  if (window.jQuery) {
                    const emps = jQuery("#pt-employee_ids").val() || [];
                    payload.employee_ids = emps;
                  }

                  const isEdit = !!this._editingId;
                  const url = isEdit
                    ? APP.endpoints.personalTasksUpdate(this._editingId)
                    : APP.endpoints.personalTasksStore;
                  const method = isEdit ? "PUT" : "POST";

                  try {
                    const resp = await fetch(url, {
                      method,
                      credentials: "same-origin",
                      headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF(),
                      },
                      body: JSON.stringify(payload),
                    });

                    const json = await resp.json().catch(() => ({}));
                    if (!resp.ok || json?.success === false) {
                      throw new Error(json?.message || "Aufgabe konnte nicht gespeichert werden.");
                    }

                    const saved =
                      json.task ||
                      json.data ||
                      json;

                    if (isEdit) {
                      const idx = this._tasks.findIndex(
                        (t) => String(t.id) === String(this._editingId)
                      );
                      if (idx !== -1) this._tasks[idx] = saved;
                    } else {
                      this._tasks.push(saved);
                    }

                    this._editingId = null;
                    if (qs("#pt-form")) qs("#pt-form").reset();
                    if (window.jQuery) {
                      jQuery("#pt-employee_ids").val(null).trigger("change");
                    }

                    this.renderFiltered();
                    this.updateCardBadge();

                    Swal.fire(
                      "Gespeichert",
                      isEdit ? "Aufgabe aktualisiert." : "Aufgabe angelegt.",
                      "success"
                    );
                  } catch (err) {
                    Swal.fire("Fehler", err.message || "Serverfehler", "error");
                  }
                },

                fillForm(id) {
                  const task = this._tasks.find((t) => String(t.id) === String(id));
                  if (!task) return;

                  this._editingId = id;

                  const set = (sel, val) => {
                    const el = qs(sel);
                    if (el) el.value = val ?? "";
                  };

                  set("#pt-task_title", task.task_title || "");
                  set("#pt-description", task.description || "");
                  set("#pt-start_date", task.start_date || "");
                  set("#pt-due_date", task.due_date || "");
                  set("#pt-due_time", task.due_time || "");
                  set("#pt-priority", task.priority || "normal");
                  set("#pt-color", task.color || "#8fc73e");

                  if (window.jQuery) {
                    const ids = Array.isArray(task.employees)
                      ? task.employees.map((e) => e.id)
                      : [];
                    jQuery("#pt-employee_ids").val(ids).trigger("change");
                  }
                },

                async deleteTask(id) {
                  const ok = await Swal.fire({
                    title: "Aufgabe löschen?",
                    text: "Dieser Vorgang kann nicht rückgängig gemacht werden.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ja, löschen",
                  });
                  if (!ok.isConfirmed) return;

                  try {
                    const resp = await fetch(APP.endpoints.personalTasksDestroy(id), {
                      method: "DELETE",
                      credentials: "same-origin",
                      headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": CSRF(),
                      },
                    });

                    const json = await resp.json().catch(() => ({}));
                    if (!resp.ok || json?.success === false) {
                      throw new Error(json?.message || "Löschen fehlgeschlagen.");
                    }

                    this._tasks = this._tasks.filter(
                      (t) => String(t.id) !== String(id)
                    );
                    this.renderFiltered();
                    this.updateCardBadge();

                    Swal.fire("Gelöscht", "Aufgabe wurde gelöscht.", "success");
                  } catch (err) {
                    Swal.fire("Fehler", err.message || "Serverfehler", "error");
                  }
                },
              };

              // ------------------------------------------------
              // Global bindings
              // ------------------------------------------------

              // Drawer open/close
              qs("#pt-backdrop")?.addEventListener("click", () => PTK.hide());
              qsa("[data-pt-close]").forEach((btn) =>
                btn.addEventListener("click", () => PTK.hide())
              );

              // Form submit
              qs("#pt-form")?.addEventListener("submit", (ev) => PTK.submitForm(ev));

              // Edit / delete buttons (delegated)
              document.addEventListener("click", (e) => {
                const del = e.target.closest("[data-ptk-del]");
                if (del) {
                  e.preventDefault();
                  const id = del.getAttribute("data-ptk-del");
                  if (id) PTK.deleteTask(id);
                  return;
                }

                const edit = e.target.closest("[data-ptk-edit]");
                if (edit) {
                  e.preventDefault();
                  const id = edit.getAttribute("data-ptk-edit");
                  if (id) PTK.fillForm(id);
                }
              });

              // Custom event fallback: open-personal-tasks
              document.addEventListener("open-personal-tasks", (e) => {
                const d = e.detail || {};
                PTK.open(d.customerId, d.alternativeId, d.productId, d.title);
              });

              // Export to global
              window.PersonalTasksUI = PTK;
            })();
          </script>

           <script>
              (() => {
                "use strict";

                /* --------------------------------------------------------------------------
                * Team Hover Popover (fixed)
                * - Reads assigned-by / assigned-at from:
                *    1) avatar element itself (img/li/span with data-emp-id)
                *    2) closest <li> wrapper (even if LI does NOT have data-emp-id)
                *    3) closest parent element
                * - Shows Stage (German) from nearest .card or tr.list-row-item dataset.stage
                *   using window.LeadUI.APP.stageNames when available
                * ------------------------------------------------------------------------ */

                const EMP_SRC = "/images/employee";
                const employees = Array.isArray(window.ALL_EMPLOYEES) ? window.ALL_EMPLOYEES : [];

                const byId = new Map(
                  employees
                    .map((e) => {
                      const id = Number(e?.id);
                      return Number.isFinite(id) ? [id, e] : null;
                    })
                    .filter(Boolean)
                );

                const fallbackStageNames = {
                  lead: "Lead",
                  offer: "Angebot",
                  follow_up: "Nachfassen",
                  accepted: "Annehmen",
                  deal: "Auftrag",
                  project: "Montage",
                  completed: "Abschluss",
                  archive: "Archiv",
                  junk: "Junk",
                };

                const stageNames =
                  (window.LeadUI?.APP?.stageNames && typeof window.LeadUI.APP.stageNames === "object"
                    ? window.LeadUI.APP.stageNames
                    : fallbackStageNames);

                let pop = null;
                let anchor = null;
                let hideTimer = null;

                const esc = (s) =>
                  String(s ?? "").replace(/[&<>"']/g, (m) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m]));

                const pad2 = (n) => String(n).padStart(2, "0");

                const parseAnyDate = (raw) => {
                  const s = String(raw || "").trim();
                  if (!s) return null;

                  // ISO works directly
                  let d = new Date(s);
                  if (!Number.isNaN(d.getTime())) return d;

                  // "YYYY-MM-DD HH:mm:ss" -> "YYYY-MM-DDTHH:mm:ss"
                  if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/.test(s)) {
                    d = new Date(s.replace(" ", "T"));
                    if (!Number.isNaN(d.getTime())) return d;
                  }

                  return null;
                };

                const fmtDE = (raw) => {
                  const d = parseAnyDate(raw);
                  if (!d) return "–";
                  try {
                    return d.toLocaleString("de-DE");
                  } catch {
                    return `${pad2(d.getDate())}.${pad2(d.getMonth() + 1)}.${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
                  }
                };

                function ensurePop() {
                  if (pop) return pop;

                  pop = document.createElement("div");
                  pop.className = "team-popover";
                  pop.setAttribute("role", "dialog");
                  pop.setAttribute("aria-label", "Team");

                  pop.innerHTML = `
                    <div class="team-popover__title">
                      <div class="t1">Team</div>
                      <div class="t2" data-subline></div>
                    </div>
                    <div class="team-popover__list" data-list></div>
                  `;

                  document.body.appendChild(pop);

                  pop.addEventListener("mouseenter", () => hideTimer && clearTimeout(hideTimer));
                  pop.addEventListener("mouseleave", () => scheduleHide());

                  return pop;
                }

                function readAttrChain(node, keyKebab, keyDataset) {
                  if (!node) return "";
                  const direct = node.getAttribute?.(keyKebab) || node.dataset?.[keyDataset] || "";
                  return String(direct || "").trim();
                }

                function getContextStage(ul) {
                  const ctx = ul.closest?.(".card, tr.list-row-item") || null;
                  const raw = String(ctx?.dataset?.stage || "").trim().toLowerCase();
                  if (!raw) return "";
                  return stageNames[raw] || raw;
                }

                function collectAvatars(ul) {
                  // Keep DOM order: select anything with data-emp-id (img or li etc.)
                  const nodes = Array.from(ul.querySelectorAll("[data-emp-id]"));

                  const out = [];
                  for (const n of nodes) {
                    const id = Number(n.getAttribute("data-emp-id"));
                    if (!Number.isFinite(id) || id <= 0) continue;

                    // IMPORTANT FIX:
                    // Your markup usually has data-emp-id on IMG but assigned-by/date on LI.
                    // So we read from: n, closest LI, and parent.
                    const li = n.closest("li");
                    const parent = n.parentElement;

                    const assignedBy =
                      readAttrChain(n, "data-assigned-by", "assignedBy") ||
                      readAttrChain(li, "data-assigned-by", "assignedBy") ||
                      readAttrChain(parent, "data-assigned-by", "assignedBy");

                    const assignedAt =
                      readAttrChain(n, "data-assigned-at", "assignedAt") ||
                      readAttrChain(li, "data-assigned-at", "assignedAt") ||
                      readAttrChain(parent, "data-assigned-at", "assignedAt");

                    const position =
                      readAttrChain(n, "data-position", "position") ||
                      readAttrChain(li, "data-position", "position") ||
                      readAttrChain(parent, "data-position", "position");

                    const stage =
                      readAttrChain(n, "data-stage", "stage") ||
                      readAttrChain(li, "data-stage", "stage") ||
                      readAttrChain(parent, "data-stage", "stage");

                    const stageLabel =
                      readAttrChain(n, "data-stage-label", "stageLabel") ||
                      readAttrChain(li, "data-stage-label", "stageLabel") ||
                      readAttrChain(parent, "data-stage-label", "stageLabel") ||
                      getContextStage(ul);

                    out.push({ id, assignedBy, assignedAt, position, stage, stageLabel });
                  }
                  return out;
                }

                function uniqueById(list) {
                  const seen = new Set();
                  const out = [];
                  for (const it of list) {
                    if (seen.has(it.id)) continue;
                    seen.add(it.id);
                    out.push(it);
                  }
                  return out;
                }

                // 1) Your buildRow is OK now (it WILL show phase) ✅
                  // The missing part is: you must PASS stage / stageLabel into buildRow
                  // from the DOM (data-* attrs) OR from the API payload (team_assignments).

                  function buildRow({ id, assignedBy, assignedAt, position, stage, stageLabel }) {
                    const emp = byId.get(Number(id)) || null;

                    const name = emp ? `${emp.lastname || ""} ${emp.name || ""}`.trim() : `#${id}`;
                    const img = emp?.image ? `${EMP_SRC}/${emp.image}` : `${EMP_SRC}/noimage.png`;

                    const role =
                      (position && String(position).trim()) ||
                      (emp?.position ? String(emp.position) : "") ||
                      (emp?.role ? String(emp.role) : "") ||
                      "Mitarbeiter";

                    const by = (assignedBy && String(assignedBy).trim()) || "–";
                    const when = fmtDE(assignedAt);

                    const stageText =
                      (stageLabel && String(stageLabel).trim()) ||
                      (stage && String(stage).trim()) ||
                      "–";

                    return `
                      <div class="team-popover__item">
                        <img class="team-popover__avatar" src="${esc(img)}" alt="${esc(name)}">
                        <div style="min-width:0;">
                          <div class="team-popover__name">${esc(name)}</div>
                          <div class="team-popover__meta">${esc(role)}</div>

                          <div class="team-popover__meta">
                            <strong>Phase:</strong> ${esc(stageText)}
                          </div>

                          <div class="team-popover__meta">
                            <strong>Zugewiesen von:</strong> ${esc(by)}
                            <span style="padding:0 6px;">•</span>
                            <strong><i class="feather icon-calendar"></i></strong> ${esc(when)}
                          </div>
                        </div>
                      </div>
                    `;
                  }

                  // 2) Build popover rows from EACH avatar <li> dataset (this is what makes Phase show)
                  function rowsFromTeamEl(teamEl) {
                    const lis = Array.from(teamEl.querySelectorAll('li[data-emp-id]'));
                    return lis.map((li) => ({
                      id: li.dataset.empId,
                      assignedBy: li.dataset.assignedBy,     // must exist on li
                      assignedAt: li.dataset.assignedAt,     // must exist on li
                      position: li.dataset.position,
                      stage: li.dataset.stage,              // must exist on li
                      stageLabel: li.dataset.stageLabel,    // must exist on li (German label)
                    }));
                  }

                  // Example usage inside your hover/open logic:
                  function renderTeamPopover(teamEl, popoverEl) {
                    const rows = rowsFromTeamEl(teamEl);
                    popoverEl.innerHTML = rows.map(buildRow).join("") || `<div class="team-popover__empty">–</div>`;
                    if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
                  }



                function renderFor(ul) {
                  const p = ensurePop();
                  const listEl = p.querySelector("[data-list]");
                  const subEl = p.querySelector("[data-subline]");

                  const stageLabel = getContextStage(ul);
                  const avatars = uniqueById(collectAvatars(ul));

                  const countText = `${avatars.length} Mitglied${avatars.length === 1 ? "" : "er"}`;
                  subEl.textContent = stageLabel ? `${countText} • Phase: ${stageLabel}` : countText;

                  if (!avatars.length) {
                    listEl.innerHTML = `
                      <div class="team-popover__item">
                        <div style="min-width:0;">
                          <div class="team-popover__name">Kein Team</div>
                          <div class="team-popover__meta">—</div>
                        </div>
                      </div>
                    `;
                    return;
                  }

                  listEl.innerHTML = avatars.map(buildRow).join("");
                }

                function placeNear(el) {
                  const p = ensurePop();
                  const r = el.getBoundingClientRect();

                  const pw = p.offsetWidth || 320;
                  const ph = p.offsetHeight || 220;

                  const pad = 12;
                  const vw = window.innerWidth;
                  const vh = window.innerHeight;

                  let left = r.left + r.width / 2 - pw / 2;
                  let top = r.top - ph - 10;

                  left = Math.max(pad, Math.min(left, vw - pw - pad));
                  if (top < pad) top = r.bottom + 10;
                  if (top + ph > vh - pad) top = Math.max(pad, vh - ph - pad);

                  p.style.left = `${Math.round(left)}px`;
                  p.style.top = `${Math.round(top)}px`;
                }

                function openFor(ul) {
                  if (!ul) return;
                  hideTimer && clearTimeout(hideTimer);
                  anchor = ul;

                  renderFor(ul);
                  placeNear(ul);

                  ensurePop().classList.add("is-open");
                }

                function closeNow() {
                  if (!pop) return;
                  pop.classList.remove("is-open");
                  anchor = null;
                }

                function scheduleHide() {
                  hideTimer && clearTimeout(hideTimer);
                  hideTimer = setTimeout(closeNow, 120);
                }

                function getTeamTarget(node) {
                  return node?.closest ? node.closest("ul[data-team-hover]") : null;
                }

                document.addEventListener(
                  "mouseover",
                  (e) => {
                    const ul = getTeamTarget(e.target);
                    if (!ul) return;

                    const from = e.relatedTarget;
                    if (from && ul.contains(from)) return;

                    if (anchor === ul && pop?.classList.contains("is-open")) return;
                    openFor(ul);
                  },
                  true
                );

                document.addEventListener(
                  "mouseout",
                  (e) => {
                    if (!anchor) return;

                    const to = e.relatedTarget;
                    if (to && (anchor.contains(to) || (pop && pop.contains(to)))) return;

                    scheduleHide();
                  },
                  true
                );

                window.addEventListener(
                  "scroll",
                  () => {
                    if (anchor && pop?.classList.contains("is-open")) placeNear(anchor);
                  },
                  true
                );

                window.addEventListener("resize", () => {
                  if (anchor && pop?.classList.contains("is-open")) placeNear(anchor);
                });

                document.addEventListener("keydown", (e) => {
                  if (e.key === "Escape") closeNow();
                });
              })();
          </script>

          <!-- Aufgabe Script  -->
          <script>
          (function () {
            "use strict";

            const root  = window.LeadUI || {};
            const APP   = root.APP || {};
            const net   = root.net || {};
            const utils = root.utils || {};

            const qs  = (s, el=document) => el.querySelector(s);
            const qsa = (s, el=document) => Array.from(el.querySelectorAll(s));

            const esc = (s) => String(s ?? "").replace(/[&<>"]/g, (m) => ({ "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;" }[m]));
            const dateOnly = (v) => (v ? String(v).slice(0, 10) : "");
            const timeOnly = (v) => (v ? String(v).slice(0, 8) : "");
            const isZeroTime = (v) => !v || String(v).startsWith("00:00");
            const addDays = (yyyy_mm_dd, days) => {
              if (!yyyy_mm_dd) return "";
              const d = new Date(yyyy_mm_dd + "T00:00:00");
              d.setDate(d.getDate() + (days || 0));
              return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,"0")}-${String(d.getDate()).padStart(2,"0")}`;
            };

            const EMP_IMG_BASE = (window.__EMP_IMG_BASE || "/images/employee").replace(/\/+$/, "");
            const employeeImageUrl = (img) => {
              if (!img) return "";
              if (/^https?:\/\//i.test(img)) return img;
              return EMP_IMG_BASE + "/" + String(img).replace(/^\/+/, "");
            };

            const EMP_ENDPOINT = APP?.endpoints?.getAllEmployees || "/getAllEmployees";


            function resolveEmployeeCalendarUrl(empId) {
              if (APP?.endpoints?.employeeCalendar && typeof APP.endpoints.employeeCalendar === "function") {
                return APP.endpoints.employeeCalendar(empId);
              }
              const sel = qs("#ap-emp-filter");
              const base = sel?.dataset?.empCalBase || "";
              if (base) return String(base).replace(/\/+$/, "") + "/" + encodeURIComponent(empId);
              return "/get_employee_calendar/" + encodeURIComponent(empId);
            }

            function safeJsonFetch(url) {
              return fetch(url, {
                method: "GET",
                credentials: "same-origin",
                headers: { "Accept": "application/json" }
              }).then(async (r) => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
              });
            }

            // small styles
            (function injectStyles() {
              if (qs("#ap-unified-style")) return;
              const st = document.createElement("style");
              st.id = "ap-unified-style";
              st.textContent = `
                .ap-loading-mask{position:absolute;inset:0;background:rgba(255,255,255,.72);display:flex;align-items:center;justify-content:center;z-index:50;border-radius:6px;}
                .ap-loading-mask .spin{width:18px;height:18px;border-radius:999px;border:2px solid rgba(0,0,0,.12);border-top-color:rgba(0,0,0,.55);animation:apspin 1s linear infinite;}
                @keyframes apspin{to{transform:rotate(360deg)}}
                .ap-creator-line{font-size:11px;opacity:.85;margin-top:2px;display:flex;gap:6px;align-items:center;}
                .ap-emp-avatars{display:flex;gap:6px;flex-wrap:wrap;align-items:center;margin-top:4px;}
                .ap-emp-pill{display:inline-flex;align-items:center;gap:6px;padding:2px 8px 2px 2px;border-radius:999px;border:1px solid rgba(0,0,0,.10);background:#fff;}
                .ap-emp-pill .av{width:22px;height:22px;border-radius:999px;overflow:hidden;border:1px solid rgba(0,0,0,.12);display:inline-flex;align-items:center;justify-content:center;background:#f8f9fa;}
                .ap-emp-pill .av img{width:100%;height:100%;object-fit:cover;display:block;}
                .ap-emp-pill .fb{font-size:10px;font-weight:900;opacity:.75;}
                .ap-emp-pill .nm{font-size:12px;font-weight:700;}
                .ap-source-badge{font-size:10px;font-weight:900;padding:2px 8px;border-radius:999px;border:1px solid rgba(0,0,0,.12);opacity:.85;}
              `;
              document.head.appendChild(st);
            })();

            const UI = {
              _ctx: {},
              _calendar: null,

              _employees: [],
              _selectedEmpId: "",

              // mode:
              // - "customer" => load customer appointmentsIndex
              // - "employee" => load ONLY employee calendar endpoint
              _mode: "customer",

              _customerAppointments: [],
              _employeeEvents: [],
              _rendered: [],

              open(customerId, alternativeId, productId, title, contact) {
                this._ctx = { customerId: customerId || "", alternativeId: alternativeId || "", productId: productId || "" };

                const titleEl = qs("#ap-title");
                if (titleEl) titleEl.textContent = title || "Termine";

                this.resetForm();
                this.prefillFromContact(contact || {});

                qs("#ap-backdrop")?.classList.add("show");
                qs("#ap-drawer")?.classList.add("open");
                document.body.style.overflow = "hidden";

                this.switchTab("calendar");
                this.switchView("calendar");

                this.ensureEmployeesLoaded()
                  .then(() => {
                    this.initSelect2ForFormEmployees();
                    return this.onEmpFilterChanged(true);
                  })
                  .catch(() => this.onEmpFilterChanged(true));
              },

              hide() {
                qs("#ap-backdrop")?.classList.remove("show");
                qs("#ap-drawer")?.classList.remove("open");
                document.body.style.overflow = "";
              },

              switchTab(tab) {
                qsa(".ap-tab-link").forEach(b => b.classList.toggle("active", b.dataset.tab === tab));
                qsa(".ap-tab-content").forEach(c => c.classList.remove("active"));
                const target = qs(tab === "calendar" ? "#ap-tab-calendar" : "#ap-tab-form");
                if (target) target.classList.add("active");
                if (tab === "calendar") setTimeout(() => { this.initCalendar(); this.refreshCalendar(); }, 150);
              },

              switchView(viewName) {
                qsa("[data-view]").forEach(btn => {
                  const active = btn.dataset.view === viewName;
                  btn.classList.toggle("active", active);
                  btn.classList.toggle("btn-outline-primary", active);
                  btn.classList.toggle("btn-outline-secondary", !active);
                });

                const calWrap = qs("#ap-calendar-wrap");
                const cardWrap = qs("#ap-card-view");
                if (calWrap) calWrap.style.display = viewName === "calendar" ? "block" : "none";
                if (cardWrap) cardWrap.style.display = viewName === "cards" ? "grid" : "none";
                if (viewName === "calendar") setTimeout(() => this.refreshCalendar(), 120);
              },

              showLoading(on) {
                const wrap = qs("#ap-calendar-wrap");
                if (!wrap) return;

                let mask = qs("#ap-loading-mask");
                if (on) {
                  if (!mask) {
                    mask = document.createElement("div");
                    mask.id = "ap-loading-mask";
                    mask.className = "ap-loading-mask";
                    mask.innerHTML = `<div class="spin"></div>`;
                    wrap.style.position = "relative";
                    wrap.appendChild(mask);
                  }
                } else {
                  mask?.remove();
                }
              },

              /* =========================
               * EMPLOYEES (filter + form select2)
               * =======================*/
              async ensureEmployeesLoaded() {
                const sel = qs("#ap-emp-filter");
                if (!sel) return;
                if (this._employees.length) return;

                const res = await net.safeFetchJSON(EMP_ENDPOINT, { retries: 0 });
                const rows = Array.isArray(res) ? res : (Array.isArray(res?.employees) ? res.employees : []);

                this._employees = rows.map(e => ({
                  id: String(e.emp_id ?? e.id ?? ""),
                  name: e.name || "",
                  lastname: e.lastname || "",
                  image: e.image || null
                })).filter(e => e.id);

                // IMPORTANT: empty option means "customer calendar"
                sel.innerHTML =
                  `<option value="">Aktueller Kunde (Termine)</option>` +
                  this._employees
                    .slice()
                    .sort((a,b) => (a.lastname||"").localeCompare(b.lastname||""))
                    .map(e => `<option value="${esc(e.id)}">${esc((e.lastname + " " + e.name).trim())}</option>`)
                    .join("");

                // If filter select2 exists, attach hooks
                if (window.jQuery) {
                  const $sel = window.jQuery(sel);
                  if ($sel.length && !$sel.data("select2")) {
                    $sel.select2({
                      placeholder: "Aktueller Kunde (Termine)",
                      allowClear: true,
                      dropdownParent: window.jQuery("#ap-drawer"),
                      width: "100%"
                    });
                    $sel.on("select2:select select2:clear", () => this.onEmpFilterChanged(false));
                  }

                  const $jump = window.jQuery("#ap-jump");
                  if ($jump.length && !$jump.data("select2")) {
                    $jump.select2({
                      placeholder: "Termin auswählen…",
                      allowClear: true,
                      dropdownParent: window.jQuery("#ap-drawer"),
                      width: "100%"
                    });
                  }
                }
              },

              initSelect2ForFormEmployees() {
                if (!window.jQuery) return;
                const el = qs("#ap-employee_ids");
                if (!el) return;

                const $el = window.jQuery(el);
                if ($el.data("select2")) return;

                $el.select2({
                  placeholder: "Mitarbeiter wählen…",
                  dropdownParent: window.jQuery("#ap-drawer"),
                  width: "100%"
                });
              },

              getEmpFilterValue() {
                const sel = qs("#ap-emp-filter");
                if (!sel) return "";
                return String(sel.value || "");
              },

              async onEmpFilterChanged(isFirstOpen) {
                const empId = this.getEmpFilterValue();
                this._selectedEmpId = empId;

                // Always return to calendar view
                this.switchTab("calendar");
                this.switchView("calendar");
                this.initCalendar();

                if (empId) {
                  // EMPLOYEE MODE => only employee calendar
                  this._mode = "employee";
                  await this.loadEmployeeCalendar(empId);
                } else {
                  // CUSTOMER MODE => customer appointments
                  this._mode = "customer";
                  await this.loadCustomerAppointments();
                }

                this.renderCalendarEvents();
                this.renderCardList();
                this.populateJumpDropdown();
                this.updateCount();

                setTimeout(() => this.refreshCalendar(), 160);
              },

              /* =========================
               * DATA LOADERS
               * =======================*/
              async loadCustomerAppointments() {
                const { customerId, alternativeId, productId } = this._ctx;
                if (!customerId || !APP?.endpoints?.appointmentsIndex) {
                  this._customerAppointments = [];
                  this._rendered = [];
                  return;
                }

                const url =
                  `${APP.endpoints.appointmentsIndex}?customer_id=${encodeURIComponent(customerId)}` +
                  (alternativeId ? `&alternative_id=${encodeURIComponent(alternativeId)}` : "") +
                  (productId ? `&product_id=${encodeURIComponent(productId)}` : "");

                this.showLoading(true);
                try {
                  const res = await net.safeFetchJSON(url, { retries: 0 });
                  const list = Array.isArray(res?.appointments) ? res.appointments : (Array.isArray(res) ? res : []);

                  this._customerAppointments = list.map(a => ({
                    ...a,
                    __creator_label:
                      (typeof a.created_by === "string" ? a.created_by : null) ||
                      a.created_by_name ||
                      a.creator_name ||
                      (a.created_by ? `User #${a.created_by}` : "")
                  }));

                  this._rendered = this._customerAppointments.map(a => this.mapLeadAppointment(a));
                } catch (e) {
                  this._customerAppointments = [];
                  this._rendered = [];
                  window.Swal?.fire("Fehler", "Kunden-Termine konnten nicht geladen werden.", "error");
                } finally {
                  this.showLoading(false);
                }
              },

              async loadEmployeeCalendar(empId) {
                const url = resolveEmployeeCalendarUrl(empId);

                this.showLoading(true);
                try {
                  const json = await safeJsonFetch(url);
                  const rows = Array.isArray(json?.data) ? json.data : [];

                  this._employeeEvents = rows;
                  this._rendered = rows.map(r => this.mapEmployeeEvent(r));
                } catch (e) {
                  this._employeeEvents = [];
                  this._rendered = [];
                  window.Swal?.fire("Fehler", "Mitarbeiter-Kalender konnte nicht geladen werden.", "error");
                } finally {
                  this.showLoading(false);
                }
              },

              /* =========================
               * NORMALIZERS
               * =======================*/
              mapLeadAppointment(a) {
                const sd = dateOnly(a.start_date);
                const ed = dateOnly(a.end_date) || sd;
                const st = timeOnly(a.start_time);
                const et = timeOnly(a.end_time);

                const allDay = !st || isZeroTime(st);

                let start, end;
                if (allDay) {
                  start = sd;
                  end = addDays(ed, 1);
                } else {
                  start = `${sd}T${st || "00:00:00"}`;
                  end   = `${ed}T${et || st || "23:59:59"}`;
                }

                const emps = Array.isArray(a.employees) ? a.employees : [];
                const employees = emps.map(e => {
                  const full = ((e.lastname ? e.lastname + " " : "") + (e.name || "")).trim();
                  return { id: String(e.id), full, initials: (e.lastname || e.name || "?").slice(0,2).toUpperCase(), image: e.image ? employeeImageUrl(e.image) : "" };
                });

                return {
                  _source: "customer",
                  _raw: a,
                  id: `lead-${a.id}`,
                  title: a.name || "Termin",
                  start, end, allDay,
                  color: a.color || "#74b2d4",
                  creator_label: a.__creator_label || "",
                  description: a.note || "",
                  type: "appointment",
                  employees
                };
              },

              mapEmployeeEvent(r) {
                const sd = dateOnly(r.start_date);
                const ed = dateOnly(r.end_date) || sd;
                const st = timeOnly(r.start_time);
                const et = timeOnly(r.end_time);

                const allDay = isZeroTime(st) && isZeroTime(et);

                let start, end;
                if (allDay) {
                  start = sd;
                  end = addDays(ed, 1);
                } else {
                  start = `${sd}T${st || "00:00:00"}`;
                  end   = `${ed}T${et || "23:59:59"}`;
                }

                const emps = Array.isArray(r.employees) ? r.employees : [];
                const employees = emps.map(e => {
                  const full = ((e.lastname ? e.lastname + " " : "") + (e.name || "")).trim();
                  return { id: String(e.employee_id), full, initials: (e.lastname || e.name || "?").slice(0,2).toUpperCase(), image: e.image ? employeeImageUrl(e.image) : "" };
                });

                return {
                  _source: "employee",
                  _raw: r,
                  id: `${r.type || "event"}-${r.id}`,
                  title: r.title || "Eintrag",
                  start, end, allDay,
                  color: r.taskColor || "#74b2d4",
                  creator_label: "",
                  description: r.description || "",
                  type: r.type || "event",
                  employees
                };
              },

              /* =========================
               * CALENDAR
               * =======================*/
              initCalendar() {
                const calEl = qs("#ap-fullcalendar");
                if (!calEl || !window.FullCalendar) return;
                if (this._calendar) return;

                this._calendar = new FullCalendar.Calendar(calEl, {
                  locale: "de",
                  initialView: "dayGridMonth",
                  headerToolbar: { left: "prev,next today", center: "title", right: "dayGridMonth,timeGridWeek,listWeek" },
                  height: "100%",
                  navLinks: true,
                  editable: false,
                  dayMaxEvents: true,
                  events: [],

                  eventContent: (arg) => {
                    const p = arg.event.extendedProps || {};
                    const title = esc(arg.event.title || "");
                    const creator = p.creator_label ? `<div class="ap-creator-line"><i class="feather icon-user" style="font-size:12px;"></i><span>${esc(p.creator_label)}</span></div>` : "";

                    const emps = Array.isArray(p.employees) ? p.employees : [];
                    const avatars = emps.length ? `
                      <div class="ap-emp-avatars">
                        ${emps.slice(0,4).map(e => {
                          const nm = esc(e.full || "");
                          const img = e.image ? esc(e.image) : "";
                          const fb  = esc((e.initials || "?").slice(0,2));
                          return `<span title="${nm}" style="width:18px;height:18px;border-radius:999px;overflow:hidden;border:1px solid rgba(0,0,0,.12);display:inline-flex;align-items:center;justify-content:center;background:#fff;">
                            ${img ? `<img src="${img}" alt="${nm}" style="width:100%;height:100%;object-fit:cover;display:block;">` : `<span style="font-size:9px;font-weight:900;opacity:.75;">${fb}</span>`}
                          </span>`;
                        }).join("")}
                        ${emps.length > 4 ? `<span style="font-size:10px;font-weight:900;opacity:.8;">+${emps.length-4}</span>` : ""}
                      </div>` : "";

                    return { html: `<div><div style="font-weight:800;font-size:12px;">${title}</div>${creator}${avatars}</div>` };
                  },

                  dateClick: (info) => {
                    // ALWAYS allow creation (even if employee is selected)
                    this.resetForm();

                    // preset date
                    qs("#ap-start_date") && (qs("#ap-start_date").value = info.dateStr);

                    // preset selected employee into appointment employees
                    this.preselectEmployeeIntoForm();

                    qs("#ap-form-title") && (qs("#ap-form-title").textContent =
                      "Neuer Termin am " + new Date(info.dateStr + "T00:00:00").toLocaleDateString("de-DE")
                    );

                    this.switchTab("form");
                  },

                  eventClick: (info) => {
                    const id = String(info.event.id || "");
                    // only lead appointments are editable
                    if (id.startsWith("lead-")) {
                      this.fillForm(id.replace("lead-",""));
                      this.switchTab("form");
                    } else {
                      this.switchView("cards");
                    }
                  }
                });

                this._calendar.render();
              },

              refreshCalendar() {
                if (!this._calendar) return;
                try { this._calendar.updateSize(); } catch(_) {}
              },

              renderCalendarEvents() {
                if (!this._calendar) return;

                const events = (this._rendered || []).map(ev => ({
                  id: ev.id,
                  title: ev.title,
                  start: ev.start,
                  end: ev.end,
                  allDay: !!ev.allDay,
                  backgroundColor: ev.color,
                  borderColor: ev.color,
                  extendedProps: {
                    creator_label: ev.creator_label || "",
                    employees: ev.employees || [],
                    raw: ev._raw
                  }
                }));

                this._calendar.removeAllEvents();
                this._calendar.addEventSource(events);

                // if employee mode, jump to first event date
                if (this._mode === "employee" && this._employeeEvents?.length) {
                  const first = dateOnly(this._employeeEvents[0]?.start_date);
                  if (first) this._calendar.gotoDate(first);
                }
              },

              /* =========================
               * LIST VIEW + JUMP + COUNT
               * =======================*/
              updateCount() {
                const el = qs("#ap-count");
                if (el) el.textContent = String((this._rendered || []).length);
              },

              populateJumpDropdown() {
                const sel = qs("#ap-jump");
                if (!sel) return;

                const list = (this._rendered || []).slice().sort((a,b) => String(b.start).localeCompare(String(a.start)));
                sel.innerHTML =
                  `<option value="">— Termin auswählen (Springen) —</option>` +
                  list.map(ev => {
                    const d = String(ev.start || "").slice(0,10);
                    const dateLabel = d ? d.split("-").reverse().join(".") : "";
                    const timeLabel = ev.allDay ? " Ganztägig" : (" " + String(ev.start).slice(11,16));
                    return `<option value="${esc(ev.id)}">${esc(dateLabel + timeLabel)} — ${esc(ev.title)}</option>`;
                  }).join("");
              },

              jumpToEvent(eventId) {
                const ev = (this._rendered || []).find(x => String(x.id) === String(eventId));
                if (!ev || !this._calendar) return;

                const d = String(ev.start || "").slice(0,10);
                if (!d) return;

                this.switchView("calendar");
                this.switchTab("calendar");

                setTimeout(() => {
                  this._calendar.gotoDate(d);
                  if (String(ev.id).startsWith("lead-")) {
                    this.fillForm(String(ev.id).replace("lead-",""));
                    this.switchTab("form");
                  }
                }, 160);
              },

              renderCardList() {
                const wrap = qs("#ap-card-view");
                if (!wrap) return;

                const list = (this._rendered || []);
                if (!list.length) {
                  wrap.innerHTML = '<div class="text-center text-muted col-12 small my-3">Keine Einträge gefunden.</div>';
                  return;
                }

                wrap.innerHTML = list.map(ev => {
                  const d = String(ev.start || "").slice(0,10);
                  const date = d ? d.split("-").reverse().join(".") : "";

                  const time = ev.allDay
                    ? "Ganztägig"
                    : `${String(ev.start).slice(11,16)} – ${String(ev.end || "").slice(11,16)}`;

                  const creator = ev.creator_label ? `
                    <div class="small text-muted" style="margin-top:2px;">
                      <i class="feather icon-user" style="font-size:12px;"></i>
                      <span class="ml-1">${esc(ev.creator_label)}</span>
                    </div>` : "";

                  const empPills = (ev.employees || []).map(e => {
                    const full = e.full || "Mitarbeiter";
                    const img  = e.image || "";
                    const fb   = esc((e.initials || "?").slice(0,2));
                    return `
                      <span class="ap-emp-pill" title="${esc(full)}">
                        <span class="av">
                          ${img ? `<img src="${esc(img)}" alt="${esc(full)}">` : `<span class="fb">${fb}</span>`}
                        </span>
                        <span class="nm">${esc(full)}</span>
                      </span>`;
                  }).join("");

                  return `
                    <article class="ap-card" style="cursor:pointer" onclick="AppointmentsUI.jumpToEvent('${esc(ev.id)}')">
                      <div class="ap-color" style="background:${esc(ev.color || "#74b2d4")};"></div>
                      <div class="ap-main">
                        <div class="d-flex justify-content-between">
                          <div class="ap-title font-weight-bold">${esc(ev.title)}</div>
                          <div class="text-muted" style="font-size:10px;"><i class="feather icon-calendar"></i> ${esc(date)}</div>
                        </div>
                        <div class="text-muted small mb-1">${esc(time)}</div>
                        ${creator}
                        <div class="ap-note small text-muted mb-2" style="line-height:1.2;">${esc(ev.description || "").slice(0,110)}</div>
                        <div class="ap-emp-avatars">${empPills}</div>
                      </div>
                    </article>
                  `;
                }).join("");
              },

              /* =========================
               * FORM (create/edit lead appointments)
               * =======================*/
              preselectEmployeeIntoForm() {
                const empId = String(this._selectedEmpId || "");
                if (!empId) return;

                // ensure select2 exists
                this.initSelect2ForFormEmployees();

                if (!window.jQuery || !qs("#ap-employee_ids")) return;

                const $sel = window.jQuery("#ap-employee_ids");
                const existing = $sel.val() || [];
                if (!existing.includes(empId)) {
                  $sel.val([...existing, empId]).trigger("change");
                }
              },

              resetForm() {
                const form = qs("#ap-form");
                if (!form) return;
                form.reset();

                qs("#ap-form-title") && (qs("#ap-form-title").textContent = "Neuer Termin");

                const delBtn = qs("#ap-btn-delete");
                if (delBtn) { delBtn.classList.add("d-none"); delBtn.onclick = null; }

                qs("#ap-customer_id") && (qs("#ap-customer_id").value = this._ctx.customerId);
                qs("#ap-alternative_id") && (qs("#ap-alternative_id").value = this._ctx.alternativeId);
                qs("#ap-product_id") && (qs("#ap-product_id").value = this._ctx.productId);
                qs("#ap-id") && (qs("#ap-id").value = "");

                qs("#ap-color") && (qs("#ap-color").value = "#74b2d4");

                if (window.jQuery) window.jQuery("#ap-employee_ids").val(null).trigger("change");
              },

              fillForm(id) {
                const appt = (this._customerAppointments || []).find(x => String(x.id) === String(id));
                if (!appt) return;

                this.resetForm();
                qs("#ap-form-title") && (qs("#ap-form-title").textContent = "Termin bearbeiten");

                const delBtn = qs("#ap-btn-delete");
                if (delBtn) {
                  delBtn.classList.remove("d-none");
                  delBtn.onclick = () => this.delete(id);
                }

                const set = (sel, val) => { const el = qs(sel); if (el) el.value = val ?? ""; };

                set("#ap-id", appt.id);
                set("#ap-name", appt.name);
                set("#ap-note", appt.note);

                set("#ap-start_date", dateOnly(appt.start_date));
                set("#ap-start_time", timeOnly(appt.start_time).slice(0,5));
                set("#ap-end_time", timeOnly(appt.end_time).slice(0,5));

                set("#ap-appointment_type", appt.appointment_type);
                set("#ap-contact_mode", appt.contact_mode);
                set("#ap-priority", appt.priority || "normal");
                set("#ap-color", appt.color || "#74b2d4");

                set("#ap-full_address", appt.full_address);
                set("#ap-street", appt.street);
                set("#ap-postcode", appt.postcode);
                set("#ap-city", appt.city);

                this.initSelect2ForFormEmployees();
                if (window.jQuery && Array.isArray(appt.employees)) {
                  const ids = appt.employees.map(e => String(e.id));
                  window.jQuery("#ap-employee_ids").val(ids).trigger("change");
                }
              },

              prefillFromContact(contact) {
                if (!contact) return;
                const map = {
                  full_address:"#ap-full_address",
                  street:"#ap-street",
                  postcode:"#ap-postcode",
                  city:"#ap-city",
                  latitude:"#ap-latitude",
                  longitude:"#ap-longitude"
                };
                for (const [k, sel] of Object.entries(map)) {
                  if (contact[k]) { const el = qs(sel); if (el) el.value = contact[k]; }
                }
              },

              async submitForm(ev) {
                ev.preventDefault();

                const name = qs("#ap-name")?.value.trim();
                const startDate = qs("#ap-start_date")?.value;
                const customerId = qs("#ap-customer_id")?.value;

                if (!name || !startDate || !customerId) {
                  window.Swal?.fire("Fehler", "Titel und Datum sind Pflichtfelder.", "error");
                  return;
                }

                const payload = {
                  customer_id: customerId,
                  alternative_id: qs("#ap-alternative_id")?.value || null,
                  product_id: qs("#ap-product_id")?.value || null,
                  name,
                  note: qs("#ap-note")?.value || null,
                  start_date: startDate,
                  start_time: qs("#ap-start_time")?.value || null,
                  end_time: qs("#ap-end_time")?.value || null,
                  appointment_type: qs("#ap-appointment_type")?.value || null,
                  contact_mode: qs("#ap-contact_mode")?.value || null,
                  priority: qs("#ap-priority")?.value || "normal",
                  color: qs("#ap-color")?.value || "#74b2d4",
                  full_address: qs("#ap-full_address")?.value || null,
                  street: qs("#ap-street")?.value || null,
                  postcode: qs("#ap-postcode")?.value || null,
                  city: qs("#ap-city")?.value || null,
                  latitude: qs("#ap-latitude")?.value || null,
                  longitude: qs("#ap-longitude")?.value || null,
                  employee_ids: window.jQuery ? (window.jQuery("#ap-employee_ids").val() || []) : []
                };

                const id = qs("#ap-id")?.value || "";
                const isEdit = !!id;
                const url = isEdit ? APP.endpoints.appointmentsUpdate(id) : APP.endpoints.appointmentsStore;
                const method = isEdit ? "PUT" : "POST";

                try {
                  const res = await net.safeFetchJSON(url, {
                    method,
                    headers: {
                      "Content-Type": "application/json",
                      "X-CSRF-TOKEN": (utils.CSRF ? utils.CSRF() : "")
                    },
                    body: JSON.stringify(payload)
                  });

                  if (res?.success !== false) {
                    window.Swal?.fire("Gespeichert", isEdit ? "Termin aktualisiert." : "Termin angelegt.", "success");

                    // after save: reload CUSTOMER calendar (since appointment belongs to customer context)
                    await this.loadCustomerAppointments();

                    // keep employee filter selection as is, but rendering should match mode:
                    // - if employee selected => show employee calendar (not customer)
                    // - if empty => show customer calendar
                    await this.onEmpFilterChanged(false);

                    this.switchTab("calendar");
                  } else {
                    throw new Error(res?.message || "Fehler beim Speichern.");
                  }
                } catch (err) {
                  window.Swal?.fire("Fehler", err.message || "Fehler beim Speichern.", "error");
                }
              },

              async delete(id) {
                const ok = window.Swal
                  ? await Swal.fire({ title:"Löschen?", text:"Wirklich löschen?", icon:"warning", showCancelButton:true })
                  : { isConfirmed: confirm("Wirklich löschen?") };

                if (!ok.isConfirmed) return;

                try {
                  await net.safeFetchJSON(APP.endpoints.appointmentsDestroy(id), {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": (utils.CSRF ? utils.CSRF() : "") }
                  });

                  window.Swal?.fire("Gelöscht", "Termin entfernt.", "success");

                  await this.loadCustomerAppointments();
                  await this.onEmpFilterChanged(false);
                  this.switchTab("calendar");
                } catch (err) {
                  window.Swal?.fire("Fehler", err.message || "Fehler beim Löschen.", "error");
                }
              }
            };

            // delegated events (works with ajax-injected drawer too)
            if (!window.__AP_UNIFIED_BOUND) {
              window.__AP_UNIFIED_BOUND = true;

              document.addEventListener("click", (e) => {
                const t = e.target;
                if (t?.id === "ap-backdrop" || t?.closest?.("[data-ap-close]")) UI.hide();
              });

              document.addEventListener("click", (e) => {
                const btn = e.target?.closest?.(".ap-tab-link");
                if (btn) UI.switchTab(btn.dataset.tab);
              });

              document.addEventListener("click", (e) => {
                const btn = e.target?.closest?.("[data-view]");
                if (btn) UI.switchView(btn.dataset.view);
              });

              document.addEventListener("change", (e) => {
                if (e.target?.id === "ap-jump") {
                  const v = e.target.value;
                  if (v) UI.jumpToEvent(v);
                  e.target.value = "";
                }
                if (e.target?.id === "ap-emp-filter") UI.onEmpFilterChanged(false);
              });

              document.addEventListener("submit", (e) => {
                if (e.target?.id === "ap-form") UI.submitForm(e);
              });

              document.addEventListener("click", (e) => {
                if (e.target?.closest?.("#ap-btn-back-to-cal")) UI.switchTab("calendar");
              });

              document.addEventListener("transitionend", (e) => {
                const drawer = qs("#ap-drawer");
                if (drawer && e.target === drawer && drawer.classList.contains("open")) {
                  UI.initCalendar();
                  UI.refreshCalendar();
                }
              });
            }

            document.addEventListener("open-appointments", (e) => {
              const d = e.detail || {};
              UI.open(d.customerId, d.alternativeId, d.productId, d.title, d);
            });

            window.AppointmentsUI = UI;
          })();
          </script>

           <!-- Termin Script -->
           <script>
          (function () {
            "use strict";

            const BRANCH_COLOR_MAP = @json(
  collect($branches ?? [])->mapWithKeys(function ($b) {
    $name = mb_strtolower(trim((string) ($b->branch ?? '')));
    $color = (string) ($b->color ?? '#93c21c');
    return [$name => $color];
  })->all()
);

            const DEFAULT_COLOR = "#93c21c";
            const norm = (v) => (v ?? "").toString().trim().toLowerCase();

            function setImportant(el, prop, value) {
              if (!el) return;
              el.style.setProperty(prop, value, "important");
            }

            function pickBranchName(branchEl) {
              if (!branchEl) return "";
              const t = norm(branchEl.getAttribute("title"));
              if (t) return t;

              const nameEl = branchEl.querySelector(".kb-branch-name");
              const txt = norm(nameEl ? nameEl.textContent : branchEl.textContent);
              return txt;
            }

            function resolveColor(branchName) {
              const key = norm(branchName);
              return BRANCH_COLOR_MAP[key] || DEFAULT_COLOR;
            }

            function findCard(el) {
              // Your circle lives inside `.card`, so include that.
              return (
                el.closest(".kb-card") ||
                el.closest(".kanban-card") ||
                el.closest(".kb-item") ||
                el.closest(".card") ||
                el.closest("[data-lead-id]") ||
                el.closest("[data-id]") ||
                el.parentElement
              );
            }

            function paintCardCircle(card, color) {
              if (!card) return;

              // IMPORTANT: target product_circle specifically
              const circle =
                card.querySelector(".circle.product_circle") ||
                card.querySelector(".product_circle") ||
                card.querySelector(".circle");

              if (!circle) return;

              circle.style.setProperty("--branch-color", color);
              setImportant(circle, "background-color", color);
              setImportant(circle, "color", "#fff");
            }

            function paintBranch(branchEl) {
              const card = findCard(branchEl);
              const branchName = pickBranchName(branchEl);
              const color = resolveColor(branchName);

              // color branch label + svg
              branchEl.style.setProperty("--branch-color", color);
              setImportant(branchEl, "color", color);

              // color product circle in the same card
              paintCardCircle(card, color);
            }

            function paintCircle(circleEl) {
              // only force product circle (avoid random circles elsewhere)
              if (!circleEl.classList.contains("product_circle")) return;

              const card = findCard(circleEl);
              if (!card) return;

              const branchEl = card.querySelector(".kb-meta-item.kb-branch");
              const branchName = pickBranchName(branchEl);
              if (!branchName) return;

              const color = resolveColor(branchName);
              paintCardCircle(card, color);
            }

            function paintAll(root = document) {
              root.querySelectorAll(".kb-meta-item.kb-branch").forEach(paintBranch);
              root.querySelectorAll(".circle.product_circle, .product_circle").forEach(paintCircle);
            }

            document.addEventListener("DOMContentLoaded", () => paintAll());

            const container =
              document.querySelector("#kanban") ||
              document.querySelector(".kanban-board") ||
              document.body;

            const obs = new MutationObserver((mutations) => {
              for (const m of mutations) {
                if (!m.addedNodes) continue;
                m.addedNodes.forEach((node) => {
                  if (node && node.nodeType === 1) paintAll(node);
                });
              }
            });

            obs.observe(container, { childList: true, subtree: true });

            // optional manual trigger after your own render
            window.paintBranchColors = paintAll;
          })();


          document.addEventListener("click", function(e) {
              // Target pagination links specifically inside the Junk pane
              const paginationLink = e.target.closest("#junk .pagination a");

              if (paginationLink) {
                  e.preventDefault();

                  const url = paginationLink.getAttribute("href");
                  const junkPane = document.querySelector("#junk");

                  // Show a loading state
                  junkPane.style.opacity = '0.5';

                  fetch(url, {
                      headers: {
                          'X-Requested-With': 'XMLHttpRequest'
                      }
                  })
                  .then(response => response.text())
                  .then(html => {
                      // Update the content
                      junkPane.innerHTML = html;
                      junkPane.style.opacity = '1';

                      // Re-initialize any specific UI elements if needed
                      // e.g., if you have Tooltips or specific button styles
                      if (window.feather) {
                          feather.replace();
                      }

                      // Optional: Smooth scroll back to the top of the table
                      junkPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  })
                  .catch(error => {
                      console.error('Error loading junk pagination:', error);
                      junkPane.style.opacity = '1';
                  });
              }
          });

          </script>

          <script>
          document.addEventListener("click", function(e) {
              const toggleBtn = e.target.closest(".toggle-feed-btn");
              if (toggleBtn) {
                  e.preventDefault();
                  // Find the closest row and toggle the feed visibility class
                  const row = toggleBtn.closest("tr.list-row-item");
                  if (row) {
                      const feed = row.querySelector(".list-live-feed");
                      if (feed) {
                          feed.classList.toggle("force-show-feed");
                      }
                  }
              }
          });
          </script>

          <script>
          document.addEventListener("click", function(e) {
              // Look for a click inside the status block specifically inside the table
              const kbStatus = e.target.closest(".table .kb-status");

              if (kbStatus) {
                  e.preventDefault();
                  e.stopPropagation();

                  // Simply toggle the expanded state class
                  kbStatus.classList.toggle("is-expanded");
              }
          });
          </script>



          <script>
            /* =========================================================
             * Dynamic Lead Stage Manager - restored + icon Select2 + drag/drop
             * ========================================================= */
            (function () {
              "use strict";

              const LeadUI = window.LeadUI || {};
              const APP_ROOT = LeadUI.APP || {};
              const APP = APP_ROOT.endpoints || APP_ROOT || {};

              const STAGE_ADMIN_BASE = "{{ url('/task-phase/ajax/stage-admin') }}";
              const PHASE_API = {
                index: `${STAGE_ADMIN_BASE}/stages`,
                store: `${STAGE_ADMIN_BASE}/stages`,
                update: (id) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(id)}/update`,
                destroy: (id) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(id)}`,
                reorder: `${STAGE_ADMIN_BASE}/stages/reorder`,
                show: (id) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(id)}`,
              };
              APP.leadStagesIndex   = PHASE_API.index;
              APP.leadStagesStore   = PHASE_API.store;
              APP.leadStagesUpdate  = PHASE_API.update;
              APP.leadStagesDestroy = PHASE_API.destroy;
              APP.leadStagesReorder = PHASE_API.reorder;

              const qs = (s, ctx = document) => ctx.querySelector(s);
              const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
              const csrf = () => qs('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}";
              const esc = (v) => String(v ?? "").replace(/[&<>"']/g, (m) => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[m]));

              const modal = qs("#leadStageManagerModal");
              const panel = modal?.querySelector(".lsm-panel");
              const list = qs("#leadStagesList");
              const form = qs("#leadStageForm");
              const formTitle = qs("#lsmFormTitle");
              const idInput = qs("#lsmStageId");
              const nameInput = qs("#lsmStageName");
              const colorInput = qs("#lsmStageColor");
              const colorText = qs("#lsmStageColorText");
              const iconInput = qs("#lsmStageIcon");
              const activeInput = qs("#lsmStageActive");
              const closedInput = qs("#lsmStageClosed");

              const ICONS = [
                "circle", "user-plus", "users", "file-text", "phone-call", "check-circle", "briefcase", "tool", "flag", "archive", "trash-2", "clock", "calendar", "activity", "target", "send", "mail", "message-square", "clipboard", "list", "layers", "box", "truck", "home", "map-pin", "star", "award", "alert-triangle", "zap", "settings", "edit-2"
              ];

              let stages = [];
              let dragId = null;

              let selectedSubstageStageId = null;
              let substageDragId = null;

              const subStageApi = {
                create: (stageId) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(stageId)}/sub-stages`,
                update: (subId) => `${STAGE_ADMIN_BASE}/sub-stages/${encodeURIComponent(subId)}/update`,
                destroy: (subId) => `${STAGE_ADMIN_BASE}/sub-stages/${encodeURIComponent(subId)}`,
                reorder: (stageId) => `${STAGE_ADMIN_BASE}/stages/${encodeURIComponent(stageId)}/sub-stages/reorder`,
              };

              const subDrawer = qs("#lsmSubstageDrawer");
              const subTitle = qs("#lsmSubstageTitle");
              const subSubtitle = qs("#lsmSubstageSubtitle");
              const subStageIdInput = qs("#lsmSubstageStageId");
              const subNameInput = qs("#lsmSubstageName");
              const subKeyInput = qs("#lsmSubstageKey");
              const subColorInput = qs("#lsmSubstageColor");
              const subIconInput = qs("#lsmSubstageIcon");
              const subActiveInput = qs("#lsmSubstageActive");
              const subList = qs("#lsmSubstageList");

              function normalizeSubStages(stage) {
                if (!stage) return [];
                const raw = stage.sub_stages || stage.subStages || stage.active_sub_stages || stage.activeSubStages || [];
                return Array.isArray(raw) ? raw : [];
              }

              function normalizeStage(stage) {
                const copy = { ...(stage || {}) };
                copy.sub_stages = normalizeSubStages(copy);
                copy.sub_stage_count = copy.sub_stages.length || Number(copy.sub_stage_count || copy.sub_stages_count || copy.subStageCount || 0);
                return copy;
              }

              function setKanbanPhaseName(name, hint = "Aktuelle Ansicht") {
                // The global centered phase banner was removed by design.
                // Column headers now show each phase name directly at the top of every column.
              }

              function refreshIcons() {
                if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
              }

              function formatIconOption(state) {
                if (!state.id) return state.text;
                const span = document.createElement("span");
                span.className = "lsm-icon-option";
                span.innerHTML = `<i data-feather="${esc(state.id)}"></i><span>${esc(state.text)}</span>`;
                setTimeout(refreshIcons, 0);
                return span;
              }

              function initIconSelect() {
                if (!iconInput || iconInput.dataset.ready === "1") return;
                iconInput.innerHTML = ICONS.map((i) => `<option value="${esc(i)}">${esc(i)}</option>`).join("");
                iconInput.dataset.ready = "1";
                if (window.jQuery && window.jQuery.fn.select2) {
                  window.jQuery(iconInput).select2({
                    dropdownParent: window.jQuery("#leadStageManagerModal"),
                    width: "100%",
                    templateResult: formatIconOption,
                    templateSelection: formatIconOption,
                    minimumResultsForSearch: 0,
                  });
                }
                iconInput.value = "circle";
                if (window.jQuery) window.jQuery(iconInput).trigger("change.select2");
              }

              function setIconValue(value) {
                initIconSelect();
                iconInput.value = value || "circle";
                if (window.jQuery) window.jQuery(iconInput).val(iconInput.value).trigger("change");
                refreshIcons();
              }

              function updateColorText() {
                if (colorText && colorInput) colorText.textContent = colorInput.value || "#74b2d4";
              }

              function openModal() {
                if (!modal) return;
                modal.classList.add("is-open");
                modal.setAttribute("aria-hidden", "false");
                document.body.style.overflow = "hidden";
                initIconSelect();
                setTimeout(() => panel?.focus?.({ preventScroll: true }), 30);
                loadStages();
                refreshIcons();
              }

              function closeModal() {
                if (!modal) return;
                modal.classList.remove("is-open");
                modal.setAttribute("aria-hidden", "true");
                document.body.style.overflow = "";
                closeSubstageDrawer();
              }

              async function requestJSON(url, options = {}) {
                const res = await fetch(url, {
                  credentials: "same-origin",
                  headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrf(),
                    ...(options.headers || {}),
                  },
                  ...options,
                });
                const text = await res.text();
                let data = {};
                try { data = text ? JSON.parse(text) : {}; } catch { data = { message: text || "Ungültige Serverantwort." }; }
                if (!res.ok || data.success === false) {
                  const err = new Error(data.message || `HTTP ${res.status}`);
                  err.payload = data;
                  throw err;
                }
                return data;
              }

              function resetForm() {
                if (!form) return;
                idInput.value = "";
                formTitle.textContent = "Neue Phase";
                nameInput.value = "";
                colorInput.value = "#74b2d4";
                updateColorText();
                setIconValue("circle");
                activeInput.checked = true;
                closedInput.checked = false;
                nameInput.focus();
              }

              function fillForm(stage) {
                idInput.value = stage.id || "";
                formTitle.textContent = "Phase bearbeiten";
                nameInput.value = stage.name || "";
                colorInput.value = stage.color || "#74b2d4";
                updateColorText();
                setIconValue(stage.icon || "circle");
                activeInput.checked = !!stage.is_active;
                closedInput.checked = !!stage.is_closed;
                nameInput.focus();
              }

              function rowsToItems() {
                return qsa("#leadStagesList [data-stage-id]").map((row, index) => ({
                  id: Number(row.dataset.stageId),
                  sort_order: (index + 1) * 10,
                })).filter((x) => x.id > 0);
              }

              function renderStages() {
                if (!list) return;
                if (!stages.length) {
                  list.innerHTML = `<div class="lsm-empty">Keine Phasen gefunden.</div>`;
                  return;
                }
                list.innerHTML = stages.map((stage) => {
                  const usage = Number(stage.usage_count || 0);
                  const isProtected = !!stage.is_protected;
                  const color = stage.color || "#74b2d4";
                  const icon = stage.icon || "circle";
                  const subCount = Number(stage.sub_stage_count || normalizeSubStages(stage).length || 0);
                  return `
                    <div class="lsm-stage-row" data-stage-id="${esc(stage.id)}" draggable="true">
                      <div><span class="lsm-drag-handle" title="Ziehen zum Sortieren"><i class="feather icon-move"></i></span></div>
                      <div class="lsm-stage-name">
                        <span class="lsm-color-dot" style="background:${esc(color)}"></span>
                        <div>
                          <strong><i class="feather icon-${esc(icon)} mr-50"></i>${esc(stage.name)}</strong>
                          <small>${stage.is_default ? "Standard" : "Eigene Phase"}${stage.is_closed ? " • geschlossen" : ""}</small>
                          <span class="lsm-stage-subcount-line"><i class="feather icon-git-branch"></i>${subCount} Unterphasen</span>
                        </div>
                      </div>
                      <div><code>${esc(stage.key)}</code></div>
                      <div><span class="lsm-badge ${usage > 0 ? "lsm-badge--warn" : "lsm-badge--ok"}">${usage} Einträge</span></div>
                      <div class="lsm-actions">
                        ${stage.is_active ? `<span class="lsm-badge lsm-badge--blue">Aktiv</span>` : `<span class="lsm-badge">Inaktiv</span>`}
                        <button type="button" class="lsm-substage-btn" data-lsm-substages="${esc(stage.id)}" title="Unterphasen für diese Hauptphase konfigurieren">
                          <i class="feather icon-list"></i><span>Unterphasen</span><span class="lsm-substage-count-pill">${subCount}</span>
                        </button>
                        <button type="button" class="lsm-icon-btn" data-lsm-edit="${esc(stage.id)}" title="Bearbeiten"><i class="feather icon-edit-2"></i></button>
                        <button type="button" class="lsm-icon-btn danger" data-lsm-delete="${esc(stage.id)}" data-usage="${usage}" data-protected="${isProtected ? 1 : 0}" title="Löschen"><i class="feather icon-trash-2"></i></button>
                      </div>
                    </div>`;
                }).join("");
                bindDragRows();
                refreshIcons();
              }


              function selectedStageForSubstages() {
                return stages.find((stage) => String(stage.id) === String(selectedSubstageStageId)) || null;
              }

              function resetSubstageForm(stage = null) {
                if (subNameInput) subNameInput.value = "";
                if (subKeyInput) subKeyInput.value = "";
                if (subColorInput) subColorInput.value = stage?.color || "#93c21c";
                if (subIconInput) subIconInput.value = "list";
                if (subActiveInput) subActiveInput.checked = true;
              }

              async function ensureStageSubStages(stageId) {
                let stage = selectedStageForSubstages();
                if (!stage) return null;
                if (Array.isArray(stage.sub_stages)) return stage;
                try {
                  const data = await requestJSON(PHASE_API.show(stageId), { method: "GET" });
                  const full = normalizeStage(data.stage || data.data || data);
                  stages = stages.map((item) => String(item.id) === String(stageId) ? { ...item, ...full } : item);
                  return selectedStageForSubstages();
                } catch (err) {
                  // Keep the drawer usable even if the detail route is not available.
                  stage.sub_stages = normalizeSubStages(stage);
                  return stage;
                }
              }

              async function openSubstageDrawer(stageId) {
                selectedSubstageStageId = stageId;
                let stage = await ensureStageSubStages(stageId);
                if (!stage) {
                  Swal.fire("Hinweis", "Diese Hauptphase konnte nicht gefunden werden. Bitte lade die Phasen neu.", "info");
                  return;
                }
                if (subStageIdInput) subStageIdInput.value = stage.id;
                if (subTitle) subTitle.textContent = `Unterphasen · ${stage.name || 'Hauptphase'}`;
                if (subSubtitle) {
                  const subCount = Number(stage.sub_stage_count || normalizeSubStages(stage).length || 0);
                  subSubtitle.innerHTML = `Hier verwaltest du <strong>${subCount}</strong> Unterphasen von <strong>${esc(stage.name || stage.key || stage.id)}</strong>. Die Hauptphasenliste bleibt geöffnet.`;
                }
                setKanbanPhaseName(stage.name || stage.key || "Hauptphase", "Ausgewählte Hauptphase");
                resetSubstageForm(stage);
                renderSubstages();
                subDrawer?.classList.add("is-open");
                subDrawer?.setAttribute("aria-hidden", "false");
                setTimeout(() => subNameInput?.focus?.(), 80);
                refreshIcons();
              }

              function closeSubstageDrawer() {
                subDrawer?.classList.remove("is-open");
                subDrawer?.setAttribute("aria-hidden", "true");
                selectedSubstageStageId = null;
                setKanbanPhaseName("Hauptphasen", "Aktuelle Ansicht");
              }

              function substageRowsToItems() {
                return qsa("#lsmSubstageList [data-sub-id]")
                  .map((row) => Number(row.dataset.subId))
                  .filter((id) => id > 0);
              }

              function renderSubstages() {
                if (!subList) return;
                const stage = selectedStageForSubstages();
                if (!stage) {
                  subList.innerHTML = `<div class="lsm-substage-empty">Bitte zuerst links eine Hauptphase auswählen.</div>`;
                  return;
                }
                const subs = normalizeSubStages(stage);
                if (!subs.length) {
                  subList.innerHTML = `<div class="lsm-substage-empty">Für <strong>${esc(stage.name)}</strong> gibt es noch keine Unterphasen. Erstelle oben die erste Unterphase.</div>`;
                  return;
                }
                subList.innerHTML = subs.map((sub) => `
                  <div class="lsm-substage-row" data-sub-id="${esc(sub.id)}" draggable="true">
                    <span class="lsm-substage-handle" title="Ziehen zum Sortieren"><i class="feather icon-menu"></i></span>
                    <input class="form-control js-lsm-sub-name" value="${esc(sub.name)}" placeholder="Name">
                    <input class="form-control js-lsm-sub-key" value="${esc(sub.key)}" placeholder="Key">
                    <input class="form-control js-lsm-sub-color" type="color" value="${esc(sub.color || stage.color || '#93c21c')}">
                    <input class="form-control js-lsm-sub-icon" value="${esc(sub.icon || 'list')}" placeholder="Icon">
                    <div class="lsm-substage-actions">
                      <label class="mb-0 small text-muted" title="Aktiv"><input type="checkbox" class="js-lsm-sub-active" ${sub.is_active ? 'checked' : ''}> Aktiv</label>
                      <button type="button" class="lsm-mini-btn" data-lsm-sub-save="${esc(sub.id)}" title="Speichern"><i class="feather icon-save"></i></button>
                      <button type="button" class="lsm-mini-btn danger" data-lsm-sub-delete="${esc(sub.id)}" data-usage="${esc(sub.usage_count || 0)}" title="Löschen"><i class="feather icon-trash-2"></i></button>
                    </div>
                  </div>
                `).join("");
                bindSubstageDragRows();
                refreshIcons();
              }

              function bindSubstageDragRows() {
                qsa("#lsmSubstageList [data-sub-id]").forEach((row) => {
                  row.addEventListener("dragstart", (e) => {
                    substageDragId = row.dataset.subId;
                    row.classList.add("is-dragging");
                    e.dataTransfer.effectAllowed = "move";
                    e.dataTransfer.setData("text/plain", substageDragId);
                  });
                  row.addEventListener("dragend", () => {
                    row.classList.remove("is-dragging");
                    qsa("#lsmSubstageList .is-drop-target").forEach((el) => el.classList.remove("is-drop-target"));
                    substageDragId = null;
                  });
                  row.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    const dragging = qs("#lsmSubstageList .is-dragging");
                    if (!dragging || dragging === row) return;
                    row.classList.add("is-drop-target");
                    const rect = row.getBoundingClientRect();
                    const after = e.clientY > rect.top + rect.height / 2;
                    subList.insertBefore(dragging, after ? row.nextSibling : row);
                  });
                  row.addEventListener("dragleave", () => row.classList.remove("is-drop-target"));
                });
              }

              async function reloadStagesAndKeepSubstageDrawer() {
                await loadStages();
                if (selectedSubstageStageId) {
                  const stage = selectedStageForSubstages();
                  if (stage) renderSubstages();
                  else closeSubstageDrawer();
                }
              }

              async function createSubstage() {
                const stageId = subStageIdInput?.value || selectedSubstageStageId;
                if (!stageId) return;
                const payload = {
                  lead_stage_id: Number(stageId),
                  name: (subNameInput?.value || "").trim(),
                  key: (subKeyInput?.value || "").trim(),
                  color: subColorInput?.value || "#93c21c",
                  icon: (subIconInput?.value || "list").trim() || "list",
                  is_active: subActiveInput?.checked ? 1 : 0,
                };
                if (!payload.name) { Swal.fire("Hinweis", "Bitte gib einen Namen für die Unterphase ein.", "info"); return; }
                try {
                  await requestJSON(subStageApi.create(stageId), { method: "POST", body: JSON.stringify(payload) });
                  resetSubstageForm(selectedStageForSubstages());
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphase gespeichert", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Unterphase konnte nicht erstellt werden.", "error"); }
              }

              async function saveSubstage(subId) {
                const row = qs(`#lsmSubstageList [data-sub-id="${CSS.escape(String(subId))}"]`);
                const stageId = subStageIdInput?.value || selectedSubstageStageId;
                if (!row || !stageId) return;
                const payload = {
                  lead_stage_id: Number(stageId),
                  name: row.querySelector(".js-lsm-sub-name")?.value?.trim() || "",
                  key: row.querySelector(".js-lsm-sub-key")?.value?.trim() || "",
                  color: row.querySelector(".js-lsm-sub-color")?.value || "#93c21c",
                  icon: row.querySelector(".js-lsm-sub-icon")?.value?.trim() || "list",
                  is_active: row.querySelector(".js-lsm-sub-active")?.checked ? 1 : 0,
                };
                if (!payload.name) { Swal.fire("Hinweis", "Bitte gib einen Namen ein.", "info"); return; }
                try {
                  await requestJSON(subStageApi.update(subId), { method: "POST", body: JSON.stringify(payload) });
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphase aktualisiert", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Unterphase konnte nicht gespeichert werden.", "error"); }
              }

              async function deleteSubstage(subId, usage) {
                usage = Number(usage || 0);
                if (usage > 0) {
                  Swal.fire("Nicht möglich", `Diese Unterphase enthält noch ${usage} Einträge. Bitte verschiebe diese Einträge zuerst.`, "warning");
                  return;
                }
                const ask = await Swal.fire({ icon:"warning", title:"Unterphase löschen?", text:"Diese Aktion kann nicht rückgängig gemacht werden.", showCancelButton:true, confirmButtonText:"Ja, löschen", cancelButtonText:"Abbrechen", confirmButtonColor:"#ef4444" });
                if (!ask.isConfirmed) return;
                try {
                  await requestJSON(subStageApi.destroy(subId), { method: "DELETE" });
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphase gelöscht", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Unterphase konnte nicht gelöscht werden.", "error"); }
              }

              async function saveSubstageOrder() {
                const stageId = subStageIdInput?.value || selectedSubstageStageId;
                if (!stageId) return;
                const items = substageRowsToItems();
                if (!items.length) return;
                try {
                  await requestJSON(subStageApi.reorder(stageId), { method: "POST", body: JSON.stringify({ items }) });
                  await reloadStagesAndKeepSubstageDrawer();
                  Swal.fire({ icon:"success", title:"Unterphasen sortiert", timer:850, showConfirmButton:false });
                } catch (err) { Swal.fire("Fehler", err.message || "Sortierung konnte nicht gespeichert werden.", "error"); }
              }

              function bindDragRows() {
                qsa("#leadStagesList [data-stage-id]").forEach((row) => {
                  row.addEventListener("dragstart", (e) => {
                    dragId = row.dataset.stageId;
                    row.classList.add("is-dragging");
                    e.dataTransfer.effectAllowed = "move";
                    e.dataTransfer.setData("text/plain", dragId);
                  });
                  row.addEventListener("dragend", () => {
                    row.classList.remove("is-dragging");
                    qsa("#leadStagesList .is-drop-target").forEach((el) => el.classList.remove("is-drop-target"));
                    dragId = null;
                  });
                  row.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    const dragging = qs("#leadStagesList .is-dragging");
                    if (!dragging || dragging === row) return;
                    row.classList.add("is-drop-target");
                    const rect = row.getBoundingClientRect();
                    const after = e.clientY > rect.top + rect.height / 2;
                    list.insertBefore(dragging, after ? row.nextSibling : row);
                  });
                  row.addEventListener("dragleave", () => row.classList.remove("is-drop-target"));
                });
              }

              async function loadStages() {
                if (list) list.innerHTML = `<div class="lsm-empty">Phasen werden geladen…</div>`;
                try {
                  const data = await requestJSON(PHASE_API.index, { method: "GET" });
                  const rawStages = Array.isArray(data.stages) ? data.stages : (Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []));
                  stages = rawStages.map(normalizeStage);
                  renderStages();
                  if (selectedSubstageStageId) {
                    const stage = selectedStageForSubstages();
                    if (stage) renderSubstages();
                    else closeSubstageDrawer();
                  }
                } catch (err) {
                  if (list) list.innerHTML = `<div class="lsm-empty text-danger">${esc(err.message || "Phasen konnten nicht geladen werden.")}<br><small>Prüfe die Route: /task-phase/ajax/stage-admin/stages</small></div>`;
                }
              }

              async function saveStage(event) {
                event.preventDefault();
                const id = idInput.value;
                const payload = {
                  name: nameInput.value.trim(),
                  color: colorInput.value || "#74b2d4",
                  icon: iconInput.value || "circle",
                  is_active: activeInput.checked ? 1 : 0,
                  is_closed: closedInput.checked ? 1 : 0,
                };
                if (!payload.name) { Swal.fire("Hinweis", "Bitte gib einen Namen ein.", "info"); return; }
                try {
                  await requestJSON(id ? PHASE_API.update(id) : PHASE_API.store, { method: "POST", body: JSON.stringify(payload) });
                  resetForm();
                  await loadStages();
                  Swal.fire({ icon:"success", title:"Gespeichert", text:"Die Phase wurde gespeichert.", timer:900, showConfirmButton:false });
                  setTimeout(() => window.location.reload(), 950);
                } catch (err) { Swal.fire("Fehler", err.message || "Phase konnte nicht gespeichert werden.", "error"); }
              }

              async function deleteStage(id, usage, isProtected) {
                usage = Number(usage || 0);
                isProtected = Number(isProtected || 0) === 1;
                if (usage > 0) {
                  Swal.fire({ icon:"warning", title:"Löschen nicht möglich", html:`Diese Phase enthält noch <strong>${usage}</strong> Einträge.<br>Bitte verschiebe diese Einträge zuerst in eine andere Phase.` });
                  return;
                }
                if (isProtected) {
                  Swal.fire({ icon:"info", title:"Standard-Phase", text:"Diese Phase ist geschützt. Du kannst sie umbenennen oder deaktivieren, aber nicht löschen." });
                  return;
                }
                const ask = await Swal.fire({ icon:"warning", title:"Phase löschen?", text:"Diese Aktion kann nicht rückgängig gemacht werden.", showCancelButton:true, confirmButtonText:"Ja, löschen", cancelButtonText:"Abbrechen", confirmButtonColor:"#e50656" });
                if (!ask.isConfirmed) return;
                try {
                  await requestJSON(PHASE_API.destroy(id), { method:"DELETE" });
                  await loadStages();
                  Swal.fire({ icon:"success", title:"Gelöscht", timer:900, showConfirmButton:false });
                  setTimeout(() => window.location.reload(), 950);
                } catch (err) { Swal.fire("Nicht möglich", err.message || "Phase konnte nicht gelöscht werden.", "warning"); }
              }

              async function saveOrder() {
                const items = rowsToItems();
                if (!items.length) return;

                const ask = await Swal.fire({
                  icon: "warning",
                  title: "Pipeline-Reihenfolge ändern?",
                  html: `
                    <div style="text-align:left;line-height:1.55">
                      Die neue Reihenfolge wird direkt für das Kanban, Filter und den Workflow verwendet.<br>
                      <strong>Alle vorhandenen Einträge bleiben in ihrer Phase</strong>, werden aber ab sofort nach dieser Pipeline-Reihenfolge angezeigt und verarbeitet.
                    </div>
                  `,
                  showCancelButton: true,
                  confirmButtonText: "Ja, Reihenfolge speichern",
                  cancelButtonText: "Abbrechen",
                  confirmButtonColor: "#93c21c"
                });
                if (!ask.isConfirmed) return;

                try {
                  await requestJSON(PHASE_API.reorder, { method:"POST", body:JSON.stringify({ items }) });
                  await loadStages();
                  Swal.fire({ icon:"success", title:"Sortierung gespeichert", text:"Die Pipeline wird neu geladen.", timer:1000, showConfirmButton:false });
                  setTimeout(() => window.location.reload(), 1050);
                } catch (err) { Swal.fire("Fehler", err.message || "Sortierung konnte nicht gespeichert werden.", "error"); }
              }

              document.addEventListener("click", (event) => {
                if (event.target.closest("#btnOpenStageManager") || event.target.closest("#btnOpenStageManagerTop")) { event.preventDefault(); openModal(); return; }
                if (event.target.closest("[data-lsm-close]")) { event.preventDefault(); closeModal(); return; }
                const subBtn = event.target.closest("[data-lsm-substages]");
                if (subBtn) { event.preventDefault(); openSubstageDrawer(subBtn.dataset.lsmSubstages); return; }
                const saveSubBtn = event.target.closest("[data-lsm-sub-save]");
                if (saveSubBtn) { event.preventDefault(); saveSubstage(saveSubBtn.dataset.lsmSubSave); return; }
                const deleteSubBtn = event.target.closest("[data-lsm-sub-delete]");
                if (deleteSubBtn) { event.preventDefault(); deleteSubstage(deleteSubBtn.dataset.lsmSubDelete, deleteSubBtn.dataset.usage); return; }
                const editBtn = event.target.closest("[data-lsm-edit]");
                if (editBtn) { const stage = stages.find((s) => String(s.id) === String(editBtn.dataset.lsmEdit)); if (stage) fillForm(stage); return; }
                const deleteBtn = event.target.closest("[data-lsm-delete]");
                if (deleteBtn) deleteStage(deleteBtn.dataset.lsmDelete, deleteBtn.dataset.usage, deleteBtn.dataset.protected);
              });
              document.addEventListener("keydown", (event) => { if (event.key === "Escape" && modal?.classList.contains("is-open")) closeModal(); });
              colorInput?.addEventListener("input", updateColorText);
              form?.addEventListener("submit", saveStage);
              qs("#lsmResetForm")?.addEventListener("click", resetForm);
              qs("#lsmReloadStages")?.addEventListener("click", loadStages);
              qs("#lsmSaveOrder")?.addEventListener("click", saveOrder);
              qs("#lsmCloseSubstageDrawer")?.addEventListener("click", closeSubstageDrawer);
              qs("#lsmCreateSubstage")?.addEventListener("click", createSubstage);
              qs("#lsmSaveSubstageOrder")?.addEventListener("click", saveSubstageOrder);
              window.openLeadStageSubstageConfig = async function (stageId) {
                if (!stageId) return;
                openModal();
                if (!stages.length) await loadStages();
                await openSubstageDrawer(stageId);
              };
              document.addEventListener("DOMContentLoaded", () => { initIconSelect(); updateColorText(); refreshIcons(); setKanbanPhaseName("Hauptphasen", "Aktuelle Ansicht"); });
            })();
          </script>

          <script>
            (function () {
              "use strict";

              const STORAGE_KEY = "leadKanban.viewOptions.v2";
              const zoomSteps = [0.7, 0.8, 0.9, 1];
              const DEFAULT_COLUMN_COLOR = "#93c21c";

              const qs = (s, ctx = document) => ctx.querySelector(s);
              const qsa = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));

              function readState() {
                try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}"); } catch { return {}; }
              }
              function saveState(state) {
                try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch {}
              }
              function clampZoom(value) {
                const n = Number(value);
                if (!Number.isFinite(n)) return 1;
                return Math.min(1, Math.max(0.7, n));
              }
              function nearestStep(value) {
                const z = clampZoom(value);
                return zoomSteps.reduce((best, current) => Math.abs(current - z) < Math.abs(best - z) ? current : best, 1);
              }
              function safeWidth(value) {
                return ["compact", "normal", "wide"].includes(value) ? value : "normal";
              }
              function stageColorFor(key) {
                const meta = window.LeadUI?.APP?.kanbanStageMeta?.[key] || window.LeadUI?.APP?.stageMeta?.[key] || {};
                return meta.color || DEFAULT_COLUMN_COLOR;
              }
              function paintColumnColors(useStageColors) {
                qsa("#kanban .column").forEach((col) => {
                  const color = useStageColors ? stageColorFor(col.id) : DEFAULT_COLUMN_COLOR;
                  col.style.setProperty("--stage-color", color || DEFAULT_COLUMN_COLOR);
                  col.dataset.stageColor = useStageColors ? "1" : "0";
                  const head = col.querySelector("h3");
                  if (head) head.style.setProperty("background", color || DEFAULT_COLUMN_COLOR, "important");
                });
              }
              function applyKanbanViewOptions(next = {}) {
                const card = qs(".kanban-zoom-card");
                const area = qs("#kanbanZoomArea");
                const compactToggle = qs("#kbCompactToggle");
                const useStageColorsToggle = qs("#kbUseStageColorsToggle");
                const widthSelect = qs("#kbColumnWidthSelect");
                if (!card || !area) return;

                const current = readState();
                const state = {
                  zoom: nearestStep(next.zoom ?? current.zoom ?? 1),
                  compact: typeof next.compact === "boolean" ? next.compact : !!current.compact,
                  width: safeWidth(next.width ?? current.width ?? "normal"),
                  useStageColors: typeof next.useStageColors === "boolean" ? next.useStageColors : !!current.useStageColors,
                };

                card.style.setProperty("--kb-zoom", String(state.zoom));
                card.classList.toggle("kb-compact", state.compact);
                card.classList.remove("kb-width-compact", "kb-width-normal", "kb-width-wide");
                card.classList.add(`kb-width-${state.width}`);

                if (compactToggle) compactToggle.checked = state.compact;
                if (useStageColorsToggle) useStageColorsToggle.checked = state.useStageColors;
                if (widthSelect) widthSelect.value = state.width;

                qsa("[data-kb-zoom]").forEach((btn) => {
                  btn.classList.toggle("is-active", Number(btn.dataset.kbZoom) === state.zoom);
                });

                paintColumnColors(state.useStageColors);
                saveState(state);

                if (window.feather?.replace) requestAnimationFrame(() => feather.replace());
              }
              function changeZoom(direction) {
                const state = readState();
                const current = nearestStep(state.zoom ?? 1);
                const index = zoomSteps.indexOf(current);
                const nextIndex = Math.min(zoomSteps.length - 1, Math.max(0, index + direction));
                applyKanbanViewOptions({ zoom: zoomSteps[nextIndex] });
              }

              document.addEventListener("click", (event) => {
                const zoomBtn = event.target.closest("[data-kb-zoom]");
                if (zoomBtn) { event.preventDefault(); applyKanbanViewOptions({ zoom: Number(zoomBtn.dataset.kbZoom) }); return; }
                if (event.target.closest("#kbZoomOutBtn")) { event.preventDefault(); changeZoom(-1); return; }
                if (event.target.closest("#kbZoomInBtn")) { event.preventDefault(); changeZoom(1); }
              });
              document.addEventListener("change", (event) => {
                if (event.target?.id === "kbCompactToggle") applyKanbanViewOptions({ compact: event.target.checked });
                if (event.target?.id === "kbUseStageColorsToggle") applyKanbanViewOptions({ useStageColors: event.target.checked });
                if (event.target?.id === "kbColumnWidthSelect") applyKanbanViewOptions({ width: event.target.value });
              });
              document.addEventListener("DOMContentLoaded", () => applyKanbanViewOptions());
              const obs = new MutationObserver(() => applyKanbanViewOptions());
              document.addEventListener("DOMContentLoaded", () => {
                const board = qs("#kanban");
                if (board) obs.observe(board, { childList:true, subtree:false });
              });
              window.applyKanbanViewOptions = applyKanbanViewOptions;
            })();
          </script>

        <script>
          window.GlobalBreadcrumbs = [
              {
                  label: 'Workspace',
                  url: "{{ url('/') }}"
              },
              {
                  label: 'Kunden',
                  url: "{{ url('/new_lead_view') }}"
              },
              {
                  label: 'Prozess',
                  url: "{{ url()->current() }}",
                  clickable: false
              }
          ];

          if (window.setGlobalBreadcrumbs) {
              window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
          }
      </script>


      <script>
      /* =========================================================
         Final Kanban Safety Bridge
         Keeps old inline/list handlers working even if functions are scoped.
      ========================================================= */
      (function(){
        'use strict';

        window.escapeHTML = window.escapeHTML || function(value) {
          return String(value ?? '').replace(/[&<>"']/g, function(m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
          });
        };

        window.showProductStageInfoFromElement = window.showProductStageInfoFromElement || function(el) {
          const d = el?.dataset || {};
          const product = d.productName || d.initial || d.product || 'Produkt';
          const companyStage = d.stage || d.companyStage || '-';
          const productStage = d.productStageName || d.product_stage_name || d.productStage || d.productStageId || 'Noch keine Produktphase';
          const taskPhase = d.productTaskPhaseName || d.product_task_phase_name || d.productTaskPhase || 'Keine Unterphase';
          const html = `
            <div style="text-align:left">
              <div style="border:1px solid #dbeafe;background:#f8fafc;border-radius:14px;padding:12px">
                <div style="font-weight:900;margin-bottom:8px">${window.escapeHTML(product)}</div>
                <div><strong>Unternehmensphase:</strong> ${window.escapeHTML(companyStage)}</div>
                <div><strong>Produktphase:</strong> ${window.escapeHTML(productStage)}</div>
                <div><strong>Unterphase:</strong> ${window.escapeHTML(taskPhase)}</div>
              </div>
            </div>`;
          if (window.Swal) {
            Swal.fire({title:'Produktstatus', html, width:560, confirmButtonText:'Schließen', didOpen:function(){ if(window.feather) window.feather.replace(); }});
          } else {
            alert(`Produkt: ${product}
      Unternehmensphase: ${companyStage}
      Produktphase: ${productStage}
      Unterphase: ${taskPhase}`);
          }
        };

        document.addEventListener('DOMContentLoaded', function(){
          const applyBtn = document.getElementById('kbWorkflowApplyProduct');
          const productSelect = document.getElementById('kbWorkflowProduct');
          const productBox = document.getElementById('kbWorkflowProductBox');

          if (productSelect && window.jQuery && window.jQuery.fn.select2 && !window.jQuery(productSelect).hasClass('select2-hidden-accessible')) {
            window.jQuery(productSelect).select2({
              placeholder: 'Produkt für Workflow wählen…',
              allowClear: true,
              width: '260px',
              dropdownParent: window.jQuery(document.body),
              templateResult: function(option){
                if (!option.id) return option.text;
                const el = option.element;
                const initial = el?.dataset?.initial || '';
                const name = el?.dataset?.name || option.text || '';
                return window.jQuery(`<span class="kb-workflow-select2-option"><span class="kb-workflow-select2-icon"><i class="feather icon-box"></i></span><span class="kb-workflow-select2-text"><span class="kb-workflow-select2-title">${window.escapeHTML(name || option.text)}</span><span class="kb-workflow-select2-sub">${window.escapeHTML(initial ? ('Kürzel: ' + initial) : 'Produkt-Workflow')}</span></span></span>`);
              },
              templateSelection: function(option){
                if (!option.id) return option.text;
                const el = option.element;
                const initial = el?.dataset?.initial || '';
                const name = el?.dataset?.name || option.text || '';
                return window.jQuery(`<span class="kb-workflow-select2-option"><span class="kb-workflow-select2-icon"><i class="feather icon-box"></i></span><span class="kb-workflow-select2-text"><span class="kb-workflow-select2-title">${window.escapeHTML(name || option.text)}</span><span class="kb-workflow-select2-sub">${window.escapeHTML(initial ? ('Kürzel: ' + initial) : 'Produkt-Workflow')}</span></span></span>`);
              },
              escapeMarkup: function(m){ return m; }
            });
          }

          document.querySelectorAll('[data-kb-workflow-mode="product"]').forEach(function(btn){
            btn.addEventListener('click', function(){
              productBox?.classList.remove('d-none');
              if (productSelect) productSelect.disabled = false;
              if (applyBtn) applyBtn.disabled = !productSelect?.value;
              if (window.jQuery && productSelect) window.jQuery(productSelect).prop('disabled', false).trigger('change.select2');
            }, true);
          });
        });
      })();




        </script>

  <script>
      /* =========================================================
         Kanban Lead Task Management - AJAX loaded on demand
         Replaces the old Next Step button on Kanban cards.
         ========================================================= */
      (function () {
        'use strict';

        if (window.__kanbanLeadTaskManagementBooted) return;
        window.__kanbanLeadTaskManagementBooted = true;

        const TASK_URLS = {
          context: (leadProductId) => `{{ url('/admin/kanban/tasks/context') }}/${encodeURIComponent(leadProductId)}`,
          manual: `{{ url('/admin/kanban/tasks/manual') }}`,
          template: `{{ url('/admin/kanban/tasks/template') }}`,
          status: (taskId) => `{{ url('/admin/kanban/tasks') }}/${encodeURIComponent(taskId)}/status`,
          destroy: (taskId) => `{{ url('/admin/kanban/tasks') }}/${encodeURIComponent(taskId)}`
        };

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        const state = {
          open: false,
          leadProductListId: null,
          context: null,
          templates: [],
          tasks: [],
          employees: [],
          authEmployeeId: "{{ auth()->user()->name ?? '' }}",
          search: '',
          status: ''
        };

        const qs = (selector, root = document) => root.querySelector(selector);
        const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));
        const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
        const cssEscape = (value) => window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&');

        function featherRefresh() {
          if (window.feather) window.feather.replace();
        }

        function notify(message, type = 'success') {
          if (window.toastr) {
            window.toastr[type === 'error' ? 'error' : 'success'](message);
            return;
          }
          if (type === 'error') alert(message);
        }

        async function requestJson(url, options = {}) {
          const res = await fetch(url, {
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              ...(options.headers || {})
            },
            ...options
          });

          const data = await res.json().catch(() => ({}));
          if (!res.ok || data.success === false) {
            throw new Error(data.message || 'Serverfehler.');
          }
          return data;
        }

        function openMainModal() {
          qs('#kbTaskModal')?.classList.add('open');
          qs('#kbTaskModalBackdrop')?.classList.add('show');
          qs('#kbTaskModal')?.setAttribute('aria-hidden', 'false');
          state.open = true;
        }

        function closeMainModal() {
          qs('#kbTaskModal')?.classList.remove('open');
          qs('#kbTaskModalBackdrop')?.classList.remove('show');
          qs('#kbTaskModal')?.setAttribute('aria-hidden', 'true');
          state.open = false;
        }

        function openFormModal() {
          qs('#kbTaskFormModal')?.classList.add('open');
          qs('#kbTaskFormBackdrop')?.classList.add('show');
          qs('#kbTaskFormModal')?.setAttribute('aria-hidden', 'false');
        }

        function closeFormModal() {
          qs('#kbTaskFormModal')?.classList.remove('open');
          qs('#kbTaskFormBackdrop')?.classList.remove('show');
          qs('#kbTaskFormModal')?.setAttribute('aria-hidden', 'true');
          qs('#kbTaskForm')?.reset();
          setSelectValue('#kbFormPerformer', '');
          setSelectValue('#kbFormEmployees', []);
        }

        function setSelectValue(selector, value) {
          const el = qs(selector);
          if (!el) return;
          if (window.jQuery && window.jQuery.fn.select2) window.jQuery(el).val(value).trigger('change');
          else if (Array.isArray(value)) qsa('option', el).forEach(o => o.selected = value.includes(o.value));
          else el.value = value;
        }

        function initEmployeeSelects() {
          const performer = qs('#kbFormPerformer');
          const employees = qs('#kbFormEmployees');

          const performerOptions = ['<option value="">Automatisch: Ich selbst</option>']
            .concat(state.employees.map(e => `<option value="${esc(e.id)}">${esc(e.text || ((e.lastname || '') + ' ' + (e.name || '')).trim() || ('#' + e.id))}</option>`))
            .join('');

          if (performer) performer.innerHTML = performerOptions;
          if (employees) {
            employees.innerHTML = state.employees
              .map(e => `<option value="${esc(e.id)}">${esc(e.text || ((e.lastname || '') + ' ' + (e.name || '')).trim() || ('#' + e.id))}</option>`)
              .join('');
          }

          if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery('.kb-task-select2').select2({
              width: '100%',
              dropdownParent: window.jQuery('#kbTaskFormModal')
            });
          }
        }

        async function openTaskManagement(leadProductListId) {
          if (!leadProductListId) {
            notify('lead_product_list_id fehlt.', 'error');
            return;
          }

          state.leadProductListId = leadProductListId;
          openMainModal();

          const contextText = qs('#kbTaskContextText');
          if (contextText) contextText.textContent = 'Aufgaben werden geladen …';
          qs('#kbTaskTemplates').innerHTML = '<div class="kb-task-empty">Stage-Aufgaben werden geladen …</div>';
          qs('#kbTaskSaved').innerHTML = '<div class="kb-task-empty">Gespeicherte Aufgaben werden geladen …</div>';

          try {
            const data = await requestJson(TASK_URLS.context(leadProductListId));
            state.context = data.context || {};
            const sourceCard = document.querySelector(`[data-lead-product-list-id="${CSS.escape(String(leadProductListId))}"]`)
              || document.querySelector(`.card[data-lead-product-id="${CSS.escape(String(leadProductListId))}"]`);
            if (sourceCard && !state.context.stage_started_at) {
              state.context.stage_started_at = sourceCard.dataset.stageStartedAt || sourceCard.dataset.updatedAt || sourceCard.dataset.createdAt || null;
            }
            state.templates = data.templates || [];
            state.tasks = data.tasks || [];
            state.employees = data.employees || [];
            state.authEmployeeId = String(data.auth_employee_id || state.authEmployeeId || '');

            if (contextText) {
              const ctx = state.context;
              contextText.textContent = `${ctx.customer_name || 'Kunde'} · ${ctx.product_name || 'Produkt'} · ${ctx.stage_label || 'Stage'}${ctx.sub_stage_label ? ' / ' + ctx.sub_stage_label : ''}`;
            }

            initEmployeeSelects();
            renderAll();
            updateCardBadge(leadProductListId, state.tasks);
          } catch (e) {
            qs('#kbTaskTemplates').innerHTML = `<div class="kb-task-empty">${esc(e.message)}</div>`;
            qs('#kbTaskSaved').innerHTML = '<div class="kb-task-empty">Fehler beim Laden.</div>';
            notify(e.message, 'error');
          }
        }

        function filteredTasks() {
          const q = state.search.toLowerCase().trim();
          const status = state.status;
          return state.tasks.filter(task => {
            if (status && task.status !== status) return false;
            if (!q) return true;
            const hay = [
              task.title,
              task.description,
              task.internal_note,
              task.status,
              task.performer?.display_name,
              ...(task.employees || []).map(e => e.display_name)
            ].join(' ').toLowerCase();
            return hay.includes(q);
          });
        }

        function taskTitle(task) {
          return String(task?.title || task?.phase_name || task?.activity_title || '-');
        }

        function firstTemplateTaskTitle(offset = 0) {
          const flat = [];
          (state.templates || []).forEach(phase => {
            const acts = Array.isArray(phase.activities) ? phase.activities : [];
            if (acts.length) {
              acts.forEach(activity => flat.push({
                title: activity.title || phase.phase_name,
                description: activity.description || phase.description || '',
                minutes: activity.estimated_minutes || ''
              }));
            } else {
              flat.push({ title: phase.phase_name, description: phase.description || '', minutes: '' });
            }
          });
          return flat[offset] || null;
        }

        function updateSequenceSummary() {
          const landed = state.context?.stage_started_at || state.context?.landed_at || state.context?.updated_at || null;
          const doneTasks = (state.tasks || []).filter(t => t.status === 'done');
          const openTasks = (state.tasks || []).filter(t => !['done', 'cancelled'].includes(t.status));
          const previous = doneTasks.length ? doneTasks[doneTasks.length - 1] : null;
          const current = openTasks.length ? openTasks[0] : firstTemplateTaskTitle(0);
          const next = openTasks.length > 1 ? openTasks[1] : firstTemplateTaskTitle(openTasks.length ? 0 : 1);

          const set = (id, html) => {
            const el = qs(id);
            if (el) el.innerHTML = html;
          };

          set('#kbTaskSeqLanded', esc(landed ? String(landed).replace('T', ' ').slice(0, 16) : 'Neu / gerade gestartet'));
          set('#kbTaskSeqPrevious', previous ? esc(taskTitle(previous)) : 'Noch nichts erledigt');
          set('#kbTaskSeqCurrent', current ? esc(taskTitle(current)) : 'Keine offene Aufgabe');
          set('#kbTaskSeqNext', next ? esc(taskTitle(next)) : 'Keine weitere Aufgabe');
        }

        function renderAll() {
          renderTemplates();
          renderSavedTasks();
          updateSequenceSummary();
          featherRefresh();
        }

        function renderTemplates() {
          const host = qs('#kbTaskTemplates');
          if (!host) return;
          if (!state.templates.length) {
            host.innerHTML = '<div class="kb-task-empty">Keine passenden Aufgaben für diese Stage/Sub-Stage gefunden.</div>';
            return;
          }

          host.innerHTML = state.templates.map(phase => {
            const activities = phase.activities || [];
            const activityHtml = activities.length
              ? activities.map(activity => templateActivityHtml(phase, activity)).join('')
              : templatePhaseHtml(phase);

            return `
              <div class="kb-task-card">
                <div class="kb-task-card-title">${esc(phase.phase_name)}</div>
                ${phase.section_name ? `<div class="kb-task-card-desc">${esc(phase.section_name)}</div>` : ''}
                ${phase.description ? `<div class="kb-task-card-desc">${esc(phase.description)}</div>` : ''}
                <div class="kb-task-list" style="margin-top:10px;">${activityHtml}</div>
              </div>`;
          }).join('');
        }

        function templatePhaseHtml(phase) {
          return `
            <div class="kb-task-card">
              <div class="kb-task-card-title">${esc(phase.phase_name)}</div>
              <div class="kb-task-card-actions">
                <button type="button" class="kb-task-mini-btn" data-kb-template-plan data-phase-id="${esc(phase.id)}" data-activity-id="" data-title="${esc(phase.phase_name)}" data-description="${esc(phase.description || '')}" data-minutes="">
                  <i class="feather icon-calendar"></i> Planen
                </button>
                <button type="button" class="kb-task-mini-btn" data-kb-template-direct data-phase-id="${esc(phase.id)}" data-activity-id="">
                  <i class="feather icon-plus"></i> Übernehmen
                </button>
              </div>
            </div>`;
        }

        function templateActivityHtml(phase, activity) {
          return `
            <div class="kb-task-card">
              <div class="kb-task-card-title">${esc(activity.title || phase.phase_name)}</div>
              ${activity.description ? `<div class="kb-task-card-desc">${esc(activity.description)}</div>` : ''}
              <div class="kb-task-card-meta">
                ${activity.estimated_minutes ? `<span class="kb-task-pill blue"><i class="feather icon-clock"></i>${esc(activity.estimated_minutes)} Min.</span>` : ''}
                ${activity.photo_required ? '<span class="kb-task-pill red"><i class="feather icon-camera"></i> Foto Pflicht</span>' : ''}
              </div>
              <div class="kb-task-card-actions">
                <button type="button" class="kb-task-mini-btn" data-kb-template-plan data-phase-id="${esc(phase.id)}" data-activity-id="${esc(activity.id)}" data-title="${esc(activity.title || phase.phase_name)}" data-description="${esc(activity.description || '')}" data-minutes="${esc(activity.estimated_minutes || '')}">
                  <i class="feather icon-calendar"></i> Planen
                </button>
                <button type="button" class="kb-task-mini-btn" data-kb-template-direct data-phase-id="${esc(phase.id)}" data-activity-id="${esc(activity.id)}">
                  <i class="feather icon-plus"></i> Übernehmen
                </button>
              </div>
            </div>`;
        }

        function renderSavedTasks() {
          const host = qs('#kbTaskSaved');
          if (!host) return;
          const tasks = filteredTasks();
          if (!tasks.length) {
            host.innerHTML = '<div class="kb-task-empty">Keine Aufgaben gefunden.</div>';
            return;
          }
          host.innerHTML = tasks.map(savedTaskHtml).join('');
          featherRefresh();
        }

        function statusLabel(status) {
          return ({ open: 'Offen', scheduled: 'Geplant', in_progress: 'In Bearbeitung', done: 'Erledigt', cancelled: 'Abgebrochen' }[status] || status || 'Offen');
        }

        function savedTaskHtml(task) {
          const overdue = task.is_overdue ? ' is-overdue' : '';
          const performer = task.performer?.display_name || 'Automatisch / Ich';
          const nextText = task.status === 'done'
            ? `Erledigt${task.done_at ? ' am ' + task.done_at : ''}`
            : (task.planned_start_at ? `Nächste Aktion: ${String(task.planned_start_at).replace('T', ' ')}` : 'Nächste Aktion noch nicht geplant');

          return `
            <div class="kb-task-card${overdue}" data-kb-task-id="${esc(task.id)}">
              <div class="kb-task-card-title">${esc(task.title)}</div>
              ${task.description ? `<div class="kb-task-card-desc">${esc(task.description)}</div>` : ''}

              <div class="kb-task-card-meta">
                <span class="kb-task-pill ${task.status === 'done' ? 'green' : 'blue'}">${esc(statusLabel(task.status))}</span>
                ${task.is_overdue ? '<span class="kb-task-pill red">Überfällig</span>' : ''}
                ${task.photo_required ? '<span class="kb-task-pill red"><i class="feather icon-camera"></i> Foto Pflicht</span>' : ''}
                ${task.estimated_minutes ? `<span class="kb-task-pill"><i class="feather icon-clock"></i>${esc(task.estimated_minutes)} Min.</span>` : ''}
                ${task.planned_start_at ? `<span class="kb-task-pill">Start: ${esc(String(task.planned_start_at).replace('T', ' '))}</span>` : ''}
                ${task.planned_end_at ? `<span class="kb-task-pill">Ende: ${esc(String(task.planned_end_at).replace('T', ' '))}</span>` : ''}
                <span class="kb-task-pill"><i class="feather icon-user"></i>${esc(performer)}</span>
                ${(task.has_personal_task || task.external_links?.personal_task_id) ? '<span class="kb-task-pill green"><i class="feather icon-check-square"></i> Persönliche Aufgabe</span>' : ''}
                ${(task.has_appointment || task.external_links?.appointment_id) ? '<span class="kb-task-pill blue"><i class="feather icon-calendar"></i> Termin</span>' : ''}
              </div>

              ${task.internal_note ? `<div class="kb-task-card-desc"><strong>Ablauf:</strong> ${esc(task.internal_note)}</div>` : ''}
              <div class="kb-task-card-next"><strong>${esc(nextText)}</strong></div>

              <div class="kb-task-card-actions">
                ${task.status !== 'done' ? `<button type="button" class="kb-task-mini-btn" data-kb-task-done="${esc(task.id)}"><i class="feather icon-check"></i> Erledigt</button>` : ''}
                ${task.status !== 'in_progress' && task.status !== 'done' ? `<button type="button" class="kb-task-mini-btn" data-kb-task-progress="${esc(task.id)}"><i class="feather icon-play"></i> Starten</button>` : ''}
                <button type="button" class="kb-task-mini-btn" data-kb-task-plan-existing="${esc(task.id)}"><i class="feather icon-calendar"></i> Planen</button>
                <button type="button" class="kb-task-mini-btn" data-kb-task-delete="${esc(task.id)}"><i class="feather icon-trash"></i> Löschen</button>
              </div>
            </div>`;
        }

        function resetFormBase(title, mode) {
          qs('#kbTaskFormTitle').textContent = title;
          qs('#kbFormMode').value = mode;
          qs('#kbFormLeadProductListId').value = state.leadProductListId || '';
          qs('#kbFormTaskPhaseId').value = '';
          qs('#kbFormPhaseActivityId').value = '';
          qs('#kbFormExistingTaskId').value = '';
          qs('#kbFormTitle').value = '';
          qs('#kbFormDescription').value = '';
          qs('#kbFormStart').value = '';
          qs('#kbFormEnd').value = '';
          qs('#kbFormMinutes').value = '';
          qs('#kbFormScheduled').checked = false;
          qs('#kbFormInternalNote').value = '';
          qs('#kbFormCreatePersonalTask').checked = false;
          qs('#kbFormCreateAppointment').checked = false;
          qs('#kbFormAppointmentType').value = 'kanban_task';
          qs('#kbFormAppointmentContactMode').value = '';
          qs('#kbFormAppointmentPriority').value = 'normal';
          qs('#kbAppointmentOptions')?.classList.add('d-none');
          setSelectValue('#kbFormPerformer', state.authEmployeeId || '');
          setSelectValue('#kbFormEmployees', []);
        }

        function openManualForm() {
          resetFormBase('Manuelle Aufgabe erstellen', 'manual');
          openFormModal();
        }

        function openTemplateForm(btn) {
          resetFormBase('Vorlagen-Aufgabe planen', 'template');
          qs('#kbFormTaskPhaseId').value = btn.dataset.phaseId || '';
          qs('#kbFormPhaseActivityId').value = btn.dataset.activityId || '';
          qs('#kbFormTitle').value = btn.dataset.title || 'Aufgabe';
          qs('#kbFormDescription').value = btn.dataset.description || '';
          qs('#kbFormMinutes').value = btn.dataset.minutes || '';
          qs('#kbFormScheduled').checked = true;
          openFormModal();
        }

        function openExistingPlanForm(taskId) {
          const task = state.tasks.find(t => Number(t.id) === Number(taskId));
          if (!task) return;
          resetFormBase('Aufgabe planen / aktualisieren', 'existing');
          qs('#kbFormExistingTaskId').value = task.id;
          qs('#kbFormTitle').value = task.title || '';
          qs('#kbFormDescription').value = task.description || '';
          qs('#kbFormStart').value = task.planned_start_at || '';
          qs('#kbFormEnd').value = task.planned_end_at || '';
          qs('#kbFormMinutes').value = task.estimated_minutes || '';
          qs('#kbFormScheduled').checked = true;
          qs('#kbFormInternalNote').value = task.internal_note || '';
          setSelectValue('#kbFormPerformer', task.performer?.id || state.authEmployeeId || '');
          setSelectValue('#kbFormEmployees', (task.employees || []).map(e => String(e.id)));
          openFormModal();
        }

        function employeeIdsFromForm() {
          if (window.jQuery && window.jQuery.fn.select2) return window.jQuery('#kbFormEmployees').val() || [];
          return qsa('#kbFormEmployees option:checked').map(o => o.value);
        }

        function formPayloadBase() {
          return {
            lead_product_list_id: qs('#kbFormLeadProductListId').value,
            title: qs('#kbFormTitle').value,
            description: qs('#kbFormDescription').value,
            internal_note: qs('#kbFormInternalNote').value,
            is_scheduled: qs('#kbFormScheduled').checked,
            planned_start_at: qs('#kbFormStart').value || null,
            planned_end_at: qs('#kbFormEnd').value || null,
            estimated_minutes: qs('#kbFormMinutes').value || null,
            performer_employee_id: qs('#kbFormPerformer').value || null,
            employee_ids: employeeIdsFromForm(),

            // NEW
            create_personal_task: qs('#kbFormCreatePersonalTask')?.checked || false,
            create_appointment: qs('#kbFormCreateAppointment')?.checked || false,
            appointment_type: qs('#kbFormAppointmentType')?.value || 'kanban_task',
            appointment_contact_mode: qs('#kbFormAppointmentContactMode')?.value || null,
            appointment_priority: qs('#kbFormAppointmentPriority')?.value || 'normal'
          };
        }

        async function directTemplateStore(btn) {
          const payload = {
            lead_product_list_id: state.leadProductListId,
            task_phase_id: btn.dataset.phaseId,
            phase_activity_id: btn.dataset.activityId || null,
            performer_employee_id: state.authEmployeeId || null,
            employee_ids: [],
            is_scheduled: false
          };
          const data = await requestJson(TASK_URLS.template, { method: 'POST', body: JSON.stringify(payload) });
          state.tasks.unshift(data.task);
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify(data.message || 'Aufgabe wurde übernommen.');
        }

        async function submitForm(e) {
          e.preventDefault();
          const mode = qs('#kbFormMode').value;
          const payload = formPayloadBase();
          let url = TASK_URLS.manual;
          let method = 'POST';

          if (mode === 'template') {
            url = TASK_URLS.template;
            payload.task_phase_id = qs('#kbFormTaskPhaseId').value;
            payload.phase_activity_id = qs('#kbFormPhaseActivityId').value || null;
            delete payload.title;
            delete payload.description;
          }

          if (mode === 'existing') {
            const taskId = qs('#kbFormExistingTaskId').value;
            url = TASK_URLS.status(taskId);
            method = 'PATCH';
            payload.status = payload.is_scheduled ? 'scheduled' : 'open';
            delete payload.lead_product_list_id;
            delete payload.title;
            delete payload.description;
            delete payload.is_scheduled;
            delete payload.estimated_minutes;
          }

          const data = await requestJson(url, { method, body: JSON.stringify(payload) });

          if (mode === 'existing') {
            const index = state.tasks.findIndex(t => Number(t.id) === Number(data.task.id));
            if (index >= 0) state.tasks[index] = data.task;
          } else {
            state.tasks.unshift(data.task);
          }

          closeFormModal();
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify(data.message || 'Aufgabe wurde gespeichert.');
        }

        async function updateTaskStatus(taskId, status) {
          const data = await requestJson(TASK_URLS.status(taskId), { method: 'PATCH', body: JSON.stringify({ status }) });
          const index = state.tasks.findIndex(t => Number(t.id) === Number(taskId));
          if (index >= 0) state.tasks[index] = data.task;
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify(data.message || 'Aufgabe wurde aktualisiert.');
        }

        async function deleteTask(taskId) {
          const ok = window.Swal
            ? await Swal.fire({ title: 'Aufgabe löschen?', text: 'Diese Aufgabe wird entfernt.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Löschen', cancelButtonText: 'Abbrechen' }).then(r => r.isConfirmed)
            : confirm('Aufgabe wirklich löschen?');
          if (!ok) return;
          await requestJson(TASK_URLS.destroy(taskId), { method: 'DELETE' });
          state.tasks = state.tasks.filter(t => Number(t.id) !== Number(taskId));
          renderSavedTasks();
          updateCardBadge(state.leadProductListId, state.tasks);
          notify('Aufgabe wurde gelöscht.');
        }

        function updateCardBadge(leadProductListId, tasks) {
          const card = document.querySelector(`[data-lead-product-list-id="${cssEscape(leadProductListId)}"], [data-lead-product-id="${cssEscape(leadProductListId)}"]`);
          if (!card) return;
          const badge = card.querySelector('[data-kanban-task-count]');
          if (!badge) return;
          const openCount = (tasks || []).filter(t => !['done', 'cancelled'].includes(t.status)).length;
          badge.textContent = openCount > 99 ? '99+' : String(openCount);
          badge.classList.toggle('d-none', openCount <= 0);
        }

        window.openKanbanTaskManagement = openTaskManagement;

        document.addEventListener('click', function (e) {
          const openBtn = e.target.closest('[data-open-kanban-task-management]');
          if (openBtn) {
            e.preventDefault();
            e.stopPropagation();
            const leadProductId = openBtn.dataset.leadProductListId || openBtn.closest('[data-lead-product-list-id]')?.dataset.leadProductListId;
            openTaskManagement(leadProductId);
            return;
          }

          const templatePlan = e.target.closest('[data-kb-template-plan]');
          if (templatePlan) {
            e.preventDefault();
            openTemplateForm(templatePlan);
            return;
          }

          const templateDirect = e.target.closest('[data-kb-template-direct]');
          if (templateDirect) {
            e.preventDefault();
            directTemplateStore(templateDirect).catch(err => notify(err.message, 'error'));
            return;
          }

          const doneBtn = e.target.closest('[data-kb-task-done]');
          if (doneBtn) {
            e.preventDefault();
            updateTaskStatus(doneBtn.dataset.kbTaskDone, 'done').catch(err => notify(err.message, 'error'));
            return;
          }

          const progressBtn = e.target.closest('[data-kb-task-progress]');
          if (progressBtn) {
            e.preventDefault();
            updateTaskStatus(progressBtn.dataset.kbTaskProgress, 'in_progress').catch(err => notify(err.message, 'error'));
            return;
          }

          const planExistingBtn = e.target.closest('[data-kb-task-plan-existing]');
          if (planExistingBtn) {
            e.preventDefault();
            openExistingPlanForm(planExistingBtn.dataset.kbTaskPlanExisting);
            return;
          }

          const deleteBtn = e.target.closest('[data-kb-task-delete]');
          if (deleteBtn) {
            e.preventDefault();
            deleteTask(deleteBtn.dataset.kbTaskDelete).catch(err => notify(err.message, 'error'));
          }
        }, true);

        qs('#kbTaskModalClose')?.addEventListener('click', closeMainModal);
        qs('#kbTaskModalBackdrop')?.addEventListener('click', closeMainModal);
        qs('#kbTaskFormClose')?.addEventListener('click', closeFormModal);
        qs('#kbTaskFormCancel')?.addEventListener('click', closeFormModal);
        qs('#kbTaskFormBackdrop')?.addEventListener('click', closeFormModal);
        qs('#kbManualTaskBtn')?.addEventListener('click', openManualForm);
        qs('#kbTaskForm')?.addEventListener('submit', submitForm);

        qs('#kbTaskSearch')?.addEventListener('input', function () {
          state.search = this.value || '';
          renderSavedTasks();
        });

        qs('#kbTaskStatusFilter')?.addEventListener('change', function () {
          state.status = this.value || '';
          renderSavedTasks();
        });
        qs('#kbFormCreateAppointment')?.addEventListener('change', function () {
          qs('#kbAppointmentOptions')?.classList.toggle('d-none', !this.checked);

          if (this.checked) {
            qs('#kbFormScheduled').checked = true;
          }
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            closeFormModal();
            closeMainModal();
          }
        });
      })();
    </script>
     <script>

      /* =========================================================
         Lead Aktivität / Erinnerung / Nächster Schritt Add-on
         Preload summaries, counters, carousel, searchable activity modal
         ========================================================= */
      (function () {
        'use strict';
        // Disabled: replaced by Kanban Lead Task Management modal.
        return;

        const ENDPOINTS = {
          store: "{{ url('/kanban/reminders') }}",
          due: "{{ url('/kanban/reminders/due') }}",
          context: "{{ url('/kanban/reminders/context') }}",
          cardsSummary: "{{ url('/kanban/reminders/cards-summary') }}",
          doneBase: "{{ url('/kanban/reminders') }}"
        };

        const DE = {
          priority: { normal: 'Normal', important: 'Wichtig', critical: 'Kritisch', success: 'Erfolgreich', error: 'Fehler', warning: 'Warnung' },
          status: { open: 'Offen', done: 'Erledigt', cancelled: 'Abgebrochen', in_progress: 'In Bearbeitung', overdue: 'Überfällig' },
          event: {
            created: 'Erstellt', updated: 'Aktualisiert', deleted: 'Gelöscht', reminder_created: 'Erinnerung erstellt', reminder_done: 'Erinnerung erledigt',
            stage_changed: 'Phase geändert', status_changed: 'Status geändert', updated_stage: 'Phase aktualisiert', moved: 'Verschoben', activity: 'Aktivität'
          }
        };

        const state = { booted: false, pollingStarted: false, active: null, activities: [], reminders: [], filter: 'all', search: '', sort: 'oldest', preloadRunning: false };

        function csrf() { return document.querySelector('meta[name="csrf-token"]')?.content || ''; }
        function esc(value) { return String(value ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
        function cssEscape(value) { return window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&'); }
        function translate(map, value, fallback) { const key = String(value || '').toLowerCase(); return map[key] || fallback || value || ''; }

        function parseDateValue(value, timeValue) {
          if (!value) return null;
          const raw = String(value).trim();
          const time = timeValue ? String(timeValue).slice(0,5) : '';
          if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
            const [y,m,d] = raw.split('-').map(Number);
            const [hh,mm] = time ? time.split(':').map(Number) : [0,0];
            return new Date(y, m - 1, d, hh || 0, mm || 0);
          }
          const normalized = raw.replace(' ', 'T');
          const date = new Date(normalized);
          return Number.isNaN(date.getTime()) ? null : date;
        }

        function formatGermanDateTime(value, timeValue) {
          const date = parseDateValue(value, timeValue);
          if (!date) return '';
          const d = date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
          const hasTime = !!timeValue || /\d{2}:\d{2}/.test(String(value || ''));
          if (!hasTime) return d;
          const t = date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
          return `${d}, ${t} Uhr`;
        }

        function dateSortValue(item) {
          const d = item.bucket === 'reminder'
            ? parseDateValue(item.reminder_date, item.reminder_time)
            : parseDateValue(item.date_raw || item.created_at || item.activity_date || item.date || '');
          return d ? d.getTime() : 0;
        }

        function normalizeActivities(items) {
          return (Array.isArray(items) ? items : []).map(item => {
            const typeRaw = item.type_raw || item.type || item.event_type || item.activity_type || 'activity';
            const rawDate = item.date_raw || item.created_at || item.activity_date || '';
            return {
              id: item.id || '', bucket: 'activity', type: item.type_label || translate(DE.event, typeRaw, 'Aktivität'), type_raw: typeRaw,
              text: item.text || item.description || item.result || '', employee: item.employee || item.employee_name || item.user_name || 'System',
              date: item.date_human || item.date || item.created_at_formatted || formatGermanDateTime(rawDate),
              date_raw: rawDate
            };
          });
        }

        function normalizeReminders(items, ctx) {
          return (Array.isArray(items) ? items : []).map(item => {
            const priorityRaw = String(item.priority || 'normal').toLowerCase();
            const statusRaw = String(item.status || 'open').toLowerCase();
            const dateRaw = item.reminder_date || item.due_date || '';
            const timeRaw = item.reminder_time || item.due_time || '';
            return {
              id: item.id || '', bucket: 'reminder', kind: 'reminder', title: item.title || 'Erinnerung', text: item.description || item.text || '',
              priority: priorityRaw, priority_label: item.priority_label || translate(DE.priority, priorityRaw, 'Normal'),
              status: statusRaw, status_label: item.status_label || translate(DE.status, statusRaw, 'Offen'),
              reminder_date: dateRaw, reminder_time: timeRaw, due_text: item.due_text || formatGermanDateTime(dateRaw, timeRaw),
              employee: item.employee || item.responsible_employee_name || item.owner_name || 'Nicht zugewiesen',
              customer_id: item.customer_id || ctx?.customer_id || state.active?.customer_id || '', alternative_id: item.alternative_id || ctx?.alternative_id || state.active?.alternative_id || '',
              product_id: item.product_id || ctx?.product_id || state.active?.product_id || '', lead_product_list_id: item.lead_product_list_id || ctx?.lead_product_list_id || state.active?.lead_product_list_id || ''
            };
          });
        }

        function ensureReminderButtonBadge(card) {
          const btn = card.querySelector('[data-open-lead-reminder], .kb-open-activity-panel');
          if (!btn) return null;
          let badge = btn.querySelector('[data-kb-reminder-button-count]');
          if (!badge) {
            badge = document.createElement('span');
            badge.className = 'badge-notes kb-reminder-button-count';
            badge.dataset.kbReminderButtonCount = '1';
            badge.style.display = 'none';
            badge.textContent = '0';
            btn.appendChild(badge);
          }
          return badge;
        }

        function contextFromCard(card) {
          return {
            lead_product_list_id: card.dataset.leadProductListId || card.dataset.leadProductId || '',
            customer_id: card.dataset.customerId || '', alternative_id: card.dataset.alternativeId || '', product_id: card.dataset.productId || ''
          };
        }

        function findKanbanCard(ctx) {
          const lpl = ctx.lead_product_list_id || '';
          const c = ctx.customer_id || '';
          const a = ctx.alternative_id || '';
          const p = ctx.product_id || '';
          const selectors = [
            lpl ? `.card[data-lead-product-list-id="${cssEscape(lpl)}"], .card[data-lead-product-id="${cssEscape(lpl)}"]` : '',
            c && a && p ? `.card[data-customer-id="${cssEscape(c)}"][data-alternative-id="${cssEscape(a)}"][data-product-id="${cssEscape(p)}"]` : ''
          ].filter(Boolean);
          for (const selector of selectors) { const found = document.querySelector(selector); if (found) return found; }
          return null;
        }

        function renderCardReminderSummary(card, remindersRaw, activityCountRaw) {
          const reminders = normalizeReminders(remindersRaw || [], contextFromCard(card)).sort((a,b) => dateSortValue(a) - dateSortValue(b));
          const activityCount = Number(activityCountRaw || 0);
          const reminderCount = reminders.length;
          const totalCount = reminderCount + activityCount;
          const badge = ensureReminderButtonBadge(card);
          if (badge) {
            badge.textContent = totalCount > 99 ? '99+' : String(totalCount);
            badge.style.display = totalCount > 0 ? 'inline-flex' : 'none';
            badge.title = `${reminderCount} offene nächste Schritte • ${activityCount} Aktivitäten`;
          }

          let summary = card.querySelector('.kb-reminder-summary');
          if (!summary) {
            summary = document.createElement('div');
            summary.className = 'kb-reminder-summary is-empty';
            const stageTime = card.querySelector('.kb-stage-time');
            if (stageTime) stageTime.insertAdjacentElement('afterend', summary);
            else card.appendChild(summary);
          }

          if (!reminderCount) {
            summary.className = 'kb-reminder-summary is-empty';
            summary.innerHTML = `
              <div class="kb-reminder-head">
                <div class="kb-reminder-title"><i class="feather icon-bell"></i> Keine Erinnerung</div>
                <span class="kb-reminder-priority normal">Offen</span>
              </div>
              <div class="kb-reminder-body">
                <i class="feather icon-info"></i>
                <span>Noch kein nächster Schritt geplant.</span>
              </div>
              <div class="kb-card-summary-counts">
                <span class="is-reminder">${reminderCount} nächste Schritte</span>
                <span class="is-activity">${activityCount} Aktivitäten</span>
              </div>`;
            card.classList.add('kb-summary-ready');
            if (window.feather) window.feather.replace();
            return;
          }

          const today = new Date();
          const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
          const firstDate = String(reminders[0].reminder_date || '').slice(0,10);
          const boxState = firstDate && firstDate < todayStr ? ' kb-reminder-overdue' : (firstDate === todayStr ? ' kb-reminder-due-today' : '');
          summary.className = `kb-reminder-summary${boxState}`;
          summary.dataset.kbReminderIndex = summary.dataset.kbReminderIndex || '0';
          const activeIndex = Math.min(Number(summary.dataset.kbReminderIndex || 0), reminders.length - 1);

          const slides = reminders.map((r, i) => `
            <div class="kb-card-reminder-slide ${i === activeIndex ? 'is-active' : ''}" data-kb-reminder-slide="${i}">
              <div class="kb-reminder-head">
                <div class="kb-reminder-title"><i class="feather icon-bell"></i> Nächster Schritt</div>
                <span class="kb-reminder-priority ${esc(r.priority || 'normal')}">${esc(r.priority_label || 'Normal')}</span>
              </div>
              <div class="kb-reminder-body">
                <i class="feather icon-check-square"></i>
                <span><strong>${esc(r.title || 'Erinnerung')}</strong>${r.text ? `<br>${esc(r.text).slice(0,120)}` : ''}</span>
                <i class="feather icon-calendar"></i>
                <span class="kb-reminder-due">${esc(r.due_text || 'Kein Datum')}</span>
                <i class="feather icon-user"></i>
                <span>${esc(r.employee || 'Nicht zugewiesen')}</span>
              </div>
            </div>`).join('');

          summary.innerHTML = `
            <div class="kb-card-reminder-carousel" data-kb-card-reminder-carousel>
              <div class="kb-card-reminder-track">${slides}</div>
              ${reminders.length > 1 ? `
                <div class="kb-card-reminder-nav">
                  <button type="button" data-kb-reminder-prev title="Zurück">‹</button>
                  <span class="kb-card-reminder-counter">${activeIndex + 1} / ${reminders.length}</span>
                  <button type="button" data-kb-reminder-next title="Weiter">›</button>
                </div>` : ''}
              <div class="kb-card-summary-counts">
                <span class="is-reminder">${reminderCount} nächste Schritte</span>
                <span class="is-activity">${activityCount} Aktivitäten</span>
              </div>
            </div>`;
          card.classList.add('kb-summary-ready');
          if (window.feather) window.feather.replace();
        }

        function moveCardCarousel(summary, dir) {
          const slides = [...summary.querySelectorAll('[data-kb-reminder-slide]')];
          if (!slides.length) return;
          let index = Number(summary.dataset.kbReminderIndex || 0);
          index = (index + dir + slides.length) % slides.length;
          summary.dataset.kbReminderIndex = String(index);
          slides.forEach((s, i) => s.classList.toggle('is-active', i === index));
          const counter = summary.querySelector('.kb-card-reminder-counter');
          if (counter) counter.textContent = `${index + 1} / ${slides.length}`;
        }

        function preloadCardSummaries() {
          if (state.preloadRunning) return;
          const cards = [...document.querySelectorAll('.card[data-customer-id]')].filter(card => !card.dataset.kbSummaryLoaded);
          if (!cards.length) return;
          state.preloadRunning = true;
          cards.forEach(card => { ensureReminderButtonBadge(card); card.dataset.kbSummaryLoaded = 'loading'; });
          const contexts = cards.map(contextFromCard);

          fetch(ENDPOINTS.cardsSummary, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            body: JSON.stringify({ contexts })
          })
          .then(async res => {
            if (!res.ok) throw new Error('summary endpoint failed');
            return await res.json();
          })
          .then(data => {
            const items = data.items || data.cards || {};
            cards.forEach((card, idx) => {
              const ctx = contexts[idx];
              const key = ctx.lead_product_list_id || `${ctx.customer_id}:${ctx.alternative_id}:${ctx.product_id}`;
              const payload = items[key] || items[String(ctx.lead_product_list_id)] || items[idx] || {};
              renderCardReminderSummary(card, payload.reminders || [], payload.activities_count || payload.activity_count || 0);
              card.dataset.kbSummaryLoaded = '1';
            });
          })
          .catch(() => {
            Promise.all(cards.map((card) => {
              const ctx = contextFromCard(card);
              const params = new URLSearchParams(ctx);
              return fetch(ENDPOINTS.context + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => renderCardReminderSummary(card, data.reminders || data.next_steps || [], (data.activities || data.activity_logs || []).length))
                .catch(() => renderCardReminderSummary(card, [], 0))
                .finally(() => { card.dataset.kbSummaryLoaded = '1'; });
            }));
          })
          .finally(() => { state.preloadRunning = false; });
        }

        function setLoadingState() {
          state.activities = []; state.reminders = [];
          const activityList = document.getElementById('kbActivityList');
          const reminderList = document.getElementById('kbReminderList');
          const activityCount = document.getElementById('kbActivityCount');
          const reminderCount = document.getElementById('kbReminderCount');
          if (activityList) activityList.innerHTML = '<div class="kb-empty-state">Aktivitäten werden geladen...</div>';
          if (reminderList) reminderList.innerHTML = '<div class="kb-empty-state">Erinnerungen werden geladen...</div>';
          if (activityCount) activityCount.textContent = '0';
          if (reminderCount) reminderCount.textContent = '0';
        }

        function getCombinedItems() { return [...state.activities.map(x => ({...x, bucket:'activity'})), ...state.reminders.map(x => ({...x, bucket:'reminder'}))]; }

        function renderActivities() {
          const list = document.getElementById('kbActivityList');
          const count = document.getElementById('kbActivityCount');
          if (!list) return;
          const q = String(state.search || '').toLowerCase();
          let filtered = getCombinedItems().filter(item => state.filter === 'all' || item.bucket === state.filter);
          if (q) filtered = filtered.filter(item => [item.type, item.title, item.text, item.employee, item.date, item.due_text, item.priority_label, item.status_label].join(' ').toLowerCase().includes(q));
          filtered.sort((a,b) => {
            if (state.sort === 'newest') return dateSortValue(b) - dateSortValue(a);
            if (state.sort === 'type') return String((a.type || a.title || '')).localeCompare(String((b.type || b.title || '')), 'de');
            if (state.sort === 'employee') return String(a.employee || '').localeCompare(String(b.employee || ''), 'de');
            return dateSortValue(a) - dateSortValue(b);
          });
          if (count) count.textContent = String(filtered.length);
          if (!filtered.length) { list.innerHTML = '<div class="kb-empty-state">Keine Aktivitäten gefunden</div>'; return; }
          list.innerHTML = filtered.map(item => {
            const isReminder = item.bucket === 'reminder';
            const priority = item.priority || 'normal';
            const priorityLabel = item.priority_label || translate(DE.priority, priority, 'Normal');
            const dateLabel = isReminder ? (item.due_text || formatGermanDateTime(item.reminder_date, item.reminder_time)) : item.date;
            const title = isReminder ? (item.title || 'Erinnerung') : (item.type || 'Aktivität');
            return `
              <div class="kb-activity-item${isReminder ? ` is-reminder is-${esc(priority)}` : ''}">
                <div class="kb-activity-top"><span>${esc(item.employee || (isReminder ? 'Nicht zugewiesen' : 'System'))}</span><span>${esc(dateLabel || '')}</span></div>
                <div class="kb-activity-text"><strong>${esc(title)}</strong><br>${esc(item.text || '')}</div>
                <div class="kb-activity-meta"><span>${isReminder ? 'Nächster Schritt' : 'Aktivität'}</span>${isReminder ? `<span>Priorität: ${esc(priorityLabel)}</span>` : ''}</div>
              </div>`;
          }).join('');
        }

        function renderReminders() {
          const list = document.getElementById('kbReminderList');
          const count = document.getElementById('kbReminderCount');
          if (!list) return;
          const items = [...state.reminders].sort((a,b) => dateSortValue(a) - dateSortValue(b));
          if (count) count.textContent = String(items.length);
          if (!items.length) { list.innerHTML = '<div class="kb-empty-state">Keine Erinnerung / kein nächster Schritt</div>'; return; }
          list.innerHTML = items.map(item => {
            const priority = item.priority || 'normal';
            const priorityLabel = item.priority_label || translate(DE.priority, priority, 'Normal');
            const statusLabel = item.status_label || translate(DE.status, item.status, 'Offen');
            const due = item.due_text || formatGermanDateTime(item.reminder_date, item.reminder_time);
            return `
              <div class="kb-activity-item is-reminder is-${esc(priority)}" data-reminder-id="${esc(item.id)}">
                <div class="kb-activity-top"><span>${esc(item.employee || 'Nicht zugewiesen')}</span><span>${esc(due || 'Kein Datum')}</span></div>
                <div class="kb-activity-text"><strong>${esc(item.title || 'Erinnerung')}</strong><br>${esc(item.text || '')}</div>
                <div class="kb-activity-meta">
                  <span>Status: ${esc(statusLabel)}</span><span>Priorität: ${esc(priorityLabel)}</span>
                  ${item.id ? `<button type="button" class="lead-reminder-toast-btn" data-kb-reminder-done="${esc(item.id)}">Erledigt</button>` : ''}
                </div>
              </div>`;
          }).join('');
        }

        function openActivityModal() { document.getElementById('kbActivityModal')?.classList.add('is-open'); document.getElementById('kbActivityBackdrop')?.classList.add('is-open'); document.getElementById('kbActivityModal')?.setAttribute('aria-hidden','false'); document.body.style.overflow = 'hidden'; if (window.feather) window.feather.replace(); }
        function closeActivityModal() { document.getElementById('kbActivityModal')?.classList.remove('is-open'); document.getElementById('kbActivityBackdrop')?.classList.remove('is-open'); document.getElementById('kbActivityModal')?.setAttribute('aria-hidden','true'); document.body.style.overflow = ''; }
        function getCustomerNameFromHolder(holder) { return holder?.querySelector('.card-name, .customer-link')?.textContent?.trim() || ''; }

        function openFromElement(btn) {
          const holder = btn.closest('.card, tr.list-row-item, tr, [data-customer-id]');
          const lpl = btn.dataset.leadProductListId || holder?.dataset.leadProductListId || holder?.dataset.leadProductId || '';
          state.active = {
            lead_product_list_id: lpl, customer_id: btn.dataset.customerId || holder?.dataset.customerId || '', alternative_id: btn.dataset.alternativeId || holder?.dataset.alternativeId || '', product_id: btn.dataset.productId || holder?.dataset.productId || '',
            customer_name: btn.dataset.customerName || getCustomerNameFromHolder(holder) || 'Kunde', product_name: btn.dataset.productName || holder?.dataset.productStageName || holder?.dataset.initial || 'Produkt',
            object_text: holder?.dataset.fullAddress || [holder?.dataset.street, holder?.dataset.postcode, holder?.dataset.city].filter(Boolean).join(', ')
          };
          const form = document.getElementById('kbReminderForm'); form?.reset();
          const lplInput = document.getElementById('kb_reminder_lpl_id'); if (lplInput) lplInput.value = state.active.lead_product_list_id;
          const context = document.getElementById('kbActivityContextText'); if (context) context.textContent = [state.active.customer_name, state.active.object_text, state.active.product_name].filter(Boolean).join(' • ');
          setLoadingState(); openActivityModal(); loadContext();
        }

        function loadContext() {
          if (!state.active?.customer_id) { showKbToast('Fehler', 'Customer-ID fehlt auf der Kanban-Karte.', 'error'); return; }
          const params = new URLSearchParams({ customer_id: state.active.customer_id, alternative_id: state.active.alternative_id || '', product_id: state.active.product_id || '', lead_product_list_id: state.active.lead_product_list_id || '' });
          fetch(ENDPOINTS.context + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
            .then(async res => { const data = await res.json().catch(() => ({})); if (!res.ok) throw data; return data; })
            .then(data => {
              state.activities = normalizeActivities(data.activities || data.activity_logs || []);
              state.reminders = normalizeReminders(data.reminders || data.next_steps || [], state.active).sort((a,b) => dateSortValue(a) - dateSortValue(b));
              renderActivities(); renderReminders();
              const card = findKanbanCard(state.active); if (card) renderCardReminderSummary(card, state.reminders, state.activities.length);
            })
            .catch(err => { const msg = err?.message || 'Aktivitäten konnten nicht geladen werden. Prüfe die Route kanban.reminders.context.'; document.getElementById('kbActivityList').innerHTML = `<div class="kb-empty-state">${esc(msg)}</div>`; document.getElementById('kbReminderList').innerHTML = '<div class="kb-empty-state">Erinnerungen konnten nicht geladen werden.</div>'; });
        }

        function submitReminderForm(e) {
          e.preventDefault(); if (!state.active) return;
          const form = e.currentTarget; const btn = form.querySelector('button[type="submit"]'); if (btn) btn.disabled = true;
          fetch(ENDPOINTS.store, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf(), 'Accept':'application/json' }, body:new FormData(form) })
            .then(async res => { const data = await res.json().catch(() => ({})); if (!res.ok) throw data; return data; })
            .then(data => { form.reset(); const lplInput = document.getElementById('kb_reminder_lpl_id'); if (lplInput) lplInput.value = state.active.lead_product_list_id; showKbToast('Erinnerung gespeichert', data.message || 'Der nächste Schritt wurde gespeichert.', 'success'); loadContext(); document.querySelectorAll('.card').forEach(c => delete c.dataset.kbSummaryLoaded); preloadCardSummaries(); })
            .catch(err => { const msg = err?.message || (err?.errors ? Object.values(err.errors || {}).flat().join('\n') : 'Serverfehler beim Speichern.'); showKbToast('Fehler', msg, 'error'); })
            .finally(() => { if (btn) btn.disabled = false; });
        }

        function markReminderDone(id, toast, reloadContext) {
          if (!id) return;
          fetch(`${ENDPOINTS.doneBase}/${encodeURIComponent(id)}/done`, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf(), 'Accept':'application/json' } })
            .then(() => { if (toast) toast.remove(); showKbToast('Erledigt', 'Erinnerung wurde erledigt.', 'success'); if (reloadContext) loadContext(); document.querySelectorAll('.card').forEach(c => delete c.dataset.kbSummaryLoaded); preloadCardSummaries(); })
            .catch(() => showKbToast('Fehler', 'Erinnerung konnte nicht erledigt werden.', 'error'));
        }

        function focusKanbanCard(item) {
          const ctx = { lead_product_list_id: item.lead_product_list_id || item.lead_product_id || '', customer_id: item.customer_id || item.customer?.id || '', alternative_id: item.alternative_id || '', product_id: item.product_id || item.product?.id || '' };
          const card = findKanbanCard(ctx);
          if (card) { card.scrollIntoView({ behavior:'smooth', block:'center', inline:'center' }); card.classList.add('kanban-card-highlight-reminder'); window.setTimeout(() => card.classList.remove('kanban-card-highlight-reminder'), 7000); return; }
          const url = new URL(window.location.href); if (ctx.customer_id) url.searchParams.set('customer_id', ctx.customer_id); if (ctx.alternative_id) url.searchParams.set('alternative_id', ctx.alternative_id); if (ctx.product_id) url.searchParams.set('product_id', ctx.product_id); if (ctx.lead_product_list_id) url.searchParams.set('lead_product_list_id', ctx.lead_product_list_id); window.location.href = url.toString();
        }

        function showLeadReminderToast(item) {
          const wrap = document.getElementById('leadReminderToastWrap'); if (!wrap) return;
          const toast = document.createElement('div'); const priority = String(item.priority || item.type || 'success').toLowerCase(); toast.className = 'lead-reminder-toast ' + esc(priority); toast.dataset.reminderId = item.id || '';
          const customerName = item.customer ? ((item.customer.firma || '') || `${item.customer.name || ''} ${item.customer.lastname || ''}`.trim()) : (item.customer_name || 'Kunde');
          const productName = item.product ? (item.product.article_group || item.product.initial || '') : (item.product_name || '');
          const due = item.due_text || formatGermanDateTime(item.reminder_date || item.due_date || '', item.reminder_time || item.due_time || '');
          toast.innerHTML = `<div class="lead-reminder-toast-head"><span><i class="feather icon-bell"></i> ${esc(item.title || 'Erinnerung')}</span><button type="button" class="lead-reminder-toast-close" aria-label="Schließen">×</button></div><div class="lead-reminder-toast-body"><strong>${esc(customerName)}</strong><br>${productName ? `${esc(productName)}<br>` : ''}${due ? `<small><strong>Fällig:</strong> ${esc(due)}</small><br>` : ''}<small>${esc(item.description || item.message || '')}</small></div><div class="lead-reminder-toast-actions"><button type="button" class="lead-reminder-toast-btn" data-reminder-focus>Kanban anzeigen</button>${item.id ? `<button type="button" class="lead-reminder-toast-btn" data-reminder-done>Erledigt</button>` : ''}</div>`;
          toast.addEventListener('click', e => { if (e.target.closest('.lead-reminder-toast-close')) { e.preventDefault(); toast.remove(); return; } if (e.target.closest('[data-reminder-done]')) { e.preventDefault(); markReminderDone(item.id, toast, false); return; } focusKanbanCard(item); toast.remove(); });
          wrap.prepend(toast); if (window.feather) window.feather.replace(); window.setTimeout(() => toast.parentNode && toast.remove(), 20000);
        }
        function showKbToast(title, message, type) { const priority = type === 'error' ? 'critical' : (type === 'warning' ? 'important' : 'success'); showLeadReminderToast({ title, description: message, priority }); }
        function checkDueLeadReminders() { fetch(ENDPOINTS.due, { headers:{ 'Accept':'application/json' } }).then(res => res.json()).then(data => (data.items || []).forEach(showLeadReminderToast)).catch(() => {}); }

        function bootLeadActivityAddon() {
          if (state.booted) return; state.booted = true;
          const modal = document.getElementById('kbActivityModal'); const backdrop = document.getElementById('kbActivityBackdrop'); const closeBtn = document.getElementById('kbActivityCloseBtn'); const form = document.getElementById('kbReminderForm'); if (!modal || !backdrop || !form) return;
          document.addEventListener('click', function(e) {
            const carouselBtn = e.target.closest('[data-kb-reminder-prev], [data-kb-reminder-next]');
            if (carouselBtn) { e.preventDefault(); e.stopPropagation(); const summary = carouselBtn.closest('.kb-reminder-summary'); if (summary) moveCardCarousel(summary, carouselBtn.hasAttribute('data-kb-reminder-next') ? 1 : -1); return; }
            const doneBtn = e.target.closest('[data-kb-reminder-done]'); if (doneBtn) { e.preventDefault(); e.stopPropagation(); markReminderDone(doneBtn.dataset.kbReminderDone, null, true); return; }
            const btn = e.target.closest('[data-open-lead-reminder], .kb-open-activity-panel'); if (btn) { e.preventDefault(); e.stopPropagation(); openFromElement(btn); }
          }, true);
          closeBtn?.addEventListener('click', closeActivityModal); backdrop?.addEventListener('click', closeActivityModal); document.addEventListener('keydown', e => { if (e.key === 'Escape') closeActivityModal(); });
          document.querySelectorAll('[data-kb-activity-filter]').forEach(btn => btn.addEventListener('click', function(){ state.filter = this.dataset.kbActivityFilter || 'all'; document.querySelectorAll('[data-kb-activity-filter]').forEach(b => b.classList.remove('is-active')); this.classList.add('is-active'); renderActivities(); }));
          document.getElementById('kbActivitySearch')?.addEventListener('input', function(){ state.search = this.value || ''; renderActivities(); });
          document.getElementById('kbActivitySort')?.addEventListener('change', function(){ state.sort = this.value || 'oldest'; renderActivities(); });
          form.addEventListener('submit', submitReminderForm);
          window.openLeadActivityPanel = openFromElement; window.checkDueLeadReminders = checkDueLeadReminders; window.preloadLeadReminderSummaries = preloadCardSummaries;
          preloadCardSummaries(); window.setTimeout(preloadCardSummaries, 800); window.setTimeout(preloadCardSummaries, 2000);
          const mo = new MutationObserver(() => window.setTimeout(preloadCardSummaries, 250)); mo.observe(document.body, { childList:true, subtree:true });
          if (!state.pollingStarted) { state.pollingStarted = true; checkDueLeadReminders(); window.setInterval(checkDueLeadReminders, 30000); }
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bootLeadActivityAddon); else bootLeadActivityAddon();
      })();

      </script>

      @if($canManageKanbanLeadStages)
        <script>
            (function () {
                'use strict';

                if (window.__KANBAN_LEAD_STAGE_SUB_STAGE_ADMIN__) return;
                window.__KANBAN_LEAD_STAGE_SUB_STAGE_ADMIN__ = true;

                const base = '{{ url('/task-phase/ajax/stage-admin') }}';
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const qs = (s, r = document) => r.querySelector(s);
                const qsa = (s, r = document) => Array.from(r.querySelectorAll(s));
                const modal = qs('#kanbanStageAdminModal');
                const list = qs('#kbsaStageList');
                const err = qs('#kbsaError');

                function esc(v) {
                    return String(v ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function showError(message) {
                    if (!err) return;
                    err.textContent = message || 'Fehler';
                    err.classList.add('is-visible');
                }

                function clearError() {
                    if (!err) return;
                    err.textContent = '';
                    err.classList.remove('is-visible');
                }

                function notify(type, message) {
                    if (window.toastr && toastr[type]) {
                        toastr[type](message);
                        return;
                    }
                    if (window.Swal && type === 'success') {
                        Swal.fire({ icon: 'success', title: message, timer: 900, showConfirmButton: false });
                        return;
                    }
                    console[type === 'error' ? 'error' : 'log'](message);
                }

                function apiMessage(payload, fallback) {
                    if (!payload) return fallback || 'Fehler';
                    if (payload.message) return payload.message;
                    if (payload.errors) return Object.values(payload.errors).flat().join('\n');
                    return fallback || 'Fehler';
                }

                function refreshIcons() {
                    if (window.feather) {
                        window.requestAnimationFrame(() => feather.replace());
                    }
                }

                function refreshKanbanAfterStageChange() {
                    if (typeof window.LeadUIFetchKanban === 'function') {
                        window.LeadUIFetchKanban(window.State?.filtersQS || '');
                        return;
                    }

                    if (typeof window.fetchKanbanView === 'function') {
                        window.fetchKanbanView(window.State?.filtersQS || '');
                        return;
                    }

                    if (typeof window.loadKanban === 'function') {
                        window.loadKanban();
                        return;
                    }
                }

                function openModal() {
                    if (!modal) return;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    loadStages();
                    refreshIcons();
                }

                function closeModal() {
                    if (!modal) return;
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = '';
                }

                async function requestJson(url, options = {}) {
                    const response = await fetch(url, {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token,
                            ...(options.headers || {})
                        },
                        ...options
                    });

                    const raw = await response.text();
                    let data = {};
                    try {
                        data = raw ? JSON.parse(raw) : {};
                    } catch (e) {
                        data = { message: raw || 'Ungültige Serverantwort.' };
                    }

                    if (!response.ok || data.success === false) {
                        const error = new Error(apiMessage(data, 'Anfrage fehlgeschlagen.'));
                        error.payload = data;
                        throw error;
                    }

                    return data;
                }

                function postJson(url, data = {}, method = 'POST') {
                    return requestJson(url, {
                        method,
                        body: JSON.stringify(data)
                    });
                }

                function stageItems() {
                    return qsa('#kbsaStageList > .kbsa-stage')
                        .map(el => parseInt(el.dataset.stageId, 10))
                        .filter(Boolean);
                }

                function subStageItems(stageEl) {
                    return qsa('.js-kbsa-sub-list > .kbsa-sub', stageEl)
                        .map(el => parseInt(el.dataset.subId, 10))
                        .filter(Boolean);
                }

                function renderStages(stages) {
                    if (!list) return;

                    if (!Array.isArray(stages) || !stages.length) {
                        list.innerHTML = '<div class="kbsa-small">Keine LeadStages vorhanden.</div>';
                        return;
                    }

                    list.innerHTML = stages.map(stage => `
                        <div class="kbsa-stage" data-stage-id="${stage.id}">
                            <div class="kbsa-stage-head">
                                <span class="kbsa-handle" title="Ziehen zum Sortieren"><i class="feather icon-menu"></i></span>
                                <input class="kbsa-input js-kbsa-stage-name" value="${esc(stage.name)}" placeholder="Name">
                                <input class="kbsa-input js-kbsa-stage-key" value="${esc(stage.key)}" placeholder="Key">
                                <input class="kbsa-input js-kbsa-stage-color" type="color" value="${esc(stage.color || '#93c21c')}">
                                <input class="kbsa-input js-kbsa-stage-icon" value="${esc(stage.icon || 'columns')}" placeholder="Icon">
                                <label class="kbsa-check"><input type="checkbox" class="js-kbsa-stage-active" ${stage.is_active ? 'checked' : ''}> Aktiv</label>
                                <span class="kbsa-usage">${Number(stage.usage_count || 0)} Einträge</span>
                                <div style="display:flex;gap:6px;justify-content:flex-end;">
                                    <button type="button" class="kbsa-btn-soft js-kbsa-stage-save"><i class="feather icon-save"></i></button>
                                    <button type="button" class="kbsa-btn-danger js-kbsa-stage-delete"><i class="feather icon-trash"></i></button>
                                </div>
                            </div>

                            <div class="kbsa-sub-list">
                                <div class="kbsa-toolbar" style="grid-template-columns:minmax(160px,1fr) 130px 90px 90px auto; margin-bottom:10px;">
                                    <input class="kbsa-input js-kbsa-sub-name" placeholder="Neue SubStage">
                                    <input class="kbsa-input js-kbsa-sub-key" placeholder="Key auto">
                                    <input class="kbsa-input js-kbsa-sub-color" type="color" value="${esc(stage.color || '#93c21c')}">
                                    <input class="kbsa-input js-kbsa-sub-icon" value="list">
                                    <button type="button" class="kbsa-btn js-kbsa-sub-create">
                                        <i class="feather icon-plus"></i> SubStage
                                    </button>
                                </div>

                                <div class="js-kbsa-sub-list">
                                    ${(stage.sub_stages || []).map(sub => `
                                        <div class="kbsa-sub" data-sub-id="${sub.id}">
                                            <span class="kbsa-handle" title="Ziehen zum Sortieren"><i class="feather icon-menu"></i></span>
                                            <input class="kbsa-input js-kbsa-sub-edit-name" value="${esc(sub.name)}">
                                            <input class="kbsa-input js-kbsa-sub-edit-key" value="${esc(sub.key)}">
                                            <input class="kbsa-input js-kbsa-sub-edit-color" type="color" value="${esc(sub.color || stage.color || '#93c21c')}">
                                            <input class="kbsa-input js-kbsa-sub-edit-icon" value="${esc(sub.icon || 'list')}">
                                            <label class="kbsa-check"><input type="checkbox" class="js-kbsa-sub-edit-active" ${sub.is_active ? 'checked' : ''}> Aktiv</label>
                                            <span class="kbsa-usage">${Number(sub.usage_count || 0)} Einträge</span>
                                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                                <button type="button" class="kbsa-btn-soft js-kbsa-sub-save"><i class="feather icon-save"></i></button>
                                                <button type="button" class="kbsa-btn-danger js-kbsa-sub-delete"><i class="feather icon-trash"></i></button>
                                            </div>
                                        </div>
                                    `).join('') || '<div class="kbsa-small">Keine SubStages.</div>'}
                                </div>
                            </div>
                        </div>
                    `).join('');

                    initSortables();
                    refreshIcons();
                }

                async function loadStages() {
                    clearError();

                    if (list) {
                        list.innerHTML = '<div class="kbsa-small">Lade LeadStages...</div>';
                    }

                    try {
                        const data = await requestJson(base + '/stages', { method: 'GET' });
                        renderStages(data.stages || data.data || []);
                    } catch (error) {
                        showError(error.message || 'LeadStages konnten nicht geladen werden.');
                    }
                }

                function initSortables() {
                    if (!window.jQuery || !jQuery.fn.sortable) return;

                    const stageList = jQuery('#kbsaStageList');

                    if (stageList.data('ui-sortable')) {
                        stageList.sortable('destroy');
                    }

                    stageList.sortable({
                        items: '> .kbsa-stage',
                        handle: '.kbsa-handle',
                        placeholder: 'kbsa-stage',
                        stop: function () {
                            postJson(base + '/stages/reorder', { items: stageItems() })
                                .then(() => {
                                    notify('success', 'Phasen-Reihenfolge gespeichert.');
                                    refreshKanbanAfterStageChange();
                                })
                                .catch(error => showError(error.message || 'Phasen-Sortierung fehlgeschlagen.'));
                        }
                    });

                    jQuery('.js-kbsa-sub-list').each(function () {
                        const subList = jQuery(this);

                        if (subList.data('ui-sortable')) {
                            subList.sortable('destroy');
                        }

                        subList.sortable({
                            items: '> .kbsa-sub',
                            handle: '.kbsa-handle',
                            placeholder: 'kbsa-sub',
                            stop: function () {
                                const stageEl = subList.closest('.kbsa-stage')[0];

                                postJson(base + '/stages/' + stageEl.dataset.stageId + '/sub-stages/reorder', {
                                    items: subStageItems(stageEl)
                                })
                                    .then(() => {
                                        notify('success', 'SubStage-Reihenfolge gespeichert.');
                                        refreshKanbanAfterStageChange();
                                    })
                                    .catch(error => showError(error.message || 'SubStage-Sortierung fehlgeschlagen.'));
                            }
                        });
                    });
                }

                qsa('.kb-stage-admin-open, #btnOpenKanbanStageAdmin').forEach(button => {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        openModal();
                    });
                });

                qsa('[data-kbsa-close]').forEach(button => button.addEventListener('click', closeModal));

                modal?.addEventListener('click', function (event) {
                    if (event.target === modal) closeModal();
                });

                qs('#kbsaReloadStages')?.addEventListener('click', loadStages);

                qs('#kbsaCreateStage')?.addEventListener('click', async function () {
                    clearError();

                    try {
                        await postJson(base + '/stages', {
                            name: qs('#kbsaStageName')?.value || '',
                            key: qs('#kbsaStageKey')?.value || '',
                            color: qs('#kbsaStageColor')?.value || '#93c21c',
                            icon: qs('#kbsaStageIcon')?.value || 'columns',
                            is_active: qs('#kbsaStageActive')?.checked ? 1 : 0
                        });

                        qs('#kbsaStageName').value = '';
                        qs('#kbsaStageKey').value = '';
                        notify('success', 'Phase erstellt.');
                        await loadStages();
                        refreshKanbanAfterStageChange();
                    } catch (error) {
                        showError(error.message || 'Phase konnte nicht erstellt werden.');
                    }
                });

                document.addEventListener('click', async function (event) {
                    const stageEl = event.target.closest('.kbsa-stage');
                    const subEl = event.target.closest('.kbsa-sub');

                    if (event.target.closest('.js-kbsa-stage-save') && stageEl) {
                        clearError();

                        try {
                            await postJson(base + '/stages/' + stageEl.dataset.stageId + '/update', {
                                name: qs('.js-kbsa-stage-name', stageEl).value,
                                key: qs('.js-kbsa-stage-key', stageEl).value,
                                color: qs('.js-kbsa-stage-color', stageEl).value,
                                icon: qs('.js-kbsa-stage-icon', stageEl).value || 'columns',
                                is_active: qs('.js-kbsa-stage-active', stageEl).checked ? 1 : 0
                            });

                            notify('success', 'Phase gespeichert.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'Phase konnte nicht gespeichert werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-stage-delete') && stageEl) {
                        const ask = window.Swal
                            ? await Swal.fire({
                                icon: 'warning',
                                title: 'Phase löschen?',
                                text: 'Diese Aktion kann nicht rückgängig gemacht werden.',
                                showCancelButton: true,
                                confirmButtonText: 'Löschen',
                                cancelButtonText: 'Abbrechen',
                                confirmButtonColor: '#ef4444'
                            })
                            : { isConfirmed: confirm('Phase löschen?') };

                        if (!ask.isConfirmed) return;

                        try {
                            await postJson(base + '/stages/' + stageEl.dataset.stageId, {}, 'DELETE');
                            notify('success', 'Phase gelöscht.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'Phase konnte nicht gelöscht werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-sub-create') && stageEl) {
                        clearError();

                        try {
                            await postJson(base + '/stages/' + stageEl.dataset.stageId + '/sub-stages', {
                                lead_stage_id: parseInt(stageEl.dataset.stageId, 10),
                                name: qs('.js-kbsa-sub-name', stageEl).value,
                                key: qs('.js-kbsa-sub-key', stageEl).value,
                                color: qs('.js-kbsa-sub-color', stageEl).value,
                                icon: qs('.js-kbsa-sub-icon', stageEl).value || 'list',
                                is_active: 1
                            });

                            notify('success', 'SubStage erstellt.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'SubStage konnte nicht erstellt werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-sub-save') && subEl && stageEl) {
                        clearError();

                        try {
                            await postJson(base + '/sub-stages/' + subEl.dataset.subId + '/update', {
                                lead_stage_id: parseInt(stageEl.dataset.stageId, 10),
                                name: qs('.js-kbsa-sub-edit-name', subEl).value,
                                key: qs('.js-kbsa-sub-edit-key', subEl).value,
                                color: qs('.js-kbsa-sub-edit-color', subEl).value,
                                icon: qs('.js-kbsa-sub-edit-icon', subEl).value || 'list',
                                is_active: qs('.js-kbsa-sub-edit-active', subEl).checked ? 1 : 0
                            });

                            notify('success', 'SubStage gespeichert.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'SubStage konnte nicht gespeichert werden.');
                        }
                    }

                    if (event.target.closest('.js-kbsa-sub-delete') && subEl) {
                        const ask = window.Swal
                            ? await Swal.fire({
                                icon: 'warning',
                                title: 'SubStage löschen?',
                                text: 'Diese Aktion kann nicht rückgängig gemacht werden.',
                                showCancelButton: true,
                                confirmButtonText: 'Löschen',
                                cancelButtonText: 'Abbrechen',
                                confirmButtonColor: '#ef4444'
                            })
                            : { isConfirmed: confirm('SubStage löschen?') };

                        if (!ask.isConfirmed) return;

                        try {
                            await postJson(base + '/sub-stages/' + subEl.dataset.subId, {}, 'DELETE');
                            notify('success', 'SubStage gelöscht.');
                            await loadStages();
                            refreshKanbanAfterStageChange();
                        } catch (error) {
                            showError(error.message || 'SubStage konnte nicht gelöscht werden.');
                        }
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
                        closeModal();
                    }
                });
            })();
        </script>
      @endif



    <style>
      /* ===== Kanban next-step auto preload + overdue highlight + compact analytics ===== */
      .kb-next-step-preview.kb-has-next-step {
        border-color: #bfdbfe;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
      }
      .kb-next-step-preview.kb-overdue,
      .card.kb-task-overdue-card .kb-next-step-preview,
      tr.kb-task-overdue-row .kb-status {
        border-color: #ef4444 !important;
        background: linear-gradient(135deg, #fff7f7 0%, #ffffff 100%) !important;
        animation: kbTaskCardOverduePulse 1.25s infinite;
      }
      .card.kb-task-overdue-card {
        border-left-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, .15), 0 8px 22px rgba(15, 23, 42, .12);
      }
      .card.kb-task-overdue-card::after {
        content: "Überfällige Aufgabe";
        position: absolute;
        top: 34px;
        right: 8px;
        z-index: 6;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 10px;
        font-weight: 900;
        padding: 3px 7px;
        box-shadow: 0 8px 16px rgba(239, 68, 68, .25);
        pointer-events: none;
      }
      @keyframes kbTaskCardOverduePulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, .24); }
        70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
      }
      .kb-list-next-step-status {
        border: 1px solid #dbeafe;
        border-radius: 14px;
        padding: 8px 9px;
        background: #ffffff;
        min-width: 230px;
        max-width: 320px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        cursor: pointer;
      }
      .kb-list-next-step-status .kb-list-next-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 7px;
        margin-bottom: 6px;
      }
      .kb-list-next-step-status .kb-list-next-title {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        font-size: 12px;
        font-weight: 900;
        color: #0f172a;
      }
      .kb-list-next-step-status .kb-list-next-title span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
      .kb-list-next-step-status .kb-list-next-meta {
        display: grid;
        grid-template-columns: 16px minmax(0, 1fr);
        gap: 4px 6px;
        color: #475569;
        font-size: 11px;
        line-height: 1.25;
      }
      .kb-list-next-step-status .kb-list-next-meta .feather {
        width: 14px;
        height: 14px;
        color: #74b2d4;
      }
      .kb-list-next-step-status .kb-list-next-counts {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        margin-top: 6px;
      }
      .kb-list-next-step-status .kb-mini-pill {
        border-radius: 999px;
        padding: 3px 7px;
        font-size: 10px;
        font-weight: 900;
        background: #f1f5f9;
        color: #334155;
      }
      .kb-list-next-step-status .kb-mini-pill.red {
        background: #fee2e2;
        color: #991b1b;
      }
      .kb-column-analytics-toggle {
        border: 1px solid rgba(255,255,255,.35);
        background: rgba(255,255,255,.18);
        color: #fff;
        border-radius: 999px;
        height: 24px;
        min-width: 28px;
        padding: 0 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 900;
        cursor: pointer;
      }
      .kb-column-analytics-toggle:hover { background: rgba(255,255,255,.30); }
      .column.kb-analytics-hidden,
      .kanban-zoom-card.kb-compact .column.kb-analytics-hidden {
        width: 292px;
        min-width: 292px;
        flex-basis: 292px;
      }
      .column.kb-analytics-hidden .kb-header-counts,
      .column.kb-analytics-hidden .kb-column-substage-wrap,
      .column.kb-analytics-hidden .kb-understage-btn span,
      .column.kb-analytics-hidden .kb-understage-btn b {
        display: none !important;
      }
      .column.kb-analytics-hidden .kb-column-actions { padding: 4px 5px 5px; }
    </style>


    <script>
      /* =========================================================
         Auto-load Kanban next-step preview on board/list load
         - Uses the same task context endpoint as the modal
         - Updates card preview, list status, badges, overdue animation/toast
         - Adds compact analytics toggle to each Kanban column
         ========================================================= */
      (function () {
        'use strict';

        if (window.__kanbanNextStepAutoPreviewBooted) return;
        window.__kanbanNextStepAutoPreviewBooted = true;

        const CONTEXT_URL = (leadProductId) => `{{ url('/admin/kanban/tasks/context') }}/${encodeURIComponent(leadProductId)}`;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const loaded = new Map();
        const warned = new Set();
        let queueRunning = false;

        const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({
          '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m]));

        const cssEscape = (value) => window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\$&');

        function parseDate(value) {
          if (!value) return null;
          const normalized = String(value).includes('T') ? String(value) : String(value).replace(' ', 'T');
          const date = new Date(normalized);
          return Number.isNaN(date.getTime()) ? null : date;
        }

        function formatDateTimeDE(value) {
          const date = parseDate(value);
          if (!date) return '-';
          return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: '2-digit' }) + ' ' +
            date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        }

        function statusLabel(status) {
          return ({ open: 'Offen', scheduled: 'Geplant', in_progress: 'In Bearbeitung', done: 'Erledigt', cancelled: 'Abgebrochen' }[status] || status || 'Offen');
        }

        function isTaskOverdue(task) {
          const status = String(task?.status || '').toLowerCase();
          if (['done', 'cancelled'].includes(status)) return false;
          if (task?.is_overdue) return true;
          const end = parseDate(task?.planned_end_at || task?.due_at || task?.due_date);
          return !!(end && end.getTime() < Date.now());
        }

        function templateTitleFromContext(data) {
          const templates = Array.isArray(data?.templates) ? data.templates : [];
          for (const phase of templates) {
            const activities = Array.isArray(phase?.activities) ? phase.activities : [];
            const firstActivity = activities.find(a => a && (a.title || a.description));
            if (firstActivity) {
              return {
                title: firstActivity.title || phase.phase_name || 'Nächste Aufgabe',
                description: firstActivity.description || phase.description || '',
                estimated_minutes: firstActivity.estimated_minutes || null,
                photo_required: !!firstActivity.photo_required,
                source: 'task_phase'
              };
            }
            if (phase?.phase_name) {
              return {
                title: phase.phase_name,
                description: phase.description || '',
                estimated_minutes: null,
                photo_required: false,
                source: 'task_phase'
              };
            }
          }
          return null;
        }

        function summarizeContext(data, fallback = {}) {
          const tasks = Array.isArray(data?.tasks) ? data.tasks : [];
          const openTasks = tasks.filter(t => !['done', 'cancelled'].includes(String(t?.status || '').toLowerCase()));
          const doneTasks = tasks.filter(t => String(t?.status || '').toLowerCase() === 'done');
          const current = openTasks[0] || null;
          const previous = doneTasks.slice().sort((a, b) => String(b.done_at || b.updated_at || '').localeCompare(String(a.done_at || a.updated_at || '')))[0] || null;
          const template = current ? null : templateTitleFromContext(data);
          const next = current || template || null;
          const overdueTasks = openTasks.filter(isTaskOverdue);
          const ctx = data?.context || {};

          return {
            lead_product_list_id: ctx.lead_product_list_id || fallback.leadProductId || '',
            customer_name: ctx.customer_name || fallback.customerName || '',
            product_name: ctx.product_name || fallback.productName || '',
            stage_label: ctx.stage_label || fallback.stageLabel || '',
            sub_stage_label: ctx.sub_stage_label || fallback.subStageLabel || '',
            title: next?.title || 'Noch keine Aufgabe',
            description: next?.description || '',
            previous_title: previous?.title || '-',
            open_count: openTasks.length,
            done_count: doneTasks.length,
            estimated_minutes: next?.estimated_minutes || null,
            photo_required: !!next?.photo_required,
            status: current?.status || (template ? 'open' : ''),
            planned_start_at: current?.planned_start_at || null,
            planned_end_at: current?.planned_end_at || null,
            overdue: overdueTasks.length > 0,
            overdue_count: overdueTasks.length,
            source: current ? 'saved_task' : (template ? 'task_phase' : null),
            stage_landed_at: fallback.stageLandedAt || fallback.updatedAt || fallback.createdAt || null
          };
        }

        async function fetchContext(leadProductId) {
          if (!leadProductId) return null;
          if (loaded.has(String(leadProductId))) return loaded.get(String(leadProductId));
          const promise = fetch(CONTEXT_URL(leadProductId), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
          }).then(async (res) => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok || data.success === false) throw new Error(data.message || 'Aufgaben konnten nicht geladen werden.');
            return data;
          }).catch((error) => {
            console.warn('[Kanban next-step preload]', leadProductId, error);
            return null;
          });
          loaded.set(String(leadProductId), promise);
          return promise;
        }

        function previewHtml(summary, leadProductId) {
          return `
            <div class="kb-next-step-preview ${summary.source ? 'kb-has-next-step' : ''} ${summary.overdue ? 'kb-overdue' : ''}">
              <div class="kb-next-step-preview-head">
                <span><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</span>
                <button type="button" class="kb-next-step-preview-btn" data-open-kanban-task-management data-lead-product-list-id="${esc(leadProductId)}">Details</button>
              </div>
              <div class="kb-next-step-preview-line"><i class="feather icon-log-in"></i><span>Seit: <strong>${esc(formatDateTimeDE(summary.stage_landed_at))}</strong></span></div>
              <div class="kb-next-step-preview-line"><i class="feather icon-check-circle"></i><span>Vorher: <strong>${esc(summary.previous_title || '-')}</strong></span></div>
              <div class="kb-next-step-preview-line"><i class="feather icon-list"></i><span><strong>${esc(summary.title || 'Noch keine Aufgabe')}</strong></span></div>
              ${summary.planned_end_at ? `<div class="kb-next-step-preview-line"><i class="feather icon-clock"></i><span>Fällig: <strong>${esc(formatDateTimeDE(summary.planned_end_at))}</strong></span></div>` : ''}
              <div class="kb-next-step-preview-line"><i class="feather icon-activity"></i><span>Offen: <strong>${esc(summary.open_count)}</strong> · Erledigt: <strong>${esc(summary.done_count)}</strong>${summary.overdue ? ` · <strong class="text-danger">Überfällig: ${esc(summary.overdue_count)}</strong>` : ''}</span></div>
            </div>`;
        }

        function listStatusHtml(row, summary) {
          const stage = row?.dataset?.stage || summary.stage_label || 'lead';
          const status = summary.overdue ? 'Überfällig' : (summary.status ? statusLabel(summary.status) : 'Offen');
          return `
            <div class="kb-status kb-list-next-step-status ${summary.overdue ? 'kb-overdue' : ''}" title="Nächster Schritt">
              <div class="kb-list-next-head">
                <span class="badge ${summary.overdue ? 'badge-danger bg-danger' : 'badge-primary bg-primary'}">${esc(status)}</span>
                <button type="button" class="kb-next-step-preview-btn" data-open-kanban-task-management data-lead-product-list-id="${esc(summary.lead_product_list_id || row?.dataset?.leadProductId || '')}">Details</button>
              </div>
              <div class="kb-list-next-title"><i class="feather icon-arrow-right-circle"></i><span>${esc(summary.title || 'Noch keine Aufgabe')}</span></div>
              <div class="kb-list-next-meta mt-1">
                <i class="feather icon-layers"></i><span>Phase: <strong>${esc(summary.stage_label || stage)}</strong>${summary.sub_stage_label ? ' / ' + esc(summary.sub_stage_label) : ''}</span>
                <i class="feather icon-log-in"></i><span>Seit: <strong>${esc(formatDateTimeDE(summary.stage_landed_at))}</strong></span>
                <i class="feather icon-check-circle"></i><span>Vorher: <strong>${esc(summary.previous_title || '-')}</strong></span>
                ${summary.planned_end_at ? `<i class="feather icon-clock"></i><span>Fällig: <strong>${esc(formatDateTimeDE(summary.planned_end_at))}</strong></span>` : ''}
              </div>
              <div class="kb-list-next-counts">
                <span class="kb-mini-pill">Offen: ${esc(summary.open_count)}</span>
                <span class="kb-mini-pill">Erledigt: ${esc(summary.done_count)}</span>
                ${summary.overdue ? `<span class="kb-mini-pill red">Überfällig: ${esc(summary.overdue_count)}</span>` : ''}
              </div>
            </div>`;
        }

        function updateBadge(root, summary) {
          const badge = root.querySelector('[data-kanban-task-count]');
          if (!badge) return;
          const count = Number(summary.open_count || 0);
          badge.textContent = count > 99 ? '99+' : String(count);
          badge.classList.toggle('d-none', count <= 0);
          badge.style.display = count > 0 ? '' : 'none';
        }

        function warnOverdueOnce(summary) {
          if (!summary.overdue || !summary.lead_product_list_id) return;
          const key = String(summary.lead_product_list_id);
          if (warned.has(key) || sessionStorage.getItem('kb_overdue_warned_' + key)) return;
          warned.add(key);
          sessionStorage.setItem('kb_overdue_warned_' + key, '1');
          const message = `Überfällige Aufgabe: ${summary.customer_name || 'Lead'}${summary.product_name ? ' · ' + summary.product_name : ''} — ${summary.title || ''}`;
          if (window.toastr) window.toastr.warning(message, 'Kanban Aufgabe überfällig');
          else if (window.Swal) Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: message, showConfirmButton: false, timer: 5500 });
          else console.warn(message);
        }

        function updateCard(card, summary) {
          if (!card) return;
          const leadProductId = card.dataset.leadProductListId || card.dataset.leadProductId || summary.lead_product_list_id || '';
          const old = card.querySelector('.kb-next-step-preview');
          const html = previewHtml(summary, leadProductId);
          if (old) old.outerHTML = html;
          else {
            const before = card.querySelector('.employeeList') || card.querySelector('.card-actions');
            if (before) before.insertAdjacentHTML('beforebegin', html);
          }
          card.classList.toggle('kb-task-overdue-card', !!summary.overdue);
          updateBadge(card, summary);
          warnOverdueOnce(summary);
        }

        function updateRow(row, summary) {
          if (!row) return;
          const statusCell = row.querySelector('td:nth-child(6)') || row.querySelector('td');
          const old = row.querySelector('.kb-status');
          const html = listStatusHtml(row, summary);
          if (old) old.outerHTML = html;
          else if (statusCell) statusCell.insertAdjacentHTML('afterbegin', html);
          row.classList.toggle('kb-task-overdue-row', !!summary.overdue);
          warnOverdueOnce(summary);
        }

        function fallbackFromElement(el) {
          return {
            leadProductId: el.dataset.leadProductListId || el.dataset.leadProductId || '',
            customerName: el.dataset.customerName || el.querySelector?.('.card-name')?.textContent?.trim() || el.querySelector?.('.customer-link')?.textContent?.trim() || '',
            productName: el.dataset.productName || el.dataset.initial || '',
            stageLabel: el.dataset.stage || el.dataset.companyStage || '',
            stageLandedAt: el.dataset.stageLandedAt || el.dataset.updatedAt || el.dataset.createdAt || '',
            updatedAt: el.dataset.updatedAt || '',
            createdAt: el.dataset.createdAt || ''
          };
        }

        async function hydrateElement(el) {
          const id = el.dataset.leadProductListId || el.dataset.leadProductId || '';
          if (!id || el.dataset.nextStepAutoLoaded === '1') return;
          el.dataset.nextStepAutoLoaded = '1';
          const data = await fetchContext(id);
          if (!data) return;
          const summary = summarizeContext(data, fallbackFromElement(el));
          if (el.matches('tr.list-row-item')) updateRow(el, summary);
          else updateCard(el, summary);
          if (window.feather) window.feather.replace();
        }

        function hydrateVisibleNextSteps(root = document) {
          const nodes = Array.from(root.querySelectorAll('.card[data-lead-product-list-id], .card[data-lead-product-id], tr.list-row-item[data-lead-product-id], tr.list-row-item[data-lead-product-list-id]'));
          const todo = nodes.filter(el => el.dataset.nextStepAutoLoaded !== '1');
          if (!todo.length || queueRunning) return;
          queueRunning = true;
          let index = 0;
          const CONCURRENCY = 3;
          const worker = async () => {
            while (index < todo.length) {
              const el = todo[index++];
              await hydrateElement(el);
            }
          };
          Promise.all(Array.from({ length: CONCURRENCY }, worker)).finally(() => { queueRunning = false; });
        }

        function installAnalyticsToggles(root = document) {
          root.querySelectorAll('.column').forEach((column) => {
            const h3 = column.querySelector('h3');
            const actions = h3?.querySelector('.kb-column-actions');
            if (!h3 || !actions || actions.querySelector('[data-kb-toggle-analytics]')) return;
            const key = h3.dataset.workflowStageKey || column.id || 'stage';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kb-column-analytics-toggle';
            btn.dataset.kbToggleAnalytics = key;
            btn.title = 'Analytics ein-/ausblenden';
            btn.innerHTML = '<i class="feather icon-bar-chart-2"></i>';
            actions.appendChild(btn);
            const hidden = localStorage.getItem('kb_analytics_hidden_' + key) === '1';
            column.classList.toggle('kb-analytics-hidden', hidden);
          });
        }

        document.addEventListener('click', function (event) {
          const btn = event.target.closest('[data-kb-toggle-analytics]');
          if (!btn) return;
          event.preventDefault();
          event.stopPropagation();
          const column = btn.closest('.column');
          const key = btn.dataset.kbToggleAnalytics || column?.id || 'stage';
          const hidden = !column.classList.contains('kb-analytics-hidden');
          column.classList.toggle('kb-analytics-hidden', hidden);
          localStorage.setItem('kb_analytics_hidden_' + key, hidden ? '1' : '0');
        }, true);

        function boot(root = document) {
          installAnalyticsToggles(root);
          hydrateVisibleNextSteps(root);
        }

        document.addEventListener('DOMContentLoaded', function () {
          boot();
          setTimeout(boot, 300);
          setTimeout(boot, 1200);
        });

        const observer = new MutationObserver((mutations) => {
          let shouldBoot = false;
          for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
              if (node.nodeType === 1 && (node.matches?.('.card, tr.list-row-item, .column') || node.querySelector?.('.card, tr.list-row-item, .column'))) {
                shouldBoot = true;
                break;
              }
            }
            if (shouldBoot) break;
          }
          if (shouldBoot) setTimeout(() => boot(), 60);
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });

        window.refreshKanbanNextStepPreviews = function () {
          document.querySelectorAll('[data-next-step-auto-loaded="1"]').forEach(el => delete el.dataset.nextStepAutoLoaded);
          boot();
        };
      })();
    </script>

  <script>
    /* ===== Boss final: bulk next-step preload + column analytics default hidden ===== */
    (function () {
      'use strict';

      if (window.__bossKanbanNextStepBulkBooted) return;
      window.__bossKanbanNextStepBulkBooted = true;

      const SUMMARY_URL = `{{ url('/admin/kanban/tasks/summaries') }}`;
      const CONTEXT_URL = (id) => `{{ url('/admin/kanban/tasks/context') }}/${encodeURIComponent(id)}`;
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

      const loaded = new Set();
      const overdueToastShown = new Set();

      const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (m) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[m]));

      const cssEscape = (value) => window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&');

      function fmtDate(value) {
        if (!value) return '-';
        const s = String(value).replace('T', ' ');
        const d = new Date(s);
        if (!isNaN(d.getTime())) {
          return d.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: '2-digit' }) + ' ' +
            d.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
        }
        return s.slice(0, 16);
      }

      function getLeadProductId(el) {
        return el?.dataset?.leadProductListId || el?.dataset?.leadProductId || el?.dataset?.id || '';
      }

      function collectVisibleLeadProductIds(root = document) {
        const ids = new Set();

        root.querySelectorAll(
          '.card[data-lead-product-list-id], .card[data-lead-product-id], tr[data-lead-product-id], [data-open-kanban-task-management][data-lead-product-list-id]'
        ).forEach((el) => {
          const id = getLeadProductId(el);
          if (id && /^\d+$/.test(String(id))) ids.add(String(id));
        });

        return Array.from(ids);
      }

      function findCard(id) {
        const safe = cssEscape(id);
        return document.querySelector(`.card[data-lead-product-list-id="${safe}"], .card[data-lead-product-id="${safe}"]`);
      }

      function findListRow(id) {
        const safe = cssEscape(id);
        return document.querySelector(`tr#row-${safe}, tr[data-lead-product-id="${safe}"], tr[data-lead-product-list-id="${safe}"]`);
      }

      function firstTemplateTitle(templates, offset = 0) {
        const flat = [];
        (templates || []).forEach((phase) => {
          const acts = Array.isArray(phase.activities) ? phase.activities : [];
          if (acts.length) {
            acts.forEach((activity) => flat.push({
              title: activity.title || phase.phase_name,
              description: activity.description || phase.description || '',
              minutes: activity.estimated_minutes || ''
            }));
          } else if (phase.phase_name) {
            flat.push({ title: phase.phase_name, description: phase.description || '', minutes: '' });
          }
        });
        return flat[offset] || null;
      }

      function normalizeSummary(id, payload) {
        const summary = payload?.summary || payload || {};
        const tasks = Array.isArray(payload?.tasks) ? payload.tasks : (Array.isArray(summary.tasks) ? summary.tasks : []);
        const templates = Array.isArray(payload?.templates) ? payload.templates : (Array.isArray(summary.templates) ? summary.templates : []);

        const openTasks = tasks.filter(t => !['done', 'cancelled'].includes(String(t.status || '').toLowerCase()));
        const doneTasks = tasks.filter(t => String(t.status || '').toLowerCase() === 'done');
        const previous = doneTasks.length ? doneTasks[doneTasks.length - 1] : null;
        const current = openTasks[0] || null;
        const tmplCurrent = firstTemplateTitle(templates, 0);
        const tmplNext = firstTemplateTitle(templates, openTasks.length ? 0 : 1);
        const overdueTasks = openTasks.filter(t => t.is_overdue);

        return {
          lead_product_list_id: id,
          stage_landed_at: summary.stage_landed_at || summary.stage_started_at || summary.landed_at || payload?.context?.stage_started_at || null,
          previous_title: summary.previous_title || previous?.title || null,
          current_title: summary.current_title || current?.title || tmplCurrent?.title || null,
          next_title: summary.next_title || (openTasks[1]?.title || tmplNext?.title || null),
          description: summary.description || current?.description || tmplCurrent?.description || null,
          open_count: Number(summary.open_count ?? openTasks.length ?? 0),
          done_count: Number(summary.done_count ?? doneTasks.length ?? 0),
          overdue_count: Number(summary.overdue_count ?? overdueTasks.length ?? 0),
          is_overdue: Boolean(summary.is_overdue || overdueTasks.length),
          overdue_title: summary.overdue_title || overdueTasks[0]?.title || null,
          overdue_at: summary.overdue_at || overdueTasks[0]?.planned_end_at || null,
          sub_stage_label: summary.sub_stage_label || payload?.context?.sub_stage_label || null,
          source: summary.source || (current ? 'saved_task' : (tmplCurrent ? 'task_phase' : null)),
          has_personal_task: Boolean(summary.has_personal_task || current?.has_personal_task || current?.external_links?.personal_task_id),
          has_appointment: Boolean(summary.has_appointment || current?.has_appointment || current?.external_links?.appointment_id),
        };
      }

      function renderPreviewHtml(id, summary) {
        const title = summary.current_title || summary.next_title || 'Noch keine Aufgabe';
        const previous = summary.previous_title || '-';
        const landed = fmtDate(summary.stage_landed_at);
        const overdueLine = summary.is_overdue
          ? `<div class="kb-next-step-preview-line text-danger">
               <i class="feather icon-alert-triangle"></i>
               <span>Überfällig: <strong>${esc(summary.overdue_title || title)}</strong>${summary.overdue_at ? ` · ${esc(fmtDate(summary.overdue_at))}` : ''}</span>
             </div>`
          : '';

        const linkedLine = (summary.has_personal_task || summary.has_appointment)
          ? `<div class="kb-next-step-preview-line">
               <i class="feather icon-link"></i>
               <span>
                 ${summary.has_personal_task ? '<span class="kb-task-pill green"><i class="feather icon-check-square"></i> Persönliche Aufgabe</span>' : ''}
                 ${summary.has_appointment ? '<span class="kb-task-pill blue"><i class="feather icon-calendar"></i> Termin</span>' : ''}
               </span>
             </div>`
          : '';

        return `
          <div class="kb-next-step-preview-head">
            <span><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</span>
            <button type="button"
                    class="kb-next-step-preview-btn"
                    data-open-kanban-task-management
                    data-lead-product-list-id="${esc(id)}">
              Details
            </button>
          </div>
          <div class="kb-next-step-preview-line">
            <i class="feather icon-log-in"></i>
            <span>Seit: <strong>${esc(landed)}</strong></span>
          </div>
          ${summary.sub_stage_label ? `
            <div class="kb-next-step-preview-line">
              <i class="feather icon-git-branch"></i>
              <span>Unterphase: <strong>${esc(summary.sub_stage_label)}</strong></span>
            </div>` : ''}
          <div class="kb-next-step-preview-line">
            <i class="feather icon-check-circle"></i>
            <span>Vorher: <strong>${esc(previous)}</strong></span>
          </div>
          <div class="kb-next-step-preview-line">
            <i class="feather icon-list"></i>
            <span>${esc(title)}</span>
          </div>
          ${linkedLine}
          ${summary.next_title ? `
            <div class="kb-next-step-preview-line">
              <i class="feather icon-corner-down-right"></i>
              <span>Danach: <strong>${esc(summary.next_title)}</strong></span>
            </div>` : ''}
          ${overdueLine}
          <div class="kb-next-step-preview-line">
            <i class="feather icon-activity"></i>
            <span>Offen: <strong>${esc(summary.open_count)}</strong> · Erledigt: <strong>${esc(summary.done_count)}</strong></span>
          </div>`;
      }

      function applySummaryToCard(id, summary) {
        const card = findCard(id);
        if (!card) return;

        let preview = card.querySelector('.kb-next-step-preview');

        if (!preview) {
          preview = document.createElement('div');
          preview.className = 'kb-next-step-preview';
          const anchor = card.querySelector('.kb-card-meta') || card.querySelector('.card-header') || card.firstElementChild;
          if (anchor && anchor.parentNode) anchor.insertAdjacentElement('afterend', preview);
          else card.appendChild(preview);
        }

        preview.classList.remove('is-loading');
        preview.classList.toggle('is-overdue', !!summary.is_overdue);
        preview.innerHTML = renderPreviewHtml(id, summary);

        card.classList.toggle('kb-task-overdue-card', !!summary.is_overdue);

        const badge = card.querySelector('[data-kanban-task-count]');
        if (badge) {
          const count = Number(summary.open_count || 0);
          badge.textContent = count > 99 ? '99+' : String(count);
          badge.classList.toggle('d-none', count <= 0);
        }

        if (summary.is_overdue) {
          showOverdueToast(id, summary, card);
        }
      }

      function renderListHtml(id, summary) {
        const title = summary.current_title || summary.next_title || 'Noch keine Aufgabe';
        const overdue = summary.is_overdue ? ' is-overdue' : '';

        return `
          <div class="kb-list-next-step-box${overdue}">
            <div class="kb-list-next-head">
              <span class="kb-list-next-title">
                <i class="feather ${summary.is_overdue ? 'icon-alert-triangle' : 'icon-arrow-right-circle'}"></i>
                <strong>${esc(summary.is_overdue ? 'Überfällig' : 'Nächster Schritt')}</strong>
              </span>
              <button type="button"
                      class="kb-list-next-box-btn"
                      data-open-kanban-task-management
                      data-lead-product-list-id="${esc(id)}">
                Details
              </button>
            </div>
            <div class="kb-list-next-line">
              <i class="feather icon-list"></i>
              <span>${esc(title)}</span>
            </div>
            ${(summary.has_personal_task || summary.has_appointment) ? `
              <div class="kb-list-next-line">
                <i class="feather icon-link"></i>
                <span>
                  ${summary.has_personal_task ? '<span class="kb-task-pill green"><i class="feather icon-check-square"></i> Persönliche Aufgabe</span>' : ''}
                  ${summary.has_appointment ? '<span class="kb-task-pill blue"><i class="feather icon-calendar"></i> Termin</span>' : ''}
                </span>
              </div>` : ''}
            ${summary.sub_stage_label ? `
              <div class="kb-list-next-line">
                <i class="feather icon-git-branch"></i>
                <span>Unterphase: <strong>${esc(summary.sub_stage_label)}</strong></span>
              </div>` : ''}
            <div class="kb-list-next-line">
              <i class="feather icon-log-in"></i>
              <span>Seit: <strong>${esc(fmtDate(summary.stage_landed_at))}</strong></span>
            </div>
            <div class="kb-list-next-line">
              <i class="feather icon-check-circle"></i>
              <span>Vorher: <strong>${esc(summary.previous_title || '-')}</strong></span>
            </div>
            <div class="kb-list-next-line">
              <i class="feather icon-activity"></i>
              <span>Offen: <strong>${esc(summary.open_count)}</strong> · Erledigt: <strong>${esc(summary.done_count)}</strong>${summary.overdue_count ? ` · Überfällig: <strong>${esc(summary.overdue_count)}</strong>` : ''}</span>
            </div>
          </div>`;
      }

      function applySummaryToList(id, summary) {
        const row = findListRow(id);
        if (!row) return;

        row.classList.toggle('kb-task-overdue-row', !!summary.is_overdue);

        let box = row.querySelector('.kb-list-next-step-box');
        if (box) {
          box.outerHTML = renderListHtml(id, summary);
          return;
        }

        const oldStatus = row.querySelector('.kb-status');
        if (oldStatus) {
          oldStatus.outerHTML = renderListHtml(id, summary);
          return;
        }

        const cells = row.querySelectorAll('td');
        const target = cells.length >= 6 ? cells[5] : row.lastElementChild;
        if (target) {
          target.insertAdjacentHTML('afterbegin', renderListHtml(id, summary));
        }
      }

      function applySummary(id, raw) {
        const summary = normalizeSummary(id, raw);
        applySummaryToCard(id, summary);
        applySummaryToList(id, summary);
        if (window.feather) window.feather.replace();
      }

      function showOverdueToast(id, summary, card) {
        if (overdueToastShown.has(String(id))) return;
        overdueToastShown.add(String(id));

        const wrapId = 'kbKanbanTaskToastWrap';
        let wrap = document.getElementById(wrapId);
        if (!wrap) {
          wrap = document.createElement('div');
          wrap.id = wrapId;
          wrap.className = 'kb-kanban-task-toast-wrap';
          document.body.appendChild(wrap);
        }

        const customer = card?.querySelector('.card-name')?.textContent?.trim() || `Prozess #${id}`;
        const toast = document.createElement('div');
        toast.className = 'kb-kanban-task-toast';
        toast.innerHTML = `
          <div class="kb-kanban-task-toast-head">
            <span><i class="feather icon-alert-triangle"></i> Überfällige Aufgabe</span>
            <span>#${esc(id)}</span>
          </div>
          <div class="kb-kanban-task-toast-body">
            <strong>${esc(customer)}</strong><br>
            ${esc(summary.overdue_title || summary.current_title || 'Aufgabe')}<br>
            ${summary.overdue_at ? `Fällig: ${esc(fmtDate(summary.overdue_at))}` : ''}
          </div>
        `;
        toast.addEventListener('click', () => {
          const btn = card?.querySelector('[data-open-kanban-task-management]');
          if (btn) btn.click();
        });

        wrap.appendChild(toast);
        if (window.feather) window.feather.replace();

        setTimeout(() => toast.remove(), 9000);
      }

      async function fetchSummaries(ids) {
        const cleanIds = ids.filter(id => id && !loaded.has(String(id)));
        if (!cleanIds.length) return;

        cleanIds.forEach(id => loaded.add(String(id)));

        cleanIds.forEach(id => {
          const card = findCard(id);
          const preview = card?.querySelector('.kb-next-step-preview');
          if (preview) preview.classList.add('is-loading');
        });

        try {
          const res = await fetch(SUMMARY_URL, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ lead_product_list_ids: cleanIds }),
          });

          const json = await res.json().catch(() => ({}));

          if (!res.ok || json.success === false || !json.summaries) {
            throw new Error(json.message || 'Bulk summaries unavailable');
          }

          Object.entries(json.summaries).forEach(([id, summary]) => applySummary(id, summary));
        } catch (bulkError) {
          // Fallback: old context endpoint one-by-one, so it still works before routes are cached.
          await Promise.all(cleanIds.map(async (id) => {
            try {
              const res = await fetch(CONTEXT_URL(id), {
                headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': csrf,
                },
              });
              const json = await res.json().catch(() => ({}));
              if (res.ok && json.success !== false) {
                applySummary(id, {
                  context: json.context,
                  tasks: json.tasks || [],
                  templates: json.templates || [],
                });
              }
            } catch (e) {
              const card = findCard(id);
              const preview = card?.querySelector('.kb-next-step-preview');
              if (preview) preview.classList.remove('is-loading');
              console.warn('Kanban next-step preload failed for lead_product_list_id', id, e);
            }
          }));
        }
      }

      function normalizeColumnHeaders(root = document) {
        root.querySelectorAll('.column').forEach((column) => {
          const h3 = column.querySelector('h3');
          if (!h3) return;

          const stageKey =
            h3.dataset.workflowStageKey ||
            column.dataset.stage ||
            column.id ||
            h3.textContent.trim().toLowerCase().replace(/\s+/g, '_');

          column.dataset.analyticsKey = stageKey;

          // Default is hidden. Only show if user explicitly turned it on.
          const stored = localStorage.getItem('kb_analytics_hidden_' + stageKey);
          const hidden = stored === null ? true : stored !== '0';
          column.classList.toggle('kb-analytics-hidden', hidden);

          const underBtn = h3.querySelector('.kb-understage-btn');
          if (underBtn) {
            const count = underBtn.querySelector('b')?.textContent?.trim() || '';
            const icon = underBtn.querySelector('i')?.outerHTML || '<i class="feather icon-git-branch"></i>';
            underBtn.innerHTML = `${icon}<span>Unterphasen</span>${count ? `<b>${esc(count)}</b>` : ''}`;
            underBtn.title = 'Unterphasen anzeigen';
          }

          const actions = h3.querySelector('.kb-column-actions') || h3;
          if (!actions.querySelector('[data-kb-toggle-analytics]')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'kb-toggle-analytics';
            btn.dataset.kbToggleAnalytics = stageKey;
            btn.title = 'Analyse-Badges ein-/ausblenden';
            btn.innerHTML = '<i class="feather icon-bar-chart-2"></i>';
            actions.appendChild(btn);
          }

          const toggle = actions.querySelector('[data-kb-toggle-analytics]');
          if (toggle) toggle.classList.toggle('is-active', !hidden);
        });

        if (window.feather) window.feather.replace();
      }

      document.addEventListener('click', function (event) {
        const btn = event.target.closest('[data-kb-toggle-analytics]');
        if (!btn) return;

        event.preventDefault();
        event.stopPropagation();

        const column = btn.closest('.column');
        if (!column) return;

        const key = btn.dataset.kbToggleAnalytics || column.dataset.analyticsKey || column.id || 'stage';
        const nextHidden = !column.classList.contains('kb-analytics-hidden');

        column.classList.toggle('kb-analytics-hidden', nextHidden);
        btn.classList.toggle('is-active', !nextHidden);
        localStorage.setItem('kb_analytics_hidden_' + key, nextHidden ? '1' : '0');
      }, true);

      function boot(root = document) {
        normalizeColumnHeaders(root);
        fetchSummaries(collectVisibleLeadProductIds(root));
      }

      document.addEventListener('DOMContentLoaded', function () {
        boot();
        setTimeout(() => boot(), 250);
        setTimeout(() => boot(), 1000);
        setTimeout(() => boot(), 2500);
      });

      const observer = new MutationObserver((mutations) => {
        let shouldBoot = false;
        for (const mutation of mutations) {
          for (const node of mutation.addedNodes) {
            if (node.nodeType === 1 && (node.matches?.('.card, tr.list-row-item, .column') || node.querySelector?.('.card, tr.list-row-item, .column'))) {
              shouldBoot = true;
              break;
            }
          }
          if (shouldBoot) break;
        }
        if (shouldBoot) setTimeout(() => boot(), 80);
      });

      observer.observe(document.documentElement, { childList: true, subtree: true });

      window.refreshKanbanNextStepPreviews = function () {
        loaded.clear();
        boot();
      };
    })();
  </script>


<style>
  /* ===== Boss final: column title left + Unterphasen + analytics toggle + sub-stage DnD fix ===== */
  .column h3[data-workflow-stage-key] {
    min-height: 44px !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 6px !important;
    padding: 6px 8px !important;
    overflow: visible !important;
  }

  .column h3[data-workflow-stage-key] .kb-column-head-left {
    width: auto !important;
    flex: 1 1 auto !important;
    min-width: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    padding: 0 !important;
    min-height: 0 !important;
    border-bottom: 0 !important;
    overflow: hidden !important;
  }

  .column h3[data-workflow-stage-key] .kb-column-title {
    justify-content: flex-start !important;
    text-align: left !important;
    max-width: 100% !important;
    min-width: 0 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    font-size: 14px !important;
  }

  .column h3[data-workflow-stage-key] .kb-column-actions {
    width: auto !important;
    flex: 0 0 auto !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 5px !important;
    padding: 0 !important;
    background: transparent !important;
    flex-wrap: nowrap !important;
    overflow: visible !important;
  }

  .column h3[data-workflow-stage-key] .kb-understage-btn {
    height: 28px !important;
    min-height: 28px !important;
    border-radius: 999px !important;
    padding: 0 8px !important;
    font-size: 10px !important;
    max-width: 132px !important;
  }

  .column h3[data-workflow-stage-key] .kb-understage-btn span {
    max-width: 82px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
  }

  .column h3[data-workflow-stage-key] .kb-understage-btn b {
    display: inline-flex !important;
    min-width: 18px !important;
    height: 18px !important;
    align-items: center !important;
    justify-content: center !important;
    margin-left: 2px !important;
    border-radius: 999px !important;
    background: #fff !important;
    color: #334155 !important;
    font-size: 10px !important;
    font-weight: 1000 !important;
  }

  .column h3[data-workflow-stage-key] .kb-toggle-analytics,
  .column h3[data-workflow-stage-key] .kb-column-analytics-toggle {
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    border-radius: 999px !important;
    border: 1px solid rgba(255,255,255,.35) !important;
    background: rgba(255,255,255,.16) !important;
    color: #fff !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 !important;
    cursor: pointer !important;
  }

  .column h3[data-workflow-stage-key] .kb-toggle-analytics.is-active,
  .column h3[data-workflow-stage-key] .kb-column-analytics-toggle.is-active {
    background: #fff !important;
    color: #334155 !important;
  }

  .column h3[data-workflow-stage-key] .kb-column-substage-wrap {
    display: none !important;
  }

  .column.kb-analytics-hidden .kb-header-counts {
    display: none !important;
  }

  .column:not(.kb-analytics-hidden) .kb-header-counts {
    position: absolute !important;
    left: 6px !important;
    right: 6px !important;
    top: 42px !important;
    z-index: 4 !important;
    display: flex !important;
    justify-content: center !important;
    background: rgba(15, 23, 42, .16) !important;
    border-radius: 999px !important;
    padding: 3px !important;
  }

  .column:not(.kb-analytics-hidden) h3[data-workflow-stage-key] {
    min-height: 74px !important;
    padding-bottom: 30px !important;
  }

  .kb-understage-sidebar .card,
  [data-understage-dropzone] .card {
    cursor: grab;
  }

  [data-understage-dropzone].drag-over {
    outline: 2px dashed #74b2d4 !important;
    outline-offset: -5px !important;
    background: #eef7fb !important;
  }
</style>

<script>
  /* ===== Boss final: normalize column header order + restore Unterphasen drag/drop ===== */
  (function () {
    'use strict';

    const MIME = window.KB_DND_MIME || 'application/x-leadui-cards';

    function cssEscape(value) {
      return window.CSS?.escape ? CSS.escape(String(value)) : String(value).replace(/[^a-zA-Z0-9_-]/g, '\\$&');
    }

    function normalizeColumnHeader(column) {
      if (!column) return;
      const h3 = column.querySelector('h3[data-workflow-stage-key]') || column.querySelector('h3');
      if (!h3) return;

      const stageKey = h3.dataset.workflowStageKey || column.id || column.dataset.stage || 'stage';
      h3.dataset.workflowStageKey = stageKey;
      column.dataset.analyticsKey = stageKey;

      let titleWrap = h3.querySelector('.kb-column-head-left');
      let title = h3.querySelector('.kb-column-title');

      if (!title) {
        title = document.createElement('span');
        title.className = 'kb-column-title';
        title.textContent = h3.textContent.trim() || stageKey;
      }

      if (!titleWrap) {
        titleWrap = document.createElement('span');
        titleWrap.className = 'kb-column-head-left';
      }

      if (!titleWrap.contains(title)) titleWrap.appendChild(title);

      let actions = h3.querySelector('.kb-column-actions');
      if (!actions) {
        actions = document.createElement('span');
        actions.className = 'kb-column-actions';
      }

      const underBtn = h3.querySelector('.kb-understage-btn');
      if (underBtn) {
        const count = (underBtn.querySelector('b')?.textContent || '').trim();
        const icon = underBtn.querySelector('i')?.outerHTML || '<i class="feather icon-git-branch"></i>';
        underBtn.innerHTML = icon + '<span>Unterphasen</span>' + (count ? '<b>' + count + '</b>' : '<b>0</b>');
        underBtn.title = 'Unterphasen anzeigen';
        actions.appendChild(underBtn);
      }

      let toggle = h3.querySelector('[data-kb-toggle-analytics]');
      if (!toggle) {
        toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'kb-toggle-analytics';
        toggle.dataset.kbToggleAnalytics = stageKey;
        toggle.title = 'Analyse-Badges ein-/ausblenden';
        toggle.innerHTML = '<i class="feather icon-bar-chart-2"></i>';
      }
      actions.appendChild(toggle);

      const configWrap = h3.querySelector('.kb-column-substage-wrap');
      if (configWrap) actions.appendChild(configWrap);

      const counts = h3.querySelector('.kb-header-counts');
      if (counts) actions.appendChild(counts);

      h3.innerHTML = '';
      h3.appendChild(titleWrap);
      h3.appendChild(actions);

      // Default OFF. Only show analytics if user explicitly turned them on.
      const stored = localStorage.getItem('kb_analytics_hidden_' + stageKey);
      const hidden = stored === null ? true : stored !== '0';
      column.classList.toggle('kb-analytics-hidden', hidden);
      toggle.classList.toggle('is-active', !hidden);
    }

    function normalizeAllColumnHeaders(root = document) {
      root.querySelectorAll('.column').forEach(normalizeColumnHeader);
      if (window.feather) window.feather.replace();
    }

    document.addEventListener('click', function (event) {
      const btn = event.target.closest('[data-kb-toggle-analytics]');
      if (!btn) return;

      event.preventDefault();
      event.stopPropagation();

      const column = btn.closest('.column');
      if (!column) return;

      const key = btn.dataset.kbToggleAnalytics || column.dataset.analyticsKey || column.id || 'stage';
      const nextHidden = !column.classList.contains('kb-analytics-hidden');

      column.classList.toggle('kb-analytics-hidden', nextHidden);
      btn.classList.toggle('is-active', !nextHidden);
      localStorage.setItem('kb_analytics_hidden_' + key, nextHidden ? '1' : '0');
    }, true);

    // Dragging inside Unterphasen sidebar was broken because the original dragstart only listened to #kanban .card.
    document.addEventListener('dragstart', function (event) {
      const card = event.target.closest('#kbUnderstageBoard .card, .kb-understage-sidebar .card, [data-understage-dropzone] .card');
      if (!card || !event.dataTransfer) return;

      const id = card.id || ('card-' + (card.dataset.leadProductId || card.dataset.leadProductListId || ''));
      if (!id) return;

      event.dataTransfer.setData(MIME, JSON.stringify([id]));
      event.dataTransfer.setData('text/plain', JSON.stringify([id]));
      event.dataTransfer.effectAllowed = 'move';
      card.classList.add('kb-understage-dragging');
    }, true);

    document.addEventListener('dragend', function (event) {
      const card = event.target.closest('#kbUnderstageBoard .card, .kb-understage-sidebar .card, [data-understage-dropzone] .card');
      if (card) card.classList.remove('kb-understage-dragging');
    }, true);

    // Make sure every card inside Unterphasen remains draggable after rendering.
    function enableUnderstageCards(root = document) {
      root.querySelectorAll('#kbUnderstageBoard .card, .kb-understage-sidebar .card, [data-understage-dropzone] .card').forEach((card) => {
        card.draggable = true;
        if (!card.dataset.leadProductListId && card.dataset.leadProductId) {
          card.dataset.leadProductListId = card.dataset.leadProductId;
        }
      });
    }

    function boot(root = document) {
      normalizeAllColumnHeaders(root);
      enableUnderstageCards(root);
    }

    document.addEventListener('DOMContentLoaded', function () {
      boot();
      setTimeout(boot, 250);
      setTimeout(boot, 1000);
      setTimeout(boot, 2500);
    });

    const observer = new MutationObserver(function (mutations) {
      let shouldBoot = false;
      for (const mutation of mutations) {
        for (const node of mutation.addedNodes) {
          if (node.nodeType === 1 && (node.matches?.('.column, #kbUnderstageBoard, .card') || node.querySelector?.('.column, #kbUnderstageBoard, .card'))) {
            shouldBoot = true;
            break;
          }
        }
        if (shouldBoot) break;
      }
      if (shouldBoot) setTimeout(() => boot(), 80);
    });

    observer.observe(document.documentElement, { childList: true, subtree: true });

    window.normalizeKanbanColumnHeaders = normalizeAllColumnHeaders;
    window.enableUnderstageCardDragDrop = enableUnderstageCards;
  })();
</script>

@endsection



<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#btnOpenKanbanStageAdminTop, #btnOpenKanbanStageAdminMain, .kb-stage-admin-open').forEach(function (el) {
      el.remove();
    });
  });
</script>

