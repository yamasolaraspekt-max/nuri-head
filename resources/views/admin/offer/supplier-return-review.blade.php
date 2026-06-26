@extends('admin.layouts.app')

@section('title', 'Lieferanten-Rückgabe prüfen')

@section('content')
    <style>
        .sr-page {
            min-height: calc(100vh - 80px);
            background: #f8fafc;
            padding: 24px;
        }

        .sr-shell {
            max-width: 1280px;
            margin: 0 auto;
        }

        .sr-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .07);
            padding: 18px;
            margin-bottom: 16px;
        }

        .sr-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .sr-title {
            font-size: 25px;
            font-weight: 950;
            color: #111827;
            margin: 0;
        }

        .sr-sub {
            color: #6b7280;
            font-size: 13px;
            margin-top: 6px;
            line-height: 1.55;
        }

        .sr-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .sr-item-grid {
            display: grid;
            grid-template-columns: 32px 1.6fr 1fr 1fr 1fr;
            gap: 10px;
        }

        .sr-wide {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .sr-label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: #374151;
            margin-bottom: 5px;
        }

        .sr-input,
        .sr-select,
        .sr-textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            padding: 10px 12px;
            background: #fff;
            font-size: 13px;
            outline: none;
        }

        .sr-textarea {
            min-height: 64px;
            resize: vertical;
        }

        .sr-input:focus,
        .sr-select:focus,
        .sr-textarea:focus {
            border-color: #93c21c;
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .12);
        }

        .sr-btn {
            border: 0;
            border-radius: 14px;
            padding: 11px 15px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }

        .sr-btn-green {
            background: #93c21c;
            color: #fff;
        }

        .sr-btn-soft {
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .sr-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.5;
        }

        .sr-alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 14px;
        }

        .sr-product-card {
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 14px;
            margin-bottom: 12px;
            background: #fff;
        }

        .sr-product-card:hover {
            border-color: #93c21c;
            box-shadow: 0 14px 28px rgba(147, 194, 28, .10);
        }


        /* =========================
               SELECT2 FIXES
            ========================= */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            min-height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            display: flex;
            align-items: center;
            padding: 0 8px;
            font-size: 13px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #93c21c;
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .12);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            color: #111827;
            padding-left: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .select2-dropdown {
            border-color: #d1d5db;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
        }

        .select2-search__field {
            border-radius: 10px !important;
            border-color: #d1d5db !important;
            outline: none !important;
            padding: 8px 10px !important;
        }

        .select2-results__option {
            font-size: 13px;
            padding: 9px 12px;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: #93c21c;
            color: white;
        }

        .sr-price-note {
            margin-top: 10px;
            background: #f7fee7;
            border: 1px solid rgba(147, 194, 28, .35);
            color: #4d7c0f;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.45;
        }


        @media(max-width:1000px) {

            .sr-grid,
            .sr-item-grid,
            .sr-wide {
                grid-template-columns: 1fr
            }

            .sr-head {
                flex-direction: column
            }

            .sr-btn {
                width: 100%;
            }
        }
    </style>

    @php
        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $items = is_array($items ?? null) ? $items : [];

        $normalizeItem = function ($item) {
            $orderItem = $item['OrderItem'] ?? $item;
            if (isset($orderItem[0]) && is_array($orderItem[0])) {
                $orderItem = $orderItem[0];
            }
            return is_array($orderItem) ? $orderItem : [];
        };

        $val = function ($row, array $keys, $default = '') {
            foreach ($keys as $key) {
                if (isset($row[$key]) && $row[$key] !== '') {
                    return $row[$key];
                }
            }
            return $default;
        };
    @endphp

    <div class="sr-page">
        <div class="sr-shell">
            <div class="sr-head">
                <div>
                    <h1 class="sr-title">Rückgabe prüfen und ins Angebot übernehmen</h1>
                    <div class="sr-sub">
                        Lieferant: <strong>{{ $connection->name }}</strong> ·
                        Log #{{ $log->id }} ·
                        Artikel: <strong>{{ count($items) }}</strong>
                    </div>
                </div>

                <a href="{{ route('admin.offers.folders.show', $folder) }}" class="sr-btn sr-btn-soft">
                    Zurück zum Angebot
                </a>
            </div>

            @if(session('error'))
                <div class="sr-alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="sr-alert-error">
                    Bitte prüfe die Eingaben. {{ $errors->first() }}
                </div>
            @endif

            <form method="POST"
                action="{{ route('admin.offers.folders.supplier.logs.import-to-offer', [$folder, $connection, $log]) }}">
                @csrf

                <input type="hidden" name="target_section_index" value="{{ $targetSectionIndex }}">

                <div class="sr-card">
                    <div class="sr-info">
                        Wähle hier zuerst, wo die Lieferantenartikel gespeichert werden sollen. Danach werden die Produkte
                        in
                        <strong>products</strong> und <strong>distributor_prices</strong> gespeichert und per Reverb live
                        ins geöffnete Angebot eingefügt.
                    </div>

                    <div class="sr-price-note">
                        Wichtig: VK, EK, Rabatt, Verfügbarkeit und Lieferanten-Art.-Nr. gehören zum Distributor und werden
                        in
                        <strong>distributor_prices</strong> gespeichert. Im <strong>products</strong>-Datensatz bleiben nur
                        die allgemeinen Produktdaten.
                    </div>

                    <div class="sr-grid" style="margin-top:14px;">
                        <div>
                            <label class="sr-label">Distributor / Lieferant</label>
                            <select name="distributor_id" class="sr-select js-sr-select2"
                                data-placeholder="Distributor suchen..." required>
                                <option value="">Bitte wählen</option>
                                @foreach($distributors as $distributor)
                                    <option value="{{ $distributor->id }}" @selected((int) old('distributor_id', $selectedDistributor?->id) === (int) $distributor->id)>
                                        {{ $distributor->short_name ?: $distributor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="sr-label">Standard Brand / Hersteller</label>
                            <select name="default_brand_id" class="sr-select js-sr-select2"
                                data-placeholder="Brand suchen...">
                                <option value="">Nicht setzen</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected((int) old('default_brand_id') === (int) $brand->id)>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="sr-label">Standard Artikelgruppe</label>
                            <select name="default_article_group_id" class="sr-select js-sr-select2 js-default-article-group"
                                data-placeholder="Artikelgruppe suchen...">
                                <option value="">Nicht setzen</option>
                                @foreach($articleGroups as $group)
                                    <option value="{{ $group->id }}" @selected((int) old('default_article_group_id') === (int) $group->id)>
                                        {{ $group->article_group }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="sr-label">Standard Untergruppe</label>
                            <select name="default_sub_article_group_id"
                                class="sr-select js-sr-select2 js-default-sub-article-group"
                                data-placeholder="Untergruppe suchen...">
                                <option value="">Nicht setzen</option>
                                @foreach($subArticleGroups as $group)
                                    <option value="{{ $group->id }}"
                                        data-article-group-id="{{ $group->article_group_id ?? $group->article_group ?? $group->group_id ?? '' }}"
                                        @selected((int) old('default_sub_article_group_id') === (int) $group->id)>
                                        {{ $group->sub_article }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <label
                        style="display:flex;gap:8px;align-items:flex-start;margin-top:14px;font-size:13px;font-weight:800;color:#374151;">
                        <input type="checkbox" name="update_price_only_if_exists" value="1" checked style="margin-top:3px;">
                        Wenn Produkt existiert, nur Lieferantenpreis aktualisieren und Produktdaten nicht überschreiben.
                    </label>
                </div>

                <div class="sr-card">
                    <h2 style="font-size:18px;font-weight:950;color:#111827;margin:0 0 14px;">
                        Artikel prüfen
                    </h2>

                    @forelse($items as $index => $raw)
                        @php
                            $row = $normalizeItem($raw);

                            $title = $val($row, [
                                'Description',
                                'DESCRIPTION',
                                'Kurztext',
                                'Text',
                                'NEW_ITEM-DESCRIPTION',
                                'NEW_ITEM-DESCRIPTION[1]',
                                'ProductName',
                                'Name'
                            ], 'Lieferantenartikel');

                            $ean = $val($row, ['EAN', 'Ean', 'ean', 'NEW_ITEM-EAN'], '');
                            $manufacturerNo = $val($row, ['ManufacturerArtNo', 'HerstellerArtikelnummer', 'NEW_ITEM-MANUFACTMAT', 'ManufactMat', 'MANUFACTMAT'], 'Not filled');
                            $supplierNo = $val($row, ['ArtNo', 'Artikelnummer', 'SupplierArtNo', 'VENDORMAT', 'NEW_ITEM-VENDORMAT', 'ItemNo'], '');
                            $price = $val($row, ['Price', 'PRICE', 'OfferPrice', 'NEW_ITEM-PRICE', 'VK'], 0);
                            $purchase = $val($row, ['NetPrice', 'PurchasePrice', 'EK', 'EKPrice', 'NEW_ITEM-PRICE'], $price);
                            $unit = $val($row, ['QU', 'Unit', 'UNIT', 'NEW_ITEM-UNIT'], 'Stk');
                            $qty = $val($row, ['Quantity', 'QUANTITY', 'NEW_ITEM-QUANTITY'], 1);
                            $availability = $val($row, ['Availability', 'AVAILABILITY', 'NEW_ITEM-AVAILABILITY'], '');
                            $long = $val($row, ['LongText', 'LONGTEXT', 'DescriptionLong', 'NEW_ITEM-LONGTEXT'], '');
                            $image = $val($row, ['ImageUrl', 'IMAGE_URL', 'Picture', 'PICTURE', 'image_url'], '');
                        @endphp

                        <div class="sr-product-card">
                            <div class="sr-item-grid">
                                <div>
                                    <label class="sr-label">Import</label>
                                    <input type="checkbox" name="items[{{ $index }}][import]" value="1" checked>
                                </div>

                                <div>
                                    <label class="sr-label">Produkttitel</label>
                                    <input class="sr-input" name="items[{{ $index }}][product_title]"
                                        value="{{ old("items.$index.product_title", $title) }}">
                                </div>

                                <div>
                                    <label class="sr-label">EAN</label>
                                    <input class="sr-input" name="items[{{ $index }}][ean]"
                                        value="{{ old("items.$index.ean", $ean) }}">
                                </div>

                                <div>
                                    <label class="sr-label">Hersteller-Art.-Nr.</label>
                                    <input class="sr-input" name="items[{{ $index }}][manufacturer_article_no]"
                                        value="{{ old("items.$index.manufacturer_article_no", $manufacturerNo) }}">
                                </div>

                                <div>
                                    <label class="sr-label">Lieferanten-Art.-Nr.</label>
                                    <input class="sr-input" name="items[{{ $index }}][distributor_article_no]"
                                        value="{{ old("items.$index.distributor_article_no", $supplierNo) }}">
                                </div>
                            </div>

                            <div class="sr-wide">
                                <div>
                                    <label class="sr-label">Brand</label>
                                    <select name="items[{{ $index }}][brand_id]" class="sr-select js-sr-select2"
                                        data-placeholder="Brand suchen...">
                                        <option value="">Standard verwenden</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="sr-label">Artikelgruppe</label>
                                    <select name="items[{{ $index }}][article_group_id]"
                                        class="sr-select js-sr-select2 js-item-article-group" data-index="{{ $index }}"
                                        data-placeholder="Artikelgruppe suchen...">
                                        <option value="">Standard verwenden</option>
                                        @foreach($articleGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->article_group }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="sr-label">Untergruppe</label>
                                    <select name="items[{{ $index }}][sub_article_group_id]"
                                        class="sr-select js-sr-select2 js-item-sub-article-group" data-index="{{ $index }}"
                                        data-placeholder="Untergruppe suchen...">
                                        <option value="">Standard verwenden</option>
                                        @foreach($subArticleGroups as $group)
                                            <option value="{{ $group->id }}"
                                                data-article-group-id="{{ $group->article_group_id ?? $group->article_group ?? $group->group_id ?? '' }}">
                                                {{ $group->sub_article }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="sr-label">Einheit / Measure</label>
                                    <input class="sr-input" name="items[{{ $index }}][measure_unit]"
                                        value="{{ old("items.$index.measure_unit", $unit) }}">
                                </div>
                            </div>

                            <div class="sr-wide">
                                <div>
                                    <label class="sr-label">VK / Preis</label>
                                    <input class="sr-input" name="items[{{ $index }}][price]"
                                        value="{{ old("items.$index.price", $price) }}">
                                </div>

                                <div>
                                    <label class="sr-label">EK / Purchase</label>
                                    <input class="sr-input" name="items[{{ $index }}][purchase_price]"
                                        value="{{ old("items.$index.purchase_price", $purchase) }}">
                                </div>

                                <div>
                                    <label class="sr-label">Verfügbarkeit</label>
                                    <input class="sr-input" name="items[{{ $index }}][availability]"
                                        value="{{ old("items.$index.availability", $availability) }}">
                                </div>

                                <div>
                                    <label class="sr-label">MwSt.</label>
                                    <input class="sr-input" name="items[{{ $index }}][vat_percent]"
                                        value="{{ old("items.$index.vat_percent", 19) }}">
                                </div>
                            </div>

                            <div class="sr-wide">
                                <div style="grid-column:span 2;">
                                    <label class="sr-label">Bild URL</label>
                                    <input class="sr-input" name="items[{{ $index }}][image_url]"
                                        value="{{ old("items.$index.image_url", $image) }}">
                                </div>

                                <div style="grid-column:span 2;">
                                    <label class="sr-label">Beschreibung</label>
                                    <textarea class="sr-textarea"
                                        name="items[{{ $index }}][short_description]">{{ old("items.$index.short_description", $long) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="sr-alert-error">
                            Keine Artikel im Rückgabe-Payload gefunden.
                        </div>
                    @endforelse
                </div>

                <div style="display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;margin-bottom:30px;">
                    <a href="{{ route('admin.offers.folders.show', $folder) }}" class="sr-btn sr-btn-soft">
                        Abbrechen
                    </a>

                    <button type="submit" class="sr-btn sr-btn-green">
                        Speichern und live ins Angebot einfügen
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Select2 assets only if the layout does not already include them --}}
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

   @push('scripts') 
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const hasJquery = typeof window.jQuery !== 'undefined';
                const hasSelect2 = hasJquery && typeof window.jQuery.fn.select2 === 'function';

                if (!hasSelect2) {
                    console.warn('[Supplier Review] Select2 is not loaded. Dropdowns will still work normally.');
                    return;
                }

                $('.js-sr-select2').each(function () {
                    const $select = $(this);

                    if ($select.data('select2')) {
                        return;
                    }

                    $select.select2({
                        width: '100%',
                        placeholder: $select.data('placeholder') || 'Suchen...',
                        allowClear: true
                    });
                });

                function filterSubGroups($articleGroupSelect, $subGroupSelect) {
                    const groupId = String($articleGroupSelect.val() || '');

                    $subGroupSelect.find('option').each(function () {
                        const $option = $(this);

                        if (!$option.val()) {
                            $option.prop('disabled', false).show();
                            return;
                        }

                        const belongsTo = String($option.data('article-group-id') || '');

                        if (!groupId || !belongsTo || belongsTo === groupId) {
                            $option.prop('disabled', false).show();
                        } else {
                            $option.prop('disabled', true).hide();
                        }
                    });

                    const selected = $subGroupSelect.find(':selected');
                    if (selected.length && selected.prop('disabled')) {
                        $subGroupSelect.val(null).trigger('change.select2');
                    }

                    $subGroupSelect.trigger('change.select2');
                }

                $('.js-default-article-group').on('change', function () {
                    filterSubGroups($(this), $('.js-default-sub-article-group'));
                });

                $('.js-item-article-group').on('change', function () {
                    const index = $(this).data('index');
                    const $sub = $('.js-item-sub-article-group[data-index="' + index + '"]');
                    filterSubGroups($(this), $sub);
                });

                $('.js-default-article-group').trigger('change');
                $('.js-item-article-group').trigger('change');
            });
        </script>
    @endpush

@endsection