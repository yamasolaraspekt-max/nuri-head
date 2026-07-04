@extends('admin.layouts.app')
@section('title', 'Anfragen')

@php
    use Illuminate\Pagination\AbstractPaginator;
    use Illuminate\Pagination\LengthAwarePaginator;

    $currentRoute = Route::currentRouteName();

    $pageTitle = match ($currentRoute) {
        'inquiry.view' => 'ANFRAGE AUFNAHME',
        'inquiry.customer' => 'ANFRAGEN',
        'my.inquiry.view' => 'MEINE ANFRAGEN',
        'inquiry.junk.list' => 'JUNK LISTE',
        'inquiry.deleted.list' => 'GELÖSCHTE ANFRAGEN',
        'inquiry.published.list' => 'VERÖFFENTLICHTE ANFRAGEN',
        default => 'ANFRAGEN',
    };

    $pageSub = match ($currentRoute) {
        'inquiry.junk.list' => 'Verwalten Sie Junk-Anfragen, Junk-Gründe und stellen Sie Einträge bei Bedarf wieder her.',
        'inquiry.deleted.list' => 'Gelöschte Anfragen ansehen und bei Bedarf wiederherstellen.',
        'inquiry.customer' => 'Verwalten Sie Ihre Kundenanfragen, Produkte, Zuständigkeiten und Status zentral.',
        'my.inquiry.view' => 'Ihre eigenen Anfragen mit Produkten, Zuständigkeiten und Status.',
        default => 'Verwalten Sie Anfragen, Produkte, Zuständigkeiten, Verifizierung und Junk-Gründe zentral.',
    };

    $fs = function ($v, string $fallback = 'Nicht angegeben') {
        $vv = is_string($v) ? trim($v) : $v;
        return (isset($vv) && $vv !== '' && $vv !== null) ? $vv : $fallback;
    };

    $fsName = function ($first, $last) use ($fs) {
        $full = trim(($first ?? '') . ' ' . ($last ?? ''));
        return $fs($full, 'Unbenannt');
    };

    $fsCity = fn($city) => $fs($city, 'Unbekannte Stadt');

    $toObject = function ($value) {
        if ($value instanceof \Illuminate\Database\Eloquent\Model) {
            return $value;
        }

        if (is_array($value)) {
            return (object) $value;
        }

        return $value;
    };

    $toArray = function ($value) {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Illuminate\Database\Eloquent\Model) {
            return $value->toArray();
        }

        if ($value instanceof \Illuminate\Support\Arrayable) {
            return $value->toArray();
        }

        if (is_object($value)) {
            return get_object_vars($value);
        }

        return [];
    };

    $fsType = function ($row) use ($fs) {
        $label = data_get($row, 'type_name') ?? data_get($row, 'pre_type');
        return $fs($label, 'Unbekannter Typ');
    };

    $isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;

    if ($isPaginator) {
        $dataRows = collect($data->items())->map($toObject);
    } elseif (is_array($data ?? null) && array_key_exists('data', $data)) {
        $dataRows = collect($data['data'])->map($toObject);
    } else {
        $dataRows = collect($data ?? [])->map($toObject);
    }

    $collection = $dataRows;

    $totalCount = $isPaginator
        ? $data->total()
        : ((is_array($data ?? null) && isset($data['total'])) ? (int) $data['total'] : $collection->count());

    $publishedCount = (int) $collection->filter(fn($item) => strtolower(trim((string) data_get($item, 'status', ''))) === 'published')->count();
    $junkCount = (int) $collection->filter(fn($item) => strtolower(trim((string) data_get($item, 'status', ''))) === 'junk')->count();
    $unpublishedCount = (int) $collection->filter(fn($item) => strtolower(trim((string) data_get($item, 'status', ''))) === 'unpublished')->count();
    $typedCount = (int) $collection->filter(fn($item) => !empty(data_get($item, 'type_name')) || !empty(data_get($item, 'pre_type')))->count();

    $highlightId = session('highlight_inquiry_id');

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
        'created_at',
        'name',
        'email',
        'status',
        'periority',
    ];

    $currentSort = request('sort', 'id');
    if (!in_array($currentSort, $allowedSorts, true)) {
        $currentSort = 'id';
    }

    $isJunkPage = $currentRoute === 'inquiry.junk.list';
    $isDeletedPage = $currentRoute === 'inquiry.deleted.list';

    $currentDirection = strtolower(request('direction', 'desc'));
    $currentDirection = in_array($currentDirection, ['asc', 'desc'], true) ? $currentDirection : 'desc';

    $sortUrl = function (string $column) use ($currentSort, $currentDirection, $allowedSorts) {
        if (!in_array($column, $allowedSorts, true)) {
            $column = 'id';
        }

        $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $direction,
            'page' => 1,
        ]);
    };

    $typeFilters = [
        'Lead' => 'Lead',
        'Lieferant' => 'Lieferant',
        'Hersteller' => 'Hersteller',
        'Geschäftspartner' => 'Geschäftspartner',
        'Architekt' => 'Architekt',
        'Nachunternehmer' => 'Nachunternehmer',
        'Bank' => 'Bank',
        'Versicherung' => 'Versicherung',
        'Bewerber' => 'Bewerber',
        'Kunde' => 'Kunde',
        'other' => 'Sonstiges',
    ];

    $servicesMap = [
        'complete' => 'Komplettlösung',
        'montage' => 'Montage',
        'product' => 'Produkt',
        'plan' => 'Planung',
        'maintenance' => 'Wartung',
        'repair' => 'Reparatur',
        'reclaim' => 'Reklamation',
        'emergency' => 'Notdienst',
        'others' => 'Sonstiges',
    ];

    $permissions = is_array($permissions ?? []) ? ($permissions ?? []) : (array) $permissions;

    $canUpdate = array_key_exists('canUpdate', $permissions)
        ? (bool) $permissions['canUpdate']
        : true;

    $canDelete = array_key_exists('canDelete', $permissions)
        ? (bool) $permissions['canDelete']
        : true;

    $canVerify = array_key_exists('canVerify', $permissions)
        ? (bool) $permissions['canVerify']
        : true;

    $product_list = collect($productList ?? [])->map($toObject);

    $normalizeProductRows = function ($rows) use ($toObject) {
        return collect($rows ?? [])->map($toObject)->values();
    };

    if (empty($productListGrouped ?? null) && $product_list->isNotEmpty()) {
        $productListGrouped = $product_list->groupBy(function ($row) {
            return data_get($row, 'inquiry_id') ?? data_get($row, 'customer_id') ?? data_get($row, 'lead_id');
        });
    } else {
        $productListGrouped = collect($productListGrouped ?? [])->map(function ($rows) use ($normalizeProductRows) {
            return $normalizeProductRows($rows);
        });
    }

    $existingLeadMatches = collect($existingLeadMatches ?? [])->map(function ($rows) use ($toArray) {
        return collect($rows ?? [])->map($toArray)->values();
    });
@endphp

@once
    @push('style')
        <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <style>
            :root {
                --app-bg: #f3f4f6;
                --card-bg: #ffffff;
                --text-main: #1f2937;
                --text-muted: #6b7280;
                --border: #e5e7eb;
                --primary: #93c21c;
                --primary-hover: #7baa18;
                --primary-light: #f4fae7;
                --blue: #74b2d4;
                --blue-light: #eff6ff;
                --success: #10b981;
                --success-light: #ecfdf5;
                --warning: #f59e0b;
                --warning-light: #fffbeb;
                --danger: #ef4444;
                --danger-hover: #dc2626;
                --danger-light: #fef2f2;
                --gray: #6b7280;
                --gray-light: #f3f4f6;
                --purple: #7c3aed;
                --purple-light: #f5f3ff;
                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
                --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
                --radius: 14px;
                --transition: all .2s ease-in-out;
            }

            .oc-wrap {
                font-family: Inter, system-ui, -apple-system, sans-serif;
                color: var(--text-main);
            }

            .oc-header {
                margin: 18px;
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
                font-weight: 800;
                letter-spacing: -.025em;
                color: #111827
            }

            .oc-sub {
                font-size: 14px;
                color: var(--text-muted);
                margin-top: 4px
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
                font-weight: 700;
            }

            .oc-breadcrumb a:hover {
                color: var(--text-main)
            }

            .oc-breadcrumb span.current {
                color: #111827;
                font-weight: 800
            }

            .oc-btn,
            .oc-btn-soft,
            .oc-btn-ic {
                transition: var(--transition);
                text-decoration: none;
            }

            .oc-btn {
                background: var(--primary);
                color: #fff;
                border: none;
                padding: 10px 16px;
                border-radius: 10px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .oc-btn:hover {
                background: var(--primary-hover);
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
            }

            .oc-btn-soft:hover {
                background: #f9fafb;
                color: var(--text-main);
                text-decoration: none;
            }

            .oc-btn-ic {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                border: 1px solid var(--border);
                background: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: var(--text-muted);
                cursor: pointer;
            }

            .oc-btn-ic:hover {
                background: #f9fafb;
                color: var(--text-main);
                border-color: #d1d5db;
                text-decoration: none;
            }

            .oc-btn-ic.primary {
                color: var(--primary);
                border-color: var(--primary-light);
                background: var(--primary-light);
            }

            .oc-btn-ic.primary:hover {
                border-color: var(--primary)
            }

            .oc-btn-ic.warning {
                color: #d97706;
                border-color: #fde7b0;
                background: #fffbeb;
            }

            .oc-btn-ic.warning:hover {
                border-color: #f59e0b
            }

            .oc-btn-ic.success {
                color: var(--success);
                border-color: #c7f2df;
                background: var(--success-light);
            }

            .oc-btn-ic.success:hover {
                border-color: var(--success)
            }

            .oc-btn-ic.danger {
                color: var(--danger);
                border-color: rgba(239, 68, 68, .18);
                background: var(--danger-light);
            }

            .oc-btn-ic.danger:hover {
                border-color: rgba(239, 68, 68, .35)
            }

            .oc-btn-ic.purple {
                color: var(--purple);
                border-color: #e9d5ff;
                background: var(--purple-light);
            }

            .oc-btn-ic.purple:hover {
                border-color: var(--purple)
            }

            .oc-analytics {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 18px;
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
                color: var(--blue)
            }

            .oc-stat-icon.published {
                background: var(--success-light);
                color: var(--success)
            }

            .oc-stat-icon.unpublished {
                background: var(--warning-light);
                color: #d97706
            }

            .oc-stat-icon.type {
                background: var(--gray-light);
                color: var(--gray)
            }

            .oc-stat-icon.junk {
                background: var(--danger-light);
                color: var(--danger)
            }

            .oc-stat-meta {
                min-width: 0
            }

            .oc-stat-label {
                font-size: 11px;
                font-weight: 800;
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

            .oc-toolbar {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 14px 16px;
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                align-items: flex-end;
                justify-content: space-between;
                margin-bottom: 16px;
                box-shadow: var(--shadow-sm);
            }

            .oc-toolbar-left,
            .oc-toolbar-right {
                display: flex;
                align-items: flex-end;
                gap: 12px;
                flex-wrap: wrap;
            }

            .oc-toolbar-left {
                flex: 1
            }

            .oc-filter-block {
                display: flex;
                flex-direction: column;
                gap: 6px;
                min-width: 170px;
            }

            .oc-filter-block.search {
                flex: 1 1 320px;
                min-width: 320px;
            }

            .oc-filter-block.wide {
                min-width: 230px;
            }

            .oc-filter-label {
                font-size: 11px;
                font-weight: 800;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .oc-input,
            .oc-select {
                background: #f9fafb;
                border: 1px solid var(--border);
                border-radius: 8px;
                padding: 10px 12px;
                font-size: 14px;
                outline: none;
                transition: var(--transition);
                min-width: 180px;
                width: 100%;
            }

            .oc-input.search {
                padding-left: 36px;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: 10px center;
                background-size: 16px;
            }

            .oc-input:focus,
            .oc-select:focus {
                background: #fff;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px var(--primary-light);
            }

            .oc-bulkbar {
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 12px 16px;
                margin-bottom: 16px;
                box-shadow: var(--shadow-sm);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .oc-chip {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                background: #eef2ff;
                color: var(--blue);
                padding: 6px 12px;
                font-weight: 800;
                font-size: 12px;
            }

            .oc-card {
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 16px;
                box-shadow: var(--shadow-sm);
                overflow: visible;
            }

            .oc-list-head {
                display: grid;
                grid-template-columns: 52px 88px minmax(250px, 1.4fr) minmax(160px, .9fr) minmax(220px, 1fr) minmax(140px, .8fr) 230px;
                gap: 14px;
                align-items: center;
                padding: 16px 16px 10px;
                color: var(--text-muted);
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .oc-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                padding: 0 0 16px;
            }

            .oc-item {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                transition: var(--transition);
                overflow: visible;
                margin: 0 16px;
                position: relative;
                z-index: 1;
            }

            .oc-item:hover {
                border-color: var(--primary);
                box-shadow: var(--shadow);
                z-index: 5;
            }

            .oc-item.highlight {
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .2);
                border-color: var(--primary);
            }

            .oc-item-row {
                padding: 16px;
                display: grid;
                gap: 16px;
                align-items: start;
                grid-template-columns: 52px 88px minmax(250px, 1.4fr) minmax(160px, .9fr) minmax(220px, 1fr) minmax(140px, .8fr) 230px;
            }

            .oc-cell {
                min-width: 0
            }

            .oc-cell-title {
                font-size: 11px;
                font-weight: 800;
                color: var(--text-muted);
                text-transform: uppercase;
                margin-bottom: 4px;
                display: none;
            }

            .oc-id-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 54px;
                height: 36px;
                padding: 0 12px;
                border-radius: 10px;
                background: var(--blue-light);
                color: var(--blue);
                font-size: 13px;
                font-weight: 900;
            }

            .oc-checkbox {
                margin-top: 10px
            }

            .oc-avatar {
                width: 52px;
                height: 52px;
                border-radius: 14px;
                background: #eff6ff;
                color: var(--blue);
                border: 1px solid #dbeafe;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 900;
                font-size: 18px;
            }

            .oc-main {
                display: flex;
                flex-direction: column;
                min-width: 0;
            }

            .oc-ttl {
                font-weight: 800;
                font-size: 15px;
                margin-bottom: 4px;
                color: #111827;
            }

            .oc-subt,
            .oc-subt-wrap {
                font-size: 13px;
                color: var(--text-muted);
                line-height: 1.45;
            }

            .oc-subt-wrap {
                white-space: normal
            }

            .oc-mini {
                font-size: 12px;
                color: var(--text-muted);
            }

            .oc-note {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                background: #f9fafb;
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 6px 8px;
                margin-top: 8px;
            }

            .oc-badges {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }

            .oc-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 900;
                white-space: nowrap;
            }

            .oc-badge.gray {
                background: #f3f4f6;
                color: #4b5563
            }

            .oc-badge.green {
                background: #ecfdf5;
                color: #047857
            }

            .oc-badge.orange {
                background: #fffbeb;
                color: #b45309
            }

            .oc-badge.red {
                background: #fef2f2;
                color: #b91c1c
            }

            .oc-badge.blue {
                background: #eff6ff;
                color: var(--blue)
            }

            .oc-badge.purple {
                background: #f5f3ff;
                color: #7c3aed
            }

            .oc-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                flex-wrap: wrap;
                position: relative;
                z-index: 30;
            }

            .oc-row-menu {
                position: relative;
                z-index: 50;
            }

            .oc-row-dropdown {
                position: absolute;
                right: 0;
                top: 42px;
                min-width: 220px;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
                padding: 8px;
                display: none;
                z-index: 9999;
            }

            .oc-row-dropdown.open {
                display: block
            }

            .oc-row-dropdown-item {
                width: 100%;
                border: none;
                background: transparent;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                border-radius: 10px;
                color: #374151;
                text-decoration: none;
                font-size: 13px;
                font-weight: 700;
                text-align: left;
                cursor: pointer;
            }

            .oc-row-dropdown-item:hover {
                background: #f8fafc;
                color: #111827;
                text-decoration: none;
            }

            .oc-row-dropdown-item i {
                width: 16px;
                text-align: center;
            }

            .oc-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1200;
                background: rgba(17, 24, 39, .55);
                backdrop-filter: blur(3px);
                opacity: 0;
                pointer-events: none;
                transition: opacity .22s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 18px;
            }

            .oc-modal-backdrop.open {
                opacity: 1;
                pointer-events: auto;
            }

            .oc-modal {
                width: 100%;
                max-width: 760px;
                background: #fff;
                border: 1px solid rgba(229, 231, 235, .9);
                border-radius: 16px;
                box-shadow: var(--shadow);
                transform: translateY(12px) scale(.985);
                transition: transform .22s ease;
                overflow: hidden;
            }

            .oc-modal.oc-modal-md {
                max-width: 620px
            }

            .oc-modal.oc-modal-lg {
                max-width: 980px
            }

            .oc-modal-backdrop.open .oc-modal {
                transform: translateY(0) scale(1)
            }

            .oc-modal-h {
                display: flex;
                gap: 12px;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                border-bottom: 1px solid var(--border);
                background: #fafafa;
            }

            .oc-modal-ttl {
                font-weight: 900;
                font-size: 16px;
                line-height: 1.2;
                margin: 0;
                color: #111827;
            }

            .oc-modal-b {
                padding: 20px 18px;
                max-height: 72vh;
                overflow-y: auto;
            }

            .oc-modal-f {
                padding: 14px 18px;
                border-top: 1px solid var(--border);
                background: #fafafa;
                display: flex;
                gap: 10px;
                justify-content: flex-end;
                flex-wrap: wrap;
            }

            .oc-empty {
                text-align: center;
                padding: 60px;
                color: var(--text-muted);
                background: #fff;
                border: 1px dashed var(--border);
                border-radius: 16px;
                margin: 16px;
            }

            .oc-pagination {
                margin-top: 18px;
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 14px 16px;
                box-shadow: var(--shadow-sm);
            }

            .oc-pagination-links .pagination {
                margin: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .oc-pagination-links .page-item .page-link {
                border-radius: 10px !important;
                border: 1px solid var(--border);
                color: var(--text-main);
                padding: 8px 12px;
                line-height: 1.1;
                box-shadow: none !important;
            }

            .oc-pagination-links .page-item.active .page-link {
                background: var(--primary);
                border-color: var(--primary);
                color: #fff;
            }

            .oc-pagination-links .page-item.disabled .page-link {
                color: #9ca3af;
                background: #f9fafb;
            }

            .oc-pagination-links .page-link:hover {
                background: #f9fafb;
                color: var(--text-main);
            }

            .oc-toast-wrap {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
                pointer-events: none;
            }

            .oc-toast {
                pointer-events: auto;
                min-width: 280px;
                max-width: 360px;
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 14px;
                box-shadow: var(--shadow);
                padding: 12px;
                display: flex;
                gap: 10px;
                align-items: flex-start;
                animation: ocToastIn .3s cubic-bezier(.175, .885, .32, 1.275) forwards;
            }

            @keyframes ocToastIn {
                from {
                    transform: translateX(100%);
                    opacity: 0
                }

                to {
                    transform: translateX(0);
                    opacity: 1
                }
            }

            .oc-toast-ic {
                width: 34px;
                height: 34px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .oc-toast-ic.ok {
                background: var(--success-light);
                color: var(--success)
            }

            .oc-toast-ic.bad {
                background: var(--danger-light);
                color: var(--danger)
            }

            .oc-toast-ttl {
                font-weight: 900;
                font-size: 13px;
                margin: 0;
                color: #111827;
            }

            .oc-toast-msg {
                font-size: 12px;
                color: #374151;
                margin: 4px 0 0;
                line-height: 1.4;
            }

            .oc-toast-x {
                margin-left: auto;
                background: transparent;
                border: none;
                cursor: pointer;
                color: var(--text-muted);
            }

            .verify-drawer-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .35);
                opacity: 0;
                visibility: hidden;
                transition: opacity .2s ease, visibility .2s ease;
                z-index: 1300;
            }

            .verify-drawer-overlay.is-open {
                opacity: 1;
                visibility: visible;
            }

            .verify-drawer {
                position: fixed;
                top: 0;
                right: -420px;
                width: min(420px, 100%);
                height: 100%;
                background: #ffffff;
                box-shadow: -12px 0 30px rgba(15, 23, 42, .25);
                z-index: 1301;
                display: flex;
                flex-direction: column;
                transition: right .2s ease;
            }

            .verify-drawer.is-open {
                right: 0
            }

            .verify-drawer-header {
                padding: .9rem 1.1rem;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: .5rem;
            }

            .verify-drawer-title {
                font-size: .9rem;
                font-weight: 600;
                color: #111827;
            }

            .verify-drawer-count {
                font-size: .75rem;
                color: #6b7280;
            }

            .verify-drawer-body {
                padding: .75rem 1.1rem 1rem;
                overflow-y: auto;
                flex: 1;
            }

            .verify-drawer-footer {
                padding: .75rem 1.1rem 1rem;
                border-top: 1px solid #e5e7eb;
                background: #f9fafb;
            }

            .verify-drawer-item {
                border-radius: .75rem;
                border: 1px solid #e5e7eb;
                padding: .6rem .7rem;
                margin-bottom: .5rem;
                background: #ffffff;
            }

            .verify-drawer-badge {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: .12rem .55rem;
                font-size: .7rem;
                font-weight: 600;
                margin-right: .25rem;
            }

            .verify-drawer-badge--ok {
                background: #ecfdf3;
                color: #166534
            }

            .verify-drawer-badge--warn {
                background: #fef3c7;
                color: #92400e
            }

            .verify-drawer-badge--lead {
                background: #eff6ff;
                color: var(--blue)
            }

            .verify-drawer-badge--other {
                background: #e5e7eb;
                color: #374151
            }

            .verify-drawer-name {
                font-size: .9rem;
                font-weight: 600;
                color: #111827;
            }

            .verify-drawer-meta {
                font-size: .75rem;
                color: #6b7280;
            }

            .swal2-popup {
                font-size: 16px !important;
                width: 600px !important;
                max-width: 92vw;
            }

            .swal2-html-container {
                text-align: left !important
            }

            .swal2-select {
                width: 100% !important;
                padding: 10px;
                font-size: 15px;
                border-radius: 6px;
                border: 1px solid #ced4da;
                margin-top: 8px;
            }

            .select2-container--default .select2-results__option img,
            .select2-selection__rendered img {
                width: 26px;
                height: 26px;
                border-radius: 50%;
                margin-right: 8px;
                vertical-align: middle;
            }

            .oc-flow-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-top: 2px;
            }

            .oc-flow-card {
                display: flex;
                flex-direction: column;
                gap: 4px;
                padding: 6px 0;
                border-bottom: 1px dashed #e5e7eb;
            }

            .oc-flow-card:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            .oc-flow-top {
                display: flex;
                align-items: center;
                flex-wrap: nowrap;
                gap: 0;
            }

            .oc-flow-badge,
            .oc-flow-avatar {
                width: 42px;
                height: 42px;
                min-width: 42px;
                border-radius: 999px;
                position: relative;
                z-index: 2;
            }

            .oc-flow-badge {
                background: #93c21c;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                font-size: 15px;
                font-weight: 800;
                letter-spacing: .01em;
                border: 3px solid #93c21c;
                box-shadow: 0 1px 4px rgba(147, 194, 28, .16);
            }

            .oc-flow-line {
                width: 10px;
                height: 4px;
                background: #93c21c;
                border-radius: 999px;
                margin: 0 -1px;
                position: relative;
                z-index: 1;
            }

            .oc-flow-avatar {
                object-fit: cover;
                background: #fff;
                border: 3px solid #93c21c;
                box-shadow: 0 1px 4px rgba(15, 23, 42, .08);
                cursor: pointer;
            }

            .oc-flow-avatar.outside {
                border-color: #f0a356
            }

            .oc-flow-meta {
                margin-top: 2px;
                padding-left: 1px;
            }

            .oc-flow-service {
                font-size: 11px;
                line-height: 1.15;
                font-weight: 800;
                color: #5b5b5b;
                margin-top: 2px;
            }

            .oc-flow-department {
                font-size: 10px;
                line-height: 1.15;
                font-weight: 700;
                color: #aeb8c6;
                margin-top: 2px;
            }

            .oc-junk-box {
                margin-top: 6px;
                border-radius: 10px;
                background: #fff7ed;
                border: 1px solid #fed7aa;
                padding: 7px 9px;
            }

            .oc-junk-title {
                font-size: 10px;
                font-weight: 900;
                color: #c2410c;
                text-transform: uppercase;
                letter-spacing: .03em;
                margin-bottom: 4px;
            }

            .oc-junk-text {
                font-size: 11px;
                color: #7c2d12;
                line-height: 1.35;
            }

            .oc-flow-empty {
                font-size: 11px;
                color: #94a3b8;
                font-weight: 700;
            }

            @media (max-width:1280px) {
                .oc-list-head {
                    display: none
                }

                .oc-item-row {
                    grid-template-columns: 1fr
                }

                .oc-cell-title {
                    display: block
                }
            }

            @media (max-width:1200px) {
                .oc-analytics {
                    grid-template-columns: repeat(2, minmax(0, 1fr))
                }
            }

            @media (max-width:980px) {

                .oc-toolbar-left,
                .oc-toolbar-right {
                    width: 100%;
                }

                .oc-filter-block,
                .oc-filter-block.search,
                .oc-filter-block.wide {
                    min-width: 100%;
                    flex: 1 1 100%;
                }
            }

            @media (max-width:700px) {
                .oc-analytics {
                    grid-template-columns: 1fr
                }
            }

            @media (max-width:640px) {

                .oc-flow-badge,
                .oc-flow-avatar {
                    width: 36px;
                    height: 36px;
                    min-width: 36px;
                }

                .oc-flow-badge {
                    font-size: 13px
                }

                .oc-flow-line {
                    width: 8px;
                    height: 3px;
                }

                .oc-flow-service {
                    font-size: 10px
                }

                .oc-flow-department {
                    font-size: 9px
                }
            }

            @media (max-width:576px) {
                .verify-drawer {
                    width: 100%
                }

                .verify-drawer-header {
                    flex-wrap: wrap;
                    align-items: flex-start;
                }
            }

            .oc-existing-warning {
                margin: 0 16px 14px;
                border: 1px solid #fed7aa;
                background: linear-gradient(135deg, #fff7ed, #fffbeb);
                border-radius: 14px;
                padding: 14px;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
                box-shadow: 0 8px 18px rgba(245, 158, 11, .12);
            }

            .oc-existing-warning-left {
                display: flex;
                gap: 12px;
                align-items: flex-start;
                min-width: 0;
            }

            .oc-existing-warning-icon {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                background: #ffedd5;
                color: #c2410c;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .oc-existing-warning-title {
                font-size: 14px;
                font-weight: 900;
                color: #9a3412;
                margin-bottom: 4px;
            }

            .oc-existing-warning-text {
                font-size: 12px;
                color: #7c2d12;
                line-height: 1.45;
            }

            .oc-existing-warning-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 8px;
            }

            .oc-existing-warning-tag {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: 4px 8px;
                background: #fff;
                border: 1px solid #fed7aa;
                color: #9a3412;
                font-size: 11px;
                font-weight: 800;
            }

            .oc-existing-warning-actions {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 8px;
                flex: 0 0 auto;
            }

            .oc-warning-action {
                border: none;
                border-radius: 10px;
                padding: 8px 10px;
                font-size: 12px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-decoration: none;
            }

            .oc-warning-action.view {
                background: #eff6ff;
                color: #2563eb;
            }

            .oc-warning-action.junk {
                background: #fff7ed;
                color: #c2410c;
                border: 1px solid #fed7aa;
            }

            .oc-warning-action.delete {
                background: #fef2f2;
                color: #dc2626;
                border: 1px solid #fecaca;
            }

            .oc-warning-action:hover {
                text-decoration: none;
                filter: brightness(.98);
            }

            @media (max-width:900px) {
                .oc-existing-warning {
                    flex-direction: column;
                }

                .oc-existing-warning-actions {
                    justify-content: flex-start;
                }
            }

            .custom-product-modal {
                position: fixed;
                inset: 0;
                z-index: 2500;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 24px;
            }

            .custom-product-modal.is-open {
                display: flex;
            }

            .custom-product-modal__overlay {
                position: absolute;
                inset: 0;
                background: rgba(15, 23, 42, .58);
                backdrop-filter: blur(4px);
            }

            .custom-product-modal__panel {
                position: relative;
                z-index: 2;
                width: min(1280px, 96vw);
                max-height: 92vh;
                background: #ffffff;
                border-radius: 22px;
                box-shadow: 0 30px 80px rgba(15, 23, 42, .35);
                border: 1px solid #e5e7eb;
                overflow: hidden;
                animation: productModalIn .18s ease-out;
            }

            @keyframes productModalIn {
                from {
                    transform: translateY(18px) scale(.98);
                    opacity: 0;
                }

                to {
                    transform: translateY(0) scale(1);
                    opacity: 1;
                }
            }

            .custom-product-modal__header {
                padding: 18px 22px;
                background: linear-gradient(135deg, #93c21c, #74b2d4);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
            }

            .custom-product-modal__kicker {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .08em;
                opacity: .9;
            }

            .custom-product-modal__title {
                margin: 3px 0 0;
                font-size: 20px;
                font-weight: 900;
                color: #ffffff;
            }

            .custom-product-modal__close {
                width: 40px;
                height: 40px;
                border: none;
                border-radius: 12px;
                background: rgba(255, 255, 255, .18);
                color: #ffffff;
                font-size: 28px;
                line-height: 1;
                cursor: pointer;
                transition: all .18s ease;
            }

            .custom-product-modal__close:hover {
                background: rgba(255, 255, 255, .28);
            }

            .custom-product-modal__body {
                padding: 20px 22px;
                max-height: calc(92vh - 150px);
                overflow-y: auto;
                background: #f9fafb;
            }

            .custom-product-modal__table-wrap {
                width: 100%;
                overflow-x: auto;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
            }

            .custom-product-table {
                width: 100%;
                min-width: 1180px;
                border-collapse: collapse;
                background: #ffffff;
            }

            .custom-product-table thead th {
                background: #f3f4f6;
                color: #374151;
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                padding: 13px 12px;
                border-bottom: 1px solid #e5e7eb;
                white-space: nowrap;
            }

            .custom-product-table tbody td {
                padding: 12px;
                border-bottom: 1px solid #eef2f7;
                vertical-align: middle;
            }

            .custom-product-table tbody tr:last-child td {
                border-bottom: none;
            }

            .custom-product-table tbody tr.table-danger td {
                background: #fef2f2;
            }

            .custom-product-field,
            .custom-product-table .form-select,
            .custom-product-table .form-control {
                width: 100%;
                min-height: 42px;
                border: 1px solid #d1d5db;
                border-radius: 10px;
                background: #ffffff;
                padding: 9px 11px;
                font-size: 13px;
                color: #111827;
                outline: none;
            }

            .custom-product-field:focus,
            .custom-product-table .form-select:focus,
            .custom-product-table .form-control:focus {
                border-color: #93c21c;
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .14);
            }

            .custom-product-add-btn {
                margin-top: 14px;
                border: none;
                border-radius: 12px;
                background: #ecfdf5;
                color: #047857;
                font-weight: 900;
                padding: 10px 14px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
            }

            .custom-product-add-btn:hover {
                background: #d1fae5;
            }

            .custom-product-remove-btn {
                width: 38px;
                height: 38px;
                border: 1px solid #fecaca;
                border-radius: 10px;
                background: #fef2f2;
                color: #dc2626;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .custom-product-remove-btn:hover {
                background: #fee2e2;
            }

            .custom-product-modal__footer {
                padding: 15px 22px;
                background: #ffffff;
                border-top: 1px solid #e5e7eb;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
            }

            .custom-product-cancel-btn,
            .custom-product-save-btn {
                border: none;
                border-radius: 12px;
                padding: 11px 16px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .custom-product-cancel-btn {
                background: #f3f4f6;
                color: #374151;
            }

            .custom-product-cancel-btn:hover {
                background: #e5e7eb;
            }

            .custom-product-save-btn {
                background: #93c21c;
                color: #ffffff;
            }

            .custom-product-save-btn:hover {
                background: #7baa18;
            }

            .custom-product-delete-existing {
                width: 36px;
                height: 36px;
                border: none;
                border-radius: 10px;
                background: #ef4444;
                color: #ffffff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .custom-product-delete-existing:hover {
                background: #dc2626;
            }

            body.custom-product-modal-open {
                overflow: hidden;
            }

            .custom-product-modal .select2-container {
                z-index: 2600;
            }

            .select2-container--open {
                z-index: 2700 !important;
            }

            @media (max-width: 768px) {
                .custom-product-modal {
                    padding: 10px;
                }

                .custom-product-modal__panel {
                    width: 100%;
                    max-height: 96vh;
                    border-radius: 18px;
                }

                .custom-product-modal__body {
                    max-height: calc(96vh - 150px);
                    padding: 14px;
                }

                .custom-product-modal__header,
                .custom-product-modal__footer {
                    padding: 14px;
                }

                .custom-product-modal__footer {
                    flex-direction: column-reverse;
                    align-items: stretch;
                }

                .custom-product-cancel-btn,
                .custom-product-save-btn {
                    justify-content: center;
                    width: 100%;
                }
            }
        </style>


    @endpush
@endonce


@section('content')
    @include('admin.inquiry._tabs')
    <div class="oc-wrap">
        <div class="oc-header">
            <div class="oc-titlebar">
                <div>
                    <div class="oc-title">{{ $pageTitle }}</div>
                    <div class="oc-sub">{{ $pageSub }}</div>

                    <div class="oc-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Home</a>
                        <span>›</span>
                        <span class="current">{{ $pageTitle }}</span>
                    </div>
                </div>

                <div class="oc-inline-actions d-flex align-items-center" style="gap:10px;">
                    @if(!$isDeletedPage)
                        <a href="{{ url('inquiry_create') }}" class="oc-btn">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"></path>
                            </svg>
                            Neue Anfrage
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="oc-analytics">
            <div class="oc-stat">
                <div class="oc-stat-icon total">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Gesamt</div>
                    <div class="oc-stat-value">{{ $totalCount }}</div>
                    <div class="oc-stat-sub">Einträge insgesamt</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon published">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Veröffentlicht</div>
                    <div class="oc-stat-value">{{ $publishedCount }}</div>
                    <div class="oc-stat-sub">Aktive Datensätze</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon unpublished">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Unveröffentlicht</div>
                    <div class="oc-stat-value">{{ $unpublishedCount }}</div>
                    <div class="oc-stat-sub">Noch nicht aktiv</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon type">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M7 12h10M10 17h4" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Mit Typ</div>
                    <div class="oc-stat-value">{{ $typedCount }}</div>
                    <div class="oc-stat-sub">Kategorisierte Einträge</div>
                </div>
            </div>

            <div class="oc-stat">
                <div class="oc-stat-icon junk">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6" />
                    </svg>
                </div>
                <div class="oc-stat-meta">
                    <div class="oc-stat-label">Junk</div>
                    <div class="oc-stat-value">{{ $junkCount }}</div>
                    <div class="oc-stat-sub">Als Junk markiert</div>
                </div>
            </div>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="oc-toolbar">
            <div class="oc-toolbar-left">
                <div class="oc-filter-block search">
                    <label class="oc-filter-label">Suche</label>
                    <input type="text" class="oc-input search"
                        placeholder="Suche nach Name, Firma, Adresse, Produkt, Typ, Status, Notiz, Junk-Grund" name="search"
                        value="{{ request('search') }}">
                </div>

                <div class="oc-filter-block wide">
                    <label class="oc-filter-label">Mitarbeiter</label>
                    <select class="oc-select js-select2-filter" name="employee_id">
                        <option value="">Alle Mitarbeiter</option>
                        @foreach(($employees ?? collect()) as $emp)
                            <option value="{{ $emp->id }}" {{ (string) request('employee_id') === (string) $emp->id ? 'selected' : '' }}>
                                {{ trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block wide">
                    <label class="oc-filter-label">Zuständig</label>
                    <select class="oc-select js-select2-filter" name="direct_to">
                        <option value="">Alle Zuständigen</option>
                        @foreach(($employees ?? collect()) as $emp)
                            <option value="{{ $emp->id }}" {{ (string) request('direct_to') === (string) $emp->id ? 'selected' : '' }}>
                                {{ trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Typ</label>
                    <select class="oc-select" name="type_filter">
                        <option value="">Alle Typen</option>
                        @foreach($typeFilters as $value => $label)
                            <option value="{{ $value }}" {{ request('type_filter') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Status</label>
                    <select class="oc-select" name="status_filter">
                        <option value="">Alle Status</option>
                        <option value="Unpublished" {{ request('status_filter') === 'Unpublished' ? 'selected' : '' }}>Nicht
                            verifiziert</option>
                        <option value="Published" {{ request('status_filter') === 'Published' ? 'selected' : '' }}>
                            Veröffentlicht</option>
                        <option value="Junk" {{ request('status_filter') === 'Junk' ? 'selected' : '' }}>Junk</option>
                        <option value="Draft" {{ request('status_filter') === 'Draft' ? 'selected' : '' }}>Entwurf</option>
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Niederlassung</label>
                    <select class="oc-select" name="branch_filter">
                        <option value="">Alle Niederlassungen</option>
                        @foreach(($branches ?? collect()) as $branch)
                            <option value="{{ $branch->id }}" {{ (string) request('branch_filter') === (string) $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="oc-filter-block">
                    <label class="oc-filter-label">Kundenabgleich</label>
                    <select class="oc-select" name="already_customer">
                        <option value="">Alle</option>
                        <option value="1" {{ request('already_customer') === '1' ? 'selected' : '' }}>
                            Bereits in Kunden/Leads
                        </option>
                    </select>
                </div>


            </div>

            <div class="oc-toolbar-right">
                <input type="hidden" name="sort" value="{{ $currentSort }}">
                <input type="hidden" name="direction" value="{{ $currentDirection }}">

                <button class="oc-btn-soft" type="submit">
                    <i class="fa fa-search mr-50"></i> Filtern
                </button>

                @if(
                        request('search') ||
                        request('employee_id') ||
                        request('direct_to') ||
                        request('type_filter') ||
                        request('status_filter') ||
                        request('branch_filter') ||
                        request('already_customer') ||
                        request('sort') ||
                        request('direction')
                    )
                    <a href="{{ url()->current() }}" class="oc-btn-soft">
                        <i class="fa fa-refresh mr-50"></i> Zurücksetzen
                    </a>
                @endif
            </div>
        </form>

        @if(!$isDeletedPage)
            <div class="oc-bulkbar">
                <div class="d-flex align-items-center flex-wrap" style="gap:12px;">
                    <label class="mb-0 d-flex align-items-center" style="gap:8px;">
                        <input type="checkbox" id="checkAllInquiries">
                        <span>Alle auswählen</span>
                    </label>

                    <span class="oc-chip">
                        Ausgewählt: <span id="selectedCount" style="margin-left:6px;">0</span>
                    </span>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                    @if($isJunkPage)
                        <button type="button" class="oc-btn-soft" id="btnBulkUnjunk">
                            <i class="fa fa-undo mr-50"></i> Aus Junk entfernen
                        </button>
                        <button type="button" class="oc-btn-soft" id="btnBulkDelete">
                            <i class="feather icon-trash-2 mr-50"></i> Löschen
                        </button>
                    @else
                        <button type="button" class="oc-btn-soft" id="btnBulkVerify">
                            <i class="fa fa-check-circle mr-50"></i> Verifizieren
                        </button>
                        <button type="button" class="oc-btn-soft" id="btnBulkJunk">
                            <i class="fa fa-trash mr-50"></i> Junk
                        </button>
                        <button type="button" class="oc-btn-soft" id="btnBulkDelete">
                            <i class="feather icon-trash-2 mr-50"></i> Löschen
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <div class="oc-card">
            <div class="oc-list-head">
                <div></div>
                <div>ID</div>
                <div>Name / Adresse / Kontakt</div>
                <div>Typ / Status / Priorität</div>
                <div>Produkte / Zuständig / Junk</div>
                <div>Verfasser</div>
                <div style="text-align:right;">Aktionen</div>
            </div>

            <div class="oc-list">


                @forelse($dataRows as $rawItem)
                    @php
                        $item = $toObject($rawItem);
                        $rowHighlight = ($highlightId == $item->id) ? 'highlight' : '';
                        $safeFullName = $fsName($item->name ?? null, $item->lastname ?? null);
                        $safeCity = $fsCity($item->city ?? null);
                        $typeLabel = $fsType($item);

                        $leadMatches = collect($existingLeadMatches->get($item->id, collect()))->map($toArray);
                        $bestLeadMatch = $leadMatches->first();

                        $createdAt = $item->created_at ?? now();
                        $hoursDifference = \Carbon\Carbon::parse($createdAt)->diffInHours(now());

                        $empTitle = $fsName($item->emp_name ?? null, $item->emp_lastname ?? null);
                        $empAvatar = (!empty($item->emp_image) && file_exists(public_path('images/employee/' . $item->emp_image)))
                            ? asset('images/employee/' . $item->emp_image)
                            : asset('images/gender/male.png');

                        $customerProducts = collect($productListGrouped->get($item->id, collect()))
                            ->map($toObject)
                            ->values();

                        $hasProduct = $customerProducts->isNotEmpty();

                        $statusRaw = strtolower(trim((string) ($item->status ?? '')));
                        $isJunkRow = $statusRaw === 'junk';

                        $statusClass = match ($statusRaw) {
                            'published' => 'green',
                            'junk' => 'red',
                            'unpublished' => 'orange',
                            'draft' => 'gray',
                            default => 'gray',
                        };

                        $statusLabel = match ($statusRaw) {
                            'published' => 'Veröffentlicht',
                            'junk' => 'Junk',
                            'unpublished' => 'Nicht verifiziert',
                            'draft' => 'Entwurf',
                            default => $fs($item->status ?? null, 'Unbekannt'),
                        };

                        $firstLetter = mb_strtoupper(mb_substr($safeFullName, 0, 1));

                        $info = [
                            'id' => $item->id,
                            'name' => $item->name ?? '',
                            'lastname' => $item->lastname ?? '',
                            'type' => $item->type ?? '',
                            'type_name' => $item->type_name ?? '',
                            'pre_type' => $item->pre_type ?? '',
                        ];
                    @endphp

                    <div class="oc-item {{ $rowHighlight }}">
                        <div class="oc-item-row" data-inquiry-id="{{ $item->id }}" data-inquiry-name="{{ $safeFullName }}"
                            data-inquiry-type="{{ $typeLabel }}" data-inquiry-city="{{ $safeCity }}"
                            data-has-product="{{ $hasProduct ? 1 : 0 }}"
                            data-status="{{ $fs($item->status ?? null, 'Unbekannt') }}">

                            <div class="oc-cell">
                                <div class="oc-cell-title">Auswahl</div>
                                @if(!$isDeletedPage)
                                    <div class="oc-checkbox">
                                        <input type="checkbox" class="inquiry-checkbox" name="selected_inquiries[]"
                                            value="{{ $item->id }}">
                                    </div>
                                @endif
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">ID</div>
                                <div class="d-flex flex-column" style="gap:10px;">
                                    <span class="oc-id-badge">#{{ $item->id }}</span>
                                    <div class="oc-avatar">{{ $firstLetter }}</div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Name / Adresse / Kontakt</div>
                                <div class="oc-main">
                                    <div class="d-flex align-items-start justify-content-between flex-wrap" style="gap:10px;">
                                        <div>
                                            <div class="oc-ttl">
                                                <a href="{{ url('inquiry_show/' . $item->id) }}"
                                                    style="color:inherit;text-decoration:none;">
                                                    {{ $safeFullName }}
                                                </a>
                                            </div>
                                            <div class="oc-subt-wrap">
                                                {{ $fs($item->street ?? null, 'Unbekannte Straße') }},
                                                {{ $fs($item->postcode ?? null, '—') }}
                                                {{ $safeCity }}
                                            </div>
                                            <div class="oc-subt-wrap">
                                                {{ $fs($item->email ?? null, 'Keine E-Mail') }}
                                                @if(!empty($item->phone))
                                                    • {{ $item->phone }}
                                                @elseif(!empty($item->telephone))
                                                    • {{ $item->telephone }}
                                                @endif
                                            </div>

                                            @if(!empty($item->firma))
                                                <div class="oc-note">
                                                    <i class="feather icon-briefcase"></i>
                                                    <strong>Firma:</strong> {{ $item->firma }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="d-flex" style="gap:8px;">
                                            <button type="button" class="oc-btn-ic primary js-open-contact"
                                                data-name="{{ $safeFullName }}"
                                                data-street="{{ $fs($item->street ?? null, 'Unbekannte Straße') }}"
                                                data-postcode="{{ $fs($item->postcode ?? null, '—') }}"
                                                data-city="{{ $safeCity }}"
                                                data-telephone="{{ $fs($item->telephone ?? null, 'Keine Telefonnummer') }}"
                                                data-phone="{{ $fs($item->phone ?? null, 'Keine Mobilnummer') }}"
                                                data-email="{{ $fs($item->email ?? null, 'Keine E-Mail') }}"
                                                data-firma="{{ $fs($item->firma ?? null, 'Keine Firma') }}" title="Kontakt">
                                                <i class="feather icon-info"></i>
                                            </button>

                                            @if(!empty($item->note))
                                                <button type="button" class="oc-btn-ic purple js-open-note"
                                                    data-name="{{ $safeFullName }}" data-note="{{ e($item->note) }}" title="Notiz">
                                                    <i class="fa fa-sticky-note-o"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Typ / Status / Priorität</div>
                                <div class="oc-main">
                                    <div class="oc-badges">
                                        <span class="oc-badge blue">{{ $typeLabel }}</span>
                                        <span class="oc-badge {{ $statusClass }}">{{ $statusLabel }}</span>

                                        @if(!empty($item->periority))
                                            <span class="oc-badge purple">{{ $item->periority }}</span>
                                        @endif
                                    </div>

                                    <div class="oc-subt-wrap mt-2">
                                        <strong>Erstellt:</strong>
                                        {{ \Carbon\Carbon::parse($createdAt)->isoFormat('DD.MM.YYYY') }}<br>
                                        <strong>Vor:</strong> {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                                    </div>

                                    <div class="d-flex align-items-center flex-wrap mt-2" style="gap:8px;">
                                        <i
                                            class="feather icon-alert-circle {{ (!empty($item->periority) && strtolower($item->periority) === 'sehr dringend') ? 'text-danger' : 'text-muted' }}"></i>
                                        <i
                                            class="feather icon-bell {{ $hoursDifference > 48 ? 'text-primary' : 'text-muted' }}"></i>
                                        <i
                                            class="feather icon-star {{ $hoursDifference <= 48 ? 'text-warning' : 'text-muted' }}"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Produkte / Zuständig / Junk</div>
                                <div class="oc-main">
                                    @if($customerProducts->isNotEmpty())
                                        <div class="oc-flow-list">
                                            @foreach($customerProducts->unique(fn($product) => data_get($product, 'id')) as $product)
                                                @php
                                                    $product = $toObject($product);
                                                    $serviceKey = strtolower($product->phase_section ?? '');
                                                    $service = $fs($servicesMap[$serviceKey] ?? ($product->phase_section ?? null), 'Unbekannte Dienstleistung');
                                                    $department = $fs($product->department_name ?? null, 'Unbekannte Abteilung');

                                                    $insideTitle = !empty($product->employee_id)
                                                        ? $fsName($product->ename ?? null, $product->elastname ?? null)
                                                        : 'Innendienst auswählen';

                                                    $fieldTitle = !empty($product->field_employee)
                                                        ? $fsName($product->fname ?? null, $product->flastname ?? null)
                                                        : 'Außendienst auswählen';

                                                    $productInitial = trim((string) ($product->initial ?? ''));
                                                    if ($productInitial === '') {
                                                        $articleGroup = trim((string) ($product->article_group ?? 'PR'));
                                                        $parts = preg_split('/[\s\-\/]+/', $articleGroup, -1, PREG_SPLIT_NO_EMPTY);
                                                        if (count($parts) >= 2) {
                                                            $productInitial = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
                                                        } else {
                                                            $productInitial = mb_strtoupper(mb_substr($articleGroup, 0, 2));
                                                        }
                                                    }
                                                    $productInitial = $productInitial !== '' ? $productInitial : 'PR';

                                                    $insideImg = $product->inside_image_url ?? asset('images/gender/male.png');
                                                    $fieldImg = $product->field_image_url ?? asset('images/gender/male.png');
                                                @endphp

                                                <div class="oc-flow-card">
                                                    <div class="oc-flow-top">
                                                        <div class="oc-flow-badge"
                                                            title="{{ $fs($product->article_group ?? null, 'Unbekanntes Produkt') }}">
                                                            {{ $productInitial }}
                                                        </div>

                                                        <div class="oc-flow-line"></div>

                                                        <img src="{{ $insideImg }}" alt="Innendienst"
                                                            class="oc-flow-avatar select-employee" data-type="employee"
                                                            data-id="{{ $product->id }}" title="{{ $insideTitle }}">

                                                        <div class="oc-flow-line"></div>

                                                        <img src="{{ $fieldImg }}" alt="Außendienst"
                                                            class="oc-flow-avatar outside select-employee" data-type="field_employee"
                                                            data-id="{{ $product->id }}" title="{{ $fieldTitle }}">
                                                    </div>

                                                    <div class="oc-flow-meta">
                                                        <div class="oc-flow-service">{{ $service }}</div>
                                                        <div class="oc-flow-department">{{ $department }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="oc-flow-empty">Keine Produkte vorhanden</div>
                                    @endif

                                    @if($isJunkRow)
                                        <div class="oc-junk-box">
                                            <div class="oc-junk-title">Junk-Information</div>
                                            <div class="oc-junk-text">
                                                <strong>Grund:</strong>
                                                {{ $fs($item->junk_reason ?? null, 'Kein Grund hinterlegt') }}
                                            </div>
                                            @if(!empty($item->junk_note))
                                                <div class="oc-junk-text" style="margin-top:6px;">
                                                    <strong>Notiz:</strong> {{ $item->junk_note }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Verfasser</div>
                                <div class="oc-main">
                                    <div class="d-flex align-items-center" style="gap:10px;">
                                        <img src="{{ $empAvatar }}" alt="avatar"
                                            style="width:42px;height:42px;border-radius:999px;object-fit:cover;border:1px solid #e5e7eb;">
                                        <div>
                                            <div class="oc-ttl" style="font-size:14px;">{{ $empTitle }}</div>
                                            <div class="oc-subt">{{ $fs($item->branch ?? null, 'Keine Niederlassung') }}</div>
                                            @if(!empty($item->direct_name) || !empty($item->direct_lastname))
                                                <div class="oc-subt">Zuständig:
                                                    {{ trim(($item->direct_name ?? '') . ' ' . ($item->direct_lastname ?? '')) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="oc-cell">
                                <div class="oc-cell-title">Aktionen</div>
                                <div class="oc-actions">
                                    <div class="oc-row-menu" data-menu>
                                        <button type="button" class="oc-btn-ic" data-menu-toggle title="Aktionen">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>

                                        <div class="oc-row-dropdown" data-menu-panel>
                                            @if($item->deleted_at)
                                                <a href="{{ url('inquiry_restore/' . $item->id) }}" class="oc-row-dropdown-item">
                                                    <i class="feather icon-refresh-ccw"></i>
                                                    <span>Wiederherstellen</span>
                                                </a>
                                            @endif

                                            @if($canUpdate && !$item->deleted_at)
                                                <a href="{{ url('inquiry_edit/' . $item->id) }}" class="oc-row-dropdown-item">
                                                    <i class="feather icon-edit"></i>
                                                    <span>Bearbeiten</span>
                                                </a>
                                            @endif

                                            @if(!$item->deleted_at && !$isJunkPage && !$isJunkRow && $canVerify)
                                                <button type="button" class="oc-row-dropdown-item verify-btn"
                                                    data-info='@json($info)'>
                                                    <i class="fa fa-check-circle"></i>
                                                    <span>Verifizieren</span>
                                                </button>
                                            @endif

                                            @if($canDelete && !$item->deleted_at && !$isJunkPage)
                                                <button type="button" class="oc-row-dropdown-item addNewProduct"
                                                    data-id="{{ $item->id }}">
                                                    <i class="feather icon-plus"></i>
                                                    <span>Produkt hinzufügen</span>
                                                </button>
                                            @endif

                                            @if(!$item->deleted_at)
                                                @if($isJunkPage || $isJunkRow)
                                                    <button type="button" class="oc-row-dropdown-item js-unjunk-btn"
                                                        data-id="{{ $item->id }}" data-name="{{ $safeFullName }}">
                                                        <i class="fa fa-undo"></i>
                                                        <span>Aus Junk entfernen</span>
                                                    </button>
                                                @elseif($canDelete)
                                                    <button type="button" class="oc-row-dropdown-item js-junk-btn"
                                                        data-id="{{ $item->id }}" data-name="{{ $safeFullName }}">
                                                        <i class="fa fa-trash"></i>
                                                        <span>Als Junk markieren</span>
                                                    </button>
                                                @endif
                                            @endif

                                            @if($canDelete && !$item->deleted_at)
                                                <button type="button" class="oc-row-dropdown-item text-danger js-open-delete"
                                                    data-name="{{ $safeFullName }}"
                                                    data-url="{{ route('inquiry.delete', $item->id) }}">
                                                    <i class="feather icon-trash-2"></i>
                                                    <span>Löschen</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($bestLeadMatch)
                            <div class="oc-existing-warning" data-existing-warning data-inquiry-id="{{ $item->id }}"
                                data-inquiry-name="{{ $safeFullName }}" data-delete-url="{{ route('inquiry.delete', $item->id) }}"
                                data-junk-url="{{ route('inquiry.junk', $item->id) }}">

                                <div class="oc-existing-warning-left">
                                    <div class="oc-existing-warning-icon">
                                        <i class="feather icon-alert-triangle"></i>
                                    </div>

                                    <div>
                                        <div class="oc-existing-warning-title">
                                            @if(($bestLeadMatch['source_type'] ?? null) === 'inquiry')
                                                Diese Anfrage existiert wahrscheinlich bereits als andere Anfrage
                                            @else
                                                Dieser Kontakt existiert wahrscheinlich bereits in Kunden/Leads
                                            @endif
                                        </div>

                                        <div class="oc-existing-warning-text">
                                            Gefundener Datensatz:
                                            <strong>
                                                {{ $bestLeadMatch['display_name'] ?? $bestLeadMatch['name'] ?? 'Unbekannt' }}
                                            </strong>

                                            @if(!empty($bestLeadMatch['customer_no']))
                                                · Kundennr.: <strong>{{ $bestLeadMatch['customer_no'] }}</strong>
                                            @endif

                                            @if(!empty($bestLeadMatch['status']))
                                                · Status: <strong>{{ $bestLeadMatch['status'] }}</strong>
                                            @endif

                                            <br>

                                            @if(!empty($bestLeadMatch['email']))
                                                E-Mail: {{ $bestLeadMatch['email'] }}
                                            @endif

                                            @if(!empty($bestLeadMatch['phone']))
                                                · Telefon: {{ $bestLeadMatch['phone'] }}
                                            @endif

                                            @if(!empty($bestLeadMatch['postcode']) || !empty($bestLeadMatch['city']))
                                                · Adresse:
                                                {{ trim(($bestLeadMatch['street'] ?? '') . ', ' . ($bestLeadMatch['postcode'] ?? '') . ' ' . ($bestLeadMatch['city'] ?? '')) }}
                                            @endif
                                        </div>

                                        <div class="oc-existing-warning-tags">
                                            <span class="oc-existing-warning-tag">
                                                Match: {{ $bestLeadMatch['score'] ?? 0 }}%
                                            </span>

                                            @foreach(($bestLeadMatch['reasons'] ?? []) as $reason)
                                                <span class="oc-existing-warning-tag">{{ $reason }}</span>
                                            @endforeach

                                            @foreach(($bestLeadMatch['products'] ?? []) as $matchedProduct)
                                                <span class="oc-existing-warning-tag">
                                                    Produkt: {{ $matchedProduct }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="oc-existing-warning-actions">
                                    @if(!empty($bestLeadMatch['profile_url']))
                                        <a href="{{ $bestLeadMatch['profile_url'] }}" class="oc-warning-action view">
                                            <i class="feather icon-external-link"></i>
                                            Kunde öffnen
                                        </a>
                                    @endif

                                    <button type="button" class="oc-warning-action junk js-existing-junk">
                                        <i class="fa fa-trash"></i>
                                        Als Junk
                                    </button>

                                    <button type="button" class="oc-warning-action delete js-existing-delete">
                                        <i class="feather icon-trash-2"></i>
                                        Anfrage löschen
                                    </button>
                                </div>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="oc-empty">Keine Datensätze gefunden.</div>
                @endforelse
            </div>
        </div>

        @if($isPaginator && method_exists($data, 'links') && $data->hasPages())
            <div class="oc-pagination">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                    <div class="oc-mini">
                        Zeige
                        <strong>{{ $data->firstItem() ?? 0 }}</strong>
                        bis
                        <strong>{{ $data->lastItem() ?? 0 }}</strong>
                        von
                        <strong>{{ $data->total() }}</strong>
                        Einträgen
                    </div>

                    <div class="oc-pagination-links">
                        {{ $data->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="oc-modal-backdrop" id="globalInfoModal">
        <div class="oc-modal oc-modal-md">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl" id="globalInfoModalTitle">Details</h3>
                <button class="oc-btn-ic" type="button" onclick="closeModal('globalInfoModal')">×</button>
            </div>
            <div class="oc-modal-b" id="globalInfoModalBody"></div>
            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('globalInfoModal')">Schließen</button>
            </div>
        </div>
    </div>

    <div class="oc-modal-backdrop" id="globalDeleteModal">
        <div class="oc-modal oc-modal-md">
            <div class="oc-modal-h">
                <h3 class="oc-modal-ttl">Anfrage löschen</h3>
                <button class="oc-btn-ic" type="button" onclick="closeModal('globalDeleteModal')">×</button>
            </div>
            <div class="oc-modal-b" id="globalDeleteModalBody">
                Möchten Sie diese Anfrage wirklich löschen? Diese Aktion kann später über die gelöschte Liste rückgängig
                gemacht werden.
            </div>
            <div class="oc-modal-f">
                <button type="button" class="oc-btn-soft" onclick="closeModal('globalDeleteModal')">Abbrechen</button>
                <a href="#" id="globalDeleteModalLink" class="oc-btn-ic danger" style="width:auto;padding:0 14px;">
                    Löschen
                </a>
            </div>
        </div>
    </div>

    <div class="custom-product-modal" id="addProductModal" aria-hidden="true">
        <div class="custom-product-modal__overlay" data-close-product-modal></div>

        <div class="custom-product-modal__panel">
            <form id="addProductForm">
                @csrf
                <input type="hidden" name="inquiry_id" id="modal_inquiry_id">

                <div class="custom-product-modal__header">
                    <div>
                        <div class="custom-product-modal__kicker">Anfrage Produkte</div>
                        <h5 class="custom-product-modal__title">Produkt hinzufügen</h5>
                    </div>

                    <button type="button" class="custom-product-modal__close" data-close-product-modal
                        aria-label="Schließen">
                        ×
                    </button>
                </div>

                <div class="custom-product-modal__body">
                    <div class="custom-product-modal__table-wrap">
                        <table class="custom-product-table">
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

                    <button type="button" class="custom-product-add-btn" id="modalAddRow">
                        <i class="feather icon-plus"></i>
                        Neue Zeile
                    </button>
                </div>

                <div class="custom-product-modal__footer">
                    <button type="button" class="custom-product-cancel-btn" data-close-product-modal>
                        Abbrechen
                    </button>

                    <button type="submit" class="custom-product-save-btn">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="verifyDrawerOverlay" class="verify-drawer-overlay"></div>
    <div id="verifyDrawer" class="verify-drawer">
        <div class="verify-drawer-header">
            <div>
                <div class="verify-drawer-title">Ausgewählte Anfragen prüfen</div>
                <div class="verify-drawer-count"><span id="verifyDrawerCount">0</span> ausgewählt</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="verifyDrawerClose">
                <i class="feather icon-x"></i>
            </button>
        </div>
        <div class="verify-drawer-body">
            <div class="mb-2">
                <label for="verifyDrawerType" class="small font-weight-bold mb-1">Zieltyp für alle ausgewählten:</label>
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
                <small class="text-muted d-block mt-1">Hinweis: Für Lead/Kunde sollten bereits Produkte hinterlegt
                    sein.</small>
            </div>

            <hr class="my-2">

            <div id="verifyDrawerList"></div>
        </div>
        <div class="verify-drawer-footer">
            <button type="button" class="btn btn-primary btn-block" id="verifyDrawerApply">
                <i class="fa fa-check-circle mr-50"></i> Verifizierung ausführen
            </button>
        </div>
    </div>

    <div class="oc-toast-wrap" id="toast-wrap"></div>
@endsection

@once
    @push('scripts')
        <script src="{{ asset('css/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            function openModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.add('open');
            }

            function closeModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.remove('open');
            }

            function toast(kind, title, msg) {
                const wrap = document.getElementById('toast-wrap');
                if (!wrap) return;

                const icons = {
                    ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
                    bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
                };

                const el = document.createElement('div');
                el.className = 'oc-toast';
                el.innerHTML = `
                        <div class="oc-toast-ic ${kind}">${icons[kind] || icons.ok}</div>
                        <div style="flex:1;">
                            <p class="oc-toast-ttl">${title}</p>
                            <p class="oc-toast-msg">${msg}</p>
                        </div>
                        <button class="oc-toast-x" onclick="this.parentElement.remove()">×</button>
                    `;
                wrap.appendChild(el);
                setTimeout(() => { try { el.remove(); } catch (e) { } }, 4000);
            }

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('oc-modal-backdrop')) {
                    e.target.classList.remove('open');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.oc-modal-backdrop.open').forEach(el => el.classList.remove('open'));
                }
            });

            @if(session('update_msg'))
                toast('ok', 'Aktualisiert', @json(session('update_msg')));
            @endif

            @if(session('save_msg'))
                toast('ok', 'Gespeichert', @json(session('save_msg')));
            @endif

            @if(session('delete_msg'))
                toast('bad', 'Gelöscht', @json(session('delete_msg')));
            @endif

            $(document).ready(function () {
                $('.js-select2-filter').select2({
                    width: '100%'
                });
            });
        </script>

        <script>
            document.addEventListener('click', function (e) {
                const contactBtn = e.target.closest('.js-open-contact');
                if (contactBtn) {
                    document.getElementById('globalInfoModalTitle').textContent = contactBtn.dataset.name || 'Kontakt';
                    document.getElementById('globalInfoModalBody').innerHTML = `
                            <p><strong>Adresse:</strong><br>${contactBtn.dataset.street}, ${contactBtn.dataset.postcode} ${contactBtn.dataset.city}</p>
                            <p><strong>Telefon:</strong> ${contactBtn.dataset.telephone}</p>
                            <p><strong>Mobil:</strong> ${contactBtn.dataset.phone}</p>
                            <p><strong>E-Mail:</strong> ${contactBtn.dataset.email}</p>
                            <p><strong>Firma:</strong> ${contactBtn.dataset.firma}</p>
                        `;
                    openModal('globalInfoModal');
                    return;
                }

                const noteBtn = e.target.closest('.js-open-note');
                if (noteBtn) {
                    document.getElementById('globalInfoModalTitle').textContent = `Notiz – ${noteBtn.dataset.name || ''}`;
                    document.getElementById('globalInfoModalBody').innerHTML = `<p>${noteBtn.dataset.note || ''}</p>`;
                    openModal('globalInfoModal');
                    return;
                }

                const deleteBtn = e.target.closest('.js-open-delete');
                if (deleteBtn) {
                    document.getElementById('globalDeleteModalBody').innerHTML =
                        `Möchten Sie <strong>${deleteBtn.dataset.name || 'diese Anfrage'}</strong> wirklich löschen? Diese Aktion kann später über die gelöschte Liste rückgängig gemacht werden.`;
                    document.getElementById('globalDeleteModalLink').setAttribute('href', deleteBtn.dataset.url || '#');
                    openModal('globalDeleteModal');
                }
            });
        </script>

        <script>
            $(document).on('click', '.verify-btn', function (e) {
                e.preventDefault();

                const rawData = $(this).attr('data-info') || '{}';
                let data;

                try {
                    data = JSON.parse(rawData);
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Fehlerhafte Daten', text: 'Verifizierungsdaten konnten nicht gelesen werden.' });
                    return;
                }

                if (!data || !data.id || (!data.name && !data.lastname)) {
                    Swal.fire({ icon: 'error', title: 'Fehlende Informationen', text: 'Es muss mindestens Vor- oder Nachname vorhanden sein.' });
                    return;
                }

                const currentType = data.type_name || data.pre_type || '';
                const options = ["Lead", "Lieferant", "Hersteller", "Geschäftspartner", "Architekt", "Nachunternehmer", "Bank", "Versicherung", "Bewerber", "others"];

                let optionsHtml = '';
                options.forEach(opt => {
                    const selected = (opt.toLowerCase() === String(currentType).toLowerCase()) ? 'selected' : '';
                    optionsHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
                });

                Swal.fire({
                    title: 'Anfrage verifizieren',
                    html: `
                            <div style="text-align:left;font-size:16px;">
                                <p><strong>Name:</strong> ${data.name ?? ''} ${data.lastname ?? ''}</p>
                                <p><strong>Aktueller Typ:</strong> ${currentType || '—'}</p>
                                <label for="verifyOption"><strong>Neuer Typ wählen:</strong></label>
                                <select id="verifyOption" class="swal2-select">${optionsHtml}</select>
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

                    $.ajax({
                        url: `/inquiry/${data.id}/verify`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            type: result.value
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Verifiziert',
                                text: 'Die Anfrage wurde erfolgreich verifiziert und übertragen.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                if (response.redirect_url) {
                                    window.location.href = response.redirect_url;
                                } else {
                                    location.reload();
                                }
                            });
                        },
                        error: function (xhr) {
                            let text = xhr.responseJSON?.message || 'Verifizierung fehlgeschlagen. Bitte erneut versuchen.';
                            if (xhr.responseJSON?.errors) {
                                const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                                if (firstKey && Array.isArray(xhr.responseJSON.errors[firstKey])) {
                                    text = xhr.responseJSON.errors[firstKey][0];
                                }
                            }
                            Swal.fire({ icon: 'error', title: 'Fehler', text });
                        }
                    });
                });
            });
        </script>

        <script>
            $(document).on('click', '.js-junk-btn', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                const name = $(this).data('name') || 'Diese Anfrage';

                Swal.fire({
                    title: 'Anfrage als Junk markieren',
                    html: `
                            <div style="text-align:left;">
                                <div style="margin-bottom:14px;padding:12px;border-radius:12px;background:#fff7ed;border:1px solid #fed7aa;">
                                    <div style="font-size:13px;color:#9a3412;font-weight:700;">Ausgewählte Anfrage</div>
                                    <div style="font-size:16px;color:#111827;font-weight:800;margin-top:4px;">${name}</div>
                                </div>

                                <label for="junk_reason" style="display:block;font-weight:700;margin-bottom:6px;">Junk-Grund *</label>
                                <input id="junk_reason"
                                       class="swal2-input"
                                       placeholder="z. B. Doppelt, Kein Interesse, Falsche Anfrage"
                                       style="margin:0 0 14px 0; width:100%;">

                                <label for="junk_note" style="display:block;font-weight:700;margin-bottom:6px;">Zusätzliche Notiz</label>
                                <textarea id="junk_note"
                                          class="swal2-textarea"
                                          placeholder="Optionale interne Notiz"
                                          style="margin:0; width:100%; min-height:110px;"></textarea>
                            </div>
                        `,
                    showCancelButton: true,
                    confirmButtonText: 'Als Junk speichern',
                    cancelButtonText: 'Abbrechen',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    focusConfirm: false,
                    preConfirm: () => {
                        const reason = $('#junk_reason').val().trim();
                        const note = $('#junk_note').val().trim();

                        if (!reason) {
                            Swal.showValidationMessage('Bitte einen Junk-Grund eingeben.');
                            return false;
                        }

                        return { reason, note };
                    }
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: `{{ url('/inquiry_junk') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            junk_reason: result.value.reason,
                            junk_note: result.value.note
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Gespeichert',
                                text: res.message || 'Anfrage wurde als Junk markiert.'
                            }).then(() => location.reload());
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: xhr.responseJSON?.message || 'Junk-Aktion fehlgeschlagen.'
                            });
                        }
                    });
                });
            });

            $(document).on('click', '.js-unjunk-btn', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                const name = $(this).data('name') || 'Diese Anfrage';

                Swal.fire({
                    title: 'Aus Junk entfernen',
                    html: `
                            <div style="text-align:left;">
                                <div style="padding:12px;border-radius:12px;background:#ecfdf5;border:1px solid #a7f3d0;">
                                    <div style="font-size:13px;color:#065f46;font-weight:700;">Wiederherstellen</div>
                                    <div style="font-size:16px;color:#111827;font-weight:800;margin-top:4px;">${name}</div>
                                </div>
                            </div>
                        `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, wiederherstellen',
                    cancelButtonText: 'Abbrechen',
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: `{{ url('/inquiry_unjunk') }}/${id}`,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Wiederhergestellt',
                                text: res.message || 'Anfrage wurde aus Junk entfernt.'
                            }).then(() => location.reload());
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: xhr.responseJSON?.message || 'Unjunk fehlgeschlagen.'
                            });
                        }
                    });
                });
            });
        </script>

        <script>
            document.addEventListener('click', function (e) {
                const toggle = e.target.closest('[data-menu-toggle]');

                document.querySelectorAll('.oc-item').forEach(item => {
                    item.classList.remove('menu-open-row');
                });

                document.querySelectorAll('[data-menu-panel].open').forEach(panel => {
                    if (!panel.contains(e.target) && !panel.previousElementSibling?.contains(e.target)) {
                        panel.classList.remove('open');
                    }
                });

                if (toggle) {
                    const menu = toggle.closest('[data-menu]');
                    const panel = menu ? menu.querySelector('[data-menu-panel]') : null;
                    const row = toggle.closest('.oc-item');

                    if (panel) {
                        e.stopPropagation();
                        panel.classList.toggle('open');

                        if (panel.classList.contains('open') && row) {
                            row.classList.add('menu-open-row');
                        }
                    }
                }
            });
        </script>

        <script>
            const SVC = @json($serviceList ?? []);
            const PROD = @json($products ?? []);
            const DEPTS = @json($departments ?? []);
            const EMP_IMG = "{{ asset('images/employee') }}";
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const URL_EMP = '{{ route("inquiry.department.employees") }}';
            const URL_SAVE = '{{ route("inquiry.products.save") }}';
            const URL_DEL = '{{ route("inquiry.products.delete") }}';

            let modalRowIndex = 0;
            let productModalShouldReload = false;

            const tService = (k) => {
                const m = {
                    complete: 'Komplettlösung',
                    montage: 'Montage',
                    product: 'Produkt',
                    plan: 'Planung',
                    maintenance: 'Wartung',
                    repair: 'Reparatur',
                    reclaim: 'Reklamation',
                    emergency: 'Notdienst',
                    others: 'Sonstiges'
                };

                return m[(k || '').toLowerCase()] || (k || '');
            };

            const debounce = (fn, ms = 150) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), ms);
                };
            };

            function openProductModal() {
                const modal = document.getElementById('addProductModal');
                if (!modal) return;

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('custom-product-modal-open');
            }

            function closeProductModal(shouldReload = true) {
                const modal = document.getElementById('addProductModal');
                if (!modal) return;

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('custom-product-modal-open');

                $('.product-select, .service-select, .department-select, .employee-select, .field-employee-select').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });

                if (shouldReload && productModalShouldReload) {
                    location.reload();
                }
            }

            function formatEmployee(opt) {
                if (!opt.id) return opt.text;

                const $el = $(opt.element);
                const img = $el.data('img') ? `${EMP_IMG}/${$el.data('img')}` : '';
                const pos = $el.data('positions') || '';

                return `
                        <div style="display:flex;align-items:center;gap:10px;">
                            ${img
                        ? `<img src="${img}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">`
                        : `<div style="width:36px;height:36px;border-radius:50%;background:#e5e7eb;"></div>`
                    }
                            <div>
                                <strong>${opt.text}</strong><br>
                                <small>${pos}</small>
                            </div>
                        </div>
                    `;
            }

            const formatEmployeeSelection = opt => opt.text;

            function ensureOption($sel, value, label) {
                const v = String(value);

                if (!$sel.find(`option[value="${v}"]`).length) {
                    $sel.append(`<option value="${v}">${label}</option>`);
                }
            }

            function fillEmployeesSelect($sel, list, placeholder) {
                $sel.empty().append(`<option value="">${placeholder}</option>`);

                (list || []).forEach(emp => {
                    $sel.append(`
                            <option value="${emp.id}"
                                    data-img="${emp.image || ''}"
                                    data-positions="${(emp.positions || []).join(', ')}">
                                ${emp.name} ${emp.lastname}
                            </option>
                        `);
                });

                $sel.trigger('change.select2');
            }

            function loadServices(idx) {
                const pid = $(`.product-select[data-index="${idx}"]`).val();
                const $s = $(`.service-select[data-index="${idx}"]`);
                const list = SVC.filter(x => String(x.product_id) === String(pid));

                $s.empty().append('<option value="">Service wählen</option>');

                list.forEach(x => {
                    $s.append(`<option value="${x.id}">${tService(x.phase_section)}</option>`);
                });

                $s.trigger('change.select2');
            }

            function fetchEmployees({ pid, did = null, sid = null, stage = 'inquiry' }) {
                return $.post(URL_EMP, {
                    _token: CSRF,
                    product_id: pid,
                    department_id: did,
                    service_id: sid,
                    stage: stage
                });
            }

            function newModalRow(idx) {
                return `
                        <tr data-index="${idx}">
                            <td>
                                <select class="custom-product-field product-select"
                                        data-index="${idx}"
                                        name="product_id[]">
                                    <option value="">Produkt wählen</option>
                                    ${PROD.map(p => `<option value="${p.id}" data-img="${p.image || ''}">${p.article_group}</option>`).join('')}
                                </select>
                            </td>

                            <td>
                                <select class="custom-product-field service-select"
                                        data-index="${idx}"
                                        name="service_id[]">
                                    <option value="">Service wählen</option>
                                </select>
                            </td>

                            <td>
                                <select class="custom-product-field department-select"
                                        data-index="${idx}"
                                        name="department_id[]">
                                    <option value="">Abteilung wählen</option>
                                    ${DEPTS.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('')}
                                </select>
                            </td>

                            <td>
                                <select class="custom-product-field employee-select"
                                        data-index="${idx}"
                                        name="employee_id[]">
                                    <option value="">Innendienst wählen</option>
                                </select>
                            </td>

                            <td>
                                <select class="custom-product-field field-employee-select"
                                        data-index="${idx}"
                                        name="field_employee[]">
                                    <option value="">Außendienst wählen</option>
                                </select>
                            </td>

                            <td>
                                <input type="datetime-local"
                                       class="custom-product-field"
                                       name="appointment_date[]"
                                       data-index="${idx}">
                            </td>

                            <td style="text-align:center;">
                                <button type="button" class="custom-product-remove-btn removeRow">
                                    <i class="feather icon-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
            }

            function initRow(idx) {
                const $modal = $('#addProductModal');
                const $p = $(`.product-select[data-index="${idx}"]`);
                const $s = $(`.service-select[data-index="${idx}"]`);
                const $d = $(`.department-select[data-index="${idx}"]`);
                const $eIn = $(`.employee-select[data-index="${idx}"]`);
                const $eOut = $(`.field-employee-select[data-index="${idx}"]`);

                const sel2 = ($el, conf = {}) => {
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }

                    $el.select2({
                        width: '100%',
                        dropdownParent: $modal,
                        ...conf
                    });
                };

                sel2($p);
                sel2($s);
                sel2($d);

                sel2($eIn, {
                    templateResult: formatEmployee,
                    templateSelection: formatEmployeeSelection,
                    escapeMarkup: m => m
                });

                sel2($eOut, {
                    templateResult: formatEmployee,
                    templateSelection: formatEmployeeSelection,
                    escapeMarkup: m => m
                });

                $p.on('change', async () => {
                    const pid = $p.val();

                    if (!pid) {
                        $s.empty().append('<option value="">Service wählen</option>').trigger('change.select2');
                        $d.val('').trigger('change.select2');
                        fillEmployeesSelect($eIn, [], 'Innendienst wählen');
                        fillEmployeesSelect($eOut, [], 'Außendienst wählen');
                        return;
                    }

                    loadServices(idx);

                    try {
                        const res = await fetchEmployees({ pid, stage: 'inquiry' });

                        const did = res?.department_id ? String(res.department_id) : '';
                        const sid = res?.service_id ? String(res.service_id) : '';

                        if (did) {
                            ensureOption($d, did, DEPTS.find(x => String(x.id) === did)?.department_name || `Abt. ${did}`);
                            $d.val(did).trigger('change.select2');
                        }

                        if (sid) {
                            const svcMeta = SVC.find(x => String(x.id) === sid);
                            const svcLabel = tService(svcMeta?.phase_section || '');

                            ensureOption($s, sid, svcLabel || `Service ${sid}`);
                            $s.val(sid).trigger('change.select2');
                        }

                        const internal = Array.isArray(res?.internal_employees) ? res.internal_employees : [];
                        const external = Array.isArray(res?.external_employees) && res.external_employees.length
                            ? res.external_employees
                            : internal;

                        fillEmployeesSelect($eIn, internal, 'Innendienst wählen');
                        fillEmployeesSelect($eOut, external, 'Außendienst wählen');
                    } catch (e) {
                        refreshEmployees(idx);
                    }
                });

                $d.on('change', debounce(() => refreshEmployees(idx)));
                $s.on('change', debounce(() => refreshEmployees(idx)));

                if (window.feather) {
                    feather.replace();
                }
            }

            function refreshEmployees(idx) {
                const pid = $(`.product-select[data-index="${idx}"]`).val();
                const sid = $(`.service-select[data-index="${idx}"]`).val();
                const did = $(`.department-select[data-index="${idx}"]`).val();

                const $eIn = $(`.employee-select[data-index="${idx}"]`);
                const $eOut = $(`.field-employee-select[data-index="${idx}"]`);

                if (!pid) {
                    fillEmployeesSelect($eIn, [], 'Innendienst wählen');
                    fillEmployeesSelect($eOut, [], 'Außendienst wählen');
                    return;
                }

                fetchEmployees({ pid, did, sid, stage: 'inquiry' })
                    .done(res => {
                        if (res?.department_id && String(res.department_id) !== String(did || '')) {
                            const $d = $(`.department-select[data-index="${idx}"]`);

                            ensureOption(
                                $d,
                                res.department_id,
                                DEPTS.find(x => String(x.id) === String(res.department_id))?.department_name || `Abt. ${res.department_id}`
                            );

                            $d.val(String(res.department_id)).trigger('change.select2');
                        }

                        if (res?.service_id && String(res.service_id) !== String(sid || '')) {
                            const $s = $(`.service-select[data-index="${idx}"]`);
                            const svcLabel = tService(SVC.find(x => String(x.id) === String(res.service_id))?.phase_section || '');

                            ensureOption($s, res.service_id, svcLabel || `Service ${res.service_id}`);
                            $s.val(String(res.service_id)).trigger('change.select2');
                        }

                        const internal = Array.isArray(res?.internal_employees) ? res.internal_employees : [];
                        const external = Array.isArray(res?.external_employees) && res.external_employees.length
                            ? res.external_employees
                            : internal;

                        fillEmployeesSelect($eIn, internal, 'Innendienst wählen');
                        fillEmployeesSelect($eOut, external, 'Außendienst wählen');
                    })
                    .fail(() => {
                        fillEmployeesSelect($eIn, [], 'Innendienst wählen');
                        fillEmployeesSelect($eOut, [], 'Außendienst wählen');
                    });
            }

            $(document).ready(function () {
                $(document).on('click', '[data-close-product-modal]', function () {
                    closeProductModal(true);
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && document.getElementById('addProductModal')?.classList.contains('is-open')) {
                        closeProductModal(true);
                    }
                });

                $(document).on('click', '.addNewProduct', function () {
                    const inquiryId = $(this).data('id');

                    productModalShouldReload = false;

                    $('#modal_inquiry_id').val(inquiryId);
                    $('#existingProductRows').empty();
                    $('#modalNewRows').empty();

                    modalRowIndex = 0;

                    $.get(`/inquiry/get/products/${inquiryId}`, function (rows) {
                        (rows || []).forEach(row => {
                            const productLabel = row.article_group || '—';
                            const serviceLabel = tService(row.phase_section);
                            const departmentLabel = row.department_name || '—';
                            const inImg = row.in_image ? `${EMP_IMG}/${row.in_image}` : '{{ asset("images/gender/male.png") }}';
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

                                        <td style="text-align:center;">
                                            <button type="button"
                                                    class="custom-product-delete-existing delete-product"
                                                    data-id="${row.id}">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `);
                        });

                        if (window.feather) {
                            feather.replace();
                        }
                    });

                    openProductModal();

                    $('#modalAddRow').trigger('click');
                });

                $('#modalAddRow').on('click', function () {
                    modalRowIndex++;
                    $('#modalNewRows').append(newModalRow(modalRowIndex));
                    initRow(modalRowIndex);
                });

                $(document).on('click', '.removeRow', function () {
                    const $row = $(this).closest('tr');

                    $row.find('select').each(function () {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                    });

                    $row.remove();
                });

                $(document).on('click', '.delete-product', function () {
                    const $tr = $(this).closest('tr');
                    const rowId = $(this).data('id');

                    Swal.fire({
                        title: 'Bist du sicher?',
                        text: 'Das Produkt wird dauerhaft gelöscht.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ja, löschen!',
                        cancelButtonText: 'Abbrechen',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280'
                    }).then(res => {
                        if (!res.isConfirmed) return;

                        $.ajax({
                            url: URL_DEL,
                            method: 'DELETE',
                            data: {
                                _token: CSRF,
                                id: rowId
                            },
                            success: function () {
                                productModalShouldReload = true;
                                $tr.remove();

                                Swal.fire('Gelöscht', 'Produkt erfolgreich gelöscht', 'success');
                            },
                            error: function () {
                                Swal.fire('Fehler', 'Löschen fehlgeschlagen', 'error');
                            }
                        });
                    });
                });

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

                        const empty = !p && !s && !d && !e && !f && !a;

                        if (empty) {
                            $(this).remove();
                            return;
                        }

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
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hinweis',
                            text: 'Bitte fügen Sie mindestens ein neues Produkt hinzu, bevor Sie speichern.'
                        });
                        return;
                    }

                    if (!ok) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fehler',
                            text: 'Alle Zeilen müssen vollständig sein: Produkt, Service, Abteilung und Innendienst.'
                        });
                        return;
                    }

                    $.post(URL_SAVE, payload)
                        .done(res => {
                            productModalShouldReload = false;

                            Swal.fire({
                                icon: 'success',
                                title: 'Gespeichert',
                                text: res.message || 'Erfolgreich gespeichert.',
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .fail(xhr => {
                            const errs = xhr.responseJSON?.errors
                                ? Object.values(xhr.responseJSON.errors).flat().join('\n')
                                : (xhr.responseJSON?.message || 'Speichern fehlgeschlagen.');

                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: errs
                            });
                        });
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                document.querySelectorAll('.select-employee').forEach(el => {
                    el.addEventListener('click', async function () {
                        const type = this.dataset.type;
                        const id = this.dataset.id;

                        try {
                            const res = await fetch(`/getAllEmployees`);
                            const employees = await res.json();

                            if (!employees.length) {
                                return Swal.fire('Keine Mitarbeiter', 'Für diese Abteilung wurden keine Mitarbeiter gefunden.', 'info');
                            }

                            let optionsHtml = `<select id="employeeSelect" class="form-control" style="width:100%">`;
                            employees.forEach(emp => {
                                const imgSrc = emp.image
                                    ? `/images/employee/${emp.image}`
                                    : (emp.gender === 'male' ? '/images/gender/male.png' : '/images/gender/female.png');
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
                                        return $(`<span><img src="${img}" style="width:26px;height:26px;border-radius:50%;margin-right:8px;vertical-align:middle;">${opt.text}</span>`);
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
                                Swal.fire('Aktualisiert!', 'Der Mitarbeiter wurde erfolgreich geändert.', 'success')
                                    .then(() => location.reload());
                            }

                        } catch (err) {
                            console.error(err);
                            Swal.fire('Fehler', 'Die Mitarbeiterliste konnte nicht geladen werden.', 'error');
                        }
                    });
                });
            });
        </script>

        <script>
            const INQUIRY_PRODUCTS = @json(
                $product_list
                    ->groupBy(fn($row) => data_get($row, 'inquiry_id'))
                    ->map(function ($rows) {
                        return $rows->map(fn($row) => data_get($row, 'article_group'))->filter()->unique()->values();
                    })
            );
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const headMaster = document.getElementById('checkAllInquiries');
                const selectedCount = document.getElementById('selectedCount');

                const btnBulkVerify = document.getElementById('btnBulkVerify');
                const btnBulkDelete = document.getElementById('btnBulkDelete');
                const btnBulkJunk = document.getElementById('btnBulkJunk');
                const btnBulkUnjunk = document.getElementById('btnBulkUnjunk');

                const drawer = document.getElementById('verifyDrawer');
                const drawerOverlay = document.getElementById('verifyDrawerOverlay');
                const drawerClose = document.getElementById('verifyDrawerClose');
                const drawerApply = document.getElementById('verifyDrawerApply');
                const drawerList = document.getElementById('verifyDrawerList');
                const drawerCount = document.getElementById('verifyDrawerCount');
                const drawerType = document.getElementById('verifyDrawerType');

                function rowCheckboxes() {
                    return Array.from(document.querySelectorAll('input.inquiry-checkbox[name="selected_inquiries[]"]'));
                }

                function getSelectedRows() {
                    return rowCheckboxes().filter(cb => cb.checked).map(cb => cb.closest('.oc-item-row')).filter(Boolean);
                }

                function getSelectedIds() {
                    return rowCheckboxes().filter(cb => cb.checked).map(cb => cb.value);
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
                    const arr = INQUIRY_PRODUCTS && INQUIRY_PRODUCTS[key] ? INQUIRY_PRODUCTS[key] : [];
                    return Array.isArray(arr) ? arr : [];
                }

                function escapeHtml(str) {
                    if (typeof str !== 'string') return str;
                    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
                }

                function buildDrawerList() {
                    if (!drawerList || !drawerCount) return;

                    const rows = getSelectedRows();
                    drawerList.innerHTML = '';
                    drawerCount.textContent = rows.length;

                    if (!rows.length) {
                        drawerList.innerHTML = '<p class="text-muted small mb-0">Keine Anfragen ausgewählt.</p>';
                        return;
                    }

                    rows.forEach(tr => {
                        const checkbox = tr.querySelector('input.inquiry-checkbox[name="selected_inquiries[]"]');
                        const id = tr.dataset.inquiryId || (checkbox ? checkbox.value : '');
                        const name = tr.dataset.inquiryName || '';
                        const type = tr.dataset.inquiryType || '';
                        const city = tr.dataset.inquiryCity || '';
                        const status = tr.dataset.status || '';

                        const products = getProductsForInquiry(id);
                        const hasProductFlag = tr.dataset.hasProduct === '1';
                        const hasProduct = hasProductFlag || products.length > 0;
                        const isLead = String(type).toLowerCase() === 'lead';

                        const wrapper = document.createElement('div');
                        wrapper.className = 'verify-drawer-item';

                        const productsHtml = products.length
                            ? `<div class="mt-1"><div class="verify-drawer-meta mb-25">Artikelgruppen:</div><div class="d-flex flex-wrap">${products.map(p => `<span class="badge badge-light-primary mr-25 mb-25">${escapeHtml(p)}</span>`).join('')}</div></div>`
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
                                            <span class="verify-drawer-badge ${hasProduct ? 'verify-drawer-badge--ok' : 'verify-drawer-badge--warn'}">${hasProduct ? 'hat Produkte' : 'ohne Produkte'}</span>
                                            <span class="verify-drawer-badge ${isLead ? 'verify-drawer-badge--lead' : 'verify-drawer-badge--other'}">${isLead ? 'Lead' : (escapeHtml(type || 'Typ offen'))}</span>
                                        </div>
                                        ${productsHtml}
                                    </div>
                                </div>
                            `;
                        drawerList.appendChild(wrapper);
                    });
                }

                function syncBulkUI() {
                    const rows = rowCheckboxes();
                    const selected = getSelectedRows().length;
                    const allChecked = rows.length > 0 && selected === rows.length;

                    if (selectedCount) selectedCount.textContent = selected;
                    if (headMaster) headMaster.checked = allChecked;

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

                document.addEventListener('change', function (e) {
                    if (e.target.matches && e.target.matches('input.inquiry-checkbox[name="selected_inquiries[]"]')) {
                        syncBulkUI();
                    }
                });

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

                if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
                if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

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

                            Swal.fire({
                                title: 'Verarbeite...',
                                text: 'Bitte warten.',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });

                            $.ajax({
                                url: '{{ route('inquiries.bulk.verify') }}',
                                method: 'POST',
                                data: {
                                    _token: csrf,
                                    type: type,
                                    ids: ids
                                },
                                success: function (res) {
                                    let icon = 'success';
                                    let title = 'Abgeschlossen';
                                    let html = '';

                                    if (res.processed_count > 0) {
                                        html += `<div class="text-success mb-2"><i class="fa fa-check-circle"></i> ${res.processed_count} Anfrage(n) erfolgreich verifiziert.</div>`;
                                    }

                                    if (res.skipped_count > 0) {
                                        icon = res.processed_count > 0 ? 'warning' : 'error';
                                        title = res.processed_count > 0 ? 'Teilweise abgeschlossen' : 'Fehlgeschlagen';

                                        html += `<div class="text-left mt-2">
                                                        <div class="font-weight-bold text-danger mb-1">${res.skipped_count} Anfrage(n) übersprungen:</div>
                                                        <ul class="pl-3 text-danger small" style="max-height:150px;overflow-y:auto;">`;

                                        if (Array.isArray(res.skipped)) {
                                            res.skipped.forEach(item => {
                                                html += `<li><strong>ID ${item.id}:</strong> ${item.reason}</li>`;
                                            });
                                        }

                                        html += `</ul></div>`;
                                    }

                                    Swal.fire({ icon, title, html, confirmButtonText: 'OK', width: 600 })
                                        .then(() => location.reload());
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
                                    Swal.fire('Gelöscht', (res.deleted || 0) + ' Anfrage(n) wurden gelöscht.', 'success')
                                        .then(() => location.reload());
                                },
                                error: function () {
                                    Swal.fire('Fehler', 'Löschen fehlgeschlagen.', 'error');
                                }
                            });
                        });
                    });
                }

                if (btnBulkJunk) {
                    btnBulkJunk.addEventListener('click', function () {
                        const ids = getSelectedIds();
                        if (!ids.length) {
                            Swal.fire('Hinweis', 'Bitte zuerst Anfragen auswählen.', 'info');
                            return;
                        }

                        Swal.fire({
                            title: 'Ausgewählte Anfragen als Junk markieren?',
                            html: `
                                    <div style="text-align:left;">
                                        <p><strong>${ids.length}</strong> Anfrage(n) werden als Junk markiert.</p>
                                        <label for="bulk_junk_reason"><strong>Junk-Grund</strong></label>
                                        <input id="bulk_junk_reason" class="swal2-input" placeholder="z. B. Doppelt / Kein Interesse / Spam">
                                        <label for="bulk_junk_note"><strong>Notiz</strong></label>
                                        <textarea id="bulk_junk_note" class="swal2-textarea" placeholder="Optionale zusätzliche Notiz"></textarea>
                                    </div>
                                `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ja, Junk',
                            cancelButtonText: 'Abbrechen',
                            preConfirm: () => {
                                const reason = document.getElementById('bulk_junk_reason').value.trim();
                                const note = document.getElementById('bulk_junk_note').value.trim();

                                if (!reason) {
                                    Swal.showValidationMessage('Bitte Junk-Grund eingeben.');
                                    return false;
                                }

                                return { reason, note };
                            }
                        }).then(result => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                url: '{{ route('inquiries.bulk.junk') }}',
                                method: 'POST',
                                data: {
                                    _token: csrf,
                                    ids: ids,
                                    junk_reason: result.value.reason,
                                    junk_note: result.value.note
                                },
                                success: function (res) {
                                    Swal.fire('Aktualisiert', (res.junked || 0) + ' Anfrage(n) wurden als Junk markiert.', 'success')
                                        .then(() => location.reload());
                                },
                                error: function () {
                                    Swal.fire('Fehler', 'Aktion fehlgeschlagen.', 'error');
                                }
                            });
                        });
                    });
                }

                if (btnBulkUnjunk) {
                    btnBulkUnjunk.addEventListener('click', function () {
                        const ids = getSelectedIds();
                        if (!ids.length) {
                            Swal.fire('Hinweis', 'Bitte zuerst Anfragen auswählen.', 'info');
                            return;
                        }

                        Swal.fire({
                            title: 'Ausgewählte Anfragen wiederherstellen?',
                            html: `<p><strong>${ids.length}</strong> Anfrage(n) werden aus Junk entfernt.</p>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ja, wiederherstellen',
                            cancelButtonText: 'Abbrechen',
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#6b7280'
                        }).then(result => {
                            if (!result.isConfirmed) return;

                            const requests = ids.map(id => {
                                return $.ajax({
                                    url: `{{ url('/inquiry_unjunk') }}/${id}`,
                                    type: 'POST',
                                    data: { _token: csrf }
                                });
                            });

                            Promise.all(requests)
                                .then(() => {
                                    Swal.fire('Wiederhergestellt', `${ids.length} Anfrage(n) wurden aus Junk entfernt.`, 'success')
                                        .then(() => location.reload());
                                })
                                .catch(() => {
                                    Swal.fire('Fehler', 'Wiederherstellung fehlgeschlagen.', 'error');
                                });
                        });
                    });
                }

                syncBulkUI();
            });
        </script>

        <script>
            document.addEventListener('click', function (e) {
                const junkBtn = e.target.closest('.js-existing-junk');
                const deleteBtn = e.target.closest('.js-existing-delete');

                if (junkBtn) {
                    e.preventDefault();

                    const banner = junkBtn.closest('[data-existing-warning]');
                    if (!banner) return;

                    const inquiryId = banner.dataset.inquiryId;
                    const inquiryName = banner.dataset.inquiryName || 'diese Anfrage';
                    const junkUrl = banner.dataset.junkUrl;

                    Swal.fire({
                        title: 'Als Junk markieren?',
                        html: `
                                <div style="text-align:left;">
                                    <p>
                                        Die Anfrage <strong>${inquiryName}</strong> scheint bereits als Kunde/Lead zu existieren.
                                    </p>
                                    <p class="text-muted mb-0">
                                        Sie wird als Junk gespeichert mit dem Grund: <strong>Bereits als Kunde vorhanden</strong>.
                                    </p>
                                </div>
                            `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ja, als Junk markieren',
                        cancelButtonText: 'Abbrechen',
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6b7280'
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            url: junkUrl,
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                junk_reason: 'Bereits als Kunde vorhanden',
                                junk_note: 'Diese Anfrage wurde automatisch als möglicher Duplikat-Kunde erkannt.'
                            },
                            success: function (res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Als Junk markiert',
                                    text: res.message || 'Die Anfrage wurde als Junk markiert.',
                                    timer: 1400,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Fehler',
                                    text: xhr.responseJSON?.message || 'Die Anfrage konnte nicht als Junk markiert werden.'
                                });
                            }
                        });
                    });

                    return;
                }

                if (deleteBtn) {
                    e.preventDefault();

                    const banner = deleteBtn.closest('[data-existing-warning]');
                    if (!banner) return;

                    const inquiryName = banner.dataset.inquiryName || 'diese Anfrage';
                    const deleteUrl = banner.dataset.deleteUrl;

                    Swal.fire({
                        title: 'Anfrage löschen?',
                        html: `
                                <div style="text-align:left;">
                                    <p>
                                        Die Anfrage <strong>${inquiryName}</strong> scheint bereits als Kunde/Lead zu existieren.
                                    </p>
                                    <p class="text-muted mb-0">
                                        Die Anfrage wird in die gelöschte Liste verschoben.
                                    </p>
                                </div>
                            `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ja, löschen',
                        cancelButtonText: 'Abbrechen',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280'
                    }).then(result => {
                        if (!result.isConfirmed) return;
                        window.location.href = deleteUrl;
                    });
                }
            });
        </script>

    @endpush
@endonce

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: "{{ $pageTitle }}",
                url: "{{ url('inquiry_view') }}",
                clickable: false
            },
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush