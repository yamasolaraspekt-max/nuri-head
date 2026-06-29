{{-- ===================== MODERN LIST VIEW ===================== --}}
@if($products->count())
    <style>
        .product-modern-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .product-modern-head,
        .product-modern-row {
            display: grid;
            grid-template-columns:
            gap: 14px;
            align-items: center;
        }

        .product-modern-head {
            padding: 0 18px 10px 18px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .product-modern-item {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .07);
            transition: all .18s ease;
            overflow: hidden;
        }

        .product-modern-item:hover {
            border-color: rgba(116, 178, 212, .55);
            box-shadow: 0 18px 40px rgba(15, 23, 42, .12);
            transform: translateY(-2px);
        }

        .product-modern-row {
            padding: 16px 18px;
        }

        .product-modern-cell {
            min-width: 0;
        }

        .product-modern-mobile-label {
            display: none;
            font-size: 11px;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 4px;
        }

        .product-modern-id {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 12px;
            font-weight: 800;
        }

        .product-modern-media {
            width: 68px;
            height: 68px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, .22);
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .08);
        }

        .product-modern-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-modern-media-placeholder {
            color: #94a3b8;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-modern-title {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 5px;
        }

        .product-modern-title a {
            font-size: 15px;
            font-weight: 800;
            color: #111827;
            text-decoration: none;
            line-height: 1.35;
        }

        .product-modern-title a:hover {
            color: #2563eb;
            text-decoration: none;
        }

        .product-modern-sub {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.55;
        }

        .product-modern-sub strong {
            color: #475569;
            font-weight: 800;
        }

        .product-modern-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin: 0 6px 6px 0;
            white-space: nowrap;
            max-width: 100%;
        }

        .product-modern-chip.brand {
            background: rgba(37, 99, 235, .07);
            border-color: rgba(37, 99, 235, .12);
            color: #1d4ed8;
        }

        .product-modern-chip.group {
            background: rgba(147, 194, 28, .10);
            border-color: rgba(147, 194, 28, .18);
            color: #6b8b12;
        }

        .product-modern-chip.category {
            background: #f8fafc;
            border-color: #e5e7eb;
            color: #475569;
        }

        .product-modern-brand-box {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: flex-start;
        }

        .product-modern-article-no {
            font-size: 11px;
            color: #64748b;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 3px 8px;
            font-weight: 700;
        }

        .product-modern-meta-stack {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-start;
        }

        .product-modern-status-wrap {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            flex-direction: column;
            gap: 6px;
        }

        .product-modern-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
        }

        .product-modern-status.active {
            background: #ecfdf5;
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, .22);
        }

        .product-modern-status.inactive {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, .20);
        }

        .product-modern-list-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            line-height: 1;
            white-space: nowrap;
        }

        .product-modern-list-badge.fav {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, .22);
        }

        .product-modern-list-badge.stamp {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, .20);
        }

        .product-modern-list-empty {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
        }

        .product-modern-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .product-modern-btn-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            flex-wrap: wrap;
        }

        .product-modern-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .16s ease;
            text-decoration: none;
        }

        .product-modern-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .10);
            text-decoration: none;
        }

        .product-modern-btn.edit:hover {
            color: #2563eb;
            border-color: rgba(37, 99, 235, .28);
            background: #eff6ff;
        }

        .product-modern-btn.copy:hover {
            color: #0891b2;
            border-color: rgba(8, 145, 178, .28);
            background: #ecfeff;
        }

        .product-modern-btn.cart {
            color: #6b8b12;
            border-color: rgba(147, 194, 28, .22);
            background: #f4fae7;
        }

        .product-modern-btn.cart:hover {
            color: #5d7710;
            border-color: rgba(147, 194, 28, .38);
            background: #ebf6cf;
        }

        .product-modern-btn.toggle:hover {
            border-color: rgba(34, 197, 94, .28);
            background: #ecfdf5;
            color: #15803d;
        }

        .product-modern-btn.disable:hover,
        .product-modern-btn.delete:hover {
            border-color: rgba(239, 68, 68, .24);
            background: #fef2f2;
            color: #dc2626;
        }

        .product-modern-btn.folder:hover {
            color: #2563eb;
            border-color: rgba(37, 99, 235, .24);
            background: #eff6ff;
        }

        .product-modern-distributor-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-modern-distributor-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 8px 10px;
            border: 1px solid rgba(148, 163, 184, .20);
            border-radius: 14px;
            background: #f8fafc;
        }

        .product-modern-distributor-top {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        .product-modern-distributor-name {
            font-size: 12px;
            font-weight: 800;
            color: #334155;
            line-height: 1.35;
            word-break: break-word;
        }

        .product-modern-distributor-price {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            font-size: 12px;
            color: #0f172a;
            font-weight: 700;
            padding-left: 20px;
        }

        .product-modern-distributor-price small {
            color: #64748b;
            font-weight: 700;
            margin-right: 2px;
        }

        .product-modern-distributor-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
        }

        .product-modern-distributor-badge.cheapest {
            background: #ecfdf5;
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, .20);
        }

        .product-modern-distributor-badge.expensive {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, .18);
        }

        .product-modern-distributor-meta {
            font-size: 11px;
            color: #6b7280;
            padding-left: 20px;
            line-height: 1.4;
        }

        .product-modern-distributor-more {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 10px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid rgba(37, 99, 235, .14);
            color: #2563eb;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            width: max-content;
        }

        @media (max-width: 1499.98px) {

            .product-modern-head,
            .product-modern-row {
                grid-template-columns:
                    42px 76px minmax(130px, .75fr) minmax(230px, 1.2fr) minmax(170px, .95fr) minmax(160px, .9fr) 105px 170px;
            }
        }

        @media (max-width: 1199.98px) {
            .product-modern-head {
                display: none;
            }

            .product-modern-row {
                grid-template-columns: 42px 84px 1fr;
                gap: 14px;
                align-items: flex-start;
            }

            .product-modern-cell {
                grid-column: 3;
            }

            .product-modern-cell.is-check,
            .product-modern-cell.is-image {
                grid-column: auto;
            }

            .product-modern-mobile-label {
                display: block;
            }

            .product-modern-actions,
            .product-modern-btn-group {
                justify-content: flex-start;
            }
        }

        @media (max-width: 767.98px) {
            .product-modern-row {
                grid-template-columns: 1fr;
                padding: 14px;
            }

            .product-modern-cell,
            .product-modern-cell.is-check,
            .product-modern-cell.is-image {
                grid-column: auto;
            }

            .product-modern-check-wrap {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .product-modern-media {
                width: 100%;
                max-width: 100px;
                height: 100px;
            }
        }


        .product-modern-item.is-selected{
            background:linear-gradient(90deg, #f0f9ff 0%, #f7fee7 100%) !important;
            border-color:rgba(116,178,212,.75) !important;
            box-shadow:
                0 0 0 2px rgba(116,178,212,.18),
                0 18px 45px rgba(15,23,42,.10) !important;
        }

        .product-modern-item.is-selected .product-modern-row{
            background:rgba(255,255,255,.28);
        }

        .product-modern-item.is-selected .product-modern-id{
            background:#dbeafe;
            color:#1d4ed8;
        }

        .product-modern-item.is-selected .product-modern-media{
            border-color:rgba(37,99,235,.35);
        }

        .product-supplier-count-badge{
            display:inline-flex;
            align-items:center;
            width:max-content;
            padding:4px 9px;
            border-radius:999px;
            background:#eef6ff;
            border:1px solid rgba(116,178,212,.28);
            color:#2563eb;
            font-size:10px;
            font-weight:900;
            line-height:1;
        }

        .product-modern-btn.history {
            color:#7c3aed;
            border-color:rgba(124,58,237,.22);
            background:#f5f3ff;
        }

        .product-modern-btn.history:hover {
            color:#6d28d9;
            border-color:rgba(124,58,237,.38);
            background:#ede9fe;
        }

        .product-history-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 30050;
        }

        .product-history-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        .product-history-modal {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 30051;
        }

        .product-history-modal.show {
            opacity: 1;
            pointer-events: auto;
        }

        .product-history-dialog {
            width: min(1040px, 100%);
            max-height: calc(100vh - 48px);
            transform: translateY(14px) scale(.98);
            transition: transform .22s ease;
        }

        .product-history-modal.show .product-history-dialog {
            transform: translateY(0) scale(1);
        }

        .product-history-card {
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 40px 110px rgba(15,23,42,.30);
            border: 1px solid rgba(15,23,42,.08);
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 48px);
        }

        .product-history-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px 16px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 55%, #f5f3ff 100%);
        }

        .product-history-title {
            margin: 0;
            font-size: 1.08rem;
            font-weight: 900;
            color: #111827;
        }

        .product-history-subtitle {
            margin-top: 5px;
            font-size: .82rem;
            color: #64748b;
            line-height: 1.45;
        }

        .product-history-close {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .16s ease;
            flex: 0 0 auto;
        }

        .product-history-close:hover {
            background: #f8fafc;
            color: #111827;
            transform: translateY(-1px);
        }

        .product-history-body {
            padding: 20px;
            overflow: auto;
        }

        .product-history-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .product-history-summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #f8fafc;
            padding: 12px;
        }

        .product-history-summary-label {
            font-size: 10px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 5px;
        }

        .product-history-summary-value {
            font-size: 13px;
            font-weight: 800;
            color: #111827;
            line-height: 1.35;
            word-break: break-word;
        }

        .product-history-loading,
        .product-history-empty {
            border: 1px dashed #cbd5e1;
            background: #f8fafc;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .product-history-timeline {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .product-history-item {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: #fff;
            padding: 14px;
            box-shadow: 0 8px 24px rgba(15,23,42,.05);
        }

        .product-history-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid rgba(37,99,235,.14);
        }

        .product-history-icon.created {
            background: #ecfdf5;
            color: #15803d;
            border-color: rgba(34,197,94,.20);
        }

        .product-history-icon.updated {
            background: #f5f3ff;
            color: #7c3aed;
            border-color: rgba(124,58,237,.20);
        }

        .product-history-icon.deleted {
            background: #fef2f2;
            color: #dc2626;
            border-color: rgba(239,68,68,.20);
        }

        .product-history-item-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .product-history-action {
            font-size: 13px;
            font-weight: 900;
            color: #111827;
        }

        .product-history-meta {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            line-height: 1.5;
        }

        .product-history-date {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 5px 9px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        .product-history-fields {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .product-history-field-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid rgba(37,99,235,.14);
            font-size: 10px;
            font-weight: 900;
        }

        .product-history-changes {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .product-history-change-row {
            display: grid;
            grid-template-columns: 145px 1fr 1fr;
            gap: 8px;
            align-items: stretch;
            font-size: 11px;
        }

        .product-history-change-field,
        .product-history-change-old,
        .product-history-change-new {
            border-radius: 12px;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #334155;
            line-height: 1.45;
            word-break: break-word;
        }

        .product-history-change-field {
            font-weight: 900;
            color: #111827;
        }

        .product-history-change-old {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .product-history-change-new {
            background: #ecfdf5;
            border-color: #bbf7d0;
        }

        body.product-history-modal-open {
            overflow: hidden !important;
        }

        @media (max-width: 991.98px) {
            .product-history-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .product-history-change-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .product-history-modal {
                padding: 12px;
            }

            .product-history-card {
                border-radius: 22px;
            }

            .product-history-head,
            .product-history-body {
                padding: 14px;
            }

            .product-history-summary {
                grid-template-columns: 1fr;
            }

            .product-history-item {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="product-modern-list">

        <div class="product-modern-head">
            <div>
                <label class="bulk-check mb-0">
                    <input type="checkbox" id="select-all-page">
                    <span></span>
                </label>
            </div>
            <div>Bild</div>
            <div>Hersteller / Hersteller.Nr.</div>
            <div>Name / Modell</div>
            <div>Lieferant</div>
            <div>ArtikelGruppe / Kategorie</div>
            <div>Status</div>
            <div style="text-align:right;">Action</div>
        </div>

        @foreach($products as $item)
                            @php
            $distList = ($distributorsByProduct ?? collect())->get((int) $item->id, collect());

            $isPublished = $item->status === 'Published';
            $statusText = $isPublished ? 'Aktiv' : 'Inaktiv';

            $favCount = isset($favCountsByProduct) ? ($favCountsByProduct[$item->id] ?? 0) : 0;
            $stampCount = isset($stampCountsByProduct) ? ($stampCountsByProduct[$item->id] ?? 0) : 0;

            /*
             |--------------------------------------------------------------------------
             | Article group name
             |--------------------------------------------------------------------------
             | Best result:
             | Controller should send article_groups.article_group as article_group_name.
             |
             | Fallbacks:
             | - Eloquent relation articleGroup
             | - Raw products.article_group value
             */
            $articleGroupLabel = $item->article_group_name
                ?? optional($item->articleGroup)->article_group
                ?? $item->article_group
                ?? '–';

            $articleGroupRaw = trim((string) $articleGroupLabel);

            $articleGroupNormalized = \Illuminate\Support\Str::of($articleGroupRaw)
                ->lower()
                ->replace(['ä', 'ö', 'ü', 'ß'], ['a', 'o', 'u', 'ss'])
                ->value();

            $showWpButtons = in_array($articleGroupNormalized, [
                'wp',
                'warmepumpe',
                'waermepumpe',
                'heatpump',
            ], true);

            $mainImage = $item->firstImage?->image;
            $mainImageUrl = $mainImage ? asset('images/products/' . ltrim($mainImage, '/')) : '';
                            @endphp

                            <div class="product-modern-item">
                                <div class="product-modern-row">

                                    {{-- CHECKBOX --}}
                                    <div class="product-modern-cell is-check">
                                        <div class="product-modern-check-wrap">
                                            <label class="bulk-check mb-0">
                                                <input type="checkbox" class="product-select" value="{{ $item->id }}">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- BILD --}}
                                    <div class="product-modern-cell is-image">
                                        <div class="product-modern-mobile-label">Bild</div>

                                        <button type="button" class="product-modern-media js-open-image-modal" data-product-id="{{ $item->id }}"
                                            data-product-name="{{ e($item->product) }}" data-image="{{ $mainImageUrl }}" title="Bild ändern"
                                            style="padding:0; cursor:pointer;">
                                            @if($mainImageUrl)
                                                <img src="{{ $mainImageUrl }}" alt="{{ $item->product }}">
                                            @else
                                                <div class="product-modern-media-placeholder">
                                                    <i class="feather icon-image"></i>
                                                </div>
                                            @endif
                                        </button>
                                    </div>

                                    {{-- HERSTELLER / ART.NR. --}}
                                    <div class="product-modern-cell">
                                        <div class="product-modern-mobile-label">Hersteller / Hersteller.Nr.</div>

                                        <div class="product-modern-brand-box">
                                            @if(!empty($item->brand_name))
                                                <span class="product-modern-chip brand">
                                                    {{ $item->brand_name }}
                                                </span>
                                            @else
                                                <span class="product-modern-list-empty">Kein Hersteller</span>
                                            @endif

                                            <span class="product-modern-article-no">
                                                Hersteller.Nr.: {{ $item->article_no ?: '–' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- NAME / MODELL --}}
                                    <div class="product-modern-cell">
                                        <div class="product-modern-mobile-label">Name / Modell</div>

                                        <div class="product-modern-title">
                                            <span class="product-modern-id">#{{ $item->id }}</span>

                                            <a href="{{ url('/product_details/' . $item->id) }}">
                                                {{ \Illuminate\Support\Str::limit($item->product ?: 'Ohne Name', 70) }}
                                            </a>
                                        </div>

                                        <div class="product-modern-sub">
                                            <strong>Modell:</strong> {{ $item->model ?: 'Kein Modell' }}
                                        </div>
                                    </div>

                                    {{-- LIEFERANT --}}
                                    <div class="product-modern-cell">
                                        <div class="product-modern-mobile-label">Lieferant</div>

                                        @if($distList->count())
                                            @php
                $supplierRows = $distList->values()->map(function ($dist) {
                    $ekPrice = $dist->purchase_price
                        ?? $dist->discount_price
                        ?? $dist->price
                        ?? $dist->display_price
                        ?? null;

                    return [
                        'id' => $dist->distributor_id ?? $dist->id ?? null,
                        'name' => $dist->distributor_name ?? $dist->name ?? 'Lieferant',
                        'ek_price' => $ekPrice,
                        'ek_price_formatted' => $ekPrice !== null
                            ? number_format((float) $ekPrice, 2, ',', '.') . ' €'
                            : '–',
                        'article_no' => $dist->article_no ?? '–',
                        'availability' => $dist->availability ?? '–',
                    ];
                });

                $supplierCount = $supplierRows->count();
                $firstSupplier = $supplierRows->first();
                                            @endphp

                                            <div class="product-supplier-select-wrap" data-supplier-widget="1" data-suppliers='@json($supplierRows)'>

                                                <div class="product-supplier-top">
                                                    <span class="product-supplier-count-badge">
                                                        <i class="fa fa-truck"></i>
                                                        {{ $supplierCount }} Lieferant{{ $supplierCount === 1 ? '' : 'en' }}
                                                    </span>
                                                </div>

                                                @if($supplierCount > 1)
                                                    <select class="product-supplier-select js-product-supplier-select" data-product-id="{{ $item->id }}">
                                                        @foreach($supplierRows as $index => $supplier)
                                                            <option value="{{ $index }}">
                                                                {{ $supplier['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <div class="product-supplier-detail-name">
                                                        <i class="fa fa-truck"></i>
                                                        <span>{{ $firstSupplier['name'] ?? 'Lieferant' }}</span>
                                                    </div>
                                                @endif

                                                <div class="product-supplier-detail js-product-supplier-detail">
                                                    <div class="product-supplier-detail-name">
                                                        <i class="fa fa-truck"></i>
                                                        <span>{{ $firstSupplier['name'] ?? 'Lieferant' }}</span>
                                                    </div>

                                                    <div class="product-supplier-detail-grid">
                                                        <div class="product-supplier-detail-line">
                                                            <span class="product-supplier-detail-label">EK Preis</span>
                                                            <span class="product-supplier-detail-value">
                                                                {{ $firstSupplier['ek_price_formatted'] ?? '–' }}
                                                            </span>
                                                        </div>

                                                        <div class="product-supplier-detail-line">
                                                            <span class="product-supplier-detail-label">Art.-Nr.</span>
                                                            <span class="product-supplier-detail-value">
                                                                {{ $firstSupplier['article_no'] ?? '–' }}
                                                            </span>
                                                        </div>

                                                        <div class="product-supplier-detail-line">
                                                            <span class="product-supplier-detail-label">Verfügbarkeit</span>
                                                            <span class="product-supplier-detail-value">
                                                                {{ $firstSupplier['availability'] ?? '–' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="product-supplier-empty">Keine Lieferanten</span>
                                        @endif
                                    </div>
                                    {{-- ARTIKELGRUPPE / KATEGORIE --}}
                                    <div class="product-modern-cell">
                                        <div class="product-modern-mobile-label">ArtikelGruppe / Kategorie</div>

                                        <div class="product-modern-meta-stack">
                                            <span class="product-modern-chip group">
                                                {{ $articleGroupLabel ?: '–' }}
                                            </span>

                                            <span class="product-modern-chip category">
                                                {{ $item->category ?: '–' }}
                                            </span>

                                            @if(!empty($item->sub_article))
                                                <div class="product-modern-sub">
                                                    <strong>Sub:</strong> {{ $item->sub_article }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- STATUS --}}
                                    <div class="product-modern-cell">
                                        <div class="product-modern-mobile-label">Status</div>

                                        <div class="product-modern-status-wrap">
                                            <span class="product-modern-status {{ $isPublished ? 'active' : 'inactive' }}">
                                                <i class="feather {{ $isPublished ? 'icon-check-circle' : 'icon-slash' }}"></i>
                                                {{ $statusText }}
                                            </span>

                                            @if($favCount > 0)
                                                <span class="product-modern-list-badge fav">
                                                    <i class="feather icon-star"></i>
                                                    {{ $favCount }} Favorit
                                                </span>
                                            @endif

                                            @if($stampCount > 0)
                                                <span class="product-modern-list-badge stamp">
                                                    <i class="feather icon-tag"></i>
                                                    {{ $stampCount }} Stempel
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ACTION --}}
                                    <div class="product-modern-cell">
                                        <div class="product-modern-mobile-label">Action</div>

                                        <div class="product-modern-actions">
                                            <div class="product-modern-btn-group">
                                                <a href="{{ url('/product/edit/' . $item->id) }}" class="product-modern-btn edit"
                                                    title="Bearbeiten">
                                                    <i class="feather icon-edit"></i>
                                                </a>

                                                <button type="button" class="product-modern-btn history js-product-history" data-product-id="{{ $item->id }}"
                                                    data-product-name="{{ e($item->product ?: 'Ohne Name') }}" title="Historie anzeigen">
                                                    <i class="feather icon-clock"></i>
                                                </button>

                                                <button type="button" class="product-modern-btn copy js-duplicate-product"
                                                    data-product-id="{{ $item->id }}" title="Duplizieren">
                                                    <i class="feather icon-copy"></i>
                                                </button>

                                                <button type="button" class="product-modern-btn cart" data-product-id="{{ $item->id }}"
                                                    title="In Cart hinzufügen"
                                                    onclick="event.preventDefault(); event.stopPropagation(); addProductToCart('{{ $item->id }}');">
                                                    <i class="feather icon-shopping-cart"></i>
                                                </button>

                                                @if($showWpButtons)
                                                    <a href="{{ route('product_wp', $item->id) }}" class="product-modern-btn folder"
                                                        title="Settings">
                                                        <i class="feather icon-settings"></i>
                                                    </a>

                                                    <a href="{{ route('product_wp_analytic', $item->id) }}" class="product-modern-btn folder"
                                                        title="Analytics">
                                                        <i class="feather icon-bar-chart-2"></i>
                                                    </a>
                                                @endif

                                                @if($isPublished)
                                                    <a href="{{ url('/product_unpublish/' . $item->id) }}" class="product-modern-btn disable"
                                                        title="Deaktivieren">
                                                        <i class="feather icon-slash"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ url('/product_publish/' . $item->id) }}" class="product-modern-btn toggle"
                                                        title="Aktivieren">
                                                        <i class="feather icon-check"></i>
                                                    </a>
                                                @endif

                                                <form action="{{ route('product.destroy', $item->id) }}" method="POST"
                                                    style="display:inline-flex;margin:0;"
                                                    onsubmit="return confirm('Dieses Produkt wirklich löschen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="product-modern-btn delete" title="Löschen">
                                                        <i class="feather icon-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-muted py-4">
        Keine Produkte gefunden. Passen Sie Ihre Suche oder Filter an.
    </div>
@endif