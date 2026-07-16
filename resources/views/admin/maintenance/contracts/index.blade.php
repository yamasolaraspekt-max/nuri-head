{{-- resources/views/admin/maintenance/contracts/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Wartungsverträge')

@php
  use Illuminate\Pagination\AbstractPaginator;
  use Illuminate\Pagination\LengthAwarePaginator;
  use Illuminate\Support\Carbon;
  use Illuminate\Support\Facades\Route;

  $isPaginator = $contracts instanceof LengthAwarePaginator || $contracts instanceof AbstractPaginator;
  $contractItems = $isPaginator ? collect($contracts->items()) : collect($contracts);

  $statusOptions = [
    'draft' => 'Entwurf',
    'active' => 'Aktiv',
    'inactive' => 'Inaktiv',
    'cancelled' => 'Gekündigt',
  ];

  $statusColumns = [
    'draft' => ['label' => 'Entwurf', 'icon' => 'fa-regular fa-pen-to-square'],
    'active' => ['label' => 'Aktiv', 'icon' => 'fa-solid fa-circle-check'],
    'inactive' => ['label' => 'Inaktiv', 'icon' => 'fa-regular fa-circle-pause'],
    'cancelled' => ['label' => 'Gekündigt', 'icon' => 'fa-solid fa-ban'],
  ];

  $intervalOptions = [
    'yearly' => 'Jährlich',
    'monthly' => 'Monatlich',
    'custom' => 'Individuell',
  ];

  $totalCount = $isPaginator ? $contracts->total() : $contractItems->count();
  $activeCount = (int) $contractItems->where('status', 'active')->count();
  $draftCount = (int) $contractItems->where('status', 'draft')->count();
  $inactiveCount = (int) $contractItems->whereIn('status', ['inactive', 'cancelled'])->count();

  $upcomingCount = (int) $contractItems->filter(function ($contract) {
    $date = $contract->next_service_date ?? $contract->end_date ?? null;
    if (!$date)
      return false;

    try {
      return Carbon::parse($date)->between(now()->startOfDay(), now()->copy()->addDays(30)->endOfDay());
    } catch (\Throwable $e) {
      return false;
    }
  })->count();

  $routeIndex = Route::has('admin.maintenance.contracts.index') ? route('admin.maintenance.contracts.index') : url('/admin/maintenance/contracts');
  $routeCreate = Route::has('admin.maintenance.contracts.create') ? route('admin.maintenance.contracts.create') : url('/admin/maintenance/contracts/create');
  $routeBulkStatus = Route::has('admin.maintenance.contracts.bulk-status') ? route('admin.maintenance.contracts.bulk-status') : null;
  $routeBulkDelete = Route::has('admin.maintenance.contracts.bulk-delete') ? route('admin.maintenance.contracts.bulk-delete') : null;
  $routeKanbanUpdate = Route::has('admin.maintenance.contracts.kanban-update') ? route('admin.maintenance.contracts.kanban-update') : null;
  $routeKanbanFeed = Route::has('admin.maintenance.contracts.kanban_feed') ? route('admin.maintenance.contracts.kanban_feed') : null;
  $routeCalendarFeed = Route::has('admin.maintenance.contracts.calendar_feed') ? route('admin.maintenance.contracts.calendar_feed') : null;
  $routeIncoming = Route::has('admin.maintenance.contracts.incoming') ? route('admin.maintenance.contracts.incoming') : null;

  $baseUrl = url('/admin/maintenance/contracts');

  $contractPayload = $contractItems->map(function ($contract) use ($statusOptions, $baseUrl) {
    $lead = $contract->lead ?? null;
    $alt = $contract->alternative ?? null;
    $asset = $contract->asset ?? null;
    $resp = $contract->responsibleEmployee ?? null;

    $customerName = null;
    if ($lead) {
      $customerName = $lead->firma
        ?? trim(($lead->name ?? $lead->vorname ?? '') . ' ' . ($lead->lastname ?? $lead->nachname ?? ''));
      $customerName = trim((string) $customerName) !== '' ? $customerName : null;
    }

    $addressText = null;
    if ($alt) {
      $addressText = $alt->full_address
        ?? trim(($alt->street ?? '') . ', ' . ($alt->postcode ?? '') . ' ' . ($alt->city ?? ''));
      $addressText = trim((string) $addressText) !== '' ? $addressText : null;
    }

    if (!$addressText && $asset && is_array($asset->technical_data ?? null)) {
      $addressText = $asset->technical_data['installationAddressText']
        ?? ($asset->technical_data['installationLocation']['addressText'] ?? null)
        ?? ($asset->technical_data['installationLocation']['notes'] ?? null);
      $addressText = trim((string) $addressText) !== '' ? $addressText : null;
    }

    if (!$addressText && $lead) {
      $addressText = trim(($lead->street ?? '') . ', ' . ($lead->postcode ?? '') . ' ' . ($lead->city ?? ''));
      $addressText = trim((string) $addressText) !== '' ? $addressText : null;
    }

    $contractTitle = trim((string) ($contract->title ?? ''));
    if ($contractTitle === '' && $asset)
      $contractTitle = trim((string) ($asset->title ?? ''));
    if ($contractTitle === '')
      $contractTitle = $contract->contract_no ?? 'Wartungsvertrag';

    $productParts = [];
    if ($asset && ($asset->manufacturer_attach ?? null))
      $productParts[] = $asset->manufacturer_attach;
    if ($asset && ($asset->manufacturer ?? null))
      $productParts[] = $asset->manufacturer;
    if ($asset && ($asset->model ?? null))
      $productParts[] = $asset->model;
    if ($asset && ($asset->title ?? null))
      $productParts[] = $asset->title;
    $productLabel = trim(implode(' · ', array_filter($productParts)));

    $nextService = $contract->next_service_date ?? $contract->end_date ?? null;
    $nextServiceIso = null;
    $nextServiceDisplay = '–';
    $daysTo = null;

    if ($nextService) {
      try {
        $date = Carbon::parse($nextService)->startOfDay();
        $nextServiceIso = $date->toDateString();
        $nextServiceDisplay = $date->format('d.m.Y');
        $daysTo = now()->startOfDay()->diffInDays($date, false);
      } catch (\Throwable $e) {
        $nextServiceIso = null;
      }
    }

    $status = $contract->status ?: 'draft';
    $price = null;
    if (!is_null($contract->price ?? null)) {
      $price = number_format((float) $contract->price, 2, ',', '.') . ' ' . ($contract->currency ?? 'EUR');
    }

    return [
      'id' => $contract->id,
      'contract_no' => $contract->contract_no,
      'title' => $contractTitle,
      'customer' => $customerName ?: '–',
      'customer_no' => $lead->customer_no ?? null,
      'customer_profile_url' => $lead
        ? (Route::has('customers.show')
          ? route('customers.show', $lead->id)
          : (Route::has('admin.customers.show')
            ? route('admin.customers.show', $lead->id)
            : (Route::has('customer.profile')
              ? route('customer.profile', $lead->id)
              : url('/customer/profile/' . $lead->id))))
        : null,
      'responsible' => ($resp->full_name ?? null) ?: ($resp->name ?? '–'),
      'address' => $addressText ?: '–',
      'product' => $productLabel ?: '–',
      'interval_type' => $contract->interval_type ?: 'yearly',
      'interval_months' => $contract->interval_months,
      'next_service_date' => $nextServiceIso,
      'next_service_display' => $nextServiceDisplay,
      'days_to' => $daysTo,
      'status' => $status,
      'status_label' => $statusOptions[$status] ?? ucfirst($status),
      'price' => $price,
      'description' => $contract->description ?? null,
      'show_url' => (Route::has('admin.maintenance.contracts.show') ? route('admin.maintenance.contracts.show', $contract->id) : $baseUrl . '/' . $contract->id),
      'edit_url' => (Route::has('admin.maintenance.contracts.edit') ? route('admin.maintenance.contracts.edit', $contract->id) : $baseUrl . '/' . $contract->id . '/edit'),
    ];
  })->values();
@endphp

@once
  @push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
    <style>
      :root {
        --mc-bg: #f3f4f6;
        --mc-card: #fff;
        --mc-text: #111827;
        --mc-muted: #6b7280;
        --mc-border: #e5e7eb;
        --mc-primary: #93c21c;
        --mc-primary-dark: #7baa18;
        --mc-primary-soft: #f4fae7;
        --mc-blue: #74b2d4;
        --mc-blue-soft: #eff6ff;
        --mc-green: #10b981;
        --mc-green-soft: #ecfdf5;
        --mc-yellow: #f59e0b;
        --mc-yellow-soft: #fffbeb;
        --mc-red: #ef4444;
        --mc-red-soft: #fef2f2;
        --mc-gray-soft: #f9fafb;
        --mc-shadow: 0 18px 45px -24px rgba(15, 23, 42, .55);
        --mc-shadow-soft: 0 1px 2px rgba(15, 23, 42, .06);
        --mc-radius: 16px;
      }

      .mc-wrap {
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: var(--mc-text);
      }

      .mc-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
      }

      .mc-title {
        font-size: 27px;
        font-weight: 900;
        letter-spacing: -.035em;
        margin: 0;
      }

      .mc-subtitle {
        color: var(--mc-muted);
        font-size: 14px;
        margin-top: 5px;
      }

      .mc-breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 10px;
        color: var(--mc-muted);
        font-size: 13px;
      }

      .mc-breadcrumb a {
        color: var(--mc-muted);
        text-decoration: none;
        font-weight: 800;
      }

      .mc-breadcrumb a:hover {
        color: var(--mc-text)
      }

      .mc-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
      }

      .mc-btn,
      .mc-btn-soft,
      .mc-icon-btn {
        border: 0;
        text-decoration: none;
        cursor: pointer;
        font-weight: 900;
        transition: .18s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
      }

      .mc-btn {
        background: var(--mc-primary);
        color: #fff;
        padding: 10px 16px;
        border-radius: 11px;
        box-shadow: var(--mc-shadow-soft);
      }

      .mc-btn:hover {
        background: var(--mc-primary-dark);
        color: #fff;
        text-decoration: none
      }

      .mc-btn-soft {
        background: #fff;
        color: #1f2937;
        border: 1px solid var(--mc-border);
        padding: 10px 14px;
        border-radius: 11px;
      }

      .mc-btn-soft:hover {
        background: #f9fafb;
        color: #111827;
        text-decoration: none
      }

      .mc-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid var(--mc-border);
        background: #fff;
        color: #6b7280;
      }

      .mc-icon-btn:hover {
        background: #f9fafb;
        color: #111827;
        text-decoration: none
      }

      .mc-icon-btn.primary {
        background: var(--mc-primary-soft);
        border-color: #d8edaa;
        color: #4d7c0f
      }

      .mc-icon-btn.warning {
        background: var(--mc-yellow-soft);
        border-color: #fde68a;
        color: #b45309
      }

      .mc-icon-btn.danger {
        background: var(--mc-red-soft);
        border-color: #fecaca;
        color: #b91c1c
      }

      .mc-view-toggle {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        border: 1px solid var(--mc-border);
        border-radius: 13px;
        background: #fff;
      }

      .mc-view-toggle button {
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: var(--mc-muted);
        padding: 9px 13px;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
      }

      .mc-view-toggle button.is-active {
        background: var(--mc-primary-soft);
        color: #365314;
      }

      .mc-stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 16px;
      }

      @media(max-width:1300px) {
        .mc-stats {
          grid-template-columns: repeat(3, minmax(0, 1fr))
        }
      }

      @media(max-width:850px) {
        .mc-stats {
          grid-template-columns: repeat(2, minmax(0, 1fr))
        }
      }

      @media(max-width:620px) {
        .mc-stats {
          grid-template-columns: 1fr
        }
      }

      .mc-stat {
        background: #fff;
        border: 1px solid var(--mc-border);
        border-radius: var(--mc-radius);
        padding: 16px;
        box-shadow: var(--mc-shadow-soft);
        display: flex;
        gap: 13px;
        align-items: center;
        min-height: 92px;
      }

      .mc-stat-ic {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-size: 18px;
      }

      .mc-stat-ic.total {
        background: var(--mc-blue-soft);
        color: var(--mc-blue)
      }

      .mc-stat-ic.active {
        background: var(--mc-green-soft);
        color: #047857
      }

      .mc-stat-ic.draft {
        background: var(--mc-yellow-soft);
        color: #b45309
      }

      .mc-stat-ic.inactive {
        background: var(--mc-red-soft);
        color: #b91c1c
      }

      .mc-stat-ic.upcoming {
        background: #f3f4f6;
        color: #4b5563
      }

      .mc-stat-label {
        font-size: 11px;
        color: var(--mc-muted);
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      .mc-stat-value {
        margin-top: 4px;
        font-size: 24px;
        line-height: 1;
        font-weight: 950;
      }

      .mc-stat-sub {
        margin-top: 5px;
        color: var(--mc-muted);
        font-size: 12px;
      }

      .mc-toolbar {
        background: #fff;
        border: 1px solid var(--mc-border);
        border-radius: var(--mc-radius);
        padding: 14px 16px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        box-shadow: var(--mc-shadow-soft);
      }

      .mc-toolbar-left,
      .mc-toolbar-right {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
      }

      .mc-toolbar-left {
        flex: 1
      }

      .mc-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 170px;
      }

      .mc-field.search {
        flex: 1;
        min-width: 260px;
      }

      .mc-label {
        color: var(--mc-muted);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      .mc-input,
      .mc-select {
        width: 100%;
        border: 1px solid var(--mc-border);
        background: #fff;
        border-radius: 10px;
        padding: 10px 12px;
        outline: none;
        min-height: 42px;
        font-size: 14px;
      }

      .mc-input {
        padding-left: 36px;
        background: #f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E") no-repeat 11px center / 16px;
      }

      .mc-input:focus,
      .mc-select:focus {
        border-color: var(--mc-primary);
        box-shadow: 0 0 0 3px var(--mc-primary-soft);
        background-color: #fff;
      }

      .mc-bulkbar {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 12px 14px;
        margin-bottom: 14px;
        background: #111827;
        color: #fff;
        border-radius: 14px;
        box-shadow: var(--mc-shadow);
      }

      .mc-bulkbar.is-visible {
        display: flex
      }

      .mc-bulkbar select {
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, .22);
        background: #fff;
        color: #111827;
        padding: 8px 10px;
        min-height: 38px;
      }

      .mc-view {
        display: none
      }

      .mc-view.is-active {
        display: block
      }

      .mc-card {
        background: #fff;
        border: 1px solid var(--mc-border);
        border-radius: var(--mc-radius);
        overflow: hidden;
        box-shadow: var(--mc-shadow-soft);
      }

      .mc-list-head,
      .mc-row {
        display: grid;
        grid-template-columns: 44px minmax(240px, 1.45fr) minmax(170px, .95fr) minmax(150px, .8fr) minmax(220px, 1.2fr) 130px 150px 115px 110px 122px;
        gap: 12px;
        align-items: center;
      }

      .mc-list-head {
        padding: 15px 16px 10px;
        color: var(--mc-muted);
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .06em;
      }

      .mc-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding-bottom: 16px
      }

      .mc-item {
        margin: 0 16px;
        border: 1px solid var(--mc-border);
        border-radius: 14px;
        background: #fff;
        transition: .18s ease;
        overflow: hidden;
      }

      .mc-item:hover {
        border-color: var(--mc-primary);
        box-shadow: var(--mc-shadow);
      }

      .mc-item.is-selected {
        border-color: #111827;
        box-shadow: 0 0 0 3px rgba(17, 24, 39, .08);
      }

      .mc-row {
        padding: 16px
      }

      @media(max-width:1500px) {
        .mc-list-head {
          display: none
        }

        .mc-row {
          grid-template-columns: 44px 1fr
        }

        .mc-cell:not(.mc-check-cell) {
          grid-column: 2
        }

        .mc-actions-cell {
          grid-column: 1 / -1
        }
      }

      .mc-cell-title {
        display: none;
        color: var(--mc-muted);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 4px;
      }

      @media(max-width:1500px) {
        .mc-cell-title {
          display: block
        }
      }

      .mc-main-title {
        font-size: 15px;
        color: #111827;
        font-weight: 950;
        line-height: 1.25;
      }

      .mc-sub {
        color: var(--mc-muted);
        font-size: 13px;
        line-height: 1.45;
        margin-top: 4px;
      }

      .mc-line-clamp {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .mc-tag,
      .mc-pill,
      .mc-date,
      .mc-reminder {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        font-weight: 900;
        width: max-content;
        max-width: 100%;
      }

      .mc-tag {
        background: var(--mc-blue-soft);
        color: #256c91;
        padding: 6px 10px;
        font-size: 12px;
      }

      .mc-pill {
        padding: 6px 10px;
        font-size: 12px;
        text-transform: capitalize;
      }

      .mc-pill.draft {
        background: #f3f4f6;
        color: #4b5563
      }

      .mc-pill.active {
        background: var(--mc-green-soft);
        color: #047857
      }

      .mc-pill.inactive {
        background: var(--mc-yellow-soft);
        color: #b45309
      }

      .mc-pill.cancelled {
        background: var(--mc-red-soft);
        color: #b91c1c
      }

      .mc-date {
        border: 1px solid var(--mc-border);
        background: #fff;
        color: #111827;
        padding: 8px 10px;
        font-size: 13px;
      }

      .mc-reminder {
        margin-top: 8px;
        padding: 5px 9px;
        font-size: 12px;
      }

      .mc-reminder.ok {
        background: var(--mc-green-soft);
        color: #047857
      }

      .mc-reminder.soon {
        background: var(--mc-yellow-soft);
        color: #b45309
      }

      .mc-reminder.overdue {
        background: var(--mc-red-soft);
        color: #b91c1c
      }

      .mc-price {
        font-weight: 950;
        font-size: 14px;
        color: #111827
      }

      .mc-row-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
      }

      .mc-empty {
        margin: 16px;
        padding: 48px 20px;
        border: 1px dashed var(--mc-border);
        border-radius: 16px;
        text-align: center;
        color: var(--mc-muted);
        background: #fff;
      }

      .mc-pagination {
        margin-top: 16px;
        background: #fff;
        border: 1px solid var(--mc-border);
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: var(--mc-shadow-soft);
      }

      .mc-pagination .pagination {
        margin: 0;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
      }

      .mc-pagination .page-link {
        border-radius: 10px !important;
        border: 1px solid var(--mc-border) !important;
        color: #111827;
        box-shadow: none !important;
        padding: 8px 12px;
      }

      .mc-pagination .active .page-link {
        background: var(--mc-primary) !important;
        border-color: var(--mc-primary) !important;
        color: #fff !important;
      }

      .mc-panel {
        background: #fff;
        border: 1px solid var(--mc-border);
        border-radius: var(--mc-radius);
        box-shadow: var(--mc-shadow-soft);
        overflow: hidden;
      }

      .mc-panel-head {
        padding: 16px 18px;
        background: #fafafa;
        border-bottom: 1px solid var(--mc-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
      }

      .mc-panel-title {
        font-size: 16px;
        font-weight: 950;
        margin: 0;
        color: #111827
      }

      .mc-panel-sub {
        font-size: 12px;
        color: var(--mc-muted);
        margin-top: 3px
      }

      .mc-panel-body {
        padding: 18px
      }

      .mc-calendar-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 370px;
        gap: 16px;
      }

      @media(max-width:1200px) {
        .mc-calendar-grid {
          grid-template-columns: 1fr
        }
      }

      #mc-calendar {
        min-height: 720px
      }

      .mc-upcoming-list {
        display: flex;
        flex-direction: column;
        gap: 10px
      }

      .mc-upcoming-item {
        border: 1px solid var(--mc-border);
        border-radius: 14px;
        padding: 14px;
        background: #fff;
        cursor: pointer;
        transition: .18s ease;
      }

      .mc-upcoming-item:hover {
        border-color: var(--mc-primary);
        background: #fcfdf8;
        box-shadow: var(--mc-shadow-soft)
      }

      .mc-up-title {
        font-size: 14px;
        font-weight: 950;
        color: #111827
      }

      .mc-up-meta {
        font-size: 12px;
        color: var(--mc-muted);
        margin-top: 6px;
        line-height: 1.55
      }

      .mc-up-badge {
        display: inline-flex;
        margin-top: 10px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900
      }

      .mc-up-badge.overdue {
        background: var(--mc-red-soft);
        color: #b91c1c
      }

      .mc-up-badge.soon {
        background: var(--mc-yellow-soft);
        color: #b45309
      }

      .mc-up-badge.ok {
        background: var(--mc-green-soft);
        color: #047857
      }

      .mc-kanban {
        display: grid;
        grid-template-columns: repeat(4, minmax(250px, 1fr));
        gap: 14px;
        align-items: start;
        overflow-x: auto;
        padding-bottom: 4px;
      }

      .mc-kanban-col {
        min-width: 250px;
        background: #f9fafb;
        border: 1px solid var(--mc-border);
        border-radius: 16px;
        overflow: hidden;
      }

      .mc-kanban-head {
        padding: 13px 14px;
        background: #fff;
        border-bottom: 1px solid var(--mc-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
      }

      .mc-kanban-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 950;
        color: #111827;
        font-size: 14px;
      }

      .mc-kanban-count {
        background: #f3f4f6;
        color: #374151;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 950;
      }

      .mc-kanban-body {
        min-height: 420px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      .mc-kanban-body.drag-over {
        background: var(--mc-primary-soft);
        outline: 2px dashed var(--mc-primary);
        outline-offset: -8px;
      }

      .mc-kanban-card {
        background: #fff;
        border: 1px solid var(--mc-border);
        border-radius: 14px;
        padding: 13px;
        cursor: grab;
        box-shadow: var(--mc-shadow-soft);
        transition: .18s ease;
      }

      .mc-kanban-card:active {
        cursor: grabbing
      }

      .mc-kanban-card.dragging {
        opacity: .45;
        transform: scale(.985)
      }

      .mc-kanban-card:hover {
        border-color: var(--mc-primary);
        box-shadow: var(--mc-shadow)
      }

      .mc-kanban-card-title {
        font-size: 14px;
        font-weight: 950;
        color: #111827;
        line-height: 1.25
      }

      .mc-kanban-card-meta {
        font-size: 12px;
        color: var(--mc-muted);
        line-height: 1.5;
        margin-top: 7px
      }

      .mc-kanban-card-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap
      }

      .mc-toast-wrap {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 380px;
        max-width: calc(100vw - 40px);
        pointer-events: none;
      }

      .mc-toast {
        pointer-events: auto;
        background: #fff;
        border: 1px solid #fde68a;
        border-left: 5px solid var(--mc-yellow);
        border-radius: 16px;
        box-shadow: 0 22px 50px rgba(15, 23, 42, .2);
        overflow: hidden;
        animation: mcSlideIn .2s ease-out;
      }

      @keyframes mcSlideIn {
        from {
          opacity: 0;
          transform: translateY(10px)
        }

        to {
          opacity: 1;
          transform: translateY(0)
        }
      }

      .mc-toast-head {
        padding: 13px 15px;
        background: var(--mc-yellow-soft);
        color: #92400e;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        font-weight: 950;
      }

      .mc-toast-body {
        padding: 13px 15px;
        color: #111827;
        font-size: 13px;
        line-height: 1.55;
      }

      .mc-toast-close {
        border: 0;
        background: transparent;
        color: #92400e;
        cursor: pointer;
        font-weight: 950;
        font-size: 18px;
        line-height: 1;
      }

      .mc-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .55);
        backdrop-filter: blur(3px);
        z-index: 99998;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
      }

      .mc-modal-backdrop.is-open {
        display: flex
      }

      .mc-modal {
        width: 100%;
        max-width: 760px;
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, .65);
        box-shadow: 0 35px 80px rgba(15, 23, 42, .28);
        overflow: hidden;
      }

      .mc-modal-head {
        padding: 16px 18px;
        background: #fafafa;
        border-bottom: 1px solid var(--mc-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
      }

      .mc-modal-title {
        font-size: 16px;
        font-weight: 950;
        margin: 0
      }

      .mc-modal-body {
        padding: 18px;
        max-height: 70vh;
        overflow: auto
      }

      .mc-modal-footer {
        padding: 14px 18px;
        border-top: 1px solid var(--mc-border);
        background: #fafafa;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap
      }

      .mc-kv {
        display: grid;
        grid-template-columns: 165px 1fr;
        gap: 10px 12px
      }

      .mc-k {
        font-size: 12px;
        font-weight: 950;
        color: var(--mc-muted);
        text-transform: uppercase;
        letter-spacing: .05em
      }

      .mc-v {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        line-height: 1.5
      }

      @media(max-width:620px) {
        .mc-kv {
          grid-template-columns: 1fr
        }
      }

      .fc .fc-toolbar-title {
        font-size: 1.1rem;
        font-weight: 950;
        color: #111827
      }

      .fc .fc-button {
        background: #fff !important;
        border: 1px solid var(--mc-border) !important;
        color: #374151 !important;
        box-shadow: none !important;
        border-radius: 10px !important;
        font-weight: 900 !important;
        padding: .45rem .8rem !important;
      }

      .fc .fc-button:hover {
        background: #f9fafb !important
      }

      .fc .fc-button-active {
        background: var(--mc-primary-soft) !important;
        border-color: var(--mc-primary) !important;
        color: #365314 !important
      }

      .fc-theme-standard td,
      .fc-theme-standard th,
      .fc-theme-standard .fc-scrollgrid {
        border-color: #edf0f2 !important
      }

      .mc-list-head,
      .mc-row {
        grid-template-columns:
          44px minmax(220px, 1.3fr) minmax(160px, .9fr) minmax(140px, .8fr) minmax(210px, 1fr) 120px 140px 105px 95px 190px;
      }

      .mc-actions-cell {
        min-width: 190px;
      }

      .mc-row-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
      }

      .mc-icon-btn.success {
        background: #ecfdf5;
        border-color: #bbf7d0;
        color: #15803d;
      }

      .mc-icon-btn.success:hover {
        background: #dcfce7;
        color: #166534;
      }

      @media(max-width:1500px) {
        .mc-list-head {
          display: none;
        }

        .mc-row {
          grid-template-columns: 44px minmax(0, 1fr);
        }

        .mc-cell:not(.mc-check-cell) {
          grid-column: 2;
        }

        .mc-actions-cell {
          grid-column: 1 / -1;
          min-width: 0;
        }

        .mc-row-actions {
          justify-content: flex-start;
          flex-wrap: wrap;
          padding-left: 52px;
        }
      }

      @media(max-width:650px) {
        .mc-row {
          grid-template-columns: 1fr;
        }

        .mc-check-cell,
        .mc-cell:not(.mc-check-cell),
        .mc-actions-cell {
          grid-column: 1;
        }

        .mc-row-actions {
          padding-left: 0;
          justify-content: flex-start;
        }

        .mc-icon-btn {
          width: 40px;
          height: 40px;
        }
      }

      /* ============================================================
                         FIX: responsive list view + visible action buttons
                         The previous grid was wider than the content area, so everything
                         after price was clipped. This makes the action area sticky on wide
                         screens and card-like on smaller screens.
                      ============================================================ */

      .mc-card {
        overflow-x: auto;
        overflow-y: visible;
      }

      .mc-list-head,
      .mc-row {
        min-width: 1420px;
        grid-template-columns:
          44px minmax(210px, 1.25fr) minmax(150px, .85fr) minmax(140px, .75fr) minmax(190px, 1fr) 110px 130px 100px 90px 220px !important;
      }

      .mc-list-head>div:last-child,
      .mc-actions-cell {
        position: sticky;
        right: 0;
        z-index: 5;
        background: #fff;
        box-shadow: -14px 0 22px -22px rgba(15, 23, 42, .65);
      }

      .mc-actions-cell {
        min-width: 220px;
      }

      .mc-row-actions {
        display: flex !important;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        white-space: nowrap;
      }

      .mc-icon-btn {
        flex: 0 0 38px;
      }

      .mc-icon-btn.success {
        background: #ecfdf5;
        border-color: #bbf7d0;
        color: #15803d;
      }

      .mc-icon-btn.success:hover {
        background: #dcfce7;
        color: #166534;
      }

      .mc-icon-btn.disabled,
      .mc-icon-btn.is-disabled {
        opacity: .45;
        pointer-events: none;
        filter: grayscale(1);
      }

      @media(max-width:1700px) {
        .mc-card {
          overflow-x: visible;
        }

        .mc-list-head {
          display: none !important;
        }

        .mc-row {
          min-width: 0;
          grid-template-columns: 44px minmax(0, 1fr) !important;
          align-items: start;
        }

        .mc-check-cell {
          grid-column: 1;
          grid-row: 1;
        }

        .mc-cell:not(.mc-check-cell) {
          grid-column: 2;
        }

        .mc-actions-cell {
          position: static;
          grid-column: 1 / -1 !important;
          min-width: 0;
          box-shadow: none;
          border-top: 1px solid var(--mc-border);
          padding-top: 12px;
          margin-top: 2px;
        }

        .mc-row-actions {
          justify-content: flex-start !important;
          flex-wrap: wrap !important;
          padding-left: 52px;
        }

        .mc-cell-title {
          display: block !important;
        }
      }

      @media(max-width:650px) {
        .mc-row {
          grid-template-columns: 1fr !important;
        }

        .mc-check-cell,
        .mc-cell:not(.mc-check-cell),
        .mc-actions-cell {
          grid-column: 1 !important;
        }

        .mc-row-actions {
          padding-left: 0;
          justify-content: flex-start !important;
        }

        .mc-icon-btn {
          width: 40px;
          height: 40px;
          flex-basis: 40px;
        }
      }

      /* ============================================================
                 FINAL LIST COMPACT FIX:
                 Kunde + Adresse + Verantwortlich + Produkt are one column.
                 This prevents the row from being clipped before the action buttons.
              ============================================================ */

      .mc-list-head,
      .mc-row {
        min-width: 0 !important;
        grid-template-columns:
          44px minmax(210px, 1.15fr) minmax(320px, 1.8fr) 125px 145px 105px 100px 220px !important;
      }

      .mc-customer-object-cell {
        min-width: 0;
      }

      .mc-customer-object-cell .mc-sub {
        display: flex;
        align-items: center;
        gap: 6px;
      }

      .mc-actions-cell {
        min-width: 220px !important;
      }

      .mc-row-actions {
        display: flex !important;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: nowrap !important;
        white-space: nowrap;
      }

      .mc-icon-btn {
        flex: 0 0 38px;
      }

      .mc-icon-btn.success {
        background: #ecfdf5;
        border-color: #bbf7d0;
        color: #15803d;
      }

      .mc-icon-btn.success:hover {
        background: #dcfce7;
        color: #166534;
      }

      @media(max-width:1250px) {
        .mc-list-head {
          display: none !important;
        }

        .mc-card {
          overflow-x: visible !important;
        }

        .mc-row {
          min-width: 0 !important;
          grid-template-columns: 44px minmax(0, 1fr) !important;
          align-items: start;
        }

        .mc-check-cell {
          grid-column: 1;
          grid-row: 1;
        }

        .mc-cell:not(.mc-check-cell) {
          grid-column: 2;
        }

        .mc-actions-cell {
          grid-column: 1 / -1 !important;
          min-width: 0 !important;
          position: static !important;
          box-shadow: none !important;
          border-top: 1px solid var(--mc-border);
          padding-top: 12px;
          margin-top: 2px;
        }

        .mc-row-actions {
          justify-content: flex-start !important;
          flex-wrap: wrap !important;
          padding-left: 52px;
        }

        .mc-cell-title {
          display: block !important;
        }
      }

      @media(max-width:650px) {
        .mc-row {
          grid-template-columns: 1fr !important;
        }

        .mc-check-cell,
        .mc-cell:not(.mc-check-cell),
        .mc-actions-cell {
          grid-column: 1 !important;
        }

        .mc-row-actions {
          padding-left: 0 !important;
          justify-content: flex-start !important;
        }

        .mc-icon-btn {
          width: 40px;
          height: 40px;
          flex-basis: 40px;
        }
      }


      /* ============================================================
             ACTION MENU WITH LUCIDE ICONS
             Replaces many small action icons with one clean dropdown menu.
          ============================================================ */
      .mc-row-menu-actions {
        justify-content: flex-end !important;
        position: relative;
        overflow: visible;
      }

      .mc-action-menu {
        position: relative;
        display: inline-flex;
        justify-content: flex-end;
        min-width: 132px;
      }

      .mc-menu-btn {
        border: 1px solid var(--mc-border);
        background: #fff;
        color: #111827;
        border-radius: 12px;
        min-height: 40px;
        padding: 9px 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        font-weight: 950;
        box-shadow: var(--mc-shadow-soft);
        transition: .18s ease;
        white-space: nowrap;
      }

      .mc-menu-btn:hover,
      .mc-action-menu.is-open .mc-menu-btn {
        border-color: var(--mc-primary);
        background: var(--mc-primary-soft);
        color: #365314;
      }

      .mc-lucide {
        width: 17px;
        height: 17px;
        stroke-width: 2.25;
        flex: 0 0 auto;
      }

      .mc-lucide.sm {
        width: 15px;
        height: 15px;
      }

      .mc-action-dropdown {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 2147483000;
        width: 245px;
        max-width: calc(100vw - 24px);
        background: #fff;
        border: 1px solid var(--mc-border);
        border-radius: 15px;
        box-shadow: 0 22px 55px rgba(15, 23, 42, .20);
        padding: 7px;
        display: none;
        transform: translate3d(0, 0, 0);
      }

      .mc-action-menu.is-open .mc-action-dropdown {
        display: block;
      }

      .mc-action-dropdown::before {
        display: none;
      }

      .mc-action-item {
        width: 100%;
        border: 0;
        background: transparent;
        color: #111827;
        text-decoration: none;
        border-radius: 11px;
        padding: 10px 11px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 900;
        line-height: 1.2;
        text-align: left;
      }

      .mc-action-item:hover {
        background: #f9fafb;
        color: #111827;
        text-decoration: none;
      }

      .mc-action-item.warning {
        color: #b45309;
      }

      .mc-action-item.warning:hover {
        background: var(--mc-yellow-soft);
      }

      .mc-action-item.is-disabled {
        opacity: .45;
        pointer-events: none;
        filter: grayscale(1);
      }

      .mc-actions-cell,
      .mc-card,
      .mc-item {
        overflow: visible !important;
      }

      @media(max-width:1250px) {
        .mc-row-menu-actions {
          justify-content: flex-start !important;
        }
      }
    </style>
  @endpush
@endonce

@section('content')
  <div class="mc-wrap">
    {{-- CI-Vereinheitlichung 2026-07-15 (Welle 2): Alt-Kopf durch das gemeinsame Bauteil ersetzt. --}}
    <x-page-head title="Wartungsverträge"
        sub="Liste, Kalender, Kanban, nächste Wartungen und eingehende Vertrags-Erinnerungen an einem Ort."
        current="Wartungsverträge" style="margin:0 0 18px;">
        <x-slot:actions>
            <div class="mc-view-toggle" id="mc-view-toggle">
                <button type="button" data-view="list" class="is-active"><i class="fa fa-list-ul"></i> Liste</button>
                <button type="button" data-view="calendar"><i class="fa fa-calendar"></i> Kalender</button>
                <button type="button" data-view="kanban"><i class="fa fa-columns"></i> Kanban</button>
            </div>

            <button type="button" class="mc-btn-soft" id="mc-refresh-btn">
                <i class="fa fa-rotate"></i> Aktualisieren
            </button>

            <a href="{{ $routeCreate }}" class="mc-btn">
                <i class="fa fa-plus"></i> Vertrag anlegen
            </a>
        </x-slot:actions>
    </x-page-head>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mc-stats">
      <div class="mc-stat">
        <div class="mc-stat-ic total"><i class="fa fa-file-contract"></i></div>
        <div>
          <div class="mc-stat-label">Gesamt</div>
          <div class="mc-stat-value">{{ $totalCount }}</div>
          <div class="mc-stat-sub">Verträge insgesamt</div>
        </div>
      </div>

      <div class="mc-stat">
        <div class="mc-stat-ic active"><i class="fa fa-circle-check"></i></div>
        <div>
          <div class="mc-stat-label">Aktiv</div>
          <div class="mc-stat-value">{{ $activeCount }}</div>
          <div class="mc-stat-sub">Laufende Verträge</div>
        </div>
      </div>

      <div class="mc-stat">
        <div class="mc-stat-ic draft"><i class="fa fa-pen-to-square"></i></div>
        <div>
          <div class="mc-stat-label">Entwurf</div>
          <div class="mc-stat-value">{{ $draftCount }}</div>
          <div class="mc-stat-sub">Noch nicht freigegeben</div>
        </div>
      </div>

      <div class="mc-stat">
        <div class="mc-stat-ic inactive"><i class="fa fa-ban"></i></div>
        <div>
          <div class="mc-stat-label">Inaktiv / Gekündigt</div>
          <div class="mc-stat-value">{{ $inactiveCount }}</div>
          <div class="mc-stat-sub">Beendete Verträge</div>
        </div>
      </div>

      <div class="mc-stat">
        <div class="mc-stat-ic upcoming"><i class="fa fa-calendar-check"></i></div>
        <div>
          <div class="mc-stat-label">Nächste 30 Tage</div>
          <div class="mc-stat-value" id="mc-stat-upcoming">{{ $upcomingCount }}</div>
          <div class="mc-stat-sub">Anstehende Wartungen</div>
        </div>
      </div>
    </div>

    <form method="GET" action="{{ $routeIndex }}" class="mc-toolbar" id="mc-filter-form">
      <div class="mc-toolbar-left">
        <div class="mc-field search">
          <label class="mc-label">Suche</label>
          <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="mc-input"
            placeholder="Vertragsnr., Titel, Kunde, Adresse, Produkt, Mitarbeiter">
        </div>

        <div class="mc-field">
          <label class="mc-label">Status</label>
          <select name="status" class="mc-select">
            <option value="">Status (alle)</option>
            @foreach($statusOptions as $value => $label)
              <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="mc-field">
          <label class="mc-label">Intervall</label>
          <select name="interval_type" class="mc-select">
            <option value="">Intervall (alle)</option>
            @foreach($intervalOptions as $value => $label)
              <option value="{{ $value }}" @selected(($filters['intervalType'] ?? '') === $value)>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <div class="mc-field">
          <label class="mc-label">Sortierung</label>
          <select name="sort" class="mc-select">
            <option value="next_service_asc" @selected(($filters['sort'] ?? '') === 'next_service_asc')>Nächste Wartung ↑
            </option>
            <option value="next_service_desc" @selected(($filters['sort'] ?? '') === 'next_service_desc')>Nächste Wartung ↓
            </option>
            <option value="start_asc" @selected(($filters['sort'] ?? '') === 'start_asc')>Vertragsbeginn ↑</option>
            <option value="start_desc" @selected(($filters['sort'] ?? '') === 'start_desc')>Vertragsbeginn ↓</option>
            <option value="created_desc" @selected(($filters['sort'] ?? '') === 'created_desc')>Neueste zuerst</option>
            <option value="created_asc" @selected(($filters['sort'] ?? '') === 'created_asc')>Älteste zuerst</option>
          </select>
        </div>
      </div>

      <div class="mc-toolbar-right">
        <button type="submit" class="mc-btn-soft"><i class="fa fa-filter"></i> Filtern</button>
        <a href="{{ $routeIndex }}" class="mc-btn-soft"><i class="fa fa-xmark"></i> Zurücksetzen</a>
      </div>
    </form>

    <div class="mc-bulkbar" id="mc-bulkbar">
      <div><strong id="mc-selected-count">0</strong> Vertrag/Verträge ausgewählt</div>
      <div class="mc-actions">
        <select id="mc-bulk-status">
          <option value="">Status ändern…</option>
          @foreach($statusOptions as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
          @endforeach
        </select>
        <button type="button" class="mc-btn-soft" id="mc-bulk-status-btn"><i class="fa fa-check"></i> Anwenden</button>
        <button type="button" class="mc-btn-soft" id="mc-bulk-delete-btn"><i class="fa fa-trash"></i> Löschen</button>
      </div>
    </div>

    <div class="mc-view is-active" id="mc-view-list">
      <div class="mc-card">
        <div class="mc-list-head">
          <div><input type="checkbox" id="mc-select-all"></div>
          <div>Vertrag</div>
          <div>Kunde / Objekt</div>
          <div>Intervall</div>
          <div>Nächste Wartung</div>
          <div>Status</div>
          <div>Preis</div>
          <div style="text-align:right">Aktionen</div>
        </div>

        <div class="mc-list" id="mc-list">
          @forelse($contractPayload as $item)
            <div class="mc-item" data-contract-id="{{ $item['id'] }}" data-status="{{ e($item['status']) }}"
              data-title="{{ e($item['title']) }}" data-customer="{{ e($item['customer']) }}"
              data-responsible="{{ e($item['responsible']) }}" data-address="{{ e($item['address']) }}"
              data-product="{{ e($item['product']) }}" data-next-service="{{ e($item['next_service_date'] ?? '') }}"
              data-show-url="{{ e($item['show_url']) }}" data-profile-url="{{ e($item['customer_profile_url'] ?? '') }}"
              data-edit-url="{{ e($item['edit_url']) }}">
              <div class="mc-row">
                <div class="mc-check-cell">
                  <input type="checkbox" class="mc-row-check" value="{{ $item['id'] }}">
                </div>

                <div class="mc-cell">
                  <div class="mc-cell-title">Vertrag</div>
                  <div class="mc-main-title">{{ $item['title'] }}</div>
                  <div class="mc-sub"><span class="mc-tag">Nr.: {{ $item['contract_no'] ?? '–' }}</span></div>
                </div>

                <div class="mc-cell mc-customer-object-cell">
                  <div class="mc-cell-title">Kunde / Objekt</div>

                  <div class="mc-main-title" style="font-size:14px">
                    <i class="fa fa-user" style="color:#93c21c;margin-right:6px;"></i>
                    {{ $item['customer'] }}
                  </div>

                  <div class="mc-sub">
                    <i class="fa fa-id-card" style="width:14px;"></i>
                    {{ $item['customer_no'] ? 'Kundennr.: ' . $item['customer_no'] : 'Keine Kundennr.' }}
                  </div>

                  <div class="mc-sub mc-line-clamp">
                    <i class="fa fa-location-dot" style="width:14px;"></i>
                    {{ $item['address'] }}
                  </div>

                  <div class="mc-sub">
                    <i class="fa fa-user-gear" style="width:14px;"></i>
                    <strong>Verantwortlich:</strong> {{ $item['responsible'] }}
                  </div>

                  <div class="mc-sub mc-line-clamp">
                    <i class="fa fa-screwdriver-wrench" style="width:14px;"></i>
                    <strong>Produkt:</strong> {{ $item['product'] }}
                  </div>
                </div>

                <div class="mc-cell">
                  <div class="mc-cell-title">Intervall</div>
                  <span class="mc-tag">
                    {{ $intervalOptions[$item['interval_type']] ?? $item['interval_type'] }}
                    @if($item['interval_months'])
                      · {{ $item['interval_months'] }} Mon.
                    @endif
                  </span>
                </div>

                <div class="mc-cell">
                  <div class="mc-cell-title">Nächste Wartung</div>
                  <div class="mc-date"><i class="fa fa-calendar"></i> {{ $item['next_service_display'] }}</div>
                  @if(!is_null($item['days_to']))
                    @if($item['days_to'] < 0)
                      <div class="mc-reminder overdue">Überfällig · {{ abs($item['days_to']) }} Tage</div>
                    @elseif($item['days_to'] <= 14)
                      <div class="mc-reminder soon">Bald · {{ $item['days_to'] }} Tage</div>
                    @else
                      <div class="mc-reminder ok">Geplant</div>
                    @endif
                  @endif
                </div>

                <div class="mc-cell">
                  <div class="mc-cell-title">Status</div>
                  <span class="mc-pill {{ $item['status'] }}">{{ $item['status_label'] }}</span>
                </div>

                <div class="mc-cell">
                  <div class="mc-cell-title">Preis</div>
                  <div class="mc-price">{{ $item['price'] ?? '–' }}</div>
                </div>

                <div class="mc-cell mc-actions-cell">
                  <div class="mc-cell-title">Aktionen</div>

                  <div class="mc-row-actions mc-row-menu-actions">
                    <div class="mc-action-menu" data-action-menu-wrap>
                      <button type="button" class="mc-menu-btn" data-action-menu-trigger aria-expanded="false"
                        aria-haspopup="true">
                        <i data-lucide="menu" class="mc-lucide"></i>
                        <span>Menü</span>
                        <i data-lucide="chevron-down" class="mc-lucide sm"></i>
                      </button>

                      <div class="mc-action-dropdown" data-action-menu>
                        <button type="button" class="mc-action-item" data-open-modal="{{ $item['id'] }}">
                          <i data-lucide="eye" class="mc-lucide"></i>
                          <span>Schnellansicht</span>
                        </button>

                        <a href="{{ $item['show_url'] }}" class="mc-action-item">
                          <i data-lucide="file-text" class="mc-lucide"></i>
                          <span>Wartungsvertrag öffnen</span>
                        </a>

                        <a href="{{ $item['customer_profile_url'] ?: '#' }}"
                          class="mc-action-item {{ empty($item['customer_profile_url']) ? 'is-disabled' : '' }}"
                          title="{{ !empty($item['customer_profile_url']) ? 'Kundenprofil öffnen' : 'Kein Kunde verknüpft' }}">
                          <i data-lucide="user-round" class="mc-lucide"></i>
                          <span>Kundenprofil</span>
                        </a>

                        <a href="{{ $item['edit_url'] }}" class="mc-action-item warning">
                          <i data-lucide="square-pen" class="mc-lucide"></i>
                          <span>Bearbeiten</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="mc-empty">
              <div style="font-size:32px;margin-bottom:8px"><i class="fa fa-folder-open"></i></div>
              Keine Wartungsverträge gefunden.
            </div>
          @endforelse
        </div>
      </div>

      @if($isPaginator && method_exists($contracts, 'links') && $contracts->hasPages())
        <div class="mc-pagination">
          <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <div style="font-size:12px;color:#6b7280;">
              Zeige <strong>{{ $contracts->firstItem() ?? 0 }}</strong>
              bis <strong>{{ $contracts->lastItem() ?? 0 }}</strong>
              von <strong>{{ $contracts->total() }}</strong> Einträgen
            </div>
            <div>{{ $contracts->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}</div>
          </div>
        </div>
      @endif
    </div>

    <div class="mc-view" id="mc-view-calendar">
      <div class="mc-calendar-grid">
        <div class="mc-panel">
          <div class="mc-panel-head">
            <div>
              <h3 class="mc-panel-title">Kalenderansicht</h3>
              <div class="mc-panel-sub">Alle Wartungstermine mit Klick zur Profilseite.</div>
            </div>
            <button type="button" class="mc-btn-soft" id="mc-calendar-reload"><i class="fa fa-rotate"></i> Neu
              laden</button>
          </div>
          <div class="mc-panel-body">
            <div id="mc-calendar"></div>
          </div>
        </div>

        <div class="mc-panel">
          <div class="mc-panel-head">
            <div>
              <h3 class="mc-panel-title">Incoming Wartung</h3>
              <div class="mc-panel-sub">Nächste 30 Tage und überfällige Verträge.</div>
            </div>
          </div>
          <div class="mc-panel-body">
            <div class="mc-upcoming-list" id="mc-upcoming-list">
              <div class="mc-sub">Lade…</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mc-view" id="mc-view-kanban">
      <div class="mc-panel">
        <div class="mc-panel-head">
          <div>
            <h3 class="mc-panel-title">Kanban</h3>
            <div class="mc-panel-sub">Status per Drag & Drop ändern. Änderungen werden per AJAX gespeichert.</div>
          </div>
          <button type="button" class="mc-btn-soft" id="mc-kanban-reload"><i class="fa fa-rotate"></i> Kanban neu
            laden</button>
        </div>
        <div class="mc-panel-body">
          <div class="mc-kanban" id="mc-kanban">
            @foreach($statusColumns as $status => $column)
              <div class="mc-kanban-col" data-kanban-status="{{ $status }}">
                <div class="mc-kanban-head">
                  <div class="mc-kanban-title"><i class="{{ $column['icon'] }}"></i> {{ $column['label'] }}</div>
                  <div class="mc-kanban-count" data-kanban-count="{{ $status }}">0</div>
                </div>
                <div class="mc-kanban-body" data-kanban-drop="{{ $status }}"></div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mc-toast-wrap" id="mc-toast-wrap"></div>

  <div class="mc-modal-backdrop" id="mc-modal">
    <div class="mc-modal">
      <div class="mc-modal-head">
        <h3 class="mc-modal-title" id="mc-modal-title">Wartungsvertrag</h3>
        <button type="button" class="mc-icon-btn" id="mc-modal-close"><i class="fa fa-xmark"></i></button>
      </div>
      <div class="mc-modal-body" id="mc-modal-body"></div>
      <div class="mc-modal-footer">
        <a href="#" class="mc-btn" id="mc-modal-open"><i class="fa fa-arrow-up-right-from-square"></i> Profil öffnen</a>
        <a href="#" class="mc-btn-soft" id="mc-modal-edit"><i class="fa fa-pen"></i> Bearbeiten</a>
        <button type="button" class="mc-btn-soft" id="mc-modal-dismiss"><i class="fa fa-bell-slash"></i> Heute
          ausblenden</button>
      </div>
    </div>
  </div>
@endsection

@once
  @push('scripts')
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
      (function () {
        "use strict";

        const CONTRACTS = @json($contractPayload);
        const STATUS_COLUMNS = @json($statusColumns);
        const URLS = {
          base: @json($baseUrl),
          bulkStatus: @json($routeBulkStatus),
          bulkDelete: @json($routeBulkDelete),
          kanbanUpdate: @json($routeKanbanUpdate),
          kanbanFeed: @json($routeKanbanFeed),
          calendarFeed: @json($routeCalendarFeed),
          incoming: @json($routeIncoming),
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || @json(csrf_token());
        const viewToggle = document.getElementById("mc-view-toggle");
        const views = {
          list: document.getElementById("mc-view-list"),
          calendar: document.getElementById("mc-view-calendar"),
          kanban: document.getElementById("mc-view-kanban"),
        };

        let calendarInstance = null;
        let kanbanItems = [...CONTRACTS];

        function qs(sel, ctx = document) { return ctx.querySelector(sel); }
        function qsa(sel, ctx = document) { return Array.from(ctx.querySelectorAll(sel)); }


        function refreshLucideIcons() {
          if (window.lucide && typeof window.lucide.createIcons === "function") {
            window.lucide.createIcons();
          }
        }

        function closeAllActionMenus(exceptWrap = null) {
          qsa("[data-action-menu-wrap].is-open").forEach(wrap => {
            if (exceptWrap && wrap === exceptWrap) return;
            wrap.classList.remove("is-open");
            const trigger = qs("[data-action-menu-trigger]", wrap);
            const menu = qs("[data-action-menu]", wrap);
            if (trigger) trigger.setAttribute("aria-expanded", "false");
            if (menu) {
              menu.style.left = "0px";
              menu.style.top = "0px";
            }
          });
        }

        function placeActionMenu(wrap) {
          const trigger = qs("[data-action-menu-trigger]", wrap);
          const menu = qs("[data-action-menu]", wrap);
          if (!trigger || !menu) return;

          const rect = trigger.getBoundingClientRect();
          const gap = 8;
          const safe = 12;
          const menuWidth = Math.min(245, window.innerWidth - safe * 2);

          menu.style.width = menuWidth + "px";
          menu.style.left = "0px";
          menu.style.top = "0px";

          requestAnimationFrame(() => {
            const menuHeight = menu.offsetHeight || 210;
            let left = rect.right - menuWidth;
            let top = rect.bottom + gap;

            if (left < safe) left = safe;
            if (left + menuWidth > window.innerWidth - safe) {
              left = window.innerWidth - menuWidth - safe;
            }

            if (top + menuHeight > window.innerHeight - safe) {
              top = rect.top - menuHeight - gap;
            }
            if (top < safe) top = safe;

            menu.style.left = left + "px";
            menu.style.top = top + "px";
          });
        }

        document.addEventListener("click", function (e) {
          const trigger = e.target.closest("[data-action-menu-trigger]");
          if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            const wrap = trigger.closest("[data-action-menu-wrap]");
            const willOpen = !wrap.classList.contains("is-open");
            closeAllActionMenus(wrap);

            qsa("[data-action-menu-wrap]").forEach(item => {
              if (item !== wrap) item.classList.remove("is-open");
            });

            wrap.classList.toggle("is-open", willOpen);
            trigger.setAttribute("aria-expanded", willOpen ? "true" : "false");
            if (willOpen) placeActionMenu(wrap);
            return;
          }

          if (!e.target.closest("[data-action-menu]")) {
            closeAllActionMenus();
          }
        }, true);

        document.addEventListener("keydown", function (e) {
          if (e.key === "Escape") closeAllActionMenus();
        });

        window.addEventListener("resize", () => closeAllActionMenus());
        window.addEventListener("scroll", () => closeAllActionMenus(), true);

        document.addEventListener("DOMContentLoaded", refreshLucideIcons);

        function escapeHtml(value) {
          return String(value ?? "")
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function formatDE(iso) {
          if (!iso) return "–";
          const d = new Date(String(iso).slice(0, 10) + "T00:00:00");
          if (isNaN(d.getTime())) return "–";
          return String(d.getDate()).padStart(2, "0") + "." + String(d.getMonth() + 1).padStart(2, "0") + "." + d.getFullYear();
        }

        function daysTo(iso) {
          if (!iso) return null;
          const d = new Date(String(iso).slice(0, 10) + "T00:00:00");
          if (isNaN(d.getTime())) return null;
          const today = new Date();
          today.setHours(0, 0, 0, 0);
          d.setHours(0, 0, 0, 0);
          return Math.round((d.getTime() - today.getTime()) / 86400000);
        }

        function normalizeIncomingItem(item) {
          return {
            id: item.id,
            contract_no: item.contract_no || "",
            title: item.title || "Wartung",
            customer: item.customer || "–",
            responsible: item.responsible || "–",
            address: item.address || "–",
            product: item.product || "–",
            status: item.status || "draft",
            status_label: item.status_label || STATUS_COLUMNS[item.status]?.label || item.status || "Entwurf",
            next_service_date: item.next_service_date || "",
            next_service_display: item.next_service_display || formatDE(item.next_service_date),
            show_url: item.show_url || (URLS.base + "/" + item.id),
            customer_profile_url: item.customer_profile_url || item.profile_url || "",
            edit_url: item.edit_url || (URLS.base + "/" + item.id + "/edit"),
            price: item.price || null,
            interval_type: item.interval_type || "yearly",
            interval_months: item.interval_months || null,
          };
        }

        function findContract(id) {
          id = String(id);
          return normalizeIncomingItem((kanbanItems.find(x => String(x.id) === id) || CONTRACTS.find(x => String(x.id) === id) || {}));
        }

        function setView(view) {
          if (!views[view]) view = "list";

          Object.entries(views).forEach(([name, el]) => {
            if (el) el.classList.toggle("is-active", name === view);
          });

          qsa("button[data-view]", viewToggle).forEach(btn => {
            btn.classList.toggle("is-active", btn.dataset.view === view);
          });

          const url = new URL(window.location.href);
          url.searchParams.set("view", view);
          window.history.replaceState({}, "", url.toString());

          if (view === "calendar") initCalendar();
          if (view === "kanban") renderKanban();
        }

        viewToggle?.addEventListener("click", function (e) {
          const btn = e.target.closest("button[data-view]");
          if (!btn) return;
          setView(btn.dataset.view);
        });

        document.getElementById("mc-refresh-btn")?.addEventListener("click", function () {
          window.location.reload();
        });

        function openModal(item, allowDismiss = false) {
          item = normalizeIncomingItem(item);
          const modal = qs("#mc-modal");
          const title = qs("#mc-modal-title");
          const body = qs("#mc-modal-body");
          const open = qs("#mc-modal-open");
          const edit = qs("#mc-modal-edit");
          const dismiss = qs("#mc-modal-dismiss");

          if (!modal || !body) return;

          title.textContent = (item.contract_no ? item.contract_no + " · " : "") + item.title;
          open.href = item.show_url;
          edit.href = item.edit_url;
          dismiss.style.display = allowDismiss ? "inline-flex" : "none";
          dismiss.dataset.dismissId = item.id || "";

          const diff = daysTo(item.next_service_date);
          const reminder = diff === null ? "–" : (diff < 0 ? "Überfällig seit " + Math.abs(diff) + " Tagen" : (diff === 0 ? "Heute fällig" : "In " + diff + " Tagen"));

          body.innerHTML = `
                            <div class="mc-kv">
                                <div class="mc-k">Kunde</div><div class="mc-v">${escapeHtml(item.customer)}</div>
                                <div class="mc-k">Verantwortlich</div><div class="mc-v">${escapeHtml(item.responsible)}</div>
                                <div class="mc-k">Adresse</div><div class="mc-v">${escapeHtml(item.address)}</div>
                                <div class="mc-k">Produkt</div><div class="mc-v">${escapeHtml(item.product)}</div>
                                <div class="mc-k">Nächste Wartung</div><div class="mc-v">${escapeHtml(item.next_service_display || formatDE(item.next_service_date))}</div>
                                <div class="mc-k">Reminder</div><div class="mc-v">${escapeHtml(reminder)}</div>
                                <div class="mc-k">Status</div><div class="mc-v"><span class="mc-pill ${escapeHtml(item.status)}">${escapeHtml(item.status_label)}</span></div>
                                <div class="mc-k">Preis</div><div class="mc-v">${escapeHtml(item.price || "–")}</div>
                            </div>
                        `;

          modal.classList.add("is-open");
        }

        function closeModal() {
          qs("#mc-modal")?.classList.remove("is-open");
        }

        qs("#mc-modal-close")?.addEventListener("click", closeModal);
        qs("#mc-modal")?.addEventListener("click", function (e) { if (e.target === this) closeModal(); });
        document.addEventListener("keydown", function (e) { if (e.key === "Escape") closeModal(); });

        qs("#mc-modal-dismiss")?.addEventListener("click", function () {
          const id = this.dataset.dismissId;
          if (id) rememberDismiss(id);
          closeModal();
        });

        qsa("[data-open-modal]").forEach(btn => {
          btn.addEventListener("click", function () {
            openModal(findContract(this.dataset.openModal));
          });
        });

        function todayKey() {
          const d = new Date();
          return d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") + "-" + String(d.getDate()).padStart(2, "0");
        }

        function dismissKey(id) { return "maintenance_contract_incoming_dismissed_" + id; }
        function rememberDismiss(id) { try { localStorage.setItem(dismissKey(id), todayKey()); } catch (e) { } }
        function isDismissed(id) { try { return localStorage.getItem(dismissKey(id)) === todayKey(); } catch (e) { return false; } }

        function showToast(item, autoOpen = false) {
          item = normalizeIncomingItem(item);
          if (!item.id || isDismissed(item.id)) return;

          const wrap = qs("#mc-toast-wrap");
          if (!wrap) return;

          const diff = daysTo(item.next_service_date);
          const text = diff < 0 ? "Überfällig seit " + Math.abs(diff) + " Tagen" : (diff === 0 ? "Heute fällig" : "In " + diff + " Tagen");

          const node = document.createElement("div");
          node.className = "mc-toast";
          node.innerHTML = `
                            <div class="mc-toast-head">
                                <span>⚠️ Incoming Wartungsvertrag</span>
                                <button type="button" class="mc-toast-close">×</button>
                            </div>
                            <div class="mc-toast-body">
                                <strong>${escapeHtml(item.contract_no || "")} ${escapeHtml(item.title)}</strong>
                                <div style="color:#6b7280;margin-top:4px">${escapeHtml(item.customer)}</div>
                                <div style="color:#b45309;margin-top:6px;font-weight:900">${escapeHtml(text)} · ${escapeHtml(formatDE(item.next_service_date))}</div>
                                <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap">
                                    <a class="mc-btn" href="${escapeHtml(item.show_url)}">Profil öffnen</a>
                                    <button type="button" class="mc-btn-soft mc-toast-detail">Details</button>
                                    <button type="button" class="mc-btn-soft mc-toast-dismiss">Heute ausblenden</button>
                                </div>
                            </div>
                        `;

          node.querySelector(".mc-toast-close")?.addEventListener("click", () => node.remove());
          node.querySelector(".mc-toast-detail")?.addEventListener("click", () => openModal(item, true));
          node.querySelector(".mc-toast-dismiss")?.addEventListener("click", () => {
            rememberDismiss(item.id);
            node.remove();
          });

          wrap.appendChild(node);

          if (autoOpen) openModal(item, true);
        }

        async function loadIncoming() {
          let items = [...CONTRACTS];

          if (URLS.incoming) {
            try {
              const res = await fetch(URLS.incoming + "?days=30", { headers: { "Accept": "application/json" } });
              const data = await res.json();
              if (data && Array.isArray(data.items)) {
                items = data.items.map(normalizeIncomingItem);
              }
            } catch (e) {
              console.warn("Incoming contracts feed failed", e);
            }
          }

          const upcoming = items
            .map(normalizeIncomingItem)
            .map(x => ({ ...x, diff: daysTo(x.next_service_date) }))
            .filter(x => x.diff !== null && x.diff <= 30)
            .sort((a, b) => a.diff - b.diff);

          renderUpcoming(upcoming);

          const stat = qs("#mc-stat-upcoming");
          if (stat) stat.textContent = upcoming.length;

          const first = upcoming.find(x => x.diff <= 14 && !isDismissed(x.id));
          if (first) showToast(first, false);
        }

        function renderUpcoming(items) {
          const box = qs("#mc-upcoming-list");
          if (!box) return;

          if (!items.length) {
            box.innerHTML = '<div class="mc-sub">Keine anstehenden Wartungen in den nächsten 30 Tagen.</div>';
            return;
          }

          box.innerHTML = items.map(item => {
            const diff = item.diff;
            const cls = diff < 0 ? "overdue" : (diff <= 14 ? "soon" : "ok");
            const label = diff < 0 ? "Überfällig · " + Math.abs(diff) + " Tage" : (diff === 0 ? "Heute fällig" : "In " + diff + " Tagen");
            return `
                                <div class="mc-upcoming-item" data-upcoming-id="${escapeHtml(item.id)}">
                                    <div class="mc-up-title">${escapeHtml(item.contract_no || "")} ${escapeHtml(item.title)}</div>
                                    <div class="mc-up-meta">
                                        <div><strong>Kunde:</strong> ${escapeHtml(item.customer)}</div>
                                        <div><strong>Verantwortlich:</strong> ${escapeHtml(item.responsible)}</div>
                                        <div><strong>Adresse:</strong> ${escapeHtml(item.address)}</div>
                                        <div><strong>Produkt:</strong> ${escapeHtml(item.product)}</div>
                                        <div><strong>Datum:</strong> ${escapeHtml(formatDE(item.next_service_date))}</div>
                                    </div>
                                    <div class="mc-up-badge ${cls}">${escapeHtml(label)}</div>
                                </div>
                            `;
          }).join("");

          qsa("[data-upcoming-id]", box).forEach(el => {
            el.addEventListener("click", () => openModal(findContract(el.dataset.upcomingId), true));
          });
        }

        async function initCalendar(force = false) {
          const el = qs("#mc-calendar");
          if (!el || !window.FullCalendar) return;

          if (calendarInstance && !force) {
            setTimeout(() => calendarInstance.updateSize(), 100);
            return;
          }

          if (calendarInstance) {
            calendarInstance.destroy();
            calendarInstance = null;
          }

          let events = CONTRACTS
            .filter(x => x.next_service_date)
            .map(x => ({
              id: String(x.id),
              title: (x.contract_no ? x.contract_no + " · " : "") + x.title,
              start: x.next_service_date,
              allDay: true,
              url: x.show_url,
              extendedProps: normalizeIncomingItem(x)
            }));

          if (URLS.calendarFeed) {
            try {
              const res = await fetch(URLS.calendarFeed, { headers: { "Accept": "application/json" } });
              const data = await res.json();
              if (Array.isArray(data)) {
                events = data.map(ev => ({
                  id: String(ev.id),
                  title: ev.title || "Wartung",
                  start: ev.start,
                  allDay: ev.allDay !== false,
                  url: ev.url || (URLS.base + "/" + ev.id),
                  extendedProps: normalizeIncomingItem(ev.extendedProps || ev)
                }));
              }
            } catch (e) {
              console.warn("Calendar feed failed, fallback to current page data", e);
            }
          }

          calendarInstance = new FullCalendar.Calendar(el, {
            initialView: "dayGridMonth",
            height: "auto",
            locale: "de",
            firstDay: 1,
            headerToolbar: {
              left: "prev,next today",
              center: "title",
              right: "dayGridMonth,timeGridWeek,listWeek"
            },
            events,
            eventClick: function (info) {
              info.jsEvent.preventDefault();
              const props = info.event.extendedProps || {};
              if (props.show_url) window.location.href = props.show_url;
              else if (info.event.url) window.location.href = info.event.url;
            },
            eventDidMount: function (info) {
              const p = info.event.extendedProps || {};
              info.el.title = [
                p.customer ? "Kunde: " + p.customer : "",
                p.responsible ? "Verantwortlich: " + p.responsible : "",
                p.address ? "Adresse: " + p.address : "",
                p.product ? "Produkt: " + p.product : "",
                p.status ? "Status: " + p.status : ""
              ].filter(Boolean).join("\n");
            }
          });

          calendarInstance.render();
        }

        qs("#mc-calendar-reload")?.addEventListener("click", () => initCalendar(true));

        async function getKanbanItems() {
          if (!URLS.kanbanFeed) return [...CONTRACTS];

          try {
            const res = await fetch(URLS.kanbanFeed, { headers: { "Accept": "application/json" } });
            const data = await res.json();
            if (data && Array.isArray(data.items)) return data.items.map(normalizeIncomingItem);
          } catch (e) {
            console.warn("Kanban feed failed, fallback to current page data", e);
          }

          return [...CONTRACTS];
        }

        async function renderKanban(force = false) {
          if (force || !kanbanItems.length) {
            kanbanItems = await getKanbanItems();
          }

          Object.keys(STATUS_COLUMNS).forEach(status => {
            const body = qs(`[data-kanban-drop="${status}"]`);
            if (body) body.innerHTML = "";
          });

          kanbanItems.map(normalizeIncomingItem).forEach(item => {
            const status = STATUS_COLUMNS[item.status] ? item.status : "draft";
            const body = qs(`[data-kanban-drop="${status}"]`);
            if (!body) return;
            body.insertAdjacentHTML("beforeend", kanbanCardHtml(item));
          });

          bindKanbanCards();
          updateKanbanCounts();
          refreshLucideIcons();
        }

        function kanbanCardHtml(item) {
          const diff = daysTo(item.next_service_date);
          const reminderCls = diff === null ? "ok" : (diff < 0 ? "overdue" : (diff <= 14 ? "soon" : "ok"));
          const reminder = diff === null ? "Kein Termin" : (diff < 0 ? "Überfällig · " + Math.abs(diff) + " Tage" : (diff === 0 ? "Heute" : "In " + diff + " Tagen"));

          return `
                            <div class="mc-kanban-card" draggable="true" data-kanban-card="${escapeHtml(item.id)}">
                                <div class="mc-kanban-card-title">${escapeHtml(item.contract_no || "")} ${escapeHtml(item.title)}</div>
                                <div class="mc-kanban-card-meta">
                                    <div><strong>Kunde:</strong> ${escapeHtml(item.customer)}</div>
                                    <div><strong>Verantwortlich:</strong> ${escapeHtml(item.responsible)}</div>
                                    <div><strong>Produkt:</strong> ${escapeHtml(item.product)}</div>
                                </div>
                                <div class="mc-kanban-card-foot">
                                    <span class="mc-reminder ${reminderCls}" style="margin-top:0">${escapeHtml(reminder)}</span>
                                    <button type="button" class="mc-icon-btn primary" data-kanban-open="${escapeHtml(item.id)}" title="Öffnen"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        `;
        }

        function bindKanbanCards() {
          qsa("[data-kanban-card]").forEach(card => {
            card.addEventListener("dragstart", e => {
              card.classList.add("dragging");
              e.dataTransfer.setData("text/plain", card.dataset.kanbanCard);
            });

            card.addEventListener("dragend", () => card.classList.remove("dragging"));
          });

          qsa("[data-kanban-open]").forEach(btn => {
            btn.addEventListener("click", e => {
              e.stopPropagation();
              openModal(findContract(btn.dataset.kanbanOpen));
            });
          });

          qsa("[data-kanban-drop]").forEach(drop => {
            drop.addEventListener("dragover", e => {
              e.preventDefault();
              drop.classList.add("drag-over");
              const dragging = qs(".mc-kanban-card.dragging");
              const after = getDragAfterElement(drop, e.clientY);
              if (!dragging) return;
              if (!after) drop.appendChild(dragging);
              else drop.insertBefore(dragging, after);
            });

            drop.addEventListener("dragleave", () => drop.classList.remove("drag-over"));

            drop.addEventListener("drop", async e => {
              e.preventDefault();
              drop.classList.remove("drag-over");
              const id = e.dataTransfer.getData("text/plain");
              const status = drop.dataset.kanbanDrop;
              await updateKanbanStatus(id, status);
            });
          });
        }

        function getDragAfterElement(container, y) {
          const els = qsa(".mc-kanban-card:not(.dragging)", container);
          return els.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) return { offset, element: child };
            return closest;
          }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        async function updateKanbanStatus(id, status) {
          const item = kanbanItems.find(x => String(x.id) === String(id));
          if (item) item.status = status;

          updateKanbanCounts();

          if (!URLS.kanbanUpdate) {
            showToast({ id, title: "Kanban", customer: "Route kanban-update fehlt", next_service_date: null, show_url: URLS.base + "/" + id });
            return;
          }

          const payload = collectKanbanPayload();

          try {
            const res = await fetch(URLS.kanbanUpdate, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken
              },
              body: JSON.stringify({ items: payload, contracts: payload })
            });

            if (!res.ok) throw new Error("HTTP " + res.status);

            const data = await res.json().catch(() => ({}));
            if (data.ok === false || data.success === false) throw new Error(data.message || "Kanban update failed");
          } catch (e) {
            console.error(e);
            alert("Kanban konnte nicht gespeichert werden. Bitte Route/Controller prüfen.");
          }
        }

        function collectKanbanPayload() {
          const payload = [];
          qsa("[data-kanban-drop]").forEach(drop => {
            const status = drop.dataset.kanbanDrop;
            qsa("[data-kanban-card]", drop).forEach((card, index) => {
              payload.push({
                id: card.dataset.kanbanCard,
                status: status,
                position: index + 1
              });
            });
          });
          return payload;
        }

        function updateKanbanCounts() {
          Object.keys(STATUS_COLUMNS).forEach(status => {
            const count = qsa(`[data-kanban-drop="${status}"] [data-kanban-card]`).length;
            const el = qs(`[data-kanban-count="${status}"]`);
            if (el) el.textContent = count;
          });
        }

        qs("#mc-kanban-reload")?.addEventListener("click", async () => {
          kanbanItems = await getKanbanItems();
          renderKanban(true);
        });

        function updateBulkBar() {
          const checks = qsa(".mc-row-check:checked");
          qs("#mc-selected-count").textContent = checks.length;
          qs("#mc-bulkbar")?.classList.toggle("is-visible", checks.length > 0);
          qsa(".mc-item").forEach(item => {
            const id = item.dataset.contractId;
            item.classList.toggle("is-selected", checks.some(c => c.value === id));
          });
        }

        qs("#mc-select-all")?.addEventListener("change", function () {
          qsa(".mc-row-check").forEach(c => c.checked = this.checked);
          updateBulkBar();
        });

        qsa(".mc-row-check").forEach(c => c.addEventListener("change", updateBulkBar));

        async function postBulk(url, payload) {
          if (!url) {
            alert("Die benötigte Route fehlt.");
            return;
          }

          const res = await fetch(url, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify(payload)
          });

          if (!res.ok) throw new Error("HTTP " + res.status);
          window.location.reload();
        }

        qs("#mc-bulk-status-btn")?.addEventListener("click", async function () {
          const ids = qsa(".mc-row-check:checked").map(c => c.value);
          const status = qs("#mc-bulk-status")?.value;
          if (!ids.length) return alert("Bitte zuerst Verträge auswählen.");
          if (!status) return alert("Bitte Status auswählen.");

          try {
            await postBulk(URLS.bulkStatus, { ids, status, contract_ids: ids });
          } catch (e) {
            console.error(e);
            alert("Bulk-Status konnte nicht gespeichert werden.");
          }
        });

        qs("#mc-bulk-delete-btn")?.addEventListener("click", async function () {
          const ids = qsa(".mc-row-check:checked").map(c => c.value);
          if (!ids.length) return alert("Bitte zuerst Verträge auswählen.");
          if (!confirm("Ausgewählte Wartungsverträge wirklich löschen?")) return;

          try {
            await postBulk(URLS.bulkDelete, { ids, contract_ids: ids });
          } catch (e) {
            console.error(e);
            alert("Bulk-Löschen konnte nicht ausgeführt werden.");
          }
        });

        document.addEventListener("DOMContentLoaded", async function () {
          const params = new URLSearchParams(window.location.search);
          const requestedView = params.get("view") || "list";

          renderKanban();
          await loadIncoming();
          refreshLucideIcons();

          if (["list", "calendar", "kanban"].includes(requestedView)) {
            setView(requestedView);
          }
        });
      })();
    </script>
  @endpush
@endonce

@push('scripts')
  <script>
    window.GlobalBreadcrumbs = [
      { label: "Dashboard", url: "{{ url('/') }}" },
      { label: "Wartungsverträge", url: "{{ url()->current() }}", clickable: false }
    ];

    if (window.setGlobalBreadcrumbs) {
      window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
    }
  </script>
@endpush