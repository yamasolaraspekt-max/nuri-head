{{-- resources/views/admin/inquiry/index.blade.php --}}
@extends('admin.layouts.app')

@section('title') ANFRAGE AUFNAHME @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .accordion-row {
        cursor: pointer;
        background: white;
        position: relative;
    }
    .accordion-content {
        display: none;
        position: relative;
    }
    .accordion-content.visible {
        display: table-row;
    }
    .table {
        color: #464545 !important;
    }

    #danger_1 .popover-header {
        background-color: #ff0000 !important;
    }

    /* SweetAlert2 custom styles */
    .swal2-popup {
        font-size: 16px !important;
        padding: 25px;
        width: 600px !important;
        max-width: 90vw;
    }
    .swal2-html-container {
        text-align: left !important;
        margin-bottom: 1rem;
    }
    .swal2-select {
        width: 100% !important;
        padding: 10px;
        font-size: 15px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        margin-top: 8px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .swal2-select:focus {
        border-color: #5A8DEE;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(90, 141, 238, 0.25);
    }
    .swal2-confirm {
        background-color: #28a745 !important;
        color: white !important;
        font-weight: bold;
        border-radius: 5px;
        padding: 8px 20px;
    }
    .swal2-cancel {
        background-color: #dc3545 !important;
        color: white !important;
        font-weight: bold;
        border-radius: 5px;
        padding: 8px 20px;
    }

    .employee-img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
    }
    .employee-img-small {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 5px;
        vertical-align: middle;
    }

    .js-menu-panel { display:none; opacity:0; transition:opacity .15s ease; }
    .js-menu-panel.is-open { display:block; opacity:1; }

    .select2-container--default .select2-results__option img,
    .select2-selection__rendered img {
        width: 26px; height: 26px; border-radius: 50%;
        margin-right: 8px; vertical-align: middle;
    }

    .circle {
        width: 35px;
        height: 35px;
        background-color: #7DC242;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .line {
        width: 9px;
        height: 4px;
        background-color: #7DC242;
        margin-left: -3px;
        margin-right: -2px;
        position: relative;
        top: 2px;
    }
    .profile,
    .profile-s,
    .profile-r {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }
    .profile  { border: 3px solid #7DC242; }
    .profile-s{ border: 3px solid #f4a459; }
    .profile-r{ border: 3px solid #ea5455; }

    .text {
        font-size: 10px;
        font-weight: 500;
        color: #555;
        text-align: center;
        margin-top: 10px;
    }

    @keyframes flash {
        0%   { background-color: #c3f3c3; }
        50%  { background-color: #a8e6a8; }
        100% { background-color: #c3f3c3; }
    }
    .animated.flash { animation: flash 2s ease-in-out 1; }

    /* Sortable header styling */
    th.sortable-th a {
        display:inline-flex;
        align-items:center;
        gap:4px;
        color:#374151;
        font-weight:600;
        text-decoration:none;
        font-size: .8rem;
        text-transform:uppercase;
    }
    th.sortable-th a:hover {
        color:#111827;
    }
    .sort-indicator {
        font-size: .75rem;
        opacity:.35;
    }
    th.sortable-th--active .sort-indicator {
        opacity:1;
    }

    .chip {
        display:inline-flex;align-items:center;border-radius:16px;
        height:28px;padding:0 10px;font-weight:600;line-height:1
    }
    .chip .chip-body{display:inline-flex;align-items:center}
    .chip-primary{background:#7367f0;color:#fff}
    .chip-danger{background:#ea5455;color:#fff}

    .js-menu-portal { z-index: 1075 !important; position: fixed !important; transform: none !important; }

    .custom-menu{
        position:absolute; z-index:1080; min-width:220px;
        display:none; opacity:0; transform:translateY(4px);
        background:#fff; border:1px solid rgba(0,0,0,.08);
        border-radius:.75rem; box-shadow:0 10px 30px rgba(0,0,0,.12);
        padding:.35rem; pointer-events:auto;
    }
    .custom-menu.is-open{
        display:block; opacity:1; transform:translateY(0);
        transition:opacity .12s ease, transform .12s ease;
    }
    .custom-menu-item{
        display:flex; align-items:center; gap:.5rem;
        padding:.5rem .65rem; border-radius:.5rem; color:#334155; text-decoration:none;
        white-space:nowrap;
    }
    .custom-menu-item:hover{ background:#f1f5f9; color:#0f172a; }

    .table td, .table th { vertical-align: middle; }
    .blink { animation: blink 1.2s linear infinite; }
    @keyframes blink { 50% { opacity: .35; } }

    @media (max-width: 576px) {
        .input-group > .form-control { min-width: 0; }
        .dropdown-menu { max-height: 50vh; overflow: auto; }
    }

    /* Stat cards */
    .inquiry-stat-card{
        border-radius: 1rem;
        border: 1px solid rgba(148,163,184,.35);
        background: #5da9ff2e;
        padding: 1.1rem 1.25rem;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:.75rem;
    }
    .inquiry-stat-label{
        font-size:.75rem;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:#6b7280;
        margin-bottom:.2rem;
    }
    .inquiry-stat-value{
        font-size:1.4rem;
        font-weight:700;
        color:#111827;
    }
    .inquiry-stat-pill{
        font-size:.7rem;
        padding:.15rem .55rem;
        border-radius:999px;
        background:#eff6ff;
        color:#74b2d4;
        font-weight:600;
        white-space:nowrap;
    }
    .inquiry-stat-icon{
        width:40px;
        height:40px;
        border-radius:999px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:rgba(15,23,42,.06);
        color:#111827;
    }
    @media (max-width: 767.98px){
        .inquiry-stat-card{ margin-bottom:.75rem; }
    }

    /* Drawer */
    .verify-drawer-overlay{
        position:fixed;
        inset:0;
        background:rgba(15,23,42,0.35);
        opacity:0;
        visibility:hidden;
        transition:opacity .2s ease, visibility .2s ease;
        z-index:1050;
    }
    .verify-drawer-overlay.is-open{
        opacity:1;
        visibility:visible;
    }
    .verify-drawer{
        position:fixed;
        top:0;
        right:-420px;
        width:min(420px,100%);
        height:100%;
        background:#ffffff;
        box-shadow:-12px 0 30px rgba(15,23,42,0.25);
        z-index:1051;
        display:flex;
        flex-direction:column;
        transition:right .2s ease;
    }
    .verify-drawer.is-open{ right:0; }

    .verify-drawer-header{
        padding:.9rem 1.1rem;
        border-bottom:1px solid #e5e7eb;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:.5rem;
    }
    .verify-drawer-title{
        font-size:.9rem;
        font-weight:600;
        color:#111827;
    }
    .verify-drawer-count{
        font-size:.75rem;
        color:#6b7280;
    }
    .verify-drawer-body{
        padding: .75rem 1.1rem 1rem;
        overflow-y:auto;
        flex:1;
    }
    .verify-drawer-footer{
        padding:.75rem 1.1rem 1rem;
        border-top:1px solid #e5e7eb;
        background:#f9fafb;
    }
    .verify-drawer-item{
        border-radius:.75rem;
        border:1px solid #e5e7eb;
        padding:.6rem .7rem;
        margin-bottom:.5rem;
        background:#ffffff;
    }
    .verify-drawer-badge{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        padding:.12rem .55rem;
        font-size:.7rem;
        font-weight:600;
        margin-right:.25rem;
    }
    .verify-drawer-badge--ok{
        background:#ecfdf3;
        color:#166534;
    }
    .verify-drawer-badge--warn{
        background:#fef3c7;
        color:#92400e;
    }
    .verify-drawer-badge--lead{
        background:#eff6ff;
        color:#1d4ed8;
    }
    .verify-drawer-badge--other{
        background:#f3e8ff;
        color:#6b21a8;
    }
    .verify-drawer-name{
        font-size:.9rem;
        font-weight:600;
        color:#111827;
    }
    .verify-drawer-meta{
        font-size:.75rem;
        color:#6b7280;
    }
    @media (max-width: 576px){
        .verify-drawer{ width:100%; }
        .verify-drawer-header{ flex-wrap:wrap; align-items:flex-start; }
    }
</style>
@endsection

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                @if(Route::currentRouteName()=='inquiry.view')
                    <h2 class="content-header-title float-left mb-0">ANFRAGE AUFNAHME</h2>
                @elseif(Route::currentRouteName() == 'inquiry.junk.list')
                    <h2 class="content-header-title float-left mb-0">JUNK LISTE</h2>
                @elseif(Route::currentRouteName() == 'inquiry.customer')
                    <h2 class="content-header-title float-left mb-0">ANFRAGE LISTE</h2>
                @elseif(Route::currentRouteName() == 'my.inquiry.view')
                    <h2 class="content-header-title float-left mb-0">MEINE ANFRAGE LISTE</h2>
                @else
                    <h2 class="content-header-title float-left mb-0">GELÖSCHTE ANFRAGEN</h2>
                @endif

                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            @php
                use Illuminate\Pagination\LengthAwarePaginator;

                // -----------------------------
                // German fail-safe helpers
                // -----------------------------
                $fs = function ($v, string $fallback = 'Nicht angegeben') {
                    $vv = is_string($v) ? trim($v) : $v;
                    return (isset($vv) && $vv !== '' && $vv !== null) ? $vv : $fallback;
                };

                $fsName = function ($first, $last) use ($fs) {
                    $full = trim(($first ?? '').' '.($last ?? ''));
                    return $fs($full, 'Unbenannt');
                };

                $fsCity = fn($city) => $fs($city, 'Unbekannte Stadt');

                $fsType = function ($row) use ($fs) {
                    $label = $row->type_name ?? $row->pre_type ?? null;
                    return $fs($label, 'Unbekannter Typ');
                };

                $fsImg = function (?string $relativePath, string $fallbackAsset) {
                    if (!empty($relativePath) && file_exists(public_path($relativePath))) {
                        return asset($relativePath);
                    }
                    return asset($fallbackAsset);
                };

                // -----------------------------
                // Normalize $data into $collection
                // -----------------------------
                $isPaginator = $data instanceof LengthAwarePaginator;
                $collection  = $isPaginator ? collect($data->items()) : collect($data);

                // -----------------------------
                // Safe counts
                // -----------------------------
                $totalInquiries = isset($totalInquiries)
                    ? (int) $totalInquiries
                    : ($isPaginator ? (int) $data->total() : (int) $collection->count());

                $product_list = collect($productList ?? []);
                $withProductCount = isset($withProductCount)
                    ? (int) $withProductCount
                    : (int) $product_list->pluck('inquiry_id')->filter()->unique()->count();

                $leadCount = isset($leadCount)
                    ? (int) $leadCount
                    : (int) $collection->filter(function ($row) {
                        $type = strtolower(trim($row->type_name ?? $row->pre_type ?? ''));
                        return $type === 'lead';
                    })->count();

                $otherCount = isset($otherCount)
                    ? (int) $otherCount
                    : max($totalInquiries - $leadCount, 0);

                // -----------------------------
                // Sorting (safe)
                // -----------------------------
                $allowedSorts = [
                    'id',
                    'inquiries.name',
                    'types.type',
                    'inquiries.firma',
                    'inquiries.reason',
                    'inquiries.note',
                    'emp_name',
                    'direct_name',
                    'inquiries.periority',
                    'inquiries.status',
                ];

                $currentSort = request('sort', 'id');
                if (!in_array($currentSort, $allowedSorts, true)) {
                    $currentSort = 'id';
                }

                $currentDirection = strtolower(request('direction', 'desc'));
                $currentDirection = in_array($currentDirection, ['asc', 'desc'], true) ? $currentDirection : 'desc';

                $sortUrl = function (string $column) use ($currentSort, $currentDirection, $allowedSorts) {
                    if (!in_array($column, $allowedSorts, true)) {
                        $column = 'id';
                    }
                    $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';

                    return request()->fullUrlWithQuery([
                        'sort'      => $column,
                        'direction' => $direction,
                        'page'      => 1,
                    ]);
                };

                $sortThClass = function (string $column) use ($currentSort) {
                    return $currentSort === $column ? 'sortable-th sortable-th--active' : 'sortable-th';
                };

                $sortIcon = function (string $column) use ($currentSort, $currentDirection) {
                    $neutral = '<span class="sort-indicator"><i class="fa fa-sort"></i></span>';
                    if ($currentSort !== $column) return $neutral;

                    return $currentDirection === 'asc'
                        ? '<span class="sort-indicator"><i class="fa fa-sort-amount-up"></i></span>'
                        : '<span class="sort-indicator"><i class="fa fa-sort-amount-down"></i></span>';
                };

                // -----------------------------
                // Filters + UI state
                // -----------------------------
                $typeFilters = [
                    'Lead'             => 'Lead',
                    'Lieferant'        => 'Lieferant',
                    'Hersteller'       => 'Hersteller',
                    'Geschäftspartner' => 'Geschäftspartner',
                    'Architekt'        => 'Architekt',
                    'Nachunternehmer'  => 'Nachunternehmer',
                    'Bank'             => 'Bank',
                    'Versicherung'     => 'Versicherung',
                    'Bewerber'         => 'Bewerber',
                    'other'            => 'Sonstiges',
                ];

                $highlightId = session('highlight_inquiry_id');
            @endphp

            {{-- STATS ROW --}}
            <div class="row mb-2">
                <div class="col-xl-3 col-md-6 col-sm-6 mb-1">
                    <div class="inquiry-stat-card">
                        <div>
                            <div class="inquiry-stat-label">Gesamtanfragen</div>
                            <div class="inquiry-stat-value">{{ $totalInquiries }}</div>
                            <div class="inquiry-stat-pill">Aktuelle Filter</div>
                        </div>
                        <div class="inquiry-stat-icon">
                            <i class="feather icon-layers"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-sm-6 mb-1">
                    <div class="inquiry-stat-card">
                        <div>
                            <div class="inquiry-stat-label">Mit Produkten</div>
                            <div class="inquiry-stat-value">{{ $withProductCount }}</div>
                            <div class="inquiry-stat-pill">mind. 1 Produkt</div>
                        </div>
                        <div class="inquiry-stat-icon">
                            <i class="feather icon-package"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-sm-6 mb-1">
                    <div class="inquiry-stat-card">
                        <div>
                            <div class="inquiry-stat-label">Leads</div>
                            <div class="inquiry-stat-value">{{ $leadCount }}</div>
                            <div class="inquiry-stat-pill">Typ: Lead</div>
                        </div>
                        <div class="inquiry-stat-icon">
                            <i class="feather icon-user-plus"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-sm-6 mb-1">
                    <div class="inquiry-stat-card">
                        <div>
                            <div class="inquiry-stat-label">Andere als Lead</div>
                            <div class="inquiry-stat-value">{{ $otherCount }}</div>
                            <div class="inquiry-stat-pill">z.B. Lieferant, Hersteller…</div>
                        </div>
                        <div class="inquiry-stat-icon">
                            <i class="feather icon-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid px-0" id="table-hover-animation">
                <div class="row no-gutters">
                    <div class="col-12">
                        <div class="card border-0 rounded-0">
                            <div class="card-body">

                                {{-- TOOLBAR: FILTER + SEARCH + ADD --}}
                                <div class="d-flex flex-wrap align-items-stretch gap-2 justify-content-between mb-3">
                                    <form method="GET"
                                        action="{{ url()->current() }}"
                                        class="flex-grow-1"
                                        style="min-width:260px;">
                                        <fieldset>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button"
                                                            class="btn btn-primary dropdown-toggle waves-effect waves-light"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        <i class="feather icon-filter"></i>
                                                    </button>

                                                    <div class="dropdown-menu">
                                                        @foreach($typeFilters as $value => $label)
                                                            <a class="dropdown-item"
                                                            href="{{ request()->fullUrlWithQuery(['search' => $value, 'page' => 1]) }}">
                                                                {{ $label }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <input type="text"
                                                    class="form-control"
                                                    name="search"
                                                    value="{{ request('search') }}"
                                                    placeholder="Typ, Datum, Kontakt,..."
                                                    aria-label="search">

                                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                                <input type="hidden" name="direction" value="{{ $currentDirection }}">

                                                <div class="input-group-append">
                                                    <button type="submit"
                                                            class="btn btn-primary waves-effect waves-light">
                                                        Suchen
                                                    </button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>

                                    <div class="d-flex align-items-center ml-auto mt-2 mt-sm-0">
                                        <a class="btn btn-icon rounded-circle btn-primary waves-effect waves-light"
                                        href="{{ url('inquiry_create') }}">
                                            <i class="feather icon-plus"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- BULK BAR --}}
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                    <div class="form-check mb-1">
                                        <input type="checkbox"
                                            class="form-check-input"
                                            id="checkAllInquiries">
                                        <label class="form-check-label" for="checkAllInquiries">
                                            Alle auswählen
                                        </label>
                                    </div>

                                    <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                        <span class="chip chip-primary mr-1 mb-1">
                                            <span class="chip-body">
                                                <span class="mr-50">Ausgewählt</span>
                                                <span id="selectedCount">0</span>
                                            </span>
                                        </span>

                                        <div class="btn-group mb-1 ml-0 ml-sm-1">
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm"
                                                    id="btnBulkVerify">
                                                <i class="fa fa-check-circle"></i> Ausgewählte verifizieren
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-warning btn-sm"
                                                    id="btnBulkJunk">
                                                <i class="fa fa-trash"></i> Ausgewählte Junk
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm"
                                                    id="btnBulkDelete">
                                                <i class="feather icon-trash-2"></i> Ausgewählte löschen
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- TABLE --}}
                                <div class="table-responsive w-100">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-white">
                                        <tr>
                                            <th style="width:32px;" class="text-center align-middle">
                                                <input type="checkbox"
                                                    class="form-check-input"
                                                    id="checkAllInquiriesHeader">
                                            </th>

                                            <th class="{{ $sortThClass('id') }} text-nowrap">
                                                <a href="{{ $sortUrl('id') }}">
                                                    ID {!! $sortIcon('id') !!}
                                                </a>
                                            </th>

                                            <th class="text-center text-nowrap">
                                                <div class="d-flex justify-content-around">
                                                    <a href="{{ url('/lead_important_sort') }}"
                                                    data-toggle="popover"
                                                    data-content="Bitte dringend die Anfrage bearbeiten"
                                                    data-trigger="hover"
                                                    title="Wichtigkeit sehr hoch">
                                                        <i class="feather icon-alert-circle" style="font-size:20px;"></i>
                                                    </a>

                                                    <a href="{{ url('/lead_over_clock_sort') }}"
                                                    data-toggle="popover"
                                                    data-content=">48 Stunden – dringend bearbeiten"
                                                    data-trigger="hover"
                                                    title="Zeit überschritten">
                                                        <i class="feather icon-bell" style="font-size:20px;"></i>
                                                    </a>

                                                    <a href="{{ url('/lead_new_sort') }}"
                                                    data-toggle="popover"
                                                    data-content="Bitte innerhalb von 48 Stunden qualifizieren"
                                                    data-trigger="hover"
                                                    title="Neue Anfrage">
                                                        <i class="feather icon-star" style="font-size:20px;"></i>
                                                    </a>
                                                </div>
                                            </th>

                                            <th class="{{ $sortThClass('inquiries.name') }} text-nowrap">
                                                <a href="{{ $sortUrl('inquiries.name') }}">
                                                    NAME {!! $sortIcon('inquiries.name') !!}
                                                </a>
                                            </th>

                                            <th class="{{ $sortThClass('types.type') }} text-nowrap d-none d-sm-table-cell">
                                                <a href="{{ $sortUrl('types.type') }}">
                                                    KONTAKTART {!! $sortIcon('types.type') !!}
                                                </a>
                                            </th>

                                            <th class="{{ $sortThClass('inquiries.firma') }} text-nowrap d-none d-lg-table-cell">
                                                <a href="{{ $sortUrl('inquiries.firma') }}">
                                                    FIRMA {!! $sortIcon('inquiries.firma') !!}
                                                </a>
                                            </th>

                                             

                                            <th class="{{ $sortThClass('inquiries.note') }} text-nowrap d-none d-lg-table-cell">
                                                <a href="{{ $sortUrl('inquiries.note') }}">
                                                    NOTIZ {!! $sortIcon('inquiries.note') !!}
                                                </a>
                                            </th>

                                            <th class="{{ $sortThClass('emp_name') }} text-nowrap d-none d-md-table-cell">
                                                <a href="{{ $sortUrl('emp_name') }}">
                                                    VERFASSER {!! $sortIcon('emp_name') !!}
                                                </a>
                                            </th>

                                            <th class="{{ $sortThClass('direct_name') }} text-nowrap d-none d-xl-table-cell">
                                                <a href="{{ $sortUrl('direct_name') }}">
                                                    ZUSTÄNDIG {!! $sortIcon('direct_name') !!}
                                                </a>
                                            </th>

                                            <th class="{{ $sortThClass('inquiries.periority') }} text-nowrap d-none d-md-table-cell">
                                                <a href="{{ $sortUrl('inquiries.periority') }}">
                                                    PRIORITÄT {!! $sortIcon('inquiries.periority') !!}
                                                </a>
                                            </th>

                                            <th class="{{ $sortThClass('inquiries.status') }} text-nowrap">
                                                <a href="{{ $sortUrl('inquiries.status') }}">
                                                    STATUS {!! $sortIcon('inquiries.status') !!}
                                                </a>
                                            </th>

                                            <th class="text-nowrap text-center">
                                                BEARBEITEN
                                            </th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach($data as $item)
                                            @php
                                                $rowHighlight = ($highlightId == $item->id) ? 'table-success animated flash' : '';

                                                $createdAt = $item->created_at ?? now();
                                                $currentDateTime = new DateTime();
                                                $requestDateTime = new DateTime($createdAt);
                                                $interval        = $currentDateTime->diff($requestDateTime);
                                                $hoursDifference = ($interval->days * 24) + $interval->h;

                                                $typeLabel  = $fsType($item);
                                                $hasProduct = $product_list->where('inquiry_id', $item->id)->isNotEmpty();

                                                $safeFullName = $fsName($item->name ?? null, $item->lastname ?? null);
                                                $safeCity     = $fsCity($item->city ?? null);

                                                $empTitle = $fsName($item->emp_name ?? null, $item->emp_lastname ?? null);
                                                $empAvatar = $fsImg(!empty($item->emp_image) ? ('images/employee/'.$item->emp_image) : null, 'images/gender/male.png');
                                            @endphp

                                            <tr class="bg-white {{ $rowHighlight }}"
                                                data-inquiry-id="{{ $item->id }}"
                                                data-inquiry-name="{{ $safeFullName }}"
                                                data-inquiry-type="{{ $typeLabel }}"
                                                data-inquiry-city="{{ $safeCity }}"
                                                data-has-product="{{ $hasProduct ? 1 : 0 }}"
                                                data-status="{{ $fs($item->status ?? null, 'Unbekannt') }}">

                                                <td class="text-center align-middle">
                                                    <input type="checkbox"
                                                        class="form-check-input row-check inquiry-checkbox"
                                                        name="selected_inquiries[]"
                                                        value="{{ $item->id }}">
                                                </td>

                                                <td class="text-center">{{ $item->id }}</td>

                                                <td class="text-center">
                                                    <div class="d-flex justify-content-around" style="min-width:115px;">
                                                        <i class="feather icon-alert-circle {{ (!empty($item->periority) && strtolower($item->periority) === 'sehr dringend') ? 'text-danger blink' : 'text-secondary' }}"
                                                        style="font-size:20px;"></i>

                                                        <i class="feather icon-bell {{ $hoursDifference > 48 ? 'text-primary blink' : 'text-secondary' }}"
                                                        style="font-size:20px;"></i>

                                                        <i class="feather icon-star {{ $hoursDifference <= 48 ? 'text-warning' : 'text-secondary' }}"
                                                        style="font-size:20px;"></i>
                                                    </div>
                                                </td>

                                                <td style="min-width:220px;">
                                                    <i class="feather icon-info"
                                                    data-toggle="modal"
                                                    data-target="#contact_data{{ $item->id }}"></i>

                                                    <a href="{{ url('inquiry_show/'.$item->id) }}" class="text-dark">
                                                        <p class="mb-0 font-weight-bold" style="font-size:14px;">
                                                            {{ $safeFullName }}
                                                        </p>
                                                    </a>

                                                    <small>
                                                        <i class="feather icon-pin"></i> {{ $safeCity }}
                                                    </small>

                                                    {{-- CONTACT MODAL --}}
                                                    <div class="modal fade"
                                                        id="contact_data{{ $item->id }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title">{{ $safeFullName }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <p>
                                                                        <i class="feather icon-pin"></i>
                                                                        {{ $fs($item->street ?? null, 'Unbekannte Straße') }}
                                                                        {{ $fs($item->postcode ?? null, '—') }}
                                                                        {{ $safeCity }}
                                                                    </p>

                                                                    <p class="mb-0">
                                                                        <i class="feather icon-phone-call"></i>
                                                                        {{ $fs($item->telephone ?? null, 'Keine Telefonnummer') }}
                                                                    </p>

                                                                    <p class="mb-0">
                                                                        <i class="feather icon-smartphone"></i>
                                                                        {{ $fs($item->phone ?? null, 'Keine Mobilnummer') }}
                                                                    </p>

                                                                    <p class="mb-0">
                                                                        <i class="feather icon-mail"></i>
                                                                        {{ $fs($item->email ?? null, 'Keine E-Mail') }}
                                                                    </p>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                                        Abbrechen
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="d-none d-sm-table-cell">
                                                    {{ $typeLabel }}
                                                </td>

                                                <td class="d-none d-lg-table-cell">
                                                    {{ $fs($item->firma ?? null, 'Keine Firma') }}
                                                </td>
 

                                                <td class="text-center d-none d-lg-table-cell">
                                                    @if(!empty($item->note))
                                                        <button type="button"
                                                                class="btn btn-icon rounded-circle btn-primary"
                                                                data-toggle="modal"
                                                                data-target="#note{{ $item->id }}">
                                                            <i class="fa fa-sticky-note-o"></i>
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                                class="btn btn-icon rounded-circle btn-danger"
                                                                disabled>
                                                            <i class="fa fa-sticky-note-o"></i>
                                                        </button>
                                                    @endif

                                                    <div class="modal fade"
                                                        id="note{{ $item->id }}"
                                                        tabindex="-1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title">Notizen</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="mb-2">
                                                                        <h5 class="mb-1">{{ $safeFullName }}</h5>

                                                                        <p class="mb-2">
                                                                            {{ $fs($item->street ?? null, 'Unbekannte Straße') }}<br>
                                                                            {{ $fs($item->postcode ?? null, '—') }} {{ $safeCity }}
                                                                        </p>

                                                                        <p class="mb-0">
                                                                            <i class="feather icon-phone-call"></i>
                                                                            {{ $fs($item->telephone ?? null, 'Keine Telefonnummer') }}
                                                                        </p>
                                                                        <p class="mb-0">
                                                                            <i class="feather icon-smartphone"></i>
                                                                            {{ $fs($item->phone ?? null, 'Keine Mobilnummer') }}
                                                                        </p>
                                                                        <p class="mb-0">
                                                                            <i class="feather icon-mail"></i>
                                                                            {{ $fs($item->email ?? null, 'Keine E-Mail') }}
                                                                        </p>
                                                                    </div>

                                                                    <hr>

                                                                    <h5 class="mb-2">Notiz</h5>
                                                                    <p class="mb-0">{{ $fs($item->note ?? null, 'Keine Notiz vorhanden') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="d-none d-md-table-cell" style="min-width:180px;">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar mr-2">
                                                            <img src="{{ $empAvatar }}"
                                                                alt="avatar"
                                                                height="32"
                                                                width="32"
                                                                data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="{{ $empTitle }}">
                                                        </div>
                                                        <div>
                                                            <div class="small">
                                                                {{ \Carbon\Carbon::parse($createdAt)->isoFormat('DD.MM.YY') }}
                                                            </div>
                                                            <code class="small">
                                                                {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                                                            </code>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- ZUSTÄNDIG --}}
                                                <td class="d-none d-xl-table-cell">
                                                    @php
                                                        $servicesMap = [
                                                            'complete'    => 'Komplettlösung',
                                                            'montage'     => 'Montage',
                                                            'product'     => 'Produkt',
                                                            'plan'        => 'Planung',
                                                            'maintenance' => 'Wartung',
                                                            'repair'      => 'Reparatur',
                                                            'emergency'   => 'Notdienst',
                                                            'others'      => 'Sonstiges',
                                                        ];

                                                        $male   = asset('images/gender/male.png');
                                                        $female = asset('images/gender/female.png');
                                                    @endphp

                                                    <div class="d-flex align-items-center flex-wrap">
                                                        @foreach ($product_list->where('inquiry_id', $item->id)
                                                                            ->unique(fn($p) => $p->product_id.'-'.$p->inquiry_id) as $product)
                                                            @if (($product->status ?? null) === 'open')
                                                                @php
                                                                    $serviceKey = strtolower($product->phase_section ?? '');
                                                                    $service    = $fs($servicesMap[$serviceKey] ?? ($product->phase_section ?? null), 'Unbekannte Dienstleistung');
                                                                    $department = $fs($product->department_name ?? null, 'Unbekannte Abteilung');

                                                                    $insideExists = !empty($product->eimage) && file_exists(public_path('images/employee/'.$product->eimage));
                                                                    $insideImg    = $insideExists
                                                                        ? asset('images/employee/'.$product->eimage)
                                                                        : (strtolower($product->egender ?? '') === 'male' ? $male : $female);

                                                                    $fieldExists = !empty($product->fimage) && file_exists(public_path('images/employee/'.$product->fimage));
                                                                    $fieldImg    = $fieldExists
                                                                        ? asset('images/employee/'.$product->fimage)
                                                                        : (strtolower($product->fgender ?? '') === 'male' ? $male : $female);

                                                                    $insideTitle = !empty($product->employee_id)
                                                                        ? $fsName($product->ename ?? null, $product->elastname ?? null)
                                                                        : 'Innendienst auswählen';

                                                                    $fieldTitle = !empty($product->field_employee)
                                                                        ? $fsName($product->fname ?? null, $product->flastname ?? null)
                                                                        : 'Außendienst auswählen';

                                                                    $circleTitle = $fs($product->article_group ?? null, 'Unbekannt');
                                                                    $circleText  = $fs($product->initial ?? null, '?');
                                                                @endphp

                                                                <div class="d-flex flex-column align-items-center mr-3 mb-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="circle"
                                                                            data-toggle="tooltip"
                                                                            title="{{ $circleTitle }}">
                                                                            {{ $circleText }}
                                                                        </div>

                                                                        <div class="line"></div>

                                                                        <div class="image select-employee"
                                                                            data-type="employee"
                                                                            data-id="{{ $product->id }}"
                                                                            data-department="{{ $product->department_id }}"
                                                                            data-toggle="tooltip"
                                                                            title="{{ $insideTitle }}">
                                                                            <img src="{{ $insideImg }}"
                                                                                alt="Innendienst"
                                                                                class="profile">
                                                                        </div>

                                                                        <div class="line"></div>

                                                                        <div class="image select-employee"
                                                                            data-type="field_employee"
                                                                            data-id="{{ $product->id }}"
                                                                            data-department="{{ $product->department_id }}"
                                                                            data-toggle="tooltip"
                                                                            title="{{ $fieldTitle }}">
                                                                            <img src="{{ $fieldImg }}"
                                                                                alt="Außendienst"
                                                                                class="profile-s">
                                                                        </div>
                                                                    </div>

                                                                    <div class="small">{{ $service }}</div>
                                                                    <div class="small text-muted">{{ $department }}</div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>

                                                <td class="d-none d-md-table-cell">
                                                    {{ $fs($item->periority ?? null, 'Keine Priorität') }}
                                                </td>

                                                <td class="text-nowrap">
                                                    @if(($item->status ?? null) === "Unpublished" && ($item->deleted_at ?? null) === null)
                                                        <span class="badge badge-secondary">Nicht verifiziert</span>
                                                    @elseif(($item->status ?? null) === "Published" && ($item->deleted_at ?? null) === null)
                                                        <span class="badge badge-success">Veröffentlicht</span>
                                                    @elseif(($item->status ?? null) === "Junk" && ($item->deleted_at ?? null) === null)
                                                        <span class="badge badge-danger">JUNK</span>
                                                    @elseif(($item->deleted_at ?? null) !== null)
                                                        <span class="badge badge-danger">GELÖSCHT</span>
                                                    @else
                                                        <span class="badge badge-primary">{{ $fs($item->status ?? null, 'Verifiziert') }}</span>
                                                    @endif
                                                </td>

                                                
                                                <td class="text-center">
                                                    @php
                                                        $menuId   = "menu-{$item->id}";
                                                        $toggleId = "toggle-{$item->id}";
                                                        $panelId  = "panel-{$item->id}";

                                                        $canUpdate = DB::table('user_rolls')
                                                            ->where('user_id', auth()->user())
                                                            ->where('item_id', 'Customer') 
                                                            ->where('is_update', 1)
                                                            ->exists();
                                                        

                                                        $canDelete = DB::table('user_rolls')
                                                            ->where('user_id', auth()->user())
                                                            ->where('item_id', 'Customer') 
                                                            ->where('is_delete', 1)
                                                            ->exists();

                                                        $canVerify = true;

                                                        $info = [
                                                            'id'        => $item->id,
                                                            'name'      => $fs($item->name ?? null, 'Unbenannt'),
                                                            'lastname'  => $fs($item->lastname ?? null, ''),
                                                            'type'      => $item->type,
                                                            'type_name' => $item->type_name,
                                                            'pre_type'  => $item->pre_type,
                                                        ];
                                                    @endphp

                                                    <div id="{{ $menuId }}"
                                                        class="js-menu inline-flex relative"
                                                        data-menu-id="{{ $menuId }}"
                                                        data-menu-align="end"
                                                        data-menu-offset="0,8"
                                                        data-menu-portal="true">

                                                        <button id="{{ $toggleId }}"
                                                                type="button"
                                                                class="btn btn-primary"
                                                                aria-haspopup="true"
                                                                aria-expanded="false"
                                                                aria-controls="{{ $panelId }}">
                                                            <i class="feather icon-menu"></i>
                                                        </button>

                                                        <div id="{{ $panelId }}"
                                                            class="js-menu-panel custom-menu"
                                                            role="menu"
                                                            aria-labelledby="{{ $toggleId }}">

                                                            @if($item->deleted_at)
                                                                <a href="{{ url('inquiry_restore/'.$item->id) }}" class="custom-menu-item">
                                                                    <i class="feather icon-refresh-ccw text-warning"></i>
                                                                    Wiederherstellen
                                                                </a>
                                                            @endif

                                                            @if($canUpdate)
                                                                <a href="{{ url('inquiry_edit/'.$item->id) }}" class="custom-menu-item">
                                                                    <i class="feather icon-edit text-warning"></i>
                                                                    Bearbeiten
                                                                </a>
                                                            @endif

                                                            @if($canVerify)
                                                                <a href="#" class="custom-menu-item verify-btn" data-info='@json($info)'>
                                                                    <i class="fa fa-check-circle text-primary"></i>
                                                                    Verifizieren
                                                                </a>
                                                            @endif

                                                            @if($canDelete)
                                                                <a href="#" class="custom-menu-item" data-toggle="modal" data-target="#delete-pro{{ $item->id }}">
                                                                    <i class="feather icon-trash-2 text-danger"></i>
                                                                    Löschen
                                                                </a>
                                                            @endif

                                                            @if($canDelete)
                                                                <a href="#" class="custom-menu-item addNewProduct" data-toggle="modal" data-target="#addProductModal" data-id="{{ $item->id }}">
                                                                    <i class="feather icon-plus text-success"></i>
                                                                    Produkt erstellen
                                                                </a>
                                                            @endif

                                                            @if($canDelete && ($item->status ?? null) !== 'Junk')
                                                                <a href="#" class="custom-menu-item" data-toggle="modal" data-target="#junk{{ $item->id }}">
                                                                    <i class="fa fa-trash text-danger"></i>
                                                                    Junk
                                                                </a>
                                                            @elseif($canDelete)
                                                                <a href="#" class="custom-menu-item" data-toggle="modal" data-target="#unjunk{{ $item->id }}">
                                                                    <i class="fa fa-undo text-primary"></i>
                                                                    Unjunk
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Delete Inquiry Modal --}}
                                            <div class="modal fade" id="delete-pro{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Anfrage löschen</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Möchten Sie diese Anfrage wirklich löschen? Diese Aktion kann rückgängig gemacht werden (Papierkorb).
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                            <a href="{{ route('inquiry.delete', $item->id) }}" class="btn btn-danger">Löschen</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Mark as Junk Modal --}}
                                            <div class="modal fade" id="junk{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning text-white">
                                                            <h5 class="modal-title">Als Junk markieren</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Diese Anfrage als <strong>Junk</strong> markieren?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                            <a href="{{ route('inquiry.junk', $item->id) }}" class="btn btn-warning text-white">Junk</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Unjunk Modal --}}
                                            <div class="modal fade" id="unjunk{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">Junk entfernen</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Diese Anfrage wiederherstellen (kein Junk)?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                            <a href="{{ route('inquiry.unjunk', $item->id) }}" class="btn btn-primary">Unjunk</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    @if($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                        {{ $data->links() }}
                                    @endif
                                </div>

                                {{-- ADD PRODUCT MODAL --}}
                                <div class="modal fade" id="addProductModal" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <form id="addProductForm">
                                            @csrf
                                            <input type="hidden" name="inquiry_id" id="modal_inquiry_id">

                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Produkt hinzufügen</h5>
                                                    <button type="button" class="close" data-dismiss="modal">×</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle">
                                                            <thead>
                                                            <tr>
                                                                <th>Produkt</th>
                                                                <th>Dienstleistung</th>
                                                                <th>Abteilung</th>
                                                                <th>Innendienst</th>
                                                                <th>Außendienst</th>
                                                                <th>Termin</th>
                                                                <th>Aktion</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="existingProductRows"></tbody>
                                                            <tbody id="modalNewRows"></tbody>
                                                        </table>
                                                    </div>

                                                    <button type="button" class="btn btn-sm btn-success mt-1" id="modalAddRow">
                                                        + Neue
                                                    </button>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">
                                                        Speichern
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div> {{-- card-body --}}
                        </div> {{-- card --}}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- VERIFY DRAWER --}}
<div id="verifyDrawerOverlay" class="verify-drawer-overlay"></div>
<div id="verifyDrawer" class="verify-drawer">
    <div class="verify-drawer-header">
        <div>
            <div class="verify-drawer-title">Ausgewählte Anfragen prüfen</div>
            <div class="verify-drawer-count">
                <span id="verifyDrawerCount">0</span> ausgewählt
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="verifyDrawerClose">
            <i class="feather icon-x"></i>
        </button>
    </div>
    <div class="verify-drawer-body">
        <div class="mb-2">
            <label for="verifyDrawerType" class="small font-weight-bold mb-1">
                Zieltyp für alle ausgewählten:
            </label>
            <select id="verifyDrawerType" class="form-control form-control-sm">
                <option value="">Typ wählen…</option>
                <option value="Lead">Lead</option> 
                <option value="Lieferant">Lieferant</option>
                <option value="Hersteller">Hersteller</option>
                <option value="Geschäftspartner">Geschäftspartner</option>
                <option value="Architekt">Architekt</option>
                <option value="Nachunternehmer">Nachunternehmer</option>
                <option value="Bank">Bank</option>
                <option value="Versicherung">Versicherung</option>
                <option value="Bewerber">Bewerber</option>
                <option value="others">Sonstiges</option>
            </select>
            <small class="text-muted d-block mt-1">
                Hinweis: Für Lead/Kunde sollten bereits Produkte hinterlegt sein.
            </small>
        </div>

        <hr class="my-2">

        <div id="verifyDrawerList"></div>
    </div>
    <div class="verify-drawer-footer">
        <button type="button"
                class="btn btn-primary btn-block"
                id="verifyDrawerApply">
            <i class="fa fa-check-circle mr-50"></i> Verifizierung ausführen
        </button>
    </div>
</div>
<!-- END: Content-->
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- TOASTR FLASH --}}
<script>
    $(document).ready(function(){
        @if(Session::has('update_msg'))
            toastr.success("{{ session('update_msg') }}");
        @endif
        @if(Session::has('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script>

{{-- SINGLE VERIFY (PER ROW) --}}
<script>
$(document).on('click', '.verify-btn', function (e) {
    e.preventDefault();

    if (typeof closeClosestMenuHelper === 'function') {
        closeClosestMenuHelper(this);
    }

    const rawData = $(this).attr('data-info') || '{}';
    let data;

    try {
        data = JSON.parse(rawData);
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Fehlerhafte Daten',
            text: 'Verifizierungsdaten konnten nicht gelesen werden.',
        });
        return;
    }

    if (!data || !data.id || (!data.name && !data.lastname)) {
        Swal.fire({
            icon: 'error',
            title: 'Fehlende Informationen',
            text: 'Es muss mindestens Vor- oder Nachname vorhanden sein.',
        });
        return;
    }

    const currentType = data.type_name || data.pre_type || '';
    const options = [
        "Lead", "Lieferant", "Hersteller", "Geschäftspartner",
        "Architekt", "Nachunternehmer", "Bank", "Versicherung", "Bewerber", "others"
    ];

    let optionsHtml = '';
    options.forEach(opt => {
        const selected = (opt.toLowerCase() === currentType.toLowerCase()) ? 'selected' : '';
        optionsHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
    });

    Swal.fire({
        title: 'Anfrage verifizieren',
        html: `
            <div style="text-align:left;font-size:16px;">
                <p><strong>Name:</strong> ${data.name ?? ''} ${data.lastname ?? ''}</p>
                <p><strong>Aktueller Typ:</strong> ${currentType || '—'}</p>
                <label for="verifyOption"><strong>Neuer Typ wählen:</strong></label>
                <select id="verifyOption" class="swal2-select">
                    ${optionsHtml}
                </select>
            </div>
        `,
        width: 600,
        showCancelButton: true,
        confirmButtonText: 'Verifizieren',
        cancelButtonText: 'Abbrechen',
        focusConfirm: false,
        preConfirm: () => {
            const selectEl = document.getElementById('verifyOption');
            if (!selectEl || !selectEl.value) {
                Swal.showValidationMessage('Bitte wählen Sie einen Typ aus.');
                return false;
            }
            return selectEl.value;
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        const selectedType = result.value;

       $.ajax({
            url: `/inquiry/${data.id}/verify`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                type: selectedType
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Verifiziert',
                    text: 'Die Anfrage wurde erfolgreich verifiziert und übertragen.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // ✅ Check if controller sent a redirect URL (for leads)
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else {
                        // Fallback for other types
                        location.reload();
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: 'Verifizierung fehlgeschlagen. Bitte erneut versuchen.',
                });
            }
        });
    });
});
</script>

{{-- ARTICLE FILTER --}}
<script>
$(document).ready(function() {
    $('.articles input[type="radio"]').on('change', function() {
        $('.articles input[type="radio"] + label').css({
            'background': '#b1aaaa',
            'color': 'inherit',
            'border-radius': '50%'
        });

        if (this.checked) {
            $(this).next('label').css({
                'background': '#92b532',
                'color': 'white',
                'border-radius': '50%'
            });

            const articleGroup = $(this).val();
            $.ajax({
                url: '/customer_details',
                method: 'GET',
                data: { search: articleGroup, is_ajax: true },
                success: function(response) {
                    $('#results').html(response);
                },
                error: function(error) {
                    console.error(error);
                }
            });
        }
    });
});
</script>

{{-- COLLAPSE UPPER VIEW --}}
<script>
document.addEventListener('click', function(e){
    const btn = e.target.closest('#colaps');
    if (!btn) return;

    const section = document.getElementById('upper_view');
    if (!section) return;

    const icon = btn.querySelector('i');
    const isHidden = getComputedStyle(section).display === 'none';

    section.style.display = isHidden ? 'block' : 'none';
    if (icon){
        icon.classList.toggle('icon-chevron-down', !isHidden);
        icon.classList.toggle('icon-chevron-up',   isHidden);
    }
});
</script>

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const employeeImagePath = "{{ asset('images/employee/') }}";
</script>

{{-- PRODUCT / SERVICE / EMPLOYEE MODAL LOGIC --}}
 <script>
// ... [Keep constants SVC, PROD, DEPTS, etc. exactly as they were] ...
const SVC      = @json($serviceList ?? []);
const PROD     = @json($products ?? []);
const DEPTS    = @json($departments ?? []);

const EMP_IMG  = "{{ asset('images/employee') }}";
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const URL_EMP  = '{{ route("inquiry.department.employees") }}';
const URL_SAVE = '{{ route("inquiry.products.save") }}';
const URL_DEL  = '{{ route("inquiry.products.delete") }}';

let modalRowIndex = 0;

const tService = (k) => {
    const m = {
        complete:'Komplettlösung', montage:'Montage', product:'Produkt', plan:'Planung',
        maintenance:'Wartung', repair:'Reparatur', reclaim:'Reklamation',
        emergency:'Notdienst', others:'Sonstiges'
    };
    return m[(k||'').toLowerCase()] || (k||'');
};

const debounce = (fn, ms=150)=>{ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; };

function formatEmployee(opt){
    if(!opt.id) return opt.text;
    const $el = $(opt.element);
    const img = $el.data('img') ? `${EMP_IMG}/${$el.data('img')}` : '';
    const pos = $el.data('positions') || '';
    return `
        <div style="display:flex;align-items:center;">
            ${img
                ? `<img src="${img}" class="me-2 rounded-circle" style="width:36px;height:36px;object-fit:cover;">`
                : `<div class="me-2 rounded-circle" style="width:36px;height:36px;background:#e5e7eb;"></div>`
            }
            <div><strong>${opt.text}</strong><br><small>${pos}</small></div>
        </div>`;
}
const formatEmployeeSelection = o => o.text;

function ensureOption($sel, value, label){
    const v = String(value);
    if (!$sel.find(`option[value="${v}"]`).length) {
        $sel.append(`<option value="${v}">${label}</option>`);
    }
}
function fillEmployeesSelect($sel, list, placeholder){
    $sel.empty().append(`<option value="">${placeholder}</option>`);
    (list || []).forEach(emp=>{
        $sel.append(
        `<option value="${emp.id}" data-img="${emp.image||''}" data-positions="${(emp.positions||[]).join(', ')}">${emp.name} ${emp.lastname}</option>`
        );
    });
    $sel.trigger('change.select2');
}
function loadServices(idx){
    const pid = $(`.product-select[data-index="${idx}"]`).val();
    const $s  = $(`.service-select[data-index="${idx}"]`);
    const list= SVC.filter(x => String(x.product_id)===String(pid));
    $s.empty().append('<option value="">Service wählen</option>');
    list.forEach(x => $s.append(`<option value="${x.id}">${tService(x.phase_section)}</option>`));
    $s.trigger('change.select2');
}
function fetchEmployees({ pid, did=null, sid=null, stage='inquiry' }){
    return $.post(URL_EMP, {
        _token: CSRF,
        product_id: pid,
        department_id: did,
        service_id: sid,
        stage: stage
    });
}

function newModalRow(idx){
    return `
        <tr data-index="${idx}">
            <td>
                <select class="form-select product-select" data-index="${idx}" name="product_id[]" style="width:100%">
                    <option value="">Produkt wählen</option>
                    ${PROD.map(p=>`<option value="${p.id}" data-img="${p.image||''}">${p.article_group}</option>`).join('')}
                </select>
            </td>
            <td>
                <select class="form-select service-select" data-index="${idx}" name="service_id[]" style="width:100%">
                    <option value="">Service wählen</option>
                </select>
            </td>
            <td>
                <select class="form-select department-select" data-index="${idx}" name="department_id[]" style="width:100%">
                    <option value="">Abteilung wählen</option>
                    ${DEPTS.map(d=>`<option value="${d.id}">${d.department_name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select class="form-select employee-select" data-index="${idx}" name="employee_id[]" style="width:100%">
                    <option value="">Innendienst wählen</option>
                </select>
            </td>
            <td>
                <select class="form-select field-employee-select" data-index="${idx}" name="field_employee[]" style="width:100%">
                    <option value="">Außendienst wählen</option>
                </select>
            </td>
            <td>
                <input type="datetime-local" class="form-control" name="appointment_date[]" data-index="${idx}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                    <i class="feather icon-trash"></i>
                </button>
            </td>
        </tr>`;
}

function initRow(idx){
    const $modal = $('#addProductModal');
    const $p   = $(`.product-select[data-index="${idx}"]`);
    const $s   = $(`.service-select[data-index="${idx}"]`);
    const $d   = $(`.department-select[data-index="${idx}"]`);
    const $eIn = $(`.employee-select[data-index="${idx}"]`);
    const $eOut= $(`.field-employee-select[data-index="${idx}"]`);

    const sel2 = ($el, conf={}) => $el.select2({ width:'100%', dropdownParent: $modal, ...conf });
    sel2($p); sel2($s); sel2($d);
    sel2($eIn,  { templateResult:formatEmployee, templateSelection:formatEmployeeSelection, escapeMarkup:m=>m });
    sel2($eOut, { templateResult:formatEmployee, templateSelection:formatEmployeeSelection, escapeMarkup:m=>m });

    $p.on('change', async () => {
        const pid = $p.val();
        if (!pid){
            $s.empty().append('<option value="">Service wählen</option>').trigger('change.select2');
            $d.val('').trigger('change.select2');
            fillEmployeesSelect($eIn, [], 'Innendienst wählen');
            fillEmployeesSelect($eOut, [], 'Außendienst wählen');
            return;
        }

        loadServices(idx);

        try {
            const res = await fetchEmployees({ pid, stage:'inquiry' });
            const did = res?.department_id ? String(res.department_id) : '';
            const sid = res?.service_id    ? String(res.service_id)    : '';

            if (did){
                ensureOption(
                    $d,
                    did,
                    DEPTS.find(x=>String(x.id)===did)?.department_name || `Abt. ${did}`
                );
                $d.val(did).trigger('change.select2');
            }

            if (sid){
                const svcMeta  = SVC.find(x=>String(x.id)===sid);
                const svcLabel = tService(svcMeta?.phase_section || '');
                ensureOption($s, sid, svcLabel || `Service ${sid}`);
                $s.val(sid).trigger('change.select2');
            }

            const internal = Array.isArray(res?.internal_employees) ? res.internal_employees : [];
            const external = Array.isArray(res?.external_employees) && res.external_employees.length
                ? res.external_employees
                : internal;

            fillEmployeesSelect($eIn,  internal, 'Innendienst wählen');
            fillEmployeesSelect($eOut, external, 'Außendienst wählen');

        } catch (e) {
            refreshEmployees(idx);
        }
    });

    $d.on('change', debounce(()=> refreshEmployees(idx)));
    $s.on('change', debounce(()=> refreshEmployees(idx)));
}

function refreshEmployees(idx){
    const pid = $(`.product-select[data-index="${idx}"]`).val();
    const sid = $(`.service-select[data-index="${idx}"]`).val();
    const did = $(`.department-select[data-index="${idx}"]`).val();
    const $eIn  = $(`.employee-select[data-index="${idx}"]`);
    const $eOut = $(`.field-employee-select[data-index="${idx}"]`);

    if (!pid){
        fillEmployeesSelect($eIn, [], 'Innendienst wählen');
        fillEmployeesSelect($eOut, [], 'Außendienst wählen');
        return;
    }

    fetchEmployees({ pid, did, sid, stage:'inquiry' }).done(res=>{
        if (res?.department_id && String(res.department_id)!==String(did || '')) {
            const $d = $(`.department-select[data-index="${idx}"]`);
            ensureOption(
                $d,
                res.department_id,
                DEPTS.find(x=>String(x.id)===String(res.department_id))?.department_name
                    || `Abt. ${res.department_id}`
            );
            $d.val(String(res.department_id)).trigger('change.select2');
        }
        if (res?.service_id && String(res.service_id)!==String(sid || '')) {
            const $s = $(`.service-select[data-index="${idx}"]`);
            const svcLabel = tService(SVC.find(x=>String(x.id)===String(res.service_id))?.phase_section || '');
            ensureOption($s, res.service_id, svcLabel || `Service ${res.service_id}`);
            $s.val(String(res.service_id)).trigger('change.select2');
        }

        const internal = Array.isArray(res?.internal_employees) ? res.internal_employees : [];
        const external = Array.isArray(res?.external_employees) && res.external_employees.length
            ? res.external_employees
            : internal;

        fillEmployeesSelect($eIn,  internal, 'Innendienst wählen');
        fillEmployeesSelect($eOut, external, 'Außendienst wählen');
    }).fail(()=>{
        fillEmployeesSelect($eIn, [], 'Innendienst wählen');
        fillEmployeesSelect($eOut, [], 'Außendienst wählen');
    });
}

$(document).ready(function () {
    $('#addProductModal').on('hidden.bs.modal', () => location.reload());

    // 1. OPEN MODAL & LOAD EXISTING PRODUCTS
    $(document).on('click', '.addNewProduct', function () {
        const inquiryId = $(this).data('id');
        $('#modal_inquiry_id').val(inquiryId);
        $('#existingProductRows').empty();
        $('#modalNewRows').empty();
        modalRowIndex = 0;

        $.get(`/inquiry/get/products/${inquiryId}`, function (rows) {
            (rows||[]).forEach(row => {
                const productLabel    = row.article_group || '—';
                const serviceLabel    = tService(row.phase_section);
                const departmentLabel = row.department_name || '—';
                const inImg  = row.in_image  ? `${EMP_IMG}/${row.in_image}`  : '{{ asset("images/gender/male.png") }}';
                const outImg = row.out_image ? `${EMP_IMG}/${row.out_image}` : '{{ asset("images/gender/male.png") }}';
                const dateLabel = row.appointment_date ? new Date(row.appointment_date).toLocaleString('de-DE') : '—';

                $('#existingProductRows').append(`
                    <tr>
                        <td>${productLabel}</td>
                        <td>${serviceLabel}</td>
                        <td>${departmentLabel}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="${inImg}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                                <span>${row.in_name ?? ''} ${row.in_lastname ?? ''}</span>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="${outImg}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                                <span>${row.out_name ?? ''} ${row.out_lastname ?? ''}</span>
                            </div>
                        </td>
                        <td>${dateLabel}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger delete-product" data-id="${row.id}">
                                <i class="feather icon-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        });

        $('#addProductModal').modal('show');
        // Optional: Automatically add one new empty row
        $('#modalAddRow').trigger('click');
    });

    $('#modalAddRow').on('click', function () {
        modalRowIndex++;
        $('#modalNewRows').append(newModalRow(modalRowIndex));
        initRow(modalRowIndex);
    });

    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });

    // 2. DELETE EXISTING PRODUCT VIA AJAX
    $(document).on('click', '.delete-product', function () {
        const $tr  = $(this).closest('tr');
        const rowId= $(this).data('id');

        Swal.fire({
            title:'Bist du sicher?',
            text:'Das Produkt wird dauerhaft gelöscht.',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ja, löschen!',
            cancelButtonText:'Abbrechen'
        }).then(res => {
            if(!res.isConfirmed) return;
            $.ajax({
                url: URL_DEL,
                method: 'DELETE',
                data: { _token: CSRF, id: rowId },
                success: function(){
                    $tr.remove();
                    Swal.fire('Gelöscht','Produkt erfolgreich gelöscht','success');
                },
                error:   function(){
                    Swal.fire('Fehler','Löschen fehlgeschlagen','error');
                }
            });
        });
    });

    // 3. SUBMIT NEW PRODUCTS
    $('#addProductForm').on('submit', function (e) {
        e.preventDefault();
        const rows = $('#modalNewRows tr');
        let ok = true;

        const payload = {
            _token: CSRF,
            inquiry_id: $('#modal_inquiry_id').val(),
            product_id: [],
            service_id: [],
            department_id: [],
            employee_id: [],
            field_employee: [],
            appointment_date: []
        };

        rows.each(function () {
            const idx = $(this).data('index');
            const p = $(`.product-select[data-index="${idx}"]`).val();
            const s = $(`.service-select[data-index="${idx}"]`).val();
            const d = $(`.department-select[data-index="${idx}"]`).val();
            const e = $(`.employee-select[data-index="${idx}"]`).val();
            const f = $(`.field-employee-select[data-index="${idx}"]`).val();
            const a = $(`input[name="appointment_date[]"][data-index="${idx}"]`).val();

            // If empty line, skip
            const empty = !p && !s && !d && !e && !f && !a;
            if (empty) { $(this).remove(); return; }

            // Validation: Product, Service, Dept, Inside Employee are required
            if (!p || !s || !d || !e) {
                $(this).addClass('table-danger');
                ok = false;
                return;
            }
            $(this).removeClass('table-danger');

            payload.product_id.push(p);
            payload.service_id.push(s);
            payload.department_id.push(d);
            payload.employee_id.push(e);
            payload.field_employee.push(f || null);
            payload.appointment_date.push(a || null);
        });

        if (!payload.product_id.length) {
             Swal.fire({ icon:'warning', title:'Hinweis', text:'Bitte fügen Sie mindestens ein neues Produkt hinzu, bevor Sie speichern.' });
             return;
        }

        if (!ok) {
            Swal.fire({ icon:'warning', title:'Fehler', text:'Alle Zeilen müssen vollständig sein (Produkt, Service, Abteilung, Innendienst) oder entfernt werden.' });
            return;
        }

        $.post(URL_SAVE, payload)
            .done(res => {
                Swal.fire({
                    icon:'success',
                    title:'Gespeichert',
                    text: res.message || 'Erfolgreich gespeichert.'
                }).then(()=> location.reload());
            })
            .fail(xhr => {
                const errs = xhr.responseJSON?.errors
                    ? Object.values(xhr.responseJSON.errors).flat().join('\n')
                    : (xhr.responseJSON?.message || 'Speichern fehlgeschlagen.');
                Swal.fire({ icon:'error', title:'Fehler', text: errs });
            });
    });

});
</script>
{{-- MENU JS (ROW ACTION DROPDOWN) --}}
<script>
(() => {
    const clamp = (v, min, max) => Math.max(min, Math.min(max, v));
    const parseOffset = s => {
        if (!s) return { x: 0, y: 8 };
        const [x = '0', y = '8'] = String(s).split(',');
        return { x: +x || 0, y: +y || 0 };
    };

    window.__menuGhostUntil = 0;
    let openCtx = null;

    function getParts(group) {
        if (!group) return {};
        const toggle = group.querySelector('[aria-controls]') || group.querySelector('.js-menu-toggle');
        let panel = null;
        if (toggle && toggle.getAttribute('aria-controls')) {
            panel = document.getElementById(toggle.getAttribute('aria-controls'));
        }
        if (!panel) panel = group.querySelector('.js-menu-panel');
        return { toggle, panel };
    }

    function portalOpen(group, panel) {
        const shouldPortal = String(group.dataset.menuPortal ?? 'true') !== 'false';
        if (!shouldPortal || !panel) return null;
        const parent = panel.parentElement;
        panel.classList.add('js-menu-portal');
        panel.__portalParent = parent;
        document.body.appendChild(panel);
        return parent;
    }

    function portalClose(panel) {
        if (panel?.__portalParent) {
            panel.__portalParent.appendChild(panel);
            delete panel.__portalParent;
        }
        panel?.classList.remove('js-menu-portal');
    }

    function place(panel, toggle, align, offset) {
        if (!panel || !toggle) return;

        const prevVis = panel.style.visibility;
        const prevDisp = panel.style.display;
        panel.style.visibility = 'hidden';
        panel.style.display = 'block';

        const r  = toggle.getBoundingClientRect();
        const pw = panel.offsetWidth;
        const ph = panel.offsetHeight;

        const vw = document.documentElement.clientWidth;
        const vh = document.documentElement.clientHeight;

        let left = align === 'end' ? r.right - pw : r.left;
        let top  = r.bottom;

        left += offset.x;
        top  += offset.y;

        left = clamp(left, 8, vw - pw - 8);
        top  = clamp(top,  8, vh - ph - 8);

        Object.assign(panel.style, {
            left: `${left}px`,
            top:  `${top}px`,
            visibility: prevVis || '',
            display:     prevDisp || 'block'
        });
    }

    function closeCurrent() {
        if (!openCtx) return;
        const { toggle, panel, onScroll, onResize } = openCtx;

        toggle?.setAttribute('aria-expanded', 'false');
        panel?.classList.remove('is-open');

        window.removeEventListener('scroll', onScroll, true);
        window.removeEventListener('resize', onResize);

        portalClose(panel);
        openCtx = null;

        window.__menuGhostUntil = Date.now() + 300;
    }

    function openGroup(group) {
        const { toggle, panel } = getParts(group);
        if (!toggle || !panel) return;

        const align  = (group.dataset.menuAlign || 'start').toLowerCase();
        const offset = parseOffset(group.dataset.menuOffset);

        if (openCtx && openCtx.group !== group) closeCurrent();

        portalOpen(group, panel);
        place(panel, toggle, align, offset);

        toggle.setAttribute('aria-expanded', 'true');
        panel.classList.add('is-open');

        const onScroll = () => place(panel, toggle, align, offset);
        const onResize = () => place(panel, toggle, align, offset);

        window.addEventListener('scroll', onScroll, true);
        window.addEventListener('resize', onResize);

        openCtx = { group, toggle, panel, align, offset, onScroll, onResize };
    }

    window.openMenuById = menuId => {
        const group = document.getElementById(menuId);
        if (group) openGroup(group);
    };

    window.closeMenuById = menuId => {
        const group = document.getElementById(menuId);
        if (group && openCtx && openCtx.group === group) closeCurrent();
    };

    document.addEventListener('click', function(e){
        if (Date.now() < window.__menuGhostUntil) {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    }, true);

    document.addEventListener('click', e => {
        const toggleBtn = e.target.closest('.js-menu [aria-controls]');
        if (toggleBtn) {
            e.preventDefault();
            e.stopPropagation();
            const group = toggleBtn.closest('.js-menu');
            if (openCtx && openCtx.group === group) closeCurrent();
            else openGroup(group);
            return;
        }

        if (openCtx?.panel?.contains(e.target)) {
            setTimeout(closeCurrent, 0);
            return;
        }

        if (openCtx) {
            const insideGroup = openCtx.group?.contains(e.target);
            const insidePanel = openCtx.panel?.contains(e.target);
            if (!insideGroup && !insidePanel) closeCurrent();
        }
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeCurrent();
    });

    window.addEventListener('focus', () => {
        if (openCtx) place(openCtx.panel, openCtx.toggle, openCtx.align, openCtx.offset);
    });
})();
</script>

<script>
function closeClosestMenuHelper(el){
    const group = el && el.closest ? el.closest('.js-menu') : null;
    if (group && typeof window.closeMenuById === 'function') {
        window.closeMenuById(group.id || group.dataset.menuId);
    }
}
</script>

{{-- SELECT EMPLOYEE OVER SWAL --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.select-employee').forEach(el => {
        el.addEventListener('click', async function () {
            const type = this.dataset.type;
            const id   = this.dataset.id;

            try {
                const res = await fetch(`/getAllEmployees`);
                const employees = await res.json();

                if (!employees.length) {
                    return Swal.fire(
                        'Keine Mitarbeiter',
                        'Für diese Abteilung wurden keine Mitarbeiter gefunden.',
                        'info'
                    );
                }

                let optionsHtml = `<select id="employeeSelect" class="form-control" style="width:100%">`;
                employees.forEach(emp => {
                    const imgSrc = emp.image
                        ? `/images/employee/${emp.image}`
                        : (emp.gender === 'male'
                            ? '/images/gender/male.png'
                            : '/images/gender/female.png');
                    optionsHtml += `<option value="${emp.emp_id}" data-img="${imgSrc}">${emp.name} ${emp.lastname}</option>`;
                });
                optionsHtml += `</select>`;

                const swal = await Swal.fire({
                    title: 'Mitarbeiter auswählen',
                    html: optionsHtml,
                    confirmButtonText: 'Aktualisieren',
                    cancelButtonText: 'Abbrechen',
                    showCancelButton: true,
                    focusConfirm: false,
                    didOpen: () => {
                        $('#employeeSelect').select2({
                            templateResult: formatOption,
                            templateSelection: formatOption,
                            dropdownParent: $('.swal2-container'),
                            width: '100%'
                        });

                        function formatOption(opt) {
                            if (!opt.id) return opt.text;
                            const img = $(opt.element).data('img');
                            return $(
                                `<span>
                                    <img src="${img}" style="width:26px;height:26px;border-radius:50%;margin-right:8px;vertical-align:middle;">
                                    ${opt.text}
                                </span>`
                            );
                        }
                    },
                    preConfirm: async () => {
                        const empId = $('#employeeSelect').val();
                        if (!empId) {
                            Swal.showValidationMessage('Bitte einen Mitarbeiter auswählen.');
                            return false;
                        }

                        try {
                            const response = await fetch(`/inquiry-products/${id}/update-employee`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token
                                },
                                body: JSON.stringify({ type, employee_id: empId })
                            });

                            const text = await response.text();
                            let result;
                            try {
                                result = JSON.parse(text);
                            } catch {
                                throw new Error('Ungültige Serverantwort.');
                            }

                            if (result.status !== 'success') {
                                throw new Error(result.message || 'Fehler');
                            }
                            return result;
                        } catch (e) {
                            Swal.showValidationMessage(`Fehler: ${e.message}`);
                            return false;
                        }
                    }
                });

                if (swal.isConfirmed) {
                    Swal.fire(
                        'Aktualisiert!',
                        'Der Mitarbeiter wurde erfolgreich geändert.',
                        'success'
                    ).then(() => location.reload());
                }

            } catch (err) {
                console.error(err);
                Swal.fire('Fehler', 'Die Mitarbeiterliste konnte nicht geladen werden.', 'error');
            }
        });
    });
});
</script>
 {{-- BULK SELECTION + DRAWER LOGIC --}}
{{-- Map: inquiry_id => [article_group, ...] --}}
<script>
    const INQUIRY_PRODUCTS = @json(
        $product_list
            ->groupBy('inquiry_id')
            ->map(function($rows){
                return $rows->pluck('article_group')->unique()->values();
            })
    );
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf          = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const headMaster    = document.getElementById('checkAllInquiriesHeader');
    const toolbarMaster = document.getElementById('checkAllInquiries');
    const selectedCount = document.getElementById('selectedCount');

    const btnBulkVerify = document.getElementById('btnBulkVerify');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const btnBulkJunk   = document.getElementById('btnBulkJunk');

    const drawer        = document.getElementById('verifyDrawer');
    const drawerOverlay = document.getElementById('verifyDrawerOverlay');
    const drawerClose   = document.getElementById('verifyDrawerClose');
    const drawerApply   = document.getElementById('verifyDrawerApply');
    const drawerList    = document.getElementById('verifyDrawerList');
    const drawerCount   = document.getElementById('verifyDrawerCount');
    const drawerType    = document.getElementById('verifyDrawerType');

 
    function rowCheckboxes() {
        return Array.from(
            document.querySelectorAll('input.inquiry-checkbox[name="selected_inquiries[]"]')
        );
    }

    function getSelectedRows() {
        return rowCheckboxes()
            .filter(cb => cb.checked)
            .map(cb => cb.closest('tr'))
            .filter(tr => !!tr);
    }

    function getSelectedIds() {
        return rowCheckboxes()
            .filter(cb => cb.checked)
            .map(cb => cb.value);
    }

    function openDrawer() {
        if (!drawer || !drawerOverlay) return;
        buildDrawerList();
        drawer.classList.add('is-open');
        drawerOverlay.classList.add('is-open');
    }

    function closeDrawer() {
        if (!drawer || !drawerOverlay) return;
        drawer.classList.remove('is-open');
        drawerOverlay.classList.remove('is-open');
    }

    function getProductsForInquiry(id) {
        if (!id) return [];
        const key = String(id);
        const arr = INQUIRY_PRODUCTS && INQUIRY_PRODUCTS[key]
            ? INQUIRY_PRODUCTS[key]
            : [];
        return Array.isArray(arr) ? arr : [];
    }

    function escapeHtml(str) {
        if (typeof str !== 'string') return str;
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function buildDrawerList() {
        if (!drawerList || !drawerCount) return;

        const rows = getSelectedRows();
        drawerList.innerHTML = '';
        drawerCount.textContent = rows.length;

        if (!rows.length) {
            drawerList.innerHTML =
                '<p class="text-muted small mb-0">Keine Anfragen ausgewählt.</p>';
            return;
        }

        rows.forEach(tr => {
            const checkbox   = tr.querySelector('input.inquiry-checkbox[name="selected_inquiries[]"]');
            const id         = tr.dataset.inquiryId || (checkbox ? checkbox.value : '');
            const name       = tr.dataset.inquiryName || '';
            const type       = tr.dataset.inquiryType || '';
            const city       = tr.dataset.inquiryCity || '';
            const status     = tr.dataset.status || '';

            const products   = getProductsForInquiry(id);
            const hasProductFlag = tr.dataset.hasProduct === '1';
            const hasProduct = hasProductFlag || products.length > 0;

            const isLead = type.toLowerCase() === 'lead';

            const wrapper = document.createElement('div');
            wrapper.className = 'verify-drawer-item';
            wrapper.dataset.inquiryId = id;

            const productsHtml = products.length
                ? `
                    <div class="mt-1">
                        <div class="verify-drawer-meta mb-25">
                            Artikelgruppen:
                        </div>
                        <div class="d-flex flex-wrap">
                            ${products.map(p => `
                                <span class="badge badge-light-primary mr-25 mb-25">
                                    ${escapeHtml(p)}
                                </span>
                            `).join('')}
                        </div>
                    </div>
                  `
                : '';

            wrapper.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="mr-1 w-100">
                        <div class="verify-drawer-name">#${id || '–'} ${escapeHtml(name || '')}</div>
                        <div class="verify-drawer-meta">
                            ${type ? 'Typ: ' + escapeHtml(type) : 'Typ: —'}
                            ${city ? ' · ' + escapeHtml(city) : ''}
                            ${status ? ' · Status: ' + escapeHtml(status) : ''}
                        </div>
                        <div class="mt-1">
                            <span class="verify-drawer-badge ${hasProduct ? 'verify-drawer-badge--ok' : 'verify-drawer-badge--warn'}">
                                ${hasProduct ? 'hat Produkte' : 'ohne Produkte'}
                            </span>
                            <span class="verify-drawer-badge ${isLead ? 'verify-drawer-badge--lead' : 'verify-drawer-badge--other'}">
                                ${isLead ? 'Lead' : (escapeHtml(type || 'Typ offen'))}
                            </span>
                        </div>
                        ${productsHtml}
                    </div>
                </div>
            `;
            drawerList.appendChild(wrapper);
        });
    }

    function syncBulkUI() {
        const rows         = rowCheckboxes();
        const selectedRows = getSelectedRows();
        const selected     = selectedRows.length;
        const allChecked   = rows.length > 0 && selected === rows.length;

        if (selectedCount)  selectedCount.textContent = selected;
        if (headMaster)     headMaster.checked       = allChecked;
        if (toolbarMaster)  toolbarMaster.checked    = allChecked;

        if (!selected && drawer && drawer.classList.contains('is-open')) {
            closeDrawer();
        }
    }

    function toggleAll(checked) {
        rowCheckboxes().forEach(cb => cb.checked = checked);
        syncBulkUI();
    }

    if (headMaster) {
        headMaster.addEventListener('change', function () {
            toggleAll(this.checked);
        });
    }

    if (toolbarMaster) {
        toolbarMaster.addEventListener('change', function () {
            toggleAll(this.checked);
        });
    }

    document.addEventListener('change', function (e) {
        if (e.target.matches && e.target.matches('input.inquiry-checkbox[name="selected_inquiries[]"]')) {
            syncBulkUI();
        }
    });

    // Bulk Verify → Drawer
    if (btnBulkVerify) {
        btnBulkVerify.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (!ids.length) {
                Swal.fire('Hinweis', 'Bitte zuerst Anfragen auswählen.', 'info');
                return;
            }
            openDrawer();
        });
    }

    if (drawerClose) {
        drawerClose.addEventListener('click', closeDrawer);
    }

    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', closeDrawer);
    }

  // Locate this ID in your provided code: verifyDrawerApply
    if (drawerApply) {
        drawerApply.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (!ids.length) {
                Swal.fire('Hinweis', 'Keine Anfragen ausgewählt.', 'info');
                return;
            }

            const type = drawerType && drawerType.value;
            if (!type) {
                Swal.fire('Hinweis', 'Bitte einen Zieltyp im Drawer wählen.', 'warning');
                return;
            }

            // Confirmation Dialog
            Swal.fire({
                title: 'Verifizieren?',
                html: `<p><strong>${ids.length}</strong> Anfrage(n) werden als <strong>${type}</strong> verifiziert.</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ja, verifizieren',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#7367f0',
                cancelButtonColor: '#d33',
            }).then(result => {
                if (!result.isConfirmed) return;

                // Show loading state
                Swal.fire({
                    title: 'Verarbeite...',
                    text: 'Bitte warten.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('inquiries.bulk.verify') }}',
                    method: 'POST',
                    data: {
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        type: type,
                        ids: ids
                    },
                    success: function (res) {
                        let icon = 'success';
                        let title = 'Abgeschlossen';
                        let html = '';

                        // 1. Success Message
                        if (res.processed_count > 0) {
                            html += `<div class="text-success mb-2">
                                        <i class="fa fa-check-circle"></i> ${res.processed_count} Anfrage(n) erfolgreich verifiziert.
                                    </div>`;
                        }

                        // 2. Skipped/Error Message (Detailed)
                        if (res.skipped_count > 0) {
                            // Change icon to warning if some were skipped
                            icon = res.processed_count > 0 ? 'warning' : 'error';
                            title = res.processed_count > 0 ? 'Teilweise abgeschlossen' : 'Fehlgeschlagen';

                            html += `<div class="text-left mt-2">
                                        <div class="font-weight-bold text-danger mb-1">
                                            ${res.skipped_count} Anfrage(n) übersprungen:
                                        </div>
                                        <ul class="pl-3 text-danger small" style="max-height: 150px; overflow-y: auto;">`;
                            
                            // Loop through specific reasons provided by controller
                            if(Array.isArray(res.skipped)) {
                                res.skipped.forEach(item => {
                                    html += `<li><strong>ID ${item.id}:</strong> ${item.reason}</li>`;
                                });
                            }
                            html += `</ul></div>`;
                        }

                        Swal.fire({
                            icon: icon,
                            title: title,
                            html: html,
                            confirmButtonText: 'OK',
                            width: 600
                        }).then(() => location.reload());
                    },
                    error: function (xhr) {
                        let text = 'Aktion fehlgeschlagen.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            text = xhr.responseJSON.message;
                        }
                        Swal.fire('Fehler', text, 'error');
                    }
                });
            });
        });
    }
    // Bulk Delete
    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (!ids.length) {
                Swal.fire('Hinweis', 'Bitte zuerst Anfragen auswählen.', 'info');
                return;
            }

            Swal.fire({
                title: 'Ausgewählte Anfragen löschen?',
                html: `<p><strong>${ids.length}</strong> Anfrage(n) werden in den Papierkorb verschoben.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen',
                cancelButtonText: 'Abbrechen'
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route('inquiries.bulk.delete') }}',
                    method: 'POST',
                    data: {
                        _token: csrf,
                        ids: ids
                    },
                    success: function (res) {
                        Swal.fire(
                            'Gelöscht',
                            (res.deleted || 0) + ' Anfrage(n) wurden gelöscht.',
                            'success'
                        ).then(() => location.reload());
                    },
                    error: function () {
                        Swal.fire('Fehler', 'Löschen fehlgeschlagen.', 'error');
                    }
                });
            });
        });
    }

    // Bulk Junk (optional)
    if (btnBulkJunk) {
        btnBulkJunk.addEventListener('click', function () {
            const ids = getSelectedIds();
            if (!ids.length) {
                Swal.fire('Hinweis', 'Bitte zuerst Anfragen auswählen.', 'info');
                return;
            }

            Swal.fire({
                title: 'Ausgewählte Anfragen als Junk markieren?',
                html: `<p><strong>${ids.length}</strong> Anfrage(n) werden als Junk markiert.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, Junk',
                cancelButtonText: 'Abbrechen'
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route('inquiries.bulk.junk') }}',
                    method: 'POST',
                    data: {
                        _token: csrf,
                        ids: ids
                    },
                    success: function (res) {
                        Swal.fire(
                            'Aktualisiert',
                            (res.junked || 0) + ' Anfrage(n) wurden als Junk markiert.',
                            'success'
                        ).then(() => location.reload());
                    },
                    error: function () {
                        Swal.fire('Fehler', 'Aktion fehlgeschlagen.', 'error');
                    }
                });
            });
        });
    }

    // Initial UI sync
    syncBulkUI();
});
</script>

@endsection
