@extends('admin.layouts.app')
@section('title', 'Kontakte')

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

$isPaginator = $contacts instanceof LengthAwarePaginator || $contacts instanceof AbstractPaginator;
$items = $isPaginator ? collect($contacts->items()) : collect($contacts);

$totalCount = $isPaginator ? $contacts->total() : $items->count();

$customerCount = (int) $items->where('type', 'Kunde')->count();
$brandCount = (int) $items->where('type', 'Hersteller')->count();
$distributorCount = (int) $items->where('type', 'Lieferant')->count();
$inquiryCount = (int) $items->where('type', 'Anfrage')->count();
$employeeCount = (int) $items->where('type', 'Mitarbeiter')->count();

$sortField = request('sort', $sort ?? 'name');
$sortDir = request('direction', $direction ?? 'asc');

$sortUrl = function ($field) use ($sortField, $sortDir) {
  $nextDirection = ($sortField === $field && $sortDir === 'asc') ? 'desc' : 'asc';

  return request()->fullUrlWithQuery([
    'sort' => $field,
    'direction' => $nextDirection,
    'page' => 1,
  ]);
};

$sortIcon = function ($field) use ($sortField, $sortDir) {
  if ($sortField !== $field) {
    return '↕';
  }

  return $sortDir === 'asc' ? '↑' : '↓';
};

$typeLabels = [
  'Kunde' => ['label' => 'Kunde', 'class' => 'customer'],
  'Hersteller' => ['label' => 'Hersteller', 'class' => 'brand'],
  'Lieferant' => ['label' => 'Lieferant', 'class' => 'supplier'],
  'Anfrage' => ['label' => 'Anfrage', 'class' => 'inquiry'],
  'Mitarbeiter' => ['label' => 'Mitarbeiter', 'class' => 'employee'],
];

$profileUrl = function ($contact) {
  return match ($contact->type ?? '') {
    'Hersteller' => url('brand_department/' . $contact->id),
    'Lieferant' => url('distributor_department/' . $contact->id),
    'Mitarbeiter' => url('employee_profile/' . $contact->id),
    'Anfrage' => url('inquiry_show/' . $contact->id),
    'Kunde' => url('new_lead_profile/' . $contact->id),
    default => '#',
  };
};

