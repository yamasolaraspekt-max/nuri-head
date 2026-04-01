@extends('admin.layouts.app')

@section('title') Produkt – {{ $data->product }} @endsection

@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">


<style>
    :root {
        --prod-bg: #f6f7fb;
        --prod-shell: #ffffff;
        --prod-border: rgba(15,23,42,.06);
        --prod-border-strong: rgba(148,163,184,.35);
        --prod-muted: #2c2c2cff;
        --prod-accent: #2563eb;
        --prod-accent-soft: rgba(37,99,235,.08);
        --prod-success: #16a34a;
        --prod-danger: #ef4444;
        --prod-radius-xl: 20px;
        --prod-radius-lg: 16px;
        --prod-shadow: 0 22px 60px rgba(15,23,42,.10);
    }

    body {
        background: var(--prod-bg) !important;
    }

    .product-page-shell {
        border-radius: var(--prod-radius-xl);
        background: var(--prod-shell);
        border: 1px solid var(--prod-border);
        box-shadow: var(--prod-shadow);
        padding: 1.25rem 1.5rem 1.6rem;
    }

    .product-hero {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px dashed rgba(148,163,184,.5);
        padding-bottom: .75rem;
    }

    .product-hero-main h1 {
        font-size: 1.5rem;
        margin: 0;
    }
    .product-hero-main small {
        display: block;
        font-size: .85rem;
        color: var(--prod-muted);
    }

    .product-hero-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .5rem;
        justify-content: flex-end;
    }

    .pill-badge {
        font-size: .75rem;
        padding: .22rem .7rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        background: var(--prod-accent-soft);
        color: var(--prod-accent);
    }
    .pill-badge-muted {
        background: #e5e7eb;
        color: #374151;
    }
    .pill-status-published {
        background: rgba(22,163,74,.12);
        color: #15803d;
    }
    .pill-status-unpublished {
        background: rgba(239,68,68,.10);
        color: #b91c1c;
    }

    .product-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .product-column-left {
        border-right: 1px dashed rgba(148,163,184,.5);
    }

    .card-soft {
        border-radius: var(--prod-radius-lg);
        border: 1px solid var(--prod-border-strong);
        background: #ffffffff;
        color: #3f3f3fff; 
        padding: .9rem .85rem 1rem;
        margin-bottom: .9rem;
    }
 
    .card-soft-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .4rem;
    }
    .card-soft-header h5 {
        font-size: .9rem;
        margin: 0;
        color: #3a3a3aff;
    }
    .card-soft-header small {
        font-size: .7rem;
        color: #3a3a3aff;
    }

    .media-shell {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(148,163,184,.45);
        background: radial-gradient(circle at 0 0, rgba(37,99,235,.25), transparent 60%);
        margin-bottom: .6rem;
    }

    .media-shell-inner {
        background: #020617;
    }

    .doc-chip {
        font-size: .75rem;
        padding: .14rem .5rem;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.6);
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        margin: 0 .25rem .25rem 0;
    }

    .summary-list {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: .8rem;
    }
    .summary-list li {
        display: flex;
        justify-content: space-between;
        gap: .5rem;
        padding: .2rem 0;
        border-bottom: 1px dashed rgba(148,163,184,.35);
    }
    .summary-list li:last-child {
        border-bottom: 0;
    }
    .summary-label {
        color: #3a3a3aff;
    }
    .summary-value {
        color: #3a3a3aff;
        text-align: right;
    }

    .product-tabs {
        border-bottom: 1px solid rgba(209,213,219,.9);
        margin-bottom: .8rem;
    }
    .product-tabs .nav-link {
        font-size: .85rem;
        padding: .45rem .85rem;
        border-radius: 999px 999px 0 0;
        margin-right: .2rem;
        color: #4b5563;
    }
    .product-tabs .nav-link.active {
        background: #111827;
        color: #f9fafb;
    }

    .product-tab-panel {
        margin-bottom: .9rem;
    }

    .product-panel-card {
        border-radius: 14px;
        border: 1px solid rgba(209,213,219,.9);
        background: #ffffff;
        padding: .85rem .9rem 1rem;
        margin-bottom: .8rem;
    }
    .product-panel-card h5 {
        font-size: .95rem;
        margin-bottom: .4rem;
    }
    .product-panel-card small {
        color: var(--prod-muted);
    }

    .short-description {
        max-height: 200px;
        overflow: auto;
        border-radius: 10px;
        background: #f9fafb;
        padding: .6rem .7rem;
        font-size: .82rem;
    }

    .tag-chip {
        display: inline-flex;
        align-items: center;
        padding: .14rem .5rem;
        border-radius: 999px;
        font-size: .75rem;
        background: #eff6ff;
        color: #1d4ed8;
        margin: 0 .25rem .25rem 0;
    }

    .timeline {
        list-style: none;
        padding-left: 0;
        position: relative;
        margin: 0;
    }
    .timeline::before {
        content: "";
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: rgba(148,163,184,.7);
    }
    .timeline-item {
        position: relative;
        padding-left: 32px;
        padding-bottom: .7rem;
    }
    .timeline-marker {
        position: absolute;
        left: 3px;
        width: 14px;
        height: 14px;
        border-radius: 999px;
        background: #22c55e;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px rgba(34,197,94,.35);
        top: 3px;
    }
    .timeline-marker.danger {
        background: #ef4444;
        box-shadow: 0 0 0 2px rgba(239,68,68,.35);
    }
    .timeline-marker.warning {
        background: #eab308;
        box-shadow: 0 0 0 2px rgba(234,179,8,.35);
    }
    .timeline-title {
        font-weight: 600;
        font-size: .85rem;
    }
    .timeline-meta {
        font-size: .72rem;
        color: var(--prod-muted);
    }

    .doc-list-table th,
    .doc-list-table td,
    .price-table th,
    .price-table td,
    .inventory-table th,
    .inventory-table td {
        font-size: .8rem;
        vertical-align: middle;
    }

    .tech-stars i {
        color: #facc15;
        font-size: .75rem;
    }

    .related-product-card {
        border-radius: 10px;
        border: 1px solid rgba(209,213,219,.8);
        padding: .35rem .45rem;
        font-size: .78rem;
        margin-bottom: .35rem;
        display: flex;
        gap: .5rem;
    }
    .related-product-card img {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        object-fit: cover;
    }

    @media (max-width: 991.98px) {
        .product-column-left {
            border-right: none;
            border-bottom: 1px dashed rgba(148,163,184,.5);
            margin-bottom: 1rem;
            padding-bottom: .75rem;
        }
        .product-page-shell {
            padding: 1rem;
        }
    }
</style>

<style>
    .supplier-panel {
        border-radius: 16px;
        border: 1px solid rgba(15,23,42,.08);
        background: #ffffff;
        padding: 1rem 1.25rem 1.25rem;
        box-shadow: 0 18px 40px rgba(15,23,42,.08);
    }

    .supplier-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
        margin-bottom: .75rem;
    }

    .supplier-panel-header h5 {
        margin: 0;
        font-size: 1.05rem;
    }
    .supplier-panel-header small {
        font-size: .78rem;
        color: #6b7280;
    }

    .supplier-badge {
        font-size: .78rem;
        padding: .15rem .7rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }

    .supplier-label {
        font-size: .8rem;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: .25rem;
        margin-bottom: .15rem;
    }

    .supplier-input-group .input-group-text {
        font-size: .75rem;
        padding: .15rem .45rem;
        background: #f3f4f6;
        border-color: #e5e7eb;
    }

    .supplier-form-row .,
    .supplier-form-row .form-control {
        font-size: .8rem;
    }

    .supplier-calc-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .15rem .6rem;
        border-radius: 999px;
        background: #ecfdf3;
        color: #15803d;
        font-size: .78rem;
        border: 1px solid rgba(22,163,74,.25);
    }

    .price-table thead th {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        border-bottom-width: 1px;
    }

    .price-table tbody td {
        font-size: .78rem;
        vertical-align: middle;
    }

    @media (max-width: 991.98px) {
        .supplier-panel {
            padding: .85rem;
        }
    }
    text { 
    height: 39px !important;
    }
</style>

<style>
  /* =========================================================
     PRODUCT PAGE FONT SCALE
     - Central place to make everything bigger
     - Adjust only the variables if you want more/less
  ========================================================= */
  :root{
    --fs-base: 1rem;        /* general text */
    --fs-small: .92rem;     /* muted/help text */
    --fs-xs: .88rem;        /* tiny badges/chips */
    --fs-label: 1rem;       /* form labels */
    --fs-h1: 1.8rem;        /* main product title */
    --fs-h2: 1.35rem;       /* section titles */
    --fs-h5: 1.10rem;       /* card titles */
    --fs-tab: 1rem;         /* tab labels */
    --fs-table: .98rem;     /* table text */
    --fs-btn: .98rem;       /* button text */
    --fs-pill: .92rem;      /* badges/pills */
  }

  /* Scope everything to this page shell to avoid affecting other pages */
  .product-page-shell{
    font-size: var(--fs-base);
  }

  /* Headings */
  .product-hero-main h1{ font-size: var(--fs-h1) !important; }
  .content-header-title{ font-size: var(--fs-h2) !important; }

  .card-soft-header h5,
  .product-panel-card h5,
  .supplier-panel-header h5{
    font-size: var(--fs-h5) !important;
  }

  /* Subtitles / muted texts */
  .product-hero-main small,
  .card-soft-header small,
  .product-panel-card small,
  .supplier-panel-header small,
  .text-muted,
  small{
    font-size: var(--fs-small);
  }

  /* Labels */
  .product-page-shell label,
  .supplier-label{
    font-size: var(--fs-label) !important;
  }

  /* Tabs */
  .product-tabs .nav-link{
    font-size: var(--fs-tab) !important;
  }

  /* Tables */
  .doc-list-table th,
  .doc-list-table td,
  .price-table th,
  .price-table td,
  .inventory-table th,
  .inventory-table td,
  #tech-description-table th,
  #tech-description-table td{
    font-size: var(--fs-table) !important;
  }

  /* Lists / body blocks */
  .summary-list{ font-size: var(--fs-base) !important; }
  .timeline-title{ font-size: 1rem !important; }
  .timeline-meta{ font-size: var(--fs-small) !important; }
  .short-description{ font-size: 1rem !important; }

  /* Pills / chips */
  .pill-badge{ font-size: var(--fs-pill) !important; }
  .doc-chip,
  .tag-chip{
    font-size: var(--fs-xs) !important;
  }

  /* Buttons */
  .product-actions .btn,
  .btn.btn-sm{
    font-size: var(--fs-btn) !important;
  }

  /* Input text (Bootstrap form controls) */
  .product-page-shell .form-control,
  .product-page-shell .input-group-text{
    font-size: 1rem !important;
  }
</style>

@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-7 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Produkt</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('product.info') }}">Produkte</a></li>
                                <li class="breadcrumb-item active">{{ $data->product }} – {{ $data->model }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="product-page-shell">
                {{-- HERO --}}
                <div class="product-hero">
                    <div class="product-hero-main">
                        <h1>{{ $data->product }}</h1>
                        <small>
                            Modell: {{ $data->model ?: '–' }}
                            · Hersteller Nr. {{ $data->article_no ?: '–' }} 
                        </small>

                        <small>
                            Kategorie: {{ $data->category ?: '–' }} · Gruppe: {{ $data->article_group ?: '–' }}
                        </small>

                        <small>
                            Maße / Measurement: {{ $data->measurement ?: '–' }}
                        </small>

                        <small>
                            Kategorie: {{ $data->category ?: '–' }} · Gruppe: {{ $data->article_group ?: '–' }}
                        </small>
                    </div>
                    <div class="product-hero-meta">
                        <span class="pill-badge">
                            <i class="feather icon-archive"></i>
                            {{ $data->brandname ?: 'Hersteller unbekannt' }}
                        </span>

                        <span class="pill-badge pill-badge-muted">
                            EAN: {{ $data->ean ?: '–' }}
                        </span>

                        <span class="pill-badge {{ $data->status === 'Published' ? 'pill-status-published' : 'pill-status-unpublished' }}">
                            {{ $data->status === 'Published' ? 'Aktiv' : 'Inaktiv' }}
                        </span>

                        <div class="product-actions">
                            <a href="{{ url('/product/edit/'.$data->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="feather icon-edit mr-25"></i> Bearbeiten
                            </a>
                            <a href="{{ url('/product_installation/'.$data->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="feather icon-clock mr-25"></i> Montagezeiten
                            </a>
                            @if($data->status === 'Published')
                                <a href="{{ url('/product_unpublish/'.$data->id) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="feather icon-slash mr-25"></i> Deaktivieren
                                </a>
                            @else
                                <a href="{{ url('/product_publish/'.$data->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="feather icon-check mr-25"></i> Aktivieren
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- LEFT COLUMN: MEDIA, SUMMARY, RELATED --}}
                    <div class="col-xl-4 col-lg-5 product-column-left">

                        {{-- MEDIA: IMAGES + QUICK DOCS --}}
                        <div class="card-soft mb-1">
                            <div class="card-soft-header">
                                <div>
                                    <h5>Medien & Dateien</h5>
                                    <small>Bilder, Dokumente, technische Unterlagen</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ url('product_create_image/'.$data->id) }}" class="btn btn-outline-light" data-toggle="tooltip" title="Bild hinzufügen">
                                        <i class="feather icon-image"></i>
                                    </a>
                                    <a href="{{ url('product_create_document/'.$data->id) }}" class="btn btn-outline-light" data-toggle="tooltip" title="Dokument hinzufügen">
                                        <i class="feather icon-file-plus"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="media-shell">
                                <div class="media-shell-inner">
                                    {{-- Reuse existing slider include --}}
                                    @include('admin.product.product.pages.slider', ['pro_images' => $pro_images])
                                </div>
                            </div>

                            {{-- Quick docs chips --}}
                            <div class="mt-25">
                                @forelse($documents as $doc)
                                    <a href="{{ asset('images/products/document/'.$doc->document) }}" target="_blank" class="doc-chip">
                                        <i class="feather icon-file-text"></i>
                                        {{ $doc->title ?: $doc->document }}
                                    </a>
                                @empty
                                    <span class="text-sm text-muted">Noch keine Dokumente hinterlegt.</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- QUICK SUMMARY (dark) --}}
                        <div class="card-soft">
                            <div class="card-soft-header">
                                <h5>Produktübersicht</h5>
                                <small>Stammdaten & Kennzahlen</small>
                            </div>
                            <ul class="summary-list">
                                <li>
                                    <span class="summary-label">Hersteller</span>
                                    <span class="summary-value">{{ $data->brandname ?: '–' }}</span>
                                </li>
                                <li>
                                    <span class="summary-label">Farbe</span>
                                    <span class="summary-value">{{ $data->color ?: '–' }}</span>
                                </li>
                                <li>
                                    <span class="summary-label">Mengeneinheit</span>
                                    <span class="summary-value">{{ $data->measurement ?: '–' }}</span>
                                </li>
                                <li>
                                    <span class="summary-label">Preiseinheit</span>
                                    <span class="summary-value">{{ $data->price_unit ?: '–' }}</span>
                                </li>
                                <li>
                                    <span class="summary-label">Packungseinheit</span>
                                    <span class="summary-value">{{ $data->package_unit ?: '–' }}</span>
                                </li>
                            </ul>

                            {{-- PV / Radiator status --}}
                            <div class="mt-75">
                                @if($product_pv || $product_radiator)
                                    <div class="tag-chip">
                                        <i class="feather icon-sun"></i> PV / Heizkörper konfiguriert
                                    </div>
                                    @if($product_pv)
                                        <div class="text-xs text-muted mt-25">
                                            PV: {{ $product_pv->pv_type ?? '' }} · {{ $product_pv->power ?? '' }}
                                        </div>
                                    @endif
                                    @if($product_radiator)
                                        <div class="text-xs text-muted">
                                            Heizkörper: {{ $product_radiator->radiator_type ?? '' }} · {{ $product_radiator->power ?? '' }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted" style="font-size:.78rem;">
                                        Noch keine PV/Heizkörper-Konfiguration hinterlegt.
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- RELATED PRODUCTS / OFFERS --}} 


                    </div>

                    {{-- RIGHT COLUMN: TABS --}}
                    <div class="col-xl-8 col-lg-7">
                        {{-- tabs --}}
                        <ul class="nav nav-tabs product-tabs" id="productTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-overview" data-toggle="tab" href="#panel-overview" role="tab">Übersicht</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-tech" data-toggle="tab" href="#panel-tech" role="tab">Technische Daten</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-suppliers" data-toggle="tab" href="#panel-suppliers" role="tab">Lieferanten & Preise</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-docs" data-toggle="tab" href="#panel-docs" role="tab">Dokumente</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-inventory" data-toggle="tab" href="#panel-inventory" role="tab">Inventar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-install" data-toggle="tab" href="#panel-install" role="tab">Montage</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-team" data-toggle="tab" href="#panel-team" role="tab">Technisches Team</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="productTabsContent">

                            {{-- OVERVIEW --}}
                            <div class="tab-pane fade show active product-tab-panel" id="panel-overview" role="tabpanel">
                                <div class="product-panel-card">
                                    <h5>Produktbeschreibung</h5>
                                    <small>Kurzbeschreibung, wie im Artikel hinterlegt.</small>
                                    <div class="short-description mt-50">
                                        {!! $data->short_description ?: '<span class="text-muted">Noch keine Kurzbeschreibung hinterlegt.</span>' !!}
                                    </div>
                                </div>

                                <div class="product-panel-card">
                                    <h5>Technische Highlights</h5>
                                    <small>Wichtige Eigenschaften auf einen Blick.</small>
                                    <div class="row mt-50">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled" style="font-size:.82rem;">
                                                @forelse($descriptions->take(4) as $descript)
                                                    <li class="mb-25">
                                                        <strong>{{ $descript->field }}:</strong>
                                                        {{ Str::limit($descript->description, 80) }}
                                                    </li>
                                                @empty
                                                    <li class="text-muted">Noch keine technischen Details hinterlegt.</li>
                                                @endforelse
                                            </ul>
                                        </div> 
                                    </div>

                                    <div class="mt-50">
                                        <a href="{{ url('/product_create_description/'.$data->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="feather icon-edit mr-25"></i> Technische Beschreibung bearbeiten
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- TECHNICAL DESCRIPTION TAB --}} 
                            <div class="tab-pane fade product-tab-panel" id="panel-tech" role="tabpanel">
                                <div class="product-panel-card" id="product-tech-tab-card" data-product-id="{{ $data->id }}">
                                    <h5>Technische Beschreibung</h5>
                                    <small>Alle hinterlegten technischen Felder.</small>

                                    <div class="table-responsive mt-50">
                                        <table class="table table-sm" id="tech-description-table">
                                            <tbody>
                                            @forelse($descriptions as $descript)
                                                <tr id="tech-row-{{ $descript->id }}">
                                                    <th style="width:30%;">{{ $descript->field }}</th>
                                                    <td>
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="pr-1">
                                                                {{ $descript->description }}
                                                                @if($descript->remark)
                                                                    <div class="text-muted" style="font-size:.76rem;">
                                                                        {{ $descript->remark }}
                                                                    </div>
                                                                @endif
                                                                @if($descript->status)
                                                                    <div class="text-muted" style="font-size:.7rem;">
                                                                        Status: {{ $descript->status }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="pl-1 text-nowrap">
                                                                <button type="button"
                                                                        class="btn btn-sm btn-link text-primary p-0 btn-edit-tech"
                                                                        data-id="{{ $descript->id }}"
                                                                        data-field="{{ $descript->field }}"
                                                                        data-description="{{ $descript->description }}"
                                                                        data-remark="{{ $descript->remark }}"
                                                                        data-status="{{ $descript->status }}">
                                                                    <i class="feather icon-edit-2"></i>
                                                                </button>
                                                                <button type="button"
                                                                        class="btn btn-sm btn-link text-danger p-0 btn-delete-tech"
                                                                        data-id="{{ $descript->id }}">
                                                                    <i class="feather icon-trash-2"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr id="tech-empty-row">
                                                    <td colspan="2" class="text-muted">
                                                        Keine technischen Beschreibungen hinterlegt.
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-50 d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-open-add-tech-modal">
                                            <i class="feather icon-plus mr-25"></i> Technische Daten hinzufügen
                                        </button>

                                        @if($descriptions->count())
                                            <small class="text-muted" id="tech-count-label">
                                                {{ $descriptions->count() }} Einträge
                                            </small>
                                        @else
                                            <small class="text-muted" id="tech-count-label">0 Einträge</small>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== ADD MULTIPLE DESCRIPTIONS MODAL ===================== --}}
                            <div class="modal fade" id="technicalDescriptionModal" tabindex="-1" role="dialog"
                                aria-labelledby="technicalDescriptionModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <form id="technical-description-form">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="technicalDescriptionModalLabel">Technische Daten hinzufügen</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-1">
                                                    <small class="text-muted">
                                                        Mit <strong>+</strong> und <strong>−</strong> können mehrere Zeilen ergänzt werden,
                                                        bevor sie gemeinsam gespeichert werden.
                                                    </small>
                                                </div>

                                                <div id="td-rows-container">
                                                    {{-- Default row --}}
                                                    <div class="td-row border rounded p-1 mb-1">
                                                        <div class="form-row">
                                                            <div class="form-group col-md-3">
                                                                <label>Feld</label>
                                                                <input type="text" name="field[]" class="form-control" required>
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <label>Beschreibung</label>
                                                                <input type="text" name="description[]" class="form-control">
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                <label>Bemerkung</label>
                                                                <input type="text" name="remark[]" class="form-control">
                                                            </div>

                                                            <div class="form-group col-md-2">
                                                                <label>Status</label>
                                                                <input type="text" name="status[]" class="form-control">
                                                            </div>

                                                            <div class="form-group col-md-1 d-flex align-items-end">
                                                                <button type="button" class="btn btn-sm btn-danger td-btn-remove-row">
                                                                    <i class="feather icon-minus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" class="btn btn-sm btn-secondary" id="td-btn-add-row">
                                                    <i class="feather icon-plus"></i> Zeile hinzufügen
                                                </button>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                                                <button type="submit" class="btn btn-primary">Speichern</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- ===================== EDIT SINGLE DESCRIPTION MODAL ===================== --}}
                            <div class="modal fade" id="editTechnicalDescriptionModal" tabindex="-1" role="dialog"
                                aria-labelledby="editTechnicalDescriptionModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form id="edit-technical-description-form">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" id="edit-description-id">

                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editTechnicalDescriptionModalLabel">Technische Beschreibung bearbeiten</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Feld</label>
                                                    <input type="text" class="form-control" id="edit-field" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Beschreibung</label>
                                                    <input type="text" class="form-control" id="edit-description">
                                                </div>
                                                <div class="form-group">
                                                    <label>Bemerkung</label>
                                                    <input type="text" class="form-control" id="edit-remark">
                                                </div>
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <input type="text" class="form-control" id="edit-status">
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                                                <button type="submit" class="btn btn-primary">Speichern</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
   
                            <div class="tab-pane fade product-tab-panel" id="panel-suppliers" role="tabpanel">
                                <div
                                    class="product-panel-card supplier-panel"
                                    id="supplier-panel"
                                    data-product-id="{{ $data->id }}"
                                    data-url-load="{{ route('products.suppliers.data', $data->id) }}"
                                    data-url-store="{{ route('products.distributor-prices.store', $data->id) }}"
                                >
                                <div class="supplier-panel-header">
                                <div>
                                    <h5>
                                    <i class="feather icon-truck mr-50"></i>
                                    Lieferanten & Preise
                                    </h5>
                                    <small>Hinterlegen Sie Einkaufs- und Rabattkonditionen pro Lieferant.</small>
                                </div>

                                <span class="supplier-badge">
                                    <i class="feather icon-tag mr-25"></i>
                                    Produkt-ID #{{ $data->id }}
                                </span>
                                </div>

                                {{-- INLINE FORM (AJAX) --}}
                                <form id="supplier-price-form" class="mt-50">
                                @csrf

                                {{-- ✅ Advanced toggle (default OFF => show EK + Art.Nr + Verfügbarkeit) --}}
                                <div class="d-flex align-items-center justify-content-between mb-50">
                                    <label class="mb-0 d-flex align-items-center" style="gap:.5rem; cursor:pointer;">
                                    <input type="checkbox" id="supplier-advanced-toggle" class="mr-25">
                                    <span class="font-weight-600">Erweitert</span>
                                    <small class="text-muted">(UVP/Rabatte/Gruppe/Datum/Status)</small>
                                    </label>

                                    <small class="text-muted" style="font-size:.78rem;">
                                    Standardansicht: EK + Art.Nr + Verfügbarkeit
                                    </small>
                                </div>

                                <div class="row supplier-form-row">

                                    {{-- Lieferant (always visible) --}}
                                    <div class="col-lg-4 col-md-6 mb-1">
                                    <label class="supplier-label">
                                        <i class="feather icon-user mr-25"></i> Lieferant
                                    </label>
                                    <select name="distributor_id" id="supplier_distributor_id" class="form-control">
                                        <option value="">– Lieferant auswählen –</option>
                                        {{-- options filled via AJAX --}}
                                    </select>
                                    <small class="text-muted d-block mt-25" style="font-size:.75rem;">
                                        Fehlt der Lieferant? Legen Sie ihn zuerst im Lieferantenmodul an.
                                    </small>
                                    </div>

                                    {{-- Einkaufspreis (EK) (always visible) --}}
                                    <div class="col-lg-2 col-md-6 mb-1">
                                    <label class="supplier-label">
                                        <i class="feather icon-shopping-cart mr-25"></i> Einkaufspreis
                                    </label>
                                    <div class="input-group input-group supplier-input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">€</span>
                                        </div>
                                        <input
                                        type="number"
                                        step="0.01"
                                        name="purchase_price"
                                        id="sp_purchase_price"
                                        class="form-control text"
                                        placeholder="0,00"
                                        >
                                    </div>
                                    </div>

                                    {{-- ✅ Artikelnummer (ALWAYS visible) --}}
                                    <div class="col-lg-2 col-md-6 mb-1">
                                    <label class="supplier-label">
                                        <i class="feather icon-hash mr-25"></i> Art.Nr.
                                    </label>
                                    <input type="text" name="article_no" id="sp_article_no" class="form-control" value="">
                                    </div>

                                    {{-- ✅ Verfügbarkeit (ALWAYS visible) --}}
                                    <div class="col-lg-3 col-md-6 mb-1">
                                    <label class="supplier-label">
                                        <i class="feather icon-package mr-25"></i> Verfügbarkeit
                                    </label>
                                    <input
                                        type="text"
                                        name="availability"
                                        id="sp_availability"
                                        class="form-control"
                                        placeholder="z.B. Lagernd, 2-3 Wochen, auf Anfrage"
                                    >
                                    </div>

                                    {{-- UVP (advanced-only) --}}
                                    <div class="col-lg-2 col-md-6 mb-1 supplier-advanced-only">
                                    <label class="supplier-label">
                                        <i class="feather icon-dollar-sign mr-25"></i> UVP (€)
                                    </label>
                                    <div class="input-group input-group supplier-input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">€</span>
                                        </div>
                                        <input type="number" step="0.01" name="price" id="sp_price" class="form-control text" placeholder="0,00">
                                    </div>
                                    </div>

                                    {{-- Rabatt in % (advanced-only) --}}
                                    <div class="col-lg-2 col-md-6 mb-1 supplier-advanced-only">
                                    <label class="supplier-label">
                                        <i class="feather icon-percent mr-25"></i> Rabatt %
                                    </label>
                                    <div class="input-group input-group supplier-input-group">
                                        <input type="number" name="discount_percent" id="sp_discount_percent" class="form-control text" min="0" max="100" placeholder="z.B. 20">
                                        <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    </div>

                                    {{-- Rabatt in € (advanced-only) --}}
                                    <div class="col-lg-2 col-md-6 mb-1 supplier-advanced-only">
                                    <label class="supplier-label">
                                        <i class="feather icon-arrow-down-right mr-25"></i> Rabatt €
                                    </label>
                                    <div class="input-group input-group supplier-input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">€</span>
                                        </div>
                                        <input type="number" step="0.01" name="discount_price" id="sp_discount_price" class="form-control text" placeholder="0,00">
                                    </div>
                                    </div>

                                    {{-- Datum (advanced-only) --}}
                                    <div class="col-lg-2 col-md-6 mb-1 supplier-advanced-only">
                                    <label class="supplier-label">
                                        <i class="feather icon-calendar mr-25"></i> Datum
                                    </label>
                                    <input type="date" name="price_date" class="form-control" value="{{ now()->toDateString() }}">
                                    </div>

                                    {{-- Rabattgruppe (advanced-only) --}}
                                    <div class="col-lg-3 col-md-6 mb-1 supplier-advanced-only">
                                    <label class="supplier-label">
                                        <i class="feather icon-layers mr-25"></i> Rabattgruppe
                                    </label>
                                    <select name="discount_group_id" id="supplier_discount_group_id" class="form-control">
                                        <option value="">– keine –</option>
                                        {{-- options filled via AJAX --}}
                                    </select>
                                    </div>

                                    {{-- Status (advanced-only) --}}
                                    <div class="col-lg-2 col-md-6 mb-1 supplier-advanced-only">
                                    <label class="supplier-label">
                                        <i class="feather icon-activity mr-25"></i> Status
                                    </label>
                                    <select name="status" class="form-control">
                                        <option value="Published">Aktiv</option>
                                        <option value="Unpublished">Inaktiv</option>
                                    </select>
                                    </div>

                                    {{-- Save button (always visible) --}}
                                    <div class="col-lg-2 col-md-6 mb-1">
                                    <label class="supplier-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-sm btn-primary btn-block" id="supplier-save-btn">
                                        <span class="spinner-border spinner-border-sm mr-25 d-none" id="supplier-save-spinner"></span>
                                        <i class="feather icon-save mr-25"></i> Preis speichern
                                    </button>
                                    </div>
                                </div>

                                {{-- Live calc info (advanced-only) --}}
                                <div class="supplier-calc-info mt-25 supplier-advanced-only" id="supplier-calc-info" style="display:none;">
                                    <div class="supplier-calc-pill">
                                    <i class="feather icon-info mr-25"></i>
                                    <span id="supplier-calc-text"></span>
                                    </div>
                                </div>

                                <div id="supplier-price-errors" class="text-danger mt-25" style="font-size:.78rem; display:none;"></div>
                                </form>

                                {{-- TABLE --}}
                                <div class="mt-50">
                                <p class="text-muted" style="font-size:.8rem;" id="supplier-prices-empty">
                                    Es sind noch keine Lieferantenpreise für dieses Produkt hinterlegt.
                                </p>

                                <div class="table-responsive d-none" id="supplier-prices-table-wrapper">
                                    <table class="table table-sm price-table">
                                    <thead>
                                        <tr>
                                        {{-- ✅ always visible --}}
                                        <th>Art.Nr.</th>
                                        <th>Lieferant</th>

                                        {{-- advanced-only columns --}}
                                        <th class="supplier-col-advanced-only">UVP</th>
                                        <th class="supplier-col-advanced-only">Rabatt €</th>
                                        <th class="supplier-col-advanced-only">Rabatt %</th>

                                        {{-- ✅ always visible --}}
                                        <th>EK</th>

                                        {{-- advanced-only --}}
                                        <th class="supplier-col-advanced-only">Datum</th>

                                        {{-- ✅ always visible --}}
                                        <th>Verfügbarkeit</th>

                                        <th style="width:120px;" class="text-right">Aktion</th>
                                        </tr>
                                    </thead>

                                    <tbody id="supplier-prices-tbody">
                                        {{-- rows via JS --}}
                                    </tbody>
                                    </table>
                                </div>
                                </div>

                                </div>
                            </div>
 
                            {{-- DOCUMENTS TAB --}}
                            <div class="tab-pane fade product-tab-panel" id="panel-docs" role="tabpanel">
                                <div class="product-panel-card">
                                    <h5>Dokumente & technische Unterlagen</h5>
                                    <small>Alle zum Produkt hinterlegten Dateien.</small>

                                    <div class="table-responsive mt-50">
                                        <table class="table table-sm doc-list-table">
                                            <thead>
                                                <tr>
                                                    <th>Titel</th>
                                                    <th>Datei</th>
                                                    <th>Typ</th>
                                                    <th style="width:120px;">Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($documents as $doc)
                                                    @php
                                                        $ext = pathinfo($doc->document, PATHINFO_EXTENSION);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $doc->title ?: '—' }}</td>
                                                        <td>{{ $doc->document }}</td>
                                                        <td>{{ strtoupper($ext) }}</td>
                                                        <td>
                                                            <a href="{{ asset('images/products/document/'.$doc->document) }}"
                                                               target="_blank"
                                                               class="btn btn-sm btn-outline-secondary">
                                                                <i class="feather icon-eye"></i>
                                                            </a>
                                                            <a href="{{ asset('images/products/document/'.$doc->document) }}"
                                                               download
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="feather icon-download"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-muted">Keine Dokumente hinterlegt.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-50">
                                        <a href="{{ url('product_create_document/'.$data->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="feather icon-file-plus mr-25"></i> Dokument hinzufügen
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- INVENTORY TAB --}}
                            <div class="tab-pane fade product-tab-panel" id="panel-inventory" role="tabpanel">
                                <div class="product-panel-card">
                                    <h5>Inventar</h5>
                                    <small>Seriennummern, Lagerorte und Bestände.</small>

                                    {{-- Quick add form (AJAX) --}}
                                    <form id="inventoryForm" class="mt-50">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $data->id }}">
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label>Serien-Nr.</label>
                                                <input type="text" name="serial_no" class="form-control ">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Artikel-Nr.</label>
                                                <input type="text" name="article_no" class="form-control " value="{{ $data->article_no }}">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>EAN</label>
                                                <input type="text" name="ean" class="form-control " value="{{ $data->ean }}">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Lagerort</label>
                                                <input type="text" name="location" class="form-control ">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Menge</label>
                                                <input type="number" name="quantity" class="form-control " min="1" value="1">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="feather icon-save mr-25"></i> Inventar hinzufügen
                                        </button>
                                    </form>

                                    <div class="table-responsive mt-75">
                                        <table class="table table-sm inventory-table" id="inventoryTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Serien-Nr.</th>
                                                    <th>Artikel-Nr.</th>
                                                    <th>EAN</th>
                                                    <th>Lagerort</th>
                                                    <th>Menge</th>
                                                    <th>Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- INSTALLATION TAB --}}
                            <div class="tab-pane fade product-tab-panel" id="panel-install" role="tabpanel">
                                <div class="product-panel-card">
                                    <h5>Geschätzte Montagezeit</h5>
                                    <small>Fälle und Aufwandseinschätzung.</small>

                                    <ul class="timeline mt-50">
                                        @forelse($installation as $install)
                                            @php
                                                $markerClass = 'success';
                                                if ($install->rate >= 5) {
                                                    $markerClass = 'danger';
                                                } elseif ($install->rate >= 3) {
                                                    $markerClass = 'warning';
                                                }
                                            @endphp
                                            <li class="timeline-item">
                                                <span class="timeline-marker {{ $markerClass }}"></span>
                                                <div class="timeline-title">{{ $install->case }}</div>
                                                <div class="timeline-meta">
                                                    Aufwand: {{ $install->rate }}
                                                </div>
                                                <div style="font-size:.8rem; margin-top:.15rem;">
                                                    {{ Str::limit($install->description, 140) }}
                                                </div>
                                            </li>
                                        @empty
                                            <li class="timeline-item">
                                                <span class="timeline-marker"></span>
                                                <div class="timeline-title text-muted">
                                                    Keine Montagefälle hinterlegt.
                                                </div>
                                            </li>
                                        @endforelse
                                    </ul>

                                    <div class="mt-75">
                                        <a href="{{ url('/product_installation/'.$data->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="feather icon-edit mr-25"></i> Montagefälle bearbeiten
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- TECHNICAL PERSON TAB --}}
                            <div class="tab-pane fade product-tab-panel" id="panel-team" role="tabpanel">
                                <div class="product-panel-card">
                                    <h5>Technisches Team</h5>
                                    <small>Kompetenzen je Mitarbeiter und Dienst.</small>

                                    <div class="mt-50">
                                        @if($technical_person->count())
                                            {{-- LIST VIEW --}}
                                            <div class="table-responsive mb-1">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Person</th>
                                                            <th>Beratung</th>
                                                            <th>Planung</th>
                                                            <th>Kalkulation</th>
                                                            <th>Montage</th>
                                                            <th>Projektierung</th>
                                                            <th>Bauleitung</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($technical_person as $tech1)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ url('next_employee/'.$tech1->empid) }}">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar mr-50">
                                                                            <img src="{{ asset('images/employee/'.$tech1->image) }}"
                                                                                 alt="Avatar" width="32" height="32" class="rounded-circle">
                                                                        </div>
                                                                        <div style="font-size:.8rem;">
                                                                            {{ $tech1->empname }} {{ $tech1->lastname }}
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </td>
                                                            @foreach (['advice', 'plan', 'calculation', 'montage', 'project_planing', 'site_management'] as $field)
                                                                <td>
                                                                    <div class="tech-stars">
                                                                        @for($i = 1; $i <= $tech1->$field; $i++)
                                                                            <i class="fa fa-star"></i>
                                                                        @endfor
                                                                    </div>
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- BY SERVICE VIEW --}}
                                            <div class="mt-1">
                                                <h6 style="font-size:.85rem; margin-bottom:.4rem;">Team nach Dienst</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <tbody>
                                                            @php
                                                                $categories = [
                                                                    'advice'          => 'Beratung',
                                                                    'plan'            => 'Planung',
                                                                    'calculation'     => 'Kalkulation',
                                                                    'montage'         => 'Montage',
                                                                    'project_planing' => 'Projektierung',
                                                                    'site_management' => 'Bauleitung',
                                                                ];
                                                            @endphp

                                                            @foreach($categories as $key => $label)
                                                                <tr>
                                                                    <th style="width:20%;">{{ $label }}</th>
                                                                    <td>
                                                                        <ul class="list-unstyled users-list d-flex align-items-center mb-0">
                                                                            @foreach($technical_person as $tech2)
                                                                                @if($tech2->$key >= 1)
                                                                                    <li class="avatar pull-up mr-50" data-toggle="tooltip"
                                                                                        data-placement="bottom"
                                                                                        data-original-title="{{ $tech2->empname }} {{ $tech2->lastname }}">
                                                                                        <a href="{{ url('next_employee/'.$tech2->empid) }}">
                                                                                            <img class="media-object rounded-circle"
                                                                                                 src="{{ asset('images/employee/'.$tech2->image) }}"
                                                                                                 alt="Avatar" height="30" width="30">
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted" style="font-size:.8rem;">
                                                Für dieses Produkt sind noch keine technischen Verantwortlichen hinterlegt.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div> {{-- /tab-content --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- ===================== EDIT SUPPLIER PRICE MODAL ===================== --}}
<div id="editSupplierPriceModal" class="xmodal" aria-hidden="true" aria-labelledby="editSupplierPriceModalLabel" role="dialog">
  <div class="xmodal__backdrop" data-xmodal-close></div>

  <div class="xmodal__panel" role="document" tabindex="-1">
    <form id="edit-supplier-price-form" class="xmodal__form">
      @csrf
      @method('PUT')

      <input type="hidden" id="esp_id" value="">

      <div class="xmodal__header">
        <div class="xmodal__titlewrap">
          <h5 class="xmodal__title" id="editSupplierPriceModalLabel">Lieferantenpreis bearbeiten</h5>
          <small class="xmodal__subtitle">Standard: nur EK sichtbar. Für Details „Erweitert“ aktivieren.</small>
        </div>

        <div class="xmodal__actions">
          <label class="xswitch" title="Erweiterte Felder ein-/ausblenden">
            <input type="checkbox" id="esp-advanced-toggle">
            <span class="xswitch__track"></span>
            <span class="xswitch__label">Erweitert</span>
          </label>

          <button type="button" class="xmodal__close" aria-label="Schließen" data-xmodal-close>
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div>

      <div class="xmodal__body">
        <div class="row supplier-form-row">
          <!-- Lieferant (always) -->
          <div class="col-lg-4 col-md-6 mb-1">
            <label class="supplier-label">
              <i class="feather icon-user mr-25"></i> Lieferant
            </label>
            <select id="esp_distributor_id" class="form-control" required>
              <option value="">– Lieferant auswählen –</option>
            </select>
          </div>

          <!-- Einkaufspreis (EK) (always) -->
          <div class="col-lg-2 col-md-6 mb-1">
            <label class="supplier-label">
              <i class="feather icon-shopping-cart mr-25"></i> Einkaufspreis
            </label>
            <div class="input-group input-group supplier-input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">€</span>
              </div>
              <input type="number" step="0.01" id="esp_purchase_price" class="form-control" placeholder="0,00">
            </div>
          </div>

          <!-- Advanced fields -->
          <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
            <label class="supplier-label"><i class="feather icon-hash mr-25"></i> Art.Nr.</label>
            <input type="text" id="esp_article_no" class="form-control" value="">
          </div>

          <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
            <label class="supplier-label"><i class="feather icon-dollar-sign mr-25"></i> UVP (€)</label>
            <div class="input-group input-group supplier-input-group">
              <div class="input-group-prepend"><span class="input-group-text">€</span></div>
              <input type="number" step="0.01" id="esp_price" class="form-control" placeholder="0,00">
            </div>
          </div>

          <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
            <label class="supplier-label"><i class="feather icon-percent mr-25"></i> Rabatt %</label>
            <div class="input-group input-group supplier-input-group">
              <input type="number" id="esp_discount_percent" class="form-control" min="0" max="100" placeholder="z.B. 20">
              <div class="input-group-append"><span class="input-group-text">%</span></div>
            </div>
          </div>

          <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
            <label class="supplier-label"><i class="feather icon-arrow-down-right mr-25"></i> Rabatt €</label>
            <div class="input-group input-group supplier-input-group">
              <div class="input-group-prepend"><span class="input-group-text">€</span></div>
              <input type="number" step="0.01" id="esp_discount_price" class="form-control" placeholder="0,00">
            </div>
          </div>

          <div class="col-lg-2 col-md-6 mb-1 esp-advanced d-none">
            <label class="supplier-label"><i class="feather icon-calendar mr-25"></i> Datum</label>
            <input type="date" id="esp_price_date" class="form-control">
          </div>

          <div class="col-lg-3 col-md-6 mb-1 esp-advanced d-none">
            <label class="supplier-label"><i class="feather icon-package mr-25"></i> Verfügbarkeit</label>
            <input type="text" id="esp_availability" class="form-control" placeholder="z.B. Lagernd, 2-3 Wochen, auf Anfrage">
          </div>

          <div class="col-lg-3 col-md-6 mb-1 esp-advanced d-none">
            <label class="supplier-label"><i class="feather icon-layers mr-25"></i> Rabattgruppe</label>
            <select id="esp_discount_group_id" class="form-control">
              <option value="">– keine –</option>
            </select>
            <small class="text-muted d-block mt-25" style="font-size:.9rem;">Auswahl übernimmt automatisch Rabatt %.</small>
          </div>

          <!-- Status (always) -->
          <div class="col-lg-2 col-md-6 mb-1">
            <label class="supplier-label"><i class="feather icon-activity mr-25"></i> Status</label>
            <select id="esp_status" class="form-control">
              <option value="Published">Aktiv</option>
              <option value="Unpublished">Inaktiv</option>
            </select>
          </div>
        </div>

        <!-- calc info (advanced only) -->
        <div class="supplier-calc-info mt-25 esp-advanced d-none" id="esp-calc-info" style="display:none;">
          <div class="supplier-calc-pill">
            <i class="feather icon-info mr-25"></i>
            <span id="esp-calc-text"></span>
          </div>
        </div>

        <div id="esp-errors" class="text-danger mt-25" style="font-size:.95rem; display:none;"></div>
      </div>

      <div class="xmodal__footer">
        <button type="button" class="btn btn-outline-secondary" data-xmodal-close>Abbrechen</button>
        <button type="submit" class="btn btn-primary" id="esp-save-btn">
          <span class="spinner-border spinner-border-sm mr-25 d-none" id="esp-save-spinner"></span>
          Speichern
        </button>
      </div>
    </form>
  </div>
</div>

<style>
/* =========================
   CUSTOM MODAL STYLES
========================= */
.xmodal { position: fixed; inset: 0; z-index: 2000; display: none; }
.xmodal.is-open { display: block; }
.xmodal__backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.55); backdrop-filter: blur(2px); }
.xmodal__panel {
  position: relative;
  width: min(1100px, calc(100% - 32px));
  margin: 32px auto;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  overflow: hidden;
  outline: none;
}
.xmodal__header, .xmodal__footer { padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,.08); }
.xmodal__footer { border-top: 1px solid rgba(0,0,0,.08); border-bottom: 0; display:flex; justify-content:flex-end; gap:10px; }
.xmodal__body { padding: 16px; max-height: calc(100vh - 180px); overflow: auto; }
.xmodal__header { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.xmodal__title { margin: 0; font-weight: 700; }
.xmodal__subtitle { color: #6c757d; }
.xmodal__actions { display:flex; align-items:center; gap:12px; }
.xmodal__close {
  width: 38px; height: 38px;
  border-radius: 10px;
  border: 1px solid rgba(0,0,0,.12);
  background: #fff;
  cursor: pointer;
}
.xmodal__close:hover { background: rgba(0,0,0,.04); }

/* Switch */
.xswitch { display:flex; align-items:center; gap:10px; cursor:pointer; user-select:none; }
.xswitch input { display:none; }
.xswitch__track {
  width: 44px; height: 26px;
  border-radius: 999px;
  background: rgba(0,0,0,.12);
  position: relative;
  transition: .15s ease;
}
.xswitch__track::after{
  content:'';
  position:absolute; top:3px; left:3px;
  width: 20px; height: 20px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,.18);
  transition: .15s ease;
}
.xswitch input:checked + .xswitch__track { background: rgba(0,123,255,.35); }
.xswitch input:checked + .xswitch__track::after { transform: translateX(18px); }
.xswitch__label { font-weight: 600; font-size: .9rem; color: #212529; }
</style>


@endsection

   @section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
 <script>
(function ($) {
  'use strict';

  const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content') || '';
  const PRODUCT_ID = {{ $data->id }};

  const WF = {
    inv: { initialized: false },
    suppliers: {
      loaded: false,
      cache: {},        // id -> price object
      advanced: false   // TAB advanced (default OFF)
    }
  };

  /* =========================================================
   * XMODAL (no Bootstrap JS)
   * ======================================================= */
  const xmodal = (() => {
    const state = { lastFocus: null };

    const qs  = (sel, root=document) => root.querySelector(sel);
    const qsa = (sel, root=document) => Array.from(root.querySelectorAll(sel));

    function setModalAdvanced(modalEl, on) {
      qsa('.esp-advanced', modalEl).forEach(n => n.classList.toggle('d-none', !on));
      const calc = qs('#esp-calc-info', modalEl);
      if (!on && calc) calc.style.display = 'none';
    }

    function open(id) {
      const el = document.getElementById(id);
      if (!el) return;

      state.lastFocus = document.activeElement;

      el.classList.add('is-open');
      el.setAttribute('aria-hidden', 'false');

      // default advanced OFF on every open
      const adv = qs('#esp-advanced-toggle', el);
      if (adv) adv.checked = false;
      setModalAdvanced(el, false);

      const panel = qs('.xmodal__panel', el);
      if (panel) panel.focus();

      document.body.style.overflow = 'hidden';
    }

    function close(id) {
      const el = document.getElementById(id);
      if (!el) return;

      el.classList.remove('is-open');
      el.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';

      if (state.lastFocus && typeof state.lastFocus.focus === 'function') {
        state.lastFocus.focus();
      }
    }

    function bindOnce() {
      qsa('.xmodal').forEach(el => {
        qsa('[data-xmodal-close]', el).forEach(btn => btn.addEventListener('click', () => close(el.id)));
        const backdrop = qs('.xmodal__backdrop', el);
        if (backdrop) backdrop.addEventListener('click', () => close(el.id));

        const adv = qs('#esp-advanced-toggle', el);
        if (adv) adv.addEventListener('change', () => setModalAdvanced(el, !!adv.checked));
      });

      document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        const openEl = document.querySelector('.xmodal.is-open');
        if (openEl) close(openEl.id);
      });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bindOnce);
    else bindOnce();

    return { open, close };
  })();

  /* =========================================================
   * HELPERS
   * ======================================================= */
  function escapeHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Parse supports "12,3" and "12.3" (NO formatting while typing)
  function toFiniteNumber(val) {
    if (val === undefined || val === null) return null;
    if (typeof val === 'number') return Number.isFinite(val) ? val : null;

    let s = String(val).trim();
    if (s === '') return null;
    s = s.replace(',', '.');

    const n = parseFloat(s);
    return Number.isFinite(n) ? n : null;
  }

  function formatNumber(val, decimals) {
    const n = toFiniteNumber(val);
    if (n === null) return '';
    return n.toFixed(decimals);
  }

  function money(val, decimals) {
    const txt = formatNumber(val, decimals);
    return txt ? (txt + ' €') : '';
  }

  function percent(val) {
    const txt = formatNumber(val, 0);
    return txt ? (txt + ' %') : '';
  }

  function ajaxErrorToHtml(xhr) {
    if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
      let html = '<ul class="mb-0">';
      $.each(xhr.responseJSON.errors, function (_field, messages) {
        (messages || []).forEach(function (msg) {
          html += '<li>' + escapeHtml(msg) + '</li>';
        });
      });
      html += '</ul>';
      return html;
    }
    return null;
  }

  function safeJsonFromJqXHR(xhr) {
    if (!xhr) return null;
    if (xhr.responseJSON) return xhr.responseJSON;
    try { return JSON.parse(xhr.responseText || '{}'); } catch (_e) { return null; }
  }

  function setBtnLoading($btn, $spinner, loading) {
    if ($spinner && $spinner.length) $spinner.toggleClass('d-none', !loading);
    if ($btn && $btn.length) $btn.prop('disabled', !!loading);
  }

  function ensureSelect2($el) {
    if ($.fn.select2 && $el && $el.length && !$el.hasClass('select2-hidden-accessible')) {
      $el.select2({ width: '100%', placeholder: 'Bitte auswählen', allowClear: true });
    }
  }

  /* =========================================================
   * TAB ADVANCED TOGGLE (default OFF)
   * IMPORTANT: Blade now uses:
   *   - advanced-only fields: .supplier-advanced-only
   *   - advanced-only columns: .supplier-col-advanced-only
   *   - ALWAYS visible: EK + Art.Nr + Verfügbarkeit + Lieferant
   * ======================================================= */
  function setSupplierAdvanced(on) {
    WF.suppliers.advanced = !!on;
    $('.supplier-advanced-only').toggleClass('d-none', !WF.suppliers.advanced);
    $('.supplier-col-advanced-only').toggleClass('d-none', !WF.suppliers.advanced);
    if (!WF.suppliers.advanced) $('#supplier-calc-info').hide();
  }

  function applySupplierAdvancedDefault() {
    const $t = $('#supplier-advanced-toggle');
    if ($t.length) $t.prop('checked', false);
    setSupplierAdvanced(false);
  }

  /* =========================================================
   * INVENTORY
   * ======================================================= */
  function initInventoryTable() {
    if (WF.inv.initialized) return;
    WF.inv.initialized = true;

    if (!$.fn.DataTable) return;

    $('#inventoryTable').DataTable({
      ajax: '/ajax/inventory/list/' + PRODUCT_ID,
      columns: [
        { data: null, render: function (_d, _t, _r, meta) { return meta.row + 1; } },
        { data: 'serial_no' },
        { data: 'article_no' },
        { data: 'ean' },
        { data: 'location' },
        { data: 'quantity' },
        {
          data: 'id',
          render: function (id) {
            return '' +
              '<button class="btn btn-sm btn-outline-danger" onclick="deleteInventoryItem(' + id + ')">' +
                '<i class="feather icon-trash-2"></i>' +
              '</button>';
          }
        }
      ],
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/de-DE.json' }
    });
  }

  window.deleteInventoryItem = function (id) {
    if (!confirm('Diesen Inventareintrag wirklich löschen?')) return;

    $.ajax({
      url: '/ajax/inventory/delete/' + id,
      type: 'DELETE',
      data: { _token: CSRF_TOKEN },
      success: function (res) {
        toastr.success((res && res.message) ? res.message : 'Eintrag gelöscht');
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#inventoryTable')) {
          $('#inventoryTable').DataTable().ajax.reload();
        }
      },
      error: function () {
        toastr.error('Löschen fehlgeschlagen.');
      }
    });
  };

  function initInventoryForm() {
    const $form = $('#inventoryForm');
    if (!$form.length) return;

    $form.off('submit.inv').on('submit.inv', function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.set('_token', CSRF_TOKEN);

      $.ajax({
        url: "{{ route('ajax.inventory.store') }}",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
          toastr.success((res && res.message) ? res.message : 'Inventar gespeichert');
          $form[0].reset();

          if ($.fn.DataTable && $.fn.DataTable.isDataTable('#inventoryTable')) {
            $('#inventoryTable').DataTable().ajax.reload();
          }
        },
        error: function () {
          toastr.error('Fehler beim Speichern des Inventars.');
        }
      });
    });
  }

  /* =========================================================
   * CALC UI
   * RULES:
   *  ✅ advanced OFF: EK typing FREE (no handlers)
   *  ✅ advanced ON: calc runs on blur/change (not input)
   *  ✅ never overwrite the focused input
   * ======================================================= */
  function updateCalcInfo($wrap, $txt, price, discEuro, discPercent, purchase) {
    if (!$wrap || !$wrap.length) return;

    if (price === null && purchase === null && discEuro === null && discPercent === null) {
      $wrap.hide();
      return;
    }

    let text = '';
    if (price !== null) text += 'UVP ' + formatNumber(price, 2) + ' €';
    if (discEuro !== null) text += ' – Rabatt ' + formatNumber(discEuro, 2) + ' €';
    if (discPercent !== null) text += ' (' + formatNumber(discPercent, 0) + ' %)';
    if (purchase !== null) text += ' → Einkaufspreis ' + formatNumber(purchase, 2) + ' €';

    $txt.text(text);
    $wrap.show();
  }

  function computeFromValues(price, discPercent, discEuro, purchase) {
    const hasPrice    = price !== null && price > 0;
    const hasEuro     = discEuro !== null && discEuro >= 0;
    const hasPercent  = discPercent !== null && discPercent >= 0;
    const hasPurchase = purchase !== null && purchase > 0;

    if (!hasPrice && !hasEuro && !hasPercent && !hasPurchase) {
      return { empty:true, ambiguous:false, ekOnly:false, price:null, discEuro:null, discPercent:null, purchase:null };
    }

    // EK-only allowed
    if (hasPurchase && !hasPrice && !hasEuro && !hasPercent) {
      return { empty:false, ambiguous:false, ekOnly:true, price:null, discEuro:null, discPercent:null, purchase };
    }

    // ambiguous: discount without base
    if (!hasPrice && !hasPurchase && (hasEuro || hasPercent)) {
      return { empty:false, ambiguous:true, ekOnly:false, price, discEuro, discPercent, purchase };
    }

    if (hasPrice && hasPercent) {
      discEuro = price * discPercent / 100;
      purchase = price - discEuro;
    } else if (hasPrice && hasEuro) {
      discPercent = price > 0 ? (discEuro / price) * 100 : 0;
      purchase = price - discEuro;
    } else if (hasPrice && hasPurchase) {
      discEuro = price - purchase;
      discPercent = price > 0 ? (discEuro / price) * 100 : 0;
    } else if (hasPurchase && hasPercent) {
      if (discPercent >= 100) discPercent = 99;
      price = purchase / (1 - discPercent / 100);
      discEuro = price - purchase;
    } else if (hasPurchase && hasEuro) {
      price = purchase + discEuro;
      discPercent = price > 0 ? (discEuro / price) * 100 : 0;
    } else {
      return { empty:false, ambiguous:true, ekOnly:false, price, discEuro, discPercent, purchase };
    }

    if (discPercent !== null && Number.isFinite(discPercent)) discPercent = Math.round(discPercent);
    return { empty:false, ambiguous:false, ekOnly:false, price, discEuro, discPercent, purchase };
  }

  function bindCalcController(opts) {
    const $price       = $(opts.priceSel);
    const $discPercent = $(opts.discPercentSel);
    const $discEuro    = $(opts.discEuroSel);
    const $purchase    = $(opts.purchaseSel);

    const $wrap = $(opts.wrapSel);
    const $txt  = $(opts.txtSel);

    const enabled = () => (typeof opts.enabledFn === 'function' ? !!opts.enabledFn() : true);
    const ns = opts.ns || '.calc';

    function focusedId() {
      const el = document.activeElement;
      return el && el.id ? el.id : null;
    }

    function setIfNotFocused($el, id, val) {
      if (!$el || !$el.length) return;
      if (!id) return;
      if (focusedId() === id) return;
      $el.val(val);
    }

    function recalc() {
      if (!enabled()) return;

      const price       = toFiniteNumber($price.val());
      const discPercent = toFiniteNumber($discPercent.val());
      const discEuro    = toFiniteNumber($discEuro.val());
      const purchase    = toFiniteNumber($purchase.val());

      const r = computeFromValues(price, discPercent, discEuro, purchase);

      if (r.empty || r.ambiguous) {
        updateCalcInfo($wrap, $txt, null, null, null, null);
        return r;
      }

      if (r.ekOnly) {
        updateCalcInfo($wrap, $txt, null, null, null, r.purchase);
        return r;
      }

      setIfNotFocused($price,       $price.attr('id'),       formatNumber(r.price, 2));
      setIfNotFocused($discEuro,    $discEuro.attr('id'),    formatNumber(r.discEuro, 2));
      setIfNotFocused($discPercent, $discPercent.attr('id'), formatNumber(r.discPercent, 0));
      setIfNotFocused($purchase,    $purchase.attr('id'),    formatNumber(r.purchase, 2));

      updateCalcInfo($wrap, $txt, r.price, r.discEuro, r.discPercent, r.purchase);
      return r;
    }

    function detachAll() {
      $price.add($discPercent).add($discEuro).add($purchase).off(ns);
    }

    function attachAdvancedOnly() {
      $price.add($discPercent).add($discEuro).add($purchase)
        .on('blur' + ns + ' change' + ns, recalc);

      $purchase.on('blur' + ns, function () {
        if (!enabled()) return;
        const n = toFiniteNumber($purchase.val());
        if (n !== null) $purchase.val(formatNumber(n, 2));
      });
    }

    function refresh() {
      detachAll();
      if (!enabled()) {
        updateCalcInfo($wrap, $txt, null, null, null, null);
        return;
      }
      attachAdvancedOnly();
    }

    return { recalc, refresh, detachAll };
  }

  /* =========================================================
   * SUPPLIER TABLE (Blade now has always-visible Art.Nr + Verfügbarkeit)
   * ======================================================= */
  function supplierRowHtml(p) {
    WF.suppliers.cache[p.id] = p;

    return '' +
      '<tr data-id="' + p.id + '">' +
        // always visible
        '<td>' + escapeHtml(p.article_no || '') + '</td>' +
        '<td>' + escapeHtml(p.distributor_name || '') + '</td>' +

        // advanced-only columns
        '<td class="supplier-col-advanced-only">' + money(p.price, 2) + '</td>' +
        '<td class="supplier-col-advanced-only">' + money(p.discount_price, 2) + '</td>' +
        '<td class="supplier-col-advanced-only">' + percent(p.discount_percent) + '</td>' +

        // always visible
        '<td>' + money(p.purchase_price, 2) + '</td>' +

        // advanced-only
        '<td class="supplier-col-advanced-only">' + escapeHtml(p.price_date || '') + '</td>' +

        // always visible
        '<td>' + escapeHtml(p.availability || '') + '</td>' +

        '<td class="text-right">' +
          (document.getElementById('editSupplierPriceModal')
            ? '<button type="button" class="btn btn-sm btn-outline-primary mr-25 js-edit-supplier-price" title="Bearbeiten">' +
                '<i class="feather icon-edit-2"></i>' +
              '</button>'
            : ''
          ) +
          '<button type="button" class="btn btn-sm btn-outline-danger js-delete-supplier-price" title="Löschen">' +
            '<i class="feather icon-trash-2"></i>' +
          '</button>' +
        '</td>' +
      '</tr>';
  }

  function renderSupplierTable(prices) {
    const $tbody = $('#supplier-prices-tbody');
    const $empty = $('#supplier-prices-empty');
    const $wrap  = $('#supplier-prices-table-wrapper');

    $tbody.empty();
    WF.suppliers.cache = {};

    if (prices && prices.length) {
      prices.forEach(p => $tbody.append(supplierRowHtml(p)));
      setSupplierAdvanced(WF.suppliers.advanced);
      $empty.addClass('d-none');
      $wrap.removeClass('d-none');
    } else {
      $empty.removeClass('d-none');
      $wrap.addClass('d-none');
    }
  }

  function upsertSupplierRow(p) {
    if (!p || !p.id) return;

    WF.suppliers.cache[p.id] = p;

    const $tbody = $('#supplier-prices-tbody');
    const $existing = $tbody.find('tr[data-id="' + p.id + '"]');

    if ($existing.length) $existing.replaceWith(supplierRowHtml(p));
    else $tbody.prepend(supplierRowHtml(p));

    $('#supplier-prices-empty').addClass('d-none');
    $('#supplier-prices-table-wrapper').removeClass('d-none');
    setSupplierAdvanced(WF.suppliers.advanced);
  }

  function fillSelectOptions(res) {
    const dists  = (res && res.distributors) || [];
    const groups = (res && res.discountGroups) || [];

    const $dist = $('#supplier_distributor_id');
    $dist.find('option:not(:first)').remove();
    dists.forEach(d => $dist.append($('<option>', { value: d.id, text: d.name })));

    const $dg = $('#supplier_discount_group_id');
    $dg.find('option:not(:first)').remove();
    groups.forEach(g => {
      $dg.append(
        $('<option>', { value: g.id, text: g.discount_group + ' (' + g.discount + ' %)' })
          .attr('data-discount', g.discount)
      );
    });

    const $edist = $('#esp_distributor_id');
    $edist.find('option:not(:first)').remove();
    dists.forEach(d => $edist.append($('<option>', { value: d.id, text: d.name })));

    const $edg = $('#esp_discount_group_id');
    $edg.find('option:not(:first)').remove();
    groups.forEach(g => {
      $edg.append(
        $('<option>', { value: g.id, text: g.discount_group + ' (' + g.discount + ' %)' })
          .attr('data-discount', g.discount)
      );
    });
  }

  function pickPrice(res) {
    if (!res) return null;
    if (res.price && res.price.id) return res.price;
    if (res.data && res.data.price && res.data.price.id) return res.data.price;
    if (res.data && res.data.id) return res.data;
    return null;
  }

  /* =========================================================
   * SUPPLIERS PANEL
   * ======================================================= */
  function initSupplierPanel() {
    const $panel = $('#supplier-panel');
    if (!$panel.length) return;

    const loadUrl  = $panel.data('url-load');
    const storeUrl = $panel.data('url-store');

    ensureSelect2($('#supplier_distributor_id'));
    ensureSelect2($('#supplier_discount_group_id'));
    ensureSelect2($('#esp_distributor_id'));
    ensureSelect2($('#esp_discount_group_id'));

    applySupplierAdvancedDefault();

    const tabCalc = bindCalcController({
      ns: '.tabcalc',
      priceSel: '#sp_price',
      discPercentSel: '#sp_discount_percent',
      discEuroSel: '#sp_discount_price',
      purchaseSel: '#sp_purchase_price',
      wrapSel: '#supplier-calc-info',
      txtSel: '#supplier-calc-text',
      enabledFn: () => document.getElementById('supplier-advanced-toggle')?.checked
    });

    const modalCalc = bindCalcController({
      ns: '.modalcalc',
      priceSel: '#esp_price',
      discPercentSel: '#esp_discount_percent',
      discEuroSel: '#esp_discount_price',
      purchaseSel: '#esp_purchase_price',
      wrapSel: '#esp-calc-info',
      txtSel: '#esp-calc-text',
      enabledFn: () => document.getElementById('esp-advanced-toggle')?.checked
    });

    tabCalc.refresh();
    modalCalc.refresh();

    $('#supplier-advanced-toggle')
      .off('change.supadv')
      .on('change.supadv', function () {
        setSupplierAdvanced(this.checked);
        tabCalc.refresh();
        if (this.checked) tabCalc.recalc();
      });

    $(document)
      .off('change.espadv', '#esp-advanced-toggle')
      .on('change.espadv', '#esp-advanced-toggle', function () {
        modalCalc.refresh();
        if (this.checked) modalCalc.recalc();
      });

    $('#supplier_discount_group_id')
      .off('change.dg')
      .on('change.dg', function () {
        if (!document.getElementById('supplier-advanced-toggle')?.checked) return;
        const d = $(this).find('option:selected').data('discount');
        if (d !== undefined && d !== null && d !== '') {
          $('#sp_discount_percent').val(d);
          tabCalc.recalc();
        }
      });

    $('#esp_discount_group_id')
      .off('change.edg')
      .on('change.edg', function () {
        if (!document.getElementById('esp-advanced-toggle')?.checked) return;
        const d = $(this).find('option:selected').data('discount');
        if (d !== undefined && d !== null && d !== '') {
          $('#esp_discount_percent').val(d);
          modalCalc.recalc();
        }
      });

    function loadDataOnce() {
      if (WF.suppliers.loaded) return;
      WF.suppliers.loaded = true;

      $.getJSON(loadUrl, function (res) {
        fillSelectOptions(res);
        renderSupplierTable(res.prices || []);
      }).fail(function () {
        toastr.error('Lieferanten & Preise konnten nicht geladen werden.');
      });
    }

    $('a[data-toggle="tab"][href="#panel-suppliers"]')
      .off('shown.bs.tab.sup')
      .on('shown.bs.tab.sup', loadDataOnce);

    if ($('#panel-suppliers').hasClass('active') || $('#panel-suppliers').hasClass('show')) {
      loadDataOnce();
    }

    /* --------------------------
     * ADD submit
     * IMPORTANT: since Art.Nr + Verfügbarkeit are now always visible,
     * we must reset by IDs (sp_article_no/sp_availability) not name selectors.
     * ------------------------ */
    $('#supplier-price-form')
      .off('submit.sup')
      .on('submit.sup', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $errors = $('#supplier-price-errors');
        const $btn = $('#supplier-save-btn');
        const $spinner = $('#supplier-save-spinner');

        $errors.hide().empty();
        setBtnLoading($btn, $spinner, true);

        $.ajax({
          url: storeUrl,
          type: 'POST',
          data: $form.serialize(),
          headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
          success: function (res) {
            const p = pickPrice(res);
            if (!p) {
              console.error('Unexpected store response:', res);
              toastr.error('Antwortformat unerwartet (console prüfen).');
              return;
            }

            upsertSupplierRow(p);
            toastr.success(res.message || 'Lieferantenpreis gespeichert.');

            // reset (keep distributor selected or not? -> here we clear all)
            $('#sp_purchase_price').val('');
            $('#sp_article_no').val('');
            $('#sp_availability').val('');

            // advanced-only fields reset
            $('#sp_price, #sp_discount_percent, #sp_discount_price').val('');
            $('#supplier_discount_group_id').val('').trigger('change');

            $('#supplier-calc-info').hide();
          },
          error: function (xhr) {
            const html = ajaxErrorToHtml(xhr);
            if (html) $errors.html(html).show();
            else {
              const j = safeJsonFromJqXHR(xhr);
              toastr.error((j && j.message) ? j.message : 'Fehler beim Speichern des Lieferantenpreises.');
            }
          },
          complete: function () {
            setBtnLoading($btn, $spinner, false);
          }
        });
      });

    // DELETE
    $('#supplier-prices-tbody')
      .off('click.supdel')
      .on('click.supdel', '.js-delete-supplier-price', function () {
        const $row = $(this).closest('tr');
        const id = $row.data('id');
        if (!id) return;

        if (!confirm('Diesen Lieferantenpreis wirklich löschen?')) return;

        $.ajax({
          url: "{{ route('products.distributor-prices.destroy', ':id') }}".replace(':id', id),
          type: 'POST',
          data: { _token: CSRF_TOKEN, _method: 'DELETE' },
          success: function (res) {
            delete WF.suppliers.cache[id];
            $row.remove();

            if (!$('#supplier-prices-tbody tr').length) {
              $('#supplier-prices-empty').removeClass('d-none');
              $('#supplier-prices-table-wrapper').addClass('d-none');
            }
            toastr.success((res && res.message) ? res.message : 'Preis gelöscht.');
          },
          error: function () {
            toastr.error('Fehler beim Löschen des Lieferantenpreises.');
          }
        });
      });

    // EDIT open modal (unchanged UI expectations)
    $('#supplier-prices-tbody')
      .off('click.supedit')
      .on('click.supedit', '.js-edit-supplier-price', function () {
        const id = $(this).closest('tr').data('id');
        const p = WF.suppliers.cache[id];
        if (!p) {
          toastr.error('Cache fehlt. Bitte Tab neu laden.');
          return;
        }

        $('#esp_id').val(p.id);
        $('#esp_article_no').val(p.article_no || '');
        $('#esp_price').val(p.price != null ? formatNumber(p.price, 2) : '');
        $('#esp_discount_price').val(p.discount_price != null ? formatNumber(p.discount_price, 2) : '');
        $('#esp_discount_percent').val(p.discount_percent != null ? formatNumber(p.discount_percent, 0) : '');
        $('#esp_purchase_price').val(p.purchase_price != null ? formatNumber(p.purchase_price, 2) : '');
        $('#esp_price_date').val(p.price_date || '');
        $('#esp_availability').val(p.availability || '');
        $('#esp_status').val(p.status || 'Published');

        $('#esp_distributor_id').val(p.distributor_id || '').trigger('change');
        $('#esp_discount_group_id').val(p.discount_group_id || '').trigger('change');

        $('#esp-errors').hide().empty();

        xmodal.open('editSupplierPriceModal');
        modalCalc.refresh();
      });

    // EDIT submit
    $('#edit-supplier-price-form')
      .off('submit.supupd')
      .on('submit.supupd', function (e) {
        e.preventDefault();

        const id = $('#esp_id').val();
        if (!id) return;

        const $errors = $('#esp-errors');
        const $btn = $('#esp-save-btn');
        const $spinner = $('#esp-save-spinner');

        $errors.hide().empty();
        setBtnLoading($btn, $spinner, true);

        const url = "{{ route('products.distributor-prices.update', [':product', ':price']) }}"
          .replace(':product', PRODUCT_ID)
          .replace(':price', id);

        $.ajax({
          url: url,
          type: 'POST',
          headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
          data: {
            _method: 'PUT',
            distributor_id: $('#esp_distributor_id').val(),
            article_no: $('#esp_article_no').val(),
            price: $('#esp_price').val(),
            discount_percent: $('#esp_discount_percent').val(),
            discount_price: $('#esp_discount_price').val(),
            purchase_price: $('#esp_purchase_price').val(),
            price_date: $('#esp_price_date').val(),
            availability: $('#esp_availability').val(),
            discount_group_id: $('#esp_discount_group_id').val(),
            status: $('#esp_status').val()
          },
          success: function (res) {
            const p = pickPrice(res);
            if (!p) {
              console.error('Unexpected update response:', res);
              toastr.error('Antwortformat unerwartet (console prüfen).');
              return;
            }

            upsertSupplierRow(p);
            toastr.success(res.message || 'Lieferantenpreis aktualisiert.');
            xmodal.close('editSupplierPriceModal');
          },
          error: function (xhr) {
            const html = ajaxErrorToHtml(xhr);
            if (html) $errors.html(html).show();
            else {
              const j = safeJsonFromJqXHR(xhr);
              toastr.error((j && j.message) ? j.message : 'Fehler beim Aktualisieren des Lieferantenpreises.');
            }
          },
          complete: function () {
            setBtnLoading($btn, $spinner, false);
          }
        });
      });
  }

  /* =========================================================
   * INIT
   * ======================================================= */
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('a[data-toggle="tab"][href="#panel-inventory"]')
      .off('shown.bs.tab.inv')
      .on('shown.bs.tab.inv', function () {
        initInventoryTable();
      });

    initInventoryForm();
    initSupplierPanel();
  });

})(jQuery);
</script>


@endsection

