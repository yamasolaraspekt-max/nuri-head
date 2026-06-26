@extends('admin.layouts.app')

@section('title', 'Rückgabe prüfen')

@section('content')
    <style>
        .preview-page {
            padding: 24px;
            background: #f8fafc;
            min-height: calc(100vh - 80px);
        }

        .preview-shell {
            max-width: 1280px;
            margin: 0 auto;
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .preview-title {
            font-size: 26px;
            font-weight: 900;
            color: #111827;
            margin: 0;
        }

        .preview-subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-top: 6px;
            line-height: 1.55;
        }

        .preview-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
            padding: 18px;
            margin-bottom: 18px;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .preview-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .preview-label {
            display: block;
            font-size: 13px;
            font-weight: 900;
            color: #374151;
            margin-bottom: 6px;
        }

        .preview-input,
        .preview-select,
        .preview-textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 14px;
            background: white;
            outline: none;
        }

        .preview-textarea {
            min-height: 70px;
            resize: vertical;
        }

        .preview-input:focus,
        .preview-select:focus,
        .preview-textarea:focus {
            border-color: #74b2d4;
            box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
        }

        .preview-btn {
            border: none;
            border-radius: 14px;
            padding: 11px 15px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .preview-btn-green {
            background: #93c21c;
            color: white;
        }

        .preview-btn-soft {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .preview-alert-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
            border-radius: 14px;
            padding: 12px 14px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .preview-alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 14px;
            padding: 12px 14px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .preview-info {
            background: rgba(116, 178, 212, .10);
            border: 1px solid rgba(116, 178, 212, .25);
            color: #075985;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .duplicate-banner {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 14px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 18px;
            box-shadow: 0 12px 30px rgba(146, 64, 14, .08);
        }

        .duplicate-banner-icon {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            background: #f59e0b;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
        }

        .duplicate-banner-title {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 950;
            color: #78350f;
        }

        .duplicate-banner-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            font-weight: 800;
        }

        .duplicate-option {
            margin-top: 14px;
            padding: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #f9fafb;
        }

        .duplicate-option label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 0;
            cursor: pointer;
        }

        .duplicate-option input {
            margin-top: 4px;
        }

        .duplicate-option strong {
            display: block;
            color: #111827;
            font-size: 14px;
            font-weight: 950;
        }

        .duplicate-option small {
            display: block;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
            margin-top: 4px;
            font-weight: 700;
        }

        .item-card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 14px;
            background: #fff;
        }

        .item-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .item-title {
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            margin: 0 0 5px;
        }

        .item-meta {
            color: #6b7280;
            font-size: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .item-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #374151;
            padding: 3px 8px;
            font-weight: 800;
        }

        .item-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: 12px;
        }

        .item-grid-wide {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .preview-code {
            max-height: 360px;
            overflow: auto;
            background: #0f172a;
            color: #e5e7eb;
            padding: 14px;
            border-radius: 14px;
            font-size: 12px;
            white-space: pre-wrap;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 12px;
            color: #374151;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        @media(max-width: 1100px) {
            .preview-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width: 1000px) {
            .item-grid,
            .item-grid-wide,
            .preview-grid,
            .preview-grid-4 {
                grid-template-columns: 1fr;
            }

            .preview-header,
            .item-head,
            .duplicate-banner {
                grid-template-columns: 1fr;
                flex-direction: column;
            }

            .preview-btn {
                width: 100%;
            }
        }
    </style>

    @php
        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $items = is_array($items ?? null) ? $items : [];

        $selectedArticleGroup = $selectedArticleGroup ?? null;
        $selectedSubArticleGroup = $selectedSubArticleGroup ?? null;

        $updatePriceOnlyIfExists = $updatePriceOnlyIfExists ?? true;

        $normalizeItem = function ($item) {
            $orderItem = $item['OrderItem'] ?? $item;

            if (isset($orderItem[0]) && is_array($orderItem[0])) {
                $orderItem = $orderItem[0];
            }

            return is_array($orderItem) ? $orderItem : [];
        };

        $extractManufacturerNo = function ($item) {
            return $item['ManufacturerArtNo']
                ?? $item['HerstellerArtikelnummer']
                ?? $item['NEW_ITEM-MANUFACTMAT']
                ?? $item['ManufactMat']
                ?? $item['MANUFACTMAT']
                ?? 'Not filled';
        };
    @endphp

    <div class="preview-page">
        <div class="preview-shell">
            <div class="preview-header">
                <div>
                    <h1 class="preview-title">Rückgabe prüfen und importieren</h1>
                    <div class="preview-subtitle">
                        Lieferant: <strong>{{ $connection->name }}</strong> ·
                        Log #{{ $log->id }} ·
                        Status: <strong>{{ $log->status }}</strong>
                    </div>
                </div>

                <a href="{{ route('admin.supplier-connectors.search', $connection) }}" class="preview-btn preview-btn-soft">
                    Zurück zur Suche
                </a>
            </div>

            <div class="duplicate-banner">
                <div class="duplicate-banner-icon">!</div>
                <div>
                    <h2 class="duplicate-banner-title">Duplikat-Prüfung ist aktiv</h2>
                    <p class="duplicate-banner-text">
                        Beim Speichern wird geprüft, ob das Produkt bereits im System existiert.
                        Die Prüfung läuft über <strong>EAN</strong>, <strong>Hersteller-Artikelnummer</strong>
                        oder eine vorhandene <strong>Lieferanten-Artikelnummer</strong> in
                        <strong>distributor_prices</strong>. Wenn das Produkt existiert und die Option unten aktiv ist,
                        wird nur der Lieferantenpreis aktualisiert.
                    </p>
                </div>
            </div>

            @if(session('success'))
                <div class="preview-alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="preview-alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="preview-alert-error">
                    Bitte prüfe die Felder. Mindestens Lieferant und ein importierbarer Artikel müssen vorhanden sein.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.supplier-connectors.logs.import', [$connection, $log]) }}">
                @csrf

                <div class="preview-card">
                    <h2 style="font-size:18px;font-weight:900;color:#111827;margin:0 0 8px;">
                        1. Zuordnung auswählen
                    </h2>

                    <div class="preview-info">
                        Wähle Lieferant, Hersteller, Artikelgruppe und Unter-Artikelgruppe.
                        <strong>products.article_no</strong> ist die Hersteller-Artikelnummer.
                        <strong>distributor_prices.article_no</strong> ist die Lieferanten-Artikelnummer.
                    </div>

                    <div class="preview-grid-4">
                        <div>
                            <label class="preview-label">Lieferant / Distributor</label>
                            <select name="distributor_id" class="preview-select select2-distributors" required>
                                @if($selectedDistributor)
                                    <option value="{{ $selectedDistributor->id }}" selected>
                                        {{ $selectedDistributor->name ?? $selectedDistributor->short_name }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="preview-label">Standard-Hersteller / Brand</label>
                            <select name="default_brand_id" class="preview-select select2-brands">
                                @if($selectedBrand)
                                    <option value="{{ $selectedBrand->id }}" selected>
                                        {{ $selectedBrand->name }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="preview-label">Standard-Artikelgruppe</label>
                            <select name="default_article_group_id"
                                    id="defaultArticleGroupSelect"
                                    class="preview-select select2-article-groups">
                                @if($selectedArticleGroup)
                                    <option value="{{ $selectedArticleGroup->id }}" selected>
                                        {{ $selectedArticleGroup->article_group }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="preview-label">Standard-Unter-Artikelgruppe</label>
                            <select name="default_sub_article_group_id"
                                    id="defaultSubArticleGroupSelect"
                                    class="preview-select select2-sub-article-groups">
                                @if($selectedSubArticleGroup)
                                    <option value="{{ $selectedSubArticleGroup->id }}" selected>
                                        {{ $selectedSubArticleGroup->sub_article }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="duplicate-option">
                        <label>
                            <input type="checkbox"
                                   name="update_price_only_if_exists"
                                   value="1"
                                   {{ $updatePriceOnlyIfExists ? 'checked' : '' }}>

                            <span>
                                <strong>Wenn Produkt bereits existiert, nur Lieferantenpreis aktualisieren</strong>
                                <small>
                                    Produktdaten wie Titel, Hersteller, Artikelgruppe, Untergruppe und Beschreibung bleiben unverändert.
                                    Aktualisiert wird nur <strong>distributor_prices</strong>.
                                </small>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="preview-card">
                    <h2 style="font-size:18px;font-weight:900;color:#111827;margin:0 0 8px;">
                        2. Empfangene Artikel prüfen
                    </h2>

                    <div class="preview-info">
                        Die Felder wurden aus <strong>OrderItem</strong> gelesen:
                        <strong>ArtNo</strong>, <strong>Kurztext</strong>, <strong>OfferPrice</strong>,
                        <strong>NetPrice</strong>, <strong>VAT</strong>, <strong>QU</strong>.
                    </div>

                    @forelse($items as $index => $rawItem)
                        @php
                            $item = $normalizeItem($rawItem);

                            $productTitle = old(
                                "items.$index.product_title",
                                $item['Kurztext']
                                    ?? $item['Description']
                                    ?? $item['Bezeichnung']
                                    ?? $item['NEW_ITEM-DESCRIPTION']
                                    ?? ''
                            );

                            $distributorArticleNo = old(
                                "items.$index.distributor_article_no",
                                $item['ArtNo']
                                    ?? $item['ARTNO']
                                    ?? $item['Artikelnummer']
                                    ?? $item['NEW_ITEM-VENDORMAT']
                                    ?? ''
                            );

                            $manufacturerArticleNo = old(
                                "items.$index.manufacturer_article_no",
                                $extractManufacturerNo($item)
                            );

                            $ean = old(
                                "items.$index.ean",
                                $item['EAN']
                                    ?? $item['Ean']
                                    ?? $item['NEW_ITEM-EAN']
                                    ?? ''
                            );

                            $price = old(
                                "items.$index.price",
                                $item['OfferPrice']
                                    ?? $item['Price']
                                    ?? $item['PREIS']
                                    ?? ''
                            );

                            $purchasePrice = old(
                                "items.$index.purchase_price",
                                $item['NetPrice']
                                    ?? $item['PurchasePrice']
                                    ?? $item['EK']
                                    ?? $item['NEW_ITEM-PRICE']
                                    ?? ''
                            );

                            $vat = old(
                                "items.$index.vat_percent",
                                $item['VAT']
                                    ?? $item['Vat']
                                    ?? 19
                            );

                            $unit = old(
                                "items.$index.measure_unit",
                                $item['QU']
                                    ?? $item['Unit']
                                    ?? $item['NEW_ITEM-UNIT']
                                    ?? ''
                            );

                            $qty = $item['Qty'] ?? null;

                            $imageUrl = old(
                                "items.$index.image_url",
                                $item['ImageUrl']
                                    ?? $item['BildUrl']
                                    ?? $item['Produktbild']
                                    ?? $item['NEW_ITEM-IMAGE_URL']
                                    ?? ''
                            );
                        @endphp

                        <div class="item-card">
                            <div class="item-head">
                                <div>
                                    <h3 class="item-title">
                                        {{ $productTitle ?: 'Unbenannter Artikel' }}
                                    </h3>

                                    <div class="item-meta">
                                        <span class="item-pill">Lieferanten-Nr.: {{ $distributorArticleNo ?: '-' }}</span>
                                        <span class="item-pill">Hersteller-Nr.: {{ $manufacturerArticleNo ?: 'Not filled' }}</span>
                                        <span class="item-pill">Menge: {{ $qty ?: '-' }}</span>
                                        <span class="item-pill">Einheit: {{ $unit ?: '-' }}</span>
                                        <span class="item-pill">Netto/EK: {{ $purchasePrice ?: '-' }}</span>
                                    </div>
                                </div>

                                <label style="display:inline-flex;align-items:center;gap:8px;font-weight:900;color:#047857;">
                                    <input type="checkbox" name="items[{{ $index }}][import]" value="1" checked>
                                    Importieren
                                </label>
                            </div>

                            <div class="item-grid">
                                <div>
                                    <label class="preview-label">Produkt-Titel</label>
                                    <input type="text"
                                           name="items[{{ $index }}][product_title]"
                                           value="{{ $productTitle }}"
                                           class="preview-input"
                                           required>
                                </div>

                                <div>
                                    <label class="preview-label">Lieferanten-Artikelnummer → distributor_prices.article_no</label>
                                    <input type="text"
                                           name="items[{{ $index }}][distributor_article_no]"
                                           value="{{ $distributorArticleNo }}"
                                           class="preview-input"
                                           required>
                                </div>

                                <div>
                                    <label class="preview-label">Hersteller-Artikelnummer → products.article_no</label>
                                    <input type="text"
                                           name="items[{{ $index }}][manufacturer_article_no]"
                                           value="{{ $manufacturerArticleNo ?: 'Not filled' }}"
                                           class="preview-input"
                                           placeholder="Not filled">
                                </div>

                                <div>
                                    <label class="preview-label">EAN</label>
                                    <input type="text"
                                           name="items[{{ $index }}][ean]"
                                           value="{{ $ean }}"
                                           class="preview-input">
                                </div>
                            </div>

                            <div class="item-grid-wide">
                                <div>
                                    <label class="preview-label">Hersteller pro Artikel</label>
                                    <select name="items[{{ $index }}][brand_id]"
                                            class="preview-select select2-brands">
                                    </select>
                                </div>

                                <div>
                                    <label class="preview-label">Artikelgruppe pro Artikel</label>
                                    <select name="items[{{ $index }}][article_group_id]"
                                            class="preview-select select2-article-groups item-article-group"
                                            data-index="{{ $index }}">
                                    </select>
                                </div>

                                <div>
                                    <label class="preview-label">Unter-Artikelgruppe pro Artikel</label>
                                    <select name="items[{{ $index }}][sub_article_group_id]"
                                            class="preview-select select2-sub-article-groups item-sub-article-group"
                                            data-index="{{ $index }}">
                                    </select>
                                </div>
                            </div>

                            <div class="item-grid-wide">
                                <div>
                                    <label class="preview-label">VK / Angebotspreis → distributor_prices.price</label>
                                    <input type="text"
                                           name="items[{{ $index }}][price]"
                                           value="{{ $price }}"
                                           class="preview-input">
                                </div>

                                <div>
                                    <label class="preview-label">EK / Nettopreis → distributor_prices.purchase_price</label>
                                    <input type="text"
                                           name="items[{{ $index }}][purchase_price]"
                                           value="{{ $purchasePrice }}"
                                           class="preview-input">
                                </div>

                                <div>
                                    <label class="preview-label">MwSt.</label>
                                    <input type="text"
                                           name="items[{{ $index }}][vat_percent]"
                                           value="{{ $vat }}"
                                           class="preview-input">
                                </div>

                                <div>
                                    <label class="preview-label">Einheit</label>
                                    <input type="text"
                                           name="items[{{ $index }}][measure_unit]"
                                           value="{{ $unit }}"
                                           class="preview-input">
                                </div>

                                <div>
                                    <label class="preview-label">Verfügbarkeit</label>
                                    <input type="text"
                                           name="items[{{ $index }}][availability]"
                                           value="{{ old("items.$index.availability", $item['Availability'] ?? '') }}"
                                           class="preview-input">
                                </div>

                                <div>
                                    <label class="preview-label">Bild-URL</label>
                                    <input type="text"
                                           name="items[{{ $index }}][image_url]"
                                           value="{{ $imageUrl }}"
                                           class="preview-input"
                                           placeholder="Falls vom Lieferant gesendet">
                                </div>
                            </div>

                            <div style="margin-top:12px;">
                                <label class="preview-label">Beschreibung</label>
                                <textarea name="items[{{ $index }}][short_description]"
                                          class="preview-textarea">{{ old("items.$index.short_description", $item['Langtext'] ?? $item['LongText'] ?? '') }}</textarea>
                            </div>

                            <details style="margin-top:12px;">
                                <summary style="cursor:pointer;font-weight:900;color:#374151;">
                                    Rohdaten dieses Artikels anzeigen
                                </summary>

                                <pre class="preview-code">{{ json_encode($rawItem, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        </div>
                    @empty
                        <div class="preview-alert-error">
                            Keine Artikel gefunden. Prüfe den Raw JSON Bereich unten.
                        </div>
                    @endforelse

                    @if(count($items))
                        <div style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap;">
                            <button type="submit" class="preview-btn preview-btn-green">
                                Ausgewählte Artikel speichern
                            </button>

                            <a href="{{ route('admin.supplier-connectors.edit', $connection) }}"
                               class="preview-btn preview-btn-soft">
                                Mapping bearbeiten
                            </a>
                        </div>
                    @endif
                </div>
            </form>

            <div class="preview-card">
                <h2 style="font-size:18px;font-weight:900;color:#111827;margin:0 0 8px;">
                    Raw Rückgabe
                </h2>

                <details>
                    <summary style="cursor:pointer;font-weight:900;color:#374151;">
                        Komplette Rückgabe anzeigen
                    </summary>

                    <pre class="preview-code">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                </details>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initSelect2WhenReady() {
                if (!window.jQuery || !jQuery.fn.select2) {
                    setTimeout(initSelect2WhenReady, 150);
                    return;
                }

                $('.select2-brands').select2({
                    width: '100%',
                    placeholder: 'Hersteller suchen...',
                    allowClear: true,
                    ajax: {
                        url: @json(route('admin.supplier-connectors.ajax.brands')),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                });

                $('.select2-distributors').select2({
                    width: '100%',
                    placeholder: 'Lieferant suchen...',
                    allowClear: true,
                    ajax: {
                        url: @json(route('admin.supplier-connectors.ajax.distributors')),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                });

                $('.select2-article-groups').select2({
                    width: '100%',
                    placeholder: 'Artikelgruppe suchen...',
                    allowClear: true,
                    ajax: {
                        url: @json(route('admin.supplier-connectors.ajax.article-groups')),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                });

                $('.select2-sub-article-groups').select2({
                    width: '100%',
                    placeholder: 'Unter-Artikelgruppe suchen...',
                    allowClear: true,
                    ajax: {
                        url: @json(route('admin.supplier-connectors.ajax.sub-article-groups')),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            const $select = $(this);
                            const index = $select.data('index');

                            let articleGroupId = null;

                            if (index !== undefined) {
                                articleGroupId = $('.item-article-group[data-index="' + index + '"]').val();
                            } else {
                                articleGroupId = $('#defaultArticleGroupSelect').val();
                            }

                            return {
                                q: params.term || '',
                                page: params.page || 1,
                                article_group_id: articleGroupId || ''
                            };
                        },
                        processResults: function (data) {
                            return data;
                        }
                    }
                });

                $('#defaultArticleGroupSelect').on('change', function () {
                    $('#defaultSubArticleGroupSelect').val(null).trigger('change');
                });

                $(document).on('change', '.item-article-group', function () {
                    const index = $(this).data('index');
                    $('.item-sub-article-group[data-index="' + index + '"]').val(null).trigger('change');
                });
            }

            initSelect2WhenReady();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection