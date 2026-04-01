@extends('admin.layouts.app')

@section('title', 'Produkt CSV Import')

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

<style>
    .import-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:20px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }
    .import-field{
        width:100%;
        border:1px solid #d1d5db;
        border-radius:12px;
        padding:10px 12px;
        font-size:14px;
        outline:none;
        background:#fff;
    }
    .import-field:focus{
        border-color:#93c21c;
        box-shadow:0 0 0 3px rgba(147,194,28,.15);
    }
    .btn-brand{
        background:#93c21c;
        color:#fff;
        border:none;
        border-radius:12px;
        padding:10px 16px;
        font-weight:700;
    }
    .btn-brand:hover{ background:#7ea61a; }
    .btn-soft{
        background:#f3f4f6;
        color:#111827;
        border:none;
        border-radius:12px;
        padding:10px 16px;
        font-weight:700;
    }
    .preview-table th, .preview-table td{
        border-bottom:1px solid #e5e7eb;
        padding:10px;
        text-align:left;
        font-size:13px;
        vertical-align:top;
    }

    /* Select2 styling */
    .select2-container{
        width:100% !important;
    }
    .select2-container .select2-selection--single{
        height:44px !important;
        border:1px solid #d1d5db !important;
        border-radius:12px !important;
        background:#fff !important;
        display:flex !important;
        align-items:center !important;
        padding:0 10px !important;
        box-shadow:none !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered{
        color:#111827 !important;
        line-height:42px !important;
        padding-left:0 !important;
        padding-right:24px !important;
        font-size:14px !important;
    }
    .select2-container .select2-selection--single .select2-selection__placeholder{
        color:#9ca3af !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow{
        height:42px !important;
        right:10px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single{
        border-color:#93c21c !important;
        box-shadow:0 0 0 3px rgba(147,194,28,.15) !important;
    }
    .select2-dropdown{
        border:1px solid #d1d5db !important;
        border-radius:12px !important;
        overflow:hidden !important;
        box-shadow:0 16px 40px rgba(15,23,42,.12) !important;
    }
    .select2-search--dropdown{
        padding:10px !important;
    }
    .select2-search--dropdown .select2-search__field{
        border:1px solid #d1d5db !important;
        border-radius:10px !important;
        padding:8px 10px !important;
        outline:none !important;
    }
    .select2-results__option{
        padding:10px 12px !important;
        font-size:14px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected]{
        background:#93c21c !important;
        color:#fff !important;
    }
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Produkt CSV Import</h1>
        <p class="text-sm text-gray-600 mt-1">
            Produkte, Distributor-Preise und Bilder automatisch importieren.
        </p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1">
            <div class="import-card p-5">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">CSV Datei</label>
                            <input type="file" name="csv_file" class="import-field" accept=".csv,.txt" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Distributor</label>
                            <select name="default_distributor_id" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value=""></option>
                                @foreach($distributors as $distributor)
                                    <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Hersteller / Brand</label>
                            <select name="default_brand_id" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value=""></option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Article Group</label>
                            <select name="default_article_group_id" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value=""></option>
                                @foreach($articleGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->article_group }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Sub Article</label>
                            <select name="default_sub_article_id" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value=""></option>
                                @foreach($subArticles as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name ?? ('#'.$sub->id) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Measure Unit</label>
                            <select name="default_measure_unit_id" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value=""></option>
                                @foreach($measures as $measure)
                                    <option value="{{ $measure->id }}">{{ $measure->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Preis speichern als</label>
                            <select name="price_target" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value="purchase_price">EK / purchase_price</option>
                                <option value="price">UVP / distributor price</option>
                                <option value="discount_price">Rabattpreis</option>
                                <option value="retail_price">Produkt retail_price</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Availability</label>
                            <select name="default_availability" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value=""></option>
                                <option value="Sofort lieferbar">Sofort lieferbar</option>
                                <option value="Auf Lager">Auf Lager</option>
                                <option value="Bestellware">Bestellware</option>
                                <option value="Nicht verfügbar">Nicht verfügbar</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Preis Status</label>
                            <select name="default_status" class="import-field js-select2" data-placeholder="Bitte wählen">
                                <option value="Published">Published</option>
                                <option value="Unpublished">Unpublished</option>
                            </select>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="has_header" value="1">
                            CSV hat Kopfzeile
                        </label>

                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="skip_existing_images" value="1" checked>
                            Vorhandene Bilder nicht doppelt speichern
                        </label>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <button type="button" class="btn-soft" id="previewBtn">Vorschau laden</button>
                            <button type="button" class="btn-brand" id="importBtn">Import starten</button>
                        </div>
                    </div>
                </form>

                <div id="resultBox" class="hidden mt-5 rounded-xl border p-4 text-sm"></div>
            </div>
        </div>

        <div class="xl:col-span-2">
            <div class="import-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">CSV Vorschau</h2>
                    <div id="previewCount" class="text-sm text-gray-500"></div>
                </div>

                <div class="overflow-auto">
                    <table class="w-full preview-table">
                        <thead>
                            <tr class="bg-gray-50">
                                <th>#</th>
                                <th>Artikel-Nr.</th>
                                <th>Produkt</th>
                                <th>Menge</th>
                                <th>Preis</th>
                                <th>Total</th>
                                <th>Bild URL</th>
                            </tr>
                        </thead>
                        <tbody id="previewBody">
                            <tr>
                                <td colspan="7" class="text-center text-gray-400 py-10">
                                    Noch keine Vorschau geladen.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/select2.full.min.js') }}"></script>

<script>
(function () {
    const form = document.getElementById('importForm');
    const previewBtn = document.getElementById('previewBtn');
    const importBtn = document.getElementById('importBtn');
    const previewBody = document.getElementById('previewBody');
    const previewCount = document.getElementById('previewCount');
    const resultBox = document.getElementById('resultBox');

    function initSelect2() {
        $('.js-select2').each(function () {
            const $el = $(this);
            const placeholder = $el.data('placeholder') || 'Bitte wählen';

            $el.select2({
                width: '100%',
                placeholder: placeholder,
                allowClear: true,
                dropdownAutoWidth: false
            });
        });
    }

    function getFormData() {
        return new FormData(form);
    }

    function showResult(html, isError = false) {
        resultBox.classList.remove('hidden');
        resultBox.className = 'mt-5 rounded-xl border p-4 text-sm ' + (
            isError
                ? 'border-red-200 bg-red-50 text-red-800'
                : 'border-green-200 bg-green-50 text-green-800'
        );
        resultBox.innerHTML = html;
    }

    function renderPreview(rows) {
        if (!rows || !rows.length) {
            previewBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-gray-400 py-10">Keine Daten gefunden.</td>
                </tr>
            `;
            return;
        }

        previewBody.innerHTML = rows.map((row, index) => {
            const link = row.image_url
                ? `<a href="${row.image_url}" target="_blank" class="text-blue-600 underline break-all">${row.image_url}</a>`
                : '-';

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${row.article_no ?? ''}</td>
                    <td>${row.product ?? ''}</td>
                    <td>${row.qty ?? ''}</td>
                    <td>${row.price ?? ''}</td>
                    <td>${row.total ?? ''}</td>
                    <td>${link}</td>
                </tr>
            `;
        }).join('');
    }

    async function sendForm(url) {
        const response = await fetch(url, {
            method: 'POST',
            body: getFormData(),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (!response.ok) {
            let message = data.message || 'Fehler';
            if (data.errors) {
                message += '<br><br>' + Object.values(data.errors).flat().join('<br>');
            }
            throw new Error(message);
        }

        return data;
    }

    previewBtn.addEventListener('click', async function () {
        try {
            previewBtn.disabled = true;
            previewBtn.innerText = 'Lade...';

            const data = await sendForm('{{ route('products.import.preview') }}');

            renderPreview(data.rows || []);
            previewCount.innerText = `${data.count || 0} Zeilen erkannt`;

            showResult(`Vorschau erfolgreich geladen. Erkannte Zeilen: <strong>${data.count || 0}</strong>`);
        } catch (e) {
            showResult(e.message, true);
        } finally {
            previewBtn.disabled = false;
            previewBtn.innerText = 'Vorschau laden';
        }
    });

    importBtn.addEventListener('click', async function () {
        try {
            importBtn.disabled = true;
            importBtn.innerText = 'Import läuft...';

            const data = await sendForm('{{ route('products.import.store') }}');
            const s = data.summary || {};

            let html = `
                <div><strong>Import abgeschlossen</strong></div>
                <div class="mt-2">Neue Produkte: ${s.created_products ?? 0}</div>
                <div>Aktualisierte Produkte: ${s.updated_products ?? 0}</div>
                <div>Gespeicherte Preise: ${s.prices_saved ?? 0}</div>
                <div>Gespeicherte Bilder: ${s.images_saved ?? 0}</div>
                <div>Fehler: ${s.errors_count ?? 0}</div>
            `;

            if (s.errors && s.errors.length) {
                html += `<div class="mt-3"><strong>Fehlerdetails:</strong><ul class="list-disc pl-5 mt-1">`;
                s.errors.forEach(err => {
                    html += `<li>Zeile ${err.row}: ${err.message}</li>`;
                });
                html += `</ul></div>`;
            }

            showResult(html, false);
        } catch (e) {
            showResult(e.message, true);
        } finally {
            importBtn.disabled = false;
            importBtn.innerText = 'Import starten';
        }
    });

    $(document).ready(function () {
        initSelect2();
    });
})();
</script>
@endsection