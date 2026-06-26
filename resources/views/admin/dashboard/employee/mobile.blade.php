@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('style')
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

  <style>
    :root {
      --color-green: #8bc34a;
      --color-green-dark: #6fa12f;
      --color-green-soft: #edf7df;
      --color-green-light: #cfe09b;

      --color-blue: #74b2d4;
      --color-blue-dark: #4f9bc5;
      --color-blue-soft: #e6f0f7;
      --color-blue-light: #d7ebf6;

      --color-warning: #f8ac00;
      --color-warning-soft: #fff7df;

      --color-danger: #e50656;
      --color-danger-soft: #fff0f5;

      --color-text: #111827;
      --color-text-soft: #374151;
      --color-text-muted: #6b7280;
      --color-bg: #f4f7f9;
      --color-surface: #ffffff;
      --color-surface-2: #f9fbfd;
      --color-border: #e5e7eb;
      --color-border-strong: #d7dee8;

      --shadow-xs: 0 1px 2px rgba(15, 23, 42, .04);
      --shadow-sm: 0 4px 12px rgba(15, 23, 42, .06);
      --shadow-md: 0 12px 28px rgba(15, 23, 42, .08);
      --shadow-lg: 0 24px 70px rgba(15, 23, 42, .16);

      --radius-sm: .65rem;
      --radius-md: .9rem;
      --radius-lg: 1.25rem;
      --radius-xl: 1.65rem;

      --font-main: 'Inter', sans-serif;
      --grid-gap: 1.35rem;
      --grid-row: 62px;
      --widget-padding: 1.35rem;
    }

    body.theme-soft {
      --color-bg: #f8fafc;
      --color-surface: #ffffff;
      --color-surface-2: #f7fafc;
    }

    body.theme-contrast {
      --color-bg: #eef3f7;
      --color-surface: #ffffff;
      --color-surface-2: #f3f7fa;
      --color-border: #d4dde8;
      --color-text: #0f172a;
    }

    body.density-comfortable {
      --grid-gap: 1.55rem;
      --grid-row: 68px;
      --widget-padding: 1.55rem;
    }

    body.density-compact {
      --grid-gap: .85rem;
      --grid-row: 54px;
      --widget-padding: 1rem;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      min-height: 100vh;
      font-family: var(--font-main);
      background:
        radial-gradient(circle at top left, rgba(116, 178, 212, .18), transparent 34rem),
        radial-gradient(circle at top right, rgba(139, 195, 74, .14), transparent 32rem),
        var(--color-bg);
      color: var(--color-text);
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    button,
    input,
    select,
    textarea {
      font: inherit;
    }

    button {
      cursor: pointer;
      border: 0;
      background: none;
      color: inherit;
    }

    input,
    select,
    textarea {
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      color: var(--color-text);
      outline: none;
    }

    input:focus,
    select:focus,
    textarea:focus {
      border-color: var(--color-blue);
      box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
    }

    .main-content {
      width: min(1660px, 100%);
      min-height: 100vh;
      margin: 0 auto;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }

    .dashboard-hero {
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, .75);
      border-radius: var(--radius-xl);
      background:
        linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(255, 255, 255, .82)),
        linear-gradient(135deg, rgba(139, 195, 74, .26), rgba(116, 178, 212, .28));
      box-shadow: var(--shadow-sm);
      padding: 1.25rem;
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 1.25rem;
      align-items: center;
    }

    .dashboard-hero:before {
      content: '';
      position: absolute;
      inset: auto -90px -160px auto;
      width: 320px;
      height: 320px;
      background: radial-gradient(circle, rgba(139, 195, 74, .26), transparent 68%);
      pointer-events: none;
    }

    .hero-title {
      margin-top: .75rem;
      font-size: clamp(1.55rem, 3vw, 2.35rem);
      font-weight: 900;
      letter-spacing: -.045em;
      line-height: 1.05;
    }

    .hero-subtitle {
      margin-top: .5rem;
      color: var(--color-text-muted);
      max-width: 780px;
      font-size: .94rem;
      font-weight: 600;
    }

    .hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: .65rem;
      justify-content: flex-end;
      position: relative;
      z-index: 2;
    }

    .btn {
      min-height: 42px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      padding: .65rem 1rem;
      border-radius: .9rem;
      font-size: .84rem;
      font-weight: 800;
      transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border .18s ease;
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      color: var(--color-text-soft);
      box-shadow: var(--shadow-xs);
      white-space: nowrap;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
      border-color: var(--color-border-strong);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
      border-color: rgba(111, 161, 47, .32);
      color: #fff;
    }

    .btn-blue {
      background: linear-gradient(135deg, var(--color-blue), var(--color-blue-dark));
      border-color: rgba(79, 155, 197, .32);
      color: #fff;
    }

    .btn-danger {
      color: var(--color-danger);
      background: var(--color-danger-soft);
      border-color: rgba(229, 6, 86, .18);
    }

    .btn.is-active {
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
      border-color: rgba(116, 178, 212, .65);
    }

    .toolbar-card {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: .85rem;
      background: rgba(255, 255, 255, .9);
      backdrop-filter: blur(14px);
      padding: .85rem;
      border-radius: var(--radius-xl);
      border: 1px solid rgba(229, 231, 235, .86);
      box-shadow: var(--shadow-xs);
    }

    .tabs {
      display: flex;
      gap: .45rem;
      background: var(--color-surface-2);
      padding: .32rem;
      border-radius: 1rem;
      border: 1px solid var(--color-border);
      overflow-x: auto;
      scrollbar-width: none;
    }

    .tabs::-webkit-scrollbar {
      display: none;
    }

    .tab-btn {
      min-height: 40px;
      padding: .55rem .9rem;
      border-radius: .78rem;
      font-weight: 800;
      font-size: .84rem;
      color: var(--color-text-muted);
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      white-space: nowrap;
      transition: all .18s ease;
    }

    .tab-btn.active {
      background: var(--color-surface);
      color: var(--color-green-dark);
      box-shadow: var(--shadow-xs);
    }

    .toolbar-right {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: .6rem;
    }

    .dashboard-search {
      position: relative;
      min-width: min(320px, 100%);
    }

    .dashboard-search i {
      position: absolute;
      left: .85rem;
      top: 50%;
      transform: translateY(-50%);
      width: 17px;
      height: 17px;
      color: var(--color-text-muted);
    }

    .dashboard-search input {
      width: 100%;
      height: 42px;
      padding: 0 .9rem 0 2.35rem;
      border-radius: .9rem;
      font-weight: 700;
      background: var(--color-surface);
    }

    .select-control {
      height: 42px;
      min-width: 150px;
      padding: 0 .85rem;
      border-radius: .9rem;
      font-size: .84rem;
      font-weight: 800;
    }

    .feed-bar {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 0;
      align-items: center;
      overflow: hidden;
      border-radius: var(--radius-xl);
      background: linear-gradient(135deg, var(--color-blue), var(--color-blue-dark));
      color: #fff;
      box-shadow: var(--shadow-xs);
    }

    .feed-icon {
      height: 100%;
      min-height: 54px;
      display: grid;
      place-items: center;
      padding: 0 1.1rem;
      background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
    }

    .feed-content {
      display: flex;
      align-items: center;
      gap: .85rem;
      padding: .85rem 1rem;
      overflow-x: auto;
      white-space: nowrap;
      font-size: .86rem;
      font-weight: 700;
      scrollbar-width: none;
    }

    .feed-content::-webkit-scrollbar {
      display: none;
    }

    .feed-badge {
      border: 1px solid rgba(255, 255, 255, .5);
      border-radius: 999px;
      padding: .18rem .55rem;
      font-size: .72rem;
      font-weight: 900;
    }

    .feed-actions {
      padding: .65rem;
    }

    .feed-mini-btn {
      width: 34px;
      height: 34px;
      display: grid;
      place-items: center;
      border-radius: .8rem;
      background: rgba(255, 255, 255, .14);
      color: #fff;
    }

    .view-section {
      display: none;
    }

    .view-section.active {
      display: block;
      animation: fadeIn .28s ease both;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(8px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .widget-grid {
      display: grid;
      grid-template-columns: repeat(12, minmax(0, 1fr));
      grid-auto-rows: var(--grid-row);
      grid-auto-flow: dense;
      gap: var(--grid-gap);
      align-items: stretch;
      transition: gap .2s ease;
    }

    .widget {
      position: relative;
      height: 100%;
      min-width: 0;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      padding: var(--widget-padding);
      border-radius: var(--radius-xl);
      border: 1px solid rgba(229, 231, 235, .92);
      background:
        linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(255, 255, 255, .9)),
        var(--color-surface);
      box-shadow: var(--shadow-sm);
      transition: transform .18s ease, box-shadow .18s ease, border .18s ease, opacity .18s ease;
    }

    .widget:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .widget.is-hidden-by-filter {
      display: none !important;
    }

    .widget-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .85rem;
      margin-bottom: .95rem;
      flex-shrink: 0;
    }

    .widget-title-wrap {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: .7rem;
    }

    .widget-icon {
      width: 36px;
      height: 36px;
      flex: 0 0 auto;
      display: grid;
      place-items: center;
      border-radius: .95rem;
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
    }

    .widget-icon.green {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .widget-icon.warning {
      background: var(--color-warning-soft);
      color: #c47b00;
    }

    .widget-icon.danger {
      background: var(--color-danger-soft);
      color: var(--color-danger);
    }

    .widget-title {
      display: block;
      font-size: .99rem;
      font-weight: 900;
      letter-spacing: -.02em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .widget-subtitle {
      display: block;
      margin-top: .08rem;
      font-size: .72rem;
      font-weight: 700;
      color: var(--color-text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .widget-tools {
      display: flex;
      align-items: center;
      gap: .35rem;
      flex-shrink: 0;
    }

    .widget-tool-btn {
      width: 32px;
      height: 32px;
      display: none;
      place-items: center;
      border-radius: .75rem;
      color: var(--color-text-muted);
      background: var(--color-surface-2);
      border: 1px solid var(--color-border);
    }

    .widget-tool-btn:hover {
      color: var(--color-text);
      border-color: var(--color-border-strong);
    }

    .widget-tool-btn.danger:hover {
      color: var(--color-danger);
      background: var(--color-danger-soft);
    }

    .widget-content {
      flex: 1;
      min-height: 0;
      width: 100%;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .col-span-1 {
      grid-column: span 1;
    }

    .col-span-2 {
      grid-column: span 2;
    }

    .col-span-3 {
      grid-column: span 3;
    }

    .col-span-4 {
      grid-column: span 4;
    }

    .col-span-5 {
      grid-column: span 5;
    }

    .col-span-6 {
      grid-column: span 6;
    }

    .col-span-7 {
      grid-column: span 7;
    }

    .col-span-8 {
      grid-column: span 8;
    }

    .col-span-9 {
      grid-column: span 9;
    }

    .col-span-10 {
      grid-column: span 10;
    }

    .col-span-11 {
      grid-column: span 11;
    }

    .col-span-12 {
      grid-column: span 12;
    }

    .row-span-1 {
      grid-row: span 1;
    }

    .row-span-2 {
      grid-row: span 2;
    }

    .row-span-3 {
      grid-row: span 3;
    }

    .row-span-4 {
      grid-row: span 4;
    }

    .row-span-5 {
      grid-row: span 5;
    }

    .row-span-6 {
      grid-row: span 6;
    }

    .row-span-7 {
      grid-row: span 7;
    }

    .row-span-8 {
      grid-row: span 8;
    }

    .row-span-9 {
      grid-row: span 9;
    }

    .row-span-10 {
      grid-row: span 10;
    }

    .row-span-11 {
      grid-row: span 11;
    }

    .row-span-12 {
      grid-row: span 12;
    }

    .row-span-13 {
      grid-row: span 13;
    }

    .row-span-14 {
      grid-row: span 14;
    }

    .row-span-15 {
      grid-row: span 15;
    }

    .row-span-16 {
      grid-row: span 16;
    }

    .row-span-17 {
      grid-row: span 17;
    }

    .row-span-18 {
      grid-row: span 18;
    }

    .row-span-19 {
      grid-row: span 19;
    }

    .row-span-20 {
      grid-row: span 20;
    }

    .row-span-21 {
      grid-row: span 21;
    }

    .row-span-22 {
      grid-row: span 22;
    }

    .row-span-23 {
      grid-row: span 23;
    }

    .row-span-24 {
      grid-row: span 24;
    }

    body.edit-mode .widget {
      border: 2px dashed rgba(116, 178, 212, .8);
    }

    body.edit-mode .widget-header {
      cursor: grab;
    }

    body.edit-mode .widget-tool-btn {
      display: grid;
    }

    body.edit-mode .widget.dragging {
      opacity: .46;
      transform: scale(.985);
    }

    body.edit-mode .widget-grid {
      position: relative;
    }

    .widget-drop-placeholder {
      min-height: 120px;
      border-radius: var(--radius-xl);
      border: 2px dashed var(--color-green);
      background:
        linear-gradient(135deg, rgba(139, 195, 74, .13), rgba(116, 178, 212, .13));
      box-shadow: inset 0 0 0 4px rgba(139, 195, 74, .08);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--color-green-dark);
      font-size: .82rem;
      font-weight: 900;
      pointer-events: none;
      animation: dropPulse .9s ease-in-out infinite alternate;
    }

    .widget-drop-placeholder::before {
      content: 'Hier platzieren';
    }

    body.edit-mode .widget.dragging {
      opacity: .35;
      transform: scale(.985);
      box-shadow: none;
    }

    body.edit-mode .widget.is-drag-target-before {
      box-shadow: -8px 0 0 var(--color-green);
    }

    body.edit-mode .widget.is-drag-target-after {
      box-shadow: 8px 0 0 var(--color-blue);
    }

    @keyframes dropPulse {
      from {
        border-color: var(--color-green);
        transform: scale(.995);
      }

      to {
        border-color: var(--color-blue);
        transform: scale(1);
      }
    }

    .resize-handle {
      display: none;
      position: absolute;
      right: 0;
      bottom: 0;
      width: 30px;
      height: 30px;
      cursor: nwse-resize;
      background: linear-gradient(135deg, transparent 50%, var(--color-blue) 50%);
      border-bottom-right-radius: var(--radius-xl);
      z-index: 30;
    }

    body.edit-mode .resize-handle {
      display: block;
    }

    .stat-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: .8rem;
    }

    .dash-cal-widget {
      height: 100%;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: .75rem;
    }

    .dash-cal-grid {
      min-height: 250px;
      display: grid;
      grid-template-columns: repeat(7, minmax(0, 1fr));
      gap: .35rem;
    }

    .dash-cal-event-main {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: .28rem;
    }

    .dash-cal-employee-stack {
      display: flex;
      align-items: center;
      min-height: 24px;
      margin-top: .15rem;
    }

    .dash-cal-employee-stack img {
      width: 24px;
      height: 24px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid var(--color-surface);
      box-shadow: var(--shadow-xs);
      margin-left: -7px;
      background: var(--color-surface);
    }

    .dash-cal-employee-stack img:first-child {
      margin-left: 0;
    }

    .dash-cal-more {
      width: 24px;
      height: 24px;
      display: grid;
      place-items: center;
      margin-left: -7px;
      border-radius: 999px;
      border: 2px solid var(--color-surface);
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
      font-size: .62rem;
      font-weight: 900;
    }

    .dash-cal-event {
      width: 100%;
      text-align: left;
    }

    .dash-cal-modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 2400;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background: rgba(15, 23, 42, .55);
    }

    .dash-cal-modal-overlay.show {
      display: flex;
    }

    .dash-cal-modal {
      width: min(920px, 100%);
      max-height: calc(100vh - 2rem);
      overflow-y: auto;
      padding: 1.35rem;
      border-radius: var(--radius-xl);
      background: var(--color-surface);
      border: 1px solid var(--color-border);
      box-shadow: var(--shadow-lg);
    }

    .stat-box {
      min-height: 108px;
      padding: 1rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .stat-box.green {
      background: var(--color-green-soft);
      border-color: rgba(139, 195, 74, .28);
      color: var(--color-green-dark);
    }

    .stat-box.blue {
      background: var(--color-blue-soft);
      border-color: rgba(116, 178, 212, .28);
      color: var(--color-blue-dark);
    }

    .stat-box.warning {
      background: var(--color-warning-soft);
      border-color: rgba(248, 172, 0, .28);
      color: #b87400;
    }

    .stat-label {
      font-size: .68rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .055em;
    }

    .stat-value {
      display: flex;
      align-items: baseline;
      gap: .32rem;
      font-size: 1.85rem;
      line-height: 1;
      font-weight: 900;
      letter-spacing: -.045em;
    }

    .stat-value small {
      font-size: .77rem;
      letter-spacing: 0;
      font-weight: 900;
    }

    .info-note {
      margin-top: auto;
      padding: .75rem;
      border-radius: var(--radius-md);
      background: var(--color-surface-2);
      border: 1px solid var(--color-border);
      font-size: .78rem;
      font-weight: 800;
      color: var(--color-text-muted);
      display: flex;
      align-items: center;
      gap: .55rem;
    }

    .focus-toolbar {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 150px 42px;
      gap: .55rem;
      margin-bottom: .85rem;
    }

    .focus-toolbar input,
    .focus-toolbar select,
    .focus-toolbar button {
      height: 40px;
      border-radius: .85rem;
      padding: 0 .75rem;
      font-size: .78rem;
      font-weight: 800;
    }

    .focus-toolbar button {
      display: grid;
      place-items: center;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      color: var(--color-text-muted);
    }

    .focus-list {
      list-style: none;
      overflow-y: auto;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: .55rem;
      padding-right: .25rem;
    }

    .focus-item {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: .75rem;
      align-items: center;
      padding: .75rem;
      border-radius: var(--radius-md);
      border: 1px solid transparent;
      background: var(--color-surface-2);
      transition: border .18s ease, transform .18s ease;
    }

    .focus-item:hover {
      border-color: var(--color-border-strong);
      transform: translateX(2px);
    }

    .type-badge {
      width: 78px;
      padding: .22rem .42rem;
      border-radius: .55rem;
      font-size: .62rem;
      line-height: 1;
      font-weight: 900;
      text-align: center;
      text-transform: uppercase;
    }

    .type-ticket {
      background: #fee2e2;
      color: #dc2626;
    }

    .type-termin {
      background: #dbeafe;
      color: #2563eb;
    }

    .type-task {
      background: #f3f4f6;
      color: #4b5563;
    }

    .type-anfrage {
      background: #fef3c7;
      color: #d97706;
    }

    .type-lead {
      background: #e0e7ff;
      color: #4f46e5;
    }

    .type-angebot {
      background: #dcfce7;
      color: #16a34a;
    }

    .type-project {
      background: #fce7f3;
      color: #db2777;
    }

    .type-sonstiges {
      background: #e5e7eb;
      color: #374151;
    }

    .focus-title {
      font-size: .86rem;
      font-weight: 900;
      color: var(--color-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .focus-meta {
      margin-top: .2rem;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: .45rem;
      color: var(--color-text-muted);
      font-size: .72rem;
      font-weight: 700;
    }

    .focus-open-link {
      width: 30px;
      height: 30px;
      display: grid;
      place-items: center;
      border-radius: .75rem;
      background: #fff;
      color: var(--color-blue-dark);
      border: 1px solid var(--color-border);
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      padding: .24rem .55rem;
      border-radius: 999px;
      font-size: .72rem;
      font-weight: 900;
      white-space: nowrap;
    }

    .pill.danger {
      background: var(--color-danger-soft);
      color: var(--color-danger);
    }

    .pill.green {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .pill.blue {
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
    }

    .pill.warning {
      background: var(--color-warning-soft);
      color: #b87400;
    }

    .clock-container {
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .analog-clock-wrapper {
      flex: 1;
      min-height: 0;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      transform-origin: center center;
    }

    .analog-clock {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 5px solid var(--color-surface-2);
      box-shadow: inset 0 2px 8px rgba(15, 23, 42, .06), 0 12px 28px rgba(116, 178, 212, .12);
      position: relative;
      background: linear-gradient(180deg, #fff, #f9fbfd);
    }

    .clock-center {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 11px;
      height: 11px;
      background: var(--color-text);
      border: 2px solid #fff;
      border-radius: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
    }

    .hand {
      position: absolute;
      bottom: 50%;
      left: 50%;
      transform-origin: bottom;
      border-radius: 8px;
      z-index: 5;
    }

    .hour-hand {
      width: 5px;
      height: 40px;
      margin-left: -2.5px;
      background: var(--color-text);
    }

    .minute-hand {
      width: 3px;
      height: 55px;
      margin-left: -1.5px;
      background: var(--color-blue-dark);
    }

    .second-hand {
      width: 1.5px;
      height: 64px;
      margin-left: -.75px;
      background: var(--color-danger);
      z-index: 6;
    }

    .clock-numbers {
      position: absolute;
      inset: 0;
    }

    .num {
      position: absolute;
      width: 24px;
      height: 24px;
      top: 50%;
      left: 50%;
      margin: -12px 0 0 -12px;
      text-align: center;
      line-height: 24px;
      font-size: 13px;
      font-weight: 900;
      color: var(--color-text-soft);
    }

    .num-1 {
      transform: rotate(30deg) translateY(-55px) rotate(-30deg);
    }

    .num-2 {
      transform: rotate(60deg) translateY(-55px) rotate(-60deg);
    }

    .num-3 {
      transform: rotate(90deg) translateY(-55px) rotate(-90deg);
    }

    .num-4 {
      transform: rotate(120deg) translateY(-55px) rotate(-120deg);
    }

    .num-5 {
      transform: rotate(150deg) translateY(-55px) rotate(-150deg);
    }

    .num-6 {
      transform: rotate(180deg) translateY(-55px) rotate(-180deg);
    }

    .num-7 {
      transform: rotate(210deg) translateY(-55px) rotate(-210deg);
    }

    .num-8 {
      transform: rotate(240deg) translateY(-55px) rotate(-240deg);
    }

    .num-9 {
      transform: rotate(270deg) translateY(-55px) rotate(-270deg);
    }

    .num-10 {
      transform: rotate(300deg) translateY(-55px) rotate(-300deg);
    }

    .num-11 {
      transform: rotate(330deg) translateY(-55px) rotate(-330deg);
    }

    .num-12 {
      transform: rotate(360deg) translateY(-55px) rotate(-360deg);
    }

    .clock-text {
      flex-shrink: 0;
      margin-top: .8rem;
    }

    .clock-time {
      font-size: 1.35rem;
      font-weight: 900;
      letter-spacing: -.035em;
    }

    .clock-date {
      margin-top: .15rem;
      font-size: .73rem;
      color: var(--color-text-muted);
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .kpi-row {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: .85rem;
      height: 100%;
    }

    .kpi-card {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: .9rem;
      padding: 1rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .kpi-icon {
      width: 48px;
      height: 48px;
      border-radius: 1rem;
      display: grid;
      place-items: center;
      flex-shrink: 0;
    }

    .kpi-val {
      display: block;
      font-size: 1.45rem;
      font-weight: 900;
      line-height: 1;
      letter-spacing: -.04em;
    }

    .kpi-lbl {
      display: block;
      margin-top: .25rem;
      color: var(--color-text-muted);
      font-size: .72rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .notes-area {
      width: 100%;
      height: 100%;
      min-height: 110px;
      resize: none;
      border-radius: var(--radius-lg);
      padding: .9rem;
      font-size: .86rem;
      font-weight: 600;
      line-height: 1.55;
      background: var(--color-surface-2);
    }

    .shortcut-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .75rem;
      overflow-y: auto;
    }

    .shortcut-card {
      min-height: 86px;
      padding: .9rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      transition: transform .18s ease, border .18s ease;
    }

    .shortcut-card:hover {
      transform: translateY(-2px);
      border-color: var(--color-blue);
    }

    .shortcut-card strong {
      font-size: .86rem;
      font-weight: 900;
    }

    .shortcut-card span {
      color: var(--color-text-muted);
      font-size: .72rem;
      font-weight: 800;
    }

    .widget[data-widget-key="personalChart"] .widget-content,
    .widget[data-widget-key="deptPie"] .widget-content,
    .widget[data-widget-key="deptBar"] .widget-content,
    .widget[data-widget-key="deptCharts"] .widget-content,
    .widget[data-widget-key="companyRevenue"] .widget-content,
    .widget[data-widget-key="companyTypes"] .widget-content,
    .widget[data-widget-key="companyDepartmentPerformance"] .widget-content {
      min-height: 0;
      overflow: auto;
    }

    .widget[data-widget-key="deptCharts"] .widget-content {
      height: 100%;
      min-height: 0;
    }

    .dept-chart-grid {
      width: 100%;
      height: 100%;
      min-height: 0;
      display: grid;
      grid-template-columns: repeat(2, minmax(260px, 1fr));
      grid-auto-rows: minmax(235px, 1fr);
      gap: .85rem;
    }

    .dept-chart-card {
      min-width: 0;
      min-height: 235px;
      display: flex;
      flex-direction: column;
      padding: .85rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      overflow: hidden;
    }

    .dept-chart-title {
      flex: 0 0 auto;
      margin-bottom: .55rem;
      color: var(--color-text);
      font-size: .78rem;
      font-weight: 900;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .chart-wrap {
      position: relative;
      flex: 1 1 auto;
      width: 100%;
      min-width: 0;
      min-height: 180px;
      height: auto;
      overflow: hidden;
    }

    .dept-chart-card .chart-wrap {
      min-height: 180px;
    }

    .chart-wrap {
      position: relative;
      flex: 1 1 auto;
      width: 100%;
      min-width: 0;
      min-height: 220px;
      height: 220px;
      overflow: hidden;
    }

    @media (max-width: 1200px) {
      .dept-chart-grid {
        grid-template-columns: repeat(2, minmax(220px, 1fr));
      }
    }

    @media (max-width: 768px) {
      .dept-chart-grid {
        grid-template-columns: 1fr;
        grid-auto-rows: minmax(240px, auto);
      }

      .dept-chart-card {
        min-height: 260px;
      }

      .dept-chart-card .chart-wrap {
        min-height: 205px;
      }
    }

    .widget[data-widget-key="personalChart"] .chart-wrap,
    .widget[data-widget-key="deptPie"] .chart-wrap,
    .widget[data-widget-key="deptBar"] .chart-wrap,
    .widget[data-widget-key="deptCharts"] .chart-wrap,
    .widget[data-widget-key="companyRevenue"] .chart-wrap,
    .widget[data-widget-key="companyTypes"] .chart-wrap,
    .widget[data-widget-key="companyDepartmentPerformance"] .chart-wrap {
      min-height: 160px;
    }

    .chart-wrap canvas {
      width: 100% !important;
      height: 100% !important;
      max-width: 100%;
      max-height: 100%;
      display: block;
    }

    .empty-state {
      min-height: 160px;
      display: grid;
      place-items: center;
      text-align: center;
      color: var(--color-text-muted);
      font-weight: 800;
      padding: 1rem;
    }

    .side-panel,
    .widget-tray {
      position: fixed;
      top: 0;
      right: -430px;
      width: min(430px, 100%);
      height: 100vh;
      z-index: 1000;
      padding: 1.4rem;
      overflow-y: auto;
      background: var(--color-surface);
      box-shadow: var(--shadow-lg);
      transition: right .25s ease;
    }

    .side-panel.open,
    .widget-tray.open {
      right: 0;
    }

    .overlay {
      position: fixed;
      inset: 0;
      display: none;
      opacity: 0;
      z-index: 999;
      background: rgba(15, 23, 42, .48);
      transition: opacity .25s ease;
    }

    .overlay.show {
      display: block;
      opacity: 1;
    }

    .panel-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--color-border);
    }

    .panel-title {
      font-size: 1.2rem;
      font-weight: 900;
      letter-spacing: -.035em;
    }

    .panel-subtitle {
      color: var(--color-text-muted);
      font-size: .8rem;
      font-weight: 700;
    }

    .close-btn {
      width: 38px;
      height: 38px;
      display: grid;
      place-items: center;
      border-radius: .9rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .setting-group {
      padding: 1rem 0;
      border-bottom: 1px solid var(--color-border);
    }

    .setting-label {
      display: block;
      margin-bottom: .55rem;
      font-size: .78rem;
      font-weight: 900;
      color: var(--color-text-muted);
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .segmented {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: .45rem;
      padding: .35rem;
      border-radius: 1rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .segmented button {
      min-height: 38px;
      border-radius: .75rem;
      font-size: .78rem;
      font-weight: 900;
      color: var(--color-text-muted);
    }

    .segmented button.active {
      background: var(--color-surface);
      color: var(--color-green-dark);
      box-shadow: var(--shadow-xs);
    }

    .check-list {
      display: flex;
      flex-direction: column;
      gap: .55rem;
    }

    .check-row {
      min-height: 44px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: .75rem;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-md);
      background: var(--color-surface-2);
      font-size: .86rem;
      font-weight: 800;
    }

    .switch {
      width: 44px;
      height: 24px;
      position: relative;
      flex-shrink: 0;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      inset: 0;
      border-radius: 999px;
      background: #d1d5db;
      transition: .18s ease;
    }

    .slider:before {
      content: '';
      position: absolute;
      width: 18px;
      height: 18px;
      left: 3px;
      top: 3px;
      border-radius: 50%;
      background: #fff;
      transition: .18s ease;
      box-shadow: 0 1px 3px rgba(15, 23, 42, .25);
    }

    .switch input:checked+.slider {
      background: var(--color-green);
    }

    .switch input:checked+.slider:before {
      transform: translateX(20px);
    }

    .tray-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: .95rem;
      margin-bottom: .75rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      font-weight: 900;
      cursor: pointer;
      transition: transform .18s ease, border .18s ease, color .18s ease;
    }

    .tray-item:hover {
      transform: translateX(-2px);
      border-color: var(--color-green);
      color: var(--color-green-dark);
    }

    .toast {
      position: fixed;
      left: 50%;
      bottom: 1.35rem;
      transform: translateX(-50%) translateY(20px);
      opacity: 0;
      pointer-events: none;
      z-index: 2000;
      display: inline-flex;
      align-items: center;
      gap: .55rem;
      padding: .8rem 1rem;
      border-radius: 999px;
      background: #111827;
      color: #fff;
      font-size: .84rem;
      font-weight: 800;
      box-shadow: var(--shadow-lg);
      transition: .2s ease;
    }

    .toast.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }

    .dash-notes-wrapper {
      height: 100%;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: .85rem;
    }

    .dash-notes-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .65rem;
      flex-shrink: 0;
    }

    .dash-notes-tabs {
      display: inline-flex;
      gap: .35rem;
      padding: .28rem;
      border-radius: .9rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      overflow-x: auto;
      scrollbar-width: none;
    }

    .dash-notes-tabs::-webkit-scrollbar {
      display: none;
    }

    .dash-notes-tab {
      min-height: 34px;
      padding: .45rem .7rem;
      border-radius: .72rem;
      display: inline-flex;
      align-items: center;
      gap: .42rem;
      color: var(--color-text-muted);
      font-size: .75rem;
      font-weight: 900;
      white-space: nowrap;
    }

    .dash-notes-tab.active {
      background: var(--color-surface);
      color: var(--color-green-dark);
      box-shadow: var(--shadow-xs);
    }

    .dash-notes-add {
      min-height: 34px;
      padding: .45rem .7rem;
      border-radius: .75rem;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      color: #fff;
      background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
      font-size: .75rem;
      font-weight: 900;
      box-shadow: var(--shadow-xs);
      white-space: nowrap;
    }

    .dash-notes-grid {
      flex: 1;
      min-height: 0;
      overflow-y: auto;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      align-content: start;
      gap: .75rem;
      padding-right: .25rem;
    }

    .dash-note-card {
      min-height: 132px;
      display: flex;
      flex-direction: column;
      gap: .55rem;
      padding: .85rem;
      border-radius: var(--radius-lg);
      border: 1px solid rgba(15, 23, 42, .07);
      box-shadow: var(--shadow-xs);
      transition: transform .18s ease, box-shadow .18s ease;
    }

    .dash-note-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-sm);
    }

    .dash-note-card-header {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr);
      gap: .55rem;
      align-items: start;
    }

    .dash-note-checkbox {
      width: 18px;
      height: 18px;
      margin-top: .1rem;
      accent-color: var(--color-green);
      cursor: pointer;
    }

    .dash-note-title {
      min-width: 0;
      color: rgba(17, 24, 39, .88);
      font-size: .86rem;
      line-height: 1.25;
      font-weight: 900;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .dash-note-body {
      flex: 1;
      color: rgba(17, 24, 39, .72);
      font-size: .76rem;
      line-height: 1.45;
      font-weight: 700;
      white-space: pre-wrap;
      word-break: break-word;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 4;
      -webkit-box-orient: vertical;
    }

    .dash-note-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .45rem;
      flex-wrap: wrap;
    }

    .dash-note-badge {
      max-width: 100%;
      display: inline-flex;
      align-items: center;
      padding: .18rem .5rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, .58);
      color: rgba(17, 24, 39, .72);
      font-size: .62rem;
      line-height: 1;
      font-weight: 900;
      text-transform: uppercase;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .dash-note-date {
      color: rgba(17, 24, 39, .55);
      font-size: .66rem;
      font-weight: 900;
    }

    .dash-note-modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 2100;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background: rgba(15, 23, 42, .5);
    }

    .dash-note-modal-overlay.show {
      display: flex;
    }

    .dash-note-modal {
      width: min(460px, 100%);
      max-height: calc(100vh - 2rem);
      overflow-y: auto;
      padding: 1.35rem;
      border-radius: var(--radius-xl);
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      box-shadow: var(--shadow-lg);
    }

    .dash-note-form-group {
      margin-bottom: .9rem;
    }

    .dash-note-form-row {
      display: flex;
      gap: .85rem;
    }

    .dash-note-control {
      width: 100%;
      height: 42px;
      padding: 0 .85rem;
      border-radius: var(--radius-md);
      font-size: .84rem;
      font-weight: 800;
    }

    .dash-note-textarea {
      width: 100%;
      min-height: 130px;
      resize: vertical;
      padding: .85rem;
      border-radius: var(--radius-md);
      font-size: .86rem;
      line-height: 1.55;
      font-weight: 700;
    }

    @media (max-width: 768px) {

      .dash-notes-toolbar,
      .dash-note-form-row {
        flex-direction: column;
        align-items: stretch;
      }

      .dash-notes-tabs,
      .dash-notes-add {
        width: 100%;
      }

      .dash-notes-tab {
        flex: 1;
        justify-content: center;
      }

      .dash-notes-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 1200px) {

      .col-span-1,
      .col-span-2,
      .col-span-3,
      .col-span-4,
      .col-span-5 {
        grid-column: span 6;
      }

      .col-span-6,
      .col-span-7,
      .col-span-8,
      .col-span-9,
      .col-span-10,
      .col-span-11 {
        grid-column: span 12;
      }

      .dashboard-hero {
        grid-template-columns: 1fr;
      }

      .hero-actions {
        justify-content: flex-start;
      }
    }

    @media (max-width: 768px) {
      .main-content {
        padding: .9rem;
      }

      .widget {
        grid-column: span 12 !important;
        grid-row: auto !important;
        min-height: 260px;
      }

      .toolbar-card,
      .toolbar-right,
      .dashboard-search,
      .select-control,
      .btn {
        width: 100%;
      }

      .tabs {
        width: 100%;
      }

      .tab-btn {
        flex: 1 0 auto;
      }

      .feed-bar {
        grid-template-columns: auto minmax(0, 1fr);
      }

      .feed-actions {
        display: none;
      }

      .stat-grid,
      .kpi-row,
      .shortcut-grid {
        grid-template-columns: 1fr;
      }

      .focus-toolbar {
        grid-template-columns: 1fr;
      }
    }

    .dash-notes-toolbar {
      display: flex;
      flex-direction: column;
      gap: .65rem;
      flex-shrink: 0;
    }

    .dash-notes-toolbar-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .65rem;
    }

    .dash-notes-search {
      position: relative;
      display: block;
      width: 100%;
    }

    .dash-notes-search i {
      position: absolute;
      left: .78rem;
      top: 50%;
      width: 15px;
      height: 15px;
      color: var(--color-text-muted);
      transform: translateY(-50%);
      pointer-events: none;
    }

    .dash-notes-search input {
      width: 100%;
      height: 38px;
      padding: 0 .8rem 0 2.15rem;
      border-radius: .8rem;
      font-size: .78rem;
      font-weight: 800;
      background: var(--color-surface);
    }

    @media (max-width: 768px) {

      .dash-notes-toolbar-top,
      .dash-note-form-row {
        flex-direction: column;
        align-items: stretch;
      }

      .dash-notes-tabs,
      .dash-notes-add {
        width: 100%;
      }

      .dash-notes-tab {
        flex: 1;
        justify-content: center;
      }

      .dash-notes-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
  <style>
    /* =========================
                BREAKING NEWS BAR
                ========================= */
    .sa-bn {
      display: flex;
      align-items: stretch;
      width: 100%;
      min-height: 58px;
      border-radius: var(--radius-xl);
      overflow: hidden;
      background: linear-gradient(135deg, var(--color-blue), var(--color-green));
      color: #fff;
      position: relative;
      box-shadow: var(--shadow-xs);
    }

    .sa-bn.hidden {
      display: none !important;
    }

    .sa-bn-label {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      min-height: 58px;
      background: var(--color-danger);
      border-right: 1px solid rgba(255, 255, 255, .28);
      flex-shrink: 0;
    }

    .sa-bn-label--breaking-info {
      background: var(--color-blue-dark);
    }

    .sa-bn-label--breaking-warning {
      background: var(--color-warning);
    }

    .sa-bn-label--breaking-danger {
      background: var(--color-danger);
    }

    .sa-bn-label--breaking-success {
      background: var(--color-green-dark);
    }

    .sa-bn-label-icon {
      font-size: 1.35rem;
      color: #fff;
    }

    .sa-bn-label-blink {
      animation: saBnBlink 1.2s ease-in-out infinite;
    }

    @keyframes saBnBlink {
      0% {
        transform: scale(1);
        opacity: 1;
      }

      50% {
        transform: scale(1.16);
        opacity: .62;
      }

      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    .sa-bn-main {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: .45rem .95rem;
    }

    .sa-bn-track {
      position: relative;
      overflow: hidden;
      min-height: 26px;
      display: flex;
      align-items: center;
    }

    .sa-bn-text {
      position: absolute;
      left: 0;
      white-space: nowrap;
      font-size: .88rem;
      font-weight: 700;
      color: #fff;
      animation: saTicker 16s linear infinite;
    }

    @keyframes saTicker {
      0% {
        transform: translateX(100%);
      }

      100% {
        transform: translateX(-120%);
      }
    }

    .sa-bn.is-paused .sa-bn-text {
      animation-play-state: paused;
    }

    .sa-bn-meta {
      display: flex;
      align-items: center;
      gap: .55rem;
      margin-top: .15rem;
      font-size: .72rem;
      font-weight: 800;
      color: rgba(255, 255, 255, .88);
    }

    .sa-bn-pill {
      display: inline-flex;
      align-items: center;
      padding: .16rem .58rem;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, .46);
      color: #fff;
      font-size: .68rem;
      font-weight: 900;
      line-height: 1;
    }

    .sa-bn-time {
      display: inline-flex;
      align-items: center;
      gap: .25rem;
      color: #fff;
    }

    .sa-bn-creator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .45rem;
      padding: 0 .4rem;
      flex-shrink: 0;
    }

    .sa-bn-avatar {
      width: 31px;
      height: 31px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid rgba(255, 255, 255, .78);
    }

    .sa-bn-creator-name {
      font-size: .72rem;
      font-weight: 900;
      color: #fff;
      white-space: nowrap;
    }

    .sa-bn-audio {
      display: flex;
      align-items: center;
      gap: .65rem;
      margin-top: .35rem;
    }

    .sa-bn-audio.hidden {
      display: none !important;
    }

    .sa-bn-audio-wave {
      position: relative;
      flex: 1;
      height: 18px;
      border-radius: 999px;
      overflow: hidden;
      background: rgba(255, 255, 255, .18);
    }

    .sa-bn-audio-wave-bg {
      position: absolute;
      inset: 0;
      background-image: linear-gradient(135deg,
          rgba(255, 255, 255, .22) 0%,
          rgba(255, 255, 255, .08) 45%,
          rgba(255, 255, 255, .22) 100%);
    }

    .sa-bn-audio-progress {
      position: absolute;
      inset: 0;
      width: 0%;
      background: linear-gradient(90deg, var(--color-green), var(--color-green-light));
      opacity: .95;
    }

    .sa-bn-audio-handle {
      position: absolute;
      top: 50%;
      left: 0%;
      width: 4px;
      height: 80%;
      border-radius: 999px;
      background: #111827;
      transform: translate(-50%, -50%);
    }

    .sa-bn-audio-range {
      position: absolute;
      inset: 0;
      opacity: 0;
      cursor: pointer;
    }

    .sa-bn-audio-time {
      min-width: 80px;
      display: inline-flex;
      justify-content: flex-end;
      gap: .2rem;
      font-size: .72rem;
      font-weight: 800;
      color: #fff;
    }

    .sa-bn-controls {
      display: flex;
      align-items: center;
      gap: .2rem;
      padding: 0 .7rem 0 .35rem;
      flex-shrink: 0;
    }

    .sa-bn-btn {
      width: 32px;
      height: 32px;
      display: grid;
      place-items: center;
      border-radius: .85rem;
      color: #fff;
      background: rgba(255, 255, 255, .12);
      transition: all .16s ease;
    }

    .sa-bn-btn:hover {
      background: rgba(255, 255, 255, .22);
      transform: translateY(-1px);
    }

    .sa-bn-btn i {
      font-size: 1.35rem;
      pointer-events: none;
    }

    @media (max-width: 768px) {
      .sa-bn {
        min-height: 64px;
        border-radius: var(--radius-lg);
      }

      .sa-bn-label {
        width: 48px;
      }

      .sa-bn-text {
        font-size: .78rem;
      }

      .sa-bn-controls {
        padding-right: .35rem;
      }

      .sa-bn-btn {
        width: 28px;
        height: 28px;
      }

      .sa-bn-creator-name {
        display: none;
      }
    }

    .shortcut-widget-wrap {
      height: 100%;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: .75rem;
    }

    .shortcut-actions {
      display: flex;
      justify-content: flex-end;
      flex-shrink: 0;
    }

    .shortcut-manage-btn {
      min-height: 34px;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      padding: .45rem .7rem;
      border-radius: .75rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      color: var(--color-text-muted);
      font-size: .74rem;
      font-weight: 900;
    }

    .shortcut-manage-btn:hover {
      border-color: var(--color-blue);
      color: var(--color-blue-dark);
    }

    .shortcut-card {
      text-align: left;
    }

    .shortcut-card i,
    .shortcut-card svg {
      width: 18px;
      height: 18px;
      margin-bottom: .45rem;
      color: var(--color-green-dark);
    }

    .shortcut-card-top {
      display: flex;
      flex-direction: column;
      gap: .25rem;
    }

    .shortcut-manage-list,
    .shortcut-available-list {
      display: flex;
      flex-direction: column;
      gap: .65rem;
    }

    .shortcut-manage-row,
    .shortcut-available-row {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: .7rem;
      padding: .8rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .shortcut-manage-row {
      cursor: grab;
    }

    .shortcut-manage-row.dragging {
      opacity: .45;
    }

    .shortcut-manage-icon {
      width: 34px;
      height: 34px;
      display: grid;
      place-items: center;
      border-radius: .85rem;
      background: var(--color-green-soft);
      color: var(--color-green-dark);
      flex-shrink: 0;
    }

    .shortcut-manage-title {
      min-width: 0;
    }

    .shortcut-manage-title strong {
      display: block;
      font-size: .86rem;
      font-weight: 900;
      color: var(--color-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .shortcut-manage-title span {
      display: block;
      margin-top: .1rem;
      font-size: .72rem;
      font-weight: 800;
      color: var(--color-text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .shortcut-row-actions {
      display: flex;
      align-items: center;
      gap: .35rem;
    }

    .shortcut-icon-btn {
      width: 32px;
      height: 32px;
      display: grid;
      place-items: center;
      border-radius: .75rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      color: var(--color-text-muted);
    }

    .shortcut-icon-btn:hover {
      color: var(--color-blue-dark);
      border-color: var(--color-blue);
    }

    .shortcut-icon-btn.danger:hover {
      color: var(--color-danger);
      border-color: rgba(229, 6, 86, .35);
      background: var(--color-danger-soft);
    }

    .shortcut-edit-form {
      display: none;
      grid-column: 1 / -1;
      gap: .55rem;
      margin-top: .65rem;
    }

    .shortcut-manage-row.is-editing .shortcut-edit-form {
      display: grid;
    }

    .shortcut-edit-form input,
    .shortcut-edit-form select {
      width: 100%;
      height: 38px;
      padding: 0 .75rem;
      border-radius: .8rem;
      font-size: .78rem;
      font-weight: 800;
    }

    .shortcut-edit-actions {
      display: flex;
      gap: .5rem;
    }

    .shortcut-edit-actions .btn {
      flex: 1;
      min-height: 38px;
    }
  </style>

  <style>
    .absence-widget-content {
      gap: .9rem;
    }

    .absence-profile-box {
      display: flex;
      align-items: center;
      gap: .85rem;
      padding: .9rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .absence-avatar {
      width: 54px;
      height: 54px;
      border-radius: 999px;
      object-fit: cover;
      border: 3px solid #fff;
      box-shadow: var(--shadow-xs);
    }

    .absence-profile-box strong {
      display: block;
      font-size: .94rem;
      font-weight: 900;
      color: var(--color-text);
    }

    .absence-profile-box span {
      display: block;
      margin-top: .15rem;
      font-size: .74rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .absence-action-box {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: .75rem;
      padding: .95rem;
      border-radius: var(--radius-lg);
      background: linear-gradient(135deg, var(--color-green-soft), var(--color-blue-soft));
      border: 1px solid rgba(139, 195, 74, .24);
    }

    .absence-action-box .btn {
      width: 100%;
    }

    .absence-action-box p {
      margin: 0;
      color: var(--color-text-muted);
      font-size: .76rem;
      font-weight: 800;
      line-height: 1.45;
      text-align: center;
    }

    .absence-modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 2300;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background: rgba(15, 23, 42, .52);
    }

    .absence-modal-overlay.show {
      display: flex;
    }

    .absence-modal {
      width: min(560px, 100%);
      max-height: calc(100vh - 2rem);
      overflow-y: auto;
      padding: 1.35rem;
      border-radius: var(--radius-xl);
      background: var(--color-surface);
      border: 1px solid var(--color-border);
      box-shadow: var(--shadow-lg);
    }

    .absence-modal-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--color-border);
    }

    .absence-modal-header h3 {
      margin: 0;
      font-size: 1.18rem;
      font-weight: 900;
      letter-spacing: -.035em;
    }

    .absence-modal-header p {
      margin: .15rem 0 0;
      color: var(--color-text-muted);
      font-size: .8rem;
      font-weight: 700;
    }

    .absence-type-switch {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .55rem;
      margin-bottom: 1rem;
    }

    .absence-type-option {
      cursor: pointer;
    }

    .absence-type-option input {
      display: none;
    }

    .absence-type-option span {
      min-height: 46px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      border-radius: var(--radius-md);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      color: var(--color-text-muted);
      font-size: .84rem;
      font-weight: 900;
      transition: all .18s ease;
    }

    .absence-type-option.active span {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
      border-color: rgba(139, 195, 74, .45);
    }

    .absence-form-row {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .75rem;
    }

    .absence-form-group {
      margin-bottom: .85rem;
    }

    .absence-form-group label {
      display: block;
      margin-bottom: .38rem;
      color: var(--color-text-muted);
      font-size: .74rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .045em;
    }

    .absence-form-group input,
    .absence-form-group select,
    .absence-form-group textarea {
      width: 100%;
      border-radius: var(--radius-md);
      font-size: .84rem;
      font-weight: 800;
    }

    .absence-form-group input,
    .absence-form-group select {
      height: 42px;
      padding: 0 .85rem;
    }

    .absence-form-group textarea {
      min-height: 105px;
      resize: vertical;
      padding: .85rem;
      line-height: 1.5;
    }

    .absence-form-group small {
      display: block;
      margin-top: .35rem;
      color: var(--color-text-muted);
      font-size: .72rem;
      font-weight: 700;
    }

    .absence-modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: .65rem;
      padding-top: .55rem;
    }

    @media (max-width: 768px) {

      .absence-form-row,
      .absence-type-switch {
        grid-template-columns: 1fr;
      }

      .absence-modal-actions {
        flex-direction: column;
      }

      .absence-modal-actions .btn {
        width: 100%;
      }
    }
  </style>

  <style>
    .dept-toolbar {
      display: grid;
      grid-template-columns: minmax(220px, 1fr) 160px 160px auto;
      gap: .75rem;
      align-items: end;
      margin-bottom: .9rem;
    }

    .dept-control-group label {
      display: block;
      margin-bottom: .35rem;
      color: var(--color-text-muted);
      font-size: .7rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .05em;
    }

    .dept-control-group input,
    .dept-control-group select {
      width: 100%;
      height: 42px;
      padding: 0 .85rem;
      border-radius: .9rem;
      font-size: .84rem;
      font-weight: 800;
    }

    .dept-kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: .75rem;
    }

    .dept-kpi-card {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: .85rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .dept-kpi-icon {
      width: 42px;
      height: 42px;
      display: grid;
      place-items: center;
      flex-shrink: 0;
      border-radius: .95rem;
    }

    .dept-kpi-icon.blue {
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
    }

    .dept-kpi-icon.green {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .dept-kpi-icon.warning {
      background: var(--color-warning-soft);
      color: #b87400;
    }

    .dept-kpi-card strong {
      display: block;
      font-size: 1.25rem;
      line-height: 1;
      font-weight: 900;
      letter-spacing: -.04em;
    }

    .dept-kpi-card span:last-child {
      display: block;
      margin-top: .25rem;
      color: var(--color-text-muted);
      font-size: .7rem;
      font-weight: 900;
      text-transform: uppercase;
    }

    .dept-team-list,
    .dept-recent-list,
    .dept-change-list {
      height: 100%;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: .55rem;
      padding-right: .25rem;
    }

    .dept-team-row,
    .dept-recent-row,
    .dept-change-row {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: .7rem;
      align-items: center;
      padding: .75rem;
      border-radius: var(--radius-md);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .dept-avatar {
      width: 38px;
      height: 38px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #fff;
      box-shadow: var(--shadow-xs);
    }

    .dept-row-title {
      min-width: 0;
      font-size: .82rem;
      font-weight: 900;
      color: var(--color-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dept-row-meta {
      margin-top: .15rem;
      color: var(--color-text-muted);
      font-size: .7rem;
      font-weight: 800;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dept-mini-pill {
      display: inline-flex;
      align-items: center;
      padding: .22rem .5rem;
      border-radius: 999px;
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
      font-size: .66rem;
      font-weight: 900;
      white-space: nowrap;
    }

    .dept-chart-grid {
      height: 100%;
      min-height: 0;
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .8rem;
    }

    .dept-chart-card {
      min-height: 0;
      display: flex;
      flex-direction: column;
      padding: .75rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .dept-chart-title {
      margin-bottom: .55rem;
      color: var(--color-text-muted);
      font-size: .72rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .dept-recent-tabs {
      display: flex;
      flex-wrap: wrap;
      gap: .35rem;
      margin-bottom: .7rem;
    }

    .dept-recent-tabs button {
      min-height: 30px;
      padding: .35rem .55rem;
      border-radius: .7rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      color: var(--color-text-muted);
      font-size: .7rem;
      font-weight: 900;
    }

    .dept-recent-tabs button.active {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
      border-color: rgba(139, 195, 74, .4);
    }

    [data-widget-key="deptPie"] .chart-wrap,
    [data-widget-key="deptBar"] .chart-wrap,
    [data-widget-key="deptCharts"] .chart-wrap {
      min-height: 240px;
    }

    @media (max-width: 1100px) {

      .dept-toolbar,
      .dept-kpi-grid,
      .dept-chart-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 768px) {

      .dept-toolbar,
      .dept-kpi-grid,
      .dept-chart-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <style>
    .company-main-area-card {
      height: 100%;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: .8rem;
    }

    .company-main-box {
      padding: 1rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: linear-gradient(135deg, var(--color-green-soft), var(--color-blue-soft));
    }

    .company-main-box strong {
      display: block;
      font-size: 1.1rem;
      font-weight: 900;
      color: var(--color-text);
    }

    .company-main-box span {
      display: block;
      margin-top: .25rem;
      font-size: .78rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .company-main-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .6rem;
    }

    .company-main-mini {
      padding: .75rem;
      border-radius: var(--radius-md);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .company-main-mini strong {
      display: block;
      font-size: 1rem;
      font-weight: 900;
    }

    .company-main-mini span {
      display: block;
      margin-top: .15rem;
      font-size: .68rem;
      font-weight: 900;
      color: var(--color-text-muted);
      text-transform: uppercase;
    }

    .company-main-area-card {
      height: 100%;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: .8rem;
    }

    .company-main-box {
      padding: 1rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: linear-gradient(135deg, var(--color-green-soft), var(--color-blue-soft));
    }

    .company-main-box strong {
      display: block;
      font-size: 1.1rem;
      font-weight: 900;
      color: var(--color-text);
    }

    .company-main-box span {
      display: block;
      margin-top: .25rem;
      font-size: .78rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .company-main-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .6rem;
    }

    .company-main-mini {
      padding: .75rem;
      border-radius: var(--radius-md);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .company-main-mini strong {
      display: block;
      font-size: 1rem;
      font-weight: 900;
    }

    .company-main-mini span {
      display: block;
      margin-top: .15rem;
      font-size: .68rem;
      font-weight: 900;
      color: var(--color-text-muted);
      text-transform: uppercase;
    }

    .dash-cal-widget {
      height: 100%;
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: .75rem;
    }

    .dash-cal-toolbar {
      display: grid;
      grid-template-columns: 36px minmax(0, 1fr) 36px minmax(150px, 220px);
      gap: .55rem;
      align-items: center;
    }

    .dash-cal-nav {
      width: 36px;
      height: 36px;
      display: grid;
      place-items: center;
      border-radius: .8rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      color: var(--color-text-muted);
    }

    .dash-cal-title-wrap strong {
      display: block;
      font-size: .92rem;
      font-weight: 900;
    }

    .dash-cal-title-wrap span {
      display: block;
      font-size: .7rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .dash-cal-select {
      height: 36px;
      min-width: 0;
      padding: 0 .7rem;
      border-radius: .8rem;
      font-size: .76rem;
      font-weight: 800;
    }

    .dash-cal-weekdays,
    .dash-cal-grid {
      display: grid;
      grid-template-columns: repeat(7, minmax(0, 1fr));
      gap: .35rem;
    }

    .dash-cal-weekdays span {
      text-align: center;
      font-size: .66rem;
      font-weight: 900;
      color: var(--color-text-muted);
      text-transform: uppercase;
    }

    .dash-cal-grid {
      min-height: 250px;
    }

    .dash-cal-day {
      min-height: 56px;
      padding: .42rem;
      border-radius: .75rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      display: flex;
      flex-direction: column;
      gap: .25rem;
      cursor: pointer;
      transition: .16s ease;
    }

    .dash-cal-day:hover {
      transform: translateY(-1px);
      border-color: var(--color-blue);
    }

    .dash-cal-day.is-muted {
      opacity: .38;
    }

    .dash-cal-day.is-today {
      border-color: var(--color-green);
      background: var(--color-green-soft);
    }

    .dash-cal-day.is-selected {
      border-color: var(--color-blue);
      background: var(--color-blue-soft);
    }

    .dash-cal-day-number {
      font-size: .78rem;
      font-weight: 900;
    }

    .dash-cal-day-dots {
      display: flex;
      flex-wrap: wrap;
      gap: .18rem;
      margin-top: auto;
    }

    .dash-cal-dot {
      width: 7px;
      height: 7px;
      border-radius: 999px;
      background: var(--color-blue);
    }

    .dash-cal-dot.report {
      background: var(--color-green);
    }

    .dash-cal-dot.missing {
      background: var(--color-warning);
    }

    .dash-cal-dot.ticket {
      background: var(--color-danger);
    }

    .dash-cal-day-card {
      min-height: 0;
      flex: 1;
      display: flex;
      flex-direction: column;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      overflow: hidden;
    }

    .dash-cal-day-head {
      flex-shrink: 0;
      padding: .75rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--color-border);
    }

    .dash-cal-day-head strong {
      font-size: .86rem;
      font-weight: 900;
    }

    .dash-cal-day-head span {
      font-size: .72rem;
      font-weight: 900;
      color: var(--color-text-muted);
    }

    .dash-cal-events {
      flex: 1;
      min-height: 0;
      overflow-y: auto;
      padding: .65rem;
      display: flex;
      flex-direction: column;
      gap: .55rem;
    }

    .dash-cal-event {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: .65rem;
      align-items: center;
      padding: .7rem;
      border-radius: var(--radius-md);
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      cursor: pointer;
    }

    .dash-cal-event-time {
      min-width: 58px;
      font-size: .68rem;
      font-weight: 900;
      color: var(--color-blue-dark);
    }

    .dash-cal-event-title {
      font-size: .82rem;
      font-weight: 900;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dash-cal-event-meta {
      margin-top: .12rem;
      font-size: .68rem;
      font-weight: 800;
      color: var(--color-text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dash-cal-badges {
      display: flex;
      flex-direction: column;
      gap: .25rem;
      align-items: flex-end;
    }

    .dash-cal-badge {
      padding: .18rem .45rem;
      border-radius: 999px;
      font-size: .62rem;
      font-weight: 900;
      white-space: nowrap;
    }

    .dash-cal-badge.report {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .dash-cal-badge.missing {
      background: var(--color-warning-soft);
      color: #b87400;
    }

    .dash-cal-badge.ticket {
      background: var(--color-danger-soft);
      color: var(--color-danger);
    }

    .dash-cal-modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 2400;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background: rgba(15, 23, 42, .55);
    }

    .dash-cal-modal-overlay.show {
      display: flex;
    }

    .dash-cal-modal {
      width: min(920px, 100%);
      max-height: calc(100vh - 2rem);
      overflow-y: auto;
      padding: 1.35rem;
      border-radius: var(--radius-xl);
      background: var(--color-surface);
      border: 1px solid var(--color-border);
      box-shadow: var(--shadow-lg);
    }

    .dash-cal-modal-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1rem;
      padding-bottom: 1rem;
      margin-bottom: 1rem;
      border-bottom: 1px solid var(--color-border);
    }

    .dash-cal-modal-header h3 {
      margin: 0;
      font-size: 1.2rem;
      font-weight: 900;
    }

    .dash-cal-modal-header p {
      margin: .2rem 0 0;
      font-size: .78rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .dash-cal-modal-grid {
      display: grid;
      grid-template-columns: 1.15fr .85fr;
      gap: .9rem;
    }

    .dash-cal-detail-card {
      padding: .9rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
    }

    .dash-cal-detail-card h4 {
      margin: 0 0 .65rem;
      font-size: .82rem;
      font-weight: 900;
      color: var(--color-text-muted);
      text-transform: uppercase;
    }

    .dash-cal-detail-row {
      display: flex;
      justify-content: space-between;
      gap: .8rem;
      padding: .45rem 0;
      border-bottom: 1px solid rgba(229, 231, 235, .75);
      font-size: .78rem;
      font-weight: 800;
    }

    .dash-cal-detail-row:last-child {
      border-bottom: 0;
    }

    .dash-cal-detail-row span:first-child {
      color: var(--color-text-muted);
    }

    .dash-cal-map {
      min-height: 210px;
      display: grid;
      place-items: center;
      border-radius: var(--radius-lg);
      background: linear-gradient(135deg, var(--color-blue-soft), var(--color-green-soft));
      border: 1px solid var(--color-border);
      text-align: center;
      padding: 1rem;
    }

    .dash-cal-map a {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      margin-top: .65rem;
      color: var(--color-blue-dark);
      font-weight: 900;
      text-decoration: none;
    }

    @media (max-width: 900px) {

      .dash-cal-toolbar,
      .dash-cal-modal-grid {
        grid-template-columns: 1fr;
      }
    }

    .employee-status-cards {
      display: grid;
      grid-template-columns: repeat(2, minmax(150px, 1fr));
      gap: .65rem;
      align-items: stretch;
      min-width: 320px;
    }

    .employee-status-card {
      position: relative;
      min-height: 58px;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: .65rem;
      padding: .7rem .75rem;
      border-radius: 1.1rem;
      border: 1px solid var(--color-border);
      background: rgba(255, 255, 255, .86);
      box-shadow: var(--shadow-xs);
      cursor: pointer;
      transition: transform .18s ease, box-shadow .18s ease, border .18s ease;
    }

    .employee-status-card:hover,
    .employee-status-card.open {
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .employee-status-card.active {
      background:
        linear-gradient(135deg, rgba(139, 195, 74, .16), rgba(255, 255, 255, .94));
      border-color: rgba(139, 195, 74, .35);
    }

    .employee-status-card.inactive {
      background:
        linear-gradient(135deg, rgba(248, 172, 0, .16), rgba(255, 255, 255, .94));
      border-color: rgba(248, 172, 0, .35);
    }

    .employee-status-icon {
      width: 38px;
      height: 38px;
      display: grid;
      place-items: center;
      border-radius: .95rem;
      flex-shrink: 0;
    }

    .employee-status-card.active .employee-status-icon {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .employee-status-card.inactive .employee-status-icon {
      background: var(--color-warning-soft);
      color: #b87400;
    }

    .employee-status-icon svg {
      width: 19px;
      height: 19px;
    }

    .employee-status-info {
      min-width: 0;
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }

    .employee-status-info strong {
      font-size: 1.22rem;
      font-weight: 900;
      letter-spacing: -.04em;
      color: var(--color-text);
    }

    .employee-status-info small {
      margin-top: .16rem;
      font-size: .68rem;
      font-weight: 900;
      color: var(--color-text-muted);
      text-transform: uppercase;
      letter-spacing: .045em;
      white-space: nowrap;
    }

    .employee-status-chevron {
      color: var(--color-text-muted);
      display: grid;
      place-items: center;
    }

    .employee-status-chevron svg {
      width: 16px;
      height: 16px;
      transition: transform .18s ease;
    }

    .employee-status-card.open .employee-status-chevron svg {
      transform: rotate(180deg);
    }

    .employee-status-dropdown {
      position: absolute;
      top: calc(100% + .55rem);
      left: 0;
      width: min(380px, 92vw);
      display: none;
      z-index: 500;
      padding: .8rem;
      border-radius: var(--radius-lg);
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      box-shadow: var(--shadow-lg);
    }

    .employee-status-card.open .employee-status-dropdown {
      display: block;
    }

    .employee-status-dropdown-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      padding-bottom: .65rem;
      margin-bottom: .65rem;
      border-bottom: 1px solid var(--color-border);
    }

    .employee-status-dropdown-head strong {
      font-size: .9rem;
      font-weight: 900;
    }

    .employee-status-dropdown-head span {
      font-size: .72rem;
      color: var(--color-text-muted);
      font-weight: 800;
    }

    .employee-status-list {
      max-height: 340px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: .45rem;
      padding-right: .2rem;
    }

    .employee-status-person {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: .65rem;
      padding: .55rem;
      border-radius: .9rem;
      text-decoration: none;
      color: var(--color-text);
      background: var(--color-surface-2);
      border: 1px solid transparent;
      transition: transform .16s ease, border .16s ease, background .16s ease;
    }

    .employee-status-person:hover {
      transform: translateX(2px);
      border-color: var(--color-blue);
      background: #fff;
    }

    .employee-status-avatar {
      width: 36px;
      height: 36px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #fff;
      box-shadow: var(--shadow-xs);
      background: var(--color-surface);
    }

    .employee-status-person-name {
      min-width: 0;
    }

    .employee-status-person-name strong {
      display: block;
      font-size: .82rem;
      font-weight: 900;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .employee-status-person-name span {
      display: inline-flex;
      margin-top: .12rem;
      font-size: .68rem;
      font-weight: 900;
      color: var(--color-text-muted);
    }

    .employee-status-badge {
      padding: .18rem .48rem;
      border-radius: 999px;
      font-size: .62rem;
      font-weight: 900;
      white-space: nowrap;
    }

    .employee-status-badge.active {
      color: var(--color-green-dark);
      background: var(--color-green-soft);
    }

    .employee-status-badge.sick {
      color: var(--color-danger);
      background: var(--color-danger-soft);
    }

    .employee-status-badge.holiday {
      color: #b87400;
      background: var(--color-warning-soft);
    }

    .employee-status-badge.inactive {
      color: var(--color-text-muted);
      background: #f3f4f6;
    }

    .employee-status-empty {
      min-height: 90px;
      display: grid;
      place-items: center;
      color: var(--color-text-muted);
      font-size: .78rem;
      font-weight: 800;
      text-align: center;
    }

    @media (max-width: 1200px) {
      .employee-status-cards {
        grid-template-columns: 1fr 1fr;
        width: 100%;
        min-width: 0;
      }
    }

    @media (max-width: 768px) {
      .employee-status-cards {
        grid-template-columns: 1fr;
      }

      .employee-status-dropdown {
        position: fixed;
        top: auto;
        left: 1rem;
        right: 1rem;
        bottom: 1rem;
        width: auto;
        max-height: 70vh;
      }
    }
  </style>

  <style>
    .dashboard-topbar-right {
      display: grid;
      grid-template-columns: minmax(280px, 420px) auto;
      align-items: center;
      justify-content: flex-end;
      gap: .65rem;
      min-width: 0;
    }

    .dashboard-topbar .dashboard-search {
      min-width: 0;
      width: 100%;
    }

    .dashboard-menu-wrap {
      position: relative;
      flex-shrink: 0;
    }

    .dashboard-menu-btn {
      min-height: 42px;
      white-space: nowrap;
    }

    .dashboard-menu-btn svg:last-child {
      width: 15px;
      height: 15px;
      transition: transform .18s ease;
    }

    .dashboard-menu-wrap.open .dashboard-menu-btn svg:last-child {
      transform: rotate(180deg);
    }

    .dashboard-menu {
      position: absolute;
      top: calc(100% + .55rem);
      right: 0;
      z-index: 1200;
      width: min(360px, calc(100vw - 2rem));
      display: none;
      padding: .85rem;
      border-radius: var(--radius-xl);
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      box-shadow: var(--shadow-lg);
    }

    .dashboard-menu-wrap.open .dashboard-menu {
      display: block;
      animation: menuFadeIn .16s ease both;
    }

    @keyframes menuFadeIn {
      from {
        opacity: 0;
        transform: translateY(-6px) scale(.98);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .dashboard-menu-section+.dashboard-menu-section {
      margin-top: .8rem;
      padding-top: .8rem;
      border-top: 1px solid var(--color-border);
    }

    .dashboard-menu-label {
      display: block;
      margin-bottom: .45rem;
      color: var(--color-text-muted);
      font-size: .68rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .dashboard-menu-select {
      width: 100%;
      min-width: 0;
    }

    .dashboard-menu-item {
      width: 100%;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr);
      gap: .7rem;
      align-items: center;
      padding: .75rem;
      border-radius: var(--radius-lg);
      border: 1px solid transparent;
      background: transparent;
      text-align: left;
      transition: background .16s ease, border .16s ease, transform .16s ease;
    }

    .dashboard-menu-item:hover {
      transform: translateX(-2px);
      border-color: var(--color-border);
      background: var(--color-surface-2);
    }

    .dashboard-menu-item strong {
      display: block;
      color: var(--color-text);
      font-size: .84rem;
      font-weight: 900;
    }

    .dashboard-menu-item small {
      display: block;
      margin-top: .08rem;
      color: var(--color-text-muted);
      font-size: .7rem;
      font-weight: 800;
      line-height: 1.35;
    }

    .dashboard-menu-icon {
      width: 38px;
      height: 38px;
      display: grid;
      place-items: center;
      border-radius: .9rem;
      flex-shrink: 0;
    }

    .dashboard-menu-icon.blue {
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
    }

    .dashboard-menu-icon.green {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .dashboard-menu-icon.primary {
      background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
      color: #fff;
    }

    .dashboard-menu-item.primary {
      background: linear-gradient(135deg, rgba(139, 195, 74, .12), rgba(116, 178, 212, .12));
      border-color: rgba(139, 195, 74, .22);
    }

    @media (max-width: 1350px) {
      .dashboard-topbar-right {
        grid-template-columns: minmax(260px, 1fr) auto;
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .dashboard-topbar-right {
        grid-template-columns: 1fr;
      }

      .dashboard-menu-wrap,
      .dashboard-menu-btn {
        width: 100%;
      }

      .dashboard-menu {
        left: 0;
        right: auto;
        width: 100%;
      }
    }

    .dashboard-menu-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: .85rem;
      padding-bottom: .8rem;
      margin-bottom: .8rem;
      border-bottom: 1px solid var(--color-border);
    }

    .dashboard-menu-head strong {
      display: block;
      font-size: .95rem;
      font-weight: 900;
      color: var(--color-text);
    }

    .dashboard-menu-head span {
      display: block;
      margin-top: .12rem;
      font-size: .72rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .dashboard-menu-close {
      width: 32px;
      height: 32px;
      display: grid;
      place-items: center;
      flex-shrink: 0;
      border-radius: .75rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      color: var(--color-text-muted);
    }

    .dashboard-menu-close:hover {
      color: var(--color-danger);
      background: var(--color-danger-soft);
      border-color: rgba(229, 6, 86, .2);
    }

    .dashboard-menu-filter {
      position: relative;
      display: block;
    }

    .dashboard-menu-filter>i,
    .dashboard-menu-filter>svg {
      position: absolute;
      left: .78rem;
      top: 50%;
      width: 16px;
      height: 16px;
      color: var(--color-text-muted);
      transform: translateY(-50%);
      pointer-events: none;
    }

    .dashboard-menu-filter .dashboard-menu-select {
      padding-left: 2.25rem;
    }

    .dashboard-menu-chevron {
      width: 15px;
      height: 15px;
    }

    .dashboard-menu-text {
      min-width: 0;
    }
  </style>

  <style>
    .weather-card {
      position: relative;
      height: 100%;
      min-height: 0;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: .9rem;
      padding: 1rem;
      border-radius: var(--radius-xl);
      background:
        linear-gradient(135deg, rgba(116, 178, 212, .2), rgba(139, 195, 74, .16)),
        var(--color-surface-2);
      border: 1px solid rgba(116, 178, 212, .24);
    }

    .weather-bg-orb {
      position: absolute;
      width: 160px;
      height: 160px;
      border-radius: 999px;
      pointer-events: none;
      filter: blur(2px);
      opacity: .42;
      animation: weatherFloat 6s ease-in-out infinite alternate;
    }

    .weather-orb-one {
      right: -70px;
      top: -70px;
      background: var(--color-blue);
    }

    .weather-orb-two {
      left: -80px;
      bottom: -90px;
      background: var(--color-green);
      animation-delay: 1.2s;
    }

    @keyframes weatherFloat {
      from {
        transform: translateY(0) scale(1);
      }

      to {
        transform: translateY(14px) scale(1.08);
      }
    }

    .weather-top,
    .weather-main,
    .weather-stats {
      position: relative;
      z-index: 2;
    }

    .weather-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: .7rem;
    }

    .weather-top strong {
      display: block;
      font-size: .94rem;
      font-weight: 900;
      color: var(--color-text);
    }

    .weather-top span {
      display: block;
      margin-top: .12rem;
      font-size: .72rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .weather-refresh-btn {
      width: 34px;
      height: 34px;
      display: grid;
      place-items: center;
      flex-shrink: 0;
      border-radius: .85rem;
      border: 1px solid var(--color-border);
      background: rgba(255, 255, 255, .7);
      color: var(--color-blue-dark);
    }

    .weather-refresh-btn.is-loading svg {
      animation: weatherSpin .8s linear infinite;
    }

    @keyframes weatherSpin {
      to {
        transform: rotate(360deg);
      }
    }

    .weather-main {
      display: flex;
      align-items: center;
      gap: .9rem;
    }

    .weather-icon-wrap {
      width: 74px;
      height: 74px;
      display: grid;
      place-items: center;
      border-radius: 1.4rem;
      background: rgba(255, 255, 255, .72);
      color: var(--color-blue-dark);
      box-shadow: var(--shadow-sm);
      animation: weatherIconPulse 2.4s ease-in-out infinite;
    }

    .weather-icon-wrap svg {
      width: 38px;
      height: 38px;
    }

    @keyframes weatherIconPulse {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-5px);
      }
    }

    .weather-temp {
      display: flex;
      align-items: flex-start;
      gap: .15rem;
      font-size: 2.35rem;
      line-height: 1;
      font-weight: 900;
      letter-spacing: -.06em;
      color: var(--color-text);
    }

    .weather-temp small {
      margin-top: .18rem;
      font-size: .92rem;
      font-weight: 900;
      color: var(--color-text-muted);
    }

    .weather-desc {
      margin-top: .2rem;
      font-size: .78rem;
      font-weight: 900;
      color: var(--color-text-muted);
    }

    .weather-stats {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: .55rem;
    }

    .weather-stats div {
      min-width: 0;
      padding: .65rem;
      border-radius: var(--radius-md);
      border: 1px solid rgba(255, 255, 255, .62);
      background: rgba(255, 255, 255, .62);
      text-align: center;
    }

    .weather-stats span {
      display: block;
      font-size: .86rem;
      font-weight: 900;
      color: var(--color-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .weather-stats small {
      display: block;
      margin-top: .1rem;
      color: var(--color-text-muted);
      font-size: .65rem;
      font-weight: 900;
      text-transform: uppercase;
    }

    .dashboard-topbar-one-card {
      width: 100%;
      display: grid;
      grid-template-columns: minmax(220px, 320px) auto minmax(260px, 1fr) auto;
      align-items: center;
      gap: .85rem;
      padding: 1rem;
      border-radius: var(--radius-xl);
      border: 1px solid rgba(229, 231, 235, .86);
      background:
        linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(255, 255, 255, .84)),
        linear-gradient(135deg, rgba(139, 195, 74, .18), rgba(116, 178, 212, .2));
      box-shadow: var(--shadow-xs);
    }

    .dashboard-topbar-one-card .dashboard-title-block {
      min-width: 0;
    }

    .dashboard-topbar-one-card .hero-title {
      margin: 0;
      font-size: clamp(1.2rem, 1.7vw, 1.75rem);
      font-weight: 900;
      letter-spacing: -.045em;
      line-height: 1.05;
    }

    .dashboard-topbar-one-card .hero-subtitle {
      margin-top: .28rem;
      color: var(--color-text-muted);
      font-size: .75rem;
      font-weight: 700;
      line-height: 1.35;
    }

    .dashboard-topbar-one-card .tabs {
      min-width: max-content;
      flex-shrink: 0;
    }

    .dashboard-topbar-one-card .dashboard-search {
      width: 100%;
      min-width: 0;
    }

    .dashboard-topbar-one-card .dashboard-menu-wrap {
      position: relative;
      flex-shrink: 0;
    }

    .dashboard-topbar-one-card .dashboard-menu-btn {
      min-height: 42px;
      white-space: nowrap;
    }

    .dashboard-menu {
      position: absolute;
      top: calc(100% + .55rem);
      right: 0;
      z-index: 1200;
      width: min(360px, calc(100vw - 2rem));
      display: none;
      padding: .85rem;
      border-radius: var(--radius-xl);
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      box-shadow: var(--shadow-lg);
    }

    .dashboard-menu-wrap.open .dashboard-menu {
      display: block;
      animation: menuFadeIn .16s ease both;
    }

    @keyframes menuFadeIn {
      from {
        opacity: 0;
        transform: translateY(-6px) scale(.98);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .dashboard-menu-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: .85rem;
      padding-bottom: .8rem;
      margin-bottom: .8rem;
      border-bottom: 1px solid var(--color-border);
    }

    .dashboard-menu-head strong {
      display: block;
      font-size: .95rem;
      font-weight: 900;
      color: var(--color-text);
    }

    .dashboard-menu-head span {
      display: block;
      margin-top: .12rem;
      font-size: .72rem;
      font-weight: 800;
      color: var(--color-text-muted);
    }

    .dashboard-menu-close {
      width: 32px;
      height: 32px;
      display: grid;
      place-items: center;
      flex-shrink: 0;
      border-radius: .75rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface-2);
      color: var(--color-text-muted);
    }

    .dashboard-menu-close:hover {
      color: var(--color-danger);
      background: var(--color-danger-soft);
      border-color: rgba(229, 6, 86, .2);
    }

    .dashboard-menu-section+.dashboard-menu-section {
      margin-top: .8rem;
      padding-top: .8rem;
      border-top: 1px solid var(--color-border);
    }

    .dashboard-menu-label {
      display: block;
      margin-bottom: .45rem;
      color: var(--color-text-muted);
      font-size: .68rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .dashboard-menu-filter {
      position: relative;
      display: block;
    }

    .dashboard-menu-filter>i,
    .dashboard-menu-filter>svg {
      position: absolute;
      left: .78rem;
      top: 50%;
      width: 16px;
      height: 16px;
      color: var(--color-text-muted);
      transform: translateY(-50%);
      pointer-events: none;
    }

    .dashboard-menu-filter .dashboard-menu-select {
      width: 100%;
      min-width: 0;
      padding-left: 2.25rem;
    }

    .dashboard-menu-item {
      width: 100%;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr);
      gap: .7rem;
      align-items: center;
      padding: .75rem;
      border-radius: var(--radius-lg);
      border: 1px solid transparent;
      background: transparent;
      text-align: left;
      transition: background .16s ease, border .16s ease, transform .16s ease;
    }

    .dashboard-menu-item:hover {
      transform: translateX(-2px);
      border-color: var(--color-border);
      background: var(--color-surface-2);
    }

    .dashboard-menu-item strong {
      display: block;
      color: var(--color-text);
      font-size: .84rem;
      font-weight: 900;
    }

    .dashboard-menu-item small {
      display: block;
      margin-top: .08rem;
      color: var(--color-text-muted);
      font-size: .7rem;
      font-weight: 800;
      line-height: 1.35;
    }

    .dashboard-menu-icon {
      width: 38px;
      height: 38px;
      display: grid;
      place-items: center;
      border-radius: .9rem;
      flex-shrink: 0;
    }

    .dashboard-menu-icon.blue {
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
    }

    .dashboard-menu-icon.green {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .dashboard-menu-icon.primary {
      background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
      color: #fff;
    }

    .dashboard-menu-item.primary {
      background: linear-gradient(135deg, rgba(139, 195, 74, .12), rgba(116, 178, 212, .12));
      border-color: rgba(139, 195, 74, .22);
    }

    .dashboard-menu-chevron {
      width: 15px;
      height: 15px;
      transition: transform .18s ease;
    }

    .dashboard-menu-wrap.open .dashboard-menu-chevron {
      transform: rotate(180deg);
    }

    @media (max-width: 1350px) {
      .dashboard-topbar-one-card {
        grid-template-columns: 1fr;
      }

      .dashboard-topbar-one-card .tabs {
        width: 100%;
        min-width: 0;
      }

      .dashboard-topbar-one-card .dashboard-menu-wrap,
      .dashboard-topbar-one-card .dashboard-menu-btn {
        width: 100%;
      }

      .dashboard-menu {
        left: 0;
        right: auto;
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .dashboard-topbar-one-card {
        padding: .85rem;
      }

      .dashboard-topbar-one-card .tabs {
        overflow-x: auto;
      }

      .dashboard-topbar-one-card .tab-btn {
        flex: 1 0 auto;
      }
    }

    .employee-status-mini {
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      flex-shrink: 0;
    }

    .employee-status-pill {
      position: relative;
      min-width: 112px;
      height: 42px;
      display: inline-grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: .45rem;
      padding: .35rem .55rem;
      border-radius: .95rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      color: var(--color-text);
      box-shadow: var(--shadow-xs);
      cursor: pointer;
      transition: transform .16s ease, box-shadow .16s ease, border .16s ease, background .16s ease;
    }

    .employee-status-pill:hover,
    .employee-status-pill.open {
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .employee-status-pill.active {
      border-color: rgba(139, 195, 74, .34);
      background: linear-gradient(135deg, rgba(139, 195, 74, .14), #fff);
    }

    .employee-status-pill.inactive {
      border-color: rgba(248, 172, 0, .34);
      background: linear-gradient(135deg, rgba(248, 172, 0, .14), #fff);
    }

    .employee-status-pill-icon {
      width: 28px;
      height: 28px;
      display: grid;
      place-items: center;
      border-radius: .75rem;
      flex-shrink: 0;
    }

    .employee-status-pill.active .employee-status-pill-icon {
      background: var(--color-green-soft);
      color: var(--color-green-dark);
    }

    .employee-status-pill.inactive .employee-status-pill-icon {
      background: var(--color-warning-soft);
      color: #b87400;
    }

    .employee-status-pill-icon svg {
      width: 15px;
      height: 15px;
    }

    .employee-status-pill-text {
      min-width: 0;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      line-height: 1;
    }

    .employee-status-pill-text strong {
      font-size: .95rem;
      font-weight: 900;
      letter-spacing: -.035em;
      color: var(--color-text);
    }

    .employee-status-pill-text small {
      margin-top: .16rem;
      font-size: .61rem;
      font-weight: 900;
      color: var(--color-text-muted);
      text-transform: uppercase;
      letter-spacing: .045em;
    }

    .employee-status-pill-arrow {
      width: 14px;
      height: 14px;
      color: var(--color-text-muted);
      transition: transform .16s ease;
    }

    .employee-status-pill.open .employee-status-pill-arrow {
      transform: rotate(180deg);
    }

    .employee-status-popover {
      position: absolute;
      top: calc(100% + .45rem);
      left: 0;
      width: 285px;
      display: none;
      z-index: 800;
      padding: .7rem;
      border-radius: 1rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      box-shadow: var(--shadow-lg);
      text-align: left;
    }

    .employee-status-popover.right {
      left: auto;
      right: 0;
    }

    .employee-status-pill.open .employee-status-popover {
      display: block;
    }

    .employee-status-popover-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .55rem;
      padding-bottom: .55rem;
      margin-bottom: .5rem;
      border-bottom: 1px solid var(--color-border);
    }

    .employee-status-popover-head strong {
      font-size: .78rem;
      font-weight: 900;
      color: var(--color-text);
    }

    .employee-status-popover-head span {
      font-size: .65rem;
      color: var(--color-text-muted);
      font-weight: 800;
      white-space: nowrap;
    }

    .employee-status-list {
      max-height: 260px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: .35rem;
      padding-right: .15rem;
    }

    .employee-status-person {
      min-height: 42px;
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: .5rem;
      padding: .42rem;
      border-radius: .78rem;
      text-decoration: none;
      color: var(--color-text);
      background: var(--color-surface-2);
      border: 1px solid transparent;
      transition: border .16s ease, background .16s ease, transform .16s ease;
    }

    .employee-status-person:hover {
      transform: translateX(2px);
      border-color: rgba(116, 178, 212, .45);
      background: #fff;
    }

    .employee-status-avatar {
      width: 30px;
      height: 30px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #fff;
      box-shadow: var(--shadow-xs);
      background: var(--color-surface);
    }

    .employee-status-person-name {
      min-width: 0;
      display: block;
    }

    .employee-status-person-name strong {
      display: block;
      max-width: 130px;
      font-size: .74rem;
      font-weight: 900;
      color: var(--color-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .employee-status-person-name span {
      display: block;
      margin-top: .08rem;
      font-size: .61rem;
      font-weight: 800;
      color: var(--color-text-muted);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .employee-status-badge {
      padding: .16rem .42rem;
      border-radius: 999px;
      font-size: .58rem;
      line-height: 1;
      font-weight: 900;
      white-space: nowrap;
    }

    .employee-status-badge.active {
      color: var(--color-green-dark);
      background: var(--color-green-soft);
    }

    .employee-status-badge.sick {
      color: var(--color-danger);
      background: var(--color-danger-soft);
    }

    .employee-status-badge.holiday {
      color: #b87400;
      background: var(--color-warning-soft);
    }

    .employee-status-badge.inactive {
      color: var(--color-text-muted);
      background: #f3f4f6;
    }

    .employee-status-empty {
      min-height: 76px;
      display: grid;
      place-items: center;
      color: var(--color-text-muted);
      font-size: .72rem;
      font-weight: 800;
      text-align: center;
    }

    @media (max-width: 1200px) {
      .employee-status-mini {
        order: 3;
        width: 100%;
      }

      .employee-status-pill {
        flex: 1;
      }
    }

    @media (max-width: 768px) {
      .employee-status-mini {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .employee-status-pill {
        width: 100%;
        min-width: 0;
      }

      .employee-status-popover,
      .employee-status-popover.right {
        position: fixed;
        top: auto;
        left: .9rem;
        right: .9rem;
        bottom: .9rem;
        width: auto;
        max-height: 70vh;
      }
    }

    .dashboard-topbar,
    .dashboard-topbar-one-card {
      position: relative;
      overflow: visible !important;
      z-index: 900;
    }

    .employee-status-mini {
      position: relative;
      z-index: 950;
      display: inline-flex;
      align-items: center;
      gap: .45rem;
      flex-shrink: 0;
    }

    .employee-status-pill {
      position: relative;
      min-width: 112px;
      height: 42px;
      display: inline-grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      align-items: center;
      gap: .45rem;
      padding: .35rem .55rem;
      border-radius: .95rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      color: var(--color-text);
      box-shadow: var(--shadow-xs);
      cursor: pointer;
      transition: transform .16s ease, box-shadow .16s ease, border .16s ease, background .16s ease;
    }

    .employee-status-pill:hover,
    .employee-status-pill.open {
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .employee-status-pill.active {
      border-color: rgba(139, 195, 74, .34);
      background: linear-gradient(135deg, rgba(139, 195, 74, .14), #fff);
    }

    .employee-status-pill.inactive {
      border-color: rgba(248, 172, 0, .34);
      background: linear-gradient(135deg, rgba(248, 172, 0, .14), #fff);
    }

    .employee-status-popover {
      position: absolute;
      top: calc(100% + .5rem);
      left: 0;
      width: 285px;
      display: none;
      z-index: 99999;
      padding: .7rem;
      border-radius: 1rem;
      border: 1px solid var(--color-border);
      background: var(--color-surface);
      box-shadow: 0 24px 70px rgba(15, 23, 42, .22);
      text-align: left;
    }

    .employee-status-popover.right {
      left: auto;
      right: 0;
    }

    .employee-status-pill.open .employee-status-popover {
      display: block;
    }

    .employee-status-person {
      cursor: pointer;
      pointer-events: auto;
    }

    .main-content,
    .view-section,
    .widget-grid {
      position: relative;
      z-index: 1;
    }

    .toast {
      max-width: min(520px, calc(100vw - 2rem));
    }

    .toast.toast-warning {
      background: #92400e;
    }

    .toast.toast-danger {
      background: #991b1b;
    }

    .toast.toast-confirm {
      bottom: 1.35rem;
      border-radius: 1.25rem;
      align-items: flex-start;
      padding: .95rem;
      gap: .75rem;
      pointer-events: auto;
    }

    .toast-confirm-content {
      display: flex;
      flex-direction: column;
      gap: .65rem;
    }

    .toast-confirm-title {
      font-size: .9rem;
      font-weight: 900;
      line-height: 1.25;
    }

    .toast-confirm-text {
      font-size: .78rem;
      font-weight: 700;
      opacity: .88;
      line-height: 1.45;
    }

    .toast-confirm-actions {
      display: flex;
      justify-content: flex-end;
      gap: .5rem;
    }

    .toast-confirm-btn {
      min-height: 34px;
      padding: .45rem .75rem;
      border-radius: .75rem;
      font-size: .76rem;
      font-weight: 900;
      border: 1px solid rgba(255, 255, 255, .22);
      color: #fff;
      background: rgba(255, 255, 255, .12);
    }

    .toast-confirm-btn:hover {
      background: rgba(255, 255, 255, .2);
    }

    .toast-confirm-btn.danger {
      background: #ef4444;
      border-color: rgba(239, 68, 68, .45);
    }

    .toast-confirm-btn.danger:hover {
      background: #dc2626;
    }
  </style>

  <style>
    .widget-grid.is-organizing .widget {
      transition: transform .24s ease, opacity .24s ease, box-shadow .24s ease;
    }

    .widget.is-organized-flash {
      animation: organizedFlash .75s ease both;
    }

    @keyframes organizedFlash {
      0% {
        transform: scale(.985);
        box-shadow: 0 0 0 0 rgba(116, 178, 212, .0);
      }

      45% {
        transform: scale(1.01);
        box-shadow: 0 0 0 5px rgba(116, 178, 212, .18);
      }

      100% {
        transform: scale(1);
        box-shadow: var(--shadow-sm);
      }
    }

    .dashboard-topbar-one-card {
      display: grid;
      grid-template-columns: minmax(260px, 1fr) auto;
      grid-template-areas:
        "title status"
        "nav nav";
      gap: .85rem;
      align-items: center;
      width: 100%;
      padding: 1rem;
      border-radius: var(--radius-xl);
      border: 1px solid rgba(229, 231, 235, .86);
      background: rgba(255, 255, 255, .92);
      box-shadow: var(--shadow-xs);
      backdrop-filter: blur(14px);
    }

    .dashboard-title-block {
      grid-area: title;
      min-width: 0;
    }

    .employee-status-mini {
      grid-area: status;
      justify-self: end;
      min-width: 0;
    }

    .dashboard-topbar-nav {
      grid-area: nav;
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: .75rem;
      align-items: center;
      min-width: 0;
    }

    .dashboard-view-tabs {
      width: 100%;
      min-width: 0;
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      overflow: visible;
    }

    .dashboard-view-tabs .tab-btn {
      min-width: 0;
      justify-content: center;
    }

    .dashboard-view-tabs .tab-btn span {
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dashboard-menu-wrap {
      position: relative;
      flex-shrink: 0;
    }

    .dashboard-menu-btn-icon {
      width: 42px;
      min-width: 42px;
      height: 42px;
      padding: 0;
      border-radius: .9rem;
    }

    .dashboard-menu-btn-icon svg,
    .dashboard-menu-btn-icon i {
      width: 18px;
      height: 18px;
    }

    .dashboard-menu-search {
      position: relative;
      display: block;
      width: 100%;
    }

    .dashboard-menu-search i,
    .dashboard-menu-search svg {
      position: absolute;
      left: .85rem;
      top: 50%;
      width: 16px;
      height: 16px;
      color: var(--color-text-muted);
      transform: translateY(-50%);
      pointer-events: none;
    }

    .dashboard-menu-search input {
      width: 100%;
      height: 42px;
      padding: 0 .85rem 0 2.35rem;
      border-radius: .9rem;
      font-size: .84rem;
      font-weight: 800;
      background: var(--color-surface);
    }

    .dashboard-menu-filter {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr);
      gap: .6rem;
      align-items: center;
    }

    .dashboard-menu-filter i,
    .dashboard-menu-filter svg {
      width: 17px;
      height: 17px;
      color: var(--color-text-muted);
    }

    .dashboard-menu-select {
      width: 100%;
      min-width: 0;
    }

    .dashboard-menu {
      right: 0;
      width: min(380px, calc(100vw - 1.5rem));
      max-width: calc(100vw - 1.5rem);
    }

    @media (max-width: 1100px) {
      .dashboard-topbar-one-card {
        grid-template-columns: 1fr;
        grid-template-areas:
          "title"
          "status"
          "nav";
      }

      .employee-status-mini {
        justify-self: stretch;
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 768px) {
      .main-content {
        padding: .75rem;
      }

      .dashboard-topbar-one-card {
        padding: .85rem;
        gap: .75rem;
        border-radius: var(--radius-lg);
      }

      .hero-title {
        margin-top: 0;
        font-size: 1.35rem;
      }

      .hero-subtitle {
        display: none;
      }

      .dashboard-topbar-nav {
        grid-template-columns: minmax(0, 1fr) 42px;
        gap: .55rem;
      }

      .dashboard-view-tabs {
        gap: .28rem;
        padding: .25rem;
        border-radius: .9rem;
      }

      .dashboard-view-tabs .tab-btn {
        min-height: 38px;
        padding: .45rem .35rem;
        border-radius: .7rem;
        gap: .25rem;
        font-size: .68rem;
      }

      .dashboard-view-tabs .tab-btn svg,
      .dashboard-view-tabs .tab-btn i {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
      }

      .dashboard-view-tabs .tab-btn span {
        max-width: 72px;
        font-size: .68rem;
      }

      .employee-status-pill {
        min-width: 0;
        padding: .55rem;
      }

      .employee-status-pill-icon {
        width: 32px;
        height: 32px;
      }

      .employee-status-pill-text strong {
        font-size: .95rem;
      }

      .employee-status-pill-text small {
        font-size: .62rem;
      }

      .employee-status-pill-arrow {
        display: none;
      }

      .employee-status-popover,
      .employee-status-popover.right {
        left: 0;
        right: 0;
        width: calc(100vw - 1.5rem);
        max-width: calc(100vw - 1.5rem);
      }

      .dashboard-menu {
        position: fixed;
        top: auto;
        right: .75rem;
        left: .75rem;
        bottom: .75rem;
        width: auto;
        max-width: none;
        max-height: calc(100vh - 1.5rem);
        overflow-y: auto;
        border-radius: 1.25rem;
      }
    }

    @media (max-width: 480px) {
      .dashboard-topbar-nav {
        grid-template-columns: minmax(0, 1fr) 40px;
      }

      .dashboard-menu-btn-icon {
        width: 40px;
        min-width: 40px;
        height: 40px;
      }

      .dashboard-view-tabs .tab-btn {
        min-height: 40px;
        padding: .45rem .25rem;
      }

      .dashboard-view-tabs .tab-btn span {
        display: none;
      }

      .dashboard-view-tabs .tab-btn svg,
      .dashboard-view-tabs .tab-btn i {
        width: 18px;
        height: 18px;
      }

      .employee-status-mini {
        gap: .45rem;
      }

      .employee-status-pill {
        gap: .45rem;
      }
    }

    /* =========================================================
       MOBILE DASHBOARD FIX — prevent widgets/cards overlapping
       ========================================================= */

    @media (max-width: 768px) {

      html,
      body {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
      }

      .main-content {
        width: 100%;
        max-width: 100%;
        padding: .75rem;
        gap: .85rem;
        overflow-x: hidden;
      }

      .view-section,
      .view-section.active {
        width: 100%;
        max-width: 100%;
        overflow: visible;
      }

      .widget-grid {
        display: flex !important;
        flex-direction: column !important;
        width: 100%;
        max-width: 100%;
        gap: .9rem !important;
        grid-template-columns: none !important;
        grid-auto-rows: auto !important;
        grid-auto-flow: row !important;
        align-items: stretch !important;
        overflow: visible !important;
      }

      .widget-grid>.widget,
      .widget {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;

        height: auto !important;
        min-height: auto !important;

        grid-column: auto !important;
        grid-row: auto !important;

        display: flex !important;
        flex-direction: column !important;

        overflow: visible !important;
        padding: .95rem !important;
        border-radius: 1.1rem;
        transform: none !important;
      }

      .widget-content {
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        min-height: auto !important;
        overflow: visible !important;
        display: flex;
        flex-direction: column;
      }

      .widget-header {
        align-items: flex-start;
        gap: .65rem;
        margin-bottom: .75rem;
      }

      .widget-title-wrap {
        min-width: 0;
        flex: 1;
      }

      .widget-title,
      .widget-subtitle {
        max-width: 100%;
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: clip !important;
        line-height: 1.25;
      }

      .widget-tools {
        flex-shrink: 0;
      }

      .resize-handle {
        display: none !important;
      }
    }


    .dept-recent-list {
      display: flex;
      flex-direction: column;
      gap: .65rem;
      overflow-y: auto;
      padding-right: .2rem;
    }

    .dept-recent-list::-webkit-scrollbar {
      width: 6px;
    }

    .dept-recent-list::-webkit-scrollbar-thumb {
      background: var(--color-border-strong);
      border-radius: 999px;
    }

    .dept-compact-card {
      position: relative;
      display: grid;
      grid-template-columns: 44px minmax(0, 1fr) auto;
      align-items: center;
      gap: .75rem;
      padding: .75rem;
      border: 1px solid var(--color-border);
      border-radius: 1.05rem;
      background:
        linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(249, 251, 253, .94)),
        var(--color-surface);
      box-shadow: var(--shadow-xs);
      transition: transform .16s ease, box-shadow .16s ease, border .16s ease;
    }

    .dept-compact-card:hover {
      transform: translateY(-1px);
      border-color: var(--color-border-strong);
      box-shadow: var(--shadow-sm);
    }

    .dept-compact-avatar-wrap {
      position: relative;
      width: 44px;
      height: 44px;
      flex: 0 0 auto;
    }

    .dept-compact-avatar {
      width: 44px;
      height: 44px;
      border-radius: 16px;
      object-fit: cover;
      border: 2px solid #fff;
      background: var(--color-surface-2);
      box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
    }

    .dept-compact-type-dot {
      position: absolute;
      right: -4px;
      bottom: -4px;
      width: 22px;
      height: 22px;
      display: grid;
      place-items: center;
      border-radius: 999px;
      border: 2px solid #fff;
      background: var(--color-blue-soft);
      color: var(--color-blue-dark);
    }

    .dept-compact-type-dot svg {
      width: 12px;
      height: 12px;
    }

    .dept-compact-main {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: .35rem;
    }

    .dept-compact-top {
      display: flex;
      align-items: center;
      gap: .45rem;
      min-width: 0;
    }

    .dept-compact-type {
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      gap: .25rem;
      padding: .16rem .45rem;
      border-radius: 999px;
      background: var(--color-green-soft);
      color: var(--color-green-dark);
      font-size: .64rem;
      font-weight: 950;
      white-space: nowrap;
    }

    .dept-compact-number {
      flex: 0 0 auto;
      font-size: .68rem;
      font-weight: 950;
      color: var(--color-blue-dark);
      white-space: nowrap;
    }

    .dept-compact-title {
      min-width: 0;
      font-size: .84rem;
      font-weight: 950;
      color: var(--color-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dept-compact-chips {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: .35rem;
      min-width: 0;
    }

    .dept-compact-chip {
      max-width: 190px;
      display: inline-flex;
      align-items: center;
      gap: .28rem;
      padding: .22rem .48rem;
      border-radius: .72rem;
      background: var(--color-surface-2);
      border: 1px solid var(--color-border);
      color: var(--color-text-muted);
      font-size: .68rem;
      font-weight: 850;
      line-height: 1.1;
      white-space: nowrap;
    }

    .dept-compact-chip span {
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .dept-compact-chip svg {
      width: 12px;
      height: 12px;
      flex: 0 0 auto;
      color: var(--color-blue-dark);
    }

    .dept-compact-chip.status {
      background: var(--color-warning-soft);
      border-color: rgba(248, 172, 0, .28);
      color: #b87400;
    }

    .dept-compact-chip.product {
      background: var(--color-blue-soft);
      border-color: rgba(116, 178, 212, .28);
      color: var(--color-blue-dark);
    }

    .dept-compact-chip.customer {
      background: var(--color-green-soft);
      border-color: rgba(139, 195, 74, .28);
      color: var(--color-green-dark);
    }

    .dept-compact-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: .4rem;
    }

    .dept-compact-open {
      width: 34px;
      height: 34px;
      display: grid;
      place-items: center;
      border-radius: .9rem;
      background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
      color: #fff;
      text-decoration: none;
      box-shadow: var(--shadow-xs);
    }

    .dept-compact-open:hover {
      color: #fff;
      text-decoration: none;
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .dept-compact-open svg {
      width: 15px;
      height: 15px;
    }

    @media (max-width: 760px) {
      .dept-compact-card {
        grid-template-columns: 40px minmax(0, 1fr);
      }

      .dept-compact-actions {
        grid-column: 1 / -1;
        justify-content: flex-start;
        padding-left: 52px;
      }

      .dept-compact-chip {
        max-width: 100%;
      }
    }
 

  /* =========================================================
  DEPARTMENT RECENT - COMPACT CARD UI
  Shows employee picture, customer, customer no, product,
  source, error type, priority, people and date with icons only.
  ========================================================= */
  #deptRecentList {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: .7rem;
  padding: .05rem .25rem .2rem 0;
  }

  #deptRecentList::-webkit-scrollbar {
  width: 6px;
  }

  #deptRecentList::-webkit-scrollbar-track {
  background: transparent;
  }

  #deptRecentList::-webkit-scrollbar-thumb {
  background: var(--color-border-strong);
  border-radius: 999px;
  }

  .dept-flow-card {
  position: relative;
  display: grid;
  grid-template-columns: 50px minmax(0, 1fr) 38px;
  gap: .78rem;
  align-items: center;
  padding: .78rem;
  border-radius: 1.16rem;
  border: 1px solid rgba(229, 231, 235, .96);
  background:
  linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(249, 251, 253, .95)),
  var(--color-surface);
  box-shadow: var(--shadow-xs);
  transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease, background .16s ease;
  }

  .dept-flow-card:hover {
  transform: translateY(-1px);
  border-color: var(--color-border-strong);
  box-shadow: var(--shadow-sm);
  }

  .dept-flow-avatar-box {
  position: relative;
  width: 50px;
  height: 50px;
  }

  .dept-flow-avatar {
  width: 50px;
  height: 50px;
  border-radius: 1.05rem;
  object-fit: cover;
  background: var(--color-surface-2);
  border: 2px solid #fff;
  box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
  }

  .dept-flow-type-icon {
  position: absolute;
  right: -5px;
  bottom: -5px;
  width: 24px;
  height: 24px;
  display: grid;
  place-items: center;
  border-radius: 999px;
  border: 2px solid #fff;
  background: var(--color-blue-soft);
  color: var(--color-blue-dark);
  }

  .dept-flow-type-icon svg {
  width: 12px;
  height: 12px;
  }

  .dept-flow-body {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: .38rem;
  }

  .dept-flow-head {
  min-width: 0;
  display: flex;
  align-items: center;
  gap: .42rem;
  }

  .dept-flow-type-pill {
  flex: 0 0 auto;
  display: inline-flex;
  align-items: center;
  gap: .25rem;
  padding: .16rem .48rem;
  border-radius: 999px;
  background: var(--color-green-soft);
  color: var(--color-green-dark);
  font-size: .62rem;
  line-height: 1;
  font-weight: 950;
  white-space: nowrap;
  }

  .dept-flow-customer {
  min-width: 0;
  color: var(--color-text);
  font-size: .9rem;
  line-height: 1.18;
  font-weight: 950;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  }

  .dept-flow-line {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: .34rem;
  min-width: 0;
  }

  .dept-flow-chip {
  min-width: 0;
  max-width: 185px;
  height: 26px;
  display: inline-flex;
  align-items: center;
  gap: .32rem;
  padding: 0 .52rem;
  border-radius: .78rem;
  border: 1px solid var(--color-border);
  background: var(--color-surface-2);
  color: var(--color-text-muted);
  font-size: .68rem;
  line-height: 1;
  font-weight: 850;
  white-space: nowrap;
  }

  .dept-flow-chip svg {
  width: 13px;
  height: 13px;
  flex: 0 0 auto;
  color: currentColor;
  }

  .dept-flow-chip span {
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  }

  .dept-flow-chip.customer-no {
  background: var(--color-surface-2);
  border-color: var(--color-border);
  color: var(--color-text-soft);
  max-width: 150px;
  }

  .dept-flow-chip.product {
  background: var(--color-blue-soft);
  border-color: rgba(116, 178, 212, .3);
  color: var(--color-blue-dark);
  max-width: 230px;
  }

  .dept-flow-chip.source {
  background: rgba(116, 178, 212, .1);
  border-color: rgba(116, 178, 212, .22);
  color: var(--color-blue-dark);
  max-width: 170px;
  }

  .dept-flow-chip.address {
  max-width: 240px;
  }

  .dept-flow-chip.warning {
  background: var(--color-warning-soft);
  border-color: rgba(248, 172, 0, .28);
  color: #b87400;
  }

  .dept-flow-chip.danger {
  background: var(--color-danger-soft);
  border-color: rgba(229, 6, 86, .18);
  color: var(--color-danger);
  }

  .dept-flow-chip.green {
  background: var(--color-green-soft);
  border-color: rgba(139, 195, 74, .28);
  color: var(--color-green-dark);
  }

  .dept-flow-people {
  display: inline-flex;
  align-items: center;
  min-width: 0;
  max-width: 250px;
  }

  .dept-flow-mini-avatar {
  width: 24px;
  height: 24px;
  border-radius: 999px;
  object-fit: cover;
  border: 2px solid #fff;
  background: var(--color-surface-2);
  margin-left: -7px;
  box-shadow: var(--shadow-xs);
  }

  .dept-flow-mini-avatar:first-child {
  margin-left: 0;
  }

  .dept-flow-people-name {
  margin-left: .42rem;
  min-width: 0;
  color: var(--color-text-muted);
  font-size: .68rem;
  font-weight: 850;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  }

  .dept-flow-open {
  width: 38px;
  height: 38px;
  display: grid;
  place-items: center;
  border-radius: .98rem;
  background: linear-gradient(135deg, var(--color-green), var(--color-green-dark));
  color: #fff;
  text-decoration: none;
  box-shadow: var(--shadow-xs);
  transition: transform .16s ease, box-shadow .16s ease;
  }

  .dept-flow-open:hover {
  color: #fff;
  text-decoration: none;
  transform: translateY(-1px);
  box-shadow: var(--shadow-sm);
  }

  .dept-flow-open svg {
  width: 15px;
  height: 15px;
  }

  @media (max-width: 768px) {
  .dept-flow-card {
  grid-template-columns: 46px minmax(0, 1fr);
  }

  .dept-flow-avatar,
  .dept-flow-avatar-box {
  width: 46px;
  height: 46px;
  }

  .dept-flow-open {
  grid-column: 2;
  width: 100%;
  height: 34px;
  }

  .dept-flow-chip {
  max-width: 100%;
  }
  }
 </style>

@endsection

@section('content')
  <div class="overlay" id="overlay"></div>

  <aside class="side-panel" id="customizePanel" aria-hidden="true">
    <div class="panel-header">
      <div>
        <div class="panel-title">Dashboard anpassen</div>
        <div class="panel-subtitle">Layout, Darstellung und sichtbare Widgets speichern</div>
      </div>
      <button class="close-btn" type="button" data-close-panels>
        <i data-lucide="x"></i>
      </button>
    </div>

    <div class="setting-group">
      <span class="setting-label">Darstellung</span>
      <div class="segmented" data-setting="theme">
        <button type="button" data-value="default" class="active">Standard</button>
        <button type="button" data-value="soft">Soft</button>
        <button type="button" data-value="contrast">Kontrast</button>
      </div>
    </div>

    <div class="setting-group">
      <span class="setting-label">Abstand</span>
      <div class="segmented" data-setting="density">
        <button type="button" data-value="compact">Kompakt</button>
        <button type="button" data-value="normal" class="active">Normal</button>
        <button type="button" data-value="comfortable">Groß</button>
      </div>
    </div>

    <div class="setting-group">
      <span class="setting-label">Widgets anzeigen</span>
      <div class="check-list" id="widgetVisibilityList"></div>
    </div>

    <div class="setting-group">
      <button class="btn btn-primary" type="button" id="saveDashboardBtn" style="width:100%;">
        <i data-lucide="save"></i>
        Einstellungen speichern
      </button>

      <button class="btn btn-danger" type="button" id="resetDashboardBtn" style="width:100%; margin-top:.65rem;">
        <i data-lucide="rotate-ccw"></i>
        Zurücksetzen
      </button>
    </div>
  </aside>

  <aside class="widget-tray" id="widgetTray" aria-hidden="true">
    <div class="panel-header">
      <div>
        <div class="panel-title">Widgets hinzufügen</div>
        <div class="panel-subtitle">Neue Blöcke werden im aktiven Bereich eingefügt</div>
      </div>
      <button class="close-btn" type="button" data-close-panels>
        <i data-lucide="x"></i>
      </button>
    </div>

    <div id="widgetTrayList"></div>
  </aside>

  <main class="main-content">
    <section class="dashboard-topbar dashboard-topbar-one-card">
      <div class="dashboard-title-block">
        <h1 class="hero-title">Mein Dashboard</h1>
        <p class="hero-subtitle">
          Elemente verschieben, skalieren, ausblenden und pro Benutzer speichern.
        </p>
      </div>

      <div class="employee-status-mini" id="employeeStatusCards">
        <div class="employee-status-pill active" data-status-dropdown="active" role="button" tabindex="0">
          <span class="employee-status-pill-icon">
            <i data-lucide="user-check"></i>
          </span>

          <span class="employee-status-pill-text">
            <strong id="employeeActiveCount">–</strong>
            <small>Aktiv</small>
          </span>

          <i data-lucide="chevron-down" class="employee-status-pill-arrow"></i>

          <div class="employee-status-popover" id="employeeActiveDropdown">
            <div class="employee-status-popover-head">
              <strong>Aktive Mitarbeiter</strong>
              <span id="employeeActiveSub">Heute verfügbar</span>
            </div>

            <div class="employee-status-list" id="employeeActiveList">
              <div class="employee-status-empty">Lade Mitarbeiter...</div>
            </div>
          </div>
        </div>

        <div class="employee-status-pill inactive" data-status-dropdown="inactive" role="button" tabindex="0">
          <span class="employee-status-pill-icon">
            <i data-lucide="calendar-x"></i>
          </span>

          <span class="employee-status-pill-text">
            <strong id="employeeInactiveCount">–</strong>
            <small>Abwesend</small>
          </span>

          <i data-lucide="chevron-down" class="employee-status-pill-arrow"></i>

          <div class="employee-status-popover right" id="employeeInactiveDropdown">
            <div class="employee-status-popover-head">
              <strong>Krank / Urlaub</strong>
              <span id="employeeInactiveSub">Heute abwesend</span>
            </div>

            <div class="employee-status-list" id="employeeInactiveList">
              <div class="employee-status-empty">Lade Mitarbeiter...</div>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-topbar-nav">
        <div class="tabs dashboard-view-tabs" id="tabContainer">
          <button class="tab-btn active" type="button" data-view="personal" title="Mein Bereich">
            <i data-lucide="user"></i>
            <span>Mein Bereich</span>
          </button>

          <button class="tab-btn" type="button" data-view="department" title="Abteilung">
            <i data-lucide="building"></i>
            <span>Abteilung</span>
          </button>

          <button class="tab-btn" type="button" data-view="company" title="Unternehmen">
            <i data-lucide="activity"></i>
            <span>Unternehmen</span>
          </button>
        </div>

        <div class="dashboard-menu-wrap" id="dashboardMenuWrap">
          <button class="btn dashboard-menu-btn dashboard-menu-btn-icon" type="button" id="dashboardMenuBtn"
            aria-expanded="false" aria-controls="dashboardMenu" title="Dashboard-Menü">
            <i data-lucide="sliders-horizontal"></i>
          </button>

          <div class="dashboard-menu" id="dashboardMenu" role="menu">
            <div class="dashboard-menu-head">
              <div>
                <strong>Dashboard-Menü</strong>
                <span>Suche, Filter, Ansicht und Bearbeitung</span>
              </div>

              <button type="button" class="dashboard-menu-close" id="dashboardMenuCloseBtn">
                <i data-lucide="x"></i>
              </button>
            </div>

            <div class="dashboard-menu-section">
              <span class="dashboard-menu-label">Suche</span>

              <label class="dashboard-menu-search" aria-label="Dashboard Suche">
                <i data-lucide="search"></i>
                <input type="search" id="dashboardSearch" placeholder="Dashboard durchsuchen..." autocomplete="off">
              </label>
            </div>

            <div class="dashboard-menu-section">
              <span class="dashboard-menu-label">Schnellfilter</span>

              <label class="dashboard-menu-filter">
                <i data-lucide="filter"></i>

                <select class="select-control dashboard-menu-select" id="quickFilter">
                  <option value="all">Alle Elemente</option>
                  <option value="today">Heute</option>
                  <option value="analytics">Aktivitätsübersicht</option>
                  <option value="hr">Monatsübersicht</option>
                  <option value="sales">Vertrieb</option>
                </select>
              </label>
            </div>

            <div class="dashboard-menu-section">
              <span class="dashboard-menu-label">Aktionen</span>

              <button class="dashboard-menu-item" type="button" id="customizeBtn">
                <span class="dashboard-menu-icon blue">
                  <i data-lucide="sliders-horizontal"></i>
                </span>

                <span class="dashboard-menu-text">
                  <strong>Anpassen</strong>
                  <small>Design, Dichte und Ansicht ändern</small>
                </span>
              </button>

              <button class="dashboard-menu-item" type="button" id="editModeBtn">
                <span class="dashboard-menu-icon green">
                  <i data-lucide="layout-grid"></i>
                </span>

                <span class="dashboard-menu-text">
                  <strong>Dashboard editieren</strong>
                  <small>Elemente verschieben, skalieren oder ausblenden</small>
                </span>
              </button>

              <button class="dashboard-menu-item" type="button" id="organizeDashboardBtn">
                <span class="dashboard-menu-icon blue">
                  <i data-lucide="sparkles"></i>
                </span>

                <span class="dashboard-menu-text">
                  <strong>Dashboard organisieren</strong>
                  <small>Karten sortieren und Lücken reduzieren</small>
                </span>
              </button>

              <button class="dashboard-menu-item primary" type="button" id="addWidgetBtn" style="display:none;">
                <span class="dashboard-menu-icon primary">
                  <i data-lucide="plus"></i>
                </span>

                <span class="dashboard-menu-text">
                  <strong>Widget hinzufügen</strong>
                  <small>Neue Elemente zur Ansicht hinzufügen</small>
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div id="breakingNewsBar" class="sa-bn hidden" data-widget-id="breakingNews" data-widget-key="breakingNews"
      data-widget-title="Breaking News" data-widget-tags="today notifications breaking">

      <div class="sa-bn-label" id="bnLabel">
        <i class="ri-notification-3-fill sa-bn-label-icon" id="bnMainIcon"></i>
      </div>

      <div class="sa-bn-main">
        <div class="sa-bn-track">
          <span id="breakingNewsText" class="sa-bn-text"></span>
        </div>

        <div id="bnAudioWrapper" class="sa-bn-audio hidden">
          <div class="sa-bn-audio-wave">
            <div class="sa-bn-audio-wave-bg"></div>
            <div class="sa-bn-audio-progress" id="bnAudioProgress"></div>
            <div class="sa-bn-audio-handle" id="bnAudioHandle"></div>

            <input type="range" id="bnAudioSeek" min="0" max="100" step="0.1" class="sa-bn-audio-range">
          </div>

          <div class="sa-bn-audio-time">
            <span id="bnAudioCurrent">0:00</span>
            <span>/</span>
            <span id="bnAudioDuration">0:00</span>
          </div>
        </div>

        <div class="sa-bn-meta">
          <span id="breakingNewsType" class="sa-bn-pill"></span>
          <span class="sa-bn-time">
            <i class="ri-time-line"></i>
            <span id="breakingNewsTimeText"></span>
          </span>
        </div>
      </div>

      <div class="sa-bn-creator">
        <img id="breakingNewsCreatorImage" class="sa-bn-avatar" src="" alt="" style="display:none;">
        <span id="breakingNewsCreatorName" class="sa-bn-creator-name hidden"></span>
      </div>

      <div class="sa-bn-controls">
        <button id="bnPrev" class="sa-bn-btn" type="button" title="Vorherige">
          <i class="ri-skip-back-mini-line"></i>
        </button>

        <button id="bnPlayPause" class="sa-bn-btn" type="button" title="Pause">
          <i class="ri-pause-mini-line" id="bnPlayPauseIcon"></i>
        </button>

        <button id="bnNext" class="sa-bn-btn" type="button" title="Nächste">
          <i class="ri-skip-forward-mini-line"></i>
        </button>
      </div>
    </div>

    <section id="view-personal" class="view-section active" data-view-section="personal">
      <div class="widget-grid" id="grid-personal">
        <article class="widget col-span-3 row-span-4" data-widget-id="clock" data-widget-key="clock"
          data-widget-title="Uhrzeit" data-widget-tags="today time">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon blue">
                <i data-lucide="clock"></i>
              </span>
              <span>
                <span class="widget-title">Uhrzeit</span>
                <span class="widget-subtitle">Live Zeit & Datum</span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget title="Löschen">
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content clock-container">
            <div class="analog-clock-wrapper">
              <div class="analog-clock">
                <div class="clock-numbers">
                  <span class="num num-1">1</span>
                  <span class="num num-2">2</span>
                  <span class="num num-3">3</span>
                  <span class="num num-4">4</span>
                  <span class="num num-5">5</span>
                  <span class="num num-6">6</span>
                  <span class="num num-7">7</span>
                  <span class="num num-8">8</span>
                  <span class="num num-9">9</span>
                  <span class="num num-10">10</span>
                  <span class="num num-11">11</span>
                  <span class="num num-12">12</span>
                </div>

                <div class="clock-center"></div>
                <div class="hand hour-hand" id="hourHand"></div>
                <div class="hand minute-hand" id="minuteHand"></div>
                <div class="hand second-hand" id="secondHand"></div>
              </div>
            </div>

            <div class="clock-text">
              <div class="clock-time" id="digitalTime">12:00</div>
              <div class="clock-date" id="digitalDate">Montag, 01. Jan</div>
            </div>

          </div>
          <div class="resize-handle"></div>


        </article>

        <article class="widget col-span-5 row-span-4" data-widget-id="hr" data-widget-key="hr"
          data-widget-title="Zeiten & Abwesenheit" data-widget-tags="hr today">

          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon green">
                <i data-lucide="calendar-days"></i>
              </span>

              <span>
                <span class="widget-title">Monatsübersicht</span>
                <span class="widget-subtitle" id="hrWidgetSubtitle">
                  Urlaub, Krankheit, Berufsschule
                </span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget title="Widget löschen">
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content" style="overflow-y:auto;">
            <div class="stat-grid">
              <div class="stat-box green">
                <div class="stat-label">Urlaub verfügbar</div>
                <div class="stat-value">
                  <span id="hrVacationRemaining">–</span>
                  <small>Tage</small>
                </div>
              </div>

              <div class="stat-box warning">
                <div class="stat-label">Krankheit</div>
                <div class="stat-value">
                  <span id="hrSickDays">–</span>
                  <small>Tage</small>
                </div>
              </div>

              <div class="stat-box blue">
                <div class="stat-label">Wiederkehrend</div>
                <div id="hrRecurringSummary" style="font-weight:900;font-size:.86rem;line-height:1.25;">
                  Lädt...
                </div>
              </div>
            </div>

            <div class="info-note" id="hrInfoNote">
              <i data-lucide="info" style="width:16px;color:var(--color-blue-dark);"></i>
              HR-Daten werden geladen...
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

        @include('admin.dashboard.employee.partials.absence-request-widget')

        <article class="widget col-span-4 row-span-8" data-widget-id="focus" data-widget-key="focus"
          data-widget-title="Fokus Heute" data-widget-tags="today task ticket lead angebot termin">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon danger">
                <i data-lucide="target"></i>
              </span>
              <span>
                <span class="widget-title">Mein Arbeitstag</span>
                <span class="widget-subtitle">Aufgaben, Termine, Tickets und offene Punkte</span>
              </span>
            </div>

            <div class="widget-tools">
              <span id="focusTodayCounter" class="pill danger">0 Offen</span>
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            @include('admin.dashboard.employee.partials.focus_today')
          </div>

          <div class="resize-handle"></div>
        </article>

        <article class="widget col-span-8 row-span-4" data-widget-id="personalChart" data-widget-key="personalChart"
          data-widget-title="Meine Arbeitsstunden" data-widget-tags="analytics today">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon blue">
                <i data-lucide="bar-chart-2"></i>
              </span>
              <span>
                <span class="widget-title">Meine Arbeitsstunden</span>
                <span class="widget-subtitle">Office / Montage pro Woche</span>
              </span>
            </div>

            <div class="widget-tools">
              <span class="pill blue">Woche</span>
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            <div class="chart-wrap">
              <canvas id="personalChart"></canvas>
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

        <article class="widget col-span-4 row-span-4" data-widget-id="notes" data-widget-key="notes"
          data-widget-title="Meine Notizen" data-widget-tags="today notes">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon warning">
                <i data-lucide="sticky-note"></i>
              </span>
              <span>
                <span class="widget-title">Meine Notizen</span>
                <span class="widget-subtitle">Datenbank gespeichert</span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            <div class="dash-notes-wrapper" id="dashNotesWidget">
              <div class="dash-notes-toolbar">
                <div class="dash-notes-toolbar-top">
                  <div class="dash-notes-tabs" id="dashNotesTabs">
                    <button class="dash-notes-tab active" type="button" data-status="open">
                      <i data-lucide="sticky-note"></i>
                      Aktiv
                    </button>

                    <button class="dash-notes-tab" type="button" data-status="done">
                      <i data-lucide="archive"></i>
                      Archiv
                    </button>
                  </div>

                  <button class="dash-notes-add" type="button" id="dashNoteOpenModalBtn">
                    <i data-lucide="plus"></i>
                    Neu
                  </button>
                </div>

                <label class="dash-notes-search">
                  <i data-lucide="search"></i>
                  <input type="search" id="dashNotesSearch" placeholder="Notizen suchen...">
                </label>
              </div>

              <div class="dash-notes-grid" id="dashNotesGrid">
                <div class="empty-state">Lädt...</div>
              </div>
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

        <article class="widget col-span-4 row-span-4" data-widget-id="shortcuts" data-widget-key="shortcuts"
          data-widget-title="Schnellzugriffe" data-widget-tags="today shortcuts">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon green">
                <i data-lucide="zap"></i>
              </span>
              <span>
                <span class="widget-title">Schnellzugriffe</span>
                <span class="widget-subtitle">Häufige Aktionen</span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            <div class="shortcut-widget-wrap">
              <div class="shortcut-actions">
                <button class="shortcut-manage-btn" type="button" id="openShortcutManager">
                  <i data-lucide="settings-2"></i>
                  Verwalten
                </button>
              </div>

              <div class="shortcut-grid" id="shortcutGrid">
                <div class="empty-state">Schnellzugriffe werden geladen...</div>
              </div>
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>
      </div>
    </section>

    <section id="view-department" class="view-section" data-view-section="department">
      <div class="widget-grid" id="grid-department">

        <article class="widget col-span-12 row-span-5" data-widget-id="deptOverview" data-widget-key="deptOverview"
          data-widget-title="Abteilungsübersicht" data-widget-tags="department analytics team">

          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon blue">
                <i data-lucide="building-2"></i>
              </span>

              <span>
                <span class="widget-title" id="deptTitle">Abteilungsübersicht</span>
                <span class="widget-subtitle" id="deptSubtitle">
                  Team, Aufgaben, Termine, Leads, Angebote, Aufträge und Rechnungen
                </span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content" style="overflow-y:auto;">
            <div class="dept-toolbar">
              <div class="dept-control-group">
                <label>Abteilung</label>
                <select id="departmentSelect" class="select-control">
                  <option value="">Lädt...</option>
                </select>
              </div>

              <div class="dept-control-group">
                <label>Von</label>
                <input type="date" id="departmentFromDate">
              </div>

              <div class="dept-control-group">
                <label>Bis</label>
                <input type="date" id="departmentToDate">
              </div>

              <button class="btn btn-primary" type="button" id="refreshDepartmentDashboardBtn">
                <i data-lucide="refresh-cw"></i>
                Aktualisieren
              </button>
            </div>

            <div class="dept-kpi-grid">
              <div class="dept-kpi-card">
                <span class="dept-kpi-icon blue">
                  <i data-lucide="users"></i>
                </span>
                <div>
                  <strong id="deptTeamMembers">–</strong>
                  <span>Teammitglieder</span>
                </div>
              </div>

              <div class="dept-kpi-card">
                <span class="dept-kpi-icon green">
                  <i data-lucide="user-round-plus"></i>
                </span>
                <div>
                  <strong id="deptLeads">–</strong>
                  <span>Leads</span>
                </div>
              </div>

              <div class="dept-kpi-card">
                <span class="dept-kpi-icon blue">
                  <i data-lucide="file-text"></i>
                </span>
                <div>
                  <strong id="deptOffers">–</strong>
                  <span>Angebote</span>
                </div>
              </div>

              <div class="dept-kpi-card">
                <span class="dept-kpi-icon green">
                  <i data-lucide="briefcase-business"></i>
                </span>
                <div>
                  <strong id="deptDeals">–</strong>
                  <span>Aufträge</span>
                </div>
              </div>

              <div class="dept-kpi-card">
                <span class="dept-kpi-icon warning">
                  <i data-lucide="ticket"></i>
                </span>
                <div>
                  <strong id="deptTickets">–</strong>
                  <span>Tickets</span>
                </div>
              </div>

              <div class="dept-kpi-card">
                <span class="dept-kpi-icon blue">
                  <i data-lucide="calendar-days"></i>
                </span>
                <div>
                  <strong id="deptAppointments">–</strong>
                  <span>Termine</span>
                </div>
              </div>

              <div class="dept-kpi-card">
                <span class="dept-kpi-icon green">
                  <i data-lucide="check-square"></i>
                </span>
                <div>
                  <strong id="deptTasks">–</strong>
                  <span>Aufgaben</span>
                </div>
              </div>

              <div class="dept-kpi-card">
                <span class="dept-kpi-icon warning">
                  <i data-lucide="euro"></i>
                </span>
                <div>
                  <strong id="deptInvoiceTotal">–</strong>
                  <span>Rechnungssumme</span>
                </div>
              </div>
            </div>

            <div class="info-note" id="deptInfoNote">
              <i data-lucide="info" style="width:16px;color:var(--color-blue-dark);"></i>
              Abteilungsdaten werden geladen...
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

        <article class="widget col-span-4 row-span-5" data-widget-id="deptTeam" data-widget-key="deptTeam"
          data-widget-title="Team" data-widget-tags="department team">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon green">
                <i data-lucide="users"></i>
              </span>
              <span>
                <span class="widget-title">Team</span>
                <span class="widget-subtitle">Mitarbeiter dieser Abteilung</span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            <div class="dept-team-list" id="deptTeamList">
              <div class="empty-state">Team wird geladen...</div>
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

        <article class="widget col-span-8 row-span-5" data-widget-id="deptCharts" data-widget-key="deptCharts"
          data-widget-title="Abteilungsanalyse" data-widget-tags="department analytics charts">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon blue">
                <i data-lucide="chart-no-axes-combined"></i>
              </span>
              <span>
                <span class="widget-title">Abteilungsanalyse</span>
                <span class="widget-subtitle">Arbeitslast, Pipeline und Rechnungen</span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            <div class="dept-chart-grid">
              <div class="dept-chart-card">
                <div class="dept-chart-title">Elemente nach Typ</div>
                <div class="chart-wrap">
                  <canvas id="deptItemsChart"></canvas>
                </div>
              </div>

              <div class="dept-chart-card">
                <div class="dept-chart-title">Team nach Position</div>
                <div class="chart-wrap">
                  <canvas id="deptPositionChart"></canvas>
                </div>
              </div>

              <div class="dept-chart-card">
                <div class="dept-chart-title">Arbeitslast pro Mitarbeiter</div>
                <div class="chart-wrap">
                  <canvas id="deptWorkloadChart"></canvas>
                </div>
              </div>

              <div class="dept-chart-card">
                <div class="dept-chart-title">Rechnungen nach Status</div>
                <div class="chart-wrap">
                  <canvas id="deptInvoiceChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

        <article class="widget col-span-6 row-span-6" data-widget-id="deptRecent" data-widget-key="deptRecent"
          data-widget-title="Aktuelle Vorgänge" data-widget-tags="department recent">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon warning">
                <i data-lucide="list-checks"></i>
              </span>
              <span>
                <span class="widget-title">Aktuelle Vorgänge</span>
                <span class="widget-subtitle">Kompakte Vorgänge mit Kunde, Produkt, Quelle und Team</span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            <div class="dept-recent-tabs" id="deptRecentTabs">
              <button type="button" class="active" data-type="leads">Leads</button>
              <button type="button" data-type="products">Produkte</button>
              <button type="button" data-type="offers">Angebote</button>
              <button type="button" data-type="deals">Aufträge</button>
              <button type="button" data-type="tickets">Tickets</button>
              <button type="button" data-type="appointments">Termine</button>
              <button type="button" data-type="tasks">Aufgaben</button>
              <button type="button" data-type="invoices">Rechnungen</button>
            </div>

            <div class="dept-recent-list" id="deptRecentList">
              <div class="empty-state">Daten werden geladen...</div>
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

        <article class="widget col-span-6 row-span-6" data-widget-id="deptChanges" data-widget-key="deptChanges"
          data-widget-title="Letzte Änderungen" data-widget-tags="department changes activity">
          <div class="widget-header">
            <div class="widget-title-wrap">
              <span class="widget-icon blue">
                <i data-lucide="history"></i>
              </span>
              <span>
                <span class="widget-title">Letzte Änderungen</span>
                <span class="widget-subtitle">Aktivitäten aus Lead Activity Logs</span>
              </span>
            </div>

            <div class="widget-tools">
              <button class="widget-tool-btn danger" type="button" data-delete-widget>
                <i data-lucide="trash-2"></i>
              </button>
            </div>
          </div>

          <div class="widget-content">
            <div class="dept-change-list" id="deptChangeList">
              <div class="empty-state">Änderungen werden geladen...</div>
            </div>
          </div>

          <div class="resize-handle"></div>
        </article>

      </div>
    </section>
    <section id="view-company" class="view-section" data-view-section="company">
      @include('admin.dashboard.employee.partials.company-cockpit')
    </section>
  </main>

  <div class="dash-note-modal-overlay" id="dashNoteModalOverlay">
    <div class="dash-note-modal">
      <div class="panel-header">
        <div>
          <div class="panel-title">Neue Notiz</div>
          <div class="panel-subtitle">Direkt im Dashboard speichern</div>
        </div>

        <button class="close-btn" type="button" id="dashNoteCloseModalBtn">
          <i data-lucide="x"></i>
        </button>
      </div>

      <form id="dashNoteForm">
        <div class="dash-note-form-group">
          <label class="setting-label">Titel</label>
          <input type="text" id="dashNoteTitle" class="dash-note-control" placeholder="Titel der Notiz" required>
        </div>

        <div class="dash-note-form-group">
          <label class="setting-label">Inhalt</label>
          <textarea id="dashNoteContent" class="dash-note-textarea" placeholder="Inhalt..." required></textarea>
        </div>

        <div class="dash-note-form-row">
          <div class="dash-note-form-group" style="flex:1;">
            <label class="setting-label">Kategorie</label>
            <select id="dashNoteCategory" class="dash-note-control"></select>
          </div>

          <div class="dash-note-form-group" style="width:88px;">
            <label class="setting-label">Farbe</label>
            <input type="color" id="dashNoteColor" class="dash-note-control" value="#fef9c3" style="padding:3px;">
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:.75rem;">
          <i data-lucide="save"></i>
          Speichern
        </button>
      </form>
    </div>
  </div>


  <div class="toast" id="toast">
    <i data-lucide="check-circle"></i>
    <span id="toastText">Gespeichert</span>
  </div>

  @include('admin.dashboard.employee.partials.absence-request-modal')
  @include('admin.dashboard.employee.partials.calendar-appointment-modal')
  <aside class="widget-tray" id="shortcutManagerPanel" aria-hidden="true">
    <div class="panel-header">
      <div>
        <div class="panel-title">Schnellzugriffe verwalten</div>
        <div class="panel-subtitle">Buttons hinzufügen, bearbeiten, löschen und sortieren</div>
      </div>

      <button class="close-btn" type="button" data-close-panels>
        <i data-lucide="x"></i>
      </button>
    </div>

    <div class="setting-group">
      <span class="setting-label">Verfügbare Aktionen</span>
      <div id="shortcutAvailableList"></div>
    </div>

    <div class="setting-group">
      <span class="setting-label">Meine Schnellzugriffe</span>
      <div id="shortcutManageList"></div>
    </div>
  </aside>
@endsection

@section('script')
  <script>
    window.DASHBOARD_WIDGET_ROUTES = {
      load: "{{ route('dashboard.widgets.load') }}",
      save: "{{ route('dashboard.widgets.save') }}",
      reset: "{{ route('dashboard.widgets.reset') }}",
      registry: "{{ route('dashboard.widgets.registry') }}"
    };
    window.DASHBOARD_ANALYTICS_ROUTES = {
      personalHours: "{{ route('employee.dashboard.personal_hours_chart') }}",
      miniAnalytics: "{{ route('employee.dashboard.mini_analytics_chart') }}"
    };
    window.DASHBOARD_NOTE_ROUTES = {
      list: "{{ route('notes') }}",
      search: "{{ route('notes.search') }}",
      categories: "{{ route('notes.fetch.category') }}",
      store: "{{ route('notes.store') }}",
      done: "{{ url('/notes_done') }}"
    };

    window.DASHBOARD_SHORTCUT_ROUTES = {
      index: "{{ route('dashboard.shortcuts.index') }}",
      available: "{{ route('dashboard.shortcuts.available') }}",
      store: "{{ route('dashboard.shortcuts.store') }}",
      reorder: "{{ route('dashboard.shortcuts.reorder') }}",
      updateBase: "{{ url('/dashboard/shortcuts') }}",
      deleteBase: "{{ url('/dashboard/shortcuts') }}"
    };

    window.DASHBOARD_ABSENCE_ROUTES = {
      data: "{{ route('dashboard.absence-request.data') }}",
      store: "{{ route('dashboard.absence-request.store') }}"
    };

    window.DASHBOARD_BREAKING_ROUTES = {
      breaking: "{{ route('breaking-news.active') }}",
      notifications: "{{ route('dashboard.notifications.index') }}"
    };
    window.DASHBOARD_HR_ROUTES = {
      widget: "{{ route('employee.dashboard.hr_widget') }}",
    };

    window.DASHBOARD_DEPARTMENT_ROUTES = {
      departments: "{{ route('dashboard.department.departments') }}",
      overview: "{{ route('dashboard.department.overview') }}"
    };

    window.DASHBOARD_COMPANY_ROUTES = {
      overview: "{{ route('dashboard.company.overview') }}"
    };
    window.DASHBOARD_CALENDAR_ROUTES = {
      employees: "{{ route('dashboard.calendar.employees') }}",
      month: "{{ route('dashboard.calendar.month') }}",
      day: "{{ route('dashboard.calendar.day') }}",
      show: "{{ url('/dashboard/calendar/appointments') }}"
    };

    window.DASHBOARD_EMPLOYEE_STATUS_ROUTES = {
      index: "{{ route('dashboard.employee-status.index') }}"
    };

  </script>

  <script>
    const dashboardRoutes = window.DASHBOARD_WIDGET_ROUTES || {};

    let widgetRegistry = {};
    let dashboardIsBooted = false;

    const state = {
      activeView: 'personal',
      editMode: false,
      sortAsc: true,
      settings: {
        theme: 'default',
        density: 'normal'
      },
      charts: {}
    };

    const chartLoading = {
      personalChart: false,
      miniCharts: new Set()
    };

    const focusData = [
      { id: 1, type: 'ticket', title: 'Heizungsausfall Kunde Schmidt', time: '08:00', user: 'Max Mustermann' },
      { id: 2, type: 'termin', title: 'Baustellenbesichtigung Objekt B', time: '10:30', user: 'Torsten' },
      { id: 3, type: 'task', title: 'Materialbestellung Elektro prüfen', time: '11:00', user: 'Anna Schmidt' },
      { id: 4, type: 'anfrage', title: 'Wartung Wärmepumpe Meier', time: '13:00', user: 'Team SHK' },
      { id: 5, type: 'angebot', title: 'Angebot PV Anlage erstellen', time: '14:30', user: 'Leon' },
      { id: 6, type: 'project', title: 'Kickoff Bürokomplex Süd', time: '16:00', user: 'Alle' },
      { id: 7, type: 'sonstiges', title: 'Auto in die Werkstatt', time: '17:00', user: 'Torsten' },
      { id: 8, type: 'lead', title: 'Neuer Lead: Einfamilienhaus Nord', time: '09:15', user: 'Vertrieb' }
    ];



    const widgetTemplates = {
      notes: {
        title: 'Notizen',
        subtitle: 'Benutzerdefiniert',
        icon: 'sticky-note',
        color: 'warning',
        tags: 'today notes',
        col: 4,
        row: 4,
        body: '<textarea class="notes-area" placeholder="Neue Notiz..."></textarea>'
      },

      employeeCalendar: {
        body: `
                            <div class="dash-cal-widget">
                                <div class="dash-cal-toolbar">
                                    <button type="button" class="dash-cal-nav" data-cal-prev>
                                        <i data-lucide="chevron-left"></i>
                                    </button>

                                    <div class="dash-cal-title-wrap">
                                        <strong id="dashCalMonthLabel">Kalender</strong>
                                        <span id="dashCalSummary">Termine werden geladen...</span>
                                    </div>

                                    <button type="button" class="dash-cal-nav" data-cal-next>
                                        <i data-lucide="chevron-right"></i>
                                    </button>

                                    <select id="dashCalEmployeeSelect" class="dash-cal-select">
                                        <option value="">Meine Termine</option>
                                    </select>
                                </div>

                                <div class="dash-cal-weekdays">
                                    <span>Mo</span><span>Di</span><span>Mi</span><span>Do</span><span>Fr</span><span>Sa</span><span>So</span>
                                </div>

                                <div class="dash-cal-grid" id="dashCalendarGrid">
                                    <div class="empty-state">Kalender wird geladen...</div>
                                </div>

                                <div class="dash-cal-day-card">
                                    <div class="dash-cal-day-head">
                                        <strong id="dashCalSelectedDay">Heute</strong>
                                        <span id="dashCalSelectedCount">0 Termine</span>
                                    </div>

                                    <div class="dash-cal-events" id="dashCalDayEvents">
                                        <div class="empty-state">Wähle einen Tag aus.</div>
                                    </div>
                                </div>
                            </div>
                        `
      },

      todayWeather: {
        body: `
                              <div class="weather-card" id="todayWeatherCard">
                                  <div class="weather-bg-orb weather-orb-one"></div>
                                  <div class="weather-bg-orb weather-orb-two"></div>

                                  <div class="weather-top">
                                      <div>
                                          <strong id="weatherLocation">Standort wird gesucht...</strong>
                                          <span id="weatherUpdated">Live Wetter</span>
                                      </div>

                                      <button type="button" class="weather-refresh-btn" id="refreshWeatherBtn">
                                          <i data-lucide="refresh-cw"></i>
                                      </button>
                                  </div>

                                  <div class="weather-main">
                                      <div class="weather-icon-wrap" id="weatherIconWrap">
                                          <i data-lucide="cloud-sun"></i>
                                      </div>

                                      <div>
                                          <div class="weather-temp">
                                              <span id="weatherTemp">–</span><small>°C</small>
                                          </div>
                                          <div class="weather-desc" id="weatherDescription">
                                              Wetterdaten werden geladen...
                                          </div>
                                      </div>
                                  </div>

                                  <div class="weather-stats">
                                      <div>
                                          <span id="weatherWind">–</span>
                                          <small>Wind</small>
                                      </div>

                                      <div>
                                          <span id="weatherRain">–</span>
                                          <small>Regen</small>
                                      </div>

                                      <div>
                                          <span id="weatherHumidity">–</span>
                                          <small>Feuchte</small>
                                      </div>
                                  </div>
                              </div>
                          `
      },

      absenceRequest: {
        body: `
                            <div class="absence-widget-content">
                                <div class="absence-profile-box">
                                    <img class="absenceEmployeeImage absence-avatar"
                                        src="/images/gender/male.png"
                                        alt="Mitarbeiter">

                                    <div>
                                        <strong class="absenceEmployeeName">Mein Antrag</strong>
                                        <span>Urlaub beantragen oder Krankheit melden.</span>
                                    </div>
                                </div>

                                <div class="absence-action-box">
                                    <button class="btn btn-primary openAbsenceRequestModalBtn"
                                            type="button"
                                            style="width:100%;">
                                        <i data-lucide="calendar-plus"></i>
                                        Abwesenheit beantragen
                                    </button>

                                    <p>
                                        Dein Antrag wird gespeichert und kann intern geprüft werden.
                                    </p>
                                </div>
                            </div>
                        `
      },
      shortcuts: {
        title: 'Schnellzugriffe',
        subtitle: 'Häufige Aktionen',
        icon: 'zap',
        color: 'green',
        tags: 'today shortcuts',
        col: 4,
        row: 4,
        body: `
                          <div class="shortcut-widget-wrap">
                              <div class="shortcut-actions">
                                  <button class="shortcut-manage-btn" type="button" id="openShortcutManager">
                                      <i data-lucide="settings-2"></i>
                                      Verwalten
                                  </button>
                              </div>

                              <div class="shortcut-grid" id="shortcutGrid">
                                  <div class="empty-state">Schnellzugriffe werden geladen...</div>
                              </div>
                          </div>
                      `
      },
      miniChart: {
        title: 'Mini Analytik',
        subtitle: 'Benutzerdefiniert',
        icon: 'line-chart',
        color: 'blue',
        tags: 'analytics',
        col: 4,
        row: 4,
        body: '<div class="chart-wrap"><canvas data-dynamic-chart="mini"></canvas></div>'
      },
      deptPie: {
        body: `
                  <div class="dept-chart-topline">
                      <span id="deptPieTotal">0 Vorgänge</span>
                      <span id="deptPiePeriod">Aktueller Zeitraum</span>
                  </div>

                  <div class="chart-wrap">
                      <canvas id="deptPieChart"></canvas>
                  </div>
              `
      },

      deptBar: {
        body: `
                  <div class="dept-chart-topline">
                      <span id="deptBarTotal">0 €</span>
                      <span id="deptBarPeriod">Monatliche Entwicklung</span>
                  </div>

                  <div class="chart-wrap">
                      <canvas id="deptBarChart"></canvas>
                  </div>
              `
      },

      deptHistory: {
        body: `
                  <div class="dept-history-list" id="deptHistoryList">
                      <div class="empty-state">
                          <div>
                              <i data-lucide="history"></i><br>
                              Historie wird geladen...
                          </div>
                      </div>
                  </div>
              `
      },
      empty: {
        title: 'Leeres Widget',
        subtitle: 'Benutzerdefiniert',
        icon: 'layout-template',
        color: 'blue',
        tags: 'custom',
        col: 4,
        row: 3,
        body: '<div class="empty-state"><div><i data-lucide="layout-template"></i><br>Freier Platz für eigene Inhalte</div></div>'
      }
    };

    const chartColors = {
      green: '#8bc34a',
      greenSoft: '#edf7df',
      blue: '#74b2d4',
      blueSoft: '#e6f0f7',
      warning: '#f8ac00',
      danger: '#e50656',
      grid: '#eef2f6'
    };

    let employeeStatusState = {
      active: [],
      inactive: [],
      summary: {
        active: 0,
        inactive: 0,
        sick: 0,
        holiday: 0
      }
    };

    function initEmployeeStatusCards() {
      bindEmployeeStatusDropdowns();
      fetchEmployeeStatus();

      setInterval(fetchEmployeeStatus, 60000);

      if (window.Echo) {
        window.Echo.channel('dashboard.employee-status')
          .listen('.dashboard.employee-status.updated', () => {
            fetchEmployeeStatus();
          });
      }
    }

    function bindEmployeeStatusDropdowns() {
      document.addEventListener('click', function (event) {
        const employeeLink = event.target.closest('.employee-status-person');

        if (employeeLink) {
          event.stopPropagation();

          const href = employeeLink.getAttribute('href');

          if (href && href !== '#') {
            window.location.href = href;
          }

          return;
        }

        const popover = event.target.closest('.employee-status-popover');

        if (popover) {
          event.stopPropagation();
          return;
        }

        const pill = event.target.closest('[data-status-dropdown]');

        document.querySelectorAll('.employee-status-pill.open').forEach(openPill => {
          if (openPill !== pill) {
            openPill.classList.remove('open');
          }
        });

        if (pill) {
          event.preventDefault();
          pill.classList.toggle('open');
          return;
        }

        document.querySelectorAll('.employee-status-pill.open').forEach(openPill => {
          openPill.classList.remove('open');
        });
      });
    }

    async function fetchEmployeeStatus() {
      const routes = window.DASHBOARD_EMPLOYEE_STATUS_ROUTES || {};

      if (!routes.index) {
        return;
      }

      try {
        const response = await fetch(routes.index, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        employeeStatusState.active = result.active || [];
        employeeStatusState.inactive = result.inactive || [];
        employeeStatusState.summary = result.summary || {};

        renderEmployeeStatusCards();
      } catch (error) {
        console.error('Employee status load failed:', error);
      }
    }

    function renderEmployeeStatusCards() {
      const activeCount = document.getElementById('employeeActiveCount');
      const inactiveCount = document.getElementById('employeeInactiveCount');
      const activeSub = document.getElementById('employeeActiveSub');
      const inactiveSub = document.getElementById('employeeInactiveSub');

      if (activeCount) {
        activeCount.textContent = employeeStatusState.summary.active ?? employeeStatusState.active.length;
      }

      if (inactiveCount) {
        inactiveCount.textContent = employeeStatusState.summary.inactive ?? employeeStatusState.inactive.length;
      }

      if (activeSub) {
        activeSub.textContent = `${employeeStatusState.active.length} verfügbar`;
      }

      if (inactiveSub) {
        const sick = Number(employeeStatusState.summary.sick || 0);
        const holiday = Number(employeeStatusState.summary.holiday || 0);
        inactiveSub.textContent = `${sick} krank · ${holiday} Urlaub`;
      }

      renderEmployeeStatusList('employeeActiveList', employeeStatusState.active, 'active');
      renderEmployeeStatusList('employeeInactiveList', employeeStatusState.inactive, 'inactive');

      if (window.lucide) {
        lucide.createIcons();
      }
    }

    function renderEmployeeStatusList(targetId, employees, fallbackStatus) {
      const target = document.getElementById(targetId);

      if (!target) {
        return;
      }

      if (!Array.isArray(employees) || !employees.length) {
        target.innerHTML = `
                              <div class="employee-status-empty">
                                  Keine Mitarbeiter gefunden.
                              </div>
                          `;
        return;
      }

      target.innerHTML = employees.map(employee => {
        const status = employee.absence_type || fallbackStatus;
        const label = employee.absence_label || (fallbackStatus === 'active' ? 'Aktiv' : 'Abwesend');

        return `
                              <a class="employee-status-person" href="${escapeHtml(employee.profile_url || '#')}">
                                  <img class="employee-status-avatar"
                                      src="${escapeHtml(employee.image || '')}"
                                      alt="${escapeHtml(employee.name || 'Mitarbeiter')}">

                                  <span class="employee-status-person-name">
                                      <strong>${escapeHtml(employee.name || 'Unbekannt')}</strong>
                                      <span>${fallbackStatus === 'active' ? 'Heute verfügbar' : 'Heute abwesend'}</span>
                                  </span>

                                  <span class="employee-status-badge ${escapeHtml(status)}">
                                      ${escapeHtml(label)}
                                  </span>
                              </a>
                          `;
      }).join('');
    }


    document.addEventListener('DOMContentLoaded', async () => {
      if (window.lucide) {
        lucide.createIcons();
      }

      await loadDashboard();

      renderWidgetTray();

      bindEvents();
      initDashboardShortcuts();
      initChartResizeObserver();

      initEmployeeStatusCards();

      initBreakingNewsBar();
      initDashboardNotes();
      updateClock();
      initHrWidget();

      setInterval(updateClock, 1000);

      initClockScaling();
      renderFocusList();
      bindWidgetTextareas();
      buildVisibilityList();
      applySettings();
      applyFilters();

      dashboardIsBooted = true;

      forceInitialChartRender();

      window.addEventListener('load', () => {
        forceInitialChartRender();
      });

      setTimeout(forceInitialChartRender, 500);
      setTimeout(forceInitialChartRender, 1200);
    });

    function csrfToken() {
      return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }


    function initDashboardShortcuts() {
      loadDashboardShortcuts();

      document.addEventListener('click', function (event) {
        const manageBtn = event.target.closest('#openShortcutManager');

        if (manageBtn) {
          event.preventDefault();
          openShortcutManager();
        }
      });
    }
    function bindEvents() {
      document.getElementById('overlay')?.addEventListener('click', closePanels);

      document.querySelectorAll('[data-close-panels]').forEach(btn => {
        btn.addEventListener('click', closePanels);
      });
      document.getElementById('organizeDashboardBtn')?.addEventListener('click', () => {
        organizeCurrentDashboardView();
      });

      document.getElementById('customizeBtn')?.addEventListener('click', () => openPanel('customizePanel'));
      document.getElementById('addWidgetBtn')?.addEventListener('click', () => openPanel('widgetTray'));
      document.getElementById('editModeBtn')?.addEventListener('click', () => toggleEditMode());
      document.getElementById('saveDashboardBtn')?.addEventListener('click', () => saveDashboard(true));
      document.getElementById('resetDashboardBtn')?.addEventListener('click', resetDashboard);
      document.getElementById('dashboardSearch')?.addEventListener('input', applyFilters);
      document.getElementById('quickFilter')?.addEventListener('change', applyFilters);

      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => switchView(btn.dataset.view));
      });

      document.querySelectorAll('.segmented').forEach(group => {
        group.addEventListener('click', event => {
          const btn = event.target.closest('button[data-value]');
          if (!btn) return;

          const setting = group.dataset.setting;

          state.settings[setting] = btn.dataset.value;

          group.querySelectorAll('button').forEach(item => {
            item.classList.toggle('active', item === btn);
          });

          applySettings();
          saveDashboard(false);
        });
      });

      document.getElementById('focusSearch')?.addEventListener('input', renderFocusList);
      document.getElementById('focusFilter')?.addEventListener('change', renderFocusList);

      document.getElementById('focusSort')?.addEventListener('click', () => {
        state.sortAsc = !state.sortAsc;
        renderFocusList();
      });

      document.addEventListener('click', event => {
        const deleteBtn = event.target.closest('[data-delete-widget]');
        if (deleteBtn) {
          deleteWidget(deleteBtn);
        }
      });
    }

    async function loadDashboard() {
      if (!dashboardRoutes.load) {
        showToast('Dashboard Route fehlt');
        return;
      }

      try {
        const response = await fetch(dashboardRoutes.load, {
          method: 'GET',
          headers: {
            'Accept': 'application/json'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        widgetRegistry = {};

        (result.registry || []).forEach(widget => {
          widgetRegistry[widget.key] = widget;
        });


        const settings = result.settings || {};

        state.activeView = settings.activeView || 'personal';
        state.settings.theme = settings.theme || 'default';
        state.settings.density = settings.density || 'normal';

        applyServerLayout(result.layout || []);
        switchView(state.activeView, false);
      } catch (error) {
        console.error(error);
        showToast('Dashboard konnte nicht geladen werden');
      }
    }

    function initHrWidget() {
      fetchHrWidget();

      // Optional refresh every 10 minutes
      clearInterval(initHrWidget.timer);
      initHrWidget.timer = setInterval(fetchHrWidget, 10 * 60 * 1000);
    }

    async function fetchHrWidget() {
      const routes = window.DASHBOARD_HR_ROUTES || {};

      if (!routes.widget) return;

      const vacationEl = document.getElementById('hrVacationRemaining');
      const sickEl = document.getElementById('hrSickDays');
      const recurringEl = document.getElementById('hrRecurringSummary');
      const infoEl = document.getElementById('hrInfoNote');
      const subtitleEl = document.getElementById('hrWidgetSubtitle');

      try {
        const response = await fetch(routes.widget, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        if (vacationEl) {
          vacationEl.textContent = Number(result.vacation?.remaining || 0).toFixed(0);
        }

        if (sickEl) {
          sickEl.textContent = Number(result.sick?.days || 0).toFixed(0);
        }

        if (subtitleEl) {
          const used = Number(result.vacation?.used || 0).toFixed(0);
          const total = Number(result.vacation?.annual_total || 0).toFixed(0);

          subtitleEl.textContent = `Urlaub ${used}/${total} · Jahr ${result.year}`;
        }

        if (recurringEl) {
          const summary = Array.isArray(result.recurring?.summary)
            ? result.recurring.summary
            : [];

          if (!summary.length) {
            recurringEl.innerHTML = 'Keine<br>Einträge';
          } else {
            recurringEl.innerHTML = summary
              .map(item => {
                const title = escapeHtml(item.title || 'Wiederkehrend');
                const date = item.next_date ? formatHrDate(item.next_date) : '–';

                return `${title}<br><small style="font-weight:800;color:var(--color-text-muted);">${date}</small>`;
              })
              .join('<br>');
          }
        }

        if (infoEl) {
          const messages = Array.isArray(result.messages) ? result.messages : [];

          const firstMessage = messages[0];

          infoEl.innerHTML = `
                    <i data-lucide="info" style="width:16px;color:var(--color-blue-dark);"></i>
                    ${escapeHtml(firstMessage?.message || 'Keine neuen HR-Dokumente vorhanden.')}
                  `;
        }

        if (window.lucide) {
          lucide.createIcons();
        }
      } catch (error) {
        console.error('HR widget load failed:', error);

        if (infoEl) {
          infoEl.innerHTML = `
                    <i data-lucide="alert-circle" style="width:16px;color:var(--color-danger);"></i>
                    HR-Daten konnten nicht geladen werden.
                  `;
        }

        if (window.lucide) {
          lucide.createIcons();
        }
      }
    }

    function formatHrDate(value) {
      if (!value) return '';

      const date = new Date(value);

      if (Number.isNaN(date.getTime())) {
        return String(value).slice(0, 10);
      }

      return date.toLocaleDateString('de-DE', {
        day: '2-digit',
        month: '2-digit'
      });
    }
    function applyServerLayout(layoutItems) {
      if (!Array.isArray(layoutItems) || !layoutItems.length) return;

      const existingWidgets = new Map();

      document.querySelectorAll('.widget').forEach(widget => {
        const key = widget.dataset.widgetKey || widget.dataset.widgetId;

        if (key) {
          existingWidgets.set(key, widget);
        }
      });

      /*
       * IMPORTANT:
       * Company cockpit is now a Blade partial.
       * Do NOT clear or rebuild #grid-company from saved layout.
       */
      document.querySelectorAll('.widget-grid').forEach(grid => {
        if (grid.id === 'grid-company') {
          return;
        }

        grid.innerHTML = '';
      });

      const grouped = {
        personal: [],
        department: [],
        company: []
      };

      layoutItems.forEach(item => {

        if (item.view === 'company') {
          return;
        }

        if (item.widgetKey === 'companyLine') {
          return;
        }

        if (!grouped[item.view]) {
          grouped[item.view] = [];
        }

        grouped[item.view].push(item);
      });

      Object.entries(grouped).forEach(([view, items]) => {
        if (view === 'company') {
          return;
        }

        const grid = document.getElementById(`grid-${view}`);

        if (!grid) return;

        items
          .sort((a, b) => Number(a.sortOrder || 0) - Number(b.sortOrder || 0))
          .forEach(item => {
            let widget = existingWidgets.get(item.widgetKey);

            if (!widget) {
              widget = createWidgetFromRegistry(
                item.widgetKey,
                item.instanceKey,
                item.config || {}
              );
            }

            if (!widget) return;

            widget.dataset.widgetId = item.instanceKey;
            widget.dataset.widgetKey = item.widgetKey;

            widget.className = widget.className
              .replace(/\bcol-span-\d+\b/g, '')
              .replace(/\brow-span-\d+\b/g, '')
              .trim();

            widget.classList.add(
              'widget',
              `col-span-${item.colSpan || 4}`,
              `row-span-${item.rowSpan || 4}`
            );

            widget.style.display = item.isVisible ? '' : 'none';

            applyWidgetConfig(widget, item.config || {});

            grid.appendChild(widget);
          });
      });

      if (window.lucide) {
        lucide.createIcons();
      }

      setTimeout(() => {
        resizeAllCharts();

        if (typeof initCompanyDashboard === 'function') {
          initCompanyDashboard();
        }
        if (typeof initTodayWeatherWidget === 'function') {
          initTodayWeatherWidget(true);
        }

        if (typeof initEmployeeCalendarWidget === 'function') {
          initEmployeeCalendarWidget(true);
        }
      }, 180);
    }

    function createWidgetFromRegistry(widgetKey, instanceKey = null, config = {}) {
      const registry = widgetRegistry[widgetKey];
      const template = widgetTemplates[widgetKey] || widgetTemplates.empty;

      if (!registry && !template) return null;

      const widget = document.createElement('article');

      const id = instanceKey || `${widgetKey}_${Date.now()}`;
      const title = registry?.title || template.title || widgetKey;
      const subtitle = registry?.subtitle || template.subtitle || 'Benutzerdefiniert';
      const icon = registry?.icon || template.icon || 'layout-template';
      const color = registry?.color || template.color || 'blue';
      const tags = Array.isArray(registry?.tags) ? registry.tags.join(' ') : (template.tags || '');
      const col = registry?.defaultColSpan || template.col || 4;
      const row = registry?.defaultRowSpan || template.row || 4;

      widget.className = `widget col-span-${col} row-span-${row}`;
      widget.dataset.widgetId = id;
      widget.dataset.widgetKey = widgetKey;
      widget.dataset.widgetTitle = title;
      widget.dataset.widgetTags = tags;

      widget.innerHTML = `
                                  <div class="widget-header">
                                      <div class="widget-title-wrap">
                                          <span class="widget-icon ${escapeHtml(color)}">
                                              <i data-lucide="${escapeHtml(icon)}"></i>
                                          </span>
                                          <span>
                                              <span class="widget-title">${escapeHtml(title)}</span>
                                              <span class="widget-subtitle">${escapeHtml(subtitle)}</span>
                                          </span>
                                      </div>
                                      <div class="widget-tools">
                                          <button class="widget-tool-btn danger" type="button" data-delete-widget>
                                              <i data-lucide="trash-2"></i>
                                          </button>
                                      </div>
                                  </div>
                                  <div class="widget-content">${template.body || widgetTemplates.empty.body}</div>
                                  <div class="resize-handle"></div>
                              `;

      applyWidgetConfig(widget, config);

      return widget;
    }

    function renderWidgetTray() {
      const tray = document.getElementById('widgetTrayList');
      if (!tray) return;

      const widgets = Object.values(widgetRegistry)
        .filter(widget => widget.defaultView !== 'global');

      tray.innerHTML = widgets.map(widget => `
                                  <div class="tray-item" data-add-widget="${escapeHtml(widget.key)}">
                                      <span>
                                          <i data-lucide="${escapeHtml(widget.icon || 'layout-template')}"></i>
                                          ${escapeHtml(widget.title || widget.key)}
                                      </span>
                                      <i data-lucide="plus-circle"></i>
                                  </div>
                              `).join('');

      tray.querySelectorAll('[data-add-widget]').forEach(item => {
        item.addEventListener('click', () => addWidget(item.dataset.addWidget));
      });

      lucide.createIcons();
    }

    async function saveDashboard(show = true) {
      if (!dashboardIsBooted && !show) return;

      if (!dashboardRoutes.save) {
        showToast('Speicher-Route fehlt');
        return;
      }

      const widgets = [];

      document.querySelectorAll('.widget-grid').forEach(grid => {
        const view = grid.id.replace('grid-', '');

        [...grid.querySelectorAll('.widget')].forEach((widget, index) => {
          const instanceKey = widget.dataset.widgetId;
          const widgetKey = widget.dataset.widgetKey || instanceKey;

          if (!instanceKey || !widgetKey) return;

          widgets.push({
            instanceKey: instanceKey,
            widgetKey: widgetKey,
            view: view,
            isVisible: widget.style.display === 'none' ? false : true,
            sortOrder: index + 1,
            colSpan: getSpan(widget, 'col-span-', 4),
            rowSpan: getSpan(widget, 'row-span-', 4),
            config: collectWidgetConfig(widget)
          });
        });
      });

      const payload = {
        activeView: state.activeView,
        settings: {
          theme: state.settings.theme,
          density: state.settings.density
        },
        widgets: widgets
      };

      try {
        const response = await fetch(dashboardRoutes.save, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        if (show) {
          showToast('Dashboard gespeichert');
        }
      } catch (error) {
        console.error(error);
        showToast('Speichern fehlgeschlagen');
      }
    }

    async function resetDashboard() {
      if (!dashboardRoutes.reset) {
        showToast('Reset-Route fehlt');
        return;
      }

      try {
        const response = await fetch(dashboardRoutes.reset, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        showToast('Dashboard zurückgesetzt');

        setTimeout(() => {
          window.location.reload();
        }, 350);
      } catch (error) {
        console.error(error);
        showToast('Zurücksetzen fehlgeschlagen');
      }
    }

    function switchView(viewName, persist = true) {
      state.activeView = viewName || 'personal';

      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === state.activeView);
      });

      document.querySelectorAll('.view-section').forEach(section => {
        section.classList.toggle('active', section.dataset.viewSection === state.activeView);
      });

      if (state.editMode) {
        toggleEditMode(false);
      }

      applyFilters();

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          initChartsForView(state.activeView);
          resizeChartsForActiveView();

          setTimeout(() => {
            initChartsForView(state.activeView);
            resizeChartsForActiveView();
          }, 180);

          setTimeout(() => {
            resizeChartsForActiveView();
          }, 420);
        });
      });

      if (persist) {
        saveDashboard(false);
      }
    }

    function resizeChartsForActiveView() {
      const activeSection = document.querySelector('.view-section.active');

      if (!activeSection) {
        return;
      }

      activeSection.querySelectorAll('canvas').forEach(canvas => {
        const chart = Chart.getChart(canvas);

        if (!chart) {
          return;
        }

        const wrap = canvas.closest('.chart-wrap');

        if (wrap) {
          const rect = wrap.getBoundingClientRect();

          if (rect.width < 20 || rect.height < 20) {
            return;
          }
        }

        chart.resize();
        chart.update('none');
      });
    }

    function forceInitialChartRender() {
      const view = state.activeView || 'personal';
      const activeSection = document.querySelector(`.view-section[data-view-section="${view}"]`);

      if (!activeSection) {
        return;
      }

      activeSection.classList.add('active');

      const chartWidgets = activeSection.querySelectorAll(
        '[data-widget-key="personalChart"], [data-widget-key="deptCharts"], [data-widget-key="deptPie"], [data-widget-key="deptBar"], [data-widget-key="companyRevenue"], [data-widget-key="companyTypes"], [data-widget-key="companyDepartmentPerformance"]'
      );

      chartWidgets.forEach(widget => {
        widget.style.display = '';
        widget.style.visibility = 'visible';
        widget.style.opacity = '1';

        const content = widget.querySelector('.widget-content');
        if (content) {
          content.style.minHeight = '220px';
        }

        widget.querySelectorAll('.chart-wrap').forEach(wrap => {
          wrap.style.minHeight = '220px';
          wrap.style.height = '220px';
        });
      });

      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          initChartsForView(view);

          setTimeout(() => {
            initChartsForView(view);
            resizeChartsForActiveView();
          }, 150);

          setTimeout(() => {
            resizeChartsForActiveView();
          }, 350);

          setTimeout(() => {
            resizeChartsForActiveView();
          }, 700);
        });
      });
    }
    function openPanel(id) {
      closePanels(false);

      document.getElementById(id)?.classList.add('open');
      document.getElementById('overlay')?.classList.add('show');

      lucide.createIcons();
    }

    function closePanels(hideOverlay = true) {
      document.querySelectorAll('.side-panel, .widget-tray').forEach(panel => {
        panel.classList.remove('open');
      });

      if (hideOverlay) {
        document.getElementById('overlay')?.classList.remove('show');
      }
    }

    function updateClock() {
      const now = new Date();
      const sec = now.getSeconds();
      const min = now.getMinutes();
      const hr = now.getHours();

      const secondHand = document.getElementById('secondHand');
      const minuteHand = document.getElementById('minuteHand');
      const hourHand = document.getElementById('hourHand');
      const digitalTime = document.getElementById('digitalTime');
      const digitalDate = document.getElementById('digitalDate');

      if (!secondHand || !minuteHand || !hourHand) return;

      secondHand.style.transform = `rotate(${sec * 6}deg)`;
      minuteHand.style.transform = `rotate(${min * 6 + sec * .1}deg)`;
      hourHand.style.transform = `rotate(${(hr % 12) * 30 + min * .5}deg)`;

      if (digitalTime) {
        digitalTime.textContent = now.toLocaleTimeString('de-DE', {
          hour: '2-digit',
          minute: '2-digit'
        });
      }

      if (digitalDate) {
        digitalDate.textContent = now.toLocaleDateString('de-DE', {
          weekday: 'long',
          day: '2-digit',
          month: 'short'
        });
      }
    }

    function initClockScaling() {
      if (!window.ResizeObserver) return;

      const observer = new ResizeObserver(entries => {
        entries.forEach(entry => {
          const container = entry.target;
          const wrapper = container.querySelector('.analog-clock-wrapper');
          const text = container.querySelector('.clock-text');

          if (!wrapper || !text) return;

          const availableHeight = container.clientHeight - text.offsetHeight - 12;
          const availableWidth = container.clientWidth;
          const size = Math.min(availableWidth, availableHeight);
          const scale = Math.max(.42, Math.min(size / 165, 2.8));

          wrapper.style.transform = `scale(${scale})`;
        });
      });

      document.querySelectorAll('.clock-container').forEach(container => {
        observer.observe(container);
      });
    }

    function renderFocusList() {
      const container = document.getElementById('focusListContainer');
      if (!container) return;

      const search = (document.getElementById('focusSearch')?.value || '').toLowerCase();
      const filter = document.getElementById('focusFilter')?.value || 'all';

      const filtered = focusData
        .filter(item => {
          const matchesSearch =
            item.title.toLowerCase().includes(search) ||
            item.user.toLowerCase().includes(search) ||
            item.type.toLowerCase().includes(search);

          const matchesFilter = filter === 'all' || item.type === filter;

          return matchesSearch && matchesFilter;
        })
        .sort((a, b) => state.sortAsc ? a.time.localeCompare(b.time) : b.time.localeCompare(a.time));

      const counter = document.getElementById('focusCount');

      if (counter) {
        counter.textContent = `${filtered.length} Offen`;
      }

      container.innerHTML = filtered.map(item => `
                                  <li class="focus-item">
                                      <div class="type-badge type-${escapeHtml(item.type)}">${escapeHtml(item.type)}</div>
                                      <div style="min-width:0;">
                                          <div class="focus-title">${escapeHtml(item.title)}</div>
                                          <div class="focus-meta">
                                              <span><i data-lucide="clock" style="width:12px;"></i> ${escapeHtml(item.time)}</span>
                                              <span><i data-lucide="user" style="width:12px;"></i> ${escapeHtml(item.user)}</span>
                                          </div>
                                      </div>
                                      <button class="focus-open-link" type="button" title="Öffnen">
                                          <i data-lucide="arrow-up-right" style="width:15px;"></i>
                                      </button>
                                  </li>
                              `).join('') || '<li class="empty-state">Keine Einträge gefunden.</li>';

      lucide.createIcons();
    }

    function toggleEditMode(force) {
      state.editMode = typeof force === 'boolean' ? force : !state.editMode;

      document.body.classList.toggle('edit-mode', state.editMode);

      const editBtn = document.getElementById('editModeBtn');
      const addBtn = document.getElementById('addWidgetBtn');

      if (editBtn) {
        editBtn.classList.toggle('is-active', state.editMode);
        editBtn.innerHTML = state.editMode
          ? '<i data-lucide="check"></i> Fertig'
          : '<i data-lucide="layout-grid"></i> Dashboard editieren';
      }

      if (addBtn) {
        addBtn.style.display = state.editMode ? 'inline-flex' : 'none';
      }

      if (state.editMode) {
        initEditFeatures();
      } else {
        disableEditFeatures();
        saveDashboard(false);
      }

      lucide.createIcons();
    }

    let draggedWidget = null;
    let dragPlaceholder = null;
    let lastDropGrid = null;
    let dragSaveTimer = null;
    let isResizing = false;
    let currentResizeWidget = null;
    let initialMouseX = 0;
    let initialMouseY = 0;
    let initialWidth = 0;
    let initialHeight = 0;
    let gridColWidth = 0;
    let gridRowHeight = 0;
    let gridGap = 0;

    function initEditFeatures() {
      document.querySelectorAll('.view-section.active .widget-grid').forEach(grid => {
        if (!grid.dataset.gridDropBound) {
          grid.dataset.gridDropBound = '1';
          grid.addEventListener('dragover', handleGridDragOver);
          grid.addEventListener('drop', handleGridDrop);
          grid.addEventListener('dragleave', handleGridDragLeave);
        }
      });

      document.querySelectorAll('.view-section.active .widget').forEach(widget => {
        const header = widget.querySelector('.widget-header');

        if (header && !header.dataset.editBound) {
          header.dataset.editBound = '1';
          header.setAttribute('draggable', 'true');
          header.addEventListener('dragstart', handleDragStart);
          header.addEventListener('dragend', handleDragEnd);
        }

        const handle = widget.querySelector('.resize-handle');

        if (handle && !handle.dataset.resizeBound) {
          handle.dataset.resizeBound = '1';
          handle.addEventListener('mousedown', startResize);
        }
      });
    }

    function disableEditFeatures() {
      document.querySelectorAll('.widget-grid').forEach(grid => {
        grid.removeEventListener('dragover', handleGridDragOver);
        grid.removeEventListener('drop', handleGridDrop);
        grid.removeEventListener('dragleave', handleGridDragLeave);
        delete grid.dataset.gridDropBound;
      });

      document.querySelectorAll('.widget').forEach(widget => {
        const header = widget.querySelector('.widget-header');

        if (header) {
          header.removeAttribute('draggable');
          header.removeEventListener('dragstart', handleDragStart);
          header.removeEventListener('dragend', handleDragEnd);
          delete header.dataset.editBound;
        }

        widget.classList.remove(
          'dragging',
          'drag-over',
          'is-drag-target-before',
          'is-drag-target-after'
        );

        delete widget.dataset.dropBound;

        const handle = widget.querySelector('.resize-handle');

        if (handle) {
          handle.removeEventListener('mousedown', startResize);
          delete handle.dataset.resizeBound;
        }
      });

      removeDragPlaceholder();
    }

    function handleDragStart(event) {
      if (!state.editMode || event.target.closest('.resize-handle')) {
        return;
      }

      draggedWidget = event.currentTarget.closest('.widget');

      if (!draggedWidget) {
        return;
      }

      const grid = draggedWidget.closest('.widget-grid');

      if (!grid) {
        return;
      }

      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', draggedWidget.dataset.widgetId || draggedWidget.dataset.widgetKey || 'widget');

      createDragPlaceholder(draggedWidget);

      requestAnimationFrame(() => {
        draggedWidget.classList.add('dragging');
      });
    }

    function handleDragEnd() {
      if (dragPlaceholder && dragPlaceholder.parentNode && draggedWidget) {
        dragPlaceholder.parentNode.insertBefore(draggedWidget, dragPlaceholder);
      }

      cleanupDragClasses();
      removeDragPlaceholder();

      draggedWidget = null;
      lastDropGrid = null;

      scheduleDashboardSave();

      setTimeout(() => {
        resizeAllCharts();

        if (typeof initEmployeeCalendarWidget === 'function') {
          initEmployeeCalendarWidget(true);
        }

        if (typeof initDeptCharts === 'function') {
          initDeptCharts();
        }

        if (typeof initCompanyDashboard === 'function') {
          initCompanyDashboard();
        }
      }, 120);
    }

    function handleGridDragOver(event) {
      if (!state.editMode || !draggedWidget || isResizing) {
        return;
      }

      event.preventDefault();
      event.dataTransfer.dropEffect = 'move';

      const grid = event.currentTarget;

      if (!grid || !grid.classList.contains('widget-grid')) {
        return;
      }

      lastDropGrid = grid;

      if (!dragPlaceholder) {
        createDragPlaceholder(draggedWidget);
      }

      const afterElement = getDragAfterElement(grid, event.clientX, event.clientY);

      cleanupDragTargetClasses();

      if (!afterElement) {
        grid.appendChild(dragPlaceholder);
        markLastVisibleWidget(grid, 'after');
        return;
      }

      const before = isPointerBeforeElement(afterElement, event.clientX, event.clientY);

      if (before) {
        grid.insertBefore(dragPlaceholder, afterElement);
        afterElement.classList.add('is-drag-target-before');
      } else {
        grid.insertBefore(dragPlaceholder, afterElement.nextSibling);
        afterElement.classList.add('is-drag-target-after');
      }
    }

    function handleGridDrop(event) {
      if (!state.editMode || !draggedWidget) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();

      const grid = event.currentTarget;

      if (dragPlaceholder && dragPlaceholder.parentNode === grid) {
        grid.insertBefore(draggedWidget, dragPlaceholder);
      }

      cleanupDragClasses();
      removeDragPlaceholder();

      scheduleDashboardSave();
    }

    function handleGridDragLeave(event) {
      const grid = event.currentTarget;
      const related = event.relatedTarget;

      if (!grid || grid.contains(related)) {
        return;
      }

      cleanupDragTargetClasses();
    }

    function createDragPlaceholder(widget) {
      removeDragPlaceholder();

      dragPlaceholder = document.createElement('div');
      dragPlaceholder.className = 'widget-drop-placeholder';

      const colSpan = getSpan(widget, 'col-span-', 4);
      const rowSpan = getSpan(widget, 'row-span-', 4);

      dragPlaceholder.classList.add(`col-span-${colSpan}`, `row-span-${rowSpan}`);
    }

    function removeDragPlaceholder() {
      if (dragPlaceholder && dragPlaceholder.parentNode) {
        dragPlaceholder.parentNode.removeChild(dragPlaceholder);
      }

      dragPlaceholder = null;
    }

    function cleanupDragClasses() {
      document.querySelectorAll('.widget').forEach(widget => {
        widget.classList.remove(
          'dragging',
          'drag-over',
          'is-drag-target-before',
          'is-drag-target-after'
        );
      });
    }

    function cleanupDragTargetClasses() {
      document.querySelectorAll('.widget').forEach(widget => {
        widget.classList.remove(
          'drag-over',
          'is-drag-target-before',
          'is-drag-target-after'
        );
      });
    }

    function getDragAfterElement(grid, mouseX, mouseY) {
      const widgets = [...grid.querySelectorAll('.widget:not(.dragging)')]
        .filter(widget => widget !== draggedWidget && widget.style.display !== 'none');

      if (!widgets.length) {
        return null;
      }

      let closest = {
        offset: Number.POSITIVE_INFINITY,
        element: null
      };

      widgets.forEach(widget => {
        const rect = widget.getBoundingClientRect();

        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;

        const distance = Math.hypot(mouseX - centerX, mouseY - centerY);

        if (distance < closest.offset) {
          closest = {
            offset: distance,
            element: widget
          };
        }
      });

      return closest.element;
    }

    function isPointerBeforeElement(element, mouseX, mouseY) {
      const rect = element.getBoundingClientRect();

      const isSameRow = mouseY >= rect.top && mouseY <= rect.bottom;

      if (isSameRow) {
        return mouseX < rect.left + rect.width / 2;
      }

      return mouseY < rect.top + rect.height / 2;
    }

    function markLastVisibleWidget(grid, mode = 'after') {
      const widgets = [...grid.querySelectorAll('.widget:not(.dragging)')]
        .filter(widget => widget !== draggedWidget && widget.style.display !== 'none');

      const last = widgets[widgets.length - 1];

      if (!last) {
        return;
      }

      last.classList.add(mode === 'before' ? 'is-drag-target-before' : 'is-drag-target-after');
    }

    function scheduleDashboardSave() {
      clearTimeout(dragSaveTimer);

      dragSaveTimer = setTimeout(() => {
        saveDashboard(false);
      }, 250);
    }

    function organizeCurrentDashboardView() {
      const activeView = state.activeView || 'personal';
      const grid = document.getElementById(`grid-${activeView}`);

      if (!grid) {
        showToast('Aktives Dashboard wurde nicht gefunden.', 'warning', 2400);
        return;
      }

      const widgets = [...grid.querySelectorAll('.widget')]
        .filter(widget => widget.style.display !== 'none');

      if (widgets.length <= 1) {
        showToast('Es gibt nicht genug Elemente zum Organisieren.', 'warning', 2400);
        return;
      }

      grid.classList.add('is-organizing');

      const sortedWidgets = widgets
        .map((widget, originalIndex) => {
          const colSpan = getSpan(widget, 'col-span-', 4);
          const rowSpan = getSpan(widget, 'row-span-', 4);
          const area = colSpan * rowSpan;

          return {
            widget,
            originalIndex,
            colSpan,
            rowSpan,
            area
          };
        })
        .sort((a, b) => {
          if (b.area !== a.area) {
            return b.area - a.area;
          }

          if (b.colSpan !== a.colSpan) {
            return b.colSpan - a.colSpan;
          }

          if (b.rowSpan !== a.rowSpan) {
            return b.rowSpan - a.rowSpan;
          }

          return a.originalIndex - b.originalIndex;
        });

      sortedWidgets.forEach(item => {
        grid.appendChild(item.widget);
      });

      requestAnimationFrame(() => {
        sortedWidgets.forEach(item => {
          item.widget.classList.add('is-organized-flash');

          setTimeout(() => {
            item.widget.classList.remove('is-organized-flash');
          }, 850);
        });

        resizeAllCharts?.();
        resizeChartsForActiveView?.();

        setTimeout(() => {
          grid.classList.remove('is-organizing');
          resizeAllCharts?.();
          resizeChartsForActiveView?.();
        }, 300);
      });

      saveDashboard(false);
      showToast('Dashboard wurde organisiert.');
    }
    function startResize(event) {
      if (!state.editMode) return;

      isResizing = true;
      currentResizeWidget = event.target.closest('.widget');
      initialMouseX = event.clientX;
      initialMouseY = event.clientY;
      initialWidth = currentResizeWidget.offsetWidth;
      initialHeight = currentResizeWidget.offsetHeight;

      const grid = currentResizeWidget.parentNode;
      const gridStyle = getComputedStyle(grid);
      const cols = gridStyle.getPropertyValue('grid-template-columns').split(' ');

      gridGap = parseFloat(gridStyle.gap) || 20;
      gridColWidth = parseFloat(cols[0]) + gridGap;
      gridRowHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--grid-row')) + gridGap;

      document.addEventListener('mousemove', doResize);
      document.addEventListener('mouseup', stopResize);

      event.preventDefault();
      event.stopPropagation();
    }

    let resizeRaf = null;

    function doResize(event) {
      if (!isResizing || !currentResizeWidget) return;

      const newWidth = Math.max(140, initialWidth + event.clientX - initialMouseX);
      const newHeight = Math.max(130, initialHeight + event.clientY - initialMouseY);

      const colSpan = Math.max(2, Math.min(12, Math.round((newWidth + gridGap) / gridColWidth)));
      const rowSpan = Math.max(2, Math.min(24, Math.round((newHeight + gridGap) / gridRowHeight)));

      currentResizeWidget.className = currentResizeWidget.className
        .replace(/\bcol-span-\d+\b/g, '')
        .replace(/\brow-span-\d+\b/g, '')
        .trim();

      currentResizeWidget.classList.add('widget', `col-span-${colSpan}`, `row-span-${rowSpan}`);
      if (resizeRaf) {
        cancelAnimationFrame(resizeRaf);
      }

      resizeRaf = requestAnimationFrame(() => {
        resizeAllCharts();
      });

      currentResizeWidget.dataset.liveColSpan = colSpan;
      currentResizeWidget.dataset.liveRowSpan = rowSpan;

      if (resizeRaf) {
        cancelAnimationFrame(resizeRaf);
      }

      resizeRaf = requestAnimationFrame(() => {
        resizeChartsInsideWidget(currentResizeWidget);
      });
    }

    function resizeChartsInsideWidget(widget) {
      if (!widget) return;

      widget.querySelectorAll('canvas').forEach(canvas => {
        const chart = Chart.getChart(canvas);

        if (!chart) {
          return;
        }

        const wrap = canvas.closest('.chart-wrap');

        if (!wrap) {
          chart.resize();
          chart.update('none');
          return;
        }

        const rect = wrap.getBoundingClientRect();

        if (rect.width > 0 && rect.height > 0) {
          chart.resize(rect.width, rect.height);
        } else {
          chart.resize();
        }

        chart.update('none');
      });
    }

    function stopResize() {
      const widget = currentResizeWidget;

      isResizing = false;
      currentResizeWidget = null;

      document.removeEventListener('mousemove', doResize);
      document.removeEventListener('mouseup', stopResize);

      if (resizeRaf) {
        cancelAnimationFrame(resizeRaf);
        resizeRaf = null;
      }

      resizeAllCharts();

      setTimeout(() => {
        resizeAllCharts();
      }, 180);

      setTimeout(() => {
        resizeChartsInsideWidget(widget);
        resizeAllCharts();

        if (typeof initDeptCharts === 'function') {
          initDeptCharts();
        }

        if (typeof initCompanyDashboard === 'function') {
          initCompanyDashboard();
        }

        if (typeof initEmployeeCalendarWidget === 'function') {
          initEmployeeCalendarWidget(true);
        }
      }, 120);

      saveDashboard(false);
    }

    function addWidget(type) {
      const registry = widgetRegistry[type];

      if (registry && registry.allowMultiple === false) {
        const existing = document.querySelector(`.widget[data-widget-key="${type}"]`);

        if (existing) {
          showToast('Widget existiert bereits');
          return;
        }
      }

      const instanceKey = `${type}_${Date.now()}`;
      const widget = createWidgetFromRegistry(type, instanceKey, {});

      if (!widget) {
        showToast('Widget nicht gefunden');
        return;
      }

      if (type === 'todayWeather') {
        setTimeout(() => {
          initTodayWeatherWidget(true);
        }, 180);
      }
      if (type === 'employeeCalendar') {
        calendarWidgetState.initialized = false;
        calendarWidgetState.loadedEmployees = false;

        setTimeout(() => {
          initEmployeeCalendarWidget(true);
        }, 220);
      }
      const grid = document.querySelector('.view-section.active .widget-grid');

      if (!grid) {
        showToast('Aktive Ansicht nicht gefunden');
        return;
      }

      grid.appendChild(widget);

      lucide.createIcons();
      bindWidgetTextareas();

      if (state.editMode) {
        disableEditFeatures();
        initEditFeatures();
      }

      if (type === 'miniChart') {
        initDynamicMiniChart(widget);
      }

      if (String(type).startsWith('dept')) {
        setTimeout(() => {
          initDeptCharts();
          resizeAllCharts();
        }, 250);
      }

      if (type === 'absenceRequest' && typeof loadAbsenceData === 'function') {
        setTimeout(loadAbsenceData, 80);
      }
      buildVisibilityList();
      closePanels();
      saveDashboard(false);
      showToast('Widget hinzugefügt');
    }

    function deleteWidget(button) {
      if (!state.editMode) return;

      const widget = button.closest('.widget');
      if (!widget) return;

      widget.remove();

      buildVisibilityList();
      saveDashboard(false);
      showToast('Widget entfernt');
    }

    function buildVisibilityList() {
      const list = document.getElementById('widgetVisibilityList');
      if (!list) return;

      const widgets = [...document.querySelectorAll('[data-widget-id]')]
        .filter(item => item.classList.contains('widget') || item.classList.contains('feed-bar') || item.classList.contains('sa-bn'));

      const seen = new Set();

      list.innerHTML = widgets
        .filter(widget => {
          const id = widget.dataset.widgetId;

          if (!id || seen.has(id)) return false;

          seen.add(id);
          return true;
        })
        .map(widget => {
          const id = widget.dataset.widgetId;
          const title = widget.dataset.widgetTitle || id;
          const checked = widget.style.display !== 'none';

          return `
                                          <label class="check-row">
                                              <span>${escapeHtml(title)}</span>
                                              <span class="switch">
                                                  <input type="checkbox" data-widget-visibility="${escapeHtml(id)}" ${checked ? 'checked' : ''}>
                                                  <span class="slider"></span>
                                              </span>
                                          </label>
                                      `;
        }).join('');

      list.querySelectorAll('[data-widget-visibility]').forEach(input => {
        input.addEventListener('change', () => {
          const id = input.dataset.widgetVisibility;
          const widget = document.querySelector(`[data-widget-id="${cssEscape(id)}"]`);

          if (widget) {
            widget.style.display = input.checked ? '' : 'none';
          }

          saveDashboard(false);
          setTimeout(resizeAllCharts, 50);
        });
      });
    }

    function applySettings() {
      document.body.classList.remove(
        'theme-default',
        'theme-soft',
        'theme-contrast',
        'density-compact',
        'density-normal',
        'density-comfortable'
      );

      document.body.classList.add(
        `theme-${state.settings.theme}`,
        `density-${state.settings.density}`
      );

      document.querySelectorAll('.segmented[data-setting="theme"] button').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.value === state.settings.theme);
      });

      document.querySelectorAll('.segmented[data-setting="density"] button').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.value === state.settings.density);
      });

      setTimeout(resizeAllCharts, 50);
    }

    function applyFilters() {
      const search = document.getElementById('dashboardSearch')?.value.trim().toLowerCase() || '';
      const filter = document.getElementById('quickFilter')?.value || 'all';
      const activeWidgets = document.querySelectorAll('.view-section.active .widget');

      activeWidgets.forEach(widget => {
        const title = (widget.dataset.widgetTitle || '').toLowerCase();
        const tags = (widget.dataset.widgetTags || '').toLowerCase();
        const text = widget.textContent.toLowerCase();

        const matchesSearch = !search || title.includes(search) || tags.includes(search) || text.includes(search);
        const matchesFilter = filter === 'all' || tags.includes(filter);

        widget.classList.toggle('is-hidden-by-filter', !matchesSearch || !matchesFilter);
      });

      setTimeout(resizeAllCharts, 50);
    }

    function collectWidgetConfig(widget) {
      const config = {};

      const textarea = widget.querySelector('textarea');

      if (textarea) {
        config.text = textarea.value || '';
      }

      return config;
    }

    function applyWidgetConfig(widget, config = {}) {
      const textarea = widget.querySelector('textarea');

      if (textarea && typeof config.text === 'string') {
        textarea.value = config.text;
      }

      if (textarea && !textarea.dataset.autosaveBound) {
        textarea.dataset.autosaveBound = '1';
        textarea.addEventListener('input', debounce(() => {
          saveDashboard(false);
        }, 600));
      }
    }

    function bindWidgetTextareas() {
      document.querySelectorAll('.widget textarea').forEach(textarea => {
        if (textarea.dataset.autosaveBound) return;

        textarea.dataset.autosaveBound = '1';

        textarea.addEventListener('input', debounce(() => {
          saveDashboard(false);
        }, 600));
      });
    }

    function getSpan(element, prefix, fallback) {
      const found = [...element.classList].find(cls => cls.startsWith(prefix));

      if (!found) return fallback;

      const value = parseInt(found.replace(prefix, ''), 10);

      return Number.isFinite(value) ? value : fallback;
    }

    function initChartsForView(view) {
      if (view === 'personal') initPersonalChart();
      if (view === 'department') initDeptCharts();

      if (view === 'company') {
        initCompanyDashboard();
      }

      document.querySelectorAll('[data-dynamic-chart="mini"]').forEach(canvas => {
        initDynamicMiniChart(canvas.closest('.widget'));
      });
    }

    function destroyChart(keyOrCanvas) {
      if (!window.Chart) return;

      let canvas = null;

      if (typeof keyOrCanvas === 'string') {
        const storedChart = state.charts[keyOrCanvas];

        if (storedChart) {
          try {
            storedChart.destroy();
          } catch (e) {
            console.warn('Stored chart destroy failed:', e);
          }

          delete state.charts[keyOrCanvas];
        }

        canvas = document.getElementById(keyOrCanvas);

        if (!canvas) {
          canvas = document.querySelector(`[data-chart-key="${cssEscape(keyOrCanvas)}"]`);
        }
      } else if (keyOrCanvas instanceof HTMLCanvasElement) {
        canvas = keyOrCanvas;
      }

      if (canvas) {
        const existingChart = Chart.getChart(canvas);

        if (existingChart) {
          try {
            existingChart.destroy();
          } catch (e) {
            console.warn('Canvas chart destroy failed:', e);
          }
        }

        delete canvas.dataset.chartKey;
      }

      Object.keys(state.charts).forEach(key => {
        const chart = state.charts[key];

        if (!chart) {
          delete state.charts[key];
          return;
        }

        if (canvas && chart.canvas === canvas) {
          try {
            chart.destroy();
          } catch (e) {
            console.warn('Duplicate chart destroy failed:', e);
          }

          delete state.charts[key];
        }
      });
    }


    function registerChart(key, canvas, chart) {
      state.charts[key] = chart;

      if (canvas) {
        canvas.dataset.chartKey = key;
      }
    }

    async function initPersonalChart() {
      const canvas = document.getElementById('personalChart');

      if (!canvas || !window.Chart) return;

      const section = canvas.closest('.view-section');
      const widget = canvas.closest('.widget');
      const wrap = canvas.closest('.chart-wrap');

      if (section && !section.classList.contains('active')) {
        return;
      }

      if (widget && widget.classList.contains('is-hidden-by-filter')) {
        return;
      }

      if (wrap) {
        const rect = wrap.getBoundingClientRect();

        if (rect.width < 20 || rect.height < 20) {
          wrap.style.minHeight = '220px';
          wrap.style.height = '220px';

          setTimeout(() => {
            initPersonalChart();
          }, 180);

          return;
        }
      }

      if (chartLoading.personalChart) return;

      chartLoading.personalChart = true;

      destroyChart('personalChart');
      destroyChart(canvas);

      const ctx = canvas.getContext('2d');

      if (!ctx) {
        chartLoading.personalChart = false;
        return;
      }

      try {
        const url = new URL(window.DASHBOARD_ANALYTICS_ROUTES.personalHours, window.location.origin);

        const response = await fetch(url.toString(), {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        destroyChart('personalChart');
        destroyChart(canvas);

        const chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: result.labels || [],
            datasets: [
              {
                label: 'Office (h)',
                data: result.office || [],
                backgroundColor: chartColors.blue,
                borderRadius: 8
              },
              {
                label: 'Montage (h)',
                data: result.montage || [],
                backgroundColor: chartColors.green,
                borderRadius: 8
              }
            ]
          },
          options: chartOptions({ stacked: true })
        });

        registerChart('personalChart', canvas, chart);
        updatePersonalChartSubtitle(result.summary || {});

        setTimeout(() => {
          chart.resize();
          chart.update('none');
        }, 150);

      } catch (error) {
        console.error('Personal chart failed:', error);

        destroyChart('personalChart');
        destroyChart(canvas);

        const fallbackChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'],
            datasets: [
              {
                label: 'Keine Daten',
                data: [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: chartColors.blue,
                borderRadius: 8
              }
            ]
          },
          options: chartOptions({ legend: false })
        });

        registerChart('personalChart', canvas, fallbackChart);
      } finally {
        chartLoading.personalChart = false;
      }
    }

    function updatePersonalChartSubtitle(summary) {
      const widget = document.querySelector('[data-widget-key="personalChart"]');
      const subtitle = widget?.querySelector('.widget-subtitle');

      if (!subtitle) return;

      const total = Number(summary.total || 0).toFixed(1);
      const office = Number(summary.office || 0).toFixed(1);
      const montage = Number(summary.montage || 0).toFixed(1);

      subtitle.textContent = `${total}h diese Woche · Office ${office}h · Montage ${montage}h`;
    }





    async function initDynamicMiniChart(widget) {
      const canvas = widget?.querySelector('[data-dynamic-chart="mini"]');

      if (!canvas || !widget || !window.Chart) return;

      if (!canvas.id) {
        canvas.id = `miniChartCanvas_${widget.dataset.widgetId || Date.now()}`;
      }

      const key = widget.dataset.widgetId || canvas.id || `mini_${Date.now()}`;

      if (chartLoading.miniCharts.has(key)) return;

      chartLoading.miniCharts.add(key);

      destroyChart(key);
      destroyChart(canvas);

      const ctx = canvas.getContext('2d');

      if (!ctx) {
        chartLoading.miniCharts.delete(key);
        return;
      }

      try {
        const url = new URL(window.DASHBOARD_ANALYTICS_ROUTES.miniAnalytics, window.location.origin);

        const response = await fetch(url.toString(), {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        destroyChart(key);
        destroyChart(canvas);

        const chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: result.labels || [],
            datasets: [
              {
                label: 'Heute offen',
                data: result.values || [],
                borderColor: chartColors.blue,
                backgroundColor: 'rgba(116,178,212,.12)',
                fill: true,
                tension: .42,
                borderWidth: 3,
                pointRadius: 4
              }
            ]
          },
          options: chartOptions({ legend: false })
        });

        registerChart(key, canvas, chart);
        updateMiniChartSubtitle(widget, result.summary || {});
      } catch (error) {
        console.error('Mini chart failed:', error);

        destroyChart(key);
        destroyChart(canvas);

        const fallbackChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['Aufgaben', 'Termine', 'Tickets'],
            datasets: [
              {
                label: 'Keine Daten',
                data: [0, 0, 0],
                borderColor: chartColors.blue,
                backgroundColor: 'rgba(116,178,212,.12)',
                fill: true,
                tension: .42,
                borderWidth: 3
              }
            ]
          },
          options: chartOptions({ legend: false })
        });

        registerChart(key, canvas, fallbackChart);
      } finally {
        chartLoading.miniCharts.delete(key);
      }
    }
    function updateMiniChartSubtitle(widget, summary) {
      const subtitle = widget?.querySelector('.widget-subtitle');

      if (!subtitle) return;

      subtitle.textContent = `${summary.total || 0} offene Punkte heute`;
    }

    function chartOptions(options = {}) {
      return {
        responsive: true,
        maintainAspectRatio: false,
        resizeDelay: 120,
        animation: {
          duration: 180
        },
        scales: {
          x: {
            stacked: !!options.stacked,
            grid: {
              display: false
            },
            ticks: {
              font: {
                weight: '700'
              }
            }
          },
          y: {
            stacked: !!options.stacked,
            grid: {
              color: chartColors.grid
            },
            ticks: {
              font: {
                weight: '700'
              },
              callback: value => options.money ? `${Number(value / 1000).toFixed(0)}k` : value
            }
          }
        },
        plugins: {
          legend: {
            display: options.legend === false ? false : true,
            position: 'top',
            align: 'end',
            labels: {
              boxWidth: 12,
              font: {
                weight: '700'
              }
            }
          },
          tooltip: {
            backgroundColor: '#111827',
            padding: 12,
            titleFont: {
              weight: '800'
            },
            bodyFont: {
              weight: '700'
            }
          }
        }
      };
    }


    let chartResizeTimer = null;

    function resizeAllCharts() {
      clearTimeout(chartResizeTimer);

      chartResizeTimer = setTimeout(() => {
        Object.values(state.charts || {}).forEach(chart => {
          if (!chart || typeof chart.resize !== 'function') {
            return;
          }

          const canvas = chart.canvas;
          const wrap = canvas?.closest('.chart-wrap');

          if (wrap) {
            const rect = wrap.getBoundingClientRect();

            if (rect.width < 20 || rect.height < 20) {
              return;
            }
          }

          chart.resize();
          chart.update('none');
        });
      }, 120);
    }

    let dashboardChartResizeObserver = null;

    function initChartResizeObserver() {
      if (!window.ResizeObserver || dashboardChartResizeObserver) {
        return;
      }

      dashboardChartResizeObserver = new ResizeObserver(() => {
        resizeAllCharts();
      });

      document.querySelectorAll('.widget, .chart-wrap, .dept-chart-card').forEach(element => {
        dashboardChartResizeObserver.observe(element);
      });
    }
    function showToast(text, type = 'default', duration = 1800) {
      const toast = document.getElementById('toast');
      const toastText = document.getElementById('toastText');

      if (!toast || !toastText) return;

      toast.className = 'toast';

      if (type === 'warning') {
        toast.classList.add('toast-warning');
      }

      if (type === 'danger') {
        toast.classList.add('toast-danger');
      }

      toastText.textContent = text;
      toast.classList.add('show');

      if (window.lucide) {
        lucide.createIcons();
      }

      clearTimeout(showToast.timer);

      showToast.timer = setTimeout(() => {
        toast.classList.remove('show', 'toast-warning', 'toast-danger', 'toast-confirm');
        toast.className = 'toast';
        toastText.textContent = '';
      }, duration);
    }

    function showConfirmToast({
      title = 'Aktion bestätigen',
      message = '',
      confirmText = 'Bestätigen',
      cancelText = 'Abbrechen',
      type = 'danger',
      onConfirm = null
    }) {
      const toast = document.getElementById('toast');
      const toastText = document.getElementById('toastText');

      if (!toast || !toastText) return;

      clearTimeout(showToast.timer);

      toast.className = 'toast toast-confirm';

      if (type === 'danger') {
        toast.classList.add('toast-danger');
      }

      if (type === 'warning') {
        toast.classList.add('toast-warning');
      }

      toastText.innerHTML = `
                                <span class="toast-confirm-content">
                                    <span class="toast-confirm-title">${escapeHtml(title)}</span>
                                    <span class="toast-confirm-text">${escapeHtml(message)}</span>
                                    <span class="toast-confirm-actions">
                                        <button type="button" class="toast-confirm-btn" data-toast-cancel>
                                            ${escapeHtml(cancelText)}
                                        </button>
                                        <button type="button" class="toast-confirm-btn white" data-toast-confirm>
                                            ${escapeHtml(confirmText)}
                                        </button>
                                    </span>
                                </span>
                            `;

      toast.classList.add('show');

      toast.querySelector('[data-toast-cancel]')?.addEventListener('click', () => {
        toast.classList.remove('show', 'toast-confirm', 'toast-danger', 'toast-warning');
        toast.className = 'toast';
        toastText.textContent = '';
      }, { once: true });

      toast.querySelector('[data-toast-confirm]')?.addEventListener('click', async () => {
        toast.classList.remove('show', 'toast-confirm', 'toast-danger', 'toast-warning');
        toast.className = 'toast';
        toastText.textContent = '';

        if (typeof onConfirm === 'function') {
          await onConfirm();
        }
      }, { once: true });

      if (window.lucide) {
        lucide.createIcons();
      }
    }

    function debounce(callback, delay = 400) {
      let timer = null;

      return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => callback.apply(this, args), delay);
      };
    }

    function cssEscape(value) {
      if (window.CSS && typeof window.CSS.escape === 'function') {
        return window.CSS.escape(value);
      }

      return String(value).replace(/["\\]/g, '\\$&');
    }

    function escapeHtml(value) {
      return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    const dashboardNoteState = {
      status: 'open',
      categoriesLoaded: false
    };

    function initDashboardNotes() {
      const widget = document.getElementById('dashNotesWidget');
      if (!widget || widget.dataset.bound === '1') return;

      widget.dataset.bound = '1';

      document.querySelectorAll('#dashNotesTabs .dash-notes-tab').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('#dashNotesTabs .dash-notes-tab').forEach(item => {
            item.classList.remove('active');
          });

          btn.classList.add('active');
          dashboardNoteState.status = btn.dataset.status || 'open';

          fetchDashboardNotes();
        });
      });

      document.getElementById('dashNotesSearch')?.addEventListener('input', debounce(() => {
        fetchDashboardNotes();
      }, 350));

      document.getElementById('dashNoteOpenModalBtn')?.addEventListener('click', openDashboardNoteModal);
      document.getElementById('dashNoteCloseModalBtn')?.addEventListener('click', closeDashboardNoteModal);

      document.getElementById('dashNoteModalOverlay')?.addEventListener('click', event => {
        if (event.target.id === 'dashNoteModalOverlay') {
          closeDashboardNoteModal();
        }
      });

      document.getElementById('dashNoteForm')?.addEventListener('submit', event => {
        event.preventDefault();
        saveDashboardNote();
      });

      fetchDashboardNoteCategories();
      fetchDashboardNotes();
    }

    async function fetchDashboardNotes() {
      const grid = document.getElementById('dashNotesGrid');
      const routes = window.DASHBOARD_NOTE_ROUTES || {};

      if (!grid || !routes.list) return;

      const search = document.getElementById('dashNotesSearch')?.value.trim() || '';
      const endpoint = search && routes.search ? routes.search : routes.list;

      grid.innerHTML = '<div class="empty-state">Lade Notizen...</div>';

      try {
        const url = new URL(endpoint, window.location.origin);

        if (search) {
          url.searchParams.set('search', search);
        }

        url.searchParams.set('status', dashboardNoteState.status);

        const response = await fetch(url.toString(), {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (!response.ok) {
          throw result;
        }

        const notes = Array.isArray(result.notes) ? result.notes : [];

        if (!notes.length) {
          grid.innerHTML = `
                        <div class="empty-state">
                          Keine Notizen gefunden.
                        </div>
                      `;
          return;
        }

        grid.innerHTML = notes.map(note => renderDashboardNoteCard(note)).join('');

        lucide.createIcons();
      } catch (error) {
        console.error('Dashboard notes load/search failed:', error);
        grid.innerHTML = '<div class="empty-state">Notizen konnten nicht geladen werden.</div>';
      }
    }

    function renderDashboardNoteCard(note) {
      const color = note.color || '#fef9c3';
      const checked = Number(note.is_done || 0) === 1 ? 'checked' : '';
      const title = note.title || 'Ohne Titel';
      const body = note.note || '';
      const category = note.category_name || '';
      const date = note.deadline || note.created_at || '';

      return `
                    <div class="dash-note-card" style="background-color:${escapeHtml(color)};">
                      <div class="dash-note-card-header">
                        <input
                          type="checkbox"
                          class="dash-note-checkbox"
                          ${checked}
                          onchange="toggleDashboardNote(${Number(note.id)}, this.checked)"
                        >

                        <div class="dash-note-title" title="${escapeHtml(title)}">
                          ${escapeHtml(title)}
                        </div>
                      </div>

                      <div class="dash-note-body">${escapeHtml(body)}</div>

                      <div class="dash-note-footer">
                        ${category ? `<span class="dash-note-badge">${escapeHtml(category)}</span>` : '<span></span>'}
                        ${date ? `<span class="dash-note-date">${formatDashboardNoteDate(date)}</span>` : ''}
                      </div>
                    </div>
                  `;
    }

    async function toggleDashboardNote(id, isDone) {
      const routes = window.DASHBOARD_NOTE_ROUTES || {};

      if (!routes.done) {
        showToast('Notiz-Route fehlt');
        return;
      }

      try {
        const response = await fetch(`${routes.done}/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            is_done: isDone ? 1 : 0
          })
        });

        const result = await response.json();

        if (!response.ok) {
          throw result;
        }

        showToast(isDone ? 'Notiz archiviert' : 'Notiz wiederhergestellt');
        fetchDashboardNotes();
      } catch (error) {
        console.error('Dashboard note toggle failed:', error);
        showToast('Notiz konnte nicht aktualisiert werden');
        fetchDashboardNotes();
      }
    }

    async function fetchDashboardNoteCategories() {
      const routes = window.DASHBOARD_NOTE_ROUTES || {};
      const select = document.getElementById('dashNoteCategory');

      if (!select || !routes.categories) return;

      try {
        const response = await fetch(routes.categories, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const categories = await response.json();

        if (!response.ok) {
          throw categories;
        }

        select.innerHTML = '<option value="">Allgemein</option>';

        (Array.isArray(categories) ? categories : []).forEach(category => {
          const option = document.createElement('option');
          option.value = category.id;
          option.textContent = category.category_name || 'Kategorie';
          select.appendChild(option);
        });

        dashboardNoteState.categoriesLoaded = true;
      } catch (error) {
        console.error('Dashboard note categories failed:', error);
        select.innerHTML = '<option value="">Allgemein</option>';
      }
    }

    async function saveDashboardNote() {
      const routes = window.DASHBOARD_NOTE_ROUTES || {};

      if (!routes.store) {
        showToast('Speicher-Route fehlt');
        return;
      }

      const title = document.getElementById('dashNoteTitle')?.value.trim() || '';
      const note = document.getElementById('dashNoteContent')?.value.trim() || '';
      const categoryId = document.getElementById('dashNoteCategory')?.value || '';
      const color = document.getElementById('dashNoteColor')?.value || '#fef9c3';

      if (!title || !note) {
        showToast('Titel und Inhalt sind erforderlich');
        return;
      }

      try {
        const payload = {
          title: title,
          note: note,
          category_id: categoryId || null,
          color: color,
          priority: 'medium'
        };

        const response = await fetch(routes.store, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok) {
          throw result;
        }

        showToast('Notiz gespeichert');
        closeDashboardNoteModal();

        const form = document.getElementById('dashNoteForm');
        if (form) form.reset();

        document.getElementById('dashNoteColor').value = '#fef9c3';

        dashboardNoteState.status = 'open';

        document.querySelectorAll('#dashNotesTabs .dash-notes-tab').forEach(btn => {
          btn.classList.toggle('active', btn.dataset.status === 'open');
        });

        fetchDashboardNotes();
      } catch (error) {
        console.error('Dashboard note save failed:', error);

        const message = error?.message || error?.errors
          ? 'Bitte Eingaben prüfen'
          : 'Notiz konnte nicht gespeichert werden';

        showToast(message);
      }
    }

    function openDashboardNoteModal() {
      document.getElementById('dashNoteModalOverlay')?.classList.add('show');
      fetchDashboardNoteCategories();
      lucide.createIcons();

      setTimeout(() => {
        document.getElementById('dashNoteTitle')?.focus();
      }, 80);
    }

    function closeDashboardNoteModal() {
      document.getElementById('dashNoteModalOverlay')?.classList.remove('show');
    }

    function formatDashboardNoteDate(value) {
      if (!value) return '';

      const date = new Date(value);

      if (Number.isNaN(date.getTime())) {
        return String(value).slice(0, 10);
      }

      return date.toLocaleDateString('de-DE', {
        day: '2-digit',
        month: '2-digit'
      });
    }

    function initBreakingNewsBar() {
      const routes = window.DASHBOARD_BREAKING_ROUTES || {};

      const bar = document.getElementById('breakingNewsBar');
      const textEl = document.getElementById('breakingNewsText');
      const typeEl = document.getElementById('breakingNewsType');
      const timeEl = document.getElementById('breakingNewsTimeText');
      const prevBtn = document.getElementById('bnPrev');
      const nextBtn = document.getElementById('bnNext');
      const playBtn = document.getElementById('bnPlayPause');
      const playIconEl = document.getElementById('bnPlayPauseIcon');

      const creatorImgEl = document.getElementById('breakingNewsCreatorImage');
      const creatorNameEl = document.getElementById('breakingNewsCreatorName');
      const labelIconEl = document.getElementById('bnMainIcon');
      const labelEl = document.getElementById('bnLabel');

      const audioWrapper = document.getElementById('bnAudioWrapper');
      const audioSeek = document.getElementById('bnAudioSeek');
      const audioProgress = document.getElementById('bnAudioProgress');
      const audioHandle = document.getElementById('bnAudioHandle');
      const audioCurrentEl = document.getElementById('bnAudioCurrent');
      const audioDurationEl = document.getElementById('bnAudioDuration');

      if (!bar || !textEl || !typeEl || !timeEl) return;

      let items = [];
      let index = 0;
      let mode = 'notifications';
      let loopTimer = null;
      let tickerPaused = false;
      let hasAudio = false;

      const intervalMs = 14000;
      const audio = new Audio();

      function safeHtml(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function formatTime(sec) {
        if (!isFinite(sec) || sec < 0) return '0:00';

        const minutes = Math.floor(sec / 60);
        const seconds = Math.floor(sec % 60);

        return `${minutes}:${String(seconds).padStart(2, '0')}`;
      }

      function resetAudioUI() {
        if (audioProgress) audioProgress.style.width = '0%';
        if (audioHandle) audioHandle.style.left = '0%';
        if (audioSeek) audioSeek.value = 0;
        if (audioCurrentEl) audioCurrentEl.textContent = '0:00';
        if (audioDurationEl) audioDurationEl.textContent = '0:00';
      }

      function updateAudioUI() {
        if (!audio.duration) return;

        const percent = (audio.currentTime / audio.duration) * 100;

        if (audioProgress) audioProgress.style.width = `${percent}%`;
        if (audioHandle) audioHandle.style.left = `${percent}%`;
        if (audioSeek) audioSeek.value = percent;
        if (audioCurrentEl) audioCurrentEl.textContent = formatTime(audio.currentTime);
      }

      function syncPlayIcon() {
        if (!playIconEl) return;

        playIconEl.classList.remove('ri-play-mini-line', 'ri-pause-mini-line');

        if (mode === 'breaking' && hasAudio) {
          playIconEl.classList.add(audio.paused ? 'ri-play-mini-line' : 'ri-pause-mini-line');
          return;
        }

        playIconEl.classList.add(tickerPaused ? 'ri-play-mini-line' : 'ri-pause-mini-line');
      }

      audio.addEventListener('loadedmetadata', () => {
        if (audioDurationEl) {
          audioDurationEl.textContent = formatTime(audio.duration);
        }
      });

      audio.addEventListener('timeupdate', updateAudioUI);

      audio.addEventListener('ended', () => {
        tickerPaused = false;
        bar.classList.remove('is-paused');
        syncPlayIcon();
      });

      if (audioSeek) {
        audioSeek.addEventListener('input', event => {
          if (!audio.duration) return;

          const percent = parseFloat(event.target.value || '0');
          audio.currentTime = (percent / 100) * audio.duration;
          updateAudioUI();
        });
      }

      function updateLabelVisual(scope, type) {
        if (!labelIconEl || !labelEl) return;

        labelIconEl.className = 'sa-bn-label-icon';
        labelEl.className = 'sa-bn-label';

        if (scope === 'breaking') {
          let labelClass = 'sa-bn-label--breaking-info';
          let iconClass = 'ri-megaphone-fill';

          if (type === 'warning') {
            labelClass = 'sa-bn-label--breaking-warning';
            iconClass = 'ri-alert-line';
          }

          if (type === 'danger') {
            labelClass = 'sa-bn-label--breaking-danger';
            iconClass = 'ri-alarm-warning-fill';
          }

          if (type === 'success') {
            labelClass = 'sa-bn-label--breaking-success';
            iconClass = 'ri-checkbox-circle-fill';
          }

          labelEl.classList.add(labelClass);
          labelIconEl.classList.add(iconClass, 'sa-bn-label-blink');

          return;
        }

        labelIconEl.classList.add('ri-notification-3-fill');
      }

      function getScopeIcon(scope, type) {
        if (scope === 'breaking') {
          if (type === 'warning') return 'ri-alert-line';
          if (type === 'danger') return 'ri-alarm-warning-fill';
          if (type === 'success') return 'ri-checkbox-circle-fill';
          return 'ri-megaphone-fill';
        }

        if (scope === 'customer') return 'ri-user-3-line';
        if (scope === 'employee') return 'ri-user-settings-line';
        if (scope === 'project') return 'ri-layout-4-line';
        if (scope === 'ticket') return 'ri-ticket-line';
        if (scope === 'task') return 'ri-checkbox-circle-line';

        const icons = {
          inquiry: 'ri-question-answer-line',
          lead: 'ri-user-star-line',
          offer: 'ri-file-list-3-line',
          appointment: 'ri-calendar-event-line',
          demo: 'ri-notification-3-line'
        };

        return icons[type] || 'ri-notification-3-line';
      }

      function getTypeLabel(type) {
        const labels = {
          inquiry: 'Anfrage',
          lead: 'Lead',
          offer: 'Angebot',
          appointment: 'Termin',
          task: 'Aufgabe',
          project: 'Projekt',
          ticket: 'Ticket',
          employee: 'Mitarbeiter',
          info: 'Info',
          demo: 'System',
          warning: 'Warnung',
          danger: 'Alarm',
          success: 'Hinweis'
        };

        return labels[type] || 'Info';
      }

      function stopLoop() {
        if (loopTimer) {
          clearInterval(loopTimer);
          loopTimer = null;
        }
      }

      function startLoop() {
        stopLoop();

        loopTimer = setInterval(() => {
          if (!tickerPaused) {
            showNext();
          }
        }, intervalMs);
      }

      function showNext() {
        if (!items.length) return;

        index = (index + 1) % items.length;
        showCurrent();
      }

      function showPrev() {
        if (!items.length) return;

        index = (index - 1 + items.length) % items.length;
        showCurrent();
      }

      function tryAutoPlayAudio() {
        if (!hasAudio) return;

        audio.play()
          .then(() => {
            tickerPaused = true;
            bar.classList.add('is-paused');
            syncPlayIcon();
          })
          .catch(() => {
            tickerPaused = true;
            bar.classList.add('is-paused');
            syncPlayIcon();
          });
      }

      function showCurrent() {
        if (!items.length) return;

        const item = items[index] || {};

        const title = item.title || 'Benachrichtigung';
        const message = item.message || '';
        const type = String(item.type || 'info').toLowerCase();
        const scope = item.scope || (mode === 'breaking' ? 'breaking' : 'generic');

        const iconClass = getScopeIcon(scope, type);
        const typeLabel = getTypeLabel(type);
        const timeLabel = item.performed_at_human || item.performed_at || '';

        textEl.innerHTML = `
                    <span style="display:inline-flex;align-items:center;gap:8px;">
                      <i class="${safeHtml(iconClass)}" style="font-size:1.1rem;"></i>
                      <span style="font-weight:900;">${safeHtml(title)}</span>
                      <span style="opacity:0.55;">•</span>
                      <span style="opacity:0.95;">${safeHtml(message)}</span>
                    </span>
                  `;

        typeEl.textContent = typeLabel;
        timeEl.textContent = timeLabel;

        updateLabelVisual(scope, type);

        const creatorImage = item.creator_image_url || null;
        const creatorName = item.creator_name || '';

        if (creatorImgEl) {
          if (creatorImage && mode === 'breaking') {
            creatorImgEl.src = creatorImage;
            creatorImgEl.style.display = 'block';
          } else {
            creatorImgEl.removeAttribute('src');
            creatorImgEl.style.display = 'none';
          }
        }

        if (creatorNameEl) {
          if (creatorName && mode === 'breaking') {
            creatorNameEl.textContent = creatorName;
            creatorNameEl.classList.remove('hidden');
          } else {
            creatorNameEl.textContent = '';
            creatorNameEl.classList.add('hidden');
          }
        }

        const audioUrl = mode === 'breaking' ? (item.audio_url || null) : null;

        hasAudio = !!audioUrl;

        if (audioWrapper) {
          if (hasAudio) {
            audioWrapper.classList.remove('hidden');

            audio.pause();
            audio.src = audioUrl;
            audio.currentTime = 0;

            resetAudioUI();
            tryAutoPlayAudio();
          } else {
            audioWrapper.classList.add('hidden');

            audio.pause();
            audio.removeAttribute('src');

            resetAudioUI();

            tickerPaused = false;
            bar.classList.remove('is-paused');
          }
        }

        textEl.style.animation = 'none';
        void textEl.offsetWidth;
        textEl.style.animation = '';

        syncPlayIcon();
      }

      async function loadBreakingNews() {
        if (!routes.breaking) return false;

        try {
          const response = await fetch(routes.breaking, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const data = await response.json();

          const list = Array.isArray(data.breakingNews) ? data.breakingNews : [];

          if (!list.length) return false;

          mode = 'breaking';
          items = list;
          index = 0;

          bar.classList.remove('hidden');

          showCurrent();
          startLoop();

          return true;
        } catch (error) {
          console.error('Breaking news load failed:', error);
          return false;
        }
      }

      async function loadNotifications() {
        if (!routes.notifications) return false;

        try {
          const response = await fetch(routes.notifications, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const data = await response.json();

          const list = Array.isArray(data.notifications) ? data.notifications : [];

          if (!list.length) {
            bar.classList.add('hidden');
            return false;
          }

          mode = 'notifications';
          items = list;
          index = 0;

          tickerPaused = false;
          bar.classList.remove('hidden', 'is-paused');

          showCurrent();
          startLoop();

          return true;
        } catch (error) {
          console.error('Notifications load failed:', error);
          bar.classList.add('hidden');
          return false;
        }
      }

      if (prevBtn) {
        prevBtn.addEventListener('click', () => {
          audio.pause();

          tickerPaused = false;
          bar.classList.remove('is-paused');

          showPrev();
          syncPlayIcon();
        });
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', () => {
          audio.pause();

          tickerPaused = false;
          bar.classList.remove('is-paused');

          showNext();
          syncPlayIcon();
        });
      }

      if (playBtn) {
        playBtn.addEventListener('click', () => {
          if (mode === 'breaking' && hasAudio) {
            if (audio.paused) {
              audio.play()
                .then(() => {
                  tickerPaused = true;
                  bar.classList.add('is-paused');
                  syncPlayIcon();
                })
                .catch(() => {
                  tickerPaused = true;
                  bar.classList.add('is-paused');
                  syncPlayIcon();
                });
            } else {
              audio.pause();

              tickerPaused = false;
              bar.classList.remove('is-paused');

              syncPlayIcon();
            }

            return;
          }

          tickerPaused = !tickerPaused;
          bar.classList.toggle('is-paused', tickerPaused);

          syncPlayIcon();
        });
      }

      loadBreakingNews().then(hasBreaking => {
        if (!hasBreaking) {
          loadNotifications();
        }
      });
    }
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const shortcutGrid = document.getElementById('shortcutGrid');
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      async function requestJson(url, options = {}) {
        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            ...(options.headers || {})
          },
          ...options
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
          throw new Error(data.message || 'Serverfehler');
        }

        return data;
      }

      function escapeHtml(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function renderShortcuts(shortcuts) {
        if (!shortcutGrid) return;

        if (!Array.isArray(shortcuts) || !shortcuts.length) {
          shortcutGrid.innerHTML = `
                                  <div class="empty-state">
                                      Keine Schnellzugriffe vorhanden.
                                  </div>
                              `;
          return;
        }

        shortcutGrid.innerHTML = shortcuts.map(item => {
          const url = item.url || '#';

          return `
                                  <button class="shortcut-card"
                                          type="button"
                                          data-url="${escapeHtml(url)}"
                                          data-shortcut-id="${escapeHtml(item.id)}">
                                      <span class="shortcut-card-top">
                                          <i data-lucide="${escapeHtml(item.icon || 'zap')}"></i>
                                          <strong>${escapeHtml(item.label)}</strong>
                                      </span>
                                      <span>${escapeHtml(item.subtitle || '')}</span>
                                  </button>
                              `;
        }).join('');

        shortcutGrid.querySelectorAll('.shortcut-card').forEach(button => {
          button.addEventListener('click', function () {
            const url = this.dataset.url;

            if (url && url !== '#') {
              window.location.href = url;
            }
          });
        });

        if (window.lucide) {
          lucide.createIcons();
        }
      }

      async function loadShortcuts() {
        try {
          const data = await requestJson('{{ route('dashboard.shortcuts.index') }}');
          renderShortcuts(data.shortcuts || []);
        } catch (error) {
          shortcutGrid.innerHTML = `
                                  <div class="empty-state">
                                      Schnellzugriffe konnten nicht geladen werden.
                                  </div>
                              `;
          console.error(error);
        }
      }

      loadShortcuts();

      window.reloadDashboardShortcuts = loadShortcuts;
    });
  </script>

  <script>
    function shortcutRoutes() {
      return window.DASHBOARD_SHORTCUT_ROUTES || {};
    }

    async function shortcutRequestJson(url, options = {}) {
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken(),
          'X-Requested-With': 'XMLHttpRequest',
          ...(options.headers || {})
        },
        ...options
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok || data.success === false) {
        throw data;
      }

      return data;
    }

    function renderDashboardShortcuts(shortcuts) {
      const grid = document.getElementById('shortcutGrid');

      if (!grid) {
        return;
      }

      if (!Array.isArray(shortcuts) || !shortcuts.length) {
        grid.innerHTML = `
                              <div class="empty-state">
                                  Keine Schnellzugriffe vorhanden.
                              </div>
                          `;
        return;
      }

      grid.innerHTML = shortcuts.map(item => {
        const url = item.url || '#';

        return `
                              <button class="shortcut-card"
                                      type="button"
                                      data-url="${escapeHtml(url)}"
                                      data-shortcut-id="${escapeHtml(item.id)}">
                                  <span class="shortcut-card-top">
                                      <i data-lucide="${escapeHtml(item.icon || 'zap')}"></i>
                                      <strong>${escapeHtml(item.label || '')}</strong>
                                  </span>
                                  <span>${escapeHtml(item.subtitle || '')}</span>
                              </button>
                          `;
      }).join('');

      grid.querySelectorAll('.shortcut-card').forEach(button => {
        button.addEventListener('click', function () {
          const url = this.dataset.url;

          if (url && url !== '#') {
            window.location.href = url;
          }
        });
      });

      if (window.lucide) {
        lucide.createIcons();
      }
    }

    async function loadDashboardShortcuts() {
      const routes = shortcutRoutes();
      const grid = document.getElementById('shortcutGrid');

      if (!grid) {
        return;
      }

      if (!routes.index) {
        grid.innerHTML = `
                              <div class="empty-state">
                                  Schnellzugriff-Route fehlt.
                              </div>
                          `;
        return;
      }

      grid.innerHTML = `
                          <div class="empty-state">
                              Schnellzugriffe werden geladen...
                          </div>
                      `;

      try {
        const result = await shortcutRequestJson(routes.index);
        renderDashboardShortcuts(result.shortcuts || []);
      } catch (error) {
        console.error('Shortcuts load failed:', error);

        grid.innerHTML = `
                              <div class="empty-state">
                                  Schnellzugriffe konnten nicht geladen werden.
                              </div>
                          `;
      }
    }

    let shortcutCache = [];

    function getShortcutRoutes() {
      return window.DASHBOARD_SHORTCUT_ROUTES || {};
    }

    function openShortcutManager() {
      closePanels();

      const panel = document.getElementById('shortcutManagerPanel');
      const overlay = document.getElementById('overlay');

      if (!panel || !overlay) {
        return;
      }

      panel.classList.add('open');
      overlay.classList.add('show');
      panel.setAttribute('aria-hidden', 'false');

      loadShortcutManager();
    }

    async function loadShortcutManager() {
      await Promise.all([
        loadDashboardShortcuts(),
        loadAvailableShortcuts()
      ]);

      renderShortcutManageList(shortcutCache);
    }

    async function loadDashboardShortcuts() {
      const routes = getShortcutRoutes();
      const grid = document.getElementById('shortcutGrid');

      if (!routes.index) {
        if (grid) {
          grid.innerHTML = `<div class="empty-state">Shortcut-Route fehlt.</div>`;
        }

        return;
      }

      try {
        const result = await shortcutRequestJson(routes.index);
        shortcutCache = result.shortcuts || [];
        renderDashboardShortcuts(shortcutCache);
        renderShortcutManageList(shortcutCache);
      } catch (error) {
        console.error('Shortcuts load failed:', error);

        if (grid) {
          grid.innerHTML = `<div class="empty-state">Schnellzugriffe konnten nicht geladen werden.</div>`;
        }
      }
    }

    async function loadAvailableShortcuts() {
      const routes = getShortcutRoutes();
      const target = document.getElementById('shortcutAvailableList');

      if (!target || !routes.available) {
        return;
      }

      target.innerHTML = `<div class="empty-state">Aktionen werden geladen...</div>`;

      try {
        const result = await shortcutRequestJson(routes.available);
        renderAvailableShortcuts(result.items || []);
      } catch (error) {
        console.error('Available shortcuts failed:', error);
        target.innerHTML = `<div class="empty-state">Verfügbare Aktionen konnten nicht geladen werden.</div>`;
      }
    }

    function renderAvailableShortcuts(items) {
      const target = document.getElementById('shortcutAvailableList');

      if (!target) {
        return;
      }

      if (!items.length) {
        target.innerHTML = `<div class="empty-state">Keine verfügbaren Aktionen.</div>`;
        return;
      }

      const existingUrls = new Set(shortcutCache.map(item => item.url));

      target.innerHTML = `
                    <div class="shortcut-available-list">
                        ${items.map(item => {
        const alreadyAdded = existingUrls.has(item.url);

        return `
                                <div class="shortcut-available-row">
                                    <span class="shortcut-manage-icon">
                                        <i data-lucide="${escapeHtml(item.icon || 'zap')}"></i>
                                    </span>

                                    <span class="shortcut-manage-title">
                                        <strong>${escapeHtml(item.label || '')}</strong>
                                        <span>${escapeHtml(item.subtitle || '')}</span>
                                    </span>

                                    <button class="shortcut-icon-btn"
                                            type="button"
                                            data-add-shortcut='${escapeHtml(JSON.stringify(item))}'
                                            ${alreadyAdded ? 'disabled style="opacity:.45;cursor:not-allowed;"' : ''}>
                                        <i data-lucide="${alreadyAdded ? 'check' : 'plus'}"></i>
                                    </button>
                                </div>
                            `;
      }).join('')}
                    </div>
                `;

      target.querySelectorAll('[data-add-shortcut]').forEach(button => {
        button.addEventListener('click', async function () {
          if (this.disabled) return;

          const item = JSON.parse(this.dataset.addShortcut || '{}');
          await addShortcut(item);
        });
      });

      if (window.lucide) {
        lucide.createIcons();
      }
    }

    function renderShortcutManageList(items) {
      const target = document.getElementById('shortcutManageList');

      if (!target) {
        return;
      }

      if (!items.length) {
        target.innerHTML = `<div class="empty-state">Noch keine Schnellzugriffe ausgewählt.</div>`;
        return;
      }

      target.innerHTML = `
                    <div class="shortcut-manage-list" id="shortcutSortableList">
                        ${items.map((item, index) => `
                            <div class="shortcut-manage-row"
                                 draggable="true"
                                 data-shortcut-row
                                 data-id="${escapeHtml(item.id)}"
                                 data-index="${index}">
                                <span class="shortcut-manage-icon">
                                    <i data-lucide="${escapeHtml(item.icon || 'zap')}"></i>
                                </span>

                                <span class="shortcut-manage-title">
                                    <strong>${escapeHtml(item.label || '')}</strong>
                                    <span>${escapeHtml(item.subtitle || '')}</span>
                                </span>

                                <span class="shortcut-row-actions">
                                    <button class="shortcut-icon-btn" type="button" data-edit-shortcut title="Bearbeiten">
                                        <i data-lucide="pencil"></i>
                                    </button>

                                    <button class="shortcut-icon-btn danger" type="button" data-delete-shortcut title="Löschen">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </span>

                                <div class="shortcut-edit-form">
                                    <input type="text" data-field="label" value="${escapeHtml(item.label || '')}" placeholder="Titel">
                                    <input type="text" data-field="subtitle" value="${escapeHtml(item.subtitle || '')}" placeholder="Untertitel">
                                    <input type="text" data-field="icon" value="${escapeHtml(item.icon || 'zap')}" placeholder="Lucide Icon">
                                    <input type="text" data-field="url" value="${escapeHtml(item.url || '')}" placeholder="URL">

                                    <select data-field="permissionAction">
                                        <option value="is_read" ${item.permissionAction === 'is_read' ? 'selected' : ''}>Lesen</option>
                                        <option value="is_add" ${item.permissionAction === 'is_add' ? 'selected' : ''}>Hinzufügen</option>
                                        <option value="is_update" ${item.permissionAction === 'is_update' ? 'selected' : ''}>Bearbeiten</option>
                                        <option value="is_delete" ${item.permissionAction === 'is_delete' ? 'selected' : ''}>Löschen</option>
                                    </select>

                                    <div class="shortcut-edit-actions">
                                        <button class="btn btn-primary" type="button" data-save-shortcut>
                                            <i data-lucide="save"></i>
                                            Speichern
                                        </button>

                                        <button class="btn" type="button" data-cancel-shortcut>
                                            Abbrechen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;

      bindShortcutManageEvents();

      if (window.lucide) {
        lucide.createIcons();
      }
    }

    function bindShortcutManageEvents() {
      const list = document.getElementById('shortcutSortableList');

      if (!list) {
        return;
      }

      list.querySelectorAll('[data-edit-shortcut]').forEach(button => {
        button.addEventListener('click', function () {
          const row = this.closest('[data-shortcut-row]');
          row?.classList.toggle('is-editing');

          if (window.lucide) {
            lucide.createIcons();
          }
        });
      });

      list.querySelectorAll('[data-cancel-shortcut]').forEach(button => {
        button.addEventListener('click', function () {
          this.closest('[data-shortcut-row]')?.classList.remove('is-editing');
        });
      });

      list.querySelectorAll('[data-save-shortcut]').forEach(button => {
        button.addEventListener('click', async function () {
          const row = this.closest('[data-shortcut-row]');
          const id = row?.dataset.id;

          if (!id) {
            return;
          }

          await updateShortcutFromRow(row, id);
        });
      });

      list.querySelectorAll('[data-delete-shortcut]').forEach(button => {
        button.addEventListener('click', function () {
          const row = this.closest('[data-shortcut-row]');
          const id = row?.dataset.id;
          const rows = [...list.querySelectorAll('[data-shortcut-row]')];

          if (!id) {
            return;
          }

          if (rows.length <= 1) {
            showToast(
              'Mindestens ein Schnellzugriff muss sichtbar bleiben.',
              'warning',
              2600
            );
            return;
          }

          const title = row.querySelector('.shortcut-manage-title strong')?.textContent?.trim() || 'Schnellzugriff';

          showConfirmToast({
            title: 'Schnellzugriff löschen?',
            message: `"${title}" wird aus deinem Dashboard entfernt.`,
            confirmText: 'Ja, löschen',
            cancelText: 'Abbrechen',
            type: 'danger',
            onConfirm: async () => {
              await deleteShortcut(id);
            }
          });
        });
      });

      enableShortcutDragSort(list);
    }

    async function addShortcut(item) {
      const routes = getShortcutRoutes();

      try {
        await shortcutRequestJson(routes.store, {
          method: 'POST',
          body: JSON.stringify({
            label: item.label || '',
            subtitle: item.subtitle || '',
            icon: item.icon || 'zap',
            url: item.url || '#',
            permission_key: item.permission_key || null,
            permission_action: item.permission_action || 'is_read'
          })
        });

        showToast('Schnellzugriff hinzugefügt.');
        await loadShortcutManager();
      } catch (error) {
        console.error('Shortcut add failed:', error);
        showToast(error.message || 'Schnellzugriff konnte nicht hinzugefügt werden.');
      }
    }

    async function updateShortcutFromRow(row, id) {
      const routes = getShortcutRoutes();

      const getField = key => row.querySelector(`[data-field="${key}"]`)?.value || '';

      const current = shortcutCache.find(item => String(item.id) === String(id)) || {};

      try {
        await shortcutRequestJson(`${routes.updateBase}/${id}`, {
          method: 'PUT',
          body: JSON.stringify({
            label: getField('label'),
            subtitle: getField('subtitle'),
            icon: getField('icon') || 'zap',
            url: getField('url'),
            permission_key: current.permissionKey || null,
            permission_action: getField('permissionAction') || 'is_read',
            is_visible: true
          })
        });

        showToast('Schnellzugriff aktualisiert.');
        await loadDashboardShortcuts();
      } catch (error) {
        console.error('Shortcut update failed:', error);
        showToast(error.message || 'Schnellzugriff konnte nicht gespeichert werden.');
      }
    }

    async function deleteShortcut(id) {
      const routes = getShortcutRoutes();

      try {
        await shortcutRequestJson(`${routes.deleteBase}/${id}`, {
          method: 'DELETE'
        });

        showToast('Schnellzugriff gelöscht.');
        await loadShortcutManager();
        await loadDashboardShortcuts();
      } catch (error) {
        console.error('Shortcut delete failed:', error);
        showToast(error.message || 'Schnellzugriff konnte nicht gelöscht werden.', 'danger', 2600);
      }
    }

    function enableShortcutDragSort(list) {
      let draggedRow = null;

      list.querySelectorAll('[data-shortcut-row]').forEach(row => {
        row.addEventListener('dragstart', function () {
          draggedRow = this;
          this.classList.add('dragging');
        });

        row.addEventListener('dragend', async function () {
          this.classList.remove('dragging');
          draggedRow = null;
          await saveShortcutOrder();
        });

        row.addEventListener('dragover', function (event) {
          event.preventDefault();

          const afterElement = getShortcutDragAfterElement(list, event.clientY);

          if (!draggedRow) {
            return;
          }

          if (afterElement == null) {
            list.appendChild(draggedRow);
          } else {
            list.insertBefore(draggedRow, afterElement);
          }
        });
      });
    }

    function getShortcutDragAfterElement(container, y) {
      const draggableElements = [...container.querySelectorAll('[data-shortcut-row]:not(.dragging)')];

      return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
          return {
            offset,
            element: child
          };
        }

        return closest;
      }, {
        offset: Number.NEGATIVE_INFINITY
      }).element;
    }

    async function saveShortcutOrder() {
      const routes = getShortcutRoutes();
      const rows = [...document.querySelectorAll('#shortcutSortableList [data-shortcut-row]')];

      const items = rows.map((row, index) => ({
        id: Number(row.dataset.id),
        sort_order: index + 1
      }));

      try {
        await shortcutRequestJson(routes.reorder, {
          method: 'POST',
          body: JSON.stringify({ items })
        });

        showToast('Reihenfolge gespeichert.');
        await loadDashboardShortcuts();
      } catch (error) {
        console.error('Shortcut reorder failed:', error);
        showToast(error.message || 'Reihenfolge konnte nicht gespeichert werden.');
      }
    }

  </script>

  <script>
    (function () {
      const absenceRoutes = window.DASHBOARD_ABSENCE_ROUTES || {};

      function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      }

      function toast(message) {
        if (typeof showToast === 'function') {
          showToast(message);
          return;
        }

        alert(message);
      }

      function getModal() {
        return document.getElementById('absenceRequestModalOverlay');
      }

      function getForm() {
        return document.getElementById('absenceRequestForm');
      }

      function openAbsenceModal() {
        const modal = getModal();

        if (!modal) {
          console.error('Absence modal not found: #absenceRequestModalOverlay');
          toast('Abwesenheits-Modal wurde nicht gefunden.');
          return;
        }

        modal.classList.add('show');

        setRequestType(document.querySelector('input[name="request_type"]:checked')?.value || 'leave');
        loadAbsenceData();

        if (window.lucide) {
          lucide.createIcons();
        }
      }

      function closeAbsenceModal() {
        getModal()?.classList.remove('show');
      }

      function setRequestType(type) {
        document.querySelectorAll('[data-absence-type-option]').forEach(label => {
          label.classList.toggle('active', label.dataset.absenceTypeOption === type);
        });

        const leaveTypeGroup = document.getElementById('absenceLeaveTypeGroup');
        const documentGroup = document.getElementById('absenceDocumentGroup');

        if (leaveTypeGroup) {
          leaveTypeGroup.style.display = type === 'leave' ? 'block' : 'none';
        }

        if (documentGroup) {
          documentGroup.style.display = type === 'sick' ? 'block' : 'none';
        }
      }

      async function loadAbsenceData() {
        if (!absenceRoutes.data) {
          toast('Abwesenheits-Route fehlt.');
          return;
        }

        try {
          const response = await fetch(absenceRoutes.data, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const result = await response.json();

          if (!response.ok || !result.success) {
            throw result;
          }

          document.querySelectorAll('.absenceEmployeeImage').forEach(img => {
            img.src = result.employee?.image_url || '/images/gender/male.png';
          });

          document.querySelectorAll('.absenceEmployeeName').forEach(el => {
            el.textContent = result.employee?.name || 'Mein Antrag';
          });

          const requestToSelect = document.getElementById('absenceRequestTo');

          if (requestToSelect) {
            const currentValue = requestToSelect.value;

            requestToSelect.innerHTML = '<option value="">Bitte auswählen</option>';

            (result.approvers || []).forEach(approver => {
              const option = document.createElement('option');
              option.value = approver.id;
              option.textContent = approver.name || ('Mitarbeiter #' + approver.id);
              requestToSelect.appendChild(option);
            });

            if (currentValue) {
              requestToSelect.value = currentValue;
            }
          }
        } catch (error) {
          console.error('Absence data load failed:', error);
          toast('Mitarbeiterdaten konnten nicht geladen werden.');
        }
      }

      async function submitAbsenceRequest(event) {
        event.preventDefault();

        const form = getForm();
        const submitBtn = document.getElementById('submitAbsenceRequestBtn');

        if (!form) {
          toast('Formular wurde nicht gefunden.');
          return;
        }

        if (!absenceRoutes.store) {
          toast('Speicher-Route fehlt.');
          return;
        }

        const formData = new FormData(form);

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i data-lucide="loader-2"></i> Wird gesendet...';
        }

        if (window.lucide) {
          lucide.createIcons();
        }

        try {
          const response = await fetch(absenceRoutes.store, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken(),
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          });

          const result = await response.json();

          if (!response.ok || !result.success) {
            throw result;
          }

          toast(result.message || 'Antrag wurde gesendet.');

          form.reset();
          setRequestType('leave');
          closeAbsenceModal();

          if (typeof fetchHrWidget === 'function') {
            fetchHrWidget();
          }
        } catch (error) {
          console.error('Absence request failed:', error);

          let message = error?.message || 'Antrag konnte nicht gesendet werden.';

          if (error?.errors) {
            const firstKey = Object.keys(error.errors)[0];

            if (firstKey && error.errors[firstKey]?.[0]) {
              message = error.errors[firstKey][0];
            }
          }

          toast(message);
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i data-lucide="send"></i> Antrag senden';
          }

          if (window.lucide) {
            lucide.createIcons();
          }
        }
      }

      document.addEventListener('click', function (event) {
        const openBtn = event.target.closest('.openAbsenceRequestModalBtn');

        if (openBtn) {
          event.preventDefault();
          openAbsenceModal();
          return;
        }

        if (
          event.target.closest('#closeAbsenceRequestModalBtn') ||
          event.target.closest('#cancelAbsenceRequestBtn')
        ) {
          event.preventDefault();
          closeAbsenceModal();
          return;
        }

        if (event.target && event.target.id === 'absenceRequestModalOverlay') {
          closeAbsenceModal();
        }
      });

      document.addEventListener('change', function (event) {
        if (event.target.matches('input[name="request_type"]')) {
          setRequestType(event.target.value);
        }
      });

      document.addEventListener('submit', function (event) {
        if (event.target && event.target.id === 'absenceRequestForm') {
          submitAbsenceRequest(event);
        }
      });

      document.addEventListener('DOMContentLoaded', function () {
        setRequestType('leave');
        loadAbsenceData();

        if (window.lucide) {
          lucide.createIcons();
        }
      });

      window.loadAbsenceData = loadAbsenceData;
    })();
  </script>
  <script>
    (function () {
      'use strict';

      window.departmentDashboardState = {
        loaded: false,
        loading: false,
        bound: false,
        currentDepartmentId: null,
        recentType: 'leads',
        data: null,
        requestId: 0,
        charts: {}
      };

      const departmentDashboardState = window.departmentDashboardState;

      function deptEl(id) {
        return document.getElementById(id);
      }

      function deptEscape(value) {
        if (typeof escapeHtml === 'function') {
          return escapeHtml(value);
        }

        return String(value ?? '')
          .replaceAll('&', '&amp;')
          .replaceAll('<', '&lt;')
          .replaceAll('>', '&gt;')
          .replaceAll('"', '&quot;')
          .replaceAll("'", '&#039;');
      }

      function setDeptText(id, value) {
        const el = deptEl(id);

        if (el) {
          el.textContent = value ?? '–';
        }
      }

      function deptSafeArray(value) {
        return Array.isArray(value) ? value : [];
      }

      function deptSafeNumber(value) {
        if (typeof value === 'number') {
          return Number.isFinite(value) ? value : 0;
        }

        const raw = String(value ?? '0').trim();
        const normalized = raw.includes(',')
          ? raw.replace(/\./g, '').replace(',', '.')
          : raw;
        const number = Number(normalized.replace(/[^\d.-]/g, ''));

        return Number.isFinite(number) ? number : 0;
      }

      function formatDeptMoney(value) {
        return new Intl.NumberFormat('de-DE', {
          style: 'currency',
          currency: 'EUR',
          maximumFractionDigits: 0
        }).format(deptSafeNumber(value));
      }

      function formatDeptNumber(value) {
        return new Intl.NumberFormat('de-DE').format(deptSafeNumber(value));
      }

      function deptToast(message) {
        if (typeof showToast === 'function') {
          showToast(message);
          return;
        }

        console.warn(message);
      }

      function deptIconRefresh() {
        if (window.lucide) {
          lucide.createIcons();
        }
      }

      function deptEmptyHtml(icon, text) {
        return `
          <div class="empty-state">
            <div>
              ${icon ? `<i data-lucide="${deptEscape(icon)}"></i><br>` : ''}
              ${deptEscape(text)}
            </div>
          </div>
        `;
      }

      async function loadDepartmentOptions() {
        const routes = window.DASHBOARD_DEPARTMENT_ROUTES || {};
        const select = deptEl('departmentSelect');

        if (!select || !routes.departments) {
          return;
        }

        const selectedBefore = String(
          departmentDashboardState.currentDepartmentId ||
          select.value ||
          ''
        );

        select.innerHTML = '<option value="">Abteilungen werden geladen...</option>';

        try {
          const response = await fetch(routes.departments, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const result = await response.json();

          if (!response.ok || !result.success) {
            throw result;
          }

          const departments = deptSafeArray(result.departments);

          if (!departments.length) {
            select.innerHTML = '<option value="">Keine Abteilung gefunden</option>';
            departmentDashboardState.currentDepartmentId = null;
            return;
          }

          select.innerHTML = '';

          departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = String(dept.id);

            const mainBadge = dept.is_main ? ' ⭐ Hauptbereich' : '';

            option.textContent = dept.branch
              ? `${dept.name} · ${dept.branch}${mainBadge}`
              : `${dept.name}${mainBadge}`;

            select.appendChild(option);
          });

          const mainDepartment = departments.find(dept => Number(dept.is_main) === 1 || dept.is_main === true);
          const fallbackDepartmentId = String(
            result.default_department_id ||
            mainDepartment?.id ||
            departments[0]?.id ||
            ''
          );
          const existsSelectedBefore = departments.some(dept => String(dept.id) === selectedBefore);
          const nextDepartmentId = existsSelectedBefore ? selectedBefore : fallbackDepartmentId;

          departmentDashboardState.currentDepartmentId = nextDepartmentId || null;

          if (nextDepartmentId) {
            select.value = nextDepartmentId;
          }
        } catch (error) {
          console.error('Department options failed:', error);
          select.innerHTML = '<option value="">Keine Abteilung gefunden</option>';
          deptToast('Abteilungen konnten nicht geladen werden');
        }
      }

      function clearDepartmentDashboardUi() {
        const recentList = deptEl('deptRecentList');
        const teamList = deptEl('deptTeamList');
        const changesList = deptEl('deptChangeList');
        const historyList = deptEl('deptHistoryList');

        if (recentList) {
          recentList.innerHTML = deptEmptyHtml('loader-2', 'Daten werden geladen...');
        }

        if (teamList) {
          teamList.innerHTML = deptEmptyHtml(null, 'Team wird geladen...');
        }

        if (changesList) {
          changesList.innerHTML = deptEmptyHtml(null, 'Änderungen werden geladen...');
        }

        if (historyList) {
          historyList.innerHTML = deptEmptyHtml('loader-2', 'Historie wird geladen...');
        }

        deptIconRefresh();
      }

      async function loadDepartmentOverview(options = {}) {
        const routes = window.DASHBOARD_DEPARTMENT_ROUTES || {};
        const select = deptEl('departmentSelect');
        const info = deptEl('deptInfoNote');

        if (!routes.overview) {
          deptToast('Department Overview Route fehlt');
          return;
        }

        const departmentId = String(
          options.departmentId ||
          select?.value ||
          departmentDashboardState.currentDepartmentId ||
          ''
        );

        const from = deptEl('departmentFromDate')?.value || '';
        const to = deptEl('departmentToDate')?.value || '';

        if (!departmentId) {
          if (info) {
            info.innerHTML = `
              <i data-lucide="alert-circle" style="width:16px;color:var(--color-danger);"></i>
              Keine Abteilung ausgewählt.
            `;
          }

          deptIconRefresh();
          return;
        }

        departmentDashboardState.requestId = Number(departmentDashboardState.requestId || 0) + 1;
        const requestId = departmentDashboardState.requestId;

        departmentDashboardState.loading = true;
        departmentDashboardState.loaded = false;
        departmentDashboardState.currentDepartmentId = departmentId;
        departmentDashboardState.data = null;

        if (select && String(select.value) !== departmentId) {
          select.value = departmentId;
        }

        clearDepartmentDashboardUi();

        if (info) {
          info.innerHTML = `
            <i data-lucide="loader-2" style="width:16px;color:var(--color-blue-dark);"></i>
            Abteilung #${deptEscape(departmentId)} wird geladen...
          `;
        }

        deptIconRefresh();

        try {
          const url = new URL(routes.overview, window.location.origin);

          url.searchParams.set('department_id', departmentId);

          if (from) {
            url.searchParams.set('from', from);
          }

          if (to) {
            url.searchParams.set('to', to);
          }

          const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const result = await response.json();

          if (requestId !== departmentDashboardState.requestId) {
            return;
          }

          if (!response.ok || !result.success) {
            throw result;
          }

          departmentDashboardState.data = result;
          departmentDashboardState.currentDepartmentId = departmentId;
          departmentDashboardState.loaded = true;
          departmentDashboardState.loading = false;

          renderDepartmentOverview(result);
          renderDepartmentTeam(result.team || []);
          renderDepartmentRecent();
          renderDepartmentChanges(result.recent?.changes || []);
          renderDepartmentHistory(result.recent?.changes || []);
          renderDepartmentCharts(result.charts || {});

          if (info) {
            const departmentName = result.department?.name || `Abteilung #${departmentId}`;
            const branchName = result.department?.branch ? ` · ${result.department.branch}` : '';

            info.innerHTML = `
              <i data-lucide="check-circle" style="width:16px;color:var(--color-green-dark);"></i>
              ${deptEscape(departmentName)}${deptEscape(branchName)} · ${deptEscape(result.period?.from || '–')} bis ${deptEscape(result.period?.to || '–')}
            `;
          }

          deptIconRefresh();

          setTimeout(() => {
            if (typeof resizeAllCharts === 'function') {
              resizeAllCharts();
            }

            if (typeof resizeChartsForActiveView === 'function') {
              resizeChartsForActiveView();
            }
          }, 120);
        } catch (error) {
          console.error('Department overview failed:', error);

          if (requestId === departmentDashboardState.requestId) {
            departmentDashboardState.loading = false;
            departmentDashboardState.loaded = false;
          }

          if (info) {
            info.innerHTML = `
              <i data-lucide="alert-circle" style="width:16px;color:var(--color-danger);"></i>
              Abteilungsdaten konnten nicht geladen werden.
            `;
          }

          deptToast(error?.message || 'Abteilungsdaten konnten nicht geladen werden');
          deptIconRefresh();
        } finally {
          if (requestId === departmentDashboardState.requestId) {
            departmentDashboardState.loading = false;
          }
        }
      }

      function renderDepartmentOverview(result) {
        const department = result.department || {};
        const period = result.period || {};
        const kpis = result.kpis || result.summary || {};
        const team = deptSafeArray(result.team);

        setDeptText('deptTitle', department.name || 'Abteilungsübersicht');

        const subtitle = [
          department.branch,
          department.parent,
          period.from && period.to ? `${period.from} bis ${period.to}` : null
        ].filter(Boolean).join(' · ');

        setDeptText('deptSubtitle', subtitle || 'Team, Aufgaben, Termine, Leads, Angebote, Aufträge und Rechnungen');

        setDeptText('deptTeamMembers', formatDeptNumber(kpis.team_members ?? kpis.teamMembers ?? team.length));
        setDeptText('deptLeads', formatDeptNumber(kpis.leads ?? kpis.new_leads ?? 0));
        setDeptText('deptOffers', formatDeptNumber(kpis.offers ?? 0));
        setDeptText('deptDeals', formatDeptNumber(kpis.deals ?? 0));
        setDeptText('deptTickets', formatDeptNumber(kpis.tickets ?? 0));
        setDeptText('deptAppointments', formatDeptNumber(kpis.appointments ?? 0));
        setDeptText('deptTasks', formatDeptNumber(kpis.tasks ?? 0));
        setDeptText('deptInvoiceTotal', formatDeptMoney(kpis.invoice_total ?? kpis.invoiceTotal ?? kpis.revenue ?? 0));
      }

      function renderDepartmentTeam(team) {
        const list = deptEl('deptTeamList');

        if (!list) {
          return;
        }

        team = deptSafeArray(team);

        if (!team.length) {
          list.innerHTML = deptEmptyHtml('users', 'Keine Mitarbeiter gefunden.');
          deptIconRefresh();
          return;
        }

        list.innerHTML = team.map(emp => {
          const percentParts = [];

          if (deptSafeNumber(emp.percent) > 0) {
            percentParts.push(`${deptSafeNumber(emp.percent)}%`);
          }

          if (deptSafeNumber(emp.office_percent) > 0) {
            percentParts.push(`Office ${deptSafeNumber(emp.office_percent)}%`);
          }

          if (deptSafeNumber(emp.montage_percent) > 0) {
            percentParts.push(`Montage ${deptSafeNumber(emp.montage_percent)}%`);
          }

          if (deptSafeNumber(emp.working_hours) > 0) {
            percentParts.push(`${deptSafeNumber(emp.working_hours)} Std.`);
          }

          return `
            <div class="dept-team-row">
              <img class="dept-avatar"
                   src="${deptEscape(emp.image_url || emp.image || '/images/gender/male.png')}"
                   alt="${deptEscape(emp.name || 'Mitarbeiter')}">

              <div>
                <div class="dept-row-title">
                  ${deptEscape(emp.name || 'Mitarbeiter')}
                </div>

                <div class="dept-row-meta">
                  ${deptEscape(emp.position || 'Ohne Position')}
                  ${emp.qualification ? ' · ' + deptEscape(emp.qualification) : ''}
                  ${percentParts.length ? ' · ' + deptEscape(percentParts.join(' · ')) : ''}
                  ${emp.main ? ' · Hauptposition' : ''}
                </div>
              </div>
            </div>
          `;
        }).join('');

        deptIconRefresh();
      }

      function deptFlowTypeLabel(type) {
        const labels = {
          leads: 'Lead',
          products: 'Produkt',
          offers: 'Angebot',
          deals: 'Auftrag',
          tickets: 'Ticket',
          appointments: 'Termin',
          tasks: 'Aufgabe',
          invoices: 'Rechnung'
        };

        return labels[type] || 'Eintrag';
      }

      function deptFlowTypeIcon(type) {
        const icons = {
          leads: 'user-round',
          products: 'package-check',
          offers: 'file-text',
          deals: 'badge-check',
          tickets: 'ticket',
          appointments: 'calendar-days',
          tasks: 'check-square',
          invoices: 'receipt-text'
        };

        return icons[type] || 'circle-dot';
      }

      function deptFlowText(...values) {
        for (const value of values) {
          if (value !== null && value !== undefined && String(value).trim() !== '') {
            return String(value).trim();
          }
        }

        return '';
      }

      function deptFlowPersonList(item) {
        const list = [];

        [
          item.responsible,
          item.employee,
          item.field_employee,
          item.creator,
          item.assignee,
          item.assigned_by,
          item.created_by,
          item.contact_person,
          item.first_contact,
          item.checked_by,
          item.reviewer
        ].forEach(person => {
          if (person && (person.name || person.id || person.image_url || person.image)) {
            list.push(person);
          }
        });

        if (Array.isArray(item.employees)) {
          item.employees.forEach(person => {
            if (person && (person.name || person.id || person.image_url || person.image)) {
              list.push(person);
            }
          });
        }

        const seen = new Set();

        return list.filter(person => {
          const key = person.id ? `id:${person.id}` : `name:${person.name || person.image_url || person.image}`;

          if (seen.has(key)) {
            return false;
          }

          seen.add(key);
          return true;
        });
      }

      function deptFlowCustomerName(item, type) {
        return deptFlowText(
          item.customer?.name,
          item.lead,
          type === 'leads' ? item.title : '',
          item.title,
          `${deptFlowTypeLabel(type)} #${item.id || ''}`
        );
      }

      function deptFlowCustomerNo(item) {
        const customerNumber = deptFlowText(item.customer?.number);
        const itemNumber = deptFlowText(item.number);

        if (customerNumber && itemNumber && customerNumber !== itemNumber) {
          return `${customerNumber} · ${itemNumber}`;
        }

        return deptFlowText(customerNumber, itemNumber, item.id ? `#${item.id}` : '');
      }

      function deptFlowAddress(item) {
        return deptFlowText(
          item.object?.address,
          item.customer?.address,
          item.address,
          item.object?.name
        );
      }

      function deptFlowProduct(item) {
        const product = deptFlowText(item.product, item.item_title);
        const service = deptFlowText(item.service);

        if (product && service) {
          return `${product} · ${service}`;
        }

        return product;
      }

      function deptFlowSource(item, type) {
        if (type === 'tickets') {
          return deptFlowText(item.source);
        }

        if (type === 'appointments') {
          return deptFlowText(item.appointment_type);
        }

        if (type === 'tasks') {
          return deptFlowText(item.stage, item.sub_stage);
        }

        if (type === 'offers' || type === 'deals') {
          return deptFlowText(item.folder_name, item.document_status);
        }

        if (type === 'invoices') {
          return deptFlowText(item.issue_date, item.due_date);
        }

        return deptFlowText(item.object?.status_label, item.object?.status);
      }

      function deptFlowErrorType(item, type) {
        if (type === 'tickets') {
          return deptFlowText(item.error_type);
        }

        if (type === 'appointments') {
          return deptFlowText(item.related_ticket, item.related_task);
        }

        if (type === 'tasks') {
          return deptFlowText(item.sub_stage);
        }

        return '';
      }

      function deptFlowPriority(item) {
        return deptFlowText(item.priority_label, item.priority);
      }

      function deptFlowStatus(item) {
        return deptFlowText(item.status_label, item.status);
      }

      function deptFlowDate(item, type) {
        if (type === 'appointments') {
          return deptFlowText(item.start, item.date);
        }

        if (type === 'tasks') {
          return deptFlowText(item.due, item.date);
        }

        if (type === 'invoices') {
          return deptFlowText(item.due_date, item.issue_date, item.date);
        }

        return deptFlowText(item.date);
      }

      function deptFlowAmount(item, type) {
        if (type === 'invoices') {
          if (deptSafeNumber(item.open) > 0) {
            return formatDeptMoney(item.open);
          }

          if (deptSafeNumber(item.total) > 0) {
            return formatDeptMoney(item.total);
          }
        }

        if (type === 'offers' && deptSafeNumber(item.total_gross) > 0) {
          return formatDeptMoney(item.total_gross);
        }

        if (type === 'deals' && deptSafeNumber(item.price) > 0) {
          return formatDeptMoney(item.price);
        }

        return '';
      }

      function deptFlowOpenUrl(item) {
        return deptFlowText(item.url, item.profile_url, item.open_url, '#');
      }

      function deptFlowChip(icon, value, extraClass = '') {
        const clean = deptFlowText(value);

        if (!clean) {
          return '';
        }

        return `
          <span class="dept-flow-chip ${deptEscape(extraClass)}" title="${deptEscape(clean)}">
            <i data-lucide="${deptEscape(icon)}"></i>
            <span>${deptEscape(clean)}</span>
          </span>
        `;
      }

      function deptFlowPeopleHtml(people) {
        people = deptSafeArray(people);

        if (!people.length) {
          return '';
        }

        const avatars = people.slice(0, 3).map(person => {
          const image = person.image_url || person.image || '/images/gender/male.png';
          const name = person.name || 'Mitarbeiter';

          return `
            <img class="dept-flow-mini-avatar"
                 src="${deptEscape(image)}"
                 alt="${deptEscape(name)}"
                 title="${deptEscape(name)}">
          `;
        }).join('');

        const names = people
          .map(person => person.name)
          .filter(Boolean)
          .slice(0, 3)
          .join(', ');

        return `
          <span class="dept-flow-people">
            ${avatars}
            ${names ? `<span class="dept-flow-people-name">${deptEscape(names)}</span>` : ''}
          </span>
        `;
      }

      function renderDepartmentRecent() {
        const list = deptEl('deptRecentList');
        const data = departmentDashboardState.data;
        const type = departmentDashboardState.recentType || 'leads';

        if (!list) {
          return;
        }

        if (!data) {
          list.innerHTML = deptEmptyHtml('loader-2', 'Daten werden geladen...');
          deptIconRefresh();
          return;
        }

        const items = deptSafeArray(data.recent?.[type]);

        if (!items.length) {
          list.innerHTML = deptEmptyHtml('inbox', 'Keine aktuellen Einträge gefunden.');
          deptIconRefresh();
          return;
        }

        list.innerHTML = items.map(item => {
          const typeLabel = item.type_label || deptFlowTypeLabel(type);
          const typeIcon = deptFlowTypeIcon(type);
          const people = deptFlowPersonList(item);
          const mainPerson = people[0] || null;
          const avatar = mainPerson?.image_url || mainPerson?.image || '/images/gender/male.png';
          const customerName = deptFlowCustomerName(item, type);
          const customerNo = deptFlowCustomerNo(item);
          const address = deptFlowAddress(item);
          const product = deptFlowProduct(item);
          const source = deptFlowSource(item, type);
          const errorType = deptFlowErrorType(item, type);
          const priority = deptFlowPriority(item);
          const status = deptFlowStatus(item);
          const date = deptFlowDate(item, type);
          const amount = deptFlowAmount(item, type);
          const url = deptFlowOpenUrl(item);
          const actionLabel = item.action_label || `${typeLabel} öffnen`;

          return `
            <div class="dept-flow-card">
              <div class="dept-flow-avatar-box">
                <img class="dept-flow-avatar"
                     src="${deptEscape(avatar)}"
                     alt="${deptEscape(mainPerson?.name || typeLabel)}">

                <span class="dept-flow-type-icon">
                  <i data-lucide="${deptEscape(typeIcon)}"></i>
                </span>
              </div>

              <div class="dept-flow-body">
                <div class="dept-flow-head">
                  <div class="dept-flow-name-row">
                    <span class="dept-flow-type-pill">${deptEscape(typeLabel)}</span>
                    <span class="dept-flow-customer" title="${deptEscape(customerName)}">
                      ${deptEscape(customerName || typeLabel)}
                    </span>
                  </div>

                  <div class="dept-flow-sub">
                    <i data-lucide="hash" style="width:12px;height:12px;"></i>
                    <span>${deptEscape(customerNo || '–')}</span>
                  </div>
                </div>

                <div class="dept-flow-line">
                  ${deptFlowChip('map-pin', address)}
                  ${deptFlowChip('box', product, 'product')}
                </div>

                <div class="dept-flow-line">
                  ${deptFlowChip('radio', source)}
                  ${deptFlowChip('triangle-alert', errorType, 'danger')}
                  ${deptFlowChip('flag', priority, 'warning')}
                  ${deptFlowChip('activity', status, 'green')}
                  ${deptFlowChip('euro', amount, 'warning')}
                </div>

                <div class="dept-flow-line">
                  ${deptFlowPeopleHtml(people)}
                  ${deptFlowChip('clock-3', date)}
                </div>
              </div>

              <a class="dept-flow-open"
                 href="${deptEscape(url)}"
                 title="${deptEscape(actionLabel)}"
                 aria-label="${deptEscape(actionLabel)}">
                <i data-lucide="external-link"></i>
              </a>
            </div>
          `;
        }).join('');

        deptIconRefresh();
      }

      function historyIcon(type) {
        const value = String(type || '').toLowerCase();

        if (value.includes('ticket') || value.includes('problem')) {
          return 'ticket';
        }

        if (value.includes('offer')) {
          return 'file-text';
        }

        if (value.includes('deal')) {
          return 'badge-check';
        }

        if (value.includes('appointment')) {
          return 'calendar-days';
        }

        if (value.includes('task')) {
          return 'check-square';
        }

        return 'history';
      }

      function historyLabel(type) {
        const labels = {
          LeadProductList: 'Produkt',
          Offer: 'Angebot',
          Deal: 'Auftrag',
          Problem: 'Ticket',
          MainAppointment: 'Termin',
          PersonalTask: 'Aufgabe'
        };

        return labels[type] || type || 'Änderung';
      }

      function renderDepartmentChanges(changes) {
        const list = deptEl('deptChangeList');

        if (!list) {
          return;
        }

        changes = deptSafeArray(changes);

        if (!changes.length) {
          list.innerHTML = deptEmptyHtml('history', 'Keine Änderungen gefunden.');
          deptIconRefresh();
          return;
        }

        list.innerHTML = changes.slice(0, 12).map(change => {
          const type = historyLabel(change.model_type);
          const icon = historyIcon(change.model_type);
          const user = change.user_name || change.employee_name || 'System';
          const title = change.event_type || change.event || 'Aktualisiert';

          return `
            <div class="dept-change-row">
              <span class="dept-change-icon">
                <i data-lucide="${deptEscape(icon)}"></i>
              </span>

              <div>
                <div class="dept-row-title">
                  ${deptEscape(type)} · ${deptEscape(title)}
                </div>
                <div class="dept-row-meta">
                  ${deptEscape(user)} · ${deptEscape(change.date || change.created_at || '')}
                </div>
              </div>
            </div>
          `;
        }).join('');

        deptIconRefresh();
      }

      function renderDepartmentHistory(changes) {
        const list = deptEl('deptHistoryList');

        if (!list) {
          return;
        }

        changes = deptSafeArray(changes);

        if (!changes.length) {
          list.innerHTML = deptEmptyHtml('history', 'Keine Historie gefunden.');
          deptIconRefresh();
          return;
        }

        list.innerHTML = changes.slice(0, 20).map(change => {
          const type = historyLabel(change.model_type);
          const icon = historyIcon(change.model_type);
          const user = change.user_name || change.employee_name || 'System';
          const title = change.event_type || change.event || 'Aktualisiert';

          return `
            <div class="dept-history-row">
              <span class="dept-history-icon">
                <i data-lucide="${deptEscape(icon)}"></i>
              </span>

              <div>
                <div class="dept-row-title">${deptEscape(type)} · ${deptEscape(title)}</div>
                <div class="dept-row-meta">${deptEscape(user)} · ${deptEscape(change.date || change.created_at || '')}</div>
              </div>
            </div>
          `;
        }).join('');

        deptIconRefresh();
      }

      function deptDestroyChart(key) {
        if (departmentDashboardState.charts[key]) {
          departmentDashboardState.charts[key].destroy();
          delete departmentDashboardState.charts[key];
        }
      }

      function deptChartColor(name, fallback) {
        if (typeof chartColors !== 'undefined' && chartColors?.[name]) {
          return chartColors[name];
        }

        return fallback;
      }

      function deptChartOptions(extra = {}) {
        if (typeof chartOptions === 'function') {
          return chartOptions(extra);
        }

        return {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: extra.legend !== false,
              position: extra.legendPosition || 'bottom'
            }
          },
          scales: extra.scales === false ? undefined : {
            x: {
              grid: {
                display: false
              }
            },
            y: {
              beginAtZero: true
            }
          }
        };
      }

      function renderDeptPieChart(canvasId, labels, values, chartKey) {
        const canvas = deptEl(canvasId);

        if (!canvas || !window.Chart) {
          return;
        }

        labels = deptSafeArray(labels);
        values = deptSafeArray(values).map(deptSafeNumber);

        const hasData = values.some(value => value > 0);

        deptDestroyChart(chartKey);

        departmentDashboardState.charts[chartKey] = new Chart(canvas.getContext('2d'), {
          type: 'doughnut',
          data: {
            labels: hasData ? labels : ['Keine Daten'],
            datasets: [{
              data: hasData ? values : [1],
              backgroundColor: [
                deptChartColor('green', '#8bc34a'),
                deptChartColor('blue', '#74b2d4'),
                deptChartColor('warning', '#f8ac00'),
                deptChartColor('danger', '#e50656'),
                deptChartColor('greenSoft', '#edf7df'),
                deptChartColor('blueSoft', '#e6f0f7')
              ],
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  boxWidth: 12,
                  font: {
                    weight: '700'
                  }
                }
              }
            }
          }
        });
      }

      function renderDeptBarChart(canvasId, labels, datasets, chartKey) {
        const canvas = deptEl(canvasId);

        if (!canvas || !window.Chart) {
          return;
        }

        labels = deptSafeArray(labels);

        const normalizedDatasets = deptSafeArray(datasets).map(dataset => ({
          ...dataset,
          data: deptSafeArray(dataset.data).map(deptSafeNumber)
        }));

        deptDestroyChart(chartKey);

        departmentDashboardState.charts[chartKey] = new Chart(canvas.getContext('2d'), {
          type: 'bar',
          data: {
            labels: labels.length ? labels : ['Keine Daten'],
            datasets: normalizedDatasets.length ? normalizedDatasets : [{
              label: 'Keine Daten',
              data: [0],
              backgroundColor: deptChartColor('blue', '#74b2d4'),
              borderRadius: 8
            }]
          },
          options: deptChartOptions({
            legend: true
          })
        });
      }

      function renderDepartmentProjectPie(data) {
        data = data || {};

        const total = deptSafeArray(data.values).reduce((sum, value) => sum + deptSafeNumber(value), 0);
        const totalEl = deptEl('deptPieTotal');
        const periodEl = deptEl('deptPiePeriod');

        if (totalEl) {
          totalEl.textContent = `${formatDeptNumber(total)} Vorgänge`;
        }

        if (periodEl && departmentDashboardState.data?.period) {
          periodEl.textContent = `${departmentDashboardState.data.period.from} bis ${departmentDashboardState.data.period.to}`;
        }

        renderDeptPieChart('deptPieChart', data.labels || [], data.values || [], 'deptPieChart');
      }

      function renderDepartmentRevenueBar(data) {
        data = data || {};

        const labels = data.labels || [];
        const totals = data.totals || data.values || [];
        const counts = data.counts || [];
        const total = deptSafeArray(totals).reduce((sum, value) => sum + deptSafeNumber(value), 0);
        const totalEl = deptEl('deptBarTotal');
        const periodEl = deptEl('deptBarPeriod');

        if (totalEl) {
          totalEl.textContent = formatDeptMoney(total);
        }

        if (periodEl && departmentDashboardState.data?.period) {
          periodEl.textContent = `${departmentDashboardState.data.period.from} bis ${departmentDashboardState.data.period.to}`;
        }

        renderDeptBarChart(
          'deptBarChart',
          labels,
          [
            {
              label: 'Summe',
              data: totals,
              backgroundColor: deptChartColor('green', '#8bc34a'),
              borderRadius: 8
            },
            {
              label: 'Anzahl',
              data: counts,
              backgroundColor: deptChartColor('blue', '#74b2d4'),
              borderRadius: 8
            }
          ],
          'deptBarChart'
        );
      }

      function renderDepartmentCharts(charts) {
        charts = charts || {};

        renderDepartmentProjectPie(charts.lead_pipeline || charts.objects_by_status || charts.items_by_type || {});
        renderDepartmentRevenueBar(charts.revenue_by_status || {});

        renderDeptPieChart(
          'deptItemsChart',
          charts.items_by_type?.labels || [],
          charts.items_by_type?.values || [],
          'deptItemsChart'
        );

        renderDeptPieChart(
          'deptPositionChart',
          charts.team_by_position?.labels || [],
          charts.team_by_position?.values || [],
          'deptPositionChart'
        );

        renderDeptBarChart(
          'deptWorkloadChart',
          charts.workload_by_employee?.labels || [],
          [
            {
              label: 'Aufgaben',
              data: charts.workload_by_employee?.tasks || [],
              backgroundColor: deptChartColor('green', '#8bc34a'),
              borderRadius: 8
            },
            {
              label: 'Termine',
              data: charts.workload_by_employee?.appointments || [],
              backgroundColor: deptChartColor('blue', '#74b2d4'),
              borderRadius: 8
            },
            {
              label: 'Tickets',
              data: charts.workload_by_employee?.tickets || [],
              backgroundColor: deptChartColor('warning', '#f8ac00'),
              borderRadius: 8
            }
          ],
          'deptWorkloadChart'
        );

        renderDeptBarChart(
          'deptInvoiceChart',
          charts.revenue_by_status?.labels || [],
          [
            {
              label: 'Anzahl',
              data: charts.revenue_by_status?.counts || [],
              backgroundColor: deptChartColor('blue', '#74b2d4'),
              borderRadius: 8
            },
            {
              label: 'Summe',
              data: charts.revenue_by_status?.totals || [],
              backgroundColor: deptChartColor('green', '#8bc34a'),
              borderRadius: 8
            }
          ],
          'deptInvoiceChart'
        );

        if (typeof resizeAllCharts === 'function') {
          setTimeout(resizeAllCharts, 80);
        }
      }

      async function initDepartmentDashboard() {
        if (!deptEl('departmentSelect')) {
          return;
        }

        const today = new Date();
        const from = new Date();

        from.setDate(today.getDate() - 30);

        const fromInput = deptEl('departmentFromDate');
        const toInput = deptEl('departmentToDate');

        if (fromInput && !fromInput.value) {
          fromInput.value = from.toISOString().slice(0, 10);
        }

        if (toInput && !toInput.value) {
          toInput.value = today.toISOString().slice(0, 10);
        }

        if (!departmentDashboardState.bound) {
          deptEl('departmentSelect')?.addEventListener('change', function () {
            const departmentId = String(this.value || '');

            departmentDashboardState.currentDepartmentId = departmentId;
            departmentDashboardState.loaded = false;
            departmentDashboardState.data = null;

            loadDepartmentOverview({
              departmentId: departmentId,
              force: true
            });
          });

          deptEl('departmentFromDate')?.addEventListener('change', function () {
            loadDepartmentOverview({
              force: true
            });
          });

          deptEl('departmentToDate')?.addEventListener('change', function () {
            loadDepartmentOverview({
              force: true
            });
          });

          deptEl('refreshDepartmentDashboardBtn')?.addEventListener('click', function () {
            loadDepartmentOverview({
              force: true
            });
          });

          document.querySelectorAll('#deptRecentTabs button[data-type]').forEach(tab => {
            tab.addEventListener('click', function () {
              document.querySelectorAll('#deptRecentTabs button[data-type]').forEach(item => {
                item.classList.toggle('active', item === tab);
              });

              departmentDashboardState.recentType = tab.dataset.type || 'leads';
              renderDepartmentRecent();
            });
          });

          departmentDashboardState.bound = true;
        }

        await loadDepartmentOptions();

        if (departmentDashboardState.currentDepartmentId) {
          await loadDepartmentOverview({
            departmentId: departmentDashboardState.currentDepartmentId,
            force: true
          });
        }
      }

      function initDeptCharts() {
        const departmentSection = document.querySelector('.view-section[data-view-section="department"]');

        if (!departmentSection || !departmentSection.classList.contains('active')) {
          return;
        }

        if (!departmentDashboardState.loaded) {
          initDepartmentDashboard();
          return;
        }

        if (departmentDashboardState.data) {
          renderDepartmentCharts(departmentDashboardState.data.charts || {});
          renderDepartmentHistory(departmentDashboardState.data.recent?.changes || []);
          renderDepartmentChanges(departmentDashboardState.data.recent?.changes || []);
          renderDepartmentRecent();
        }

        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            if (typeof resizeChartsForActiveView === 'function') {
              resizeChartsForActiveView();
            }

            setTimeout(() => {
              if (typeof resizeChartsForActiveView === 'function') {
                resizeChartsForActiveView();
              }
            }, 200);
          });
        });
      }

      window.initDepartmentDashboard = initDepartmentDashboard;
      window.loadDepartmentOverview = loadDepartmentOverview;
      window.initDeptCharts = initDeptCharts;
      window.renderDepartmentOverview = renderDepartmentOverview;
      window.renderDepartmentRecent = renderDepartmentRecent;
      window.renderDepartmentCharts = renderDepartmentCharts;
      window.formatDeptMoney = formatDeptMoney;
    })();
  </script>

  <script>
    const companyDashboardState = {
      loaded: false,
      loading: false,
      data: null
    };

    function formatCompanyMoney(value) {
      return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0
      }).format(Number(value || 0));
    }

    function setCompanyText(id, value) {
      const el = document.getElementById(id);
      if (el) el.textContent = value;
    }

    async function loadCompanyOverview() {
      const routes = window.DASHBOARD_COMPANY_ROUTES || {};
      const info = document.getElementById('companyInfoNote');

      if (!routes.overview) {
        showToast('Company Overview Route fehlt');
        return;
      }

      if (companyDashboardState.loading) return;

      companyDashboardState.loading = true;

      if (info) {
        info.innerHTML = `
                        <i data-lucide="loader-2" style="width:16px;color:var(--color-blue-dark);"></i>
                        Unternehmensdaten werden geladen...
                    `;
      }

      try {
        const url = new URL(routes.overview, window.location.origin);

        const from = document.getElementById('companyFromDate')?.value || '';
        const to = document.getElementById('companyToDate')?.value || '';

        if (from) url.searchParams.set('from', from);
        if (to) url.searchParams.set('to', to);

        const response = await fetch(url.toString(), {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        companyDashboardState.loaded = true;
        companyDashboardState.data = result;

        renderCompanyOverview(result);
        renderCompanyMainArea(result.main_department);
        renderCompanyDepartmentList(result.recent?.departments || []);
        renderCompanyHistory(result.recent?.changes || []);

        setTimeout(() => {
          renderCompanyCharts(result.charts || {});
          resizeAllCharts();
        }, 160);

        if (info) {
          info.innerHTML = `
                            <i data-lucide="check-circle" style="width:16px;color:var(--color-green-dark);"></i>
                            Zeitraum: ${escapeHtml(result.period?.from || '–')} bis ${escapeHtml(result.period?.to || '–')}
                        `;
        }

        if (window.lucide) lucide.createIcons();
      } catch (error) {
        console.error('Company overview failed:', error);

        if (info) {
          info.innerHTML = `
                            <i data-lucide="alert-circle" style="width:16px;color:var(--color-danger);"></i>
                            Unternehmensdaten konnten nicht geladen werden.
                        `;
        }

        showToast(error?.message || 'Unternehmensdaten konnten nicht geladen werden');
      } finally {
        companyDashboardState.loading = false;
      }
    }

    function renderCompanyOverview(result) {
      const kpis = result.kpis || {};

      setCompanyText('companyEmployees', kpis.employees || 0);
      setCompanyText('companyDepartments', kpis.departments || 0);
      setCompanyText('companyLeads', kpis.leads || 0);
      setCompanyText('companyOffers', kpis.offers || 0);
      setCompanyText('companyDeals', kpis.deals || 0);
      setCompanyText('companyTickets', kpis.tickets || 0);
      setCompanyText('companyInvoiceTotal', formatCompanyMoney(kpis.invoice_total || 0));
      setCompanyText('companyInvoiceOpen', formatCompanyMoney(kpis.invoice_open || 0));

      const subtitle = document.getElementById('companySubtitle');
      if (subtitle && result.period) {
        subtitle.textContent = `${result.period.from} bis ${result.period.to} · Unternehmensperspektive`;
      }
    }

    function renderCompanyMainArea(mainDepartment) {
      const box = document.getElementById('companyMainAreaCard');
      if (!box) return;

      if (!mainDepartment) {
        box.innerHTML = '<div class="empty-state">Kein Hauptbereich gefunden.</div>';
        return;
      }

      box.innerHTML = `
                    <div class="company-main-box">
                        <strong>${escapeHtml(mainDepartment.name || 'Hauptbereich')}</strong>
                        <span>
                            ${escapeHtml(mainDepartment.branch || 'Keine Filiale')}
                            ${mainDepartment.parent ? ' · ' + escapeHtml(mainDepartment.parent) : ''}
                        </span>
                    </div>

                    <div class="company-main-grid">
                        <div class="company-main-mini">
                            <strong>${Number(mainDepartment.percent || 0)}%</strong>
                            <span>Anteil</span>
                        </div>

                        <div class="company-main-mini">
                            <strong>${Number(mainDepartment.working_hours || 0)}</strong>
                            <span>Stunden</span>
                        </div>

                        <div class="company-main-mini">
                            <strong>${Number(mainDepartment.office_percent || 0)}%</strong>
                            <span>Office</span>
                        </div>

                        <div class="company-main-mini">
                            <strong>${Number(mainDepartment.montage_percent || 0)}%</strong>
                            <span>Montage</span>
                        </div>
                    </div>
                `;
    }

    function renderCompanyDepartmentList(items) {
      const list = document.getElementById('companyDepartmentList');
      if (!list) return;

      if (!items.length) {
        list.innerHTML = '<div class="empty-state">Keine Bereiche gefunden.</div>';
        return;
      }

      list.innerHTML = items.map(item => `
                    <div class="dept-recent-row">
                        <span class="dept-kpi-icon blue" style="width:38px;height:38px;">
                            <i data-lucide="building-2"></i>
                        </span>

                        <div>
                            <div class="dept-row-title">${escapeHtml(item.name || 'Bereich')}</div>
                            <div class="dept-row-meta">
                                Team ${Number(item.team_count || 0)}
                                · Leads ${Number(item.leads || 0)}
                                · Angebote ${Number(item.offers || 0)}
                                · Aufträge ${Number(item.deals || 0)}
                            </div>
                        </div>

                        <span class="dept-mini-pill">${Number(item.deals || 0)} AB</span>
                    </div>
                `).join('');

      if (window.lucide) lucide.createIcons();
    }

    function renderCompanyHistory(changes) {
      const list = document.getElementById('companyHistoryList');
      if (!list) return;

      if (!changes.length) {
        list.innerHTML = '<div class="empty-state">Keine Historie gefunden.</div>';
        return;
      }

      list.innerHTML = changes.map(change => `
                    <div class="dept-change-row">
                        <span class="dept-kpi-icon warning" style="width:38px;height:38px;">
                            <i data-lucide="history"></i>
                        </span>

                        <div>
                            <div class="dept-row-title">
                                ${escapeHtml(change.event_type || 'Änderung')} · ${escapeHtml(change.model_type || '')}
                            </div>
                            <div class="dept-row-meta">
                                ${escapeHtml(change.user_name || 'Unbekannt')} · ${escapeHtml(change.date || '')}
                            </div>
                        </div>

                        <span class="dept-mini-pill">#${escapeHtml(change.model_id || '')}</span>
                    </div>
                `).join('');

      if (window.lucide) lucide.createIcons();
    }

    function renderCompanyCharts(charts) {
      renderCompanyBarChart(
        'companyRevenueChart',
        charts.monthly_revenue?.labels || [],
        [
          {
            label: 'Umsatz',
            data: charts.monthly_revenue?.values || [],
            backgroundColor: chartColors.green,
            borderRadius: 8
          }
        ],
        'companyRevenueChart'
      );

      renderCompanyPieChart(
        'companyTypesChart',
        charts.items_by_type?.labels || [],
        charts.items_by_type?.values || [],
        'companyTypesChart'
      );

      renderCompanyBarChart(
        'companyDepartmentChart',
        charts.department_performance?.labels || [],
        [
          {
            label: 'Leads',
            data: charts.department_performance?.leads || [],
            backgroundColor: chartColors.blue,
            borderRadius: 8
          },
          {
            label: 'Angebote',
            data: charts.department_performance?.offers || [],
            backgroundColor: chartColors.warning,
            borderRadius: 8
          },
          {
            label: 'Aufträge',
            data: charts.department_performance?.deals || [],
            backgroundColor: chartColors.green,
            borderRadius: 8
          }
        ],
        'companyDepartmentChart'
      );
    }

    function renderCompanyPieChart(canvasId, labels, values, chartKey) {
      const canvas = document.getElementById(canvasId);
      if (!canvas || !window.Chart) return;

      const ctx = canvas.getContext('2d');
      if (!ctx) return;

      destroyChart(chartKey);
      destroyChart(canvas);

      const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels.length ? labels : ['Keine Daten'],
          datasets: [{
            data: values.length && values.some(v => Number(v) > 0) ? values : [1],
            backgroundColor: [
              chartColors.green,
              chartColors.blue,
              chartColors.warning,
              chartColors.blueSoft,
              chartColors.greenSoft,
              chartColors.danger
            ],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '68%',
          plugins: {
            legend: {
              position: 'right',
              labels: {
                boxWidth: 12,
                font: { weight: '700' }
              }
            }
          }
        }
      });

      registerChart(chartKey, canvas, chart);
    }

    function renderCompanyBarChart(canvasId, labels, datasets, chartKey) {
      const canvas = document.getElementById(canvasId);
      if (!canvas || !window.Chart) return;

      const ctx = canvas.getContext('2d');
      if (!ctx) return;

      destroyChart(chartKey);
      destroyChart(canvas);

      const chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels.length ? labels : ['Keine Daten'],
          datasets: datasets.length ? datasets : [{
            label: 'Keine Daten',
            data: [0],
            backgroundColor: chartColors.blue,
            borderRadius: 8
          }]
        },
        options: chartOptions({ legend: true })
      });

      registerChart(chartKey, canvas, chart);
    }

    function initCompanyChart() {
      if (!companyDashboardState.loaded) {
        initCompanyDashboard();
        return;
      }

      if (companyDashboardState.data) {
        renderCompanyCharts(companyDashboardState.data.charts || {});
      }
    }

    async function initCompanyDashboard() {
      const today = new Date();
      const yearStart = new Date(today.getFullYear(), 0, 1);

      const fromInput = document.getElementById('companyFromDate');
      const toInput = document.getElementById('companyToDate');

      if (fromInput && !fromInput.value) {
        fromInput.value = yearStart.toISOString().slice(0, 10);
      }

      if (toInput && !toInput.value) {
        toInput.value = today.toISOString().slice(0, 10);
      }

      if (!initCompanyDashboard.bound) {
        document.getElementById('companyFromDate')?.addEventListener('change', loadCompanyOverview);
        document.getElementById('companyToDate')?.addEventListener('change', loadCompanyOverview);
        document.getElementById('refreshCompanyDashboardBtn')?.addEventListener('click', loadCompanyOverview);

        initCompanyDashboard.bound = true;
      }

      await loadCompanyOverview();
    }

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('[data-widget-key="companyLine"]').forEach(el => el.remove());

      if (typeof initCompanyDashboard === 'function') {
        initCompanyDashboard();
      }
    });

    window.initCompanyChart = initCompanyChart;
    window.loadCompanyOverview = loadCompanyOverview;
  </script>
  <script>
    const calendarWidgetState = {
      month: new Date().toISOString().slice(0, 7),
      selectedDate: new Date().toISOString().slice(0, 10),
      employeeId: null,
      data: null,
      loadedEmployees: false,
      loading: false,
      initialized: false
    };

    function dashCalRoutes() {
      return window.DASHBOARD_CALENDAR_ROUTES || {};
    }

    async function initEmployeeCalendarWidget(force = false) {
      const grid = document.getElementById('dashCalendarGrid');

      if (!grid) {
        return;
      }

      if (calendarWidgetState.loading) {
        return;
      }

      if (calendarWidgetState.initialized && !force) {
        return;
      }

      calendarWidgetState.loading = true;

      try {
        await loadCalendarEmployees(force);
        await loadCalendarMonth();
        await loadCalendarDay(calendarWidgetState.selectedDate);

        calendarWidgetState.initialized = true;

        if (window.lucide) {
          lucide.createIcons();
        }
      } catch (error) {
        console.error('Calendar widget init failed:', error);

        grid.innerHTML = `
                <div class="empty-state">
                    Kalender konnte nicht geladen werden.
                </div>
            `;
      } finally {
        calendarWidgetState.loading = false;
      }
    }

    function dashCalRoutes() {
      return window.DASHBOARD_CALENDAR_ROUTES || {};
    }

    async function initEmployeeCalendarWidget() {
      if (!document.getElementById('dashCalendarGrid')) return;

      await loadCalendarEmployees();
      await loadCalendarMonth();
      await loadCalendarDay(calendarWidgetState.selectedDate);

      if (window.lucide) lucide.createIcons();
    }

    async function loadCalendarEmployees(force = false) {
      if (calendarWidgetState.loadedEmployees && !force) {
        return;
      }

      const routes = dashCalRoutes();
      const select = document.getElementById('dashCalEmployeeSelect');

      if (!routes.employees || !select) {
        return;
      }

      const response = await fetch(routes.employees, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw result;
      }

      calendarWidgetState.employeeId = calendarWidgetState.employeeId || result.current_employee_id;

      select.innerHTML = '';

      (result.employees || []).forEach(employee => {
        const option = document.createElement('option');
        option.value = employee.id;
        option.textContent = Number(employee.id) === Number(result.current_employee_id)
          ? `${employee.name} · Ich`
          : employee.name;

        select.appendChild(option);
      });

      select.value = calendarWidgetState.employeeId || result.current_employee_id;

      calendarWidgetState.loadedEmployees = true;
    }

    async function loadCalendarMonth() {
      const routes = dashCalRoutes();

      if (!routes.month) return;

      const url = new URL(routes.month, window.location.origin);
      url.searchParams.set('month', calendarWidgetState.month);

      if (calendarWidgetState.employeeId) {
        url.searchParams.set('employee_id', calendarWidgetState.employeeId);
      }

      const response = await fetch(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw result;
      }

      calendarWidgetState.data = result;

      renderCalendarMonth(result);
    }

    async function loadCalendarDay(date) {
      const routes = dashCalRoutes();

      if (!routes.day) return;

      calendarWidgetState.selectedDate = date;

      const url = new URL(routes.day, window.location.origin);
      url.searchParams.set('date', date);

      if (calendarWidgetState.employeeId) {
        url.searchParams.set('employee_id', calendarWidgetState.employeeId);
      }

      const response = await fetch(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const result = await response.json();

      if (!response.ok || !result.success) {
        throw result;
      }

      renderCalendarDay(result);
      highlightSelectedCalendarDay(date);
    }

    function renderCalendarMonth(result) {
      const grid = document.getElementById('dashCalendarGrid');
      const title = document.getElementById('dashCalMonthLabel');
      const summary = document.getElementById('dashCalSummary');

      if (!grid) return;

      if (title) title.textContent = result.month_label || result.month;
      if (summary) {
        summary.textContent = `${result.summary?.total || 0} Termine · ${result.summary?.with_report || 0} mit Bericht · ${result.summary?.tickets || 0} Tickets`;
      }

      const monthStart = new Date(result.month + '-01T00:00:00');
      const year = monthStart.getFullYear();
      const monthIndex = monthStart.getMonth();

      const firstDay = new Date(year, monthIndex, 1);
      const lastDay = new Date(year, monthIndex + 1, 0);

      const startOffset = (firstDay.getDay() + 6) % 7;
      const startDate = new Date(year, monthIndex, 1 - startOffset);

      const dayMap = new Map((result.days || []).map(day => [day.date, day]));

      const cells = [];

      for (let i = 0; i < 42; i++) {
        const d = new Date(startDate);
        d.setDate(startDate.getDate() + i);

        const dateKey = d.toISOString().slice(0, 10);
        const dayData = dayMap.get(dateKey);
        const isCurrentMonth = d.getMonth() === monthIndex;
        const isToday = dateKey === new Date().toISOString().slice(0, 10);
        const isSelected = dateKey === calendarWidgetState.selectedDate;

        const dots = dayData
          ? `
                      ${dayData.count ? '<span class="dash-cal-dot"></span>' : ''}
                      ${dayData.has_report ? '<span class="dash-cal-dot report"></span>' : ''}
                      ${dayData.missing_report ? '<span class="dash-cal-dot missing"></span>' : ''}
                      ${dayData.ticket_count ? '<span class="dash-cal-dot ticket"></span>' : ''}
                    `
          : '';

        cells.push(`
                  <button type="button"
                          class="dash-cal-day ${!isCurrentMonth ? 'is-muted' : ''} ${isToday ? 'is-today' : ''} ${isSelected ? 'is-selected' : ''}"
                          data-cal-date="${dateKey}">
                      <span class="dash-cal-day-number">${d.getDate()}</span>
                      <span class="dash-cal-day-dots">${dots}</span>
                  </button>
              `);
      }

      grid.innerHTML = cells.join('');
    }

    function renderCalendarDay(result) {
      const label = document.getElementById('dashCalSelectedDay');
      const count = document.getElementById('dashCalSelectedCount');
      const list = document.getElementById('dashCalDayEvents');

      if (label) {
        label.textContent = result.date_label || result.date;
      }

      if (count) {
        count.textContent = `${result.appointments?.length || 0} Termine`;
      }

      if (!list) {
        return;
      }

      const appointments = result.appointments || [];

      if (!appointments.length) {
        list.innerHTML = '<div class="empty-state">Keine Termine an diesem Tag.</div>';
        return;
      }

      list.innerHTML = appointments.map(item => {
        const employees = Array.isArray(item.employees) ? item.employees : [];

        const employeeImages = employees.length
          ? `
                    <span class="dash-cal-employee-stack">
                        ${employees.slice(0, 4).map(employee => `
                            <img src="${escapeHtml(employee.image_url || '/images/gender/male.png')}"
                                 alt="${escapeHtml(employee.name || 'Mitarbeiter')}"
                                 title="${escapeHtml(employee.name || 'Mitarbeiter')}">
                        `).join('')}
                        ${employees.length > 4 ? `<span class="dash-cal-more">+${employees.length - 4}</span>` : ''}
                    </span>
                `
          : `
                    <span class="dash-cal-employee-stack">
                        <img src="/images/gender/male.png" alt="Mitarbeiter">
                    </span>
                `;

        return `
                <button type="button"
                        class="dash-cal-event"
                        data-cal-appointment-id="${item.id}">

                    <span class="dash-cal-event-time">
                        ${escapeHtml(item.start_time || '–')}
                    </span>

                    <span class="dash-cal-event-main">
                        <span class="dash-cal-event-title">
                            ${escapeHtml(item.title || 'Termin')}
                        </span>

                        <span class="dash-cal-event-meta">
                            ${escapeHtml(item.customer || 'Kein Kunde')}
                            ${item.address ? ' · ' + escapeHtml(item.address) : ''}
                        </span>

                        ${employeeImages}
                    </span>

                    <span class="dash-cal-badges">
                        ${item.is_ticket ? '<span class="dash-cal-badge ticket">Ticket</span>' : ''}
                        ${item.has_report ? '<span class="dash-cal-badge report">Bericht</span>' : '<span class="dash-cal-badge missing">Kein Bericht</span>'}
                    </span>
                </button>
            `;
      }).join('');

      if (window.lucide) {
        lucide.createIcons();
      }
    }

    function highlightSelectedCalendarDay(date) {
      document.querySelectorAll('.dash-cal-day').forEach(day => {
        day.classList.toggle('is-selected', day.dataset.calDate === date);
      });
    }

    async function openCalendarAppointmentModal(appointmentId) {
      const routes = dashCalRoutes();
      const modal = document.getElementById('dashCalAppointmentModal');
      const body = document.getElementById('dashCalModalBody');
      const title = document.getElementById('dashCalModalTitle');
      const subtitle = document.getElementById('dashCalModalSubtitle');

      if (!modal || !body) {
        console.error('Calendar modal partial missing. Include calendar-appointment-modal partial once.');
        showToast?.('Termin-Modal wurde nicht gefunden.');
        return;
      }

      if (!routes.show) {
        console.error('Calendar show route missing.');
        showToast?.('Termin-Route fehlt.');
        return;
      }

      modal.classList.add('show');
      body.innerHTML = '<div class="empty-state">Termin wird geladen...</div>';

      try {
        const response = await fetch(`${routes.show}/${appointmentId}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
          throw result;
        }

        const appointment = result.appointment || {};

        if (title) {
          title.textContent = appointment.title || 'Termin';
        }

        if (subtitle) {
          subtitle.textContent = `${appointment.start_date || ''} ${appointment.start_time || ''} · ${appointment.status || 'Status offen'}`;
        }

        body.innerHTML = renderCalendarAppointmentDetails(result);

        if (window.lucide) {
          lucide.createIcons();
        }
      } catch (error) {
        console.error('Appointment modal load failed:', error);

        body.innerHTML = `
                <div class="empty-state">
                    Termin konnte nicht geladen werden.
                </div>
            `;

        showToast?.(error?.message || 'Termin konnte nicht geladen werden.');
      }
    }

    function renderCalendarAppointmentDetails(result) {
      const a = result.appointment || {};
      const customer = result.customer;
      const object = result.object;
      const product = result.product;
      const leadProduct = result.lead_product;
      const ticket = result.ticket;

      return `
              <div class="dash-cal-modal-grid">
                  <div class="dash-cal-detail-card">
                      <h4>Termindetails</h4>
                      ${detailRow('Zeit', `${a.start_time || '–'} - ${a.end_time || '–'}`)}
                      ${detailRow('Typ', a.appointment_type || a.type || '–')}
                      ${detailRow('Ausführung', a.execution_type || '–')}
                      ${detailRow('Status', a.status || '–')}
                      ${detailRow('Priorität', a.priority || '–')}
                      ${detailRow('Bericht', a.has_report ? 'Vorhanden' : 'Fehlt')}
                      ${detailRow('Adresse', a.full_address || '–')}
                  </div>

                  <div class="dash-cal-map">
                      <div>
                          <i data-lucide="map-pin"></i>
                          <strong style="display:block;margin-top:.5rem;">Karte / Standort</strong>
                          <span style="display:block;color:var(--color-text-muted);font-weight:800;">
                              ${escapeHtml(a.full_address || 'Keine Adresse')}
                          </span>
                          ${a.map_url ? `<a href="${escapeHtml(a.map_url)}" target="_blank"><i data-lucide="external-link"></i> In Google Maps öffnen</a>` : ''}
                      </div>
                  </div>

                  <div class="dash-cal-detail-card">
                      <h4>Kunde</h4>
                      ${detailRow('Name', customer?.name || '–')}
                      ${detailRow('Kundennr.', customer?.customer_no || '–')}
                      ${detailRow('Telefon', customer?.phone || '–')}
                      ${detailRow('E-Mail', customer?.email || '–')}
                      ${detailRow('Adresse', customer?.address || '–')}
                  </div>

                  <div class="dash-cal-detail-card">
                      <h4>Objekt / Produkt</h4>
                      ${detailRow('Objekt', object?.name || '–')}
                      ${detailRow('Objekt-Adresse', object?.address || '–')}
                      ${detailRow('Produkt', product?.name || '–')}
                      ${detailRow('Lead Status', leadProduct?.status || leadProduct?.work_status || leadProduct?.stage || '–')}
                      ${detailRow('Abteilung', leadProduct?.department || '–')}
                  </div>

                  <div class="dash-cal-detail-card">
                      <h4>Ticket</h4>
                      ${ticket ? `
                          ${detailRow('Ticket-Nr.', ticket.ticket_no || '–')}
                          ${detailRow('Status', ticket.status || '–')}
                          ${detailRow('Priorität', ticket.priority || '–')}
                          ${detailRow('Problem', ticket.problem || '–')}
                          ${detailRow('Verantwortlich', ticket.responsible?.name || '–')}
                      ` : '<div class="empty-state">Kein Ticket verknüpft.</div>'}
                  </div>

                  <div class="dash-cal-detail-card">
                      <h4>Berichte</h4>
                      ${(result.reports || []).length ? (result.reports || []).map(report => `
                          <div style="padding:.65rem 0;border-bottom:1px solid var(--color-border);">
                              <strong style="display:block;font-size:.82rem;">${escapeHtml(report.type || 'Bericht')} · ${escapeHtml(report.report_date || '')}</strong>
                              <p style="margin:.25rem 0 0;font-size:.76rem;font-weight:800;color:var(--color-text-muted);">
                                  ${escapeHtml(report.report || '')}
                              </p>
                          </div>
                      `).join('') : '<div class="empty-state">Kein Bericht vorhanden.</div>'}
                  </div>
              </div>
          `;
    }

    function detailRow(label, value) {
      return `
              <div class="dash-cal-detail-row">
                  <span>${escapeHtml(label)}</span>
                  <strong>${escapeHtml(value ?? '–')}</strong>
              </div>
          `;
    }

    document.addEventListener('click', function (event) {
      const day = event.target.closest('[data-cal-date]');
      if (day) {
        loadCalendarDay(day.dataset.calDate);
        return;
      }

      const eventBtn = event.target.closest('[data-cal-appointment-id]');
      if (eventBtn) {
        openCalendarAppointmentModal(eventBtn.dataset.calAppointmentId);
        return;
      }

      if (
        event.target.closest('#dashCalModalCloseBtn') ||
        event.target.id === 'dashCalAppointmentModal'
      ) {
        document.getElementById('dashCalAppointmentModal')?.classList.remove('show');
      }

      if (event.target.closest('[data-cal-prev]')) {
        const d = new Date(calendarWidgetState.month + '-01T00:00:00');
        d.setMonth(d.getMonth() - 1);

        calendarWidgetState.month = d.toISOString().slice(0, 7);
        calendarWidgetState.selectedDate = `${calendarWidgetState.month}-01`;

        loadCalendarMonth().then(() => loadCalendarDay(calendarWidgetState.selectedDate));
      }

      if (event.target.closest('[data-cal-next]')) {
        const d = new Date(calendarWidgetState.month + '-01T00:00:00');
        d.setMonth(d.getMonth() + 1);

        calendarWidgetState.month = d.toISOString().slice(0, 7);
        calendarWidgetState.selectedDate = `${calendarWidgetState.month}-01`;

        loadCalendarMonth().then(() => loadCalendarDay(calendarWidgetState.selectedDate));
      }
    });

    document.addEventListener('change', function (event) {
      if (event.target && event.target.id === 'dashCalEmployeeSelect') {
        calendarWidgetState.employeeId = event.target.value;
        loadCalendarMonth();
        loadCalendarDay(calendarWidgetState.selectedDate);
      }
    });

    document.addEventListener('DOMContentLoaded', function () {
      initEmployeeCalendarWidget();
    });

    const weatherCodeMap = {
      0: ['Sonnig', 'sun'],
      1: ['Überwiegend klar', 'sun'],
      2: ['Teilweise bewölkt', 'cloud-sun'],
      3: ['Bewölkt', 'cloud'],
      45: ['Nebel', 'cloud-fog'],
      48: ['Nebel', 'cloud-fog'],
      51: ['Leichter Nieselregen', 'cloud-drizzle'],
      53: ['Nieselregen', 'cloud-drizzle'],
      55: ['Starker Nieselregen', 'cloud-drizzle'],
      61: ['Leichter Regen', 'cloud-rain'],
      63: ['Regen', 'cloud-rain'],
      65: ['Starker Regen', 'cloud-rain'],
      71: ['Leichter Schnee', 'cloud-snow'],
      73: ['Schnee', 'cloud-snow'],
      75: ['Starker Schnee', 'cloud-snow'],
      80: ['Regenschauer', 'cloud-rain'],
      81: ['Regenschauer', 'cloud-rain'],
      82: ['Starke Schauer', 'cloud-rain'],
      95: ['Gewitter', 'cloud-lightning'],
      96: ['Gewitter mit Hagel', 'cloud-lightning'],
      99: ['Starkes Gewitter', 'cloud-lightning']
    };

    function initTodayWeatherWidget(force = false) {
      const card = document.getElementById('todayWeatherCard');

      if (!card) {
        return;
      }

      if (card.dataset.loaded === '1' && !force) {
        return;
      }

      loadTodayWeather();
    }

    async function loadTodayWeather() {
      const refreshBtn = document.getElementById('refreshWeatherBtn');

      try {
        refreshBtn?.classList.add('is-loading');

        if (!navigator.geolocation) {
          throw new Error('Geolocation wird von diesem Browser nicht unterstützt.');
        }

        const position = await new Promise((resolve, reject) => {
          navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            timeout: 9000,
            maximumAge: 1000 * 60 * 10
          });
        });

        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        const weatherUrl = new URL('https://api.open-meteo.com/v1/forecast');
        weatherUrl.searchParams.set('latitude', lat);
        weatherUrl.searchParams.set('longitude', lon);
        weatherUrl.searchParams.set('current', [
          'temperature_2m',
          'relative_humidity_2m',
          'precipitation',
          'weather_code',
          'wind_speed_10m'
        ].join(','));
        weatherUrl.searchParams.set('timezone', 'auto');

        const weatherResponse = await fetch(weatherUrl.toString());
        const weather = await weatherResponse.json();

        const locationName = await getWeatherLocationName(lat, lon);

        renderTodayWeather(weather, locationName);

        const card = document.getElementById('todayWeatherCard');
        if (card) {
          card.dataset.loaded = '1';
        }
      } catch (error) {
        console.error('Weather load failed:', error);

        setWeatherText('weatherLocation', 'Standort nicht verfügbar');
        setWeatherText('weatherUpdated', 'Bitte Standortfreigabe erlauben');
        setWeatherText('weatherDescription', error.message || 'Wetter konnte nicht geladen werden');
      } finally {
        refreshBtn?.classList.remove('is-loading');

        if (window.lucide) {
          lucide.createIcons();
        }
      }
    }

    async function getWeatherLocationName(lat, lon) {
        const safeLat = Number(lat);
        const safeLon = Number(lon);

        if (!Number.isFinite(safeLat) || !Number.isFinite(safeLon)) {
          return 'Aktueller Standort';
        }

        return `Aktueller Standort · ${safeLat.toFixed(3)}, ${safeLon.toFixed(3)}`;
      }

    function renderTodayWeather(weather, locationName) {
      const current = weather.current || {};
      const code = Number(current.weather_code ?? 2);
      const weatherInfo = weatherCodeMap[code] || ['Wetter', 'cloud-sun'];

      setWeatherText('weatherLocation', locationName || 'Aktueller Standort');
      setWeatherText('weatherTemp', Math.round(Number(current.temperature_2m || 0)));
      setWeatherText('weatherDescription', weatherInfo[0]);
      setWeatherText('weatherWind', `${Math.round(Number(current.wind_speed_10m || 0))} km/h`);
      setWeatherText('weatherRain', `${Number(current.precipitation || 0).toFixed(1)} mm`);
      setWeatherText('weatherHumidity', `${Math.round(Number(current.relative_humidity_2m || 0))}%`);

      const updated = current.time
        ? new Date(current.time).toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' })
        : 'Jetzt';

      setWeatherText('weatherUpdated', `Aktualisiert ${updated}`);

      const iconWrap = document.getElementById('weatherIconWrap');

      if (iconWrap) {
        iconWrap.innerHTML = `<i data-lucide="${weatherInfo[1]}"></i>`;
      }

      if (window.lucide) {
        lucide.createIcons();
      }
    }

    function setWeatherText(id, value) {
      const el = document.getElementById(id);

      if (el) {
        el.textContent = value;
      }
    }

    document.addEventListener('click', function (event) {
      if (event.target.closest('#refreshWeatherBtn')) {
        loadTodayWeather();
      }
    });

    document.addEventListener('DOMContentLoaded', function () {
      initTodayWeatherWidget();
    });

    window.initTodayWeatherWidget = initTodayWeatherWidget;


    window.initEmployeeCalendarWidget = initEmployeeCalendarWidget;

    const dashboardMenuBtn = document.getElementById('dashboardMenuBtn');
    const dashboardMenu = document.getElementById('dashboardMenu');
    const dashboardMenuCloseBtn = document.getElementById('dashboardMenuCloseBtn');
    const dashboardMenuWrap = document.getElementById('dashboardMenuWrap');

    function closeDashboardMenu() {
      dashboardMenuWrap?.classList.remove('open');
      dashboardMenuBtn?.setAttribute('aria-expanded', 'false');
    }

    dashboardMenuBtn?.addEventListener('click', function (event) {
      event.stopPropagation();

      const isOpen = dashboardMenuWrap.classList.toggle('open');
      dashboardMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

      if (window.lucide) {
        lucide.createIcons();
      }
    });

    dashboardMenuCloseBtn?.addEventListener('click', function (event) {
      event.stopPropagation();
      closeDashboardMenu();
    });

    dashboardMenu?.addEventListener('click', function (event) {
      event.stopPropagation();
    });

    document.addEventListener('click', closeDashboardMenu);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeDashboardMenu();
      }
    });

  </script>
@endsection