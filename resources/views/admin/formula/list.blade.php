@extends('admin.layouts.app')

@section('title', 'Formulaliste')

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

    <style>
    :root{
        --bg:#f3f4f6;
        --card:#fff;
        --text:#111827;
        --muted:#6b7280;
        --border:#e5e7eb;
        --primary:#93c21c;
        --primary-hover:#7baa18;
        --primary-soft:#f4fae7;
        --warning:#f59e0b;
        --warning-soft:#fffbeb;
        --danger:#ef4444;
        --danger-soft:#fef2f2;
        --info:#74b2d4;
        --info-soft:#eef7fc;
        --shadow:0 8px 24px -18px rgba(0,0,0,.35);
    }

    .fl-wrap{ 
        color:var(--text);
        font-family:Inter, system-ui, -apple-system, sans-serif;
    }

    .fl-head{
        display:flex;
        justify-content:space-between;
        align-items:flex-end;
        gap:16px;
        flex-wrap:wrap;
        margin-bottom:20px;
    }

    .fl-title{
        font-size:26px;
        font-weight:900;
        letter-spacing:-.03em;
    }

    .fl-sub{
        color:var(--muted);
        font-size:14px;
        margin-top:4px;
    }

    .fl-btn{
        border:0;
        background:var(--primary);
        color:#fff;
        border-radius:10px;
        padding:10px 15px;
        font-weight:900;
        cursor:pointer;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        min-height:40px;
    }

    .fl-btn:hover{
        background:var(--primary-hover);
        color:#fff;
        text-decoration:none;
    }

    .fl-btn-soft{
        border:1px solid var(--border);
        background:#fff;
        color:var(--text);
        border-radius:10px;
        padding:10px 14px;
        font-weight:800;
        cursor:pointer;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-height:40px;
    }

    .fl-btn-soft:hover{
        background:#f9fafb;
        color:var(--text);
        text-decoration:none;
    }

    .fl-icon-btn{
        width:36px;
        height:36px;
        border-radius:9px;
        border:1px solid var(--border);
        background:#fff;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        color:var(--muted);
        font-weight:900;
        text-decoration:none;
    }

    .fl-icon-btn:hover{
        color:var(--text);
        text-decoration:none;
        background:#f9fafb;
    }

    .fl-icon-btn.edit{
        background:var(--primary-soft);
        color:var(--primary);
        border-color:#d8edaa;
    }

    .fl-icon-btn.test{
        background:var(--warning-soft);
        color:var(--warning);
        border-color:#fde68a;
    }

    .fl-icon-btn.danger{
        background:var(--danger-soft);
        color:var(--danger);
        border-color:#fecaca;
    }

    .fl-stats{
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:14px;
        margin-bottom:18px;
    }

    .fl-stat{
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        padding:16px;
        box-shadow:var(--shadow);
    }

    .fl-stat-label{
        font-size:11px;
        color:var(--muted);
        text-transform:uppercase;
        letter-spacing:.07em;
        font-weight:900;
    }

    .fl-stat-value{
        font-size:24px;
        font-weight:900;
        margin-top:5px;
    }

    .fl-toolbar{
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        padding:14px;
        display:flex;
        gap:12px;
        flex-wrap:wrap;
        align-items:end;
        margin-bottom:18px;
    }

    .fl-field{
        display:flex;
        flex-direction:column;
        gap:6px;
    }

    .fl-field.search{
        flex:1;
        min-width:280px;
    }

    .fl-label{
        font-size:11px;
        color:var(--muted);
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
    }

    .fl-input{
        width:100%;
        border:1px solid var(--border);
        background:#f9fafb;
        border-radius:10px;
        padding:10px 12px;
        outline:none;
        font-size:14px;
        min-height:40px;
    }

    .fl-input:focus{
        border-color:var(--primary);
        background:#fff;
        box-shadow:0 0 0 3px var(--primary-soft);
    }

    .fl-card{
        background:#fff;
        border:1px solid var(--border);
        border-radius:18px;
        overflow:hidden;
        box-shadow:var(--shadow);
    }

    .fl-table-wrap{
        overflow:auto;
    }

    .fl-table{
        width:100%;
        min-width:1000px;
        border-collapse:collapse;
    }

    .fl-table th,
    .fl-table td{
        padding:13px 14px;
        border-bottom:1px solid var(--border);
        vertical-align:middle;
    }

    .fl-table th{
        background:#fafafa;
        color:var(--muted);
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        font-weight:900;
    }

    .fl-name{
        font-weight:900;
    }

    .fl-muted{
        color:var(--muted);
        font-size:12px;
        margin-top:3px;
    }

    .fl-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        padding:5px 10px;
        font-size:12px;
        font-weight:900;
        background:var(--primary-soft);
        color:var(--primary);
        border:1px solid #d8edaa;
    }

    .fl-badge.empty{
        background:#f9fafb;
        color:var(--muted);
        border-color:var(--border);
    }

    .fl-actions{
        display:flex;
        justify-content:flex-end;
        gap:7px;
        flex-wrap:wrap;
    }

    .fl-sub-row{
        display:none;
        background:#f9fafb;
    }

    .fl-sub-row.open{
        display:table-row;
    }

    .fl-sub-box{
        padding:16px;
    }

    .fl-sub-head{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
        margin-bottom:12px;
    }

    .fl-formula-grid{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:12px;
    }

    .fl-formula-card{
        background:#fff;
        border:1px solid var(--border);
        border-radius:16px;
        padding:14px;
        box-shadow:0 6px 18px -18px rgba(0,0,0,.45);
    }

    .fl-formula-top{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:12px;
    }

    .fl-formula-title{
        font-weight:900;
        font-size:15px;
    }

    .fl-meta{
        margin-top:10px;
        display:grid;
        gap:5px;
        color:var(--muted);
        font-size:12px;
    }

    .fl-meta strong{
        color:#374151;
    }

    .fl-dot{
        width:8px;
        height:8px;
        border-radius:99px;
        display:inline-block;
        margin-right:6px;
    }

    .fl-dot.green{ background:#22c55e; }
    .fl-dot.yellow{ background:#f59e0b; }
    .fl-dot.red{ background:#ef4444; }

    .fl-empty{
        padding:50px;
        text-align:center;
        color:var(--muted);
    }

    .fl-empty-small{
        background:#fff;
        border:1px dashed var(--border);
        border-radius:16px;
        padding:24px;
        text-align:center;
        color:var(--muted);
    }

    .fl-pagination{
        margin-top:16px;
        background:#fff;
        border:1px solid var(--border);
        border-radius:14px;
        padding:14px;
    }

    .fl-modal-backdrop{
        position:fixed;
        inset:0;
        background:rgba(17,24,39,.58);
        backdrop-filter:blur(3px);
        z-index:2000;
        display:none;
        align-items:center;
        justify-content:center;
        padding:18px;
    }

    .fl-modal-backdrop.open{
        display:flex;
    }

    .fl-modal{
        width:100%;
        max-width:620px;
        background:#fff;
        border-radius:18px;
        overflow:hidden;
        box-shadow:0 20px 50px rgba(0,0,0,.25);
    }

    .fl-modal-head,
    .fl-modal-foot{
        padding:16px 18px;
        background:#fafafa;
        border-bottom:1px solid var(--border);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
    }

    .fl-modal-foot{
        border-top:1px solid var(--border);
        border-bottom:0;
        justify-content:flex-end;
    }

    .fl-modal-title{
        font-size:16px;
        font-weight:900;
        margin:0;
    }

    .fl-modal-body{
        padding:18px;
        max-height:70vh;
        overflow:auto;
    }

    .fl-error{
        color:var(--danger);
        font-size:12px;
        font-weight:800;
        margin-top:7px;
        display:none;
    }

    .fl-toast-wrap{
        position:fixed;
        right:20px;
        bottom:20px;
        z-index:3000;
        display:flex;
        flex-direction:column;
        gap:10px;
    }

    .fl-toast{
        background:#fff;
        border:1px solid var(--border);
        border-radius:14px;
        padding:12px 14px;
        box-shadow:0 12px 30px rgba(0,0,0,.15);
        min-width:280px;
    }

    .select2-container{
        width:100% !important;
    }

    .select2-container--default .select2-selection--single{
        border:1px solid var(--border) !important;
        background:#f9fafb !important;
        border-radius:10px !important;
        min-height:42px !important;
        padding:5px 8px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height:40px !important;
    }

    .select2-dropdown{
        border-color:var(--border) !important;
        z-index:2500 !important;
    }

    @media(max-width:1000px){
        .fl-stats{
            grid-template-columns:repeat(2,minmax(0,1fr));
        }

        .fl-formula-grid{
            grid-template-columns:1fr;
        }
    }

    @media(max-width:700px){
        .fl-wrap{
            margin-top:90px;
            padding:0 16px 32px;
        }

        .fl-stats{
            grid-template-columns:1fr;
        }

        .fl-head{
            align-items:stretch;
        }

        .fl-head .fl-btn{
            width:100%;
        }

        .fl-toolbar{
            align-items:stretch;
        }

        .fl-field.search{
            min-width:100%;
        }

        .fl-toolbar .fl-btn,
        .fl-toolbar .fl-btn-soft{
            width:100%;
        }

        .fl-modal{
            max-width:100%;
        }
    }
    </style>
@endsection

@php
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

$isPaginator = $products instanceof LengthAwarePaginator || $products instanceof AbstractPaginator;
$items = collect($isPaginator ? $products->items() : $products);

$totalProducts = $isPaginator ? $products->total() : $items->count();
$totalFormulas = $items->sum(fn($product) => $product->formulas->count());
$productsWithFormulas = $items->filter(fn($product) => $product->formulas->count() > 0)->count();
$productsWithoutFormulas = max($items->count() - $productsWithFormulas, 0);
@endphp

@section('content')
    <div class="fl-wrap">

        <div class="fl-head">
            <div>
                <div class="fl-title">FORMULARE</div>
                <div class="fl-sub">Produkt-Formulare nach Artikelgruppe verwalten, testen und bearbeiten.</div>
            </div>

            <button type="button" class="fl-btn" onclick="openFormulaModal()">
                + Neues Formular
            </button>
        </div>

        <div class="fl-stats">
            <div class="fl-stat">
                <div class="fl-stat-label">Artikelgruppen</div>
                <div class="fl-stat-value">{{ $totalProducts }}</div>
            </div>

            <div class="fl-stat">
                <div class="fl-stat-label">Formulare</div>
                <div class="fl-stat-value">{{ $totalFormulas }}</div>
            </div>

            <div class="fl-stat">
                <div class="fl-stat-label">Mit Formular</div>
                <div class="fl-stat-value">{{ $productsWithFormulas }}</div>
            </div>

            <div class="fl-stat">
                <div class="fl-stat-label">Ohne Formular</div>
                <div class="fl-stat-value">{{ $productsWithoutFormulas }}</div>
            </div>
        </div>

        <form id="filterForm" class="fl-toolbar" action="{{ route('product.formula.index') }}" method="GET">
            <div class="fl-field search">
                <label class="fl-label">Suche</label>
                <input
                    type="text"
                    name="search"
                    class="fl-input"
                    placeholder="Suche nach Bereich oder Produkt"
                    value="{{ request('search') }}"
                >
            </div>

            <button type="submit" class="fl-btn-soft">Suchen</button>
            <a href="{{ route('product.formula.index') }}" class="fl-btn-soft">Zurücksetzen</a>
        </form>

        <div class="fl-card">
            @if($items->isEmpty())
                <div class="fl-empty">
                    Keine Formulare oder Artikelgruppen gefunden.
                </div>
            @else
                <div class="fl-table-wrap">
                    <table class="fl-table">
                        <thead>
                            <tr>
                                <th style="width:55px;"></th>
                                <th style="width:90px;">ID</th>
                                <th>Artikel Gruppe</th>
                                <th style="width:160px;">Formulare</th>
                                <th style="width:160px;">Status</th>
                                <th style="text-align:right;width:170px;">Aktion</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($items as $product)
                                @php
        $formulaCount = $product->formulas->count();
                                @endphp

                                <tr>
                                    <td>
                                        <button
                                            type="button"
                                            class="fl-icon-btn js-toggle-formulas"
                                            data-target="formula-row-{{ $product->id }}"
                                            title="Formulare anzeigen"
                                        >
                                            ↓
                                        </button>
                                    </td>

                                    <td>{{ $product->id }}</td>

                                    <td>
                                        <div class="fl-name">{{ $product->article_group }}</div>
                                        <div class="fl-muted">
                                            Produkt-ID: {{ $product->id }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="fl-badge {{ $formulaCount ? '' : 'empty' }}">
                                            {{ $formulaCount }} Formular{{ $formulaCount === 1 ? '' : 'e' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($formulaCount)
                                            <span class="fl-badge">Aktiv</span>
                                        @else
                                            <span class="fl-badge empty">Leer</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="fl-actions">
                                            <button
                                                type="button"
                                                class="fl-btn-soft"
                                                onclick="openFormulaModal({{ $product->id }})"
                                            >
                                                + Formular
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="fl-sub-row" id="formula-row-{{ $product->id }}">
                                    <td colspan="6">
                                        <div class="fl-sub-box">
                                            <div class="fl-sub-head">
                                                <div>
                                                    <strong>Formulare</strong>
                                                    <div class="fl-muted">{{ $product->article_group }}</div>
                                                </div>

                                                <input
                                                    type="text"
                                                    class="fl-input js-formula-search"
                                                    data-target="formula-list-{{ $product->id }}"
                                                    placeholder="In diesen Formularen suchen …"
                                                    style="max-width:300px;"
                                                >
                                            </div>

                                            @if($formulaCount)
                                                <div class="fl-formula-grid" id="formula-list-{{ $product->id }}">
                                                    @foreach($product->formulas as $form)
                                                        <div class="fl-formula-card">
                                                            <div class="fl-formula-top">
                                                                <div>
                                                                    <div class="fl-formula-title">
                                                                        {{ $form->section_name ?: 'Unbenanntes Formular' }}
                                                                    </div>

                                                                    <div class="fl-muted">
                                                                        Erstellt am:
                                                                        {{ optional($form->created_at)->format('d.m.Y H:i') ?? '—' }}
                                                                    </div>
                                                                </div>

                                                                <div class="fl-actions">
                                                                    <a
                                                                        href="{{ url('edit/product-formula/' . $form->id . '/' . $form->product_id) }}"
                                                                        class="fl-icon-btn edit"
                                                                        title="Bearbeiten"
                                                                    >
                                                                        ✎
                                                                    </a>

                                                                    <a
                                                                        href="{{ url('/product-formula/' . $form->id . '/test') }}"
                                                                        class="fl-icon-btn test"
                                                                        title="Testen"
                                                                    >
                                                                        ▶
                                                                    </a>

                                                                    <button
                                                                        type="button"
                                                                        class="fl-icon-btn danger"
                                                                        onclick="confirmDelete({{ $form->id }})"
                                                                        title="Löschen"
                                                                    >
                                                                        ×
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="fl-meta">
                                                                <div>
                                                                    <span class="fl-dot green"></span>
                                                                    <strong>Erstellt von:</strong>
                                                                    {{ $form->creator->name ?? 'Unbekannt' }}
                                                                </div>

                                                                <div>
                                                                    <span class="fl-dot yellow"></span>
                                                                    <strong>Bearbeitet von:</strong>
                                                                    {{ $form->editor->name ?? 'Nie bearbeitet' }}
                                                                </div>

                                                                @if($form->deleter)
                                                                    <div>
                                                                        <span class="fl-dot red"></span>
                                                                        <strong>Gelöscht von:</strong>
                                                                        <span style="color:var(--danger);font-weight:900;">
                                                                            {{ $form->deleter->name }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="fl-empty-small">
                                                    Keine Formeln vorhanden für dieses Produkt.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($isPaginator && method_exists($products, 'links') && $products->hasPages())
            <div class="fl-pagination">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                    <div style="font-size:12px;color:#6b7280;">
                        Zeige <strong>{{ $products->firstItem() ?? 0 }}</strong>
                        bis <strong>{{ $products->lastItem() ?? 0 }}</strong>
                        von <strong>{{ $products->total() }}</strong> Einträgen
                    </div>

                    <div>
                        {{ $products->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Create Formula Modal --}}
    <div class="fl-modal-backdrop" id="createFormulaModal">
        <div class="fl-modal">
            <div class="fl-modal-head">
                <h3 class="fl-modal-title">Neues Formular erstellen</h3>
                <button type="button" class="fl-icon-btn" onclick="closeFormulaModal()">×</button>
            </div>

            <form id="formulaSelectForm" method="POST" action="{{ route('product.formula.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="fl-modal-body">
                    <div class="fl-field">
                        <label class="fl-label">Artikel Gruppe</label>

                        <select name="product_id" id="product_id" class="fl-input select2">
                            <option value=""></option>
                            @foreach($items as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->article_group }}
                                </option>
                            @endforeach
                        </select>

                        <div id="productError" class="fl-error">
                            Bitte wählen Sie ein Produkt.
                        </div>
                    </div>

                    <div id="formPreviewContainer" style="margin-top:16px;"></div>

                    <div class="fl-empty-small" style="margin-top:16px;text-align:left;">
                        Nach dem Erstellen wird automatisch ein leeres Formular angelegt und direkt im Editor geöffnet.
                    </div>
                </div>

                <div class="fl-modal-foot">
                    <button type="button" class="fl-btn-soft" onclick="closeFormulaModal()">Abbrechen</button>
                    <button type="button" onclick="submitFormulaData()" class="fl-btn">Einreichen</button>
                </div>
            </form>
        </div>
    </div>

    <div class="fl-toast-wrap" id="toast-wrap"></div>
@endsection

@section('script')
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
    function openFormulaModal(productId = null) {
        const modal = document.getElementById('createFormulaModal');
        if (!modal) return;

        modal.classList.add('open');
        document.body.style.overflow = 'hidden';

        if (window.jQuery && $('#product_id').length) {
            if (productId) {
                $('#product_id').val(String(productId)).trigger('change');
            }

            setTimeout(function () {
                $('#product_id').select2({
                    placeholder: 'Wählen Sie ein Produkt',
                    allowClear: true,
                    dropdownParent: $('#createFormulaModal')
                });
            }, 50);
        }
    }

    function closeFormulaModal() {
        const modal = document.getElementById('createFormulaModal');
        if (!modal) return;

        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    function toast(type, message) {
        const wrap = document.getElementById('toast-wrap');
        if (!wrap) return;

        const el = document.createElement('div');
        el.className = 'fl-toast';
        el.innerHTML = `
            <strong>${type === 'ok' ? 'Erfolgreich' : 'Hinweis'}</strong>
            <div style="font-size:13px;color:#374151;margin-top:4px;">${message}</div>
        `;

        wrap.appendChild(el);

        setTimeout(() => {
            try { el.remove(); } catch(e) {}
        }, 3500);
    }

    function submitFormulaData() {
        const productId = $('#product_id').val();

        if (!productId) {
            $('#productError').show();
            return;
        }

        $('#productError').hide();

        fetch(`/product-formula/store`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                section_name: 'Unbenannte Checkliste',
                fields: JSON.stringify([])
            })
        })
        .then(async response => {
            let data = {};

            try {
                data = await response.json();
            } catch(e) {}

            if (!response.ok) {
                throw new Error(data.message || 'Formel konnte nicht gespeichert werden.');
            }

            return data;
        })
        .then(response => {
            if (response.success && response.id) {
                window.location.href = `/product-formula/${response.id}/${response.product_id}/edit`;
                return;
            }

            Swal.fire('Fehler', 'Formel konnte nicht gespeichert werden.', 'error');
        })
        .catch(error => {
            Swal.fire('Fehler', error.message || 'Serverfehler beim Speichern.', 'error');
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Achtung!',
            text: 'Diese Aktion ist endgültig und löscht alle zugehörigen Daten. Bitte geben Sie Ihr Passwort zur Bestätigung ein.',
            icon: 'warning',
            input: 'password',
            inputLabel: 'Passwort eingeben',
            inputPlaceholder: 'Ihr Passwort...',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Passwort erforderlich!');
                    return false;
                }

                return fetch(`/product-formula/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        password: password
                    })
                })
                .then(async response => {
                    let data = {};

                    try {
                        data = await response.json();
                    } catch(e) {}

                    if (!response.ok) {
                        throw new Error(data.message || 'Löschen fehlgeschlagen');
                    }

                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Fehler: ${error.message}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire('Gelöscht!', 'Das Formular wurde erfolgreich gelöscht.', 'success')
                    .then(() => location.reload());
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fl-modal-backdrop')) {
            e.target.classList.remove('open');
            document.body.style.overflow = '';
            return;
        }

        const toggleBtn = e.target.closest('.js-toggle-formulas');

        if (toggleBtn) {
            const row = document.getElementById(toggleBtn.dataset.target);
            if (!row) return;

            row.classList.toggle('open');
            toggleBtn.innerHTML = row.classList.contains('open') ? '↑' : '↓';
            return;
        }
    });

    document.addEventListener('input', function(e) {
        const input = e.target.closest('.js-formula-search');
        if (!input) return;

        const wrap = document.getElementById(input.dataset.target);
        if (!wrap) return;

        const term = input.value.toLowerCase().trim();

        wrap.querySelectorAll('.fl-formula-card').forEach(card => {
            card.style.display = card.innerText.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;

        document.querySelectorAll('.fl-modal-backdrop.open').forEach(el => {
            el.classList.remove('open');
        });

        document.body.style.overflow = '';
    });

    $(document).ready(function () {
        if ($('#product_id').length) {
            $('#product_id').select2({
                placeholder: 'Wählen Sie ein Produkt',
                allowClear: true,
                dropdownParent: $('#createFormulaModal')
            });
        }
    });
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
                label: 'Produktliste',
                url: "{{ url('product') }}",
            },
            {
                label: 'Produkt-Formulare',
                url: "{{ url('product') }}",
                clickable: false
            },

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush