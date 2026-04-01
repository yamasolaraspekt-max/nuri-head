@extends('admin.layouts.app')

@section('title') Produktvergleich @endsection

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .product-diff-shell {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(15,23,42,0.06);
        box-shadow: 0 18px 45px rgba(15,23,42,0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .product-diff-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .product-diff-title h2 {
        font-size: 1.25rem;
        margin: 0;
    }

    .product-diff-subtitle {
        font-size: .85rem;
        color: #6b7280;
    }

    .product-diff-stats {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: 1.25rem;
    }

    .product-diff-stat {
        flex: 1 1 160px;
        background: #f9fafb;
        border-radius: 14px;
        border: 1px solid rgba(209,213,219,0.8);
        padding: .6rem .9rem;
        font-size: .78rem;
        color: #4b5563;
    }

    .product-diff-stat span {
        display: block;
    }

    .product-diff-stat-title {
        font-weight: 600;
        margin-bottom: .1rem;
    }

    .product-diff-filters {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .75rem;
        margin-bottom: .5rem;
    }

    @media (max-width: 991.98px) {
        .product-diff-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .product-diff-filters {
            grid-template-columns: 1fr;
        }
    }

    .product-diff-label {
        font-size: .8rem;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: .25rem;
    }

    .product-diff-input,
    .product-diff-select {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: .45rem .65rem;
        font-size: .85rem;
    }

    .product-diff-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        justify-content: flex-end;
        margin-top: .75rem;
        margin-bottom: .75rem;
    }

    .btn-ghost {
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: .4rem .9rem;
        font-size: .8rem;
        background: #f9fafb;
        cursor: pointer;
    }

    .btn-primary-soft {
        border-radius: 999px;
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
        padding: .45rem 1rem;
        font-size: .84rem;
        cursor: pointer;
    }

    .btn-primary-soft:disabled {
        opacity: .5;
        cursor: default;
    }

    .product-diff-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.5fr);
        gap: 1rem;
    }

    @media (max-width: 991.98px) {
        .product-diff-layout {
            grid-template-columns: 1fr;
        }
    }

    .product-list-card,
    .comparison-card {
        background: #f9fafb;
        border-radius: 16px;
        border: 1px solid rgba(148,163,184,0.35);
        box-shadow: 0 8px 20px rgba(15,23,42,0.04);
        padding: 1rem;
    }

    .product-list-header,
    .comparison-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .75rem;
    }

    .product-list-header h3,
    .comparison-header h3 {
        font-size: 1rem;
        margin: 0;
    }

    .product-list-subtitle,
    .comparison-subtitle {
        font-size: .78rem;
        color: #6b7280;
    }

    .product-table-search {
        margin-bottom: .5rem;
    }

    .product-table-wrapper {
        max-height: 420px;
        overflow: auto;
        border-radius: 12px;
        border: 1px solid rgba(209,213,219,0.8);
        background: #ffffff;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .8rem;
    }

    .product-table thead {
        background: #f3f4f6;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .product-table th,
    .product-table td {
        padding: .5rem .6rem;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .product-table tbody tr:hover {
        background: #f9fafb;
    }

    .badge {
        padding: .15rem .45rem;
        border-radius: 999px;
        font-size: .7rem;
        border: 1px solid rgba(148,163,184,0.6);
        background: #eef2ff;
        color: #4338ca;
    }

    .badge-success-soft {
        background: #dcfce7;
        color: #166534;
        border-color: rgba(22,101,52,0.25);
    }

    .comparison-placeholder {
        font-size: .85rem;
        color: #6b7280;
        padding: .75rem;
        border-radius: 10px;
        border: 1px dashed #d1d5db;
        background: #f9fafb;
    }

    .comparison-table-wrapper {
        max-height: 450px;
        overflow: auto;
        border-radius: 12px;
        border: 1px solid rgba(209,213,219,0.8);
        background: #ffffff;
    }

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .8rem;
    }

    .comparison-table th,
    .comparison-table td {
        padding: .45rem .6rem;
        border-bottom: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .comparison-table th:first-child,
    .comparison-table td:first-child {
        background: #f9fafb;
        position: sticky;
        left: 0;
        z-index: 2;
    }

    .comparison-table thead th {
        background: #f3f4f6;
        position: sticky;
        top: 0;
        z-index: 3;
    }

    .comparison-table tr:nth-child(even) td {
        background: #fcfcfd;
    }

    .comparison-distributor-row {
        background: #f3f4f6 !important;
        font-weight: 600;
    }

    .comparison-diff {
        outline: 2px solid rgba(248,113,113,0.9);
        outline-offset: -2px;
        border-radius: 6px;
        background: #fef2f2;
    }

    .comparison-metric-label {
        font-weight: 600;
        font-size: .78rem;
        color: #374151;
    }

    /* Select2 tweaks */
    .select2-container .select2-selection--single {
        height: 36px;
        border-radius: 10px;
        border-color: #d1d5db;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        font-size: .85rem;
        padding-left: .5rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0 px-md-3">
    <div class="product-diff-shell">
        <div class="product-diff-title">
            <div>
                <h2>Produktvergleich</h2>
                <div class="product-diff-subtitle">
                    Vergleiche mehrere Produkte nach Preis, Lieferant, Verfügbarkeit und weiteren Merkmalen.
                </div>
            </div>
            <div class="text-right">
                <span class="badge">Beta</span>
            </div>
        </div>

        <div class="product-diff-stats">
            <div class="product-diff-stat">
                <span class="product-diff-stat-title">Gefundene Produkte</span>
                <span>{{ $products->total() }}</span>
            </div>
            <div class="product-diff-stat">
                <span class="product-diff-stat-title">Marken</span>
                <span>{{ $brands->count() }}</span>
            </div>
            <div class="product-diff-stat">
                <span class="product-diff-stat-title">Artikelgruppen</span>
                <span>{{ $articleGroups->count() }}</span>
            </div>
            <div class="product-diff-stat">
                <span class="product-diff-stat-title">Lieferanten</span>
                <span>{{ $distributors->count() }}</span>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.products.difference') }}" class="mb-2">
            <div class="product-diff-filters">
                <div>
                    <div class="product-diff-label">Suche (Name / Modell / EAN / Artikel-Nr.)</div>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           class="product-diff-input"
                           placeholder="z.B. Modul 400W oder 4012345678901">
                </div>

                <div>
                    <div class="product-diff-label">Marke</div>
                    <select name="brand_id" class="product-diff-select js-select2">
                        <option value="">Alle</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected($brand->id == $brandId)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="product-diff-label">Artikelgruppe</div>
                    <select name="article_group_id" class="product-diff-select js-select2">
                        <option value="">Alle</option>
                        @foreach($articleGroups as $group)
                            <option value="{{ $group->id }}" @selected($group->id == $articleGroupId)>
                                {{ $group->article_group }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="product-diff-label">Lieferant (optional vorfiltern)</div>
                    <select name="distributor_id" class="product-diff-select js-select2" id="filter-distributor-id">
                        <option value="">Alle Lieferanten</option>
                        @foreach($distributors as $dist)
                            <option value="{{ $dist->id }}" @selected($dist->id == $distributorId)>
                                {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="product-diff-actions">
                <button type="submit" class="btn-primary-soft">
                    Filter anwenden
                </button>
                <a href="{{ route('admin.products.difference') }}" class="btn-ghost">
                    Zurücksetzen
                </a>
            </div>
        </form>

        {{-- Layout: left list, right comparison --}}
        <div class="product-diff-layout">
            {{-- LEFT: product list --}}
            <div class="product-list-card">
                <div class="product-list-header">
                    <div>
                        <h3>Produktliste</h3>
                        <div class="product-list-subtitle">
                            Wähle mehrere Produkte aus, um sie auf der rechten Seite zu vergleichen.
                        </div>
                    </div>
                    <div class="text-right" style="font-size: .78rem;">
                        Seite: {{ $products->firstItem() }}–{{ $products->lastItem() }}
                        von {{ $products->total() }}
                    </div>
                </div>

                <input type="text"
                       id="product-table-search"
                       class="product-diff-input product-table-search"
                       placeholder="In dieser Liste suchen …">

                <div class="product-table-wrapper">
                    <table class="product-table">
                        <thead>
                        <tr>
                            <th style="width: 32px;">
                                <input type="checkbox" id="select-all-page">
                            </th>
                            <th>Produkt</th>
                            <th>Marke</th>
                            <th>Artikelgruppe</th>
                            <th>EAN</th>
                            <th>Artikel-Nr.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           class="js-product-checkbox"
                                           value="{{ $product->id }}">
                                </td>
                                <td>
                                    <div style="font-weight: 600;">
                                        {{ $product->product }}
                                    </div>
                                    <div style="font-size: .75rem; color: #6b7280;">
                                        {{ $product->model ?? '—' }}
                                    </div>
                                </td>
                                <td>{{ optional($product->brand)->name ?? '—' }}</td>
                                <td>{{ optional($product->articleGroup)->article_group ?? '—' }}</td>
                                <td>{{ $product->ean ?? '—' }}</td>
                                <td>{{ $product->article_no ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: .75rem;">
                                    Keine Produkte gefunden.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2" style="gap:.5rem;">
                    <div style="font-size:.78rem; color:#6b7280;">
                        Ausgewählt: <span id="selected-count">0</span> Produkte
                    </div>

                    <div class="d-flex" style="gap:.5rem;">
                        <button type="button" class="btn-ghost" id="clear-selection-btn">
                            Auswahl löschen
                        </button>
                        <button type="button"
                                class="btn-primary-soft"
                                id="compare-btn"
                                disabled>
                            Vergleichen
                        </button>
                    </div>
                </div>

                <div class="mt-2" style="font-size:.76rem; color:#9ca3af;">
                    Tipp: Du kannst Produkte seitenweise auswählen und dann auf
                    <strong>Vergleichen</strong> klicken.
                </div>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>

            {{-- RIGHT: comparison --}}
            <div class="comparison-card">
                <div class="comparison-header">
                    <div>
                        <h3>Vergleich</h3>
                        <div class="comparison-subtitle">
                            Tabellenansicht mit Hervorhebung von Unterschieden (Preis, Verfügbarkeit, etc.).
                        </div>
                    </div>
                    <div class="text-right">
                        <div style="font-size:.78rem; color:#6b7280; margin-bottom:.25rem;">
                            Lieferantenfilter:
                            <strong>
                                @if($distributorId)
                                    {{ optional($distributors->firstWhere('id', $distributorId))->name }}
                                @else
                                    Alle
                                @endif
                            </strong>
                        </div>
                        <div id="comparison-global-min"></div>
                    </div>
                </div>

                <div id="comparison-content">
                    <div class="comparison-placeholder">
                        Noch keine Produkte ausgewählt. Wähle links mindestens zwei Produkte
                        aus und klicke auf <strong>„Vergleichen“</strong>, um die Unterschiede
                        anzuzeigen.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    (function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const compareUrl = "{{ route('admin.products.difference.compare') }}";

        const selectedIds = new Set();

        const checkboxes = document.querySelectorAll('.js-product-checkbox');
        const selectAll = document.getElementById('select-all-page');
        const selectedCountEl = document.getElementById('selected-count');
        const compareBtn = document.getElementById('compare-btn');
        const clearSelectionBtn = document.getElementById('clear-selection-btn');
        const comparisonContent = document.getElementById('comparison-content');
        const filterDistributorSelect = document.getElementById('filter-distributor-id');
        const globalMinEl = document.getElementById('comparison-global-min');
        const tableSearch = document.getElementById('product-table-search');

        function updateSelectedCount() {
            selectedCountEl.textContent = selectedIds.size;
            compareBtn.disabled = selectedIds.size < 2;
        }

        function syncPageCheckboxes() {
            checkboxes.forEach(cb => {
                cb.checked = selectedIds.has(cb.value);
            });

            const allOnPageSelected = [...checkboxes].length > 0 &&
                [...checkboxes].every(cb => selectedIds.has(cb.value));
            selectAll.checked = allOnPageSelected;
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked) {
                    selectedIds.add(this.value);
                } else {
                    selectedIds.delete(this.value);
                }
                updateSelectedCount();
                syncPageCheckboxes();
            });
        });

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                if (this.checked) {
                    checkboxes.forEach(cb => {
                        cb.checked = true;
                        selectedIds.add(cb.value);
                    });
                } else {
                    checkboxes.forEach(cb => {
                        cb.checked = false;
                        selectedIds.delete(cb.value);
                    });
                }
                updateSelectedCount();
            });
        }

        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function () {
                selectedIds.clear();
                updateSelectedCount();
                syncPageCheckboxes();
            });
        }

        if (tableSearch) {
            tableSearch.addEventListener('keyup', function () {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.product-table tbody tr').forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderGlobalMin(globalMin) {
            if (!globalMin || !globalMin.price) {
                globalMinEl.innerHTML = '';
                return;
            }

            const price = parseFloat(globalMin.price).toFixed(2);
            let html  = '<span class="badge badge-success-soft">';
            html += 'Günstigster Preis: ';
            html += escapeHtml(globalMin.distributor) + ' – ';
            html += escapeHtml(globalMin.product) + ' (' + price + ' €)';
            html += '</span>';

            globalMinEl.innerHTML = html;
        }

        function renderComparison(data) {
            if (!data.products || data.products.length === 0) {
                comparisonContent.innerHTML =
                    '<div class="comparison-placeholder">' +
                    'Keine Daten zum Vergleich gefunden.' +
                    '</div>';
                renderGlobalMin(null);
                return;
            }

            renderGlobalMin(data.global_min);

            const products = data.products;
            const grid = data.grid || [];

            let html = '';
            html += '<div class="comparison-table-wrapper">';
            html += '<table class="comparison-table">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>Merkmal</th>';

            products.forEach(p => {
                html += '<th>' + escapeHtml(p.name) + '</th>';
            });
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            function metricRow(label, key) {
                html += '<tr>';
                html += '<td class="comparison-metric-label">' + escapeHtml(label) + '</td>';
                const values = [];

                products.forEach(p => {
                    const value = p[key] ?? '—';
                    values.push(value);
                });

                const allEqual = values.length > 0 && values.every(v => v === values[0]);

                products.forEach(p => {
                    const value = p[key] ?? '—';
                    const cellClasses = (!allEqual && value !== '—') ? 'comparison-diff' : '';
                    html += '<td class="' + cellClasses + '">' + escapeHtml(value) + '</td>';
                });

                html += '</tr>';
            }

            metricRow('Modell', 'model');
            metricRow('Artikel-Nr.', 'article_no');
            metricRow('EAN', 'ean');
            metricRow('Marke', 'brand');
            metricRow('Artikelgruppe', 'article_group');
            metricRow('Farbe', 'color');
            metricRow('Mengeneinheit', 'measure_unit');
            metricRow('Verpackungseinheit', 'package_unit');
            metricRow('Preiseinheit', 'price_unit');

            grid.forEach(row => {
                html += '<tr class="comparison-distributor-row">';
                html += '<td colspan="' + (products.length + 1) + '">';
                html += 'Lieferant: ' + escapeHtml(row.distributor.name);

                if (row.price_diff && row.price_diff.min_price !== null) {
                    html += ' &nbsp;&nbsp; <span style="font-size:0.78rem; color:#4b5563;">';
                    html += 'Preisunterschied: ';
                    html += parseFloat(row.price_diff.min_price).toFixed(2) + '€ – ';
                    html += parseFloat(row.price_diff.max_price).toFixed(2) + '€';
                    html += ' (Δ ' + parseFloat(row.price_diff.difference).toFixed(2) + '€';
                    if (row.price_diff.percent_diff) {
                        html += ', ' + row.price_diff.percent_diff + '%';
                    }
                    html += ')</span>';
                }

                html += '</td>';
                html += '</tr>';

                ['price', 'purchase_price', 'discount_percent', 'availability', 'price_date', 'status']
                    .forEach(metric => {
                        let label;
                        switch (metric) {
                            case 'price':
                                label = 'Verkaufspreis';
                                break;
                            case 'purchase_price':
                                label = 'Einkaufspreis';
                                break;
                            case 'discount_percent':
                                label = 'Rabatt %';
                                break;
                            case 'availability':
                                label = 'Verfügbarkeit';
                                break;
                            case 'price_date':
                                label = 'Preisdatum';
                                break;
                            case 'status':
                                label = 'Status';
                                break;
                            default:
                                label = metric;
                        }

                        html += '<tr>';
                        html += '<td class="comparison-metric-label">' + escapeHtml(label) + '</td>';

                        const values = row.products.map(pr => {
                            let v = pr[metric];
                            if (metric === 'price' || metric === 'purchase_price') {
                                if (v !== null && v !== undefined && v !== '') {
                                    v = parseFloat(v).toFixed(2) + ' €';
                                } else {
                                    v = '—';
                                }
                            } else if (metric === 'discount_percent') {
                                if (v !== null && v !== undefined && v !== '') {
                                    v = v + ' %';
                                } else {
                                    v = '—';
                                }
                            } else {
                                v = (v === null || v === undefined || v === '') ? '—' : v;
                            }
                            return v;
                        });

                        const allEqual = values.length > 0 && values.every(v => v === values[0]);

                        row.products.forEach((pr, idx) => {
                            const v = values[idx];
                            const cellClasses = (!allEqual && v !== '—') ? 'comparison-diff' : '';
                            html += '<td class="' + cellClasses + '">' + escapeHtml(v) + '</td>';
                        });

                        html += '</tr>';
                    });
            });

            html += '</tbody>';
            html += '</table>';
            html += '</div>';

            comparisonContent.innerHTML = html;
        }

        if (compareBtn) {
            compareBtn.addEventListener('click', function () {
                if (selectedIds.size < 2) {
                    alert('Bitte wähle mindestens zwei Produkte aus.');
                    return;
                }

                const distributorId = filterDistributorSelect ? filterDistributorSelect.value : '';

                comparisonContent.innerHTML =
                    '<div class="comparison-placeholder">Daten werden geladen ...</div>';
                globalMinEl.innerHTML = '';

                fetch(compareUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_ids: Array.from(selectedIds),
                        distributor_id: distributorId || null
                    }),
                })
                    .then(resp => resp.json())
                    .then(data => {
                        renderComparison(data);
                    })
                    .catch(() => {
                        comparisonContent.innerHTML =
                            '<div class="comparison-placeholder">Fehler beim Laden der Vergleichsdaten.</div>';
                        renderGlobalMin(null);
                    });
            });
        }

        updateSelectedCount();
        syncPageCheckboxes();

        // Select2 initialisieren (jQuery muss in deinem Layout vorhanden sein)
        if (window.jQuery) {
            jQuery(function ($) {
                $('.js-select2').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: 'Bitte auswählen'
                });
            });
        }
    })();
</script>
@endsection