$exportQuery = request()->only(['search', 'type', 'sort', 'direction']);
  @endphp

  <style>
    :root {
      --ac-bg:#f3f4f6;
      --ac-card:#ffffff;
      --ac-text:#111827;
      --ac-muted:#6b7280;
      --ac-border:#e5e7eb;
      --ac-border-strong:#d1d5db;

      --ac-primary:#93c21c;
      --ac-primary-hover:#7baa18;
      --ac-primary-soft:#f4fae7;

      --ac-blue:#74b2d4;
      --ac-blue-soft:#eff6ff;

      --ac-success:#10b981;
      --ac-success-soft:#ecfdf5;

      --ac-warning:#f59e0b;
      --ac-warning-soft:#fffbeb;

      --ac-danger:#ef4444;
      --ac-danger-soft:#fef2f2;

      --ac-purple:#8b5cf6;
      --ac-purple-soft:#f5f3ff;

      --ac-gray:#6b7280;
      --ac-gray-soft:#f3f4f6;

      --ac-shadow-sm:0 1px 2px 0 rgb(0 0 0 / .05);
      --ac-shadow:0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
      --ac-radius:16px;
      --ac-transition:all .2s ease-in-out;
    }

    .ac-wrap {
    font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color: var(--ac-text); 
    }

    .ac-header {
      margin-bottom:18px; 
    }

    .ac-titlebar {
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap:16px;
      flex-wrap:wrap;
      margin-bottom:16px;
    }

    .ac-title {
      font-size:26px;
      font-weight:900;
      color:#111827;
      letter-spacing:-.025em;
      text-transform:uppercase;
    }

    .ac-sub {
      margin-top:4px;
      font-size:14px;
      color:var(--ac-muted);
    }

    .ac-breadcrumb {
      display:flex;
      align-items:center;
      flex-wrap:wrap;
      gap:8px;
      margin-top:10px;
      font-size:13px;
      color:var(--ac-muted);
    }

    .ac-breadcrumb a {
      color:var(--ac-muted);
      text-decoration:none;
      font-weight:700;
    }

    .ac-breadcrumb a:hover {
      color:var(--ac-text);
      text-decoration:none;
    }

    .ac-breadcrumb span.current {
      color:#111827;
      font-weight:900;
    }

    .ac-actions-inline {
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
    }

    .ac-btn {
      background:var(--ac-primary);
      color:#fff;
      border:none;
      padding:10px 16px;
      border-radius:10px;
      font-weight:900;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      text-decoration:none;
      cursor:pointer;
      transition:var(--ac-transition);
      line-height:1.2;
    }

    .ac-btn:hover {
      background:var(--ac-primary-hover);
      color:#fff;
      text-decoration:none;
    }

    .ac-btn-soft {
      background:#fff;
      color:var(--ac-text);
      border:1px solid var(--ac-border);
      padding:10px 14px;
      border-radius:10px;
      font-weight:800;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      text-decoration:none;
      cursor:pointer;
      transition:var(--ac-transition);
      line-height:1.2;
    }

    .ac-btn-soft:hover {
      background:#f9fafb;
      color:var(--ac-text);
      border-color:var(--ac-border-strong);
      text-decoration:none;
    }

    .ac-btn-ic {
      width:38px;
      height:38px;
      border-radius:10px;
      border:1px solid var(--ac-border);
      background:#fff;
      color:var(--ac-muted);
      display:inline-flex;
      align-items:center;
      justify-content:center;
      text-decoration:none;
      cursor:pointer;
      transition:var(--ac-transition);
    }

    .ac-btn-ic:hover {
      background:#f9fafb;
      color:var(--ac-text);
      border-color:var(--ac-border-strong);
      text-decoration:none;
    }

    .ac-btn-ic.primary {
      color:var(--ac-primary-hover);
      background:var(--ac-primary-soft);
      border-color:#d8efaa;
    }

    .ac-btn-ic.primary:hover {
      border-color:var(--ac-primary);
    }

    .ac-analytics {
      display:grid;
      grid-template-columns:repeat(5, minmax(0, 1fr));
      gap:14px;
      margin-bottom:18px;
    }

    @media(max-width:1300px) {
      .ac-analytics {
        grid-template-columns:repeat(3, minmax(0, 1fr));
      }
    }

    @media(max-width:900px) {
      .ac-analytics {
        grid-template-columns:repeat(2, minmax(0, 1fr));
      }
    }

    @media(max-width:620px) {
      .ac-analytics {
        grid-template-columns:1fr;
      }
    }

    .ac-stat {
      background:var(--ac-card);
      border:1px solid var(--ac-border);
      border-radius:16px;
      padding:16px;
      box-shadow:var(--ac-shadow-sm);
      display:flex;
      align-items:center;
      gap:12px;
      min-height:92px;
    }

    .ac-stat-icon {
      width:48px;
      height:48px;
      border-radius:14px;
      display:flex;
      align-items:center;
      justify-content:center;
      flex:0 0 auto;
    }

    .ac-stat-icon.total { background:var(--ac-blue-soft); color:var(--ac-blue); }
    .ac-stat-icon.customer { background:var(--ac-success-soft); color:var(--ac-success); }
    .ac-stat-icon.brand { background:var(--ac-purple-soft); color:var(--ac-purple); }
    .ac-stat-icon.supplier { background:var(--ac-warning-soft); color:#d97706; }
    .ac-stat-icon.employee { background:var(--ac-gray-soft); color:var(--ac-gray); }

    .ac-stat-meta {
      min-width:0;
    }

    .ac-stat-label {
      font-size:11px;
      font-weight:900;
      color:var(--ac-muted);
      text-transform:uppercase;
      letter-spacing:.06em;
    }

    .ac-stat-value {
      font-size:24px;
      font-weight:900;
      color:#111827;
      line-height:1.1;
      margin-top:4px;
    }

    .ac-stat-sub {
      font-size:12px;
      color:var(--ac-muted);
      margin-top:4px;
    }

    .ac-toolbar {
      background:var(--ac-card);
      border:1px solid var(--ac-border);
      border-radius:var(--ac-radius);
      padding:14px 16px;
      display:flex;
      align-items:flex-end;
      justify-content:space-between;
      flex-wrap:wrap;
      gap:14px;
      margin-bottom:16px;
      box-shadow:var(--ac-shadow-sm);
    }

    .ac-toolbar-left,
    .ac-toolbar-right {
      display:flex;
      align-items:flex-end;
      flex-wrap:wrap;
      gap:12px;
    }

    .ac-toolbar-left {
      flex:1;
    }

    .ac-filter {
      display:flex;
      flex-direction:column;
      gap:6px;
      min-width:170px;
    }

    .ac-filter.search {
      flex:1;
      min-width:280px;
    }

    .ac-label {
      font-size:11px;
      font-weight:900;
      color:var(--ac-muted);
      text-transform:uppercase;
      letter-spacing:.06em;
    }

    .ac-input {
      background:#f9fafb;
      border:1px solid var(--ac-border);
      border-radius:10px;
      padding:10px 12px 10px 38px;
      font-size:14px;
      outline:none;
      transition:var(--ac-transition);
      min-width:240px;
      width:100%;
      color:var(--ac-text);
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
      background-repeat:no-repeat;
      background-position:12px center;
      background-size:16px;
    }

    .ac-input:focus,
    .ac-select:focus {
      background:#fff;
      border-color:var(--ac-primary);
      box-shadow:0 0 0 3px var(--ac-primary-soft);
    }

    .ac-select {
      background:#f9fafb;
      border:1px solid var(--ac-border);
      border-radius:10px;
      padding:10px 12px;
      font-size:14px;
      outline:none;
      min-width:180px;
      color:var(--ac-text);
      transition:var(--ac-transition);
    }

    .ac-card {
      background:#fff;
      border:1px solid var(--ac-border);
      border-radius:16px;
      box-shadow:var(--ac-shadow-sm);
      overflow:hidden;
    }

    .ac-list-head {
      display:grid;
      grid-template-columns:90px minmax(220px, 1.2fr) minmax(170px, .9fr) minmax(150px, .8fr) minmax(180px, 1fr) minmax(230px, 1.2fr) minmax(130px, .8fr);
      gap:14px;
      align-items:center;
      padding:16px 16px 10px 16px;
      color:var(--ac-muted);
      font-size:11px;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.06em;
    }

    .ac-list-head a {
      color:var(--ac-muted);
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:5px;
    }

    .ac-list-head a:hover {
      color:var(--ac-text);
      text-decoration:none;
    }

    @media(max-width:1280px) {
      .ac-list-head {
        display:none;
      }
    }

    .ac-list {
      display:flex;
      flex-direction:column;
      gap:12px;
      padding:0 0 16px 0;
    }

    .ac-item {
      background:#fff;
      border:1px solid var(--ac-border);
      border-radius:var(--ac-radius);
      margin:0 16px;
      overflow:hidden;
      transition:var(--ac-transition);
    }

    .ac-item:hover {
      border-color:var(--ac-primary);
      box-shadow:var(--ac-shadow);
    }

    .ac-item-row {
      display:grid;
      grid-template-columns:90px minmax(220px, 1.2fr) minmax(170px, .9fr) minmax(150px, .8fr) minmax(180px, 1fr) minmax(230px, 1.2fr) minmax(130px, .8fr);
      gap:14px;
      align-items:center;
      padding:16px;
    }

    @media(max-width:1280px) {
      .ac-item-row {
        grid-template-columns:1fr;
      }
    }

    .ac-cell {
      min-width:0;
    }

    .ac-cell-title {
      display:none;
      font-size:11px;
      font-weight:900;
      color:var(--ac-muted);
      text-transform:uppercase;
      letter-spacing:.06em;
      margin-bottom:4px;
    }

    @media(max-width:1280px) {
      .ac-cell-title {
        display:block;
      }
    }

    .ac-id {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:56px;
      height:36px;
      padding:0 12px;
      border-radius:10px;
      background:var(--ac-blue-soft);
      color:#2b7ca3;
      font-size:13px;
      font-weight:900;
    }

    .ac-main {
      display:flex;
      align-items:flex-start;
      gap:12px;
      min-width:0;
    }

    .ac-avatar {
      width:46px;
      height:46px;
      border-radius:14px;
      background:var(--ac-primary-soft);
      color:var(--ac-primary-hover);
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:16px;
      font-weight:900;
      flex:0 0 auto;
      border:1px solid #d8efaa;
      text-transform:uppercase;
    }

    .ac-main-text {
      min-width:0;
    }

    .ac-name {
      font-size:15px;
      font-weight:900;
      color:#111827;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }

    .ac-subtext {
      font-size:13px;
      color:var(--ac-muted);
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      margin-top:3px;
    }

    .ac-info {
      display:flex;
      flex-direction:column;
      gap:4px;
      min-width:0;
    }

    .ac-info-main {
      font-size:14px;
      font-weight:800;
      color:#111827;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }

    .ac-info-muted {
      font-size:13px;
      color:var(--ac-muted);
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }

    .ac-type-pill {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:6px 10px;
      border-radius:999px;
      font-size:12px;
      font-weight:900;
      white-space:nowrap;
    }

    .ac-type-pill.customer { background:var(--ac-success-soft); color:#047857; }
    .ac-type-pill.brand { background:var(--ac-purple-soft); color:#6d28d9; }
    .ac-type-pill.supplier { background:var(--ac-warning-soft); color:#b45309; }
    .ac-type-pill.inquiry { background:var(--ac-blue-soft); color:#2563eb; }
    .ac-type-pill.employee { background:var(--ac-gray-soft); color:#374151; }

    .ac-status-wrap {
      display:flex;
      align-items:center;
      flex-wrap:wrap;
      gap:6px;
      margin-top:7px;
    }

    .ac-small-pill {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:5px 9px;
      border-radius:999px;
      font-size:11px;
      font-weight:900;
      white-space:nowrap;
    }

    .ac-small-pill.info { background:var(--ac-blue-soft); color:#2563eb; }
    .ac-small-pill.success { background:var(--ac-success-soft); color:#047857; }
    .ac-small-pill.warning { background:var(--ac-warning-soft); color:#b45309; }
    .ac-small-pill.danger { background:var(--ac-danger-soft); color:#b91c1c; }
    .ac-small-pill.secondary { background:var(--ac-gray-soft); color:#374151; }

    .ac-tier {
      display:inline-flex;
      align-items:center;
      gap:5px;
      font-size:11px;
      color:var(--ac-muted);
      font-weight:800;
    }

    .ac-tier img {
      width:26px;
      height:26px;
      object-fit:contain;
    }

    .ac-actions {
      display:flex;
      justify-content:flex-end;
      align-items:center;
      gap:8px;
      flex-wrap:wrap;
    }

    @media(max-width:1280px) {
      .ac-actions {
        justify-content:flex-start;
      }
    }

    .ac-empty {
      text-align:center;
      padding:60px;
      color:var(--ac-muted);
      background:#fff;
      border:1px dashed var(--ac-border);
      border-radius:16px;
      margin:16px;
    }

    .ac-pagination {
      margin-top:18px;
      background:#fff;
      border:1px solid var(--ac-border);
      border-radius:14px;
      padding:14px 16px;
      box-shadow:var(--ac-shadow-sm);
    }

    .ac-pagination .pagination {
      margin:0;
      display:flex;
      flex-wrap:wrap;
      gap:6px;
    }

    .ac-pagination .page-item .page-link {
      border-radius:10px !important;
      border:1px solid var(--ac-border);
      color:var(--ac-text);
      padding:8px 12px;
      line-height:1.1;
      box-shadow:none !important;
    }

    .ac-pagination .page-item.active .page-link {
      background:var(--ac-primary);
      border-color:var(--ac-primary);
      color:#fff;
    }

    .ac-pagination .page-item.disabled .page-link {
      color:#9ca3af;
      background:#f9fafb;
    }

    .ac-toast-wrap {
      position:fixed;
      right:20px;
      bottom:20px;
      z-index:9999;
      display:flex;
      flex-direction:column;
      gap:10px;
      pointer-events:none;
    }

    .ac-toast {
      pointer-events:auto;
      min-width:280px;
      max-width:360px;
      background:#fff;
      border:1px solid var(--ac-border);
      border-radius:14px;
      box-shadow:var(--ac-shadow);
      padding:12px;
      display:flex;
      gap:10px;
      align-items:flex-start;
      animation:acToastIn .3s cubic-bezier(.175,.885,.32,1.275) forwards;
    }

    @keyframes acToastIn {
      from { transform:translateX(100%); opacity:0; }
      to { transform:translateX(0); opacity:1; }
    }

    .ac-toast-ic {
      width:34px;
      height:34px;
      border-radius:12px;
      display:flex;
      align-items:center;
      justify-content:center;
      flex:0 0 auto;
    }

    .ac-toast-ic.ok {
      background:var(--ac-success-soft);
      color:var(--ac-success);
    }

    .ac-toast-ic.bad {
      background:var(--ac-danger-soft);
      color:var(--ac-danger);
    }

    .ac-toast-title {
      font-weight:900;
      font-size:13px;
      color:#111827;
      margin:0;
    }

    .ac-toast-msg {
      font-size:12px;
      color:#374151;
      margin:4px 0 0 0;
      line-height:1.4;
    }

    .ac-toast-x {
      margin-left:auto;
      background:transparent;
      border:none;
      cursor:pointer;
      color:var(--ac-muted);
      font-size:18px;
      line-height:1;
    }

    @media(max-width:768px) {
      .ac-wrap {
        padding:20px 14px;
        margin:0 auto;
      }

      .ac-header {
        margin-top:40px;
      }

      .ac-toolbar-left,
      .ac-toolbar-right {
        width:100%;
      }

      .ac-filter,
      .ac-filter.search,
      .ac-input,
      .ac-select,
      .ac-btn,
      .ac-btn-soft {
        width:100%;
      }
    }
  </style>
@endsection

@section('content')
    @include('admin.contacts._tabs')
<div class="ac-wrap">
    {{-- CI-Vereinheitlichung 2026-07-15: Alt-Kopf (ac-titlebar) durch das gemeinsame Bauteil ersetzt. --}}
    <x-page-head title="Kontakte"
        sub="Zentrale Übersicht für Kunden, Hersteller, Lieferanten, Anfragen und Mitarbeiter."
        current="Kontakte" style="margin:0 0 14px;">
        <x-slot:actions>
            <a href="{{ route('all.contacts.export', $exportQuery) }}" class="ac-btn">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <path d="M7 10l5 5 5-5"/>
                    <path d="M12 15V3"/>
                </svg>
                Als CSV exportieren
            </a>
        </x-slot:actions>
    </x-page-head>

    <div class="ac-analytics">
        <div class="ac-stat">
            <div class="ac-stat-icon total">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18"/>
                </svg>
            </div>
            <div class="ac-stat-meta">
                <div class="ac-stat-label">Gesamt</div>
                <div class="ac-stat-value">{{ $totalCount }}</div>
                <div class="ac-stat-sub">Kontakte insgesamt</div>
            </div>
        </div>

        <div class="ac-stat">
            <div class="ac-stat-icon customer">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21a8 8 0 0 0-16 0"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="ac-stat-meta">
                <div class="ac-stat-label">Kunden</div>
                <div class="ac-stat-value">{{ $customerCount }}</div>
                <div class="ac-stat-sub">Auf dieser Seite</div>
            </div>
        </div>

        <div class="ac-stat">
            <div class="ac-stat-icon brand">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18"/>
                    <path d="M5 21V7l8-4v18"/>
                    <path d="M19 21V11l-6-4"/>
                </svg>
            </div>
            <div class="ac-stat-meta">
                <div class="ac-stat-label">Hersteller</div>
                <div class="ac-stat-value">{{ $brandCount }}</div>
                <div class="ac-stat-sub">Auf dieser Seite</div>
            </div>
        </div>

        <div class="ac-stat">
            <div class="ac-stat-icon supplier">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10 17h4V5H2v12h3"/>
                    <path d="M14 17h1m4 0h3v-6l-3-4h-5"/>
                    <circle cx="7.5" cy="17.5" r="2.5"/>
                    <circle cx="17.5" cy="17.5" r="2.5"/>
                </svg>
            </div>
            <div class="ac-stat-meta">
                <div class="ac-stat-label">Lieferanten</div>
                <div class="ac-stat-value">{{ $distributorCount }}</div>
                <div class="ac-stat-sub">Auf dieser Seite</div>
            </div>
        </div>

        <div class="ac-stat">
            <div class="ac-stat-icon employee">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="ac-stat-meta">
                <div class="ac-stat-label">Mitarbeiter</div>
                <div class="ac-stat-value">{{ $employeeCount }}</div>
                <div class="ac-stat-sub">Anfragen: {{ $inquiryCount }}</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('all.contacts') }}" class="ac-toolbar">
        <div class="ac-toolbar-left">
            <div class="ac-filter search">
                <label class="ac-label">Suche</label>
                <input
                    type="text"
                    name="search"
                    class="ac-input"
                    placeholder="Name, Nachname, Telefon, E-Mail oder Adresse suchen..."
                    value="{{ $search ?? request('search') }}"
                >
            </div>

            <div class="ac-filter">
                <label class="ac-label">Typ</label>
                <select name="type" class="ac-select">
                    <option value="">Alle Typen</option>
                    <option value="Kunde" {{ ($typeFilter ?? request('type')) === 'Kunde' ? 'selected' : '' }}>Kunde</option>
                    <option value="Hersteller" {{ ($typeFilter ?? request('type')) === 'Hersteller' ? 'selected' : '' }}>Hersteller</option>
                    <option value="Lieferant" {{ ($typeFilter ?? request('type')) === 'Lieferant' ? 'selected' : '' }}>Lieferant</option>
                    <option value="Anfrage" {{ ($typeFilter ?? request('type')) === 'Anfrage' ? 'selected' : '' }}>Anfrage</option>
                    <option value="Mitarbeiter" {{ ($typeFilter ?? request('type')) === 'Mitarbeiter' ? 'selected' : '' }}>Mitarbeiter</option>
                </select>
            </div>
        </div>

        <div class="ac-toolbar-right">
            <button type="submit" class="ac-btn-soft">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 21l-6-6"/>
                    <circle cx="10" cy="10" r="7"/>
                </svg>
                Suchen
            </button>

            @if(request('search') || request('type') || request('sort') || request('direction'))
                <a href="{{ route('all.contacts') }}" class="ac-btn-soft">Zurücksetzen</a>
            @endif
        </div>
    </form>

    <div class="ac-card">
        <div class="ac-list-head">
            <div><a href="{{ $sortUrl('id') }}">ID <span>{{ $sortIcon('id') }}</span></a></div>
            <div><a href="{{ $sortUrl('name') }}">Kontakt <span>{{ $sortIcon('name') }}</span></a></div>
            <div><a href="{{ $sortUrl('type') }}">Typ <span>{{ $sortIcon('type') }}</span></a></div>
            <div><a href="{{ $sortUrl('phone') }}">Telefon <span>{{ $sortIcon('phone') }}</span></a></div>
            <div><a href="{{ $sortUrl('email') }}">E-Mail <span>{{ $sortIcon('email') }}</span></a></div>
            <div><a href="{{ $sortUrl('address') }}">Adresse <span>{{ $sortIcon('address') }}</span></a></div>
            <div style="text-align:right;">Aktionen</div>
        </div>

        <div class="ac-list">
            @forelse($contacts as $contact)
                @php
  $type = $contact->type ?? 'Kontakt';
  $typeMeta = $typeLabels[$type] ?? ['label' => $type, 'class' => 'employee'];

  $fullName = trim(($contact->name ?? '') . ' ' . ($contact->lastname ?? ''));
  $initials = collect(explode(' ', $fullName))
    ->filter()
    ->take(2)
    ->map(fn($part) => mb_substr($part, 0, 1))
    ->implode('');

  $initials = $initials ?: '?';

  $statusBadgeClass = match ($contact->purchase_status_badge ?? '') {
    'badge-info' => 'info',
    'badge-success' => 'success',
    'badge-warning' => 'warning',
    'badge-danger' => 'danger',
    default => 'secondary',
  };

  $tier = $contact->tier ?? 'none';
  $tierIconAbs = public_path("icons/{$tier}.png");
  $tierIconUrl = asset("icons/{$tier}.png");

  $url = $profileUrl($contact);
                @endphp

                <div class="ac-item">
                    <div class="ac-item-row">
                        <div class="ac-cell">
                            <div class="ac-cell-title">ID</div>
                            <span class="ac-id">#{{ $contact->id }}</span>
                        </div>

                        <div class="ac-cell">
                            <div class="ac-cell-title">Kontakt</div>
                            <div class="ac-main">
                                <div class="ac-avatar">{{ $initials }}</div>
                                <div class="ac-main-text">
                                    <div class="ac-name">{{ $fullName ?: ($contact->name ?? '—') }}</div>
                                    <div class="ac-subtext">
                                        {{ $contact->lastname ? 'Nachname: ' . $contact->lastname : 'Kein Nachname' }}
                                    </div>

                                    @if(in_array(strtolower((string) $type), ['kunde', 'customer'], true))
                                        <div class="ac-status-wrap">
                                            @if($contact->purchase_status_label)
                                                <span class="ac-small-pill {{ $statusBadgeClass }}">
                                                    {{ $contact->purchase_status_label }}
                                                </span>
                                            @endif

                                            @if($tier !== 'none' && file_exists($tierIconAbs))
                                                <span class="ac-tier" title="{{ $contact->tier_label }}">
                                                    <img src="{{ $tierIconUrl }}" alt="{{ $contact->tier_label }}" loading="lazy" decoding="async">
                                                    {{ $contact->tier_label }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="ac-cell">
                            <div class="ac-cell-title">Typ</div>
                            <span class="ac-type-pill {{ $typeMeta['class'] }}">
                                {{ $typeMeta['label'] }}
                            </span>
                        </div>

                        <div class="ac-cell">
                            <div class="ac-cell-title">Telefon</div>
                            <div class="ac-info">
                                @if(!empty($contact->phone))
                                    <a href="tel:{{ preg_replace('/\s+/', '', $contact->phone) }}" class="ac-info-main">
                                        {{ $contact->phone }}
                                    </a>
                                @else
                                    <div class="ac-info-muted">Keine Telefonnummer</div>
                                @endif
                            </div>
                        </div>

                        <div class="ac-cell">
                            <div class="ac-cell-title">E-Mail</div>
                            <div class="ac-info">
                                @if(!empty($contact->email))
                                    <a href="mailto:{{ $contact->email }}" class="ac-info-main">
                                        {{ $contact->email }}
                                    </a>
                                @else
                                    <div class="ac-info-muted">Keine E-Mail</div>
                                @endif
                            </div>
                        </div>

                        <div class="ac-cell">
                            <div class="ac-cell-title">Adresse</div>
                            <div class="ac-info">
                                <div class="ac-info-main" title="{{ $contact->address ?? '' }}">
                                    {{ $contact->address ?: '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="ac-cell">
                            <div class="ac-cell-title">Aktionen</div>
                            <div class="ac-actions">
                                <a href="{{ $url }}" class="ac-btn-ic primary" target="_blank" title="Profil ansehen">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>

                                @if(!empty($contact->email))
                                    <a href="mailto:{{ $contact->email }}" class="ac-btn-ic" title="E-Mail schreiben">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                            <path d="M22 6l-10 7L2 6"/>
                                        </svg>
                                    </a>
                                @endif

                                @if(!empty($contact->phone))
                                    <a href="tel:{{ preg_replace('/\s+/', '', $contact->phone) }}" class="ac-btn-ic" title="Anrufen">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 16.92V19a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3 4.18 2 2 0 0 1 5 2h2.09a2 2 0 0 1 2 1.72l.3 2.11a2 2 0 0 1-.57 1.72l-1.27 1.27a16 16 0 0 0 6.91 6.91l1.27-1.27a2 2 0 0 1 1.72-.57l2.11.3A2 2 0 0 1 22 16.92z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="ac-empty">
                    Keine Kontakte gefunden.
                </div>
            @endforelse
        </div>
    </div>

    @if($isPaginator && method_exists($contacts, 'links') && $contacts->hasPages())
        <div class="ac-pagination">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                <div style="font-size:12px;color:#6b7280;">
                    Zeige <strong>{{ $contacts->firstItem() ?? 0 }}</strong>
                    bis <strong>{{ $contacts->lastItem() ?? 0 }}</strong>
                    von <strong>{{ $contacts->total() }}</strong> Kontakten
                </div>

                <div>
                    {{ $contacts->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @endif
</div>

<div class="ac-toast-wrap" id="ac-toast-wrap"></div>
@endsection

@section('script')
  <script src="{{ asset('js/select2.min.js') }}"></script>

  <script>
      function acToast(kind, title, msg) {
          const wrap = document.getElementById('ac-toast-wrap');
          if (!wrap) return;

          const icons = {
              ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
              bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
          };

          const el = document.createElement('div');
          el.className = 'ac-toast';
          el.innerHTML = `
              <div class="ac-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
              <div style="flex:1;">
                  <p class="ac-toast-title">${title}</p>
                  <p class="ac-toast-msg">${msg}</p>
              </div>
              <button class="ac-toast-x" type="button" onclick="this.parentElement.remove()">×</button>
          `;

          wrap.appendChild(el);

          setTimeout(() => {
              try { el.remove(); } catch(e) {}
          }, 4000);
      }

      document.addEventListener('DOMContentLoaded', function () {
          if (window.$ && $.fn.select2) {
              $('.ac-select').select2({
                  width: '100%',
                  minimumResultsForSearch: Infinity
              });
          }

          @if(Session::has('update_msg'))
              acToast('ok', 'Aktualisiert', @json(session('update_msg')));
          @endif

          @if(Session::has('updated_msg'))
              acToast('ok', 'Aktualisiert', @json(session('updated_msg')));
          @endif

          @if(Session::has('save_msg'))
              acToast('ok', 'Gespeichert', @json(session('save_msg')));
          @endif

          @if(Session::has('delete_msg'))
              acToast('bad', 'Gelöscht', @json(session('delete_msg')));
          @endif
      });
  </script>

  <script>
    window.GlobalBreadcrumbs = [
      {
        label: 'Dashboard',
        url: "{{ url('/') }}"
      }, 
      {
        label: 'Kontaktlisten',
        url: "{{ url()->current() }}",
        clickable: false
      }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endsection