@extends('admin.layouts.app')

@section('title', 'AUFTRÄGE')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

    @php
        use Illuminate\Pagination\AbstractPaginator;
        use Illuminate\Pagination\LengthAwarePaginator;

        $currentRoute = Route::currentRouteName();

        $pageTitle = match ($currentRoute) {
            'deal.junk.list' => 'JUNK AUFTRÄGE',
            'deal.all.list' => 'ALLE AUFTRÄGE',
            'deal.delete.list' => 'GELÖSCHTE AUFTRÄGE',
            default => 'AUFTRÄGE',
        };

        $isPaginator = $data instanceof LengthAwarePaginator || $data instanceof AbstractPaginator;
        $items = $isPaginator ? collect($data->items()) : collect($data);

        $totalCount = $isPaginator ? $data->total() : $items->count();
        $openCount = (int) $items->where('status', 'open')->count();
        $confirmCount = (int) $items->where('status', 'confirm')->count();
        $inconfirmCount = (int) $items->where('status', 'inconfirm')->count();
        $totalValue = (float) $items->sum(fn($item) => (float) ($item->price ?? 0));

        $dealWorkflowStages = collect($dealWorkflowStages ?? []);
        if ($dealWorkflowStages->isEmpty()) {
            $dealWorkflowStages = collect([
                (object) ['key' => 'open', 'label' => 'Offen', 'name' => 'Offen', 'color' => '#f59e0b', 'is_default' => true],
                (object) ['key' => 'confirm', 'label' => 'Bestätigt', 'name' => 'Bestätigt', 'color' => '#10b981', 'is_default' => false],
                (object) ['key' => 'inconfirm', 'label' => 'Unbestätigt', 'name' => 'Unbestätigt', 'color' => '#ef4444', 'is_default' => false],
                (object) ['key' => 'pause', 'label' => 'Pausiert', 'name' => 'Pausiert', 'color' => '#f59e0b', 'is_default' => false],
                (object) ['key' => 'cancel', 'label' => 'Absage', 'name' => 'Absage', 'color' => '#ef4444', 'is_default' => false],
            ]);
        }
        $dealWorkflowLabelMap = $dealWorkflowLabelMap ?? $dealWorkflowStages->mapWithKeys(fn($stage) => [(string) $stage->key => (string) ($stage->label ?? $stage->name ?? $stage->key)])->all();
        $dealWorkflowColorMap = $dealWorkflowColorMap ?? $dealWorkflowStages->mapWithKeys(fn($stage) => [(string) $stage->key => (string) ($stage->color ?? '#93c21c')])->all();

    @endphp

    <style>
        :root {
            --deal-text: #111827;
            --deal-muted: #6b7280;
            --deal-border: #e5e7eb;
            --deal-border-strong: #d1d5db;
            --deal-surface: #fff;
            --deal-soft: #f9fafb;
            --deal-primary: var(--sa-accent);
            --deal-primary-hover: var(--sa-accent-hover);
            --deal-primary-soft: var(--sa-accent-light);
            --deal-blue: #74b2d4;
            --deal-blue-soft: #eff6ff;
            --deal-success: #10b981;
            --deal-success-soft: #ecfdf5;
            --deal-warning: #f59e0b;
            --deal-warning-soft: #fffbeb;
            --deal-danger: #ef4444;
            --deal-danger-soft: #fef2f2;
            --deal-ring: 0 0 0 3px rgba(147, 194, 28, .16);
            --deal-shadow: 0 1px 2px rgba(0, 0, 0, .05);
            --deal-shadow-md: 0 10px 30px rgba(15, 23, 42, .10);
            --deal-shadow-lg: 0 18px 45px rgba(15, 23, 42, .18);
            --deal-radius: 14px;
            --deal-transition: all .2s ease;
        }

        /* ===== GLOBAL / LAYOUT ===== */
        .deal-wrap {
            color: var(--deal-text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .deal-card,
        .deal-stat,
        .deal-toolbar,
        .deal-pagination,
        .deal-item,
        .deal-modal,
        .kanban-card,
        .gallery-item {
            background: var(--deal-surface);
            border: 1px solid var(--deal-border);
            box-shadow: var(--deal-shadow);
        }

        .deal-link,
        .deal-link:hover,
        .deal-btn,
        .deal-btn:hover,
        .deal-btn-soft,
        .deal-btn-soft:hover,
        .deal-breadcrumb a,
        .deal-breadcrumb a:hover {
            text-decoration: none;
        }

        /* ===== HEADER ===== */
        .deal-header {
            margin: 0 18px;
        }

        .deal-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 16px;
        }

        .deal-title {
            margin: 0;
            color: var(--deal-text);
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -.03em;
        }

        .deal-sub {
            margin-top: 4px;
            color: var(--deal-muted);
            font-size: 14px;
        }

        .deal-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            color: var(--deal-muted);
            font-size: 13px;
        }

        .deal-breadcrumb a {
            color: var(--deal-muted);
            font-weight: 800;
            transition: var(--deal-transition);
        }

        .deal-breadcrumb a:hover,
        .deal-breadcrumb .current {
            color: var(--deal-text);
        }

        .deal-breadcrumb .current {
            font-weight: 900;
        }

        /* ===== BUTTONS ===== */
        .deal-btn,
        .deal-btn-soft,
        .deal-btn-ic {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            transition: var(--deal-transition);
        }

        .deal-btn {
            height: 42px;
            padding: 0 18px;
            border: 0;
            border-radius: 12px;
            background: var(--deal-primary);
            color: #fff;
            font-weight: 900;
        }

        .deal-btn:hover {
            background: var(--deal-primary-hover);
            color: #fff;
        }

        .deal-btn-soft {
            height: 42px;
            padding: 0 14px;
            border: 1px solid var(--deal-border);
            border-radius: 12px;
            background: #fff;
            color: var(--deal-text);
            font-weight: 800;
        }

        .deal-btn-soft:hover {
            background: var(--deal-soft);
            color: var(--deal-text);
            border-color: var(--deal-border-strong);
        }

        .deal-btn-ic {
            width: 30px;
            height: 30px;
            min-width: 30px;
            border: 1px solid var(--deal-border);
            border-radius: 8px;
            background: #fff;
            color: var(--deal-muted);
            font-size: 12px;
        }

        .deal-btn-ic i {
            font-size: 13px;
        }

        .deal-btn-ic:hover {
            background: var(--deal-soft);
            color: var(--deal-text);
            border-color: var(--deal-border-strong);
        }

        .deal-btn-ic.primary {
            color: var(--deal-primary);
            border-color: #dceeb6;
            background: var(--deal-primary-soft);
        }

        .deal-btn-ic.warning {
            color: #d97706;
            border-color: #fde7b0;
            background: var(--deal-warning-soft);
        }

        .deal-btn-ic.success {
            color: var(--deal-success);
            border-color: #bfead8;
            background: var(--deal-success-soft);
        }

        .deal-btn-ic.danger {
            color: var(--deal-danger);
            border-color: rgba(239, 68, 68, .18);
            background: var(--deal-danger-soft);
        }

        /* ===== ANALYTICS ===== */
        .deal-analytics {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .deal-stat {
            min-height: 88px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border-radius: 18px;
        }

        .deal-stat-icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
        }

        .deal-stat-icon.total {
            background: var(--deal-blue-soft);
            color: var(--deal-blue);
        }

        .deal-stat-icon.open {
            background: var(--deal-warning-soft);
            color: #d97706;
        }

        .deal-stat-icon.confirm {
            background: var(--deal-success-soft);
            color: var(--deal-success);
        }

        .deal-stat-icon.inconfirm {
            background: var(--deal-danger-soft);
            color: var(--deal-danger);
        }

        .deal-stat-icon.value {
            background: var(--deal-primary-soft);
            color: var(--deal-primary);
        }

        .deal-stat-label {
            color: var(--deal-muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .deal-stat-value {
            margin-top: 4px;
            color: var(--deal-text);
            font-size: 23px;
            line-height: 1.1;
            font-weight: 900;
        }

        .deal-stat-sub {
            margin-top: 4px;
            color: var(--deal-muted);
            font-size: 12px;
        }

        /* ===== TOOLBAR / INPUTS / SELECT2 ===== */
        .deal-toolbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
            padding: 14px 16px;
            border-radius: 16px;
            overflow-x: auto;
            overflow-y: visible;
            flex-wrap: nowrap;
        }

        .deal-toolbar-left,
        .deal-toolbar-right {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: nowrap;
        }

        .deal-toolbar-left {
            flex: 1 1 auto;
            min-width: max-content;
        }

        .deal-toolbar-right {
            flex: 0 0 auto;
        }

        .deal-filter-block {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 0 0 150px;
            width: 150px;
        }

        .deal-filter-block.search {
            flex-basis: 260px;
            width: 260px;
        }

        .deal-filter-block.date {
            flex-basis: 145px;
            width: 145px;
        }

        .deal-toolbar::-webkit-scrollbar,
        .deal-table-wrap::-webkit-scrollbar,
        .deal-list-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .deal-toolbar::-webkit-scrollbar-thumb,
        .deal-table-wrap::-webkit-scrollbar-thumb,
        .deal-list-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .deal-input,
        .deal-select,
        .deal-input-form,
        .deal-select-form {
            width: 100%;
            height: 42px;
            min-height: 42px;
            padding: 10px 12px;
            border: 1px solid var(--deal-border);
            border-radius: 10px;
            outline: none;
            background: #fff;
            color: var(--deal-text);
            font-size: 14px;
            transition: var(--deal-transition);
            box-sizing: border-box;
        }

        .deal-input,
        .deal-input-form {
            background: var(--deal-soft);
        }

        .deal-input:focus,
        .deal-select:focus,
        .deal-input-form:focus,
        .deal-select-form:focus {
            border-color: var(--deal-primary);
            box-shadow: var(--deal-ring);
            background: #fff;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 42px !important;
            min-height: 42px !important;
            display: flex !important;
            align-items: center !important;
            border: 1px solid var(--deal-border) !important;
            border-radius: 10px !important;
            background: var(--deal-soft) !important;
            box-shadow: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            height: 40px !important;
            line-height: 40px !important;
            padding-left: 12px !important;
            padding-right: 34px !important;
            color: var(--deal-text) !important;
            font-size: 14px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--deal-primary) !important;
            box-shadow: var(--deal-ring) !important;
            background: #fff !important;
        }

        .select2-dropdown {
            border: 1px solid var(--deal-border) !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: var(--deal-shadow-md) !important;
        }

        .select2-search--dropdown {
            padding: 10px !important;
            background: #fff !important;
        }

        .select2-search--dropdown .select2-search__field {
            height: 38px !important;
            padding: 8px 10px !important;
            border: 1px solid var(--deal-border) !important;
            border-radius: 10px !important;
            outline: none !important;
        }

        .select2-results__option {
            padding: 10px 12px !important;
            font-size: 14px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: var(--deal-primary) !important;
            color: #fff !important;
        }

        /* ===== BULK BAR ===== */
        .deal-bulk-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
            padding: 12px 14px;
            border: 1px solid var(--deal-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: var(--deal-shadow);
        }

        .deal-bulk-left,
        .deal-bulk-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .deal-check-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: var(--deal-text);
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        .deal-selected-count {
            color: var(--deal-muted);
            font-size: 13px;
        }

        .deal-row-checkbox,
        #selectAllDeals,
        #selectAllDealsHead {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--deal-primary);
        }

        /* ===== TABS / LIST ===== */
        .deal-card {
            overflow: visible;
            border-radius: 18px;
        }

        .deal-tabs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            padding: 12px 14px 0;
            border-bottom: 1px solid var(--deal-border);
        }

        .deal-tab-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 13px;
            border: 0;
            border-radius: 12px 12px 0 0;
            background: transparent;
            color: var(--deal-muted);
            font-weight: 800;
            cursor: pointer;
            transition: var(--deal-transition);
        }

        .deal-tab-link:hover,
        .deal-tab-link.active {
            color: var(--deal-text);
        }

        .deal-tab-link.active {
            background: var(--deal-soft);
            border: 1px solid var(--deal-border);
            border-bottom-color: var(--deal-soft);
        }

        .deal-tab-content {
            padding: 8px;
            overflow: visible;
        }

        .deal-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .deal-table-head,
        .deal-item-row,
        .deal-list {
            width: 100%;
            min-width: 0 !important;
        }

        .deal-table-head,
        .deal-item-row {
            display: grid;
            align-items: center;
            gap: 7px;
            grid-template-columns:
                30px
                82px
                minmax(145px, .85fr)
                minmax(145px, .85fr)
                112px
                92px
                112px
                48px
                54px
                minmax(178px, 1.05fr)
                48px;
        }

        .deal-table-head {
            padding: 10px 10px 8px;
            border-bottom: 1px solid var(--deal-border);
            background: #fafafa;
            color: var(--deal-muted);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .deal-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 10px 0 0;
            position: relative;
            z-index: 1;
        }

        .deal-item {
            margin: 0;
            overflow: visible;
            position: relative;
            z-index: 1;
            border-radius: 12px;
            background: #fff;
            transition: var(--deal-transition);
        }

        .deal-item:hover {
            z-index: 20;
            border-color: var(--deal-border-strong);
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
        }

        .deal-item.dropdown-open {
            z-index: 99999 !important;
        }

        .deal-item-row {
            min-height: 56px;
            padding: 8px;
        }

        .deal-cell {
            min-width: 0 !important;
            overflow: hidden;
        }

        .deal-cell:last-child,
        .deal-actions,
        .dropdown-icon-wrapper {
            overflow: visible !important;
        }

        .deal-cell-title {
            display: none;
            margin-bottom: 5px;
            color: var(--deal-muted);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .deal-main,
        .deal-service-box {
            min-width: 0;
        }

        .deal-id-badge {
            min-width: 58px;
            max-width: 100%;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 8px;
            border-radius: 8px;
            background: var(--deal-blue-soft);
            color: var(--deal-blue);
            font-size: 11px;
            font-weight: 900;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .deal-ttl {
            max-width: 100%;
            margin-bottom: 3px;
            color: var(--deal-text);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .deal-subt {
            color: var(--deal-muted);
            font-size: 10px;
            line-height: 1.25;
        }

        .deal-subt>div,
        .deal-date-line,
        .deal-price {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .deal-service-box {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .deal-service-badge {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #7dc242;
            color: #fff;
            font-size: 11px;
            font-weight: 900;
        }

        .deal-service-line {
            width: 6px;
            height: 3px;
            flex: 0 0 6px;
            border-radius: 999px;
            background: #7dc242;
        }

        .deal-profile {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            object-fit: cover;
            border: 2px solid #7dc242;
            border-radius: 999px;
        }

        .deal-profile.warn {
            border-color: #f4a459;
        }

        .deal-profile.danger {
            border-color: #ea5455;
        }

        .deal-price {
            color: var(--deal-text);
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
        }

        .deal-date-line {
            margin-bottom: 3px;
            color: var(--deal-muted);
            font-size: 10px;
            line-height: 1.2;
            cursor: pointer;
        }

        .deal-status-pill {
            max-width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 7px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .deal-status-pill.open {
            background: var(--deal-warning-soft);
            color: #b45309;
        }

        .deal-status-pill.confirm {
            background: var(--deal-success-soft);
            color: #047857;
        }

        .deal-status-pill.inconfirm {
            background: var(--deal-danger-soft);
            color: #b91c1c;
        }

        .deal-status-pill.default {
            background: #f3f4f6;
            color: #4b5563;
        }

        .deal-actions {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .gallery-container {
            display: none !important;
        }

        .deal-empty {
            margin: 16px;
            padding: 48px;
            text-align: center;
            color: var(--deal-muted);
            border: 1px dashed var(--deal-border);
            border-radius: 14px;
            background: #fff;
        }

        /* ===== BADGES ===== */
        .delivery-note-btn {
            position: relative;
        }

        .delivery-note-btn.has-notes {
            color: #047857;
            border-color: #bfead8;
            background: var(--deal-success-soft);
        }

        .delivery-note-btn.no-notes {
            color: #9ca3af;
            background: var(--deal-soft);
        }

        .delivery-note-badge,
        .deal-files-badge,
        .deal-actions .badge {
            position: absolute;
            top: -5px !important;
            right: -5px !important;
            min-width: 15px !important;
            height: 15px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px !important;
            border: 2px solid #fff;
            border-radius: 999px;
            background: var(--deal-danger);
            color: #fff;
            font-size: 8px !important;
            font-weight: 900;
            line-height: 12px !important;
        }

        @keyframes pulseGreenSoft {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, .5);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .badge-measurement-done {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-left: 4px;
            padding: 2px 5px;
            border: 1px solid #a7f3d0;
            border-radius: 999px;
            background: var(--deal-success-soft);
            color: #047857;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            vertical-align: middle;
            animation: pulseGreenSoft 2s infinite;
        }

        .badge-measurement-done i {
            font-size: 11px;
        }


        .deal-update-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 6px;
            padding: 2px 7px;
            border-radius: 999px;
            border: 1px solid #d9ef9d;
            background: #f4fae7;
            color: #55720d;
            font-size: 9px;
            font-weight: 900;
            vertical-align: middle;
            white-space: nowrap;
        }

        .deal-update-badge.fresh {
            background: var(--deal-primary);
            color: #fff;
            border-color: var(--deal-primary);
            animation: dealFreshPulse 2s infinite;
        }

        .deal-latest-change-line {
            color: #55720d;
            font-weight: 800;
        }

        .deal-inline-status-select {
            width: 100%;
            min-width: 132px;
            height: 32px;
            border-radius: 999px;
            border: 1px solid color-mix(in srgb, var(--status-color, #93c21c) 55%, #ffffff);
            background: color-mix(in srgb, var(--status-color, #93c21c) 12%, #ffffff);
            color: var(--status-color, #55720d);
            font-size: 11px;
            font-weight: 900;
            padding: 0 8px;
            outline: none;
            cursor: pointer;
        }

        .deal-inline-status-select:focus {
            box-shadow: var(--deal-ring);
            border-color: var(--deal-primary);
        }

        @keyframes dealFreshPulse {
            0% { box-shadow: 0 0 0 0 rgba(147, 194, 28, .45); }
            70% { box-shadow: 0 0 0 7px rgba(147, 194, 28, 0); }
            100% { box-shadow: 0 0 0 0 rgba(147, 194, 28, 0); }
        }



        /* ===== COMPACT AUFTRAG LIST REDESIGN ===== */
        .deal-table-head,
        .deal-item-row {
            grid-template-columns:
                30px
                82px
                minmax(145px, .85fr)
                minmax(145px, .85fr)
                112px
                92px
                112px
                48px
                54px
                minmax(178px, 1.05fr)
                48px !important;
            gap: 6px !important;
        }

        .deal-item-row {
            min-height: 62px;
            padding: 7px 8px;
        }

        .deal-cell {
            min-width: 0 !important;
        }

        .deal-cell.status-cell,
        .deal-cell.action-cell {
            overflow: visible !important;
        }

        .deal-ttl {
            font-size: 12px;
            line-height: 1.15;
            margin-bottom: 2px;
        }

        .deal-subt {
            font-size: 9px;
            line-height: 1.25;
        }

        .deal-subt > div,
        .deal-date-line,
        .deal-price {
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .deal-date-line {
            font-size: 9px;
            line-height: 1.25;
            margin-bottom: 2px;
        }

        .deal-update-badge {
            margin-left: 0;
            margin-top: 3px;
            max-width: 100%;
            padding: 2px 6px;
            font-size: 8px;
        }

        .deal-latest-change-line {
            display: flex;
            align-items: center;
            gap: 3px;
            margin-top: 3px;
            max-width: 100%;
            color: #55720d;
            font-size: 9px;
            font-weight: 800;
        }

        .deal-inline-status-select {
            min-width: 160px;
            height: 34px;
            padding: 0 9px;
            font-size: 10px;
        }

        .deal-status-meta {
            margin-top: 4px;
            color: var(--deal-muted);
            font-size: 9px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== PAGINATION ===== */
        .deal-pagination {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 14px;
        }

        .deal-pagination .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 0;
        }

        .deal-pagination .page-item .page-link {
            padding: 8px 12px;
            border: 1px solid var(--deal-border);
            border-radius: 10px !important;
            color: var(--deal-text);
            line-height: 1.1;
            box-shadow: none !important;
        }

        .deal-pagination .page-item.active .page-link {
            background: var(--deal-primary);
            border-color: var(--deal-primary);
            color: #fff;
        }

        .deal-pagination .page-item.disabled .page-link {
            color: #9ca3af;
            background: var(--deal-soft);
        }

        /* ===== CUSTOM MODAL ===== */
        .deal-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(17, 24, 39, .55);
            backdrop-filter: blur(3px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .22s ease;
        }

        .deal-modal-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }

        .deal-modal {
            width: 100%;
            max-width: 760px;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: var(--deal-shadow-lg);
            transform: translateY(12px) scale(.985);
            transition: transform .22s ease;
        }

        .deal-modal-backdrop.open .deal-modal {
            transform: translateY(0) scale(1);
        }

        .deal-modal-h,
        .deal-modal-f {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: #fafafa;
        }

        .deal-modal-h {
            justify-content: space-between;
            border-bottom: 1px solid var(--deal-border);
        }

        .deal-modal-f {
            justify-content: flex-end;
            flex-wrap: wrap;
            border-top: 1px solid var(--deal-border);
        }

        .deal-modal-ttl {
            margin: 0;
            color: var(--deal-text);
            font-size: 16px;
            font-weight: 900;
            line-height: 1.2;
        }

        .deal-modal-b {
            max-height: 72vh;
            padding: 18px;
            overflow-y: auto;
        }

        .deal-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .deal-form-group {
            margin-bottom: 0;
        }

        .deal-label {
            display: block;
            margin-bottom: 6px;
            color: var(--deal-text);
            font-size: 13px;
            font-weight: 700;
        }

        /* ===== NOTES / UPLOAD SIDEBARS ===== */
        .note-sidebar,
        .upload-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            height: 100vh;
            background: #fff;
            border-right: 1px solid var(--deal-border);
            box-shadow: 4px 0 24px rgba(0, 0, 0, .16);
            transition: left .3s ease;
        }

        .note-sidebar {
            z-index: 1300;
            width: min(560px, 92vw);
            display: flex;
            flex-direction: column;
        }

        .upload-sidebar {
            z-index: 1305;
            width: min(50vw, 760px);
            min-width: 420px;
            display: flex;
            flex-direction: column;
        }

        .note-sidebar.open,
        .upload-sidebar.open {
            left: 0;
        }

        .note-sidebar-header,
        .upload-sidebar-header,
        .upload-header {
            padding: 16px;
            border-bottom: 1px solid var(--deal-border);
            background: #fff8e1;
        }

        .upload-sidebar-header,
        .upload-header {
            background: var(--deal-blue-soft);
        }

        .note-sidebar-body,
        .upload-sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 14px;
            background: #fafafa;
        }

        .note-sidebar-footer {
            position: sticky;
            bottom: 0;
            padding: 12px;
            border-top: 1px solid var(--deal-border);
            background: #fff;
        }

        .note-message {
            max-width: 88%;
            margin-bottom: 15px;
            padding: 12px 14px;
            border: 1px solid var(--deal-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--deal-shadow);
        }

        .note-message.own {
            margin-left: auto;
            background: #f4fef0;
        }

        #uploadBackdrop {
            position: fixed;
            inset: 0;
            z-index: 1299;
            display: none;
            background: rgba(0, 0, 0, .38);
        }

        #uploadBackdrop.show {
            display: block;
        }

        .dropzone {
            min-height: 150px;
            border: 2px dashed var(--deal-primary);
            border-radius: 18px;
            background: #fafafa;
        }

        .upload-sidebar .dropzone-custom.dz-drag-hover {
            background: rgba(143, 199, 62, .1);
            border-color: #28a745;
        }

        .upload-tools {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 12px;
            align-items: end;
        }

        .upload-tool-group {
            min-width: 0;
        }

        .upload-gallery {
            min-height: 80px;
        }

        .gallery-wrapper,
        .upload-gallery .gallery-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .gallery-item,
        .upload-gallery .gallery-item {
            width: 180px;
            overflow: hidden;
            border-radius: 12px;
            background: #fff;
            border: 1px solid var(--deal-border);
            transition: var(--deal-transition);
        }

        .gallery-item:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
        }

        .gallery-item img,
        .gallery-item .file-icon {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .gallery-controls,
        .upload-gallery .gallery-controls {
            padding: 10px;
        }

        /* ===== DROPDOWN MENU ===== */
        .dropdown-icon-wrapper {
            position: relative !important;
            z-index: 30;
        }

        .dropdown-icon-wrapper .dropdown-menu {
            display: none;
            min-width: 230px;
            padding: 8px 0;
            border: 1px solid var(--deal-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--deal-shadow-lg);
            z-index: 999999 !important;
        }

        .dropdown-menu.deal-dropdown-fixed {
            position: fixed !important;
            z-index: 999999 !important;
            display: block !important;
            min-width: 205px !important;
            max-width: 235px !important;
            padding: 8px 0;
            margin: 0 !important;
            border: 1px solid var(--deal-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--deal-shadow-lg);
        }

        .dropdown-icon-wrapper .dropdown-item,
        .dropdown-menu.deal-dropdown-fixed .dropdown-item {
            display: block;
            width: 100%;
            padding: 0;
            border: 0;
            background: transparent;
        }

        .dropdown-icon-wrapper .dropdown-item>a,
        .dropdown-menu.deal-dropdown-fixed .dropdown-item>a {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 11px !important;
            color: var(--deal-text) !important;
            text-decoration: none !important;
            font-size: 12px !important;
            font-weight: 700;
            white-space: nowrap;
        }

        .dropdown-icon-wrapper .dropdown-item>a:hover,
        .dropdown-menu.deal-dropdown-fixed .dropdown-item>a:hover {
            background: #f8fafc;
            color: var(--deal-text) !important;
        }

        .dropdown-menu.deal-dropdown-fixed i,
        .dropdown-icon-wrapper .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        /* ===== KANBAN ===== */
        .kanban-board {
            display: flex;
            flex-wrap: nowrap;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .kanban-column {
            min-width: 360px;
            flex-shrink: 0;
            border: 1px solid var(--deal-border);
            border-radius: 16px;
            background: var(--deal-soft);
            box-shadow: var(--deal-shadow);
        }

        .kanban-list {
            min-height: 100px;
            padding: 12px;
            position: relative;
        }

        .kanban-card {
            position: relative;
            z-index: 1;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--deal-shadow);
        }

        .kanban-card.ui-sortable-helper,
        .kanban-item.ui-sortable-helper {
            z-index: 9999 !important;
            background: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, .25);
        }

        .kanban-item {
            cursor: move;
            transition: var(--deal-transition);
        }

        .kanban-item:hover {
            box-shadow: 0 0 8px rgba(0, 0, 0, .15);
        }

        .kanban-item.dragging {
            z-index: 999;
            opacity: .9;
            transform: rotate(2deg) scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .15);
        }

        .kanban-placeholder {
            min-height: 60px;
            margin: 10px 0;
            border: 2px dashed #ccc;
        }

        /* ===== PROJECT PLANNING ===== */
        .project-planning-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1390;
            display: none;
            background: rgba(15, 23, 42, .45);
        }

        .project-planning-backdrop.show {
            display: block;
        }

        .project-planning-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            z-index: 1400;
            width: 80vw;
            max-width: 1280px;
            min-width: 760px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--deal-border);
            background: #fff;
            box-shadow: -18px 0 45px rgba(15, 23, 42, .18);
            transition: right .28s ease;
        }

        .project-planning-sidebar.open {
            right: 0;
        }

        .project-planning-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--deal-border);
            background: #f8fafc;
        }

        .project-planning-header h4 {
            margin: 0;
            color: var(--deal-text);
            font-size: 20px;
            font-weight: 900;
        }

        .project-planning-sub,
        .planning-small {
            color: var(--deal-muted);
            font-size: 12px;
        }

        .project-planning-sub {
            margin-top: 4px;
            font-weight: 700;
        }

        .project-planning-body {
            flex: 1;
            overflow-y: auto;
            padding: 22px;
        }

        .planning-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 14px;
            align-items: end;
        }

        .planning-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 14px;
        }

        .planning-section {
            margin-top: 22px;
            padding: 16px;
            border: 1px solid var(--deal-border);
            border-radius: 16px;
            background: #fff;
        }

        .planning-section h5 {
            margin: 0 0 12px;
            font-size: 15px;
            font-weight: 900;
        }

        .planning-table {
            width: 100%;
            border-collapse: collapse;
        }

        .planning-table th,
        .planning-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eef2f7;
            font-size: 13px;
            vertical-align: top;
        }

        .planning-table th {
            background: #f8fafc;
            color: var(--deal-muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .planning-pill {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
        }

        .planning-pill.available {
            background: var(--deal-success-soft);
            color: #047857;
        }

        .planning-pill.busy {
            background: var(--deal-danger-soft);
            color: #b91c1c;
        }

        .planning-employee-choice {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .planning-employee-choice input {
            width: 16px;
            height: 16px;
            accent-color: var(--deal-primary);
        }

        /* ===== SMALL HELPERS ===== */
        .icon-toolbar .icon-action {
            padding: 6px;
            margin-right: 10px;
            border: 0;
            background: none;
            color: #444;
            font-size: 1.4rem;
        }

        .icon-toolbar .icon-action:last-child {
            margin-right: 0;
        }

        .icon-toolbar i {
            cursor: pointer;
            transition: var(--deal-transition);
        }

        .icon-toolbar .icon-action:hover i {
            transform: scale(1.3);
            color: #1f2937 !important;
        }

        .bg-highlight {
            background-color: rgba(142, 199, 62, .55) !important;
            animation: fadeOutHighlight 3s ease-in-out forwards;
        }

        @keyframes fadeOutHighlight {
            0% {
                background-color: #8fc73e38;
            }

            100% {
                background-color: #fff;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width:1360px) {
            .deal-table-wrap {
                overflow-x: auto;
            }

            .deal-table-head,
            .deal-item-row {
                min-width: 1120px !important;
                grid-template-columns:
                    30px 80px 145px 145px 112px 92px 108px 48px 54px 178px 48px;
            }
        }

        @media (max-width:1200px) {
            .deal-toolbar {
                flex-wrap: wrap;
                overflow-x: visible;
            }

            .deal-toolbar-left {
                min-width: 100%;
                flex-wrap: wrap;
            }

            .deal-toolbar-right {
                width: 100%;
                justify-content: flex-end;
                margin-top: 6px;
            }

            .deal-analytics {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width:1199px) {
            .deal-table-wrap {
                overflow-x: visible;
            }

            .deal-table-head {
                display: none;
            }

            .deal-list {
                padding-top: 0;
            }

            .deal-item-row {
                min-width: 0 !important;
                width: 100%;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .deal-cell {
                padding: 9px 10px;
                border: 1px solid #eef2f7;
                border-radius: 10px;
                background: #f9fafb;
            }

            .deal-cell-title {
                display: block;
            }

            .deal-cell:last-child {
                grid-column: 1 / -1;
            }

            .deal-actions {
                flex-wrap: wrap;
            }
        }

        @media (max-width:900px) {
            .deal-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .deal-wrap {
                padding-right: 24px;
            }

            .project-planning-sidebar {
                width: 100vw;
                min-width: 0;
            }

            .planning-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:768px) {

            .deal-filter-block,
            .deal-filter-block.search,
            .deal-filter-block.date {
                width: 100% !important;
                min-width: 100% !important;
                flex: 1 1 100% !important;
            }

            .deal-toolbar-left {
                flex-direction: column;
                align-items: stretch;
            }

            .deal-toolbar-right {
                justify-content: stretch;
            }

            .deal-toolbar-right .deal-btn-soft,
            .deal-toolbar-right .deal-btn,
            .deal-bulk-right .deal-select,
            .deal-bulk-right .deal-btn {
                width: 100%;
                max-width: 100% !important;
                flex: 1 1 auto;
            }

            .deal-bulk-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .deal-bulk-left,
            .deal-bulk-right {
                width: 100%;
            }

            .upload-sidebar {
                width: 100vw;
                min-width: 0;
            }

            .upload-tools {
                grid-template-columns: 1fr;
            }

            .gallery-item,
            .upload-gallery .gallery-item {
                width: calc(50% - 6px);
            }
        }

        @media (max-width:767px) {
            .deal-item-row {
                grid-template-columns: 1fr;
            }

            .deal-date-line,
            .deal-subt>div {
                white-space: normal;
            }

            .deal-id-badge {
                min-width: auto;
                height: 30px;
                font-size: 11px;
            }
        }

        @media (max-width:640px) {
            .deal-analytics {
                grid-template-columns: 1fr;
            }

            .deal-wrap {
                padding: 22px 16px;
            }

            .deal-title {
                font-size: 24px;
            }

            .deal-form-grid {
                grid-template-columns: 1fr;
            }

            .gallery-item,
            .upload-gallery .gallery-item {
                width: 100%;
            }
        }


    .dropdown-menu.deal-dropdown-fixed {
        position: fixed !important;
        z-index: 2147483000 !important;
        display: block !important;
        min-width: 235px !important;
        max-width: 280px !important;
        padding: 8px 0;
        border: 1px solid var(--deal-border, #e5e7eb);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .18);
    }
    .dropdown-menu.deal-dropdown-fixed .dropdown-item > a {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 9px 12px !important;
        color: var(--deal-text, #111827) !important;
        text-decoration: none !important;
        font-size: 12px !important;
        font-weight: 800;
        white-space: nowrap;
    }

        /* ===== Deal history sidebar ===== */
        .deal-history-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1410;
            display: none;
            background: rgba(15, 23, 42, .42);
        }

        .deal-history-backdrop.show {
            display: block;
        }

        .deal-history-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            z-index: 1420;
            width: min(520px, 94vw);
            height: 100vh;
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--deal-border, #e5e7eb);
            background: #fff;
            box-shadow: -18px 0 45px rgba(15, 23, 42, .18);
            transition: right .28s ease;
        }

        .deal-history-sidebar.open {
            right: 0;
        }

        .deal-history-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--deal-border, #e5e7eb);
            background: linear-gradient(180deg, #f4fae7 0%, #ffffff 100%);
        }

        .deal-history-title {
            margin: 0;
            color: #111827;
            font-size: 18px;
            font-weight: 900;
        }

        .deal-history-subtitle {
            margin-top: 3px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
        }

        .deal-history-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f8fafc;
        }

        .deal-history-item {
            display: grid;
            grid-template-columns: 38px minmax(0, 1fr);
            gap: 10px;
            margin-bottom: 12px;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #93c21c;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
        }

        .deal-history-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #f4fae7;
            color: #55720d;
        }

        .deal-history-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            color: #111827;
            font-size: 13px;
        }

        .deal-history-top span {
            flex: 0 0 auto;
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
        }

        .deal-history-text {
            margin-top: 5px;
            color: #374151;
            font-size: 12px;
            line-height: 1.45;
            white-space: normal;
            word-break: break-word;
        }


        .deal-history-reason {
            margin-top: 8px;
            padding: 9px 10px;
            border: 1px solid #d9ef9d;
            border-left: 4px solid #93c21c;
            border-radius: 12px;
            background: #f4fae7;
            color: #374151;
            font-size: 12px;
            line-height: 1.45;
            font-weight: 700;
        }
        .deal-history-reason strong {
            color: #55720d;
            font-weight: 900;
        }

        .deal-history-meta {
            margin-top: 8px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 800;
        }

        .deal-history-empty {
            padding: 28px;
            text-align: center;
            border: 1px dashed #d1d5db;
            border-radius: 16px;
            background: #fff;
            color: #6b7280;
            font-weight: 800;
        }

        .deal-history-loading {
            padding: 28px;
            text-align: center;
            color: #6b7280;
            font-weight: 800;
        }

    </style>
@endsection

@section('content')
    <div class="deal-wrap">
        <div class="deal-header">
            <div class="deal-titlebar">
                <div>
                    <div class="deal-title">{{ $pageTitle }}</div>
                    <div class="deal-sub">Verwalten Sie Aufträge, Status, Dateien, Notizen und Verantwortlichkeiten zentral.
                    </div>

                    <div class="deal-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                        <span>›</span>
                        <a href="{{ url('/new_lead_view') }}">Kunde</a>
                        <span>›</span>
                        <a href="{{ url('/deal_all_list') }}">Aufträge</a>
                        <span>›</span>
                        <span class="current">
                            @if(Route::currentRouteName() == 'deal.junk.list')
                                JUNK
                            @elseif(Route::currentRouteName() == 'deal.all.list')
                                ALLE
                            @elseif(Route::currentRouteName() == 'deal.delete.list')
                                GELÖSCHTE
                            @else
                                Neue
                            @endif
                        </span>
                    </div>
                </div>

                <div>
                    <button type="button" class="deal-btn" onclick="openDealModal('dealModalCustom')">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        Erstellen
                    </button>
                </div>
            </div>
        </div>

        <div class="deal-analytics">
            <div class="deal-stat">
                <div class="deal-stat-icon total">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 12h18M3 6h18M3 18h18" />
                    </svg>
                </div>
                <div class="deal-stat-meta">
                    <div class="deal-stat-label">Gesamt</div>
                    <div class="deal-stat-value">{{ $totalCount }}</div>
                    <div class="deal-stat-sub">Alle Einträge</div>
                </div>
            </div>

            <div class="deal-stat">
                <div class="deal-stat-icon open">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 3"></path>
                    </svg>
                </div>
                <div class="deal-stat-meta">
                    <div class="deal-stat-label">Offen</div>
                    <div class="deal-stat-value">{{ $openCount }}</div>
                    <div class="deal-stat-sub">Noch offen</div>
                </div>
            </div>

            <div class="deal-stat">
                <div class="deal-stat-icon confirm">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </div>
                <div class="deal-stat-meta">
                    <div class="deal-stat-label">Bestätigt</div>
                    <div class="deal-stat-value">{{ $confirmCount }}</div>
                    <div class="deal-stat-sub">Status confirm</div>
                </div>
            </div>

            <div class="deal-stat">
                <div class="deal-stat-icon inconfirm">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="deal-stat-meta">
                    <div class="deal-stat-label">Unbestätigt</div>
                    <div class="deal-stat-value">{{ $inconfirmCount }}</div>
                    <div class="deal-stat-sub">Status inconfirm</div>
                </div>
            </div>

            <div class="deal-stat">
                <div class="deal-stat-icon value">
                    {{-- CI 2026-07-15: Dollar-Icon durch Euro ersetzt (deutsches System, Beträge in EUR). --}}
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 10h12M4 14h9M19 6a7.7 7.7 0 0 0-5.2-2A7.9 7.9 0 0 0 6 12c0 4.4 3.5 8 7.8 8 2 0 3.8-.8 5.2-2" />
                    </svg>
                </div>
                <div class="deal-stat-meta">
                    <div class="deal-stat-label">Auftragssumme</div>
                    <div class="deal-stat-value">{{ number_format($totalValue, 0, ',', '.') }} €</div>
                    <div class="deal-stat-sub">Gesamtwert der Liste</div>
                </div>
            </div>
        </div>

        <form action="{{ url()->current() }}" method="GET" class="deal-toolbar">
            <div class="deal-toolbar-left">
                <div class="deal-filter-block search" style="min-width:260px;">
                    <label class="deal-filter-label">Suche</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="deal-input"
                        placeholder="Kunde, Produkt, Ort, Angebotsnummer ...">
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Status</label>
                    <select name="status" class="deal-select select2-filter">
                        <option value="">Alle Unterphasen</option>
                        @foreach($dealWorkflowStages as $workflowStage)
                            <option value="{{ $workflowStage->key }}" {{ request('status') == $workflowStage->key ? 'selected' : '' }}>
                                {{ $workflowStage->label ?? $workflowStage->name ?? $workflowStage->key }}
                            </option>
                        @endforeach
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Abgeschlossen
                        </option>
                        <option value="Junk" {{ request('status') == 'Junk' ? 'selected' : '' }}>Junk</option>
                    </select>
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Mitarbeiter</label>
                    <select name="employee_filter" class="deal-select select2-filter">
                        <option value="">Alle Mitarbeiter</option>
                        <option value="mine" {{ request('employee_filter') == 'mine' ? 'selected' : '' }}>Meine</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ (string) request('employee_filter') === (string) $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} {{ $employee->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Artikelgruppe</label>
                    <select name="product_id" class="deal-select select2-filter">
                        <option value="">Alle Artikelgruppen</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ (string) request('product_id') === (string) $product->id ? 'selected' : '' }}>
                                {{ $product->article_group }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Abteilung</label>
                    <select name="department_id" class="deal-select select2-filter">
                        <option value="">Alle Abteilungen</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ (string) request('department_id') === (string) $department->id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Datum von</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="deal-input">
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Datum bis</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="deal-input">
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Ansicht</label>
                    <select name="filter" id="filter" class="deal-select select2-filter">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Alle Angebote</option>
                        <option value="my" {{ request('filter', 'my') == 'my' ? 'selected' : '' }}>Meine Angebote</option>
                    </select>
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Sortieren nach</label>
                    <select name="sort_by" class="deal-select select2-filter">
                        <option value="latest_change" {{ request('sort_by', 'latest_change') == 'latest_change' ? 'selected' : '' }}>Neueste Änderung</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Erstellt am</option>
                        <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Auftrag geändert</option>
                        <option value="customer" {{ request('sort_by') == 'customer' ? 'selected' : '' }}>Kunde</option>
                        <option value="product" {{ request('sort_by') == 'product' ? 'selected' : '' }}>Produkt</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Kanban Status</option>
                        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Auftragssumme</option>
                        <option value="city" {{ request('sort_by') == 'city' ? 'selected' : '' }}>Ort</option>
                    </select>
                </div>

                <div class="deal-filter-block">
                    <label class="deal-filter-label">Richtung</label>
                    <select name="sort_dir" class="deal-select select2-filter">
                        <option value="desc" {{ request('sort_dir', 'desc') == 'desc' ? 'selected' : '' }}>Neueste zuerst</option>
                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Älteste zuerst</option>
                    </select>
                </div>
            </div>

            <div class="deal-toolbar-right">
                <button class="deal-btn-soft" type="submit">Suchen</button>

                @if(request()->hasAny(['search', 'status', 'filter', 'employee_filter', 'product_id', 'department_id', 'date_from', 'date_to', 'sort_by', 'sort_dir']))
                    <a href="{{ url()->current() }}" class="deal-btn-soft">Zurücksetzen</a>
                @endif
            </div>
        </form>

        <div class="deal-bulk-toolbar" id="dealBulkToolbar">
            <div class="deal-bulk-left">
                <label class="deal-check-label">
                    <input type="checkbox" id="selectAllDeals">
                    <span>Alle auswählen</span>
                </label>

                <span class="deal-selected-count">
                    <strong id="selectedDealsCount">0</strong> ausgewählt
                </span>
            </div>

            <div class="deal-bulk-right">
                <select id="bulkAction" class="deal-select" style="max-width:180px;">
                    <option value="">Aktion wählen</option>
                    <option value="delete">Bulk löschen</option>
                    <option value="junk">Bulk Junk</option>
                    <option value="unjunk">Bulk Un-Junk</option>
                    <option value="restore">Bulk wiederherstellen</option>
                    <option value="status">Status ändern</option>
                </select>

                <select id="bulkStatus" class="deal-select" style="max-width:180px; display:none;">
                    <option value="">Unterphase wählen</option>
                    @foreach($dealWorkflowStages as $workflowStage)
                        <option value="{{ $workflowStage->key }}">
                            {{ $workflowStage->label ?? $workflowStage->name ?? $workflowStage->key }}
                        </option>
                    @endforeach
                    <option value="complete">Abgeschlossen</option>
                    <option value="Junk">Junk</option>
                </select>

                <button type="button" class="deal-btn" id="runBulkAction">
                    Ausführen
                </button>
            </div>
        </div>

        <div class="deal-card">
            <div class="deal-tabs">
                <button type="button" class="deal-tab-link" data-tab-target="deal-kanban-pane">Kanban</button>
                <button type="button" class="deal-tab-link active" data-tab-target="deal-list-pane">Liste</button>
            </div>

            <div class="deal-tab-content">
                <div class="deal-tab-pane" id="deal-kanban-pane" style="display:none;">
                    @include('admin.deal.partials.kanban')
                </div>

                <div class="deal-tab-pane active" id="deal-list-pane">
                    <style>
                        /* ===== Deal no-overlap card list ===== */
                        .deal-list-card-wrap{
                            padding:12px;
                            background:#f8fafc;
                            border-radius:18px;
                        }
                        .deal-list-card-head{
                            display:grid;
                            grid-template-columns: 32px 1.7fr 1.2fr 220px 106px;
                            gap:12px;
                            align-items:center;
                            padding:10px 12px;
                            color:#6b7280;
                            font-size:10px;
                            font-weight:900;
                            text-transform:uppercase;
                            letter-spacing:.05em;
                        }
                        .deal-list-card-stack{
                            display:flex;
                            flex-direction:column;
                            gap:10px;
                        }
                        .deal-row-card{
                            position:relative;
                            display:grid;
                            grid-template-columns: 32px minmax(0, 1.75fr) minmax(0, 1.12fr) 220px 106px;
                            gap:12px;
                            align-items:stretch;
                            padding:12px;
                            border:1px solid var(--deal-border, #e5e7eb);
                            border-left:4px solid var(--row-status-color, #93c21c);
                            border-radius:16px;
                            background:#fff;
                            box-shadow:0 1px 2px rgba(15,23,42,.04);
                            transition:.18s ease;
                        }
                        .deal-row-card:hover{
                            border-color:#d1d5db;
                            box-shadow:0 10px 24px rgba(15,23,42,.08);
                            transform:translateY(-1px);
                            z-index:20;
                        }
                        .deal-row-select{display:flex;align-items:center;justify-content:center;}
                        .deal-row-main{min-width:0;display:flex;flex-direction:column;gap:8px;}
                        .deal-row-top{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;min-width:0;}
                        .deal-row-titlebox{min-width:0;}
                        .deal-row-no{display:inline-flex;align-items:center;height:24px;padding:0 8px;border-radius:999px;background:#eff6ff;color:#74b2d4;font-size:10px;font-weight:900;white-space:nowrap;}
                        .deal-row-customer{display:flex;align-items:center;gap:7px;min-width:0;}
                        .deal-row-customer .deal-ttl{font-size:14px;line-height:1.2;margin:0;min-width:0;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-row-location{margin-top:3px;color:#6b7280;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-row-update{display:flex;align-items:center;gap:6px;min-width:0;}
                        .deal-update-badge{display:inline-flex;align-items:center;gap:4px;max-width:150px;padding:3px 7px;border-radius:999px;border:1px solid #d9ef9d;background:#f4fae7;color:#55720d;font-size:9px;font-weight:900;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-update-badge.fresh{background:#93c21c;color:#fff;border-color:#93c21c;animation:dealFreshPulse 2s infinite;}
                        .deal-latest-change-line{min-width:0;color:#55720d;font-size:10px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-row-meta{display:grid;grid-template-columns: repeat(4, minmax(0,1fr));gap:6px;}
                        .deal-mini{min-width:0;padding:7px 9px;border:1px solid #eef2f7;border-radius:11px;background:#f9fafb;}
                        .deal-mini-label{display:block;margin-bottom:2px;color:#9ca3af;font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.04em;}
                        .deal-mini-value{display:block;color:#111827;font-size:11px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-row-product{min-width:0;display:flex;align-items:center;gap:10px;padding:9px 10px;border:1px solid #eef2f7;border-radius:13px;background:#fbfdff;}
                        .deal-service-badge{width:34px;height:34px;flex:0 0 34px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:#93c21c;color:#fff;font-size:12px;font-weight:900;}
                        .deal-profile{width:32px;height:32px;flex:0 0 32px;border:2px solid #93c21c;border-radius:999px;object-fit:cover;}
                        .deal-product-text{min-width:0;}
                        .deal-product-text .deal-ttl{font-size:13px;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-product-text .deal-subt{font-size:10px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-row-status{min-width:0;display:flex;flex-direction:column;justify-content:center;gap:7px;padding:9px 10px;border:1px solid color-mix(in srgb, var(--row-status-color, #93c21c) 28%, #e5e7eb);border-radius:13px;background:color-mix(in srgb, var(--row-status-color, #93c21c) 7%, #fff);}
                        .deal-status-caption{display:flex;align-items:center;justify-content:space-between;gap:6px;color:#6b7280;font-size:9px;font-weight:900;text-transform:uppercase;}
                        .deal-inline-status-select{width:100%;min-width:0;height:36px;border-radius:999px;border:1px solid color-mix(in srgb, var(--row-status-color, #93c21c) 55%, #fff);background:#fff;color:var(--row-status-color, #55720d);font-size:11px;font-weight:900;padding:0 9px;outline:none;cursor:pointer;}
                        .deal-inline-status-select:focus{box-shadow:0 0 0 3px rgba(147,194,28,.18);border-color:#93c21c;}
                        .deal-status-meta{color:#6b7280;font-size:10px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                        .deal-row-actions{display:flex;align-items:center;justify-content:flex-end;gap:6px;min-width:0;}
                        .deal-row-actions .deal-btn-ic{width:32px;height:32px;min-width:32px;}
                        .deal-action-cluster{display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:flex-end;}
                        .deal-row-actions .dropdown-menu{z-index:999999;}
                        .deal-badge-count{position:absolute;top:-6px;right:-6px;min-width:16px;height:16px;display:flex;align-items:center;justify-content:center;padding:0 4px;border:2px solid #fff;border-radius:999px;background:#ef4444;color:#fff;font-size:9px;font-weight:900;}
                        @keyframes dealFreshPulse{0%{box-shadow:0 0 0 0 rgba(147,194,28,.42)}70%{box-shadow:0 0 0 7px rgba(147,194,28,0)}100%{box-shadow:0 0 0 0 rgba(147,194,28,0)}}
                        @media (max-width: 1380px){
                            .deal-list-card-head{display:none;}
                            .deal-row-card{grid-template-columns: 30px minmax(0,1fr) 220px 104px;}
                            .deal-row-product{grid-column:2 / 3;}
                            .deal-row-status{grid-column:3 / 4;grid-row:1 / span 2;}
                            .deal-row-actions{grid-column:4 / 5;grid-row:1 / span 2;align-items:center;}
                            .deal-row-meta{grid-template-columns:repeat(3,minmax(0,1fr));}
                        }
                        @media (max-width: 992px){
                            .deal-row-card{grid-template-columns:1fr;}
                            .deal-row-select{position:absolute;top:14px;right:14px;}
                            .deal-row-top{padding-right:38px;}
                            .deal-row-product,.deal-row-status,.deal-row-actions{grid-column:auto;grid-row:auto;}
                            .deal-row-actions{justify-content:flex-start;}
                            .deal-row-meta{grid-template-columns:repeat(2,minmax(0,1fr));}
                        }
                        @media (max-width: 560px){.deal-row-meta{grid-template-columns:1fr}.deal-row-top{flex-direction:column}.deal-update-badge{max-width:100%;}.deal-row-actions{align-items:stretch}.deal-action-cluster{justify-content:flex-start}.deal-inline-status-select{height:40px}}
                    </style>

                    <div class="deal-list-card-wrap">
                        <div class="deal-list-card-head">
                            <div><input type="checkbox" id="selectAllDealsHead"></div>
                            <div>Kunde / Änderung</div>
                            <div>Produkt / Verantwortlich</div>
                            <div>Kanban Status</div>
                            <div style="text-align:right;">Aktionen</div>
                        </div>

                        <div class="deal-list-card-stack">
                            @forelse($data as $item)
                                @php
                                    $services = [
                                        'complete' => 'Komplettlösung',
                                        'montage' => 'Montage',
                                        'product' => 'Produkt',
                                        'plan' => 'Planung',
                                        'maintenance' => 'Wartung',
                                        'repair' => 'Reparatur',
                                        'others' => 'Sonstiges',
                                    ];

                                    $service = $services[$item->service] ?? ($item->service ?? 'Nicht gesetzt');
                                    $gender = $item->emp_gender ?? $item->gender ?? null;
                                    $defaultImage = $gender === 'Male' ? asset('images/gender/male.png') : asset('images/gender/female.png');
                                    $employeeImage = (!empty($item->emp_image) && file_exists(public_path('images/employee/' . $item->emp_image)))
                                        ? asset('images/employee/' . $item->emp_image)
                                        : $defaultImage;

                                    $checkedBy = DB::table('employees')->where('id', $item->checked_by)->select('name', 'lastname')->first();
                                    $reviewedBy = DB::table('employees')->where('id', $item->reviewer_id)->select('name', 'lastname')->first();

                                    $statusLabel = $dealWorkflowLabelMap[$item->status] ?? match ($item->status) {
                                        'complete' => 'Abgeschlossen',
                                        'Junk' => 'Junk',
                                        default => ucfirst(str_replace('_', ' ', (string) $item->status)),
                                    };
                                    $statusColor = $dealWorkflowColorMap[$item->status] ?? match ($item->status) {
                                        'confirm', 'complete' => '#10b981',
                                        'inconfirm', 'Junk', 'cancel' => '#ef4444',
                                        'open', 'pause' => '#f59e0b',
                                        default => '#93c21c',
                                    };

                                    $latestChangeAt = $item->latest_change_at ?? $item->updated_at ?? $item->created_at ?? null;
                                    $latestChangeSource = $item->latest_change_source ?? 'Auftrag';
                                    $latestChangeText = $item->latest_change_text ?? 'Auftrag wurde geändert';
                                    $isFreshChange = $latestChangeAt ? \Carbon\Carbon::parse($latestChangeAt)->greaterThanOrEqualTo(now()->subDays(3)) : false;

                                    $auftragNo = $item->order_number ?? $item->deal_no ?? $item->offer_number ?? ('#' . $item->id);
                                    $customerDisplayName = trim(($item->name ?? '') . ' ' . ($item->lastname ?? '')) ?: ($item->firma ?? 'Unbekannter Kunde');
                                    $priceValue = is_numeric($item->price ?? null) ? number_format((float) $item->price, 2, ',', '.') . ' €' : ($item->price ?? 'unbekannt');
                                    $createdDate = !empty($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d.m.Y') : '–';
                                    $signDate = $item->sign_date ?: '–';
                                    $confirmedDate = $item->confirmed_at ?: '–';

                                    $offerRecord = null;
                                    $offerDetailId = null;
                                    $folderId = $item->offer_folder_id ?? null;

                                    if (!empty($item->offer_id)) {
                                        $offerRecord = DB::table('offers')->select('id')->where('id', $item->offer_id)->first();
                                    }

                                    if (!$offerRecord) {
                                        $offerRecord = DB::table('offers')
                                            ->select('id')
                                            ->where('customer_id', $item->customer_id)
                                            ->where('product_id', $item->product_id)
                                            ->where('alternative_id', $item->alternative_id)
                                            ->orderByDesc('id')
                                            ->first();
                                    }

                                    if ($offerRecord) {
                                        $detail = DB::table('offer_details')->select('id', 'offer_folder_id')->where('offer_id', $offerRecord->id)->orderByDesc('id')->first();
                                        $offerDetailId = $detail->id ?? null;
                                        $folderId = $folderId ?: ($detail->offer_folder_id ?? null);
                                    }

                                    // Rechte ueber das reparierte Muster (User::hasPermission -> user_rolls
                                    // ueber users.id, Flag=1, is_admin-Bypass) statt des alten name/'on'-Musters.
                                    $authUser = auth()->user();
                                    $canUpdateCustomer = $authUser ? $authUser->hasPermission('Customer', 'update') : false;
                                    $canDeleteCustomer = $authUser ? $authUser->hasPermission('Customer', 'delete') : false;
                                @endphp

                                <div id="deal-{{ $item->id }}" class="deal-item deal-row-card" style="--row-status-color: {{ $statusColor }};">
                                    <div class="deal-row-select">
                                        <input type="checkbox" class="deal-row-checkbox" value="{{ $item->id }}">
                                    </div>

                                    <div class="deal-row-main">
                                        <div class="deal-row-top">
                                            <div class="deal-row-titlebox">
                                                <a href="{{ route('deal.profile', $item->id) }}" class="deal-link" title="Auftrag Profil öffnen">
                                                    <div class="deal-row-customer">
                                                        <span class="deal-row-no">{{ $auftragNo }}</span>
                                                        <span class="deal-ttl">{{ $customerDisplayName }}</span>
                                                    </div>
                                                    <div class="deal-row-location">
                                                        <i class="feather icon-map-pin"></i>
                                                        {{ $item->city ?? 'Ort unbekannt' }}
                                                        @if(!empty($item->postcode)) · {{ $item->postcode }} @endif
                                                    </div>
                                                </a>
                                            </div>

                                            @if($latestChangeAt)
                                                <div class="deal-row-update">
                                                    <span class="deal-update-badge {{ $isFreshChange ? 'fresh' : '' }}">
                                                        {{ $isFreshChange ? 'Neu' : 'Update' }} · {{ $latestChangeSource }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="deal-subt">
                                            @if($latestChangeAt)
                                                <div class="deal-latest-change-line">
                                                    <i class="feather icon-zap"></i>
                                                    <span>{{ $latestChangeText }} · {{ \Carbon\Carbon::parse($latestChangeAt)->format('d.m.Y H:i') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="deal-row-meta">
                                            <div class="deal-mini">
                                                <span class="deal-mini-label">Summe</span>
                                                <span class="deal-mini-value editable-cell price-cell" data-field="price" data-id="{{ $item->id }}">{{ $priceValue }}</span>
                                            </div>
                                            <div class="deal-mini">
                                                <span class="deal-mini-label">Erstellt</span>
                                                <span class="deal-mini-value">{{ $createdDate }}</span>
                                            </div>
                                            <div class="deal-mini">
                                                <span class="deal-mini-label">Sign / Best.</span>
                                                <span class="deal-mini-value">{{ $signDate }} / {{ $confirmedDate }}</span>
                                            </div>
                                            <div class="deal-mini">
                                                <span class="deal-mini-label">Prüfung</span>
                                                <span class="deal-mini-value">
                                                    {{ $checkedBy ? trim($checkedBy->name . ' ' . $checkedBy->lastname) : 'Nicht definiert' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="deal-row-product">
                                        <div class="deal-service-badge">{{ $item->initial ?? '?' }}</div>
                                        <img src="{{ $employeeImage }}" alt="Profile" class="deal-profile" data-toggle="tooltip" data-original-title="{{ ($item->emp_name ?? null) && ($item->emp_lastname ?? null) ? $item->emp_name . ' ' . $item->emp_lastname : 'Nicht zugewiesen' }}">
                                        <div class="deal-product-text">
                                            <div class="deal-ttl">{{ $item->article_group ?? 'Produkt' }}</div>
                                            <div class="deal-subt">{{ $service }}</div>
                                            @if($reviewedBy)
                                                <div class="deal-subt">Review: {{ $reviewedBy->name }} {{ $reviewedBy->lastname }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="deal-row-status">
                                        <div class="deal-status-caption">
                                            <span>Kanban Status</span>
                                            <i class="feather icon-refresh-cw"></i>
                                        </div>
                                        <select class="deal-inline-status-select js-deal-list-status" data-deal-id="{{ $item->id }}" data-current-status="{{ $item->status }}">
                                            @foreach($dealWorkflowStages as $workflowStage)
                                                <option value="{{ $workflowStage->key }}" @selected((string) $workflowStage->key === (string) $item->status)>
                                                    {{ $workflowStage->label ?? $workflowStage->name ?? $workflowStage->key }}
                                                </option>
                                            @endforeach
                                            <option value="complete" @selected((string) $item->status === 'complete')>Abgeschlossen</option>
                                            <option value="Junk" @selected((string) $item->status === 'Junk')>Junk</option>
                                        </select>
                                        <div class="deal-status-meta">{{ $latestChangeAt ? \Carbon\Carbon::parse($latestChangeAt)->diffForHumans() : $statusLabel }}</div>
                                    </div>

                                    <div class="deal-row-actions">
                                        <div class="deal-action-cluster">
                                            <button type="button" class="deal-btn-ic warning open-notes-sidebar position-relative"
                                                data-deal-id="{{ $item->id }}"
                                                data-customer-id="{{ $item->customer_id }}"
                                                data-alternative-id="{{ $item->alternative_id }}"
                                                data-product-id="{{ $item->product_id }}"
                                                title="Notizen">
                                                <i class="fa fa-sticky-note-o"></i>
                                                @if(($item->notes_count ?? 0) > 0)
                                                    <span class="deal-badge-count">{{ $item->notes_count }}</span>
                                                @endif
                                            </button>

                                            <button type="button" class="deal-btn-ic primary open-upload-sidebar position-relative"
                                                data-customer-id="{{ $item->customer_id }}"
                                                data-alternative-id="{{ $item->alternative_id }}"
                                                data-product-id="{{ $item->product_id }}"
                                                data-item-id="{{ $item->id }}"
                                                title="Dokumente">
                                                <i class="fa fa-picture-o"></i>
                                                @if(($item->files_count ?? 0) > 0)
                                                    <span class="deal-badge-count">{{ $item->files_count }}</span>
                                                @endif
                                            </button>

                                            <a href="{{ route('deal.profile', $item->id) }}" class="deal-btn-ic success" title="Auftrag Profil">
                                                <i class="feather icon-file-text"></i>
                                            </a>

                                            <div class="btn-group dropup dropdown-icon-wrapper">
                                                <button type="button" class="deal-btn-ic deal-menu-toggle" data-deal-menu-toggle="1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Menü">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <span class="dropdown-item"><a href="{{ url('new_lead_profile/' . $item->customer_id) }}" class="text-primary"><i class="feather icon-user primary"></i> Kundenprofil</a></span>
                                                    @if(!empty($folderId))
                                                        <span class="dropdown-item"><a href="{{ route('admin.offers.folders.show', $folderId) }}" class="text-primary"><i class="feather icon-folder primary"></i> Angebot Ordner</a></span>
                                                    @endif
                                                    @if(!empty($offerDetailId))
                                                        <span class="dropdown-item"><a href="{{ route('deal.material.list', $offerDetailId) }}" class="text-success"><i class="feather icon-layers"></i> Materialliste</a></span>
                                                    @endif
                                                    @if($canUpdateCustomer)
                                                        <span class="dropdown-item"><a href="{{ url('/deal_delete/' . $item->id) }}" class="text-danger" onclick="return confirm('Auftrag wirklich löschen?')"><i class="feather icon-trash danger"></i> Löschen</a></span>
                                                    @endif
                                                    @if($canDeleteCustomer)
                                                        @if($item->status !== 'Junk')
                                                            <span class="dropdown-item"><a href="{{ url('/deal_junk/' . $item->id) }}" class="text-danger" onclick="return confirm('Auftrag als Junk setzen?')"><i class="fa fa-power-off danger"></i> Junk</a></span>
                                                        @else
                                                            <span class="dropdown-item"><a href="{{ url('/deal_unjunk/' . $item->id) }}" class="text-primary" onclick="return confirm('Auftrag aus Junk wiederherstellen?')"><i class="fa fa-power-off primary"></i> Un-Junk</a></span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gallery-container mt-2 d-flex" id="gallery-container-{{ $item->id }}"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="deal-empty">Keine Aufträge gefunden.</div>
                            @endforelse
                        </div>
                    </div>


                                            @if($isPaginator && method_exists($data, 'links') && $data->hasPages())
                                                <div class="deal-pagination">
                                                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                                                        <div style="font-size:12px;color:#6b7280;">
                                                            Zeige <strong>{{ $data->firstItem() ?? 0 }}</strong>
                                                            bis <strong>{{ $data->lastItem() ?? 0 }}</strong>
                                                            von <strong>{{ $data->total() }}</strong> Einträgen
                                                        </div>

                                                        <div>
                                                            {{ $data->appends(request()->input())->onEachSide(1)->links('pagination::bootstrap-4') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="deal-modal-backdrop" id="dealModalCustom">
        <div class="deal-modal">
            <div class="deal-modal-h">
                <h5 class="deal-modal-ttl">Neues Projekt erstellen</h5>
                <button type="button" class="deal-btn-ic" onclick="closeDealModal('dealModalCustom')">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('deal.store') }}" id="dealStore">
                @csrf
                <div class="deal-modal-b">
                    <div class="deal-form-grid">
                        <div class="deal-form-group">
                            <label class="deal-label">Kunde</label>
                            <select name="customer_id" id="customer_id" class="deal-select-form select2" required>
                                <option value="">-- Wähle Kunde --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }} {{ $cust->lastname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="deal-form-group">
                            <label class="deal-label">Produkt</label>
                            <select name="lead_product_list_id" id="product_list_id" class="deal-select-form select2"
                                required>
                                <option value="">-- Wähle Produkt --</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="alternative_id" id="alternative_id">
                    <input type="hidden" name="department_id" id="department_id">
                    <input type="hidden" name="service_id" id="service_id">
                    <input type="hidden" name="employee_id" id="employee_id">
                    <input type="hidden" name="service" id="service_str">
                </div>

                <div class="deal-modal-f">
                    <button type="button" class="deal-btn-soft"
                        onclick="closeDealModal('dealModalCustom')">Abbrechen</button>
                    <button type="submit" class="deal-btn">Projekt erstellen</button>
                </div>
            </form>
        </div>
    </div>

    <div id="noteSidebar" class="note-sidebar">
        <div class="note-sidebar-header">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <strong>Notizen</strong>
                <button type="button" id="closeSidebar" class="btn btn-sm btn-danger">✕</button>
            </div>

            <input type="text" id="searchNotes" placeholder="Suche Notizen..." class="form-control">
        </div>

        <div id="notesList" class="note-sidebar-body">
        </div>

        <div class="note-sidebar-footer">
            <textarea id="newNoteContent" class="form-control" rows="3" placeholder="Neue Notiz schreiben..."></textarea>

            <button id="sendNote" type="button" class="btn btn-success btn-sm mt-2 w-100">
                Senden
            </button>
        </div>
    </div>


    <div id="dealHistoryBackdrop" class="deal-history-backdrop"></div>
    <aside id="dealHistorySidebar" class="deal-history-sidebar" aria-label="Auftrag Historie">
        <div class="deal-history-header">
            <div>
                <h4 class="deal-history-title">Auftrag Historie</h4>
                <div class="deal-history-subtitle" id="dealHistorySubtitle">Alle Änderungen, Statuswechsel, Dokumente und Feinaufmaß</div>
            </div>
            <button type="button" class="deal-btn-ic danger" id="closeDealHistorySidebar" title="Schließen">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="dealHistoryBody" class="deal-history-body">
            <div class="deal-history-loading">Historie laden...</div>
        </div>
    </aside>

    <div id="uploadSidebar" class="upload-sidebar">
        <div class="upload-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="mb-0">Datei-Upload</h5>
            <button class="btn btn-sm btn-danger" id="closeUploadSidebar"><i class="fa fa-times"></i></button>
        </div>

        <form action="{{ url('customer_upload') }}" method="POST" id="dealUploadDropzone"
            class="dropzone dropzone-custom m-3" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="customer_id" id="uploadCustomerId">
            <input type="hidden" name="alternative_id" id="uploadAlternativeId">
            <input type="hidden" name="product_id" id="uploadProductId">
            <input type="hidden" name="status" value="deal">
            <input type="hidden" name="stage_id" id="uploadStage">
            <input type="hidden" name="deal_id" id="uploadDealId">
            <input type="hidden" name="offer_folder_id" id="uploadOfferFolderId">

            <div class="p-2">
                <label for="uploadStage">Stufe:</label>
                <select class="form-control form-control-sm" name="stage_id">
                    <option value="">-- wählen --</option>
                    <option value="order">Kundenauftrag</option>
                    <option value="confirmed_order">Auftragsbestätigung</option>
                    <option value="offer">Angebot</option>
                </select>
            </div>
        </form>

        <div class="upload-gallery px-3 pb-3"></div>
    </div>

    <div id="uploadBackdrop"></div>
    <div class="modal fade" id="deliveryNotesModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header" style="background:#ecfdf5;">
                    <h5 class="modal-title">
                        <i class="feather icon-truck"></i>
                        Lieferscheine für Auftrag
                        <span id="deliveryNotesOrderNumber"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>×</span>
                    </button>
                </div>

                <div class="modal-body" id="deliveryNotesModalBody">
                    <div class="text-muted py-4 text-center">Lade Lieferscheine...</div>
                </div>
            </div>
        </div>
    </div>


    <div id="projectPlanningBackdrop" class="project-planning-backdrop"></div>

    <div id="projectPlanningSidebar" class="project-planning-sidebar">
        <div class="project-planning-header">
            <div>
                <h4>Projekt planen</h4>
                <div class="project-planning-sub" id="planningOrderNumber">Auftrag</div>
            </div>

            <button type="button" class="deal-btn-ic" id="closeProjectPlanningSidebar">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="project-planning-body">
            <form id="projectPlanningForm">
                @csrf

                <input type="hidden" id="planningDealId">

                <div class="planning-grid">
                    <div>
                        <label class="deal-label">Projektname</label>
                        <input type="text" id="planningTitle" class="deal-input-form" required
                            placeholder="z.B. Montage Projekt">
                    </div>

                    <div>
                        <label class="deal-label">Datum</label>
                        <input type="date" id="planningDate" class="deal-input-form" required>
                    </div>

                    <div>
                        <label class="deal-label">Startzeit</label>
                        <input type="time" id="planningStartTime" class="deal-input-form" required value="08:00">
                    </div>

                    <div>
                        <label class="deal-label">Endzeit</label>
                        <input type="time" id="planningEndTime" class="deal-input-form" required value="16:00">
                    </div>
                </div>

                <div class="planning-actions">
                    <button type="button" class="deal-btn-soft" id="checkPlanningAvailability">
                        Verfügbarkeit prüfen
                    </button>

                    <button type="submit" class="deal-btn" id="saveProjectPlanning">
                        Projekt speichern
                    </button>
                </div>
            </form>

            <div class="planning-section">
                <h5>Benötigte Qualifikationen</h5>
                <div id="planningQualifications">
                    <div class="text-muted">Noch keine Daten geladen.</div>
                </div>
            </div>

            <div class="planning-section">
                <h5>Smart Mitarbeiter-Vorschläge</h5>
                <div id="planningSuggestions">
                    <div class="text-muted">Bitte zuerst Verfügbarkeit prüfen.</div>
                </div>
            </div>

            <div class="planning-section">
                <h5>Materialliste</h5>
                <div id="planningMaterialList">
                    <div class="text-muted">Noch keine Daten geladen.</div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;
        }
    </script>
    <script>
        (function () {
            'use strict';

            if (typeof Dropzone !== 'undefined') {
                Dropzone.autoDiscover = false;
            }
            const csrfToken = '{{ csrf_token() }}';
            const authEmployeeId = '{{ auth()->user()->name }}';

            let uploadSidebarParams = {};
            let currentNoteParams = {};
            let dealDropzoneInstance = null;

            const selectors = {
                dealModalBackdrop: '.deal-modal-backdrop',
                dealTabLink: '.deal-tab-link',
                dealTabPane: '.deal-tab-pane',
                editableCell: '.editable-cell',
                openUploadSidebar: '.open-upload-sidebar',
                closeUploadSidebar: '#closeUploadSidebar, #uploadBackdrop',
                uploadSidebar: '#uploadSidebar',
                uploadBackdrop: '#uploadBackdrop',
                uploadGallery: '.upload-gallery',
                filterStage: '#filterStage',
                fileSearchInput: '#fileSearchInput',
                renameInput: '.rename-input',
                deleteFile: '.delete-file',
                kanbanItem: '.kanban-item',
                kanbanList: '.kanban-list',
                openNotesSidebar: '.open-notes-sidebar',
                closeSidebar: '#closeSidebar',
                noteSidebar: '#noteSidebar',
                notesList: '#notesList',
                searchNotes: '#searchNotes',
                newNoteContent: '#newNoteContent',
                sendNote: '#sendNote',
                dealStore: '#dealStore',
                customerSelect: '#customer_id',
                productListSelect: '#product_list_id',
                productId: '#product_id',
                alternativeId: '#alternative_id',
                departmentId: '#department_id',
                serviceId: '#service_id',
                employeeId: '#employee_id',
                serviceStr: '#service_str',
                uploadCustomerId: '#uploadCustomerId',
                uploadAlternativeId: '#uploadAlternativeId',
                uploadProductId: '#uploadProductId',
                kanbanSearch: '#kanban-search',
                kanbanFilter: '#kanban-filter',
                kanbanStage: '#kanban-stage',
                kanbanProduct: '#kanban-product',
                uploadDealId: '#uploadDealId',
                uploadOfferFolderId: '#uploadOfferFolderId',
                uploadStageSelect: '#uploadStageSelect',
            };

            const labelMap = {
                sign_date: 'Signierungsdatum',
                confirmed_at: 'Bestätigt am',
                status: 'Status',
                price: 'Preis'
            };

            function qs(selector, scope = document) {
                return scope.querySelector(selector);
            }

            function qsa(selector, scope = document) {
                return Array.from(scope.querySelectorAll(selector));
            }

            function openDealModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.add('open');
            }

            function closeDealModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.remove('open');
            }

            window.openDealModal = openDealModal;
            window.closeDealModal = closeDealModal;

            function switchDealTab(tabTargetId) {
                qsa(selectors.dealTabLink).forEach(el => el.classList.remove('active'));
                qsa(selectors.dealTabPane).forEach(el => {
                    el.classList.remove('active');
                    el.style.display = 'none';
                });

                const tabBtn = qsa('[data-tab-target]').find(el => el.dataset.tabTarget === tabTargetId);
                const target = document.getElementById(tabTargetId);

                if (tabBtn) tabBtn.classList.add('active');
                if (target) {
                    target.classList.add('active');
                    target.style.display = '';
                }
            }

            function initSelect2() {
                if (!$.fn.select2) return;

                $('.select2').select2({
                    width: '100%'
                });

                $('.select2-filter').select2({
                    width: '100%',
                    allowClear: false
                });
            }
            function fillProductHiddenFields(optionElement) {
                const $selected = $(optionElement);

                $(selectors.productId).val($selected.data('product') ?? '');
                $(selectors.alternativeId).val($selected.data('alternative') ?? '');
                $(selectors.departmentId).val($selected.data('department') ?? '');
                $(selectors.serviceId).val($selected.data('service') ?? '');
                $(selectors.employeeId).val($selected.data('employee') ?? '');
                $(selectors.serviceStr).val($selected.data('service-str') ?? '');
            }

            function loadCustomerProductLists(customerId) {
                if (!customerId) {
                    $(selectors.productListSelect).html('<option value="">-- Wähle Produkt --</option>');
                    return;
                }

                $(selectors.productListSelect).html('<option value="">-- Lade Produkte --</option>');

                $.get(`/get-product-lists/${customerId}`, function (data) {
                    const $select = $(selectors.productListSelect);
                    $select.html('<option value="">-- Wähle Produkt --</option>');

                    data.forEach(d => {
                        $select.append(`
                                    <option value="${d.id}"
                                        data-product="${d.product_id ?? ''}"
                                        data-alternative="${d.alternative_id ?? ''}"
                                        data-department="${d.department_id ?? ''}"
                                        data-service="${d.service_id ?? ''}"
                                        data-employee="${d.employee_id ?? ''}"
                                        data-service-str="${d.service ?? ''}">
                                        ${d.article_group ?? 'Produkt'}
                                    </option>
                                `);
                    });

                    $select.trigger('change.select2');
                });
            }

            function openEmployeeSelector(dealId, checkedById = null, reviewedById = null) {
                $.get('/get-employees', function (employees) {
                    if (!employees || !employees.length) {
                        Swal.fire('Keine Mitarbeiter gefunden');
                        return;
                    }

                    const employeeOptions = {};
                    employees.forEach(e => {
                        employeeOptions[e.id] = `${e.name} ${e.lastname}`;
                    });

                    const checkedOptions = Object.entries(employeeOptions).map(([id, name]) => {
                        const selected = String(id) === String(checkedById) ? 'selected' : '';
                        return `<option value="${id}" ${selected}>${name}</option>`;
                    }).join('');

                    const reviewedOptions = Object.entries(employeeOptions).map(([id, name]) => {
                        const selected = String(id) === String(reviewedById) ? 'selected' : '';
                        return `<option value="${id}" ${selected}>${name}</option>`;
                    }).join('');

                    Swal.fire({
                        title: 'Mitarbeiter auswählen',
                        html: `
                                    <select id="checked_by" class="swal2-input">
                                        <option value="">-- Geprüft durch --</option>
                                        ${checkedOptions}
                                    </select>
                                    <select id="reviewed_by" class="swal2-input">
                                        <option value="">-- Prüfer / Reviewer --</option>
                                        ${reviewedOptions}
                                    </select>
                                `,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Speichern',
                        cancelButtonText: 'Abbrechen',
                        preConfirm: () => ({
                            checked_by: $('#checked_by').val(),
                            reviewed_by: $('#reviewed_by').val(),
                        })
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        $.post('/update-deal-reviewers', {
                            _token: csrfToken,
                            deal_id: dealId,
                            checked_by: result.value.checked_by,
                            reviewed_by: result.value.reviewed_by,
                        }, function (res) {
                            if (res.success) {
                                Swal.fire('Gespeichert');
                                location.reload();
                            } else {
                                Swal.fire('Fehler beim Speichern');
                            }
                        });
                    });
                });
            }

            window.openEmployeeSelector = openEmployeeSelector;

            function promptDealFieldUpdate(dealId, field) {
                const label = labelMap[field] || field;
                const today = new Date().toISOString().split('T')[0];

                if (field === 'status') {
                    Swal.fire({
                        title: `${label} ändern`,
                        input: 'select',
                        inputOptions: {
                            confirm: 'Bestätigt',
                            inconfirm: 'Unbestätigt',
                            open: 'Offen'
                        },
                        inputPlaceholder: 'Status wählen',
                        showCancelButton: true,
                        confirmButtonText: 'Speichern',
                        cancelButtonText: 'Abbrechen',
                        inputValidator: value => !value ? 'Bitte einen Status wählen' : undefined
                    }).then(result => {
                        if (result.isConfirmed) {
                            submitDealFieldUpdate(dealId, field, result.value);
                        }
                    });
                    return;
                }

                if (field === 'price') {
                    Swal.fire({
                        title: `${label} ändern`,
                        html: `<input type="number" id="priceInput" class="swal2-input" step="0.01" min="0" placeholder="z.B. 199.99">`,
                        showCancelButton: true,
                        confirmButtonText: 'Speichern',
                        cancelButtonText: 'Abbrechen',
                        preConfirm: () => {
                            const val = document.getElementById('priceInput').value;
                            return val.trim() === '' ? null : parseFloat(val).toFixed(2);
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            submitDealFieldUpdate(dealId, field, result.value);
                        }
                    });
                    return;
                }

                Swal.fire({
                    title: `${label} ändern`,
                    html: `<input type="date" id="dateInput" class="swal2-input" value="${today}">`,
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen',
                    preConfirm: () => {
                        const val = document.getElementById('dateInput').value;
                        return val.trim() === '' ? null : val;
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        submitDealFieldUpdate(dealId, field, result.value);
                    }
                });
            }

            function submitDealFieldUpdate(dealId, field, value) {
                $.post('/update-deal-date', {
                    _token: csrfToken,
                    deal_id: dealId,
                    field,
                    value
                }, function (res) {
                    if (res.success) {
                        Swal.fire('Gespeichert');
                        location.reload();
                    } else {
                        Swal.fire('Fehler beim Speichern');
                    }
                }).fail(() => {
                    Swal.fire('Fehler', 'Validierung fehlgeschlagen.', 'error');
                });
            }

            function openUploadSidebar(dealId, customerId, alternativeId, productId, offerFolderId) {
                uploadSidebarParams = {
                    deal_id: dealId,
                    customer_id: customerId,
                    alternative_id: alternativeId,
                    product_id: productId,
                    offer_folder_id: offerFolderId
                };

                $(selectors.uploadDealId).val(dealId || '');
                $(selectors.uploadCustomerId).val(customerId || '');
                $(selectors.uploadAlternativeId).val(alternativeId || '');
                $(selectors.uploadProductId).val(productId || '');
                $(selectors.uploadOfferFolderId).val(offerFolderId || '');

                $(selectors.uploadSidebar).addClass('open');
                $(selectors.uploadBackdrop).addClass('show');

                loadUploadGallery();
            }
            function closeUploadSidebar() {
                $(selectors.uploadSidebar).removeClass('open');
                $(selectors.uploadBackdrop).removeClass('show');
            }

            function updateDealFilesBadge(dealId, count) {
                if (!dealId) return;

                const $button = $(`.open-upload-sidebar[data-deal-id="${dealId}"]`);
                let $badge = $(`[data-deal-files-badge="${dealId}"]`);

                count = parseInt(count || 0, 10);

                if (count <= 0) {
                    $badge.remove();
                    return;
                }

                if (!$badge.length && $button.length) {
                    $button.append(`
                        <span
                            class="badge badge-pill badge-danger position-absolute deal-files-badge"
                            data-deal-files-badge="${dealId}"
                            style="top:-6px;right:-6px;font-size:10px;padding:4px 6px;border:2px solid #fff;"
                        ></span>
                    `);

                    $badge = $(`[data-deal-files-badge="${dealId}"]`);
                }

                $badge.text(count);
            }

            function loadUploadGallery() {
                const stage = $(selectors.filterStage).val() || '';

                $(selectors.uploadGallery).html('<div class="text-muted">Lade Dateien...</div>');

                $.get('/deal/load-customer-files', {
                    deal_id: uploadSidebarParams.deal_id,
                    customer_id: uploadSidebarParams.customer_id,
                    alternative_id: uploadSidebarParams.alternative_id,
                    product_id: uploadSidebarParams.product_id,
                    offer_folder_id: uploadSidebarParams.offer_folder_id,
                    stage: stage,
                    json: 1
                }, function (res) {
                    $(selectors.uploadGallery).html(res.html || '');

                    updateDealFilesBadge(uploadSidebarParams.deal_id, res.files_count || 0);
                    applyGallerySearch();

                    if (typeof GLightbox !== 'undefined') {
                        GLightbox({ selector: '.glightbox' });
                    }
                }).fail(function () {
                    $(selectors.uploadGallery).html('<div class="text-danger">Dateien konnten nicht geladen werden.</div>');
                });
            }

            function initDropzone() {
                if (typeof Dropzone === 'undefined') return;

                Dropzone.autoDiscover = false;

                const formEl = document.getElementById('dealUploadDropzone');
                if (!formEl) return;

                if (formEl.dropzone) {
                    formEl.dropzone.destroy();
                }

                dealDropzoneInstance = new Dropzone(formEl, {
                    url: formEl.getAttribute('action'),
                    paramName: 'file',
                    uploadMultiple: false,
                    parallelUploads: 5,
                    maxFilesize: 20,
                    acceptedFiles: '.jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    init: function () {
                        this.on('sending', function (file, xhr, formData) {
                            formData.append('deal_id', $(selectors.uploadDealId).val() || '');
                            formData.append('customer_id', $(selectors.uploadCustomerId).val() || '');
                            formData.append('alternative_id', $(selectors.uploadAlternativeId).val() || '');
                            formData.append('product_id', $(selectors.uploadProductId).val() || '');
                            formData.append('offer_folder_id', $(selectors.uploadOfferFolderId).val() || '');
                            formData.append('stage_id', $(selectors.uploadStageSelect).val() || '');
                            formData.append('status', 'order');
                        });

                        this.on('success', function (file, response) {
                            this.removeAllFiles(true);

                            if (response && response.files_count !== undefined) {
                                updateDealFilesBadge(uploadSidebarParams.deal_id, response.files_count);
                            }

                            loadUploadGallery();
                        });

                        this.on('error', function (file, response) {
                            let message = 'Upload fehlgeschlagen.';

                            if (typeof response === 'string') {
                                message = response;
                            } else if (response && response.message) {
                                message = response.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: message
                            });
                        });
                    }
                });
            }
            function openNotesSidebar(dealId, customerId, alternativeId, productId) {
                currentNoteParams = {
                    deal_id: dealId,
                    customer_id: customerId,
                    alternative_id: alternativeId,
                    product_id: productId
                };

                $(selectors.noteSidebar).addClass('open');
                loadNotes();
            }

            function closeNotesSidebar() {
                $(selectors.noteSidebar).removeClass('open');
            }

            function loadNotes(search = '') {
                $.get('/deal/load-customer-notes', {
                    ...currentNoteParams,
                    search
                }).done(function (html) {
                    $(selectors.notesList).html(html);
                }).fail(function () {
                    $(selectors.notesList).html('<div class="deal-notes-empty">Fehler beim Laden der Notizen.</div>');
                });
            }

            function createNote() {
                const content = ($(selectors.newNoteContent).val() || '').trim();

                if (!content) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hinweis',
                        text: 'Bitte zuerst eine Notiz eingeben.'
                    });
                    return;
                }

                $.post('/deal/store-customer-note', {
                    _token: csrfToken,
                    ...currentNoteParams,
                    description: content
                }).done((res) => {
                    $(selectors.newNoteContent).val('');
                    loadNotes();

                    Swal.fire({
                        icon: 'success',
                        title: 'Gespeichert',
                        text: res.message || 'Notiz wurde gespeichert.',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }).fail((xhr) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        text: xhr.responseJSON?.message || 'Fehler beim Speichern der Notiz.'
                    });
                });
            }

            function editNote(noteId) {
                Swal.fire({
                    title: 'Notiz bearbeiten',
                    input: 'textarea',
                    inputPlaceholder: 'Neue Notiz eingeben...',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen',
                    inputValidator: (value) => {
                        if (!value || !value.trim()) return 'Bitte einen Text eingeben.';
                    }
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.post('/deal/update-customer-note', {
                        _token: csrfToken,
                        note_id: noteId,
                        description: result.value.trim()
                    }).done((res) => {
                        loadNotes();
                        Swal.fire({
                            icon: 'success',
                            title: 'Aktualisiert',
                            text: res.message || 'Notiz wurde aktualisiert.',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }).fail((xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler',
                            text: xhr.responseJSON?.message || 'Notiz konnte nicht aktualisiert werden.'
                        });
                    });
                });
            }

            function deleteNote(noteId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Notiz löschen?',
                    text: 'Diese Aktion kann rückgängig gemacht werden.',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, löschen',
                    cancelButtonText: 'Abbrechen'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.post('/deal/delete-customer-note', {
                        _token: csrfToken,
                        note_id: noteId
                    }).done((res) => {
                        loadNotes();
                        Swal.fire({
                            icon: 'success',
                            title: 'Gelöscht',
                            text: res.message || 'Notiz wurde gelöscht.',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }).fail((xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler',
                            text: xhr.responseJSON?.message || 'Notiz konnte nicht gelöscht werden.'
                        });
                    });
                });
            }

            function toggleReplyInput(noteId) {
                $(`#reply_box_${noteId}`).slideToggle();
            }

            function sendReply(parentId) {
                const replyText = ($(`#reply_input_${parentId}`).val() || '').trim();

                if (!replyText) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hinweis',
                        text: 'Bitte zuerst eine Antwort eingeben.'
                    });
                    return;
                }

                $.post('/deal/store-customer-note', {
                    _token: csrfToken,
                    ...currentNoteParams,
                    description: replyText,
                    parent_id: parentId
                }).done(() => {
                    $(`#reply_input_${parentId}`).val('');
                    $(`#reply_box_${parentId}`).slideUp();
                    loadNotes();
                }).fail((xhr) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler',
                        text: xhr.responseJSON?.message || 'Antwort konnte nicht gespeichert werden.'
                    });
                });
            }

            window.editNote = editNote;
            window.deleteNote = deleteNote;
            window.toggleReplyInput = toggleReplyInput;
            window.sendReply = sendReply;

            function submitDealCreateForm(e) {
                e.preventDefault();

                const $form = $(selectors.dealStore);
                const url = $form.attr('action');
                const formData = $form.serialize();
                const $submitBtn = $form.find('button[type="submit"]');

                $.ajax({
                    url,
                    method: 'POST',
                    data: formData,
                    beforeSend: function () {
                        $submitBtn.prop('disabled', true).text('Speichern...');
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Projekt erstellt!',
                            text: response.message || 'Das Projekt wurde erfolgreich erstellt.'
                        });

                        closeDealModal('dealModalCustom');
                        $form[0].reset();
                        $submitBtn.prop('disabled', false).text('Projekt erstellen');

                        setTimeout(() => {
                            window.location.href = window.location.pathname + '#deal-' + response.deal_id;
                            window.location.reload();
                        }, 1000);
                    },
                    error: function (xhr) {
                        $submitBtn.prop('disabled', false).text('Projekt erstellen');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            const errorList = Object.values(errors).map(err => `<li>${err[0]}</li>`).join('');
                            Swal.fire({
                                icon: 'error',
                                title: 'Validierungsfehler',
                                html: `<ul style="text-align:left;">${errorList}</ul>`
                            });
                        } else if (xhr.status === 409) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Doppelter Eintrag',
                                text: xhr.responseJSON.message || 'Ein ähnliches Projekt existiert bereits.'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: 'Unbekannter Fehler aufgetreten.'
                            });
                        }
                    }
                });
            }

            function highlightDealFromHash() {
                const hash = window.location.hash;
                if (!hash.startsWith('#deal-')) return;

                const el = document.querySelector(hash);
                if (!el) return;

                el.classList.add('bg-highlight');
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }


            function refreshKanbanColumnCounts() {
                document.querySelectorAll('.deal-kanban-column').forEach(column => {
                    const count = column.querySelectorAll('.kanban-item:not([style*="display: none"])').length;
                    const badge = column.querySelector('.deal-kanban-count');
                    if (badge) badge.textContent = count;
                });
            }

            function bindKanbanFilters() {
                const search = ($(selectors.kanbanSearch).val() || '').toLowerCase();
                const filter = $(selectors.kanbanFilter).val();
                const stage = $(selectors.kanbanStage).val();
                const product = $(selectors.kanbanProduct).val();

                $(selectors.kanbanItem).each(function () {
                    const $item = $(this);
                    const name = ($item.text() || '').toLowerCase();
                    const empId = String($item.data('emp-id') ?? '');
                    const itemStage = String($item.data('stage') ?? '');
                    const itemProduct = String($item.data('product-id') ?? '');

                    const matchSearch = name.includes(search);
                    const matchFilter = filter === 'all' || empId === String(authEmployeeId);
                    const matchStage = !stage || itemStage === String(stage);
                    const matchProduct = !product || itemProduct === String(product);

                    $item.toggle(matchSearch && matchFilter && matchStage && matchProduct);
                });

                refreshKanbanColumnCounts();
            }

            async function promptDealStatusReason(title, label) {
                const result = await Swal.fire({
                    icon: 'question',
                    title: title || 'Kanban Status ändern?',
                    html: `Der Auftrag wird auf <strong>${escapeHtml(label || '')}</strong> gesetzt.<br><br>Bitte Grund eingeben:`,
                    input: 'textarea',
                    inputPlaceholder: 'Grund der Statusänderung...',
                    inputValidator: (value) => {
                        if (!String(value || '').trim()) {
                            return 'Bitte einen Grund eingeben.';
                        }
                        return null;
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen'
                });

                if (!result.isConfirmed) {
                    return null;
                }

                return String(result.value || '').trim();
            }

            function postDealStatusChange(dealId, status, reason) {
                return $.post('/deal/update-status', {
                    _token: csrfToken,
                    id: dealId,
                    status: status,
                    reason: reason
                });
            }

            function initKanbanSortable() {
                if (!$.fn.sortable) return;

                $(selectors.kanbanList).sortable({
                    connectWith: selectors.kanbanList,
                    placeholder: 'kanban-placeholder',
                    tolerance: 'pointer',
                    start: function (e, ui) {
                        ui.placeholder.height(ui.item.height());
                        ui.item.css('z-index', 9999);
                        ui.item.data('old-parent', ui.item.parent());
                        ui.item.data('old-index', ui.item.index());
                    },
                    stop: async function (e, ui) {
                        ui.item.css('z-index', '');

                        const itemId = ui.item.data('id');
                        const oldStatus = String(ui.item.attr('data-stage') || '');
                        const newStatus = String(ui.item.closest(selectors.kanbanList).data('status') || '');
                        const label = ui.item.closest('.deal-kanban-column').find('.deal-kanban-title span:last').text().trim() || newStatus;
                        const $oldParent = ui.item.data('old-parent');
                        const oldIndex = Number(ui.item.data('old-index') || 0);

                        const revertCard = () => {
                            if ($oldParent && $oldParent.length) {
                                const children = $oldParent.children();
                                if (oldIndex >= children.length) {
                                    $oldParent.append(ui.item);
                                } else {
                                    ui.item.insertBefore(children.eq(oldIndex));
                                }
                            }
                            refreshKanbanColumnCounts();
                        };

                        if (!itemId || !newStatus || newStatus === oldStatus) {
                            refreshKanbanColumnCounts();
                            return;
                        }

                        const reason = await promptDealStatusReason('Kanban Status ändern?', label);
                        if (!reason) {
                            revertCard();
                            return;
                        }

                        postDealStatusChange(itemId, newStatus, reason).done(response => {
                            if (!response || response.success === false) {
                                revertCard();
                                Swal.fire('Fehler', response?.message || 'Status konnte nicht aktualisiert werden.', 'error');
                                return;
                            }

                            ui.item.attr('data-stage', response.status || newStatus);
                            ui.item.find('.deal-status-pill').first().text(response.label || response.status || newStatus);
                            ui.item.find('.deal-update-badge').remove();
                            ui.item.find('.d-flex.justify-content-between.align-items-start.gap-2').first().append(
                                '<span class="deal-update-badge fresh" title="Status geändert">Neu · Status</span>'
                            );
                            refreshKanbanColumnCounts();

                            Swal.fire({
                                icon: 'success',
                                title: 'Gespeichert',
                                text: response.message || 'Kanban Status wurde aktualisiert.',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        }).fail(xhr => {
                            revertCard();
                            const message = xhr.responseJSON?.message || 'Status konnte nicht aktualisiert werden.';
                            Swal.fire('Fehler', message, 'error');
                        });
                    }
                }).disableSelection();
            }



            $(document).on('change', '.js-deal-list-status', function () {
                const $select = $(this);
                const dealId = $select.data('deal-id');
                const oldStatus = String($select.data('current-status') || '');
                const newStatus = String($select.val() || '');
                const selectedLabel = $select.find('option:selected').text().trim();

                if (!dealId || !newStatus || newStatus === oldStatus) {
                    return;
                }

                Swal.fire({
                    icon: 'question',
                    title: 'Kanban Status ändern?',
                    html: `Der Auftrag wird auf <strong>${escapeHtml(selectedLabel)}</strong> gesetzt.<br><br>Bitte Grund eingeben:`,
                    input: 'textarea',
                    inputPlaceholder: 'Grund der Statusänderung...',
                    inputValidator: (value) => {
                        if (!String(value || '').trim()) {
                            return 'Bitte einen Grund eingeben.';
                        }
                        return null;
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        $select.val(oldStatus);
                        return;
                    }

                    $.post('/deal/update-status', {
                        _token: csrfToken,
                        id: dealId,
                        status: newStatus,
                        reason: result.value || ''
                    }).done((response) => {
                        if (!response || response.success === false) {
                            $select.val(oldStatus);
                            Swal.fire('Fehler', response?.message || 'Status konnte nicht aktualisiert werden.', 'error');
                            return;
                        }

                        $select.data('current-status', response.status || newStatus);

                        const $row = $select.closest('.deal-item');
                        const label = response.label || selectedLabel;
                        const updateText = 'Status geändert · ' + label;
                        const nowLabel = new Date().toLocaleString('de-DE', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        if (!$row.find('.deal-update-badge').length) {
                            $row.find('.deal-ttl').append(' <span class="deal-update-badge fresh"></span>');
                        }

                        $row.find('.deal-update-badge')
                            .addClass('fresh')
                            .text('Neu · Auftrag');

                        if (!$row.find('.deal-latest-change-line').length) {
                            $row.find('.deal-subt').first().append('<div class="deal-latest-change-line"><i class="feather icon-zap"></i> <span></span></div>');
                        }

                        $row.find('.deal-latest-change-line span').text(updateText + ' · ' + nowLabel);

                        if (window.feather) {
                            feather.replace();
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Gespeichert',
                            text: response.message || 'Kanban Status wurde aktualisiert.',
                            timer: 1300,
                            showConfirmButton: false
                        });
                    }).fail((xhr) => {
                        $select.val(oldStatus);
                        Swal.fire('Fehler', xhr.responseJSON?.message || 'Status konnte nicht aktualisiert werden.', 'error');
                    });
                });
            });


            function applyGallerySearch() {
                const term = (($(selectors.fileSearchInput).val() || '').trim().toLowerCase());

                $(`${selectors.uploadGallery} .gallery-item`).each(function () {
                    const $item = $(this);
                    const haystack = String($item.data('search') || '').toLowerCase();

                    const matches = term === '' || haystack.includes(term);
                    $item.toggle(matches);
                });

                const $visibleItems = $(`${selectors.uploadGallery} .gallery-item:visible`);
                const $emptyState = $(`${selectors.uploadGallery} .gallery-search-empty`);

                if ($visibleItems.length === 0 && $(`${selectors.uploadGallery} .gallery-item`).length > 0) {
                    if ($emptyState.length === 0) {
                        $(selectors.uploadGallery).append(
                            '<div class="gallery-search-empty text-muted w-100 pt-2">Keine Dateien entsprechen der Suche.</div>'
                        );
                    }
                } else {
                    $emptyState.remove();
                }
            }


            function closeDealHistorySidebar() {
                $('#dealHistorySidebar').removeClass('open');
                $('#dealHistoryBackdrop').removeClass('show');
            }

            function openDealHistorySidebar(dealId, orderNumber = '') {
                if (!dealId) {
                    Swal.fire('Fehler', 'Deal-ID fehlt.', 'error');
                    return;
                }

                $('#dealHistorySubtitle').text(orderNumber ? `Historie für ${orderNumber}` : 'Alle Änderungen, Statuswechsel, Dokumente und Feinaufmaß');
                $('#dealHistoryBody').html('<div class="deal-history-loading"><i class="fa fa-spinner fa-spin"></i> Historie wird geladen...</div>');
                $('#dealHistoryBackdrop').addClass('show');
                $('#dealHistorySidebar').addClass('open');

                $.get(`/deal/${dealId}/history`)
                    .done(function (response) {
                        if (!response || response.success === false) {
                            $('#dealHistoryBody').html('<div class="deal-history-empty">Historie konnte nicht geladen werden.</div>');
                            return;
                        }

                        $('#dealHistoryBody').html(response.html || '<div class="deal-history-empty">Keine Historie gefunden.</div>');
                        if (window.feather) {
                            feather.replace();
                        }
                    })
                    .fail(function (xhr) {
                        $('#dealHistoryBody').html(`<div class="deal-history-empty text-danger">${escapeHtml(xhr.responseJSON?.message || 'Historie konnte nicht geladen werden.')}</div>`);
                    });
            }

            window.openDealHistorySidebar = openDealHistorySidebar;
            window.closeDealHistorySidebar = closeDealHistorySidebar;

            function bindEvents() {
                document.addEventListener('click', function (e) {
                    if (e.target.matches(selectors.dealModalBackdrop)) {
                        e.target.classList.remove('open');
                    }

                    const tabBtn = e.target.closest('[data-tab-target]');
                    if (tabBtn) {
                        switchDealTab(tabBtn.dataset.tabTarget);
                    }

                    const uploadSidebarBtn = e.target.closest(selectors.openUploadSidebar);
                    if (uploadSidebarBtn) {
                        openUploadSidebar(
                            uploadSidebarBtn.dataset.dealId,
                            uploadSidebarBtn.dataset.customerId,
                            uploadSidebarBtn.dataset.alternativeId,
                            uploadSidebarBtn.dataset.productId,
                            uploadSidebarBtn.dataset.offerFolderId
                        );
                    }

                    const notesSidebarBtn = e.target.closest(selectors.openNotesSidebar);
                    if (notesSidebarBtn) {
                        openNotesSidebar(
                            notesSidebarBtn.dataset.dealId,
                            notesSidebarBtn.dataset.customerId,
                            notesSidebarBtn.dataset.alternativeId,
                            notesSidebarBtn.dataset.productId
                        );
                    }

                    const historyBtn = e.target.closest('.open-deal-history-sidebar');
                    if (historyBtn) {
                        openDealHistorySidebar(historyBtn.dataset.dealId, historyBtn.dataset.orderNumber || '');
                    }

                    const deliveryBtn = e.target.closest('.open-delivery-notes');
                    if (deliveryBtn) {
                        const dealId = deliveryBtn.dataset.dealId;
                        const orderNumber = deliveryBtn.dataset.orderNumber || '';

                        $('#deliveryNotesOrderNumber').text(orderNumber);
                        $('#deliveryNotesModalBody').html('<div class="text-muted py-4 text-center">Lade Lieferscheine...</div>');
                        $('#deliveryNotesModal').modal('show');

                        $.get(`/deal/${dealId}/delivery-notes`)
                            .done(function (html) {
                                $('#deliveryNotesModalBody').html(html);
                            })
                            .fail(function () {
                                $('#deliveryNotesModalBody').html('<div class="text-danger py-4 text-center">Lieferscheine konnten nicht geladen werden.</div>');
                            });
                    }

                    const editableCell = e.target.closest(selectors.editableCell);
                    if (editableCell) {
                        const dealId = editableCell.dataset.id;
                        const field = editableCell.dataset.field;
                        if (dealId && field) {
                            promptDealFieldUpdate(dealId, field);
                        }
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        qsa('.deal-modal-backdrop.open').forEach(el => el.classList.remove('open'));
                        closeUploadSidebar();
                        closeNotesSidebar();
                        closeDealHistorySidebar();
                    }
                });

                $(document).on('change', selectors.renameInput, function () {
                    const id = $(this).data('id');
                    const new_name = $(this).val();

                    $.post('/deal/rename-file', {
                        _token: csrfToken,
                        id,
                        new_name
                    }).fail(function () {
                        Swal.fire('Fehler', 'Datei konnte nicht umbenannt werden.', 'error');
                    });
                });

                $(document).on('click', selectors.deleteFile, function () {
                    const id = $(this).data('id');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Datei löschen?',
                        text: 'Diese Datei wird entfernt.',
                        showCancelButton: true,
                        confirmButtonText: 'Ja, löschen',
                        cancelButtonText: 'Abbrechen'
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        $.post('/deal/delete-file', {
                            _token: csrfToken,
                            id
                        }, function () {
                            loadUploadGallery();
                        }).fail(function () {
                            Swal.fire('Fehler', 'Datei konnte nicht gelöscht werden.', 'error');
                        });
                    });
                });

                $(document).on('click', '.send-to-measurement', function () {
                    const dealId = $(this).data('deal-id');
                    const orderNumber = $(this).data('order-number') || '#' + dealId;

                    if (!dealId) {
                        Swal.fire('Fehler', 'Deal-ID fehlt.', 'error');
                        return;
                    }

                    Swal.fire({
                        icon: 'question',
                        title: 'Feinaufmaß erstellen?',
                        html: `Der Auftrag <strong>${orderNumber}</strong> wird als Feinaufmaß markiert.<br>Die Materialliste aus dem Angebot wird übernommen.`,
                        showCancelButton: true,
                        confirmButtonText: 'Ja, erstellen',
                        cancelButtonText: 'Abbrechen'
                    }).then(function (result) {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            url: `/deal/${dealId}/measurement/send`,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function () {
                                Swal.fire({
                                    title: 'Bitte warten...',
                                    text: 'Feinaufmaß wird erstellt.',
                                    allowOutsideClick: false,
                                    didOpen: () => Swal.showLoading()
                                });
                            },
                            success: function (res) {
                                if (!res.success) {
                                    Swal.fire('Fehler', res.message || 'Feinaufmaß konnte nicht erstellt werden.', 'error');
                                    return;
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Erstellt',
                                    text: res.message || 'Feinaufmaß wurde erstellt.',
                                    timer: 1200,
                                    showConfirmButton: false
                                });

                                setTimeout(function () {
                                    if (res.redirect_url) {
                                        window.location.href = res.redirect_url;
                                    } else {
                                        window.location.reload();
                                    }
                                }, 900);
                            },
                            error: function (xhr) {
                                let message = xhr.responseJSON?.message || 'Feinaufmaß konnte nicht erstellt werden.';

                                if (xhr.responseJSON?.errors) {
                                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                                }

                                Swal.fire('Fehler', message, 'error');
                            }
                        });
                    });
                });

                $(selectors.closeUploadSidebar).on('click', closeUploadSidebar);
                $(selectors.filterStage).on('change', loadUploadGallery);
                $(selectors.fileSearchInput).on('input', applyGallerySearch);
                $(selectors.closeSidebar).on('click', closeNotesSidebar);
                $('#closeDealHistorySidebar, #dealHistoryBackdrop').on('click', closeDealHistorySidebar);
                $(selectors.searchNotes).on('input', function () {
                    loadNotes(($(this).val() || '').trim());
                });
                $(selectors.sendNote).on('click', createNote);
                $(selectors.dealStore).on('submit', submitDealCreateForm);

                $(selectors.customerSelect).on('change', function () {
                    loadCustomerProductLists($(this).val());
                });

                $(selectors.productListSelect).on('change', function () {
                    fillProductHiddenFields($(this).find('option:selected'));
                });

                $([
                    selectors.kanbanSearch,
                    selectors.kanbanFilter,
                    selectors.kanbanStage,
                    selectors.kanbanProduct
                ].join(',')).on('input change', bindKanbanFilters);
            }

            function init() {
                initSelect2();
                initDropzone();
                initKanbanSortable();
                bindEvents();
                highlightDealFromHash();
            }


            window.mapServerItemToEvents = function (item) {
                const dateStr = item.start_date || (item.created_at ? item.created_at.split('T')[0] : new Date().toISOString().split('T')[0]);
                const sTime = item.start_time || "08:00:00";
                const eTime = item.end_time || "09:00:00";

                const isCancelled = item.is_cancelled === true || item.status === 'cancelled';
                let displayTitle = item.title || item.description || (item.name + ' ' + item.lastname) || "-";
                if (isCancelled && !displayTitle.includes('(ABGESAGT)')) {
                    displayTitle = `🚫 ${displayTitle} (ABGESAGT)`;
                }

                return {
                    id: 'deal-' + item.id,
                    title: displayTitle,
                    start: `${dateStr}T${sTime}`,
                    end: `${dateStr}T${eTime}`,
                    color: isCancelled ? "#ffcccc" : (item.color || "#8fc73e"),
                    textColor: isCancelled ? "#cc0000" : null,
                    allDay: false,
                    extendedProps: {
                        notes_count: parseInt(item.notes_count || 0),
                        files_count: parseInt(item.files_count || 0),
                        is_cancelled: isCancelled,
                        status: item.status || (isCancelled ? 'cancelled' : 'normal'),
                        customer_id: item.customer_id,
                        deal_id: item.id
                    }
                };
            };



            $(document).ready(init);
            /* ===== FINAL ROBUST ACTION DROPDOWN FIX ===== */
            let activeDealDropdown = null;
            let activeDealDropdownOwner = null;
            let activeDealDropdownButton = null;

            function closeFixedDealDropdown() {
                if (activeDealDropdown && activeDealDropdownOwner) {
                    activeDealDropdown
                        .removeClass('deal-dropdown-fixed show')
                        .removeAttr('style')
                        .hide();

                    activeDealDropdownOwner.append(activeDealDropdown);

                    if (activeDealDropdownButton) {
                        activeDealDropdownButton.attr('aria-expanded', 'false');
                    }
                }

                $('.deal-item').removeClass('dropdown-open');

                activeDealDropdown = null;
                activeDealDropdownOwner = null;
                activeDealDropdownButton = null;
            }

            function openFixedDealDropdown($button) {
                const $wrapper = $button.closest('.dropdown-icon-wrapper');
                const $row = $button.closest('.deal-item');
                const $menu = $wrapper.children('.dropdown-menu').first();

                if (!$menu.length) {
                    return;
                }

                if (activeDealDropdown && activeDealDropdown[0] === $menu[0]) {
                    closeFixedDealDropdown();
                    return;
                }

                closeFixedDealDropdown();

                activeDealDropdown = $menu;
                activeDealDropdownOwner = $wrapper;
                activeDealDropdownButton = $button;

                $row.addClass('dropdown-open');
                $button.attr('aria-expanded', 'true');

                const rect = $button[0].getBoundingClientRect();

                $('body').append($menu);

                $menu
                    .addClass('deal-dropdown-fixed show')
                    .css({
                        visibility: 'hidden',
                        display: 'block',
                        top: '0px',
                        left: '0px',
                        right: 'auto',
                        bottom: 'auto'
                    });

                const menuWidth = Math.max($menu.outerWidth(), 235);
                const menuHeight = $menu.outerHeight();

                let left = rect.right - menuWidth;
                let top = rect.bottom + 8;

                if (left < 8) {
                    left = 8;
                }

                if (left + menuWidth > window.innerWidth - 8) {
                    left = window.innerWidth - menuWidth - 8;
                }

                if (top + menuHeight > window.innerHeight - 8) {
                    top = rect.top - menuHeight - 8;
                }

                if (top < 8) {
                    top = 8;
                }

                $menu.css({
                    visibility: 'visible',
                    width: menuWidth + 'px',
                    top: top + 'px',
                    left: left + 'px',
                    right: 'auto',
                    bottom: 'auto'
                });
            }

            $(document).on('click', '[data-deal-menu-toggle], .deal-menu-toggle', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                openFixedDealDropdown($(this));
            });

            $(document).on('click', '.deal-dropdown-fixed a', function () {
                const $link = $(this);

                if ($link.attr('data-toggle') === 'modal' || $link.attr('data-target')) {
                    const target = $link.attr('data-target');

                    closeFixedDealDropdown();

                    if (target) {
                        setTimeout(function () {
                            $(target).modal('show');
                        }, 50);
                    }

                    return;
                }

                closeFixedDealDropdown();
            });

            $(document).on('click', function (e) {
                const clickedInsideMenu = $(e.target).closest('.deal-dropdown-fixed').length > 0;
                const clickedToggle = $(e.target).closest('[data-deal-menu-toggle], .deal-menu-toggle').length > 0;

                if (!clickedInsideMenu && !clickedToggle) {
                    closeFixedDealDropdown();
                }
            });

            $(document).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeFixedDealDropdown();
                }
            });

            $(window).on('resize scroll', function () {
                closeFixedDealDropdown();
            });

            function getSelectedDealIds() {
                return $('.deal-row-checkbox:checked')
                    .map(function () {
                        return $(this).val();
                    })
                    .get()
                    .filter(Boolean);
            }

            function updateBulkSelectionUi() {
                const selectedIds = getSelectedDealIds();
                const totalBoxes = $('.deal-row-checkbox').length;
                const checkedBoxes = selectedIds.length;

                $('#selectedDealsCount').text(checkedBoxes);

                const allChecked = totalBoxes > 0 && checkedBoxes === totalBoxes;

                $('#selectAllDeals').prop('checked', allChecked);
                $('#selectAllDealsHead').prop('checked', allChecked);
            }

            $(document).on('change', '#selectAllDeals, #selectAllDealsHead', function () {
                const checked = $(this).is(':checked');

                $('.deal-row-checkbox').prop('checked', checked);
                $('#selectAllDeals').prop('checked', checked);
                $('#selectAllDealsHead').prop('checked', checked);

                updateBulkSelectionUi();
            });

            $(document).on('change', '.deal-row-checkbox', function () {
                updateBulkSelectionUi();
            });

            $(document).on('change', '#bulkAction', function () {
                const action = $(this).val();

                if (action === 'status') {
                    $('#bulkStatus').show();
                } else {
                    $('#bulkStatus').hide().val('');
                }
            });

            $(document).on('click', '#runBulkAction', function () {
                const ids = getSelectedDealIds();
                const action = $('#bulkAction').val();
                const status = $('#bulkStatus').val();

                if (!ids.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keine Auswahl',
                        text: 'Bitte mindestens einen Auftrag auswählen.'
                    });
                    return;
                }

                if (!action) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keine Aktion',
                        text: 'Bitte eine Aktion auswählen.'
                    });
                    return;
                }

                if (action === 'status' && !status) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kein Status',
                        text: 'Bitte einen Status auswählen.'
                    });
                    return;
                }

                const actionLabels = {
                    delete: 'löschen',
                    junk: 'als Junk markieren',
                    unjunk: 'aus Junk entfernen',
                    restore: 'wiederherstellen',
                    status: 'Status ändern'
                };

                Swal.fire({
                    icon: 'warning',
                    title: 'Bulk-Aktion ausführen?',
                    html: `<strong>${ids.length}</strong> Auftrag/Aufträge werden verarbeitet.<br>Aktion: <strong>${actionLabels[action] || action}</strong>`,
                    showCancelButton: true,
                    confirmButtonText: 'Ja, ausführen',
                    cancelButtonText: 'Abbrechen'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '{{ route('deal.bulk.action') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids,
                            action: action,
                            status: status
                        },
                        beforeSend: function () {
                            $('#runBulkAction').prop('disabled', true).text('Bitte warten...');
                        },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Erledigt',
                                text: res.message || 'Bulk-Aktion wurde ausgeführt.',
                                timer: 1400,
                                showConfirmButton: false
                            });

                            setTimeout(function () {
                                window.location.reload();
                            }, 900);
                        },
                        error: function (xhr) {
                            let message = 'Bulk-Aktion konnte nicht ausgeführt werden.';

                            if (xhr.responseJSON?.message) {
                                message = xhr.responseJSON.message;
                            }

                            if (xhr.responseJSON?.errors) {
                                const errors = xhr.responseJSON.errors;
                                message = Object.values(errors).flat().join('\n');
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler',
                                text: message
                            });
                        },
                        complete: function () {
                            $('#runBulkAction').prop('disabled', false).text('Ausführen');
                        }
                    });
                });
            });

            $(document).ready(function () {
                updateBulkSelectionUi();
            });

            let planningState = {
                dealId: null,
                materialList: [],
                requiredQualifications: [],
                suggestions: [],
            };

            function openProjectPlanningSidebar(dealId, orderNumber) {
                planningState = {
                    dealId: dealId,
                    materialList: [],
                    requiredQualifications: [],
                    suggestions: [],
                };

                const today = new Date().toISOString().slice(0, 10);

                $('#planningDealId').val(dealId);
                $('#planningOrderNumber').text('Auftrag: ' + (orderNumber || '#' + dealId));
                $('#planningTitle').val('Projekt ' + (orderNumber || '#' + dealId));
                $('#planningDate').val(today);

                $('#planningQualifications').html('<div class="text-muted">Lade Qualifikationen...</div>');
                $('#planningMaterialList').html('<div class="text-muted">Lade Materialliste...</div>');
                $('#planningSuggestions').html('<div class="text-muted">Bitte zuerst Verfügbarkeit prüfen.</div>');

                $('#projectPlanningSidebar').addClass('open');
                $('#projectPlanningBackdrop').addClass('show');

                $.get(`/deal/${dealId}/planning/preview`)
                    .done(function (res) {
                        if (!res.success) {
                            Swal.fire('Fehler', res.message || 'Daten konnten nicht geladen werden.', 'error');
                            return;
                        }

                        planningState.materialList = res.material_list || [];
                        planningState.requiredQualifications = res.required_qualifications || [];

                        renderPlanningQualifications(planningState.requiredQualifications);
                        renderPlanningMaterialList(planningState.materialList);
                    })
                    .fail(function (xhr) {
                        Swal.fire(
                            'Fehler',
                            xhr.responseJSON?.message || 'Projektplanung konnte nicht geladen werden.',
                            'error'
                        );
                    });
            }

            function closeProjectPlanningSidebar() {
                $('#projectPlanningSidebar').removeClass('open');
                $('#projectPlanningBackdrop').removeClass('show');
            }

            function renderPlanningQualifications(rows) {
                if (!rows.length) {
                    $('#planningQualifications').html('<div class="text-muted">Keine Arbeits-/Qualifikationsdaten im Angebot gefunden.</div>');
                    return;
                }

                let html = `
                            <table class="planning-table">
                                <thead>
                                    <tr>
                                        <th>Qualifikation</th>
                                        <th>Stunden</th>
                                        <th>EK</th>
                                        <th>Satz</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                rows.forEach(row => {
                    html += `
                                <tr>
                                    <td>
                                        <strong>${escapeHtml(row.qualification_name || 'Unbekannt')}</strong>
                                        <div class="planning-small">${escapeHtml(row.section || '')}</div>
                                    </td>
                                    <td>${formatNumber(row.hours_total || row.qty || 0)} ${escapeHtml(row.unit || 'Std.')}</td>
                                    <td>${formatMoney(row.ek)}</td>
                                    <td>${formatMoney(row.rate)}</td>
                                    <td>${formatMoney(row.total)}</td>
                                </tr>
                            `;
                });

                html += '</tbody></table>';

                $('#planningQualifications').html(html);
            }

            function renderPlanningMaterialList(rows) {
                if (!rows.length) {
                    $('#planningMaterialList').html('<div class="text-muted">Keine Materialdaten gefunden.</div>');
                    return;
                }

                let html = `
                            <table class="planning-table">
                                <thead>
                                    <tr>
                                        <th>Artikel</th>
                                        <th>Artikel-Nr.</th>
                                        <th>Menge</th>
                                        <th>Lieferant</th>
                                        <th>Status</th>
                                        <th>Preis</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                rows.forEach(row => {
                    const indent = Math.max(0, parseInt(row.depth || 0, 10)) * 16;

                    html += `
                                <tr>
                                    <td style="padding-left:${12 + indent}px;">
                                        <strong>${escapeHtml(row.name || 'Unbenannt')}</strong>
                                        <div class="planning-small">${escapeHtml(row.section || '')}</div>
                                    </td>
                                    <td>${escapeHtml(row.article_no || '-')}</td>
                                    <td>${formatNumber(row.qty_total || row.qty || 0)} ${escapeHtml(row.unit || 'Stk.')}</td>
                                    <td>${escapeHtml(row.supplier || '-')}</td>
                                    <td>${escapeHtml(row.order_status || '-')}</td>
                                    <td>${formatMoney(row.price)}</td>
                                </tr>
                            `;
                });

                html += '</tbody></table>';

                $('#planningMaterialList').html(html);
            }

            function checkPlanningAvailability() {
                const dealId = planningState.dealId;

                if (!dealId) return;

                const payload = {
                    _token: csrfToken,
                    planned_date: $('#planningDate').val(),
                    start_time: $('#planningStartTime').val(),
                    end_time: $('#planningEndTime').val(),
                };

                if (!payload.planned_date || !payload.start_time || !payload.end_time) {
                    Swal.fire('Hinweis', 'Bitte Datum, Startzeit und Endzeit eingeben.', 'warning');
                    return;
                }

                $('#planningSuggestions').html('<div class="text-muted">Prüfe Verfügbarkeit...</div>');

                $.post(`/deal/${dealId}/planning/check`, payload)
                    .done(function (res) {
                        planningState.suggestions = res.suggestions || [];
                        renderPlanningSuggestions(planningState.suggestions);
                    })
                    .fail(function (xhr) {
                        Swal.fire(
                            'Fehler',
                            xhr.responseJSON?.message || 'Verfügbarkeit konnte nicht geprüft werden.',
                            'error'
                        );
                    });
            }

            function renderPlanningSuggestions(groups) {
                if (!groups.length) {
                    $('#planningSuggestions').html('<div class="text-muted">Keine Vorschläge gefunden.</div>');
                    return;
                }

                let html = '';

                groups.forEach((group) => {
                    html += `
                                <div style="margin-bottom:18px;">
                                    <div style="font-weight:900;margin-bottom:8px;">
                                        ${escapeHtml(group.required_qualification_name || 'Unbekannt')}
                                        <span class="text-muted">
                                            · ${formatNumber(group.required_hours || 0)} Std.
                                        </span>
                                    </div>
                            `;

                    if (!group.employees || !group.employees.length) {
                        html += '<div class="text-danger">Keine passenden Mitarbeiter gefunden.</div></div>';
                        return;
                    }

                    html += `
                                <table class="planning-table">
                                    <thead>
                                        <tr>
                                            <th>Auswahl</th>
                                            <th>Mitarbeiter</th>
                                            <th>Position</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                    let firstAvailableSelected = false;

                    group.employees.forEach((employee) => {
                        let checked = '';

                        if (employee.available && !firstAvailableSelected) {
                            checked = 'checked';
                            firstAvailableSelected = true;
                        }

                        const disabled = employee.available ? '' : 'disabled';

                        html += `
                                    <tr>
                                        <td>
                                            <label class="planning-employee-choice">
                                                <input
                                                    type="checkbox"
                                                    class="planning-employee-checkbox"
                                                    ${checked}
                                                    ${disabled}
                                                    data-employee-id="${employee.employee_id}"
                                                    data-required-qualification-id="${group.required_qualification_id || ''}"
                                                    data-required-qualification-name="${escapeAttr(group.required_qualification_name || '')}"
                                                    data-employee-real-qualification-id="${employee.qualification_id || ''}"
                                                    data-employee-real-qualification-name="${escapeAttr(employee.qualification_name || '')}"
                                                    data-planned-hours="${group.required_hours || 0}"
                                                >
                                                auswählen
                                            </label>
                                        </td>
                                        <td>
                                            <strong>${escapeHtml(employee.name || '-')}</strong>
                                        </td>
                                        <td>
                                            ${escapeHtml(employee.position_name || '-')}
                                            <div class="planning-small">${escapeHtml(employee.qualification_name || '')}</div>
                                        </td>
                                        <td>
                                            ${employee.available
                                ? '<span class="planning-pill available">Verfügbar</span>'
                                : '<span class="planning-pill busy">Belegt</span>'
                            }
                                        </td>
                                        <td>${employee.score || 0}</td>
                                    </tr>
                                `;
                    });

                    html += '</tbody></table></div>';
                });

                $('#planningSuggestions').html(html);
            }

            function storeProjectPlanning(e) {
                e.preventDefault();

                const dealId = planningState.dealId;

                if (!dealId) return;

                const employees = $('.planning-employee-checkbox:checked')
                    .map(function () {
                        return {
                            employee_id: $(this).data('employee-id'),
                            qualification_id: $(this).data('required-qualification-id') || null,
                            qualification_name: $(this).data('required-qualification-name') || null,
                            employee_real_qualification_id: $(this).data('employee-real-qualification-id') || null,
                            employee_real_qualification_name: $(this).data('employee-real-qualification-name') || null,
                            planned_hours: $(this).data('planned-hours') || 0,
                        };
                    })
                    .get();

                const payload = {
                    _token: csrfToken,
                    title: $('#planningTitle').val(),
                    planned_date: $('#planningDate').val(),
                    start_time: $('#planningStartTime').val(),
                    end_time: $('#planningEndTime').val(),
                    employees: employees,
                };

                if (!payload.title || !payload.planned_date || !payload.start_time || !payload.end_time) {
                    Swal.fire('Hinweis', 'Bitte Projektname, Datum und Zeiten ausfüllen.', 'warning');
                    return;
                }

                $.ajax({
                    url: `/deal/${dealId}/planning/store`,
                    method: 'POST',
                    data: payload,
                    beforeSend: function () {
                        $('#saveProjectPlanning').prop('disabled', true).text('Speichern...');
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Gespeichert',
                            text: res.message || 'Projekt wurde geplant.',
                            timer: 1400,
                            showConfirmButton: false,
                        });

                        setTimeout(function () {
                            window.location.reload();
                        }, 900);
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message || 'Projektplanung konnte nicht gespeichert werden.';

                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }

                        Swal.fire('Fehler', message, 'error');
                    },
                    complete: function () {
                        $('#saveProjectPlanning').prop('disabled', false).text('Projekt speichern');
                    }
                });
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function escapeAttr(value) {
                return escapeHtml(value).replaceAll('`', '&#096;');
            }

            function formatNumber(value) {
                const num = Number(value || 0);

                return new Intl.NumberFormat('de-DE', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                }).format(num);
            }

            function formatMoney(value) {
                if (value === null || value === undefined || value === '') {
                    return '-';
                }

                const num = Number(value || 0);

                return new Intl.NumberFormat('de-DE', {
                    style: 'currency',
                    currency: 'EUR',
                }).format(num);
            }

            $(document).on('click', '.open-project-planning-sidebar', function () {
                openProjectPlanningSidebar(
                    $(this).data('deal-id'),
                    $(this).data('order-number')
                );
            });

            $(document).on('click', '#closeProjectPlanningSidebar, #projectPlanningBackdrop', closeProjectPlanningSidebar);
            $(document).on('click', '#checkPlanningAvailability', checkPlanningAvailability);
            $(document).on('submit', '#projectPlanningForm', storeProjectPlanning);

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
                url: "{{ url('new_lead_view')}}",
            },
            {
                label: '{{ $pageTitle  }}',
                url: "{{ url()->current() }}",
                clickable: false
            },

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush