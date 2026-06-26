@extends('admin.layouts.app')
@section('title', 'Angebot Vorlagen')

@php
    use Illuminate\Pagination\AbstractPaginator;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Str;

    $isPaginator = $templates instanceof LengthAwarePaginator || $templates instanceof AbstractPaginator;
    $items = $isPaginator ? collect($templates->items()) : collect($templates);

    $totalCount = $isPaginator ? $templates->total() : $items->count();
    $favoritesCount = (int) $items->where('is_favorite', true)->count();
    $stampedCount = (int) $items->filter(fn($item) => !empty($item->stamp))->count();
    $usedCount = (int) $items->filter(fn($item) => (int) ($item->usage_count ?? 0) > 0)->count();

    $activeEmployees = isset($activeEmployees) ? collect($activeEmployees) : collect();
    $selectedArticleGroup = $selectedArticleGroup ?? null;
    $selectedEmployee = $selectedEmployee ?? null;

    $employeeName = function ($employee) {
        if (!$employee) {
            return '—';
        }

        $full = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return $employee->full_name
            ?? $employee->display_name
            ?? ($full ?: ('Mitarbeiter #' . $employee->id));
    };

    $employeeInitials = function ($employee) use ($employeeName) {
        $name = $employeeName($employee);

        return collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn($part) => mb_substr($part, 0, 1))
            ->implode('') ?: '?';
    };

    $money = function ($value) {
        return number_format((float) ($value ?? 0), 2, ',', '.') . ' €';
    };

    $plain = function ($html, $limit = 130) {
        $text = trim(strip_tags((string) $html));
        $text = preg_replace('/\s+/', ' ', $text);

        return Str::limit($text, $limit);
    };

    $templateTotals = function ($template) {
        $sections = collect($template->sections ?? []);

        $sectionCount = $sections->count();
        $positionCount = 0;
        $subPositionCount = 0;
        $itemsTotal = 0.0;

        $walkItems = function ($items) use (&$walkItems, &$positionCount, &$subPositionCount, &$itemsTotal) {
            foreach (collect($items) as $item) {
                $positionCount++;

                $qty = (float) ($item['qty'] ?? 1);
                $price = (float) ($item['price'] ?? 0);

                $itemsTotal += $qty * $price;

                $children = collect($item['subItems'] ?? []);
                $subPositionCount += $children->count();

                if ($children->isNotEmpty()) {
                    $walkItems($children);
                }
            }
        };

        foreach ($sections as $section) {
            $walkItems($section['items'] ?? []);
        }

        return [
            'section_count' => $sectionCount,
            'position_count' => $positionCount,
            'sub_position_count' => $subPositionCount,
            'items_total' => $itemsTotal,
        ];
    };

    $templatePreviewPositions = function ($template, $limit = 999) use ($plain) {
        $rows = [];

        $walkItems = function ($items, $sectionTitle = null, $depth = 0) use (&$walkItems, &$rows, $limit, $plain) {
            foreach (collect($items) as $item) {
                if (count($rows) >= $limit) {
                    return;
                }

                $qty = (float) ($item['qty'] ?? 1);
                $price = (float) ($item['price'] ?? 0);

                $rows[] = [
                    'section' => $sectionTitle,
                    'depth' => $depth,
                    'name' => $item['name'] ?? 'Position',
                    'qty' => $qty,
                    'unit' => $item['unit'] ?? $item['measure'] ?? '',
                    'price' => $price,
                    'total' => $qty * $price,
                    'desc' => $plain($item['desc_html'] ?? $item['desc'] ?? '', 220),
                    'image' => $item['img'] ?? null,
                    'item_type' => $item['item_type'] ?? null,
                    'supplier' => $item['supplier'] ?? $item['distributor_name'] ?? null,
                    'article_no' => $item['article_no'] ?? $item['distributor_article_no'] ?? null,
                ];

                $children = collect($item['subItems'] ?? []);

                if ($children->isNotEmpty()) {
                    $walkItems($children, $sectionTitle, $depth + 1);
                }
            }
        };

        foreach (collect($template->sections ?? []) as $section) {
            if (count($rows) >= $limit) {
                break;
            }

            $walkItems($section['items'] ?? [], $section['title'] ?? 'Abschnitt', 0);
        }

        return $rows;
    };
@endphp

@once
    @push('style')
        <style>
            :root {
                --ot-bg: #f6f7f9;
                --ot-card: #ffffff;
                --ot-line: #e5e7eb;
                --ot-text: #111827;
                --ot-muted: #6b7280;
                --ot-soft: #f9fafb;
                --ot-primary: #93c21c;
                --ot-primary-2: #7baa18;
                --ot-primary-soft: #f4fae7;
                --ot-blue: #74b2d4;
                --ot-blue-soft: #eff6ff;
                --ot-yellow: #f59e0b;
                --ot-yellow-soft: #fffbeb;
                --ot-green: #10b981;
                --ot-green-soft: #ecfdf5;
                --ot-red: #ef4444;
                --ot-red-soft: #fef2f2;
                --ot-shadow: 0 16px 32px -24px rgba(15, 23, 42, .32);
                --ot-radius: 16px;
            }

            .ot-page {
                font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: var(--ot-text);
                background: var(--ot-bg);
                margin: -1px;
            }

            .ot-shell {
                max-width: 1760px;
                margin: 0 auto;
                padding: 18px;
            }

            .ot-topbar {
                display: grid;
                grid-template-columns: minmax(260px, 1fr) auto;
                gap: 16px;
                align-items: end;
                margin-bottom: 14px;
            }

            @media(max-width:960px) {
                .ot-topbar {
                    grid-template-columns: 1fr;
                }
            }

            .ot-title {
                font-size: 23px;
                font-weight: 950;
                letter-spacing: -.035em;
                color: #0f172a;
                margin: 0;
                line-height: 1.1;
            }

            .ot-subtitle {
                color: var(--ot-muted);
                font-size: 13px;
                margin-top: 5px;
            }

            .ot-breadcrumb {
                display: flex;
                align-items: center;
                gap: 7px;
                color: var(--ot-muted);
                font-size: 12px;
                margin-top: 8px;
            }

            .ot-breadcrumb a {
                color: var(--ot-muted);
                font-weight: 800;
                text-decoration: none;
            }

            .ot-breadcrumb strong {
                color: var(--ot-text);
                font-weight: 900;
            }

            .ot-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                flex-wrap: wrap;
            }

            .ot-btn {
                border: 0;
                background: var(--ot-primary);
                color: #fff;
                border-radius: 10px;
                padding: 9px 13px;
                font-size: 12px;
                font-weight: 950;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                text-decoration: none;
                transition: .18s ease;
                min-height: 38px;
            }

            .ot-btn:hover {
                background: var(--ot-primary-2);
                color: #fff;
                text-decoration: none;
            }

            .ot-btn-soft {
                border: 1px solid var(--ot-line);
                background: #fff;
                color: #111827;
                border-radius: 10px;
                padding: 8px 11px;
                font-size: 12px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                text-decoration: none;
                min-height: 36px;
                transition: .18s ease;
            }

            .ot-btn-soft:hover {
                border-color: #cbd5e1;
                background: #f8fafc;
                color: #111827;
                text-decoration: none;
            }

            .ot-btn-icon {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                border: 1px solid var(--ot-line);
                background: #fff;
                color: var(--ot-muted);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: .18s ease;
                text-decoration: none;
            }

            .ot-btn-icon:hover {
                background: #f8fafc;
                color: #111827;
                border-color: #cbd5e1;
            }

            .ot-btn-icon.is-blue {
                background: var(--ot-blue-soft);
                color: #2563eb;
                border-color: #dbeafe;
            }

            .ot-btn-icon.is-green {
                background: var(--ot-green-soft);
                color: #047857;
                border-color: #c7f2df;
            }

            .ot-btn-icon.is-yellow {
                background: var(--ot-yellow-soft);
                color: #b45309;
                border-color: #fde7b0;
            }

            .ot-metrics {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
                margin-bottom: 12px;
            }

            @media(max-width:1050px) {
                .ot-metrics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:620px) {
                .ot-metrics {
                    grid-template-columns: 1fr;
                }
            }

            .ot-metric {
                background: #fff;
                border: 1px solid var(--ot-line);
                border-radius: 14px;
                padding: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
                min-height: 72px;
            }

            .ot-metric-icon {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .ot-metric-icon.total {
                background: var(--ot-blue-soft);
                color: #2563eb;
            }

            .ot-metric-icon.fav {
                background: var(--ot-yellow-soft);
                color: #b45309;
            }

            .ot-metric-icon.stamp {
                background: var(--ot-green-soft);
                color: #047857;
            }

            .ot-metric-icon.used {
                background: #f3f4f6;
                color: #475569;
            }

            .ot-metric-label {
                color: var(--ot-muted);
                font-size: 10px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .ot-metric-value {
                color: #0f172a;
                font-size: 22px;
                font-weight: 950;
                line-height: 1;
                margin-top: 3px;
            }

            .ot-command {
                position: relative;
                z-index: 30;
                background: #fff;
                border: 1px solid var(--ot-line);
                border-radius: 16px;
                padding: 10px;
                display: grid;
                grid-template-columns: 340px 240px 240px auto;
                gap: 9px;
                align-items: center;
                margin-bottom: 12px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
            }

            @media(max-width:1260px) {
                .ot-command {
                    grid-template-columns: 1fr 240px auto;
                }

                .ot-command {
                    grid-template-columns: 1fr 240px 240px auto;
                }
            }

            @media(max-width:860px) {
                .ot-command {
                    grid-template-columns: 1fr;
                }
            }

            .ot-search-wrap {
                position: relative;
            }

            .ot-select-wrap {
                position: relative;
                min-width: 0;
            }

            .ot-native-hidden-select {
                width: 100% !important;
                height: 38px !important;
            }

            .ot-input {
                width: 100%;
                height: 38px;
                border: 1px solid var(--ot-line);
                border-radius: 11px;
                background: #f8fafc;
                padding: 0 12px 0 36px;
                color: #0f172a;
                font-size: 13px;
                outline: 0;
                transition: .18s ease;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: 12px center;
                background-size: 15px;
            }

            .ot-input:focus {
                background-color: #fff;
                border-color: var(--ot-primary);
                box-shadow: 0 0 0 3px var(--ot-primary-soft);
            }

            .ot-suggestions {
                position: absolute;
                left: 0;
                right: 0;
                top: calc(100% + 6px);
                z-index: 1000;
                background: #fff;
                border: 1px solid var(--ot-line);
                border-radius: 14px;
                box-shadow: var(--ot-shadow);
                display: none;
                overflow: hidden;
                max-height: 300px;
                overflow-y: auto;
            }

            .ot-suggestion {
                padding: 9px 11px;
                border-bottom: 1px solid #f1f5f9;
                cursor: pointer;
            }

            .ot-suggestion:hover {
                background: #f8fafc;
            }

            .ot-suggestion-title {
                color: #0f172a;
                font-size: 13px;
                font-weight: 900;
            }

            .ot-suggestion-meta {
                color: var(--ot-muted);
                font-size: 11px;
                margin-top: 2px;
            }

            .ot-employee-strip {
                display: flex;
                gap: 6px;
                overflow-x: auto;
                align-items: center;
                min-width: 0;
                padding-bottom: 1px;
            }

            .ot-employee-chip {
                height: 38px;
                border: 1px solid var(--ot-line);
                border-radius: 999px;
                background: #fff;
                color: #111827;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 4px 9px 4px 4px;
                min-width: max-content;
                transition: .18s ease;
            }

            .ot-employee-chip:hover,
            .ot-employee-chip.active {
                border-color: var(--ot-primary);
                background: var(--ot-primary-soft);
                color: #111827;
                text-decoration: none;
            }

            .ot-employee-img,
            .ot-employee-fallback {
                width: 28px;
                height: 28px;
                border-radius: 999px;
                flex: 0 0 auto;
            }

            .ot-employee-img {
                object-fit: cover;
                border: 1px solid #e5e7eb;
            }

            .ot-employee-fallback {
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--ot-blue-soft);
                color: #2563eb;
                font-size: 10px;
                font-weight: 950;
                border: 1px solid #dbeafe;
            }

            .ot-employee-name {
                font-size: 11px;
                font-weight: 900;
            }

            .ot-cards {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
            }

            @media(max-width:1660px) {
                .ot-cards {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media(max-width:1160px) {
                .ot-cards {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:760px) {
                .ot-cards {
                    grid-template-columns: 1fr;
                }
            }

            .ot-card {
                background: #fff;
                border: 1px solid var(--ot-line);
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
                transition: .18s ease;
                min-width: 0;
            }

            .ot-card:hover {
                border-color: #cbd5e1;
                box-shadow: var(--ot-shadow);
                transform: translateY(-1px);
            }

            .ot-card-line {
                height: 4px;
                background: var(--ot-blue);
            }

            .ot-card-body {
                padding: 12px;
            }

            .ot-card-head {
                display: grid;
                grid-template-columns: 42px 1fr auto;
                gap: 9px;
                align-items: start;
                min-width: 0;
            }

            .ot-logo,
            .ot-logo-fallback {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                border: 1px solid var(--ot-line);
                background: #fff;
                flex: 0 0 auto;
            }

            .ot-logo {
                object-fit: contain;
                padding: 5px;
            }

            .ot-logo-fallback {
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                font-weight: 950;
                background: #f8fafc;
            }

            .ot-card-title {
                color: #0f172a;
                font-size: 14px;
                font-weight: 950;
                line-height: 1.25;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                min-height: 35px;
            }

            .ot-card-sub {
                color: var(--ot-muted);
                font-size: 11px;
                font-weight: 700;
                margin-top: 3px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .ot-badges {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 4px;
            }

            .ot-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                padding: 4px 7px;
                font-size: 10px;
                font-weight: 950;
                white-space: nowrap;
            }

            .ot-pill.green {
                background: var(--ot-green-soft);
                color: #047857;
            }

            .ot-pill.yellow {
                background: var(--ot-yellow-soft);
                color: #b45309;
            }

            .ot-pill.blue {
                background: var(--ot-blue-soft);
                color: #2563eb;
            }

            .ot-pill.gray {
                background: #f3f4f6;
                color: #475569;
            }

            .ot-main-price {
                margin-top: 10px;
                display: grid;
                grid-template-columns: 1fr auto;
                gap: 8px;
                align-items: end;
                border: 1px solid rgba(147, 194, 28, .22);
                background: linear-gradient(180deg, #fbfef4, #ffffff);
                border-radius: 13px;
                padding: 9px;
            }

            .ot-price-label {
                color: #6c970f;
                font-size: 10px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .ot-price-value {
                color: #0f172a;
                font-size: 19px;
                font-weight: 950;
                margin-top: 2px;
                line-height: 1;
            }

            .ot-price-sub {
                color: var(--ot-muted);
                font-size: 10px;
                font-weight: 800;
                text-align: right;
            }

            .ot-mini-grid {
                margin-top: 9px;
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
            }

            .ot-mini {
                border: 1px solid var(--ot-line);
                border-radius: 10px;
                padding: 7px;
                min-width: 0;
                background: #fbfcfd;
            }

            .ot-mini-label {
                color: var(--ot-muted);
                font-size: 9px;
                font-weight: 950;
                letter-spacing: .05em;
                text-transform: uppercase;
            }

            .ot-mini-value {
                color: #111827;
                font-size: 11px;
                font-weight: 900;
                margin-top: 2px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .ot-card-footer {
                margin-top: 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                border-top: 1px solid #f1f5f9;
                padding-top: 10px;
            }

            .ot-people-mini {
                min-width: 0;
                color: var(--ot-muted);
                font-size: 10px;
                font-weight: 800;
                line-height: 1.45;
            }

            .ot-people-mini strong {
                color: #111827;
                font-weight: 950;
            }

            .ot-card-buttons {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 6px;
                flex: 0 0 auto;
            }

            .ot-empty {
                border: 1px dashed var(--ot-line);
                background: #fff;
                border-radius: 16px;
                padding: 46px;
                text-align: center;
                color: var(--ot-muted);
                font-weight: 850;
            }

            .ot-pagination {
                margin-top: 14px;
                background: #fff;
                border: 1px solid var(--ot-line);
                border-radius: 14px;
                padding: 11px 12px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
            }

            .ot-pagination .pagination {
                margin: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .ot-pagination .page-item .page-link {
                border-radius: 9px !important;
                border: 1px solid var(--ot-line);
                color: #111827;
                padding: 7px 10px;
                line-height: 1.1;
                box-shadow: none !important;
                font-size: 12px;
            }

            .ot-pagination .page-item.active .page-link {
                background: var(--ot-primary);
                border-color: var(--ot-primary);
                color: #fff;
            }

            .ot-modal-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1400;
                background: rgba(15, 23, 42, .55);
                backdrop-filter: blur(3px);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 18px;
                opacity: 0;
                pointer-events: none;
                transition: .18s ease;
            }

            .ot-modal-backdrop.open {
                opacity: 1;
                pointer-events: auto;
            }

            .ot-modal {
                width: 100%;
                max-width: 900px;
                background: #fff;
                border: 1px solid rgba(226, 232, 240, .9);
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 24px 60px -24px rgba(15, 23, 42, .45);
                transform: translateY(10px) scale(.985);
                transition: .18s ease;
            }

            .ot-modal-backdrop.open .ot-modal {
                transform: translateY(0) scale(1);
            }

            .ot-modal.sm {
                max-width: 720px;
            }

            .ot-modal.xl {
                max-width: 1120px;
            }

            .ot-modal-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 14px 16px;
                border-bottom: 1px solid var(--ot-line);
                background: #fbfcfd;
            }

            .ot-modal-title {
                color: #0f172a;
                font-size: 16px;
                font-weight: 950;
                margin: 0;
            }

            .ot-modal-subtitle {
                color: var(--ot-muted);
                font-size: 12px;
                margin-top: 3px;
            }

            .ot-modal-body {
                padding: 16px;
                max-height: 72vh;
                overflow-y: auto;
            }

            .ot-modal-footer {
                padding: 12px 16px;
                background: #fbfcfd;
                border-top: 1px solid var(--ot-line);
                display: flex;
                justify-content: flex-end;
                gap: 8px;
                flex-wrap: wrap;
            }

            .ot-form-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }

            @media(max-width:860px) {
                .ot-form-grid {
                    grid-template-columns: 1fr;
                }
            }

            .ot-field {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .ot-label {
                color: #374151;
                font-size: 12px;
                font-weight: 900;
            }

            .ot-help {
                color: var(--ot-muted);
                font-size: 11px;
            }

            .ot-control {
                width: 100%;
                height: 40px;
                border: 1px solid var(--ot-line);
                border-radius: 10px;
                background: #fff;
                padding: 0 10px;
                outline: 0;
                font-size: 13px;
            }

            .ot-check {
                border: 1px solid var(--ot-line);
                border-radius: 14px;
                background: #fff;
                padding: 12px;
            }

            .ot-check.warning {
                background: var(--ot-yellow-soft);
                border-color: #fde7b0;
                color: #92400e;
            }

            .ot-check.success {
                background: var(--ot-green-soft);
                border-color: #c7f2df;
                color: #065f46;
            }

            .ot-result-grid {
                display: grid;
                gap: 9px;
                margin-top: 10px;
            }

            .ot-position-table {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .ot-position-row {
                border: 1px solid var(--ot-line);
                border-radius: 14px;
                background: #fff;
                padding: 10px;
                display: grid;
                grid-template-columns: 46px 1fr auto;
                gap: 10px;
                align-items: start;
            }

            .ot-position-row.child {
                margin-left: 22px;
                border-left: 4px solid var(--ot-blue);
            }

            .ot-position-img {
                width: 46px;
                height: 46px;
                border-radius: 10px;
                border: 1px solid var(--ot-line);
                object-fit: cover;
                background: #f8fafc;
            }

            .ot-position-name {
                color: #0f172a;
                font-size: 13px;
                font-weight: 950;
            }

            .ot-position-meta {
                color: var(--ot-muted);
                font-size: 11px;
                font-weight: 700;
                margin-top: 3px;
            }

            .ot-position-desc {
                color: #374151;
                font-size: 12px;
                line-height: 1.45;
                margin-top: 7px;
            }

            .ot-position-price {
                color: #0f172a;
                font-size: 13px;
                font-weight: 950;
                white-space: nowrap;
                text-align: right;
            }

            .ot-detail-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            @media(max-width:760px) {
                .ot-detail-grid {
                    grid-template-columns: 1fr;
                }
            }

            .ot-detail-box {
                border: 1px solid var(--ot-line);
                border-radius: 13px;
                background: #fbfcfd;
                padding: 10px;
                min-width: 0;
            }

            .ot-detail-label {
                color: var(--ot-muted);
                font-size: 10px;
                font-weight: 950;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .ot-detail-value {
                color: #0f172a;
                font-size: 13px;
                font-weight: 900;
                margin-top: 4px;
                line-height: 1.4;
                overflow-wrap: anywhere;
            }

            .ot-toast-wrap {
                position: fixed;
                right: 18px;
                bottom: 18px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 9px;
                pointer-events: none;
            }

            .ot-toast {
                pointer-events: auto;
                min-width: 280px;
                max-width: 360px;
                background: #fff;
                border: 1px solid var(--ot-line);
                border-radius: 14px;
                box-shadow: var(--ot-shadow);
                padding: 11px;
                display: flex;
                gap: 9px;
                align-items: flex-start;
            }

            .ot-toast-icon {
                width: 32px;
                height: 32px;
                border-radius: 11px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .ot-toast-icon.ok {
                background: var(--ot-green-soft);
                color: #047857;
            }

            .ot-toast-icon.bad {
                background: var(--ot-red-soft);
                color: var(--ot-red);
            }

            .ot-toast-title {
                color: #0f172a;
                font-size: 13px;
                font-weight: 950;
                margin: 0;
            }

            .ot-toast-msg {
                color: #374151;
                font-size: 12px;
                margin: 3px 0 0 0;
                line-height: 1.4;
            }

            .ot-toast-x {
                margin-left: auto;
                border: 0;
                background: transparent;
                color: var(--ot-muted);
                cursor: pointer;
            }

            /* =========================
               SELECT2 ENTERPRISE FIX
               ========================= */

            .ot-select-wrap,
            .ot-field {
                position: relative !important;
                min-width: 0 !important;
            }

            .ot-native-hidden-select {
                width: 100% !important;
                height: 38px !important;
            }

            .ot-select-wrap .select2-container,
            .ot-field .select2-container,
            .select2-container {
                width: 100% !important;
                display: block !important;
            }

            .select2-hidden-accessible {
                border: 0 !important;
                clip: rect(0 0 0 0) !important;
                clip-path: inset(50%) !important;
                height: 1px !important;
                margin: -1px !important;
                overflow: hidden !important;
                padding: 0 !important;
                position: absolute !important;
                width: 1px !important;
                white-space: nowrap !important;
            }

            .select2-container--default .select2-selection--single {
                height: 38px !important;
                min-height: 38px !important;
                border: 1px solid var(--ot-line) !important;
                border-radius: 11px !important;
                background: #fff !important;
                display: flex !important;
                align-items: center !important;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
                outline: none !important;
                overflow: hidden !important;
            }

            .select2-container--default.select2-container--focus .select2-selection--single,
            .select2-container--default.select2-container--open .select2-selection--single {
                border-color: var(--ot-primary) !important;
                box-shadow: 0 0 0 3px var(--ot-primary-soft) !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #0f172a !important;
                font-size: 12px !important;
                font-weight: 850 !important;
                line-height: 36px !important;
                padding-left: 12px !important;
                padding-right: 34px !important;
                width: 100% !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__placeholder {
                color: #64748b !important;
                font-weight: 800 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
                right: 8px !important;
                top: 1px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__clear {
                height: 36px !important;
                line-height: 34px !important;
                margin-right: 22px !important;
                color: #64748b !important;
                font-size: 16px !important;
            }

            .select2-container--open {
                z-index: 99999 !important;
            }

            .select2-dropdown {
                z-index: 99999 !important;
                background: #fff !important;
                border: 1px solid var(--ot-line) !important;
                border-radius: 13px !important;
                overflow: hidden !important;
                box-shadow: 0 18px 44px -20px rgba(15, 23, 42, .45) !important;
            }

            .select2-container--default .select2-dropdown--below {
                margin-top: 6px !important;
            }

            .select2-container--default .select2-dropdown--above {
                margin-top: -6px !important;
            }

            .select2-container--default .select2-search--dropdown {
                padding: 8px !important;
                background: #fff !important;
                border-bottom: 1px solid #f1f5f9 !important;
            }

            .select2-container--default .select2-search--dropdown .select2-search__field {
                height: 34px !important;
                border: 1px solid var(--ot-line) !important;
                border-radius: 9px !important;
                padding: 0 10px !important;
                outline: none !important;
                font-size: 12px !important;
                background: #f8fafc !important;
            }

            .select2-container--default .select2-search--dropdown .select2-search__field:focus {
                background: #fff !important;
                border-color: var(--ot-primary) !important;
                box-shadow: 0 0 0 3px var(--ot-primary-soft) !important;
            }

            .select2-results,
            .select2-results__options {
                background: #fff !important;
            }

            .select2-results__options {
                max-height: 280px !important;
                overflow-y: auto !important;
            }

            .select2-container--default .select2-results__option {
                background: #fff !important;
                color: #0f172a !important;
                font-size: 12px !important;
                padding: 9px 10px !important;
            }

            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background: var(--ot-primary-soft) !important;
                color: #0f172a !important;
            }

            .select2-container--default .select2-results__option[aria-selected=true] {
                background: #eef7d6 !important;
                color: #0f172a !important;
                font-weight: 900 !important;
            }

            .ot-select-option {
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 0;
            }

            .ot-select-option-avatar,
            .ot-select-option-initials {
                width: 26px;
                height: 26px;
                border-radius: 999px;
                flex: 0 0 auto;
            }

            .ot-select-option-avatar {
                object-fit: cover;
                border: 1px solid var(--ot-line);
                background: #fff;
            }

            .ot-select-option-initials {
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--ot-blue-soft);
                color: #2563eb;
                border: 1px solid #dbeafe;
                font-size: 10px;
                font-weight: 950;
            }

            .ot-select-option-main {
                min-width: 0;
            }

            .ot-select-option-title {
                font-size: 12px;
                font-weight: 950;
                color: #0f172a;
                line-height: 1.2;
            }

            .ot-select-option-sub {
                font-size: 11px;
                color: var(--ot-muted);
                margin-top: 1px;
                line-height: 1.2;
            }

    @endpush
@endonce

@section('content')
    <div class="ot-page">
        <div class="ot-shell">
            <div class="ot-topbar">
                <div>
                    <h1 class="ot-title">Angebot Vorlagen</h1>
                    <div class="ot-subtitle">Kompakte Template-Bibliothek mit Preis, Nutzung, Mitarbeiterdaten und
                        Detail-Modals.</div>
                    <div class="ot-breadcrumb">
                        <a href="{{ url('/employee_dashboard') }}">Home</a>
                        <span>›</span>
                        <strong>Angebot Vorlagen</strong>
                    </div>
                </div>

                <div class="ot-actions">
                    <a href="{{ url('offers/wizard') }}" class="ot-btn">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        Neue Vorlage
                    </a>
                </div>
            </div>

            <div class="ot-metrics">
                <div class="ot-metric">
                    <div class="ot-metric-icon total">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                    <div>
                        <div class="ot-metric-label">Gesamt</div>
                        <div class="ot-metric-value">{{ $totalCount }}</div>
                    </div>
                </div>

                <div class="ot-metric">
                    <div class="ot-metric-icon fav">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="m12 17.27 5.18 3.13-1.64-5.89L20 10.56l-5.92-.5L12 4.6l-2.08 5.46-5.92.5 4.46 3.95-1.64 5.89z" />
                        </svg>
                    </div>
                    <div>
                        <div class="ot-metric-label">Favoriten</div>
                        <div class="ot-metric-value">{{ $favoritesCount }}</div>
                    </div>
                </div>

                <div class="ot-metric">
                    <div class="ot-metric-icon stamp">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    </div>
                    <div>
                        <div class="ot-metric-label">Gestempelt</div>
                        <div class="ot-metric-value">{{ $stampedCount }}</div>
                    </div>
                </div>

                <div class="ot-metric">
                    <div class="ot-metric-icon used">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M12 3v18" />
                        </svg>
                    </div>
                    <div>
                        <div class="ot-metric-label">Verwendet</div>
                        <div class="ot-metric-value">{{ $usedCount }}</div>
                    </div>
                </div>
            </div>

            <form action="{{ route('offer-templates.index') }}" method="GET" class="ot-command" id="templateFilterForm">
                <div class="ot-search-wrap">
                    <input type="text" class="ot-input" id="templateSmartSearch" name="q" value="{{ request('q') }}"
                        autocomplete="off" placeholder="Template, Position, Produkt, Firma...">
                    <div class="ot-suggestions" id="templateSuggestions"></div>
                </div>

                <div class="ot-select-wrap">
                    <select id="articleGroupFilter" name="article_group_id" class="ot-control ot-native-hidden-select">
                        @if($selectedArticleGroup)
                            <option value="{{ $selectedArticleGroup->id }}" selected>
                                {{ $selectedArticleGroup->article_group ?? $selectedArticleGroup->name ?? $selectedArticleGroup->title ?? ('Artikelgruppe #' . $selectedArticleGroup->id) }}
                            </option>
                        @endif
                    </select>
                </div>

                <div class="ot-select-wrap">
                    <select id="employeeFilter" name="employee_id" class="ot-control ot-native-hidden-select">
                        @if($selectedEmployee)
                            <option value="{{ $selectedEmployee->id }}" selected>
                                {{ $employeeName($selectedEmployee) }}
                            </option>
                        @elseif(request('employee_id'))
                            @php
                                $fallbackSelectedEmployee = $activeEmployees->firstWhere('id', (int) request('employee_id'));
                            @endphp
                            @if($fallbackSelectedEmployee)
                                <option value="{{ $fallbackSelectedEmployee->id }}" selected>
                                    {{ $employeeName($fallbackSelectedEmployee) }}
                                </option>
                            @endif
                        @endif
                    </select>
                </div>

                <div class="ot-actions">
                    <button class="ot-btn-soft" type="submit">Suchen</button>
                    @if(request('q') || request('article_group_id') || request('employee_id'))
                        <a href="{{ route('offer-templates.index') }}" class="ot-btn-soft">Reset</a>
                    @endif
                </div>
            </form>

            @if($templates->count())
                <div class="ot-cards">
                    @foreach($templates as $template)
                        @php
                            $brandColor = $template->brand_color ?: '#74b2d4';
                            $productName = $template->articleGroup?->article_group
                                ?? $template->articleGroup?->name
                                ?? $template->articleGroup?->title
                                ?? '—';
                            $departmentName = $template->department?->department_name ?? $template->department?->name ?? '—';
                            $brandName = $template->brand?->name ?? '—';
                            $distributorName = $template->distributor?->name ?? '—';
                            $companyName = $template->company_name ?: 'Keine Firma';

                            $totals = $templateTotals($template);
                            $positionRows = $templatePreviewPositions($template, 999);

                            $favoriteEmployees = collect($template->favoritedByEmployees ?? []);
                            $favoriteCount = $favoriteEmployees->count();

                            $creatorName = $employeeName($template->creator);
                            $lastUsedByName = $employeeName($template->lastUsedByEmployee);
                            $stampedByName = $employeeName($template->stampedByEmployee);

                            $statusClass = $template->stamp ? 'green' : ($template->is_favorite ? 'yellow' : 'gray');
                            $statusLabel = $template->stamp ? 'Gestempelt' : ($template->is_favorite ? 'Favorit' : 'Normal');

                            $mainPrice = (float) ($template->leistung ?? 0);
                            $positionTotal = (float) $totals['items_total'];

                            $favoriteNames = $favoriteEmployees->map(fn($emp) => $employeeName($emp))->filter()->values()->all();

                            $detailsPayload = [
                                'id' => $template->id,
                                'name' => $template->name,
                                'company' => $companyName,
                                'price' => $money($mainPrice),
                                'position_total' => $money($positionTotal),
                                'product' => $productName,
                                'department' => $departmentName,
                                'brand' => $brandName,
                                'distributor' => $distributorName,
                                'sections' => $totals['section_count'],
                                'positions' => $totals['position_count'],
                                'sub_positions' => $totals['sub_position_count'],
                                'usage_count' => (int) $template->usage_count,
                                'creator' => $creatorName,
                                'stamped_by' => $template->stamped_at ? $stampedByName . ' · ' . $template->stamped_at->format('d.m.Y') : '—',
                                'last_used_by' => $template->last_used_at ? $lastUsedByName . ' · ' . $template->last_used_at->format('d.m.Y H:i') : '—',
                                'favorites' => $favoriteNames,
                                'description' => $template->description ?: 'Keine Beschreibung',
                            ];
                        @endphp

                        <article class="ot-card">
                            <div class="ot-card-line" style="background:{{ $brandColor }}"></div>

                            <div class="ot-card-body">
                                <div class="ot-card-head">
                                    @if($template->brand_logo_url)
                                        <img src="{{ $template->brand_logo_url }}" alt="{{ $template->name }}" class="ot-logo">
                                    @else
                                        <div class="ot-logo-fallback" style="border-color:{{ $brandColor }};color:{{ $brandColor }};">
                                            {{ strtoupper(mb_substr($template->name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div style="min-width:0;">
                                        <div class="ot-card-title">{{ $template->name }}</div>
                                        <div class="ot-card-sub">{{ $companyName }}</div>
                                    </div>

                                    <div class="ot-badges">
                                        <span class="ot-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                                        <span class="ot-pill blue">#{{ $template->id }}</span>
                                    </div>
                                </div>

                                <div class="ot-main-price">
                                    <div>
                                        <div class="ot-price-label">Leistung</div>
                                        <div class="ot-price-value">{{ $money($mainPrice) }}</div>
                                    </div>
                                    <div class="ot-price-sub">
                                        Pos.<br>{{ $money($positionTotal) }}
                                    </div>
                                </div>

                                <div class="ot-mini-grid">
                                    <div class="ot-mini">
                                        <div class="ot-mini-label">Produkt</div>
                                        <div class="ot-mini-value">{{ $productName }}</div>
                                    </div>

                                    <div class="ot-mini">
                                        <div class="ot-mini-label">Positionen</div>
                                        <div class="ot-mini-value">{{ $totals['position_count'] }} /
                                            {{ $totals['sub_position_count'] }}</div>
                                    </div>

                                    <div class="ot-mini">
                                        <div class="ot-mini-label">Nutzung</div>
                                        <div class="ot-mini-value">{{ (int) $template->usage_count }}x</div>
                                    </div>
                                </div>

                                <div class="ot-card-footer">
                                    <div class="ot-people-mini">
                                        <div>Von <strong>{{ Str::limit($creatorName, 22) }}</strong></div>
                                        <div>Zuletzt
                                            <strong>{{ $template->last_used_at ? Str::limit($lastUsedByName, 18) : '—' }}</strong>
                                        </div>
                                    </div>

                                    <div class="ot-card-buttons">
                                        <a href="{{ route('offers.wizard', ['template_id' => $template->id, 'mode' => 'template', 'edit_template' => 1, 'source' => 'template_edit']) }}"
                                           class="ot-btn-icon is-blue"
                                           title="Vorlage im Konfigurator bearbeiten">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                            </svg>
                                        </a>

                                        <button type="button" class="ot-btn-icon is-yellow js-open-positions"
                                            title="Positionen anzeigen" data-template-id="{{ $template->id }}"
                                            data-title="{{ e($template->name) }}">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </button>

                                        <button type="button" class="ot-btn js-use-template" data-template-id="{{ $template->id }}"
                                            data-template-name="{{ e($template->name) }}"
                                            data-template-product-id="{{ $template->article_group_id }}">
                                            Nutzen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <script type="application/json" id="template-positions-{{ $template->id }}">
                                        @json($positionRows)
                                    </script>
                    @endforeach
                </div>
            @else
                <div class="ot-empty">Keine Vorlagen gefunden.</div>
            @endif

            @if($isPaginator && method_exists($templates, 'links') && $templates->hasPages())
                <div class="ot-pagination">
                    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                        <div style="font-size:12px;color:#6b7280;">
                            Zeige <strong>{{ $templates->firstItem() ?? 0 }}</strong>
                            bis <strong>{{ $templates->lastItem() ?? 0 }}</strong>
                            von <strong>{{ $templates->total() }}</strong> Einträgen
                        </div>
                        <div>
                            {{ $templates->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="ot-modal-backdrop" id="templateUseModal">
        <div class="ot-modal xl">
            <div class="ot-modal-head">
                <div>
                    <h3 class="ot-modal-title" id="modalTemplateTitle">Template verwenden</h3>
                    <div class="ot-modal-subtitle">Kunde, Objekt und Produkt auswählen. Danach vorhandene Angebote prüfen.
                    </div>
                </div>

                <button class="ot-btn-icon" type="button" onclick="closeTemplateModal()">
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="ot-modal-body">
                <div class="ot-form-grid">
                    <div class="ot-field">
                        <label class="ot-label">Kunde *</label>
                        <select id="customerSelect" class="ot-control"></select>
                        <div class="ot-help">Name, Firma, Kundennummer, E-Mail, Telefon oder Ort.</div>
                    </div>

                    <div class="ot-field">
                        <label class="ot-label">Objekt / Alternative</label>
                        <select id="objectSelect" class="ot-control"></select>
                        <div class="ot-help">Wird nach Kundenauswahl geladen.</div>
                    </div>

                    <div class="ot-field">
                        <label class="ot-label">Produkt *</label>
                        <select id="productSelect" class="ot-control"></select>
                        <div class="ot-help">Leer = Produkt aus Vorlage.</div>
                    </div>
                </div>

                <div class="ot-field" style="margin-top:12px;">
                    <label class="ot-label">Neuer Ordnername</label>
                    <input type="text" id="folderNameInput" class="ot-control"
                        placeholder="z.B. Angebot Wärmepumpe - Juni 2026">
                </div>

                <div id="templateLoading" class="ot-check" style="display:none;margin-top:12px;">Wird geladen...</div>
                <div id="checkResultBox" style="display:none;margin-top:12px;"></div>
            </div>

            <div class="ot-modal-footer">
                <button type="button" class="ot-btn-soft" onclick="closeTemplateModal()">Abbrechen</button>
                <button type="button" class="ot-btn-soft" id="checkTemplateBtn">Prüfen</button>
                <button type="button" class="ot-btn" id="confirmUseTemplateBtn">Angebot erstellen</button>
            </div>
        </div>
    </div>

    <div class="ot-modal-backdrop" id="templateDetailsModal">
        <div class="ot-modal sm">
            <div class="ot-modal-head">
                <div>
                    <h3 class="ot-modal-title" id="detailsTitle">Template Details</h3>
                    <div class="ot-modal-subtitle">Enterprise Übersicht mit Preis, Nutzung, Personen und Metadaten.</div>
                </div>

                <button class="ot-btn-icon" type="button" onclick="closeDetailsModal()">
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="ot-modal-body">
                <div class="ot-detail-grid" id="detailsGrid"></div>
            </div>

            <div class="ot-modal-footer">
                <button type="button" class="ot-btn-soft" onclick="closeDetailsModal()">Schließen</button>
            </div>
        </div>
    </div>

    <div class="ot-modal-backdrop" id="positionsInfoModal">
        <div class="ot-modal xl">
            <div class="ot-modal-head">
                <div>
                    <h3 class="ot-modal-title" id="positionsModalTitle">Positionsdetails</h3>
                    <div class="ot-modal-subtitle">Alle Positionen, Unterpositionen, Mengen, Einheiten und Preise.</div>
                </div>

                <button class="ot-btn-icon" type="button" onclick="closePositionsModal()">
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="ot-modal-body">
                <div class="ot-position-table" id="positionsModalList"></div>
            </div>

            <div class="ot-modal-footer">
                <button type="button" class="ot-btn-soft" onclick="closePositionsModal()">Schließen</button>
            </div>
        </div>
    </div>

    <div class="ot-toast-wrap" id="toast-wrap"></div>
@endsection

@once
    @push('scripts')
        <script>
            function toast(kind, title, msg) {
                const wrap = document.getElementById('toast-wrap');
                if (!wrap) return;

                const icons = {
                    ok: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="17" height="17"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/></svg>`,
                    bad: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="17" height="17"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
                };

                const el = document.createElement('div');
                el.className = 'ot-toast';
                el.innerHTML = `
                            <div class="ot-toast-icon ${kind}">${icons[kind] || icons.ok}</div>
                            <div style="flex:1;">
                                <p class="ot-toast-title">${escapeHtml(title)}</p>
                                <p class="ot-toast-msg">${escapeHtml(msg)}</p>
                            </div>
                            <button class="ot-toast-x" onclick="this.parentElement.remove()">×</button>
                        `;
                wrap.appendChild(el);
                setTimeout(() => { try { el.remove(); } catch (e) { } }, 4500);
            }

            let selectedTemplateId = null;
            let selectedTemplateName = null;
            let selectedTemplateProductId = null;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function openTemplateModal() {
                document.getElementById('templateUseModal')?.classList.add('open');

                if (window.jQuery && $.fn.select2) {
                    setTimeout(() => {
                        $('#customerSelect, #objectSelect, #productSelect').select2('close');
                        $('.select2-container').css('width', '100%');
                    }, 80);
                }
            }

            function closeTemplateModal() {
                if (window.jQuery && $.fn.select2) {
                    $('#customerSelect, #objectSelect, #productSelect').select2('close');
                }

                document.getElementById('templateUseModal')?.classList.remove('open');

                selectedTemplateId = null;
                selectedTemplateName = null;
                selectedTemplateProductId = null;

                if (window.jQuery && $.fn.select2) {
                    $('#customerSelect').val(null).trigger('change');
                    $('#objectSelect').val(null).trigger('change');
                    $('#productSelect').val(null).trigger('change');
                }

                document.getElementById('folderNameInput').value = '';
                document.getElementById('checkResultBox').style.display = 'none';
                document.getElementById('checkResultBox').innerHTML = '';
                document.getElementById('templateLoading').style.display = 'none';
            }

            function closeDetailsModal() {
                document.getElementById('templateDetailsModal')?.classList.remove('open');
            }

            function closePositionsModal() {
                document.getElementById('positionsInfoModal')?.classList.remove('open');
            }

            function formatCurrency(value) {
                return Number(value || 0).toLocaleString('de-DE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' €';
            }

            function openDetailsModal(data) {
                document.getElementById('detailsTitle').textContent = data.name || 'Template Details';

                const favorites = Array.isArray(data.favorites) && data.favorites.length
                    ? data.favorites.join(', ')
                    : '—';

                const rows = [
                    ['Leistung / Preis', data.price || '—'],
                    ['Positionssumme', data.position_total || '—'],
                    ['Produkt', data.product || '—'],
                    ['Abteilung', data.department || '—'],
                    ['Hersteller', data.brand || '—'],
                    ['Lieferant', data.distributor || '—'],
                    ['Abschnitte / Positionen', `${data.sections || 0} / ${data.positions || 0} / ${data.sub_positions || 0}`],
                    ['Nutzung', `${data.usage_count || 0}x`],
                    ['Erstellt von', data.creator || '—'],
                    ['Gestempelt von', data.stamped_by || '—'],
                    ['Zuletzt genutzt', data.last_used_by || '—'],
                    ['Favorisiert von', favorites],
                    ['Beschreibung', data.description || '—'],
                ];

                document.getElementById('detailsGrid').innerHTML = rows.map(([label, value]) => `
                            <div class="ot-detail-box" ${label === 'Beschreibung' || label === 'Favorisiert von' ? 'style="grid-column:1 / -1;"' : ''}>
                                <div class="ot-detail-label">${escapeHtml(label)}</div>
                                <div class="ot-detail-value">${escapeHtml(value)}</div>
                            </div>
                        `).join('');

                document.getElementById('templateDetailsModal')?.classList.add('open');
            }

            function openPositionsModal(templateId, title) {
                const script = document.getElementById(`template-positions-${templateId}`);
                let positions = [];

                try {
                    positions = script ? JSON.parse(script.textContent || '[]') : [];
                } catch (e) {
                    positions = [];
                }

                document.getElementById('positionsModalTitle').textContent = title || 'Positionsdetails';

                const list = document.getElementById('positionsModalList');

                if (!positions.length) {
                    list.innerHTML = '<div class="ot-check">Keine Positionsdaten in dieser Vorlage.</div>';
                } else {
                    list.innerHTML = positions.map(position => {
                        const isChild = Number(position.depth || 0) > 0;
                        const qty = Number(position.qty || 0).toLocaleString('de-DE', { maximumFractionDigits: 2 });
                        const price = formatCurrency(position.price || 0);
                        const total = formatCurrency(position.total || 0);
                        const image = position.image || '';

                        return `
                                    <div class="ot-position-row ${isChild ? 'child' : ''}">
                                        ${image
                                ? `<img src="${escapeHtml(image)}" class="ot-position-img" alt="">`
                                : `<div class="ot-position-img"></div>`
                            }

                                        <div>
                                            <div class="ot-position-name">${isChild ? '↳ ' : ''}${escapeHtml(position.name || 'Position')}</div>
                                            <div class="ot-position-meta">
                                                ${escapeHtml(position.section || 'Abschnitt')} ·
                                                ${qty} ${escapeHtml(position.unit || '')} × ${price}
                                                ${position.article_no ? ' · Art.-Nr. ' + escapeHtml(position.article_no) : ''}
                                                ${position.supplier ? ' · ' + escapeHtml(position.supplier) : ''}
                                            </div>
                                            ${position.desc ? `<div class="ot-position-desc">${escapeHtml(position.desc)}</div>` : ''}
                                        </div>

                                        <div class="ot-position-price">${total}</div>
                                    </div>
                                `;
                    }).join('');
                }

                document.getElementById('positionsInfoModal')?.classList.add('open');
            }

            function renderCheckResult(data) {
                const box = document.getElementById('checkResultBox');
                const hasAny = data.has_offer || data.has_folder || data.has_detail;

                const warning = hasAny
                    ? `
                                <div class="ot-check warning">
                                    <strong>Achtung: Es gibt bereits Daten für diese Auswahl.</strong>
                                    <div style="font-size:12px;margin-top:5px;">
                                        Angebot: ${data.has_offer ? 'Ja' : 'Nein'} ·
                                        Ordner: ${data.has_folder ? 'Ja' : 'Nein'} ·
                                        Detail: ${data.has_detail ? 'Ja' : 'Nein'}
                                    </div>
                                    <div style="font-size:12px;margin-top:5px;">Du kannst trotzdem ein neues Angebot mit dieser Vorlage erstellen.</div>
                                </div>
                            `
                    : `
                                <div class="ot-check success">
                                    <strong>Keine vorhandenen Angebote gefunden.</strong>
                                    <div style="font-size:12px;margin-top:5px;">Diese Vorlage kann direkt verwendet werden.</div>
                                </div>
                            `;

                const offers = (data.offers || []).map(offer => `
                            <div class="ot-check">
                                <strong>${escapeHtml(offer.offer_no || '#' + offer.id)}</strong>
                                <div style="font-size:12px;color:#6b7280;margin-top:4px;">
                                    Produkt: ${escapeHtml(offer.product)} · Status: ${escapeHtml(offer.status || '-')} · Detail: ${offer.has_detail ? 'Ja' : 'Nein'}
                                </div>
                            </div>
                        `).join('');

                const folders = (data.folders || []).map(folder => `
                            <div class="ot-check">
                                <strong>${escapeHtml(folder.name)}</strong>
                                <div style="font-size:12px;color:#6b7280;margin-top:4px;">
                                    Angebot: ${escapeHtml(folder.offer_no || '-')} · Status: ${escapeHtml(folder.status || '-')} · Detail: ${folder.has_detail ? 'Ja' : 'Nein'}
                                </div>
                            </div>
                        `).join('');

                box.innerHTML = `${warning}<div class="ot-result-grid">${offers}${folders}</div>`;
                box.style.display = 'block';
            }

            async function checkTemplate() {
                if (!selectedTemplateId) {
                    toast('bad', 'Fehler', 'Template fehlt.');
                    return;
                }

                const customerId = $('#customerSelect').val();
                const alternativeId = $('#objectSelect').val();
                const productId = $('#productSelect').val() || selectedTemplateProductId || '';

                if (!customerId) {
                    toast('bad', 'Kunde fehlt', 'Bitte zuerst einen Kunden auswählen.');
                    return;
                }

                document.getElementById('templateLoading').style.display = 'block';

                const params = new URLSearchParams({
                    customer_id: customerId,
                    alternative_id: alternativeId || '',
                    product_id: productId
                });

                try {
                    const response = await fetch(`/offer-templates/${selectedTemplateId}/check?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Prüfung fehlgeschlagen.');
                    }

                    renderCheckResult(data);
                } catch (error) {
                    toast('bad', 'Prüfung fehlgeschlagen', error.message || 'Bitte Route und Controller prüfen.');
                } finally {
                    document.getElementById('templateLoading').style.display = 'none';
                }
            }

            async function useTemplate() {
                if (!selectedTemplateId) {
                    toast('bad', 'Fehler', 'Template fehlt.');
                    return;
                }

                const customerId = $('#customerSelect').val();
                const alternativeId = $('#objectSelect').val();
                const productId = $('#productSelect').val() || selectedTemplateProductId || '';
                const folderName = document.getElementById('folderNameInput').value || '';

                if (!customerId) {
                    toast('bad', 'Kunde fehlt', 'Bitte zuerst einen Kunden auswählen.');
                    return;
                }

                if (!productId) {
                    toast('bad', 'Produkt fehlt', 'Bitte ein Produkt auswählen.');
                    return;
                }

                const btn = document.getElementById('confirmUseTemplateBtn');
                btn.disabled = true;
                btn.textContent = 'Wird erstellt...';

                try {
                    const response = await fetch(`/offer-templates/${selectedTemplateId}/use`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            customer_id: customerId,
                            alternative_id: alternativeId,
                            product_id: productId,
                            folder_name: folderName
                        })
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Template konnte nicht verwendet werden.');
                    }

                    toast('ok', 'Erstellt', data.message || 'Angebot wurde erstellt.');
                    window.location.href = data.redirect_url;
                } catch (error) {
                    toast('bad', 'Fehler beim Erstellen', error.message || 'Bitte Controller prüfen.');
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Angebot erstellen';
                }
            }

            function initTemplateSuggestions() {
                const input = document.getElementById('templateSmartSearch');
                const box = document.getElementById('templateSuggestions');

                if (!input || !box) return;

                let timer = null;

                input.addEventListener('input', function () {
                    const q = this.value.trim();

                    clearTimeout(timer);

                    if (q.length < 2) {
                        box.style.display = 'none';
                        box.innerHTML = '';
                        return;
                    }

                    timer = setTimeout(async () => {
                        try {
                            const url = new URL(@json(route('offer-templates.search.suggestions')), window.location.origin);
                            url.searchParams.set('q', q);
                            url.searchParams.set('article_group_id', $('#articleGroupFilter').val() || '');

                            const response = await fetch(url.toString(), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();
                            const results = data.results || [];

                            if (!results.length) {
                                box.style.display = 'none';
                                box.innerHTML = '';
                                return;
                            }

                            box.innerHTML = results.map(item => `
                                        <div class="ot-suggestion" data-value="${escapeHtml(item.text || '')}">
                                            <div class="ot-suggestion-title">${escapeHtml(item.text || '')}</div>
                                            <div class="ot-suggestion-meta">${escapeHtml(item.meta || '')}</div>
                                        </div>
                                    `).join('');

                            box.style.display = 'block';
                        } catch (e) {
                            box.style.display = 'none';
                            box.innerHTML = '';
                        }
                    }, 220);
                });

                box.addEventListener('click', function (e) {
                    const item = e.target.closest('.ot-suggestion');
                    if (!item) return;

                    input.value = item.dataset.value || '';
                    box.style.display = 'none';
                    document.getElementById('templateFilterForm').submit();
                });

                document.addEventListener('click', function (e) {
                    if (!box.contains(e.target) && e.target !== input) {
                        box.style.display = 'none';
                    }
                });
            }

            function initTemplateSelect2() {
                if (!window.jQuery || !$.fn.select2) {
                    console.error('Select2 is not loaded. Please load Select2 CSS/JS in admin.layouts.app.');
                    return;
                }

                const $filterForm = $('#templateFilterForm');
                const $filterParent = $('.ot-command');
                const $templateModal = $('#templateUseModal');

                function destroySelect2(selector) {
                    const $el = $(selector);

                    if ($el.length && $el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                }

                destroySelect2('#articleGroupFilter');
                destroySelect2('#employeeFilter');
                destroySelect2('#customerSelect');
                destroySelect2('#objectSelect');
                destroySelect2('#productSelect');

                $('#articleGroupFilter').select2({
                    dropdownParent: $filterParent,
                    placeholder: 'Artikelgruppe',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0,
                    ajax: {
                        url: @json(route('offer-templates.search.article-groups')),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || ''
                        }),
                        processResults: data => data
                    }
                });

                $('#employeeFilter').select2({
                    dropdownParent: $filterParent,
                    placeholder: 'Mitarbeiter',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0,
                    ajax: {
                        url: @json(route('offer-templates.search.employees')),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || ''
                        }),
                        processResults: data => data
                    },
                    templateResult: function (item) {
                        if (!item.id) {
                            return item.text;
                        }

                        const img = item.image_url
                            ? `<img src="${escapeHtml(item.image_url)}" class="ot-select-option-avatar" alt="">`
                            : `<span class="ot-select-option-initials">${escapeHtml(item.initials || '?')}</span>`;

                        const sub = item.meta
                            ? `<div class="ot-select-option-sub">${escapeHtml(item.meta)}</div>`
                            : '';

                        return $(`
                            <div class="ot-select-option">
                                ${img}
                                <div class="ot-select-option-main">
                                    <div class="ot-select-option-title">${escapeHtml(item.text || '')}</div>
                                    ${sub}
                                </div>
                            </div>
                        `);
                    },
                    templateSelection: function (item) {
                        return item.text || 'Mitarbeiter';
                    }
                });

                $('#articleGroupFilter, #employeeFilter')
                    .off('change.templateFilter')
                    .on('change.templateFilter', function () {
                        $filterForm.trigger('submit');
                    });

                $('#customerSelect').select2({
                    dropdownParent: $templateModal,
                    placeholder: 'Kunde suchen...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: @json(route('offer-templates.search.customers')),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || ''
                        }),
                        processResults: data => data
                    },
                    templateResult: function (item) {
                        if (!item.id) {
                            return item.text;
                        }

                        const address = item.address
                            ? `<div style="font-size:11px;color:#6b7280;margin-top:2px;">${escapeHtml(item.address)}</div>`
                            : '';

                        return $(`
                            <div>
                                <div style="font-weight:900;font-size:12px;color:#0f172a;">${escapeHtml(item.text || '')}</div>
                                ${address}
                            </div>
                        `);
                    },
                    templateSelection: function (item) {
                        return item.text || 'Kunde suchen...';
                    }
                });

                $('#objectSelect').select2({
                    dropdownParent: $templateModal,
                    placeholder: 'Objekt auswählen...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: @json(route('offer-templates.search.objects')),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || '',
                            customer_id: $('#customerSelect').val()
                        }),
                        processResults: data => data
                    }
                });

                $('#productSelect').select2({
                    dropdownParent: $templateModal,
                    placeholder: 'Produkt auswählen...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: @json(route('offer-templates.search.products')),
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || '',
                            customer_id: $('#customerSelect').val(),
                            alternative_id: $('#objectSelect').val()
                        }),
                        processResults: data => data
                    }
                });

                $('#customerSelect')
                    .off('change.templateCustomer')
                    .on('change.templateCustomer', function () {
                        $('#objectSelect').val(null).trigger('change');
                        $('#productSelect').val(null).trigger('change');

                        const resultBox = document.getElementById('checkResultBox');

                        if (resultBox) {
                            resultBox.style.display = 'none';
                            resultBox.innerHTML = '';
                        }
                    });

                $('#objectSelect')
                    .off('change.templateObject')
                    .on('change.templateObject', function () {
                        $('#productSelect').val(null).trigger('change');

                        const resultBox = document.getElementById('checkResultBox');

                        if (resultBox) {
                            resultBox.style.display = 'none';
                            resultBox.innerHTML = '';
                        }
                    });

                $('.select2-container').css('width', '100%');
            }

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('ot-modal-backdrop')) {
                    e.target.classList.remove('open');
                }

                const useBtn = e.target.closest('.js-use-template');

                if (useBtn) {
                    selectedTemplateId = useBtn.dataset.templateId;
                    selectedTemplateName = useBtn.dataset.templateName;
                    selectedTemplateProductId = useBtn.dataset.templateProductId;

                    document.getElementById('modalTemplateTitle').textContent = selectedTemplateName || 'Template verwenden';
                    openTemplateModal();
                    return;
                }

                const detailBtn = e.target.closest('.js-open-details');

                if (detailBtn) {
                    try {
                        openDetailsModal(JSON.parse(detailBtn.dataset.details || '{}'));
                    } catch (e) {
                        toast('bad', 'Details Fehler', 'Details konnten nicht geladen werden.');
                    }
                    return;
                }

                const positionsBtn = e.target.closest('.js-open-positions');

                if (positionsBtn) {
                    openPositionsModal(positionsBtn.dataset.templateId, positionsBtn.dataset.title);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.ot-modal-backdrop.open').forEach(el => el.classList.remove('open'));
                }
            });

            document.getElementById('checkTemplateBtn').addEventListener('click', checkTemplate);
            document.getElementById('confirmUseTemplateBtn').addEventListener('click', useTemplate);

            initTemplateSelect2();
            initTemplateSuggestions();

            window.GlobalBreadcrumbs = [
                { label: 'Dashboard', url: "{{ url('/') }}" },
                { label: 'Angebot Vorlagen', url: "{{ route('offer-templates.index') }}", clickable: false }
            ];

            if (window.setGlobalBreadcrumbs) {
                window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
            }
        </script>
    @endpush
@endonce