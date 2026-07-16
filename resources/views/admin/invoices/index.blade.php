{{-- resources/views/admin/invoices/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Rechnungen & Aufträge')

@section('style')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    .inv-wrap {
      --inv-bg: #f6f8fb;
      --inv-surface: #ffffff;
      --inv-border: #e2e8f0;
      --inv-text: #0f172a;
      --inv-muted: #64748b;

      --inv-primary: var(--sa-accent);
      --inv-primary-h: #5fa2c6;

      --inv-success: #16a34a;
      --inv-success-h: #15803d;

      --inv-danger: #dc2626;
      --inv-danger-h: #b91c1c;

      --inv-canvas: #7b2d73;
      --inv-canvas-soft: rgba(123, 45, 115, .12);
      --inv-canvas-border: rgba(123, 45, 115, .28);

      --inv-light: #f8fafc;

      font-family: 'Outfit', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      padding: 20px;
      background: var(--inv-bg);
      color: var(--inv-text);
    }

    .inv-wrap * {
      box-sizing: border-box
    }

    .inv-container {
      margin: 0 auto
    }

    .inv-flex {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap
    }

    .inv-gap-2 {
      gap: .5rem
    }

    .inv-mb-2 {
      margin-bottom: .5rem
    }

    .inv-mb-4 {
      margin-bottom: 1.5rem
    }

    .inv-muted {
      color: var(--inv-muted)
    }

    .inv-small {
      font-size: .875rem
    }

    .inv-fw-700 {
      font-weight: 700
    }

    .inv-fw-600 {
      font-weight: 600
    }

    .inv-right {
      text-align: right
    }

    .inv-center {
      text-align: center
    }

    .inv-wrap h3 {
      font-size: 1.5rem;
      font-weight: 800;
      margin: 0;
      color: var(--inv-text)
    }

    .inv-stats {
      display: grid;
      grid-template-columns: repeat(5, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 1.25rem;
    }

    @media(max-width:1200px) {
      .inv-stats {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr))
      }
    }

    .inv-stat {
      background: var(--inv-surface);
      border: 1px solid rgba(226, 232, 240, .9);
      border-radius: 16px;
      padding: 1.25rem;
      box-shadow: 0 10px 30px rgba(2, 6, 23, .06);
      transition: transform .2s, box-shadow .2s;
    }

    .inv-stat:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 35px rgba(2, 6, 23, .09)
    }

    .inv-stat-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1rem;
      margin-bottom: .75rem
    }

    .inv-stat-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.15rem;
      background: rgba(116, 178, 212, .18);
      color: var(--inv-primary);
    }

    .inv-stat-val {
      font-size: 1.65rem;
      font-weight: 900;
      line-height: 1;
      color: var(--inv-text);
      margin-bottom: .25rem
    }

    .inv-stat-lbl {
      font-size: .85rem;
      color: var(--inv-muted);
      font-weight: 700
    }

    .inv-main {
      background: var(--inv-surface);
      border-radius: 20px;
      border: 1px solid rgba(226, 232, 240, .9);
      overflow: hidden;
      box-shadow: 0 10px 35px rgba(2, 6, 23, .06);
    }

    .inv-filter {
      background: var(--inv-light);
      border-bottom: 1px solid var(--inv-border);
      padding: 1rem 1.25rem;
    }

    .inv-filter-row {
      display: flex;
      align-items: end;
      gap: .75rem;
      flex-wrap: nowrap;
      overflow-x: auto;
      padding-bottom: .25rem;
    }

    .inv-group {
      display: flex;
      flex-direction: column;
      min-width: 0
    }

    .inv-label {
      font-size: .72rem;
      text-transform: uppercase;
      letter-spacing: .5px;
      font-weight: 900;
      color: #94a3b8;
      margin-bottom: 6px;
      white-space: nowrap;
    }

    .inv-input-wrap {
      position: relative;
      display: flex;
      align-items: center
    }

    .inv-ico {
      position: absolute;
      left: .9rem;
      color: var(--inv-muted);
      pointer-events: none
    }

    .inv-control,
    .inv-select {
      width: 100%;
      border: 1px solid var(--inv-border);
      border-radius: 10px;
      padding: .6rem .95rem;
      font-size: .95rem;
      font-family: inherit;
      background: #fff;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
      height: 42px;
      color: var(--inv-text);
    }

    .inv-has-ico {
      padding-left: 2.4rem
    }

    .inv-control:focus,
    .inv-select:focus {
      border-color: var(--inv-primary);
      box-shadow: 0 0 0 3px rgba(116, 178, 212, .22);
    }

    .inv-w-search {
      min-width: 340px;
      flex: 1 1 420px
    }

    .inv-w-type {
      min-width: 190px
    }

    .inv-w-status {
      min-width: 170px
    }

    .inv-w-from,
    .inv-w-to {
      min-width: 160px
    }

    .inv-w-per {
      min-width: 140px
    }

    .inv-w-btn {
      min-width: 150px
    }

    .inv-btn {
      border: none;
      border-radius: 10px;
      padding: .55rem 1rem;
      font-weight: 800;
      cursor: pointer;
      font-family: inherit;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: transform .15s, filter .15s, background .15s, color .15s, border-color .15s;
      text-decoration: none;
      height: 42px;
      font-size: .95rem;
      white-space: nowrap;
      user-select: none;
    }

    .inv-btn:active {
      transform: translateY(1px)
    }

    .inv-btn-sm {
      height: 38px;
      padding: .45rem .85rem;
      font-size: .9rem;
      border-radius: 10px
    }

    .inv-btn-primary {
      background: var(--inv-primary);
      color: #fff
    }

    .inv-btn-primary:hover {
      background: var(--inv-primary-h)
    }

    .inv-btn-success {
      background: var(--inv-success);
      color: #fff
    }

    .inv-btn-success:hover {
      background: var(--inv-success-h)
    }

    .inv-btn-danger {
      background: var(--inv-danger);
      color: #fff
    }

    .inv-btn-danger:hover {
      background: var(--inv-danger-h)
    }

    .inv-btn-light {
      background: #fff;
      border: 1px solid var(--inv-border);
      color: var(--inv-text);
    }

    .inv-btn-light:hover {
      background: #f1f5f9
    }

    .inv-btn-canvas {
      background: #fff;
      border: 1px solid var(--inv-canvas-border);
      color: var(--inv-canvas);
    }

    .inv-btn-canvas:hover {
      background: var(--inv-canvas-soft);
      border-color: var(--inv-canvas);
      color: var(--inv-canvas);
    }

    .inv-btn-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: var(--inv-muted);
      background: #fff;
      border: 1px solid var(--inv-border);
      cursor: pointer;
      transition: all .15s;
      text-decoration: none;
    }

    .inv-btn-icon:hover {
      border-color: var(--inv-primary);
      color: var(--inv-primary);
      background: rgba(116, 178, 212, .10)
    }

    .inv-btn-icon.inv-canvas {
      color: var(--inv-canvas);
      border-color: var(--inv-canvas-border);
      background: #fff;
    }

    .inv-btn-icon.inv-canvas:hover {
      color: var(--inv-canvas);
      border-color: var(--inv-canvas);
      background: var(--inv-canvas-soft);
    }

    .inv-table-wrap {
      overflow-x: auto;
      width: 100%
    }

    .inv-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 980px
    }

    .inv-table th {
      background: #fff;
      color: var(--inv-muted);
      font-weight: 900;
      font-size: .78rem;
      text-transform: uppercase;
      letter-spacing: .5px;
      padding: 1.1rem 1.25rem;
      border-bottom: 2px solid var(--inv-border);
      text-align: left;
      white-space: nowrap;
    }

    .inv-table td {
      padding: 1.05rem 1.25rem;
      vertical-align: middle;
      border-bottom: 1px solid var(--inv-border);
      background: #fff;
      color: var(--inv-text);
    }

    .inv-row {
      cursor: pointer
    }

    .inv-row:hover td {
      background: #fbfdff
    }

    .inv-sort {
      cursor: pointer;
      user-select: none
    }

    .inv-sort i {
      margin-left: 8px;
      opacity: .55
    }

    .inv-footer {
      padding: 1.1rem 1.25rem;
      border-top: 1px solid var(--inv-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
      background: #fff;
    }

    .inv-badge {
      padding: .38em .75em;
      border-radius: 999px;
      font-size: .75rem;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      border: 1px solid rgba(226, 232, 240, .9);
      white-space: nowrap;
    }

    .inv-b-draft {
      background: rgba(116, 178, 212, .18);
      color: #0b4e68
    }

    .inv-b-sent {
      background: rgba(2, 132, 199, .10);
      color: #075985
    }

    .inv-b-paid {
      background: rgba(22, 163, 74, .12);
      color: #166534
    }

    .inv-b-overdue {
      background: rgba(220, 38, 38, .10);
      color: #991b1b
    }

    .inv-b-cancelled {
      background: rgba(100, 116, 139, .12);
      color: #334155
    }

    .inv-hide {
      display: none !important
    }

    .inv-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(2, 6, 23, .35);
      z-index: 9998;
      opacity: 0;
      visibility: hidden;
      transition: opacity .2s;
    }

    .inv-backdrop.active {
      opacity: 1;
      visibility: visible
    }

    .inv-drawer {
      position: fixed;
      top: 0;
      right: 0;
      width: 100%;
      max-width: 720px;
      height: 100%;
      background: #fff;
      z-index: 9999;
      box-shadow: -10px 0 40px rgba(2, 6, 23, .18);
      transform: translateX(100%);
      transition: transform .25s ease-in-out;
      display: flex;
      flex-direction: column;
    }

    .inv-drawer.active {
      transform: translateX(0)
    }

    .inv-drawer-head {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--inv-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      background: linear-gradient(135deg, rgba(116, 178, 212, .18), rgba(22, 163, 74, .08));
    }

    .inv-drawer-body {
      padding: 1.5rem;
      overflow: auto;
      flex: 1
    }

    .inv-close {
      background: none;
      border: none;
      font-size: 1.25rem;
      color: var(--inv-muted);
      cursor: pointer
    }

    .inv-steps {
      display: flex;
      gap: .5rem;
      margin-bottom: 1rem;
      align-items: center
    }

    .inv-step-pill {
      font-size: 12px;
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid var(--inv-border);
      background: #fff;
      color: var(--inv-muted);
      font-weight: 900;
      letter-spacing: .04em;
      text-transform: uppercase;
      cursor: pointer;
    }

    .inv-step-pill.active {
      background: rgba(116, 178, 212, .18);
      border-color: rgba(116, 178, 212, .35);
      color: #0b4e68
    }

    .inv-step-pill.meta {
      margin-left: auto;
      cursor: default
    }

    .inv-section {
      border: 1px solid var(--inv-border);
      border-radius: 14px;
      padding: 12px;
      background: #fff;
      margin-bottom: 1rem;
    }

    .inv-grid {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: .75rem
    }

    .inv-col-12 {
      grid-column: span 12
    }

    .inv-col-9 {
      grid-column: span 9
    }

    .inv-col-6 {
      grid-column: span 6
    }

    .inv-col-4 {
      grid-column: span 4
    }

    .inv-col-3 {
      grid-column: span 3
    }

    @media(max-width:720px) {

      .inv-col-9,
      .inv-col-6,
      .inv-col-4,
      .inv-col-3 {
        grid-column: span 12
      }
    }

    .inv-wrap .select2-container {
      width: 100% !important;
      min-width: 0 !important
    }

    .inv-wrap .select2-container--default .select2-selection--single,
    .inv-wrap .select2-container--default .select2-selection--multiple {
      height: 42px;
      border: 1px solid var(--inv-border);
      border-radius: 10px;
      display: flex;
      align-items: center;
      padding-left: 8px;
      background: #fff;
    }

    .inv-wrap .select2-container--default.select2-container--focus .select2-selection--single,
    .inv-wrap .select2-container--default.select2-container--focus .select2-selection--multiple {
      border-color: var(--inv-primary);
      box-shadow: 0 0 0 3px rgba(116, 178, 212, .22);
    }

    .inv-wrap .select2-container--open {
      z-index: 10050 !important
    }

    .inv-wrap .select2-dropdown {
      z-index: 10050 !important
    }

    .inv-items-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 700px
    }

    .inv-items-table th,
    .inv-items-table td {
      padding: .75rem;
      border-bottom: 1px solid var(--inv-border)
    }

    .inv-items-table th {
      font-size: .75rem;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: .05em;
      font-weight: 900
    }

    .inv-total-bar {
      display: flex;
      justify-content: flex-end;
      gap: 1.25rem;
      margin-top: .75rem;
      flex-wrap: wrap
    }

    .inv-total-bar b {
      font-weight: 900
    }

    .inv-mode {
      display: flex;
      gap: .5rem;
      flex-wrap: wrap;
      align-items: center;
      background: #f8fafc;
      border: 1px solid var(--inv-border);
      border-radius: 14px;
      padding: .6rem;
    }

    .inv-mode input {
      display: none
    }

    .inv-mode label {
      padding: .45rem .75rem;
      border-radius: 999px;
      border: 1px solid var(--inv-border);
      background: #fff;
      font-weight: 900;
      font-size: .85rem;
      color: var(--inv-muted);
      cursor: pointer;
      user-select: none;
      display: inline-flex;
      align-items: center;
      gap: .45rem;
    }

    .inv-mode input:checked+label {
      background: rgba(116, 178, 212, .18);
      border-color: rgba(116, 178, 212, .35);
      color: #0b4e68;
    }

    .inv-drop {
      border: 2px dashed rgba(116, 178, 212, .45);
      background: rgba(116, 178, 212, .12);
      border-radius: 14px;
      padding: 1rem;
      text-align: center;
    }

    .inv-file-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .75rem;
      margin-top: .75rem;
    }

    @media(max-width:520px) {
      .inv-file-grid {
        grid-template-columns: 1fr
      }
    }

    .inv-file-card {
      border: 1px solid var(--inv-border);
      border-radius: 14px;
      padding: .9rem;
      display: flex;
      gap: .75rem;
      align-items: flex-start;
      background: #fff;
      cursor: pointer;
      transition: box-shadow .15s, transform .15s;
    }

    .inv-file-card:hover {
      box-shadow: 0 10px 25px rgba(2, 6, 23, .08);
      transform: translateY(-1px)
    }

    .inv-file-ico {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(220, 38, 38, .10);
      color: var(--inv-danger);
      flex: 0 0 auto;
    }

    .inv-trunc {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis
    }

    .inv-modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(2, 6, 23, .55);
      z-index: 10060;
      opacity: 0;
      visibility: hidden;
      transition: opacity .18s;
    }

    .inv-modal-backdrop.active {
      opacity: 1;
      visibility: visible
    }

    .inv-pdf-modal {
      position: fixed;
      inset: 5vh 4vw;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 30px 80px rgba(2, 6, 23, .35);
      z-index: 10061;
      display: flex;
      flex-direction: column;
      transform: scale(.98);
      opacity: 0;
      visibility: hidden;
      transition: opacity .18s, transform .18s;
      overflow: hidden;
    }

    .inv-pdf-modal.active {
      opacity: 1;
      visibility: visible;
      transform: scale(1)
    }

    .inv-pdf-head {
      padding: .9rem 1rem;
      border-bottom: 1px solid var(--inv-border);
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }

    .inv-pdf-title {
      font-weight: 900;
      min-width: 0;
      display: flex;
      gap: .6rem;
      align-items: center;
    }

    .inv-pdf-title span {
      min-width: 0
    }

    .inv-pdf-body {
      flex: 1;
      background: #0b1220
    }

    .inv-pdf-body iframe {
      width: 100%;
      height: 100%;
      border: 0;
      background: #0b1220
    }

    .inv-acc {
      display: flex;
      flex-direction: column;
      gap: .75rem
    }

    .inv-acc details {
      border: 1px solid var(--inv-border);
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 10px 30px rgba(2, 6, 23, .05);
      overflow: hidden;
    }

    .inv-acc summary {
      list-style: none;
      cursor: pointer;
      padding: 1rem 1.1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }

    .inv-acc summary::-webkit-details-marker {
      display: none
    }

    .inv-acc-title {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: .7rem;
    }

    .inv-acc-title .inv-chev {
      width: 34px;
      height: 34px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--inv-border);
      color: var(--inv-muted);
      background: #fff;
    }

    details[open] .inv-acc-title .inv-chev {
      color: var(--inv-primary);
      border-color: rgba(116, 178, 212, .45);
      background: rgba(116, 178, 212, .10)
    }

    .inv-acc-name {
      font-weight: 900;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis
    }

    .inv-acc-sub {
      font-size: .85rem;
      color: var(--inv-muted);
      font-weight: 700;
      margin-top: 2px
    }

    .inv-acc-meta {
      display: flex;
      gap: .4rem;
      flex-wrap: wrap;
      justify-content: flex-end;
      align-items: center
    }

    .inv-pill {
      border: 1px solid rgba(226, 232, 240, .9);
      background: #fff;
      border-radius: 999px;
      padding: .35rem .65rem;
      font-size: .75rem;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      white-space: nowrap;
    }

    .inv-pill-ok {
      background: rgba(22, 163, 74, .10);
      color: #166534
    }

    .inv-pill-warn {
      background: rgba(220, 38, 38, .08);
      color: #991b1b
    }

    .inv-pill-info {
      background: rgba(2, 132, 199, .10);
      color: #075985
    }

    .inv-pill-muted {
      background: rgba(100, 116, 139, .10);
      color: #334155
    }

    .inv-acc-body {
      border-top: 1px solid var(--inv-border);
      padding: 1rem 1.1rem;
      background: #fbfdff
    }

    .inv-mini-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 980px
    }

    .inv-mini-table th {
      font-size: .75rem;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: .05em;
      font-weight: 900;
      padding: .75rem .75rem;
      border-bottom: 1px solid var(--inv-border);
      text-align: left;
      white-space: nowrap
    }

    .inv-mini-table td {
      padding: .75rem .75rem;
      border-bottom: 1px solid var(--inv-border);
      background: transparent;
      vertical-align: middle
    }

    .inv-mini-table tr:hover td {
      background: #f8fbff
    }

    .inv-compact-items .col-qty,
    .inv-compact-items .col-unit,
    .inv-compact-items .col-unitprice,
    .inv-compact-items .col-sum,
    .inv-compact-items .cell-qty,
    .inv-compact-items .cell-unit,
    .inv-compact-items .cell-unitprice,
    .inv-compact-items .cell-sum {
      display: none !important;
    }

    .inv-compact-items .col-price,
    .inv-compact-items .cell-price {
      display: table-cell !important;
    }

    .inv-status-quick {
      min-width: 140px;
      height: 34px;
      border: 1px solid var(--inv-border);
      border-radius: 999px;
      padding: .35rem 2rem .35rem .8rem;
      font-size: .82rem;
      font-weight: 800;
      font-family: inherit;
      background: #fff;
      color: var(--inv-text);
      outline: none;
      cursor: pointer;
      transition: border-color .15s, box-shadow .15s, background .15s;
    }

    .inv-status-quick:focus {
      border-color: var(--inv-primary);
      box-shadow: 0 0 0 3px rgba(116, 178, 212, .18);
    }

    .inv-status-quick.is-saving {
      opacity: .7;
      pointer-events: none;
    }

    .inv-status-wrap {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
    }

    .inv-status-dot {
      width: 10px;
      height: 10px;
      border-radius: 999px;
      display: inline-block;
    }

    .inv-status-dot.draft {
      background: #74b2d4;
    }

    .inv-status-dot.sent {
      background: #0284c7;
    }

    .inv-status-dot.paid {
      background: #16a34a;
    }

    .inv-status-dot.overdue {
      background: #dc2626;
    }

    .inv-status-dot.cancelled {
      background: #64748b;
    }

    .inv-kanban-board {
      display: flex;
      gap: 1rem;
      overflow-x: auto;
      padding: 1.25rem;
      min-height: 620px;
      background: #fbfdff;
    }

    .inv-kanban-col {
      flex: 0 0 310px;
      max-width: 310px;
      background: #f8fafc;
      border: 1px solid var(--inv-border);
      border-radius: 18px;
      display: flex;
      flex-direction: column;
      max-height: calc(100vh - 310px);
      min-height: 520px;
      overflow: hidden;
    }

    .inv-kanban-head {
      position: sticky;
      top: 0;
      z-index: 2;
      background: #fff;
      border-bottom: 1px solid var(--inv-border);
      padding: .9rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .75rem;
    }

    .inv-kanban-title {
      font-weight: 900;
      display: flex;
      align-items: center;
      gap: .55rem;
      min-width: 0;
    }

    .inv-kanban-count {
      min-width: 28px;
      height: 28px;
      padding: 0 .45rem;
      border-radius: 999px;
      background: #f1f5f9;
      border: 1px solid var(--inv-border);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 900;
      font-size: .8rem;
    }

    .inv-kanban-list {
      padding: .8rem;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: .75rem;
      flex: 1;
      min-height: 0;
    }

    .inv-kanban-card {
      background: #fff;
      border: 1px solid var(--inv-border);
      border-radius: 16px;
      padding: .85rem;
      box-shadow: 0 8px 22px rgba(2, 6, 23, .055);
      cursor: pointer;
    }

    .inv-kanban-card:hover {
      border-color: rgba(116, 178, 212, .65);
      box-shadow: 0 12px 28px rgba(2, 6, 23, .09);
    }

    .inv-kanban-no {
      font-weight: 900;
      color: var(--inv-text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .inv-kanban-meta {
      margin-top: .25rem;
      color: var(--inv-muted);
      font-size: .82rem;
      font-weight: 700;
      line-height: 1.35;
    }

    .inv-kanban-money {
      margin-top: .65rem;
      display: flex;
      justify-content: space-between;
      gap: .75rem;
      align-items: center;
      font-weight: 900;
    }

    .inv-kanban-actions {
      margin-top: .75rem;
      display: flex;
      justify-content: flex-end;
      gap: .45rem;
      flex-wrap: wrap;
    }

    .inv-kanban-card .inv-status-quick {
      width: 100%;
      min-width: 0;
      margin-top: .65rem;
    }

    @media(max-width:760px) {
      .inv-kanban-board {
        flex-direction: column;
        overflow-x: hidden;
        padding: .85rem;
      }

      .inv-kanban-col {
        flex: 0 0 auto;
        max-width: 100%;
        width: 100%;
        max-height: 520px;
      }
    }

    .inv-deal-box {
      border: 1px solid rgba(116, 178, 212, .35);
      background: rgba(116, 178, 212, .08);
      border-radius: 14px;
      padding: .75rem;
      margin-top: .75rem;
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: .6rem;
    }

    @media(max-width:720px) {
      .inv-deal-box {
        grid-template-columns: 1fr
      }
    }

    .inv-deal-mini {
      background: #fff;
      border: 1px solid var(--inv-border);
      border-radius: 12px;
      padding: .65rem;
    }

    .inv-deal-mini span {
      display: block;
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: #94a3b8;
      font-weight: 900;
    }

    .inv-deal-mini b {
      display: block;
      margin-top: 3px;
      font-size: .95rem;
      color: var(--inv-text);
    }

    .inv-profile-modal {
      position: fixed;
      inset: 4vh 4vw;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 30px 90px rgba(2, 6, 23, .32);
      z-index: 10065;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      opacity: 0;
      visibility: hidden;
      transform: scale(.98);
      transition: .18s;
    }

    .inv-profile-modal.active {
      opacity: 1;
      visibility: visible;
      transform: scale(1)
    }

    .inv-profile-head {
      padding: 1rem 1.2rem;
      border-bottom: 1px solid var(--inv-border);
      background: linear-gradient(135deg, rgba(116, 178, 212, .18), rgba(147, 194, 28, .10));
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      align-items: center;
    }

    .inv-profile-body {
      padding: 1.2rem;
      overflow: auto;
      flex: 1;
      background: #f8fafc;
    }

    .inv-profile-grid {
      display: grid;
      grid-template-columns: 1.1fr .9fr;
      gap: 1rem;
    }

    @media(max-width:980px) {
      .inv-profile-grid {
        grid-template-columns: 1fr
      }

      .inv-profile-modal {
        inset: 2vh 2vw
      }
    }

    .inv-profile-card {
      border: 1px solid var(--inv-border);
      border-radius: 16px;
      background: #fff;
      padding: 1rem;
      box-shadow: 0 8px 25px rgba(2, 6, 23, .05)
    }

    .inv-history-line {
      border: 1px solid var(--inv-border);
      border-radius: 12px;
      padding: .75rem;
      background: #fff;
      margin-bottom: .6rem;
    }

    .inv-history-title {
      font-weight: 900;
      color: var(--inv-text);
      font-size: .9rem;
      display: flex;
      justify-content: space-between;
      gap: .8rem;
    }

    .inv-history-meta {
      font-size: .8rem;
      color: var(--inv-muted);
      font-weight: 700;
      line-height: 1.45;
      margin-top: .35rem;
      white-space: pre-wrap;
    }

    .inv-preview-img {
      max-width: 100%;
      max-height: 72vh;
      margin: auto;
      display: block;
      object-fit: contain;
      background: #0b1220;
    }

    #profileBackdrop {
      z-index: 10060 !important;
    }

    #profileModal {
      z-index: 10065 !important;
    }

    #pdfBackdrop {
      z-index: 10100 !important;
      background: rgba(2, 6, 23, .72) !important;
    }

    #pdfModal {
      z-index: 10101 !important;
      inset: 3vh 3vw;
    }

    #pdfModal .inv-pdf-body {
      position: relative;
      z-index: 1;
    }

    #pdfModal iframe,
    #pdfModal img {
      position: relative;
      z-index: 2;
    }
  </style>
@endsection

@section('content')
  @php
$types = [
  'Rechnung',
  'Teilrechnung',
  'Abschlagsrechnung',
  'Schlussrechnung',
  'Anzahlung',
  'Zahlungserinnerung',
  'Mahnung',
  'Gutschrift',
  'Stornorechnung',
  'Proforma',
  'Angebot',
  'Auftrag',
  'Lieferschein',
  'Quittung'
];

$invoiceIndexUrl = \Illuminate\Support\Facades\Route::has('admin.invoices.index')
  ? route('admin.invoices.index')
  : url('/admin/invoices');

$invoiceCanvasUrl = \Illuminate\Support\Facades\Route::has('invoices.canvas.edit')
  ? route('invoices.canvas.edit', ['invoice' => '__ID__'])
  : url('/invoices/canvas/__ID__');
  @endphp

  <div class="inv-wrap" id="invApp" data-list-url="{{ route('admin.invoices.list') }}"
    data-store-url="{{ route('admin.invoices.store') }}"
    data-show-url="{{ route('admin.invoices.show', ['invoice' => '__ID__']) }}"
    data-update-url="{{ route('admin.invoices.update', ['invoice' => '__ID__']) }}"
    data-destroy-url="{{ route('admin.invoices.destroy', ['invoice' => '__ID__']) }}"
    data-upload-url="{{ route('admin.invoices.files.upload', ['invoice' => '__ID__']) }}"
    data-delete-file-url="{{ route('admin.invoices.files.delete', ['file' => '__ID__']) }}"
    data-download-file-url="{{ route('admin.invoices.files.download', ['file' => '__ID__']) }}"
    data-view-file-url="{{ route('admin.invoices.files.view', ['file' => '__ID__']) }}"
    data-sel-customers="{{ route('admin.invoices.select.customers') }}"
    data-sel-objects="{{ route('admin.invoices.select.objects') }}"
    data-status-url="{{ route('admin.invoices.status', ['invoice' => '__ID__']) }}"
    data-canvas-url="{{ $invoiceCanvasUrl }}" data-sel-products="{{ route('admin.invoices.select.products') }}"
    data-sel-deals="{{ Route::has('admin.invoices.select.deals') ? route('admin.invoices.select.deals') : url('/admin/invoices/select/deals') }}"
    data-deal-items-url="{{ Route::has('admin.invoices.deals.items') ? route('admin.invoices.deals.items', ['deal' => '__ID__']) : url('/admin/invoices/deals/__ID__/items') }}">

    <div class="inv-container">
      <div class="inv-flex inv-mb-4">
        <div>
          <h3 class="inv-mb-2">Rechnungen & Aufträge</h3>
          <div class="inv-muted inv-small inv-fw-600">Klick auf eine Zeile öffnet rechts direkt die Galerie.</div>
        </div>

        <div class="inv-flex inv-gap-2" style="justify-content:flex-end">
          <button class="inv-btn inv-btn-light" id="inv-view-table" type="button">
            <i class="fa-solid fa-table"></i>&nbsp; Tabelle
          </button>
          <button class="inv-btn inv-btn-light" id="inv-view-cards" type="button">
            <i class="fa-solid fa-grip"></i>&nbsp; Karten
          </button>
          <button class="inv-btn inv-btn-light" id="inv-view-customers" type="button">
            <i class="fa-solid fa-users"></i>&nbsp; Kunden
          </button>
          <button class="inv-btn inv-btn-light" id="inv-view-kanban" type="button">
            <i class="fa-solid fa-columns"></i>&nbsp; Kanban
          </button>

          <button class="inv-btn inv-btn-canvas" id="inv-canvas-help" type="button">
            <i class="fa-solid fa-file-invoice"></i>&nbsp; Canvas
          </button>

          <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="inv-new" type="button">
            <i class="fa-solid fa-plus"></i>&nbsp; Neue Rechnung
          </button>
        </div>
      </div>

      <div class="inv-stats" id="inv-kpis"></div>

      <div class="inv-main">
        <div class="inv-filter">
          <div class="inv-filter-row">
            <div class="inv-group inv-w-search">
              <label class="inv-label">Suche</label>
              <div class="inv-input-wrap">
                <i class="fa-solid fa-magnifying-glass inv-ico"></i>
                <input id="inv-q" class="inv-control inv-has-ico" placeholder="Rechnungsnr, Kunde, Objekt, Typ..." />
              </div>
            </div>

            <div class="inv-group inv-w-type">
              <label class="inv-label">Typ</label>
              <select id="inv-type" class="inv-select">
                <option value="">Alle</option>
                @foreach($types as $t)
                  <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
              </select>
            </div>

            <div class="inv-group inv-w-status">
              <label class="inv-label">Status</label>
              <select id="inv-status" class="inv-select">
                <option value="">Alle</option>
                <option value="draft">Entwurf</option>
                <option value="sent">Gesendet</option>
                <option value="paid">Bezahlt</option>
                <option value="overdue">Überfällig</option>
                <option value="cancelled">Storniert</option>
              </select>
            </div>

            <div class="inv-group inv-w-from">
              <label class="inv-label">Von</label>
              <input id="inv-from" type="date" class="inv-control">
            </div>

            <div class="inv-group inv-w-to">
              <label class="inv-label">Bis</label>
              <input id="inv-to" type="date" class="inv-control">
            </div>

            <div class="inv-group inv-w-per">
              <label class="inv-label">Pro Seite</label>
              <select id="inv-per" class="inv-select">
                <option value="12">12</option>
                <option value="24">24</option>
                <option value="36">36</option>
                <option value="50">50</option>
              </select>
            </div>

            <div class="inv-group inv-w-btn">
              <label class="inv-label">&nbsp;</label>
              <button id="inv-reset" class="inv-btn inv-btn-light" type="button">
                <i class="fa-solid fa-rotate-left"></i>&nbsp; Zurücksetzen
              </button>
            </div>

            <div class="inv-group inv-w-btn">
              <label class="inv-label">&nbsp;</label>
              <button id="inv-refresh" class="inv-btn inv-btn-light" type="button">
                <i class="fa-solid fa-arrows-rotate"></i>&nbsp; Aktualisieren
              </button>
            </div>
          </div>
        </div>

        <div class="inv-table-wrap inv-hide" id="inv-table-wrap">
          <table class="inv-table">
            <thead>
              <tr>
                <th class="inv-sort" data-sort="issue_date" style="padding-left:1.5rem;">Datum <i
                    class="fa-solid fa-sort"></i></th>
                <th class="inv-sort" data-sort="invoice_no">Rechnungsnr <i class="fa-solid fa-sort"></i></th>
                <th>Kunde</th>
                <th>Objekt</th>
                <th class="inv-sort" data-sort="type">Typ <i class="fa-solid fa-sort"></i></th>
                <th class="inv-sort" data-sort="status">Status <i class="fa-solid fa-sort"></i></th>
                <th class="inv-sort inv-right" data-sort="total_amount">Betrag <i class="fa-solid fa-sort"></i></th>
                <th class="inv-right" style="padding-right:1.5rem;">Aktion</th>
              </tr>
            </thead>
            <tbody id="inv-tbody">
              <tr>
                <td colspan="8" class="inv-center" style="padding:2rem;color:var(--inv-muted);">Lade Daten...</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="inv-table-wrap inv-hide" id="inv-card-wrap" style="padding:1.25rem;">
          <div id="inv-cards" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;">
          </div>
        </div>

        <div class="inv-table-wrap" id="inv-customer-wrap" style="padding:1.25rem;">
          <div id="inv-customers"></div>
        </div>

        <div class="inv-table-wrap inv-hide" id="inv-kanban-wrap">
          <div id="inv-kanban" class="inv-kanban-board"></div>
        </div>

        <div class="inv-footer">
          <div class="inv-muted inv-small inv-fw-600" id="inv-meta">Lade Daten...</div>
          <div class="inv-flex inv-gap-2" style="justify-content:flex-end;">
            <button class="inv-btn inv-btn-light inv-btn-sm" id="inv-prev" type="button">Zurück</button>
            <button class="inv-btn inv-btn-light inv-btn-sm" id="inv-next" type="button">Weiter</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="inv-backdrop" id="invBackdrop"></div>
  <div class="inv-drawer" id="invDrawer">
    <div class="inv-drawer-head">
      <div>
        <div class="inv-fw-700" style="font-size:1.15rem;" id="inv-drawer-title">Neue Rechnung</div>
        <div class="inv-muted inv-small" id="inv-drawer-sub">Schritt 1: Daten</div>
      </div>
      <button class="inv-close" id="invDrawerClose" type="button"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="inv-drawer-body">
      <div class="inv-steps">
        <button class="inv-step-pill active" id="invStep1Pill" type="button">1) Daten</button>
        <button class="inv-step-pill" id="invStep2Pill" type="button">2) Dateien</button>
        <span class="inv-step-pill meta" id="invActiveId">—</span>
      </div>

      <div id="invStep1">
        <div class="inv-section">
          <div class="inv-grid">
            <div class="inv-col-6">
              <label class="inv-label">Kunde</label>
              <select id="mCustomer" class="inv-select"></select>
            </div>
            <div class="inv-col-6">
              <label class="inv-label">Objekt</label>
              <select id="mObject" class="inv-select"></select>
            </div>

            <div class="inv-col-12">
              <label class="inv-label">Auftrag / Deal verknüpfen</label>
              <select id="mDeal" class="inv-select"></select>
              <div class="inv-deal-box inv-hide" id="invDealBox">
                <div class="inv-deal-mini"><span>Auftragswert</span><b id="dealLimitText">0,00 €</b></div>
                <div class="inv-deal-mini"><span>Bereits berechnet</span><b id="dealInvoicedText">0,00 €</b></div>
                <div class="inv-deal-mini"><span>Noch verfügbar</span><b id="dealRemainingText">0,00 €</b></div>
                <div class="inv-deal-mini"><span>Diese Rechnung max.</span><b id="dealLimitHintText">0,00 €</b></div>
              </div>
            </div>

            <div class="inv-col-4">
              <label class="inv-label">Rechnungsnr</label>
              <input id="mInvoiceNo" class="inv-control" placeholder="Automatisch, wenn leer">
            </div>
            <div class="inv-col-4">
              <label class="inv-label">Typ</label>
              <select id="mType" class="inv-select">
                @foreach($types as $t)
                  <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="inv-col-4">
              <label class="inv-label">Status</label>
              <select id="mStatus" class="inv-select">
                <option value="draft">Entwurf</option>
                <option value="sent">Gesendet</option>
                <option value="paid">Bezahlt</option>
                <option value="overdue">Überfällig</option>
                <option value="cancelled">Storniert</option>
              </select>
            </div>

            <div class="inv-col-3">
              <label class="inv-label">Ausgestellt am</label>
              <input id="mIssueDate" type="date" class="inv-control">
            </div>
            <div class="inv-col-3">
              <label class="inv-label">Fällig am</label>
              <input id="mDueDate" type="date" class="inv-control">
            </div>
            <div class="inv-col-3">
              <label class="inv-label">Leistung von</label>
              <input id="mServiceFrom" type="date" class="inv-control">
            </div>
            <div class="inv-col-3">
              <label class="inv-label">Leistung bis</label>
              <input id="mServiceTo" type="date" class="inv-control">
            </div>

            <div class="inv-col-3">
              <label class="inv-label">MwSt (%)</label>
              <input id="mTaxRate" type="number" step="0.001" min="0" max="100" class="inv-control" value="0">
            </div>
            <div class="inv-col-9">
              <label class="inv-label">Notiz</label>
              <input id="mNotes" class="inv-control" placeholder="Optional...">
            </div>
          </div>
        </div>

        <div class="inv-section">
          <div class="inv-flex inv-gap-2" style="justify-content:space-between; align-items:flex-start;">
            <div>
              <div class="inv-fw-700"><i class="fa-solid fa-list"></i>&nbsp; Positionen</div>
              <div class="inv-muted inv-small inv-fw-600" style="margin-top:4px;">
                Modus wählen: Einzelpreise oder nur Gesamtbetrag.
              </div>
            </div>

            <div class="inv-flex inv-gap-2" style="justify-content:flex-end; margin-top:.6rem;">
              <label class="inv-pill inv-pill-muted" style="cursor:pointer;">
                <input type="checkbox" id="toggleCompactColumns" style="margin-right:.45rem;">
                Nur Preis (ohne Menge/Einheit/Summe)
              </label>
            </div>

            <div style="min-width: 320px;">
              <div class="inv-mode" id="priceMode">
                <input type="radio" name="price_mode" id="pm_items" value="items" checked>
                <label for="pm_items"><i class="fa-solid fa-receipt"></i> Einzelpreise</label>

                <input type="radio" name="price_mode" id="pm_total" value="total">
                <label for="pm_total"><i class="fa-solid fa-euro-sign"></i> Nur Gesamt</label>
              </div>
            </div>
          </div>

          <div id="totalOnlyBlock" class="inv-hide" style="margin-top:.9rem;">
            <div class="inv-grid">
              <div class="inv-col-9">
                <label class="inv-label">Titel (optional)</label>
                <input id="mTotalTitle" class="inv-control" placeholder="z.B. Pauschale / Gesamtbetrag">
              </div>
              <div class="inv-col-3">
                <label class="inv-label">Rechnungsbetrag</label>
                <input id="mTotalNet" type="number" step="0.01" min="0" class="inv-control" placeholder="0.00">
              </div>
            </div>

            <div class="inv-muted inv-small inv-fw-600" style="margin-top:.6rem;">
              Hinweis: Es wird automatisch eine Position mit Menge=1 und Einzelpreis=Rechnungsbetrag gespeichert.
            </div>
          </div>

          <div id="itemsBlock" style="margin-top:.9rem;">
            <div class="inv-flex inv-gap-2" style="justify-content:flex-end;">
              <select id="mProductPicker" class="inv-select" style="min-width:320px;"></select>
              <button class="inv-btn inv-btn-light" id="btnAddManualItem" type="button">
                <i class="fa-solid fa-plus"></i>&nbsp; Position
              </button>
            </div>

            <div class="inv-table-wrap" style="margin-top:.75rem;">
              <table class="inv-items-table">
                <thead>
                  <tr>
                    <th style="min-width:220px;">Titel</th>

                    <th class="col-qty" style="width:110px;">Menge</th>
                    <th class="col-unit" style="width:120px;">Einheit</th>

                    <th class="col-unitprice inv-right" style="width:150px;">Einzelpreis</th>
                    <th class="col-price inv-right" style="width:150px; display:none;">Preis</th>

                    <th class="col-sum inv-right" style="width:140px;">Summe</th>
                    <th class="inv-right" style="width:140px;"></th>
                  </tr>
                </thead>
                <tbody id="itemsBody">
                  <tr>
                    <td colspan="7" class="inv-center" style="padding:1.5rem;color:var(--inv-muted);">Keine Positionen
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="inv-total-bar">
            <div class="inv-muted inv-small inv-fw-600">Zwischensumme: <b><span id="sumSubtotal">0.00</span> €</b></div>
            <div class="inv-muted inv-small inv-fw-600">MwSt: <b><span id="sumTax">0.00</span> €</b></div>
            <div class="inv-fw-700">Gesamt: <b><span id="sumTotal">0.00</span> €</b></div>
          </div>
        </div>

        <div class="inv-flex inv-gap-2" style="justify-content:space-between;margin-top:1rem;">
          <div class="inv-muted inv-small inv-fw-600" id="saveHint"></div>
          <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="btnSaveStep1" type="button">
            <i class="fa-solid fa-floppy-disk"></i>&nbsp; Speichern & Weiter
          </button>
        </div>
      </div>

      <div id="invStep2" class="inv-hide">
        <div class="inv-drop" id="invDrop">
          <div class="inv-fw-700" style="font-size:1.05rem;">PDF-Upload</div>
          <div class="inv-muted inv-small inv-fw-600" style="margin-top:6px;">PDFs oder Bilder ablegen oder klicken.</div>

          <input type="file" id="mFiles" accept="application/pdf,image/*" multiple style="display:none;">

          <div class="inv-flex inv-gap-2" style="justify-content:center;margin-top:10px;">
            <button class="inv-btn inv-btn-light" id="btnPickFiles" type="button">
              <i class="fa-solid fa-paperclip"></i>&nbsp; Dateien wählen
            </button>
            <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="btnUploadFiles"
              type="button">
              <i class="fa-solid fa-cloud-arrow-up"></i>&nbsp; Hochladen
            </button>
          </div>

          <div id="pickedFilesHint" class="inv-muted inv-small inv-fw-600" style="margin-top:10px;"></div>
        </div>

        <div class="inv-section" style="margin-top:1rem;">
          <div class="inv-flex" style="justify-content:space-between;">
            <div class="inv-fw-700"><i class="fa-solid fa-folder-open"></i>&nbsp; Galerie</div>
            <button class="inv-btn inv-btn-light inv-btn-sm" id="btnReloadFiles" type="button">
              <i class="fa-solid fa-arrows-rotate"></i>&nbsp; Neu laden
            </button>
          </div>

          <div id="filesList" class="inv-file-grid"></div>
        </div>

        <div class="inv-flex inv-gap-2" style="justify-content:space-between;margin-top:1rem;">
          <button class="inv-btn inv-btn-light" id="btnBackToStep1" type="button">
            <i class="fa-solid fa-arrow-left"></i>&nbsp; Zurück
          </button>
          <button class="inv-btn inv-btn-primary" style="background:#93c21c !important" id="btnFinish" type="button">
            <i class="fa-solid fa-check"></i>&nbsp; Fertig
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="inv-modal-backdrop" id="pdfBackdrop"></div>
  <div class="inv-pdf-modal" id="pdfModal">
    <div class="inv-pdf-head">
      <div class="inv-pdf-title">
        <i class="fa-solid fa-file-pdf" style="color: var(--inv-danger);"></i>
        <span class="inv-trunc" id="pdfTitle">PDF</span>
      </div>
      <div class="inv-flex inv-gap-2" style="justify-content:flex-end; flex-wrap:nowrap;">
        <a class="inv-btn inv-btn-light inv-btn-sm" id="pdfOpenNew" href="#" target="_blank" rel="noopener">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>&nbsp; Neuer Tab
        </a>
        <a class="inv-btn inv-btn-light inv-btn-sm" id="pdfDownload" href="#">
          <i class="fa-solid fa-download"></i>&nbsp; Herunterladen
        </a>
        <button class="inv-btn inv-btn-light inv-btn-sm" id="pdfClose" type="button">
          <i class="fa-solid fa-xmark"></i>&nbsp; Schließen
        </button>
      </div>
    </div>
    <div class="inv-pdf-body">
      <iframe id="pdfFrame" src="about:blank"></iframe>
    </div>
  </div>

  <div class="inv-modal-backdrop" id="profileBackdrop"></div>
  <div class="inv-profile-modal" id="profileModal">
    <div class="inv-profile-head">
      <div>
        <div class="inv-fw-700" style="font-size:1.15rem;" id="profileTitle">Rechnungsprofil</div>
        <div class="inv-muted inv-small inv-fw-600" id="profileSub">Details · Dateien · Historie</div>
      </div>
      <div class="inv-flex inv-gap-2" style="justify-content:flex-end;">
        <a class="inv-btn inv-btn-canvas inv-btn-sm" id="profileCanvas" href="#">
          <i class="fa-solid fa-file-invoice"></i>&nbsp; Canvas
        </a>
        <button class="inv-btn inv-btn-light inv-btn-sm" id="profileEdit" type="button">
          <i class="fa-solid fa-pen"></i>&nbsp; Bearbeiten
        </button>
        <button class="inv-btn inv-btn-light inv-btn-sm" id="profileClose" type="button">
          <i class="fa-solid fa-xmark"></i>&nbsp; Schließen
        </button>
      </div>
    </div>
    <div class="inv-profile-body" id="profileBody"></div>
  </div>

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const app = document.getElementById('invApp');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const API = {
          list: app.dataset.listUrl,
          store: app.dataset.storeUrl,
          show: app.dataset.showUrl,
          update: app.dataset.updateUrl,
          status: app.dataset.statusUrl,
          canvas: app.dataset.canvasUrl,
          destroy: app.dataset.destroyUrl,
          upload: app.dataset.uploadUrl,
          deleteFile: app.dataset.deleteFileUrl,
          downloadFile: app.dataset.downloadFileUrl,
          viewFile: app.dataset.viewFileUrl,
          selCustomers: app.dataset.selCustomers,
          selObjects: app.dataset.selObjects,
          selProducts: app.dataset.selProducts,
          selDeals: app.dataset.selDeals,
          dealItems: app.dataset.dealItemsUrl,
        };

        const els = {
          kpis: document.getElementById('inv-kpis'),

          q: document.getElementById('inv-q'),
          type: document.getElementById('inv-type'),
          status: document.getElementById('inv-status'),
          from: document.getElementById('inv-from'),
          to: document.getElementById('inv-to'),
          per: document.getElementById('inv-per'),
          reset: document.getElementById('inv-reset'),
          refresh: document.getElementById('inv-refresh'),

          viewTable: document.getElementById('inv-view-table'),
          viewCards: document.getElementById('inv-view-cards'),
          viewCustomers: document.getElementById('inv-view-customers'),
          viewKanban: document.getElementById('inv-view-kanban'),
          canvasHelp: document.getElementById('inv-canvas-help'),

          tbody: document.getElementById('inv-tbody'),
          meta: document.getElementById('inv-meta'),
          prev: document.getElementById('inv-prev'),
          next: document.getElementById('inv-next'),

          tableWrap: document.getElementById('inv-table-wrap'),
          cardWrap: document.getElementById('inv-card-wrap'),
          cards: document.getElementById('inv-cards'),

          customerWrap: document.getElementById('inv-customer-wrap'),
          customers: document.getElementById('inv-customers'),
          kanbanWrap: document.getElementById('inv-kanban-wrap'),
          kanban: document.getElementById('inv-kanban'),

          backdrop: document.getElementById('invBackdrop'),
          drawer: document.getElementById('invDrawer'),
          closeDrawer: document.getElementById('invDrawerClose'),
          newBtn: document.getElementById('inv-new'),

          drawerTitle: document.getElementById('inv-drawer-title'),
          drawerSub: document.getElementById('inv-drawer-sub'),
          step1Pill: document.getElementById('invStep1Pill'),
          step2Pill: document.getElementById('invStep2Pill'),
          activeId: document.getElementById('invActiveId'),

          step1: document.getElementById('invStep1'),
          step2: document.getElementById('invStep2'),

          mCustomer: $('#mCustomer'),
          mObject: $('#mObject'),
          mDeal: $('#mDeal'),
          dealBox: document.getElementById('invDealBox'),
          dealLimitText: document.getElementById('dealLimitText'),
          dealInvoicedText: document.getElementById('dealInvoicedText'),
          dealRemainingText: document.getElementById('dealRemainingText'),
          dealLimitHintText: document.getElementById('dealLimitHintText'),
          mInvoiceNo: document.getElementById('mInvoiceNo'),
          mType: document.getElementById('mType'),
          mStatus: document.getElementById('mStatus'),
          mIssueDate: document.getElementById('mIssueDate'),
          mDueDate: document.getElementById('mDueDate'),
          mServiceFrom: document.getElementById('mServiceFrom'),
          mServiceTo: document.getElementById('mServiceTo'),
          mTaxRate: document.getElementById('mTaxRate'),
          mNotes: document.getElementById('mNotes'),

          pmItems: document.getElementById('pm_items'),
          pmTotal: document.getElementById('pm_total'),
          itemsBlock: document.getElementById('itemsBlock'),
          totalOnlyBlock: document.getElementById('totalOnlyBlock'),
          mTotalTitle: document.getElementById('mTotalTitle'),
          mTotalNet: document.getElementById('mTotalNet'),

          toggleCompact: document.getElementById('toggleCompactColumns'),

          mProductPicker: $('#mProductPicker'),
          itemsBody: document.getElementById('itemsBody'),
          sumSubtotal: document.getElementById('sumSubtotal'),
          sumTax: document.getElementById('sumTax'),
          sumTotal: document.getElementById('sumTotal'),
          saveHint: document.getElementById('saveHint'),

          drop: document.getElementById('invDrop'),
          mFiles: document.getElementById('mFiles'),
          filesList: document.getElementById('filesList'),
          pickFiles: document.getElementById('btnPickFiles'),
          uploadFiles: document.getElementById('btnUploadFiles'),
          pickedHint: document.getElementById('pickedFilesHint'),
          reloadFiles: document.getElementById('btnReloadFiles'),

          addManualItem: document.getElementById('btnAddManualItem'),
          saveStep1: document.getElementById('btnSaveStep1'),
          backTo1: document.getElementById('btnBackToStep1'),
          finish: document.getElementById('btnFinish'),

          pdfBackdrop: document.getElementById('pdfBackdrop'),
          pdfModal: document.getElementById('pdfModal'),
          pdfTitle: document.getElementById('pdfTitle'),
          pdfFrame: document.getElementById('pdfFrame'),
          pdfClose: document.getElementById('pdfClose'),
          pdfOpenNew: document.getElementById('pdfOpenNew'),
          pdfDownload: document.getElementById('pdfDownload'),

          profileBackdrop: document.getElementById('profileBackdrop'),
          profileModal: document.getElementById('profileModal'),
          profileTitle: document.getElementById('profileTitle'),
          profileSub: document.getElementById('profileSub'),
          profileBody: document.getElementById('profileBody'),
          profileClose: document.getElementById('profileClose'),
          profileEdit: document.getElementById('profileEdit'),
          profileCanvas: document.getElementById('profileCanvas'),
        };

        const state = {
          view: 'customer',
          page: 1,
          perPage: 12,
          total: 0,
          hasMore: false,
          loading: false,
          timer: null,

          sortBy: 'issue_date',
          sortDir: 'desc',

          editingId: null,
          activeInvoiceId: null,
          items: [],
          uploadedFiles: [],
          pickedFiles: [],
          activeProfileId: null,
          selectedDealData: null,
          offerDetailId: null,

          priceMode: 'items',
          compactColumns: false,
        };

        function escapeHtml(s) {
          return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", "&#039;");
        }

        function money(n) {
          const x = Number(n || 0);
          return x.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function canvasUrl(id) {
          return String(API.canvas || '').replace('__ID__', id);
        }

        function fmtDate(val) {
          if (!val) return '—';
          const s = String(val);
          const d = s.slice(0, 10);
          if (!/^\d{4}-\d{2}-\d{2}$/.test(d)) return d || '—';
          const [y, m, dd] = d.split('-');
          return `${dd}.${m}.${y}`;
        }

        function dateOnly(val) {
          if (!val) return '';
          return String(val).slice(0, 10);
        }

        function statusLabel(status) {
          const s = String(status || 'draft').toLowerCase();
          return (s === 'paid') ? 'Bezahlt' :
            (s === 'overdue') ? 'Überfällig' :
              (s === 'sent') ? 'Gesendet' :
                (s === 'cancelled') ? 'Storniert' : 'Entwurf';
        }

        function statusBadge(status) {
          const s = String(status || 'draft').toLowerCase();
          const map = {
            draft: 'inv-b-draft',
            sent: 'inv-b-sent',
            paid: 'inv-b-paid',
            overdue: 'inv-b-overdue',
            cancelled: 'inv-b-cancelled',
          };
          const cls = map[s] || 'inv-b-draft';
          const icon =
            (s === 'paid') ? 'fa-circle-check' :
              (s === 'overdue') ? 'fa-triangle-exclamation' :
                (s === 'sent') ? 'fa-paper-plane' :
                  (s === 'cancelled') ? 'fa-ban' : 'fa-pen';

          return `<span class="inv-badge ${cls}"><i class="fa-solid ${icon}"></i>${escapeHtml(statusLabel(s))}</span>`;
        }

        function statusOptions(selected) {
          const current = String(selected || 'draft').toLowerCase();

          const options = [
            { value: 'draft', label: 'Entwurf' },
            { value: 'sent', label: 'Gesendet' },
            { value: 'paid', label: 'Bezahlt' },
            { value: 'overdue', label: 'Überfällig' },
            { value: 'cancelled', label: 'Storniert' },
          ];

          return `
                <span class="inv-status-wrap">
                  <span class="inv-status-dot ${escapeHtml(current)}"></span>
                  <select class="inv-status-quick inv-quick-status" data-current="${escapeHtml(current)}">
                    ${options.map(opt => `
                      <option value="${opt.value}" ${opt.value === current ? 'selected' : ''}>
                        ${escapeHtml(opt.label)}
                      </option>
                    `).join('')}
                  </select>
                </span>
              `;
        }

        function customerLabel(c) {
          if (!c) return '—';
          const firma = (c.firma || '').trim();
          const ln = (c.lastname || '').trim();
          const n = (c.name || '').trim();
          return (firma + ' ' + ln + ' ' + n).trim() || ('Lead #' + c.id);
        }

        function objectLabel(o) {
          if (!o) return '—';
          const name = (o.object_name || '').trim();
          const addr = (o.full_address || `${o.street || ''} ${o.postcode || ''} ${o.city || ''}`.trim()).trim();
          return `${name ? name + ' — ' : ''}${addr}`.trim() || ('Objekt #' + o.id);
        }

        function dealLabel(d) {
          if (!d) return '—';
          if (d.text) return d.text;
          if (d.deal_summary) {
            return `#${d.deal_summary.deal_number || d.deal_summary.id} — ${d.deal_summary.customer_name || ''}`.trim();
          }
          return d.deal_number ? `#${d.deal_number}` : (d.id ? `Auftrag #${d.id}` : '—');
        }

        function fileIsImage(file) {
          const mime = String(file?.mime || '').toLowerCase();
          const name = String(file?.stored_name || file?.original_name || '').toLowerCase();
          return mime.startsWith('image/') || /\.(jpg|jpeg|png|webp|gif)$/i.test(name);
        }

        function fileIsPdf(file) {
          const mime = String(file?.mime || '').toLowerCase();
          const name = String(file?.stored_name || file?.original_name || '').toLowerCase();
          return mime.includes('pdf') || name.endsWith('.pdf');
        }

        function kpiCard(label, value, sub, icon) {
          return `
                <div class="inv-stat">
                  <div class="inv-stat-head">
                    <div>
                      <div class="inv-stat-val">${escapeHtml(value)}</div>
                      <div class="inv-stat-lbl">${escapeHtml(label)}</div>
                    </div>
                    <div class="inv-stat-icon"><i class="fa-solid ${icon}"></i></div>
                  </div>
                  <div class="inv-small inv-muted inv-fw-600">${escapeHtml(sub || '')}</div>
                </div>
              `;
        }

        function renderKpis(a) {
          const countAll = Number(a?.count_all || 0);
          const sumTotal = Number(a?.sum_total || 0);
          const paidSum = Number(a?.paid_sum || 0);
          const openSum = Number(a?.open_sum || 0);
          const draftCount = Number(a?.draft_count || 0);

          els.kpis.innerHTML = [
            kpiCard('Treffer gesamt', String(countAll), 'Aktueller Filter', 'fa-file-invoice'),
            kpiCard('Rechnungsbetrag', money(sumTotal) + ' €', 'Ohne Entwurf/Storniert', 'fa-euro-sign'),
            kpiCard('Bezahlt', money(paidSum) + ' €', 'Tatsächlich bezahlt', 'fa-circle-check'),
            kpiCard('Offen', money(openSum) + ' €', 'Noch zu bezahlen', 'fa-triangle-exclamation'),
            kpiCard('Entwürfe', String(draftCount), 'Status: Entwurf', 'fa-pen'),
          ].join('');
        }

        async function apiJson(url, opts = {}) {
          const res = await fetch(url, {
            ...opts,
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest',
              ...(opts.headers || {})
            }
          });

          const ct = (res.headers.get('content-type') || '').toLowerCase();
          let j = {};
          if (ct.includes('application/json')) j = await res.json().catch(() => ({}));

          if (!res.ok || j?.ok === false) {
            const msg =
              j?.message ||
              (j?.errors ? Object.values(j.errors).flat().join(' ') : '') ||
              `Anfrage fehlgeschlagen (${res.status})`;
            throw new Error(msg);
          }
          return j;
        }

        function buildListUrl() {
          const u = new URL(API.list, window.location.origin);
          const params = {
            page: state.page,
            per_page: state.perPage,
            search: (els.q.value || '').trim(),
            type: els.type.value || '',
            status: els.status.value || '',
            from: els.from.value || '',
            to: els.to.value || '',
            sort_by: state.sortBy,
            sort_dir: state.sortDir,
          };
          Object.entries(params).forEach(([k, v]) => {
            if (v !== '' && v != null) u.searchParams.set(k, v);
          });
          return u.toString();
        }

        function renderEmpty(msg) {
          els.tbody.innerHTML = `<tr><td colspan="8" class="inv-center" style="padding:2rem;color:var(--inv-muted);">${escapeHtml(msg)}</td></tr>`;
          els.cards.innerHTML = `<div class="inv-center inv-muted inv-fw-600" style="padding:1.25rem;">${escapeHtml(msg)}</div>`;
          els.customers.innerHTML = `<div class="inv-center inv-muted inv-fw-600" style="padding:1.25rem;">${escapeHtml(msg)}</div>`;
          if (els.kanban) els.kanban.innerHTML = `<div class="inv-center inv-muted inv-fw-600" style="padding:1.25rem;">${escapeHtml(msg)}</div>`;
        }

        function isSchluss(inv) {
          return String(inv?.type || '').toLowerCase() === 'schlussrechnung';
        }

        function hasPdf(inv) {
          if (typeof inv?.files_count !== 'undefined') return Number(inv.files_count || 0) > 0;
          if (Array.isArray(inv?.files)) return inv.files.length > 0;
          return false;
        }

        function getPaidAmount(inv) {
          return Number(inv?.paid_amount || 0);
        }

        function getUnpaidAmount(inv) {
          const total = Number(inv?.total_amount || 0);
          const paid = getPaidAmount(inv);
          return Math.max(0, Math.round((total - paid) * 100) / 100);
        }

        function normType(t) {
          return String(t || '').trim().toLowerCase();
        }

        function isCreditType(inv) {
          const t = normType(inv?.type);
          return t === 'stornorechnung' || t === 'gutschrift';
        }

        function isFinancialType(inv) {
          const t = normType(inv?.type);
          const nonFinancial = new Set(['angebot', 'auftrag', 'lieferschein', 'quittung', 'proforma']);
          return !nonFinancial.has(t);
        }

        function signedTotal(inv) {
          const total = Number(inv?.total_amount || 0);
          const sign = isCreditType(inv) ? -1 : 1;
          return sign * total;
        }

        function signedPaid(inv) {
          const paidRaw = Number(inv?.paid_amount || 0);
          const sign = isCreditType(inv) ? -1 : 1;
          return sign * paidRaw;
        }

        function signedOpen(inv) {
          const tot = signedTotal(inv);
          const paid = signedPaid(inv);
          const open = tot - paid;
          return Math.abs(open) < 0.00001 ? 0 : open;
        }

        function moneySigned(n) {
          const x = Number(n || 0);
          const abs = money(Math.abs(x));
          return (x < 0) ? `- ${abs}` : abs;
        }

        function pillToneForOpen(open) {
          if (open > 0.00001) return { cls: 'inv-pill-warn', icon: 'fa-triangle-exclamation', label: 'Offen' };
          if (open < -0.00001) return { cls: 'inv-pill-info', icon: 'fa-circle-info', label: 'Guthaben' };
          return { cls: 'inv-pill-muted', icon: 'fa-circle-minus', label: 'Offen' };
        }

        function renderTable(rows) {
          const data = Array.isArray(rows) ? rows : [];
          if (!data.length) return renderEmpty('Keine Ergebnisse gefunden');

          els.tbody.innerHTML = data.map(inv => {
            const id = inv.id;
            const date = fmtDate(inv.issue_date);
            const no = inv.invoice_no || '—';
            const type = inv.type || '—';
            const total = money(inv.total_amount || 0);
            const c = customerLabel(inv.customer);
            const o = objectLabel(inv.object);

            return `
                  <tr class="inv-row" data-id="${id}">
                    <td style="padding-left:1.5rem;">${escapeHtml(date)}</td>
                    <td class="inv-fw-700">${escapeHtml(no)}</td>
                    <td class="inv-fw-600">${escapeHtml(c)}</td>
                    <td class="inv-muted inv-fw-600">${escapeHtml(o)}</td>
                    <td>${escapeHtml(type)}</td>
                    <td>
                      <div data-id="${id}">
                        ${statusOptions(inv.status)}
                      </div>
                    </td>
                    <td class="inv-right inv-fw-700">${escapeHtml(total)} €</td>
                    <td class="inv-right" style="padding-right:1.5rem;">
                      <a href="javascript:void(0)" class="inv-btn-icon inv-profile" data-id="${id}" title="Profil öffnen"><i class="fa-solid fa-eye"></i></a>
                      <a href="${canvasUrl(id)}" class="inv-btn-icon inv-canvas" data-id="${id}" title="Rechnung Canvas" style="margin-left:6px;"><i class="fa-solid fa-file-invoice"></i></a>
                      <a href="javascript:void(0)" class="inv-btn-icon inv-open-files" data-id="${id}" title="Dateien öffnen" style="margin-left:6px;"><i class="fa-solid fa-folder-open"></i></a>
                      <a href="javascript:void(0)" class="inv-btn-icon inv-edit" data-id="${id}" title="Bearbeiten" style="margin-left:6px;"><i class="fa-solid fa-pen-to-square"></i></a>
                      <a href="javascript:void(0)" class="inv-btn-icon inv-del" data-id="${id}" title="Löschen" style="margin-left:6px;border-color:rgba(220,38,38,.25);color:var(--inv-danger);"><i class="fa-solid fa-trash"></i></a>
                    </td>
                  </tr>
                `;
          }).join('');
        }

        function renderCards(rows) {
          const data = Array.isArray(rows) ? rows : [];
          if (!data.length) return renderEmpty('Keine Ergebnisse gefunden');

          els.cards.innerHTML = data.map(inv => {
            const id = inv.id;
            const date = fmtDate(inv.issue_date);
            const no = inv.invoice_no || '—';
            const type = inv.type || '—';
            const total = money(inv.total_amount || 0);
            const c = customerLabel(inv.customer);
            const o = objectLabel(inv.object);

            return `
                  <div class="inv-stat inv-row" data-id="${id}" style="border-radius:18px;">
                    <div class="inv-flex" style="align-items:flex-start;">
                      <div style="min-width:0;">
                        <div class="inv-muted inv-small inv-fw-700" style="text-transform:uppercase;letter-spacing:.05em;">${escapeHtml(type)}</div>
                        <div class="inv-fw-700" style="font-size:1.2rem;margin-top:4px;">${escapeHtml(no)}</div>
                        <div class="inv-small inv-fw-600" style="margin-top:6px;">${escapeHtml(c)}</div>
                        <div class="inv-small inv-muted inv-fw-600 inv-trunc" style="margin-top:4px;">${escapeHtml(o)}</div>
                      </div>
                      <div class="inv-right">
                        <div data-id="${id}">
                          ${statusOptions(inv.status)}
                        </div>
                        <div class="inv-small inv-muted inv-fw-600" style="margin-top:8px;">${escapeHtml(date)}</div>
                      </div>
                    </div>

                    <div class="inv-flex" style="margin-top:14px;">
                      <div class="inv-muted inv-small inv-fw-600">Gesamt</div>
                      <div class="inv-fw-700" style="font-size:1.35rem;">${escapeHtml(total)} €</div>
                    </div>

                    <div class="inv-flex inv-gap-2" style="justify-content:flex-end;margin-top:14px;">
                      <button class="inv-btn inv-btn-light inv-profile" data-id="${id}" type="button"><i class="fa-solid fa-eye"></i>&nbsp; Profil</button>
                      <a class="inv-btn inv-btn-canvas inv-canvas" href="${canvasUrl(id)}" data-id="${id}"><i class="fa-solid fa-file-invoice"></i>&nbsp; Canvas</a>
                      <button class="inv-btn inv-btn-light inv-open-files" data-id="${id}" type="button"><i class="fa-solid fa-folder-open"></i>&nbsp; Dateien</button>
                      <button class="inv-btn inv-btn-light inv-edit" data-id="${id}" type="button"><i class="fa-solid fa-pen"></i>&nbsp; Bearbeiten</button>
                      <button class="inv-btn inv-btn-danger inv-del" data-id="${id}" type="button"><i class="fa-solid fa-trash"></i>&nbsp; Löschen</button>
                    </div>
                  </div>
                `;
          }).join('');
        }

        function renderCustomers(rows) {
          const data = Array.isArray(rows) ? rows : [];
          if (!data.length) return renderEmpty('Keine Ergebnisse gefunden');

          const map = new Map();
          for (const inv of data) {
            const cid = inv.customer_id || inv.customer?.id || '0';
            if (!map.has(cid)) map.set(cid, { customer: inv.customer, invoices: [] });
            map.get(cid).invoices.push(inv);
          }

          const customers = Array.from(map.entries())
            .map(([cid, g]) => ({
              cid,
              label: customerLabel(g.customer),
              customer: g.customer,
              invoices: g.invoices || []
            }))
            .sort((a, b) => a.label.localeCompare(b.label, 'de'));

          els.customers.innerHTML = `
                <div class="inv-acc">
                  ${customers.map((cg, idx) => {
            const invs = cg.invoices.slice().sort((a, b) => {
              const as = isSchluss(a) ? 1 : 0;
              const bs = isSchluss(b) ? 1 : 0;
              if (as !== bs) return as - bs;
              return String(b.issue_date || '').localeCompare(String(a.issue_date || ''));
            });

            let sumTotal = 0, sumPaid = 0, sumOpen = 0;
            let pdfCount = 0;
            let schlussCount = 0, schlussTotal = 0, schlussOpen = 0;
            let stornoCount = 0, stornoTotal = 0, stornoOpen = 0;

            invs.forEach(inv => {
              if (hasPdf(inv)) pdfCount++;

              if (!isFinancialType(inv)) return;

              const tot = signedTotal(inv);
              const paid = signedPaid(inv);
              const open = signedOpen(inv);

              sumTotal += tot;
              sumPaid += paid;
              sumOpen += open;

              if (isSchluss(inv)) {
                schlussCount++;
                schlussTotal += tot;
                schlussOpen += open;
              }

              if (isCreditType(inv)) {
                stornoCount++;
                stornoTotal += tot;
                stornoOpen += open;
              }
            });

            const round2 = (n) => Math.round((Number(n || 0)) * 100) / 100;

            sumTotal = round2(sumTotal);
            sumPaid = round2(sumPaid);
            sumOpen = round2(sumOpen);
            schlussTotal = round2(schlussTotal);
            schlussOpen = round2(schlussOpen);
            stornoTotal = round2(stornoTotal);
            stornoOpen = round2(stornoOpen);

            const openTone = pillToneForOpen(sumOpen);
            const openAttr = idx === 0 ? 'open' : '';

            return `
                      <details ${openAttr}>
                        <summary>
                          <div class="inv-acc-title" style="min-width:0;">
                            <div class="inv-chev"><i class="fa-solid fa-chevron-down"></i></div>
                            <div style="min-width:0;">
                              <div class="inv-acc-name">${escapeHtml(cg.label)}</div>
                              <div class="inv-acc-sub">${invs.length} Rechnung(en)</div>
                            </div>
                          </div>

                          <div class="inv-acc-meta">
                            <span class="inv-pill inv-pill-info">
                              <i class="fa-solid fa-euro-sign"></i>
                              Rechnungsbetrag: ${escapeHtml(moneySigned(sumTotal))} €
                            </span>

                            <span class="inv-pill inv-pill-ok">
                              <i class="fa-solid fa-circle-check"></i>
                              Bezahlt: ${escapeHtml(moneySigned(sumPaid))} €
                            </span>

                            <span class="inv-pill ${openTone.cls}">
                              <i class="fa-solid ${openTone.icon}"></i>
                              ${escapeHtml(openTone.label)}: ${escapeHtml(moneySigned(sumOpen))} €
                            </span>

                            ${schlussCount ? `
                              <span class="inv-pill inv-pill-muted">
                                <i class="fa-solid fa-flag-checkered"></i>
                                Schluss: ${escapeHtml(moneySigned(schlussTotal))} € · Offen: ${escapeHtml(moneySigned(schlussOpen))} €
                              </span>
                            ` : `
                              <span class="inv-pill inv-pill-warn">
                                <i class="fa-solid fa-flag"></i>
                                Schlussrechnung: Nein
                              </span>
                            `}

                            ${stornoCount ? `
                              <span class="inv-pill inv-pill-info">
                                <i class="fa-solid fa-rotate-left"></i>
                                Storno/Gutschrift: ${escapeHtml(moneySigned(stornoTotal))} € · Offen: ${escapeHtml(moneySigned(stornoOpen))} €
                              </span>
                            ` : ``}

                            <span class="inv-pill ${pdfCount > 0 ? 'inv-pill-ok' : 'inv-pill-warn'}">
                              <i class="fa-solid fa-file-pdf"></i>
                              PDF: ${pdfCount}/${invs.length}
                            </span>
                          </div>
                        </summary>

                        <div class="inv-acc-body">
                          <div class="inv-table-wrap">
                            <table class="inv-mini-table">
                              <thead>
                                <tr>
                                  <th>Datum</th>
                                  <th>Rechnungsnr</th>
                                  <th>Typ</th>
                                  <th>Status</th>
                                  <th class="inv-right">Gesamt</th>
                                  <th class="inv-right">Bezahlt</th>
                                  <th class="inv-right">Offen</th>
                                  <th>PDF</th>
                                  <th class="inv-right">Aktion</th>
                                </tr>
                              </thead>
                              <tbody>
                                ${invs.map(inv => {
              const id = inv.id;
              const date = fmtDate(inv.issue_date);
              const no = inv.invoice_no || '—';
              const type = inv.type || '—';
              const pdfOk = hasPdf(inv);

              const fin = isFinancialType(inv);
              const total = fin ? signedTotal(inv) : 0;
              const paid = fin ? signedPaid(inv) : 0;
              const open = fin ? signedOpen(inv) : 0;

              const openIsWarn = open > 0.00001;
              const openIsCredit = open < -0.00001;

              return `
                                    <tr class="inv-row" data-id="${id}">
                                      <td>${escapeHtml(date)}</td>
                                      <td class="inv-fw-700">${escapeHtml(no)}</td>
                                      <td>
                                        ${escapeHtml(type)}
                                        ${isSchluss(inv) ? `<span class="inv-pill inv-pill-muted" style="margin-left:.4rem;"><i class="fa-solid fa-flag-checkered"></i> Ende</span>` : ``}
                                        ${isCreditType(inv) ? `<span class="inv-pill inv-pill-muted" style="margin-left:.4rem;"><i class="fa-solid fa-rotate-left"></i> Storno</span>` : ``}
                                      </td>
                                      <td>
                                        <div data-id="${id}">
                                          ${statusOptions(inv.status)}
                                        </div>
                                      </td>

                                      <td class="inv-right inv-fw-700" style="${total < 0 ? 'color:var(--inv-danger);' : ''}">
                                        ${escapeHtml(moneySigned(total))} €
                                      </td>

                                      <td class="inv-right" style="${paid < 0 ? 'color:var(--inv-danger);' : ''}">
                                        ${escapeHtml(moneySigned(paid))} €
                                      </td>

                                      <td class="inv-right ${openIsWarn ? 'inv-fw-700' : ''}"
                                          style="${openIsWarn ? 'color:var(--inv-danger);' : (openIsCredit ? 'color:#075985;' : '')}">
                                        ${escapeHtml(moneySigned(open))} €
                                      </td>

                                      <td>
                                        <span class="inv-pill ${pdfOk ? 'inv-pill-ok' : 'inv-pill-warn'}">
                                          <i class="fa-solid fa-file-pdf"></i>${pdfOk ? 'Vorhanden' : 'Fehlt'}
                                        </span>
                                      </td>

                                      <td class="inv-right">
                                        <a href="javascript:void(0)" class="inv-btn-icon inv-profile" data-id="${id}" title="Profil öffnen"><i class="fa-solid fa-eye"></i></a>
                                        <a href="${canvasUrl(id)}" class="inv-btn-icon inv-canvas" data-id="${id}" title="Rechnung Canvas" style="margin-left:6px;"><i class="fa-solid fa-file-invoice"></i></a>
                                        <a href="javascript:void(0)" class="inv-btn-icon inv-open-files" data-id="${id}" title="Dateien öffnen" style="margin-left:6px;"><i class="fa-solid fa-folder-open"></i></a>
                                        <a href="javascript:void(0)" class="inv-btn-icon inv-edit" data-id="${id}" title="Bearbeiten" style="margin-left:6px;"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="javascript:void(0)" class="inv-btn-icon inv-del" data-id="${id}" title="Löschen" style="margin-left:6px;border-color:rgba(220,38,38,.25);color:var(--inv-danger);"><i class="fa-solid fa-trash"></i></a>
                                      </td>
                                    </tr>
                                  `;
            }).join('')}
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </details>
                    `;
          }).join('')}
                </div>
              `;
        }

        function kanbanColumns() {
          return [
            { key: 'draft', label: 'Entwurf', icon: 'fa-pen' },
            { key: 'sent', label: 'Gesendet', icon: 'fa-paper-plane' },
            { key: 'overdue', label: 'Überfällig', icon: 'fa-triangle-exclamation' },
            { key: 'paid', label: 'Bezahlt', icon: 'fa-circle-check' },
            { key: 'cancelled', label: 'Storniert', icon: 'fa-ban' },
          ];
        }

        function renderKanban(rows) {
          const data = Array.isArray(rows) ? rows : [];
          if (!data.length) {
            if (els.kanban) els.kanban.innerHTML = `<div class="inv-center inv-muted inv-fw-600" style="padding:1.25rem;">Keine Ergebnisse gefunden</div>`;
            return;
          }

          const cols = kanbanColumns();
          const grouped = new Map(cols.map(c => [c.key, []]));
          data.forEach(inv => {
            const status = String(inv.status || 'draft').toLowerCase();
            if (!grouped.has(status)) grouped.set(status, []);
            grouped.get(status).push(inv);
          });

          els.kanban.innerHTML = cols.map(col => {
            const items = grouped.get(col.key) || [];
            return `
                  <section class="inv-kanban-col" data-kanban-col="${escapeHtml(col.key)}">
                    <div class="inv-kanban-head">
                      <div class="inv-kanban-title"><i class="fa-solid ${escapeHtml(col.icon)}"></i>${escapeHtml(col.label)}</div>
                      <span class="inv-kanban-count">${items.length}</span>
                    </div>
                    <div class="inv-kanban-list">
                      ${items.length ? items.map(inv => {
              const id = inv.id;
              const no = inv.invoice_no || '—';
              const type = inv.type || '—';
              const total = money(inv.total_amount || 0);
              const open = money(getUnpaidAmount(inv));
              const c = customerLabel(inv.customer);
              const o = objectLabel(inv.object);
              return `
                          <article class="inv-kanban-card inv-row" data-id="${id}">
                            <div class="inv-kanban-no">${escapeHtml(no)}</div>
                            <div class="inv-kanban-meta">${escapeHtml(type)} · ${escapeHtml(fmtDate(inv.issue_date))}</div>
                            <div class="inv-kanban-meta">${escapeHtml(c)}</div>
                            <div class="inv-kanban-meta inv-trunc">${escapeHtml(o)}</div>
                            <div class="inv-kanban-money">
                              <span>${escapeHtml(total)} €</span>
                              <span class="inv-muted inv-small">Offen ${escapeHtml(open)} €</span>
                            </div>
                            <div data-id="${id}">${statusOptions(inv.status)}</div>
                            <div class="inv-kanban-actions">
                              <button class="inv-btn inv-btn-light inv-btn-sm inv-profile" data-id="${id}" type="button"><i class="fa-solid fa-eye"></i></button>
                              <a class="inv-btn inv-btn-canvas inv-btn-sm inv-canvas" href="${canvasUrl(id)}" data-id="${id}"><i class="fa-solid fa-file-invoice"></i></a>
                              <button class="inv-btn inv-btn-light inv-btn-sm inv-open-files" data-id="${id}" type="button"><i class="fa-solid fa-folder-open"></i></button>
                              <button class="inv-btn inv-btn-light inv-btn-sm inv-edit" data-id="${id}" type="button"><i class="fa-solid fa-pen"></i></button>
                              <button class="inv-btn inv-btn-danger inv-btn-sm inv-del" data-id="${id}" type="button"><i class="fa-solid fa-trash"></i></button>
                            </div>
                          </article>
                        `;
            }).join('') : `<div class="inv-muted inv-small inv-fw-600" style="padding:.75rem;">Keine Rechnungen</div>`}
                    </div>
                  </section>
                `;
          }).join('');
        }

        async function load() {
          if (state.loading) return;
          state.loading = true;

          renderEmpty('Lade Daten...');
          els.meta.textContent = 'Lade Daten...';

          try {
            const j = await apiJson(buildListUrl());
            const rows = j.data || [];
            const meta = j.meta || {};

            state.total = meta.total || 0;
            state.hasMore = (meta.current_page || 1) < (meta.last_page || 1);

            renderKpis(j.analytics || {});

            const start = meta.total ? (((meta.current_page - 1) * meta.per_page) + 1) : 0;
            const end = meta.total ? Math.min(meta.current_page * meta.per_page, meta.total) : 0;

            els.meta.textContent = meta.total
              ? `Zeige ${start}-${end} von ${meta.total} Einträgen`
              : '0 Einträge';

            els.prev.disabled = (meta.current_page || 1) <= 1;
            els.next.disabled = !state.hasMore;

            if (state.view === 'table') renderTable(rows);
            else if (state.view === 'card') renderCards(rows);
            else if (state.view === 'kanban') renderKanban(rows);
            else renderCustomers(rows);

          } catch (e) {
            renderEmpty(e.message || 'Fehler beim Laden');
            els.meta.textContent = '';
          } finally {
            state.loading = false;
          }
        }

        function debouncedReload() {
          clearTimeout(state.timer);
          state.timer = setTimeout(() => {
            state.page = 1;
            load();
          }, 250);
        }

        function applyView(view) {
          state.view = view;

          els.tableWrap.classList.toggle('inv-hide', state.view !== 'table');
          els.cardWrap.classList.toggle('inv-hide', state.view !== 'card');
          els.customerWrap.classList.toggle('inv-hide', state.view !== 'customer');
          if (els.kanbanWrap) els.kanbanWrap.classList.toggle('inv-hide', state.view !== 'kanban');

          state.page = 1;
          load();
        }

        applyView(state.view);

        els.viewTable.addEventListener('click', () => applyView('table'));
        els.viewCards.addEventListener('click', () => applyView('card'));
        els.viewCustomers.addEventListener('click', () => applyView('customer'));
        if (els.viewKanban) els.viewKanban.addEventListener('click', () => applyView('kanban'));

        if (els.canvasHelp) {
          els.canvasHelp.addEventListener('click', () => {
            applyView('customer');
            alert('Bitte wähle eine bestehende Rechnung und klicke auf das Canvas-Symbol. Für neue Rechnungen aus Auftrag muss der Button im Auftrag/OfferDetail geöffnet werden.');
          });
        }

        els.q.addEventListener('input', debouncedReload);
        els.type.addEventListener('change', debouncedReload);
        els.status.addEventListener('change', debouncedReload);
        els.from.addEventListener('change', debouncedReload);
        els.to.addEventListener('change', debouncedReload);

        els.per.addEventListener('change', () => {
          state.perPage = Number(els.per.value || 12);
          state.page = 1;
          load();
        });

        els.reset.addEventListener('click', () => {
          els.q.value = '';
          els.type.value = '';
          els.status.value = '';
          els.from.value = '';
          els.to.value = '';
          els.per.value = '12';
          state.perPage = 12;
          state.sortBy = 'issue_date';
          state.sortDir = 'desc';
          state.page = 1;
          load();
        });

        els.refresh.addEventListener('click', () => load());

        document.querySelectorAll('.inv-sort').forEach(th => {
          th.addEventListener('click', () => {
            const s = th.getAttribute('data-sort');
            if (!s) return;
            if (state.sortBy === s) state.sortDir = (state.sortDir === 'asc') ? 'desc' : 'asc';
            else {
              state.sortBy = s;
              state.sortDir = 'desc';
            }
            state.page = 1;
            load();
          });
        });

        els.prev.addEventListener('click', () => {
          if (state.page > 1) {
            state.page--;
            load();
          }
        });

        els.next.addEventListener('click', () => {
          if (state.hasMore) {
            state.page++;
            load();
          }
        });

        function openDrawer() {
          els.drawer.classList.add('active');
          els.backdrop.classList.add('active');
          document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
          els.drawer.classList.remove('active');
          els.backdrop.classList.remove('active');
          document.body.style.overflow = '';
          resetForm();
        }

        els.closeDrawer.addEventListener('click', closeDrawer);
        els.backdrop.addEventListener('click', closeDrawer);

        function setStep(n) {
          const s1 = (n === 1);
          els.step1.classList.toggle('inv-hide', !s1);
          els.step2.classList.toggle('inv-hide', s1);
          els.step1Pill.classList.toggle('active', s1);
          els.step2Pill.classList.toggle('active', !s1);
          els.drawerSub.textContent = s1 ? 'Schritt 1: Daten' : 'Schritt 2: Dateien';
        }

        els.step1Pill.addEventListener('click', () => setStep(1));
        els.step2Pill.addEventListener('click', () => setStep(2));

        function setPriceMode(mode) {
          state.priceMode = (mode === 'total') ? 'total' : 'items';
          els.itemsBlock.classList.toggle('inv-hide', state.priceMode !== 'items');
          els.totalOnlyBlock.classList.toggle('inv-hide', state.priceMode !== 'total');

          if (state.priceMode === 'total') {
            if (!els.mTotalTitle.value) els.mTotalTitle.value = 'Pauschale / Gesamtbetrag';
          }

          calcTotals();
        }

        els.pmItems.addEventListener('change', () => setPriceMode('items'));
        els.pmTotal.addEventListener('change', () => setPriceMode('total'));
        els.mTotalNet.addEventListener('input', () => calcTotals());

        function applyCompactColumns(on) {
          state.compactColumns = !!on;
          els.itemsBlock.classList.toggle('inv-compact-items', state.compactColumns);

          if (state.compactColumns) {
            state.items = (state.items || []).map(it => ({ ...it, qty: 1, unit: '' }));
          }

          renderItems();
        }

        if (els.toggleCompact) {
          els.toggleCompact.addEventListener('change', () => applyCompactColumns(els.toggleCompact.checked));
        }

        function resetForm() {
          state.editingId = null;
          state.activeInvoiceId = null;
          state.items = [];
          state.uploadedFiles = [];
          state.pickedFiles = [];
          state.selectedDealData = null;
          state.offerDetailId = null;
          state.priceMode = 'items';
          state.compactColumns = false;

          els.drawerTitle.textContent = 'Neue Rechnung';
          els.activeId.textContent = '—';
          els.saveHint.textContent = '';
          els.pickedHint.textContent = '';

          els.mInvoiceNo.value = '';
          els.mType.value = 'Rechnung';
          els.mStatus.value = 'draft';

          const today = new Date().toISOString().slice(0, 10);
          els.mIssueDate.value = today;
          els.mDueDate.value = '';
          els.mServiceFrom.value = '';
          els.mServiceTo.value = '';
          els.mTaxRate.value = '0';
          els.mNotes.value = '';

          els.mTotalTitle.value = '';
          els.mTotalNet.value = '';

          els.pmItems.checked = true;
          els.pmTotal.checked = false;
          setPriceMode('items');

          if (els.toggleCompact) els.toggleCompact.checked = false;
          els.itemsBlock.classList.remove('inv-compact-items');

          els.mCustomer.val(null).trigger('change');
          els.mObject.val(null).trigger('change');
          els.mProductPicker.val(null).trigger('change');

          els.mFiles.value = '';
          renderItems();
          renderFiles();
          setStep(1);
        }

        function setDealInfo(data) {
          state.selectedDealData = data || null;
          state.offerDetailId = data?.offer_detail_id ? Number(data.offer_detail_id) : null;

          const has = !!data;
          if (els.dealBox) els.dealBox.classList.toggle('inv-hide', !has);

          const limit = Number(data?.deal_limit_amount || 0);
          const invoiced = Number(data?.invoiced_amount || 0);
          const remaining = Number(data?.remaining_amount || 0);

          if (els.dealLimitText) els.dealLimitText.textContent = money(limit) + ' €';
          if (els.dealInvoicedText) els.dealInvoicedText.textContent = money(invoiced) + ' €';
          if (els.dealRemainingText) els.dealRemainingText.textContent = money(remaining) + ' €';
          if (els.dealLimitHintText) els.dealLimitHintText.textContent = money(remaining) + ' €';
        }

        async function loadDealItemsFromDeal(deal) {
          if (!deal?.id || !API.dealItems) return;

          const url = String(API.dealItems).replace('__ID__', deal.id);
          els.saveHint.textContent = 'Lade Auftrag-Positionen...';

          const data = await apiJson(url);

          if (!data.ok) {
            throw new Error(data.message || 'Auftrag-Positionen konnten nicht geladen werden.');
          }

          state.offerDetailId = data.offer_detail_id ? Number(data.offer_detail_id) : state.offerDetailId;

          const rows = Array.isArray(data.items) ? data.items : [];

          if (!rows.length) {
            els.saveHint.textContent = 'Keine Materialpositionen im Auftrag gefunden.';
            return;
          }

          state.priceMode = 'items';
          els.pmItems.checked = true;
          els.pmTotal.checked = false;
          setPriceMode('items');

          state.items = rows.map((it, i) => ({
            product_id: it.product_id ?? null,
            article_product_id: it.article_product_id ?? null,
            component_id: it.component_id ?? null,
            distributor_id: it.distributor_id ?? null,
            distributor_price_id: it.distributor_price_id ?? null,
            distributor_article_no: it.distributor_article_no ?? null,
            source_item_type: it.source_item_type ?? null,
            source_item_id: it.source_item_id ?? null,
            source_payload: it.source_payload ?? null,

            title: it.title ?? 'Materialposition',
            description: it.description ?? '',
            qty: Number(it.qty ?? 1),
            unit: it.unit ?? '',
            unit_price: Number(it.unit_price ?? 0),
            tax_rate: Number(it.tax_rate ?? 0),
            sort_order: Number(it.sort_order ?? i),
          }));

          if (data.tax_rate !== undefined && data.tax_rate !== null) {
            els.mTaxRate.value = Number(data.tax_rate || 0);
          }

          renderItems();
          els.saveHint.textContent = 'Auftrag-Positionen wurden geladen.';
        }

        (function initSelect2() {
          const $drawer = $('#invDrawer');

          els.mCustomer.select2({
            placeholder: 'Kunde wählen...',
            allowClear: true,
            width: '100%',
            dropdownParent: $drawer,
            ajax: {
              url: API.selCustomers,
              dataType: 'json',
              delay: 250,
              data: params => ({ term: params.term || '' }),
              processResults: data => ({ results: data.results || [] }),
              cache: true
            }
          });

          els.mObject.select2({
            placeholder: 'Objekt wählen...',
            allowClear: true,
            width: '100%',
            dropdownParent: $drawer,
            ajax: {
              url: API.selObjects,
              dataType: 'json',
              delay: 250,
              data: params => ({ term: params.term || '', customer_id: els.mCustomer.val() || '' }),
              processResults: data => ({ results: data.results || [] }),
              cache: true
            }
          });

          els.mDeal.select2({
            placeholder: 'Auftrag / Deal wählen...',
            allowClear: true,
            width: '100%',
            dropdownParent: $drawer,
            ajax: {
              url: API.selDeals,
              dataType: 'json',
              delay: 250,
              data: params => ({
                term: params.term || '',
                customer_id: els.mCustomer.val() || '',
                object_id: els.mObject.val() || '',
              }),
              processResults: data => ({ results: data.results || [] }),
              cache: false
            }
          });

          els.mProductPicker.select2({
            placeholder: 'Produkt hinzufügen...',
            allowClear: true,
            width: '100%',
            dropdownParent: $drawer,
            ajax: {
              url: API.selProducts,
              dataType: 'json',
              delay: 250,
              data: params => ({
                term: params.term || '',
                customer_id: els.mCustomer.val() || '',
                object_id: els.mObject.val() || '',
              }),
              processResults: data => ({ results: data.results || [] }),
              cache: false
            }
          });

          els.mCustomer.on('change', () => {
            els.mObject.val(null).trigger('change');
            els.mDeal.val(null).trigger('change');
            setDealInfo(null);
            state.offerDetailId = null;
          });

          els.mObject.on('change', () => {
            els.mDeal.val(null).trigger('change');
            setDealInfo(null);
            state.offerDetailId = null;
          });

          els.mDeal.on('select2:select', async (e) => {
            const deal = e.params.data || null;
            setDealInfo(deal);

            try {
              await loadDealItemsFromDeal(deal);
            } catch (err) {
              alert(err.message || 'Auftrag-Positionen konnten nicht geladen werden.');
            }
          });

          els.mDeal.on('select2:clear', () => {
            setDealInfo(null);
            state.offerDetailId = null;
          });

          els.mProductPicker.on('select2:select', (e) => {
            const d = e.params.data || {};
            addItem({ product_id: d.id, title: d.text || 'Produkt', qty: 1, unit: '', unit_price: 0 });
            els.mProductPicker.val(null).trigger('change');
          });
        })();

        function addItem(it) {
          state.items.push({
            product_id: it.product_id ?? null,
            article_product_id: it.article_product_id ?? null,
            component_id: it.component_id ?? null,
            distributor_id: it.distributor_id ?? null,
            distributor_price_id: it.distributor_price_id ?? null,
            distributor_article_no: it.distributor_article_no ?? null,
            source_item_type: it.source_item_type ?? null,
            source_item_id: it.source_item_id ?? null,
            source_payload: it.source_payload ?? null,

            title: it.title ?? 'Position',
            description: it.description ?? '',
            qty: Number(it.qty ?? 1),
            unit: it.unit ?? '',
            unit_price: Number(it.unit_price ?? 0),
            tax_rate: Number(it.tax_rate ?? 0),
            sort_order: state.items.length,
          });

          if (state.compactColumns) {
            state.items[state.items.length - 1].qty = 1;
            state.items[state.items.length - 1].unit = '';
          }

          renderItems();
        }

        function calcTotals() {
          let subtotal = 0;

          if (state.priceMode === 'total') subtotal = Number(els.mTotalNet.value || 0);
          else state.items.forEach(it => subtotal += (Number(it.qty || 0) * Number(it.unit_price || 0)));

          subtotal = Math.round(subtotal * 100) / 100;

          const taxRate = Number(els.mTaxRate.value || 0);
          const tax = Math.round((subtotal * (taxRate / 100)) * 100) / 100;
          const total = Math.round((subtotal + tax) * 100) / 100;

          els.sumSubtotal.textContent = money(subtotal);
          els.sumTax.textContent = money(tax);
          els.sumTotal.textContent = money(total);
        }

        function renderItems() {
          els.itemsBlock.classList.toggle('inv-compact-items', !!state.compactColumns);

          if (state.priceMode === 'total') {
            els.itemsBody.innerHTML = `<tr><td colspan="7" class="inv-center" style="padding:1.25rem;color:var(--inv-muted);">Gesamtbetrag-Modus aktiv</td></tr>`;
            calcTotals();
            return;
          }

          if (!state.items.length) {
            els.itemsBody.innerHTML = `<tr><td colspan="7" class="inv-center" style="padding:1.25rem;color:var(--inv-muted);">Keine Positionen</td></tr>`;
            calcTotals();
            return;
          }

          els.itemsBody.innerHTML = state.items.map((it, idx) => {
            const qty = Number(it.qty || 0);
            const up = Number(it.unit_price || 0);
            const line = Math.round((qty * up) * 100) / 100;
            const article = it.distributor_article_no
              ? `<div class="inv-muted inv-small inv-fw-600" style="margin-top:4px;">Art.-Nr.: ${escapeHtml(it.distributor_article_no)}</div>`
              : '';

            return `
                  <tr data-idx="${idx}">
                    <td>
                      <input class="inv-control" data-k="title" value="${escapeHtml(it.title)}">
                      ${article}
                      <textarea class="inv-control" data-k="description" rows="2" style="height:auto;margin-top:6px;resize:vertical;" placeholder="Beschreibung">${escapeHtml(it.description || '')}</textarea>
                    </td>

                    <td class="cell-qty"><input class="inv-control" data-k="qty" type="number" step="0.01" min="0.01" value="${it.qty}"></td>
                    <td class="cell-unit"><input class="inv-control" data-k="unit" value="${escapeHtml(it.unit || '')}"></td>

                    <td class="cell-unitprice inv-right">
                      <input class="inv-control inv-right" data-k="unit_price" type="number" step="0.01" min="0" value="${it.unit_price}">
                    </td>

                    <td class="cell-price inv-right" style="display:none;">
                      <input class="inv-control inv-right" data-k="price_only" type="number" step="0.01" min="0" value="${it.unit_price}">
                    </td>

                    <td class="cell-sum inv-right inv-fw-700"><span class="inv-line-sum">${money(line)} €</span></td>

                    <td class="inv-right">
                      <button class="inv-btn inv-btn-light inv-remove-item inv-btn-sm" data-idx="${idx}" type="button" style="border-color:rgba(220,38,38,.25);color:var(--inv-danger);">
                        <i class="fa-solid fa-xmark"></i>&nbsp; Entfernen
                      </button>
                    </td>
                  </tr>
                `;
          }).join('');

          calcTotals();
        }

        els.itemsBody.addEventListener('input', (e) => {
          if (state.priceMode !== 'items') return;

          const tr = e.target.closest('tr[data-idx]');
          if (!tr) return;

          const idx = Number(tr.getAttribute('data-idx'));
          const k = e.target.getAttribute('data-k');
          if (!k || !state.items[idx]) return;

          if (k === 'qty' || k === 'unit_price' || k === 'price_only') {
            const raw = String(e.target.value ?? '').trim();
            const val = raw === '' ? 0 : Number(raw);

            if (k === 'price_only') {
              state.items[idx].unit_price = val;
              if (state.compactColumns) {
                state.items[idx].qty = 1;
                state.items[idx].unit = '';
              }
            } else {
              state.items[idx][k] = val;
              if (state.compactColumns) {
                state.items[idx].qty = 1;
                state.items[idx].unit = '';
              }
            }
          } else {
            state.items[idx][k] = String(e.target.value ?? '');
          }

          const it = state.items[idx];
          const line = Math.round((Number(it.qty || 0) * Number(it.unit_price || 0)) * 100) / 100;
          const sumEl = tr.querySelector('.inv-line-sum');
          if (sumEl) sumEl.textContent = `${money(line)} €`;

          calcTotals();
        });

        els.itemsBody.addEventListener('click', (e) => {
          if (state.priceMode !== 'items') return;
          const btn = e.target.closest('.inv-remove-item');
          if (!btn) return;
          const idx = Number(btn.getAttribute('data-idx'));
          state.items.splice(idx, 1);
          renderItems();
        });

        els.addManualItem.addEventListener('click', () => addItem({ title: 'Manuelle Position', qty: 1, unit: '', unit_price: 0 }));
        els.mTaxRate.addEventListener('input', () => calcTotals());

        function buildPayload() {
          let items = [];

          if (state.priceMode === 'total') {
            const net = Number(els.mTotalNet.value || 0);
            items = [{
              product_id: null,
              title: String(els.mTotalTitle.value || 'Pauschale / Gesamtbetrag').trim(),
              description: null,
              qty: 1,
              unit: null,
              unit_price: net,
              tax_rate: 0,
              sort_order: 0,
            }];
          } else {
            items = state.items.map((it, i) => ({
              product_id: it.product_id ?? null,
              article_product_id: it.article_product_id ?? null,
              component_id: it.component_id ?? null,
              distributor_id: it.distributor_id ?? null,
              distributor_price_id: it.distributor_price_id ?? null,
              distributor_article_no: it.distributor_article_no ?? null,
              source_item_type: it.source_item_type ?? null,
              source_item_id: it.source_item_id ?? null,
              source_payload: it.source_payload ?? null,

              title: String(it.title || 'Position').trim(),
              description: String(it.description || '').trim() || null,
              qty: state.compactColumns ? 1 : Number(it.qty || 1),
              unit: state.compactColumns ? null : (String(it.unit || '').trim() || null),
              unit_price: Number(it.unit_price || 0),
              tax_rate: Number(it.tax_rate || 0),
              sort_order: i,
            }));
          }

          return {
            customer_id: Number(els.mCustomer.val() || 0),
            object_id: els.mObject.val() ? Number(els.mObject.val()) : null,
            deal_id: els.mDeal.val() ? Number(els.mDeal.val()) : null,
            offer_detail_id: state.offerDetailId || null,
            deal_limit_amount: state.selectedDealData ? Number(state.selectedDealData.deal_limit_amount || 0) : null,
            invoice_no: (els.mInvoiceNo.value || '').trim() || null,
            type: els.mType.value,
            status: els.mStatus.value,
            issue_date: els.mIssueDate.value,
            due_date: els.mDueDate.value || null,
            service_from: els.mServiceFrom.value || null,
            service_to: els.mServiceTo.value || null,
            currency: 'EUR',
            tax_rate: Number(els.mTaxRate.value || 0),
            notes: (els.mNotes.value || '').trim() || null,
            items,
          };
        }

        async function saveStep1() {
          const payload = buildPayload();

          if (!payload.customer_id) throw new Error('Kunde ist erforderlich.');
          if (!payload.issue_date) throw new Error('Ausstellungsdatum ist erforderlich.');
          if (!payload.type) throw new Error('Typ ist erforderlich.');
          if (!payload.status) throw new Error('Status ist erforderlich.');
          if (!payload.items.length) throw new Error('Mindestens 1 Position hinzufügen.');
          if (state.priceMode === 'total' && Number(els.mTotalNet.value || 0) <= 0) throw new Error('Rechnungsbetrag muss größer als 0 sein.');

          els.saveHint.textContent = 'Speichere...';

          if (state.editingId) {
            await apiJson(API.update.replace('__ID__', state.editingId), {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload),
            });
            state.activeInvoiceId = state.editingId;
          } else {
            const res = await apiJson(API.store, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload),
            });
            state.editingId = res.invoice_id;
            state.activeInvoiceId = res.invoice_id;
            els.drawerTitle.textContent = 'Rechnung #' + res.invoice_id;
          }

          els.activeId.textContent = 'Rechnung-ID: ' + state.activeInvoiceId;
          els.saveHint.textContent = 'Gespeichert.';
          setStep(2);
          await load();
        }

        els.saveStep1.addEventListener('click', async () => {
          try {
            await saveStep1();
          } catch (e) {
            alert(e.message || 'Speichern fehlgeschlagen');
          }
        });

        function openPdfModal(file) {
          const name = file?.stored_name || file?.original_name || 'datei';
          const viewUrl = API.viewFile.replace('__ID__', file.id);
          const downloadUrl = API.downloadFile.replace('__ID__', file.id);

          els.pdfTitle.textContent = name;
          const body = document.querySelector('.inv-pdf-body');
          if (fileIsImage(file)) {
            body.innerHTML = `<img class="inv-preview-img" src="${viewUrl}" alt="${escapeHtml(name)}">`;
            els.pdfFrame = null;
          } else {
            body.innerHTML = '<iframe id="pdfFrame" src="about:blank"></iframe>';
            els.pdfFrame = document.getElementById('pdfFrame');
            els.pdfFrame.src = viewUrl;
          }
          els.pdfOpenNew.href = viewUrl;
          els.pdfDownload.href = downloadUrl;

          els.pdfBackdrop.classList.add('active');
          els.pdfModal.classList.add('active');
          document.body.style.overflow = 'hidden';
        }

        function closePdfModal() {
          els.pdfBackdrop.classList.remove('active');
          els.pdfModal.classList.remove('active');
          if (els.pdfFrame) els.pdfFrame.src = 'about:blank';

          const profileOpen = els.profileModal && els.profileModal.classList.contains('active');
          const drawerOpen = els.drawer && els.drawer.classList.contains('active');
          if (!profileOpen && !drawerOpen) {
            document.body.style.overflow = '';
          }
        }

        els.pdfClose.addEventListener('click', closePdfModal);
        els.pdfBackdrop.addEventListener('click', closePdfModal);
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') closePdfModal();
        });

        function renderFiles() {
          const files = Array.isArray(state.uploadedFiles) ? state.uploadedFiles : [];
          if (!files.length) {
            els.filesList.innerHTML = `<div class="inv-muted inv-small inv-fw-600" style="grid-column:1/-1;">Keine Dateien vorhanden.</div>`;
            return;
          }

          els.filesList.innerHTML = files.map(f => {
            const name = f.stored_name || f.original_name || 'datei';
            const kb = Math.round((Number(f.size || 0) / 1024));
            const downloadUrl = API.downloadFile.replace('__ID__', f.id);

            return `
                  <div class="inv-file-card inv-open-pdf" data-id="${f.id}">
                    <div class="inv-file-ico"><i class="fa-solid ${fileIsImage(f) ? 'fa-file-image' : 'fa-file-pdf'}"></i></div>
                    <div style="min-width:0;flex:1;">
                      <div class="inv-fw-700 inv-trunc" title="${escapeHtml(name)}">${escapeHtml(name)}</div>
                      <div class="inv-muted inv-small inv-fw-600" style="margin-top:2px;">${kb} KB</div>
                      <div class="inv-flex inv-gap-2" style="justify-content:flex-end;margin-top:.6rem;">
                        <a class="inv-btn inv-btn-light inv-btn-sm" href="${downloadUrl}" onclick="event.stopPropagation();">
                          <i class="fa-solid fa-download"></i>&nbsp; Herunterladen
                        </a>
                        <button class="inv-btn inv-btn-danger inv-btn-sm inv-del-file" data-id="${f.id}" type="button" onclick="event.stopPropagation();">
                          <i class="fa-solid fa-trash"></i>&nbsp; Löschen
                        </button>
                      </div>
                    </div>
                  </div>
                `;
          }).join('');
        }

        function setPickedFiles(filesArr) {
          state.pickedFiles = (filesArr || []).filter(f => f && (String(f.type || '').includes('pdf') || String(f.type || '').startsWith('image/')));
          if (!state.pickedFiles.length) {
            els.pickedHint.textContent = '';
            return;
          }
          const names = state.pickedFiles.slice(0, 4).map(f => f.name).join(', ');
          const more = state.pickedFiles.length > 4 ? ` +${state.pickedFiles.length - 4} weitere` : '';
          els.pickedHint.textContent = `Ausgewählt: ${names}${more}`;
        }

        els.mFiles.addEventListener('change', (e) => {
          e.stopPropagation();
          setPickedFiles(Array.from(els.mFiles.files || []));
        });

        async function reloadActiveInvoice() {
          if (!state.activeInvoiceId) return;
          const j = await apiJson(API.show.replace('__ID__', state.activeInvoiceId));
          const inv = j.invoice || {};
          state.uploadedFiles = (inv.files || []).map(f => ({
            id: f.id,
            stored_name: f.stored_name,
            original_name: f.original_name,
            size: f.size,
            mime: f.mime,
          }));
          renderFiles();
        }

        async function uploadFiles() {
          if (!state.activeInvoiceId) throw new Error('Bitte zuerst Schritt 1 speichern.');

          const files = state.pickedFiles.length ? state.pickedFiles : Array.from(els.mFiles.files || []);
          if (!files.length) throw new Error('Bitte PDFs oder Bilder auswählen.');

          const fd = new FormData();
          for (const f of files) fd.append('files[]', f);

          await apiJson(API.upload.replace('__ID__', state.activeInvoiceId), {
            method: 'POST',
            body: fd
          });

          state.pickedFiles = [];
          els.mFiles.value = '';
          els.pickedHint.textContent = '';
          await reloadActiveInvoice();
          await load();
        }

        async function deleteFile(id) {
          if (!confirm('Datei löschen?')) return;
          await apiJson(API.deleteFile.replace('__ID__', id), { method: 'DELETE' });
          state.uploadedFiles = state.uploadedFiles.filter(x => String(x.id) !== String(id));
          renderFiles();
        }

        els.pickFiles.addEventListener('click', (e) => {
          e.stopPropagation();
          els.mFiles.click();
        });

        els.uploadFiles.addEventListener('click', async (e) => {
          e.stopPropagation();
          try {
            await uploadFiles();
          } catch (err) {
            alert(err.message || 'Upload fehlgeschlagen');
          }
        });

        els.drop.addEventListener('click', (e) => {
          if (e.target.closest('button, a, input')) return;
          els.mFiles.click();
        });

        els.drop.addEventListener('dragover', (e) => {
          e.preventDefault();
          els.drop.style.borderColor = 'rgba(22,163,74,.55)';
        });

        els.drop.addEventListener('dragleave', () => {
          els.drop.style.borderColor = 'rgba(116,178,212,.45)';
        });

        els.drop.addEventListener('drop', (e) => {
          e.preventDefault();
          els.drop.style.borderColor = 'rgba(116,178,212,.45)';
          const dropped = Array.from(e.dataTransfer?.files || []);
          if (dropped.length) setPickedFiles(dropped);
        });

        els.filesList.addEventListener('click', (e) => {
          const delBtn = e.target.closest('.inv-del-file');
          if (delBtn) {
            e.stopPropagation();
            const id = delBtn.getAttribute('data-id');
            deleteFile(id).catch(err => alert(err.message || 'Löschen fehlgeschlagen'));
            return;
          }

          const card = e.target.closest('.inv-open-pdf');
          if (card) {
            e.stopPropagation();
            const id = card.getAttribute('data-id');
            const f = (state.uploadedFiles || []).find(x => String(x.id) === String(id));
            if (f) openPdfModal(f);
          }
        });

        els.reloadFiles.addEventListener('click', async (e) => {
          e.stopPropagation();
          try {
            await reloadActiveInvoice();
          } catch (err) {
            alert(err.message || 'Neu laden fehlgeschlagen');
          }
        });

        els.backTo1.addEventListener('click', () => setStep(1));
        els.finish.addEventListener('click', () => {
          closeDrawer();
          load();
        });

        els.newBtn.addEventListener('click', () => {
          resetForm();
          openDrawer();
        });

        async function openInvoice(id, startStep = 1) {
          resetForm();
          state.editingId = id;
          state.activeInvoiceId = id;
          els.drawerTitle.textContent = 'Rechnung #' + id;
          els.activeId.textContent = 'Rechnung-ID: ' + id;
          openDrawer();

          const j = await apiJson(API.show.replace('__ID__', id));
          const inv = j.invoice || {};
          state.offerDetailId = inv.offer_detail_id || null;

          els.mInvoiceNo.value = inv.invoice_no || '';
          els.mType.value = inv.type || 'Rechnung';
          els.mStatus.value = inv.status || 'draft';

          els.mIssueDate.value = dateOnly(inv.issue_date || '');
          els.mDueDate.value = inv.due_date ? dateOnly(inv.due_date) : '';
          els.mServiceFrom.value = inv.service_from ? dateOnly(inv.service_from) : '';
          els.mServiceTo.value = inv.service_to ? dateOnly(inv.service_to) : '';
          els.mTaxRate.value = Number(inv.tax_rate || 0);
          els.mNotes.value = inv.notes || '';

          if (inv.customer) {
            const opt = new Option(customerLabel(inv.customer), inv.customer.id, true, true);
            els.mCustomer.append(opt).trigger('change');
          }

          if (inv.object) {
            const opt = new Option(objectLabel(inv.object), inv.object.id, true, true);
            els.mObject.append(opt).trigger('change');
          }

          if (inv.deal_summary || inv.deal_id) {
            const dealText = inv.deal_summary ? dealLabel(inv) : ('Auftrag #' + inv.deal_id);
            const opt = new Option(dealText, inv.deal_id || inv.deal_summary?.id, true, true);
            els.mDeal.append(opt).trigger('change');
            setDealInfo({
              id: inv.deal_id || inv.deal_summary?.id,
              text: dealText,
              offer_detail_id: inv.offer_detail_id || null,
              deal_limit_amount: inv.deal_balance?.deal_limit_amount || inv.deal_limit_amount || 0,
              invoiced_amount: inv.deal_balance?.invoiced_amount || 0,
              remaining_amount: inv.deal_balance?.remaining_amount || inv.deal_remaining_before || 0,
            });
          }

          const invItems = (inv.items || []).map((it, i) => ({
            product_id: it.product_id ?? null,
            article_product_id: it.article_product_id ?? null,
            component_id: it.component_id ?? null,
            distributor_id: it.distributor_id ?? null,
            distributor_price_id: it.distributor_price_id ?? null,
            distributor_article_no: it.distributor_article_no ?? null,
            source_item_type: it.source_item_type ?? null,
            source_item_id: it.source_item_id ?? null,
            source_payload: it.source_payload ?? null,

            title: it.title ?? 'Position',
            description: it.description ?? '',
            qty: Number(it.qty ?? 1),
            unit: it.unit ?? '',
            unit_price: Number(it.unit_price ?? 0),
            sort_order: i,
          }));

          state.items = invItems;

          if (state.compactColumns) {
            state.items = state.items.map(it => ({ ...it, qty: 1, unit: '' }));
          }

          state.uploadedFiles = (inv.files || []).map(f => ({
            id: f.id,
            stored_name: f.stored_name,
            original_name: f.original_name,
            size: f.size,
            mime: f.mime,
          }));

          renderItems();
          renderFiles();
          setStep(startStep);
        }

        function renderProfile(inv) {
          const d = inv.profile_details || {};
          const doc = d.document || {};
          const customer = d.customer || {};
          const object = d.object || {};
          const dealFull = d.deal || {};
          const offerDetail = d.offer_detail || {};
          const financial = d.financial || {};
          const texts = d.texts || {};
          const items = d.items || inv.items || [];

          const files = inv.files || [];
          const history = inv.invoice_history || [];
          const deal = inv.deal_summary;
          const balance = inv.deal_balance;

          const val = (v) => {
            if (v === null || v === undefined || v === '') return '—';
            return String(v);
          };

          const euro = (v) => `${escapeHtml(money(Number(v || 0)))} €`;

          const field = (label, value, col = 'inv-col-6') => `
      <div class="${col}">
        <div class="inv-label">${escapeHtml(label)}</div>
        <div class="inv-fw-700" style="word-break:break-word;">${escapeHtml(val(value))}</div>
      </div>
    `;

          const moneyField = (label, value, col = 'inv-col-4') => `
      <div class="${col}">
        <div class="inv-label">${escapeHtml(label)}</div>
        <div class="inv-fw-700">${euro(value)}</div>
      </div>
    `;

          const textBox = (label, value) => `
      <div style="margin-top:.75rem;">
        <div class="inv-label">${escapeHtml(label)}</div>
        <div style="white-space:pre-wrap;border:1px solid var(--inv-border);border-radius:12px;padding:.75rem;background:#f8fafc;font-weight:600;color:#334155;">
          ${escapeHtml(val(value))}
        </div>
      </div>
    `;

          const fileCards = files.length ? files.map(f => {
            const name = f.stored_name || f.original_name || 'Datei';
            const kb = Math.round((Number(f.size || 0) / 1024));
            const uploader = f.uploader ? ((f.uploader.name || '') + ' ' + (f.uploader.lastname || '')).trim() : '';
            return `
        <div class="inv-file-card inv-profile-file" data-id="${escapeHtml(f.id)}">
          <div class="inv-file-ico">
            <i class="fa-solid ${fileIsImage(f) ? 'fa-file-image' : 'fa-file-pdf'}"></i>
          </div>
          <div style="min-width:0;flex:1;">
            <div class="inv-fw-700 inv-trunc">${escapeHtml(name)}</div>
            <div class="inv-muted inv-small inv-fw-600">${escapeHtml(kb)} KB</div>
            ${uploader ? `<div class="inv-muted inv-small inv-fw-600">Upload: ${escapeHtml(uploader)}</div>` : ''}
            ${f.created_at ? `<div class="inv-muted inv-small inv-fw-600">${escapeHtml(fmtDate(f.created_at))}</div>` : ''}
          </div>
        </div>`;
          }).join('') : '<div class="inv-muted inv-fw-600">Keine Dateien vorhanden.</div>';

          const historyHtml = history.length ? history.map(h => {
            const oldS = h.old_status || h.old_values?.status || '—';
            const newS = h.new_status || h.new_values?.status || '—';

            const oldValues = h.old_values
              ? `<details style="margin-top:.5rem;"><summary class="inv-fw-700">Vorherige Werte</summary><pre style="white-space:pre-wrap;font-size:.8rem;background:#f8fafc;border:1px solid var(--inv-border);border-radius:10px;padding:.6rem;">${escapeHtml(JSON.stringify(h.old_values, null, 2))}</pre></details>`
              : '';

            const newValues = h.new_values
              ? `<details style="margin-top:.5rem;"><summary class="inv-fw-700">Neue Werte</summary><pre style="white-space:pre-wrap;font-size:.8rem;background:#f8fafc;border:1px solid var(--inv-border);border-radius:10px;padding:.6rem;">${escapeHtml(JSON.stringify(h.new_values, null, 2))}</pre></details>`
              : '';

            return `
        <div class="inv-history-line">
          <div class="inv-history-title">
            <span>${escapeHtml(h.event_type || 'Änderung')}</span>
            <span>${escapeHtml(fmtDate(h.created_at))}</span>
          </div>
          <div class="inv-history-meta">
  Mitarbeiter: ${escapeHtml(h.employee_name || ('#' + (h.employee_id || '—')))}
  Status: ${escapeHtml(oldS)} → ${escapeHtml(newS)}
  ${h.note ? 'Notiz: ' + escapeHtml(h.note) : ''}
          </div>
          ${oldValues}
          ${newValues}
        </div>`;
          }).join('') : '<div class="inv-muted inv-fw-600">Keine Historie vorhanden.</div>';

          const itemRows = items.length ? items.map((it, i) => `
      <tr>
        <td>${escapeHtml((i + 1).toString())}</td>
        <td>
          <div class="inv-fw-700">${escapeHtml(it.title || 'Position')}</div>
          ${it.description ? `<div class="inv-muted inv-small" style="white-space:pre-wrap;margin-top:.35rem;">${escapeHtml(it.description)}</div>` : ''}
          <div class="inv-muted inv-small" style="margin-top:.35rem;">
            ${it.product_group ? `Gruppe: ${escapeHtml(it.product_group)} · ` : ''}
            ${it.distributor_article_no ? `Art.-Nr.: ${escapeHtml(it.distributor_article_no)} · ` : ''}
            ${it.source_item_type ? `Quelle: ${escapeHtml(it.source_item_type)} · ` : ''}
            ${it.source_item_id ? `Source-ID: ${escapeHtml(it.source_item_id)}` : ''}
          </div>
        </td>
        <td class="inv-right">${escapeHtml(Number(it.qty || 0).toFixed(2))}</td>
        <td>${escapeHtml(it.unit || '—')}</td>
        <td class="inv-right">${euro(it.unit_price || 0)}</td>
        <td class="inv-right">${euro(it.line_total || 0)}</td>
      </tr>
    `).join('') : `
      <tr>
        <td colspan="6" class="inv-muted inv-fw-600">Keine Positionen vorhanden.</td>
      </tr>
    `;

          els.profileBody.innerHTML = `
      <div class="inv-profile-grid">

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Rechnungsdetails</h4>
          <div class="inv-grid">
            ${field('ID', doc.id || inv.id)}
            ${field('Rechnungsnr.', doc.invoice_no || inv.invoice_no)}
            ${field('Typ', doc.type || inv.type)}
            <div class="inv-col-6"><div class="inv-label">Status</div>${statusBadge(doc.status || inv.status)}</div>
            ${field('Währung', doc.currency || inv.currency)}
            ${field('Rechnungsdatum', fmtDate(doc.issue_date || inv.issue_date))}
            ${field('Fällig bis', fmtDate(doc.due_date || inv.due_date))}
            ${field('Leistung von', fmtDate(doc.service_from || inv.service_from))}
            ${field('Leistung bis', fmtDate(doc.service_to || inv.service_to))}
            ${field('Bezahlt am', fmtDate(doc.paid_at || inv.paid_at))}
            ${field('Erstellt von', doc.created_by)}
            ${field('Aktualisiert von', doc.updated_by)}
            ${field('Erstellt am', fmtDate(doc.created_at || inv.created_at))}
            ${field('Aktualisiert am', fmtDate(doc.updated_at || inv.updated_at))}
          </div>
        </div>

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Beträge</h4>
          <div class="inv-grid">
            ${moneyField('Netto', financial.subtotal ?? inv.subtotal)}
            <div class="inv-col-4"><div class="inv-label">USt.</div><div class="inv-fw-700">${escapeHtml(Number(financial.tax_rate ?? inv.tax_rate ?? 0).toFixed(2))}%</div></div>
            ${moneyField('Steuer', financial.tax_amount ?? inv.tax_amount)}
            ${moneyField('Brutto', financial.total_amount ?? inv.total_amount)}
            ${moneyField('Bezahlt', financial.paid_amount ?? inv.paid_amount)}
            ${moneyField('Offen', financial.open_amount ?? getUnpaidAmount(inv))}
          </div>

          ${deal || dealFull.id ? `<div class="inv-deal-box" style="margin-top:1rem;">
            <div class="inv-deal-mini"><span>Auftrag</span><b>${escapeHtml(dealFull.number || deal?.deal_number || dealFull.id || deal?.id || '—')}</b></div>
            <div class="inv-deal-mini"><span>Auftragswert</span><b>${euro(balance?.deal_limit_amount || financial.deal_limit_amount || inv.deal_limit_amount || dealFull.price || 0)}</b></div>
            <div class="inv-deal-mini"><span>Bereits berechnet</span><b>${euro(balance?.invoiced_amount || 0)}</b></div>
            <div class="inv-deal-mini"><span>Noch verfügbar</span><b>${euro(balance?.remaining_amount || financial.deal_remaining_after || 0)}</b></div>
          </div>` : ''}
        </div>

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Kunde</h4>
          <div class="inv-grid">
            ${field('Kunden-ID', customer.id || inv.customer_id)}
            ${field('Kundennr.', customer.customer_no)}
            ${field('Name', customer.name || customerLabel(inv.customer), 'inv-col-12')}
            ${field('Firma', customer.firma)}
            ${field('E-Mail', customer.email)}
            ${field('Telefon', customer.phone || customer.telephone)}
            ${field('Adresse', customer.full_address, 'inv-col-12')}
          </div>
        </div>

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Objekt</h4>
          <div class="inv-grid">
            ${field('Objekt-ID', object.id || inv.object_id)}
            ${field('Objektname', object.name)}
            ${field('Adresse', object.full_address, 'inv-col-12')}
            ${field('Latitude', object.lat)}
            ${field('Longitude', object.lon)}
          </div>
        </div>

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Auftrag / Deal</h4>
          <div class="inv-grid">
            ${field('Deal-ID', dealFull.id || inv.deal_id)}
            ${field('Auftragsnr.', dealFull.number || dealFull.order_number)}
            ${field('Angebotsnr.', dealFull.offer_number)}
            ${field('Produkt', dealFull.product)}
            ${field('Status', dealFull.status)}
            ${field('Deal-Status', dealFull.deal_status)}
            ${field('Projektstatus', dealFull.project_status)}
            ${field('Signiert am', fmtDate(dealFull.sign_date))}
            ${field('Bestätigt am', fmtDate(dealFull.confirmed_at))}
            ${field('Geliefert am', fmtDate(dealFull.delivered_at))}
            ${field('Mitarbeiter', dealFull.employee)}
            ${field('Geprüft von', dealFull.checked_by)}
            ${field('Reviewer', dealFull.reviewer)}
          </div>
        </div>

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">OfferDetail / Canvas Quelle</h4>
          <div class="inv-grid">
            ${field('OfferDetail-ID', offerDetail.id || inv.offer_detail_id)}
            ${field('Offer-ID', offerDetail.offer_id)}
            ${field('Folder-ID', offerDetail.offer_folder_id)}
            ${field('Angebotsnr.', offerDetail.offer_no)}
            ${field('Dokumentstatus', offerDetail.document_status)}
            ${field('Firma', offerDetail.company_name)}
            ${field('Branch', offerDetail.branch)}
            ${moneyField('Offer netto', offerDetail.total_net)}
            <div class="inv-col-4"><div class="inv-label">Offer USt.</div><div class="inv-fw-700">${escapeHtml(Number(offerDetail.tax_rate || 0).toFixed(2))}%</div></div>
            ${moneyField('Offer brutto', offerDetail.total_gross)}
          </div>
        </div>

        <div class="inv-profile-card" style="grid-column:1/-1;">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Positionen / Material</h4>
          <div style="overflow:auto;">
            <table class="inv-mini-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Position</th>
                  <th class="inv-right">Menge</th>
                  <th>Einheit</th>
                  <th class="inv-right">Einzelpreis</th>
                  <th class="inv-right">Gesamt</th>
                </tr>
              </thead>
              <tbody>${itemRows}</tbody>
            </table>
          </div>
        </div>

        <div class="inv-profile-card" style="grid-column:1/-1;">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Zahlung & Notizen</h4>
          ${textBox('Zahlungshinweis', texts.payment_note || inv.payment_note)}
          ${textBox('Interne Notizen', texts.notes || inv.notes)}
        </div>

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Dateien / Vorschau</h4>
          <div class="inv-file-grid">${fileCards}</div>
        </div>

        <div class="inv-profile-card">
          <h4 style="margin:0 0 .75rem;font-weight:900;">IDs / Verknüpfungen</h4>
          <div class="inv-grid">
            ${field('Invoice-ID', inv.id)}
            ${field('Customer-ID', inv.customer_id)}
            ${field('Object-ID', inv.object_id)}
            ${field('Deal-ID', inv.deal_id)}
            ${field('OfferDetail-ID', inv.offer_detail_id)}
            ${field('Account-ID', inv.account_id)}
            ${field('Created by', inv.created_by)}
            ${field('Updated by', inv.updated_by)}
          </div>
        </div>

        <div class="inv-profile-card" style="grid-column:1/-1;">
          <h4 style="margin:0 0 .75rem;font-weight:900;">Historie</h4>
          ${historyHtml}
        </div>

      </div>`;
        }

        async function openProfile(id) {
          state.activeProfileId = id;
          const j = await apiJson(API.show.replace('__ID__', id));
          const inv = j.invoice || {};
          els.profileTitle.textContent = 'Rechnung ' + (inv.invoice_no || ('#' + id));
          els.profileSub.textContent = customerLabel(inv.customer) + ' · ' + statusLabel(inv.status);
          els.profileEdit.dataset.id = id;

          if (els.profileCanvas) {
            els.profileCanvas.href = canvasUrl(id);
          }

          renderProfile(inv);
          els.profileBackdrop.classList.add('active');
          els.profileModal.classList.add('active');
          document.body.style.overflow = 'hidden';
        }

        function closeProfile() {
          els.profileBackdrop.classList.remove('active');
          els.profileModal.classList.remove('active');
          document.body.style.overflow = '';
        }

        els.profileClose.addEventListener('click', closeProfile);
        els.profileBackdrop.addEventListener('click', closeProfile);
        els.profileEdit.addEventListener('click', () => {
          const id = els.profileEdit.dataset.id;
          closeProfile();
          if (id) openInvoice(id, 1).catch(err => alert(err.message || 'Laden fehlgeschlagen'));
        });

        els.profileBody.addEventListener('click', (e) => {
          const card = e.target.closest('.inv-profile-file');
          if (!card) return;
          const id = card.dataset.id;
          apiJson(API.show.replace('__ID__', state.activeProfileId)).then(j => {
            const f = (j.invoice?.files || []).find(x => String(x.id) === String(id));
            if (f) openPdfModal(f);
          });
        });

        async function doDelete(id) {
          if (!confirm('Rechnung löschen?')) return;
          await apiJson(API.destroy.replace('__ID__', id), { method: 'DELETE' });
          await load();
        }

        async function updateInvoiceStatus(id, status, selectEl = null) {
          const oldValue = selectEl ? selectEl.getAttribute('data-current') || selectEl.value : null;

          try {
            if (selectEl) selectEl.classList.add('is-saving');

            await apiJson(API.status.replace('__ID__', id), {
              method: 'PATCH',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ status })
            });

            if (selectEl) {
              selectEl.setAttribute('data-current', status);
              const wrap = selectEl.closest('.inv-status-wrap');
              const dot = wrap ? wrap.querySelector('.inv-status-dot') : null;
              if (dot) {
                dot.className = `inv-status-dot ${status}`;
              }
            }

            if (state.editingId && String(state.editingId) === String(id)) {
              els.mStatus.value = status;
            }

            await load();
          } catch (e) {
            if (selectEl && oldValue !== null) {
              selectEl.value = oldValue;
            }
            alert(e.message || 'Status konnte nicht geändert werden');
          } finally {
            if (selectEl) selectEl.classList.remove('is-saving');
          }
        }

        document.addEventListener('click', (e) => {
          const canvas = e.target.closest('.inv-canvas');
          if (canvas) {
            e.stopPropagation();
            return;
          }

          const profile = e.target.closest('.inv-profile');
          const openFiles = e.target.closest('.inv-open-files');
          const edit = e.target.closest('.inv-edit');
          const del = e.target.closest('.inv-del');

          if (profile) {
            e.stopPropagation();
            openProfile(profile.getAttribute('data-id')).catch(err => alert(err.message || 'Profil konnte nicht geladen werden'));
            return;
          }

          if (openFiles) {
            e.stopPropagation();
            openInvoice(openFiles.getAttribute('data-id'), 2).catch(err => alert(err.message || 'Laden fehlgeschlagen'));
            return;
          }

          if (edit) {
            e.stopPropagation();
            openInvoice(edit.getAttribute('data-id'), 1).catch(err => alert(err.message || 'Laden fehlgeschlagen'));
            return;
          }

          if (del) {
            e.stopPropagation();
            doDelete(del.getAttribute('data-id')).catch(err => alert(err.message || 'Löschen fehlgeschlagen'));
            return;
          }

          const row = e.target.closest('.inv-row[data-id]');
          if (row) {
            openInvoice(row.getAttribute('data-id'), 2).catch(err => alert(err.message || 'Laden fehlgeschlagen'));
          }
        });

        document.addEventListener('change', (e) => {
          const select = e.target.closest('.inv-quick-status');
          if (!select) return;

          e.stopPropagation();

          const holder = select.closest('[data-id]');
          const id = holder ? holder.getAttribute('data-id') : null;
          if (!id) return;

          updateInvoiceStatus(id, select.value, select);
        });

        document.addEventListener('click', (e) => {
          const quick = e.target.closest('.inv-quick-status');
          if (quick) {
            e.stopPropagation();
          }
        });

        document.addEventListener('mousedown', (e) => {
          const quick = e.target.closest('.inv-quick-status');
          if (quick) {
            e.stopPropagation();
          }
        });

        resetForm();

        const params = new URLSearchParams(window.location.search);
        if (params.get('open_canvas') === '1') {
          state.view = 'customer';
          setTimeout(() => {
            alert('Canvas wird pro bestehender Rechnung geöffnet. Klicke bei einer Rechnung auf das lila Canvas-Symbol.');
          }, 500);
        }

        load();
      });
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
        label: 'Kundenliste',
        url: "{{ url('new_lead_view') }}",
      },
      {
        label: 'Rechnungen',
        url: "{{ url()->current() }}",
        clickable: false
      },
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush