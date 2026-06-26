 @extends('admin.layouts.app')

@section('title') Artikel Anlagen @endsection

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />
<style>
  .field-invalid,
  .field-invalid + .select2 .select2-selection,
  .field-invalid.select2-hidden-accessible + .select2 .select2-selection {
    border-color: #dc2626 !important;
    box-shadow: 0 0 0 0.12rem rgba(220, 38, 38, 0.15) !important;
  }

  .validation-error-text {
    display: block;
    margin-top: .35rem;
    font-size: .78rem;
    color: #dc2626;
    font-weight: 500;
    line-height: 1.35;
  }

  .validation-summary {
    display: none;
    margin-bottom: .85rem;
    padding: .75rem .9rem;
    border-radius: 12px;
    border: 1px solid rgba(220,38,38,.18);
    background: rgba(220,38,38,.06);
    color: #991b1b;
    font-size: .84rem;
  }

  .validation-summary.show {
    display: block;
  }

  .wizard-step.has-errors .circle {
    border-color: #dc2626 !important;
    color: #dc2626 !important;
    background: #fff5f5 !important;
  }

  .locked-field.field-invalid .locked-select,
  .locked-field.field-invalid .locked-control::after {
    border-color: #dc2626 !important;
  }
</style>
<style>
    :root {
        --wizard-bg: #f6f7fb;
        --wizard-shell: #ffffff;
        --wizard-border: rgba(15, 23, 42, .08);
        --wizard-muted: #6b7280;
        --wizard-accent: #2563eb;
        --wizard-accent-soft: rgba(37, 99, 235, .08);
        --wizard-success: #16a34a;
        --wizard-warning: #eab308;
        --wizard-radius-xl: 18px;
        --wizard-radius-lg: 14px;
        --wizard-shadow: 0 18px 45px rgba(15, 23, 42, .08);
    }

    body {
        background: var(--wizard-bg) !important;
    }

    .container {
        max-width: 1580px !important;
    }

    .wizard-shell {
        border-radius: var(--wizard-radius-xl);
        background: var(--wizard-shell);
        box-shadow: var(--wizard-shadow);
        border: 1px solid var(--wizard-border);
        padding: 1.25rem 1.5rem 1.5rem;
    }

    .wizard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .wizard-title-block h2 {
        font-size: 1.25rem;
        margin: 0;
    }

    .wizard-title-block span {
        font-size: .85rem;
        color: var(--wizard-muted);
    }

    .wizard-meta {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .wizard-badge {
        padding: .25rem .7rem;
        border-radius: 999px;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        background: var(--wizard-accent-soft);
        color: var(--wizard-accent);
        font-weight: 600;
    }

    .wizard-progress-shell {
        margin-bottom: .75rem;
    }

    .wizard-progress-shell small {
        display: flex;
        justify-content: space-between;
        font-size: .75rem;
        color: var(--wizard-muted);
        margin-bottom: .25rem;
    }

    .progress {
        height: 9px;
        border-radius: 999px;
        overflow: hidden;
        background: rgba(15, 23, 42, .06);
        margin-bottom: 0;
    }

    .progress-bar {
        border-radius: 999px;
        font-size: 0;
    }

    .wizard-steps-row {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: .75rem;
        margin-bottom: .75rem;
    }

    .wizard-step {
        text-align: center;
        position: relative;
        cursor: pointer;
        padding-bottom: .25rem;
    }

    .wizard-step .circle {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        margin: 0 auto 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(148, 163, 184, .7);
        background: #f9fafb;
        color: #64748b;
        transition: all .15s ease-out;
    }

    .wizard-step .circle i {
        width: 18px;
        height: 18px;
    }

    .wizard-step .label {
        font-size: .76rem;
        color: #4b5563;
        white-space: nowrap;
    }

    .wizard-step.active .circle {
        background: var(--wizard-accent);
        color: #fff;
        border-color: var(--wizard-accent);
        box-shadow: 0 0 0 1px rgba(37, 99, 235, .25);
        transform: translateY(-1px);
    }

    .wizard-step.saved .circle {
        background: var(--wizard-success);
        border-color: var(--wizard-success);
        color: #fff;
    }

    .wizard-step.skipped .circle {
        background: #fef9c3;
        border-color: var(--wizard-warning);
        color: #92400e;
    }

    .wizard-step .label-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 1px 6px;
        font-size: .65rem;
        margin-top: 2px;
        background: #f3f4f6;
        color: #6b7280;
    }

    .wizard-step.saved .label-pill {
        background: rgba(22, 163, 74, .12);
        color: #15803d;
    }

    .wizard-step.skipped .label-pill {
        background: rgba(234, 179, 8, .1);
        color: #92400e;
    }

    .stage-content {
        display: none;
    }

    .stage-content.active {
        display: block;
    }

    .wizard-footer {
        border-top: 1px solid rgba(15, 23, 42, .06);
        margin-top: 1rem;
        padding-top: .75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
    }

    .wizard-footer-left {
        font-size: .78rem;
        color: var(--wizard-muted);
    }

    .wizard-footer-left strong {
        font-weight: 600;
    }

    .btn-skip-stage {
        border-radius: 999px;
        padding: .25rem .75rem;
        font-size: .78rem;
    }

    .btn-skip-stage i {
        width: 14px;
        height: 14px;
        margin-right: 3px;
    }

    .wizard-footer-right .btn {
        min-width: 110px;
    }

    /* Product Review (right side) */
    .product-review-shell {
        position: sticky;
        top: 88px;
        border-radius: var(--wizard-radius-xl);
        background: #020617;
        color: #e5e7eb;
        padding: 1rem .9rem 1.2rem;
        border: 1px solid rgba(148, 163, 184, .35);
        box-shadow: 0 20px 40px rgba(15, 23, 42, .45);
    }

    .product-review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .75rem;
    }

    .product-review-header h5 {
        margin: 0;
        font-size: .95rem;
    }

    .product-review-header span {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9ca3af;
    }

    .product-review-status-pill {
        font-size: .68rem;
        padding: .16rem .55rem;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, .6);
        color: #e5e7eb;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }

    .product-review-main {
        display: flex;
        gap: .75rem;
        margin-bottom: .8rem;
    }

    .product-review-main-img {
        width: 68px;
        height: 68px;
        border-radius: 12px;
        overflow: hidden;
        background: radial-gradient(circle at 0 0, #22c55e22, transparent 60%);
        border: 1px solid rgba(148, 163, 184, .5);
        flex-shrink: 0;
    }

    .product-review-main-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-review-main-info h6 {
        margin: 0;
        font-size: .95rem;
    }

    .product-review-main-info small {
        display: block;
        font-size: .75rem;
        color: #9ca3af;
    }

    .product-review-kv {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        column-gap: .75rem;
        row-gap: .35rem;
        font-size: .75rem;
        margin-bottom: .65rem;
    }

    .product-review-kv-label {
        color: #9ca3af;
    }

    .product-review-kv-value {
        color: #e5e7eb;
    }

    .product-review-desc {
        max-height: 120px;
        overflow: auto;
        font-size: .75rem;
        color: #cbd5f5;
        border-radius: 10px;
        background: rgba(15, 23, 42, .8);
        padding: .4rem .55rem;
        margin-bottom: .6rem;
    }

    .product-review-stages {
        border-top: 1px dashed rgba(148, 163, 184, .4);
        padding-top: .6rem;
        margin-top: .3rem;
    }

    .product-review-stage-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: .73rem;
        padding: .22rem 0;
    }

    .product-review-stage-row span:first-child {
        color: #9ca3af;
    }

    .product-review-stage-badge {
        border-radius: 999px;
        padding: .1rem .55rem;
        font-size: .68rem;
    }

    .product-review-stage-badge.saved {
        background: rgba(22, 163, 74, .15);
        color: #bbf7d0;
    }

    .product-review-stage-badge.skipped {
        background: rgba(234, 179, 8, .16);
        color: #fef9c3;
    }

    .product-review-stage-badge.open {
        background: rgba(148, 163, 184, .22);
        color: #e5e7eb;
    }

    .input-stats {
        font-size: .7rem;
        color: var(--wizard-muted);
    }

    .img-flag {
        width: 20px !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ced4da;
        padding: 0.45rem;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.4em 0.7em;
    }

    @media (max-width: 991.98px) {
        .product-review-shell {
            position: static;
            margin-top: 1rem;
        }
    }
</style>
<style>
    /* =========================
   Supplier price grid (enterprise)
========================= */

.price-grid{
    border: 1px solid rgba(15,23,42,.10);
    border-radius: 14px;
    background: #fff;
    overflow: auto;                 /* horizontal scroll */
    max-height: 520px;              /* optional: keeps page usable */
}

/* Force a real minimum width so columns don't collapse */
#distributor_price{
    min-width: 1350px;              /* adjust if you add/remove columns */
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

/* Header: make it readable + sticky */
#distributor_price thead th{
    position: sticky;
    top: 0;
    z-index: 5;
    background: #f8fafc !important; /* override thead-light */
    color: #111827 !important;      /* force contrast */
    font-weight: 700;
    font-size: .78rem;
    letter-spacing: .02em;
    text-transform: none;
    padding: .65rem .6rem;
    border-bottom: 1px solid rgba(15,23,42,.10);
    white-space: nowrap;            /* keeps headers readable */
}

/* Body cells: spacing + readability */
#distributor_price tbody td{
    padding: .55rem .6rem;
    vertical-align: middle;
    border-bottom: 1px solid rgba(15,23,42,.06);
    background: #fff;
}

/* Hover row */
#distributor_price tbody tr:hover td{
    background: rgba(37,99,235,.04);
}

/* Inputs inside table: ensure text is visible */
#distributor_price .form-control{
    height: 34px;
    padding: .35rem .5rem;
    font-size: .85rem;
    color: #111827 !important;
    background: #fff !important;
    border: 1px solid rgba(15,23,42,.14);
    border-radius: 10px;
}

/* Sticky first column (Lieferant) so you always see which row you're editing */
#distributor_price thead th:first-child,
#distributor_price tbody td:first-child{
    position: sticky;
    left: 0;
    z-index: 6;
    background: #fff;
    box-shadow: 1px 0 0 rgba(15,23,42,.10);
    min-width: 220px;               /* important */
}

/* Make the header cell of first column stay above everything */
#distributor_price thead th:first-child{
    background: #f8fafc !important;
    z-index: 10;
}

/* Column sizing (optional but recommended) */
#distributor_price th:nth-child(2), #distributor_price td:nth-child(2){ min-width: 160px; } /* Art.-Nr. */
#distributor_price th:nth-child(3), #distributor_price td:nth-child(3){ min-width: 130px; } /* UVP */
#distributor_price th:nth-child(4), #distributor_price td:nth-child(4){ min-width: 140px; } /* EK */
#distributor_price th:nth-child(5), #distributor_price td:nth-child(5){ min-width: 160px; } /* Rabattgruppe */
#distributor_price th:nth-child(6), #distributor_price td:nth-child(6){ min-width: 120px; } /* Rabatt € */
#distributor_price th:nth-child(7), #distributor_price td:nth-child(7){ min-width: 110px; } /* Rabatt % */
#distributor_price th:nth-child(8), #distributor_price td:nth-child(8){ min-width: 130px; } /* Datum */
#distributor_price th:nth-child(9), #distributor_price td:nth-child(9){ min-width: 160px; } /* Verfügbarkeit */
#distributor_price th:last-child,   #distributor_price td:last-child  { min-width: 90px;  } /* Aktion */

</style>
@endsection

@section('content')
<div class="app-content"> 

    <div class="content-wrapper"> 

        <div class="content-body">
            <div class="container mt-2">
                <div class="row">
                    {{-- LEFT: Wizard --}}
                    <div class="col-lg-8 col-xl-9 mb-2">
                        <div class="wizard-shell">
                            <div class="wizard-header">
                                <div class="wizard-title-block">
                                    <h2>Produkt Wizard</h2>
                                    <span>Erfassen Sie alle relevanten Stammdaten, Technik, Lieferanten und Lagerinformationen Schritt für Schritt.</span>
                                </div>
                              
                            </div>

                            <div class="wizard-progress-shell">
                                <small>
                                    <span>Fortschritt</span>
                                    <span id="wizard-progress-label">0% abgeschlossen</span>
                                </small>
                                <div class="progress">
                                    <div id="overallProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="wizard-steps-row">
                                @php
$stepes = [
  ['icon' => 'edit-3', 'label' => 'Produktinfo'],
  ['icon' => 'settings', 'label' => 'Produkteigenschaft'],
  ['icon' => 'truck', 'label' => 'Lieferant'],
  ['icon' => 'home', 'label' => 'Lager'],
  ['icon' => 'image', 'label' => 'Bilder'],
  ['icon' => 'file-text', 'label' => 'Mediadaten (Dokument-Center)'],
  ['icon' => 'check-circle', 'label' => 'Abschluss'],
];
                                @endphp

                                @foreach($stepes as $index => $step)
                                    <div class="wizard-step" data-index="{{ $index }}">
                                        <div class="circle">
                                            <i data-feather="{{ $step['icon'] }}"></i>
                                        </div>
                                        <div class="label">{{ $step['label'] }}</div>
                                        <div class="label-pill" data-stage-pill="{{ $index }}">Offen</div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="input-stats text-right mb-1">
                                <span><span id="filled-count">0</span> von <span id="total-count">0</span> Feldern ausgefüllt.</span>
                            </div>

                            <form id="wizardForm">
                              <div id="wizardValidationSummary" class="validation-summary"></div>
                                <input type="hidden" id="wizardProductId" value="{{ old('product_id') }}">

                                <div class="stage-content active" data-index="0">
                                    @include('admin.product.product.stages.product_info')
                                </div>
                                <div class="stage-content" data-index="1">
                                    @include('admin.product.product.stages.product_technical')
                                </div>
                                <div class="stage-content" data-index="2">
                                    @include('admin.product.product.stages.distributor')
                                </div>
                                <div class="stage-content" data-index="3">
                                    @include('admin.product.product.stages.warehouse')
                                </div>
                                <div class="stage-content" data-index="4">
                                    @include('admin.product.product.stages.photo')
                                </div>
                                <div class="stage-content" data-index="5">
                                    @include('admin.product.product.stages.document')
                                </div>
                                <div class="stage-content" data-index="6">
                                    @include('admin.product.product.stages.final')
                                </div>

                                <div class="wizard-footer">
                                    <div class="wizard-footer-left">
                                        <button type="button" class="btn btn-outline-secondary btn-skip-stage" id="skipStageBtn">
                                            <i class="feather icon-skip-forward"></i> Diesen Schritt später ausfüllen
                                        </button>
                                        <div class="mt-25">
                                            Aktueller Schritt: <strong id="current-step-label">Produktinfo</strong>
                                        </div>
                                    </div>
                                    <div class="wizard-footer-right">
                                        <button type="button" id="prevBtn" class="btn btn-outline-secondary mr-50" disabled>
                                            <i class="feather icon-arrow-left mr-25"></i> Zurück
                                        </button>
                                        <button type="button" id="saveNextBtn" class="btn btn-success mr-50">
                                            <i class="feather icon-save mr-25"></i> Speichern &amp; Weiter
                                        </button>
                                        <button type="button" id="nextBtn" class="btn btn-primary">
                                            Weiter <i class="feather icon-arrow-right ml-25"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- RIGHT: Product Review --}}
                    <div class="col-lg-4 col-xl-3">
                        <aside class="product-review-shell">
                            <div class="product-review-header">
                                <div>
                                    <span>Produktvorschau</span>
                                    <h5 id="review-title">Entwurf</h5>
                                </div>
                                <div class="product-review-status-pill" id="review-status-pill">
                                    <i class="feather icon-activity"></i> Entwurf
                                </div>
                            </div>

                            <div class="product-review-main">
                                <div class="product-review-main-img">
                                    <img src="{{ asset('images/icons/placeholder.svg') }}" id="summary-image" alt="Produktbild">
                                </div>
                                <div class="product-review-main-info">
                                    <h6 id="summary-product">Noch kein Produktname</h6>
                                    <small id="summary-model">–</small>
                                    <small id="summary-brand" class="mt-25 d-block text-primary">
                                        &nbsp;
                                    </small>
                                </div>
                            </div>

                            <div class="product-review-kv">
                                <div>
                                    <div class="product-review-kv-label">Artikel-Nr.</div>
                                    <div class="product-review-kv-value" id="summary-article_no">—</div>
                                </div>
                                <div>
                                    <div class="product-review-kv-label">EAN</div>
                                    <div class="product-review-kv-value" id="summary-ean">—</div>
                                </div>
                                <div>
                                    <div class="product-review-kv-label">Kategorie</div>
                                    <div class="product-review-kv-value" id="summary-category">—</div>
                                </div>
                                <div>
                                    <div class="product-review-kv-label">Farbe</div>
                                    <div class="product-review-kv-value" id="summary-color">—</div>
                                </div>
                                <div>
                                    <div class="product-review-kv-label">Wärmepumpe</div>
                                    <div class="product-review-kv-value" id="summary-heatpump_type">—</div>
                                </div>
                                <div>
                                    <div class="product-review-kv-label">MwSt</div>
                                    <div class="product-review-kv-value" id="summary-vat">19%</div>
                                </div>

                                <div>
                                  <div class="product-review-kv-label">Mengeneinheit</div>
                                  <div class="product-review-kv-value" id="summary-measure_unit">—</div>
                                </div>
                                <div>
                                  <div class="product-review-kv-label">Preiseinheit</div>
                                  <div class="product-review-kv-value" id="summary-price_unit">—</div>
                                </div>
                                <div>
                                  <div class="product-review-kv-label">Packungseinheit</div>
                                  <div class="product-review-kv-value" id="summary-package_unit">—</div>
                                </div>

                            </div>

                            <div class="product-review-desc" id="summary-description">
                                Kurzbeschreibung erscheint hier, sobald sie gespeichert wurde.
                            </div>

                            <div class="product-review-stages">
                                <div class="product-review-stage-row">
                                    <span>1. Produktinfo</span>
                                    <span class="product-review-stage-badge open" data-review-stage="0">Offen</span>
                                </div>
                                <div class="product-review-stage-row">
                                    <span>2. Produkteigenschaft</span>
                                    <span class="product-review-stage-badge open" data-review-stage="1">Offen</span>
                                </div>
                                <div class="product-review-stage-row">
                                    <span>3. Lieferant & Preise</span>
                                    <span class="product-review-stage-badge open" data-review-stage="2">Offen</span>
                                </div>
                                <div class="product-review-stage-row">
                                    <span>4. Lager</span>
                                    <span class="product-review-stage-badge open" data-review-stage="3">Offen</span>
                                </div>
                                <div class="product-review-stage-row">
                                    <span>5. Bilder</span>
                                    <span class="product-review-stage-badge open" data-review-stage="4">Offen</span>
                                </div>
                                <div class="product-review-stage-row">
                                    <span>6. Mediadaten (Dokument-Center)</span>
                                    <span class="product-review-stage-badge open" data-review-stage="5">Offen</span>
                                </div>
                                <div class="product-review-stage-row">
                                    <span>7. Abschluss</span>
                                    <span class="product-review-stage-badge open" data-review-stage="6">Offen</span>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')

{{-- Core vendor scripts --}}
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>

{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

{{-- Dropzone --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<script>
Dropzone.autoDiscover = false;
</script>

<script>
/**
 * Product Wizard (rewritten)
 * - No duplicate bindings (delegated events)
 * - Safe toastr / Swal fallbacks (won't break the whole script)
 * - Robust brand/distributor modal saving (works even if modal is injected later)
 * - Stable stage saving (stage 2 manual append kept)
 * - Defensive checks + console logs for debugging
 */
(function ($, window, document) {
  'use strict';

  // ==========================================================
  // GLOBALS / SAFE HELPERS
  // ==========================================================
  const CSRF_TOKEN     = $('meta[name="csrf-token"]').attr('content') || '';
  const BRAND_FALLBACK = "{{ asset('logo/logo.png') }}";
  const NO_IMAGE       = "{{ asset('images/icons/placeholder.svg') }}";
  const TODAY_DATE     = "{{ \Carbon\Carbon::now()->format('Y-m-d') }}";

  let quillInstance = null;

  const state = {
    brandRowIndex: 0,
    descriptionRowIndex: 0,
    distributorRowKey: Date.now(),
    currentStageIndex: 0,
    dropzoneImageInitialized: false,
    dropzoneDocInitialized: false,
    inventoryInitialized: false,
    finalStageInitialized: false,
  };

  const SEL = {
    stages: '.stage-content',
    steps:  '.wizard-step',
    form:   '#wizardForm',
    pid:    '#wizardProductId',
  };

  // --- Toastr / Swal safe wrappers (so missing libs won't break everything)
  const toast = {
    success: (m) => window.toastr ? toastr.success(m) : console.log('SUCCESS:', m),
    error:   (m) => window.toastr ? toastr.error(m)   : console.error('ERROR:', m),
    warning: (m) => window.toastr ? toastr.warning(m) : console.warn('WARN:', m),
    info:    (m) => window.toastr ? toastr.info(m)    : console.info('INFO:', m),
  };

  function swalFire(optsOrTitle, text, icon) {
    if (window.Swal && Swal.fire) {
      if (typeof optsOrTitle === 'object') return Swal.fire(optsOrTitle);
      return Swal.fire(optsOrTitle, text, icon);
    }
    // fallback
    alert((text || optsOrTitle || 'OK') + (icon ? ` (${icon})` : ''));
    return Promise.resolve({ isConfirmed: true });
  }
  function getFieldLabel($field) {
    const $group = $field.closest('.form-group, .col-md-6, .col-md-4, td');
    const $label = $group.find('label').first();
    return ($label.text() || $field.attr('name') || 'Dieses Feld')
      .replace(/\*/g, '')
      .trim();
  }

  function clearFieldError($field) {
    $field.removeClass('field-invalid');
    $field.removeAttr('title');

    if ($field.hasClass('select2-hidden-accessible')) {
      $field.next('.select2').find('.select2-selection').removeClass('field-invalid');
    }

    const $wrap = $field.closest('.required-wrap, .locked-field');
    $wrap.removeClass('field-invalid');

    const $group = $field.closest('.form-group, td, .locked-field');
    $group.find('> .validation-error-text').remove();

    if ($group.is('td')) {
      $group.find('.validation-error-text').remove();
    }
  }

  function showFieldError($field, message) {
    clearFieldError($field);

    $field.addClass('field-invalid');
    $field.attr('title', message);

    if ($field.hasClass('select2-hidden-accessible')) {
      $field.next('.select2').find('.select2-selection').addClass('field-invalid');
    }

    const $wrap = $field.closest('.required-wrap, .locked-field');
    $wrap.addClass('field-invalid');

    const $group = $field.closest('.form-group, td, .locked-field');
    $('<div class="validation-error-text"></div>').text(message).appendTo($group);
  }

  function isEmptyField($field) {
    if (!$field.length) return true;

    if ($field.is(':checkbox') || $field.is(':radio')) {
      const name = $field.attr('name');
      if (!name) return !$field.is(':checked');
      return $(SEL.form).find('[name="' + name + '"]:checked').length === 0;
    }

    const val = $field.val();

    if (Array.isArray(val)) return val.length === 0;
    return val == null || String(val).trim() === '';
  }

  function getVisibleStageFields(stageIndex) {
    return $(SEL.stages).eq(stageIndex).find('.required-field:enabled, .required-field');
  }

  function validateStage(stageIndex, options = {}) {
    const settings = {
      focusFirst: options.focusFirst !== false,
      showSummary: options.showSummary !== false
    };

    const $summary = $('#wizardValidationSummary');
    const $stage = $(SEL.stages).eq(stageIndex);
    const $fields = getVisibleStageFields(stageIndex);

    let firstInvalid = null;
    let invalidCount = 0;

    $summary.removeClass('show').html('');
    $(SEL.steps).eq(stageIndex).removeClass('has-errors');

    $fields.each(function () {
      const $field = $(this);

      clearFieldError($field);

      // ignore hidden optional sections
      if (!$field.closest(':visible').length) return;

      // locked category can still be valid because it already has a default value
      if (!isEmptyField($field)) return;

      invalidCount++;

      const customMessage = $field.data('required-message');
      const label = getFieldLabel($field);
      const message = customMessage || (label + ' ist erforderlich.');

      showFieldError($field, message);

      if (!firstInvalid) firstInvalid = $field;
    });

    if (invalidCount > 0) {
      $(SEL.steps).eq(stageIndex).addClass('has-errors');

      if (settings.showSummary) {
        $summary
          .addClass('show')
          .html('<strong>Es fehlen Pflichtfelder.</strong><br>Bitte prüfen Sie die rot markierten Felder im aktuellen Schritt.');
      }

      if (settings.focusFirst && firstInvalid) {
        $('html, body').animate({
          scrollTop: Math.max(firstInvalid.offset().top - 140, 0)
        }, 250);

        if (firstInvalid.hasClass('select2-hidden-accessible')) {
          firstInvalid.select2('open');
        } else {
          firstInvalid.trigger('focus');
        }
      }

      return false;
    }

    return true;
  }

  function bindLiveValidationCleanup() {
    $(document).on('input change', '.required-field', function () {
      if (!isEmptyField($(this))) {
        clearFieldError($(this));

        const stageIndex = state.currentStageIndex;
        const stillInvalid = $(SEL.stages).eq(stageIndex).find('.field-invalid').length > 0;

        if (!stillInvalid) {
          $(SEL.steps).eq(stageIndex).removeClass('has-errors');
          $('#wizardValidationSummary').removeClass('show').html('');
        }
      }
    });
  }
  // Global Ajax header
  if (CSRF_TOKEN) {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });
  } else {
    console.warn('[wizard] CSRF token missing. Ensure meta csrf-token exists.');
  }

  // ==========================================================
  // FLASH (TOASTR) - SAFE
  // ==========================================================
  function initFlashToastr() {
    try {
      @if(Session::has('update_msg'))
        toast.success("{{ session('update_msg') }}");
      @endif
      @if(Session::has('save_msg'))
        toast.success("{{ session('save_msg') }}");
      @endif
      @if(Session::has('delete_msg'))
        toast.error("{{ session('delete_msg') }}");
      @endif
    } catch (e) {
      console.error('[wizard] flash toastr failed:', e);
    }
  }

  // ==========================================================
  // QUILL
  // ==========================================================
  function initQuillEditor() {
    const $editor = $('#editor');
    if (!$editor.length || typeof Quill === 'undefined') return;

    const toolbar = [
      ['bold','italic','underline','strike'],
      ['blockquote','code-block'],
      [{header:1},{header:2}],
      [{list:'ordered'},{list:'bullet'}],
      [{script:'sub'},{script:'super'}],
      [{indent:'-1'},{indent:'+1'}],
      [{direction:'rtl'}],
      [{size:['small',false,'large','huge']}],
      [{header:[1,2,3,4,5,6,false]}],
      [{color:[]},{background:[]}],
      [{font:[]}],
      [{align:[]}],
      ['link','image','video','formula'],
      ['clean']
    ];

    quillInstance = new Quill('#editor', { modules: { toolbar }, theme: 'snow' });

    quillInstance.on('text-change', function (delta, oldDelta, source) {
      if (source !== 'user') return;
      const html = $editor.find('.ql-editor').html() || '';
      const $hidden = $('#editor_text');
      if ($hidden.length) $hidden.val(html);
    });
  }

  // ==========================================================
  // SELECT2
  // ==========================================================
  function initBaseSelect2() {
    if (!$.fn.select2) return;

    $('.select2').not('#brand').not('#distributor').select2({
      width: '100%',
      allowClear: true,
      placeholder: function () { return $(this).data('placeholder') || 'Bitte wählen'; }
    });

    $('#color').select2({ width: '100%', placeholder: 'Farbe auswählen', allowClear: true });
    $('.percent').select2({ width: '100%', placeholder: '% Rabatt', allowClear: true, tags: true });
    $('#measure_unit').select2({ width: '100%', placeholder: 'Mengeneinheit auswählen', allowClear: true });

    $('#article_group').select2();
    $('#discount_group').select2();
    $('#sub_article').select2();
    $('#category').select2();
    $('#roof_type').select2();
  }

  // ==========================================================
  // BRAND ROWS
  // ==========================================================
  function addBrandRow() {
    state.brandRowIndex++;
    $('#add_department').append(`
      <tr>
        <td><input type="text" class="form-control required" name="brand[${state.brandRowIndex}][brand_department]" placeholder="Abteilung"></td>
        <td><input type="text" class="form-control required" name="brand[${state.brandRowIndex}][name]" placeholder="Gesprächspartner"></td>
        <td><input type="text" class="form-control required" name="brand[${state.brandRowIndex}][position]" placeholder="Position"></td>
        <td><input type="text" class="form-control required" name="brand[${state.brandRowIndex}][email]" placeholder="E-Mail"></td>
        <td><input type="text" class="form-control required" name="brand[${state.brandRowIndex}][phone]" placeholder="Handynummer"></td>
        <td><input type="text" class="form-control required" name="brand[${state.brandRowIndex}][home]" placeholder="Festnetznummer"></td>
        <td><input type="text" class="form-control required" name="brand[${state.brandRowIndex}][office]" placeholder="Büro-Telefonnummer"></td>
        <td>
          <button type="button" class="btn btn-icon rounded-circle btn-outline-primary brand-remove-row">
            <i class="feather icon-minus-square"></i>
          </button>
        </td>
      </tr>
    `);
  }

  // ==========================================================
  // BRAND DROPDOWN + AJAX SAVE
  // ==========================================================
  function formatBrandOption(option) {
    if (!option.id) return option.text;
    const imgSrc = $(option.element).data('image') || BRAND_FALLBACK;
    return $(
      '<span class="d-flex align-items-center">' +
        '<img src="' + imgSrc + '" style="width:20px;height:20px;border-radius:999px;margin-right:6px;object-fit:cover;" />' +
        '<span>' + option.text + '</span>' +
      '</span>'
    );
  }

  function loadBrands(forceSelectedId = null) {
    const $brand = $(SEL.form).find('select#brand').first();
    if (!$brand.length) return;

    const previousValue = forceSelectedId !== null ? String(forceSelectedId) : String($brand.val() || '');

    $brand.empty().append(
      '<option value="" disabled selected data-image="' + BRAND_FALLBACK + '">Bitte Hersteller wählen</option>'
    );

    $.get("{{ route('product.create.get.brand') }}")
      .done(function (response) {
        const seen = new Set();

        (response || []).forEach(function (b) {
          if (!b || !b.id) return;
          if (b.status !== 'Published') return;

          const id = String(b.id);
          if (seen.has(id)) return;
          seen.add(id);

          const img = b.image ? '/images/brand/' + b.image : BRAND_FALLBACK;
          $brand.append('<option value="' + id + '" data-image="' + img + '">' + b.name + '</option>');
        });

        if ($brand.hasClass('select2-hidden-accessible')) $brand.select2('destroy');

        $brand.select2({
          width: '100%',
          allowClear: true,
          placeholder: 'Hersteller wählen',
          templateResult: formatBrandOption,
          templateSelection: formatBrandOption
        });

        // restore selection
        const desired = previousValue || (forceSelectedId ? String(forceSelectedId) : '');
        if (desired && $brand.find('option[value="' + desired + '"]').length) {
          $brand.val(desired).trigger('change.select2');
        } else {
          $brand.val('').trigger('change.select2');
        }

        // preview update
        $brand.off('change.brand').on('change.brand', updatePreviewFromForm);
      })
      .fail(function (xhr) {
        console.error('[brand] load failed', xhr);
        toast.error('Marken konnten nicht geladen werden.');
      });
  }

  function saveBrandFromModal($trigger = null) {
    // Find the form in the most reliable order:
    // 1) If triggered by a button: closest form
    // 2) If in modal: first form inside the modal
    // 3) Fallback by ids (optional)
    let $form = $();

    if ($trigger && $trigger.length) {
      $form = $trigger.closest('form');
      if (!$form.length) $form = $trigger.closest('.modal').find('form').first();
    }

    if (!$form.length && $('#brandForm').length) $form = $('#brandForm');
    if (!$form.length && $('#new_brand').length) $form = $('#new_brand').find('form').first();

    if (!$form.length) {
      console.error('[brand] brand form not found. Add id="brandForm" or ensure save button is inside the modal with a form.');
      toast.error('Brand-Formular nicht gefunden. Prüfe Modal/Form-Struktur.');
      return;
    }

    const formData = new FormData($form[0]);
    if (CSRF_TOKEN) formData.append('_token', CSRF_TOKEN);


    let modalInvalid = false;

    $form.find('.required-field').each(function () {
      const $field = $(this);
      clearFieldError($field);

      if (isEmptyField($field)) {
        modalInvalid = true;
        const msg = $field.data('required-message') || (getFieldLabel($field) + ' ist erforderlich.');
        showFieldError($field, msg);
      }
    });

    if (modalInvalid) {
      toast.warning('Bitte füllen Sie die Pflichtfelder im Marken-Modal aus.');
      const $first = $form.find('.field-invalid').first();
      if ($first.length) $first.trigger('focus');
      return;
    }

    $('#name-error,#initial-error,#purpose-error,#image-error').text('');

    $.ajax({
      url: "{{ route('product.store.brand') }}",
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false
    })
    .done(function (res) {
      toast.success(res.save_msg || 'Marke gespeichert');
      $form[0].reset();

      // Close modal if exists
     const $modal = $form.closest('.modal');
    closeAnyModal($modal.length ? $modal : null);


      if (res?.brand?.id) loadBrands(res.brand.id);
      else loadBrands();
    })
    .fail(function (xhr) {
      console.error('[brand] save failed', xhr.responseText || xhr);
      toast.error('Fehler beim Speichern der Marke');

      const errors = xhr.responseJSON?.errors || {};
      if (errors.name)    $('#name-error').text(errors.name[0]);
      if (errors.initial) $('#initial-error').text(errors.initial[0]);
      if (errors.purpose) $('#purpose-error').text(errors.purpose[0]);
      if (errors.image)   $('#image-error').text(errors.image[0]);
    });
  }


  // ==========================================================
  // DISTRIBUTORS DROPDOWN + AJAX SAVE
  // ==========================================================
  function distributorOptionTemplate(option) {
    if (!option.id) return option.text;
    const image = $(option.element).data('image') || '/images/distributor/default.png';
    return $(
      '<span class="d-flex align-items-center">' +
        '<img src="' + image + '" class="img-flag" style="width:18px;height:18px;border-radius:999px;object-fit:cover;margin-right:6px;" />' +
        '<span>' + option.text + '</span>' +
      '</span>'
    );
  }

  function fetchDistributors(selectIdsAfter = null) {
    const $distributor = $('#distributor');
    if (!$distributor.length) return;

    const prev = selectIdsAfter
      ? (Array.isArray(selectIdsAfter) ? selectIdsAfter.map(String) : [String(selectIdsAfter)])
      : (($distributor.val() || []).map(String));

    $.ajax({ url: "{{ route('product.get.distributor') }}", type: 'GET' })
      .done(function (res) {
        $distributor.empty();

        const seen = new Set();
        (res || []).forEach(function (d) {
          if (!d || !d.id) return;
          const id = String(d.id);
          if (seen.has(id)) return;
          seen.add(id);

          const img = d.image ? '/images/distributor/' + d.image : '/images/distributor/default.png';
          $distributor.append('<option value="' + id + '" data-image="' + img + '">' + d.name + '</option>');
        });

        if (!seen.size) $distributor.append('<option disabled>Keine Lieferanten gefunden</option>');

        if ($distributor.hasClass('select2-hidden-accessible')) $distributor.select2('destroy');

        $distributor.select2({
          width: '100%',
          allowClear: true,
          placeholder: 'Lieferanten auswählen',
          templateResult: distributorOptionTemplate,
          templateSelection: distributorOptionTemplate
        });

        if (prev.length) $distributor.val(prev).trigger('change.select2');
      })
      .fail(function (xhr) {
        console.error('[distributor] load failed', xhr);
        toast.error('Lieferanten konnten nicht geladen werden.');
      });
  }

  function saveDistributorFromModal() {
    const $form = $('#distributorForm');
    if (!$form.length) {
      console.error('[distributor] #distributorForm not found');
      toast.error('Lieferanten-Formular nicht gefunden (ID prüfen).');
      return;
    }

    const formData = new FormData($form[0]);
    if (CSRF_TOKEN) formData.append('_token', CSRF_TOKEN);

    $('#name-error,#address-error,#image-error').text('');

    $.ajax({
      url: "{{ route('product.store.distributor') }}",
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false
    })
    .done(function (res) {
      toast.success(res.save_msg || 'Lieferant gespeichert');
      $form[0].reset();
      if ($('#distributors').length && $.fn.modal) $('#distributors').modal('hide');

      if (res?.distributor?.id) fetchDistributors([String(res.distributor.id)]);
      else fetchDistributors();
    })
    .fail(function (xhr) {
      console.error('[distributor] save failed', xhr.responseText || xhr);
      const errs = xhr.responseJSON?.errors || {};
      $('#name-error').text(errs.name ? errs.name[0] : '');
      $('#address-error').text(errs.address ? errs.address[0] : '');
      $('#image-error').text(errs.image ? errs.image[0] : '');
      toast.error('Fehler beim Speichern des Lieferanten');
    });
  }

  // ==========================================================
  // DISTRIBUTOR PRICE TABLE + CALC
  // ==========================================================
  const discountGroupOptionsHtml = `
    <option value="">-- Gruppe --</option>
    @foreach($discount_group as $dg)
      <option value="{{ $dg->id }}" data-percent="{{ $dg->discount }}">
        {{ $dg->discount_group }} - {{ $dg->discount }}%
      </option>
    @endforeach
  `;

  function normalizeNumberInput(raw) {
    const s = String(raw ?? '').trim().replace(/[^\d,.\-]/g, '');
    if (s.includes(',') && s.includes('.')) return s.replace(/\./g, '').replace(',', '.');
    return s.replace(',', '.');
  }
  function toNum(val) {
    const n = parseFloat(normalizeNumberInput(val));
    return Number.isFinite(n) ? n : null;
  }
  function fmt2(n) {
    if (!Number.isFinite(n)) return '';
    return n.toFixed(2);
  }
  function setIfNotEditing($input, val, editingClass) {
    if (!$input.length) return;
    if ($input.hasClass(editingClass)) return;
    $input.val(val);
  }

 function addDistributorPriceRow(distributorId) {
    state.distributorRowKey++;

    const selectedIds = ($('#distributor').val() || []).map(String);
    const id = distributorId ? String(distributorId) : (selectedIds[0] || '');

    if (!id) { toast.warning('Bitte zuerst einen Lieferanten auswählen.'); return; }

    const distributorName = $('#distributor option[value="' + id + '"]').text() || 'Lieferant';

    $('#distributor_price tbody').append(`
      <tr data-rowkey="${state.distributorRowKey}">
        <td>
          <select name="price[${state.distributorRowKey}][distributor_id]" class="form-control distributor_id">
            <option value="${id}" selected>${distributorName}</option>
          </select>
        </td>

        <!-- SIMPLE -->
        <td>
          <input type="text" name="price[${state.distributorRowKey}][article_no]" class="form-control" placeholder="Artikel-Nr.">
        </td>

        <td>
          <input type="text" name="price[${state.distributorRowKey}][purchase_price]" class="form-control purchase_price" placeholder="EK-Einzelpreis">
        </td>

        <td>
          <select name="price[${state.distributorRowKey}][availability]" class="form-control">
            <option value="Sofort Lieferbar">Sofort Lieferbar</option>
            <option value="Auf Anfrage">Auf Anfrage</option>
            <option value="Nicht verfügbar">Nicht verfügbar</option>
          </select>
        </td>

        <!-- ADVANCED -->
        <td class="adv-col">
          <input type="text" name="price[${state.distributorRowKey}][price]" class="form-control price_field" placeholder="UVP / Listenpreis">
        </td>

        <td class="adv-col">
          <select name="price[${state.distributorRowKey}][discount_group_id]" class="form-control discount_group_select">
            ${discountGroupOptionsHtml}
          </select>
        </td>

        <td class="adv-col">
          <input type="text" name="price[${state.distributorRowKey}][discount_price]" class="form-control discount_price" placeholder="Rabatt €">
        </td>

        <td class="adv-col">
          <input type="text" name="price[${state.distributorRowKey}][discount_percent]" class="form-control discount_percent" placeholder="Rabatt %">
        </td>

        <td class="adv-col">
          <input type="date" name="price[${state.distributorRowKey}][price_date]" class="form-control" value="${TODAY_DATE}">
        </td>

        <td class="adv-col">
          <input type="text" name="price[${state.distributorRowKey}][note]" class="form-control" placeholder="Notiz">
        </td>

        <td class="text-right">
          <button type="button" class="btn btn-icon btn-outline-danger remove_price_row">
            <i class="feather icon-minus-square"></i>
          </button>
        </td>
      </tr>
    `);
  }

  function calculateDistributorRow($row, editingClass, sourceKey = null) {
    const $price = $row.find('.price_field');
    const $buy   = $row.find('.purchase_price');
    const $dE    = $row.find('.discount_price');
    const $dP    = $row.find('.discount_percent');

  
    const buy   = toNum($buy.val());
    const discE = toNum($dE.val());
    const discP = toNum($dP.val());
    const price = toNum($price.val());
    if (price == null || price <= 0) return;

    const src = sourceKey || $row.data('lastEdited') || null;

    let outPrice = price;
    let outBuy   = buy;
    let outDiscE = discE;
    let outDiscP = discP;

    if (price != null && price > 0) {
      if (src === 'discP' && discP != null) {
        outDiscE = (price * discP) / 100;
        outBuy   = price - outDiscE;
      } else if (src === 'discE' && discE != null) {
        outDiscP = (discE / price) * 100;
        outBuy   = price - discE;
      } else if (src === 'buy' && buy != null) {
        outDiscE = price - buy;
        outDiscP = (outDiscE / price) * 100;
      } else {
        if (buy != null) {
          outDiscE = price - buy;
          outDiscP = (outDiscE / price) * 100;
        } else if (discE != null) {
          outDiscP = (discE / price) * 100;
          outBuy   = price - discE;
        } else if (discP != null) {
          outDiscE = (price * discP) / 100;
          outBuy   = price - outDiscE;
        }
      }
    } else {
      if (buy != null && discE != null) {
        outPrice = buy + discE;
        if (outPrice > 0) outDiscP = (discE / outPrice) * 100;
      } else if (buy != null && discP != null) {
        const denom = 1 - (discP / 100);
        if (denom !== 0) {
          outPrice = buy / denom;
          outDiscE = outPrice - buy;
        }
      }
    }

    if (outPrice != null) setIfNotEditing($price, fmt2(outPrice), editingClass);
    if (outBuy   != null) setIfNotEditing($buy,   fmt2(outBuy),   editingClass);
    if (outDiscE != null) setIfNotEditing($dE,    fmt2(outDiscE), editingClass);
    if (outDiscP != null) setIfNotEditing($dP,    fmt2(outDiscP), editingClass);
  }

  // ==========================================================
  // TOP PRICING CALCS
  // ==========================================================
  function calculatePurchaseFromGroup() {
    const retailPrice = toNum($('#retail_price').val()) || 0;
    const discount    = toNum($('#discount_group').val()) || 0;
    const $purchase   = $('#purchase_price');
    if (!$purchase.length) return;

    const result = retailPrice - (retailPrice * discount / 100);
    $purchase.val(Number.isFinite(result) ? Math.round(result) : '');
  }

  function calculatePurchaseFromEuro() {
    const retailPrice = toNum($('#retail_price').val()) || 0;
    const discountE   = toNum($('#r_discount_e').val()) || 0;
    const $purchase   = $('#purchase_price');
    if (!$purchase.length) return;

    $purchase.val(Number.isFinite(retailPrice - discountE) ? (retailPrice - discountE) : '');
  }

  function calculateSalePrice() {
    const $salePrice = $('#sale_price');
    if (!$salePrice.length) return;

    const purchase = toNum($('#purchase_price').val()) || 0;
    const payMode  = $('#payment_method_type').val();

    const advP = toNum($('#advance_price_percent').val()) || 0;
    const advE = toNum($('#advance_price_euro').val()) || 0;

    const plusMode = $('#plus_percent').val();
    const plusP    = toNum($('#plus_price_percent').val()) || 0;
    const plusE    = toNum($('#plus_price_euro').val()) || 0;

    let advance = 0;
    if (payMode === 'Percent') advance = purchase * advP / 100;
    if (payMode === 'Euro')    advance = advE;

    let plus = 0;
    if (plusMode === 'Percent') plus = purchase * plusP / 100;
    if (plusMode === 'Euro')    plus = plusE;

    const total = (purchase - advance) + plus;
    $salePrice.val(Number.isFinite(total) ? total : '');
  }

  function updateTaxResult() {
    const sale   = toNum($('#sale_price').val()) || 0;
    const tax    = toNum($('#tax').val()) || 0;
    const result = sale * tax / 100;
    const $target = $('#tax_result');
    if (!$target.length) return;

    if (!sale) $target.text('Der Preis ist nicht definiert');
    else $target.text((Number.isFinite(result) ? result.toFixed(2) : '0.00') + ' Euro');
  }

  function calculateTotalWithTaxAndDiscount() {
    const sale        = toNum($('#sale_price').val()) || 0;
    const tax         = toNum($('#tax').val()) || 0;
    const type        = $('#discount_type').val();
    const discP       = toNum($('#discount_percent').val()) || 0;
    const discE       = toNum($('#discount_euro').val()) || 0;
    const $totalField = $('#total');
    if (!$totalField.length) return;

    let disc = 0;
    if (type === 'Percent') disc = sale * discP / 100;
    if (type === 'Euro')    disc = discE;

    const taxVal = sale * tax / 100;
    const total  = sale + taxVal - disc;

    $totalField.val(Number.isFinite(total) ? total : '');
  }

  // ==========================================================
  // ROOF / SUB-ARTICLE / HEATPUMP
  // ==========================================================
  function toggleRoofTypeSection() {
    const val  = $('#category').val();
    const show = (val === 'Dachziegel');
    $('#roof_type_section, #roofTypeGroup')[show ? 'show' : 'hide']();
  }

  function loadSubArticles(articleGroupId) {
    const $sub = $('#sub_article');
    if (!articleGroupId) {
      $sub.empty().append('<option selected disabled>Bitte Sub-Artikel auswählen</option>');
      return;
    }

    $.ajax({
      url: '{{ route("product.get.sub.article") }}',
      type: 'GET',
      data: { article: articleGroupId }
    })
    .done(function (res) {
      $sub.empty().append('<option selected disabled>Bitte Sub-Artikel auswählen</option>');
      (res || []).forEach(function (item) {
        $sub.append('<option value="' + item.id + '">' + item.sub_article + '</option>');
      });
    })
    .fail(function (xhr) {
      console.log('[sub-article] error', xhr);
    });
  }

  function normalizeText(txt) {
    return (txt || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
  }

  function isHeatpumpGroupName(name) {
    const n = normalizeText(name);
    return /\b(warmepumpe|waermepumpe|wärmepumpe|heat\s*pump|wp)\b/.test(n);
  }

  function getArticleGroupLabel() {
    const $ag = $('#article_group');
    if ($ag.data('select2')) {
      const data = $ag.select2('data');
      if (data && data.length && data[0].text) return data[0].text;
    }
    const el = $ag.get(0);
    if (!el) return '';
    const opt = el.options[el.selectedIndex];
    return opt ? opt.text : '';
  }

  function toggleHeatpumpFields() {
    const label  = getArticleGroupLabel();
    const show   = isHeatpumpGroupName(label);
    const $block = $('#heatpumpFields');
    if (!$block.length) return;

    const ids = ['heatpump_type', 'construction_type', 'refrigerant', 'phase_count', 'scop'];
    $block[show ? 'show' : 'hide']();

    ids.forEach(function (id) {
      const el = document.getElementById(id);
      if (!el) return;
      if (show) {
        el.setAttribute('required', 'required');
      } else {
        el.removeAttribute('required');
        if (el.tagName === 'SELECT') $(el).val(null).trigger('change');
        else el.value = '';
      }
    });
  }

  // ==========================================================
  // PREVIEW
  // ==========================================================
  function setPreviewText(selector, value, fallback = '—') {
    $(selector).text((value && value !== '') ? value : fallback);
  }

  function formVal(selList, fallback = '') {
    for (let i = 0; i < selList.length; i++) {
      const $el = $(selList[i]);
      if ($el.length && $el.val()) return $el.val().toString().trim();
    }
    return fallback;
  }

  function updatePreviewFromForm() {
    const name      = formVal(['#product', '#product_name', '[name="product"]'], '');
    const articleNo = formVal(['#article_no', '[name="article_no"]'], '');
    const ean       = formVal(['#ean', '[name="ean"]'], '');
    const category  = formVal(['#category', '[name="category"]'], '');
    const color     = formVal(['#color', '[name="color"]'], '');
    const heatpump  = formVal(['#heatpump_type', '[name="heatpump_type"]'], '');
    const vat       = formVal(['#vat', '[name="vat"]'], '19');
    const desc      = formVal(['#short_description', '[name="short_description"]'], '');

    const $brand = $(SEL.form).find('select#brand').first();
    const brandText = $brand.length ? ($brand.find('option:selected').text() || '') : '';

    const measureUnit = $('#measure_unit option:selected').text() || '';
    const priceUnit   = formVal(['#price_unit', '[name="price_unit"]'], '');
    const packageUnit = formVal(['#package_unit', '[name="package_unit"]'], '');

    setPreviewText('#summary-measure_unit', measureUnit && measureUnit !== 'Bitte wählen' ? measureUnit : '—');
    setPreviewText('#summary-price_unit',   priceUnit);
    setPreviewText('#summary-package_unit', packageUnit);


    setPreviewText('#review-title',          name || 'Entwurf', 'Entwurf');
    setPreviewText('#summary-product',       name || 'Noch kein Produktname', 'Noch kein Produktname');
    setPreviewText('#summary-article_no',    articleNo);
    setPreviewText('#summary-ean',           ean);
    setPreviewText('#summary-category',      category);
    setPreviewText('#summary-color',         color);
    setPreviewText('#summary-heatpump_type', heatpump);
    setPreviewText('#summary-vat',           vat ? vat + '%' : '19%');
    setPreviewText('#summary-description',   desc || 'Kurzbeschreibung erscheint hier, sobald sie gespeichert wurde.');
    setPreviewText('#summary-brand',         brandText, '');
  }

  function loadProductPreview(productId) {
    if (!productId) { updatePreviewFromForm(); return; }

    $.get('/ajax/product/info/' + productId)
      .done(function (res) {
        const p         = res.product || {};
        const name      = p.product || p.name || 'Noch kein Produktname';
        const articleNo = p.article_no || '';
        const ean       = p.ean || '';
        const category  = p.category || '';
        const color     = p.color || '';
        const model     = p.model || '';
        const heatpump  = p.heatpump_type || '';
        const vat       = (p.vat !== null && p.vat !== undefined) ? (p.vat + '%') : '19%';
        const desc      = p.short_description || '';
        const brandName = (res.brand && res.brand.name) || p.brand_name || p.brand || '';

        setPreviewText('#review-title',           name, 'Entwurf');
        setPreviewText('#summary-product',        name, 'Noch kein Produktname');
        setPreviewText('#summary-article_no',     articleNo);
        setPreviewText('#summary-ean',            ean);
        setPreviewText('#summary-category',       category);
        setPreviewText('#summary-color',          color);
        setPreviewText('#summary-model',          model);
        setPreviewText('#summary-heatpump_type',  heatpump);
        setPreviewText('#summary-vat',            vat);
        setPreviewText('#summary-description',    desc || 'Kurzbeschreibung erscheint hier, sobald sie gespeichert wurde.');
        setPreviewText('#summary-brand',          brandName, '');

        $('#summary-image').attr('src', res.image ? ('/images/products/' + res.image) : NO_IMAGE);
      })
      .fail(function () {
        toast.error('Produktvorschau konnte nicht geladen werden.');
      });
  }

  // ==========================================================
  // INPUT STATS
  // ==========================================================
  function updateInputStats() {
    const $inputs = $(SEL.form).find('input,select,textarea');
    const total = $inputs.length;
    const filled = $inputs.filter(function () {
      const v = $(this).val();
      return v != null && v.toString().trim() !== '';
    }).length;

    $('#total-count').text(total);
    $('#filled-count').text(filled);
  }

  // ==========================================================
  // WIZARD NAVIGATION + PROGRESS
  // ==========================================================
  function showStage(index) {
    const $stages = $(SEL.stages);
    const $steps  = $(SEL.steps);

    state.currentStageIndex = index;

    $stages.removeClass('active').eq(index).addClass('active');
    $steps.removeClass('active').eq(index).addClass('active');

    $('#prevBtn').prop('disabled', index === 0);
    $('#nextBtn').text(index === $stages.length - 1 ? 'Fertigstellen' : 'Weiter');

    const label = $steps.eq(index).find('.label').text();
    if (label) $('#current-step-label').text(label);

    updateInputStats();
  }

  function updateOverallProgress() {
    const $steps = $(SEL.steps);
    const total  = $steps.length;
    let done = 0;

    $steps.each(function () {
      if ($(this).hasClass('saved') || $(this).hasClass('skipped')) done++;
    });

    const pct = total ? Math.round(done / total * 100) : 0;
    $('#overallProgress').css('width', pct + '%');
    $('#wizard-progress-label').text(pct + '% abgeschlossen');
  }

  function markStageState(index, stageState) {
    const $step   = $(SEL.steps).eq(index);
    const $pill   = $('[data-stage-pill="' + index + '"]');
    const $review = $('[data-review-stage="' + index + '"]');

    $step.removeClass('saved skipped');
    $review.removeClass('saved skipped open');

    if (stageState === 'saved') {
      $step.addClass('saved');
      $pill.text('Gespeichert');
      $review.addClass('saved').text('Gespeichert');
    } else if (stageState === 'skipped') {
      $step.addClass('skipped');
      $pill.text('Übersprungen');
      $review.addClass('skipped').text('Später');
    } else {
      $pill.text('Offen');
      $review.addClass('open').text('Offen');
    }

    updateOverallProgress();
  }

  // ==========================================================
  // SAVE STAGE (keeps your stage 2 "hard fix")
  // ==========================================================
  function saveCurrentStage(callback) {
    const $form = $(SEL.form);
    if (!$form.length) { if (typeof callback === 'function') callback(false); return; }
    if (!validateStage(state.currentStageIndex)) {
      toast.warning('Bitte füllen Sie die markierten Pflichtfelder aus.');
      if (typeof callback === 'function') callback(false);
      return;
    }
    const productId = $(SEL.pid).val();
    const formData  = new FormData($form[0]);

    if (CSRF_TOKEN) formData.append('_token', CSRF_TOKEN);
    formData.append('stage', state.currentStageIndex);
    if (productId) formData.append('product_id', productId);

    // Stage 2 manual append (distributors + price rows)
    if (String(state.currentStageIndex) === '2') {
      const dist = $('#distributor').val() || [];
      dist.forEach((id, i) => formData.append(`distributor[${i}]`, id));

      $('#distributor_price tbody tr').each(function (rowIndex) {
        const $tr = $(this);

        const distributorId =
          $tr.find('select.distributor_id').val() ||
          $tr.find('select[name*="[distributor_id]"]').val();

        if (!distributorId) return;

        const key = $tr.data('rowkey') || rowIndex;
        $tr.attr('data-rowkey', key);

        const getVal = (sel) => {
          const v = $tr.find(sel).val();
          return (v === undefined || v === null) ? '' : String(v).trim();
        };

        formData.append(`price[${key}][distributor_id]`, distributorId);
        formData.append(`price[${key}][article_no]`,        getVal('input[name*="[article_no]"]'));
        formData.append(`price[${key}][price]`,             getVal('input.price_field, input[name*="[price]"]'));
        formData.append(`price[${key}][purchase_price]`,    getVal('input.purchase_price, input[name*="[purchase_price]"]'));
        formData.append(`price[${key}][discount_group_id]`, getVal('select.discount_group_select, select[name*="[discount_group_id]"]'));
        formData.append(`price[${key}][discount_price]`,    getVal('input.discount_price, input[name*="[discount_price]"]'));
        formData.append(`price[${key}][discount_percent]`,  getVal('input.discount_percent, input[name*="[discount_percent]"]'));
        formData.append(`price[${key}][price_date]`,        getVal('input[type="date"][name*="[price_date]"]'));
        formData.append(`price[${key}][availability]`,      getVal('select[name*="[availability]"]'));
        formData.append(`price[${key}][note]`, getVal('input[name*="[note]"]'));

      });
    }

    $.ajax({
      url: '{{ route("product.store") }}',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false
    })
    .done(function (res) {
      if (res.product_id) {
        $(SEL.pid).val(res.product_id);
        $('#hiddenProductId').val(res.product_id);
        $('#hiddenDocProductId').val(res.product_id);
        loadProductPreview(res.product_id);
      }

      markStageState(state.currentStageIndex, 'saved');
      if (typeof callback === 'function') callback(true);
    })
    .fail(function (xhr) {
      toast.error('Fehler beim Speichern.');
      console.error('[wizard] save error:', xhr.responseText || xhr);
      if (typeof callback === 'function') callback(false);
    });
  }

  // ==========================================================
  // DROPZONES
  // ==========================================================
  function initImageDropzone() {
    const el = document.getElementById('productImagesDropzone');
    if (!el || state.dropzoneImageInitialized || typeof Dropzone === 'undefined') return;

    Dropzone.autoDiscover = false;

    new Dropzone(el, {
      url: "{{ url('/product_images/upload') }}",
      acceptedFiles: 'image/*',
      maxFilesize: 5,
      paramName: 'file',
      autoProcessQueue: true,
      addRemoveLinks: true,
      sending: function (file, xhr, formData) {
        const pid = $(SEL.pid).val();
        formData.append('product_id', pid);
        $('#hiddenProductId').val(pid);
      },
      success: function () {
        loadImages($(SEL.pid).val());
      }
    });

    state.dropzoneImageInitialized = true;
  }

  function loadImages(productId) {
    if (!productId) return;

    $.get('/product_images/list/' + productId)
      .done(function (data) {
        const $gallery = $('#product-image-gallery').empty();
        const tmpl = $('#image-template').html() || '';

        (data || []).forEach(function (img) {
          const $card = $(tmpl);
          $card.attr('data-id', img.id);
          $card.find('img').attr('src', '/images/products/' + img.image);
          $card.find('.image-name-input').val(img.name || '');

          $card.find('.save-name-btn').on('click', function () {
            const newName = $card.find('.image-name-input').val();
            $.post('/product_images/update-name/' + img.id, { _token: CSRF_TOKEN, name: newName }, function () {
              loadImages(productId);
            });
          });

          $card.find('.delete-image-btn').on('click', function () {
            if (!confirm('Löschen?')) return;
            $.post('/product_images/delete/' + img.id, { _method: 'DELETE', _token: CSRF_TOKEN }, function () {
              loadImages(productId);
            });
          });

          $gallery.append($card);
        });
      });
  }

  function initDocumentsDropzone() {
    const el = document.getElementById('productDocumentsDropzone');
    if (!el || state.dropzoneDocInitialized || typeof Dropzone === 'undefined') return;

    Dropzone.autoDiscover = false;

    new Dropzone(el, {
      url: "{{ route('product_documents.upload') }}",
      acceptedFiles: '.pdf,.doc,.docx,.xls,.xlsx,.txt',
      maxFilesize: 10,
      paramName: 'file',
      sending: function (file, xhr, formData) {
        const pid = $(SEL.pid).val();
        formData.append('product_id', pid);
        $('#hiddenDocProductId').val(pid);
      },
      success: function () {
        loadDocuments($(SEL.pid).val());
      }
    });

    state.dropzoneDocInitialized = true;
  }

  function loadDocuments(productId) {
    if (!productId) return;

    $.get('/product_documents/list/' + productId)
      .done(function (data) {
        const $gallery = $('#product-doc-gallery').empty();
        const tmpl = $('#doc-template').html() || '';

        (data || []).forEach(function (doc) {
          const $card = $(tmpl);
          $card.attr('data-id', doc.id);
          $card.find('.doc-title-input').val(doc.title);
          $card.find('.doc-link').attr('href', '/documents/products/' + doc.document);

          $card.find('.save-doc-name').on('click', function () {
            const t = $card.find('.doc-title-input').val();
            $.post('/product_documents/update-name/' + doc.id, { _token: CSRF_TOKEN, name: t }, function () {
              loadDocuments(productId);
            });
          });

          $card.find('.delete-doc-btn').on('click', function () {
            if (!confirm('Löschen?')) return;
            $.post('/product_documents/delete/' + doc.id, { _method: 'DELETE', _token: CSRF_TOKEN }, function () {
              loadDocuments(productId);
            });
          });

          $gallery.append($card);
        });
      });
  }

  // ==========================================================
  // DESCRIPTIONS CRUD (kept as-is but safe toast)
  // ==========================================================
  function loadDescriptions(productId) {
    const pid = productId || $(SEL.pid).val();
    if (!pid) return;

    $.get('/product/description/get/' + pid).done(function (res) {
      let html = '';
      (res || []).forEach(function (row, i) {
        html += `
          <tr>
            <td>${i + 1}</td>
            <td>${row.field}</td>
            <td>${row.description}</td>
            <td>${row.remark || ''}</td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editModal${row.id}">
                <i class="feather icon-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteDescription(${row.id})">
                <i class="feather icon-trash-2"></i>
              </button>

              <div class="modal fade" id="editModal${row.id}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form onsubmit="event.preventDefault(); updateDescription(${row.id}, this)">
                      <div class="modal-header">
                        <h5 class="modal-title">Eintrag bearbeiten</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                          <label>Überschrift</label>
                          <input type="text" name="field" class="form-control" value="${row.field}">
                        </div>
                        <div class="form-group">
                          <label>Beschreibung</label>
                          <textarea name="description" class="form-control">${row.description}</textarea>
                        </div>
                        <div class="form-group">
                          <label>Anmerkung</label>
                          <textarea name="remark" class="form-control">${row.remark || ''}</textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Speichern</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        `;
      });
      $('#loadedDescriptions tbody').html(html);
    });
  }

  function handleDescriptionFormSubmit(e) {
    e.preventDefault();
    const pid = $(SEL.pid).val();
    if (!pid) { toast.error('Produkt ID fehlt.'); return; }

    $('#productDescriptionForm').find('input[name="product_id"]').val(pid);

    const formData = new FormData(e.target);
    $.ajax({
      url: "{{ route('product.description.store.ajax') }}",
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false
    })
    .done(function (res) {
      toast.success(res.message || 'Beschreibung gespeichert');
      $('#description_table tbody').empty();
      loadDescriptions(pid);
    })
    .fail(function () { toast.error('Fehler beim Speichern'); });
  }

  window.deleteDescription = function (id) {
    if (!confirm('Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?')) return;
    $.ajax({
      url: '/product/description/delete/' + id,
      type: 'DELETE',
      data: { _token: CSRF_TOKEN }
    })
    .done(function (res) {
      toast.success(res.message || 'Eintrag gelöscht');
      loadDescriptions();
    })
    .fail(function () { toast.error('Fehler beim Löschen'); });
  };

  window.updateDescription = function (id, form) {
    const formData = new FormData(form);
    if (CSRF_TOKEN) formData.append('_token', CSRF_TOKEN);

    $.ajax({
      url: '/product/description/update/' + id,
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false
    })
    .done(function (res) {
      toast.success(res.message || 'Eintrag aktualisiert');
      if ($('#editModal' + id).length && $.fn.modal) $('#editModal' + id).modal('hide');
      loadDescriptions();
    })
    .fail(function () { toast.error('Fehler beim Aktualisieren'); });
  };

  // ==========================================================
  // INVENTORY TABLE (kept; safe Swal/toast)
  // ==========================================================
  function prefillInventoryFields(productId) {
    if (!productId) return;
    $.get('/ajax/inventory/product/' + productId)
      .done(function (p) {
        $('#inventory_article_no').val(p.article_no || '');
        $('#inventory_ean').val(p.ean || '');
      })
      .fail(function () { toast.error('Fehler beim Laden der Produktdaten.'); });
  }

  function loadInventoryTable(productId) {
    const tableId = '#inventoryTable';
    if (!productId || !$(tableId).length || !$.fn.DataTable) return;

    if ($.fn.DataTable.isDataTable(tableId)) {
      $(tableId).DataTable().ajax.url('/ajax/inventory/list/' + productId).load();
    } else {
      $(tableId).DataTable({
        ajax: '/ajax/inventory/list/' + productId,
        columns: [
          { data: null, render: (d, t, r, m) => m.row + 1 },
          { data: 'serial_no' },
          { data: 'article_no' },
          { data: 'ean' },
          { data: 'location' },
          { data: 'quantity' },
          { data: 'id', render: (id) => `
            <button class="btn btn-sm btn-outline-danger" onclick="deleteInventory(${id})">
              <i class="feather icon-trash-2"></i>
            </button>`}
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json' }
      });
    }
  }

  function handleInventoryFormSubmit(e) {
    e.preventDefault();
    const pid = $(SEL.pid).val();
    if (!pid) { toast.warning('Bitte speichern Sie zuerst das Produkt.'); return; }

    const formData = new FormData(e.target);
    formData.set('product_id', pid);

    $.ajax({
      url: "{{ route('ajax.inventory.store') }}",
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false
    })
    .done(function (res) {
      toast.success(res.message || 'Inventar gespeichert');
      $('#inventoryForm')[0].reset();
      loadInventoryTable(pid);
    })
    .fail(function () { toast.error('Fehler beim Speichern'); });
  }

  window.deleteInventory = function (id) {
    swalFire({
      title: 'Löschen bestätigen',
      text: 'Möchten Sie diesen Eintrag wirklich löschen?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, löschen',
      cancelButtonText: 'Abbrechen'
    }).then(result => {
      if (!result.isConfirmed) return;
      $.ajax({
        url: '/ajax/inventory/delete/' + id,
        type: 'DELETE',
        data: { _token: CSRF_TOKEN }
      })
      .done(function (res) {
        toast.success(res.message || 'Eintrag gelöscht');
        if ($.fn.DataTable.isDataTable('#inventoryTable')) $('#inventoryTable').DataTable().ajax.reload();
      })
      .fail(function () { toast.error('Löschen fehlgeschlagen'); });
    });
  };

  // ==========================================================
  // FINAL SUMMARY
  // ==========================================================
  function loadFinalSummary(productId) {
    if (!productId) return;
    $.get('/product/final/summary/' + productId)
      .done(function (res) {
        if (!res.product) return;
        const p = res.product;

        setPreviewText('#summary-article_no', p.article_no || '—');
        setPreviewText('#summary-product',    p.product || '—');
        setPreviewText('#summary-ean',        p.ean || '—');
        setPreviewText('#summary-category',   p.category || '—');
        setPreviewText('#summary-color',      p.color || '—');
        setPreviewText('#summary-model',      p.model || '—');

        $('#summary-description').html(p.short_description || '—');
        $('#summary-image').attr('src', res.image ? ('/images/products/' + res.image) : NO_IMAGE);
      })
      .fail(function () { toast.error('Zusammenfassung konnte nicht geladen werden.'); });
  }

  // ==========================================================
  // EVENTS (delegated, no off/on storms)
  // ==========================================================
function bindEvents() {
  // ==========================================================
  // BRAND ROWS
  // ==========================================================
  $(document).on('click', '#add_brand', function (e) {
    e.preventDefault();
    addBrandRow();
  });

  $(document).on('click', '.brand-remove-row', function () {
    $(this).closest('tr').remove();
  });

  // ==========================================================
  // ADVANCED PRICE COLUMNS TOGGLE
  // ==========================================================
  $(document).on('change', '#toggleAdvancedPrices', function () {
    const on = $(this).is(':checked');
    $('#distributor_price').toggleClass('adv-on', on);
  });

  // ==========================================================
  // BRAND MODAL SAVE (works even if modal injected later)
  // ==========================================================
  $(document).on('click', '#saveBrandBtn', function (e) {
    e.preventDefault();
    saveBrandFromModal($(this));
  });

  $(document).on('submit', '#brandForm', function (e) {
    e.preventDefault();
    saveBrandFromModal($(this));
  });

  // ==========================================================
  // DISTRIBUTOR MODAL SAVE
  // ==========================================================
  $(document).on('click', '#saveDistributorBtn', function (e) {
    e.preventDefault();
    saveDistributorFromModal();
  });

  $(document).on('submit', '#distributorForm', function (e) {
    e.preventDefault();
    saveDistributorFromModal();
  });

  // ==========================================================
  // DISTRIBUTOR SELECTION -> ENSURE PRICE ROWS EXIST
  // ==========================================================
  $(document).on('change', '#distributor', function () {
    const selected = ($(this).val() || []).map(String);

    const existing = [];
    $('#distributor_price tbody select.distributor_id').each(function () {
      existing.push(String($(this).val()));
    });

    selected.forEach(function (id) {
      if (id && !existing.includes(id)) addDistributorPriceRow(id);
    });
  });

  $(document).on('click', '#add_price', function () {
    addDistributorPriceRow();
  });

  $(document).on('click', '.remove_price_row', function () {
    $(this).closest('tr').remove();
  });

  // discount group select -> write percent + recalc
  $(document).on('change', '.discount_group_select', function () {
    const $row = $(this).closest('tr');
    const percent = toNum($(this).find('option:selected').data('percent')) || 0;

    $row.find('.discount_percent').val(percent ? fmt2(percent) : '');
    $row.data('lastEdited', 'discP');
    calculateDistributorRow($row, 'is-editing', 'discP');
  });

  // price typing - do not override active input
  $(document)
    .on(
      'focus',
      '#distributor_price .price_field, #distributor_price .purchase_price, #distributor_price .discount_price, #distributor_price .discount_percent',
      function () {
        const $row = $(this).closest('tr');

        if ($(this).hasClass('purchase_price'))   $row.data('lastEdited', 'buy');
        if ($(this).hasClass('discount_price'))   $row.data('lastEdited', 'discE');
        if ($(this).hasClass('discount_percent')) $row.data('lastEdited', 'discP');
        if ($(this).hasClass('price_field'))      $row.data('lastEdited', 'price');

        $(this).addClass('is-editing');
      }
    )
    .on(
      'blur',
      '#distributor_price .price_field, #distributor_price .purchase_price, #distributor_price .discount_price, #distributor_price .discount_percent',
      function () {
        const n = toNum($(this).val());
        if (n != null) $(this).val(fmt2(n));

        $(this).removeClass('is-editing');
        calculateDistributorRow($(this).closest('tr'), 'is-editing');
      }
    )
    .on(
      'input',
      '#distributor_price .price_field, #distributor_price .purchase_price, #distributor_price .discount_price, #distributor_price .discount_percent',
      function () {
        calculateDistributorRow($(this).closest('tr'), 'is-editing');
      }
    );

  // ==========================================================
  // TOP PRICING CALCS
  // ==========================================================
  $(document).on('change', '#discount_group', calculatePurchaseFromGroup);
  $(document).on('input',  '#r_discount_e',   calculatePurchaseFromEuro);

  $(document).on(
    'input change',
    '#purchase_price,#payment_method_type,#advance_price_percent,#advance_price_euro,#plus_percent,#plus_price_percent,#plus_price_euro',
    calculateSalePrice
  );

  $(document).on('input change', '#sale_price,#tax', updateTaxResult);
  $(document).on('input change', '#discount_type,#discount_percent,#discount_euro,#tax', calculateTotalWithTaxAndDiscount);

  // ==========================================================
  // ROOF / SUB-ARTICLE / HEATPUMP
  // ==========================================================
  $(document).on('change', '#category', toggleRoofTypeSection);

  $(document).on('change', '#article_group', function () {
    loadSubArticles($(this).val());
    toggleHeatpumpFields();
  });

  $(document).on('select2:select', '#article_group', toggleHeatpumpFields);

  // ==========================================================
  // LIVE PREVIEW (✅ includes measure_unit / price_unit / package_unit)
  // ==========================================================
  $(document).on(
    'input change',
    '#product,#product_name,[name="product"],' +
      '#article_no,[name="article_no"],' +
      '#ean,[name="ean"],' +
      '#category,[name="category"],' +
      '#color,[name="color"],' +
      '#measure_unit,[name="measure_unit"],' +
      '#price_unit,[name="price_unit"],' +
      '#package_unit,[name="package_unit"],' +
      '#heatpump_type,[name="heatpump_type"],' +
      '#short_description,[name="short_description"],' +
      '#vat,[name="vat"],' +
      '#brand',
    updatePreviewFromForm
  );

  // ==========================================================
  // WIZARD NAVIGATION
  // ==========================================================
  $(document).on('click', '#nextBtn', function () {
    const totalStages = $(SEL.stages).length;

    if (state.currentStageIndex < totalStages - 1) {
      saveCurrentStage(function (ok) {
        if (!ok) return;
        showStage(state.currentStageIndex + 1);
      });
    } else {
      saveCurrentStage(function (ok) {
        if (ok) swalFire('Fertig', 'Alle Schritte abgeschlossen.', 'success');
      });
    }
  });

  $(document).on('click', '#saveNextBtn', function () {
    const totalStages = $(SEL.stages).length;

    saveCurrentStage(function (ok) {
      if (!ok) return;

      if (state.currentStageIndex < totalStages - 1) showStage(state.currentStageIndex + 1);
      else swalFire('Fertig', 'Alle Schritte abgeschlossen.', 'success');
    });
  });

  $(document).on('click', '#prevBtn', function () {
    if (state.currentStageIndex > 0) showStage(state.currentStageIndex - 1);
  });

  $(document).on('click', SEL.steps, function () {
    const idx = parseInt($(this).data('index'), 10) || 0;
    const pid = $(SEL.pid).val();

    if (!pid && idx > 0) {
      swalFire({
        icon: 'warning',
        title: 'Bitte zuerst speichern',
        text: 'Bitte speichern Sie die Produktinformationen, bevor Sie zu einem anderen Schritt wechseln.',
      });
      return;
    }

    showStage(idx);
  });

  $(document).on('click', '#skipStageBtn', function () {
    const totalStages = $(SEL.stages).length;
    const pid = $(SEL.pid).val();

    if (state.currentStageIndex === 0 && !pid) {
      swalFire({
        icon: 'warning',
        title: 'Erster Schritt muss gespeichert werden',
        text: 'Bitte legen Sie das Produkt zuerst mit "Speichern & Weiter" an.',
      });
      return;
    }

    markStageState(state.currentStageIndex, 'skipped');
    if (state.currentStageIndex < totalStages - 1) showStage(state.currentStageIndex + 1);
  });

  // ==========================================================
  // STAGE HOOKS
  // ==========================================================
  $(document).on('click', '.wizard-step[data-index="4"]', function () {
    const pid = $(SEL.pid).val();
    if (!pid) { toast.warning('Produkt zuerst speichern'); return; }
    $('#hiddenProductId').val(pid);
    loadImages(pid);
  });

  $(document).on('click', '.wizard-step[data-index="5"]', function () {
    const pid = $(SEL.pid).val();
    if (!pid) { toast.warning('Produkt zuerst speichern'); return; }
    $('#hiddenDocProductId').val(pid);
    loadDocuments(pid);
  });

  $(document).on('click', '.wizard-step[data-index="3"]', function () {
    const pid = $(SEL.pid).val();
    if (!pid) { toast.warning('Bitte zuerst Produkt speichern'); return; }

    if (!state.inventoryInitialized) {
      prefillInventoryFields(pid);
      loadInventoryTable(pid);
      state.inventoryInitialized = true;
    }
  });

  $(document).on('click', '.wizard-step[data-index="6"]', function () {
    const pid = $(SEL.pid).val();
    if (!pid) { toast.warning('Produkt-ID fehlt. Bitte zuerst speichern.'); return; }

    showStage(6);

    if (!state.finalStageInitialized) {
      loadFinalSummary(pid);
      state.finalStageInitialized = true;
    }
  });

  // ==========================================================
  // DESCRIPTIONS
  // ==========================================================
  $(document).on('click', '#addRow', function () {
    $('#description_table tbody').append(`
      <tr>
        <td><input type="text" class="form-control" name="product[${state.descriptionRowIndex}][field]" required></td>
        <td><textarea class="form-control" name="product[${state.descriptionRowIndex}][description]" required></textarea></td>
        <td><textarea class="form-control" name="product[${state.descriptionRowIndex}][remark]"></textarea></td>
        <td>
          <button type="button" class="btn btn-danger btn-sm remove-row">
            <i class="feather icon-trash"></i>
          </button>
        </td>
      </tr>
    `);
    state.descriptionRowIndex++;
  });

  $(document).on('click', '.remove-row', function () {
    $(this).closest('tr').remove();
  });

  $(document).on('submit', '#productDescriptionForm', handleDescriptionFormSubmit);

  // ==========================================================
  // INVENTORY
  // ==========================================================
  $(document).on('submit', '#inventoryForm', handleInventoryFormSubmit);

  $(document).on('click', '#reloadProductData', function () {
    const pid = $(SEL.pid).val();
    if (pid) { prefillInventoryFields(pid); toast.info('Produktdaten neu geladen.'); }
    else toast.warning('Produkt ist noch nicht gespeichert.');
  });
}


  function closeAnyModal(modal) {
  // modal can be: selector string, DOM node, jQuery object
  let el = null;

  if (typeof modal === 'string') {
    el = document.querySelector(modal);
  } else if (modal && modal.jquery) {
    el = modal.get(0);
  } else if (modal instanceof HTMLElement) {
    el = modal;
  }

  // If not provided / not found, try to close the currently open modal
  if (!el) el = document.querySelector('.modal.show');

  // Bootstrap 5
  if (el && window.bootstrap && bootstrap.Modal) {
    const inst = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    inst.hide();
  }
  // Bootstrap 4 (jQuery plugin)
  else if (el && window.jQuery && jQuery.fn && jQuery.fn.modal) {
    jQuery(el).modal('hide');
  }

  // Hard cleanup (covers stuck backdrop / body lock)
  window.setTimeout(() => {
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('overflow');
  }, 250);
}


  // ==========================================================
  // INIT
  // ==========================================================
  $(function () {
    initFlashToastr();
    if (window.feather) feather.replace();

    initQuillEditor();
    initBaseSelect2();

    bindEvents();

    loadBrands();
    fetchDistributors();
      bindLiveValidationCleanup();

    const existingProductId = $(SEL.pid).val();
    if (existingProductId) loadProductPreview(existingProductId);
    else updatePreviewFromForm();

    // initial toggles
    toggleRoofTypeSection();
    toggleHeatpumpFields();

    // first stage + progress
    showStage(0);
    updateOverallProgress();

    // dropzones (safe)
    initImageDropzone();
    initDocumentsDropzone();

    // descriptions initial load
    loadDescriptions(existingProductId);

    // input stats (safe)
    document.removeEventListener('input', updateInputStats);
    document.addEventListener('input', updateInputStats);
    updateInputStats();
  });

})(jQuery, window, document);
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
          url: "{{ url('product')}}", 
        },
        {
          label: 'Nue Anlegen',
          url: "{{ url()->current()}}",
          clickable:false
        }
      ];

      if (window.setGlobalBreadcrumbs) {
        window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
      }
    </script>
 <script>
(function () {
    'use strict';

    const modal = document.getElementById('new_brand');
    const form = document.getElementById('brandForm');
    const saveBtn = document.getElementById('saveBrandBtn');
    const tableBody = document.querySelector('#add_department tbody');
    const addBtn = document.getElementById('add_brand');
    const imageInput = document.getElementById('brand_image');
    const imagePreview = document.getElementById('brandLogoPreview');

    const fallbackLogo = @json(asset('logo/logo.png'));
    const brandImageBase = @json(asset('images/brand'));
    const storeUrl = @json(route('product.store.brand'));

    let rowIndex = 1;
    let isSubmitting = false;

    function refreshIcons() {
        if (window.feather) {
            window.feather.replace();
        }
    }

    function cleanupBootstrapModalState() {
        document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
            backdrop.remove();
        });

        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.body.style.removeProperty('overflow');

        if (window.$ && $.fn.modal) {
            try {
                $('#new_brand').removeClass('modal fade show').removeAttr('style');
                $('#new_brand').data('bs.modal', null);
                $('#new_brand').data('modal', null);
            } catch (e) {}
        }
    }

    function openBrandModal() {
        if (!modal) return;

        cleanupBootstrapModalState();

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(function () {
            document.getElementById('brand_name')?.focus();
        }, 80);

        refreshIcons();
    }

    function closeBrandModal(reset = false) {
        if (!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        document.body.style.removeProperty('overflow');
        cleanupBootstrapModalState();

        if (reset) {
            resetForm();
        }
    }

    function clearErrors() {
        ['name', 'initial', 'purpose', 'image'].forEach(function (field) {
            const el = document.getElementById(field + '-error');
            if (el) el.textContent = '';
        });
    }

    function createDepartmentRow(index) {
        return `
            <tr>
                <td>
                    <input type="text" class="brand-input" placeholder="Abteilung" name="brand[${index}][brand_department]">
                </td>
                <td>
                    <input type="text" class="brand-input" placeholder="Ansprechpartner" name="brand[${index}][name]">
                </td>
                <td>
                    <input type="text" class="brand-input" placeholder="Position" name="brand[${index}][position]">
                </td>
                <td>
                    <input type="email" class="brand-input" placeholder="E-Mail" name="brand[${index}][email]">
                </td>
                <td>
                    <input type="text" class="brand-input" placeholder="Mobilnummer" name="brand[${index}][phone]">
                </td>
                <td>
                    <input type="text" class="brand-input" placeholder="Festnetznummer" name="brand[${index}][home]">
                </td>
                <td>
                    <input type="text" class="brand-input" placeholder="Büro-Telefonnummer" name="brand[${index}][office]">
                </td>
                <td class="text-right">
                    <button type="button" class="brand-btn-danger brand-remove-row">
                        <i class="feather icon-trash-2"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function resetForm() {
        if (form) form.reset();

        clearErrors();

        if (imagePreview) {
            imagePreview.src = fallbackLogo;
        }

        if (tableBody) {
            tableBody.innerHTML = createDepartmentRow(0);
            rowIndex = 1;
        }

        refreshIcons();
    }

    function brandImageUrl(image) {
        if (!image) return fallbackLogo;

        const value = String(image);

        if (
            value.startsWith('http://') ||
            value.startsWith('https://') ||
            value.startsWith('/')
        ) {
            return value;
        }

        return brandImageBase + '/' + value;
    }

    function addAndSelectBrand(brand) {
        if (!brand || !brand.id || !brand.name) return;

        const select = document.getElementById('brand');
        if (!select) return;

        const imageUrl = brandImageUrl(brand.image);
        let option = select.querySelector('option[value="' + brand.id + '"]');

        if (!option) {
            option = new Option(brand.name, brand.id, true, true);
            option.setAttribute('data-image', imageUrl);
            select.appendChild(option);
        } else {
            option.textContent = brand.name;
            option.setAttribute('data-image', imageUrl);
            option.selected = true;
        }

        if (window.$ && $.fn.select2) {
            $('#brand').val(String(brand.id)).trigger('change.select2').trigger('change');
        } else {
            select.value = String(brand.id);
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function notifySuccess(message) {
        if (window.toastr) {
            toastr.success(message);
        }
    }

    function notifyError(message) {
        if (window.toastr) {
            toastr.error(message);
        } else {
            alert(message);
        }
    }

    function setLoading(isLoading) {
        if (!saveBtn) return;

        saveBtn.disabled = isLoading;
        saveBtn.innerHTML = isLoading
            ? '<i class="feather icon-loader"></i> Speichern...'
            : '<i class="feather icon-save"></i> Speichern';

        refreshIcons();
    }

    function setFieldError(field, message) {
        const errorEl = document.getElementById(field + '-error');

        if (errorEl) {
            errorEl.textContent = message;
        }

        notifyError(message);
    }

    document.addEventListener('click', function (event) {
        const customOpenBtn = event.target.closest('.js-open-brand-modal');

        if (customOpenBtn) {
            event.preventDefault();
            event.stopPropagation();
            openBrandModal();
            return;
        }

        const oldBootstrapTrigger = event.target.closest('[data-target="#new_brand"], [data-bs-target="#new_brand"]');

        if (oldBootstrapTrigger) {
            event.preventDefault();
            event.stopPropagation();
            openBrandModal();
            return;
        }

        if (event.target.closest('.js-brand-modal-close')) {
            event.preventDefault();
            closeBrandModal(false);
            return;
        }

        if (event.target === modal) {
            closeBrandModal(false);
            return;
        }

        if (event.target.closest('.brand-remove-row')) {
            event.preventDefault();

            const row = event.target.closest('tr');

            if (!row || !tableBody) return;

            if (tableBody.querySelectorAll('tr').length <= 1) {
                row.querySelectorAll('input').forEach(function (input) {
                    input.value = '';
                });
                return;
            }

            row.remove();
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
            closeBrandModal(false);
        }
    });

    addBtn?.addEventListener('click', function (event) {
        event.preventDefault();

        if (!tableBody) return;

        tableBody.insertAdjacentHTML('beforeend', createDepartmentRow(rowIndex));
        rowIndex++;

        refreshIcons();
    });

    imageInput?.addEventListener('change', function () {
        const file = imageInput.files?.[0];

        if (!file) {
            imagePreview.src = fallbackLogo;
            return;
        }

        imagePreview.src = URL.createObjectURL(file);
    });

    form?.addEventListener('submit', function (event) {
        event.preventDefault();

        if (isSubmitting) return;

        clearErrors();
        setLoading(true);
        isSubmitting = true;

        const formData = new FormData(form);

        $.ajax({
            url: storeUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            success: function (response) {
                const brand = response.brand || response.data || response;

                addAndSelectBrand(brand);

                if (typeof window.updatePreviewFromForm === 'function') {
                    window.updatePreviewFromForm();
                }

                closeBrandModal(true);

                notifySuccess(response.save_msg || response.message || 'Hersteller wurde erfolgreich gespeichert.');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};

                    Object.keys(errors).forEach(function (field) {
                        const message = errors[field]?.[0] || 'Ungültige Eingabe.';
                        setFieldError(field, message);
                    });

                    return;
                }

                notifyError(xhr.responseJSON?.message || 'Hersteller konnte nicht gespeichert werden.');
            },
            complete: function () {
                setLoading(false);
                isSubmitting = false;
            }
        });
    });

    window.openBrandModal = openBrandModal;
    window.closeBrandModal = closeBrandModal;

    cleanupBootstrapModalState();
    refreshIcons();
})();
</script>
@endpush