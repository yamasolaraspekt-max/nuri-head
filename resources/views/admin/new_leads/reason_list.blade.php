@extends('admin.layouts.app')

@section('title')
    {{ Route::currentRouteName() === 'deleted.leads' ? 'Gelöschte Leads' : 'Junk Leads' }}
@endsection

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Str;

$currentRoute = Route::currentRouteName();
$isJunkPage = $currentRoute === 'lead.junks';
$isDeletedPage = $currentRoute === 'deleted.leads';

$pageTitle = $isDeletedPage ? 'Gelöschte Leads' : 'Junk Leads';

$pageSub = $isDeletedPage
    ? 'Gelöschte Kunden/Leads ansehen, Löschgründe prüfen und bei Bedarf wiederherstellen.'
    : 'Junk-Leads ansehen, Junk-Gründe prüfen und bei Bedarf aus Junk entfernen.';

$fs = function ($value, string $fallback = 'Nicht angegeben') {
    $value = is_string($value) ? trim($value) : $value;

    return isset($value) && $value !== '' ? $value : $fallback;
};

$fsName = function ($first, $last) use ($fs) {
    $full = trim(($first ?? '') . ' ' . ($last ?? ''));

    return $fs($full, 'Unbenannt');
};

$isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;
$collection = $isPaginator ? collect($data->items()) : collect($data);

$totalCount = $isPaginator ? $data->total() : $collection->count();

$withReasonCount = $collection->filter(function ($item) use ($isDeletedPage) {
    return $isDeletedPage
        ? !empty($item->delete_reason)
        : (!empty($item->junk_reason) || !empty($item->status_msg));
})->count();

$withoutReasonCount = max(0, $collection->count() - $withReasonCount);

$todayCount = $collection->filter(function ($item) use ($isDeletedPage) {
    $date = $isDeletedPage
        ? ($item->deleted_reason_at ?? $item->deleted_at ?? null)
        : ($item->junked_at ?? null);

    return $date && Carbon::parse($date)->isToday();
})->count();

$currentSort = request('sort_by', 'new_leads.id');
$currentDirection = strtolower(request('sort_order', 'desc'));
$currentDirection = in_array($currentDirection, ['asc', 'desc'], true) ? $currentDirection : 'desc';

$allowedSorts = [
    'new_leads.id',
    'new_leads.name',
    'new_leads.customer_no',
    'new_leads.source',
    'new_leads.quelle',
    'new_leads.lastname',
    'new_leads.email',
    'new_leads.phone',
    'c_name',
    'new_leads.contact_person',
];

if (!in_array($currentSort, $allowedSorts, true)) {
    $currentSort = 'new_leads.id';
}

$sortUrl = function (string $column) use ($currentSort, $currentDirection, $allowedSorts) {
    if (!in_array($column, $allowedSorts, true)) {
        $column = 'new_leads.id';
    }

    $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';

    return request()->fullUrlWithQuery([
        'sort_by' => $column,
        'sort_order' => $direction,
        'page' => 1,
    ]);
};

$sortIcon = function (string $column) use ($currentSort, $currentDirection) {
    if ($currentSort !== $column) {
        return 'fa fa-sort';
    }

    return $currentDirection === 'asc' ? 'fa fa-sort-up' : 'fa fa-sort-down';
};

$editEndpointBase = url('/lead_reason_update');
@endphp

@section('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --lx-primary: var(--sa-accent);
            --lx-primary-dark: var(--sa-accent-hover);
            --lx-blue: #2563eb;
            --lx-red: #dc2626;
            --lx-orange: #d97706;
            --lx-slate-50: #f8fafc;
            --lx-slate-100: #f1f5f9;
            --lx-slate-200: #e2e8f0;
            --lx-slate-300: #cbd5e1;
            --lx-slate-500: #64748b;
            --lx-slate-600: #475569;
            --lx-slate-700: #334155;
            --lx-slate-800: #1e293b;
            --lx-white: #ffffff;
            --lx-shadow: 0 18px 45px rgba(15, 23, 42, .12);
        }

        .lx-page { 
            color: var(--lx-slate-800);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .lx-topbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .lx-title-wrap {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .lx-kicker {
            font-size: 12px;
            font-weight: 900;
            color: var(--lx-primary-dark);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .lx-title {
            margin: 0;
            font-size: 28px;
            line-height: 1.1;
            font-weight: 950;
            color: var(--lx-slate-800);
        }

        .lx-subtitle {
            font-size: 14px;
            color: var(--lx-slate-500);
            max-width: 720px;
        }

        .lx-actions-row {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .lx-btn {
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 9px 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 850;
            line-height: 1.2;
            text-decoration: none !important;
            cursor: pointer;
            white-space: nowrap;
            transition: transform .14s ease, box-shadow .14s ease, background .14s ease, border-color .14s ease;
        }

        .lx-btn:hover {
            transform: translateY(-1px);
            text-decoration: none !important;
        }

        .lx-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        .lx-btn-primary {
            background: var(--lx-primary);
            color: #fff !important;
            box-shadow: 0 12px 24px rgba(147, 194, 28, .22);
        }

        .lx-btn-primary:hover {
            background: var(--lx-primary-dark);
            color: #fff !important;
        }

        .lx-btn-soft {
            background: var(--lx-white);
            border-color: var(--lx-slate-200);
            color: var(--lx-slate-700) !important;
        }

        .lx-btn-soft:hover {
            border-color: var(--lx-slate-300);
            background: var(--lx-slate-50);
            color: var(--lx-slate-800) !important;
        }

        .lx-btn-edit {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c !important;
        }

        .lx-btn-edit:hover {
            background: #ffedd5;
            border-color: #fdba74;
            color: #9a3412 !important;
        }

        .lx-btn-danger-soft {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c !important;
        }

        .lx-btn-danger-soft:hover {
            background: #fee2e2;
            color: #991b1b !important;
        }

        .lx-grid-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .lx-stat {
            background: var(--lx-white);
            border: 1px solid var(--lx-slate-200);
            border-radius: 18px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
        }

        .lx-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--lx-slate-100);
            color: var(--lx-slate-700);
            flex: 0 0 auto;
        }

        .lx-stat-icon.green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .lx-stat-icon.orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .lx-stat-icon.blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .lx-stat-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--lx-slate-500);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .lx-stat-value {
            font-size: 24px;
            font-weight: 950;
            color: var(--lx-slate-800);
            margin-top: 2px;
        }

        .lx-panel {
            background: var(--lx-white);
            border: 1px solid var(--lx-slate-200);
            border-radius: 18px;
            box-shadow: 0 14px 38px rgba(15, 23, 42, .05);
        }

        .lx-filter {
            padding: 15px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .lx-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: min(420px, 100%);
        }

        .lx-label {
            font-size: 12px;
            font-weight: 900;
            color: var(--lx-slate-600);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .lx-input {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--lx-slate-200);
            border-radius: 13px;
            background: var(--lx-slate-50);
            padding: 10px 13px;
            outline: none;
            font-size: 14px;
            color: var(--lx-slate-800);
            transition: border-color .14s ease, background .14s ease, box-shadow .14s ease;
        }

        .lx-input:focus {
            background: #fff;
            border-color: var(--lx-primary);
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .13);
        }

        .lx-table-wrap {
            overflow-x: auto;
        }

        .lx-table {
            width: 100%;
            min-width: 1180px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .lx-table th {
            background: var(--lx-slate-50);
            border-bottom: 1px solid var(--lx-slate-200);
            color: var(--lx-slate-500);
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 13px 15px;
            text-align: left;
            white-space: nowrap;
        }

        .lx-table th:first-child {
            border-top-left-radius: 18px;
        }

        .lx-table th:last-child {
            border-top-right-radius: 18px;
        }

        .lx-table th a {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .lx-table td {
            padding: 16px 15px;
            border-bottom: 1px solid var(--lx-slate-200);
            vertical-align: top;
            background: #fff;
        }

        .lx-table tbody tr:hover td {
            background: #fcfcfd;
        }

        .lx-id {
            font-size: 15px;
            font-weight: 950;
            color: var(--lx-slate-800);
        }

        .lx-small {
            font-size: 12px;
            color: var(--lx-slate-500);
            line-height: 1.55;
        }

        .lx-customer-name {
            color: var(--lx-slate-800) !important;
            font-size: 15px;
            font-weight: 950;
            text-decoration: none !important;
        }

        .lx-customer-name:hover {
            color: var(--lx-primary-dark) !important;
        }

        .lx-muted-line {
            margin-top: 5px;
            color: var(--lx-slate-500);
            font-size: 12px;
            line-height: 1.5;
        }

        .lx-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 950;
            line-height: 1;
            background: var(--lx-slate-100);
            color: var(--lx-slate-700);
            max-width: 100%;
        }

        .lx-badge-blue {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .lx-badge-red {
            background: #fef2f2;
            color: #b91c1c;
        }

        .lx-badge-orange {
            background: #fff7ed;
            color: #c2410c;
        }

        .lx-badge-green {
            background: #f0fdf4;
            color: #15803d;
        }

        .lx-reason-card {
            border-radius: 16px;
            border: 1px solid #fed7aa;
            background: linear-gradient(180deg, #fff7ed 0%, #fff 100%);
            padding: 12px;
            min-width: 280px;
        }

        .lx-reason-card.deleted {
            border-color: #fecaca;
            background: linear-gradient(180deg, #fef2f2 0%, #fff 100%);
        }

        .lx-reason-text {
            font-size: 13px;
            line-height: 1.55;
            color: var(--lx-slate-800);
            white-space: pre-wrap;
            word-break: break-word;
        }

        .lx-reason-placeholder {
            color: var(--lx-slate-500);
            font-style: italic;
        }

        .lx-reason-meta {
            margin-top: 10px;
            padding-top: 9px;
            border-top: 1px dashed var(--lx-slate-300);
            font-size: 11px;
            color: var(--lx-slate-500);
            line-height: 1.5;
        }

        .lx-action-stack {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            min-width: 185px;
        }

        .lx-action-stack .lx-btn {
            width: 100%;
        }

        .lx-empty {
            text-align: center;
            padding: 54px 20px !important;
            color: var(--lx-slate-500);
        }

        .lx-empty-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--lx-slate-100);
            color: var(--lx-slate-500);
            margin-bottom: 12px;
            font-size: 22px;
        }

        .lx-pagination {
            margin-top: 16px;
            padding: 14px;
            border-radius: 18px;
            border: 1px solid var(--lx-slate-200);
            background: #fff;
        }

        .lx-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .lx-modal-backdrop.is-open {
            display: flex;
        }

        .lx-modal {
            width: min(620px, 100%);
            background: #fff;
            border-radius: 22px;
            box-shadow: var(--lx-shadow);
            overflow: hidden;
            transform: translateY(6px);
            animation: lxModalIn .16s ease forwards;
        }

        @keyframes lxModalIn {
            to {
                transform: translateY(0);
            }
        }

        .lx-modal-head {
            padding: 18px 20px;
            border-bottom: 1px solid var(--lx-slate-200);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .lx-modal-title {
            font-size: 18px;
            font-weight: 950;
            color: var(--lx-slate-800);
            margin: 0;
        }

        .lx-modal-subtitle {
            margin-top: 3px;
            font-size: 12px;
            color: var(--lx-slate-500);
        }

        .lx-modal-close-x {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            border: 1px solid var(--lx-slate-200);
            background: #fff;
            color: var(--lx-slate-600);
            cursor: pointer;
        }

        .lx-modal-body {
            padding: 20px;
        }

        .lx-textarea {
            width: 100%;
            min-height: 160px;
            border-radius: 16px;
            border: 1px solid var(--lx-slate-200);
            background: var(--lx-slate-50);
            padding: 13px 14px;
            resize: vertical;
            outline: none;
            font-size: 14px;
            line-height: 1.55;
            color: var(--lx-slate-800);
            transition: border-color .14s ease, background .14s ease, box-shadow .14s ease;
        }

        .lx-textarea:focus {
            border-color: var(--lx-primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .13);
        }

        .lx-help {
            margin-top: 8px;
            color: var(--lx-slate-500);
            font-size: 12px;
        }

        .lx-modal-footer {
            border-top: 1px solid var(--lx-slate-200);
            background: var(--lx-slate-50);
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
            flex-wrap: wrap;
        }

        .lx-toast {
            position: fixed;
            right: 22px;
            bottom: 22px;
            z-index: 100000;
            max-width: 420px;
            border-radius: 16px;
            padding: 13px 15px;
            background: var(--lx-slate-800);
            color: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .22);
            display: none;
            font-size: 13px;
            font-weight: 800;
        }

        .lx-toast.is-visible {
            display: block;
        }

        .lx-toast.success {
            background: #15803d;
        }

        .lx-toast.error {
            background: #b91c1c;
        }

        @media (max-width: 992px) {
            .lx-grid-stats {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .lx-page {
                margin-top: 18px;
                padding-inline: 12px;
            }

            .lx-grid-stats {
                grid-template-columns: 1fr;
            }

            .lx-title {
                font-size: 22px;
            }

            .lx-actions-row,
            .lx-filter {
                align-items: stretch;
            }

            .lx-actions-row .lx-btn,
            .lx-filter .lx-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    @include('admin.new_leads._tabs')
    <div class="lx-page" id="lxLeadReasonApp" data-update-base="{{ $editEndpointBase }}">
        <div class="lx-topbar">
            <div class="lx-title-wrap">
                <div class="lx-kicker">
                    {{ $isDeletedPage ? 'Archiv / Gelöscht' : 'Lead-Qualität / Junk' }}
                </div>

                <h1 class="lx-title">
                    {{ $pageTitle }}
                </h1>

                <div class="lx-subtitle">
                    {{ $pageSub }}
                </div>
            </div>

            <div class="lx-actions-row">
                <a href="{{ route('lead.junks') }}"
                   class="lx-btn {{ $isJunkPage ? 'lx-btn-primary' : 'lx-btn-soft' }}">
                    <i class="fa fa-ban"></i>
                    Junk
                </a>

                <a href="{{ route('deleted.leads') }}"
                   class="lx-btn {{ $isDeletedPage ? 'lx-btn-primary' : 'lx-btn-soft' }}">
                    <i class="fa fa-trash"></i>
                    Gelöscht
                </a>

                <a href="{{ url('/new_lead_view') }}" class="lx-btn lx-btn-soft">
                    <i class="fa fa-users"></i>
                    Alle Leads
                </a>
            </div>
        </div>

        <div class="lx-grid-stats">
            <div class="lx-stat">
                <div class="lx-stat-icon blue">
                    <i class="fa fa-database"></i>
                </div>
                <div>
                    <div class="lx-stat-label">Gesamt</div>
                    <div class="lx-stat-value">{{ number_format($totalCount, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="lx-stat">
                <div class="lx-stat-icon green">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>
                    <div class="lx-stat-label">Mit Grund</div>
                    <div class="lx-stat-value">{{ number_format($withReasonCount, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="lx-stat">
                <div class="lx-stat-icon orange">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="lx-stat-label">Ohne Grund</div>
                    <div class="lx-stat-value">{{ number_format($withoutReasonCount, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="lx-stat">
                <div class="lx-stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>
                <div>
                    <div class="lx-stat-label">Heute</div>
                    <div class="lx-stat-value">{{ number_format($todayCount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="lx-panel lx-filter">
            <div class="lx-field">
                <label class="lx-label" for="lxSearchInput">Suchen</label>
                <input
                    id="lxSearchInput"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="lx-input"
                    placeholder="Name, Firma, Kundennummer, E-Mail, Telefon..."
                    autocomplete="off"
                >
            </div>

            <input type="hidden" name="sort_by" value="{{ $currentSort }}">
            <input type="hidden" name="sort_order" value="{{ $currentDirection }}">

            <div class="lx-actions-row">
                <button type="submit" class="lx-btn lx-btn-primary">
                    <i class="fa fa-search"></i>
                    Filtern
                </button>

                <a href="{{ url()->current() }}" class="lx-btn lx-btn-soft">
                    <i class="fa fa-refresh"></i>
                    Zurücksetzen
                </a>
            </div>
        </form>

        <div class="lx-panel">
            <div class="lx-table-wrap">
                <table class="lx-table">
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ $sortUrl('new_leads.id') }}">
                                    ID
                                    <i class="{{ $sortIcon('new_leads.id') }}"></i>
                                </a>
                            </th>

                            <th>
                                <a href="{{ $sortUrl('new_leads.name') }}">
                                    Kunde
                                    <i class="{{ $sortIcon('new_leads.name') }}"></i>
                                </a>
                            </th>

                            <th>
                                <a href="{{ $sortUrl('new_leads.email') }}">
                                    Kontakt
                                    <i class="{{ $sortIcon('new_leads.email') }}"></i>
                                </a>
                            </th>

                            <th>Status</th>

                            <th style="width: 36%;">
                                {{ $isDeletedPage ? 'Löschgrund' : 'Junk-Grund' }}
                            </th>

                            <th>Aktionen</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $item)
                            @php
    $leadId = $item->id;
    $safeFullName = $fsName($item->name ?? null, $item->lastname ?? null);

    $reasonText = $isDeletedPage
        ? ($item->delete_reason ?? null)
        : ($item->junk_reason ?? $item->status_msg ?? null);

    $reasonDate = $isDeletedPage
        ? ($item->deleted_reason_at ?? $item->deleted_at ?? null)
        : ($item->junked_at ?? null);

    $actorName = $fsName(
        $item->reason_actor_name ?? null,
        $item->reason_actor_lastname ?? null
    );

    $phone = $item->phone ?? $item->telephone ?? null;

    $profileUrl = url('new_lead_profile/' . $leadId);

    $stage = $isDeletedPage ? 'deleted' : 'junk';

    $reasonDisplay = $fs($reasonText, 'Kein Grund hinterlegt');
                            @endphp

                            <tr id="lx-lead-row-{{ $leadId }}">
                                <td>
                                    <div class="lx-id">#{{ $leadId }}</div>

                                    @if(!empty($item->customer_no))
                                        <div style="margin-top: 7px;">
                                            <span class="lx-badge lx-badge-blue">
                                                K-Nr. {{ $item->customer_no }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ $profileUrl }}" class="lx-customer-name">
                                        {{ $safeFullName }}
                                    </a>

                                    @if(!empty($item->firma))
                                        <div class="lx-muted-line">
                                            <i class="fa fa-building-o"></i>
                                            {{ $item->firma }}
                                        </div>
                                    @endif

                                    @if(!empty($item->source) || !empty($item->quelle))
                                        <div style="margin-top: 8px;">
                                            <span class="lx-badge">
                                                {{ $fs($item->source ?? $item->quelle ?? null, 'Keine Quelle') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="lx-small">
                                        @if(!empty($item->email))
                                            <div>
                                                <i class="fa fa-envelope-o"></i>
                                                {{ $item->email }}
                                            </div>
                                        @else
                                            <div>
                                                <i class="fa fa-envelope-o"></i>
                                                Keine E-Mail
                                            </div>
                                        @endif

                                        @if(!empty($phone))
                                            <div>
                                                <i class="fa fa-phone"></i>
                                                {{ $phone }}
                                            </div>
                                        @else
                                            <div>
                                                <i class="fa fa-phone"></i>
                                                Kein Telefon
                                            </div>
                                        @endif

                                        @if(!empty($item->postcode) || !empty($item->city))
                                            <div>
                                                <i class="fa fa-map-marker"></i>
                                                {{ trim(($item->postcode ?? '') . ' ' . ($item->city ?? '')) ?: '-' }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if($isDeletedPage)
                                        <span class="lx-badge lx-badge-red">
                                            <i class="fa fa-trash"></i>
                                            Gelöscht
                                        </span>
                                    @else
                                        <span class="lx-badge lx-badge-orange">
                                            <i class="fa fa-ban"></i>
                                            Junk
                                        </span>
                                    @endif

                                    <div style="margin-top: 8px;">
                                        <span class="lx-badge">
                                            {{ Str::limit($item->status_msg ?? 'Kein Status', 36) }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="lx-reason-card {{ $isDeletedPage ? 'deleted' : '' }}">
                                        <div
                                            class="lx-reason-text {{ empty($reasonText) ? 'lx-reason-placeholder' : '' }}"
                                            id="lx-reason-text-{{ $leadId }}"
                                        >{{ $reasonDisplay }}</div>

                                        <div class="lx-reason-meta" id="lx-reason-meta-{{ $leadId }}">
                                            <div>
                                                <strong>Von:</strong>
                                                <span id="lx-reason-actor-{{ $leadId }}">{{ $actorName }}</span>
                                            </div>

                                            <div>
                                                <strong>Datum:</strong>
                                                <span id="lx-reason-date-{{ $leadId }}">
                                                    {{ $reasonDate ? Carbon::parse($reasonDate)->format('d.m.Y H:i') : '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="lx-action-stack">
                                        <a href="{{ $profileUrl }}" class="lx-btn lx-btn-soft">
                                            <i class="fa fa-user"></i>
                                            Profil ansehen
                                        </a>

                                        {{-- IMPORTANT:
                                             This button is protected from global JS:
                                             - No generic class like edit-btn
                                             - No text-search logic
                                             - JS listens only to .lxjs-edit-reason inside #lxLeadReasonApp
                                        --}}
                                        <button
                                            type="button"
                                            class="lx-btn lx-btn-edit lxjs-edit-reason"
                                            data-lead-id="{{ $leadId }}"
                                            data-stage="{{ $stage }}"
                                            data-lead-name="{{ e($safeFullName) }}"
                                            data-reason="{{ e($reasonText ?? '') }}"
                                            aria-label="Grund bearbeiten für {{ e($safeFullName) }}"
                                        >
                                            <i class="fa fa-pencil"></i>
                                            <span>Grund bearbeiten</span>
                                        </button>

                                        @if($isDeletedPage)
                                            <a href="{{ url('/restore_leads/' . $leadId) }}"
                                               class="lx-btn lx-btn-soft"
                                               onclick="return confirm('Diesen Lead wirklich wiederherstellen?')">
                                                <i class="fa fa-undo"></i>
                                                Wiederherstellen
                                            </a>
                                        @else
                                            <form action="{{ route('leads.unjunk.reason', $leadId) }}"
                                                  method="POST"
                                                  style="margin: 0;">
                                                @csrf

                                                <input
                                                    type="hidden"
                                                    name="reason"
                                                    value="Aus Junk-Liste wiederhergestellt"
                                                >

                                                <button
                                                    type="submit"
                                                    class="lx-btn lx-btn-soft"
                                                    onclick="return confirm('Diesen Lead wirklich aus Junk entfernen?')"
                                                >
                                                    <i class="fa fa-undo"></i>
                                                    Aus Junk entfernen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="lx-empty">
                                    <div class="lx-empty-icon">
                                        <i class="fa fa-search"></i>
                                    </div>

                                    <div style="font-size: 16px; font-weight: 950; color: var(--lx-slate-700);">
                                        Keine Leads gefunden
                                    </div>

                                    <div style="margin-top: 5px;">
                                        Es gibt aktuell keine Einträge für diese Ansicht.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($isPaginator && method_exists($data, 'links') && $data->hasPages())
            <div class="lx-pagination">
                {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>

    <div class="lx-modal-backdrop" id="lxReasonModal" aria-hidden="true">
        <div class="lx-modal" role="dialog" aria-modal="true" aria-labelledby="lxReasonModalTitle">
            <form id="lxReasonForm">
                @csrf

                <input type="hidden" id="lxReasonLeadId">
                <input type="hidden" id="lxReasonStage">

                <div class="lx-modal-head">
                    <div>
                        <h2 class="lx-modal-title" id="lxReasonModalTitle">
                            Grund bearbeiten
                        </h2>

                        <div class="lx-modal-subtitle" id="lxReasonModalSubtitle">
                            Bearbeite den hinterlegten Grund für diesen Lead.
                        </div>
                    </div>

                    <button type="button" class="lx-modal-close-x lxjs-close-modal" aria-label="Modal schließen">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="lx-modal-body">
                    <label for="lxReasonTextarea" class="lx-label">
                        Begründung
                    </label>

                    <textarea
                        id="lxReasonTextarea"
                        class="lx-textarea"
                        required
                        placeholder="Bitte Grund eingeben..."
                    ></textarea>

                    <div class="lx-help">
                        Tipp: Der Text wird direkt in der Tabelle aktualisiert, ohne die Seite neu zu laden.
                    </div>
                </div>

                <div class="lx-modal-footer">
                    <button type="button" class="lx-btn lx-btn-soft lxjs-close-modal">
                        Abbrechen
                    </button>

                    <button type="submit" class="lx-btn lx-btn-primary" id="lxReasonSubmitBtn">
                        <i class="fa fa-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lx-toast" id="lxToast"></div>
@endsection

@section('script')
    <script>
        (() => {
            'use strict';

            const app = document.getElementById('lxLeadReasonApp');

            if (!app) {
                return;
            }

            const modal = document.getElementById('lxReasonModal');
            const form = document.getElementById('lxReasonForm');
            const leadIdInput = document.getElementById('lxReasonLeadId');
            const stageInput = document.getElementById('lxReasonStage');
            const textarea = document.getElementById('lxReasonTextarea');
            const title = document.getElementById('lxReasonModalTitle');
            const subtitle = document.getElementById('lxReasonModalSubtitle');
            const submitBtn = document.getElementById('lxReasonSubmitBtn');
            const toast = document.getElementById('lxToast');

            const updateBase = app.dataset.updateBase || '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const showToast = (message, type = 'success') => {
                if (!toast) {
                    alert(message);
                    return;
                }

                toast.textContent = message;
                toast.className = `lx-toast is-visible ${type}`;

                window.clearTimeout(showToast._timer);
                showToast._timer = window.setTimeout(() => {
                    toast.className = 'lx-toast';
                    toast.textContent = '';
                }, 2800);
            };

            const openModal = (button) => {
                const leadId = button.dataset.leadId || '';
                const stage = button.dataset.stage || 'junk';
                const leadName = button.dataset.leadName || 'Lead';
                const reason = button.dataset.reason || '';

                leadIdInput.value = leadId;
                stageInput.value = stage;
                textarea.value = reason;

                title.textContent = stage === 'deleted'
                    ? 'Löschgrund bearbeiten'
                    : 'Junk-Grund bearbeiten';

                subtitle.textContent = `Lead: ${leadName}`;

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');

                window.setTimeout(() => {
                    textarea.focus();
                    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                }, 80);
            };

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');

                leadIdInput.value = '';
                stageInput.value = '';
                textarea.value = '';
            };

            const setSubmitting = (isSubmitting) => {
                submitBtn.disabled = isSubmitting;

                submitBtn.innerHTML = isSubmitting
                    ? '<i class="fa fa-spinner fa-spin"></i> Speichert...'
                    : '<i class="fa fa-save"></i> Speichern';
            };

            const updateRowReason = (leadId, reason, payload = {}) => {
                const reasonEl = document.getElementById(`lx-reason-text-${leadId}`);
                const dateEl = document.getElementById(`lx-reason-date-${leadId}`);
                const actorEl = document.getElementById(`lx-reason-actor-${leadId}`);

                const cleanReason = (reason || '').trim();

                if (reasonEl) {
                    reasonEl.textContent = cleanReason || 'Kein Grund hinterlegt';

                    if (cleanReason) {
                        reasonEl.classList.remove('lx-reason-placeholder');
                    } else {
                        reasonEl.classList.add('lx-reason-placeholder');
                    }
                }

                if (dateEl && payload.updated_at_formatted) {
                    dateEl.textContent = payload.updated_at_formatted;
                }

                if (actorEl && payload.actor_name) {
                    actorEl.textContent = payload.actor_name;
                }

                app.querySelectorAll(`.lxjs-edit-reason[data-lead-id="${CSS.escape(String(leadId))}"]`).forEach((button) => {
                    button.dataset.reason = cleanReason;
                });
            };

            app.addEventListener('click', (event) => {
                const editButton = event.target.closest('.lxjs-edit-reason');

                if (!editButton || !app.contains(editButton)) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                openModal(editButton);
            });

            document.addEventListener('click', (event) => {
                const closeButton = event.target.closest('.lxjs-close-modal');

                if (closeButton) {
                    event.preventDefault();
                    closeModal();
                }
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const leadId = leadIdInput.value;
                const stage = stageInput.value;
                const reason = textarea.value.trim();

                if (!leadId) {
                    showToast('Lead-ID fehlt.', 'error');
                    return;
                }

                if (!reason) {
                    showToast('Bitte einen Grund eingeben.', 'error');
                    textarea.focus();
                    return;
                }

                setSubmitting(true);

                try {
                    const response = await fetch(`${updateBase}/${encodeURIComponent(leadId)}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            stage: stage,
                            reason: reason,
                        }),
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(payload.message || 'Speichern fehlgeschlagen.');
                    }

                    if (payload.success === false) {
                        throw new Error(payload.message || 'Speichern fehlgeschlagen.');
                    }

                    const savedReason = payload.reason || reason;

                    updateRowReason(leadId, savedReason, payload);
                    closeModal();

                    showToast(payload.message || 'Grund wurde gespeichert.', 'success');
                } catch (error) {
                    showToast(error.message || 'Ein Fehler ist aufgetreten.', 'error');
                } finally {
                    setSubmitting(false);
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
                label: 'Kundeliste',
                url: "{{ url('/new_lead_view') }}"
            },

            {
                label: "{{ $pageTitle }}",
                url: "{{ url()->current() }}",
                clickable: false

            } 
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush